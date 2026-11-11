<?php

namespace App\Helpers\FileHelpers;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Projects\ProjectGallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\{Log, Storage};
use RahulHaque\Filepond\Facades\Filepond;
use Str;

class ChunkFileHelper
{
	public static function singleFile($request, $existingProject, $newProject, $FILE_FOLDER, $FILE_NAME)
	{
		$DISK_FOLDER = config('filesystems.default');

		if ((isset($request?->video) && $request?->video) || $request->boolean('remove_video')) {

			$videoInfo = null;
			if ($request->video) {
				$videoInfo = Filepond::field($request->video)->moveTo($FILE_FOLDER . "/" . $FILE_NAME);
			}

			if ($existingProject && $existingProject?->video) {
				FileManagement::deleteFile($existingProject->video, $DISK_FOLDER);
			}
			$newProject->video = $videoInfo['location'] ?? ($request->boolean('remove_video') ? null : $existingProject?->video);

			return ($newProject->video);
		} else {
			return null;
		}
	}

	public static function multiFileCreate($request, $modelClass, $FILE_FOLDER, $FILE_NAME, $parentKey, $parentId)
	{
		if ($request->video_gallery) {
			$videoGalleryInfos = Filepond::field($request->video_gallery)->moveTo($FILE_FOLDER . "/" . $FILE_NAME);

			foreach ($videoGalleryInfos as $index => $singleItem) {

				if (!$singleItem || empty($singleItem['location'])) {
					// return back()->withErrors(['filepond' => 'Uploaded video file not found or could not be moved.']);
					ExceptionHandling::bailout(500, 'Uploaded video file not found or could not be moved.');
				}

				$modelClass::create([
					// 'project_id' => $project->id,
					$parentKey => $parentId,
					'filename' => $singleItem['filename'] ?? $index,
					'file' => $singleItem['location'],
					'metadata' => [
						"dirname" => $singleItem['dirname'] ?? null,
						"basename" => $singleItem['basename'] ?? null,
						"extension" => $singleItem['extension'] ?? null,
						"mimetype" => $singleItem['mimetype'] ?? null,
					],
					'status' => 1,
				]);
			}
			return true;
		}

	}

	public static function multiFileReplace($validated, $FILE_FOLDER, $FILE_NAME)
	{
		$DISK_FOLDER = config('filesystems.default');

		$ids = collect($validated['edit_video_gallery'])->pluck('id');
		$GALLERIES = $validated['edit_video_gallery'];
		// dd($GALLERIES);
		$dbGalleries = ProjectGallery::whereIn('id', $ids)->get()->keyBy('id');
		$replacements = collect($GALLERIES)->filter(fn($item) => isset($item['replacement']) && $item['replacement'])->pluck('replacement')->values()->all();

		$replacementResults = [];
		$replacementIndex = 0;

		if (!empty($replacements)) {
			$replacementResults = Filepond::field($replacements)->moveTo($FILE_FOLDER . "/" . $FILE_NAME);
		}

		foreach ($GALLERIES as $index => $item) {

			$gallery = $dbGalleries[$item['id']] ?? null;
			if (!$gallery)
				continue;

			if (isset($item['remove']) && (int)$item['remove'] === 1) {
				FileManagement::deleteFile($gallery->file, $DISK_FOLDER);
				$gallery->delete();
				continue;
			} else if (isset($item['replacement']) && $item['replacement']) {
				if (!isset($replacementResults[$replacementIndex]['location'])) {
					continue;
				}
				// dd();
				FileManagement::deleteFile($gallery->file, $DISK_FOLDER);
				$gallery->file = $replacementResults[$replacementIndex]['location'];
				$gallery->save();
				$replacementIndex++;
				continue;
			}
		}

		return null;
	}

}

/*
	USE CASE-1:
		 $project->video = ChunkFileHelper::singleFile($request, $existingProject, $project, $FILE_FOLDER, $singleVideoFilename);

	USE CASE-2:

	ChunkFileHelper::multiFile($validated, $FILE_FOLDER, $singleVideoFilename);
*/