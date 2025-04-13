<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Readyforexport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Export_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Dispatch_model");
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
        $data["title"] = $this->lang->line("readyforexport_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_exportmanagement";
        if (!empty($session)) {
            $applicable_origins = $session["applicable_origins"];
            $data["products"] = $this->Master_model->get_product_byorigin($applicable_origins[0]->id);
            $data["producttypes"] = $this->Master_model->get_product_type();

            $data["subview"] = $this->load->view("export/exportready", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function exportready_list()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            $exportContainers = $this->Export_model->all_export_containers($this->input->get("originid"), $this->input->get("pid"), $this->input->get("tid"));

            $data = array();

            foreach ($exportContainers as $r) {
                $editDispatch = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("download_excel") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloaddispatch" data-toggle="modal" data-target=".download-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-download"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewdispatch" data-toggle="modal" data-target=".view-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-eye"></span></button></span>';

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $editDispatch,
                    $r->container_number,
                    $r->shipping_line,
                    $product,
                    $r->warehouse_name,
                    ($r->total_pieces + 0),
                    ($r->total_volume + 0),
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

    public function dialog_export_action()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->get("type") == "downloaddispatch") {

                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $this->generate_dispatch_report($dispatchId, $containerNumber);
            } else if ($this->input->get("type") == "viewdispatch") {
                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $getDispatchDetails = $this->Dispatch_model->get_dispatch_details($dispatchId, $containerNumber);
                $getWH = $this->Master_model->get_warehouse_by_origin($getDispatchDetails[0]->origin_id);
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($getDispatchDetails[0]->origin_id);

                if ($getDispatchDetails[0]->is_special_uploaded == 1) {
                    $getDispatchDetails[0]->is_special_uploaded = $this->lang->line("pieces");
                } else {
                    $getDispatchDetails[0]->is_special_uploaded = $this->lang->line("qr_code");
                }

                $data = array(
                    "pageheading" => $this->lang->line("dispatch_details"),
                    "pagetype" => "view",
                    "dispatchid" => $dispatchId,
                    "containernumber" => $containerNumber,
                    "warehouses" => $getWH,
                    "shippinglines" => $getShippingLines,
                    "dispatch_details" => $getDispatchDetails,
                    "dispatch_submit" => "dispatches/add"
                );
                $this->load->view("export/dialog_view_dispatch", $data);
            }
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_dispatch_report($dispatchId, $containerNumber)
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => ""
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getDispatchDetails = $this->Dispatch_model->get_dispatch_details($dispatchId, $containerNumber);
                $getDispatchDataDetails = $this->Dispatch_model->get_dispatch_data_details($dispatchId, $containerNumber);

                if (count($getDispatchDetails) == 1 && count($getDispatchDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle(strtoupper($containerNumber));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue("A2", $this->lang->line("container_number"));
                    $objSheet->SetCellValue("A3", $this->lang->line("shipping_line"));
                    $objSheet->SetCellValue("A4", $this->lang->line("product"));
                    $objSheet->SetCellValue("A5", $this->lang->line("dispatch_date"));
                    $objSheet->SetCellValue("A6", $this->lang->line("origin"));
                    $objSheet->SetCellValue("C2", $this->lang->line("warehouse"));
                    $objSheet->SetCellValue("C3", $this->lang->line("total_no_of_pieces"));
                    $objSheet->SetCellValue("C4", $this->lang->line("total_volume"));

                    $objSheet->SetCellValue("B2", $getDispatchDetails[0]->container_number);
                    $objSheet->SetCellValue("B3", $getDispatchDetails[0]->shipping_line);
                    $objSheet->SetCellValue("B4", $getDispatchDetails[0]->product_name . ' - ' . $this->lang->line($getDispatchDetails[0]->product_type_name));
                    $objSheet->SetCellValue("B5", $getDispatchDetails[0]->dispatch_date);
                    $objSheet->SetCellValue("B6", $getDispatchDetails[0]->origin);
                    $objSheet->SetCellValue("D2", $getDispatchDetails[0]->warehouse_name);

                    $objSheet->getStyle("A2:A6")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("C2:C6")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                    $objSheet->getColumnDimension("D")->setAutoSize(true);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle("A2:D6")->applyFromArray($styleArray);

                    $rowCount = 8;

                    $getFormulae = $this->Master_model->get_formulae_by_measurementsystem(2, $getDispatchDetails[0]->origin_id);

                    $grossVolumeFormula = "";
                    $netVolumeFormula = "";
                    foreach ($getFormulae as $formula) {

                        if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                            $grossVolumeFormula = str_replace(
                                array('pow', 'round'),
                                array("POWER", "ROUND"),
                                $formula->calculation_formula
                            );
                        }

                        if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                            $netVolumeFormula = str_replace(
                                array('pow', 'round'),
                                array("POWER", "ROUND"),
                                $formula->calculation_formula
                            );
                        }
                    }

                    if ($getDispatchDetails[0]->product_type_id == 1 || $getDispatchDetails[0]->product_type_id == 3) {
                    } else if ($getDispatchDetails[0]->product_type_id == 2 || $getDispatchDetails[0]->product_type_id == 4) {

                        $objSheet->SetCellValue("A$rowCount", $this->lang->line("circumference"));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line("length"));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line("pieces"));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line("inventory_order"));
                        $objSheet->SetCellValue("E$rowCount", $this->lang->line("gross_volume"));
                        $objSheet->SetCellValue("F$rowCount", $this->lang->line("net_volume"));

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:F$rowCount");

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getStyle("A$rowCount:F$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB("add8e6");



                        $rowCountData = 9;
                        foreach ($getDispatchDataDetails as $dispatchdata) {

                            $grossVolumeFormulae = str_replace(
                                array('$l', '$c'),
                                array("B$rowCountData", "A$rowCountData"),
                                $grossVolumeFormula
                            );

                            $netVolumeFormulae = str_replace(
                                array('$l', '$c'),
                                array("B$rowCountData", "A$rowCountData"),
                                $netVolumeFormula
                            );

                            $objSheet->SetCellValue("A$rowCountData", ($dispatchdata->circumference_bought + 0));
                            $objSheet->SetCellValue("B$rowCountData", ($dispatchdata->length_bought + 0));
                            $objSheet->SetCellValue("C$rowCountData", ($dispatchdata->dispatch_pieces + 0));
                            $objSheet->SetCellValue("D$rowCountData", $dispatchdata->salvoconducto);
                            $objSheet->SetCellValue("E$rowCountData", "=$grossVolumeFormulae*C$rowCountData");
                            $objSheet->SetCellValue("F$rowCountData", "=$netVolumeFormulae*C$rowCountData");

                            $rowCountData++;
                        }

                        $objSheet->SetCellValue("D3", "=SUM(C$rowCount:C$rowCountData)");
                        $objSheet->SetCellValue("D4", "=SUM(F$rowCount:F$rowCountData)");

                        $rowCountData = $rowCountData - 1;

                        $objSheet->getStyle("A$rowCount:F$rowCountData")->applyFromArray($styleArray);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);
                        $objSheet->getColumnDimension("E")->setAutoSize(true);
                        $objSheet->getColumnDimension("F")->setAutoSize(true);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "DispatchReport_" . $containerNumber . "_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/DispatchReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return["error"] = "";
                    $Return["result"] = site_url() . "reports/DispatchReports/" . $filename;
                    $Return["successmessage"] = $this->lang->line("report_downloaded");
                    if ($Return["result"] != "") {
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
            $Return["error"] = $e->getMessage(); // $this->lang->line("error_reports');
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/DispatchReports/*.xlsx");
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

    public function debug_to_console($data)
    {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}
