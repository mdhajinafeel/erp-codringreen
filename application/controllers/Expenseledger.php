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
        $this->load->model("Expense_model");
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
        $data["title"] = $this->lang->line("expenseledger_title") . " - " . $this->lang->line("finance_title") .  " | " . $this->Settings_model->site_title();
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
                foreach ($getCreditTransactions as $credittransaction) {

                    $amount = $credittransaction->amount;
                    $amount = $fmt->formatCurrency($amount, $currencyFormat);

                    $creditTransaction = array(
                        "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("edit") . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editcreditamount" data-toggle="modal" data-target=".edit-modal-data" data-transaction_id="' . $credittransaction->transaction_id . '" data-transaction_display_id="' . $credittransaction->transaction_display_id . '"><span class="fas fa-pencil"></span></button></span>
                            <span style="margin-left:6px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletecreditamount" data-toggle="modal" data-target=".edit-modal-data" data-transaction_id="' . $credittransaction->transaction_id . '" data-transaction_display_id="' . $credittransaction->transaction_display_id . '"><span class="fas fa-trash"></span></button></span>',
                        "transactionId" => $credittransaction->transaction_id,
                        "transactionDisplayId" => $credittransaction->transaction_display_id,
                        "transactionDate" => $credittransaction->transaction_date,
                        "conceptGeneral" => $credittransaction->concept_general,
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
                        "action" => '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewdebittransaction" data-toggle="modal" data-target=".view-modal-data" data-transaction_id="' . $debittransaction->transaction_id . '" data-user_id = "' . $debittransaction->user_id . '" data-transaction_display_id="' . $debittransaction->transaction_display_id . '"><span class="fas fa-eye"></span></button></span>
                        <span style="margin-left:6px;" data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("delete") . '"><button type="button" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-role="deletedebittransaction" data-toggle="modal" data-target=".delete-modal-data" data-transaction_id="' . $debittransaction->transaction_id . '" data-transaction_display_id="' . $debittransaction->transaction_display_id . '"><span class="fas fa-trash"></span></button></span>',
                        "transactionDisplayId" => $debittransaction->transaction_display_id,
                        "transactionDate" => $debittransaction->transaction_date,
                        "amount" => $amount,
                        "expenseType" => $debittransaction->expensetype,
                        "beneficiaryName" => $debittransaction->beneficiary_name,
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

    public function dialog_expense_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "viewcredit") {

                $transactionId = $this->input->get('tid');
                $displayId = $this->input->get('did');
                $originId = $this->input->get('oid');
                $userId = $this->input->get('uid');

                $getFinanceDetails = $this->Financemaster_model->get_credit_details_by_id($transactionId, $displayId, $originId, $userId);

                $data = array(
                    'pageheading' => $this->lang->line('credit_details'),
                    'pagetype' => 'update',
                    'transactionId' => $transactionId,
                    'displayId' => $displayId,
                    'originId' => $originId,
                    'userId' => $userId,
                    'csrf_hash' => $this->security->get_csrf_hash(),
                    'credit_details' => $getFinanceDetails,
                );
                $this->load->view('expensetrackers/dialog_view_credit', $data);
            } else if ($this->input->get('type') == "viewdebit") {

                $transactionId = $this->input->get('tid');
                $displayId = $this->input->get('did');
                $originId = $this->input->get('oid');
                $userId = $this->input->get('uid');

                $getFinanceDetails = $this->Financemaster_model->get_debit_details_by_id($transactionId, $displayId, $originId, $userId);

                $data = array(
                    'pageheading' => $this->lang->line('debit_details'),
                    'pagetype' => 'update',
                    'transactionId' => $transactionId,
                    'displayId' => $displayId,
                    'originId' => $originId,
                    'userId' => $userId,
                    'csrf_hash' => $this->security->get_csrf_hash(),
                    'credit_details' => $this->Expense_model->fetch_credit_transactions($originId, $userId),
                    'account_heads' => $this->Financemaster_model->all_account_heads($originId),
                    'debit_details' => $getFinanceDetails,
                );
                $this->load->view('expensetrackers/dialog_view_debit', $data);
            } else if ($this->input->get('type') == "deletecreditconfirmation") {
                $data = array(
                    'pageheading' => $this->lang->line('confirmation'),
                    'pagemessage' => $this->lang->line('delete_message'),
                    'inputid' => $this->input->get('tid'),
                    'inputid1' => $this->input->get('did'),
                    'inputid2' => $this->input->get('oid'),
                    'inputid3' => $this->input->get('uid'),
                    'actionurl' => "expenseledger/dialog_expense_action",
                    'actiontype' => "deletecredit",
                    'xin_table' => "#xin_table_credits",
                );
                $this->load->view('dialogs/dialog_confirmation_expense_ledger', $data);
            } else if ($this->input->get('type') == "deletecredit") {

                $transactionId = $this->input->get('inputid');
                $displayId = $this->input->get('inputid1');

                $creditDelete = $this->Financemaster_model->delete_credit_transaction($transactionId, $displayId, $session['user_id']);

                if ($creditDelete) {
                    $Return['result'] = $this->lang->line('data_deleted');
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_deleting');
                    $Return['result'] = "";
                    $Return['redirect'] = false;
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function update_creditdetails()
    {
        $Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if ($this->input->post('add_type') == 'creditdetails') {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originid = $this->input->post('originid');
                $transactionid = $this->input->post('transactionid');
                $displayid = $this->input->post('displayid');
                $userid = $this->input->post('userid');
                $amount = $this->input->post('amount');
                $conceptgeneral = $this->input->post('conceptgeneral');
                $transactiondate = $this->input->post('transactiondate');

                $dataCreditDetails = array(
                    "amount" => $amount,
                    "transaction_date" => $transactiondate,
                    "concept_general" => $conceptgeneral,
                    "updated_by" => $session['user_id'],
                );

                $updateCreditDetails = $this->Financemaster_model->update_credit_details($transactionid, $displayid, $userid, $originid, $dataCreditDetails);

                if ($updateCreditDetails == true) {
                    $Return['result'] = $this->lang->line('data_updated');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_updating');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return['error'] = "";
                $Return['result'] = "";
                $Return['redirect'] = true;
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }

    public function update_debitdetails()
    {
        $Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if ($this->input->post('add_type') == 'debitdetails') {

            if (!empty($session)) {

                $Return['csrf_hash'] = $this->security->get_csrf_hash();

                $originid = $this->input->post('originid');
                $transactionid = $this->input->post('transactionid');
                $displayid = $this->input->post('displayid');
                $userid = $this->input->post('userid');

                $concept_general = $this->input->post('concept_general');
                $account_head = $this->input->post('account_head');
                $expense_date = $this->input->post('expense_date');
                $beneficiary_name = $this->input->post('beneficiary_name');
                $document_number = $this->input->post('document_number');
                $amount = $this->input->post('amount');

                // 🔹 Existing file URL from hidden input
                $existing_file_url = $this->input->post('existing_file_url');

                $file_url = $existing_file_url; // default: keep old file

                // ---- FILE UPLOAD HANDLING ----
                if (!empty($_FILES['expense_attachemnt_file']['name'])) {

                    // Custom file name (NO extension)
                    $customFileName = 'att_' . $userid . '_' . time();

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
                    $file_url = base_url('uploads/expensedocuments/' . $uploadData['file_name']);
                }


                $dataDebitDetails = array(
                    "credit_transaction_id" => $concept_general,
                    "amount" => $amount,
                    "transaction_date" => $expense_date,
                    "updated_by" => $session['user_id'],
                );

                $updateDebitDetails = $this->Financemaster_model->update_debit_details($transactionid, $displayid, $userid, $originid, $dataDebitDetails);

                if ($updateDebitDetails == true) {

                    $dataDebitExpenseDetails = array(
                        "expense_type" => 1,
                        "account_head" => $account_head,
                        "beneficiary_name" => $beneficiary_name,
                        "document_number" => $document_number,
                        "expense_date" => $expense_date,
                        "expense_uploaded_image" => $file_url,
                        "updated_by" => $session['user_id'],
                    );

                    $updateDebitExpenseDetails = $this->Financemaster_model->update_debit_expense_details($transactionid, $displayid, $dataDebitExpenseDetails);

                    if($updateDebitExpenseDetails == false){
                        $Return['error'] = $this->lang->line('error_updating');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }

                    $Return['result'] = $this->lang->line('data_updated');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('error_updating');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return['error'] = "";
                $Return['result'] = "";
                $Return['redirect'] = true;
                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }
}
