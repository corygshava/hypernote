<?php
	require_once __DIR__.'/codes_datadisplay.php';
?>

<style>
	.page-header{
		padding: var(--size-sm);
		text-align: center;
	}
	.page-part{
		display: flex;
		flex-direction: column;
	}

	/* Filter Bar */
		.filter-bar {
			background: white;
			border-radius: var(--roundness);
			padding: 20px;
			margin-bottom: 20px;
			/*box-shadow: var(--shadow-sm);*/
			/*border: 1px solid var(--border);*/
		}
		.filter-row {
			display: flex;
			gap: 15px;
			flex-wrap: wrap;
			align-items: end;
		}
		.filter-group {
			text-align: left;
			flex: 1;
			min-width: 200px;
			max-width: 300px;
		}
		.filter-group label {
			display: block;
			font-size: 12px;
			font-weight: 600;
			color: var(--gray);
			margin-bottom: 6px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		.filter-control {
			width: 100%;
			padding: 10px 15px;
			border: 1px solid var(--clr-border);
			border-radius: var(--roundness);
			font-size: 14px;
			transition: all 0.3s;
			background: white;
		}
		.filter-control:focus {
			outline: none;
			border-color: var(--primary);
			box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
		}
</style>

<div class="page-header">
	<span class="h4">your notes</span>
	<p>here are your notes</p>
</div>

<div class="page-part" id="notes_container">
	<div data-role="sorter_container" class="w3-center"><div class="loader_2"></div></div>
	<div data-role="notes_stats" class="w3-center"></div>
	<div data-role="filters_container" class="w3-center"></div>
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
		notes_container.querySelector('[data-role="notes_display"]').innerHTML = mekStandin(mekDiv(`loading your notes<div class="loader_2"></div>`,'flow center overflow gap-md'));

		config = {
			entity_name: 'notes',
			apiEndpoint: './data/my_notes',
			statsEndpoint: './data/stats/my_notes',

			// uis
			ui_display: '#notes_container [data-role="notes_display"]',
			ui_sortHead: '#notes_container [data-role="sorter_container"]',
			ui_filterContainer: '#notes_container [data-role="filters_container"]',
			ui_loading: '#notes_container [data-role="loader_ui"]',
			ui_stats: '#notes_container [data-role="loader_ui"]',

			enableSort: true,
			enableFilters: true,
			enableStats: true,

			columns: [
				{
					title: 'Client Name',
					data: 'client_name',
					// sortable: true,
					render: function(value, row) {
						return `
							<div class="d-flex align-items-center gap-sm">
								<div>
									<div style="font-weight: 600;">${value}</div>
									<span style="font-size: 12px; color: var(--clr-text-muted);">${row.client_contact}</span>
								</div>
							</div>
						`;
					}
				},
			],
		};

		let t = classes.get('DataDis');
		ui_load_instance = new t(config);
	}
</script>
