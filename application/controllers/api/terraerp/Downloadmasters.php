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

    private function output($data = array(), $code = 200)
    {
        http_response_code($code);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }


    public function index()
    {
        try {

            // =========================
            // REQUEST METHOD CHECK
            // =========================
            if ($this->input->method(TRUE) !== 'GET') {
                return $this->output([
                    'status' => false,
                    'message' => 'Invalid request method'
                ], 405);
            }

            // =========================
            // AUTHORIZATION
            // =========================
            $headers = apache_request_headers();
            $requestBearerToken = '';
            foreach ($headers as $header => $value) {
                if ($header == "Authorization") {
                    list($a, $b) = explode(" ", $value);
                    $requestBearerToken = $b;
                }
            }

            if (empty($requestBearerToken)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Authorization token missing'
                ], 401);
            }

            $token = JWT::decode($requestBearerToken, JWT_SECRET);
            $userid   = $token->userid ?? 0;
            $originid = $token->originid ?? 0;
            $roleid   = $token->roleid ?? 0;

            // =========================
            // USER VALIDATION
            // =========================

            if (!$this->Terralogin_model->check_user_exists_terra_app($userid, $originid)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Unauthorized user'
                ], 401);
            }

            // 🔹 Fast role check (optimized)
            $roles = explode(',', $roleid);

            // 🔹 Final response
            $return_arr_suppliers = [];
            $return_arr_warehouses = [];
            $return_arr_contracts = [];
            $return_arr_measurement_systems = [];
            $return_arr_shipping_lines = [];
            $return_arr_products = [];
            $return_arr_product_types = [];
            $return_arr_container_categories = [];

            if (!in_array('9', $roles) && !in_array('7', $roles)) {
                return $this->output([
                    "status" => false,
                    "message" => "Access denied (role)"
                ], 403);
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

                // SHIPPING LINES
                $dataShippingLines = $this->Terramaster_model->get_shipping_lines_by_origin($originid);

                foreach ($dataShippingLines as &$rowSl) {
                    $rowSl->id = (int)$rowSl->id;
                }

                $return_arr_shipping_lines = $dataShippingLines;

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

                // CONTAINER CATEGORIES
                $dataContainerCategories = $this->Terramaster_model->get_container_categories();

                foreach ($dataContainerCategories as &$cc) {
                    $cc->id = (int)$cc->id;
                    $cc->productTypeId = (int)$cc->productTypeId;
                }

                $return_arr_container_categories = $dataContainerCategories;
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
                    "products" => $return_arr_products,
                    "productTypes" => $return_arr_product_types,
                    "containerCategories" => $return_arr_container_categories,
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
