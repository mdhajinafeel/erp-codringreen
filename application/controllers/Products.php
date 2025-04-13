<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Products extends MY_Controller
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
		$data['title'] = $this->lang->line('product_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
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
				$data['subview'] = $this->load->view("masters/products", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function product_list()
	{
		$data['title'] = $this->lang->line('product_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$products = $this->Master_model->all_products();
		} else {
			$products = $this->Master_model->all_products_origin($originid);
		}
		$data = array();

		foreach ($products as $r) {


			$editProduct = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="edit" data-product_id="' . $r->product_id . '"><span class="fas fa-pencil"></span></button></span>';
			$productIcon = '<img style="width: 40px; height: 40px; background: burlywood;
					padding: 8px;" src="' . site_url() . $r->icon_name . '" />';
			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editProduct,
				$r->product_name,
				$r->product_desc,
				$productIcon,
				$r->origin,
				$status
			);
		}

		$output = array(
			"draw" => $draw,
			//"recordsTotal" => 10,
			//"recordsFiltered" => 10,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dialog_product_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "addproduct") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_product'),
					'pagetype' => "add",
					'productid' => 0,
					'get_all_producticons' => $this->Master_model->all_product_icons(),
				);
				$this->load->view('masters/dialog_add_product', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editproduct") {

			if (!empty($session)) {

				$getProductDetails = $this->Master_model->get_product_detail_by_id($this->input->get('pid'));

				$data = array(
					'pageheading' => $this->lang->line('edit_product'),
					'pagetype' => "edit",
					'productid' => $getProductDetails[0]->product_id,
					'get_all_producticons' => $this->Master_model->all_product_icons(),
					'get_product_details' => $getProductDetails,
				);
				$this->load->view('masters/dialog_add_product', $data);
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

		if ($this->input->post('add_type') == 'products') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('name');
					$description = $this->input->post('description');
					$selectedicon = $this->input->post('selectedicon');
					$status = $this->input->post('status');
					$product_origin = $this->input->post('product_origin');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataUser = array(
						"product_name" => $name, "product_desc" => $description,
						"product_icon" => $selectedicon, "createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'origin_id' => $product_origin,
						'isactive' => $status,
					);

					$insertProduct = $this->Master_model->add_product($dataUser);

					if ($insertProduct > 0) {
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

					$product_id = $this->input->post('product_id');
					$name = $this->input->post('name');
					$description = $this->input->post('description');
					$selectedicon = $this->input->post('selectedicon');
					$status = $this->input->post('status');
					$product_origin = $this->input->post('product_origin');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataUser = array(
						"product_name" => $name, "product_desc" => $description,
						"product_icon" => $selectedicon,
						"updatedby" => $session['user_id'], 'origin_id' => $product_origin,
						'isactive' => $status,
					);

					$updateProduct = $this->Master_model->update_product($dataUser, $product_id);

					if ($updateProduct == true) {
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
