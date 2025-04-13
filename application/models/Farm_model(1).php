<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Farm_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //FARMS
    public function all_farms()
    {
        $query = $this->db->query("SELECT farm_id, inventory_order, supplier_name, contract_id, 
                contract_code, product_name, product_type_name, 
                DATE_FORMAT(STR_TO_DATE(purchase_date, '%Y-%m-%d'),'%d/%m/%Y') AS purchase_date, 
                total_volume, uploaded_by, origin, created_from
                FROM v_fetch_farms
                ORDER BY STR_TO_DATE(purchase_date, '%Y-%m-%d') DESC, created_date DESC");
        return $query->result();
    }

    public function all_farms_origin($originid)
    {
        $query = $this->db->query("SELECT farm_id, inventory_order, supplier_name, contract_id, 
                contract_code, product_name, product_type_name, 
                DATE_FORMAT(STR_TO_DATE(purchase_date, '%Y-%m-%d'),'%d/%m/%Y') AS purchase_date, 
                total_volume, uploaded_by, origin, created_from 
                FROM v_fetch_farms WHERE origin_id = $originid
                ORDER BY STR_TO_DATE(purchase_date, '%Y-%m-%d') DESC, created_date DESC");
        return $query->result();
    }

    public function get_contracts_for_farm($originid, $supplierid, $productid, $producttypeid)
    {

        if ($producttypeid == 1 || $producttypeid == 3) {
            $producttypeids = "1,3";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $producttypeids = "2,4";
        }

        $query = $this->db->query("SELECT contract_id, contract_code FROM tbl_supplier_purchase_contract 
            WHERE is_active = 1 AND origin_id = $originid AND supplier_id = $supplierid AND product = $productid AND product_type IN ($producttypeids) 
            ORDER BY contract_id ASC");
        return $query->result();
    }

    public function fetch_contract_details_for_farm($originid, $contractid, $supplierid, $productid, $producttypeid)
    {
        $query = $this->db->query("SELECT remaining_volume, currency_code, purchase_unit, currency, unit_of_purchase FROM v_fetch_contracts 
            WHERE is_active = 1 AND contract_id = $contractid AND supplier_id = $supplierid 
            AND product = $productid AND product_type = $producttypeid AND origin_id = $originid");
        return $query->result();
    }

    public function fetch_payto_providers($originid, $supplierid)
    {
        $query = $this->db->query("SELECT supplier_id, supplier_name FROM (SELECT A.supplier_id, CONCAT(B.supplier_name,' - ',B.supplier_code) as supplier_name FROM tbl_suppliers_roles A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
                    WHERE A.supplier_id = $supplierid AND A.role_id = 1 AND A.is_active = 1 AND B.isactive = 1 AND B.origin_id = $originid
                    UNION 
                    SELECT A.supplier_id, CONCAT(B.supplier_name,' - ',B.supplier_code) FROM tbl_suppliers_roles A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    WHERE A.role_id = 2 AND A.is_active = 1 AND B.isactive = 1 AND B.origin_id = $originid) A");
        return $query->result();
    }

    public function get_scanned_code_count($scannedcode, $originid)
    {
        $query = $this->db->query("SELECT COUNT(scannedcode) as cnt FROM tbl_generated_scannedcode WHERE scannedcode = '$scannedcode' AND origin_id = $originid 
                    AND isactive = 1");
        return $query->result();
    }

    public function get_farm_scanned_code_count($scannedcode, $originid)
    {
        $query = $this->db->query("SELECT COUNT(A.scanned_code) as cnt FROM tbl_farm_data A 
                    INNER JOIN tbl_farm B ON B.farm_id = A.farm_id 
                    WHERE A.scanned_code = '$scannedcode' AND B.origin_id = $originid AND A.is_active = 1");
        return $query->result();
    }

    public function get_inventory_order_count($inventoryorder, $originid)
    {
        $query = $this->db->query("SELECT COUNT(inventory_order) as cnt FROM tbl_farm 
        WHERE is_active = 1 AND origin_id = $originid AND inventory_order = '$inventoryorder'");
        return $query->result();
    }

    public function get_supplier_taxes($supplierid)
    {
        $query = $this->db->query("SELECT is_iva_enabled, iva_value, is_retencion_enabled, retencion_value, 
                is_reteica_enabled, reteica_value FROM tbl_suppliers 
                WHERE id = $supplierid AND isactive = 1");
        return $query->result();
    }

    public function get_transportor_taxes($supplierid)
    {
        $query = $this->db->query("SELECT is_iva_provider_enabled, iva_provider_value, is_retencion_provider_enabled, 
                retencion_provider_value, is_reteica_provider_enabled, reteica_provider_value, is_iva_enabled, iva_value, 
                is_retencion_enabled, retencion_value, is_reteica_enabled, reteica_value  
                FROM tbl_suppliers WHERE id = $supplierid AND isactive = 1");
        return $query->result();
    }

    public function get_price_for_circumference($circumference, $purchasecontractid)
    {
        if ($circumference == -1) {
            $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3 
                FROM tbl_supplier_contract_price WHERE is_active = 1 
                AND supplier_id = $purchasecontractid");
        } else {
            $query = $this->db->query("SELECT pricerange_grade3 
                FROM tbl_supplier_contract_price WHERE is_active = 1 
                AND $circumference BETWEEN minrange_grade1 AND maxrange_grade2 
                AND supplier_id = $purchasecontractid");
        }
        return $query->result();
    }

    public function add_farm($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_farm', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_farm_data($data)
    {
        $this->db->insert_batch('tbl_farm_data', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_supplier_price($contractid, $supplierid, $inventorynumber, $userid)
    {
        $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3 
                    FROM tbl_supplier_contract_price WHERE is_active = 1
                    AND supplier_id = $contractid");
        $data = $query->result();

        foreach ($data as $r) {
            $dataPrice = array(
                "contract_id" => $contractid, "supplier_id" => $supplierid,
                "inventory_number" => $inventorynumber, "minrange_grade1" => $r->minrange_grade1,
                "maxrange_grade2" => $r->maxrange_grade2, "pricerange_grade3" => $r->pricerange_grade3,
                "created_date" => date('Y-m-d H:i:s'), "created_by" => $userid, "updated_date" => date('Y-m-d H:i:s'),
                "updated_by" => $userid, "is_active" => 1
            );
            $this->db->insert('tbl_supplier_contract_inventory_price', $dataPrice);
        }
        return true;
    }

    public function add_contract_inventory_mapping($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_contract_inventory_mapping', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_inventory_ledger($data, $woodValueWithSupplierTaxes, $inventorytype, $supplierid)
    {
        $this->db->set('supplier_id', $supplierid, FALSE);
        $this->db->set('amount', $woodValueWithSupplierTaxes, FALSE);
        $this->db->set('expense_type', $inventorytype, FALSE);
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_inventory_ledger', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_farm($farmid, $inventoryorder, $contractid, $userid)
    {
        $updateData = array(
            "is_active" => 0, "updated_by" => $userid,
        );
        $multiClause = array('farm_id' => $farmid, 'inventory_order' => $inventoryorder);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_farm', $updateData)) {

            //FIELD PURCHASE

            $updateDataFieldPurchase = array(
                "is_active" => 0, "updated_by" => $userid,
            );
            $multiClauseFieldPurchase = array('farm_id' => $farmid);
            $this->db->where($multiClauseFieldPurchase);
            $this->db->set('updated_date', 'NOW()', FALSE);
            $this->db->update('tbl_field_purchase_farm_data', $updateDataFieldPurchase);

            $updateDataFieldPurchaseConsent = array(
                "is_active" => 0, "updated_by" => $userid,
            );
            $multiClauseFieldPurchaseConsent = array('farm_id' => $farmid);
            $this->db->where($multiClauseFieldPurchaseConsent);
            $this->db->set('updated_date', 'NOW()', FALSE);
            $this->db->update('tbl_field_purchase_farm_consent', $updateDataFieldPurchaseConsent);

            //END FIELD PURCHASE

            $updateData = array(
                "is_active" => 0, "updated_by" => $userid,
            );
            $multiClause = array('farm_id' => $farmid);
            $this->db->where($multiClause);
            $this->db->set('updated_date', 'NOW()', FALSE);
            if ($this->db->update('tbl_farm_data', $updateData)) {
                $updateData = array(
                    "is_active" => 0, "updated_by" => $userid,
                );
                $multiClause = array('inventory_order' => $inventoryorder);
                $this->db->where($multiClause);
                $this->db->set('updated_date', 'NOW()', FALSE);
                if ($this->db->update('tbl_contract_inventory_mapping', $updateData)) {

                    $updateData = array(
                        "is_active" => 0, "updated_by" => $userid,
                    );
                    $multiClause = array('contract_id' => $contractid, 'inventory_order' => $inventoryorder);
                    $this->db->where($multiClause);
                    $this->db->set('updated_date', 'NOW()', FALSE);
                    if ($this->db->update('tbl_inventory_ledger', $updateData)) {

                        $updateData = array(
                            "is_active" => 0, "updated_by" => $userid,
                        );
                        $multiClause = array('inventory_number' => $inventoryorder);
                        $this->db->where($multiClause);
                        $this->db->set('updated_date', 'NOW()', FALSE);
                        if ($this->db->update('tbl_supplier_contract_inventory_price', $updateData)) {

                            $getContractDetails = $this->get_contracts_by_contractid($contractid);

                            $remainingVolume = 0;
                            if (count($getContractDetails) == 1) {
                                $remainingVolume = $getContractDetails[0]->total_volume - $getContractDetails[0]->mapping_volume;
                            }

                            $updateData = array(
                                "remaining_volume" => $remainingVolume,
                            );
                            $multiClause = array('contract_id' => $contractid);
                            $this->db->where($multiClause);
                            $this->db->set('updated_date', 'NOW()', FALSE);
                            if ($this->db->update('tbl_supplier_purchase_contract', $updateData)) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_contracts_by_contractid($contractid)
    {
        $query = $this->db->query("SELECT A.total_volume, gettotalvolume_bycontractid(A.contract_id) as mapping_volume
                        FROM tbl_supplier_purchase_contract A
                        WHERE A.contract_id = $contractid");
        return $query->result();
    }

    public function get_farm_details($farmid, $contractid, $inventoryorder)
    {
        $query = $this->db->query("SELECT A.inventory_order, A.supplier_id, B.supplier_name, B.supplier_code, C.contract_code, 
                D.product_name, E.product_type_name, DATE_FORMAT(A.purchase_date, '%d/%m/%Y') AS purchase_date, 
                A.service_cost, A.pay_service_to, CASE WHEN getsuppliername_bysupplierid(A.pay_service_to) IS NULL THEN '-' ELSE getsuppliername_bysupplierid(A.pay_service_to) END as service_pay_to, 
                A.logistic_cost, A.pay_logistics_to, CASE WHEN getsuppliername_bysupplierid(A.pay_logistics_to) IS NULL THEN '-' ELSE getsuppliername_bysupplierid(A.pay_logistics_to) END as logistic_pay_to, 
                A.wood_value, A.adjustment, gettotal_payamount_inventoryorder(A.inventory_order) AS total_payment, 
                getapplicableorigins_byid(A.origin_id) AS origin, 
                CASE WHEN A.created_from = 1 THEN gettotalpieces_byfarm_contract(A.contract_id, A.farm_id, A.inventory_order) 
                ELSE getnoofpieces_farm_fieldpurchase(A.inventory_order,0,0) END AS total_pieces, 
                A.total_volume, getusername_byuserid(A.created_by) as uploaded_by, 
                CASE WHEN A.created_from = 1 THEN ABS(A.total_value - gettotal_payamount_inventoryorder(A.inventory_order)) 
                ELSE ABS(A.wood_value - A.service_cost - A.logistic_cost - gettotal_payamount_inventoryorder(A.inventory_order)) END 
                AS total_taxes, 
                A.plate_number, A.exchange_rate, getcurrencyabbreviation_origin(A.origin_id) AS currency_abbreviation,
                F.purchase_unit, C.purchase_allowance, C.purchase_allowance_length, C.unit_of_purchase, A.origin_id, 
                getcurrencyexcelformat_origin(A.origin_id) as currency_excel_format, A.product_type_id, 
                A.supplier_taxes, A.logistic_taxes, A.service_taxes, A.logistic_provider_taxes, A.service_provider_taxes, 
                A.adjusted_value, A.supplier_taxes_array, A.logistics_taxes_array, A.service_taxes_array 
                FROM tbl_farm A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
                INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                INNER JOIN tbl_purchase_unit F ON F.id = C.unit_of_purchase
                WHERE A.is_active = 1 AND A.farm_id = $farmid AND A.inventory_order = '$inventoryorder' 
                AND A.contract_id = $contractid");
        return $query->result();
    }

    public function get_farm_data_details($farmid, $contractid, $inventoryorder)
    {
        $query = $this->db->query("SELECT no_of_pieces, circumference, length, scanned_code, width, thickness, 
                width_export, length_export, thickness_export, grade_id FROM (SELECT SUM(A.no_of_pieces) AS no_of_pieces, A.circumference, A.length, A.scanned_code, A.width, A.thickness, 
                A.width_export, A.length_export, A.thickness_export, A.grade_id FROM tbl_farm_data A  
                INNER JOIN tbl_farm B ON B.farm_id = A.farm_id 
                WHERE A.is_active = 1 AND B.farm_id = $farmid AND B.inventory_order = '$inventoryorder' 
                AND B.contract_id = $contractid 
                GROUP BY CASE WHEN (B.product_type_id = 1 OR B.product_type_id = 3) THEN A.scanned_code ELSE A.circumference END
                ORDER BY CASE WHEN (B.product_type_id = 1 OR B.product_type_id = 3) THEN A.scanned_code ELSE A.circumference END) X ORDER BY circumference");
        return $query->result();
    }

    public function get_farm_data_summary($farmid, $contractid, $inventoryorder, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3 
                FROM tbl_supplier_contract_inventory_price WHERE contract_id = $contractid 
                AND inventory_number = '$inventoryorder' AND is_active = 1");
        } else {
            $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3,
                    getnoofpieces_priceranges_farm('$inventoryorder', $farmid, minrange_grade1, maxrange_grade2) as pieces_farm
                    FROM tbl_supplier_contract_inventory_price WHERE contract_id = $contractid 
                    AND inventory_number = '$inventoryorder' AND is_active = 1");
        }
        return $query->result();
    }

    public function get_farm_data_summary_field_purchase($farmid, $contractid, $inventoryorder, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3, 
                getnoofpieces_farm_fieldpurchase(inventory_number, minrange_grade1, maxrange_grade2) AS pieces_farm 
                FROM tbl_supplier_contract_inventory_price WHERE contract_id = $contractid 
                AND inventory_number = '$inventoryorder' AND is_active = 1");
        } else {
            $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3,
                    getnoofpieces_farm_fieldpurchase(inventory_number, minrange_grade1, maxrange_grade2) as pieces_farm
                    FROM tbl_supplier_contract_inventory_price WHERE contract_id = $contractid 
                    AND inventory_number = '$inventoryorder' AND is_active = 1");
        }
        return $query->result();
    }

    public function update_farm($farmid, $inventoryorder, $contractid, $data)
    {
        $multiClause = array('farm_id' => $farmid, 'inventory_order' => $inventoryorder, 'contract_id' => $contractid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_farm', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_inventory_ledger($inventoryorder, $contractid, $data)
    {
        $multiClause = array('inventory_order' => $inventoryorder, 'contract_id' => $contractid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_inventory_ledger', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_inventory_mapping($inventoryorder, $contractid, $data)
    {
        $multiClause = array('inventory_order' => $inventoryorder, 'contract_id' => $contractid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_contract_inventory_mapping', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_contract_price($inventoryorder, $contractid, $data)
    {
        $multiClause = array('inventory_number' => $inventoryorder, 'contract_id' => $contractid);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_supplier_contract_inventory_price', $data)) {
            return true;
        } else {
            return false;
        }
    }

    //FARM REPORTS
    public function get_farm_inventory_order_by_supplier($originid, $supplierid)
    {
        $query = $this->db->query("SELECT inventory_order FROM tbl_farm 
                WHERE is_active = 1 AND origin_id = $originid AND supplier_id = $supplierid ORDER BY inventory_order ASC");
        return $query->result();
    }

    public function get_farm_report_by_supplier($originid, $supplierid, $inventoryorder, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, width, thickness, length_export, width_export, thickness_export, 
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE supplier_id = $supplierid 
                    AND origin_id = $originid AND product_type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, circumference, length, received_by, received_date, inventory_order, purchase_unit_id,
                    circumference_allowance, length_allowance, contract_code 
                    FROM v_fetch_farm_report_data WHERE supplier_id = $supplierid 
                    AND origin_id = $originid AND product_type_id IN (2,4)";
        }

        if($inventoryorder > 0) {
            $strQuery = $strQuery . " AND inventory_order = '$inventoryorder'";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_farm_report_by_product($originid, $productid, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, width, thickness, length_export, width_export, thickness_export, 
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE product_id = $productid AND origin_id = $originid AND product_type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, circumference, length, received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code 
                    FROM v_fetch_farm_report_data WHERE product_id = $productid AND origin_id = $originid AND product_type_id IN (2,4)";
        }
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_farm_report_by_daterange($originid, $startdate, $enddate, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, width, thickness, length_export, width_export, thickness_export, 
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE (received_date BETWEEN '$startdate' AND '$enddate') AND origin_id = $originid AND product_type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, circumference, length, received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code 
                    FROM v_fetch_farm_report_data WHERE (received_date BETWEEN '$startdate' AND '$enddate') AND origin_id = $originid AND product_type_id IN (2,4)";
        }
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_farm_report_by_producttype_square_block($originid, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $producttypeid = "1, 3";
        }
        $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, width, thickness, length_export, width_export, thickness_export,  
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE product_type_id IN ($producttypeid) AND origin_id = $originid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_farm_report_by_producttype_round_logs($originid, $producttypeid)
    {
        if ($producttypeid == 2 || $producttypeid == 4) {
            $producttypeid = "2, 4";
        }
        $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, circumference,  
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE product_type_id IN ($producttypeid) AND origin_id = $originid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_farm_report_by_inventory($originid, $inventoryorder, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code, 
                    no_of_pieces, length, width, thickness, length_export, width_export, thickness_export,  
                    received_by, received_date, inventory_order, purchase_unit_id, 
                    circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE inventory_order = '$inventoryorder' AND origin_id = $originid AND product_type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, product_name, product_type_name, scanned_code,  
                    no_of_pieces, circumference, length, received_by, received_date, inventory_order, 
                    purchase_unit_id, circumference_allowance, length_allowance, contract_code
                    FROM v_fetch_farm_report_data WHERE inventory_order = '$inventoryorder' AND origin_id = $originid AND product_type_id IN (2,4)";
        }
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_field_purchase_farm_data_details($farmid, $contractid, $inventoryorder)
    {
        $query = $this->db->query("SELECT min_max_circumference AS circumference, min_circumference, length, no_of_pieces 
                FROM tbl_field_purchase_farm_data A 
                INNER JOIN tbl_farm B ON B.farm_id = A.farm_id
                WHERE A.is_active = 1 AND B.inventory_order = '$inventoryorder' AND B.contract_id = $contractid
                AND B.farm_id = $farmid");
        return $query->result();
    }

    public function get_contract_list_to_create() {
        $query = $this->db->query("SELECT DATE_FORMAT(A.purchase_date, '%d/%m/%Y') AS purchase_date, A.inventory_order, 
                B.supplier_name, B.supplier_id, B.city, A.total_value, 
                CASE WHEN B.contact_no IS NULL THEN '' ELSE B.contact_no END AS contact_no, 
                B.supplier_address, CASE WHEN B.email_id IS NULL THEN '' ELSE B.email_id END AS email_id, 
                A.is_contract_created, 
                CASE WHEN A.contract_link IS NULL THEN '' ELSE A.contract_link END AS contract_link, 
                A.contract_id, 
                CASE WHEN getconsenturl_byfarm(A.farm_id, A.inventory_order) IS NULL THEN '' ELSE getconsenturl_byfarm(A.farm_id, A.inventory_order) END as consent_url, 
                A.origin_id, getapplicableorigins_byid(A.origin_id) AS origin  
                FROM tbl_farm A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id
                WHERE A.created_from = 2 and is_mail_sent = 0 AND A.is_active = 1");
        return $query->result();
    }

    public function update_contract_create($inventoryorder, $data)
    {
        $multiClause = array('inventory_order' => $inventoryorder, 'is_active' => 1, 'created_from' => 2);
        $this->db->where($multiClause);
        if ($this->db->update('tbl_farm', $data)) {
            return true;
        } else {
            return false;
        }
    }
}