<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Expense_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function transaction_record_count()
    {
        $this->db->where("transaction_type", 1);
        $this->db->from("tbl_transaction");
        return $this->db->count_all_results();
    }

    public function add_transaction($data)
    {
        $this->db->set('expense_timestamp', 'CAST(UNIX_TIMESTAMP(NOW()) * 1000 AS unsigned)', FALSE);
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_transaction', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }
}
