<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Downloadfarmmasters extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
        $this->load->model("Farm_model");
        $this->load->library("jwttoken");
        $this->load->helper('url');
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        try {
            if ($this->input->method(TRUE) == "GET") {
                $headers = apache_request_headers();
                foreach ($headers as $header => $value) {
                    if ($header == "Authorization") {
                        list($a, $b) = explode(" ", $value);
                        $requestBearerToken = $b;
                    }
                }
                $token = JWT::decode($requestBearerToken, JWT_SECRET);

                $userid = $token->userid;
                $originid = $token->originid;
                $roleid = $token->roleid;

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);

                    if ($checkUserExists == true) {

                        //USERDATA
                        $fetchUserData = $this->Login_model->fetch_user_information($userid);
                        $row_array_user["userId"] = (int) $userid;
                        $row_array_user["originId"] = (int) $originid;
                        $row_array_user["fullName"] = $fetchUserData[0]->fullname;
                        $row_array_user["photo"] = base_url() . $fetchUserData[0]->profilephoto;
                        $row_array_final["userData"] = $row_array_user;

                        //SUPPLIERS
                        $fetchSuppliers = $this->Master_model->get_suppliers_by_origin($originid);
                        $return_arr_suppliers = array();
                        
                        foreach ($fetchSuppliers as $supplier) {
                            $row_array_suppliers["supplierId"] = (int) $supplier->id;
                            $row_array_suppliers["supplierName"] = $supplier->supplier_name;
                            $row_array_suppliers["supplierCode"] = $supplier->supplier_code;

                            $return_arr_supplier_products = array();
                            $return_arr_supplier_product_type = array();
                            
                            $fetchSupplierProducts = $this->Master_model->get_suppliers_products_by_origin($supplier->id);
                            foreach ($fetchSupplierProducts as $supplierproduct) {
                                $row_array_supplierproduct["supplierProductId"] = (int) $supplierproduct->supplier_product_id;
                                $row_array_supplierproduct["productId"] = (int) $supplierproduct->product_id;
                                $row_array_supplierproduct["productName"] = $supplierproduct->product_name;
                                
                                $fetchSupplierProductType = $this->Master_model->get_suppliers_product_types_by_origin($supplier->id, $supplierproduct->supplier_product_id);
                                foreach ($fetchSupplierProductType as $supplierproducttype) {
    
                                    $row_array_supplierproducttype["typeId"] = (int) $supplierproducttype->type_id;
                                    $row_array_supplierproducttype["productTypeName"] = $supplierproducttype->product_type_name;
                                    $row_array_supplierproducttype["productTypeId"] = (int) $supplierproducttype->product_type_id;

                                    array_push($return_arr_supplier_product_type, $row_array_supplierproducttype);
                                }

                                $row_array_supplierproduct["supplierProductTypes"] = $return_arr_supplier_product_type;
                                array_push($return_arr_supplier_products, $row_array_supplierproduct);
                            }

                            $row_array_suppliers["supplierProducts"] = $return_arr_supplier_products;

                            array_push($return_arr_suppliers, $row_array_suppliers);
                        }
                        $row_array_final["suppliers"] = $return_arr_suppliers;

                        //PURCHASE CONTRACTS
                        $fetchPurchaseContracts = $this->Contract_model->fetch_purchase_contract_origin($originid);
                        $return_arr_purchasecontracts = array();
                        foreach ($fetchPurchaseContracts as $purchasecontract) {
                            $row_array_purchasecontract["contractId"] = (int) $purchasecontract->contract_id;
                            $row_array_purchasecontract["supplierId"] = (int) $purchasecontract->supplier_id;
                            $row_array_purchasecontract["contractCode"] = $purchasecontract->contract_code;
                            $row_array_purchasecontract["product"] = (int) $purchasecontract->product;
                            $row_array_purchasecontract["productType"] = (int) $purchasecontract->product_type;
                            $row_array_purchasecontract["purchaseUnit"] = $purchasecontract->purchase_unit;
                            $row_array_purchasecontract["currency"] = $purchasecontract->currency;
                            $row_array_purchasecontract["circAllowance"] = $purchasecontract->purchase_allowance + 0;
                            $row_array_purchasecontract["lengthAllowance"] = $purchasecontract->purchase_allowance_length + 0;
                            $row_array_purchasecontract["description"] = $purchasecontract->description;
                            $row_array_purchasecontract["purchaseUnitId"] = (int) $purchasecontract->purchase_unit_id;
                            array_push($return_arr_purchasecontracts, $row_array_purchasecontract);
                        }
                        $row_array_final["purchaseContract"] = $return_arr_purchasecontracts;

                        //FARM MASTERS
                        $fetchFarmMasters = $this->Farm_model->get_inventory_order_masters($originid);
                        $return_arr_farmmasters = array();
                        foreach ($fetchFarmMasters as $farmmaster) {
                            $row_array_farmmaster["inventoryNumber"] = (int) $farmmaster->inventory_order;
                            $row_array_farmmaster["supplierId"] = (int) $farmmaster->supplier_id;

                            array_push($return_arr_farmmasters, $row_array_farmmaster);
                        }
                        $row_array_final["farmMasters"] = $return_arr_farmmasters;

                        //FARM DETAILS & DATA
                        $fetchFarmDetails = $this->Farm_model->fetch_farm_details($originid);
                        $return_arr_farmdetails = array();
                        foreach ($fetchFarmDetails as $farmdetail) {
                            $row_array_farmdetail["farmId"] = (int) $farmdetail->farm_id;
                            $row_array_farmdetail["supplierId"] = (int) $farmdetail->supplier_id;
                            $row_array_farmdetail["productId"] = (int) $farmdetail->product_id;
                            $row_array_farmdetail["productTypeId"] = (int) $farmdetail->product_type_id;
                            $row_array_farmdetail["inventoryOrder"] = $farmdetail->inventory_order;
                            $row_array_farmdetail["purchaseContractId"] = (int) $farmdetail->contract_id;
                            $row_array_farmdetail["purchaseUnitId"] = (int) $farmdetail->purchase_unit_id;
                            $row_array_farmdetail["purchaseDate"] = $farmdetail->purchase_date;
                            $row_array_farmdetail["truckPlateNumber"] = $farmdetail->plate_number;
                            $row_array_farmdetail["totalPieces"] = $farmdetail->total_pieces + 0;
                            $row_array_farmdetail["grossVolume"] = $farmdetail->total_gross_volume + 0;
                            $row_array_farmdetail["netVolume"] = $farmdetail->total_volume + 0;
                            $row_array_farmdetail["supplierName"] = $farmdetail->supplier_name;
                            $row_array_farmdetail["measurementSystem"] = $farmdetail->purchase_unit;
                            $row_array_farmdetail["productName"] = $farmdetail->product_name;
                            $row_array_farmdetail["circAllowance"] = $farmdetail->circ_allowance + 0;
                            $row_array_farmdetail["lengthAllowance"] = $farmdetail->length_allowance + 0;
                            $row_array_farmdetail["description"] = $farmdetail->description;
                            $row_array_farmdetail["isForData"] = (bool) $farmdetail->fordata;

                            $return_arr_farm_data = array();
                            if($farmdetail->fordata == 1) {
                                //DO NOTHING
                            } else {
                                $fetchFarmData = $this->Farm_model->get_farm_data_by_farm_id_detailed($farmdetail->farm_id);
                                foreach ($fetchFarmData as $farmdata) {
                                    $row_array_farmdata["pieces"] = (int) $farmdata->no_of_pieces;
                                    $row_array_farmdata["circumference"] = $farmdata->circumference + 0;
                                    $row_array_farmdata["length"] = $farmdata->length + 0;
                                    $row_array_farmdata["grossVolume"] = $farmdata->gross_volume + 0;
                                    $row_array_farmdata["netVolume"] = $farmdata->volume + 0;
                                    $row_array_farmdata["farmDataId"] = $farmdata->farm_data_id + 0;
                                    $row_array_farmdata["farmId"] = $farmdata->farm_id + 0;
                                    array_push($return_arr_farm_data, $row_array_farmdata);
                                }
                                $row_array_farmdetail["farmData"] = $return_arr_farm_data;
                            }
                            
                            array_push($return_arr_farmdetails, $row_array_farmdetail);
                        }
                        $row_array_final["farmDetails"] = $return_arr_farmdetails;

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $row_array_final;
                        http_response_code(200);
                        $this->output($Return);
                    } else {
                        $Return["status"] = false;
                        $Return["message"] = "Unauthorized";
                        http_response_code(401);
                        $this->output($Return);
                    }
                } else {
                    $Return["status"] = false;
                    $Return["message"] = "Unauthorized";
                    http_response_code(401);
                    $this->output($Return);
                }
            }
        } catch (Exception $e) {
            $Return["status"] = false;
            $Return["message"] = "Internal Server Error";
            http_response_code(500);
            $this->output($Return);
        }
    }
}