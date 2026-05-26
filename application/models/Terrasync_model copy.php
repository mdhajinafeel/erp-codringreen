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

    public function get_receptions_by_temp_ids(array $tempIds)
    {
        if (empty($tempIds)) {
            return [];
        }

        return $this->db
            ->where_in('temp_reception_id', $tempIds)
            ->get('tbl_reception')
            ->result();
    }

    // =========================
    // RECEPTION DATA
    // =========================
    public function reception_data_exists(string $tempReceptionDataId, string $tempReceptionId)
    {
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

    public function get_reception_data_by_temp_reception_id(string $tempReceptionId)
    {
        return $this->db
            ->where('temp_reception_id', $tempReceptionId)
            ->get('tbl_reception_data')
            ->result();
    }

    public function get_reception_data_by_temp_ids(array $tempIds)
    {
        if (empty($tempIds)) {
            return [];
        }

        return $this->db
            ->where_in(
                'temp_reception_data_id',
                $tempIds
            )
            ->get('tbl_reception_data')
            ->result();
    }

    public function get_reception_data_by_id(int $receptionDataId) {
        return $this->db
            ->where('reception_data_id', $receptionDataId)
            ->get('tbl_reception_data')
            ->row();
    }

    // =========================
    // DISPATCH
    // =========================
    public function dispatch_exists(string $tempDispatchId)
    {
        return $this->db
            ->where('temp_dispatch_id', $tempDispatchId)
            ->where('isactive', 1)
            ->get('tbl_dispatch_container')
            ->row();
    }

    public function add_dispatch(array $data): int
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_container', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_dispatch(int $dispatchId, string $tempDispatchId, array $data): bool
    {
        $multiClause = array(
            'dispatch_id' => $dispatchId,
            'temp_dispatch_id' => $tempDispatchId,
            'isactive' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_container', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_dispatches_by_temp_ids(array $tempIds)
    {
        if (empty($tempIds)) {
            return [];
        }

        return $this->db
            ->where_in('temp_dispatch_id', $tempIds)
            ->get('tbl_dispatch_container')
            ->result();
    }

    // =========================
    // CONTAINER DATA
    // =========================
    public function dispatch_data_exists(string $tempDispatchId, string $tempReceptionDataId, string $tempReceptionId)
    {
        return $this->db
            ->where('temp_dispatch_id', $tempDispatchId)
            ->where('temp_reception_data_id', $tempReceptionDataId)
            ->where('temp_reception_id', $tempReceptionId)
            ->where('isactive', 1)
            ->get('tbl_dispatch_data')
            ->row();
    }

    public function add_dispatch_data(array $data): int
    {
        $this->db->set('createddate', 'NOW()', FALSE);
        $this->db->set('updateddate', 'NOW()', FALSE);
        $this->db->insert('tbl_dispatch_data', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_dispatch_data(int $dispatchDataId, string $tempDispatchId, string $tempReceptionDataId, string $tempReceptionId, array $data): bool
    {
        $multiClause = array(
            'dispatch_data_id' => $dispatchDataId,
            'temp_dispatch_id' => $tempDispatchId,
            'temp_reception_data_id' => $tempReceptionDataId,
            'temp_reception_id' => $tempReceptionId,
            'isactive' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updateddate', 'NOW()', FALSE);
        if ($this->db->update('tbl_dispatch_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_dispatch_data_by_temp_ids(array $tempDispatchIds)
    {
        if (empty($tempDispatchIds)) {
            return [];
        }

        return $this->db
            ->where_in('temp_dispatch_id', $tempDispatchIds)
            ->get('tbl_dispatch_data')
            ->result();
    }

    public function recalculate_remaining_stock(string $tempReceptionId, string $tempReceptionDataId)
    {
        $sql = "
        UPDATE tbl_reception_data rd
        SET remaining_stock_count =
        (
            rd.scanned_code -
            IFNULL(
                (
                    SELECT SUM(dd.dispatch_pieces)
                    FROM tbl_dispatch_data dd
                    WHERE dd.temp_reception_id =
                        rd.temp_reception_id
                    AND dd.temp_reception_data_id =
                        rd.temp_reception_data_id
                    AND dd.isactive = 1
                ),
                0
            )
        )
        WHERE rd.temp_reception_id = ?
        AND rd.temp_reception_data_id = ?";

        return $this->db->query($sql, [$tempReceptionId, $tempReceptionDataId]);
    }

    public function update_reception_data_stock(string $tempReceptionDataId, string $tempReceptionId, string $receptionDataId, array $data)
    {
        $this->db->where('reception_data_id', $receptionDataId);
        $this->db->where('temp_reception_data_id', $tempReceptionDataId);
        $this->db->where('temp_reception_id', $tempReceptionId);

        if ($this->db->update('tbl_reception_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // =========================
    // FARM DETAILS
    // =========================
    public function farm_exists(string $tempFarmId)
    {
        return $this->db
            ->where('temp_farm_id', $tempFarmId)
            ->get('tbl_farm')
            ->row();
    }

    public function add_farm(array $data): int
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_farm', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_farm(int $farmId, string $tempFarmId, array $data): bool
    {
        $multiClause = array(
            'farm_id' => $farmId,
            'temp_farm_id' => $tempFarmId,
            'is_active' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_farm', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // =========================
    // FARM DATA
    // =========================
    public function farm_data_exists(int $farmId, string $tempReceptionDataId)
    {
        return $this->db
            ->where('farm_id', $farmId)
            ->where('temp_farm_data_id', $tempReceptionDataId)
            ->get('tbl_farm_data')
            ->row();
    }

    public function add_farm_data(array $data): int
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_farm_data', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function update_farm_data(int $farmDataId, string $tempFarmDataId, array $data): bool
    {
        $multiClause = array(
            'farm_data_id' => $farmDataId,
            'temp_farm_data_id' => $tempFarmDataId,
            'is_active' => 1
        );
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_farm_data', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_farm_data_by_farm_id_and_length(int $farmId, int $length)
    {
        if ($length == 1) {
            $query = $this->db->query("SELECT A.farm_data_id, A.farm_id, A.no_of_pieces, A.circumference, A.length, A.gross_volume, 
                A.volume, A.captured_timestamp FROM tbl_farm_data A 
                WHERE A.is_active = 1 AND A.farm_id = $farmId AND A.length < 330");
        } else if ($length == 2) {
            $query = $this->db->query("SELECT A.farm_data_id, A.farm_id, A.no_of_pieces, A.circumference, A.length, A.gross_volume, 
                A.volume, A.captured_timestamp FROM tbl_farm_data A 
                WHERE A.is_active = 1 AND A.farm_id = $farmId AND (A.length >= 330 && A.length < 600)");
        } else {
            $query = $this->db->query("SELECT A.farm_data_id, A.farm_id, A.no_of_pieces, A.circumference, A.length, A.gross_volume, 
                A.volume, A.captured_timestamp FROM tbl_farm_data A 
                WHERE A.is_active = 1 AND A.farm_id = $farmId AND A.length >= 600");
        }

        return $query->result();
    }

    // =========================
    // FARM CONTRACT MAPPING
    // =========================
    public function add_contract_inventory_mapping(array $data): int
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_contract_inventory_mapping', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function delete_contract_inventory_mapping(int $contractId, string $supplierId, string $inventoryOrder, array $data): bool
    {
        $multiClause = array(
            'contract_id' => $contractId,
            'supplier_id' => $supplierId,
            'inventory_order' => $inventoryOrder
        );
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_contract_inventory_mapping', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // =========================
    // INVENTORY LEDGER
    // =========================

    public function add_inventory_ledger(array $data): int
    {
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->set('updated_date', 'NOW()', FALSE);
        $this->db->insert('tbl_inventory_ledger', $data);
        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return 0;
        }
    }

    public function delete_inventory_ledger(string $inventoryOrder, int $contractId, array $data)
    {
        $multiClause = array('inventory_order' => $inventoryOrder, 'contract_id' => $contractId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_inventory_ledger', $data)) {
            return true;
        } else {
            return false;
        }
    }

    // =========================
    // INVENTORY SUPPLIER PRICE
    // =========================
    public function add_inventory_supplier_price(int $contractId, int $supplierId, string $inventoryOrder, int $userId)
    {
        $query = $this->db->query("SELECT minrange_grade1, maxrange_grade2, pricerange_grade3, pricerange_grade_semi, pricerange_grade_longs  
                    FROM tbl_supplier_contract_price WHERE is_active = 1
                    AND supplier_id = $contractId");
        $data = $query->result();

        foreach ($data as $r) {
            $dataPrice = array(
                "contract_id" => $contractId,
                "supplier_id" => $supplierId,
                "inventory_number" => $inventoryOrder,
                "minrange_grade1" => $r->minrange_grade1,
                "maxrange_grade2" => $r->maxrange_grade2,
                "pricerange_grade3" => $r->pricerange_grade3,
                "pricerange_grade_semi" => $r->pricerange_grade_semi,
                "pricerange_grade_longs" => $r->pricerange_grade_longs,
                "created_date" => date('Y-m-d H:i:s'),
                "created_by" => $userId,
                "updated_date" => date('Y-m-d H:i:s'),
                "updated_by" => $userId,
                "is_active" => 1
            );
            $this->db->insert('tbl_supplier_contract_inventory_price', $dataPrice);
        }
        return true;
    }

    public function delete_inventory_supplier_price(string $inventoryOrder, int $contractId, int $supplierId, array $data)
    {
        $multiClause = array('inventory_number' => $inventoryOrder, 'contract_id' => $contractId, 'supplier_id' => $supplierId);
        $this->db->where($multiClause);
        $this->db->set('updated_date', 'NOW()', FALSE);
        if ($this->db->update('tbl_supplier_contract_inventory_price', $data)) {
            return true;
        } else {
            return false;
        }
    }
}
