<?php

// error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
// ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Operationalcost extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Costing_model");
        $this->load->model("Forestry_model");
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
        $data["title"] = $this->lang->line("operational_costs") . " - " . $this->lang->line("forestry_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_forestry";
        if (!empty($session)) {

            //$data["machines"] = $this->Costing_model->fetch_machines_masters();
            $data["csrfhash"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("forestry/operational_cost", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function operationalcosting_list()
    {
        $draw = intval($this->input->get("draw"));
        $originId = intval($this->input->get("originId"));
        $costType = intval($this->input->get("costType"));

        $operationalCosting = $this->Forestry_model->get_operational_costing($originId, $costType);

        $data = array();

        if ($costType == 4) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_acpm" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_acpm" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->invoice_number,
                    $r->expense_date,
                    ($r->quantity + 0),
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 5) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_maintenance" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_maintenance" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->invoice_number,
                    $r->expense_date,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 6) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_others" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_others" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->invoice_number,
                    $r->expense_date,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 7) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_machinerental" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_machinerental" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->expense_date,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 8) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_manuallabour" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_manuallabour" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->expense_date,
                    '$ ' . number_format(($r->amount + 0), 2, ',', '.'),
                );
            }
        } else if ($costType == 9) {

            foreach ($operationalCosting as $r) {

                $editCostings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcosting_lubricants" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
                        <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecosting_lubricants" data-toggle="modal" data-target=".edit-modal-data" data-costing_id="' . $r->id . '"><span class="fas fa-trash"></span></button></span>';

                $data[] = array(
                    $editCostings,
                    $r->supplier_name,
                    $r->contract_code . ' -- ' . $r->description,
                    $r->invoice_number,
                    $r->expense_date,
                    ($r->quantity + 0),
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

    public function save_opertaional_cost()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "operationalcosting") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "saveACPM") {

                    $originId = $this->input->post("originId");
                    $acpmDate = $this->input->post('acpmDate');
                    $acpmQuantity = $this->input->post('acpmQuantity');
                    $acpmTotalValue = $this->input->post("acpmTotalValue");
                    $acpmClaimRemarks = $this->input->post("acpmClaimRemarks");
                    $acpmSuppliers = $this->input->post("acpmSuppliers");
                    $acpmPurchaseContract = $this->input->post("acpmPurchaseContract");
                    $acpmPurchaser = $this->input->post("acpmPurchaser");
                    $acpmInvoiceNumber = $this->input->post("acpmInvoiceNumber");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $acpmSuppliers,
                            "contract_id" => $acpmPurchaseContract,
                            "purchaser_id" => $acpmPurchaser,
                            "invoice_number" => $acpmInvoiceNumber,
                            "quantity" => $acpmQuantity,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $acpmTotalValue,
                            "expense_date" => $acpmDate,
                            "remarks" => $acpmClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "contract_id" => $acpmPurchaseContract,
                            "purchaser_id" => $acpmPurchaser,
                            "invoice_number" => $acpmInvoiceNumber,
                            "quantity" => $acpmQuantity,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $acpmTotalValue,
                            "expense_date" => $acpmDate,
                            "remarks" => $acpmClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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
                    $maintainanceSubTotal = $this->input->post('maintainanceSubTotal');
                    $maintainanceTax = $this->input->post("maintainanceTax");
                    $maintainanceAmount = $this->input->post("maintainanceAmount");
                    $maintainanceClaimRemarks = $this->input->post("maintainanceClaimRemarks");
                    $maintainanceContract = $this->input->post("maintainanceContract");
                    $maintainanceSuppliers = $this->input->post("maintainanceSuppliers");
                    $maintainanceInvoiceNumber = $this->input->post("maintainanceInvoiceNumber");
                    $maintainancePurchaser = $this->input->post("maintainancePurchaser");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $maintainanceSuppliers,
                            "contract_id" => $maintainanceContract,
                            "purchaser_id" => $maintainancePurchaser,
                            "invoice_number" => $maintainanceInvoiceNumber,
                            "quantity" => 0,
                            "sub_total" => $maintainanceSubTotal,
                            'tax_amount' => $maintainanceTax,
                            "amount" => $maintainanceAmount,
                            "expense_date" => $maintainanceDate,
                            "remarks" => $maintainanceClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "contract_id" => $maintainanceContract,
                            "purchaser_id" => $maintainancePurchaser,
                            "invoice_number" => $maintainanceInvoiceNumber,
                            "quantity" => 0,
                            "sub_total" => $maintainanceSubTotal,
                            'tax_amount' => $maintainanceTax,
                            "amount" => $maintainanceAmount,
                            "expense_date" => $maintainanceDate,
                            "remarks" => $maintainanceClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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
                } else if ($this->input->post("action_type") == "saveMachineRental") {

                    $originId = $this->input->post("originId");
                    $machinerentalDate = $this->input->post('machinerentalDate');
                    $machinerentalContract = $this->input->post('machinerentalContract');
                    $machinerentalAmount = $this->input->post("machinerentalAmount");
                    $machinerentalClaimRemarks = $this->input->post("machinerentalClaimRemarks");
                    $machinerentalSuppliers = $this->input->post("machinerentalSuppliers");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $machinerentalSuppliers,
                            "contract_id" => $machinerentalContract,
                            "purchaser_id" => '',
                            "invoice_number" => '',
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $machinerentalAmount,
                            "expense_date" => $machinerentalDate,
                            "remarks" => $machinerentalClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "supplier_id" => $machinerentalSuppliers,
                            "contract_id" => $machinerentalContract,
                            "purchaser_id" => '',
                            "invoice_number" => '',
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $machinerentalAmount,
                            "expense_date" => $machinerentalDate,
                            "remarks" => $machinerentalClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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
                } else if ($this->input->post("action_type") == "saveManualLabour") {

                    $originId = $this->input->post("originId");
                    $manuallabourDate = $this->input->post('manuallabourDate');
                    $manuallabourContract = $this->input->post('manuallabourContract');
                    $manuallabourAmount = $this->input->post("manuallabourAmount");
                    $manuallabourClaimRemarks = $this->input->post("manuallabourClaimRemarks");
                    $manuallabourSuppliers = $this->input->post("manuallabourSuppliers");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $manuallabourSuppliers,
                            "contract_id" => $manuallabourContract,
                            "purchaser_id" => '',
                            "invoice_number" => '',
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $manuallabourAmount,
                            "expense_date" => $manuallabourDate,
                            "remarks" => $manuallabourClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "supplier_id" => $manuallabourSuppliers,
                            "contract_id" => $manuallabourContract,
                            "purchaser_id" => '',
                            "invoice_number" => '',
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $manuallabourAmount,
                            "expense_date" => $manuallabourDate,
                            "remarks" => $manuallabourClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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
                } else if ($this->input->post("action_type") == "saveLubricants") {

                    $originId = $this->input->post("originId");
                    $lubricantsDate = $this->input->post('lubricantsDate');
                    $lubricantsQuantity = $this->input->post('lubricantsQuantity');
                    $lubricantsTotalValue = $this->input->post("lubricantsTotalValue");
                    $lubricantsClaimRemarks = $this->input->post("lubricantsClaimRemarks");
                    $lubricantsSuppliers = $this->input->post("lubricantsSuppliers");
                    $lubricantsPurchaseContract = $this->input->post("lubricantsPurchaseContract");
                    $lubricantsPurchaser = $this->input->post("lubricantsPurchaser");
                    $lubricantsInvoiceNumber = $this->input->post("lubricantsInvoiceNumber");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $lubricantsSuppliers,
                            "contract_id" => $lubricantsPurchaseContract,
                            "purchaser_id" => $lubricantsPurchaser,
                            "invoice_number" => $lubricantsInvoiceNumber,
                            "quantity" => $lubricantsQuantity,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $lubricantsTotalValue,
                            "expense_date" => $lubricantsDate,
                            "remarks" => $lubricantsClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "supplier_id" => $lubricantsSuppliers,
                            "contract_id" => $lubricantsPurchaseContract,
                            "purchaser_id" => $lubricantsPurchaser,
                            "invoice_number" => $lubricantsInvoiceNumber,
                            "quantity" => $lubricantsQuantity,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $lubricantsTotalValue,
                            "expense_date" => $lubricantsDate,
                            "remarks" => $lubricantsClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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
                } else if ($this->input->post("action_type") == "saveOthers") {

                    $originId = $this->input->post("originId");
                    $othersDate = $this->input->post('othersDate');
                    $othersTotalValue = $this->input->post("othersTotalValue");
                    $othersClaimRemarks = $this->input->post("othersClaimRemarks");
                    $othersSuppliers = $this->input->post("othersSuppliers");
                    $othersPurchaseContract = $this->input->post("othersPurchaseContract");
                    $othersPurchaser = $this->input->post("othersPurchaser");
                    $othersInvoiceNumber = $this->input->post("othersInvoiceNumber");
                    $editId = $this->input->post("edit_id");

                    $costType = $this->input->post("costType");

                    if ($editId > 0) {

                        $dataCosting = array(
                            "supplier_id" => $othersSuppliers,
                            "contract_id" => $othersPurchaseContract,
                            "purchaser_id" => $othersPurchaser,
                            "invoice_number" => $othersInvoiceNumber,
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $othersTotalValue,
                            "expense_date" => $othersDate,
                            "remarks" => $othersClaimRemarks,
                            "created_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $updateCosting = $this->Forestry_model->update_opertational_costs($dataCosting, $editId);

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
                            "supplier_id" => $othersSuppliers,
                            "contract_id" => $othersPurchaseContract,
                            "purchaser_id" => $othersPurchaser,
                            "invoice_number" => $othersInvoiceNumber,
                            "quantity" => 0,
                            "sub_total" => 0,
                            'tax_amount' => 0,
                            "amount" => $othersTotalValue,
                            "expense_date" => $othersDate,
                            "remarks" => $othersClaimRemarks,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId
                        );

                        $insertCosting = $this->Forestry_model->add_opertational_costs($dataCosting);

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

                if ($_FILES['others_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['others_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['others_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["others_xmlupload"]["tmp_name"];
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
            } else if ($costingType == 7) {

                if ($_FILES['machinerental_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['machinerental_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['machinerental_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["machinerental_xmlupload"]["tmp_name"];
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
            } else if ($costingType == 9) {

                if ($_FILES['lubricants_xmlupload']['size'] > 0) {
                    if (is_uploaded_file($_FILES['lubricants_xmlupload']['tmp_name'])) {
                        $allowed =  array('xml', "XML");
                        $filename = $_FILES['lubricants_xmlupload']['name'];
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {
                            if ($ext == "xml" || $ext == "XML") {
                                $tmp_name = $_FILES["lubricants_xmlupload"]["tmp_name"];
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
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 9);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 9,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 9);
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
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 7);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 7,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 7);
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
            $checkCompanyIdExistsCount = $this->Master_model->check_company_id_costingpurchaser_count($companyIdNode->item(0)->nodeValue, 9);
            if ($checkCompanyIdExistsCount[0]->cnt == 0) {

                $dataSupplier = array(
                    "purchaser_name" => $registrationNameNode->item(0)->nodeValue,
                    "company_id" => $companyIdNode->item(0)->nodeValue,
                    "costing_type" => 9,
                    "created_by" => $session['user_id'],
                    "updated_by" => $session['user_id'],
                    'is_active' => 1,
                    'origin_id' => $originId,
                );

                $insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);
                $supplierId = $insertSupplier + 0;
                $isNewSupplier = true;
            } else {
                $checkCompanyIdExists = $this->Master_model->check_company_id_costingpurchaser($companyIdNode->item(0)->nodeValue, 9);
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

    public function dialog_operational_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            if ($this->input->get('type') == "editmaintainance") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 5, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'purchaserId' => $operationalCostDetails[0]->purchaser_id + 0,
                    'invoiceNumber' => $operationalCostDetails[0]->invoice_number,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'subTotal' => $operationalCostDetails[0]->sub_total + 0,
                    'taxAmount' => $operationalCostDetails[0]->tax_amount + 0,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "editmachinerental") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 7, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "editmanuallabour") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 8, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "editacpm") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 4, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'purchaserId' => $operationalCostDetails[0]->purchaser_id + 0,
                    'invoiceNumber' => $operationalCostDetails[0]->invoice_number,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'quantity' => $operationalCostDetails[0]->quantity + 0,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "editlubricants") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 9, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'purchaserId' => $operationalCostDetails[0]->purchaser_id + 0,
                    'invoiceNumber' => $operationalCostDetails[0]->invoice_number,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'quantity' => $operationalCostDetails[0]->quantity + 0,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "editothers") {

                $operationalCostDetails = $this->Forestry_model->get_operational_cost_details_byid($this->input->get('originId'), 6, $this->input->get('costingId'));

                $data = array(
                    'supplierId' => $operationalCostDetails[0]->supplier_id + 0,
                    'contractId' => $operationalCostDetails[0]->contract_id + 0,
                    'purchaserId' => $operationalCostDetails[0]->purchaser_id + 0,
                    'invoiceNumber' => $operationalCostDetails[0]->invoice_number,
                    'expenseDate' => $operationalCostDetails[0]->expense_date,
                    'amount' => $operationalCostDetails[0]->amount + 0,
                    'remarks' => $operationalCostDetails[0]->remarks,
                    'costingid' => $operationalCostDetails[0]->id,
                );

                $Return["result"] = $data;
                $Return["error"] = "";
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
            } else if ($this->input->get('type') == "deletemaintainanceconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deletemaintenance",
                    'xin_table' => "#xin_table_maintenance",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletemaintenance") {

                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 5);

                if ($operationalDelete) {
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
            } else if ($this->input->get('type') == "deletemachinerentalconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deletemachinerental",
                    'xin_table' => "#xin_table_machinerental",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletemachinerental") {

                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 7);

                if ($operationalDelete) {
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
            } else if ($this->input->get('type') == "deletemanuallabourconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deletemanuallabour",
                    'xin_table' => "#xin_table_manuallabour",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletemanuallabour") {
                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 8);

                if ($operationalDelete) {
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
            } else if ($this->input->get('type') == "deletelubricantsconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deletelubricants",
                    'xin_table' => "#xin_table_lubricants",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletelubricants") {
                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 9);

                if ($operationalDelete) {
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
            } else if ($this->input->get('type') == "deleteacpmconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deleteacpm",
                    'xin_table' => "#xin_table_acpm",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deleteacpm") {
                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 4);

                if ($operationalDelete) {
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
            } else if ($this->input->get('type') == "deleteothersconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('cid'),
                    'actionurl' => "forestry/operationalcost/dialog_operational_action",
                    'actiontype' => "deleteothers",
                    'xin_table' => "#xin_table_others",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deleteothers") {
                $costingId = $this->input->get('inputid');

                $dataDelete = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $operationalDelete = $this->Forestry_model->update_operational_cost($dataDelete, $costingId, 6);

                if ($operationalDelete) {
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
}
