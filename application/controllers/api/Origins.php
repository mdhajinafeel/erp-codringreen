<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Origins extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        try {
            if ($this->input->method(TRUE) == "GET") {
            
                    $getOrigins = $this->Master_model->all_active_origins();

                    $return_arr_origins = array();
                    foreach($getOrigins as $origin) {
                        $row_array_origin["originId"] = (int) $origin->id;
                        $row_array_origin["originName"] = $origin->origin_name;
                        array_push($return_arr_origins, $row_array_origin);
                    }
                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $return_arr_origins;
                        http_response_code(200);
                        $this->output($Return);
            }
        } catch (Exception $e) {
            $Return["status"] = false;
            $Return["message"] = "Internal Server Error";
            http_response_code(500);
            $this->output($Return);
        }
    }
}