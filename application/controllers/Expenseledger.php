<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expenseledger extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("User_model");
        $this->load->model("Financemaster_model");
        $this->load->library('excel');
    }

    public function output($Return = array())
    {
        /*Set response header*/
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        /*Final JSON response*/
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("viewledger_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_expenseledger";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("expensetrackers/expenseledger", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_expense_ledger_users()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getExpenseUsers = $this->User_model->get_expense_ledger_users($this->input->get("originid"));
                foreach ($getExpenseUsers as $expsenseuser) {
                    $result = $result . "<option value='" . $expsenseuser->userid . "'>" . $expsenseuser->fullname . "</option>";
                }
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_currency_code()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = $this->lang->line('amount');
            if ($this->input->get("originid") > 0) {
                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));
                $result = $result . " (" . $getcurrencycode[0]->currency_code . ")";
            }

            $Return["result"] = $result;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function get_ledger_details_by_user()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getExpenseLedgerDetails = $this->Financemaster_model->fetch_expense_ledger_by_user($this->input->get("originid"), $this->input->get("userid"));
            
            if (count($getExpenseLedgerDetails) > 0) {
                
                $totalCredits = 0;
                $totalDebits = 0;
                $totalOutstanding = 0;

                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));
                $getCreditTransactions = $this->Financemaster_model->get_credit_transactions_by_user($this->input->get("originid"), $this->input->get("userid"));
                $getDebitTransactions = $this->Financemaster_model->get_debit_transactions_by_user($this->input->get("originid"), $this->input->get("userid"));
                
                foreach ($getExpenseLedgerDetails as $expenseledger) {
                    if ($expenseledger->transaction_type == 1) {
                        $totalCredits = $expenseledger->amount;
                    } else if ($expenseledger->transaction_type == 2) {
                        $totalDebits = $expenseledger->amount;
                    }
                }

                $totalCredits = $totalCredits + 0;
                $totalDebits = $totalDebits + 0;
                $totalOutstanding = $totalCredits - $totalDebits;

                $currencyCode = $getcurrencycode[0]->currency_abbreviation;
                $currencyFormat = $getcurrencycode[0]->currency_format;

                $fmt = new NumberFormatter($currencyCode, NumberFormatter::CURRENCY);
                $totalOutstanding = $fmt->formatCurrency($totalOutstanding, $currencyFormat);
                $totalCredits = $fmt->formatCurrency($totalCredits, $currencyFormat);
                $totalDebits = $fmt->formatCurrency($totalDebits, $currencyFormat);

                $creditTransactions = array();
                foreach($getCreditTransactions as $credittransaction){

                    $amount = $credittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);
                   
                    $creditTransaction = array(
                       "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcreditamount" data-toggle="modal" data-target=".edit-modal-data" data-transaction_id="' . $credittransaction->transaction_id . '"><span class="fas fa-pencil"></span></button></span>',
                       "transactionId" => $credittransaction->transaction_id,
                       "transactionDisplayId" => $credittransaction->transaction_display_id,
                       "transactionDate" => $credittransaction->transaction_date,
                       "amount" => $amount,
                       "fullName" => $credittransaction->fullname
                    );

                    array_push($creditTransactions, $creditTransaction);
                }

                $debitTransactions = array();
                foreach($getDebitTransactions as $debittransaction){

                    $amount = $debittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    $debitTransaction = array(
                        "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewdebittransaction" data-toggle="modal" data-target=".view-modal-data" data-transaction_id="' . $debittransaction->transaction_id . '"><span class="fas fa-eye"></span></button></span>
                        <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletedebittransaction" data-toggle="modal" data-target=".delete-modal-data" data-transaction_id="' . $debittransaction->transaction_id . '"><span class="fas fa-trash"></span></button></span>',
                        "transactionDisplayId" => $debittransaction->transaction_display_id,
                        "transactionDate" =>$debittransaction->transaction_date,
                        "amount" => $amount,
                        "expenseType" =>$debittransaction->expensetype,
                        "beneficiaryName" =>$debittransaction->beneficiary_name,
                        "updatedBy" => $debittransaction->updated_by,
                    );

                    array_push($debitTransactions, $debitTransaction);
                }

                $dataTransaction = array(
                    "totalCredits" => $totalCredits,
                    "totalDebits" => $totalDebits,
                    "totalOutstanding" => $totalOutstanding,
                    "creditTransactions" => $creditTransactions,
                    "debitTransactions" => $debitTransactions,
                );

                $Return["result"] = $dataTransaction;
                $Return["error"] = "";
                $Return["pages"] = "";
                $Return["redirect"] = false;
                $this->output($Return);

            } else {
                $Return["error"] = $this->lang->line("common_error");
                $Return["pages"] = "";
                $Return["redirect"] = false;
                $this->output($Return);
            }
        } else {
            $Return["error"] = "";
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }
}