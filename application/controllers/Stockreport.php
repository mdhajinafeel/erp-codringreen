<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Stockreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
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
        $data["title"] = $this->lang->line("stockreport_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_financereport";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financereports/stockreport", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_stock_details()
    {
        try {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getStockReports = $this->Financemaster_model->get_stock_transactions($this->input->get("originid"));

            if (count($getStockReports) > 0) {

                $totalInventory = 0;
                $totalPieces = 0;
                $totalCosts = 0;
                $totalVolume = 0;

                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));

                $currencyCode = $getcurrencycode[0]->currency_abbreviation;
                $currencyFormat = $getcurrencycode[0]->currency_format;

                $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);

                $stockTransactions = array();
                foreach ($getStockReports as $stockreport) {

                    $totalInventory = $totalInventory + 1;
                    $totalPieces = $totalPieces + $stockreport->remaining_stock;
                    $totalVolume = $totalVolume + $stockreport->total_volume;

                    $costPerPiece = $stockreport->totalcost / $stockreport->total_pieces;
                    $costOfWood = round($stockreport->remaining_stock * $costPerPiece, 2);

                    $totalCosts = $totalCosts + $costOfWood;

                    $costOfWood = $fmt->formatCurrency($costOfWood, $currencyFormat);
                    $costOfWood = str_replace('$', '', $costOfWood);

                    $stockTransaction = array(
                        "inventoryOrder" => $stockreport->inventory_order,
                        "supplierName" => utf8_encode($stockreport->supplier_name),
                        "remaningStock" => $stockreport->remaining_stock,
                        "remaningVolume" => round($stockreport->total_volume,3),
                        "costOfWood" => $costOfWood
                    );

                    array_push($stockTransactions, $stockTransaction);
                }
                
                $totalCosts = $fmt->formatCurrency($totalCosts, $currencyFormat);

                $dataTransaction = array(
                    "totalInventory" => $totalInventory,
                    "totalPieces" => $totalPieces,
                    "totalCosts" => $totalCosts,
                    "totalVolume" => round($totalVolume,3),
                    "stockTransactions" => $stockTransactions,
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
        } catch (Exception $e) {
            $Return['error'] = $this->lang->line("error_reports");
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_stock_report()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getStockReports = $this->Financemaster_model->get_stock_transactions($this->input->get("originid"));
                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));

                if (count($getStockReports) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("stockreport_title"));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->mergeCells("B2:C2");
                    $objSheet->SetCellValue("B2", $this->lang->line("stock_details"));
                    $objSheet->SetCellValue("B3", $this->lang->line("total_inventory"));
                    $objSheet->SetCellValue("B4", $this->lang->line("total_no_of_pieces"));
                    $objSheet->SetCellValue("B5", $this->lang->line("total_volume"));
                    $objSheet->SetCellValue("B6", $this->lang->line("total_cost"));

                    $objSheet->getStyle("B2:B6")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("B2")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("B2:C6")->applyFromArray($styleArray);

                    $objSheet->getStyle("B2")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB("D8E4BC");

                    $objSheet->SetCellValue("A8", $this->lang->line("s_no"));
                    $objSheet->SetCellValue("B8", $this->lang->line("inventory_order"));
                    $objSheet->SetCellValue("C8", $this->lang->line("supplier_name"));
                    $objSheet->SetCellValue("D8", $this->lang->line("remaining_pieces"));
                    $objSheet->SetCellValue("E8", $this->lang->line("remaining_volume"));
                    $objSheet->SetCellValue("F8", $this->lang->line("cost_of_wood"));

                    $objSheet->getStyle("A8:F8")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->setAutoFilter("A8:F8");

                    // HEADER ALIGNMENT
                    $objSheet->getStyle("A8:F8")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getStyle("A8:F8")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('add8e6');

                    $objSheet->getStyle("A8:F8")->applyFromArray($styleArray);

                    $i = 1;
                    $rowCountData = 9;

                    foreach ($getStockReports as $stock) {

                        $objSheet->SetCellValue("A$rowCountData", $i);
                        $objSheet->SetCellValue("B$rowCountData", $stock->inventory_order);
                        $objSheet->SetCellValue("C$rowCountData", $stock->supplier_name);
                        $objSheet->SetCellValue("D$rowCountData", $stock->remaining_stock);
                        $objSheet->SetCellValue("E$rowCountData", $stock->total_volume);
                        $costPerPiece = $stock->totalcost / $stock->total_pieces;
                        $costOfWood = round($stock->remaining_stock * $costPerPiece, 2);

                        $objSheet->SetCellValue("F$rowCountData", $costOfWood);

                        $objSheet->getStyle("F$rowCountData")
                            ->getNumberFormat()
                            ->setFormatCode($getcurrencycode[0]->currency_excel_format);

                        $i++;
                        $rowCountData++;
                    }

                    $rowCountData = $rowCountData - 1;

                    $objSheet->getStyle("A9:F$rowCountData")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("C3", "=SUBTOTAL(2,A8:A$rowCountData)");
                    $objSheet->SetCellValue("C4", "=SUBTOTAL(9,D8:D$rowCountData)");
                    $objSheet->SetCellValue("C5", "=SUBTOTAL(9,E8:E$rowCountData)");
                    $objSheet->SetCellValue("C6", "=SUBTOTAL(9,F8:F$rowCountData)");

                    $objSheet->getStyle("C6")
                        ->getNumberFormat()
                        ->setFormatCode($getcurrencycode[0]->currency_excel_format);

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                    $objSheet->getColumnDimension("D")->setAutoSize(true);
                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                    $objSheet->getColumnDimension("F")->setAutoSize(true);

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "StockReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/StockReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/StockReports/" . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
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
            $Return['error'] = $this->lang->line("error_reports");
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

        $files = glob(FCPATH . "reports/StockReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}