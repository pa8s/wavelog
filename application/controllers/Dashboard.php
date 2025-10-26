<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
		// Check if users logged in
		$this->load->model('user_model');
		if ($this->user_model->validate_session() == 0) {
			// user is not logged in
			redirect('user/login');
		}

		// Database connections
		$this->load->model('logbook_model');

		// LoTW infos
		$this->load->model('Lotw_model');
		$current_date = date('Y-m-d H:i:s');
		$data['lotw_cert_expired'] = $this->Lotw_model->lotw_cert_expired($this->session->userdata('user_id'), $current_date);
		$data['lotw_cert_expiring'] = $this->Lotw_model->lotw_cert_expiring($this->session->userdata('user_id'), $current_date);


		$this->load->model('logbooks_model');
		$logbooks_locations_array = $this->logbooks_model->list_logbook_relationships($this->session->userdata('active_station_logbook'));


		if (($logbooks_locations_array[0]>-1) && (!(in_array($this->stations->find_active(),$logbooks_locations_array)))) {
			$data['active_not_linked']=true;
		} else {
			$data['active_not_linked']=false;
		}

		if ($logbooks_locations_array[0] == -1) {
			$data['linkedCount']=0;
		} else {
			$data['linkedCount']=sizeof($logbooks_locations_array);
		}
		// Calculate Lat/Lng from Locator to use on Maps
		if ($this->session->userdata('user_locator')) {
			if(!$this->load->is_loaded('Qra')) {
			    $this->load->library('Qra');
		    }

			$qra_position = $this->qra->qra2latlong($this->session->userdata('user_locator'));
			if ($qra_position) {
				$data['qra'] = "set";
				$data['qra_lat'] = $qra_position[0];
				$data['qra_lng'] = $qra_position[1];
			} else {
				$data['qra'] = "none";
			}
		} else {
			$data['qra'] = "none";
		}

		// We need the form_helper for the layout/messages
		$this->load->helper('form');

		$this->load->model('stations');
		$this->load->model('setup_model');

		$data['countryCount'] = $this->setup_model->getCountryCount();
		$data['logbookCount'] = $this->setup_model->getLogbookCount();
		$data['locationCount'] = $this->setup_model->getLocationCount();

		$data['current_active'] = $this->stations->find_active();

		$data['themesWithoutMode'] = $this->setup_model->checkThemesWithoutMode();
		if (($this->session->userdata('user_dashboard_map') ?? '') != '') {
			$data['dashboard_map'] = $this->session->userdata('user_dashboard_map') ?? 'Y';
		} else {
			$data['dashboard_map'] = 'N';
		}

		if (($this->session->userdata('user_dashboard_banner') ?? '') != '') {
			$data['dashboard_banner'] = $this->session->userdata('user_dashboard_banner') ?? 'Y';
		} else {
			$data['dashboard_banner'] = 'N';
		}

		// Check user preferrence to show Solar Data on Dashboard
		// Default to not show
		if (($this->session->userdata('user_dashboard_solar') ?? '') != '') {
			$data['dashboard_solar'] = $this->session->userdata('user_dashboard_solar') ?? 'N';
		} else {
			$data['dashboard_solar'] = 'N'; // Default to not show
		}

		$data['user_map_custom'] = $this->optionslib->get_map_custom();

		$this->load->model('cat');
		$this->load->model('vucc');
		$this->load->model('dayswithqso_model');

		$data['radio_status'] = $this->cat->recent_status();

		// Store info
		$data['todays_qsos'] = $this->logbook_model->todays_qsos($logbooks_locations_array);
		$data['total_qsos'] = $this->logbook_model->total_qsos($logbooks_locations_array);
		$data['month_qsos'] = $this->logbook_model->month_qsos($logbooks_locations_array);
		$data['year_qsos'] = $this->logbook_model->year_qsos($logbooks_locations_array);

		$rawstreak=$this->dayswithqso_model->getAlmostCurrentStreak();
		if (is_array($rawstreak)) {
			$data['current_streak']=$rawstreak['highstreak'];
		} else {
			$data['current_streak']=0;
		}

		// Load  Countries Breakdown data into array
		$CountriesBreakdown = $this->logbook_model->total_countries_confirmed($logbooks_locations_array);

		$data['total_countries'] = $CountriesBreakdown['Countries_Worked'];
		$data['total_countries_confirmed_paper'] = $CountriesBreakdown['Countries_Worked_QSL'];
		$data['total_countries_confirmed_eqsl'] = $CountriesBreakdown['Countries_Worked_EQSL'];
		$data['total_countries_confirmed_lotw'] = $CountriesBreakdown['Countries_Worked_LOTW'];

		$QSLStatsBreakdownArray = $this->logbook_model->get_QSLStats($logbooks_locations_array);

		$data['total_qsl_sent'] = $QSLStatsBreakdownArray['QSL_Sent'];
		$data['total_qsl_rcvd'] = $QSLStatsBreakdownArray['QSL_Received'];
		$data['total_qsl_requested'] = $QSLStatsBreakdownArray['QSL_Requested'];
		$data['qsl_sent_today'] = $QSLStatsBreakdownArray['QSL_Sent_today'];
		$data['qsl_rcvd_today'] = $QSLStatsBreakdownArray['QSL_Received_today'];
		$data['qsl_requested_today'] = $QSLStatsBreakdownArray['QSL_Requested_today'];

		$data['total_eqsl_sent'] = $QSLStatsBreakdownArray['eQSL_Sent'];
		$data['total_eqsl_rcvd'] = $QSLStatsBreakdownArray['eQSL_Received'];
		$data['eqsl_sent_today'] = $QSLStatsBreakdownArray['eQSL_Sent_today'];
		$data['eqsl_rcvd_today'] = $QSLStatsBreakdownArray['eQSL_Received_today'];

		$data['total_lotw_sent'] = $QSLStatsBreakdownArray['LoTW_Sent'];
		$data['total_lotw_rcvd'] = $QSLStatsBreakdownArray['LoTW_Received'];
		$data['lotw_sent_today'] = $QSLStatsBreakdownArray['LoTW_Sent_today'];
		$data['lotw_rcvd_today'] = $QSLStatsBreakdownArray['LoTW_Received_today'];

		$data['total_qrz_sent'] = $QSLStatsBreakdownArray['QRZ_Sent'];
		$data['total_qrz_rcvd'] = $QSLStatsBreakdownArray['QRZ_Received'];
		$data['qrz_sent_today'] = $QSLStatsBreakdownArray['QRZ_Sent_today'];
		$data['qrz_rcvd_today'] = $QSLStatsBreakdownArray['QRZ_Received_today'];

		$data['last_qso_count'] = empty($this->session->userdata('dashboard_last_qso_count')) ? DASHBOARD_DEFAULT_QSOS_COUNT : $this->session->userdata('dashboard_last_qso_count');
		$data['last_qsos_list'] = $this->logbook_model->get_last_qsos(
			$data['last_qso_count'],
			$logbooks_locations_array
		);

		$data['vucc'] = $this->vucc->fetchVuccSummary();
		$data['vuccSAT'] = $this->vucc->fetchVuccSummary('SAT');

		$data['page_title'] = __("Dashboard");

		$this->load->model('dxcc');
		$dxcc = $this->dxcc->list_current();

		$current = $this->logbook_model->total_countries_current($logbooks_locations_array);

		$footerData['scripts'] = [
			'assets/js/sections/dashboard.js?' . filemtime(realpath(__DIR__ . "/../../assets/js/sections/dashboard.js")),
		];

		// First Login Wizard
		$fl_wiz_value = $this->session->userdata('FirstLoginWizard') ?? null;
		$show_fl_wiz = false;

		// if the value is empty, we check if the user has any station locations
		if ($fl_wiz_value === null) {
			$this->load->model('stations');
			if ($this->stations->all_of_user()->num_rows() == 0) {
				$show_fl_wiz = true;
			} else {
				$this->user_options_model->set_option('FirstLoginWizard', 'shown', ['boolean' => 1]);
				$this->session->set_userdata('FirstLoginWizard', 1);
			}
		} elseif ($fl_wiz_value == 0) {
			$show_fl_wiz = true;
		}

		$data['is_first_login'] = $show_fl_wiz;
		$data['firstloginwizard'] = '';
		if ($this->session->userdata('impersonate') == 0 &&		// Don't show to impersonated user
			$this->session->userdata('clubstation') == 0 &&		// Don't show to Clubstation
			$data['is_first_login']) {							// Don't show if already done

			$this->load->model('dxcc');
			$viewdata['dxcc_list'] = $this->dxcc->list();

			$footerData['scripts'][] = 'assets/js/bootstrap-multiselect.js?' . filemtime(realpath(__DIR__ . "/../../assets/js/bootstrap-multiselect.js"));

			$this->load->library('form_validation');

			$data['firstloginwizard'] = $this->load->view('user/modals/first_login_wizard', $viewdata, true);
		}

		$data['total_countries_needed'] = count($dxcc->result()) - $current;

		// Check user preferrence to show Solar Data on Dashboard and load data if yes
		// Default to not show
		if($data['dashboard_solar'] == 'Y') {
			$this->load->model('Hamqsl_model');	// Load HAMQSL model

			if (!$this->Hamqsl_model->set_solardata()) {
				// Problem getting data, set to null
				$data['solar_bandconditions'] = null;
				$data['solar_solardata'] = null;
			} else {
				// Load data into arrays
				$data['solar_bandconditions'] = $this->Hamqsl_model->get_bandconditions_array();
				$data['solar_solardata'] = $this->Hamqsl_model->get_solarinformation_array();
			}
		}

		// Load the views
		$this->load->view('interface_assets/header', $data);
		$this->load->view('dashboard/index');
		$this->load->view('interface_assets/footer', $footerData);
	}

	function radio_display_component()
	{
		$this->load->model('cat');

		$data['radio_status'] = $this->cat->recent_status();
		$this->load->view('components/radio_display_table', $data);
	}
}
