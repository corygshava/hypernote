<?php

namespace App\Traits;

use Carbon\Carbon;

trait UpdateRangeScope
{
	// must use with TimeRangeScope to prevent errors
    public $rangeAtlas = [
		'today',
		'yesterday',
		'juzi',
		'this_week',
		'this_month',
		'this_year',
		'last_week',
		'last_month',
		'last_year',
		'lifetime',
	];

	public function scopeUpdateRange($q, string|array $range){
		$t_field = 'updated_at';

		// Handle array (date range)
		if (is_array($range)) {
			if (count($range) === 2) {
				return $q->whereBetween($t_field, [
					Carbon::parse($range[0])->startOfDay(),
					Carbon::parse($range[1])->endOfDay(),
				]);
			}
			return $q; // Invalid array, return unmodified query
		}

		// Handle single date (string that can be parsed as date)
		if ($this->isDate($range)) {
			// Controller::log_payload('a date was passed to timerange','debug_payload_0');
			$date = Carbon::parse($range);

			return $q->whereBetween($t_field, [
				(clone $date)->startOfDay(),
				(clone $date)->endOfDay(),
			]);
		}

		// Handle named ranges
		$range = strtolower($range);

		return match($range){
			'today' => $q->whereDate($t_field, Carbon::today()),
			'yesterday' => $q->whereBetween($t_field, [
				Carbon::yesterday()->startOfDay(),
				Carbon::yesterday()->endOfDay(),
			]),
			'juzi' => $q->whereBetween($t_field, [
				Carbon::yesterday()->subDay()->startOfDay(),
				Carbon::yesterday()->subDay()->endOfDay(),
			]),
			'kabla_juzi' => $q->whereBetween($t_field, [
				Carbon::today()->subDays(3)->startOfDay(),
				Carbon::today()->subDays(3)->endOfDay(),
			]),
			'this_week' => $q->whereBetween($t_field, [
				Carbon::now()->startOfWeek(),
				Carbon::now()->endOfWeek(),
			]),
			'this_month' => $q->whereMonth($t_field, Carbon::now()->month)
				->whereYear($t_field, Carbon::now()->year),
			'this_year' => $q->whereYear($t_field, Carbon::now()->year),
			'last_week' => $q->whereBetween($t_field, [
				Carbon::now()->subWeek()->startOfWeek(),
				Carbon::now()->subWeek()->endOfWeek(),
			]),
			'last_month' => $q->whereMonth($t_field, Carbon::now()->subMonth()->month)
				->whereYear($t_field, Carbon::now()->subMonth()->year),
			'last_year' => $q->whereYear($t_field, Carbon::now()->subYear()->year),
			'lifetime' => $q,
			default => $q,
		};
	}
}
