<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Suppliercredit extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Expense_model");
        $this->load->model("Contract_model");
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
        $data["title"] = $this->lang->line("supplier_credit_header") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_suppliercredit";
        if (!empty($session)) {
            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("financecredits/suppliercredit.php", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function get_purchase_contracts_by_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getContracts = $this->Contract_model->fetch_purchase_contract_by_type($this->input->get("originid"), 1);
                foreach ($getContracts as $contract) {
                    $result = $result . "<option value='" . $contract->contract_id . "'>" . ($contract->contract_code) . "</option>";
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

    public function get_suppliers_by_contract_origin()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line("select") . "</option>";
            if ($this->input->get("originid") > 0) {
                $getSuppliers = $this->Contract_model->get_suppliers_by_contract_origin($this->input->get("originid"), $this->input->get("contractid"));
                foreach ($getSuppliers as $supplier) {
                    $result = $result . "<option value='" . $supplier->id . "'>" . ($supplier->supplier_name) . "</option>";
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

    public function save_supplier_credit()
    {
        $Return = array("result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");
        if ($this->input->post("add_type") == "advanceregistry") {
            if (!empty($session)) {
                if ($this->input->post("action_type") == "save") {

                    $origin_id = $this->input->post("origin_id");
                    $purchase_contract = $this->input->post('purchase_contract');
                    $supplier_name = $this->input->post('supplier_name');
                    $amount = $this->input->post("amount");
                    $transaction_date = $this->input->post("transaction_date");

                    $date = str_replace('/', '-', $transaction_date);
                    $transaction_date = date('Y-m-d', strtotime($date));

                    $dataTransaction = array(
                        "contract_id" => $purchase_contract, "supplier_id" => $supplier_name,
                        "inventory_order" => "", "ledger_type" => 1, "expense_type" => 0,
                        "amount" => $amount, "expense_date" => $transaction_date, "created_by" => $session['user_id'],
                        "updated_by" => $session['user_id'], "is_active" => 1, "is_advance_app" => 0, "user_type" => 1
                    );

                    $insertTransaction = $this->Financemaster_model->add_inventory_expense_ledger($dataTransaction);

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
}