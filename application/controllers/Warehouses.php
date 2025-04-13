<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouses extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
	}

	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function index()
	{
		$data['title'] = $this->lang->line('warehouse_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		$role_resources_ids = explode(',', $session["role_id"]);

		if (empty($session)) {
			redirect("/logout");
		}
		
		if (in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids)) {
			redirect("/errorpage");
		} else {
			$data['path_url'] = 'cgr_masters';
			if (!empty($session)) {
				$data['subview'] = $this->load->view("masters/warehouses", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function warehouse_list()
	{
		$data['title'] = $this->lang->line('warehouse_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$origin_id = intval($this->input->get("originid"));

		if ($origin_id == 0) {
			$warehouses = $this->Master_model->all_warehouses();
		} else {
			$warehouses = $this->Master_model->all_warehouses_originid($origin_id);
		}
		$data = array();

		foreach ($warehouses as $r) {

			$editWarehouse = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editwarehouse" data-warehouse_id="' . $r->whid . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editWarehouse,
				$r->warehouse_name,
				$r->warehouse_code,
				$r->warehouse_ownername,
				$r->pol_name,
				$r->origin,
				$status
			);
		}

		$output = array(
			"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dialog_warehouse_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addwarehouse") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_warehouse'),
					'pagetype' => "add",
					'warehouseid' => 0,
				);
				$this->load->view('masters/dialog_add_warehouse', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editwarehouse") {

			if (!empty($session)) {

				$getWarehouseDetails = $this->Master_model->get_warehouse_detail_by_id($this->input->get('wid'));
				$getExportPol = $this->Master_model->get_export_pol($getWarehouseDetails[0]->origin_id);

				$data = array(
					'pageheading' => $this->lang->line('edit_warehouse'),
					'pagetype' => "edit",
					'warehouseid' => $getWarehouseDetails[0]->whid,
					"export_pol" => $getExportPol,
					'get_warehouse_details' => $getWarehouseDetails,
				);
				$this->load->view('masters/dialog_add_warehouse', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function add()
	{
		$Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');

		if ($this->input->post('add_type') == 'warehouses') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('wh_name');
					$ownersname = $this->input->post('wh_owners_name');
					$address = $this->input->post('wh_address');
					$status = $this->input->post('status');
					$whorigin = $this->input->post('whorigin');
					$port_of_loading = $this->input->post('port_of_loading');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$abbreviate = $this->abbreviate($name);
					$current_year = date("Y");
					$companyname = 'CGR';
					$generatealpha = $this->random_strings(10);
					$warehouse_code = $abbreviate . '/' . $companyname . '/' . $current_year . '/' . $generatealpha;

					$dataWH = array(
						"warehouse_name" => $name, "warehouse_ownername" => $ownersname,
						"warehouse_address" => $address, "warehouse_code" => $warehouse_code,
						"pol" => $port_of_loading, "createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $whorigin,
					);

					$insertWarehouse = $this->Master_model->add_warehouse($dataWH);

					if ($insertWarehouse > 0) {
						$Return['result'] = $this->lang->line('data_added');
						$this->output($Return);
						exit;
					} else {
						$Return['error'] = $this->lang->line('error_adding');
						$this->output($Return);
						exit;
					}
				} else if ($this->input->post('action_type') == 'edit') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$warehouse_id = $this->input->post('warehouse_id');
					$name = $this->input->post('wh_name');
					$ownersname = $this->input->post('wh_owners_name');
					$address = $this->input->post('wh_address');
					$status = $this->input->post('status');
					$whorigin = $this->input->post('whorigin');
					$port_of_loading = $this->input->post('port_of_loading');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataWH = array(
						"warehouse_name" => $name, "warehouse_ownername" => $ownersname,
						"warehouse_address" => $address, "pol" => $port_of_loading,
						"updatedby" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $whorigin,
					);

					$updateWarehouse = $this->Master_model->update_warehouse($dataWH, $warehouse_id);

					if ($updateWarehouse == true) {
						$Return['result'] = $this->lang->line('data_updated');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
						exit;
					} else {
						$Return['error'] = $this->lang->line('error_updating');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
						exit;
					}
				}
			} else {
				$Return['error'] = "";
				$Return['result'] = "";
				$Return['redirect'] = true;
				$Return['csrf_hash'] = $this->security->get_csrf_hash();
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = $this->lang->line('invalid_request');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}

	public function get_export_pol_by_origin()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (!empty($session)) {
			$result = "<option value='0'>" . $this->lang->line('select') . "</option>";
			if ($this->input->get('originid') > 0) {
				$getExportPol = $this->Master_model->get_export_pol($this->input->get('originid'));
				foreach ($getExportPol as $exportpol) {
					$result = $result . "<option value='" . $exportpol->id . "'>" . $exportpol->pol_name . "</option>";
				}
			}

			$Return['result'] = $result;
			$Return['redirect'] = false;
			$this->output($Return);
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	function abbreviate($strString, $intLength = NULL)
	{
		$defaultAbbrevLength = 3;

		$strString = preg_replace("/[^A-Za-z0-9]/", '', $strString);
		$strString = ucfirst($strString);
		$stringIndex = 0;
		$uppercaseCount   = preg_match_all('/[A-Z]/', $strString, $uppercaseLetters, PREG_OFFSET_CAPTURE);
		$targetLength     = isset($intLength) ? intval($intLength) : $defaultAbbrevLength;
		$uppercaseCount   = $uppercaseCount > $targetLength ? $targetLength : $uppercaseCount;
		$targetWordLength = round($targetLength / intval($uppercaseCount));
		$abbrevLength     = 0;
		$abbreviation     = '';
		for ($i = 0; $i < $uppercaseCount; $i++) {
			$ucLetters[] = $uppercaseLetters[0][$i][0];
		}
		$characterDeficit = 0;
		$wordIndex = $targetWordLength;
		while ($stringIndex < strlen($strString)) {
			if ($abbrevLength >= $targetLength)
				break;
			$currentChar = $strString[$stringIndex++];
			if (in_array($currentChar, $ucLetters)) {
				$characterDeficit += $targetWordLength - $wordIndex;
				$wordIndex = 0;
			} else if ($wordIndex >= $targetWordLength) {
				if ($characterDeficit == 0)
					continue;
				else
					$characterDeficit--;
			}
			$abbreviation .= $currentChar;
			$abbrevLength++;
			$wordIndex++;
		}
		return strtoupper($abbreviation);
	}

	function random_strings($length_of_string)
	{
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(
			str_shuffle($str_result),
			0,
			$length_of_string
		);
	}
}
