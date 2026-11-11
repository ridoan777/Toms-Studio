<?php

namespace App\Imports;

use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsersImport implements ToModel, WithHeadingRow
{
	/**
	 * @param array $row
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function model(array $row)
	{
		// dd($row);
		return new User([
			'user_uid' => $row['user_uid'],
			'name' => $row['name'],
			'user_role' => $row['user_role'],
			'phone' => $row['phone'],
			'status' => $row['status'],
			'email' => $row['email'],
			'email_verified_at' => now(),
			'password' => Hash::make($row['password']),
		]);
	}
}
