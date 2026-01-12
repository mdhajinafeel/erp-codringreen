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
}
