<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->model("Settings_model");
	}

	public function index()
     {
		$data['title'] = "Roles & Privileges";
		$session = $this->session->userdata('fullname');
		if(empty($session)){ 
			redirect("/logout");
		}
		// $data['breadcrumbs'] = $this->lang->line('xin_role_urole');
		// $data['path_url'] = 'roles';
		// $user = $this->Xin_model->read_employee_info($session['user_id']);
		//if($user[0]->user_role_id==1) {
			if(!empty($session)){ 
				$data['subview'] = $this->load->view("roles/role_list", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		//} else {
		//	redirect('admin/dashboard');
		//}
     }
}
