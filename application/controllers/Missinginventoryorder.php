<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Missinginventoryorder extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Inventoryreports_model");
        $this->load->model("Settings_model");
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
        $data['title'] = $this->lang->line('missinginventory_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }
        $data['path_url'] = 'cgr_inventoryreports';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("inventoryreports/missing_inventoryorder", $data, TRUE);
            $this->load->view('layout/layout_main', $data);
        } else {
            redirect("/logout");
        }
    }

    public function missed_inventory_farms()
    {
        $session = $this->session->userdata('fullname');

        if (empty($session)) {
            redirect("/logout");
        } else {

            $draw = intval($this->input->get("draw"));
            $originid = intval($this->input->get("originid"));
            $missedinventories = $this->Inventoryreports_model->fetch_missing_inventory_farm($originid);

            $data = array();

            foreach ($missedinventories as $r) {

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $r->inventory_order,
                    $r->supplier_name,
                    $product,
                    $r->receiveddate,
                    ($r->scanned_pcs + 0),
                    $r->origin,
                    ucwords(strtolower($r->uploadedby)),
                );
            }

            $output = array(
                "draw" => $draw,
                "data" => $data
            );
            echo json_encode($output);
            exit();
        }
    }

    public function missed_inventory_receptions()
    {
        $session = $this->session->userdata('fullname');

        if (empty($session)) {
            redirect("/logout");
        } else {

            $draw = intval($this->input->get("draw"));
            $originid = intval($this->input->get("originid"));
            $missedinventories = $this->Inventoryreports_model->fetch_missing_inventory_reception($originid);

            $data = array();

            foreach ($missedinventories as $r) {

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $r->salvoconducto,
                    $r->supplier_name,
                    $product,
                    $r->received_date,
                    ($r->scanned_pcs + 0),
                    $r->origin,
                    ucwords(strtolower($r->uploadedby)),
                );
            }

            $output = array(
                "draw" => $draw,
                "data" => $data
            );
            echo json_encode($output);
            exit();
        }
    }

    public function generate_missing_inventory_order_report()
    {
        try {
            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $originid = intval($this->input->get("originid"));
                $fetchFarmsData = $this->Inventoryreports_model->fetch_missing_inventory_farm($originid);
                $fetchReceptionData = $this->Inventoryreports_model->fetch_missing_inventory_reception($originid);

                if (count($fetchFarmsData) == 0 && count($fetchReceptionData) == 0) {
                    $Return['error'] = $this->lang->line("no_data_reports");
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {

                    $isSheetCreated = 0;
                    if (count($fetchFarmsData) > 0) {
                        $this->excel->setActiveSheetIndex(0);
                        $isSheetCreated = 1;

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle(strtoupper($this->lang->line("farmreception")));
                        $objSheet->getParent()->getDefaultStyle()
                            ->getFont()
                            ->setName('Calibri')
                            ->setSize(11);

                        $objSheet->SetCellValue("A1", $this->lang->line("inventory_order"));
                        $objSheet->SetCellValue("B1", $this->lang->line("supplier_name"));
                        $objSheet->SetCellValue("C1", $this->lang->line("supplier_code"));
                        $objSheet->SetCellValue("D1", $this->lang->line("product"));
                        $objSheet->SetCellValue("E1", $this->lang->line("purchase_date"));
                        $objSheet->SetCellValue("F1", $this->lang->line("total_no_of_pieces"));
                        $objSheet->SetCellValue("G1", $this->lang->line("origin"));
                        $objSheet->SetCellValue("H1", $this->lang->line("uploaded_by"));

                        $objSheet->getStyle("A1:H1")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle("A1:H1")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("add8e6");

                        $objSheet->getStyle("A1:H1")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $rowDataCount = 2;
                        foreach ($fetchFarmsData as $farmdata) {
                            $objSheet->SetCellValue("A$rowDataCount", $farmdata->inventory_order);
                            $objSheet->SetCellValue("B$rowDataCount", $farmdata->supplier_name);
                            $objSheet->SetCellValue("C$rowDataCount", $farmdata->supplier_code);
                            $objSheet->SetCellValue("D$rowDataCount", $farmdata->product_name . " - " . $this->lang->line($farmdata->product_type_name));

                            $receivedDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $farmdata->receiveddate));
                            $objSheet->setCellValue("E$rowDataCount", $receivedDateValue);
                            $objSheet->getStyle("E$rowDataCount")
                                ->getNumberFormat()
                                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                            $objSheet->SetCellValue("F$rowDataCount", $farmdata->scanned_pcs);
                            $objSheet->SetCellValue("G$rowDataCount", $farmdata->origin);
                            $objSheet->SetCellValue("H$rowDataCount", $farmdata->uploadedby);


                            $rowDataCount++;
                        }
                        $rowDataCount = $rowDataCount - 1;
                        $objSheet->getStyle("A1:H$rowDataCount")->applyFromArray($styleArray);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                        $objSheet->getColumnDimension("G")->setAutoSize(true);
                        $objSheet->getColumnDimension("H")->setAutoSize(true);

                        $objSheet->getSheetView()->setZoomScale(95);
                    }

                    if (count($fetchReceptionData) > 0) {
                        if ($isSheetCreated == 1) {
                            $this->excel->createSheet(1);
                            $this->excel->setActiveSheetIndex(1);
                        } else {
                            $this->excel->setActiveSheetIndex(0);

                            $styleArray = array(
                                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN
                                    )
                                )
                            );
                        }

                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle(strtoupper($this->lang->line("receptionfarm")));
                        $objSheet->getParent()->getDefaultStyle()
                            ->getFont()
                            ->setName('Calibri')
                            ->setSize(11);

                        $objSheet->SetCellValue("A1", $this->lang->line("inventory_order"));
                        $objSheet->SetCellValue("B1", $this->lang->line("supplier_name"));
                        $objSheet->SetCellValue("C1", $this->lang->line("supplier_code"));
                        $objSheet->SetCellValue("D1", $this->lang->line("product"));
                        $objSheet->SetCellValue("E1", $this->lang->line("received_date"));
                        $objSheet->SetCellValue("F1", $this->lang->line("total_no_of_pieces"));
                        $objSheet->SetCellValue("G1", $this->lang->line("origin"));
                        $objSheet->SetCellValue("H1", $this->lang->line("uploaded_by"));

                        $objSheet->getStyle("A1:H1")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle("A1:H1")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("add8e6");

                        $objSheet->getStyle("A1:H1")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $rowDataCount = 2;
                        foreach ($fetchReceptionData as $receptiondata) {
                            $objSheet->SetCellValue("A$rowDataCount", $receptiondata->salvoconducto);
                            $objSheet->SetCellValue("B$rowDataCount", $receptiondata->supplier_name);
                            $objSheet->SetCellValue("C$rowDataCount", $receptiondata->supplier_code);
                            $objSheet->SetCellValue("D$rowDataCount", $receptiondata->product_name . " - " . $this->lang->line($receptiondata->product_type_name));

                            $receivedDateValue = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $receptiondata->received_date));
                            $objSheet->setCellValue("E$rowDataCount", $receivedDateValue);
                            $objSheet->getStyle("E$rowDataCount")
                                ->getNumberFormat()
                                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

                            $objSheet->SetCellValue("F$rowDataCount", $receptiondata->scanned_pcs);
                            $objSheet->SetCellValue("G$rowDataCount", $receptiondata->origin);
                            $objSheet->SetCellValue("H$rowDataCount", $receptiondata->uploadedby);


                            $rowDataCount++;
                        }
                        $rowDataCount = $rowDataCount - 1;
                        $objSheet->getStyle("A1:H$rowDataCount")->applyFromArray($styleArray);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                        $objSheet->getColumnDimension("G")->setAutoSize(true);
                        $objSheet->getColumnDimension("H")->setAutoSize(true);

                        $objSheet->getSheetView()->setZoomScale(95);
                    }

                    unset($styleArray);
                    
                    $this->excel->setActiveSheetIndex(0);

                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'MissingInventoryReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/MissingInventoryReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/MissingInventoryReports/" . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
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
            $Return['error'] = $e->getMessage(); // $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
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
        
        $files = glob(FCPATH . "reports/MissingInventoryReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
