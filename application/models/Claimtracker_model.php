<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Claimtracker_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function add_claimtracker($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_claim_tracker', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_claimtracker($data, $id)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where('id', $id);
		if ($this->db->update('tbl_export_claim_tracker', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function fetch_count_tracker_exportid($exportid)
	{
		$strQuery = "SELECT MAX(SPLIT_STR(claim_reference_id, '--', 2)) + 1 AS max_claim FROM tbl_export_claim_tracker A 
			WHERE export_id = $exportid";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function fetch_claim_tracker_list($originid)
	{
		$strQuery = "SELECT A.id, A.claim_reference_id, B.sa_number, A.claim_amount, A.claim_remarks, A.is_claimed 
		FROM tbl_export_claim_tracker A 
		INNER JOIN tbl_export_container_details B ON B.id = A.export_id 
		WHERE A.is_active = 1 AND A.origin_id = $originid
		ORDER BY id DESC";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function fetch_unclaim_list($originid)
	{
		$strQuery = "SELECT id, CONCAT(claim_reference_id, ' ($', TRIM(LEADING '0' FROM claim_amount) + 0, ')') AS claim_amount 
				FROM tbl_export_claim_tracker 
				WHERE is_active = 1 AND is_claimed = 0 AND origin_id = $originid 
				ORDER BY id ASC";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function get_claim_details_byid($creditnotes)
	{
		$strQuery = "SELECT GROUP_CONCAT(claim_reference_id SEPARATOR ', ') AS claim_reference, 
			SUM(claim_amount) AS claim_amount
			FROM tbl_export_claim_tracker 
			WHERE is_active = 1 AND is_claimed = 0 AND id IN ($creditnotes) ";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function update_credit_note_claims($data, $claimid)
	{
		$multiClause = array('id' => $claimid, 'is_active' => 1, 'is_claimed' => 0);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_export_claim_tracker', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_claim_details_history_byid($creditnotes)
	{
		$strQuery = "SELECT GROUP_CONCAT(claim_reference_id SEPARATOR ', ') AS claim_reference, 
			SUM(claim_amount) AS claim_amount
			FROM tbl_export_claim_tracker 
			WHERE is_active = 1 AND is_claimed = 1 AND id IN ($creditnotes) ";

		$query = $this->db->query($strQuery);

		return $query->result();
	}
}