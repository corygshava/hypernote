<?php
	// require_once __DIR__.'/codes_datadisplay.php';
?>

{{-- <!-- --}}
@include('pieces.codes_datadisplay')
{{-- --> --}}

<style>
</style>

<div class="page-header">
	<span class="h4">your notes</span>
	<p>here are your notes</p>
</div>

<div class="page-part" id="notes_container">
	<div data-role="notes_stats" class="w3-center stats-holder w3-display-container">
		<div class="stat-card-modern" style="opacity: 0;">
			<div class="stat-icon-modern primary">
				<i class="fas fa-chart-line"></i>
			</div>
			<div class="stat-info-modern">
				<h3>0</h3>
				<p>All Notes</p>
			</div>
		</div>

		<div class="w3-display-middle">
			<div>loading stats</div>
			<div class="loader_2"></div>
		</div>
	</div>

	<div class="collapser spacy-tn themeround border-bottom onshow_border">
		<div class="spacy-sm m_pointer topper" data-toggle="collapse" data-target='[data-role="sort_options"]'>
			<span class="text-decoration-none flowline spread centerline" href="#sampleInfo">
				<span class="text-uppercase font-weight-bold">Filter options</span>
				<!-- <input type="text" name=""> -->
				<i class="fa fa-chevron-down myicon"></i>
			</span>
		</div>
		<div class="collapse" data-role="sort_options">
			<div data-role="filters_container" class="w3-center"></div>
			<div data-role="sorter_container" class="w3-center">
				<div class="loader_2"></div>
			</div>
			<!-- <div data-role="stats_ui"></div> -->
		</div>
	</div>

	<div class="w3-display-container">
		<div data-role="notes_display"></div>
		<div data-role="loader_ui"></div>
	</div>
</div>

<script>
	window['killOnLeave'] = [
		'ui_load_instance',
		'loadMyNotes',
	];

	window['ui_load_instance'] = undefined;
	window['runOnAwake'] = () => {
		loadMyNotes();
	}

	window['loadMyNotes'] = () => {
		// notes_container.querySelector('[data-role="notes_display"]').innerHTML = mekStandin(mekDiv(`loading your notes<div class="loader_2"></div>`,'flow center overflow gap-md'));

		config = {
			entity_name: 'notes',
			apiEndpoint: './data/my_notes',
			statsEndpoint: './data/stats/my_notes',

			// uis
			ui_display: '#notes_container [data-role="notes_display"]',
			ui_sortHead: '#notes_container [data-role="sorter_container"]',
			ui_filterContainer: '#notes_container [data-role="filters_container"]',
			ui_stats: '#notes_container [data-role="notes_stats"]',

			enableSort: true,
			enableFilters: true,
			enableStats: true,

			columns: [
				{
					title: 'title',
					data: 'title',
					sortable: true,
				},
				{
					title: 'body',
					data: 'body',
				},
				{
					title: 'Created At',
					data: 'created_at',
					sortable: true,
				},
				{
					title: 'Updated At',
					data: 'updated_at',
					sortable: true,
				},
			],
		};

		let t = classes.get('DataDis');
		ui_load_instance = new t(config);
	}
</script>
