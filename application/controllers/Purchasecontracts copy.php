<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Purchasecontracts extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_model");
        $this->load->model("Master_model");
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
        $data['title'] = $this->lang->line('contracts_title') . " - " . $this->lang->line('finance_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }
        // $data['breadcrumbs'] = $this->lang->line('xin_role_urole');
        $data['path_url'] = 'cgr_contracts';
        // $user = $this->Xin_model->read_employee_info($session['user_id']);
        //if($user[0]->user_role_id==1) {
        if (!empty($session)) {
            $data['subview'] = $this->load->view("purchasecontracts/contract_list", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
        //} else {
        //	redirect('admin/dashboard');
        //}
    }

    public function contract_list()
    {
        $data['title'] =  $this->lang->line('contracts_title') . " - " . $this->lang->line('finance_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            $this->load->view("purchasecontracts/contract_list", $data);
        } else {
            redirect("/logout");
        }

        $draw = intval($this->input->get("draw"));
        $originid = intval($this->input->get("originid"));

        if ($originid == 0) {
            $contracts = $this->Contract_model->all_contracts();
        } else {
            $contracts = $this->Contract_model->all_contracts_origin($originid);
        }

        $data = array();

        foreach ($contracts as $r) {
            $editContract = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcontract" data-toggle="modal" data-target=".edit-modal-data"  data-contract_id="' . $r->contract_id . '"><span class="fas fa-pencil"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewcontract" data-toggle="modal" data-target=".edit-modal-data"  data-contract_id="' . $r->contract_id . '"><span class="fas fa-eye"></span></button></span>';

            $contractType = $this->lang->line('warehouse');
            if ($r->contract_type == 2) {
                $contractType = $this->lang->line('field_purchase');
            }

            $measurmentSystem = $this->lang->line($r->purchase_unit);

            if ($r->is_expired == 1) {
                $status = $this->lang->line('expired');
            } else if ($r->is_active == 1) {
                $status = $this->lang->line('active');
            } else {
                $status = $this->lang->line('inactive');
            }
            $data[] = array(
                $editContract,
                $r->supplier_name,
                $r->contract_code,
                $contractType,
                $measurmentSystem,
                ($r->remaining_volume + 0),
                $r->origin,
                $status
            );
        }

        $output = array(
            "draw" => $draw,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function dialog_contract_add()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {
            if ($this->input->get('type') == "addcontract") {
                $data = array(
                    'pageheading' => $this->lang->line('add_contract'),
                    'pagetype' => "add",
                    'contractid' => 0,
                    'contractcode' => "",
                );
            } else if ($this->input->get('type') == "editcontract") {

                $getContractDetails = $this->Contract_model->get_contracts_by_contractid($this->input->get('cid'));
                $originId = $getContractDetails[0]->origin_id;
                $supplierId = $getContractDetails[0]->supplier_id;
                $productId = $getContractDetails[0]->product;
                $productTypeId = $getContractDetails[0]->product_type;

                if ($getContractDetails[0]->contract_type == 1) {
                    $getSuppliers =  $this->Contract_model->get_suppliers_by_origin($originId);
                    $getProducts = $this->Contract_model->get_supplier_product_byorigin($originId, $supplierId);
                    $getProductTypes = $this->Contract_model->get_supplier_product_type_byorigin($supplierId, $productId);
                } else if ($getContractDetails[0]->contract_type == 2) {
                    $getSuppliers =  $this->Contract_model->get_purchase_manager_by_origin($originId);
                    $getProducts = $this->Contract_model->get_product_by_origin($originId);
                    $getProductTypes = $this->Master_model->get_product_type();
                }

                $getPurchaseUnit = $this->Contract_model->get_purchase_unit($productTypeId);
                $getCurrencies = $this->Contract_model->get_currencies_by_origin($originId);
                $getPaymentMethods = $this->Contract_model->get_payment_methods();
                $getContractPrice = $this->Contract_model->get_purchase_contract_price_by_contractid($getContractDetails[0]->contract_id);

                $data = array(
                    'pageheading' => $this->lang->line('edit_contract'),
                    'pagetype' => "edit",
                    'contractid' => $getContractDetails[0]->contract_id,
                    'contractcode' => $getContractDetails[0]->contract_code,
                    'get_contract_details' => $getContractDetails,
                    'suppliers' => $getSuppliers,
                    'products' => $getProducts,
                    'product_types' => $getProductTypes,
                    'measurement_systems' => $getPurchaseUnit,
                    'currencies' => $getCurrencies,
                    'payment_methods' => $getPaymentMethods,
                    'contractprice' => $getContractPrice
                );
            }
            $this->load->view('purchasecontracts/dialog_add_contract', $data);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function fetch_supplier_purchasemanager()
    {

        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {

                if ($this->input->get('contracttype') == 1) {
                    $getSuppliers = $this->Contract_model->get_suppliers_by_origin($this->input->get('originid'));
                    foreach ($getSuppliers as $supplier) {
                        $result = $result . "<option value='" . $supplier->id . "'>" . $supplier->supplier_name . "</option>";
                    }
                } else if ($this->input->get('contracttype') == 2) {
                    $getPurchaseManager = $this->Contract_model->get_purchase_manager_by_origin($this->input->get('originid'));
                    foreach ($getPurchaseManager as $purchasemanager) {
                        $result = $result . "<option value='" . $purchasemanager->id . "'>" . $purchasemanager->supplier_name . "</option>";
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

    public function get_product_by_supplier_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                if ($this->input->get('supplierid') > 0) {

                    if ($this->input->get('contracttype') == 1) {

                        $getProducts = $this->Contract_model->get_supplier_product_byorigin($this->input->get('originid'), $this->input->get('supplierid'));
                        foreach ($getProducts as $products) {
                            $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
                        }
                    } else if ($this->input->get('contracttype') == 2) {

                        $getProducts = $this->Contract_model->get_product_by_origin($this->input->get('originid'));
                        foreach ($getProducts as $products) {
                            $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
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
                        if ($this->input->get('contracttype') == 1) {

                            $getProductTypes = $this->Contract_model->get_supplier_product_type_byorigin($this->input->get('supplierid'), $this->input->get('productid'));
                            foreach ($getProductTypes as $producttype) {
                                $productTypeName = $this->lang->line($producttype->product_type_name);
                                $result = $result . "<option value='" . $producttype->type_id . "'>" . $productTypeName . "</option>";
                            }
                        } else if ($this->input->get('contracttype') == 2) {

                            $getProductTypes = $this->Master_model->get_product_type();
                            foreach ($getProductTypes as $producttype) {
                                $productTypeName = $this->lang->line($producttype->product_type_name);
                                $result = $result . "<option value='" . $producttype->type_id . "'>" . $productTypeName . "</option>";
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

    public function get_measurement_system()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('producttypeid') > 0) {

                $getPurchaseUnit = $this->Contract_model->get_purchase_unit_origin($this->input->get('producttypeid'), $this->input->get('originid'));
                foreach ($getPurchaseUnit as $purchaseunit) {
                    $measurmentSystem = $this->lang->line($purchaseunit->purchase_unit);
                    $result = $result . "<option value='" . $purchaseunit->id . "'>" . $measurmentSystem . "</option>";
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

    public function get_currencies_by_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {

                $getCurrencies = $this->Contract_model->get_currencies_by_origin($this->input->get('originid'));
                foreach ($getCurrencies as $currency) {
                    $result = $result . "<option value='" . $currency->currency_id . "'>" . $currency->currency . "</option>";
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

    public function get_payment_methods()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getPaymentMethods = $this->Contract_model->get_payment_methods();
                foreach ($getPaymentMethods as $paymentmethod) {
                    $result = $result . "<option value='" . $paymentmethod->id . "'>" . $this->lang->line($paymentmethod->payment_name) . "</option>";
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
        if ($this->input->post('add_type') == 'contract') {

            if (!empty($session)) {

                $origin = $this->input->post('origin');
                $contracttype = $this->input->post('contracttype');
                $suppliername = $this->input->post('suppliername');
                $productname = $this->input->post('productname');
                $producttype = $this->input->post('producttype');
                $measuremetsystem = $this->input->post('measuremetsystem');
                $circumferenceallowance = $this->input->post('circumferenceallowance');
                $lengthallowance = $this->input->post('lengthallowance');
                $contractcurrency = $this->input->post('contractcurrency');
                $paymentmethod = $this->input->post('paymentmethod');
                $startdate = $this->input->post('startdate');
                $enddate = $this->input->post('enddate');
                $totalvolume = $this->input->post('totalvolume');
                $contractstatus = $this->input->post('contractstatus');
                $pricearray = $this->input->post('pricearray');

                if ($this->input->post('page_type') == 'add') {

                    $lastContractCode = $this->Contract_model->get_last_contract_code($origin);
                    if($lastContractCode[0]->tid == 0) {
                        $lastContractCode[0]->tid = 1;
                    }
                    $getCountryCode = $this->Master_model->get_origin_iso3_code($origin);

                    if (count($getCountryCode) == 1) {

                        $countryCode = $getCountryCode[0]->origin_iso3_code;
                        $companyCode = "CGR";
                        $currentYear = date("Y");
                        $contractCode = $countryCode . '/' . $companyCode . '/' . $currentYear . '/' . str_pad($lastContractCode[0]->tid, 10, '0', STR_PAD_LEFT);

                        $dataPurchaseContract = array(
                            "supplier_id" => $suppliername, "contract_code" => $contractCode,
                            "contract_type" => $contracttype, "product" => $productname,
                            "product_type" => $producttype, "measurement_system" => 0,
                            "purchase_allowance" => $circumferenceallowance, "purchase_allowance_length" => $lengthallowance,
                            "unit_of_purchase" => $measuremetsystem, "currency" => $contractcurrency,
                            "payment_method" => $paymentmethod, "total_volume" => $totalvolume,
                            "total_value" => 0, "start_date" => $startdate,
                            "end_date" => $enddate, "remaining_volume" => $totalvolume,
                            "is_expired" => 0,
                            "created_by" => $session['user_id'],
                            "updated_by" => $session['user_id'], 'is_active' => $contractstatus,
                            'origin_id' => $origin,
                        );

                        $insertPurchaseContract = $this->Contract_model->add_purchase_contract($dataPurchaseContract);

                        if ($insertPurchaseContract > 0) {

                            $priceArrayJson = json_decode($pricearray, true);

                            foreach ($priceArrayJson as $price) {
                                $dataContractPrice = array(
                                    "supplier_id" => $insertPurchaseContract, "minrange_grade1" => $price["minRange"],
                                    "maxrange_grade2" => $price["maxRange"], "pricerange_grade3" => $price["price"],
                                    "created_by" => $session['user_id'],
                                    "updated_by" => $session['user_id'], 'is_active' => $contractstatus,
                                );

                                $insertPurchaseContractPrice = $this->Contract_model->add_purchase_contract_price($dataContractPrice);
                            }

                            $Return['result'] = $this->lang->line('data_added');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        } else {
                            $Return['error'] = $this->lang->line('error_adding');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                        }
                    } else {
                        $Return['error'] = $this->lang->line('error_adding');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    }
                } else if ($this->input->post('page_type') == 'edit') {

                    $contractcode = $this->input->post('contractcode');
                    $contractid = $this->input->post('contractid');

                    $totalVolumeBySupplier = $this->Contract_model->get_total_volume_by_supplier_contract($contractid, $suppliername);
                    $remainingVolume = $totalvolume - $totalVolumeBySupplier[0]->total_volume;

                    $today = date("Y-m-d");
                    $date = str_replace('/', '-', $enddate);
                    $newformat = date('Y-m-d', strtotime($date));

                    $isExpired = 0;

                    $expDate =  date_create($newformat);
                    $todayDate = date_create($today);
                    $diff =  date_diff($todayDate, $expDate);
                    if ($diff->format("%R%a") > 0) {
                        $isExpired = 0;
                    } else {
                        $isExpired = 1;
                    }

                    if ($contractstatus == 0) {
                        $dataPurchaseContract = array(
                            "total_volume" => $totalvolume,
                            "start_date" => $startdate,
                            "end_date" => $enddate, "remaining_volume" => $remainingVolume,
                            "is_expired" => $isExpired,
                            "updated_by" => $session['user_id'], 'is_active' => 0,
                        );
                    } else {
                        $dataPurchaseContract = array(
                            "total_volume" => $totalvolume,
                            "start_date" => $startdate,
                            "end_date" => $enddate, "remaining_volume" => $remainingVolume,
                            "is_expired" => $isExpired,
                            "updated_by" => $session['user_id'], 'is_active' => 1,
                        );
                    }


                    $updatePurchaseContract = $this->Contract_model->update_purchase_contract($dataPurchaseContract, $contractid, $contractcode);

                    if ($updatePurchaseContract == TRUE) {

                        $dataPurchaseContractPrice = array(
                            "updated_by" => $session['user_id'], 'is_active' => 0,
                        );

                        $updatePurchaseContractPrice = $this->Contract_model->update_purchase_contract_price($dataPurchaseContractPrice, $contractid);

                        if ($updatePurchaseContractPrice == TRUE) {

                            $priceArrayJson = json_decode($pricearray, true);
                            foreach ($priceArrayJson as $price) {
                                $dataContractPrice = array(
                                    "supplier_id" => $contractid, "minrange_grade1" => $price["minRange"],
                                    "maxrange_grade2" => $price["maxRange"], "pricerange_grade3" => $price["price"],
                                    "created_by" => $session['user_id'],
                                    "updated_by" => $session['user_id'], 'is_active' => $contractstatus,
                                );

                                $insertPurchaseContractPrice = $this->Contract_model->add_purchase_contract_price($dataContractPrice);
                            }
                        }

                        $Return['result'] = $this->lang->line('data_updated');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                    } else {

                        $Return['error'] = $this->lang->line('error_updating');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
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

    public function dialog_contract_view()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

        if ($this->input->get('type') == "viewcontract") {
            if (!empty($session)) {

                $getContractDetails = $this->Contract_model->get_contracts_by_id($this->input->get('cid'));
                $getContractPrice = $this->Contract_model->get_purchase_contract_price_by_contractid($getContractDetails[0]->contract_id);

                if ($getContractDetails[0]->is_expired == 1) {
                    $status = $this->lang->line('expired');
                } else if ($getContractDetails[0]->is_active == 1) {
                    $status = $this->lang->line('active');
                } else {
                    $status = $this->lang->line('inactive');
                }

                $contractType = $this->lang->line('warehouse');
                if ($getContractDetails[0]->contract_type == 2) {
                    $contractType = $this->lang->line('field_purchase');
                }

                $data = array(
                    'pageheading' => $this->lang->line('contract_details'),
                    'origin' => $getContractDetails[0]->origin,
                    'contract_price' => $getContractPrice,
                    'status' => $status,
                    'contract_type' => $contractType,
                    'contract_code' => $getContractDetails[0]->contract_code,
                    'contract_type_id' => $getContractDetails[0]->contract_type,
                    'supplier_name' => $getContractDetails[0]->supplier_name,
                    'product_name' => $getContractDetails[0]->product_name,
                    'product' => $getContractDetails[0]->product,
                    'product_type_name' => $getContractDetails[0]->product_type_name,
                    'product_type' => $getContractDetails[0]->product_type,
                    'purchase_unit' => $getContractDetails[0]->purchase_unit,
                    'currency' => $getContractDetails[0]->currency,
                    'payment_method' => $getContractDetails[0]->payment_name,
                    'start_date' => ucwords(str_replace($getContractDetails[0]->start_date_month, $this->lang->line($getContractDetails[0]->start_date_month), $getContractDetails[0]->start_date)),
                    'end_date' => ucwords(str_replace($getContractDetails[0]->end_date_month, $this->lang->line($getContractDetails[0]->end_date_month), $getContractDetails[0]->end_date)),
                    'total_volume' => ($getContractDetails[0]->total_volume + 0),
                    'remaining_volume' => ($getContractDetails[0]->remaining_volume + 0),
                    'currency_abbreviation' => $getContractDetails[0]->currency_abbreviation,
                );
                $this->load->view('purchasecontracts/dialog_view_contract', $data);
            } else {
                $Return['pages'] = "";
                $Return['redirect'] = true;
                $this->output($Return);
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function generate_contract_report()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $getContractReport = $this->Contract_model->get_contracts_reports($session["applicable_origins_id"]);

                if (count($getContractReport) > 0) {
                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('contracts_title'));
                    $objSheet->getParent()->getDefaultStyle()
                        ->getFont()
                        ->setName('Calibri')
                        ->setSize(11);

                    $objSheet->SetCellValue('A1', $this->lang->line('s_no'));
                    $objSheet->SetCellValue('B1', $this->lang->line('contract_code'));
                    $objSheet->SetCellValue('C1', $this->lang->line('contract_type'));
                    $objSheet->SetCellValue('D1', $this->lang->line('supplier_pm_name'));
                    $objSheet->SetCellValue('E1', $this->lang->line('supplier_code'));
                    $objSheet->SetCellValue('F1', $this->lang->line('supplier_id'));
                    $objSheet->SetCellValue('G1', $this->lang->line('product'));
                    $objSheet->SetCellValue('H1', $this->lang->line('product_type'));
                    $objSheet->SetCellValue('I1', $this->lang->line('measuremet_system'));
                    $objSheet->SetCellValue('J1', $this->lang->line('circumference_allowance'));
                    $objSheet->SetCellValue('K1', $this->lang->line('length_allowance'));
                    $objSheet->SetCellValue('L1', $this->lang->line('currency'));
                    $objSheet->SetCellValue('M1', $this->lang->line('payment_method'));
                    $objSheet->SetCellValue('N1', $this->lang->line('total_volume'));
                    $objSheet->SetCellValue('O1', $this->lang->line('remaining_volume'));
                    $objSheet->SetCellValue('P1', $this->lang->line('start_date'));
                    $objSheet->SetCellValue('Q1', $this->lang->line('end_date'));
                    $objSheet->SetCellValue('R1', $this->lang->line('status'));
                    $objSheet->SetCellValue('S1', $this->lang->line('origin'));

                    $objSheet->getStyle("A1:S1")
                        ->getFont()
                        ->setBold(true);

                    $objSheet->setAutoFilter('A1:S1');

                    // HEADER ALIGNMENT
                    $objSheet->getStyle("A1:S1")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    $objSheet->getColumnDimension('C')->setAutoSize(true);
                    $objSheet->getColumnDimension('D')->setAutoSize(true);
                    $objSheet->getColumnDimension('E')->setAutoSize(true);
                    $objSheet->getColumnDimension('F')->setAutoSize(true);
                    $objSheet->getColumnDimension('G')->setAutoSize(false);
                    $objSheet->getColumnDimension('G')->setWidth(30);
                    $objSheet->getColumnDimension('H')->setAutoSize(true);
                    $objSheet->getColumnDimension('I')->setAutoSize(true);
                    $objSheet->getColumnDimension('J')->setAutoSize(true);
                    $objSheet->getColumnDimension('K')->setAutoSize(true);
                    $objSheet->getColumnDimension('L')->setAutoSize(true);
                    $objSheet->getColumnDimension('M')->setAutoSize(true);
                    $objSheet->getColumnDimension('N')->setAutoSize(true);
                    $objSheet->getColumnDimension('O')->setAutoSize(true);
                    $objSheet->getColumnDimension('P')->setAutoSize(true);
                    $objSheet->getColumnDimension('Q')->setAutoSize(true);
                    $objSheet->getColumnDimension('R')->setAutoSize(true);
                    $objSheet->getColumnDimension('S')->setAutoSize(true);

                    $objSheet->getStyle('A1:S1')
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('add8e6');

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A1:N1')->applyFromArray($styleArray);

                    $i = 1;
                    $rowCountData = 2;

                    foreach ($getContractReport as $contract) {
                        $objSheet->SetCellValue('A' . $rowCountData, $i);
                        $objSheet->SetCellValue('B' . $rowCountData, $contract->contract_code);

                        $contractType = $this->lang->line('warehouse');
                        if ($contract->contract_type == 2) {
                            $contractType = $this->lang->line('field_purchase');
                        }

                        $objSheet->SetCellValue('C' . $rowCountData, $contractType);
                        $objSheet->SetCellValue('D' . $rowCountData, $contract->supplier_name);
                        $objSheet->SetCellValue('E' . $rowCountData, $contract->supplier_code);

                        $objSheet->setCellValueExplicit('F' . $rowCountData, $contract->supplier_id, PHPExcel_Cell_DataType::TYPE_STRING);

                        $objSheet->SetCellValue('G' . $rowCountData, $contract->product_name);
                        $objSheet->SetCellValue('H' . $rowCountData, $this->lang->line($contract->product_type_name));
                        $objSheet->SetCellValue('I' . $rowCountData, $this->lang->line($contract->purchase_unit));
                        $objSheet->SetCellValue('J' . $rowCountData, $contract->purchase_allowance);
                        $objSheet->SetCellValue('K' . $rowCountData, $contract->purchase_allowance_length);
                        $objSheet->SetCellValue('L' . $rowCountData, $contract->currency);
                        $objSheet->SetCellValue('M' . $rowCountData, $this->lang->line($contract->payment_name));
                        $objSheet->SetCellValue('N' . $rowCountData, $contract->total_volume);
                        $objSheet->SetCellValue('O' . $rowCountData, $contract->remaining_volume);
                        $objSheet->SetCellValue('P' . $rowCountData, ucwords(str_replace($contract->start_date_month, $this->lang->line($contract->start_date_month), $contract->start_date)));
                        $objSheet->SetCellValue('Q' . $rowCountData, ucwords(str_replace($contract->end_date_month, $this->lang->line($contract->end_date_month), $contract->end_date)));

                        if ($contract->is_expired == 1) {
                            $status = $this->lang->line('expired');
                        } else if ($contract->is_active == 1) {
                            $status = $this->lang->line('active');
                        } else {
                            $status = $this->lang->line('inactive');
                        }

                        $objSheet->SetCellValue('R' . $rowCountData, $status);
                        $objSheet->SetCellValue('S' . $rowCountData, $contract->origin);

                        $objSheet->getStyle('A' . $rowCountData . ':S' . $rowCountData)->applyFromArray($styleArray);

                        $i++;
                        $rowCountData++;
                    }

                    $objSheet->getSheetView()->setZoomScale(95);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    // if (!is_dir('./reports/ContractReports')) {
                    //     mkdir('./reports/ContractReports', 0777, TRUE);
                    // }

                    $filename =  'ContractReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

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
            $Return['error'] = $this->lang->line('error_reports');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function load_price_template()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );
            
            $originId = $this->input->post('originId');

            if (!empty($session)) {

                if ($_FILES['filePriceExcel']['size'] > 0) {
                    $config['upload_path'] = FCPATH . 'reports/';
                    $config['allowed_types'] = 'xlsx';
                    $config['remove_spaces'] = TRUE;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('filePriceExcel')) {
                        $Return['error'] = "sdfsdf";
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
                            $createArray = array('MIN RANGE', 'MAX RANGE', 'PRICE');
                            $makeArray = array('MINRANGE' => 'MIN RANGE', 'MAXRANGE' => 'MAX RANGE', 'PRICE' => 'PRICE');
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
                            $priceData = array();
                            if (empty($data)) {
                                $lastMaxRange = 0;
                                for ($i = 2; $i <= $arrayCount; $i++) {
                                    $minRange = $SheetDataKey['MINRANGE'];
                                    $maxRange = $SheetDataKey['MAXRANGE'];
                                    $price = $SheetDataKey['PRICE'];

                                    $minRangeVal = filter_var(trim($allDataInSheet[$i][$minRange]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $maxRangeVal = filter_var(trim($allDataInSheet[$i][$maxRange]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $priceVal = filter_var(trim($allDataInSheet[$i][$price]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    
                                    if ($originId == 3) {

                                        if (($minRangeVal != null && $minRangeVal != "" && $minRangeVal > 0) &&
                                            ($maxRangeVal != null && $maxRangeVal != "" && $maxRangeVal > 0) &&
                                            ($priceVal != null && $priceVal != "" && $priceVal > 0)
                                        ) {
                                            $priceData[] = array(
                                                'minRange' => ($minRangeVal + 0),
                                                'maxRange' => ($maxRangeVal + 0), 'price' => ($priceVal + 0),
                                            );
                                        }

                                    } else {

                                        if (($minRangeVal != null && $minRangeVal != "" && $minRangeVal > 0) &&
                                            ($maxRangeVal != null && $maxRangeVal != "" && $maxRangeVal > 0) &&
                                            ($priceVal != null && $priceVal != "" && $priceVal > 0)
                                        ) {
                                            if ($minRangeVal >= $maxRangeVal) {
                                                $Return['warning'] = $this->lang->line('enter_min_range_check_excel') . $i;
                                                $Return['result'] = "";
                                                $Return['redirect'] = false;
                                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                                $this->output($Return);
                                                exit;
                                            } else if ($lastMaxRange >= $minRangeVal) {
                                                $Return['warning'] = $this->lang->line('enter_last_range_check_excel') . $i;
                                                $Return['result'] = "";
                                                $Return['redirect'] = false;
                                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                                $this->output($Return);
                                                exit;
                                            } else {
                                                $priceData[] = array(
                                                    'minRange' => ($minRangeVal + 0),
                                                    'maxRange' => ($maxRangeVal + 0), 'price' => ($priceVal + 0),
                                                );
                                                $lastMaxRange = $maxRangeVal;
                                            }
                                        }
                                    }
                                }

                                if (count($priceData) == 0) {
                                    $Return['warning'] = $this->lang->line('error_nodata_excel');
                                    $Return['error'] = "";
                                    $Return['result'] = "";
                                    $Return['redirect'] = false;
                                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                    $this->output($Return);
                                    exit;
                                } else {
                                    $Return['error'] = "";
                                    $Return['result'] = $priceData;
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

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . 'reports/*.xlsx');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/ContractReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
