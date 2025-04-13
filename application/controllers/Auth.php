<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model("Settings_model");
		$this->load->model("Master_model");
	}

	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}
	
	public function login()
	{

		$username = $this->input->post('login-username');
		$password = $this->input->post('login-password');

		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');

		$data = array(
			'username' => $username,
			'password' => $password
		);

		$result = $this->Login_model->login($data);

		if ($result == TRUE) {
			$result = $this->Login_model->read_user_information($username);

			if($result[0]->profilephoto == null || $result[0]->profilephoto == "") {
				$result[0]->profilephoto = "assets/img/user_icon.png";
			}

			$session_data = array(
				'user_id' => $result[0]->userid,
				'full_name' => $result[0]->fullname,
				'profile_photo' => $result[0]->profilephoto,
				'role_id' => $result[0]->role_id,
				'applicable_origins_id' => $result[0]->applicable_origins,
				'applicable_origins' => $this->Master_model->all_applicable_origins($result[0]->applicable_origins),
				'default_timezone' => $result[0]->timezone
			);
			$this->session->set_userdata('fullname', $session_data);
			$this->session->set_userdata('userid', $session_data);
			$this->session->set_userdata('profilephoto', $session_data);
			$this->session->set_userdata('role_id', $session_data);
			$this->session->set_userdata('site_lang', $result[0]->language_name);

			$Return['result'] = "Sucessfully logged in";//$this->lang->line('xin_success_logged_in');

			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			//$this->session->set_flashdata('expire_official_document', 'expire_official_document');
			$this->output($Return);
		} else {
			$Return['error'] = "Invalid Credentials";//$this->lang->line('xin_error_invalid_credentials');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}
}
