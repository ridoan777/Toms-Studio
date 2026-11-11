<?php

namespace App\Http\Controllers\Workflows\Projects;

use App\Helpers\ApiJsonReturnHelper;
use App\Helpers\FileHelpers\FileManagement;
use App\Helpers\UidGenerator;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Projects\Project;
use App\Models\Workflows\Projects\ProjectGallery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Throwable;


class ApiProjectController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         // new Middleware(RoleMiddleware::using('user|admin|student'), only: ['allProjects']),
         // new Middleware('verify_role_key:admin', only: ['allProjects']),
         new Middleware(PermissionMiddleware::using('project_index'), only: ['index']),
         new Middleware(PermissionMiddleware::using('project_show'), only: ['show']),
      ];
   }
   // -----------------------------------------------

   public function index()
   {
      try {
         $allProjects = Project::with('projectRelatingBackTo_User:id,user_uid,name,user_role,email,phone,gender,avatar,timezone,status')->get();

         // ------------- TRANSFORM project_relating_back_to__user TO Manager -------------
         $allProjects->transform(function ($project) {
            $project->manager_details = $project->projectRelatingBackTo_User;
            unset($project->projectRelatingBackTo_User);
            return $project;
         });

         return ApiJsonReturnHelper::handle(true, 200, "All projects have been fetched successfully!", $allProjects);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching projects', $e);
      }
   }
   // -----------------------------------------------

   public function show($id)
   {
      $MESSAGE = "This project either doesn't exist or is already deleted!";
      try {
         $singleProject = Project::with('projectRelatingBackTo_User:id,user_uid,name,user_role,email,phone,gender,avatar,timezone,status')->where('id', $id)->first();

         if ($singleProject) {
            $singleProject->manager = $singleProject->projectRelatingBackTo_User;
            unset($singleProject->projectRelatingBackTo_User);
            $MESSAGE = "A single project has been fetched successfully!";
         }

         return ApiJsonReturnHelper::handle(true, 200, $MESSAGE, $singleProject);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching projects', $e);
      }
   }
   // -----------------------------------------------

   public function store(Request $request)
   {
      DB::beginTransaction();
      try {
         $validated = $request->validate([
            'id' => 'nullable|integer|exists:projects,id',
            'title' => 'required|string|max:120',

            'file' => 'nullable|array',
            'file.*.id' => 'nullable|integer|exists:project_galleries,id',
            'file.*.file' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10485',
            'file.*.remove_file' => ['nullable', 'integer'],
         ]);
         $USER = $request->user();
         $DISK_FOLDER = config('filesystems.default');
         $existingProject = null;
         $managerId = null;

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingProject = Project::find($validated['id']);
            $PROJECT_UID = $existingProject->project_uid;
            $managerId = $existingProject?->project_manager_id;
         } else {
            $PROJECT_UID = UidGenerator::uniqueName($validated['title'], 12, 4);
            $managerId = $USER->id;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         // ---------------- PROJECT SAVE -------------------------
         $project = Project::updateOrCreate(
            [
               'id' => $validated['id'] ?? null
            ],
            [
               'project_uid' => $PROJECT_UID,

               'project_manager_id' => (int)$managerId,
               'title' => $validated['title'],
            ]
         );
         // ---------------- PROJECT SAVE -------------------------

         // ------------ IMAGE FILE HANDLING ------------
         $IMAGE_FOLDER = 'Projects/' . $PROJECT_UID;

         $GALLERIES = $validated['file'] ?? [];
         $filesToDelete = [];

         if (count($GALLERIES) !== 0) {
            $ids = collect($GALLERIES)->pluck('id')->filter()->all();
            $dbGalleries = ProjectGallery::whereIn('id', $ids)->project($project->id)->get()->keyBy('id');

            foreach ($GALLERIES as $item) {

               if (!empty($item['id'])) {
                  $mapDbGallery = $dbGalleries[$item['id']] ?? null;
                  if (!$mapDbGallery)
                     continue;

                  # -------- CASE-1 : REMOVE --------
                  if (isset($item['remove_file']) && (int)$item['remove_file'] === 1) {
                     // FileManagement::deleteFile($mapDbGallery->file, $DISK_FOLDER);
                     $filesToDelete[] = $mapDbGallery->file;
                     $mapDbGallery->delete();
                     continue;
                  }
                  # -------- CASE-2 : replace --------
                  else if (!empty($item['file'])) {

                     // ------------ IMAGE FILE HANDLING ------------
                     $replacedFile = FileManagement::handleFile(
                        $item['file'],
                        "IMG-" . ($validated['title'] ?? 'no-title'),
                        $mapDbGallery?->file ?? null,
                        $IMAGE_FOLDER,
                        $DISK_FOLDER,
                        0
                     );
                     // ------------ IMAGE FILE HANDLING ------------
                     $mapDbGallery->filename = $replacedFile['file_name'] ?? $mapDbGallery->filename;
                     $mapDbGallery->file = $replacedFile['path'] ?? $mapDbGallery->file;
                     $mapDbGallery->metadata = $replacedFile['meta'] ?? $mapDbGallery->metadata;
                     $mapDbGallery->save();
                     continue;
                  }
               }
               # -------- CASE-3 : new --------
               else if (empty($item['id']) && !empty($item['file'])) {
                  // ------------ IMAGE FILE HANDLING ------------
                  $newFile = FileManagement::handleFile(
                     $item['file'],
                     "IMG-" . ($validated['title'] ?? 'no-title'),
                     null,
                     $IMAGE_FOLDER,
                     $DISK_FOLDER,
                     0
                  );
                  // ------------ IMAGE FILE HANDLING ------------

                  ProjectGallery::create([
                     'project_id' => $project->id,
                     'filename' => $newFile['file_name'] ?? null,
                     'file' => $newFile['path'],
                     'metadata' => $newFile['meta'] ?? null,
                     'status' => 1,
                  ]);
                  continue;
               }
            }
         }

         $projectGallery = ProjectGallery::project($project?->id)->select(['id', 'project_id', 'filename', 'file', 'status'])->get();

         DB::commit();
         foreach ($filesToDelete as $file) {
            FileManagement::deleteFile($file, $DISK_FOLDER);
         }

         return ApiJsonReturnHelper::handle(true, 200, "A project has been saved sucessfully!", $projectGallery);
      } catch (Throwable $e) {
         DB::rollback();
         return ExceptionHandling::handle('saving a project', $e);
      }
   }
}