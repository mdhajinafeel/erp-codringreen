<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inputparameters extends MY_Controller
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
		$data['title'] = $this->lang->line('inputparams_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
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
				$data['subview'] = $this->load->view("masters/inputparameters", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function inputparameters_list()
	{
		$data['title'] = $this->lang->line('inputparams_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$inputparameters = $this->Master_model->all_inputparameters();
		} else {
			$inputparameters = $this->Master_model->all_inputparameters_originid($originid);
		}
		$data = array();

		foreach ($inputparameters as $r) {

			$editInputParameters = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editinputparameter" data-input_parameter_id="' . $r->input_parameter_id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$r->product_type_name = $this->lang->line($r->product_type_name);

			$data[] = array(
				$editInputParameters,
				$r->parametername,
				$r->parametercode,
				$r->product_type_name,
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

	public function dialog_inputparameter_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		$getProductTypes = $this->Master_model->all_product_types();

		if ($this->input->get('type') == "addinputparameter") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_inputparameters'),
					'pagetype' => "add",
					'inputparameterid' => 0,
					'get_product_types' => $getProductTypes,
				);
				$this->load->view('masters/dialog_add_inputparameter', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editinputparameter") {

			if (!empty($session)) {

				$getInputParameterDetails = $this->Master_model->get_inputparameter_detail_by_id($this->input->get('ipid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_inputparamaters'),
					'pagetype' => "edit",
					'inputparameterid' => $getInputParameterDetails[0]->input_parameter_id,
					'get_inputparameter_details' => $getInputParameterDetails,
					'get_product_types' => $getProductTypes,
				);
				$this->load->view('masters/dialog_add_inputparameter', $data);
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

		if ($this->input->post('add_type') == 'inputparameter') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$parameter_name = $this->input->post('parametername');
					$product_type_id = $this->input->post('product_type_id');
					$parameter_code = $this->input->post('parametercode');
					$status = $this->input->post('status');
					$inputparam_origin_id = $this->input->post('inputparam_origin_id');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataInputParameter = array(
						"product_type_id" => $product_type_id, "parametername" => $parameter_name,
						"parametercode" => $parameter_code, "createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'isactive' => $status,
						'origin_id' => $inputparam_origin_id,
					);

					$insertInputParameter = $this->Master_model->add_inputparameter($dataInputParameter);

					if ($insertInputParameter > 0) {
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

					$inputparameter_id = $this->input->post('inputparameter_id');
					$parameter_name = $this->input->post('parametername');
					$product_type_id = $this->input->post('product_type_id');
					$parameter_code = $this->input->post('parametercode');
					$status = $this->input->post('status');
					$inputparam_origin_id = $this->input->post('inputparam_origin_id');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataInputParameter = array(
						"product_type_id" => $product_type_id, "parametername" => $parameter_name,
						"parametercode" => $parameter_code, "createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'isactive' => $status,
						'origin_id' => $inputparam_origin_id,
					);

					$updateInputParameter = $this->Master_model->update_inputparameter($dataInputParameter, $inputparameter_id);

					if ($updateInputParameter == true) {
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
