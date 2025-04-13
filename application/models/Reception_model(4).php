<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Reception_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function add_reception($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_reception', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_reception_tracking($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_reception_open_tracking', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_reception_data($data)
    {
        $this->db->insert_batch('tbl_reception_data', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_reception_data_from_farm($receptionid, $inventorynumber, $purchaseunitid, $userid)
    {
        $query = $this->db->query("SELECT B.inventory_order, A.scanned_code, A.length, A.width, A.thickness, 
                A.circumference, A.volume_pie, A.volume_bought, A.length_export, A.width_export, A.thickness_export, 
                A.volume, A.grade_id, A.face FROM tbl_farm_data A 
                INNER JOIN tbl_farm B ON B.farm_id = A.farm_id
                WHERE A.is_active = 1 AND B.is_active = 1 AND B.inventory_order = '$inventorynumber'");
        $data = $query->result();

        foreach ($data as $r) {
            if ($purchaseunitid == 1) {
                $dataReception = array(
                    "reception_id" => $receptionid, "salvoconducto" => $inventorynumber,
                    "scanned_code" => $r->scanned_code, "length_bought" => $r->length,
                    "width_bought" => $r->width, "thickness_bought" => $r->thickness,
                    "circumference_bought" => 0, "volumepie_bought" => $r->volume_pie, "cbm_bought" => $r->volume_bought,
                    "length_export" => $r->length_export, "width_export" => $r->width_export, "thickness_export" => $r->thickness_export,
                    "cbm_export" => $r->volume, "grade" => $r->grade_id, "face" => $r->face,
                    "createddate" => date('Y-m-d H:i:s'), "createdby" => $userid,
                    "updateddate" => date('Y-m-d H:i:s'), "updatedby" => $userid,
                    "isactive" => 1, "isdispatch" => 0, "scanned_timestamp" => 0, "isduplicatescanned" => 0,
                );
            } else if ($purchaseunitid == 2) {
                $dataReception = array(
                    "reception_id" => $receptionid, "salvoconducto" => $inventorynumber,
                    "scanned_code" => $r->scanned_code, "length_bought" => 0, "width_bought" => 0, "thickness_bought" => 0,
                    "circumference_bought" => 0, "volumepie_bought" => 0, "cbm_bought" => 0,
                    "length_export" => $r->length_export, "width_export" => $r->width_export, "thickness_export" => $r->thickness_export,
                    "cbm_export" => $r->volume, "grade" => $r->grade_id, "face" => $r->face,
                    "createddate" => date('Y-m-d H:i:s'), "createdby" => $userid,
                    "updateddate" => date('Y-m-d H:i:s'), "updateddate" => $userid,
                    "isactive" => 1, "isdispatch" => 0, "scanned_timestamp" => 0, "isduplicatescanned" => 0
                );
            }

            $this->db->insert('tbl_reception_data', $dataReception);
        }
        return true;
    }

    public function all_receptions()
    {
        $query = $this->db->query("SELECT A.reception_id, A.salvoconducto, B.supplier_name, E.product_name, D.product_type_name, 
                    DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'),'%d/%m/%Y') AS received_date, 
                    F.warehouse_name, getapplicableorigins_byid(A.origin_id) AS origin, 
                    total_volume AS totalvolume,
                    getusername_byuserid(A.createdby) AS uploadedby FROM tbl_reception A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                    INNER JOIN tbl_product_types D ON D.type_id = A.supplier_product_typeid 
                    INNER JOIN tbl_product_master E ON E.product_id = C.product_name 
                    INNER JOIN tbl_warehouses F ON F.whid = A.warehouse_id
                    WHERE A.isactive = 1 ORDER BY STR_TO_DATE(A.received_date, '%d/%m/%Y') DESC, A.createddate DESC");
        return $query->result();
    }

    public function all_receptions_origin($originid)
    {
        $query = $this->db->query("SELECT A.reception_id, A.salvoconducto, B.supplier_name, E.product_name, D.product_type_name, 
                    DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'),'%d/%m/%Y') AS received_date, F.warehouse_name, 
                    getapplicableorigins_byid(A.origin_id) AS origin, 
                    total_volume AS totalvolume,
                    getusername_byuserid(A.createdby) AS uploadedby FROM tbl_reception A 
                    INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                    INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                    INNER JOIN tbl_product_types D ON D.type_id = A.supplier_product_typeid 
                    INNER JOIN tbl_product_master E ON E.product_id = C.product_name 
                    INNER JOIN tbl_warehouses F ON F.whid = A.warehouse_id
                    WHERE A.isactive = 1 AND A.origin_id = $originid 
                    ORDER BY STR_TO_DATE(A.received_date, '%d/%m/%Y') DESC, A.createddate DESC");
        return $query->result();
    }

    public function get_inventory_order_count($inventoryorder, $originid)
    {
        $query = $this->db->query("SELECT COUNT(salvoconducto) as cnt FROM tbl_reception 
                    WHERE isactive = 1 AND origin_id = $originid AND salvoconducto = '$inventoryorder'");
        return $query->result();
    }

    public function get_supplier_product_byorigin($originid, $supplierid)
    {
        $query = $this->db->query("SELECT A.product_id, B.product_name FROM tbl_suppliers_products A 
                INNER JOIN tbl_product_master B ON B.product_id = A.product_name
                WHERE supplier_id = '$supplierid' AND B.origin_id = $originid AND A.is_active = 1 AND B.isactive = 1 ORDER BY A.product_name");
        return $query->result();
    }

    public function get_supplier_product_type_byorigin($supplierid, $productid)
    {
        $query = $this->db->query("SELECT C.type_id, C.product_type_name 
                            FROM tbl_suppliers_product_type A 
                            INNER JOIN tbl_product_types C ON C.option_id = A.product_type_id
                            WHERE A.supplier_id = $supplierid AND A.is_active = 1 AND A.product_id = $productid 
                            ORDER BY C.type_id");
        return $query->result();
    }

    public function get_measurement_system($producttypeid, $originid)
    {
        if ($producttypeid == 3) {
            $producttypeid = 1;
        } else if ($producttypeid == 4) {
            $producttypeid = 2;
        }

        $query = $this->db->query("SELECT measurement_id, measurement_name FROM tbl_measurement_system 
                        WHERE product_type_id = $producttypeid AND isactive = 1 AND origin_id = $originid 
                        ORDER BY measurement_id");
        return $query->result();
    }

    public function get_reception_details($receptionid, $inventoryorder)
    {
        $query = $this->db->query("SELECT A.reception_id, A.supplier_id, C.supplier_name, C.supplier_code, A.salvoconducto, 
                        A.received_date, A.warehouse_id, B.warehouse_name, E.product_name, E.product_id, 
                        F.product_type_name, G.fullname AS uploaded_by, A.total_pieces, A.total_volume, 
                        getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, H.measurement_name, 
                        A.measurementsystem_id, 
                        getremainingpieces_reception(A.reception_id, A.salvoconducto) AS remaining_pieces, 
                        getremainingvolume_reception(A.reception_id, A.salvoconducto) AS remaining_volume, A.isclosed 
                        FROM tbl_reception A 
                        INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                        INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
                        INNER JOIN tbl_suppliers_products D ON D.product_id = A.supplier_product_id 
                        INNER JOIN tbl_product_master E ON E.product_id = D.product_name 
                        INNER JOIN tbl_product_types F ON F.type_id = A.supplier_product_typeid 
                        INNER JOIN tbl_user_registration G ON G.userid = A.createdby 
                        INNER JOIN tbl_measurement_system H ON H.measurement_id = A.measurementsystem_id 
                        WHERE reception_id = $receptionid AND salvoconducto = '$inventoryorder'");
        return $query->result();
    }

    public function get_reception_data_details($receptionid, $inventoryorder)
    {
        $query = $this->db->query("SELECT A.scanned_code, A.length_bought, A.width_bought, 
                A.thickness_bought, A.circumference_bought, A.volumepie_bought, A.length_export, A.width_export, 
                A.thickness_export, A.remaining_stock_count, A.container_number, A.face  
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto
                WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
                AND A.reception_id = $receptionid AND A.salvoconducto = '$inventoryorder' 
                ORDER BY A.circumference_bought");
        return $query->result();
    }

    public function update_reception($receptionid, $inventoryorder, $data)
    {
        $multiClause = array('reception_id' => $receptionid, 'salvoconducto' => $inventoryorder);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_reception_data($receptionid, $inventoryorder, $data)
    {
        $multiClause = array('reception_id' => $receptionid, 'salvoconducto' => $inventoryorder);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_reception_data_for_dispatch($receptiondataid, $receptionid, $inventoryorder, $data)
    {
        $multiClause = array('reception_data_id' => $receptiondataid, 'reception_id' => $receptionid, 'salvoconducto' => $inventoryorder);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_reception($receptionid, $inventoryorder, $userid)
    {
        $updateData = array(
            "isactive" => 0, "updatedby" => $userid,
        );
        $multiClause = array('reception_id' => $receptionid, 'salvoconducto' => $inventoryorder);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception', $updateData)) {

            $updateData = array(
                "isactive" => 0, "updatedby" => $userid,
            );
            $multiClause = array('reception_id' => $receptionid, 'salvoconducto' => $inventoryorder);
            $this->db->where($multiClause);
            $this->db->set('updateddate', 'NOW()', FALSE);
            if ($this->db->update('tbl_reception_data', $updateData)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_reception_data_for_dispatch($inventoryorder, $circumference, $length, $originid)
    {

        $query = $this->db->query("SELECT A.reception_data_id, A.reception_id, A.remaining_stock_count, 
                CASE WHEN A.container_number IS NULL THEN '' ELSE A.container_number END AS container_number 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto 
                WHERE A.isactive = 1 AND B.isactive = 1 AND A.salvoconducto = '$inventoryorder' 
                AND A.circumference_bought = $circumference AND A.length_bought = $length AND A.remaining_stock_count > 0 
                AND B.origin_id = $originid");
        return $query->result();
    }
    
    public function get_reception_data_for_dispatch_square_blocks($inventoryorder, $length, $width, $thickness, $originid)
    {

        $query = $this->db->query("SELECT A.reception_data_id, A.reception_id, A.remaining_stock_count, 
                CASE WHEN A.container_number IS NULL THEN '' ELSE A.container_number END AS container_number 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto 
                WHERE A.isactive = 1 AND B.isactive = 1 AND A.salvoconducto = '$inventoryorder' 
                AND A.length_bought = $length AND A.width_bought = $width AND A.thickness_bought = $thickness AND A.remaining_stock_count > 0 
                AND B.origin_id = $originid LIMIT 1");
        return $query->result();
    }

    public function fetch_reception_by_status($originid, $status)
    {
        if ($originid == 0) {

            $query = $this->db->query("SELECT A.reception_id, A.salvoconducto, B.supplier_name, 
                        E.product_name, D.product_type_name, 
                        DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'),'%d/%m/%Y') AS received_date, 
                        getapplicableorigins_byid(A.origin_id) AS origin, 
                        total_volume AS totalvolume,
                        getusername_byuserid(A.createdby) AS uploadedby FROM tbl_reception A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                        INNER JOIN tbl_product_types D ON D.type_id = A.supplier_product_typeid 
                        INNER JOIN tbl_product_master E ON E.product_id = C.product_name 
                        WHERE A.isactive = 1 AND A.isclosed = $status 
                        ORDER BY STR_TO_DATE(A.received_date, '%d/%m/%Y') DESC, A.createddate DESC");
        } else {
            $query = $this->db->query("SELECT A.reception_id, A.salvoconducto, B.supplier_name, 
                        E.product_name, D.product_type_name, 
                        DATE_FORMAT(STR_TO_DATE(A.received_date, '%d/%m/%Y'),'%d/%m/%Y') AS received_date, 
                        getapplicableorigins_byid(A.origin_id) AS origin, 
                        total_volume AS totalvolume,
                        getusername_byuserid(A.createdby) AS uploadedby FROM tbl_reception A 
                        INNER JOIN tbl_suppliers B ON B.id = A.supplier_id 
                        INNER JOIN tbl_suppliers_products C ON C.product_id = A.supplier_product_id 
                        INNER JOIN tbl_product_types D ON D.type_id = A.supplier_product_typeid 
                        INNER JOIN tbl_product_master E ON E.product_id = C.product_name 
                        WHERE A.isactive = 1 AND A.origin_id = $originid AND A.isclosed = $status 
                        ORDER BY STR_TO_DATE(A.received_date, '%d/%m/%Y') DESC, A.createddate DESC");
        }
        return $query->result();
    }

    public function fetch_reception_tracking_users($receptionid, $status)
    {
        $query = $this->db->query("SELECT A.user_id, B.fullname FROM tbl_reception_open_tracking A 
                        INNER JOIN tbl_user_registration B ON B.userid = A.user_id 
                        WHERE A.isactive = 1 AND A.isclosed = $status AND A.reception_id = $receptionid");
        return $query->result();
    }

    public function update_reception_tracking_users($userid, $receptionid, $status, $updatedby)
    {
        $query = "UPDATE tbl_reception_open_tracking SET isclosed = $status, updateddate = NOW(), updatedby = $updatedby 
            WHERE user_id IN ($userid) AND reception_id = $receptionid AND isactive = 1";
        if ($this->db->query($query)) {

            $queryReceptionTracking = $this->db->query("SELECT COUNT(*) AS cnt FROM tbl_reception_open_tracking 
                            WHERE isactive = 1 AND isclosed = 0 AND reception_id = $receptionid");
            $dataTracking = $queryReceptionTracking->result();
            if ($dataTracking[0]->cnt == 0) {
                $queryCloseReception = "UPDATE tbl_reception SET isclosed = 1, closedby = $updatedby, closeddate = NOW(), 
                    updateddate = NOW(), updatedby = $updatedby 
                    WHERE reception_id = $receptionid";

                return $this->db->query($queryCloseReception);
            } else if ($dataTracking[0]->cnt > 0) {
                $queryCloseReception = "UPDATE tbl_reception SET isclosed = 0, closedby = 0, closeddate = NOW(), 
                    updateddate = NOW(), updatedby = $updatedby 
                    WHERE reception_id = $receptionid";

                return $this->db->query($queryCloseReception);
            } else {
                return true;
            }
        }
    }

    //INVENTORY REPORTS
    public function get_warehouse_inventory_order_by_supplier($originid, $supplierid)
    {
        $query = $this->db->query("SELECT salvoconducto as inventory_order FROM tbl_reception 
                WHERE isactive = 1 AND origin_id = $originid AND supplier_id = $supplierid ORDER BY salvoconducto ASC");
        return $query->result();
    }

    public function get_warehouse_report_by_supplier($originid, $supplierid, $inventoryorder, $producttypeid, $reportstatus)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, length_bought, width_bought, 
                    thickness_bought, length_export, thickness_export, width_export, scanned_code, 
                    remaining_stock_count, container_number, measurement_code, is_special, isdispatch  
                    FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND supplier_id = $supplierid AND type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, circumference_bought, 
                    length_bought, scanned_code, remaining_stock_count, container_number, measurement_code,  
                    is_special, isdispatch FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND supplier_id = $supplierid AND type_id IN (2,4)";
        }

        if ($inventoryorder > 0) {
            $strQuery = $strQuery . " AND salvoconducto = '$inventoryorder'";
        }

        if ($reportstatus == 1) {
            $strQuery = $strQuery . " AND isdispatch = 0";
        } else if ($reportstatus == 2) {
            $strQuery = $strQuery . " AND isdispatch = 1";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_warehouse_report_by_product($originid, $productid, $producttypeid, $reportstatus)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, length_bought, width_bought, 
                    thickness_bought, length_export, thickness_export, width_export, scanned_code, 
                    remaining_stock_count, container_number, measurement_code, is_special, isdispatch 
                    FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND product_id = $productid AND type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, circumference_bought, 
                    length_bought, scanned_code, remaining_stock_count, container_number, measurement_code, 
                    is_special, isdispatch FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND product_id = $productid AND type_id IN (2,4)";
        }

        if ($reportstatus == 1) {
            $strQuery = $strQuery . " AND isdispatch = 0";
        } else if ($reportstatus == 2) {
            $strQuery = $strQuery . " AND isdispatch = 1";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_warehouse_report_by_daterange($originid, $startdate, $enddate, $producttypeid, $reportstatus)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, DATE_FORMAT(received_date, '%d/%m/%Y') AS received_date, length_bought, width_bought, 
                    thickness_bought, length_export, thickness_export, width_export, scanned_code, 
                    remaining_stock_count, container_number, measurement_code, is_special, isdispatch  
                    FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND (received_date BETWEEN '$startdate' AND '$enddate') 
                    AND type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, DATE_FORMAT(received_date, '%d/%m/%Y') AS received_date, circumference_bought, 
                    length_bought, scanned_code, remaining_stock_count, container_number, measurement_code,  
                    is_special, isdispatch FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND (received_date BETWEEN '$startdate' AND '$enddate') 
                    AND type_id IN (2,4)";
        }

        if ($reportstatus == 1) {
            $strQuery = $strQuery . " AND isdispatch = 0";
        } else if ($reportstatus == 2) {
            $strQuery = $strQuery . " AND isdispatch = 1";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_warehouse_report_by_producttype_square_block($originid, $producttypeid)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $producttypeid = "1, 3";
        }
        $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, length_bought, width_bought, 
                    thickness_bought, length_export, thickness_export, width_export, scanned_code, 
                    remaining_stock_count, container_number, measurement_code, is_special 
                    FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND type_id IN ($producttypeid)";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_warehouse_report_by_producttype_round_logs($originid, $producttypeid, $reportstatus)
    {
        if ($producttypeid == 2 || $producttypeid == 4) {
            $producttypeid = "2, 4";
        }
        $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, circumference_bought, 
                    length_bought, scanned_code, remaining_stock_count, container_number, measurement_code, 
                    is_special, isdispatch FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND type_id IN ($producttypeid)";

        if ($reportstatus == 1) {
            $strQuery = $strQuery . " AND isdispatch = 0";
        } else if ($reportstatus == 2) {
            $strQuery = $strQuery . " AND isdispatch = 1";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_warehouse_report_by_inventory($originid, $inventoryorder, $producttypeid, $reportstatus)
    {
        if ($producttypeid == 1 || $producttypeid == 3) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, length_bought, width_bought, 
                    thickness_bought, length_export, thickness_export, width_export, scanned_code, 
                    remaining_stock_count, container_number, measurement_code, is_special, isdispatch 
                    FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND salvoconducto = '$inventoryorder' 
                    AND type_id IN (1,3)";
        } else if ($producttypeid == 2 || $producttypeid == 4) {
            $strQuery = "SELECT supplier_name, supplier_code, salvoconducto, product_name, product_type_name, 
                    warehouse_name, measurement_name, received_by, received_date, circumference_bought, 
                    length_bought, scanned_code, remaining_stock_count, container_number, measurement_code,  
                    is_special, isdispatch FROM v_fetch_warehouse_report_data 
                    WHERE origin_id = $originid AND salvoconducto = '$inventoryorder' 
                    AND type_id IN (2,4)";
        }

        if ($reportstatus == 1) {
            $strQuery = $strQuery . " AND isdispatch = 0";
        } else if ($reportstatus == 2) {
            $strQuery = $strQuery . " AND isdispatch = 1";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_reception_closed_status($receptionid)
    {
        $query = $this->db->query("SELECT isclosed FROM tbl_reception WHERE isactive = 1 AND reception_id = $receptionid");
        return $query->result();
    }

    public function get_reception_detail_by_id($receptionid)
    {
        $query = $this->db->query("SELECT C.supplier_name, C.supplier_code, A.salvoconducto, B.warehouse_name, E.product_name, 
                        F.product_type_name, G.fullname AS closedby, A.total_pieces, A.total_volume, 
                        getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id 
                        FROM tbl_reception A 
                        INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                        INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
                        INNER JOIN tbl_suppliers_products D ON D.product_id = A.supplier_product_id 
                        INNER JOIN tbl_product_master E ON E.product_id = D.product_name 
                        INNER JOIN tbl_product_types F ON F.type_id = A.supplier_product_typeid 
                        INNER JOIN tbl_user_registration G ON G.userid = A.closedby 
                        WHERE reception_id = $receptionid");
        return $query->result();
    }

    public function update_reception_data_id($receptionid, $data)
    {
        $multiClause = array('reception_id' => $receptionid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function add_reception_data_single($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_reception_data', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_reception_data_dataid($receptionid, $receptiondataid, $data)
    {
        $multiClause = array('reception_id' => $receptionid, 'reception_data_id' => $receptiondataid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_reception_detail_by_id_app($receptionid)
    {
        $query = $this->db->query("SELECT C.supplier_name, C.supplier_code, A.salvoconducto, B.warehouse_name, E.product_name, 
                        F.product_type_name, A.total_pieces, A.total_volume, 
                        getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id 
                        FROM tbl_reception A 
                        INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                        INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
                        INNER JOIN tbl_suppliers_products D ON D.product_id = A.supplier_product_id 
                        INNER JOIN tbl_product_master E ON E.product_id = D.product_name 
                        INNER JOIN tbl_product_types F ON F.type_id = A.supplier_product_typeid 
                        WHERE reception_id = $receptionid");
        return $query->result();
    }

    public function update_reception_app($receptionid, $data)
    {
        $multiClause = array('reception_id' => $receptionid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_reception_lists($originid)
    {
        $query = $this->db->query("SELECT reception_id, salvoconducto, measurementsystem_id, origin_id, received_date, 
                warehouse_id, A.supplier_id, supplier_product_id, supplier_product_typeid, total_pieces, 
                total_gross_volume, total_volume, A.createdby, UNIX_TIMESTAMP(A.createddate) AS createddate, isclosed, closedby, 
                UNIX_TIMESTAMP(A.closeddate) AS closeddate, is_create_farm, contract_id, truck_plate_number, 
                is_special_uploaded, B.product_name, D.type_id, A.logistic_cost, A.logistic_pay_to
                FROM tbl_reception A 
                INNER JOIN tbl_suppliers_products B ON B.product_id = A.supplier_product_id 
                INNER JOIN tbl_product_types D ON D.type_id = A.supplier_product_typeid
                WHERE A.isactive = 1 AND isclosed = 0 AND origin_id = $originid");
        return $query->result();
    }

    public function get_reception_data_by_receptionid($receptionid)
    {
        $query = $this->db->query("SELECT B.circumference_bought, B.length_bought, A.dispatch_id, C.container_number, 
            B.cbm_bought, B.cbm_export, B.scanned_code, B.reception_data_id, B.reception_id 
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = A.dispatch_id 
            INNER JOIN tbl_reception_data B ON B.reception_data_id = A.reception_data_id AND B.reception_id = A.reception_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) AND B.isactive = 1 
            AND (B.isduplicatescanned = 0 OR B.isduplicatescanned IS NULL) AND B.reception_id = $receptionid");
        return $query->result();
    }
    
    public function get_reception_dispatch_mapping($receptionid)
    {
        $query = $this->db->query("SELECT A.dispatch_id, A.reception_id, B.salvoconducto, C.container_number, C.isclosed FROM tbl_reception_dispatch_mapping A 
            INNER JOIN tbl_reception B ON B.reception_id = A.reception_id 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = A.dispatch_id 
            WHERE A.is_active = 1 AND B.isactive = 1 AND A.reception_id = $receptionid");
        return $query->result();
    }

    public function get_reception_dispatch_mapping_containerid($receptionid, $containerid)
    {
        $query = $this->db->query("SELECT A.dispatch_id, A.reception_id, B.salvoconducto, C.container_number, C.isclosed FROM tbl_reception_dispatch_mapping A 
            INNER JOIN tbl_reception B ON B.reception_id = A.reception_id 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = A.dispatch_id 
            WHERE A.is_active = 1 AND B.isactive = 1 AND A.reception_id = $receptionid AND A.dispatch_id = $containerid");
        return $query->result();
    }

    public function add_reception_mapping_data($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_reception_dispatch_mapping', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }
    
    public function get_reception_details_app($receptionid, $inventoryorder)
    {
        $query = $this->db->query("SELECT A.reception_id, A.supplier_id, A.salvoconducto, 
                    A.received_date, A.warehouse_id, E.product_id, A.supplier_product_typeid,
                    A.total_pieces, A.total_volume, 
                    A.origin_id, A.measurementsystem_id, A.is_create_farm, A.contract_id, A.truck_plate_number, A.createdby, 
                    A.logistic_cost, A.logistic_pay_to 
                    FROM tbl_reception A 
                    INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                    INNER JOIN tbl_suppliers C ON C.id = A.supplier_id 
                    INNER JOIN tbl_suppliers_products D ON D.product_id = A.supplier_product_id 
                    INNER JOIN tbl_product_master E ON E.product_id = D.product_name 
                    WHERE A.reception_id = $receptionid AND A.salvoconducto = '$inventoryorder'");
        return $query->result();
    }

    public function get_reception_data_details_app($receptionid, $inventoryorder)
    {
        $query = $this->db->query("SELECT A.scanned_code, A.length_bought, A.width_bought, 
                A.thickness_bought, A.circumference_bought, A.volumepie_bought, A.length_export, A.width_export, 
                A.thickness_export, A.remaining_stock_count, A.container_number 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto
                WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
                AND A.reception_id = $receptionid AND A.salvoconducto = '$inventoryorder' 
                ORDER BY A.circumference_bought");
        return $query->result();
    }
    
    public function update_reception_data_dataid_user($receptionid, $receptiondataid, $data, $userid)
    {
        $multiClause = array('reception_id' => $receptionid, 'reception_data_id' => $receptiondataid, 'createdby' => $userid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_reception_data_by_receptionid_user($receptionid, $userid)
    {
        $query = $this->db->query("SELECT B.circumference_bought, B.length_bought, A.dispatch_id, C.container_number, 
            B.cbm_bought, B.cbm_export, B.scanned_code, B.reception_data_id, B.reception_id 
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = A.dispatch_id 
            INNER JOIN tbl_reception_data B ON B.reception_data_id = A.reception_data_id AND B.reception_id = A.reception_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) AND B.isactive = 1 
            AND (B.isduplicatescanned = 0 OR B.isduplicatescanned IS NULL) AND B.createdby = $userid AND B.reception_id = $receptionid");
        return $query->result();
    }

    public function get_total_reception_data_details_app($receptionid, $inventoryorder)
    {
        $query = $this->db->query("SELECT SUM(A.scanned_code) AS totalpieces, SUM(A.cbm_bought) AS gross_volume, SUM(A.cbm_export) AS net_volume 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto
                WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
                AND A.reception_id = $receptionid AND A.salvoconducto = '$inventoryorder'");
        return $query->result();
    }
}