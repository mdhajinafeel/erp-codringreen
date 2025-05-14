<?php


 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Exportpod extends MY_Controller
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
		$data['title'] = $this->lang->line('export_pod') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		$role_resources_ids = explode(',', $session["role_id"]);

		if (empty($session)) {
			redirect("/logout");
		}

		if (in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids)) {
			redirect("/errorpage");
		} else {
			$data['path_url'] = 'cgr_exportpod';
			if (!empty($session)) {
				$data['subview'] = $this->load->view("masters/exportpod", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function pod_list()
	{
		$data['title'] = $this->lang->line('export_pod') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));

		$exportspod = $this->Master_model->all_exportpod();

		$data = array();

		foreach ($exportspod as $r) {

			$editExportPod = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editpod" data-pod_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editExportPod,
				$r->pod_name,
				$r->name,
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

	public function dialog_exportpod_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addexportpod") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_pod'),
					'pagetype' => "add",
					'exportid' => 0,
					'countries' => $this->Origin_model->all_countries(),
				);
				$this->load->view('masters/dialog_add_exportpod', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editexportpod") {

			if (!empty($session)) {

				$getExportPODDetails = $this->Master_model->get_exportpod_detail_by_id($this->input->get('pid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_pod'),
					'pagetype' => "edit",
					'exportid' => $getExportPODDetails[0]->id,
					'get_export_details' => $getExportPODDetails,
					'countries' => $this->Origin_model->all_countries(),
				);
				$this->load->view('masters/dialog_add_exportpod', $data);
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

		if ($this->input->post('add_type') == 'exportpod') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('name');
					$country = $this->input->post('country');
					$longitude = $this->input->post('longitude');
					$latitude = $this->input->post('latitude');
					$colorcode = $this->input->post('colorcode');
					$status = $this->input->post('status');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataExportPod = array(
						"pod_name" => $name, "country_id" => $country,
						"latitude" => $latitude, "longitude" => $longitude, "color_code" => $colorcode,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => $status,
					);

					$insertExportPod = $this->Master_model->add_exportpod($dataExportPod);

					if ($insertExportPod > 0) {
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

					$exportpodid = $this->input->post('exportpodid');
					$name = $this->input->post('name');
					$country = $this->input->post('country');
					$longitude = $this->input->post('longitude');
					$latitude = $this->input->post('latitude');
					$colorcode = $this->input->post('colorcode');
					$status = $this->input->post('status');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataExportPod = array(
						"pod_name" => $name, "country_id" => $country,
						"latitude" => $latitude, "longitude" => $longitude, "color_code" => $colorcode,
						"updated_by" => $session['user_id'], 'is_active' => $status,
					);

					$updateExportPod = $this->Master_model->update_exportpod($dataExportPod, $exportpodid);

					if ($updateExportPod == true) {
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
