<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Forestryreports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_model");
        $this->load->model("Master_model");
        $this->load->model("Settings_model");
        $this->load->model("Forestry_model");
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
        $data['title'] = $this->lang->line('forestry_report') . " - " . $this->lang->line('forestry_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (empty($session)) {
            redirect("/logout");
        }

        $data['path_url'] = 'cgr_forestry';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("forestry/forestry_report", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function dialog_report_action()
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "downloadreport") {

                $this->deletefilesfromfolder();

                $forestryReportData = $this->Forestry_model->get_forestry_reports_farm_data(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate')
                );

                $extractionReportData = $this->Forestry_model->get_forestry_extraction_report_data(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate')
                );

                $machineMaintainanceReportData = $this->Forestry_model->get_forestry_operation_cost_report_data_15days(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    5
                );

                $machineRentalReportData = $this->Forestry_model->get_forestry_operation_cost_report_data_15days(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    7
                );

                $manualLabourReportData = $this->Forestry_model->get_forestry_operation_cost_report_data_15days(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    8
                );

                $acpmData = $this->Forestry_model->get_forestry_operation_report_data(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    4
                );

                $lubricantsData = $this->Forestry_model->get_forestry_operation_report_data(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    9
                );

                $othersData = $this->Forestry_model->get_forestry_operation_report_data(
                    $this->input->get('oid'),
                    $this->input->get('sid'),
                    $this->input->get('cid'),
                    $this->input->get('fromdate'),
                    $this->input->get('todate'),
                    6
                );

                if (count($forestryReportData) == 0) {
                    $Return['error'] = $this->lang->line('no_data_available');
                    $this->output($Return);
                } else {

                    $reportSheetName = $this->lang->line('report');
                    $extractionSheetName = $this->lang->line('extraction_cost');
                    $machineMaintainanceSheetName = $this->lang->line('machine_maintenance_cost');
                    $machineRentalSheetName = $this->lang->line('machine_rental_cost');
                    $manualLabourSheetName = $this->lang->line('manual_labour_cost');
                    $acpmSheetName = $this->lang->line('acpm_cost');
                    $lubricantsSheetName = $this->lang->line('lubricants_cost');
                    $othersSheetName = $this->lang->line('miscellaneous');

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('report'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $objSheet->SetCellValue('A4', strtoupper($this->lang->line('FECHA')));
                    $objSheet->SetCellValue('B4', strtoupper($this->lang->line('ica_number')));
                    $objSheet->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                    $objSheet->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                    $objSheet->SetCellValue('E4', strtoupper($this->lang->line('description')));
                    $objSheet->SetCellValue('F4', strtoupper($this->lang->line('pieces')));
                    $objSheet->SetCellValue('G4', strtoupper($this->lang->line('text_volume')));
                    $objSheet->SetCellValue('H4', strtoupper($this->lang->line('extraction')));
                    $objSheet->SetCellValue('I4', strtoupper($this->lang->line('wood_value')));
                    $objSheet->SetCellValue('J4', strtoupper($this->lang->line('transport')));
                    $objSheet->SetCellValue('K4', strtoupper($this->lang->line('zona')));
                    $objSheet->SetCellValue('L4', strtoupper($this->lang->line('loading')));
                    $objSheet->SetCellValue('M4', strtoupper($this->lang->line('maintenance')));
                    $objSheet->SetCellValue('N4', strtoupper($this->lang->line('machine_rental')));
                    $objSheet->SetCellValue('O4', strtoupper($this->lang->line('manual_labours')));
                    $objSheet->SetCellValue('P4', strtoupper($this->lang->line('acpm')));
                    $objSheet->SetCellValue('Q4', strtoupper($this->lang->line('lubricants')));
                    $objSheet->SetCellValue('R4', strtoupper($this->lang->line('miscellaneous')));
                    $objSheet->SetCellValue('S4', strtoupper($this->lang->line('total_value')));

                    $objSheet->getStyle("A4:S4")->getFont()->setBold(true);
                    $objSheet->getStyle("A4:S4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(40);
                    $objSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                    $objSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(30);
                    $objSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('I')->setAutoSize(false)->setWidth(25);
                    $objSheet->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('K')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('L')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('M')->setAutoSize(false)->setWidth(25);
                    $objSheet->getColumnDimension('N')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('O')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('P')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('Q')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('R')->setAutoSize(false)->setWidth(20);
                    $objSheet->getColumnDimension('S')->setAutoSize(false)->setWidth(20);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A4:S4')->applyFromArray($styleArray);

                    $row = 5;
                    foreach ($forestryReportData as $rdata) {

                        $extractionFormula = '=IFERROR( INDEX(\'' . $extractionSheetName . '\'!$L$5:$L$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $extractionSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $extractionSheetName . '\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $machineMaintainanceFormula = '=IFERROR( INDEX(\'' . $machineMaintainanceSheetName . '\'!$J$5:$J$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $machineMaintainanceSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $machineMaintainanceSheetName . '\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $machineRentalFormula = '=IFERROR( INDEX(\'' . $machineRentalSheetName . '\'!$J$5:$J$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $machineRentalSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $machineRentalSheetName . '\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $manualLabourFormula = '=IFERROR( INDEX(\'' . $manualLabourSheetName . '\'!$J$5:$J$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $manualLabourSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $manualLabourSheetName . '\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $lubricantsFormula = '=IFERROR( INDEX(\'' . $lubricantsSheetName . '\'!$L$5:$L$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $lubricantsSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $lubricantsSheetName .'\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $acpmFormula = '=IFERROR( INDEX(\'' . $acpmSheetName . '\'!$L$5:$L$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $acpmSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $acpmSheetName .'\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';
                        $othersFormula = '=IFERROR( INDEX(\'' . $othersSheetName . '\'!$L$5:$L$10000, MATCH(1, INDEX( (A' . $row . '>=\'' . $othersSheetName . '\'!$A$5:$A$10000) * (A' . $row . '<=\'' . $othersSheetName .'\'!$B$5:$B$10000), 0), 0) ) * G' . $row . ', 0)';

                        $dateObj = DateTime::createFromFormat('d/m/Y', trim($rdata->purchase_date));

                        if ($dateObj !== false) {
                            $dateObj->setTime(0, 0, 0);

                            // FLOOR removes any decimal time fraction
                            $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                            $objSheet->setCellValue('A' . $row, $excelDate);
                        }

                        $objSheet->SetCellValue('B' . $row, $rdata->inventory_order);
                        $objSheet->SetCellValue('C' . $row, $rdata->supplier_name);
                        $objSheet->SetCellValue('D' . $row, $rdata->contract_code);
                        $objSheet->SetCellValue('E' . $row, $rdata->description);
                        $objSheet->SetCellValue('F' . $row, $rdata->pieces + 0);
                        $objSheet->SetCellValue('G' . $row, $rdata->net_volume + 0);
                        $objSheet->SetCellValue('H' . $row, $extractionFormula);
                        $objSheet->SetCellValue('I' . $row, $rdata->wood_value + 0);
                        $objSheet->SetCellValue('J' . $row, $rdata->logistic_cost + 0);
                        $objSheet->SetCellValue('K' . $row, $rdata->service_cost + 0);
                        $objSheet->SetCellValue('L' . $row, $rdata->loading_cost + 0);
                        $objSheet->SetCellValue('M' . $row, $machineMaintainanceFormula);
                        $objSheet->SetCellValue('N' . $row, $machineRentalFormula);
                        $objSheet->SetCellValue('O' . $row, $manualLabourFormula);
                        $objSheet->SetCellValue('P' . $row, $acpmFormula);
                        $objSheet->SetCellValue('Q' . $row, $lubricantsFormula);
                        $objSheet->SetCellValue('R' . $row, $othersFormula);
                        $objSheet->SetCellValue('S' . $row, "=SUM(H$row:R$row)");
                        $row++;
                    }

                    $lastRow = $row - 1;

                    $objSheet->SetCellValue('F3', "=SUBTOTAL(9,F5:F$lastRow)");
                    $objSheet->getStyle('F3')->applyFromArray($styleArray);
                    $objSheet->getStyle("F3")->getFont()->setBold(true);
                    $objSheet->getStyle("F3")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                    $objSheet->SetCellValue('G3', "=SUBTOTAL(9,G5:G$lastRow)");
                    $objSheet->getStyle('G3')->applyFromArray($styleArray);
                    $objSheet->getStyle("G3")->getFont()->setBold(true);
                    $objSheet->getStyle("G3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $objSheet->SetCellValue('I3', "=SUBTOTAL(9,I5:I$lastRow)");
                    $objSheet->getStyle('I3')->applyFromArray($styleArray);
                    $objSheet->getStyle("I3")->getFont()->setBold(true);
                    $objSheet->getStyle("I3")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                    $objSheet->getStyle("A5:S$lastRow")->applyFromArray($styleArray);
                    $objSheet->getStyle("F5:F$lastRow")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("G5:G$lastRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("H5:S$lastRow")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                    // 🔥 FORCE date-only format for entire column
                    $objSheet->getStyle("A5:A$lastRow")
                        ->getNumberFormat()
                        ->setFormatCode('dd/mm/yyyy');

                    // 🔥 FORCE re-write values (this breaks cached datetime format)
                    for ($r = 5; $r <= $lastRow; $r++) {
                        $objSheet->setCellValue('A' . $r, (int)$objSheet->getCell('A' . $r)->getValue());
                    }

                    // Freeze pane
                    $objSheet->freezePane('A5');

                    // Auto filter
                    $objSheet->setAutoFilter("A4:S$lastRow");

                    // Set zoom scale
                    $objSheet->getSheetView()->setZoomScale(95);

                    // Extraction Cost
                    if (count($extractionReportData) > 0) {
                        $objSheet2 = $this->excel->createSheet();
                        $objSheet2->setTitle($this->lang->line('extraction_cost'));
                        $objSheet2->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet2->SetCellValue('A4', strtoupper($this->lang->line('from_date')));
                        $objSheet2->SetCellValue('B4', strtoupper($this->lang->line('to_date')));
                        $objSheet2->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet2->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet2->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet2->SetCellValue('F4', strtoupper($this->lang->line('total_trees')));
                        $objSheet2->SetCellValue('G4', strtoupper($this->lang->line('pieces')));
                        $objSheet2->SetCellValue('H4', strtoupper($this->lang->line('text_volume')));
                        $objSheet2->SetCellValue('I4', strtoupper($this->lang->line('extraction_cost')));
                        $objSheet2->SetCellValue('J4', strtoupper($this->lang->line('value_tree')));
                        $objSheet2->SetCellValue('K4', strtoupper($this->lang->line('value_piece')));
                        $objSheet2->SetCellValue('L4', strtoupper($this->lang->line('value_volume')));

                        $objSheet2->getStyle("A4:L4")->getFont()->setBold(true);
                        $objSheet2->getStyle("A4:L4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet2->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet2->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet2->getColumnDimension('E')->setAutoSize(false)->setWidth(25);
                        $objSheet2->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('I')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('K')->setAutoSize(false)->setWidth(20);
                        $objSheet2->getColumnDimension('L')->setAutoSize(false)->setWidth(20);

                        $objSheet2->getStyle('A4:L4')->applyFromArray($styleArray);

                        $row2 = 5;
                        foreach ($extractionReportData as $edata) {

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($edata->extraction_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet2->setCellValue('A' . $row2, $excelDate);
                            }

                            $objSheet2->setCellValue('B' . $row2, '=IF(A' . ($row2 + 1) . '="", A' . $row2 . ', A' . ($row2 + 1) . '-1)');
                            $objSheet2->getStyle('B' . $row2)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                            $objSheet2->SetCellValue('C' . $row2, $edata->supplier_name);
                            $objSheet2->SetCellValue('D' . $row2, $edata->contract_code);
                            $objSheet2->SetCellValue('E' . $row2, $edata->description);
                            $objSheet2->SetCellValue('F' . $row2, $edata->total_trees + 0);
                            $objSheet2->SetCellValue('G' . $row2, $edata->tota_pieces + 0);
                            $objSheet2->SetCellValue('H' . $row2, $edata->total_volume + 0);
                            $objSheet2->SetCellValue('I' . $row2, $edata->extraction_cost + 0);
                            $objSheet2->SetCellValue('J' . $row2, "=I$row2/F$row2");
                            $objSheet2->SetCellValue('K' . $row2, "=I$row2/G$row2");
                            $objSheet2->SetCellValue('L' . $row2, "=I$row2/H$row2");
                            $row2++;
                        }

                        $lastRow2 = $row2 - 1;

                        $objSheet2->SetCellValue('G3', "=SUBTOTAL(9,G5:G$lastRow)");
                        $objSheet2->getStyle('G3')->applyFromArray($styleArray);
                        $objSheet2->getStyle("G3")->getFont()->setBold(true);
                        $objSheet2->getStyle("G3")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

                        $objSheet2->SetCellValue('H3', "=SUBTOTAL(9,H5:H$lastRow)");
                        $objSheet2->getStyle('H3')->applyFromArray($styleArray);
                        $objSheet2->getStyle("H3")->getFont()->setBold(true);
                        $objSheet2->getStyle("H3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSheet2->getStyle("A5:L$lastRow2")->applyFromArray($styleArray);
                        $objSheet2->getStyle("F5:G$lastRow2")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet2->getStyle("H5:H$lastRow2")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet2->getStyle("I5:L$lastRow2")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet2->getStyle("A5:A$lastRow2")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow2; $r++) {
                            $objSheet2->setCellValue('A' . $r, (int)$objSheet2->getCell('A' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet2->freezePane('A5');
                        // Auto filter
                        $objSheet2->setAutoFilter("A4:L$lastRow2");
                        // Set zoom scale
                        $objSheet2->getSheetView()->setZoomScale(95);
                    }

                    // Machine Maintainance Cost
                    if (count($machineMaintainanceReportData) > 0) {
                        $objSheet3 = $this->excel->createSheet();
                        $objSheet3->setTitle($this->lang->line('machine_maintenance_cost'));
                        $objSheet3->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet3->SetCellValue('A4', strtoupper($this->lang->line('start_date')));
                        $objSheet3->SetCellValue('B4', strtoupper($this->lang->line('end_date')));
                        $objSheet3->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet3->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet3->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet3->SetCellValue('F4', strtoupper($this->lang->line('pieces')));
                        $objSheet3->SetCellValue('G4', strtoupper($this->lang->line('text_volume')));
                        $objSheet3->SetCellValue('H4', strtoupper($this->lang->line('amount')));
                        $objSheet3->SetCellValue('I4', strtoupper($this->lang->line('value_piece')));
                        $objSheet3->SetCellValue('J4', strtoupper($this->lang->line('value_volume')));

                        $objSheet3->getStyle("A4:J4")->getFont()->setBold(true);
                        $objSheet3->getStyle("A4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet3->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet3->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet3->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet3->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet3->getColumnDimension('E')->setAutoSize(false)->setWidth(25);
                        $objSheet3->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
                        $objSheet3->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet3->getColumnDimension('H')->setAutoSize(false)->setWidth(25);
                        $objSheet3->getColumnDimension('I')->setAutoSize(false)->setWidth(25);
                        $objSheet3->getColumnDimension('J')->setAutoSize(false)->setWidth(25);

                        $objSheet3->getStyle('A4:J4')->applyFromArray($styleArray);

                        $row3 = 5;
                        foreach ($machineMaintainanceReportData as $mdata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row3 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row3 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row3 
                                . ')';

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($mdata->start_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet3->setCellValue('A' . $row3, $excelDate);
                            }

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($mdata->end_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet3->setCellValue('B' . $row3, $excelDate);
                            }

                            $objSheet3->SetCellValue('C' . $row3, $mdata->supplier_name);
                            $objSheet3->SetCellValue('D' . $row3, $mdata->contract_code);
                            $objSheet3->SetCellValue('E' . $row3, $mdata->description);
                            $objSheet3->SetCellValue('F' . $row3, $piecesFormula);
                            $objSheet3->setCellValue('G' . $row3, $volumeFormula);
                            $objSheet3->SetCellValue('H' . $row3, $mdata->total_amount + 0);
                            $objSheet3->SetCellValue('I' . $row3, "=IF(F$row3=0, 0, H$row3/F$row3)");
                            $objSheet3->SetCellValue('J' . $row3, "=IF(G$row3=0, 0, H$row3/G$row3)");
                            $row3++;
                        }

                        $lastRow3 = $row3 - 1;
                        $objSheet3->getStyle("A5:J$lastRow3")->applyFromArray($styleArray);
                        $objSheet3->getStyle("F5:F$lastRow3")->getNumberFormat()->setFormatCode('_ * #,##0_ ;_ * -#,##0_ ;_ * "-"_ ;_ @_ ');
                        $objSheet3->getStyle("G5:G$lastRow3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet3->getStyle("H5:J$lastRow3")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet3->getStyle("A5:B$lastRow3")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow3; $r++) {
                            $objSheet3->setCellValue('A' . $r, (int)$objSheet3->getCell('A' . $r)->getValue());
                            $objSheet3->setCellValue('B' . $r, (int)$objSheet3->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet3->freezePane('A5');
                        // Auto filter
                        $objSheet3->setAutoFilter("A4:J$lastRow3");
                        // Set zoom scale
                        $objSheet3->getSheetView()->setZoomScale(95);
                    }

                    // Machine Rental Cost
                    if(count($machineRentalReportData) > 0) {
                        $objSheet4 = $this->excel->createSheet();
                        $objSheet4->setTitle($this->lang->line('machine_rental_cost'));
                        $objSheet4->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet4->SetCellValue('A4', strtoupper($this->lang->line('start_date')));
                        $objSheet4->SetCellValue('B4', strtoupper($this->lang->line('end_date')));
                        $objSheet4->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet4->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet4->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet4->SetCellValue('F4', strtoupper($this->lang->line('pieces')));
                        $objSheet4->SetCellValue('G4', strtoupper($this->lang->line('text_volume')));
                        $objSheet4->SetCellValue('H4', strtoupper($this->lang->line('amount')));
                        $objSheet4->SetCellValue('I4', strtoupper($this->lang->line('value_piece')));
                        $objSheet4->SetCellValue('J4', strtoupper($this->lang->line('value_volume')));

                        $objSheet4->getStyle("A4:J4")->getFont()->setBold(true);
                        $objSheet4->getStyle("A4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet4->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet4->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet4->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet4->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet4->getColumnDimension('E')->setAutoSize(false)->setWidth(25);
                        $objSheet4->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
                        $objSheet4->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet4->getColumnDimension('H')->setAutoSize(false)->setWidth(25);
                        $objSheet4->getColumnDimension('I')->setAutoSize(false)->setWidth(25);
                        $objSheet4->getColumnDimension('J')->setAutoSize(false)->setWidth(25);

                        $objSheet4->getStyle('A4:J4')->applyFromArray($styleArray);

                        $row4 = 5;
                        foreach ($machineRentalReportData as $rdata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row4 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row4 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row4 
                                . ')';

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($rdata->start_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet4->setCellValue('A' . $row4, $excelDate);
                            }

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($rdata->end_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet4->setCellValue('B' . $row4, $excelDate);
                            }

                            $objSheet4->SetCellValue('C' . $row4, $rdata->supplier_name);
                            $objSheet4->SetCellValue('D' . $row4, $rdata->contract_code);
                            $objSheet4->SetCellValue('E' . $row4, $rdata->description);
                            $objSheet4->SetCellValue('F' . $row4, $piecesFormula);
                            $objSheet4->SetCellValue('G' . $row4, $volumeFormula);
                            $objSheet4->SetCellValue('H' . $row4, $rdata->total_amount + 0);
                            $objSheet4->SetCellValue('I' . $row4, "=IF(F$row4=0, 0, H$row4/F$row4)");
                            $objSheet4->SetCellValue('J' . $row4, "=IF(G$row4=0, 0, H$row4/G$row4)");
                            $row4++;
                        }

                        $lastRow4 = $row4 - 1;
                        $objSheet4->getStyle("A5:J$lastRow4")->applyFromArray($styleArray);
                        $objSheet4->getStyle("F5:F$lastRow4")->getNumberFormat()->setFormatCode('_ * #,##0_ ;_ * -#,##0_ ;_ * "-"_ ;_ @_ ');
                        $objSheet4->getStyle("G5:G$lastRow4")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet4->getStyle("H5:J$lastRow4")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet4->getStyle("A5:B$lastRow4")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow4; $r++) {
                            $objSheet4->setCellValue('A' . $r, (int)$objSheet4->getCell('A' . $r)->getValue());
                            $objSheet4->setCellValue('B' . $r, (int)$objSheet4->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet4->freezePane('A5');
                        // Auto filter
                        $objSheet4->setAutoFilter("A4:J$lastRow4");
                        // Set zoom scale
                        $objSheet4->getSheetView()->setZoomScale(95);
                    }

                    // Manual Labour Cost
                    if (count($manualLabourReportData) > 0) {
                        $objSheet5 = $this->excel->createSheet();
                        $objSheet5->setTitle($this->lang->line('manual_labour_cost'));
                        $objSheet5->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet5->SetCellValue('A4', strtoupper($this->lang->line('start_date')));
                        $objSheet5->SetCellValue('B4', strtoupper($this->lang->line('end_date')));
                        $objSheet5->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet5->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet5->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet5->SetCellValue('F4', strtoupper($this->lang->line('pieces')));
                        $objSheet5->SetCellValue('G4', strtoupper($this->lang->line('text_volume')));
                        $objSheet5->SetCellValue('H4', strtoupper($this->lang->line('amount')));
                        $objSheet5->SetCellValue('I4', strtoupper($this->lang->line('value_piece')));
                        $objSheet5->SetCellValue('J4', strtoupper($this->lang->line('value_volume')));

                        $objSheet5->getStyle("A4:J4")->getFont()->setBold(true);
                        $objSheet5->getStyle("A4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet5->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet5->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet5->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet5->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet5->getColumnDimension('E')->setAutoSize(false)->setWidth(25);
                        $objSheet5->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
                        $objSheet5->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet5->getColumnDimension('H')->setAutoSize(false)->setWidth(25);
                        $objSheet5->getColumnDimension('I')->setAutoSize(false)->setWidth(25);
                        $objSheet5->getColumnDimension('J')->setAutoSize(false)->setWidth(25);

                        $objSheet5->getStyle('A4:J4')->applyFromArray($styleArray);

                        $row5 = 5;
                        foreach ($manualLabourReportData as $ldata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row5 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row5 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row5 
                                . ')';
                            
                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($ldata->start_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet5->setCellValue('A' . $row5, $excelDate);
                            }

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($ldata->end_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet5->setCellValue('B' . $row5, $excelDate);
                            }

                            $objSheet5->SetCellValue('C' . $row5, $ldata->supplier_name);
                            $objSheet5->SetCellValue('D' . $row5, $ldata->contract_code);
                            $objSheet5->SetCellValue('E' . $row5, $ldata->description);
                            $objSheet5->SetCellValue('F' . $row5, $piecesFormula);
                            $objSheet5->SetCellValue('G' . $row5, $volumeFormula);
                            $objSheet5->SetCellValue('H' . $row5, $ldata->total_amount + 0);
                            $objSheet5->SetCellValue('I' . $row5, "=IF(F$row5=0, 0, H$row5/F$row5)");
                            $objSheet5->SetCellValue('J' . $row5, "=IF(G$row5=0, 0, H$row5/G$row5)");

                            $row5++;
                        }

                        $lastRow5 = $row5 - 1;
                        $objSheet5->getStyle("A5:J$lastRow5")->applyFromArray($styleArray);
                        $objSheet5->getStyle("F5:F$lastRow5")->getNumberFormat()->setFormatCode('_ * #,##0_ ;_ * -#,##0_ ;_ * "-"_ ;_ @_ ');
                        $objSheet5->getStyle("G5:G$lastRow5")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet5->getStyle("H5:J$lastRow5")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet5->getStyle("A5:B$lastRow5")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow5; $r++) {
                            $objSheet5->setCellValue('A' . $r, (int)$objSheet5->getCell('A' . $r)->getValue());
                            $objSheet5->setCellValue('B' . $r, (int)$objSheet5->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet5->freezePane('A5');
                        // Auto filter
                        $objSheet5->setAutoFilter("A4:J$lastRow5");
                        // Set zoom scale
                        $objSheet5->getSheetView()->setZoomScale(95);
                    }

                    // ACPM Cost
                    if(count($acpmData) > 0) {
                        $objSheet6 = $this->excel->createSheet();
                        $objSheet6->setTitle($this->lang->line('acpm_cost'));
                        $objSheet6->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet6->SetCellValue('A4', strtoupper($this->lang->line('from_date')));
                        $objSheet6->SetCellValue('B4', strtoupper($this->lang->line('to_date')));
                        $objSheet6->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet6->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet6->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet6->SetCellValue('F4', strtoupper($this->lang->line('quantity')));
                        $objSheet6->SetCellValue('G4', strtoupper($this->lang->line('pieces')));
                        $objSheet6->SetCellValue('H4', strtoupper($this->lang->line('text_volume')));
                        $objSheet6->SetCellValue('I4', strtoupper($this->lang->line('total_cost')));
                        $objSheet6->SetCellValue('J4', strtoupper($this->lang->line('total_ica')));
                        $objSheet6->SetCellValue('K4', strtoupper($this->lang->line('value_piece')));
                        $objSheet6->SetCellValue('L4', strtoupper($this->lang->line('value_volume')));
                        $objSheet6->SetCellValue('M4', strtoupper($this->lang->line('value_ica')));

                        $objSheet6->getStyle("A4:M4")->getFont()->setBold(true);
                        $objSheet6->getStyle("A4:M4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet6->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet6->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet6->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('F')->setAutoSize(false)->setWidth(18);
                        $objSheet6->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('I')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('K')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('L')->setAutoSize(false)->setWidth(20);
                        $objSheet6->getColumnDimension('M')->setAutoSize(false)->setWidth(20);

                        $objSheet6->getStyle('A4:M4')->applyFromArray($styleArray);

                        $row6 = 5;
                        foreach ($acpmData as $adata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row6 . ',' 
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row6 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row6 . ',' 
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row6 
                                . ')';

                            $icasFormula = '=COUNTIFS('
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row6 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row6 
                                . ')';

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($adata->expense_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet6->setCellValue('A' . $row6, $excelDate);
                            }

                            //$dateObj = DateTime::createFromFormat('d/m/Y', trim($adata->end_date));

                            // if ($dateObj !== false) {
                            //     $dateObj->setTime(0, 0, 0);

                            //     // FLOOR removes any decimal time fraction
                            //     $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                            //     $objSheet6->setCellValue('B' . $row6, $excelDate);
                            // }

                            $objSheet6->setCellValue('B' . $row6, '=IF(A' . ($row6 + 1) . '="", A' . $row6 . ', A' . ($row6 + 1) . '-1)');
                            $objSheet6->SetCellValue('C' . $row6, $adata->supplier_name);
                            $objSheet6->SetCellValue('D' . $row6, $adata->contract_code);
                            $objSheet6->SetCellValue('E' . $row6, $adata->description);
                            $objSheet6->SetCellValue('F' . $row6, $adata->quantity + 0);
                            $objSheet6->SetCellValue('G' . $row6, $piecesFormula);
                            $objSheet6->SetCellValue('H' . $row6, $volumeFormula);
                            $objSheet6->SetCellValue('I' . $row6, $adata->amount + 0);
                            $objSheet6->SetCellValue('J' . $row6, $icasFormula);
                            $objSheet6->SetCellValue('K' . $row6, "=IF(G$row6=0, 0, I$row6/G$row6)");
                            $objSheet6->SetCellValue('L' . $row6, "=IF(G$row6=0, 0, I$row6/H$row6)");
                            $objSheet6->SetCellValue('M' . $row6, "=IF(G$row6=0, 0, I$row6/J$row6)");
                            $row6++;
                        }

                        $lastRow6 = $row6 - 1;
                        $objSheet6->getStyle("A5:M$lastRow6")->applyFromArray($styleArray);
                        $objSheet6->getStyle("F5:F$lastRow6")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
                        $objSheet6->getStyle("G5:G$lastRow6")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet6->getStyle("H5:H$lastRow6")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet6->getStyle("I5:I$lastRow6")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');
                        $objSheet6->getStyle("J5:J$lastRow6")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet6->getStyle("K5:M$lastRow6")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet6->getStyle("A5:B$lastRow6")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow6; $r++) {
                            $objSheet6->setCellValue('A' . $r, (int)$objSheet6->getCell('A' . $r)->getValue());
                            //$objSheet6->setCellValue('B' . $r, (int)$objSheet6->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet6->freezePane('A5');
                        // Auto filter
                        $objSheet6->setAutoFilter("A4:M$lastRow6");
                        // Set zoom scale
                        $objSheet6->getSheetView()->setZoomScale(95);

                    }

                    // Lubricants Cost
                    if (count($lubricantsData) > 0) {
                        $objSheet7 = $this->excel->createSheet();
                        $objSheet7->setTitle($this->lang->line('lubricants_cost'));
                        $objSheet7->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet7->SetCellValue('A4', strtoupper($this->lang->line('from_date')));
                        $objSheet7->SetCellValue('B4', strtoupper($this->lang->line('to_date')));
                        $objSheet7->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet7->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet7->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet7->SetCellValue('F4', strtoupper($this->lang->line('quantity')));
                        $objSheet7->SetCellValue('G4', strtoupper($this->lang->line('pieces')));
                        $objSheet7->SetCellValue('H4', strtoupper($this->lang->line('text_volume')));
                        $objSheet7->SetCellValue('I4', strtoupper($this->lang->line('amount')));
                        $objSheet7->SetCellValue('J4', strtoupper($this->lang->line('total_ica')));
                        $objSheet7->SetCellValue('K4', strtoupper($this->lang->line('value_piece')));
                        $objSheet7->SetCellValue('L4', strtoupper($this->lang->line('value_volume')));
                        $objSheet7->SetCellValue('M4', strtoupper($this->lang->line('value_ica')));

                        $objSheet7->getStyle("A4:M4")->getFont()->setBold(true);
                        $objSheet7->getStyle("A4:M4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet7->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet7->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet7->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('F')->setAutoSize(false)->setWidth(18);
                        $objSheet7->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('I')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('K')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('L')->setAutoSize(false)->setWidth(20);
                        $objSheet7->getColumnDimension('M')->setAutoSize(false)->setWidth(20);

                        $objSheet7->getStyle('A4:M4')->applyFromArray($styleArray);

                        $row7 = 5;
                        foreach ($lubricantsData as $ludata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row7 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row7 
                                . ')';

                            $icasFormula = '=COUNTIFS('
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row7 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row7 
                                . ')';

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($ludata->expense_date));

                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet7->setCellValue('A' . $row7, $excelDate);
                            }

                            // $dateObj = DateTime::createFromFormat('d/m/Y', trim($ludata->end_date));

                            // if ($dateObj !== false) {
                            //     $dateObj->setTime(0, 0, 0);

                            //     // FLOOR removes any decimal time fraction
                            //     $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                            //     $objSheet7->setCellValue('B' . $row7, $excelDate);
                            // }

                            $objSheet7->setCellValue('B' . $row7, '=IF(A' . ($row7 + 1) . '="", A' . $row7 . ', A' . ($row7 + 1) . '-1)');
                            $objSheet7->SetCellValue('C' . $row7, $ludata->supplier_name);
                            $objSheet7->SetCellValue('D' . $row7, $ludata->contract_code);
                            $objSheet7->SetCellValue('E' . $row7, $ludata->description);
                            $objSheet7->SetCellValue('F' . $row7, $ludata->quantity + 0);
                            $objSheet7->SetCellValue('G' . $row7, $piecesFormula);
                            $objSheet7->SetCellValue('H' . $row7, $volumeFormula);
                            $objSheet7->SetCellValue('I' . $row7, $ludata->amount + 0);
                            $objSheet7->SetCellValue('J' . $row7, $icasFormula);
                            $objSheet7->SetCellValue('K' . $row7, "=IF(G$row7=0, 0, I$row7/G$row7)");
                            $objSheet7->SetCellValue('L' . $row7, "=IF(G$row7=0, 0, I$row7/H$row7)");
                            $objSheet7->SetCellValue('M' . $row7, "=IF(G$row7=0, 0, I$row7/J$row7)");
                            $row7++;
                        }

                        $lastRow7 = $row7 - 1;
                        $objSheet7->getStyle("A5:M$lastRow7")->applyFromArray($styleArray);
                        $objSheet7->getStyle("F5:F$lastRow7")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
                        $objSheet7->getStyle("G5:G$lastRow7")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet7->getStyle("H5:H$lastRow7")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet7->getStyle("I5:I$lastRow7")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');
                        $objSheet7->getStyle("J5:J$lastRow7")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet7->getStyle("K5:M$lastRow7")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet7->getStyle("A5:B$lastRow7")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow7; $r++) {
                            $objSheet7->setCellValue('A' . $r, (int)$objSheet7->getCell('A' . $r)->getValue());
                            //$objSheet7->setCellValue('B' . $r, (int)$objSheet7->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet7->freezePane('A5');
                        // Auto filter
                        $objSheet7->setAutoFilter("A4:M$lastRow7");
                        // Set zoom scale
                        $objSheet7->getSheetView()->setZoomScale(95);
                    }

                    // Others Cost
                    if (count($othersData) > 0) {
                        $objSheet8 = $this->excel->createSheet();
                        $objSheet8->setTitle($this->lang->line('miscellaneous'));
                        $objSheet8->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSheet8->SetCellValue('A4', strtoupper($this->lang->line('from_date')));
                        $objSheet8->SetCellValue('B4', strtoupper($this->lang->line('to_date')));
                        $objSheet8->SetCellValue('C4', strtoupper($this->lang->line('supplier_name')));
                        $objSheet8->SetCellValue('D4', strtoupper($this->lang->line('purchase_contract')));
                        $objSheet8->SetCellValue('E4', strtoupper($this->lang->line('description')));
                        $objSheet8->SetCellValue('F4', strtoupper($this->lang->line('quantity')));
                        $objSheet8->SetCellValue('G4', strtoupper($this->lang->line('pieces')));
                        $objSheet8->SetCellValue('H4', strtoupper($this->lang->line('text_volume')));
                        $objSheet8->SetCellValue('I4', strtoupper($this->lang->line('amount')));
                        $objSheet8->SetCellValue('J4', strtoupper($this->lang->line('total_ica')));
                        $objSheet8->SetCellValue('K4', strtoupper($this->lang->line('value_piece')));
                        $objSheet8->SetCellValue('L4', strtoupper($this->lang->line('value_volume')));
                        $objSheet8->SetCellValue('M4', strtoupper($this->lang->line('value_ica')));

                        $objSheet8->getStyle("A4:M4")->getFont()->setBold(true);
                        $objSheet8->getStyle("A4:M4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSheet8->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                        $objSheet8->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                        $objSheet8->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('F')->setAutoSize(false)->setWidth(18);
                        $objSheet8->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('I')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('J')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('K')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('L')->setAutoSize(false)->setWidth(20);
                        $objSheet8->getColumnDimension('M')->setAutoSize(false)->setWidth(20);

                        $objSheet8->getStyle('A4:M4')->applyFromArray($styleArray);

                        $row8 = 5;
                        foreach ($othersData as $odata) {

                            $piecesFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$F$4:$F$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row8 . ',' 
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row8 
                                . ')';

                            $volumeFormula = '=SUMIFS('
                                . '\'' . $reportSheetName . '\'!$G$4:$G$10000,'
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row8 
                                . ')';

                            $icasFormula = '=COUNTIFS('
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, ">=" & A' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$A$4:$A$10000, "<=" & B' . $row8 . ',' 
                                . '\'' . $reportSheetName . '\'!$C$4:$C$10000, "<=" & C' . $row8 . ','
                                . '\'' . $reportSheetName . '\'!$D$4:$D$10000, "<=" & D' . $row8 
                                . ')';

                            $dateObj = DateTime::createFromFormat('d/m/Y', trim($odata->expense_date));
                            if ($dateObj !== false) {
                                $dateObj->setTime(0, 0, 0);

                                // FLOOR removes any decimal time fraction
                                $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                                $objSheet8->setCellValue('A' . $row8, $excelDate);
                            }

                            // $dateObj = DateTime::createFromFormat('d/m/Y', trim($odata->end_date));

                            // if ($dateObj !== false) {
                            //     $dateObj->setTime(0, 0, 0);

                            //     // FLOOR removes any decimal time fraction
                            //     $excelDate = (int) PHPExcel_Shared_Date::PHPToExcel($dateObj);

                            //     $objSheet8->setCellValue('B' . $row8, $excelDate);
                            // }

                            $objSheet8->setCellValue('B' . $row8, '=IF(A' . ($row8 + 1) . '="", A' . $row8 . ', A' . ($row8 + 1) . '-1)');
                            $objSheet8->SetCellValue('C' . $row8, $odata->supplier_name);
                            $objSheet8->SetCellValue('D' . $row8, $odata->contract_code);
                            $objSheet8->SetCellValue('E' . $row8, $odata->description);
                            $objSheet8->SetCellValue('F' . $row8, $odata->quantity + 0);
                            $objSheet8->SetCellValue('G' . $row8, $piecesFormula);
                            $objSheet8->SetCellValue('H' . $row8, $volumeFormula);
                            $objSheet8->SetCellValue('I' . $row8, $odata->amount + 0);
                            $objSheet8->SetCellValue('J' . $row8, $icasFormula);
                            $objSheet8->SetCellValue('K' . $row8, "=IF(G$row8=0, 0, I$row8/G$row8)");
                            $objSheet8->SetCellValue('L' . $row8, "=IF(G$row8=0, 0, I$row8/H$row8)");
                            $objSheet8->SetCellValue('M' . $row8, "=IF(G$row8=0, 0, I$row8/J$row8)");
                            $row8++;
                        }

                        $lastRow8 = $row8 - 1;
                        $objSheet8->getStyle("A5:M$lastRow8")->applyFromArray($styleArray);
                        $objSheet8->getStyle("F5:F$lastRow8")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
                        $objSheet8->getStyle("G5:G$lastRow8")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet8->getStyle("H5:H$lastRow8")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objSheet8->getStyle("I5:I$lastRow8")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');
                        $objSheet8->getStyle("J5:J$lastRow8")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                        $objSheet8->getStyle("K5:M$lastRow8")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                        // 🔥 FORCE date-only format for entire column
                        $objSheet8->getStyle("A5:B$lastRow8")
                            ->getNumberFormat()
                            ->setFormatCode('dd/mm/yyyy');

                        // 🔥 FORCE re-write values (this breaks cached datetime format)
                        for ($r = 5; $r <= $lastRow8; $r++) {
                            $objSheet8->setCellValue('A' . $r, (int)$objSheet8->getCell('A' . $r)->getValue());
                            //$objSheet8->setCellValue('B' . $r, (int)$objSheet8->getCell('B' . $r)->getValue());
                        }

                        // Freeze pane
                        $objSheet8->freezePane('A5');
                        // Auto filter
                        $objSheet8->setAutoFilter("A4:M$lastRow8");
                        // Set zoom scale
                        $objSheet8->getSheetView()->setZoomScale(95);
                    }

                    $objSheet9 = $this->excel->createSheet();
                    $objSheet9->setTitle($this->lang->line('inventory'));
                    $objSheet9->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $objSheet9->SetCellValue('A4', strtoupper($this->lang->line('total_no_of_pieces')));
                    $objSheet9->SetCellValue('A5', strtoupper($this->lang->line('total_volume_buyer')));
                    $objSheet9->SetCellValue('A6', strtoupper($this->lang->line('wood_value')));

                    $objSheet9->SetCellValue('B3', strtoupper($this->lang->line('extracted')));
                    $objSheet9->SetCellValue('C3', strtoupper($this->lang->line('loaded')));
                    $objSheet9->SetCellValue('D3', strtoupper($this->lang->line('remaining')));

                    $objSheet9->getStyle('A4')->applyFromArray($styleArray);
                    $objSheet9->getStyle("A4:A6")->getFont()->setBold(true);

                    $objSheet9->getStyle('B3:D3')->applyFromArray($styleArray);
                    $objSheet9->getStyle("B3:D3")->getFont()->setBold(true);

                    $extractedPiecesFormula = '='. '\'' . $extractionSheetName . '\'!$G$3';
                    $extractedVolumeFormula = '='. '\'' . $extractionSheetName . '\'!$H$3';

                    $loadedPiecesFormula = '='. '\'' . $reportSheetName . '\'!$F$3';
                    $loadedVolumeFormula = '='. '\'' . $reportSheetName . '\'!$G$3';
                    $totalWoodValueFormula = '='. '\'' . $reportSheetName . '\'!$I$3';

                    $objSheet9->SetCellValue('B4', $extractedPiecesFormula);
                    $objSheet9->SetCellValue('C4', $loadedPiecesFormula);
                    $objSheet9->SetCellValue('D4', "=B4-C4");

                    $objSheet9->SetCellValue('B5', $extractedVolumeFormula);
                    $objSheet9->SetCellValue('C5', $loadedVolumeFormula);
                    $objSheet9->SetCellValue('D5', "=B5-C5");

                    $objSheet9->SetCellValue('B6', $totalWoodValueFormula);

                    $objSheet9->getStyle("A4:D5")->applyFromArray($styleArray);
                    $objSheet9->getStyle("A6:B6")->applyFromArray($styleArray);

                    $objSheet9->getStyle("B4:D4")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $objSheet9->getStyle("B5:D5")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet9->getStyle("B6")->getNumberFormat()->setFormatCode('_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)');

                    $objSheet9->getColumnDimension('A')->setAutoSize(false)->setWidth(20);
                    $objSheet9->getColumnDimension('B')->setAutoSize(false)->setWidth(25);
                    $objSheet9->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                    $objSheet9->getColumnDimension('D')->setAutoSize(false)->setWidth(25);

                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ForestryReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ForestryReports/' . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ForestryReports/' . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                }
            }
        } else {
            redirect('/logout');
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

        $files = glob(FCPATH . "reports/ForestryReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
