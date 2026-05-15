<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Terralogin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function login_app_terraerp(array $data)
    {
        $originId = $data['originId'];
        $sql = "SELECT password FROM tbl_login A 
				INNER JOIN tbl_role_master B ON B.roleid = A.roleid 
				INNER JOIN tbl_user_registration C ON C.userid = A.userid 
				WHERE username = ? AND A.isactive = ? AND A.isdeleted = ? AND B.is_app_user = ?
				AND C.applicable_origins LIKE '%$originId%' 
				GROUP BY username";
        $binds = array($data['username'], 1, 0, 1);

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

    public function read_user_information_terra_app(string $username, int $originId)
    {
        $sql = "SELECT B.userid, B.fullname, B.profilephoto, 
				A.roleid, E.timezone_abbreviation AS timezone, F.language_name, B.contactno, B.emailid, B.address, 
				geticon_by_origin(1) AS icon, getapplicableorigins_byid(1) AS origin_name,
				H.currency_name, H.currency_abbreviation AS currency_format, H.currency_excel_format AS currency_format_excel, getroleids_userid(A.userid) AS role_ids 
				FROM tbl_login A 
				INNER JOIN tbl_user_registration B ON B.userid = A.userid 
				INNER JOIN tbl_origin_timezones D ON D.id = B.default_timezone 
                INNER JOIN tbl_master_timezones E ON E.id = D.timezone_id 
				INNER JOIN tbl_languages F ON F.id = B.default_language 
                INNER JOIN tbl_origin_currencies G ON G.origin_id = 1 
                INNER JOIN tbl_currency H ON H.id = G.currency_id 
				WHERE username = '$username' 
				AND A.isactive = 1 AND B.applicable_origins LIKE '%$originId%' AND A.isdeleted = 0 GROUP BY username";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function delete_login_details(int $userid)
    {
        return $this->db
            ->where("user_id", $userid)
            ->update("tbl_login_details", [
                "is_active" => 0,
                "updated_date" => date("Y-m-d H:i:s")
            ]);
    }

    public function add_login_details(array $data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_login_details', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function check_user_exists_terra_app(int $userId, int $originId)
    {
        $query = $this->db->query("SELECT COUNT(A.userid) AS cnt FROM tbl_user_registration A 
			WHERE A.isactive = 1 AND A.userid = $userId 
			AND A.applicable_origins LIKE '%$originId%'");

        $rowData = $query->result();
        if ($rowData[0]->cnt == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_by_refresh_token(string $token)
    {
        return $this->db
            ->where("refresh_token", $token)
            ->where("is_logged_in", 1)
            ->order_by("id", "DESC") // latest record
            ->limit(1)
            ->get("tbl_login_details")
            ->row();
    }

    public function update_refresh_token(int $id, string $token)
    {
        return $this->db
            ->where("id", $id)
            ->update("tbl_login_details", [
                "refresh_token" => $token,
                "updated_date" => date("Y-m-d H:i:s")
            ]);
    }

    public function logout_by_token(string $token)
    {
        return $this->db
            ->where("refresh_token", $token)
            ->update("tbl_login_details", [
                "is_logged_in" => 0
            ]);
    }

    public function get_latest_active_fcm_token(int $userId)
    {
        return $this->db
            ->select('fcm_token')
            ->from('tbl_login_details')
            ->where('user_id', $userId)
            ->where('is_logged_in', 1)
            ->where('is_active', 1)
            ->where('fcm_token !=', '')
            ->order_by('updated_date', 'DESC')
            ->limit(1)
            ->get()
            ->row();
    }
}
