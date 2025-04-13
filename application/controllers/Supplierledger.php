<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Supplierledger extends MY_Controller
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
        $data["title"] = $this->lang->line("supplier_ledger_header") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_suppliercredit";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financeledgers/supplierledger", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_suppliers_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getSuppliers = $this->Master_model->all_suppliers_origin($this->input->get("originid"));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . ($supplier->supplier_name . ' - ' . $supplier->supplier_code) . "</option>";
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

    public function get_currency_code()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = $this->lang->line('amount');
            if ($this->input->get("originid") > 0) {
                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));
                $result = $result . " (" . $getcurrencycode[0]->currency_code . ")";
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

    public function get_ledger_details_by_supplier()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getExpenseLedgerDetails = $this->Financemaster_model->get_ledger_details_by_supplier($this->input->get("supplierid"));

            if (count($getExpenseLedgerDetails) > 0) {

                $totalCredits = 0;
                $totalDebits = 0;
                $totalOutstanding = 0;

                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));
                $getCreditTransactions = $this->Financemaster_model->get_credited_transaction_supplier($this->input->get("supplierid"));
                $getDebitTransactions = $this->Financemaster_model->get_debited_transaction_supplier($this->input->get("supplierid"));

                $totalCredits = $getExpenseLedgerDetails[0]->creditamount;
                $totalDebits = $getExpenseLedgerDetails[0]->debitamount;

                $totalCredits = $totalCredits + 0;
                $totalDebits = $totalDebits + 0;
                $totalOutstanding = $totalCredits - $totalDebits;

                $currencyCode = $getcurrencycode[0]->currency_abbreviation;
                $currencyFormat = $getcurrencycode[0]->currency_format;

                $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
                $totalOutstanding = $fmt->formatCurrency($totalOutstanding, $currencyFormat);
                $totalCredits = $fmt->formatCurrency($totalCredits, $currencyFormat);
                $totalDebits = $fmt->formatCurrency($totalDebits, $currencyFormat);

                $creditTransactions = array();
                foreach ($getCreditTransactions as $credittransaction) {

                    $amount = $credittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    // $creditTransaction = array(
                    //     "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcreditamount" data-toggle="modal" data-target=".edit-modal-data" data-transaction_id="' . $credittransaction->id . '"><span class="fas fa-pencil"></span></button></span>
                    //   <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecreditamount" data-toggle="modal" data-target=".delete-modal-data" data-transaction_id="' . $credittransaction->id . '"><span class="fas fa-trash"></span></button></span>',
                    //     "transactionDate" => $credittransaction->expense_date,
                    //     "amount" => $amount,
                    //     "fullName" => $credittransaction->fullname
                    // );
                    
                    $creditTransaction = array(
                        "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecreditamount" data-toggle="modal" data-target=".delete-modal-data" data-transaction_id="' . $credittransaction->id . '" data-supplier_id="' . $credittransaction->supplier_id . '" data-contract_id="' . $credittransaction->contract_id . '" data-origin_id = "'.$this->input->get("originid").'"><span class="fas fa-trash"></span></button></span>',
                        "transactionDate" => $credittransaction->expense_date,
                        "amount" => $amount,
                        "fullName" => $credittransaction->fullname
                    );


                    array_push($creditTransactions, $creditTransaction);
                }

                $debitTransactions = array();
                foreach ($getDebitTransactions as $debittransaction) {

                    $amount = $debittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    $debitTransaction = array(
                        "transactionDate" => $debittransaction->expense_date,
                        "inventoryOrder" => $debittransaction->inventory_order,
                        "amount" => $amount,
                        "transactionType" => $debittransaction->type_name,
                    );

                    array_push($debitTransactions, $debitTransaction);
                }

                $dataTransaction = array(
                    "totalCredits" => $totalCredits,
                    "totalDebits" => $totalDebits,
                    "totalOutstanding" => $totalOutstanding,
                    "creditTransactions" => $creditTransactions,
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
    
    public function generate_all_supplier_ledger()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {
                if ($this->input->get("originid") > 0) {

                    $getAllSupplierLedger = $this->Financemaster_model->get_all_supplier_ledger_by_origin($this->input->get("originid"));

                    if (count($getAllSupplierLedger) == 0) {
                        $Return["error"] = $this->lang->line("no_data_reports");
                        $Return["pages"] = "";
                        $Return["redirect"] = false;
                        $this->output($Return);
                    } else {
                        $this->excel->setActiveSheetIndex(0);
                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle($this->lang->line("viewledger_title"));
                        $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $objSheet->SetCellValue('A1', "S. No");
                        $objSheet->SetCellValue('B1', "Supplier Name");
                        $objSheet->SetCellValue('C1', "Supplier Code");
                        $objSheet->SetCellValue('D1', "Credits");
                        $objSheet->SetCellValue('E1', "Debits");
                        $objSheet->SetCellValue('F1', "Outstanding");

                        $objSheet->getStyle("A1:F1")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter('A1:F1');

                        // HEADER ALIGNMENT
                        $objSheet->getStyle("A1:F1")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        // AUTOSIZE
                        $objSheet->getColumnDimension('A')->setAutoSize(true);
                        $objSheet->getColumnDimension('B')->setAutoSize(true);
                        $objSheet->getColumnDimension('C')->setAutoSize(true);
                        $objSheet->getColumnDimension('D')->setAutoSize(true);
                        $objSheet->getColumnDimension('E')->setAutoSize(true);
                        $objSheet->getColumnDimension('F')->setAutoSize(true);

                        $objSheet->getStyle('A1:F1')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $objSheet->getStyle('A1:F1')->applyFromArray($styleArray);

                        $i = 1;
                        $rowCountData = 2;

                        foreach($getAllSupplierLedger as $ledgerdata) {

                            $objSheet->SetCellValue('A' . $rowCountData, $i);
                            $objSheet->SetCellValue('B' . $rowCountData, $ledgerdata->supplier_name);
                            $objSheet->SetCellValue('C' . $rowCountData, $ledgerdata->supplier_code);
                            $objSheet->SetCellValue('D' . $rowCountData, $ledgerdata->creditamount);
                            $objSheet->SetCellValue('E' . $rowCountData, $ledgerdata->debitamount);


                            $outStadingAmount = $ledgerdata->creditamount - $ledgerdata->debitamount;
                            $objSheet->SetCellValue('F' . $rowCountData, $outStadingAmount);

                            if($this->input->get("originid") == 1) {
                                $objSheet->getStyle('D' . $rowCountData . ':F' . $rowCountData)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("COP"* #,##0.000_);_("COP"* \(#,##0.000\);_("COP"* "-"??_);_(@_)');
                            } else {
                                $objSheet->getStyle('D' . $rowCountData . ':F' . $rowCountData)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("USD"* #,##0.000_);_("USD"* \(#,##0.000\);_("USD"* "-"??_);_(@_)');
                            }
                            

                            $objSheet->getStyle('A' . $rowCountData . ':F' . $rowCountData)->applyFromArray($styleArray);

                            $i++;
                            $rowCountData++;
                        }

                        $objSheet->getSheetView()->setZoomScale(95);

                        unset($styleArray);
                        $six_digit_random_number = mt_rand(100000, 999999);
                        $month_name = ucfirst(date("dmY"));

                        $filename =  "LedgerReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

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
