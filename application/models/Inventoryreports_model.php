<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Inventoryreports_model extends CI_Model
{
    public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
    
    //MISSING INVENTORY
    public function fetch_missing_inventory_farm($originid)
    {
        if($originid == 0) {
            $query = $this->db->query("SELECT B.supplier_name, B.supplier_code, C.product_name, D.product_type_name, inventory_order, 
                            CASE WHEN (A.product_type_id = 1 OR A.product_type_id = 3) THEN gettotalscannedcode_farm(A.farm_id) ELSE gettotalpieces_farm(A.farm_id) END as scanned_pcs, 
                            DATE_FORMAT(A.purchase_date, '%d/%m/%Y') as receiveddate,
                            E.fullname AS uploadedby, 
                            getapplicableorigins_byid(A.origin_id) AS origin
                            FROM tbl_farm A 
                            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                            INNER JOIN tbl_product_master C ON C.product_id = A.product_id 
                            INNER JOIN tbl_product_types D ON D.type_id = A.product_type_id 
                            INNER JOIN tbl_user_registration E ON E.userid = A.created_by 
                            WHERE A.is_active = 1 
                            AND A.inventory_order NOT IN (SELECT salvoconducto FROM tbl_reception 
                            WHERE isactive = 1 AND (isduplicatecaptured = 0 OR isduplicatecaptured IS NULL)) 
                            GROUP BY A.inventory_order ORDER BY DATE_FORMAT(A.purchase_date, '%d-%m-%Y') DESC");
        } else {
            $query = $this->db->query("SELECT B.supplier_name, B.supplier_code, C.product_name, D.product_type_name, inventory_order, 
                            CASE WHEN (A.product_type_id = 1 OR A.product_type_id = 3) THEN gettotalscannedcode_farm(A.farm_id) ELSE gettotalpieces_farm(A.farm_id) END as scanned_pcs, 
                            DATE_FORMAT(A.purchase_date, '%d/%m/%Y') as receiveddate, 
                            E.fullname AS uploadedby,
                            getapplicableorigins_byid(A.origin_id) AS origin
                            FROM tbl_farm A 
                            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                            INNER JOIN tbl_product_master C ON C.product_id = A.product_id 
                            INNER JOIN tbl_product_types D ON D.type_id = A.product_type_id 
                            INNER JOIN tbl_user_registration E ON E.userid = A.created_by 
                            WHERE A.is_active = 1 AND A.origin_id = $originid
                            AND A.inventory_order NOT IN (SELECT salvoconducto FROM tbl_reception 
                            WHERE isactive = 1 AND (isduplicatecaptured = 0 OR isduplicatecaptured IS NULL)) 
                            GROUP BY A.inventory_order ORDER BY DATE_FORMAT(A.purchase_date, '%d-%m-%Y') DESC");
        }
        
        return $query->result();
    }

    public function fetch_missing_inventory_reception($originid)
    {
        if($originid == 0) {
            $query = $this->db->query("SELECT S.supplier_name, S.supplier_code, S.product_name, S.product_type_name, S.salvoconducto, 
                            S.scanned_pcs, S.received_date, S.uploadedby, S.origin 
                            FROM (SELECT B.supplier_name, B.supplier_code, D.product_name, E.product_type_name, A.salvoconducto, 
                            CASE WHEN A.is_special_uploaded = 1 THEN getreceptioncount_byId_special(A.reception_id) 
                            ELSE getreceptioncount_byId(A.reception_id) END AS scanned_pcs, 
                            DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'), '%d/%m/%Y') as received_date, 
                            F.fullname AS uploadedby, getapplicableorigins_byid(A.origin_id) AS origin FROM tbl_reception A 
                            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                            INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                            INNER JOIN tbl_product_master D ON D.product_id = C.product_name 
                            INNER JOIN tbl_product_types E ON E.type_id = A.supplier_product_typeid 
                            INNER JOIN tbl_user_registration F ON F.userid = A.createdby 
                            WHERE A.isactive = 1 AND (A.isduplicatecaptured = 0 OR A.isduplicatecaptured IS NULL)
                            GROUP BY A.salvoconducto 
                            ORDER BY DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'), '%Y-%m-%d') DESC) S 
                            WHERE S.scanned_pcs = 0 OR S.salvoconducto NOT IN (SELECT inventory_order FROM tbl_farm C WHERE C.is_active = 1)");
        } else {
            $query = $this->db->query("SELECT S.supplier_name, S.supplier_code, S.product_name, S.product_type_name, S.salvoconducto, 
                            S.scanned_pcs, S.received_date, S.uploadedby, S.origin 
                            FROM (SELECT B.supplier_name, B.supplier_code, D.product_name, E.product_type_name, A.salvoconducto, 
                            CASE WHEN A.is_special_uploaded = 1 THEN getreceptioncount_byId_special(A.reception_id) 
                            ELSE getreceptioncount_byId(A.reception_id) END AS scanned_pcs, 
                            DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'), '%d/%m/%Y') as received_date, 
                            F.fullname AS uploadedby, getapplicableorigins_byid(A.origin_id) AS origin FROM tbl_reception A 
                            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                            INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                            INNER JOIN tbl_product_master D ON D.product_id = C.product_name 
                            INNER JOIN tbl_product_types E ON E.type_id = A.supplier_product_typeid 
                            INNER JOIN tbl_user_registration F ON F.userid = A.createdby 
                            WHERE A.isactive = 1 AND A.origin_id = $originid AND (A.isduplicatecaptured = 0 OR A.isduplicatecaptured IS NULL)
                            GROUP BY A.salvoconducto 
                            ORDER BY DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'), '%Y-%m-%d') DESC) S 
                            WHERE S.scanned_pcs = 0 OR S.salvoconducto NOT IN (SELECT inventory_order FROM tbl_farm C WHERE C.is_active = 1)");
        }
        
        return $query->result();
    }
}