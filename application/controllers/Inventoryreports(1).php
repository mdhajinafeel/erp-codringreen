<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inventoryreports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Reception_model");
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
        $data["title"] = $this->lang->line("inventoryreport_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_warehousereports";
        if (!empty($session)) {

            $data["csrf_hash"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("inventoryreports/warehousereport", $data, TRUE);

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

    public function get_warehouse_inventory_by_supplier()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("all") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getInventory = $this->Reception_model->get_warehouse_inventory_order_by_supplier($this->input->get("originid"), $this->input->get("supplierid"));
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

    public function generate_warehouse_reports()
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

                $fetchWarehouseData_SquareBlocks = array();
                $fetchWarehouseData_RoundLogs = array();

                if ($downloadCriteria == 1) {
                    $fetchWarehouseData_SquareBlocks = $this->Reception_model->get_warehouse_report_by_supplier($originId, $supplierId, $inventoryOrder, 1);
                    $fetchWarehouseData_RoundLogs = $this->Reception_model->get_warehouse_report_by_supplier($originId, $supplierId, $inventoryOrder, 2);
                } else if ($downloadCriteria == 2) {
                    $fetchWarehouseData_SquareBlocks = $this->Reception_model->get_warehouse_report_by_daterange($originId, $reportStartDate, $reportEndDate, 1);
                    $fetchWarehouseData_RoundLogs = $this->Reception_model->get_warehouse_report_by_daterange($originId, $reportStartDate, $reportEndDate, 2);
                } else if ($downloadCriteria == 3) {
                    $fetchWarehouseData_SquareBlocks = $this->Reception_model->get_warehouse_report_by_product($originId, $productId, 1);
                    $fetchWarehouseData_RoundLogs = $this->Reception_model->get_warehouse_report_by_product($originId, $productId, 2);
                } else if ($downloadCriteria == 4) {
                    $fetchWarehouseData_SquareBlocks = $this->Reception_model->get_warehouse_report_by_producttype_square_block($originId, $productTypeId);
                    $fetchWarehouseData_RoundLogs = $this->Reception_model->get_warehouse_report_by_producttype_round_logs($originId, $productTypeId);
                } else if ($downloadCriteria == 5) {
                    $fetchWarehouseData_SquareBlocks = $this->Reception_model->get_warehouse_report_by_inventory($originId, $inputInventoryOrder, 1);
                    $fetchWarehouseData_RoundLogs = $this->Reception_model->get_warehouse_report_by_inventory($originId, $inputInventoryOrder, 2);
                }

                if (count($fetchWarehouseData_SquareBlocks) > 0 || count($fetchWarehouseData_RoundLogs) > 0) {

                    $isSquareBlockAvailable = 0;
                    if (count($fetchWarehouseData_SquareBlocks) > 0) {

                        $isSquareBlockAvailable = 1;
                        $this->excel->setActiveSheetIndex(0);
                        $objSquareBlocks = $this->excel->getActiveSheet();
                        $objSquareBlocks->setTitle($this->lang->line("Square Blocks"));
                        $objSquareBlocks->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                        $objSquareBlocks->getSheetView()->setZoomScale(95);
                    }

                    if (count($fetchWarehouseData_RoundLogs) > 0) {

                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem_producttype(2, $originId);

                        $grossVolumeFormula_Geo = "";
                        $netVolumeFormula_Geo = "";
                        $grossVolumeFormula_Hoppus = "";
                        $netVolumeFormula_Hoppus = "";

                        if (count($getFormulae) > 0) {
                            foreach ($getFormulae as $formula) {

                                if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                    $grossVolumeFormula_Hoppus = str_replace(
                                        array('pow', 'round', 'truncate'),
                                        array("POWER", "ROUND", "TRUNC"),
                                        $formula->calculation_formula
                                    );
                                    $grossVolumeFormula_Hoppus = "=$grossVolumeFormula_Hoppus*pcs";
                                }

                                if($formula->context == "CBM_GEO_GROSSVOLUME") {
                                    $grossVolumeFormula_Geo = str_replace(
                                        array('pow', 'round', 'truncate'),
                                        array("POWER", "ROUND", "TRUNC"),
                                        $formula->calculation_formula
                                    );
                                    $grossVolumeFormula_Geo = "=$grossVolumeFormula_Geo*pcs";
                                }
        
                                if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                    $netVolumeFormula_Hoppus = str_replace(
                                        array('pow', 'round', 'truncate'),
                                        array("POWER", "ROUND",  "TRUNC"),
                                        $formula->calculation_formula
                                    );
                                    $netVolumeFormula_Hoppus = "=$netVolumeFormula_Hoppus*pcs";
                                }

                                if($formula->context == "CBM_GEO_NETVOLUME") {
                                    $netVolumeFormula_Geo = str_replace(
                                        array('pow', 'round', 'truncate'),
                                        array("POWER", "ROUND",  "TRUNC"),
                                        $formula->calculation_formula
                                    );
                                    $netVolumeFormula_Geo = "=$netVolumeFormula_Geo*pcs";
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
                        $objRoundLogs->SetCellValue("B6", $this->lang->line("total_gross_volume"));
                        $objRoundLogs->SetCellValue("B7", $this->lang->line("total_net_volume"));
                        $objRoundLogs->getStyle("B2:B7")->getFont()->setBold(true);
                        $objRoundLogs->getStyle("B2:C7")->applyFromArray($styleArray);

                        // END SUMMARY HEADER

                        $dataRowCount = 9;
                        $startRowCount = 9;

                        $objRoundLogs->SetCellValue("A$dataRowCount", $this->lang->line("serial_no"));
                        $objRoundLogs->SetCellValue("B$dataRowCount", $this->lang->line("supplier_name"));
                        $objRoundLogs->SetCellValue("C$dataRowCount", $this->lang->line("supplier_code"));
                        $objRoundLogs->SetCellValue("D$dataRowCount", $this->lang->line("product_name"));
                        $objRoundLogs->SetCellValue("E$dataRowCount", $this->lang->line("product_type"));
                        $objRoundLogs->SetCellValue("F$dataRowCount", $this->lang->line("inventory_order"));
                        $objRoundLogs->SetCellValue("G$dataRowCount", $this->lang->line("scanned_code"));
                        $objRoundLogs->SetCellValue("H$dataRowCount", $this->lang->line("dispatch_pieces"));
                        $objRoundLogs->SetCellValue("I$dataRowCount", $this->lang->line("circumference"));
                        $objRoundLogs->SetCellValue("J$dataRowCount", $this->lang->line("length"));
                        $objRoundLogs->SetCellValue("K$dataRowCount", $this->lang->line("wh_name"));
                        $objRoundLogs->SetCellValue("L$dataRowCount", $this->lang->line("measuremet_system"));
                        $objRoundLogs->SetCellValue("M$dataRowCount", $this->lang->line("gross_volume"));
                        $objRoundLogs->SetCellValue("N$dataRowCount", $this->lang->line("net_volume"));
                        $objRoundLogs->SetCellValue("O$dataRowCount", $this->lang->line("received_date"));
                        $objRoundLogs->SetCellValue("P$dataRowCount", $this->lang->line("uploaded_by"));

                        $objRoundLogs->getStyle("A$dataRowCount:P$dataRowCount")->getFont()->setBold(true);
                        $objRoundLogs->setAutoFilter("A$dataRowCount:P$dataRowCount");
                        $objRoundLogs->getStyle("A$dataRowCount:P$dataRowCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //WAREHOUSE DATA

                        $sNo = 0;
                        foreach ($fetchWarehouseData_RoundLogs as $warehousedata) {

                            $dataRowCount++;
                            $sNo++;

                            $objRoundLogs->SetCellValue("A$dataRowCount", $sNo);
                            $objRoundLogs->SetCellValue("B$dataRowCount", $warehousedata->supplier_name);
                            $objRoundLogs->SetCellValue("C$dataRowCount", $warehousedata->supplier_code);
                            $objRoundLogs->SetCellValue("D$dataRowCount", $warehousedata->product_name);
                            $objRoundLogs->SetCellValue("E$dataRowCount", $warehousedata->product_type_name);
                            $objRoundLogs->SetCellValue("F$dataRowCount", $warehousedata->salvoconducto);

                            if($warehousedata->is_special == 0) {
                                $objRoundLogs->SetCellValue("G$dataRowCount", $warehousedata->scanned_code);
                                $objRoundLogs->SetCellValue("H$dataRowCount", "1");
                            } else {
                                $objRoundLogs->SetCellValue("H$dataRowCount", $warehousedata->scanned_code);
                            }
                            
                            $objRoundLogs->getStyle("G$dataRowCount:H$dataRowCount")->getNumberFormat()->setFormatCode('0');

                            $objRoundLogs->SetCellValue("I$dataRowCount", $warehousedata->circumference_bought);
                            $objRoundLogs->SetCellValue("J$dataRowCount", $warehousedata->length_bought);
                            $objRoundLogs->SetCellValue("K$dataRowCount", $warehousedata->warehouse_name);
                            $objRoundLogs->SetCellValue("L$dataRowCount", $warehousedata->measurement_name);

                            if ($warehousedata->measurement_code == "HOPPUS") {
                                if ($warehousedata->is_special == 1) {
                                    $objRoundLogs->SetCellValue("M$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"), $grossVolumeFormula_Hoppus));
                                    $objRoundLogs->SetCellValue("N$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"),$netVolumeFormula_Hoppus));
                                } else {
                                    $objRoundLogs->SetCellValue("M$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"), $grossVolumeFormula_Hoppus));
                                    $objRoundLogs->SetCellValue("N$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"), $netVolumeFormula_Hoppus));
                                }
                            } else if ($warehousedata->measurement_code == "GEO") {
                                if ($warehousedata->is_special == 1) {
                                    $objRoundLogs->SetCellValue("M$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"),$grossVolumeFormula_Geo));
                                    $objRoundLogs->SetCellValue("N$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                            array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"),$netVolumeFormula_Geo));
                                } else {
                                    $objRoundLogs->SetCellValue("M$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                        array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"),$grossVolumeFormula_Geo));
                                    $objRoundLogs->SetCellValue("N$dataRowCount", str_replace(array('$c', '$l', 'pcs'), 
                                                        array("I$dataRowCount", "J$dataRowCount", "H$dataRowCount"),$netVolumeFormula_Geo));
                                }
                            }

                            $receivedDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $warehousedata->received_date));
                            $objRoundLogs->setCellValue("O$dataRowCount", $receivedDateValue);
                            $objRoundLogs->getStyle("O$dataRowCount")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                            $objRoundLogs->SetCellValue("P$dataRowCount", $warehousedata->received_by);
                        }

                        //END DATA
                        $startRowCount1 = $startRowCount + 1;
                        $objRoundLogs->getStyle("M$startRowCount1:N$dataRowCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objRoundLogs->getStyle("A$startRowCount:P$dataRowCount")->applyFromArray($styleArray);

                        $objRoundLogs->SetCellValue("C5", "=SUM(H$startRowCount1:H$dataRowCount)");
                        $objRoundLogs->SetCellValue("C6", "=SUM(M$startRowCount1:M$dataRowCount)");
                        $objRoundLogs->SetCellValue("C7", "=SUM(N$startRowCount1:N$dataRowCount)");
                        $objRoundLogs->getStyle("C6:C7")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

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
                        $objRoundLogs->getColumnDimension("O")->setAutoSize(true);
                        $objRoundLogs->getColumnDimension("P")->setAutoSize(true);

                        $objRoundLogs->getSheetView()->setZoomScale(95);
                    }

                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'InventoryReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/WarehouseReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/WarehouseReports/' . $filename;
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

        $files = glob(FCPATH . "reports/WarehouseReports/*.xlsx");
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