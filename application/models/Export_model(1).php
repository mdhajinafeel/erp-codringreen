<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Export_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all_export_containers($originid, $productid, $producttypeid)
    {
        $strQuery = "SELECT A.dispatch_id, A.container_number, B.warehouse_name, C.shipping_line, D.product_name, 
                E.product_type_name, A.total_pieces, A.total_volume, getapplicableorigins_byid(A.origin_id) AS origin  
                FROM tbl_dispatch_container A 
                INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                INNER JOIN tbl_shippingline_master C ON C.id = A.shipping_line 
                INNER JOIN tbl_product_master D ON D.product_id = A.product_id 
                INNER JOIN tbl_product_types E ON E.type_id = A.product_type_id 
                WHERE A.isactive = 1 AND (A.isclosed = 1 OR A.isclosed = 0) AND A.isexport = 0";

        if ($originid > 0) {
            $strQuery = $strQuery . " AND A.origin_id = $originid";
        }

        if ($productid > 0) {
            $strQuery = $strQuery . " AND A.product_id = $productid";
        }

        if ($producttypeid > 0) {

            if ($producttypeid == 1 || $producttypeid == 3) {
                $producttypeid = "1, 3";
            } else if ($producttypeid == 2 || $producttypeid == 4) {
                $producttypeid = "2, 4";
            }
            $strQuery = $strQuery . " AND A.product_type_id IN ($producttypeid)";
        }

        $strQuery = $strQuery  . " ORDER BY STR_TO_DATE(A.dispatch_date, '%d/%m/%Y')";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    //EXPORT CHECK
    public function get_origin_count_for_export($dispatchid)
    {
        $query = $this->db->query("SELECT COUNT(DISTINCT A.origin_id) AS cnt FROM tbl_dispatch_container A 
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid)");
        return $query->result();
    }

    public function get_warehouse_count_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT COUNT(DISTINCT A.warehouse_id) AS cnt FROM tbl_dispatch_container A 
                    INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid");
        return $query->result();
    }

    public function get_warehouse_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT A.warehouse_id, B.pol AS pol_id, C.pol_name FROM tbl_dispatch_container A 
                    INNER JOIN tbl_warehouses B ON B.whid = A.warehouse_id 
                    INNER JOIN tbl_export_pol C ON C.id = B.pol
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid 
                    GROUP BY A.warehouse_id");
        return $query->result();
    }

    public function get_shippingline_count_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT COUNT(DISTINCT A.shipping_line) AS cnt FROM tbl_dispatch_container A 
                    INNER JOIN tbl_shippingline_master B ON B.id = A.shipping_line 
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid");
        return $query->result();
    }

    public function get_shippingline_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT B.id AS shipping_line_id, B.shipping_line FROM tbl_dispatch_container A 
                    INNER JOIN tbl_shippingline_master B ON B.id = A.shipping_line 
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid 
                    GROUP BY A.shipping_line");
        return $query->result();
    }

    public function get_producttype_count_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT COUNT(DISTINCT A.product_type_id) AS cnt FROM tbl_dispatch_container A 
                    WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid");
        return $query->result();
    }

    public function get_producttype_for_export($dispatchid, $originid)
    {
        $query = $this->db->query("SELECT A.product_type_id, B.product_type_name FROM tbl_dispatch_container A 
                            INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id
                            WHERE A.isactive = 1 AND A.dispatch_id IN ($dispatchid) AND A.origin_id = $originid 
                            GROUP BY A.product_type_id");
        return $query->result();
    }

    //EXPORT
    public function get_total_volume($dispatchid, $grossformula, $netformula)
    {
        $strQuery = "SELECT SUM(total_pieces) AS total_pieces, SUM(L.grossvolume) AS grossvolume, SUM(L.netvolume) AS netvolume
            FROM (SELECT SUM(dispatch_pieces) AS total_pieces, 
            $grossformula AS grossvolume, 
            $netformula AS netvolume 
            FROM tbl_dispatch_data A 
            INNER JOIN tbl_reception_data B ON B.reception_data_id = A.reception_data_id AND B.reception_id = A.reception_id 
            WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
            AND B.isactive = 1 AND (B.isduplicatescanned = 0 OR B.isduplicatescanned IS NULL) 
            AND A.dispatch_id IN ($dispatchid) GROUP BY B.circumference_bought, B.length_bought) L";
        $query = $this->db->query($strQuery);
        return $query->result();
    }
    
    public function get_total_volume_square_block($dispatchid)
    {
        $strQuery = "SELECT SUM(total_gross_volume) AS grossvolume, SUM(total_volume) AS netvolume, SUM(total_pieces) AS total_pieces FROM tbl_dispatch_container WHERE dispatch_id = $dispatchid AND isactive = 1";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_sa_number_count($sanumber, $originid)
    {
        $query = $this->db->query("SELECT COUNT(sa_number) as cnt FROM tbl_export_container_details 
            WHERE isactive = 1 AND origin_id = $originid 
            AND REGEXP_REPLACE(REPLACE(sa_number,' ',''),'[\]\\[!@#$%.&*`~^_{}:;<>/\\|()-]+','') = REGEXP_REPLACE(REPLACE('$sanumber',' ',''),'[\]\\[!@#$%.&*`~^_{}:;<>/\\|()-]+','')");
        return $query->result();
    }

    public function add_export_details($data)
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_export_container_details', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function add_export_container_data($data)
    {
        $this->db->insert_batch("tbl_export_container", $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_export_dispatch($dispatchids, $userid)
    {
        $query = "UPDATE tbl_dispatch_container SET isexport = 1, exportedby = $userid,
                updatedby =$userid , updateddate = NOW() 
                WHERE dispatch_id IN ($dispatchids)";
        return $this->db->query($query);
    }

    public function all_exports($originid, $producttypeid, $shippingid)
    {
        $strQuery = "SELECT A.id, A.sa_number, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, E.shipping_line, 
                A.total_containers, A.total_pieces, A.total_net_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, getdispatchids_by_exportid(A.id) AS dispatchids  
                FROM tbl_export_container_details A 
                INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id 
                INNER JOIN tbl_export_pol C ON C.id = A.pol 
                INNER JOIN tbl_export_pod D ON D.id = A.pod 
                INNER JOIN tbl_countries F ON F.id = D.country_id 
                INNER JOIN tbl_shippingline_master E ON E.id = A.liner 
                INNER JOIN tbl_user_registration G ON G.userid = A.createdby
                WHERE A.isactive = 1";

        if ($originid > 0) {
            $strQuery = $strQuery . " AND A.origin_id = $originid";
        }

        if ($producttypeid > 0) {

            if ($producttypeid == 1 || $producttypeid == 3) {
                $producttypeid = "1, 3";
            } else if ($producttypeid == 2 || $producttypeid == 4) {
                $producttypeid = "2, 4";
            }
            $strQuery = $strQuery . " AND A.product_type_id IN ($producttypeid)";
        }

        if ($shippingid > 0) {
            $strQuery = $strQuery . " AND A.liner = $shippingid";
        }

        $strQuery = $strQuery  . " ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_details_by_id($exportid, $sanumber)
    {
        $strQuery = "SELECT A.id, A.sa_number, A.product_type_id, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, A.pod, A.liner, E.shipping_line, 
                A.total_containers, A.total_pieces, A.total_net_volume, A.total_gross_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, A.measurement_system, 
                A.vessel_name, A.shipped_date, A.bl_no, A.bl_date, A.client_pno, 
                getdispatchids_by_exportid(A.id) AS dispatchids 
                FROM tbl_export_container_details A 
                INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id 
                INNER JOIN tbl_export_pol C ON C.id = A.pol 
                INNER JOIN tbl_export_pod D ON D.id = A.pod 
                INNER JOIN tbl_countries F ON F.id = D.country_id 
                INNER JOIN tbl_shippingline_master E ON E.id = A.liner 
                INNER JOIN tbl_user_registration G ON G.userid = A.createdby
                WHERE A.isactive = 1 AND A.id = $exportid AND A.sa_number = '$sanumber'";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function update_export_details($exportid, $sanumber, $data)
    {
        $multiClause = array('id' => $exportid, 'sa_number' => $sanumber);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_export_container_details', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_export_container_data($exportid, $dispatchid, $data)
    {
        $multiClause = array('container_details_id' => $exportid, 'dispatch_id' => $dispatchid, 'isactive' => 1);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_export_container', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_exports($exportid, $sanumber, $dispatchids, $userid)
    {
        $updateData = array(
            "isactive" => 0, "updatedby" => $userid,
        );
        $multiClause = array('id' => $exportid, 'sa_number' => $sanumber);
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_export_container_details', $updateData)) {

            $updateData = array(
                "isactive" => 0, "updatedby" => $userid,
            );
            $multiClause = array('container_details_id' => $exportid);
            $this->db->where($multiClause);
            $this->db->set('updateddate', 'NOW()', FALSE);
            if ($this->db->update('tbl_export_container', $updateData)) {
                // $updateData = array(
                //     "isexport" => 0, "exportedby" => 0,
                // );

                $updateQuery = "UPDATE tbl_dispatch_container SET isexport = 0, exportedby = 0, updateddate = NOW() 
                    WHERE dispatch_id IN ($dispatchids)";
                $this->db->query($updateQuery);

                // $multiClause = array('dispatch_id' => $dispatchids);
                // $this->db->where_in($multiClause);
                // $this->db->set('updateddate', 'NOW()', FALSE);
                //if ($this->db->update('tbl_dispatch_container', $updateData)) {
                if($this->db->query($updateQuery)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    //EXPORT SUMMARY
    public function get_container_details($dispatchids, $originid)
    {
        $query = $this->db->query("SELECT E.product_name, F.product_type_name, C.container_number, C.dispatch_id,
                DATE_FORMAT(STR_TO_DATE(C.dispatch_date, '%d/%m/%Y'), '%d/%m/%Y') as dispatch_date,
                C.product_type_id,
                C.seal_number, H.warehouse_name, C.container_pic_url
                FROM tbl_dispatch_container C 
                INNER JOIN tbl_product_master E ON E.product_id = C.product_id
                INNER JOIN tbl_product_types F ON F.type_id = C.product_type_id
                INNER JOIN tbl_warehouses H ON H.whid = C.warehouse_id
                WHERE C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid AND C.isactive = 1
                GROUP BY E.product_name, F.product_type_name, C.container_number, C.dispatch_id,
                STR_TO_DATE(C.dispatch_date, '%d/%m/%Y')
                ORDER BY STR_TO_DATE(C.dispatch_date, '%d/%m/%Y')");
        return $query->result();
    }

    public function get_container_dispatch_data_old($dispatchid, $containernumber, $originid)
    {
        if($originid == 1) {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN SUM(A.dispatch_pieces) ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, (C.circumference_bought + 1) AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 GROUP BY C.circumference_bought, C.length_bought 
                    ORDER BY C.circumference_bought, C.length_bought, A.dispatch_data_id");
        } else {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN SUM(A.dispatch_pieces) ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, C.circumference_bought AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 GROUP BY C.circumference_bought, C.length_bought 
                    ORDER BY C.circumference_bought, C.length_bought, A.dispatch_data_id");
        }
        return $query->result();
    }
    
    public function get_container_dispatch_data($dispatchid, $containernumber, $originid)
    {
        if($originid == 1) {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN SUM(A.dispatch_pieces) ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, C.circumference_bought AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 GROUP BY C.circumference_bought, C.length_bought 
                    ORDER BY C.circumference_bought, C.length_bought, A.dispatch_data_id");
        } else {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN SUM(A.dispatch_pieces) ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, C.circumference_bought AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 GROUP BY C.circumference_bought, C.length_bought 
                    ORDER BY C.circumference_bought, C.length_bought, A.dispatch_data_id");
        }
        return $query->result();
    }
    
    public function get_container_dispatch_data_square_blocks($dispatchid, $containernumber, $originid)
    {
        if($originid == 1) {
            $query = $this->db->query("SELECT C.circumference_bought, C.length_bought, C.thickness_bought, C.width_bought, A.dispatch_pieces, 
                    C.salvoconducto, C.reception_data_id, C.reception_id, A.dispatch_id, B.container_number 
                    FROM tbl_dispatch_data A 
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id 
                    INNER JOIN tbl_reception_data C ON C.reception_data_id = A.reception_data_id AND C.reception_id = A.reception_id 
                    WHERE A.isactive = 1 AND (A.isduplicatescanned = 0 OR A.isduplicatescanned IS NULL) 
                    AND A.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid
                    ORDER BY C.salvoconducto ASC, CASE WHEN (B.product_type_id = 1 OR B.product_type_id = 3) 
                    THEN C.width_bought ELSE C.circumference_bought END ASC");
        } else {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN SUM(A.dispatch_pieces) ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, C.circumference_bought AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 GROUP BY C.circumference_bought, C.length_bought 
                    ORDER BY C.circumference_bought, C.length_bought, A.dispatch_data_id");
        }
        return $query->result();
    }

    public function get_export_order_details_by_id($exportid, $sanumber, $originid)
    {
        $strQuery = "SELECT A.id, A.sa_number, A.product_type_id, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, A.pod, A.liner, E.shipping_line, 
                A.total_containers, A.total_pieces, A.total_net_volume, A.total_gross_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, A.measurement_system, 
                A.vessel_name, A.shipped_date, A.bl_no, A.bl_date, A.client_pno, 
                getdispatchids_by_exportid(A.id) AS dispatchids 
                FROM tbl_export_container_details A 
                INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id 
                INNER JOIN tbl_export_pol C ON C.id = A.pol 
                INNER JOIN tbl_export_pod D ON D.id = A.pod 
                INNER JOIN tbl_countries F ON F.id = D.country_id 
                INNER JOIN tbl_shippingline_master E ON E.id = A.liner 
                INNER JOIN tbl_user_registration G ON G.userid = A.createdby
                WHERE A.isactive = 1 AND A.id = $exportid AND A.sa_number = '$sanumber' AND A.origin_id = $originid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }
}
