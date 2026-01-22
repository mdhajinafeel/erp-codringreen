<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Forestry_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // EXTRACTIONS
    public function add_forestry_extractions($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_forestry_extractions', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function get_exist_forestry_extractions($originid, $supplierid, $contractid, $extractiondate)
    {
        return $this->db
            ->where('origin_id', $originid)
            ->where('supplier_id', $supplierid)
            ->where('contract_id', $contractid)
            ->where('extraction_date', $extractiondate)
            ->where('is_active', 1)
            ->count_all_results('tbl_forestry_extractions');
    }

    public function get_extractions_data($originId)
    {
        $strQuery = "SELECT A.id, A.supplier_id, A.contract_id, A.extraction_date, A.extraction_cost, B.contract_code, B.description, C.supplier_name, 
            COUNT(DISTINCT L.extraction_tree_id) AS total_trees, COUNT(L.log_no) AS tota_pieces, SUM(L.volume) AS total_volume, 
            COUNT(DISTINCT L.extraction_tree_id) * A.extraction_cost AS total_cost 
            FROM tbl_forestry_extractions A 
            INNER JOIN tbl_forestry_extraction_trees K ON K.extraction_id = A.id AND K.is_active = 1 
            INNER JOIN tbl_forestry_extraction_tree_details L ON L.extraction_tree_id = K.id AND L.is_active = 1
            INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
            INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
            WHERE A.origin_id = $originId AND A.is_active = 1 
            GROUP BY A.id DESC";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_extractions_details_byid($extractionId)
    {
        $strQuery = "SELECT id, supplier_id, contract_id, extraction_date, extraction_cost, origin_id FROM tbl_forestry_extractions WHERE id = $extractionId";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_extractions_data_byextractionid($extractionId)
    {
        $query = $this->db->query("SELECT id, tree_no, total_pieces, total_volume FROM tbl_forestry_extraction_trees WHERE is_active = 1 AND extraction_id = $extractionId");
        return $query->result();
    }

    public function add_forestry_extraction_trees($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_forestry_extraction_trees', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_extraction($data, $extractionId)
    {
        $multiClause = array('id' => $extractionId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_extractions', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function add_forestry_extraction_tree_details($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_forestry_extraction_tree_details', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function get_extractions_trees_summary($extractionId)
    {
        $strQuery = "SELECT B.id, B.extraction_id, tree_no, COUNT(A.log_no) AS total_pieces, SUM(A.volume) AS total_volume 
                FROM tbl_forestry_extraction_tree_details A 
                INNER JOIN tbl_forestry_extraction_trees B ON B.id = A.extraction_tree_id 
                WHERE A.extraction_id = $extractionId AND A.is_active = 1 AND B.is_active = 1 GROUP BY A.extraction_tree_id 
                ORDER BY tree_no ASC";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_extraction_tree_details_by_treeid($treeId, $extractionId)
    {
        return $this->db
            ->select('log_no, circumference, length, volume')
            ->from('tbl_forestry_extraction_tree_details')
            ->where('extraction_tree_id', $treeId)
            ->where('extraction_id', $extractionId)
            ->where('is_active', 1)
            ->order_by('log_no', 'ASC')
            ->get()
            ->result();
    }

    public function get_used_tree_numbers($extractionId)
    {
        return $this->db
            ->select('tree_no')
            ->from('tbl_forestry_extraction_trees')
            ->where('extraction_id', $extractionId)
            ->where('is_active', 1)
            ->get()
            ->result();
    }

    public function update_extraction_trees($data, $extractionId, $treeNo)
    {
        $multiClause = array('extraction_id' => $extractionId, 'tree_no' => $treeNo);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_extraction_trees', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_extraction_tree_byid($data, $extractionId, $treeId)
    {
        $multiClause = array('extraction_id' => $extractionId, 'id' => $treeId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_extraction_trees', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_extraction_tree_details($data, $extractionId, $extractionTreeId)
    {
        $multiClause = array('extraction_id' => $extractionId, 'extraction_tree_id' => $extractionTreeId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_extraction_tree_details', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_extraction_tree_details_byid($data, $extractionId, $extractionTreeId)
    {
        $multiClause = array('extraction_id' => $extractionId, 'extraction_tree_id' => $extractionTreeId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_extraction_tree_details', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_extraction_totals($extractionId)
    {
        return $this->db->select("
                COUNT(DISTINCT et.extraction_tree_id) AS total_trees,
                IFNULL(COUNT(et.log_no),0) AS total_pieces,
                IFNULL(SUM(et.volume),0) AS total_volume
            ")
            ->from('tbl_forestry_extraction_tree_details et')
            ->where('et.extraction_id', $extractionId)
            ->where('et.is_active', 1)
            ->get()
            ->row();
    }

    public function generate_extraction_report($originId, $supplierId, $contractId, $fromDate, $toDate)
    {
        $strQuery = "SELECT extraction_cost, DATE_FORMAT(STR_TO_DATE(extraction_date, '%d/%m/%Y'), '%d/%m/%Y') AS date, tree_no, supplier_name, contract_code, description,    
            MAX(CASE WHEN rn = 1 THEN circumference END) AS circ1, MAX(CASE WHEN rn = 1 THEN length END) AS len1, 
            MAX(CASE WHEN rn = 2 THEN circumference END) AS circ2, MAX(CASE WHEN rn = 2 THEN length END) AS len2, 
            MAX(CASE WHEN rn = 3 THEN circumference END) AS circ3, MAX(CASE WHEN rn = 3 THEN length END) AS len3, 
            MAX(CASE WHEN rn = 4 THEN circumference END) AS circ4, MAX(CASE WHEN rn = 4 THEN length END) AS len4, 
            MAX(CASE WHEN rn = 5 THEN circumference END) AS circ5, MAX(CASE WHEN rn = 5 THEN length END) AS len5, 
            MAX(CASE WHEN rn = 6 THEN circumference END) AS circ6, MAX(CASE WHEN rn = 6 THEN length END) AS len6, 
            MAX(CASE WHEN rn = 7 THEN circumference END) AS circ7, MAX(CASE WHEN rn = 7 THEN length END) AS len7, 
            MAX(CASE WHEN rn = 8 THEN circumference END) AS circ8, MAX(CASE WHEN rn = 8 THEN length END) AS len8, 
            MAX(CASE WHEN rn = 9 THEN circumference END) AS circ9, MAX(CASE WHEN rn = 9 THEN length END) AS len9, 
            MAX(CASE WHEN rn = 10 THEN circumference END) AS circ10, MAX(CASE WHEN rn = 10 THEN length END) AS len10,
            MAX(CASE WHEN rn = 11 THEN circumference END) AS circ11, MAX(CASE WHEN rn = 11 THEN length END) AS len11,
            MAX(CASE WHEN rn = 12 THEN circumference END) AS circ12, MAX(CASE WHEN rn = 12 THEN length END) AS len12,
            MAX(CASE WHEN rn = 13 THEN circumference END) AS circ13, MAX(CASE WHEN rn = 13 THEN length END) AS len13,
            MAX(CASE WHEN rn = 14 THEN circumference END) AS circ14, MAX(CASE WHEN rn = 14 THEN length END) AS len14,
            MAX(CASE WHEN rn = 15 THEN circumference END) AS circ15, MAX(CASE WHEN rn = 15 THEN length END) AS len15  
            FROM ( SELECT A.extraction_date, A.extraction_cost, B.id AS tree_id, B.tree_no AS tree_no, D.supplier_name, E.contract_code, E.description, C.circumference, C.length, ROW_NUMBER() OVER ( PARTITION BY A.extraction_date, B.id ORDER BY C.id ) AS rn 
            FROM tbl_forestry_extractions A 
            JOIN tbl_forestry_extraction_trees B ON B.extraction_id = A.id 
            JOIN tbl_forestry_extraction_tree_details C ON C.extraction_tree_id = B.id 
            JOIN tbl_suppliers D ON D.id = A.supplier_id 
            JOIN tbl_supplier_purchase_contract E ON E.contract_id = A.contract_id 
            WHERE A.is_active = 1 AND B.is_active = 1 AND C.is_active = 1 AND A.origin_id = $originId AND A.contract_id = $contractId AND A.supplier_id = $supplierId";

        if ($fromDate != '' && $toDate != '') {
            $strQuery .= " AND STR_TO_DATE(A.extraction_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        }

        $strQuery .= " ) x GROUP BY extraction_date, tree_id 
            ORDER BY STR_TO_DATE(extraction_date, '%d/%m/%Y'), tree_no";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    // OPERATIONAL COSTS
    public function add_opertational_costs($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_forestry_operational_costs', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_opertational_costs($data, $costingid)
    {
        $multiClause = array('id' => $costingid, 'is_active' => 1);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_operational_costs', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_operational_costing($originId, $costType)
    {
        if ($costType == 4 || $costType == 9 || $costType == 6) {
            $strQuery = "SELECT A.id, B.supplier_name, C.contract_code, C.description, A.invoice_number, A.quantity, A.amount, A.expense_date, A.expense_type 
                        FROM tbl_forestry_operational_costs A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
                        WHERE A.is_active = 1 AND A.cost_type = $costType AND A.origin_id = $originId  
                        ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
        } else if ($costType == 5) {
            $strQuery = "SELECT A.id, B.supplier_name, C.contract_code, C.description, A.invoice_number, A.sub_total, 
                        A.tax_amount, A.amount, A.expense_date, D.machine_type, D.chassis_no 
                        FROM tbl_forestry_operational_costs A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
                        LEFT JOIN tbl_master_machines D ON D.id = A.machine_type 
                        WHERE A.is_active = 1 AND A.cost_type = $costType AND A.origin_id = $originId  
                        ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
        } else if ($costType == 7 || $costType == 8) {
            $strQuery = "SELECT A.id, B.supplier_name, C.contract_code, C.description, A.invoice_number, A.sub_total, A.tax_amount, A.amount, A.expense_date 
                        FROM tbl_forestry_operational_costs A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
                        WHERE A.is_active = 1 AND A.cost_type = $costType AND A.origin_id = $originId  
                        ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
        }
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    //FORESTRY REPORTS DATA
    public function get_forestry_reports_farm_data($originId, $supplierId, $contractId, $fromDate, $toDate)
    {
        $strQuery = "SELECT A.farm_id, DATE_FORMAT(STR_TO_DATE(A.purchase_date, '%Y-%m-%d'), '%d/%m/%Y') AS purchase_date, A.inventory_order, gettotalpieces_farm(A.farm_id) AS pieces, 
            gettotalvolume_farm(A.farm_id, 0, 0, 1) AS net_volume, A.wood_value, A.service_cost, A.logistic_cost, A.loading_cost, B.supplier_name, C.contract_code, C.description 
            FROM tbl_farm A 
            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
            INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
            WHERE A.is_active = 1
            AND A.origin_id = $originId";

        if ($supplierId > 0) {
            $strQuery .= " AND A.supplier_id = $supplierId";
        }

        if ($contractId > 0) {
            $strQuery .= " AND A.contract_id = $contractId";
        }

        if ($fromDate != '' && $toDate != '') {
            $strQuery .= " AND STR_TO_DATE(A.purchase_date, '%Y-%m-%d') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        }

        $strQuery .= " ORDER BY STR_TO_DATE(A.purchase_date, '%Y-%m-%d') ASC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_forestry_extraction_report_data($originId, $supplierId, $contractId, $fromDate, $toDate)
    {
        $strQuery = "SELECT A.extraction_date, B.contract_code, B.description, C.supplier_name, 
            COUNT(DISTINCT L.extraction_tree_id) AS total_trees, COUNT(L.log_no) AS tota_pieces, SUM(L.volume) AS total_volume, 
            COUNT(DISTINCT L.extraction_tree_id) * A.extraction_cost AS extraction_cost 
            FROM tbl_forestry_extractions A 
            INNER JOIN tbl_forestry_extraction_trees K ON K.extraction_id = A.id AND K.is_active = 1 
            INNER JOIN tbl_forestry_extraction_tree_details L ON L.extraction_tree_id = K.id AND L.is_active = 1
            INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
            INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
            WHERE A.origin_id = $originId AND A.is_active = 1";

        if ($supplierId != 0) {
            $strQuery .= " AND A.supplier_id = $supplierId";
        }

        if ($contractId != 0) {
            $strQuery .= " AND A.contract_id = $contractId";
        }

        // if($fromDate != '' && $toDate != '') {
        //     $strQuery .= " AND STR_TO_DATE(A.extraction_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        // }

        $strQuery .= " GROUP BY STR_TO_DATE(A.extraction_date, '%d/%m/%Y') ASC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_forestry_operation_cost_report_data_15days($originId, $supplierId, $contractId, $fromDate, $toDate, $costType)
    {
        $conditions = [];

        $conditions[] = "cost_type = $costType";
        $conditions[] = "expense_date IS NOT NULL";
        $conditions[] = "expense_date <> ''";

        if ($supplierId != 0) {
            $conditions[] = "A.supplier_id = " . (int)$supplierId;
        }

        if ($contractId != 0) {
            $conditions[] = "A.contract_id = " . (int)$contractId;
        }

        // if ($fromDate != '' && $toDate != '') {
        //     $conditions[] = "
        //     STR_TO_DATE(A.expense_date, '%d/%m/%Y')
        //     BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y')
        //         AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        // }

        $whereSql = implode(" AND ", $conditions);

        $sql = "
        SELECT
            YEAR(d)  AS yr,
            MONTH(d) AS mon,

            -- Period start
            CASE
                WHEN DAY(d) <= 15
                    THEN DATE_FORMAT(DATE(d) - INTERVAL (DAY(d) - 1) DAY, '%d/%m/%Y')
                ELSE
                    DATE_FORMAT(DATE(d) - INTERVAL (DAY(d) - 16) DAY, '%d/%m/%Y')
            END AS start_date,

            -- Period end
            CASE
                WHEN DAY(d) <= 15
                    THEN DATE_FORMAT(
                        DATE(d) - INTERVAL (DAY(d) - 1) DAY + INTERVAL 14 DAY,
                        '%d/%m/%Y'
                    )
                ELSE
                    DATE_FORMAT(LAST_DAY(d), '%d/%m/%Y')
            END AS end_date,

            SUM(quantity) AS total_quantity,
            SUM(amount)   AS total_amount, 
            supplier_name, 
            contract_code, 
			description
        FROM (
            SELECT
                STR_TO_DATE(expense_date, '%d/%m/%Y') AS d,
                quantity,
                amount, B.supplier_name, C.contract_code, C.description 
            FROM tbl_forestry_operational_costs A 
            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
            INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
            WHERE A.origin_id = $originId AND $whereSql 
        ) t

        GROUP BY
            YEAR(d),
            MONTH(d),
            CASE WHEN DAY(d) <= 15 THEN 1 ELSE 2 END

        ORDER BY yr, mon";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_forestry_operation_cost_report_data_week($originId, $supplierId, $contractId, $fromDate, $toDate, $costType)
    {
        $conditions = [];

        $conditions[] = "A.cost_type = $costType";
        $conditions[] = "A.expense_date IS NOT NULL";
        $conditions[] = "A.expense_date <> ''";

        if ($supplierId != 0) {
            $conditions[] = "A.supplier_id = " . (int)$supplierId;
        }

        if ($contractId != 0) {
            $conditions[] = "A.contract_id = " . (int)$contractId;
        }

        // Optional date filter
        /*
        if ($fromDate != '' && $toDate != '') {
            $conditions[] = "
                STR_TO_DATE(A.expense_date, '%d/%m/%Y')
                BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y')
                    AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        }
        */

        $whereSql = implode(" AND ", $conditions);

        $sql = "
        SELECT
            YEAR(d) AS yr,
            WEEK(d, 1) AS week_no,

            DATE_FORMAT(
                DATE_SUB(d, INTERVAL WEEKDAY(d) DAY),
                '%d/%m/%Y'
            ) AS start_date,

            DATE_FORMAT(
                DATE_ADD(DATE_SUB(d, INTERVAL WEEKDAY(d) DAY), INTERVAL 6 DAY),
                '%d/%m/%Y'
            ) AS end_date,

            SUM(quantity) AS total_quantity,
            SUM(amount) AS total_amount,
            supplier_name,
            contract_code,
            description

        FROM (
            SELECT
                STR_TO_DATE(A.expense_date, '%d/%m/%Y') AS d,
                A.quantity,
                A.amount,
                B.supplier_name,
                C.contract_code,
                C.description
            FROM tbl_forestry_operational_costs A
            INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
            INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id
            WHERE A.origin_id = $originId
            AND $whereSql
        ) t

        GROUP BY
            YEARWEEK(d, 1),
            supplier_name,
            contract_code,
            description

        ORDER BY
            YEAR(d),
            WEEK(d, 1)
        ";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_forestry_operation_report_data($originId, $supplierId, $contractId, $fromDate, $toDate, $costType)
    {
        $strQuery = "SELECT A.expense_date, B.supplier_name, C.contract_code, C.description, SUM(A.quantity) AS quantity, SUM(A.amount) AS amount 
                    FROM tbl_forestry_operational_costs A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id
                    WHERE A.cost_type = $costType AND A.is_active = 1 AND A.origin_id = $originId";

        if ($supplierId != 0) {
            $strQuery .= " AND A.supplier_id = $supplierId";
        }

        if ($contractId != 0) {
            $strQuery .= " AND A.contract_id = $contractId";
        }

        if($costType == 4 || $costType == 9) {
            $strQuery .= " AND A.expense_type = 0";
        }

        // if($fromDate != '' && $toDate != '') {
        //     $strQuery .= " AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        // }

        $strQuery .= " GROUP BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function update_operational_cost($data, $costingid, $costType)
    {
        $multiClause = array('id' => $costingid, "cost_type" => $costType, 'is_active' => 1);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_forestry_operational_costs', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_operational_cost_details_byid($originId, $costType, $costingId)
    {
        $strQuery = "SELECT id, supplier_id, contract_id, purchaser_id, invoice_number, quantity, sub_total, tax_amount, amount, 
            expense_date, remarks, origin_id, machine_type, expense_type, clock_start, clock_end    
            FROM tbl_forestry_operational_costs WHERE is_active = 1 AND origin_id = $originId AND cost_type = $costType AND id = $costingId";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_operations_report_data($costType, $originId, $supplierId, $contractId, $fromDate, $toDate)
    {
        $strQuery = "SELECT B.supplier_name, C.contract_code, C.description, D.purchaser_name, D.company_id, A.invoice_number, 
                A.expense_date, A.quantity, A.sub_total, A.tax_amount, A.amount, A.remarks, A.expense_type, E.machine_type, E.chassis_no  
                FROM tbl_forestry_operational_costs A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
                LEFT JOIN tbl_farm_costing_purchasers D ON D.id = A.purchaser_id 
                LEFT JOIN tbl_master_machines E ON E.id = A.machine_type 
                WHERE A.origin_id = $originId AND A.is_active = 1 AND A.cost_type = $costType";

        if ($supplierId != 0) {
            $strQuery .= " AND A.supplier_id = $supplierId";
        }

        if ($contractId != 0) {
            $strQuery .= " AND A.contract_id = $contractId";
        }

        if ($fromDate != '' && $toDate != '') {
            $strQuery .= " AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }
}
