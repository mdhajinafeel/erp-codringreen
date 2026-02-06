<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Expenseledgerreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Financemaster_model");
        $this->load->model("Expense_model");
        $this->load->library('excel');
        $this->load->library('zip');
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
        $data["title"] = $this->lang->line("ledgerreport_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_expenseledger";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("expensetrackers/ledgerreport", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_expense_ledger_users()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getExpenseUsers = $this->Expense_model->get_expense_ledger_users($this->input->get("originid"));
                foreach ($getExpenseUsers as $expsenseuser) {
                    $result = $result . "<option value='" . $expsenseuser->user_id . "'>" . $expsenseuser->fullname . "</option>";
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

    public function get_account_heads()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getAccountHeads = $this->Financemaster_model->all_account_heads($this->input->get("originid"));
                foreach ($getAccountHeads as $accountHead) {
                    $result = $result . "<option value='" . $accountHead->id . "'>" . $accountHead->name_in_ledger . "</option>";
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

    public function get_credit_transactions()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0 && $this->input->get("userid") > 0) {
                $getCreditTransactions = $this->Expense_model->fetch_credit_transactions($this->input->get("originid"), $this->input->get("userid"));
                foreach ($getCreditTransactions as $creditTransaction) {
                    $result = $result . "<option value='" . $creditTransaction->transaction_id . "'>" . $creditTransaction->concept_general . ' --- ' . $creditTransaction->transaction_display_id . "</option>";
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

    public function generate_expense_ledger_reports()
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

                $originId = $this->input->post("originId");
                $userId = $this->input->post("userId");
                $fromDate = $this->input->post("fromDate");
                $toDate = $this->input->post("toDate");
                $conceptGeneral = $this->input->post("conceptGeneral");
                $accountHead = $this->input->post("accountHead");

                $ledgerReportUserDetails = $this->Expense_model->get_ledger_report_details($originId, $userId);
                $getCurrencyCode = $this->Financemaster_model->get_currency_code($originId);
                if (count($ledgerReportUserDetails) == 1) {

                    $this->deletefilesfromfolder();

                    $conceptGeneralName = '';
                    $conceptGeneralDisplayId = '';
                    $conceptGeneralAmount = 0;
                    $ledgerCreditTransactionDetails = $this->Expense_model->get_credit_transaction_details($conceptGeneral, $originId, $userId);

                    if (count($ledgerCreditTransactionDetails) == 1) {
                        $conceptGeneralName = $ledgerCreditTransactionDetails[0]->concept_general;
                        $conceptGeneralDisplayId = $ledgerCreditTransactionDetails[0]->transaction_display_id;
                        $conceptGeneralAmount = $ledgerCreditTransactionDetails[0]->amount;
                    }

                    $fetchExpenseReportDetails = $this->Expense_model->fetch_expense_report_details($originId, $userId, $fromDate, $toDate, $conceptGeneral, $accountHead);

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('report'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    $thinStyleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $thinRightStyleArray = array(
                        'borders' => array(
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $rightStyleArray = array(
                        'borders' => array(
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    $leftStyleArray = array(
                        'borders' => array(
                            'left' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    $topStyleArray = array(
                        'borders' => array(
                            'top' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    $bottomStyleArray = array(
                        'borders' => array(
                            'bottom' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    // START IMAGE CELL

                    $objSheet->mergeCells("A3:A6");
                    $objSheet->getStyle("A3:A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A3:A6")->applyFromArray($styleArray);

                    $gdImage = imagecreatefrompng("./assets/img/iconz/logo_cgr.png");

                    $drawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setImageResource($gdImage);
                    $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);

                    $drawing->setHeight(80);
                    $drawing->setCoordinates('A3');
                    $drawing->setOffsetX(65);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($objSheet);

                    // END IMAGE CELL

                    // START TOP HEADER TEXT CELL

                    $objSheet->mergeCells("B3:E6");
                    $objSheet->getStyle("B3:E6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("B3:E6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("B3:E6")->getAlignment()->setWrapText(true);
                    $objSheet->getStyle("B3:E6")->applyFromArray($styleArray);

                    $richText = new PHPExcel_RichText();
                    $line1 = $richText->createTextRun("CODRIN GREEN\n");
                    $line1->getFont()->setName('Cambria')->setSize(14)->setBold(true);
                    $line2 = $richText->createTextRun("LEGALIZACION GASTOS");
                    $line2->getFont()->setName('Cambria')->setSize(14)->setBold(false);
                    $objSheet->setCellValue("B3", $richText);

                    // END TOP HEADER TEXT CELL

                    // SIDE TEXT CELLS

                    $today_date = ucfirst(date("d/m/Y"));

                    $objSheet->setCellValue("A7", $this->lang->line('name'))->getStyle("A7")->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                    $objSheet->getStyle("A7")->applyFromArray($rightStyleArray);
                    $objSheet->setCellValue("B7", $ledgerReportUserDetails[0]->fullname)->getStyle("B7")->getFont()->setName('Calibri')->setSize(11)->setBold(false);
                    $objSheet->mergeCells("B7:E7");
                    $objSheet->getStyle("A7")->applyFromArray($leftStyleArray);
                    $objSheet->getStyle("B7:E7")->applyFromArray($rightStyleArray);
                    $objSheet->getStyle("B7:E7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                    $objSheet->setCellValue("A8", $this->lang->line('expense_report_date'))->getStyle("A8")->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                    $objSheet->getStyle("A8")->applyFromArray($rightStyleArray);

                    $todayDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $today_date));
                    $objSheet->setCellValue("B8", $todayDateValue);
                    $objSheet->getStyle("B8")->getNumberFormat()->setFormatCode('dd"/"mm"/"yyyy');
                    $objSheet->getStyle("B8")->getFont()->setName('Calibri')->setSize(11)->setBold(false);
                    $objSheet->mergeCells("B8:E8");
                    $objSheet->getStyle("A8")->applyFromArray($leftStyleArray);
                    $objSheet->getStyle("B8:E8")->applyFromArray($rightStyleArray);
                    $objSheet->getStyle("B8:E8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                    $objSheet->setCellValue("A9", $this->lang->line('concept_general'))->getStyle("A9")->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                    $objSheet->getStyle("A9")->applyFromArray($rightStyleArray);
                    if ($conceptGeneralDisplayId == '') {
                        $conceptGeneralDisplayId = '';
                    } else {
                        $conceptGeneralDisplayId = ' / ' . $conceptGeneralDisplayId;
                    }
                    $objSheet->setCellValue("B9", $conceptGeneralName . '' . $conceptGeneralDisplayId)->getStyle("B9")->getFont()->setName('Calibri')->setSize(11)->setBold(false);
                    $objSheet->mergeCells("B9:E9");
                    $objSheet->getStyle("A9")->applyFromArray($leftStyleArray);
                    $objSheet->getStyle("B9:E9")->applyFromArray($rightStyleArray);
                    $objSheet->getStyle("B9:E9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                    // END SIDE TEXT CELLS

                    // START TABLE HEADERS

                    $objSheet->mergeCells("A10:A11");
                    $objSheet->setCellValue("A10", strtoupper($this->lang->line('FECHA')))->getStyle("A10")->getFont()->setName('Arial')->setSize(11)->setItalic(true)->setBold(true);
                    $objSheet->getStyle("A10:A11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A10:A11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("A10:A11")->applyFromArray($styleArray);

                    $objSheet->mergeCells("B10:B11");
                    $objSheet->setCellValue("B10", strtoupper($this->lang->line('BENEFICIARIO')))->getStyle("B10")->getFont()->setName('Arial')->setItalic(true)->setSize(11)->setBold(true);
                    $objSheet->getStyle("B10:B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("B10:B11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("B10:B11")->applyFromArray($styleArray);

                    $objSheet->mergeCells("C10:C11");
                    $objSheet->setCellValue("C10", strtoupper($this->lang->line('IDENTIFICACION')))->getStyle("C10")->getFont()->setName('Arial')->setItalic(true)->setSize(11)->setBold(true);
                    $objSheet->getStyle("C10:C11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("C10:C11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("C10:C11")->applyFromArray($styleArray);

                    $objSheet->mergeCells("D10:D11");
                    $objSheet->setCellValue("D10", strtoupper($this->lang->line('CONCEPTO')))->getStyle("D10")->getFont()->setName('Arial')->setItalic(true)->setSize(11)->setBold(true);
                    $objSheet->getStyle("D10:D11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("D10:D11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("D10:D11")->applyFromArray($styleArray);

                    $objSheet->mergeCells("E10:E11");
                    $objSheet->setCellValue("E10", strtoupper($this->lang->line('VALOR')))->getStyle("E10")->getFont()->setName('Arial')->setItalic(true)->setSize(11)->setBold(true);
                    $objSheet->getStyle("E10:E11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("E10:E11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("E10:E11")->applyFromArray($styleArray);

                    // END TABLE HEADERS

                    // START TABLE DATA

                    // DATA ROWS
                    $rowCount = 11;
                    if (count($fetchExpenseReportDetails) > 0) {
                        foreach ($fetchExpenseReportDetails as $expenseReportData) {
                            $rowCount++;

                            $expenseDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $expenseReportData->expense_date));
                            $objSheet->setCellValue("A" . $rowCount, $expenseDateValue);
                            $objSheet->getStyle("A" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                            $objSheet->getStyle("A" . $rowCount)->getNumberFormat()->setFormatCode('dd"/"mm"/"yyyy');
                            $objSheet->setCellValue("B" . $rowCount, $expenseReportData->beneficiary_name)->getStyle("B" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                            $objSheet->setCellValue("C" . $rowCount, $expenseReportData->document_number)->getStyle("C" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                            $objSheet->setCellValue("D" . $rowCount, $expenseReportData->name_in_ledger)->getStyle("D" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                            $objSheet->setCellValue("E" . $rowCount, $expenseReportData->amount)->getStyle("E" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);

                            $objSheet->getStyle("E" . $rowCount)->getNumberFormat()->setFormatCode($getCurrencyCode[0]->currency_excel_format);

                            $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($thinStyleArray);
                            $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($rightStyleArray);
                            $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($leftStyleArray);
                        }
                    } else {
                        $rowCount++;

                        $objSheet->setCellValue("A" . $rowCount, "")->getStyle("A" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                        $objSheet->setCellValue("B" . $rowCount, "")->getStyle("B" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                        $objSheet->setCellValue("C" . $rowCount, "")->getStyle("C" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                        $objSheet->setCellValue("D" . $rowCount, "")->getStyle("D" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);
                        $objSheet->setCellValue("E" . $rowCount, "")->getStyle("E" . $rowCount)->getFont()->setName('Arial')->setSize(12)->setBold(false);

                        $objSheet->getStyle("E" . $rowCount)->getNumberFormat()->setFormatCode($getCurrencyCode[0]->currency_excel_format);
                        $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($thinStyleArray);
                        $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($rightStyleArray);
                        $objSheet->getStyle("A" . $rowCount . ":E" . $rowCount)->applyFromArray($leftStyleArray);
                    }

                    // END TABLE DATA

                    // FINAL TOTAL ROW

                    $rowCount++;
                    $lastDataRow = $rowCount - 1;

                    // TOTAL
                    $summaryRowCount = $rowCount;
                    $objSheet->mergeCells("A" . $rowCount . ":D" . $rowCount);
                    $objSheet->setCellValue("A" . $rowCount, strtoupper($this->lang->line('total')))->getStyle("A" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($topStyleArray);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($thinRightStyleArray);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($leftStyleArray);

                    $objSheet->setCellValue("E" . $rowCount, "=SUM(E12:E" . $lastDataRow . ")")->getStyle("E" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("E" . $rowCount)->getNumberFormat()->setFormatCode($getCurrencyCode[0]->currency_excel_format);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($topStyleArray);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($rightStyleArray);

                    // CREDIT
                    $rowCount++;
                    $advanceReceived = $rowCount;
                    $objSheet->mergeCells("A" . $rowCount . ":D" . $rowCount);
                    $objSheet->setCellValue("A" . $rowCount, strtoupper($this->lang->line('advance_received')))->getStyle("A" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($thinStyleArray);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($leftStyleArray);

                    $objSheet->setCellValue("E" . $rowCount, $conceptGeneralAmount)->getStyle("E" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("E" . $rowCount)->getNumberFormat()->setFormatCode($getCurrencyCode[0]->currency_excel_format);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($thinStyleArray);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($rightStyleArray);

                    // BALANCE
                    $rowCount++;
                    $objSheet->mergeCells("A" . $rowCount . ":D" . $rowCount);
                    $objSheet->setCellValue("A" . $rowCount, strtoupper($this->lang->line('balance')))->getStyle("A" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($thinStyleArray);
                    $objSheet->getStyle("A" . $rowCount . ":D" . $rowCount)->applyFromArray($leftStyleArray);

                    $objSheet->setCellValue("E" . $rowCount, "=+E$advanceReceived-E$summaryRowCount")->getStyle("E" . $rowCount)->getFont()->setName('Arial')->setSize(11)->setBold(true);
                    $objSheet->getStyle("E" . $rowCount)->getNumberFormat()->setFormatCode($getCurrencyCode[0]->currency_excel_format);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($thinStyleArray);
                    $objSheet->getStyle("E" . $rowCount)->applyFromArray($rightStyleArray);

                    // FINAL TOTAL ROW

                    // START FOOTER NOTES

                    $rowCount++;
                    $footerStartRow = $rowCount;
                    $objSheet->mergeCells("A" . $footerStartRow . ":B" . ($footerStartRow + 4));
                    $objSheet->setCellValue("A" . $footerStartRow, $this->lang->line('employee_signature'))->getStyle("A" . $footerStartRow)->getFont()->setName('Arial')->setSize(11)->setItalic(true)->setBold(true);
                    $objSheet->getStyle("A" . $footerStartRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $objSheet->getStyle("A" . $footerStartRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                    $objSheet->getStyle("A" . $footerStartRow . ":B" . ($footerStartRow + 4))->applyFromArray($styleArray);

                    $objSheet->mergeCells("C" . $footerStartRow . ":E" . ($footerStartRow + 4));
                    $objSheet->getStyle("C" . $footerStartRow . ":E" . ($footerStartRow + 4))->applyFromArray($styleArray);

                    // END FOOTER NOTES

                    // SET WIDTH OF COLUMNS

                    $objSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(40);
                    $objSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(24);
                    $objSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(23);
                    $objSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(45);
                    $objSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(28);

                    // END SET WIDTH OF COLUMNS

                    //SET HEIGHT OF ROWS

                    $objSheet->getRowDimension(3)->setRowHeight(22);

                    //END SET HEIGHT OF ROWS

                    $objSheet->getSheetView()->setZoomScale(85);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ExpenseLedgerReports_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ExpenseReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ExpenseReports/' . $filename;
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

    public function download_expense_ledger_files()
    {
        try {

            $session = $this->session->userdata("fullname");

            $Return = array("result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => "");

            if (empty($session)) {
                redirect("/logout");
            }

            $this->deletefilesfromfolder();
            $Return['csrf_hash'] = $this->security->get_csrf_hash();

            $originId       = $this->input->post("originId");
            $userId         = $this->input->post("userId");
            $fromDate       = $this->input->post("fromDate");
            $toDate         = $this->input->post("toDate");
            $conceptGeneral = $this->input->post("conceptGeneral");
            $accountHead    = $this->input->post("accountHead");

            // 📁 Public download folder
            $downloadDir = FCPATH . 'uploads/downloadedphotos/';
            if (!is_dir($downloadDir)) {
                mkdir($downloadDir, 0777, true);
            }

            // 📦 ZIP name
            $zipFileName = 'facturas_' . time() . '.zip';
            $zipFullPath = $downloadDir . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                $Return["error"] = $this->lang->line('file_creation_failed');
                $this->output($Return);
                return;
            }

            // 🔍 Fetch data
            $fetchExpenseReportDetails =
                $this->Expense_model->fetch_expense_report_details($originId, $userId, $fromDate, $toDate, $conceptGeneral, $accountHead);

            if (count($fetchExpenseReportDetails) == 0) {
                $zip->close();
                unlink($zipFullPath);

                $Return["error"] = $this->lang->line('no_data_reports');
                $this->output($Return);
                return;
            }

            // ➕ Add files
            foreach ($fetchExpenseReportDetails as $row) {

                if (empty($row->expense_uploaded_image)) {
                    continue;
                }

                $fileName = basename(parse_url($row->expense_uploaded_image, PHP_URL_PATH));
                $localPath = FCPATH . 'uploads/expensedocuments/' . $fileName;

                if (file_exists($localPath)) {
                    // Prevent duplicate names in ZIP
                    $zip->addFile($localPath, uniqid() . '_' . $fileName);
                }
            }

            $zip->close();

            // ✅ Success response
            $Return['result'] = $this->lang->line('file_downloaded');
            $Return['downloadfile'] = site_url('uploads/downloadedphotos/' . $zipFileName);
            $Return['redirect'] = false;

            $this->output($Return);

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

        $files = glob(FCPATH . "reports/ExpenseReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "uploads/downloadedphotos/*.zip");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
