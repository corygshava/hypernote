<?php
	// require_once __DIR__.'/codes_datadisplay.php';
?>

{{-- <!-- --}}
@include('pieces.codes_datadisplay')
{{-- --> --}}

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


	/* Empty State */
		.empty-state {
			text-align: center;
			padding: 60px 20px;
			display: none;
		}
		.empty-state.show {
			display: block;
		}
		.empty-icon {
			width: 80px;
			height: 80px;
			background: var(--primary-light);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 20px;
			color: var(--primary);
			font-size: 32px;
		}
		.empty-state h3 {
			font-size: 18px;
			color: var(--dark);
			margin-bottom: 10px;
		}
		.empty-state p {
			color: var(--gray);
			font-size: 14px;
		}

	/* Toast Notification */
		.toast-container {
			position: fixed;
			top: 20px;
			right: 20px;
			z-index: 3000;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}
		.toast_ {
			background: white;
			border-radius: var(--radius-sm);
			padding: 16px 20px;
			box-shadow: var(--shadow-lg);
			display: flex;
			align-items: center;
			gap: 12px;
			min-width: 300px;
			border-left: 4px solid;
		}
		.toast.success { border-left-color: var(--success); }
		.toast.error { border-left-color: var(--danger); }
		.toast.info { border-left-color: var(--info); }
		.toast-icon {
			width: 24px;
			height: 24px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 12px;
		}
		.toast.success .toast-icon { background: #d1fae5; color: var(--success); }
		.toast.error .toast-icon { background: #fee2e2; color: var(--danger); }
		.toast.info .toast-icon { background: #dbeafe; color: var(--info); }
		.toast-content {
			flex: 1;
		}
		.toast-title {
			font-weight: 600;
			font-size: 14px;
			color: var(--dark);
		}
		.toast-message {
			font-size: 13px;
			color: var(--gray);
			margin-top: 2px;
		}
		.toast-close {
			background: none;
			border: none;
			color: var(--gray);
			cursor: pointer;
			padding: 4px;
		}
</style>

<div class="page-header">
	<span class="h4">your notes</span>
	<p>here are your notes</p>
</div>

<div class="page-part" id="notes_container">
	<div data-role="sorter_container" class="w3-center"><div class="loader_2"></div></div>
	<div data-role="notes_stats" class="w3-center stats-holder"></div>
	<div data-role="filters_container" class="w3-center"></div>
	<!-- <div data-role="stats_ui"></div> -->
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
			ui_loading: '#notes_container [data-role="loader_ui"]',
			ui_stats: '#notes_container [data-role="notes_stats"]',

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
