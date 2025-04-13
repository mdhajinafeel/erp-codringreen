<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Origin_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all_origins()
	{
		return $this->db->get("tbl_origins");
	}

	public function all_countries()
	{
		$query = $this->db->query("SELECT * from tbl_countries");
  	  	return $query->result();
	}

	public function get_countries_info($id) {
	
		$sql = 'SELECT * FROM tbl_countries WHERE id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);
		
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function add($data){
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_origins', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
}
