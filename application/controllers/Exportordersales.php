<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '1');

defined('BASEPATH') or exit('No direct script access allowed');

class Exportordersales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Export_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Claimtracker_model");
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
        $data["title"] = $this->lang->line("exportorder_title") . " - " . $this->lang->line("sales") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_exportsales";
        if (!empty($session)) {

            $applicable_origins = $session["applicable_origins"];
            $data["shippinglines"] = $this->Master_model->get_shippinglines_by_origin($applicable_origins[0]->id);
            $data["producttypes"] = $this->Master_model->get_product_type();
            $data["csrf_hash"] = $this->security->get_csrf_hash();

            $data["subview"] = $this->load->view("exportsales/export_order", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function export_order_list()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            $exportContainers = $this->Export_model->all_exports($this->input->get("originid"), $this->input->get("tid"), $this->input->get("sid"));

            $data = array();

            foreach ($exportContainers as $r) {
                // if ($this->input->get("originid") == 3) {
                //     $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("generate") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="proformainvoice" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-receipt"></span></button></span>
                //     <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("download") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexportorder" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fas fa-eye"></span></button></span>';
                // } else {
                //     $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("generate") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="proformainvoice" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-receipt"></span></button></span>
                //     <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("download") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexportorder" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fas fa-eye"></span></button></span>';
                // }
                
                if ($this->input->get("originid") == 3) {
                    $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("invoice_history") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="invoicehistory" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-history"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("generate_proforma_invoice") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="proformainvoice" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-receipt"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("download") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexportorder" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fas fa-eye"></span></button></span>';
                } else {

                    // if($r->id >= 531) {
                        $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("invoice_history") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="invoicehistory" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-history"></span></button></span> 
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("generate_proforma_invoice") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="proformainvoice" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-receipt"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("download") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexportorder" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fas fa-eye"></span></button></span>';
                    // } else {
                    //     $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("generate") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="invoicehistory" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fa fa-history"></span></button></span> 
                    //     <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("download") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexportorder" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '"><span class="fas fa-eye"></span></button></span>';
                    // }
                }

                $product_type = $this->lang->line($r->product_type_name);

                $data[] = array(
                    $actionExport,
                    $r->sa_number,
                    $product_type,
                    $r->shipping_line,
                    $r->pol_name,
                    $r->pod_name,
                    ($r->d_total_containers + 0),
                    ($r->total_pieces + 0),
                    ($r->total_net_volume + 0),
                    $r->origin,
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

    public function dialog_export_order_option()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->post("type") == "viewexport") {

                $originId = $this->input->post("origin_id");
                $saNumber = $this->input->post("sa_number");
                $exportOrderId = $this->input->post("export_order_id");

                //CHECK EXPORT DETAILS
                $exportOrderDetail = $this->Export_model->get_export_order_details_by_id($exportOrderId, $saNumber, $originId);
                if (count($exportOrderDetail) == 1) {

                    $data = array(
                        "pageheading" => $this->lang->line("generate_summary"),
                        "pagetype" => "generate_summary",
                        "csrfhash" => $this->security->get_csrf_hash(),
                        "originid" => $originId,
                        "exportid" => $exportOrderId,
                        "sanumber" => $saNumber,
                        "export_details" => $exportOrderDetail,
                        "product_type_id" => $exportOrderDetail[0]->product_type_id,
                        "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($originId, $exportOrderDetail[0]->product_type_id),
                    );

                    $this->load->view("exportsales/dialog_view_export_order", $data);
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("common_error");
                    $Return["messagetype"] = "info";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return["pagemessage"] = "";
            $Return["pageheading"] = "";
            $Return["pages"] = "";
            $Return["messagetype"] = "redirect";
            $Return["pagemessage"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function generate_export_order_report()
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "",
                "error" => "",
                "redirect" => false,
                "csrf_hash" => "",
                "successmessage" => ""
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originId = $this->input->post("originid");
                $saNumber = $this->input->post("sanumber");
                $exportOrderId = $this->input->post("exportid");
                $productTypeId = $this->input->post("producttypeid");
                $measurementSystemId = $this->input->post("measurementsystem");
                $circumferenceAllowance = $this->input->post("circumferenceallowance");
                $lengthAllowance = $this->input->post("lengthallowance") / 100;
                $circumferenceAdjustment = $this->input->post("circumferenceadjustment");

                //CHECK EXPORT ORDER
                $exportOrderDetail = $this->Export_model->get_export_order_details_by_id($exportOrderId, $saNumber, $originId);
                if (count($exportOrderDetail) == 1) {

                    $originName = $this->Master_model->get_origin_iso3_code($originId);
                    $companySettings = $this->Master_model->get_company_settings_by_origin($originId);

                    //SUMMARY REPORT

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle(strtoupper($this->lang->line("report_summary")));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    if ($originId == 3) {

                        $diameterHeaderArr = array();
                        $lengthHeaderArr = array();

                        $nofityName = $exportOrderDetail[0]->notify_name;
                        $notifyDetails = $exportOrderDetail[0]->notify_details;
                        $consigneeName = $exportOrderDetail[0]->consignee_name;
                        $consigneeDetails = $exportOrderDetail[0]->consignee_details;

                        //SUMMARY PAGE HEADING

                        $objSheet->SetCellValue("D1", strtoupper("Shipment Advice"));
                        $objSheet->getStyle("D1")->getFont()->setUnderline(true);
                        $objSheet->getStyle("D1")->getFont()->setBold(true);
                        $objSheet->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $link_style_array = array('font' => array('color' => array('rgb' => '0000FF'), 'underline' => 'single'));
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

                        // INSERT IMAGE
                        $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                        $objDrawing->setName($this->Settings_model->site_title());
                        $objDrawing->setDescription($this->Settings_model->site_title());
                        $objDrawing->setImageResource($gdImage);
                        $objDrawing->setHeight(60);
                        $objDrawing->setCoordinates("D4");
                        $objDrawing->setWorksheet($objSheet);
                        // END IMAGE

                        //END SUMMARY PAGE HEADING

                        //SUMMARY HEADER

                        $objSheet->SetCellValue("A13", $this->lang->line("product_type"));
                        $objSheet->SetCellValue("A14", $this->lang->line("origin"));
                        $objSheet->SetCellValue("B13", $exportOrderDetail[0]->product_type_name);
                        $objSheet->SetCellValue("B14", $originName[0]->origin_name);

                        $objSheet->getStyle("A13:B14")->getFont()->setBold(true);
                        $objSheet->getStyle("A13:B14")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("A2", $this->lang->line("sa_number"));
                        $objSheet->SetCellValue("A3", $this->lang->line("port_of_loading"));
                        $objSheet->SetCellValue("A4", $this->lang->line("port_of_discharge"));
                        $objSheet->SetCellValue("A5", $this->lang->line("shipped_date"));
                        $objSheet->SetCellValue("A6", $this->lang->line("booking_number"));
                        $objSheet->SetCellValue("A7", $this->lang->line("bl_date"));
                        $objSheet->SetCellValue("A8", $this->lang->line("vessel_name"));
                        $objSheet->SetCellValue("A9", $this->lang->line("shipping_line"));
                        $objSheet->SetCellValue("A10", $this->lang->line("no_of_fcls"));
                        $objSheet->SetCellValue("A11", $this->lang->line("eta_destination"));

                        $objSheet->SetCellValue("B2", $exportOrderDetail[0]->sa_number);
                        $objSheet->SetCellValue("B3", $exportOrderDetail[0]->pol_name);
                        $objSheet->SetCellValue("B4", $exportOrderDetail[0]->pod_name);
                        $objSheet->SetCellValue("B5", $exportOrderDetail[0]->shipped_date);
                        $objSheet->SetCellValue("B6", $exportOrderDetail[0]->bl_no);
                        $objSheet->SetCellValue("B7", $exportOrderDetail[0]->bl_date);
                        $objSheet->SetCellValue("B8", $exportOrderDetail[0]->vessel_name);
                        $objSheet->SetCellValue("B9", $exportOrderDetail[0]->shipping_line);
                        $objSheet->SetCellValue("B10", $exportOrderDetail[0]->total_containers);

                        $objSheet->getStyle("A2:B11")->getFont()->setBold(true);
                        $objSheet->getStyle("A2:B11")->applyFromArray($styleArray);
                        $objSheet->getStyle("B2:B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        //END SUMMARY HEADER

                        //SUMMARY DATA HEADER

                        $objSheet->SetCellValue("A17", strtoupper($this->lang->line("serial_no")));
                        $objSheet->SetCellValue("B17", strtoupper($this->lang->line("seal_number")));
                        $objSheet->SetCellValue("C17", strtoupper($this->lang->line("CONTAINER #")));
                        $objSheet->SetCellValue("D17", strtoupper($this->lang->line("diameter")));
                        $objSheet->SetCellValue("E17", strtoupper($this->lang->line("avg_dia")));
                        $objSheet->SetCellValue("F17", strtoupper($this->lang->line("length")));

                        if ($measurementSystemId == 7 || $measurementSystemId == 9) {
                            $objSheet->SetCellValue("G17", strtoupper($this->lang->line("gross_volume")));
                            $objSheet->SetCellValue("H17", strtoupper($this->lang->line("net_volume")));
                        } else if ($measurementSystemId == 8 || $measurementSystemId == 10) {
                            $objSheet->SetCellValue("G17", strtoupper($this->lang->line("gross_weight")));
                            $objSheet->SetCellValue("H17", strtoupper($this->lang->line("net_weight")));
                        }

                        $objSheet->SetCellValue("I17", strtoupper($this->lang->line("pieces")));

                        $objSheet->SetCellValue("J17", strtoupper($this->lang->line("average_log_weight")));

                        $objSheet->getStyle("A17:J17")->getFont()->setBold(true);
                        $objSheet->getStyle("A17:J17")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                        $objSheet->getStyle("A17:J17")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle("A17:J17")->applyFromArray($styleArray);

                        //END SUMMARY DATA HEADER

                        //CONTAINER SHEET
                        $getContainerDetails = $this->Export_model->get_container_details($exportOrderDetail[0]->dispatchids, $originId);

                        $sheetNo = 1;
                        $summaryDataCount = 18;
                        $containerCnt = 0;

                        $containerAvgWeightArray = array();

                        if (count($getContainerDetails) > 0) {
                            foreach ($getContainerDetails as $containerdetails) {
                                $objWorkSheet = $this->excel->createSheet($sheetNo);
                                $objWorkSheet->setTitle(strtoupper($containerdetails->container_number));

                                // INSERT IMAGE
                                $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                $objDrawing->setName($this->Settings_model->site_title());
                                $objDrawing->setDescription($this->Settings_model->site_title());
                                $objDrawing->setImageResource($gdImage);
                                $objDrawing->setHeight(60);
                                $objDrawing->setCoordinates("F3");
                                $objDrawing->setWorksheet($objWorkSheet);
                                // END IMAGE

                                //HEADER

                                $objWorkSheet->SetCellValue("A2", strtoupper($this->lang->line("CONTAINER #")));
                                $objWorkSheet->SetCellValue("A3", strtoupper($this->lang->line("pieces")));
                                $objWorkSheet->SetCellValue("A4", strtoupper($this->lang->line("jas_cbm")));
                                $objWorkSheet->SetCellValue("A5", strtoupper($this->lang->line("average_dia")));
                                $objWorkSheet->SetCellValue("A6", strtoupper($this->lang->line("seal_number")));
                                $objWorkSheet->SetCellValue("A7", strtoupper($this->lang->line("gross_weight")));
                                $objWorkSheet->SetCellValue("A8", strtoupper($this->lang->line("net_weight")));

                                $objWorkSheet->SetCellValue("B2", $containerdetails->container_number);
                                $objWorkSheet->SetCellValue("B4", $containerdetails->total_volume);
                                $objWorkSheet->SetCellValue("B6", $containerdetails->seal_number);
                                $objWorkSheet->SetCellValue("B7", $containerdetails->metric_ton);
                                $objWorkSheet->SetCellValue("B8", $containerdetails->metric_ton);

                                $objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkSheet->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                $objWorkSheet->getStyle("A2")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A3")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A4")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A5")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A6")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A7")->getFont()->setBold(true);
                                $objWorkSheet->getStyle("A8")->getFont()->setBold(true);

                                $objWorkSheet->getStyle("A2:B8")->applyFromArray($styleArray);

                                $objWorkSheet->SetCellValue("A10", $this->lang->line("serial_no"));
                                $objWorkSheet->SetCellValue("B10", $this->lang->line("diameter"));

                                $objWorkSheet->getStyle("A10:B10")->getFont()->setBold(true);
                                $objWorkSheet->getRowDimension(10)->setRowHeight(25);
                                $objWorkSheet->getStyle("A10:B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $objWorkSheet->getStyle("A10:B10")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                $objWorkSheet->getStyle("A10:B10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
                                $objWorkSheet->setAutoFilter("A10:B10");

                                //END HEADER

                                //DISPATCH DATA

                                $getDispatchData = $this->Export_model->get_container_dispatch_data($containerdetails->dispatch_id, $containerdetails->container_number, $originId);

                                $dispatchDataCount = 11;

                                if (count($getDispatchData) > 0) {
                                    $totalCount = 0;
                                    foreach ($getDispatchData as $dispatchdata) {

                                        $totalCount++;

                                        $objWorkSheet->SetCellValue("A$dispatchDataCount", $totalCount);
                                        $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + 0));

                                        $dispatchDataCount++;
                                    }
                                }

                                $lastRow = $dispatchDataCount - 1;
                                $objWorkSheet->getStyle("A10:B$lastRow")->applyFromArray($styleArray);
                                $objWorkSheet->getColumnDimension("A")->setAutoSize(true);
                                $objWorkSheet->getColumnDimension("B")->setAutoSize(true);

                                //END DATA

                                //RENDER HEADER DATA

                                $objWorkSheet->SetCellValue("B3", "=COUNT(A11:A$lastRow)");
                                $objWorkSheet->SetCellValue("B5", "=AVERAGE(B11:B$lastRow)");

                                $objWorkSheet->getStyle("B5")->getNumberFormat()->setFormatCode('0');
                                $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                //END RENDER HEADER DATA

                                //SUMMARY DATA

                                $containerCnt++;

                                $objSheet->SetCellValue("A$summaryDataCount", $containerCnt);
                                $objSheet->SetCellValue("B$summaryDataCount", $containerdetails->seal_number);
                                $objSheet->SetCellValue("C$summaryDataCount", $containerdetails->container_number);
                                $objSheet->SetCellValue("D$summaryDataCount", html_entity_decode($containerdetails->diameter_text, ENT_QUOTES, 'UTF-8'));

                                $objSheet->SetCellValue("E$summaryDataCount", "='$containerdetails->container_number'!B5");
                                $objSheet->getStyle("E$summaryDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                $objSheet->SetCellValue("F$summaryDataCount", html_entity_decode($containerdetails->length_text, ENT_QUOTES, 'UTF-8'));
                                
                                $int_var = preg_replace('/[^0-9]/', '', html_entity_decode($containerdetails->diameter_text, ENT_QUOTES, 'UTF-8'));  
                                $diameterValue = substr($int_var, -2) + 0;
                                $avgDiameterValue = $objSheet->getCell("E$summaryDataCount")->getFormattedValue() + 0;

                                if($avgDiameterValue < $diameterValue){
                                    $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFont()->getColor()->setRGB('C00000');
                                } else {
                                    $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFont()->getColor()->setRGB('000000');
                                }

                                array_push($diameterHeaderArr, html_entity_decode($containerdetails->diameter_text, ENT_QUOTES, 'UTF-8'));
                                array_push($lengthHeaderArr, html_entity_decode($containerdetails->length_text, ENT_QUOTES, 'UTF-8'));

                                if ($measurementSystemId == 7 || $measurementSystemId == 9) {
                                    $objSheet->SetCellValue("G$summaryDataCount", ($containerdetails->total_gross_volume + 0));
                                    $objSheet->SetCellValue("H$summaryDataCount", ($containerdetails->total_volume + 0));

                                    $objSheet->getStyle("G$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');
                                    $objSheet->getStyle("H$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');
                                } else if ($measurementSystemId == 8 || $measurementSystemId == 10) {
                                    $objSheet->SetCellValue("G$summaryDataCount", ($containerdetails->metric_ton + 0));
                                    $objSheet->SetCellValue("H$summaryDataCount", ($containerdetails->metric_ton + 0));

                                    $objSheet->getStyle("G$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');
                                    $objSheet->getStyle("H$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');
                                }

                                $objSheet->SetCellValue("I$summaryDataCount", $containerdetails->total_pieces);


                                $objSheet->SetCellValue("J$summaryDataCount", "=ROUND(H$summaryDataCount*1000/I$summaryDataCount,0)");

                                if ($measurementSystemId == 7 || $measurementSystemId == 9) {

                                    $row_array_data["diameter"] = html_entity_decode($containerdetails->diameter_text, ENT_QUOTES, 'UTF-8');
                                    $row_array_data["length"] = html_entity_decode($containerdetails->length_text, ENT_QUOTES, 'UTF-8');
                                    $row_array_data["avgWeight"] = round($containerdetails->metric_ton * 1000 / $containerdetails->total_pieces, 0);
                                    array_push($containerAvgWeightArray, $row_array_data);
                                } else if ($measurementSystemId == 8 || $measurementSystemId == 10) {
                                    $row_array_data["diameter"] = html_entity_decode($containerdetails->diameter_text, ENT_QUOTES, 'UTF-8');
                                    $row_array_data["length"] = html_entity_decode($containerdetails->length_text, ENT_QUOTES, 'UTF-8');
                                    $row_array_data["avgWeight"] = round($containerdetails->metric_ton * 1000 / $containerdetails->total_pieces, 0);
                                    array_push($containerAvgWeightArray, $row_array_data);
                                }

                                $objSheet->getStyle("J$summaryDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                //END SUMMARY

                                $sheetNo++;
                                $summaryDataCount++;
                            }
                        }

                        //END CONTAINER SHEET

                        //END SUMMARY REPORT

                        $this->excel->setActiveSheetIndex(0);

                        $lastRowSummary = $summaryDataCount - 1;

                        $objSheet->getStyle("A18:J$lastRowSummary")->applyFromArray($styleArray);
                        $objSheet->getStyle("A18:J$lastRowSummary")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(false)->setWidth(25);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(false)->setWidth(32);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                        $objSheet->getColumnDimension("G")->setAutoSize(false)->setWidth(32);
                        $objSheet->getColumnDimension("H")->setAutoSize(true);
                        $objSheet->getColumnDimension("I")->setAutoSize(true);
                        $objSheet->getColumnDimension("J")->setAutoSize(true);

                        $summaryDataCount = $summaryDataCount + 1;
                        $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFont()->setBold(true);
                        $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                        $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("C$summaryDataCount", count($getContainerDetails) . " " . $this->lang->line("containers"));
                        $objSheet->SetCellValue("G$summaryDataCount", "=SUM(G18:G$lastRowSummary)");
                        $objSheet->SetCellValue("H$summaryDataCount", "=SUM(H18:H$lastRowSummary)");

                        $objSheet->getStyle("G$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("H$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');

                        $objSheet->SetCellValue("I$summaryDataCount", "=SUM(I18:I$lastRowSummary)");
                        //$objSheet->SetCellValue("I$summaryDataCount", "");
                        $objSheet->SetCellValue("J$summaryDataCount", "=ROUND(H$summaryDataCount*1000/I$summaryDataCount,0)");
                        $objSheet->getStyle("J$summaryDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                        $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $summaryDataCountNotify = $summaryDataCount + 2;
                        $summaryDataCountConsignee = $summaryDataCount + 2;

                        $objSheet->SetCellValue("C$summaryDataCountConsignee", $this->lang->line("consignee"));
                        $objSheet->SetCellValue("D$summaryDataCountConsignee", $consigneeName);
                        $objSheet->getStyle("C$summaryDataCountConsignee:D$summaryDataCountConsignee")->getFont()->setBold(true);

                        $objSheet->SetCellValue("F$summaryDataCountNotify", $this->lang->line("notify"));
                        $objSheet->SetCellValue("G$summaryDataCountNotify", $nofityName);
                        $objSheet->getStyle("F$summaryDataCountNotify:G$summaryDataCountNotify")->getFont()->setBold(true);

                        $arrConsignee = explode("\n", $consigneeDetails);
                        $arrNotify = explode("\n", $notifyDetails);

                        if (count($arrNotify) > 0) {
                            foreach ($arrNotify as $val) {
                                $summaryDataCountNotify = $summaryDataCountNotify + 1;
                                $objSheet->SetCellValue("G$summaryDataCountNotify", $val);
                            }
                        } else {
                            $summaryDataCountNotify = $summaryDataCountNotify + 1;
                            $objSheet->SetCellValue("G$summaryDataCountNotify", $notifyDetails);
                        }

                        if (count($arrConsignee) > 0) {
                            foreach ($arrConsignee as $val) {
                                $summaryDataCountConsignee = $summaryDataCountConsignee + 1;
                                $objSheet->SetCellValue("D$summaryDataCountConsignee", $val);
                            }
                        } else {
                            $summaryDataCountConsignee = $summaryDataCountConsignee + 1;
                            $objSheet->SetCellValue("D$summaryDataCountConsignee", $consigneeDetails);
                        }

                        // //SUMMARY COUNT & WEIGHT DATA

                        // $diameterHeaderArr = array_unique($diameterHeaderArr);
                        // $lengthHeaderArr = array_unique($lengthHeaderArr);

                        // $analysisHeaderCount = 17;
                        // $analysisRowDataCount = 17;
                        // $analysisColumnDataCount = "K";

                        // foreach ($lengthHeaderArr as $length) {
                        //     $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "$length");

                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFont()->setBold(true);
                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("B7DEE8");
                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //     $analysisColumnDataCount++;
                        // }

                        // $analysisRowDataCount++;
                        // foreach ($diameterHeaderArr as $diameter) {
                        //     $objSheet->SetCellValue("J$analysisRowDataCount", "$diameter");

                        //     $objSheet->getStyle("J$analysisRowDataCount")->getFont()->setBold(true);
                        //     $objSheet->getStyle("J$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                        //     $objSheet->getStyle("J$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //     $analysisColumnDataCount = "K";
                        //     foreach ($lengthHeaderArr as $length) {
                        //         $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=COUNTIFS(D17:D$lastRowSummary,J$analysisRowDataCount,E17:E$lastRowSummary,$analysisColumnDataCount$analysisHeaderCount)");
                        //         $analysisColumnDataCount++;
                        //     }

                        //     $analysisRowDataCount++;
                        // }

                        // $analysisColumnDataCount = $this->decrementLetter($analysisColumnDataCount);
                        // $analysisRowDataCount = $analysisRowDataCount - 1;
                        // $objSheet->getStyle("J17:$analysisColumnDataCount$analysisRowDataCount")->applyFromArray($styleArray);


                        // $analysisRowDataCount = $analysisRowDataCount + 3;
                        // $analysisHeaderCount = $analysisRowDataCount;
                        // $analysisColumnDataCount = "K";
                        // foreach ($lengthHeaderArr as $length) {
                        //     $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "$length");

                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFont()->setBold(true);
                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("B7DEE8");
                        //     $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //     $analysisColumnDataCount++;
                        // }

                        // $analysisRowDataCount++;
                        // foreach ($diameterHeaderArr as $diameter) {
                        //     $objSheet->SetCellValue("J$analysisRowDataCount", "$diameter");

                        //     $objSheet->getStyle("J$analysisRowDataCount")->getFont()->setBold(true);
                        //     $objSheet->getStyle("J$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                        //     $objSheet->getStyle("J$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //     $analysisColumnDataCount = "K";
                        //     foreach ($lengthHeaderArr as $length) {

                        //         $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=SUMIFS(G17:G$lastRowSummary,D17:D$lastRowSummary,J$analysisRowDataCount,E17:E$lastRowSummary,$analysisColumnDataCount$analysisHeaderCount)");

                        //         $analysisColumnDataCount++;
                        //     }

                        //     $analysisRowDataCount++;
                        // }

                        // $analysisRowDataCount = $analysisRowDataCount - 1;
                        // $analysisColumnDataCount = $this->decrementLetter($analysisColumnDataCount);
                        // $objSheet->getStyle("J$analysisHeaderCount:$analysisColumnDataCount$analysisRowDataCount")->applyFromArray($styleArray);

                        //SUMMARY COUNT & WEIGHT DATA

                        $diameterHeaderArr = array_unique($diameterHeaderArr);
                        $lengthHeaderArr = array_unique($lengthHeaderArr);

                        $analysisHeaderCount = 17;
                        $analysisRowDataCount = 17;
                        $analysisColumnDataCount = "M";

                        foreach ($lengthHeaderArr as $length) {
                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "$length");

                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("B7DEE8");
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisColumnDataCount++;
                        }

                        $objSheet->SetCellValue("L$analysisRowDataCount", $this->lang->line("text_container_count"));
                        $objSheet->getStyle("L$analysisRowDataCount")->getFont()->setBold(true);
                        $objSheet->getStyle("L$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                        $objSheet->getStyle("L$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $analysisRowDataCount++;
                        foreach ($diameterHeaderArr as $diameter) {
                            $objSheet->SetCellValue("L$analysisRowDataCount", "$diameter");

                            $objSheet->getStyle("L$analysisRowDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("L$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                            $objSheet->getStyle("L$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisColumnDataCount = "M";
                            foreach ($lengthHeaderArr as $length) {
                                $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=COUNTIFS(D17:D$lastRowSummary,L$analysisRowDataCount,F17:F$lastRowSummary,$analysisColumnDataCount$analysisHeaderCount)");
                                $analysisColumnDataCount++;
                            }

                            $analysisRowDataCount++;
                        }

                        $analysisColumnDataCount = $this->decrementLetter($analysisColumnDataCount);
                        $analysisRowDataCount = $analysisRowDataCount - 1;
                        $objSheet->getStyle("L17:$analysisColumnDataCount$analysisRowDataCount")->applyFromArray($styleArray);


                        $analysisRowDataCount = $analysisRowDataCount + 3;
                        $analysisHeaderCount = $analysisRowDataCount;
                        $analysisColumnDataCount = "M";
                        foreach ($lengthHeaderArr as $length) {
                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "$length");

                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("B7DEE8");
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisColumnDataCount++;
                        }

                        $objSheet->SetCellValue("L$analysisRowDataCount", $this->lang->line("text_weight_count"));
                        $objSheet->getStyle("L$analysisRowDataCount")->getFont()->setBold(true);
                        $objSheet->getStyle("L$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                        $objSheet->getStyle("L$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $analysisRowDataCount++;
                        foreach ($diameterHeaderArr as $diameter) {
                            $objSheet->SetCellValue("L$analysisRowDataCount", "$diameter");

                            $objSheet->getStyle("L$analysisRowDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("L$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                            $objSheet->getStyle("L$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisColumnDataCount = "M";
                            foreach ($lengthHeaderArr as $length) {

                                $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=SUMIFS(H17:H$lastRowSummary,D17:D$lastRowSummary,L$analysisRowDataCount,F17:F$lastRowSummary,$analysisColumnDataCount$analysisHeaderCount)");

                                $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getNumberFormat()->setFormatCode('0.00');

                                $analysisColumnDataCount++;
                            }

                            $analysisRowDataCount++;
                        }

                        $analysisRowDataCount = $analysisRowDataCount - 1;
                        $analysisColumnDataCount = $this->decrementLetter($analysisColumnDataCount);
                        $objSheet->getStyle("L$analysisHeaderCount:$analysisColumnDataCount$analysisRowDataCount")->applyFromArray($styleArray);

                        $analysisRowDataCountFirstRow = $analysisRowDataCount + 2;
                        $analysisRowDataCount = $analysisRowDataCount + 2;
                        $analysisHeaderCount = $analysisRowDataCount;
                        $analysisColumnDataCount = "M";
                        $mergeCellDataCount = $analysisColumnDataCount;

                        foreach ($lengthHeaderArr as $length) {

                            $mergeCellDataCount++;
                            $mergeCellDataCount++;
                            $mergeCellDataCount++;

                            $objSheet->mergeCells("$analysisColumnDataCount$analysisHeaderCount:$mergeCellDataCount$analysisHeaderCount");
                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisHeaderCount", "$length");

                            $objSheet->getStyle("$analysisColumnDataCount$analysisHeaderCount")->getFont()->setBold(true);
                            $objSheet->getStyle("$analysisColumnDataCount$analysisHeaderCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("B7DEE8");
                            $objSheet->getStyle("$analysisColumnDataCount$analysisHeaderCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisRowDataCount = $analysisHeaderCount + 1;

                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", $this->lang->line("text_min"));
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $analysisColumnDataCount++;

                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", $this->lang->line("text_max"));
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $analysisColumnDataCount++;

                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", $this->lang->line("text_var"));
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $analysisColumnDataCount++;

                            $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", $this->lang->line("text_avg"));
                            $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $analysisColumnDataCount++;

                            $mergeCellDataCount++;
                        }

                        $objSheet->mergeCells("L$analysisHeaderCount:L$analysisRowDataCount");
                        $objSheet->SetCellValue("L$analysisHeaderCount", $this->lang->line("text_wt_var_report"));
                        $objSheet->getStyle("L$analysisHeaderCount")->getFont()->setBold(true);
                        $objSheet->getStyle("L$analysisHeaderCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                        $objSheet->getStyle("L$analysisHeaderCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

                        $analysisRowDataCount++;
                        foreach ($diameterHeaderArr as $diameter) {
                            $objSheet->SetCellValue("L$analysisRowDataCount", "$diameter");

                            $objSheet->getStyle("L$analysisRowDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("L$analysisRowDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("F2DCDB");
                            $objSheet->getStyle("L$analysisRowDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $analysisColumnDataCount = "M";
                            $calcRowCount = $analysisColumnDataCount;
                            foreach ($lengthHeaderArr as $length) {

                                $calcRowCount = $analysisColumnDataCount;

                                $array1 = array();
                                foreach ($containerAvgWeightArray as $avgweight) {
                                    if ($avgweight["diameter"] == "$diameter" && $avgweight["length"] == "$length") {
                                        array_push($array1, $avgweight["avgWeight"]);
                                    }
                                }

                                foreach ($containerAvgWeightArray as $avgweight) {
                                    if ($avgweight["diameter"] == "$diameter" && $avgweight["length"] == "$length") {
                                        $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", min($array1));
                                    }
                                }

                                $varianceMinColumn = $analysisColumnDataCount;

                                // $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=MIN(IF(D18:D22=K$analysisRowDataCount, I18:I22))");
                                //$objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=IFERROR(MAXIFS(I18:I$lastRowSummary,D18:D$lastRowSummary,K$analysisRowDataCount,E18:E$lastRowSummary,$calcRowCount$analysisHeaderCount), 0)");
                                $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                $analysisColumnDataCount++;
                                foreach ($containerAvgWeightArray as $avgweight) {
                                    if ($avgweight["diameter"] == "$diameter" && $avgweight["length"] == "$length") {
                                        $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", max($array1));
                                    }
                                }

                                $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                $varianceMaxColumn = $analysisColumnDataCount;

                                $analysisColumnDataCount++;
                                $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=$varianceMaxColumn$analysisRowDataCount-$varianceMinColumn$analysisRowDataCount");
                                $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                $analysisColumnDataCount++;
                                $objSheet->SetCellValue("$analysisColumnDataCount$analysisRowDataCount", "=IFERROR(AVERAGEIFS(J18:J$lastRowSummary,D18:D$lastRowSummary,L$analysisRowDataCount,F18:F$lastRowSummary,$calcRowCount$analysisHeaderCount), 0)");
                                $objSheet->getStyle("$analysisColumnDataCount$analysisRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                                $analysisColumnDataCount++;
                            }

                            $analysisRowDataCount++;
                        }

                        $analysisRowDataCount = $analysisRowDataCount - 1;
                        $analysisColumnDataCount = $this->decrementLetter($analysisColumnDataCount);
                        $objSheet->getStyle("L$analysisHeaderCount:$analysisColumnDataCount$analysisRowDataCount")->applyFromArray($styleArray);
                    } else {


                        //SUMMARY PAGE HEADING

                        $objSheet->SetCellValue("E1", strtoupper($this->lang->line("shipment_advice")));
                        $objSheet->getStyle("E1")->getFont()->setUnderline(true);
                        $objSheet->getStyle("E1")->getFont()->setBold(true);
                        $objSheet->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $link_style_array = array('font' => array('color' => array('rgb' => '0000FF'), 'underline' => 'single'));
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

                        // INSERT IMAGE
                        $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                        $objDrawing->setName($this->Settings_model->site_title());
                        $objDrawing->setDescription($this->Settings_model->site_title());
                        $objDrawing->setImageResource($gdImage);
                        $objDrawing->setHeight(60);
                        $objDrawing->setCoordinates("A1");
                        $objDrawing->setWorksheet($objSheet);
                        // END IMAGE

                        //END SUMMARY PAGE HEADING

                        //SUMMARY HEADER

                        $objSheet->SetCellValue("A12", $this->lang->line("product_type"));
                        $objSheet->SetCellValue("A13", $this->lang->line("origin"));
                        $objSheet->SetCellValue("B12", $exportOrderDetail[0]->product_type_name);
                        $objSheet->SetCellValue("B13", $originName[0]->origin_name);

                        $objSheet->getStyle("A12:A13")->getFont()->setBold(true);
                        $objSheet->getStyle("A12:B13")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("E5", $this->lang->line("sa_number"));
                        $objSheet->SetCellValue("E6", $this->lang->line("port_of_loading"));
                        $objSheet->SetCellValue("E7", $this->lang->line("port_of_discharge"));
                        $objSheet->SetCellValue("E8", $this->lang->line("shipped_date"));
                        $objSheet->SetCellValue("E9", $this->lang->line("bl_number"));
                        $objSheet->SetCellValue("E10", $this->lang->line("bl_date"));
                        $objSheet->SetCellValue("E11", $this->lang->line("vessel_name"));
                        $objSheet->SetCellValue("E12", $this->lang->line("shipping_line"));
                        $objSheet->SetCellValue("E13", $this->lang->line("no_of_fcls"));
                        $objSheet->SetCellValue("E14", $this->lang->line("eta_destination"));

                        $objSheet->SetCellValue("F5", $exportOrderDetail[0]->sa_number);
                        $objSheet->SetCellValue("F6", $exportOrderDetail[0]->pol_name);
                        $objSheet->SetCellValue("F7", $exportOrderDetail[0]->pod_name);
                        $objSheet->SetCellValue("F9", $exportOrderDetail[0]->bl_no);
                        $objSheet->SetCellValue("F10", $exportOrderDetail[0]->bl_date);
                        $objSheet->SetCellValue("F11", $exportOrderDetail[0]->vessel_name);
                        $objSheet->SetCellValue("F12", $exportOrderDetail[0]->shipping_line);
                        $objSheet->SetCellValue("F13", $exportOrderDetail[0]->total_containers);

                        $shippedDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $exportOrderDetail[0]->shipped_date));
                        $objSheet->setCellValue("F8", $shippedDateValue);
                        $objSheet->getStyle("F8")
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                        $objSheet->setCellValue("F14", "=F8+42");
                        $objSheet->getStyle("F14")
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                        $objSheet->getStyle("F5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F13")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("F14")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        $objSheet->getStyle("E5:E14")->getFont()->setBold(true);
                        $objSheet->getStyle("E5:F14")->applyFromArray($styleArray);

                        //END SUMMARY HEADER

                        if ($exportOrderDetail[0]->product_type_id == 1 || $exportOrderDetail[0]->product_type_id == 3) {

                            //SUMMARY DATA HEADER

                            $objSheet->SetCellValue("A17", $this->lang->line("client_pno"));
                            $objSheet->SetCellValue("B17", $this->lang->line("stuffing_date"));
                            $objSheet->SetCellValue("C17", $this->lang->line("container_number"));
                            $objSheet->SetCellValue("D17", $this->lang->line("length"));
                            $objSheet->SetCellValue("E17", $this->lang->line("width"));
                            $objSheet->SetCellValue("F17", $this->lang->line("thickness"));
                            $objSheet->SetCellValue("G17", $this->lang->line("volume_pie"));
                            $objSheet->SetCellValue("H17", $this->lang->line("gross_volume"));
                            $objSheet->SetCellValue("I17", $this->lang->line("net_volume"));
                            $objSheet->SetCellValue("J17", $this->lang->line("pieces"));
                            $objSheet->SetCellValue("K17", $this->lang->line("text_cft"));
                            $objSheet->SetCellValue("L17", $this->lang->line("photo_link"));

                            $objSheet->getStyle("A17:L17")->getFont()->setBold(true);
                            $objSheet->getStyle("A17:L17")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                            $objSheet->getStyle("A17:L17")->applyFromArray($styleArray);

                            //END SUMMARY DATA HEADER

                            //CONTAINER SHEET
                            $getContainerDetails = $this->Export_model->get_container_details($exportOrderDetail[0]->dispatchids, $originId);

                            $sheetNo = 1;
                            $summaryDataCount = 18;

                            if (count($getContainerDetails) > 0) {
                                foreach ($getContainerDetails as $containerdetails) {

                                    $objWorkSheet = $this->excel->createSheet($sheetNo);
                                    $objWorkSheet->setTitle(strtoupper($containerdetails->container_number));

                                    // INSERT IMAGE
                                    $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                    $objDrawing->setName($this->Settings_model->site_title());
                                    $objDrawing->setDescription($this->Settings_model->site_title());
                                    $objDrawing->setImageResource($gdImage);
                                    $objDrawing->setHeight(60);
                                    $objDrawing->setCoordinates("J10");
                                    $objDrawing->setWorksheet($objWorkSheet);
                                    // END IMAGE

                                    //HEADER

                                    $objWorkSheet->SetCellValue("A2", $this->lang->line("container_number"));
                                    $objWorkSheet->SetCellValue("A3", $this->lang->line("load_date"));
                                    $objWorkSheet->SetCellValue("A4", $this->lang->line("loading_place"));
                                    $objWorkSheet->SetCellValue("A5", $this->lang->line("seal_number"));
                                    $objWorkSheet->SetCellValue("A6", $this->lang->line("pieces"));
                                    $objWorkSheet->SetCellValue("A7", $this->lang->line("length"));
                                    $objWorkSheet->SetCellValue("A8", $this->lang->line("width"));
                                    $objWorkSheet->SetCellValue("A9", $this->lang->line("thickness"));
                                    $objWorkSheet->SetCellValue("A10", $this->lang->line("total_gross_volume"));
                                    $objWorkSheet->SetCellValue("A11", $this->lang->line("total_net_volume"));
                                    $objWorkSheet->SetCellValue("A12", $this->lang->line("volume_pie"));
                                    $objWorkSheet->SetCellValue("A13", $this->lang->line("text_cft"));

                                    $objWorkSheet->SetCellValue("B2", $containerdetails->container_number);
                                    $objWorkSheet->SetCellValue("B3", $containerdetails->dispatch_date);
                                    $objWorkSheet->SetCellValue("B4", $containerdetails->warehouse_name);

                                    $objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B13")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                    $objWorkSheet->getStyle("A2")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A3")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A4")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A5")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A6")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A7")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A8")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A9")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A10")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A11")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A12")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A13")->getFont()->setBold(true);

                                    $objWorkSheet->getStyle("A2:B13")->applyFromArray($styleArray);

                                    $objWorkSheet->SetCellValue("A15", $this->lang->line("length"));
                                    $objWorkSheet->SetCellValue("B15", $this->lang->line("width"));
                                    $objWorkSheet->SetCellValue("C15", $this->lang->line("thickness"));
                                    $objWorkSheet->SetCellValue("D15", $this->lang->line("pieces"));
                                    $objWorkSheet->SetCellValue("E15", $this->lang->line("volume_pie"));
                                    $objWorkSheet->SetCellValue("F15", $this->lang->line("length_export"));
                                    $objWorkSheet->SetCellValue("G15", $this->lang->line("width_export"));
                                    $objWorkSheet->SetCellValue("H15", $this->lang->line("thickness_export"));
                                    $objWorkSheet->SetCellValue("I15", $this->lang->line("gross_volume"));
                                    $objWorkSheet->SetCellValue("J15", $this->lang->line("net_volume"));
                                    $objWorkSheet->SetCellValue("K15", $this->lang->line("grade"));

                                    $objWorkSheet->getStyle("A15:K15")->getFont()->setBold(true);
                                    $objWorkSheet->getRowDimension(15)->setRowHeight(30);
                                    $objWorkSheet->getStyle("A15:K15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objWorkSheet->getStyle("A15:K15")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    $objWorkSheet->getStyle("A15:K15")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
                                    $objWorkSheet->setAutoFilter("A15:K15");

                                    //END HEADER

                                    //DISPATCH DATA

                                    $getDispatchData = $this->Export_model->get_container_dispatch_data_square_blocks($containerdetails->dispatch_id, $containerdetails->container_number, $originId);

                                    // $getDispatchData = $this->Export_model->get_container_dispatch_data_old($containerdetails->dispatch_id, $containerdetails->container_number, $originId);

                                    $dispatchDataCount = 16;
                                    $textGrade1 = '"' . $this->lang->line('grade1') . '"';
                                    $textGrade2 = '"' . $this->lang->line('grade2') . '"';
                                    $textGrade3 = '"' . $this->lang->line('grade3') . '"';
                                    if (count($getDispatchData) > 0) {
                                        foreach ($getDispatchData as $dispatchdata) {

                                            $objWorkSheet->SetCellValue("A$dispatchDataCount", ($dispatchdata->length_bought + 0));
                                            $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->width_bought + 0));
                                            $objWorkSheet->SetCellValue("C$dispatchDataCount", ($dispatchdata->thickness_bought + 0));
                                            $objWorkSheet->SetCellValue("D$dispatchDataCount", ($dispatchdata->dispatch_pieces + 0));
                                            $objWorkSheet->SetCellValue("E$dispatchDataCount", "=ROUND(A$dispatchDataCount * B$dispatchDataCount * C$dispatchDataCount / 12 ,2)");
                                            $objWorkSheet->SetCellValue("F$dispatchDataCount", "=A$dispatchDataCount * 0.3");
                                            $objWorkSheet->SetCellValue("G$dispatchDataCount", "=TRUNC(B$dispatchDataCount * 2.54, 0)");
                                            $objWorkSheet->SetCellValue("H$dispatchDataCount", "=ROUND(C$dispatchDataCount * 2.54, 0)");
                                            $objWorkSheet->SetCellValue("I$dispatchDataCount", "=ROUND(E$dispatchDataCount/424,3)");
                                            $objWorkSheet->SetCellValue("J$dispatchDataCount", "=ROUND(F$dispatchDataCount * G$dispatchDataCount * H$dispatchDataCount / 10000, 3)");
                                            $objWorkSheet->SetCellValue("K$dispatchDataCount", "=IF(G$dispatchDataCount<15, " . $textGrade1 . " ,IF(H$dispatchDataCount<15, " . $textGrade1 . " ,IF(G$dispatchDataCount>19.9, " . $textGrade3 . " ,IF(H$dispatchDataCount>19.9, " . $textGrade3 . " ," . $textGrade2 . "))))");

                                            $dispatchDataCount++;
                                        }
                                    }

                                    $lastRow = $dispatchDataCount - 1;
                                    $objWorkSheet->getStyle("I15:J$lastRow")
                                        ->getNumberFormat()
                                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                    $objWorkSheet->getStyle("A15:K$lastRow")->applyFromArray($styleArray);

                                    $objWorkSheet->getColumnDimension("A")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("B")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("C")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("D")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("E")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("F")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("G")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("H")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("I")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("J")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("K")->setAutoSize(true);

                                    //END DATA

                                    //RENDER HEADER DATA

                                    $cftText = "&" . '"' . " x " . '"' . "&";

                                    $objWorkSheet->SetCellValue("B6", "=SUM(D16:D$lastRow)");
                                    $objWorkSheet->SetCellValue("B7", "=ROUND(AVERAGE(A16:A$lastRow),2)");
                                    $objWorkSheet->SetCellValue("B8", "=ROUND(AVERAGE(B16:B$lastRow),2)");
                                    $objWorkSheet->SetCellValue("B9", "=ROUND(AVERAGE(C16:C$lastRow),2)");
                                    $objWorkSheet->SetCellValue("B10", "=SUM(I16:I$lastRow)");
                                    $objWorkSheet->SetCellValue("B11", "=SUM(J16:J$lastRow)");
                                    $objWorkSheet->SetCellValue("B12", "=SUM(E16:E$lastRow)");
                                    $objWorkSheet->SetCellValue("B13", "=B8" . $cftText . "B9");

                                    $objWorkSheet->mergeCells('F2:H2');
                                    $objWorkSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                                    $objWorkSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                    $objWorkSheet->SetCellValue('G3', $this->lang->line("text_volume"));
                                    $objWorkSheet->SetCellValue('H3', $this->lang->line("ages"));

                                    $objWorkSheet->SetCellValue('F4', $this->lang->line("grade1"));
                                    $objWorkSheet->SetCellValue('F5', $this->lang->line("grade2"));
                                    $objWorkSheet->SetCellValue('F6', $this->lang->line("grade3"));
                                    $objWorkSheet->SetCellValue('F7', $this->lang->line("total"));

                                    $objWorkSheet->SetCellValue('G4', "=SUMIF(K16:K$lastRow,F4,J16:J$lastRow)");
                                    $objWorkSheet->SetCellValue('G5', "=SUMIF(K16:K$lastRow,F5,J16:J$lastRow)");
                                    $objWorkSheet->SetCellValue('G6', "=SUMIF(K16:K$lastRow,F6,J16:J$lastRow)");
                                    $objWorkSheet->SetCellValue('G7', "=SUM(G4:G6)");

                                    $objWorkSheet->SetCellValue('H4', "=G4/G7");
                                    $objWorkSheet->SetCellValue('H5', "=G5/G7");
                                    $objWorkSheet->SetCellValue('H6', "=G6/G7");
                                    $objWorkSheet->SetCellValue('H7', "=SUM(H4:H6)");

                                    $objWorkSheet->getStyle('H4:H7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                                    $objWorkSheet->getStyle('H7')->getFont()->setBold(true);

                                    $BStyle = array(
                                        'borders' => array(
                                            'outline' => array(
                                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                            )
                                        )
                                    );
                                    $objWorkSheet->getStyle('F2:H7')->applyFromArray($styleArray);
                                    $objWorkSheet->getStyle('F2:H7')->applyFromArray($BStyle);
                                    unset($BStyle);

                                    //END RENDER HEADER DATA

                                    //SUMMARY DATA

                                    $objSheet->SetCellValue("B$summaryDataCount", $containerdetails->dispatch_date);
                                    $objSheet->SetCellValue("C$summaryDataCount", $containerdetails->container_number);

                                    $objSheet->SetCellValue("D$summaryDataCount", "='$containerdetails->container_number'!B7");
                                    $objSheet->SetCellValue("E$summaryDataCount", "='$containerdetails->container_number'!B8");
                                    $objSheet->SetCellValue("F$summaryDataCount", "='$containerdetails->container_number'!B9");
                                    $objSheet->SetCellValue("G$summaryDataCount", "='$containerdetails->container_number'!B12");
                                    $objSheet->SetCellValue("H$summaryDataCount", "='$containerdetails->container_number'!B10");
                                    $objSheet->SetCellValue("I$summaryDataCount", "='$containerdetails->container_number'!B11");
                                    $objSheet->SetCellValue("J$summaryDataCount", "='$containerdetails->container_number'!B6");
                                    $objSheet->SetCellValue("K$summaryDataCount", "='$containerdetails->container_number'!B13");

                                    if ($containerdetails->container_pic_url != "" && $containerdetails->container_pic_url != null) {

                                        $objSheet->SetCellValue("L$summaryDataCount", $containerdetails->container_pic_url);
                                        $objSheet->getCell("L$summaryDataCount")->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
                                        $objSheet->getCell("L$summaryDataCount")->getHyperlink()->setUrl(strip_tags($containerdetails->container_pic_url));
                                        $objSheet->getStyle("L$summaryDataCount")->applyFromArray($link_style_array);
                                    } else {
                                        $objSheet->SetCellValue("L$summaryDataCount", "");
                                    }

                                    //END SUMMARY

                                    $sheetNo++;
                                    $summaryDataCount++;
                                }
                            }
                            //END CONTAINER SHEET

                        } else if ($exportOrderDetail[0]->product_type_id == 2 || $exportOrderDetail[0]->product_type_id == 4) {

                            $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($measurementSystemId, $originId);

                            $strGrossFormula = "";
                            $strNetFormula = "";

                            if (count($getFormulae) > 0) {
                                foreach ($getFormulae as $formula) {

                                    if ($formula->context == "CBM_HOPPUS_GROSSVOLUME_EXPORT" || $formula->context == "CBM_GEO_GROSSVOLUME_EXPORT") {
                                        $strGrossFormula = str_replace(array('$l', '$c', '$pcs', 'truncate'), array("###", "$$$", "!!!", '$this->truncate'), $formula->calculation_formula);
                                    }

                                    if ($originId == 2 && $exportOrderId >= 178) {
                                        if ($formula->context == "CBM_HOPPUS_NETVOLUME_EXPORT1" || $formula->context == "CBM_GEO_NETVOLUME_EXPORT1") {
                                            $strNetFormula = str_replace(array('$l', '$c', '$pcs', 'truncate', '$ac', '$al'), array("###", "$$$", "!!!", '$this->truncate', $circumferenceAllowance, $lengthAllowance), $formula->calculation_formula);
                                        }
                                    } else {

                                        if ($formula->context == "CBM_HOPPUS_NETVOLUME_EXPORT" || $formula->context == "CBM_GEO_NETVOLUME_EXPORT") {
                                            $strNetFormula = str_replace(array('$l', '$c', '$pcs', 'truncate', '$ac', '$al'), array("###", "$$$", "!!!", '$this->truncate', $circumferenceAllowance, $lengthAllowance), $formula->calculation_formula);
                                        }
                                    }
                                }
                            }

                            //SUMMARY DATA HEADER

                            $objSheet->SetCellValue("A17", $this->lang->line("client_pno"));
                            $objSheet->SetCellValue("B17", $this->lang->line("stuffing_date"));
                            $objSheet->SetCellValue("C17", $this->lang->line("container_number"));
                            $objSheet->SetCellValue("D17", $this->lang->line("product_type"));
                            $objSheet->SetCellValue("E17", $this->lang->line("length"));
                            $objSheet->SetCellValue("F17", $this->lang->line("circumference"));
                            $objSheet->SetCellValue("G17", $this->lang->line("gross_volume"));
                            $objSheet->SetCellValue("H17", $this->lang->line("net_volume"));
                            $objSheet->SetCellValue("I17", $this->lang->line("pieces"));
                            $objSheet->SetCellValue("J17", $this->lang->line("text_cft"));
                            $objSheet->SetCellValue("K17", $this->lang->line("photo_link"));

                            $objSheet->getStyle("A17:K17")->getFont()->setBold(true);
                            $objSheet->getStyle("A17:K17")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                            $objSheet->getStyle("A17:K17")->applyFromArray($styleArray);

                            //END SUMMARY DATA HEADER

                            //CONTAINER SHEET
                            $getContainerDetails = $this->Export_model->get_container_details($exportOrderDetail[0]->dispatchids, $originId);

                            $sheetNo = 1;
                            $summaryDataCount = 18;
                            $emptyText = '""';
                            $textShorts = '"SHORTS"';
                            $textLongs = '"LONGS"';
                            $textSemi = '"SEMI LONGS"';

                            if (count($getContainerDetails) > 0) {
                                foreach ($getContainerDetails as $containerdetails) {

                                    $objWorkSheet = $this->excel->createSheet($sheetNo);
                                    $objWorkSheet->setTitle(strtoupper($containerdetails->container_number));

                                    // INSERT IMAGE
                                    $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                    $objDrawing->setName($this->Settings_model->site_title());
                                    $objDrawing->setDescription($this->Settings_model->site_title());
                                    $objDrawing->setImageResource($gdImage);
                                    $objDrawing->setHeight(60);
                                    $objDrawing->setCoordinates("H10");
                                    $objDrawing->setWorksheet($objWorkSheet);
                                    // END IMAGE

                                    //HEADER

                                    $objWorkSheet->SetCellValue("A2", $this->lang->line("container_number"));
                                    $objWorkSheet->SetCellValue("A3", $this->lang->line("load_date"));
                                    $objWorkSheet->SetCellValue("A4", $this->lang->line("loading_place"));
                                    $objWorkSheet->SetCellValue("A5", $this->lang->line("seal_number"));
                                    $objWorkSheet->SetCellValue("A6", $this->lang->line("pieces"));
                                    $objWorkSheet->SetCellValue("A7", $this->lang->line("length"));
                                    $objWorkSheet->SetCellValue("A8", $this->lang->line("circumference"));
                                    $objWorkSheet->SetCellValue("A9", $this->lang->line("circumference_allowance"));
                                    $objWorkSheet->SetCellValue("A10", $this->lang->line("length_allowance"));
                                    $objWorkSheet->SetCellValue("A11", $this->lang->line("total_gross_volume"));
                                    $objWorkSheet->SetCellValue("A12", $this->lang->line("total_net_volume"));
                                    $objWorkSheet->SetCellValue("A13", $this->lang->line("text_cft"));

                                    $objWorkSheet->SetCellValue("B2", $containerdetails->container_number);
                                    $objWorkSheet->SetCellValue("B3", $containerdetails->dispatch_date);
                                    $objWorkSheet->SetCellValue("B4", $containerdetails->warehouse_name);
                                    // $objWorkSheet->SetCellValue("B9", ($companySettings[0]->circumference_allowance_export + 0));
                                    // $objWorkSheet->SetCellValue("B10", ($companySettings[0]->length_allowance_export + 0));

                                    $objWorkSheet->SetCellValue("B9", $circumferenceAllowance + 0);
                                    $objWorkSheet->SetCellValue("B10", ($lengthAllowance * 100) + 0);

                                    $objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objWorkSheet->getStyle("B13")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                    $objWorkSheet->getStyle("A2")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A3")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A4")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A5")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A6")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A7")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A8")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A9")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A10")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A11")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A12")->getFont()->setBold(true);
                                    $objWorkSheet->getStyle("A13")->getFont()->setBold(true);

                                    $objWorkSheet->getStyle("A2:B13")->applyFromArray($styleArray);

                                    $objWorkSheet->SetCellValue("A15", $this->lang->line("dispatch_pieces"));
                                    $objWorkSheet->SetCellValue("B15", $this->lang->line("circumference"));
                                    $objWorkSheet->SetCellValue("C15", $this->lang->line("length"));
                                    $objWorkSheet->SetCellValue("D15", $this->lang->line("gross_volume"));
                                    $objWorkSheet->SetCellValue("E15", $this->lang->line("net_volume"));

                                    $objWorkSheet->getStyle("A15:E15")->getFont()->setBold(true);
                                    $objWorkSheet->getRowDimension(15)->setRowHeight(30);
                                    $objWorkSheet->getStyle("A15:E15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objWorkSheet->getStyle("A15:E15")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    $objWorkSheet->getStyle("A15:E15")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
                                    $objWorkSheet->setAutoFilter("A15:E15");

                                    //END HEADER

                                    //DISPATCH DATA

                                    if ($exportOrderId <= 158 and $originId == 1) {
                                        $getDispatchData = $this->Export_model->get_container_dispatch_data_old($containerdetails->dispatch_id, $containerdetails->container_number, $originId);
                                    } else {
                                        $getDispatchData = $this->Export_model->get_container_dispatch_data($containerdetails->dispatch_id, $containerdetails->container_number, $originId);
                                    }



                                    $dispatchDataCount = 16;
                                    if (count($getDispatchData) > 0) {
                                        foreach ($getDispatchData as $dispatchdata) {

                                            if ($originId == 1 || $originId == 2) {
                                                if ($circumferenceAdjustment < 0) {
                                                    $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                    $grossFormula = "return (" . $grossFormula . ");";

                                                    $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    $netFormula = "return (" . $netFormula . ");";
                                                } else {
                                                    $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);

                                                    $grossFormula = "return (" . $grossFormula . ");";

                                                    $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    $netFormula = "return (" . $netFormula . ");";
                                                }
                                            } else {
                                                $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                $grossFormula = "return (" . $grossFormula . ");";

                                                $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                $netFormula = "return (" . $netFormula . ");";
                                            }

                                            $objWorkSheet->SetCellValue("A$dispatchDataCount", ($dispatchdata->scanned_code + 0));

                                            if ($originId == 1 || $originId == 2) {
                                                if ($circumferenceAdjustment < 0) {
                                                    $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought - abs($circumferenceAdjustment)));
                                                } else {
                                                    $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + $circumferenceAdjustment));
                                                }
                                            } else {
                                                $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + 0));
                                            }

                                            $objWorkSheet->SetCellValue("C$dispatchDataCount", ($dispatchdata->length_bought / 100));
                                            $objWorkSheet->getStyle("C$dispatchDataCount")->getNumberFormat()->setFormatCode('0.00');

                                            $objWorkSheet->SetCellValue("D$dispatchDataCount", sprintf('%0.3f', eval($grossFormula)));
                                            $objWorkSheet->SetCellValue("E$dispatchDataCount", sprintf('%0.3f', eval($netFormula)));

                                            $dispatchDataCount++;
                                        }
                                    }

                                    $lastRow = $dispatchDataCount - 1;
                                    $objWorkSheet->getStyle("D15:E$lastRow")
                                        ->getNumberFormat()
                                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                    $objWorkSheet->getStyle("A15:E$lastRow")->applyFromArray($styleArray);

                                    $objWorkSheet->getColumnDimension("A")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("B")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("C")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("D")->setAutoSize(true);
                                    $objWorkSheet->getColumnDimension("E")->setAutoSize(true);

                                    //END DATA

                                    //RENDER HEADER DATA

                                    $objWorkSheet->SetCellValue("B6", "=SUM(A16:A$lastRow)");

                                    $objWorkSheet->SetCellValue("B7", "=ROUND(SUMPRODUCT(C16:C$lastRow,A16:A$lastRow)/B6,2)");
                                    $objWorkSheet->getStyle("B7")->getNumberFormat()->setFormatCode('0.00');

                                    $objWorkSheet->SetCellValue("B8", "=TRUNC(SUMPRODUCT(B16:B$lastRow,A16:A$lastRow)/B6,0)");

                                    $objWorkSheet->SetCellValue("B11", "=SUM(D16:D$lastRow)");
                                    $objWorkSheet->getStyle("B11")->getNumberFormat()->setFormatCode('0.000');
                                    $objWorkSheet->SetCellValue("B12", "=SUM(E16:E$lastRow)");
                                    $objWorkSheet->getStyle("B12")->getNumberFormat()->setFormatCode('0.000');

                                    $objWorkSheet->SetCellValue("B13", "=ROUND(B11/B6*35.315,2)");
                                    $objWorkSheet->getStyle("B13")->getNumberFormat()->setFormatCode('0.00');

                                    //END RENDER HEADER DATA

                                    //SUMMARY DATA

                                    $objSheet->SetCellValue("A$summaryDataCount", $exportOrderDetail[0]->client_pno);
                                    $objSheet->SetCellValue("B$summaryDataCount", $containerdetails->dispatch_date);
                                    $objSheet->SetCellValue("C$summaryDataCount", $containerdetails->container_number);

                                    $objSheet->SetCellValue("D$summaryDataCount", "=IF(E$summaryDataCount=" . $emptyText . "," . $emptyText . ",IF(E$summaryDataCount<2.75," . $textShorts . ",IF(E$summaryDataCount<6," . $textSemi . "," . $textLongs . ")))");

                                    $objSheet->SetCellValue("E$summaryDataCount", "='$containerdetails->container_number'!B7");
                                    $objSheet->getStyle("E$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');

                                    $objSheet->SetCellValue("F$summaryDataCount", "='$containerdetails->container_number'!B8");

                                    $objSheet->SetCellValue("G$summaryDataCount", "='$containerdetails->container_number'!B11");
                                    $objSheet->getStyle("G$summaryDataCount")->getNumberFormat()->setFormatCode('0.000');

                                    $objSheet->SetCellValue("H$summaryDataCount", "='$containerdetails->container_number'!B12");
                                    $objSheet->getStyle("H$summaryDataCount")->getNumberFormat()->setFormatCode('0.000');

                                    $objSheet->SetCellValue("I$summaryDataCount", "='$containerdetails->container_number'!B6");

                                    $objSheet->SetCellValue("J$summaryDataCount", "='$containerdetails->container_number'!B13");
                                    $objSheet->getStyle("J$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');


                                    if ($containerdetails->container_pic_url != "" && $containerdetails->container_pic_url != null) {

                                        $objSheet->SetCellValue("K$summaryDataCount", $containerdetails->container_pic_url);
                                        $objSheet->getCell("K$summaryDataCount")->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
                                        $objSheet->getCell("K$summaryDataCount")->getHyperlink()->setUrl(strip_tags($containerdetails->container_pic_url));
                                        $objSheet->getStyle("K$summaryDataCount")->applyFromArray($link_style_array);
                                    } else {
                                        $objSheet->SetCellValue("K$summaryDataCount", "");
                                    }

                                    //END SUMMARY

                                    $sheetNo++;
                                    $summaryDataCount++;
                                }
                            }
                            //END CONTAINER SHEET
                        }

                        //END SUMMARY REPORT

                        $this->excel->setActiveSheetIndex(0);

                        if ($exportOrderDetail[0]->product_type_id == 1 || $exportOrderDetail[0]->product_type_id == 3) {

                            $lastRowSummary = $summaryDataCount - 1;
                            $objSheet->getStyle("A18:L$lastRowSummary")->applyFromArray($styleArray);

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
                            $objSheet->getColumnDimension("L")->setAutoSize(true);

                            $summaryDataCount = $summaryDataCount + 1;
                            $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                            $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->applyFromArray($styleArray);

                            $objSheet->SetCellValue("C$summaryDataCount", count($getContainerDetails) . " " . $this->lang->line("containers"));

                            $objSheet->SetCellValue("D$summaryDataCount", "=ROUND(AVERAGE(D18:D$lastRowSummary),2)");
                            $objSheet->SetCellValue("E$summaryDataCount", "=ROUND(AVERAGE(E18:E$lastRowSummary),2)");
                            $objSheet->SetCellValue("F$summaryDataCount", "=ROUND(AVERAGE(F18:F$lastRowSummary),2)");
                            $objSheet->SetCellValue("H$summaryDataCount", "=SUM(H18:H$lastRowSummary)");
                            $objSheet->SetCellValue("I$summaryDataCount", "=SUM(I18:I$lastRowSummary)");
                            $objSheet->SetCellValue("J$summaryDataCount", "=SUM(J18:J$lastRowSummary)");

                            $summaryDataCount = $summaryDataCount + 2;

                            $BStyle = array(
                                'borders' => array(
                                    'bottom' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                    )
                                )
                            );
                            $objSheet->getStyle("A$summaryDataCount:E$summaryDataCount")->applyFromArray($BStyle);
                            $summaryDataCount++;
                            $objSheet->mergeCells("A$summaryDataCount:E$summaryDataCount");
                            $objSheet->SetCellValue("A$summaryDataCount", $this->lang->line("seller_text"));
                            $objSheet->getStyle("A$summaryDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        } else {



                            $lastRowSummary = $summaryDataCount - 1;
                            $objSheet->getStyle("A18:K$lastRowSummary")->applyFromArray($styleArray);

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

                            $summaryDataCount = $summaryDataCount + 1;
                            $objSheet->getStyle("A$summaryDataCount:K$summaryDataCount")->getFont()->setBold(true);
                            $objSheet->getStyle("A$summaryDataCount:K$summaryDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                            $objSheet->getStyle("A$summaryDataCount:K$summaryDataCount")->applyFromArray($styleArray);

                            $objSheet->SetCellValue("C$summaryDataCount", count($getContainerDetails) . " " . $this->lang->line("containers"));

                            $objSheet->SetCellValue("E$summaryDataCount", "=ROUND(AVERAGE(E18:E$lastRowSummary), 2)");
                            $objSheet->getStyle("E$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');

                            $objSheet->SetCellValue("F$summaryDataCount", "=TRUNC(AVERAGE(F18:F$lastRowSummary), 0)");

                            $objSheet->SetCellValue("G$summaryDataCount", "=SUM(G18:G$lastRowSummary)");
                            $objSheet->getStyle("G$summaryDataCount")->getNumberFormat()->setFormatCode('0.000');

                            $objSheet->SetCellValue("H$summaryDataCount", "=SUM(H18:H$lastRowSummary)");
                            $objSheet->getStyle("H$summaryDataCount")->getNumberFormat()->setFormatCode('0.000');

                            $objSheet->SetCellValue("I$summaryDataCount", "=SUM(I18:I$lastRowSummary)");

                            $objSheet->SetCellValue("J$summaryDataCount", "=ROUND(AVERAGE(J18:J$lastRowSummary),2)");
                            $objSheet->getStyle("J$summaryDataCount")->getNumberFormat()->setFormatCode('0.00');

                            $summaryDataCount = $summaryDataCount + 2;

                            $BStyle = array(
                                'borders' => array(
                                    'bottom' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                    )
                                )
                            );
                            $objSheet->getStyle("A$summaryDataCount:E$summaryDataCount")->applyFromArray($BStyle);
                            $summaryDataCount++;
                            $objSheet->mergeCells("A$summaryDataCount:E$summaryDataCount");
                            $objSheet->SetCellValue("A$summaryDataCount", $this->lang->line("seller_text"));
                            $objSheet->getStyle("A$summaryDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        }
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($link_style_array);
                    unset($styleArray);
                    unset($BStyle);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "ExportSummaryReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/ExportReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/ExportReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("common_error");
                    $Return["messagetype"] = "info";
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

    public function generate_proforma_invoice()
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "",
                "error" => "",
                "redirect" => false,
                "csrf_hash" => "",
                "successmessage" => ""
            );

            if (!empty($session)) {
                
                $this->deletefilesfromfolder();

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originId = $this->input->post("originid");
                $saNumber = $this->input->post("sanumber");
                $originalSANumber = $this->input->post("sanumber");
                $exportOrderId = $this->input->post("exportid");
                $buyerId = $this->input->post("buyerid");
                $bankId = $this->input->post("bankid");
                $sellerId = $this->input->post("sellerid");
                $invoiceDate = $this->input->post("invoicedate");
                $updateBuyerLedger = $this->input->post("update_buyer_ledger");

                $getBuyerDetails = $this->Master_model->get_buyers_details_by_id($buyerId);
                $getBankDetails = $this->Master_model->get_bank_details_by_id($bankId);
                $getSellerDetails = $this->Master_model->get_seller_details_by_id($sellerId);

                if ($originId == 3) {
                    $getExportDetails = $this->Export_model->get_export_details_invoice_id($exportOrderId);
                } else {
                    $getExportDetails = $this->Export_model->get_export_details_invoice_export_origin($exportOrderId);
                }

                $arrBuyerAddress = explode("\n", $getBuyerDetails[0]->address);

                if ($originId == 3) {
                    $saNumber = $getExportDetails[0]->booking_no . "-" . str_replace("/", "-", $saNumber);
                } else {
                    $saNumber = str_replace("/", "-", $saNumber);
                }
                
                $accountingInvoice = $this->input->post("accountinginvoice");

                $objExcel = PHPExcel_IOFactory::createReader('Excel2007');
                // if ($originId == 3) {
                //     $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_USA.xlsx');
                // } else {
                //     if($bankId == 5) {
                //         $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Singapore.xlsx');
                //     } else {
                //         $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Colombia.xlsx');
                //     } 
                // }

                if ($originId == 1 && $accountingInvoice == 1) {
                    $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Colombia_AcccoutingInvoice.xlsx');
                } else {
                    $objExcel = $objExcel->load('./assets/templates/' . $getSellerDetails[0]->template_file);
                }
                

                $objExcel->setActiveSheetIndex(0);
                $objSheet = $objExcel->getActiveSheet();

                //BUYER DETAILS
                $buyerRowCount = 11;
                $objSheet->setCellValue("B$buyerRowCount", $getBuyerDetails[0]->buyer_name);

                foreach ($arrBuyerAddress as $buyeraddress) {
                    $buyerRowCount = $buyerRowCount + 1;
                    $objSheet->setCellValue("B$buyerRowCount", $buyeraddress);
                }

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Contact : " . $getBuyerDetails[0]->contact_name);

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Phone : " . $getBuyerDetails[0]->contact_no);

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Email : " . $getBuyerDetails[0]->email);

                //EXPORT ITEMS
                if ($originId == 3) {

                    $circAllowance = 0;
                    $lengthAllowance = 0;
                    $circAdjustment = 0;
                    $measurementsystem = $this->input->post("measurementsystem");

                    $serviceSales = $this->input->post("servicesales");
                    $servicesalesPercentage = $this->input->post("servicesales_percentage");
                    $salesAdvance = $this->input->post("salesadvance");
                    $salesAdvanceCost = $this->input->post("salesadvance_cost");
                    
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $this->input->post("creditnotes");

                    $priceShortsBase = 0;
                    $enableJumpShortsBool = false;
                    $priceSemiBase = 0;
                    $enableJumpSemiBool = false;
                    $priceLongsBase = 0;
                    $enableJumpLongsBool = false;
                    
                    $excelVolumeValue = 0;
                    $excelInvoicePrice = 0;

                    $objSheet->setCellValue('C8', "INVOICE - CGUS-SYP-$saNumber");

                    //EXPORT DETAILS

                    $todayDate = date($invoiceDate);

                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", $getExportDetails[0]->origin_name);
                    $objSheet->setCellValue("G13", $getExportDetails[0]->pol);
                    $objSheet->setCellValue("G14", $getExportDetails[0]->pod);

                    $getExportItemDetails = $this->Export_model->get_export_items_by_exportid($exportOrderId);
                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;

                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;
                        foreach ($getExportItemDetails as $exportitem) {
                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $objSheet->setCellValue("B$exportItemsRowCount", $exportitem->total_containers);
                            $objSheet->setCellValue("C$exportItemsRowCount", $exportitem->description);
                            $objSheet->setCellValue("D$exportItemsRowCount", $exportitem->metric_ton);
                            $objSheet->setCellValue("E$exportItemsRowCount", "Metric Tonne");

                            $objSheet->setCellValue("F$exportItemsRowCount", $exportitem->unit_price);
                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + $exportitem->metric_ton * $exportitem->unit_price;
                            $totalInvoiceVolume = $totalInvoiceVolume + $exportitem->metric_ton;
                        }
                    }

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("G40", "=SUM(G20:G39)");
                    $objSheet->setCellValue("F40", "=G40/D40");

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($salesAdvance == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("G$lastRowDetails", "=COUNT(G20:G39)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($serviceSales == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("G$lastRowDetails", "=SUM(G20:G39)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("G$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if ($accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        $exportItemsRowCount = 19;
                        foreach ($getExportItemDetails as $exportitem) {
                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;
                            
                            $objSheet->setCellValue("B$exportItemsRowCount", $exportitem->total_containers);
                            $objSheet->setCellValue("C$exportItemsRowCount", $exportitem->description);
                            $objSheet->setCellValue("D$exportItemsRowCount", $exportitem->metric_ton);
                            $objSheet->setCellValue("E$exportItemsRowCount", "Metric Tonne");

                            $containerUPrice = ($exportitem->unit_price - $accountingInvoiceUnitPrice) + 0;
                            $objSheet->setCellValue("F$exportItemsRowCount", $containerUPrice);

                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + $exportitem->metric_ton * $containerUPrice;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    //$num = 15786.05;
                    //$numberToWords = $this->convertNumber(round($totalValue, 2));
                    //$objSheet->setCellValue("C37", "US Dollars " . str_replace("  ", " ", $numberToWords) . " Cents Only");

                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('G40')->getCalculatedValue());
                    $excelVolumeValue = round($objSheet->getCell('D40')->getCalculatedValue(), 3);
                    $excelInvoicePrice = $objSheet->getCell('F40')->getCalculatedValue();

                    $tempNum = explode('.', $excelInvoiceValue);
                    
                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' With ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' Cents' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", ucwords($convertedNumber)) . " Only");

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                } else if($originId == 1 && $accountingInvoice == 1) {
                    
                    $serviceSales = $this->input->post("servicesales");
                    $servicesalesPercentage = $this->input->post("servicesales_percentage");
                    $salesAdvance = $this->input->post("salesadvance");
                    $salesAdvanceCost = $this->input->post("salesadvance_cost");
                    //$accountingInvoice = $this->input->post("accountinginvoice");
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $this->input->post("creditnotes");

                    date_default_timezone_set($getExportDetails[0]->timezone_abbreviation);
                    $invoiceBuyerName = $getBuyerDetails[0]->invoice_name;
                    $objSheet->setCellValue('C8', "INVOICE -$invoiceBuyerName-$saNumber");

                    //EXPORT DETAILS

                    $todayDate = date($invoiceDate);

                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", strtoupper($getExportDetails[0]->origin_name));
                    $objSheet->setCellValue("G13", strtoupper($getExportDetails[0]->pol));
                    $objSheet->setCellValue("G14", strtoupper($getExportDetails[0]->pod));

                    $enableJumpShorts = $this->input->post("enablejump_shorts");
                    $enableJumpSemi = $this->input->post("enablejump_semi");
                    $enableJumpLongs = $this->input->post("enablejump_longs");
                    $priceShortsBase = $this->input->post("priceshorts");
                    $priceSemiBase = $this->input->post("pricesemi");
                    $priceLongsBase = $this->input->post("pricelongs");

                    //SHORTS
                    $shortsArray = array();
                    $priceShorts = $this->input->post("priceshorts");
                    if ($priceShorts > 0) {
                        for ($i = 1.75; $i >= 1; $i -= 0.25) {
                            $shortsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 0.24,
                                'rangePrice' => $priceShorts - 15,
                            );

                            $priceShorts = $priceShorts - 15;
                        }

                        $priceShorts = $this->input->post("priceshorts");
                        for ($i = 2; $i <= 20; $i += 0.25) {

                            if ($i == 5) {

                                if ($enableJumpShorts == 1) {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts + 20,
                                    );

                                    $priceShorts = $priceShorts + 15 + 20;
                                } else {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts,
                                    );

                                    $priceShorts = $priceShorts + 15;
                                }
                            } else {
                                $shortsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 0.24,
                                    'rangePrice' => $priceShorts,
                                );

                                $priceShorts = $priceShorts + 15;
                            }
                        }
                    }

                    //SEMI
                    $semiArray = array();
                    $priceSemi = $this->input->post("pricesemi");
                    if ($priceSemi > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {

                            $semiArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceSemi - 50,
                            );

                            $priceSemi = $priceSemi - 50;
                        }

                        $priceSemi = $this->input->post("pricesemi");
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {

                                if ($enableJumpSemi == 1) {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi + 20,
                                    );

                                    $priceSemi = $priceSemi + 30 + 20;
                                } else {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi,
                                    );

                                    $priceSemi = $priceSemi + 30;
                                }
                            } else {
                                $semiArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceSemi,
                                );

                                $priceSemi = $priceSemi + 30;
                            }
                        }
                    }

                    //LONGS
                    $longsArray = array();
                    $priceLongs = $this->input->post("pricelongs");
                    if ($priceLongs > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {
                            $longsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceLongs - 50,
                            );

                            $priceLongs = $priceLongs - 50;
                        }

                        $priceLongs = $this->input->post("pricelongs");
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {
                                if ($enableJumpLongs == 1) {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs + 20,
                                    );

                                    $priceLongs = $priceLongs + 30 + 20;
                                } else {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs,
                                    );

                                    $priceLongs = $priceLongs + 30;
                                }
                            } else {
                                $longsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceLongs,
                                );

                                $priceLongs = $priceLongs + 30;
                            }
                        }
                    }

                    $circAllowance = $this->input->post("circumferenceallowance");
                    $lengthAllowance = $this->input->post("lengthallowance");
                    $circAdjustment = $this->input->post("circumferenceadjustment");
                    $measurementsystem = $this->input->post("measurementsystem");

                    $getExportItemDetails = $this->Export_model->get_export_data_by_export_id($exportOrderId, $originalSANumber, $originId, $circAllowance, $lengthAllowance, $circAdjustment, $measurementsystem);

                    $totalValue = 0;
                    $containerItemArray = array();
                    $containerNumberArray = array();

                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($getExportItemDetails as $exportitem) {

                            array_push($containerNumberArray, $exportitem->container_number);

                            if ($exportitem->product_type == 1) {
                                if ($priceShorts > 0) {
                                    $containerUnitPrice = 0;
                                    if (count($shortsArray) > 0) {
                                        foreach ($shortsArray as $shortprice) {

                                            if (($shortprice["minRange"] <= $exportitem->gross_cft) && ($shortprice["maxRange"] >= $exportitem->gross_cft)) {
                                                $containerUnitPrice = $shortprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $shortprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $shortprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 2) {
                                if ($priceSemi > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($semiArray) > 0) {
                                        foreach ($semiArray as $semiprice) {

                                            if (($semiprice["minRange"] <= $exportitem->avg_circumference) && ($semiprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $semiprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $semiprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $semiprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 3) {
                                if ($priceLongs > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($longsArray) > 0) {
                                        foreach ($longsArray as $longprice) {

                                            if (($longprice["minRange"] <= $exportitem->avg_circumference) && ($longprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $longprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $longprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $longprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else {
                                //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                            }

                            //$objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            //$objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                        }
                    }

                    $duplicateContainers = array_diff_assoc($containerNumberArray, array_unique($containerNumberArray));
                    $containerFinalArray = array();

                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;
                    if (count($containerItemArray) > 0) {
                        foreach ($containerItemArray as $containerdata) {
                            if (count($duplicateContainers) > 0) {
                                foreach ($duplicateContainers as $duplicatecontainer) {
                                    if ($containerdata["containerNumber"] == $duplicatecontainer) {
                                        //DO NOTHING
                                    } else {
                                        $containerFinalArray[] = array(
                                            "containerNumber" => $containerdata["containerNumber"],
                                            "unitPrice" => $containerdata["unitPrice"] + 0,
                                            "volume" => $containerdata["volume"] + 0,
                                            "pieces" => $containerdata["pieces"] + 0,
                                        );

                                        break;
                                    }
                                }
                            } else {
                                $containerFinalArray[] = array(
                                    "containerNumber" => $containerdata["containerNumber"],
                                    "unitPrice" => $containerdata["unitPrice"] + 0,
                                    "volume" => $containerdata["volume"] + 0,
                                    "pieces" => $containerdata["pieces"] + 0,
                                );
                            }
                        }
                    }

                    $containerDataArray = array();
                    if (count($duplicateContainers) > 0) {
                        $containerDuplicateDataArray = array();
                        foreach ($duplicateContainers as $containernum) {
                            $totalVolume = 0;
                            $totalValue = 0;
                            $unitPrice = 0;
                            $totalPieces = 0;
                            foreach ($containerItemArray as $containeritem) {
                                if ($containeritem["containerNumber"] == $containernum) {
                                    $totalVolume = $totalVolume + $containeritem["volume"];
                                    $totalValue = $totalValue + ($containeritem["volume"] * $containeritem["unitPrice"]);
                                    $totalPieces = $totalPieces + $containeritem["pieces"];
                                }
                            }

                            if ($totalVolume > 0 && $totalValue > 0) {
                                $unitPrice = $totalValue / $totalVolume;
                            }

                            $containerDuplicateDataArray[] = array(
                                "containerNumber" => $containernum,
                                "unitPrice" => $unitPrice + 0,
                                "volume" => $totalVolume + 0,
                                "pieces" => $totalPieces + 0,
                            );
                        }

                        if (count($containerFinalArray) > 0) {
                            foreach ($containerFinalArray as $containerfinal) {
                                if (array_search($containerfinal["containerNumber"], $duplicateContainers) !== false) {
                                    //DO NOTHING
                                } else {
                                    $containerDataArray[] = array(
                                        "containerNumber" => $containerfinal["containerNumber"],
                                        "unitPrice" => $containerfinal["unitPrice"] + 0,
                                        "volume" => $containerfinal["volume"] + 0,
                                        "pieces" => $containerfinal["pieces"] + 0,
                                    );
                                }
                            }
                        }

                        $containerDataArray = array_merge($containerDataArray, $containerDuplicateDataArray);
                    } else {
                        $containerDataArray = array_merge($containerDataArray, $containerFinalArray);
                    }

                    if (count($containerDataArray) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            if ($accountingInvoice == 0) {

                                $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                                $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                                $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                                $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                                $objSheet->setCellValue("E$exportItemsRowCount", "CBM");

                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $containerno["containerPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);

                                            break;
                                        } else {
                                            $containerUPrice = $container["unitPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $containerUPrice = $container["unitPrice"] + 0;
                                    $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                    //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                    //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }

                                $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                            }

                            $totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;

                            if ($accountingInvoice == 1) {
                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);
                                            break;
                                        } else {
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }
                            } else {
                                $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                            }
                        }
                    }

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($salesAdvance == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("H$lastRowDetails", "=COUNT(H20:H$exportItemsRowCount)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($serviceSales == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("H$lastRowDetails", "=SUM(H20:H$exportItemsRowCount)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("H$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if (count($containerDataArray) > 0 && $accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;
                            
                            $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                            $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                            $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                            $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                            $objSheet->setCellValue("E$exportItemsRowCount", $container["pieces"] + 0);
                            $objSheet->getStyle("E$exportItemsRowCount")->getNumberFormat()->setFormatCode('0');
                            $objSheet->setCellValue("F$exportItemsRowCount", "CBM");

                            if (count($updateContainerNumberDataJson) > 0) {
                                foreach ($updateContainerNumberDataJson as $containerno) {
                                    if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                        $containerUPrice = ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("G$exportItemsRowCount", ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0);
                                        break;
                                    } else {
                                        $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("G$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                                    }
                                }
                            } else {
                                $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                $objSheet->setCellValue("G$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                            }

                            $objSheet->getStyle("G$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            $objSheet->setCellValue("H$exportItemsRowCount", "=D$exportItemsRowCount*G$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerUPrice) + 0;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("E40", "=SUM(E20:E39)");
                    $objSheet->setCellValue("H40", "=SUM(H20:H39)");
                    $objSheet->setCellValue("G40", "=H40/D40");

                    //if ($totalInvoiceValue > 0) {

                    //$excelInvoiceValue = round($objSheet->getCell('G40')->getCalculatedValue(), 2);
                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('H40')->getCalculatedValue());
                    $excelVolumeValue = round($objSheet->getCell('D40')->getCalculatedValue(), 3);
                    $excelInvoicePrice = $objSheet->getCell('G40')->getCalculatedValue();

                    $tempNum = explode('.', $excelInvoiceValue);

                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' WITH ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' CENTS' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", strtoupper($convertedNumber)) . " ONLY");
                    //}

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                    
                } else {

                    $serviceSales = $this->input->post("servicesales");
                    $servicesalesPercentage = $this->input->post("servicesales_percentage");
                    $salesAdvance = $this->input->post("salesadvance");
                    $salesAdvanceCost = $this->input->post("salesadvance_cost");
                    //$accountingInvoice = $this->input->post("accountinginvoice");
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $this->input->post("creditnotes");

                    date_default_timezone_set($getExportDetails[0]->timezone_abbreviation);
                    $invoiceBuyerName = $getBuyerDetails[0]->invoice_name;
                    $objSheet->setCellValue('C8', "INVOICE -$invoiceBuyerName-$saNumber");

                    //EXPORT DETAILS

                    $todayDate = date($invoiceDate);

                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", strtoupper($getExportDetails[0]->origin_name));
                    $objSheet->setCellValue("G13", strtoupper($getExportDetails[0]->pol));
                    $objSheet->setCellValue("G14", strtoupper($getExportDetails[0]->pod));

                    $enableJumpShorts = $this->input->post("enablejump_shorts");
                    $enableJumpSemi = $this->input->post("enablejump_semi");
                    $enableJumpLongs = $this->input->post("enablejump_longs");
                    $priceShortsBase = $this->input->post("priceshorts");
                    $priceSemiBase = $this->input->post("pricesemi");
                    $priceLongsBase = $this->input->post("pricelongs");

                    //SHORTS
                    $shortsArray = array();
                    $priceShorts = $this->input->post("priceshorts");
                    if ($priceShorts > 0) {
                        for ($i = 1.75; $i >= 1; $i -= 0.25) {
                            $shortsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 0.24,
                                'rangePrice' => $priceShorts - 15,
                            );

                            $priceShorts = $priceShorts - 15;
                        }

                        $priceShorts = $this->input->post("priceshorts");
                        for ($i = 2; $i <= 20; $i += 0.25) {

                            if ($i == 5) {

                                if ($enableJumpShorts == 1) {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts + 20,
                                    );

                                    $priceShorts = $priceShorts + 15 + 20;
                                } else {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts,
                                    );

                                    $priceShorts = $priceShorts + 15;
                                }
                            } else {
                                $shortsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 0.24,
                                    'rangePrice' => $priceShorts,
                                );

                                $priceShorts = $priceShorts + 15;
                            }
                        }
                    }

                    //SEMI
                    $semiArray = array();
                    $priceSemi = $this->input->post("pricesemi");
                    if ($priceSemi > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {

                            $semiArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceSemi - 50,
                            );

                            $priceSemi = $priceSemi - 50;
                        }

                        $priceSemi = $this->input->post("pricesemi");
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {

                                if ($enableJumpSemi == 1) {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi + 20,
                                    );

                                    $priceSemi = $priceSemi + 30 + 20;
                                } else {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi,
                                    );

                                    $priceSemi = $priceSemi + 30;
                                }
                            } else {
                                $semiArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceSemi,
                                );

                                $priceSemi = $priceSemi + 30;
                            }
                        }
                    }

                    //LONGS
                    $longsArray = array();
                    $priceLongs = $this->input->post("pricelongs");
                    if ($priceLongs > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {
                            $longsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceLongs - 50,
                            );

                            $priceLongs = $priceLongs - 50;
                        }

                        $priceLongs = $this->input->post("pricelongs");
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {
                                if ($enableJumpLongs == 1) {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs + 20,
                                    );

                                    $priceLongs = $priceLongs + 30 + 20;
                                } else {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs,
                                    );

                                    $priceLongs = $priceLongs + 30;
                                }
                            } else {
                                $longsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceLongs,
                                );

                                $priceLongs = $priceLongs + 30;
                            }
                        }
                    }

                    $circAllowance = $this->input->post("circumferenceallowance");
                    $lengthAllowance = $this->input->post("lengthallowance");
                    $circAdjustment = $this->input->post("circumferenceadjustment");
                    $measurementsystem = $this->input->post("measurementsystem");

                    $getExportItemDetails = $this->Export_model->get_export_data_by_export_id($exportOrderId, $originalSANumber, $originId, $circAllowance, $lengthAllowance, $circAdjustment, $measurementsystem);

                    $totalValue = 0;
                    $containerItemArray = array();
                    $containerNumberArray = array();

                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($getExportItemDetails as $exportitem) {

                            array_push($containerNumberArray, $exportitem->container_number);

                            if ($exportitem->product_type == 1) {
                                if ($priceShorts > 0) {
                                    $containerUnitPrice = 0;
                                    if (count($shortsArray) > 0) {
                                        foreach ($shortsArray as $shortprice) {

                                            if (($shortprice["minRange"] <= $exportitem->gross_cft) && ($shortprice["maxRange"] >= $exportitem->gross_cft)) {
                                                $containerUnitPrice = $shortprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $shortprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $shortprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 2) {
                                if ($priceSemi > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($semiArray) > 0) {
                                        foreach ($semiArray as $semiprice) {

                                            if (($semiprice["minRange"] <= $exportitem->avg_circumference) && ($semiprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $semiprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $semiprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $semiprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 3) {
                                if ($priceLongs > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($longsArray) > 0) {
                                        foreach ($longsArray as $longprice) {

                                            if (($longprice["minRange"] <= $exportitem->avg_circumference) && ($longprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $longprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $longprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $longprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else {
                                //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                            }

                            //$objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            //$objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                        }
                    }

                    $duplicateContainers = array_diff_assoc($containerNumberArray, array_unique($containerNumberArray));
                    $containerFinalArray = array();

                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;
                    if (count($containerItemArray) > 0) {
                        foreach ($containerItemArray as $containerdata) {
                            if (count($duplicateContainers) > 0) {
                                foreach ($duplicateContainers as $duplicatecontainer) {
                                    if ($containerdata["containerNumber"] == $duplicatecontainer) {
                                        //DO NOTHING
                                    } else {
                                        $containerFinalArray[] = array(
                                            "containerNumber" => $containerdata["containerNumber"],
                                            "unitPrice" => $containerdata["unitPrice"] + 0,
                                            "volume" => $containerdata["volume"] + 0,
                                        );

                                        break;
                                    }
                                }
                            } else {
                                $containerFinalArray[] = array(
                                    "containerNumber" => $containerdata["containerNumber"],
                                    "unitPrice" => $containerdata["unitPrice"] + 0,
                                    "volume" => $containerdata["volume"] + 0,
                                );
                            }
                        }
                    }

                    $containerDataArray = array();
                    if (count($duplicateContainers) > 0) {
                        $containerDuplicateDataArray = array();
                        foreach ($duplicateContainers as $containernum) {
                            $totalVolume = 0;
                            $totalValue = 0;
                            $unitPrice = 0;
                            foreach ($containerItemArray as $containeritem) {
                                if ($containeritem["containerNumber"] == $containernum) {
                                    $totalVolume = $totalVolume + $containeritem["volume"];
                                    $totalValue = $totalValue + ($containeritem["volume"] * $containeritem["unitPrice"]);
                                }
                            }

                            if ($totalVolume > 0 && $totalValue > 0) {
                                $unitPrice = $totalValue / $totalVolume;
                            }

                            $containerDuplicateDataArray[] = array(
                                "containerNumber" => $containernum,
                                "unitPrice" => $unitPrice + 0,
                                "volume" => $totalVolume + 0,
                            );
                        }

                        if (count($containerFinalArray) > 0) {
                            foreach ($containerFinalArray as $containerfinal) {
                                if (array_search($containerfinal["containerNumber"], $duplicateContainers) !== false) {
                                    //DO NOTHING
                                } else {
                                    $containerDataArray[] = array(
                                        "containerNumber" => $containerfinal["containerNumber"],
                                        "unitPrice" => $containerfinal["unitPrice"] + 0,
                                        "volume" => $containerfinal["volume"] + 0,
                                    );
                                }
                            }
                        }

                        $containerDataArray = array_merge($containerDataArray, $containerDuplicateDataArray);
                    } else {
                        $containerDataArray = array_merge($containerDataArray, $containerFinalArray);
                    }
                    
                    if (count($containerDataArray) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            if ($accountingInvoice == 0) {

                                $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                                $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                                $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                                $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                                $objSheet->setCellValue("E$exportItemsRowCount", "CBM");

                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $containerno["containerPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);

                                            break;
                                        } else {
                                            $containerUPrice = $container["unitPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $containerUPrice = $container["unitPrice"] + 0;
                                    $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                    //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                    //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }

                                $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                            }

                            $totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;

                            if ($accountingInvoice == 1) {
                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);
                                            break;
                                        } else {
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }
                            } else {
                                $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                            }
                        }
                    }

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($salesAdvance == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("G$lastRowDetails", "=COUNT(G20:G$exportItemsRowCount)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($serviceSales == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("G$lastRowDetails", "=SUM(G20:G$exportItemsRowCount)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("G$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if (count($containerDataArray) > 0 && $accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                            $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                            $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                            $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                            $objSheet->setCellValue("E$exportItemsRowCount", "CBM");


                            if (count($updateContainerNumberDataJson) > 0) {
                                foreach ($updateContainerNumberDataJson as $containerno) {
                                    if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                        $containerUPrice = ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("F$exportItemsRowCount", ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0);
                                        break;
                                    } else {
                                        $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("F$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                                    }
                                }
                            } else {
                                $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                $objSheet->setCellValue("F$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                            }

                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerUPrice) + 0;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("G40", "=SUM(G20:G39)");
                    $objSheet->setCellValue("F40", "=G40/D40");

                    //if ($totalInvoiceValue > 0) {

                    //$excelInvoiceValue = round($objSheet->getCell('G40')->getCalculatedValue(), 2);
                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('G40')->getCalculatedValue());
                    $excelVolumeValue = round($objSheet->getCell('D40')->getCalculatedValue(), 3);
                    $excelInvoicePrice = $objSheet->getCell('F40')->getCalculatedValue();

                    $tempNum = explode('.', $excelInvoiceValue);

                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' WITH ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' CENTS' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", strtoupper($convertedNumber)) . " ONLY");
                    //}

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                }

                $saNumber = str_replace("/", "_", $saNumber);
                $month_name = ucfirst(date("dmY"));

                //DATA ADDING

                if ($creditnotes != null && $creditnotes != "") {
                    $creditNotesArray = explode(",", $creditnotes);
                    if (count($creditNotesArray) > 0) {
                        foreach ($creditNotesArray as $creditnoteval) {
                            $dataUpdateCreditClaim = array(
                                "is_claimed" => true,
                                "updated_by" => $session['user_id'],
                            );
                            $updateCreditClaim = $this->Claimtracker_model->update_credit_note_claims($dataUpdateCreditClaim, $creditnoteval);
                        }
                    }
                }

                $serviceSalesBool = false;
                if ($serviceSales == 1) {
                    $serviceSalesBool = true;
                }

                $advanceCostBool = false;
                if ($salesAdvance == 1) {
                    $advanceCostBool = true;
                }

                $accountingInvoiceBool = false;
                if ($accountingInvoice == 1) {
                    $accountingInvoiceBool = true;
                }

                $enableJumpShortsBool = false;
                if ($enableJumpShorts == 1) {
                    $enableJumpShortsBool = true;
                }

                $enableJumpSemiBool = false;
                if ($enableJumpSemi == 1) {
                    $enableJumpSemiBool = true;
                }

                $enableJumpLongsBool = false;
                if ($enableJumpLongs == 1) {
                    $enableJumpLongsBool = true;
                }

                if($originId == 3) {
                    $dataExportInvoiceHistory = array(
                        "export_id" => $exportOrderId,
                        "invoice_date" => $invoiceDate,
                        "total_containers" => $getExportDetails[0]->d_total_containers,
                        "circ_allowance" => $circAllowance,
                        "length_allowance" => $lengthAllowance,
                        "circ_adjustment" => $circAdjustment,
                        "measurement_system" => $measurementsystem,
                        "service_enabled" => $serviceSalesBool,
                        "service_sales_percentage" => $servicesalesPercentage,
                        "total_service_cost" => $salesServiceCost,
                        "advance_enabled" => $advanceCostBool,
                        "advance_cost" => $salesAdvanceCost,
                        "total_advance_cost" => $advanceCost,
                        "accounting_invoice" => $accountingInvoiceBool,
                        "claim_id" => $creditnotes,
                        "claim_amount" => abs($claimCost) + 0,
                        "shorts_base_price" => $priceShortsBase,
                        "enabled_jump_shorts" => $enableJumpShortsBool,
                        "semi_base_price" => $priceSemiBase,
                        "enabled_jump_semi" => $enableJumpSemiBool,
                        "long_base_price" => $priceLongsBase,
                        "enabled_jump_long" => $enableJumpLongsBool,
                        "credit_notes" => $creditnotes,
                        "buyer_id" => $buyerId,
                        "bank_id" => $bankId,
                        "seller_id" => $sellerId,
                        "total_volume" => $totalInvoiceVolume,
                        "invoice_unit_price" => $excelInvoicePrice,
                        "total_invoice_value" => $totalInvoiceVolume * $excelInvoicePrice,
                        "total_sales_value" => ($totalInvoiceVolume * $excelInvoicePrice) + (abs($claimCost) + 0) + $advanceCost + $salesServiceCost,
                        "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'],
                        'is_active' => 1,
                    );
                } else {
                    $dataExportInvoiceHistory = array(
                    "export_id" => $exportOrderId,
                    "invoice_date" => $invoiceDate,
                    "total_containers" => $getExportDetails[0]->d_total_containers,
                    "circ_allowance" => $circAllowance,
                    "length_allowance" => $lengthAllowance,
                    "circ_adjustment" => $circAdjustment,
                    "measurement_system" => $measurementsystem,
                    "service_enabled" => $serviceSalesBool,
                    "service_sales_percentage" => $servicesalesPercentage,
                    "total_service_cost" => $salesServiceCost,
                    "advance_enabled" => $advanceCostBool,
                    "advance_cost" => $salesAdvanceCost,
                    "total_advance_cost" => $advanceCost,
                    "accounting_invoice" => $accountingInvoiceBool,
                    "claim_id" => $creditnotes,
                    "claim_amount" => abs($claimCost) + 0,
                    "shorts_base_price" => $priceShortsBase,
                    "enabled_jump_shorts" => $enableJumpShortsBool,
                    "semi_base_price" => $priceSemiBase,
                    "enabled_jump_semi" => $enableJumpSemiBool,
                    "long_base_price" => $priceLongsBase,
                    "enabled_jump_long" => $enableJumpLongsBool,
                    "credit_notes" => $creditnotes,
                    "buyer_id" => $buyerId,
                    "bank_id" => $bankId,
                    "seller_id" => $sellerId,
                    "total_volume" => $excelVolumeValue,
                    "invoice_unit_price" => $excelInvoicePrice,
                    "total_invoice_value" => $excelVolumeValue * $excelInvoicePrice,
                    "total_sales_value" => ($excelVolumeValue * $excelInvoicePrice) + (abs($claimCost) + 0) + $advanceCost + $salesServiceCost,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );
                }

                

                $insertExportInvoice = $this->Export_model->add_export_invoice_history($dataExportInvoiceHistory);

                if ($insertExportInvoice > 0) {
                    if (count($updateContainerNumberDataJson) > 0) {
                        foreach ($updateContainerNumberDataJson as $containerno) {
                            $dataExportContainerCustomPrice = array(
                                "export_id" => $exportOrderId,
                                "export_invoice_id" => $insertExportInvoice,
                                "container_number" => $containerno["containerNumber"],
                                "unit_price" => $containerno["containerPrice"],
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                'is_active' => 1,
                            );

                            $insertExportContainer = $this->Export_model->add_export_container_price($dataExportContainerCustomPrice);
                        }
                    }
                }
                
                //DELETE EXISTING LEDGER
                
                if ($updateBuyerLedger == 1) {

                    $deleteSalesBuyer = $this->Export_model->update_sales_buyer_ledger($exportOrderId, $session['user_id']);
    
                    if ($deleteSalesBuyer) {
                        
                        if($originId == 3) {
                            
                            $dataSalesBuyerLedger = array(
                                "buyer_id" => $buyerId,
                                "export_id" => $exportOrderId,
                                "ledger_type" => 2,
                                "invoice_date" => $invoiceDate,
                                "total_sales_value" => ($totalInvoiceVolume * $excelInvoicePrice) + (abs($claimCost) + 0) + $advanceCost + $salesServiceCost,
                                "total_advance_cost" => $advanceCost,
                                "total_service_cost" => $salesServiceCost,
                                "claim_amount" => abs($claimCost) + 0,
                                "total_invoice_value" => $totalInvoiceVolume * $excelInvoicePrice,
                                "total_containers" => $getExportDetails[0]->d_total_containers,
                                "total_volume" => $totalInvoiceVolume,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                'is_active' => 1,
                            );
                            
                        } else {
                            $dataSalesBuyerLedger = array(
                                "buyer_id" => $buyerId,
                                "export_id" => $exportOrderId,
                                "ledger_type" => 2,
                                "invoice_date" => $invoiceDate,
                                "total_sales_value" => ($excelVolumeValue * $excelInvoicePrice) + (abs($claimCost) + 0) + $advanceCost + $salesServiceCost,
                                "total_advance_cost" => $advanceCost,
                                "total_service_cost" => $salesServiceCost,
                                "claim_amount" => abs($claimCost) + 0,
                                "total_invoice_value" => $excelVolumeValue * $excelInvoicePrice,
                                "total_containers" => $getExportDetails[0]->d_total_containers,
                                "total_volume" => $excelVolumeValue,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                'is_active' => 1,
                            );
                        }
                        
                        
    
                        $insertSalesLedger = $this->Export_model->add_sales_buyer_ledger($dataSalesBuyerLedger);
                    }
                
                }

                //END DATA ADDING

                $filename =  "Proforma_Invoice_" . $saNumber . "_" . $month_name . ".xlsx";

                $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
                $objWriter->save("./reports/ExportReports/" . $filename);
                $objWriter->setPreCalculateFormulas(true);
                $Return["error"] = "";
                $Return["result"] = site_url() . "reports/ExportReports/" . $filename;
                $Return["successmessage"] = $this->lang->line("report_downloaded");
                if ($Return["result"] != "") {
                    $this->output($Return);
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

    public function dialog_proforma_invoice_option()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->post("type") == "generateinvoice") {

                $originId = $this->input->post("origin_id");
                $saNumber = $this->input->post("sa_number");
                $exportOrderId = $this->input->post("export_order_id");

                //CHECK EXPORT DETAILS
                $exportOrderDetail = $this->Export_model->get_export_order_details_by_id($exportOrderId, $saNumber, $originId);
                if (count($exportOrderDetail) == 1) {

                    $data = array(
                        "pageheading" => $this->lang->line("generate_proforma_invoice"),
                        "pagetype" => "generate_invoice",
                        "csrfhash" => $this->security->get_csrf_hash(),
                        "originid" => $originId,
                        "exportid" => $exportOrderId,
                        "sanumber" => $saNumber,
                        "export_details" => $exportOrderDetail,
                        "product_type_id" => $exportOrderDetail[0]->product_type_id,
                        "buyers" => $this->Master_model->fetch_buyers_list($originId),
                        "sellers" => $this->Master_model->fetch_sellers_list(),
                        "banks" => $this->Master_model->fetch_banks_invoice_list($originId),
                        "export_volume" => $this->Export_model->get_export_volume_by_id($exportOrderId, $saNumber, $originId),
                        "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($originId, $exportOrderDetail[0]->product_type_id),
                        "company_setting" => $this->Settings_model->read_company_setting($originId),
                        "containernumbers" => $this->Export_model->fetch_distinct_container_number($exportOrderId),
                        "claimtrackers" => $this->Claimtracker_model->fetch_unclaim_list($originId),
                        "salesBuyerData" => $this->Export_model->get_ledger_transaction_details_by_export($exportOrderId),

                    );

                    $this->load->view("exportsales/dialog_view_export_invoice", $data);
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("common_error");
                    $Return["messagetype"] = "info";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return["pagemessage"] = "";
            $Return["pageheading"] = "";
            $Return["pages"] = "";
            $Return["messagetype"] = "redirect";
            $Return["pagemessage"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function fetch_buyer_details()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "", "isnetweight" => false);
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            $originId = $this->input->post("originId");
            $buyerId = $this->input->post("buyerId");

            $getBuyerDetails = $this->Master_model->get_buyers_details_by_id($buyerId);

            if (count($getBuyerDetails) > 0) {

                $address = $getBuyerDetails[0]->address;
                $contact = "Contact : " . $getBuyerDetails[0]->contact_name;
                $phone = "Phone : " . $getBuyerDetails[0]->contact_no;
                $email = "Email : " . $getBuyerDetails[0]->email;

                $buyerText = "$address\n$contact\n$phone\n$email";

                $Return["pages"] = "";
                $Return["result"] = html_entity_decode($buyerText, ENT_QUOTES, 'UTF-8');
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $Return["redirect"] = false;
                $this->output($Return);
                exit;
            } else {
                $Return["pages"] = "";
                $Return["error"] = $this->lang->line("common_error");
                $Return["result"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            }
        } else {
            $Return["pages"] = "";
            $Return["result"] = "";
            $Return["error"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function fetch_bank_details()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "", "isnetweight" => false);
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            $originId = $this->input->post("originId");
            $bankId = $this->input->post("bankId");

            $getBankDetails = $this->Master_model->get_bank_details_by_id($bankId);

            if (count($getBankDetails) > 0) {

                $payTo = "Pay To : " . $getBankDetails[0]->pay_to;
                $address = "Bank Address : " . $getBankDetails[0]->bank_address;
                $name = "Beneficiary Bank : "  . $getBankDetails[0]->bank_name;
                $swiftCode = "Swift Code : " . $getBankDetails[0]->swift_code;
                $accountNumber = "Account Number : " . $getBankDetails[0]->account_number;

                $buyerText = "$payTo\n$name\n$address\n$swiftCode\n$accountNumber";

                $Return["pages"] = "";
                $Return["result"] = html_entity_decode($buyerText, ENT_QUOTES, 'UTF-8');
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $Return["redirect"] = false;
                $this->output($Return);
                exit;
            } else {
                $Return["pages"] = "";
                $Return["error"] = $this->lang->line("common_error");
                $Return["result"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            }
        } else {
            $Return["pages"] = "";
            $Return["result"] = "";
            $Return["error"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function fetch_seller_details()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "", "isnetweight" => false);
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            $sellerId = $this->input->post("sellerId");

            $getBuyerDetails = $this->Master_model->get_seller_details_by_id($sellerId);

            if (count($getBuyerDetails) > 0) {

                $address = $getBuyerDetails[0]->address;
                $contact = "Contact : " . $getBuyerDetails[0]->contact_name;
                $phone = "Phone : " . $getBuyerDetails[0]->contact_no;
                $email = "Email : " . $getBuyerDetails[0]->email;

                $buyerText = "$address\n$contact\n$phone\n$email";

                $Return["pages"] = "";
                $Return["result"] = html_entity_decode($buyerText, ENT_QUOTES, 'UTF-8');
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $Return["redirect"] = false;
                $this->output($Return);
                exit;
            } else {
                $Return["pages"] = "";
                $Return["error"] = $this->lang->line("common_error");
                $Return["result"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            }
        } else {
            $Return["pages"] = "";
            $Return["result"] = "";
            $Return["error"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }
    
    public function dialog_invoice_history_option()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->post("type") == "invoicehistory") {

                $originId = $this->input->post("origin_id");
                $saNumber = $this->input->post("sa_number");
                $exportOrderId = $this->input->post("export_order_id");

                //CHECK EXPORT DETAILS
                $invoiceHistoryData = $this->Export_model->fetch_invoice_history($exportOrderId);
                $exportOrderDetail = $this->Export_model->get_export_order_details_by_id($exportOrderId, $saNumber, $originId);
                if (count($exportOrderDetail) == 1) {

                    $data = array(
                        "pageheading" => $this->lang->line("invoice_history"),
                        "pagetype" => "invoice_history",
                        "csrfhash" => $this->security->get_csrf_hash(),
                        "originid" => $originId,
                        "exportid" => $exportOrderId,
                        "sanumber" => $saNumber,
                        "invoiceHistory" => $invoiceHistoryData,
                        "export_details" => $exportOrderDetail,
                        "invoiceContainers" => $this->Export_model->fetch_container_invoice_price_by_export($exportOrderId),
                    );

                    $this->load->view("exportsales/dialog_view_invoice_history", $data);
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("common_error");
                    $Return["messagetype"] = "info";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return["pagemessage"] = "";
            $Return["pageheading"] = "";
            $Return["pages"] = "";
            $Return["messagetype"] = "redirect";
            $Return["pagemessage"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function generate_invoice_from_history()
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "",
                "error" => "",
                "redirect" => false,
                "csrf_hash" => "",
                "successmessage" => ""
            );

            if (!empty($session)) {
                
                $this->deletefilesfromfolder();

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originId = $this->input->post("originid");
                $saNumber = $this->input->post("sanumber");
                $originalSANumber = $this->input->post("sanumber");
                $exportOrderId = $this->input->post("exportid");
                $invoiceId = $this->input->post("invoiceId");

                $invoiceData = $this->Export_model->fetch_invoice_history_invoice($exportOrderId, $invoiceId);

                $getBuyerDetails = $this->Master_model->get_buyers_details_by_id($invoiceData[0]->buyer_id);
                $getBankDetails = $this->Master_model->get_bank_details_by_id($invoiceData[0]->bank_id);
                $getSellerDetails = $this->Master_model->get_seller_details_by_id($invoiceData[0]->seller_id);

                if ($originId == 3) {
                    $getExportDetails = $this->Export_model->get_export_details_invoice_id($exportOrderId);
                } else {
                    $getExportDetails = $this->Export_model->get_export_details_invoice_export_origin($exportOrderId);
                }

                $arrBuyerAddress = explode("\n", $getBuyerDetails[0]->address);

                if ($originId == 3) {
                    $saNumber = $getExportDetails[0]->booking_no . "-" . str_replace("/", "-", $saNumber);
                } else {
                    $saNumber = str_replace("/", "-", $saNumber);
                }

                $objExcel = PHPExcel_IOFactory::createReader('Excel2007');
                // if ($originId == 3) {
                //     $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_USA.xlsx');
                // } else {
                //     if($invoiceData[0]->bank_id == 5) {
                //         $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Singapore.xlsx');
                //     } else {
                //         $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Colombia.xlsx');
                //     }
                // }
                
                $accountingInvoice = $this->input->post("accountingInvoice");
                
                if ($originId == 1 && $accountingInvoice == 1) {
                    $objExcel = $objExcel->load('./assets/templates/ProForma_Invoice_Template_Colombia_AcccoutingInvoice.xlsx');
                } else {
                    $objExcel = $objExcel->load('./assets/templates/' . $getSellerDetails[0]->template_file);
                }

                
                $objExcel->setActiveSheetIndex(0);
                $objSheet = $objExcel->getActiveSheet();

                //BUYER DETAILS
                $buyerRowCount = 11;
                $objSheet->setCellValue("B$buyerRowCount", $getBuyerDetails[0]->buyer_name);

                foreach ($arrBuyerAddress as $buyeraddress) {
                    $buyerRowCount = $buyerRowCount + 1;
                    $objSheet->setCellValue("B$buyerRowCount", $buyeraddress);
                }

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Contact : " . $getBuyerDetails[0]->contact_name);

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Phone : " . $getBuyerDetails[0]->contact_no);

                $buyerRowCount = $buyerRowCount + 1;
                $objSheet->setCellValue("B$buyerRowCount", "Email : " . $getBuyerDetails[0]->email);

                //EXPORT ITEMS
                if ($originId == 3) {

                    $servicesalesPercentage = $invoiceData[0]->service_sales_percentage;
                    $salesAdvanceCost = $invoiceData[0]->advance_cost;
                    
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $invoiceData[0]->claim_id;

                    $objSheet->setCellValue('C8', "INVOICE - CGUS-SYP-$saNumber");

                    $excelVolumeValue = 0;
                    $excelInvoicePrice = 0;

                    //EXPORT DETAILS

                    $todayDate = date($invoiceData[0]->invoice_date);
                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", $getExportDetails[0]->origin_name);
                    $objSheet->setCellValue("G13", $getExportDetails[0]->pol);
                    $objSheet->setCellValue("G14", $getExportDetails[0]->pod);

                    $getExportItemDetails = $this->Export_model->get_export_items_by_exportid($exportOrderId);
                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;
                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;
                        foreach ($getExportItemDetails as $exportitem) {
                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $objSheet->setCellValue("B$exportItemsRowCount", $exportitem->total_containers);
                            $objSheet->setCellValue("C$exportItemsRowCount", $exportitem->description);
                            $objSheet->setCellValue("D$exportItemsRowCount", $exportitem->metric_ton);
                            $objSheet->setCellValue("E$exportItemsRowCount", "Metric Tonne");

                            $objSheet->setCellValue("F$exportItemsRowCount", $exportitem->unit_price);
                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + $exportitem->metric_ton * $exportitem->unit_price;
                            $totalInvoiceVolume = $totalInvoiceVolume + $exportitem->metric_ton;
                        }
                    }

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("G40", "=SUM(G20:G39)");
                    $objSheet->setCellValue("F40", "=G40/D40");

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($invoiceData[0]->advance_enabled == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("G$lastRowDetails", "=COUNT(G20:G39)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($invoiceData[0]->service_enabled == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("G$lastRowDetails", "=SUM(G20:G39)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_history_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("G$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if ($accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        $exportItemsRowCount = 19;
                        foreach ($getExportItemDetails as $exportitem) {
                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;
                            
                            $objSheet->setCellValue("B$exportItemsRowCount", $exportitem->total_containers);
                            $objSheet->setCellValue("C$exportItemsRowCount", $exportitem->description);
                            $objSheet->setCellValue("D$exportItemsRowCount", $exportitem->metric_ton);
                            $objSheet->setCellValue("E$exportItemsRowCount", "Metric Tonne");

                            $containerUPrice = ($exportitem->unit_price - $accountingInvoiceUnitPrice) + 0;
                            $objSheet->setCellValue("F$exportItemsRowCount", $containerUPrice);

                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + $exportitem->metric_ton * $containerUPrice;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    //$num = 15786.05;
                    //$numberToWords = $this->convertNumber(round($totalValue, 2));
                    //$objSheet->setCellValue("C37", "US Dollars " . str_replace("  ", " ", $numberToWords) . " Cents Only");

                    //$excelInvoiceValue = round($objSheet->getCell('G40')->getCalculatedValue(), 2);
                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('G40')->getCalculatedValue());

                    $tempNum = explode('.', $excelInvoiceValue);
                    
                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                    
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' With ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' Cents' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", ucwords($convertedNumber)) . " Only");

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                } else if($originId == 1 && $accountingInvoice == 1) {
                    
                    $servicesalesPercentage = $invoiceData[0]->service_sales_percentage;
                    $salesAdvanceCost = $invoiceData[0]->advance_cost;
                    $accountingInvoice = $this->input->post("accountingInvoice");
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $invoiceData[0]->claim_id;

                    date_default_timezone_set($getExportDetails[0]->timezone_abbreviation);
                    $invoiceBuyerName = $getBuyerDetails[0]->invoice_name;
                    $objSheet->setCellValue('C8', "INVOICE -$invoiceBuyerName-$saNumber");

                    //EXPORT DETAILS

                    $todayDate = date($invoiceData[0]->invoice_date);

                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", strtoupper($getExportDetails[0]->origin_name));
                    $objSheet->setCellValue("G13", strtoupper($getExportDetails[0]->pol));
                    $objSheet->setCellValue("G14", strtoupper($getExportDetails[0]->pod));

                    $enableJumpShorts = $invoiceData[0]->enabled_jump_shorts;
                    $enableJumpSemi = $invoiceData[0]->enabled_jump_semi;
                    $enableJumpLongs = $invoiceData[0]->enabled_jump_long;
                    $priceShortsBase = $invoiceData[0]->shorts_base_price;
                    $priceSemiBase = $invoiceData[0]->semi_base_price;
                    $priceLongsBase = $invoiceData[0]->long_base_price;

                    //SHORTS
                    $shortsArray = array();
                    $priceShorts = $priceShortsBase;
                    if ($priceShorts > 0) {
                        for ($i = 1.75; $i >= 1; $i -= 0.25) {
                            $shortsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 0.24,
                                'rangePrice' => $priceShorts - 15,
                            );

                            $priceShorts = $priceShorts - 15;
                        }

                        $priceShorts = $priceShortsBase;
                        for ($i = 2; $i <= 20; $i += 0.25) {

                            if ($i == 5) {

                                if ($enableJumpShorts == 1) {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts + 20,
                                    );

                                    $priceShorts = $priceShorts + 15 + 20;
                                } else {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts,
                                    );

                                    $priceShorts = $priceShorts + 15;
                                }
                            } else {
                                $shortsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 0.24,
                                    'rangePrice' => $priceShorts,
                                );

                                $priceShorts = $priceShorts + 15;
                            }
                        }
                    }

                    //SEMI
                    $semiArray = array();
                    $priceSemi = $priceSemiBase;
                    if ($priceSemi > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {

                            $semiArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceSemi - 50,
                            );

                            $priceSemi = $priceSemi - 50;
                        }

                        $priceSemi = $priceSemiBase;
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {

                                if ($enableJumpSemi == 1) {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi + 20,
                                    );

                                    $priceSemi = $priceSemi + 30 + 20;
                                } else {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi,
                                    );

                                    $priceSemi = $priceSemi + 30;
                                }
                            } else {
                                $semiArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceSemi,
                                );

                                $priceSemi = $priceSemi + 30;
                            }
                        }
                    }

                    //LONGS
                    $longsArray = array();
                    $priceLongs = $priceLongsBase;
                    if ($priceLongs > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {
                            $longsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceLongs - 50,
                            );

                            $priceLongs = $priceLongs - 50;
                        }

                        $priceLongs = $priceLongsBase;
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {
                                if ($enableJumpLongs == 1) {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs + 20,
                                    );

                                    $priceLongs = $priceLongs + 30 + 20;
                                } else {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs,
                                    );

                                    $priceLongs = $priceLongs + 30;
                                }
                            } else {
                                $longsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceLongs,
                                );

                                $priceLongs = $priceLongs + 30;
                            }
                        }
                    }

                    $circAllowance = $invoiceData[0]->circ_allowance;
                    $lengthAllowance = $invoiceData[0]->length_allowance;
                    $circAdjustment = $invoiceData[0]->circ_adjustment;
                    $measurementsystem = $invoiceData[0]->measurement_system;

                    $getExportItemDetails = $this->Export_model->get_export_data_by_export_id($exportOrderId, $originalSANumber, $originId, $circAllowance, $lengthAllowance, $circAdjustment, $measurementsystem);

                    $totalValue = 0;
                    $containerItemArray = array();
                    $containerNumberArray = array();

                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($getExportItemDetails as $exportitem) {

                            array_push($containerNumberArray, $exportitem->container_number);

                            if ($exportitem->product_type == 1) {
                                if ($priceShorts > 0) {
                                    $containerUnitPrice = 0;
                                    if (count($shortsArray) > 0) {
                                        foreach ($shortsArray as $shortprice) {

                                            if (($shortprice["minRange"] <= $exportitem->gross_cft) && ($shortprice["maxRange"] >= $exportitem->gross_cft)) {
                                                $containerUnitPrice = $shortprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $shortprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $shortprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 2) {
                                if ($priceSemi > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($semiArray) > 0) {
                                        foreach ($semiArray as $semiprice) {

                                            if (($semiprice["minRange"] <= $exportitem->avg_circumference) && ($semiprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $semiprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $semiprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $semiprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 3) {
                                if ($priceLongs > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($longsArray) > 0) {
                                        foreach ($longsArray as $longprice) {

                                            if (($longprice["minRange"] <= $exportitem->avg_circumference) && ($longprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $longprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $longprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                    "pieces" => $exportitem->total_pieces + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $longprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                        "pieces" => $exportitem->total_pieces + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else {
                                //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                            }

                            //$objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            //$objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                        }
                    }

                    $duplicateContainers = array_diff_assoc($containerNumberArray, array_unique($containerNumberArray));
                    $containerFinalArray = array();

                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;
                    if (count($containerItemArray) > 0) {
                        foreach ($containerItemArray as $containerdata) {
                            if (count($duplicateContainers) > 0) {
                                foreach ($duplicateContainers as $duplicatecontainer) {
                                    if ($containerdata["containerNumber"] == $duplicatecontainer) {
                                        //DO NOTHING
                                    } else {
                                        $containerFinalArray[] = array(
                                            "containerNumber" => $containerdata["containerNumber"],
                                            "unitPrice" => $containerdata["unitPrice"] + 0,
                                            "volume" => $containerdata["volume"] + 0,
                                            "pieces" => $containerdata["pieces"] + 0,
                                        );

                                        break;
                                    }
                                }
                            } else {
                                $containerFinalArray[] = array(
                                    "containerNumber" => $containerdata["containerNumber"],
                                    "unitPrice" => $containerdata["unitPrice"] + 0,
                                    "volume" => $containerdata["volume"] + 0,
                                    "pieces" => $containerdata["pieces"] + 0,
                                );
                            }
                        }
                    }
                    
                    $containerDataArray = array();
                    if (count($duplicateContainers) > 0) {
                        $containerDuplicateDataArray = array();
                        foreach ($duplicateContainers as $containernum) {
                            $totalVolume = 0;
                            $totalValue = 0;
                            $unitPrice = 0;
                            $totalPieces = 0;
                            foreach ($containerItemArray as $containeritem) {
                                if ($containeritem["containerNumber"] == $containernum) {
                                    $totalVolume = $totalVolume + $containeritem["volume"];
                                    $totalValue = $totalValue + ($containeritem["volume"] * $containeritem["unitPrice"]);
                                    $totalPieces = $totalPieces + ($containeritem["pieces"]);
                                }
                            }

                            if ($totalVolume > 0 && $totalValue > 0) {
                                $unitPrice = $totalValue / $totalVolume;
                            }

                            $containerDuplicateDataArray[] = array(
                                "containerNumber" => $containernum,
                                "unitPrice" => $unitPrice + 0,
                                "volume" => $totalVolume + 0,
                                "pieces" => $totalPieces,
                            );
                        }

                        if (count($containerFinalArray) > 0) {
                            foreach ($containerFinalArray as $containerfinal) {
                                if (array_search($containerfinal["containerNumber"], $duplicateContainers) !== false) {
                                    //DO NOTHING
                                } else {
                                    $containerDataArray[] = array(
                                        "containerNumber" => $containerfinal["containerNumber"],
                                        "unitPrice" => $containerfinal["unitPrice"] + 0,
                                        "volume" => $containerfinal["volume"] + 0,
                                        "pieces" => $containerfinal["pieces"] + 0,
                                    );
                                }
                            }
                        }

                        $containerDataArray = array_merge($containerDataArray, $containerDuplicateDataArray);
                    } else {
                        $containerDataArray = array_merge($containerDataArray, $containerFinalArray);
                    }

                    if (count($containerDataArray) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            if ($accountingInvoice == 0) {
                                
                                $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                                $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                                $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                                $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                                $objSheet->setCellValue("E$exportItemsRowCount", "CBM");

                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $containerno["containerPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);

                                            break;
                                        } else {
                                            $containerUPrice = $container["unitPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $containerUPrice = $container["unitPrice"] + 0;
                                    $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                    //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                    //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }

                                $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                            }

                            $totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;

                            if ($accountingInvoice == 1) {
                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);
                                            break;
                                        } else {
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }
                            } else {
                                $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                            }
                        }
                    }

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($invoiceData[0]->advance_enabled == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("H$lastRowDetails", "=COUNT(H20:H$exportItemsRowCount)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($invoiceData[0]->service_enabled == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("H$lastRowDetails", "=SUM(H20:H$exportItemsRowCount)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_history_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("H$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if (count($containerDataArray) > 0 && $accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                            $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                            $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                            $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                            $objSheet->setCellValue("E$exportItemsRowCount", $container["pieces"] + 0);
                            $objSheet->getStyle("E$exportItemsRowCount")->getNumberFormat()->setFormatCode('0');
                            $objSheet->setCellValue("F$exportItemsRowCount", "CBM");


                            if (count($updateContainerNumberDataJson) > 0) {
                                foreach ($updateContainerNumberDataJson as $containerno) {
                                    if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                        $containerUPrice = ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("G$exportItemsRowCount", ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0);
                                        break;
                                    } else {
                                        $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("G$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                                    }
                                }
                            } else {
                                $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                $objSheet->setCellValue("G$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                            }

                            $objSheet->getStyle("G$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            $objSheet->setCellValue("H$exportItemsRowCount", "=D$exportItemsRowCount*G$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerUPrice) + 0;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("E40", "=SUM(E20:E39)");
                    $objSheet->setCellValue("H40", "=SUM(H20:H39)");
                    $objSheet->setCellValue("G40", "=H40/D40");

                    //if ($totalInvoiceValue > 0) {

                    //$excelInvoiceValue = round($objSheet->getCell('G40')->getCalculatedValue(), 2);
                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('H40')->getCalculatedValue());
                    $excelVolumeValue = round($objSheet->getCell('D40')->getCalculatedValue(), 3);
                    $excelInvoicePrice = $objSheet->getCell('G40')->getCalculatedValue();

                    $tempNum = explode('.', $excelInvoiceValue);

                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                    
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' WITH ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' CENTS' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", strtoupper($convertedNumber)) . " ONLY");
                    //}

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                    
                } else {

                    $servicesalesPercentage = $invoiceData[0]->service_sales_percentage;
                    $salesAdvanceCost = $invoiceData[0]->advance_cost;
                    $accountingInvoice = $this->input->post("accountingInvoice");
                    $updateContainerNumberDataJson = json_decode($this->input->post("updatecontainerprice"), true);
                    $creditnotes = $invoiceData[0]->claim_id;

                    date_default_timezone_set($getExportDetails[0]->timezone_abbreviation);
                    $invoiceBuyerName = $getBuyerDetails[0]->invoice_name;
                    $objSheet->setCellValue('C8', "INVOICE -$invoiceBuyerName-$saNumber");

                    //EXPORT DETAILS

                    $todayDate = date($invoiceData[0]->invoice_date);

                    $shippedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $todayDate));
                    $objSheet->setCellValue("G11", $shippedDate);
                    $objSheet->getStyle("G11")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                    $objSheet->setCellValue("G12", strtoupper($getExportDetails[0]->origin_name));
                    $objSheet->setCellValue("G13", strtoupper($getExportDetails[0]->pol));
                    $objSheet->setCellValue("G14", strtoupper($getExportDetails[0]->pod));

                    $enableJumpShorts = $invoiceData[0]->enabled_jump_shorts;
                    $enableJumpSemi = $invoiceData[0]->enabled_jump_semi;
                    $enableJumpLongs = $invoiceData[0]->enabled_jump_long;
                    $priceShortsBase = $invoiceData[0]->shorts_base_price;
                    $priceSemiBase = $invoiceData[0]->semi_base_price;
                    $priceLongsBase = $invoiceData[0]->long_base_price;

                    //SHORTS
                    $shortsArray = array();
                    $priceShorts = $priceShortsBase;
                    if ($priceShorts > 0) {
                        for ($i = 1.75; $i >= 1; $i -= 0.25) {
                            $shortsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 0.24,
                                'rangePrice' => $priceShorts - 15,
                            );

                            $priceShorts = $priceShorts - 15;
                        }

                        $priceShorts = $priceShortsBase;
                        for ($i = 2; $i <= 20; $i += 0.25) {

                            if ($i == 5) {

                                if ($enableJumpShorts == 1) {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts + 20,
                                    );

                                    $priceShorts = $priceShorts + 15 + 20;
                                } else {
                                    $shortsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 0.24,
                                        'rangePrice' => $priceShorts,
                                    );

                                    $priceShorts = $priceShorts + 15;
                                }
                            } else {
                                $shortsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 0.24,
                                    'rangePrice' => $priceShorts,
                                );

                                $priceShorts = $priceShorts + 15;
                            }
                        }
                    }

                    //SEMI
                    $semiArray = array();
                    $priceSemi = $priceSemiBase;
                    if ($priceSemi > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {

                            $semiArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceSemi - 50,
                            );

                            $priceSemi = $priceSemi - 50;
                        }

                        $priceSemi = $priceSemiBase;
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {

                                if ($enableJumpSemi == 1) {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi + 20,
                                    );

                                    $priceSemi = $priceSemi + 30 + 20;
                                } else {
                                    $semiArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceSemi,
                                    );

                                    $priceSemi = $priceSemi + 30;
                                }
                            } else {
                                $semiArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceSemi,
                                );

                                $priceSemi = $priceSemi + 30;
                            }
                        }
                    }

                    //LONGS
                    $longsArray = array();
                    $priceLongs = $priceLongsBase;
                    if ($priceLongs > 0) {
                        for ($i = 55; $i >= 30; $i -= 5) {
                            $longsArray[] = array(
                                'minRange' => $i + 0,
                                'maxRange' => $i + 4.99,
                                'rangePrice' => $priceLongs - 50,
                            );

                            $priceLongs = $priceLongs - 50;
                        }

                        $priceLongs = $priceLongsBase;
                        for ($i = 60; $i <= 199; $i += 5) {

                            if ($i == 100) {
                                if ($enableJumpLongs == 1) {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs + 20,
                                    );

                                    $priceLongs = $priceLongs + 30 + 20;
                                } else {
                                    $longsArray[] = array(
                                        'minRange' => $i + 0,
                                        'maxRange' => $i + 4.99,
                                        'rangePrice' => $priceLongs,
                                    );

                                    $priceLongs = $priceLongs + 30;
                                }
                            } else {
                                $longsArray[] = array(
                                    'minRange' => $i + 0,
                                    'maxRange' => $i + 4.99,
                                    'rangePrice' => $priceLongs,
                                );

                                $priceLongs = $priceLongs + 30;
                            }
                        }
                    }

                    $circAllowance = $invoiceData[0]->circ_allowance;
                    $lengthAllowance = $invoiceData[0]->length_allowance;
                    $circAdjustment = $invoiceData[0]->circ_adjustment;
                    $measurementsystem = $invoiceData[0]->measurement_system;

                    $getExportItemDetails = $this->Export_model->get_export_data_by_export_id($exportOrderId, $originalSANumber, $originId, $circAllowance, $lengthAllowance, $circAdjustment, $measurementsystem);

                    $totalValue = 0;
                    $containerItemArray = array();
                    $containerNumberArray = array();

                    if (count($getExportItemDetails) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($getExportItemDetails as $exportitem) {

                            array_push($containerNumberArray, $exportitem->container_number);

                            if ($exportitem->product_type == 1) {
                                if ($priceShorts > 0) {
                                    $containerUnitPrice = 0;
                                    if (count($shortsArray) > 0) {
                                        foreach ($shortsArray as $shortprice) {

                                            if (($shortprice["minRange"] <= $exportitem->gross_cft) && ($shortprice["maxRange"] >= $exportitem->gross_cft)) {
                                                $containerUnitPrice = $shortprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $shortprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $shortprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 2) {
                                if ($priceSemi > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($semiArray) > 0) {
                                        foreach ($semiArray as $semiprice) {

                                            if (($semiprice["minRange"] <= $exportitem->avg_circumference) && ($semiprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $semiprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $semiprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $semiprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else if ($exportitem->product_type == 3) {
                                if ($priceLongs > 0) {
                                    $containerUnitPrice = 0;

                                    if (count($longsArray) > 0) {
                                        foreach ($longsArray as $longprice) {

                                            if (($longprice["minRange"] <= $exportitem->avg_circumference) && ($longprice["maxRange"] >= $exportitem->avg_circumference)) {
                                                $containerUnitPrice = $longprice["rangePrice"] + 0;

                                                $containerItemArray[] = array(
                                                    "containerNumber" => $exportitem->container_number,
                                                    "unitPrice" => $longprice["rangePrice"] + 0,
                                                    "volume" => $exportitem->net_volume + 0,
                                                );

                                                //$totalValue = $totalValue + ($exportitem->net_volume * $longprice["rangePrice"]);
                                            }
                                        }
                                    }

                                    //$objSheet->setCellValue("F$exportItemsRowCount", $containerUnitPrice + 0);
                                } else {

                                    $containerItemArray[] = array(
                                        "containerNumber" => $exportitem->container_number,
                                        "unitPrice" => 0,
                                        "volume" => $exportitem->net_volume + 0,
                                    );

                                    //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                                }
                            } else {
                                //$objSheet->setCellValue("F$exportItemsRowCount",  0);
                            }

                            //$objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            //$objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                        }
                    }

                    $duplicateContainers = array_diff_assoc($containerNumberArray, array_unique($containerNumberArray));
                    $containerFinalArray = array();

                    $totalInvoiceValue = 0;
                    $totalInvoiceVolume = 0;
                    if (count($containerItemArray) > 0) {
                        foreach ($containerItemArray as $containerdata) {
                            if (count($duplicateContainers) > 0) {
                                foreach ($duplicateContainers as $duplicatecontainer) {
                                    if ($containerdata["containerNumber"] == $duplicatecontainer) {
                                        //DO NOTHING
                                    } else {
                                        $containerFinalArray[] = array(
                                            "containerNumber" => $containerdata["containerNumber"],
                                            "unitPrice" => $containerdata["unitPrice"] + 0,
                                            "volume" => $containerdata["volume"] + 0,
                                        );

                                        break;
                                    }
                                }
                            } else {
                                $containerFinalArray[] = array(
                                    "containerNumber" => $containerdata["containerNumber"],
                                    "unitPrice" => $containerdata["unitPrice"] + 0,
                                    "volume" => $containerdata["volume"] + 0,
                                );
                            }
                        }
                    }
                    
                    $containerDataArray = array();
                    if (count($duplicateContainers) > 0) {
                        $containerDuplicateDataArray = array();
                        foreach ($duplicateContainers as $containernum) {
                            $totalVolume = 0;
                            $totalValue = 0;
                            $unitPrice = 0;
                            foreach ($containerItemArray as $containeritem) {
                                if ($containeritem["containerNumber"] == $containernum) {
                                    $totalVolume = $totalVolume + $containeritem["volume"];
                                    $totalValue = $totalValue + ($containeritem["volume"] * $containeritem["unitPrice"]);
                                }
                            }

                            if ($totalVolume > 0 && $totalValue > 0) {
                                $unitPrice = $totalValue / $totalVolume;
                            }

                            $containerDuplicateDataArray[] = array(
                                "containerNumber" => $containernum,
                                "unitPrice" => $unitPrice + 0,
                                "volume" => $totalVolume + 0,
                            );
                        }

                        if (count($containerFinalArray) > 0) {
                            foreach ($containerFinalArray as $containerfinal) {
                                if (array_search($containerfinal["containerNumber"], $duplicateContainers) !== false) {
                                    //DO NOTHING
                                } else {
                                    $containerDataArray[] = array(
                                        "containerNumber" => $containerfinal["containerNumber"],
                                        "unitPrice" => $containerfinal["unitPrice"] + 0,
                                        "volume" => $containerfinal["volume"] + 0,
                                    );
                                }
                            }
                        }

                        $containerDataArray = array_merge($containerDataArray, $containerDuplicateDataArray);
                    } else {
                        $containerDataArray = array_merge($containerDataArray, $containerFinalArray);
                    }

                    if (count($containerDataArray) > 0) {
                        $exportItemsRowCount = 19;

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            if ($accountingInvoice == 0) {
                                
                                $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                                $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                                $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                                $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                                $objSheet->setCellValue("E$exportItemsRowCount", "CBM");

                                if (is_array($updateContainerNumberDataJson) && count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $containerno["containerPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);

                                            break;
                                        } else {
                                            $containerUPrice = $container["unitPrice"] + 0;
                                            $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                            //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                            //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $containerUPrice = $container["unitPrice"] + 0;
                                    $objSheet->setCellValue("F$exportItemsRowCount", $container["unitPrice"] + 0);

                                    //$totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;
                                    //$totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }

                                $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");
                            }

                            $totalInvoiceVolume = $totalInvoiceVolume + $container["volume"] + 0;

                            if ($accountingInvoice == 1) {
                                if (count($updateContainerNumberDataJson) > 0) {
                                    foreach ($updateContainerNumberDataJson as $containerno) {
                                        if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                            $containerUPrice = $containerno["containerPrice"] + 0;
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerno["containerPrice"] + 0);
                                            break;
                                        } else {
                                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                        }
                                    }
                                } else {
                                    $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                                }
                            } else {
                                $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($container["unitPrice"] + 0);
                            }
                        }
                    }

                    $lastRowDetails = 40;
                    $advanceCost = 0;
                    if ($invoiceData[0]->advance_enabled == 1 && $salesAdvanceCost != 0) {
                        $lastRowDetails = $lastRowDetails - 1;
                        $objSheet->setCellValue("B$lastRowDetails", "(Less Advance)");
                        $objSheet->setCellValue("G$lastRowDetails", "=COUNT(G20:G$exportItemsRowCount)*-$salesAdvanceCost");

                        $advanceCost = $getExportDetails[0]->d_total_containers * ($salesAdvanceCost);
                    }

                    $salesServiceCost = 0;
                    if ($invoiceData[0]->service_enabled == 1 && $servicesalesPercentage != 0) {

                        if ($accountingInvoice == 0) {
                            $lastRowDetails = $lastRowDetails - 1;
                            $objSheet->setCellValue("B$lastRowDetails", "(Less Sales Serv Fees)");
                            $objSheet->setCellValue("G$lastRowDetails", "=SUM(G20:G$exportItemsRowCount)*-$servicesalesPercentage%");
                        }

                        $salesServiceCost = $totalInvoiceValue *  ($servicesalesPercentage / 100);
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $salesServiceCost;

                    $claimCost = 0;
                    if ($creditnotes != null && $creditnotes != "") {
                        $creditNotesArray = explode(",", $creditnotes);
                        if (count($creditNotesArray) > 0) {
                            $getCreditNoteDetails = $this->Claimtracker_model->get_claim_details_history_byid($creditnotes);
                            if (count($getCreditNoteDetails) == 1) {
                                $lastRowDetails = $lastRowDetails - 1;
                                $objSheet->setCellValue("B$lastRowDetails", "(Less Credit Note)");
                                $objSheet->setCellValue("C$lastRowDetails", $getCreditNoteDetails[0]->claim_reference);
                                $objSheet->getRowDimension("$lastRowDetails")->setRowHeight(-1);
                                $objSheet->getStyle("C$lastRowDetails")->getAlignment()->setWrapText(true);
                                $objSheet->setCellValue("G$lastRowDetails", (-1 * $getCreditNoteDetails[0]->claim_amount + 0));

                                $claimCost = (-1 * $getCreditNoteDetails[0]->claim_amount + 0);
                            }
                        }
                    }

                    if (count($containerDataArray) > 0 && $accountingInvoice == 1) {
                        $totalInvoiceValue = 0;
                        $exportItemsRowCount = 19;

                        $accountingInvoiceUnitPrice = 0;
                        if ($salesServiceCost > 0) {
                            $accountingInvoiceUnitPrice = $salesServiceCost / $totalInvoiceVolume;
                        } else {
                            $accountingInvoiceUnitPrice = 0;
                        }

                        foreach ($containerDataArray as $container) {

                            $exportItemsRowCount = $exportItemsRowCount + 1;
                            $containerUPrice = 0;

                            $objSheet->setCellValue("B$exportItemsRowCount", $container["containerNumber"]);
                            $objSheet->setCellValue("C$exportItemsRowCount", "TEAK WOOD ROUND LOGS");
                            $objSheet->setCellValue("D$exportItemsRowCount", $container["volume"] + 0);
                            $objSheet->getStyle("D$exportItemsRowCount")->getNumberFormat()->setFormatCode('0.000');
                            $objSheet->setCellValue("E$exportItemsRowCount", "CBM");


                            if (count($updateContainerNumberDataJson) > 0) {
                                foreach ($updateContainerNumberDataJson as $containerno) {
                                    if ($containerno["containerNumber"] == $container["containerNumber"]) {
                                        $containerUPrice = ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("F$exportItemsRowCount", ($containerno["containerPrice"] - $accountingInvoiceUnitPrice) + 0);
                                        break;
                                    } else {
                                        $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                        $objSheet->setCellValue("F$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                                    }
                                }
                            } else {
                                $containerUPrice = ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0;
                                $objSheet->setCellValue("F$exportItemsRowCount", ($container["unitPrice"] - $accountingInvoiceUnitPrice) + 0);
                            }

                            $objSheet->getStyle("F$exportItemsRowCount")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                            $objSheet->setCellValue("G$exportItemsRowCount", "=D$exportItemsRowCount*F$exportItemsRowCount");

                            $totalInvoiceValue = $totalInvoiceValue + ($container["volume"] + 0) * ($containerUPrice) + 0;
                        }
                    }

                    $totalInvoiceValue = $totalInvoiceValue - $advanceCost + $claimCost;

                    $objSheet->setCellValue("D40", "=SUM(D20:D39)");
                    $objSheet->setCellValue("G40", "=SUM(G20:G39)");
                    $objSheet->setCellValue("F40", "=G40/D40");

                    //if ($totalInvoiceValue > 0) {

                    //$excelInvoiceValue = round($objSheet->getCell('G40')->getCalculatedValue(), 2);
                    $excelInvoiceValue = sprintf("%.2f", $objSheet->getCell('G40')->getCalculatedValue());
                    $excelVolumeValue = round($objSheet->getCell('D40')->getCalculatedValue(), 3);
                    $excelInvoicePrice = $objSheet->getCell('F40')->getCalculatedValue();

                    $tempNum = explode('.', $excelInvoiceValue);

                    $convertedNumber = (isset($tempNum[0]) ? $this->convertNumber($tempNum[0]) : '');
                    
                    if(isset($tempNum[1]) && $tempNum[1] > 0) {
                    
                        $convertedNumber .= ((isset($tempNum[0]) and isset($tempNum[1]))  ? ' WITH ' : '');
    
                        $tempNum[1] = ltrim($tempNum[1], '0');
    
                        $convertedNumber .= (isset($tempNum[1]) ? $this->convertNumber($tempNum[1]) . ' CENTS' : '');
                    }

                    //$numberToWords = $this->getIndianCurrency(round(60453.31, 2));
                    $objSheet->setCellValue("C42", "US Dollars " . str_replace("  ", " ", strtoupper($convertedNumber)) . " ONLY");
                    //}

                    //BANK NAME
                    $objSheet->setCellValue("C45", $getBankDetails[0]->pay_to);
                    $objSheet->setCellValue("C47", $getBankDetails[0]->bank_name);
                    $objSheet->setCellValue("C48", $getBankDetails[0]->bank_address);
                    $objSheet->setCellValue("C49", $getBankDetails[0]->swift_code);
                    $objSheet->setCellValue("C50", $getBankDetails[0]->account_number);
                    $objSheet->getStyle("C50")->getNumberFormat()->setFormatCode('0');
                }

                $saNumber = str_replace("/", "_", $saNumber);
                $month_name = ucfirst(date("dmY"));


                $filename =  "Proforma_Invoice_" . $saNumber . "_" . $month_name . ".xlsx";

                $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
                $objWriter->save("./reports/ExportReports/" . $filename);
                $objWriter->setPreCalculateFormulas(true);
                $Return["error"] = "";
                $Return["result"] = site_url() . "reports/ExportReports/" . $filename;
                $Return["successmessage"] = $this->lang->line("report_downloaded");
                if ($Return["result"] != "") {
                    $this->output($Return);
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

        $files = glob(FCPATH . "reports/ExportReports/*.xlsx");
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

    public function decrementLetter($Alphabet)
    {
        return chr(ord($Alphabet) - 1);
    }

    function convertNumber($number)
    {
        list($integer, $fraction) = explode(".", (string) $number);

        $output = "";

        if ($integer[0] == "-") {
            $output = "negative ";
            $integer    = ltrim($integer, "-");
        } else if ($integer[0] == "+") {
            $output = "positive ";
            $integer    = ltrim($integer, "+");
        }

        if ($integer[0] == "0") {
            $output .= "zero";
        } else {
            $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
            $group   = rtrim(chunk_split($integer, 3, " "), " ");
            $groups  = explode(" ", $group);

            $groups2 = array();
            foreach ($groups as $g) {
                $groups2[] = $this->convertThreeDigit($g[0], $g[1], $g[2]);
            }

            for ($z = 0; $z < count($groups2); $z++) {
                if ($groups2[$z] != "") {
                    $output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11][0] == '0'
                        ? " and "
                        : " "
                    );
                }
            }

            $output = rtrim($output, ", ");
        }

        if ($fraction > 0) {
            $output .= " point";
            for ($i = 0; $i < strlen($fraction); $i++) {
                $output .= " " . $this->convertDigit($fraction[$i]);
            }
        }

        return $output;
    }

    function convertGroup($index)
    {
        switch ($index) {
            case 11:
                return " decillion";
            case 10:
                return " nonillion";
            case 9:
                return " octillion";
            case 8:
                return " septillion";
            case 7:
                return " sextillion";
            case 6:
                return " quintrillion";
            case 5:
                return " quadrillion";
            case 4:
                return " trillion";
            case 3:
                return " billion";
            case 2:
                return " million";
            case 1:
                return " thousand";
            case 0:
                return "";
        }
    }

    function convertThreeDigit($digit1, $digit2, $digit3)
    {
        $buffer = "";

        if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0") {
            return "";
        }

        if ($digit1 != "0") {
            $buffer .= $this->convertDigit($digit1) . " hundred";
            if ($digit2 != "0" || $digit3 != "0") {
                $buffer .= " and ";
            }
        }

        if ($digit2 != "0") {
            $buffer .= $this->convertTwoDigit($digit2, $digit3);
        } else if ($digit3 != "0") {
            $buffer .= $this->convertDigit($digit3);
        }

        return $buffer;
    }

    function convertTwoDigit($digit1, $digit2)
    {
        if ($digit2 == "0") {
            switch ($digit1) {
                case "1":
                    return "ten";
                case "2":
                    return "twenty";
                case "3":
                    return "thirty";
                case "4":
                    return "forty";
                case "5":
                    return "fifty";
                case "6":
                    return "sixty";
                case "7":
                    return "seventy";
                case "8":
                    return "eighty";
                case "9":
                    return "ninety";
            }
        } else if ($digit1 == "1") {
            switch ($digit2) {
                case "1":
                    return "eleven";
                case "2":
                    return "twelve";
                case "3":
                    return "thirteen";
                case "4":
                    return "fourteen";
                case "5":
                    return "fifteen";
                case "6":
                    return "sixteen";
                case "7":
                    return "seventeen";
                case "8":
                    return "eighteen";
                case "9":
                    return "nineteen";
            }
        } else {
            $temp = $this->convertDigit($digit2);
            switch ($digit1) {
                case "2":
                    return "twenty $temp";
                case "3":
                    return "thirty $temp";
                case "4":
                    return "forty $temp";
                case "5":
                    return "fifty $temp";
                case "6":
                    return "sixty $temp";
                case "7":
                    return "seventy $temp";
                case "8":
                    return "eighty $temp";
                case "9":
                    return "ninety $temp";
            }
        }
    }

    function convertDigit($digit)
    {
        switch ($digit) {
                // case "0":
                //     return "zero";
            case "1":
                return "one";
            case "2":
                return "two";
            case "3":
                return "three";
            case "4":
                return "four";
            case "5":
                return "five";
            case "6":
                return "six";
            case "7":
                return "seven";
            case "8":
                return "eight";
            case "9":
                return "nine";
        }
    }
}