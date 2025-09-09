<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Costingpurchasers extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
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
		$data['title'] = $this->lang->line('costing_purchasers') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = 'cgr_costing_purchasers';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/costingpurchasers", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function supplier_list()
	{
		$data['title'] = $this->lang->line('costing_purchasers') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$supplier = $this->Master_model->all_costingpurchasers();
		} else {
			$supplier = $this->Master_model->all_costingpurchasers_origin($originid);
		}
		$data = array();

		foreach ($supplier as $r) {

			$editSupplier = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editsupplier" data-supplier_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

			if ($r->is_active == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editSupplier,
				$r->purchaser_name,
				$r->company_id,
				$r->costing_type,
				$status
			);
		}

		$output = array(
			"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dialog_supplier_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		$costingtypes = $this->Master_model->all_costing_types();

		if ($this->input->get('type') == "addsupplier") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_purchaser'),
					'pagetype' => "add",
					'supplierid' => 0,
					'costingtypes' => $costingtypes,
					'csrfhash' => $this->security->get_csrf_hash(),
				);
				$this->load->view('masters/dialog_add_costingpurchasers', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editsupplier") {

			if (!empty($session)) {

				$getSupplierDetails = $this->Master_model->get_costingpurchaser_detail_by_id($this->input->get('sid'));

				$getSupplierDetails[0]->costing_type = explode(',', $getSupplierDetails[0]->costing_type);

				$data = array(
					'pageheading' => $this->lang->line('edit_purchaser'),
					'pagetype' => "edit",
					'supplierid' => $getSupplierDetails[0]->id,
					'costingtypes' => $costingtypes,
					'get_supplier_details' => $getSupplierDetails,
					'csrfhash' => $this->security->get_csrf_hash(),
				);
				$this->load->view('masters/dialog_add_costingpurchasers', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function add()
	{
		$Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');

		if ($this->input->post('add_type') == 'suppliers') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('name');
					$supplierid = $this->input->post('supplierid');
					$status = $this->input->post('status');
					$supplier_origin = $this->input->post('supplier_origin');
					$costing_type = $this->input->post('export_type');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"purchaser_name" => $name,
						"company_id" => $supplierid,
						"costing_type" => $costing_type,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'],
						'is_active' => $status,
						'origin_id' => $supplier_origin,
					);

					$insertSupplier = $this->Master_model->add_costingpurchaser($dataSupplier);

					if ($insertSupplier > 0) {
						$Return['result'] = $this->lang->line('data_added');
						$this->output($Return);
						exit;
					} else {
						$Return['error'] = $this->lang->line('error_adding');
						$this->output($Return);
						exit;
					}
				} else if ($this->input->post('action_type') == 'edit') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$supplier_id = $this->input->post('supplier_id');
					$name = $this->input->post('name');
					$supplierid = $this->input->post('supplierid');
					$status = $this->input->post('status');
					$supplier_origin = $this->input->post('supplier_origin');
					$costing_type = $this->input->post('export_type');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"purchaser_name" => $name,
						"company_id" => $supplierid,
						"costing_type" => $costing_type,
						"updated_by" => $session['user_id'],
						'is_active' => $status,
						'origin_id' => $supplier_origin,
					);

					$updateSupplier = $this->Master_model->update_costingpurchaser($dataSupplier, $supplier_id);

					if ($updateSupplier == true) {

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

	public function generate_supplier_report()
	{
		try {

			$session = $this->session->userdata('fullname');

			$Return = array(
				'result' => '',
				'error' => '',
				'redirect' => false,
				'csrf_hash' => '',
				'successmessage' => ''
			);

			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$getSupplierDetailsReport = $this->Master_model->get_costingpurchaser_report($this->input->get("oid"));

				if (count($getSupplierDetailsReport) > 0) {
					$this->excel->setActiveSheetIndex(0);
					$objSheet = $this->excel->getActiveSheet();
					$objSheet->setTitle($this->lang->line('excel_supplier_title'));
					$objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

					$objSheet->SetCellValue('A1', $this->lang->line('s_no'));
					$objSheet->SetCellValue('B1', $this->lang->line('purchaser_name'));
					$objSheet->SetCellValue('C1', $this->lang->line('company_id'));
					$objSheet->SetCellValue('D1', $this->lang->line('costing_type'));
					$objSheet->SetCellValue('E1', $this->lang->line('status'));

					$objSheet->getStyle("A1:E1")->getFont()->setBold(true);
					$objSheet->setAutoFilter('A1:E1');
					$objSheet->getStyle("A1:E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objSheet->getColumnDimension('A')->setAutoSize(true);
					$objSheet->getColumnDimension('B')->setAutoSize(true);
					$objSheet->getColumnDimension('C')->setAutoSize(true);
					$objSheet->getColumnDimension('D')->setAutoSize(false);
					$objSheet->getColumnDimension('D')->setWidth(30);
					$objSheet->getColumnDimension('E')->setAutoSize(true);

					$objSheet->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');

					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

					$objSheet->getStyle('A1:E1')->applyFromArray($styleArray);

					$i = 1;
					$rowCountData = 2;

					foreach ($getSupplierDetailsReport as $supplier) {

						$objSheet->SetCellValue('A' . $rowCountData, $i);
						$objSheet->SetCellValue('B' . $rowCountData, $supplier->purchaser_name);
						$objSheet->SetCellValue('C' . $rowCountData, $supplier->company_id);
						$objSheet->SetCellValue('D' . $rowCountData, $supplier->costing_types);
						if ($supplier->is_active == 1) {
							$objSheet->SetCellValue('E' . $rowCountData, $this->lang->line('active'));
						} else {
							$objSheet->SetCellValue('E' . $rowCountData, $this->lang->line('inactive'));
						}

						$objSheet->getStyle('D' . $rowCountData . ':D' . $rowCountData)->getAlignment()->setWrapText(true);
						$objSheet->getStyle('A' . $rowCountData . ':E' . $rowCountData)->applyFromArray($styleArray);

						$i++;
						$rowCountData++;
					}

					$objSheet->getSheetView()->setZoomScale(95);

					unset($styleArray);
					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY"));

					$filename =  'CostingPurchaserReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename="' . $filename . '"');
					header('Cache-Control: max-age=0');

					$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
					$objWriter->save('./reports/SupplierReports/' . $filename);
					$Return['error'] = '';
					$Return['result'] = site_url() . 'reports/SupplierReports/' . $filename;
					$Return['successmessage'] = $this->lang->line('report_downloaded');
					if ($Return['result'] != '') {
						$this->output($Return);
					}
				} else {
					$Return['error'] = $this->lang->line('no_data_reports');
					$Return['result'] = "";
					$Return['redirect'] = false;
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
		} catch (Exception $e) {
			$Return['error'] = $this->lang->line('error_reports');
			$Return['result'] = "";
			$Return['redirect'] = false;
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
			exit;
		}
	}

	public function deletefilesfromfolder()
	{
		$files = glob(FCPATH . "reports/*.xlsx");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		$files = glob(FCPATH . "reports/SupplierReports/*.xlsx");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
}
