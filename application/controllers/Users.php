<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
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

	public function index()
	{
		$data['title'] = $this->lang->line('user_title') . " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect('/logout');
		}
		$data['path_url'] = 'cgr_users';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("users/user_list", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect('/logout');
		}
	}

	public function user_list()
	{
		$data['title'] = $this->lang->line('user_title') ." | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect('/logout');
		} else {
			$draw = intval($this->input->get("draw"));
			$originid = intval($this->input->get("originid"));
	
			if($originid == 0) {
				$user = $this->User_model->all_users($session['user_id']);
			} else {
				$user = $this->User_model->all_users_origin($originid, $session['user_id']);
			}
			$data = array();
	
			foreach ($user as $r) {
	
				$editUser = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-default waves-effect waves-light" data-role="edit" data-user_id="' . $r->userid . '"><span class="fas fa-pencil"></span></button></span>';
	
				if ($r->isactive == 1) {
					$status = $this->lang->line('active');
				} else {
					$status = $this->lang->line('inactive');
				}
	
				$data[] = array(
					$editUser,
					$r->fullname,
					$r->contactno,
					$r->role,
					$r->origins,
					$r->updatedby,
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
	}

	public function dialog_user_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "adduser") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_user'),
					'pagetype' => "add",
					'userid' => 0,
					'get_all_roles' => $this->User_model->all_roles(),
					'get_all_languages' => $this->Master_model->fetch_languages(),
					'get_all_timezones' => $this->Master_model->fetch_origin_timezones(),
				);
				$this->load->view('users/dialog_add_user', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "edituser") {

			if (!empty($session)) {
				$cipher_algo = "aes-256-cbc";
				$option = 0;
				$decrypt_iv = '3963673579222347';
				$decryption_key = "TjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8";

				$getUserDetails = $this->User_model->get_user_detail_by_id($this->input->get('uid'));

				$getUserDetails[0]->role = explode(', ', $getUserDetails[0]->role);
				$getUserDetails[0]->applicable_origins = explode(',', $getUserDetails[0]->applicable_origins);
				$getUserDetails[0]->password = openssl_decrypt(
					$getUserDetails[0]->password,
					$cipher_algo,
					$decryption_key,
					$option,
					$decrypt_iv
				);

				$data = array(
					'pageheading' => $this->lang->line('edit_user'),
					'pagetype' => "edit",
					'userid' => $getUserDetails[0]->userid,
					'get_user_details' => $getUserDetails,
					'get_all_roles' => $this->User_model->all_roles(),
					'get_all_languages' => $this->Master_model->fetch_languages(),
					'get_all_timezones' => $this->Master_model->fetch_origin_timezones(),
				);
				$this->load->view('users/dialog_add_user', $data);
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

		if ($this->input->post('add_type') == 'users') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$duplicate_user = $this->User_model->check_duplicate_users($this->input->post('username'));
					if ($duplicate_user == TRUE) {
						$Return['error'] = $this->lang->line('duplicate_username');
						$this->output($Return);
						exit;
					} else {

						$fname = "";
						if ($_FILES['photo']['size'] > 0) {

							if (is_uploaded_file($_FILES['photo']['tmp_name'])) {

								$allowed =  array('png', 'jpg', 'jpeg');
								$filename = $_FILES['photo']['name'];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if (in_array($ext, $allowed)) {

									$tmp_name = $_FILES["photo"]["tmp_name"];
									$profile = "assets/userimages/";

									$newfilename = 'profile_' . round(microtime(true)) . '.' . $ext;
									move_uploaded_file($tmp_name, $profile . $newfilename);
									$fname = "assets/userimages/" . $newfilename;
								} else {
									$Return['error'] = $this->lang->line('invalid_pic');
									$this->output($Return);
								}
							}
						}

						$name = $this->input->post('name');
						$emailid = $this->input->post('emailid');
						$contactno = $this->input->post('contactno');
						$address = $this->input->post('address');
						$username = $this->input->post('username');
						$password = $this->input->post('password');

						$cipher_algo = "aes-256-cbc";
						$option = 0;
						$encrypt_iv = '3963673579222347';
						$encryption_key = "TjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8";
						$encryptedPassword = openssl_encrypt($password, $cipher_algo, $encryption_key, $option, $encrypt_iv);

						$status = $this->input->post('status');
						$roles = $this->input->post('roles');
						$applicableorigins = $this->input->post('applicableorigins');
						$default_language = $this->input->post('default_language');
						$photo = $fname;

						if($status == 1) {
							$status = true;
						} else {
							$status = false;
						}

						$dataUser = array(
							"fullname" => $name, "emailid" => $emailid,
							"contactno" => $contactno, "address" => $address, "profilephoto" => $photo,
							"applicable_origins" => $applicableorigins, "createdby" => $session['user_id'],
							"updatedby" => $session['user_id'], "isactive" => $status, "default_language" => $default_language,
						);

						$insertUsers = $this->User_model->add($dataUser);

						if ($insertUsers > 0) {

							$totalRolesInsert = 0;

							$role_arr = explode(",", $roles);
							foreach ($role_arr as $row) {
								$dataLogin = array(
									"userid" => $insertUsers, "roleid" => $row,
									"username" => $username, "password" => $encryptedPassword, "createdby" => $session['user_id'],
									"updatedby" => $session['user_id'], 'isactive' => $status, 'isdeleted' => 0
								);

								$insertLogin = $this->User_model->addLogin($dataLogin);

								if ($insertLogin > 0) {
									$totalRolesInsert = $totalRolesInsert + 1;
								}
							}

							if ($totalRolesInsert > 0) {

								$Return['result'] = $this->lang->line('data_added');
								$this->output($Return);
								exit;
							} else {
								$Return['error'] = $this->lang->line('error_adding');
								$this->output($Return);
								exit;
							}
						} else {
							$Return['error'] = $this->lang->line('error_adding');
							$this->output($Return);
							exit;
						}
					}
				} else if ($this->input->post('action_type') == 'edit') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$fname = "";
					if ($_FILES['photo']['size'] > 0) {

						if (is_uploaded_file($_FILES['photo']['tmp_name'])) {

							$allowed =  array('png', 'jpg', 'jpeg');
							$filename = $_FILES['photo']['name'];
							$ext = pathinfo($filename, PATHINFO_EXTENSION);

							if (in_array($ext, $allowed)) {

								$tmp_name = $_FILES["photo"]["tmp_name"];
								$profile = "assets/userimages/";

								$newfilename = 'profile_' . round(microtime(true)) . '.' . $ext;
								move_uploaded_file($tmp_name, $profile . $newfilename);
								$fname = "assets/userimages/" . $newfilename;
							} else {
								$Return['error'] = $this->lang->line('invalid_pic');
								$this->output($Return);
							}
						}
					}

					$userid = $this->input->post('user_id');
					$name = $this->input->post('name');
					$emailid = $this->input->post('emailid');
					$contactno = $this->input->post('contactno');
					$address = $this->input->post('address');
					$username = $this->input->post('username');
					$password = $this->input->post('password');

					$cipher_algo = "aes-256-cbc";
					$option = 0;
					$encrypt_iv = '3963673579222347';
					$encryption_key = "TjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8";
					$encryptedPassword = openssl_encrypt($password, $cipher_algo, $encryption_key, $option, $encrypt_iv);

					$status = $this->input->post('status');
					$roles = $this->input->post('roles');
					$applicableorigins = $this->input->post('applicableorigins');
					$default_language = $this->input->post('default_language');
					$photo = $fname;

					if($status == 1) {
						$status = true;
					} else {
						$status = false;
					}

					if($photo == "" || $photo == null) {
						$dataUser = array(
							"fullname" => $name, "emailid" => $emailid,
							"contactno" => $contactno, "address" => $address,
							"applicable_origins" => $applicableorigins,
							"updatedby" => $session['user_id'], "isactive" => $status, "default_language" => $default_language,
						);
					} else {
						$dataUser = array(
							"fullname" => $name, "emailid" => $emailid,
							"contactno" => $contactno, "address" => $address, "profilephoto" => $photo,
							"applicable_origins" => $applicableorigins,
							"updatedby" => $session['user_id'], "isactive" => $status, "default_language" => $default_language,
						);
					}

					$updateUsers = $this->User_model->update($dataUser, $userid);

					if ($updateUsers == true) {

						$result = $this->User_model->delete_user_login($userid);

						$totalRolesInsert = 0;

						$role_arr = explode(",", $roles);
						foreach ($role_arr as $row) {
							$dataLogin = array(
								"userid" => $userid, "roleid" => $row,
								"username" => $username, "password" => $encryptedPassword, "createdby" => $session['user_id'],
								"updatedby" => $session['user_id'], 'isactive' => $status, 'isdeleted' => 0
							);

							$insertLogin = $this->User_model->addLogin($dataLogin);

							if ($insertLogin > 0) {
								$totalRolesInsert = $totalRolesInsert + 1;
							}
						}

						if ($totalRolesInsert > 0) {

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
