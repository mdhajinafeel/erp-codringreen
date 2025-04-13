<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Settings_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	public function get_language_info($code)
	{
		$sql = 'SELECT * FROM tbl_languages WHERE language_name = ?';
		$binds = array($code);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function read_setting_info($id)
	{
		$sql = 'SELECT * FROM tbl_system_settings WHERE id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function site_title()
	{
		$system = $this->read_setting_info(1);
		return $system[0]->application_name;
	}

	public function all_languages()
	{
	     $sql = 'SELECT * FROM tbl_languages WHERE is_active = ? order by id asc';
		 $binds = array(1);
		 $query = $this->db->query($sql, $binds); 
		 
  	  	  return $query->result();
	}

	public function all_timezones()
	{
	     $sql = 'SELECT * FROM tbl_master_timezones order by id asc';
		 $query = $this->db->query($sql); 
  	  	return $query->result();
	}

	public function update_timezones($data, $id)
	{
		$this->db->where('id', $id);
		if ($this->db->update('tbl_master_timezones', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//COMPANY SETTINGS
	public function read_company_setting($originid)
	{
		$sql = 'SELECT * FROM tbl_origin_company_settings WHERE origin_id = ?';
		$binds = array($originid);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}
}
