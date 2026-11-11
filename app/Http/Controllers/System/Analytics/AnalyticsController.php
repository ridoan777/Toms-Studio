<?php

namespace App\Http\Controllers\System\Analytics;

use App\Helpers\Errors\ExceptionHandling;
use App\Http\Controllers\Controller;
use App\Models\Users\User;
use App\Models\Workflows\Delivery\Delivery;
use App\Models\Workflows\Projects\Project;
use App\Models\Workflows\Tools\{Tools, ToolTracking};
use Illuminate\Http\Request;
use Throwable;

class AnalyticsController extends Controller
{

	public function index(Request $request)
	{
		$totalUsers = User::whereNotNull('email_verified_at')->status(1)->count();
		$totalProjects = Project::status(1)->count();
		// $totalDeliveries = Delivery::status(1)->count();
		// $totalTools = Tools::status(1)->count();


		return view('Admin.sidebar.Analytics.index', compact('totalUsers', 'totalProjects'));
	}

	// ---------------------- USER ----------------------
	public function personnels()
	{
		try {
			$roles = ['admin', 'student', 'bar_admin', 'guest'];

			$data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
				->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
				->whereIn('roles.role_key', $roles)
				->selectRaw('roles.role_key, COUNT(*) as count')
				->groupBy('roles.role_key')
				->pluck('count', 'roles.role_key');

			// $data = User::selectRaw('user_role, COUNT(*) as count')->whereIn('user_role', $roles)->groupBy('user_role')->pluck('count', 'user_role');

			return response()->json([
				'status' => true,
				'message' => 'User roles analytics fetched successfully!',
				'data' => [
					'labels' => $data->keys(),
					'values' => $data->values(),
				],
				'code' => 200
			]);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching donut chart data', $e);
		}
	}
	// ---------------------- USER ----------------------


	// ---------------------- PROJECTS ----------------------

	public function projectPhasesChart()
	{
		// Count project phases
		try {
			$data = Project::select('phase')->selectRaw('COUNT(*) as total')->groupBy('phase')->pluck('total', 'phase');

			return response()->json([
				'status' => true,
				'message' => 'Project phase chart data fetched!',
				'data' => [
					'labels' => $data->keys(),     // phases
					'values' => $data->values(),   // counts
				],
				'code' => 200
			]);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching donut chart data', $e);
		}
	}

	public function projectClosingLinesOld()
	{
		try {
			$data = Project::selectRaw("DATE_FORMAT(target_date, '%Y-%m') as month, COUNT(*) as total")
				->whereNotNull('target_date')
				->groupBy('month')
				->orderBy('month', 'ASC')
				->pluck('total', 'month');

			return response()->json([
				'status' => true,
				'message' => 'Project closing date chart data fetched!',
				'data' => [
					'labels' => $data->keys(),     // YYYY-MM values
					'values' => $data->values(),   // project counts
				],
				'code' => 200
			]);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching project closing-line chart', $e);
		}
	}

	public function projectClosingLines()
	{
		try {
			$projects = Project::whereNotNull('target_date')
				->selectRaw("DATE_FORMAT(target_date, '%Y-%m') as month, title, project_uid")
				->orderBy('target_date')
				->get()
				->groupBy('month');

			$labels = $projects->keys();   // months
			$values = $projects->map->count()->values();
			$names = $projects->map(function ($group) {
				return $group->map(function ($p) {
					return $p->title . " (" . $p->project_uid . ")";
				})->values();
			});

			return response()->json([
				'status' => true,
				'message' => 'Project closing data fetched!',
				'data' => [
					'labels' => $labels,
					'values' => $values,
					'names' => $names,    // sending project names per point
				],
				'code' => 200
			]);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('closing-line chart', $e);
		}
	}
	// ---------------------- PROJECTS ----------------------

	/*
	// ---------------------- DELIVERY ----------------------

	public function deliveryScheduleLines()
	{
		try {
			$deliveries = Delivery::whereNotNull('delivery_date')
				->selectRaw("DATE_FORMAT(delivery_date, '%Y-%m') as month, name, delivery_uid")
				->orderBy('delivery_date')
				->get()
				->groupBy('month');

			$labels = $deliveries->keys();   // months
			$values = $deliveries->map->count()->values();
			$names = $deliveries->map(function ($group) {
				return $group->map(function ($p) {
					return $p->name . " (" . $p->delivery_uid . ")";
				})->values();
			});

			return response()->json([
				'status' => true,
				'message' => 'Delivery scheduled dates fetched!',
				'data' => [
					'labels' => $labels,
					'values' => $values,
					'names' => $names,
				],
				'code' => 200
			]);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('scheduled-line chart', $e);
		}
	}
	// ---------------------- DELIVERY ----------------------

	// ---------------------- INVENTORY ----------------------

	public function toolsUsageBarNLines()
	{
		try {
			// monthly usage 
			$usage = ToolTracking::where('use_type', 'usage')
				->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
				->groupBy('month')
				->orderBy('month')
				->pluck('total', 'month');

			// monthly maintenance
			$maintenance = ToolTracking::where('use_type', 'maintenance')
				->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
				->groupBy('month')
				->orderBy('month')
				->pluck('total', 'month');

			// Merge months
			$allMonths = collect(array_unique(
				array_merge($usage->keys()->toArray(), $maintenance->keys()->toArray())
			))->sort()->values();

			$usageCounts = [];
			$maintenanceCounts = [];

			foreach ($allMonths as $month) {
				$usageCounts[] = $usage[$month] ?? 0;
				$maintenanceCounts[] = $maintenance[$month] ?? 0;
			}

			return response()->json([
				'status' => true,
				'message' => 'Tools usage chart data fetched!',
				'data' => [
					'labels' => $allMonths,
					'usage' => $usageCounts,
					'maintenance' => $maintenanceCounts,
				],
				'code' => 200
			]);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching tools usage chart', $e);
		}
	}

	// ---------------------- INVENTORY ----------------------
	*/
}
