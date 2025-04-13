<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Syncreceptiondata extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
        $this->load->model("Dispatch_model");
        $this->load->model("Reception_model");
        $this->load->model("Farm_model");
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

                $reception_arr_response = array();

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);
                    if ($checkUserExists == true) {

                        $requestdata = json_decode(file_get_contents("php://input"), true);

                        $receptionData = $requestdata["receptionData"];

                        if (count($receptionData) > 0) {
                            foreach ($receptionData as $key => $value) {

                                $row_reception_array = array();

                                $tempReceptionId = $value["tempReceptionId"];
                                $inventoryOrder = $value["inventoryOrder"];
                                $receptionMainId = $value["receptionId"];
                                $warehouseId = $value["warehouseId"];
                                $supplierId = $value["supplierId"];
                                $productId = $value["productId"];
                                $productTypeId = $value["productTypeId"];
                                $supplierProductId = $value["supplierProductId"];
                                $supplierProductTypeId = $value["supplierProductTypeId"];
                                $measurementsystemId = $value["measurementsystemId"];
                                $receivedDate = $value["receivedDate"];
                                $createdBy = $value["createdBy"];
                                $createdDate = $value["createdDate"];
                                $isClosed = $value["isClosed"];
                                $closedBy = $value["closedBy"];
                                $closedDate = $value["closedDate"];
                                $isSpecialUploaded = $value["isSpecialUploaded"];
                                $originId = $value["originId"];
                                $totalGrossVolume = $value["totalGrossVolume"];
                                $totalVolume = $value["totalVolume"];
                                $totalPieces = $value["totalPieces"];
                                $isCreateFarm = $value["isCreateFarm"];
                                $contractId = $value["contractId"];
                                $truckPlateNumber = $value["truckPlateNumber"];
                                
                                $logisticCost = $value["logisticCost"];
                                $logisticPayTo = $value["logisticPayTo"];
                                
                                $receptionCaptured = $value["receptionCapturedData"];
                                $receptionDispatchMappingData = $value["receptionDispatchMappingData"];

                                $closedStatus = 0;
                                if ($isClosed == true) {
                                    $closedStatus = 1;
                                }

                                $crdate = new DateTime("@$createdDate");
                                $createdDate = $crdate->format('Y-m-d H:i:s');

                                if ($closedDate > 0) {
                                    $cdate = new DateTime("@$closedDate");
                                    $closedDate = $cdate->format('Y-m-d H:i:s');
                                }

                                $reception_data_arr_response = array();
                                $container_data_arr_response = array();
                                $reception_container_mapping_data_arr_response = array();
                                if ($receptionMainId > 0 && $inventoryOrder != null && $inventoryOrder != "") {

                                    $getReceptionDetailsById = $this->Reception_model->get_reception_detail_by_id_app($receptionMainId);

                                    if (count($getReceptionDetailsById) == 1) {

                                        //UPDATE CONTAINER DETAILS
                                        $updateDataReception = array(
                                            "warehouse_id" => $warehouseId, "supplier_id" => $supplierId,
                                            "supplier_code" => $getReceptionDetailsById[0]->supplier_code,
                                            "supplier_product_id" => $supplierProductId, "supplier_product_typeid" => $supplierProductTypeId,
                                            "measurementsystem_id" => $measurementsystemId, "received_date" => $receivedDate,
                                            "salvoconducto" => $inventoryOrder, "createdby" => $createdBy, "updatedby" => $createdBy,
                                            "isactive" => 1, "isclosed" => $closedStatus, "closedby" => $closedBy, "closeddate" => $closedDate,
                                            "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId, "total_gross_volume" => $totalGrossVolume,
                                            "total_volume" => $totalVolume,
                                            "total_pieces" => $totalPieces, "captured_from_app" => 1, "is_create_farm" => $isCreateFarm,
                                            "contract_id" => $contractId, "truck_plate_number" => $truckPlateNumber, 
                                            "logistic_cost" => $logisticCost, "logistic_pay_to" => $logisticPayTo,
                                        );

                                        $updateReception = $this->Reception_model->update_reception_app($receptionMainId, $updateDataReception);

                                        if ($updateReception == true) {

                                            if (count($receptionCaptured) > 0) {

                                                foreach ($receptionCaptured as $key => $capturevalue) {

                                                    $container_data_arr_response = array();

                                                    $pieces = $capturevalue["pieces"];
                                                    $lengthBought = $capturevalue["lengthBought"];
                                                    $circumferenceBought = $capturevalue["circumferenceBought"];
                                                    $grossVolume = $capturevalue["grossVolume"];
                                                    $netVolume = $capturevalue["netVolume"];
                                                    $receptionDataId = $capturevalue["receptionDataId"];
                                                    $isDeleted = $capturevalue["isDeleted"];
                                                    $containerNumber = $capturevalue["containerNumber"];
                                                    $containerId = $capturevalue["containerId"];
                                                    $receptionId = $capturevalue["receptionId"];

                                                    if ($receptionId > 0 && $receptionDataId > 0) {

                                                        $isActive = 1;
                                                        if ($isDeleted == true) {
                                                            $isActive = 0;
                                                        }

                                                        $updateDataCaputureReception = array(
                                                            "scanned_code" => $pieces, "length_bought" => $lengthBought,
                                                            "circumference_bought" => $circumferenceBought, "cbm_bought" => $grossVolume,
                                                            "cbm_export" => $netVolume, "updatedby" => $userid, "isactive" => $isActive,
                                                        );

                                                        $updateReceptionData = $this->Reception_model->update_reception_data_dataid_user($receptionId, $receptionDataId, $updateDataCaputureReception, $userid);

                                                        if ($updateReceptionData == true) {

                                                            if ($containerId > 0) {
                                                                //UPDATE CONTAINER DATA
                                                                $updateDataCaputureDispatch = array(
                                                                    "cbm_bought" => $grossVolume, "cbm_export" => $netVolume, "updatedby" => $userid,
                                                                    "scanned_timestamp" => 0, "isduplicatescanned" => 0, "isactive" => $isActive, "dispatch_pieces" => $pieces
                                                                );

                                                                $updateDispatchCapure = $this->Dispatch_model->update_dispatch_data_by_id_user(
                                                                    $receptionDataId,
                                                                    $receptionId,
                                                                    $containerId,
                                                                    $updateDataCaputureDispatch,
                                                                    $userid
                                                                );

                                                                if ($updateDispatchCapure == true) {
                                                                    $row_container_data_array["receptionId"] = $receptionId;
                                                                    $row_container_data_array["receptionDataId"] = $receptionDataId;
                                                                    array_push($container_data_arr_response, $row_container_data_array);
                                                                } else {
                                                                    $row_container_data_array["receptionId"] = $receptionId;
                                                                    $row_container_data_array["receptionDataId"] = $receptionDataId;
                                                                    array_push($container_data_arr_response, $row_container_data_array);
                                                                }
                                                            } else {

                                                                //INSERT CONTAINER DATA
                                                                $insertDataCaputureDispatch = array(
                                                                    "dispatch_id" => $containerId, "reception_data_id" => $receptionDataId, "reception_id" => $receptionId,
                                                                    "cbm_bought" => $grossVolume, "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid,
                                                                    "scanned_timestamp" => 0, "isduplicatescanned" => 0, "isactive" => $isActive,
                                                                    "is_special" => 1, "dispatch_pieces" => $pieces
                                                                );

                                                                $insertDispatchCapure = $this->Dispatch_model->add_dispatch_data_single($insertDataCaputureDispatch);

                                                                if ($insertDispatchCapure > 0) {



                                                                    $row_container_data_array["receptionId"] = $receptionId;
                                                                    $row_container_data_array["receptionDataId"] = $insertDispatchCapure;
                                                                    $row_container_data_array["syncStatus"] = true;
                                                                    array_push($container_data_arr_response, $row_container_data_array);
                                                                } else {
                                                                    $row_container_data_array["receptionId"] = $receptionId;
                                                                    $row_container_data_array["receptionDataId"] = 0;
                                                                    $row_container_data_array["syncStatus"] = false;
                                                                    array_push($container_data_arr_response, $row_container_data_array);
                                                                }
                                                            }

                                                            $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                            $row_reception_data_array["receptionId"] = $receptionId;
                                                            $row_reception_data_array["receptionDataId"] = $insertDispatchCapure;
                                                            $row_reception_data_array["syncStatus"] = true;
                                                            $row_reception_data_array["tempReceptionDataId"] = "";
                                                            $row_reception_data_array["tempReceptionId"] = "";



                                                            array_push($reception_data_arr_response, $row_reception_data_array);
                                                        } else {
                                                            $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                            $row_reception_data_array["receptionId"] = $receptionId;
                                                            $row_reception_data_array["receptionDataId"] = 0;
                                                            $row_reception_data_array["syncStatus"] = false;
                                                            $row_reception_data_array["tempReceptionDataId"] = "";
                                                            $row_reception_data_array["tempReceptionId"] = "";
                                                            array_push($reception_data_arr_response, $row_reception_data_array);
                                                        }
                                                    } else {

                                                        $insertDataCaputureReception = array(
                                                            "reception_id" => $receptionId, "salvoconducto" => $inventoryOrder,
                                                            "scanned_code" => $pieces, "length_bought" => $lengthBought,
                                                            "circumference_bought" => $circumferenceBought, "cbm_bought" => $grossVolume,
                                                            "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid, "isactive" => 1,
                                                            "isdispatch" => 1, "scanned_timestamp" => 0, "isduplicatescanned" => 0, "dispatch_date" => $receivedDate,
                                                            "container_number" => $containerNumber, "is_special" => 1, "remaining_stock_count" => 0
                                                        );

                                                        $insertReceptionData = $this->Reception_model->add_reception_data_single($insertDataCaputureReception);

                                                        if ($insertReceptionData > 0) {

                                                            $insertDataCaputureDispatch = array(
                                                                "dispatch_id" => $containerId, "reception_data_id" => $insertReceptionData, "reception_id" => $receptionId,
                                                                "cbm_bought" => $grossVolume, "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid,
                                                                "scanned_timestamp" => 0, "isduplicatescanned" => 0, "isactive" => 1,
                                                                "is_special" => 1, "dispatch_pieces" => $pieces
                                                            );

                                                            $insertDispatchCapure = $this->Dispatch_model->add_dispatch_data_single($insertDataCaputureDispatch);

                                                            if ($insertDispatchCapure > 0) {

                                                                $row_container_data_array["receptionId"] = $receptionId;
                                                                $row_container_data_array["receptionDataId"] = $insertDispatchCapure;
                                                                array_push($container_data_arr_response, $row_container_data_array);
                                                            } else {
                                                                $row_container_data_array["receptionId"] = $receptionId;
                                                                $row_container_data_array["receptionDataId"] = 0;
                                                                array_push($container_data_arr_response, $row_container_data_array);
                                                            }

                                                            $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                            $row_reception_data_array["receptionId"] = $receptionId;
                                                            $row_reception_data_array["receptionDataId"] = $insertReceptionData;
                                                            $row_reception_data_array["syncStatus"] = true;
                                                            $row_reception_data_array["tempReceptionDataId"] = "";
                                                            $row_reception_data_array["tempReceptionId"] = "";
                                                            $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                            $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                            $row_reception_data_array["containerNumber"] = $containerNumber;
                                                            array_push($reception_data_arr_response, $row_reception_data_array);
                                                        } else {
                                                            $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                            $row_reception_data_array["receptionId"] = $receptionId;
                                                            $row_reception_data_array["receptionDataId"] = 0;
                                                            $row_reception_data_array["syncStatus"] = false;
                                                            $row_reception_data_array["tempReceptionDataId"] = "";
                                                            $row_reception_data_array["tempReceptionId"] = "";
                                                            $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                            $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                            $row_reception_data_array["containerNumber"] = $containerNumber;
                                                            array_push($reception_data_arr_response, $row_reception_data_array);
                                                        }
                                                    }

                                                    $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app($containerId);
                                                    if (count($getTotalDispatchData) == 1) {
                                                        $updateDataDispatch = array(
                                                            "total_gross_volume" => $getTotalDispatchData[0]->gross_volume, "total_volume" => $getTotalDispatchData[0]->net_volume, "total_pieces" => $getTotalDispatchData[0]->total_pieces,
                                                        );

                                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($containerId, $updateDataDispatch);
                                                    }
                                                }
                                            } else {

                                                $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($receptionMainId, $inventoryOrder);
                                                if (count($getTotalReceptionData) == 1) {
                                                    $updateDataReception = array(
                                                        "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                                    );

                                                    $updateReception = $this->Reception_model->update_reception_app($receptionMainId, $updateDataReception);
                                                }

                                                $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app_open();
                                                if (count($getTotalDispatchData) > 0) {
                                                    foreach ($getTotalDispatchData as $totaldispatchdata) {
                                                        $dId = $totaldispatchdata->dispatch_id;
                                                        $updateDataDispatch = array(
                                                            "total_gross_volume" => $totaldispatchdata->total_gross_volume, "total_volume" => $totaldispatchdata->total_volume, "total_pieces" => $totaldispatchdata->total_pieces,
                                                        );

                                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($dId, $updateDataDispatch);
                                                    }
                                                }
                                            }

                                            if (count($receptionDispatchMappingData) > 0) {

                                                $reception_container_mapping_data_arr_response = array();

                                                foreach ($receptionDispatchMappingData as $key => $mappingvalue) {

                                                    $tempReceptionIdMapping = $mappingvalue["tempReceptionId"];
                                                    $inventoryOrderMapping = $mappingvalue["inventoryOrder"];
                                                    $containerNumberMapping = $mappingvalue["containerNumber"];
                                                    $receptionIdReq = $mappingvalue["receptionId"];
                                                    $containerIdReq = $mappingvalue["containerId"];

                                                    if ($containerIdReq > 0 && $receptionIdReq > 0) {

                                                        $getReceptionMappingData = $this->Reception_model->get_reception_dispatch_mapping_containerid($receptionIdReq, $containerIdReq);

                                                        if (count($getReceptionMappingData) == 0) {
                                                            $insertReceptionMappingData = array(
                                                                "reception_id" => $receptionIdReq, "dispatch_id" => $containerIdReq,
                                                                "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                            );

                                                            $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                            if ($insertReceptionMappingData > 0) {
                                                                $row_container_mapping_data_array["receptionId"] = $receptionIdReq;
                                                                $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                                $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                                $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                                $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                                array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                            }
                                                        }
                                                    } else {
                                                        $insertReceptionMappingData = array(
                                                            "reception_id" => $receptionId, "dispatch_id" => $containerIdReq,
                                                            "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                        );

                                                        $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                        if ($insertReceptionMappingData > 0) {
                                                            $row_container_mapping_data_array["receptionId"] = $receptionId;
                                                            $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                            $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                            $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                            $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                            array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                        }
                                                    }
                                                }
                                            }

                                            //UPDATE TOTAL PIECES AND VOLUME
                                            $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($receptionMainId, $inventoryOrder);
                                            if (count($getTotalReceptionData) == 1) {
                                                $updateDataReception = array(
                                                    "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                                );

                                                $updateReception = $this->Reception_model->update_reception_app($receptionMainId, $updateDataReception);
                                            }

                                            if ($isClosed == true) {
                                                if ($isCreateFarm == true) {
                                                    $this->save_farm_from_reception($receptionMainId, $inventoryOrder);
                                                }
                                            }

                                            $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                            $row_reception_array["receptionData"] = $reception_data_arr_response;
                                            $row_reception_array["receptionId"] = $receptionMainId;
                                            $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                            $row_reception_array["syncStatus"] = true;
                                            $row_reception_array["tempReceptionId"] = "";
                                        } else {
                                            $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                            $row_reception_array["receptionData"] = $reception_data_arr_response;
                                            $row_reception_array["receptionId"] = $receptionMainId;
                                            $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                            $row_reception_array["syncStatus"] = false;
                                            $row_reception_array["tempReceptionId"] = "";
                                        }

                                        array_push($reception_arr_response, $row_reception_array);
                                    } else if ($tempReceptionId != null && $tempReceptionId != "") {

                                        $getSupplierDetailById = $this->Master_model->get_supplier_detail_by_id($supplierId);

                                        //INSERT CONTAINER DETAILS
                                        $insertDataReception = array(
                                            "warehouse_id" => $warehouseId, "supplier_id" => $supplierId,
                                            "supplier_code" => $getSupplierDetailById[0]->supplier_code, "supplier_product_id" => $supplierProductId,
                                            "supplier_product_typeid" => $supplierProductTypeId, "measurementsystem_id" => $measurementsystemId,
                                            "received_date" => $receivedDate, "salvoconducto" => $inventoryOrder,
                                            "createdby" => $userid, "updatedby" => $userid, "isactive" => 1,
                                            "isclosed" => $closedStatus, "closedby" => $closedBy, "closeddate" => $closedDate, "captured_timestamp" => 0,
                                            "isduplicatecaptured" => 0, "is_contract_added" => 0, "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId,
                                            "total_gross_volume" => $totalGrossVolume, "total_volume" => $totalVolume, "total_pieces" => $totalPieces, "captured_from_app" => 1, "is_create_farm" => $isCreateFarm,
                                            "contract_id" => $contractId, "truck_plate_number" => $truckPlateNumber, 
                                            "logistic_cost" => $logisticCost, "logistic_pay_to" => $logisticPayTo,
                                        );

                                        $insertReception = $this->Reception_model->add_reception($insertDataReception);

                                        if ($insertReception > 0) {

                                            if (count($receptionCaptured) > 0) {

                                                foreach ($receptionCaptured as $key => $capturevalue) {

                                                    $container_data_arr_response = array();

                                                    $pieces = $capturevalue["pieces"];
                                                    $lengthBought = $capturevalue["lengthBought"];
                                                    $circumferenceBought = $capturevalue["circumferenceBought"];
                                                    $grossVolume = $capturevalue["grossVolume"];
                                                    $netVolume = $capturevalue["netVolume"];
                                                    $receptionDataId = $capturevalue["receptionDataId"];
                                                    $isDeleted = $capturevalue["isDeleted"];
                                                    $containerNumber = $capturevalue["containerNumber"];
                                                    $containerId = $capturevalue["containerId"];
                                                    $receptionId = $capturevalue["receptionId"];

                                                    $isActive = 1;
                                                    if ($isDeleted == true) {
                                                        $isActive = 0;
                                                    }

                                                    $insertDataCaputureReception = array(
                                                        "reception_id" => $insertReception, "salvoconducto" => $inventoryOrder,
                                                        "scanned_code" => $pieces, "length_bought" => $lengthBought,
                                                        "circumference_bought" => $circumferenceBought, "cbm_bought" => $grossVolume,
                                                        "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid, "isactive" => $isActive,
                                                        "isdispatch" => 1, "scanned_timestamp" => 0, "isduplicatescanned" => 0, "dispatch_date" => $receivedDate,
                                                        "container_number" => $containerNumber, "is_special" => 1, "remaining_stock_count" => 0
                                                    );

                                                    $insertReceptionData = $this->Reception_model->add_reception_data_single($insertDataCaputureReception);

                                                    if ($insertReceptionData > 0) {

                                                        $insertDataCaputureDispatch = array(
                                                            "dispatch_id" => $containerId, "reception_data_id" => $insertReceptionData, "reception_id" => $receptionId,
                                                            "cbm_bought" => $grossVolume, "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid,
                                                            "scanned_timestamp" => 0, "isduplicatescanned" => 0, "isactive" => $isActive,
                                                            "is_special" => 1, "dispatch_pieces" => $pieces
                                                        );

                                                        $insertDispatchCapure = $this->Dispatch_model->add_dispatch_data_single($insertDataCaputureDispatch);

                                                        if ($insertDispatchCapure > 0) {

                                                            $row_container_data_array["receptionId"] = $receptionId;
                                                            $row_container_data_array["receptionDataId"] = $insertDispatchCapure;
                                                            $row_container_data_array["syncStatus"] = true;
                                                            array_push($container_data_arr_response, $row_container_data_array);
                                                        } else {
                                                            $row_container_data_array["receptionId"] = $receptionId;
                                                            $row_container_data_array["receptionDataId"] = 0;
                                                            $row_container_data_array["syncStatus"] = false;
                                                            array_push($container_data_arr_response, $row_container_data_array);
                                                        }

                                                        $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                        $row_reception_data_array["receptionId"] = $receptionId;
                                                        $row_reception_data_array["receptionDataId"] = $insertReceptionData;
                                                        $row_reception_data_array["syncStatus"] = true;
                                                        $row_reception_data_array["tempReceptionDataId"] = "";
                                                        $row_reception_data_array["tempReceptionId"] = $tempReceptionId;
                                                        $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                        $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                        $row_reception_data_array["containerNumber"] = $containerNumber;
                                                        array_push($reception_data_arr_response, $row_reception_data_array);
                                                    } else {
                                                        $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                        $row_reception_data_array["receptionId"] = $receptionId;
                                                        $row_reception_data_array["receptionDataId"] = 0;
                                                        $row_reception_data_array["syncStatus"] = false;
                                                        $row_reception_data_array["tempReceptionDataId"] = "";
                                                        $row_reception_data_array["tempReceptionId"] = $tempReceptionId;
                                                        $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                        $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                        $row_reception_data_array["containerNumber"] = $containerNumber;
                                                        array_push($reception_data_arr_response, $row_reception_data_array);
                                                    }

                                                    $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app($containerId);
                                                    if (count($getTotalDispatchData) == 1) {
                                                        $updateDataDispatch = array(
                                                            "total_gross_volume" => $getTotalDispatchData[0]->gross_volume, "total_volume" => $getTotalDispatchData[0]->net_volume, "total_pieces" => $getTotalDispatchData[0]->total_pieces,
                                                        );

                                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($containerId, $updateDataDispatch);
                                                    }
                                                }
                                            } else {

                                                $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($receptionMainId, $inventoryOrder);
                                                if (count($getTotalReceptionData) == 1) {
                                                    $updateDataReception = array(
                                                        "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                                    );

                                                    $updateReception = $this->Reception_model->update_reception_app($receptionMainId, $updateDataReception);
                                                }

                                                $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app_open();
                                                if (count($getTotalDispatchData) > 0) {
                                                    foreach ($getTotalDispatchData as $totaldispatchdata) {
                                                        $dId = $totaldispatchdata->dispatch_id;
                                                        $updateDataDispatch = array(
                                                            "total_gross_volume" => $totaldispatchdata->total_gross_volume, "total_volume" => $totaldispatchdata->total_volume, "total_pieces" => $totaldispatchdata->total_pieces,
                                                        );

                                                        $updateDispatch = $this->Dispatch_model->update_dispatch_id($dId, $updateDataDispatch);
                                                    }
                                                }
                                            }

                                            if (count($receptionDispatchMappingData) > 0) {

                                                $reception_container_mapping_data_arr_response = array();

                                                foreach ($receptionDispatchMappingData as $key => $mappingvalue) {

                                                    $tempReceptionIdMapping = $mappingvalue["tempReceptionId"];
                                                    $inventoryOrderMapping = $mappingvalue["inventoryOrder"];
                                                    $containerNumberMapping = $mappingvalue["containerNumber"];
                                                    $receptionIdReq = $mappingvalue["receptionId"];
                                                    $containerIdReq = $mappingvalue["containerId"];

                                                    if ($containerIdReq > 0 && $receptionIdReq > 0) {

                                                        $getReceptionMappingData = $this->Reception_model->get_reception_dispatch_mapping_containerid($receptionIdReq, $containerIdReq);

                                                        if (count($getReceptionMappingData) == 0) {
                                                            $insertReceptionMappingData = array(
                                                                "reception_id" => $receptionIdReq, "dispatch_id" => $containerIdReq,
                                                                "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                            );

                                                            $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                            if ($insertReceptionMappingData > 0) {
                                                                $row_container_mapping_data_array["receptionId"] = $receptionIdReq;
                                                                $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                                $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                                $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                                $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                                array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                            }
                                                        }
                                                    } else {
                                                        $insertReceptionMappingData = array(
                                                            "reception_id" => $insertReception, "dispatch_id" => $containerIdReq,
                                                            "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                        );

                                                        $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                        if ($insertReceptionMappingData > 0) {
                                                            $row_container_mapping_data_array["receptionId"] = $insertReception;
                                                            $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                            $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                            $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                            $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                            array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                        }
                                                    }
                                                }
                                            }

                                            //UPDATE TOTAL PIECES AND VOLUME
                                            $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($insertReception, $inventoryOrder);
                                            if (count($getTotalReceptionData) == 1) {
                                                $updateDataReception = array(
                                                    "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                                );

                                                $updateReception = $this->Reception_model->update_reception_app($insertReception, $updateDataReception);
                                            }

                                            if ($isClosed == true) {
                                                if ($isCreateFarm == true) {
                                                    $this->save_farm_from_reception($insertReception, $inventoryOrder);
                                                }
                                            }

                                            $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                            $row_reception_array["receptionData"] = $reception_data_arr_response;
                                            $row_reception_array["receptionId"] = $insertReception;
                                            $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                            $row_reception_array["syncStatus"] = true;
                                            $row_reception_array["tempReceptionId"] = $tempReceptionId;
                                        } else {
                                            $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                            $row_reception_array["receptionData"] = $reception_data_arr_response;
                                            $row_reception_array["receptionId"] = 0;
                                            $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                            $row_reception_array["syncStatus"] = false;
                                            $row_reception_array["tempReceptionId"] = $tempReceptionId;
                                        }

                                        array_push($reception_arr_response, $row_reception_array);
                                    }
                                } else if ($tempReceptionId != null && $tempReceptionId != "") {


                                    $getSupplierDetailById = $this->Master_model->get_supplier_detail_by_id($supplierId);

                                    //INSERT CONTAINER DETAILS
                                    $insertDataReception = array(
                                        "warehouse_id" => $warehouseId, "supplier_id" => $supplierId,
                                        "supplier_code" => $getSupplierDetailById[0]->supplier_code, "supplier_product_id" => $supplierProductId,
                                        "supplier_product_typeid" => $supplierProductTypeId, "measurementsystem_id" => $measurementsystemId,
                                        "received_date" => $receivedDate, "salvoconducto" => $inventoryOrder,
                                        "createdby" => $userid, "updatedby" => $userid, "isactive" => 1,
                                        "isclosed" => $closedStatus, "closedby" => $closedBy, "closeddate" => $closedDate, "captured_timestamp" => 0,
                                        "isduplicatecaptured" => 0, "is_contract_added" => 0, "is_special_uploaded" => $isSpecialUploaded, "origin_id" => $originId,
                                        "total_gross_volume" => $totalGrossVolume, "total_volume" => $totalVolume, "total_pieces" => $totalPieces, "captured_from_app" => 1, "is_create_farm" => $isCreateFarm,
                                        "contract_id" => $contractId, "truck_plate_number" => $truckPlateNumber,
                                        "logistic_cost" => $logisticCost, "logistic_pay_to" => $logisticPayTo,
                                    );

                                    $insertReception = $this->Reception_model->add_reception($insertDataReception);

                                    if ($insertReception > 0) {

                                        if (count($receptionCaptured) > 0) {

                                            foreach ($receptionCaptured as $key => $capturevalue) {

                                                $container_data_arr_response = array();

                                                $pieces = $capturevalue["pieces"];
                                                $lengthBought = $capturevalue["lengthBought"];
                                                $circumferenceBought = $capturevalue["circumferenceBought"];
                                                $grossVolume = $capturevalue["grossVolume"];
                                                $netVolume = $capturevalue["netVolume"];
                                                $receptionDataId = $capturevalue["receptionDataId"];
                                                $isDeleted = $capturevalue["isDeleted"];
                                                $containerNumber = $capturevalue["containerNumber"];
                                                $containerId = $capturevalue["containerId"];
                                                $receptionId = $capturevalue["receptionId"];

                                                $isActive = 1;
                                                if ($isDeleted == true) {
                                                    $isActive = 0;
                                                }

                                                $insertDataCaputureReception = array(
                                                    "reception_id" => $insertReception, "salvoconducto" => $inventoryOrder,
                                                    "scanned_code" => $pieces, "length_bought" => $lengthBought,
                                                    "circumference_bought" => $circumferenceBought, "cbm_bought" => $grossVolume,
                                                    "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid, "isactive" => $isActive,
                                                    "isdispatch" => 1, "scanned_timestamp" => 0, "isduplicatescanned" => 0, "dispatch_date" => $receivedDate,
                                                    "container_number" => $containerNumber, "is_special" => 1, "remaining_stock_count" => 0
                                                );

                                                $insertReceptionData = $this->Reception_model->add_reception_data_single($insertDataCaputureReception);

                                                if ($insertReceptionData > 0) {

                                                    $insertDataCaputureDispatch = array(
                                                        "dispatch_id" => $containerId, "reception_data_id" => $insertReceptionData, "reception_id" => $insertReception,
                                                        "cbm_bought" => $grossVolume, "cbm_export" => $netVolume, "createdby" => $userid, "updatedby" => $userid,
                                                        "scanned_timestamp" => 0, "isduplicatescanned" => 0, "isactive" => $isActive,
                                                        "is_special" => 1, "dispatch_pieces" => $pieces
                                                    );

                                                    $insertDispatchCapure = $this->Dispatch_model->add_dispatch_data_single($insertDataCaputureDispatch);

                                                    if ($insertDispatchCapure > 0) {



                                                        $row_container_data_array["receptionId"] = $insertReception;
                                                        $row_container_data_array["receptionDataId"] = $insertDispatchCapure;
                                                        $row_container_data_array["syncStatus"] = true;
                                                        array_push($container_data_arr_response, $row_container_data_array);
                                                    } else {
                                                        $row_container_data_array["receptionId"] = $insertReception;
                                                        $row_container_data_array["receptionDataId"] = 0;
                                                        $row_container_data_array["syncStatus"] = false;
                                                        array_push($container_data_arr_response, $row_container_data_array);
                                                    }

                                                    $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                    $row_reception_data_array["receptionId"] = $insertReception;
                                                    $row_reception_data_array["receptionDataId"] = $insertReceptionData;
                                                    $row_reception_data_array["syncStatus"] = true;
                                                    $row_reception_data_array["tempReceptionDataId"] = "";
                                                    $row_reception_data_array["tempReceptionId"] = $tempReceptionId;
                                                    $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                    $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                    $row_reception_data_array["containerNumber"] = $containerNumber;
                                                    array_push($reception_data_arr_response, $row_reception_data_array);
                                                } else {
                                                    $row_reception_data_array["containerData"] = $container_data_arr_response;
                                                    $row_reception_data_array["receptionId"] = $insertReception;
                                                    $row_reception_data_array["receptionDataId"] = 0;
                                                    $row_reception_data_array["syncStatus"] = false;
                                                    $row_reception_data_array["tempReceptionDataId"] = "";
                                                    $row_reception_data_array["tempReceptionId"] = $tempReceptionId;
                                                    $row_reception_data_array["circumferenceBought"] = $circumferenceBought + 0;
                                                    $row_reception_data_array["lengthBought"] = $lengthBought + 0;
                                                    $row_reception_data_array["containerNumber"] = $containerNumber;
                                                    array_push($reception_data_arr_response, $row_reception_data_array);
                                                }

                                                $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app($containerId);
                                                if (count($getTotalDispatchData) == 1) {
                                                    $updateDataDispatch = array(
                                                        "total_gross_volume" => $getTotalDispatchData[0]->gross_volume, "total_volume" => $getTotalDispatchData[0]->net_volume, "total_pieces" => $getTotalDispatchData[0]->total_pieces,
                                                    );

                                                    $updateDispatch = $this->Dispatch_model->update_dispatch_id($containerId, $updateDataDispatch);
                                                }
                                            }
                                        } else {

                                            $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($insertReception, $inventoryOrder);
                                                if (count($getTotalReceptionData) == 1) {
                                                    $updateDataReception = array(
                                                        "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                                    );

                                                    $updateReception = $this->Reception_model->update_reception_app($insertReception, $updateDataReception);
                                                }
                                                
                                            $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app_open();
                                            if (count($getTotalDispatchData) > 0) {
                                                foreach ($getTotalDispatchData as $totaldispatchdata) {
                                                    $dId = $totaldispatchdata->dispatch_id;
                                                    $updateDataDispatch = array(
                                                        "total_gross_volume" => $totaldispatchdata->total_gross_volume, "total_volume" => $totaldispatchdata->total_volume, "total_pieces" => $totaldispatchdata->total_pieces,
                                                    );

                                                    $updateDispatch = $this->Dispatch_model->update_dispatch_id($dId, $updateDataDispatch);
                                                }
                                            }
                                        }

                                        if (count($receptionDispatchMappingData) > 0) {

                                            $reception_container_mapping_data_arr_response = array();

                                            foreach ($receptionDispatchMappingData as $key => $mappingvalue) {

                                                $tempReceptionIdMapping = $mappingvalue["tempReceptionId"];
                                                $inventoryOrderMapping = $mappingvalue["inventoryOrder"];
                                                $containerNumberMapping = $mappingvalue["containerNumber"];
                                                $receptionIdReq = $mappingvalue["receptionId"];
                                                $containerIdReq = $mappingvalue["containerId"];

                                                if ($containerIdReq > 0 && $receptionIdReq > 0) {

                                                    $getReceptionMappingData = $this->Reception_model->get_reception_dispatch_mapping_containerid($receptionIdReq, $containerIdReq);

                                                    if (count($getReceptionMappingData) == 0) {
                                                        $insertReceptionMappingData = array(
                                                            "reception_id" => $receptionIdReq, "dispatch_id" => $containerIdReq,
                                                            "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                        );

                                                        $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                        if ($insertReceptionMappingData > 0) {
                                                            $row_container_mapping_data_array["receptionId"] = $receptionIdReq;
                                                            $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                            $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                            $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                            $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                            array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                        }
                                                    }
                                                } else {
                                                    $insertReceptionMappingData = array(
                                                        "reception_id" => $insertReception, "dispatch_id" => $containerIdReq,
                                                        "created_by" => $userid, "updated_by" => $userid, "is_active" => 1,
                                                    );

                                                    $insertReceptionMappingData = $this->Reception_model->add_reception_mapping_data($insertReceptionMappingData);

                                                    if ($insertReceptionMappingData > 0) {
                                                        $row_container_mapping_data_array["receptionId"] = $insertReception;
                                                        $row_container_mapping_data_array["containerId"] = $containerIdReq;
                                                        $row_container_mapping_data_array["inventoryOrder"] = $inventoryOrderMapping;
                                                        $row_container_mapping_data_array["containerNumber"] = $containerNumberMapping;
                                                        $row_container_mapping_data_array["tempReceptionId"] = $tempReceptionIdMapping;
                                                        array_push($reception_container_mapping_data_arr_response, $row_container_mapping_data_array);
                                                    }
                                                }
                                            }
                                        }

                                        //UPDATE TOTAL PIECES AND VOLUME
                                        $getTotalReceptionData = $this->Reception_model->get_total_reception_data_details_app($insertReception, $inventoryOrder);
                                        if (count($getTotalReceptionData) == 1) {
                                            $updateDataReception = array(
                                                "total_gross_volume" => $getTotalReceptionData[0]->gross_volume, "total_volume" => $getTotalReceptionData[0]->net_volume, "total_pieces" => $getTotalReceptionData[0]->totalpieces,
                                            );

                                            $updateReception = $this->Reception_model->update_reception_app($insertReception, $updateDataReception);
                                        }

                                        if ($isClosed == true) {
                                            if ($isCreateFarm == true) {
                                                $this->save_farm_from_reception($insertReception, $inventoryOrder);
                                            }
                                        }

                                        $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                        $row_reception_array["receptionData"] = $reception_data_arr_response;
                                        $row_reception_array["receptionId"] = $insertReception;
                                        $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                        $row_reception_array["syncStatus"] = true;
                                        $row_reception_array["tempReceptionId"] = $tempReceptionId;
                                    } else {
                                        $row_reception_array["containerMapping"] = $reception_container_mapping_data_arr_response;
                                        $row_reception_array["receptionData"] = $reception_data_arr_response;
                                        $row_reception_array["receptionId"] = 0;
                                        $row_reception_array["inventoryOrder"] = $inventoryOrder;
                                        $row_reception_array["syncStatus"] = false;
                                        $row_reception_array["tempReceptionId"] = $tempReceptionId;
                                    }

                                    array_push($reception_arr_response, $row_reception_array);
                                }
                            }
                        } else {

                            $getTotalDispatchData = $this->Dispatch_model->get_total_dispatch_data_details_app_open();
                            if (count($getTotalDispatchData) > 0) {
                                foreach ($getTotalDispatchData as $totaldispatchdata) {
                                    $dId = $totaldispatchdata->dispatch_id;
                                    $updateDataDispatch = array(
                                        "total_gross_volume" => $totaldispatchdata->total_gross_volume, "total_volume" => $totaldispatchdata->total_volume, "total_pieces" => $totaldispatchdata->total_pieces,
                                    );

                                    $updateDispatch = $this->Dispatch_model->update_dispatch_id($dId, $updateDataDispatch);
                                }
                            }
                        }

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $reception_arr_response;
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

    public function save_farm_from_reception($receptionId, $inventoryOrder)
    {
        try {
            if ($receptionId > 0) {

                $fetchReceptionById = $this->Reception_model->get_reception_details_app($receptionId, $inventoryOrder);
                if (count($fetchReceptionById) == 1) {

                    $originid = $fetchReceptionById[0]->origin_id;
                    $supplierid = $fetchReceptionById[0]->supplier_id;
                    $productid = $fetchReceptionById[0]->product_id;
                    $producttypeid = $fetchReceptionById[0]->supplier_product_typeid;
                    $purchasecontractid = $fetchReceptionById[0]->contract_id;
                    $inventoryorder = $fetchReceptionById[0]->salvoconducto;
                    $truckplatenumber = $fetchReceptionById[0]->truck_plate_number;
                    $receptiondate = $fetchReceptionById[0]->received_date;
                    $createdby = $fetchReceptionById[0]->createdby;
                    $conversionrate = 1;
                    
                    //$logisticcost = $fetchReceptionById[0]->logistic_cost;
                    $logisticpayto = $fetchReceptionById[0]->logistic_pay_to;

                    $date = DateTime::createFromFormat('d/m/Y', $receptiondate);
                    $receptiondate = $date->format('Y-m-d');

                    $getContractDetails = $this->Contract_model->get_contracts_by_contractid($purchasecontractid);

                    if (count($getContractDetails) == 1) {

                        if ($getContractDetails[0]->currency == 1) {
                            $conversionrate = 1;
                        } else {
                            $conversionrate = 0;
                        }


                        $purchaseunit = $getContractDetails[0]->unit_of_purchase;
                        $circumferenceAllowance = $getContractDetails[0]->purchase_allowance;
                        $lengthAllowance = $getContractDetails[0]->purchase_allowance_length;

                        $getInventoryOrderCount = $this->Farm_model->get_inventory_order_count($inventoryorder, $originid);
                        if ($getInventoryOrderCount[0]->cnt == 0) {

                            $servicecost = 0;
                            $logisticcost = $fetchReceptionById[0]->logistic_cost;

                            $totalVolume = 0;
                            $woodValue = 0;
                            $woodValueWithSupplierTaxes = 0;
                            $logisticsCostWithTaxes = 0;
                            $servicesCostWithTaxes = 0;
                            $totalValue = 0;

                            $supplierTaxesArr = array();
                            $providerLogisticTaxesArr = array();
                            $providerServiceTaxesArr = array();
                            $supplierLogisticTaxesArr = array();
                            $supplierServiceTaxesArr = array();
                            $supplierTaxesAdjustArr = array();
                            $providerLogisticTaxesAdjustArr = array();
                            $providerServiceTaxesAdjustArr = array();

                            $cProduct = 0;
                            $totalPiecesFarm = 0;
                            $totalVolumeFarm = 0;
                            $totalGrossVolumeFarm = 0;

                            $getReceptionDataByReceptionId = $this->Reception_model->get_reception_data_details_app($receptionId, $inventoryOrder);

                            $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($purchaseunit, $originid);

                            foreach ($getReceptionDataByReceptionId as $farm) {

                                $circumference = $farm->circumference_bought;
                                $length = $farm->length_bought;
                                $noOfPieces = $farm->scanned_code;

                                $netVolume = 0;
                                $grossVolume = 0;
                                if (count($getFormulae) > 0) {
                                    foreach ($getFormulae as $formula) {
                                        $strFormula = str_replace(array('$ac', '$al', '$l', '$c', 'truncate'), array($circumferenceAllowance, $lengthAllowance, $length, $circumference, '$this->truncate'), $formula->formula_context);
                                        $strFormula = "return (" . $strFormula . ");";

                                        if ($formula->type == "netvolume") {
                                            $netVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPieces;
                                        }

                                        if ($formula->type == "grossvolume") {
                                            $grossVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPieces;
                                        }
                                    }
                                }

                                $totalVolume = $totalVolume + $netVolume;

                                if ($purchaseunit == 3 || $purchaseunit == 4 || $purchaseunit == 5) {

                                    if ($noOfPieces > 0) {
                                        $getPriceRanges = $this->Farm_model->get_price_for_circumference($circumference, $purchasecontractid);

                                        if (count($getPriceRanges) == 1) {
                                            if ($purchaseunit == 3) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $noOfPieces));
                                            } else if ($purchaseunit == 4 || $purchaseunit == 5) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $netVolume));
                                            }
                                        }
                                    }
                                } else if ($purchaseunit == 6 || $purchaseunit == 7) {

                                    $cProduct = $cProduct + ($circumference * $noOfPieces);
                                    $totalPiecesFarm = $totalPiecesFarm + $noOfPieces;
                                    $totalVolumeFarm = $totalVolumeFarm + $netVolume;
                                    $totalGrossVolumeFarm = $totalGrossVolumeFarm + $grossVolume;
                                } else if ($purchaseunit == 8 || $purchaseunit == 9) {

                                    $totalVolumeFarm = $totalVolumeFarm + $netVolume;
                                    $totalGrossVolumeFarm = $totalGrossVolumeFarm + $grossVolume;
                                    $totalPiecesFarm = $totalPiecesFarm + $noOfPieces;
                                }
                            }

                            if ($purchaseunit == 6 || $purchaseunit == 7) {

                                $averageGirth = $this->truncate($cProduct / $totalPiecesFarm, 0);
                                $getPriceRanges = $this->Farm_model->get_price_for_circumference($averageGirth, $purchasecontractid);

                                if (count($getPriceRanges) == 1) {
                                    $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $totalVolumeFarm));
                                }
                            }

                            if ($purchaseunit == 8 || $purchaseunit == 9) {

                                $cftValue = round($totalGrossVolumeFarm / $totalPiecesFarm * 35.315, 2);
                                $getPriceRanges = $this->Farm_model->get_price_for_circumference($cftValue, $purchasecontractid);

                                if (count($getPriceRanges) == 1) {
                                    $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $totalVolumeFarm));
                                }
                            }

                            if ($conversionrate > 0) {
                                $woodValue = $woodValue * $conversionrate;
                            }

                            $woodValue = sprintf('%0.3f', ($woodValue + 0));

                            // WOOD VALUE WITH TAXES

                            $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierid);

                            $supplierTaxesValue = 0;
                            if (count($getSupplierTaxes) > 0) {

                                $supplierTaxesValue = 0;
                                foreach ($getSupplierTaxes as $suppliertax) {

                                    $calcValue = 0;
                                    $taxId = $suppliertax->tax_id;
                                    $taxValue = $suppliertax->tax_value;
                                    $taxFormat = $suppliertax->number_format;
                                    $taxType = $suppliertax->arithmetic_type;

                                    if ($taxValue > 0) {
                                        if ($taxType == 2) {
                                            $taxValue = $taxValue * -1;
                                        }
                                        if ($taxFormat == 2) {
                                            $calcValue = $woodValue * ($taxValue / 100);
                                        } else {
                                            $calcValue = $woodValue * ($taxValue);
                                        }
                                    }

                                    $supplierTaxesAdjustArr[] = array(
                                        "taxId" => $taxId,
                                        "taxValue" => $calcValue,
                                        "taxVal" => (abs($taxValue) + 0),
                                    );

                                    array_push($supplierTaxesArr, $taxId);

                                    $supplierTaxesValue = $supplierTaxesValue + $calcValue;
                                }
                            }

                            $woodValueWithSupplierTaxes = $woodValue + $supplierTaxesValue;

                            // END WOOD VALUE WITH TAXES
                            
                            // LOGISTICS WITH TAXES

                            if ($logisticcost != 0 && $logisticpayto > 0) {

                                $transportorIvaValue_Logistics = 0;
                                $transportorRetenctionValue_Logistics = 0;
                                $transportorReticaValue_Logistics = 0;

                                $getTransportorTaxes_Logistics = $this->Master_model->get_provider_taxes($logisticpayto);
                                $getTransportorTaxes_Logistics_Supplier = $this->Master_model->get_supplier_taxes($logisticpayto);

                                $logisticTaxesValue = 0;

                                if (count($getTransportorTaxes_Logistics) > 0) {

                                    $logisticTaxesValue = 0;
                                    foreach ($getTransportorTaxes_Logistics as $logistictaxtransportor) {

                                        $calcValue_LTransportor = 0;
                                        $taxId_LTransportor = $logistictaxtransportor->tax_id;
                                        $taxValue_LTransportor = $logistictaxtransportor->tax_value;
                                        $taxFormat_LTransportor = $logistictaxtransportor->number_format;
                                        $taxType_LTransportor = $logistictaxtransportor->arithmetic_type;

                                        if ($taxValue_LTransportor > 0) {
                                            if ($taxType_LTransportor == 2) {
                                                $taxValue_LTransportor = $taxValue_LTransportor * -1;
                                            }
                                            if ($taxFormat_LTransportor == 2) {
                                                $calcValue_LTransportor = $logisticcost * ($taxValue_LTransportor / 100);
                                            } else {
                                                $calcValue_LTransportor = $logisticcost * ($taxValue_LTransportor);
                                            }
                                        }

                                        $logisticTaxesValue = $logisticTaxesValue + $calcValue_LTransportor;

                                        $providerLogisticTaxesAdjustArr[] = array(
                                            "taxId" => $taxId_LTransportor,
                                            "taxValue" => $calcValue_LTransportor,
                                            "taxVal" => (abs($taxValue_LTransportor) + 0),
                                        );

                                        array_push($providerLogisticTaxesArr, $taxId_LTransportor);
                                    }

                                    if ($logisticTaxesValue == 0) {
                                        foreach ($getTransportorTaxes_Logistics_Supplier as $logistictaxsupplier) {

                                            $calcValue_STransportor = 0;
                                            $taxId_STransportor = $logistictaxsupplier->tax_id;
                                            $taxValue_STransportor = $logistictaxsupplier->tax_value;
                                            $taxFormat_STransportor = $logistictaxsupplier->number_format;
                                            $taxType_STransportor = $logistictaxsupplier->arithmetic_type;

                                            if ($taxValue_STransportor > 0) {
                                                if ($taxType_STransportor == 2) {
                                                    $taxValue_STransportor = $taxValue_STransportor * -1;
                                                }
                                                if ($taxFormat_STransportor == 2) {
                                                    $calcValue_STransportor = $logisticcost * ($taxValue_STransportor / 100);
                                                } else {
                                                    $calcValue_STransportor = $logisticcost * ($taxValue_STransportor);
                                                }
                                            }

                                            $logisticTaxesValue = $logisticTaxesValue + $calcValue_STransportor;

                                            $providerLogisticTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_STransportor,
                                                "taxValue" => $calcValue_STransportor,
                                                "taxVal" => (abs($taxValue_STransportor) + 0),
                                            );

                                            array_push($supplierLogisticTaxesArr, $taxId_STransportor);
                                        }
                                    }
                                } else if ($logisticpayto == $supplierid) {

                                    $getTransportorTaxes_Logistics = $this->Master_model->get_supplier_taxes($logisticpayto);

                                    $logisticTaxesValue = 0;
                                    if (count($getTransportorTaxes_Logistics) > 0) {

                                        foreach ($getTransportorTaxes_Logistics as $transporttax) {

                                            $calcValue_logistic = 0;
                                            $taxId_logistic = $transporttax->tax_id;
                                            $taxValue_logistic = $transporttax->tax_value;
                                            $taxFormat_logistic = $transporttax->number_format;
                                            $taxType_logistic = $transporttax->arithmetic_type;

                                            if ($taxValue_logistic > 0) {
                                                if ($taxType_logistic == 2) {
                                                    $taxValue_logistic = $taxValue_logistic * -1;
                                                }
                                                if ($taxFormat_logistic == 2) {
                                                    $calcValue_logistic = $logisticcost * ($taxValue_logistic / 100);
                                                } else {
                                                    $calcValue_logistic = $logisticcost * ($taxValue_logistic);
                                                }
                                            }

                                            $logisticTaxesValue = $logisticTaxesValue + $calcValue_logistic;

                                            $providerLogisticTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_logistic,
                                                "taxValue" => $calcValue_logistic,
                                                "taxVal" => (abs($taxValue_logistic) + 0),
                                            );

                                            array_push($supplierLogisticTaxesArr, $taxId_logistic);
                                        }
                                    }
                                }
                            }

                            $logisticsCostWithTaxes = $logisticcost + $logisticTaxesValue;

                            // END LOGISTICS WITH TAXES

                            $totalValue = $woodValue + $logisticcost + $servicecost;

                            $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                            $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                            $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                            $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                            $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);
                            
                            $dataFarm = array(
                                "supplier_id" => $supplierid, "contract_id" => $purchasecontractid,
                                "product_id" => $productid, "product_type_id" => $producttypeid, "purchase_unit_id" => $purchaseunit,
                                "inventory_order" => $inventoryorder, "plate_number" => $truckplatenumber,
                                "purchase_date" => $receptiondate, "service_cost" => $servicecost,
                                "logistic_cost" => $logisticcost, "adjustment" => 0,
                                "total_volume" => $totalVolume, "total_value" => $totalValue, "wood_value" => $woodValue,
                                "pay_service_to" => 0, "pay_logistics_to" => $logisticpayto,
                                "exchange_rate" => $conversionrate,
                                "created_by" => $createdby, "updated_by" => $createdby, "is_active" => 1,
                                "origin_id" => $originid, "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                "service_cost_withtaxes" => $servicesCostWithTaxes, "logistic_cost_withtaxes" => $logisticsCostWithTaxes,
                                "supplier_taxes" => $supplierTaxesArrList, "logistic_taxes" => $supplierLogisticTaxesArrList,
                                "service_taxes" => $supplierServiceTaxesArrList, "adjust_taxes" => '',
                                "is_adjust_rf" => 0, "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                "service_provider_taxes" => $providerServiceTaxesArrList, "adjusted_value" => 0,
                                "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                            );

                            $insertFarm = $this->Farm_model->add_farm($dataFarm);

                            if ($insertFarm > 0) {
                                $dataFarmData = array();
                                foreach ($getReceptionDataByReceptionId as $farm) {
                                    $circumference = $farm->circumference_bought;
                                    $netVolume = $farm->netVolume;
                                    $noOfPieces = $farm->scanned_code;
                                    $length = $farm->length_bought;

                                    if ($noOfPieces > 0) {
                                        $dataFarmData[] = array(
                                            "farm_id" => $insertFarm, "scanned_code" => "",
                                            "no_of_pieces" => $noOfPieces, "circumference" => $circumference,
                                            "length" => $length, "width" => 0, "thickness" => 0, "volume" => $netVolume,
                                            "volume_pie" => 0, "grade_id" => 0, "length_export" => 0, "width_export" => 0,
                                            "thickness_export" => 0, "volume_bought" => 0, "created_by" => $createdby,
                                            "updated_by" => $createdby, "is_active" => 1,
                                            "created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
                                        );
                                    }
                                }

                                if (count($dataFarmData) > 0) {
                                    $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                    if ($insertFarmData) {
                                        //SUPPLIER PRICE
                                        $this->Farm_model->add_supplier_price(
                                            $purchasecontractid,
                                            $supplierid,
                                            $inventoryorder,
                                            $createdby
                                        );

                                        //CONTRACT INVENTORY MAPPING
                                        $dataContractMapping = array(
                                            "contract_id" => $purchasecontractid, "supplier_id" => $supplierid,
                                            "inventory_order" => $inventoryorder, "total_volume" => $totalVolume,
                                            "invoice_number" => "", "created_by" => $createdby,
                                            "updated_by" => $createdby, "is_active" => 1,
                                        );

                                        $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                        $dataInventoryLedger = array(
                                            "contract_id" => $purchasecontractid,
                                            "inventory_order" => $inventoryorder, "ledger_type" => 2,
                                            "expense_date" => $receptiondate, "created_by" => $createdby,
                                            "updated_by" => $createdby, "is_active" => 1, "is_advance_app" => 0,
                                        );

                                        if ($woodValueWithSupplierTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierid);
                                        }

                                        $getContracts = $this->Contract_model->get_contracts_by_contractid($purchasecontractid);
                                        if (count($getContracts) == 1) {
                                            $remainingVolume = $getContracts[0]->remaining_volume - $totalVolume;

                                            $dataRemainingVolume = array(
                                                "remaining_volume" => $remainingVolume,
                                            );

                                            $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchasecontractid, $supplierid);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $Return["status"] = false;
            $Return["message"] = $e->getMessage();
            http_response_code(500);
            $this->output($Return);
        }
    }

    public function truncate($val, $f = "0")
    {
        if (($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }
}