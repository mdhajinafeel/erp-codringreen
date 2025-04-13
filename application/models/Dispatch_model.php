<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dispatch_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all_dispatches($originid, $dispatch_status)
    {

        if ($originid == 0) {
            $query = $this->db->query("SELECT A.dispatch_id, A.container_number, B.warehouse_name, C.shipping_line, 
                D.product_name, E.product_type_name, DATE_FORMAT(STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'),'%d/%m/%Y') AS dispatch_date, 
                getapplicableorigins_byid(A.origin_id) AS origin, getusername_byuserid(A.createdby) AS uploadedby, 
                A.total_volume, A.total_pieces 
                FROM tbl_dispatch_container A 
                INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                INNER JOIN tbl_shippingline_master C ON C.id = A.shipping_line 
                INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                WHERE A.isactive = 1 AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL) AND A.isexport = $dispatch_status
                ORDER BY A.dispatch_id DESC");
        } else {
            $query = $this->db->query("SELECT A.dispatch_id, A.container_number, B.warehouse_name, C.shipping_line, 
                D.product_name, E.product_type_name, DATE_FORMAT(STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'),'%d/%m/%Y') AS dispatch_date, 
                getapplicableorigins_byid(A.origin_id) AS origin, getusername_byuserid(A.createdby) AS uploadedby, 
                A.total_volume, A.total_pieces  
                FROM tbl_dispatch_container A 
                INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                INNER JOIN tbl_shippingline_master C ON C.id = A.shipping_line 
                INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                WHERE A.isactive = 1 AND A.origin_id = $originid AND A.isexport = $dispatch_status
                AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL) 
                ORDER BY A.dispatch_id DESC");
        }
        return $query->result();
    }

    public function get_container_count($containernumber, $originid)
    {
        $query = $this->db->query("SELECT COUNT(container_number) as cnt FROM tbl_dispatch_container 
                    WHERE isactive = 1 AND origin_id = $originid AND container_number = '$containernumber'");
        return $query->result();
    }

    public function check_inventory_order_exists($inventoryorder, $originid)
    {
        $query = $this->db->query("SELECT COUNT(salvoconducto) as cnt FROM tbl_reception 
                    WHERE isactive = 1 AND origin_id = $originid AND salvoconducto = '$inventoryorder'");
        return $query->result();
    }

    public function check_dispatch_pieces_availablity($inventoryorder, $circumference, $length, $originid)
    {
        $query = $this->db->query("SELECT SUM(A.remaining_stock_count) AS remaining_pieces 
			FROM tbl_reception_data A 
			INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto
			WHERE A.isactive = 1 AND B.isactive = 1 AND B.origin_id = $originid AND A.salvoconducto = '$inventoryorder' 
			AND A.circumference_bought = $circumference AND A.length_bought = $length GROUP BY A.salvoconducto, A.circumference_bought");
        return $query->result();
    }

    public function check_dispatch_pieces_availablity_squareblock($inventoryorder, $width, $length, $thickness, $originid)
    {
        $query = $this->db->query("SELECT SUM(A.remaining_stock_count) AS remaining_pieces 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id AND B.salvoconducto = A.salvoconducto
                WHERE A.isactive = 1 AND B.isactive = 1 AND B.origin_id = $originid AND A.salvoconducto = '$inventoryorder' 
                AND A.width_bought = $width AND A.length_bought = $length AND A.thickness_bought = $thickness GROUP BY A.salvoconducto, A.width_bought, A.length_bought, A.thickness_bought");
        return $query->result();
    }

    public function add_dispatch($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_container', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_dispatch_tracking($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_open_tracking', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_dispatch_data($data)
    {
        $this->db->insert_batch('tbl_dispatch_data', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_dispatch_details($dispatchid, $containernumber)
    {
        $query = $this->db->query("SELECT A.dispatch_id, A.container_number, A.warehouse_id, D.warehouse_name, 
                    A.product_id, B.product_name, A.product_type_id, C.product_type_name, E.id AS shipping_line_id, 
                    E.shipping_line, A.seal_number, A.container_pic_url, DATE_FORMAT(STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'),'%d/%m/%Y') AS dispatch_date, 
                    A.origin_id, getapplicableorigins_byid(A.origin_id) AS origin, getusername_byuserid(A.createdby) AS uploadedby, 
                    A.total_volume, A.total_pieces, A.isexport, A.is_special_uploaded, A.isclosed, A.measurement_system_id, A.circ_allowance, A.length_allowance, A.rounding_factor 
                    FROM tbl_dispatch_container A 
                    INNER JOIN tbl_product_master B ON B.product_id = A.product_id 
                    INNER JOIN tbl_product_types C ON C.type_id = A.product_type_id 
                    INNER JOIN tbl_warehouses D ON D.whid = A.warehouse_id 
                    INNER JOIN tbl_shippingline_master E ON E.id = A.shipping_line 
                    WHERE A.isactive = 1 AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL) 
                    AND A.dispatch_id = $dispatchid AND A.container_number = '$containernumber'");
        return $query->result();
    }

    public function get_dispatch_data_details($dispatchid, $containernumber)
    {
        $query = $this->db->query("SELECT C.circumference_bought, C.length_bought, A.dispatch_pieces, 
        C.salvoconducto, C.reception_data_id, C.reception_id, A.dispatch_id, B.container_number 
        FROM tbl_dispatch_data A 
        INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id 
        INNER JOIN tbl_reception_data C ON C.reception_data_id = A.reception_data_id AND C.reception_id = A.reception_id 
        WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
        AND A.dispatch_id = $dispatchid AND B.container_number = '$containernumber'
        ORDER BY C.salvoconducto ASC, CASE WHEN (B.product_type_id = 1 OR B.product_type_id = 3) 
        THEN C.width_bought ELSE C.circumference_bought END ASC");
        return $query->result();
    }

    public function update_dispatch($dispatchid, $containernumber, $data)
    {
        $multiClause = array('dispatch_id' => $dispatchid, 'container_number' => $containernumber);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_container', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_dispatch($dispatchid, $containernumber, $userid)
    {
        $query = "UPDATE tbl_dispatch_data, tbl_reception_data, tbl_dispatch_container 
                SET tbl_dispatch_container.isactive = 0, tbl_dispatch_container.updatedby = $userid,
                tbl_dispatch_container.updateddate = NOW(), tbl_dispatch_data.isactive = 0, 
                tbl_dispatch_data.updatedby = $userid, tbl_dispatch_data.updateddate = NOW(), 
                tbl_reception_data.remaining_stock_count = tbl_reception_data.remaining_stock_count + tbl_dispatch_data.dispatch_pieces, 
                tbl_reception_data.isdispatch = 0, 
                tbl_reception_data.container_number = REPLACE(REPLACE(tbl_reception_data.container_number, '$containernumber', ''),', ',''), 
                tbl_reception_data.updatedby = $userid, tbl_reception_data.updateddate = NOW() 
                WHERE tbl_dispatch_data.dispatch_data_id = tbl_dispatch_data.dispatch_data_id 
                AND tbl_dispatch_data.dispatch_id = $dispatchid
                AND tbl_reception_data.reception_data_id = tbl_dispatch_data.reception_data_id 
                AND tbl_reception_data.reception_id = tbl_dispatch_data.reception_id 
                AND tbl_dispatch_container.dispatch_id = $dispatchid 
                AND tbl_dispatch_container.container_number = '$containernumber' AND tbl_dispatch_data.isactive = 1 
                AND (tbl_dispatch_data.isduplicatescanned = 0 OR tbl_dispatch_data.isduplicatescanned IS NULL) ";
        return $this->db->query($query);
        // $updateData = array(
        //     "isactive" => 0, "updatedby" => $userid,
        // );
        // $multiClause = array('dispatch_id' => $dispatchid, 'container_number' => $containernumber);
        // $this->db->where($multiClause);
        // $this->db->set('updateddate', 'NOW()', FALSE);
        // if ($this->db->update('tbl_dispatch_container', $updateData)) {

        //     $updateData = array(
        //         "isactive" => 0, "updatedby" => $userid,
        //     );
        //     $multiClause = array('dispatch_id' => $dispatchid);
        //     $this->db->where($multiClause);
        //     $this->db->set('updateddate', 'NOW()', FALSE);
        //     if ($this->db->update('tbl_dispatch_data', $updateData)) {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // } else {
        //     return false;
        // }
    }

    public function fetch_dispatch_by_status($originid, $status)
    {
        if ($originid == 0) {

            $query = $this->db->query("SELECT A.dispatch_id, A.container_number, C.shipping_line, 
                        D.product_name, E.product_type_name, DATE_FORMAT(STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'),'%d/%m/%Y') AS dispatch_date, 
                        getapplicableorigins_byid(A.origin_id) AS origin, 
                        getusername_byuserid(A.createdby) AS uploadedby, A.isclosed, A.isexport, A.total_volume, A.captured_from_app 
                        FROM tbl_dispatch_container A 
                        INNER JOIN tbl_shippingline_master C ON C.id = A.shipping_line 
                        INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                        INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                        WHERE A.isactive = 1 AND A.isclosed = $status
                        AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL)");
        } else {
            $query = $this->db->query("SELECT A.dispatch_id, A.container_number, C.shipping_line, 
                        D.product_name, E.product_type_name, DATE_FORMAT(STR_TO_DATE(A.dispatch_date, '%d/%m/%Y'),'%d/%m/%Y') AS dispatch_date, 
                        getapplicableorigins_byid(A.origin_id) AS origin, 
                        getusername_byuserid(A.createdby) AS uploadedby, A.isclosed, A.isexport, A.total_volume, A.captured_from_app 
                        FROM tbl_dispatch_container A 
                        INNER JOIN tbl_shippingline_master C ON C.id = A.shipping_line 
                        INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                        INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                        WHERE A.isactive = 1 AND A.origin_id = $originid AND A.isclosed = $status
                        AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL)");
        }
        return $query->result();
    }

    public function fetch_dispatch_tracking_users($dispatchid, $status)
    {
        // $query = $this->db->query("SELECT A.user_id, B.fullname FROM tbl_dispatch_open_tracking A 
        //                 INNER JOIN tbl_user_registration B ON B.userid = A.user_id 
        //                 WHERE A.isactive = 1 AND A.isclosed = $status AND A.dispatch_id = $dispatchid");
        
        $query = $this->db->query("SELECT A.user_id, B.fullname FROM tbl_dispatch_open_tracking A 
                        INNER JOIN tbl_user_registration B ON B.userid = A.user_id 
                        WHERE A.isactive = 1 AND A.isclosed = $status AND A.dispatch_id = $dispatchid
                        UNION ALL
                        SELECT A.createdby user_id, B.fullname FROM tbl_dispatch_container A 
                        INNER JOIN tbl_user_registration B ON B.userid = A.createdby 
                        WHERE A.isactive = 1 AND A.isclosed = $status AND A.dispatch_id = $dispatchid");
        return $query->result();
    }

    public function update_dispatch_tracking_users($userid, $dispatchid, $status, $updatedby)
    {
        
        if($status == 0) {
            $queryCloseReception = "UPDATE tbl_dispatch_container SET isclosed = $status, closedby = 0, closeddate = NOW(), 
                    updateddate = NOW(), updatedby = $updatedby 
                    WHERE dispatch_id = $dispatchid";
                    
            return $this->db->query($queryCloseReception);
        } else {
        
            $query = "UPDATE tbl_dispatch_open_tracking SET isclosed = $status, updateddate = NOW(), updatedby = $updatedby 
                WHERE user_id IN ($userid) AND dispatch_id = $dispatchid AND isactive = 1";
            if ($this->db->query($query)) {
    
                $queryReceptionTracking = $this->db->query("SELECT COUNT(*) AS cnt FROM tbl_dispatch_open_tracking 
                                WHERE isactive = 1 AND isclosed = 0 AND dispatch_id = $dispatchid");
                $dataTracking = $queryReceptionTracking->result();
                if ($dataTracking[0]->cnt == 0) {
                    $queryCloseReception = "UPDATE tbl_dispatch_container SET isclosed = 1, closedby = $updatedby, closeddate = NOW(), 
                        updateddate = NOW(), updatedby = $updatedby 
                        WHERE dispatch_id = $dispatchid";
    
                    return $this->db->query($queryCloseReception);
                } else if ($dataTracking[0]->cnt > 0) {
                    $queryCloseReception = "UPDATE tbl_dispatch_container SET isclosed = 0, closedby = 0, closeddate = NOW(), 
                        updateddate = NOW(), updatedby = $updatedby 
                        WHERE dispatch_id = $dispatchid";
    
                    return $this->db->query($queryCloseReception);
                } else {
                    return true;
                }
            }
        }
    }

    public function get_dispatch_closed_status($dispatchid)
    {
        $query = $this->db->query("SELECT isclosed FROM tbl_dispatch_container WHERE isactive = 1 AND dispatch_id = $dispatchid");
        return $query->result();
    }

    public function get_dispatch_details_by_id($dispatchid)
    {
        $query = $this->db->query("SELECT A.container_number, D.warehouse_name, B.product_name, C.product_type_name, E.shipping_line, 
                    A.seal_number, A.container_pic_url, getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, 
                    getusername_byuserid(A.closedby) AS closedby, 
                    A.total_volume, A.total_pieces 
                    FROM tbl_dispatch_container A 
                    INNER JOIN tbl_product_master B ON B.product_id = A.product_id 
                    INNER JOIN tbl_product_types C ON C.type_id = A.product_type_id 
                    INNER JOIN tbl_warehouses D ON D.whid = A.warehouse_id 
                    INNER JOIN tbl_shippingline_master E ON E.id = A.shipping_line 
                    WHERE A.isactive = 1 AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL) 
                    AND A.dispatch_id = $dispatchid");
        return $query->result();
    }

    public function update_dispatch_id($dispatchid, $data)
    {
        $multiClause = array('dispatch_id' => $dispatchid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_container', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_dispatch_data_by_id($receptiondataid, $receptionid, $containerid, $data)
    {
        $multiClause = array('reception_data_id' => $receptiondataid, 'reception_id' => $receptionid, 'dispatch_id' => $containerid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function add_dispatch_data_single($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_data', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_dispatch_photos($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_photos', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function get_available_container_numbers($originid)
    {
        $query = $this->db->query("SELECT container_number FROM tbl_dispatch_container WHERE isactive = 1 AND isexport = 0 AND origin_id = $originid");
        return $query->result();
    }

    public function get_container_lists($originid)
    {
        $query = $this->db->query("SELECT dispatch_id, container_number, shipping_line, product_id, product_type_id, 
            warehouse_id, dispatch_date, seal_number, category, createdby, UNIX_TIMESTAMP(createddate) AS createddate, isclosed, 
            closedby, UNIX_TIMESTAMP(closeddate) AS closeddate, iscontainer_available, is_special_uploaded, origin_id, total_gross_volume, total_volume, 
            total_pieces, container_number AS existingContainerNumber FROM tbl_dispatch_container
            WHERE isactive = 1 AND isclosed = 0 AND isexport = 0 AND origin_id = $originid");
        return $query->result();
    }

    public function get_dispatch_photos($dispatchid)
    {
        $query = $this->db->query("SELECT image_url FROM tbl_dispatch_photos WHERE is_active = 1 AND dispatch_id = $dispatchid");
        return $query->result();
    }

    public function get_dispatch_detail_bydid($dispatchid)
    {
        $query = $this->db->query("SELECT A.container_number
                    FROM tbl_dispatch_container A 
                    WHERE A.isactive = 1 AND (A.isduplicatedispatched = 0 OR A.isduplicatedispatched IS NULL) 
                    AND A.dispatch_id = $dispatchid");
        return $query->result();
    }

    public function update_dispatch_data_by_id_user($receptiondataid, $receptionid, $containerid, $data, $userid)
    {
        $multiClause = array('reception_data_id' => $receptiondataid, 'reception_id' => $receptionid, 'dispatch_id' => $containerid, 'createdby' => $userid);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_total_dispatch_data_details_app($dispatchid)
    {
        $query = $this->db->query("SELECT SUM(A.dispatch_pieces) As total_pieces, SUM(B.cbm_bought) AS gross_volume, SUM(B.cbm_export) AS net_volume
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = A.dispatch_id 
            INNER JOIN tbl_reception_data B ON B.reception_data_id = A.reception_data_id AND B.reception_id = A.reception_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) AND B.isactive = 1 
            AND (B.isduplicatescanned = 0 OR B.isduplicatescanned IS NULL) AND A.dispatch_id = $dispatchid");
        return $query->result();
    }

    public function get_total_dispatch_data_details_app_open()
    {
        $query = $this->db->query("SELECT A.dispatch_id, SUM(C.cbm_bought) AS total_gross_volume, SUM(C.cbm_export) AS total_volume, SUM(A.dispatch_pieces) AS total_pieces 
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id 
            INNER JOIN tbl_reception_data C ON C.reception_data_id = A.reception_data_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
            AND B.isactive = 1 AND (B.isduplicatedispatched = 0 OR B.isduplicatedispatched IS NULL) 
            AND B.isclosed = 0 AND B.isexport = 0 GROUP BY B.dispatch_id");
        return $query->result();
    }

    public function add_dispatch_data_from_reception($receptionid, $dispatchid, $inventorynumber, $userid, $dispatchdate)
    {
        $query = $this->db->query("SELECT A.reception_data_id, A.reception_id, B.salvoconducto, A.scanned_code, A.circumference_bought, A.length_bought, A.cbm_bought, A.cbm_export 
                FROM tbl_reception_data A 
                INNER JOIN tbl_reception B ON B.reception_id = A.reception_id
                WHERE A.isactive = 1 AND B.isactive = 1 AND B.salvoconducto = '$inventorynumber' AND A.reception_id = $receptionid");
        $data = $query->result();

        foreach ($data as $r) {
            $dataDispatchData = array(
                "dispatch_id" => $dispatchid, "reception_data_id" => $r->reception_data_id,
                "reception_id" => $r->reception_id, "cbm_bought" => $r->cbm_bought, "cbm_export" => $r->cbm_export, 
                "createddate" => date('Y-m-d H:i:s'), "createdby" => $userid,
                "updateddate" => date('Y-m-d H:i:s'), "updatedby" => $userid,
                "isactive" => 1, "scanned_timestamp" => 0, "isduplicatescanned" => 0, 
                "is_special" => 1, "dispatch_pieces" => $r->scanned_code, 
            );

            $this->db->insert('tbl_dispatch_data', $dataDispatchData);
        }

        $updatequery = "UPDATE tbl_reception_data SET remaining_stock_count = 0, isdispatch = 1, dispatch_date = '$dispatchdate', 
            container_number = '$inventorynumber'
            WHERE reception_id = $receptionid AND isactive = 1";
        $this->db->query($updatequery);

        return true;
    }
    
    public function get_closed_containers($originid)
    {
        $query = $this->db->query("SELECT dispatch_id, container_number FROM tbl_dispatch_container WHERE isclosed = 1 AND isexport = 0 AND isactive = 1 AND origin_id = 1 ORDER BY dispatch_id ASC");
        return $query->result();
    }
}
