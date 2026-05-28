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
                $return_arr_dispatch_details = [];

                $serverTime = time();
                $lastSync = (int)$this->input->get("lastSync");

                // 🚀 OPTIMIZED DATA FETCH
                if (in_array('9', $roles) || in_array('7', $roles)) {



                    // FARM INVENTORY ORDERS
                    $dataFarmInventoryOrders = $this->Terratransactions_model->get_farm_inventory_orders_by_origin($originid);

                    foreach ($dataFarmInventoryOrders as &$io) {
                        $io->supplierId = (int)$io->supplierId;
                        $io->isFromReception = (bool)$io->isFromReception;
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

                    // ======================
                    // RECEPTION DETAILS
                    // ======================
                    $dataReceptionDetails = $this->Terratransactions_model->get_reception_details($originid);

                    // ======================
                    // GET RECEPTION IDS
                    // ======================
                    $receptionIds = [];

                    foreach ($dataReceptionDetails as $rd) {
                        $receptionIds[] = (int)$rd->receptionId;
                    }

                    // ======================
                    // GET ALL RECEPTION DATA
                    // ======================
                    $allReceptionData = $this->Terratransactions_model->get_reception_data_by_ids($receptionIds);

                    // ======================
                    // GROUP RECEPTION DATA
                    // ======================
                    $groupedReceptionData = [];

                    foreach ($allReceptionData as $data) {

                        $data->receptionDataId = (int)$data->receptionDataId;
                        $data->receptionId = (int)$data->receptionId;
                        $data->circumference = (float)$data->circumference;
                        $data->length = (float)$data->length;
                        $data->thickness = (float)$data->thickness;
                        $data->width = (float)$data->width;
                        $data->pieces = (int)$data->pieces;
                        $data->grossVolume = (float)$data->grossVolume;
                        $data->netVolume = (float)$data->netVolume;
                        $data->volumePie = (float)$data->volumePie;
                        $data->createdAt = (int)$data->createdAt;
                        $data->updatedAt = (int)$data->updatedAt;

                        // GROUP BY RECEPTION ID
                        $groupedReceptionData[$data->receptionId][] = $data;
                    }

                    // ======================
                    // FORMAT RECEPTION DETAILS
                    // ======================
                    foreach ($dataReceptionDetails as &$rd) {

                        $rd->receptionId = (int)$rd->receptionId;
                        $rd->warehouseId = (int)$rd->warehouseId;
                        $rd->supplierId = (int)$rd->supplierId;
                        $rd->supplierProductId = (int)$rd->supplierProductId;
                        $rd->supplierProductTypeId = (int)$rd->supplierProductTypeId;
                        $rd->measurementSystemId = (int)$rd->measurementSystemId;
                        $rd->isClosed = (bool)$rd->isClosed;
                        $rd->closedDate = $rd->closedDate ? strtotime($rd->closedDate) * 1000 : null;
                        $rd->closedBy = (int)$rd->closedBy;
                        $rd->totalGrossVolume = (float)$rd->totalGrossVolume;
                        $rd->totalNetVolume = (float)$rd->totalNetVolume;
                        $rd->totalPieces = (int)$rd->totalPieces;
                        $rd->isCreateFarm = (bool)$rd->isCreateFarm;
                        $rd->contractId = (int)$rd->contractId;
                        $rd->productId = (int)$rd->productId;
                        $rd->productTypeId = (int)$rd->productTypeId;
                        $rd->capturedTimestamp = (int)$rd->capturedTimestamp;
                        $rd->updatedAt = (int)$rd->updateAt;

                        // ======================
                        // ATTACH RECEPTION DATA
                        // ======================
                        $rd->receptionData = $groupedReceptionData[$rd->receptionId] ?? [];
                    }

                    $return_arr_reception_details = $dataReceptionDetails;

                    // ======================
                    // DISPATCH DETAILS
                    // ======================
                    $dataDispatchDetails = $this->Terratransactions_model->get_dispatch_details($originid);

                    // ======================
                    // GET DISPATCH IDS
                    // ======================
                    $dispatchIds = [];

                    foreach ($dataDispatchDetails as $dd) {
                        $dispatchIds[] = (int)$dd->dispatchId;
                    }

                    // ======================
                    // GET ALL RECEPTION DATA
                    // ======================
                    $allDispatchData = $this->Terratransactions_model->get_dispatch_data_by_ids($dispatchIds);

                    // ======================
                    // GROUP DISPATCH DATA
                    // ======================
                    $groupedDispatchData = [];

                    foreach ($allDispatchData as $dispatchdata) {

                        $dispatchdata->dispatchDataId = (int)$dispatchdata->dispatchDataId;
                        $dispatchdata->dispatchId = (int)$dispatchdata->dispatchId;
                        $dispatchdata->receptionDataId = (int)$dispatchdata->receptionDataId;
                        $dispatchdata->receptionId = (int)$dispatchdata->receptionId;
                        $dispatchdata->grossVolume = (float)$dispatchdata->grossVolume;
                        $dispatchdata->netVolume = (float)$dispatchdata->netVolume;
                        $dispatchdata->volumePie = (float)$dispatchdata->volumePie;
                        $dispatchdata->pieces = (int)$dispatchdata->pieces;
                        $dispatchdata->createdAt = (int)$dispatchdata->createdAt;
                        $dispatchdata->updatedAt = (int)$dispatchdata->updatedAt;

                        // GROUP BY DISPATCH ID
                        $groupedDispatchData[$dispatchdata->dispatchId][] = $dispatchdata;
                    }


                    // ======================
                    // FORMAT DISPATCH DETAILS
                    // ======================
                    foreach ($dataDispatchDetails as &$dd) {

                        $dd->dispatchId = (int)$dd->dispatchId;
                        $dd->warehouseId = (int)$dd->warehouseId;
                        $dd->shippingLineId = (int)$dd->shippingLineId;
                        $dd->productId = (int)$dd->productId;
                        $dd->productTypeId = (int)$dd->productTypeId;
                        $dd->createdAt = (int)$dd->createdAt;
                        $dd->updatedAt = (int)$dd->updatedAt;
                        $dd->isClosed = (bool)$dd->isClosed;
                        $dd->closedDate = $dd->closedDate ? strtotime($dd->closedDate) * 1000 : null;
                        $dd->closedBy = (int)$dd->closedBy;
                        $dd->totalGrossVolume = (float)$dd->totalGrossVolume;
                        $dd->totalNetVolume = (float)$dd->totalNetVolume;
                        $dd->totalPieces = (int)$dd->totalPieces;
                        $dd->categoryId = (int)$dd->categoryId;

                        // ======================
                        // ATTACH DISPATCH DATA
                        // ======================
                        $dd->containerData = $groupedDispatchData[$dd->dispatchId] ?? [];
                    }

                    $return_arr_dispatch_details = $dataDispatchDetails;
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
                        "dispatchDetails" => $return_arr_dispatch_details,
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
