<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Auth extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Terralogin_model");
		$this->load->model("Terramaster_model");
		$this->load->library("jwttoken");
		$this->load->helper('url');
	}

	public function output($Return = array())
	{
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		exit(json_encode($Return));
	}

	public function login()
	{
		if ($this->input->method(TRUE) == "POST") {

			$requestdata = json_decode(file_get_contents("php://input"), true);

			$username = $requestdata["username"];
			$password = $requestdata["password"];
			$originId = (int) $requestdata["originId"];

			$requestBasicAuth = '';
			$headers = apache_request_headers();
			foreach ($headers as $header => $value) {
				if ($header == "Authorization") {
					list($a, $b) = explode(" ", $value);
					$requestBasicAuth = $b;
				}
			}

			$generateBasicAuth = base64_encode(BASIC_AUTH_UNAME_TERRA . ":" . BASIC_AUTH_PWD_TERRA);

			if ($requestBasicAuth != $generateBasicAuth) {
				return $this->output([
					"status" => false,
					"message" => "Bad Header Details"
				]);
			}

			$data = [
				"username" => $username,
				"password" => $password,
				"originId" => $originId,
			];

			$result = $this->Terralogin_model->login_app_terraerp($data);

			if (!$result) {
				return $this->output([
					"status" => false,
					"message" => "Invalid username or password"
				]);
			}

			$user = $this->Terralogin_model->read_user_information_terra_app($username, $originId)[0];

			if (empty($user->profilephoto)) {
				$user->profilephoto = "assets/img/user_icon.png";
			}

			// Time setup
			$date = new DateTime();
			$timezone = new DateTimeZone($user->timezone);
			$date->setTimezone($timezone);
			$timestamp = $date->getTimestamp();

			// Expiry
			$accessExpiry = $timestamp + (60 * 60); // 1 hour
			$refreshExpiry = $timestamp + (7 * 24 * 60 * 60); // 7 days

			// Access token
			$accessPayload = [
				"userid" => $user->userid + 0,
				"roleid" => $user->role_ids,
				"originid" => $originId + 0,
				"expiresin" => $accessExpiry
			];

			// Refresh token
			$refreshPayload = [
				"userid" => $user->userid + 0,
				"roleid" => $user->role_ids,
				"originid" => $originId + 0,
				"expiresin" => $refreshExpiry
			];

			$accessToken = JWT::encode($accessPayload, JWT_SECRET);
			$refreshToken = JWT::encode($refreshPayload, JWT_SECRET);

			// Device info
			$dataLoginDetails = [
				"user_id" => $user->userid,
				"device_id" => $requestdata["deviceId"],
				"fcm_token" => $requestdata["fcmToken"],
				"refresh_token" => $refreshToken,
				"device_model" => $requestdata["deviceModel"],
				"android_version" => $requestdata["androidVersion"],
				"app_version" => $requestdata["appVersion"],
				"is_logged_in" => 1,
				"created_by" => $user->userid,
				"updated_by" => $user->userid,
				"is_active" => 1,
			];

			$loginDetailId = $this->Login_model->add_login_details($dataLoginDetails);

			$response = [
				"userId" => $user->userid + 0,
				"fullName" => $user->fullname,
				"profilePhoto" =>  base_url() . $user->profilephoto,
				"roleIds" => $user->role_ids,
				"expiresIn" => $accessExpiry,
				"defaultTimezone" => $user->timezone,
				"contactNo" => $user->contactno,
				"address" => $user->address,
				"originId" => $originId,
				"emailId" => $user->emailid,
				"userName" => $username,
				"originName" => $user->origin_name,
				"originIcon" => $user->icon,
				"currencyName" => $user->currency_name,
				"currencyFormat" => $user->currency_format,
				"currencyExcelFormat" => $user->currency_format_excel,
				"accessToken" => $accessToken,
				"refreshToken" => $refreshToken,
				"loginDetailId" => $loginDetailId + 0,
			];

			return $this->output([
				"status" => true,
				"data" => $response
			]);
		}
	}

	public function refresh_token()
	{
		if ($this->input->method(TRUE) == "POST") {

			$requestdata = json_decode(file_get_contents("php://input"), true);
			$refreshToken = $requestdata["refreshToken"];

			try {

				$decoded = JWT::decode($refreshToken, JWT_SECRET, ['HS256']);

				// Expiry check
				if ($decoded->expiresin < time()) {
					throw new Exception("Refresh token expired");
				}

				// Check DB
				$login = $this->Login_model->get_by_refresh_token($refreshToken);

				if (!$login) {
					throw new Exception("Invalid refresh token");
				}

				// New access token
				$newAccess = [
					"userid" => $decoded->userid + 0,
					"originid" => $decoded->originid + 0,
					"roleid" => $decoded->roleid,
					"expiresin" => time() + (60 * 60)
				];

				$accessToken = JWT::encode($newAccess, JWT_SECRET);

				// OPTIONAL: rotate refresh token
				$newRefreshPayload = [
					"userid" => $decoded->userid + 0,
					"originid" => $decoded->originid + 0,
					"roleid" => $decoded->roleid,
					"expiresin" => time() + (7 * 24 * 60 * 60)
				];

				$newRefreshToken = JWT::encode($newRefreshPayload, JWT_SECRET);

				// Update DB
				$this->Login_model->update_refresh_token(
					$login->id,
					$newRefreshToken
				);

				return $this->output([
					"status" => true,
					"data" => [
						"accessToken" => $accessToken,
						"refreshToken" => $newRefreshToken
					]
				]);
			} catch (Exception $e) {

				return $this->output([
					"status" => false,
					"message" => $e->getMessage()
				]);
			}
		}
	}

	public function logout()
	{
		$requestdata = json_decode(file_get_contents("php://input"), true);
		$refreshToken = $requestdata["refreshToken"];

		$this->Login_model->logout_by_token($refreshToken);

		return $this->output([
			"status" => true,
			"message" => "Logged out successfully"
		]);
	}
}
