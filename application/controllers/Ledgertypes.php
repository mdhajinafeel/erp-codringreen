<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ledgertypes extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Financemaster_model');
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
		$data['title'] = $this->lang->line('ledgertype_title') . " - " . $this->lang->line('master_title') .  " - " . $this->lang->line('finance_title') . " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = "cgr_financemasters";
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/ledgertypes", $data, TRUE);
			$this->load->view("layout/layout_main", $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function ledger_type_list()
	{
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		} else {
			$origin_id = intval($this->input->get("originid"));
			$ledgerTypes = $this->Financemaster_model->all_ledger_types($origin_id);

			$data = array();

			foreach ($ledgerTypes as $r) {

				$editLedgerTypes = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editledger" data-ledger_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

				if ($r->is_active == 1) {
					$status = $this->lang->line('active');
				} else {
					$status = $this->lang->line('inactive');
				}

				$data[] = array(
					$editLedgerTypes,
					$r->ledger_name,
					$r->origin,
					$status
				);
			}

			$output = array(
				"data" => $data
			);
			echo json_encode($output);
			exit();
		}
	}

	public function dialog_ledger_type_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addledgertype") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_ledger_type'),
					'pagetype' => "add",
					'ledgertypeid' => 0,
				);
				$this->load->view('masters/dialog_add_ledgertype', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editledgertype") {

			if (!empty($session)) {

				$getLedgerTypeDetails = $this->Financemaster_model->get_ledger_type_by_id($this->input->get('lid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_ledger_type'),
					'pagetype' => "edit",
					'ledgertypeid' => $getLedgerTypeDetails[0]->id,
					'get_ledger_details' => $getLedgerTypeDetails,
				);
				$this->load->view('masters/dialog_add_ledgertype', $data);
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

		if ($this->input->post('add_type') == 'ledgertypes') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('ledger_type_name');
					$origin = $this->input->post('origin');
					$status = $this->input->post('status');

					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataLedger = array(
						"ledger_name" => $name, "created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $origin,
					);

					$insertLedgerType = $this->Financemaster_model->add_ledger_type($dataLedger);

					if ($insertLedgerType > 0) {
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

					$ledgertype_id = $this->input->post('ledgertype_id');
					$name = $this->input->post('ledger_type_name');
					$status = $this->input->post('status');

					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataLedger = array(
						"ledger_name" => $name,
						"updated_by" => $session['user_id'], 'is_active' => $status
					);

					$updateLedgerTypes = $this->Financemaster_model->update_ledger_type($dataLedger, $ledgertype_id);

					if ($updateLedgerTypes == true) {
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
