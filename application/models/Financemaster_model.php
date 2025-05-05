<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Financemaster_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//CURRENCIES

	public function get_currency_code($originid)
	{
		$query = $this->db->query("SELECT B.currency_code, B.currency_abbreviation, B.currency_format, B.currency_symbol, B.currency_excel_format, B.currency_excel_format1 
				FROM tbl_origin_currencies A
				INNER JOIN tbl_currency B ON B.id = A.currency_id
				WHERE A.origin_id = $originid AND A.is_default_expense = 1 
				UNION ALL 
                SELECT B.currency_code, B.currency_abbreviation, B.currency_format,  B.currency_symbol, B.currency_excel_format, B.currency_excel_format1 
				FROM tbl_currency B
				WHERE B.is_default = 1");
		return $query->result();
	}

	//LEDGER TYPES
	public function all_ledger_types($originid)
	{
		$strQuery = "SELECT id, ledger_name, getapplicableorigins_byid(origin_id) AS origin, is_active 
                 FROM tbl_ledger_type_master";

		if ($originid > 0) {
			$strQuery = $strQuery . " WHERE origin_id = $originid";
		}
		$query = $this->db->query($strQuery);
		return $query->result();
	}

	public function get_ledger_type_by_id($id)
	{
		$query = $this->db->query("SELECT id, ledger_name, is_active, origin_id
				FROM tbl_ledger_type_master WHERE id = $id");
		return $query->result();
	}

	public function add_ledger_type($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_ledger_type_master', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_ledger_type($data, $id)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where('id', $id);
		if ($this->db->update('tbl_ledger_type_master', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//ACCOUNT HEADS
	public function all_account_heads($originid)
	{
		$strQuery = "SELECT A.id, A.code, A.name_in_ledger, A.name_in_app, A.ledger_type, B.ledger_name, A.is_active, getapplicableorigins_byid(A.origin_id) AS origin FROM tbl_accounting_heads A 
			INNER JOIN tbl_ledger_type_master B ON B.id = A.ledger_type";


		if ($originid > 0) {
			$strQuery = $strQuery . " WHERE A.origin_id = $originid";
		}

		$strQuery = $strQuery . " ORDER BY A.ledger_type";

		$query = $this->db->query($strQuery);
		return $query->result();
	}

	public function get_account_head_by_id($id)
	{
		$query = $this->db->query("SELECT id, code, name_in_ledger, name_in_app, ledger_type, is_active, origin_id FROM tbl_accounting_heads WHERE id = $id");
		return $query->result();
	}

	public function add_account_heads($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_accounting_heads', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_account_heads($data, $id)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where('id', $id);
		if ($this->db->update('tbl_accounting_heads', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//EXPENSE LEDGER
	public function fetch_expense_ledger_by_user($originid, $userid)
	{
		$query = $this->db->query("SELECT 1 AS transaction_type, 
			CASE WHEN SUM(amount) IS NULL THEN 0 ELSE SUM(amount) END AS amount FROM tbl_transaction 
			WHERE user_id = $userid 
			AND transaction_type = 1 AND is_active = 1 AND origin_id = $originid
			UNION
			SELECT 2 AS transaction_type, 
			CASE WHEN SUM(amount) IS NULL THEN 0 ELSE SUM(amount) END AS amount FROM tbl_transaction 
			WHERE user_id = $userid 
			AND transaction_type = 2 AND is_active = 1 AND origin_id = $originid");
		return $query->result();
	}

	public function get_credit_transactions_by_user($originid, $userid)
	{
		$query = $this->db->query("SELECT transaction_id, transaction_display_id, 
				A.transaction_date, C.fullname, A.amount FROM tbl_transaction A
				INNER JOIN tbl_user_registration C ON C.userid = A.created_by
				WHERE is_active = 1 AND A.origin_id = $originid AND A.transaction_type = 1 AND A.user_id = $userid 
				ORDER BY transaction_id DESC");
		return $query->result();
	}

	public function get_debit_transactions_by_user($originid, $userid)
	{
		$query = $this->db->query("SELECT A.transaction_id, A.transaction_display_id, 
				A.transaction_date, 
				A.amount, CONCAT(D.ledger_name,' / ', E.name_in_ledger) as expensetype, 
				B.beneficiary_name, U.fullname as updated_by FROM tbl_transaction A 
				INNER JOIN tbl_expense_details B ON B.transaction_id = A.transaction_id
				INNER JOIN tbl_user_registration C ON C.userid = A.created_by 
				INNER JOIN tbl_user_registration U ON U.userid = A.updated_by 
				INNER JOIN tbl_ledger_type_master D ON D.id = B.expense_type 
				INNER JOIN tbl_accounting_heads E ON E.id = B.account_head
				WHERE A.is_active = 1 AND A.transaction_type = 2 AND A.user_id = $userid AND A.origin_id = $originid  
				ORDER BY transaction_id DESC");
		return $query->result();
	}

	//LIQUIDATION REPORT
	public function get_contracts_liquidation_warehouse($originid)
	{
		$query = $this->db->query("SELECT B.contract_id, B.contract_code, SUM(A.total_volume) AS total_volume, 
					C.supplier_name AS fullname, D.product_name, E.product_type_name, 
					C.id as sid, getapplicableorigins_byid(B.origin_id) AS origin, B.origin_id, B.existing_price_condition, 
					CASE WHEN (B.description IS NULL OR B.description = '') THEN '---' ELSE B.description END AS description, F.purchase_unit    
					FROM tbl_contract_inventory_mapping A 
					INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id AND B.supplier_id = A.supplier_id 
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
					INNER JOIN tbl_product_master D ON D.product_id = B.product 
					INNER JOIN tbl_product_types E ON E.type_id = B.product_type 
					INNER JOIN tbl_purchase_unit F ON F.id = B.unit_of_purchase 
					WHERE A.is_active = 1 AND B.is_active = 1 
					AND B.contract_type = 1 AND B.origin_id = $originid GROUP BY A.contract_id ORDER BY A.contract_id");
		return $query->result();
	}

	public function get_contracts_liquidation_fieldpurchase($originid)
	{
		$query = $this->db->query("SELECT B.contract_id, B.contract_code, SUM(A.total_volume) AS total_volume, 
					D.product_name, E.product_type_name, C.fullname, C.userid as sid, 
					getapplicableorigins_byid(B.origin_id) AS origin, B.origin_id, 
					CASE WHEN (B.description IS NULL OR B.description = '') THEN '---' ELSE B.description END AS description, F.purchase_unit    
					FROM tbl_contract_inventory_mapping A 
					INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
					INNER JOIN tbl_user_registration C ON C.userid = B.supplier_id 
					INNER JOIN tbl_product_master D ON D.product_id = B.product 
					INNER JOIN tbl_product_types E ON E.type_id = B.product_type 
					INNER JOIN tbl_purchase_unit F ON F.id = B.unit_of_purchase 
					WHERE A.is_active = 1 AND B.is_active = 1 
					AND B.contract_type = 2 AND B.origin_id = $originid GROUP BY A.contract_id");
		return $query->result();
	}

	public function get_inventory_by_contract($contractid, $contractcode, $originid, $supplierid)
	{
		$query = $this->db->query("SELECT A.mapping_id, inventory_order, invoice_number 
					FROM tbl_contract_inventory_mapping A 
					INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
					AND B.supplier_id = A.supplier_id 
					WHERE A.contract_id = $contractid AND A.supplier_id = $supplierid AND A.is_active = 1 
					AND B.origin_id = $originid AND B.contract_code = '$contractcode'");
		return $query->result();
	}

	public function get_inventory_by_contract_fieldpurchase($contractid, $contractcode, $originid, $supplierid)
	{
		$query = $this->db->query("SELECT A.mapping_id, A.inventory_order, A.invoice_number 
					FROM tbl_contract_inventory_mapping A
					INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
					WHERE A.contract_id = $contractid AND B.supplier_id = $supplierid AND A.is_active = 1 AND B.origin_id = $originid 
					AND B.contract_code = '$contractcode'");
		return $query->result();
	}

	public function update_invoice_number($mappingid, $contractid, $inventoryorder, $supplierid, $data)
	{
		$multiClause = array('mapping_id' => $mappingid, 'contract_id' => $contractid, 'inventory_order' => $inventoryorder, 'supplier_id' => $supplierid, 'is_active' => 1);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_contract_inventory_mapping', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function update_invoice_number_field_purchase($mappingid, $contractid, $inventoryorder, $data)
	{
		$multiClause = array('mapping_id' => $mappingid, 'contract_id' => $contractid, 'inventory_order' => $inventoryorder, 'is_active' => 1);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_contract_inventory_mapping', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function fetch_inventory_report_warehouse($contractid, $contractcode, $supplierid, $originid)
	{
		if ($supplierid == 0) {
			$query = $this->db->query("SELECT B.contract_code, A.inventory_order, B.product, B.product_type, 
				B.supplier_id, B.unit_of_purchase,
				purchase_allowance, purchase_allowance_length, B.existing_price_condition FROM tbl_contract_inventory_mapping A
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				WHERE A.contract_id = $contractid AND B.origin_id = $originid 
				AND B.contract_code = '$contractcode' AND A.is_active = 1");
		} else {
			$query = $this->db->query("SELECT B.contract_code, A.inventory_order, B.product, B.product_type, 
				B.supplier_id, B.unit_of_purchase,
				purchase_allowance, purchase_allowance_length, B.existing_price_condition FROM tbl_contract_inventory_mapping A
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				WHERE A.contract_id = $contractid AND A.supplier_id = $supplierid AND B.origin_id = $originid 
				AND B.contract_code = '$contractcode' AND A.is_active = 1");
		}

		return $query->result();
	}

	public function get_inventory_ledger_contract($contractid, $supplierid, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		$query = $this->db->query("SELECT DATE_FORMAT(STR_TO_DATE(A.expense_date,'%Y-%m-%d'),'%d %M %Y') AS expense_date, 
					B.type_name, A.inventory_order, A.amount, C.supplier_name
					FROM tbl_inventory_ledger A
					INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id
					WHERE contract_id = $contractid AND A.is_active = 1  
					ORDER BY B.id, A.id, STR_TO_DATE(A.expense_date,'%Y-%m-%d')");
		return $query->result();
	}

	public function get_farm_detail($contractid, $supplierid, $originid, $inventoryorder, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		if ($supplierid == 0) {
			$query = $this->db->query("SELECT A.farm_id, DATE_FORMAT(A.purchase_date, '%d %M %Y') AS purchase_date, A.inventory_order, 
						A.plate_number, B.supplier_name, A.supplier_taxes_array, A.logistics_taxes_array, A.service_taxes_array, 
						A.logistic_cost, A.service_cost, A.adjustment, A.purchase_unit_id, A.exchange_rate, 
						C.invoice_number, D.purchase_allowance, D.purchase_allowance_length, A.driver_name     
						FROM tbl_farm A 
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						INNER JOIN tbl_contract_inventory_mapping C ON C.contract_id = A.contract_id AND C.supplier_id = A.supplier_id 
						AND C.inventory_order = A.inventory_order 
						INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
						WHERE A.contract_id = $contractid AND A.origin_id = $originid 
						AND A.inventory_order = '$inventoryorder' AND A.is_active = 1 AND C.is_active = 1");
		} else {
			$query = $this->db->query("SELECT A.farm_id, DATE_FORMAT(A.purchase_date, '%d %M %Y') AS purchase_date, A.inventory_order, 
						A.plate_number, B.supplier_name, A.supplier_taxes_array, A.logistics_taxes_array, A.service_taxes_array, 
						A.logistic_cost, A.service_cost, A.adjustment, A.purchase_unit_id, A.exchange_rate, 
						C.invoice_number, D.purchase_allowance, D.purchase_allowance_length, A.driver_name     
						FROM tbl_farm A 
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						INNER JOIN tbl_contract_inventory_mapping C ON C.contract_id = A.contract_id AND C.supplier_id = A.supplier_id 
						AND C.inventory_order = A.inventory_order 
						INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
						WHERE A.contract_id = $contractid AND A.supplier_id = $supplierid AND A.origin_id = $originid 
						AND A.inventory_order = '$inventoryorder' AND A.is_active = 1 AND C.is_active = 1");
		}
		return $query->result();
	}

	public function get_farm_data($supplierid, $inventoryorder, $originid)
	{
		$query = $this->db->query("SELECT A.circumference_bought, A.length_bought, 
				SUM(CASE WHEN A.type = 2 THEN A.noofpieces ELSE 0 END) AS farm, 
				SUM(CASE WHEN A.type = 1 THEN A.noofpieces ELSE 0 END) as reception FROM 
				(SELECT circumference_bought, length_bought, id, type, CASE type WHEN 1 THEN
				getnoofpieces_reception(inventory_number, id, circumference_bought, length_bought)
				ELSE getnoofpieces_farm(inventory_number, id, circumference_bought, length_bought)
				END AS noofpieces 
				FROM (SELECT B.circumference_bought AS circumference_bought,B.length_bought AS length_bought, 
				B.reception_data_id AS data_id,A.salvoconducto AS inventory_number,A.supplier_id AS supplier_id, 
				A.reception_id AS id,'1' AS type 
				FROM (tbl_reception A JOIN tbl_reception_data B ON (((B.reception_id = A.reception_id) AND (B.isactive = 1) 
				AND ((B.isduplicatescanned = 0) OR ISNULL(B.isduplicatescanned))))) WHERE ((A.isactive = 1) AND 
				((A.isduplicatecaptured = 0) OR ISNULL(A.isduplicatecaptured))) AND A.origin_id = $originid 
				AND A.salvoconducto = '$inventoryorder' AND A.supplier_id = $supplierid 
				GROUP BY B.circumference_bought, B.length_bought, A.salvoconducto
				UNION ALL
				SELECT B.circumference AS circumference_bought,B.length AS length_bought,B.farm_data_id AS getid, 
				A.inventory_order AS inventory_number,A.supplier_id AS supplier_id,A.farm_id AS id,'2' AS type 
				FROM (tbl_farm A JOIN tbl_farm_data B ON(((B.farm_id = A.farm_id) AND (B.is_active = 1)))) 
				WHERE (A.is_active = 1) AND A.origin_id = $originid AND A.inventory_order = '$inventoryorder' AND A.supplier_id = $supplierid 
				GROUP BY B.circumference, B.length, A.inventory_order) x ORDER BY circumference_bought) A 
				GROUP BY A.circumference_bought, A.length_bought");
		return $query->result();
	}

	// 	public function get_farm_data_square($supplierid, $inventoryorder, $originid)
	// 	{
	// 		$query = $this->db->query("SELECT A.width_bought, A.length_bought, A.thickness_bought,
	// 				SUM(CASE WHEN A.type = 2 THEN A.noofpieces ELSE 0 END) AS farm, 
	// 				SUM(CASE WHEN A.type = 1 THEN A.noofpieces ELSE 0 END) as reception, inventory_order FROM (SELECT inventory_order, width_bought, length_bought, thickness_bought, type, noofpieces FROM 
	// 				(SELECT B.salvoconducto AS inventory_order, width_bought, length_bought, thickness_bought, reception_data_id AS data_id, B.reception_id AS id, 1 AS type, getnoofpieces_reception_square(B.salvoconducto, B.reception_id, width_bought, length_bought, thickness_bought) AS noofpieces FROM tbl_reception_data A 
	// 				INNER JOIN tbl_reception B ON B.reception_id = A.reception_id 
	// 				WHERE B.salvoconducto = '$inventoryorder' AND B.origin_id = $originid AND B.supplier_id = $supplierid
	// 				UNION ALL 
	// 				SELECT B.inventory_order AS inventory_order, A.width AS width_bought, A.length AS length, A.thickness AS thickness_bought, A.farm_data_id AS data_id, A.farm_id AS id, 2 AS type, getnoofpieces_farm_square(B.inventory_order, A.farm_id, width, length, thickness) AS noofpieces
	// 				FROM tbl_farm_data A 
	// 				INNER JOIN tbl_farm B ON B.farm_id = A.farm_id
	// 				AND B.inventory_order = '$inventoryorder' AND B.origin_id = $originid AND B.supplier_id = $supplierid) X) A 
	// 				GROUP BY inventory_order, A.width_bought, A.length_bought, A.thickness_bought 
	// 				ORDER BY A.length_bought");
	// 		return $query->result();
	// 	}

	public function get_farm_data_square($supplierid, $inventoryorder, $originid)
	{
		$query = $this->db->query("SELECT A.width_bought, A.length_bought, A.thickness_bought,
				SUM(CASE WHEN A.type = 2 THEN A.noofpieces ELSE 0 END) AS farm, 
				SUM(CASE WHEN A.type = 1 THEN A.noofpieces ELSE 0 END) as reception, inventory_order FROM (SELECT inventory_order, width_bought, length_bought, thickness_bought, type, noofpieces FROM 
				(SELECT B.salvoconducto AS inventory_order, width_bought, length_bought, thickness_bought, reception_data_id AS data_id, B.reception_id AS id, 1 AS type, A.scanned_code AS noofpieces FROM tbl_reception_data A 
				INNER JOIN tbl_reception B ON B.reception_id = A.reception_id 
				WHERE B.salvoconducto = '$inventoryorder' AND B.origin_id = $originid AND B.isactive = 1 AND B.supplier_id = $supplierid
				UNION ALL 
				SELECT B.inventory_order AS inventory_order, A.width AS width_bought, A.length AS length, A.thickness AS thickness_bought, A.farm_data_id AS data_id, A.farm_id AS id, 2 AS type, A.no_of_pieces AS noofpieces
				FROM tbl_farm_data A 
				INNER JOIN tbl_farm B ON B.farm_id = A.farm_id
				AND B.inventory_order = '$inventoryorder' AND B.origin_id = $originid AND B.is_active = 1 AND B.supplier_id = $supplierid) X) A 
				GROUP BY inventory_order, A.width_bought, A.length_bought, A.thickness_bought 
				ORDER BY A.length_bought");
		return $query->result();
	}

	// public function get_farm_data($supplierid, $inventoryorder, $originid)
	// {
	// 	$query = $this->db->query("SELECT A.circumference_bought, A.length_bought, 
	// 			SUM(CASE WHEN A.type = 2 THEN A.noofpieces ELSE 0 END) AS farm, 
	// 			SUM(CASE WHEN A.type = 1 THEN A.noofpieces ELSE 0 END) as reception FROM 
	// 			(SELECT circumference_bought, length_bought, id, type, CASE type WHEN 1 THEN
	// 			getnoofpieces_reception(inventory_number, id, circumference_bought)
	// 			ELSE getnoofpieces_farm(inventory_number, id, circumference_bought)
	// 			END AS noofpieces 
	// 			FROM (SELECT B.circumference_bought AS circumference_bought,B.length_bought AS length_bought, 
	// 			B.reception_data_id AS data_id,A.salvoconducto AS inventory_number,A.supplier_id AS supplier_id, 
	// 			A.reception_id AS id,'1' AS type 
	// 			FROM (tbl_reception A JOIN tbl_reception_data B ON (((B.reception_id = A.reception_id) AND (B.isactive = 1) 
	// 			AND ((B.isduplicatescanned = 0) OR ISNULL(B.isduplicatescanned))))) WHERE ((A.isactive = 1) AND 
	// 			((A.isduplicatecaptured = 0) OR ISNULL(A.isduplicatecaptured))) AND A.origin_id = $originid 
	// 			AND A.salvoconducto = '$inventoryorder' AND A.supplier_id = $supplierid 
	// 			GROUP BY B.circumference_bought, B.length_bought, A.salvoconducto
	// 			UNION ALL
	// 			SELECT B.circumference AS circumference_bought,B.length AS length_bought,B.farm_data_id AS getid, 
	// 			A.inventory_order AS inventory_number,A.supplier_id AS supplier_id,A.farm_id AS id,'2' AS type 
	// 			FROM (tbl_farm A JOIN tbl_farm_data B ON(((B.farm_id = A.farm_id) AND (B.is_active = 1)))) 
	// 			WHERE (A.is_active = 1) AND A.origin_id = $originid AND A.inventory_order = '$inventoryorder' AND A.supplier_id = $supplierid 
	// 			GROUP BY B.circumference, B.length, A.inventory_order) x ORDER BY circumference_bought) A 
	// 			GROUP BY A.circumference_bought, A.length_bought");
	// 	return $query->result();
	// }

	public function get_farm_data_purchase_manager($circumferencemin, $circumferencemax, $inventoryorder)
	{
		$query = $this->db->query("SELECT getlengthbought_farm_fieldpurchase(inventory_order, $circumferencemin, $circumferencemax) AS length_bought, 
				getnoofpieces_farm_fieldpurchase(inventory_order, $circumferencemin, $circumferencemax) AS farmPieces, 
				getnoofpieces_reception_fieldpurchase(inventory_order, $circumferencemin, $circumferencemax) AS receptionPieces
				FROM tbl_farm WHERE is_active = 1 AND inventory_order = '$inventoryorder'");
		return $query->result();
	}

	public function get_contract_price_data($contractid, $inventoryorder)
	{
		$query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3, pricerange_grade_semi, pricerange_grade_longs  
				FROM tbl_supplier_contract_inventory_price WHERE contract_id = $contractid
				AND inventory_number = '$inventoryorder' AND is_active = 1");
		return $query->result();
	}

	//STOCK
	public function get_stock_transactions($originid)
	{
		$query = $this->db->query("SELECT F.salvoconducto AS inventory_order, CASE WHEN G.wood_value IS NULL THEN 0 ELSE G.wood_value END AS totalcost, 
				SUM(E.scanned_code) as total_pieces, SUM(E.remaining_stock_count) as remaining_stock, 
				SUM(ROUND(((E.circumference_bought-2)*(E.circumference_bought-2)*(E.length_bought-5)/16000000)*E.remaining_stock_count,3)) AS total_volume, 
				H.supplier_name
				FROM tbl_reception_data E 
				INNER JOIN tbl_reception F ON F.reception_id = E.reception_id
				LEFT JOIN tbl_farm G ON G.inventory_order = F.salvoconducto AND G.is_active = 1 
				INNER JOIN tbl_suppliers H ON H.id = F.supplier_id
				AND (E.isduplicatescanned = 0 OR E.isduplicatescanned IS NULL) AND E.isactive = 1
				WHERE F.isactive = 1 AND (F.isduplicatecaptured = 0 OR F.isduplicatecaptured IS NULL) 
				AND F.origin_id = $originid 
				GROUP BY F.salvoconducto 
				HAVING SUM(E.remaining_stock_count) > 0 OR SUM(E.remaining_stock_count) < 0");
		return $query->result();
	}

	//COST SUMMARY REPORT
	public function get_export_sa_numbers($originid)
	{
		$query = $this->db->query("SELECT id, sa_number FROM tbl_export_container_details 
				WHERE isactive = 1 AND origin_id = $originid ORDER BY id DESC");
		return $query->result();
	}

	// public function fetch_export_containers($originid, $producttypeid, $startdate, $enddate)
	// {
	// 	$query = $this->db->query("SELECT B.export_id, B.dispatch_id, C.container_number, B.cft_value, 
	// 			A.product_type_id FROM tbl_export_container_details A
	// 			INNER JOIN tbl_export_container B ON B.container_details_id = A.id
	// 			INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1
	// 			AND C.isduplicatedispatched = 0
	// 			WHERE A.isactive = 1 AND B.isactive = 1 
	// 			AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
	// 			BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid");
	// 	return $query->result();
	// }

	// public function fetch_export_containers_by_exportid($originid, $exportid)
	// {
	// 	$query = $this->db->query("SELECT B.export_id, B.dispatch_id, C.container_number, B.cft_value, 
	// 			A.product_type_id FROM tbl_export_container_details A
	// 			INNER JOIN tbl_export_container B ON B.container_details_id = A.id
	// 			INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1
	// 			AND C.isduplicatedispatched = 0
	// 			WHERE A.isactive = 1 AND B.isactive = 1 
	// 			AND A.id = $exportid AND A.origin_id = $originid");
	// 	return $query->result();
	// }

	// 	public function fetch_export_containers($originid, $producttypeid, $startdate, $enddate, $langcode)
	// 	{
	// 		$this->db->query("SET lc_time_names='$langcode'");
	// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, 
	// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
	// 				get_material_cost_by_dispatch_id(B.dispatch_id) AS material_cost, 
	// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
	// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
	// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
	// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
	// 				D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
	// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
	// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
	// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1
	// 				AND C.isduplicatedispatched = 0
	// 				WHERE A.isactive = 1 AND B.isactive = 1 
	// 				AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
	// 				BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid");
	// 		return $query->result();
	// 	}

	// 	public function fetch_export_containers_by_exportid($originid, $exportid, $langcode)
	// 	{
	// 		$this->db->query("SET lc_time_names='$langcode'");
	// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, 
	// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
	// 				get_material_cost_by_dispatch_id(B.dispatch_id) AS material_cost, 
	// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
	// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
	// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
	// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
	// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
	// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
	// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
	// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1
	// 				AND C.isduplicatedispatched = 0
	// 				WHERE A.isactive = 1 AND B.isactive = 1 
	// 				AND A.id = $exportid AND A.origin_id = $originid");
	// 		return $query->result();
	// 	}

	public function fetch_export_containers1($originid, $producttypeid, $startdate, $enddate, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
		// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
		// 				CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
		// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
		// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE A.isactive = 1 AND B.isactive = 1 
		// 				AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
		// 				BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id");

		if ($originid == 1) {
			$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
					B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
					CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
					D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
					D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
					D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
					D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
					D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
					INNER JOIN tbl_export_container B ON B.container_details_id = A.id
					INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
					LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
					AND C.isduplicatedispatched = 0
					WHERE A.isactive = 1 AND B.isactive = 1 
					AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
					BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id ORDER BY A.sa_number ASC");
		} else {
			$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
						B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
						CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
						D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
						D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
						D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
						D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
						D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
						INNER JOIN tbl_export_container B ON B.container_details_id = A.id
						INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
						LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
						AND C.isduplicatedispatched = 0
						WHERE A.isactive = 1 AND B.isactive = 1 
						AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
						BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id ORDER BY A.sa_number ASC");
		}


		return $query->result();
	}

	public function fetch_export_containers($originid, $producttypeid, $startdate, $enddate, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
		// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
		// 				CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
		// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
		// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE A.isactive = 1 AND B.isactive = 1 
		// 				AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
		// 				BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id");

		if ($originid == 1) {
			// $query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
			// 	B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
			// 	CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
			// 	D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
			// 	D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
			// 	D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
			// 	D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
			// 	D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
			// 	INNER JOIN tbl_export_container B ON B.container_details_id = A.id
			// 	INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
			// 	LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
			// 	AND C.isduplicatedispatched = 0
			// 	WHERE A.isactive = 1 AND B.isactive = 1 
			// 	AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
			// 	BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id ORDER BY A.sa_number ASC");

			$query = $this->db->query("SELECT A.id AS export_id, A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
					B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
					UPPER(D1.invoice_no) AS custom_invoice, D.container_value AS custom_value, 
					UPPER(E1.invoice_no) AS itr_invoice, E.container_value AS itr_value, 
					UPPER(F1.invoice_no) AS port_invoice, F.container_value AS port_value, 
					UPPER(G1.invoice_no) AS shipping_invoice, G.container_value AS shipping_value, 
					UPPER(H1.invoice_no) AS fumigation_invoice, H.container_value AS fumigation_value, 
					UPPER(I1.invoice_no) AS phyto_invoice, I.container_value AS phyto_value, 
					UPPER(J1.invoice_no) AS coteros_invoice, J.container_value AS coteros_value, 
					UPPER(K1.invoice_no) AS incentive_invoice, K.container_value AS incentive_value,
					UPPER(L1.invoice_no) AS remobilization_invoice, L.container_value AS remobilization_value, 
					CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
					C.dispatch_id
					FROM tbl_export_container_details A
					INNER JOIN tbl_export_container B ON B.container_details_id = A.id
					INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
					LEFT JOIN tbl_export_document_container D ON D.dispatch_id = C.dispatch_id AND D.export_type = 1 AND D.is_active = 1 
					LEFT JOIN tbl_export_documents D1 ON D1.export_id = A.id AND D1.export_type = 1 AND D1.is_active = 1  
					LEFT JOIN tbl_export_document_container E ON E.dispatch_id = C.dispatch_id AND E.export_type = 2 AND E.is_active = 1  
					LEFT JOIN tbl_export_documents E1 ON E1.export_id = A.id AND E1.export_type = 2 AND E1.is_active = 1 
					LEFT JOIN tbl_export_document_container F ON F.dispatch_id = C.dispatch_id AND F.export_type = 3 AND F.is_active = 1 
					LEFT JOIN tbl_export_documents F1 ON F1.export_id = A.id AND F1.export_type = 3 AND F1.is_active = 1 
					LEFT JOIN tbl_export_document_container G ON G.dispatch_id = C.dispatch_id AND G.export_type = 9 AND G.is_active = 1 
					LEFT JOIN tbl_export_documents G1 ON G1.export_id = A.id AND G1.export_type = 9 AND G1.is_active = 1 
					LEFT JOIN tbl_export_document_container H ON H.dispatch_id = C.dispatch_id AND H.export_type = 4 AND H.is_active = 1 
					LEFT JOIN tbl_export_documents H1 ON H1.export_id = A.id AND H1.export_type = 4 AND H1.is_active = 1 
					LEFT JOIN tbl_export_document_container I ON I.dispatch_id = C.dispatch_id AND I.export_type = 5 AND I.is_active = 1 
					LEFT JOIN tbl_export_documents I1 ON I1.export_id = A.id AND I1.export_type = 5 AND I1.is_active = 1 
					LEFT JOIN tbl_export_document_container J ON J.dispatch_id = C.dispatch_id AND J.export_type = 6 AND J.is_active = 1 
					LEFT JOIN tbl_export_documents J1 ON J1.export_id = A.id AND J1.export_type = 6 AND J1.is_active = 1 
					LEFT JOIN tbl_export_document_container K ON K.dispatch_id = C.dispatch_id AND K.export_type = 7 AND K.is_active = 1 
					LEFT JOIN tbl_export_documents K1 ON K1.export_id = A.id AND K1.export_type = 7 AND K1.is_active = 1  
					LEFT JOIN tbl_export_document_container L ON L.dispatch_id = C.dispatch_id AND L.export_type = 8 AND L.is_active = 1 
					LEFT JOIN tbl_export_documents L1 ON L1.export_id = A.id AND L1.export_type = 8 AND L1.is_active = 1 
					AND C.isduplicatedispatched = 0
					WHERE A.isactive = 1 AND B.isactive = 1 
					AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
					BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id ORDER BY A.sa_number ASC");
		} else {
			$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
						B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
						CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
						D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
						D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
						D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
						D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
						D.exchange_rate, D.entry_weight_cost, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
						INNER JOIN tbl_export_container B ON B.container_details_id = A.id
						INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
						LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
						AND C.isduplicatedispatched = 0
						WHERE A.isactive = 1 AND B.isactive = 1 
						AND (A.shipped_date = '' OR (STR_TO_DATE(A.shipped_date, '%d/%m/%Y')
						BETWEEN '$startdate' AND '$enddate')) AND A.product_type_id IN ($producttypeid) AND A.origin_id = $originid GROUP BY B.dispatch_id ORDER BY A.sa_number ASC");
		}


		return $query->result();
	}

	public function fetch_export_containers_by_exportid1($originid, $exportid, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
		// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
		// 				CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
		// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
		// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE A.isactive = 1 AND B.isactive = 1 
		// 				AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");

		if ($originid == 1) {
			//   $query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
			// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
			// 				CASE WHEN A.id >= 282 THEN (A.unit_price * B.total_pieces) ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
			// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
			// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
			// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
			// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
			// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
			// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
			// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
			// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
			// 				AND C.isduplicatedispatched = 0
			// 				WHERE A.isactive = 1 AND B.isactive = 1 
			// 				AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");

			$query = $this->db->query("SELECT A.id AS export_id, A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
				CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
				D.exchange_rate, D.loss_profit, D.document_number, D.observation, get_average_length_by_container(B.dispatch_id) AS avg_length 
				FROM tbl_export_container_details A
				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
				AND C.isduplicatedispatched = 0
				WHERE A.isactive = 1 AND B.isactive = 1 
				AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");
		} else {
			$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
					B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
					CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
					D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
					D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
					D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
					D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
					D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
					INNER JOIN tbl_export_container B ON B.container_details_id = A.id
					INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
					LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
					AND C.isduplicatedispatched = 0
					WHERE A.isactive = 1 AND B.isactive = 1 
					AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");
		}


		return $query->result();
	}

	public function fetch_export_containers_by_exportid($originid, $exportid, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		// 		$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
		// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
		// 				CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
		// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
		// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE A.isactive = 1 AND B.isactive = 1 
		// 				AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");

		if ($originid == 1) {
			//   $query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
			// 				B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
			// 				CASE WHEN A.id >= 282 THEN (A.unit_price * B.total_pieces) ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
			// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
			// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
			// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
			// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
			// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
			// 				INNER JOIN tbl_export_container B ON B.container_details_id = A.id
			// 				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
			// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
			// 				AND C.isduplicatedispatched = 0
			// 				WHERE A.isactive = 1 AND B.isactive = 1 
			// 				AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");

			// $query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
			// B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
			// CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
			// D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
			// D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
			// D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
			// D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
			// D.exchange_rate, D.loss_profit, D.document_number, D.observation, get_average_length_by_container(B.dispatch_id) AS avg_length 
			// FROM tbl_export_container_details A
			// INNER JOIN tbl_export_container B ON B.container_details_id = A.id
			// INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
			// LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
			// AND C.isduplicatedispatched = 0
			// WHERE A.isactive = 1 AND B.isactive = 1 
			// AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");

			$query = $this->db->query("SELECT A.id AS export_id, A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces, 
					B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
					UPPER(D1.invoice_no) AS custom_invoice, D.container_value AS custom_value, 
					UPPER(E1.invoice_no) AS itr_invoice, E.container_value AS itr_value, 
					UPPER(F1.invoice_no) AS port_invoice, F.container_value AS port_value, 
					UPPER(G1.invoice_no) AS shipping_invoice, G.container_value AS shipping_value, 
					UPPER(H1.invoice_no) AS fumigation_invoice, H.container_value AS fumigation_value, 
					UPPER(I1.invoice_no) AS phyto_invoice, I.container_value AS phyto_value, 
					UPPER(J1.invoice_no) AS coteros_invoice, J.container_value AS coteros_value, 
					UPPER(K1.invoice_no) AS incentive_invoice, K.container_value AS incentive_value,
					UPPER(L1.invoice_no) AS remobilization_invoice, L.container_value AS remobilization_value, 
					CASE WHEN A.id >= 282 THEN CASE WHEN A.product_type_id = 1 THEN (A.unit_price * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 1 AND 2.99 THEN (A.unit_price_shorts * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) BETWEEN 3 AND 5.99 THEN (A.unit_price_semi * B.total_pieces) WHEN get_average_length_by_container(B.dispatch_id) >= 6 THEN (A.unit_price_longs * B.total_pieces) END ELSE CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END END AS material_cost, 
					C.dispatch_id 
					FROM tbl_export_container_details A
					INNER JOIN tbl_export_container B ON B.container_details_id = A.id
					INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
					LEFT JOIN tbl_export_document_container D ON D.dispatch_id = C.dispatch_id AND D.export_type = 1 AND D.is_active = 1 
					LEFT JOIN tbl_export_documents D1 ON D1.export_id = A.id AND D1.export_type = 1 AND D1.is_active = 1  
					LEFT JOIN tbl_export_document_container E ON E.dispatch_id = C.dispatch_id AND E.export_type = 2 AND E.is_active = 1  
					LEFT JOIN tbl_export_documents E1 ON E1.export_id = A.id AND E1.export_type = 2 AND E1.is_active = 1 
					LEFT JOIN tbl_export_document_container F ON F.dispatch_id = C.dispatch_id AND F.export_type = 3 AND F.is_active = 1 
					LEFT JOIN tbl_export_documents F1 ON F1.export_id = A.id AND F1.export_type = 3 AND F1.is_active = 1 
					LEFT JOIN tbl_export_document_container G ON G.dispatch_id = C.dispatch_id AND G.export_type = 9 AND G.is_active = 1 
					LEFT JOIN tbl_export_documents G1 ON G1.export_id = A.id AND G1.export_type = 9 AND G1.is_active = 1 
					LEFT JOIN tbl_export_document_container H ON H.dispatch_id = C.dispatch_id AND H.export_type = 4 AND H.is_active = 1 
					LEFT JOIN tbl_export_documents H1 ON H1.export_id = A.id AND H1.export_type = 4 AND H1.is_active = 1 
					LEFT JOIN tbl_export_document_container I ON I.dispatch_id = C.dispatch_id AND I.export_type = 5 AND I.is_active = 1 
					LEFT JOIN tbl_export_documents I1 ON I1.export_id = A.id AND I1.export_type = 5 AND I1.is_active = 1 
					LEFT JOIN tbl_export_document_container J ON J.dispatch_id = C.dispatch_id AND J.export_type = 6 AND J.is_active = 1 
					LEFT JOIN tbl_export_documents J1 ON J1.export_id = A.id AND J1.export_type = 6 AND J1.is_active = 1 
					LEFT JOIN tbl_export_document_container K ON K.dispatch_id = C.dispatch_id AND K.export_type = 7 AND K.is_active = 1 
					LEFT JOIN tbl_export_documents K1 ON K1.export_id = A.id AND K1.export_type = 7 AND K1.is_active = 1  
					LEFT JOIN tbl_export_document_container L ON L.dispatch_id = C.dispatch_id AND L.export_type = 8 AND L.is_active = 1 
					LEFT JOIN tbl_export_documents L1 ON L1.export_id = A.id AND L1.export_type = 8 AND L1.is_active = 1 
					AND C.isduplicatedispatched = 0
					WHERE A.isactive = 1 AND B.isactive = 1 
					AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");
		} else {
			$query = $this->db->query("SELECT A.sa_number, CASE WHEN (A.shipped_date IS NULL or A.shipped_date = '') THEN '' ELSE DATE_FORMAT(STR_TO_DATE(A.shipped_date, '%d/%m/%Y'),'%M') END AS shipped_date, B.total_pieces,  
					B.net_volume, B.dispatch_id, C.container_number, B.cft_value, 
					CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(B.dispatch_id) END AS material_cost, 
					D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
					D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
					D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
					D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
					D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_export_container_details A
					INNER JOIN tbl_export_container B ON B.container_details_id = A.id
					INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
					LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = B.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
					AND C.isduplicatedispatched = 0
					WHERE A.isactive = 1 AND B.isactive = 1 
					AND A.id = $exportid AND A.origin_id = $originid GROUP BY B.dispatch_id");
		}


		return $query->result();
	}

	public function get_producttype_by_exportid($originid, $exportid)
	{
		$query = $this->db->query("SELECT product_type_id FROM tbl_export_container_details
			WHERE isactive = 1 AND id = $exportid AND origin_id = $originid");
		return $query->result();
	}

	public function get_total_volume($dispatchid, $netformula)
	{
		$strQuery = "SELECT SUM(total_pieces) AS total_pieces, SUM(L.netvolume) AS netvolume
            FROM (SELECT SUM(dispatch_pieces) AS total_pieces, 
            $netformula AS netvolume 
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_reception_data B ON B.reception_data_id = A.reception_data_id AND B.reception_id = A.reception_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
            AND B.isactive = 1 AND (B.isduplicatescanned = 0 OR B.isduplicatescanned IS NULL) 
            AND A.dispatch_id IN ($dispatchid) GROUP BY B.circumference_bought, B.length_bought) L";
		$query = $this->db->query($strQuery);
		return $query->result();
	}

	public function get_material_cost_by_dispatch($originid, $producttypeid, $dispatchid)
	{
		if ($producttypeid == 1 || $producttypeid == 3) {
			$producttypeid = "1,3";
		} else {
			$producttypeid = "2,4";
		}
		$query = $this->db->query("SELECT ROUND(SUM(material_cost),2) AS material_cost 
			FROM v_fetch_reception_dispatch WHERE dispatch_id = $dispatchid AND origin_id = $originid 
			AND product_type_id IN ($producttypeid)");
		return $query->result();
	}

	public function get_cost_summary_data_count($originid, $dispatchid, $containernumber)
	{
		$query = $this->db->query("SELECT COUNT(dispatch_id) as cnt FROM tbl_costsummary_files 
                    WHERE is_active = 1 AND origin_id = $originid AND dispatch_id = $dispatchid 
					AND container_number = '$containernumber'");
		return $query->result();
	}

	public function add_cost_summary($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_costsummary_files', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_cost_summary($dispatchid, $containernumber, $data)
	{
		$multiClause = array('dispatch_id' => $dispatchid, 'container_number' => $containernumber, 'is_active' => 1);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_costsummary_files', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_cost_summary_data($originid, $dispatchids)
	{
		$query = $this->db->query("SELECT dispatch_id, container_number, export_cost, freight_cost, tasa_cost, sales_cost 
					FROM tbl_costsummary_files 
                    WHERE is_active = 1 AND origin_id = $originid AND dispatch_id IN ($dispatchids)");
		return $query->result();
	}

	public function get_count_containers($containernumber, $dispatchid, $sanumber)
	{
		$query = $this->db->query("SELECT COUNT(B.dispatch_id) AS cnt FROM tbl_export_container_details A 
				INNER JOIN tbl_export_container B ON B.container_details_id = A.id AND B.isactive = 1
				INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
				WHERE C.container_number = '$containernumber' AND B.dispatch_id = $dispatchid AND A.sa_number = '$sanumber'");
		return $query->result();
	}

	public function check_exist_dispatch_cost($containernumber, $dispatchid, $sanumber)
	{
		$query = $this->db->query("SELECT COUNT(dispatch_id) AS cnt FROM tbl_dispatch_cost_details
				WHERE is_active = 1 AND container_number = '$containernumber' AND dispatch_id = $dispatchid AND sa_number = '$sanumber'");
		return $query->result();
	}

	public function add_dispatch_cost_details($data)
	{
		$this->db->insert_batch('tbl_dispatch_cost_details', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function update_dispatch_cost_details($dispatchid, $containernumber, $sanumber, $data)
	{
		$multiClause = array('dispatch_id' => $dispatchid, 'container_number' => $containernumber, 'sa_number' => $sanumber, 'is_active' => 1);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_dispatch_cost_details', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//SUPPLIER CREDIT
	public function add_inventory_expense_ledger($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_inventory_ledger', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	//SUPPLIER LEDGER
	public function get_ledger_details_by_supplier($supplierid)
	{
		$query = $this->db->query("SELECT CASE WHEN getsupplierledgeramount_type(A.supplier_id, 1) IS NULL THEN 0 ELSE 
                    getsupplierledgeramount_type(A.supplier_id, 1) END as creditamount, 
                    CASE WHEN getdebitedamount_bysupplier(A.supplier_id) IS NULL THEN 0 ELSE 
                    getdebitedamount_bysupplier(A.supplier_id) END as debitamount
                    FROM tbl_inventory_ledger A 
                    WHERE A.is_active = 1 AND A.supplier_id = $supplierid
                    GROUP BY A.supplier_id 
                    ORDER BY A.id");
		return $query->result();
	}

	public function get_debited_transaction_supplier($supplierid)
	{
		$query = $this->db->query("SELECT A.id, inventory_order, B.type_name, A.amount,
				DATE_FORMAT(STR_TO_DATE(A.expense_date, '%Y-%m-%d'), '%d/%m/%Y') as expense_date, 
				C.contract_type, A.contract_id, A.supplier_id, A.expense_type FROM tbl_inventory_ledger A
				INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type 
				INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
				WHERE A.supplier_id = $supplierid AND A.ledger_type = 2 AND A.is_active = 1 
				ORDER BY A.created_date DESC, A.expense_type");
		return $query->result();
	}

	public function get_credited_transaction_supplier($supplierid)
	{
		$query = $this->db->query("SELECT A.id, A.amount, A.supplier_id, A.contract_id,
				DATE_FORMAT(A.expense_date, '%d/%m/%Y') as expense_date, E.fullname AS fullname, 
				A.user_type FROM tbl_inventory_ledger A 
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				INNER JOIN tbl_suppliers C ON C.id = B.supplier_id 
				LEFT JOIN tbl_user_registration E ON E.userid = A.created_by
				where A.is_active = 1 AND A.supplier_id = $supplierid AND expense_type IN (0,1) AND ledger_type = 1 ORDER BY A.created_date DESC");
		return $query->result();
	}

	//PM LEDGER
	public function get_ledger_details_by_purchasemanager($supplierid)
	{
		$query = $this->db->query("SELECT CASE WHEN getpurchasemanagerledgeramount_type($supplierid, 1) IS NULL THEN 0 ELSE 
				getpurchasemanagerledgeramount_type($supplierid, 1) END as creditamount, 
				CASE WHEN getpurchasemanageramount_type($supplierid) IS NULL THEN 0 ELSE 
				getpurchasemanageramount_type($supplierid) END as debitamount");
		return $query->result();
	}

	public function get_debited_transaction_purchasemanager($supplierid)
	{
		$query = $this->db->query("SELECT A.id, A.inventory_order, C.type_name, A.amount, 
		DATE_FORMAT(STR_TO_DATE(A.expense_date, '%Y-%m-%d'), '%d/%m/%Y') as expense_date, D.supplier_name FROM tbl_inventory_ledger A 
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				INNER JOIN tbl_inventor_ledger_types C ON C.id = A.expense_type 
				INNER JOIN tbl_suppliers D ON D.id = A.supplier_id
				WHERE A.is_active = 1 AND A.pm_ledger_type = 2 AND expense_type IN (0,1) AND A.created_by = $supplierid 
				AND A.user_type = 2");
		return $query->result();
	}

	public function get_credited_transaction_purchasemanager($supplierid)
	{
		$query = $this->db->query("SELECT A.id, A.amount,
				DATE_FORMAT(STR_TO_DATE(A.expense_date, '%Y-%m-%d'), '%d/%m/%Y') as expense_date, 
				CASE WHEN A.contract_id = 0 THEN getpurchasemanagername_byid(A.created_by) 
				ELSE getsuppliername_bysupplierid(A.supplier_id) END AS fullname FROM tbl_inventory_ledger A 
				WHERE A.supplier_id = $supplierid AND A.ledger_type = 1 AND A.is_active = 1 AND A.user_type = 2");
		return $query->result();
	}

	//PURCHASE MANAGER LIQUIDATION
	public function get_suppliers_by_contract($contractid)
	{
		$query = $this->db->query("SELECT DISTINCT A.supplier_id, 
				CONCAT(B.supplier_name, ' - ', B.supplier_code) as supplier_name FROM tbl_farm A
				INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
				WHERE is_active = 1 AND contract_id = $contractid");
		return $query->result();
	}

	public function get_inventory_ledger_contract_purchase_manager($contractid, $supplierid, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		if ($supplierid == 0) {
			$query = $this->db->query("SELECT DATE_FORMAT(A.purchase_date, '%d %M %Y') AS expense_date, 'Total Wood Value' AS type_name, A.inventory_order, A.total_value as amount, C.supplier_name, '1' as type  
					FROM tbl_farm A 
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
					WHERE A.is_active = 1 AND A.contract_id = $contractid
					
					UNION ALL

					SELECT DATE_FORMAT(STR_TO_DATE(A.expense_date,'%Y-%m-%d'),'%d %M %Y') AS expense_date, 
					CASE WHEN (A.inventory_order IS NULL OR A.inventory_order = '') THEN B.type_name ELSE B.type_name END as type_name, A.inventory_order, -1 * A.amount, C.supplier_name, '2' as type 
					FROM tbl_inventory_ledger A
					INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
					WHERE contract_id = $contractid AND A.is_active = 1 
					AND pm_ledger_type = 2 AND user_type = 2 AND A.expense_type IN (0,1,2,3)");
		} else {
			$query = $this->db->query("SELECT DATE_FORMAT(A.purchase_date, '%d %M %Y') AS expense_date, 'Total Wood Value' AS type_name, A.inventory_order, A.total_value as amount, C.supplier_name, '1' as type  
					FROM tbl_farm A 
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
					WHERE A.is_active = 1 AND A.contract_id = $contractid AND A.supplier_id = $supplierid
					
					UNION ALL

					SELECT DATE_FORMAT(STR_TO_DATE(A.expense_date,'%Y-%m-%d'),'%d %M %Y') AS expense_date, 
					CASE WHEN (A.inventory_order IS NULL OR A.inventory_order = '') THEN B.type_name ELSE B.type_name END as type_name, A.inventory_order, -1 * A.amount, C.supplier_name, '2' as type 
					FROM tbl_inventory_ledger A
					INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
					WHERE contract_id = $contractid AND A.supplier_id = $supplierid
					AND A.is_active = 1 AND pm_ledger_type = 2 AND user_type = 2 AND A.expense_type IN (0,1,2,3)");
		}

		return $query->result();
	}

	public function get_all_supplier_ledger_by_origin($originid)
	{
		$query = $this->db->query("SELECT A.supplier_id, supplier_name, supplier_code,
					CASE WHEN getsupplierledgeramount_type(A.supplier_id, 1) IS NULL THEN 0 ELSE 
					getsupplierledgeramount_type(A.supplier_id, 1) END as creditamount, 
					CASE WHEN getdebitedamount_bysupplier(A.supplier_id) IS NULL THEN 0 ELSE 
					getdebitedamount_bysupplier(A.supplier_id) END as debitamount
					FROM tbl_inventory_ledger A 
					INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
					WHERE A.is_active = 1 AND B.origin_id = $originid
					GROUP BY A.supplier_id 
					ORDER BY B.id");
		return $query->result();
	}

	public function get_supplier_credit_transactions($supplierid)
	{
		$query = $this->db->query("SELECT A.id, C.supplier_name, A.amount,
				DATE_FORMAT(STR_TO_DATE(A.expense_date, '%Y-%m-%d'), '%d %M %Y') as expense_date 
				FROM tbl_inventory_ledger A 
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				INNER JOIN tbl_suppliers C ON C.id = B.supplier_id 
				where A.is_active = 1 AND A.supplier_id = $supplierid AND expense_type IN (0,1) AND ledger_type = 1 
				ORDER BY A.created_date DESC");
		return $query->result();
	}

	public function get_supplier_debit_transactions($supplierid)
	{
		$query = $this->db->query("SELECT A.id, inventory_order, B.type_name, D.supplier_name, A.amount,
				DATE_FORMAT(STR_TO_DATE(A.expense_date, '%Y-%m-%d'), '%d %M %Y') as expense_date, 
				C.contract_type, A.contract_id, A.supplier_id, A.expense_type FROM tbl_inventory_ledger A
				INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type 
				INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
				INNER JOIN tbl_suppliers D ON D.id = A.supplier_id
				WHERE A.supplier_id = $supplierid AND A.ledger_type = 2 AND A.is_active = 1 
				ORDER BY A.created_date DESC, A.expense_type");
		return $query->result();
	}

	public function get_total_volume_by_supplier($supplierid)
	{
		$query = $this->db->query("SELECT SUM(total_volume) as total_volume
				FROM tbl_contract_inventory_mapping WHERE supplier_id = $supplierid");
		return $query->result();
	}

	public function get_supplier_name_ledger($supplierid)
	{
		$query = $this->db->query("SELECT DISTINCT C.id, C.supplier_name, C.supplier_id FROM tbl_inventory_ledger A 
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				INNER JOIN tbl_suppliers C ON C.id = A.supplier_id
				WHERE A.supplier_id IN ($supplierid) AND A.ledger_type = 2 AND A.is_active = 1");
		return $query->result();
	}

	public function fetch_inventory_report_warehouse_bulk($supplierId, $fromDate, $toDate, $originId, $farmId)
	{
		if ($supplierId == 0) {
			$query = "SELECT B.contract_code, A.inventory_order, B.product, B.product_type, 
				B.supplier_id, B.unit_of_purchase,
				purchase_allowance, purchase_allowance_length, A.purchase_date, B.existing_price_condition FROM tbl_farm A
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				WHERE B.origin_id = $originId AND A.is_active = 1 AND A.purchase_date BETWEEN '$fromDate' AND '$toDate'";
		} else {
			$query = "SELECT B.contract_code, A.inventory_order, B.product, B.product_type, 
				B.supplier_id, B.unit_of_purchase,
				purchase_allowance, purchase_allowance_length, A.purchase_date, B.existing_price_condition FROM tbl_farm A
				INNER JOIN tbl_supplier_purchase_contract B ON B.contract_id = A.contract_id 
				WHERE B.origin_id = $originId AND B.supplier_id = $supplierId
				AND A.is_active = 1 AND A.purchase_date BETWEEN '$fromDate' AND '$toDate'";
		}

		if ($farmId != "") {
			$query = $query . " AND A.farm_id IN ($farmId)";
		}

		$query = $query . " ORDER BY A.farm_id, STR_TO_DATE(A.purchase_date,'%Y-%m-%d')";

		$query = $this->db->query($query);

		return $query->result();
	}

	public function get_inventory_ledger_contract_bulk($supplierId, $fromDate, $toDate, $langcode, $farmId, $originId)
	{
		$this->db->query("SET lc_time_names='$langcode'");

		if ($supplierId == 0) {

			$query = "SELECT DATE_FORMAT(STR_TO_DATE(A.expense_date,'%Y-%m-%d'),'%d %M %Y') AS expense_date, 
					B.type_name, A.inventory_order, A.amount, C.supplier_name
					FROM tbl_inventory_ledger A 
					INNER JOIN tbl_farm F ON F.inventory_order = A.inventory_order AND F.is_active = 1 AND F.supplier_id = A.supplier_id 
					INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type 
					INNER JOIN tbl_suppliers C ON C.id = A.supplier_id
					WHERE F.purchase_date BETWEEN '$fromDate' AND '$toDate'  
					AND A.is_active = 1 AND F.origin_id = $originId ";
		} else {
			$query = "SELECT DATE_FORMAT(STR_TO_DATE(A.expense_date,'%Y-%m-%d'),'%d %M %Y') AS expense_date, 
				B.type_name, A.inventory_order, A.amount, C.supplier_name
				FROM tbl_inventory_ledger A 
				INNER JOIN tbl_farm F ON F.inventory_order = A.inventory_order AND F.is_active = 1 AND F.supplier_id = $supplierId 
				INNER JOIN tbl_inventor_ledger_types B ON B.id = A.expense_type
				INNER JOIN tbl_suppliers C ON C.id = A.supplier_id
				WHERE F.purchase_date BETWEEN '$fromDate' AND '$toDate' AND A.supplier_id = $supplierId 
				AND A.is_active = 1 AND F.origin_id = $originId ";
		}

		if ($farmId != "") {
			$query = $query . " AND F.farm_id IN ($farmId) ORDER BY B.id, A.id, STR_TO_DATE(F.purchase_date,'%Y-%m-%d')";
		} else {

			$query = $query . " ORDER BY B.id, A.id, STR_TO_DATE(F.purchase_date,'%Y-%m-%d')";
		}

		$query = $this->db->query($query);

		return $query->result();
	}

	public function get_farm_detail_bulk($supplierId, $fromDate, $toDate, $originid, $inventoryOrder, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		if ($supplierId == 0) {
			$query = $this->db->query("SELECT A.supplier_id, A.contract_id, A.farm_id, DATE_FORMAT(A.purchase_date, '%d %M %Y') AS purchase_date, A.inventory_order, 
						A.plate_number, B.supplier_name, A.supplier_taxes_array, A.logistics_taxes_array, A.service_taxes_array, 
						A.logistic_cost, A.service_cost, A.adjustment, A.purchase_unit_id, A.exchange_rate, 
						C.invoice_number, D.purchase_allowance, D.purchase_allowance_length    
						FROM tbl_farm A 
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						INNER JOIN tbl_contract_inventory_mapping C ON C.contract_id = A.contract_id AND C.supplier_id = A.supplier_id AND C.is_active = 1 
						AND C.inventory_order = A.inventory_order 
						INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
						WHERE A.origin_id = $originid 
						AND A.inventory_order = '$inventoryOrder' AND A.purchase_date BETWEEN '$fromDate' AND '$toDate' 
						AND A.is_active = 1");
		} else {
			$query = $this->db->query("SELECT A.supplier_id, A.contract_id, A.farm_id, DATE_FORMAT(A.purchase_date, '%d %M %Y') AS purchase_date, A.inventory_order, 
						A.plate_number, B.supplier_name, A.supplier_taxes_array, A.logistics_taxes_array, A.service_taxes_array, 
						A.logistic_cost, A.service_cost, A.adjustment, A.purchase_unit_id, A.exchange_rate, 
						C.invoice_number, D.purchase_allowance, D.purchase_allowance_length    
						FROM tbl_farm A 
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
						INNER JOIN tbl_contract_inventory_mapping C ON C.contract_id = A.contract_id AND C.supplier_id = A.supplier_id AND C.is_active = 1 
						AND C.inventory_order = A.inventory_order 
						INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
						WHERE A.supplier_id = $supplierId AND A.origin_id = $originid 
						AND A.inventory_order = '$inventoryOrder' AND A.purchase_date BETWEEN '$fromDate' AND '$toDate' 
						AND A.is_active = 1");
		}
		return $query->result();
	}

	public function get_inventory_by_supplierid($supplierid)
	{
		$query = $this->db->query("SELECT farm_id, inventory_order FROM tbl_farm WHERE supplier_id = $supplierid AND is_active = 1");
		return $query->result();
	}

	public function delete_credits($transactionid, $supplierid, $contractid, $userid)
	{
		$updateData = array(
			"is_active" => 0,
			"updated_by" => $userid,
		);
		$multiClause = array('id' => $transactionid, 'contract_id' => $contractid, 'supplier_id' => $supplierid);
		$this->db->where($multiClause);
		$this->db->set('updated_date', 'NOW()', FALSE);
		if ($this->db->update('tbl_inventory_ledger', $updateData)) {
			return true;
		} else {
			return false;
		}
	}

	public function fetch_closed_container_data1($originid, $dispatchids, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		$query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
				C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
				get_material_cost_by_dispatch_id_export(C.dispatch_id) AS material_cost, 
				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
				AND C.isduplicatedispatched = 0
				WHERE C.isactive = 1
				AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");

		// $query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
		// C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
		// CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(C.dispatch_id) END AS material_cost, 
		// D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
		// LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// AND C.isduplicatedispatched = 0
		// WHERE C.isactive = 1
		// AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");

		// 		$query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
		// 				C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
		// 				(A.unit_price * C.total_pieces) AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE C.isactive = 1
		// 				AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");
		return $query->result();
	}

	public function fetch_closed_container_data($originid, $dispatchids, $langcode)
	{
		$this->db->query("SET lc_time_names='$langcode'");
		// $query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
		// 		C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
		// 		get_material_cost_by_dispatch_id_export(C.dispatch_id) AS material_cost, 
		// 		D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 		D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 		D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 		D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 		D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
		// 		LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 		AND C.isduplicatedispatched = 0
		// 		WHERE C.isactive = 1
		// 		AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");

		$query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
        				C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
                        '' AS custom_invoice, 0 AS custom_value, 
                        '' AS itr_invoice, 0 AS itr_value, 
                        '' AS port_invoice, 0 AS port_value, 
                        '' AS shipping_invoice, 0 AS shipping_value, 
                        '' AS fumigation_invoice, 0 AS fumigation_value, 
                        '' AS phyto_invoice, 0 AS phyto_value, 
                        '' AS coteros_invoice, 0 AS coteros_value, 
                        '' AS incentive_invoice, 0 AS incentive_value,
                        '' AS remobilization_invoice, 0 AS remobilization_value, 
						get_material_cost_by_dispatch_id_export(C.dispatch_id) AS material_cost
                        FROM tbl_dispatch_container C 
        				WHERE C.isduplicatedispatched = 0 AND C.isactive = 1 
						AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");

		// $query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
		// C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
		// CASE WHEN A.product_type_id = 1 THEN B.material_cost ELSE get_material_cost_by_dispatch_id_export(C.dispatch_id) END AS material_cost, 
		// D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
		// LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// AND C.isduplicatedispatched = 0
		// WHERE C.isactive = 1
		// AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");

		// 		$query = $this->db->query("SELECT '---' AS sa_number, '' AS shipped_date, C.total_pieces, 
		// 				C.total_volume AS net_volume, C.dispatch_id, C.container_number, 0 AS cft_value, 
		// 				(A.unit_price * C.total_pieces) AS material_cost, 
		// 				D.custom_agency_invoice_number, D.custom_agency_cost, D.transport_invoice_number, 
		// 				D.transport_cost, D.port_invoice_number, D.port_cost, D.phyto_cost, D.fumigation_invoice_number, 
		// 				D.fumigation_cost, D.dhl_invoice_number, D.dhl_cost, D.coteros_cost, D.provistional_stamp_cost, 
		// 				D.incentive, D.mobilization_cost, D.shipping_invoice_number, D.warehouse_port_cost, D.freight_cost, D.sales_cost, 
		// 				D.exchange_rate, D.loss_profit, D.document_number, D.observation FROM tbl_dispatch_container C 
		// 				LEFT JOIN tbl_dispatch_cost_details D ON D.dispatch_id = C.dispatch_id AND D.container_number = C.container_number AND D.is_active = 1 
		// 				AND C.isduplicatedispatched = 0
		// 				WHERE C.isactive = 1
		// 				AND C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid GROUP BY C.dispatch_id");
		return $query->result();
	}

	public function get_ledger_details_by_buyer($buyerid, $originid)
	{
		if ($originid > 0) {
			$query = $this->db->query("SELECT COUNT(X.sa_number) AS total_shipments, SUM(X.total_advance_cost) AS total_advance_cost, 
				SUM(X.total_service_cost) AS total_service_cost, SUM(X.total_claim_amount) AS total_claim_amount, 
				SUM(X.total_invoice_value) AS total_invoice_value, SUM(X.total_containers) AS total_containers, 
				SUM(X.total_weight) AS total_weight, SUM(X.total_volume) AS total_volume, SUM(X.total_pieces) AS total_pieces, 
				SUM(X.total_sales_value) AS total_sales_value
				FROM (SELECT B.sa_number, SUM(A.total_advance_cost) AS total_advance_cost, 
				SUM(A.total_service_cost) AS total_service_cost, SUM(A.claim_amount) AS total_claim_amount, 
				SUM(A.total_invoice_value) AS total_invoice_value, SUM(A.total_containers) AS total_containers, 
				CASE WHEN B.origin_id = 3 THEN SUM(A.total_volume) ELSE 0 END AS total_weight, 
                CASE WHEN (B.origin_id = 1 OR B.origin_id = 2 OR B.origin_id = 4) THEN SUM(A.total_volume) ELSE 0 END AS total_volume, 
      			SUM(B.total_pieces) AS total_pieces, SUM(A.total_sales_value) AS total_sales_value
				FROM tbl_sales_buyer_ledger A 
				INNER JOIN tbl_export_container_details B ON B.id = A.export_id 
				WHERE A.is_active = 1 AND A.buyer_id = $buyerid AND B.origin_id = $originid AND A.ledger_type = 2
				GROUP BY A.export_id) X");
		} else {
			$query = $this->db->query("SELECT COUNT(X.sa_number) AS total_shipments, SUM(X.total_advance_cost) AS total_advance_cost, 
				SUM(X.total_service_cost) AS total_service_cost, SUM(X.total_claim_amount) AS total_claim_amount, 
				SUM(X.total_invoice_value) AS total_invoice_value, SUM(X.total_containers) AS total_containers, 
				SUM(X.total_weight) AS total_weight, SUM(X.total_volume) AS total_volume, SUM(X.total_pieces) AS total_pieces, 
				SUM(X.total_sales_value) AS total_sales_value
				FROM (SELECT B.sa_number, SUM(A.total_advance_cost) AS total_advance_cost, 
				SUM(A.total_service_cost) AS total_service_cost, SUM(A.claim_amount) AS total_claim_amount, 
				SUM(A.total_invoice_value) AS total_invoice_value, SUM(A.total_containers) AS total_containers, 
				CASE WHEN B.origin_id = 3 THEN SUM(A.total_volume) ELSE 0 END AS total_weight, 
                CASE WHEN (B.origin_id = 1 OR B.origin_id = 2 OR B.origin_id = 4) THEN SUM(A.total_volume) ELSE 0 END AS total_volume, 
      			SUM(B.total_pieces) AS total_pieces, SUM(A.total_sales_value) AS total_sales_value
				FROM tbl_sales_buyer_ledger A 
				INNER JOIN tbl_export_container_details B ON B.id = A.export_id 
				WHERE A.is_active = 1 AND A.buyer_id = $buyerid AND A.ledger_type = 2
				GROUP BY A.export_id) X");
		}

		return $query->result();
	}

	public function get_ledger_transaction_details_by_buyer($buyerid, $originid)
	{
		if ($originid > 0) {
			$query = $this->db->query("SELECT B.sa_number, SUM(A.total_advance_cost) AS total_advance_cost, 
				SUM(A.total_service_cost) AS total_service_cost, SUM(A.claim_amount) AS total_claim_amount, 
				SUM(A.total_invoice_value) AS total_invoice_value, SUM(A.total_containers) AS total_containers, 
				CASE WHEN B.origin_id = 3 THEN SUM(A.total_volume) ELSE 0 END AS total_weight, 
                CASE WHEN (B.origin_id = 1 OR B.origin_id = 2 OR B.origin_id = 4) THEN SUM(A.total_volume) ELSE 0 END AS total_volume, 
      			SUM(B.total_pieces) AS total_pieces, getorigin_namebyid(B.origin_id) AS origin, SUM(A.total_sales_value) AS total_sales_value
				FROM tbl_sales_buyer_ledger A 
				INNER JOIN tbl_export_container_details B ON B.id = A.export_id 
				WHERE A.is_active = 1 AND A.buyer_id = $buyerid AND B.origin_id = $originid AND A.ledger_type = 2
				GROUP BY A.export_id 
                ORDER BY B.origin_id ASC, B.id DESC");
		} else {
			$query = $this->db->query("SELECT B.sa_number, SUM(A.total_advance_cost) AS total_advance_cost, 
				SUM(A.total_service_cost) AS total_service_cost, SUM(A.claim_amount) AS total_claim_amount, 
				SUM(A.total_invoice_value) AS total_invoice_value, SUM(A.total_containers) AS total_containers, 
				CASE WHEN B.origin_id = 3 THEN SUM(A.total_volume) ELSE 0 END AS total_weight, 
                CASE WHEN (B.origin_id = 1 OR B.origin_id = 2 OR B.origin_id = 4) THEN SUM(A.total_volume) ELSE 0 END AS total_volume, 
      			SUM(B.total_pieces) AS total_pieces, getorigin_namebyid(B.origin_id) AS origin, SUM(A.total_sales_value) AS total_sales_value
				FROM tbl_sales_buyer_ledger A 
				INNER JOIN tbl_export_container_details B ON B.id = A.export_id 
				WHERE A.is_active = 1 AND A.buyer_id = $buyerid AND A.ledger_type = 2
				GROUP BY A.export_id 
                ORDER BY B.origin_id ASC, B.id DESC");
		}

		return $query->result();
	}

	public function get_gc_report_data($originid, $buyerid)
	{
		try {
			$sql = "SELECT
				X.id,
				X.sa_number,
				X.dispatch_id,
                STR_TO_DATE(X.shipped_date, '%d/%m/%Y') AS shipped_date, 
                CASE WHEN (X.bl_no IS NULL OR X.bl_no = '') THEN X.client_pno ELSE X.bl_no END AS bl_no, 
                X.bl_date,
                X.vessel_name,
                X.shipping_line, 
                X.pod_name,
				X.container_number AS container_number,
				X.measurement_system,
				X.circumference_allowance_export,
				X.length_allowance_export,
				CASE WHEN X.origin_id = 3 THEN get_netlbs_bydispatchid(X.dispatch_id) ELSE X.total_gross_volume END AS total_gross_volume,
				X.total_net_volume,
				X.avg_circumference,
				CASE WHEN X.origin_id = 3 THEN 0 ELSE ROUND(X.total_gross_volume / X.total_pieces * 35.315, 2) END AS gross_cft,
				CASE WHEN X.origin_id = 3 THEN 0 ELSE ROUND(X.total_net_volume / X.total_pieces * 35.315, 2) END AS net_cft,
				X.total_pieces,
				CASE WHEN X.origin_id = 3 THEN 'SYP' WHEN X.product_type = 1 THEN 'SHORTS' WHEN X.product_type = 2 THEN 'SEMI' ELSE 'LONGS' END AS product_type,
				X.base_price,
				CASE
					WHEN X.origin_id = 3
					THEN X.container_unit_price
					ELSE CASE
						WHEN X.product_type = 1 THEN
							CASE
								WHEN X.gross_cft >= 2 THEN 
									X.base_price + CEIL((X.gross_cft - 2.24) / 0.25) * 15 + 
									CASE 
										WHEN X.gross_cft > 5.24 THEN CASE WHEN X.enabled_jump = 1 THEN 20 ELSE 0 END 
										ELSE 0 
									END
								ELSE 
									X.base_price - CEIL((2 - X.gross_cft) / 0.25) * 15
							END
						WHEN X.product_type IN (2, 3)
						THEN CASE
								WHEN X.avg_circumference >= 60 THEN 
									X.base_price + CEIL((X.avg_circumference - 64.99) / 4.99) * 30 + 
									CASE 
										WHEN X.avg_circumference >= 100 THEN CASE WHEN X.enabled_jump = 1 THEN 20 ELSE 0 END 
										ELSE 0 
									END
								ELSE 
									X.base_price - CEIL((60 - X.avg_circumference) / 4.99) * 50
							END
						ELSE 0
					END
				END AS sales_price,
				get_container_price_bydispatchid(X.dispatch_id, X.origin_id) AS container_price,
				X.buyer_name, X.seller_name, X.diameter_text, X.length_text, X.seal_number, X.avg_length  
			FROM (
				SELECT
					A.id,
					A.sa_number,
					C.dispatch_id,
					C.container_number,
					A.origin_id,
                	A.shipped_date, 
                	A.bl_no, 
					A.client_pno,
                	A.bl_date,
                	A.vessel_name, 
                	H.shipping_line, 
                	CONCAT(I.pod_name, ', ', J.code) AS pod_name,
					getcalculateddispatch_volume_byid(B.dispatch_id, A.measurement_system, 0, 0, E.circ_adjustment) AS total_gross_volume,
					getcalculateddispatch_volume_byid(B.dispatch_id, A.measurement_system, E.circ_allowance, E.length_allowance, E.circ_adjustment) AS total_net_volume,
					CASE
						WHEN A.origin_id = 3
						THEN 0
						ELSE get_average_circumference_by_container(C.dispatch_id, 0)
					END AS avg_circumference,
					CASE WHEN A.origin_id = 3 THEN 0 ELSE get_average_length_by_container(C.dispatch_id) END AS avg_length,
					get_totalpieces_by_dispatchid(C.dispatch_id) AS total_pieces,
					CASE
						WHEN A.origin_id = 3
						THEN A.product_type_id
						ELSE get_producttype_by_length(C.dispatch_id)
					END AS product_type, A.measurement_system,
					E.circ_allowance AS circumference_allowance_export,
					E.length_allowance AS length_allowance_export,
					CASE
						WHEN A.origin_id = 3
						THEN C.unit_price
						ELSE CASE
							WHEN get_producttype_by_length(C.dispatch_id) = 1
							THEN E.shorts_base_price
							WHEN get_producttype_by_length(C.dispatch_id) = 2
							THEN E.semi_base_price
							WHEN get_producttype_by_length(C.dispatch_id) = 3
							THEN E.long_base_price
							ELSE 0
						END
					END AS base_price,
					CASE
						WHEN A.origin_id = 3
						THEN 0
						ELSE CASE
							WHEN get_producttype_by_length(C.dispatch_id) = 1
							THEN E.enabled_jump_shorts
							WHEN get_producttype_by_length(C.dispatch_id) = 2
							THEN E.enabled_jump_semi
							WHEN get_producttype_by_length(C.dispatch_id) = 3
							THEN E.enabled_jump_long
							ELSE 0
						END
					END AS enabled_jump,
					ROUND(
						getcalculateddispatch_volume_byid(B.dispatch_id, A.measurement_system, 0, 0, 0) / get_totalpieces_by_dispatchid(C.dispatch_id) * 35.315, 2) AS gross_cft, 
						F.buyer_name, G.seller_name, C.diameter_text, C.length_text, C.unit_price AS container_unit_price, 
						C.seal_number    
				FROM tbl_export_container_details A
				INNER JOIN tbl_export_container B
					ON B.container_details_id = A.id
				INNER JOIN tbl_dispatch_container C
					ON C.dispatch_id = B.dispatch_id
				INNER JOIN (
					SELECT
						id, buyer_id, seller_id, 
						export_id,
						shorts_base_price,
						semi_base_price,
						long_base_price,
						enabled_jump_shorts,
						enabled_jump_semi,
						enabled_jump_long,
						circ_allowance,
						length_allowance,
						circ_adjustment,
						measurement_system, service_sales_percentage, advance_cost, claim_amount
					FROM tbl_export_invoice_history 
					WHERE 
						is_active = 1";

			if ($buyerid > 0) {
				$sql = $sql . " AND buyer_id = $buyerid";
			}

			$sql = $sql . ") E ON E.export_id = A.id 
				INNER JOIN tbl_master_buyers F ON F.id = E.buyer_id 
				INNER JOIN tbl_master_sellers G ON G.id = E.seller_id 
                INNER JOIN tbl_shippingline_master H ON H.id = A.liner 
                INNER JOIN tbl_export_pod I ON I.id = A.pod 
                INNER JOIN tbl_countries J ON J.id = I.country_id 
				WHERE
					A.isactive = 1 AND A.origin_id = $originid AND B.isactive = 1 
				GROUP BY 
					C.container_number 
			) X
			ORDER BY
				STR_TO_DATE(X.shipped_date, '%d/%m/%Y') ASC, X.sa_number ASC";

			$query = $this->db->query($sql);
			return $query->result();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function get_container_cost($exportid, $dispatchid)
    {
        $strQuery = "SELECT dispatch_id, unit_price, exchange_rate FROM tbl_export_document_container_cost 
            WHERE is_active = 1 AND export_id = $exportid AND dispatch_id = $dispatchid";
        $query = $this->db->query($strQuery);
        return $query->result();
    }
}
