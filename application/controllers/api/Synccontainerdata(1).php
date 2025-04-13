<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Synccontainerdata extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
        $this->load->model("Dispatch_model");
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

                $continer_arr_response = array();

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);
                    if ($checkUserExists == true) {

                        $requestdata = json_decode(file_get_contents("php://input"), true);

                        $containerData = $requestdata["containerData"];

                        if (count($containerData) > 0) {
                            foreach ($containerData as $key => $value) {

                                $continer_images_arr_response = array();

                                $row_container_array = array();

                                $tempContainerId = $value["tempContainerId"];
                                $containerNumber = $value["containerNumber"];
                                $containerId = $value["containerId"];
                                $warehouseId = $value["warehouseId"];
                                $shippingLine = $value["shippingLine"];
                                $productId = $value["productId"];
                                $productTypeId = $value["productTypeId"];
                                $dispatchDate = $value["dispatchDate"];
                                $sealNumber = $value["sealNumber"];
                                $category = $value["category"];
                                $createdBy = $value["createdBy"];
                                $createdDate = $value["createdDate"];
                                $isClosed = $value["isClosed"];
                                $closedBy = $value["closedBy"];
                                $closedDate = $value["closedDate"];
                                $isContainerAvailable = $value["isContainerAvailable"];
                                $isSpecialUploaded = $value["isSpecialUploaded"];
                                $originId = $value["originId"];
                                $totalGrossVolume = $value["totalGrossVolume"];
                                $totalNetVolume = $value["totalNetVolume"];
                                $totalPieces = $value["totalPieces"];
                                $existingContainerNumber = $value["existingContainerNumber"];
                                $containerImages = $value["containerImages"];

                                $closedStatus = 0;
                                $containerImageUrl = "";
                                if ($isClosed == true) {
                                    $closedStatus = 1;
                                }

                                $crdate = new DateTime("@$createdDate");
                                $createdDate = $crdate->format('Y-m-d H:i:s');

                                if($closedDate > 0) {
                                    $cdate = new DateTime("@$closedDate");
                                    $closedDate = $cdate->format('Y-m-d H:i:s');
                                }

                                if ($containerId > 0 && $containerNumber != null && $containerNumber != "") {

                                    $getDispatchDetailsById = $this->Dispatch_model->get_dispatch_details_by_id($containerId);
                                    if (count($getDispatchDetailsById) == 1) {

                                        $containerImageUrl = base_url() . "/viewcontainerimages?cid=" . $containerId;

                                        //UPDATE CONTAINER DETAILS
                                        $updateDataDispatch = array(
                                            "container_number" => $containerNumber, "warehouse_id" => $warehouseId,
                                            "shipping_line" => $shippingLine, "dispatch_date" => $dispatchDate,
                                            "updatedby" => $closedBy, "seal_number" => $sealNumber,
                                            "container_pic_url" => $containerImageUrl, "isclosed" => $closedStatus, "closedby" => $closedBy,
                                            "closeddate" => $closedDate, "iscontainer_available" => $isContainerAvailable,
                                            "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId,
                                            "total_gross_volume" => $totalGrossVolume, "total_volume" => $totalNetVolume,
                                            "total_pieces" => $totalPieces, "category" => $category, "captured_from_app" => 1,
                                        );

                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($containerId, $updateDataDispatch);

                                        if ($updateDispatch == true) {

                                            if(count($containerImages) > 0) {
                                                foreach($containerImages as $key => $containerimage) {
                                                    $imageUrl = $containerimage["imageUrl"];
                                                    $generatedImageId = $containerimage["generatedImageId"];

                                                    $insertDataContainerImages = array(
                                                        "dispatch_id" => $containerId, "image_url" => $imageUrl,
                                                        "created_by" => $createdBy, "updated_by" => $createdBy, "is_active" => 1,
                                                    );
            
                                                    $insertDispatchPhotos = $this->Dispatch_model->add_dispatch_photos($insertDataContainerImages);

                                                    if($insertDispatchPhotos > 0) {
                                                        $row_container_image_array["isUploaded"] = true;
                                                        $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                        array_push($continer_images_arr_response, $row_container_image_array);
                                                    } else {
                                                        $row_container_image_array["isUploaded"] = false;
                                                        $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                        array_push($continer_images_arr_response, $row_container_image_array);
                                                    }
                                                }
                                            }

                                            $row_container_array["containerImages"] = $continer_images_arr_response;
                                            $row_container_array["containerId"] = $containerId;
                                            $row_container_array["containerNumber"] = $containerNumber;
                                            $row_container_array["syncStatus"] = true;
                                            $row_container_array["tempContainerId"] = "";
                                        } else {
                                            $row_container_array["containerImages"] = $continer_images_arr_response;
                                            $row_container_array["containerId"] = $containerId;
                                            $row_container_array["containerNumber"] = $containerNumber;
                                            $row_container_array["syncStatus"] = false;
                                            $row_container_array["tempContainerId"] = "";
                                        }

                                        array_push($continer_arr_response, $row_container_array);
                                    } else if ($tempContainerId != null && $tempContainerId != "") {

                                        //INSERT CONTAINER DETAILS
                                        $insertDataDispatch = array(
                                            "container_number" => $containerNumber, "warehouse_id" => $warehouseId,
                                            "shipping_line" => $shippingLine, "product_id" => $productId,
                                            "product_type_id" => $productTypeId, "dispatch_date" => $dispatchDate,
                                            "seal_number" => $sealNumber,
                                            "createdby" => $createdBy, "updatedby" => $createdBy, "isactive" => 1,
                                            "isclosed" => $closedStatus, "closedby" => $closedBy, "closeddate" => $closedDate,
                                            "isexport" => 0, "exportedby" => 0, "dispatched_timestamp" => 0,
                                            "isduplicatedispatched" => 0, "temp_container_number" => "",
                                            "iscontainer_available" => 1, "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId,
                                            "total_volume" => $totalNetVolume, "total_gross_volume" => $totalGrossVolume,
                                            "total_pieces" => $totalPieces, "category" => $category, "captured_from_app" => 1
                                        );

                                        $insertDispatch = $this->Dispatch_model->add_dispatch($insertDataDispatch);

                                        if ($insertDispatch > 0) {

                                            $containerImageUrl = base_url() . "/viewcontainerimages?cid=" . $insertDispatch;

                                            $updateDataDispatch = array(
                                                "container_pic_url" => $containerImageUrl,
                                            );

                                            $updateDispatch = $this->Dispatch_model->update_dispatch_id($insertDispatch, $updateDataDispatch);

                                            if(count($containerImages) > 0) {
                                                foreach($containerImages as $key => $containerimage) {
                                                    $imageUrl = $containerimage["imageUrl"];
                                                    $generatedImageId = $containerimage["generatedImageId"];

                                                    $insertDataContainerImages = array(
                                                        "dispatch_id" => $containerId, "image_url" => $imageUrl,
                                                        "created_by" => $createdBy, "updated_by" => $createdBy, "is_active" => 1,
                                                    );
            
                                                    $insertDispatchPhotos = $this->Dispatch_model->add_dispatch_photos($insertDataContainerImages);

                                                    if($insertDispatchPhotos > 0) {
                                                        $row_container_image_array["isUploaded"] = true;
                                                        $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                        array_push($continer_images_arr_response, $row_container_image_array);
                                                    } else {
                                                        $row_container_image_array["isUploaded"] = false;
                                                        $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                        array_push($continer_images_arr_response, $row_container_image_array);
                                                    }
                                                }
                                            }

                                            $row_container_array["containerImages"] = $continer_images_arr_response;
                                            $row_container_array["containerId"] = $insertDispatch;
                                            $row_container_array["containerNumber"] = $containerNumber;
                                            $row_container_array["syncStatus"] = true;
                                            $row_container_array["tempContainerId"] = $tempContainerId;
                                        } else {
                                            $row_container_array["containerImages"] = $continer_images_arr_response;
                                            $row_container_array["containerId"] = 0;
                                            $row_container_array["containerNumber"] = $containerNumber;
                                            $row_container_array["syncStatus"] = false;
                                            $row_container_array["tempContainerId"] = $tempContainerId;
                                        }

                                        array_push($continer_arr_response, $row_container_array);
                                    }
                                } else if ($tempContainerId != null && $tempContainerId != "") {

                                    //INSERT CONTAINER DETAILS
                                    $insertDataDispatch = array(
                                        "container_number" => $containerNumber, "warehouse_id" => $warehouseId,
                                        "shipping_line" => $shippingLine, "product_id" => $productId,
                                        "product_type_id" => $productTypeId, "dispatch_date" => $dispatchDate,
                                        "seal_number" => $sealNumber, "container_pic_url" => $containerImageUrl,
                                        "createdby" => $createdBy, "updatedby" => $createdBy, "isactive" => 1,
                                        "isclosed" => $closedStatus, "closedby" => $closedBy, "closeddate" => $closedDate,
                                        "isexport" => 0, "exportedby" => 0, "dispatched_timestamp" => 0,
                                        "isduplicatedispatched" => 0, "temp_container_number" => "",
                                        "iscontainer_available" => 1, "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId,
                                        "total_volume" => $totalNetVolume, "total_gross_volume" => $totalGrossVolume,
                                        "total_pieces" => $totalPieces, "category" => $category, "captured_from_app" => 1
                                    );

                                    $insertDispatch = $this->Dispatch_model->add_dispatch($insertDataDispatch);

                                    if ($insertDispatch > 0) {

                                        $containerImageUrl = base_url() . "/viewcontainerimages?cid=" . $insertDispatch;

                                        $updateDataDispatch = array(
                                            "container_pic_url" => $containerImageUrl,
                                        );

                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($insertDispatch, $updateDataDispatch);

                                        if(count($containerImages) > 0) {
                                            foreach($containerImages as $key => $containerimage) {
                                                $imageUrl = $containerimage["imageUrl"];
                                                $generatedImageId = $containerimage["generatedImageId"];

                                                $insertDataContainerImages = array(
                                                    "dispatch_id" => $containerId, "image_url" => $imageUrl,
                                                    "created_by" => $createdBy, "updated_by" => $createdBy, "is_active" => 1,
                                                );
        
                                                $insertDispatchPhotos = $this->Dispatch_model->add_dispatch_photos($insertDataContainerImages);

                                                if($insertDispatchPhotos > 0) {
                                                    $row_container_image_array["isUploaded"] = true;
                                                    $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                    array_push($continer_images_arr_response, $row_container_image_array);
                                                } else {
                                                    $row_container_image_array["isUploaded"] = false;
                                                    $row_container_image_array["generatedImageId"] = $generatedImageId;
                                                    array_push($continer_images_arr_response, $row_container_image_array);
                                                }
                                            }
                                        }

                                        $row_container_array["containerImages"] = $continer_images_arr_response;
                                        $row_container_array["containerId"] = $insertDispatch;
                                        $row_container_array["containerNumber"] = $containerNumber;
                                        $row_container_array["syncStatus"] = true;
                                        $row_container_array["tempContainerId"] = $tempContainerId;
                                    } else {
                                        $row_container_array["containerImages"] = $continer_images_arr_response;
                                        $row_container_array["containerId"] = 0;
                                        $row_container_array["containerNumber"] = $containerNumber;
                                        $row_container_array["syncStatus"] = false;
                                        $row_container_array["tempContainerId"] = $tempContainerId;
                                    }

                                    array_push($continer_arr_response, $row_container_array);
                                }
                            }
                        }

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $continer_arr_response;
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
            $Return["message"] = $e->getMessage();
            http_response_code(500);
            $this->output($Return);
        }
    }
}