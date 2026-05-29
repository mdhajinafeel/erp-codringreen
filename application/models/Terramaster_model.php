<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Terramaster_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // SUPPLIERS & PRODUCTS
    public function get_suppliers_full_data(int $originid)
    {
        $sql = "SELECT 
            S.id AS supplierId,
            S.supplier_name AS supplierName,

            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'supplierProductId', P.product_id,
                    'productId', P.product_name,
                    'productName', PM.product_name,

                    'supplierProductTypes', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'typeId', PT.type_id,
                                'productTypeName', PT.product_type_name,
                                'productTypeId',
                                CASE 
                                    WHEN PT.type_id = 1 THEN 1
                                    WHEN PT.type_id = 2 THEN 2
                                    WHEN PT.type_id = 3 THEN 1
                                    ELSE 2
                                END
                            )
                        )
                        FROM tbl_suppliers_product_type SPT
                        JOIN tbl_product_types PT 
                            ON PT.option_id = SPT.product_type_id
                        WHERE SPT.product_id = P.product_id
                        AND SPT.supplier_id = S.id
                        AND SPT.is_active = 1
                        AND PT.isactive = 1
                    )
                )
            ) AS supplierProducts

        FROM tbl_suppliers S
        JOIN tbl_suppliers_products P ON P.supplier_id = S.id
        JOIN tbl_product_master PM ON PM.product_id = P.product_name

        WHERE S.origin_id = $originid
        AND S.isactive = 1
        AND P.is_active = 1

        GROUP BY S.id";

        return $this->db->query($sql, [$originid])->result();
    }

    public function get_supplier_code_by_id(int $supplierid)
    {
        $row = $this->db
            ->select("supplier_code as supplierCode")
            ->from("tbl_suppliers")
            ->where("id", $supplierid)
            ->get()
            ->row();

        return $row->supplierCode ?? '';
    }

    // WAREHOUSES
    public function get_warehouses_by_origin(int $originid)
    {
        return $this->db
            ->select("whid as id, warehouse_name as warehouseName")
            ->from("tbl_warehouses")
            ->where("is_active", 1)
            ->where("origin_id", $originid)
            ->get()
            ->result();
    }

    // PURCHASE CONTRACT
    public function fetch_purchase_contract_origin(int $originid)
    {
        return $this->db
            ->select("
                A.contract_id AS contractId,
                A.supplier_id AS supplierId,
                A.contract_code AS contractCode,
                A.product AS product,
                A.product_type AS productType,
                B.purchase_unit AS purchaseUnit,
                A.unit_of_purchase AS purchaseUnitId,
                CONCAT(C.currency_name, ' (', C.currency_code, ')') AS currency,
                A.purchase_allowance AS purchaseAllowance,
                A.purchase_allowance_length AS purchaseAllowanceLength,
                A.description AS description
            ", false)
            ->from("tbl_supplier_purchase_contract A")
            ->join("tbl_purchase_unit B", "B.id = A.unit_of_purchase")
            ->join("tbl_currency C", "C.id = A.currency")
            ->where("A.origin_id", $originid)
            ->where("A.is_active", 1)
            ->order_by("A.contract_id", "ASC")
            ->get()
            ->result();
    }

    // MEASUREMENT SYSTEMS
    public function get_measurement_systems_by_origin(int $originid)
    {
        return $this->db
            ->select("measurement_id as id, measurement_name as measurementName, product_type_id as productTypeId")
            ->from("tbl_measurement_system")
            ->where("isactive", 1)
            ->where("origin_id", $originid)
            ->get()
            ->result();
    }

    // SHIPPING LINES
    public function get_shipping_lines_by_origin(int $originid)
    {
        return $this->db
            ->select("id, shipping_line as shippingLine")
            ->from("tbl_shippingline_master")
            ->where("isactive", 1)
            ->where("origin_id", $originid)
            ->get()
            ->result();
    }

    // PRODUCTS
    public function get_products_by_origin(int $originid)
    {
        return $this->db
            ->select("product_id as productId, product_name as productName")
            ->from("tbl_product_master")
            ->where("isactive", 1)
            ->where("origin_id", $originid)
            ->get()
            ->result();
    }

    // PRODUCT TYPES
    public function get_product_types_by_origin()
    {
        return $this->db
            ->select("type_id as typeId, product_type_name as productTypeName")
            ->from("tbl_product_types")
            ->where_in("option_id", [1, 2])
            ->where("isactive", 1)
            ->get()
            ->result();
    }

    // MEASUREMENT FORMULA
    public function get_formulas_grouped()
    {
        return $this->db->query("SELECT 
            A.measurement_system_id,
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'formulaMasterId', A.id,
                    'formula', A.formula,
                    'roundPrecision', A.round_precision,
                    'roundingType', A.rounding_type,
                    'context', A.context,
                    'sortOrder', A.sort_order,
                    'variables', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'formulaMasterId', V.formula_master_id,
                                'varName', V.var_name,
                                'displayName', V.display_name,
                                'unit', V.unit,
                                'sortOrder', V.sort_order
                            )
                        )
                        FROM tbl_formula_variable V
                        WHERE V.formula_master_id = A.id
                    )
                )
            ) AS formulas

        FROM tbl_formula_master A
        WHERE A.is_active = 1
        GROUP BY A.measurement_system_id")->result();
    }

    // CONTAINER CATEGORIES
    public function get_container_categories()
    {
        return $this->db
            ->select("id as id, category, product_type_id as productTypeId")
            ->from("tbl_container_categories")
            ->where("is_active", 1)
            ->get()
            ->result();
    }

    // CONTRACT DETAILS
    public function get_contract_details(int $contractId, int $supplierId, int $originId)
    {
        return $this->db
            ->select("contract_id as contractId, supplier_id as supplierId, product as productId, product_type as productTypeId, unit_of_purchase as purchaseUnitId, 
                purchase_allowance as purchaseAllowance, purchase_allowance_length as purchaseAllowanceLength, currency as currencyId")
            ->from("tbl_supplier_purchase_contract")
            ->where("contract_id", $contractId)
            ->where("supplier_id", $supplierId)
            ->where("origin_id", $originId)
            ->where("is_active", 1)
            ->get()
            ->row();
    }

    public function get_contract_details_bulk(array $contractIds, array $supplierIds, int $originId)
    {
        if (empty($contractIds) || empty($supplierIds)) {
            return [];
        }

        return $this->db
            ->select("contract_id as contractId, supplier_id as supplierId, product as productId, product_type as productTypeId, unit_of_purchase as purchaseUnitId, 
                purchase_allowance as purchaseAllowance, purchase_allowance_length as purchaseAllowanceLength, currency as currencyId")
            ->where_in('contract_id', $contractIds)
            ->where_in('supplier_id', $supplierIds)
            ->where('origin_id', $originId)
            ->where("is_active", 1)
            ->get('tbl_supplier_purchase_contract')
            ->result();
    }

    public function fetch_contract_prices_for_farm(int $contractId)
    {
        $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3, pricerange_grade_semi, pricerange_grade_longs 
                FROM tbl_supplier_contract_price A
                WHERE A.is_active = 1 AND A.supplier_id = $contractId");
        return $query->result();
    }

    public function fetch_contract_prices_bulk(array $contractIds)
    {
        if (empty($contractIds)) {
            return [];
        }

        return $this->db
            ->select("minrange_grade1, maxrange_grade2, pricerange_grade3, pricerange_grade_semi, pricerange_grade_longs ")
            ->where_in('supplier_id', $contractIds)
            ->where('is_active', 1)
            ->get('tbl_supplier_contract_price')
            ->result();
    }

    // EXCHANGE RATE
    public function fetch_exchange_rate_by_date(string $purchaseDate)
    {
        $query = $this->db->query("SELECT value FROM tbl_exchange_rate WHERE exchange_date <= '$purchaseDate' AND is_active = 1 ORDER BY exchange_date DESC LIMIT 1");
        return $query->result();
    }

    // SUPPLIER TAXES
    public function get_supplier_taxes(int $supplierId)
    {
        $query = $this->db->query("SELECT A.tax_id, A.tax_value, B.tax_name, B.number_format, B.arithmetic_type 
					FROM tbl_supplier_taxes A 
					INNER JOIN tbl_origin_supplier_taxes B ON B.id = A.tax_id 
					WHERE A.is_active = 1 AND A.supplier_id = $supplierId AND B.is_enabled_supplier = 1
					ORDER BY A.tax_id");
        return $query->result();
    }

    //FINANCE
    public function all_account_heads(int $originId)
	{
		$strQuery = "SELECT A.id as accountHeadId, A.name_in_app as accountHeadName, A.icon_svg as icon, A.color_code_primary as colorCodePrimary, A.color_code_secondary as colorCodeSecondary, 
            A.is_forestry as isForestry, A.forestry_cost_type as forestryCostType 
            FROM tbl_accounting_heads A 
            WHERE A.origin_id = $originId AND A.is_active = 1 ORDER BY A.id";

		$query = $this->db->query($strQuery);
		return $query->result();
	}

    public function all_beneficiaries(int $originId)
    {
        $query = "SELECT DISTINCT beneficiary_name AS beneficiaryName, document_number AS beneficiaryIdentification
                FROM tbl_expense_details A 
                INNER JOIN tbl_transaction B ON B.transaction_id = A.transaction_id 
                WHERE B.origin_id = $originId";
        $result = $this->db->query($query);
        return $result->result();
    }
}
