<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Receptions extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_model");
        $this->load->model("Master_model");
        $this->load->model("Reception_model");
        $this->load->model("Settings_model");
        $this->load->library('excel');
    }

    public function output($Return = array())
    {
        /*Set response header*/
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        /*Final JSON response*/
        exit(json_encode($Return));
    }

    public function index()
    {
        $data['title'] = $this->lang->line('reception_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }
        $data['path_url'] = 'cgr_receptions';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("receptions/reception_list", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function reception_list()
    {
        $data['title'] =  $this->lang->line('reception_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            $this->load->view("receptions/reception_list", $data);
        } else {
            redirect("/logout");
        }

        $draw = intval($this->input->get("draw"));
        $originid = intval($this->input->get("originid"));

        if ($originid == 0) {
            $receptions = $this->Reception_model->all_receptions();
        } else {
            $receptions = $this->Reception_model->all_receptions_origin($originid);
        }

        $data = array();

        foreach ($receptions as $r) {
            $editReception = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_excel') . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloadreception" data-toggle="modal" data-target=".download-modal-data" data-reception_id="' . $r->reception_id . '" data-inventory_order="' . $r->salvoconducto . '"><span class="fas fa-download"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewreception" data-toggle="modal" data-target=".view-modal-data" data-reception_id="' . $r->reception_id . '" data-inventory_order="' . $r->salvoconducto . '"><span class="fas fa-eye"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletereception" data-toggle="modal" data-target=".delete-modal-data" data-reception_id="' . $r->reception_id . '" data-inventory_order="' . $r->salvoconducto . '"><span class="fas fa-trash"></span></button></span>';

            $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

            $data[] = array(
                $editReception,
                $r->salvoconducto,
                $r->supplier_name,
                $product,
                $r->received_date,
                $r->warehouse_name,
                ($r->totalvolume + 0),
                $r->origin,
                ucwords(strtolower($r->uploadedby)),
            );
        }

        $output = array(
            "draw" => $draw,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function dialog_reception_add()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {
            if ($this->input->get('type') == "addreception") {
                $data = array(
                    'pageheading' => $this->lang->line('add_reception'),
                    'pagetype' => "add",
                    'farmid' => 0,
                    'inventory_order' => "",
                );
            }
            $this->load->view('receptions/dialog_add_reception', $data);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function fetch_suppliers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getSuppliers = $this->Contract_model->get_suppliers_by_origin($this->input->get('originid'));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . $supplier->supplier_name . "</option>";
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_product_by_supplier_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    $getProducts = $this->Reception_model->get_supplier_product_byorigin($this->input->get('originid'), $this->input->get('supplierid'));
                    foreach ($getProducts as $products) {
                        $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
                    }
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_product_type_by_supplier()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    if ($this->input->get('productid') > 0) {
                        $getProductTypes = $this->Reception_model->get_supplier_product_type_byorigin($this->input->get('supplierid'), $this->input->get('productid'));
                        foreach ($getProductTypes as $producttype) {
                            $productTypeName = $this->lang->line($producttype->product_type_name);
                            $result = $result . "<option value='" . $producttype->type_id . "'>" . $productTypeName . "</option>";
                        }
                    }
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_warehouse_by_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getWH = $this->Master_model->get_warehouse_by_origin($this->input->get('originid'));
                foreach ($getWH as $warehouse) {
                    $result = $result . "<option value='" . $warehouse->whid . "'>" . $warehouse->warehouse_name . "</option>";
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_measurement_system()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('producttypeid') > 0) {

                $getMeasurementSystem = $this->Reception_model->get_measurement_system($this->input->get('producttypeid'), $this->input->get('originid'));
                foreach ($getMeasurementSystem as $measurementsystem) {
                    $measurmentSystem = $this->lang->line($measurementsystem->measurement_name);
                    $result = $result . "<option value='" . $measurementsystem->measurement_id . "'>" . $measurmentSystem . "</option>";
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function load_reception_template()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                $inventoryOrderPost = $this->input->post("inventoryOrder");
                $originId = $this->input->post("originId");
                $productTypeId = $this->input->post("productTypeId");
                $purchaseUnit = $this->input->post("measurementSystemId");
                $uploadType = $this->input->post("uploadType");

                $getInventoryOrderCount = $this->Reception_model->get_inventory_order_count($inventoryOrderPost, $originId);

                if ($getInventoryOrderCount[0]->cnt == 0) {
                    if ($_FILES['fileReceptionExcel']['size'] > 0) {
                        $config['upload_path'] = FCPATH . 'reports/';
                        $config['allowed_types'] = 'xlsx';
                        $config['remove_spaces'] = TRUE;
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('fileReceptionExcel')) {
                            $Return['error'] = $this->lang->line('error_excel_upload');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $data = array('upload_data' => $this->upload->data());
                            $inputFileName = FCPATH . 'reports/' . $data['upload_data']['file_name'];
                            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                            $objPHPExcel = $objReader->load($inputFileName);
                            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            $arrayCount = count($allDataInSheet);

                            if ($arrayCount > 0) {

                                if ($productTypeId == 1 || $productTypeId == 3) {

                                    $createArray = array('INVENTORY ORDER', 'SCANNED CODE', 'LENGTH', 'WIDTH', 'THICKNESS');
                                    $makeArray = array('INVENTORYORDER' => 'INVENTORY ORDER', 'SCANNEDCODE' => 'SCANNED CODE', 'LENGTH' => 'LENGTH', 'WIDTH' => 'WIDTH', 'THICKNESS' => 'THICKNESS');
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
                                    $receptionData = array();
                                    $totalPieces = 0;
                                    $totalNetVolume = 0;
                                    $totalVolumePie = 0;
                                    if (empty($data)) {

                                        $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($purchaseUnit, $originId);
                                        $getCalulationType = $this->Master_model->get_calulation_type($purchaseUnit);

                                        $calculationType = $getCalulationType[0]->calculation_type;

                                        for ($i = 2; $i <= $arrayCount; $i++) {
                                            $inventoryOrder = $SheetDataKey['INVENTORYORDER'];
                                            $scannedCode = $SheetDataKey['SCANNEDCODE'];
                                            $length = $SheetDataKey['LENGTH'];
                                            $width = $SheetDataKey['WIDTH'];
                                            $thickness = $SheetDataKey['THICKNESS'];

                                            $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $scannedCode = filter_var(trim($allDataInSheet[$i][$scannedCode]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $widthVal = filter_var(trim($allDataInSheet[$i][$width]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $thicknessVal = filter_var(trim($allDataInSheet[$i][$thickness]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                            if (($inventoryOrderVal != null && $inventoryOrderVal != "") &&
                                                ($scannedCode != null && $scannedCode != "") &&
                                                ($lengthVal != null && $lengthVal != "" && $lengthVal > 0) &&
                                                ($widthVal != null && $widthVal != "" && $widthVal > 0) &&
                                                ($thicknessVal != null && $thicknessVal != "" && $thicknessVal > 0)
                                            ) {

                                                $getValidScannedCode = $this->Farm_model->get_scanned_code_count($scannedCode, $originId);
                                                $getExistingScannedCode = $this->Farm_model->get_farm_scanned_code_count($scannedCode, $originId);

                                                if ($getValidScannedCode[0]->cnt == 1) {
                                                    if ($getExistingScannedCode[0]->cnt == 0) {
                                                        if ($inventoryOrderVal == $inventoryOrderPost) {

                                                            $netVolume = 0;
                                                            $volumePie = 0;
                                                            $grossVolume = 0;
                                                            $widthExport = 0;
                                                            $thicknessExport = 0;
                                                            $lengthExport = 0;
                                                            $grade = 0;

                                                            if (count($getFormulae) > 0) {
                                                                foreach ($getFormulae as $formula) {
                                                                    $strFormula = str_replace(
                                                                        array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        $formula->formula_context
                                                                    );
                                                                    $strFormula = "return (" . $strFormula . ");";

                                                                    if ($purchaseUnit == 1) {
                                                                        if ($formula->type == "volumepie") {
                                                                            $volumePie = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "grossvolume") {
                                                                            $grossVolume = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "widthexport") {
                                                                            $widthExport = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "thicknessexport") {
                                                                            $thicknessExport = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "lengthexport") {
                                                                            $lengthExport = eval($strFormula);
                                                                        }
                                                                    } else if ($purchaseUnit == 2) {
                                                                        $widthExport = $widthVal;
                                                                        $thicknessExport = $thicknessVal;
                                                                        $lengthExport = $lengthVal;
                                                                    }

                                                                    if ($formula->type == "grade") {
                                                                        $grade = eval($strFormula);
                                                                        // $grade = $this->calculateFormula(array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        // array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        // $formula->formula_context);
                                                                    }

                                                                    if ($formula->type == "netvolume") {
                                                                        $netVolume = eval($strFormula);
                                                                        // $netVolume = $this->calculateFormula(array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        // array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        // $formula->formula_context);
                                                                    }
                                                                }

                                                                if ($purchaseUnit == 2) {

                                                                    $lengthVal = 0;
                                                                    $widthVal = 0;
                                                                    $thicknessVal = 0;
                                                                }

                                                                $receptionData[] = array(
                                                                    'noOfPieces' => 1,
                                                                    'length' => sprintf('%0.1f', ($lengthVal + 0)),
                                                                    'width' => ($widthVal + 0),
                                                                    'thickness' => ($thicknessVal + 0),
                                                                    'lengthExport' => sprintf('%0.1f', ($lengthExport + 0)),
                                                                    'widthExport' => ($widthExport + 0),
                                                                    'thicknessExport' => ($thicknessExport + 0),
                                                                    'volumePie' => $volumePie,
                                                                    'grossVolume' => sprintf('%0.3f', $grossVolume),
                                                                    'grade' => $grade,
                                                                    'netVolume' => sprintf('%0.3f', $netVolume),
                                                                    'scannedCode' => $scannedCode,
                                                                );

                                                                $totalVolumePie = $totalVolumePie + $volumePie;
                                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                                $totalPieces = $totalPieces + 1;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (count($receptionData) == 0 || $totalPieces == 0) {
                                            $Return['warning'] = $this->lang->line('error_nodata_excel');
                                            $Return['error'] = "";
                                            $Return['result'] = "";
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        } else {

                                            if ($calculationType == "PIE") {
                                                $totalVolume = $totalVolumePie;
                                            } else if ($calculationType == "CBM") {
                                                $totalVolume = $totalNetVolume;
                                            }

                                            $dataUploaded = array(
                                                "receptionData" => $receptionData,
                                                "totalPieces" => $totalPieces,
                                                "purchaseUnit" => $purchaseUnit,
                                                "totalVolume" => sprintf('%0.3f', $totalVolume),
                                            );

                                            $Return['error'] = "";
                                            $Return['result'] = $dataUploaded;
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    } else {
                                        $Return['error'] = $this->lang->line('error_excel_template');
                                        $Return['result'] = "";
                                        $Return['redirect'] = false;
                                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else if ($productTypeId == 2 || $productTypeId == 4) {

                                    $createArray = array('INVENTORY ORDER', 'NO OF PIECES', 'CIRCUMFERENCE', 'LENGTH');
                                    $makeArray = array('INVENTORYORDER' => 'INVENTORY ORDER', 'NOOFPIECES' => 'NO OF PIECES', 'CIRCUMFERENCE' => 'CIRCUMFERENCE', 'LENGTH' => 'LENGTH');
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
                                    $receptionData = array();
                                    $totalPieces = 0;
                                    $totalNetVolume = 0;
                                    if (empty($data)) {

                                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($purchaseUnit, $originId);

                                        for ($i = 2; $i <= $arrayCount; $i++) {
                                            $inventoryOrder = $SheetDataKey['INVENTORYORDER'];
                                            $noOfPieces = $SheetDataKey['NOOFPIECES'];
                                            $circumference = $SheetDataKey['CIRCUMFERENCE'];
                                            $length = $SheetDataKey['LENGTH'];

                                            $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $noOfPiecesVal = filter_var(trim($allDataInSheet[$i][$noOfPieces]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $circumferenceVal = filter_var(trim($allDataInSheet[$i][$circumference]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                            if (($inventoryOrderVal != null && $inventoryOrderVal != "") &&
                                                ($noOfPiecesVal != null && $noOfPiecesVal != "" && $noOfPiecesVal > 0) &&
                                                ($circumferenceVal != null && $circumferenceVal != "" && $circumferenceVal > 0) &&
                                                ($lengthVal != null && $lengthVal != "" && $lengthVal > 0)
                                            ) {
                                                if ($inventoryOrderVal == $inventoryOrderPost) {

                                                    $netVolume = 0;
                                                    $grossVolume = 0;
                                                    if (count($getFormulae) > 0) {
                                                        foreach ($getFormulae as $formula) {
                                                            $strFormula = str_replace(array('$l', '$c', 'truncate'), array($lengthVal, $circumferenceVal, '$this->truncate'), $formula->calculation_formula);
                                                            $strFormula = "return (" . $strFormula . ");";

                                                            if ($uploadType == 1) {
                                                                if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                                                    $grossVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPiecesVal;
                                                                }

                                                                if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                                                    $netVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPiecesVal;
                                                                }
                                                            } else {
                                                                if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                                                    $grossVolume = sprintf('%0.3f', eval($strFormula));
                                                                }

                                                                if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                                                    $netVolume = sprintf('%0.3f', eval($strFormula));
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    if ($uploadType == 1) {
                                                        $totalPieces = $totalPieces + $noOfPiecesVal;
                                                    } else {
                                                        $totalPieces = $totalPieces + 1;
                                                    }

                                                    $isCircumferenceExists = false;
                                                    if ($uploadType == 1) {
                                                        if (count($receptionData) > 0) {
                                                            foreach ($receptionData as &$value) {
                                                                if ($value['circumference'] == ($circumferenceVal + 0) && $value['length'] == ($lengthVal + 0)) {
                                                                    $isCircumferenceExists = true;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if ($isCircumferenceExists == true) {
                                                        foreach ($receptionData as &$value) {
                                                            if (
                                                                $value['circumference'] == ($circumferenceVal + 0)
                                                                && $value['length'] == ($lengthVal + 0)
                                                            ) {
                                                                $value['noOfPieces'] = $value['noOfPieces'] + ($noOfPiecesVal + 0);
                                                                $value['netVolume'] = sprintf('%0.3f', $value['netVolume'] + sprintf('%0.3f', $netVolume));
                                                                $value['grossVolume'] = sprintf('%0.3f', $value['grossVolume'] + sprintf('%0.3f', $grossVolume));
                                                            }
                                                        }
                                                    } else {
                                                        $receptionData[] = array(
                                                            'noOfPieces' => ($noOfPiecesVal + 0),
                                                            'circumference' => ($circumferenceVal + 0),
                                                            'length' => ($lengthVal + 0),
                                                            'netVolume' => sprintf('%0.3f', $netVolume),
                                                            'grossVolume' => sprintf('%0.3f', $grossVolume),
                                                            'inventoryOrder' => $inventoryOrderVal,
                                                        );
                                                    }
                                                }
                                            }
                                        }

                                        if (count($receptionData) == 0 || $totalPieces == 0) {
                                            $Return['warning'] = $this->lang->line('error_nodata_excel');
                                            $Return['error'] = "";
                                            $Return['result'] = "";
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        } else {

                                            $dataUploaded = array(
                                                "receptionData" => $receptionData,
                                                "totalPieces" => $totalPieces,
                                                "purchaseUnit" => $purchaseUnit,
                                                "totalVolume" => sprintf('%0.3f', $totalNetVolume),
                                            );

                                            $Return['error'] = "";
                                            $Return['result'] = $dataUploaded;
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    } else {
                                        $Return['error'] = $this->lang->line('error_excel_template');
                                        $Return['result'] = "";
                                        $Return['redirect'] = false;
                                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else {
                                    $Return['error'] = $this->lang->line('error_excel_template');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['warning'] = $this->lang->line('error_nodata_excel');
                                $Return['error'] = "";
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_loadtemplate');
                        $Return['result'] = "";
                        $Return['redirect'] = false;
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return['error'] = $this->lang->line('exist_inventory_order');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return['error'] = "";
                $Return['result'] = "";
                $Return['redirect'] = true;
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return['error'] = $this->lang->line('error_loadtemplate');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function add()
    {
        $Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        
        if ($this->input->post('add_type') == 'reception') {

            if (!empty($session)) {

                if ($this->input->post('action_type') == 'add') {

                    $originid = $this->input->post('originid');
                    $supplierid = $this->input->post('supplierid');
                    $suppliername = $this->input->post('suppliername');
                    $productid = $this->input->post('productid');
                    $producttypeid = $this->input->post('producttypeid');
                    $inventoryorder = strtoupper($this->input->post('inventoryorder'));
                    $warehouseid = $this->input->post('warehouseid');
                    $receptiondate = $this->input->post('receiveddate');
                    $measurementsystemid = $this->input->post('measurementsystemid');
                    $receptiondata = $this->input->post('receptiondata');
                    $uploadtype = $this->input->post("uploadtypeid");
                    $totalpiecesuploaded = $this->input->post("totalpiecesuploaded");
                    $totalvolumeuploaded = $this->input->post("totalvolumeuploaded");

                    $isspecialuploaded = 0;
                    if ($uploadtype == 1) {
                        $isspecialuploaded = 1;
                    }

                    $supplierSplit = explode(" --- ", $suppliername);
                    $suppliercode = $supplierSplit[1];

                    $getInventoryOrderCount = $this->Reception_model->get_inventory_order_count($inventoryorder, $originid);

                    if ($getInventoryOrderCount[0]->cnt == 0) {

                        $receptionDataJson = json_decode($receptiondata, true);

                        $isCorrectInventory = 0;
                        foreach ($receptionDataJson as $receptiondatacheck) {
                            $inventoryOrderReception = $receptiondatacheck["inventoryOrder"];

                            if ($inventoryOrderReception == $inventoryorder) {
                                $isCorrectInventory = $isCorrectInventory + 1;
                            }
                        }

                        if (count($receptionDataJson) > 0 == $isCorrectInventory) {
                            if ($producttypeid == 1 || $producttypeid == 3) {
                            } else if ($producttypeid == 2 || $producttypeid == 4) {

                                $dataReception = array(
                                    "warehouse_id" => $warehouseid, "supplier_id" => $supplierid,
                                    "supplier_code" => $suppliercode, "supplier_product_id" => $productid,
                                    "supplier_product_typeid" => $producttypeid, "measurementsystem_id" => $measurementsystemid,
                                    "received_date" => $receptiondate, "salvoconducto" => $inventoryorder,
                                    "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    "isclosed" => 0, "closedby" => 0, "captured_timestamp" => 0, "isduplicatecaptured" => 0,
                                    "is_special_uploaded" => $isspecialuploaded, "origin_id" => $originid,
                                    "total_volume" => $totalvolumeuploaded, "total_pieces" => $totalpiecesuploaded,
                                );

                                $insertReception = $this->Reception_model->add_reception($dataReception);

                                if ($insertReception > 0) {

                                    $dataReceptionTracking = array(
                                        "reception_id" => $insertReception, "user_id" => $session['user_id'],
                                        "isclosed" => 0, "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                    );

                                    $insertReceptionTracking = $this->Reception_model->add_reception_tracking($dataReceptionTracking);


                                    $dataReceptionData = array();
                                    foreach ($receptionDataJson as $reception) {

                                        $circumference = $reception["circumference"];
                                        $noOfPieces = $reception["noOfPieces"];
                                        $length = $reception["length"];
                                        $netVolume = $reception["netVolume"];
                                        $grossVolume = $reception["grossVolume"];

                                        $remainingstockcount = 0;
                                        if ($isspecialuploaded == 1) {
                                            $remainingstockcount = $noOfPieces;
                                        }

                                        if ($noOfPieces > 0) {
                                            $dataReceptionData[] = array(
                                                "reception_id" => $insertReception, "salvoconducto" => $inventoryorder,
                                                "scanned_code" => $noOfPieces, "length_bought" => $length,
                                                "width_bought" => 0, "thickness_bought" => 0,
                                                "circumference_bought" => $circumference, "volumepie_bought" => 0,
                                                "cbm_bought" => $grossVolume, "length_export" => 0, "width_export" => 0,
                                                "thickness_export" => 0, "cbm_export" => $netVolume, "grade" => 0,
                                                "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                                "isdispatch" => 0, "scanned_timestamp" => 0, "isduplicatescanned" => 0, "is_special" => $isspecialuploaded,
                                                "createddate" => date('Y-m-d H:i:s'), "updateddate" => date('Y-m-d H:i:s'), "remaining_stock_count" => $remainingstockcount,
                                            );
                                        }
                                    }

                                    if (count($dataReceptionData) > 0) {
                                        $insertReceptionData = $this->Reception_model->add_reception_data($dataReceptionData);

                                        if ($insertReceptionData) {
                                            $Return['error'] = "";
                                            $Return['result'] = $this->lang->line('data_added');
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    } else {
                                        $Return['error'] = $this->lang->line('error_adding');
                                        $Return['result'] = "";
                                        $Return['redirect'] = false;
                                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else {
                                    $Return['error'] = $this->lang->line('error_adding');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_inventory');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return['error'] = $this->lang->line('exist_inventory_order');
                        $Return['result'] = "";
                        $Return['redirect'] = false;
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else if ($this->input->post('action_type') == 'update') {

                    $originid = $this->input->post('origin_id');
                    $receptionid = $this->input->post('reception_id');
                    $inventory_order = strtoupper(preg_replace('/\s+/', '', $this->input->post('inventory_order')));
                    $input_inventory_order = strtoupper(preg_replace('/\s+/', '', $this->input->post('input_inventory_order')));
                    $warehouseid = $this->input->post('warehouse_id');
                    $receiveddate = $this->input->post('received_date');

                    if ($input_inventory_order == $inventory_order) {

                        $dataReception = array(
                            "warehouse_id" => $warehouseid, "received_date" => $receiveddate, "updatedby" => $session['user_id']
                        );

                        $updateReception = $this->Reception_model->update_reception($receptionid, $inventory_order, $dataReception);

                        if ($updateReception == true) {
                            $Return['result'] = $this->lang->line('data_updated');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return['error'] = $this->lang->line('error_updating');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {

                        $getInventoryOrderCount = $this->Reception_model->get_inventory_order_count($input_inventory_order, $originid);

                        if ($getInventoryOrderCount[0]->cnt == 0) {

                            $dataReception = array(
                                "warehouse_id" => $warehouseid, "salvoconducto" => $input_inventory_order,
                                "received_date" => $receiveddate, "updatedby" => $session['user_id']
                            );

                            $updateReception = $this->Reception_model->update_reception($receptionid, $inventory_order, $dataReception);

                            if ($updateReception == true) {

                                $dataReceptionData = array(
                                    "salvoconducto" => $input_inventory_order, "updatedby" => $session['user_id']
                                );

                                $updateReceptionData = $this->Reception_model->update_reception_data($receptionid, $inventory_order, $dataReceptionData);

                                if ($updateReceptionData == true) {
                                    $Return['result'] = $this->lang->line('data_updated');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return['error'] = $this->lang->line('error_updating');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['error'] = $this->lang->line('error_updating');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return['error'] = $this->lang->line('exist_inventory_order');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else {
                    $Return['error'] = $this->lang->line('invalid_request');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                redirect("/logout");
            }
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_reception_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "downloadreception") {

                $receptionId = $this->input->get('rid');
                $inventoryOrder = $this->input->get('io');

                $this->generate_reception_report($receptionId,  $inventoryOrder);
            } else if ($this->input->get('type') == "viewreception") {
                $receptionId = $this->input->get('rid');
                $inventoryOrder = $this->input->get('io');

                $getReceptionDetails = $this->Reception_model->get_reception_details($receptionId, $inventoryOrder);
                $getWH = $this->Master_model->get_warehouse_by_origin($getReceptionDetails[0]->origin_id);
                $getSuppliers = $this->Contract_model->get_suppliers_by_origin($getReceptionDetails[0]->origin_id);

                $data = array(
                    'pageheading' => $this->lang->line("reception_details"),
                    'pagetype' => 'update',
                    'receptionid' => $receptionId,
                    'inventoryorder' => $inventoryOrder,
                    'warehouses' => $getWH,
                    'suppliers' => $getSuppliers,
                    'reception_details' => $getReceptionDetails,
                    'reception_submit' => 'receptions/add'
                );
                $this->load->view('receptions/dialog_view_reception', $data);
            } else if ($this->input->get('type') == "deletereceptionconfirmation") {

                $receptionId = $this->input->get('rid');
                $inventoryOrder = $this->input->get('io');

                $getReceptionDetail = $this->Reception_model->get_reception_details($receptionId, $inventoryOrder);

                $totalPieces = ($getReceptionDetail[0]->total_pieces + 0);
                $remainingPieces = ($getReceptionDetail[0]->remaining_pieces + 0);

                if ($totalPieces == $remainingPieces) {
                    $data = array(
                        'pageheading' => $this->lang->line('confirmation'),
                        'pagemessage' => $this->lang->line('delete_message'),
                        'inputid' => $this->input->get('rid'),
                        'inputid1' => $this->input->get('io'),
                        'actionurl' => "receptions/dialog_reception_action",
                        'actiontype' => "deletereception",
                        'xin_table' => "#xin_table_receptions",
                    );
                    $this->load->view('dialogs/dialog_confirmation', $data);
                } else {
                    $data = array(
                        "pageheading" => $this->lang->line("information"),
                        "pagemessage" => $this->lang->line("reception_delete_message"),
                        "messagetype" => "info",
                    );
                    $this->load->view("dialogs/dialog_message", $data);
                }
            } else if ($this->input->get('type') == "deletereception") {

                $receptionId = $this->input->get('inputid');
                $inventoryOrder = $this->input->get('inputid1');

                $receptionDelete = $this->Reception_model->delete_reception($receptionId, $inventoryOrder, $session['user_id']);

                if ($receptionDelete) {
                    $Return['result'] = $this->lang->line('data_deleted');
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_deleting');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function generate_reception_report($receptionId, $inventoryOrder)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getReceptionDetails = $this->Reception_model->get_reception_details($receptionId, $inventoryOrder);
                $getReceptionDataDetails = $this->Reception_model->get_reception_data_details($receptionId, $inventoryOrder);

                if (count($getReceptionDetails) == 1 && count($getReceptionDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($inventoryOrder);
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue("A2", $this->lang->line('supplier_name'));
                    $objSheet->SetCellValue("A3", $this->lang->line('inventory_order'));
                    $objSheet->SetCellValue("A4", $this->lang->line('product'));
                    $objSheet->SetCellValue("A5", $this->lang->line('received_date'));
                    $objSheet->SetCellValue("A6", $this->lang->line('origin'));
                    $objSheet->SetCellValue("C2", $this->lang->line('measuremet_system'));
                    $objSheet->SetCellValue("C3", $this->lang->line('total_no_of_pieces'));
                    $objSheet->SetCellValue("C4", $this->lang->line('total_volume'));
                    $objSheet->SetCellValue("C5", $this->lang->line('remaining_pieces'));
                    $objSheet->SetCellValue("C6", $this->lang->line('remaining_volume'));

                    $objSheet->SetCellValue("B2", $getReceptionDetails[0]->supplier_name);
                    $objSheet->SetCellValue("B3", $getReceptionDetails[0]->salvoconducto);
                    $objSheet->SetCellValue("B4", $getReceptionDetails[0]->product_name . ' - ' . $this->lang->line($getReceptionDetails[0]->product_type_name));
                    $objSheet->SetCellValue("B5", $getReceptionDetails[0]->received_date);
                    $objSheet->SetCellValue("B6", $getReceptionDetails[0]->origin);
                    $objSheet->SetCellValue("D2", $this->lang->line($getReceptionDetails[0]->measurement_name));

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

                    $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($getReceptionDetails[0]->measurementsystem_id, $getReceptionDetails[0]->origin_id);

                    $grossVolumeFormula = "";
                    $netVolumeFormula = "";
                    foreach ($getFormulae as $formula) {

                        if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                            $grossVolumeFormula = str_replace(
                                array('pow', 'round', 'truncate'),
                                array("POWER", "ROUND", "TRUNC"),
                                $formula->calculation_formula
                            );
                        }

                        if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                            $netVolumeFormula = str_replace(
                                array('pow', 'round', 'truncate'),
                                array("POWER", "ROUND",  "TRUNC"),
                                $formula->calculation_formula
                            );
                        }
                    }

                    if ($getReceptionDetails[0]->measurementsystem_id == 1) {
                    } else if ($getReceptionDetails[0]->measurementsystem_id == 2 || $getReceptionDetails[0]->measurementsystem_id == 3) {

                        $objSheet->SetCellValue("A$rowCount", $this->lang->line("circumference"));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line("length"));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line("pieces"));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line("gross_volume"));
                        $objSheet->SetCellValue("E$rowCount", $this->lang->line("net_volume"));
                        $objSheet->SetCellValue("F$rowCount", $this->lang->line("remaining_pieces"));
                        $objSheet->SetCellValue("G$rowCount", $this->lang->line("gross_volume"));
                        $objSheet->SetCellValue("H$rowCount", $this->lang->line("net_volume"));
                        $objSheet->SetCellValue("I$rowCount", $this->lang->line("dispatched_containers"));

                        $objSheet->getStyle("A$rowCount:I$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:I$rowCount");

                        $objSheet->getStyle("A$rowCount:I$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getStyle("A$rowCount:I$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("add8e6");

                        $objSheet->getStyle("A$rowCount:I$rowCount")->applyFromArray($styleArray);

                        $rowCountData = 9;
                        foreach ($getReceptionDataDetails as $receptiondata) {

                            $grossVolumeFormulae = str_replace(
                                array('$l', '$c'),
                                array("B$rowCountData", "A$rowCountData"),
                                $grossVolumeFormula
                            );

                            $netVolumeFormulae = str_replace(
                                array('$l', '$c'),
                                array("B$rowCountData", "A$rowCountData"),
                                $netVolumeFormula
                            );

                            $objSheet->SetCellValue("A$rowCountData", ($receptiondata->circumference_bought + 0));
                            $objSheet->SetCellValue("B$rowCountData", ($receptiondata->length_bought + 0));
                            $objSheet->SetCellValue("C$rowCountData", ($receptiondata->scanned_code + 0));
                            $objSheet->SetCellValue("F$rowCountData", ($receptiondata->remaining_stock_count + 0));

                            $objSheet->SetCellValue("D$rowCountData", "=$grossVolumeFormulae*C$rowCountData");
                            $objSheet->SetCellValue("E$rowCountData", "=$netVolumeFormulae*C$rowCountData");
                            $objSheet->SetCellValue("G$rowCountData", "=$grossVolumeFormulae*F$rowCountData");
                            $objSheet->SetCellValue("H$rowCountData", "=$netVolumeFormulae*F$rowCountData");

                            $objSheet->SetCellValue("I$rowCountData", $receptiondata->container_number);
                            $rowCountData++;
                        }

                        $rowCountData = $rowCountData - 1;
                        $objSheet->getStyle("A$rowCount:I$rowCountData")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("D3", "=SUM(C9:C$rowCountData)");
                        $objSheet->SetCellValue("D4", "=SUM(E9:E$rowCountData)");
                        $objSheet->SetCellValue("D5", "=SUM(F9:F$rowCountData)");
                        $objSheet->SetCellValue("D6", "=SUM(H9:H$rowCountData)");
                    }

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                    $objSheet->getColumnDimension("D")->setAutoSize(true);
                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                    $objSheet->getColumnDimension("F")->setAutoSize(true);
                    $objSheet->getColumnDimension("G")->setAutoSize(true);
                    $objSheet->getColumnDimension("H")->setAutoSize(true);
                    $objSheet->getColumnDimension("I")->setAutoSize(false)->setWidth(40);

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ReceptionReport_' . $inventoryOrder . '_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/ReceptionReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/ReceptionReports/" . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line('no_data_reports');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return['error'] = "";
                $Return['result'] = "";
                $Return['redirect'] = true;
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return['error'] = $e->getMessage(); // $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
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

        $files = glob(FCPATH . "reports/ReceptionReports/*.xlsx");
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
