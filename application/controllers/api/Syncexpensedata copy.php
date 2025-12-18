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

                $expense_arr_response = array();

                if ($userid > 0) {

                    $checkUserExists = $this->Login_model->check_user_exists($userid, $originid, $roleid);
                    if ($checkUserExists == true) {

                        $requestdata = json_decode(file_get_contents("php://input"), true);
                        $expenseData = $requestdata["expenseData"];

                        if (count($expenseData) > 0) {
                            foreach ($expenseData as $key => $value) {
                                $row_expense_array = array();

                                $transactionId = $value["transactionId"];
                                $transactionDisplayId = $this->transactionCodeSequence();
                                $capturedTimeStamp = $value["capturedTimeStamp"];
                                $accountHeadId = $value["accountHeadId"];
                                $beneficiaryName = $value["beneficiaryName"];
                                $beneficiaryIdentification = $value["beneficiaryIdentification"];
                                $expenseDate = $value["expenseDate"];
                                $attachFileUrl = $value["attachFileUrl"];
                                $amount = $value["amount"];
                                $isDeleted     = filter_var($value["isDeleted"] ?? false, FILTER_VALIDATE_BOOLEAN);
                                $isDataEdited  = filter_var($value["isDataEdited"] ?? false, FILTER_VALIDATE_BOOLEAN);

                                $transactionCount = $this->Expense_model->check_existing_transaction_count($capturedTimeStamp, $userid, $originid);

                                // DELETE EXPENSE
                                if ($isDeleted === true) {
                                    if ($transactionCount > 0) {
                                        // Delete the transaction
                                        $deletedTransaction = $this->Expense_model->delete_transaction_by_temp_id($capturedTimeStamp, $userid, $originid);

                                        $row_expense_array["transactionId"] = $transactionId;
                                        $row_expense_array["transactionDisplayId"] = $transactionDisplayId;
                                        $row_expense_array["expenseTimeStamp"] = $capturedTimeStamp;

                                        if ($deletedTransaction) {
                                            $row_expense_array["status"] = true;
                                            $row_expense_array["message"] = "Deleted successfully";
                                        } else {
                                            $row_expense_array["status"] = false;
                                            $row_expense_array["message"] = "Failed to delete";
                                        }

                                        array_push($expense_arr_response, $row_expense_array);
                                    } else {

                                        $row_expense_array["transactionId"] = $transactionId;
                                        $row_expense_array["transactionDisplayId"] = $transactionDisplayId;
                                        $row_expense_array["expenseTimeStamp"] = $capturedTimeStamp;
                                        $row_expense_array["status"] = false;
                                        $row_expense_array["message"] = "No Data to delete";

                                        array_push($expense_arr_response, $row_expense_array);
                                    }
                                } else {

                                    if ($isDataEdited === true) {
                                        if ($transactionCount > 0) {
                                            // Update existing transaction

                                            $dataTransaction = array(
                                                "amount" => $amount,
                                                "transaction_date" => $expenseDate,
                                                "updated_by" => $userid,
                                            );

                                            $updated = $this->Expense_model->update_transaction(
                                                $transactionId,
                                                $transactionDisplayId,
                                                $capturedTimeStamp,
                                                $userid,
                                                $originid,
                                                $dataTransaction
                                            );

                                            if ($updated) {

                                                $dataExpenseDetail = array(
                                                    "expense_type" => 1,
                                                    "account_head" => $accountHeadId,
                                                    "beneficiary_name" => $beneficiaryName,
                                                    "document_type" => 0,
                                                    "document_number" => $beneficiaryIdentification,
                                                    "expense_date" => $expenseDate,
                                                    "expense_uploaded_image" => $attachFileUrl,
                                                    "updated_by" => $userid
                                                );

                                                $expenseUpdatedId = $this->Expense_model->update_expense_detail($transactionId, $transactionDisplayId, $dataExpenseDetail);

                                                $row_expense_array["status"] = true;
                                                $row_expense_array["message"] = "Updated successfully";
                                                $row_expense_array["transactionDisplayId"] = $transactionDisplayId;
                                            } else {
                                                $row_expense_array["status"] = false;
                                                $row_expense_array["message"] = "Failed to update";
                                                $row_expense_array["transactionDisplayId"] = "";
                                            }

                                            array_push($expense_arr_response, $row_expense_array);
                                        } else {
                                            // Insert new transaction

                                            $newTransactionDisplayId = $this->transactionCodeSequence();

                                            $dataTransaction = array(
                                                "transaction_display_id" => $newTransactionDisplayId,
                                                "user_id" => $userid,
                                                "transaction_type" => 2, // Expense
                                                "amount" => $amount,
                                                "transaction_date" => $expenseDate,
                                                "expense_timestamp" => $capturedTimeStamp,
                                                "temp_expense_id" => 'T_' . $capturedTimeStamp,
                                                "concept_general" => "",
                                                "created_by" => $userid,
                                                "updated_by" => $userid,
                                                "is_active" => 1,
                                                "origin_id" => $originid,
                                            );

                                            $insertedId = $this->Expense_model->add_transaction($dataTransaction, true);

                                            $row_expense_array["transactionId"] = $insertedId;
                                            $row_expense_array["expenseTimeStamp"] = $capturedTimeStamp;

                                            if ($insertedId > 0) {

                                                $dataExpenseDetail = array(
                                                    "transaction_id" => $insertedId,
                                                    "transaction_display_id" => $newTransactionDisplayId,
                                                    "expense_type" => 1,
                                                    "account_head" => $accountHeadId,
                                                    "beneficiary_name" => $beneficiaryName,
                                                    "document_type" => 0,
                                                    "document_number" => $beneficiaryIdentification,
                                                    "expense_date" => $expenseDate,
                                                    "expense_uploaded_image" => $attachFileUrl,
                                                    "created_by" => $userid,
                                                    "updated_by" => $userid,
                                                    "is_active" => 1,
                                                );

                                                $expenseInsertedId = $this->Expense_model->add_expense_detail($dataExpenseDetail);

                                                $row_expense_array["status"] = true;
                                                $row_expense_array["message"] = "Inserted successfully";
                                                $row_expense_array["transactionDisplayId"] = $newTransactionDisplayId;
                                            } else {
                                                $row_expense_array["status"] = false;
                                                $row_expense_array["message"] = "Failed to insert";
                                                $row_expense_array["transactionDisplayId"] = "";
                                            }

                                            array_push($expense_arr_response, $row_expense_array);
                                        }
                                    } else {
                                        // Insert new transaction

                                        $newTransactionDisplayId = $this->transactionCodeSequence();

                                        $dataTransaction = array(
                                            "transaction_display_id" => $newTransactionDisplayId,
                                            "user_id" => $userid,
                                            "transaction_type" => 2, // Expense
                                            "amount" => $amount,
                                            "transaction_date" => $expenseDate,
                                            "expense_timestamp" => $capturedTimeStamp,
                                            "temp_expense_id" => 'T_' . $capturedTimeStamp,
                                            "concept_general" => "",
                                            "created_by" => $userid,
                                            "updated_by" => $userid,
                                            "is_active" => 1,
                                            "origin_id" => $originid,
                                        );

                                        $insertedId = $this->Expense_model->add_transaction($dataTransaction, true);

                                        if ($insertedId > 0) {
                                            $dataExpenseDetail = array(
                                                "transaction_id" => $insertedId,
                                                "transaction_display_id" => $newTransactionDisplayId,
                                                "expense_type" => 1,
                                                "account_head" => $accountHeadId,
                                                "beneficiary_name" => $beneficiaryName,
                                                "document_type" => 0,
                                                "document_number" => $beneficiaryIdentification,
                                                "expense_date" => $expenseDate,
                                                "expense_uploaded_image" => $attachFileUrl,
                                                "created_by" => $userid,
                                                "updated_by" => $userid,
                                                "is_active" => 1,
                                                "origin_id" => $originid,
                                            );

                                            $insertedId = $this->Expense_model->add_expense_detail($dataExpenseDetail);
                                        }


                                        $row_expense_array["transactionId"] = $insertedId;
                                        $row_expense_array["expenseTimeStamp"] = $capturedTimeStamp;

                                        if ($insertedId > 0) {
                                            $row_expense_array["status"] = true;
                                            $row_expense_array["message"] = "Inserted successfully";
                                            $row_expense_array["transactionDisplayId"] = $newTransactionDisplayId;
                                        } else {
                                            $row_expense_array["status"] = false;
                                            $row_expense_array["message"] = "Failed to insert";
                                            $row_expense_array["transactionDisplayId"] = "";
                                        }
                                    }

                                    array_push($expense_arr_response, $row_expense_array);
                                }
                            }
                        }

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $expense_arr_response;
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
            $Return['status'] = false;
            $Return['message'] = 'Error: ' . $e->getMessage();
            http_response_code(500);
            $this->output($Return);
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
