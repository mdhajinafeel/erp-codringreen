<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Downloadexpensemasters extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Login_model");
        $this->load->model("FinanceMaster_model");
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
                        
                        $row_array_final = array();

                        //ACCOUNT HEADS
                        $fetchAccountHeads = $this->FinanceMaster_model->all_account_heads($originid);
                        $return_arr_accountheads = array();
                        foreach ($fetchAccountHeads as $accounthead) {
                            $row_array_accounthead["accountHeadId"] = (int) $accounthead->id;
                            $row_array_accounthead["accountHeadName"] = $accounthead->name_in_app;
                            $row_array_accounthead["icon"] = $accounthead->icon;
                            $row_array_accounthead["colorCodePrimary"] = $accounthead->color_code_primary;
                            $row_array_accounthead["colorCodeSecondary"] = $accounthead->color_code_secondary;
                            $row_array_accounthead["isForestry"] = (bool) $accounthead->is_forestry;
                            $row_array_accounthead["forestryCostType"] = (int) $accounthead->forestry_cost_type;
                            array_push($return_arr_accountheads, $row_array_accounthead);
                        }
                        $row_array_final["accountHeads"] = $return_arr_accountheads;

                        //BENEFICIARIES
                        $fetchBeneficiaries = $this->Expense_model->fetch_beneficiaries($originid);
                        $return_arr_beneficiaries = array();
                        foreach ($fetchBeneficiaries as $beneficiary) {
                            $row_array_beneficiary["beneficiaryIdentification"] = $beneficiary->document_number;
                            $row_array_beneficiary["beneficiaryName"] = $beneficiary->beneficiary_name;
                            array_push($return_arr_beneficiaries, $row_array_beneficiary);
                        }
                        $row_array_final["beneficiaries"] = $return_arr_beneficiaries;

                        //DEBIT TRANSACTIONS
                        $fetchDebitTransactions = $this->Expense_model->fetch_debit_transactions($originid, $userid);
                        $return_arr_debit_transactions = array();
                        foreach ($fetchDebitTransactions as $debitTransaction) {
                            $row_array_debittransaction["transactionId"] = (int) $debitTransaction->transaction_id;
                            $row_array_debittransaction["transactionDisplayId"] = $debitTransaction->transaction_display_id;
                            $row_array_debittransaction["amount"] = $debitTransaction->amount + 0;
                            $row_array_debittransaction["transactionDate"] = $debitTransaction->transaction_date;
                            $row_array_debittransaction["transactionTimestamp"] = $debitTransaction->expense_timestamp + 0;
                            $row_array_debittransaction["conceptGeneral"] = $debitTransaction->concept_general;
                            array_push($return_arr_debit_transactions, $row_array_debittransaction);
                        }
                        $row_array_final["debitTransactions"] = $return_arr_debit_transactions;

                        //CREDIT TRANSACTIONS
                        $fetchCreditTransactions = $this->Expense_model->fetch_credit_transactions($originid, $userid);
                        $return_arr_credit_transactions = array();
                        foreach ($fetchCreditTransactions as $creditTransaction) {
                            $row_array_credittransaction["transactionId"] = (int) $creditTransaction->transaction_id;
                            $row_array_credittransaction["transactionDisplayId"] = $creditTransaction->transaction_display_id;
                            $row_array_credittransaction["amount"] = $creditTransaction->amount + 0;
                            $row_array_credittransaction["transactionDate"] = $creditTransaction->transaction_date;
                            $row_array_credittransaction["transactionTimestamp"] = $creditTransaction->expense_timestamp + 0;
                            array_push($return_arr_credit_transactions, $row_array_credittransaction);
                        }
                        $row_array_final["creditTransactions"] = $return_arr_credit_transactions;

                        //DEBIT TRANSACTIONS
                        $fetchDebitTransactions = $this->Expense_model->fetch_debit_transactions($originid, $userid);
                        $return_arr_debit_transactions = array();
                        foreach ($fetchDebitTransactions as $debitTransaction) {
                            $row_array_debittransaction["creditTransactionId"] = (int) $debitTransaction->credit_transaction_id;
                            $row_array_debittransaction["transactionTimestamp"] = $debitTransaction->expense_timestamp + 0;
                            $row_array_debittransaction["transactionId"] = (int) $debitTransaction->transaction_id;
                            $row_array_debittransaction["transactionDisplayId"] = $debitTransaction->transaction_display_id;
                            $row_array_debittransaction["accountHeadId"] = (int) $debitTransaction->account_head;
                            $row_array_debittransaction["beneficiaryName"] = $debitTransaction->beneficiary_name;
                            $row_array_debittransaction["beneficiaryIdentification"] = $debitTransaction->document_number;
                            $row_array_debittransaction["expenseDate"] = $debitTransaction->expense_date;
                            $row_array_debittransaction["amount"] = $debitTransaction->amount + 0;
                            $row_array_debittransaction["attachmentFileUrl"] = $debitTransaction->expense_uploaded_image;
                            array_push($return_arr_debit_transactions, $row_array_debittransaction);
                        }
                        $row_array_final["debitTransactions"] = $return_arr_debit_transactions;

                        $Return["status"] = true;
                        $Return["message"] = "";
                        $Return["data"] = $row_array_final;
                        http_response_code(200);
                        $this->output($Return);
                    } else {
                        $Return["status"] = false;
                        $Return["message"] = "Unauthorized";
                        http_response_code(200);
                        $this->output($Return);
                    }
                } else {
                    $Return["status"] = false;
                    $Return["message"] = "Unauthorized";
                    http_response_code(200);
                    $this->output($Return);
                }
            }
        } catch (Exception $e) {
            $Return["status"] = false;
            $Return["message"] = "Internal Server Error";
            http_response_code(200);
            $this->output($Return);
        }
    }
}