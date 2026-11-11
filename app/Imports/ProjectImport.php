<?php

namespace App\Imports;

use App\Helpers\UidGenerator;
use App\Models\Users\User;
use App\Models\Workflows\Projects\Project;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class ProjectImport implements ToModel, WithHeadingRow
{
	/**
	 * @param array $row
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function model(array $row)
	{
		// dd($row);
		try{
			return new Projects([
				'title' => $row['title'] ?? "no-title (update)",
				'project_uid' => $row['project_uid'] ?? UidGenerator::uniqueName($row['title'], 12, 4),
				'phase' => $row['phase'] ?? "on-track",
				'location' => $row['location'] ?? null,
				'project_manager_id' => $row['project_manager_id'] ?? User::where('user_role', 'project_manager')->value('id'),
				'start_date' => $row['start_date'] ?? now(),
				'target_date' => $row['target_date'] ?? null,
				'description' => $row['description'] ?? null,
			]);
		} catch (Throwable $e) {
			throw $e;
		}
	}
}
