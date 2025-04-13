<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '1');

defined('BASEPATH') or exit('No direct script access allowed');

class Buyerledger extends MY_Controller
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
        /*Set response header*/
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        /*Final JSON response*/
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("buyer_ledger_header") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_buyerledger";
        if (!empty($session)) {
            $data["buyers"] = $this->Master_model->fetch_buyers_list(0);
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financeledgers/buyerledger", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_ledger_details_by_buyer()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getBuyerLedgerDetails = $this->Financemaster_model->get_ledger_details_by_buyer($this->input->get("buyerId"), $this->input->get("originId"));
            $getBuyerLedgerTransactionDetails = $this->Financemaster_model->get_ledger_transaction_details_by_buyer($this->input->get("buyerId"), $this->input->get("originId"));

            if (count($getBuyerLedgerTransactionDetails) > 0) {

                $fmt = new NumberFormatter("en_US", NumberFormatter::CURRENCY);

                $debitTransactions = array();
                foreach ($getBuyerLedgerTransactionDetails as $debittransaction) {
                    
                    $salesCostAmount = $debittransaction->total_sales_value + 0;
                    $salesCostAmount = str_replace("$", "", $fmt->formatCurrency($salesCostAmount, "USD"));

                    $serviceCostAmount = $debittransaction->total_service_cost + 0;
                    $serviceCostAmount = str_replace("$", "", $fmt->formatCurrency($serviceCostAmount, "USD"));

                    $advanceCostAmount = $debittransaction->total_advance_cost + 0;
                    $advanceCostAmount = str_replace("$", "", $fmt->formatCurrency($advanceCostAmount, "USD"));

                    $claimCostAmount = $debittransaction->total_claim_amount + 0;
                    $claimCostAmount = str_replace("$", "", $fmt->formatCurrency($claimCostAmount, "USD"));

                    $invoiceValueAmount = $debittransaction->total_invoice_value + 0;
                    $invoiceValueAmount = str_replace("$", "", $fmt->formatCurrency($invoiceValueAmount, "USD"));

                    $debitTransaction = array(
                        "saNumber" => $debittransaction->sa_number,
                        "containers" => $debittransaction->total_containers,
                        "pieces" => $debittransaction->total_pieces,
                        "volume" => sprintf("%0.3f", $debittransaction->total_volume + 0),
                        "weight" => sprintf("%0.3f", $debittransaction->total_weight + 0),
                        "salescost" => $salesCostAmount,
                        "servicecost" => $serviceCostAmount,
                        "advancecost" => $advanceCostAmount,
                        "claimcost" => $claimCostAmount,
                        "invoicevalue" => $invoiceValueAmount,
                        "origin" => $debittransaction->origin,
                    );

                    array_push($debitTransactions, $debitTransaction);
                }

                $dataTransaction = array(
                    "totalShipments" => $getBuyerLedgerDetails[0]->total_shipments + 0,
                    "totalContainers" => $getBuyerLedgerDetails[0]->total_containers + 0,
                    "totalVolume" => sprintf("%0.3f", $getBuyerLedgerDetails[0]->total_volume + 0),
                    "totalWeight" => sprintf("%0.3f", $getBuyerLedgerDetails[0]->total_weight + 0),
                    "totalPieces" => $getBuyerLedgerDetails[0]->total_pieces + 0,

                    // "totalSalesCost" => $getBuyerLedgerDetails[0]->total_sales_value + 0,
                    // "totalAdvanceCost" => $getBuyerLedgerDetails[0]->total_advance_cost + 0,
                    // "totalServiceCost" => $getBuyerLedgerDetails[0]->total_service_cost + 0,
                    // "totalClaimCost" => $getBuyerLedgerDetails[0]->total_claim_amount + 0,
                    // "totalInvoiceValue" => $getBuyerLedgerDetails[0]->total_invoice_value + 0,

                    "totalSalesCost" => $fmt->formatCurrency($getBuyerLedgerDetails[0]->total_sales_value + 0, "USD"),
                    "totalAdvanceCost" => $fmt->formatCurrency($getBuyerLedgerDetails[0]->total_advance_cost + 0, "USD"),
                    "totalServiceCost" => $fmt->formatCurrency($getBuyerLedgerDetails[0]->total_service_cost + 0, "USD"),
                    "totalClaimCost" => $fmt->formatCurrency($getBuyerLedgerDetails[0]->total_claim_amount + 0, "USD"),
                    "totalInvoiceValue" => $fmt->formatCurrency($getBuyerLedgerDetails[0]->total_invoice_value + 0, "USD"),

                    "debitTransactions" => $debitTransactions,
                );

                $Return["result"] = $dataTransaction;
                $Return["error"] = "";
                $Return["pages"] = "";
                $Return["redirect"] = false;
                $this->output($Return);
            } else {
                $Return["error"] = $this->lang->line("no_data_available");
                $Return["pages"] = "";
                $Return["redirect"] = false;
                $this->output($Return);
            }
        } else {
            $Return["error"] = "";
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_supplier_ledger()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {
                if ($this->input->get("originid") > 0) {

                    $getSupplierCreditTransaction = $this->Financemaster_model->get_supplier_credit_transactions($this->input->get("supplierid"));
                    $getSupplierDebitTransaction = $this->Financemaster_model->get_supplier_debit_transactions($this->input->get("supplierid"));

                    if (count($getSupplierCreditTransaction) == 0 && count($getSupplierDebitTransaction) == 0) {
                        $Return["error"] = $this->lang->line("no_data_reports");
                        $Return["pages"] = "";
                        $Return["redirect"] = false;
                        $this->output($Return);
                    } else {
                        $this->excel->setActiveSheetIndex(0);
                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle($this->lang->line("supplier_ledger_header"));
                        $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $getSupplierTotalVolume = $this->Financemaster_model->get_total_volume_by_supplier($this->input->get("supplierid"));
                        $getSupplierName = $this->Financemaster_model->get_supplier_name_ledger($this->input->get("supplierid"));

                        $objSheet->SetCellValue('A3', strtoupper($this->lang->line("supplier_name")));
                        $objSheet->SetCellValue('B3', strtoupper($getSupplierName[0]->supplier_name . ' - ' .$getSupplierName[0]->supplier_id));
                        $objSheet->SetCellValue('A4', strtoupper($this->lang->line("total_volume")));
                        $objSheet->getStyle('B4')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->SetCellValue('B4', $getSupplierTotalVolume[0]->total_volume);
                        $objSheet->getStyle("A2")
                            ->getFont()
                            ->setBold(true);
                        $objSheet->getStyle("A3")
                            ->getFont()
                            ->setBold(true);
                        $objSheet->getStyle("A4")
                            ->getFont()
                            ->setBold(true);

                        // AMOUNT
                        $objSheet->SetCellValue('A6', strtoupper($this->lang->line("total_credit")));
                        $objSheet->SetCellValue('A7', strtoupper($this->lang->line("total_debit")));
                        $objSheet->SetCellValue('A8', strtoupper($this->lang->line("total_outstanding")));
                        $objSheet->getStyle("A6")
                            ->getFont()
                            ->setBold(true);
                        $objSheet->getStyle("A7")
                            ->getFont()
                            ->setBold(true);
                        $objSheet->getStyle("A8")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->SetCellValue('A11', $this->lang->line("credits_title"));
                        $objSheet->mergeCells('A11:B11');

                        $objSheet->getStyle("A11")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('A11')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getStyle('A11')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFF2CC');

                        $objSheet->SetCellValue('D11', $this->lang->line("debits_title"));
                        $objSheet->mergeCells('D11:G11');

                        $objSheet->getStyle("D11")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('D11')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getStyle('D11')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFF2CC');

                        $objSheet->getStyle('A2:B4')->applyFromArray($styleArray);
                        $objSheet->getStyle('A6:B8')->applyFromArray($styleArray);
                        $objSheet->getStyle('A11:B11')->applyFromArray($styleArray);
                        $objSheet->getStyle('D11:G11')->applyFromArray($styleArray);

                        $objSheet->getStyle('A12:B12')->applyFromArray($styleArray);
                        $objSheet->getStyle('D12:G12')->applyFromArray($styleArray);

                        // DATA CREDIT

                        $objSheet->SetCellValue('A12', $this->lang->line("costsummary_date"));
                        $objSheet->SetCellValue('B12', $this->lang->line("amount"));

                        $objSheet->getStyle('A12:B12')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('DBEDFF');

                        $objSheet->getStyle("A12:B12")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('A12:B12')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        $dataRow = 13;
                    
                        if (count($getSupplierCreditTransaction) > 0) {
                            foreach ($getSupplierCreditTransaction as $suppliercredit) {

                                //$totalCredit = $totalCredit + $amount;

                                $objSheet->SetCellValue('A' . $dataRow, $suppliercredit->expense_date);
                                $objSheet->SetCellValue('B' . $dataRow, $suppliercredit->amount);

                                $objSheet->getStyle('A' . $dataRow . ':B' . $dataRow)->applyFromArray($styleArray);

                                $objSheet->getStyle('B' . $dataRow)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.000_);_("$"* \(#,##0.000\);_("$"* "-"??_);_(@_)');
                                $dataRow++;
                            }
                        }

                        $creditLastRow = $dataRow - 1;

                        // END DATA CREDIT

                        // DATA DEBIT

                        $objSheet->SetCellValue('D12', $this->lang->line("costsummary_date"));
                        $objSheet->SetCellValue('E12', $this->lang->line("inventory_order"));
                        $objSheet->SetCellValue('F12', $this->lang->line("type_text"));
                        $objSheet->SetCellValue('G12', $this->lang->line("amount"));

                        $objSheet->getStyle('D12:G12')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('DBEDFF');

                        $objSheet->getStyle("D12:G12")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('D12:G12')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        $dataRow = 13;

                        if (count($getSupplierDebitTransaction) > 0) {
                            foreach ($getSupplierDebitTransaction as $supplierdebit) {

                                $objSheet->SetCellValue('D' . $dataRow, $supplierdebit->expense_date);

                                $objSheet->setCellValueExplicit('E' . $dataRow, $supplierdebit->inventory_order, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objSheet->setCellValueExplicit('F' . $dataRow, $supplierdebit->type_name, PHPExcel_Cell_DataType::TYPE_STRING);

                                $objSheet->SetCellValue('G' . $dataRow, $supplierdebit->amount);

                                $objSheet->getStyle('D' . $dataRow . ':G' . $dataRow)->applyFromArray($styleArray);

                                $objSheet->getStyle('G' . $dataRow)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.000_);_("$"* \(#,##0.000\);_("$"* "-"??_);_(@_)');
                                $dataRow++;
                            }
                        }

                        $debitLastRow = $dataRow - 1;

                        // END DATA DEBIT

                        $objSheet->SetCellValue('B6', "=SUM(B13:B$creditLastRow)");
                        $objSheet->SetCellValue('B7', "=SUM(G13:G$debitLastRow)");
                        $objSheet->SetCellValue('B8', "=B6-B7");

                        $objSheet->getStyle("B6:B8")
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.000_);_("$"* \(#,##0.000\);_("$"* "-"??_);_(@_)');

                        // AUTOSIZE
                        $objSheet->getColumnDimension('A')->setAutoSize(true);
                        $objSheet->getColumnDimension('B')->setAutoSize(true);
                        $objSheet->getColumnDimension('D')->setAutoSize(true);
                        $objSheet->getColumnDimension('E')->setAutoSize(true);
                        $objSheet->getColumnDimension('F')->setAutoSize(true);
                        $objSheet->getColumnDimension('G')->setAutoSize(true);


                        $objSheet->getSheetView()->setZoomScale(95);

                        unset($styleArray);
                        $six_digit_random_number = mt_rand(100000, 999999);
                        $month_name = ucfirst(date("dmY"));

                        $filename =  "SupplierLedgerReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

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
                    }
                } else {
                    $Return["error"] = $this->lang->line("common_error");
                    $Return["pages"] = "";
                    $Return["redirect"] = false;
                    $this->output($Return);
                }
            } else {
                $Return["pages"] = "";
                $Return["redirect"] = true;
                $this->output($Return);
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

    public function dialog_ledger_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "deleteledgerconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('tid'),
                    'inputid1' => $this->input->get('sid'),
                    'inputid2' => $this->input->get('cid'),
                    'inputid3' => $this->input->get('oid'),
                    'actionurl' => "supplierledger/dialog_ledger_action",
                    'actiontype' => "deletecredit",
                    'xin_table' => "#xin_table_credits",
                );
                $this->load->view('dialogs/dialog_confirmation_ledger', $data);
            } else if ($this->input->get('type') == "deletecredit") {

                $transactionId = $this->input->get('inputid');
                $supplierId = $this->input->get('inputid1');
                $contractId = $this->input->get('inputid2');
                $oId = $this->input->get('inputid3');

                $farmDelete = $this->Financemaster_model->delete_credits($transactionId, $supplierId, $contractId, $session['user_id']);

                if ($farmDelete) {
                    $Return['result'] = $this->lang->line('data_deleted');
                    $Return['redirect'] = false;
                    $Return['originId'] = $oId;
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
}
