<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Origins extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Origin_model");
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
		$data['title'] = $this->lang->line('origins_title') . " - " . $this->lang->line('settings_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		// $data['breadcrumbs'] = $this->lang->line('xin_role_urole');
		$data['path_url'] = 'cgr_origins';
		// $user = $this->Xin_model->read_employee_info($session['user_id']);
		//if($user[0]->user_role_id==1) {
		if (!empty($session)) {
			$data['subview'] = $this->load->view("origins/origin_list", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
		//} else {
		//	redirect('admin/dashboard');
		//}
	}

	public function origin_list()
	{
		$data['title'] =  $this->lang->line('origins_title') . " - " . $this->lang->line('settings_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (!empty($session)) {
			$this->load->view("origins/origin_list", $data);
		} else {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));

		$origin = $this->Origin_model->all_origins();
		$data = array();

		foreach ($origin->result() as $r) {
			$editOrigin = '<span data-toggle="tooltip" data-placement="top" title="'.$this->lang->line('edit').'"><button type="button" class="btn icon-btn btn-xs btn-default waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-orign_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';
			$originCode = $r->origin_iso2_code . ' / ' . $r->origin_iso3_code;

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}
			$data[] = array(
				$editOrigin,
				$r->id,
				$r->origin_name,
				$originCode,
				$status
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $origin->num_rows(),
			"recordsFiltered" => $origin->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dialog_origin_add()
	{
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');
		if (!empty($session)) {
			$data = array(
				'get_all_countries' => $this->Origin_model->all_countries(),
			);
			$this->load->view('origins/dialog_add_origin', $data);
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function add()
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');
		if ($this->input->post('add_type') == 'origin') {

			if (!empty($session)) {

				$id = $this->input->post('origin_id');
				$result = $this->Origin_model->get_countries_info($id);

				if ($result == TRUE) {

					$data = array(
						'origin_name' => $result[0]->name,
						'origin_iso2_code' => $result[0]->code,
						'origin_iso3_code' => $result[0]->alpha_3,
						'created_by' => $session['user_id'],
						'updated_by' => $session['user_id'],
						'is_active' => 1,
					);

					$insertresult = $this->Origin_model->add($data);

					if ($insertresult == TRUE) {
						$Return['result'] = $this->lang->line('data_added');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						//$this->session->set_flashdata('expire_official_document', 'expire_official_document');
						$this->output($Return);
					} else {
						$Return['error'] = $this->lang->line('error_adding');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
					}
				} else {
					$Return['error'] = $this->lang->line('error_adding');
					$Return['csrf_hash'] = $this->security->get_csrf_hash();
					$this->output($Return);
				}
			} else {
				redirect("/logout");
			}
		} else {
			$Return['error'] = $this->lang->line('invalid_request');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}
}
