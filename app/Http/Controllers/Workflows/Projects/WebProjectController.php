<?php

namespace App\Http\Controllers\Workflows\Projects;

use App\Exports\ProjectExport;
use App\Helpers\{IconPack, Notifications\PushNotificationHelper, Ui\DatatableHelper, UidGenerator};
use App\Helpers\FileHelpers\{ChunkFileHelper, FileManagement};
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Http\Controllers\Controller;
use App\Imports\ProjectImport;
use App\Models\System\Settings\OptionSiteSetup;
use App\Models\Users\User;
use App\Models\Workflows\Projects\{Project,ProjectGallery};
use App\Notifications\InAppNotifications\ProjectInAppNotifier;
use App\Notifications\PushNotifications\ProjectSavePushNotify;
use Illuminate\Support\{Str, Carbon, Facades\Cache};
use Illuminate\Support\Facades\{Log, Storage, DB};
use Illuminate\Http\Request;
use Exception;
use Throwable;
use FFMpeg;
use Maatwebsite\Excel\Facades\Excel;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{RoleMiddleware, PermissionMiddleware};

class WebProjectController extends Controller implements HasMiddleware
{
   public static function middleware(): array
   {
      return [
         // new Middleware(RoleMiddleware::using('super_admin|admin'), only: ['index']),
         // new Middleware('verify_role_key:admin,student', only: ['index']),
         new Middleware(PermissionMiddleware::using('project_index'), only: ['index']),
         new Middleware(PermissionMiddleware::using('project_show'), only: ['show']),
         new Middleware(PermissionMiddleware::using('project_create'), only: ['create', 'store', 'toggle']),
         new Middleware(PermissionMiddleware::using('project_update'), only: ['edit', 'update', 'toggle']),
         new Middleware(PermissionMiddleware::using('project_delete'), only: ['delete']),
      ];
   }

   public function create()
   {
      $users = User::whereDoesntHave('roles', function ($q) {
         $q->whereIn('role_key', ['super_admin', 'admin', 'guest']);
      })->status(1)->get();
      return view('Admin.sidebar.Projects.create', compact('users'));
   }


   public function index()
   {
      return view('Admin.sidebar.Projects.index');
   }


   public function show(Project $singleProject)
   {
      // $users = User::where('user_role', 'user')->status(1)->get();
      $users = User::status(1)->get();
      $videoGallery = ProjectGallery::project($singleProject->id)->get();

      // ---------------------- FIELD ITEMS ----------------------

      return view('Admin.sidebar.Projects.show', compact('singleProject', 'users', 'videoGallery'));
   }


   public function store(Request $request)
   {
      try {
         $validated = $request->validate([
            'id' => 'nullable|integer|exists:projects,id',
            'project_manager_id' => 'required|integer|exists:users,id',
            'status' => 'nullable|boolean|in:0,1',

            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',

            'phase' => 'nullable|string|max:50',
            'progress' => 'nullable|string|max:50',

            'location' => 'nullable|string|max:255',

            'start_date' => 'nullable|date',
            // 'target_date' => 'nullable|date|after_or_equal:start_date',
            'target_date' => 'nullable|date',

            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10485', // KB, not bytes
            'remove_image' => 'nullable|boolean',

            'video' => 'nullable|string', // 1GB
            'remove_video' => 'nullable|boolean',

            'video_gallery' => 'nullable|array|max:4',

            'edit_video_gallery' => 'nullable|array',
            'edit_video_gallery.*.id' => 'required|integer|exists:project_galleries,id',
            'edit_video_gallery.*.replacement' => 'nullable|string',
            'edit_video_gallery.*.remove' => ['nullable', 'integer'],
         ], [
            'project_manager_id.required' => "The project manager field is required.",
            'description.max' =>
               "The description field must not be greater than 2000 characters. Use 'Ctrl+Shift+V' for copy-pasting or choose plain text.",
            'target_date.after_or_equal' =>
               "The target completion date must be after or equal to the start date.",
         ]);
         // dd($validated);
         $USER = $request->user();
         $DISK_FOLDER = config('filesystems.default');
         $PROJECT_MANAGER = User::find($validated['project_manager_id']);
         $existingProject = null;

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingProject = Project::find($validated['id']);
            $PROJECT_UID = $existingProject->project_uid;
         } else {
            $PROJECT_UID = UidGenerator::uniqueName($validated['title'], 12, 4);
         }
         // ------------ CHECKING EXISTANCE + UID ------------


         // ------------ IMAGE FILE HANDLING ------------
         $IMAGE_FOLDER = 'Projects/' . $PROJECT_UID;

         if ($request->hasFile('image') || $request->boolean('remove_image')) {
            $validated['image'] = FileManagement::handleFile(
               $request->file('image'),
               "0-FEATURE-" . ($validated['title'] ?? '0-no-title'),
               $existingProject?->image ?? null,
               $IMAGE_FOLDER,
               $DISK_FOLDER,
               $request->boolean('remove_image')
            );
         } else {
            $validated['image'] = ['path' => $existingProject->image ?? null];
         }
         // ------------ IMAGE FILE HANDLING ------------

         $project = Project::updateOrCreate(
            [
               'id' => $validated['id'] ?? null
            ],
            [
               'project_uid' => $PROJECT_UID,

               'project_manager_id' => (int)$PROJECT_MANAGER->id,
               'title' => $validated['title'],

               'phase' => empty($validated['id']) ? 'on-track' : ($validated['phase'] ?? null),
               'progress' => empty($validated['id']) ? 'N/A' : ($validated['progress'] ?? null),

               'location' => $validated['location'] ?? null,

               'start_date' => $validated['start_date'] ?? null,
               'target_date' => $validated['target_date'] ?? null,

               'description' => $validated['description'] ?? null,
               'image' => $validated['image']['path'] ?? null,
               // 'video' => $fileInfo ? $fileInfo['location'] : null,
               'video' => $existingProject?->video ?? null,
               'video_gallery' => $existingProject?->video_gallery ?? null,

               'status' => $validated['status'] ?? 1, // default active
            ]
         );
         // ---------------------- FILEPOND ---------------------------

         // -------------- Single --------------

         $singleVideoFilename = $USER->id . '-' . substr($project?->title, 0, 12) . '-' . Str::uuid();

         if ((isset($validated['video']) && $validated['video']) || $request->boolean('remove_video')) {
            $project->video = ChunkFileHelper::singleFile($request, $existingProject, $project, $IMAGE_FOLDER, $singleVideoFilename);
         }
         // -------------- Single --------------

         // -------------- multiple --------------

         $multiReplaceFilename = $USER->id . '-' . substr($project?->title, 0, 12) . '-' . Str::uuid();
         if (isset($validated['edit_video_gallery']) && $validated['edit_video_gallery']) {
            ChunkFileHelper::multiFileReplace($validated, $IMAGE_FOLDER, $multiReplaceFilename);
         }

         if ((isset($validated['video_gallery']) && $validated['video_gallery']) || $request->boolean('remove_video')) {

            $multiCreateFilename = $USER->id . '-' . substr($project?->title, 0, 12) . '-' . Str::uuid();
            $multiCreate = ChunkFileHelper::multiFileCreate($request, ProjectGallery::class, $IMAGE_FOLDER, $multiCreateFilename, 'project_id', $project->id);
         }

         // ---------------------- FILEPOND ---------------------------
         $project->save();

         // ------------------------- IN-APP-NOTIFICATION -------------------------
         (!isset($validated['id']) || empty($validated['id']))
            ? (new ProjectInAppNotifier($project, $USER, true))->create($PROJECT_MANAGER)
            : (new ProjectInAppNotifier($project, $USER, true))->update($PROJECT_MANAGER);
         // ------------------------- IN-APP-NOTIFICATION -------------------------

         return redirect()->back()->with('success', "Project '{$project->title}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }


   public function toggle(Project $project)
   {
      try {
         $project->update([
            'status' => !$project->status,
         ]);

         return response()->json(['success' => true]);
      } catch (Exception $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function delete(Project $project)
   {
      $name = null;
      $imageFolder = null;
      try {
         $name = $project?->title ?? null;
         $imageFolder = dirname($project->image);
         $DISK_FOLDER = config('filesystems.default');
         $firstGallery = ProjectGallery::project($project->id)->first();

         $trackFile = $project->image ?? $project->video ?? ($firstGallery->file ?? null);
         $imageFolder = dirname($trackFile);

         if ($imageFolder) {
            // FileManagement::deleteFile($project->image, $DISK_FOLDER);
            FileManagement::deleteFolder($imageFolder, $DISK_FOLDER);
         }

         $project->delete();

         return redirect()->back()->with('success', "Workout plan '{$name}' deleted successfully.");
      } catch (Exception $e) {
         return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
      }
   }


   public function bulkDelete(Request $request)
   {
      $request->validate([
         'ids' => 'required|array',
         'ids.*' => 'integer|exists:projects,id',
      ]);

      $ids = $request->ids;

      $projects = Project::whereIn('id', $ids)->get();

      if ($projects->isEmpty()) {
         return back()->with('error', 'No valid projects selected.');
      }

      $MESSAGE = "Selected projects are deleted.";
      $DISK_FOLDER = config('filesystems.default');
      $SKIPPED = [];
      $deleted = [];

      DB::beginTransaction();

      try {
         foreach ($projects as $project) {

            if (strtolower($project->phase) === 'disputed') {
               $SKIPPED[] = $project->title;
               continue;
            }
            $firstGallery = ProjectGallery::project($project->id)->first();

            $trackFile = $project->image ?? $project->video ?? ($firstGallery->file ?? null);
            $imageFolder = dirname($trackFile);

            $project->delete();

            if ($imageFolder) {
               FileManagement::deleteFolder($imageFolder, $DISK_FOLDER);
            }

            $deleted[] = $project->title;
         }

         DB::commit();

         $MESSAGE = $MESSAGE . "success: [" . count($deleted) . "], dispute-skipped: [" . count($SKIPPED) . "].";

         return redirect()->back()->with('success', $MESSAGE);
      } catch (Throwable $e) {
         DB::rollBack();

         Log::error('Project bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);

         return back()->with('error', 'Bulk delete failed. No changes were made.');
      }
   }


   public function projectDatatable(Request $request)
   {
      try {
         $columns = ['id', 'project_uid', 'project_manager_id', 'title', 'phase', 'progress', 'location', 'start_date', 'target_date', 'description', 'image', 'status', 'created_at'];

         $draw = (int)$request->input('draw');
         $start = (int)$request->input('start', 0);
         $length = (int)$request->input('length', 25);
         $orderColIndex = (int)$request->input('order.0.column', 0);
         $orderCol = $columns[$orderColIndex] ?? 'id';
         $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

         $query = Project::select(['id', 'project_uid', 'project_manager_id', 'title', 'phase', 'progress', 'location', 'start_date', 'target_date', 'description', 'image', 'status', 'created_at']);

         if ($search = $request->input('search.value')) {
            DatatableHelper::handleSearch($query, $search, ['title', 'project_uid', 'phase', 'progress', 'location', 'start_date', 'target_date', 'image', 'description']);
         }

         $recordsTotal = Project::count();
         $recordsFiltered = $query->count();

         $data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

         $sn = $start;
         $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

         $data->transform(function ($row) use (&$sn, $zones) {

            $row->DT_RowId = 'row_' . $row->id;
            $row->view_url = route('backend_project_show', $row->id); // clickable row
            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

            $row->SN = ++$sn;

            $row->description_short = Str::limit(strip_tags($row->description), 20, '...');

            // --------- Created date ---------
            $row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

            // --------- manager ---------
            $row->manager = DatatableHelper::userBlock($row->projectRelatingBackTo_User);

            // --------- Phases ---------
            $row->phase = DatatableHelper::phaseColor($row->phase);

            // --------- Progress -------
            $row->progress = $row->progress ?? "N/A";

            // --------- Description -------
            $row->description_short = $row->description ? substr($row->description, 0, 30) . "..." : "N/A";

            $row->image_preview = $row->image
               ? '<img src="' . Storage::url($row->image) . '" class="w-14 h-14 rounded object-cover mx-auto" />'
               : '<span class="text-gray-400 italic">No image</span>';

            $row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

            return $row;
         });

         return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $data);
      } catch (Exception $e) {
         return DatatableHelper::handleDatatableError($request, $e, true, "Projects Datatable Error");
      }
   }

   // ###############################################################################

   public function exportProjects($fileType)
   {
      switch ($fileType) {
         case 'xlsx':
            return Excel::download(new ProjectExport, 'projects-sheet.xlsx', \Maatwebsite\Excel\Excel::XLSX);

         case 'csv':
            return Excel::download(new ProjectExport, 'projects-sheet.csv', \Maatwebsite\Excel\Excel::CSV);

         case 'pdf':
            $time = now()->format('Ymd') ?? null;

            $Projects = Project::all();

            $pdf = LaravelMpdf::loadview('System.Reports.projects', [
               'Projects' => $Projects,
            ]);
            return $pdf->stream("all_projects_{$time}.pdf");


         default:
            abort(400, 'Invalid file type.');
            return redirect()->back()->with('error', 'Invalid file type');
      }
   }

   public function importExcelAllProjects(Request $request)
   {
      try {
         Excel::import(new ProjectImport(), request()->file('file'));

         return redirect()->route('backend_project_index')->with('success', 'Data imported successfully!');
      } catch (Throwable $e) {
         return redirect()->back()->with('error', "Failed to import." . $e->getMessage());
      }
   }
}
