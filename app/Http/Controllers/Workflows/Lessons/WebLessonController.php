<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\LessonSaveAction;
use App\Domains\Lessons\Enums\{LessonDifficultyEnums, LessonPhaseEnums};
use App\Domains\Lessons\Services\CascadeHistoryDeleteService;
use App\Enums\{CRUDActionEnums, LessonCategoryEnums, NotifyChannelEnums};
use App\Domains\Lessons\Events\LessonNotifyEvent;
use App\Helpers\{Ui\DatatableHelper, UidGenerator};
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflows\LessonStoreRequest;
use App\Models\Billings\Payments\PaymentPayable;
use App\Models\Users\User;
use App\Models\Workflows\Instruments\Instrument;
use App\Models\Workflows\Lessons\Lesson;
use Illuminate\Support\{Str, Carbon, Facades\Cache};
use Illuminate\Support\Facades\{Log, Storage, DB};
use Illuminate\Http\Request;
use Throwable;

class WebLessonController extends Controller
{
   public function create()
   {
      $users = User::whereHas('roles', function ($q) {
         $q->whereIn('role_key', ['teacher', 'student']);
      })->with('userRelatingBackTo_Instrument')->status(1)->get();

      $teachers = $users->filter(fn($u) => $u->hasRoleKey('teacher'))->values();
      $students = $users->filter(fn($u) => $u->hasRoleKey('student'))->values();
      $instrumentSubjects = Instrument::status(1)->get();

      $phases = LessonPhaseEnums::options();
      $difficulties = LessonDifficultyEnums::options();

      return view('Admin.sidebar.Lessons.create', compact('teachers', 'students', 'instrumentSubjects', 'phases', 'difficulties'));
   }


   public function index()
   {
      return view('Admin.sidebar.Lessons.index');
   }


   public function show(Lesson $singleLesson)
   {
      $users = User::whereHas('roles', function ($q) {
         $q->whereIn('role_key', ['teacher', 'student']);
      })->with('userRelatingBackTo_Instrument')->status(1)->get();

      $teachers = $users->filter(fn($u) => $u->hasRoleKey('teacher'))->values();
      $students = $users->filter(fn($u) => $u->hasRoleKey('student'))->values();
      $instrumentSubjects = Instrument::status(1)->get();

      $phases = LessonPhaseEnums::options();
      $difficulties = LessonDifficultyEnums::options();

      // ------------------ LESSON PAYABLES ------------------
      $lessonPayables = PaymentPayable::where('payable_type', Lesson::class)->where('payable_id', $singleLesson->id)->latest('id')->get();

      $payableTotalRows = $lessonPayables->count();
      $payableTotalAmounts = $lessonPayables->sum('amount');
      $payableTotalPaidAmounts = $lessonPayables->whereNotNull('paid_at')->sum('amount');
      $payableTotalDues = $lessonPayables->whereNull('paid_at')->sum('amount');

      $lastPaidPayable = $lessonPayables->whereNotNull('paid_at')->sortByDesc('paid_at')->first();
      $lessonPayableLastPaidAmount = $lastPaidPayable?->amount ?? 0;

      $payables = [
         'rows' => $payableTotalRows,
         'total' => $payableTotalAmounts,
         'paid' => $payableTotalPaidAmounts,
         'dues' => $payableTotalDues,
         'last_payment' => $lastPaidPayable,
      ];
      // ------------------ LESSON PAYABLES ------------------

      return view('Admin.sidebar.Lessons.show', compact('singleLesson', 'teachers', 'students', 'instrumentSubjects', 'phases', 'difficulties', 'payables'));
   }


   public function dues(Lesson $singleLesson)
   {
      $lessonPayables = PaymentPayable::where('payable_type', Lesson::class)->where('payable_id', $singleLesson->id)->latest('id')->paginate(20);

      return view('Admin.sidebar.Lessons.dues', compact('singleLesson', 'lessonPayables'));
   }


   public function store(LessonStoreRequest $request, LessonSaveAction $action)
   {
      try {
         $validated = $request->validated();
         // dd($validated);
         $USER = $request->user();
         $existingLesson = null;
         $instrument = Instrument::findOrFail((int)$validated['subject_id']);
         $isNewRecord = false;

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingLesson = Lesson::find($validated['id']);
            $LESSON_UID = $existingLesson->lesson_uid;
         } else {
            $LESSON_UID = UidGenerator::uniqueULID($instrument?->name ?? $validated['title'], 12, 18, 4);
            $isNewRecord = true;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         $lesson = $action->execute($validated, $existingLesson, $USER, $LESSON_UID, $isNewRecord);


         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = empty($validated['id']) ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;
         $targetUsers = [(int)$validated['teacher_id'], (int)$validated['student_id']];

         event(new LessonNotifyEvent($lesson, null, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->back()->with('success', "Lesson '{$lesson->title}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }


   public function toggle(Request $request, Lesson $lesson)
   {
      try {
         if (($lesson->lesson_category === LessonCategoryEnums::MAKEUP) && !$lesson->is_accepted) {
            return response()->json(['success' => false, 'error' => "You cannot change the status of a makeup lesson unless the student accepts it."]);
         }

         $lesson->update([
            'status' => !$lesson->status,
         ]);

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = $lesson->status ? CRUDActionEnums::ACTIVATE : CRUDActionEnums::DEACTIVATE;

         $targetUsers = [(int)$lesson?->teacher_id, (int)$lesson?->student_id];
         event(new LessonNotifyEvent($lesson, null, $request->user(), $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return response()->json(['success' => true]);
      } catch (Throwable $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function delete(Request $request, Lesson $lesson, CascadeHistoryDeleteService $cascadeHistoryDeleteService)
   {
      $name = null;
      try {
         $USER = $request->user();
         $object = $lesson;
         $teacher = $lesson?->teacher_id ?? null;
         $student = $lesson?->student_id ?? null;

         $name = $object?->title ?? null;
         $cascadeHistoryDeleteService->handle($lesson, 'lesson');
         $lesson->delete();

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = CRUDActionEnums::DELETE;
         $targetUsers = [(int)$teacher, (int)$student];
         event(new LessonNotifyEvent($lesson, null, $USER, $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->route('backend_lesson_index')->with('success', "A lesson '{$name}' has been deleted successfully.");
      } catch (Throwable $e) {
         return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
      }
   }


   public function bulkDelete(Request $request, CascadeHistoryDeleteService $cascadeHistoryDeleteService)
   {
      $request->validate([
         'ids' => 'required|array',
         'ids.*' => 'integer|exists:lessons,id',
      ]);

      $ids = $request->ids;

      /** @var \Illuminate\Database\Eloquent\Collection<int, Lesson> $lessons */

      $lessons = Lesson::whereIn('id', $ids)->get();
      $USER = $request->user();

      if ($lessons->isEmpty()) {
         return back()->with('error', 'No valid lessons selected.');
      }

      $MESSAGE = "Selected lessons are deleted.";
      $DISK_FOLDER = config('filesystems.default');
      $SKIPPED = [];
      $deleted = [];

      DB::beginTransaction();

      try {
         foreach ($lessons as $lesson) {
            /** @var Lesson $lesson */

            if (strtolower($lesson->phase) === 'disputed') {
               $SKIPPED[] = $lesson->title;
               continue;
            }

            // -------------------- NOTIFICATION EVENTS --------------------
            $actionFlag = CRUDActionEnums::DELETE;
            $targetUsers = [(int)$lesson?->teacher_id, (int)$lesson?->student_id];

            event(new LessonNotifyEvent($lesson, null, $request->user(), $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
            // -------------------- NOTIFICATION EVENTS --------------------

            $deleted[] = $lesson->title;

            $cascadeHistoryDeleteService->handle($lesson, 'lesson');
            $lesson->delete();

         }

         DB::commit();

         $MESSAGE = $MESSAGE . "success: [" . count($deleted) . "], dispute-skipped: [" . count($SKIPPED) . "].";
         logger("lessons bulk deleted", ['user' => $USER]);

         return redirect()->back()->with('success', $MESSAGE);
      } catch (Throwable $e) {
         DB::rollBack();

         Log::error('Lessons bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);

         return back()->with('error', 'Bulk delete failed. No changes were made.');
      }
   }


   public function lessonDatatable(Request $request)
   {
      try {
         $columns = ['id', 'lesson_uid', 'title', 'fees', 'schedule_day', 'start_time', 'end_time', 'status', 'created_at'];

         $draw = (int)$request->input('draw');
         $start = (int)$request->input('start', 0);
         $length = (int)$request->input('length', 25);
         $orderColIndex = (int)$request->input('order.0.column', 0);
         $orderCol = $columns[$orderColIndex] ?? 'id';
         $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

         $query = Lesson::with(['lessonRelatingBackTo_Teacher', 'lessonRelatingBackTo_Student', 'lessonRelatingBackTo_Intrument'])
            ->select(['id', 'lesson_uid', 'teacher_id', 'student_id', 'subject_id', 'title', 'lesson_category', 'is_accepted', 'fees', 'schedule_day', 'starting_date', 'start_time', 'end_time', 'status', 'created_at']);

         if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {

               foreach (['lesson_uid', 'title', 'fees', 'difficulty', 'schedule_day', 'remarks'] as $field) {
                  $q->orWhere($field, 'like', "%{$search}%");
               }

               foreach ([
                  'lessonRelatingBackTo_Teacher',
                  'lessonRelatingBackTo_Student',
                  'lessonRelatingBackTo_Creator'
               ] as $relation) {
                  $q->orWhereHas($relation, function ($q) use ($search) {
                     $q->where(function ($sub) use ($search) {
                        foreach (['user_uid', 'username', 'name', 'email', 'phone'] as $field) {
                           $sub->orWhere($field, 'like', "%{$search}%");
                        }
                     });
                  });
               }

               $q->orWhereHas('lessonRelatingBackTo_Intrument', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
               });
            });
         }

         $recordsTotal = Lesson::count();
         $recordsFiltered = $query->count();

         $data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

         $sn = $start;
         $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

         $data->transform(function ($row) use (&$sn, $zones) {

            $row->DT_RowId = 'row_' . $row->id;
            $row->view_url = route('backend_lesson_show', $row->id);

            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';
            $row->SN = ++$sn;

            // -------- Phase + Subject --------
            $row->phase_subject = '<div class="flex flex-col">
               <span class="mt-2 p-1 border rounded-lg text-2xs">' . ($row->lessonRelatingBackTo_Intrument?->name ?? 'N/A')
               . '</div>';

            // -------- Category + Type --------
            $row->type = '<div class="flex flex-col">
               <p class="mt-2 p-1 border rounded-lg text-2xs capitalize ' .
               ($row?->lesson_category === LessonCategoryEnums::MAKEUP ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') . '">' . ($row?->lesson_category?->value ?? 'N/A') . '</p>
               <p class="mt-2 p-1 border rounded-lg text-2xs ' . ($row?->is_accepted ? "border-gray-300 bg-gray-50  dark:bg-gray-500  text-gray-400" : "bg-yellow-50 text-yellow-700") . '">' . ($row?->is_accepted ? "Accepted" : "Pending") . '</p>
               </div>';

            // -------- Fees + Last Class --------
            $row->fees_last_class = '<div class="flex flex-col">
            <span>$' . ($row->fees ? number_format($row->fees, 2, ".", ",") : '0') . '</span>' .
               '<span class="mt-2 p-1 text-2xs">' . ($row->last_class ?? 'N/A')
               . '</div>';


            // $row->fees_last_class = ($row->fees ?? '0') . ' | ' . ($row->last_class ?? 'N/A');

            // -------- Teacher --------
            $row->teacher = DatatableHelper::userBlock($row->lessonRelatingBackTo_Teacher);

            // -------- Student --------
            $row->student = DatatableHelper::userBlock($row->lessonRelatingBackTo_Student);

            // -------- Schedule --------
            $days = is_array($row->schedule_day) ? implode(', ', $row->schedule_day) : ($row->schedule_day ?? 'N/A');

            $row->schedule = $days . '<br>' . ($row->start_time . ' - ' . $row->end_time);

            $zone = in_array($zones['zone'] ?? null, timezone_identifiers_list(), true) ? $zones['zone'] : 'Australia/Brisbane';

            $row->dates = ($row->starting_date ? $row->starting_date->timezone($zone)->format('d M Y') : 'N/A') . '|' . '<br>' . Carbon::now($zone)->startOfWeek(Carbon::MONDAY)->format('d M Y');

            // -------- Status --------
            $row->status_label = DatatableHelper::phaseColor($row->status);

            // -------- Created --------
            $row->created_at_formatted = $row->created_at
               ? Carbon::parse($row->created_at, 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')'
               : 'N/A';

            // -------- Actions --------
            $row->actions = DatatableHelper::datatableAction($row, true, 'backend_lesson_toggle', 'backend_lesson_delete');

            return $row;
         });

         return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $data);

      } catch (Throwable $e) {
         return DatatableHelper::handleDatatableError($request, $e, true, "Lesson Datatable Error");
      }
   }
}
