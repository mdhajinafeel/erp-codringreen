<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Claimtracker extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
		$this->load->model("Export_model");
		$this->load->model("Claimtracker_model");
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
		$data['title'] = $this->lang->line('claimtracker_title') . " - " . $this->lang->line('sales') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		$role_resources_ids = explode(',', $session["role_id"]);

		if (empty($session)) {
			redirect("/logout");
		}

		if (in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids) || in_array('7', $role_resources_ids)) {
			redirect("/errorpage");
		} else {
			$data['path_url'] = 'cgr_claimtracker';
			if (!empty($session)) {
				$data['subview'] = $this->load->view("claimtracker/claimtrackers", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function claim_list()
	{
		$data['title'] = $this->lang->line('claimtracker_title') . " - " . $this->lang->line('sales') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		$claimLists = $this->Claimtracker_model->fetch_claim_tracker_list($originid);
		$data = array();

		foreach ($claimLists as $r) {

			$editClaimLists = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editclaim" data-claimid="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
				<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deleteclaim" data-claimid="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

			if ($r->is_claimed == 1) {
				$status = $this->lang->line('text_claimed');
			} else {
				$status = $this->lang->line('active');
			}

			$data[] = array(
				$editClaimLists,
				$r->claim_reference_id,
				$r->sa_number,
				$r->claim_amount,
				$r->claim_remarks,
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

	public function dialog_claim_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addclaim") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_claim'),
					'pagetype' => "add",
					'claimid' => 0,
				);
				$this->load->view('claimtracker/dialog_add_claimtracker', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editclaim") {

			if (!empty($session)) {

				$getShippingDetails = $this->Master_model->get_shipping_detail_by_id($this->input->get('sid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_claim'),
					'pagetype' => "edit",
					'claimid' => $getShippingDetails[0]->id,
					'get_claim_details' => $getShippingDetails,
				);
				$this->load->view('claimtracker/dialog_add_claimtracker', $data);
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

		if ($this->input->post('add_type') == 'invoiceclaim') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$saNumber = $this->input->post('sa_number');
					$saNumberText = $this->input->post('sa_number_text');
					$claimAmount = $this->input->post('claim_amount');
					$claimRemarks = $this->input->post('claim_remarks');
					$originId = $this->input->post('origin');

					//CLAIM ID
					$getCountClaimData = $this->Claimtracker_model->fetch_count_tracker_exportid($saNumber);

					$claimCount = 1;
					if ($getCountClaimData[0]->max_claim > 0) {
						$claimCount = $getCountClaimData[0]->max_claim;
					}

					if ($claimCount < 10) {
						$claimCount = str_pad($claimCount, 2, '0', STR_PAD_LEFT);
					}

					$claimId = "CN-" . $saNumberText . "--" . $claimCount;

					$dataClaim = array(
						"export_id" => $saNumber,
						"claim_amount" => $claimAmount,
						"claim_remarks" => $claimRemarks,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'],
						'is_active' => 1,
						"is_claimed" => 0,
						'origin_id' => $originId,
						"claim_reference_id" => $claimId,
					);

					$insertClaimData = $this->Claimtracker_model->add_claimtracker($dataClaim);

					if ($insertClaimData > 0) {
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

					$shipping_id = $this->input->post('shipping_id');
					$shippingline = $this->input->post('shipping_name');
					$shipping_originid = $this->input->post('shipping_originid');
					$status = $this->input->post('status');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataShipping = array(
						"shipping_line" => $shippingline,
						"updatedby" => $session['user_id'],
						'isactive' => $status,
						'origin_id' => $shipping_originid,
					);

					$updateShipping = $this->Master_model->update_shipping($dataShipping, $shipping_id);

					if ($updateShipping == true) {
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

	public function fetch_sanumbers()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (!empty($session)) {
			$result = "<option value='0'>" . $this->lang->line('select') . "</option>";
			if ($this->input->get('originid') > 0) {
				$getSANumbers = $this->Export_model->fetch_sa_numbers_by_origin($this->input->get('originid'));
				foreach ($getSANumbers as $sanumber) {
					$result = $result . "<option value='" . $sanumber->id . "'>" . $sanumber->sa_number . "</option>";
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