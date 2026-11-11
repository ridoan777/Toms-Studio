<?php

namespace App\Exports;

use App\Models\Users\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		return User::limit(50)
			->get(['id', 'user_uid', 'name', 'user_role', 'phone', 'email', 'password', 'status'])
			->map(function ($user) {
				return [
					'id' => $user->id,
					'user_uid' => $user->user_uid,
					'name' => $user->name,
					'user_role' => $user->user_role,
					'phone' => $user->phone,
					'email' => $user->email,
					'password' => $user->password,
					'status' => $user->status,
				];
			});
	}

	public function headings(): array
	{
		return ['id', 'user_uid', 'name', 'user_role', 'phone', 'email', 'password', 'status'];
	}
}
