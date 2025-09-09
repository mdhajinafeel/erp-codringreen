<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Costing_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function fetch_machines_masters($originId, $supplierId)
	{
		$strQuery = "SELECT id, CONCAT(machine_type, ' --- ', chassis_no) AS machine_type FROM tbl_master_machines 
			WHERE is_active = 1 AND supplier_id = $supplierId AND origin_id = $originId ORDER BY id ASC";

		$query = $this->db->query($strQuery);
		return $query->result();
	}

	//SAVE FARM COSTINGS
	public function add_farm_costing($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_farm_costings', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_farm_costing($data, $costingid)
    {
        $multiClause = array('id' => $costingid, 'is_active' => 1);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_farm_costings', $data)) {
            return true;
        } else {
            return false;
        }
    }

	public function get_farm_costing($originId, $costType)
	{
		if ($costType == 1) {
			$strQuery = "SELECT A.id, cost_type, machine_type, UCASE(concept) AS concept, quantity, amount, expense_date, expense_type, remarks, 
						B.supplier_name, B.supplier_code
						FROM tbl_farm_costings A
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						WHERE is_active = 1 AND cost_type = $costType AND A.origin_id = $originId 
						ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
		} else if ($costType == 4) {
			$strQuery = "SELECT A.id, cost_type, machine_type, UCASE(concept) AS concept, quantity, amount, expense_date, expense_type, remarks, 
						B.supplier_name, B.supplier_code, 
						CASE WHEN C.purchaser_name IS NULL THEN '--' ELSE C.purchaser_name END AS purchaser_name,
						CASE WHEN C.company_id IS NULL THEN '--' ELSE C.company_id END AS company_id,
						CASE WHEN A.invoice_number IS NULL OR A.invoice_number = '' THEN '--' ELSE A.invoice_number END AS invoice_number
						FROM tbl_farm_costings A
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						LEFT JOIN tbl_farm_costing_purchasers C ON C.id = A.purchaser_id 
						WHERE A.is_active = 1 AND cost_type = $costType AND A.origin_id = $originId 
						ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
		} else if ($costType == 5) {
			$strQuery = "SELECT A.id, A.cost_type, A.machine_type, UCASE(A.concept) AS concept, A.amount, A.expense_date, A.remarks, B.machine_type AS machine_name, 
						B.chassis_no, C.supplier_name, C.supplier_code, 
						CASE WHEN D.purchaser_name IS NULL THEN '--' ELSE D.purchaser_name END AS purchaser_name,
						CASE WHEN D.company_id IS NULL THEN '--' ELSE D.company_id END AS company_id,
						CASE WHEN A.invoice_number IS NULL OR A.invoice_number = '' THEN '--' ELSE A.invoice_number END AS invoice_number 
						FROM tbl_farm_costings A 
						INNER JOIN tbl_master_machines B ON B.id = A.machine_type 
						INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
						LEFT JOIN tbl_farm_costing_purchasers D ON D.id = A.purchaser_id 
						WHERE A.is_active = 1 AND A.cost_type = $costType AND A.origin_id = $originId 
						ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
		} else if ($costType == 6) {
			$strQuery = "SELECT A.id, cost_type, machine_type, UCASE(concept) AS concept, quantity, amount, expense_date, expense_type, remarks, 
						B.supplier_name, B.supplier_code, 
						CASE WHEN C.purchaser_name IS NULL THEN '--' ELSE C.purchaser_name END AS purchaser_name,
						CASE WHEN C.company_id IS NULL THEN '--' ELSE C.company_id END AS company_id,
						CASE WHEN A.invoice_number IS NULL OR A.invoice_number = '' THEN '--' ELSE A.invoice_number END AS invoice_number
						FROM tbl_farm_costings A
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						LEFT JOIN tbl_farm_costing_purchasers C ON C.id = A.purchaser_id 
						WHERE A.is_active = 1 AND cost_type = $costType AND A.origin_id = $originId 
						ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') DESC";
		}
		$query = $this->db->query($strQuery);
		return $query->result();
	}

	public function get_suppliers_by_origin($originid)
	{
		$query = $this->db->query("SELECT id, CONCAT(supplier_name,' --- ', supplier_code) as supplier_name
                FROM tbl_suppliers WHERE isactive = 1 AND origin_id = $originid ORDER BY id");
		return $query->result();
	}

	public function get_costingpurchasers_by_origin($originid, $costingtype)
	{
		$query = $this->db->query("SELECT id, CONCAT(purchaser_name,' --- ', company_id) as purchaser_name
                FROM tbl_farm_costing_purchasers WHERE is_active = 1 AND origin_id = $originid AND FIND_IN_SET($costingtype, costing_type) ORDER BY id");
		return $query->result();
	}

	// public function get_expense_summary_data($originId)
	// {
	// 	$sql_months = "SELECT DISTINCT DATE_FORMAT(STR_TO_DATE(expense_date, '%d/%m/%Y'), '%M') as month_name, MONTH(STR_TO_DATE(expense_date, '%d/%m/%Y')) as month_num
	//     		FROM tbl_farm_costings WHERE origin_id = $originId ORDER BY month_num";
	// 	$months = $this->db->query($sql_months)->result_array();

	// 	$caseColumns = [];
	// 	foreach ($months as $m) {
	// 		$caseColumns[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
	//                         THEN A.amount ELSE 0 END) AS `{$m['month_name']}`";
	// 	}

	// 	$caseSql = implode(", ", $caseColumns);

	// 	$sql = "SELECT B.costing_name, $caseSql FROM tbl_farm_costings A 
	// 		INNER JOIN tbl_master_farm_costings B ON B.id = A.cost_type
	//     	WHERE A.is_active = 1 AND A.origin_id = $originId AND A.expense_type = 0 GROUP BY B.costing_name";

	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	public function get_expense_summary_data($originId, $year, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings A
            WHERE A.origin_id = " . $this->db->escape($originId) . " 
				 $supplierFilter	
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
            UNION
            SELECT A.purchase_date AS d 
            FROM tbl_farm A
            WHERE A.origin_id = " . $this->db->escape($originId) . " 
			  $supplierFilter
              AND YEAR(A.purchase_date) = " . $this->db->escape($year) . "
        ) x
        ORDER BY year_num, month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$caseColumnsService = [];
		$caseColumnsLogistics = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsService[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
			AND YEAR(purchase_date) = {$m['year_num']}
            THEN service_cost ELSE 0 END) AS `{$alias}`";
			$caseColumnsLogistics[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
			AND YEAR(purchase_date) = {$m['year_num']}
            THEN logistic_cost ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlService = implode(", ", $caseColumnsService);
		$caseSqlLogistics = implode(", ", $caseColumnsLogistics);

		$caseColumnsTotalVolume = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsTotalVolume[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']}
            THEN A.total_volume ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlTotalVolume = implode(", ", $caseColumnsTotalVolume);

		$finalSql = "SELECT B.id AS cost_type_id, B.costing_name AS costing_type, $caseSqlCosting
        FROM tbl_farm_costings A
        INNER JOIN tbl_master_farm_costings B ON B.id = A.cost_type
        WHERE A.is_active = 1 AND (A.expense_date IS NOT NULL OR A.expense_date != '')
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND A.expense_type = 0 
		  $supplierFilter 
		  AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
        GROUP BY B.costing_name

        UNION ALL

        -- Service row
        SELECT 3 AS cost_type_id, 'zona' AS costing_type, $caseSqlService
        FROM tbl_farm A
        WHERE A.is_active = 1
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND YEAR(A.purchase_date) = " . $this->db->escape($year) . "
          $supplierFilter

        UNION ALL

        -- Logistics row
        SELECT 2 AS cost_type_id, 'loading' AS costing_type, $caseSqlLogistics
        FROM tbl_farm A
        WHERE A.is_active = 1
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND YEAR(A.purchase_date) = " . $this->db->escape($year) . "
          $supplierFilter

		 UNION ALL

        -- Total Volume row
        SELECT 0 AS cost_type_id, 'total_volume' AS costing_type, $caseSqlTotalVolume
        FROM tbl_farm A 
		INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
        WHERE A.is_active = 1 AND B.is_saw_mill = 0
          AND A.origin_id = " . $this->db->escape($originId) . "
		  $supplierFilter 
          AND YEAR(A.purchase_date) = " . $this->db->escape($year) . "";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.cost_type_id";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_total_ica($originId, $year)
	{
		$sql = "SELECT COUNT(DISTINCT REPLACE(REPLACE(REPLACE(REPLACE(A.inventory_order, 'L6M', ''), 'L', ''), 'A', ''), 'R', '')) AS total_ica FROM tbl_farm A
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
				WHERE A.is_active = 1 AND A.origin_id = $originId
				AND B.is_saw_mill = 0 AND YEAR(A.purchase_date) = $year";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_total_volume($originId, $year, $supplierId)
	{

		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT SUM(A.total_volume) AS total_volume FROM tbl_farm A 
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND B.is_saw_mill = 0 
				AND YEAR(A.purchase_date) = $year $supplierFilter";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_costing_detail_byid($id, $costType, $originId) {
		$sql = "SELECT id, cost_type, supplier_id, purchaser_id, invoice_number, machine_type, concept, 
				quantity, sub_total, tax_amount, amount, expense_date, remarks, expense_type 
				FROM tbl_farm_costings 
				WHERE is_active = 1 AND origin_id = $originId AND cost_type = $costType AND id = $id";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_acpm_summary_data($originId, $expenseType, $supplierId)
	{
		if($supplierId > 0) {
			$sqlQuery = "SELECT A.expense_date, B.supplier_name, C.purchaser_name, A.invoice_number, A.quantity, A.amount, A.remarks, A.expense_type 
			FROM tbl_farm_costings A 
			INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
			LEFT JOIN tbl_farm_costing_purchasers C ON C.id = A.purchaser_id 
			WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = 4 AND A.expense_type = $expenseType
			AND A.supplier_id = $supplierId
			ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";
		} else {
			$sqlQuery = "SELECT A.expense_date, B.supplier_name, C.purchaser_name, A.invoice_number, A.quantity, A.amount, A.remarks, A.expense_type 
			FROM tbl_farm_costings A 
			INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
			LEFT JOIN tbl_farm_costing_purchasers C ON C.id = A.purchaser_id 
			WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = 4 AND A.expense_type = $expenseType
			ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";
		}
		// $sqlQuery = "SELECT A.expense_date, B.supplier_name, C.purchaser_name, A.invoice_number, A.quantity, A.amount, A.remarks, A.expense_type 
		// 	FROM tbl_farm_costings A 
		// 	INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
		// 	LEFT JOIN tbl_farm_costing_purchasers C ON C.id = A.purchaser_id 
		// 	WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = 4 AND A.expense_type = $expenseType
		// 	ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";

		return $this->db->query($sqlQuery)->result();
	}

	public function get_extraction_cost() {
		$sqlQuery = "SELECT extraction_cost_farm FROM tbl_sawmill_costing WHERE is_active = 1 LIMIT 1";
		return $this->db->query($sqlQuery)->result();
	}

	public function get_expense_summary_data_by_date($originId, $fromDate, $toDate, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
				FROM (
					SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
					FROM tbl_farm_costings A
					WHERE A.origin_id = " . $this->db->escape($originId) . " 
						$supplierFilter
						AND STR_TO_DATE(expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
					UNION
					SELECT purchase_date AS d 
					FROM tbl_farm A
					WHERE A.origin_id = " . $this->db->escape($originId) . " 
					$supplierFilter
					AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
				) x
				ORDER BY year_num, month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$caseColumnsService = [];
		$caseColumnsLogistics = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsService[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']}
            THEN A.service_cost ELSE 0 END) AS `{$alias}`";
			$caseColumnsLogistics[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']}
            THEN A.logistic_cost ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlService = implode(", ", $caseColumnsService);
		$caseSqlLogistics = implode(", ", $caseColumnsLogistics);

		$caseColumnsTotalVolume = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsTotalVolume[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']}
            THEN A.total_volume ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlTotalVolume = implode(", ", $caseColumnsTotalVolume);

		$finalSql = "SELECT B.id AS cost_type_id, B.costing_name AS costing_type, $caseSqlCosting
        FROM tbl_farm_costings A
        INNER JOIN tbl_master_farm_costings B ON B.id = A.cost_type
        WHERE A.is_active = 1 AND (A.expense_date IS NOT NULL OR A.expense_date != '')
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND A.expense_type = 0 $supplierFilter
		  AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
        GROUP BY B.costing_name

        UNION ALL

        SELECT 3 AS cost_type_id, 'zona' AS costing_type, $caseSqlService
        FROM tbl_farm A
        WHERE A.is_active = 1
          AND A.origin_id = " . $this->db->escape($originId) . " 
		  $supplierFilter
          AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')

        UNION ALL

        -- Logistics row
        SELECT 2 AS cost_type_id, 'loading' AS costing_type, $caseSqlLogistics
        FROM tbl_farm A
        WHERE A.is_active = 1
          AND A.origin_id = " . $this->db->escape($originId) . "
		   $supplierFilter
          AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')

		 UNION ALL

        -- Total Volume row
        SELECT 0 AS cost_type_id, 'total_volume' AS costing_type, $caseSqlTotalVolume
        FROM tbl_farm A 
		INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
        WHERE A.is_active = 1 AND B.is_saw_mill = 0
          AND A.origin_id = " . $this->db->escape($originId) . " 
		   $supplierFilter
          AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.cost_type_id";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_total_volume_by_date($originId, $fromDate, $toDate, $supplierId)
	{

		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT SUM(A.total_volume) AS total_volume FROM tbl_farm A 
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND B.is_saw_mill = 0 
				$supplierFilter
				AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_expense_summary_data_nomina($originId, $year)
	{
		// Step 1: Build unified month list (based on both farm and costings)
		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings 	
            WHERE origin_id = " . $this->db->escape($originId) . " 
				AND YEAR(STR_TO_DATE(expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
            UNION
            SELECT purchase_date AS d 
            FROM tbl_farm 
            WHERE origin_id = " . $this->db->escape($originId) . " 
              AND YEAR(purchase_date) = " . $this->db->escape($year) . "
        ) x
        ORDER BY year_num, month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		// Step 2: CASE WHEN statements for farm_costings (grouped by costing_name)
		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']} 
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		// Step 3: CASE WHEN statements for Service & Logistics
		$caseColumnsService = [];
		$caseColumnsLogistics = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsService[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
            AND YEAR(purchase_date) = {$m['year_num']} THEN service_cost ELSE 0 END) AS `{$alias}`";
			$caseColumnsLogistics[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
            AND YEAR(purchase_date) = {$m['year_num']} THEN logistic_cost ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlService = implode(", ", $caseColumnsService);
		$caseSqlLogistics = implode(", ", $caseColumnsLogistics);

		// Step 2: CASE WHEN statements for farm_costings (grouped by costing_name)
		$caseColumnsTotalVolume = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsTotalVolume[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']}
            THEN A.total_volume ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlTotalVolume = implode(", ", $caseColumnsTotalVolume);

		// Step 4: Build final UNION query
		$finalSql = "
        -- farm costings by costing_name
        SELECT B.id AS cost_type_id, B.costing_name AS costing_type, $caseSqlCosting
        FROM tbl_farm_costings A
        INNER JOIN tbl_master_farm_costings B ON B.id = A.cost_type
        WHERE A.is_active = 1 AND (A.expense_date IS NOT NULL OR A.expense_date != '')
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND A.expense_type = 0 AND B.is_report_nomina = 1 
		  AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
        GROUP BY B.costing_name

        UNION ALL

        -- Service row
        SELECT 3 AS cost_type_id, 'zona' AS costing_type, $caseSqlService
        FROM tbl_farm
        WHERE is_active = 1
          AND origin_id = " . $this->db->escape($originId) . "
          AND YEAR(purchase_date) = " . $this->db->escape($year) . "

        UNION ALL

        -- Logistics row
        SELECT 2 AS cost_type_id, 'loading' AS costing_type, $caseSqlLogistics
        FROM tbl_farm
        WHERE is_active = 1
          AND origin_id = " . $this->db->escape($originId) . "
          AND YEAR(purchase_date) = " . $this->db->escape($year) . " 
		  
		 UNION ALL

        -- Total Volume row
        SELECT 0 AS cost_type_id, 'total_volume' AS costing_type, $caseSqlTotalVolume
        FROM tbl_farm A 
		INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
        WHERE A.is_active = 1 AND B.is_saw_mill = 0
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND YEAR(A.purchase_date) = " . $this->db->escape($year) . "";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.cost_type_id";

		// Step 5: Execute
		return $this->db->query($finalSql)->result_array();
	}

	public function get_total_volume_nomina($originId, $year)
	{
		$sql = "SELECT SUM(A.total_volume) AS total_volume FROM tbl_farm A 
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND B.is_saw_mill = 0 
				AND YEAR(A.purchase_date) = $year";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_expense_summary_data_by_date_nomina($originId, $fromDate, $toDate)
	{
		// Step 1: Build unified month list (based on both farm and costings)
		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
				FROM (
					SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
					FROM tbl_farm_costings 
					WHERE origin_id = " . $this->db->escape($originId) . " 
						AND STR_TO_DATE(expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
					UNION
					SELECT purchase_date AS d 
					FROM tbl_farm 
					WHERE origin_id = " . $this->db->escape($originId) . " 
					AND purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
				) x
				ORDER BY year_num, month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		// Step 2: CASE WHEN statements for farm_costings (grouped by costing_name)
		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		// Step 3: CASE WHEN statements for Service & Logistics
		$caseColumnsService = [];
		$caseColumnsLogistics = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsService[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
			AND YEAR(purchase_date) = {$m['year_num']}
            THEN service_cost ELSE 0 END) AS `{$alias}`";
			$caseColumnsLogistics[] = "SUM(CASE WHEN MONTH(purchase_date) = {$m['month_num']} 
			AND YEAR(purchase_date) = {$m['year_num']}
            THEN logistic_cost ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlService = implode(", ", $caseColumnsService);
		$caseSqlLogistics = implode(", ", $caseColumnsLogistics);

		// Step 2: CASE WHEN statements for farm_costings (grouped by costing_name)
		$caseColumnsTotalVolume = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsTotalVolume[] = "SUM(CASE WHEN MONTH(A.purchase_date) = {$m['month_num']} 
			AND YEAR(A.purchase_date) = {$m['year_num']} 
            THEN A.total_volume ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlTotalVolume = implode(", ", $caseColumnsTotalVolume);

		// Step 4: Build final UNION query
		$finalSql = "
        -- farm costings by costing_name
        SELECT B.id AS cost_type_id, B.costing_name AS costing_type, $caseSqlCosting
        FROM tbl_farm_costings A
        INNER JOIN tbl_master_farm_costings B ON B.id = A.cost_type
        WHERE A.is_active = 1 AND (A.expense_date IS NOT NULL OR A.expense_date != '')
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND A.expense_type = 0 AND B.is_report_nomina = 1 
		  AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
        GROUP BY B.costing_name

        UNION ALL

        -- Service row
        SELECT 3 AS cost_type_id, 'zona' AS costing_type, $caseSqlService
        FROM tbl_farm
        WHERE is_active = 1
          AND origin_id = " . $this->db->escape($originId) . "
          AND purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')

        UNION ALL

        -- Logistics row
        SELECT 2 AS cost_type_id, 'loading' AS costing_type, $caseSqlLogistics
        FROM tbl_farm
        WHERE is_active = 1
          AND origin_id = " . $this->db->escape($originId) . "
          AND purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
		  
		 UNION ALL

        -- Total Volume row
        SELECT 0 AS cost_type_id, 'total_volume' AS costing_type, $caseSqlTotalVolume
        FROM tbl_farm A 
		INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
        WHERE A.is_active = 1 AND B.is_saw_mill = 0
          AND A.origin_id = " . $this->db->escape($originId) . "
          AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.cost_type_id";

		// Step 5: Execute
		return $this->db->query($finalSql)->result_array();
	}

	public function get_total_volume_by_date_nomina($originId, $fromDate, $toDate)
	{
		$sql = "SELECT SUM(A.total_volume) AS total_volume FROM tbl_farm A 
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND B.is_saw_mill = 0 
				AND A.purchase_date BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_expense_summary_data_by_costtype($originId, $year, $costType, $supplierId)
	{

		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings A 
            WHERE origin_id = $originId AND cost_type = $costType
				AND YEAR(STR_TO_DATE(expense_date, '%d/%m/%Y')) = $year
				$supplierFilter
        ) x
        ORDER BY month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
				THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$finalSql = "SELECT B.id, B.machine_type, B.chassis_no, $caseSqlCosting
        		FROM tbl_farm_costings A 
				INNER JOIN tbl_master_machines B ON B.id = A.machine_type 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				$supplierFilter 
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
				GROUP BY B.id";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.id ASC";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_expense_summary_data_by_daterange_costtype($originId, $fromDate, $toDate, $costType, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings A
            WHERE A.origin_id = $originId AND cost_type = $costType 
				AND (STR_TO_DATE(expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y'))
				$supplierFilter 
        ) x
        ORDER BY month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$finalSql = "SELECT B.id, B.machine_type, B.chassis_no, $caseSqlCosting
        		FROM tbl_farm_costings A 
				INNER JOIN tbl_master_machines B ON B.id = A.machine_type 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				$supplierFilter 
				AND (STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y'))
				GROUP BY B.id";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.id ASC";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_machine_type_by_year($originId, $year, $costType, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT B.id, B.machine_type, B.chassis_no 
				FROM tbl_farm_costings A 
				INNER JOIN tbl_master_machines B ON B.id = A.machine_type 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = $year
				$supplierFilter 
				GROUP BY B.id 
				ORDER BY B.id ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_machine_type_by_date_range($originId, $fromDate, $toDate, $costType, $supplierId)
	{

		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT B.id, B.machine_type, B.chassis_no 
				FROM tbl_farm_costings A 
				INNER JOIN tbl_master_machines B ON B.id = A.machine_type 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y') 
				$supplierFilter 
				GROUP BY B.id 
				ORDER BY B.id ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_machine_details_by_id_year($originId, $year, $costType, $machineId, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT expense_date, concept, amount, B.supplier_name
					FROM tbl_farm_costings A 
					INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
					WHERE cost_type = $costType AND A.is_active = 1 AND A.origin_id = $originId 
					AND machine_type = $machineId
					AND YEAR(STR_TO_DATE(expense_date, '%d/%m/%Y')) = $year 
					$supplierFilter
					ORDER BY STR_TO_DATE(expense_date, '%d/%m/%Y') ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_machine_details_by_id_date_range($originId, $fromDate, $toDate, $costType, $machineId, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql = "SELECT expense_date, concept, amount, B.supplier_name
					FROM tbl_farm_costings A
					INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
					WHERE cost_type = $costType AND A.is_active = 1 AND A.origin_id = $originId 
					AND machine_type = $machineId 
					$supplierFilter 
					AND STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')
					ORDER BY STR_TO_DATE(expense_date, '%d/%m/%Y') ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_miscellaneous_summary_data_by_costtype($originId, $year, $costType, $supplierId)
	{

		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings A 
            WHERE origin_id = $originId AND cost_type = $costType
				AND YEAR(STR_TO_DATE(expense_date, '%d/%m/%Y')) = $year
				$supplierFilter
        ) x
        ORDER BY month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
				THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$finalSql = "SELECT A.id, A.concept, $caseSqlCosting
        		FROM tbl_farm_costings A 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				$supplierFilter 
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = " . $this->db->escape($year) . "
				GROUP BY A.concept";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.id ASC";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_miscellaneous_summary_data_by_daterange_costtype($originId, $fromDate, $toDate, $costType, $supplierId)
	{
		if($supplierId > 0) {
			$supplierFilter = " AND A.supplier_id = " . $this->db->escape($supplierId) . " ";
		} else {
			$supplierFilter = "";
		}

		$sql_months = "SELECT DISTINCT MONTH(d) AS month_num, YEAR(d) AS year_num, DATE_FORMAT(d, '%M') AS month_name
        FROM (
            SELECT STR_TO_DATE(expense_date, '%d/%m/%Y') AS d 
            FROM tbl_farm_costings A
            WHERE A.origin_id = $originId AND cost_type = $costType 
				AND (STR_TO_DATE(expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y'))
				$supplierFilter 
        ) x
        ORDER BY month_num";

		$months = $this->db->query($sql_months)->result_array();

		if (empty($months)) {
			return [];
		}

		$caseColumnsCosting = [];
		foreach ($months as $m) {
			$alias = "{$m['month_name']}-{$m['year_num']}"; // e.g. July-2025
			$caseColumnsCosting[] = "SUM(CASE WHEN MONTH(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['month_num']} 
			AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = {$m['year_num']}
            THEN A.amount ELSE 0 END) AS `{$alias}`";
		}
		$caseSqlCosting = implode(", ", $caseColumnsCosting);

		$finalSql = "SELECT A.id, A.concept, $caseSqlCosting
        		FROM tbl_farm_costings A 
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType 
				$supplierFilter 
				AND (STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y'))
				GROUP BY A.concept";

		$finalSql = "SELECT A.* FROM ($finalSql) A ORDER BY A.id ASC";

		return $this->db->query($finalSql)->result_array();
	}

	public function get_miscallaneous_detailed_data_year($originId, $year, $costType)
	{
		$sql = "SELECT A.concept, A.expense_date, B.supplier_name, A.amount  
				FROM tbl_farm_costings A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id  
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType
				AND YEAR(STR_TO_DATE(A.expense_date, '%d/%m/%Y')) = $year
				ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_miscallaneous_detailed_data_date_range($originId, $fromDate, $toDate, $costType)
	{
		$sql = "SELECT A.concept, A.expense_date, B.supplier_name, A.amount  
				FROM tbl_farm_costings A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id  
				WHERE A.is_active = 1 AND A.origin_id = $originId AND A.cost_type = $costType
				AND (STR_TO_DATE(A.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') 
				AND STR_TO_DATE('$toDate', '%d/%m/%Y'))
				ORDER BY STR_TO_DATE(A.expense_date, '%d/%m/%Y') ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}
}