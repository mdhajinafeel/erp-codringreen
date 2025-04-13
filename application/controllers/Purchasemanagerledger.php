<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Purchasemanagerledger extends MY_Controller
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
        $data["title"] = $this->lang->line("purchase_manager_ledger_header") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_purchasemanagercredit";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financeledgers/purchasemanagerledger", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_purchasemanager_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getPurchaseManager = $this->User_model->get_purchase_manager_users($this->input->get("originid"));
                foreach ($getPurchaseManager as $purchasemanager) {
                    $result = $result . "<option value='" . $purchasemanager->userid . "'>" . $purchasemanager->fullname . "</option>";
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

    public function get_ledger_details_by_purchasemanager()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getExpenseLedgerDetails = $this->Financemaster_model->get_ledger_details_by_purchasemanager($this->input->get("purchasemanagerid"));

            if (count($getExpenseLedgerDetails) > 0) {

                $totalCredits = 0;
                $totalDebits = 0;
                $totalOutstanding = 0;

                $getcurrencycode = $this->Financemaster_model->get_currency_code($this->input->get("originid"));
                $getCreditTransactions = $this->Financemaster_model->get_credited_transaction_purchasemanager($this->input->get("purchasemanagerid"));
                $getDebitTransactions = $this->Financemaster_model->get_debited_transaction_purchasemanager($this->input->get("purchasemanagerid"));

                $totalCredits = $getExpenseLedgerDetails[0]->creditamount;
                $totalDebits = $getExpenseLedgerDetails[0]->debitamount;

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
                foreach ($getCreditTransactions as $credittransaction) {

                    $amount = $credittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    $creditTransaction = array(
                        "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcreditamount" data-toggle="modal" data-target=".edit-modal-data" data-transaction_id="' . $credittransaction->id . '"><span class="fas fa-pencil"></span></button></span>
                       <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecreditamount" data-toggle="modal" data-target=".delete-modal-data" data-transaction_id="' . $credittransaction->id . '"><span class="fas fa-trash"></span></button></span>',
                        "transactionDate" => $credittransaction->expense_date,
                        "amount" => $amount,
                        "fullName" => $credittransaction->fullname
                    );

                    array_push($creditTransactions, $creditTransaction);
                }

                $debitTransactions = array();
                foreach ($getDebitTransactions as $debittransaction) {

                    $amount = $debittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    $debitTransaction = array(
                        "transactionDate" => $debittransaction->expense_date,
                        "inventoryOrder" => $debittransaction->inventory_order,
                        "amount" => $amount,
                        "transactionType" => $debittransaction->type_name,
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
