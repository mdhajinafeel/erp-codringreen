<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Sync extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Terralogin_model");
        $this->load->model("Terramaster_model");
        $this->load->model("Terrasync_model");
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

    // =====================
    // IMAGE UPLOAD
    // =====================
    public function uploadcontainerphotos()
    {
        try {
            // =========================
            // REQUEST METHOD CHECK
            // =========================

            if ($this->input->method(TRUE) !== 'POST') {
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

            // =========================
            // USER VALIDATION
            // =========================

            if (!$this->Terralogin_model->check_user_exists_terra_app($userid, $originid)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Unauthorized user'
                ], 401);
            }

            // =========================
            // REQUIRED PARAMS
            // =========================

            $tempContainerImageId = $this->input->post('tempContainerImageId');
            $tempDispatchId = $this->input->post('tempDispatchId');

            if (empty($tempContainerImageId) || empty($tempDispatchId)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Required parameters missing'
                ], 400);
            }

            // =========================
            // FILE CHECK
            // =========================

            if (!isset($_FILES['image'])) {
                return $this->output([
                    'status' => false,
                    'message' => 'Image file missing'
                ], 400);
            }

            // =========================
            // CREATE DIRECTORY
            // =========================

            $uploadPath = FCPATH . 'uploads/containerimages/' . $tempDispatchId . '/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // =========================
            // FILE EXTENSION
            // =========================
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            // =========================
            // CUSTOM FILE NAME
            // =========================

            $customFileName = 'container_' . $tempContainerImageId . '_' . rand(1000000, 9999999) . '.' . $ext;

            // =========================
            // UPLOAD CONFIG
            // =========================

            $config = [
                'upload_path' => $uploadPath,
                'allowed_types' => 'jpg|jpeg|png|webp',
                'file_name' => $customFileName,
                'overwrite' => false,
                'max_size' => 10240 // 10MB
            ];

            $this->load->library('upload', $config);

            // =========================
            // UPLOAD FILE
            // =========================

            if (!$this->upload->do_upload('image')) {
                return $this->output([
                    'status' => false,
                    'message' => $this->upload->display_errors('', '')
                ], 400);
            }

            // =========================
            // FILE DATA
            // =========================

            $fileData = $this->upload->data();

            $imageUrl = base_url('uploads/containerimages/' . $tempDispatchId . '/' . $fileData['file_name']);

            // =========================
            // SUCCESS RESPONSE
            // =========================
            return $this->output([
                'status' => true,
                'message' => 'Image uploaded successfully',
                'tempContainerImageId' => $tempContainerImageId,
                'tempDispatchId' => $tempDispatchId,
                'url' => $imageUrl
            ]);
        } catch (Exception $e) {
            return $this->output([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    // =====================
    // SYNC DATA
    // =====================
    public function syncdata()
    {
        try {

            // =========================
            // REQUEST METHOD CHECK
            // =========================

            if ($this->input->method(TRUE) !== 'POST') {
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

            // =========================
            // USER VALIDATION
            // =========================

            if (!$this->Terralogin_model->check_user_exists_terra_app($userid, $originid)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Unauthorized user'
                ], 401);
            }

            // =========================
            // INPUT
            // =========================
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                return $this->output([
                    'status' => false,
                    'message' => 'Invalid JSON payload'
                ], 400);
            }

            // =========================
            // SAFE DEFAULTS
            // =========================
            $input['deviceId'] = $input['deviceId'] ?? '';
            $input['receptionDetails'] = $input['receptionDetails'] ?? [];
            $input['receptionData'] = $input['receptionData'] ?? [];
            $input['dispatchDetails'] = $input['dispatchDetails'] ?? [];
            $input['containerData'] = $input['containerData'] ?? [];

            // =========================
            // RESPONSE
            // =========================
            $response = $this->initialize_sync_response();

            // =========================
            // START TRANSACTION
            // =========================
            $this->db->trans_begin();

            // =================
            // RECEPTION DETAILS
            // =================
            $farmReceptionTempIds = $this->process_reception_details($input, $userid, $originid, $response);

            // =========================
            // RECEPTION DATA
            // =========================
            $this->process_reception_data($input, $userid, $response);

            // =====================================================
            // CREATE / UPDATE FARM DETAILS
            // =====================================================
            $this->process_farm_data($farmReceptionTempIds, $userid, $originid);

            // =================
            // CONTAINER DETAILS
            // =================
            $this->process_dispatch_details($input, $userid, $originid, $response);

            // =========================
            // DISPATCH DATA
            // =========================
            $this->process_container_data($input, $userid, $response);

            // =================
            // FARM DETAILS
            // =================
            $farmTempIds = $this->process_farm_details($input, $userid, $originid, $response);

            // =========================
            // FARM CAPTURED DATA
            // =========================
            $this->process_farm_captured_data($input, $userid, $response);

            // =====================================================
            // FARM PRICE CALCULATION
            // =====================================================
            $this->process_farm_price_calculation($farmTempIds, $userid, $originid);

            // =====================================================
            // TRANSACTION STATUS
            // =====================================================
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            // =========================
            // COMMIT
            // =========================
            $this->db->trans_commit();

            // =========================
            // SEND PUSH NOTIFICATION
            // =========================
            $this->send_sync_notification($userid, 'sync_completed', 'data_sync_successfully', 'SUCCESS');

            // =====================================
            // SUCCESS LOG
            // =====================================
            $this->write_sync_log('SUCCESS', 'SYNC SUCCESS', 'Sync completed successfully', [
                'user_id' => $userid,
                'origin_id' => $originid,
                'request' => $input,
                'response' => $response
            ]);

            return $this->output($response);
        } catch (Throwable $e) {

            // =========================
            // ROLLBACK
            // =========================
            $this->db->trans_rollback();

            // =========================
            // ERROR DATA
            // =========================
            $errorData = [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' =>  $e->getFile(),
                'trace' => $e->getTraceAsString()
            ];

            // =========================
            // FAILURE PUSH NOTIFICATION
            // =========================
            $this->send_sync_notification($userid ?? 0, 'sync_failed', $e->getMessage(), "FAILED");

            // =====================================
            // CUSTOM ERROR LOG
            // =====================================
            $this->write_sync_log('ERROR', 'SYNC FAILED', $e->getMessage(), [
                'error' => $errorData,
                'request' => json_decode(file_get_contents('php://input'), true)
            ]);

            return $this->output([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =====================================================
    // CUSTOM SYNC LOGGER
    // =====================================================
    private function write_sync_log($type = 'INFO', $title = '', $message = '', $payload = null)
    {
        try {

            // =====================================
            // LOG DIRECTORY
            // =====================================
            $logDir = APPPATH . 'logs/sync/';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }

            // =====================================
            // FILE NAME
            // =====================================
            $fileName = strtolower($type) . '_' . date('Y_m_d_H_i_s') . '_' . uniqid() . '.txt';
            $filePath = $logDir . $fileName;

            // =====================================
            // CONTENT
            // =====================================
            $content = '';
            $content .= "====================================================\n";
            $content .= "TYPE : " . strtoupper($type) . "\n";
            $content .= "DATE : " . date('Y-m-d H:i:s') . "\n";
            $content .= "TITLE : " . $title . "\n";
            $content .= "MESSAGE : " . $message . "\n";

            // =====================================
            // PAYLOAD
            // =====================================
            if ($payload !== null) {
                $content .= "\nPAYLOAD : \n";
                $content .= json_encode($payload, JSON_PRETTY_PRINT);
                $content .= "\n";
            }

            $content .= "\n====================================================\n\n";

            // =====================================
            // WRITE FILE
            // =====================================
            file_put_contents($filePath, $content, FILE_APPEND);
        } catch (Throwable $e) {
            log_message('error', $e->getMessage());
        }
    }

    // =====================================================
    // TRUNCATE DECIMAL
    // =====================================================
    private function truncate_decimal(float $number, int $digits = 2): float
    {
        $factor = pow(10, $digits);
        return floor($number * $factor) / $factor;
    }

    // =====================================================
    // FCM NOTIFICATION
    // =====================================================
    private function send_sync_notification(int $userId, string $title, string $message, string $status = 'SUCCESS')
    {

        // =====================================
        // GET TOKEN
        // =====================================
        $tokenData = $this->Terralogin_model->get_latest_active_fcm_token($userId);

        if (!$tokenData) {
            return;
        }

        $token = $tokenData->fcm_token ?? '';

        if (empty($token)) {
            return;
        }

        // =====================================
        // SEND FCM
        // =====================================
        $this->load->helper('fcm');
        send_fcm_notification_v1([$token], $title, $message, 'SYNC', $status);
    }

    // =====================================================
    // SYNC RESPONSE
    // =====================================================
    private function initialize_sync_response()
    {
        return [
            'status' => true,
            'receptionMappings' => [],
            'receptionDataMappings' => [],
            'dispatchMappings' => [],
            'containerDataMappings' => [],
            'farmMappings' => [],
            'farmDataMappings' => [],
        ];
    }

    // =====================================================
    // PROCESS RECEPTION DETAILS
    // =====================================================
    private function process_reception_details(array $input, int $userid, int $originid, array &$response)
    {

        // =========================
        // FARM ENABLED TEMP IDS
        // =========================
        $farmReceptionTempIds = [];

        foreach ($input['receptionDetails'] as $receptiondetail) {

            // =====================
            // FLAGS
            // =====================
            $isDeleted = filter_var($receptiondetail['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =====================
            // COMMON DATA
            // =====================
            $receptionDetailData = [
                "measurementsystem_id" => $receptiondetail['measurementSystem'],
                "warehouse_id" => $receptiondetail['warehouse'],
                "supplier_id" => $receptiondetail['supplierId'],
                "supplier_code" => $this->Terramaster_model->get_supplier_code_by_id($receptiondetail['supplierId']),
                "supplier_product_id" => $receptiondetail['supplierProductId'],
                "supplier_product_typeid" => $receptiondetail['supplierProductTypeId'],
                "received_date" => $receptiondetail['receptionDate'],
                "salvoconducto" => $receptiondetail['ica'],
                "updatedby" => $userid,
                "isactive" => $isDeleted ? 0 : 1,
                "isclosed" => $receptiondetail['isClosed'],
                "closedby" => $receptiondetail['closedBy'],
                "closeddate" => !empty($receptiondetail['closedDate']) ? date('Y-m-d H:i:s', $receptiondetail['closedDate'] / 1000) : null,
                "origin_id" => $originid,
                "total_gross_volume" => $receptiondetail['totalGrossVolume'] ?? 0,
                "total_volume" => $receptiondetail['totalNetVolume'] ?? 0,
                "total_pieces" => $receptiondetail['totalPieces'] ?? 0,
                "total_volume_pie" => $receptiondetail['totalVolumePie'] ?? 0,
                "captured_from_app" => 1,
                "is_create_farm" => $receptiondetail['isFarmEnabled'],
                "contract_id" => $receptiondetail['purchaseContract'],
                "truck_plate_number" => $receptiondetail['truckNumber'],
                "driver_name" => $receptiondetail['truckDriverName'],
                "container_reception_mapping_id" => $receptiondetail['containerReceptionMappingId'] ?? null,
            ];

            // =====================
            // CHECK EXISTS
            // =====================
            $receptionExists = $this->Terrasync_model->reception_exists($receptiondetail['tempReceptionId']);

            // =====================
            // INSERT
            // =====================
            if (!$receptionExists) {
                $receptionDetailData['createdby'] = $userid;
                $receptionDetailData['captured_timestamp'] = $receptiondetail['createdAt'];
                $receptionDetailData['isduplicatecaptured'] = 0;
                $receptionDetailData['is_contract_added'] = 0;
                $receptionDetailData['is_special_uploaded'] = 1;
                $receptionDetailData['logistic_cost'] = 0;
                $receptionDetailData['logistic_pay_to'] = 0;
                $receptionDetailData['metric_ton'] = 0;
                $receptionDetailData['circ_allowance'] = 0;
                $receptionDetailData['length_allowance'] = 0;
                $receptionDetailData['rounding_factor'] = 0;
                $receptionDetailData['temp_reception_id'] = $receptiondetail['tempReceptionId'];

                $receptionId = $this->Terrasync_model->add_reception($receptionDetailData);
            } else {

                // =================
                // UPDATE
                // =================

                $receptionId = $receptionExists->reception_id ?? 0;

                if ($receptionId > 0) {
                    $this->Terrasync_model->update_reception($receptionId, $receptiondetail['tempReceptionId'], $receptionDetailData);
                }
            }

            // =====================
            // STORE FARM ENABLED IDS
            // =====================
            if (!empty($receptiondetail['isFarmEnabled'])) {
                $farmReceptionTempIds[] = [
                    'tempReceptionId' => $receptiondetail['tempReceptionId'],
                    'receptionId' => $receptionId
                ];
            }

            // =====================
            // RESPONSE MAPPING
            // =====================
            $response['receptionMappings'][] = [
                'tempReceptionId' => $receptiondetail['tempReceptionId'],
                'receptionId' => (int) $receptionId
            ];
        }

        return $farmReceptionTempIds;
    }

    // =====================================================
    // PROCESS RECEPTION DATA
    // =====================================================
    private function process_reception_data(array $input, int $userid, array &$response)
    {
        foreach ($input['receptionData'] as $receptiondata) {

            $isDeleted = filter_var($receptiondata['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =================
            // FETCH EXIST RECEPTION DATA
            // =================
            $reception = $this->Terrasync_model->reception_exists($receptiondata['tempReceptionId']);
            $receptionId = $reception->reception_id ?? 0;
            $salvoconducto = $reception->salvoconducto ?? '';
            $supplierProductTypeId = $reception->supplier_product_typeid ?? 0;

            $lengthExport = 0;
            $widthExport = 0;
            $thicknessExport = 0;
            $grossVolume = 0;
            $netVolume = 0;
            $face = 0;
            $grade = 0;

            if ($supplierProductTypeId == 1 || $supplierProductTypeId == 3) {
                $lengthExport = $this->truncate_decimal((float) $receptiondata['length'] * 0.3048, 2);
                $widthExport = $this->truncate_decimal((float) $receptiondata['width'] * 2.54, 0);
                $thicknessExport = $this->truncate_decimal((float) $receptiondata['thickness'] * 2.54, 0);
                $grossVolume = $this->truncate_decimal((float) $receptiondata['volumePie'] / 424, 3);
                $netVolume = $this->truncate_decimal((float) $lengthExport * (float) $widthExport * (float) $thicknessExport / 10000, 3) * (float) $receptiondata['pieces'];
                $face = (float) $receptiondata['width'] * (float) $receptiondata['thickness'];

                if (($widthExport < 15) || ($thicknessExport < 15)) {
                    $grade = 1;
                } elseif (($widthExport > 19.9) || ($thicknessExport > 19.9)) {
                    $grade = 3;
                } else {
                    $grade = 2;
                }
            } else {
                $grossVolume = $receptiondata['grossVolume'];
                $netVolume = $receptiondata['netVolume'];
            }

            $receptionData = [
                "reception_id" => $receptionId,
                "salvoconducto" => $salvoconducto,
                "scanned_code" => $receptiondata['pieces'],
                "length_bought" => $receptiondata['length'],
                "width_bought" => $receptiondata['width'],
                "thickness_bought" => $receptiondata['thickness'],
                "circumference_bought" => $receptiondata['circumference'],
                "volumepie_bought" => $receptiondata['volumePie'],
                "length_export" => $lengthExport,
                "width_export" => $widthExport,
                "thickness_export" => $thicknessExport,
                "cbm_bought" => $grossVolume,
                "cbm_export" => $netVolume,
                "grade" => $grade,
                "face" => $face,
                "isdispatch" => 0,
                "scanned_timestamp" => $receptiondata['createdAt'],
                "isduplicatescanned" => 0,
                "dispatch_date" => '',
                "container_number" => '',
                "is_special" => 1,
                "remaining_stock_count" => $receptiondata['pieces'],
                "temp_reception_id" => $receptiondata['tempReceptionId'],
                "temp_reception_data_id" => $receptiondata['tempReceptionDataId'],
                "reception_container_mapping_id" => $receptiondata['containerReceptionMappingId'],
                "isactive" => $isDeleted ? 0 : 1,
                "updatedby" => $userid
            ];

            $receptionDataExists = $this->Terrasync_model->reception_data_exists($receptiondata['tempReceptionDataId'], $receptiondata['tempReceptionId']);

            if (!$receptionDataExists) {
                $receptionData['createdby'] =  $userid;
                $receptionDataId = $this->Terrasync_model->add_reception_data($receptionData);
            } else {
                $receptionDataId = $receptionDataExists->reception_data_id ?? 0;
                $this->Terrasync_model->update_reception_data(
                    $receptionDataId,
                    $receptiondata['tempReceptionDataId'],
                    $receptiondata['containerReceptionMappingId'],
                    $receptionData
                );
            }

            $response['receptionDataMappings'][] = [
                'tempReceptionDataId' => $receptiondata['tempReceptionDataId'],
                'tempReceptionId' => $receptiondata['tempReceptionId'],
                'receptionDataId' => (int) $receptionDataId,
                'receptionId' => (int) $receptionId,
                'receptionContainerMappingId' => $receptiondata['containerReceptionMappingId']
            ];
        }
    }

    // =====================================================
    // PROCESS FARM DATA
    // =====================================================
    private function process_farm_data(array $farmReceptionTempIds, int $userid, int $originid)
    {
        foreach ($farmReceptionTempIds as $farmReception) {
            $tempReceptionId = $farmReception['tempReceptionId'];
            $receptionId = $farmReception['receptionId'];

            // =========================================
            // FETCH RECEPTION
            // =========================================
            $reception = $this->Terrasync_model->reception_exists($tempReceptionId);

            if (!$reception) {
                continue;
            }

            // =========================================
            // CHECK FARM EXISTS
            // =========================================
            $farmExists = $this->Terrasync_model->farm_exists($tempReceptionId);

            // =========================================
            // FETCH CONTRACT DETAILS
            // =========================================
            $contractDetails = $this->Terramaster_model->get_contract_details($reception->contract_id, $reception->supplier_id, $originid);

            $farmSupplierId = $reception->supplier_id ?? 0;
            $farmContractId = $contractDetails->contractId ?? 0;
            $farmProductTypeId = $contractDetails->productTypeId ?? 0;
            $farmPurchaseUnitId = $contractDetails->purchaseUnitId ?? 0;
            $farmCurrencyId = $contractDetails->currencyId ?? 0;
            $farmInventoryOrder = $reception->salvoconducto ?? '';
            $farmTotalVolume = $reception->total_volume ?? 0;

            $farmPurchaseDate = null;
            if (!empty($reception->received_date)) {
                $dateObj = DateTime::createFromFormat('d/m/Y', $reception->received_date);
                if ($dateObj) {
                    $farmPurchaseDate = $dateObj->format('Y-m-d');
                }
            }

            $supplierTaxesArr = array();
            $supplierTaxesAdjustArr = array();
            $woodValueWithSupplierTaxes = 0;

            // =========================================
            // FARM COMMON DATA
            // =========================================
            $farmData = [
                'supplier_id' => $farmSupplierId,
                'contract_id' => $farmContractId,
                'product_id' => $contractDetails->productId ?? 0,
                'product_type_id' => $farmProductTypeId,
                'purchase_unit_id' => $farmPurchaseUnitId,
                'inventory_order' => $farmInventoryOrder,
                'plate_number' => $reception->truck_plate_number ?? '',
                'driver_name' =>  $reception->driver_name ?? '',
                'purchase_date' => $farmPurchaseDate,
                'total_volume' => $farmTotalVolume,
                'total_value' => 0,
                'wood_value' => 0,
                'updated_by' => $userid,
                'is_active' => 1,
                'created_from' => 1,
                'origin_id' => $originid,
                'total_gross_volume' => $reception->total_gross_volume ?? 0,
                'total_pieces' => $reception->total_pieces ?? 0,
                'is_closed' => $reception->isclosed ?? 0,
                'closed_date' => $reception->closeddate ?? '',
                'closed_by' => $reception->closedby ?? 0,
                'temp_farm_id' => $tempReceptionId,
                'circ_allowance' => $contractDetails->purchaseAllowance ?? 0,
                'length_allowance' => $contractDetails->lengthAllowance ?? 0,
                'is_from_reception' => 1,
            ];

            // =========================================
            // INSERT / UPDATE FARM
            // =========================================
            if (!$farmExists) {

                $farmData['created_by'] = $userid;
                $farmId = $this->Terrasync_model->add_farm($farmData);
            } else {

                $farmId = $farmExists->farm_id ?? 0;
                $this->Terrasync_model->update_farm($farmId, $tempReceptionId, $farmData);
            }

            // =========================================
            // FETCH RECEPTION DATA
            // =========================================
            $receptionDatas = $this->Terrasync_model->get_reception_data_by_temp_reception_id($tempReceptionId);

            // =========================================
            // LOOP RECEPTION DATA
            // =========================================
            foreach ($receptionDatas as $receptionData) {

                // =====================================
                // CHECK FARM DETAIL EXISTS
                // =====================================
                $farmDetailExists = $this->Terrasync_model->farm_data_exists($farmId, $receptionData->temp_reception_data_id);

                // =====================================
                // FARM DETAIL DATA
                // =====================================
                $farmDetailData = [
                    'farm_id' => $farmId,
                    'scanned_code' => '',
                    'no_of_pieces' => $receptionData->scanned_code,
                    'circumference' => $receptionData->circumference_bought,
                    'length' => $receptionData->length_bought,
                    'width' => $receptionData->width_bought,
                    'thickness' => $receptionData->thickness_bought,
                    'gross_volume' => $receptionData->cbm_bought,
                    'volume' => $receptionData->cbm_export,
                    'volume_pie' => $receptionData->volumepie_bought,
                    'grade_id' => $receptionData->grade,
                    'face' => $receptionData->face,
                    'length_export' => $receptionData->length_export,
                    'width_export' => $receptionData->width_export,
                    'thickness_export' => $receptionData->thickness_export,
                    'volume_bought' => $receptionData->cbm_bought,
                    'captured_timestamp' => $receptionData->scanned_timestamp,
                    'temp_farm_data_id' => $receptionData->temp_reception_data_id,
                    'updated_by' => $userid,
                    'is_active' => 1
                ];

                // =====================================
                // INSERT / UPDATE FARM DETAIL
                // =====================================
                if (!$farmDetailExists) {
                    $farmDetailData['created_by'] = $userid;
                    $this->Terrasync_model->add_farm_data($farmDetailData);
                } else {
                    $this->Terrasync_model->update_farm_data($farmDetailExists->farm_data_id, $farmDetailExists->temp_farm_data_id, $farmDetailData);
                }
            }

            // =========================================
            // CALCULATE FARM VALUE
            // =========================================
            if ($farmId > 0) {

                //CONTRACT MAPPING
                $updateContractMappingData = [
                    'is_active' => 0,
                    'updated_by' => $userid
                ];

                $this->Terrasync_model->delete_contract_inventory_mapping($farmContractId, $farmSupplierId, $farmInventoryOrder, $updateContractMappingData);

                $dataContractMapping = array(
                    "contract_id" => $farmContractId,
                    "supplier_id" => $farmSupplierId,
                    "inventory_order" => $farmInventoryOrder,
                    "total_volume" => $farmTotalVolume,
                    "invoice_number" => "",
                    "created_by" => $userid,
                    "updated_by" => $userid,
                    "is_active" => 1,
                );

                $this->Terrasync_model->add_contract_inventory_mapping($dataContractMapping);

                $woodValue = 0;
                $finalArray = [];
                $totalVolume = 0;

                if ($farmProductTypeId == 1 || $farmProductTypeId == 3) {

                    //CALCULATE WOOD VALUE & TAXES
                    $farmDataSquare = $this->Terrasync_model->get_farm_data_by_farm_id_sqaure_blocks($farmId);

                    $fetchContractPrice = $this->Terramaster_model->fetch_contract_prices_for_farm($farmContractId);
                    $exchangeRate = $this->Terramaster_model->fetch_exchange_rate_by_date($farmPurchaseDate);

                    foreach ($farmDataSquare as $square) {
                        $width = $square->width;
                        $thickness = $square->thickness;
                        $length = $square->length;
                        $face = $square->face;
                        $volumePie = $square->volume_pie;
                        $netVolume = $square->volume;
                        $pieces = $shorts->no_of_pieces;
                        $price = 0;

                        foreach ($fetchContractPrice as $range) {
                            if ($face >= $range->minrange_grade1 && $face <= $range->maxrange_grade2) {
                                $price = $range->pricerange_grade3;
                                break;
                            }
                        }

                        if ($farmPurchaseUnitId == 1) {
                            $finalArray[] = [
                                'width' => $width,
                                'thickness' => $thickness,
                                'length' => $length,
                                'face' => $face,
                                'volume_pie' => $volumePie,
                                'price' => $price,
                                'volume' => $netVolume,
                                'value' => round($price * $volumePie, 3)
                            ];
                        } else {
                            $finalArray[] = [
                                'width' => $width,
                                'thickness' => $thickness,
                                'length' => $length,
                                'face' => $face,
                                'volume_pie' => $volumePie,
                                'price' => $price,
                                'volume' => $netVolume,
                                'value' => round($price * $netVolume, 3)
                            ];
                        }
                    }

                    foreach ($finalArray as $item) {
                        $woodValue = $woodValue + $item['value'];
                    }
                } else {

                    //CALCULATE WOOD VALUE & TAXES
                    $farmDataShorts = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 1);
                    $farmDataSemi = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 2);
                    $farmDataLongs = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 3);

                    $fetchContractPrice = $this->Terramaster_model->fetch_contract_prices_for_farm($farmContractId);
                    $exchangeRate = $this->Terramaster_model->fetch_exchange_rate_by_date($farmPurchaseDate);

                    if ($farmPurchaseUnitId == 15) {
                        $price = $fetchContractPrice[0]->pricerange_grade3;
                        $woodValue = $price;
                    } else {

                        foreach ($farmDataShorts as $shorts) {
                            $circumference = $shorts->circumference;
                            $length = $shorts->length;
                            $netVolume = $shorts->volume;
                            $totalVolume = $totalVolume + $netVolume;
                            $pieces = $shorts->no_of_pieces;
                            $price = 0;

                            foreach ($fetchContractPrice as $range) {
                                if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                    $price = $range->pricerange_grade3;
                                    break;
                                }
                            }

                            if ($farmPurchaseUnitId == 3) {
                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $pieces, 3)
                                ];
                            } else {

                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $netVolume, 3)
                                ];
                            }
                        }

                        foreach ($farmDataSemi as $semi) {
                            $circumference = $semi->circumference;
                            $length = $semi->length;
                            $netVolume = $semi->volume;
                            $totalVolume = $totalVolume + $netVolume;
                            $pieces = $semi->no_of_pieces;
                            $price = 0;

                            foreach ($fetchContractPrice as $range) {
                                if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                    $price = $range->pricerange_grade_semi;
                                    break;
                                }
                            }

                            if ($farmPurchaseUnitId == 3) {
                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $pieces, 3)
                                ];
                            } else {

                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $netVolume, 3)
                                ];
                            }
                        }

                        foreach ($farmDataLongs as $longs) {
                            $circumference = $longs->circumference;
                            $length = $longs->length;
                            $netVolume = $longs->volume;
                            $totalVolume = $totalVolume + $netVolume;
                            $pieces = $longs->no_of_pieces;
                            $price = 0;

                            foreach ($fetchContractPrice as $range) {
                                if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                    $price = $range->pricerange_grade_longs;
                                    break;
                                }
                            }

                            if ($farmPurchaseUnitId == 3) {
                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $pieces, 3)
                                ];
                            } else {
                                $finalArray[] = [
                                    'circumference' => $circumference,
                                    'length' => $length,
                                    'price' => $price,
                                    'volume' => $netVolume,
                                    'value' => round($price * $netVolume, 3)
                                ];
                            }
                        }

                        foreach ($finalArray as $item) {
                            $woodValue = $woodValue + $item['value'];
                        }
                    }
                }

                if ($woodValue > 0) {
                    if ($farmCurrencyId == 1) {
                        if ($exchangeRate[0]->value > 0 && $woodValue > 0) {
                            $woodValue = $woodValue * $exchangeRate[0]->value;
                        }
                    }

                    // =====================================
                    // WOOD VALUE WITH TAXES
                    // =====================================
                    $getSupplierTaxes = $this->Terramaster_model->get_supplier_taxes($farmSupplierId);
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

                    // =====================================
                    // UPDATE FINAL FARM VALUES
                    // =====================================
                    $this->Terrasync_model->update_farm(
                        $farmId,
                        $tempReceptionId,
                        [
                            'wood_value' => $woodValue,
                            'total_value' => $woodValue,
                            'wood_value_withtaxes' => $woodValueWithSupplierTaxes,
                            'supplier_taxes' => implode(', ', $supplierTaxesArr),
                            'supplier_taxes_array' => json_encode($supplierTaxesAdjustArr),
                            'updated_by' => $userid
                        ]
                    );

                    // =====================================
                    // INVENTORY SUPPLIER PRICE
                    // =====================================
                    $updateInventorySupplierPriceData = array(
                        "updated_by" => $userid,
                        "is_active" => 0,
                    );

                    $this->Terrasync_model->delete_inventory_supplier_price($farmInventoryOrder, $farmContractId, $farmSupplierId, $updateInventorySupplierPriceData);

                    $this->Terrasync_model->add_inventory_supplier_price($farmContractId, $farmSupplierId, $farmInventoryOrder, $userid);

                    // =====================================
                    // INVENTORY LEDGER
                    // =====================================
                    $updateInventoryLedgerData = array(
                        "amount" => 0,
                        "updated_by" => $userid,
                        "is_active" => 0,
                    );

                    $this->Terrasync_model->delete_inventory_ledger($farmInventoryOrder, $farmContractId, $updateInventoryLedgerData);

                    $dataInventoryLedger = array(
                        "contract_id" => $farmContractId,
                        "supplier_id" => $farmSupplierId,
                        "inventory_order" => $farmInventoryOrder,
                        "ledger_type" => 2,
                        "pm_ledger_type" => 0,
                        "expense_type" => 1,
                        "amount" => $woodValueWithSupplierTaxes,
                        "expense_date" => $farmPurchaseDate,
                        "created_by" => $userid,
                        "updated_by" => $userid,
                        "is_active" => 1,
                        "is_advance_app" => 0,
                    );

                    if ($woodValueWithSupplierTaxes != 0) {
                        $this->Terrasync_model->add_inventory_ledger($dataInventoryLedger);
                    }
                }
            }
        }
    }

    // =====================================================
    // PROCESS DISPATCH DETAILS
    // =====================================================
    private function process_dispatch_details(array $input, int $userid, int $originid, array &$response)
    {
        foreach ($input['dispatchDetails'] as $dispatchdetail) {

            // =====================
            // FLAGS
            // =====================
            $isDeleted = filter_var($dispatchdetail['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $isClosed = filter_var($dispatchdetail['isClosed'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =====================
            // COMMON DATA
            // =====================
            $dispatchDetailData = [
                "container_number" => $dispatchdetail['containerNumber'],
                "warehouse_id" => $dispatchdetail['warehouseId'],
                "shipping_line" => $dispatchdetail['shippingLineId'],
                "product_id" => $dispatchdetail['productId'],
                "product_type_id" => $dispatchdetail['productTypeId'],
                "dispatch_date" => $dispatchdetail['dispatchDate'],
                "updatedby" => $userid,
                "isactive" => $isDeleted ? 0 : 1,
                "isclosed" => $isClosed ? 1 : 0,
                "closedby" => $dispatchdetail['closedBy'],
                "closeddate" => !empty($dispatchdetail['closedDate']) ? date('Y-m-d H:i:s', $dispatchdetail['closedDate'] / 1000) : null,
                "isexport" => 0,
                "origin_id" => $originid,
                "total_gross_volume" => $dispatchdetail['totalGrossVolume'] ?? 0,
                "total_volume" => $dispatchdetail['totalNetVolume'] ?? 0,
                "total_pieces" => $dispatchdetail['totalPieces'] ?? 0,
                "app_category" => $dispatchdetail['categoryId'] ?? '',
                "captured_from_app" => 1,
                "metric_ton" => 0,
                "short_ton" => 0,
                "net_lbs" => 0,
                "diameter_text" => '',
                "length_text" => '',
                "unit_price" => 0,
                "total_value" => 0,
                "measurement_system_id" => 0,
                "circ_allowance" => 0,
                "length_allowance" => 0,
                "rounding_factor" => 0,
                "is_saw_mill_loading" => 0,
            ];

            // =====================
            // CHECK EXISTS
            // =====================
            $dispatchExists = $this->Terrasync_model->dispatch_exists($dispatchdetail['tempDispatchId']);

            // =====================
            // INSERT
            // =====================
            if (!$dispatchExists) {
                $dispatchDetailData['createdby'] = $userid;
                $dispatchDetailData['temp_dispatch_id'] = $dispatchdetail['tempDispatchId'];
                $dispatchDetailData['dispatched_timestamp'] = $dispatchdetail['createdAt'];

                $dispatchId = $this->Terrasync_model->add_dispatch($dispatchDetailData);
            } else {

                // =================
                // UPDATE
                // =================

                $dispatchId = $dispatchExists->dispatch_id ?? 0;

                if ($dispatchId > 0) {
                    $this->Terrasync_model->update_dispatch($dispatchId, $dispatchdetail['tempDispatchId'], $dispatchDetailData);
                }
            }

            // =====================
            // RESPONSE MAPPING
            // =====================
            $response['dispatchMappings'][] = [
                'tempDispatchId' => $dispatchdetail['tempDispatchId'],
                'dispatchId' => (int) $dispatchId
            ];
        }
    }

    // =====================================================
    // PROCESS CONTAINER DATA
    // =====================================================
    private function process_container_data(array $input, int $userid, array &$response)
    {
        foreach ($input['containerData'] as $containerdata) {

            $isDeleted = filter_var(
                $containerdata['isDeleted'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            // =========================
            // FETCH DISPATCH
            // =========================
            $dispatch = $this->Terrasync_model->dispatch_exists($containerdata['tempDispatchId']);

            $dispatchId = $dispatch->dispatch_id ?? 0;

            if ($dispatchId <= 0) {
                throw new Exception('Dispatch header not found');
            }

            // =========================
            // FETCH RECEPTION DATA
            // =========================
            $receptionDataExists = $this->Terrasync_model->reception_data_exists($containerdata['tempReceptionDataId'], $containerdata['tempReceptionId']);
            $receptionDataId = $receptionDataExists->reception_data_id ?? 0;

            if ($receptionDataId <= 0) {
                throw new Exception('Reception data not found');
            }

            // =========================
            // CONTAINER DATA
            // =========================
            $containerData = [
                "dispatch_id" => $dispatchId,
                "reception_data_id" => $receptionDataId,
                "reception_id" => $receptionDataExists->reception_id ?? 0,
                "cbm_bought" => $containerdata['grossVolume'],
                "cbm_export" => $containerdata['netVolume'],
                "volume_pie" => $containerdata['volumePie'] ?? 0,
                "scanned_timestamp" => $containerdata['createdAt'],
                "isduplicatescanned" => 0,
                "is_special" => 1,
                "dispatch_pieces" => $containerdata['pieces'],
                "temp_dispatch_data_id" => $containerdata['tempDispatchDataId'],
                "temp_dispatch_id" => $containerdata['tempDispatchId'],
                "temp_reception_id" => $containerdata['tempReceptionId'],
                "temp_reception_data_id" => $containerdata['tempReceptionDataId'],
                "container_reception_mapping_id" => $containerdata['containerReceptionMappingId'],
                "isactive" => $isDeleted ? 0 : 1,
                "updatedby" => $userid
            ];

            // =========================
            // CHECK EXISTING DISPATCH DATA
            // =========================
            $containerDataExists = $this->Terrasync_model->dispatch_data_exists(
                $containerdata['tempDispatchId'],
                $containerdata['tempReceptionDataId'],
                $containerdata['tempReceptionId']
            );

            // =====================================================
            // INSERT
            // =====================================================
            if (!$containerDataExists) {
                $containerData['createdby'] = $userid;
                $dispatchDataId = $this->Terrasync_model->add_dispatch_data($containerData);
            }

            // =====================================================
            // UPDATE
            // =====================================================
            else {

                $dispatchDataId = $containerDataExists->dispatch_data_id ?? 0;

                $this->Terrasync_model->update_dispatch_data(
                    $dispatchDataId,
                    $containerdata['tempDispatchId'],
                    $containerdata['tempReceptionDataId'],
                    $containerdata['tempReceptionId'],
                    $containerData
                );
            }

            // =====================================================
            // RECALCULATE REMAINING STOCK
            // =====================================================
            $this->Terrasync_model->recalculate_remaining_stock($containerdata['tempReceptionId'], $containerdata['tempReceptionDataId']);

            // =====================================================
            // FETCH UPDATED STOCK
            // =====================================================
            $updatedReceptionData = $this->Terrasync_model->reception_data_exists($containerdata['tempReceptionDataId'], $containerdata['tempReceptionId']);
            $remainingStock = (float) ($updatedReceptionData->remaining_stock_count ?? 0);

            // =====================================================
            // PARTIAL/FULL DISPATCH STATUS
            // =====================================================
            $isDispatch = ($remainingStock <= 0) ? 1 : 0;

            // =====================================================
            // UPDATE RECEPTION DATA STATUS
            // =====================================================
            $updateReceptionData = [
                'isdispatch' => $isDispatch,
                'dispatch_date' => $dispatch->dispatch_date ?? '',
                'container_number' => $dispatch->container_number ?? '',
                'updatedby' => $userid
            ];

            $this->Terrasync_model->update_reception_data_stock(
                $containerdata['tempReceptionDataId'],
                $containerdata['tempReceptionId'],
                $receptionDataId,
                $updateReceptionData
            );

            // =====================================================
            // RESPONSE MAPPING
            // =====================================================
            $response['containerDataMappings'][] = [
                'tempDispatchDataId' => $containerdata['tempDispatchDataId'],
                'tempReceptionDataId' => $containerdata['tempReceptionDataId'],
                'tempDispatchId' => $containerdata['tempDispatchId'],
                'dispatchDataId' => (int) $dispatchDataId,
                'containerReceptionMappingId' => $containerdata['containerReceptionMappingId'],
                'receptionDataId' => (int) $receptionDataId,
                'receptionId' => (int) $receptionDataExists->reception_id,
                'dispatchId' => (int) $dispatchId,
                'tempReceptionId' => $containerdata['tempReceptionId']
            ];
        }
    }

    // =====================================================
    // PROCESS FARM DETAILS
    // =====================================================
    private function process_farm_details(array $input, int $userid, int $originid, array &$response)
    {

        // =========================
        // FARM ENABLED TEMP IDS
        // =========================
        $farmTempIds = [];

        foreach ($input['farmDetails'] as $farmdetail) {

            // =====================
            // FLAGS
            // =====================
            $isDeleted = filter_var($farmdetail['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =========================================
            // FETCH CONTRACT DETAILS
            // =========================================
            $contractDetails = $this->Terramaster_model->get_contract_details($farmdetail['purchaseContract'], $farmdetail['supplierId'], $originid);

            // =====================
            // COMMON DATA
            // =====================
            $farmDetailData = [
                "supplier_id" => $farmdetail['supplierId'],
                "contract_id" => $farmdetail['purchaseContract'],
                "product_id" => $farmdetail['productId'],
                "product_type_id" => $farmdetail['productTypeId'],
                "purchase_unit_id" => $contractDetails->purchaseUnitId ?? 0,
                "purchase_date" => $farmdetail['purchaseDate'],
                "inventory_order" => $farmdetail['ica'],
                "plate_number" => $farmdetail['truckNumber'],
                "driver_name" => $farmdetail['truckDriverName'],
                "total_volume" => $farmdetail['totalNetVolume'] ?? 0,
                'total_value' => 0,
                'wood_value' => 0,
                "updated_by" => $userid,
                "is_active" => $isDeleted ? 0 : 1,
                "origin_id" => $originid,
                'circ_allowance' => $contractDetails->purchaseAllowance ?? 0,
                'length_allowance' => $contractDetails->lengthAllowance ?? 0,
                "total_gross_volume" => $farmdetail['totalGrossVolume'] ?? 0,
                "total_pieces" => $farmdetail['totalPieces'] ?? 0,
                "total_volume_pie" => $farmdetail['totalVolumePie'] ?? 0,
                "is_closed" => $farmdetail['isClosed'],
                "closed_by" => $farmdetail['closedBy'],
                "closed_date" => !empty($farmdetail['closedDate']) ? date('Y-m-d H:i:s', $farmdetail['closedDate'] / 1000) : null,
                "temp_farm_id" => $farmdetail['tempFarmId'] ?? null,
                "is_from_reception" => 0,
            ];

            // =====================
            // CHECK EXISTS
            // =====================
            $farmExists = $this->Terrasync_model->farm_exists($farmdetail['tempFarmId']);

            // =====================
            // INSERT
            // =====================
            if (!$farmExists) {
                $farmDetailData['created_by'] = $userid;
                $farmDetailData['captured_timestamp'] = $farmdetail['createdAt'];

                $farmId = $this->Terrasync_model->add_farm($farmDetailData);
            } else {

                // =================
                // UPDATE
                // =================

                $farmId = $farmExists->farm_id ?? 0;

                if ($farmId > 0) {
                    $this->Terrasync_model->update_farm($farmId, $farmdetail['tempFarmId'], $farmDetailData);
                }
            }

            // =====================
            // STORE FARM ENABLED IDS
            // =====================
            $farmTempIds[] = [
                'tempFarmId' => $farmdetail['tempFarmId'],
                'farmId' => $farmId
            ];

            // =====================
            // RESPONSE MAPPING
            // =====================
            $response['farmMappings'][] = [
                'tempFarmId' => $farmdetail['tempFarmId'],
                'farmId' => (int) $farmId
            ];
        }

        return $farmTempIds;
    }

    // =====================================================
    // PROCESS FARM CAPTURED DATA
    // =====================================================
    private function process_farm_captured_data(array $input, int $userid, array &$response)
    {
        foreach ($input['farmData'] as $farmdata) {

            $isDeleted = filter_var($farmdata['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =================
            // FETCH EXIST FARM DATA
            // =================
            $farm = $this->Terrasync_model->farm_exists($farmdata['tempFarmId']);
            $farmId = $farm->farm_id ?? 0;
            $productTypeId = $farm->product_typeid ?? 0;

            $lengthExport = 0;
            $widthExport = 0;
            $thicknessExport = 0;
            $grossVolume = 0;
            $netVolume = 0;
            $face = 0;
            $grade = 0;

            if ($productTypeId == 1 || $productTypeId == 3) {
                $lengthExport = $this->truncate_decimal((float) $farmdata['length'] * 0.3048, 2);
                $widthExport = $this->truncate_decimal((float) $farmdata['width'] * 2.54, 0);
                $thicknessExport = $this->truncate_decimal((float) $farmdata['thickness'] * 2.54, 0);
                $grossVolume = $this->truncate_decimal((float) $farmdata['grossVolume'] / 424, 3);
                $netVolume = $this->truncate_decimal((float) $lengthExport * (float) $widthExport * (float) $thicknessExport / 10000, 3) * (float) $farmdata['pieces'];
                $face = (float) $farmdata['width'] * (float) $farmdata['thickness'];

                if (($widthExport < 15) || ($thicknessExport < 15)) {
                    $grade = 1;
                } elseif (($widthExport > 19.9) || ($thicknessExport > 19.9)) {
                    $grade = 3;
                } else {
                    $grade = 2;
                }
            } else {
                $grossVolume = $farmdata['grossVolume'];
                $netVolume = $farmdata['netVolume'];
            }

            $farmData = [
                "farm_id" => $farmId,
                "scanned_code" => "",
                "no_of_pieces" => $farmdata['pieces'],
                "circumference" => $farmdata['circumference'],
                "length" => $farmdata['length'],
                "width" => $farmdata['width'],
                "thickness" => $farmdata['thickness'],
                "gross_volume" => $grossVolume,
                "volume" => $netVolume,
                "volume_pie" => $farmdata['volumePie'] ?? 0,
                "grade_id" => $grade,
                "face" => $face,
                "length_export" => $lengthExport,
                "width_export" => $widthExport,
                "thickness_export" => $thicknessExport,
                "volume_bought" => $grossVolume,
                "updated_by" => $userid,
                "is_active" => $isDeleted ? 0 : 1,
                "captured_timestamp" => $farmdata['createdAt'],
                "temp_farm_data_id" => $farmdata['tempFarmDataId'] ?? null,
            ];

            $farmDataExists = $this->Terrasync_model->farm_data_exists($farmId, $farmdata['tempFarmDataId']);

            if (!$farmDataExists) {
                $farmData['created_by'] =  $userid;
                $farmDataId = $this->Terrasync_model->add_farm_data($farmData);
            } else {
                $farmDataId = $farmDataExists->farm_data_id ?? 0;
                $this->Terrasync_model->update_farm_data($farmDataId, $farmdata['tempFarmId'], $farmData);
            }

            $response['farmDataMappings'][] = [
                'tempFarmDataId' => $farmdata['tempFarmDataId'],
                'tempFarmId' => $farmdata['tempFarmId'],
                'farmDataId' => (int) $farmDataId,
                'farmId' => (int) $farmId,
            ];
        }
    }

    // =====================================================
    // PROCESS FARM PRICE CALCULATION
    // =====================================================
    private function process_farm_price_calculation(array $farmTempIds, int $userid, int $originid)
    {
        foreach ($farmTempIds as $farmTemp) {

            $tempFarmId = $farmTemp['tempFarmId'];
            $farmId = $farmTemp['farmId'];

            // =========================================
            // FETCH FARM
            // =========================================
            $farm = $this->Terrasync_model->farm_exists($tempFarmId);

            if (!$farm || $farmId <= 0) {
                continue;
            }

            // =========================================
            // FARM DATA
            // =========================================
            $farmSupplierId = $farm->supplier_id ?? 0;
            $farmContractId = $farm->contract_id ?? 0;
            $farmProductTypeId = $farm->product_type_id ?? 0;
            $farmPurchaseUnitId = $farm->purchase_unit_id ?? 0;
            $farmInventoryOrder = $farm->inventory_order ?? '';
            $farmPurchaseDate = $farm->purchase_date ?? '';

            // =========================================
            // FETCH CONTRACT DETAILS
            // =========================================
            $contractDetails = $this->Terramaster_model->get_contract_details($farm->contract_id ?? 0, $farm->supplier_id ?? 0, $originid);
            $farmCurrencyId = $contractDetails->currencyId ?? 0;

            // =========================================
            // CONTRACT MAPPING
            // =========================================
            $updateContractMappingData = [
                'is_active' => 0,
                'updated_by' => $userid
            ];

            $this->Terrasync_model->delete_contract_inventory_mapping($farmContractId, $farmSupplierId, $farmInventoryOrder, $updateContractMappingData);

            $dataContractMapping = [
                "contract_id" => $farmContractId,
                "supplier_id" => $farmSupplierId,
                "inventory_order" => $farmInventoryOrder,
                "total_volume" => $farm->total_volume ?? 0,
                "invoice_number" => "",
                "created_by" => $userid,
                "updated_by" => $userid,
                "is_active" => 1,
            ];

            $this->Terrasync_model->add_contract_inventory_mapping($dataContractMapping);

            // =========================================
            // CALCULATIONS
            // =========================================
            $woodValue = 0;
            $finalArray = [];
            $totalVolume = 0;

            $fetchContractPrice = $this->Terramaster_model->fetch_contract_prices_for_farm($farmContractId);
            $exchangeRate = $this->Terramaster_model->fetch_exchange_rate_by_date($farmPurchaseDate);

            // =====================================================
            // WOOD VALUE CALCULATION
            // =====================================================
            if ($farmProductTypeId == 1 || $farmProductTypeId == 3) {
                $farmDataSquare = $this->Terrasync_model->get_farm_data_by_farm_id_sqaure_blocks($farmId);
                foreach ($farmDataSquare as $square) {
                    $face = $square->face;
                    $volumePie = $square->volume_pie;
                    $netVolume = $square->volume;

                    $price = 0;

                    foreach ($fetchContractPrice as $range) {
                        if ($face >= $range->minrange_grade1 && $face <= $range->maxrange_grade2) {
                            $price = $range->pricerange_grade3;
                            break;
                        }
                    }

                    $value = ($farmPurchaseUnitId == 1) ? round($price * $volumePie, 3) : round($price * $netVolume, 3);

                    $woodValue += $value;
                }
            } else {
                if ($farmPurchaseUnitId == 15) {
                    $woodValue = $fetchContractPrice[0]->pricerange_grade3 ?? 0;
                } else {

                    $farmDataShorts = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 1);
                    $farmDataSemi   = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 2);
                    $farmDataLongs  = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 3);

                    $allFarmData = [
                        ['data' => $farmDataShorts, 'field' => 'pricerange_grade3'],
                        ['data' => $farmDataSemi, 'field' => 'pricerange_grade_semi'],
                        ['data' => $farmDataLongs, 'field' => 'pricerange_grade_longs'],
                    ];

                    foreach ($allFarmData as $group) {
                        foreach ($group['data'] as $item) {
                            $circumference = $item->circumference;
                            $netVolume = $item->volume;
                            $pieces = $item->no_of_pieces;
                            $price = 0;
                            foreach ($fetchContractPrice as $range) {
                                if ($circumference >= $range->minrange_grade1 && $circumference <= $range->maxrange_grade2) {
                                    $price = $range->{$group['field']};
                                    break;
                                }
                            }
                            $value = ($farmPurchaseUnitId == 3) ? round($price * $pieces, 3) : round($price * $netVolume, 3);
                            $woodValue += $value;
                        }
                    }
                }
            }

            // =========================================
            // EXCHANGE RATE
            // =========================================
            if ($woodValue > 0 && $farmCurrencyId == 1) {
                if (!empty($exchangeRate) && ($exchangeRate[0]->value ?? 0) > 0) {
                    $woodValue *= $exchangeRate[0]->value;
                }
            }

            // =========================================
            // SUPPLIER TAXES
            // =========================================
            $supplierTaxesArr = [];
            $supplierTaxesAdjustArr = [];
            $supplierTaxesValue = 0;

            $getSupplierTaxes = $this->Terramaster_model->get_supplier_taxes($farmSupplierId);

            foreach ($getSupplierTaxes as $suppliertax) {

                $taxId = $suppliertax->tax_id;
                $taxValue = $suppliertax->tax_value;
                $taxFormat = $suppliertax->number_format;
                $taxType = $suppliertax->arithmetic_type;

                if ($taxType == 2) {
                    $taxValue *= -1;
                }

                $calcValue = ($taxFormat == 2) ? $woodValue * ($taxValue / 100) : $woodValue * $taxValue;

                $supplierTaxesAdjustArr[] = [
                    "taxId" => $taxId,
                    "taxValue" => $calcValue,
                    "taxVal" => abs($taxValue)
                ];

                $supplierTaxesArr[] = $taxId;
                $supplierTaxesValue += $calcValue;
            }

            $woodValueWithSupplierTaxes = $woodValue + $supplierTaxesValue;

            // =========================================
            // UPDATE FARM
            // =========================================
            $this->Terrasync_model->update_farm(
                $farmId,
                $tempFarmId,
                [
                    'wood_value' => $woodValue,
                    'total_value' => $woodValue,
                    'wood_value_withtaxes' => $woodValueWithSupplierTaxes,
                    'supplier_taxes' => implode(', ', $supplierTaxesArr),
                    'supplier_taxes_array' => json_encode($supplierTaxesAdjustArr),
                    'updated_by' => $userid
                ]
            );

            // =========================================
            // INVENTORY SUPPLIER PRICE
            // =========================================
            $updateInventorySupplierPriceData = [
                "updated_by" => $userid,
                "is_active" => 0,
            ];

            $this->Terrasync_model->delete_inventory_supplier_price(
                $farmInventoryOrder,
                $farmContractId,
                $farmSupplierId,
                $updateInventorySupplierPriceData
            );

            $this->Terrasync_model->add_inventory_supplier_price(
                $farmContractId,
                $farmSupplierId,
                $farmInventoryOrder,
                $userid
            );

            // =========================================
            // INVENTORY LEDGER
            // =========================================
            $updateInventoryLedgerData = [
                "amount" => 0,
                "updated_by" => $userid,
                "is_active" => 0,
            ];

            $this->Terrasync_model->delete_inventory_ledger(
                $farmInventoryOrder,
                $farmContractId,
                $updateInventoryLedgerData
            );

            if ($woodValueWithSupplierTaxes != 0) {

                $dataInventoryLedger = [
                    "contract_id" => $farmContractId,
                    "supplier_id" => $farmSupplierId,
                    "inventory_order" => $farmInventoryOrder,
                    "ledger_type" => 2,
                    "pm_ledger_type" => 0,
                    "expense_type" => 1,
                    "amount" => $woodValueWithSupplierTaxes,
                    "expense_date" => $farmPurchaseDate,
                    "created_by" => $userid,
                    "updated_by" => $userid,
                    "is_active" => 1,
                    "is_advance_app" => 0,
                ];

                $this->Terrasync_model->add_inventory_ledger($dataInventoryLedger);
            }
        }
    }
}
