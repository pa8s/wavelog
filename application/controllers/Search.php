<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {


	function __construct() {
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		if($this->optionslib->get_option('global_search') != "true") {
			$this->load->model('user_model');
			if(!$this->user_model->authorize(2)) { $this->session->set_flashdata('error', __("You're not allowed to do that!")); redirect('dashboard'); }
		}
	}

	public function index() {
		$data['page_title'] = __("Search");

		$this->load->view('interface_assets/header', $data);
		$this->load->view('search/main');
		$this->load->view('interface_assets/footer');
	}

	// Filter is for advanced searching and filtering of the logbook
	public function filter() {
		$data['page_title'] = __("Search & Filter Logbook");

		$this->load->library('form_validation');

		$this->load->model('Search_filter');

		$data['get_table_names'] = $this->Search_filter->get_table_columns();
		$data['stored_queries'] = $this->Search_filter->get_stored_queries();

		//print_r($this->Search_filter->get_table_columns());

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('interface_assets/header', $data);
			$this->load->view('search/filter');
			$this->load->view('interface_assets/footer');
		} else {
			$this->load->view('interface_assets/header', $data);
			$this->load->view('search/filter');
			$this->load->view('interface_assets/footer');
		}
	}

	// Searches for incorrect CQ Zones
	public function incorrect_cq_zones() {
		$this->load->model('stations');

		$data['station_profile'] = $this->stations->all_of_user();
		$data['page_title'] = __("Incorrectly logged CQ zones");

		$this->load->view('interface_assets/header', $data);
		$this->load->view('search/cqzones');
		$this->load->view('interface_assets/footer');
	}

	// Searches for incorrect ITU Zones
	public function incorrect_itu_zones() {
		$this->load->model('stations');

		$data['station_profile'] = $this->stations->all_of_user();
		$data['page_title'] = __("Incorrectly logged ITU zones");

		$this->load->view('interface_assets/header', $data);
		$this->load->view('search/ituzones');
		$this->load->view('interface_assets/footer');
	}

	// Searches for unconfirmed Lotw QSOs where QSO partner has uploaded to LoTW after the QSO date
	public function lotw_unconfirmed() {
		$this->load->model('stations');

		$data['station_profile'] = $this->stations->all_of_user();
		$data['page_title'] = __("QSOs unconfirmed on LoTW, but the callsign has uploaded to LoTW after QSO date");

		$this->load->view('interface_assets/header', $data);
		$this->load->view('search/lotw_unconfirmed');
		$this->load->view('interface_assets/footer');
	}

	function json_result() {
		$result = $this->fetchQueryResult(($this->input->post('search', TRUE) ?? ''), FALSE);
		echo json_encode($result->result_array());
	}

	function get_stored_queries() {
		$this->load->model('Search_filter');
		$data['result'] = $this->Search_filter->get_stored_queries();
		$this->load->view('search/stored_queries', $data);
	}

	function search_result() {
		$sstring = str_replace('Ø', "0", $this->input->post("search", TRUE) ?? '');
		$data['results'] = $this->fetchQueryResult($sstring, FALSE);
		$this->load->view('search/search_result_ajax', $data);
	}

	function export_to_adif() {
		$sstring = str_replace('Ø', "0", $this->input->post("search", TRUE) ?? '');
		$data['qsos'] = $this->fetchQueryResult($sstring, FALSE);
		$this->load->view('adif/data/exportall', $data);
	}

	function export_stored_query_to_adif() {
		$this->db->where('id', xss_clean($this->input->post('id')));
		$sql = $this->db->get('queries')->result();

		$data['qsos'] = $this->db->query($sql[0]->query);
		$this->load->view('adif/data/exportall', $data);
	}

	function run_query() {
		$this->db->where('id', xss_clean($this->input->post('id')));
		$sql = $this->db->get('queries')->result();
		$sql = $sql[0]->query;

		if (stristr($sql, 'select') && !stristr($sql, 'delete') && !stristr($sql, 'update')) {
			if (!(strpos(strtolower($sql),'limit'))) {
				$sql.=' limit 5000';
			}
			$data['results'] = $this->db->query($sql);

			$this->load->view('search/search_result_ajax', $data);
		}
	}

	function save_query() {
		$search_param = $this->input->post('search', TRUE);
		$description = $this->input->post('description', TRUE);

		$query = $this->fetchQueryResult($search_param, TRUE);

		$data = array(
			'userid' => xss_clean($this->session->userdata('user_id')),
			'query' => $query,
			'description' => $description
		);

		$this->db->insert('queries', $data);
		$last_id = $this->db->insert_id();
		header('Content-Type: application/json');
		echo json_encode(array('id' => $last_id, 'description' => $description));
	}

	function delete_query() {
		$id = xss_clean($this->input->post('id'));
		$this->load->model('search_filter');
		$this->search_filter->delete_query($id);
	}

	function save_edited_query() {
		$data = array(
			'description' => xss_clean($this->input->post('description')),
		);

		$this->db->where('id', xss_clean($this->input->post('id')));
		$this->db->where('userid', $this->session->userdata['user_id']);
		$this->db->update('queries', $data);
	}

	function buildWhere(array $object, string $condition = null): void {
		/*
		 * The $object is one of the following:
		 * - a group, with 'condition' and 'rules' keys
		 * - a condition, that is either 'AND' or 'OR' depending on the parent group setting
		 */
		$objectIsGroup = isset($object['condition']);
		if ($objectIsGroup) {
			if ($condition === null || $condition === 'AND') {
				$this->db->group_start();
			} else {
				$this->db->or_group_start();
			}
			foreach ($object['rules'] as $rule) {
				/*
				 * Now iterate over the children, that are either groups or conditions
				 */
				$this->buildWhere($rule, $object['condition']);
			}
			$this->db->group_end();
		} else {
			$object['field'] = $this->config->item('table_name') . '.' . $object['field'];

			if ($object['operator'] == "equal") {
				if ($condition == "AND") {
					$this->db->where($object['field'], $object['value']);
				} else {
					$this->db->or_where($object['field'], $object['value']);
				}
			}

			if ($object['operator'] == "not_equal") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' !=', $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' !=', $object['value']);
				}
			}

			if ($object['operator'] == "begins_with") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' like ', $object['value'] . "%");
				} else {
					$this->db->or_where($object['field'] . ' like ', $object['value'] . "%");
				}
			}

			if ($object['operator'] == "contains") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' like ', "%" . $object['value'] . "%");
				} else {
					$this->db->or_where($object['field'] . ' like ', "%" . $object['value'] . "%");
				}
			}

			if ($object['operator'] == "ends_with") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' like ', "%" . $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' like ', "%" . $object['value']);
				}
			}

			if ($object['operator'] == "is_empty") {
				if ($condition == "AND") {
					$this->db->where($object['field'], '');
				} else {
					$this->db->or_where($object['field'], '');
				}
			}

			if ($object['operator'] == "is_not_empty") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' !=', '');
				} else {
					$this->db->or_where($object['field'] . ' !=', '');
				}
			}

			if ($object['operator'] == "is_null") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' IS NULL');
				} else {
					$this->db->or_where($object['field'] . ' IS NULL');
				}
			}

			if ($object['operator'] == "is_not_null") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' IS NOT NULL');
				} else {
					$this->db->or_where($object['field'] . ' IS NOT NULL');
				}
			}


			if ($object['operator'] == "less") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' <', $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' <', $object['value']);
				}
			}

			if ($object['operator'] == "less_or_equal") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' <=', $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' <=', $object['value']);
				}
			}

			if ($object['operator'] == "greater") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' >', $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' >', $object['value']);
				}
			}

			if ($object['operator'] == "greater_or_equal") {
				if ($condition == "AND") {
					$this->db->where($object['field'] . ' >=', $object['value']);
				} else {
					$this->db->or_where($object['field'] . ' >=', $object['value']);
				}
			}
		}
	}

	function fetchQueryResult($json, $returnquery) {
		$search_items = json_decode($json, true);

		$this->db->select($this->config->item('table_name').'.*, station_profile.station_profile_name, station_profile.station_gridsquare, station_profile.station_city, station_profile.station_iota, station_profile.station_callsign, station_profile.station_sota, station_profile.station_wwff, station_profile.station_dxcc, station_profile.station_pota, station_profile.station_cq, station_profile.station_itu, station_profile.station_sig, station_profile.station_sig_info, station_profile.station_cnty, station_profile.county, station_profile.state, dxcc_entities.name as station_country');

		$this->db->group_start();
		$this->buildWhere($search_items);
		$this->db->group_end();

		$this->db->order_by('COL_TIME_ON', 'DESC');
		$this->db->join('station_profile', 'station_profile.station_id = '.$this->config->item('table_name').'.station_id');
		$this->db->join('dxcc_entities', 'station_profile.station_dxcc = dxcc_entities.adif', 'left');
		$this->db->where('station_profile.user_id', $this->session->userdata('user_id'));
		$this->db->limit(5000);

		if ($returnquery) {
			$query = $this->db->get_compiled_select($this->config->item('table_name'));
		} else {
			$query = $this->db->get($this->config->item('table_name'));
		}
		return $query;
	}
}
