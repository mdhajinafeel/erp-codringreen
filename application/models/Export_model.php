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
                WHERE A.isactive = 1 AND A.isclosed = 1 AND A.isexport = 0";

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
                A.total_containers, A.total_pieces, CASE WHEN A.origin_id = 3 THEN A.total_net_weight ELSE A.total_net_volume END AS total_net_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, getdispatchids_by_exportid(A.id) AS dispatchids, 
                get_distinct_containers_count_export(A.id) AS d_total_containers   
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
                getdispatchids_by_exportid(A.id) AS dispatchids, 
                A.total_net_weight, A.notify_name, A.notify_details, A.consignee_name, A.consignee_details 
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
                C.seal_number, H.warehouse_name, C.container_pic_url, C.metric_ton, C.short_ton, C.net_lbs, C.diameter_text, 
                C.length_text, C.total_pieces, C.total_gross_volume, C.total_volume 
                FROM tbl_dispatch_container C 
                INNER JOIN tbl_product_master E ON E.product_id = C.product_id
                INNER JOIN tbl_product_types F ON F.type_id = C.product_type_id
                INNER JOIN tbl_warehouses H ON H.whid = C.warehouse_id
                WHERE C.dispatch_id IN ($dispatchids) AND C.origin_id = $originid AND C.isactive = 1
                GROUP BY E.product_name, F.product_type_name, C.container_number, C.dispatch_id,
                STR_TO_DATE(C.dispatch_date, '%d/%m/%Y')
                ORDER BY C.dispatch_id ASC, STR_TO_DATE(C.dispatch_date, '%d/%m/%Y')");
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
        } else if($originid == 3) {
            $query = $this->db->query("SELECT CASE A.is_special WHEN 1 THEN A.dispatch_pieces ELSE C.scanned_code END AS scanned_code, 
                    C.length_bought, C.circumference_bought AS circumference_bought, A.is_special 
                    FROM tbl_dispatch_data A
                    INNER JOIN tbl_dispatch_container B ON B.dispatch_id = A.dispatch_id AND B.isactive = 1
                    INNER JOIN tbl_reception_data C ON C.reception_id = A.reception_id AND C.reception_data_id = A.reception_data_id
                    AND A.isactive = 1 AND C.isactive = 1 AND (C.isdispatch = 1 OR (C.is_special = 1 AND C.isdispatch = 0))
                    WHERE B.dispatch_id = $dispatchid AND B.container_number = '$containernumber' AND B.origin_id = $originid AND A.isactive = 1 
                    AND A.isduplicatescanned = 0 
                    ORDER BY A.dispatch_data_id");
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
                getdispatchids_by_exportid(A.id) AS dispatchids, 
                A.total_net_weight, A.notify_name, A.notify_details, A.consignee_name, A.consignee_details, 
                get_distinct_containers_count_export(A.id) AS d_total_containers 
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

    public function get_total_volume_for_containers($dispatchids, $originid)
    {
        $strQuery = "SELECT SUM(total_gross_volume) AS gross_volume, SUM(total_volume) AS net_volume, 
                SUM(total_pieces) AS total_pieces, SUM(metric_ton) AS total_weight 
                FROM tbl_dispatch_container 
                WHERE origin_id = $originid AND isactive = 1 AND dispatch_id IN ($dispatchids);";
        $query = $this->db->query($strQuery);
        return $query->result();
    }
    
    public function get_export_details_invoice_id($exportid)
    {
        $strQuery = "SELECT A.sa_number, B.pol_short_name AS pol, CONCAT(C.pod_name, ', ', D.code) AS pod, A.bl_no AS booking_no, 
            E.origin_name, get_distinct_containers_count_export(A.id) AS d_total_containers
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_pol B ON B.id = A.pol 
            INNER JOIN tbl_export_pod C ON C.id = A.pod 
            INNER JOIN tbl_countries D ON D.id = C.country_id 
            INNER JOIN tbl_origins E ON E.id = A.origin_id
            WHERE A.isactive = 1 AND A.id = $exportid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_items_by_exportid($exportid)
    {
        $strQuery = "SELECT COUNT(C.dispatch_id) AS total_containers, D.description, SUM(C.metric_ton) AS metric_ton, 
            AVG(C.unit_price) AS unit_price 
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_master_invoice_description D ON D.diameter_text = C.diameter_text AND D.length_text = C.length_text 
            WHERE A.isactive = 1 AND A.origin_id = 3 AND B.isactive = 1 AND A.id = $exportid 
            GROUP BY D.description, C.unit_price 
            ORDER BY D.description";

        $query = $this->db->query($strQuery);
        return $query->result();
    }
    
    public function get_export_volume_by_id($exportid, $sanumber, $originid)
    {
        $strQuery = "SELECT SUM(Y.total_pieces) AS total_pieces, SUM(Y.gross_volume) AS gross_volume, SUM(Y.net_volume) AS net_volume
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, D.circumference_bought AS circumference, (D.length_bought / 100) AS length, 
                CASE WHEN A.measurement_system = 2 THEN TRUNCATE((D.circumference_bought)*(D.circumference_bought)*(D.length_bought) / 16000000, 3) ELSE ROUND((D.circumference_bought)*(D.circumference_bought)*(D.length_bought) * 0.0796 / 1000000, 3) END AS gross_volume, 
                CASE WHEN A.measurement_system = 2 THEN TRUNCATE(((D.circumference_bought) - F.circumference_allowance_export)*((D.circumference_bought) - F.circumference_allowance_export)*((D.length_bought) - F.length_allowance_export) / 16000000, 3) ELSE ROUND(((D.circumference_bought) - F.circumference_allowance_export)*((D.circumference_bought) - F.circumference_allowance_export)*((D.length_bought) - F.length_allowance_export) * 0.0796 / 1000000, 3) END AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1 
            INNER JOIN tbl_origin_company_settings F ON F.origin_id = A.origin_id 
            WHERE A.isactive = 1 AND A.id = $exportid AND A.origin_id = $originid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_data_by_export_id($exportid, $sanumber, $originid, $circallowance, $lengthallowance, $circadjust, $measurementsystem)
    {
        if($measurementsystem == 2 || $measurementsystem == 5) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000, 3) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.origin_id = $originid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else if ($measurementsystem == 12) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume) AS gross_volume, SUM(X.net_volume) AS net_volume, ROUND(SUM(X.gross_volume)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000, 3)  * SUM(C.dispatch_pieces) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000, 3) * SUM(C.dispatch_pieces) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.origin_id = $originid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                ROUND((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) * 0.0796 / 1000000, 3) AS gross_volume, 
                ROUND(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) * 0.0796 / 1000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.origin_id = $originid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        }
        
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_details_invoice_export_origin($exportid)
    {
        $strQuery = "SELECT A.sa_number, LCASE(CONCAT(B.pol_short_name, ', ', E.origin_name)) AS pol, LCASE(CONCAT(C.pod_name, ', ', D.name)) AS pod, A.bl_no AS booking_no, 
            E.origin_name, G.timezone_abbreviation, get_distinct_containers_count_export(A.id) AS d_total_containers  
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_pol B ON B.id = A.pol 
            INNER JOIN tbl_export_pod C ON C.id = A.pod 
            INNER JOIN tbl_countries D ON D.id = C.country_id 
            INNER JOIN tbl_origins E ON E.id = A.origin_id 
            INNER JOIN tbl_origin_timezones F ON F.origin_id = E.id 
            INNER JOIN tbl_master_timezones G ON G.id = F.timezone_id 
            WHERE A.isactive = 1 AND A.id = $exportid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_distinct_container_number($exportid)
    {
        $strQuery = "SELECT DISTINCT LEFT(REPLACE(TRIM(C.container_number), ' ', ''), 11) AS container_number
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id 
            WHERE A.id = $exportid AND A.isactive = 1 AND B.isactive = 1 AND C.isactive = 1";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_sa_numbers_by_origin($originid) {
        $strQuery = "SELECT id, sa_number FROM 
            tbl_export_container_details WHERE isactive = 1 AND origin_id = $originid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function add_export_invoice_history($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_invoice_history', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

    public function add_export_container_price($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_invoice_container', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}
	
	public function update_sales_buyer_ledger($exportid, $userid)
    {
        $query = "UPDATE tbl_sales_buyer_ledger SET is_active = 0, updated_by = $userid,
                updated_date = NOW() 
                WHERE export_id = $exportid AND is_active = 1";
        return $this->db->query($query);
    }

    public function add_sales_buyer_ledger($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_sales_buyer_ledger', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}
	
	public function fetch_invoice_history($exportid) {
        $strQuery = "SELECT A.id, A.invoice_date, A.total_containers, A.circ_allowance, A.length_allowance, 
            A.circ_adjustment, A.measurement_system, A.service_enabled, A.service_sales_percentage, A.total_service_cost, 
            A.advance_enabled, A.advance_cost, A.total_advance_cost, A.accounting_invoice, A.claim_id, A.claim_amount, 
            A.shorts_base_price, A.enabled_jump_shorts, A.semi_base_price, A.enabled_jump_semi, A.long_base_price, 
            A.enabled_jump_long, A.credit_notes, A.buyer_id, A.bank_id, A.total_volume, A.invoice_unit_price, 
            A.total_invoice_value, B.fullname AS created_by, C.buyer_name, D.bank_name, E.seller_name, A.total_sales_value 
            FROM tbl_export_invoice_history A 
            INNER JOIN tbl_user_registration B ON B.userid = A.created_by 
            INNER JOIN tbl_master_buyers C ON C.id = A.buyer_id 
            INNER JOIN tbl_master_invoice_banks D ON D.id = A.bank_id 
            INNER JOIN tbl_master_sellers E ON E.id = A.seller_id 
            WHERE A.is_active = 1 AND A.export_id = $exportid ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_container_invoice_price_by_export($exportid) {
        $strQuery = "SELECT B.id AS invoice_id, A.container_number, A.unit_price 
            FROM tbl_export_invoice_container A 
            INNER JOIN tbl_export_invoice_history B ON B.id = A.export_invoice_id 
            WHERE A.is_active = 1 AND A.export_id = $exportid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    // public function fetch_invoice_history_invoice($exportid, $invoiceid) {
    //     $strQuery = "SELECT A.id, A.invoice_date, A.total_containers, A.circ_allowance, A.length_allowance, 
    //         A.circ_adjustment, A.measurement_system, A.service_enabled, A.service_sales_percentage, A.total_service_cost, 
    //         A.advance_enabled, A.advance_cost, A.total_advance_cost, A.accounting_invoice, A.claim_id, A.claim_amount, 
    //         A.shorts_base_price, A.enabled_jump_shorts, A.semi_base_price, A.enabled_jump_semi, A.long_base_price, 
    //         A.enabled_jump_long, A.credit_notes, A.buyer_id, A.bank_id, A.total_volume, A.invoice_unit_price, 
    //         A.total_invoice_value, B.fullname AS created_by, C.buyer_name, D.bank_name
    //         FROM tbl_export_invoice_history A 
    //         INNER JOIN tbl_user_registration B ON B.userid = A.created_by 
    //         INNER JOIN tbl_master_buyers C ON C.id = A.buyer_id 
    //         INNER JOIN tbl_master_invoice_banks D ON D.id = A.bank_id 
    //         WHERE A.is_active = 1 AND A.export_id = $exportid AND A.id = $invoiceid ORDER BY A.id DESC";

    //     $query = $this->db->query($strQuery);
    //     return $query->result();
    // }
    
    public function fetch_invoice_history_invoice($exportid, $invoiceid) {
        $strQuery = "SELECT A.id, A.invoice_date, A.total_containers, A.circ_allowance, A.length_allowance, 
            A.circ_adjustment, A.measurement_system, A.service_enabled, A.service_sales_percentage, A.total_service_cost, 
            A.advance_enabled, A.advance_cost, A.total_advance_cost, A.accounting_invoice, A.claim_id, A.claim_amount, 
            A.shorts_base_price, A.enabled_jump_shorts, A.semi_base_price, A.enabled_jump_semi, A.long_base_price, 
            A.enabled_jump_long, A.credit_notes, A.buyer_id, A.bank_id, A.total_volume, A.invoice_unit_price, 
            A.total_invoice_value, B.fullname AS created_by, C.buyer_name, D.bank_name, A.seller_id, E.seller_name 
            FROM tbl_export_invoice_history A 
            INNER JOIN tbl_user_registration B ON B.userid = A.created_by 
            INNER JOIN tbl_master_buyers C ON C.id = A.buyer_id 
            INNER JOIN tbl_master_invoice_banks D ON D.id = A.bank_id 
            INNER JOIN tbl_master_sellers E ON E.id = A.seller_id 
            WHERE A.is_active = 1 AND A.export_id = $exportid AND A.id = $invoiceid ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }
    
    public function get_ledger_transaction_details_by_export($exportid)
    {
        $query = $this->db->query("SELECT buyer_id, total_invoice_value 
				FROM tbl_sales_buyer_ledger A WHERE A.is_active = 1 
				AND A.ledger_type = 2 AND A.export_id = $exportid");

        return $query->result();
    }
    
    public function fetch_exportid_trader($traderid)
    {
        if ($traderid > 0) {
            $strQuery = "SELECT subquery.max_id AS history_id, eih.export_id, eih.buyer_id
            FROM tbl_export_invoice_history eih
            JOIN (
                SELECT export_id, MAX(id) AS max_id
                FROM tbl_export_invoice_history
                WHERE is_active = 1 AND buyer_id = $traderid
                GROUP BY export_id
            ) subquery
            ON eih.id = subquery.max_id
            ORDER BY eih.export_id";
        } else {
            $strQuery = "SELECT subquery.max_id AS history_id, eih.export_id, eih.buyer_id
            FROM tbl_export_invoice_history eih
            JOIN (
                SELECT export_id, MAX(id) AS max_id
                FROM tbl_export_invoice_history
                WHERE is_active = 1
                GROUP BY export_id
            ) subquery
            ON eih.id = subquery.max_id
            ORDER BY eih.export_id";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function trader_exports($exportid)
    {
        $strQuery = "SELECT A.id, A.sa_number, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, E.shipping_line, 
                A.total_containers, A.total_pieces, CASE WHEN A.origin_id = 3 THEN A.total_net_weight ELSE A.total_net_volume END AS total_net_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, getdispatchids_by_exportid(A.id) AS dispatchids, 
                get_distinct_containers_count_export(A.id) AS d_total_containers   
                FROM tbl_export_container_details A 
                INNER JOIN tbl_product_types B ON B.type_id = A.product_type_id 
                INNER JOIN tbl_export_pol C ON C.id = A.pol 
                INNER JOIN tbl_export_pod D ON D.id = A.pod 
                INNER JOIN tbl_countries F ON F.id = D.country_id 
                INNER JOIN tbl_shippingline_master E ON E.id = A.liner 
                INNER JOIN tbl_user_registration G ON G.userid = A.createdby
                WHERE A.isactive = 1 AND A.id IN ($exportid)";

        $strQuery = $strQuery  . " ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_order_details_by_id_for_trader($exportid, $sanumber)
    {
        $strQuery = "SELECT A.id, A.sa_number, A.product_type_id, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, A.pod, A.liner, E.shipping_line, 
                A.total_containers, A.total_pieces, A.total_net_volume, A.total_gross_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, A.measurement_system, 
                A.vessel_name, A.shipped_date, A.bl_no, A.bl_date, A.client_pno, 
                getdispatchids_by_exportid(A.id) AS dispatchids, 
                A.total_net_weight, A.notify_name, A.notify_details, A.consignee_name, A.consignee_details, 
                get_distinct_containers_count_export(A.id) AS d_total_containers 
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

    public function get_details_invoice_history_trader($invoiceid)
    {
        $strQuery = "SELECT service_enabled, service_sales_percentage, advance_enabled, advance_cost, shorts_base_price, 
            semi_base_price, long_base_price, enabled_jump_shorts, enabled_jump_semi, enabled_jump_long 
            FROM tbl_export_invoice_history WHERE id = $invoiceid";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_order_details_by_id_trader($exportid, $sanumber)
    {
        $strQuery = "SELECT A.id, A.sa_number, A.product_type_id, B.product_type_name, C.pol_name, 
                CONCAT(D.pod_name, ', ', F.code) AS pod_name, A.pod, A.liner, E.shipping_line, 
                A.total_containers, A.total_pieces, A.total_net_volume, A.total_gross_volume, G.fullname, 
                getapplicableorigins_byid(A.origin_id) AS origin, A.origin_id, A.measurement_system, 
                A.vessel_name, A.shipped_date, A.bl_no, A.bl_date, A.client_pno, 
                getdispatchids_by_exportid(A.id) AS dispatchids, 
                A.total_net_weight, A.notify_name, A.notify_details, A.consignee_name, A.consignee_details, 
                get_distinct_containers_count_export(A.id) AS d_total_containers 
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

    public function fetch_container_invoice_price_by_export_trader($exportid)
    {
        $strQuery = "SELECT B.id AS invoice_id, A.container_number, A.unit_price 
            FROM tbl_export_invoice_container A 
            INNER JOIN tbl_export_invoice_history B ON B.id = A.export_invoice_id 
            WHERE A.is_active = 1 AND A.export_id = $exportid AND B.is_trader = 1";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_invoice_history_trader($exportid)
    {
        $strQuery = "SELECT A.id, A.invoice_date, A.total_containers, A.circ_allowance, A.length_allowance, 
            A.circ_adjustment, A.measurement_system, A.service_enabled, A.service_sales_percentage, A.total_service_cost, 
            A.advance_enabled, A.advance_cost, A.total_advance_cost, A.accounting_invoice, A.claim_id, A.claim_amount, 
            A.shorts_base_price, A.enabled_jump_shorts, A.semi_base_price, A.enabled_jump_semi, A.long_base_price, 
            A.enabled_jump_long, A.credit_notes, A.buyer_id, A.bank_id, A.total_volume, A.invoice_unit_price, 
            A.total_invoice_value, B.fullname AS created_by, C.buyer_name, D.bank_name, E.seller_name, A.total_sales_value, 
            A.tp_cost
            FROM tbl_export_invoice_history A 
            INNER JOIN tbl_user_registration B ON B.userid = A.created_by 
            INNER JOIN tbl_master_buyers C ON C.id = A.buyer_id 
            INNER JOIN tbl_master_invoice_banks D ON D.id = A.bank_id 
            INNER JOIN tbl_master_sellers E ON E.id = A.seller_id 
            WHERE A.is_active = 1 AND A.export_id = $exportid AND A.is_trader = 1 ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_data_by_export_id_trader($exportid, $sanumber, $circallowance, $lengthallowance, $circadjust, $measurementsystem)
    {
        if ($measurementsystem == 2 || $measurementsystem == 5) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000, 3) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else if ($measurementsystem == 12) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume) AS gross_volume, SUM(X.net_volume) AS net_volume, ROUND(SUM(X.gross_volume)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000 * SUM(C.dispatch_pieces), 3) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000 * SUM(C.dispatch_pieces), 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                ROUND((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) * 0.0796 / 1000000, 3) AS gross_volume, 
                ROUND(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) * 0.0796 / 1000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id = $exportid AND A.sa_number = '$sanumber' AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        }

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_container_details_bydispatchids($dispatchids)
    {
        $strQuery = "SELECT dispatch_id, container_number, total_pieces, total_volume FROM tbl_dispatch_container WHERE dispatch_id IN ($dispatchids) AND isactive = 1";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function add_exportdocuments($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_documents', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_exportdocuments($data, $exportid, $exporttype)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$multiClause = array('export_id' => $exportid, 'export_type' => $exporttype);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_export_documents', $data)) {
			return true;
		} else {
			return false;
		}
	}

    public function add_exportcontainerdoc($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_document_container', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

	public function update_exportcontainerdoc($data, $exportid, $exporttype)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$multiClause = array('export_id' => $exportid, 'export_type' => $exporttype);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_export_document_container', $data)) {
			return true;
		} else {
			return false;
		}
	}

    public function fetch_export_documents($exportid, $exporttype)
    {
        $strQuery = "SELECT id, export_id, invoice_no, supplier_id, REPLACE(invoice_date, '/', '-') AS invoice_date, sub_total, tax_total, allowance_total, payable_total 
            FROM tbl_export_documents WHERE is_active = 1 AND export_type = $exporttype AND export_id = $exportid";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_export_container_documents($exportid, $exporttype, $exportdocid)
    {
        $strQuery = "SELECT dispatch_id, container_value FROM tbl_export_document_container 
            WHERE is_active = 1 AND export_type = $exporttype AND export_id = $exportid AND export_doc_id = $exportdocid";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function update_exportcontainercost($data, $exportid)
	{
		$this->db->set('updated_date', 'NOW()', FALSE);
		$multiClause = array('export_id' => $exportid);
		$this->db->where($multiClause);
		if ($this->db->update('tbl_export_document_container_cost', $data)) {
			return true;
		} else {
			return false;
		}
	}

    public function add_exportcontainercost($data)
	{
		$this->db->set('created_date', 'NOW()', FALSE);
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->insert('tbl_export_document_container_cost', $data);
		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			return $insert_id;
		} else {
			return 0;
		}
	}

    public function fetch_export_container_costs($exportid)
    {
        $strQuery = "SELECT dispatch_id, unit_price, exchange_rate FROM tbl_export_document_container_cost 
            WHERE is_active = 1 AND export_id = $exportid";
        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function fetch_merge_invoice_history_invoice() {
        $strQuery = "SELECT A.id, A.invoice_date, A.total_containers, A.circ_allowance, A.length_allowance, 
            A.circ_adjustment, A.measurement_system, A.service_enabled, A.service_sales_percentage, A.total_service_cost, 
            A.advance_enabled, A.advance_cost, A.total_advance_cost, A.accounting_invoice, A.claim_id, A.claim_amount, 
            A.shorts_base_price, A.enabled_jump_shorts, A.semi_base_price, A.enabled_jump_semi, A.long_base_price, 
            A.enabled_jump_long, A.credit_notes, A.buyer_id, A.bank_id, A.total_volume, A.invoice_unit_price, 
            A.total_invoice_value, B.fullname AS created_by, C.buyer_name, D.bank_name, A.seller_id, E.seller_name 
            FROM tbl_export_invoice_history A 
            INNER JOIN tbl_user_registration B ON B.userid = A.created_by 
            INNER JOIN tbl_master_buyers C ON C.id = A.buyer_id 
            INNER JOIN tbl_master_invoice_banks D ON D.id = A.bank_id 
            INNER JOIN tbl_master_sellers E ON E.id = A.seller_id 
            WHERE A.is_active = 1 AND A.id IN (219, 221, 222) ORDER BY A.id DESC";

        $query = $this->db->query($strQuery);
        return $query->result();
    }

    public function get_export_data_by_export_id_merge($originid, $circallowance, $lengthallowance, $circadjust, $measurementsystem)
    {
        if($measurementsystem == 2 || $measurementsystem == 5) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000, 3) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id IN (622, 623, 624) AND A.origin_id = $originid AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else if ($measurementsystem == 12) {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume) AS gross_volume, SUM(X.net_volume) AS net_volume, ROUND(SUM(X.gross_volume)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                TRUNCATE((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) / 16000000, 3)  * SUM(C.dispatch_pieces) AS gross_volume, 
                TRUNCATE(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) / 16000000, 3) * SUM(C.dispatch_pieces) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 AND A.id IN (622, 623, 624) AND A.origin_id = $originid AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        } else {
            $strQuery = "SELECT Y.dispatch_id, LEFT(REPLACE(TRIM(Y.container_number), ' ', ''), 11) AS container_number, Y.total_pieces, Y.gross_volume, Y.net_volume, Y.gross_cft, Y.net_cft, Y.avg_circumference, Y.avg_length, 
            CASE WHEN (Y.product_type_id = 1 OR Y.product_type_id = 3) THEN 4 ELSE CASE WHEN Y.avg_length < 2.75 THEN 1 WHEN Y.avg_length < 6 THEN 2 ELSE 3 END END AS product_type
            FROM (
                SELECT X.dispatch_id, X.container_number, X.product_type_id, SUM(X.total_pieces) AS total_pieces, SUM(X.gross_volume * X.total_pieces) AS gross_volume, SUM(X.net_volume * X.total_pieces) AS net_volume, ROUND(SUM(X.gross_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS gross_cft, ROUND(SUM(X.net_volume * X.total_pieces)/ SUM(X.total_pieces)*35.315,2) AS net_cft, TRUNCATE(SUM(X.circumference * X.total_pieces)/SUM(X.total_pieces),0) AS avg_circumference, ROUND(SUM(X.length * X.total_pieces)/SUM(X.total_pieces),2) AS avg_length
            FROM (
                SELECT E.container_number, B.dispatch_id, E.product_type_id, SUM(C.dispatch_pieces) AS total_pieces, (D.circumference_bought + $circadjust) AS circumference, (D.length_bought / 100) AS length, 
                ROUND((D.circumference_bought + $circadjust) * (D.circumference_bought + $circadjust) * (D.length_bought) * 0.0796 / 1000000, 3) AS gross_volume, 
                ROUND(((D.circumference_bought + $circadjust) - $circallowance) * ((D.circumference_bought + $circadjust) - $circallowance)*((D.length_bought) - $lengthallowance) * 0.0796 / 1000000, 3) AS net_volume
            FROM tbl_export_container_details A 
            INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
            INNER JOIN tbl_dispatch_container E ON E.dispatch_id = B.dispatch_id 
            INNER JOIN tbl_dispatch_data C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
            INNER JOIN tbl_reception_data D ON D.reception_data_id = C.reception_data_id AND D.isactive = 1
            WHERE A.isactive = 1 A.id IN (622, 623, 624) AND A.origin_id = $originid AND B.isactive = 1 
            GROUP BY E.container_number, D.circumference_bought, D.length_bought) X 
            GROUP BY X.container_number) Y
            ORDER BY Y.dispatch_id ASC ";
        }
        
        $query = $this->db->query($strQuery);
        return $query->result();
    }
}
