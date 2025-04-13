<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Contract_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //CONTRACTS
    public function all_contracts()
    {
        $query = $this->db->query("SELECT contract_id, contract_type, supplier_name, 
                                contract_code, purchase_unit, total_volume, remaining_volume, 
                                is_expired, getapplicableorigins_byid(origin_id) as origin, is_active
                                FROM v_fetch_contracts
                                ORDER BY contract_id");
        return $query->result();
    }

    public function all_contracts_origin($originid)
    {
        $query = $this->db->query("SELECT contract_id, contract_type, supplier_name, 
                        contract_code, purchase_unit, total_volume, remaining_volume, 
                        is_expired, getapplicableorigins_byid(origin_id) as origin, is_active
                        FROM v_fetch_contracts
                        WHERE origin_id = $originid ORDER BY contract_id");
        return $query->result();
    }

    public function get_contracts_reports($originid)
    {
        $query = $this->db->query("SELECT A.contract_id, A.contract_type, CASE WHEN A.contract_type = 1 THEN B.supplier_name ELSE K.fullname END as supplier_name, CASE WHEN A.contract_type = 1 THEN B.supplier_id ELSE '-' END AS supplier_id, CASE WHEN A.contract_type = 1 THEN B.supplier_code ELSE '-' END AS supplier_code,
                        A.contract_code,
                        C.purchase_unit,
                        A.total_volume, A.remaining_volume, A.is_expired, G.product_name, 
                        H.product_type_name, CONCAT(D.currency_name , ' - ', D.currency_code) as currency, 
                        A.purchase_allowance, A.purchase_allowance_length, E.payment_name, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.start_date, '%d/%m/%Y'), '%d %M %Y')) as start_date, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.end_date, '%d/%m/%Y'), '%d %M %Y')) as end_date, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.start_date, '%d/%m/%Y'), '%M')) as start_date_month, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.end_date, '%d/%m/%Y'), '%M')) as end_date_month, 
                        getapplicableorigins_byid(A.origin_id) as origin, A.is_active  
                        FROM tbl_supplier_purchase_contract A
                        LEFT JOIN tbl_suppliers B ON B.id = A.supplier_id
                        INNER JOIN tbl_purchase_unit C ON C.id = A.unit_of_purchase
                        INNER JOIN tbl_currency D ON D.id = A.currency
                        INNER JOIN tbl_payment_method E ON E.id = A.payment_method
                        INNER JOIN tbl_product_master G ON G.product_id = A.product 
                        INNER JOIN tbl_product_types H ON H.type_id = A.product_type 
                        LEFT JOIN tbl_user_registration K ON K.userid = A.supplier_id
                        WHERE A.origin_id IN ($originid) ORDER BY A.contract_id");
        return $query->result();
    }

    public function get_suppliers_by_origin($originid)
    {
        $query = $this->db->query("SELECT id, CONCAT(supplier_name,' --- ', supplier_code) as supplier_name
                FROM tbl_suppliers WHERE isactive = 1 AND origin_id = $originid ORDER BY id");
        return $query->result();
    }

    public function get_purchase_manager_by_origin($originid)
    {
        $query = $this->db->query("SELECT A.userid as id, A.fullname as supplier_name FROM tbl_user_registration A 
                        INNER JOIN tbl_login B ON B.userid = A.userid 
                        WHERE B.roleid = 5 AND A.isactive = 1 AND B.isactive = 1 AND FIND_IN_SET($originid, A.applicable_origins) 
                        ORDER BY A.userid");
        return $query->result();
    }

    public function get_supplier_product_byorigin($originid, $supplierid)
    {
        $query = $this->db->query("SELECT A.product_name as product_id, B.product_name FROM tbl_suppliers_products A 
                INNER JOIN tbl_product_master B ON B.product_id = A.product_name
                WHERE supplier_id = '$supplierid' AND B.origin_id = $originid AND A.is_active = 1 AND B.isactive = 1 ORDER BY A.product_name");
        return $query->result();
    }

    public function get_product_by_origin($originid)
    {
        $query = $this->db->query("SELECT B.product_id, B.product_name 
                FROM tbl_product_master B
                WHERE B.isactive = 1 AND B.origin_id = $originid ORDER BY B.product_id");
        return $query->result();
    }

    public function get_supplier_product_type_byorigin($supplierid, $productid)
    {
        $query = $this->db->query("SELECT C.type_id, C.product_type_name FROM tbl_suppliers_product_type A 
                            INNER JOIN tbl_suppliers_products B ON B.product_id = A.product_id
                            INNER JOIN tbl_product_types C ON C.option_id = A.product_type_id
                            WHERE A.supplier_id = $supplierid AND A.is_active = 1 AND B.product_name = $productid ORDER BY C.type_id");
        return $query->result();
    }

    public function get_purchase_unit($producttypeid)
    {

        if ($producttypeid == 3) {
            $producttypeid = 1;
        } else if ($producttypeid == 4) {
            $producttypeid = 2;
        }

        $query = $this->db->query("SELECT id, purchase_unit FROM tbl_purchase_unit WHERE type_id = $producttypeid AND is_active = 1 ORDER BY id");
        return $query->result();
    }
    
    public function get_purchase_unit_origin($producttypeid, $originid)
    {

        if ($producttypeid == 3) {
            $producttypeid = 1;
        } else if ($producttypeid == 4) {
            $producttypeid = 2;
        }
        
        if($originid == 3 || $originid == 4) {
            $query = $this->db->query("SELECT id, purchase_unit FROM tbl_purchase_unit WHERE type_id = $producttypeid AND is_active = 1 AND origin_id = $originid ORDER BY id");
        } else {
            $query = $this->db->query("SELECT id, purchase_unit FROM tbl_purchase_unit WHERE type_id = $producttypeid AND is_active = 1 AND origin_id = 0 ORDER BY id");
        }

        
        return $query->result();
    }

    public function get_currencies_by_origin($originid)
    {
        $query = $this->db->query("SELECT currency_id, currency FROM (SELECT id AS currency_id, CONCAT(currency_name, ' - ', currency_code) AS currency FROM tbl_currency WHERE is_active = 1 AND is_default = 1
                        UNION ALL
                        SELECT A.currency_id, CONCAT(B.currency_name, ' - ', B.currency_code) AS currency FROM tbl_origin_currencies A 
                        INNER JOIN tbl_currency B ON B.id = A.currency_id WHERE A.is_active = 1 AND A.origin_id = $originid) A ORDER BY A.currency_id");
        return $query->result();
    }

    public function get_payment_methods()
    {
        $query = $this->db->query("SELECT id, payment_name FROM tbl_payment_method WHERE is_active = 1 ORDER BY id");
        return $query->result();
    }

    public function get_last_contract_code($originid)
    {
        $query = $this->db->query("SELECT MAX(SPLIT_STR(contract_code, '/', 4)) + 1 as tid
                FROM tbl_supplier_purchase_contract WHERE origin_id = $originid ORDER BY contract_code DESC LIMIT 1");
        return $query->result();
    }

    public function add_purchase_contract($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_supplier_purchase_contract', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_purchase_contract_price($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_supplier_contract_price', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function get_contracts_by_contractid($contractid)
    {
        $query = $this->db->query("SELECT A.contract_id, A.contract_type, A.supplier_id, 
                        A.contract_code, A.product, A.product_type, A.purchase_allowance, A.purchase_allowance_length, 
                        A.unit_of_purchase, A.currency, A.payment_method, A.start_date, A.end_date, A.total_volume, A.remaining_volume, 
                        A.is_expired, A.is_active, A.origin_id, gettotalvolume_bycontractid(A.contract_id) as mapping_volume, 
                        A.description
                        FROM tbl_supplier_purchase_contract A
                        WHERE A.contract_id = $contractid");
        return $query->result();
    }

    public function get_purchase_contract_price_by_contractid($contractid)
    {
        $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3, pricerange_grade_semi, pricerange_grade_longs
                        FROM tbl_supplier_contract_price
                        WHERE supplier_id = $contractid AND is_active = 1");
        return $query->result();
    }

    public function update_purchase_contract($data, $contractid, $contractcode)
    {
        $multiClause = array('contract_id' => $contractid, 'contract_code' => $contractcode);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_supplier_purchase_contract', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_purchase_contract_price($data, $contractid)
    {
        $multiClause = array('supplier_id' => $contractid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_supplier_contract_price', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_total_volume_by_supplier_contract($contractid, $supplierid)
    {
        $query = $this->db->query("SELECT SUM(total_volume) as total_volume FROM tbl_contract_inventory_mapping 
									WHERE contract_id = $contractid AND supplier_id = '$supplierid' 
									AND is_active = 1");
        return $query->result();
    }

    public function get_contracts_by_id($contractid)
    {
        $query = $this->db->query("SELECT A.contract_id, A.contract_type, CASE WHEN A.contract_type = 1 THEN B.supplier_name ELSE K.fullname END as supplier_name, CASE WHEN A.contract_type = 1 THEN B.supplier_id ELSE '-' END AS supplier_id, 
                        A.contract_code,
                        C.purchase_unit, A.product_type, A.product, 
                        A.total_volume, A.remaining_volume, A.is_expired, G.product_name, 
                        H.product_type_name, CONCAT(D.currency_name , ' - ', D.currency_code) as currency, 
                        A.purchase_allowance, A.purchase_allowance_length, E.payment_name, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.start_date, '%d/%m/%Y'), '%d %M %Y')) as start_date, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.end_date, '%d/%m/%Y'), '%d %M %Y')) as end_date, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.start_date, '%d/%m/%Y'), '%M')) as start_date_month, 
                        LOWER(DATE_FORMAT(STR_TO_DATE(A.end_date, '%d/%m/%Y'), '%M')) as end_date_month, 
                        getapplicableorigins_byid(A.origin_id) as origin, A.is_active, D.currency_abbreviation, 
                        A.description    
                        FROM tbl_supplier_purchase_contract A
                        LEFT JOIN tbl_suppliers B ON B.id = A.supplier_id
                        INNER JOIN tbl_purchase_unit C ON C.id = A.unit_of_purchase
                        INNER JOIN tbl_currency D ON D.id = A.currency
                        INNER JOIN tbl_payment_method E ON E.id = A.payment_method
                        INNER JOIN tbl_product_master G ON G.product_id = A.product 
                        INNER JOIN tbl_product_types H ON H.type_id = A.product_type 
                        LEFT JOIN tbl_user_registration K ON K.userid = A.supplier_id
                        WHERE A.contract_id = $contractid");
        return $query->result();
    }

    public function update_purchase_contract_volume($data, $contractid, $supplierid)
    {
        $multiClause = array('contract_id' => $contractid, 'supplier_id' => $supplierid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_supplier_purchase_contract', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_suppliers_by_contract_origin($originid, $contractid)
    {
        $query = $this->db->query("SELECT DISTINCT B.id, A.contract_type, CASE WHEN A.contract_type = 1 THEN B.supplier_name ELSE C.fullname END AS supplier_name, 
                CASE WHEN A.contract_type = 1 THEN B.supplier_id ELSE '' END AS supplier_id FROM tbl_supplier_purchase_contract A 
                LEFT JOIN tbl_user_registration C ON C.userid = A.supplier_id 
                LEFT JOIN tbl_suppliers B ON B.id = A.supplier_id
                WHERE contract_id = $contractid AND A.is_active = 1 AND A.origin_id = $originid 
                
                UNION
                
                SELECT DISTINCT C.supplier_id as id, 1, D.supplier_name, D.supplier_id  FROM tbl_inventory_ledger C 
                INNER JOIN tbl_suppliers D ON D.id = C.supplier_id
                WHERE contract_id = $contractid AND expense_type > 1");
        return $query->result();
    }

    public function fetch_purchase_contract_by_type($originid, $contracttype)
    {
        $query = $this->db->query("SELECT contract_id, contract_code, CASE WHEN contract_type = 1 THEN 'Warehouse' ELSE 'Field Purchase' END AS contract_type 
                FROM tbl_supplier_purchase_contract WHERE is_active = 1 AND origin_id = $originid AND contract_type = $contracttype");
        return $query->result();
    }
    
    public function fetch_purchase_contract_origin($originid) {
        $query = $this->db->query("SELECT contract_id, supplier_id, contract_code, product, product_type, 
                B.purchase_unit, A.unit_of_purchase AS purchase_unit_id, 
                CONCAT(C.currency_name, ' (', C.currency_code,')') AS currency, A.purchase_allowance, A.purchase_allowance_length, A.description 
                FROM tbl_supplier_purchase_contract A 
                INNER JOIN tbl_purchase_unit B ON B.id = A.unit_of_purchase
                INNER JOIN tbl_currency C ON C.id = A.currency 
                WHERE A.origin_id = $originid AND A.is_active = 1 ORDER BY A.contract_id");
        return $query->result();
    }
}
