<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Downloadmasters extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Terralogin_model");
        $this->load->model("Terramaster_model");
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
            if ($this->input->method(TRUE) != "GET") {
                return $this->output([
                    "status" => false,
                    "message" => "Invalid Request Method"
                ]);
            }

            // 🔹 Get Authorization Header
            $headers = getallheaders();

            $authHeader = null;

            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            }

            if (!$authHeader) {
                return $this->unauthorized();
            }

            $parts = explode(" ", $authHeader);
            if (count($parts) !== 2 || strtolower($parts[0]) !== 'bearer') {
                return $this->unauthorized();
            }

            $tokenStr = $parts[1];

            $token = JWT::decode($tokenStr, JWT_SECRET, ['HS256']);

            $userid   = $token->userid;
            $originid = $token->originid;
            $roleid   = $token->roleid;

            if ($userid <= 0) {
                return $this->unauthorized();
            }

            // 🔹 Validate user
            $checkUserExists = $this->Terralogin_model
                ->check_user_exists_terra_app($userid, $originid);

            if (!$checkUserExists) {
                return $this->unauthorized();
            }

            // 🔹 Fast role check (optimized)
            $roles = explode(',', $roleid);

            // 🔹 Final response
            $return_arr_suppliers = [];
            $return_arr_warehouses = [];
            $return_arr_contracts = [];
            $return_arr_measurement_systems = [];
            $return_arr_farm_inventory_orders = [];
            $return_arr_reception_inventory_orders = [];
            $return_arr_shipping_lines = [];
            $return_arr_dispatch_containers = [];
            $return_arr_products = [];
            $return_arr_product_types = [];
            $return_arr_girth_classification = [];
            $return_arr_length_classification = [];

            if (!in_array('9', $roles) && !in_array('7', $roles)) {
                return $this->output([
                    "status" => false,
                    "message" => "Access denied (role)"
                ]);
            }

            // 🚀 OPTIMIZED DATA FETCH (ONLY 3 QUERIES)

            if (in_array('9', $roles) || in_array('7', $roles)) {

                // SUPPLIERS, PRODUCTS & PRODUCT TYPES
                $dataSuppliers = $this->Terramaster_model->get_suppliers_full_data($originid);

                // 🔥 Fix JSON string → object
                foreach ($dataSuppliers as &$row) {

                    $row->supplierId = (int)$row->supplierId;

                    $row->supplierProducts = json_decode($row->supplierProducts);
                }

                $return_arr_suppliers = $dataSuppliers;

                // WAREHOUSES
                $dataWarehouses = $this->Terramaster_model->get_warehouses_by_origin($originid);

                foreach ($dataWarehouses as &$rowWh) {
                    $rowWh->id = (int)$rowWh->id;
                }

                $return_arr_warehouses = $dataWarehouses;

                // PURCHASE CONTRACTS
                $dataContracts = $this->Terramaster_model->fetch_purchase_contract_origin($originid);

                // 🔥 Type casting (important)
                foreach ($dataContracts as &$c) {
                    $c->contractId = (int)$c->contractId;
                    $c->supplierId = (int)$c->supplierId;
                    $c->purchaseUnitId = (int)$c->purchaseUnitId;
                    $c->purchaseAllowance = (float)$c->purchaseAllowance;
                    $c->purchaseAllowanceLength = (float)$c->purchaseAllowanceLength;
                    $c->product = (int)$c->product;
                    $c->productType = (int)$c->productType;
                }

                $return_arr_contracts = $dataContracts;

                // MEASUREMENT SYSTEMS
                $dataMeasurementSystems = $this->Terramaster_model->get_measurement_systems_by_origin($originid);
                $dataFormulas = $this->Terramaster_model->get_formulas_grouped();

                // Convert formulas to map [measurement_system_id => formulas]
                $formulaMap = [];

                foreach ($dataFormulas as $f) {
                    $formulaMap[$f->measurement_system_id] = json_decode($f->formulas);
                }

                foreach ($dataMeasurementSystems as &$ms) {
                    $ms->id = (int)$ms->id;
                    $ms->productTypeId = (int)$ms->productTypeId;

                    $ms->formulas = isset($formulaMap[$ms->id]) ? $formulaMap[$ms->id] : [];
                }

                $return_arr_measurement_systems = $dataMeasurementSystems;

                // FARM INVENTORY ORDERS
                $dataFarmInventoryOrders = $this->Terramaster_model->get_farm_inventory_orders_by_origin($originid);

                foreach ($dataFarmInventoryOrders as &$io) {
                    $io->supplierId = (int)$io->supplierId;
                }

                $return_arr_farm_inventory_orders = $dataFarmInventoryOrders;

                // RECEPTION INVENTORY ORDERS
                $dataReceptionInventoryOrders = $this->Terramaster_model->get_reception_inventory_orders_by_origin($originid, 2025);

                foreach ($dataReceptionInventoryOrders as &$io) {
                    $io->supplierId = (int)$io->supplierId;
                }

                $return_arr_reception_inventory_orders = $dataReceptionInventoryOrders;

                // SHIPPING LINES
                $dataShippingLines = $this->Terramaster_model->get_shipping_lines_by_origin($originid);

                foreach ($dataShippingLines as &$rowSl) {
                    $rowSl->id = (int)$rowSl->id;
                }

                $return_arr_shipping_lines = $dataShippingLines;

                // DISPATCH CONTAINERS
                $dataDispatchContainers = $this->Terramaster_model->get_dispatch_containers_by_origin($originid, 2025);

                foreach ($dataDispatchContainers as &$dc) {
                    $dc->shippingLineId = (int)$dc->shippingLineId;
                }

                $return_arr_dispatch_containers = $dataDispatchContainers;

                // PRODUCTS
                $dataProducts = $this->Terramaster_model->get_products_by_origin($originid);

                foreach ($dataProducts as &$p) {
                    $p->productId = (int)$p->productId;
                }

                $return_arr_products = $dataProducts;

                // PRODUCT TYPES
                $dataProductTypes = $this->Terramaster_model->get_product_types_by_origin();

                foreach ($dataProductTypes as &$pt) {
                    $pt->typeId = (int)$pt->typeId;
                }

                $return_arr_product_types = $dataProductTypes;

                // GIRTH CLASSIFICATION
                $dataGirthClassification = $this->Terramaster_model->get_girth_classification_by_origin($originid);
                foreach ($dataGirthClassification as &$gc) {
                    $gc->id = (int)$gc->id;
                }

                $return_arr_girth_classification = $dataGirthClassification;

                // LENGTH CLASSIFICATION
                $dataLengthClassification = $this->Terramaster_model->get_length_classification_by_origin($originid);
                foreach ($dataLengthClassification as &$lc) {
                    $lc->id = (int)$lc->id;
                }

                $return_arr_length_classification = $dataLengthClassification;

                // FORMULA
                $dataFormula = $this->Terramaster_model->get_formula_by_origin($originid);
            }

            return $this->output([
                "status" => true,
                "message" => "",
                "version" => 1,
                "data" => [
                    "suppliers" => $return_arr_suppliers,
                    "warehouses" => $return_arr_warehouses,
                    "purchaseContracts" => $return_arr_contracts,
                    "measurementSystems" => $return_arr_measurement_systems, 
                    "shippingLines" => $return_arr_shipping_lines,
                    "farmInventoryOrders" => $return_arr_farm_inventory_orders, 
                    "receptionInventoryOrders" => $return_arr_reception_inventory_orders, 
                    "dispatchContainers" => $return_arr_dispatch_containers,
                    "products" => $return_arr_products,
                    "productTypes" => $return_arr_product_types,
                    "girthClassification" => $return_arr_girth_classification,
                    "lengthClassification" => $return_arr_length_classification
                ]
            ]);
        } catch (Exception $e) {

            return $this->output([
                "status" => false,
                "message" => "Internal Server Error"
            ]);
        }
    }

    private function unauthorized()
    {
        return $this->output([
            "status" => false,
            "message" => "Unauthorized"
        ]);
    }
}
