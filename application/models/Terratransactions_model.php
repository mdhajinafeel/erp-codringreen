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
    public function get_reception_details(int $originid, int $year)
    {
        return $this->db
            ->select("rd.reception_id as receptionId,
            rd.warehouse_id as warehouseId,
            rd.supplier_id as supplierId,
            rd.supplier_product_id as supplierProductId,
            rd.supplier_product_typeid as supplierProductTypeId,
            rd.measurementsystem_id as measurementSystemId,
            rd.received_date as receivedDate,
            rd.salvoconducto as ica,
            rd.isclosed as isClosed,
            rd.closedby as closedBy,
            rd.closeddate as closedDate,
            rd.total_gross_volume as totalGrossVolume,
            rd.total_volume as totalNetVolume,
            rd.total_pieces as totalPieces,
            rd.is_create_farm as isCreateFarm,
            rd.contract_id as contractId,
            rd.truck_plate_number as truckPlateNumber,
            rd.temp_reception_id as tempReceptionId,
            tf.driver_name as truckDriverName")
            ->from('tbl_reception rd')
            ->join('tbl_farm tf', 'tf.inventory_order = rd.salvoconducto', 'left')
            ->where('rd.origin_id', $originid)
            ->where('rd.isactive', 1)
            ->where('rd.isclosed', 0)
            ->where("RIGHT(rd.received_date, 4) >=", $year)
            // ->where('updated_at >', $lastSync)
            // ->where('updated_at <=', $serverTime)
            ->get()
            ->result();
    }
}
