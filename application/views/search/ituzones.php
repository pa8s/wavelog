<div class="container search">

	<h1>
		<?= __("Search"); ?>
		<small class="text-muted"><?= __("Ready to find a QSO?"); ?></small>
	</h1>

	<div class="card text-center">
	  <div class="card-header">
	    <ul class="nav nav-tabs card-header-tabs">
	      <li class="nav-item">
	        <a class="nav-link" href="<?php echo site_url('search'); ?>"><?= __("Search"); ?></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="<?php echo site_url('search/filter'); ?>"><?= __("Advanced Search"); ?></a>
	      </li>
		  <li class="nav-item">
	        <a class="nav-link" href="<?php echo site_url('search/incorrect_cq_zones'); ?>"><?= __("Incorrect CQ Zones"); ?></a>
	      </li>
		  <li class="nav-item">
	        <a class="nav-link active" href="<?php echo site_url('search/incorrect_itu_zones'); ?>"><?= __("Incorrect ITU Zones"); ?></a>
	      </li>
		  <li class="nav-item">
	        <a class="nav-link" href="<?php echo site_url('search/lotw_unconfirmed'); ?>"><?= __("QSOs unconfirmed on LoTW"); ?></a>
	      </li>
	    </ul>
	  </div>
	  <div class="card-body">
	  	<form method="post" action="" id="search_box" name="test">
		  <div class="mb-3 row">
		    <label for="callsign" class="w-auto col-form-label"><?= __("Station location:"); ?></label>
		    <select id="station_id" name="station_profile" class="form-select form-select-sm col-sm-3 mb-3 w-auto">
					<option value="All"><?= __("All"); ?></option>
                    <?php foreach ($station_profile->result() as $station) { ?>
                    <option value="<?php echo $station->station_id; ?>"><?= __("Callsign"); ?>: <?php echo $station->station_callsign; ?> (<?php echo $station->station_profile_name; ?>)</option>
                    <?php } ?>
                    </select>
		    <div class="col-sm-4">
		    	<button onclick="findincorrectituzones();" class="btn btn-sm btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i> <?= __("Search"); ?></button>
				<button hidden="true" type="button" class="btn btn-sm btn-outline-primary me-1" id="btn-lba" onclick="open_in_lba();"><?= __("Open in the Advanced Logbook"); ?></button>
		    </div>
		  </div>
		</form>

		<div id="partial_view"></div>

	  </div>
	</div>

</div>
