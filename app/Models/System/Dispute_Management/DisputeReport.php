<?php

namespace App\Models\System\Dispute_Management;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class DisputeReport extends Model
{
	protected $table = 'dispute_reports';

	protected $fillable = [
		'ticket',

		'dispute_report_type',
		'dispute_report_id',

		'issue_section',
		'victim_id',
		'accused_id',

		'original_id',
		'original_content',

		'issue_label',
		'description',

		'message_victim',
		'message_accused',

		'is_resolved',
		'status',
	];

	protected function casts(): array
	{
		return [
			'dispute_report_id' => 'integer',
			'victim_id' => 'integer',
			'accused_id' => 'integer',
			'original_id' => 'integer',
			'is_resolved' => 'integer',
			'status' => 'integer',
		];
	}

	// // ----------------- RELATIONSHIPS -----------------
	public function disputeReport()
	{
		return $this->morphTo();
	}

	public function victimRelationWith_User()
	{
		return $this->belongsTo(User::class, 'victim_id', 'id');
	}

	public function accusedRelationWith_User()
	{
		return $this->belongsTo(User::class, 'accused_id', 'id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeReportableId($query, $dispute_report_id)
	{
		return $query->where('dispute_report_id', $dispute_report_id);
	}

	public function scopeReportableType($query, $dispute_report_type)
	{
		return $query->where('dispute_report_type', $dispute_report_type);
	}

	public function scopeIssueSection($query, $issue_section)
	{
		return $query->where('issue_section', $issue_section);
	}

	public function scopeVictim($query, $victim_id)
	{
		return $query->where('victim_id', $victim_id);
	}

	public function scopeAccused($query, $accused_id)
	{
		return $query->where('accused_id', $accused_id);
	}

	public function scopeIsResolved($query, $is_resolved)
	{
		return $query->where('is_resolved', $is_resolved);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
