<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Dispatches extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dispatch_model");
        $this->load->model("Reception_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->library('excel');
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("dispatch_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_dispatches";
        if (!empty($session)) {
            $data["subview"] = $this->load->view("dispatches/dispatch_list", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function dispatch_list()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            $dispatches = $this->Dispatch_model->all_dispatches($this->input->get("originid"), $this->input->get("dispatchstatus"));

            $data = array();

            foreach ($dispatches as $r) {
                $editDispatch = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("download_excel") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloaddispatch" data-toggle="modal" data-target=".download-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-download"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewdispatch" data-toggle="modal" data-target=".view-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-eye"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletedispatch" data-toggle="modal" data-target=".delete-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-trash"></span></button></span>';

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $editDispatch,
                    $r->container_number,
                    $r->shipping_line,
                    $product,
                    $r->dispatch_date,
                    $r->warehouse_name,
                    ($r->total_pieces + 0),
                    ($r->total_volume + 0),
                    $r->origin,
                    ucwords(strtolower($r->uploadedby)),
                );
            }

            $output = array(
                "data" => $data
            );
            echo json_encode($output);
            exit();
        } else {
            redirect("/logout");
        }
    }

    public function dialog_dispatch_add()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->get("type") == "adddispatch") {
                $data = array(
                    "pageheading" => $this->lang->line("add_dispatch"),
                    "pagetype" => "add",
                    "dispatchid" => 0,
                    "container_number" => "",
                );
            }
            $this->load->view('dispatches/dialog_add_dispatch', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_products_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getProducts = $this->Master_model->get_product_byorigin($this->input->get("originid"));
                foreach ($getProducts as $products) {
                    $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_product_types()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getProductTypes = $this->Master_model->all_product_types();
                foreach ($getProductTypes as $producttype) {
                    $result = $result . "<option value='" . $producttype->type_id . "'>" . $producttype->product_type_name . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_warehouses_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getWarehouses = $this->Master_model->get_warehouse_by_origin($this->input->get("originid"));
                foreach ($getWarehouses as $warehouse) {
                    $result = $result . "<option value='" . $warehouse->whid . "'>" . $warehouse->warehouse_name . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_shipping_lines_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($this->input->get("originid"));
                foreach ($getShippingLines as $shippingline) {
                    $result = $result . "<option value='" . $shippingline->id . "'>" . $shippingline->shipping_line . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function load_dispatch_template()
    {
        try {
            $session = $this->session->userdata('fullname');
            $Return = array(
                "result" => "", "error" => "", "templateerror" => false, "templateerrordata" => "", "redirect" => false, "csrf_hash" => "", "warning" => "", "success" => "",
            );

            if (!empty($session)) {

                $containerNumberPost = $this->input->post("containerNumber");
                $originId = $this->input->post("originId");
                $productTypeId = $this->input->post("productTypeId");
                $productid = $this->input->post("productid");
                $uploadType = $this->input->post("uploadType");

                $containerNumberPost = strtoupper(str_replace(array('-', ' '), '', $containerNumberPost));

                $getContainerCount = $this->Dispatch_model->get_container_count($containerNumberPost, $originId);

                if ($getContainerCount[0]->cnt == 0) {
                    
                    if ($_FILES["fileDispatchExcel"]["size"] > 0) {
                        $config["upload_path"] = FCPATH . "reports/";
                        $config["allowed_types"] = "xlsx";
                        $config["remove_spaces"] = TRUE;
                        $this->load->library("upload", $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload("fileDispatchExcel")) {
                            $Return["error"] = $this->lang->line("error_excel_upload");
                            $Return["result"] = "";
                            $Return["redirect"] = false;
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $data = array("upload_data" => $this->upload->data());
                            $inputFileName = FCPATH . "reports/" . $data["upload_data"]["file_name"];
                            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                            $objPHPExcel = $objReader->load($inputFileName);
                            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            $arrayCount = count($allDataInSheet);
                            
                            if ($arrayCount > 0) {

                                if (($productid == 4 || $productid == 1 || $productid == 2) && ($productTypeId == 1 || $productTypeId == 3)) {
                                    if ($uploadType == 1) {
                                        
                                        $createArray = array('CONTAINER NUMBER', 'NO OF PIECES', 'LENGTH', 'THICKNESS', 'WIDTH', 'INVENTORY ORDER');
                                        $makeArray = array('CONTAINERNUMBER' => 'CONTAINER NUMBER', 'NOOFPIECES' => 'NO OF PIECES', 'LENGTH' => 'LENGTH', 'THICKNESS' => 'THICKNESS', 'WIDTH' => 'WIDTH', 'INVENTORYORDER' => 'INVENTORY ORDER');
                                        $SheetDataKey = array();
                                        foreach ($allDataInSheet as $dataInSheet) {
                                            foreach ($dataInSheet as $key => $value) {

                                                if (in_array(trim($value), $createArray)) {
                                                    $value = preg_replace('/\s+/', '', $value);
                                                    $SheetDataKey[trim($value)] = $key;
                                                }
                                            }
                                        }
                                        $data = array_diff_key($makeArray, $SheetDataKey);
                                        $dispatchData = array();
                                        $dispatchErrorData = array();
                                        $lengthArray = array();
                                        $widthArray = array();
                                        $thicknessArray = array();
                                        $totalPieces = 0;
                                        $totalNetVolume = 0;
                                        $totalGrossVolume = 0;
                                        if (empty($data)) {

                                            $isContainError = false;

                                            for ($i = 2; $i <= $arrayCount; $i++) {
                                                $containerNumber = $SheetDataKey["CONTAINERNUMBER"];
                                                $inventoryOrder = $SheetDataKey["INVENTORYORDER"];
                                                $noOfPieces = $SheetDataKey["NOOFPIECES"];
                                                $length = $SheetDataKey["LENGTH"];
                                                $width = $SheetDataKey["WIDTH"];
                                                $thickness = $SheetDataKey["THICKNESS"];
                                                
                                                $containerNumberVal = filter_var(trim($allDataInSheet[$i][$containerNumber]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $noOfPiecesVal = filter_var(trim($allDataInSheet[$i][$noOfPieces]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $widthVal = filter_var(trim($allDataInSheet[$i][$width]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $thicknessVal = filter_var(trim($allDataInSheet[$i][$thickness]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                                $containerNumberVal = strtoupper(str_replace(array('-', ' '), '', $containerNumberVal));

                                                if (($containerNumberVal == null || $containerNumberVal == "") ||
                                                    ($inventoryOrderVal == null || $inventoryOrderVal == "") ||
                                                    ($widthVal == null || $widthVal == "") ||
                                                    ($lengthVal == null || $lengthVal == "") || 
                                                    ($thicknessVal == null || $thicknessVal == "")
                                                ) {
                                                    if (($containerNumberVal == null || $containerNumberVal == "") &&
                                                        ($inventoryOrderVal == null || $inventoryOrderVal == "") &&
                                                        ($widthVal == null || $widthVal == "") &&
                                                        ($lengthVal == null && $lengthVal == "") && 
                                                        ($thicknessVal == null || $thicknessVal == "")
                                                    ) {
                                                        //IGNORE
                                                    } else {

                                                        $isContainError = true;

                                                        $dispatchErrorData[] = array(
                                                            "containerNumber" => $containerNumberVal,
                                                            "inventoryOrder" => $inventoryOrderVal,
                                                            "width" => $widthVal,
                                                            "length" => $lengthVal,
                                                            "thickness" => $thicknessVal,
                                                            "noOfPieces" => $noOfPiecesVal,
                                                            "errorType" => 1,
                                                            "remainingPieces" => -1,
                                                        );
                                                    }
                                                } else {

                                                    if ($containerNumberVal == $containerNumberPost) {

                                                        $checkInventoryExists = $this->Dispatch_model->check_inventory_order_exists($inventoryOrderVal, $originId);

                                                        if ($checkInventoryExists[0]->cnt == 1) {

                                                            $checkPiecesAvailability = $this->Dispatch_model->check_dispatch_pieces_availablity_squareblock($inventoryOrderVal, $widthVal, $lengthVal, $thicknessVal, $originId);

                                                            if ($checkPiecesAvailability[0]->remaining_pieces <= 0) {
                                                                $isContainError = true;

                                                                $dispatchErrorData[] = array(
                                                                    "containerNumber" => $containerNumberVal,
                                                                    "inventoryOrder" => $inventoryOrderVal,
                                                                    "width" => $widthVal,
                                                                    "length" => $lengthVal,
                                                                    "thickness" => $thicknessVal,
                                                                    "noOfPieces" => $noOfPiecesVal,
                                                                    "errorType" => 3,
                                                                    "remainingPieces" => 0,
                                                                );
                                                            } else {

                                                                if ($noOfPiecesVal > 0) {
                                                                    if ($noOfPiecesVal > $checkPiecesAvailability[0]->remaining_pieces) {
                                                                        $isContainError = true;

                                                                        $dispatchErrorData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "width" => $widthVal,
                                                                            "length" => $lengthVal,
                                                                            "thickness" => $thicknessVal,
                                                                            "noOfPieces" => $noOfPiecesVal,
                                                                            "errorType" => 3,
                                                                            "remainingPieces" => $checkPiecesAvailability[0]->remaining_pieces,
                                                                        );
                                                                    } else {
                                                                        $dispatchErrorData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "width" => $widthVal,
                                                                            "length" => $lengthVal,
                                                                            "thickness" => $thicknessVal,
                                                                            "noOfPieces" => $noOfPiecesVal,
                                                                            "errorType" => 0,
                                                                            "remainingPieces" => -1,
                                                                        );

                                                                        $dispatchData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "width" => $widthVal,
                                                                            "length" => $lengthVal,
                                                                            "thickness" => $thicknessVal,
                                                                            "noOfPieces" => ($noOfPiecesVal + 0),
                                                                            "grossVolume" => 0,
                                                                            "netVolume" => 0,
                                                                        );

                                                                        array_push($widthArray, ($widthVal + 0));
                                                                        array_push($lengthArray, ($lengthVal + 0));
                                                                        array_push($thicknessArray, ($thicknessVal + 0));
                                                                    }
                                                                } else {
                                                                    // DO NOTHING
                                                                }
                                                            }
                                                        } else {
                                                            $isContainError = true;

                                                            $dispatchErrorData[] = array(
                                                                "containerNumber" => $containerNumberVal,
                                                                "inventoryOrder" => $inventoryOrderVal,
                                                                "width" => $widthVal,
                                                                "length" => $lengthVal,
                                                                "thickness" => $thicknessVal,
                                                                "noOfPieces" => $noOfPiecesVal,
                                                                "errorType" => 2,
                                                                "remainingPieces" => -1,
                                                            );
                                                        }
                                                    } else {
                                                        $isContainError = true;

                                                        $dispatchErrorData[] = array(
                                                            "containerNumber" => $containerNumberVal,
                                                            "inventoryOrder" => $inventoryOrderVal,
                                                            "width" => $widthVal,
                                                            "length" => $lengthVal,
                                                            "thickness" => $thicknessVal,
                                                            "noOfPieces" => $noOfPiecesVal,
                                                            "errorType" => 4,
                                                            "remainingPieces" => -1,
                                                        );
                                                    }
                                                }
                                            }

                                            if ($isContainError == true && count($dispatchErrorData) > 0) {
                                                $Return["error"] = "";
                                                $Return["templateerror"] = true;
                                                $Return["templateerrordata"] = $dispatchErrorData;
                                                $Return["result"] = "";
                                                $Return["redirect"] = false;
                                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                $this->output($Return);
                                                exit;
                                            } else {
                                                $dispatchErrorData = array();

                                                //CALCULATE
                                                if (count($dispatchData) > 0) {

                                                    if($originId == 1) {
                                                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(2, $originId);
                                                    } else {
                                                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(5, $originId);
                                                    }

                                                    
                                                    $totalPieces = 0;
                                                    $totalNetVolume = 0;

                                                    //$arrayCircumference = array_unique($circumferenceArray);
                                                    //$arrayLength = array_unique($lengthArray);
                                                    $calcVolumeArray = array();

                                                    if (count($getFormulae) > 0) {
                                                        if (count($dispatchData) > 0) {
                                                            foreach ($dispatchData as $datadispatch) {
                                                                $totalPieces = $totalPieces + $datadispatch["noOfPieces"];
                                                                // foreach ($getFormulae as $formula) {
                                                                //     $strFormula = str_replace(array('$l', '$c', 'truncate'), array($datadispatch["length"], $datadispatch["circumference"], '$this->truncate'), $formula->calculation_formula);
                                                                //     $strFormula = "return (" . $strFormula . ");";

                                                                //     if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                                                //         $grossVolume = sprintf('%0.3f', eval($strFormula)) * $datadispatch["noOfPieces"];
                                                                //         $datadispath["grossVolume"] = $grossVolume;

                                                                //         $totalGrossVolume = $totalGrossVolume + $grossVolume;
                                                                //     }

                                                                //     if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                                                //         $netVolume = sprintf('%0.3f', eval($strFormula)) * $datadispatch["noOfPieces"];
                                                                //         $datadispath["netVolume"] = $netVolume;

                                                                //         $totalNetVolume = $totalNetVolume + $netVolume;
                                                                //     }
                                                                // }

                                                                $lengthExport = $this->truncate($datadispatch["length"] * 0.3048, 2);
                                                                $widthExport = $this->truncate($datadispatch["width"] * 0.0254, 2);
                                                                $thicknessExport = $this->truncate($datadispatch["thickness"] * 0.0254, 2);
                                                                $volumePie = $this->truncate(($datadispatch["length"] * $datadispatch["width"] * $datadispatch["thickness"] / 12) * $datadispatch["noOfPieces"], 0);
                                                                $face = $datadispatch["width"] * $datadispatch["width"];
                                                                $netVolume = round($widthExport * $thicknessExport * $lengthExport, 3) * $datadispatch["noOfPieces"];

                                                                $totalGrossVolume = $volumePie/424;
                                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                            }
                                                        }
                                                    }

                                                    $dataUploaded = array(
                                                        "dispatchData" => $dispatchData,
                                                        "totalPieces" => $totalPieces,
                                                        "measurementSystemId" => 2,
                                                        "totalVolume" => sprintf('%0.3f', $totalNetVolume),
                                                        "totalGrossVolume" => sprintf('%0.3f', $totalGrossVolume),
                                                    );

                                                    $Return["error"] = "";
                                                    $Return["templateerror"] = false;
                                                    $Return["templateerrordata"] = "";
                                                    $Return["result"] = $dataUploaded;
                                                    $Return["redirect"] = false;
                                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                    $this->output($Return);
                                                    exit;
                                                } else {
                                                    $Return["error"] = $this->lang->line("error_excel_template");;
                                                    $Return["templateerror"] = false;
                                                    $Return["templateerrordata"] = "";
                                                    $Return["result"] = "";
                                                    $Return["redirect"] = false;
                                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                    $this->output($Return);
                                                    exit;
                                                }
                                            }

                                        } else {
                                            $Return["error"] = $this->lang->line("error_excel_template");
                                            $Return["result"] = "";
                                            $Return["templateerror"] = false;
                                            $Return["redirect"] = false;
                                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    }
                                } else if ($productTypeId == 1 || $productTypeId == 3) {
                                } else if ($productTypeId == 2 || $productTypeId == 4) {

                                    if ($uploadType == 1) {
                                        
                                        $createArray = array('CONTAINER NUMBER', 'NO OF PIECES', 'CIRCUMFERENCE', 'LENGTH', 'INVENTORY ORDER');
                                        $makeArray = array('CONTAINERNUMBER' => 'CONTAINER NUMBER', 'NOOFPIECES' => 'NO OF PIECES', 'CIRCUMFERENCE' => 'CIRCUMFERENCE', 'LENGTH' => 'LENGTH', 'INVENTORYORDER' => 'INVENTORY ORDER');
                                        $SheetDataKey = array();
                                        foreach ($allDataInSheet as $dataInSheet) {
                                            foreach ($dataInSheet as $key => $value) {

                                                if (in_array(trim($value), $createArray)) {
                                                    $value = preg_replace('/\s+/', '', $value);
                                                    $SheetDataKey[trim($value)] = $key;
                                                }
                                            }
                                        }
                                        $data = array_diff_key($makeArray, $SheetDataKey);
                                        $dispatchData = array();
                                        $dispatchErrorData = array();
                                        $circumferenceArray = array();
                                        $lengthArray = array();
                                        $totalPieces = 0;
                                        $totalNetVolume = 0;
                                        $totalGrossVolume = 0;
                                        if (empty($data)) {

                                            $isContainError = false;

                                            for ($i = 2; $i <= $arrayCount; $i++) {
                                                $containerNumber = $SheetDataKey["CONTAINERNUMBER"];
                                                $inventoryOrder = $SheetDataKey["INVENTORYORDER"];
                                                $noOfPieces = $SheetDataKey["NOOFPIECES"];
                                                $circumference = $SheetDataKey["CIRCUMFERENCE"];
                                                $length = $SheetDataKey["LENGTH"];

                                                $containerNumberVal = filter_var(trim($allDataInSheet[$i][$containerNumber]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $noOfPiecesVal = filter_var(trim($allDataInSheet[$i][$noOfPieces]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $circumferenceVal = filter_var(trim($allDataInSheet[$i][$circumference]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                                $containerNumberVal = strtoupper(str_replace(array('-', ' '), '', $containerNumberVal));

                                                if (($containerNumberVal == null || $containerNumberVal == "") ||
                                                    ($inventoryOrderVal == null || $inventoryOrderVal == "") ||
                                                    ($circumferenceVal == null || $circumferenceVal == "") ||
                                                    ($lengthVal == null || $lengthVal == "")
                                                ) {
                                                    if (($containerNumberVal == null || $containerNumberVal == "") &&
                                                        ($inventoryOrderVal == null || $inventoryOrderVal == "") &&
                                                        ($circumferenceVal == null || $circumferenceVal == "") &&
                                                        ($lengthVal == null && $lengthVal == "")
                                                    ) {
                                                        //IGNORE
                                                    } else {

                                                        $isContainError = true;

                                                        $dispatchErrorData[] = array(
                                                            "containerNumber" => $containerNumberVal,
                                                            "inventoryOrder" => $inventoryOrderVal,
                                                            "circumference" => $circumferenceVal,
                                                            "length" => $lengthVal,
                                                            "noOfPieces" => $noOfPiecesVal,
                                                            "errorType" => 1,
                                                            "remainingPieces" => -1,
                                                        );
                                                    }
                                                } else {

                                                    if ($containerNumberVal == $containerNumberPost) {

                                                        $checkInventoryExists = $this->Dispatch_model->check_inventory_order_exists($inventoryOrderVal, $originId);

                                                        if ($checkInventoryExists[0]->cnt == 1) {

                                                            $checkPiecesAvailability = $this->Dispatch_model->check_dispatch_pieces_availablity($inventoryOrderVal, $circumferenceVal, $lengthVal, $originId);

                                                            if ($checkPiecesAvailability[0]->remaining_pieces <= 0) {
                                                                $isContainError = true;

                                                                $dispatchErrorData[] = array(
                                                                    "containerNumber" => $containerNumberVal,
                                                                    "inventoryOrder" => $inventoryOrderVal,
                                                                    "circumference" => $circumferenceVal,
                                                                    "length" => $lengthVal,
                                                                    "noOfPieces" => $noOfPiecesVal,
                                                                    "errorType" => 3,
                                                                    "remainingPieces" => 0,
                                                                );
                                                            } else {

                                                                if ($noOfPiecesVal > 0) {
                                                                    if ($noOfPiecesVal > $checkPiecesAvailability[0]->remaining_pieces) {
                                                                        $isContainError = true;

                                                                        $dispatchErrorData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "circumference" => $circumferenceVal,
                                                                            "length" => $lengthVal,
                                                                            "noOfPieces" => $noOfPiecesVal,
                                                                            "errorType" => 3,
                                                                            "remainingPieces" => $checkPiecesAvailability[0]->remaining_pieces,
                                                                        );
                                                                    } else {
                                                                        $dispatchErrorData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "circumference" => $circumferenceVal,
                                                                            "length" => $lengthVal,
                                                                            "noOfPieces" => $noOfPiecesVal,
                                                                            "errorType" => 0,
                                                                            "remainingPieces" => -1,
                                                                        );

                                                                        $dispatchData[] = array(
                                                                            "containerNumber" => $containerNumberVal,
                                                                            "inventoryOrder" => $inventoryOrderVal,
                                                                            "circumference" => ($circumferenceVal + 0),
                                                                            "length" => ($lengthVal + 0),
                                                                            "noOfPieces" => ($noOfPiecesVal + 0),
                                                                            "grossVolume" => 0,
                                                                            "netVolume" => 0,
                                                                        );

                                                                        array_push($circumferenceArray, ($circumferenceVal + 0));
                                                                        array_push($lengthArray, ($lengthVal + 0));
                                                                    }
                                                                } else {
                                                                    // DO NOTHING
                                                                }
                                                            }
                                                        } else {
                                                            $isContainError = true;

                                                            $dispatchErrorData[] = array(
                                                                "containerNumber" => $containerNumberVal,
                                                                "inventoryOrder" => $inventoryOrderVal,
                                                                "circumference" => $circumferenceVal,
                                                                "length" => $lengthVal,
                                                                "noOfPieces" => $noOfPiecesVal,
                                                                "errorType" => 2,
                                                                "remainingPieces" => -1,
                                                            );
                                                        }
                                                    } else {
                                                        $isContainError = true;

                                                        $dispatchErrorData[] = array(
                                                            "containerNumber" => $containerNumberVal,
                                                            "inventoryOrder" => $inventoryOrderVal,
                                                            "circumference" => $circumferenceVal,
                                                            "length" => $lengthVal,
                                                            "noOfPieces" => $noOfPiecesVal,
                                                            "errorType" => 4,
                                                            "remainingPieces" => -1,
                                                        );
                                                    }
                                                }
                                            }

                                            if ($isContainError == true && count($dispatchErrorData) > 0) {
                                                $Return["error"] = "";
                                                $Return["templateerror"] = true;
                                                $Return["templateerrordata"] = $dispatchErrorData;
                                                $Return["result"] = "";
                                                $Return["redirect"] = false;
                                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                $this->output($Return);
                                                exit;
                                            } else {
                                                $dispatchErrorData = array();

                                                //CALCULATE
                                                if (count($dispatchData) > 0) {

                                                    if($originId == 1) {
                                                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(2, $originId);
                                                    } else {
                                                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(5, $originId);
                                                    }

                                                    
                                                    $totalPieces = 0;
                                                    $totalNetVolume = 0;

                                                    //$arrayCircumference = array_unique($circumferenceArray);
                                                    //$arrayLength = array_unique($lengthArray);
                                                    $calcVolumeArray = array();

                                                    if (count($getFormulae) > 0) {
                                                        // foreach ($arrayLength as $uniquelength) {

                                                        //     foreach ($arrayCircumference as $uniquecircumference) {
                                                        //         $totalPiecesCircumferenceLength = 0;
                                                        //         foreach ($dispatchData as &$datadispath) {
                                                        //             if ($datadispath["circumference"] == $uniquecircumference && $datadispath["length"] == $uniquelength) {
                                                        //                 $totalPieces = $totalPieces + $datadispath["noOfPieces"];
                                                        //                 $totalPiecesCircumferenceLength = $totalPiecesCircumferenceLength + $datadispath["noOfPieces"];
                                                        //             }
                                                        //         }

                                                        //         $calcVolumeArray[] = array(
                                                        //             "totalPieces" => $totalPiecesCircumferenceLength,
                                                        //             "length" => $uniquelength,
                                                        //             "circumference" => $uniquecircumference,
                                                        //         );
                                                        //     }
                                                        // }

                                                        if (count($dispatchData) > 0) {
                                                            foreach ($dispatchData as $datadispatch) {
                                                                $totalPieces = $totalPieces + $datadispatch["noOfPieces"];
                                                                foreach ($getFormulae as $formula) {
                                                                    $strFormula = str_replace(array('$l', '$c', 'truncate'), array($datadispatch["length"], $datadispatch["circumference"], '$this->truncate'), $formula->calculation_formula);
                                                                    $strFormula = "return (" . $strFormula . ");";

                                                                    if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                                                        $grossVolume = sprintf('%0.3f', eval($strFormula)) * $datadispatch["noOfPieces"];
                                                                        $datadispath["grossVolume"] = $grossVolume;

                                                                        $totalGrossVolume = $totalGrossVolume + $grossVolume;
                                                                    }

                                                                    if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                                                        $netVolume = sprintf('%0.3f', eval($strFormula)) * $datadispatch["noOfPieces"];
                                                                        $datadispath["netVolume"] = $netVolume;

                                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $dataUploaded = array(
                                                        "dispatchData" => $dispatchData,
                                                        "totalPieces" => $totalPieces,
                                                        "measurementSystemId" => 2,
                                                        "totalVolume" => sprintf('%0.3f', $totalNetVolume),
                                                        "totalGrossVolume" => sprintf('%0.3f', $totalGrossVolume),
                                                    );

                                                    $Return["error"] = "";
                                                    $Return["templateerror"] = false;
                                                    $Return["templateerrordata"] = "";
                                                    $Return["result"] = $dataUploaded;
                                                    $Return["redirect"] = false;
                                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                    $this->output($Return);
                                                    exit;
                                                } else {
                                                    $Return["error"] = $this->lang->line("error_excel_template");;
                                                    $Return["templateerror"] = false;
                                                    $Return["templateerrordata"] = "";
                                                    $Return["result"] = "";
                                                    $Return["redirect"] = false;
                                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                                    $this->output($Return);
                                                    exit;
                                                }
                                            }

                                            // if (count($dispatchData) == 0 || $totalPieces == 0) {
                                            //     $Return["warning"] = $this->lang->line("error_nodata_excel");
                                            //     $Return["error"] = "";
                                            //     $Return["result"] = "";
                                            //     $Return["redirect"] = false;
                                            //     $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            //     $this->output($Return);
                                            //     exit;
                                            // } else {

                                            //     $dataUploaded = array(
                                            //         "dispatchData" => $dispatchData,
                                            //         "totalPieces" => $totalPieces,
                                            //         "purchaseUnit" => "",
                                            //         "totalVolume" => sprintf("%0.3f", $totalNetVolume),
                                            //     );

                                            //     $Return["error"] = "";
                                            //     $Return["result"] = $dataUploaded;
                                            //     $Return["redirect"] = false;
                                            //     $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            //     $this->output($Return);
                                            //     exit;
                                            // }


                                        } else {
                                            $Return["error"] = $this->lang->line("error_excel_template");
                                            $Return["result"] = "";
                                            $Return["templateerror"] = false;
                                            $Return["redirect"] = false;
                                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    }
                                } else {
                                    $Return["error"] = $this->lang->line("error_excel_template");
                                    $Return["result"] = "";
                                    $Return["templateerror"] = false;
                                    $Return["redirect"] = false;
                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return["warning"] = $this->lang->line("error_nodata_excel");
                                $Return["error"] = "";
                                $Return["templateerror"] = false;
                                $Return["result"] = "";
                                $Return["redirect"] = false;
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        }
                    } else {
                        $Return["error"] = $this->lang->line("error_loadtemplate");
                        $Return["result"] = "";
                        $Return["templateerror"] = false;
                        $Return["redirect"] = false;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["error"] = $this->lang->line("exist_container_number");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["templateerror"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["templateerror"] = false;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $this->lang->line("error_loadtemplate");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["templateerror"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_error_report()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array("result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => "");

            if (!empty($session)) {
                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $errorDataPost = $this->input->post("errorData");
                $productTypeId = $this->input->post("productTypeId");
                $productid = $this->input->post("productid");
                $errordataJson = json_decode($errorDataPost, true);

                if (count($errordataJson) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    if(($productid == 4 || $productid == 1 || $productid == 2) && ($productTypeId == 1 || $productTypeId == 3)) {
                        $objSheet->SetCellValue("A1", $this->lang->line("container_number_error_report"));
                        $objSheet->SetCellValue("B1", $this->lang->line("no_of_pieces_error_report"));
                        $objSheet->SetCellValue("C1", $this->lang->line("length_error_report"));
                        $objSheet->SetCellValue("D1", $this->lang->line("thickness_error_report"));
                        $objSheet->SetCellValue("E1", $this->lang->line("width_error_report"));
                        $objSheet->SetCellValue("F1", $this->lang->line("inventory_error_report"));
                        $objSheet->SetCellValue("G1", $this->lang->line("remarks_error_report"));

                        $objSheet->getStyle("A1:G1")
                            ->getFont()
                            ->setBold(true);
    
                        $objSheet->getStyle("A1:G1")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("00B0F0");
    
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $rowCountData = 2;
    
                        foreach ($errordataJson as $errordata) {
    
                            $errorType = $errordata["errorType"];
    
                            $objSheet->SetCellValue("A$rowCountData", strtoupper($errordata["containerNumber"]));
                            $objSheet->SetCellValue("B$rowCountData", $errordata["noOfPieces"]);
                            $objSheet->SetCellValue("C$rowCountData", $errordata["length"]);
                            $objSheet->SetCellValue("D$rowCountData", $errordata["thickness"]);
                            $objSheet->SetCellValue("E$rowCountData", ($errordata["width"]));
                            $objSheet->SetCellValue("F$rowCountData", ($errordata["inventoryOrder"]));
    
                            if ($errorType == 0) {
                                $objSheet->SetCellValue("G$rowCountData", $this->lang->line("valid_text"));
                                $objSheet->getStyle("G$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("00CB1F");
                            } else if ($errorType == 1) {
                                $objSheet->SetCellValue("G$rowCountData", $this->lang->line("data_missing"));
                                $objSheet->getStyle("G$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FFFF00");
                            } else if ($errorType == 2) {
                                $objSheet->SetCellValue("G$rowCountData", $this->lang->line("invalid_inventory_order"));
                                $objSheet->getStyle("G$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FD543B");
                            } else if ($errorType == 3) {
                                $objSheet->SetCellValue("G$rowCountData", $this->lang->line("insufficient_pieces") . ": " . ($errordata["remainingPieces"] + 0));
                                $objSheet->getStyle("G$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FFC000");
                            } else if ($errorType == 4) {
                                $objSheet->SetCellValue("G$rowCountData", $this->lang->line("invalid_container_number"));
                                $objSheet->getStyle("G$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FD543B");
                            }
    
                            $rowCountData++;
                        }
    
                        $rowCountData = $rowCountData - 1;
                        $objSheet->getStyle("A1:G$rowCountData")->applyFromArray($styleArray);
    
                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                        $objSheet->getColumnDimension("G")->setAutoSize(false)->setWidth(31);
                    } else {
                        $objSheet->SetCellValue("A1", $this->lang->line("container_number_error_report"));
                        $objSheet->SetCellValue("B1", $this->lang->line("no_of_pieces_error_report"));
                        $objSheet->SetCellValue("C1", $this->lang->line("circumference_error_report"));
                        $objSheet->SetCellValue("D1", $this->lang->line("length_error_report"));
                        $objSheet->SetCellValue("E1", $this->lang->line("inventory_error_report"));
                        $objSheet->SetCellValue("F1", $this->lang->line("remarks_error_report"));
    
                        $objSheet->getStyle("A1:F1")
                            ->getFont()
                            ->setBold(true);
    
                        $objSheet->getStyle("A1:F1")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("00B0F0");
    
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
    
                        $rowCountData = 2;
    
                        foreach ($errordataJson as $errordata) {
    
                            $errorType = $errordata["errorType"];
    
                            $objSheet->SetCellValue("A$rowCountData", strtoupper($errordata["containerNumber"]));
                            $objSheet->SetCellValue("B$rowCountData", $errordata["noOfPieces"]);
                            $objSheet->SetCellValue("C$rowCountData", $errordata["circumference"]);
                            $objSheet->SetCellValue("D$rowCountData", $errordata["length"]);
                            $objSheet->SetCellValue("E$rowCountData", ($errordata["inventoryOrder"]));
    
                            if ($errorType == 0) {
                                $objSheet->SetCellValue("F$rowCountData", $this->lang->line("valid_text"));
                                $objSheet->getStyle("F$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("00CB1F");
                            } else if ($errorType == 1) {
                                $objSheet->SetCellValue("F$rowCountData", $this->lang->line("data_missing"));
                                $objSheet->getStyle("F$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FFFF00");
                            } else if ($errorType == 2) {
                                $objSheet->SetCellValue("F$rowCountData", $this->lang->line("invalid_inventory_order"));
                                $objSheet->getStyle("F$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FD543B");
                            } else if ($errorType == 3) {
                                $objSheet->SetCellValue("F$rowCountData", $this->lang->line("insufficient_pieces") . ": " . ($errordata["remainingPieces"] + 0));
                                $objSheet->getStyle("F$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FFC000");
                            } else if ($errorType == 4) {
                                $objSheet->SetCellValue("F$rowCountData", $this->lang->line("invalid_container_number"));
                                $objSheet->getStyle("F$rowCountData")
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB("FD543B");
                            }
    
                            $rowCountData++;
                        }
    
                        $rowCountData = $rowCountData - 1;
                        $objSheet->getStyle("A1:F$rowCountData")->applyFromArray($styleArray);
    
                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(false)->setWidth(31);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "DispatchErrorReport_" . $month_name . '_' . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/DispatchErrorReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/DispatchErrorReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $e->getMessage(); // $this->lang->line("error_reports');
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function add()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "dispatch") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "add") {

                    $originid = $this->input->post("originid");
                    $containernumber = strtoupper(preg_replace('/\s+/', '', $this->input->post("containernumber")));
                    $productid = $this->input->post("productid");
                    $producttypeid = $this->input->post("producttypeid");
                    $warehouseid = $this->input->post("warehouseid");
                    $dispatchdate = $this->input->post("dispatchdate");
                    $uploadtypeid = $this->input->post("uploadtypeid");
                    $containerimageurl = $this->input->post("containerimageurl");
                    $sealnumber = strtoupper($this->input->post("sealnumber"));
                    $totalpiecesuploaded = $this->input->post("totalpiecesuploaded");
                    $totalvolumeuploaded = $this->input->post("totalvolumeuploaded");
                    $totalgrossvolumeuploaded = $this->input->post("totalgrossvolumeuploaded");
                    $shippinglineid = $this->input->post("shippinglineid");
                    $dispatchdata = $this->input->post("dispatchdata");

                    $isspecialuploaded = 0;
                    if ($uploadtypeid == 1) {
                        $isspecialuploaded = 1;
                    }

                    $getContainerCount = $this->Dispatch_model->get_container_count($containernumber, $originid);

                    if ($getContainerCount[0]->cnt == 0) {

                        $dispatchDataJson = json_decode($dispatchdata, true);
                        $isCorrectContainer = 0;
                        foreach ($dispatchDataJson as $dispatchdatacheck) {
                            $inventoryOrderDispatch = $dispatchdatacheck["containerNumber"];

                            if ($inventoryOrderDispatch == $containernumber) {
                                $isCorrectContainer = $isCorrectContainer + 1;
                            }
                        }

                        if (count($dispatchDataJson) == $isCorrectContainer) {

                            if (($productid == 4 || $productid == 1 || $productid == 2) && ($producttypeid == 1 || $producttypeid == 3)) {

                                $dataDispatch = array(
                                    "container_number" => $containernumber, "warehouse_id" => $warehouseid,
                                    "shipping_line" => $shippinglineid, "product_id" => $productid,
                                    "product_type_id" => $producttypeid, "dispatch_date" => $dispatchdate,
                                    "seal_number" => $sealnumber, "container_pic_url" => $containerimageurl,
                                    "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    "isclosed" => 0, "closedby" => 0, "isexport" => 0, "exportedby" => 0,
                                    "dispatched_timestamp" => 0, "isduplicatedispatched" => 0, "temp_container_number" => "",
                                    "iscontainer_available" => 1, "is_special_uploaded" => $isspecialuploaded, "origin_id" => $originid,
                                    "total_volume" => $totalvolumeuploaded, "total_gross_volume" => $totalgrossvolumeuploaded, 
                                    "total_pieces" => $totalpiecesuploaded,
                                );

                                $insertDispatch = $this->Dispatch_model->add_dispatch($dataDispatch);

                                if ($insertDispatch > 0) {
                                    $dataDispatchTracking = array(
                                        "dispatch_id" => $insertDispatch, "user_id" => $session['user_id'],
                                        "isclosed" => 0, "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    );

                                    $insertDispatchTracking = $this->Dispatch_model->add_dispatch_tracking($dataDispatchTracking);

                                    $dataDispatchData = array();
                                    foreach ($dispatchDataJson as $dispatch) {

                                        $getReceptionData = $this->Reception_model->get_reception_data_for_dispatch_square_blocks($dispatch["inventoryOrder"], $dispatch["length"], $dispatch["width"], $dispatch["thickness"], $originid);
                                        $noOfDispatchPieces = $dispatch["noOfPieces"];

                                        if (count($getReceptionData) > 0) {
                                            foreach ($getReceptionData as $receptiondata) {

                                                $dispatchPiece = 0;
                                                $remainingPieces = $receptiondata->remaining_stock_count;
                                                $containerNumbers = $receptiondata->container_number;

                                                if (($remainingPieces - $noOfDispatchPieces) >= 0) {
                                                    $remainingPieces = $remainingPieces - $noOfDispatchPieces;
                                                    $dispatchPiece = $noOfDispatchPieces;
                                                } else {
                                                    $noOfDispatchPieces = $noOfDispatchPieces - $remainingPieces;
                                                    $dispatchPiece = $noOfDispatchPieces;
                                                    $remainingPieces = 0;
                                                }

                                                if ($containerNumbers == "") {
                                                    $containerNumbers = $dispatch["containerNumber"];
                                                } else {
                                                    $containerNumbers = $containerNumbers . ", " . $dispatch["containerNumber"];
                                                }

                                                $isDispatch = 0;
                                                if ($remainingPieces == 0) {
                                                    $isDispatch = 1;
                                                }

                                                $updateReceptionData = array(
                                                    "container_number" => $containerNumbers, "remaining_stock_count" => $remainingPieces,
                                                    "isdispatch" => $isDispatch, "updatedby" => $session['user_id']
                                                );

                                                $updateReception = $this->Reception_model->update_reception_data_for_dispatch(
                                                    $receptiondata->reception_data_id,
                                                    $receptiondata->reception_id,
                                                    $dispatch["inventoryOrder"],
                                                    $updateReceptionData
                                                );

                                                $dataDispatchData[] = array(
                                                    "dispatch_id" => $insertDispatch, "reception_data_id" => $receptiondata->reception_data_id,
                                                    "reception_id" => $receptiondata->reception_id, "cbm_bought" => 0, "cbm_export" => 0,
                                                    "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                                    "scanned_timestamp" => 0, "isduplicatescanned" => 0, "is_special" => $isspecialuploaded,
                                                    "dispatch_pieces" => $dispatchPiece, "createddate" => date('Y-m-d H:i:s'), "updateddate" => date('Y-m-d H:i:s'),
                                                );
                                            }
                                        }
                                    }

                                    if (count($dataDispatchData) > 0) {
                                        $insertDispatchData = $this->Dispatch_model->add_dispatch_data($dataDispatchData);

                                        if ($insertDispatchData) {
                                            $Return["error"] = "";
                                            $Return["result"] = $this->lang->line("data_added");
                                            $Return["redirect"] = false;
                                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    }
                                }
                            } else if ($producttypeid == 1 || $producttypeid == 3) {
                            } else if ($producttypeid == 2 || $producttypeid == 4) {

                                $dataDispatch = array(
                                    "container_number" => $containernumber, "warehouse_id" => $warehouseid,
                                    "shipping_line" => $shippinglineid, "product_id" => $productid,
                                    "product_type_id" => $producttypeid, "dispatch_date" => $dispatchdate,
                                    "seal_number" => $sealnumber, "container_pic_url" => $containerimageurl,
                                    "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    "isclosed" => 0, "closedby" => 0, "isexport" => 0, "exportedby" => 0,
                                    "dispatched_timestamp" => 0, "isduplicatedispatched" => 0, "temp_container_number" => "",
                                    "iscontainer_available" => 1, "is_special_uploaded" => $isspecialuploaded, "origin_id" => $originid,
                                    "total_volume" => $totalvolumeuploaded, "total_gross_volume" => $totalgrossvolumeuploaded, 
                                    "total_pieces" => $totalpiecesuploaded,
                                );

                                $insertDispatch = $this->Dispatch_model->add_dispatch($dataDispatch);

                                if ($insertDispatch > 0) {
                                    $dataDispatchTracking = array(
                                        "dispatch_id" => $insertDispatch, "user_id" => $session['user_id'],
                                        "isclosed" => 0, "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    );

                                    $insertDispatchTracking = $this->Dispatch_model->add_dispatch_tracking($dataDispatchTracking);

                                    $dataDispatchData = array();
                                    foreach ($dispatchDataJson as $dispatch) {

                                        $getReceptionData = $this->Reception_model->get_reception_data_for_dispatch($dispatch["inventoryOrder"], $dispatch["circumference"], $dispatch["length"], $originid);
                                        $noOfDispatchPieces = $dispatch["noOfPieces"];

                                        if (count($getReceptionData) > 0) {
                                            foreach ($getReceptionData as $receptiondata) {

                                                $dispatchPiece = 0;
                                                $remainingPieces = $receptiondata->remaining_stock_count;
                                                $containerNumbers = $receptiondata->container_number;

                                                if (($remainingPieces - $noOfDispatchPieces) >= 0) {
                                                    $remainingPieces = $remainingPieces - $noOfDispatchPieces;
                                                    $dispatchPiece = $noOfDispatchPieces;
                                                } else {
                                                    $noOfDispatchPieces = $noOfDispatchPieces - $remainingPieces;
                                                    $dispatchPiece = $noOfDispatchPieces;
                                                    $remainingPieces = 0;
                                                }

                                                if ($containerNumbers == "") {
                                                    $containerNumbers = $dispatch["containerNumber"];
                                                } else {
                                                    $containerNumbers = $containerNumbers . ", " . $dispatch["containerNumber"];
                                                }

                                                $isDispatch = 0;
                                                if ($remainingPieces == 0) {
                                                    $isDispatch = 1;
                                                }

                                                $updateReceptionData = array(
                                                    "container_number" => $containerNumbers, "remaining_stock_count" => $remainingPieces,
                                                    "isdispatch" => $isDispatch, "updatedby" => $session['user_id']
                                                );

                                                $updateReception = $this->Reception_model->update_reception_data_for_dispatch(
                                                    $receptiondata->reception_data_id,
                                                    $receptiondata->reception_id,
                                                    $dispatch["inventoryOrder"],
                                                    $updateReceptionData
                                                );

                                                $dataDispatchData[] = array(
                                                    "dispatch_id" => $insertDispatch, "reception_data_id" => $receptiondata->reception_data_id,
                                                    "reception_id" => $receptiondata->reception_id, "cbm_bought" => 0, "cbm_export" => 0,
                                                    "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                                    "scanned_timestamp" => 0, "isduplicatescanned" => 0, "is_special" => $isspecialuploaded,
                                                    "dispatch_pieces" => $dispatchPiece, "createddate" => date('Y-m-d H:i:s'), "updateddate" => date('Y-m-d H:i:s'),
                                                );
                                            }
                                        }
                                    }

                                    if (count($dataDispatchData) > 0) {
                                        $insertDispatchData = $this->Dispatch_model->add_dispatch_data($dataDispatchData);

                                        if ($insertDispatchData) {
                                            $Return["error"] = "";
                                            $Return["result"] = $this->lang->line("data_added");
                                            $Return["redirect"] = false;
                                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    }
                                }
                            }
                        } else {
                            $Return["error"] = $this->lang->line("error_invalid_container");
                            $Return["result"] = "";
                            $Return["redirect"] = false;
                            $Return["templateerror"] = false;
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return["error"] = $this->lang->line("exist_container_number");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
                        $Return["templateerror"] = false;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else if ($this->input->post("action_type") == "update") {

                    $originid = $this->input->post("origin_id");
                    $dispatchid = $this->input->post('dispatch_id');
                    $container_number = strtoupper(preg_replace('/\s+/', '', $this->input->post('container_number')));
                    $input_container_number = strtoupper(preg_replace('/\s+/', '', $this->input->post('input_container_number')));
                    $warehouse_id = $this->input->post("warehouse_id");
                    $shippingline_id = $this->input->post("shippingline_id");
                    $dispatched_date = $this->input->post("dispatched_date");
                    $containerimageurl = $this->input->post("containerimageurl");
                    $sealnumber = strtoupper($this->input->post("sealnumber"));

                    if ($input_container_number == $container_number) {

                        $dataDispatch = array(
                            "warehouse_id" => $warehouse_id, "shipping_line" => $shippingline_id,
                            "dispatch_date" => $dispatched_date, "updatedby" => $session['user_id'],
                            "seal_number" => $sealnumber, "container_pic_url" => $containerimageurl,
                        );

                        $updateDispatch = $this->Dispatch_model->update_dispatch($dispatchid, $container_number, $dataDispatch);
                        if ($updateDispatch == true) {
                            $Return['result'] = $this->lang->line("data_updated");
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return['error'] = $this->lang->line("error_updating");
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }

                    } else {

                        $getContainerCount = $this->Dispatch_model->get_container_count($input_container_number, $originid);

                        if ($getContainerCount[0]->cnt == 0) {

                            $dataDispatch = array(
                                "container_number" => $input_container_number, "warehouse_id" => $warehouse_id, "shipping_line" => $shippingline_id,
                                "dispatch_date" => $dispatched_date, "updatedby" => $session['user_id'],
                                "seal_number" => $sealnumber, "container_pic_url" => $containerimageurl,
                            );

                            $updateDispatch = $this->Dispatch_model->update_dispatch($dispatchid, $container_number, $dataDispatch);
                            if ($updateDispatch == true) {
                                $Return['result'] = $this->lang->line("data_updated");
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            } else {
                                $Return['error'] = $this->lang->line("error_updating");
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return["error"] = $this->lang->line("exist_container_number");
                            $Return["result"] = "";
                            $Return["redirect"] = false;
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else {
                    $Return['error'] = $this->lang->line("invalid_request");
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                redirect("/logout");
            }
        } else {
            $Return["error"] = $this->lang->line("invalid_request");
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_dispatch_action()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->get("type") == "downloaddispatch") {

                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $this->generate_dispatch_report($dispatchId,  $containerNumber);
            } else if ($this->input->get("type") == "viewdispatch") {
                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $getDispatchDetails = $this->Dispatch_model->get_dispatch_details($dispatchId, $containerNumber);
                $getWH = $this->Master_model->get_warehouse_by_origin($getDispatchDetails[0]->origin_id);
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($getDispatchDetails[0]->origin_id);

                if ($getDispatchDetails[0]->is_special_uploaded == 1) {
                    $getDispatchDetails[0]->is_special_uploaded = $this->lang->line("pieces");
                } else {
                    $getDispatchDetails[0]->is_special_uploaded = $this->lang->line("qr_code");
                }

                $data = array(
                    "pageheading" => $this->lang->line("dispatch_details"),
                    "pagetype" => "update",
                    "dispatchid" => $dispatchId,
                    "containernumber" => $containerNumber,
                    "warehouses" => $getWH,
                    "shippinglines" => $getShippingLines,
                    "dispatch_details" => $getDispatchDetails,
                    "dispatch_submit" => "dispatches/add"
                );
                $this->load->view("dispatches/dialog_view_dispatch", $data);
            } else if ($this->input->get('type') == "deletedispatchconfirmation") {

                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $getDispatchDetail = $this->Dispatch_model->get_dispatch_details($dispatchId, $containerNumber);

                if ($getDispatchDetail[0]->isexport == 0) {
                    $data = array(
                        "pageheading" => $this->lang->line("confirmation"),
                        "pagemessage" => $this->lang->line("delete_message"),
                        "inputid" => $this->input->get("did"),
                        "inputid1" => $this->input->get("cn"),
                        "actionurl" => "dispatches/dialog_dispatch_action",
                        "actiontype" => "deletedispatch",
                        "xin_table" => "#xin_table_dispatches",
                    );
                    $this->load->view('dialogs/dialog_confirmation', $data);
                } else {
                    $data = array(
                        "pageheading" => $this->lang->line("information"),
                        "pagemessage" => $this->lang->line("already_export_container"),
                        "messagetype" => "info",
                    );
                    $this->load->view("dialogs/dialog_message", $data);
                }
            } else if ($this->input->get('type') == "deletedispatch") {

                $dispatchId = $this->input->get("inputid");
                $containerNumber = $this->input->get("inputid1");

                $dispatchDelete = $this->Dispatch_model->delete_dispatch($dispatchId, $containerNumber, $session['user_id']);

                if ($dispatchDelete) {
                    $Return['result'] = $this->lang->line("data_deleted");
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line("error_deleting");
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_dispatch_report($dispatchId, $containerNumber)
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => ""
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getDispatchDetails = $this->Dispatch_model->get_dispatch_details($dispatchId, $containerNumber);
                $getDispatchDataDetails = $this->Dispatch_model->get_dispatch_data_details($dispatchId, $containerNumber);

                if (count($getDispatchDetails) == 1 && count($getDispatchDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle(strtoupper($containerNumber));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue("A2", $this->lang->line("container_number"));
                    $objSheet->SetCellValue("A3", $this->lang->line("shipping_line"));
                    $objSheet->SetCellValue("A4", $this->lang->line("product"));
                    $objSheet->SetCellValue("A5", $this->lang->line("dispatch_date"));
                    $objSheet->SetCellValue("A6", $this->lang->line("origin"));
                    $objSheet->SetCellValue("C2", $this->lang->line("warehouse"));
                    $objSheet->SetCellValue("C3", $this->lang->line("total_no_of_pieces"));
                    $objSheet->SetCellValue("C4", $this->lang->line("total_volume"));

                    $objSheet->SetCellValue("B2", $getDispatchDetails[0]->container_number);
                    $objSheet->SetCellValue("B3", $getDispatchDetails[0]->shipping_line);
                    $objSheet->SetCellValue("B4", $getDispatchDetails[0]->product_name . ' - ' . $this->lang->line($getDispatchDetails[0]->product_type_name));
                    $objSheet->SetCellValue("B5", $getDispatchDetails[0]->dispatch_date);
                    $objSheet->SetCellValue("B6", $getDispatchDetails[0]->origin);
                    $objSheet->SetCellValue("D2", $getDispatchDetails[0]->warehouse_name);

                    $objSheet->getStyle("A2:A6")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("C2:C6")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                    $objSheet->getColumnDimension("D")->setAutoSize(true);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle("A2:D6")->applyFromArray($styleArray);

                    $rowCount = 8;

                    if($getDispatchDetails[0]->origin_id == 1) {
                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(2, $getDispatchDetails[0]->origin_id);
                    } else if($getDispatchDetails[0]->origin_id == 4) {
                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($getDispatchDetails[0]->measurement_system_id, $getDispatchDetails[0]->origin_id);
                    } else {
                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(5, $getDispatchDetails[0]->origin_id);
                    }

                    $grossVolumeFormula = "";
                    $netVolumeFormula = "";
                    foreach ($getFormulae as $formula) {
                        
                        if($getDispatchDetails[0]->origin_id == 4) {
                            
                            if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                $grossVolumeFormula = str_replace(
                                    array('pow', 'round'),
                                    array("POWER", "ROUND"),
                                    $formula->calculation_formula
                                );
                            }
    
                            if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                $netVolumeFormula = str_replace(
                                    array('pow', 'round', '$ac', '$al'),
                                    array("POWER", "ROUND", $getDispatchDetails[0]->circ_allowance, $getDispatchDetails[0]->length_allowance),
                                    $formula->calculation_formula
                                );
                            }
                            
                        } ELSE {
                            if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                            $grossVolumeFormula = str_replace(
                                array('pow', 'round'),
                                array("POWER", "ROUND"),
                                $formula->calculation_formula
                            );
                            }
    
                            if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                $netVolumeFormula = str_replace(
                                    array('pow', 'round'),
                                    array("POWER", "ROUND"),
                                    $formula->calculation_formula
                                );
                            }
                        }

                        
                    }

                    if ($getDispatchDetails[0]->product_type_id == 1 || $getDispatchDetails[0]->product_type_id == 3) {
                        
                        $rowCount = 10;
                        
                        $objSheet->SetCellValue("A$rowCount", $this->lang->line("length"));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line("width"));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line("thickness"));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line("pieces"));
                        $objSheet->SetCellValue("E$rowCount", $this->lang->line("volume_pie"));
                        $objSheet->SetCellValue("F$rowCount", $this->lang->line("length_export"));
                        $objSheet->SetCellValue("G$rowCount", $this->lang->line("width_export"));
                        $objSheet->SetCellValue("H$rowCount", $this->lang->line("thickness_export"));
                        $objSheet->SetCellValue("I$rowCount", $this->lang->line("text_volume"));
                        $objSheet->SetCellValue("J$rowCount", $this->lang->line("grade"));
                        $objSheet->SetCellValue("K$rowCount", $this->lang->line("inventory_order"));
                        
                        $objSheet->getStyle("A$rowCount:K$rowCount")->getFont()->setBold(true);
                        $objSheet->setAutoFilter("A$rowCount:K$rowCount");
                        $objSheet->getStyle("A$rowCount:K$rowCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle("A$rowCount:K$rowCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("add8e6");
                            
                        $rowCountData = 11;
                        
                        $textGrade1 = '"'.$this->lang->line('grade1').'"';
                        $textGrade2 = '"'.$this->lang->line('grade2').'"';
                        $textGrade3 = '"'.$this->lang->line('grade3').'"';
                        
                        foreach ($getDispatchDataDetails as $dispatchdata) {
                            
                            $objSheet->SetCellValue("A$rowCountData", ($dispatchdata->length_bought + 0));
                            $objSheet->SetCellValue("B$rowCountData", ($dispatchdata->width_bought + 0));
                            $objSheet->SetCellValue("C$rowCountData", ($dispatchdata->thickness_bought + 0));
                            $objSheet->SetCellValue("D$rowCountData", ($dispatchdata->dispatch_pieces + 0));
                            $objSheet->SetCellValue("E$rowCountData", "=ROUND(A$rowCountData * B$rowCountData * C$rowCountData / 12 ,2)");
                            $objSheet->SetCellValue("F$rowCountData", "=A$rowCountData * 0.3");
                            $objSheet->SetCellValue("G$rowCountData", "=TRUNC(B$rowCountData * 2.54, 0)");
                            $objSheet->SetCellValue("H$rowCountData", "=ROUND(C$rowCountData * 2.54, 0)");
                            $objSheet->SetCellValue("I$rowCountData", "=ROUND(F$rowCountData * G$rowCountData * H$rowCountData / 10000, 3)");
                            $objSheet->SetCellValue("J$rowCountData", "=IF(G$rowCountData<15, ".$textGrade1." ,IF(H$rowCountData<15, ".$textGrade1." ,IF(G$rowCountData>19.9, ".$textGrade3." ,IF(H$rowCountData>19.9, ".$textGrade3." ,".$textGrade2."))))");
                            $objSheet->SetCellValue("K$rowCountData", $dispatchdata->salvoconducto);
                            $objSheet->getStyle("K$rowCountData")->getNumberFormat()->setFormatCode('0');
                            
                            $rowCountData++;
                        }
                        
                        $objSheet->SetCellValue("D3", "=SUM(D$rowCount:D$rowCountData)");
                        $objSheet->SetCellValue("D4", "=SUM(I$rowCount:I$rowCountData)");
                        
                        $rowCountData = $rowCountData - 1;
                        
                        $objSheet->mergeCells('F2:H2');
                        $objSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                        $objSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
                        $objSheet->SetCellValue('G3', $this->lang->line("text_volume"));
                        $objSheet->SetCellValue('H3', $this->lang->line("ages"));
    
                        $objSheet->SetCellValue('F4', $this->lang->line("grade1"));
                        $objSheet->SetCellValue('F5', $this->lang->line("grade2"));
                        $objSheet->SetCellValue('F6', $this->lang->line("grade3"));
                        $objSheet->SetCellValue('F7', $this->lang->line("total"));
                        
                        $objSheet->SetCellValue('G4', "=SUMIF(J11:J$rowCountData,F4,I11:I$rowCountData)");
                        $objSheet->SetCellValue('G5', "=SUMIF(J11:J$rowCountData,F5,I11:I$rowCountData)");
                        $objSheet->SetCellValue('G6', "=SUMIF(J11:J$rowCountData,F6,I11:I$rowCountData)");
                        $objSheet->SetCellValue('G7', "=SUM(G4:G6)");
                        
                        $objSheet->SetCellValue('H4', "=G4/G7");
                        $objSheet->SetCellValue('H5', "=G5/G7");
                        $objSheet->SetCellValue('H6', "=G6/G7");
                        $objSheet->SetCellValue('H7', "=SUM(H4:H6)");
                        
                        $objSheet->getStyle('H4:H7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        
                        $objSheet->getStyle('H7')->getFont()->setBold(true);
                        
                        $BStyle = array(
                        'borders' => array(
                            'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                        );
                        $objSheet->getStyle('F2:H7')->applyFromArray($styleArray);
                        $objSheet->getStyle('F2:H7')->applyFromArray($BStyle);
                        unset($BStyle);

                        
                        $objSheet->getStyle("A$rowCount:K$rowCountData")->applyFromArray($styleArray);
                        
                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                        $objSheet->getColumnDimension("G")->setAutoSize(true);
                        $objSheet->getColumnDimension("H")->setAutoSize(true);
                        $objSheet->getColumnDimension("I")->setAutoSize(true);
                        $objSheet->getColumnDimension("J")->setAutoSize(true);
                        $objSheet->getColumnDimension("K")->setAutoSize(true);
                        
                    } else if ($getDispatchDetails[0]->product_type_id == 2 || $getDispatchDetails[0]->product_type_id == 4) {

                        $objSheet->SetCellValue("A$rowCount", $this->lang->line("circumference"));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line("length"));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line("pieces"));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line("inventory_order"));
                        $objSheet->SetCellValue("E$rowCount", $this->lang->line("gross_volume"));
                        $objSheet->SetCellValue("F$rowCount", $this->lang->line("net_volume"));

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:F$rowCount");

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("add8e6");

                        $rowCountData = 9;
                        foreach ($getDispatchDataDetails as $dispatchdata) {
                            
                            $grossVolumeFormulae = str_replace(
                                array('$l', '$c', '$pcs', 'truncate'),
                                array("B$rowCountData", "A$rowCountData", "C$rowCountData", "TRUNC"),
                                $grossVolumeFormula
                            );
    
                            $netVolumeFormulae = str_replace(
                                array('$l', '$c', '$pcs', 'truncate'),
                                array("B$rowCountData", "A$rowCountData", "C$rowCountData", "TRUNC"),
                                $netVolumeFormula
                            );

                            $objSheet->SetCellValue("A$rowCountData", ($dispatchdata->circumference_bought + 0));
                            $objSheet->SetCellValue("B$rowCountData", ($dispatchdata->length_bought + 0));
                            $objSheet->SetCellValue("C$rowCountData", ($dispatchdata->dispatch_pieces + 0));
                            $objSheet->SetCellValue("D$rowCountData", $dispatchdata->salvoconducto);
                            
                            if($getDispatchDetails[0]->measurement_system_id == 13 || $getDispatchDetails[0]->measurement_system_id == 14) {
                                $objSheet->SetCellValue("E$rowCountData", "=$grossVolumeFormulae");
                                $objSheet->SetCellValue("F$rowCountData", "=$netVolumeFormulae");
                            } else {
                                $objSheet->SetCellValue("E$rowCountData", "=$grossVolumeFormulae*C$rowCountData");
                                $objSheet->SetCellValue("F$rowCountData", "=$netVolumeFormulae*C$rowCountData");
                            }

                            $rowCountData++;
                        }

                        $objSheet->SetCellValue("D3", "=SUM(C$rowCount:C$rowCountData)");
                        
                        if($getDispatchDetails[0]->rounding_factor > 0) {
                            $roundingFactorVal = $getDispatchDetails[0]->rounding_factor;
                            $objSheet->SetCellValue("D4", "=ROUND(SUM(F$rowCount:F$rowCountData), $roundingFactorVal)");
                        } else {
                            $objSheet->SetCellValue("D4", "=SUM(F$rowCount:F$rowCountData)");
                        }
                        
                        $rowCountData = $rowCountData - 1;

                        $objSheet->getStyle("A$rowCount:F$rowCountData")->applyFromArray($styleArray);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "DispatchReport_" . $containerNumber . "_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/DispatchReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/DispatchReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $e->getMessage(); // $this->lang->line("error_reports');
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/DispatchReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/DispatchErrorReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function truncate($val, $f = "0")
    {
        if (($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    public function debug_to_console($data)
    {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}