<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Extractioncost extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_model");
        $this->load->model("Master_model");
        $this->load->model("Settings_model");
        $this->load->model("Forestry_model");
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
        $data['title'] = $this->lang->line('extraction_cost') . " - " . $this->lang->line('forestry_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (empty($session)) {
            redirect("/logout");
        }

        $data['path_url'] = 'cgr_forestry';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("forestry/extraction_cost", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function extraction_list()
    {
        $data['title'] =  $this->lang->line('extraction_cost') . " - " . $this->lang->line('forestry_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            $this->load->view("forestry/extraction_cost", $data);
        } else {
            redirect("/logout");
        }

        $originid = intval($this->input->get("originid"));

        $extractionData = $this->Forestry_model->get_extractions_data($originid);

        $data = array();

        foreach ($extractionData as $row) {
            $editExtraction = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editextraction" data-toggle="modal" data-target=".edit-modal-data" data-extraction_id="' . $row->id . '"><span class="fas fa-pencil"></span></button></span>
            <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deleteextraction" data-toggle="modal" data-target=".delete-modal-data" data-extraction_id="' . $row->id . '"><span class="fas fa-trash"></span></button></span>';

            $data[] = array(
                $editExtraction,
                $row->supplier_name,
                $row->contract_code . " -- " . $row->description,
                $row->extraction_date,
                $row->total_trees,
                $row->tota_pieces,
                $row->total_volume,
                $row->total_cost,
            );
        }

        $output = array(
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function dialog_extraction_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            if ($this->input->get('type') == "addcost") {
                $data = array(
                    'pageheading' => $this->lang->line('add_extraction_cost'),
                    'pagetype' => "add",
                    'extraction_id' => 0,
                    'csrfcgr' => $this->security->get_csrf_hash(),
                    'extractionTreesLists' => array(),
                    'extractionTreesListCount' => 0,
                    'totalCost' => 0,
                    'totalTrees'  => 0,
                    'totalPieces' => 0,
                    'totalVolume' => 0.000
                );

                $this->load->view('forestry/dialog_extraction_cost', $data);
            } else if ($this->input->get('type') == "editcost") {

                $extractionDetails = $this->Forestry_model->get_extractions_details_byid($this->input->get('eId'));
                $extractionContractDetails = $this->Contract_model->fetch_contract_details_by_contract_id($extractionDetails[0]->origin_id, $extractionDetails[0]->contract_id);
                $totals = $this->Forestry_model->get_extraction_totals($this->input->get('eId'));

                $data = array(
                    'pageheading' => $this->lang->line('edit_extraction_cost'),
                    'pagetype' => "edit",
                    'extraction_id' => $this->input->get('eId'),
                    'csrfcgr' => $this->security->get_csrf_hash(),
                    'extractionDetails' => $extractionDetails,
                    'suppliers' => $this->Contract_model->get_suppliers_by_origin($extractionDetails[0]->origin_id),
                    'purchaseContracts' => $this->Contract_model->get_contracts_by_suppliers($extractionDetails[0]->origin_id, $extractionDetails[0]->supplier_id),
                    'contractDescription' => $extractionContractDetails,
                    'totalCost' => $totals->total_trees * $extractionDetails[0]->extraction_cost + 0,
                    'totalTrees'  => (int) $totals->total_trees,
                    'totalPieces' => (int) $totals->total_pieces,
                    'totalVolume' => number_format((float)$totals->total_volume, 3)
                );

                $this->load->view('forestry/dialog_extraction_cost', $data);
            } else if ($this->input->get('type') == "deleteconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('eid'),
                    'actionurl' => "forestry/extractioncost/dialog_extraction_action",
                    'actiontype' => "deleteextraction",
                    'xin_table' => "#xin_table_extractions",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deleteextraction") {

                $extractionId = $this->input->get('inputid');

                $dataDeleteExtraction = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $extractionDelete = $this->Forestry_model->update_extraction($dataDeleteExtraction, $extractionId);

                if ($extractionDelete) {
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
            } else if ($this->input->get('type') == "deleteextractionconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('eid'),
                    'inputid1' => $this->input->get('tid'),
                    'actionurl' => "forestry/extractioncost/dialog_extraction_action",
                    'actiontype' => "deleteextractiontree",
                    'xin_table' => "#xin_table_extraction_trees",
                );
                $this->load->view('dialogs/dialog_confirmation_forestry', $data);
            } else if ($this->input->get('type') == "deleteextractiontree") {

                $extractionId = $this->input->get('inputid');
                $treeId = $this->input->get('inputid1');

                $dataDeleteExtractionTrees = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $extractionTreeDelete = $this->Forestry_model->update_extraction_tree_byid($dataDeleteExtractionTrees, $extractionId, $treeId);

                $dataDeleteExtractionTreeDetails = array(
                    "updated_by" => $session['user_id'],
                    "is_active" => 0,
                );

                $extractionTreeDetailDelete = $this->Forestry_model->update_extraction_tree_details_byid($dataDeleteExtractionTrees, $extractionId, $treeId);

                if ($extractionTreeDelete && $extractionTreeDelete) {
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
            } else if ($this->input->get('type') == "generatereport") {
                $data = array(
                    'pageheading' => $this->lang->line('generate_report'),
                    'csrf_hash' => $this->security->get_csrf_hash(),
                );

                $this->load->view('forestry/dialog_generate_extraction', $data);
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function fetch_suppliers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getSuppliers = $this->Contract_model->get_suppliers_by_origin($this->input->get('originid'));
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

    public function get_contracts_by_supplier()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    $getContracts = $this->Contract_model->get_contracts_by_suppliers($this->input->get('originid'), $this->input->get('supplierid'));
                    foreach ($getContracts as $contract) {
                        if ($contract->description == null || $contract->description == "") {
                            $result = $result . "<option value='" . $contract->contract_id . "'>" . $contract->contract_code . "</option>";
                        } else {
                            $result = $result . "<option value='" . $contract->contract_id . "'>" . $contract->contract_code . " -- " . $contract->description . "</option>";
                        }
                    }
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

    public function fetch_contract_details()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            if ($this->input->get('originid') > 0 && $this->input->get('contractid') > 0) {
                $getContractDetails = $this->Contract_model->fetch_contract_details_by_contract_id($this->input->get('originid'), $this->input->get('contractid'));

                if (count($getContractDetails) == 1) {
                    $data = array(
                        'contract_desc' => $getContractDetails[0]->description,
                        'extraction_cost' => ($getContractDetails[0]->extraction_cost + 0),
                    );

                    $Return['result'] = $data;
                    $Return['redirect'] = false;
                    $this->output($Return);
                } else {
                    $Return['error'] = $this->lang->line('error_fetch_details');
                    $Return['redirect'] = false;
                    $this->output($Return);
                }
            } else {
                $Return['error'] = "";
                $Return['redirect'] = false;
                $this->output($Return);
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function save_extraction()
    {
        $Return = array('result' => '', 'error' => '', 'warning' => '', 'redirect' => false, 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if ($this->input->post('actionType') == 'extraction') {

            if (!empty($session)) {

                $originId = $this->input->post('originId');
                $supplierId = $this->input->post('supplierId');
                $contractId = $this->input->post('contractId');
                $extractionDate = $this->input->post('extractionDate');
                $extractionId = $this->input->post('extractionId');
                $extractionCost = $this->input->post('extractionCost');

                if ($this->input->post('pageType') == 'add') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    if ($this->Forestry_model->get_exist_forestry_extractions($originId, $supplierId, $contractId, $extractionDate) > 0) {
                        $Return['result'] = "";
                        $Return['error'] = "";
                        $Return['redirect'] = false;
                        $Return['warning'] = $this->lang->line('exist_forestry_extraction');
                        $this->output($Return);
                        exit;
                    } else {

                        $dataExtraction = array(
                            "supplier_id" => $supplierId,
                            "contract_id" => $contractId,
                            "extraction_date" => $extractionDate,
                            "extraction_cost" => $extractionCost,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                            "origin_id" => $originId,
                        );

                        $insertForestryExtractions = $this->Forestry_model->add_forestry_extractions($dataExtraction);

                        if ($insertForestryExtractions > 0) {
                            $Return['result'] = $this->lang->line('data_added');
                            $Return['error'] = "";
                            $Return['extraction_id'] = $insertForestryExtractions;
                            $this->output($Return);
                            exit;
                        } else {
                            $Return['result'] = "";
                            $Return['error'] = $this->lang->line('error_adding');
                            $this->output($Return);
                            exit;
                        }
                    }
                } else if ($this->input->post('pageType') == 'edit') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $warehouse_id = $this->input->post('warehouse_id');
                    $name = $this->input->post('wh_name');
                    $ownersname = $this->input->post('wh_owners_name');
                    $address = $this->input->post('wh_address');
                    $status = $this->input->post('status');
                    $whorigin = $this->input->post('whorigin');
                    $port_of_loading = $this->input->post('port_of_loading');

                    if ($status == 0) {
                        $status = false;
                    } else {
                        $status = true;
                    }

                    $dataWH = array(
                        "warehouse_name" => $name,
                        "warehouse_ownername" => $ownersname,
                        "warehouse_address" => $address,
                        "pol" => $port_of_loading,
                        "updatedby" => $session['user_id'],
                        'is_active' => $status,
                        'origin_id' => $whorigin,
                    );

                    $updateWarehouse = $this->Master_model->update_warehouse($dataWH, $warehouse_id);

                    if ($updateWarehouse == true) {
                        $Return['result'] = $this->lang->line('data_updated');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    } else {
                        $Return['error'] = $this->lang->line('error_updating');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
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
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }

    public function save_extraction_details()
    {
        $Return = array('result' => '', 'error' => '', 'warning' => '', 'redirect' => false, 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if ($this->input->post('actionType') == 'extractionTrees') {

            if (!empty($session)) {

                $extractionId = $this->input->post('extractionId');
                $totalPieces = $this->input->post('totalPieces');
                $totalVolume = $this->input->post('totalVolume');
                $extractionData = $this->input->post('extractionData');
                $treeNo = $this->input->post('treeNo');

                if ($this->input->post('pageType') == 'add') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $dataExtractionTrees = array(
                        "extraction_id" => $extractionId,
                        "tree_no" => $treeNo,
                        "total_pieces" => $totalPieces,
                        "total_volume" => $totalVolume,
                        "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'],
                        "is_active" => 1,
                    );

                    $insertForestryExtractionTrees = $this->Forestry_model->add_forestry_extraction_trees($dataExtractionTrees);

                    if ($insertForestryExtractionTrees > 0) {

                        $extractionDataJson = json_decode($extractionData, true);

                        if (count($extractionDataJson) > 0) {

                            foreach ($extractionDataJson as $extractdata) {
                                $dataExportTreeDetails = array(
                                    "extraction_id" => $extractionId,
                                    "extraction_tree_id" => $insertForestryExtractionTrees,
                                    "log_no" => $extractdata["log_number"] + 0,
                                    "circumference" => $extractdata["circumference"] + 0,
                                    "length" => $extractdata["length"] + 0,
                                    "volume" => $extractdata["volume"] + 0,
                                    "created_by" => $session['user_id'],
                                    "updated_by" => $session['user_id'],
                                    "is_active" => 1
                                );

                                $insertExtractTreesDetails = $this->Forestry_model->add_forestry_extraction_tree_details($dataExportTreeDetails);
                            }
                        }

                        $dataExtractionTrees = array(
                            "extraction_id" => $extractionId,
                            "tree_no" => $treeNo,
                            "total_pieces" => $totalPieces,
                            "total_volume" => $totalVolume,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $Return['result'] = $this->lang->line('data_added');
                        $Return['error'] = "";
                        $this->output($Return);
                        exit;
                    } else {
                        $Return['result'] = "";
                        $Return['error'] = $this->lang->line('error_adding');
                        $this->output($Return);
                        exit;
                    }
                } else if ($this->input->post('pageType') == 'edit') {

                    $treeId = $this->input->post("treeId");

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $dataDeleteExtractionTrees = array(
                        "updated_by" => $session['user_id'],
                        "is_active" => 0,
                    );
                    $deleteExtractionTrees = $this->Forestry_model->update_extraction_trees($dataDeleteExtractionTrees, $extractionId, $treeNo);

                    $dataDeleteExtractionTreeDetails = array(
                        "updated_by" => $session['user_id'],
                        "is_active" => 0,
                    );
                    $deleteExtractionTreeDetails = $this->Forestry_model->update_extraction_tree_details($dataDeleteExtractionTreeDetails, $extractionId, $treeId);

                    $dataExtractionTrees = array(
                        "extraction_id" => $extractionId,
                        "tree_no" => $treeNo,
                        "total_pieces" => $totalPieces,
                        "total_volume" => $totalVolume,
                        "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'],
                        "is_active" => 1,
                    );

                    $insertForestryExtractionTrees = $this->Forestry_model->add_forestry_extraction_trees($dataExtractionTrees);

                    if ($insertForestryExtractionTrees > 0) {

                        $extractionDataJson = json_decode($extractionData, true);

                        if (count($extractionDataJson) > 0) {

                            foreach ($extractionDataJson as $extractdata) {
                                $dataExportTreeDetails = array(
                                    "extraction_id" => $extractionId,
                                    "extraction_tree_id" => $insertForestryExtractionTrees,
                                    "log_no" => $extractdata["log_number"] + 0,
                                    "circumference" => $extractdata["circumference"] + 0,
                                    "length" => $extractdata["length"] + 0,
                                    "volume" => $extractdata["volume"] + 0,
                                    "created_by" => $session['user_id'],
                                    "updated_by" => $session['user_id'],
                                    "is_active" => 1
                                );

                                $insertExtractTreesDetails = $this->Forestry_model->add_forestry_extraction_tree_details($dataExportTreeDetails);
                            }
                        }

                        $dataExtractionTrees = array(
                            "extraction_id" => $extractionId,
                            "tree_no" => $treeNo,
                            "total_pieces" => $totalPieces,
                            "total_volume" => $totalVolume,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'],
                            "is_active" => 1,
                        );

                        $Return['result'] = $this->lang->line('data_updated');
                        $Return['error'] = "";
                        $this->output($Return);
                        exit;
                    } else {
                        $Return['result'] = "";
                        $Return['error'] = $this->lang->line('error_adding');
                        $this->output($Return);
                        exit;
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
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }

    public function extraction_trees_list()
    {
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }

        $extractionId = (int)$this->input->get('extractionId');

        $trees = $this->Forestry_model->get_extractions_trees_summary($extractionId);

        $data = [];
        foreach ($trees as $row) {

            $action = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editextractiontree" data-toggle="modal" data-extraction_tree_id="' . $row->id . '"><span class="fas fa-pencil"></span></button></span>
            <span style="margin-left:5px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deleteextractiontree" data-toggle="modal" data-extraction_tree_id="' . $row->id . '"><span class="fas fa-trash"></span></button></span>';

            $data[] = [
                $action,
                $row->tree_no,
                $row->total_pieces,
                number_format($row->total_volume, 3)
            ];
        }

        echo json_encode(["data" => $data]);
        exit;
    }

    public function fetch_extraction_tree_details()
    {
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }

        $treeId = (int)$this->input->get('treeId');
        $extractionId = (int)$this->input->get('extractionId');

        $details = $this->Forestry_model->get_extraction_tree_details_by_treeid($treeId, $extractionId);

        echo json_encode([
            'result' => $details
        ]);
        exit;
    }

    public function get_used_tree_numbers()
    {
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }

        $extractionId = (int)$this->input->get('extractionId');

        $usedTrees = $this->Forestry_model->get_used_tree_numbers($extractionId);

        // Convert to simple array [1,2,3]
        $treeNos = array_map(function ($row) {
            return (int)$row->tree_no;
        }, $usedTrees);

        echo json_encode($treeNos);
        exit;
    }

    public function get_extraction_totals()
    {
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }

        $extractionId = (int)$this->input->get('extractionId');
        $extractionDetails = $this->Forestry_model->get_extractions_details_byid($extractionId);
        $totals = $this->Forestry_model->get_extraction_totals($extractionId);

        echo json_encode([
            'total_cost' => $totals->total_trees * $extractionDetails[0]->extraction_cost + 0,
            'total_trees'  => (int)$totals->total_trees,
            'total_pieces' => (int)$totals->total_pieces,
            'total_volume' => number_format((float)$totals->total_volume, 3)
        ]);
        exit;
    }

    public function download_extraction_report()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }

        $originId = (int)$this->input->post('originId');
        $supplierId = (int)$this->input->post('supplierId');
        $contractId = (int)$this->input->post('farmId');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');

        // Generate report
        $extractionReports = $this->Forestry_model->generate_extraction_report($originId, $supplierId, $contractId, $fromDate, $toDate);

        if (count($extractionReports) == 0) {
            $Return['error'] = $this->lang->line('no_data_available');
            $this->output($Return);
        } else {

            $this->excel->setActiveSheetIndex(0);
            $objSheet = $this->excel->getActiveSheet();
            $objSheet->setTitle($this->lang->line('extraction') . " - " . $this->lang->line('report'));
            $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $objSheet->SetCellValue('A4', strtoupper($this->lang->line('FECHA')));
            $objSheet->SetCellValue('B4', strtoupper($this->lang->line('supplier_name')));
            $objSheet->SetCellValue('C4', strtoupper($this->lang->line('purchase_contract')));
            $objSheet->SetCellValue('D4', strtoupper($this->lang->line('tree_no')));
            $objSheet->SetCellValue('E4', strtoupper($this->lang->line('total_no_of_pieces')));
            $objSheet->SetCellValue('F4', strtoupper($this->lang->line('total_volume')));
            $objSheet->SetCellValue('G4', strtoupper($this->lang->line('extraction_cost')));

            $objSheet->getStyle("A4:G4")->getFont()->setBold(true);
            $objSheet->getStyle("A4:G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A4:G4')->applyFromArray($styleArray);

            $objSheet->mergeCells("H3:J3");
            $objSheet->SetCellValue('H3', "1");
            $objSheet->getStyle("H3:J4")->getFont()->setBold(true);
            $objSheet->getStyle("H3:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('H4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('I4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('J4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("K3:M3");
            $objSheet->SetCellValue('K3', "2");
            $objSheet->getStyle("K3:M4")->getFont()->setBold(true);
            $objSheet->getStyle("K3:M4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('K4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('L4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('M4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("N3:P3");
            $objSheet->SetCellValue('N3', "3");
            $objSheet->getStyle("N3:P4")->getFont()->setBold(true);
            $objSheet->getStyle("N3:P4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('N4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('O4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('P4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("Q3:S3");
            $objSheet->SetCellValue('Q3', "4");
            $objSheet->getStyle("Q3:S4")->getFont()->setBold(true);
            $objSheet->getStyle("Q3:S4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('Q4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('R4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('S4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("T3:V3");
            $objSheet->SetCellValue('T3', "5");
            $objSheet->getStyle("T3:V4")->getFont()->setBold(true);
            $objSheet->getStyle("T3:V4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('T4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('U4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('V4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("W3:Y3");
            $objSheet->SetCellValue('W3', "6");
            $objSheet->getStyle("W3:Y4")->getFont()->setBold(true);
            $objSheet->getStyle("W3:Y4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('W4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('X4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('Y4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("Z3:AB3");
            $objSheet->SetCellValue('Z3', "7");
            $objSheet->getStyle("Z3:AB4")->getFont()->setBold(true);
            $objSheet->getStyle("Z3:AB4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('Z4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('AA4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('AB4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("AC3:AE3");
            $objSheet->SetCellValue('AC3', "8");
            $objSheet->getStyle("AC3:AE4")->getFont()->setBold(true);
            $objSheet->getStyle("AC3:AE4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('AC4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('AD4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('AE4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("AF3:AH3");
            $objSheet->SetCellValue('AF3', "9");
            $objSheet->getStyle("AF3:AH4")->getFont()->setBold(true);
            $objSheet->getStyle("AF3:AH4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('AF4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('AG4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('AH4', strtoupper($this->lang->line('text_volume')));

            $objSheet->mergeCells("AI3:AK3");
            $objSheet->SetCellValue('AI3', "10");
            $objSheet->getStyle("AI3:AK4")->getFont()->setBold(true);
            $objSheet->getStyle("AI3:AK4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->SetCellValue('AI4', strtoupper($this->lang->line('circumference')));
            $objSheet->SetCellValue('AJ4', strtoupper($this->lang->line('length')));
            $objSheet->SetCellValue('AK4', strtoupper($this->lang->line('text_volume')));

            $objSheet->getStyle('H3:AK4')->applyFromArray($styleArray);

            $row = 5;
            foreach ($extractionReports as $rdata) {
                $dateObj = DateTime::createFromFormat('d/m/Y', trim($rdata->date));                

                if ($dateObj !== false) {
                    $dateObj->setTime(0, 0, 0);

                    // FLOOR removes any decimal time fraction
                    $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                    $objSheet->setCellValue('A' . $row, $excelDate);
                }

                $objSheet->setCellValue('B' . $row, $rdata->supplier_name);
                $objSheet->setCellValue('C' . $row, $rdata->contract_code . ' - ' . $rdata->description);
                $objSheet->setCellValue('D' . $row, $rdata->tree_no);

                $formulaPieces =
                    "=COUNTIFS(H$row,\">0\",I$row,\">0\")+COUNTIFS(K$row,\">0\",L$row,\">0\")+COUNTIFS(N$row,\">0\",O$row,\">0\")+COUNTIFS(Q$row,\">0\",R$row,\">0\")+COUNTIFS(T$row,\">0\",U$row,\">0\")+COUNTIFS(W$row,\">0\",X$row,\">0\")+COUNTIFS(Z$row,\">0\",AA$row,\">0\")+COUNTIFS(AC$row,\">0\",AD$row,\">0\")+COUNTIFS(AF$row,\">0\",AG$row,\">0\")+COUNTIFS(AI$row,\">0\",AJ$row,\">0\")";

                $objSheet->setCellValue('E' . $row, $formulaPieces);
                $objSheet->setCellValue('F' . $row, "=SUM(J$row,M$row,P$row,S$row,V$row,Y$row,AB$row,AE$row,AH$row,AK$row)");
                $objSheet->setCellValue('G' . $row, $rdata->extraction_cost);

                $objSheet->setCellValue('H' . $row, $rdata->circ1);
                $objSheet->setCellValue('I' . $row, $rdata->len1);
                if($rdata->circ1 > 0 && $rdata->len1 > 0){
                    $objSheet->setCellValue('J' . $row, "=TRUNC(H$row*H$row*I$row/16000000,3)");
                    $objSheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('J' . $row, "");
                }

                $objSheet->setCellValue('K' . $row, $rdata->circ2);
                $objSheet->setCellValue('L' . $row, $rdata->len2);
                if($rdata->circ2 > 0 && $rdata->len2 > 0){
                    $objSheet->setCellValue('M' . $row, "=TRUNC(K$row*K$row*L$row/16000000,3)");
                    $objSheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('M' . $row, "");
                }

                $objSheet->setCellValue('N' . $row, $rdata->circ3);
                $objSheet->setCellValue('O' . $row, $rdata->len3);
                if($rdata->circ3 > 0 && $rdata->len3 > 0){
                    $objSheet->setCellValue('P' . $row, "=TRUNC(N$row*N$row*O$row/16000000,3)");
                    $objSheet->getStyle('P' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('P' . $row, "");
                }

                $objSheet->setCellValue('Q' . $row, $rdata->circ4);
                $objSheet->setCellValue('R' . $row, $rdata->len4);
                if($rdata->circ4 > 0 && $rdata->len4 > 0){
                    $objSheet->setCellValue('S' . $row, "=TRUNC(P$row*P$row*R$row/16000000,3)");
                    $objSheet->getStyle('S' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('S' . $row, "");
                }

                $objSheet->setCellValue('T' . $row, $rdata->circ5);
                $objSheet->setCellValue('U' . $row, $rdata->len5);
                if($rdata->circ5 > 0 && $rdata->len5 > 0){
                    $objSheet->setCellValue('V' . $row, "=TRUNC(T$row*T$row*U$row/16000000,3)");
                    $objSheet->getStyle('V' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('V' . $row, "");
                }

                $objSheet->setCellValue('W' . $row, $rdata->circ6);
                $objSheet->setCellValue('X' . $row, $rdata->len6);
                if($rdata->circ6 > 0 && $rdata->len6 > 0){
                    $objSheet->setCellValue('Y' . $row, "=TRUNC(W$row*W$row*X$row/16000000,3)");
                    $objSheet->getStyle('Y' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('Y' . $row, "");
                }

                $objSheet->setCellValue('Z' . $row, $rdata->circ7);
                $objSheet->setCellValue('AA' . $row, $rdata->len7);
                if($rdata->circ7 > 0 && $rdata->len7 > 0){
                    $objSheet->setCellValue('AB' . $row, "=TRUNC(Z$row*Z$row*AA$row/16000000,3)");
                    $objSheet->getStyle('AB' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('AB' . $row, "");
                }

                $objSheet->setCellValue('AC' . $row, $rdata->circ8);
                $objSheet->setCellValue('AD' . $row, $rdata->len8);
                if($rdata->circ8 > 0 && $rdata->len8 > 0){
                    $objSheet->setCellValue('AE' . $row, "=TRUNC(AC$row*AC$row*AD$row/16000000,3)");
                    $objSheet->getStyle('AE' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('AE' . $row, "");
                }

                $objSheet->setCellValue('AF' . $row, $rdata->circ9);
                $objSheet->setCellValue('AG' . $row, $rdata->len9);
                if($rdata->circ9 > 0 && $rdata->len9 > 0){
                    $objSheet->setCellValue('AH' . $row, "=TRUNC(AF$row*AF$row*AG$row/16000000,3)");
                    $objSheet->getStyle('AH' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('AH' . $row, "");
                }

                $objSheet->setCellValue('AI' . $row, $rdata->circ10);
                $objSheet->setCellValue('AJ' . $row, $rdata->len10);
                if($rdata->circ10 > 0 && $rdata->len10 > 0){
                    $objSheet->setCellValue('AK' . $row, "=TRUNC(AI$row*AI$row*AJ$row/16000000,3)");
                    $objSheet->getStyle('AK' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                } else {
                    $objSheet->setCellValue('AK' . $row, "");
                }

                $row++;
            }

            $lastRow = $row - 1;

            $objSheet->setCellValue('D3', "=SUBTOTAL(2,D5:D$lastRow)");
            $objSheet->getStyle("D3")->getNumberFormat()->setFormatCode('0');

            $objSheet->setCellValue('E3', "=SUBTOTAL(9,E5:E$lastRow)");
            $objSheet->getStyle("E3")->getNumberFormat()->setFormatCode('0');

            $objSheet->setCellValue('F3', "=SUBTOTAL(9,F5:F$lastRow)");
            $objSheet->getStyle("F3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

            $objSheet->setCellValue('G3', "=SUBTOTAL(9,G5:G$lastRow)");
            $objSheet->getStyle("G3")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');
            $objSheet->getStyle('D3:G3')->applyFromArray($styleArray);

            $objSheet->getStyle("E5:E$lastRow")->getNumberFormat()->setFormatCode('0');
            $objSheet->getStyle("F5:F$lastRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
            $objSheet->getStyle("G5:G$lastRow")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');
            $objSheet->getStyle("A5:A$lastRow")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $objSheet->getStyle('A5:AK' . $lastRow)->applyFromArray($styleArray);

            $objSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(40);
            $objSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(40);
            $objSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(10);
            $objSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(18);
            $objSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(18);
            $objSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('I')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('K')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('L')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('M')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('N')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('O')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('P')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('Q')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('R')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('S')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('T')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('U')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('V')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('W')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('X')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('Y')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('Z')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AA')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AB')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('AC')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AD')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AE')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('AF')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AG')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AH')->setAutoSize(false)->setWidth(20);
            $objSheet->getColumnDimension('AI')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AJ')->setAutoSize(false)->setWidth(15);
            $objSheet->getColumnDimension('AK')->setAutoSize(false)->setWidth(20);

            unset($styleArray);
            $six_digit_random_number = mt_rand(100000, 999999);
            $month_name = ucfirst(date("dmY"));

            $filename =  'ForestryReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
            $objWriter->save('./reports/ForestryReports/' . $filename);
            $objWriter->setPreCalculateFormulas(true);
            $Return['error'] = '';
            $Return['result'] = site_url() . 'reports/ForestryReports/' . $filename;
            $Return['successmessage'] = $this->lang->line('report_downloaded');
            if ($Return['result'] != '') {
                $this->output($Return);
            }
        }
    }
}
