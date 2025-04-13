<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Authlogin extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Login_model");
		$this->load->model("Settings_model");
		$this->load->model("Master_model");
		$this->load->library("jwttoken");
		$this->load->helper('url');
	}

	public function output($Return = array())
	{
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		exit(json_encode($Return));
	}

	public function index()
	{
		if ($this->input->method(TRUE) == "POST") {

			$requestdata = json_decode(file_get_contents("php://input"), true);

			$username = $requestdata["username"];
			$password = $requestdata["password"];
			$roleId = (int) $requestdata["roleId"];
			$originId = (int) $requestdata["originId"];
			
			$data = array(
				"username" => $username,
				"password" => $password,
				"roleId" => $roleId,
				"originId" => $originId,
			);

			$headers = apache_request_headers();
			foreach ($headers as $header => $value) {
				if ($header == "Authorization") {
					list($a, $b) = explode(" ", $value);
					$requestBasicAuth = $b;
				}
			}

			$generateBasicAuth = base64_encode(BASIC_AUTH_UNAME . ":" . BASIC_AUTH_PWD);

			if ($requestBasicAuth == $generateBasicAuth) {

				$result = $this->Login_model->login_app($data);

				if ($result == TRUE) {
					$result = $this->Login_model->read_user_information_app($username, $roleId, $originId);

					if ($result[0]->profilephoto == null || $result[0]->profilephoto == "") {
						$result[0]->profilephoto = "assets/img/user_icon.png";
					}

					$date = new DateTime();
					$timezone = new DateTimeZone($result[0]->timezone);
					$date->setTimezone($timezone);
					$timestamp = ($date->getTimestamp());
					$expiresIn = $timestamp + (300 * 60);

					$token["userid"] = $result[0]->userid;
					$token["fullname"] = $result[0]->fullname;
					$token["roleid"] = $result[0]->roleid;
					$token["originid"] = $originId;
					$token["defaulttimezone"] = $result[0]->timezone;
					$token["expiresin"] = $expiresIn;

					$token1["userid"] = $result[0]->userid;
					$token1["fullname"] = $result[0]->fullname;
					$token1["roleid"] = $result[0]->roleid;
					$token1["originid"] = $originId;
					$token1["defaulttimezone"] = $result[0]->timezone;

					$data = array(
						"userId" => $result[0]->userid + 0,
						"fullName" => $result[0]->fullname,
						"profilePhoto" =>  base_url() . $result[0]->profilephoto,
						"roleId" => $result[0]->roleid + 0,
						"roleName" => $result[0]->rolename,
						"expiresIn" => $expiresIn + 0,
						"defaultTimezone" => $result[0]->timezone,
						"contactNo" => $result[0]->contactno, 
						"address" => $result[0]->address,
						"originId" => $originId,
						"emailId" => $result[0]->emailid,
						"userName" => $username,
						"accessToken" => JWT::encode($token, JWT_SECRET),
						"refreshToken" => JWT::encode($token1, JWT_SECRET),
					);

					$Return["status"] = true;
					$Return["data"] = $data;
					http_response_code(200);
					$this->output($Return);
				} else {
					$Return["status"] = false;
					$Return["message"] = "Invalid username or password";
					http_response_code(401);
					$this->output($Return);
				}
			} else {

				$Return["status"] = false;
				$Return["message"] = "Bad Header Details";
				http_response_code(400);
				$this->output($Return);
			}
		} else {
			$Return["status"] = false;
			$Return["message"] = "Bad Header Details";
			http_response_code(400);
			$this->output($Return);
		}
	}
}