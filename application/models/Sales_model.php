<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Sales_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_sa_lists($origin)
    {
        $strQuery = "SELECT DISTINCT sa_no, concat(sa_no,' - ', bl_no) as sa_details FROM tbl_sales_details WHERE is_active = 1 AND origin_id = $origin ORDER BY sa_no ASC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_count_containers($containernumber, $sanumber, $originid)
	{
        $strQuery = "SELECT COUNT(*) AS cnt FROM tbl_sales_details 
            WHERE is_active = 1 AND container_number = '$containernumber' AND sa_no = '$sanumber' AND origin_id = $originid";
		$query = $this->db->query($strQuery);
		return $query->result();
	}

    public function add_sales_data($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_sales_details', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_sales_data($containernumber, $sanumber, $originid, $data)
    {
        $multiClause = array('container_number' => $containernumber, 'sa_no' => $sanumber, 'origin_id' => $originid, 'is_active' => 1);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_sales_details', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_sales_report_lists($originid, $sano)
    {
        $strQuery = "SELECT getorigin_namebyid(origin_id) AS origin, year, container_number, sa_no, bl_no, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(bl_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN bl_date ELSE DATE_FORMAT(STR_TO_DATE(bl_date, '%m/%d/%Y'), '%d/%m/%Y') END AS bl_date, 
            liner, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(eta_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN eta_date ELSE DATE_FORMAT(STR_TO_DATE(eta_date, '%m/%d/%Y'), '%d/%m/%Y') END AS eta_date, 
            product, gross_length, gross_girth, gross_volume, pieces, net_length, net_girth, 
            net_volume, base_price, rate_card, cft, pod, consignee, sales_status, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(sold_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN sold_date ELSE DATE_FORMAT(STR_TO_DATE(sold_date, '%m/%d/%Y'), '%d/%m/%Y') END AS sold_date, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(proforma_invoice, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN proforma_invoice ELSE DATE_FORMAT(STR_TO_DATE(proforma_invoice, '%m/%d/%Y'), '%d/%m/%Y') END AS proforma_invoice, 
            sales_term, usance, tt_advance, advance_percentage, sales_price, invoice_price, interest, bank_nego_value, 
            claim_value, aot_value, sales_remarks, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(phyto_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN phyto_date ELSE DATE_FORMAT(STR_TO_DATE(phyto_date, '%m/%d/%Y'), '%d/%m/%Y') END AS phyto_date, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(coo_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN coo_date ELSE DATE_FORMAT(STR_TO_DATE(coo_date, '%m/%d/%Y'), '%d/%m/%Y') END AS coo_date, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(obl_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN obl_date ELSE DATE_FORMAT(STR_TO_DATE(obl_date, '%m/%d/%Y'), '%d/%m/%Y') END AS obl_date, 
            lc_no, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(lc_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN lc_date ELSE DATE_FORMAT(STR_TO_DATE(lc_date, '%m/%d/%Y'), '%d/%m/%Y') END AS lc_date, invoice_no, 
            nego_status, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(nego_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN nego_date ELSE DATE_FORMAT(STR_TO_DATE(nego_date, '%m/%d/%Y'), '%d/%m/%Y') END AS nego_date, 
            payment_status, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(expected_accept_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN expected_accept_date ELSE DATE_FORMAT(STR_TO_DATE(expected_accept_date, '%m/%d/%Y'), '%d/%m/%Y') END AS expected_accept_date, 
            CASE WHEN DATE_FORMAT(STR_TO_DATE(received_date, '%m/%d/%Y'), '%d/%m/%Y') IS NULL THEN received_date ELSE DATE_FORMAT(STR_TO_DATE(received_date, '%m/%d/%Y'), '%d/%m/%Y') END AS received_date, report_status, remarks 
            FROM tbl_sales_details 
            WHERE is_active = 1 AND origin_id = $originid AND sa_no = '$sano'
            ORDER BY sa_no ASC";
       $query = $this->db->query($strQuery);
       return $query->result();
   }
}