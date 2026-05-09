<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Terrasync_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // =========================
    // RECEPTION
    // =========================
    public function reception_exists(string $tempReceptionId)
    {
        return $this->db
            ->where('temp_reception_id', $tempReceptionId)
            ->where('isactive', 1)
            ->get('tbl_reception')
            ->row();
    }

    public function add_reception(array $data): int
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_reception', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_reception(int $receptionId, string $tempReceptionId, array $data): bool
    {
        $multiClause = array(
            'reception_id' => $receptionId,
            'temp_reception_id' => $tempReceptionId,
            'isactive' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // =========================
    // RECEPTION DATA
    // =========================
    public function reception_data_exists(string $tempReceptionDataId, string $tempReceptionId) {
        return $this->db
            ->where('temp_reception_data_id', $tempReceptionDataId)
            ->where('temp_reception_id', $tempReceptionId)
            ->where('isactive', 1)
            ->get('tbl_reception_data')
            ->row();
    }

    public function add_reception_data(array $data): int
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_reception_data', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_reception_data(int $receptionDataId, string $tempReceptionDataId, string $receptionContainerMappingId, array $data): bool
    {
        $multiClause = array(
            'reception_data_id' => $receptionDataId,
            'temp_reception_data_id' => $tempReceptionDataId,
            'reception_container_mapping_id' => $receptionContainerMappingId,
            'reception_id' => $data['reception_id'],
            'temp_reception_id' => $data['temp_reception_id'],
            'isactive' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }
}