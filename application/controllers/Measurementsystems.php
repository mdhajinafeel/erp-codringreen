<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Measurementsystems extends MY_Controller
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
		$data['title'] = $this->lang->line('measurement_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = 'cgr_masters';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/measurementsystems", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function measurementsystem_list()
	{
		$data['title'] = $this->lang->line('measurement_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if($originid == 0) {
			$measurementsystems = $this->Master_model->all_measurementsystems();
		} else {
			$measurementsystems = $this->Master_model->all_measurementsystems_origin($originid);
		}
		$data = array();

		foreach ($measurementsystems as $r) {

			$editMeasurementSystem = '<span data-toggle="tooltip" data-placement="top" title="'.$this->lang->line('edit').'"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editmeasurement" data-measurement_id="' . $r->measurement_id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editMeasurementSystem,
				$r->measurement_name,
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

	public function dialog_measurementsystem_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addmeasurementsystem") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_measurementsystem'),
					'pagetype' => "add",
					'measurementsystemid' => 0,
				);
				$this->load->view('masters/dialog_add_measurementsystem', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editmeasurement") {

			if (!empty($session)) {

				$getMeasurmentSystemDetails = $this->Master_model->get_measurementsystem_detail_by_id($this->input->get('mid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_measurementsystem'),
					'pagetype' => "edit",
					'measurementsystemid' => $getMeasurmentSystemDetails[0]->measurement_id,
					'get_measurementsystem_details' => $getMeasurmentSystemDetails,
				);
				$this->load->view('masters/dialog_add_measurementsystem', $data);
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

		if ($this->input->post('add_type') == 'measurementsystem') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$measurement_name = $this->input->post('measurement_name');
					$measurement_originid = $this->input->post('measurement_originid');
					$status = $this->input->post('status');
					
					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataMeasurementSystem = array(
						"measurement_name" => $measurement_name,
						"createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'isactive' => $status,
						'origin_id' => $measurement_originid,
					);

					$insertMeasurement = $this->Master_model->add_measurementsystem($dataMeasurementSystem);

					if ($insertMeasurement > 0) {
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

					$measurement_id = $this->input->post('measurement_id');
					$measurement_name = $this->input->post('measurement_name');
					$measurement_originid = $this->input->post('measurement_originid');
					$status = $this->input->post('status');

					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataMeasurementSystem = array(
						"measurement_name" => $measurement_name,
						"updatedby" => $session['user_id'], 'isactive' => $status,
						'origin_id' => $measurement_originid,
					);

					$updateMeasurement = $this->Master_model->update_measurementsystem($dataMeasurementSystem, $measurement_id);

					if ($updateMeasurement == true) {
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
}
