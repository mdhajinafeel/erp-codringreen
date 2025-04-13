<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all_roles()
	{
		$query = $this->db->query("SELECT roleid, rolename FROM tbl_role_master WHERE isactive = 1 AND roleid <> 1");
		return $query->result();
	}

	public function all_origins()
	{
		$query = $this->db->query("SELECT id, origin_name FROM tbl_origins WHERE is_active = 1");
		return $query->result();
	}

	public function all_users($userid)
	{
		if ($userid == 1) {
			$query = $this->db->query("SELECT A.userid, A.fullname, A.contactno, 
					GROUP_CONCAT(D.rolename SEPARATOR ', ') as role, 
					getapplicableorigins_byid(A.applicable_origins) as origins, B.username, 
					A.isactive, C.fullname AS updatedby FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					INNER JOIN tbl_user_registration C ON C.userid = A.updatedby 
					INNER JOIN tbl_role_master D ON D.roleid = B.roleid
					WHERE B.isdeleted = 0 AND B.roleid <> 1
					GROUP BY A.userid");
		} else {
			$query = $this->db->query("SELECT A.userid, A.fullname, A.contactno, 
					GROUP_CONCAT(D.rolename SEPARATOR ', ') as role, 
					getapplicableorigins_byid(A.applicable_origins) as origins, B.username, 
					A.isactive, C.fullname AS updatedby FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					INNER JOIN tbl_user_registration C ON C.userid = A.updatedby 
					INNER JOIN tbl_role_master D ON D.roleid = B.roleid
					WHERE B.isdeleted = 0 AND B.roleid <> 1 AND B.userid <> $userid
					GROUP BY A.userid");
		}

		return $query->result();
	}

	public function all_users_origin($originId, $userid)
	{
		if($userid == 1) {
			$query = $this->db->query("SELECT A.userid, A.fullname, A.contactno, 
					GROUP_CONCAT(D.rolename SEPARATOR ', ') as role, 
					getapplicableorigins_byid(A.applicable_origins) as origins, B.username, 
					A.isactive, C.fullname AS updatedby FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					INNER JOIN tbl_user_registration C ON C.userid = A.updatedby 
					INNER JOIN tbl_role_master D ON D.roleid = B.roleid
					WHERE B.isdeleted = 0 AND B.roleid <> 1 AND A.applicable_origins REGEXP '$originId'
					GROUP BY A.userid");
		} else {
			$query = $this->db->query("SELECT A.userid, A.fullname, A.contactno, 
					GROUP_CONCAT(D.rolename SEPARATOR ', ') as role, 
					getapplicableorigins_byid(A.applicable_origins) as origins, B.username, 
					A.isactive, C.fullname AS updatedby FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					INNER JOIN tbl_user_registration C ON C.userid = A.updatedby 
					INNER JOIN tbl_role_master D ON D.roleid = B.roleid
					WHERE B.isdeleted = 0 AND B.roleid <> 1 AND B.userid <> $userid AND A.applicable_origins REGEXP '$originId'
					GROUP BY A.userid");
		}
		
		return $query->result();
	}

	public function get_user_detail_by_id($id)
	{
		$query = $this->db->query("SELECT A.userid, A.fullname, A.contactno, 
					GROUP_CONCAT(D.roleid SEPARATOR ', ') as role, B.username, B.password, 
					A.emailid, A.isactive, C.fullname AS updatedby, A.address, A.applicable_origins, 
					A.profilephoto, A.default_language
					FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					INNER JOIN tbl_user_registration C ON C.userid = A.updatedby 
					INNER JOIN tbl_role_master D ON D.roleid = B.roleid
					WHERE B.isdeleted = 0 AND B.roleid <> 1 AND A.userid = $id
					GROUP BY A.userid");
		return $query->result();
	}

	public function add($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_user_registration', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function addLogin($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_login', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update($data, $id)
	{
		$this->db->where('userid', $id);
		$this->db->set('updateddate', 'NOW()', FALSE);
		if ($this->db->update('tbl_user_registration', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_user_login($id)
	{
		$this->db->where('userid', $id);
		$this->db->delete('tbl_login');
	}

	public function check_duplicate_users($username)
	{
		$query = $this->db->query("SELECT COUNT(*) as cnt FROM tbl_login WHERE username = '$username'");
		$result = $query->result();
		if ($result[0]->cnt > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_expense_ledger_users($originid) {
		$query = $this->db->query("SELECT A.userid, fullname FROM tbl_user_registration A 
					INNER JOIN tbl_login B ON B.userid = A.userid 
					WHERE B.roleid = 6 AND A.isactive = 1 AND B.isactive = 1 AND B.isdeleted = 0 
					AND A.applicable_origins LIKE '%$originid%'");
		return $query->result();
	}

	public function get_purchase_manager_users($originid) {
		$query = $this->db->query("SELECT A.userid, fullname FROM tbl_user_registration A 
		INNER JOIN tbl_login B ON B.userid = A.userid WHERE B.roleid = 5 AND A.isactive = 1 AND B.isactive = 1 AND B.isdeleted = 0 AND 
		A.applicable_origins LIKE '%$originid%'");
		return $query->result();
	}
}
