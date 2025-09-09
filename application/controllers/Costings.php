<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Costings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Costing_model");
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
        $data["title"] = $this->lang->line("costings") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_costings";
        if (!empty($session)) {

            //$data["machines"] = $this->Costing_model->fetch_machines_masters();
            $data["extraction_cost"] = $this->Costing_model->get_extraction_cost()[0]->extraction_cost_farm;
            $data["csrfhash"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("costings/costing", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function save_farm_costing()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "farmcosting") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "saveExtraction") {

                    $originId = $this->input->post("originId");
                    $extractionDate = $this->input->post('extractionDate');
                    $extractionQuantity = $this->input->post('extractionQuantity');
                    $extractionTotalValue = $this->input->post("extractionTotalValue");
                    $extractionClaimRemarks = $this->input->post("extractionClaimRemarks");
                    $extractionSuppliers = $this->input->post("extractionSuppliers");
                    $editId = $this->input->post("edit_id");
                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $extractionSuppliers,
                            "quantity" => $extractionQuantity,
                            "amount" => $extractionTotalValue,
                            "expense_date" => $extractionDate,
                            "remarks" => $extractionClaimRemarks,
                            "expense_type" => 0,
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Costing_model->update_farm_costing($dataCosting, $editId);

                        if ($updateCosting > 0) {
                            $Return["result"] = $this->lang->line("data_updated");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $dataCosting = array(
                            "cost_type" => $costType,
                            "supplier_id" => $extractionSuppliers,
                            "quantity" => $extractionQuantity,
                            "amount" => $extractionTotalValue,
                            "expense_date" => $extractionDate,
                            "remarks" => $extractionClaimRemarks,
                            "expense_type" => 0,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Costing_model->add_farm_costing($dataCosting);

                        if ($insertCosting > 0) {
                            $Return["result"] = $this->lang->line("data_added");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else if ($this->input->post("action_type") == "saveACPM") {

                    $originId = $this->input->post("originId");
                    $acpmDate = $this->input->post('acpmDate');
                    $acpmQuantity = $this->input->post('acpmQuantity');
                    $acpmTotalValue = $this->input->post("acpmTotalValue");
                    $acpmClaimRemarks = $this->input->post("acpmClaimRemarks");
                    $isPurchasedSpend = $this->input->post("isPurchasedSpend");
                    $acpmSuppliers = $this->input->post("acpmSuppliers");
                    $acpmPurchaser = $this->input->post("acpmPurchaser");
                    $acpmInvoiceNumber = $this->input->post("acpmInvoiceNumber");
                    $editId = $this->input->post("edit_id");

                    $expenseType = 0;
                    if ($isPurchasedSpend == 1) {
                        $acpmTotalValue = 0;
                        $expenseType = 1;
                    }

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $acpmSuppliers,
                            "purchaser_id" => $acpmPurchaser,
                            "invoice_number" => $acpmInvoiceNumber,
                            "quantity" => $acpmQuantity,
                            "amount" => $acpmTotalValue,
                            "expense_date" => $acpmDate,
                            "remarks" => $acpmClaimRemarks,
                            "expense_type" => $expenseType,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Costing_model->update_farm_costing($dataCosting, $editId);

                        if ($updateCosting > 0) {
                            $Return["result"] = $this->lang->line("data_updated");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $dataCosting = array(
                            "cost_type" => $costType,
                            "supplier_id" => $acpmSuppliers,
                            "purchaser_id" => $acpmPurchaser,
                            "invoice_number" => $acpmInvoiceNumber,
                            "quantity" => $acpmQuantity,
                            "amount" => $acpmTotalValue,
                            "expense_date" => $acpmDate,
                            "remarks" => $acpmClaimRemarks,
                            "expense_type" => $expenseType,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Costing_model->add_farm_costing($dataCosting);

                        if ($insertCosting > 0) {
                            $Return["result"] = $this->lang->line("data_added");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else if ($this->input->post("action_type") == "saveMaintenance") {

                    $originId = $this->input->post("originId");
                    $maintainanceDate = $this->input->post('maintainanceDate');
                    $maintainanceMachineType = $this->input->post('maintainanceMachineType');
                    $maintainanceConcept = $this->input->post("maintainanceConcept");
                    $maintainanceSubTotal = $this->input->post("maintainanceSubTotal");
                    $maintainanceTax = $this->input->post("maintainanceTax");
                    $maintainanceAmount = $this->input->post("maintainanceAmount");
                    $maintainanceClaimRemarks = $this->input->post("maintainanceClaimRemarks");
                    $maintainanceSuppliers = $this->input->post("maintainanceSuppliers");
                    $maintainancePurchaser = $this->input->post("maintainancePurchaser");
                    $maintainanceInvoiceNumber = $this->input->post("maintainanceInvoiceNumber");
                    $costType = $this->input->post("costType");
                    $editId = $this->input->post("edit_id");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $maintainanceSuppliers,
                            "purchaser_id" => $maintainancePurchaser,
                            "invoice_number" => $maintainanceInvoiceNumber,
                            "machine_type" => $maintainanceMachineType,
                            "concept" => $maintainanceConcept,
                            "quantity" => "",
                            "sub_total" => $maintainanceSubTotal,
                            "tax_amount" => $maintainanceTax,
                            "amount" => $maintainanceAmount,
                            "expense_date" => $maintainanceDate,
                            "remarks" => $maintainanceClaimRemarks,
                            "expense_type" => 0,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Costing_model->update_farm_costing($dataCosting, $editId);

                        if ($updateCosting > 0) {
                            $Return["result"] = $this->lang->line("data_updated");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {

                        $dataCosting = array(
                            "cost_type" => $costType,
                            "supplier_id" => $maintainanceSuppliers,
                            "purchaser_id" => $maintainancePurchaser,
                            "invoice_number" => $maintainanceInvoiceNumber,
                            "machine_type" => $maintainanceMachineType,
                            "concept" => $maintainanceConcept,
                            "quantity" => "",
                            "sub_total" => $maintainanceSubTotal,
                            "tax_amount" => $maintainanceTax,
                            "amount" => $maintainanceAmount,
                            "expense_date" => $maintainanceDate,
                            "remarks" => $maintainanceClaimRemarks,
                            "expense_type" => 0,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Costing_model->add_farm_costing($dataCosting);

                        if ($insertCosting > 0) {
                            $Return["result"] = $this->lang->line("data_added");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else if ($this->input->post("action_type") == "saveMiscellaneous") {

                    $originId = $this->input->post("originId");
                    $miscellaneousDate = $this->input->post('miscellaneousDate');
                    $miscellaneousConcept = $this->input->post('miscellaneousConcept');
                    $miscellaneousAmount = $this->input->post("miscellaneousAmount");
                    $miscellaneousClaimRemarks = $this->input->post("miscellaneousClaimRemarks");
                    $miscellaneousSuppliers = $this->input->post("miscellaneousSuppliers");
                    $miscellaneousPurchaser = $this->input->post("miscellaneousPurchaser");
                    $miscellaneousInvoiceNumber = $this->input->post("miscellaneousInvoiceNumber");
                    $costType = $this->input->post("costType");
                    $editId = $this->input->post("edit_id");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $miscellaneousSuppliers,
                            "purchaser_id" => $miscellaneousPurchaser,
                            "invoice_number" => $miscellaneousInvoiceNumber,
                            "machine_type" => 0,
                            "concept" => $miscellaneousConcept,
                            "quantity" => "",
                            "amount" => $miscellaneousAmount,
                            "expense_date" => $miscellaneousDate,
                            "remarks" => $miscellaneousClaimRemarks,
                            "expense_type" => 0,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Costing_model->update_farm_costing($dataCosting, $editId);

                        if ($updateCosting > 0) {
                            $Return["result"] = $this->lang->line("data_updated");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {

                        $dataCosting = array(
                            "cost_type" => $costType,
                            "supplier_id" => $miscellaneousSuppliers,
                            "purchaser_id" => $miscellaneousPurchaser,
                            "invoice_number" => $miscellaneousInvoiceNumber,
                            "machine_type" => 0,
                            "concept" => $miscellaneousConcept,
                            "quantity" => "",
                            "amount" => $miscellaneousAmount,
                            "expense_date" => $miscellaneousDate,
                            "remarks" => $miscellaneousClaimRemarks,
                            "expense_type" => 0,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Costing_model->add_farm_costing($dataCosting);

                        if ($insertCosting > 0) {
                            $Return["result"] = $this->lang->line("data_added");
                            $Return["error"] = "";
                            $Return["csrf_hash"] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $Return["result"] = "";
                            $Return["error"] = $this->lang->line("error_adding");
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
            $Return["error"] = $this->lang->line("invalid_request");
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function farmcosting_list()
    {
        $draw = intval($this->input->get("draw"));
        $originId = intval($this->input->get("originId"));
        $costType = intval($this->input->get("costType"));

        $farmCosting = $this->Costing_model->get_farm_costing($originId, $costType);

        $data = array();

        if ($costType == 1) {

            foreach ($farmCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_extraction" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_extraction" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->expense_date,
                    ($r->quantity + 0),
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 4) {

            foreach ($farmCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_acpm" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_acpm" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                if ($r->expense_type == 0) {
                    $expenseType = $this->lang->line('purchase');
                } else {
                    $expenseType = $this->lang->line('spend');
                }

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->purchaser_name,
                    $r->invoice_number,
                    $r->expense_date,
                    ($r->quantity + 0),
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                    $expenseType,
                );
            }
        } else if ($costType == 5) {

            foreach ($farmCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_maintenance" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_maintenance" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->purchaser_name,
                    $r->invoice_number,
                    $r->expense_date,
                    $r->machine_name,
                    $r->concept,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 6) {

            foreach ($farmCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_miscellaneous" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_miscellaneous" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->purchaser_name,
                    $r->invoice_number,
                    $r->expense_date,
                    $r->concept,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        }

        $output = array(
            "draw" => $draw,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function fetch_suppliers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getSuppliers = $this->Costing_model->get_suppliers_by_origin($this->input->get('originid'));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . $supplier->supplier_name . "</option>";
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

    public function fetch_purchasers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getPurchasers = $this->Costing_model->get_costingpurchasers_by_origin($this->input->get('originid'), $this->input->get('costingtype'));
                foreach ($getPurchasers as $purchaser) {
                    $result = $result . "<option value='" . $purchaser->id . "'>" . $purchaser->purchaser_name . "</option>";
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
            $originId = $this->input->post("originId");
            $costingType = $this->input->post("costingType");

            //DELETE EXISTING FILES
            $this->deletefilesfromfolder();

            if ($costingType == 4) {

                if ($_FILES['acpm_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['acpm_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['acpm_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["acpm_xmlupload"]["tmp_name"];
                                $invoiceFolder = "assets/costingdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/costingdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $costingType, $fileurl), true);

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
                } else {
                    $Return['error'] = $this->lang->line('error_invalid_file');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                }
            } else if ($costingType == 5) {

                if ($_FILES['maintenance_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['maintenance_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['maintenance_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["maintenance_xmlupload"]["tmp_name"];
                                $invoiceFolder = "assets/costingdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/costingdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $costingType, $fileurl), true);

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
                } else {
                    $Return['error'] = $this->lang->line('error_invalid_file');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                }
            } else if ($costingType == 6) {

                if ($_FILES['miscellaneous_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['miscellaneous_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['miscellaneous_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["miscellaneous_xmlupload"]["tmp_name"];
                                $invoiceFolder = "assets/costingdocs/xmlupload/";

                                $newfilename = 'XML_' . round(microtime(true)) . '.xml';
                                move_uploaded_file($tmp_name, $invoiceFolder . $newfilename);
                                $fileurl = "assets/costingdocs/xmlupload/" . $newfilename;

                                $docXml = file_get_contents($fileurl);

                                if (empty(trim($docXml))) {
                                    $Return['error'] = $this->lang->line('error_xml');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }

                                $docXml = preg_replace('/[\x00-\x1F\x7F]/', '', $docXml);
                                $docXml = mb_convert_encoding($docXml, 'UTF-8', 'auto');

                                $xmlResponse = json_decode($this->importInvoice($docXml, $ext, $originId, $costingType, $fileurl), true);

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

        if ($exportType == 4) {
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
            $isNewSupplier = false;
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 4);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 4,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 4);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
                $isNewSupplier = false;
            }

            //Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $payableAmount = 0;
            $totalQuantity = 0;

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");
                        $totalQuantityNode = $embeddedXpath->query("//cac:InvoiceLine/cbc:InvoicedQuantity");

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($totalQuantityNode->length > 0) {
                            $totalQuantity = $totalQuantityNode->item(0)->nodeValue + 0;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $formattedDate = "";
            if ($issuedDate != "") {
                $date = new DateTime($issuedDate);
                $formattedDate = $date->format('d/m/Y');
            }

            $dados = [
                'issueDate' => $formattedDate,
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'payableAmount' => $payableAmount,
                'invoicedQuantity' => $totalQuantity,
                'supplierId' => $supplierId,
                'isNewSupplier' => $isNewSupplier
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
            $isNewSupplier = false;
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 5);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 5,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 5);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
                $isNewSupplier = false;
            }

            //Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $payableAmount = 0;
            $totalQuantity = 0;
            $description = "";

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        //$payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");
                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount");
                        $totalQuantityNode = $embeddedXpath->query("//cac:InvoiceLine/cbc:InvoicedQuantity");
                        $descriptionNode = $embeddedXpath->query("//cac:InvoiceLine/cac:Item/cbc:Description");

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($totalQuantityNode->length > 0) {
                            $totalQuantity = $totalQuantityNode->item(0)->nodeValue + 0;
                        }

                        if ($descriptionNode->length > 0) {
                            $description = $descriptionNode->item(0)->nodeValue;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $formattedDate = "";
            if ($issuedDate != "") {
                $date = new DateTime($issuedDate);
                $formattedDate = $date->format('d/m/Y');
            }

            $dados = [
                'issueDate' => $formattedDate,
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'payableAmount' => $payableAmount,
                'invoicedQuantity' => $totalQuantity,
                'supplierId' => $supplierId,
                'isNewSupplier' => $isNewSupplier,
                'description' => $description,
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
            $isNewSupplier = false;
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 6);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 6,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 6);
                $supplierId = $checkCompanyIdExists[0]->id + 0;
                $isNewSupplier = false;
            }

            //Extract Embedded XML from `cbc:Description`
            $embeddedXmlNode = $xpath->query("//cac:Attachment/cac:ExternalReference/cbc:Description");
            $payableAmount = 0;
            $totalQuantity = 0;
            $description = "";

            if ($embeddedXmlNode->length > 0) {
                $embeddedXml = trim($embeddedXmlNode->item(0)->nodeValue);

                if (!empty($embeddedXml)) {
                    $embeddedDoc = new DOMDocument();
                    if ($embeddedDoc->loadXML($embeddedXml)) {
                        $embeddedXpath = new DOMXPath($embeddedDoc);
                        $embeddedXpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
                        $embeddedXpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

                        $payableAmountNode = $embeddedXpath->query("//cac:LegalMonetaryTotal/cbc:PayableAmount");
                        $totalQuantityNode = $embeddedXpath->query("//cac:InvoiceLine/cbc:InvoicedQuantity");
                        $descriptionNode = $embeddedXpath->query("//cac:InvoiceLine/cac:Item/cbc:Description");

                        if ($payableAmountNode->length > 0) {
                            $payableAmount = $payableAmountNode->item(0)->nodeValue + 0;
                        }

                        if ($totalQuantityNode->length > 0) {
                            $totalQuantity = $totalQuantityNode->item(0)->nodeValue + 0;
                        }

                        if ($descriptionNode->length > 0) {
                            $description = $descriptionNode->item(0)->nodeValue;
                        }
                    }
                }
            }

            $issuedDate = ($issueDateNode->length > 0) ? $issueDateNode->item(0)->nodeValue : "";
            $formattedDate = "";
            if ($issuedDate != "") {
                $date = new DateTime($issuedDate);
                $formattedDate = $date->format('d/m/Y');
            }

            $dados = [
                'issueDate' => $formattedDate,
                'registrationName' => ($registrationNameNode->length > 0) ? $registrationNameNode->item(0)->nodeValue : "NA",
                'companyId' => ($companyIdNode->length > 0) ? $companyIdNode->item(0)->nodeValue : "NA",
                'documentId' => ($documentIdNode->length > 0) ? $documentIdNode->item(0)->nodeValue : "NA",
                'payableAmount' => $payableAmount,
                'invoicedQuantity' => $totalQuantity,
                'supplierId' => $supplierId,
                'isNewSupplier' => $isNewSupplier,
                'description' => $description
            ];

            return json_encode($dados, JSON_PRETTY_PRINT);
        }
    }

    public function dialog_costing_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {

            if ($this->input->get('type') == "editcosting") {

                $costingId = $this->input->get('cid');
                $typeid = $this->input->get('typeid');
                $originId = $this->input->get('originid');

                if ($typeid == 1) {
                    $getCostingDetails = $this->Costing_model->get_costing_detail_byid($costingId, $typeid, $originId);

                    if (count($getCostingDetails) > 0) {

                        $data = array(
                            'supplierid' => $getCostingDetails[0]->supplier_id + 0,
                            'expensedate' => $getCostingDetails[0]->expense_date,
                            'treecount' => $getCostingDetails[0]->quantity + 0,
                            'amount' => $getCostingDetails[0]->amount + 0,
                            'remarks' => $getCostingDetails[0]->remarks,
                            'costingid' => $getCostingDetails[0]->id,
                            "updatetext" => $this->lang->line("update")
                        );

                        $Return["result"] = $data;
                        $Return["error"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    } else {
                        $Return["result"] = "";
                        $Return["error"] = $this->lang->line("error_fetch_details");
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($typeid == 4) {
                    $getCostingDetails = $this->Costing_model->get_costing_detail_byid($costingId, $typeid, $originId);

                    if (count($getCostingDetails) > 0) {

                        $data = array(
                            'supplierid' => $getCostingDetails[0]->supplier_id + 0,
                            'purchaserid' => $getCostingDetails[0]->purchaser_id + 0,
                            'invoiceno' => $getCostingDetails[0]->invoice_number,
                            'expensedate' => $getCostingDetails[0]->expense_date,
                            'quantity' => $getCostingDetails[0]->quantity + 0,
                            'amount' => $getCostingDetails[0]->amount + 0,
                            'remarks' => $getCostingDetails[0]->remarks,
                            'expensetype' => $getCostingDetails[0]->expense_type + 0,
                            'costingid' => $getCostingDetails[0]->id,
                            "updatetext" => $this->lang->line("update")
                        );

                        $Return["result"] = $data;
                        $Return["error"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    } else {
                        $Return["result"] = "";
                        $Return["error"] = $this->lang->line("error_fetch_details");
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($typeid == 5) {
                    $getCostingDetails = $this->Costing_model->get_costing_detail_byid($costingId, $typeid, $originId);

                    if (count($getCostingDetails) > 0) {

                        $data = array(
                            'supplierid' => $getCostingDetails[0]->supplier_id + 0,
                            'purchaserid' => $getCostingDetails[0]->purchaser_id + 0,
                            'invoiceno' => $getCostingDetails[0]->invoice_number,
                            'expensedate' => $getCostingDetails[0]->expense_date,
                            'machinetype' => $getCostingDetails[0]->machine_type + 0,
                            'concept' => $getCostingDetails[0]->concept,
                            'quantity' => $getCostingDetails[0]->quantity + 0,
                            'subtotal' => $getCostingDetails[0]->sub_total + 0,
                            'taxamount' => $getCostingDetails[0]->tax_amount + 0,
                            'amount' => $getCostingDetails[0]->amount + 0,
                            'remarks' => $getCostingDetails[0]->remarks,
                            'expensetype' => $getCostingDetails[0]->expense_type + 0,
                            'costingid' => $getCostingDetails[0]->id,
                            "updatetext" => $this->lang->line("update")
                        );

                        $Return["result"] = $data;
                        $Return["error"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    } else {
                        $Return["result"] = "";
                        $Return["error"] = $this->lang->line("error_fetch_details");
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($typeid == 6) {
                    $getCostingDetails = $this->Costing_model->get_costing_detail_byid($costingId, $typeid, $originId);

                    if (count($getCostingDetails) > 0) {

                        $data = array(
                            'supplierid' => $getCostingDetails[0]->supplier_id + 0,
                            'purchaserid' => $getCostingDetails[0]->purchaser_id + 0,
                            'invoiceno' => $getCostingDetails[0]->invoice_number,
                            'expensedate' => $getCostingDetails[0]->expense_date,
                            'concept' => $getCostingDetails[0]->concept,
                            'quantity' => $getCostingDetails[0]->quantity + 0,
                            'amount' => $getCostingDetails[0]->amount + 0,
                            'remarks' => $getCostingDetails[0]->remarks,
                            'expensetype' => $getCostingDetails[0]->expense_type + 0,
                            'costingid' => $getCostingDetails[0]->id,
                            "updatetext" => $this->lang->line("update")
                        );

                        $Return["result"] = $data;
                        $Return["error"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    } else {
                        $Return["result"] = "";
                        $Return["error"] = $this->lang->line("error_fetch_details");
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                }
            } else if ($this->input->get('type') == "deletecostingconfirmation") {

                if ($this->input->get('costingtype') == 1) {

                    $data = array(
                        'pageheading' => $this->lang->line('confirmation'),
                        'pagemessage' => $this->lang->line('delete_message'),
                        'inputid' => $this->input->get('cid'),
                        'actionurl' => "costings/dialog_costing_action",
                        'actiontype' => "deletecosting",
                        'xin_table' => "#xin_table_extraction",
                    );
                } else if ($this->input->get('costingtype') == 4) {

                    $data = array(
                        'pageheading' => $this->lang->line('confirmation'),
                        'pagemessage' => $this->lang->line('delete_message'),
                        'inputid' => $this->input->get('cid'),
                        'actionurl' => "costings/dialog_costing_action",
                        'actiontype' => "deletecosting",
                        'xin_table' => "#xin_table_acpm",
                    );
                } else if ($this->input->get('costingtype') == 5) {

                    $data = array(
                        'pageheading' => $this->lang->line('confirmation'),
                        'pagemessage' => $this->lang->line('delete_message'),
                        'inputid' => $this->input->get('cid'),
                        'actionurl' => "costings/dialog_costing_action",
                        'actiontype' => "deletecosting",
                        'xin_table' => "#xin_table_maintenance",
                    );
                } else if ($this->input->get('costingtype') == 6) {

                    $data = array(
                        'pageheading' => $this->lang->line('confirmation'),
                        'pagemessage' => $this->lang->line('delete_message'),
                        'inputid' => $this->input->get('cid'),
                        'actionurl' => "costings/dialog_costing_action",
                        'actiontype' => "deletecosting",
                        'xin_table' => "#xin_table_miscellaneous",
                    );
                }

                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletecosting") {

                $costingId = $this->input->get('inputid');

                $data = array(
                    'is_active' => 0,
                );

                $costingDelete = $this->Costing_model->update_farm_costing($data, $costingId);

                if ($costingDelete) {
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
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function dialog_generate_summary_report()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->post("type") == "generate_report") {
                $data = array(
                    "pageheading" => $this->lang->line("generate_report"),
                    "pagetype" => "generate_report",
                    "originId" => $this->input->post("originId"),
                    "csrf_hash" => $this->security->get_csrf_hash(),
                    "suppliers" => $this->Costing_model->get_suppliers_by_origin($this->input->post("originId")),
                );
            }
            $this->load->view('costings/dialog_select_date', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    // public function generate_summary_report()
    // {
    //     try {

    //         $session = $this->session->userdata('fullname');

    //         $Return = array(
    //             'result' => '',
    //             'error' => '',
    //             'redirect' => false,
    //             'csrf_hash' => '',
    //             'successmessage' => ''
    //         );

    //         if (!empty($session)) {

    //             $Return['csrf_hash'] = $this->security->get_csrf_hash();

    //             if ($this->input->post("reportType") == 1) {

    //                 $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data($this->input->post("originId"), $this->input->post("year"));
    //                 $getTotalICAs = $this->Costing_model->get_total_volume($this->input->post("originId"), $this->input->post("year"));

    //                 if (count($getExpenseSummaryReport) > 0) {

    //                     $this->excel->setActiveSheetIndex(0);
    //                     $objSheet = $this->excel->getActiveSheet();
    //                     $objSheet->setTitle($this->lang->line('report_summary'));
    //                     $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

    //                     $styleArray = array(
    //                         'borders' => array(
    //                             'allborders' => array(
    //                                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //                             )
    //                         )
    //                     );

    //                     $styleThickArray = array(
    //                         'borders' => array(
    //                             'allborders' => array(
    //                                 'style' => PHPExcel_Style_Border::BORDER_THICK
    //                             )
    //                         )
    //                     );

    //                     $objSheet->SetCellValue('B1', $this->lang->line("expense_summary"));
    //                     $objSheet->mergeCells('B1:C2');
    //                     $objSheet->getStyle("B1")->getFont()->setSize(13)->setBold(true);
    //                     $objSheet->getStyle("B1:C2")->applyFromArray($styleThickArray);
    //                     $objSheet->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //                     $objSheet->getStyle("B1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    //                     $objSheet->SetCellValue("A4", $this->lang->line('total_volume'));
    //                     $objSheet->getStyle("A4")->getFont()->setBold(true);

    //                     $objSheet->SetCellValue("B4", $getTotalICAs[0]->total_volume + 0);
    //                     $objSheet->getStyle("B4")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

    //                     $objSheet->getStyle("A4:B4")->applyFromArray($styleArray);

    //                     // Get column headers dynamically (exclude cost_type_id)
    //                     $headers = array_keys($getExpenseSummaryReport[0]);
    //                     $headers = array_filter($headers, function ($h) {
    //                         return $h !== 'cost_type_id';
    //                     });

    //                     // Reset array indexes (important after filter)
    //                     $headers = array_values($headers);

    //                     // Add YTD column
    //                     $headers[] = "YTD";

    //                     // Add Cost / ICA column
    //                     $headers[] = "cost_ica";

    //                     // Write headers starting from A4
    //                     $col = 0;
    //                     foreach ($headers as $header) {
    //                         $objSheet->setCellValueByColumnAndRow($col, 5, $this->lang->line(strtolower($header)));
    //                         $col++;
    //                     }

    //                     $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1); // get last column letter
    //                     $headerRange   = "A5:" . $lastColLetter . "5";

    //                     $objSheet->getStyle($headerRange)->getFont()->setBold(true);
    //                     $objSheet->getStyle($headerRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //                     $objSheet->getStyle($headerRange)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //                     $objSheet->getStyle($headerRange)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
    //                     $objSheet->getStyle($headerRange)->applyFromArray($styleArray);

    //                     for ($c = 0; $c < count($headers); $c++) {
    //                         $colLetter = PHPExcel_Cell::stringFromColumnIndex($c); // 0=A, 1=B, etc.
    //                         $objSheet->getColumnDimension($colLetter)->setAutoSize(false)->setWidth(20);
    //                     }

    //                     $row = 6;
    //                     foreach ($getExpenseSummaryReport as $dataRow) {

    //                         if (isset($dataRow['costing_type']) && strtolower($dataRow['costing_type']) === 'total_volume') {
    //                             $totalVolumeRow = $dataRow; // store for later
    //                             continue;
    //                         }

    //                         $col = 0;
    //                         foreach ($headers as $header) {
    //                             if ($header === "YTD") {
    //                                 // YTD = SUM of all month columns
    //                                 $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1); // B
    //                                 $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3); // last month column (before YTD)
    //                                 $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
    //                                 $objSheet->setCellValueByColumnAndRow($col, $row, $formula);

    //                                 $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    //                             } elseif ($header === "cost_ica") {
    //                                 // Use YTD column (previous col index) divided by B4
    //                                 $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
    //                                 $formula = "=" . $ytdColLetter . $row . "/B4";
    //                                 $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
    //                                 $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    //                             } else {
    //                                 if ($header !== 'cost_type_id') {
    //                                     $value = isset($dataRow[$header]) ? $dataRow[$header] : '';

    //                                     // ✅ Translate special row values
    //                                     if (in_array(strtolower($value), ['zona', 'loading'])) {
    //                                         $value = $this->lang->line(strtolower($value));
    //                                     }

    //                                     $objSheet->setCellValueByColumnAndRow($col, $row, $value);
    //                                 }
    //                             }

    //                             $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
    //                             $objSheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
    //                             $col++;
    //                         }
    //                         $row++;
    //                     }

    //                     // After data rows
    //                     $lastDataRow = $row - 1; // last row with actual data
    //                     $totalRow    = $row;     // next row will be totals

    //                     $col = 0;
    //                     foreach ($headers as $header) {
    //                         if ($header === "costing_type" || $header === "costing_name") {
    //                             // Put label in first column (example: "Total")
    //                             if ($col == 0) {
    //                                 $objSheet->setCellValueByColumnAndRow($col, $totalRow, strtoupper($this->lang->line("total")));
    //                                 $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
    //                                 $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                                 $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //                             }
    //                         } elseif ($header === "cost_ica") {
    //                             // Cost / ICA total = YTD total / B3
    //                             $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
    //                             $formula = "=" . $ytdColLetter . $totalRow . "/B4";
    //                             $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
    //                             $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
    //                         } elseif ($header === "YTD") {
    //                             // YTD total = sum of all YTD column cells
    //                             $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
    //                             $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
    //                             $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
    //                             $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
    //                         } else {
    //                             // Each month column total
    //                             $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
    //                             $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
    //                             $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
    //                             $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
    //                         }
    //                         $col++;
    //                     }

    //                     // (optional) style entire total row bold
    //                     $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
    //                     $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getFont()->setBold(true);
    //                     $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
    //                     $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray($styleArray);

    //                     // // === Add total_volume row ===
    //                     // if ($totalVolumeRow) {
    //                     //     $row = $totalRow + 1;
    //                     //     $col = 0;
    //                     //     foreach ($headers as $header) {
    //                     //         if ($header === "cost_type_id") continue;

    //                     //         $value = isset($totalVolumeRow[$header]) ? $totalVolumeRow[$header] : '';
    //                     //         if (strtolower($header) === "costing_type") {
    //                     //             $value = strtoupper($this->lang->line("volume"));
    //                     //             $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    //                     //             $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                     //             $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //                     //         }

    //                     //         $objSheet->setCellValueByColumnAndRow($col, $row, $value);


    //                     //         $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
    //                     //         $objSheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
    //                     //         $col++;
    //                     //     }
    //                     // }

    //                     // === Add total_volume row ===
    //                     if ($totalVolumeRow) {
    //                         $row = $totalRow + 1;
    //                         $col = 0;
    //                         foreach ($headers as $header) {
    //                             if ($header === "cost_type_id") continue;

    //                             if ($header === "YTD") {
    //                                 // YTD = SUM of all month columns
    //                                 $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1); // B
    //                                 $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3); // last month column
    //                                 $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
    //                                 $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
    //                             }
    //                             // elseif ($header === "cost_ica") {
    //                             //     // Optional: Cost/ICA for total_volume (same as others)
    //                             //     $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
    //                             //     $formula = "=" . $ytdColLetter . $row . "/B4";
    //                             //     $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
    //                             // } 
    //                             else {
    //                                 $value = isset($totalVolumeRow[$header]) ? $totalVolumeRow[$header] : '';
    //                                 if (strtolower($header) === "costing_type") {
    //                                     $value = strtoupper($this->lang->line("volume"));
    //                                     $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    //                                     $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                                     $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //                                 }
    //                                 $objSheet->setCellValueByColumnAndRow($col, $row, $value);
    //                             }

    //                             $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
    //                             $objSheet->getStyleByColumnAndRow($col, $row)
    //                                 ->getNumberFormat()
    //                                 ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

    //                             $col++;
    //                         }
    //                     }

    //                     // === Add cost/volume row ===
    //                     if ($totalVolumeRow) {
    //                         $costPerVolumeRow = $row + 1; // row after total_volume
    //                         $col = 0;

    //                         foreach ($headers as $header) {
    //                             if ($header === "cost_type_id") continue;

    //                             if ($header === "costing_type" || $header === "costing_name") {
    //                                 if ($col == 0) {
    //                                     $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, strtoupper($this->lang->line("cost_per_cbm")));
    //                                     $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                                     $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //                                 }
    //                             } else {
    //                                 // Divide "Total row" by "Total Volume row"
    //                                 $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
    //                                 $formula   = "=" . $colLetter . $totalRow . "/" . $colLetter . $row;
    //                                 $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, $formula);
    //                             }

    //                             $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->applyFromArray($styleArray);
    //                             $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)
    //                                 ->getNumberFormat()
    //                                 ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
    //                             $col++;
    //                         }

    //                         // Style it bold
    //                         $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
    //                         $objSheet->getStyle("A{$costPerVolumeRow}:{$lastColLetter}{$costPerVolumeRow}")->getFont()->setBold(true);
    //                     }

    //                     $objSheet->getSheetView()->setZoomScale(95);
    //                     $this->excel->setActiveSheetIndex(0);

    //                     unset($styleArray);
    //                     unset($styleThickArray);
    //                     $six_digit_random_number = mt_rand(100000, 999999);
    //                     $month_name = ucfirst(date("dmY"));

    //                     $filename =  'ExpenseReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

    //                     header('Content-Type: application/vnd.ms-excel');
    //                     header('Content-Disposition: attachment;filename="' . $filename . '"');
    //                     header('Cache-Control: max-age=0');

    //                     $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
    //                     $objWriter->save('./reports/ContractReports/' . $filename);
    //                     $Return['error'] = '';
    //                     $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
    //                     $Return['successmessage'] = $this->lang->line('report_downloaded');
    //                     if ($Return['result'] != '') {
    //                         $this->output($Return);
    //                     }
    //                 } else {
    //                     $Return["error"] = $this->lang->line("no_data_reports");
    //                     $Return["pages"] = "";
    //                     $Return["redirect"] = false;
    //                     $this->output($Return);
    //                 }
    //             }
    //         } else {
    //             $Return['error'] = "";
    //             $Return['result'] = "";
    //             $Return['redirect'] = true;
    //             $Return['csrf_hash'] = $this->security->get_csrf_hash();
    //             $this->output($Return);
    //             exit;
    //         }
    //     } catch (Exception $e) {
    //         $Return['error'] = $this->lang->line('error_reports');
    //         $Return['result'] = "";
    //         $Return['redirect'] = false;
    //         $Return['csrf_hash'] = $this->security->get_csrf_hash();
    //         $this->output($Return);
    //         exit;
    //     }
    // }

    public function generate_summary_report()
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

                if ($this->input->post("reportType") == 1) {

                    if ($this->input->post("dateType") == 1) {
                        $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data(
                            $this->input->post("originId"),
                            $this->input->post("year"),
                            $this->input->post("supplierId")
                        );
                        $getTotalICAs = $this->Costing_model->get_total_volume(
                            $this->input->post("originId"),
                            $this->input->post("year"),
                            $this->input->post("supplierId")
                        );
                    } else if ($this->input->post("dateType") == 2) {
                        $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data_by_date(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate"),
                            $this->input->post("supplierId")
                        );
                        $getTotalICAs = $this->Costing_model->get_total_volume_by_date(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate"),
                            $this->input->post("supplierId")
                        );
                    }

                    if (count($getExpenseSummaryReport) > 0) {
                        $this->excel->setActiveSheetIndex(0);
                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle($this->lang->line('report_summary'));
                        $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $styleThickArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THICK
                                )
                            )
                        );

                        $objSheet->SetCellValue('B1', $this->lang->line("expense_summary"));
                        $objSheet->mergeCells('B1:C2');
                        $objSheet->getStyle("B1")->getFont()->setSize(13)->setBold(true);
                        $objSheet->getStyle("B1:C2")->applyFromArray($styleThickArray);
                        $objSheet->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle("B1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $objSheet->SetCellValue("A4", $this->lang->line('total_volume'));
                        $objSheet->getStyle("A4")->getFont()->setBold(true);

                        $objSheet->SetCellValue("B4", $getTotalICAs[0]->total_volume + 0);
                        $objSheet->getStyle("B4")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSheet->getStyle("A4:B4")->applyFromArray($styleArray);

                        // Get column headers dynamically (exclude cost_type_id)
                        $headers = array_keys($getExpenseSummaryReport[0]);
                        $headers = array_filter($headers, function ($h) {
                            return $h !== 'cost_type_id';
                        });

                        // Reset array indexes (important after filter)
                        $headers = array_values($headers);

                        // Add YTD column
                        $headers[] = "YTD";

                        // Add Cost / ICA column
                        $headers[] = "cost_ica";

                        // Write headers starting from A5
                        $col = 0;
                        foreach ($headers as $header) {
                            $headerText = explode("-", $header);
                            $objSheet->setCellValueByColumnAndRow($col, 5, $this->lang->line(strtolower($headerText[0])) . (isset($headerText[1]) ? " - " . $headerText[1] : ''));
                            $col++;
                        }

                        $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1); // get last column letter
                        $headerRange   = "A5:" . $lastColLetter . "5";

                        $objSheet->getStyle($headerRange)->getFont()->setBold(true);
                        $objSheet->getStyle($headerRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle($headerRange)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objSheet->getStyle($headerRange)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objSheet->getStyle($headerRange)->applyFromArray($styleArray);

                        for ($c = 0; $c < count($headers); $c++) {
                            $colLetter = PHPExcel_Cell::stringFromColumnIndex($c);
                            $objSheet->getColumnDimension($colLetter)->setAutoSize(false)->setWidth(20);
                        }

                        $row = 6;
                        foreach ($getExpenseSummaryReport as $dataRow) {
                            if (isset($dataRow['costing_type']) && strtolower($dataRow['costing_type']) === 'total_volume') {
                                $totalVolumeRow = $dataRow;
                                continue;
                            }

                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "YTD") {
                                    $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1); // B
                                    $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3); // last month column (before YTD)
                                    $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                    $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                } elseif ($header === "cost_ica") {
                                    $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
                                    $formula = "=" . $ytdColLetter . $row . "/B4";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                    $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                } else {
                                    if ($header !== 'cost_type_id') {
                                        $value = isset($dataRow[$header]) ? $dataRow[$header] : '';
                                        if (in_array(strtolower($value), ['zona', 'loading'])) {
                                            $value = $this->lang->line(strtolower($value));
                                        }
                                        $objSheet->setCellValueByColumnAndRow($col, $row, $value);
                                    }
                                }

                                $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $col++;
                            }
                            $row++;
                        }

                        $lastDataRow = $row - 1;
                        $totalRow    = $row;

                        $col = 0;
                        foreach ($headers as $header) {
                            if ($header === "costing_type" || $header === "costing_name") {
                                if ($col == 0) {
                                    $objSheet->setCellValueByColumnAndRow($col, $totalRow, strtoupper($this->lang->line("total")));
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                }
                            } elseif ($header === "cost_ica") {
                                $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
                                $formula = "=" . $ytdColLetter . $totalRow . "/B4";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            } elseif ($header === "YTD") {
                                $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            } else {
                                $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            }
                            $col++;
                        }

                        $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getFont()->setBold(true);
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray($styleArray);

                        // === Add total_volume row ===
                        if ($totalVolumeRow) {
                            $row = $totalRow + 1;
                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "cost_type_id" || $header === "cost_ica") continue;

                                if ($header === "YTD") {
                                    $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1);
                                    $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3);
                                    $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                } else {
                                    $value = isset($totalVolumeRow[$header]) ? $totalVolumeRow[$header] : '';
                                    if (strtolower($header) === "costing_type") {
                                        $value = strtoupper($this->lang->line("volume"));
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    }
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $value);
                                }

                                $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $row)
                                    ->getNumberFormat()
                                    ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                $col++;
                            }
                        }

                        // === Add cost/volume row ===
                        if ($totalVolumeRow) {
                            $costPerVolumeRow = $row + 1;
                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "cost_type_id" || $header === "cost_ica") continue;

                                if ($header === "costing_type" || $header === "costing_name") {
                                    if ($col == 0) {
                                        $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, strtoupper($this->lang->line("cost_per_cbm")));
                                        $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                        $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    }
                                } else {
                                    $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                    $formula   = "=IFERROR(" . $colLetter . $totalRow . "/" . $colLetter . $row . ", 0)";
                                    $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, $formula);
                                }

                                $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $col++;
                            }

                            $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
                            $objSheet->getStyle("A{$costPerVolumeRow}:{$lastColLetter}{$costPerVolumeRow}")->getFont()->setBold(true);
                        }

                        $objSheet->freezePane("B6");
                        $objSheet->getSheetView()->setZoomScale(95);
                        $this->excel->setActiveSheetIndex(0);

                        unset($styleArray);
                        unset($styleThickArray);
                        $six_digit_random_number = mt_rand(100000, 999999);
                        $month_name = ucfirst(date("dmY"));

                        $filename = 'ExpenseReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '"');
                        header('Cache-Control: max-age=0');

                        $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                        $objWriter->save('./reports/ContractReports/' . $filename);
                        $Return['error'] = '';
                        $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
                        $Return['successmessage'] = $this->lang->line('report_downloaded');
                        if ($Return['result'] != '') {
                            $this->output($Return);
                        }
                    } else {
                        $Return["error"] = $this->lang->line("no_data_reports");
                        $Return["pages"] = "";
                        $Return["redirect"] = false;
                        $this->output($Return);
                    }
                } else if ($this->input->post("reportType") == 2) {

                    if ($this->input->post("dateType") == 1) {
                        $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data_nomina(
                            $this->input->post("originId"),
                            $this->input->post("year")
                        );
                        $getTotalICAs = $this->Costing_model->get_total_volume_nomina(
                            $this->input->post("originId"),
                            $this->input->post("year")
                        );
                    } else if ($this->input->post("dateType") == 2) {
                        $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data_by_date_nomina(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate")
                        );
                        $getTotalICAs = $this->Costing_model->get_total_volume_by_date_nomina(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate")
                        );
                    }

                    if (count($getExpenseSummaryReport) > 0) {
                        $this->excel->setActiveSheetIndex(0);
                        $objSheet = $this->excel->getActiveSheet();
                        $objSheet->setTitle($this->lang->line('report_summary'));
                        $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );

                        $styleThickArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THICK
                                )
                            )
                        );

                        $objSheet->SetCellValue('B1', $this->lang->line("nominal_report_type"));
                        $objSheet->mergeCells('B1:C2');
                        $objSheet->getStyle("B1")->getFont()->setSize(13)->setBold(true);
                        $objSheet->getStyle("B1:C2")->applyFromArray($styleThickArray);
                        $objSheet->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle("B1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $objSheet->SetCellValue("A4", $this->lang->line('total_volume'));
                        $objSheet->getStyle("A4")->getFont()->setBold(true);

                        $objSheet->SetCellValue("B4", $getTotalICAs[0]->total_volume + 0);
                        $objSheet->getStyle("B4")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSheet->getStyle("A4:B4")->applyFromArray($styleArray);

                        // Get column headers dynamically (exclude cost_type_id)
                        $headers = array_keys($getExpenseSummaryReport[0]);
                        $headers = array_filter($headers, function ($h) {
                            return $h !== 'cost_type_id';
                        });

                        // Reset array indexes (important after filter)
                        $headers = array_values($headers);

                        // Add YTD column
                        $headers[] = "YTD";

                        // Add Cost / ICA column
                        $headers[] = "cost_ica";

                        // Write headers starting from A5
                        $col = 0;
                        foreach ($headers as $header) {
                            $headerText = explode("-", $header);
                            $objSheet->setCellValueByColumnAndRow($col, 5, $this->lang->line(strtolower($headerText[0])) . (isset($headerText[1]) ? " - " . $headerText[1] : ''));
                            $col++;
                        }

                        $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1); // get last column letter
                        $headerRange   = "A5:" . $lastColLetter . "5";

                        $objSheet->getStyle($headerRange)->getFont()->setBold(true);
                        $objSheet->getStyle($headerRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle($headerRange)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objSheet->getStyle($headerRange)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objSheet->getStyle($headerRange)->applyFromArray($styleArray);

                        for ($c = 0; $c < count($headers); $c++) {
                            $colLetter = PHPExcel_Cell::stringFromColumnIndex($c);
                            $objSheet->getColumnDimension($colLetter)->setAutoSize(false)->setWidth(20);
                        }

                        $row = 6;
                        foreach ($getExpenseSummaryReport as $dataRow) {
                            if (isset($dataRow['costing_type']) && strtolower($dataRow['costing_type']) === 'total_volume') {
                                $totalVolumeRow = $dataRow;
                                continue;
                            }

                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "YTD") {
                                    $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1); // B
                                    $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3); // last month column (before YTD)
                                    $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                    $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                } elseif ($header === "cost_ica") {
                                    $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
                                    $formula = "=" . $ytdColLetter . $row . "/B4";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                    $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                } else {
                                    if ($header !== 'cost_type_id') {
                                        $value = isset($dataRow[$header]) ? $dataRow[$header] : '';
                                        if (in_array(strtolower($value), ['zona', 'loading'])) {
                                            $value = $this->lang->line(strtolower($value));
                                        }
                                        $objSheet->setCellValueByColumnAndRow($col, $row, $value);
                                    }
                                }

                                $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $col++;
                            }
                            $row++;
                        }

                        $lastDataRow = $row - 1;
                        $totalRow    = $row;

                        $col = 0;
                        foreach ($headers as $header) {
                            if ($header === "costing_type" || $header === "costing_name") {
                                if ($col == 0) {
                                    $objSheet->setCellValueByColumnAndRow($col, $totalRow, strtoupper($this->lang->line("total")));
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objSheet->getStyleByColumnAndRow($col, $totalRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                }
                            } elseif ($header === "cost_ica") {
                                $ytdColLetter = PHPExcel_Cell::stringFromColumnIndex($col - 1);
                                $formula = "=" . $ytdColLetter . $totalRow . "/B4";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            } elseif ($header === "YTD") {
                                $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            } else {
                                $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                $formula   = "=SUM(" . $colLetter . "6:" . $colLetter . $lastDataRow . ")";
                                $objSheet->setCellValueByColumnAndRow($col, $totalRow, $formula);
                                $objSheet->getStyleByColumnAndRow($col, $totalRow)->getFont()->setBold(true);
                            }
                            $col++;
                        }

                        $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getFont()->setBold(true);
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objSheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray($styleArray);

                        // === Add total_volume row ===
                        if ($totalVolumeRow) {
                            $row = $totalRow + 1;
                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "cost_type_id" || $header === "cost_ica") continue;

                                if ($header === "YTD") {
                                    $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1);
                                    $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 3);
                                    $formula = "=SUM(" . $firstMonthCol . $row . ":" . $lastMonthCol . $row . ")";
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $formula);
                                } else {
                                    $value = isset($totalVolumeRow[$header]) ? $totalVolumeRow[$header] : '';
                                    if (strtolower($header) === "costing_type") {
                                        $value = strtoupper($this->lang->line("volume"));
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                        $objSheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    }
                                    $objSheet->setCellValueByColumnAndRow($col, $row, $value);
                                }

                                $objSheet->getStyleByColumnAndRow($col, $row)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $row)
                                    ->getNumberFormat()
                                    ->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                                $col++;
                            }
                        }

                        // === Add cost/volume row ===
                        if ($totalVolumeRow) {
                            $costPerVolumeRow = $row + 1;
                            $col = 0;
                            foreach ($headers as $header) {
                                if ($header === "cost_type_id" || $header === "cost_ica") continue;

                                if ($header === "costing_type" || $header === "costing_name") {
                                    if ($col == 0) {
                                        $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, strtoupper($this->lang->line("cost_per_cbm")));
                                        $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                        $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                    }
                                } else {
                                    $colLetter = PHPExcel_Cell::stringFromColumnIndex($col);
                                    $formula   = "=IFERROR(" . $colLetter . $totalRow . "/" . $colLetter . $row . ", 0)";
                                    $objSheet->setCellValueByColumnAndRow($col, $costPerVolumeRow, $formula);
                                }

                                $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)->applyFromArray($styleArray);
                                $objSheet->getStyleByColumnAndRow($col, $costPerVolumeRow)
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                                $col++;
                            }

                            $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
                            $objSheet->getStyle("A{$costPerVolumeRow}:{$lastColLetter}{$costPerVolumeRow}")->getFont()->setBold(true);
                        }

                        $objSheet->freezePane("B6");
                        $objSheet->getSheetView()->setZoomScale(95);
                        $this->excel->setActiveSheetIndex(0);

                        unset($styleArray);
                        unset($styleThickArray);
                        $six_digit_random_number = mt_rand(100000, 999999);
                        $month_name = ucfirst(date("dmY"));

                        $filename = 'NominaReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '"');
                        header('Cache-Control: max-age=0');

                        $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                        $objWriter->save('./reports/ContractReports/' . $filename);
                        $Return['error'] = '';
                        $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
                        $Return['successmessage'] = $this->lang->line('report_downloaded');
                        if ($Return['result'] != '') {
                            $this->output($Return);
                        }
                    } else {
                        $Return["error"] = $this->lang->line("no_data_reports");
                        $Return["pages"] = "";
                        $Return["redirect"] = false;
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
            $Return['error'] = $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_generate_acpm_report()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->post("type") == "generate_acpm") {
                $data = array(
                    "pageheading" => $this->lang->line("generate_acpm_report"),
                    "pagetype" => "generate_acpm",
                    "originId" => $this->input->post("originId"),
                    "csrf_hash" => $this->security->get_csrf_hash(),
                    "suppliers" => $this->Costing_model->get_suppliers_by_origin($this->input->post("originId")),
                );
            }
            $this->load->view('costings/dialog_select_supplier', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_acpm_report()
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

                $getACPMSummaryDataPurchase = $this->Costing_model->get_acpm_summary_data($this->input->post("originId"), 0, $this->input->post("supplierId"));
                $getACPMSummaryDataSpend = $this->Costing_model->get_acpm_summary_data($this->input->post("originId"), 1, $this->input->post("supplierId"));

                if (count($getACPMSummaryDataPurchase) > 0 || count($getACPMSummaryDataSpend) > 0) {

                    $this->deletefilesfromfolder();

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('report_summary'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $styleThickArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK
                            )
                        )
                    );

                    $getSupplierDetails = $this->Master_model->get_supplier_detail_by_id($this->input->post("supplierId"));

                    $objSheet->SetCellValue('A2', strtoupper($this->lang->line("supplier_name")));
                    $objSheet->SetCellValue('A3', strtoupper($this->lang->line("total_purchase")));
                    $objSheet->SetCellValue('A4', strtoupper($this->lang->line("total_spend")));
                    $objSheet->SetCellValue('A5', strtoupper($this->lang->line("total_inventory")));
                    $objSheet->getStyle("A2:A5")->getFont()->setBold(true);
                    $objSheet->getStyle("A2:B5")->applyFromArray($styleArray);

                    //Purchase
                    $objSheet->SetCellValue('A8', $this->lang->line("purchase"));
                    $objSheet->mergeCells('A8:F8');
                    $objSheet->getStyle("A8")->getFont()->setBold(true);
                    $objSheet->getStyle("A8:F8")->applyFromArray($styleArray);
                    $objSheet->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle('A8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC');

                    $objSheet->SetCellValue('A9', $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue('B9', $this->lang->line("invoice_number"));
                    $objSheet->SetCellValue('C9', $this->lang->line("supplier_name"));
                    $objSheet->SetCellValue('D9', $this->lang->line("purchaser_name"));
                    $objSheet->SetCellValue('E9', $this->lang->line("quantity"));
                    $objSheet->SetCellValue('F9', $this->lang->line("amount"));
                    $objSheet->getStyle("A9:F9")->getFont()->setBold(true);
                    $objSheet->getStyle("A9:F9")->applyFromArray($styleArray);
                    $objSheet->getStyle('A9:F9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle('A9:F9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBEDFF');

                    $rowPurchaseData = 10;
                    foreach ($getACPMSummaryDataPurchase as $purchasedata) {

                        $objSheet->SetCellValue("A$rowPurchaseData", $purchasedata->expense_date);
                        $objSheet->SetCellValue("B$rowPurchaseData", $purchasedata->invoice_number);
                        $objSheet->getStyle("B$rowPurchaseData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->SetCellValue("C$rowPurchaseData", $purchasedata->supplier_name);
                        $objSheet->SetCellValue("D$rowPurchaseData", $purchasedata->purchaser_name);
                        $objSheet->SetCellValue("E$rowPurchaseData", $purchasedata->quantity + 0);
                        $objSheet->SetCellValue("F$rowPurchaseData", $purchasedata->amount + 0);

                        $rowPurchaseData++;
                    }

                    $lastPurchaseData = $rowPurchaseData - 1;
                    $objSheet->getStyle("F10:F$lastPurchaseData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                    $objSheet->getStyle("A10:F$lastPurchaseData")->applyFromArray($styleArray);

                    $objSheet->getStyle("F7")->getFont()->setBold(true);
                    $objSheet->SetCellValue('F7', "=SUM(F10:F$lastPurchaseData)");
                    $objSheet->getStyle("F7")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                    $objSheet->getStyle("F7")->applyFromArray($styleArray);

                    //Spend
                    $objSheet->SetCellValue('H8', $this->lang->line("spend"));
                    $objSheet->mergeCells('H8:J8');
                    $objSheet->getStyle("H8")->getFont()->setBold(true);
                    $objSheet->getStyle("H8:J8")->applyFromArray($styleArray);
                    $objSheet->getStyle('H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle('H8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC');

                    $objSheet->SetCellValue('H9', $this->lang->line("costsummary_date"));
                    $objSheet->SetCellValue('I9', $this->lang->line("supplier_name"));
                    $objSheet->SetCellValue('J9', $this->lang->line("quantity"));
                    $objSheet->getStyle("H9:J9")->getFont()->setBold(true);
                    $objSheet->getStyle("H9:J9")->applyFromArray($styleArray);
                    $objSheet->getStyle('H9:J9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle('H9:J9')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBEDFF');

                    $rowSpendData = 10;
                    foreach ($getACPMSummaryDataSpend as $spenddata) {

                        $objSheet->SetCellValue("H$rowSpendData", $spenddata->expense_date);
                        $objSheet->SetCellValue("I$rowSpendData", $spenddata->supplier_name);
                        $objSheet->SetCellValue("J$rowSpendData", $spenddata->quantity + 0);
                        $rowSpendData++;
                    }

                    $lastSpendData = $rowSpendData - 1;
                    $objSheet->getStyle("H10:J$lastSpendData")->applyFromArray($styleArray);

                    $objSheet->SetCellValue('B2', $getSupplierDetails[0]->supplier_name . " - " . $getSupplierDetails[0]->supplier_id);
                    $objSheet->SetCellValue('B3', "=SUM(E10:E$lastPurchaseData)");
                    $objSheet->SetCellValue('B4', "=SUM(J10:J$lastSpendData)");
                    $objSheet->SetCellValue('B5', "=B3-B4");

                    $objSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(18);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    $objSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(22);
                    $objSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(22);
                    $objSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(18);
                    $objSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(18);
                    $objSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(5);
                    $objSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(18);
                    $objSheet->getColumnDimension('I')->setAutoSize(false)->setWidth(22);
                    $objSheet->getColumnDimension('J')->setAutoSize(false)->setWidth(18);

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ACPMReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["pages"] = "";
                    $Return["redirect"] = false;
                    $this->output($Return);
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
            $Return['error'] = $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function deletefilesfromfolder()
    {
        $files = glob("assets/costingdocs/xmlupload/*.xml");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files1 = glob(FCPATH . "reports/ContractReports/*.xlsx");
        foreach ($files1 as $file1) {
            if (is_file($file1)) {
                unlink($file1);
            }
        }
    }

    public function dialog_generate_maintainance_report()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->post("type") == "generate_maintainance_report") {
                $data = array(
                    "pageheading" => $this->lang->line("generate_report"),
                    "pagetype" => "generate_report",
                    "originId" => $this->input->post("originId"),
                    "csrf_hash" => $this->security->get_csrf_hash(),
                    "suppliers" => $this->Costing_model->get_suppliers_by_origin($this->input->post("originId")),
                );
            }
            $this->load->view('costings/dialog_select_maintainance_date', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_maintainance_report()
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

                if ($this->input->post("dateType") == 1) {
                    $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data_by_costtype(
                        $this->input->post("originId"),
                        $this->input->post("year"),
                        5,
                        $this->input->post("supplierId")
                    );
                } else if ($this->input->post("dateType") == 2) {
                    $getExpenseSummaryReport = $this->Costing_model->get_expense_summary_data_by_daterange_costtype(
                        $this->input->post("originId"),
                        $this->input->post("fromDate"),
                        $this->input->post("toDate"),
                        5,
                        $this->input->post("supplierId")
                    );
                }

                if (count($getExpenseSummaryReport) > 0) {
                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('maintenance_report_type'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $styleThickArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK
                            )
                        )
                    );

                    $objSheet->SetCellValue('B1', $this->lang->line("maintenance_report_type"));
                    $objSheet->mergeCells('B1:C2');
                    $objSheet->getStyle("B1")->getFont()->setSize(13)->setBold(true);
                    $objSheet->getStyle("B1:C2")->applyFromArray($styleThickArray);
                    $objSheet->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("B1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    $headers = array_keys($getExpenseSummaryReport[0]);

                    $headers = array_filter($headers, function ($h) {
                        return $h !== 'id';
                    });

                    $headers = array_filter($headers, function ($h) {
                        return $h !== 'machine_type';
                    });

                    $headers = array_filter($headers, function ($h) {
                        return $h !== 'chassis_no';
                    });

                    $headers[] = 'YTD';

                    // Reset array indexes (important after filter)
                    $headers = array_values($headers);

                    $objSheet->SetCellValue("A4", $this->lang->line('machine_type'));
                    $objSheet->SetCellValue("B4", $this->lang->line('chassis_model'));

                    for ($c = 0; $c < count($headers); $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 2);
                        $headerText = explode("-", $headers[$c]);
                        $objSheet->SetCellValue("{$colLetter}4",  $this->lang->line(strtolower($headerText[0])) . (isset($headerText[1]) ? " - " . $headerText[1] : ''));
                    }


                    $rowCount = 5;
                    foreach ($getExpenseSummaryReport as $expenseSummary) {

                        $objSheet->SetCellValue("A$rowCount", $expenseSummary['machine_type']);
                        $objSheet->SetCellValue("B$rowCount", $expenseSummary['chassis_no']);
                        $objSheet->getStyle("B$rowCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        for ($c = 0; $c < count($headers); $c++) {

                            $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 2);
                            $objSheet->SetCellValue("{$colLetter}$rowCount", $expenseSummary[$headers[$c]]);
                        }

                        // Add YTD formula
                        $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(2); // C
                        $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) + 1 - 1); // before YTD col
                        $ytdColLetter  = PHPExcel_Cell::stringFromColumnIndex(count($headers) + 1);

                        $formula = "=SUM({$firstMonthCol}{$rowCount}:{$lastMonthCol}{$rowCount})";
                        $objSheet->SetCellValue("{$ytdColLetter}{$rowCount}", $formula);

                        $rowCount++;
                    }

                    $lastRowCount = $rowCount - 1;
                    $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) + 1);
                    $headerRange   = "A4:" . $lastColLetter . "$lastRowCount";

                    $objSheet->getStyle("C5:{$lastColLetter}{$rowCount}")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getFont()->setBold(true);
                    $objSheet->getStyle("$headerRange")->applyFromArray($styleArray);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');

                    // === Add Grand Total Row ===
                    $totalRow = $rowCount; // first empty row after data

                    $objSheet->setCellValue("A{$totalRow}", $this->lang->line('total'));
                    $objSheet->mergeCells("A{$totalRow}:B{$totalRow}");
                    $objSheet->getStyle("A{$totalRow}")->getFont()->setBold(true);
                    $objSheet->getStyle("A{$totalRow}:B{$totalRow}")->applyFromArray($styleArray);
                    $objSheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A{$totalRow}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("A{$totalRow}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFACD');

                    // Loop through all month columns + YTD
                    for ($c = 0; $c < count($headers); $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 2);
                        $firstDataRow = 5;
                        $lastDataRow  = $rowCount - 1;

                        // Formula: SUM(column from firstDataRow to lastDataRow)
                        $formula = "=SUM({$colLetter}{$firstDataRow}:{$colLetter}{$lastDataRow})";
                        $objSheet->setCellValue("{$colLetter}{$totalRow}", $formula);

                        // Make total bold
                        $objSheet->getStyle("{$colLetter}{$totalRow}")->getFont()->setBold(true);
                        $objSheet->getStyle("{$colLetter}{$totalRow}")->applyFromArray($styleArray);
                    }

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    for ($c = 0; $c < count($headers); $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 2);
                        $objSheet->getColumnDimension($colLetter)->setAutoSize(false)->setWidth(25);
                    }

                    if ($this->input->post("dateType") == 1) {
                        $getMachineTypeReport = $this->Costing_model->get_machine_type_by_year(
                            $this->input->post("originId"),
                            $this->input->post("year"),
                            5, 
                            $this->input->post("supplierId") 
                        );
                    } else if ($this->input->post("dateType") == 2) {
                        $getMachineTypeReport = $this->Costing_model->get_machine_type_by_date_range(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate"),
                            5, 
                            $this->input->post("supplierId") 
                        );
                    }

                    if (count($getMachineTypeReport) > 0) {

                        $sheetNo = 0;
                        $sheetNo++;
                        $objMaintenanceDetailedSheet = $this->excel->createSheet($sheetNo);
                        $objMaintenanceDetailedSheet->setTitle($this->lang->line("detailed_data"));
                        $objMaintenanceDetailedSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                        // ==== Styles ====
                        $headerStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => true,
                                'color' => array('rgb' => '000000'),
                                'size'  => 11,
                                'name'  => 'Calibri'
                            ),
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ADD8E6')
                            )
                        );

                        $dataStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => false,
                                'color' => array('rgb' => '000000'),
                                'size'  => 10,
                                'name'  => 'Calibri'
                            )
                        );

                        $totalStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => true,
                                'color' => array('rgb' => '000000'),
                                'size'  => 10,
                                'name'  => 'Calibri'
                            ),
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFACD') // light yellow
                            )
                        );

                        // ==== Header ====
                        $objMaintenanceDetailedSheet->SetCellValue('A3', $this->lang->line('machine_type'));
                        $objMaintenanceDetailedSheet->SetCellValue('B3', $this->lang->line('chassis_model'));
                        $objMaintenanceDetailedSheet->SetCellValue('C3', $this->lang->line('costsummary_date'));
                        $objMaintenanceDetailedSheet->SetCellValue('D3', $this->lang->line('supplier_name'));
                        $objMaintenanceDetailedSheet->SetCellValue('E3', $this->lang->line('concept'));
                        $objMaintenanceDetailedSheet->SetCellValue('F3', $this->lang->line('amount'));
                        $objMaintenanceDetailedSheet->SetCellValue('G3', $this->lang->line('total'));

                        $objMaintenanceDetailedSheet->getStyle("A3:G3")->applyFromArray($headerStyle);

                        // ==== Data ====
                        foreach ($getMachineTypeReport as $rowData) {
                            $startRow = $objMaintenanceDetailedSheet->getHighestRow() + 1;

                            if ($this->input->post("dateType") == 1) {
                                $getDetailedExpenseData = $this->Costing_model->get_machine_details_by_id_year(
                                    $this->input->post("originId"),
                                    $this->input->post("year"),
                                    5,
                                    $rowData->id,
                                    $this->input->post("supplierId")
                                );
                            } else if ($this->input->post("dateType") == 2) {
                                $getDetailedExpenseData = $this->Costing_model->get_machine_details_by_id_date_range(
                                    $this->input->post("originId"),
                                    $this->input->post("fromDate"),
                                    $this->input->post("toDate"),
                                    5,
                                    $rowData->id,
                                    $this->input->post("supplierId")
                                );
                            }

                            if (count($getDetailedExpenseData) > 0) {
                                // Start writing immediately after header/previous data
                                $rowCount = $objMaintenanceDetailedSheet->getHighestRow() + 1;
                                $startRow = $rowCount; // first row for this machine type

                                foreach ($getDetailedExpenseData as $detaileddata) {
                                    $objMaintenanceDetailedSheet->SetCellValue("A{$rowCount}", $rowData->machine_type);
                                    $objMaintenanceDetailedSheet->SetCellValue("B{$rowCount}", $rowData->chassis_no);
                                    $objMaintenanceDetailedSheet->getStyle("B{$rowCount}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objMaintenanceDetailedSheet->SetCellValue("C{$rowCount}", $detaileddata->expense_date);
                                    $objMaintenanceDetailedSheet->SetCellValue("D{$rowCount}", $detaileddata->supplier_name);
                                    $objMaintenanceDetailedSheet->SetCellValue("E{$rowCount}", $detaileddata->concept);
                                    $objMaintenanceDetailedSheet->SetCellValue("F{$rowCount}", $detaileddata->amount + 0);

                                    // Currency format for data rows
                                    $objMaintenanceDetailedSheet->getStyle("F{$rowCount}")
                                        ->getNumberFormat()
                                        ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                                    // Apply style to data row
                                    $objMaintenanceDetailedSheet->getStyle("A{$rowCount}:F{$rowCount}")->applyFromArray($dataStyle);

                                    $rowCount++; // move to next row
                                }

                                $endRow = $rowCount - 1; // last row for this machine type

                                // Merge A, B, F for this machine type
                                if ($endRow > $startRow) {
                                    $objMaintenanceDetailedSheet->mergeCells("A{$startRow}:A{$endRow}");
                                    $objMaintenanceDetailedSheet->mergeCells("B{$startRow}:B{$endRow}");
                                    $objMaintenanceDetailedSheet->mergeCells("G{$startRow}:G{$endRow}");
                                }

                                // Formula in G column
                                $objMaintenanceDetailedSheet->setCellValue("G{$startRow}", "=SUM(F{$startRow}:F{$endRow})");

                                // Apply total style to full merged F
                                $objMaintenanceDetailedSheet->getStyle("G{$startRow}:G{$endRow}")->applyFromArray($totalStyle);

                                // Currency format for totals
                                $objMaintenanceDetailedSheet->getStyle("G{$startRow}:G{$endRow}")
                                    ->getNumberFormat()
                                    ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                                // Center vertically for merged A & B
                                $objMaintenanceDetailedSheet->getStyle("A{$startRow}:A{$endRow}")
                                    ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                $objMaintenanceDetailedSheet->getStyle("B{$startRow}:B{$endRow}")
                                    ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            }
                        }

                        $objMaintenanceDetailedSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(30);
                        $objMaintenanceDetailedSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(25);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);
                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    unset($styleThickArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename = 'MaintenanceReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["pages"] = "";
                    $Return["redirect"] = false;
                    $this->output($Return);
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
            $Return['error'] = $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function dialog_generate_miscellaneous_report()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if (!empty($session)) {
            if ($this->input->post("type") == "generate_miscellaneous_report") {
                $data = array(
                    "pageheading" => $this->lang->line("generate_report"),
                    "pagetype" => "generate_report",
                    "originId" => $this->input->post("originId"),
                    "csrf_hash" => $this->security->get_csrf_hash(),
                    "suppliers" => $this->Costing_model->get_suppliers_by_origin($this->input->post("originId")),
                );
            }
            $this->load->view('costings/dialog_select_miscellaneous_date', $data);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function generate_miscellaneous_report()
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

                if ($this->input->post("dateType") == 1) {
                    $getExpenseSummaryReport = $this->Costing_model->get_miscellaneous_summary_data_by_costtype(
                        $this->input->post("originId"),
                        $this->input->post("year"),
                        6,
                        $this->input->post("supplierId")
                    );
                } else if ($this->input->post("dateType") == 2) {
                    $getExpenseSummaryReport = $this->Costing_model->get_miscellaneous_summary_data_by_daterange_costtype(
                        $this->input->post("originId"),
                        $this->input->post("fromDate"),
                        $this->input->post("toDate"),
                        6,
                        $this->input->post("supplierId")
                    );
                }

                if (count($getExpenseSummaryReport) > 0) {
                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('miscellaneous_report_type'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $styleThickArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK
                            )
                        )
                    );

                    $objSheet->SetCellValue('B1', $this->lang->line("miscellaneous_report_type"));
                    $objSheet->mergeCells('B1:C2');
                    $objSheet->getStyle("B1")->getFont()->setSize(13)->setBold(true);
                    $objSheet->getStyle("B1:C2")->applyFromArray($styleThickArray);
                    $objSheet->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("B1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    $headers = array_keys($getExpenseSummaryReport[0]);

                    $headers = array_filter($headers, function ($h) {
                        return $h !== 'id';
                    });

                    $headers[] = 'YTD';

                    // Reset array indexes (important after filter)
                    $headers = array_values($headers);
                    for ($c = 0; $c < count($headers); $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c);
                        $headerText = explode("-", $headers[$c]);
                        $objSheet->SetCellValue("{$colLetter}4",  $this->lang->line(strtolower($headerText[0])) . (isset($headerText[1]) ? " - " . $headerText[1] : ''));
                    }

                    $rowCount = 5;
                    foreach ($getExpenseSummaryReport as $expenseSummary) {

                        for ($c = 0; $c < count($headers); $c++) {

                            $colLetter = PHPExcel_Cell::stringFromColumnIndex($c);
                            $objSheet->SetCellValue("{$colLetter}$rowCount", $expenseSummary[$headers[$c]]);
                        }

                        // Add YTD formula
                        $firstMonthCol = PHPExcel_Cell::stringFromColumnIndex(1); // C
                        $lastMonthCol  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 2); // before YTD col
                        $ytdColLetter  = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);

                        $formula = "=SUM({$firstMonthCol}{$rowCount}:{$lastMonthCol}{$rowCount})";
                        $objSheet->SetCellValue("{$ytdColLetter}{$rowCount}", $formula);

                        $rowCount++;
                    }

                    $lastRowCount = $rowCount - 1;
                    $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers) - 1);
                    $headerRange   = "A4:" . $lastColLetter . "$lastRowCount";

                    $objSheet->getStyle("B5:{$lastColLetter}{$rowCount}")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getFont()->setBold(true);
                    $objSheet->getStyle("$headerRange")->applyFromArray($styleArray);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("A4:" . $lastColLetter . "4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');

                    // === Add Grand Total Row ===
                    $totalRow = $rowCount; // first empty row after data

                    $objSheet->setCellValue("A{$totalRow}", $this->lang->line('total'));
                    $objSheet->getStyle("A{$totalRow}")->getFont()->setBold(true);
                    $objSheet->getStyle("A{$totalRow}")->applyFromArray($styleArray);
                    $objSheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objSheet->getStyle("A{$totalRow}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objSheet->getStyle("A{$totalRow}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFACD');

                    // Loop through all month columns + YTD
                    for ($c = 0; $c < count($headers) - 1; $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 1);
                        $firstDataRow = 5;
                        $lastDataRow  = $rowCount - 1;

                        $formula = "=SUM({$colLetter}{$firstDataRow}:{$colLetter}{$lastDataRow})";
                        $objSheet->setCellValue("{$colLetter}{$totalRow}", $formula);

                        $objSheet->getStyle("{$colLetter}{$totalRow}")->getFont()->setBold(true);
                        $objSheet->getStyle("{$colLetter}{$totalRow}")->applyFromArray($styleArray);
                    }

                    for ($c = 0; $c < count($headers); $c++) {
                        $colLetter = PHPExcel_Cell::stringFromColumnIndex($c + 1);
                        $objSheet->getColumnDimension($colLetter)->setAutoSize(false)->setWidth(25);
                    }

                    if ($this->input->post("dateType") == 1) {
                        $getDetailedReport = $this->Costing_model->get_miscallaneous_detailed_data_year(
                            $this->input->post("originId"),
                            $this->input->post("year"),
                            6, 
                            $this->input->post("supplierId")
                        );
                    } else if ($this->input->post("dateType") == 2) {
                        $getDetailedReport = $this->Costing_model->get_miscallaneous_detailed_data_date_range(
                            $this->input->post("originId"),
                            $this->input->post("fromDate"),
                            $this->input->post("toDate"),
                            6,
                            $this->input->post("supplierId")
                        );
                    }

                    if (count($getDetailedReport) > 0) {

                        $sheetNo = 0;
                        $sheetNo++;
                        $objMaintenanceDetailedSheet = $this->excel->createSheet($sheetNo);
                        $objMaintenanceDetailedSheet->setTitle($this->lang->line("detailed_data"));
                        $objMaintenanceDetailedSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                        // ==== Styles ====
                        $headerStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => true,
                                'color' => array('rgb' => '000000'),
                                'size'  => 11,
                                'name'  => 'Calibri'
                            ),
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ADD8E6')
                            )
                        );

                        $dataStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => false,
                                'color' => array('rgb' => '000000'),
                                'size'  => 10,
                                'name'  => 'Calibri'
                            )
                        );

                        $totalStyle = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '000000')
                                )
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'font' => array(
                                'bold' => true,
                                'color' => array('rgb' => '000000'),
                                'size'  => 10,
                                'name'  => 'Calibri'
                            ),
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFACD') // light yellow
                            )
                        );

                        // ==== Header ====
                        $objMaintenanceDetailedSheet->SetCellValue('A3', $this->lang->line('costsummary_date'));
                        $objMaintenanceDetailedSheet->SetCellValue('B3', $this->lang->line('concept'));
                        $objMaintenanceDetailedSheet->SetCellValue('C3', $this->lang->line('supplier_name'));
                        $objMaintenanceDetailedSheet->SetCellValue('D3', $this->lang->line('amount'));

                        $objMaintenanceDetailedSheet->getStyle("A3:D3")->applyFromArray($headerStyle);

                        $startRow = 4;
                        foreach ($getDetailedReport as $rowData) {
                            $startRow = $objMaintenanceDetailedSheet->getHighestRow() + 1;

                            $rowCount = $objMaintenanceDetailedSheet->getHighestRow() + 1;
                            $startRow = $rowCount; // first row for this machine type

                            $objMaintenanceDetailedSheet->SetCellValue("A{$rowCount}", $rowData->expense_date);
                            $objMaintenanceDetailedSheet->SetCellValue("B{$rowCount}", $rowData->concept);
                            $objMaintenanceDetailedSheet->SetCellValue("C{$rowCount}", $rowData->supplier_name);
                            $objMaintenanceDetailedSheet->SetCellValue("D{$rowCount}", $rowData->amount + 0);

                            // Currency format for data rows
                            $objMaintenanceDetailedSheet->getStyle("D{$rowCount}")
                                ->getNumberFormat()
                                ->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                            // Apply style to data row
                            $objMaintenanceDetailedSheet->getStyle("A{$rowCount}:D{$rowCount}")->applyFromArray($dataStyle);

                            $rowCount++; // move to next row
                        }

                        $objMaintenanceDetailedSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objMaintenanceDetailedSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(25);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);
                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    unset($styleThickArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename = 'MiscellaneousReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["pages"] = "";
                    $Return["redirect"] = false;
                    $this->output($Return);
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
            $Return['error'] = $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function fetch_machines()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0 && $this->input->get('supplierid') > 0) {
                $getMachines = $this->Costing_model->fetch_machines_masters($this->input->get('originid'), $this->input->get('supplierid'));
                foreach ($getMachines as $machine) {
                    $result = $result . "<option value='" . $machine->id . "'>" . $machine->machine_type . "</option>";
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
}
