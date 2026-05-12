    <?php
    defined("BASEPATH") or exit("No direct script access allowed");

    class Downloadtransactions extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->model("Terralogin_model");
            $this->load->model("Terratransactions_model");
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

                if (!in_array('9', $roles) && !in_array('7', $roles)) {
                    return $this->output([
                        "status" => false,
                        "message" => "Access denied (role)"
                    ], 403);
                }

                // 🔹 Final response
                $return_arr_farm_inventory_orders = [];
                $return_arr_reception_inventory_orders = [];
                $return_arr_dispatch_containers = [];
                $return_arr_reception_details = [];

                $serverTime = time();
                $lastSync = (int)$this->input->get("lastSync");

                // 🚀 OPTIMIZED DATA FETCH
                if (in_array('9', $roles) || in_array('7', $roles)) {



                    // FARM INVENTORY ORDERS
                    $dataFarmInventoryOrders = $this->Terratransactions_model->get_farm_inventory_orders_by_origin($originid);

                    foreach ($dataFarmInventoryOrders as &$io) {
                        $io->supplierId = (int)$io->supplierId;
                    }

                    $return_arr_farm_inventory_orders = $dataFarmInventoryOrders;

                    // RECEPTION INVENTORY ORDERS
                    $dataReceptionInventoryOrders = $this->Terratransactions_model->get_reception_inventory_orders_by_origin($originid, 2025);

                    foreach ($dataReceptionInventoryOrders as &$io) {
                        $io->supplierId = (int)$io->supplierId;
                    }

                    $return_arr_reception_inventory_orders = $dataReceptionInventoryOrders;

                    // DISPATCH CONTAINERS
                    $dataDispatchContainers = $this->Terratransactions_model->get_dispatch_containers_by_origin($originid, 2025);

                    foreach ($dataDispatchContainers as &$dc) {
                        $dc->shippingLineId = (int)$dc->shippingLineId;
                    }

                    $return_arr_dispatch_containers = $dataDispatchContainers;

                    // RECEPTION DETAILS
                    $dataReceptionDetails = $this->Terratransactions_model->get_reception_details($originid, 2025);
                    foreach ($dataReceptionDetails as &$rd) {
                        $rd->receptionId = (int)$rd->receptionId;
                        $rd->warehouseId = (int)$rd->warehouseId;
                        $rd->supplierId = (int)$rd->supplierId;
                        $rd->supplierProductId = (int)$rd->supplierProductId;
                        $rd->supplierProductTypeId = (int)$rd->supplierProductTypeId;
                        $rd->measurementSystemId = (int)$rd->measurementSystemId;
                        $rd->isClosed = (bool)$rd->isClosed;
                        $rd->closedBy = (int)$rd->closedBy;
                        $rd->totalGrossVolume = (float)$rd->totalGrossVolume;
                        $rd->totalNetVolume = (float)$rd->totalNetVolume;
                        $rd->totalPieces = (int)$rd->totalPieces;
                        $rd->isCreateFarm = (bool)$rd->isCreateFarm;
                        $rd->contractId = (int)$rd->contractId;
                    }

                    $return_arr_reception_details = $dataReceptionDetails;
                }

                return $this->output([
                    "status" => true,
                    "message" => "",
                    "serverTime" => $serverTime,
                    "data" => [
                        "farmInventoryOrders" => $return_arr_farm_inventory_orders,
                        "receptionInventoryOrders" => $return_arr_reception_inventory_orders,
                        "dispatchContainers" => $return_arr_dispatch_containers,
                        "receptionDetails" => $return_arr_reception_details,
                    ]
                ]);
            } catch (Exception $e) {
                return $this->output([
                    "status" => false,
                    "message" => "Internal Server Error"
                ], 500);
            }
        }
    }
