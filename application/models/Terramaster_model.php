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

    // FARM INVENTORY ORDERS
    public function get_farm_inventory_orders_by_origin(int $originid)
    {
        return $this->db
            ->select("inventory_order as inventoryOrder, supplier_id as supplierId")
            ->from("tbl_farm")
            ->where("is_active", 1)
            ->where("origin_id", $originid)
            ->where("purchase_date >=", '2025-01-01') // ✅ start of year
            ->get()
            ->result();
    }

    // RECEPTION INVENTORY ORDERS
    public function get_reception_inventory_orders_by_origin(int $originid, int $year)
    {
        return $this->db
            ->select("salvoconducto as inventoryOrder, supplier_id as supplierId")
            ->from("tbl_reception")
            ->where("isactive", 1)
            ->where("origin_id", $originid)

            // 🔥 FAST: extract year directly
            ->where("RIGHT(received_date, 4) >=", $year)

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

    // DISPATCH CONTAINERS
    public function get_dispatch_containers_by_origin(int $originid, int $year)
    {
        return $this->db
            ->select("container_number as containerNumber, shipping_line as shippingLineId")
            ->from("tbl_dispatch_container")
            ->where("isactive", 1)
            ->where("origin_id", $originid)

            // 🔥 FAST: extract year directly
            ->where("RIGHT(dispatch_date, 4) >=", $year)

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
    public function get_product_types_by_origin(int $originid)
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
}
