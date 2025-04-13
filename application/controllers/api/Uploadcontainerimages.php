<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Uploadcontainerimages extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
        $this->load->library("jwttoken");
        $this->load->helper('url');
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
            if ($this->input->method(TRUE) == "POST") {

                $headers = apache_request_headers();
                foreach ($headers as $header => $value) {
                    if ($header == "Authorization") {
                        list($a, $b) = explode(" ", $value);
                        $requestBearerToken = $b;
                    }
                }
                $token = JWT::decode($requestBearerToken, JWT_SECRET);

                $userid = $token->userid;
                $originid = $token->originid;
                $roleid = $token->roleid;

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);
                    if ($checkUserExists == true) {

                        $return_arr_response = array();
                        $imgArrayCount = 0;
                        $arrayImageId = explode(', ', $_REQUEST["generatedImageId"]);
                        $arrayContainerNumber = explode(', ', $_REQUEST["containerNumber"]);
                        
                        foreach ($_FILES['files']['tmp_name'] as $key => $value) {

                            $file_tmpname = $_FILES['files']['tmp_name'][$key];
                            if (empty($file_tmpname)) {
                                $row_array['imageUrl'] = "";
                                $row_array['uploadedStatus'] = false;
                            } else {
            
                                $file_name = str_replace(" ", "", $_FILES['files']['name'][$key]);
            
                                $target_dir = "./uploads/containerimages/";
                                $fullImagePath =  base_url() . 'uploads/containerimages/' . $file_name;
            
                                if (move_uploaded_file($file_tmpname,  $target_dir . $file_name)) {
                                    $row_array['imageUrl'] = $fullImagePath;
                                    $row_array['uploadedStatus'] = true;
                                } else {
                                    $row_array['imageUrl'] = "";
                                    $row_array['uploadedStatus'] = false;
                                }
                            }
                            $row_array["generatedImageId"] = $arrayImageId[$imgArrayCount];
                            $row_array["containerNumber"] = $arrayContainerNumber[$imgArrayCount];
                            array_push($return_arr_response, $row_array);
            
                            $imgArrayCount++;
                        }

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $return_arr_response;
                        http_response_code(200);
                        $this->output($Return);

                    } else {
                        $Return["status"] = false;
                        $Return["message"] = "Unauthorized";
                        http_response_code(401);
                        $this->output($Return);
                    }
                } else {
                    $Return["status"] = false;
                    $Return["message"] = "Unauthorized";
                    http_response_code(401);
                    $this->output($Return);
                }
            } else {
                $Return["status"] = false;
                $Return["message"] = "Bad Header Details";
                http_response_code(400);
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
