<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Master_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//ORIGINS
	public function all_active_origins()
	{
		$query = $this->db->query("SELECT id, origin_name FROM tbl_origins WHERE is_active = 1");
		return $query->result();
	}

	public function all_applicable_origins($origins)
	{
		$query = $this->db->query("SELECT id, origin_name FROM tbl_origins WHERE id IN ($origins)");
		return $query->result();
	}

	public function get_origin_iso3_code($originid)
	{
		$query = $this->db->query("SELECT origin_iso3_code, origin_name FROM tbl_origins WHERE id = $originid");
		return $query->result();
	}

	//ORIGIN SETTING
	public function get_company_settings_by_origin($originid)
	{
		$query = $this->db->query("SELECT circumference_allowance, length_allowance, circumference_allowance_export, 
				length_allowance_export, mandatory_reception_creation FROM tbl_origin_company_settings WHERE origin_id = $originid");
		return $query->result();
	}

	//LANGUAGES
	public function fetch_languages()
	{
		$query = $this->db->query("SELECT id, language_name FROM tbl_languages WHERE is_active = 1");
		return $query->result();
	}

	//TIMEZONE
	public function fetch_origin_timezones()
	{
		$query = $this->db->query("SELECT A.id, 
				CONCAT(C.name,' (UTC', TRIM(SPLIT_STR(B.timezone_offset, ':00',1)+0),')') AS name 
				FROM tbl_origin_timezones A 
				INNER JOIN tbl_master_timezones B ON B.id = A.timezone_id 
				INNER JOIN tbl_countries C ON C.id = B.country_id
				WHERE A.is_active = 1");
		return $query->result();
	}

	//PRODUCTS
	public function all_products()
	{
		$query = $this->db->query("SELECT A.product_id, A.product_name, A.product_desc, 
					A.isactive, B.icon_name, getapplicableorigins_byid(A.origin_id) as origin FROM tbl_product_master A 
					INNER JOIN tbl_product_icons B ON B.icon_id = A.product_icon");
		return $query->result();
	}

	public function all_products_origin($origin_id)
	{
		$query = $this->db->query("SELECT A.product_id, A.product_name, A.product_desc, 
					A.isactive, B.icon_name, getapplicableorigins_byid(A.origin_id) as origin FROM tbl_product_master A 
					INNER JOIN tbl_product_icons B ON B.icon_id = A.product_icon 
					WHERE A.origin_id = $origin_id");
		return $query->result();
	}

	public function all_active_products()
	{
		$query = $this->db->query("SELECT A.product_id, A.product_name FROM tbl_product_master A WHERE A.isactive = 1");
		return $query->result();
	}

	public function all_product_icons()
	{
		$query = $this->db->query("SELECT icon_id, icon_name FROM tbl_product_icons WHERE isactive = 1");
		return $query->result();
	}

	public function get_product_detail_by_id($id)
	{
		$query = $this->db->query("SELECT A.product_id, A.product_name, A.product_desc, A.product_icon, A.isactive, A.origin_id 
					FROM tbl_product_master A 
					WHERE A.product_id = $id");
		return $query->result();
	}

	public function get_product_byorigin($originid)
	{
		$query = $this->db->query("SELECT A.product_id, A.product_name 
					FROM tbl_product_master A 
					WHERE A.origin_id = $originid AND A.isactive = 1");
		return $query->result();
	}

	public function get_product_type()
	{
		$query = $this->db->query("SELECT type_id, product_type_name FROM tbl_product_types WHERE isactive = 1 LIMIT 2");
		return $query->result();
	}

	public function get_product_type_by_id($id)
	{
		$query = $this->db->query("SELECT type_id, product_type_name FROM tbl_product_types WHERE isactive = 1 AND type_id = $id");
		return $query->result();
	}

	public function add_product($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_product_master', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_product($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('product_id', $id);
		if ($this->db->update('tbl_product_master', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function all_product_types()
	{
		$query = $this->db->query("SELECT type_id, product_type_name FROM tbl_product_types WHERE option_id <> 3");
		return $query->result();
	}

	//SUPPLIERS
	public function all_suppliers()
	{
		$query = $this->db->query("SELECT id, supplier_code, supplier_name, supplier_id, getsupplierroles_byid(id) as roles, 
			B.fullname as updatedby, A.isactive, getsupplierproducts_id_web(id) as products, getapplicableorigins_byid(origin_id) as origin
		FROM tbl_suppliers A 
		INNER JOIN tbl_user_registration B ON B.userid = A.updatedby");
		return $query->result();
	}

	public function all_suppliers_origin($originid)
	{
		$query = $this->db->query("SELECT id, supplier_code, supplier_name, supplier_id, getsupplierroles_byid(id) as roles, 
			B.fullname as updatedby, A.isactive, getsupplierproducts_id_web(id) as products, getapplicableorigins_byid(origin_id) as origin
		FROM tbl_suppliers A 
		INNER JOIN tbl_user_registration B ON B.userid = A.updatedby WHERE A.origin_id = $originid");
		return $query->result();
	}

	public function get_supplier_detail_by_id($id)
	{
		$query = $this->db->query("SELECT id, supplier_code, supplier_name, supplier_id, 
				getsupplierroles_byid(id) as roles, 
				CASE WHEN (company_name IS NULL OR company_name = '') THEN '' ELSE company_name END as company_name, 
				CASE WHEN (company_id IS NULL OR company_id = '') THEN '' ELSE company_id END as company_id, supplier_address, 
				A.is_iva_enabled, A.iva_value, A.is_retencion_enabled, A.retencion_value, A.is_reteica_enabled, 
				A.reteica_value, A.is_iva_provider_enabled, A.iva_provider_value,  
            	A.is_retencion_provider_enabled, A.retencion_provider_value, A.is_reteica_provider_enabled, 
				A.reteica_provider_value,
				getsupplierproducts_id_web(id) as products,
				CASE WHEN getsupplierbank_id_web(id) IS NULL THEN '' ELSE getsupplierbank_id_web(id) END as banks, 
				A.isactive, getapplicableorigins_byid(A.origin_id) as origin, A.origin_id
				FROM tbl_suppliers A 
				INNER JOIN tbl_user_registration B ON B.userid = A.updatedby WHERE A.id = $id");
		return $query->result();
	}

	public function supplier_record_count($originid)
	{
		$this->db->where("origin_id", $originid);
        $this->db->from("tbl_suppliers");
		return $this->db->count_all_results();
		//return $this->db->count_all("tbl_suppliers");
	}

	public function get_supplierbank_byid($supplierid)
	{
		$query = $this->db->query("SELECT A.bank_name, A.bank_accountnumber, A.bank_holdername, A.bank_accounttype 
					FROM tbl_suppliers_bankdetails A 
					WHERE A.supplier_id = $supplierid AND A.isactive = 1");
		return $query->result();
	}

	public function get_supplierproduct_byid($supplierid)
	{
		$query = $this->db->query("SELECT product_name, getsupplierproductype_supplierproduct(product_id, supplier_id) as product_type 
				FROM tbl_suppliers_products WHERE is_active = 1 AND supplier_id = $supplierid");
		return $query->result();
	}

	public function add_supplier($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_suppliers', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function add_supplier_roles($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_suppliers_roles', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function add_supplier_taxes($data)
	{
		$this->db->insert_batch('tbl_supplier_taxes', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_supplier_taxes($supplierid)
	{
		$query = $this->db->query("SELECT A.tax_id, A.tax_value, B.tax_name, B.number_format, B.arithmetic_type 
					FROM tbl_supplier_taxes A 
					INNER JOIN tbl_origin_supplier_taxes B ON B.id = A.tax_id 
					WHERE A.is_active = 1 AND A.supplier_id = $supplierid AND B.is_enabled_supplier = 1
					ORDER BY A.tax_id");
		return $query->result();
	}

	public function add_provider_taxes($data)
	{
		$this->db->insert_batch('tbl_provider_taxes', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_provider_taxes($supplierid)
	{
		$query = $this->db->query("SELECT A.tax_id, A.tax_value, B.tax_name, B.number_format, B.arithmetic_type 
					FROM tbl_provider_taxes A 
					INNER JOIN tbl_origin_supplier_taxes B ON B.id = A.tax_id 
					WHERE A.is_active = 1 AND A.supplier_id = $supplierid AND B.is_enabled_transporter = 1
					ORDER BY A.tax_id");
		return $query->result();
	}

	public function delete_supplier_taxes($id, $data)
	{
		$multiClause = array('supplier_id' => $id);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_supplier_taxes', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_provider_taxes($id, $data)
	{
		$multiClause = array('supplier_id' => $id);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_provider_taxes', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function add_supplier_banks($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_suppliers_bankdetails', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function add_supplier_products($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_suppliers_products', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function add_supplier_product_types($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_suppliers_product_type', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_supplier($data, $id, $originid)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$multiClause = array('id' => $id, 'origin_id' => $originid);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_suppliers', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_supplier_role($id, $roleid)
	{
		$multiClause = array('supplier_id' => $id, 'role_id' => $roleid);
		$this->db->where($multiClause);
		$this->db->delete('tbl_suppliers_roles');
		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_supplier_bank($id)
	{
		$this->db->where('supplier_id', $id);
		$this->db->delete('tbl_suppliers_bankdetails');
		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_supplier_product($data, $data1, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('supplier_id', $id);
		if ($this->db->update('tbl_suppliers_products', $data)) {
			$this->delete_supplier_producttype($data1, $id);
			return true;
		} else {
			return false;
		}
	}

	public function delete_supplier_producttype($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('supplier_id', $id);
		if ($this->db->update('tbl_suppliers_product_type', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_supplier_details_report($applicableorigins)
	{
		$query = $this->db->query("SELECT A.id, A.supplier_code, A.supplier_id, supplier_name, company_name, company_id, 
						supplier_address,
						getsupplierroles_byid(A.id) as roles,
						getsupplierproducts_id(A.id) as products, 
						getsupplierbank_id(A.id) as bankdetails, 
						CONCAT('IVA: ', CASE WHEN (A.iva_value > 0 OR A.iva_value < 0) THEN A.iva_value ELSE 'Not applicable' END, '\n', 'Retencion: ', CASE WHEN (A.retencion_value > 0 OR A.retencion_value < 0) THEN A.retencion_value ELSE 'Not applicable' END, '\n', 'Reteica: ', CASE WHEN (A.reteica_value > 0 OR A.reteica_value < 0) THEN A.reteica_value ELSE 'Not applicable' END) as supplier_taxes,
						CONCAT('IVA: ', CASE WHEN (A.iva_provider_value > 0 OR A.iva_provider_value < 0) THEN A.iva_provider_value ELSE 'Not applicable' END, '\n', 'Retencion: ', CASE WHEN (A.retencion_provider_value > 0 OR A.retencion_provider_value < 0) THEN A.retencion_provider_value ELSE 'Not applicable' END, '\n', 'Reteica: ', CASE WHEN (A.reteica_provider_value > 0 OR A.reteica_provider_value < 0) THEN A.reteica_provider_value ELSE 'Not applicable' END) as provider_taxes,
						A.isactive, getorigin_namebyid(A.origin_id) as origin
						FROM tbl_suppliers A WHERE A.origin_id IN ($applicableorigins)");
		return $query->result();
	}

	public function get_supplier_detail_reception($supplierid, $productid)
	{
		$query = $this->db->query("SELECT B.supplier_code, A.product_id AS product_name, 
						getsupplierproductype_supplierproduct(A.product_id, A.supplier_id) as product_type 
						FROM tbl_suppliers_products A 
						INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
						WHERE A.is_active = 1 AND A.supplier_id = $supplierid AND A.product_name = $productid");
		return $query->result();
	}

	//WAREHOUSE
	public function all_warehouses()
	{
		$query = $this->db->query("SELECT whid, warehouse_name, warehouse_ownername, 
				warehouse_address, warehouse_code, A.is_active, getapplicableorigins_byid(A.origin_id) as origin, B.pol_name
				FROM tbl_warehouses A INNER JOIN tbl_export_pol B ON B.id = A.pol");
		return $query->result();
	}

	public function all_warehouses_originid($originid)
	{
		$query = $this->db->query("SELECT whid, warehouse_name, warehouse_ownername, 
				warehouse_address, warehouse_code, A.is_active, getapplicableorigins_byid(A.origin_id) as origin, B.pol_name 
				FROM tbl_warehouses A INNER JOIN tbl_export_pol B ON B.id = A.pol WHERE A.origin_id = $originid");
		return $query->result();
	}

	public function all_active_warehouses()
	{
		$query = $this->db->query("SELECT whid, warehouse_name, warehouse_code 
		FROM tbl_warehouses WHERE is_active = 1");
		return $query->result();
	}

	public function get_warehouse_detail_by_id($id)
	{
		$query = $this->db->query("SELECT whid, warehouse_name, warehouse_ownername, warehouse_address, 
				warehouse_code, pol, is_active, origin_id
				FROM tbl_warehouses WHERE whid = $id");
		return $query->result();
	}

	public function add_warehouse($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_warehouses', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_warehouse($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('whid', $id);
		if ($this->db->update('tbl_warehouses', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_warehouse_by_origin($originid)
	{
		$query = $this->db->query("SELECT whid, warehouse_name 
				FROM tbl_warehouses WHERE origin_id = $originid");
		return $query->result();
	}

	//SHIPPING LINES
	public function all_shippinglines()
	{
		$query = $this->db->query("SELECT id, shipping_line, isactive, getapplicableorigins_byid(origin_id) as origin
				FROM tbl_shippingline_master");
		return $query->result();
	}

	public function all_shippinglines_origin($originid)
	{
		$query = $this->db->query("SELECT id, shipping_line, isactive, getapplicableorigins_byid(origin_id) as origin
				FROM tbl_shippingline_master WHERE origin_id = $originid");
		return $query->result();
	}

	public function all_active_shippinglines()
	{
		$query = $this->db->query("SELECT id, shipping_line
				FROM tbl_shippingline_master WHERE isactive = 1");
		return $query->result();
	}

	public function get_shipping_detail_by_id($id)
	{
		$query = $this->db->query("SELECT id, shipping_line, isactive, origin_id
				FROM tbl_shippingline_master WHERE id = $id");
		return $query->result();
	}

	public function get_shippinglines_by_origin($originid)
	{
		$query = $this->db->query("SELECT id, shipping_line 
				FROM tbl_shippingline_master WHERE isactive = 1 AND origin_id = $originid");
		return $query->result();
	}

	public function add_shipping($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_shippingline_master', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_shipping($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('id', $id);
		if ($this->db->update('tbl_shippingline_master', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//MEASUREMENT SYSTEMS
	public function all_measurementsystems()
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name, isactive, getapplicableorigins_byid(origin_id) as origin 
				FROM tbl_measurement_system");
		return $query->result();
	}

	public function all_measurementsystems_origin($originid)
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name, isactive, getapplicableorigins_byid(origin_id) as origin 
				FROM tbl_measurement_system WHERE origin_id = $originid");
		return $query->result();
	}

	public function all_active_measurementsystems()
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name
				FROM tbl_measurement_system WHERE isactive = 1");
		return $query->result();
	}

	public function fetch_measurementsystems_by_origin($originid, $producttypeid)
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name
				FROM tbl_measurement_system WHERE origin_id = $originid AND product_type_id = $producttypeid 
				AND isactive = 1");
		return $query->result();
	}

	public function get_measurementsystem_detail_by_id($id)
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name, isactive, origin_id 
				FROM tbl_measurement_system WHERE measurement_id = $id");
		return $query->result();
	}

	public function add_measurementsystem($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_measurement_system', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_measurementsystem($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('measurement_id', $id);
		if ($this->db->update('tbl_measurement_system', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//MEASUREMENT SYSTEMS
	public function all_inputparameters()
	{
		$query = $this->db->query("SELECT input_parameter_id, parametername, parametercode, A.isactive, 
			B.product_type_name, getapplicableorigins_byid(A.origin_id) as origin 
			FROM tbl_input_parameters A 
			INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id");
		return $query->result();
	}

	public function all_inputparameters_originid($originid)
	{
		$query = $this->db->query("SELECT input_parameter_id, parametername, parametercode, A.isactive, 
			B.product_type_name, getapplicableorigins_byid(A.origin_id) as origin 
			FROM tbl_input_parameters A 
			INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id WHERE A.origin_id = $originid");
		return $query->result();
	}

	public function all_active_inputparameters()
	{
		$query = $this->db->query("SELECT input_parameter_id, parametername, parametercode, A.isactive, B.product_type_name
			FROM tbl_input_parameters A 
			INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id WHERE A.isactive = 1");
		return $query->result();
	}

	public function get_inputparameter_detail_by_id($id)
	{
		$query = $this->db->query("SELECT input_parameter_id, product_type_id, parametername, parametercode, isactive,
				origin_id FROM tbl_input_parameters WHERE input_parameter_id = $id");
		return $query->result();
	}

	public function add_inputparameter($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_input_parameters', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_inputparameter($data, $id)
	{
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->where('input_parameter_id', $id);
		if ($this->db->update('tbl_input_parameters', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//QR CODE
	public function get_last_generated_qrcode($originid)
	{
		$query = $this->db->query("SELECT CASE WHEN MAX(scannedcode) IS NULL THEN '-NA-' ELSE MAX(scannedcode) END as lastcode FROM tbl_generated_scannedcode WHERE origin_id = $originid");
		return $query->result();
	}

	public function get_last_generated_qrcode_pdf($originid)
	{
		$query = $this->db->query("SELECT CASE WHEN MAX(scannedcode) IS NULL THEN '0' ELSE MAX(scannedcode) END as lastcode FROM tbl_generated_scannedcode WHERE origin_id = $originid");
		return $query->result();
	}

	public function get_exist_generated_qrcode($originid, $scannedCode)
	{
		$query = $this->db->query("SELECT COUNT(scannedcode) as cnt FROM tbl_generated_scannedcode WHERE origin_id = $originid AND scannedcode = '$scannedCode' AND isactive = 1");
		return $query->result();
	}

	public function add_scanned_code($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_generated_scannedcode', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_scanned_code($data, $id)
	{
		$this->db->where('origin_id', $id);
		if ($this->db->update('tbl_qrcode_sequences', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function add_scanned_code_files($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_scanned_codes_files', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function all_available_qrcodes()
	{
		$query = $this->db->query("SELECT id, CONCAT(min_range, ' - ', max_range) as qrrange, 
			number_of_codes, getusername_byuserid(created_by) as created_by, getapplicableorigins_byid(origin_id) as origin
			FROM tbl_scanned_codes_files WHERE is_active = 1 ORDER BY created_date DESC");
		return $query->result();
	}

	public function all_available_qrcodes_origin($originid)
	{
		$query = $this->db->query("SELECT id, CONCAT(min_range, ' - ', max_range) as qrrange, 
			number_of_codes, getusername_byuserid(created_by) as created_by, 
			getapplicableorigins_byid(origin_id) as origin
			FROM tbl_scanned_codes_files WHERE is_active = 1 AND origin_id = $originid ORDER BY created_date DESC");
		return $query->result();
	}

	public function get_qr_code_details_origin($qrcodeid)
	{
		$query = $this->db->query("SELECT min_range, max_range, number_of_codes, origin_id
			FROM tbl_scanned_codes_files WHERE is_active = 1 AND id = $qrcodeid");
		return $query->result();
	}

	//INPUT PARAMETER SETTINGS
	public function all_input_parameters_origin($producttypeid, $originid)
	{
		$query = $this->db->query("SELECT input_parameter_id, CONCAT(parametername,' (', parametercode,')') as parametername
        	FROM tbl_input_parameters WHERE product_type_id = $producttypeid AND origin_id = $originid AND isactive = 1");
		return $query->result();
	}

	public function get_input_parameter_settings($productid, $producttypeid, $inputparameterid, $originid)
	{
		$query = $this->db->query("SELECT input_parameter_id, minrange, maxrange, isenable FROM tbl_input_parameter_settings
				WHERE product_id = $productid AND product_type_id = $producttypeid AND isactive = 1
				AND input_parameter_id = $inputparameterid AND origin_id = $originid");
		return $query->result();
	}

	public function add_ip_settings($data)
	{
		$this->db->set('createddate', 'NOW()', FALSE);
		$this->db->set('updateddate', 'NOW()', FALSE);
		$this->db->insert('tbl_input_parameter_settings', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function get_count_ip_settings($productid, $producttypeid, $inputparameterid, $originid)
	{
		$query = $this->db->query("SELECT COUNT(*) as cnt FROM tbl_input_parameter_settings WHERE origin_id = $originid AND product_id = '$productid' 
									AND product_type_id = $producttypeid AND input_parameter_id = $inputparameterid AND isactive = 1");
		return $query->result();
	}

	public function update_ip_settings($data, $input_parameter_id, $product_id, $product_type_id, $origin_id)
	{
		$multiClause = array('input_parameter_id' => $input_parameter_id, 'product_id' => $product_id, 'product_type_id' => $product_type_id, 'origin_id' => $origin_id);
		$this->db->where($multiClause);
		$this->db->set('updateddate', 'NOW()', FALSE);
		if ($this->db->update('tbl_input_parameter_settings', $data)) {
			return true;
		} else {
			return false;
		}
	}

	//FORMULA
	public function get_formulae_by_purchase_units($purchaseunitids, $originid)
	{
		$query = $this->db->query("SELECT formula_context, type, purchase_unit_id FROM tbl_formula_purchase_unit
				WHERE purchase_unit_id IN ($purchaseunitids) AND origin_id = $originid AND is_active = 1
				ORDER BY order_of_calculation ASC");
		return $query->result();
	}

	public function get_formulae_by_purchase_unit($purchaseunitid, $originid)
	{
		$query = $this->db->query("SELECT formula_context, type FROM tbl_formula_purchase_unit
				WHERE purchase_unit_id = $purchaseunitid AND origin_id = $originid AND is_active = 1
				ORDER BY order_of_calculation ASC");
		return $query->result();
	}

	public function get_calulation_type($purchaseunitid)
	{
		$query = $this->db->query("SELECT calculation_type FROM tbl_purchase_unit
				WHERE id = $purchaseunitid AND is_active = 1");
		return $query->result();
	}

	public function get_formulae_by_measurementsystem($measurementsystemid, $originid)
	{
		$query = $this->db->query("SELECT context, calculation_formula FROM tbl_calculation_formula
				WHERE measurement_system_id = $measurementsystemid AND origin_id = $originid AND isactive = 1
				ORDER BY order_of_calculation ASC");
		return $query->result();
	}

	public function get_formulae_by_measurementsystem_producttype($producttypeid, $originid)
	{
		$query = $this->db->query("SELECT A.context, A.calculation_formula FROM tbl_calculation_formula A 
				INNER JOIN tbl_measurement_system B ON B.measurement_id = A.measurement_system_id 
				WHERE A.isactive = 1 AND A.origin_id = $originid AND B.isactive = 1 
				AND B.origin_id = $originid AND B.product_type_id = $producttypeid 
				ORDER BY A.order_of_calculation ASC");
		return $query->result();
	}

	//EXPORT
	public function get_export_pol($originid)
	{
		$query = $this->db->query("SELECT id, pol_name 
				FROM tbl_export_pol WHERE origin_id = $originid AND is_active = 1");
		return $query->result();
	}

	public function get_export_pod()
	{
		$query = $this->db->query("SELECT A.id, CONCAT(A.pod_name, ', ', B.code) AS pod_name FROM tbl_export_pod A 
				INNER JOIN tbl_countries B ON B.id = A.country_id 
				WHERE A.is_active = 1");
		return $query->result();
	}

	//SUPPLIER TAXES
	public function all_taxsettings_origin($origin_id)
	{
		if($origin_id == 0) {
			$query = $this->db->query("SELECT id, tax_name,number_format,arithmetic_type,is_enabled_supplier, 
					is_enabled_transporter, default_tax_value_supplier, default_tax_value_provider, 
					getapplicableorigins_byid(origin_id) AS origin, origin_id, is_active 
					FROM tbl_origin_supplier_taxes");
		} else {
			$query = $this->db->query("SELECT id, tax_name,number_format,arithmetic_type,is_enabled_supplier, 
					is_enabled_transporter, default_tax_value_supplier, default_tax_value_provider, 
					getapplicableorigins_byid(origin_id) AS origin, origin_id, is_active 
					FROM tbl_origin_supplier_taxes WHERE origin_id = $origin_id");
		}
		
		return $query->result();
	}	

	public function get_supplier_taxes_by_origin($originid)
	{
		$query = $this->db->query("SELECT id, tax_name, number_format, arithmetic_type, 
				default_tax_value_supplier, default_tax_value_provider 
				FROM tbl_origin_supplier_taxes WHERE is_active = 1 AND is_enabled_supplier = 1 
				AND origin_id = $originid");
		return $query->result();
	}

	public function get_supplier_taxes_by_origin_report($originid)
	{
		$query = $this->db->query("SELECT id, tax_name, number_format, arithmetic_type, 
				default_tax_value_supplier, default_tax_value_provider 
				FROM tbl_origin_supplier_taxes WHERE origin_id = $originid");
		return $query->result();
	}

	public function get_provider_taxes_by_origin($originid)
	{
		$query = $this->db->query("SELECT id, tax_name, number_format, arithmetic_type, 
				default_tax_value_supplier, default_tax_value_provider 
				FROM tbl_origin_supplier_taxes WHERE is_active = 1 AND is_enabled_transporter = 1
				AND origin_id = $originid");
		return $query->result();
	}

	public function get_supplier_taxes_report($supplierid)
	{
		$query = $this->db->query("SELECT CASE WHEN GROUP_CONCAT(CONCAT(B.tax_name, 
				CASE WHEN B.number_format = 2 THEN ' (%):' ELSE ' :' END, TRIM(A.tax_value) + 0) SEPARATOR '\n') IS NULL THEN '' 
				ELSE GROUP_CONCAT(CONCAT(B.tax_name, CASE WHEN B.number_format = 2 THEN ' (%):' ELSE ' :' END, TRIM(A.tax_value) + 0) SEPARATOR '\n') END AS supplier_taxes 
				FROM tbl_supplier_taxes A 
				INNER JOIN tbl_origin_supplier_taxes B ON B.id = A.tax_id
				WHERE A.supplier_id = $supplierid AND A.is_active = 1");
		return $query->result();
	}

	public function get_provider_taxes_report($supplierid)
	{
		$query = $this->db->query("SELECT CASE WHEN GROUP_CONCAT(CONCAT(B.tax_name, 
				CASE WHEN B.number_format = 2 THEN ' (%):' ELSE ' :' END, TRIM(A.tax_value) + 0) SEPARATOR '\n') IS NULL THEN '' 
				ELSE GROUP_CONCAT(CONCAT(B.tax_name, CASE WHEN B.number_format = 2 THEN ' (%):' ELSE ' :' END, TRIM(A.tax_value) + 0) SEPARATOR '\n') END AS provider_taxes 
				FROM tbl_provider_taxes A 
				INNER JOIN tbl_origin_supplier_taxes B ON B.id = A.tax_id
				WHERE A.supplier_id = $supplierid AND A.is_active = 1");
		return $query->result();
	}

	public function add_tax_settings($data)
	{
		$this->db->set("created_date", "NOW()", FALSE);
		$this->db->set("updated_date", "NOW()", FALSE);
		$this->db->insert("tbl_origin_supplier_taxes", $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_tax_settings($data, $id)
	{
		$this->db->set("updated_date", "NOW()", FALSE);
		$this->db->where("id", $id);
		if ($this->db->update("tbl_origin_supplier_taxes", $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function tax_apply_suppliers($taxid, $taxvalue, $userid, $originid, $isactive)
	{
		$query = "INSERT INTO tbl_supplier_taxes(supplier_id, tax_id, tax_value, created_by, created_date, 
			updated_by, updated_date, is_active) SELECT B.id, $taxid, $taxvalue, $userid, NOW(), $userid, 
			NOW(), $isactive FROM tbl_suppliers_roles A 
			INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
			WHERE A.is_active = 1 AND A.role_id = 1 AND B.origin_id = $originid";
		$this->db->query($query);
	}

	public function tax_apply_tranporter($taxid, $taxvalue, $userid, $originid, $isactive)
	{
		$query = "INSERT INTO tbl_provider_taxes(supplier_id, tax_id, tax_value, created_by, created_date, 
			updated_by, updated_date, is_active) SELECT B.id, $taxid, $taxvalue, $userid, NOW(), $userid, 
			NOW(), $isactive FROM tbl_suppliers_roles A 
			INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
			WHERE A.is_active = 1 AND A.role_id = 2 AND B.origin_id = $originid";
		$this->db->query($query);
	}

	public function get_supplier_taxes_by_id($taxid)
	{
		$query = $this->db->query("SELECT id, origin_id, tax_name, number_format, arithmetic_type, 
				default_tax_value_supplier, default_tax_value_provider, is_enabled_supplier, is_enabled_transporter, 
				is_applicable_purchase_manager, is_active
				FROM tbl_origin_supplier_taxes WHERE is_active = 1 AND id = $taxid");
		return $query->result();
	}

	public function get_supplier_taxes_by_edit($taxid)
	{
		$query = $this->db->query("SELECT id, origin_id, tax_name, number_format, arithmetic_type, 
				default_tax_value_supplier, default_tax_value_provider, is_enabled_supplier, is_enabled_transporter, 
				is_applicable_purchase_manager, is_active
				FROM tbl_origin_supplier_taxes WHERE id = $taxid");
		return $query->result();
	}

	public function update_apply_suppier($id, $userid)
	{
		$this->db->set("is_active", 0, FALSE);
		$this->db->set("updated_by", $userid, FALSE);
		$this->db->set("updated_date", "NOW()", FALSE);
		$this->db->where("tax_id", $id);
		if ($this->db->update("tbl_supplier_taxes")) {
			return true;
		} else {
			return false;
		}
	}

	public function update_apply_transporter($id, $userid)
	{
		$this->db->set("is_active", 0, FALSE);
		$this->db->set("updated_by", $userid, FALSE);
		$this->db->set("updated_date", "NOW()", FALSE);
		$this->db->where("tax_id", $id);
		if ($this->db->update("tbl_provider_taxes")) {
			return true;
		} else {
			return false;
		}
	}

	//EMAIL TEMPLATES
	public function get_email_template_by_code($templatecode) 
	{
		$query = $this->db->query("SELECT template_subject, template_message
				FROM tbl_email_templates WHERE template_code = '$templatecode' AND is_active = 1");
		return $query->result();
	}

	//PURCHASE CONTRACT
	public function get_contract_code_sequence($originid) 
	{
		$query = $this->db->query("SELECT contract_sequences FROM tbl_qrcode_sequences WHERE origin_id = $originid 
			ORDER BY id DESC LIMIT 1");
		return $query->result();
	}

	public function update_contract_code_sequence($originid, $data)
    {
        $multiClause = array('origin_id' => $originid);
        $this->db->where($multiClause);
        if ($this->db->update('tbl_qrcode_sequences', $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    //API
    public function get_suppliers_by_origin($originid)
	{
		$query = $this->db->query("SELECT id, supplier_name, supplier_code FROM tbl_suppliers 
			WHERE isactive = 1 AND origin_id = $originid");
		return $query->result();
	}
	
	public function get_suppliers_products_by_origin($supplierid)
	{
		$query = $this->db->query("SELECT A.product_id AS supplier_product_id, A.product_name AS product_id, 
			B.product_name FROM tbl_suppliers_products A
			INNER JOIN tbl_product_master B ON B.product_id = A.product_name
			WHERE A.is_active = 1 AND A.supplier_id = $supplierid");
		return $query->result();
	}
	
	public function get_suppliers_product_types_by_origin($supplierid, $productid)
	{
		$query = $this->db->query("SELECT B.type_id, B.product_type_name, 
			CASE WHEN B.type_id = 1 THEN 1  WHEN B.type_id = 2 THEN 2 WHEN B.type_id = 3 THEN 1 
			ELSE 2 END AS 'product_type_id' FROM tbl_suppliers_product_type A INNER JOIN tbl_product_types B ON B.option_id = A.product_type_id 
			WHERE A.is_active = 1 AND B.isactive = 1 AND A.product_id = $productid AND A.supplier_id = $supplierid");
		return $query->result();
	}
	
	public function get_warehouses_by_origin($originid)
	{
		$query = $this->db->query("SELECT whid, warehouse_name, warehouse_code, pol 
			FROM tbl_warehouses WHERE is_active = 1 AND origin_id = $originid");
		return $query->result();
	}
	
	public function fetch_measurementsystems_by_originid($originid)
	{
		$query = $this->db->query("SELECT measurement_id, measurement_name, product_type_id
				FROM tbl_measurement_system WHERE origin_id = $originid 
				AND isactive = 1");
		return $query->result();
	}
	
	//GIRTH CLASSIFICATION
	public function get_girth_classification_by_origin($originid) 
	{
		$query = $this->db->query("SELECT id, girth_classification, is_manual FROM tbl_master_girth_classification WHERE is_active = 1 
			AND origin_id = $originid ORDER BY id ASC");
		return $query->result();
	}
	
	//LENGTH CLASSIFICATION
	public function get_length_classification_by_origin($originid) 
	{
		$query = $this->db->query("SELECT id, length_classification, is_manual FROM tbl_master_length_classification WHERE is_active = 1 
			AND origin_id = $originid ORDER BY id ASC");
		return $query->result();
	}

	public function get_product_type_app()
	{
		$query = $this->db->query("SELECT type_id, product_type_name FROM tbl_product_types WHERE isactive = 1");
		return $query->result();
	}
	
	public function get_supplier_roles_by_origin($supplierid)
	{
		$query = $this->db->query("SELECT role_id FROM tbl_suppliers_roles WHERE supplier_id = $supplierid AND is_active = 1");
		return $query->result();
	}
}
