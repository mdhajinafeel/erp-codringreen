<?php

use PHPUnit\Util\Xml\FailedSchemaDetectionResult;

defined("BASEPATH") or exit("No direct script access allowed");

class Syncfarmdata extends MY_Controller
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
        $this->load->model("Farm_model");
        $this->load->model("Exchange_model");
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
            if ($this->input->method(TRUE) == "POST") {

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

                $farm_arr_response = array();

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);
                    if ($checkUserExists == true) {

                        $requestdata = json_decode(file_get_contents("php://input"), true);

                        $farmData = $requestdata["farmData"];

                        if (count($farmData) > 0) {
                            foreach ($farmData as $key => $value) {
                                $row_farm_array = array();

                                $tempFarmId = $value["tempFarmId"];
                                $inventoryOrder = $value["inventoryOrder"];
                                $farmMainId = $value["farmId"];
                                $supplierId = $value["supplierId"];
                                $productId = $value["productId"];
                                $productTypeId = $value["productTypeId"];
                                $purchaseUnitId = $value["purchaseUnitId"];
                                $purchaseDate = $value["purchaseDate"];
                                $purchaseContractId = $value["purchaseContractId"];
                                $truckPlateNumber = $value["truckPlateNumber"];
                                $truckDriverName = $value["truckDriverName"];
                                $createdBy = $value["createdBy"];
                                $isClosed = $value["isClosed"];
                                $closedBy = $value["closedBy"];
                                $closedDate = $value["closedDate"];
                                $originId = $value["originId"];
                                $circAllowance = $value["circAllowance"];
                                $lengthAllowance = $value["lengthAllowance"];
                                $farmCapturedData = $value["farmCapturedData"];
                                $totalNetVolume = 0;

                                $getContractDetails = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);

                                if ($farmMainId > 0 && $inventoryOrder != null && $inventoryOrder != "") {

                                    if (count($farmCapturedData) > 0) {
                                        $dataFarmData = array();

                                        foreach ($farmCapturedData as $key => $capturevalue) {
                                            $pieces = $capturevalue["pieces"];
                                            $farmDataId = $capturevalue["farmDataId"];
                                            $circumference = $capturevalue["circumference"];
                                            $length = $capturevalue["length"];
                                            $grossVolume = $capturevalue["grossVolume"];
                                            $netVolume = $capturevalue["netVolume"];
                                            $capturedTimeStamp = $capturevalue["capturedTimeStamp"];

                                            if ($farmDataId > 0) {

                                                $dataUpdateFarm = array(
                                                    "scanned_code" => "",
                                                    "no_of_pieces" => $pieces,
                                                    "circumference" => $circumference,
                                                    "length" => $length,
                                                    "width" => 0,
                                                    "thickness" => 0,
                                                    "gross_volume" => $grossVolume,
                                                    "volume" => $netVolume,
                                                    "volume_pie" => 0,
                                                    "grade_id" => 0,
                                                    "face" => 0,
                                                    "length_export" => 0,
                                                    "width_export" => 0,
                                                    "thickness_export" => 0,
                                                    "volume_bought" => 0,
                                                    "updated_by" => $userid,
                                                );

                                                $updateFarmData = $this->Farm_model->update_farm_data($farmDataId, $farmMainId, $dataUpdateFarm);
                                            } else {

                                                $dataFarmData[] = array(
                                                    "farm_id" => $farmMainId,
                                                    "scanned_code" => "",
                                                    "no_of_pieces" => $pieces,
                                                    "circumference" => $circumference,
                                                    "length" => $length,
                                                    "width" => 0,
                                                    "thickness" => 0,
                                                    "gross_volume" => $grossVolume,
                                                    "volume" => $netVolume,
                                                    "volume_pie" => 0,
                                                    "grade_id" => 0,
                                                    "face" => 0,
                                                    "length_export" => 0,
                                                    "width_export" => 0,
                                                    "thickness_export" => 0,
                                                    "volume_bought" => 0,
                                                    "created_by" => $userid,
                                                    "updated_by" => $userid,
                                                    "is_active" => 1,
                                                    "created_date" => date('Y-m-d H:i:s'),
                                                    "updated_date" => date('Y-m-d H:i:s'),
                                                    "captured_timestamp" => $capturedTimeStamp,
                                                );
                                            }
                                        }

                                        if (count($dataFarmData) > 0) {
                                            $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                            if ($insertFarmData) {

                                                //FETCH FARM DATA
                                                $getFarmData = $this->Farm_model->get_farm_data_by_farm_id($farmMainId);

                                                $totalGrossVolume = 0;
                                                $totalNetVolume = 0;
                                                $totalPieces = 0;

                                                foreach ($getFarmData as $farmdata) {
                                                    $totalGrossVolume = $totalGrossVolume + $farmdata->gross_volume;
                                                    $totalNetVolume = $totalNetVolume + $farmdata->volume;
                                                    $totalPieces = $totalPieces + $farmdata->no_of_pieces;
                                                }

                                                //UPDATE FARM DETAILS
                                                $dataFarm = array(
                                                    "total_volume" => $totalNetVolume,
                                                    "total_gross_volume" => $totalGrossVolume,
                                                    "total_pieces" => $totalPieces,
                                                );

                                                $updateFarm = $this->Farm_model->update_farm($farmMainId, $inventoryOrder, $purchaseContractId, $dataFarm);
                                            }
                                        }

                                        //CHECK IF FARM IS CLOSED
                                        if ($isClosed === true || $isClosed === false) {

                                            //CALCULATE WOOD VALUE & TAXES
                                            $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 1);
                                            $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 2);
                                            $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 3);

                                            $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                            $finalArray = [];
                                            $totalWoodValue = 0;
                                            $supplierTaxesValue = 0;
                                            $supplierTaxesArr = array();
                                            $providerLogisticTaxesArr = array();
                                            $providerServiceTaxesArr = array();
                                            $supplierLogisticTaxesArr = array();
                                            $supplierServiceTaxesArr = array();
                                            $supplierTaxesAdjustArr = array();
                                            $providerLogisticTaxesAdjustArr = array();
                                            $providerServiceTaxesAdjustArr = array();

                                            foreach ($farmDataShorts as $shorts) {
                                                $circumference = $shorts->circumference;
                                                $length = $shorts->length;
                                                $netVolume = $shorts->volume;
                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade3;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 2)
                                                ];
                                            }

                                            foreach ($farmDataSemi as $semi) {
                                                $circumference = $semi->circumference;
                                                $length = $semi->length;
                                                $netVolume = $semi->volume;
                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade_semi;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 2)
                                                ];
                                            }

                                            foreach ($farmDataLongs as $longs) {
                                                $circumference = $longs->circumference;
                                                $length = $longs->length;
                                                $netVolume = $longs->volume;
                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade_longs;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 2)
                                                ];
                                            }

                                            if (count($finalArray) > 0) {

                                                //WOOD VALUE
                                                foreach ($finalArray as $item) {
                                                    $totalWoodValue = $totalWoodValue + $item['value'];
                                                }

                                                //SUPPLIER TAXES
                                                $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                $calcValue = $totalWoodValue * ($taxValue / 100);
                                                            } else {
                                                                $calcValue = $totalWoodValue * ($taxValue);
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

                                                $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                $exchangeRate = 1;
                                                if ($getContractDetails[0]->currency == 1) {
                                                    $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                    $exchangeRate = $fetchExchangeRate[0]->value;
                                                    $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                    $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                }

                                                $dataUpdateFarmDetails = array(
                                                    "exchange_rate" => $exchangeRate,
                                                    "total_value" => $totalWoodValue,
                                                    "wood_value" => $totalWoodValue,
                                                    "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                    "service_cost_withtaxes" => 0,
                                                    "logistic_cost_withtaxes" => 0,
                                                    "supplier_taxes" => $supplierTaxesArrList,
                                                    "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                    "service_taxes" => $supplierServiceTaxesArrList,
                                                    "adjust_taxes" => '',
                                                    "is_adjust_rf" => 0,
                                                    "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                    "service_provider_taxes" => $providerServiceTaxesArrList,
                                                    "adjusted_value" => 0,
                                                    "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                    "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                    "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                    "updated_by" => $userid,
                                                );

                                                $updateFarmData = $this->Farm_model->update_farm($farmMainId, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                if ($updateFarmData) {

                                                    $dataInventoryLedgerUpdate = array(
                                                        "amount" => 0,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                    $dataContractPriceUpdate = array(
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                    $dataContractMappingUpdate = array(
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                    //SUPPLIER PRICE
                                                    $this->Farm_model->add_supplier_price(
                                                        $purchaseContractId,
                                                        $supplierId,
                                                        $inventoryOrder,
                                                        $createdBy
                                                    );

                                                    //CONTRACT INVENTORY MAPPING
                                                    $dataContractMapping = array(
                                                        "contract_id" => $purchaseContractId,
                                                        "supplier_id" => $supplierId,
                                                        "inventory_order" => $inventoryOrder,
                                                        "total_volume" => $totalNetVolume,
                                                        "invoice_number" => "",
                                                        "created_by" => $createdBy,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 1,
                                                    );

                                                    $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                    $dataInventoryLedger = array(
                                                        "contract_id" => $purchaseContractId,
                                                        "inventory_order" => $inventoryOrder,
                                                        "ledger_type" => 2,
                                                        "expense_date" => $purchaseDate,
                                                        "created_by" => $createdBy,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 1,
                                                        "is_advance_app" => 0,
                                                    );

                                                    if ($woodValueWithSupplierTaxes != 0) {
                                                        $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                    }

                                                    $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                    if (count($getContracts) == 1) {
                                                        $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                        $dataRemainingVolume = array(
                                                            "remaining_volume" => $remainingVolume,
                                                        );

                                                        $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                    }
                                                }
                                            }
                                        }

                                        $row_farm_array["farmId"] = $farmMainId + 0;
                                        $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                        $row_farm_array["syncStatus"] = true;
                                        $row_farm_array["tempFarmId"] = $tempFarmId;
                                        array_push($farm_arr_response, $row_farm_array);
                                    } else {

                                        $finalArray = [];
                                        $totalWoodValue = 0;
                                        $supplierTaxesValue = 0;
                                        $supplierTaxesArr = array();
                                        $providerLogisticTaxesArr = array();
                                        $providerServiceTaxesArr = array();
                                        $supplierLogisticTaxesArr = array();
                                        $supplierServiceTaxesArr = array();
                                        $supplierTaxesAdjustArr = array();
                                        $providerLogisticTaxesAdjustArr = array();
                                        $providerServiceTaxesAdjustArr = array();

                                        if ($isClosed === true || $isClosed === false) {

                                            //CALCULATE WOOD VALUE & TAXES
                                            $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 1);
                                            $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 2);
                                            $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 3);

                                            $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                            foreach ($farmDataShorts as $shorts) {
                                                $circumference = $shorts->circumference;
                                                $length = $shorts->length;
                                                $netVolume = $shorts->volume;

                                                $totalNetVolume = $totalNetVolume + $netVolume;

                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade3;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 3)
                                                ];
                                            }

                                            foreach ($farmDataSemi as $semi) {
                                                $circumference = $semi->circumference;
                                                $length = $semi->length;
                                                $netVolume = $semi->volume;
                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade_semi;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 3)
                                                ];
                                            }

                                            foreach ($farmDataLongs as $longs) {
                                                $circumference = $longs->circumference;
                                                $length = $longs->length;
                                                $netVolume = $longs->volume;
                                                $totalNetVolume = $totalNetVolume + $netVolume;
                                                $price = 0;

                                                foreach ($fetchContractPrice as $range) {
                                                    if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                        $price = $range->pricerange_grade_longs;
                                                        break;
                                                    }
                                                }

                                                $finalArray[] = [
                                                    'circumference' => $circumference,
                                                    'length' => $length,
                                                    'price' => $price,
                                                    'volume' => $netVolume,
                                                    'value' => round($price * $netVolume, 3)
                                                ];
                                            }

                                            if (count($finalArray) > 0) {

                                                //WOOD VALUE
                                                foreach ($finalArray as $item) {
                                                    $totalWoodValue = $totalWoodValue + $item['value'];
                                                }

                                                //SUPPLIER TAXES
                                                $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                $calcValue = $totalWoodValue * ($taxValue / 100);
                                                            } else {
                                                                $calcValue = $totalWoodValue * ($taxValue);
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

                                                $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                $exchangeRate = 1;
                                                if ($getContractDetails[0]->currency == 1) {
                                                    $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                    $exchangeRate = $fetchExchangeRate[0]->value;
                                                    $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                    $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                }

                                                $dataUpdateFarmDetails = array(
                                                    "exchange_rate" => $exchangeRate,
                                                    "total_value" => $totalWoodValue,
                                                    "wood_value" => $totalWoodValue,
                                                    "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                    "service_cost_withtaxes" => 0,
                                                    "logistic_cost_withtaxes" => 0,
                                                    "supplier_taxes" => $supplierTaxesArrList,
                                                    "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                    "service_taxes" => $supplierServiceTaxesArrList,
                                                    "adjust_taxes" => '',
                                                    "is_adjust_rf" => 0,
                                                    "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                    "service_provider_taxes" => $providerServiceTaxesArrList,
                                                    "adjusted_value" => 0,
                                                    "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                    "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                    "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                    "updated_by" => $userid,
                                                );

                                                $updateFarmData = $this->Farm_model->update_farm($farmMainId, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                if ($updateFarmData) {

                                                    $dataInventoryLedgerUpdate = array(
                                                        "amount" => 0,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                    $dataContractPriceUpdate = array(
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                    $dataContractMappingUpdate = array(
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 0,
                                                    );

                                                    $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                    //SUPPLIER PRICE
                                                    $this->Farm_model->add_supplier_price(
                                                        $purchaseContractId,
                                                        $supplierId,
                                                        $inventoryOrder,
                                                        $createdBy
                                                    );

                                                    //CONTRACT INVENTORY MAPPING
                                                    $dataContractMapping = array(
                                                        "contract_id" => $purchaseContractId,
                                                        "supplier_id" => $supplierId,
                                                        "inventory_order" => $inventoryOrder,
                                                        "total_volume" => $totalNetVolume,
                                                        "invoice_number" => "",
                                                        "created_by" => $createdBy,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 1,
                                                    );

                                                    $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                    $dataInventoryLedger = array(
                                                        "contract_id" => $purchaseContractId,
                                                        "inventory_order" => $inventoryOrder,
                                                        "ledger_type" => 2,
                                                        "expense_date" => $purchaseDate,
                                                        "created_by" => $createdBy,
                                                        "updated_by" => $createdBy,
                                                        "is_active" => 1,
                                                        "is_advance_app" => 0,
                                                    );

                                                    if ($woodValueWithSupplierTaxes != 0) {
                                                        $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                    }

                                                    $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                    if (count($getContracts) == 1) {
                                                        $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                        $dataRemainingVolume = array(
                                                            "remaining_volume" => $remainingVolume,
                                                        );

                                                        $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                    }
                                                }
                                            }
                                        }

                                        $row_farm_array["farmId"] = $farmMainId + 0;
                                        $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                        $row_farm_array["syncStatus"] = true;
                                        $row_farm_array["tempFarmId"] = $tempFarmId;
                                        array_push($farm_arr_response, $row_farm_array);
                                    }
                                } else if ($tempFarmId != null && $tempFarmId != "") {

                                    //CHECK IF EXISTS
                                    $getFarmDetails = $this->Farm_model->get_farm_details_byid_supplier($inventoryOrder, $supplierId);

                                    $farmId = 0;
                                    if (count($getFarmDetails) > 0) {
                                        $farmId = $getFarmDetails[0]->farm_id;
                                    } else {
                                        $farmId = 0;
                                    }

                                    if ($farmId > 0) {

                                        if (count($farmCapturedData) > 0) {
                                            $dataFarmData = array();

                                            foreach ($farmCapturedData as $key => $capturevalue) {
                                                $pieces = $capturevalue["pieces"];
                                                $farmDataId = $capturevalue["farmDataId"];
                                                $circumference = $capturevalue["circumference"];
                                                $length = $capturevalue["length"];
                                                $grossVolume = $capturevalue["grossVolume"];
                                                $netVolume = $capturevalue["netVolume"];
                                                $capturedTimeStamp = $capturevalue["capturedTimeStamp"];

                                                if ($farmDataId > 0) {

                                                    $dataUpdateFarm = array(
                                                        "scanned_code" => "",
                                                        "no_of_pieces" => $pieces,
                                                        "circumference" => $circumference,
                                                        "length" => $length,
                                                        "width" => 0,
                                                        "thickness" => 0,
                                                        "gross_volume" => $grossVolume,
                                                        "volume" => $netVolume,
                                                        "volume_pie" => 0,
                                                        "grade_id" => 0,
                                                        "face" => 0,
                                                        "length_export" => 0,
                                                        "width_export" => 0,
                                                        "thickness_export" => 0,
                                                        "volume_bought" => 0,
                                                        "updated_by" => $userid,
                                                    );

                                                    $updateFarmData = $this->Farm_model->update_farm_data($farmDataId, $farmId, $dataUpdateFarm);
                                                } else {

                                                    $dataFarmData[] = array(
                                                        "farm_id" => $farmId,
                                                        "scanned_code" => "",
                                                        "no_of_pieces" => $pieces,
                                                        "circumference" => $circumference,
                                                        "length" => $length,
                                                        "width" => 0,
                                                        "thickness" => 0,
                                                        "gross_volume" => $grossVolume,
                                                        "volume" => $netVolume,
                                                        "volume_pie" => 0,
                                                        "grade_id" => 0,
                                                        "face" => 0,
                                                        "length_export" => 0,
                                                        "width_export" => 0,
                                                        "thickness_export" => 0,
                                                        "volume_bought" => 0,
                                                        "created_by" => $userid,
                                                        "updated_by" => $userid,
                                                        "is_active" => 1,
                                                        "created_date" => date('Y-m-d H:i:s'),
                                                        "updated_date" => date('Y-m-d H:i:s'),
                                                        "captured_timestamp" => $capturedTimeStamp,
                                                    );
                                                }
                                            }

                                            if (count($dataFarmData) > 0) {
                                                $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                                if ($insertFarmData) {

                                                    //FETCH FARM DATA
                                                    $getFarmData = $this->Farm_model->get_farm_data_by_farm_id($farmId);

                                                    $totalGrossVolume = 0;
                                                    $totalNetVolume = 0;
                                                    $totalPieces = 0;

                                                    foreach ($getFarmData as $farmdata) {
                                                        $totalGrossVolume = $totalGrossVolume + $farmdata->gross_volume;
                                                        $totalNetVolume = $totalNetVolume + $farmdata->volume;
                                                        $totalPieces = $totalPieces + $farmdata->no_of_pieces;
                                                    }

                                                    //UPDATE FARM DETAILS
                                                    $dataFarm = array(
                                                        "total_volume" => $totalNetVolume,
                                                        "total_gross_volume" => $totalGrossVolume,
                                                        "total_pieces" => $totalPieces,
                                                    );

                                                    $updateFarm = $this->Farm_model->update_farm($farmId, $inventoryOrder, $purchaseContractId, $dataFarm);
                                                }
                                            }

                                            //CHECK IF FARM IS CLOSED
                                            if ($isClosed === true || $isClosed === false) {

                                                //CALCULATE WOOD VALUE & TAXES
                                                $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmId, 1);
                                                $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmId, 2);
                                                $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmId, 3);

                                                $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                                $finalArray = [];
                                                $totalWoodValue = 0;
                                                $supplierTaxesValue = 0;
                                                $supplierTaxesArr = array();
                                                $providerLogisticTaxesArr = array();
                                                $providerServiceTaxesArr = array();
                                                $supplierLogisticTaxesArr = array();
                                                $supplierServiceTaxesArr = array();
                                                $supplierTaxesAdjustArr = array();
                                                $providerLogisticTaxesAdjustArr = array();
                                                $providerServiceTaxesAdjustArr = array();

                                                foreach ($farmDataShorts as $shorts) {
                                                    $circumference = $shorts->circumference;
                                                    $length = $shorts->length;
                                                    $netVolume = $shorts->volume;
                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade3;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 2)
                                                    ];
                                                }

                                                foreach ($farmDataSemi as $semi) {
                                                    $circumference = $semi->circumference;
                                                    $length = $semi->length;
                                                    $netVolume = $semi->volume;
                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade_semi;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 2)
                                                    ];
                                                }

                                                foreach ($farmDataLongs as $longs) {
                                                    $circumference = $longs->circumference;
                                                    $length = $longs->length;
                                                    $netVolume = $longs->volume;
                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade_longs;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 2)
                                                    ];
                                                }

                                                if (count($finalArray) > 0) {

                                                    //WOOD VALUE
                                                    foreach ($finalArray as $item) {
                                                        $totalWoodValue = $totalWoodValue + $item['value'];
                                                    }

                                                    //SUPPLIER TAXES
                                                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                    $calcValue = $totalWoodValue * ($taxValue / 100);
                                                                } else {
                                                                    $calcValue = $totalWoodValue * ($taxValue);
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

                                                    $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                    $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                    $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                    $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                    $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                    $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                    $exchangeRate = 1;
                                                    if ($getContractDetails[0]->currency == 1) {
                                                        $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                        $exchangeRate = $fetchExchangeRate[0]->value;
                                                        $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                        $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                    }

                                                    $dataUpdateFarmDetails = array(
                                                        "exchange_rate" => $exchangeRate,
                                                        "total_value" => $totalWoodValue,
                                                        "wood_value" => $totalWoodValue,
                                                        "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                        "service_cost_withtaxes" => 0,
                                                        "logistic_cost_withtaxes" => 0,
                                                        "supplier_taxes" => $supplierTaxesArrList,
                                                        "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                        "service_taxes" => $supplierServiceTaxesArrList,
                                                        "adjust_taxes" => '',
                                                        "is_adjust_rf" => 0,
                                                        "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                        "service_provider_taxes" => $providerServiceTaxesArrList,
                                                        "adjusted_value" => 0,
                                                        "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                        "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                        "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                        "updated_by" => $userid,
                                                    );

                                                    $updateFarmData = $this->Farm_model->update_farm($farmId, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                    if ($updateFarmData) {

                                                        $dataInventoryLedgerUpdate = array(
                                                            "amount" => 0,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                        $dataContractPriceUpdate = array(
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                        $dataContractMappingUpdate = array(
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                        //SUPPLIER PRICE
                                                        $this->Farm_model->add_supplier_price(
                                                            $purchaseContractId,
                                                            $supplierId,
                                                            $inventoryOrder,
                                                            $createdBy
                                                        );

                                                        //CONTRACT INVENTORY MAPPING
                                                        $dataContractMapping = array(
                                                            "contract_id" => $purchaseContractId,
                                                            "supplier_id" => $supplierId,
                                                            "inventory_order" => $inventoryOrder,
                                                            "total_volume" => $totalNetVolume,
                                                            "invoice_number" => "",
                                                            "created_by" => $createdBy,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 1,
                                                        );

                                                        $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                        $dataInventoryLedger = array(
                                                            "contract_id" => $purchaseContractId,
                                                            "inventory_order" => $inventoryOrder,
                                                            "ledger_type" => 2,
                                                            "expense_date" => $purchaseDate,
                                                            "created_by" => $createdBy,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 1,
                                                            "is_advance_app" => 0,
                                                        );

                                                        if ($woodValueWithSupplierTaxes != 0) {
                                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                        }

                                                        $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                        if (count($getContracts) == 1) {
                                                            $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                            $dataRemainingVolume = array(
                                                                "remaining_volume" => $remainingVolume,
                                                            );

                                                            $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                        }
                                                    }
                                                }
                                            }

                                            $row_farm_array["farmId"] = $farmId + 0;
                                            $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                            $row_farm_array["syncStatus"] = true;
                                            $row_farm_array["tempFarmId"] = $tempFarmId;
                                            array_push($farm_arr_response, $row_farm_array);
                                        } else {

                                            $finalArray = [];
                                            $totalWoodValue = 0;
                                            $supplierTaxesValue = 0;
                                            $supplierTaxesArr = array();
                                            $providerLogisticTaxesArr = array();
                                            $providerServiceTaxesArr = array();
                                            $supplierLogisticTaxesArr = array();
                                            $supplierServiceTaxesArr = array();
                                            $supplierTaxesAdjustArr = array();
                                            $providerLogisticTaxesAdjustArr = array();
                                            $providerServiceTaxesAdjustArr = array();

                                            if ($isClosed === true || $isClosed === false) {

                                                //CALCULATE WOOD VALUE & TAXES
                                                $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 1);
                                                $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 2);
                                                $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($farmMainId, 3);

                                                $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                                foreach ($farmDataShorts as $shorts) {
                                                    $circumference = $shorts->circumference;
                                                    $length = $shorts->length;
                                                    $netVolume = $shorts->volume;

                                                    $totalNetVolume = $totalNetVolume + $netVolume;

                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade3;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 3)
                                                    ];
                                                }

                                                foreach ($farmDataSemi as $semi) {
                                                    $circumference = $semi->circumference;
                                                    $length = $semi->length;
                                                    $netVolume = $semi->volume;
                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade_semi;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 3)
                                                    ];
                                                }

                                                foreach ($farmDataLongs as $longs) {
                                                    $circumference = $longs->circumference;
                                                    $length = $longs->length;
                                                    $netVolume = $longs->volume;
                                                    $totalNetVolume = $totalNetVolume + $netVolume;
                                                    $price = 0;

                                                    foreach ($fetchContractPrice as $range) {
                                                        if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                            $price = $range->pricerange_grade_longs;
                                                            break;
                                                        }
                                                    }

                                                    $finalArray[] = [
                                                        'circumference' => $circumference,
                                                        'length' => $length,
                                                        'price' => $price,
                                                        'volume' => $netVolume,
                                                        'value' => round($price * $netVolume, 3)
                                                    ];
                                                }

                                                if (count($finalArray) > 0) {

                                                    //WOOD VALUE
                                                    foreach ($finalArray as $item) {
                                                        $totalWoodValue = $totalWoodValue + $item['value'];
                                                    }

                                                    //SUPPLIER TAXES
                                                    $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                    $calcValue = $totalWoodValue * ($taxValue / 100);
                                                                } else {
                                                                    $calcValue = $totalWoodValue * ($taxValue);
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

                                                    $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                    $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                    $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                    $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                    $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                    $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                    $exchangeRate = 1;
                                                    if ($getContractDetails[0]->currency == 1) {
                                                        $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                        $exchangeRate = $fetchExchangeRate[0]->value;
                                                        $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                        $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                    }

                                                    $dataUpdateFarmDetails = array(
                                                        "exchange_rate" => $exchangeRate,
                                                        "total_value" => $totalWoodValue,
                                                        "wood_value" => $totalWoodValue,
                                                        "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                        "service_cost_withtaxes" => 0,
                                                        "logistic_cost_withtaxes" => 0,
                                                        "supplier_taxes" => $supplierTaxesArrList,
                                                        "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                        "service_taxes" => $supplierServiceTaxesArrList,
                                                        "adjust_taxes" => '',
                                                        "is_adjust_rf" => 0,
                                                        "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                        "service_provider_taxes" => $providerServiceTaxesArrList,
                                                        "adjusted_value" => 0,
                                                        "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                        "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                        "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                        "updated_by" => $userid,
                                                    );

                                                    $updateFarmData = $this->Farm_model->update_farm($farmMainId, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                    if ($updateFarmData) {

                                                        $dataInventoryLedgerUpdate = array(
                                                            "amount" => 0,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                        $dataContractPriceUpdate = array(
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                        $dataContractMappingUpdate = array(
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 0,
                                                        );

                                                        $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                        //SUPPLIER PRICE
                                                        $this->Farm_model->add_supplier_price(
                                                            $purchaseContractId,
                                                            $supplierId,
                                                            $inventoryOrder,
                                                            $createdBy
                                                        );

                                                        //CONTRACT INVENTORY MAPPING
                                                        $dataContractMapping = array(
                                                            "contract_id" => $purchaseContractId,
                                                            "supplier_id" => $supplierId,
                                                            "inventory_order" => $inventoryOrder,
                                                            "total_volume" => $totalNetVolume,
                                                            "invoice_number" => "",
                                                            "created_by" => $createdBy,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 1,
                                                        );

                                                        $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                        $dataInventoryLedger = array(
                                                            "contract_id" => $purchaseContractId,
                                                            "inventory_order" => $inventoryOrder,
                                                            "ledger_type" => 2,
                                                            "expense_date" => $purchaseDate,
                                                            "created_by" => $createdBy,
                                                            "updated_by" => $createdBy,
                                                            "is_active" => 1,
                                                            "is_advance_app" => 0,
                                                        );

                                                        if ($woodValueWithSupplierTaxes != 0) {
                                                            $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                        }

                                                        $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                        if (count($getContracts) == 1) {
                                                            $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                            $dataRemainingVolume = array(
                                                                "remaining_volume" => $remainingVolume,
                                                            );

                                                            $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                        }
                                                    }
                                                }
                                            }

                                            $row_farm_array["farmId"] = $farmId + 0;
                                            $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                            $row_farm_array["syncStatus"] = true;
                                            $row_farm_array["tempFarmId"] = $tempFarmId;
                                            array_push($farm_arr_response, $row_farm_array);
                                        }
                                    } else {

                                        //INSERT FARM DETAILS
                                        $insertDataFarm = array(
                                            "supplier_id" => $supplierId,
                                            "contract_id" => $purchaseContractId,
                                            "product_id" => $productId,
                                            "product_type_id" => $productTypeId,
                                            "purchase_unit_id" => $purchaseUnitId,
                                            "inventory_order" => $inventoryOrder,
                                            "plate_number" => $truckPlateNumber,
                                            "driver_name" => $truckDriverName,
                                            "purchase_date" => $purchaseDate,
                                            "created_by" => $createdBy,
                                            "updated_by" => $createdBy,
                                            "is_active" => 1,
                                            "created_from" => 1,
                                            "origin_id" => $originId,
                                            "circ_allowance" => $circAllowance,
                                            "length_allowance" => $lengthAllowance,
                                            "is_closed" => $isClosed,
                                            "closed_date" => $closedDate,
                                            "closed_by" => $closedBy,
                                        );

                                        $insertFarm = $this->Farm_model->add_farm($insertDataFarm);

                                        if ($insertFarm > 0) {
                                            $dataFarmData = array();
                                            if (count($farmCapturedData) > 0) {
                                                foreach ($farmCapturedData as $key => $capturevalue) {

                                                    $pieces = $capturevalue["pieces"];
                                                    $circumference = $capturevalue["circumference"];
                                                    $length = $capturevalue["length"];
                                                    $grossVolume = $capturevalue["grossVolume"];
                                                    $netVolume = $capturevalue["netVolume"];
                                                    $capturedTimeStamp = $capturevalue["capturedTimeStamp"];

                                                    $dataFarmData[] = array(
                                                        "farm_id" => $insertFarm,
                                                        "scanned_code" => "",
                                                        "no_of_pieces" => $pieces,
                                                        "circumference" => $circumference,
                                                        "length" => $length,
                                                        "width" => 0,
                                                        "thickness" => 0,
                                                        "gross_volume" => $grossVolume,
                                                        "volume" => $netVolume,
                                                        "volume_pie" => 0,
                                                        "grade_id" => 0,
                                                        "face" => 0,
                                                        "length_export" => 0,
                                                        "width_export" => 0,
                                                        "thickness_export" => 0,
                                                        "volume_bought" => 0,
                                                        "created_by" => $userid,
                                                        "updated_by" => $userid,
                                                        "is_active" => 1,
                                                        "created_date" => date('Y-m-d H:i:s'),
                                                        "updated_date" => date('Y-m-d H:i:s'),
                                                        "captured_timestamp" => $capturedTimeStamp,
                                                    );
                                                }

                                                if (count($dataFarmData) > 0) {
                                                    $insertFarmData = $this->Farm_model->add_farm_data($dataFarmData);

                                                    if ($insertFarmData) {

                                                        //FETCH FARM DATA
                                                        $getFarmData = $this->Farm_model->get_farm_data_by_farm_id($insertFarm);

                                                        $totalGrossVolume = 0;
                                                        $totalNetVolume = 0;
                                                        $totalPieces = 0;

                                                        foreach ($getFarmData as $farmdata) {
                                                            $totalGrossVolume = $totalGrossVolume + $farmdata->gross_volume;
                                                            $totalNetVolume = $totalNetVolume + $farmdata->volume;
                                                            $totalPieces = $totalPieces + $farmdata->no_of_pieces;
                                                        }

                                                        //UPDATE FARM DETAILS
                                                        $dataFarm = array(
                                                            "total_volume" => $totalNetVolume,
                                                            "total_gross_volume" => $totalGrossVolume,
                                                            "total_pieces" => $totalPieces,
                                                        );

                                                        $updateFarm = $this->Farm_model->update_farm($insertFarm, $inventoryOrder, $purchaseContractId, $dataFarm);
                                                    }
                                                }

                                                //CHECK IF FARM IS CLOSED
                                                if ($isClosed === true || $isClosed === false) {

                                                    //CALCULATE WOOD VALUE & TAXES
                                                    $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 1);
                                                    $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 2);
                                                    $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 3);

                                                    $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                                    $finalArray = [];
                                                    $totalWoodValue = 0;
                                                    $supplierTaxesValue = 0;
                                                    $supplierTaxesArr = array();
                                                    $providerLogisticTaxesArr = array();
                                                    $providerServiceTaxesArr = array();
                                                    $supplierLogisticTaxesArr = array();
                                                    $supplierServiceTaxesArr = array();
                                                    $supplierTaxesAdjustArr = array();
                                                    $providerLogisticTaxesAdjustArr = array();
                                                    $providerServiceTaxesAdjustArr = array();

                                                    foreach ($farmDataShorts as $shorts) {
                                                        $circumference = $shorts->circumference;
                                                        $length = $shorts->length;
                                                        $netVolume = $shorts->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade3;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 3)
                                                        ];
                                                    }

                                                    foreach ($farmDataSemi as $semi) {
                                                        $circumference = $semi->circumference;
                                                        $length = $semi->length;
                                                        $netVolume = $semi->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade_semi;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 3)
                                                        ];
                                                    }

                                                    foreach ($farmDataLongs as $longs) {
                                                        $circumference = $longs->circumference;
                                                        $length = $longs->length;
                                                        $netVolume = $longs->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade_longs;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 3)
                                                        ];
                                                    }

                                                    if (count($finalArray) > 0) {

                                                        //WOOD VALUE
                                                        foreach ($finalArray as $item) {
                                                            $totalWoodValue = $totalWoodValue + $item['value'];
                                                        }

                                                        //SUPPLIER TAXES
                                                        $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                        $calcValue = $totalWoodValue * ($taxValue / 100);
                                                                    } else {
                                                                        $calcValue = $totalWoodValue * ($taxValue);
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

                                                        $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                        $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                        $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                        $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                        $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                        $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                        $exchangeRate = 1;
                                                        if ($getContractDetails[0]->currency == 1) {
                                                            $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                            $exchangeRate = $fetchExchangeRate[0]->value;
                                                            $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                            $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                        }

                                                        $dataUpdateFarmDetails = array(
                                                            "exchange_rate" => $exchangeRate,
                                                            "total_value" => $totalWoodValue,
                                                            "wood_value" => $totalWoodValue,
                                                            "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                            "service_cost_withtaxes" => 0,
                                                            "logistic_cost_withtaxes" => 0,
                                                            "supplier_taxes" => $supplierTaxesArrList,
                                                            "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                            "service_taxes" => $supplierServiceTaxesArrList,
                                                            "adjust_taxes" => '',
                                                            "is_adjust_rf" => 0,
                                                            "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                            "service_provider_taxes" => $providerServiceTaxesArrList,
                                                            "adjusted_value" => 0,
                                                            "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                            "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                            "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                            "updated_by" => $userid,
                                                        );

                                                        $updateFarmData = $this->Farm_model->update_farm($insertFarm, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                        if ($updateFarmData) {

                                                            $dataInventoryLedgerUpdate = array(
                                                                "amount" => 0,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                            $dataContractPriceUpdate = array(
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                            $dataContractMappingUpdate = array(
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                            //SUPPLIER PRICE
                                                            $this->Farm_model->add_supplier_price(
                                                                $purchaseContractId,
                                                                $supplierId,
                                                                $inventoryOrder,
                                                                $createdBy
                                                            );

                                                            //CONTRACT INVENTORY MAPPING
                                                            $dataContractMapping = array(
                                                                "contract_id" => $purchaseContractId,
                                                                "supplier_id" => $supplierId,
                                                                "inventory_order" => $inventoryOrder,
                                                                "total_volume" => $totalNetVolume,
                                                                "invoice_number" => "",
                                                                "created_by" => $createdBy,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 1,
                                                            );

                                                            $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                            $dataInventoryLedger = array(
                                                                "contract_id" => $purchaseContractId,
                                                                "inventory_order" => $inventoryOrder,
                                                                "ledger_type" => 2,
                                                                "expense_date" => $purchaseDate,
                                                                "created_by" => $createdBy,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 1,
                                                                "is_advance_app" => 0,
                                                            );

                                                            if ($woodValueWithSupplierTaxes != 0) {
                                                                $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                            }

                                                            $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                            if (count($getContracts) == 1) {
                                                                $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                                $dataRemainingVolume = array(
                                                                    "remaining_volume" => $remainingVolume,
                                                                );

                                                                $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                            }
                                                        }
                                                    }
                                                }

                                                $row_farm_array["farmId"] = $insertFarm + 0;
                                                $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                                $row_farm_array["syncStatus"] = true;
                                                $row_farm_array["tempFarmId"] = $tempFarmId;
                                                array_push($farm_arr_response, $row_farm_array);
                                            } else {

                                                $finalArray = [];
                                                $totalWoodValue = 0;
                                                $supplierTaxesValue = 0;
                                                $supplierTaxesArr = array();
                                                $providerLogisticTaxesArr = array();
                                                $providerServiceTaxesArr = array();
                                                $supplierLogisticTaxesArr = array();
                                                $supplierServiceTaxesArr = array();
                                                $supplierTaxesAdjustArr = array();
                                                $providerLogisticTaxesAdjustArr = array();
                                                $providerServiceTaxesAdjustArr = array();

                                                if ($isClosed === true || $isClosed === false) {

                                                    //CALCULATE WOOD VALUE & TAXES
                                                    $farmDataShorts = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 1);
                                                    $farmDataSemi = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 2);
                                                    $farmDataLongs = $this->Farm_model->get_farm_data_by_farm_id_and_length($insertFarm, 3);

                                                    $fetchContractPrice = $this->Farm_model->fetch_contract_prices_for_farm($purchaseContractId);

                                                    foreach ($farmDataShorts as $shorts) {
                                                        $circumference = $shorts->circumference;
                                                        $length = $shorts->length;
                                                        $netVolume = $shorts->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade3;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 2)
                                                        ];
                                                    }

                                                    foreach ($farmDataSemi as $semi) {
                                                        $circumference = $semi->circumference;
                                                        $length = $semi->length;
                                                        $netVolume = $semi->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade_semi;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 2)
                                                        ];
                                                    }

                                                    foreach ($farmDataLongs as $longs) {
                                                        $circumference = $longs->circumference;
                                                        $length = $longs->length;
                                                        $netVolume = $longs->volume;
                                                        $totalNetVolume = $totalNetVolume + $netVolume;
                                                        $price = 0;

                                                        foreach ($fetchContractPrice as $range) {
                                                            if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                                                $price = $range->pricerange_grade_longs;
                                                                break;
                                                            }
                                                        }

                                                        $finalArray[] = [
                                                            'circumference' => $circumference,
                                                            'length' => $length,
                                                            'price' => $price,
                                                            'volume' => $netVolume,
                                                            'value' => round($price * $netVolume, 2)
                                                        ];
                                                    }

                                                    if (count($finalArray) > 0) {

                                                        //WOOD VALUE
                                                        foreach ($finalArray as $item) {
                                                            $totalWoodValue = $totalWoodValue + $item['value'];
                                                        }

                                                        //SUPPLIER TAXES
                                                        $getSupplierTaxes = $this->Master_model->get_supplier_taxes($supplierId);
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
                                                                        $calcValue = $totalWoodValue * ($taxValue / 100);
                                                                    } else {
                                                                        $calcValue = $totalWoodValue * ($taxValue);
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

                                                        $woodValueWithSupplierTaxes = $totalWoodValue + $supplierTaxesValue;
                                                        $supplierTaxesArrList = implode(', ', $supplierTaxesArr);
                                                        $providerLogisticTaxesArrList = implode(', ', $providerLogisticTaxesArr);
                                                        $providerServiceTaxesArrList = implode(', ', $providerServiceTaxesArr);
                                                        $supplierLogisticTaxesArrList = implode(', ', $supplierLogisticTaxesArr);
                                                        $supplierServiceTaxesArrList = implode(', ', $supplierServiceTaxesArr);

                                                        $exchangeRate = 1;
                                                        if ($getContractDetails[0]->currency == 1) {
                                                            $fetchExchangeRate = $this->Exchange_model->fetch_exchange_rate_by_date($purchaseDate);
                                                            $exchangeRate = $fetchExchangeRate[0]->value;
                                                            $totalWoodValue = $totalWoodValue * $exchangeRate;
                                                            $woodValueWithSupplierTaxes = $woodValueWithSupplierTaxes * $exchangeRate;
                                                        }

                                                        $dataUpdateFarmDetails = array(
                                                            "exchange_rate" => $exchangeRate,
                                                            "total_value" => $totalWoodValue,
                                                            "wood_value" => $totalWoodValue,
                                                            "wood_value_withtaxes" => $woodValueWithSupplierTaxes,
                                                            "service_cost_withtaxes" => 0,
                                                            "logistic_cost_withtaxes" => 0,
                                                            "supplier_taxes" => $supplierTaxesArrList,
                                                            "logistic_taxes" => $supplierLogisticTaxesArrList,
                                                            "service_taxes" => $supplierServiceTaxesArrList,
                                                            "adjust_taxes" => '',
                                                            "is_adjust_rf" => 0,
                                                            "logistic_provider_taxes" => $providerLogisticTaxesArrList,
                                                            "service_provider_taxes" => $providerServiceTaxesArrList,
                                                            "adjusted_value" => 0,
                                                            "supplier_taxes_array" => json_encode($supplierTaxesAdjustArr),
                                                            "logistics_taxes_array" => json_encode($providerLogisticTaxesAdjustArr),
                                                            "service_taxes_array" => json_encode($providerServiceTaxesAdjustArr),
                                                            "updated_by" => $userid,
                                                        );

                                                        $updateFarmData = $this->Farm_model->update_farm($insertFarm, $inventoryOrder, $purchaseContractId, $dataUpdateFarmDetails);

                                                        if ($updateFarmData) {

                                                            $dataInventoryLedgerUpdate = array(
                                                                "amount" => 0,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateInventoryLedger = $this->Farm_model->update_inventory_ledger($inventoryOrder, $purchaseContractId, $dataInventoryLedgerUpdate);

                                                            $dataContractPriceUpdate = array(
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateContractPrice = $this->Farm_model->update_contract_price($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                            $dataContractMappingUpdate = array(
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 0,
                                                            );

                                                            $updateContractMapping = $this->Farm_model->update_inventory_mapping($inventoryOrder, $purchaseContractId, $dataContractPriceUpdate);

                                                            //SUPPLIER PRICE
                                                            $this->Farm_model->add_supplier_price(
                                                                $purchaseContractId,
                                                                $supplierId,
                                                                $inventoryOrder,
                                                                $createdBy
                                                            );

                                                            //CONTRACT INVENTORY MAPPING
                                                            $dataContractMapping = array(
                                                                "contract_id" => $purchaseContractId,
                                                                "supplier_id" => $supplierId,
                                                                "inventory_order" => $inventoryOrder,
                                                                "total_volume" => $totalNetVolume,
                                                                "invoice_number" => "",
                                                                "created_by" => $createdBy,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 1,
                                                            );

                                                            $this->Farm_model->add_contract_inventory_mapping($dataContractMapping);

                                                            $dataInventoryLedger = array(
                                                                "contract_id" => $purchaseContractId,
                                                                "inventory_order" => $inventoryOrder,
                                                                "ledger_type" => 2,
                                                                "expense_date" => $purchaseDate,
                                                                "created_by" => $createdBy,
                                                                "updated_by" => $createdBy,
                                                                "is_active" => 1,
                                                                "is_advance_app" => 0,
                                                            );

                                                            if ($woodValueWithSupplierTaxes != 0) {
                                                                $this->Farm_model->add_inventory_ledger($dataInventoryLedger, $woodValueWithSupplierTaxes, 1, $supplierId);
                                                            }

                                                            $getContracts = $this->Contract_model->get_contracts_by_contractid($purchaseContractId);
                                                            if (count($getContracts) == 1) {
                                                                $remainingVolume = $getContracts[0]->remaining_volume - $totalNetVolume;

                                                                $dataRemainingVolume = array(
                                                                    "remaining_volume" => $remainingVolume,
                                                                );

                                                                $this->Contract_model->update_purchase_contract_volume($dataRemainingVolume, $purchaseContractId, $supplierId);
                                                            }
                                                        }
                                                    }
                                                }

                                                $row_farm_array["farmId"] = $insertFarm + 0;
                                                $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                                $row_farm_array["syncStatus"] = true;
                                                $row_farm_array["tempFarmId"] = $tempFarmId;
                                                array_push($farm_arr_response, $row_farm_array);
                                            }
                                        } else {
                                            $row_farm_array["farmId"] = 0;
                                            $row_farm_array["inventoryOrder"] = $inventoryOrder;
                                            $row_farm_array["syncStatus"] = false;
                                            $row_farm_array["tempFarmId"] = $tempFarmId;
                                            array_push($farm_arr_response, $row_farm_array);
                                        }
                                    }
                                }
                            }
                        }

                        //ASYNC EMAIL
                        $data = json_encode($farmData, true);
                        $url = 'https://portal.codringreen.com/api/sendnotificationsemail';
                        $cmd = "curl -X POST \"$url\" -H \"Content-Type: application/json\" -d '$data' > /dev/null 2>/dev/null &";
                        exec($cmd);

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $farm_arr_response;
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
            } else {
                $Return["status"] = false;
                $Return["message"] = "Bad Header Details";
                http_response_code(400);
                $this->output($Return);
            }
        } catch (Exception $e) {
            $Return["status"] = false;
            $Return["message"] = $e->getMessage();
            http_response_code(500);
            $this->output($Return);
        }
    }
}
