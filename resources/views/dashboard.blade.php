<x-layouts.mainlayout>
	<!-- helpers -->
	<div class="w3-bottom spacy-md flowline gap-sm left" style="z-index: 3;width: auto;">
		<button class="circle_btn" data-myclass="circle_btn" data-runme="toggle_ui_mode" id="mode_indicator"><i class="fa fa-magic"></i></button>
	</div>

	<!-- vetted elements -->
        <x-uikit.topnav_profile/>

		<?php
			// require_once __DIR__.'/pieces/piece_smallhero.php';
		?>

        <x-uikit.smallhero/>

		<div>
			<div class="ui_cage">
				<div id="dynamic_ui"></div>
			</div>
			<div id="dynamic_js"></div>
			<div id="loader_ui" class="flowline centroid pageloader_waiter">
				<div class="flowline centroid">
					loading, please wait
					<div class="loader_2"></div>
				</div>
			</div>
		</div>

		<div class="thecontrols flow gap-tn right spacy-sm slide-l" data-role="applet_controls" data-visibledata="1,0,0">
			<!-- <button class="btn circle_btn lg" data-myclass="btn circle_btn" data-runme="toggle_ui_mode" data-role="mode_indicator"><i class="fa fa-plus"></i></button> -->
			<button class="btn circle_btn lg themegrad" data-runme="add_timer"><i class="fa fa-plus"></i></button>
			<!-- <button class="btn circle_btn" data-runme="clear_timers"><i class="fa fa-trash"></i></button> -->
		</div>

	<?php
		// require_once __DIR__.'/pieces/_commons.php';
		// require_once __DIR__.'/pieces/_dashcodes.php';
	?>

    @include('pieces.commons')
    @include('pieces.dashcodes')

	<script>
		callOnLoad.push({act: setup_uimode});
		callOnLoad.push({act: init_stuff});
	</script>
</x-layouts.mainlayout>
