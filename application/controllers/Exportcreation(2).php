<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Exportcreation extends MY_Controller
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
        $data["title"] = $this->lang->line("createexport_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_exportcreation";
        if (!empty($session)) {
            $applicable_origins = $session["applicable_origins"];
            $data["products"] = $this->Master_model->get_product_byorigin($applicable_origins[0]->id);
            $data["producttypes"] = $this->Master_model->get_product_type();
            $data["csrfhash"] = $this->security->get_csrf_hash();

            $data["subview"] = $this->load->view("export/exportcreation", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function exportcreation_list()
    {
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            $exportContainers = $this->Export_model->all_export_containers($this->input->get("originid"), $this->input->get("pid"), $this->input->get("tid"));

            $data = array();

            foreach ($exportContainers as $r) {
                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $selectContainer = '<th class="align-middle white-space-nowrap"><div class="form-check1"><input class="form-check-input" type="checkbox" class="select-checkbox" onClick="check_box_click(this);" name="container[]" value="' . $r->dispatch_id . '" id="container_' . $r->dispatch_id . '" /></div></th>';

                $data[] = array(
                    $selectContainer,
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

    public function dialog_create_export()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {

            if ($this->input->post("type") == "createexport") {

                $dispatchIds = $this->input->post("dispatchids");
                $originId = $this->input->post("originid");

                //CHECK ORIGIN
                $checkOrigin = $this->Export_model->get_origin_count_for_export($dispatchIds);
                if ($checkOrigin[0]->cnt == 1) {

                    //CHECK PRODUCT TYPE
                    $checkProductType = $this->Export_model->get_producttype_count_for_export($dispatchIds, $originId);
                    if ($checkProductType[0]->cnt == 1) {

                        //CHECK WAREHOUSE
                        $checkWarehouse = $this->Export_model->get_warehouse_count_for_export($dispatchIds, $originId);
                        if ($checkWarehouse[0]->cnt == 1) {

                            //CHECK SHIPPING LINE
                            $checkShippingLine = $this->Export_model->get_shippingline_count_for_export($dispatchIds, $originId);
                            if ($checkShippingLine[0]->cnt == 1) {

                                $shippingLine = $this->Export_model->get_shippingline_for_export($dispatchIds, $originId);
                                $warehouse = $this->Export_model->get_warehouse_for_export($dispatchIds, $originId);
                                $productType = $this->Export_model->get_producttype_for_export($dispatchIds, $originId);

                                $data = array(
                                    "pageheading" => $this->lang->line("create_export"),
                                    "pagetype" => "createexport",
                                    "csrfhash" => $this->security->get_csrf_hash(),
                                    "formsubmit" => "exportcreation/add",
                                    "originid" => $originId,
                                    "dispatchids" => $dispatchIds,
                                    "shipping_line_name" => $shippingLine[0]->shipping_line,
                                    "shipping_line_id" => $shippingLine[0]->shipping_line_id,
                                    "warehouse_pol_id" => $warehouse[0]->pol_id,
                                    "warehouse_pol_name" => $warehouse[0]->pol_name,
                                    "warehouse_id" => $warehouse[0]->warehouse_id,
                                    "product_type_id" => $productType[0]->product_type_id,
                                    "product_type_name" => $productType[0]->product_type_name,
                                    "exportpod" => $this->Master_model->get_export_pod(),
                                    "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($originId, $productType[0]->product_type_id),
                                );

                                $this->load->view("export/dialog_create_export", $data);
                            } else {
                                $Return["redirect"] = false;
                                $Return["result"] = "";
                                $Return["pageheading"] = $this->lang->line("information");
                                $Return["pagemessage"] = $this->lang->line("error_same_shippingline");
                                $Return["messagetype"] = "info";
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return["redirect"] = false;
                            $Return["result"] = "";
                            $Return["pageheading"] = $this->lang->line("information");
                            $Return["pagemessage"] = $this->lang->line("error_same_pol");
                            $Return["messagetype"] = "info";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return["redirect"] = false;
                        $Return["result"] = "";
                        $Return["pageheading"] = $this->lang->line("information");
                        $Return["pagemessage"] = $this->lang->line("error_same_producttype");
                        $Return["messagetype"] = "info";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("error_same_origin");
                    $Return["messagetype"] = "info";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->post("type") == "dialogmeasurement") {

                $dispatchIds = $this->input->post("dispatchids");
                $originId = $this->input->post("originid");

                //CHECK ORIGIN
                $checkOrigin = $this->Export_model->get_origin_count_for_export($dispatchIds);
                if ($checkOrigin[0]->cnt == 1) {

                    //CHECK PRODUCT TYPE
                    $checkProductType = $this->Export_model->get_producttype_count_for_export($dispatchIds, $originId);
                    if ($checkProductType[0]->cnt == 1) {

                        //CHECK WAREHOUSE
                        $checkWarehouse = $this->Export_model->get_warehouse_count_for_export($dispatchIds, $originId);
                        if ($checkWarehouse[0]->cnt == 1) {

                            //CHECK SHIPPING LINE
                            $checkShippingLine = $this->Export_model->get_shippingline_count_for_export($dispatchIds, $originId);
                            if ($checkShippingLine[0]->cnt == 1) {

                                $shippingLine = $this->Export_model->get_shippingline_for_export($dispatchIds, $originId);
                                $warehouse = $this->Export_model->get_warehouse_for_export($dispatchIds, $originId);
                                $productType = $this->Export_model->get_producttype_for_export($dispatchIds, $originId);

                                $data = array(
                                    "pageheading" => $this->lang->line("generate_summary"),
                                    "pagetype" => "generate_summary",
                                    "csrfhash" => $this->security->get_csrf_hash(),
                                    "originid" => $originId,
                                    "dispatchids" => $dispatchIds,
                                    "shippingline" => $shippingLine[0]->shipping_line,
                                    "warehouse" => $warehouse[0]->pol_name,
                                    "producttype" => $productType[0]->product_type_name,
                                    "measurementsystems" => $this->Master_model->fetch_measurementsystems_by_origin($originId, $productType[0]->product_type_id),
                                );

                                $this->load->view("export/dialog_select_measurementsystem", $data);
                            } else {
                                $Return["redirect"] = false;
                                $Return["result"] = "";
                                $Return["pageheading"] = $this->lang->line("information");
                                $Return["pagemessage"] = $this->lang->line("error_same_shippingline");
                                $Return["messagetype"] = "info";
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return["redirect"] = false;
                            $Return["result"] = "";
                            $Return["pageheading"] = $this->lang->line("information");
                            $Return["pagemessage"] = $this->lang->line("error_same_pol");
                            $Return["messagetype"] = "info";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return["redirect"] = false;
                        $Return["result"] = "";
                        $Return["pageheading"] = $this->lang->line("information");
                        $Return["pagemessage"] = $this->lang->line("error_same_producttype");
                        $Return["messagetype"] = "info";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("error_same_origin");
                    $Return["messagetype"] = "info";
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return["pagemessage"] = "";
            $Return["pageheading"] = "";
            $Return["pages"] = "";
            $Return["messagetype"] = "redirect";
            $Return["pagemessage"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
            exit;
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
                    
                    if ($productTypeId == 1 || $productTypeId == 3) {
                        
                            $totalGrossVolume = 0;
                            $totalNetVolume = 0;
                            $totalPieces = 0;

                            $dataContainer = array();
                            $dispatchIdArray = explode(',', $dispatchIds);
                            foreach ($dispatchIdArray as $dispatchid) {
                                $fetchVolume = $this->Export_model->get_total_volume_square_block($dispatchid);
                                
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
                        
                    } else if ($productTypeId == 2 || $productTypeId == 4) {

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

    public function add()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "export") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "createexport") {

                    $originid = $this->input->post("originid");
                    $sanumber = strtoupper(preg_replace('/\s+/', '', $this->input->post("sanumber")));
                    $dispatchids = $this->input->post("dispatchids");
                    $producttypeid = $this->input->post("producttypeid");
                    $measurementsystemid = $this->input->post("measurementsystemid");
                    $shippingline = $this->input->post("shippingline");
                    $warehouseid = $this->input->post("warehouseid");
                    $warehousepolid = $this->input->post("warehousepolid");
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

                    $getSANumberCount = $this->Export_model->get_sa_number_count($sanumber, $originid);

                    if ($getSANumberCount[0]->cnt == 0) {


                        $dataExportDetails = array(
                            "product_id" => 0, "product_type_id" => $producttypeid,
                            "sa_number" => $sanumber, "warehouse_id" => $warehouseid,
                            "pol" => $warehousepolid, "pod" => $portofdischarge, "shipped_date" => $shippeddate,
                            "bl_no" => $blnumber, "bl_date" => $bldate, "vessel_name" => $vesselname,
                            "liner" => $shippingline, "client_pno" => $clientpno, "total_containers" => $totalcontainers,
                            "total_pieces" => $totalpiecesuploaded, "total_gross_volume" => $totalgrossvolume,
                            "total_net_volume" => $totalnetvolume, "origin_id" => $originid, "createdby" => $session['user_id'],
                            "updatedby" => $session['user_id'], "isactive" => 1, "measurement_system" => $measurementsystemid,
                        );

                        $insertExportDetails = $this->Export_model->add_export_details($dataExportDetails);

                        if ($insertExportDetails > 0) {
                            $dataExportContainerData = array();

                            $containerDataJson = json_decode($containerdata, true);

                            if (count($containerDataJson) > 0) {
                                foreach ($containerDataJson as $containerdata) {
                                    $dataExportContainerData[] = array(
                                        "container_details_id" => $insertExportDetails, "dispatch_id" => $containerdata["dispatchId"],
                                        "gross_volume" => $containerdata["grossVolume"], "net_volume" => $containerdata["netVolume"],
                                        "cft_value" => $containerdata["cftGross"], "cft_net_value" => $containerdata["cftNet"],
                                        "total_pieces" => $containerdata["totalPieces"], "createdby" => $session["user_id"], "updatedby" => $session["user_id"],
                                        "isactive" => 1, "createddate" => date("Y-m-d H:i:s"), "updateddate" => date("Y-m-d H:i:s"),
                                    );
                                }

                                $insertExportContainerData = $this->Export_model->add_export_container_data($dataExportContainerData);

                                if ($insertExportContainerData == true) {

                                    $updateExportDispatch = $this->Export_model->update_export_dispatch($dispatchids, $session['user_id']);

                                    if ($updateExportDispatch) {
                                        $Return["duplicateerror"] = "";
                                        $Return["error"] = "";
                                        $Return["result"] = $this->lang->line("data_added");
                                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    } else {
                                        $Return["duplicateerror"] = "";
                                        $Return["error"] = $this->lang->line("error_adding");
                                        $Return["result"] = "";
                                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else {
                                    $Return["duplicateerror"] = "";
                                    $Return["error"] = $this->lang->line("error_adding");
                                    $Return["result"] = "";
                                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            }
                        } else {

                            $Return["duplicateerror"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
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

    public function generate_export_summary_report()
    {
        try {
            $session = $this->session->userdata("fullname");

            $Return = array(
                "result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => ""
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $dispatchIds = $this->input->post("dispatchids");
                $originId = $this->input->post("originid");
                $measurementSystemId = $this->input->post("measurementsystem");
                $circumferenceAllowance = $this->input->post("circumferenceallowance");
                $lengthAllowance = $this->input->post("lengthallowance") / 100;
                $circumferenceAdjustment = $this->input->post("circumferenceadjustment");
                
                //CHECK ORIGIN
                $checkOrigin = $this->Export_model->get_origin_count_for_export($dispatchIds);
                if ($checkOrigin[0]->cnt == 1) {

                    //CHECK PRODUCT TYPE
                    $checkProductType = $this->Export_model->get_producttype_count_for_export($dispatchIds, $originId);
                    if ($checkProductType[0]->cnt == 1) {

                        //CHECK WAREHOUSE
                        $checkWarehouse = $this->Export_model->get_warehouse_count_for_export($dispatchIds, $originId);
                        if ($checkWarehouse[0]->cnt == 1) {

                            //CHECK SHIPPING LINE
                            $checkShippingLine = $this->Export_model->get_shippingline_count_for_export($dispatchIds, $originId);
                            if ($checkShippingLine[0]->cnt == 1) {

                                $originName = $this->Master_model->get_origin_iso3_code($originId);
                                $shippingLine = $this->Export_model->get_shippingline_for_export($dispatchIds, $originId);
                                $warehouse = $this->Export_model->get_warehouse_for_export($dispatchIds, $originId);
                                $productType = $this->Export_model->get_producttype_for_export($dispatchIds, $originId);
                                $companySettings = $this->Master_model->get_company_settings_by_origin($originId);

                                //SUMMARY REPORT

                                $this->excel->setActiveSheetIndex(0);
                                $objSheet = $this->excel->getActiveSheet();
                                $objSheet->setTitle(strtoupper($this->lang->line("report_summary")));
                                $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                                //SUMMARY PAGE HEADING

                                $objSheet->SetCellValue("E1", strtoupper("Shipment Advice"));
                                $objSheet->getStyle("E1")->getFont()->setUnderline(true);
                                $objSheet->getStyle("E1")->getFont()->setBold(true);
                                $objSheet->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $link_style_array = array('font' => array('color' => array('rgb' => '0000FF'), 'underline' => 'single'));
                                $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

                                // INSERT IMAGE
                                $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                $objDrawing->setName($this->Settings_model->site_title());
                                $objDrawing->setDescription($this->Settings_model->site_title());
                                $objDrawing->setImageResource($gdImage);
                                $objDrawing->setHeight(60);
                                $objDrawing->setCoordinates("A1");
                                $objDrawing->setWorksheet($objSheet);
                                // END IMAGE

                                //END SUMMARY PAGE HEADING

                                //SUMMARY HEADER

                                $objSheet->SetCellValue("A12", $this->lang->line("product_type"));
                                $objSheet->SetCellValue("A13", $this->lang->line("origin"));
                                $objSheet->SetCellValue("B12", $productType[0]->product_type_name);
                                $objSheet->SetCellValue("B13", $originName[0]->origin_name);

                                $objSheet->getStyle("A12:A13")->getFont()->setBold(true);
                                $objSheet->getStyle("A12:B13")->applyFromArray($styleArray);

                                $objSheet->SetCellValue("E5", $this->lang->line("sa_number"));
                                $objSheet->SetCellValue("E6", $this->lang->line("port_of_loading"));
                                $objSheet->SetCellValue("E7", $this->lang->line("port_of_discharge"));
                                $objSheet->SetCellValue("E8", $this->lang->line("shipped_date"));
                                $objSheet->SetCellValue("E9", $this->lang->line("bl_number"));
                                $objSheet->SetCellValue("E10", $this->lang->line("bl_date"));
                                $objSheet->SetCellValue("E11", $this->lang->line("vessel_name"));
                                $objSheet->SetCellValue("E12", $this->lang->line("shipping_line"));
                                $objSheet->SetCellValue("E13", $this->lang->line("no_of_fcls"));
                                $objSheet->SetCellValue("E14", $this->lang->line("eta_destination"));

                                $objSheet->SetCellValue("F6", $warehouse[0]->pol_name);
                                $objSheet->SetCellValue("F12", $shippingLine[0]->shipping_line);

                                $objSheet->getStyle("E5:E14")->getFont()->setBold(true);
                                $objSheet->getStyle("E5:F14")->applyFromArray($styleArray);

                                //END SUMMARY HEADER

                                if ($productType[0]->product_type_id == 1 || $productType[0]->product_type_id == 3) {

                                    //SUMMARY DATA HEADER

                                    $objSheet->SetCellValue("A17", $this->lang->line("client_pno"));
                                    $objSheet->SetCellValue("B17", $this->lang->line("stuffing_date"));
                                    $objSheet->SetCellValue("C17", $this->lang->line("container_number"));
                                    $objSheet->SetCellValue("D17", $this->lang->line("length"));
                                    $objSheet->SetCellValue("E17", $this->lang->line("width"));
                                    $objSheet->SetCellValue("F17", $this->lang->line("thickness"));
                                    $objSheet->SetCellValue("G17", $this->lang->line("volume_pie"));
                                    $objSheet->SetCellValue("H17", $this->lang->line("gross_volume"));
                                    $objSheet->SetCellValue("I17", $this->lang->line("net_volume"));
                                    $objSheet->SetCellValue("J17", $this->lang->line("pieces"));
                                    $objSheet->SetCellValue("K17", $this->lang->line("text_cft"));
                                    $objSheet->SetCellValue("L17", $this->lang->line("photo_link"));

                                    $objSheet->getStyle("A17:L17")->getFont()->setBold(true);
                                    $objSheet->getStyle("A17:L17")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                                    $objSheet->getStyle("A17:L17")->applyFromArray($styleArray);

                                    //END SUMMARY DATA HEADER
                                    
                                    //CONTAINER SHEET
                                    $getContainerDetails = $this->Export_model->get_container_details($dispatchIds, $originId);
                                    
                                    $sheetNo = 1;
                                    $summaryDataCount = 18;
                                    
                                    if (count($getContainerDetails) > 0) {
                                        foreach ($getContainerDetails as $containerdetails) {

                                            $objWorkSheet = $this->excel->createSheet($sheetNo);
                                            $objWorkSheet->setTitle(strtoupper($containerdetails->container_number));

                                            // INSERT IMAGE
                                            $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                            $objDrawing->setName($this->Settings_model->site_title());
                                            $objDrawing->setDescription($this->Settings_model->site_title());
                                            $objDrawing->setImageResource($gdImage);
                                            $objDrawing->setHeight(60);
                                            $objDrawing->setCoordinates("J10");
                                            $objDrawing->setWorksheet($objWorkSheet);
                                            // END IMAGE

                                            //HEADER

                                            $objWorkSheet->SetCellValue("A2", $this->lang->line("container_number"));
                                            $objWorkSheet->SetCellValue("A3", $this->lang->line("load_date"));
                                            $objWorkSheet->SetCellValue("A4", $this->lang->line("loading_place"));
                                            $objWorkSheet->SetCellValue("A5", $this->lang->line("seal_number"));
                                            $objWorkSheet->SetCellValue("A6", $this->lang->line("pieces"));
                                            $objWorkSheet->SetCellValue("A7", $this->lang->line("length"));
                                            $objWorkSheet->SetCellValue("A8", $this->lang->line("width"));
                                            $objWorkSheet->SetCellValue("A9", $this->lang->line("thickness"));
                                            $objWorkSheet->SetCellValue("A10", $this->lang->line("total_gross_volume"));
                                            $objWorkSheet->SetCellValue("A11", $this->lang->line("total_net_volume"));
                                            $objWorkSheet->SetCellValue("A12", $this->lang->line("volume_pie"));
                                            $objWorkSheet->SetCellValue("A13", $this->lang->line("text_cft"));

                                            $objWorkSheet->SetCellValue("B2", $containerdetails->container_number);
                                            $objWorkSheet->SetCellValue("B3", $containerdetails->dispatch_date);
                                            $objWorkSheet->SetCellValue("B4", $containerdetails->warehouse_name);

                                            $objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B13")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                            $objWorkSheet->getStyle("A2")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A3")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A4")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A5")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A6")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A7")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A8")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A9")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A10")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A11")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A12")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A13")->getFont()->setBold(true);

                                            $objWorkSheet->getStyle("A2:B13")->applyFromArray($styleArray);
                                            
                                            $objWorkSheet->SetCellValue("A15", $this->lang->line("length"));
                                            $objWorkSheet->SetCellValue("B15", $this->lang->line("width"));
                                            $objWorkSheet->SetCellValue("C15", $this->lang->line("thickness"));
                                            $objWorkSheet->SetCellValue("D15", $this->lang->line("pieces"));
                                            $objWorkSheet->SetCellValue("E15", $this->lang->line("volume_pie"));
                                            $objWorkSheet->SetCellValue("F15", $this->lang->line("length_export"));
                                            $objWorkSheet->SetCellValue("G15", $this->lang->line("width_export"));
                                            $objWorkSheet->SetCellValue("H15", $this->lang->line("thickness_export"));
                                            $objWorkSheet->SetCellValue("I15", $this->lang->line("gross_volume"));
                                            $objWorkSheet->SetCellValue("J15", $this->lang->line("net_volume"));
                                            $objWorkSheet->SetCellValue("K15", $this->lang->line("grade"));

                                            $objWorkSheet->getStyle("A15:K15")->getFont()->setBold(true);
                                            $objWorkSheet->getRowDimension(15)->setRowHeight(30);
                                            $objWorkSheet->getStyle("A15:K15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                            $objWorkSheet->getStyle("A15:K15")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                            $objWorkSheet->getStyle("A15:K15")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
                                            $objWorkSheet->setAutoFilter("A15:K15");

                                            //END HEADER

                                            //DISPATCH DATA

                                            $getDispatchData = $this->Export_model->get_container_dispatch_data_square_blocks($containerdetails->dispatch_id, $containerdetails->container_number, $originId);
                                            
                                            // $getDispatchData = $this->Export_model->get_container_dispatch_data_old($containerdetails->dispatch_id, $containerdetails->container_number, $originId);

                                            $dispatchDataCount = 16;
                                            $textGrade1 = '"'.$this->lang->line('grade1').'"';
                                            $textGrade2 = '"'.$this->lang->line('grade2').'"';
                                            $textGrade3 = '"'.$this->lang->line('grade3').'"';
                                            if (count($getDispatchData) > 0) {
                                                foreach ($getDispatchData as $dispatchdata) {

                                                    $objWorkSheet->SetCellValue("A$dispatchDataCount", ($dispatchdata->length_bought + 0));
                                                    $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->width_bought + 0));
                                                    $objWorkSheet->SetCellValue("C$dispatchDataCount", ($dispatchdata->thickness_bought + 0));
                                                    $objWorkSheet->SetCellValue("D$dispatchDataCount", ($dispatchdata->dispatch_pieces + 0));
                                                    $objWorkSheet->SetCellValue("E$dispatchDataCount", "=ROUND(A$dispatchDataCount * B$dispatchDataCount * C$dispatchDataCount / 12 ,2)");
                                                    $objWorkSheet->SetCellValue("F$dispatchDataCount", "=A$dispatchDataCount * 0.3");
                                                    $objWorkSheet->SetCellValue("G$dispatchDataCount", "=TRUNC(B$dispatchDataCount * 2.54, 0)");
                                                    $objWorkSheet->SetCellValue("H$dispatchDataCount", "=ROUND(C$dispatchDataCount * 2.54, 0)");
                                                    $objWorkSheet->SetCellValue("I$dispatchDataCount", "=ROUND(E$dispatchDataCount/424,3)");
                                                    $objWorkSheet->SetCellValue("J$dispatchDataCount", "=ROUND(F$dispatchDataCount * G$dispatchDataCount * H$dispatchDataCount / 10000, 3)");
                                                    $objWorkSheet->SetCellValue("K$dispatchDataCount", "=IF(G$dispatchDataCount<15, ".$textGrade1." ,IF(H$dispatchDataCount<15, ".$textGrade1." ,IF(G$dispatchDataCount>19.9, ".$textGrade3." ,IF(H$dispatchDataCount>19.9, ".$textGrade3." ,".$textGrade2."))))");

                                                    $dispatchDataCount++;
                                                }
                                            }

                                            $lastRow = $dispatchDataCount - 1;
                                            $objWorkSheet->getStyle("I15:J$lastRow")
                                                ->getNumberFormat()
                                                ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                            $objWorkSheet->getStyle("A15:K$lastRow")->applyFromArray($styleArray);

                                            $objWorkSheet->getColumnDimension("A")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("B")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("C")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("D")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("E")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("F")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("G")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("H")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("I")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("J")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("K")->setAutoSize(true);

                                            //END DATA

                                            //RENDER HEADER DATA
                                            
                                            $cftText = "&" .'"' . " x " .'"'. "&";

                                            $objWorkSheet->SetCellValue("B6", "=SUM(D16:D$lastRow)");
                                            $objWorkSheet->SetCellValue("B7", "=ROUND(AVERAGE(A16:A$lastRow),2)");
                                            $objWorkSheet->SetCellValue("B8", "=ROUND(AVERAGE(B16:B$lastRow),2)");
                                            $objWorkSheet->SetCellValue("B9", "=ROUND(AVERAGE(C16:C$lastRow),2)");
                                            $objWorkSheet->SetCellValue("B10", "=SUM(I16:I$lastRow)");
                                            $objWorkSheet->SetCellValue("B11", "=SUM(J16:J$lastRow)");
                                            $objWorkSheet->SetCellValue("B12", "=SUM(E16:E$lastRow)");
                                            $objWorkSheet->SetCellValue("B13", "=B8".$cftText."B9");
                                            
                                            $objWorkSheet->mergeCells('F2:H2');
                                            $objWorkSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                                            $objWorkSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                            
                                            $objWorkSheet->SetCellValue('G3', $this->lang->line("text_volume"));
                                            $objWorkSheet->SetCellValue('H3', $this->lang->line("ages"));
                        
                                            $objWorkSheet->SetCellValue('F4', $this->lang->line("grade1"));
                                            $objWorkSheet->SetCellValue('F5', $this->lang->line("grade2"));
                                            $objWorkSheet->SetCellValue('F6', $this->lang->line("grade3"));
                                            $objWorkSheet->SetCellValue('F7', $this->lang->line("total"));
                                            
                                            $objWorkSheet->SetCellValue('G4', "=SUMIF(K16:K$lastRow,F4,J16:J$lastRow)");
                                            $objWorkSheet->SetCellValue('G5', "=SUMIF(K16:K$lastRow,F5,J16:J$lastRow)");
                                            $objWorkSheet->SetCellValue('G6', "=SUMIF(K16:K$lastRow,F6,J16:J$lastRow)");
                                            $objWorkSheet->SetCellValue('G7', "=SUM(G4:G6)");
                                            
                                            $objWorkSheet->SetCellValue('H4', "=G4/G7");
                                            $objWorkSheet->SetCellValue('H5', "=G5/G7");
                                            $objWorkSheet->SetCellValue('H6', "=G6/G7");
                                            $objWorkSheet->SetCellValue('H7', "=SUM(H4:H6)");
                                            
                                            $objWorkSheet->getStyle('H4:H7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                                            
                                            $objWorkSheet->getStyle('H7')->getFont()->setBold(true);
                                            
                                            $BStyle = array(
                                            'borders' => array(
                                                'outline' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                                )
                                            )
                                            );
                                            $objWorkSheet->getStyle('F2:H7')->applyFromArray($styleArray);
                                            $objWorkSheet->getStyle('F2:H7')->applyFromArray($BStyle);
                                            unset($BStyle);

                                            //END RENDER HEADER DATA

                                            //SUMMARY DATA

                                            $objSheet->SetCellValue("B$summaryDataCount", $containerdetails->dispatch_date);
                                            $objSheet->SetCellValue("C$summaryDataCount", $containerdetails->container_number);
                                            $objSheet->SetCellValue("D$summaryDataCount", "='$containerdetails->container_number'!B7");
                                            $objSheet->SetCellValue("E$summaryDataCount", "='$containerdetails->container_number'!B8");
                                            $objSheet->SetCellValue("F$summaryDataCount", "='$containerdetails->container_number'!B9");
                                            $objSheet->SetCellValue("G$summaryDataCount", "='$containerdetails->container_number'!B12");
                                            $objSheet->SetCellValue("H$summaryDataCount", "='$containerdetails->container_number'!B10");
                                            $objSheet->SetCellValue("I$summaryDataCount", "='$containerdetails->container_number'!B11");
                                            $objSheet->SetCellValue("J$summaryDataCount", "='$containerdetails->container_number'!B6");
                                            $objSheet->SetCellValue("K$summaryDataCount", "='$containerdetails->container_number'!B13");

                                            if ($containerdetails->container_pic_url != "" && $containerdetails->container_pic_url != null) {

                                                $objSheet->SetCellValue("L$summaryDataCount", $containerdetails->container_pic_url);
                                                $objSheet->getCell("L$summaryDataCount")->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
                                                $objSheet->getCell("L$summaryDataCount")->getHyperlink()->setUrl(strip_tags($containerdetails->container_pic_url));
                                                $objSheet->getStyle("L$summaryDataCount")->applyFromArray($link_style_array);
                                            } else {
                                                $objSheet->SetCellValue("L$summaryDataCount", "");
                                            }

                                            //END SUMMARY

                                            $sheetNo++;
                                            $summaryDataCount++;
                                        }
                                    }
                                    //END CONTAINER SHEET
                                    
                                } else if ($productType[0]->product_type_id == 2 || $productType[0]->product_type_id == 4) {

                                    $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($measurementSystemId, $originId);

                                    $strGrossFormula = "";
                                    $strNetFormula = "";

                                    if (count($getFormulae) > 0) {
                                        foreach ($getFormulae as $formula) {
            
                                            if ($formula->context == "CBM_HOPPUS_GROSSVOLUME_EXPORT" || $formula->context == "CBM_GEO_GROSSVOLUME_EXPORT") {
                                                $strGrossFormula = str_replace(array('$l', '$c', '$pcs', 'truncate'), array("###", "$$$", "!!!", '$this->truncate'), $formula->calculation_formula);
                                            }
            
                                            if ($formula->context == "CBM_HOPPUS_NETVOLUME_EXPORT" || $formula->context == "CBM_GEO_NETVOLUME_EXPORT") {
                                                // $strNetFormula = str_replace(array('$l', '$c', '$pcs', 'truncate'), array("###", "$$$", "!!!", '$this->truncate'), $formula->calculation_formula);
                                                
                                                 $strNetFormula = str_replace(array('$l', '$c', '$pcs', 'truncate', '$ac', '$al'), array("###", "$$$", "!!!", '$this->truncate', $circumferenceAllowance, $lengthAllowance), $formula->calculation_formula);
                                            }
                                        }
                                    }

                                    //SUMMARY DATA HEADER

                                    $objSheet->SetCellValue("A17", $this->lang->line("client_pno"));
                                    $objSheet->SetCellValue("B17", $this->lang->line("stuffing_date"));
                                    $objSheet->SetCellValue("C17", $this->lang->line("container_number"));
                                    $objSheet->SetCellValue("D17", $this->lang->line("length"));
                                    $objSheet->SetCellValue("E17", $this->lang->line("circumference"));
                                    $objSheet->SetCellValue("F17", $this->lang->line("gross_volume"));
                                    $objSheet->SetCellValue("G17", $this->lang->line("net_volume"));
                                    $objSheet->SetCellValue("H17", $this->lang->line("pieces"));
                                    $objSheet->SetCellValue("I17", $this->lang->line("text_cft"));
                                    $objSheet->SetCellValue("J17", $this->lang->line("photo_link"));

                                    $objSheet->getStyle("A17:J17")->getFont()->setBold(true);
                                    $objSheet->getStyle("A17:J17")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                                    $objSheet->getStyle("A17:J17")->applyFromArray($styleArray);

                                    //END SUMMARY DATA HEADER

                                    //CONTAINER SHEET
                                    $getContainerDetails = $this->Export_model->get_container_details($dispatchIds, $originId);

                                    $sheetNo = 1;
                                    $summaryDataCount = 18;

                                    if (count($getContainerDetails) > 0) {
                                        foreach ($getContainerDetails as $containerdetails) {

                                            $objWorkSheet = $this->excel->createSheet($sheetNo);
                                            $objWorkSheet->setTitle(strtoupper($containerdetails->container_number));

                                            // INSERT IMAGE
                                            $gdImage = imagecreatefrompng("./assets/img/iconz/cgrlogo_white.png");
                                            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                                            $objDrawing->setName($this->Settings_model->site_title());
                                            $objDrawing->setDescription($this->Settings_model->site_title());
                                            $objDrawing->setImageResource($gdImage);
                                            $objDrawing->setHeight(60);
                                            $objDrawing->setCoordinates("H10");
                                            $objDrawing->setWorksheet($objWorkSheet);
                                            // END IMAGE

                                            //HEADER

                                            $objWorkSheet->SetCellValue("A2", $this->lang->line("container_number"));
                                            $objWorkSheet->SetCellValue("A3", $this->lang->line("load_date"));
                                            $objWorkSheet->SetCellValue("A4", $this->lang->line("loading_place"));
                                            $objWorkSheet->SetCellValue("A5", $this->lang->line("seal_number"));
                                            $objWorkSheet->SetCellValue("A6", $this->lang->line("pieces"));
                                            $objWorkSheet->SetCellValue("A7", $this->lang->line("length"));
                                            $objWorkSheet->SetCellValue("A8", $this->lang->line("circumference"));
                                            $objWorkSheet->SetCellValue("A9", $this->lang->line("circumference_allowance"));
                                            $objWorkSheet->SetCellValue("A10", $this->lang->line("length_allowance"));
                                            $objWorkSheet->SetCellValue("A11", $this->lang->line("total_gross_volume"));
                                            $objWorkSheet->SetCellValue("A12", $this->lang->line("total_net_volume"));
                                            $objWorkSheet->SetCellValue("A13", $this->lang->line("text_cft"));

                                            $objWorkSheet->SetCellValue("B2", $containerdetails->container_number);
                                            $objWorkSheet->SetCellValue("B3", $containerdetails->dispatch_date);
                                            $objWorkSheet->SetCellValue("B4", $containerdetails->warehouse_name);
                                            $objWorkSheet->SetCellValue("B9", ($companySettings[0]->circumference_allowance_export + 0));
                                            $objWorkSheet->SetCellValue("B10", ($companySettings[0]->length_allowance_export + 0));

                                            $objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                            $objWorkSheet->getStyle("B13")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                            $objWorkSheet->getStyle("A2")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A3")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A4")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A5")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A6")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A7")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A8")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A9")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A10")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A11")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A12")->getFont()->setBold(true);
                                            $objWorkSheet->getStyle("A13")->getFont()->setBold(true);

                                            $objWorkSheet->getStyle("A2:B13")->applyFromArray($styleArray);

                                            $objWorkSheet->SetCellValue("A15", $this->lang->line("dispatch_pieces"));
                                            $objWorkSheet->SetCellValue("B15", $this->lang->line("circumference"));
                                            $objWorkSheet->SetCellValue("C15", $this->lang->line("length"));
                                            $objWorkSheet->SetCellValue("D15", $this->lang->line("gross_volume"));
                                            $objWorkSheet->SetCellValue("E15", $this->lang->line("net_volume"));

                                            $objWorkSheet->getStyle("A15:E15")->getFont()->setBold(true);
                                            $objWorkSheet->getRowDimension(15)->setRowHeight(30);
                                            $objWorkSheet->getStyle("A15:E15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                            $objWorkSheet->getStyle("A15:E15")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                            $objWorkSheet->getStyle("A15:E15")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
                                            $objWorkSheet->setAutoFilter("A15:E15");

                                            //END HEADER

                                            //DISPATCH DATA

                                            $getDispatchData = $this->Export_model->get_container_dispatch_data($containerdetails->dispatch_id, $containerdetails->container_number, $originId);
                                            
                                            // $getDispatchData = $this->Export_model->get_container_dispatch_data_old($containerdetails->dispatch_id, $containerdetails->container_number, $originId);

                                            $dispatchDataCount = 16;
                                            if (count($getDispatchData) > 0) {
                                                foreach ($getDispatchData as $dispatchdata) {

                                                    // $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                    // $grossFormula = "return (" . $grossFormula . ");";
                                                    // $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    // $netFormula = "return (" . $netFormula . ");";
                                                    
                                                    // if($originId == 2) {
                                                    //     if($circumferenceAdjustment < 0) {
                                                    //         $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                    //         $grossFormula = "return (" . $grossFormula . ");";
                                                            
                                                    //         $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    //         $netFormula = "return (" . $netFormula . ");";
                                                    //     } else {
                                                    //         $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                            
                                                    //         $grossFormula = "return (" . $grossFormula . ");";
                                                            
                                                    //         $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    //         $netFormula = "return (" . $netFormula . ");";
                                                    //     }
                                                    // } else {
                                                    //     $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                    //     $grossFormula = "return (" . $grossFormula . ");";
                                                        
                                                    //     $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                    //     $netFormula = "return (" . $netFormula . ");";
                                                    // }
                                                    
                                                    if($originId == 1 || $originId == 2) {
                                                        if($circumferenceAdjustment < 0) {
                                                            $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                            $grossFormula = "return (" . $grossFormula . ");";
                                                            
                                                            $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought - abs($circumferenceAdjustment))", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                            $netFormula = "return (" . $netFormula . ");";
                                                        } else {
                                                            $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                            
                                                            $grossFormula = "return (" . $grossFormula . ");";
                                                            
                                                            $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + $circumferenceAdjustment)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                            $netFormula = "return (" . $netFormula . ");";
                                                        }
                                                    } else {
                                                        $grossFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strGrossFormula);
                                                        $grossFormula = "return (" . $grossFormula . ");";
                                                        
                                                        $netFormula = str_replace(array("###", "$$$", "!!!"), array("($dispatchdata->length_bought / 100)", "($dispatchdata->circumference_bought + 0)", "($dispatchdata->scanned_code + 0)"), $strNetFormula);
                                                        $netFormula = "return (" . $netFormula . ");";
                                                    }

                                                    $objWorkSheet->SetCellValue("A$dispatchDataCount", ($dispatchdata->scanned_code + 0));
                                                    // $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + 0));
                                                    
                                                    // if($originId == 2) {
                                                    //     if($circumferenceAdjustment < 0) {
                                                    //         $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought - abs($circumferenceAdjustment)));
                                                    //     } else {
                                                    //         $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + $circumferenceAdjustment));
                                                    //     }
                                                    // } else {
                                                    //     $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + 0));
                                                    // }
                                                    
                                                    if($originId == 1 || $originId == 2) {
                                                        if($circumferenceAdjustment < 0) {
                                                            $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought - abs($circumferenceAdjustment)));
                                                        } else {
                                                            $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + $circumferenceAdjustment));
                                                        }
                                                    } else {
                                                        $objWorkSheet->SetCellValue("B$dispatchDataCount", ($dispatchdata->circumference_bought + 0));
                                                    }
                                                    
                                                    $objWorkSheet->SetCellValue("C$dispatchDataCount", ($dispatchdata->length_bought / 100));
                                                    $objWorkSheet->SetCellValue("D$dispatchDataCount", sprintf('%0.3f', eval($grossFormula)));
                                                    $objWorkSheet->SetCellValue("E$dispatchDataCount", sprintf('%0.3f', eval($netFormula)));

                                                    $dispatchDataCount++;
                                                }
                                            }

                                            $lastRow = $dispatchDataCount - 1;
                                            $objWorkSheet->getStyle("D15:E$lastRow")
                                                ->getNumberFormat()
                                                ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                            $objWorkSheet->getStyle("A15:E$lastRow")->applyFromArray($styleArray);

                                            $objWorkSheet->getColumnDimension("A")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("B")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("C")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("D")->setAutoSize(true);
                                            $objWorkSheet->getColumnDimension("E")->setAutoSize(true);

                                            //END DATA

                                            //RENDER HEADER DATA

                                            $objWorkSheet->SetCellValue("B6", "=SUM(A16:A$lastRow)");
                                            $objWorkSheet->SetCellValue("B7", "=ROUND(SUMPRODUCT(C16:C$lastRow,A16:A$lastRow)/B6,2)");
                                            $objWorkSheet->SetCellValue("B8", "=TRUNC(SUMPRODUCT(B16:B$lastRow,A16:A$lastRow)/B6,2)");
                                            $objWorkSheet->SetCellValue("B11", "=SUM(D16:D$lastRow)");
                                            $objWorkSheet->SetCellValue("B12", "=SUM(E16:E$lastRow)");
                                            $objWorkSheet->SetCellValue("B13", "=ROUND(B11/B6*35.315,2)");

                                            //END RENDER HEADER DATA

                                            //SUMMARY DATA

                                            $objSheet->SetCellValue("B$summaryDataCount", $containerdetails->dispatch_date);
                                            $objSheet->SetCellValue("C$summaryDataCount", $containerdetails->container_number);
                                            $objSheet->SetCellValue("D$summaryDataCount", "='$containerdetails->container_number'!B7");
                                            $objSheet->SetCellValue("E$summaryDataCount", "='$containerdetails->container_number'!B8");
                                            $objSheet->SetCellValue("F$summaryDataCount", "='$containerdetails->container_number'!B11");
                                            $objSheet->SetCellValue("G$summaryDataCount", "='$containerdetails->container_number'!B12");
                                            $objSheet->SetCellValue("H$summaryDataCount", "='$containerdetails->container_number'!B6");
                                            $objSheet->SetCellValue("I$summaryDataCount", "='$containerdetails->container_number'!B13");


                                            if ($containerdetails->container_pic_url != "" && $containerdetails->container_pic_url != null) {

                                                $objSheet->SetCellValue("J$summaryDataCount", $containerdetails->container_pic_url);
                                                $objSheet->getCell("J$summaryDataCount")->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
                                                $objSheet->getCell("J$summaryDataCount")->getHyperlink()->setUrl(strip_tags($containerdetails->container_pic_url));
                                                $objSheet->getStyle("J$summaryDataCount")->applyFromArray($link_style_array);
                                            } else {
                                                $objSheet->SetCellValue("J$summaryDataCount", "");
                                            }

                                            //END SUMMARY

                                            $sheetNo++;
                                            $summaryDataCount++;
                                        }
                                    }
                                    //END CONTAINER SHEET
                                    
                                }

                                //END SUMMARY REPORT

                                $this->excel->setActiveSheetIndex(0);

                                $lastRowSummary = $summaryDataCount - 1;
                                
                                if ($productType[0]->product_type_id == 1 || $productType[0]->product_type_id == 3) {
                                    
                                    $objSheet->getStyle("A18:L$lastRowSummary")->applyFromArray($styleArray);
    
                                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                                    $objSheet->getColumnDimension("D")->setAutoSize(true);
                                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                                    $objSheet->getColumnDimension("F")->setAutoSize(true);
                                    $objSheet->getColumnDimension("G")->setAutoSize(true);
                                    $objSheet->getColumnDimension("H")->setAutoSize(true);
                                    $objSheet->getColumnDimension("I")->setAutoSize(true);
                                    $objSheet->getColumnDimension("J")->setAutoSize(true);
                                    $objSheet->getColumnDimension("K")->setAutoSize(true);
                                    $objSheet->getColumnDimension("L")->setAutoSize(true);
                                    
                                    $summaryDataCount = $summaryDataCount + 1;
                                    $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->getFont()->setBold(true);
                                    $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                                    $objSheet->getStyle("A$summaryDataCount:L$summaryDataCount")->applyFromArray($styleArray);
                                    
                                    $objSheet->SetCellValue("C$summaryDataCount", count($getContainerDetails) . " " . $this->lang->line("containers"));
                                    $objSheet->SetCellValue("D$summaryDataCount", "=ROUND(AVERAGE(D18:D$lastRowSummary),2)");
                                    $objSheet->SetCellValue("E$summaryDataCount", "=ROUND(AVERAGE(E18:E$lastRowSummary),2)");
                                    $objSheet->SetCellValue("F$summaryDataCount", "=ROUND(AVERAGE(F18:F$lastRowSummary),2)");
                                    $objSheet->SetCellValue("H$summaryDataCount", "=SUM(H18:H$lastRowSummary)");
                                    $objSheet->SetCellValue("I$summaryDataCount", "=SUM(I18:I$lastRowSummary)");
                                    $objSheet->SetCellValue("J$summaryDataCount", "=SUM(J18:J$lastRowSummary)");

                                    
                                    
                                } else {
                                
                                    $objSheet->getStyle("A18:J$lastRowSummary")->applyFromArray($styleArray);
    
                                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                                    $objSheet->getColumnDimension("C")->setAutoSize(true);
                                    $objSheet->getColumnDimension("D")->setAutoSize(true);
                                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                                    $objSheet->getColumnDimension("F")->setAutoSize(true);
                                    $objSheet->getColumnDimension("G")->setAutoSize(true);
                                    $objSheet->getColumnDimension("H")->setAutoSize(true);
                                    $objSheet->getColumnDimension("I")->setAutoSize(true);
                                    $objSheet->getColumnDimension("J")->setAutoSize(true);
                                    
                                    $summaryDataCount = $summaryDataCount + 1;
                                    $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFont()->setBold(true);
                                    $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C4D79B");
                                    $objSheet->getStyle("A$summaryDataCount:J$summaryDataCount")->applyFromArray($styleArray);
                                    
                                    $objSheet->SetCellValue("C$summaryDataCount", count($getContainerDetails) . " " . $this->lang->line("containers"));
                                    $objSheet->SetCellValue("D$summaryDataCount", "=ROUND(AVERAGE(D18:D$lastRowSummary),2)");
                                    $objSheet->SetCellValue("E$summaryDataCount", "=ROUND(AVERAGE(E18:E$lastRowSummary),2)");
                                    $objSheet->SetCellValue("F$summaryDataCount", "=SUM(F18:F$lastRowSummary)");
                                    $objSheet->SetCellValue("G$summaryDataCount", "=SUM(G18:G$lastRowSummary)");
                                    $objSheet->SetCellValue("H$summaryDataCount", "=SUM(H18:H$lastRowSummary)");
                                    $objSheet->SetCellValue("I$summaryDataCount", "=ROUND(AVERAGE(I18:I$lastRowSummary),2)");
    
                                }

                                $summaryDataCount = $summaryDataCount + 2;

                                $BStyle = array(
                                    'borders' => array(
                                        'bottom' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                                        )
                                    )
                                );
                                $objSheet->getStyle("A$summaryDataCount:E$summaryDataCount")->applyFromArray($BStyle);
                                $summaryDataCount++;
                                $objSheet->mergeCells("A$summaryDataCount:E$summaryDataCount");
                                $objSheet->SetCellValue("A$summaryDataCount", $this->lang->line("seller_text"));
                                $objSheet->getStyle("A$summaryDataCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objSheet->getSheetView()->setZoomScale(95);

                                unset($link_style_array);
                                unset($styleArray);
                                unset($BStyle);
                                $six_digit_random_number = mt_rand(100000, 999999);
                                $month_name = ucfirst(date("dmY"));

                                $filename =  "ExportSummaryReport_" . $month_name . "_" . $six_digit_random_number . ".xlsx";

                                header('Content-Type: application/vnd.ms-excel');
                                header('Content-Disposition: attachment;filename="' . $filename . '"');
                                header('Cache-Control: max-age=0');

                                $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                                $objWriter->save("./reports/ExportReports/" . $filename);
                                $objWriter->setPreCalculateFormulas(true);
                                $Return["error"] = "";
                                $Return["result"] = site_url() . "reports/ExportReports/" . $filename;
                                $Return["successmessage"] = $this->lang->line("report_downloaded");
                                if ($Return["result"] != "") {
                                    $this->output($Return);
                                }
                            } else {
                                $Return["redirect"] = false;
                                $Return["result"] = "";
                                $Return["pageheading"] = $this->lang->line("information");
                                $Return["pagemessage"] = $this->lang->line("error_same_shippingline");
                                $Return["messagetype"] = "info";
                                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return["redirect"] = false;
                            $Return["result"] = "";
                            $Return["pageheading"] = $this->lang->line("information");
                            $Return["pagemessage"] = $this->lang->line("error_same_pol");
                            $Return["messagetype"] = "info";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return["redirect"] = false;
                        $Return["result"] = "";
                        $Return["pageheading"] = $this->lang->line("information");
                        $Return["pagemessage"] = $this->lang->line("error_same_producttype");
                        $Return["messagetype"] = "info";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return["redirect"] = false;
                    $Return["result"] = "";
                    $Return["pageheading"] = $this->lang->line("information");
                    $Return["pagemessage"] = $this->lang->line("error_same_origin");
                    $Return["messagetype"] = "info";
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

    public function truncate($val, $f = "0")
    {
        if (($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/ExportReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
