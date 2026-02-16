<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Exporttracking extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Export_model");
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
        $data["title"] = $this->lang->line("export_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_exporttracking";
        if (!empty($session)) {
            $applicable_origins = $session["applicable_origins"];
            $data["shippinglines"] = $this->Master_model->get_shippinglines_by_origin($applicable_origins[0]->id);
            $data["producttypes"] = $this->Master_model->get_product_type();
            $data["csrf_hash"] =  $this->security->get_csrf_hash();

            $data["subview"] = $this->load->view("export/export_tracking_lists", $data, TRUE);
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

                $actionExport = '<span style="margin-left:1px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") .'"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewexport" data-toggle="modal" data-target=".view-modal-data" data-export_id="' . $r->id . '" data-sa_number="' . $r->sa_number . '" data-dispatch_ids =' . $r->dispatchids . '><span class="fas fa-eye"></span></button></span>';

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

                //$this->generate_dispatch_report($dispatchId, $containerNumber);
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
            } else if ($this->input->get('type') == "deleteinvoiceconfirmation") {
                $Return["redirect"] = false;
                $Return["result"] = "";
                $Return["selectedInvoiceId"] = $this->input->get('id');
                $Return["selectedExportId"] = $this->input->get('export_id');
                $Return["selectedExportType"] = $this->input->get('export_type');
                $Return["pageheading"] = $this->lang->line("confirmation");
                $Return["pagemessage"] = $this->lang->line("delete_message");
                $Return["messagetype"] = "info";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            } else if ($this->input->get('type') == "deleteexportinvoice") {

                $invoiceId = $this->input->get('inputid');
                $exportId = $this->input->get('inputid1');
                $exportType = $this->input->get('inputid2');

                $data = array(
                    'is_active' => 0,
                );

                $invoiceDelete = $this->Export_model->update_invoice_data($exportId, $invoiceId, $data);
                
                $dataContainerDelete = array(
                    'is_active' => 0,
                );
                
                $invoiceDeleteContainer = $this->Export_model->update_invoice_data_container($exportId, $invoiceId, $dataContainerDelete);

                $getExportDocumentsPortInvoiceLists = json_encode($this->Export_model->fetch_export_document_details($exportId, $exportType));
                if ($invoiceDelete) {

                    $Return['result'] = $this->lang->line('data_deleted');
                    $Return['redirect'] = false;
                    $Return['type'] = $exportType;
                    $Return['updatedlist'] = $getExportDocumentsPortInvoiceLists;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_deleting');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['type'] = $exportType;
                    $Return['updatedlist'] = $getExportDocumentsPortInvoiceLists;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->post("type") == "viewexportdocuments") {
                $exportId = $this->input->post("eid");
                $saNumber = $this->input->post("sn");

                $getExportDetails = $this->Export_model->get_export_details_by_id($exportId, $saNumber);
                $getShippingLines = $this->Master_model->get_shippinglines_by_origin($getExportDetails[0]->origin_id);

                //CUSTOMS
                $getExportDocumentsCustoms = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 1);
                $getExportDocumentsCustomsInvoiceLists = [];
                if (count($getExportDocumentsCustoms) > 0) {
                    $getExportDocumentsCustomsInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsCustoms[0]->export_id, 1);
                }

                //TRANSPORT
                $getExportDocumentsTransport = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 2);
                $getExportDocumentsITRInvoiceLists = [];
                if (count($getExportDocumentsTransport) > 0) {
                    $getExportDocumentsITRInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsTransport[0]->export_id, 2);
                }

                //PORT
                $getExportDocumentsPort = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 3);
                $getExportDocumentsPortInvoiceLists = [];
                if (count($getExportDocumentsPort) > 0) {
                    $getExportDocumentsPortInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsPort[0]->export_id, 3);
                }

                //FUMIGATION
                $getExportDocumentsFumigation = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 4);
                $getExportDocumentsFumigationInvoiceLists = [];
                if (count($getExportDocumentsFumigation) > 0) {
                    $getExportDocumentsFumigationInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsFumigation[0]->export_id, 4);
                }

                //PHYTO
                $getExportDocumentsPhyto = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 5);
                $getExportDocumentsPhytoInvoiceLists = [];
                if (count($getExportDocumentsPhyto) > 0) {
                    $getExportDocumentsPhytoInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsPhyto[0]->export_id, 5);
                }

                //COTEROS
                $getExportDocumentsCoteros = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 6);
                $getExportDocumentsCoterosInvoiceLists = [];
                if (count($getExportDocumentsCoteros) > 0) {
                    $getExportDocumentsCoterosInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsCoteros[0]->export_id, 6);
                }

                //INCENTIVES
                $getExportDocumentsIncentives = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 7);
                $getExportDocumentsIncentivesInvoiceLists = [];
                if (count($getExportDocumentsIncentives) > 0) {
                    $getExportDocumentsIncentivesInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsIncentives[0]->export_id, 7);
                }

                //REMOBILIZATION
                $getExportDocumentsRemobilization = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 8);
                $getExportDocumentsRemobilizationInvoiceLists = [];
                if (count($getExportDocumentsRemobilization) > 0) {
                    $getExportDocumentsRemobilizationInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsRemobilization[0]->export_id, 8);
                }

                //SHIPPING
                $getExportDocumentsShipping = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 9);
                $getExportDocumentsShippingInvoiceLists = [];
                if (count($getExportDocumentsShipping) > 0) {
                    $getExportDocumentsShippingInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsShipping[0]->export_id, 9);
                }

                //CONTAINER COSTS
                $getExportContainerCosts = $this->Export_model->fetch_export_container_costs($exportId);
                
                //LOADING COSTS
                //$getExportLoadingCosts = $this->Export_model->fetch_export_loading_costs($exportId); 
                $getExportDocumentsContainerLoadingCosts = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 11);
                $getExportDocumentsContainerLoadingCostsInvoiceLists = [];
                if (count($getExportDocumentsContainerLoadingCosts) > 0) {
                    $getExportDocumentsContainerLoadingCostsInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsContainerLoadingCosts[0]->export_id, 11);
                }
                
                //OTHERCOSTS
                $getExportDocumentsOtherCosts = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 12);
                $getExportDocumentsOtherCostsInvoiceLists = [];
                if (count($getExportDocumentsOtherCosts) > 0) {
                    $getExportDocumentsOtherCostsInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsOtherCosts[0]->export_id, 12);
                }

                //DHL COSTS
                $getExportDocumentsDhlCosts = $this->Export_model->fetch_export_documents($getExportDetails[0]->id, 13);
                $getExportDocumentsDhlCostsInvoiceLists = [];
                if (count($getExportDocumentsDhlCosts) > 0) {
                    $getExportDocumentsDhlCostsInvoiceLists = $this->Export_model->fetch_export_document_details($getExportDocumentsDhlCosts[0]->export_id, 13);
                }

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
                    'exportSuppliersDhlCost' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 13),
                    'exportSuppliersContainerLoadingCost' => $this->Master_model->fetch_export_suppliers($getExportDetails[0]->origin_id, 11),
                    'containerDetails' => $this->Export_model->fetch_container_details_bydispatchids($getExportDetails[0]->dispatchids),
                    'exportDocumentsCustoms' => $getExportDocumentsCustoms,
                    'exportDocumentsTransport' => $getExportDocumentsTransport,
                    'exportDocumentsPort' => $getExportDocumentsPort,
                    'exportDocumentsFumigation' => $getExportDocumentsFumigation,
                    'exportDocumentsPhyto' => $getExportDocumentsPhyto,
                    'exportDocumentsCoteros' => $getExportDocumentsCoteros,
                    'exportDocumentsIncentives' => $getExportDocumentsIncentives,
                    'exportDocumentsRemobilization' => $getExportDocumentsRemobilization,
                    'exportDocumentsShipping' => $getExportDocumentsShipping,
                    'exportContainerCosts' => $getExportContainerCosts, 
                    //'exportLoadingCosts' => $getExportLoadingCosts,
                    "exportDocumentsPortInvoiceLists" => json_encode($getExportDocumentsPortInvoiceLists),
                    "exportDocumentsPortInvoiceListsCount" => count($getExportDocumentsPortInvoiceLists),
                    "exportDocumentsCustomsInvoiceLists" => json_encode($getExportDocumentsCustomsInvoiceLists),
                    "exportDocumentsCustomsInvoiceListsCount" => count($getExportDocumentsCustomsInvoiceLists),
                    "exportDocumentsITRInvoiceLists" => json_encode($getExportDocumentsITRInvoiceLists),
                    "exportDocumentsITRInvoiceListsCount" => count($getExportDocumentsITRInvoiceLists),
                    "exportDocumentsFumigationInvoiceLists" => json_encode($getExportDocumentsFumigationInvoiceLists),
                    "exportDocumentsFumigationInvoiceListsCount" => count($getExportDocumentsFumigationInvoiceLists),
                    "exportDocumentsCoterosInvoiceLists" => json_encode($getExportDocumentsCoterosInvoiceLists),
                    "exportDocumentsCoterosInvoiceListsCount" => count($getExportDocumentsCoterosInvoiceLists),
                    "exportDocumentsPhytoInvoiceLists" => json_encode($getExportDocumentsPhytoInvoiceLists),
                    "exportDocumentsPhytoInvoiceListsCount" => count($getExportDocumentsPhytoInvoiceLists),
                    "exportDocumentsIncentivesInvoiceLists" => json_encode($getExportDocumentsIncentivesInvoiceLists),
                    "exportDocumentsIncentivesInvoiceListsCount" => count($getExportDocumentsIncentivesInvoiceLists),
                    "exportDocumentsRemobilizationInvoiceLists" => json_encode($getExportDocumentsRemobilizationInvoiceLists),
                    "exportDocumentsRemobilizationInvoiceListsCount" => count($getExportDocumentsRemobilizationInvoiceLists),
                    "exportDocumentsOthercostInvoiceLists" => json_encode($getExportDocumentsOtherCostsInvoiceLists),
                    "exportDocumentsOthercostInvoiceListsCount" => count($getExportDocumentsOtherCostsInvoiceLists),
                    "exportDocumentsShippingInvoiceLists" => json_encode($getExportDocumentsShippingInvoiceLists),
                    "exportDocumentsShippingInvoiceListsCount" => count($getExportDocumentsShippingInvoiceLists),
                    "exportDocumentsDhlcostInvoiceLists" => json_encode($getExportDocumentsDhlCostsInvoiceLists),
                    "exportDocumentsDhlcostInvoiceListsCount" => count($getExportDocumentsDhlCostsInvoiceLists),
                    "exportDocumentsContainerLoadingCostInvoiceLists" => json_encode($getExportDocumentsContainerLoadingCostsInvoiceLists),
                    "exportDocumentsContainerLoadingCostInvoiceListsCount" => count($getExportDocumentsContainerLoadingCostsInvoiceLists),
                    "csrfhash" => $this->security->get_csrf_hash(),
                    "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($getExportDetails[0]->origin_id, $getExportDetails[0]->product_type_id),
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
            } else if ($this->input->get('type') == "editcustoms_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsCustomsInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 1, $invoiceId);
                if (count($getExportDocumentsCustomsInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsCustomsInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 1, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsCustomsInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsCustomsInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsCustomsInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsCustomsInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsCustomsInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsCustomsInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsCustomsInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsCustomsInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "edititr_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsCustomsInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 2, $invoiceId);
                if (count($getExportDocumentsCustomsInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsCustomsInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 2, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsCustomsInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsCustomsInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsCustomsInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsCustomsInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsCustomsInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsCustomsInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsCustomsInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsCustomsInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editport_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 3, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 3, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editshipping_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 9, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 9, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editfumigation_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 4, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 4, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editcoteros_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 6, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 6, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editphyto_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 5, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 5, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editincentives_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 7, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 7, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editremobilization_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 8, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 8, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editdhlcost_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 13, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 13, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->get('type') == "editcontainerloadingcost_invoice") {

                $invoiceId = $this->input->get('id');
                $exportId = $this->input->get('export_id');

                $getExportDocumentsPortInvoice = $this->Export_model->fetch_export_document_details_withid($exportId, 11, $invoiceId);
                if (count($getExportDocumentsPortInvoice) == 1) {

                    $date = new DateTime($getExportDocumentsPortInvoice[0]->invoice_date);
                    $formattedDate = $date->format('Y-m-d\TH:i');

                    $getExportDocumentsPortContainers = $this->Export_model->fetch_export_container_documents($exportId, 11, $invoiceId);

                    $data = array(
                        "invoice_number" => $getExportDocumentsPortInvoice[0]->invoice_no,
                        "supplier_id" => $getExportDocumentsPortInvoice[0]->supplier_id,
                        "invoice_date" => $formattedDate,
                        "original_invoice_date" => $getExportDocumentsPortInvoice[0]->invoice_date,
                        "sub_total" => $getExportDocumentsPortInvoice[0]->sub_total + 0,
                        "tax_total" => $getExportDocumentsPortInvoice[0]->tax_total + 0,
                        "allowance_total" => $getExportDocumentsPortInvoice[0]->allowance_total + 0,
                        "payable_total" => $getExportDocumentsPortInvoice[0]->payable_total + 0,
                        "container_value_total" => $getExportDocumentsPortInvoice[0]->container_value_total + 0,
                        "container_data" => json_encode($getExportDocumentsPortContainers),
                        "invoice_id" => $invoiceId,
                        "export_id" => $exportId,
                        "csrfhash" => $this->security->get_csrf_hash(),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $Return['error'] = "";
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('common_error');
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
}
