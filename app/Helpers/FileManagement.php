<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\{Log, Storage};
use Str;

class FileManagement
{
	private static $modifiedFileName = '';
	private static $DBFriendlyFileName = '';

	public static function handleFile($fileInput, $title, $oldFile = null, $folder, $disk = 'public', $remove = false)
	{
		if ($remove && $oldFile && Storage::disk($disk)->exists($oldFile)) {
			Storage::disk($disk)->delete($oldFile);
			return null;
		}

		if ($fileInput) {
			if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
				Storage::disk($disk)->delete($oldFile);
			}

			return [
				'path' => self::storeFile($fileInput, $title, $folder, $disk),
				'file_name' => self::getDirectoryFreeName(),
				'meta' => self::extractMetadata($fileInput),
			];
		}

		return $remove ? null : $oldFile;
	}

	// ------------------------------------------------------------------------------------
	public static function storeFile($file, $title, $folder, $disk = 'public'): string
	{
		$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$fileExtension = $file->getClientOriginalExtension();

		$oldFilenamePartial = substr(self::sanitizeForFilename($originalName), 0, 9);
		$cleanTitle = !empty($title) ? self::sanitizeForFilename(strip_tags($title)) : 'no_title';
		$postTitlePartial = substr($cleanTitle, 0, 40);

		self::$modifiedFileName = "{$postTitlePartial}_{$oldFilenamePartial}_" . uniqid() . ".{$fileExtension}";
		self::$DBFriendlyFileName = trim($folder, '/') . '/' . self::$modifiedFileName;

		Storage::disk($disk)->putFileAs($folder, $file, self::$modifiedFileName);

		return self::$DBFriendlyFileName;
	}

	// ------------------------------------------------------------------------------------
	public static function socialLoginAvatar($fileUrl, $oldFile, $title, $folder, $disk)
	{
		if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
			return null;
		}
		$content = @file_get_contents($fileUrl);
		if ($content === false) {
			return null;
		}
		// ----------- Delete Old Avatar -----------
		if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
			Storage::disk($disk)->delete($oldFile);
		}
		// ----------- Delete Old Avatar -----------

		// ----------- Modify Name -----------
		$cleanTitle = !empty($title) ? self::sanitizeForFilename(strip_tags($title)) : 'no_title';
		$postTitlePartial = substr($cleanTitle, 0, 20);
		$modifiedFileName = "{$postTitlePartial}_" . uniqid() . ".jpg";
		$DBFriendlyFileName = trim($folder, '/') . '/' . $modifiedFileName;
		// ----------- Modify Name -----------

		Storage::disk($disk)->put($DBFriendlyFileName, $content);
		return [
			'path' => $DBFriendlyFileName,
			'file_name' => $modifiedFileName,
		];
	}
	// ------------------------------------------------------------------------------------
	public static function sanitizeForFilename(string $input): string
	{
		$clean = preg_replace('/[^A-Za-z0-9\-\_\[\]\(\)]/', '_', $input);
		return trim($clean, '_');
	}

	// ------------------------------------------------------------------------------------
	public static function getDirectoryFreeName(): string
	{
		return self::$modifiedFileName;
	}

	// ------------------------------------------------------------------------------------
	public static function deleteFile(?string $oldFilePath, string $disk = 'public'): bool
	{
		if ($oldFilePath && Storage::disk($disk)->exists($oldFilePath)) {
			Storage::disk($disk)->delete($oldFilePath);
			return true;
		}
		return false;
	}

	// ------------------------------------------------------------------------------------
	public static function deleteFolder(?string $folderPath, string $disk = 'public'): bool
	{
		if ($folderPath && Storage::disk($disk)->exists($folderPath)) {
			Storage::disk($disk)->deleteDirectory($folderPath);
			return true;
		}
		return false;
	}

	// ------------------------------------------------------------------------------------
	public static function getFileUrl(?string $filePath, string $disk = 'public'): ?string
	{
		if (!$filePath)
			return null;
		return Storage::disk($disk)->url($filePath);
	}
	// ------------------------------------------------------------------------------------
	public static function extractMetadata(UploadedFile $file): array
	{
		$sizeBytes = $file->getSize();
		$mime = $file->getClientMimeType();

		$metadata = [
			// ---------- Identity ----------
			'original_name' => $file->getClientOriginalName(),
			'extension' => $file->getClientOriginalExtension(), // user-provided
			'guessed_ext' => $file->guessExtension(),              // server guess
			'mime' => $mime,

			// ---------- Size ----------
			'size_bytes' => $sizeBytes,
			'size_kb' => round($sizeBytes / 1024, 2),
			'size_mb' => round($sizeBytes / 1024 / 1024, 2),

			// ---------- Upload state ----------
			'is_valid' => $file->isValid(),
			'error_code' => $file->getError(),
			'error_message' => $file->getErrorMessage(),

			// ---------- File type flags ----------
			'mimetype' => [
				'is_image' => Str::startsWith($mime, 'image/'),
				'is_audio' => Str::startsWith($mime, 'audio/'),
				'is_video' => Str::startsWith($mime, 'video/'),
				'is_text' => Str::startsWith($mime, 'text/'),

				// ---------- Document flags ----------
				'is_pdf' => $mime === 'application/pdf',
				'is_word' => in_array($mime, [
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				]),
				'is_excel' => in_array($mime, [
					'application/vnd.ms-excel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				]),
				'is_csv' => in_array($mime, [
					'text/csv',
					'application/csv',
				]),
			],
		];

		// ---------- Image-only metadata ----------
		if ($metadata['mimetype']['is_image']) {
			$imageInfo = @getimagesize($file->getPathname());
			if ($imageInfo) {
				$metadata['image'] = [
					'width' => $imageInfo[0],
					'height' => $imageInfo[1],
					'mime' => $imageInfo['mime'] ?? null,
				];
			}
		}

		return $metadata;
	}

	########################## FAVICON + SITE ASSETS #########################

	public static function handleDirectPublicAsset($fileInput, $title, $oldFile = null, $subfolder = 'logo_n_icons', $remove = false)
	{
		// ------------------ REMOVAL ------------------
		if ($remove) {
			if ($oldFile) {
				$publicPath = public_path($oldFile);
				if (file_exists($publicPath)) {
					unlink($publicPath);
				}
			}
			return null;
		}
		// ------------------ UPLOAD ------------------
		if ($fileInput) {
			// Remove old file first if exists
			if ($oldFile) {
				$publicPath = public_path($oldFile);
				if (file_exists($publicPath)) {
					unlink($publicPath);
				}
			}

			// Store new file
			return self::storeDirectPublicAsset($fileInput, $title, $subfolder);
		}

		// ------------------ NO CHANGE ------------------
		return $oldFile;
	}

	public static function storeDirectPublicAsset($file, $title, $subfolder = 'logo_n_icons'): ?string
	{
		if (!$file) {
			return null;
		}

		// ------------------ SPECIAL CASE: FAVICON ------------------
		if (strtolower($title) === 'favicon') {
			$folderPath = public_path();
			$targetFile = $folderPath . DIRECTORY_SEPARATOR . 'favicon.ico';

			if (file_exists($targetFile)) {
				unlink($targetFile);
			}

			$extension = strtolower($file->getClientOriginalExtension());
			$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

			$newFileName = "app_favicon" . "." . "{$extension}";

			$file->move($folderPath, $newFileName);

			return $newFileName;
		}

		// ------------------ LOGO OR OTHER FILES ------------------
		$folderPath = public_path("site_assets/{$subfolder}");
		$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0775, true);
		}

		$extension = strtolower($file->getClientOriginalExtension());
		$newFileName = "app_logo" . "." . "{$extension}";
		$file->move($folderPath, $newFileName);

		return "site_assets/{$subfolder}/{$newFileName}";
	}

	###################################################

	public static function videoDuration($filePath)
	{
		if (!file_exists($filePath)) {
			return 0;
		}

		try {
			$getID3 = new \getID3;
			$fileInfo = $getID3->analyze($filePath);

			Log::info('Video analysis', [
				'file' => basename($filePath),
				'playtime_seconds' => $fileInfo['playtime_seconds'] ?? 'not found',
				'playtime_string' => $fileInfo['playtime_string'] ?? 'not found'
			]);

			if (!empty($fileInfo['playtime_seconds'])) {
				$durationInSeconds = (float) $fileInfo['playtime_seconds'];
				$minutes = round($durationInSeconds / 60);
				return $minutes;
			}

			return 0;
		} catch (\Exception $e) {
			Log::error('Video duration error: ' . $e->getMessage());
			return 0;
		}
	}

}