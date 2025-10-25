<!-- DX Waterfall Component - START -->
<?php if ($this->session->userdata('user_dxwaterfall_enable') == 'Y' && isset($manual_mode) && $manual_mode == 0) { ?>
	<!-- DX Waterfall Component - JS -->
	<script src="<?php echo base_url() ;?>assets/js/dxwaterfall.js"></script>
	<script language="javascript">
		/*
		DX Waterfall Language
		*/

		// Detect platform for keyboard shortcuts (Cmd on Mac, Ctrl on Windows/Linux)
		var isMac = navigator.userAgent.toUpperCase().indexOf('MAC') >= 0;
		var modKey = isMac ? 'Cmd' : 'Ctrl';

		var lang_dxwaterfall_tune_to_spot = "<?= __("Tune to spot frequency"); ?>" + " [" + modKey + "+Shift+Space]";
		var lang_dxwaterfall_cycle_nearby_spots = "<?= __("Cycle to nearby spots"); ?>";
		var lang_dxwaterfall_spots = "<?= __("spots"); ?>";
		var lang_dxwaterfall_new_continent = "<?= __("New Continent"); ?>";
		var lang_dxwaterfall_new_dxcc = "<?= __("New DXCC"); ?>";
		var lang_dxwaterfall_new_callsign = "<?= __("New Callsign"); ?>";
		var lang_dxwaterfall_previous_spot = "<?= __("Previous spot"); ?>" + " [" + modKey + "+Left] | <?= __("First spot"); ?> [" + modKey + "+Down]";
		var lang_dxwaterfall_no_spots_lower = "<?= __("No spots at lower frequency"); ?>";
		var lang_dxwaterfall_next_spot = "<?= __("Next spot"); ?>" + " [" + modKey + "+Right] | <?= __("Last spot"); ?> [" + modKey + "+Up]";
		var lang_dxwaterfall_no_spots_higher = "<?= __("No spots at higher frequency"); ?>";
		var lang_dxwaterfall_no_spots_available = "<?= __("No spots available"); ?>";
		var lang_dxwaterfall_cycle_unworked = "<?= __("Cycle through unworked continents/DXCC"); ?>";
		var lang_dxwaterfall_dx_hunter = "<?= __("DX Hunter"); ?>";
		var lang_dxwaterfall_no_unworked = "<?= __("No unworked continents/DXCC on this band"); ?>";
		var lang_dxwaterfall_fetching_spots = "<?= __("Fetching spots..."); ?>";
		var lang_dxwaterfall_click_to_cycle = "<?= __("Click to cycle or wait 1.5s to apply"); ?>";
		var lang_dxwaterfall_change_continent = "<?= __("Change spotter continent"); ?>";
		var lang_dxwaterfall_filter_by_mode = "<?= __("Filter by mode"); ?>";
		var lang_dxwaterfall_toggle_phone = "<?= __("Toggle Phone mode filter"); ?>";
		var lang_dxwaterfall_phone = "<?= __("Phone"); ?>";
		var lang_dxwaterfall_toggle_cw = "<?= __("Toggle CW mode filter"); ?>";
		var lang_dxwaterfall_cw = "<?= __("CW"); ?>";
		var lang_dxwaterfall_toggle_digi = "<?= __("Toggle Digital mode filter"); ?>";
		var lang_dxwaterfall_digi = "<?= __("Digi"); ?>";
		var lang_dxwaterfall_zoom_out = "<?= __("Zoom out"); ?>" + " [" + modKey + "+-]";
		var lang_dxwaterfall_reset_zoom = "<?= __("Reset zoom to default (3)"); ?>";
		var lang_dxwaterfall_zoom_in = "<?= __("Zoom in"); ?>" + " [" + modKey + "++]";
		var lang_dxwaterfall_waiting_data = "<?= __("Waiting for DX Cluster data..."); ?>";
		var lang_dxwaterfall_comment = "<?= __("Comment: "); ?>";
		var lang_dxwaterfall_modes_label = "<?= __("modes:"); ?>";
		var lang_dxwaterfall_out_of_bandplan = "<?= __("OUT OF BANDPLAN"); ?>";
		var lang_dxwaterfall_changing_frequency = "<?= __("Changing radio frequency..."); ?>";
		var lang_dxwaterfall_invalid = "<?= __("INVALID"); ?>";
		var lang_dxwaterfall_turn_on = "<?= __("Click to turn on the DX Waterfall"); ?>";
		var lang_dxwaterfall_turn_off = "<?= __("Turn off DX Waterfall"); ?>";
		var lang_dxwaterfall_warming_up = "<?= __("Warming up..."); ?>";
		var lang_dxwaterfall_label_size_cycle = "<?= __("Cycle label size"); ?>";
		var lang_dxwaterfall_label_size_xsmall = "<?= __("X-Small"); ?>";
		var lang_dxwaterfall_label_size_small = "<?= __("Small"); ?>";
		var lang_dxwaterfall_label_size_medium = "<?= __("Medium"); ?>";
		var lang_dxwaterfall_label_size_large = "<?= __("Large"); ?>";
		var lang_dxwaterfall_label_size_xlarge = "<?= __("X-Large"); ?>";
		var lang_dxwaterfall_spotted_by = "<?= __("by:"); ?>";

		// DX Waterfall Configuration from User Options
		let dxwaterfall_decont = '<?php echo $this->optionslib->get_option('dxcluster_decont'); ?>';
		let dxwaterfall_maxage = '<?php echo $this->optionslib->get_option('dxcluster_maxage'); ?>';

		// Helper function to safely check if optional field exists
		window.DX_WATERFALL_HAS_FIELD = function(fieldName) {
			var fieldId = window.DX_WATERFALL_FIELD_MAP.optional[fieldName];
			return fieldId && document.getElementById(fieldId) !== null;
		};
	</script>

	<!-- DX Waterfall Component - CSS -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/dxwaterfall.css">

	<!-- DX Waterfall Component - HTML -->
	<div class="row dxwaterfallpane">
	<div class="col-sm-12">
		<div id="dxWaterfallSpot">
		<div id="dxWaterfallSpotHeader">
			<div id="dxWaterfallSpotLeft">
			<span id="dxWaterfallMessage"></span>
			</div>
			<i id="dxWaterfallPowerOnIcon" class="fas fa-power-off"></i>
		</div>
		<div id="dxWaterfallSpotContent"></div>
		<i id="dxWaterfallPowerOffIcon" class="fas fa-power-off"></i>
		</div>
	</div>
	<div class="col-sm-12" id="dxWaterfallCanvasContainer" style="display: none;">
		<canvas id="dxWaterfall"></canvas>
	</div>
	<div class="col-sm-12" id="dxWaterfallMenuContainer" style="display: none;">
		<div id="dxWaterfallMenu">&nbsp;</div>
	</div>
	</div>

<?php } ?>
<!-- DX Waterfall Component - END -->
