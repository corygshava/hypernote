<?php
    /*
	require_once __DIR__.'/codes_datadisplay.php';
	require_once __DIR__.'/piece_shownote.php';
    // */
?>

{{-- <!-- --}}
@include('pieces.codes_datadisplay')
@include('pieces.piece_shownote')
{{-- --> --}}

<style>
	[data-role="notes_display"]{
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		gap: 16px;
	}

	/* note card styles */
		.social-card {
			background: var(--clr-panelbg);
			border: 1px solid transparent;
			border-radius: var(--roundness);
			padding: 16px;
			backdrop-filter: blur(10px);
			position: relative;
			z-index: 0;
			flex: 1 0 300px;
			/*max-width: 300px;*/
		}
		.social-card:hover {
			border: 1px solid var(--clr-border);
			background: var(--clr-panelbg2);
		}

		.social-card .card-actions {
			display: flex;
			gap: 0.5rem;
			padding: 8px;
		}

		.social-card .note-category {
			font-size: 0.75rem;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			color: var(--themecolor);
			font-weight: 600;
			margin-bottom: 0.5rem;
			display: inline-block;
		}

		.social-card .note-heading {
			font-size: 1.25rem;
			font-weight: 600;
			margin-bottom: 0.75rem;
			line-height: 1.4;
		}

		.social-card .note-body {
		/*color: var(--text-secondary);*/
		font-size: 0.9375rem;
		line-height: 1.6;
		margin-bottom: 1.25rem;
	}

		@media(min-width: 600px){
			/* for tabs and PCs */
			.social-card{
				max-width: 400px;
			}
		}

		.social-card .my-card-header{
			border-color: var(--clr-modeglass);
		}

		.social-card .privacy_state_btn{
			padding: 4px 8px;
			border-radius: var(--roundness-sm);
			background: var(--bg,var(--theme-glass));
			color: var(--fg, var(--themecolor));
			font-size: 0.7rem;
		}
		.privacy_state_btn.t_1 {
			/* public notes tag */
		    --bg: #7a00ff;
		    --fg: #fff;
		}
		.privacy_state_btn.t_2 {
			/* unlisted notes tag */
		    --bg: #ff009354;
		    --fg: #fff;
		}
		.privacy_state_btn.t_3 {
			/* unlisted notes tag */
		    --bg: slategrey;
		    --fg: #fff;
		}
		.privacy_state_btn.t_6 {
			/* unlisted notes tag */
		    --bg: slategrey;
		    --fg: #fff;
		}

	/* reusables */
		.custom_drop.sm {
		    width: auto !important;
		    font-size: 0.8rem;
		    min-width: 100px !important;
		    overflow: hidden;
		}
		.custom_drop.sm .dropdown-item{
		    font-weight: 700 !important;
		    color: var(--clr-text);
		    cursor: pointer;
		}
		.custom_drop.sm .dropdown-item:hover{
			background: var(--clr-text);
			color: var(--clr-alttext);
		}
</style>

<style>
	.feed-stream {
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
	}

	/* User Header Section */
		.card-user-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 1.25rem;
		}

		.user-info-group {
			display: flex;
			align-items: center;
			gap: 0.75rem;
		}

		.user-avatar {
			width: 48px;
			height: 48px;
			border-radius: 50%;
			object-fit: cover;
			border: 2px solid rgba(255,255,255,0.1);
		}

		.user-details h4 {
			font-size: 0.9375rem;
			font-weight: 600;
			color: var(--text-primary);
			margin-bottom: 0.125rem;
			display: flex;
			align-items: center;
			gap: 0.25rem;
		}

		.verified-badge {
			color: var(--accent-cyan);
			font-size: 0.75rem;
		}

		.user-stats {
			font-size: 0.75rem;
			color: var(--text-muted);
		}

		.user-stats span {
			color: var(--text-secondary);
			font-weight: 500;
		}

	/* Action Buttons in Card */
		.btn-follow {
			background: rgba(180, 77, 255, 0.1);
			color: var(--accent-purple);
			border: 1px solid rgba(180, 77, 255, 0.2);
		}
		.btn-follow:hover { background: var(--accent-purple); color: white; }
		.btn-follow.following { background: transparent; color: var(--text-muted); border-color: var(--border); }

		.btn-view-profile {
			background: transparent;
			color: var(--text-muted);
			border: 1px solid var(--border);
		}
		.btn-view-profile:hover { border-color: var(--text-secondary); color: var(--text-primary); }

	/* Note Content */


	/* Card Footer */
		.card-footer {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-top: 1rem;
			border-top: 1px solid rgba(255,255,255,0.05);
		}

		.timestamp {
			font-size: 0.75rem;
			color: var(--text-muted);
			display: flex;
			align-items: center;
			gap: 0.5rem;
		}

		.interaction-stats {
			display: flex;
			gap: 1rem;
			font-size: 0.875rem;
			color: var(--text-muted);
		}

		.stat-item {
			display: flex;
			align-items: center;
			gap: 0.375rem;
			cursor: pointer;
			transition: var(--transition);
		}
		.stat-item:hover { color: var(--accent-pink); }
		.stat-item.liked { color: var(--accent-pink); }
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

	<div class="w3-display-container py-2">
		<div data-role="notes_overview"></div>
		<div data-role="notes_display" style="margin-bottom: 64px;"></div>
		<div data-role="loader_ui"></div>
	</div>
</div>

<script>
	window['killOnLeave'] = [
		'ui_load_instance',
		'loadMyNotes',
	];

	window['ui_load_instance'] = undefined;
	window['last_fetched_notes'] = [];
	window['runOnAwake'] = () => {
		loadMyNotes();
	}

	window['loadMyNotes'] = () => {
		// notes_container.querySelector('[data-role="notes_display"]').innerHTML = mekStandin(mekDiv(`loading your notes<div class="loader_2"></div>`,'flow center overflow gap-md'));

		let d_config = {
			entity_name: 'notes',
			apiEndpoint: './data/my_notes',
			statsEndpoint: './data/stats/my_notes',

			// uis
			ui_display: '#notes_container [data-role="notes_display"]',
			ui_overview: '#notes_container [data-role="notes_overview"]',
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

			itemRenderer: (data) => {render_note(data)},
		};

		window['render_note'] = (dta) => {
			alert_info('rendering data');
			let config = d_config;
			let f_notes = dta.data;
			let display_ui = document.querySelector(config.ui_display);
			let overview_ui = document.querySelector(config.ui_overview);

			last_fetched_notes = f_notes;

			if(dta.data.length == 0){
				display_ui.innerHTML = ui_load_instance.gen_empty_ui();
				return;
			}

			let outht = '';

			overview_ui.innerHTML = mekDiv(mekBold('the Notes','h4'),'spacy-sm');

			f_notes.forEach((d,n) => {
				let t_ht;
				/*
				t_ht = `
					<div class="social-card">
						<div class="card-user-header">
							<div class="user-info-group">
								<img src="https://i.pravatar.cc/150?u=sarah" alt="Sarah Jenkins" class="user-avatar">
								<div class="user-details">
									<h4>Sarah Jenkins <i class="fas fa-check-circle verified-badge"></i></h4>
									<div class="user-stats">
										<span>142</span> notes • @sarahj_dev
									</div>
								</div>
							</div>
							<div class="card-actions">
								<button class="btn-sm btn-view-profile">View Profile</button>
								<button class="btn-sm btn-follow" onclick="toggleFollow(this)">Follow</button>
							</div>
						</div>

						<div class="note-content-area">
							<span class="note-category">Design Systems</span>
							<h3 class="note-heading">The Psychology of Dark Mode</h3>
							<p class="note-body">Dark mode isn't just about saving battery life. It's about reducing eye strain in low-light environments and creating a sense of depth. When designing for dark interfaces, remember that pure black (#000000) can cause smearing on OLED screens. Aim for dark grays like #121212.</p>
						</div>

						<div class="card-footer">
							<div class="timestamp">
								<i class="far fa-clock"></i> 2 hours ago
							</div>
							<div class="interaction-stats">
								<div class="stat-item" onclick="toggleLike(this)">
									<i class="far fa-heart"></i> <span>245</span>
								</div>
								<div class="stat-item">
									<i class="far fa-comment"></i> <span>12</span>
								</div>
								<div class="stat-item">
									<i class="fas fa-share-nodes"></i>
								</div>
							</div>
						</div>
					</div>
				`;
				*/

				b_text = d.body.length > 100 ? d.body.substr(0,100) + '...' : d.body;

				let mystate = privacy_state_atlas[d.privacy_state];

				t_ht = `
					<div class="social-card slide-up" style="${mekstagger(100,n)}" onclick="note_click(event,this)" data-myid="${n}">
						<div class="card-actions w3-display-topright w3-hide_">
							<button class="mybtn sm outline w3-hide" onclick="edit_note(${n})">follow</button>
							<div class="dropdown w3-hide_">
								<div class="mybtn sm outline flowline gap-tn centerline" data-role="user-card" data-toggle="dropdown">
									<i class="fa fa-elipsis-v fa-chevron-down"></i>
								</div>
								<div class="dropdown-menu dropdown-menu-right border panelbg2 modetxt themeround slide-down nopadding custom_drop sm mt-3" style="min-width: 180px;z-index:2">
									<div class="options">
										<a class="dropdown-item" onclick="edit_note(${n})">Edit note</a>
										<a class="dropdown-item" onclick="set_privacy(${n})">Change privacy</a>
										<a class="dropdown-item w3-hide" data-subrole="view_profile">View Profile</a>
										<a class="dropdown-item w3-hide" data-subrole="follow_user">Follow</a>
									</div>
								</div>
							</div>
						</div>

						<div class="my-card-header border-bottom pb-2">
							<b class="privacy_state_btn t_${d.privacy_state}">${mekIcon(iconAtlas[mystate])} ${mystate}</b>
						</div>

						<div class="note-content-area">
							<span class="note-category w3-hide">${d.title}</span>
							<h3 class="note-heading">${d.title}</h3>
							<p class="note-body">${b_text}</p>
						</div>
					</div>
				`;

				outht += t_ht;
			});

			display_ui.innerHTML = outht;
		}

		let t = classes.get('DataDis');
		ui_load_instance = new t(d_config);
	}

	window['note_click'] = (e,el) => {
		console.log(e);
		window['last_note_click_event'] = e;

		if(last_note_click_event.target.closest('.card-actions') != undefined){
			return;
		}

		alert_info('showing note');
		openNoteModal(last_fetched_notes[Number(el.dataset.myid)]);
	}
</script>
