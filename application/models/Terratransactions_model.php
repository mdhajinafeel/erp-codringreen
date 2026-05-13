<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Terratransactions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
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

    // RECEPTION DETAILS (ADDITIONAL)
    public function get_reception_details(int $originid)
    {
        return $this->db
            ->select("rd.reception_id as receptionId, rd.warehouse_id as warehouseId, rd.supplier_id as supplierId, rd.supplier_product_id as supplierProductId,
            rd.supplier_product_typeid as supplierProductTypeId, rd.measurementsystem_id as measurementSystemId, rd.received_date as receivedDate,
            rd.salvoconducto as ica, rd.isclosed as isClosed, rd.closedby as closedBy, rd.closeddate as closedDate, rd.total_gross_volume as totalGrossVolume, 
            rd.total_volume as totalNetVolume, rd.total_pieces as totalPieces, rd.is_create_farm as isCreateFarm, rd.contract_id as contractId, 
            rd.truck_plate_number as truckPlateNumber, rd.temp_reception_id as tempReceptionId, tf.driver_name as truckDriverName , sp.product_name AS productId,
            CASE WHEN rd.supplier_product_typeid IN (1,3) THEN 1 ELSE 2 END AS productTypeId, rd.captured_timestamp as capturedTimestamp, 
            rd.container_reception_mapping_id as containerReceptionMappingId, ROUND(UNIX_TIMESTAMP(rd.updateddate) * 1000) AS updatedAt")
            ->from('tbl_reception rd')
            ->join('tbl_farm tf', 'tf.inventory_order = rd.salvoconducto', 'left')
            ->join('tbl_suppliers_products sp', 'sp.product_id = rd.supplier_product_id', 'inner')
            ->join('tbl_product_types pt', 'pt.type_id = rd.supplier_product_typeid', 'inner')
            ->where('rd.origin_id', $originid)
            ->where('rd.isactive', 1)
            ->where("STR_TO_DATE(rd.received_date, '%d/%m/%Y') >=", "DATE_SUB(CURDATE(), INTERVAL 3 MONTH)", false)
            ->get()
            ->result();
    }

    // RECEPTION DATA
    public function get_reception_data_by_ids(array $receptionIds)
    {
        if (empty($receptionIds)) {
            return [];
        }

        return $this->db
            ->select("temp_reception_data_id AS tempReceptionDataId, temp_reception_id AS tempReceptionId, reception_container_mapping_id AS containerReceptionMappingId, 
            reception_data_id AS receptionDataId, reception_id AS receptionId, circumference_bought AS circumference,
            length_bought AS length, thickness_bought AS thickness, width_bought AS width, scanned_code AS pieces,
            cbm_bought AS grossVolume, cbm_export AS netVolume, volumepie_bought AS volumePie, scanned_timestamp AS createdAt, ROUND(UNIX_TIMESTAMP(updateddate) * 1000) AS updatedAt")
            ->from('tbl_reception_data')
            ->where_in('reception_id', $receptionIds)
            ->where('isactive', 1)
            ->get()
            ->result();
    }

    // DISPATCH DETAILS (ADDITIONAL)
    public function get_dispatch_details(int $originid)
    {
        return $this->db
            ->select("dc.dispatch_id AS dispatchId, dc.container_number AS containerNumber, dc.warehouse_id AS warehouseId, dc.shipping_line AS shippingLineId, dc.product_id AS productId, 
            dc.product_type_id AS productTypeId, dc.dispatch_date AS dispatchDate, dc.isclosed AS isClosed, dc.closedby AS closedBy, dc.closeddate AS closedDate, 
            dc.total_gross_volume AS totalGrossVolume, dc.total_volume AS totalNetVolume, dc.total_pieces AS totalPieces, dc.temp_dispatch_id AS tempDispatchId,
            dc.dispatched_timestamp AS createdAt, dc.app_category AS categoryId, ROUND(UNIX_TIMESTAMP(dc.updateddate) * 1000) AS updatedAt")
            ->from('tbl_dispatch_container dc')
            ->where('dc.origin_id', $originid)
            ->where('dc.isactive', 1)
            ->where("STR_TO_DATE(dc.dispatch_date, '%d/%m/%Y') >=", "DATE_SUB(CURDATE(), INTERVAL 3 MONTH)", false)
            ->get()
            ->result();
    }

    // DISPATCH DATA
    public function get_dispatch_data_by_ids(array $dispatchIds)
    {
        if (empty($dispatchIds)) {
            return [];
        }

        return $this->db
            ->select("dispatch_data_id AS dispatchDataId, temp_dispatch_id AS tempDispatchId, container_reception_mapping_id AS containerReceptionMappingId, 
            temp_reception_id AS tempReceptionId, temp_reception_data_id AS tempReceptionDataId, dispatch_id AS dispatchId, reception_data_id AS receptionDataId, 
            reception_id AS receptionId, cbm_bought AS grossVolume, cbm_export AS netVolume, dispatch_pieces AS pieces, 
            scanned_timestamp AS createdAt, ROUND(UNIX_TIMESTAMP(updateddate) * 1000) AS updatedAt")
            ->from('tbl_dispatch_data')
            ->where_in('dispatch_id', $dispatchIds)
            ->where('isactive', 1)
            ->get()
            ->result();
    }
}
