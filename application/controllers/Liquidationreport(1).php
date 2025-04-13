<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Liquidationreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Financemaster_model");
        $this->load->model("Master_model");
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
                    $r->total_volume,
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
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
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
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
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
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
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
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
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
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("J2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            $objWorkInventorySheet->SetCellValue("K2", "2");
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

                            //DATA FEED FARM

                            $getFarmDataDetails = $this->Financemaster_model->get_farm_data($supplierId, $getFarmDetail[0]->inventory_order);
                            $farmDataFirstRow = $rowCount;
                            if (count($getFarmDataDetails) > 0) {

                                $rowCount++;
                                $farmDataFirstRow = $rowCount;
                                foreach ($getFarmDataDetails as $farmData) {

                                    $objWorkInventorySheet->SetCellValue("A$rowCount", $farmData->reception);
                                    $objWorkInventorySheet->SetCellValue("B$rowCount", $farmData->farm);
                                    $objWorkInventorySheet->SetCellValue("C$rowCount", ($farmData->circumference_bought + 0));
                                    $objWorkInventorySheet->SetCellValue("D$rowCount", ($farmData->length_bought + 0));

                                    $objWorkInventorySheet->SetCellValue("E$rowCount", "=IFERROR(ROUND(POWER(C$rowCount,2)*D$rowCount/16000000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("F$rowCount", "=IFERROR(ROUND(POWER(C$rowCount-" . '$K$2' . ",2)*(D$rowCount-5)/16000000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("G$rowCount", "=IFERROR(ROUND(POWER(TRUNC(C$rowCount/PI(),0),2)*0.7854*D$rowCount/1000000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("H$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*A$rowCount,0)");
                                    $objWorkInventorySheet->SetCellValue("I$rowCount", "=IFERROR(ROUND(POWER(TRUNC((C$rowCount)/PI(),0)-5,2)*0.7854*(D$rowCount-5)/1000000,3)*B$rowCount,0)");
                                    $rowCount++;
                                }
                            }

                            $lastRowFarmData = $rowCount - 1;

                            $objWorkInventorySheet->SetCellValue("A$sumCalcRow", "=SUM(A$farmDataFirstRow:A$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("B$sumCalcRow", "=SUM(B$farmDataFirstRow:B$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("E$sumCalcRow", "=SUM(E$farmDataFirstRow:E$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("F$sumCalcRow", "=SUM(F$farmDataFirstRow:F$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("G$sumCalcRow", "=SUM(G$farmDataFirstRow:G$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("H$sumCalcRow", "=SUM(H$farmDataFirstRow:H$lastRowFarmData)");
                            $objWorkInventorySheet->SetCellValue("I$sumCalcRow", "=SUM(I$farmDataFirstRow:I$lastRowFarmData)");

                            $objWorkInventorySheet->SetCellValue("H2", "=E$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H4", "=F$sumCalcRow");
                            $objWorkInventorySheet->SetCellValue("H6", "==IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");

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

                                foreach ($getInventoryContractPrice as $pricedata) {

                                    $objWorkInventorySheet->SetCellValue("L$priceSummaryRow", ($pricedata->minrange_grade1 + 0));
                                    $objWorkInventorySheet->SetCellValue("M$priceSummaryRow", ($pricedata->maxrange_grade2 + 0));

                                    if ($getFarmDetail[0]->purchase_unit_id == 4 || $getFarmDetail[0]->purchase_unit_id == 5) {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$H$' . "$farmDataFirstRow" . ':$H$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$I$' . "$farmDataFirstRow" . ':$I$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                    } else {
                                        $objWorkInventorySheet->SetCellValue("N$priceSummaryRow", "=SUMIFS(" . '$A$' . "$farmDataFirstRow" . ':$A$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
                                        $objWorkInventorySheet->SetCellValue("O$priceSummaryRow", "=SUMIFS(" . '$B$' . "$farmDataFirstRow" . ':$B$' . "$lastRowFarmData" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',">="&$L$' . "$priceSummaryRow" . ',$C$' . "$farmDataFirstRow" . ':$C$' . "$lastRowFarmData" . ',"<="&$M' . "$priceSummaryRow)");
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
                    $Return['redirect'] = true;
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
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
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
                            $objWorkInventorySheet->getStyle("E2")->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
                            $objWorkInventorySheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("375623");
                            $objWorkInventorySheet->SetCellValue("B4", $getFarmDetail[0]->plate_number);
                            $objWorkInventorySheet->getStyle("B4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->SetCellValue("B6", $getFarmDetail[0]->supplier_name);
                            $objWorkInventorySheet->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A9D08E");
                            $objWorkInventorySheet->getStyle("J2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FF0000");
                            $objWorkInventorySheet->SetCellValue("K2", "2");
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
                                $objWorkInventorySheet->SetCellValue("H6", "=IFERROR(ROUND(H2/A$sumCalcRow*35.315,2),0)");

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
                    $Return['redirect'] = true;
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
