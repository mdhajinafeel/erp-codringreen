<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Sawmill_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //SAWMILLS
    public function all_sawmills()
    {
        // $query = $this->db->query("SELECT A.farm_id, A.inventory_order, B.supplier_name, B.supplier_code, 
        //         DATE_FORMAT(STR_TO_DATE(purchase_date, '%Y-%m-%d'),'%d/%m/%Y') AS purchase_date, 
        //         A.total_volume, A.total_pieces, C.contract_code, 
        //         D.product_name, E.product_type_name, F.purchase_unit 
        //         FROM tbl_farm A 
        //         INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
        //         INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
        //         INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
        //         INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
        //         INNER JOIN tbl_purchase_unit F ON F.id = A.purchase_unit_id 
        //         WHERE A.is_active = 1 AND A.process_type = 1 AND B.is_saw_mill = 0
        //         ORDER BY STR_TO_DATE(A.purchase_date, '%Y-%m-%d') DESC, A.created_date DESC");

        $query = $this->db->query("SELECT B.supplier_name, COUNT(A.inventory_order) AS count_ica, 
                        SUM(A.wood_value) + 
						SUM(A.extraction_cost) + 
                        SUM(C.logistic_cost_farm)  + 
                        SUM(C.service_cost_farm) + 
                        SUM(C.loading_cost_farm) + 
                        SUM(C.unloading_cost_farm) AS total_cost, 
                        SUM(gettotalpieces_farm(A.farm_id)) AS total_pieces, 
                        gettotalvolume_farm_supplier(A.supplier_id, 0, 0, 1) AS total_volume
                        FROM tbl_farm A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_sawmill_costing C ON C.supplier_id = A.supplier_id 
                        WHERE A.is_active = 1 AND A.process_type = 1 AND B.is_saw_mill = 0
                        GROUP BY A.supplier_id");
        return $query->result();
    }

    public function all_sawmills_origin($originid)
    {
        // $query = $this->db->query("SELECT A.farm_id, A.inventory_order, B.supplier_name, B.supplier_code, 
        //         DATE_FORMAT(STR_TO_DATE(purchase_date, '%Y-%m-%d'),'%d/%m/%Y') AS purchase_date, 
        //         A.total_volume, A.total_pieces, C.contract_code, 
        //         D.product_name, E.product_type_name, F.purchase_unit 
        //         FROM tbl_farm A 
        //         INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
        //         INNER JOIN tbl_supplier_purchase_contract C ON C.contract_id = A.contract_id 
        //         INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
        //         INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
        //         INNER JOIN tbl_purchase_unit F ON F.id = A.purchase_unit_id 
        //         WHERE A.is_active = 1 AND A.process_type = 1 AND A.origin_id = $originid AND B.is_saw_mill = 0
        //         ORDER BY STR_TO_DATE(A.purchase_date, '%Y-%m-%d') DESC, A.created_date DESC");

        $query = $this->db->query("SELECT B.supplier_name, COUNT(A.inventory_order) AS count_ica, 
                        SUM(A.wood_value) + 
						SUM(A.extraction_cost) + 
                        SUM(C.logistic_cost_farm)  + 
                        SUM(C.service_cost_farm) + 
                        SUM(C.loading_cost_farm) + 
                        SUM(C.unloading_cost_farm) AS total_cost, 
                        SUM(gettotalpieces_farm(A.farm_id)) AS total_pieces, 
                        gettotalvolume_farm_supplier(A.supplier_id, 0, 0, 1) AS total_volume
                        FROM tbl_farm A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_sawmill_costing C ON C.supplier_id = A.supplier_id 
                        WHERE A.is_active = 1 AND A.process_type = 1 AND A.origin_id = $originid AND B.is_saw_mill = 0
                        GROUP BY A.supplier_id");
        return $query->result();
    }

    public function get_sawmills_reports($originid)
    {
        // $query = $this->db->query("SELECT B.supplier_name, COUNT(A.inventory_order) AS count_ica, SUM(A.wood_value) AS wood_value, SUM(A.extraction_cost) AS extraction_cost, 
        //                 SUM(A.logistic_cost) AS logistic_cost, SUM(A.service_cost) AS service_cost, SUM(A.loading_cost) AS loading_cost, 
        //                 SUM(A.unloading_cost) AS unloading_cost, SUM(A.process_cost_sawmill) AS process_cost_sawmill, SUM(A.loading_cost_sawmill) AS loading_cost_sawmill, 
        //                 SUM(A.logistic_cost_sawmill) AS logistic_cost_sawmill, SUM(gettotalpieces_farm(A.farm_id)) AS total_pieces, 
        //                 gettotalvolume_farm_supplier(A.supplier_id, 0, 0, 1) AS total_volume
        //                 FROM tbl_farm A 
        //                 INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
        //                 WHERE A.is_active = 1 AND A.process_type = 1 AND A.origin_id = $originid AND B.is_saw_mill = 0
        //                 GROUP BY A.supplier_id");

        $query = $this->db->query("SELECT B.supplier_name, COUNT(A.inventory_order) AS count_ica, SUM(A.wood_value) AS wood_value, 
						SUM(A.extraction_cost) AS extraction_cost, 
                        SUM(C.logistic_cost_farm) AS logistic_cost, 
                        SUM(C.service_cost_farm) AS service_cost, 
                        SUM(C.loading_cost_farm) AS loading_cost, 
                        SUM(C.unloading_cost_farm) AS unloading_cost, 
                        SUM(C.process_cost_farm) * get_exchangerate_latest() AS process_cost_sawmill, 
                        SUM(A.loading_cost_sawmill) AS loading_cost_sawmill, 
                        SUM(A.logistic_cost_sawmill) AS logistic_cost_sawmill, 
                        SUM(gettotalpieces_farm(A.farm_id)) AS total_pieces, 
                        gettotalvolume_farm_supplier(A.supplier_id, 0, 0, 1) AS total_volume
                        FROM tbl_farm A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_sawmill_costing C ON C.supplier_id = A.supplier_id 
                        WHERE A.is_active = 1 AND A.process_type = 1 AND A.origin_id = $originid AND B.is_saw_mill = 0
                        GROUP BY A.supplier_id");
        return $query->result();
    }

    public function get_sawmill_process_data()
    {
        $query = $this->db->query("SELECT A.process_date, A.girth_rl, A.girth_rsq, A.length, A.gross_volume_rl, A.gross_volume_rsq, A.net_volume_rl, 
                        A.net_volume_rsq, A.gross_loss, A.gross_loss_percentage, A.net_loss, 
                        A.net_loss_percentage, B.value AS exchange_rate, getprocessed_cost_sawmill() AS process_cost 
                        FROM tbl_sawmill_process_data A
                        INNER JOIN tbl_exchange_rate B ON B.exchange_date = STR_TO_DATE(A.process_date, '%d/%m/%Y') 
                        WHERE A.is_active = 1 AND B.is_active = 1");
        return $query->result();
    }

    public function get_sawmill_container_data($originid)
    {
        $query = $this->db->query("SELECT A.container_number, B.dispatch_pieces, C.circumference_bought, C.length_bought, 
                        getcalculated_volume(1, C.circumference_bought, C.length_bought, 0, 0, C.scanned_code) AS gross_volume,
                        getcalculated_volume(1, C.circumference_bought, C.length_bought, 1, 5, C.scanned_code) AS net_volume, C.salvoconducto 
                        FROM tbl_dispatch_container A 
                        INNER JOIN tbl_dispatch_data B ON B.dispatch_id = A.dispatch_id 
                        INNER JOIN tbl_reception_data C ON C.reception_data_id = B.reception_data_id AND C.reception_id = B.reception_id 
                        WHERE A.is_saw_mill_loading = 1 AND A.isactive = 1 AND A.origin_id = $originid AND B.isactive = 1 AND C.isactive = 1");
        return $query->result();
    }

    public function get_sawmill_inventory_number($originid)
    {
        $query = $this->db->query("SELECT CASE WHEN (GROUP_CONCAT(DISTINCT QUOTE(D.salvoconducto) SEPARATOR ',') IS NULL OR '') THEN '-' ELSE 
                        GROUP_CONCAT(DISTINCT QUOTE(D.salvoconducto) SEPARATOR ',') END AS inventory_numbers
                        FROM tbl_dispatch_container A 
                        INNER JOIN tbl_dispatch_data B ON B.dispatch_id = A.dispatch_id 
                        INNER JOIN tbl_reception_data C ON C.reception_data_id = B.reception_data_id AND C.reception_id = B.reception_id 
                        INNER JOIN tbl_reception D ON D.reception_id = C.reception_id 
                        WHERE A.isactive = 1 AND A.is_saw_mill_loading = 1
                        AND B.isactive = 1 AND C.isactive = 1 AND D.isactive = 1 AND A.origin_id = $originid");
        return $query->result();
    }

    public function get_sawmill_inventory_data($inventorynumbers)
    {
        $query = $this->db->query("SELECT B.salvoconducto, A.scanned_code AS pieces, A.circumference_bought AS circumference, A.length_bought AS length, 
                    getcalculated_volume(1, A.circumference_bought, A.length_bought, 0, 0, A.scanned_code) AS gross_volume, 
                    getcalculated_volume(1, A.circumference_bought, A.length_bought, 1, 5, A.scanned_code) AS net_volume, A.container_number 
                    FROM tbl_reception_data A 
                    INNER JOIN tbl_reception B ON B.reception_id = A.reception_id 
                    WHERE A.isactive = 1 AND B.isactive AND B.salvoconducto IN ($inventorynumbers)");
        return $query->result();
    }

    public function fetch_inventory_chart_data($originid) {
        $query = $this->db->query("SELECT SUM(DISTINCT gettotalvolume_farm_supplier(A.supplier_id, 0, 0, 1)) AS total_volume, 
                CASE WHEN $originid = 1 THEN getprocessed_volume_sawmill() ELSE 0 END AS processed_volume, 
                CASE WHEN $originid = 1 THEN getexported_volume_sawmill() ELSE 0 END AS exported_volume 
                FROM tbl_farm A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                WHERE A.is_active = 1 AND A.process_type = 1 AND A.origin_id = $originid AND B.is_saw_mill = 0");
        return $query->result();
    }

    public function fetch_inventory_summary_data($originid) {
        $query = $this->db->query("SELECT CASE WHEN $originid = 1 THEN getprocessed_volume_sawmill() ELSE 0 END AS processed_volume, 
            CASE WHEN $originid = 1 THEN getprocessed_pieces_sawmill() ELSE 0 END AS processed_pieces, 
            CASE WHEN $originid = 1 THEN (getprocessed_calculated_cost_sawmill() + gettotal_cost_sawmill($originid)) ELSE 0 END AS processed_cost");
        return $query->result();
    }

    public function fetch_farm_summary_data($originid) {
        $query = $this->db->query("SELECT SUM(getfarmvolume_sawmill_byid(A.farm_id, 0, 0)) AS received_volume, 
                SUM(getfarmpieces_sawmill_byid(A.farm_id)) AS pieces, COUNT(A.inventory_order) AS total_ica
                FROM tbl_farm A 
                INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                WHERE A.is_active = 1 AND A.origin_id = $originid AND A.process_type = 1 
                AND B.isactive = 1  AND B.is_saw_mill = 0");
        return $query->result();
    }

    public function fetch_dispatch_summary_data($originid) {
        $query = $this->db->query("SELECT SUM(getdispatchvolume_sawmill_byid(dispatch_id, 0, 0)) AS volume, 
                SUM(getdispatchpieces_sawmill_byid(dispatch_id)) AS pieces, COUNT(dispatch_id) AS total_containers 
                FROM tbl_dispatch_container 
                WHERE is_saw_mill_loading = 1 AND isactive = 1 AND origin_id = $originid");
        return $query->result();
    }

    public function fetch_export_summary_data_sawmill($originid) {
        // $query = $this->db->query("SELECT getsanumber_bydispatchid(A.dispatch_id) AS sanumber, A.container_number, 
        //         SUM(B.dispatch_pieces) AS dispatch_pieces, getnoofpieces_reception(C.salvoconducto, C.reception_id, 0, 0) AS received_pieces, 
        //         C.salvoconducto AS inventory_number
        //         FROM tbl_dispatch_container A
        //         INNER JOIN tbl_dispatch_data B ON B.dispatch_id = A.dispatch_id 
        //         INNER JOIN tbl_reception_data C ON C.reception_data_id = B.reception_data_id 
        //         WHERE A.isactive = 1 AND B.isactive = 1 AND C.isactive = 1 
        //         AND A.is_saw_mill_loading = 1 AND A.origin_id = $originid
        //         GROUP BY A.container_number, C.reception_id 
        //         ORDER BY A.dispatch_id, C.reception_id ASC");

        $query = $this->db->query("SELECT sanumber, container_number, dispatch_pieces, received_pieces, inventory_number, 
                ROUND((loading_cost_sawmill / received_pieces) * dispatch_pieces,2) AS loading_cost_sawmill, 
                ROUND((transport_cost_sawmill / received_pieces) * dispatch_pieces, 2) AS transport_cost_sawmill 
                FROM (SELECT A.dispatch_id, C.reception_id, getsanumber_bydispatchid(A.dispatch_id) AS sanumber, A.container_number, 
                SUM(B.dispatch_pieces) AS dispatch_pieces, getnoofpieces_reception(C.salvoconducto, C.reception_id, 0, 0) AS received_pieces, 
                C.salvoconducto AS inventory_number, E.loading_cost_sawmill, E.transport_cost_sawmill
                FROM tbl_dispatch_container A
                INNER JOIN tbl_dispatch_data B ON B.dispatch_id = A.dispatch_id 
                INNER JOIN tbl_reception_data C ON C.reception_data_id = B.reception_data_id 
                INNER JOIN tbl_reception D ON D.reception_id = C.reception_id 
                INNER JOIN tbl_sawmill_costing E ON E.supplier_id = D.supplier_id 
                WHERE A.isactive = 1 AND B.isactive = 1 AND C.isactive = 1 
                AND A.is_saw_mill_loading = 1 AND A.origin_id = 1
                GROUP BY A.container_number, C.reception_id) A1 
                ORDER BY A1.dispatch_id, A1.reception_id ASC
                ");
        return $query->result();
    }

    //RECEIVED REPORT
    public function fetch_received_data_sawmill($originid) {
        $query = $this->db->query("SELECT A.inventory_order, B.supplier_name, C.no_of_pieces, C.circumference, C.length, 
                    getcalculated_volume(1, C.circumference, C.length, 0, 0, C.no_of_pieces) AS gross_volume, 
                    getcalculated_volume(1, C.circumference, C.length, D.purchase_allowance, D.purchase_allowance_length, C.no_of_pieces) AS net_volume 
                    FROM tbl_farm A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    INNER JOIN tbl_farm_data C ON C.farm_id = A.farm_id 
                    INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
                    WHERE A.is_active = 1 AND A.origin_id = $originid 
                    AND A.process_type = 1 AND B.isactive = 1 AND B.is_saw_mill = 0 
                    AND C.is_active = 1");
        return $query->result();
    }

    public function fetch_received_summary_data_sawmill($originid) {
        $query = $this->db->query("SELECT A.inventory_order, B.supplier_name, DATE_FORMAT(A.purchase_date, '%d/%m/%Y') AS purchase_date, SUM(C.no_of_pieces) AS pieces, SUM(getcalculated_volume(1, C.circumference, C.length, 0, 0, C.no_of_pieces)) AS gross_volume, 
                    SUM(getcalculated_volume(1, C.circumference, C.length, D.purchase_allowance, D.purchase_allowance_length, C.no_of_pieces)) AS net_volume 
                    FROM tbl_farm A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    INNER JOIN tbl_farm_data C ON C.farm_id = A.farm_id 
                    INNER JOIN tbl_supplier_purchase_contract D ON D.contract_id = A.contract_id 
                    WHERE A.is_active = 1 AND A.origin_id = $originid 
                    AND A.process_type = 1 AND B.isactive = 1 AND B.is_saw_mill = 0 
                    AND C.is_active = 1 
                    GROUP BY A.inventory_order 
                    ORDER BY A.purchase_date, A.farm_id");
        return $query->result();
    }

     //EXPORTED SUMMARY
    public function fetch_exported_summary_data_sawmill($originid) {
        $query = $this->db->query("SELECT A.container_number, A.dispatch_date, SUM(B.dispatch_pieces) AS dispatch_pieces, 
                        SUM(getcalculated_volume(1, C.circumference_bought, C.length_bought, 0, 0, C.scanned_code)) AS gross_volume,
                        SUM(getcalculated_volume(1, C.circumference_bought, C.length_bought, 1, 5, C.scanned_code)) AS net_volume 
                        FROM tbl_dispatch_container A 
                        INNER JOIN tbl_dispatch_data B ON B.dispatch_id = A.dispatch_id 
                        INNER JOIN tbl_reception_data C ON C.reception_data_id = B.reception_data_id AND C.reception_id = B.reception_id 
                        WHERE A.is_saw_mill_loading = 1 AND A.isactive = 1 AND A.origin_id = $originid AND B.isactive = 1 AND C.isactive = 1
                        GROUP BY A.container_number 
                        ORDER BY STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'), A.dispatch_id ASC");
        return $query->result();
    }
}
