<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Farmreports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Farm_model");
        $this->load->library("excel");
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("farmreport_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_farmreports";
        if (!empty($session)) {

            $data["csrf_hash"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("inventoryreports/farmreport", $data, TRUE);

            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function get_supplier_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getSuppliers = $this->Master_model->all_suppliers_origin($this->input->get("originid"));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . $supplier->supplier_name . "</option>";
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

    public function get_farm_inventory_by_supplier()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("all") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getInventory = $this->Farm_model->get_farm_inventory_order_by_supplier($this->input->get("originid"), $this->input->get("supplierid"));
                foreach ($getInventory as $inventory) {
                    $result = $result . "<option value='" . $inventory->inventory_order . "'>" . $inventory->inventory_order . "</option>";
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

    public function get_products_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getProducts = $this->Master_model->get_product_byorigin($this->input->get("originid"));
                foreach ($getProducts as $product) {
                    $result = $result . "<option value='" . $product->product_id . "'>" . $product->product_name . "</option>";
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

    public function get_product_type()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            $getProductType = $this->Master_model->get_product_type($this->input->get("originid"));
            foreach ($getProductType as $producttype) {
                $result = $result . "<option value='" . $producttype->type_id . "'>" . $producttype->product_type_name . "</option>";
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

    public function generate_farm_reports()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originId = $this->input->post("originId");
                $downloadCriteria = $this->input->post("downloadCriteria");
                $supplierId = $this->input->post("supplierId");
                $inventoryOrder = $this->input->post("inventoryOrder");
                $productId = $this->input->post("productId");
                $productTypeId = $this->input->post("productTypeId");
                $reportStartDate = $this->input->post("reportStartDate");
                $reportEndDate = $this->input->post("reportEndDate");
                $inputInventoryOrder = $this->input->post("inputInventoryOrder");

                $fetchFarmData_SquareBlocks = array();
                $fetchFarmData_RoundLogs = array();

                if ($downloadCriteria == 1) {
                    $fetchFarmData_SquareBlocks = $this->Farm_model->get_farm_report_by_supplier($originId, $supplierId, $inventoryOrder, 1);
                    $fetchFarmData_RoundLogs = $this->Farm_model->get_farm_report_by_supplier($originId, $supplierId, $inventoryOrder, 2);
                } else if ($downloadCriteria == 2) {
                    $fetchFarmData_SquareBlocks = $this->Farm_model->get_farm_report_by_daterange($originId, $reportStartDate, $reportEndDate, 1);
                    $fetchFarmData_RoundLogs = $this->Farm_model->get_farm_report_by_daterange($originId, $reportStartDate, $reportEndDate, 2);
                } else if ($downloadCriteria == 3) {
                    $fetchFarmData_SquareBlocks = $this->Farm_model->get_farm_report_by_product($originId, $productId, 1);
                    $fetchFarmData_RoundLogs = $this->Farm_model->get_farm_report_by_product($originId, $productId, 2);
                } else if ($downloadCriteria == 4) {
                    $fetchFarmData_SquareBlocks = $this->Farm_model->get_farm_report_by_producttype_square_block($originId, $productTypeId);
                    $fetchFarmData_RoundLogs = $this->Farm_model->get_farm_report_by_producttype_round_logs($originId, $productTypeId);
                } else if ($downloadCriteria == 5) {
                    $fetchFarmData_SquareBlocks = $this->Farm_model->get_farm_report_by_inventory($originId, $inputInventoryOrder, 1);
                    $fetchFarmData_RoundLogs = $this->Farm_model->get_farm_report_by_inventory($originId, $inputInventoryOrder, 2);
                }

                if (count($fetchFarmData_SquareBlocks) > 0 || count($fetchFarmData_RoundLogs) > 0) {

                    $isSquareBlockAvailable = 0;
                    if (count($fetchFarmData_SquareBlocks) > 0) {

                        $isSquareBlockAvailable = 1;
                        $this->excel->setActiveSheetIndex(0);
                        $objSquareBlocks = $this->excel->getActiveSheet();
                        $objSquareBlocks->setTitle($this->lang->line("Square Blocks"));
                        $objSquareBlocks->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                        $objSquareBlocks->getSheetView()->setZoomScale(95);
                    }

                    if (count($fetchFarmData_RoundLogs) > 0) {

                        $getFormulae = $this->Master_model->get_formulae_by_purchase_units("3,4,5", $originId);

                        $pieceFormula = "";
                        $geoFormula = "";
                        $hoppusFormula = "";

                        if (count($getFormulae) > 0) {
                            foreach ($getFormulae as $formula) {
                                $strFormula = str_replace(array('$ac', '$al', '$l', '$c', 'truncate', 'pow'), array("###", "@@@", "%%%", "!!!", "TRUNC", "POWER"), $formula->formula_context);

                                if ($formula->purchase_unit_id == 3) {
                                    if ($formula->type == "netvolume") {
                                        $pieceFormula = "=$strFormula*&&&";
                                    }
                                }

                                if ($formula->purchase_unit_id == 4) {
                                    if ($formula->type == "netvolume") {
                                        $geoFormula = "=$strFormula*&&&";
                                    }
                                }

                                if ($formula->purchase_unit_id == 5) {
                                    if ($formula->type == "netvolume") {
                                        $hoppusFormula = "=$strFormula*&&&";
                                    }
                                }
                            }
                        }

                        if ($isSquareBlockAvailable == 1) {

                            $objRoundLogs = $this->excel->createSheet(1);
                            $objRoundLogs->setTitle($this->lang->line("Round Logs"));
                            $objRoundLogs->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);
                        } else {
                            $this->excel->setActiveSheetIndex(0);
                            $objRoundLogs = $this->excel->getActiveSheet();
                            $objRoundLogs->setTitle($this->lang->line("Round Logs"));
                            $objRoundLogs->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);
                        }

                        // SUMMARY HEADER

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $objRoundLogs->mergeCells("B2:C2");
                        $objRoundLogs->SetCellValue("B2", $this->lang->line("report_summary1"));
                        $objRoundLogs->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objRoundLogs->getStyle("B2:C2")->getFont()->setBold(true);

                        $objRoundLogs->SetCellValue("B3", $this->lang->line("download_criteria"));
                        if ($downloadCriteria == 1) {
                            $objRoundLogs->SetCellValue("B4", $this->lang->line("supplier_name"));
                            $objRoundLogs->SetCellValue("C3", $this->lang->line("suppliercredit_title"));

                            $getSupplier = $this->Master_model->get_supplier_detail_by_id($supplierId);
                            
                            $objRoundLogs->SetCellValue("C4", $getSupplier[0]->supplier_name);
                        } else if ($downloadCriteria == 2) {
                            $objRoundLogs->SetCellValue("B4", $this->lang->line("date_range"));
                            $objRoundLogs->SetCellValue("C3", $this->lang->line("date_range"));

                            $objRoundLogs->SetCellValue("C4", "$reportStartDate - $reportEndDate");
                        } else if ($downloadCriteria == 3) {
                            $objRoundLogs->SetCellValue("B4", $this->lang->line("product_name"));
                            $objRoundLogs->SetCellValue("C3", $this->lang->line("product"));

                            $getProduct = $this->Master_model->get_product_detail_by_id($productId);
                            
                            $objRoundLogs->SetCellValue("C4", $getProduct[0]->product_name);
                        } else if ($downloadCriteria == 4) {
                            $objRoundLogs->SetCellValue("B4", $this->lang->line("product_type"));
                            $objRoundLogs->SetCellValue("C3", $this->lang->line("product_type"));

                            $getProductType = $this->Master_model->get_product_type_by_id($productTypeId);
                            
                            $objRoundLogs->SetCellValue("C4", $getProductType[0]->product_type_name);
                        } else if ($downloadCriteria == 5) {
                            $objRoundLogs->SetCellValue("B4", $this->lang->line("inventory_order"));
                            $objRoundLogs->SetCellValue("C3", $this->lang->line("inventory_order"));
                            
                            $objRoundLogs->SetCellValue("C4", $inputInventoryOrder);
                        }

                        $objRoundLogs->SetCellValue("B5", $this->lang->line("total_no_of_pieces"));
                        $objRoundLogs->SetCellValue("B6", $this->lang->line("total_volume"));
                        $objRoundLogs->getStyle("B2:B6")->getFont()->setBold(true);
                        $objRoundLogs->getStyle("B2:C6")->applyFromArray($styleArray);

                        // END SUMMARY HEADER

                        $dataRowCount = 8;
                        $startRowCount = 8;

                        $objRoundLogs->SetCellValue("A$dataRowCount", $this->lang->line("serial_no"));
                        $objRoundLogs->SetCellValue("B$dataRowCount", $this->lang->line("supplier_name"));
                        $objRoundLogs->SetCellValue("C$dataRowCount", $this->lang->line("supplier_code"));
                        $objRoundLogs->SetCellValue("D$dataRowCount", $this->lang->line("contract_code"));
                        $objRoundLogs->SetCellValue("E$dataRowCount", $this->lang->line("product_name"));
                        $objRoundLogs->SetCellValue("F$dataRowCount", $this->lang->line("product_type"));
                        $objRoundLogs->SetCellValue("G$dataRowCount", $this->lang->line("inventory_order"));
                        $objRoundLogs->SetCellValue("H$dataRowCount", $this->lang->line("scanned_code"));
                        $objRoundLogs->SetCellValue("I$dataRowCount", $this->lang->line("dispatch_pieces"));
                        $objRoundLogs->SetCellValue("J$dataRowCount", $this->lang->line("circumference"));
                        $objRoundLogs->SetCellValue("K$dataRowCount", $this->lang->line("length"));
                        $objRoundLogs->SetCellValue("L$dataRowCount", $this->lang->line("text_volume"));
                        $objRoundLogs->SetCellValue("M$dataRowCount", $this->lang->line("purchase_date"));
                        $objRoundLogs->SetCellValue("N$dataRowCount", $this->lang->line("uploaded_by"));

                        $objRoundLogs->getStyle("A$dataRowCount:N$dataRowCount")->getFont()->setBold(true);
                        $objRoundLogs->setAutoFilter("A$dataRowCount:N$dataRowCount");
                        $objRoundLogs->getStyle("A$dataRowCount:N$dataRowCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //FARM DATA

                        $sNo = 0;
                        foreach ($fetchFarmData_RoundLogs as $farmdata) {

                            $dataRowCount++;
                            $sNo++;

                            $objRoundLogs->SetCellValue("A$dataRowCount", $sNo);
                            $objRoundLogs->SetCellValue("B$dataRowCount", $farmdata->supplier_name);
                            $objRoundLogs->SetCellValue("C$dataRowCount", $farmdata->supplier_code);
                            $objRoundLogs->SetCellValue("D$dataRowCount", $farmdata->contract_code);
                            $objRoundLogs->SetCellValue("E$dataRowCount", $farmdata->product_name);
                            $objRoundLogs->SetCellValue("F$dataRowCount", $farmdata->product_type_name);
                            $objRoundLogs->SetCellValue("G$dataRowCount", $farmdata->inventory_order);

                            $objRoundLogs->SetCellValue("H$dataRowCount", $farmdata->scanned_code);

                            if($farmdata->scanned_code != null && $farmdata->scanned_code != "") {
                                $objRoundLogs->SetCellValue("I$dataRowCount", "1");
                            } else {
                                $objRoundLogs->SetCellValue("I$dataRowCount", $farmdata->no_of_pieces);
                            }

                            
                            $objRoundLogs->getStyle("H$dataRowCount:I$dataRowCount")->getNumberFormat()->setFormatCode('0');

                            $objRoundLogs->SetCellValue("J$dataRowCount", $farmdata->circumference);
                            $objRoundLogs->SetCellValue("K$dataRowCount", $farmdata->length);


                            if ($farmdata->purchase_unit_id == 3) {
                                if ($farmdata->no_of_pieces > 0) {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", "*I$dataRowCount"),
                                        $pieceFormula
                                    ));
                                } else {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", ""),
                                        $pieceFormula
                                    ));
                                }
                            } else if ($farmdata->purchase_unit_id == 4) {
                                if ($farmdata->no_of_pieces > 0) {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", "*I$dataRowCount"),
                                        $geoFormula
                                    ));
                                } else {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", ""),
                                        $geoFormula
                                    ));
                                }
                            } else if ($farmdata->purchase_unit_id == 5) {
                                if ($farmdata->no_of_pieces > 0) {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", "*I$dataRowCount"),
                                        $hoppusFormula
                                    ));
                                } else {
                                    $objRoundLogs->SetCellValue("L$dataRowCount", str_replace(
                                        array("###", "@@@", "%%%", "!!!", "*&&&"),
                                        array($farmdata->circumference_allowance, $farmdata->length_allowance, "K$dataRowCount", "J$dataRowCount", ""),
                                        $hoppusFormula
                                    ));
                                }
                            }

                            $receivedDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $farmdata->received_date));
                            $objRoundLogs->setCellValue("M$dataRowCount", $receivedDateValue);
                            $objRoundLogs->getStyle("M$dataRowCount")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                            $objRoundLogs->SetCellValue("N$dataRowCount", $farmdata->received_by);
                        }

                        //END DATA
                        $startRowCount1 = $startRowCount + 1;
                        $objRoundLogs->getStyle("L$startRowCount1:L$dataRowCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objRoundLogs->getStyle("A$startRowCount:N$dataRowCount")->applyFromArray($styleArray);

                        $objRoundLogs->SetCellValue("C5", "=SUM(I$startRowCount1:I$dataRowCount)");
                        $objRoundLogs->SetCellValue("C6", "=SUM(L$startRowCount1:L$dataRowCount)");
                        $objRoundLogs->getStyle("C6")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objRoundLogs->getColumnDimension("A")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("B")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("C")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("D")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("E")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("F")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("G")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("H")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("I")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("J")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("K")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("L")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("M")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("N")->setAutoSize(true);

                        $objRoundLogs->getSheetView()->setZoomScale(95);
                    }

                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'FarmReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/FarmReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/FarmReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
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
            $Return["error"] = $e->getMessage(); // $this->lang->line('error_reports');
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

        $files = glob(FCPATH . "reports/FarmReports/*.xlsx");
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
}
