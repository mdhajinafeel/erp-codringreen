<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Expense_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function transaction_record_count($transactionType)
    {
        $this->db->where("transaction_type", $transactionType);
        $this->db->from("tbl_transaction");
        return $this->db->count_all_results();
    }

    public function add_transaction($data, $isSync)
    {
        if ($isSync) {
            // NOTHING TO DO, KEEP THE PROVIDED TIMESTAMP
        } else {
            $this->db->set('expense_timestamp', 'CAST(UNIX_TIMESTAMP(NOW()) * 1000 AS unsigned)', FALSE);
        }

        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_transaction', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_transaction($transactionId, $transactionDisplayId, $expenseTimeStamp, $userId, $originId, $data)
    {
        $multiClause = array(
            'transaction_id' => $transactionId,
            'transaction_display_id' => $transactionDisplayId,
            'expense_timestamp' => $expenseTimeStamp,
            'user_id' => $userId,
            'origin_id' => $originId,
            'is_active' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_transaction', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function check_existing_transaction_count($expenseTimeStamp, $userId, $originId)
    {
        $this->db->where("expense_timestamp", $expenseTimeStamp);
        $this->db->where("user_id", $userId);
        $this->db->where("origin_id", $originId);
        return $this->db->count_all_results('tbl_transaction');
    }

    public function delete_transaction_by_temp_id($expenseTimeStamp, $transactionId, $transactionDisplayId, $userId, $originId)
    {
        $this->db->trans_start();

        /* ---- Soft delete transaction ---- */
        $this->db->where([
            "transaction_id" => $transactionId,
            "transaction_display_id" => $transactionDisplayId,
            "expense_timestamp" => $expenseTimeStamp,
            "user_id" => $userId,
            "origin_id" => $originId
        ])->update("tbl_transaction", [
            "is_active" => 0
        ]);

        /* ---- Soft delete expense details ---- */
        $this->db->where([
            "transaction_id" => $transactionId,
            "transaction_display_id" => $transactionDisplayId
        ])->update("tbl_expense_details", [
            "is_active" => 0
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function add_expense_detail($data)
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_expense_details', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_expense_detail($transactionId, $transactionDisplayId, $data)
    {
        $multiClause = array('transaction_id' => $transactionId, 'transaction_display_id' => $transactionDisplayId, 'is_active' => 1);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_expense_details', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function fetch_credit_transactions($originId, $userId)
    {
        $query = "SELECT transaction_id, transaction_display_id, amount, transaction_date, expense_timestamp, concept_general 
            FROM tbl_transaction 
            WHERE is_active = 1 AND origin_id = $originId AND user_id = $userId AND transaction_type = 1";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_debit_transactions($originId, $userId)
    {
        $query = "SELECT A.credit_transaction_id, A.transaction_id, A.transaction_display_id, A.amount, A.expense_timestamp, B.expense_date, B.account_head, 
            B.beneficiary_name, B.document_number, B.expense_uploaded_image
            FROM tbl_transaction A 
            INNER JOIN tbl_expense_details B ON B.transaction_id = A.transaction_id AND B.transaction_display_id = A.transaction_display_id 
            WHERE A.is_active = 1 AND origin_id = $originId AND user_id = $userId AND transaction_type = 2";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_beneficiaries($originId)
    {
        $query = "SELECT DISTINCT beneficiary_name, document_number 
                FROM tbl_expense_details A 
                INNER JOIN tbl_transaction B ON B.transaction_id = A.transaction_id 
                WHERE B.origin_id = $originId";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_expense_ledger_users($originid) {
		$query = $this->db->query("SELECT DISTINCT A.user_id, B.fullname FROM 
        tbl_transaction A 
        INNER JOIN tbl_user_registration B ON B.userid = A.user_id 
        WHERE A.is_active = 1 AND A.origin_id = $originid");
		return $query->result();
	}

    public function get_ledger_report_details($originId, $userId)
    {
        $query = $this->db->query("SELECT fullname FROM tbl_user_registration WHERE userid = $userId AND FIND_IN_SET($originId, applicable_origins);");
        return $query->result();
    }

    public function get_credit_transaction_details($creditTransactionId, $originId)
    {
        if($creditTransactionId > 0) {
            $query = $this->db->query("SELECT concept_general, transaction_display_id, amount 
                FROM tbl_transaction WHERE is_active = 1 AND origin_id = $originId AND transaction_id = $creditTransactionId");
        } else {
            $query = $this->db->query("SELECT '' as concept_general, '' as transaction_display_id, SUM(amount) AS amount 
                FROM tbl_transaction WHERE is_active = 1 AND origin_id = $originId");
        }
        return $query->result();
    }

    public function fetch_expense_report_details($originId, $userId, $fromDate, $toDate, $conceptGeneral, $accountHead) {

        $sqlQuery = "SELECT B.expense_date, B.beneficiary_name, B.document_number, C.name_in_ledger, A.amount, B.expense_uploaded_image  
            FROM tbl_transaction A 
            INNER JOIN tbl_expense_details B ON B.transaction_id = A.transaction_id 
            INNER JOIN tbl_accounting_heads C ON C.id = B.account_head 
            WHERE A.is_active = 1 AND A.origin_id = $originId AND A.user_id = $userId AND A.transaction_type = 2 ";

        if ($fromDate != '' && $toDate != '') {
            $sqlQuery .= " AND STR_TO_DATE(B.expense_date, '%d/%m/%Y') BETWEEN STR_TO_DATE('$fromDate', '%d/%m/%Y') AND STR_TO_DATE('$toDate', '%d/%m/%Y')";
        }

        if ($conceptGeneral > 0) {
            $sqlQuery .= " AND A.credit_transaction_id = $conceptGeneral ";
        }

        if ($accountHead > 0) {
            $sqlQuery .= " AND B.account_head = $accountHead ";
        }

        $sqlQuery .= " ORDER BY STR_TO_DATE(B.expense_date, '%d/%m/%Y') ASC";

        $query = $this->db->query($sqlQuery);
        return $query->result();
    }
}