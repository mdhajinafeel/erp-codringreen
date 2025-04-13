<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Downloadmasters extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Contract_model");
        $this->load->model("Dispatch_model");
        $this->load->model("Reception_model");
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
                        $row_array_user["address"] = $fetchUserData[0]->address;
                        $row_array_user["emailId"] = $fetchUserData[0]->emailid;
                        $row_array_user["contactNo"] = $fetchUserData[0]->contactno;
                        $row_array_final["userData"] = $row_array_user;
                        
                        //PRODUCTS
                        $fetchProducts = $this->Master_model->get_product_byorigin($originid);
                        $return_arr_products = array();
                        foreach ($fetchProducts as $product) {
                            $row_array_product["productId"] = (int) $product->product_id;
                            $row_array_product["productName"] = $product->product_name;
                            array_push($return_arr_products, $row_array_product);
                        }
                        $row_array_final["products"] = $return_arr_products;

                        //PRODUCT TYPES
                        $fetchProductTypes = $this->Master_model->get_product_type_app();
                        $return_arr_product_types = array();
                        foreach ($fetchProductTypes as $producttype) {
                            $row_array_producttype["productTypeId"] = (int) $producttype->type_id;
                            $row_array_producttype["productTypeName"] = $producttype->product_type_name;
                            array_push($return_arr_product_types, $row_array_producttype);
                        }
                        $row_array_final["productTypes"] = $return_arr_product_types;

                        //MEASUREMENT SYSTEMS
                        $fetchMeasurmentSystems = $this->Master_model->fetch_measurementsystems_by_originid($originid);
                        $return_arr_measurmentsystems = array();
                        foreach ($fetchMeasurmentSystems as $measurementsystem) {
                            $row_array_measurementsystem["measurementSystemId"] = (int) $measurementsystem->measurement_id;
                            $row_array_measurementsystem["measurementSystemName"] = $measurementsystem->measurement_name;
                            $row_array_measurementsystem["productTypeId"] = (int) $measurementsystem->product_type_id;
                            array_push($return_arr_measurmentsystems, $row_array_measurementsystem);
                        }
                        $row_array_final["measurementSystems"] = $return_arr_measurmentsystems;

                        //SHIPPING LINES
                        $fetchShippingLine = $this->Master_model->get_shippinglines_by_origin($originid);
                        $return_arr_shipping_line = array();
                        foreach ($fetchShippingLine as $shippingline) {
                            $row_array_shippingline["shippingId"] = (int) $shippingline->id;
                            $row_array_shippingline["shippingLine"] = $shippingline->shipping_line;
                            array_push($return_arr_shipping_line, $row_array_shippingline);
                        }
                        $row_array_final["shippingLines"] = $return_arr_shipping_line;

                        //WAREHOUSES
                        $fetchWarehouses = $this->Master_model->get_warehouses_by_origin($originid);
                        $return_arr_warehouse = array();
                        foreach ($fetchWarehouses as $warehouse) {
                            $row_array_warehouse["warehouseId"] = (int) $warehouse->whid;
                            $row_array_warehouse["warehouseName"] = $warehouse->warehouse_name;
                            $row_array_warehouse["pol"] = (int) $warehouse->pol;
                            array_push($return_arr_warehouse, $row_array_warehouse);
                        }
                        $row_array_final["warehouses"] = $return_arr_warehouse;

                        //GIRTH CLASSIFICATION
                        $fetchGirthClassification = $this->Master_model->get_girth_classification_by_origin($originid);
                        $return_arr_girthclassification = array();
                        foreach ($fetchGirthClassification as $girthclassification) {
                            $row_array_girthclassification["girthClassificationId"] = (int) $girthclassification->id;
                            $row_array_girthclassification["girthClassification"] = $girthclassification->girth_classification;
                            $row_array_girthclassification["isManual"] = (bool) $girthclassification->is_manual;
                            array_push($return_arr_girthclassification, $row_array_girthclassification);
                        }
                        $row_array_final["girthClassification"] = $return_arr_girthclassification;

                        //LENGTH CLASSIFICATION
                        $fetchLengthClassification = $this->Master_model->get_length_classification_by_origin($originid);
                        $return_arr_lengthclassification = array();
                        foreach ($fetchLengthClassification as $lengthclassification) {
                            $row_array_lengthclassification["lengthClassificationId"] = (int) $lengthclassification->id;
                            $row_array_lengthclassification["lengthClassification"] = $lengthclassification->length_classification;
                            $row_array_lengthclassification["isManual"] = (bool) $lengthclassification->is_manual;
                            array_push($return_arr_lengthclassification, $row_array_lengthclassification);
                        }
                        $row_array_final["lengthClassification"] = $return_arr_lengthclassification;

                        //SUPPLIERS
                        $fetchSuppliers = $this->Master_model->get_suppliers_by_origin($originid);
                        $return_arr_suppliers = array();
                        
                        foreach ($fetchSuppliers as $supplier) {
                            $row_array_suppliers["supplierId"] = (int) $supplier->id;
                            $row_array_suppliers["supplierName"] = $supplier->supplier_name;
                            $row_array_suppliers["supplierCode"] = $supplier->supplier_code;

                            $return_arr_supplier_products = array();
                            $return_arr_supplier_product_type = array();
                            $return_arr_supplier_roles = array();
                            
                            $fetchSupplierRoles = $this->Master_model->get_supplier_roles_by_origin($supplier->id);
                            foreach ($fetchSupplierRoles as $supplierrole) {
                                $row_array_supplierrole["roleId"] = (int) $supplierrole->role_id;
                                
                                array_push($return_arr_supplier_roles, $row_array_supplierrole);
                            }
                            
                            $row_array_suppliers["supplierRoles"] = $return_arr_supplier_roles;
                            
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
                            array_push($return_arr_purchasecontracts, $row_array_purchasecontract);
                        }
                        $row_array_final["purchaseContract"] = $return_arr_purchasecontracts;

                        //CONTAINER NUMBERS
                        $fetchContainerNumbers = $this->Dispatch_model->get_available_container_numbers($originid);
                        $return_arr_containernumbers = array();
                        foreach ($fetchContainerNumbers as $containernumber) {
                            $row_array_containernumber["containerNumber"] = $containernumber->container_number;
                            array_push($return_arr_containernumbers, $row_array_containernumber);
                        }
                        $row_array_final["containerNumbers"] = $return_arr_containernumbers;

                        //CONTAINER DATA
                        $fetchContainerLists = $this->Dispatch_model->get_container_lists($originid);
                        $return_arr_containers = array();
                        foreach ($fetchContainerLists as $containers) {
                            $row_array_container["containerId"] = (int) $containers->dispatch_id;
                            $row_array_container["containerNumber"] = $containers->container_number;
                            $row_array_container["shippingLine"] = (int) $containers->shipping_line;
                            $row_array_container["productId"] = (int) $containers->product_id;
                            $row_array_container["productTypeId"] =  (int) $containers->product_type_id;
                            $row_array_container["warehouseId"] = (int) $containers->warehouse_id;
                            $row_array_container["dispatchDate"] = $containers->dispatch_date;
                            $row_array_container["sealNumber"] = $containers->seal_number;
                            $row_array_container["category"] = (int) $containers->category;
                            $row_array_container["createdBy"] = (int) $containers->createdby;
                            $row_array_container["createdDate"] = (int) $containers->createddate;
                            $row_array_container["isClosed"] = (bool) $containers->isclosed;
                            $row_array_container["closedBy"] = (int) $containers->closedby;
                            $row_array_container["closedDate"] = (int) $containers->closeddate;
                            $row_array_container["isContainerAvailable"] = (bool) $containers->iscontainer_available;
                            $row_array_container["isSpecialUploaded"] = (bool) $containers->is_special_uploaded;
                            $row_array_container["originId"] = (int) $containers->origin_id;
                            $row_array_container["totalGrossVolume"] = $containers->total_gross_volume + 0;
                            $row_array_container["totalNetVolume"] = $containers->total_volume + 0;
                            $row_array_container["totalPieces"] = (int) $containers->total_pieces;
                            $row_array_container["existingContainerNumber"] = $containers->existingContainerNumber;
                            array_push($return_arr_containers, $row_array_container);
                        }
                        $row_array_final["containers"] = $return_arr_containers;

                        //RECEPTION DATA
                        $fetchReceptionLists = $this->Reception_model->get_reception_lists($originid);
                        $return_arr_receptions = array();
                        foreach ($fetchReceptionLists as $reception) {
                            $row_array_reception["receptionId"] = (int) $reception->reception_id;
                            $row_array_reception["inventoryOrder"] = $reception->salvoconducto;
                            $row_array_reception["measurementSystemId"] = (int) $reception->measurementsystem_id;
                            $row_array_reception["originId"] = (int) $reception->origin_id;
                            $row_array_reception["receivedDate"] = $reception->received_date;
                            $row_array_reception["warehouseId"] = (int) $reception->warehouse_id;
                            $row_array_reception["supplierId"] = (int) $reception->supplier_id;
                            $row_array_reception["productId"] = (int) $reception->product_name;
                            $row_array_reception["productTypeId"] = (int) $reception->type_id;
                            $row_array_reception["supplierProductId"] = (int) $reception->supplier_product_id;
                            $row_array_reception["supplierProductTypeId"] = (int) $reception->supplier_product_typeid;
                            $row_array_reception["createdBy"] = (int) $reception->createdby;
                            $row_array_reception["createdDate"] = (int) $reception->createddate;
                            $row_array_reception["isClosed"] = (bool) $reception->isclosed;
                            $row_array_reception["closedBy"] = (int) $reception->closedby;
                            $row_array_reception["closedDate"] = (int) $reception->closeddate;
                            $row_array_reception["isCreateFarm"] = (bool) $reception->is_create_farm;
                            $row_array_reception["isSpecialUploaded"] = (bool) $reception->is_special_uploaded;
                            $row_array_reception["contractId"] = (int) $reception->contract_id;
                            $row_array_reception["totalGrossVolume"] = $reception->total_gross_volume + 0;
                            $row_array_reception["totalNetVolume"] = $reception->total_volume + 0;
                            $row_array_reception["totalPieces"] = (int) $reception->total_pieces;
                            $row_array_reception["truckPlateNumber"] = $reception->truck_plate_number;
                            $row_array_reception["logisticCost"] = $reception->logistic_cost + 0;
                            $row_array_reception["logisticPayTo"] = (int) $reception->logistic_pay_to;

                            $fetchReceptionData = $this->Reception_model->get_reception_data_by_receptionid_user($reception->reception_id, $userid);
                            $return_arr_receptiondata = array();
                            foreach ($fetchReceptionData as $receptiondata) {
                                $row_array_receptiondata["circumferenceBought"] = $receptiondata->circumference_bought + 0;
                                $row_array_receptiondata["lengthBought"] = $receptiondata->length_bought + 0;
                                $row_array_receptiondata["containerId"] = (int) $receptiondata->dispatch_id;
                                $row_array_receptiondata["containerNumber"] = $receptiondata->container_number;
                                $row_array_receptiondata["grossVolume"] = $receptiondata->cbm_bought + 0;
                                $row_array_receptiondata["netVolume"] = $receptiondata->cbm_export + 0;
                                $row_array_receptiondata["pieces"] = (int) $receptiondata->scanned_code;
                                $row_array_receptiondata["receptionDataId"] = (int) $receptiondata->reception_data_id;
                                $row_array_receptiondata["receptionId"] = (int) $receptiondata->reception_id;
                                $row_array_receptiondata["createdBy"] = (int) $userid;
                                array_push($return_arr_receptiondata, $row_array_receptiondata);
                            }

                            $row_array_reception["receptionData"] = $return_arr_receptiondata;

                            $fetchReceptionDispatchData = $this->Reception_model->get_reception_dispatch_mapping($reception->reception_id);
                            $return_arr_receptiondispatchdata = array();
                            foreach ($fetchReceptionDispatchData as $receptiondispacthdata) {
                                $row_array_receptiondispatchdata["inventoryOrder"] = $receptiondispacthdata->salvoconducto;
                                $row_array_receptiondispatchdata["containerNumber"] = $receptiondispacthdata->container_number;
                                $row_array_receptiondispatchdata["receptionId"] = (int) $receptiondispacthdata->reception_id;
                                $row_array_receptiondispatchdata["containerId"] = (int) $receptiondispacthdata->dispatch_id;
                                $row_array_receptiondispatchdata["isClosed"] = (bool) $receptiondispacthdata->isclosed;
                                array_push($return_arr_receptiondispatchdata, $row_array_receptiondispatchdata);
                            }

                            $row_array_reception["receptionContainerMapping"] = $return_arr_receptiondispatchdata;
                            array_push($return_arr_receptions, $row_array_reception);
                        }
                        $row_array_final["receptions"] = $return_arr_receptions;

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