<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Sync extends MY_Controller
{
    private const PRODUCT_TYPE_LOG = 1;
    private const PRODUCT_TYPE_TIMBER = 3;

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
            $headers = $this->input->request_headers();
            $requestBearerToken = '';
            foreach ($headers as $header => $value) {
                if (strtolower($header) == 'authorization') {
                    $parts = explode(' ', $value);

                    if (count($parts) == 2) {
                        $requestBearerToken = $parts[1];
                    }
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
            $ext = strtolower($ext);
            if (empty($ext)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Invalid file extension'
                ], 400);
            }

            // =========================
            // CUSTOM FILE NAME
            // =========================

            $customFileName = 'container_' . $tempContainerImageId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

            // =========================
            // FILE MIME TYPE
            // =========================
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);

            $allowedMime = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($mime, $allowedMime)) {
                return $this->output([
                    'status' => false,
                    'message' => 'Invalid image type'
                ], 400);
            }

            finfo_close($finfo);

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
        $startTime = microtime(true);
        $requestId = bin2hex(random_bytes(16));

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
            $headers = $this->input->request_headers();
            $requestBearerToken = '';
            foreach ($headers as $header => $value) {
                if (strtolower($header) == 'authorization') {
                    $parts = explode(' ', $value);

                    if (count($parts) == 2) {
                        $requestBearerToken = $parts[1];
                    }
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
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->output([
                    'status' => false,
                    'message' => json_last_error_msg()
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
            $this->db->query("SET innodb_lock_wait_timeout = 50");
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
            $executionTime = microtime(true) - $startTime;

            // =========================
            // SEND PUSH NOTIFICATION
            // =========================
            $this->send_sync_notification($userid, 'sync_completed', 'data_sync_successfully', 'SUCCESS');

            // =====================================
            // SUCCESS LOG
            // =====================================
            $this->write_sync_log(
                'SUCCESS',
                'SYNC SUCCESS',
                'Sync completed successfully',
                [
                    'request_id' => $requestId,
                    'user_id' => $userid,
                    'origin_id' => $originid,
                    'execution_time' => $executionTime,
                    'payload_size' => strlen($rawInput),
                    'request_summary' => [
                        'receptionDetails' => count($input['receptionDetails']),
                        'receptionData' => count($input['receptionData']),
                        'dispatchDetails' => count($input['dispatchDetails']),
                        'containerData' => count($input['containerData'])
                    ],
                    'response' => [
                        'receptionMappings' => count($response['receptionMappings']),
                        'receptionDataMappings' => count($response['receptionDataMappings']),
                        'dispatchMappings' => count($response['dispatchMappings']),
                        'containerDataMappings' => count($response['containerDataMappings'])
                    ]
                ]
            );

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
                'request_id' => $requestId ?? '',
                'request_summary' => [
                    'receptionDetails' => count($input['receptionDetails'] ?? []),
                    'receptionData' => count($input['receptionData'] ?? []),
                    'dispatchDetails' => count($input['dispatchDetails'] ?? []),
                    'containerData' => count($input['containerData'] ?? [])
                ]
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
            'containerDataMappings' => []
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

        // =========================
        // TEMP RECEPTION IDS
        // =========================
        $tempReceptionIds = array_column($input['receptionDetails'], 'tempReceptionId');
        $existingReceptions = $this->Terrasync_model->get_receptions_by_temp_ids($tempReceptionIds);
        $receptionMap = [];
        foreach ($existingReceptions as $item) {
            $receptionMap[$item->temp_reception_id] = $item;
        }

        $supplierIds = array_unique(
            array_column(
                $input['receptionDetails'],
                'supplierId'
            )
        );

        $supplierCodes = $this->Terramaster_model->get_supplier_codes($supplierIds);
        $supplierCodeMap = [];

        foreach ($supplierCodes as $item) {
            $supplierCodeMap[$item->supplier_id] = $item->supplier_code;
        }

        foreach ($input['receptionDetails'] as $receptiondetail) {

            $tempReceptionId = $receptiondetail['tempReceptionId'];
            $receptionExists = $receptionMap[$tempReceptionId] ?? null;

            // =====================
            // FLAGS
            // =====================
            $isDeleted = filter_var($receptiondetail['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =====================
            // TOTAL GROSS VOLUME
            // =====================
            $productType = $receptiondetail['supplierProductTypeId'];
            if ($productType == self::PRODUCT_TYPE_LOG || $productType == self::PRODUCT_TYPE_TIMBER) {
                $totalGrossVolume = $receptiondetail['totalVolumePie'] ?? 0;
            } else {
                $totalGrossVolume = $receptiondetail['totalGrossVolume'] ?? 0;
            }

            // =====================
            // COMMON DATA
            // =====================
            $receptionDetailData = [
                "measurementsystem_id" => $receptiondetail['measurementSystem'],
                "warehouse_id" => $receptiondetail['warehouse'],
                "supplier_id" => $receptiondetail['supplierId'],
                "supplier_code" => $supplierCodeMap[$receptiondetail['supplierId']] ?? null,
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
                "total_gross_volume" => $totalGrossVolume,
                "total_volume" => $receptiondetail['totalNetVolume'] ?? 0,
                "total_pieces" => $receptiondetail['totalPieces'] ?? 0,
                "captured_from_app" => 1,
                "is_create_farm" => $receptiondetail['isFarmEnabled'],
                "contract_id" => $receptiondetail['purchaseContract'],
                "truck_plate_number" => $receptiondetail['truckNumber'],
                "driver_name" => $receptiondetail['truckDriverName'],
                "container_reception_mapping_id" => $receptiondetail['containerReceptionMappingId'] ?? null,
            ];

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

                $receptionMap[$tempReceptionId] = (object) [
                    'reception_id' => $receptionId,
                    'temp_reception_id' => $tempReceptionId
                ];
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
        $tempReceptionIds = array_unique(array_column($input['receptionData'], 'tempReceptionId'));
        $existingReceptions = $this->Terrasync_model->get_receptions_by_temp_ids($tempReceptionIds);
        $receptionMap = [];
        foreach ($existingReceptions as $item) {
            $receptionMap[$item->temp_reception_id] = $item;
        }

        $tempReceptionDataIds = array_unique(
            array_column(
                $input['receptionData'],
                'tempReceptionDataId'
            )
        );

        $existingReceptionData = $this->Terrasync_model->get_reception_data_by_temp_ids($tempReceptionDataIds);
        $receptionDataMap = [];
        foreach ($existingReceptionData as $item) {
            $key = $item->temp_reception_id . '_' . $item->temp_reception_data_id;
            $receptionDataMap[$key] = $item;
        }

        foreach ($input['receptionData'] as $receptiondata) {

            $isDeleted = filter_var($receptiondata['isDeleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // =================
            // FETCH EXIST RECEPTION DATA
            // =================
            $reception = $receptionMap[$receptiondata['tempReceptionId']] ?? null;
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

            if ($supplierProductTypeId == self::PRODUCT_TYPE_LOG || $supplierProductTypeId == self::PRODUCT_TYPE_TIMBER) {
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

            $key = $receptiondata['tempReceptionId'] . '_' . $receptiondata['tempReceptionDataId'];
            $receptionDataExists = $receptionDataMap[$key] ?? null;

            if (!$receptionDataExists) {
                $receptionData['createdby'] =  $userid;
                $receptionDataId = $this->Terrasync_model->add_reception_data($receptionData);

                $receptionDataMap[$key] = (object) [
                    'reception_data_id' => $receptionDataId,
                    'temp_reception_id' => $receptiondata['tempReceptionId'],
                    'temp_reception_data_id' => $receptiondata['tempReceptionDataId'],
                    'reception_id' => $receptionId
                ];
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
        $tempFarmIds = array_column(
            $farmReceptionTempIds,
            'tempReceptionId'
        );

        $existingFarms = $this->Terrasync_model->get_farms_by_temp_ids($tempFarmIds);
        $farmMap = [];

        foreach ($existingFarms as $item) {
            $farmMap[$item->temp_farm_id] = $item;
        }

        $tempReceptionIds = array_column(
            $farmReceptionTempIds,
            'tempReceptionId'
        );

        $existingReceptions = $this->Terrasync_model->get_receptions_by_temp_ids($tempReceptionIds);
        $receptionMap = [];
        foreach ($existingReceptions as $item) {
            $receptionMap[$item->temp_reception_id] = $item;
        }

        $existingFarmData =
            $this->Terrasync_model->get_farm_data_by_farm_ids(
                array_column(
                    $existingFarms,
                    'farm_id'
                )
            );

        $farmDataMap = [];
        foreach ($existingFarmData as $item) {
            $key = $item->farm_id . '_' . $item->temp_farm_data_id;
            $farmDataMap[$key] = $item;
        }

        $allReceptionDatas = $this->Terrasync_model->get_reception_data_by_temp_reception_ids($tempReceptionIds);
        $receptionDataGrouped = [];

        foreach ($allReceptionDatas as $item) {
            $receptionDataGrouped[$item->temp_reception_id][] = $item;
        }

        $contractIds = [];
        $supplierIds = [];

        foreach ($existingReceptions as $item) {
            $contractIds[] = $item->contract_id;
            $supplierIds[] = $item->supplier_id;
        }

        $contractIds = array_unique($contractIds);
        $supplierIds = array_unique($supplierIds);

        $contractDetailsList = $this->Terramaster_model->get_contract_details_bulk($contractIds, $supplierIds, $originid);

        $contractMap = [];

        foreach ($contractDetailsList as $item) {
            $key = $item->contract_id . '_' . $item->supplier_id;
            $contractMap[$key] = $item;
        }

        $contractPrices = $this->Terramaster_model->fetch_contract_prices_bulk($contractIds);
        $contractPriceMap = [];
        foreach ($contractPrices as $item) {
            $contractPriceMap[$item->supplier_id][] = $item;
        }

        $supplierTaxes = $this->Terramaster_model->get_supplier_taxes_bulk($supplierIds);
        $supplierTaxMap = [];
        foreach ($supplierTaxes as $item) {
            $supplierTaxMap[$item->supplier_id][] = $item;
        }

        foreach ($farmReceptionTempIds as $farmReception) {
            $tempReceptionId = $farmReception['tempReceptionId'];
            $receptionId = $farmReception['receptionId'];

            // =========================================
            // FETCH RECEPTION
            // =========================================
            $reception = $receptionMap[$tempReceptionId] ?? null;

            if (!$reception) {
                continue;
            }

            // =========================================
            // CHECK FARM EXISTS
            // =========================================
            $farmExists = $farmMap[$tempReceptionId] ?? null;

            // =========================================
            // FETCH CONTRACT DETAILS
            // =========================================
            $contractKey = $reception->contract_id . '_' . $reception->supplier_id;
            $contractDetails =  $contractMap[$contractKey] ?? null;

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
            ];

            // =========================================
            // INSERT / UPDATE FARM
            // =========================================
            if (!$farmExists) {

                $farmData['created_by'] = $userid;
                $farmId = $this->Terrasync_model->add_farm($farmData);

                $farmMap[$tempReceptionId] = (object) [
                    'farm_id' => $farmId,
                    'temp_farm_id' => $tempReceptionId
                ];
            } else {

                $farmId = $farmExists->farm_id ?? 0;
                $this->Terrasync_model->update_farm($farmId, $tempReceptionId, $farmData);
            }

            // =========================================
            // FETCH RECEPTION DATA
            // =========================================
            $receptionDatas = $receptionDataGrouped[$tempReceptionId] ?? [];

            // =========================================
            // LOOP RECEPTION DATA
            // =========================================
            foreach ($receptionDatas as $receptionData) {

                // =====================================
                // CHECK FARM DETAIL EXISTS
                // =====================================
                $farmKey = $farmId . '_' . $receptionData->temp_reception_data_id;
                $farmDetailExists =  $farmDataMap[$farmKey] ?? null;

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
                    $farmId = $this->Terrasync_model->add_farm_data($farmDetailData);

                    $farmDataMap[$farmKey] = (object) [
                        'farm_id' => $farmId,
                        'temp_farm_data_id' => $receptionData->temp_reception_data_id
                    ];
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

                if ($farmProductTypeId == self::PRODUCT_TYPE_LOG || $farmProductTypeId == self::PRODUCT_TYPE_TIMBER) {
                } else {

                    //CALCULATE WOOD VALUE & TAXES
                    $farmDataShorts = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 1);
                    $farmDataSemi = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 2);
                    $farmDataLongs = $this->Terrasync_model->get_farm_data_by_farm_id_and_length($farmId, 3);

                    $fetchContractPrice = $contractPriceMap[$farmContractId] ?? [];
                    $exchangeRate = $this->Terramaster_model->fetch_exchange_rate_by_date($farmPurchaseDate);

                    if ($farmPurchaseUnitId == 15) {
                        $price = $fetchContractPrice[0]->pricerange_grade3 ?? 0;
                        $woodValue = $price;
                    } else {

                        $shortResults = $this->process_price_ranges($farmDataShorts, $fetchContractPrice, 'pricerange_grade3', $farmPurchaseUnitId);
                        $semiResults = $this->process_price_ranges($farmDataSemi, $fetchContractPrice, 'pricerange_grade_semi', $farmPurchaseUnitId);
                        $longResults =  $this->process_price_ranges($farmDataLongs, $fetchContractPrice, 'pricerange_grade_longs', $farmPurchaseUnitId);

                        $finalArray = array_merge($shortResults,  $semiResults, $longResults);

                        $woodValue = 0;

                        foreach ($finalArray as $item) {
                            $woodValue += $item['value'];
                        }
                    }
                }

                if ($woodValue > 0) {
                    if ($farmCurrencyId == 1) {
                        $exchangeValue = $exchangeRate[0]->value ?? 0;
                        if ($exchangeValue > 0 && $woodValue > 0) {
                            $woodValue = $woodValue * $exchangeValue;
                        }
                    }

                    // =====================================
                    // WOOD VALUE WITH TAXES
                    // =====================================
                    $getSupplierTaxes = $supplierTaxMap[$farmSupplierId] ?? [];
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
        $tempDispatchIds = array_unique(
            array_column(
                $input['dispatchDetails'],
                'tempDispatchId'
            )
        );

        $existingDispatches = $this->Terrasync_model->get_dispatches_by_temp_ids($tempDispatchIds);
        $dispatchMap = [];
        foreach ($existingDispatches as $item) {
            $dispatchMap[$item->temp_dispatch_id] = $item;
        }

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
            $dispatchExists = $dispatchMap[$dispatchdetail['tempDispatchId']] ?? null;

            // =====================
            // INSERT
            // =====================
            if (!$dispatchExists) {
                $dispatchDetailData['createdby'] = $userid;
                $dispatchDetailData['temp_dispatch_id'] = $dispatchdetail['tempDispatchId'];
                $dispatchDetailData['dispatched_timestamp'] = $dispatchdetail['createdAt'];

                $dispatchId = $this->Terrasync_model->add_dispatch($dispatchDetailData);

                $dispatchMap[$dispatchdetail['tempDispatchId']] = (object) [
                    'dispatch_id' => $dispatchId,
                    'temp_dispatch_id' => $dispatchdetail['tempDispatchId']
                ];
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
        $tempDispatchIds = array_unique(
            array_column(
                $input['containerData'],
                'tempDispatchId'
            )
        );

        $existingDispatches = $this->Terrasync_model->get_dispatches_by_temp_ids($tempDispatchIds);
        $dispatchMap = [];
        foreach ($existingDispatches as $item) {
            $dispatchMap[$item->temp_dispatch_id] = $item;
        }

        $tempReceptionDataIds = array_unique(
            array_column(
                $input['containerData'],
                'tempReceptionDataId'
            )
        );

        $existingReceptionData = $this->Terrasync_model->get_reception_data_by_temp_ids($tempReceptionDataIds);
        $receptionDataMap = [];

        foreach ($existingReceptionData as $item) {
            $key = $item->temp_reception_id . '_' . $item->temp_reception_data_id;
            $receptionDataMap[$key] = $item;
        }

        $existingDispatchData = $this->Terrasync_model->get_dispatch_data_by_temp_ids($tempDispatchIds);
        $dispatchDataMap = [];
        foreach ($existingDispatchData as $item) {
            $key = $item->temp_dispatch_id . '_' . $item->temp_reception_id . '_' . $item->temp_reception_data_id;
            $dispatchDataMap[$key] = $item;
        }

        $recalculateStocks = [];

        foreach ($input['containerData'] as $containerdata) {

            $isDeleted = filter_var(
                $containerdata['isDeleted'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            // =========================
            // FETCH DISPATCH
            // =========================
            $dispatch = $dispatchMap[$containerdata['tempDispatchId']] ?? null;

            $dispatchId = $dispatch->dispatch_id ?? 0;

            if ($dispatchId <= 0) {
                throw new Exception('Dispatch header not found');
            }

            // =========================
            // FETCH RECEPTION DATA
            // =========================
            $key =  $containerdata['tempReceptionId'] . '_' . $containerdata['tempReceptionDataId'];
            $receptionDataExists = $receptionDataMap[$key] ?? null;
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
            $dispatchKey = $containerdata['tempDispatchId'] . '_'  . $containerdata['tempReceptionId'] . '_' . $containerdata['tempReceptionDataId'];
            $containerDataExists = $dispatchDataMap[$dispatchKey] ?? null;

            // =====================================================
            // INSERT
            // =====================================================
            if (!$containerDataExists) {
                $containerData['createdby'] = $userid;
                $dispatchDataId = $this->Terrasync_model->add_dispatch_data($containerData);

                $dispatchDataMap[$dispatchKey] = (object) [
                    'dispatch_data_id' => $dispatchDataId,
                    'temp_dispatch_id' => $containerdata['tempDispatchId'],
                    'temp_reception_id' => $containerdata['tempReceptionId'],
                    'temp_reception_data_id' => $containerdata['tempReceptionDataId']
                ];
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
            $remainingStock = (float) $this->Terrasync_model->recalculate_remaining_stock($containerdata['tempReceptionId'], $containerdata['tempReceptionDataId']);

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
    // PROCESS PRICE RANGES
    // =====================================================
    private function process_price_ranges(array $items, array $contractPrices, string $priceColumn, int $purchaseUnitId)
    {
        $results = [];
        $price = 0;
        foreach ($items as $item) {
            foreach ($contractPrices as $range) {

                if ($item->circumference >= $range->minrange_grade1 && $item->circumference <= $range->maxrange_grade2) {
                    $price = $range->$priceColumn;
                    break;
                }
            }

            $value = ($purchaseUnitId == 3) ? round($price * $item->no_of_pieces, 3) : round($price * $item->volume, 3);

            $results[] = [
                'price' => $price,
                'value' => $value
            ];
        }

        return $results;
    }
}
