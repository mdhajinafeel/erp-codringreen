<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expensedebitregistry extends MY_Controller
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
        $data["title"] = $this->lang->line("debitregistry_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_expenseledger";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("expensetrackers/debitregistry", $data, TRUE);
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

    public function get_account_heads()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getAccountHeads = $this->Financemaster_model->all_account_heads($this->input->get("originid"));
                foreach ($getAccountHeads as $accountHead) {
                    $result = $result . "<option value='" . $accountHead->id . "'>" . $accountHead->name_in_ledger . "</option>";
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

    public function get_credit_transactions()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0 && $this->input->get("userid") > 0) {
                $getCreditTransactions = $this->Expense_model->fetch_credit_transactions($this->input->get("originid"), $this->input->get("userid"));
                foreach ($getCreditTransactions as $creditTransaction) {
                    $result = $result . "<option value='" . $creditTransaction->transaction_id . "'>" . $creditTransaction->concept_general . ' --- ' . $creditTransaction->transaction_display_id . "</option>";
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

    public function save_debit_registry()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "debitregistry") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "save") {

                    $origin_id = $this->input->post("origin_id");
                    $beneficary_name = $this->input->post('beneficary_name');
                    $user_id = $this->input->post('user_id');
                    $account_head = $this->input->post('account_head');
                    $beneficary_name = $this->input->post('beneficary_name');
                    $document_number = $this->input->post('document_number');
                    $amount = $this->input->post("amount");
                    $concept_general = $this->input->post("concept_general");
                    $expense_date = $this->input->post("expense_date");
                    $transactionCode = $this->transactionCodeSequence();

                    $file_url = ""; // default: keep old file

                    // ---- FILE UPLOAD HANDLING ----
                    if (!empty($_FILES['expense_attachemnt_file']['name'])) {

                        // Custom file name (NO extension)
                        $customFileName = 'att_' . $user_id . '_' . time();

                        $config['upload_path']   = './uploads/expensedocuments/';
                        $config['allowed_types'] = '*';
                        $config['max_size']      = 10240; // 10 MB
                        $config['file_name']     = $customFileName;
                        $config['overwrite']     = false;
                        $config['remove_spaces'] = true;

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('expense_attachemnt_file')) {
                            $Return['error'] = $this->upload->display_errors('', '');
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            return $this->output($Return);
                        }

                        $uploadData = $this->upload->data();

                        // CI automatically appends extension
                        $file_url = base_url('uploads/expenses/' . $uploadData['file_name']);
                    }

                    $dataTransaction = array(
                        "credit_transaction_id" => $concept_general,
                        "transaction_display_id" => $transactionCode,
                        "user_id" => $user_id,
                        "transaction_type" => 2,
                        "amount" => $amount,
                        "transaction_date" => $expense_date,
                        "temp_expense_id" => "T_" . time(),
                        "concept_general" => "",
                        "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'],
                        "is_active" => 1,
                        "origin_id" => $origin_id,
                    );

                    $insertTransaction = $this->Expense_model->add_transaction($dataTransaction, false);

                    $this->Expense_model->add_expense_detail([
                        "transaction_id" => $insertTransaction,
                        "transaction_display_id" => $transactionCode,
                        "expense_type" => 1,
                        "account_head" => $account_head,
                        "beneficiary_name" => $beneficary_name,
                        "document_number" => $document_number,
                        "expense_date" => $expense_date,
                        "expense_uploaded_image" => $file_url,
                        "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'],
                        "is_active" => 1
                    ]);

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
        $count = $this->Expense_model->transaction_record_count(2);
        $next = $count + 1;

        // Always return an 8-digit padded code
        return "D" . str_pad($next, 8, '0', STR_PAD_LEFT);
    }
}
