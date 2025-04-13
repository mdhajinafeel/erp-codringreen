<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expenseadvanceregistry extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("User_model");
        $this->load->model("Expense_model");
        $this->load->model("Financemaster_model");
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
        $data["title"] = $this->lang->line("advanceregistry_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_expenseledger";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("expensetrackers/advanceregistry", $data, TRUE);
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
                $result = $result . " (" .$getcurrencycode[0]->currency_code .")";
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

    public function save_advance_registry()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "advanceregistry") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "save") {

                    $origin_id = $this->input->post("origin_id");
                    $beneficary_name = $this->input->post('beneficary_name');
                    $amount = $this->input->post("amount");
                    $transaction_date = $this->input->post("transaction_date");
                    $transactionCode = $this->transactionCodeSequence();

                    $dataTransaction = array(
                        "transaction_display_id" => $transactionCode, "user_id" => $beneficary_name,
                        "transaction_type" => 1, "amount" => $amount,
                        "transaction_date" => $transaction_date, "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'], "is_active" => 1,
                        "origin_id" => $origin_id, "temp_expense_id" => $transactionCode
                    );

                    $insertTransaction = $this->Expense_model->add_transaction($dataTransaction);

                    if ($insertTransaction > 0) {
                        $Return["result"] = $this->lang->line("data_added");
                        $Return["error"] = "";
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    } else {
                        $Return["result"] = "";
                        $Return["error"] = $this->lang->line("error_adding");
                        $Return["csrf_hash"] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
                }
            } else {
                redirect("/logout");
            }
        } else {
            $Return["error"] = $this->lang->line("invalid_request");
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    private function transactionCodeSequence()
    {
        $transaction_record_count = $this->Expense_model->transaction_record_count();
        $result = '';
        if ($transaction_record_count > 0) {
            $lenDataCount = strlen($transaction_record_count);
            if ($lenDataCount == 1) {
                $result = '0000000' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 2) {
                $result = '000000' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 3) {
                $result = '00000' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 4) {
                $result = '0000' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 5) {
                $result = '000' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 6) {
                $result = '00' . ($transaction_record_count + 1);
            } else if ($lenDataCount == 7) {
                $result = '0' . ($transaction_record_count + 1);
            } else {
                $result = ($transaction_record_count + 1);
            }
        } else {
            $result = '00000001';
        }
        return $result;
    }
}
