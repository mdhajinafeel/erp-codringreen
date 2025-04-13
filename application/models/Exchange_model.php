<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Exchange_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //FARMS
    public function add_exchange_rate($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_exchange_rate', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_exchange_rate()
    {
        $this->db->where('is_active', 1);
        return $this->db->delete('tbl_exchange_rate'); // Replace with your actual table
    }

    public function fetch_exchange_rate_by_date($exchange_date)
    {
        $query = $this->db->query("SELECT value FROM tbl_exchange_rate WHERE exchange_date <= '$exchange_date' AND is_active = 1 ORDER BY exchange_date DESC LIMIT 1");
        return $query->result();
    }
}
