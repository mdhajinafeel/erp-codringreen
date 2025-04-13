<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Accountheads extends MY_Controller
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
		$data['title'] = $this->lang->line('accounthead_title') . " - " . $this->lang->line('master_title') .  " - " . $this->lang->line('finance_title') . " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = "cgr_financemasters";
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/accountheads", $data, TRUE);
			$this->load->view("layout/layout_main", $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function accounthead_list()
	{
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		} else {
			$origin_id = intval($this->input->get("originid"));
			$accountHeads = $this->Financemaster_model->all_account_heads($origin_id);

			$data = array();

			foreach ($accountHeads as $r) {

				$editAccountHeads = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editaccounthead" data-account_head_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

				if ($r->is_active == 1) {
					$status = $this->lang->line('active');
				} else {
					$status = $this->lang->line('inactive');
				}

				$data[] = array(
					$editAccountHeads,
					$r->ledger_name,
					$r->code,
					$r->name_in_app,
					$r->name_in_ledger,
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

	public function dialog_account_head_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addaccounthead") {
			if (!empty($session)) {

				$applicable_origins = $session["applicable_origins"];

				$data = array(
					'pageheading' => $this->lang->line('add_account_head'),
					'pagetype' => "add",
					'accountheadid' => 0,
					"ledgertypes" => $this->Financemaster_model->all_ledger_types($applicable_origins[0]->origin_id),
				);
				$this->load->view('masters/dialog_add_accounthead', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editaccounthead") {

			if (!empty($session)) {

				$getAccountHeadDetails = $this->Financemaster_model->get_account_head_by_id($this->input->get('aid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_account_head'),
					'pagetype' => "edit",
					'accountheadid' => $getAccountHeadDetails[0]->id,
					'get_accounthead_details' => $getAccountHeadDetails,
					"ledgertypes" => $this->Financemaster_model->all_ledger_types($getAccountHeadDetails[0]->origin_id),
				);
				$this->load->view('masters/dialog_add_accounthead', $data);
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

		if ($this->input->post('add_type') == 'accounthead') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$origin = $this->input->post('origin');
					$status = $this->input->post('status');
					$accountheadcode = $this->input->post('accountheadcode');
					$app_account_head = $this->input->post('app_account_head');
					$ledger_account_head = $this->input->post('ledger_account_head');
					$ledger_type = $this->input->post('ledger_type');

					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataAccountHead = array(
						"code" => $accountheadcode, "name_in_ledger" => $ledger_account_head, 
						"ledger_type" => $ledger_type, "name_in_app" => $app_account_head, "created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $origin,
					);

					$insertAccountHead = $this->Financemaster_model->add_account_heads($dataAccountHead);

					if ($insertAccountHead > 0) {
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

					$accountheadid = $this->input->post('accountheadid');
					$status = $this->input->post('status');
					$accountheadcode = $this->input->post('accountheadcode');
					$app_account_head = $this->input->post('app_account_head');
					$ledger_account_head = $this->input->post('ledger_account_head');
					$ledger_type = $this->input->post('ledger_type');

					if($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataAccountHead = array(
						"code" => $accountheadcode, "name_in_ledger" => $ledger_account_head, 
						"ledger_type" => $ledger_type, "name_in_app" => $app_account_head,
						"updated_by" => $session['user_id'], 'is_active' => $status
					);

					$updateAccountHead = $this->Financemaster_model->update_account_heads($dataAccountHead, $accountheadid);

					if ($updateAccountHead == true) {
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
	
	public function get_ledger_types_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getLedgerTypes = $this->Financemaster_model->all_ledger_types($this->input->get("originid"));
                foreach ($getLedgerTypes as $ledgertype) {
                    $result = $result . "<option value='" . $ledgertype->id . "'>" . $ledgertype->product_name . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }
}
