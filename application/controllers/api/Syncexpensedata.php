<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Syncexpensedata extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("Expense_model");
        $this->load->library("jwttoken");
        $this->load->helper('url');
    }

    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }

    public function index()
    {
        try {
            if ($this->input->method(TRUE) !== "POST") {
                return $this->jsonResponse(["status" => false, "message" => "Invalid Method"], 405);
            }

            /* ================= AUTH ================= */

            $headers = apache_request_headers();
            if (empty($headers["Authorization"])) {
                return $this->jsonResponse(["status" => false, "message" => "Token missing"], 401);
            }

            $token = JWT::decode(explode(" ", $headers["Authorization"])[1], JWT_SECRET);

            $userid   = $token->userid;
            $originid = $token->originid;
            $roleid   = $token->roleid;

            if (!$this->Login_model->check_user_exists($userid, $originid, $roleid)) {
                return $this->jsonResponse(["status" => false, "message" => "Unauthorized"], 401);
            }

            /* ================= PAYLOAD ================= */

            $payload = json_decode(file_get_contents("php://input"), true);
            $expenses = $payload["expenseData"] ?? [];

            if (empty($expenses)) {
                return $this->jsonResponse(["status" => true, "data" => []]);
            }

            $response = [];

            foreach ($expenses as $row) {

                $timestamp = $row["capturedTimeStamp"];
                $creditTransactionId = $row["creditTransactionId"];
                $transactionId = $row["transactionId"];
                $transactionDisplayId = $row["transactionDisplayId"];
                $isDeleted = filter_var($row["isDeleted"] ?? false, FILTER_VALIDATE_BOOLEAN);
                $isEdited  = filter_var($row["isDataEdited"] ?? false, FILTER_VALIDATE_BOOLEAN);
                $isForestry  = filter_var($row["isForestry"] ?? false, FILTER_VALIDATE_BOOLEAN);
                $forestryCostType = $row["forestryCostType"];

                $exists = $this->Expense_model->check_existing_transaction_count($timestamp, $userid, $originid);

                $this->db->trans_start();

                if ($isDeleted) {
                    /* ================= DELETE ================= */
                    if ($exists) {
                        $this->Expense_model->delete_transaction_by_temp_id($timestamp, $transactionId, $transactionDisplayId, $userid, $originid);

                        $status = true;
                        $message = "Deleted successfully";
                        $isDataDeleted = true;
                    } else {
                        $status = false;
                        $message = "No data to delete";
                        $isDataDeleted = false;
                    }
                } elseif ($exists && $isEdited) {
                    /* ================= UPDATE ================= */
                    $this->Expense_model->update_transaction(
                        $transactionId,
                        $transactionDisplayId,
                        $timestamp,
                        $userid,
                        $originid,
                        [
                            "credit_transaction_id" => $creditTransactionId,
                            "amount" => $row["amount"],
                            "transaction_date" => $row["expenseDate"],
                            "updated_by" => $userid, 
                            "is_forestry" => $isForestry,
                            "forestry_cost_type" => $forestryCostType
                        ]
                    );

                    $this->Expense_model->update_expense_detail(
                        $transactionId,
                        $transactionDisplayId,
                        [
                            "account_head" => $row["accountHeadId"],
                            "beneficiary_name" => $row["beneficiaryName"],
                            "document_number" => $row["beneficiaryIdentification"],
                            "expense_date" => $row["expenseDate"],
                            "expense_uploaded_image" => $row["attachFileUrl"],
                            "updated_by" => $userid
                        ]
                    );

                    $status = true;
                    $message = "Updated successfully";
                    $isDataDeleted = false;
                } elseif (!$exists) {
                    /* ================= INSERT ================= */

                    $transactionDisplayId = $this->transactionCodeSequence();

                    $transactionId = $this->Expense_model->add_transaction([
                        "credit_transaction_id" => $creditTransactionId,
                        "transaction_display_id" => $transactionDisplayId,
                        "user_id" => $userid,
                        "origin_id" => $originid,
                        "transaction_type" => 2,
                        "amount" => $row["amount"],
                        "transaction_date" => $row["expenseDate"],
                        "expense_timestamp" => $timestamp,
                        "temp_expense_id" => "T_" . $timestamp,
                        "created_by" => $userid,
                        "updated_by" => $userid,
                        "is_active" => 1,
                        "is_forestry" => $isForestry,
                        "forestry_cost_type" => $forestryCostType
                    ], true);


                    $this->Expense_model->add_expense_detail([
                        "transaction_id" => $transactionId,
                        "transaction_display_id" => $transactionDisplayId,
                        "expense_type" => 1,
                        "account_head" => $row["accountHeadId"],
                        "beneficiary_name" => $row["beneficiaryName"],
                        "document_number" => $row["beneficiaryIdentification"],
                        "expense_date" => $row["expenseDate"],
                        "expense_uploaded_image" => $row["attachFileUrl"],
                        "created_by" => $userid,
                        "updated_by" => $userid,
                        "is_active" => 1
                    ]);

                    $status = true;
                    $message = "Inserted successfully";
                    $isDataDeleted = false;
                } else {
                    $status = true;
                    $message = "No action needed";
                    $isDataDeleted = false;
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $status = false;
                    $message = "Database error";
                    $isDataDeleted = false;
                }

                $response[] = [
                    "transactionId" => $transactionId,
                    "transactionDisplayId" => $transactionDisplayId,
                    "expenseTimeStamp" => $timestamp,
                    "status" => $status,
                    "message" => $message,
                    "dataDeleted" => $isDataDeleted
                ];
            }

            return $this->jsonResponse([
                "status" => true,
                "message" => "Sync completed",
                "data" => $response
            ], 200);
        } catch (Exception $e) {
            return $this->jsonResponse([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    private function transactionCodeSequence()
    {
        $count = $this->Expense_model->transaction_record_count(2);
        $next = $count + 1;

        // Always return an 8-digit padded code
        return "D" . str_pad($next, 8, '0', STR_PAD_LEFT);
    }
}
