<?php

namespace App\Helpers\FileHelpers;

use Illuminate\Database\Eloquent\Model;

class GalleryManagement
{
	public static function handle(array $GALLERIES, string $GALLERY_MODEL_NAME, ?Model $parentObject, string $parentForeignKey, ?string $FILE_FOLDER, ?string $FILE_NAME, bool $rollbackSafety = false, array &$uploadedPaths = [])
	{
		if (empty($GALLERIES)) {
			return [];
		}

		$DISK_FOLDER = config('filesystems.default');
		$storedGallery = [];

		foreach ($GALLERIES as $gallery) {

			if ($gallery instanceof \Illuminate\Http\UploadedFile) {
				$gallery = [
					'id' => null,
					'file' => $gallery,
					'remove_file' => false,
				];
			}

			if (!is_array($gallery)) {
				continue;
			}

			$file = $gallery['file'] ?? null;
			$removeFile = (bool)($gallery['remove_file'] ?? false);

			$existingGallery = !empty($gallery['id'])
				? $GALLERY_MODEL_NAME::findOrFail((int)$gallery['id'])
				: null;

			if ($existingGallery && ((int)$existingGallery->{$parentForeignKey} !== (int)$parentObject->id)) {
				throw new \Exception("The selected file does not belong to this parent.");
			}

			// ---------------- FILE HANDLING ----------------
			$storedfile = null;

			if ($file || $removeFile) {

				$storedfile = FileManagement::handleFile(
					$file,
					$FILE_NAME,
					$existingGallery?->filepath ?? null,
					$FILE_FOLDER,
					$DISK_FOLDER,
					$removeFile
				);

				if ($rollbackSafety && $file && !empty($storedfile['path'])) {
					$uploadedPaths[] = $storedfile['path'];
				}
			}
			// ---------------- FILE HANDLING ----------------

			// ---------------- REMOVE IMAGE ----------------
			if ($removeFile && $existingGallery) {
				$existingGallery->delete();
				continue;
			}
			// ---------------- REMOVE IMAGE ----------------

			$updateData = [
				$parentForeignKey => $parentObject?->id,
				'status' => 1,
			];

			// ONLY touch file fields if file action happened
			if ($file || $removeFile) {
				$updateData['filename'] = $storedfile['file_name'] ?? $existingGallery?->filename;
				$updateData['filepath'] = $storedfile['path'] ?? $existingGallery?->filepath;
				$updateData['metadata'] = $storedfile['meta'] ?? $existingGallery?->metadata;
			}

			if (empty($gallery['id'])) {
				$updateData['creator_id'] = request()->user()?->id;
			} else {
				$updateData['last_modifier_id'] = request()->user()?->id;
			}

			$storedGallery[] = $GALLERY_MODEL_NAME::updateOrCreate(
				['id' => $gallery['id'] ?? null],
				$updateData
			);
		}

		return $storedGallery;
	}
}
/*
	USE CASE-1:

	$IMAGE_FOLDER = 'Lesson_notes/' . $note->note_uid;
	$FILE_NAME = "NOTE-GALLERY-" . ($note?->note_uid);
	$gallery = GalleryManagement::handle($validated, LessonNoteGallery::class, $note, 'note_uid', $IMAGE_FOLDER, $FILE_NAME, null);

	USE CASE-2:

		use array &$uploadedPaths = [] ony when DB::transaction to delelte newly uploaded files.
		Then, bool $rollbackSafety = true

*/