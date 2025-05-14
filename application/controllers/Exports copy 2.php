<?php

// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
// ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Exports extends MY_Controller
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
        $data["title"] = $this->lang->line("export_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_exports";
        if (!empty($session)) {
            $applicable_origins = $session["applicable_origins"];
            $data["shippinglines"] = $this->Master_model->get_shippinglines_by_origin($applicable_origins[0]->id);
            $data["producttypes"] = $this->Master_model->get_product_type();
            $data["csrf_hash"] =  $this->security->get_csrf_hash();

            $data["subview"] = $this->load->view("export/export_list", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function export_list()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            $exportContainers = $this->Export_model->all_exports($this->input->get("originid"), $this->input->get("tid"), $this->input->get("sid"));

            $data = array();

            foreach ($exportContainers as $r) {
                if ($this->input->get("originid") == 1) {
                    $actionExport = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("document_view") . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="viewexportdocuments" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-file"></span></button></span>
                    <span style="margin-left:1px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexport" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-pencil"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deleteexport" data-toggle="modal" data-target=".download-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-trash"></span></button></span>';
                } else {
                    $actionExport = '<span style="margin-left:1px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '/' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexport" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-pencil"></span></button></span>
                    <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deleteexport" data-toggle="modal" data-target=".download-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-trash"></span></button></span>';
                }

                $product_type = $this->lang->line($r->product_type_name);

                $data[] = array(
                    $actionExport,
                    $r->sa_number,
                    $product_type,
                    $r->shipping_line,
                    $r->pol_name,
                    $r->pod_name,
                    ($r->total_containers + 0),
                    ($r->total_pieces + 0),
                    ($r->total_net_volume + 0),
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

            if ($this->input->post("type") == "downloaddispatch") {

                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("cn");

                $this->generate_dispatch_report($dispatchId, $containerNumber);
            } else if ($this->input->post("type") == "viewexport") {
                $exportId = $this->input->post("eid");
                $saNumber = $this->input->post("sn");

                $getExportDetails = $this->Export_model->get_export_details_by_id($exportId, $saNumber);
                //$getWH = $this->Master_model->get_warehouse_by_origin($getDispatchDetails[0]->origin_id);
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($getExportDetails[0]->origin_id);

                $data = array(
                    "pageheading" => $this->lang->line("export_details"),
                    "pagetype" => "view",
                    "exportid" => $exportId,
                    "sanumber" => $saNumber,
                    "exportpod" => $this->Master_model->get_export_pod(),
                    "shippinglines" => $getShippingLines,
                    "export_details" => $getExportDetails,
                    "originid" => $getExportDetails[0]->origin_id,
                    "dispatchids" => $getExportDetails[0]->dispatchids,
                    "product_type_id" => $getExportDetails[0]->product_type_id,
                    "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($getExportDetails[0]->origin_id, $getExportDetails[0]->product_type_id),
                    "formsubmit" => "exports/update",
                    "csrfhash" => $this->security->get_csrf_hash(),
                );
                $this->load->view("export/dialog_view_export", $data);
            } else if ($this->input->post("type") == "viewexportdocuments") {
                $exportId = $this->input->post("eid");
                $saNumber = $this->input->post("sn");

                $getExportDetails = $this->Export_model->get_export_details_by_id($exportId, $saNumber);
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($getExportDetails[0]->origin_id);

                //CUSTOMS
                $getExportDocumentsCustoms = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 1);
                if (!empty($getExportDocumentsCustoms[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsCustoms[0]->invoice_date);
                    $getExportDocumentsCustoms[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsCustomsContainers = array();
                if (count($getExportDocumentsCustoms) == 1) {
                    $getExportDocumentsCustomsContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsCustoms[0]->export_id, 1, $getExportDocumentsCustoms[0]->id);
                }

                //TRANSPORT
                $getExportDocumentsTransport = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 2);
                if (!empty($getExportDocumentsTransport[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsTransport[0]->invoice_date);
                    $getExportDocumentsTransport[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsTransportContainers = array();
                if (count($getExportDocumentsTransport) == 1) {
                    $getExportDocumentsTransportContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsTransport[0]->export_id, 2, $getExportDocumentsTransport[0]->id);
                }

                //PORT
                $getExportDocumentsPort = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 3);
                if (!empty($getExportDocumentsPort[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsPort[0]->invoice_date);
                    $getExportDocumentsPort[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsPortContainers = array();
                if (count($getExportDocumentsPort) == 1) {
                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsPort[0]->export_id, 3, $getExportDocumentsPort[0]->id);
                }

                //FUMIGATION
                $getExportDocumentsFumigation = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 4);
                if (!empty($getExportDocumentsFumigation[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsFumigation[0]->invoice_date);
                    $getExportDocumentsFumigation[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsFumigationContainers = array();
                if (count($getExportDocumentsFumigation) == 1) {
                    $getExportDocumentsFumigationContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsFumigation[0]->export_id, 4, $getExportDocumentsFumigation[0]->id);
                }

                //PHYTO
                $getExportDocumentsPhyto = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 5);
                if (!empty($getExportDocumentsPhyto[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsPhyto[0]->invoice_date);
                    $getExportDocumentsPhyto[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsPhytoContainers = array();
                if (count($getExportDocumentsPhyto) == 1) {
                    $getExportDocumentsPhytoContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsPhyto[0]->export_id, 5, $getExportDocumentsPhyto[0]->id);
                }

                //COTEROS
                $getExportDocumentsCoteros = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 6);
                if (!empty($getExportDocumentsCoteros[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsCoteros[0]->invoice_date);
                    $getExportDocumentsCoteros[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsCoterosContainers = array();
                if (count($getExportDocumentsCoteros) == 1) {
                    $getExportDocumentsCoterosContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsCoteros[0]->export_id, 6, $getExportDocumentsCoteros[0]->id);
                }

                //INCENTIVES
                $getExportDocumentsIncentives = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 7);
                if (!empty($getExportDocumentsIncentives[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsIncentives[0]->invoice_date);
                    $getExportDocumentsIncentives[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsIncentivesContainers = array();
                if (count($getExportDocumentsIncentives) == 1) {
                    $getExportDocumentsIncentivesContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsIncentives[0]->export_id, 7, $getExportDocumentsIncentives[0]->id);
                }

                //REMOBILIZATION
                $getExportDocumentsRemobilization = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 8);
                if (!empty($getExportDocumentsRemobilization[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsRemobilization[0]->invoice_date);
                    $getExportDocumentsRemobilization[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsRemobilizationContainers = array();
                if (count($getExportDocumentsRemobilization) == 1) {
                    $getExportDocumentsRemobilizationContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsRemobilization[0]->export_id, 8, $getExportDocumentsRemobilization[0]->id);
                }

                //SHIPPING
                $getExportDocumentsShipping = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 9);
                if (!empty($getExportDocumentsShipping[0]->invoice_date)) {
                    $date = new DateTime($getExportDocumentsShipping[0]->invoice_date);
                    $getExportDocumentsShipping[0]->invoice_date = $date->format('Y-m-d\TH:i');
                }

                $getExportDocumentsShippingContainers = array();
                if (count($getExportDocumentsShipping) == 1) {
                    $getExportDocumentsShippingContainers = $this->Export_model->fetch_export_container_documents($getExportDocumentsShipping[0]->export_id, 9, $getExportDocumentsShipping[0]->id);
                }

                //CONTAINER COSTS
                $getExportContainerCosts = $this->Export_model->fetch_export_container_costs($exportId);

                $data = array(
                    "pageheading" => $this->lang->line("document_view"),
                    "pagetype" => "view",
                    "exportid" => $exportId,
                    "sanumber" => $saNumber,
                    "exportpod" => $this->Master_model->get_export_pod(),
                    "shippinglines" => $getShippingLines,
                    "export_details" => $getExportDetails,
                    "originid" => $getExportDetails[0]->origin_id,
                    "dispatchids" => $getExportDetails[0]->dispatchids,
                    "product_type_id" => $getExportDetails[0]->product_type_id,
                    "formsubmit" => "exports/update",
                    'exportSuppliersCustoms' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 1),
                    'exportSuppliersItr' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 2),
                    'exportSuppliersPort' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 3),
                    'exportSuppliersShipping' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 9),
                    'exportSuppliersFumigation' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 4),
                    'exportSuppliersPhyto' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 5),
                    'exportSuppliersCoteros' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 6),
                    'exportSuppliersIncentives' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 7),
                    'exportSuppliersRemobilization' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 8),
                    'containerDetails' => $this->Export_model->fetch_container_details_bydispatchids($getExportDetails[0]->dispatchids),
                    'exportDocumentsCustoms' => $getExportDocumentsCustoms,
                    'exportDocumentsCustomsContainers' => $getExportDocumentsCustomsContainers,
                    'exportDocumentsTransport' => $getExportDocumentsTransport,
                    'exportDocumentsTransportContainers' => $getExportDocumentsTransportContainers,
                    'exportDocumentsPort' => $getExportDocumentsPort,
                    'exportDocumentsPortContainers' => $getExportDocumentsPortContainers,
                    'exportDocumentsFumigation' => $getExportDocumentsFumigation,
                    'exportDocumentsFumigationContainers' => $getExportDocumentsFumigationContainers,
                    'exportDocumentsPhyto' => $getExportDocumentsPhyto,
                    'exportDocumentsPhytoContainers' => $getExportDocumentsPhytoContainers,
                    'exportDocumentsCoteros' => $getExportDocumentsCoteros,
                    'exportDocumentsCoterosContainers' => $getExportDocumentsCoterosContainers,
                    'exportDocumentsIncentives' => $getExportDocumentsIncentives,
                    'exportDocumentsIncentivesContainers' => $getExportDocumentsIncentivesContainers,
                    'exportDocumentsRemobilization' => $getExportDocumentsRemobilization,
                    'exportDocumentsRemobilizationContainers' => $getExportDocumentsRemobilizationContainers,
                    'exportDocumentsShipping' => $getExportDocumentsShipping,
                    'exportDocumentsShippingContainers' => $getExportDocumentsShippingContainers,
                    'exportContainerCosts' => $getExportContainerCosts,
                    "csrfhash" => $this->security->get_csrf_hash(),
                );
                $this->load->view("export/dialog_view_export_documents", $data);
            } else if ($this->input->post('type') == "deleteexportconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->post('eid'),
                    'inputid1' => $this->input->post('sn'),
                    'inputid2' => $this->input->post('did'),
                    'actionurl' => "exports/dialog_export_action",
                    'actiontype' => "deleteexport",
                    'xin_table' => "#xin_table_exports",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deleteexport") {

                $exportId = $this->input->get('inputid');
                $saNumber = $this->input->get('inputid1');
                $dispatchIds = $this->input->get('inputid2');

                $exportDetailsDelete = $this->Export_model->delete_exports($exportId, $saNumber, $dispatchIds, $session['user_id']);

                if ($exportDetailsDelete) {
                    $Return['result'] = $this->lang->line('data_deleted');
                    $Return['redirect'] = false;
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
                "result" => "",
                "error" => "",
                "redirect" => false,
                "csrf_hash" => "",
                "successmessage" => ""
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

    public function get_shipping_lines_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($this->input->get("originid"));
                foreach ($getShippingLines as $shippingline) {
                    $result = $result . "<option value='" . $shippingline->id . "'>" . $shippingline->shipping_line . "</option>";
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

    public function fetch_export_summary_details()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            $dispatchIds = $this->input->post("dispatchIds");
            $originId = $this->input->post("originId");
            $measurementSystemId = $this->input->post("measurementId");
            $productTypeId = $this->input->post("productTypeId");

            if ($dispatchIds != "") {
                $totalContainers = count(explode(",", $dispatchIds));

                $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($measurementSystemId, $originId);

                if (count($getFormulae) > 0) {

                    if ($productTypeId == 1 || $productTypeId == 2) {
                    } else if ($productTypeId == 2 || $productTypeId == 4) {
                    }

                    $strGrossFormula = "";
                    $strNetFormula = "";

                    $dispatchIdArray = explode(',', $dispatchIds);

                    foreach ($getFormulae as $formula) {


                        if ($formula->context == "CBM_HOPPUS_GROSSVOLUME_DISPATCH" || $formula->context == "CBM_GEO_GROSSVOLUME_DISPATCH") {
                            $strGrossFormula = str_replace(array('$l', '$c', '$pcs'), array("length_bought", "circumference_bought", "SUM(dispatch_pieces)"), $formula->calculation_formula);
                        }

                        if ($formula->context == "CBM_HOPPUS_NETVOLUME_DISPATCH" || $formula->context == "CBM_GEO_NETVOLUME_DISPATCH") {
                            $strNetFormula = str_replace(array('$l', '$c', '$pcs'), array("length_bought", "circumference_bought", "SUM(dispatch_pieces)"), $formula->calculation_formula);
                        }
                    }

                    if ($strGrossFormula != "" && $strNetFormula != "") {

                        $totalGrossVolume = 0;
                        $totalNetVolume = 0;
                        $totalPieces = 0;
                        $cftGross = 0;
                        $cftNet = 0;

                        $dataContainer = array();
                        foreach ($dispatchIdArray as $dispatchid) {
                            $fetchVolume = $this->Export_model->get_total_volume($dispatchid, $strGrossFormula, $strNetFormula);

                            if (
                                $fetchVolume[0]->total_pieces > 0 &&
                                $fetchVolume[0]->grossvolume > 0 && $fetchVolume[0]->netvolume > 0
                            ) {
                                $dataContainer[] = array(
                                    "dispatchId" => $dispatchid,
                                    "grossVolume" => $fetchVolume[0]->grossvolume,
                                    "netVolume" => $fetchVolume[0]->netvolume,
                                    "totalPieces" => $fetchVolume[0]->total_pieces,
                                    "cftGross" =>  round($fetchVolume[0]->grossvolume / $fetchVolume[0]->total_pieces * 35.515, 3),
                                    "cftNet" =>  round($fetchVolume[0]->netvolume / $fetchVolume[0]->total_pieces * 35.515, 3),
                                );
                            }

                            $totalPieces = $totalPieces + $fetchVolume[0]->total_pieces;
                            $totalGrossVolume = $totalGrossVolume + $fetchVolume[0]->grossvolume;
                            $totalNetVolume = $totalNetVolume + $fetchVolume[0]->netvolume;
                        }

                        if (count($dataContainer) > 0) {
                            $dataUploaded = array(
                                "totalContainers" => $totalContainers,
                                "totalPieces" => $totalPieces,
                                "totalNetVolume" => sprintf('%0.3f', $totalNetVolume),
                                "totalGrossVolume" => sprintf('%0.3f', $totalGrossVolume),
                                "dataContainers" => $dataContainer,
                            );

                            $Return["pages"] = "";
                            $Return["result"] = $dataUploaded;
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $Return["redirect"] = false;
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["pages"] = "";
                            $Return["error"] = $this->lang->line("common_error");
                            $Return["result"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return["pages"] = "";
                        $Return["error"] = $this->lang->line("common_error");
                        $Return["result"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else {
                    $Return["pages"] = "";
                    $Return["error"] = $this->lang->line("common_error");
                    $Return["result"] = "";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                }
            } else {
                $Return["pages"] = "";
                $Return["error"] = $this->lang->line("invalid_request");
                $Return["result"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            }
        } else {
            $Return["pages"] = "";
            $Return["result"] = "";
            $Return["error"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
        }
    }

    public function update()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "export") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "view") {

                    $exportid = $this->input->post("exportid");
                    $originid = $this->input->post("originid");
                    $sanumber = strtoupper(preg_replace('/\s+/', '', $this->input->post("sanumber")));
                    $inputsanumber = strtoupper(preg_replace('/\s+/', '', $this->input->post("inputsanumber")));
                    $dispatchids = $this->input->post("dispatchids");
                    $producttypeid = $this->input->post("producttypeid");
                    $measurementsystemid = $this->input->post("measurementsystemid");
                    $portofdischarge = $this->input->post("portofdischarge");
                    $blnumber = strtoupper($this->input->post("blnumber"));
                    $bldate = $this->input->post("bldate");
                    $shippeddate = $this->input->post("shippeddate");
                    $clientpno = strtoupper($this->input->post("clientpno"));
                    $vesselname = strtoupper($this->input->post("vesselname"));
                    $totalpiecesuploaded = $this->input->post("totalpiecesuploaded");
                    $totalgrossvolume = $this->input->post("totalgrossvolume");
                    $totalnetvolume = $this->input->post("totalnetvolume");
                    $totalcontainers = $this->input->post("totalcontainers");
                    $containerdata = $this->input->post("containerdata");

                    if ($sanumber == $inputsanumber) {

                        $dataExportDetails = array(
                            "product_id" => 0,
                            "product_type_id" => $producttypeid,
                            "sa_number" => $inputsanumber,
                            "pod" => $portofdischarge,
                            "shipped_date" => $shippeddate,
                            "bl_no" => $blnumber,
                            "bl_date" => $bldate,
                            "vessel_name" => $vesselname,
                            "client_pno" => $clientpno,
                            "total_containers" => $totalcontainers,
                            "total_pieces" => $totalpiecesuploaded,
                            "total_gross_volume" => $totalgrossvolume,
                            "total_net_volume" => $totalnetvolume,
                            "updatedby" => $session['user_id'],
                            "measurement_system" => $measurementsystemid,
                        );

                        $updateExportDetails = $this->Export_model->update_export_details($exportid, $sanumber, $dataExportDetails);

                        if ($updateExportDetails == true) {

                            $containerDataJson = json_decode($containerdata, true);

                            if (count($containerDataJson) > 0) {

                                foreach ($containerDataJson as $containerdata) {
                                    $dataExportContainerData = array(
                                        "gross_volume" => $containerdata["grossVolume"],
                                        "net_volume" => $containerdata["netVolume"],
                                        "cft_value" => $containerdata["cftGross"],
                                        "cft_net_value" => $containerdata["cftNet"],
                                        "total_pieces" => $containerdata["totalPieces"],
                                        "updatedby" => $session["user_id"],
                                        "isactive" => 1,
                                        "updateddate" => date("Y-m-d H:i:s"),
                                    );

                                    $updateExportContainerData = $this->Export_model->update_export_container_data(
                                        $exportid,
                                        $containerdata["dispatchId"],
                                        $dataExportContainerData
                                    );
                                }

                                $Return["duplicateerror"] = "";
                                $Return["error"] = "";
                                $Return["result"] = $this->lang->line("data_updated");
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            } else {
                                $Return["duplicateerror"] = "";
                                $Return["error"] = "";
                                $Return["result"] = $this->lang->line("data_updated");
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return["duplicateerror"] = "";
                            $Return["error"] = $this->lang->line("error_updating");
                            $Return["result"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $getSANumberCount = $this->Export_model->get_sa_number_count($inputsanumber, $originid);

                        if ($getSANumberCount[0]->cnt == 0) {

                            $dataExportDetails = array(
                                "product_id" => 0,
                                "product_type_id" => $producttypeid,
                                "sa_number" => $inputsanumber,
                                "pod" => $portofdischarge,
                                "shipped_date" => $shippeddate,
                                "bl_no" => $blnumber,
                                "bl_date" => $bldate,
                                "vessel_name" => $vesselname,
                                "client_pno" => $clientpno,
                                "total_containers" => $totalcontainers,
                                "total_pieces" => $totalpiecesuploaded,
                                "total_gross_volume" => $totalgrossvolume,
                                "total_net_volume" => $totalnetvolume,
                                "updatedby" => $session['user_id'],
                                "measurement_system" => $measurementsystemid,
                            );

                            $updateExportDetails = $this->Export_model->update_export_details($exportid, $sanumber, $dataExportDetails);

                            if ($updateExportDetails == true) {

                                $containerDataJson = json_decode($containerdata, true);

                                if (count($containerDataJson) > 0) {

                                    foreach ($containerDataJson as $containerdata) {
                                        $dataExportContainerData = array(
                                            "gross_volume" => $containerdata["grossVolume"],
                                            "net_volume" => $containerdata["netVolume"],
                                            "cft_value" => $containerdata["cftGross"],
                                            "cft_net_value" => $containerdata["cftNet"],
                                            "total_pieces" => $containerdata["totalPieces"],
                                            "updatedby" => $session["user_id"],
                                            "isactive" => 1,
                                            "updateddate" => date("Y-m-d H:i:s"),
                                        );

                                        $updateExportContainerData = $this->Export_model->update_export_container_data(
                                            $exportid,
                                            $containerdata["dispatchId"],
                                            $dataExportContainerData
                                        );
                                    }

                                    $Return["duplicateerror"] = "";
                                    $Return["error"] = "";
                                    $Return["result"] = $this->lang->line("data_updated");
                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return["duplicateerror"] = "";
                                    $Return["error"] = "";
                                    $Return["result"] = $this->lang->line("data_updated");
                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return["duplicateerror"] = "";
                                $Return["error"] = $this->lang->line("error_updating");
                                $Return["result"] = "";
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {

                            $Return["duplicateerror"] = $this->lang->line("error_sanumber_exists");
                            $Return["error"] = "";
                            $Return["result"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                }
            } else {
                redirect("/logout");
            }
        } else {
            $Return["duplicateerror"] = "";
            $Return["error"] = $this->lang->line("invalid_request");
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

    //DOCUMENTS 
    public function upload_documents()
    {
        $Return = array(
            'result' => '',
            'error' => '',
            'redirect' => false,
            'csrf_hash' => '',
            'warning' => '',
            'success' => '',
        );
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            $exportId = $this->input->post("exportId");
            $originId = $this->input->post("originId");
            $saNumber = $this->input->post("dispatchids");
            $exporttype = $this->input->post("exportType");

            //DELETE EXISTING FILES
            $this->deletefilesfromfoldertype("xml");

            if ($_FILES['fileUploadDoc']['size'] > 0) {

                if ($exporttype == 1) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                if (strpos(trim($docXml), '<?xml') !== 0) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 2) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 3) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 9) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 4) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 6) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 5) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 7) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($exporttype == 8) {
                    if (is_uploaded_file($_FILES['fileUploadDoc']['tmp_name'])) {
                        $allowed =  array('xml', "XML", 'pdf', "PDF");
                        $filename = $_FILES['fileUploadDoc']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            if ($ext == "pdf" || $ext == "PDF") {
                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/invoices/";

                                $newfilename = 'INV_' . round(microtime(true)) . '.pdf';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/invoices/" . $newfilename;

                                $dados = [
                                    'fileExtension' => $ext,
                                    'fileUrl' => $fileurl,
                                ];

                                $dataResponse = json_decode(json_encode($dados, JSON_PRETTY_PRINT), true);;

                                $Return['result'] = $dataResponse;
                                $Return['error'] = "";
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                $this->output($Return);
                            } else if ($ext == "xml" || $ext == "XML") {

                                $tmp_name = $_FILES["fileUploadDoc"]["tmp_name"];
                                $invoiceFolder = "assets/exportdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/exportdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                // if (strpos(trim($docXml), '<?xml') !== 0) {
                                //     $Return['error'] = $this->lang->line('error_xml');
                                //     $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                //     $this->output($Return);
                                //     exit;
                                // }

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $exporttype, $fileurl), true);
                                if ($xmlResponse != null && $xmlResponse != null) {
                                    $Return['result'] = $xmlResponse;
                                    $Return['error'] = "";
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                } else {
                                    $Return['error'] = $this->lang->line('error_invalid_file');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                }
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_invalid_file');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_invalid_file');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                }
            } else {
                $Return['error'] = $this->lang->line("error_invalid_file");
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
    }

    public function importInvoice($xml, $ext, $originId, $exportType, $fileurl = null)
    {
        $session = $this->session->userdata('fullname');

        if ($exportType == 1) {
            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            if ($companyIdNode->item(0)->nodeValue == "" || $companyIdNode->item(0)->nodeValue == null) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 1,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";
            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 2) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 2,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 3) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 3,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 9) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 9,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 4) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 4,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 6) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 6,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 5) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 5,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 7) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 7,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        } else if ($exportType == 8) {

            // Clean malformed XML
            $xml = preg_replace('/<(\w+)xmlns=/', '<\1 xmlns=', $xml);
            $xml = preg_replace('/\s+>/', '>', $xml);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = false;
            libxml_use_internal_errors(true); // Capture XML parsing errors

            if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
                $Return['result'] = "";
                $Return['error'] = $this->lang->line('error_xml');
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

            // Extract from Main XML
            $issueDateNode = $xpath->query('//*[local-name()="IssueDate"]');
            $issueTimeNode = $xpath->query('//*[local-name()="IssueTime"]');
            $documentIdNode = $xpath->query('//*[local-name()="ParentDocumentID"]');
            $registrationNameNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:RegistrationName');
            $companyIdNode = $xpath->query('//cac:SenderParty/cac:PartyTaxScheme/cbc:CompanyID');
            $supplierId = 0;

            //CHECK AND REGISTER COMPANY ID
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_exportsupplier_count($companyIdNode->item(0)->nodeValue);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "supplier_name" => $registrationNameNode->item(0)->nodeValue,
                    "supplier_id" => $companyIdNode->item(0)->nodeValue,
                    "export_type" => 7,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);
                $supplierId = $insertSupplier + 0;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_exportsupplier($companyIdNode->item(0)->nodeValue);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
            }

            // Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $taxAmount = 0;
            $allowanceTotalAmount = 0;
            $payableAmount = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        // Extract `TaxExclusiveAmount` from the embedded XML
                        $taxExclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $taxInclusiveAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount");
                        $allowanceTotalAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");

                        if ($taxExclusiveAmountNode->length > 0) {
                            $taxExclusiveAmount = $taxExclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($taxInclusiveAmountNode->length > 0) {
                            $taxInclusiveAmount = $taxInclusiveAmountNode->item(0)->nodeValue + 0;
                        }

                        $taxAmount = $taxInclusiveAmount - $taxExclusiveAmount;

                        if ($taxExclusiveAmount <= 0 && $taxInclusiveAmount >= 0) {
                            $taxExclusiveAmount = $taxInclusiveAmount + 0;
                            $taxInclusiveAmount = 0;
                            $taxAmount = 0;
                        }

                        if ($allowanceTotalAmountNode->length > 0) {
                            $allowanceTotalAmount = $allowanceTotalAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $issuedTime = ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "";
            $formattedDate = "";

            if ($issuedDate != "" && $issuedTime != "") {
                $date = new DateTime($issuedDate . " " . $issuedTime);
                $formattedDate = $date->format('d/m/Y h:i A');
            }

            $currencyCode = "es_CO";
            $currencyFormat = "COP";

            $taxExclusiveAmountValue = $taxExclusiveAmount + 0;
            $taxInclusiveAmountValue = $taxInclusiveAmount + 0;
            $taxAmountValue = $taxAmount + 0;
            $allowanceTotalAmountValue = $allowanceTotalAmount + 0;
            $payableAmountValue = $payableAmount + 0;

            $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
            $taxExclusiveAmount = $fmt->formatCurrency($taxExclusiveAmount, $currencyFormat);
            $taxInclusiveAmount = $fmt->formatCurrency($taxInclusiveAmount, $currencyFormat);
            $taxAmount = $fmt->formatCurrency($taxAmount, $currencyFormat);
            $allowanceTotalAmount = $fmt->formatCurrency($allowanceTotalAmount, $currencyFormat);
            $payableAmount = $fmt->formatCurrency($payableAmount, $currencyFormat);

            $dados = [
                'issueDate' => $formattedDate,
                //'issueTime' => ($issueTimeNode->length > 0) ? $issueTimeNode->item(0)->nodeValue : "NA",
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'taxExclusiveAmount' => $taxExclusiveAmount,
                'taxInclusiveAmount' => $taxInclusiveAmount,
                'taxAmount' => $taxAmount,
                'allowanceTotalAmount' => $allowanceTotalAmount,
                'payableAmount' => $payableAmount,
                'taxExclusiveAmountValue' => $taxExclusiveAmountValue,
                'taxInclusiveAmountValue' => $taxInclusiveAmountValue,
                'taxAmountValue' => $taxAmountValue,
                'allowanceTotalAmountValue' => $allowanceTotalAmountValue,
                'payableAmountValue' => $payableAmountValue,
                'fileExtension' => $ext,
                'fileUrl' => $fileurl,
                'supplierId' => $supplierId,
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        }
    }

    //DOCUMENTS SAVING

    public function save_export_documents()
    {
        $Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if ($this->input->post('add_type') == 1) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Custom = $this->input->post('updateContainerValueData_Custom');
                $invoiceNo_Custom = $this->input->post('invoiceNo_Custom');
                $supplierName_Custom = $this->input->post('supplierName_Custom');
                $formattedDate_Custom = $this->input->post('formattedDate_Custom');
                $subTotal_Custom = $this->input->post('subTotal_Custom');
                $iva_Custom = $this->input->post('iva_Custom');
                $retefuente_Custom = $this->input->post('retefuente_Custom');
                $payable_Custom = $this->input->post('payable_Custom');
                $updateContainerValueJson = json_decode($updateContainerValueData_Custom, true);
                $uploadPdfFileCustomAgency = $this->input->post('uploadPdfFileCustomAgency');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 1);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileCustomAgency,
                    "invoice_no" => $invoiceNo_Custom,
                    "supplier_id" => $supplierName_Custom,
                    "invoice_date " => $formattedDate_Custom,
                    "sub_total " => $subTotal_Custom,
                    "tax_total" => $iva_Custom,
                    "allowance_total" => $retefuente_Custom,
                    "payable_total" => $payable_Custom,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {
                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 1);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 1,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"],
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }



                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 2) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_ITR = $this->input->post('updateContainerValueData_ITR');
                $invoiceNo_ITR = $this->input->post('invoiceNo_ITR');
                $supplierName_ITR = $this->input->post('supplierName_ITR');
                $formattedDate_ITR = $this->input->post('formattedDate_ITR');
                $subTotal_ITR = $this->input->post('subTotal_ITR');
                $iva_ITR = $this->input->post('iva_ITR');
                $retefuente_ITR = $this->input->post('retefuente_ITR');
                $payable_ITR = $this->input->post('payable_ITR');
                $updateContainerValueJson = json_decode($updateContainerValueData_ITR, true);
                $uploadPdfFileITR = $this->input->post('uploadPdfFileITR');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 2);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileITR,
                    "invoice_no" => $invoiceNo_ITR,
                    "supplier_id" => $supplierName_ITR,
                    "invoice_date " => $formattedDate_ITR,
                    "sub_total " => $subTotal_ITR,
                    "tax_total" => $iva_ITR,
                    "allowance_total" => $retefuente_ITR,
                    "payable_total" => $payable_ITR,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 2);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 2,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 3) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Port = $this->input->post('updateContainerValueData_Port');
                $invoiceNo_Port = $this->input->post('invoiceNo_Port');
                $supplierName_Port = $this->input->post('supplierName_Port');
                $formattedDate_Port = $this->input->post('formattedDate_Port');
                $subTotal_Port = $this->input->post('subTotal_Port');
                $iva_Port = $this->input->post('iva_Port');
                $retefuente_Port = $this->input->post('retefuente_Port');
                $payable_Port = $this->input->post('payable_Port');
                $updateContainerValueJson = json_decode($updateContainerValueData_Port, true);
                $uploadPdfFilePort = $this->input->post('uploadPdfFilePort');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 3);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFilePort,
                    "invoice_no" => $invoiceNo_Port,
                    "supplier_id" => $supplierName_Port,
                    "invoice_date " => $formattedDate_Port,
                    "sub_total " => $subTotal_Port,
                    "tax_total" => $iva_Port,
                    "allowance_total" => $retefuente_Port,
                    "payable_total" => $payable_Port,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 3);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 3,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 9) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Shipping = $this->input->post('updateContainerValueData_Shipping');
                $invoiceNo_Shipping = $this->input->post('invoiceNo_Shipping');
                $supplierName_Shipping = $this->input->post('supplierName_Shipping');
                $formattedDate_Shipping = $this->input->post('formattedDate_Shipping');
                $subTotal_Shipping = $this->input->post('subTotal_Shipping');
                $iva_Shipping = $this->input->post('iva_Shipping');
                $retefuente_Shipping = $this->input->post('retefuente_Shipping');
                $payable_Shipping = $this->input->post('payable_Shipping');
                $updateContainerValueJson = json_decode($updateContainerValueData_Shipping, true);
                $uploadPdfFileShipping = $this->input->post('uploadPdfFileShipping');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 9);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileShipping,
                    "invoice_no" => $invoiceNo_Shipping,
                    "supplier_id" => $supplierName_Shipping,
                    "invoice_date " => $formattedDate_Shipping,
                    "sub_total " => $subTotal_Shipping,
                    "tax_total" => $iva_Shipping,
                    "allowance_total" => $retefuente_Shipping,
                    "payable_total" => $payable_Shipping,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 9);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 9,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 4) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Fumigation = $this->input->post('updateContainerValueData_Fumigation');
                $invoiceNo_Fumigation = $this->input->post('invoiceNo_Fumigation');
                $supplierName_Fumigation = $this->input->post('supplierName_Fumigation');
                $formattedDate_Fumigation = $this->input->post('formattedDate_Fumigation');
                $subTotal_Fumigation = $this->input->post('subTotal_Fumigation');
                $iva_Fumigation = $this->input->post('iva_Fumigation');
                $retefuente_Fumigation = $this->input->post('retefuente_Fumigation');
                $payable_Fumigation = $this->input->post('payable_Fumigation');
                $updateContainerValueJson = json_decode($updateContainerValueData_Fumigation, true);
                $uploadPdfFileFumigation = $this->input->post('uploadPdfFileFumigation');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 4);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileFumigation,
                    "invoice_no" => $invoiceNo_Fumigation,
                    "supplier_id" => $supplierName_Fumigation,
                    "invoice_date " => $formattedDate_Fumigation,
                    "sub_total " => $subTotal_Fumigation,
                    "tax_total" => $iva_Fumigation,
                    "allowance_total" => $retefuente_Fumigation,
                    "payable_total" => $payable_Fumigation,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 4);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 4,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 6) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Coteros = $this->input->post('updateContainerValueData_Coteros');
                $invoiceNo_Coteros = $this->input->post('invoiceNo_Coteros');
                $supplierName_Coteros = $this->input->post('supplierName_Coteros');
                $formattedDate_Coteros = $this->input->post('formattedDate_Coteros');
                $subTotal_Coteros = $this->input->post('subTotal_Coteros');
                $iva_Coteros = $this->input->post('iva_Coteros');
                $retefuente_Coteros = $this->input->post('retefuente_Coteros');
                $payable_Coteros = $this->input->post('payable_Coteros');
                $updateContainerValueJson = json_decode($updateContainerValueData_Coteros, true);
                $uploadPdfFileCoteros = $this->input->post('uploadPdfFileCoteros');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 6);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileCoteros,
                    "invoice_no" => $invoiceNo_Coteros,
                    "supplier_id" => $supplierName_Coteros,
                    "invoice_date " => $formattedDate_Coteros,
                    "sub_total " => $subTotal_Coteros,
                    "tax_total" => $iva_Coteros,
                    "allowance_total" => $retefuente_Coteros,
                    "payable_total" => $payable_Coteros,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 6);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 6,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 5) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Phyto = $this->input->post('updateContainerValueData_Phyto');
                $invoiceNo_Phyto = $this->input->post('invoiceNo_Phyto');
                $supplierName_Phyto = $this->input->post('supplierName_Phyto');
                $formattedDate_Phyto = $this->input->post('formattedDate_Phyto');
                $subTotal_Phyto = $this->input->post('subTotal_Phyto');
                $iva_Phyto = $this->input->post('iva_Phyto');
                $retefuente_Phyto = $this->input->post('retefuente_Phyto');
                $payable_Phyto = $this->input->post('payable_Phyto');
                $updateContainerValueJson = json_decode($updateContainerValueData_Phyto, true);
                $uploadPdfFilePhyto = $this->input->post('uploadPdfFilePhyto');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 5);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFilePhyto,
                    "invoice_no" => $invoiceNo_Phyto,
                    "supplier_id" => $supplierName_Phyto,
                    "invoice_date " => $formattedDate_Phyto,
                    "sub_total " => $subTotal_Phyto,
                    "tax_total" => $iva_Phyto,
                    "allowance_total" => $retefuente_Phyto,
                    "payable_total" => $payable_Phyto,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 5);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 5,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 7) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Incentives = $this->input->post('updateContainerValueData_Incentives');
                $invoiceNo_Incentives = $this->input->post('invoiceNo_Incentives');
                $supplierName_Incentives = $this->input->post('supplierName_Incentives');
                $formattedDate_Incentives = $this->input->post('formattedDate_Incentives');
                $subTotal_Incentives = $this->input->post('subTotal_Incentives');
                $iva_Incentives = $this->input->post('iva_Incentives');
                $retefuente_Incentives = $this->input->post('retefuente_Incentives');
                $payable_Incentives = $this->input->post('payable_Incentives');
                $updateContainerValueJson = json_decode($updateContainerValueData_Incentives, true);
                $uploadPdfFileIncentives = $this->input->post('uploadPdfFileIncentives');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 7);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileIncentives,
                    "invoice_no" => $invoiceNo_Incentives,
                    "supplier_id" => $supplierName_Incentives,
                    "invoice_date " => $formattedDate_Incentives,
                    "sub_total " => $subTotal_Incentives,
                    "tax_total" => $iva_Incentives,
                    "allowance_total" => $retefuente_Incentives,
                    "payable_total" => $payable_Incentives,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 7);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 7,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 8) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $fileExtension = $this->input->post('fileExtension');
                $updateContainerValueData_Remobilization = $this->input->post('updateContainerValueData_Remobilization');
                $invoiceNo_Remobilization = $this->input->post('invoiceNo_Remobilization');
                $supplierName_Remobilization = $this->input->post('supplierName_Remobilization');
                $formattedDate_Remobilization = $this->input->post('formattedDate_Remobilization');
                $subTotal_Remobilization = $this->input->post('subTotal_Remobilization');
                $iva_Remobilization = $this->input->post('iva_Remobilization');
                $retefuente_Remobilization = $this->input->post('retefuente_Remobilization');
                $payable_Remobilization = $this->input->post('payable_Remobilization');
                $updateContainerValueJson = json_decode($updateContainerValueData_Remobilization, true);
                $uploadPdfFileRemobilization = $this->input->post('uploadPdfFileRemobilization');

                //DELETE EXISTING

                $updateExportDoc = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $this->Export_model->update_exportdocuments($updateExportDoc, $exportId, 8);

                //INSERT
                $dataExportDocuments = array(
                    "export_id " => $exportId,
                    "export_type " => $this->input->post('add_type'),
                    "file_extension " => $fileExtension,
                    "file_url" => $uploadPdfFileRemobilization,
                    "invoice_no" => $invoiceNo_Remobilization,
                    "supplier_id" => $supplierName_Remobilization,
                    "invoice_date " => $formattedDate_Remobilization,
                    "sub_total " => $subTotal_Remobilization,
                    "tax_total" => $iva_Remobilization,
                    "allowance_total" => $retefuente_Remobilization,
                    "payable_total" => $payable_Remobilization,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                );

                $insertExportDocuments = $this->Export_model->add_exportdocuments($dataExportDocuments);

                if ($insertExportDocuments > 0) {
                    if (count($updateContainerValueJson) > 0) {

                        $updateExportDocContainer = array(
                            "updated_by" => $session['user_id'],
                            "is_active" => 0,
                        );

                        $this->Export_model->update_exportcontainerdoc($updateExportDocContainer, $exportId, 8);

                        foreach ($updateContainerValueJson as $containerdata) {
                            $dataExportContainer = array(
                                "export_doc_id" => $insertExportDocuments,
                                "export_id" => $exportId,
                                "export_type" => 8,
                                "dispatch_id" => $containerdata["mappingid"],
                                "container_value" => $containerdata["updatedContainerValue"] + 0,
                                "created_by" => $session['user_id'],
                                "updated_by" => $session['user_id'],
                                "is_active" => 1
                            );

                            $insertExportContainerValue = $this->Export_model->add_exportcontainerdoc($dataExportContainer);
                        }
                    }
                }

                if ($insertExportDocuments > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else if ($this->input->post('add_type') == 10) {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $exportId = $this->input->post('exportId');
                $updateContainerValueData_ContainerCost = $this->input->post('updateContainerValueData_ContainerCost');
                $updateContainerValueJson = json_decode($updateContainerValueData_ContainerCost, true);

                if (count($updateContainerValueJson) > 0) {

                    $updateExportDocContainer = array(
                        "updated_by" => $session['user_id'],
                        "is_active" => 0,
                    );

                    $this->Export_model->update_exportcontainercost($updateExportDocContainer, $exportId);

                    foreach ($updateContainerValueJson as $containerdata) {
                        $dataExportContainer = array(
                            "export_id" => $exportId,
                            "dispatch_id" => $containerdata["mappingid"],
                            "unit_price" => $containerdata["updatedContainerCostValue"] + 0,
                            "exchange_rate" => $containerdata["updatedContainerCostTrmValue"] + 0,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1
                        );

                        $insertExportContainerValue = $this->Export_model->add_exportcontainercost($dataExportContainer);
                    }
                }

                if ($insertExportContainerValue > 0) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_adding');
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
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }

    //END DOCUMENTS SAVING

    function tagValue($node, $tag)
    {
        return $node->getElementsByTagName("$tag")->item(0)->nodeValue;
    }

    function tagValue1($node, $tag)
    {
        return $node->getElementsByTagName("$tag")->nodeValue;
    }

    public function deletefilesfromfoldertype($type)
    {
        if ($type == "invoices") {
            $files = glob("assets/exportdocs/invoices/*.pdf");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } else {
            $files = glob("assets/exportdocs/xmlupload/*.xml");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
