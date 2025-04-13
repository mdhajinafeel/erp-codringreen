<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    //EXPORT MAP
    public function get_export_map_details($originId, $year)
	{
// 		$query = $this->db->query("SELECT COUNT(A.sa_number) AS total_shipment, SUM(total_containers) AS total_containers, 
//                 SUM(A.total_net_volume) AS total_volume, B.pod_name, ROUND(B.latitude, 4) AS latitude, 
//                 ROUND(B.longitude, 4) AS longitude, 
//                 ROUND((SUM(A.total_containers) / getcontainercount_byexportid(0))*100,2) AS contribution, B.color_code
//                 FROM tbl_export_container_details A 
//                 INNER JOIN tbl_export_pod B ON B.id = A.pod
//                 WHERE A.isactive = 1 
//                 GROUP BY A.pod");

        $strQuery = "SELECT COUNT(DISTINCT A.sa_number) AS total_shipment, COUNT(C.container_number) AS total_containers, 
                D.pod_name, SUM(DISTINCT A.total_net_volume) AS total_net_volume, 
                SUM(DISTINCT A.total_net_weight) AS total_net_weight, ROUND(D.latitude, 4) AS latitude, 
                ROUND(D.longitude, 4) AS longitude, D.color_code, 
                ROUND(COUNT(C.container_number) / getcontainercount_by_year_origin($year, $originId) * 100, 0) AS contribution
                FROM tbl_export_container_details A 
                INNER JOIN tbl_export_container B ON B.container_details_id = A.id 
                INNER JOIN tbl_dispatch_container C ON C.dispatch_id = B.dispatch_id AND C.isactive = 1 
                INNER JOIN tbl_export_pod D ON D.id = A.pod 
                WHERE A.isactive = 1 AND A.origin_id = $originId AND A.sa_number LIKE '%$year%' AND LENGTH(REPLACE(TRIM(C.container_number), ' ', '')) = 11 
                GROUP BY A.pod";

		$query = $this->db->query($strQuery);

		return $query->result();
	}
}