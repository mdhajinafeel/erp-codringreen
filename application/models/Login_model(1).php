<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Login_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Read data using username and password
	public function login($data)
	{

		$sql = 'SELECT password FROM tbl_login A 
				INNER JOIN tbl_role_master B ON B.roleid = A.roleid 
				WHERE username = ? AND A.isactive = ? AND A.isdeleted = ? AND B.is_app_user = ? 
				GROUP BY username';
		$binds = array($data['username'], 1, 0, 0);
		
		$query = $this->db->query($sql, $binds);

		$cipher_algo = "aes-256-cbc";
		$option = 0;
		$encrypt_iv = '3963673579222347';
		$encryption_key = "TjWnZr4u7x!A%D*G-KaPdSgVkXp2s5v8";
		$encryptedPassword = openssl_encrypt($data['password'], $cipher_algo, $encryption_key, $option, $encrypt_iv);

		if ($query->num_rows() > 0) {
			$rw_password = $query->result();
			if ($encryptedPassword == $rw_password[0]->password) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// Read data from database to show data in admin page
	public function read_user_information($username)
	{

		$sql = 'SELECT B.userid, B.fullname, B.profilephoto, 
				GROUP_CONCAT(A.roleid SEPARATOR ", ") as role_id, B.applicable_origins, 
				E.timezone_abbreviation AS timezone, F.language_name
				FROM tbl_login A 
				INNER JOIN tbl_user_registration B ON B.userid = A.userid 
				INNER JOIN tbl_origin_timezones D ON D.id = B.default_timezone 
                INNER JOIN tbl_master_timezones E ON E.id = D.timezone_id 
				INNER JOIN tbl_languages F ON F.id = B.default_language 
				INNER JOIN tbl_role_master C ON C.roleid = A.roleid WHERE username = ? 
				AND A.isactive = 1 AND A.isdeleted = 0 AND C.is_app_user = ? GROUP BY username';
		$binds = array($username, 0);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
}
