<?php

// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
// ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Machines extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
		$this->load->model("Contract_model");
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
		$data['title'] = $this->lang->line('machine_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$data['path_url'] = 'cgr_masters';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/machines", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function machines_list()
	{
		$data['title'] = $this->lang->line('machine_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$origin_id = intval($this->input->get("originid"));

		if ($origin_id == 0) {
			$machines = $this->Master_model->all_machines();
		} else {
			$machines = $this->Master_model->all_machines_originid($origin_id);
		}
		$data = array();

		foreach ($machines as $r) {

			$editMachine = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editmachine" data-machine_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editMachine,
				$r->machine_type,
				$r->chassis_no,
				$r->supplier_name,
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

	public function dialog_machine_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addmachine") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_machine'),
					'pagetype' => "add",
					'machineid' => 0,
				);
				$this->load->view('masters/dialog_add_machine', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editmachine") {

			if (!empty($session)) {

				$getMachineDetails = $this->Master_model->get_machine_detail_by_id($this->input->get('mid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_machine'),
					'pagetype' => "edit",
					'machineid' => $getMachineDetails[0]->id,
					'suppliers' => $this->Contract_model->get_suppliers_by_origin($getMachineDetails[0]->origin_id),
					'get_machine_details' => $getMachineDetails,
				);
				$this->load->view('masters/dialog_add_machine', $data);
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

		if ($this->input->post('add_type') == 'machines') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$machine_name = $this->input->post('machine_name');
					$chassis_model = $this->input->post('chassis_model');
					$supplier = $this->input->post('supplier');
					$status = $this->input->post('status');
					$origin = $this->input->post('origin');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataMachine = array(
						"machine_type" => $machine_name,
						"chassis_no" => $chassis_model,
						"supplier_id" => $supplier,
						"origin_id" => $origin,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'],
						'is_active' => $status,
					);

					$insertMachine = $this->Master_model->add_machine($dataMachine);

					if ($insertMachine > 0) {
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

					$machine_id = $this->input->post('machineid');
					$machine_name = $this->input->post('machine_name');
					$chassis_model = $this->input->post('chassis_model');
					$supplier = $this->input->post('supplier');
					$status = $this->input->post('status');
					$origin = $this->input->post('origin');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataMachine = array(
						"machine_type" => $machine_name,
						"chassis_no" => $chassis_model,
						"supplier_id" => $supplier,
						"origin_id" => $origin,
						"updated_by" => $session['user_id'],
						'is_active' => $status,
					);

					$updateMachine = $this->Master_model->update_machine($dataMachine, $machine_id);

					if ($updateMachine == true) {
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

	public function fetch_suppliers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getSuppliers = $this->Contract_model->get_suppliers_by_origin($this->input->get('originid'));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . $supplier->supplier_name . "</option>";
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
}
