<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Exportpol extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model('Origin_model');
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
		$data['title'] = $this->lang->line('export_pol') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		$role_resources_ids = explode(',', $session["role_id"]);

		if (empty($session)) {
			redirect("/logout");
		}

		if (in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids)) {
			redirect("/errorpage");
		} else {
			$data['path_url'] = 'cgr_exportpol';
			if (!empty($session)) {
				$data['subview'] = $this->load->view("masters/exportpol", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function pol_list()
	{
		$data['title'] = $this->lang->line('export_pol') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$exportspol = $this->Master_model->all_exportpol();
		} else {
			$exportspol = $this->Master_model->all_exportpol_by_origin($originid);
		}

		$data = array();

		foreach ($exportspol as $r) {

			$editExportPol = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editpol" data-pol_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editExportPol,
				$r->pol_name,
				$r->pol_short_name,
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

	public function dialog_exportpol_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addexportpol") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_pol'),
					'pagetype' => "add",
					'exportid' => 0,
				);
				$this->load->view('masters/dialog_add_exportpol', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editexportpol") {

			if (!empty($session)) {

				$getExportPOLDetails = $this->Master_model->get_exportpol_detail_by_id($this->input->get('pid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_pod'),
					'pagetype' => "edit",
					'exportid' => $getExportPOLDetails[0]->id,
					'get_export_details' => $getExportPOLDetails,
				);
				$this->load->view('masters/dialog_add_exportpol', $data);
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

		if ($this->input->post('add_type') == 'exportpol') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('name');
					$shortname = $this->input->post('shortname');
					$longitude = $this->input->post('longitude');
					$latitude = $this->input->post('latitude');
					$origin = $this->input->post('origin');
					$status = $this->input->post('status');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataExportPol = array(
						"pol_name" => $name, "pol_short_name" => $shortname,
						"latitude" => $latitude, "longitude" => $longitude, "origin_id" => $origin,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => $status,
					);

					$insertExportPol = $this->Master_model->add_exportpol($dataExportPol);

					if ($insertExportPol > 0) {
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

					$exportpolid = $this->input->post('exportpolid');
					$name = $this->input->post('name');
					$shortname = $this->input->post('shortname');
					$longitude = $this->input->post('longitude');
					$latitude = $this->input->post('latitude');
					$origin = $this->input->post('origin');
					$status = $this->input->post('status');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataExportPol = array(
						"pol_name" => $name, "pol_short_name" => $shortname,
						"latitude" => $latitude, "longitude" => $longitude, "origin_id" => $origin,
						"updated_by" => $session['user_id'], 'is_active' => $status,
					);

					$updateExportPol = $this->Master_model->update_exportpol($dataExportPol, $exportpolid);

					if ($updateExportPol == true) {
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
