<?php

namespace App\Http\Controllers\Workflows\Positions;

use App\Helpers\{Ui\DatatableHelper, UidGenerator};
use App\Http\Controllers\Controller;
use App\Models\Workflows\Instruments\Instrument;
use Illuminate\Support\{Str, Carbon, Facades\Cache};
use Illuminate\Support\Facades\{Log, Storage, DB};
use Illuminate\Http\Request;
use Exception;
use Throwable;

class WebInstrumentController extends Controller
{

   public function index()
   {
      return view('Admin.sidebar.Instruments.index');
   }

   public function create()
   {
      return view('Admin.sidebar.Instruments.create');
   }

   public function show(Instrument $instrument)
   {
      return view('Admin.sidebar.Instruments.show', compact('instrument'));
   }


   public function store(Request $request)
   {
      try {
         $validated = $request->validate([
            'id' => 'nullable|integer|exists:instruments,id',
            'name' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|boolean|in:0,1',
         ]);
         // dd($validated);

         $postion = Instrument::updateOrCreate(
            [
               'id' => $validated['id'] ?? null
            ],
            [
               'name' => $validated['name'],
               'description' => $validated['description'] ?? null,
               'status' => $validated['status'] ?? 1,
            ]
         );

         return redirect()->back()->with('success', "A teacher postion '{$postion->name}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }


   public function toggle(Instrument $instrument)
   {
      try {
         $instrument->update([
            'status' => !$instrument->status,
         ]);

         return response()->json(['success' => true]);
      } catch (Exception $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function delete(Instrument $instrument)
   {
      $name = null;
      try {
         $name = $instrument?->name ?? null;

         $instrument->delete();

         return redirect()->back()->with('success', "Position '{$name}' deleted successfully.");
      } catch (Exception $e) {
         return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
      }
   }


   public function bulkDelete(Request $request)
   {
      $request->validate([
         'ids' => 'required|array',
         'ids.*' => 'integer|exists:instruments,id',
      ]);

      $ids = $request->ids;

      $instruments = Instrument::whereIn('id', $ids)->get();

      if ($instruments->isEmpty()) {
         return back()->with('error', 'No valid positions selected.');
      }

      $MESSAGE = "Selected positions are deleted.";
      $SKIPPED = [];
      $deleted = [];

      DB::beginTransaction();

      try {
         foreach ($instruments as $instrument) {

            if (strtolower($instrument->phase) === 'disputed') {
               $SKIPPED[] = $instrument->name;
               continue;
            }
            /** @var \App\Models\Workflows\Instruments\Instrument $instrument */
            $instrument->delete();

            $deleted[] = $instrument->name;
         }

         DB::commit();

         $MESSAGE = $MESSAGE . "success: [" . count($deleted) . "], dispute-skipped: [" . count($SKIPPED) . "].";

         return redirect()->back()->with('success', $MESSAGE);
      } catch (Throwable $e) {
         DB::rollBack();

         Log::error('Positions bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);

         return back()->with('error', 'Bulk delete failed. No changes were made.');
      }
   }


   public function instrumentDatatable(Request $request)
   {
      try {
         $columns = ['id', 'name', 'note', 'status', 'created_at'];

         $draw = (int)$request->input('draw');
         $start = (int)$request->input('start', 0);
         $length = (int)$request->input('length', 25);
         $orderColIndex = (int)$request->input('order.0.column', 0);
         $orderCol = $columns[$orderColIndex] ?? 'id';
         $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

         $query = Instrument::select(['id', 'name', 'note', 'status', 'created_at']);

         if ($search = $request->input('search.value')) {
            // DatatableHelper::handleSearch($query, $search, ['title', 'positions_uid', 'phase', 'progress', 'location', 'start_date', 'target_date', 'image', 'description'], 'lessonRelatingBackTo_Teacher');

            // if ($search = $request->input('search.value')) {
            //    $query->where(function ($q) use ($search) {
            //       $q->where('name', 'like', "%{$search}%")
            //          ->orWhere('lesson_uid', 'like', "%{$search}%")
            //          ->orWhere('phone', 'like', "%{$search}%")
            //          ->orWhere('title', 'like', "%{$search}%")
            //          ->orWhere('phase', 'like', "%{$search}%")
            //          ->orWhere('cost', 'like', "%{$search}%")
            //          ->orWhere('difficulty', 'like', "%{$search}%")
            //          ->orWhere('remarks', 'like', "%{$search}%")
            //          ->orWhereHas('lessonRelatingBackTo_Teacher', function ($q) use ($search) {
            //             $q->where('user_uid', 'like', "%{$search}%");
            //             $q->where('username', 'like', "%{$search}%");
            //             $q->where('name', 'like', "%{$search}%");
            //             $q->where('email', 'like', "%{$search}%");
            //             $q->where('phone', 'like', "%{$search}%");
            //          });
            //    });
            // }
         }

         $recordsTotal = Instrument::count();
         $recordsFiltered = $query->count();

         $data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

         $sn = $start;
         $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

         $data->transform(function ($row) use (&$sn, $zones) {

            $row->DT_RowId = 'row_' . $row->id;
            $row->view_url = route('backend_instrument_show', $row->id); // clickable row
            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

            $row->SN = ++$sn;

            $row->name = Str::limit(strip_tags($row->name), 60, '...');

            // --------- Created date ---------
            $row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';


            // --------- Description -------
            $row->note = $row->note ? substr($row->note, 0, 120) . "..." : "N/A";

            $row->actions = DatatableHelper::datatableAction($row, true, 'backend_instrument_toggle', 'backend_instrument_delete');

            return $row;
         });

         return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $data);
      } catch (Exception $e) {
         return DatatableHelper::handleDatatableError($request, $e, true, "Positions Datatable Error");
      }
   }
}
