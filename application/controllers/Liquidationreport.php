<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Liquidationreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Financemaster_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
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
        $data["title"] = $this->lang->line("liquidationreport_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_financereport";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financereports/liquidationreport", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function liquidation_contract_lists()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            if ($this->input->get("type") == 1) {
                $liquidationContracts = $this->Financemaster_model->get_contracts_liquidation_warehouse($this->input->get("originid"));
            } else if ($this->input->get("type") == 2) {
                $liquidationContracts = $this->Financemaster_model->get_contracts_liquidation_fieldpurchase($this->input->get("originid"));
            } else {
                $liquidationContracts = array();
            }


            $data = array();

            foreach ($liquidationContracts as $r) {

                if ($this->input->get("type") == 1) {
                    $actionLiquidation = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("download_liquidation_report") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloadliquidation_warehouse" data-toggle="modal" data-target=".download-modal-data" data-contract_id="' . $r->contract_id . '" data-supplier_id="' . $r->sid . '" data-origin_id="' . $r->origin_id . '" data-contract_code="' . $r->contract_code . '"><span class="fas fa-sack-dollar"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("update_invoice") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="updateinvoice_warehouse" data-toggle="modal" data-target=".view-modal-data" data-contract_id="' . $r->contract_id . '" data-supplier_id="' . $r->sid . '" data-origin_id="' . $r->origin_id . '" data-contract_code="' . $r->contract_code . '"><span class="fas fa-pencil"></span></button></span>';
                } else {
                    $actionLiquidation = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("download_liquidation_report") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloadliquidation_fieldpurchase" data-toggle="modal" data-target=".download-modal-data" data-contract_id="' . $r->contract_id . '" data-supplier_id="' . $r->sid . '" data-origin_id="' . $r->origin_id . '" data-contract_code="' . $r->contract_code . '"><span class="fas fa-sack-dollar"></span></button></span>
                            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("update_invoice") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="updateinvoice_fieldpurchase" data-toggle="modal" data-target=".view-modal-data" data-contract_id="' . $r->contract_id . '" data-supplier_id="' . $r->sid . '" data-origin_id="' . $r->origin_id . '" data-contract_code="' . $r->contract_code . '"><span class="fas fa-pencil"></span></button></span>';
                }
                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $actionLiquidation,
                    $r->contract_code,
                    $r->fullname,
                    $product,
                    $r->total_volume + 0,
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

    public function fetch_inventory()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $contractId = $this->input->post("contract_id");
                $contractCode = $this->input->post("contract_code");
                $supplierId = $this->input->post("supplier_id");
                $originId = $this->input->post("origin_id");
                $typeId = $this->input->post("type_id");

                if ($contractId > 0 && $supplierId > 0 && $originId > 0 && ($contractCode != "" && $contractCode != null)) {

                    if ($typeId == 1) {
                        $getInventory = $this->Financemaster_model->get_inventory_by_contract($contractId, $contractCode, $originId, $supplierId);
                    } else {
                        $getInventory = $this->Financemaster_model->get_inventory_by_contract_fieldpurchase($contractId, $contractCode, $originId, $supplierId);
                    }
                    if (count($getInventory) > 0) {

                        $data = array(
                            "pageheading" => $this->lang->line("update_invoice"),
                            "pagetype" => "viewupdateinvoice",
                            "csrf_hash" => $this->security->get_csrf_hash(),
                            "inventoryData" => $getInventory,
                            "contractCode" => $contractCode,
                            "supplierId" => $supplierId,
                            "contractId" => $contractId,
                            "originId" => $originId,
                        );
                        if ($typeId == 1) {
                            $this->load->view("financereports/dialog_updateinvoice", $data);
                        } else {
                            $this->load->view("financereports/dialog_updateinvoicefieldpurchase", $data);
                        }
                    } else {
                        $Return["error"] = $this->lang->line("no_data_available");
                        $Return["result"] = "";
                        $Return["redirect"] = false;
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function update_inventory_invoice_number()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $contractId = $this->input->post("contractId");
                $supplierId = $this->input->post("supplierId");
                $updateInvoiceNumberData = $this->input->post("updateInvoiceNumberData");

                $updateInvoiceNumberDataJson = json_decode($updateInvoiceNumberData, true);
                if (count($updateInvoiceNumberDataJson) > 0) {

                    foreach ($updateInvoiceNumberDataJson as $invoicedata) {
                        $dataInvoice = array(
                            "invoice_number" => $invoicedata["updatedinvoicenumber"],
                            "updated_by" => $session['user_id'],
                        );

                        $updateInvoice = $this->Financemaster_model->update_invoice_number(
                            $invoicedata["mappingid"],
                            $contractId,
                            $invoicedata["inventoryorder"],
                            $supplierId,
                            $dataInvoice
                        );
                    }
                }

                $Return["error"] = "";
                $Return["successmessage"] = $this->lang->line("data_updated");
                $Return["redirect"] = false;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function update_inventory_invoice_number_field_purchase()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $contractId = $this->input->post("contractId");
                $supplierId = $this->input->post("supplierId");
                $updateInvoiceNumberData = $this->input->post("updateInvoiceNumberData");

                $updateInvoiceNumberDataJson = json_decode($updateInvoiceNumberData, true);
                if (count($updateInvoiceNumberDataJson) > 0) {

                    foreach ($updateInvoiceNumberDataJson as $invoicedata) {
                        $dataInvoice = array(
                            "invoice_number" => $invoicedata["updatedinvoicenumber"],
                            "updated_by" => $session['user_id'],
                        );

                        $updateInvoice = $this->Financemaster_model->update_invoice_number_field_purchase(
                            $invoicedata["mappingid"],
                            $contractId,
                            $invoicedata["inventoryorder"],
                            $dataInvoice
                        );
                    }
                }

                $Return["error"] = "";
                $Return["successmessage"] = $this->lang->line("data_updated");
                $Return["redirect"] = false;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_generate_liquidation()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->post("type") == "generate_liquidation") {
                $data = array(
                    "pageheading" => $this->lang->line("generate_liquidation_report"),
                    "pagetype" => "generate_liquidation",
                    "contractId" => $this->input->post("contractId"),
                    "contractCode" => $this->input->post("contractCode"),
                    "originId" => $this->input->post("originId"),
                    "csrf_hash" => $this->security->get_csrf_hash(),
                    "suppliers" => $this->Financemaster_model->get_suppliers_by_contract($this->input->post("contractId")),
                );
            }
            $this->load->view('financereports/dialog_select_supplier', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_warehouse_liquidation_report()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $contractId = $this->input->post("contractId");
                $contractCode = $this->input->post("contractCode");
                $originId = $this->input->post("originId");
                $supplierId = $this->input->post("supplierId");

                $getInventoryLiquidationReport = $this->Financemaster_model->fetch_inventory_report_warehouse($contractId, $contractCode, $supplierId, $originId);

                if (count($getInventoryLiquidationReport) > 0) {

                    $getCurrency = $this->Financemaster_model->get_currency_code($originId);

                    //ADVANCE CONTROL SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("advance_control"));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->SetCellValue("B2", $this->lang->line("account_status"));
                    $objSheet->getStyle("B2:C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("000000");
                    $objSheet->getStyle('B2:C2')->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                    $objSheet->SetCellValue("A5", $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue("B5", $this->lang->line("concept"));
                    $objSheet->SetCellValue("C5", $this->lang->line("value"));
                    $objSheet->SetCellValue("D5", $this->lang->line("pay_to"));

                    $objSheet->getStyle("A5:D5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objSheet->getStyle("A5:D5")->getFont()->setBold(true)->getColor()->setRGB("000000");
                    $objSheet->getStyle("A5:D5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("B2:C2")->applyFromArray($styleArray);
                    $objSheet->getStyle("A5:D5")->applyFromArray($styleArray);

                    $wz_lang = $this->session->userdata("site_lang");
                    $lang_code = $this->Settings_model->get_language_info($wz_lang);
                    $getInventoryLedger = $this->Financemaster_model->get_inventory_ledger_contract($contractId, $supplierId, $lang_code[0]->language_format_code);

                    $rowLedger = 6;
                    if (count($getInventoryLedger) > 0) {

                        foreach ($getInventoryLedger as $inventoryledger) {

                            $objSheet->SetCellValue("A$rowLedger", $inventoryledger->expense_date);
                            $objSheet->SetCellValue("B$rowLedger", $this->lang->line($inventoryledger->type_name) . " / " . $inventoryledger->inventory_order);
                            $objSheet->SetCellValue("C$rowLedger", $inventoryledger->amount);
                            $objSheet->SetCellValue("D$rowLedger", $inventoryledger->supplier_name);

                            $rowLedger++;
                        }

                        $lastRowLedger = $rowLedger - 1;
                        $objSheet->getStyle("C6:C$lastRowLedger")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);

                        $objSheet->getStyle("A6:D$lastRowLedger")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("C2", "=SUM(C6:C$lastRowLedger)");
                        $objSheet->getStyle("C2")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);



                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                    }

                    //END ADVANCE CONTROL SHEET

                    //SUMMARY SHEET

                    $objWorkSummarySheet = $this->excel->createSheet(1);
                    $objWorkSummarySheet->setTitle($this->lang->line("report_summary"));

                    $objWorkSummarySheet->SetCellValue("C1", $this->lang->line("transit_loss"));
                    $objWorkSummarySheet->getStyle("C1:D1")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("C1:D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("AEAAAA");
                    $objWorkSummarySheet->getStyle("C1:D1")->applyFromArray($styleArray);

                    $objWorkSummarySheet->SetCellValue("A6", $this->lang->line("serial_no"));
                    $objWorkSummarySheet->mergeCells("A6:A7");

                    $objWorkSummarySheet->SetCellValue("B6", $this->lang->line("costsummary_date"));
                    $objWorkSummarySheet->mergeCells("B6:B7");

                    $objWorkSummarySheet->SetCellValue("C6", $this->lang->line("inventory_order"));
                    $objWorkSummarySheet->mergeCells("C6:C7");

                    $objWorkSummarySheet->SetCellValue("D6", $this->lang->line("pieces"));
                    $objWorkSummarySheet->mergeCells("D6:E6");

                    $objWorkSummarySheet->SetCellValue("D7", $this->lang->line("reception_title"));
                    $objWorkSummarySheet->SetCellValue("E7", $this->lang->line("farm_title"));

                    $objWorkSummarySheet->SetCellValue("F6", $this->lang->line("length"));
                    $objWorkSummarySheet->mergeCells("F6:F7");

                    $objWorkSummarySheet->SetCellValue("G6", $this->lang->line("circumference") . " / " . $this->lang->line("square_foot"));
                    $objWorkSummarySheet->mergeCells("G6:G7");

                    $objWorkSummarySheet->SetCellValue("H6", $this->lang->line("volume_gross_hoppus"));
                    $objWorkSummarySheet->mergeCells("H6:H7");

                    $objWorkSummarySheet->SetCellValue("I6", $this->lang->line("volume_net_hoppus"));
                    $objWorkSummarySheet->mergeCells("I6:I7");

                    $objWorkSummarySheet->SetCellValue("J6", $this->lang->line("volume_gross_area"));
                    $objWorkSummarySheet->mergeCells("J6:J7");

                    $objWorkSummarySheet->SetCellValue("K6", $this->lang->line("volume_farm"));
                    $objWorkSummarySheet->mergeCells("K6:K7");

                    $objWorkSummarySheet->SetCellValue("L6", $this->lang->line("value_wood_reception"));
                    $objWorkSummarySheet->mergeCells("L6:L7");

                    $objWorkSummarySheet->SetCellValue("M6", $this->lang->line("value_wood_farm"));
                    $objWorkSummarySheet->mergeCells("M6:M7");

                    $objWorkSummarySheet->SetCellValue("N6", $this->lang->line("Logistics"));
                    $objWorkSummarySheet->mergeCells("N6:N7");

                    $objWorkSummarySheet->SetCellValue("O6", $this->lang->line("Service"));
                    $objWorkSummarySheet->mergeCells("O6:O7");

                    $objWorkSummarySheet->SetCellValue("P6", $this->lang->line("total"));
                    $objWorkSummarySheet->mergeCells("P6:P7");

                    $objWorkSummarySheet->SetCellValue("Q6", $this->lang->line("difference_farm_reception"));
                    $objWorkSummarySheet->mergeCells("Q6:Q7");

                    $objWorkSummarySheet->SetCellValue("R6", $this->lang->line("value_material"));
                    $objWorkSummarySheet->mergeCells("R6:R7");

                    $objWorkSummarySheet->SetCellValue("S6", $this->lang->line("invoice_number"));
                    $objWorkSummarySheet->mergeCells("S6:S7");

                    $objWorkSummarySheet->getStyle("A6:S7")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("A6:S6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("D7:E7")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("A6:S7")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("F6:Q6")->getAlignment()->setWrapText(true);

                    //END SUMMARY SHEET

                    //INVENTORY SHEET
                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($originId);

                    $sheetNo = 2;
                    $summarySNo = 1;
                    $summarySheetFirstRowData = 8;
                    $summarySheetRowDataCount = 8;

                    foreach ($getInventoryLiquidationReport as $sheetinventory) {
                        $objWorkInventorySheet = $this->excel->createSheet($sheetNo);
                        $objWorkInventorySheet->setTitle(strtoupper($sheetinventory->inventory_order));

                        if ($sheetinventory->product == 2 || $sheetinventory->product == 1 || $sheetinventory->product == 4 && ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3)) {

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail($contractId, $supplierId, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("Q$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("Q$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("Q$startPaymentRow:R$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("R$totalPaymentRow", "=SUM(R$totalPaymentRow1:R$calcRow)");

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("width"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("thickness"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("square_foot"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("length_mtr"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("width_cms"));
                            $objWorkInventorySheet->SetCellValue("J$headerRow", $this->lang->line("thickness_cms"));
                            $objWorkInventorySheet->SetCellValue("K$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("L$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("value"));
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data_square($supplierId, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;
                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;

                                $txtFeet = '"ft"';
                                $txtMeter = '"m"';
                                $txtInch = '"in"';
                                $txtCm = '"cm"';
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->length_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->width_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("E$rowCount", ($farmData->thickness_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC((C$rowCount*D$rowCount*E$rowCount/12)*B$rowCount,0),0)");
                                    $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(D$rowCount*E$rowCount, 0)");
                                    $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(CONVERT(C$rowCount,$txtFeet,$txtMeter),2),0)");
                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(CONVERT(D$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("J$rowCount", "=IFERROR(TRUNC(CONVERT(E$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("K$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("L$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*B$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("M$rowCount", "=IFERROR(ROUND(L$rowCount * Q$farmDataFirstRow,2),0)");
                                    $objWorkInventorySheet->getStyle("M$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    $rowCount++;
                                }
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(E$farmDataFirstRow:E$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(H$farmDataFirstRow:H$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(I$farmDataFirstRow:I$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("J$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(J$farmDataFirstRow:J$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("K$sumCalcRow", "=SUM(K$farmDataFirstRow:K$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("L$sumCalcRow", "=SUM(L$farmDataFirstRow:L$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("M$sumCalcRow", "=SUM(M$farmDataFirstRow:M$lastRowFarmData)");
                            $objWorkInventorySheet->getStyle("M$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            $objWorkInventorySheet->getStyle("A$sumCalcRow:M$lastRowFarmData")->applyFromArray($styleArray);

                            $cftText = "&" . '"' . " x " . '"' . "&";

                            $objWorkInventorySheet->SetCellValue("H4", "=K$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H6", "=E$sumCalcRow" . $cftText . "D$sumCalcRow");

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->mergeCells("O$headerRow:P$headerRow");
                            $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("volume_per_piece"));

                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->applyFromArray($styleArray);

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($contractId, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->exchange_rate > 0) {
                                        $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    } else {
                                        $priceRange = $pricedata->pricerange_grade3;
                                    }

                                    $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", ($priceRange + 0));
                                    $objWorkInventorySheet->getStyle("Q$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                $objWorkInventorySheet->getStyle("O$priceFirstRow:Q$priceLastRow")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                            $objWorkInventorySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(22);

                            $objWorkInventorySheet->SetCellValue("R$sumCalcRow", "=M$sumCalcRow");
                            $objWorkInventorySheet->getStyle("R$totalPaymentRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $objWorkInventorySheet->getStyle("R$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            // $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            // $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            // $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            // $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            // $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");


                            // $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!I$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            // $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            // $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(K$summarySheetRowDataCount:M$summarySheetRowDataCount)");
                            // $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=J$summarySheetRowDataCount-K$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=N$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "0");


                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!L$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(M$summarySheetRowDataCount:O$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=L$summarySheetRowDataCount-M$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=P$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;
                        } else if ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3) {
                        } else {

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail($contractId, $supplierId, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G2", $this->lang->line("gross_volume"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));
                            $objWorkInventorySheet->SetCellValue("J2", $this->lang->line("circumference_allowance"));
                            $objWorkInventorySheet->SetCellValue("J3", $this->lang->line("length_allowance"));

                            if ($getFarmDetail[0]->exchange_rate > 0) {
                                $objWorkInventorySheet->SetCellValue("J6", $this->lang->line("exchange_rate"));
                                $objWorkInventorySheet->SetCellValue("K6", $getFarmDetail[0]->exchange_rate + 0);
                                $objWorkInventorySheet->getStyle("K6")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                $objWorkInventorySheet->getStyle("J6")->getFont()->setBold(true);
                                $objWorkInventorySheet->getStyle("J6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objWorkInventorySheet->getStyle("K6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                                $objWorkInventorySheet->getStyle("J6:K6")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J3")->getFont()->setBold(true);
                            

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("K2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("K3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            
                            $objWorkInventorySheet->SetCellValue("K2", $getFarmDetail[0]->purchase_allowance);
                            $objWorkInventorySheet->SetCellValue("K3", $getFarmDetail[0]->purchase_allowance_length);

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G2:H2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("J2:K3")->applyFromArray($styleArray);
                           

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "Q$rowCount";

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("P$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("P$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "Q$rowCount";

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("P$startPaymentRow:Q$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("Q$totalPaymentRow", "=SUM(Q$totalPaymentRow1:Q$calcRow)");
                            $objWorkInventorySheet->getStyle("Q$totalPaymentRow:Q$calcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("circumference"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("vol_gross") . " - " . $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("vol_net") . " - " . $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("vol_gross") . " - " . $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("vol_net") . " - " . $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("product_type"));
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data($supplierId, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;

                            $textShorts = '"' . $this->lang->line("text_shorts") . '"';
                            $textSemi = '"' . $this->lang->line("text_semi") . '"';
                            $textLongs = '"' . $this->lang->line("text_longs") . '"';

                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->circumference_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->length_bought + 0));


                                    $circumferenceAllowance = $getFarmDetail[0]->purchase_allowance;
                                    $lengthAllowance = $getFarmDetail[0]->purchase_allowance_length;

                                    if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 6) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 5 || $getFarmDetail[0]->purchase_unit_id == 7 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 8) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 9) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        }
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                    }

                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IF(D$rowCount<330, $textShorts ,IF(D$rowCount>=600, $textLongs, $textSemi))");

                                    $objWorkInventorySheet->getStyle("E$rowCount:H$rowCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                                    $rowCount++;
                                }
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=IFERROR(TRUNC(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0), 0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=IFERROR(TRUNC(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0)/100 , 0)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=SUM(E$farmDataFirstRow:E$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("G$sumCalcRow", "=SUM(G$farmDataFirstRow:G$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=SUM(H$farmDataFirstRow:H$lastRowFarmData)");

                            $objWorkInventorySheet->getStyle("E$sumCalcRow:H$sumCalcRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                            $objWorkInventorySheet->SetCellValue("H2", "=E$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H4", "=F$sumCalcRow");
                            if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/B$sumCalcRow*35.315,2),0)");
                            } else {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");
                            }

                            if ($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                $objWorkInventorySheet->SetCellValue("J4", $this->lang->line("average_girth"));
                                $objWorkInventorySheet->SetCellValue("K4", "=TRUNC(SUMPRODUCT(B$farmDataFirstRow:B$lastRowFarmData,C$farmDataFirstRow:C$lastRowFarmData)/B$sumCalcRow,0)");

                                $objWorkInventorySheet->getStyle("K4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                                $objWorkInventorySheet->getStyle("J4:K4")->getFont()->setBold(true)->getColor()->setRGB("000000");
                                $objWorkInventorySheet->getStyle("J4:K4")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getStyle("A$sumCalcRow:I$lastRowFarmData")->applyFromArray($styleArray);

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("K$headerRow", $this->lang->line("circumference_range"));
                            $objWorkInventorySheet->mergeCells("K$headerRow:L$headerRow");

                            // if (
                            //     $getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5
                            //     || $getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7
                            // ) {
                                //$objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("volume_reception"));
                                //$objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_farm"));
                                $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("text_shorts"));
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("text_semi"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("text_longs"));

                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("reception_total_value"));
                                $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("farm_total_value"));

                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFont()->setBold(true);
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->applyFromArray($styleArray);
                            // } else {
                            //     $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("pieces_reception"));
                            //     $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("pieces_farm"));
                            //     $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_per_piece"));

                            //     $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("reception_total_value"));
                            //     $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("farm_total_value"));

                            //     if($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getFont()->setBold(true);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->applyFromArray($styleArray);
                            //     } else {
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFont()->setBold(true);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->applyFromArray($styleArray);
                            //     }
                            // }

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($contractId, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("K$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("L$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    }
                                    

                                    // if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$I$' . "$farmDataFirstRow" . ':$I$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    // } else if ($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(" . '$K$4' . ">=L$priceSummaryRow,IF(" . '$K$4' . "<=M$priceSummaryRow," . '$H$' . "$sumCalcRow,0),0)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(" . '$K$4' . ">=L$priceSummaryRow,IF(" . '$K$4' . "<=M$priceSummaryRow," . '$I$' . "$sumCalcRow,0),0)");
                                    // } else if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(" . '$H$6' . ">=L$priceSummaryRow,IF(" . '$H$6' . "<=M$priceSummaryRow," . '$H$' . "$sumCalcRow,0),0)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(" . '$H$6' . ">=L$priceSummaryRow,IF(" . '$H$6' . "<=M$priceSummaryRow," . '$I$' . "$sumCalcRow,0),0)");
                                    // } else {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    // }

                                    // if ($getFarmDetail[0]->exchange_rate > 0) {
                                    //     $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    // } else {
                                    //     $priceRange = $pricedata->pricerange_grade3;
                                    // }

                                    $priceRangeShorts = $pricedata->pricerange_grade3;
                                    $priceRangeSemi = $pricedata->pricerange_grade_semi;
                                    $priceRangeLongs = $pricedata->pricerange_grade_longs;

                                    // if ($getFarmDetail[0]->purchase_unit_id == 15) {
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeShorts/N$priceSummaryRow");
                                    //     // $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=$priceRangeSemi/O$priceSummaryRow");
                                    //     // $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeLongs/O$priceSummaryRow");
                                    // } else if($getFarmDetail[0]->purchase_unit_id == 3) {
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeShorts");
                                    // } else {
                                        $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", ($priceRangeShorts + 0));
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", ($priceRangeSemi + 0));
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", ($priceRangeLongs + 0));
                                    //}

                                    // if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                    //     $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*O$priceSummaryRow)");
                                    //     $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(N$priceSummaryRow*O$priceSummaryRow)");
                                        
                                    //     $objWorkInventorySheet->getStyle("O$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    //     // $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,R10)");
                                    //     // $objWorkInventorySheet->SetCellValue("S$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,S10)");
                                    //     // $objWorkInventorySheet->SetCellValue("T$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,T10)");

                                    //     // $objWorkInventorySheet->SetCellValue("U$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,U10)");
                                    //     // $objWorkInventorySheet->SetCellValue("V$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,V10)");
                                    //     // $objWorkInventorySheet->SetCellValue("W$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,W10)");

                                    //     // $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*R$priceSummaryRow)+(N$priceSummaryRow*S$priceSummaryRow)+(O$priceSummaryRow*T$priceSummaryRow)");
                                    //     // $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(M$priceSummaryRow*U$priceSummaryRow)+(N$priceSummaryRow*V$priceSummaryRow)+(O$priceSummaryRow*W$priceSummaryRow)");

                                    //     // $objWorkInventorySheet->getStyle("M$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    // } else {

                                    if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {

                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,R10)");
                                        $objWorkInventorySheet->SetCellValue("S$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,S10)");
                                        $objWorkInventorySheet->SetCellValue("T$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,T10)");

                                        $objWorkInventorySheet->SetCellValue("U$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,U10)");
                                        $objWorkInventorySheet->SetCellValue("V$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,V10)");
                                        $objWorkInventorySheet->SetCellValue("W$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,W10)");

                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*R$priceSummaryRow)+(N$priceSummaryRow*S$priceSummaryRow)+(O$priceSummaryRow*T$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(M$priceSummaryRow*U$priceSummaryRow)+(N$priceSummaryRow*V$priceSummaryRow)+(O$priceSummaryRow*W$priceSummaryRow)");

                                        $objWorkInventorySheet->getStyle("M$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                    } else {
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,R10)");
                                        $objWorkInventorySheet->SetCellValue("S$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,S10)");
                                        $objWorkInventorySheet->SetCellValue("T$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,T10)");

                                        $objWorkInventorySheet->SetCellValue("U$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,U10)");
                                        $objWorkInventorySheet->SetCellValue("V$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,V10)");
                                        $objWorkInventorySheet->SetCellValue("W$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,W10)");

                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*R$priceSummaryRow)+(N$priceSummaryRow*S$priceSummaryRow)+(O$priceSummaryRow*T$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(M$priceSummaryRow*U$priceSummaryRow)+(N$priceSummaryRow*V$priceSummaryRow)+(O$priceSummaryRow*W$priceSummaryRow)");

                                        $objWorkInventorySheet->getStyle("M$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    }
                                    //}



                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                if ($getFarmDetail[0]->exchange_rate > 0) {
                                    $objWorkInventorySheet->SetCellValue("P$sumCalcRow", "=SUM(P$priceFirstRow:P$priceLastRow)*K6");
                                    $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)*K6");
                                } else {
                                    $objWorkInventorySheet->SetCellValue("P$sumCalcRow", "=SUM(P$priceFirstRow:P$priceLastRow)");
                                    $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)");
                                }

                                $objWorkInventorySheet->getStyle("P$sumCalcRow:Q$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                // if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                //     //$objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('0');
                                //     $objWorkInventorySheet->getStyle("K$priceFirstRow:Q$priceLastRow")->applyFromArray($styleArray);
                                // } else {

                                if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                    $objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('0');
                                } else {
                                    $objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                }
                                    $objWorkInventorySheet->getStyle("K$priceFirstRow:W$priceLastRow")->applyFromArray($styleArray);
                                    
                                //}

                                // if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                //     $objWorkInventorySheet->SetCellValue("M$sumCalcRow", "=SUM(M$priceFirstRow:M$priceLastRow)");
                                //     $objWorkInventorySheet->SetCellValue("N$sumCalcRow", "=SUM(N$priceFirstRow:N$priceLastRow)");

                                //     $objWorkInventorySheet->getStyle("M$sumCalcRow:N$sumCalcRow")->applyFromArray($styleArray);
                                // } else {

                                    $objWorkInventorySheet->SetCellValue("R$sumCalcRow", $this->lang->line("reception_title"));
                                    $objWorkInventorySheet->mergeCells("R$sumCalcRow:T$sumCalcRow");

                                    $objWorkInventorySheet->SetCellValue("U$sumCalcRow", $this->lang->line("farm_title"));
                                    $objWorkInventorySheet->mergeCells("U$sumCalcRow:W$sumCalcRow");

                                    $objWorkInventorySheet->SetCellValue("R$headerRow", $this->lang->line("text_shorts"));
                                    $objWorkInventorySheet->SetCellValue("S$headerRow", $this->lang->line("text_semi"));
                                    $objWorkInventorySheet->SetCellValue("T$headerRow", $this->lang->line("text_longs"));

                                    $objWorkInventorySheet->SetCellValue("U$headerRow", $this->lang->line("text_shorts"));
                                    $objWorkInventorySheet->SetCellValue("V$headerRow", $this->lang->line("text_semi"));
                                    $objWorkInventorySheet->SetCellValue("W$headerRow", $this->lang->line("text_longs"));

                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getFont()->setBold(true);
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->applyFromArray($styleArray);
                                //}

                                
                            }

                            $objWorkInventorySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(13);
                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(17);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(17);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");


                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!H$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!P$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!Q$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(M$summarySheetRowDataCount:O$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=L$summarySheetRowDataCount-M$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=P$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);

                            $objWorkSummarySheet->getStyle("H$summarySheetRowDataCount:K$summarySheetRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;
                        }
                    }

                    $summarySheetLastRowData = $summarySheetRowDataCount - 1;

                    $objWorkSummarySheet->SetCellValue("D5", "=SUM(D$summarySheetFirstRowData:D$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("E5", "=SUM(E$summarySheetFirstRowData:E$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("H5", "=SUM(H$summarySheetFirstRowData:H$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("I5", "=SUM(I$summarySheetFirstRowData:I$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("J5", "=SUM(J$summarySheetFirstRowData:J$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("K5", "=SUM(K$summarySheetFirstRowData:K$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("D5:K5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("D5:K5")->getFont()->setBold(true);

                    $objWorkSummarySheet->SetCellValue("L5", "=SUM(L$summarySheetFirstRowData:L$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("M5", "=SUM(M$summarySheetFirstRowData:M$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("N5", "=SUM(N$summarySheetFirstRowData:N$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("O5", "=SUM(O$summarySheetFirstRowData:O$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("P5", "=SUM(P$summarySheetFirstRowData:P$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("Q5", "=SUM(Q$summarySheetFirstRowData:Q$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("R5", "=SUM(R$summarySheetFirstRowData:R$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("L5:R5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("L5:R5")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("L5:R5")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->SetCellValue("L4", "=IFERROR(L5/D5,0)");
                    $objWorkSummarySheet->SetCellValue("M4", "=IFERROR(M5/E5,0)");
                    $objWorkSummarySheet->SetCellValue("N4", "=IFERROR(N5/E5,0)");
                    $objWorkSummarySheet->SetCellValue("O4", "=IFERROR(O5/E5,0)");
                    $objWorkSummarySheet->SetCellValue("P4", "=IFERROR(P5/E5,0)");
                    $objWorkSummarySheet->SetCellValue("Q4", "=IFERROR(Q5/E5,0)");
                    $objWorkSummarySheet->SetCellValue("R4", "=IFERROR(R5/E5,0)");
                    $objWorkSummarySheet->getStyle("L4:R4")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("L4:R4")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("L4:R4")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->getStyle("L$summarySheetFirstRowData:R$summarySheetLastRowData")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                    $objWorkSummarySheet->getStyle("A$summarySheetFirstRowData:S$summarySheetLastRowData")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("A$summarySheetRowDataCount:S$summarySheetLastRowData")->applyFromArray($styleArray);

                    $objWorkSummarySheet->getStyle("L5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("ED7D31");
                    $objWorkSummarySheet->getStyle("M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("7B7B7B");
                    $objWorkSummarySheet->getStyle("L5:M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("548235");
                    $objWorkSummarySheet->getStyle("O5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objWorkSummarySheet->getStyle("L4:R4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("9BC2E6");

                    $objWorkSummarySheet->SetCellValue("D1", "=D5-E5");
                    $objWorkSummarySheet->getStyle("D1")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objWorkSummarySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(9);
                    $objWorkSummarySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(16);
                    $objWorkSummarySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14);
                    $objWorkSummarySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("S")->setAutoSize(false)->setWidth(14);

                    //END INVENTORY SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "LiquidationReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/LiquidationReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/LiquidationReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line("no_data_reports");
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_fieldpurchase_liquidation_report()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $contractId = $this->input->post("contractId");
                $contractCode = $this->input->post("contractCode");
                $originId = $this->input->post("originId");
                $supplierId = $this->input->post("supplierId");

                $getInventoryLiquidationReport = $this->Financemaster_model->fetch_inventory_report_warehouse($contractId, $contractCode, $supplierId, $originId);

                if (count($getInventoryLiquidationReport) > 0) {

                    $getCurrency = $this->Financemaster_model->get_currency_code($originId);

                    //ADVANCE CONTROL SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("advance_control"));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->SetCellValue("B2", $this->lang->line("account_status"));
                    $objSheet->getStyle("B2:C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("000000");
                    $objSheet->getStyle('B2:C2')->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                    $objSheet->SetCellValue("A5", $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue("B5", $this->lang->line("concept"));
                    $objSheet->SetCellValue("C5", $this->lang->line("value"));
                    $objSheet->SetCellValue("D5", $this->lang->line("pay_to"));

                    $objSheet->getStyle("A5:D5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objSheet->getStyle("A5:D5")->getFont()->setBold(true)->getColor()->setRGB("000000");
                    $objSheet->getStyle("A5:D5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("B2:C2")->applyFromArray($styleArray);
                    $objSheet->getStyle("A5:D5")->applyFromArray($styleArray);

                    $wz_lang = $this->session->userdata("site_lang");
                    $lang_code = $this->Settings_model->get_language_info($wz_lang);
                    $getInventoryLedger = $this->Financemaster_model->get_inventory_ledger_contract_purchase_manager($contractId, $supplierId, $lang_code[0]->language_format_code);

                    $rowLedger = 6;
                    if (count($getInventoryLedger) > 0) {

                        foreach ($getInventoryLedger as $inventoryledger) {

                            $objSheet->SetCellValue("A$rowLedger", $inventoryledger->expense_date);
                            $objSheet->SetCellValue("B$rowLedger", $this->lang->line($inventoryledger->type_name) . " / " . $inventoryledger->inventory_order);
                            $objSheet->SetCellValue("C$rowLedger", $inventoryledger->amount);
                            $objSheet->SetCellValue("D$rowLedger", $inventoryledger->supplier_name);

                            $rowLedger++;
                        }

                        $lastRowLedger = $rowLedger - 1;
                        $objSheet->getStyle("C6:C$lastRowLedger")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);

                        $objSheet->getStyle("A6:D$lastRowLedger")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("C2", "=SUM(C6:C$lastRowLedger)");
                        $objSheet->getStyle("C2")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);



                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                    }

                    //END ADVANCE CONTROL SHEET

                    //SUMMARY SHEET

                    $objWorkSummarySheet = $this->excel->createSheet(1);
                    $objWorkSummarySheet->setTitle($this->lang->line("report_summary"));

                    $objWorkSummarySheet->SetCellValue("C1", $this->lang->line("transit_loss"));
                    $objWorkSummarySheet->getStyle("C1:D1")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("C1:D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("AEAAAA");
                    $objWorkSummarySheet->getStyle("C1:D1")->applyFromArray($styleArray);

                    $objWorkSummarySheet->SetCellValue("A6", $this->lang->line("serial_no"));
                    $objWorkSummarySheet->mergeCells("A6:A7");

                    $objWorkSummarySheet->SetCellValue("B6", $this->lang->line("costsummary_date"));
                    $objWorkSummarySheet->mergeCells("B6:B7");

                    $objWorkSummarySheet->SetCellValue("C6", $this->lang->line("inventory_order"));
                    $objWorkSummarySheet->mergeCells("C6:C7");

                    $objWorkSummarySheet->SetCellValue("D6", $this->lang->line("pieces"));
                    $objWorkSummarySheet->mergeCells("D6:E6");

                    $objWorkSummarySheet->SetCellValue("D7", $this->lang->line("reception_title"));
                    $objWorkSummarySheet->SetCellValue("E7", $this->lang->line("farm_title"));

                    $objWorkSummarySheet->SetCellValue("F6", $this->lang->line("volume_gross_hoppus"));
                    $objWorkSummarySheet->mergeCells("F6:F7");

                    $objWorkSummarySheet->SetCellValue("G6", $this->lang->line("volume_net_hoppus"));
                    $objWorkSummarySheet->mergeCells("G6:G7");

                    $objWorkSummarySheet->SetCellValue("H6", $this->lang->line("volume_gross_area"));
                    $objWorkSummarySheet->mergeCells("H6:H7");

                    $objWorkSummarySheet->SetCellValue("I6", $this->lang->line("volume_farm"));
                    $objWorkSummarySheet->mergeCells("I6:I7");

                    $objWorkSummarySheet->SetCellValue("J6", $this->lang->line("value_wood_reception"));
                    $objWorkSummarySheet->mergeCells("J6:J7");

                    $objWorkSummarySheet->SetCellValue("K6", $this->lang->line("value_wood_farm"));
                    $objWorkSummarySheet->mergeCells("K6:K7");

                    $objWorkSummarySheet->SetCellValue("L6", $this->lang->line("Logistics"));
                    $objWorkSummarySheet->mergeCells("L6:L7");

                    $objWorkSummarySheet->SetCellValue("M6", $this->lang->line("Service"));
                    $objWorkSummarySheet->mergeCells("M6:M7");

                    $objWorkSummarySheet->SetCellValue("N6", $this->lang->line("total"));
                    $objWorkSummarySheet->mergeCells("N6:N7");

                    $objWorkSummarySheet->SetCellValue("O6", $this->lang->line("difference_farm_reception"));
                    $objWorkSummarySheet->mergeCells("O6:O7");

                    $objWorkSummarySheet->SetCellValue("P6", $this->lang->line("value_material"));
                    $objWorkSummarySheet->mergeCells("P6:P7");

                    $objWorkSummarySheet->SetCellValue("Q6", $this->lang->line("invoice_number"));
                    $objWorkSummarySheet->mergeCells("Q6:Q7");

                    $objWorkSummarySheet->getStyle("A6:Q7")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("A6:Q6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("D7:E7")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("A6:Q7")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("F6:O6")->getAlignment()->setWrapText(true);

                    //END SUMMARY SHEET

                    //INVENTORY SHEET
                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($originId);

                    $sheetNo = 2;
                    $summarySNo = 1;
                    $summarySheetFirstRowData = 8;
                    $summarySheetRowDataCount = 8;

                    foreach ($getInventoryLiquidationReport as $sheetinventory) {

                        $objWorkInventorySheet = $this->excel->createSheet($sheetNo);
                        $objWorkInventorySheet->setTitle(strtoupper($sheetinventory->inventory_order));

                        if ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3) {
                        } else {

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G2", $this->lang->line("gross_volume"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));
                            $objWorkInventorySheet->SetCellValue("J2", $this->lang->line("text_cdv"));

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail($contractId, $supplierId, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("J2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            if ($originId == 1) {
                                $objWorkInventorySheet->SetCellValue("K2", "3");
                            } else {
                                $objWorkInventorySheet->SetCellValue("K2", "3");
                            }
                            //$objWorkInventorySheet->SetCellValue("K2", "2");
                            $objWorkInventorySheet->getStyle("K2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            $objWorkInventorySheet->getStyle("J2:K2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G2:H2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("J2:K2")->applyFromArray($styleArray);

                            $rowCount = 2;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('total_payment'));
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", ($getFarmDetail[0]->logistic_cost * -1));
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("Q$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("Q$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", ($getFarmDetail[0]->service_cost * -1));
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("R$rowCount", ($getFarmDetail[0]->adjustment * -1));
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("Q2:R$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $objWorkInventorySheet->SetCellValue("R2", "=SUM(R3:R$calcRow)");

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("circumference"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("vol_gross"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("vol_net"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("vol_gross_area"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("vol_reception"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("vol_farm"));
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");


                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("L$headerRow", $this->lang->line("circumference_range"));
                            $objWorkInventorySheet->mergeCells("L$headerRow:M$headerRow");

                            if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("volume_reception"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_farm"));
                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("volume_per_volume"));
                            } else {
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("pieces_reception"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("pieces_farm"));
                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("volume_per_piece"));
                            }

                            $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("reception_total_value"));
                            $objWorkInventorySheet->SetCellValue("R$headerRow", $this->lang->line("farm_total_value"));

                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->applyFromArray($styleArray);

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($contractId, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;
                                $farmDataFirstRow = $rowCount + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $circumferenceBoughtMin = $pricedata->minrange_grade1;
                                    $circumferenceBoughtMax = $pricedata->maxrange_grade2;

                                    //DATA FEED FARM

                                    $getFarmDataDetails = $this->Financemaster_model->get_farm_data_purchase_manager($circumferenceBoughtMin, $circumferenceBoughtMax, $getFarmDetail[0]->inventory_order);
                                    if (count($getFarmDataDetails) > 0) {

                                        $rowCount++;
                                        $farmData = $getFarmDataDetails[0];

                                        $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->receptionPieces);
                                        $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farmPieces);
                                        $objWorkInventorySheet->SetCellValue("C$rowCount", ($circumferenceBoughtMin + 0) . " - " . ($circumferenceBoughtMax + 0));
                                        $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->length_bought + 0));

                                        $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(LEFT(C$rowCount,FIND(" . '" - "' . ",C$rowCount)-1)+1,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(LEFT(C$rowCount,FIND(" . '" - "' . ",C$rowCount)-1)+1-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(ROUND(POWER(TRUNC((LEFT(C$rowCount,FIND(" . '" - "' . ",C$rowCount)-1)+1)/PI(),0),2)*0.7854*D$rowCount/1000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER(TRUNC((LEFT(C$rowCount,FIND(" . '" - "' . ",C$rowCount)-1)+1)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER(TRUNC((LEFT(C$rowCount,FIND(" . '" - "' . ",C$rowCount)-1)+1)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");


                                        //END DATA FEED FARM

                                        $objWorkInventorySheet->SetCellValue("L$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                        $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                        if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                            $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=H$priceSummaryRow");
                                            $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=I$priceSummaryRow");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=A$priceSummaryRow");
                                            $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=B$priceSummaryRow");
                                        }

                                        if ($getFarmDetail[0]->exchange_rate > 0) {
                                            $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                        } else {
                                            $priceRange = $pricedata->pricerange_grade3;
                                        }

                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", ($priceRange + 0));
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=SUM(N$priceSummaryRow*P$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUM(O$priceSummaryRow*P$priceSummaryRow)");
                                        $objWorkInventorySheet->getStyle("P$priceSummaryRow:R$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    }
                                    $priceSummaryRow++;
                                }

                                $lastRowFarmData = $rowCount;

                                $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=SUM(E$farmDataFirstRow:E$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("G$sumCalcRow", "=SUM(G$farmDataFirstRow:G$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=SUM(H$farmDataFirstRow:H$lastRowFarmData)");
                                $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=SUM(I$farmDataFirstRow:I$lastRowFarmData)");

                                $objWorkInventorySheet->SetCellValue("H2", "=E$sumCalcRow");
                                $objWorkInventorySheet->SetCellValue("H4", "=F$sumCalcRow");
                                if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                    $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/B$sumCalcRow*35.315,2),0)");
                                } else {
                                    $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");
                                }

                                $objWorkInventorySheet->getStyle("A$sumCalcRow:I$lastRowFarmData")->applyFromArray($styleArray);

                                $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                                $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                                $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                                $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);

                                $priceLastRow = $priceSummaryRow - 1;

                                $objWorkInventorySheet->SetCellValue("N$sumCalcRow", "=SUM(N$priceFirstRow:N$priceLastRow)");
                                $objWorkInventorySheet->SetCellValue("O$sumCalcRow", "=SUM(O$priceFirstRow:O$priceLastRow)");
                                $objWorkInventorySheet->getStyle("N$sumCalcRow:O$sumCalcRow")->applyFromArray($styleArray);

                                $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)");
                                $objWorkInventorySheet->SetCellValue("R$sumCalcRow", "=SUM(R$priceFirstRow:R$priceLastRow)");

                                $objWorkInventorySheet->getStyle("Q$sumCalcRow:R$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                $objWorkInventorySheet->getStyle("R2:R$priceLastRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                $objWorkInventorySheet->getStyle("L$priceFirstRow:R$priceLastRow")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(13);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(13);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(17);
                            $objWorkInventorySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(17);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!I$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!Q$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "=SUM(K$summarySheetRowDataCount:M$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "=J$summarySheetRowDataCount-K$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=N$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;
                        }
                    }

                    $summarySheetLastRowData = $summarySheetRowDataCount - 1;

                    $objWorkSummarySheet->SetCellValue("D5", "=SUM(D$summarySheetFirstRowData:D$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("E5", "=SUM(E$summarySheetFirstRowData:E$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("F5", "=SUM(F$summarySheetFirstRowData:F$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("G5", "=SUM(G$summarySheetFirstRowData:G$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("D5:G5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("D5:G5")->getFont()->setBold(true);

                    $objWorkSummarySheet->SetCellValue("J5", "=SUM(J$summarySheetFirstRowData:J$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("K5", "=SUM(K$summarySheetFirstRowData:K$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("L5", "=SUM(L$summarySheetFirstRowData:L$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("M5", "=SUM(M$summarySheetFirstRowData:M$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("N5", "=SUM(N$summarySheetFirstRowData:N$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("O5", "=SUM(O$summarySheetFirstRowData:O$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("P5", "=SUM(P$summarySheetFirstRowData:P$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("J5:P5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("J5:P5")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("J5:P5")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->SetCellValue("J4", "=J5/D5");
                    $objWorkSummarySheet->SetCellValue("K4", "=K5/D5");
                    $objWorkSummarySheet->SetCellValue("L4", "=L5/D5");
                    $objWorkSummarySheet->SetCellValue("M4", "=M5/D5");
                    $objWorkSummarySheet->SetCellValue("N4", "=N5/D5");
                    $objWorkSummarySheet->SetCellValue("O4", "=O5/D5");
                    $objWorkSummarySheet->SetCellValue("P4", "=P5/D5");
                    $objWorkSummarySheet->getStyle("J4:P4")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("J4:P4")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("J4:P4")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->getStyle("J$summarySheetFirstRowData:P$summarySheetLastRowData")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                    $objWorkSummarySheet->getStyle("A$summarySheetFirstRowData:Q$summarySheetLastRowData")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("A$summarySheetRowDataCount:Q$summarySheetLastRowData")->applyFromArray($styleArray);

                    $objWorkSummarySheet->getStyle("J5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("ED7D31");
                    $objWorkSummarySheet->getStyle("K5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("7B7B7B");
                    $objWorkSummarySheet->getStyle("L5:M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("548235");
                    $objWorkSummarySheet->getStyle("O5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objWorkSummarySheet->getStyle("J4:P4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("9BC2E6");

                    $objWorkSummarySheet->SetCellValue("D1", "=D5-E5");
                    $objWorkSummarySheet->getStyle("D1")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objWorkSummarySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(9);
                    $objWorkSummarySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(16);
                    $objWorkSummarySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(14);

                    //END INVENTORY SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "LiquidationReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/LiquidationReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/LiquidationReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line("no_data_reports");
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_liquidation_report()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '',
                'error' => '',
                'redirect' => false,
                'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $originId = $this->input->post("origin_id");

                if ($originId > 0) {

                    $data = array(
                        "pageheading" => $this->lang->line("download_report"),
                        "pagetype" => "downloadliquidation",
                        "csrf_hash" => $this->security->get_csrf_hash(),
                        "originId" => $originId,
                        "suppliers" => $this->Contract_model->get_suppliers_by_origin($originId),
                    );
                    $this->load->view("financereports/dialog_downloadliquidation", $data);
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function download_liquidation_report_bulk1()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $supplierId = $this->input->post("supplierId");
                $fromDate = $this->input->post("fromDate");
                $toDate = $this->input->post("toDate");
                $originId = $this->input->post("originId");
                $farmId = $this->input->post("farmId");

                $fromDate = str_replace('/', '-', $fromDate);
                $fromDate = date("Y-m-d", strtotime($fromDate));

                $toDate = str_replace('/', '-', $toDate);
                $toDate = date("Y-m-d", strtotime($toDate));

                $getInventoryLiquidationReport = $this->Financemaster_model->fetch_inventory_report_warehouse_bulk($supplierId, $fromDate, $toDate, $originId, $farmId);

                if (count($getInventoryLiquidationReport) > 0) {

                    $getCurrency = $this->Financemaster_model->get_currency_code($originId);

                    //ADVANCE CONTROL SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("advance_control"));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->SetCellValue("B2", $this->lang->line("account_status"));
                    $objSheet->getStyle("B2:C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("000000");
                    $objSheet->getStyle('B2:C2')->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                    $objSheet->SetCellValue("A5", $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue("B5", $this->lang->line("concept"));
                    $objSheet->SetCellValue("C5", $this->lang->line("value"));
                    $objSheet->SetCellValue("D5", $this->lang->line("pay_to"));

                    $objSheet->getStyle("A5:D5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objSheet->getStyle("A5:D5")->getFont()->setBold(true)->getColor()->setRGB("000000");
                    $objSheet->getStyle("A5:D5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("B2:C2")->applyFromArray($styleArray);
                    $objSheet->getStyle("A5:D5")->applyFromArray($styleArray);

                    $wz_lang = $this->session->userdata("site_lang");
                    $lang_code = $this->Settings_model->get_language_info($wz_lang);
                    $getInventoryLedger = $this->Financemaster_model->get_inventory_ledger_contract_bulk($supplierId, $fromDate, $toDate, $lang_code[0]->language_format_code, $farmId, $originId);

                    $rowLedger = 6;
                    if (count($getInventoryLedger) > 0) {

                        foreach ($getInventoryLedger as $inventoryledger) {

                            $objSheet->SetCellValue("A$rowLedger", $inventoryledger->expense_date);
                            $objSheet->SetCellValue("B$rowLedger", $this->lang->line($inventoryledger->type_name) . " / " . $inventoryledger->inventory_order);
                            $objSheet->SetCellValue("C$rowLedger", $inventoryledger->amount);
                            $objSheet->SetCellValue("D$rowLedger", $inventoryledger->supplier_name);

                            $rowLedger++;
                        }

                        $lastRowLedger = $rowLedger - 1;
                        $objSheet->getStyle("C6:C$lastRowLedger")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);

                        $objSheet->getStyle("A6:D$lastRowLedger")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("C2", "=SUM(C6:C$lastRowLedger)");
                        $objSheet->getStyle("C2")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);



                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                    }

                    //END ADVANCE CONTROL SHEET

                    //SUMMARY SHEET

                    $objWorkSummarySheet = $this->excel->createSheet(1);
                    $objWorkSummarySheet->setTitle($this->lang->line("report_summary"));

                    $objWorkSummarySheet->SetCellValue("C1", $this->lang->line("transit_loss"));
                    $objWorkSummarySheet->getStyle("C1:D1")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("C1:D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("AEAAAA");
                    $objWorkSummarySheet->getStyle("C1:D1")->applyFromArray($styleArray);

                    $objWorkSummarySheet->SetCellValue("A6", $this->lang->line("serial_no"));
                    $objWorkSummarySheet->mergeCells("A6:A7");

                    $objWorkSummarySheet->SetCellValue("B6", $this->lang->line("costsummary_date"));
                    $objWorkSummarySheet->mergeCells("B6:B7");

                    $objWorkSummarySheet->SetCellValue("C6", $this->lang->line("supplier_name"));
                    $objWorkSummarySheet->mergeCells("C6:C7");

                    $objWorkSummarySheet->SetCellValue("D6", $this->lang->line("inventory_order"));
                    $objWorkSummarySheet->mergeCells("D6:D7");

                    $objWorkSummarySheet->SetCellValue("E6", $this->lang->line("pieces"));
                    $objWorkSummarySheet->mergeCells("E6:F6");

                    $objWorkSummarySheet->SetCellValue("E7", $this->lang->line("reception_title"));
                    $objWorkSummarySheet->SetCellValue("F7", $this->lang->line("farm_title"));

                    $objWorkSummarySheet->SetCellValue("G6", $this->lang->line("length"));
                    $objWorkSummarySheet->mergeCells("G6:G7");

                    $objWorkSummarySheet->SetCellValue("H6", $this->lang->line("circumference") . " / ". $this->lang->line("square_foot"));
                    $objWorkSummarySheet->mergeCells("H6:H7");

                    $objWorkSummarySheet->SetCellValue("I6", $this->lang->line("volume_gross_hoppus"));
                    $objWorkSummarySheet->mergeCells("I6:I7");

                    $objWorkSummarySheet->SetCellValue("J6", $this->lang->line("volume_net_hoppus"));
                    $objWorkSummarySheet->mergeCells("J6:J7");

                    $objWorkSummarySheet->SetCellValue("K6", $this->lang->line("volume_gross_area"));
                    $objWorkSummarySheet->mergeCells("K6:K7");

                    $objWorkSummarySheet->SetCellValue("L6", $this->lang->line("volume_farm"));
                    $objWorkSummarySheet->mergeCells("L6:L7");

                    $objWorkSummarySheet->SetCellValue("M6", $this->lang->line("value_wood_reception"));
                    $objWorkSummarySheet->mergeCells("M6:M7");

                    $objWorkSummarySheet->SetCellValue("N6", $this->lang->line("value_wood_farm"));
                    $objWorkSummarySheet->mergeCells("N6:N7");

                    $objWorkSummarySheet->SetCellValue("O6", $this->lang->line("Logistics"));
                    $objWorkSummarySheet->mergeCells("O6:O7");

                    $objWorkSummarySheet->SetCellValue("P6", $this->lang->line("Service"));
                    $objWorkSummarySheet->mergeCells("P6:P7");

                    $objWorkSummarySheet->SetCellValue("Q6", $this->lang->line("total"));
                    $objWorkSummarySheet->mergeCells("Q6:Q7");

                    $objWorkSummarySheet->SetCellValue("R6", $this->lang->line("difference_farm_reception"));
                    $objWorkSummarySheet->mergeCells("R6:R7");

                    $objWorkSummarySheet->SetCellValue("S6", $this->lang->line("value_material"));
                    $objWorkSummarySheet->mergeCells("S6:S7");

                    $objWorkSummarySheet->SetCellValue("T6", $this->lang->line("invoice_number"));
                    $objWorkSummarySheet->mergeCells("T6:T7");

                    $objWorkSummarySheet->getStyle("A6:T7")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("A6:T6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("E7:F7")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("A6:T7")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("G6:R6")->getAlignment()->setWrapText(true);

                    //END SUMMARY SHEET

                    //INVENTORY SHEET
                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($originId);

                    $sheetNo = 2;
                    $summarySNo = 1;
                    $summarySheetFirstRowData = 8;
                    $summarySheetRowDataCount = 8;

                    foreach ($getInventoryLiquidationReport as $sheetinventory) {

                        $objWorkInventorySheet = $this->excel->createSheet($sheetNo);
                        $objWorkInventorySheet->setTitle(strtoupper($sheetinventory->inventory_order));

                        if ($sheetinventory->product == 4 && ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3)) {

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail_bulk($supplierId, $fromDate, $toDate, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("Q$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("Q$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("Q$startPaymentRow:R$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("R$totalPaymentRow", "=SUM(R$totalPaymentRow1:R$calcRow)");

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("width"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("thickness"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("square_foot"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("length_mtr"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("width_cms"));
                            $objWorkInventorySheet->SetCellValue("J$headerRow", $this->lang->line("thickness_cms"));
                            $objWorkInventorySheet->SetCellValue("K$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("L$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("value"));
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data_square($supplierId, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;
                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;

                                $txtFeet = '"ft"';
                                $txtMeter = '"m"';
                                $txtInch = '"in"';
                                $txtCm = '"cm"';
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->length_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->width_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("E$rowCount", ($farmData->thickness_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC((C$rowCount*D$rowCount*E$rowCount/12)*B$rowCount,0),0)");
                                    $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(D$rowCount*E$rowCount, 0)");
                                    $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(CONVERT(C$rowCount,$txtFeet,$txtMeter),2),0)");
                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(CONVERT(D$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("J$rowCount", "=IFERROR(TRUNC(CONVERT(E$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("K$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("L$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*B$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("M$rowCount", "=IFERROR(LOOKUP(G$rowCount,O$farmDataFirstRow:P100,Q$farmDataFirstRow:Q100)*F$rowCount,0)");
                                    $objWorkInventorySheet->getStyle("M$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    $rowCount++;
                                }

                                
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(E$farmDataFirstRow:E$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(H$farmDataFirstRow:H$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(I$farmDataFirstRow:I$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("J$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(J$farmDataFirstRow:J$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("K$sumCalcRow", "=SUM(K$farmDataFirstRow:K$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("L$sumCalcRow", "=SUM(L$farmDataFirstRow:L$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("M$sumCalcRow", "=SUM(M$farmDataFirstRow:M$lastRowFarmData)");
                            $objWorkInventorySheet->getStyle("M$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            
                            $objWorkInventorySheet->getStyle("A$sumCalcRow:M$lastRowFarmData")->applyFromArray($styleArray);

                            $cftText = "&" .'"' . " x " .'"'. "&";

                            $objWorkInventorySheet->SetCellValue("H4", "=K$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H6", "=E$sumCalcRow".$cftText."D$sumCalcRow");

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->mergeCells("O$headerRow:P$headerRow");
                            $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("volume_per_piece"));

                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->applyFromArray($styleArray);

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($getFarmDetail[0]->contract_id, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->exchange_rate > 0) {
                                        $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    } else {
                                        $priceRange = $pricedata->pricerange_grade3;
                                    }

                                    $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", ($priceRange + 0));
                                    $objWorkInventorySheet->getStyle("Q$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                $objWorkInventorySheet->getStyle("O$priceFirstRow:Q$priceLastRow")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                            $objWorkInventorySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(22);

                            $objWorkInventorySheet->SetCellValue("R$sumCalcRow", "=M$sumCalcRow");
                            $objWorkInventorySheet->getStyle("R$totalPaymentRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $objWorkInventorySheet->getStyle("R$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            // $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            // $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            // $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            // $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            // $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");


                            // $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!I$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            // $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            // $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(K$summarySheetRowDataCount:M$summarySheetRowDataCount)");
                            // $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=J$summarySheetRowDataCount-K$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=N$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B6");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "0");
                            

                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!L$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=SUM(N$summarySheetRowDataCount:P$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=M$summarySheetRowDataCount-N$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", "=Q$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("T$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;

                        } else if ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3) {
                        } else {

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G2", $this->lang->line("gross_volume"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));
                            $objWorkInventorySheet->SetCellValue("J2", $this->lang->line("text_cdv"));

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail_bulk($supplierId, $fromDate, $toDate, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("J2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            
                            if($originId == 1) {
                                $objWorkInventorySheet->SetCellValue("K2", "3");
                            } else {
                                $objWorkInventorySheet->SetCellValue("K2", "3");
                            }
                            
                            $objWorkInventorySheet->getStyle("K2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            $objWorkInventorySheet->getStyle("J2:K2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G2:H2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("J2:K2")->applyFromArray($styleArray);

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("Q$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("Q$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("Q$startPaymentRow:R$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("R$totalPaymentRow", "=SUM(R$totalPaymentRow1:R$calcRow)");

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("circumference"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("vol_gross"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("vol_net"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("vol_gross_area"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("vol_reception"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("vol_farm"));
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data($getFarmDetail[0]->supplier_id, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;
                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->circumference_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->length_bought + 0));

                                    
                                    $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(ROUND(POWER(TRUNC(C$rowCount/PI(),0),2)*0.7854*D$rowCount/1000000,3)*A$rowCount,0)");


                                    $circumferenceAllowance = $getFarmDetail[0]->purchase_allowance;
                                    $lengthAllowance = $getFarmDetail[0]->purchase_allowance_length;

                                    if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 6) {
                                        if($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                        
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 5 || $getFarmDetail[0]->purchase_unit_id == 7 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        if($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 8) {
                                        if($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 9) {
                                        if($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-$circumferenceAllowance),2)*(D$rowCount-$lengthAllowance)/16000000,3)*B$rowCount,0)");
                                        }
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                    }

                                    $rowCount++;
                                }
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=TRUNC(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=TRUNC(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0)/100");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=SUM(E$farmDataFirstRow:E$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("G$sumCalcRow", "=SUM(G$farmDataFirstRow:G$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=SUM(H$farmDataFirstRow:H$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=SUM(I$farmDataFirstRow:I$lastRowFarmData)");

                            $objWorkInventorySheet->SetCellValue("H2", "=E$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H4", "=F$sumCalcRow");
                            if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/B$sumCalcRow*35.315,2),0)");
                            } else {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");
                            }

                            if($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                $objWorkInventorySheet->SetCellValue("J4", $this->lang->line("average_girth"));
                                $objWorkInventorySheet->SetCellValue("K4", "=TRUNC(SUMPRODUCT(B$farmDataFirstRow:B$lastRowFarmData,C$farmDataFirstRow:C$lastRowFarmData)/B$sumCalcRow,0)");
                                
                                $objWorkInventorySheet->getStyle("K4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                                $objWorkInventorySheet->getStyle("J4:K4")->getFont()->setBold(true)->getColor()->setRGB("000000");
                                $objWorkInventorySheet->getStyle("J4:K4")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getStyle("A$sumCalcRow:I$lastRowFarmData")->applyFromArray($styleArray);

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("L$headerRow", $this->lang->line("circumference_range"));
                            $objWorkInventorySheet->mergeCells("L$headerRow:M$headerRow");

                            if (
                                $getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5
                                || $getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7
                            ) {
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("volume_reception"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_farm"));
                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("volume_per_volume"));
                            } else {
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("pieces_reception"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("pieces_farm"));
                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("volume_per_piece"));
                            }

                            $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("reception_total_value"));
                            $objWorkInventorySheet->SetCellValue("R$headerRow", $this->lang->line("farm_total_value"));

                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objWorkInventorySheet->getStyle("L$headerRow:R$headerRow")->applyFromArray($styleArray);

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($getFarmDetail[0]->contract_id, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("L$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$I$' . "$farmDataFirstRow" . ':$I$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(".'$K$4'.">=L$priceSummaryRow,IF(".'$K$4'."<=M$priceSummaryRow,".'$H$'."$sumCalcRow,0),0)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(".'$K$4'.">=L$priceSummaryRow,IF(".'$K$4'."<=M$priceSummaryRow,".'$I$'."$sumCalcRow,0),0)");
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(".'$H$6'.">=L$priceSummaryRow,IF(".'$H$6'."<=M$priceSummaryRow,".'$H$'."$sumCalcRow,0),0)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(".'$H$6'.">=L$priceSummaryRow,IF(".'$H$6'."<=M$priceSummaryRow,".'$I$'."$sumCalcRow,0),0)");
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                    }

                                    if ($getFarmDetail[0]->exchange_rate > 0) {
                                        $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    } else {
                                        $priceRange = $pricedata->pricerange_grade3;
                                    }

                                    if($getFarmDetail[0]->purchase_unit_id == 15) {
                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=$priceRange/O$priceSummaryRow");
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", ($priceRange + 0));
                                    }
                                    
                                    if($getFarmDetail[0]->purchase_unit_id == 15) {
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=SUM(N$priceSummaryRow*P$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUM(O$priceSummaryRow*P$priceSummaryRow)");
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=SUM(N$priceSummaryRow*P$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUM(O$priceSummaryRow*P$priceSummaryRow)");   
                                    }
                                    $objWorkInventorySheet->getStyle("P$priceSummaryRow:R$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                $objWorkInventorySheet->SetCellValue("N$sumCalcRow", "=SUM(N$priceFirstRow:N$priceLastRow)");
                                $objWorkInventorySheet->SetCellValue("O$sumCalcRow", "=SUM(O$priceFirstRow:O$priceLastRow)");
                                $objWorkInventorySheet->getStyle("N$sumCalcRow:O$sumCalcRow")->applyFromArray($styleArray);

                                $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)");
                                $objWorkInventorySheet->SetCellValue("R$sumCalcRow", "=SUM(R$priceFirstRow:R$priceLastRow)");

                                $objWorkInventorySheet->getStyle("Q$sumCalcRow:R$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                $objWorkInventorySheet->getStyle("R2:R$priceLastRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                $objWorkInventorySheet->getStyle("L$priceFirstRow:R$priceLastRow")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(13);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(17);
                            $objWorkInventorySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(17);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B6");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");
                            

                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!I$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!Q$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=SUM(N$summarySheetRowDataCount:P$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=M$summarySheetRowDataCount-N$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", "=Q$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("T$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;
                        }
                    }

                    $summarySheetLastRowData = $summarySheetRowDataCount - 1;

                    $objWorkSummarySheet->SetCellValue("E5", "=SUM(E$summarySheetFirstRowData:E$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("F5", "=SUM(F$summarySheetFirstRowData:F$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("I5", "=SUM(I$summarySheetFirstRowData:I$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("J5", "=SUM(J$summarySheetFirstRowData:J$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("E5:J5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("E5:J5")->getFont()->setBold(true);

                    $objWorkSummarySheet->SetCellValue("M5", "=SUM(M$summarySheetFirstRowData:M$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("N5", "=SUM(N$summarySheetFirstRowData:N$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("O5", "=SUM(O$summarySheetFirstRowData:O$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("P5", "=SUM(P$summarySheetFirstRowData:P$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("Q5", "=SUM(Q$summarySheetFirstRowData:Q$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("R5", "=SUM(R$summarySheetFirstRowData:R$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("S5", "=SUM(S$summarySheetFirstRowData:S$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("M5:S5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("M5:S5")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("M5:S5")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->SetCellValue("M4", "=M5/E5");
                    $objWorkSummarySheet->SetCellValue("N4", "=N5/E5");
                    $objWorkSummarySheet->SetCellValue("O4", "=O5/E5");
                    $objWorkSummarySheet->SetCellValue("P4", "=P5/E5");
                    $objWorkSummarySheet->SetCellValue("Q4", "=Q5/E5");
                    $objWorkSummarySheet->SetCellValue("R4", "=R5/E5");
                    $objWorkSummarySheet->SetCellValue("S4", "=S5/E5");
                    $objWorkSummarySheet->getStyle("M4:S4")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("M4:S4")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("M4:S4")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->getStyle("M$summarySheetFirstRowData:S$summarySheetLastRowData")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                    $objWorkSummarySheet->getStyle("A$summarySheetFirstRowData:T$summarySheetLastRowData")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("A$summarySheetRowDataCount:T$summarySheetLastRowData")->applyFromArray($styleArray);

                    $objWorkSummarySheet->getStyle("L5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("ED7D31");
                    $objWorkSummarySheet->getStyle("M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("7B7B7B");
                    $objWorkSummarySheet->getStyle("L5:M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("548235");
                    $objWorkSummarySheet->getStyle("O5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objWorkSummarySheet->getStyle("L4:R4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("9BC2E6");

                    $objWorkSummarySheet->SetCellValue("D1", "=E5-F5");
                    $objWorkSummarySheet->getStyle("D1")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objWorkSummarySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(9);
                    $objWorkSummarySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(30);
                    $objWorkSummarySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(16);
                    $objWorkSummarySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14);
                    $objWorkSummarySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("S")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("T")->setAutoSize(false)->setWidth(14);

                    //END INVENTORY SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "LiquidationReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/LiquidationReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/LiquidationReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line("no_data_reports");
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function download_liquidation_report_bulk()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $supplierId = $this->input->post("supplierId");
                $fromDate = $this->input->post("fromDate");
                $toDate = $this->input->post("toDate");
                $originId = $this->input->post("originId");
                $farmId = $this->input->post("farmId");

                $fromDate = str_replace('/', '-', $fromDate);
                $fromDate = date("Y-m-d", strtotime($fromDate));

                $toDate = str_replace('/', '-', $toDate);
                $toDate = date("Y-m-d", strtotime($toDate));

                $getInventoryLiquidationReport = $this->Financemaster_model->fetch_inventory_report_warehouse_bulk($supplierId, $fromDate, $toDate, $originId, $farmId);

                if (count($getInventoryLiquidationReport) > 0) {

                    $getCurrency = $this->Financemaster_model->get_currency_code($originId);

                    //ADVANCE CONTROL SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("advance_control"));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->SetCellValue("B2", $this->lang->line("account_status"));
                    $objSheet->getStyle("B2:C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("000000");
                    $objSheet->getStyle('B2:C2')->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");

                    $objSheet->SetCellValue("A5", $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue("B5", $this->lang->line("concept"));
                    $objSheet->SetCellValue("C5", $this->lang->line("value"));
                    $objSheet->SetCellValue("D5", $this->lang->line("pay_to"));

                    $objSheet->getStyle("A5:D5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objSheet->getStyle("A5:D5")->getFont()->setBold(true)->getColor()->setRGB("000000");
                    $objSheet->getStyle("A5:D5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("B2:C2")->applyFromArray($styleArray);
                    $objSheet->getStyle("A5:D5")->applyFromArray($styleArray);

                    $wz_lang = $this->session->userdata("site_lang");
                    $lang_code = $this->Settings_model->get_language_info($wz_lang);
                    $getInventoryLedger = $this->Financemaster_model->get_inventory_ledger_contract_bulk($supplierId, $fromDate, $toDate, $lang_code[0]->language_format_code, $farmId, $originId);

                    $rowLedger = 6;
                    if (count($getInventoryLedger) > 0) {

                        foreach ($getInventoryLedger as $inventoryledger) {

                            $objSheet->SetCellValue("A$rowLedger", $inventoryledger->expense_date);
                            $objSheet->SetCellValue("B$rowLedger", $this->lang->line($inventoryledger->type_name) . " / " . $inventoryledger->inventory_order);
                            $objSheet->SetCellValue("C$rowLedger", $inventoryledger->amount);
                            $objSheet->SetCellValue("D$rowLedger", $inventoryledger->supplier_name);

                            $rowLedger++;
                        }

                        $lastRowLedger = $rowLedger - 1;
                        $objSheet->getStyle("C6:C$lastRowLedger")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);

                        $objSheet->getStyle("A6:D$lastRowLedger")->applyFromArray($styleArray);

                        $objSheet->SetCellValue("C2", "=SUM(C6:C$lastRowLedger)");
                        $objSheet->getStyle("C2")
                            ->getNumberFormat()
                            ->setFormatCode($getCurrency[0]->currency_excel_format);



                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                    }

                    //END ADVANCE CONTROL SHEET

                    //SUMMARY SHEET

                    $objWorkSummarySheet = $this->excel->createSheet(1);
                    $objWorkSummarySheet->setTitle($this->lang->line("report_summary"));

                    $objWorkSummarySheet->SetCellValue("C1", $this->lang->line("transit_loss"));
                    $objWorkSummarySheet->getStyle("C1:D1")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("C1:D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("AEAAAA");
                    $objWorkSummarySheet->getStyle("C1:D1")->applyFromArray($styleArray);

                    $objWorkSummarySheet->SetCellValue("A6", $this->lang->line("serial_no"));
                    $objWorkSummarySheet->mergeCells("A6:A7");

                    $objWorkSummarySheet->SetCellValue("B6", $this->lang->line("costsummary_date"));
                    $objWorkSummarySheet->mergeCells("B6:B7");

                    $objWorkSummarySheet->SetCellValue("C6", $this->lang->line("supplier_name"));
                    $objWorkSummarySheet->mergeCells("C6:C7");

                    $objWorkSummarySheet->SetCellValue("D6", $this->lang->line("inventory_order"));
                    $objWorkSummarySheet->mergeCells("D6:D7");

                    $objWorkSummarySheet->SetCellValue("E6", $this->lang->line("pieces"));
                    $objWorkSummarySheet->mergeCells("E6:F6");

                    $objWorkSummarySheet->SetCellValue("E7", $this->lang->line("reception_title"));
                    $objWorkSummarySheet->SetCellValue("F7", $this->lang->line("farm_title"));

                    $objWorkSummarySheet->SetCellValue("G6", $this->lang->line("length"));
                    $objWorkSummarySheet->mergeCells("G6:G7");

                    $objWorkSummarySheet->SetCellValue("H6", $this->lang->line("circumference") . " / ". $this->lang->line("square_foot"));
                    $objWorkSummarySheet->mergeCells("H6:H7");

                    $objWorkSummarySheet->SetCellValue("I6", $this->lang->line("volume_gross_hoppus"));
                    $objWorkSummarySheet->mergeCells("I6:I7");

                    $objWorkSummarySheet->SetCellValue("J6", $this->lang->line("volume_net_hoppus"));
                    $objWorkSummarySheet->mergeCells("J6:J7");

                    $objWorkSummarySheet->SetCellValue("K6", $this->lang->line("volume_gross_area"));
                    $objWorkSummarySheet->mergeCells("K6:K7");

                    $objWorkSummarySheet->SetCellValue("L6", $this->lang->line("volume_farm"));
                    $objWorkSummarySheet->mergeCells("L6:L7");

                    $objWorkSummarySheet->SetCellValue("M6", $this->lang->line("value_wood_reception"));
                    $objWorkSummarySheet->mergeCells("M6:M7");

                    $objWorkSummarySheet->SetCellValue("N6", $this->lang->line("value_wood_farm"));
                    $objWorkSummarySheet->mergeCells("N6:N7");

                    $objWorkSummarySheet->SetCellValue("O6", $this->lang->line("Logistics"));
                    $objWorkSummarySheet->mergeCells("O6:O7");

                    $objWorkSummarySheet->SetCellValue("P6", $this->lang->line("Service"));
                    $objWorkSummarySheet->mergeCells("P6:P7");

                    $objWorkSummarySheet->SetCellValue("Q6", $this->lang->line("total"));
                    $objWorkSummarySheet->mergeCells("Q6:Q7");

                    $objWorkSummarySheet->SetCellValue("R6", $this->lang->line("difference_farm_reception"));
                    $objWorkSummarySheet->mergeCells("R6:R7");

                    $objWorkSummarySheet->SetCellValue("S6", $this->lang->line("value_material"));
                    $objWorkSummarySheet->mergeCells("S6:S7");

                    $objWorkSummarySheet->SetCellValue("T6", $this->lang->line("invoice_number"));
                    $objWorkSummarySheet->mergeCells("T6:T7");

                    $objWorkSummarySheet->getStyle("A6:T7")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("A6:T6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("E7:F7")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSummarySheet->getStyle("A6:T7")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("G6:R6")->getAlignment()->setWrapText(true);

                    //END SUMMARY SHEET

                    //INVENTORY SHEET
                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($originId);

                    $sheetNo = 2;
                    $summarySNo = 1;
                    $summarySheetFirstRowData = 8;
                    $summarySheetRowDataCount = 8;

                    foreach ($getInventoryLiquidationReport as $sheetinventory) {

                        $objWorkInventorySheet = $this->excel->createSheet($sheetNo);
                        $objWorkInventorySheet->setTitle(strtoupper($sheetinventory->inventory_order));

                        if ($sheetinventory->product == 4 && ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3)) {

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail_bulk($supplierId, $fromDate, $toDate, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("Q$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("Q$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(R&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(R&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "R$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "R$rowCount";

                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("R$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("R$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("Q$startPaymentRow:R$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("R$totalPaymentRow", "=SUM(R$totalPaymentRow1:R$calcRow)");

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("width"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("thickness"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("square_foot"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("length_mtr"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("width_cms"));
                            $objWorkInventorySheet->SetCellValue("J$headerRow", $this->lang->line("thickness_cms"));
                            $objWorkInventorySheet->SetCellValue("K$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("L$headerRow", $this->lang->line("cbm_block_export"));
                            $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("value"));
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:M$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data_square($supplierId, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;
                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;

                                $txtFeet = '"ft"';
                                $txtMeter = '"m"';
                                $txtInch = '"in"';
                                $txtCm = '"cm"';
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->length_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->width_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("E$rowCount", ($farmData->thickness_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC((C$rowCount*D$rowCount*E$rowCount/12)*B$rowCount,0),0)");
                                    $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(D$rowCount*E$rowCount, 0)");
                                    $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(CONVERT(C$rowCount,$txtFeet,$txtMeter),2),0)");
                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(TRUNC(CONVERT(D$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("J$rowCount", "=IFERROR(TRUNC(CONVERT(E$rowCount,$txtInch,$txtCm),2),0)");
                                    $objWorkInventorySheet->SetCellValue("K$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("L$rowCount", "=IFERROR(ROUND(H$rowCount*I$rowCount*J$rowCount/10000,3)*B$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("M$rowCount", "=IFERROR(LOOKUP(G$rowCount,O$farmDataFirstRow:P100,Q$farmDataFirstRow:Q100)*F$rowCount,0)");
                                    $objWorkInventorySheet->getStyle("M$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    $rowCount++;
                                }

                                
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(E$farmDataFirstRow:E$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(H$farmDataFirstRow:H$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(I$farmDataFirstRow:I$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("J$sumCalcRow", "=IFERROR(ROUND(SUMPRODUCT(J$farmDataFirstRow:J$lastRowFarmData,B$farmDataFirstRow:B$lastRowFarmData)/B$sumCalcRow,2), 0)");
                            $objWorkInventorySheet->SetCellValue("K$sumCalcRow", "=SUM(K$farmDataFirstRow:K$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("L$sumCalcRow", "=SUM(L$farmDataFirstRow:L$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("M$sumCalcRow", "=SUM(M$farmDataFirstRow:M$lastRowFarmData)");
                            $objWorkInventorySheet->getStyle("M$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            
                            $objWorkInventorySheet->getStyle("A$sumCalcRow:M$lastRowFarmData")->applyFromArray($styleArray);

                            $cftText = "&" .'"' . " x " .'"'. "&";

                            $objWorkInventorySheet->SetCellValue("H4", "=K$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H6", "=E$sumCalcRow".$cftText."D$sumCalcRow");

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("face"));
                            $objWorkInventorySheet->mergeCells("O$headerRow:P$headerRow");
                            $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("volume_per_piece"));

                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objWorkInventorySheet->getStyle("O$headerRow:Q$headerRow")->applyFromArray($styleArray);

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($getFarmDetail[0]->contract_id, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->exchange_rate > 0) {
                                        $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    } else {
                                        $priceRange = $pricedata->pricerange_grade3;
                                    }

                                    $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", ($priceRange + 0));
                                    $objWorkInventorySheet->getStyle("Q$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                $objWorkInventorySheet->getStyle("O$priceFirstRow:Q$priceLastRow")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(10);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                            $objWorkInventorySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(22);

                            $objWorkInventorySheet->SetCellValue("R$sumCalcRow", "=M$sumCalcRow");
                            $objWorkInventorySheet->getStyle("R$totalPaymentRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $objWorkInventorySheet->getStyle("R$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            // $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            // $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            // $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            // $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            // $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");


                            // $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!I$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            // $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            // $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            // $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(K$summarySheetRowDataCount:M$summarySheetRowDataCount)");
                            // $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=J$summarySheetRowDataCount-K$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=N$summarySheetRowDataCount");
                            // $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B6");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "0");
                            

                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "0");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!L$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!R$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=SUM(N$summarySheetRowDataCount:P$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=M$summarySheetRowDataCount-N$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", "=Q$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("T$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;

                        } else if ($sheetinventory->product_type == 1 || $sheetinventory->product_type == 3) {
                        } else {

                            $getFarmDetail = $this->Financemaster_model->get_farm_detail_bulk($supplierId, $fromDate, $toDate, $originId, $sheetinventory->inventory_order, $lang_code[0]->language_format_code);

                            $objWorkInventorySheet->SetCellValue("A2", $this->lang->line("costsummary_date"));
                            $objWorkInventorySheet->SetCellValue("A4", $this->lang->line("truck_plate"));
                            $objWorkInventorySheet->SetCellValue("A6", $this->lang->line("supplier_name"));
                            $objWorkInventorySheet->SetCellValue("D2", $this->lang->line("inventory_order"));
                            $objWorkInventorySheet->SetCellValue("G2", $this->lang->line("gross_volume"));
                            $objWorkInventorySheet->SetCellValue("G4", $this->lang->line("net_volume"));
                            $objWorkInventorySheet->SetCellValue("G6", $this->lang->line("text_cft"));
                            $objWorkInventorySheet->SetCellValue("J2", $this->lang->line("circumference_allowance"));
                            $objWorkInventorySheet->SetCellValue("J3", $this->lang->line("length_allowance"));

                            if ($getFarmDetail[0]->exchange_rate > 0) {
                                $objWorkInventorySheet->SetCellValue("J6", $this->lang->line("exchange_rate"));
                                $objWorkInventorySheet->SetCellValue("K6", $getFarmDetail[0]->exchange_rate + 0);
                                $objWorkInventorySheet->getStyle("K6")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            }

                            $objWorkInventorySheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objWorkInventorySheet->getStyle("J6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objWorkInventorySheet->getStyle("A2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("D2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G4")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("G6")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J2")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J3")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("J6")->getFont()->setBold(true);

                            $objWorkInventorySheet->SetCellValue("B2", $getFarmDetail[0]->purchase_date);
                            $objWorkInventorySheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("E2", $sheetinventory->inventory_order);
                            $objWorkInventorySheet->getStyle("E2")->getNumberFormat()->setFormatCode('0');
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("K2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("K3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("K6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("K2", $getFarmDetail[0]->purchase_allowance);
                            $objWorkInventorySheet->SetCellValue("K3", $getFarmDetail[0]->purchase_allowance_length);

                            $objWorkInventorySheet->getStyle("A2:B2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A4:B4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("A6:B6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("D2:E2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G2:H2")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G4:H4")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("G6:H6")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("J2:K3")->applyFromArray($styleArray);
                            $objWorkInventorySheet->getStyle("J6:K6")->applyFromArray($styleArray);

                            if (count($getSupplierTaxes) >= 3) {
                                $rowCount = 2;
                            } else {
                                $rowCount = 6;
                            }

                            $startPaymentRow = $rowCount;

                            $taxCellsArray = array();

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('total_payment'));
                            $totalPaymentRow = $rowCount;
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('logistic_cost'));

                            $logiscticRowNumber = "Q$rowCount";

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->logistic_cost);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $logisticCostRow = "$rowCount";
                            $rowCount++;

                            foreach ($getSupplierTaxes as $suppliertax) {

                                $supplierTaxName = "";
                                if ($suppliertax->number_format == 2) {
                                    $supplierTaxName = $suppliertax->tax_name . " (%)";
                                } else {
                                    $supplierTaxName = $suppliertax->tax_name;
                                }
                                $objWorkInventorySheet->SetCellValue("P$rowCount", $supplierTaxName);

                                if ($suppliertax->arithmetic_type == 2) {
                                    $objWorkInventorySheet->getStyle("P$rowCount")->getFont()->getColor()->setRGB("FF0000");
                                }

                                $supplierTaxesArr = json_decode($getFarmDetail[0]->supplier_taxes_array, true);
                                $logisticsTaxesArray = json_decode($getFarmDetail[0]->logistics_taxes_array, true);
                                $serviceTaxesArray = json_decode($getFarmDetail[0]->service_taxes_array, true);

                                if (count($supplierTaxesArr) > 0) {
                                    $formula = "";

                                    foreach ($supplierTaxesArr as $tax) {

                                        if ($tax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $taxval = $tax['taxVal'] * -1;
                                            } else {
                                                $taxval = $tax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q$$$*$taxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q$$$*$taxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q$$$*$taxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q$$$*$taxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($logisticsTaxesArray as $logistictax) {

                                        if ($logistictax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $ltaxval = $logistictax['taxVal'] * -1;
                                            } else {
                                                $ltaxval = $logistictax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q###*$ltaxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q###*$ltaxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q###*$ltaxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q###*$ltaxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }

                                    foreach ($serviceTaxesArray as $servicetax) {

                                        if ($servicetax["taxId"] == $suppliertax->id) {
                                            if ($suppliertax->arithmetic_type == 2) {
                                                $staxval = $servicetax['taxVal'] * -1;
                                            } else {
                                                $staxval = $servicetax['taxVal'];
                                            }
                                            if ($suppliertax->number_format == 2) {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q&&&*$staxval%)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q&&&*$staxval%)";
                                                }
                                            } else {
                                                if ($formula == "") {
                                                    $formula = $formula . "=SUM(Q&&&*$staxval)";
                                                } else {
                                                    $formula = $formula . "+SUM(Q&&&*$staxval)";
                                                }
                                            }

                                            $taxCellsArray[] = array(
                                                "rowCell" => "Q$rowCount",
                                                "formula" => $formula,
                                            );
                                        }
                                    }
                                }

                                $rowCount++;
                            }

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('service_cost'));

                            $serviceRowNumber = "Q$rowCount";

                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->service_cost);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $serviceCostRow = "$rowCount";
                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("P$rowCount", $this->lang->line('adjustment'));
                            $objWorkInventorySheet->SetCellValue("Q$rowCount", $getFarmDetail[0]->adjustment);
                            $objWorkInventorySheet->getStyle("Q$rowCount")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                            $rowCount++;

                            $objWorkInventorySheet->getStyle("P$startPaymentRow:Q$rowCount")->applyFromArray($styleArray);

                            $calcRow = $rowCount;

                            $totalPaymentRow1 = $totalPaymentRow + 1;

                            $objWorkInventorySheet->SetCellValue("Q$totalPaymentRow", "=SUM(Q$totalPaymentRow1:Q$calcRow)");
                            $objWorkInventorySheet->getStyle("Q$totalPaymentRow:Q$calcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                            if (count($taxCellsArray) > 0) {
                                foreach ($taxCellsArray as $taxcell) {
                                    $taxCells = $taxcell["rowCell"];
                                    $objWorkInventorySheet->SetCellValue("$taxCells", str_replace(
                                        array("$$$", "###", "&&&"),
                                        array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                        $taxcell["formula"]
                                    ));
                                }
                            }

                            $sumCalcRow = $rowCount;
                            $headerRow = $rowCount + 1;

                            $rowCount++;

                            $objWorkInventorySheet->SetCellValue("A$headerRow", $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("B$headerRow", $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("C$headerRow", $this->lang->line("circumference"));
                            $objWorkInventorySheet->SetCellValue("D$headerRow", $this->lang->line("length"));
                            $objWorkInventorySheet->SetCellValue("E$headerRow", $this->lang->line("vol_gross") . " - " . $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("F$headerRow", $this->lang->line("vol_net") . " - " . $this->lang->line("reception_title"));
                            $objWorkInventorySheet->SetCellValue("G$headerRow", $this->lang->line("vol_gross") . " - " . $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("H$headerRow", $this->lang->line("vol_net") . " - " . $this->lang->line("farm_title"));
                            $objWorkInventorySheet->SetCellValue("I$headerRow", $this->lang->line("product_type"));
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFont()->setBold(true);
                            $objWorkInventorySheet->getStyle("A$headerRow:I$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data($getFarmDetail[0]->supplier_id, $getFarmDetail[0]->inventory_order, $originId);
                            $farmDataFirstRow = $rowCount;

                            $textShorts = '"' . $this->lang->line("text_shorts") . '"';
                            $textSemi = '"' . $this->lang->line("text_semi") . '"';
                            $textLongs = '"' . $this->lang->line("text_longs") . '"';

                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->circumference_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->length_bought + 0));


                                    $circumferenceAllowance = $getFarmDetail[0]->purchase_allowance;
                                    $lengthAllowance = $getFarmDetail[0]->purchase_allowance_length;

                                    if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 6) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 5 || $getFarmDetail[0]->purchase_unit_id == 7 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 8) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount*0.0796/1000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)*0.0796/1000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")*0.0796/1000000,3)*B$rowCount,0)");
                                        }
                                    } else if ($getFarmDetail[0]->purchase_unit_id == 9) {
                                        if ($originId == 1) {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        } else {
                                            $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(TRUNC(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount),2)*(D$rowCount)/16000000,3)*B$rowCount,0)");
                                            $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(TRUNC(POWER((C$rowCount-" . '$K$2' . "),2)*(D$rowCount-" . '$K$3' . ")/16000000,3)*B$rowCount,0)");
                                        }
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*(D$rowCount)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                        $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                    }

                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IF(D$rowCount<330, $textShorts ,IF(D$rowCount>=600, $textLongs, $textSemi))");

                                    $objWorkInventorySheet->getStyle("E$rowCount:H$rowCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                                    $rowCount++;
                                }
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("C$sumCalcRow", "=IFERROR(TRUNC(SUMPRODUCT(C$farmDataFirstRow:C$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0), 0)");
                            $objWorkInventorySheet->SetCellValue("D$sumCalcRow", "=IFERROR(TRUNC(SUMPRODUCT(D$farmDataFirstRow:D$lastRowFarmData,A$farmDataFirstRow:A$lastRowFarmData)/A$sumCalcRow,0)/100 , 0)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=SUM(E$farmDataFirstRow:E$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("G$sumCalcRow", "=SUM(G$farmDataFirstRow:G$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=SUM(H$farmDataFirstRow:H$lastRowFarmData)");

                            $objWorkInventorySheet->getStyle("E$sumCalcRow:H$sumCalcRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                            $objWorkInventorySheet->SetCellValue("H2", "=E$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H4", "=F$sumCalcRow");
                            if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/B$sumCalcRow*35.315,2),0)");
                            } else {
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");
                            }

                            if ($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                $objWorkInventorySheet->SetCellValue("J4", $this->lang->line("average_girth"));
                                $objWorkInventorySheet->SetCellValue("K4", "=TRUNC(SUMPRODUCT(B$farmDataFirstRow:B$lastRowFarmData,C$farmDataFirstRow:C$lastRowFarmData)/B$sumCalcRow,0)");

                                $objWorkInventorySheet->getStyle("K4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                                $objWorkInventorySheet->getStyle("J4:K4")->getFont()->setBold(true)->getColor()->setRGB("000000");
                                $objWorkInventorySheet->getStyle("J4:K4")->applyFromArray($styleArray);
                            }

                            $objWorkInventorySheet->getStyle("A$sumCalcRow:I$lastRowFarmData")->applyFromArray($styleArray);

                            $objWorkInventorySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                            $objWorkInventorySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14.6);
                            $objWorkInventorySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);

                            //END DATA FEED FARM

                            //PRICE SUMMARY

                            $objWorkInventorySheet->SetCellValue("K$headerRow", $this->lang->line("circumference_range"));
                            $objWorkInventorySheet->mergeCells("K$headerRow:L$headerRow");

                            // if (
                            //     $getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5
                            //     || $getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7
                            // ) {
                                //$objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("volume_reception"));
                                //$objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_farm"));
                                $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("text_shorts"));
                                $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("text_semi"));
                                $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("text_longs"));

                                $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("reception_total_value"));
                                $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("farm_total_value"));

                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFont()->setBold(true);
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->applyFromArray($styleArray);
                            // } else {
                            //     $objWorkInventorySheet->SetCellValue("M$headerRow", $this->lang->line("pieces_reception"));
                            //     $objWorkInventorySheet->SetCellValue("N$headerRow", $this->lang->line("pieces_farm"));
                            //     $objWorkInventorySheet->SetCellValue("O$headerRow", $this->lang->line("volume_per_piece"));

                            //     $objWorkInventorySheet->SetCellValue("P$headerRow", $this->lang->line("reception_total_value"));
                            //     $objWorkInventorySheet->SetCellValue("Q$headerRow", $this->lang->line("farm_total_value"));

                            //     if($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getFont()->setBold(true);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:Q$headerRow")->applyFromArray($styleArray);
                            //     } else {
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getFont()->setBold(true);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            //         $objWorkInventorySheet->getStyle("K$headerRow:W$headerRow")->applyFromArray($styleArray);
                            //     }
                            // }

                            $getInventoryContractPrice = $this->Financemaster_model->get_contract_price_data($getFarmDetail[0]->contract_id, $getFarmDetail[0]->inventory_order);

                            if (count($getInventoryContractPrice) > 0) {

                                $priceSummaryRow = $headerRow + 1;
                                $priceFirstRow = $headerRow + 1;

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("K$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("L$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    }
                                    

                                    // if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$I$' . "$farmDataFirstRow" . ':$I$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    // } else if ($getFarmDetail[0]->purchase_unit_id == 6 || $getFarmDetail[0]->purchase_unit_id == 7) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(" . '$K$4' . ">=L$priceSummaryRow,IF(" . '$K$4' . "<=M$priceSummaryRow," . '$H$' . "$sumCalcRow,0),0)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(" . '$K$4' . ">=L$priceSummaryRow,IF(" . '$K$4' . "<=M$priceSummaryRow," . '$I$' . "$sumCalcRow,0),0)");
                                    // } else if ($getFarmDetail[0]->purchase_unit_id == 8 || $getFarmDetail[0]->purchase_unit_id == 9) {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=IF(" . '$H$6' . ">=L$priceSummaryRow,IF(" . '$H$6' . "<=M$priceSummaryRow," . '$H$' . "$sumCalcRow,0),0)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=IF(" . '$H$6' . ">=L$priceSummaryRow,IF(" . '$H$6' . "<=M$priceSummaryRow," . '$I$' . "$sumCalcRow,0),0)");
                                    // } else {
                                    //     $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow)");
                                    // }

                                    // if ($getFarmDetail[0]->exchange_rate > 0) {
                                    //     $priceRange = $pricedata->pricerange_grade3 * $getFarmDetail[0]->exchange_rate;
                                    // } else {
                                    //     $priceRange = $pricedata->pricerange_grade3;
                                    // }

                                    $priceRangeShorts = $pricedata->pricerange_grade3;
                                    $priceRangeSemi = $pricedata->pricerange_grade_semi;
                                    $priceRangeLongs = $pricedata->pricerange_grade_longs;

                                    // if ($getFarmDetail[0]->purchase_unit_id == 15) {
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeShorts/N$priceSummaryRow");
                                    //     // $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=$priceRangeSemi/O$priceSummaryRow");
                                    //     // $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeLongs/O$priceSummaryRow");
                                    // } else if($getFarmDetail[0]->purchase_unit_id == 3) {
                                    //     $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=$priceRangeShorts");
                                    // } else {
                                        $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", ($priceRangeShorts + 0));
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", ($priceRangeSemi + 0));
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", ($priceRangeLongs + 0));
                                    //}

                                    if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,R10)");
                                        $objWorkInventorySheet->SetCellValue("S$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,S10)");
                                        $objWorkInventorySheet->SetCellValue("T$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,T10)");

                                        $objWorkInventorySheet->SetCellValue("U$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,U10)");
                                        $objWorkInventorySheet->SetCellValue("V$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,V10)");
                                        $objWorkInventorySheet->SetCellValue("W$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,W10)");

                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*R$priceSummaryRow)+(N$priceSummaryRow*S$priceSummaryRow)+(O$priceSummaryRow*T$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(M$priceSummaryRow*U$priceSummaryRow)+(N$priceSummaryRow*V$priceSummaryRow)+(O$priceSummaryRow*W$priceSummaryRow)");

                                        $objWorkInventorySheet->getStyle("M$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("R$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,R10)");
                                        $objWorkInventorySheet->SetCellValue("S$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,S10)");
                                        $objWorkInventorySheet->SetCellValue("T$priceSummaryRow", "=SUMIFS(" . '$F$' . "$farmDataFirstRow" . ':$F$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,T10)");

                                        $objWorkInventorySheet->SetCellValue("U$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,U10)");
                                        $objWorkInventorySheet->SetCellValue("V$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,V10)");
                                        $objWorkInventorySheet->SetCellValue("W$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$K$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$L' . "$priceSummaryRow, I$farmDataFirstRow:I$lastRowFarmData,W10)");

                                        $objWorkInventorySheet->SetCellValue("P$priceSummaryRow", "=(M$priceSummaryRow*R$priceSummaryRow)+(N$priceSummaryRow*S$priceSummaryRow)+(O$priceSummaryRow*T$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("Q$priceSummaryRow", "=(M$priceSummaryRow*U$priceSummaryRow)+(N$priceSummaryRow*V$priceSummaryRow)+(O$priceSummaryRow*W$priceSummaryRow)");

                                        $objWorkInventorySheet->getStyle("M$priceSummaryRow:Q$priceSummaryRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                                    }



                                    $priceSummaryRow++;
                                }

                                $priceLastRow = $priceSummaryRow - 1;

                                if ($getFarmDetail[0]->exchange_rate > 0) {
                                    $objWorkInventorySheet->SetCellValue("P$sumCalcRow", "=SUM(P$priceFirstRow:P$priceLastRow)*K6");
                                    $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)*K6");
                                } else {
                                    $objWorkInventorySheet->SetCellValue("P$sumCalcRow", "=SUM(P$priceFirstRow:P$priceLastRow)");
                                    $objWorkInventorySheet->SetCellValue("Q$sumCalcRow", "=SUM(Q$priceFirstRow:Q$priceLastRow)");
                                }

                                $objWorkInventorySheet->getStyle("P$sumCalcRow:Q$sumCalcRow")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                                // if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                //     //$objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('0');
                                //     $objWorkInventorySheet->getStyle("K$priceFirstRow:Q$priceLastRow")->applyFromArray($styleArray);
                                // } else {
                                //     $objWorkInventorySheet->getStyle("K$priceFirstRow:W$priceLastRow")->applyFromArray($styleArray);
                                //     $objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                // }

                                if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                    $objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('0');
                                } else {
                                    $objWorkInventorySheet->getStyle("R$priceFirstRow:W$priceLastRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                }
                                $objWorkInventorySheet->getStyle("K$priceFirstRow:W$priceLastRow")->applyFromArray($styleArray);


                                // if ($getFarmDetail[0]->purchase_unit_id == 3 || $getFarmDetail[0]->purchase_unit_id == 15) {
                                //     $objWorkInventorySheet->SetCellValue("M$sumCalcRow", "=SUM(M$priceFirstRow:M$priceLastRow)");
                                //     $objWorkInventorySheet->SetCellValue("N$sumCalcRow", "=SUM(N$priceFirstRow:N$priceLastRow)");
                                    
                                //     $objWorkInventorySheet->getStyle("M$sumCalcRow:N$sumCalcRow")->applyFromArray($styleArray);
                                // } else {
                                    $objWorkInventorySheet->SetCellValue("R$sumCalcRow", $this->lang->line("reception_title"));
                                    $objWorkInventorySheet->mergeCells("R$sumCalcRow:T$sumCalcRow");

                                    $objWorkInventorySheet->SetCellValue("U$sumCalcRow", $this->lang->line("farm_title"));
                                    $objWorkInventorySheet->mergeCells("U$sumCalcRow:W$sumCalcRow");

                                    $objWorkInventorySheet->SetCellValue("R$headerRow", $this->lang->line("text_shorts"));
                                    $objWorkInventorySheet->SetCellValue("S$headerRow", $this->lang->line("text_semi"));
                                    $objWorkInventorySheet->SetCellValue("T$headerRow", $this->lang->line("text_longs"));

                                    $objWorkInventorySheet->SetCellValue("U$headerRow", $this->lang->line("text_shorts"));
                                    $objWorkInventorySheet->SetCellValue("V$headerRow", $this->lang->line("text_semi"));
                                    $objWorkInventorySheet->SetCellValue("W$headerRow", $this->lang->line("text_longs"));

                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getFont()->setBold(true);
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("DBEDFF");
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objWorkInventorySheet->getStyle("R$sumCalcRow:W$sumCalcRow")->applyFromArray($styleArray);
                                //}

                                
                            }

                            $objWorkInventorySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(13);
                            $objWorkInventorySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(15);
                            $objWorkInventorySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(17);
                            $objWorkInventorySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(17);

                            //PRICE SUMMARY

                            //SUMMARY DATA FEED

                            $objWorkSummarySheet->SetCellValue("A$summarySheetRowDataCount", $summarySNo);
                            $objWorkSummarySheet->SetCellValue("B$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B2");
                            $objWorkSummarySheet->SetCellValue("C$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E2");
                            $objWorkSummarySheet->SetCellValue("D$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!A$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("E$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!B$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("F$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!D$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("G$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!C$sumCalcRow");

                            $objWorkSummarySheet->SetCellValue("H$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!E$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("I$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!F$sumCalcRow");


                            $objWorkSummarySheet->SetCellValue("J$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!G$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("K$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!H$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("L$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!P$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("M$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!Q$sumCalcRow");
                            $objWorkSummarySheet->SetCellValue("N$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$logiscticRowNumber");
                            $objWorkSummarySheet->SetCellValue("O$summarySheetRowDataCount", "='" . $getFarmDetail[0]->inventory_order . "'!$serviceRowNumber");
                            $objWorkSummarySheet->SetCellValue("P$summarySheetRowDataCount", "=SUM(M$summarySheetRowDataCount:O$summarySheetRowDataCount)");
                            $objWorkSummarySheet->SetCellValue("Q$summarySheetRowDataCount", "=L$summarySheetRowDataCount-M$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("R$summarySheetRowDataCount", "=P$summarySheetRowDataCount");
                            $objWorkSummarySheet->SetCellValue("S$summarySheetRowDataCount", $getFarmDetail[0]->invoice_number);

                            $objWorkSummarySheet->getStyle("H$summarySheetRowDataCount:K$summarySheetRowDataCount")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');


                            $summarySNo++;
                            $summarySheetRowDataCount++;

                            //END SUMMARY DATA FEED
                            $sheetNo++;
                        }
                    }

                    $summarySheetLastRowData = $summarySheetRowDataCount - 1;

                    $objWorkSummarySheet->SetCellValue("E5", "=SUM(E$summarySheetFirstRowData:E$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("F5", "=SUM(F$summarySheetFirstRowData:F$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("I5", "=SUM(I$summarySheetFirstRowData:I$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("J5", "=SUM(J$summarySheetFirstRowData:J$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("E5:J5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("E5:J5")->getFont()->setBold(true);

                    $objWorkSummarySheet->SetCellValue("M5", "=SUM(M$summarySheetFirstRowData:M$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("N5", "=SUM(N$summarySheetFirstRowData:N$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("O5", "=SUM(O$summarySheetFirstRowData:O$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("P5", "=SUM(P$summarySheetFirstRowData:P$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("Q5", "=SUM(Q$summarySheetFirstRowData:Q$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("R5", "=SUM(R$summarySheetFirstRowData:R$summarySheetLastRowData)");
                    $objWorkSummarySheet->SetCellValue("S5", "=SUM(S$summarySheetFirstRowData:S$summarySheetLastRowData)");
                    $objWorkSummarySheet->getStyle("M5:S5")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("M5:S5")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("M5:S5")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->SetCellValue("M4", "=M5/E5");
                    $objWorkSummarySheet->SetCellValue("N4", "=N5/E5");
                    $objWorkSummarySheet->SetCellValue("O4", "=O5/E5");
                    $objWorkSummarySheet->SetCellValue("P4", "=P5/E5");
                    $objWorkSummarySheet->SetCellValue("Q4", "=Q5/E5");
                    $objWorkSummarySheet->SetCellValue("R4", "=R5/E5");
                    $objWorkSummarySheet->SetCellValue("S4", "=S5/E5");
                    $objWorkSummarySheet->getStyle("M4:S4")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("M4:S4")->getFont()->setBold(true);
                    $objWorkSummarySheet->getStyle("M4:S4")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);

                    $objWorkSummarySheet->getStyle("M$summarySheetFirstRowData:S$summarySheetLastRowData")->getNumberFormat()->setFormatCode($getCurrency[0]->currency_excel_format);
                    $objWorkSummarySheet->getStyle("A$summarySheetFirstRowData:T$summarySheetLastRowData")->applyFromArray($styleArray);
                    $objWorkSummarySheet->getStyle("A$summarySheetRowDataCount:T$summarySheetLastRowData")->applyFromArray($styleArray);

                    $objWorkSummarySheet->getStyle("L5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("ED7D31");
                    $objWorkSummarySheet->getStyle("M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("7B7B7B");
                    $objWorkSummarySheet->getStyle("L5:M5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("548235");
                    $objWorkSummarySheet->getStyle("O5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFF2CC");
                    $objWorkSummarySheet->getStyle("L4:R4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("9BC2E6");

                    $objWorkSummarySheet->SetCellValue("D1", "=E5-F5");
                    $objWorkSummarySheet->getStyle("D1")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objWorkSummarySheet->getColumnDimension("A")->setAutoSize(false)->setWidth(9);
                    $objWorkSummarySheet->getColumnDimension("B")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("C")->setAutoSize(false)->setWidth(30);
                    $objWorkSummarySheet->getColumnDimension("D")->setAutoSize(false)->setWidth(16);
                    $objWorkSummarySheet->getColumnDimension("E")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("F")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("G")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("H")->setAutoSize(false)->setWidth(14);
                    $objWorkSummarySheet->getColumnDimension("I")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("J")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("K")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("L")->setAutoSize(false)->setWidth(12);
                    $objWorkSummarySheet->getColumnDimension("M")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("N")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("O")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("P")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("R")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("S")->setAutoSize(false)->setWidth(18);
                    $objWorkSummarySheet->getColumnDimension("T")->setAutoSize(false)->setWidth(14);

                    //END INVENTORY SHEET

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "LiquidationReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/LiquidationReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/LiquidationReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line("no_data_reports");
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
            $Return["error"] = $e->getMessage();
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function get_inventory_order()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "";
            $getInventory = $this->Financemaster_model->get_inventory_by_supplierid($this->input->get('supplier_id'));
            foreach ($getInventory as $inventoryorder) {

                $result = $result . "<option value='" . $inventoryorder->farm_id . "'; ?>" . $inventoryorder->inventory_order . "</option>";
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

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/LiquidationReports/*.xlsx");
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
