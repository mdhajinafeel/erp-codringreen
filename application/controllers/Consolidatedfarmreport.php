<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Consolidatedfarmreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Farm_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
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
        $data["title"] = $this->lang->line("consolidatedfarmreport") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_financeconsolidatedfarmreport";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financereports/consolidatedfarmreport", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function dialog_farm_report()
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
                        "pagetype" => "downloadfarmreport",
                        "csrf_hash" => $this->security->get_csrf_hash(),
                        "originId" => $originId,
                        "suppliers" => $this->Contract_model->get_suppliers_by_origin($originId),
                    );
                    $this->load->view("financereports/dialog_downloadfarmreport", $data);
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

    public function download_farm_report_bulk()
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

                $this->deletefilesfromfolder();

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $supplierId = $this->input->post("supplierId");
                $fromDate = $this->input->post("fromDate");
                $toDate = $this->input->post("toDate");
                $originId = $this->input->post("originId");
                $productTypeId = 4;

                $fromDate = str_replace('/', '-', $fromDate);
                $fromDate = date("Y-m-d", strtotime($fromDate));

                $toDate = str_replace('/', '-', $toDate);
                $toDate = date("Y-m-d", strtotime($toDate));

                $getInventoryFarmReport = $this->Farm_model->fetch_farm_details_consolidated($originId, $supplierId, $fromDate, $toDate, $productTypeId);

                if (count($getInventoryFarmReport) > 0) {

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $getCurrency = $this->Financemaster_model->get_currency_code($originId);

                    $sheetNo = 0;
                    foreach ($getInventoryFarmReport as $farmreport) {

                        if ($sheetNo == 0) {
                            $this->excel->setActiveSheetIndex(0);
                            $objSheet = $this->excel->getActiveSheet();
                            $objSheet->setTitle($farmreport->base_number);
                            $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
                        } else {

                            $objSheet = $this->excel->createSheet($sheetNo);
                            $objSheet->setTitle(strtoupper($farmreport->base_number));
                            $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
                        }

                        $objSheet->SetCellValue("A1", $this->lang->line("ica_number"));
                        $objSheet->SetCellValue("A2", $this->lang->line("farm_name"));
                        $objSheet->SetCellValue("A3", $this->lang->line("loading_date"));
                        $objSheet->SetCellValue("C1", $this->lang->line("measuremet_system"));
                        $objSheet->getStyle("A1:A3")->getFont()->setBold(true);
                        $objSheet->getStyle("C1:C1")->getFont()->setBold(true);

                        $objSheet->SetCellValue("B1", $farmreport->base_number);
                        $objSheet->SetCellValue("B2", $farmreport->supplier_name);
                        $objSheet->SetCellValue("B3", $farmreport->purchase_date);
                        $objSheet->SetCellValue("D1", $farmreport->purchase_unit);

                        $objSheet->getStyle("A1:D3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->getStyle("A1:D3")->applyFromArray($styleArray);

                        $rowCountDataHeader = 5;
                        $objSheet->SetCellValue("A$rowCountDataHeader", $this->lang->line("circumference"));
                        $objSheet->SetCellValue("B$rowCountDataHeader", $this->lang->line("length"));
                        $objSheet->SetCellValue("C$rowCountDataHeader", $this->lang->line("pieces"));
                        $objSheet->SetCellValue("D$rowCountDataHeader", $this->lang->line("text_volume"));

                        $objSheet->getStyle("A$rowCountDataHeader:D$rowCountDataHeader")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle("A$rowCountDataHeader:D$rowCountDataHeader")->getFont()->setBold(true);
                        $objSheet->getStyle("A$rowCountDataHeader:D$rowCountDataHeader")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("ADD8E6");
                        $objSheet->getStyle("A$rowCountDataHeader:D$rowCountDataHeader")->applyFromArray($styleArray);

                        $getFarmData = $this->Farm_model->get_farm_data_by_inventory_order($farmreport->base_number);
                        if(count($getFarmData) > 0) {

                            $rowCountData = $rowCountDataHeader + 1;
                            $rowCountFirstData = $rowCountData;
                            foreach ($getFarmData as $farmdata) {
                                $objSheet->SetCellValue("A$rowCountData", $farmdata->circumference);
                                $objSheet->SetCellValue("B$rowCountData", $farmdata->length);
                                $objSheet->SetCellValue("C$rowCountData", $farmdata->no_of_pieces);
                                $circumferenceAllowance = $farmdata->purchase_allowance;
                                $lengthAllowance = $farmdata->purchase_allowance_length;

                                if ($farmreport->purchase_unit_id == 4 || $farmreport->purchase_unit_id == 6 || $farmreport->purchase_unit_id == 8) {
                                    $objSheet->SetCellValue("D$rowCountData", "=IFERROR(ROUND(POWER((A$rowCountData-$circumferenceAllowance),2)*(B$rowCountData-$lengthAllowance)*0.0796/1000000,3)*C$rowCountData,0)");
                                } else if ($farmreport->purchase_unit_id == 5 || $farmreport->purchase_unit_id == 7 || $farmreport->purchase_unit_id == 9 || $farmreport->purchase_unit_id == 15) {
                                    $objSheet->SetCellValue("D$rowCountData", "=IFERROR(TRUNC(POWER((A$rowCountData-$circumferenceAllowance),2)*(B$rowCountData-$lengthAllowance)/16000000,3)*C$rowCountData,0)");
                                } else {
                                    $objSheet->SetCellValue("D$rowCountData", "=IFERROR(ROUND(POWER(TRUNC((A$rowCountData)/PI(),0)-5,2)*0.7854*(B$rowCountData-5)/1000000,3)*C$rowCountData,0)");
                                }
                                $objSheet->getStyle("D$rowCountData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                $rowCountData++;
                            }

                            $rowCountCalcRow = $rowCountDataHeader - 1;
                            $lastDataRow = $rowCountData - 1;
                            $objSheet->SetCellValue("C$rowCountCalcRow", "=SUM(C$rowCountFirstData:C$lastDataRow)");
                            $objSheet->SetCellValue("D$rowCountCalcRow", "=SUM(D$rowCountFirstData:D$lastDataRow)");
                        }

                       

                        $sheetNo++;
                    }

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "FarmReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

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
}
