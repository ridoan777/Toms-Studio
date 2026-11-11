<?php

namespace App\Helpers;

use App\Models\Users\User;
use Illuminate\Support\Str;

class UidGenerator
{
	public static function uniqueULID($userName = null, $nameLength = null, $bodyCut = 13, $tailCut = 4): string
	{
		$oldNameSanitizedPartial = $userName ? substr(self::sanitizeName($userName), 0, $nameLength) : null;
		$ulid = (string)Str::ulid();

		// $body = substr($ulid, 0, $bodyCut);	// taking chars from the starting point
		$body = substr($ulid, -$bodyCut);	// taking chars from the ending point
		$middle = substr($body, 0, $bodyCut - $tailCut);
		$middle = substr($middle, 0, 6) . '-' . substr($middle, 6);	// adding a '-' after 6 char

		$tail = substr($body, -$tailCut);

		$generatedUid = ($oldNameSanitizedPartial ? $oldNameSanitizedPartial . '-' : null) . strtoupper($middle) . '-' . strtoupper($tail);

		return $generatedUid;
	}
	// ------------------------------------------------------------------------------------
	public static function uniqueUsername($userName, $nameLength = null, $randomLength = 8): string
	{
		do {
			$oldNameSanitizedPartial = substr(self::sanitizeName($userName), 0, $nameLength);
			$generatedUid = $oldNameSanitizedPartial . '_' . strtoupper(Str::random($randomLength));
		} while (User::where('user_uid', $generatedUid)->exists());

		return $generatedUid;
	}
	// ------------------------------------------------------------------------------------

	public static function uniqueName($userName, $nameLength = null, $randomLength = 3): string
	{
		$oldNameSanitizedPartial = substr(self::sanitizeName($userName), 0, $nameLength);

		$generatedUid = now()->format('dmy') . "-" . $oldNameSanitizedPartial . "-" . strtoupper(Str::random($randomLength));

		return $generatedUid;
	}
	// ------------------------------------------------------------------------------------

	private static function sanitizeName(string $input): string
	{
		$clean = preg_replace('/[^A-Za-z0-9\-\_\[\]\(\)]/', '', $input);
		// Replace every character that is not A–Z, a–z, 0–9, -, _, [, ], (, or ) with '' (trimmed space/blank)
		return trim($clean, '_'); // remove (_) underscore from the first and last character
	}
}
/*
	USE CASE-1:
		$PROJECT_UID = UidGenerator::uniqueULID($validated['title'], 12, 15, 4);

	USE CASE-2:
		$PROJECT_UID = UidGenerator::uniqueName($validated['title'], 12, 4);

	USE CASE-2:
		UidGenerator::uniqueUsername($item['name'], 8, 4);
*/