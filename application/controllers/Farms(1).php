<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Farms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_model");
        $this->load->model("Farm_model");
        $this->load->model("Master_model");
        $this->load->model("Reception_model");
        $this->load->model("Settings_model");
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
        $data['title'] = $this->lang->line('farm_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        $role_resources_ids = explode(',', $session["role_id"]);

        // if (empty($session)) {
        //     redirect("/logout");
        // }

        // if (in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids)) {
        //     redirect("/errorpage");
        // } else {
        $data['path_url'] = 'cgr_farms';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("farms/farm_list", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
        //}
    }

    public function farm_list()
    {
        $data['title'] =  $this->lang->line('farm_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            $this->load->view("farms/farm_list", $data);
        } else {
            redirect("/logout");
        }

        $draw = intval($this->input->get("draw"));
        $originid = intval($this->input->get("originid"));

        if ($originid == 0) {
            $farms = $this->Farm_model->all_farms();
        } else {
            $farms = $this->Farm_model->all_farms_origin($originid);
        }

        $data = array();

        foreach ($farms as $r) {
            $editFarm = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_excel') . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloadfarm" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-created_from="' . $r->created_from . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-download"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_receipt') . '"><button type="button" class="btn icon-btn btn-xs btn-downloadreceipt waves-effect waves-light" data-role="downloadreceipt" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-created_from="' . $r->created_from . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-receipt"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewfarm" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-eye"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('delete') . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletefarm" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-trash"></span></button></span>';

            $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

            $data[] = array(
                $editFarm,
                $r->inventory_order,
                $r->supplier_name,
                $r->contract_code,
                $product,
                $r->purchase_date,
                ($r->total_volume + 0),
                $r->origin,
                ucwords(strtolower($r->uploaded_by)),
            );
        }

        $output = array(
            "draw" => $draw,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function dialog_farm_add()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {
            if ($this->input->get('type') == "addfarm") {
                $data = array(
                    'pageheading' => $this->lang->line('add_farm'),
                    'pagetype' => "add",
                    'farmid' => 0,
                    'inventory_order' => "",
                );
            }
            $this->load->view('farms/dialog_add_farm', $data);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function dialog_farm_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "downloadfarm") {

                $farmId = $this->input->get('fid');
                $contractId = $this->input->get('cid');
                $inventoryOrder = $this->input->get('io');
                $createdFrom = $this->input->get('cf');

                if ($createdFrom == 2) {
                    $this->generate_farm_report_field_purchase($farmId, $contractId, $inventoryOrder);
                } else {
                    $this->generate_farm_report($farmId, $contractId, $inventoryOrder);
                }
            } else if ($this->input->get('type') == "downloadreceipt") {

                $farmId = $this->input->get('fid');
                $contractId = $this->input->get('cid');
                $inventoryOrder = $this->input->get('io');
                $createdFrom = $this->input->get('cf');

                if ($createdFrom == 2) {
                    $this->generate_supplier_receipt($farmId, $contractId, $inventoryOrder);
                } else {
                    $this->generate_supplier_receipt($farmId, $contractId, $inventoryOrder);
                }
            } else if ($this->input->get('type') == "viewfarm") {
                $farmId = $this->input->get('fid');
                $contractId = $this->input->get('cid');
                $inventoryOrder = $this->input->get('io');

                $getFarmDetails = $this->Farm_model->get_farm_details($farmId, $contractId, $inventoryOrder);

                $data = array(
                    'pageheading' => $this->lang->line('farm_details'),
                    'pagetype' => 'update',
                    'farmid' => $farmId,
                    'contractid' => $contractId,
                    'inventoryorder' => $inventoryOrder,
                    'farm_details' => $getFarmDetails,
                    'farm_submit' => 'farms/add'
                );
                $this->load->view('farms/dialog_view_farms', $data);
            } else if ($this->input->get('type') == "deletefarmconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('fid'),
                    'inputid1' => $this->input->get('cid'),
                    'inputid2' => $this->input->get('io'),
                    'actionurl' => "farms/dialog_farm_action",
                    'actiontype' => "deletefarm",
                    'xin_table' => "#xin_table_farms",
                );
                $this->load->view('dialogs/dialog_confirmation', $data);
            } else if ($this->input->get('type') == "deletefarm") {

                $farmId = $this->input->get('inputid');
                $contractId = $this->input->get('inputid1');
                $inventoryOrder = $this->input->get('inputid2');


                $farmDelete = $this->Farm_model->delete_farm($farmId, $inventoryOrder, $contractId, $session['user_id']);

                if ($farmDelete) {
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

    public function get_companysetting_by_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = 0;
            if ($this->input->get('originid') > 0) {
                $getCompanySettings = $this->Master_model->get_company_settings_by_origin($this->input->get('originid'));
                $result = $getCompanySettings[0]->mandatory_reception_creation;
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

    public function get_product_by_supplier_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    $getProducts = $this->Contract_model->get_supplier_product_byorigin($this->input->get('originid'), $this->input->get('supplierid'));
                    foreach ($getProducts as $products) {
                        $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
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

    public function get_product_type_by_supplier()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    if ($this->input->get('productid') > 0) {
                        $getProductTypes = $this->Contract_model->get_supplier_product_type_byorigin($this->input->get('supplierid'), $this->input->get('productid'));
                        foreach ($getProductTypes as $producttype) {
                            $productTypeName = $this->lang->line($producttype->product_type_name);
                            $result = $result . "<option value='" . $producttype->type_id . "'>" . $productTypeName . "</option>";
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

    public function get_contracts_by_supplier()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    if ($this->input->get('productid') > 0) {
                        if ($this->input->get('producttypeid') > 0) {
                            $getContracts = $this->Farm_model->get_contracts_for_farm(
                                $this->input->get('originid'),
                                $this->input->get('supplierid'),
                                $this->input->get('productid'),
                                $this->input->get('producttypeid')
                            );
                            foreach ($getContracts as $contract) {
                                $result = $result . "<option value='" . $contract->contract_id . "'>" . $contract->contract_code . "</option>";
                            }
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

            if (
                $this->input->get('originid') > 0 && $this->input->get('supplierid') > 0 &&
                $this->input->get('productid') > 0 && $this->input->get('producttypeid') > 0 &&
                $this->input->get('contractid') > 0
            ) {
                $getContractDetails = $this->Farm_model->fetch_contract_details_for_farm(
                    $this->input->get('originid'),
                    $this->input->get('contractid'),
                    $this->input->get('supplierid'),
                    $this->input->get('productid'),
                    $this->input->get('producttypeid')
                );

                if (count($getContractDetails) == 1) {
                    $data = array(
                        'purchaseunit' => $this->lang->line($getContractDetails[0]->purchase_unit),
                        'remainingvolume' => sprintf('%0.3f', ($getContractDetails[0]->remaining_volume + 0)),
                        'currencycode' => $getContractDetails[0]->currency_code,
                        'currency' => $getContractDetails[0]->currency,
                        'unit_of_purchase' => $getContractDetails[0]->unit_of_purchase,
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

    public function fetch_providers()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {
                    $getProviders = $this->Farm_model->fetch_payto_providers($this->input->get('originid'), $this->input->get('supplierid'));
                    foreach ($getProviders as $provider) {
                        $result = $result . "<option value='" . $provider->supplier_id . "'>" . $provider->supplier_name . "</option>";
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

    public function get_warehouse_by_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {
            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getWH = $this->Master_model->get_warehouse_by_origin($this->input->get('originid'));
                foreach ($getWH as $warehouse) {
                    $result = $result . "<option value='" . $warehouse->whid . "'>" . $warehouse->warehouse_name . "</option>";
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

    public function get_measurement_system()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('producttypeid') > 0) {

                $getMeasurementSystem = $this->Reception_model->get_measurement_system($this->input->get('producttypeid'), $this->input->get('originid'));
                foreach ($getMeasurementSystem as $measurementsystem) {
                    $measurmentSystem = $this->lang->line($measurementsystem->measurement_name);
                    $result = $result . "<option value='" . $measurementsystem->measurement_id . "'>" . $measurmentSystem . "</option>";
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

    public function add()
    {
        $Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if ($this->input->post('add_type') == 'farm') {

            if (!empty($session)) {

                if ($this->input->post('action_type') == 'add') {

                    $originid = $this->input->post('originid');
                    $supplierid = $this->input->post('supplierid');
                    $productid = $this->input->post('productid');
                    $producttypeid = $this->input->post('producttypeid');
                    $purchasecontractid = $this->input->post('purchasecontractid');
                    $inventoryorder = strtoupper($this->input->post('inventoryorder'));
                    $truckplatenumber = strtoupper($this->input->post('truckplatenumber'));
                    $servicecost = $this->input->post('servicecost');
                    $servicepayto = $this->input->post('servicepayto');
                    $logisticcost = $this->input->post('logisticcost');
                    $logisticpayto = $this->input->post('logisticpayto');
                    $farmadjustment = $this->input->post('farmadjustment');
                    $conversionrate = $this->input->post('conversionrate');
                    $adjustrf = $this->input->post('adjustrf');
                    $warehouseid = $this->input->post('warehouseid');
                    $receptiondate = $this->input->post('receptiondate');
                    $purchaseunit = $this->input->post('purchaseunit');
                    $farmdata = $this->input->post('farmdata');

                    $warehouseid_rounglogs = $this->input->post('warehouseid_rounglogs');
                    $measurement_system_roundlogs = $this->input->post('measurement_system_roundlogs');
                    $mandatoryreception = $this->input->post('mandatoryreception');

                    $getInventoryOrderCount = $this->Farm_model->get_inventory_order_count($inventoryorder, $originid);

                    if ($getInventoryOrderCount[0]->cnt == 0) {

                        if ($servicecost == null || $servicecost == "") {
                            $servicecost = 0;
                        }

                        if ($logisticcost == null || $logisticcost == "") {
                            $logisticcost = 0;
                        }

                        date_default_timezone_set($session['default_timezone']);
                        $purchase_date = date('Y-m-d', time());
                        $receptiondate_roundlogs = date('d/m/Y', time());

                        $farmdataJson = json_decode($farmdata, true);

                        $totalVolume = 0;
                        $woodValue = 0;
                        $woodValueWithSupplierTaxes = 0;
                        $logisticsCostWithTaxes = 0;
                        $servicesCostWithTaxes = 0;
                        $totalValueWithTaxes = 0;
                        $totalValue = 0;

                        $supplierTaxesArr = array();
                        $providerLogisticTaxesArr = array();
                        $providerServiceTaxesArr = array();
                        $supplierLogisticTaxesArr = array();
                        $supplierServiceTaxesArr = array();
                        $supplierTaxesAdjustArr = array();
                        $providerLogisticTaxesAdjustArr = array();
                        $providerServiceTaxesAdjustArr = array();

                        if ($producttypeid == 1 || $producttypeid == 3) {

                            $getPriceRanges = $this->Farm_model->get_price_for_circumference(-1, $purchasecontractid);

                            foreach ($farmdataJson as $farm) {

                                $noOfPieces = $farm["noOfPieces"];
                                $volumePie = $farm["volumePie"];
                                $grade = $farm["grade"];
                                $netVolume = $farm["netVolume"];
                                $totalVolume = $totalVolume + $farm["netVolume"];

                                if ($noOfPieces > 0) {
                                    if (count($getPriceRanges) == 1) {
                                        if ($purchaseunit == 1) {
                                            if ($grade == 1) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->minrange_grade1 * $volumePie));
                                            } else if ($grade == 2) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->maxrange_grade2 * $volumePie));
                                            } else if ($grade == 3) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $volumePie));
                                            }
                                        } else {
                                            if ($grade == 1) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->minrange_grade1 * $netVolume));
                                            } else if ($grade == 2) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->maxrange_grade2 * $netVolume));
                                            } else if ($grade == 3) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $netVolume));
                                            }
                                        }
                                    }
                                }
                            }

                            if ($conversionrate > 0) {
                                $woodValue = $woodValue * $conversionrate;
                            }

                            $woodValue = sprintf('%0.3f', ($woodValue + 0));

                            // WOOD VALUE WITH TAXES
                            $supplierIvaValue = 0;
                            $supplierRetenctionValue = 0;
                            $supplierReticaValue = 0;

                            $getSupplierTaxes = $this->Farm_model->get_supplier_taxes($supplierid);

                            if (count($getSupplierTaxes) == 1) {

                                if ($getSupplierTaxes[0]->is_iva_enabled == 1) {
                                    $supplierIvaValue =  $woodValue * ($getSupplierTaxes[0]->iva_value / 100);
                                }

                                if ($getSupplierTaxes[0]->is_retencion_enabled == 1) {
                                    $supplierRetenctionValue = $woodValue * ($getSupplierTaxes[0]->retencion_value / 100);
                                }

                                if ($getSupplierTaxes[0]->is_reteica_enabled == 1) {
                                    $supplierReticaValue = $woodValue * ($getSupplierTaxes[0]->reteica_value);
                                }
                            }

                            $woodValueWithSupplierTaxes = $woodValue + ($supplierIvaValue + $supplierRetenctionValue + $supplierReticaValue);

                            // END WOOD VALUE WITH TAXES

                            // LOGISTICS WITH TAXES

                            if ($logisticcost != 0 && $logisticpayto > 0) {

                                $transportorIvaValue_Logistics = 0;
                                $transportorRetenctionValue_Logistics = 0;
                                $transportorReticaValue_Logistics = 0;

                                $getTransportorTaxes_Logistics = $this->Farm_model->get_transportor_taxes($logisticpayto);
                                $getTransportorTaxes_Logistics_Supplier = $this->Farm_model->get_supplier_taxes($logisticpayto);

                                if (count($getTransportorTaxes_Logistics) == 1) {

                                    if ($getTransportorTaxes_Logistics[0]->is_iva_provider_enabled == 1) {
                                        $transportorIvaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->iva_provider_value / 100);
                                    } else {
                                        if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_iva_enabled == 1) {
                                            $transportorIvaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics_Supplier[0]->iva_value / 100);
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_retencion_provider_enabled == 1) {
                                        $transportorRetenctionValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->retencion_provider_value / 100);
                                    } else {
                                        if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_retencion_enabled == 1) {
                                            $transportorRetenctionValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value / 100);
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_reteica_provider_enabled == 1) {
                                        $transportorReticaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->reteica_provider_value);
                                    } else {
                                        if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_reteica_enabled == 1) {
                                            $transportorReticaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value);
                                        }
                                    }
                                } else if ($logisticpayto == $supplierid) {
                                    $getTransportorTaxes_Logistics = $this->Farm_model->get_supplier_taxes($logisticpayto);

                                    if (count($getTransportorTaxes_Logistics) == 1) {
                                        if ($getTransportorTaxes_Logistics[0]->is_iva_enabled == 1) {
                                            $transportorIvaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->iva_value / 100);
                                        }

                                        if ($getTransportorTaxes_Logistics[0]->is_retencion_enabled == 1) {
                                            $transportorRetenctionValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->retencion_value / 100);
                                        }

                                        if ($getTransportorTaxes_Logistics[0]->is_reteica_enabled == 1) {
                                            $transportorReticaValue_Logistics = $logisticcost * ($getTransportorTaxes_Logistics[0]->reteica_value);
                                        }
                                    }
                                }
                            }

                            $logisticsCostWithTaxes = $logisticcost + ($transportorIvaValue_Logistics + $transportorRetenctionValue_Logistics + $transportorReticaValue_Logistics);

                            // END LOGISTICS WITH TAXES

                            // SERVICES WITH TAXES

                            if ($servicecost != 0 && $servicepayto > 0) {
                                $transportorIvaValue_Service = 0;
                                $transportorRetenctionValue_Service = 0;
                                $transportorReticaValue_Service = 0;

                                $getTransportorTaxes_Service = $this->Farm_model->get_transportor_taxes($servicepayto);
                                $getTransportorTaxes_Service_Supplier = $this->Farm_model->get_supplier_taxes($servicepayto);

                                if (count($getTransportorTaxes_Service) == 1) {

                                    if ($getTransportorTaxes_Service[0]->is_iva_provider_enabled == 1) {
                                        $transportorIvaValue_Service = $servicecost * ($getTransportorTaxes_Service[0]->iva_provider_value / 100);
                                    } else {
                                        if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_iva_enabled == 1) {
                                            $transportorIvaValue_Service = $servicecost * ($getTransportorTaxes_Service_Supplier[0]->iva_value / 100);
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_retencion_provider_enabled == 1) {
                                        $transportorRetenctionValue_Service = $servicecost * ($getTransportorTaxes_Service[0]->retencion_provider_value / 100);
                                    } else {
                                        if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_retencion_enabled == 1) {
                                            $transportorRetenctionValue_Service = $servicecost * ($getTransportorTaxes_Service_Supplier[0]->retencion_value / 100);
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_reteica_provider_enabled == 1) {
                                        $transportorReticaValue_Service = $servicecost * ($getTransportorTaxes_Service[0]->reteica_provider_value);
                                    } else {
                                        if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_reteica_enabled == 1) {
                                            $transportorReticaValue_Service = $servicecost * ($getTransportorTaxes_Service_Supplier[0]->reteica_value);
                                        }
                                    }
                                } else if ($servicepayto == $supplierid) {

                                    $getTransportorTaxes_Service = $this->Farm_model->get_supplier_taxes($servicepayto);

                                    if (count($getTransportorTaxes_Service) == 1) {
                                        if ($getTransportorTaxes_Service[0]->is_iva_enabled == 1) {
                                            $transportorIvaValue_Service = $logisticcost * ($getTransportorTaxes_Service[0]->iva_value / 100);
                                        }

                                        if ($getTransportorTaxes_Service[0]->is_retencion_enabled == 1) {
                                            $transportorRetenctionValue_Service = $logisticcost * ($getTransportorTaxes_Service[0]->retencion_value / 100);
                                        }

                                        if ($getTransportorTaxes_Service[0]->is_reteica_enabled == 1) {
                                            $transportorReticaValue_Service = $logisticcost * ($getTransportorTaxes_Service[0]->reteica_value);
                                        }
                                    }
                                }
                            }

                            $servicesCostWithTaxes = $servicecost + ($transportorIvaValue_Service + $transportorRetenctionValue_Service + $transportorReticaValue_Service);

                            // END SERVICES WITH TAXES

                            if (count($adjustrf) > 0 || $farmadjustment != 0) {
                                $totalValueWithTaxes = $woodValueWithSupplierTaxes + $logisticsCostWithTaxes + $servicesCostWithTaxes - ($supplierRetenctionValue + $transportorRetenctionValue_Logistics + $transportorRetenctionValue_Service);
                            } else {
                                $totalValueWithTaxes = $woodValueWithSupplierTaxes + $logisticsCostWithTaxes + $servicesCostWithTaxes;
                            }

                            if ($farmadjustment != 0) {
                                $totalValueWithTaxes = $totalValueWithTaxes - $farmadjustment;
                            }

                            $totalValue = $woodValue + $logisticcost + $servicecost;

                            $dataFarm = array(
                                "supplier_id" => $supplierid, "contract_id" => $purchasecontractid,
                                "product_id" => $productid, "product_type_id" => $producttypeid,
                                "inventory_order" => $inventoryorder, "plate_number" => $truckplatenumber,
                                "purchase_date" => $purchase_date, "service_cost" => $servicecost,
                                "logistic_cost" => $logisticcost, "adjustment" => $farmadjustment,
                                "total_volume" => $totalVolume, "total_value" => $totalValue, "wood_value" => $woodValue,
                                "pay_service_to" => $servicepayto, "pay_logistics_to" => $logisticpayto,
                                "exchange_rate" => $conversionrate, "is_adjust_rf" => $adjustrf,
                                "created_by" => $session['user_id'], "updated_by" => $session['user_id'], "is_active" => 1,
                                "origin_id" => $originid,
                            );

                            $insertFarm = $this->Farm_model->add_farm($dataFarm);

                            if ($insertFarm > 0) {
                                $dataFarmData = array();
                                foreach ($farmdataJson as $farm) {

                                    $noOfPieces = $farm["noOfPieces"];
                                    $length = $farm["length"];
                                    $width = $farm["width"];
                                    $thickness = $farm["thickness"];
                                    $lengthExport = $farm["lengthExport"];
                                    $widthExport = $farm["widthExport"];
                                    $thicknessExport = $farm["thicknessExport"];
                                    $volumePie = $farm["volumePie"];
                                    $grossVolume = $farm["grossVolume"];
                                    $grade = $farm["grade"];
                                    $netVolume = $farm["netVolume"];
                                    $scannedCode = $farm["scannedCode"];

                                    if ($noOfPieces > 0) {
                                        $dataFarmData[] = array(
                                            "farm_id" => $insertFarm, "scanned_code" => $scannedCode,
                                            "no_of_pieces" => $noOfPieces, "circumference" => 0,
                                            "length" => $length, "width" => $width, "thickness" => $thickness, "volume" => $netVolume,
                                            "volume_pie" => $volumePie, "grade_id" => $grade, "length_export" => $lengthExport, "width_export" => $widthExport,
                                            "thickness_export" => $thicknessExport, "volume_bought" => $grossVolume, "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1,
                                            "created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
                                        );
                                    }
                                }

                                if (count($dataFarmData) > 0) {
                                    $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                    if ($insertFarmData) {
                                        //SUPPLIER PRICE
                                        $this->Farm_model->add_supplier_price(
                                            $purchasecontractid,
                                            $supplierid,
                                            $inventoryorder,
                                            $session['user_id']
                                        );

                                        //CONTRACT INVENTORY MAPPING
                                        $dataContractMapping = array(
                                            "contract_id" => $purchasecontractid, "supplier_id" => $supplierid,
                                            "inventory_order" => $inventoryorder, "total_volume" => $totalVolume,
                                            "invoice_number" => "", "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1,
                                        );

                                        $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                        $dataInventoryLedger = array(
                                            "contract_id" => $purchasecontractid,
                                            "inventory_order" => $inventoryorder, "ledger_type" => 2,
                                            "expense_date" => $purchase_date, "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1, "is_advance_app" => 0,
                                        );

                                        if ($woodValueWithSupplierTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierid);
                                        }

                                        if ($logisticsCostWithTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $logisticsCostWithTaxes, 2, $logisticpayto);
                                        }

                                        if ($servicesCostWithTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $servicesCostWithTaxes, 3, $servicepayto);
                                        }

                                        if ($farmadjustment != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $farmadjustment, 4, $supplierid);
                                        }

                                        $getContracts = $this->Contract_model->get_contracts_by_contractid($purchasecontractid);
                                        if (count($getContracts) == 1) {
                                            $remainingVolume = $getContracts[0]->remaining_volume - $totalVolume;

                                            $dataRemainingVolume = array(
                                                "remaining_volume" => $remainingVolume,
                                            );

                                            $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchasecontractid, $supplierid);
                                        }

                                        //END

                                        //CREATE RECEPTION

                                        $getSupplierDetails = $this->Master_model->get_supplier_detail_reception($supplierid, $productid);

                                        if (count($getSupplierDetails) == 1) {
                                            $dataReception = array(
                                                "warehouse_id" => $warehouseid, "supplier_id" => $supplierid,
                                                "supplier_code" => $getSupplierDetails[0]->supplier_code, "supplier_product_id" => $getSupplierDetails[0]->product_name,
                                                "supplier_product_typeid" => $getSupplierDetails[0]->product_type,  "measurementsystem_id" => 1,
                                                "received_date" => $receptiondate, "salvoconducto" => $inventoryorder,
                                                "createdby" => $session['user_id'], "updatedby" => $session['user_id'],
                                                "isactive" => 1, "isclosed" => 1, "closedby" => $session['user_id'],
                                                "captured_timestamp" => 0, "isduplicatecaptured" => 0, "is_contract_added" => 0,
                                                "is_special_uploaded" => 0, "origin_id" => $originid,
                                            );

                                            $insertReception = $this->Reception_model->add_reception($dataReception);

                                            if ($insertReception > 0) {
                                                $this->Reception_model->add_reception_data_from_farm($insertReception, $inventoryorder, $purchaseunit, $session['user_id']);
                                            }
                                        }

                                        //END RECEPTION
                                    }

                                    $Return['result'] = $this->lang->line('data_added');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return['error'] = $this->lang->line('error_adding');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['error'] = $this->lang->line('error_adding');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }

                            // $Return['result'] = $this->lang->line('data_added');
                            // $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            // $this->output($Return);
                            // exit;
                        } else if ($producttypeid == 2 || $producttypeid == 4) {

                            $cProduct = 0;
                            $totalPiecesFarm = 0;
                            $totalVolumeFarm = 0;
                            $totalGrossVolumeFarm = 0;
                            foreach ($farmdataJson as $farm) {

                                $circumference = $farm["circumference"];
                                $totalVolume = $totalVolume + $farm["netVolume"];
                                $noOfPieces = $farm["noOfPieces"];

                                if ($purchaseunit == 3 || $purchaseunit == 4 || $purchaseunit == 5) {


                                    if ($noOfPieces > 0) {
                                        $getPriceRanges = $this->Farm_model->get_price_for_circumference($circumference, $purchasecontractid);

                                        if (count($getPriceRanges) == 1) {
                                            if ($purchaseunit == 3) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $noOfPieces));
                                            } else if ($purchaseunit == 4 || $purchaseunit == 5) {
                                                $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $farm["netVolume"]));
                                            }
                                        }
                                    }
                                } else if ($purchaseunit == 6 || $purchaseunit == 7) {

                                    $cProduct = $cProduct + ($circumference * $noOfPieces);
                                    $totalPiecesFarm = $totalPiecesFarm + $noOfPieces;
                                    $totalVolumeFarm = $totalVolumeFarm + $farm["netVolume"];
                                } else if ($purchaseunit == 8 || $purchaseunit == 9) {

                                    $totalVolumeFarm = $totalVolumeFarm + $farm["netVolume"];
                                    $totalGrossVolumeFarm = $totalGrossVolumeFarm + $farm["grossVolume"];
                                    $totalPiecesFarm = $totalPiecesFarm + $noOfPieces;
                                }
                            }

                            if ($purchaseunit == 6 || $purchaseunit == 7) {

                                $averageGirth = $this->truncate($cProduct / $totalPiecesFarm, 0);
                                $getPriceRanges = $this->Farm_model->get_price_for_circumference($averageGirth, $purchasecontractid);

                                if (count($getPriceRanges) == 1) {
                                    $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $totalVolumeFarm));
                                }
                            }

                            if ($purchaseunit == 8 || $purchaseunit == 9) {

                                $cftValue = round($totalGrossVolumeFarm / $totalPiecesFarm * 35.315, 2);
                                $getPriceRanges = $this->Farm_model->get_price_for_circumference($cftValue, $purchasecontractid);

                                if (count($getPriceRanges) == 1) {
                                    $woodValue = $woodValue + (($getPriceRanges[0]->pricerange_grade3 * $totalVolumeFarm));
                                }
                            }

                            if ($conversionrate > 0) {
                                $woodValue = $woodValue * $conversionrate;
                            }

                            $woodValue = sprintf('%0.3f', ($woodValue + 0));

                            // WOOD VALUE WITH TAXES
                            $supplierIvaValue = 0;
                            $supplierRetenctionValue = 0;
                            $supplierReticaValue = 0;

                            $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierid);

                            $supplierTaxesValue = 0;
                            if (count($getSupplierTaxes) > 0) {

                                $supplierTaxesValue = 0;
                                foreach ($getSupplierTaxes as $suppliertax) {

                                    $calcValue = 0;
                                    $taxId = $suppliertax->tax_id;
                                    $taxValue = $suppliertax->tax_value;
                                    $taxFormat = $suppliertax->number_format;
                                    $taxType = $suppliertax->arithmetic_type;

                                    if ($taxValue > 0) {
                                        if ($taxType == 2) {
                                            $taxValue = $taxValue * -1;
                                        }
                                        if ($taxFormat == 2) {
                                            $calcValue = $woodValue * ($taxValue / 100);
                                        } else {
                                            $calcValue = $woodValue * ($taxValue);
                                        }
                                    }

                                    $supplierTaxesAdjustArr[] = array(
                                        "taxId" => $taxId,
                                        "taxValue" => $calcValue,
                                        "taxVal" => (abs($taxValue) + 0),
                                    );

                                    array_push($supplierTaxesArr, $taxId);

                                    $supplierTaxesValue = $supplierTaxesValue + $calcValue;
                                }
                            }

                            $woodValueWithSupplierTaxes = $woodValue + $supplierTaxesValue;

                            // END WOOD VALUE WITH TAXES

                            // LOGISTICS WITH TAXES

                            if ($logisticcost != 0 && $logisticpayto > 0) {

                                $transportorIvaValue_Logistics = 0;
                                $transportorRetenctionValue_Logistics = 0;
                                $transportorReticaValue_Logistics = 0;

                                $getTransportorTaxes_Logistics = $this->Master_model->get_provider_taxes($logisticpayto);
                                $getTransportorTaxes_Logistics_Supplier = $this->Master_model->get_supplier_taxes($logisticpayto);

                                $logisticTaxesValue = 0;

                                if (count($getTransportorTaxes_Logistics) > 0) {

                                    $logisticTaxesValue = 0;
                                    foreach ($getTransportorTaxes_Logistics as $logistictaxtransportor) {

                                        $calcValue_LTransportor = 0;
                                        $taxId_LTransportor = $logistictaxtransportor->tax_id;
                                        $taxValue_LTransportor = $logistictaxtransportor->tax_value;
                                        $taxFormat_LTransportor = $logistictaxtransportor->number_format;
                                        $taxType_LTransportor = $logistictaxtransportor->arithmetic_type;

                                        if ($taxValue_LTransportor > 0) {
                                            if ($taxType_LTransportor == 2) {
                                                $taxValue_LTransportor = $taxValue_LTransportor * -1;
                                            }
                                            if ($taxFormat_LTransportor == 2) {
                                                $calcValue_LTransportor = $logisticcost * ($taxValue_LTransportor / 100);
                                            } else {
                                                $calcValue_LTransportor = $logisticcost * ($taxValue_LTransportor);
                                            }
                                        }

                                        $logisticTaxesValue = $logisticTaxesValue + $calcValue_LTransportor;

                                        $providerLogisticTaxesAdjustArr[] = array(
                                            "taxId" => $taxId_LTransportor,
                                            "taxValue" => $calcValue_LTransportor,
                                            "taxVal" => (abs($taxValue_LTransportor) + 0),
                                        );

                                        array_push($providerLogisticTaxesArr, $taxId_LTransportor);
                                    }

                                    if ($logisticTaxesValue == 0) {
                                        foreach ($getTransportorTaxes_Logistics_Supplier as $logistictaxsupplier) {

                                            $calcValue_STransportor = 0;
                                            $taxId_STransportor = $logistictaxsupplier->tax_id;
                                            $taxValue_STransportor = $logistictaxsupplier->tax_value;
                                            $taxFormat_STransportor = $logistictaxsupplier->number_format;
                                            $taxType_STransportor = $logistictaxsupplier->arithmetic_type;

                                            if ($taxValue_STransportor > 0) {
                                                if ($taxType_STransportor == 2) {
                                                    $taxValue_STransportor = $taxValue_STransportor * -1;
                                                }
                                                if ($taxFormat_STransportor == 2) {
                                                    $calcValue_STransportor = $logisticcost * ($taxValue_STransportor / 100);
                                                } else {
                                                    $calcValue_STransportor = $logisticcost * ($taxValue_STransportor);
                                                }
                                            }

                                            $logisticTaxesValue = $logisticTaxesValue + $calcValue_STransportor;

                                            $providerLogisticTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_STransportor,
                                                "taxValue" => $calcValue_STransportor,
                                                "taxVal" => (abs($taxValue_STransportor) + 0),
                                            );

                                            array_push($supplierLogisticTaxesArr, $taxId_STransportor);
                                        }
                                    }
                                } else if ($logisticpayto == $supplierid) {

                                    $getTransportorTaxes_Logistics = $this->Master_model->get_supplier_taxes($logisticpayto);

                                    $logisticTaxesValue = 0;
                                    if (count($getTransportorTaxes_Logistics) > 0) {

                                        foreach ($getTransportorTaxes_Logistics as $transporttax) {

                                            $calcValue_logistic = 0;
                                            $taxId_logistic = $transporttax->tax_id;
                                            $taxValue_logistic = $transporttax->tax_value;
                                            $taxFormat_logistic = $transporttax->number_format;
                                            $taxType_logistic = $transporttax->arithmetic_type;

                                            if ($taxValue_logistic > 0) {
                                                if ($taxType_logistic == 2) {
                                                    $taxValue_logistic = $taxValue_logistic * -1;
                                                }
                                                if ($taxFormat_logistic == 2) {
                                                    $calcValue_logistic = $logisticcost * ($taxValue_logistic / 100);
                                                } else {
                                                    $calcValue_logistic = $logisticcost * ($taxValue_logistic);
                                                }
                                            }

                                            $logisticTaxesValue = $logisticTaxesValue + $calcValue_logistic;

                                            $providerLogisticTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_logistic,
                                                "taxValue" => $calcValue_logistic,
                                                "taxVal" => (abs($taxValue_logistic) + 0),
                                            );

                                            array_push($supplierLogisticTaxesArr, $taxId_logistic);
                                        }
                                    }
                                }
                            }

                            $logisticsCostWithTaxes = $logisticcost + $logisticTaxesValue;

                            // END LOGISTICS WITH TAXES

                            // SERVICES WITH TAXES

                            if ($servicecost != 0 && $servicepayto > 0) {
                                $transportorIvaValue_Service = 0;
                                $transportorRetenctionValue_Service = 0;
                                $transportorReticaValue_Service = 0;

                                $getTransportorTaxes_Service = $this->Master_model->get_provider_taxes($servicepayto);
                                $getTransportorTaxes_Service_Supplier = $this->Master_model->get_supplier_taxes($servicepayto);

                                $servicesTaxesValue = 0;

                                if (count($getTransportorTaxes_Service) > 0) {

                                    $servicesTaxesValue = 0;
                                    foreach ($getTransportorTaxes_Service as $servicetaxtransportor) {

                                        $calcValue_SerTransportor = 0;
                                        $taxId_SerTransportor = $servicetaxtransportor->tax_id;
                                        $taxValue_SerTransportor = $servicetaxtransportor->tax_value;
                                        $taxFormat_SerTransportor = $servicetaxtransportor->number_format;
                                        $taxType_SerTransportor = $servicetaxtransportor->arithmetic_type;

                                        if ($taxValue_SerTransportor > 0) {
                                            if ($taxType_SerTransportor == 2) {
                                                $taxValue_SerTransportor = $taxValue_SerTransportor * -1;
                                            }
                                            if ($taxFormat_SerTransportor == 2) {
                                                $calcValue_SerTransportor = $servicecost * ($taxValue_SerTransportor / 100);
                                            } else {
                                                $calcValue_SerTransportor = $servicecost * ($taxValue_SerTransportor);
                                            }
                                        }

                                        $servicesTaxesValue = $servicesTaxesValue + $calcValue_SerTransportor;

                                        $providerServiceTaxesAdjustArr[] = array(
                                            "taxId" => $taxId_SerTransportor,
                                            "taxValue" => $calcValue_SerTransportor,
                                            "taxVal" => (abs($taxValue_SerTransportor) + 0),
                                        );
                                        array_push($providerServiceTaxesArr, $taxId_SerTransportor);
                                    }

                                    if ($servicesTaxesValue == 0) {
                                        foreach ($getTransportorTaxes_Service_Supplier as $servicetaxsupplier) {

                                            $calcValue_SSTransportor = 0;
                                            $taxId_SSTransportor = $servicetaxsupplier->tax_id;
                                            $taxValue_SSTransportor = $servicetaxsupplier->tax_value;
                                            $taxFormat_SSTransportor = $servicetaxsupplier->number_format;
                                            $taxType_SSTransportor = $servicetaxsupplier->arithmetic_type;

                                            if ($taxValue_SSTransportor > 0) {
                                                if ($taxType_SSTransportor == 2) {
                                                    $taxValue_SSTransportor = $taxValue_SSTransportor * -1;
                                                }
                                                if ($taxFormat_SSTransportor == 2) {
                                                    $calcValue_SSTransportor = $servicecost * ($taxValue_SSTransportor / 100);
                                                } else {
                                                    $calcValue_SSTransportor = $servicecost * ($taxValue_SSTransportor);
                                                }
                                            }

                                            $servicesTaxesValue = $servicesTaxesValue + $calcValue_SSTransportor;

                                            $providerServiceTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_SSTransportor,
                                                "taxValue" => $calcValue_SSTransportor,
                                                "taxVal" => (abs($taxValue_SSTransportor) + 0),
                                            );

                                            array_push($supplierServiceTaxesArr, $taxId_SSTransportor);
                                        }
                                    }
                                } else if ($servicepayto == $supplierid) {

                                    $getTransportorTaxes_Services = $this->Master_model->get_supplier_taxes($logisticpayto);

                                    $servicesTaxesValue = 0;
                                    if (count($getTransportorTaxes_Services) > 0) {

                                        foreach ($getTransportorTaxes_Services as $servicesuppliertax) {

                                            $calcValue_service = 0;
                                            $taxId_service = $servicesuppliertax->tax_id;
                                            $taxValue_service = $servicesuppliertax->tax_value;
                                            $taxFormat_service = $servicesuppliertax->number_format;
                                            $taxType_service = $servicesuppliertax->arithmetic_type;

                                            if ($taxValue_service > 0) {
                                                if ($taxType_service == 2) {
                                                    $taxValue_service = $taxValue_service * -1;
                                                }
                                                if ($taxFormat_service == 2) {
                                                    $calcValue_service = $servicecost * ($taxValue_service / 100);
                                                } else {
                                                    $calcValue_service = $servicecost * ($taxValue_service);
                                                }
                                            }

                                            $servicesTaxesValue = $servicesTaxesValue + $calcValue_service;

                                            $providerServiceTaxesAdjustArr[] = array(
                                                "taxId" => $taxId_service,
                                                "taxValue" => $calcValue_service,
                                                "taxVal" => (abs($taxValue_service) + 0),
                                            );

                                            array_push($supplierServiceTaxesArr, $taxId_service);
                                        }
                                    }
                                }
                            }

                            $servicesCostWithTaxes = $servicecost + $servicesTaxesValue;

                            // END SERVICES WITH TAXES

                            $adjust_arr = explode(",", $adjustrf);
                            $isAdjustEnabled = false;
                            $adjustmentValues = 0;

                            if (count($adjust_arr) > 0) {

                                $isAdjustEnabled = true;

                                $totalValueWithTaxes = $woodValueWithSupplierTaxes + $logisticsCostWithTaxes + $servicesCostWithTaxes;

                                foreach ($adjust_arr as $adjust) {

                                    foreach ($supplierTaxesAdjustArr as $suppliertaxestax) {
                                        if ($suppliertaxestax["taxId"] == $adjust) {
                                            $totalValueWithTaxes = $totalValueWithTaxes - abs($suppliertaxestax["taxValue"]);
                                            $adjustmentValues = $adjustmentValues + abs($suppliertaxestax["taxValue"]);
                                        }
                                    }

                                    foreach ($providerServiceTaxesAdjustArr as $providerservicetaxestax) {
                                        if ($providerservicetaxestax["taxId"] == $adjust) {
                                            $totalValueWithTaxes = $totalValueWithTaxes - abs($providerservicetaxestax["taxValue"]);
                                            $adjustmentValues = $adjustmentValues + abs($suppliertaxestax["taxValue"]);
                                        }
                                    }

                                    foreach ($providerLogisticTaxesAdjustArr as $providerlogistictaxestax) {
                                        if ($providerlogistictaxestax["taxId"] == $adjust) {
                                            $totalValueWithTaxes = $totalValueWithTaxes - abs($providerlogistictaxestax["taxValue"]);
                                            $adjustmentValues = $adjustmentValues + abs($suppliertaxestax["taxValue"]);
                                        }
                                    }
                                }
                            } else {
                                $totalValueWithTaxes = $woodValueWithSupplierTaxes + $logisticsCostWithTaxes + $servicesCostWithTaxes;
                            }

                            if ($farmadjustment != 0) {
                                $totalValueWithTaxes = $totalValueWithTaxes - $farmadjustment;
                            }

                            $totalValue = $woodValue + $logisticcost + $servicecost;

                            $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                            $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                            $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                            $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                            $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                            $dataFarm = array(
                                "supplier_id" => $supplierid, "contract_id" => $purchasecontractid,
                                "product_id" => $productid, "product_type_id" => $producttypeid, "purchase_unit_id" => $purchaseunit,
                                "inventory_order" => $inventoryorder, "plate_number" => $truckplatenumber,
                                "purchase_date" => $purchase_date, "service_cost" => $servicecost,
                                "logistic_cost" => $logisticcost, "adjustment" => $farmadjustment,
                                "total_volume" => $totalVolume, "total_value" => $totalValue, "wood_value" => $woodValue,
                                "pay_service_to" => $servicepayto, "pay_logistics_to" => $logisticpayto,
                                "exchange_rate" => $conversionrate,
                                "created_by" => $session['user_id'], "updated_by" => $session['user_id'], "is_active" => 1,
                                "origin_id" => $originid, "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                "service_cost_withtaxes" => $servicesCostWithTaxes, "logistic_cost_withtaxes" => $logisticsCostWithTaxes,
                                "supplier_taxes" => $supplierTaxesArrList, "logistic_taxes" => $supplierLogisticTaxesArrList,
                                "service_taxes" => $supplierServiceTaxesArrList, "adjust_taxes" => $adjustrf,
                                "is_adjust_rf" => $isAdjustEnabled, "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                "service_provider_taxes" => $providerServiceTaxesArrList, "adjusted_value" => $adjustmentValues,
                                "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                            );

                            $insertFarm = $this->Farm_model->add_farm($dataFarm);

                            if ($insertFarm > 0) {
                                $dataFarmData = array();
                                foreach ($farmdataJson as $farm) {
                                    $circumference = $farm["circumference"];
                                    $netVolume = $farm["netVolume"];
                                    $noOfPieces = $farm["noOfPieces"];
                                    $length = $farm["length"];

                                    if ($noOfPieces > 0) {
                                        $dataFarmData[] = array(
                                            "farm_id" => $insertFarm, "scanned_code" => "",
                                            "no_of_pieces" => $noOfPieces, "circumference" => $circumference,
                                            "length" => $length, "width" => 0, "thickness" => 0, "volume" => $netVolume,
                                            "volume_pie" => 0, "grade_id" => 0, "length_export" => 0, "width_export" => 0,
                                            "thickness_export" => 0, "volume_bought" => 0, "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1,
                                            "created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
                                        );
                                    }
                                }

                                if (count($dataFarmData) > 0) {
                                    $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                    if ($insertFarmData) {
                                        //SUPPLIER PRICE
                                        $this->Farm_model->add_supplier_price(
                                            $purchasecontractid,
                                            $supplierid,
                                            $inventoryorder,
                                            $session['user_id']
                                        );

                                        //CONTRACT INVENTORY MAPPING
                                        $dataContractMapping = array(
                                            "contract_id" => $purchasecontractid, "supplier_id" => $supplierid,
                                            "inventory_order" => $inventoryorder, "total_volume" => $totalVolume,
                                            "invoice_number" => "", "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1,
                                        );

                                        $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                        $dataInventoryLedger = array(
                                            "contract_id" => $purchasecontractid,
                                            "inventory_order" => $inventoryorder, "ledger_type" => 2,
                                            "expense_date" => $purchase_date, "created_by" => $session['user_id'],
                                            "updated_by" => $session['user_id'], "is_active" => 1, "is_advance_app" => 0,
                                        );

                                        if ($woodValueWithSupplierTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierid);
                                        }

                                        if ($logisticsCostWithTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $logisticsCostWithTaxes, 2, $logisticpayto);
                                        }

                                        if ($servicesCostWithTaxes != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $servicesCostWithTaxes, 3, $servicepayto);
                                        }

                                        if ($farmadjustment != 0) {
                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $farmadjustment, 4, $supplierid);
                                        }

                                        $getContracts = $this->Contract_model->get_contracts_by_contractid($purchasecontractid);
                                        if (count($getContracts) == 1) {
                                            $remainingVolume = $getContracts[0]->remaining_volume - $totalVolume;

                                            $dataRemainingVolume = array(
                                                "remaining_volume" => $remainingVolume,
                                            );

                                            $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchasecontractid, $supplierid);
                                        }

                                        //CREATE RECEPTION

                                        if ($warehouseid_rounglogs > 0 && $measurement_system_roundlogs > 0) {

                                            $getFormulae = $this->Master_model->get_formulae_by_measurementsystem($measurement_system_roundlogs, $originid);

                                            if (count($getFormulae) > 0) {
                                                foreach ($getFormulae as $formula) {
                                                    $strFormula = str_replace(array('truncate'), array('$this->truncate'), $formula->calculation_formula);

                                                    if ($formula->context == "CBM_HOPPUS_GROSSVOLUME") {
                                                        $grossFormula = "return (" . $strFormula . ");";
                                                    }

                                                    if ($formula->context == "CBM_HOPPUS_NETVOLUME") {
                                                        $netFormula = "return (" . $strFormula . ");";
                                                    }

                                                    if ($formula->context == "CBM_GEO_GROSSVOLUME") {
                                                        $grossFormula = "return (" . $strFormula . ");";
                                                    }

                                                    if ($formula->context == "CBM_GEO_NETVOLUME") {
                                                        $netFormula = "return (" . $strFormula . ");";
                                                    }
                                                }
                                            }

                                            if ($grossFormula != "" && $netFormula != "") {

                                                $getSupplierDetails = $this->Master_model->get_supplier_detail_reception($supplierid, $productid);

                                                if (count($getSupplierDetails) == 1) {

                                                    $productTypeId = 2;
                                                    if ($getSupplierDetails[0]->product_type == 3) {
                                                        $productTypeId = 4;
                                                    }

                                                    $dataReception = array(
                                                        "warehouse_id" => $warehouseid_rounglogs, "supplier_id" => $supplierid,
                                                        "supplier_code" => $getSupplierDetails[0]->supplier_code, "supplier_product_id" => $getSupplierDetails[0]->product_name,
                                                        "supplier_product_typeid" => $productTypeId,  "measurementsystem_id" => $measurement_system_roundlogs,
                                                        "received_date" => $receptiondate_roundlogs, "salvoconducto" => $inventoryorder,
                                                        "createdby" => $session['user_id'], "updatedby" => $session['user_id'],
                                                        "isactive" => 1, "isclosed" => 1, "closedby" => $session['user_id'], "closeddate" => date('Y-m-d H:i:s'),
                                                        "captured_timestamp" => 0, "isduplicatecaptured" => 0, "is_contract_added" => 0,
                                                        "is_special_uploaded" => 1, "origin_id" => $originid,
                                                    );

                                                    $insertReception = $this->Reception_model->add_reception($dataReception);

                                                    $dataReceptionData = array();
                                                    if ($insertReception > 0) {

                                                        $dataReceptionTracking = array(
                                                            "reception_id" => $insertReception, "user_id" => $session['user_id'],
                                                            "isclosed" => 1, "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                                        );

                                                        $insertReceptionTracking = $this->Reception_model->add_reception_tracking($dataReceptionTracking);

                                                        $totalReceptionVolume = 0;
                                                        $totalReceptionPieces = 0;

                                                        foreach ($farmdataJson as $farm) {

                                                            $circumference = $farm["circumference"];
                                                            $noOfPieces = $farm["noOfPieces"];
                                                            $length = $farm["length"];

                                                            $grossFormulaVal = str_replace(array('$l', '$c'), array($length, $circumference), $grossFormula);
                                                            $grossVolume = sprintf('%0.3f', eval($grossFormulaVal) * $noOfPieces);

                                                            $netFormulaVal = str_replace(array('$l', '$c'), array($length, $circumference), $netFormula);
                                                            $netVolume = sprintf('%0.3f', eval($netFormulaVal) * $noOfPieces);

                                                            $totalReceptionVolume = $totalReceptionVolume + $netVolume;
                                                            $totalReceptionPieces = $totalReceptionPieces + $noOfPieces;

                                                            $dataReceptionData[] = array(
                                                                "reception_id" => $insertReception, "salvoconducto" => $inventoryorder,
                                                                "scanned_code" => $noOfPieces, "length_bought" => $length,
                                                                "width_bought" => 0, "thickness_bought" => 0,
                                                                "circumference_bought" => $circumference, "volumepie_bought" => 0,
                                                                "cbm_bought" => $grossVolume, "length_export" => 0, "width_export" => 0,
                                                                "thickness_export" => 0, "cbm_export" => $netVolume, "grade" => 0,
                                                                "createdby" => $session['user_id'], "updatedby" => $session['user_id'], "isactive" => 1,
                                                                "isdispatch" => 0, "scanned_timestamp" => 0, "isduplicatescanned" => 0, "is_special" => 1,
                                                                "createddate" => date('Y-m-d H:i:s'), "updateddate" => date('Y-m-d H:i:s'), "remaining_stock_count" => $noOfPieces,
                                                            );
                                                        }

                                                        $this->Reception_model->add_reception_data($dataReceptionData);

                                                        //UPDATE
                                                        $dataReceptionUpdate = array(
                                                            "total_volume" => $totalReceptionVolume, "total_pieces" => $totalReceptionPieces,
                                                            "updatedby" => $session['user_id'],
                                                        );

                                                        $this->Reception_model->update_reception($insertReception, $inventoryorder, $dataReceptionUpdate);

                                                        //CHECK ALL CLOSED RECEPTION
                                                        $checkClosedReception = $this->Reception_model->get_reception_closed_status($insertReception);
                                                        if ($checkClosedReception[0]->isclosed == 1) {

                                                            //SEND MAIL
                                                            $getReceptionDetail = $this->Reception_model->get_reception_detail_by_id($insertReception);
                                                            $fetchEmailTemplate = $this->Master_model->get_email_template_by_code("RECEPTIONCLOSE");

                                                            $mailSubject = $fetchEmailTemplate[0]->template_subject . " " . $getReceptionDetail[0]->salvoconducto;
                                                            $logo = base_url() . 'assets/img/iconz/cgrlogo_new.png';

                                                            $woodtype = $this->lang->line($getReceptionDetail[0]->product_type_name);
                                                            $netvolume = ($getReceptionDetail[0]->total_volume + 0) . " " . $this->lang->line("volume_unit");
                                                            $message = '<div style="background:#f6f6f6;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;padding: 20px;">
                                                                <img width="74px" src="' . $logo . '" title="Codrin Green"><br>' . str_replace(
                                                                array("{var inventorynumber}", "{var suppliername}", "{var woodspecies}", "{var woodtype}", "{var warehouse}", "{var totalpieces}", "{var netvolume}", "{var closedby}", "{var origin}"),
                                                                array(
                                                                    $getReceptionDetail[0]->salvoconducto, $getReceptionDetail[0]->supplier_name, $getReceptionDetail[0]->product_name, $woodtype,
                                                                    $getReceptionDetail[0]->warehouse_name, $getReceptionDetail[0]->total_pieces, $netvolume,
                                                                    $getReceptionDetail[0]->closedby, $getReceptionDetail[0]->origin
                                                                ),
                                                                htmlspecialchars_decode(stripslashes($fetchEmailTemplate[0]->template_message))
                                                            ) . '</div>';

                                                            $config = array(
                                                                'protocol' => 'smtp',
                                                                'smtp_host' => 'smtp.titan.email',
                                                                'smtp_port' => 587,
                                                                'smtp_user' => 'codrinsystems@codringreen.com',
                                                                'smtp_pass' => "Tb]-(g3Bjh&t[,K5",
                                                                'mailtype'  => 'html',
                                                                'charset'   => 'utf-8',
                                                                'wordwrap' => TRUE
                                                            );

                                                            $this->load->library('email', $config);
                                                            $this->email->set_newline("\r\n");

                                                            if ($getReceptionDetail[0]->origin_id == 1) {
                                                                $list = array('priyank@codringroup.com', 'jonathan.batista@codringroup.com', 'nafeel@codringroup.com');
                                                            } else {
                                                                $list = array('priyank@codringroup.com', 'ivette@codringroup.com', 'srikumar@codringroup.com', 'nafeel@codringroup.com');
                                                            }
                                                            $this->email->to($list);
                                                            $this->email->from("codrinsystems@codringreen.com", "Codrin Systems");
                                                            $this->email->bcc("nafeel@codringroup.com");
                                                            $this->email->subject($mailSubject);
                                                            $this->email->message("$message");
                                                            $resultSend = $this->email->send();
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //END RECEPTION
                                    }

                                    $Return['error'] = "";
                                    $Return['result'] = $this->lang->line('data_added');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return['error'] = $this->lang->line('error_adding');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['error'] = $this->lang->line('error_adding');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }

                            $Return['error'] = $totalValueWithTaxes; //$this->lang->line('error_adding');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {
                        $Return['error'] = $this->lang->line('exist_inventory_order');
                        $Return['result'] = "";
                        $Return['redirect'] = false;
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else if ($this->input->post('action_type') == 'update') {

                    $originid = $this->input->post('origin_id');
                    $farm_id = $this->input->post('farm_id');
                    $contract_id = $this->input->post('contract_id');
                    $inventory_order = strtoupper($this->input->post('inventory_order'));
                    $input_inventory_order = strtoupper($this->input->post('input_inventory_order'));
                    $input_truck_plate_number = strtoupper($this->input->post('input_truck_plate_number'));
                    $input_purchase_date = $this->input->post('input_purchase_date');
                    $input_purchase_date = str_replace('/', '-', $input_purchase_date);
                    $purchase_date = date("Y-m-d", strtotime($input_purchase_date));

                    if ($input_inventory_order == $inventory_order) {

                        $dataFarm = array(
                            "plate_number" => $input_truck_plate_number,
                            "purchase_date" => $purchase_date, "updated_by" => $session['user_id']
                        );

                        $updateFarm = $this->Farm_model->update_farm($farm_id, $inventory_order, $contract_id, $dataFarm);

                        if ($updateFarm == true) {
                            $dataInventoryLedger = array(
                                "expense_date" => $purchase_date, "updated_by" => $session['user_id']
                            );

                            $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventory_order, $contract_id, $dataInventoryLedger);

                            if ($updateInventoryLedger == true) {
                                $Return['result'] = $this->lang->line('data_updated');
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            } else {
                                $Return['error'] = $this->lang->line('error_updating');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return['error'] = $this->lang->line('error_updating');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    } else {

                        $getInventoryOrderCount = $this->Farm_model->get_inventory_order_count($input_inventory_order, $originid);

                        if ($getInventoryOrderCount[0]->cnt == 0) {

                            $dataFarm = array(
                                "plate_number" => $input_truck_plate_number, "inventory_order" => $input_inventory_order,
                                "purchase_date" => $purchase_date, "updated_by" => $session['user_id']
                            );

                            $updateFarm = $this->Farm_model->update_farm($farm_id, $inventory_order, $contract_id, $dataFarm);

                            if ($updateFarm == true) {

                                $dataContractMapping = array(
                                    "inventory_order" => $input_inventory_order, "updated_by" => $session['user_id']
                                );

                                $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventory_order, $contract_id, $dataContractMapping);

                                $dataContractInventoryPrice = array(
                                    "inventory_number" => $input_inventory_order, "updated_by" => $session['user_id']
                                );

                                $updateContractInventoryPrice = $this->Farm_model->update_contract_price($inventory_order, $contract_id, $dataContractInventoryPrice);

                                $dataInventoryLedger = array(
                                    "inventory_order" => $input_inventory_order,
                                    "expense_date" => $purchase_date, "updated_by" => $session['user_id']
                                );

                                $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventory_order, $contract_id, $dataInventoryLedger);

                                if ($updateInventoryLedger == true && $updateContractMapping == true && $updateContractInventoryPrice == true) {
                                    $Return['result'] = $this->lang->line('data_updated');
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return['error'] = $this->lang->line('error_updating');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['error'] = $this->lang->line('error_updating');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return['error'] = $this->lang->line('exist_inventory_order');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else {
                    $Return['error'] = $this->lang->line('invalid_request');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                }
            } else {
                redirect("/logout");
            }
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }

    public function load_farm_template()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                $inventoryOrderPost = $this->input->post("inventoryOrder");
                $originId = $this->input->post("originId");
                $purchaseUnit = $this->input->post("purchaseUnit");
                $productTypeId = $this->input->post("productTypeId");
                $purchaseContractId = $this->input->post("purchaseContractId");

                $getInventoryOrderCount = $this->Farm_model->get_inventory_order_count($inventoryOrderPost, $originId);

                if ($getInventoryOrderCount[0]->cnt == 0) {
                    if ($_FILES['fileFarmExcel']['size'] > 0) {
                        $config['upload_path'] = FCPATH . 'reports/';
                        $config['allowed_types'] = 'xlsx';
                        $config['remove_spaces'] = TRUE;
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('fileFarmExcel')) {
                            $Return['error'] = $this->lang->line('error_excel_upload');
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        } else {
                            $data = array('upload_data' => $this->upload->data());
                            $inputFileName = FCPATH . 'reports/' . $data['upload_data']['file_name'];
                            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                            $objPHPExcel = $objReader->load($inputFileName);
                            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            $arrayCount = count($allDataInSheet);

                            if ($arrayCount > 0) {

                                $getContractDetails = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);

                                $circumferenceAllowance = 0;
                                $lengthAllowance = 0;

                                if (count($getContractDetails) == 1) {
                                    $circumferenceAllowance = $getContractDetails[0]->purchase_allowance;
                                    $lengthAllowance = $getContractDetails[0]->purchase_allowance_length;
                                }

                                if ($productTypeId == 1 || $productTypeId == 3) {

                                    $createArray = array('INVENTORY ORDER', 'SCANNED CODE', 'LENGTH', 'WIDTH', 'THICKNESS');
                                    $makeArray = array('INVENTORYORDER' => 'INVENTORY ORDER', 'SCANNEDCODE' => 'SCANNED CODE', 'LENGTH' => 'LENGTH', 'WIDTH' => 'WIDTH', 'THICKNESS' => 'THICKNESS');
                                    $SheetDataKey = array();
                                    foreach ($allDataInSheet as $dataInSheet) {
                                        foreach ($dataInSheet as $key => $value) {

                                            if (in_array(trim($value), $createArray)) {
                                                $value = preg_replace('/\s+/', '', $value);
                                                $SheetDataKey[trim($value)] = $key;
                                            }
                                        }
                                    }
                                    $data = array_diff_key($makeArray, $SheetDataKey);
                                    $farmData = array();
                                    $totalPieces = 0;
                                    $totalNetVolume = 0;
                                    $totalVolumePie = 0;
                                    if (empty($data)) {

                                        $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($purchaseUnit, $originId);
                                        $getCalulationType = $this->Master_model->get_calulation_type($purchaseUnit);

                                        $calculationType = $getCalulationType[0]->calculation_type;

                                        for ($i = 2; $i <= $arrayCount; $i++) {
                                            $inventoryOrder = $SheetDataKey['INVENTORYORDER'];
                                            $scannedCode = $SheetDataKey['SCANNEDCODE'];
                                            $length = $SheetDataKey['LENGTH'];
                                            $width = $SheetDataKey['WIDTH'];
                                            $thickness = $SheetDataKey['THICKNESS'];

                                            $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $scannedCode = filter_var(trim($allDataInSheet[$i][$scannedCode]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $widthVal = filter_var(trim($allDataInSheet[$i][$width]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $thicknessVal = filter_var(trim($allDataInSheet[$i][$thickness]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                            if (($inventoryOrderVal != null && $inventoryOrderVal != "") &&
                                                ($scannedCode != null && $scannedCode != "") &&
                                                ($lengthVal != null && $lengthVal != "" && $lengthVal > 0) &&
                                                ($widthVal != null && $widthVal != "" && $widthVal > 0) &&
                                                ($thicknessVal != null && $thicknessVal != "" && $thicknessVal > 0)
                                            ) {

                                                $getValidScannedCode = $this->Farm_model->get_scanned_code_count($scannedCode, $originId);
                                                $getExistingScannedCode = $this->Farm_model->get_farm_scanned_code_count($scannedCode, $originId);

                                                if ($getValidScannedCode[0]->cnt == 1) {
                                                    if ($getExistingScannedCode[0]->cnt == 0) {
                                                        if ($inventoryOrderVal == $inventoryOrderPost) {

                                                            $netVolume = 0;
                                                            $volumePie = 0;
                                                            $grossVolume = 0;
                                                            $widthExport = 0;
                                                            $thicknessExport = 0;
                                                            $lengthExport = 0;
                                                            $grade = 0;

                                                            if (count($getFormulae) > 0) {
                                                                foreach ($getFormulae as $formula) {
                                                                    $strFormula = str_replace(
                                                                        array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        $formula->formula_context
                                                                    );
                                                                    $strFormula = "return (" . $strFormula . ");";

                                                                    if ($purchaseUnit == 1) {
                                                                        if ($formula->type == "volumepie") {
                                                                            $volumePie = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "grossvolume") {
                                                                            $grossVolume = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "widthexport") {
                                                                            $widthExport = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "thicknessexport") {
                                                                            $thicknessExport = eval($strFormula);
                                                                        }

                                                                        if ($formula->type == "lengthexport") {
                                                                            $lengthExport = eval($strFormula);
                                                                        }
                                                                    } else if ($purchaseUnit == 2) {
                                                                        $widthExport = $widthVal;
                                                                        $thicknessExport = $thicknessVal;
                                                                        $lengthExport = $lengthVal;
                                                                    }

                                                                    if ($formula->type == "grade") {
                                                                        $grade = eval($strFormula);
                                                                        // $grade = $this->calculateFormula(array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        // array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        // $formula->formula_context);
                                                                    }

                                                                    if ($formula->type == "netvolume") {
                                                                        $netVolume = eval($strFormula);
                                                                        // $netVolume = $this->calculateFormula(array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                                                        // array($lengthVal, $widthVal, $thicknessVal, $volumePie, '$this->truncate', $widthExport, $thicknessExport, $lengthExport),
                                                                        // $formula->formula_context);
                                                                    }
                                                                }

                                                                if ($purchaseUnit == 2) {

                                                                    $lengthVal = 0;
                                                                    $widthVal = 0;
                                                                    $thicknessVal = 0;
                                                                }

                                                                $farmData[] = array(
                                                                    'noOfPieces' => 1,
                                                                    'length' => sprintf('%0.1f', ($lengthVal + 0)),
                                                                    'width' => ($widthVal + 0),
                                                                    'thickness' => ($thicknessVal + 0),
                                                                    'lengthExport' => sprintf('%0.1f', ($lengthExport + 0)),
                                                                    'widthExport' => ($widthExport + 0),
                                                                    'thicknessExport' => ($thicknessExport + 0),
                                                                    'volumePie' => $volumePie,
                                                                    'grossVolume' => sprintf('%0.3f', $grossVolume),
                                                                    'grade' => $grade,
                                                                    'netVolume' => sprintf('%0.3f', $netVolume),
                                                                    'scannedCode' => $scannedCode,
                                                                );

                                                                $totalVolumePie = $totalVolumePie + $volumePie;
                                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                                $totalPieces = $totalPieces + 1;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (count($farmData) == 0 || $totalPieces == 0) {
                                            $Return['warning'] = $this->lang->line('error_nodata_excel');
                                            $Return['error'] = "";
                                            $Return['result'] = "";
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        } else {

                                            if ($calculationType == "PIE") {
                                                $totalVolume = $totalVolumePie;
                                            } else if ($calculationType == "CBM") {
                                                $totalVolume = $totalNetVolume;
                                            }

                                            $dataUploaded = array(
                                                "farmData" => $farmData,
                                                "totalPieces" => $totalPieces,
                                                "purchaseUnit" => $purchaseUnit,
                                                "totalVolume" => sprintf('%0.3f', $totalVolume),
                                            );

                                            $Return['error'] = "";
                                            $Return['result'] = $dataUploaded;
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    } else {
                                        $Return['error'] = $this->lang->line('error_excel_template');
                                        $Return['result'] = "";
                                        $Return['redirect'] = false;
                                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else if ($productTypeId == 2 || $productTypeId == 4) {

                                    $createArray = array('INVENTORY ORDER', 'NO OF PIECES', 'CIRCUMFERENCE', 'LENGTH');
                                    $makeArray = array('INVENTORYORDER' => 'INVENTORY ORDER', 'NOOFPIECES' => 'NO OF PIECES', 'CIRCUMFERENCE' => 'CIRCUMFERENCE', 'LENGTH' => 'LENGTH');
                                    $SheetDataKey = array();
                                    foreach ($allDataInSheet as $dataInSheet) {
                                        foreach ($dataInSheet as $key => $value) {

                                            if (in_array(trim($value), $createArray)) {
                                                $value = preg_replace('/\s+/', '', $value);
                                                $SheetDataKey[trim($value)] = $key;
                                            }
                                        }
                                    }
                                    $data = array_diff_key($makeArray, $SheetDataKey);
                                    $farmData = array();
                                    $totalPieces = 0;
                                    $totalNetVolume = 0;
                                    $totalGrossVolume = 0;
                                    if (empty($data)) {

                                        $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($purchaseUnit, $originId);

                                        for ($i = 2; $i <= $arrayCount; $i++) {
                                            $inventoryOrder = $SheetDataKey['INVENTORYORDER'];
                                            $noOfPieces = $SheetDataKey['NOOFPIECES'];
                                            $circumference = $SheetDataKey['CIRCUMFERENCE'];
                                            $length = $SheetDataKey['LENGTH'];

                                            $inventoryOrderVal = filter_var(trim($allDataInSheet[$i][$inventoryOrder]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $noOfPiecesVal = filter_var(trim($allDataInSheet[$i][$noOfPieces]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $circumferenceVal = filter_var(trim($allDataInSheet[$i][$circumference]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            $lengthVal = filter_var(trim($allDataInSheet[$i][$length]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                            
                                            $inventoryOrderVal = str_replace(",", "", $inventoryOrderVal);
                                            $circumferenceVal = str_replace(",", "", $circumferenceVal);
                                            $lengthVal = str_replace(",", "", $lengthVal);

                                            if (($inventoryOrderVal != null && $inventoryOrderVal != "") &&
                                                ($noOfPiecesVal != null && $noOfPiecesVal != "" && $noOfPiecesVal > 0) &&
                                                ($circumferenceVal != null && $circumferenceVal != "" && $circumferenceVal > 0) &&
                                                ($lengthVal != null && $lengthVal != "" && $lengthVal > 0)
                                            ) {
                                                if ($inventoryOrderVal == $inventoryOrderPost) {

                                                    $netVolume = 0;
                                                    $grossVolume = 0;
                                                    if (count($getFormulae) > 0) {
                                                        foreach ($getFormulae as $formula) {
                                                            $strFormula = str_replace(array('$ac', '$al', '$l', '$c', 'truncate'), array($circumferenceAllowance, $lengthAllowance, $lengthVal, $circumferenceVal, '$this->truncate'), $formula->formula_context);
                                                            $strFormula = "return (" . $strFormula . ");";

                                                            if ($formula->type == "netvolume") {
                                                                $netVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPiecesVal;
                                                            }

                                                            if ($formula->type == "grossvolume") {
                                                                $grossVolume = sprintf('%0.3f', eval($strFormula)) * $noOfPiecesVal;
                                                            }
                                                        }
                                                    }

                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $totalGrossVolume = $totalGrossVolume + $grossVolume;
                                                    $totalPieces = $totalPieces + $noOfPiecesVal;

                                                    if ($originId == 1) {
                                                        $isCircumferenceExists = false;
                                                        if (count($farmData) > 0) {
                                                            foreach ($farmData as &$value) {
                                                                if ($value['circumference'] == ($circumferenceVal + 0) && $value['length'] == ($lengthVal + 0)) {
                                                                    $isCircumferenceExists = true;
                                                                }
                                                            }
                                                        }

                                                        if ($isCircumferenceExists == true) {
                                                            foreach ($farmData as &$value) {
                                                                if (
                                                                    $value['circumference'] == ($circumferenceVal + 0)
                                                                    && $value['length'] == ($lengthVal + 0)
                                                                ) {
                                                                    $value['noOfPieces'] = $value['noOfPieces'] + ($noOfPiecesVal + 0);
                                                                    $value['netVolume'] = sprintf('%0.3f', $value['netVolume'] + sprintf('%0.3f', $netVolume));
                                                                    $value['grossVolume'] = sprintf('%0.3f', $value['grossVolume'] + sprintf('%0.3f', $grossVolume));
                                                                }
                                                            }
                                                        } else {
                                                            $farmData[] = array(
                                                                'noOfPieces' => ($noOfPiecesVal + 0),
                                                                'circumference' => ($circumferenceVal + 0),
                                                                'length' => ($lengthVal + 0),
                                                                'netVolume' => sprintf('%0.3f', $netVolume),
                                                                'grossVolume' => sprintf('%0.3f', $grossVolume),
                                                            );
                                                        }
                                                    } else {
                                                        $farmData[] = array(
                                                            'noOfPieces' => ($noOfPiecesVal + 0),
                                                            'circumference' => ($circumferenceVal + 0),
                                                            'length' => ($lengthVal + 0),
                                                            'netVolume' => sprintf('%0.3f', $netVolume),
                                                            'grossVolume' => sprintf('%0.3f', $grossVolume),
                                                        );
                                                    }
                                                }
                                            }
                                        }

                                        if (count($farmData) == 0 || $totalPieces == 0) {
                                            $Return['warning'] = $this->lang->line('error_nodata_excel');
                                            $Return['error'] = "";
                                            $Return['result'] = "";
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        } else {

                                            $dataUploaded = array(
                                                "farmData" => $farmData,
                                                "totalPieces" => $totalPieces,
                                                "purchaseUnit" => $purchaseUnit,
                                                "totalVolume" => sprintf('%0.3f', $totalNetVolume),
                                                "totalGrossVolume" => sprintf('%0.3f', $totalGrossVolume),
                                            );

                                            $Return['error'] = "";
                                            $Return['result'] = $dataUploaded;
                                            $Return['redirect'] = false;
                                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                            $this->output($Return);
                                            exit;
                                        }
                                    } else {
                                        $Return['error'] = $this->lang->line('error_excel_template');
                                        $Return['result'] = "";
                                        $Return['redirect'] = false;
                                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                        $this->output($Return);
                                        exit;
                                    }
                                } else {
                                    $Return['error'] = $this->lang->line('error_excel_template');
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                }
                            } else {
                                $Return['warning'] = $this->lang->line('error_nodata_excel');
                                $Return['error'] = "";
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_loadtemplate');
                        $Return['result'] = "";
                        $Return['redirect'] = false;
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                } else {
                    $Return['error'] = $this->lang->line('exist_inventory_order');
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
        } catch (Exception $e) {
            $Return['error'] = $this->lang->line('error_loadtemplate');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_farm_report($farmId, $contractId, $inventoryOrder)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getFarmDetails = $this->Farm_model->get_farm_details($farmId, $contractId, $inventoryOrder);
                $getFarmDataDetails = $this->Farm_model->get_farm_data_details($farmId, $contractId, $inventoryOrder);
                $return_arr_farmdata = array();
                $summaryHeaderLastRow = 0;

                if (count($getFarmDetails) == 1 && count($getFarmDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($inventoryOrder);
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue('A2', $this->lang->line('contract_code'));
                    $objSheet->SetCellValue('A3', $this->lang->line('supplier_name'));
                    $objSheet->SetCellValue('A4', $this->lang->line('inventory_order'));
                    $objSheet->SetCellValue('A5', $this->lang->line('product'));
                    $objSheet->SetCellValue('A6', $this->lang->line('purchase_date'));
                    $objSheet->SetCellValue('A7', $this->lang->line('origin'));
                    $objSheet->SetCellValue('A8', $this->lang->line('truck_plate_number'));
                    $objSheet->SetCellValue('C2', $this->lang->line('total_no_of_pieces'));
                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                        $objSheet->SetCellValue('C3', $this->lang->line('total_pie'));
                    } else {
                        $objSheet->SetCellValue('C3', $this->lang->line('total_volume'));
                    }
                    $objSheet->SetCellValue('C4', $this->lang->line('measuremet_system'));


                    $objSheet->SetCellValue('B2', $getFarmDetails[0]->contract_code);
                    $objSheet->SetCellValue('B3', $getFarmDetails[0]->supplier_name);
                    $objSheet->SetCellValue('B4', $getFarmDetails[0]->inventory_order);
                    $objSheet->SetCellValue('B5', $getFarmDetails[0]->product_name . ' - ' . $this->lang->line($getFarmDetails[0]->product_type_name));
                    $objSheet->SetCellValue('B6', $getFarmDetails[0]->purchase_date);
                    $objSheet->SetCellValue('B7', $getFarmDetails[0]->origin);
                    $objSheet->SetCellValue('B8', $getFarmDetails[0]->plate_number);
                    $objSheet->SetCellValue('D4', $this->lang->line($getFarmDetails[0]->purchase_unit));

                    $rowCount = 5;

                    if ($getFarmDetails[0]->purchase_allowance > 0) {
                        $rowVal = $rowCount++;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('circumference_allowance'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->purchase_allowance);
                    }

                    if ($getFarmDetails[0]->purchase_allowance_length > 0) {
                        $rowVal = $rowCount++;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('length_allowance'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->purchase_allowance_length);
                    }

                    if ($getFarmDetails[0]->exchange_rate > 0) {
                        $rowVal = $rowCount++;
                        $summaryHeaderLastRow = $rowVal + 1;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('conversion_rate'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->exchange_rate);
                    }

                    $objSheet->getStyle("A2:A8")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("C2:C8")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    $objSheet->getColumnDimension('C')->setAutoSize(true);
                    $objSheet->getColumnDimension('D')->setAutoSize(true);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A2:D8')->applyFromArray($styleArray);

                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($getFarmDetails[0]->origin_id);

                    $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($getFarmDetails[0]->unit_of_purchase, $getFarmDetails[0]->origin_id);

                    if (
                        $getFarmDetails[0]->unit_of_purchase == 3 || $getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5
                        || $getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7
                        || $getFarmDetails[0]->unit_of_purchase == 8 || $getFarmDetails[0]->unit_of_purchase == 9
                    ) {

                        if (count($getSupplierTaxes) >= 3) {
                            $rowCount = 2;
                        } else {
                            $rowCount = 6;
                        }
                        $totalStartRow = $rowCount;

                        $taxCellsArray = array();

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('total_payment'));
                        $totalPaymentRow = $rowCount;
                        $rowCount++;

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue("J$rowCount", $getFarmDetails[0]->logistic_cost);
                        $objSheet->getStyle("J$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $logisticCostRow = "$rowCount";
                        $rowCount++;

                        foreach ($getSupplierTaxes as $suppliertax) {

                            if ($suppliertax->number_format == 2) {
                                $suppliertax->tax_name = $suppliertax->tax_name . " (%)";
                            }
                            $objSheet->SetCellValue("I$rowCount", $suppliertax->tax_name);

                            if ($suppliertax->arithmetic_type == 2) {
                                $objSheet->getStyle("I$rowCount")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB("FF0000");
                            }

                            $supplierTaxesArr = json_decode($getFarmDetails[0]->supplier_taxes_array, true);
                            $logisticsTaxesArray = json_decode($getFarmDetails[0]->logistics_taxes_array, true);
                            $serviceTaxesArray = json_decode($getFarmDetails[0]->service_taxes_array, true);

                            if (count($supplierTaxesArr) > 0) {
                                $formula = "";

                                foreach ($supplierTaxesArr as $tax) {

                                    if ($tax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $taxval = $tax['taxVal'] * -1;
                                        } else {
                                            $taxval = $tax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J$$$*$taxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J$$$*$taxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J$$$*$taxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J$$$*$taxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($logisticsTaxesArray as $logistictax) {

                                    if ($logistictax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $ltaxval = $logistictax['taxVal'] * -1;
                                        } else {
                                            $ltaxval = $logistictax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J###*$ltaxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J###*$ltaxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J###*$ltaxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J###*$ltaxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($serviceTaxesArray as $servicetax) {

                                    if ($servicetax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $staxval = $servicetax['taxVal'] * -1;
                                        } else {
                                            $staxval = $servicetax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J&&&*$staxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J&&&*$staxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J&&&*$staxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J&&&*$staxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }
                            }

                            $rowCount++;
                        }

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('service_cost'));
                        $objSheet->SetCellValue("J$rowCount", $getFarmDetails[0]->service_cost);
                        $objSheet->getStyle("J$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $serviceCostRow = "$rowCount";
                        $rowCount++;

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('adjustment'));
                        $rowCount++;

                        $objSheet->getStyle("I$totalStartRow:J$rowCount")->applyFromArray($styleArray);

                        $calcRow = $rowCount;

                        $totalPaymentRow1 = $totalPaymentRow + 1;
                        $objSheet->SetCellValue("J$totalPaymentRow", "=SUM(J$totalPaymentRow1:J$calcRow)");

                        if (count($taxCellsArray) > 0) {
                            foreach ($taxCellsArray as $taxcell) {
                                $taxCells = $taxcell["rowCell"];
                                $objSheet->SetCellValue("$taxCells", str_replace(
                                    array("$$$", "###", "&&&"),
                                    array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                    $taxcell["formula"]
                                ));
                            }
                        }

                        if ($rowCount <= 6) {
                            $rowCount = 10;
                        }

                        $objSheet->SetCellValue("A$rowCount", $this->lang->line('circumference'));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line('length'));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line('pieces'));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('volume'));

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:D$rowCount");

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $objSheet->getStyle("A$rowCount:D$rowCount")->applyFromArray($styleArray);

                        $calcStartRow = $rowCount + 1;
                        $rowCountData = $rowCount + 1;
                        $grossVolumeTotal = 0;
                        foreach ($getFarmDataDetails as $farmdata) {
                            $objSheet->SetCellValue('A' . $rowCountData, ($farmdata->circumference + 0));
                            $objSheet->SetCellValue('B' . $rowCountData, ($farmdata->length + 0));
                            $objSheet->SetCellValue('C' . $rowCountData, ($farmdata->no_of_pieces + 0));

                            $strFormula = "";
                            $netVolumeFormula = "";
                            $grossVolumeFormula = "";
                            $strFormulaGross = "";



                            foreach ($getFormulae as $formula) {
                                if ($formula->type == "netvolume") {
                                    $netVolumeFormula = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate', 'pow', 'round'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, 'B' . $rowCountData, 'A' . $rowCountData, "TRUNC", "POWER", "ROUND"),
                                        $formula->formula_context
                                    );

                                    $strFormula = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, $farmdata->length, $farmdata->circumference, '$this->truncate'),
                                        $formula->formula_context
                                    );

                                    $strFormula = "return (" . $strFormula . ");";
                                }

                                if ($formula->type == "grossvolume") {

                                    $strFormulaGross = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, $farmdata->length, $farmdata->circumference, '$this->truncate'),
                                        $formula->formula_context
                                    );

                                    $strFormulaGross = "return (" . $strFormulaGross . ");";
                                }
                            }

                            $objSheet->SetCellValue('D' . $rowCountData, "=$netVolumeFormula*C$rowCountData");

                            $objSheet->getStyle("A$rowCountData:D$rowCountData")->applyFromArray($styleArray);

                            $row_array_farmdata['circumference'] = round($farmdata->circumference, 2);
                            if ($strFormula != "") {
                                $row_array_farmdata['netvolume'] = (eval($strFormula) * $farmdata->no_of_pieces);
                            } else {
                                $row_array_farmdata['netvolume'] = 0;
                            }

                            if ($strFormulaGross != "") {
                                $grossVolumeTotal = $grossVolumeTotal + ((eval($strFormulaGross) * $farmdata->no_of_pieces));
                            } else {
                                $grossVolumeTotal = $grossVolumeTotal + 0;
                            }

                            array_push($return_arr_farmdata, $row_array_farmdata);

                            $rowCountData++;
                        }

                        $calcLastRow = $rowCountData - 1;
                        $objSheet->SetCellValue("D2", "=SUM(C$calcStartRow:C$calcLastRow)");
                        $objSheet->SetCellValue("D3", "=SUM(D$calcStartRow:D$calcLastRow)");

                        if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                            $objSheet->SetCellValue("C$summaryHeaderLastRow", $this->lang->line('average_girth'));
                            $objSheet->SetCellValue("D$summaryHeaderLastRow", "=TRUNC(SUMPRODUCT(A$calcStartRow:A$calcLastRow,C$calcStartRow:C$calcLastRow)/D2,0)");
                        }

                        if ($getFarmDetails[0]->unit_of_purchase == 8 || $getFarmDetails[0]->unit_of_purchase == 9) {
                            $objSheet->SetCellValue("C$summaryHeaderLastRow", $this->lang->line('text_cft'));
                            $objSheet->SetCellValue("D$summaryHeaderLastRow", "=ROUND($grossVolumeTotal/D2*35.315,2)");
                        }


                        //SUMMARY DATA

                        $getFarmDataSummary = $this->Farm_model->get_farm_data_summary($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        if (count($getFarmDataSummary) > 0) {

                            $rowCountSummary = $rowCount;
                            $piecesPriceCalcRow = $rowCountSummary;

                            $objSheet->getStyle("H$rowCountSummary")->applyFromArray($styleArray);
                            $objSheet->getStyle("J$rowCountSummary")->applyFromArray($styleArray);
                            $rowCountSummary++;

                            $objSheet->SetCellValue("F$rowCountSummary", $this->lang->line('circumference_range'));
                            $objSheet->mergeCells("F$rowCountSummary:G$rowCountSummary");

                            if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                $objSheet->SetCellValue("H$rowCountSummary", $this->lang->line('volume'));
                                $objSheet->SetCellValue("I$rowCountSummary", $this->lang->line('value_volume'));
                            } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                $objSheet->SetCellValue("H$rowCountSummary", $this->lang->line('volume'));
                                $objSheet->SetCellValue("I$rowCountSummary", $this->lang->line('value_volume'));
                            } else {
                                $objSheet->SetCellValue("H$rowCountSummary", $this->lang->line('pieces'));
                                $objSheet->SetCellValue("I$rowCountSummary", $this->lang->line('value_piece'));
                            }
                            $objSheet->SetCellValue("J$rowCountSummary", $this->lang->line('total_value'));

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('DBEDFF');

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getFont()
                                ->setBold(true);

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")->applyFromArray($styleArray);

                            $rowCountDataSummary = $rowCountSummary;
                            $piecesCalcSRow = $rowCountDataSummary + 1;
                            $priceCalcSRow = $rowCountDataSummary + 1;
                            $piecesCalcERow = $rowCountDataSummary;
                            $priceCalcERow = $rowCountDataSummary;
                            $summaryPieces = 0;
                            $priceRange = 0;

                            foreach ($getFarmDataSummary as $farmdatasummary) {

                                $rowCountDataSummary++;

                                $piecesCalcERow = $rowCountDataSummary;
                                $priceCalcERow = $rowCountDataSummary;

                                $totalVolumePrice = 0;

                                $objSheet->SetCellValue("F$rowCountDataSummary", ($farmdatasummary->minrange_grade1 + 0));
                                $objSheet->SetCellValue("G$rowCountDataSummary", ($farmdatasummary->maxrange_grade2 + 0));

                                if (count($return_arr_farmdata) > 0) {
                                    foreach ($return_arr_farmdata as $key => $value) {
                                        $circVal = $value["circumference"];
                                        $netVolumeVal = $value["netvolume"];

                                        if ($circVal >= $farmdatasummary->minrange_grade1 && $circVal <= $farmdatasummary->maxrange_grade2) {
                                            $totalVolumePrice = $totalVolumePrice + $netVolumeVal;
                                        }
                                    }

                                    $summaryPieces = $summaryPieces + $totalVolumePrice;
                                    if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                        $objSheet->SetCellValue("H$rowCountDataSummary", $totalVolumePrice);
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                        $objSheet->SetCellValue("H$rowCountDataSummary", "=IF(" . '$D$' . "$summaryHeaderLastRow" . ">=F$rowCountDataSummary,IF(" . '$D$' . "$summaryHeaderLastRow" . "<=G$rowCountDataSummary," . '$D$' . "3,0),0)");
                                    } else {
                                        $objSheet->SetCellValue("H$rowCountDataSummary", $farmdatasummary->pieces_farm);
                                    }

                                    if ($getFarmDetails[0]->exchange_rate > 0) {
                                        $priceRange = $farmdatasummary->pricerange_grade3 * $getFarmDetails[0]->exchange_rate;
                                    } else {
                                        $priceRange = $farmdatasummary->pricerange_grade3;
                                    }
                                }

                                $objSheet->SetCellValue("I$rowCountDataSummary", ($priceRange + 0));
                                if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                    $objSheet->SetCellValue("J$rowCountDataSummary", ($priceRange * $totalVolumePrice + 0));
                                } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                    $objSheet->SetCellValue("J$rowCountDataSummary", "=H$rowCountDataSummary*I$rowCountDataSummary");
                                } else {
                                    $objSheet->SetCellValue("J$rowCountDataSummary", ($priceRange * $farmdatasummary->pieces_farm + 0));
                                }

                                $objSheet->getStyle("I$rowCountDataSummary:J$rowCountDataSummary")
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                                $objSheet->getStyle("F$rowCountDataSummary:J$rowCountDataSummary")->applyFromArray($styleArray);
                            }

                            $objSheet->getColumnDimension('F')->setAutoSize(false);
                            $objSheet->getColumnDimension('F')->setWidth('15');
                            $objSheet->getColumnDimension('G')->setAutoSize(false);
                            $objSheet->getColumnDimension('G')->setWidth('15');
                            $objSheet->getColumnDimension('H')->setAutoSize(false);
                            $objSheet->getColumnDimension('H')->setWidth('15');
                            $objSheet->getColumnDimension('I')->setAutoSize(false);
                            $objSheet->getColumnDimension('I')->setWidth('15');
                            $objSheet->getColumnDimension('J')->setAutoSize(false);
                            $objSheet->getColumnDimension('J')->setWidth('18');

                            $objSheet->SetCellValue("H$piecesPriceCalcRow", "=SUM(H$piecesCalcSRow:H$piecesCalcERow)");
                            $objSheet->SetCellValue("J$piecesPriceCalcRow", "=SUM(J$priceCalcSRow:J$priceCalcERow)");

                            $objSheet->getStyle("J2:J$calcRow")
                                ->getNumberFormat()
                                ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        }

                        //END SUMMARY DATA

                    } else if ($getFarmDetails[0]->unit_of_purchase == 1 || $getFarmDetails[0]->unit_of_purchase == 2) {

                        $rowCount = 10;

                        $objSheet->SetCellValue('A' . $rowCount, $this->lang->line('scanned_code'));
                        $objSheet->SetCellValue('B' . $rowCount, $this->lang->line('length') . ' ' . $this->lang->line('feet'));
                        $objSheet->SetCellValue('C' . $rowCount, $this->lang->line('width') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('D' . $rowCount, $this->lang->line('thickness') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('E' . $rowCount, $this->lang->line('volume_pie'));
                        $objSheet->SetCellValue('F' . $rowCount, $this->lang->line('gross_volume'));
                        $objSheet->SetCellValue('G' . $rowCount, $this->lang->line('length_export') . ' ' . $this->lang->line('meters'));
                        $objSheet->SetCellValue('H' . $rowCount, $this->lang->line('width_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('I' . $rowCount, $this->lang->line('thickness_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('J' . $rowCount, $this->lang->line('grade'));
                        $objSheet->SetCellValue('K' . $rowCount, $this->lang->line('net_volume'));
                        $objSheet->SetCellValue('L' . $rowCount, $this->lang->line('total_value'));

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:L$rowCount");

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension('A')->setAutoSize(true);
                        $objSheet->getColumnDimension('B')->setAutoSize(true);
                        $objSheet->getColumnDimension('C')->setAutoSize(true);
                        $objSheet->getColumnDimension('D')->setAutoSize(true);
                        $objSheet->getColumnDimension('E')->setAutoSize(true);
                        $objSheet->getColumnDimension('F')->setAutoSize(true);
                        $objSheet->getColumnDimension('G')->setAutoSize(true);
                        $objSheet->getColumnDimension('H')->setAutoSize(true);
                        $objSheet->getColumnDimension('I')->setAutoSize(true);
                        $objSheet->getColumnDimension('J')->setAutoSize(true);
                        $objSheet->getColumnDimension('K')->setAutoSize(true);
                        $objSheet->getColumnDimension('L')->setAutoSize(true);

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $objSheet->getStyle("A$rowCount:L$rowCount")->applyFromArray($styleArray);

                        $getContractPrice = $getFarmDataSummary = $this->Farm_model->get_farm_data_summary($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        $grade1Price = 0;
                        $grade2Price = 0;
                        $grade3Price = 0;
                        if (count($getContractPrice) == 1) {
                            $grade1Price = $getContractPrice[0]->minrange_grade1;
                            $grade2Price = $getContractPrice[0]->maxrange_grade2;
                            $grade3Price = $getContractPrice[0]->pricerange_grade3;
                        }

                        $objSheet->mergeCells('I2:I3');
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_pie"));
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_cbm"));
                        }

                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $objSheet->SetCellValue('I4', $grade1Price);
                        $objSheet->SetCellValue('I5', $grade2Price);
                        $objSheet->SetCellValue('I6', $grade3Price);

                        $objSheet->getStyle('I3:I6')
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);

                        $objSheet->getStyle('I2:I6')->applyFromArray($styleArray);

                        $rowCountData = 11;
                        foreach ($getFarmDataDetails as $farmdata) {
                            $objSheet->SetCellValue('A' . $rowCountData, $farmdata->scanned_code);
                            $objSheet->getStyle('A' . $rowCountData)
                                ->getNumberFormat()
                                ->setFormatCode('0');

                            $objSheet->SetCellValue('B' . $rowCountData, ($farmdata->length + 0));
                            $objSheet->SetCellValue('C' . $rowCountData, ($farmdata->width + 0));
                            $objSheet->SetCellValue('D' . $rowCountData, ($farmdata->thickness + 0));

                            $strFormula = "";
                            foreach ($getFormulae as $formula) {
                                $strFormula = str_replace(
                                    array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                    array(
                                        'B' . $rowCountData, 'C' . $rowCountData, 'D' . $rowCountData,
                                        'E' . $rowCountData, 'TRUNC', 'H' . $rowCountData, 'I' . $rowCountData,
                                        'G' . $rowCountData
                                    ),
                                    $formula->formula_context
                                );


                                if ($formula->type == "volumepie") {
                                    $objSheet->SetCellValue('E' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "grossvolume") {
                                    $objSheet->SetCellValue('F' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "lengthexport") {
                                    $objSheet->SetCellValue('G' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "widthexport") {
                                    $objSheet->SetCellValue('H' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "thicknessexport") {
                                    $objSheet->SetCellValue('I' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "netvolume") {
                                    $objSheet->SetCellValue('K' . $rowCountData, "=$strFormula");
                                }

                                if ($farmdata->grade_id == 1) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade1'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I4");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I4");
                                    }
                                } else if ($farmdata->grade_id == 2) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade2'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I5");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I5");
                                    }
                                } else {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade3'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I6");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I6");
                                    }
                                }

                                $objSheet->getStyle('L' . $rowCountData)
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                            }

                            $objSheet->getStyle("A$rowCountData:L$rowCountData")->applyFromArray($styleArray);

                            $rowCountData++;
                        }

                        $objSheet->getStyle("L9")->applyFromArray($styleArray);
                        $objSheet->SetCellValue('L9', "=SUM(L11:L$rowCountData)");

                        $objSheet->SetCellValue('K2', $this->lang->line('total_payment'));
                        $objSheet->SetCellValue('K3', $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue('K4', $this->lang->line('reteica'));
                        $objSheet->SetCellValue('K5', $this->lang->line('retention'));
                        $objSheet->SetCellValue('K6', $this->lang->line('iva'));
                        $objSheet->SetCellValue('K7', $this->lang->line('service_cost'));
                        $objSheet->SetCellValue('K8', $this->lang->line('adjustment'));

                        // WOOD VALUE WITH TAXES

                        $getSupplierTaxes = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->supplier_id);

                        $ivaFormula = "";
                        $retencionFormula = "";
                        $reteicaFormula = "";
                        if (count($getSupplierTaxes) == 1) {
                            if ($getSupplierTaxes[0]->is_iva_enabled == 1) {
                                $ivaFormula = $ivaFormula . "(L9*" . ($getSupplierTaxes[0]->iva_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_retencion_enabled == 1) {
                                $retencionFormula = $retencionFormula . "(L9*" . ($getSupplierTaxes[0]->retencion_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_reteica_enabled == 1) {
                                $reteicaFormula = $reteicaFormula . "(L9*" . ($getSupplierTaxes[0]->reteica_value + 0) . ")";
                            }
                        }

                        // END WOOD VALUE WITH TAXES

                        // LOGISTICS WITH TAXES

                        if ($getFarmDetails[0]->logistic_cost > 0 && $getFarmDetails[0]->pay_logistics_to > 0) {

                            $objSheet->SetCellValue('L3', ($getFarmDetails[0]->logistic_cost + 0));

                            $getTransportorTaxes_Logistics = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_logistics_to);
                            $getTransportorTaxes_Logistics_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                            if (count($getTransportorTaxes_Logistics) == 1) {
                                if ($getTransportorTaxes_Logistics[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else if ($getFarmDetails[0]->pay_logistics_to == $getFarmDetails[0]->supplier_id) {

                                $getTransportorTaxes_Logistics = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                                if (count($getTransportorTaxes_Logistics) == 1) {

                                    if ($getTransportorTaxes_Logistics[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END LOGISTICS WITH TAXES

                        // SERVICES WITH TAXES

                        if ($getFarmDetails[0]->service_cost > 0 && $getFarmDetails[0]->pay_service_to > 0) {

                            $objSheet->SetCellValue('L7', ($getFarmDetails[0]->service_cost + 0));

                            $getTransportorTaxes_Service = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_service_to);
                            $getTransportorTaxes_Service_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                            if (count($getTransportorTaxes_Service) == 1) {

                                if ($getTransportorTaxes_Service[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else {

                                $getTransportorTaxes_Service = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                                if (count($getTransportorTaxes_Service) == 1) {

                                    if ($getTransportorTaxes_Service[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END SERVICES WITH TAXES

                        if ($getFarmDetails[0]->adjustment > 0) {
                            $objSheet->SetCellValue('L8', $getFarmDetails[0]->adjustment);
                        }

                        $objSheet->SetCellValue('L2', "=SUM(L3:L9)");

                        if ($ivaFormula != "") {
                            $objSheet->SetCellValue("L6", "=$ivaFormula");
                        }

                        if ($retencionFormula != "") {
                            $objSheet->SetCellValue("L5", "=$retencionFormula");
                        }

                        if ($reteicaFormula != "") {
                            $objSheet->SetCellValue("L4", "=$reteicaFormula");
                        }

                        $objSheet->getStyle("K2:L8")->applyFromArray($styleArray);

                        $objSheet->getStyle("K4:L5")
                            ->getFont()
                            ->getColor()
                            ->setRGB('FF0000');

                        $objSheet->mergeCells('F2:H2');
                        $objSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                        $objSheet->getStyle('F2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('G3', $this->lang->line("volume"));
                        $objSheet->SetCellValue('H3', $this->lang->line("ages"));
                        $objSheet->getStyle('G3:H3')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('F4', $this->lang->line("grade1"));
                        $objSheet->SetCellValue('F5', $this->lang->line("grade2"));
                        $objSheet->SetCellValue('F6', $this->lang->line("grade3"));
                        $objSheet->SetCellValue('F7', $this->lang->line("total"));

                        $objSheet->SetCellValue('G4', "=SUMIF(J11:J$rowCountData,F4,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G5', "=SUMIF(J11:J$rowCountData,F5,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G6', "=SUMIF(J11:J$rowCountData,F6,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G7', "=SUM(G4:G6)");

                        $objSheet->getStyle('H4:H7')
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        $objSheet->SetCellValue('H4', "=G4/G7");
                        $objSheet->SetCellValue('H5', "=G5/G7");
                        $objSheet->SetCellValue('H6', "=G6/G7");
                        $objSheet->SetCellValue('H7', "=SUM(H4:H6)");

                        $objSheet->getStyle('G7:H7')
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('F2:H7')->applyFromArray($styleArray);

                        $objSheet->SetCellValue('D2', "=COUNT(A11:A$rowCountData)");
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('D3', "=SUM(E11:E$rowCountData)");
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('D3', "=SUM(K11:K$rowCountData)");
                        }


                        $objSheet->getStyle("L2:L9")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'FarmReport_' . $inventoryOrder . '_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/FarmReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/FarmReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line('no_data_reports');
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
        } catch (Exception $e) {
            $Return['error'] = $e->getMessage(); // $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_farm_report_field_purchase($farmId, $contractId, $inventoryOrder)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getFarmDetails = $this->Farm_model->get_farm_details($farmId, $contractId, $inventoryOrder);
                $getFarmDataDetails = $this->Farm_model->get_field_purchase_farm_data_details($farmId, $contractId, $inventoryOrder);
                $return_arr_farmdata = array();

                if (count($getFarmDetails) == 1 && count($getFarmDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($inventoryOrder);
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue('A2', $this->lang->line('contract_code'));
                    $objSheet->SetCellValue('A3', $this->lang->line('supplier_name'));
                    $objSheet->SetCellValue('A4', $this->lang->line('inventory_order'));
                    $objSheet->SetCellValue('A5', $this->lang->line('product'));
                    $objSheet->SetCellValue('A6', $this->lang->line('purchase_date'));
                    $objSheet->SetCellValue('A7', $this->lang->line('origin'));
                    $objSheet->SetCellValue('A8', $this->lang->line('truck_plate_number'));
                    $objSheet->SetCellValue('C2', $this->lang->line('total_no_of_pieces'));
                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                        $objSheet->SetCellValue('C3', $this->lang->line('total_pie'));
                    } else {
                        $objSheet->SetCellValue('C3', $this->lang->line('total_volume'));
                    }
                    $objSheet->SetCellValue('C4', $this->lang->line('measuremet_system'));


                    $objSheet->SetCellValue('B2', $getFarmDetails[0]->contract_code);
                    $objSheet->SetCellValue('B3', $getFarmDetails[0]->supplier_name);
                    $objSheet->SetCellValue('B4', $getFarmDetails[0]->inventory_order);
                    $objSheet->SetCellValue('B5', $getFarmDetails[0]->product_name . ' - ' . $this->lang->line($getFarmDetails[0]->product_type_name));
                    $objSheet->SetCellValue('B6', $getFarmDetails[0]->purchase_date);
                    $objSheet->SetCellValue('B7', $getFarmDetails[0]->origin);
                    $objSheet->SetCellValue('B8', $getFarmDetails[0]->plate_number);
                    $objSheet->SetCellValue('D4', $this->lang->line($getFarmDetails[0]->purchase_unit));

                    $rowCount = 5;

                    if ($getFarmDetails[0]->purchase_allowance > 0) {
                        $rowVal = $rowCount++;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('circumference_allowance'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->purchase_allowance);
                    }

                    if ($getFarmDetails[0]->purchase_allowance_length > 0) {
                        $rowVal = $rowCount++;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('length_allowance'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->purchase_allowance_length);
                    }

                    if ($getFarmDetails[0]->exchange_rate > 0) {
                        $rowVal = $rowCount++;
                        $objSheet->SetCellValue('C' . $rowVal, $this->lang->line('conversion_rate'));
                        $objSheet->SetCellValue('D' . $rowVal, $getFarmDetails[0]->exchange_rate);
                    }

                    $objSheet->getStyle("A2:A8")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getStyle("C2:C8")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    $objSheet->getColumnDimension('C')->setAutoSize(true);
                    $objSheet->getColumnDimension('D')->setAutoSize(true);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A2:D8')->applyFromArray($styleArray);

                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($getFarmDetails[0]->origin_id);

                    $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($getFarmDetails[0]->unit_of_purchase, $getFarmDetails[0]->origin_id);

                    if ($getFarmDetails[0]->unit_of_purchase == 3 || $getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {

                        $rowCount = 2;

                        $taxCellsArray = array();

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('total_payment'));
                        $rowCount++;

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue("J$rowCount", $getFarmDetails[0]->logistic_cost * -1);
                        $objSheet->getStyle("J$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $logisticCostRow = "$rowCount";
                        $rowCount++;

                        foreach ($getSupplierTaxes as $suppliertax) {

                            if ($suppliertax->number_format == 2) {
                                $suppliertax->tax_name = $suppliertax->tax_name . " (%)";
                            }
                            $objSheet->SetCellValue("I$rowCount", $suppliertax->tax_name);

                            if ($suppliertax->arithmetic_type == 2) {
                                $objSheet->getStyle("I$rowCount")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB("FF0000");
                            }

                            $supplierTaxesArr = json_decode($getFarmDetails[0]->supplier_taxes_array, true);
                            $logisticsTaxesArray = json_decode($getFarmDetails[0]->logistics_taxes_array, true);
                            $serviceTaxesArray = json_decode($getFarmDetails[0]->service_taxes_array, true);

                            if (count($supplierTaxesArr) > 0) {
                                $formula = "";

                                foreach ($supplierTaxesArr as $tax) {

                                    if ($tax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $taxval = $tax['taxVal'] * -1;
                                        } else {
                                            $taxval = $tax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J$$$*$taxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J$$$*$taxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J$$$*$taxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J$$$*$taxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($logisticsTaxesArray as $logistictax) {

                                    if ($logistictax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $ltaxval = $logistictax['taxVal'] * -1;
                                        } else {
                                            $ltaxval = $logistictax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J###*$ltaxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J###*$ltaxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J###*$ltaxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J###*$ltaxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($serviceTaxesArray as $servicetax) {

                                    if ($servicetax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $staxval = $servicetax['taxVal'] * -1;
                                        } else {
                                            $staxval = $servicetax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J&&&*$staxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(J&&&*$staxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(J&&&*$staxval)";
                                            } else {
                                                $formula = $formula . "+SUM(J&&&*$staxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "J$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }
                            }

                            $rowCount++;
                        }

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('service_cost'));
                        $objSheet->SetCellValue("J$rowCount", $getFarmDetails[0]->service_cost * -1);
                        $objSheet->getStyle("J$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $serviceCostRow = "$rowCount";
                        $rowCount++;

                        $objSheet->SetCellValue("I$rowCount", $this->lang->line('adjustment'));
                        $objSheet->SetCellValue("J$rowCount", $getFarmDetails[0]->adjustment * -1);
                        $objSheet->getStyle("J$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $rowCount++;

                        $objSheet->getStyle("I2:J$rowCount")->applyFromArray($styleArray);

                        $calcRow = $rowCount;

                        $objSheet->SetCellValue("J2", "=SUM(J3:J$calcRow)");

                        if (count($taxCellsArray) > 0) {
                            foreach ($taxCellsArray as $taxcell) {
                                $taxCells = $taxcell["rowCell"];
                                $objSheet->SetCellValue("$taxCells", str_replace(
                                    array("$$$", "###", "&&&"),
                                    array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                    $taxcell["formula"]
                                ));
                            }
                        }

                        $objSheet->SetCellValue("A$rowCount", $this->lang->line('circumference'));
                        $objSheet->SetCellValue("B$rowCount", $this->lang->line('length'));
                        $objSheet->SetCellValue("C$rowCount", $this->lang->line('pieces'));
                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('volume'));

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:D$rowCount");

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension("A")->setAutoSize(true);
                        $objSheet->getColumnDimension("B")->setAutoSize(true);
                        $objSheet->getColumnDimension("C")->setAutoSize(true);
                        $objSheet->getColumnDimension("D")->setAutoSize(true);

                        $objSheet->getStyle("A$rowCount:D$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $objSheet->getStyle("A$rowCount:D$rowCount")->applyFromArray($styleArray);

                        $calcStartRow = $rowCount + 1;
                        $rowCountData = $rowCount + 1;
                        foreach ($getFarmDataDetails as $farmdata) {
                            $objSheet->SetCellValue('A' . $rowCountData, $farmdata->circumference);
                            $objSheet->SetCellValue('B' . $rowCountData, ($farmdata->length + 0));
                            $objSheet->SetCellValue('C' . $rowCountData, ($farmdata->no_of_pieces + 0));

                            $strFormula = "";
                            $netVolumeFormula = "";
                            foreach ($getFormulae as $formula) {
                                if ($formula->type == "netvolume") {
                                    $netVolumeFormula = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate', 'pow', 'round'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, 'B' . $rowCountData, "(LEFT(A$rowCountData,FIND(" . '" - "' . ",A$rowCountData)-1) +1)", "TRUNC", "POWER", "ROUND"),
                                        $formula->formula_context
                                    );

                                    $strFormula = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, $farmdata->length, ($farmdata->min_circumference + 1), '$this->truncate'),
                                        $formula->formula_context
                                    );

                                    $strFormula = "return (" . $strFormula . ");";
                                }
                            }

                            $objSheet->SetCellValue('D' . $rowCountData, "=$netVolumeFormula*C$rowCountData");

                            $objSheet->getStyle("A$rowCountData:D$rowCountData")->applyFromArray($styleArray);

                            $row_array_farmdata['circumference'] = round($farmdata->circumference, 2);
                            if ($strFormula != "") {
                                $row_array_farmdata['netvolume'] = (eval($strFormula) * $farmdata->no_of_pieces);
                            } else {
                                $row_array_farmdata['netvolume'] = 0;
                            }
                            array_push($return_arr_farmdata, $row_array_farmdata);

                            $rowCountData++;
                        }

                        $calcLastRow = $rowCountData - 1;
                        $objSheet->SetCellValue("D2", "=SUM(C$calcStartRow:C$calcLastRow)");
                        $objSheet->SetCellValue("D3", "=SUM(D$calcStartRow:D$calcLastRow)");

                        //SUMMARY DATA

                        $getFarmDataSummary = $this->Farm_model->get_farm_data_summary_field_purchase($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        if (count($getFarmDataSummary) > 0) {
                            $rowCountSummary = $rowCount;

                            $piecesPriceCalcRow = $rowCountSummary;

                            $objSheet->getStyle("H$rowCountSummary")->applyFromArray($styleArray);
                            $objSheet->getStyle("J$rowCountSummary")->applyFromArray($styleArray);
                            $rowCountSummary++;

                            $objSheet->SetCellValue("F$rowCountSummary", $this->lang->line('circumference_range'));
                            $objSheet->mergeCells("F$rowCountSummary:G$rowCountSummary");

                            if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                $objSheet->SetCellValwue("H$rowCountSummary", $this->lang->line('volume'));
                                $objSheet->SetCellValue("I$rowCountSummary", $this->lang->line('value_volume'));
                            } else {
                                $objSheet->SetCellValue("H$rowCountSummary", $this->lang->line('pieces'));
                                $objSheet->SetCellValue("I$rowCountSummary", $this->lang->line('value_piece'));
                            }
                            $objSheet->SetCellValue("J$rowCountSummary", $this->lang->line('total_value'));

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('DBEDFF');

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getFont()
                                ->setBold(true);

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objSheet->getStyle("F$rowCountSummary:J$rowCountSummary")->applyFromArray($styleArray);

                            $rowCountDataSummary = $rowCountSummary;
                            $piecesCalcSRow = $rowCountDataSummary + 1;
                            $priceCalcSRow = $rowCountDataSummary + 1;
                            $piecesCalcERow = $rowCountDataSummary;
                            $priceCalcERow = $rowCountDataSummary;
                            $summaryPieces = 0;
                            $priceRange = 0;

                            foreach ($getFarmDataSummary as $farmdatasummary) {

                                $rowCountDataSummary++;

                                $piecesCalcERow = $rowCountDataSummary;
                                $priceCalcERow = $rowCountDataSummary;

                                $totalVolumePrice = 0;

                                $objSheet->SetCellValue("F$rowCountDataSummary", ($farmdatasummary->minrange_grade1 + 0));
                                $objSheet->SetCellValue("G$rowCountDataSummary", ($farmdatasummary->maxrange_grade2 + 0));

                                if (count($return_arr_farmdata) > 0) {
                                    foreach ($return_arr_farmdata as $key => $value) {
                                        $circVal = $value["circumference"];
                                        $netVolumeVal = $value["netvolume"];

                                        if ($circVal >= $farmdatasummary->minrange_grade1 && $circVal <= $farmdatasummary->maxrange_grade2) {
                                            $totalVolumePrice = $totalVolumePrice + $netVolumeVal;
                                        }
                                    }

                                    $summaryPieces = $summaryPieces + $totalVolumePrice;
                                    if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                        $objSheet->SetCellValue("H$rowCountDataSummary", $totalVolumePrice);
                                    } else {
                                        $objSheet->SetCellValue("H$rowCountDataSummary", $farmdatasummary->pieces_farm);
                                    }

                                    if ($getFarmDetails[0]->exchange_rate > 0) {
                                        $priceRange = $farmdatasummary->pricerange_grade3 * $getFarmDetails[0]->exchange_rate;
                                    } else {
                                        $priceRange = $farmdatasummary->pricerange_grade3;
                                    }
                                }

                                $objSheet->SetCellValue("I$rowCountDataSummary", ($priceRange + 0));
                                if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                    $objSheet->SetCellValue("J$rowCountDataSummary", ($priceRange * $totalVolumePrice + 0));
                                } else {
                                    $objSheet->SetCellValue("J$rowCountDataSummary", ($priceRange * $farmdatasummary->pieces_farm + 0));
                                }

                                $objSheet->getStyle("I$rowCountDataSummary:J$rowCountDataSummary")
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                                $objSheet->getStyle("F$rowCountDataSummary:J$rowCountDataSummary")->applyFromArray($styleArray);
                            }

                            $objSheet->getColumnDimension('F')->setAutoSize(false);
                            $objSheet->getColumnDimension('F')->setWidth('15');
                            $objSheet->getColumnDimension('G')->setAutoSize(false);
                            $objSheet->getColumnDimension('G')->setWidth('15');
                            $objSheet->getColumnDimension('H')->setAutoSize(false);
                            $objSheet->getColumnDimension('H')->setWidth('15');
                            $objSheet->getColumnDimension('I')->setAutoSize(false);
                            $objSheet->getColumnDimension('I')->setWidth('15');
                            $objSheet->getColumnDimension('J')->setAutoSize(false);
                            $objSheet->getColumnDimension('J')->setWidth('18');

                            $objSheet->SetCellValue("H$piecesPriceCalcRow", "=SUM(H$piecesCalcSRow:H$piecesCalcERow)");
                            $objSheet->SetCellValue("J$piecesPriceCalcRow", "=SUM(J$priceCalcSRow:J$priceCalcERow)");

                            $objSheet->getStyle("J2:J$calcRow")
                                ->getNumberFormat()
                                ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        }

                        //END SUMMARY DATA

                    } else if ($getFarmDetails[0]->unit_of_purchase == 1 || $getFarmDetails[0]->unit_of_purchase == 2) {

                        $rowCount = 10;

                        $objSheet->SetCellValue('A' . $rowCount, $this->lang->line('scanned_code'));
                        $objSheet->SetCellValue('B' . $rowCount, $this->lang->line('length') . ' ' . $this->lang->line('feet'));
                        $objSheet->SetCellValue('C' . $rowCount, $this->lang->line('width') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('D' . $rowCount, $this->lang->line('thickness') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('E' . $rowCount, $this->lang->line('volume_pie'));
                        $objSheet->SetCellValue('F' . $rowCount, $this->lang->line('gross_volume'));
                        $objSheet->SetCellValue('G' . $rowCount, $this->lang->line('length_export') . ' ' . $this->lang->line('meters'));
                        $objSheet->SetCellValue('H' . $rowCount, $this->lang->line('width_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('I' . $rowCount, $this->lang->line('thickness_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('J' . $rowCount, $this->lang->line('grade'));
                        $objSheet->SetCellValue('K' . $rowCount, $this->lang->line('net_volume'));
                        $objSheet->SetCellValue('L' . $rowCount, $this->lang->line('total_value'));

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:L$rowCount");

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension('A')->setAutoSize(true);
                        $objSheet->getColumnDimension('B')->setAutoSize(true);
                        $objSheet->getColumnDimension('C')->setAutoSize(true);
                        $objSheet->getColumnDimension('D')->setAutoSize(true);
                        $objSheet->getColumnDimension('E')->setAutoSize(true);
                        $objSheet->getColumnDimension('F')->setAutoSize(true);
                        $objSheet->getColumnDimension('G')->setAutoSize(true);
                        $objSheet->getColumnDimension('H')->setAutoSize(true);
                        $objSheet->getColumnDimension('I')->setAutoSize(true);
                        $objSheet->getColumnDimension('J')->setAutoSize(true);
                        $objSheet->getColumnDimension('K')->setAutoSize(true);
                        $objSheet->getColumnDimension('L')->setAutoSize(true);

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $objSheet->getStyle("A$rowCount:L$rowCount")->applyFromArray($styleArray);

                        $getContractPrice = $getFarmDataSummary = $this->Farm_model->get_farm_data_summary($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        $grade1Price = 0;
                        $grade2Price = 0;
                        $grade3Price = 0;
                        if (count($getContractPrice) == 1) {
                            $grade1Price = $getContractPrice[0]->minrange_grade1;
                            $grade2Price = $getContractPrice[0]->maxrange_grade2;
                            $grade3Price = $getContractPrice[0]->pricerange_grade3;
                        }

                        $objSheet->mergeCells('I2:I3');
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_pie"));
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_cbm"));
                        }

                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $objSheet->SetCellValue('I4', $grade1Price);
                        $objSheet->SetCellValue('I5', $grade2Price);
                        $objSheet->SetCellValue('I6', $grade3Price);

                        $objSheet->getStyle('I3:I6')
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);

                        $objSheet->getStyle('I2:I6')->applyFromArray($styleArray);

                        $rowCountData = 11;
                        foreach ($getFarmDataDetails as $farmdata) {
                            $objSheet->SetCellValue('A' . $rowCountData, $farmdata->scanned_code);
                            $objSheet->getStyle('A' . $rowCountData)
                                ->getNumberFormat()
                                ->setFormatCode('0');

                            $objSheet->SetCellValue('B' . $rowCountData, ($farmdata->length + 0));
                            $objSheet->SetCellValue('C' . $rowCountData, ($farmdata->width + 0));
                            $objSheet->SetCellValue('D' . $rowCountData, ($farmdata->thickness + 0));

                            $strFormula = "";
                            foreach ($getFormulae as $formula) {
                                $strFormula = str_replace(
                                    array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                    array(
                                        'B' . $rowCountData, 'C' . $rowCountData, 'D' . $rowCountData,
                                        'E' . $rowCountData, 'TRUNC', 'H' . $rowCountData, 'I' . $rowCountData,
                                        'G' . $rowCountData
                                    ),
                                    $formula->formula_context
                                );


                                if ($formula->type == "volumepie") {
                                    $objSheet->SetCellValue('E' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "grossvolume") {
                                    $objSheet->SetCellValue('F' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "lengthexport") {
                                    $objSheet->SetCellValue('G' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "widthexport") {
                                    $objSheet->SetCellValue('H' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "thicknessexport") {
                                    $objSheet->SetCellValue('I' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "netvolume") {
                                    $objSheet->SetCellValue('K' . $rowCountData, "=$strFormula");
                                }

                                if ($farmdata->grade_id == 1) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade1'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I4");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I4");
                                    }
                                } else if ($farmdata->grade_id == 2) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade2'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I5");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I5");
                                    }
                                } else {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade3'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I6");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I6");
                                    }
                                }

                                $objSheet->getStyle('L' . $rowCountData)
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                            }

                            $objSheet->getStyle("A$rowCountData:L$rowCountData")->applyFromArray($styleArray);

                            $rowCountData++;
                        }

                        $objSheet->getStyle("L9")->applyFromArray($styleArray);
                        $objSheet->SetCellValue('L9', "=SUM(L11:L$rowCountData)");

                        $objSheet->SetCellValue('K2', $this->lang->line('total_payment'));
                        $objSheet->SetCellValue('K3', $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue('K4', $this->lang->line('reteica'));
                        $objSheet->SetCellValue('K5', $this->lang->line('retention'));
                        $objSheet->SetCellValue('K6', $this->lang->line('iva'));
                        $objSheet->SetCellValue('K7', $this->lang->line('service_cost'));
                        $objSheet->SetCellValue('K8', $this->lang->line('adjustment'));

                        // WOOD VALUE WITH TAXES

                        $getSupplierTaxes = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->supplier_id);

                        $ivaFormula = "";
                        $retencionFormula = "";
                        $reteicaFormula = "";
                        if (count($getSupplierTaxes) == 1) {
                            if ($getSupplierTaxes[0]->is_iva_enabled == 1) {
                                $ivaFormula = $ivaFormula . "(L9*" . ($getSupplierTaxes[0]->iva_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_retencion_enabled == 1) {
                                $retencionFormula = $retencionFormula . "(L9*" . ($getSupplierTaxes[0]->retencion_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_reteica_enabled == 1) {
                                $reteicaFormula = $reteicaFormula . "(L9*" . ($getSupplierTaxes[0]->reteica_value + 0) . ")";
                            }
                        }

                        // END WOOD VALUE WITH TAXES

                        // LOGISTICS WITH TAXES

                        if ($getFarmDetails[0]->logistic_cost > 0 && $getFarmDetails[0]->pay_logistics_to > 0) {

                            $objSheet->SetCellValue('L3', ($getFarmDetails[0]->logistic_cost + 0));

                            $getTransportorTaxes_Logistics = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_logistics_to);
                            $getTransportorTaxes_Logistics_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                            if (count($getTransportorTaxes_Logistics) == 1) {
                                if ($getTransportorTaxes_Logistics[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else if ($getFarmDetails[0]->pay_logistics_to == $getFarmDetails[0]->supplier_id) {

                                $getTransportorTaxes_Logistics = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                                if (count($getTransportorTaxes_Logistics) == 1) {

                                    if ($getTransportorTaxes_Logistics[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END LOGISTICS WITH TAXES

                        // SERVICES WITH TAXES

                        if ($getFarmDetails[0]->service_cost > 0 && $getFarmDetails[0]->pay_service_to > 0) {

                            $objSheet->SetCellValue('L7', ($getFarmDetails[0]->service_cost + 0));

                            $getTransportorTaxes_Service = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_service_to);
                            $getTransportorTaxes_Service_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                            if (count($getTransportorTaxes_Service) == 1) {

                                if ($getTransportorTaxes_Service[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else {

                                $getTransportorTaxes_Service = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                                if (count($getTransportorTaxes_Service) == 1) {

                                    if ($getTransportorTaxes_Service[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END SERVICES WITH TAXES

                        if ($getFarmDetails[0]->adjustment > 0) {
                            $objSheet->SetCellValue('L8', $getFarmDetails[0]->adjustment);
                        }

                        $objSheet->SetCellValue('L2', "=SUM(L3:L9)");

                        if ($ivaFormula != "") {
                            $objSheet->SetCellValue("L6", "=$ivaFormula");
                        }

                        if ($retencionFormula != "") {
                            $objSheet->SetCellValue("L5", "=$retencionFormula");
                        }

                        if ($reteicaFormula != "") {
                            $objSheet->SetCellValue("L4", "=$reteicaFormula");
                        }

                        $objSheet->getStyle("K2:L8")->applyFromArray($styleArray);

                        $objSheet->getStyle("K4:L5")
                            ->getFont()
                            ->getColor()
                            ->setRGB('FF0000');

                        $objSheet->mergeCells('F2:H2');
                        $objSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                        $objSheet->getStyle('F2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('G3', $this->lang->line("volume"));
                        $objSheet->SetCellValue('H3', $this->lang->line("ages"));
                        $objSheet->getStyle('G3:H3')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('F4', $this->lang->line("grade1"));
                        $objSheet->SetCellValue('F5', $this->lang->line("grade2"));
                        $objSheet->SetCellValue('F6', $this->lang->line("grade3"));
                        $objSheet->SetCellValue('F7', $this->lang->line("total"));

                        $objSheet->SetCellValue('G4', "=SUMIF(J11:J$rowCountData,F4,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G5', "=SUMIF(J11:J$rowCountData,F5,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G6', "=SUMIF(J11:J$rowCountData,F6,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G7', "=SUM(G4:G6)");

                        $objSheet->getStyle('H4:H7')
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        $objSheet->SetCellValue('H4', "=G4/G7");
                        $objSheet->SetCellValue('H5', "=G5/G7");
                        $objSheet->SetCellValue('H6', "=G6/G7");
                        $objSheet->SetCellValue('H7', "=SUM(H4:H6)");

                        $objSheet->getStyle('G7:H7')
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('F2:H7')->applyFromArray($styleArray);

                        $objSheet->SetCellValue('D2', "=COUNT(A11:A$rowCountData)");
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('D3', "=SUM(E11:E$rowCountData)");
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('D3', "=SUM(K11:K$rowCountData)");
                        }


                        $objSheet->getStyle("L2:L9")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'FarmReport_' . $inventoryOrder . '_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/FarmReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/FarmReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line('no_data_reports');
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
        } catch (Exception $e) {
            $Return['error'] = $e->getMessage(); // $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_supplier_receipt($farmId, $contractId, $inventoryOrder)
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getFarmDetails = $this->Farm_model->get_farm_details($farmId, $contractId, $inventoryOrder);
                $getFarmDataDetails = $this->Farm_model->get_farm_data_details($farmId, $contractId, $inventoryOrder);
                $return_arr_farmdata = array();
                $summaryHeaderLastRow = 0;

                if (count($getFarmDetails) == 1 && count($getFarmDataDetails) > 0) {

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($inventoryOrder);
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);


                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->SetCellValue('A3', $this->lang->line('inventory_order'));
                    $objSheet->SetCellValue('A4', $this->lang->line('supplier_name'));

                    $objSheet->SetCellValue('B3', $getFarmDetails[0]->inventory_order);
                    $objSheet->SetCellValue('B4', $getFarmDetails[0]->supplier_name);

                    $objSheet->getStyle("A3:A4")
                        ->getFont()
                        ->setBold(true);


                    

                    $objSheet->getStyle('A3:B4')->applyFromArray($styleArray);


                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin_report($getFarmDetails[0]->origin_id);

                    $getFormulae = $this->Master_model->get_formulae_by_purchase_unit($getFarmDetails[0]->unit_of_purchase, $getFarmDetails[0]->origin_id);

                    if (
                        $getFarmDetails[0]->unit_of_purchase == 3 || $getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5
                        || $getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7
                        || $getFarmDetails[0]->unit_of_purchase == 8 || $getFarmDetails[0]->unit_of_purchase == 9
                    ) {

                        if (count($getSupplierTaxes) >= 3) {
                            $rowCount = 2;
                        } else {
                            $rowCount = 6;
                        }
                        $totalStartRow = $rowCount;

                        $taxCellsArray = array();

                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('total_payment'));
                        $totalPaymentRow = $rowCount;
                        $rowCount++;

                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue("E$rowCount", $getFarmDetails[0]->logistic_cost);
                        $objSheet->getStyle("E$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $logisticCostRow = "$rowCount";
                        $rowCount++;

                        foreach ($getSupplierTaxes as $suppliertax) {

                            if ($suppliertax->number_format == 2) {
                                $suppliertax->tax_name = $suppliertax->tax_name . " (%)";
                            }
                            $objSheet->SetCellValue("D$rowCount", $suppliertax->tax_name);

                            if ($suppliertax->arithmetic_type == 2) {
                                $objSheet->getStyle("D$rowCount")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB("FF0000");
                            }

                            $supplierTaxesArr = json_decode($getFarmDetails[0]->supplier_taxes_array, true);
                            $logisticsTaxesArray = json_decode($getFarmDetails[0]->logistics_taxes_array, true);
                            $serviceTaxesArray = json_decode($getFarmDetails[0]->service_taxes_array, true);

                            if (count($supplierTaxesArr) > 0) {
                                $formula = "";

                                foreach ($supplierTaxesArr as $tax) {

                                    if ($tax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $taxval = $tax['taxVal'] * -1;
                                        } else {
                                            $taxval = $tax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E$$$*$taxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(E$$$*$taxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E$$$*$taxval)";
                                            } else {
                                                $formula = $formula . "+SUM(E$$$*$taxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "E$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($logisticsTaxesArray as $logistictax) {

                                    if ($logistictax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $ltaxval = $logistictax['taxVal'] * -1;
                                        } else {
                                            $ltaxval = $logistictax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E###*$ltaxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(E###*$ltaxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E###*$ltaxval)";
                                            } else {
                                                $formula = $formula . "+SUM(E###*$ltaxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "E$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }

                                foreach ($serviceTaxesArray as $servicetax) {

                                    if ($servicetax["taxId"] == $suppliertax->id) {
                                        if ($suppliertax->arithmetic_type == 2) {
                                            $staxval = $servicetax['taxVal'] * -1;
                                        } else {
                                            $staxval = $servicetax['taxVal'];
                                        }
                                        if ($suppliertax->number_format == 2) {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E&&&*$staxval%)";
                                            } else {
                                                $formula = $formula . "+SUM(E&&&*$staxval%)";
                                            }
                                        } else {
                                            if ($formula == "") {
                                                $formula = $formula . "=SUM(E&&&*$staxval)";
                                            } else {
                                                $formula = $formula . "+SUM(e&&&*$staxval)";
                                            }
                                        }

                                        $taxCellsArray[] = array(
                                            "rowCell" => "E$rowCount",
                                            "formula" => $formula,
                                        );
                                    }
                                }
                            }

                            $rowCount++;
                        }

                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('service_cost'));
                        $objSheet->SetCellValue("E$rowCount", $getFarmDetails[0]->service_cost);
                        $objSheet->getStyle("E$rowCount")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        $serviceCostRow = "$rowCount";
                        $rowCount++;

                        $objSheet->SetCellValue("D$rowCount", $this->lang->line('adjustment'));
                        $rowCount++;

                        $objSheet->getStyle("D$totalStartRow:E$rowCount")->applyFromArray($styleArray);

                        $calcRow = $rowCount;

                        $totalPaymentRow1 = $totalPaymentRow + 1;
                        $objSheet->SetCellValue("E$totalPaymentRow", "=SUM(E$totalPaymentRow1:e$calcRow)");

                        if (count($taxCellsArray) > 0) {
                            foreach ($taxCellsArray as $taxcell) {
                                $taxCells = $taxcell["rowCell"];
                                $objSheet->SetCellValue("$taxCells", str_replace(
                                    array("$$$", "###", "&&&"),
                                    array("$calcRow", "$logisticCostRow", "$serviceCostRow"),
                                    $taxcell["formula"]
                                ));
                            }
                        }

                        if ($rowCount <= 6) {
                            $rowCount = 10;
                        }

                        $grossVolumeTotal = 0;

                        foreach ($getFarmDataDetails as $farmdata) {

                            $strFormula = "";
                            $strFormulaGross = "";

                            foreach ($getFormulae as $formula) {
                                if ($formula->type == "netvolume") {

                                    $strFormula = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, $farmdata->length, $farmdata->circumference, '$this->truncate'),
                                        $formula->formula_context
                                    );

                                    $strFormula = "return (" . $strFormula . ");";
                                }

                                if ($formula->type == "grossvolume") {

                                    $strFormulaGross = str_replace(
                                        array('$ac', '$al', '$l', '$c', 'truncate'),
                                        array($getFarmDetails[0]->purchase_allowance, $getFarmDetails[0]->purchase_allowance_length, $farmdata->length, $farmdata->circumference, '$this->truncate'),
                                        $formula->formula_context
                                    );

                                    $strFormulaGross = "return (" . $strFormulaGross . ");";
                                }
                            }


                            $row_array_farmdata['circumference'] = round($farmdata->circumference, 2);
                            if ($strFormula != "") {
                                $row_array_farmdata['netvolume'] = (eval($strFormula) * $farmdata->no_of_pieces);
                            } else {
                                $row_array_farmdata['netvolume'] = 0;
                            }

                            if ($strFormulaGross != "") {
                                $grossVolumeTotal = $grossVolumeTotal + ((eval($strFormulaGross) * $farmdata->no_of_pieces));
                            } else {
                                $grossVolumeTotal = $grossVolumeTotal + 0;
                            }
                            array_push($return_arr_farmdata, $row_array_farmdata);
                        }

                        //SUMMARY DATA

                        $getFarmDataSummary = $this->Farm_model->get_farm_data_summary($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        if (count($getFarmDataSummary) > 0) {

                            $rowCountSummary = $rowCount;
                            $piecesPriceCalcRow = $rowCountSummary;

                            $objSheet->getStyle("C$rowCountSummary")->applyFromArray($styleArray);
                            $objSheet->getStyle("E$rowCountSummary")->applyFromArray($styleArray);
                            $rowCountSummary++;

                            $objSheet->SetCellValue("A$rowCountSummary", $this->lang->line('circumference_range'));
                            $objSheet->mergeCells("A$rowCountSummary:B$rowCountSummary");

                            if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                $objSheet->SetCellValue("C$rowCountSummary", $this->lang->line('volume'));
                                $objSheet->SetCellValue("D$rowCountSummary", $this->lang->line('value_volume'));
                            } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                $objSheet->SetCellValue("C$rowCountSummary", $this->lang->line('volume'));
                                $objSheet->SetCellValue("D$rowCountSummary", $this->lang->line('value_volume'));
                            } else {
                                $objSheet->SetCellValue("C$rowCountSummary", $this->lang->line('pieces'));
                                $objSheet->SetCellValue("D$rowCountSummary", $this->lang->line('value_piece'));
                            }
                            $objSheet->SetCellValue("E$rowCountSummary", $this->lang->line('total_value'));

                            $objSheet->getStyle("A$rowCountSummary:E$rowCountSummary")
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('DBEDFF');

                            $objSheet->getStyle("A$rowCountSummary:E$rowCountSummary")
                                ->getFont()
                                ->setBold(true);

                            $objSheet->getStyle("A$rowCountSummary:E$rowCountSummary")
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objSheet->getStyle("A$rowCountSummary:E$rowCountSummary")->applyFromArray($styleArray);

                            $rowCountDataSummary = $rowCountSummary;
                            $piecesCalcSRow = $rowCountDataSummary + 1;
                            $priceCalcSRow = $rowCountDataSummary + 1;
                            $piecesCalcERow = $rowCountDataSummary;
                            $priceCalcERow = $rowCountDataSummary;
                            $summaryPieces = 0;
                            $priceRange = 0;

                            foreach ($getFarmDataSummary as $farmdatasummary) {

                                $rowCountDataSummary++;

                                $piecesCalcERow = $rowCountDataSummary;
                                $priceCalcERow = $rowCountDataSummary;

                                $totalVolumePrice = 0;

                                $objSheet->SetCellValue("A$rowCountDataSummary", ($farmdatasummary->minrange_grade1 + 0));
                                $objSheet->SetCellValue("B$rowCountDataSummary", ($farmdatasummary->maxrange_grade2 + 0));

                                if (count($return_arr_farmdata) > 0) {
                                    foreach ($return_arr_farmdata as $key => $value) {
                                        $circVal = $value["circumference"];
                                        $netVolumeVal = $value["netvolume"];

                                        if ($circVal >= $farmdatasummary->minrange_grade1 && $circVal <= $farmdatasummary->maxrange_grade2) {
                                            $totalVolumePrice = $totalVolumePrice + $netVolumeVal;
                                        }
                                    }

                                    $summaryPieces = $summaryPieces + $totalVolumePrice;
                                    if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                        $objSheet->SetCellValue("C$rowCountDataSummary", $totalVolumePrice);
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                        $objSheet->SetCellValue("C$rowCountDataSummary", "=IF(" . '$D$' . "$summaryHeaderLastRow" . ">=F$rowCountDataSummary,IF(" . '$D$' . "$summaryHeaderLastRow" . "<=G$rowCountDataSummary," . '$D$' . "3,0),0)");
                                    } else {
                                        $objSheet->SetCellValue("C$rowCountDataSummary", $farmdatasummary->pieces_farm);
                                    }

                                    if ($getFarmDetails[0]->exchange_rate > 0) {
                                        $priceRange = $farmdatasummary->pricerange_grade3 * $getFarmDetails[0]->exchange_rate;
                                    } else {
                                        $priceRange = $farmdatasummary->pricerange_grade3;
                                    }
                                }

                                $objSheet->SetCellValue("D$rowCountDataSummary", ($priceRange + 0));
                                if ($getFarmDetails[0]->unit_of_purchase == 4 || $getFarmDetails[0]->unit_of_purchase == 5) {
                                    $objSheet->SetCellValue("E$rowCountDataSummary", ($priceRange * $totalVolumePrice + 0));
                                } else if ($getFarmDetails[0]->unit_of_purchase == 6 || $getFarmDetails[0]->unit_of_purchase == 7) {
                                    $objSheet->SetCellValue("E$rowCountDataSummary", "=C$rowCountDataSummary*D$rowCountDataSummary");
                                } else {
                                    $objSheet->SetCellValue("E$rowCountDataSummary", ($priceRange * $farmdatasummary->pieces_farm + 0));
                                }

                                $objSheet->getStyle("D$rowCountDataSummary:E$rowCountDataSummary")
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                                $objSheet->getStyle("A$rowCountDataSummary:E$rowCountDataSummary")->applyFromArray($styleArray);
                            }

                            // $objSheet->getColumnDimension('A')->setAutoSize(false);
                            // $objSheet->getColumnDimension('A')->setWidth('15');
                            // $objSheet->getColumnDimension('B')->setAutoSize(false);
                            // $objSheet->getColumnDimension('B')->setWidth('15');

                            $objSheet->getColumnDimension('A')->setAutoSize(true);
                            $objSheet->getColumnDimension('B')->setAutoSize(true);
                            $objSheet->getColumnDimension('C')->setAutoSize(false);
                            $objSheet->getColumnDimension('C')->setWidth('15');
                            $objSheet->getColumnDimension('D')->setAutoSize(false);
                            $objSheet->getColumnDimension('D')->setWidth('15');
                            $objSheet->getColumnDimension('E')->setAutoSize(false);
                            $objSheet->getColumnDimension('E')->setWidth('18');

                            $objSheet->SetCellValue("C$piecesPriceCalcRow", "=SUM(C$piecesCalcSRow:C$piecesCalcERow)");
                            $objSheet->SetCellValue("E$piecesPriceCalcRow", "=SUM(E$priceCalcSRow:E$priceCalcERow)");

                            $objSheet->getStyle("E2:E$calcRow")
                                ->getNumberFormat()
                                ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                        }

                        //END SUMMARY DATA

                    } else if ($getFarmDetails[0]->unit_of_purchase == 1 || $getFarmDetails[0]->unit_of_purchase == 2) {

                        $rowCount = 10;

                        $objSheet->SetCellValue('A' . $rowCount, $this->lang->line('scanned_code'));
                        $objSheet->SetCellValue('B' . $rowCount, $this->lang->line('length') . ' ' . $this->lang->line('feet'));
                        $objSheet->SetCellValue('C' . $rowCount, $this->lang->line('width') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('D' . $rowCount, $this->lang->line('thickness') . ' ' . $this->lang->line('inch'));
                        $objSheet->SetCellValue('E' . $rowCount, $this->lang->line('volume_pie'));
                        $objSheet->SetCellValue('F' . $rowCount, $this->lang->line('gross_volume'));
                        $objSheet->SetCellValue('G' . $rowCount, $this->lang->line('length_export') . ' ' . $this->lang->line('meters'));
                        $objSheet->SetCellValue('H' . $rowCount, $this->lang->line('width_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('I' . $rowCount, $this->lang->line('thickness_export') . ' ' . $this->lang->line('centimeters'));
                        $objSheet->SetCellValue('J' . $rowCount, $this->lang->line('grade'));
                        $objSheet->SetCellValue('K' . $rowCount, $this->lang->line('net_volume'));
                        $objSheet->SetCellValue('L' . $rowCount, $this->lang->line('total_value'));

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFont()
                            ->setBold(true);

                        $objSheet->setAutoFilter("A$rowCount:L$rowCount");

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->getColumnDimension('A')->setAutoSize(true);
                        $objSheet->getColumnDimension('B')->setAutoSize(true);
                        $objSheet->getColumnDimension('C')->setAutoSize(true);
                        $objSheet->getColumnDimension('D')->setAutoSize(true);
                        $objSheet->getColumnDimension('E')->setAutoSize(true);
                        $objSheet->getColumnDimension('F')->setAutoSize(true);
                        $objSheet->getColumnDimension('G')->setAutoSize(true);
                        $objSheet->getColumnDimension('H')->setAutoSize(true);
                        $objSheet->getColumnDimension('I')->setAutoSize(true);
                        $objSheet->getColumnDimension('J')->setAutoSize(true);
                        $objSheet->getColumnDimension('K')->setAutoSize(true);
                        $objSheet->getColumnDimension('L')->setAutoSize(true);

                        $objSheet->getStyle("A$rowCount:L$rowCount")
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('add8e6');

                        $objSheet->getStyle("A$rowCount:L$rowCount")->applyFromArray($styleArray);

                        $getContractPrice = $getFarmDataSummary = $this->Farm_model->get_farm_data_summary($farmId, $contractId, $inventoryOrder, $getFarmDetails[0]->product_type_id);

                        $grade1Price = 0;
                        $grade2Price = 0;
                        $grade3Price = 0;
                        if (count($getContractPrice) == 1) {
                            $grade1Price = $getContractPrice[0]->minrange_grade1;
                            $grade2Price = $getContractPrice[0]->maxrange_grade2;
                            $grade3Price = $getContractPrice[0]->pricerange_grade3;
                        }

                        $objSheet->mergeCells('I2:I3');
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_pie"));
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('I2', $this->lang->line("price_per_cbm"));
                        }

                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle('I2')
                            ->getAlignment()
                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $objSheet->SetCellValue('I4', $grade1Price);
                        $objSheet->SetCellValue('I5', $grade2Price);
                        $objSheet->SetCellValue('I6', $grade3Price);

                        $objSheet->getStyle('I3:I6')
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);

                        $objSheet->getStyle('I2:I6')->applyFromArray($styleArray);

                        $rowCountData = 11;
                        foreach ($getFarmDataDetails as $farmdata) {
                            $objSheet->SetCellValue('A' . $rowCountData, $farmdata->scanned_code);
                            $objSheet->getStyle('A' . $rowCountData)
                                ->getNumberFormat()
                                ->setFormatCode('0');

                            $objSheet->SetCellValue('B' . $rowCountData, ($farmdata->length + 0));
                            $objSheet->SetCellValue('C' . $rowCountData, ($farmdata->width + 0));
                            $objSheet->SetCellValue('D' . $rowCountData, ($farmdata->thickness + 0));

                            $strFormula = "";
                            foreach ($getFormulae as $formula) {
                                $strFormula = str_replace(
                                    array('$l', '$w', '$t', '$vp', 'truncate', '$ew', '$et', '$el'),
                                    array(
                                        'B' . $rowCountData, 'C' . $rowCountData, 'D' . $rowCountData,
                                        'E' . $rowCountData, 'TRUNC', 'H' . $rowCountData, 'I' . $rowCountData,
                                        'G' . $rowCountData
                                    ),
                                    $formula->formula_context
                                );


                                if ($formula->type == "volumepie") {
                                    $objSheet->SetCellValue('E' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "grossvolume") {
                                    $objSheet->SetCellValue('F' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "lengthexport") {
                                    $objSheet->SetCellValue('G' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "widthexport") {
                                    $objSheet->SetCellValue('H' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "thicknessexport") {
                                    $objSheet->SetCellValue('I' . $rowCountData, "=$strFormula");
                                }

                                if ($formula->type == "netvolume") {
                                    $objSheet->SetCellValue('K' . $rowCountData, "=$strFormula");
                                }

                                if ($farmdata->grade_id == 1) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade1'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I4");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I4");
                                    }
                                } else if ($farmdata->grade_id == 2) {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade2'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I5");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I5");
                                    }
                                } else {
                                    $objSheet->SetCellValue('J' . $rowCountData, $this->lang->line('grade3'));

                                    if ($getFarmDetails[0]->unit_of_purchase == 1) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=E$rowCountData*I6");
                                    } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                                        $objSheet->SetCellValue('L' . $rowCountData, "=K$rowCountData*I6");
                                    }
                                }

                                $objSheet->getStyle('L' . $rowCountData)
                                    ->getNumberFormat()
                                    ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                            }

                            $objSheet->getStyle("A$rowCountData:L$rowCountData")->applyFromArray($styleArray);

                            $rowCountData++;
                        }

                        $objSheet->getStyle("L9")->applyFromArray($styleArray);
                        $objSheet->SetCellValue('L9', "=SUM(L11:L$rowCountData)");

                        $objSheet->SetCellValue('K2', $this->lang->line('total_payment'));
                        $objSheet->SetCellValue('K3', $this->lang->line('logistic_cost'));
                        $objSheet->SetCellValue('K4', $this->lang->line('reteica'));
                        $objSheet->SetCellValue('K5', $this->lang->line('retention'));
                        $objSheet->SetCellValue('K6', $this->lang->line('iva'));
                        $objSheet->SetCellValue('K7', $this->lang->line('service_cost'));
                        $objSheet->SetCellValue('K8', $this->lang->line('adjustment'));

                        // WOOD VALUE WITH TAXES

                        $getSupplierTaxes = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->supplier_id);

                        $ivaFormula = "";
                        $retencionFormula = "";
                        $reteicaFormula = "";
                        if (count($getSupplierTaxes) == 1) {
                            if ($getSupplierTaxes[0]->is_iva_enabled == 1) {
                                $ivaFormula = $ivaFormula . "(L9*" . ($getSupplierTaxes[0]->iva_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_retencion_enabled == 1) {
                                $retencionFormula = $retencionFormula . "(L9*" . ($getSupplierTaxes[0]->retencion_value + 0) . "%)";
                            }

                            if ($getSupplierTaxes[0]->is_reteica_enabled == 1) {
                                $reteicaFormula = $reteicaFormula . "(L9*" . ($getSupplierTaxes[0]->reteica_value + 0) . ")";
                            }
                        }

                        // END WOOD VALUE WITH TAXES

                        // LOGISTICS WITH TAXES

                        if ($getFarmDetails[0]->logistic_cost > 0 && $getFarmDetails[0]->pay_logistics_to > 0) {

                            $objSheet->SetCellValue('L3', ($getFarmDetails[0]->logistic_cost + 0));

                            $getTransportorTaxes_Logistics = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_logistics_to);
                            $getTransportorTaxes_Logistics_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                            if (count($getTransportorTaxes_Logistics) == 1) {
                                if ($getTransportorTaxes_Logistics[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Logistics[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Logistics_Supplier) == 1 && $getTransportorTaxes_Logistics_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else if ($getFarmDetails[0]->pay_logistics_to == $getFarmDetails[0]->supplier_id) {

                                $getTransportorTaxes_Logistics = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_logistics_to);

                                if (count($getTransportorTaxes_Logistics) == 1) {

                                    if ($getTransportorTaxes_Logistics[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->iva_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->retencion_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Logistics[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L3*" . ($getTransportorTaxes_Logistics[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END LOGISTICS WITH TAXES

                        // SERVICES WITH TAXES

                        if ($getFarmDetails[0]->service_cost > 0 && $getFarmDetails[0]->pay_service_to > 0) {

                            $objSheet->SetCellValue('L7', ($getFarmDetails[0]->service_cost + 0));

                            $getTransportorTaxes_Service = $this->Farm_model->get_transportor_taxes($getFarmDetails[0]->pay_service_to);
                            $getTransportorTaxes_Service_Supplier = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                            if (count($getTransportorTaxes_Service) == 1) {

                                if ($getTransportorTaxes_Service[0]->is_iva_provider_enabled == 1) {
                                    if ($ivaFormula != "") {
                                        $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    } else {
                                        $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->iva_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_retencion_provider_enabled == 1) {
                                    if ($retencionFormula != "") {
                                        $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    } else {
                                        $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->retencion_value + 0) . "%)";
                                        }
                                    }
                                }

                                if ($getTransportorTaxes_Service[0]->is_reteica_provider_enabled == 1) {
                                    if ($reteicaFormula != "") {
                                        $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    } else {
                                        $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                    }
                                } else {
                                    if (count($getTransportorTaxes_Service_Supplier) == 1 && $getTransportorTaxes_Service_Supplier[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service_Supplier[0]->reteica_value + 0) . ")";
                                        }
                                    }
                                }
                            } else {

                                $getTransportorTaxes_Service = $this->Farm_model->get_supplier_taxes($getFarmDetails[0]->pay_service_to);

                                if (count($getTransportorTaxes_Service) == 1) {

                                    if ($getTransportorTaxes_Service[0]->is_iva_enabled == 1) {
                                        if ($ivaFormula != "") {
                                            $ivaFormula = $ivaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        } else {
                                            $ivaFormula = $ivaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->iva_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_retencion_enabled == 1) {
                                        if ($retencionFormula != "") {
                                            $retencionFormula = $retencionFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        } else {
                                            $retencionFormula = $retencionFormula . "(L7*" . ($getTransportorTaxes_Service[0]->retencion_provider_value + 0) . "%)";
                                        }
                                    }

                                    if ($getTransportorTaxes_Service[0]->is_reteica_enabled == 1) {
                                        if ($reteicaFormula != "") {
                                            $reteicaFormula = $reteicaFormula . "+(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        } else {
                                            $reteicaFormula = $reteicaFormula . "(L7*" . ($getTransportorTaxes_Service[0]->reteica_provider_value + 0) . ")";
                                        }
                                    }
                                }
                            }
                        }

                        // END SERVICES WITH TAXES

                        if ($getFarmDetails[0]->adjustment > 0) {
                            $objSheet->SetCellValue('L8', $getFarmDetails[0]->adjustment);
                        }

                        $objSheet->SetCellValue('L2', "=SUM(L3:L9)");

                        if ($ivaFormula != "") {
                            $objSheet->SetCellValue("L6", "=$ivaFormula");
                        }

                        if ($retencionFormula != "") {
                            $objSheet->SetCellValue("L5", "=$retencionFormula");
                        }

                        if ($reteicaFormula != "") {
                            $objSheet->SetCellValue("L4", "=$reteicaFormula");
                        }

                        $objSheet->getStyle("K2:L8")->applyFromArray($styleArray);

                        $objSheet->getStyle("K4:L5")
                            ->getFont()
                            ->getColor()
                            ->setRGB('FF0000');

                        $objSheet->mergeCells('F2:H2');
                        $objSheet->SetCellValue('F2', $this->lang->line("conpliance_report"));
                        $objSheet->getStyle('F2')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('G3', $this->lang->line("volume"));
                        $objSheet->SetCellValue('H3', $this->lang->line("ages"));
                        $objSheet->getStyle('G3:H3')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet->SetCellValue('F4', $this->lang->line("grade1"));
                        $objSheet->SetCellValue('F5', $this->lang->line("grade2"));
                        $objSheet->SetCellValue('F6', $this->lang->line("grade3"));
                        $objSheet->SetCellValue('F7', $this->lang->line("total"));

                        $objSheet->SetCellValue('G4', "=SUMIF(J11:J$rowCountData,F4,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G5', "=SUMIF(J11:J$rowCountData,F5,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G6', "=SUMIF(J11:J$rowCountData,F6,K11:K$rowCountData)");
                        $objSheet->SetCellValue('G7', "=SUM(G4:G6)");

                        $objSheet->getStyle('H4:H7')
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        $objSheet->SetCellValue('H4', "=G4/G7");
                        $objSheet->SetCellValue('H5', "=G5/G7");
                        $objSheet->SetCellValue('H6', "=G6/G7");
                        $objSheet->SetCellValue('H7', "=SUM(H4:H6)");

                        $objSheet->getStyle('G7:H7')
                            ->getFont()
                            ->setBold(true);

                        $objSheet->getStyle('F2:H7')->applyFromArray($styleArray);

                        $objSheet->SetCellValue('D2', "=COUNT(A11:A$rowCountData)");
                        if ($getFarmDetails[0]->unit_of_purchase == 1) {
                            $objSheet->SetCellValue('D3', "=SUM(E11:E$rowCountData)");
                        } else if ($getFarmDetails[0]->unit_of_purchase == 2) {
                            $objSheet->SetCellValue('D3', "=SUM(K11:K$rowCountData)");
                        }


                        $objSheet->getStyle("L2:L9")
                            ->getNumberFormat()
                            ->setFormatCode($getFarmDetails[0]->currency_excel_format);
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'FarmReport_' . $inventoryOrder . '_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/FarmReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/FarmReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return['error'] = $this->lang->line('no_data_reports');
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
        } catch (Exception $e) {
            $Return['error'] = $e->getMessage(); // $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function get_supplier_taxes_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();

        if (!empty($session)) {
            $result = "";
            if ($this->input->get("originid") > 0) {
                $getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin($this->input->get("originid"));
                foreach ($getSupplierTaxes as $suppliertax) {
                    $result = $result . "<option value='" . $suppliertax->id . "'>" . $suppliertax->tax_name . "</option>";
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

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/FarmReports/*.xlsx");
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
