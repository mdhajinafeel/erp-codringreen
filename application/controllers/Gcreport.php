<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Gcreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Financemaster_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
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
        $data["title"] = $this->lang->line("gcreport") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_gcreport";
        if (!empty($session)) {

            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();

            $data["subview"] = $this->load->view("financereports/gcreports", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function fetch_buyers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getBuyers = $this->Master_model->fetch_buyers_list($this->input->get('originid'));
                foreach ($getBuyers as $buyer) {
                    $result = $result . "<option value='" . $buyer->id . "'>" . $buyer->buyer_name . "</option>";
                }
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

    public function generate_report()
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

                $originId = $this->input->post("originid");
                $buyerId = $this->input->post("buyername");

                $getGCReportData = $this->Financemaster_model->get_gc_report_data($originId, $buyerId);

                if (count($getGCReportData) > 0) {

                    //START EXCEL
                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line("gcreport"));

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

                    if ($originId == 3) {

                        //HEADING

                        $objSheet->SetCellValue("A4", strtoupper($this->lang->line("sa_no")));
                        $objSheet->SetCellValue("B4", strtoupper($this->lang->line("sa_date")));
                        $objSheet->SetCellValue("C4", strtoupper($this->lang->line("seller_name_invoice")));
                        $objSheet->SetCellValue("D4", strtoupper($this->lang->line("buyer_name_invoice")));
                        $objSheet->SetCellValue("E4", strtoupper($this->lang->line("booking_bl")));
                        $objSheet->SetCellValue("F4", strtoupper($this->lang->line("liner")));
                        $objSheet->SetCellValue("G4", strtoupper($this->lang->line("vessel")));
                        $objSheet->SetCellValue("H4", strtoupper($this->lang->line("pod")));
                        $objSheet->SetCellValue("I4", strtoupper($this->lang->line("CONTAINER #")));
                        $objSheet->SetCellValue("J4", strtoupper($this->lang->line("seal")));
                        $objSheet->SetCellValue("K4", strtoupper($this->lang->line("text_mt")));
                        $objSheet->SetCellValue("L4", strtoupper($this->lang->line("net_lbs")));
                        $objSheet->SetCellValue("M4", strtoupper($this->lang->line("diameter")));
                        $objSheet->SetCellValue("N4", strtoupper($this->lang->line("diameter_inch")));
                        $objSheet->SetCellValue("O4", strtoupper($this->lang->line("length")));
                        $objSheet->SetCellValue("P4", strtoupper($this->lang->line("pieces")));
                        $objSheet->SetCellValue("Q4", strtoupper($this->lang->line("unit_price")));
                        $objSheet->SetCellValue("R4", strtoupper($this->lang->line("purchase_value")));
                        $objSheet->SetCellValue("S4", strtoupper($this->lang->line("unit_price")));
                        $objSheet->SetCellValue("T4", strtoupper($this->lang->line("sales_value")));
                        $objSheet->SetCellValue("U4", strtoupper($this->lang->line("text_gc")));

                        $objSheet->getStyle("A4:U4")->getFont()->setBold(true);
                        $objSheet->getStyle("A4:U4")->getFont()->getColor()->setRGB("000000");
                        $objSheet->setAutoFilter("A4:U4");
                        $objSheet->getStyle("A4:U4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objSheet->getStyle("A4:U4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E2EFD9");

                        $dataRow = 4;
                        $startDataRow = 4;
                        $lastSANumber = '';
                        $emptyText = '"1"';
                        foreach ($getGCReportData as $gcreport) {
                            $dataRow++;

                            if ($lastSANumber != '' && $lastSANumber != $gcreport->sa_number) {
                                $dataRow++;
                            }
                            $lastSANumber = $gcreport->sa_number;

                            $objSheet->SetCellValue("A$dataRow", $gcreport->sa_number);

                            $saDate = new DateTime($gcreport->shipped_date);
                            $objSheet->SetCellValue("B$dataRow", PHPExcel_Shared_Date::PHPToExcel($saDate));
                            $objSheet->getStyle("B$dataRow")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                            $objSheet->SetCellValue("C$dataRow", $gcreport->seller_name);
                            $objSheet->SetCellValue("D$dataRow", $gcreport->buyer_name);
                            $objSheet->SetCellValue("E$dataRow", $gcreport->bl_no);
                            $objSheet->SetCellValue("F$dataRow", $gcreport->shipping_line);
                            $objSheet->SetCellValue("G$dataRow", $gcreport->vessel_name);
                            $objSheet->SetCellValue("H$dataRow", $gcreport->pod_name);
                            $objSheet->SetCellValue("I$dataRow", $gcreport->container_number);
                            $objSheet->SetCellValue("J$dataRow", $gcreport->seal_number);
                            $objSheet->SetCellValue("K$dataRow", $gcreport->total_net_volume + 0);
                            $objSheet->SetCellValue("L$dataRow", $gcreport->total_gross_volume + 0);
                            $objSheet->SetCellValue("M$dataRow", html_entity_decode($gcreport->diameter_text, ENT_QUOTES, 'UTF-8'));
                            $objSheet->SetCellValue("N$dataRow", "=VALUE(IF(LEFT(M$dataRow,1)=" . $emptyText . ",LEFT(M$dataRow,2),LEFT(M$dataRow,1)))");
                            $objSheet->SetCellValue("O$dataRow", html_entity_decode($gcreport->length_text, ENT_QUOTES, 'UTF-8'));
                            $objSheet->SetCellValue("P$dataRow", $gcreport->total_pieces + 0);
                            $objSheet->SetCellValue("Q$dataRow", $gcreport->container_price + 0);
                            $objSheet->SetCellValue("R$dataRow", "=K$dataRow*Q$dataRow");
                            $objSheet->SetCellValue("S$dataRow", $gcreport->base_price + 0);
                            $objSheet->SetCellValue("T$dataRow", "=K$dataRow*S$dataRow");
                            $objSheet->SetCellValue("U$dataRow", "=T$dataRow-R$dataRow");
                        }

                        $startDataRow++;
                        $objSheet->getStyle("K$startDataRow:K$dataRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet->getStyle("L$startDataRow:L$dataRow")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("P$startDataRow:P$dataRow")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("N$startDataRow:N$dataRow")->getNumberFormat()->setFormatCode('0.0');
                        $objSheet->getStyle("Q$startDataRow:U$dataRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objSheet->getStyle("A$startDataRow:U$dataRow")->applyFromArray($styleArray);

                        $objSheet->getStyle("A4:U$dataRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->freezePane("B5");

                        $objSheet->getColumnDimension("A")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("B")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("C")->setAutoSize(false)->setWidth(25);
                        $objSheet->getColumnDimension("D")->setAutoSize(false)->setWidth(25);
                        $objSheet->getColumnDimension("E")->setAutoSize(false)->setWidth(30);
                        $objSheet->getColumnDimension("F")->setAutoSize(false)->setWidth(34);
                        $objSheet->getColumnDimension("G")->setAutoSize(false)->setWidth(50);
                        $objSheet->getColumnDimension("H")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("I")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("J")->setAutoSize(false)->setWidth(13);
                        $objSheet->getColumnDimension("K")->setAutoSize(false)->setWidth(11);
                        $objSheet->getColumnDimension("L")->setAutoSize(false)->setWidth(12);
                        $objSheet->getColumnDimension("M")->setAutoSize(false)->setWidth(13);
                        $objSheet->getColumnDimension("N")->setAutoSize(false)->setWidth(16);
                        $objSheet->getColumnDimension("O")->setAutoSize(false)->setWidth(11);
                        $objSheet->getColumnDimension("P")->setAutoSize(false)->setWidth(10);
                        $objSheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("R")->setAutoSize(false)->setWidth(20);
                        $objSheet->getColumnDimension("S")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("T")->setAutoSize(false)->setWidth(20);
                        $objSheet->getColumnDimension("U")->setAutoSize(false)->setWidth(12);

                        //END HEADING
                    } else {

                        $objSheet->SetCellValue("A4", strtoupper($this->lang->line("sa_no")));
                        $objSheet->SetCellValue("B4", strtoupper($this->lang->line("sa_date")));
                        $objSheet->SetCellValue("C4", strtoupper($this->lang->line("seller_name_invoice")));
                        $objSheet->SetCellValue("D4", strtoupper($this->lang->line("buyer_name_invoice")));
                        $objSheet->SetCellValue("E4", strtoupper($this->lang->line("booking_bl")));
                        $objSheet->SetCellValue("F4", strtoupper($this->lang->line("liner")));
                        $objSheet->SetCellValue("G4", strtoupper($this->lang->line("vessel")));
                        $objSheet->SetCellValue("H4", strtoupper($this->lang->line("pod")));
                        $objSheet->SetCellValue("I4", strtoupper($this->lang->line("CONTAINER #")));
                        $objSheet->SetCellValue("J4", strtoupper($this->lang->line("product_type")));
                        $objSheet->SetCellValue("K4", strtoupper($this->lang->line("circ_allowance")));
                        $objSheet->SetCellValue("L4", strtoupper($this->lang->line("length_allowance")));
                        $objSheet->SetCellValue("M4", strtoupper($this->lang->line("average_girth")));
                        $objSheet->SetCellValue("N4", strtoupper($this->lang->line("average_length")));
                        $objSheet->SetCellValue("O4", strtoupper($this->lang->line("pieces")));
                        $objSheet->SetCellValue("P4", strtoupper($this->lang->line("gross_volume")));
                        $objSheet->SetCellValue("Q4", strtoupper($this->lang->line("net_volume")));
                        $objSheet->SetCellValue("R4", strtoupper($this->lang->line("text_cft")));
                        $objSheet->SetCellValue("S4", strtoupper($this->lang->line("unit_price")));
                        $objSheet->SetCellValue("T4", strtoupper($this->lang->line("purchase_value")));
                        $objSheet->SetCellValue("U4", strtoupper($this->lang->line("baseprice")));
                        $objSheet->SetCellValue("V4", strtoupper($this->lang->line("unit_price")));
                        $objSheet->SetCellValue("W4", strtoupper($this->lang->line("sales_value")));
                        $objSheet->SetCellValue("X4", strtoupper($this->lang->line("text_gc")));

                        $objSheet->getStyle("A4:X4")->getFont()->setBold(true);
                        $objSheet->getStyle("A4:X4")->getFont()->getColor()->setRGB("000000");
                        $objSheet->setAutoFilter("A4:X4");
                        $objSheet->getStyle("A4:X4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objSheet->getStyle("A4:X4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E2EFD9");

                        $dataRow = 4;
                        $startDataRow = 4;
                        $lastSANumber = '';
                        $emptyText = '"1"';
                        foreach ($getGCReportData as $gcreport) {
                            $dataRow++;

                            if ($lastSANumber != '' && $lastSANumber != $gcreport->sa_number) {
                                $dataRow++;
                            }
                            $lastSANumber = $gcreport->sa_number;

                            $objSheet->SetCellValue("A$dataRow", $gcreport->sa_number);
                            $saDate = new DateTime($gcreport->shipped_date);
                            $objSheet->SetCellValue("B$dataRow", PHPExcel_Shared_Date::PHPToExcel($saDate));
                            $objSheet->getStyle("B$dataRow")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                            $objSheet->SetCellValue("C$dataRow", $gcreport->seller_name);
                            $objSheet->SetCellValue("D$dataRow", $gcreport->buyer_name);
                            $objSheet->SetCellValue("E$dataRow", $gcreport->bl_no);
                            $objSheet->SetCellValue("F$dataRow", $gcreport->shipping_line);
                            $objSheet->SetCellValue("G$dataRow", $gcreport->vessel_name);
                            $objSheet->SetCellValue("H$dataRow", $gcreport->pod_name);
                            $objSheet->SetCellValue("I$dataRow", $gcreport->container_number);
                            $objSheet->SetCellValue("J$dataRow", $gcreport->product_type);
                            $objSheet->SetCellValue("K$dataRow", $gcreport->circumference_allowance_export + 0);
                            $objSheet->SetCellValue("L$dataRow", $gcreport->length_allowance_export + 0);
                            $objSheet->SetCellValue("M$dataRow", $gcreport->avg_circumference + 0);
                            $objSheet->SetCellValue("N$dataRow", $gcreport->avg_length + 0);
                            $objSheet->SetCellValue("O$dataRow", $gcreport->total_pieces + 0);
                            $objSheet->SetCellValue("P$dataRow", $gcreport->total_gross_volume + 0);
                            $objSheet->SetCellValue("Q$dataRow", $gcreport->total_net_volume + 0);
                            $objSheet->SetCellValue("R$dataRow", $gcreport->gross_cft + 0);
                            $objSheet->SetCellValue("S$dataRow", "0");
                            $objSheet->SetCellValue("T$dataRow", "0");
                            $objSheet->SetCellValue("U$dataRow", $gcreport->base_price + 0);
                            $objSheet->SetCellValue("V$dataRow", $gcreport->sales_price + 0);
                            $objSheet->SetCellValue("W$dataRow", "=Q$dataRow*V$dataRow");
                            $objSheet->SetCellValue("X$dataRow", "=W$dataRow-T$dataRow");
                        }

                        $startDataRow++;
                        $objSheet->getStyle("P$startDataRow:Q$dataRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet->getStyle("K$startDataRow:M$dataRow")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("O$startDataRow:O$dataRow")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("N$startDataRow:N$dataRow")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("R$startDataRow:R$dataRow")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("S$startDataRow:X$dataRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objSheet->getStyle("A$startDataRow:X$dataRow")->applyFromArray($styleArray);

                        $objSheet->getStyle("A4:X$dataRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->freezePane("B5");

                        $objSheet->getColumnDimension("A")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("B")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("C")->setAutoSize(false)->setWidth(26);
                        $objSheet->getColumnDimension("D")->setAutoSize(false)->setWidth(25);
                        $objSheet->getColumnDimension("E")->setAutoSize(false)->setWidth(26);
                        $objSheet->getColumnDimension("F")->setAutoSize(false)->setWidth(15);
                        $objSheet->getColumnDimension("G")->setAutoSize(false)->setWidth(35);
                        $objSheet->getColumnDimension("H")->setAutoSize(false)->setWidth(20);
                        $objSheet->getColumnDimension("I")->setAutoSize(false)->setWidth(20);
                        $objSheet->getColumnDimension("J")->setAutoSize(false)->setWidth(13);
                        $objSheet->getColumnDimension("K")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("L")->setAutoSize(false)->setWidth(18);
                        $objSheet->getColumnDimension("M")->setAutoSize(false)->setWidth(13);
                        $objSheet->getColumnDimension("N")->setAutoSize(false)->setWidth(16);
                        $objSheet->getColumnDimension("O")->setAutoSize(false)->setWidth(11);
                        $objSheet->getColumnDimension("P")->setAutoSize(false)->setWidth(22);
                        $objSheet->getColumnDimension("Q")->setAutoSize(false)->setWidth(22);
                        $objSheet->getColumnDimension("R")->setAutoSize(false)->setWidth(10);
                        $objSheet->getColumnDimension("S")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("T")->setAutoSize(false)->setWidth(16);
                        $objSheet->getColumnDimension("U")->setAutoSize(false)->setWidth(12);
                        $objSheet->getColumnDimension("V")->setAutoSize(false)->setWidth(14);
                        $objSheet->getColumnDimension("W")->setAutoSize(false)->setWidth(16);
                        $objSheet->getColumnDimension("X")->setAutoSize(false)->setWidth(12);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "GCReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

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
                    $Return["error"] = $this->lang->line("no_data_available");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["iserror"] = true;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
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
}
