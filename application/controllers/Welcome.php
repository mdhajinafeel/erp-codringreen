<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->model("Settings_model");
	}

	public function index()
	{
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			$data['title'] = "Login | " . $this->Settings_model->site_title();
			$this->load->view('auth/login', $data);
		} else {
		    $data['path_url'] = 'cgr_dashboard';
			$data['title'] = $this->lang->line('dashboard_title'). " | " . $this->Settings_model->site_title();
			$data['subview'] = $this->load->view('dashboard', $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		}
	}
}
