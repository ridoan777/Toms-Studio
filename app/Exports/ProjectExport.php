<?php

namespace App\Exports;

use App\Models\Workflows\Projects\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectExport implements FromCollection, WithHeadings
{
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		return Project::with('projectRelatingBackTo_User')
			// ->limit(50)
			->get()
			->map(function ($project) {
				return [
					'id' => $project->id,
					'project_uid' => $project->project_uid,
					'title' => $project->title,
					'phase' => $project->phase,
					'location' => $project->location,
					'project_manager' => optional($project->projectRelatingBackTo_User)->name,
					'start_date' => $project->start_date,
					'target_date' => $project->target_date,
					'description' => $project->description,
					'image' => $project->image,
					'status' => $project->status,
				];
			});
	}

	public function headings(): array
	{
		return ['id', 'project_uid', 'title', 'phase', 'location', 'project_manager', 'start_date', 'target_date', 'description', 'image', 'status'];
	}
}
