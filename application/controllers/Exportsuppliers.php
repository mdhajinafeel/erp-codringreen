<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Exportsuppliers extends MY_Controller
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
		$data['title'] = $this->lang->line('export_suppliers') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = 'cgr_exportsuppliers';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/exportsuppliers", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function supplier_list()
	{
		$data['title'] = $this->lang->line('export_suppliers') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$supplier = $this->Master_model->all_exportsuppliers();
		} else {
			$supplier = $this->Master_model->all_exportsuppliers_origin($originid);
		}
		$data = array();

		foreach ($supplier as $r) {

			$editSupplier = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editsupplier" data-supplier_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
			<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="view" data-supplier_id="' . $r->id . '"><span class="fas fa-eye"></span></button></span>';

			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$data[] = array(
				$editSupplier,
				$r->supplier_name,
				$r->supplier_id,
				$r->export_type,
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

		$exporttypes = $this->Master_model->all_export_types();

		if ($this->input->get('type') == "addsupplier") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_supplier'),
					'pagetype' => "add",
					'supplierid' => 0,
					'exporttypes' => $exporttypes,
					'csrfhash' => $this->security->get_csrf_hash(),
				);
				$this->load->view('masters/dialog_add_exportsupplier', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editsupplier") {

			if (!empty($session)) {

				$getSupplierDetails = $this->Master_model->get_exportsupplier_detail_by_id($this->input->get('sid'));

				$getSupplierDetails[0]->export_type = explode(',', $getSupplierDetails[0]->export_type);

				$data = array(
					'pageheading' => $this->lang->line('edit_supplier'),
					'pagetype' => "edit",
					'supplierid' => $getSupplierDetails[0]->id,
					'exporttypes' => $exporttypes,
					'get_supplier_details' => $getSupplierDetails,
					'csrfhash' => $this->security->get_csrf_hash(),
				);
				$this->load->view('masters/dialog_add_exportsupplier', $data);
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
					$export_type = $this->input->post('export_type');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"supplier_name" => $name, "supplier_id" => $supplierid,
						"export_type" => $export_type, "created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $supplier_origin,
					);

					$insertSupplier = $this->Master_model->add_exportsupplier($dataSupplier);

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
					$export_type = $this->input->post('export_type');

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"supplier_name" => $name, "supplier_id" => $supplierid,
						"export_type" => $export_type, 
						"updated_by" => $session['user_id'], 'is_active' => $status,
						'origin_id' => $supplier_origin,
					);

					$updateSupplier = $this->Master_model->update_exportsupplier($dataSupplier, $supplier_id, $supplier_origin);

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

	public function dialog_supplier_view()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "viewsupplier") {
			if (!empty($session)) {

				$getSupplierDetails = $this->Master_model->get_exportsupplier_detail_by_id($this->input->get('sid'));

				if ($getSupplierDetails[0]->is_active == 1) {
					$status = $this->lang->line('active');
				} else {
					$status = $this->lang->line('inactive');
				}

				$data = array(
					'pageheading' => $this->lang->line('supplier_details'),
					'supplierid' => $this->input->get('sid'),
					'supplier_name' => $getSupplierDetails[0]->supplier_name,
					'supplier_id' => $getSupplierDetails[0]->supplier_id,
					'export_types' => $getSupplierDetails[0]->export_types,
					'status' => $status,
					'origin' => $getSupplierDetails[0]->origin,
				);
				$this->load->view('masters/dialog_view_exportsupplier', $data);
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

	public function generate_supplier_report()
	{
		try {

			$session = $this->session->userdata('fullname');

			$Return = array(
				'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
				'successmessage' => ''
			);

			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$getSupplierDetailsReport = $this->Master_model->get_supplier_details_report($session["applicable_origins_id"]);

				if (count($getSupplierDetailsReport) > 0) {
					$this->excel->setActiveSheetIndex(0);
					$objSheet = $this->excel->getActiveSheet();
					$objSheet->setTitle($this->lang->line('excel_supplier_title'));
					$objSheet->getParent()->getDefaultStyle()
						->getFont()
						->setName('Calibri')
						->setSize(11);

					$objSheet->SetCellValue('A1', $this->lang->line('s_no'));
					$objSheet->SetCellValue('B1', $this->lang->line('supplier_name'));
					$objSheet->SetCellValue('C1', $this->lang->line('supplier_code'));
					$objSheet->SetCellValue('D1', $this->lang->line('supplier_id'));
					$objSheet->SetCellValue('E1', $this->lang->line('company_name'));
					$objSheet->SetCellValue('F1', $this->lang->line('company_id'));
					$objSheet->SetCellValue('G1', $this->lang->line('address'));
					$objSheet->SetCellValue('H1', $this->lang->line('roles'));
					$objSheet->SetCellValue('I1', $this->lang->line('wood_details'));
					$objSheet->SetCellValue('J1', $this->lang->line('bank_detail'));
					$objSheet->SetCellValue('K1', $this->lang->line('supplier_taxes'));
					$objSheet->SetCellValue('L1', $this->lang->line('provider_taxes'));
					$objSheet->SetCellValue('M1', $this->lang->line('origin'));
					$objSheet->SetCellValue('N1', $this->lang->line('status'));

					$objSheet->getStyle("A1:N1")
						->getFont()
						->setBold(true);

					$objSheet->setAutoFilter('A1:N1');

					// HEADER ALIGNMENT
					$objSheet->getStyle("A1:N1")
						->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objSheet->getColumnDimension('A')->setAutoSize(true);
					$objSheet->getColumnDimension('B')->setAutoSize(true);
					$objSheet->getColumnDimension('C')->setAutoSize(true);
					$objSheet->getColumnDimension('D')->setAutoSize(true);
					$objSheet->getColumnDimension('E')->setAutoSize(true);
					$objSheet->getColumnDimension('F')->setAutoSize(true);
					$objSheet->getColumnDimension('G')->setAutoSize(false);
					$objSheet->getColumnDimension('G')->setWidth(30);
					$objSheet->getColumnDimension('H')->setAutoSize(true);
					$objSheet->getColumnDimension('I')->setAutoSize(true);
					$objSheet->getColumnDimension('J')->setAutoSize(true);
					$objSheet->getColumnDimension('K')->setAutoSize(true);
					$objSheet->getColumnDimension('L')->setAutoSize(true);
					$objSheet->getColumnDimension('M')->setAutoSize(true);
					$objSheet->getColumnDimension('N')->setAutoSize(true);

					$objSheet->getStyle('A1:N1')
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('add8e6');

					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

					$objSheet->getStyle('A1:N1')->applyFromArray($styleArray);

					$i = 1;
					$rowCountData = 2;

					foreach ($getSupplierDetailsReport as $supplier) {

						$supplierTaxes = $this->Master_model->get_supplier_taxes_report($supplier->id);
						$providerTaxes = $this->Master_model->get_provider_taxes_report($supplier->id);

						$objSheet->SetCellValue('A' . $rowCountData, $i);
						$objSheet->SetCellValue('B' . $rowCountData, $supplier->supplier_name);
						$objSheet->SetCellValue('C' . $rowCountData, $supplier->supplier_code);

						$objSheet->setCellValueExplicit(
							'D' . $rowCountData,
							$supplier->supplier_id,
							PHPExcel_Cell_DataType::TYPE_STRING
						);

						$objSheet->SetCellValue('E' . $rowCountData, $supplier->company_name);

						$objSheet->setCellValueExplicit(
							'F' . $rowCountData,
							$supplier->company_id,
							PHPExcel_Cell_DataType::TYPE_STRING
						);

						$objSheet->SetCellValue('G' . $rowCountData, $supplier->supplier_address);
						$objSheet->SetCellValue('H' . $rowCountData, $supplier->roles);
						$objSheet->SetCellValue('I' . $rowCountData, $supplier->products);
						$objSheet->SetCellValue('J' . $rowCountData, $supplier->bankdetails);
						$objSheet->SetCellValue('K' . $rowCountData, $supplierTaxes[0]->supplier_taxes);
						$objSheet->SetCellValue('L' . $rowCountData, $providerTaxes[0]->provider_taxes);
						$objSheet->SetCellValue('M' . $rowCountData, $supplier->origin);

						if ($supplier->isactive == 1) {
							$objSheet->SetCellValue('N' . $rowCountData, $this->lang->line('active'));
						} else {
							$objSheet->SetCellValue('N' . $rowCountData, $this->lang->line('inactive'));
						}

						$objSheet->getStyle('G' . $rowCountData . ':L' . $rowCountData)->getAlignment()->setWrapText(true);
						$objSheet->getStyle('A' . $rowCountData . ':N' . $rowCountData)->applyFromArray($styleArray);

						$i++;
						$rowCountData++;
					}

					$objSheet->getSheetView()->setZoomScale(95);

					unset($styleArray);
					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY"));

					$filename =  'SupplierReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

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
