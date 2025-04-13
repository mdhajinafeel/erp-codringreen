<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Costsummaryreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Financemaster_model");
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
        $data["title"] = $this->lang->line("costsummaryreport_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_financereport";
        if (!empty($session)) {

            $data["productTypes"] = $this->Master_model->get_product_type();

            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financereports/costsummaryreport", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function fetch_export_sa_number()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getExportSA = $this->Financemaster_model->get_export_sa_numbers($this->input->get("originid"));

            $Return["result"] = $getExportSA;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function dialog_upload_container()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $data["pageheading"] = "Upload Container Data";
                $data["pagetype"] = "excelupload";
                $data["csrf_hash"] = $this->security->get_csrf_hash();

                $this->load->view("financereports/dialog_upload_container_data", $data);
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function fetch_containers()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $originId = $this->input->post("originid");
                $downloadType = $this->input->post("downloadtype");
                $startDate = $this->input->post("startdate");
                $endDate = $this->input->post("enddate");
                $processId = $this->input->post("downloadtype_processid");

                $wz_lang = $this->session->userdata("site_lang");
                $lang_code = $this->Settings_model->get_language_info($wz_lang);

                if ($downloadType == 1) {

                    $formartStartDate = $this->reformatDate($startDate, "d/m/Y", "Y-m-d");
                    $formartEndDate = $this->reformatDate($endDate, "d/m/Y", "Y-m-d");

                    if ($processId == 1 || $processId == 3) {
                        $processId = "1,3";
                    } else if ($processId == 2 || $processId == 4) {
                        $processId = "2,4";
                    }

                    $getExportContainers = $this->Financemaster_model->fetch_export_containers($originId, $processId, $formartStartDate, $formartEndDate, $lang_code[0]->language_format_code);

                    if (count($getExportContainers) > 0) {

                        // $dispatchIdList = [];
                        // foreach($getExportContainers as $exportcontainer) {
                        //     $dispatchIdList[] = $exportcontainer->dispatch_id;
                        // }

                        // $dispatchIds = implode(",", $dispatchIdList);

                        // $getcurrencycode = $this->Financemaster_model->get_currency_code($originId);

                        // $data = array(
                        //     "pageheading" => $this->lang->line("export_containers"),
                        //     "pagetype" => "viewcontainers",
                        //     "csrf_hash" => $this->security->get_csrf_hash(),
                        //     "exportcontainers" => $getExportContainers,
                        //     "currencycode" => $getcurrencycode[0]->currency_format,
                        //     "downloadType" => $downloadType,
                        //     "processId" => $this->input->post("downloadtype_processid"),
                        //     "originId" => $originId,
                        //     "costSummaryData" => $this->Financemaster_model->get_cost_summary_data($originId, $dispatchIds),
                        // );
                        // $this->load->view("financereports/dialog_viewcontainers", $data);

                        $this->generate_container_report($originId, $getExportContainers);
                    } else {
                        $Return["error"] = $this->lang->line("no_data_available");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
                        $Return["iserror"] = true;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else if ($downloadType == 2) {

                    $getExportContainers = $this->Financemaster_model->fetch_export_containers_by_exportid($originId, $processId, $lang_code[0]->language_format_code);
                    if (count($getExportContainers) > 0) {

                        // $dispatchIdList = [];
                        // foreach($getExportContainers as $exportcontainer) {
                        //     $dispatchIdList[] = $exportcontainer->dispatch_id;
                        // }

                        // $dispatchIds = implode(",", $dispatchIdList);

                        // $getcurrencycode = $this->Financemaster_model->get_currency_code($originId);

                        // $data = array(
                        //     "pageheading" => $this->lang->line("export_containers"),
                        //     "pagetype" => "viewcontainers",
                        //     "csrf_hash" => $this->security->get_csrf_hash(),
                        //     "exportcontainers" => $getExportContainers,
                        //     "currencycode" => $getcurrencycode[0]->currency_format,
                        //     "downloadType" => $downloadType,
                        //     "processId" => $this->input->post("downloadtype_processid"),
                        //     "originId" => $originId,
                        //     "costSummaryData" => $this->Financemaster_model->get_cost_summary_data($originId, $dispatchIds),
                        // );
                        // $this->load->view("financereports/dialog_viewcontainers", $data);
                        $this->generate_container_report($originId, $getExportContainers);
                    } else {
                        $Return["error"] = $this->lang->line("no_data_available");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
                        $Return["iserror"] = true;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["error"] = $this->lang->line("invalid_request");
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
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_cost_summaryreport_old()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return["csrf_hash"] = $this->security->get_csrf_hash();

                $originId = $this->input->post("originId");
                $dispatchIds = $this->input->post("dispatchIds");
                $processId = $this->input->post("processId");
                $downloadType = $this->input->post("downloadType");

                if ($downloadType == 1) {
                    $getProductTypeId = $processId;
                } else if ($downloadType == 2) {
                    $getProductType = $this->Financemaster_model->get_producttype_by_exportid($originId, $processId);
                    $getProductTypeId = $getProductType[0]->product_type_id;
                } else {
                    $Return["error"] = $this->lang->line("invalid_request");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }

                $dispatchIdsJson = json_decode($dispatchIds, true);

                if (count($dispatchIdsJson) > 0) {

                    $getcurrencycode = $this->Financemaster_model->get_currency_code($originId);

                    //START EXCEL

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("costsummaryreport_title"));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName("Calibri")
                        ->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    //HEADING

                    $objSheet->SetCellValue("A5", "#");
                    $objSheet->SetCellValue("B5", $this->lang->line("container_number"));
                    $objSheet->SetCellValue("C5", $this->lang->line("net_volume"));
                    $objSheet->SetCellValue("D5", $this->lang->line("total_no_of_pieces"));
                    $objSheet->SetCellValue("E5", $this->lang->line("text_cft"));
                    $objSheet->SetCellValue("F5", $this->lang->line("material_cost"));
                    $objSheet->SetCellValue("G5", $this->lang->line("export_cost"));
                    $objSheet->SetCellValue("H5", $this->lang->line("freight_cost"));
                    $objSheet->SetCellValue("I5", $this->lang->line("exchange_rate"));
                    $objSheet->SetCellValue("J5", $this->lang->line("cif_cost"));
                    $objSheet->SetCellValue("K5", $this->lang->line("sales_cost"));
                    $objSheet->SetCellValue("L5", $this->lang->line("sales_value"));
                    $objSheet->SetCellValue("M5", $this->lang->line("text_gc"));
                    $objSheet->SetCellValue("N5", $this->lang->line("text_age_ret"));

                    $objSheet->SetCellValue("M1", $this->lang->line("gc_total"));
                    $objSheet->SetCellValue("M2", str_replace("-cur-", $getcurrencycode[0]->currency_format, $this->lang->line("text_gc_per_cbm")));
                    $objSheet->SetCellValue("L3", $this->lang->line("text_gc_usd_cbm"));

                    $objSheet->getStyle("A5:N5")
                        ->getFont()
                        ->setBold(true);
                    $objSheet->getStyle("L3:M3")
                        ->getFont()
                        ->setBold(true);
                    $objSheet->getStyle("M1:M2")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("A5:N5")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("A9D08E");

                    $objSheet->getStyle("A5:N5")->applyFromArray($styleArray);

                    $objSheet->getStyle("M1:N1")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("B4C6E7");
                    $objSheet->getStyle("M1:N1")->applyFromArray($styleArray);

                    $objSheet->getStyle("M2:N2")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("A9D08E");
                    $objSheet->getStyle("M2:N2")->applyFromArray($styleArray);
                    $objSheet->getStyle("L3:M3")->applyFromArray($styleArray);
                    $objSheet->getStyle("L3")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("M1:M2")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A5:N5")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("C4:N4")->applyFromArray($styleArray);

                    //END HEADING

                    //DATA FEED

                    $rowDataCount = 6;
                    $i = 1;

                    if ($getProductTypeId == 1 || $getProductTypeId == 3) {
                    } else if ($getProductTypeId == 2 || $getProductTypeId == 4) {

                        $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(2, $originId);
                        $strNetFormula = "";

                        foreach ($getFormulae as $formula) {
                            if ($formula->context == "CBM_HOPPUS_NETVOLUME_DISPATCH") {
                                $strNetFormula = str_replace(array('$l', '$c', '$pcs'), array("length_bought", "circumference_bought", "SUM(dispatch_pieces)"), $formula->calculation_formula);
                            }
                        }

                        foreach ($dispatchIdsJson as $dispatchdata) {

                            $dispatchId = $dispatchdata["dispatchid"];
                            $containerNumber = $dispatchdata["containernumber"];
                            $cftValue = $dispatchdata["cftvalue"];
                            $exportCost = $dispatchdata["exportcost"];
                            $freightCost = $dispatchdata["freightcost"];
                            $tasaCost = $dispatchdata["exchangerate"];
                            $salesCost = $dispatchdata["salescost"];
                            $exportCostEnabled = $dispatchdata["exportcostenabled"];
                            $freightCostEnabled = $dispatchdata["freightcostenabled"];
                            $tasaCostEnabled = $dispatchdata["exchangerateenabled"];
                            $salesCostEnabled = $dispatchdata["salescostenabled"];

                            $getCostSummaryData = $this->Financemaster_model->get_cost_summary_data_count($originId, $dispatchId, $containerNumber);

                            if ($getCostSummaryData[0]->cnt == 0) {

                                if (
                                    $exportCostEnabled == true || $freightCostEnabled == true
                                    || $tasaCostEnabled == true || $salesCostEnabled == true
                                ) {

                                    $dataCostSummary = array(
                                        "dispatch_id" => $dispatchId, "container_number" => $containerNumber,
                                        "export_cost" => $exportCost, "freight_cost" => $freightCost,
                                        "tasa_cost" => $tasaCost, "sales_cost" => $salesCost,
                                        "created_by" => $session['user_id'], "updated_by" => $session['user_id'],
                                        "is_active" => 1, "origin_id" => $originId,
                                    );

                                    $insertCostSummary = $this->Financemaster_model->add_cost_summary($dataCostSummary);
                                }
                            } else {
                                if (
                                    $exportCostEnabled == true || $freightCostEnabled == true
                                    || $tasaCostEnabled == true || $salesCostEnabled == true
                                ) {
                                    $dataCostSummary = array(
                                        "export_cost" => $exportCost, "freight_cost" => $freightCost,
                                        "tasa_cost" => $tasaCost, "sales_cost" => $salesCost,
                                        "updated_by" => $session['user_id'],
                                    );

                                    $updateCostSummary = $this->Financemaster_model->update_cost_summary($dispatchId, $containerNumber, $dataCostSummary);
                                }
                            }

                            $fetchVolume = $this->Financemaster_model->get_total_volume($dispatchId, $strNetFormula);

                            $objSheet->SetCellValue("A$rowDataCount", $i);
                            $objSheet->SetCellValue("B$rowDataCount", $containerNumber);

                            if (
                                $fetchVolume[0]->total_pieces > 0 &&
                                $fetchVolume[0]->netvolume > 0
                            ) {

                                $getMaterialCost = $this->Financemaster_model->get_material_cost_by_dispatch($originId, $getProductTypeId, $dispatchId);

                                $objSheet->SetCellValue("C$rowDataCount", $fetchVolume[0]->netvolume);
                                $objSheet->SetCellValue("D$rowDataCount", $fetchVolume[0]->total_pieces);
                                $objSheet->SetCellValue("E$rowDataCount", $cftValue);
                                $objSheet->SetCellValue("F$rowDataCount", $getMaterialCost[0]->material_cost);

                                if ($exportCost == 0 && $exportCostEnabled == false) {
                                    $objSheet->SetCellValue("G$rowDataCount", "1");
                                } else {
                                    $objSheet->SetCellValue("G$rowDataCount", $exportCost);
                                }

                                if ($freightCost == 0 && $freightCostEnabled == false) {
                                    $objSheet->SetCellValue("H$rowDataCount", "1");
                                } else {
                                    $objSheet->SetCellValue("H$rowDataCount", $freightCost);
                                }

                                if ($tasaCost == 0 && $tasaCostEnabled == false) {
                                    $objSheet->SetCellValue("I$rowDataCount", "1");
                                } else {
                                    $objSheet->SetCellValue("I$rowDataCount", $tasaCost);
                                }

                                $objSheet->SetCellValue("J$rowDataCount", "=SUM(F$rowDataCount:H$rowDataCount)/I$rowDataCount");

                                if ($salesCost == 0 && $salesCostEnabled == false) {
                                    $objSheet->SetCellValue("K$rowDataCount", "1");
                                } else {
                                    $objSheet->SetCellValue("K$rowDataCount", $salesCost);
                                }

                                $objSheet->SetCellValue("L$rowDataCount", "=C$rowDataCount*K$rowDataCount");
                                $objSheet->SetCellValue("M$rowDataCount", "=L$rowDataCount-J$rowDataCount");
                                $objSheet->SetCellValue("N$rowDataCount", "=M$rowDataCount/J$rowDataCount");
                            } else {
                                $objSheet->SetCellValue("C$rowDataCount", "0");
                                $objSheet->SetCellValue("D$rowDataCount", "0");
                                $objSheet->SetCellValue("E$rowDataCount", "0");
                                $objSheet->SetCellValue("F$rowDataCount", "0");
                                $objSheet->SetCellValue("G$rowDataCount", "0");
                                $objSheet->SetCellValue("H$rowDataCount", "0");
                                $objSheet->SetCellValue("I$rowDataCount", "0");
                                $objSheet->SetCellValue("J$rowDataCount", "0");
                                $objSheet->SetCellValue("K$rowDataCount", "0");
                                $objSheet->SetCellValue("L$rowDataCount", "0");
                                $objSheet->SetCellValue("M$rowDataCount", "0");
                                $objSheet->SetCellValue("N$rowDataCount", "0");
                            }

                            $i++;
                            $rowDataCount++;
                        }
                    }

                    //END DATA FEED

                    $rowDataCount = $rowDataCount - 1;

                    $objSheet->getStyle("C6:C$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("F6:F$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);
                    $objSheet->getStyle("G6:G$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);
                    $objSheet->getStyle("H6:H$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);
                    $objSheet->getStyle("I6:I$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);
                    $objSheet->getStyle("J6:J$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);
                    $objSheet->getStyle("K6:K$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);
                    $objSheet->getStyle("L6:L$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);
                    $objSheet->getStyle("M6:M$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);
                    $objSheet->getStyle("N6:N$rowDataCount")
                        ->getNumberFormat()
                        ->applyFromArray(array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                        ));

                    //CALC DATA

                    $objSheet->SetCellValue("C4", "=SUM(C6:C$rowDataCount)");
                    $objSheet->getStyle("C4")
                        ->getNumberFormat()
                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $objSheet->SetCellValue("D4", "=SUM(D6:D$rowDataCount)");
                    $objSheet->getStyle("D4")
                        ->getNumberFormat()
                        ->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objSheet->SetCellValue("F4", "=SUM(F6:F$rowDataCount)");
                    $objSheet->SetCellValue("G4", "=SUM(G6:G$rowDataCount)");
                    $objSheet->SetCellValue("H4", "=SUM(H6:H$rowDataCount)");
                    $objSheet->SetCellValue("I4", "=SUMPRODUCT(I6:I$rowDataCount,F6:F$rowDataCount)/F4");
                    $objSheet->getStyle("F4:I4")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);

                    $objSheet->SetCellValue("J4", "=SUM(J6:J$rowDataCount)");
                    $objSheet->getStyle("J4")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);

                    $objSheet->SetCellValue("L4", "=SUM(L6:L$rowDataCount)");
                    $objSheet->getStyle("L4")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);

                    $objSheet->SetCellValue("K4", "=L4/C4");
                    $objSheet->getStyle("K4")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);

                    $objSheet->SetCellValue("M4", "=SUM(M6:M$rowDataCount)");
                    $objSheet->getStyle("M4")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);

                    $objSheet->SetCellValue("N4", "=M4/J4");
                    $objSheet->getStyle("N4")
                        ->getNumberFormat()
                        ->applyFromArray(array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                        ));

                    $objSheet->SetCellValue("M3", "=M4/C4");
                    $objSheet->getStyle("M3")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[1]->currency_excel_format1);

                    $objSheet->SetCellValue("N2", "=M3*I4");
                    $objSheet->getStyle("N2")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);

                    $objSheet->SetCellValue("N1", "=M4*I4");
                    $objSheet->getStyle("N1")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format1);

                    //END CALC DATA

                    $objSheet->getStyle("A6:N$rowDataCount")->applyFromArray($styleArray);

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(false);
                    $objSheet->getColumnDimension("B")->setWidth("15");
                    $objSheet->getColumnDimension("C")->setAutoSize(false);
                    $objSheet->getColumnDimension("C")->setWidth("15");
                    $objSheet->getColumnDimension("D")->setAutoSize(false);
                    $objSheet->getColumnDimension("D")->setWidth("15");
                    $objSheet->getColumnDimension("E")->setAutoSize(false);
                    $objSheet->getColumnDimension("E")->setWidth("18");
                    $objSheet->getColumnDimension("F")->setAutoSize(false);
                    $objSheet->getColumnDimension("F")->setWidth("18");
                    $objSheet->getColumnDimension("G")->setAutoSize(false);
                    $objSheet->getColumnDimension("G")->setWidth("15");
                    $objSheet->getColumnDimension("H")->setAutoSize(false);
                    $objSheet->getColumnDimension("H")->setWidth("15");
                    $objSheet->getColumnDimension("I")->setAutoSize(false);
                    $objSheet->getColumnDimension("I")->setWidth("15");
                    $objSheet->getColumnDimension("J")->setAutoSize(false);
                    $objSheet->getColumnDimension("J")->setWidth("20");
                    $objSheet->getColumnDimension("K")->setAutoSize(false);
                    $objSheet->getColumnDimension("K")->setWidth("15");
                    $objSheet->getColumnDimension("L")->setAutoSize(false);
                    $objSheet->getColumnDimension("L")->setWidth("15");
                    $objSheet->getColumnDimension("M")->setAutoSize(false);
                    $objSheet->getColumnDimension("M")->setWidth("18");
                    $objSheet->getColumnDimension("N")->setAutoSize(false);
                    $objSheet->getColumnDimension("N")->setWidth("18");

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "CostSummaryReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/CostSummaryReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/CostSummaryReports/" . $filename;
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
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_container_report($originid, $containerdata)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return["csrf_hash"] = $this->security->get_csrf_hash();

                if (count($containerdata) > 0) {

                    $getcurrencycode = $this->Financemaster_model->get_currency_code($originid);

                    //START EXCEL

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("containers"));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName("Calibri")
                        ->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    //HEADING

                    $objSheet->SetCellValue("A1", $this->lang->line("DISPATCH ID"));
                    $objSheet->SetCellValue("B1", $this->lang->line("REFERENCE SA"));
                    $objSheet->SetCellValue("C1", $this->lang->line("MES"));
                    $objSheet->SetCellValue("D1", $this->lang->line("CONTAINER #"));
                    $objSheet->SetCellValue("E1", $this->lang->line("VOLUMEN"));
                    $objSheet->SetCellValue("F1", strtoupper($this->lang->line("text_cft")));
                    $objSheet->SetCellValue("G1", $this->lang->line("FRA AGENCIA DE ADUANAS"));
                    $objSheet->SetCellValue("H1", $this->lang->line("CUSTOMS AGENCY"));
                    $objSheet->SetCellValue("I1", $this->lang->line("FRA TRANSPORTE"));
                    $objSheet->SetCellValue("J1", $this->lang->line("TRANSPORTTE / ITR"));
                    $objSheet->SetCellValue("K1", $this->lang->line("FACTURA PUERTO"));
                    $objSheet->SetCellValue("L1", $this->lang->line("PUERTO"));
                    $objSheet->SetCellValue("M1", $this->lang->line("PHYTO"));
                    $objSheet->SetCellValue("N1", $this->lang->line("FACTURA DE FUMIGACION"));
                    $objSheet->SetCellValue("O1", $this->lang->line("FUMIGACION"));
                    $objSheet->SetCellValue("P1", $this->lang->line("FACTURA DHL"));
                    $objSheet->SetCellValue("Q1", $this->lang->line("DHL"));
                    $objSheet->SetCellValue("R1", $this->lang->line("COTEROS"));
                    $objSheet->SetCellValue("S1", $this->lang->line("SELLOS PROVISIONALES"));
                    $objSheet->SetCellValue("T1", $this->lang->line("INCENTIVO"));
                    $objSheet->SetCellValue("U1", $this->lang->line("REMOVILIZACION"));
                    $objSheet->SetCellValue("V1", $this->lang->line("FACTURA NAVIERA"));
                    $objSheet->SetCellValue("W1", $this->lang->line("PUERTO BODEGAJE"));
                    $objSheet->SetCellValue("X1", $this->lang->line("FLETE"));
                    $objSheet->SetCellValue("Y1", $this->lang->line("VR VENTA UNT"));
                    $objSheet->SetCellValue("Z1", $this->lang->line("TRM"));
                    $objSheet->SetCellValue("AA1", $this->lang->line("PERDIDA / GANANCIA"));
                    $objSheet->SetCellValue("AB1", $this->lang->line("DEX"));
                    $objSheet->SetCellValue("AC1", $this->lang->line("OBSERVACION"));

                    $objSheet->getStyle("A1:AC1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A1:AC1")->getAlignment()->setWrapText(true);

                    $objSheet->getStyle("A1:AC1")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("A1:AC1")->applyFromArray($styleArray);


                    //END HEADING

                    //DATA FEED

                    $rowDataCount = 2;

                    foreach ($containerdata as $cdata) {
                        $objSheet->SetCellValue("A$rowDataCount", $cdata->dispatch_id);
                        $objSheet->SetCellValue("B$rowDataCount", $cdata->sa_number);
                        $objSheet->SetCellValue("C$rowDataCount", $cdata->shipped_date);
                        $objSheet->SetCellValue("D$rowDataCount", $cdata->container_number);
                        $objSheet->SetCellValue("E$rowDataCount", $cdata->net_volume);
                        $objSheet->SetCellValue("F$rowDataCount", $cdata->cft_value);
                        $objSheet->SetCellValue("G$rowDataCount", $cdata->custom_agency_invoice_number);
                        $objSheet->SetCellValue("H$rowDataCount", $cdata->custom_agency_cost);
                        $objSheet->SetCellValue("I$rowDataCount", $cdata->transport_invoice_number);
                        $objSheet->SetCellValue("J$rowDataCount", $cdata->transport_cost);
                        $objSheet->SetCellValue("K$rowDataCount", $cdata->port_invoice_number);
                        $objSheet->SetCellValue("L$rowDataCount", $cdata->port_cost);
                        $objSheet->SetCellValue("M$rowDataCount", $cdata->phyto_cost);
                        $objSheet->SetCellValue("N$rowDataCount", $cdata->fumigation_invoice_number);
                        $objSheet->SetCellValue("O$rowDataCount", $cdata->fumigation_cost);
                        $objSheet->SetCellValue("P$rowDataCount", $cdata->dhl_invoice_number);
                        $objSheet->SetCellValue("Q$rowDataCount", $cdata->dhl_cost);
                        $objSheet->SetCellValue("R$rowDataCount", $cdata->coteros_cost);
                        $objSheet->SetCellValue("S$rowDataCount", $cdata->provistional_stamp_cost);
                        $objSheet->SetCellValue("T$rowDataCount", $cdata->incentive);
                        $objSheet->SetCellValue("U$rowDataCount", $cdata->mobilization_cost);
                        $objSheet->SetCellValue("V$rowDataCount", $cdata->shipping_invoice_number);
                        $objSheet->SetCellValue("W$rowDataCount", $cdata->warehouse_port_cost);
                        $objSheet->SetCellValue("X$rowDataCount", $cdata->freight_cost);
                        $objSheet->SetCellValue("Y$rowDataCount", $cdata->sales_cost);
                        $objSheet->SetCellValue("Z$rowDataCount", $cdata->exchange_rate);
                        $objSheet->SetCellValue("AA$rowDataCount", $cdata->loss_profit);
                        $objSheet->SetCellValue("AB$rowDataCount", $cdata->document_number);
                        $objSheet->getStyle("AB$rowDataCount")
                            ->getNumberFormat()
                            ->setFormatCode('0');
                        $objSheet->SetCellValue("AC$rowDataCount", $cdata->observation);

                        $rowDataCount++;
                    }

                    //END DATA FEED

                    $rowDataCount = $rowDataCount - 1;

                    //CALC DATA



                    //END CALC DATA

                    $objSheet->getStyle("A2:AC$rowDataCount")->applyFromArray($styleArray);

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(false);
                    $objSheet->getColumnDimension("B")->setWidth("15");
                    $objSheet->getColumnDimension("C")->setAutoSize(false);
                    $objSheet->getColumnDimension("C")->setWidth("15");
                    $objSheet->getColumnDimension("D")->setAutoSize(false);
                    $objSheet->getColumnDimension("D")->setWidth("15");
                    $objSheet->getColumnDimension("E")->setAutoSize(false);
                    $objSheet->getColumnDimension("E")->setWidth("18");
                    $objSheet->getColumnDimension("F")->setAutoSize(false);
                    $objSheet->getColumnDimension("F")->setWidth("18");
                    $objSheet->getColumnDimension("G")->setAutoSize(false);
                    $objSheet->getColumnDimension("G")->setWidth("15");
                    $objSheet->getColumnDimension("H")->setAutoSize(false);
                    $objSheet->getColumnDimension("H")->setWidth("15");
                    $objSheet->getColumnDimension("I")->setAutoSize(false);
                    $objSheet->getColumnDimension("I")->setWidth("15");
                    $objSheet->getColumnDimension("J")->setAutoSize(false);
                    $objSheet->getColumnDimension("J")->setWidth("20");
                    $objSheet->getColumnDimension("K")->setAutoSize(false);
                    $objSheet->getColumnDimension("K")->setWidth("15");
                    $objSheet->getColumnDimension("L")->setAutoSize(false);
                    $objSheet->getColumnDimension("L")->setWidth("15");
                    $objSheet->getColumnDimension("M")->setAutoSize(false);
                    $objSheet->getColumnDimension("M")->setWidth("18");
                    $objSheet->getColumnDimension("N")->setAutoSize(false);
                    $objSheet->getColumnDimension("N")->setWidth("18");
                    $objSheet->getColumnDimension("O")->setAutoSize(false);
                    $objSheet->getColumnDimension("O")->setWidth("15");
                    $objSheet->getColumnDimension("P")->setAutoSize(false);
                    $objSheet->getColumnDimension("P")->setWidth("15");
                    $objSheet->getColumnDimension("Q")->setAutoSize(false);
                    $objSheet->getColumnDimension("Q")->setWidth("15");
                    $objSheet->getColumnDimension("R")->setAutoSize(false);
                    $objSheet->getColumnDimension("R")->setWidth("15");
                    $objSheet->getColumnDimension("S")->setAutoSize(false);
                    $objSheet->getColumnDimension("S")->setWidth("15");
                    $objSheet->getColumnDimension("T")->setAutoSize(false);
                    $objSheet->getColumnDimension("T")->setWidth("15");
                    $objSheet->getColumnDimension("U")->setAutoSize(false);
                    $objSheet->getColumnDimension("U")->setWidth("15");
                    $objSheet->getColumnDimension("V")->setAutoSize(false);
                    $objSheet->getColumnDimension("V")->setWidth("15");
                    $objSheet->getColumnDimension("W")->setAutoSize(false);
                    $objSheet->getColumnDimension("W")->setWidth("15");
                    $objSheet->getColumnDimension("X")->setAutoSize(false);
                    $objSheet->getColumnDimension("X")->setWidth("15");
                    $objSheet->getColumnDimension("Y")->setAutoSize(false);
                    $objSheet->getColumnDimension("Y")->setWidth("15");
                    $objSheet->getColumnDimension("Z")->setAutoSize(false);
                    $objSheet->getColumnDimension("Z")->setWidth("15");
                    $objSheet->getColumnDimension("AA")->setAutoSize(false);
                    $objSheet->getColumnDimension("AA")->setWidth("15");
                    $objSheet->getColumnDimension("AB")->setAutoSize(false);
                    $objSheet->getColumnDimension("AB")->setWidth("15");
                    $objSheet->getColumnDimension("AC")->setAutoSize(false);
                    $objSheet->getColumnDimension("AC")->setWidth("15");

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "ContainerReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/CostSummaryReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/CostSummaryReports/" . $filename;
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
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_report()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $originId = $this->input->post("originid");
                $downloadType = $this->input->post("downloadtype");
                $startDate = $this->input->post("startdate");
                $endDate = $this->input->post("enddate");
                $processId = $this->input->post("downloadtype_processid");

                $wz_lang = $this->session->userdata("site_lang");
                $lang_code = $this->Settings_model->get_language_info($wz_lang);

                if ($downloadType == 1) {

                    $formartStartDate = $this->reformatDate($startDate, "d/m/Y", "Y-m-d");
                    $formartEndDate = $this->reformatDate($endDate, "d/m/Y", "Y-m-d");

                    if ($processId == 1 || $processId == 3) {
                        $processId = "1,3";
                    } else if ($processId == 2 || $processId == 4) {
                        $processId = "2,4";
                    }

                    $getExportContainers = $this->Financemaster_model->fetch_export_containers($originId, $processId, $formartStartDate, $formartEndDate, $lang_code[0]->language_format_code);

                    if (count($getExportContainers) > 0) {

                        $this->generate_cost_summaryreport($originId, $getExportContainers);
                    } else {
                        $Return["error"] = $this->lang->line("no_data_available");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
                        $Return["iserror"] = true;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else if ($downloadType == 2) {

                    $getExportContainers = $this->Financemaster_model->fetch_export_containers_by_exportid($originId, $processId, $lang_code[0]->language_format_code);
                    if (count($getExportContainers) > 0) {

                        $this->generate_cost_summaryreport($originId, $getExportContainers);
                    } else {
                        $Return["error"] = $this->lang->line("no_data_available");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
                        $Return["iserror"] = true;
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["error"] = $this->lang->line("invalid_request");
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
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_cost_summaryreport($originid, $containerdata)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return["csrf_hash"] = $this->security->get_csrf_hash();

                if (count($containerdata) > 0) {

                    $getcurrencycode = $this->Financemaster_model->get_currency_code($originid);

                    //START EXCEL

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("costsummaryreport_title"));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName("Calibri")
                        ->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    //HEADING

                    $objSheet->SetCellValue("A3", $this->lang->line("REFERENCE SA"));
                    $objSheet->SetCellValue("B3", $this->lang->line("CONTAINER #"));
                    $objSheet->SetCellValue("C3", $this->lang->line("VOLUMEN"));
                    $objSheet->SetCellValue("D3", strtoupper($this->lang->line("text_cft")));
                    $objSheet->SetCellValue("E3", $this->lang->line("FRA AGENCIA DE ADUANAS"));
                    $objSheet->SetCellValue("F3", $this->lang->line("CUSTOMS AGENCY"));
                    $objSheet->SetCellValue("G3", $this->lang->line("FRA TRANSPORTE"));
                    $objSheet->SetCellValue("H3", $this->lang->line("TRANSPORTTE / ITR"));
                    $objSheet->SetCellValue("I3", $this->lang->line("FACTURA PUERTO"));
                    $objSheet->SetCellValue("J3", $this->lang->line("PUERTO"));
                    $objSheet->SetCellValue("K3", $this->lang->line("PHYTO"));
                    $objSheet->SetCellValue("L3", $this->lang->line("FACTURA DE FUMIGACION"));
                    $objSheet->SetCellValue("M3", $this->lang->line("FUMIGACION"));
                    $objSheet->SetCellValue("N3", $this->lang->line("FACTURA DHL"));
                    $objSheet->SetCellValue("O3", $this->lang->line("DHL"));
                    $objSheet->SetCellValue("P3", $this->lang->line("COTEROS"));
                    $objSheet->SetCellValue("Q3", $this->lang->line("SELLOS PROVISIONALES"));
                    $objSheet->SetCellValue("R3", $this->lang->line("INCENTIVO"));
                    $objSheet->SetCellValue("S3", $this->lang->line("REMOVILIZACION"));
                    $objSheet->SetCellValue("T3", $this->lang->line("TOTAL DE EXPORTACION POR CONTENEDOR"));
                    $objSheet->SetCellValue("U3", $this->lang->line("FACTURA NAVIERA"));
                    $objSheet->SetCellValue("V3", $this->lang->line("PUERTO BODEGAJE"));
                    $objSheet->SetCellValue("W3", $this->lang->line("FLETE"));
                    $objSheet->SetCellValue("X3", $this->lang->line("COSTO DE MATERIAL"));
                    $objSheet->SetCellValue("Y3", $this->lang->line("TOTAL DE LOS COSTOS"));
                    $objSheet->SetCellValue("Z3", $this->lang->line("VR VENTA UNT"));
                    $objSheet->SetCellValue("AA3", $this->lang->line("VR VENTA CONT TOTAL"));
                    $objSheet->SetCellValue("AB3", $this->lang->line("TRM"));
                    $objSheet->SetCellValue("AC3", $this->lang->line("VR PESOS INGRESO"));
                    $objSheet->SetCellValue("AD3", $this->lang->line("PERDIDA / GANANCIA"));
                    $objSheet->SetCellValue("AE3", $this->lang->line("MES"));
                    $objSheet->SetCellValue("AF3", $this->lang->line("DEX"));
                    $objSheet->SetCellValue("AG3", $this->lang->line("OBSERVACION"));

                    $objSheet->getStyle("A3:AG3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A3:AG3")->getAlignment()->setWrapText(true);

                    $objSheet->getStyle("A3:AG3")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("A3:AG3")->applyFromArray($styleArray);


                    //END HEADING

                    //DATA FEED

                    $rowDataCount = 4;

                    foreach ($containerdata as $cdata) {
                        $objSheet->SetCellValue("A$rowDataCount", $cdata->sa_number);
                        $objSheet->SetCellValue("B$rowDataCount", $cdata->container_number);
                        $objSheet->SetCellValue("C$rowDataCount", $cdata->net_volume);
                        $objSheet->SetCellValue("D$rowDataCount", $cdata->cft_value);
                        $objSheet->SetCellValue("E$rowDataCount", $cdata->custom_agency_invoice_number);
                        $objSheet->SetCellValue("F$rowDataCount", $cdata->custom_agency_cost);
                        $objSheet->SetCellValue("G$rowDataCount", $cdata->transport_invoice_number);
                        $objSheet->SetCellValue("H$rowDataCount", $cdata->transport_cost);
                        $objSheet->SetCellValue("I$rowDataCount", $cdata->port_invoice_number);
                        $objSheet->SetCellValue("J$rowDataCount", $cdata->port_cost);
                        $objSheet->SetCellValue("K$rowDataCount", $cdata->phyto_cost);
                        $objSheet->SetCellValue("L$rowDataCount", $cdata->fumigation_invoice_number);
                        $objSheet->SetCellValue("M$rowDataCount", $cdata->fumigation_cost);
                        $objSheet->SetCellValue("N$rowDataCount", $cdata->dhl_invoice_number);
                        $objSheet->SetCellValue("O$rowDataCount", $cdata->dhl_cost);
                        $objSheet->SetCellValue("P$rowDataCount", $cdata->coteros_cost);
                        $objSheet->SetCellValue("Q$rowDataCount", $cdata->provistional_stamp_cost);
                        $objSheet->SetCellValue("R$rowDataCount", $cdata->incentive);
                        $objSheet->SetCellValue("S$rowDataCount", $cdata->mobilization_cost);
                        $objSheet->SetCellValue("T$rowDataCount", "=+S$rowDataCount+R$rowDataCount+Q$rowDataCount+P$rowDataCount+O$rowDataCount+M$rowDataCount+K$rowDataCount+J$rowDataCount+H$rowDataCount+F$rowDataCount");
                        $objSheet->SetCellValue("U$rowDataCount", $cdata->shipping_invoice_number);
                        $objSheet->SetCellValue("V$rowDataCount", $cdata->warehouse_port_cost);
                        $objSheet->SetCellValue("W$rowDataCount", $cdata->freight_cost);
                        $objSheet->SetCellValue("X$rowDataCount", $cdata->material_cost);
                        $objSheet->SetCellValue("Y$rowDataCount", "=+T$rowDataCount+W$rowDataCount+X$rowDataCount");
                        $objSheet->SetCellValue("Z$rowDataCount", $cdata->sales_cost);
                        $objSheet->SetCellValue("AA$rowDataCount", "=Z$rowDataCount*C$rowDataCount");
                        $objSheet->SetCellValue("AB$rowDataCount", $cdata->exchange_rate);
                        $objSheet->SetCellValue("AC$rowDataCount", "=+(AB$rowDataCount*AA$rowDataCount)-Y$rowDataCount");
                        $objSheet->SetCellValue("AD$rowDataCount", $cdata->loss_profit);
                        $objSheet->SetCellValue("AE$rowDataCount", strtoupper($cdata->shipped_date));
                        $objSheet->SetCellValue("AF$rowDataCount", $cdata->document_number);
                        $objSheet->SetCellValue("AG$rowDataCount", $cdata->observation);


                        $rowDataCount++;
                    }

                    //END DATA FEED

                    $rowDataCount = $rowDataCount - 1;

                    $objSheet->getStyle("A4:AG$rowDataCount")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("C4:D$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $objSheet->getStyle("F4:F$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("H4:H$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("J4:J$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("K4:K$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("M4:M$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("P4:P$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"??_-;_-@_-');

                    $objSheet->getStyle("Q4:T$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("V4:AD$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');

                    $objSheet->getStyle("AD4:AD$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0_);[Red](#,##0)');

                    $objSheet->getStyle("AF4:AF$rowDataCount")
                        ->getNumberFormat()
                        ->setFormatCode('0');

                    $objSheet->getStyle("A4:AG$rowDataCount")->applyFromArray($styleArray);

                    //CALC DATA

                    $objSheet->SetCellValue("A2", "=+F2+H2+J2+K2+P2+S2");
                    $objSheet->getStyle("A2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("A2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("C2", "=SUM(C4:C$rowDataCount)");
                    $objSheet->getStyle("C2")
                        ->getNumberFormat()
                        ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("C2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("F2", "=SUM(F4:F$rowDataCount)");
                    $objSheet->getStyle("F2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("F2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("H2", "=SUM(H4:H$rowDataCount)");
                    $objSheet->getStyle("H2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("H2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("J2", "=SUM(J4:J$rowDataCount)");
                    $objSheet->getStyle("J2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("J2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("K2", "=SUM(K4:K$rowDataCount)");
                    $objSheet->getStyle("K2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("K2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("M2", "=SUM(M4:M$rowDataCount)");
                    $objSheet->getStyle("M2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("M2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("O2", "=SUM(O4:O$rowDataCount)");
                    $objSheet->SetCellValue("P2", "=SUM(P4:P$rowDataCount)");
                    $objSheet->SetCellValue("Q2", "=SUM(Q4:Q$rowDataCount)");
                    $objSheet->SetCellValue("R2", "=SUM(R4:R$rowDataCount)");
                    $objSheet->SetCellValue("S2", "=SUM(S4:S$rowDataCount)");
                    $objSheet->SetCellValue("T2", "=SUM(T4:T$rowDataCount)");
                    $objSheet->getStyle("O2:T2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("O2:T2")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("V2", "=SUM(V4:V$rowDataCount)");
                    $objSheet->SetCellValue("W2", "=SUM(W4:W$rowDataCount)");
                    $objSheet->SetCellValue("X2", "=SUM(X4:X$rowDataCount)");
                    $objSheet->SetCellValue("Y2", "=SUM(Y4:Y$rowDataCount)");
                    $objSheet->SetCellValue("Z2", "=SUM(Z4:Z$rowDataCount)");
                    $objSheet->SetCellValue("AA2", "=SUM(AA4:AA$rowDataCount)");
                    $objSheet->SetCellValue("AB2", "=SUM(AB4:AB$rowDataCount)");
                    $objSheet->SetCellValue("AC2", "=SUM(AC4:AC$rowDataCount)");
                    $objSheet->SetCellValue("AD2", "=SUM(AD4:AD$rowDataCount)");
                    $objSheet->getStyle("V2:AD2")
                        ->getNumberFormat()
                        ->setFormatCode('_-"$" * #,##0_-;-"$" * #,##0_-;_-"$" * "-"??_-;_-@_-');
                    $objSheet->getStyle("V2:AD2")->applyFromArray($styleArray);

                    //END CALC DATA

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(false);
                    $objSheet->getColumnDimension("B")->setWidth("15");
                    $objSheet->getColumnDimension("C")->setAutoSize(false);
                    $objSheet->getColumnDimension("C")->setWidth("15");
                    $objSheet->getColumnDimension("D")->setAutoSize(false);
                    $objSheet->getColumnDimension("D")->setWidth("15");
                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                    $objSheet->getColumnDimension("F")->setAutoSize(false);
                    $objSheet->getColumnDimension("F")->setWidth("18");
                    $objSheet->getColumnDimension("G")->setAutoSize(true);
                    $objSheet->getColumnDimension("H")->setAutoSize(false);
                    $objSheet->getColumnDimension("H")->setWidth("15");
                    $objSheet->getColumnDimension("I")->setAutoSize(true);
                    $objSheet->getColumnDimension("J")->setAutoSize(false);
                    $objSheet->getColumnDimension("J")->setWidth("20");
                    $objSheet->getColumnDimension("K")->setAutoSize(false);
                    $objSheet->getColumnDimension("K")->setWidth("15");
                    $objSheet->getColumnDimension("L")->setAutoSize(true);
                    $objSheet->getColumnDimension("M")->setAutoSize(false);
                    $objSheet->getColumnDimension("M")->setWidth("18");
                    $objSheet->getColumnDimension("N")->setAutoSize(true);
                    $objSheet->getColumnDimension("O")->setAutoSize(false);
                    $objSheet->getColumnDimension("O")->setWidth("15");
                    $objSheet->getColumnDimension("P")->setAutoSize(false);
                    $objSheet->getColumnDimension("P")->setWidth("15");
                    $objSheet->getColumnDimension("Q")->setAutoSize(false);
                    $objSheet->getColumnDimension("Q")->setWidth("15");
                    $objSheet->getColumnDimension("R")->setAutoSize(false);
                    $objSheet->getColumnDimension("R")->setWidth("15");
                    $objSheet->getColumnDimension("S")->setAutoSize(false);
                    $objSheet->getColumnDimension("S")->setWidth("15");
                    $objSheet->getColumnDimension("T")->setAutoSize(false);
                    $objSheet->getColumnDimension("T")->setWidth("15");
                    $objSheet->getColumnDimension("U")->setAutoSize(true);
                    $objSheet->getColumnDimension("V")->setAutoSize(false);
                    $objSheet->getColumnDimension("V")->setWidth("15");
                    $objSheet->getColumnDimension("W")->setAutoSize(false);
                    $objSheet->getColumnDimension("W")->setWidth("15");
                    $objSheet->getColumnDimension("X")->setAutoSize(false);
                    $objSheet->getColumnDimension("X")->setWidth("15");
                    $objSheet->getColumnDimension("Y")->setAutoSize(false);
                    $objSheet->getColumnDimension("Y")->setWidth("15");
                    $objSheet->getColumnDimension("Z")->setAutoSize(false);
                    $objSheet->getColumnDimension("Z")->setWidth("15");
                    $objSheet->getColumnDimension("AA")->setAutoSize(false);
                    $objSheet->getColumnDimension("AA")->setWidth("15");
                    $objSheet->getColumnDimension("AB")->setAutoSize(false);
                    $objSheet->getColumnDimension("AB")->setWidth("15");
                    $objSheet->getColumnDimension("AC")->setAutoSize(false);
                    $objSheet->getColumnDimension("AC")->setWidth("15");
                    $objSheet->getColumnDimension("AD")->setAutoSize(false);
                    $objSheet->getColumnDimension("AD")->setWidth("15");
                    $objSheet->getColumnDimension("AE")->setAutoSize(false);
                    $objSheet->getColumnDimension("AE")->setWidth("15");
                    $objSheet->getColumnDimension("AF")->setAutoSize(true);
                    $objSheet->getColumnDimension("AG")->setAutoSize(false);
                    $objSheet->getColumnDimension("AG")->setWidth("15");

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "ContainerReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/CostSummaryReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/CostSummaryReports/" . $filename;
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
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function upload_container_template_data()
    {
        try {
            $session = $this->session->userdata('fullname');
            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                if ($_FILES['fileContainerExcel']['size'] > 0) {
                    $config['upload_path'] = FCPATH . 'reports/';
                    $config['allowed_types'] = 'xlsx';
                    $config['remove_spaces'] = TRUE;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('fileContainerExcel')) {
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

                            $createArray = array(
                                'DISPATCH ID', 'REFERENCE SA', 'MES', 'CONTAINER #', 'VOLUMEN', 'CFT',
                                'FRA AGENCIA DE ADUANAS', 'CUSTOMS AGENCY', 'FRA TRANSPORTE', 'TRANSPORTTE / ITR',
                                'FACTURA PUERTO', 'PUERTO', 'PHYTO', 'FACTURA DE FUMIGACION', 'FUMIGACION', 'FACTURA DHL',
                                'DHL', 'COTEROS', 'SELLOS PROVISIONALES', 'INCENTIVO', 'REMOVILIZACION', 'FACTURA NAVIERA',
                                'PUERTO BODEGAJE', 'FLETE', 'VR VENTA UNT', 'TRM', 'PERDIDA / GANANCIA', 'DEX', 'OBSERVACION'
                            );

                            $makeArray = array(
                                'DISPATCHID' => 'DISPATCH ID', 'REFERENCESA' => 'REFERENCE SA', 'MES' => 'MES', 'CONTAINER#' => 'CONTAINER #', 'VOLUMEN' => 'VOLUMEN', 'CFT' => 'CFT',
                                'FRAAGENCIADEADUANAS' => 'FRA AGENCIA DE ADUANAS', 'CUSTOMSAGENCY' => 'CUSTOMS AGENCY', 'FRATRANSPORTE' => 'FRA TRANSPORTE', 'TRANSPORTTE/ITR' => 'TRANSPORTTE / ITR',
                                'FACTURAPUERTO' => 'FACTURA PUERTO', 'PUERTO' => 'PUERTO', 'PHYTO' => 'PHYTO', 'FACTURADEFUMIGACION' => 'FACTURA DE FUMIGACION', 'FUMIGACION' => 'FUMIGACION', 'FACTURADHL' => 'FACTURA DHL',
                                'DHL' => 'DHL', 'COTEROS' => 'COTEROS', 'SELLOSPROVISIONALES' => 'SELLOS PROVISIONALES', 'INCENTIVO' => 'INCENTIVO', 'REMOVILIZACION' => 'REMOVILIZACION', 'FACTURANAVIERA' => 'FACTURA NAVIERA',
                                'PUERTOBODEGAJE' => 'PUERTO BODEGAJE', 'FLETE' => 'FLETE', 'VRVENTAUNT' => 'VR VENTA UNT', 'TRM' => 'TRM', 'PERDIDA/GANANCIA' => 'PERDIDA / GANANCIA', 'DEX' => 'DEX', 'OBSERVACION' => 'OBSERVACION'
                            );

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

                            if (empty($data)) {

                                $dataContainerData = array();

                                for ($i = 2; $i <= $arrayCount; $i++) {

                                    $dispatchId = $SheetDataKey['DISPATCHID'];
                                    $referenceSA = $SheetDataKey['REFERENCESA'];
                                    $containerNumber = $SheetDataKey['CONTAINER#'];
                                    $frCustomAgency = $SheetDataKey['FRAAGENCIADEADUANAS'];
                                    $costCustomAgency = $SheetDataKey['CUSTOMSAGENCY'];
                                    $frTransport = $SheetDataKey['FRATRANSPORTE'];
                                    $costTransport = $SheetDataKey['TRANSPORTTE/ITR'];
                                    $frPort = $SheetDataKey['FACTURAPUERTO'];
                                    $costPort = $SheetDataKey['PUERTO'];
                                    $costPhyto = $SheetDataKey['PHYTO'];
                                    $frFumigation = $SheetDataKey['FACTURADEFUMIGACION'];
                                    $costFumigation = $SheetDataKey['FUMIGACION'];
                                    $frDhl = $SheetDataKey['FACTURADHL'];
                                    $costDhl = $SheetDataKey['DHL'];
                                    $coteros = $SheetDataKey['COTEROS'];
                                    $costProvisional = $SheetDataKey['SELLOSPROVISIONALES'];
                                    $costIncentive = $SheetDataKey['INCENTIVO'];
                                    $costMobilization = $SheetDataKey['REMOVILIZACION'];
                                    $costShipping = $SheetDataKey['FACTURANAVIERA'];
                                    $costWHPort = $SheetDataKey['PUERTOBODEGAJE'];
                                    $costFreight = $SheetDataKey['FLETE'];
                                    $costSales = $SheetDataKey['VRVENTAUNT'];
                                    $costExchange = $SheetDataKey['TRM'];
                                    $costProfitLoss = $SheetDataKey['PERDIDA/GANANCIA'];
                                    $costDex = $SheetDataKey['DEX'];
                                    $costObservation = $SheetDataKey['OBSERVACION'];

                                    $dispatchIdVal = filter_var(trim($allDataInSheet[$i][$dispatchId]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $referenceSAVal = filter_var(trim($allDataInSheet[$i][$referenceSA]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $containerNumberVal = filter_var(trim($allDataInSheet[$i][$containerNumber]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $frCustomAgencyVal = filter_var(trim($allDataInSheet[$i][$frCustomAgency]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costCustomAgencyVal = filter_var(trim($allDataInSheet[$i][$costCustomAgency]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $frTransportVal = filter_var(trim($allDataInSheet[$i][$frTransport]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costTransportVal = filter_var(trim($allDataInSheet[$i][$costTransport]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $frPortVal = filter_var(trim($allDataInSheet[$i][$frPort]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costPortVal = filter_var(trim($allDataInSheet[$i][$costPort]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costPhytoVal = filter_var(trim($allDataInSheet[$i][$costPhyto]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $frFumigationVal = filter_var(trim($allDataInSheet[$i][$frFumigation]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costFumigationVal = filter_var(trim($allDataInSheet[$i][$costFumigation]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $frDhlVal = filter_var(trim($allDataInSheet[$i][$frDhl]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costDhlVal = filter_var(trim($allDataInSheet[$i][$costDhl]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $coterosVal = filter_var(trim($allDataInSheet[$i][$coteros]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costProvisionalVal = filter_var(trim($allDataInSheet[$i][$costProvisional]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costIncentiveVal = filter_var(trim($allDataInSheet[$i][$costIncentive]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costMobilizationVal = filter_var(trim($allDataInSheet[$i][$costMobilization]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costShippingVal = filter_var(trim($allDataInSheet[$i][$costShipping]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costWHPortVal = filter_var(trim($allDataInSheet[$i][$costWHPort]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costFreightVal = filter_var(trim($allDataInSheet[$i][$costFreight]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costSalesVal = filter_var(trim($allDataInSheet[$i][$costSales]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costExchangeVal = filter_var(trim($allDataInSheet[$i][$costExchange]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costProfitLossVal = filter_var(trim($allDataInSheet[$i][$costProfitLoss]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costDexVal = filter_var(trim($allDataInSheet[$i][$costDex]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $costObservationVal = filter_var(trim($allDataInSheet[$i][$costObservation]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    //VALID CONTAINER
                                    $getContainersCount = $this->Financemaster_model->get_count_containers($containerNumberVal, $dispatchIdVal, $referenceSAVal);

                                    if ($getContainersCount[0]->cnt == 1) {

                                        //CHECK EXIST CONTAINER DATA
                                        $checkExistContainerData = $this->Financemaster_model->check_exist_dispatch_cost($containerNumberVal, $dispatchIdVal, $referenceSAVal);

                                        if ($checkExistContainerData[0]->cnt == 1) {
                                            //UPDATE
                                            $dataContainerDataUpdate = array(
                                                "custom_agency_invoice_number" => $frCustomAgencyVal, "custom_agency_cost" => $costCustomAgencyVal, "transport_invoice_number" => $frTransportVal,
                                                "transport_cost" => $costTransportVal, "port_invoice_number" => $frPortVal, "port_cost" => $costPortVal, "phyto_cost" => $costPhytoVal,
                                                "fumigation_invoice_number" => $frFumigationVal, "fumigation_cost" => $costFumigationVal, "dhl_invoice_number" => $frDhlVal, "dhl_cost" => $costDhlVal,
                                                "coteros_cost" => $coterosVal, "provistional_stamp_cost" => $costProvisionalVal, "incentive" => $costIncentiveVal, "mobilization_cost" => $costMobilizationVal,
                                                "shipping_invoice_number" => $costShippingVal, "warehouse_port_cost" => $costWHPortVal, "freight_cost" => $costFreightVal, "sales_cost" => $costSalesVal,
                                                "exchange_rate" => $costExchangeVal, "entry_weight_cost" => 0, "loss_profit" => $costProfitLossVal, "document_number" => $costDexVal,
                                                "observation" => $costObservationVal, "updated_by" => $session['user_id'], "updated_date" => date('Y-m-d H:i:s')
                                            );

                                            $updateData = $this->Financemaster_model->update_dispatch_cost_details($dispatchIdVal, $containerNumberVal, $referenceSAVal, $dataContainerDataUpdate);
                                        } else {
                                            //ADD
                                            $dataContainerData[] = array(
                                                "dispatch_id" => $dispatchIdVal, "container_number" => $containerNumberVal, "sa_number" => $referenceSAVal,
                                                "custom_agency_invoice_number" => $frCustomAgencyVal, "custom_agency_cost" => $costCustomAgencyVal, "transport_invoice_number" => $frTransportVal,
                                                "transport_cost" => $costTransportVal, "port_invoice_number" => $frPortVal, "port_cost" => $costPortVal, "phyto_cost" => $costPhytoVal,
                                                "fumigation_invoice_number" => $frFumigationVal, "fumigation_cost" => $costFumigationVal, "dhl_invoice_number" => $frDhlVal, "dhl_cost" => $costDhlVal,
                                                "coteros_cost" => $coterosVal, "provistional_stamp_cost" => $costProvisionalVal, "incentive" => $costIncentiveVal, "mobilization_cost" => $costMobilizationVal,
                                                "shipping_invoice_number" => $costShippingVal, "warehouse_port_cost" => $costWHPortVal, "freight_cost" => $costFreightVal, "sales_cost" => $costSalesVal,
                                                "exchange_rate" => $costExchangeVal, "entry_weight_cost" => 0, "loss_profit" => $costProfitLossVal, "document_number" => $costDexVal,
                                                "observation" => $costObservationVal, "created_by" => $session['user_id'],
                                                "updated_by" => $session['user_id'], "is_active" => 1,
                                                "created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
                                            );
                                        }
                                    } else {
                                        //DO NOTHING
                                    }
                                }

                                if (count($dataContainerData) > 0) {
                                    $insertData = $this->Financemaster_model->add_dispatch_cost_details($dataContainerData);
                                }

                                $Return['error'] = "";
                                $Return['result'] = "Yes";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
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

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . 'reports/*.xlsx');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/CostSummaryReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function reformatDate($date, $from_format = 'd/m/Y', $to_format = 'Y-m-d')
    {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }
}
