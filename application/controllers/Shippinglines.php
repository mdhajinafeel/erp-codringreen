<?php


 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Shippinglines extends MY_Controller
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
		$data['title'] = $this->lang->line('shipping_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
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
				$data['subview'] = $this->load->view("masters/shippinglines", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function shipping_list()
	{
		$data['title'] = $this->lang->line('shipping_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$shippinglines = $this->Master_model->all_shippinglines();
		} else {
			$shippinglines = $this->Master_model->all_shippinglines_origin($originid);
		}
		$data = array();

		foreach ($shippinglines as $r) {

			$editShippingLine = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editshipping" data-shipping_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editShippingLine,
				$r->shipping_line,
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

	public function dialog_shipping_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addshipping") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_shippingline'),
					'pagetype' => "add",
					'shippingid' => 0,
				);
				$this->load->view('masters/dialog_add_shipping', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editshipping") {

			if (!empty($session)) {

				$getShippingDetails = $this->Master_model->get_shipping_detail_by_id($this->input->get('sid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_shippingline'),
					'pagetype' => "edit",
					'shippingid' => $getShippingDetails[0]->id,
					'get_shipping_details' => $getShippingDetails,
				);
				$this->load->view('masters/dialog_add_shipping', $data);
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

		if ($this->input->post('add_type') == 'shippingline') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

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
						"createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'isactive' => $status,
						'origin_id' => $shipping_originid,
					);

					$insertShipping = $this->Master_model->add_shipping($dataShipping);

					if ($insertShipping > 0) {
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
						"updatedby" => $session['user_id'], 'isactive' => $status,
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
}
