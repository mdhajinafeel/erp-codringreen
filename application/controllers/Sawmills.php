<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Sawmills extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Sawmill_model");
        $this->load->library('excel');
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("sawmill_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_sawmill";
        if (!empty($session)) {

            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("sawmills/sawmill_list", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function sawmill_list()
    {
        $data['title'] =  $this->lang->line('sawmill_title') . " - " . $this->lang->line('inventory_title') .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            $this->load->view("sawmills/sawmill_list", $data);
        } else {
            redirect("/logout");
        }

        $draw = intval($this->input->get("draw"));
        $originid = intval($this->input->get("originid"));

        if ($originid == 0) {
            $farms = $this->Sawmill_model->all_sawmills();
        } else {
            $farms = $this->Sawmill_model->all_sawmills_origin($originid);
        }

        $data = array();

        foreach ($farms as $r) {
            $editFarm = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_excel') . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="downloadfarm" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-created_from="' . $r->created_from . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-download"></span></button></span>
            <span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="viewfarm" data-toggle="modal" data-target=".edit-modal-data" data-farm_id="' . $r->farm_id . '" data-contract_id="' . $r->contract_id . '" data-inventory_order="' . $r->inventory_order . '"><span class="fas fa-eye"></span></button></span>';

            $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

            $data[] = array(
                $r->supplier_name,
                ($r->count_ica + 0),
                ($r->total_pieces + 0),
                sprintf("%0.3f", ($r->total_volume + 0)),
                '$ ' . number_format(($r->total_cost + 0), 2, ',', '.'),
            );
        }

        $output = array(
            "draw" => $draw,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function generate_sawmill_report()
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

                $getSawmillReport = $this->Sawmill_model->get_sawmills_reports($this->input->get("originid"));

                if (count($getSawmillReport) > 0) {

                    $this->deletefilesfromfolder();

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('report_summary'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $objSheet->SetCellValue('A6', $this->lang->line('s_no'));
                    $objSheet->SetCellValue('B6', $this->lang->line('supplier_name'));
                    $objSheet->SetCellValue('C6', $this->lang->line('total_trucks'));
                    $objSheet->SetCellValue('D6', $this->lang->line('pieces'));
                    $objSheet->SetCellValue('E6', $this->lang->line('text_volume'));
                    $objSheet->SetCellValue('F6', $this->lang->line('wood_value'));
                    $objSheet->SetCellValue('G6', $this->lang->line('extraction_cost'));
                    $objSheet->SetCellValue('H6', $this->lang->line('logistic_cost'));
                    $objSheet->SetCellValue('I6', $this->lang->line('zona'));
                    $objSheet->SetCellValue('J6', $this->lang->line('loading_cost'));
                    $objSheet->SetCellValue('K6', $this->lang->line('unloading_cost'));
                    $objSheet->SetCellValue('L6', $this->lang->line('total'));

                    $objSheet->getStyle("A6:L6")->getFont()->setBold(true);
                    $objSheet->setAutoFilter('A6:L6');

                    // HEADER ALIGNMENT
                    $objSheet->getStyle("A6:L6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
                    $objSheet->getColumnDimension('C')->setAutoSize(true);
                    $objSheet->getColumnDimension('D')->setAutoSize(true);
                    $objSheet->getColumnDimension('E')->setAutoSize(true);
                    $objSheet->getColumnDimension('F')->setAutoSize(true);
                    $objSheet->getColumnDimension('G')->setAutoSize(true);
                    $objSheet->getColumnDimension('H')->setAutoSize(true);
                    $objSheet->getColumnDimension('I')->setAutoSize(true);
                    $objSheet->getColumnDimension('J')->setAutoSize(true);
                    $objSheet->getColumnDimension('K')->setAutoSize(true);
                    $objSheet->getColumnDimension('L')->setAutoSize(true);

                    $objSheet->getStyle('A6:L6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A6:L6')->applyFromArray($styleArray);

                    $i = 1;
                    $rowCountData = 7;

                    foreach ($getSawmillReport as $sawmilldata) {

                        $objSheet->SetCellValue("A$rowCountData", $i);
                        $objSheet->SetCellValue("B$rowCountData", $sawmilldata->supplier_name);
                        $objSheet->SetCellValue("C$rowCountData", $sawmilldata->count_ica + 0);
                        $objSheet->SetCellValue("D$rowCountData", $sawmilldata->total_pieces + 0);
                        $objSheet->SetCellValue("E$rowCountData", $sawmilldata->total_volume + 0);
                        $objSheet->SetCellValue("F$rowCountData", $sawmilldata->wood_value + 0);
                        $objSheet->SetCellValue("G$rowCountData", $sawmilldata->extraction_cost + 0);
                        $objSheet->SetCellValue("H$rowCountData", $sawmilldata->logistic_cost + 0);
                        $objSheet->SetCellValue("I$rowCountData", $sawmilldata->service_cost + 0);
                        $objSheet->SetCellValue("J$rowCountData", $sawmilldata->loading_cost + 0);
                        $objSheet->SetCellValue("K$rowCountData", $sawmilldata->unloading_cost + 0);
                        $objSheet->SetCellValue("L$rowCountData", "=SUM(F$rowCountData:K$rowCountData)");

                        $i++;
                        $rowCountData++;
                    }

                    $lastSummaryDataRow = $rowCountData - 1;

                    $objSheet->SetCellValue("C5", "=SUM(C7:C$lastSummaryDataRow)");
                    $objSheet->SetCellValue("D5", "=SUM(D7:D$lastSummaryDataRow)");
                    $objSheet->SetCellValue("E5", "=SUM(E7:E$lastSummaryDataRow)");
                    $objSheet->SetCellValue("F5", "=SUM(F7:F$lastSummaryDataRow)");
                    $objSheet->SetCellValue("G5", "=SUM(G7:G$lastSummaryDataRow)");
                    $objSheet->SetCellValue("H5", "=SUM(H7:H$lastSummaryDataRow)");
                    $objSheet->SetCellValue("I5", "=SUM(I7:I$lastSummaryDataRow)");
                    $objSheet->SetCellValue("J5", "=SUM(J7:J$lastSummaryDataRow)");
                    $objSheet->SetCellValue("K5", "=SUM(K7:K$lastSummaryDataRow)");
                    $objSheet->getStyle("C5:K5")->applyFromArray($styleArray);
                    $objSheet->getStyle("C5:K5")->getFont()->setBold(true);
                    $objSheet->getStyle("E5")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("F5:K5")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    $lastRowData = $rowCountData - 1;
                    $objSheet->getStyle("A7:L$lastRowData")->applyFromArray($styleArray);
                    $objSheet->getStyle("E7:E$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    $objSheet->getStyle("F7:L$lastRowData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    $objSheet->SetCellValue("K$rowCountData", $this->lang->line('process_cost'));
                    $sawmillProcessText = $this->lang->line('sawmill_process');
                    $objSheet->SetCellValue("L$rowCountData", "='$sawmillProcessText'!K2");
                    $objSheet->getStyle("K$rowCountData")->getFont()->setBold(true);
                    $objSheet->getStyle("K$rowCountData:L$rowCountData")->applyFromArray($styleArray);
                    $objSheet->getStyle("L$rowCountData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    $lastRowData = $rowCountData + 1;
                    $totalCostRow = $lastRowData;
                    $totalCostRowNumber = $lastRowData;
                    $objSheet->SetCellValue("K$lastRowData", $this->lang->line('total_cost'));
                    $objSheet->SetCellValue("L$lastRowData", "=SUM(L7:L$rowCountData)");
                    $objSheet->getStyle("K$lastRowData")->getFont()->setBold(true);
                    $objSheet->getStyle("K$lastRowData:L$lastRowData")->applyFromArray($styleArray);
                    $objSheet->getStyle("L$lastRowData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    // $lastRowData++;
                    // $totalVolumeRowNumber = $lastRowData;
                    // $objSheet->SetCellValue("H$lastRowData", $this->lang->line('total_volume'));
                    // $objSheet->SetCellValue("I$lastRowData", "=SUM(E7:E$lastSummaryDataRow)");
                    // $objSheet->getStyle("H$lastRowData")->getFont()->setBold(true);
                    // $objSheet->getStyle("H$lastRowData:I$lastRowData")->applyFromArray($styleArray);
                    // $objSheet->getStyle("I$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $lastRowData++;
                    $processRowNumber = $lastRowData;
                    $objSheet->SetCellValue("H$lastRowData", $this->lang->line('total_processed_pieces'));
                    $objSheet->SetCellValue("I$lastRowData", "='$sawmillProcessText'!D2");
                    $objSheet->getStyle("H$lastRowData")->getFont()->setBold(true);
                    $objSheet->getStyle("H$lastRowData:I$lastRowData")->applyFromArray($styleArray);
                    //$objSheet->getStyle("I$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $lastRowData++;
                    $exportedRowNumber = $lastRowData;
                    $exportText = $this->lang->line('costsummary_export');
                    $objSheet->SetCellValue("H$lastRowData", $this->lang->line('total_exported_pieces'));
                    $objSheet->SetCellValue("I$lastRowData", "=$exportText!B2");
                    $objSheet->getStyle("H$lastRowData")->getFont()->setBold(true);
                    $objSheet->getStyle("H$lastRowData:I$lastRowData")->applyFromArray($styleArray);
                    //$objSheet->getStyle("I$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $lastRowData++;
                    $remainingRowData = $lastRowData;
                    $objSheet->SetCellValue("H$lastRowData", $this->lang->line('remaining_inventory_pieces'));
                    $objSheet->SetCellValue("I$lastRowData", "=I$processRowNumber-I$exportedRowNumber");
                    $objSheet->getStyle("H$lastRowData")->getFont()->setBold(true);
                    $objSheet->getStyle("H$lastRowData:I$lastRowData")->applyFromArray($styleArray);
                    //$objSheet->getStyle("I$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $lastRowData++;
                    $objSheet->SetCellValue("H$lastRowData", $this->lang->line('unprocessed_pieces'));
                    $objSheet->SetCellValue("I$lastRowData", "=D5-I$processRowNumber");
                    $objSheet->getStyle("H$lastRowData")->getFont()->setBold(true);
                    $objSheet->getStyle("H$lastRowData:I$lastRowData")->applyFromArray($styleArray);
                    //$objSheet->getStyle("I$lastRowData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $totalCostRow++;
                    $perCbmCostRow = $totalCostRow;
                    $objSheet->SetCellValue("K$totalCostRow", $this->lang->line('cost_per_pcs'));
                    $objSheet->SetCellValue("L$totalCostRow", "=L$totalCostRowNumber/I$processRowNumber");
                    $objSheet->getStyle("K$totalCostRow")->getFont()->setBold(true);
                    $objSheet->getStyle("K$totalCostRow:L$totalCostRow")->applyFromArray($styleArray);
                    $objSheet->getStyle("L$totalCostRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    // $totalCostRow++;
                    // $objSheet->SetCellValue("K$totalCostRow", $this->lang->line('processed_cost'));
                    // $objSheet->SetCellValue("L$totalCostRow", "=I$processRowNumber*L$perCbmCostRow");
                    // $objSheet->getStyle("K$totalCostRow")->getFont()->setBold(true);
                    // $objSheet->getStyle("K$totalCostRow:L$totalCostRow")->applyFromArray($styleArray);
                    // $objSheet->getStyle("L$totalCostRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    $totalCostRow++;
                    $objSheet->SetCellValue("K$totalCostRow", $this->lang->line('exported_cost'));
                    $objSheet->SetCellValue("L$totalCostRow", "=I$exportedRowNumber*L$perCbmCostRow");
                    $objSheet->getStyle("K$totalCostRow")->getFont()->setBold(true);
                    $objSheet->getStyle("K$totalCostRow:L$totalCostRow")->applyFromArray($styleArray);
                    $objSheet->getStyle("L$totalCostRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    $totalCostRow++;
                    $objSheet->SetCellValue("K$totalCostRow", $this->lang->line('remaining_inventory_cost'));
                    $objSheet->SetCellValue("L$totalCostRow", "=I$remainingRowData*L$perCbmCostRow");
                    $objSheet->getStyle("K$totalCostRow")->getFont()->setBold(true);
                    $objSheet->getStyle("K$totalCostRow:L$totalCostRow")->applyFromArray($styleArray);
                    $objSheet->getStyle("L$totalCostRow")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                    //ICA OPERATIONS - SAWMILL

                    $sheetNo = 0;

                    $getSawmillProcessData = $this->Sawmill_model->get_sawmill_process_data();

                    if (count($getSawmillProcessData) > 0) {

                        $sheetNo++;
                        $objInventorySheet = $this->excel->createSheet($sheetNo);
                        $objInventorySheet->setTitle($this->lang->line("sawmill_process"));
                        $objInventorySheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objInventorySheet->SetCellValue('A3', $this->lang->line('s_no'));
                        $objInventorySheet->SetCellValue('B3', $this->lang->line('girth_rl'));
                        $objInventorySheet->SetCellValue('C3', $this->lang->line('girth_rsq'));
                        $objInventorySheet->SetCellValue('D3', $this->lang->line('length'));
                        $objInventorySheet->SetCellValue('E3', $this->lang->line('gross_volume_rl'));
                        $objInventorySheet->SetCellValue('F3', $this->lang->line('gross_volume_rsq'));
                        $objInventorySheet->SetCellValue('G3', $this->lang->line('gross_loss'));
                        $objInventorySheet->SetCellValue('H3', $this->lang->line('gross_loss_percentage'));
                        $objInventorySheet->SetCellValue('I3', $this->lang->line('process_cost'));
                        $objInventorySheet->SetCellValue('J3', $this->lang->line('exchange_rate'));
                        $objInventorySheet->SetCellValue('K3', $this->lang->line('processed_cost'));

                        $objInventorySheet->getStyle("A3:K3")->getFont()->setBold(true);
                        $objInventorySheet->setAutoFilter('A3:K3');
                        $objInventorySheet->getStyle('A3:K3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objInventorySheet->getStyle("A3:K3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objInventorySheet->getColumnDimension('A')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('B')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('C')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('D')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('E')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('F')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('G')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('H')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('I')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('J')->setAutoSize(true);
                        $objInventorySheet->getColumnDimension('K')->setAutoSize(true);

                        $j = 1;
                        $rowInventoryData = 4;

                        foreach ($getSawmillProcessData as $processdata) {

                            $objInventorySheet->SetCellValue("A$rowInventoryData", $j);
                            $objInventorySheet->SetCellValue("B$rowInventoryData", $processdata->girth_rl + 0);
                            $objInventorySheet->SetCellValue("C$rowInventoryData", $processdata->girth_rsq + 0);
                            $objInventorySheet->SetCellValue("D$rowInventoryData", $processdata->length + 0);
                            $objInventorySheet->SetCellValue("E$rowInventoryData", $processdata->gross_volume_rl + 0);
                            $objInventorySheet->SetCellValue("F$rowInventoryData", $processdata->gross_volume_rsq + 0);
                            $objInventorySheet->SetCellValue("G$rowInventoryData", $processdata->gross_loss + 0);
                            $objInventorySheet->SetCellValue("H$rowInventoryData", $processdata->gross_loss_percentage + 0);
                            $objInventorySheet->SetCellValue("I$rowInventoryData", $processdata->process_cost + 0);
                            $objInventorySheet->SetCellValue("J$rowInventoryData", $processdata->exchange_rate + 0);
                            $objInventorySheet->SetCellValue("K$rowInventoryData", "=(I$rowInventoryData*J$rowInventoryData)*F$rowInventoryData");

                            $j++;
                            $rowInventoryData++;
                        }

                        $lastRowInventoryData = $rowInventoryData - 1;

                        $objInventorySheet->getStyle("A3:K$lastRowInventoryData")->applyFromArray($styleArray);

                        $objInventorySheet->getStyle("E4:G$lastRowInventoryData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objInventorySheet->getStyle("I4:K$lastRowInventoryData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objInventorySheet->getStyle("H4:H$lastRowInventoryData")->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));

                        $objInventorySheet->SetCellValue("D2", "=COUNT(D4:D$lastRowInventoryData)");
                        $objInventorySheet->SetCellValue("E2", "=SUM(E4:E$lastRowInventoryData)");
                        $objInventorySheet->SetCellValue("F2", "=SUM(F4:F$lastRowInventoryData)");
                        $objInventorySheet->SetCellValue("G2", "=SUM(G4:G$lastRowInventoryData)");
                        $objInventorySheet->SetCellValue("H2", "=AVERAGE(H4:H$lastRowInventoryData)");
                        $objInventorySheet->SetCellValue("K2", "=SUM(K4:K$lastRowInventoryData)");

                        $objInventorySheet->getStyle("E2:G2")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                        $objInventorySheet->getStyle("K2")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');
                        $objInventorySheet->getStyle("H2")->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));

                        $objInventorySheet->getStyle("D2:H2")->applyFromArray($styleArray);
                        $objInventorySheet->getStyle("K2")->applyFromArray($styleArray);
                    }

                    //END ICA OPERATIONS - SAWMILL

                    //SAWMILL INVENTORY

                    $getSawmillInventoryNumber = $this->Sawmill_model->get_sawmill_inventory_number($this->input->get("originid"));
                    if ($getSawmillInventoryNumber[0]->inventory_numbers == '-') {
                        // DO NOTHING
                    } else {
                        $sheetNo++;
                        $objSawmillInventorySheet = $this->excel->createSheet($sheetNo);
                        $objSawmillInventorySheet->setTitle($this->lang->line("sawmill_inventory"));
                        $objSawmillInventorySheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSawmillInventorySheet->SetCellValue('A3', $this->lang->line('ica_number'));
                        $objSawmillInventorySheet->SetCellValue('B3', $this->lang->line('pieces'));
                        $objSawmillInventorySheet->SetCellValue('C3', $this->lang->line('circumference'));
                        $objSawmillInventorySheet->SetCellValue('D3', $this->lang->line('length'));
                        $objSawmillInventorySheet->SetCellValue('E3', $this->lang->line('gross_volume'));
                        $objSawmillInventorySheet->SetCellValue('F3', $this->lang->line('net_volume'));
                        $objSawmillInventorySheet->SetCellValue('G3', $this->lang->line('exported_container'));

                        $objSawmillInventorySheet->getStyle("A3:G3")->getFont()->setBold(true);
                        $objSawmillInventorySheet->setAutoFilter('A3:G3');
                        $objSawmillInventorySheet->getStyle('A3:G3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objSawmillInventorySheet->getStyle("A3:G3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSawmillInventorySheet->getColumnDimension('A')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('B')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('C')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('D')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('E')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('F')->setAutoSize(true);
                        $objSawmillInventorySheet->getColumnDimension('G')->setAutoSize(true);

                        $rowSawmillInventoryData = 4;

                        $getSawmillInventoryData = $this->Sawmill_model->get_sawmill_inventory_data($getSawmillInventoryNumber[0]->inventory_numbers);

                        foreach ($getSawmillInventoryData as $sawmillinventorydata) {

                            $objSawmillInventorySheet->SetCellValue("A$rowSawmillInventoryData", $sawmillinventorydata->salvoconducto);
                            $objSawmillInventorySheet->SetCellValue("B$rowSawmillInventoryData", $sawmillinventorydata->pieces + 0);
                            $objSawmillInventorySheet->SetCellValue("C$rowSawmillInventoryData", $sawmillinventorydata->circumference + 0);
                            $objSawmillInventorySheet->SetCellValue("D$rowSawmillInventoryData", $sawmillinventorydata->length + 0);
                            $objSawmillInventorySheet->SetCellValue("E$rowSawmillInventoryData", $sawmillinventorydata->gross_volume + 0);
                            $objSawmillInventorySheet->SetCellValue("F$rowSawmillInventoryData", $sawmillinventorydata->net_volume + 0);
                            $objSawmillInventorySheet->SetCellValue("G$rowSawmillInventoryData", $sawmillinventorydata->container_number);

                            $rowSawmillInventoryData++;
                        }

                        $lastRowSawmillInventoryData = $rowSawmillInventoryData - 1;

                        $objSawmillInventorySheet->getStyle("A3:G$lastRowSawmillInventoryData")->applyFromArray($styleArray);
                        $objSawmillInventorySheet->getStyle("E4:F$lastRowSawmillInventoryData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSawmillInventorySheet->SetCellValue("A2", "=SUMPRODUCT(1/COUNTIF(A4:A$lastRowSawmillInventoryData, A4:A$lastRowSawmillInventoryData))");
                        $objSawmillInventorySheet->SetCellValue("B2", "=SUM(B4:B$lastRowSawmillInventoryData)");
                        $objSawmillInventorySheet->SetCellValue("E2", "=SUM(E4:E$lastRowSawmillInventoryData)");
                        $objSawmillInventorySheet->SetCellValue("F2", "=SUM(F4:F$lastRowSawmillInventoryData)");

                        $objSawmillInventorySheet->getStyle("A2")->applyFromArray($styleArray);
                        $objSawmillInventorySheet->getStyle("B2")->applyFromArray($styleArray);
                        $objSawmillInventorySheet->getStyle("E2")->applyFromArray($styleArray);
                        $objSawmillInventorySheet->getStyle("F2")->applyFromArray($styleArray);

                        $objSawmillInventorySheet->getStyle("E2:F2")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    }

                    //END SAWMILL INVENTORY

                    //CONTAINER

                    $getSawmillContainerData = $this->Sawmill_model->get_sawmill_container_data($this->input->get("originid"));

                    if (count($getSawmillContainerData) > 0) {

                        $sheetNo++;
                        $objContainerSheet = $this->excel->createSheet($sheetNo);
                        $objContainerSheet->setTitle($this->lang->line("costsummary_export"));
                        $objContainerSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objContainerSheet->SetCellValue('A3', $this->lang->line('container_number'));
                        $objContainerSheet->SetCellValue('B3', $this->lang->line('pieces'));
                        $objContainerSheet->SetCellValue('C3', $this->lang->line('circumference'));
                        $objContainerSheet->SetCellValue('D3', $this->lang->line('length'));
                        $objContainerSheet->SetCellValue('E3', $this->lang->line('gross_volume'));
                        $objContainerSheet->SetCellValue('F3', $this->lang->line('net_volume'));
                        $objContainerSheet->SetCellValue('G3', $this->lang->line('ica_number'));

                        $objContainerSheet->getStyle("A3:G3")->getFont()->setBold(true);
                        $objContainerSheet->setAutoFilter('A3:G3');
                        $objContainerSheet->getStyle('A3:G3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objContainerSheet->getStyle("A3:G3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objContainerSheet->getColumnDimension('A')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('B')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('C')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('D')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('E')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('F')->setAutoSize(true);
                        $objContainerSheet->getColumnDimension('G')->setAutoSize(true);

                        $rowContainerData = 4;

                        foreach ($getSawmillContainerData as $containerdata) {

                            $objContainerSheet->SetCellValue("A$rowContainerData", $containerdata->container_number);
                            $objContainerSheet->SetCellValue("B$rowContainerData", $containerdata->dispatch_pieces + 0);
                            $objContainerSheet->SetCellValue("C$rowContainerData", $containerdata->circumference_bought + 0);
                            $objContainerSheet->SetCellValue("D$rowContainerData", $containerdata->length_bought + 0);
                            $objContainerSheet->SetCellValue("E$rowContainerData", $containerdata->gross_volume + 0);
                            $objContainerSheet->SetCellValue("F$rowContainerData", $containerdata->net_volume + 0);
                            $objContainerSheet->SetCellValue("G$rowContainerData", $containerdata->salvoconducto);

                            $rowContainerData++;
                        }

                        $lastRowContainerData = $rowContainerData - 1;

                        $objContainerSheet->getStyle("A3:G$lastRowContainerData")->applyFromArray($styleArray);
                        $objContainerSheet->getStyle("E4:F$lastRowContainerData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objContainerSheet->SetCellValue("B2", "=SUM(B4:B$lastRowContainerData)");
                        $objContainerSheet->SetCellValue("E2", "=SUM(E4:E$lastRowContainerData)");
                        $objContainerSheet->SetCellValue("F2", "=SUM(F4:F$lastRowContainerData)");

                        $objContainerSheet->getStyle("B2")->applyFromArray($styleArray);
                        $objContainerSheet->getStyle("E2")->applyFromArray($styleArray);
                        $objContainerSheet->getStyle("F2")->applyFromArray($styleArray);

                        $objContainerSheet->getStyle("E2:F2")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    }

                    //END CONTAINER

                    //EXPORT SUMMARY

                    $getExportSummaryData = $this->Sawmill_model->fetch_export_summary_data_sawmill($this->input->get("originid"));

                    if (count($getExportSummaryData) > 0) {

                        $sheetNo++;
                        $objExportSummarySheet = $this->excel->createSheet($sheetNo);
                        $objExportSummarySheet->setTitle($this->lang->line("export_summary"));
                        $objExportSummarySheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objExportSummarySheet->SetCellValue('A4', $this->lang->line('sa_no'));
                        $objExportSummarySheet->SetCellValue('B4', $this->lang->line('container_#'));
                        $objExportSummarySheet->SetCellValue('C4', $this->lang->line('ica_number'));
                        $objExportSummarySheet->SetCellValue('D4', $this->lang->line('received_pieces'));
                        $objExportSummarySheet->SetCellValue('E4', $this->lang->line('dispatched_pieces'));
                        $objExportSummarySheet->SetCellValue('F4', $this->lang->line('material_cost'));
                        $objExportSummarySheet->SetCellValue('G4', $this->lang->line('loading_cost'));
                        $objExportSummarySheet->SetCellValue('H4', $this->lang->line('logistic_cost'));
                        $objExportSummarySheet->SetCellValue('I4', $this->lang->line('total_cost'));
                        $objExportSummarySheet->SetCellValue('J4', $this->lang->line('cost_per_pcs'));
                        $objExportSummarySheet->SetCellValue('K4', $this->lang->line('dispatch_cost'));

                        $objExportSummarySheet->getStyle("A4:K4")->getFont()->setBold(true);
                        $objExportSummarySheet->setAutoFilter('A4:K4');
                        $objExportSummarySheet->getStyle('A4:K4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                        $objExportSummarySheet->getStyle("A4:K4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objExportSummarySheet->getColumnDimension('A')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('B')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('C')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('D')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('E')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('F')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('G')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('H')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('I')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('J')->setAutoSize(true);
                        $objExportSummarySheet->getColumnDimension('K')->setAutoSize(true);

                        $rowExportData = 5;

                        $reportSummaryText = $this->lang->line('report_summary');

                        foreach ($getExportSummaryData as $exportdata) {

                            $objExportSummarySheet->SetCellValue("A$rowExportData", $exportdata->sanumber);
                            $objExportSummarySheet->SetCellValue("B$rowExportData", $exportdata->container_number);
                            $objExportSummarySheet->SetCellValue("C$rowExportData", $exportdata->inventory_number);
                            $objExportSummarySheet->getStyle("C$rowExportData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objExportSummarySheet->SetCellValue("D$rowExportData", $exportdata->received_pieces + 0);
                            $objExportSummarySheet->SetCellValue("E$rowExportData", $exportdata->dispatch_pieces + 0);
                            $objExportSummarySheet->SetCellValue("F$rowExportData", "=$reportSummaryText!L$perCbmCostRow*D$rowExportData");
                            $objExportSummarySheet->SetCellValue("G$rowExportData", $exportdata->loading_cost_sawmill + 0);
                            $objExportSummarySheet->SetCellValue("H$rowExportData", $exportdata->transport_cost_sawmill + 0);
                            $objExportSummarySheet->SetCellValue("I$rowExportData", "=SUM(F$rowExportData:H$rowExportData)");
                            $objExportSummarySheet->SetCellValue("J$rowExportData", "=I$rowExportData/D$rowExportData");
                            $objExportSummarySheet->SetCellValue("K$rowExportData", "=J$rowExportData*E$rowExportData");

                            $rowExportData++;
                        }

                        $lastRowExportData = $rowExportData - 1;

                        $objExportSummarySheet->getStyle("A4:K$lastRowExportData")->applyFromArray($styleArray);
                        $objExportSummarySheet->getStyle("F4:K$lastRowExportData")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                        $objExportSummarySheet->SetCellValue("E3", "=SUM(E5:E$lastRowExportData)");
                        $objExportSummarySheet->SetCellValue("K3", "=SUM(K5:K$lastRowExportData)");

                        $objExportSummarySheet->getStyle("K3")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)');

                        $objExportSummarySheet->getStyle("E3")->applyFromArray($styleArray);
                        $objExportSummarySheet->getStyle("K3")->applyFromArray($styleArray);

                        $objExportSummarySheet->getStyle("E3")->getFont()->setBold(true);
                        $objExportSummarySheet->getStyle("K3")->getFont()->setBold(true);
                    }

                    //END EXPORT SUMMARY

                    $objSheet->getSheetView()->setZoomScale(95);
                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'SawmillReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
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

    public function generate_received_report()
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

                $getSawmillReceivedReport = $this->Sawmill_model->fetch_received_data_sawmill($this->input->get("originid"));

                if (count($getSawmillReceivedReport) > 0) {

                    $this->deletefilesfromfolder();

                    $this->excel->setActiveSheetIndex(0);
                    $objSheet = $this->excel->getActiveSheet();
                    $objSheet->setTitle($this->lang->line('detailed_data'));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $objSheet->SetCellValue('A4', $this->lang->line('s_no'));
                    $objSheet->SetCellValue('B4', $this->lang->line('ica_number'));
                    $objSheet->SetCellValue('C4', $this->lang->line('supplier_name'));
                    $objSheet->SetCellValue('D4', $this->lang->line('pieces'));
                    $objSheet->SetCellValue('E4', $this->lang->line('circumference'));
                    $objSheet->SetCellValue('F4', $this->lang->line('length'));
                    $objSheet->SetCellValue('G4', $this->lang->line('gross_volume'));
                    $objSheet->SetCellValue('H4', $this->lang->line('net_volume'));

                    $objSheet->getStyle("A4:H4")->getFont()->setBold(true);
                    $objSheet->setAutoFilter('A4:H4');

                    // HEADER ALIGNMENT
                    $objSheet->getStyle("A4:H4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objSheet->getColumnDimension('A')->setAutoSize(true);
                    $objSheet->getColumnDimension('B')->setAutoSize(true);
                    $objSheet->getColumnDimension('C')->setAutoSize(true);
                    $objSheet->getColumnDimension('D')->setAutoSize(true);
                    $objSheet->getColumnDimension('E')->setAutoSize(true);
                    $objSheet->getColumnDimension('F')->setAutoSize(true);
                    $objSheet->getColumnDimension('G')->setAutoSize(true);
                    $objSheet->getColumnDimension('H')->setAutoSize(true);

                    $objSheet->getStyle('A4:H4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objSheet->getStyle('A4:H4')->applyFromArray($styleArray);

                    $i = 1;
                    $rowCountData = 5;

                    foreach ($getSawmillReceivedReport as $receiveddata) {

                        $objSheet->SetCellValue("A$rowCountData", $i);
                        $objSheet->SetCellValue("B$rowCountData", $receiveddata->inventory_order);
                        $objSheet->getStyle("B$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $objSheet->SetCellValue("C$rowCountData", $receiveddata->supplier_name);
                        $objSheet->getStyle("C$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->SetCellValue("D$rowCountData", $receiveddata->no_of_pieces + 0);
                        $objSheet->SetCellValue("E$rowCountData", $receiveddata->circumference + 0);
                        $objSheet->SetCellValue("F$rowCountData", $receiveddata->length + 0);
                        $objSheet->SetCellValue("G$rowCountData", $receiveddata->gross_volume + 0);
                        $objSheet->SetCellValue("H$rowCountData", $receiveddata->net_volume + 0);
                        $i++;
                        $rowCountData++;
                    }

                    $lastSummaryDataRow = $rowCountData - 1;
                    $objSheet->getStyle("A5:H$lastSummaryDataRow")->applyFromArray($styleArray);
                    $objSheet->getStyle("G3:H$lastSummaryDataRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $objSheet->SetCellValue("D3", "=SUM(D5:D$lastSummaryDataRow)");
                    $objSheet->getStyle("D3")->applyFromArray($styleArray);

                    $objSheet->SetCellValue("G3", "=SUM(G5:G$lastSummaryDataRow)");
                    $objSheet->SetCellValue("H3", "=SUM(H5:H$lastSummaryDataRow)");
                    $objSheet->getStyle("G3:H3")->applyFromArray($styleArray);

                    $objSheet->getStyle("D3:H3")->getFont()->setBold(true);
                    $objSheet->getStyle("G3:H3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $getSawmillSummaryReport = $this->Sawmill_model->fetch_received_summary_data_sawmill($this->input->get("originid"));
                    $sheetNo = 0;
                    if (count($getSawmillSummaryReport) > 0) {
                        $sheetNo++;
                        $objSummarySheet = $this->excel->createSheet($sheetNo);
                        $objSummarySheet->setTitle($this->lang->line("report_summary"));
                        $objSummarySheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSummarySheet->SetCellValue('A4', $this->lang->line('ica_number'));
                        $objSummarySheet->SetCellValue('B4', $this->lang->line('supplier_name'));
                        $objSummarySheet->SetCellValue('C4', $this->lang->line('purchase_date'));
                        $objSummarySheet->SetCellValue('D4', $this->lang->line('pieces'));
                        $objSummarySheet->SetCellValue('E4', $this->lang->line('gross_volume'));
                        $objSummarySheet->SetCellValue('F4', $this->lang->line('net_volume'));

                        $objSummarySheet->getStyle("A4:F4")->getFont()->setBold(true);
                        $objSummarySheet->setAutoFilter('A4:F4');
                        $objSummarySheet->getStyle('A4:F4')->applyFromArray($styleArray);

                        // HEADER ALIGNMENT
                        $objSummarySheet->getStyle("A4:F4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSummarySheet->getColumnDimension('A')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('B')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('C')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('D')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('E')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('F')->setAutoSize(true);

                        $rowSummaryCountData = 5;

                        foreach ($getSawmillSummaryReport as $summarydata) {

                            $objSummarySheet->SetCellValue("A$rowSummaryCountData", $summarydata->inventory_order);
                            $objSummarySheet->getStyle("A$rowSummaryCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $objSummarySheet->SetCellValue("B$rowSummaryCountData", $summarydata->supplier_name);
                            $objSummarySheet->getStyle("B$rowSummaryCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objSummarySheet->SetCellValue("C$rowSummaryCountData", $summarydata->purchase_date);

                            $objSummarySheet->SetCellValue("D$rowSummaryCountData", $summarydata->pieces + 0);
                            $objSummarySheet->SetCellValue("E$rowSummaryCountData", $summarydata->gross_volume + 0);
                            $objSummarySheet->SetCellValue("F$rowSummaryCountData", $summarydata->net_volume + 0);
                            $rowSummaryCountData++;
                        }

                        $lastDataRow = $rowSummaryCountData - 1;
                        $objSummarySheet->getStyle("A5:F$lastDataRow")->applyFromArray($styleArray);
                        $objSummarySheet->getStyle("E3:F$lastDataRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSummarySheet->SetCellValue("A3", "=COUNTA(A5:A$lastDataRow)");
                        $objSummarySheet->getStyle("A3")->applyFromArray($styleArray);
                        $objSummarySheet->getStyle("A3")->getFont()->setBold(true);

                        $objSummarySheet->SetCellValue("D3", "=SUM(D5:D$lastDataRow)");
                        $objSummarySheet->SetCellValue("E3", "=SUM(E5:E$lastDataRow)");
                        $objSummarySheet->SetCellValue("F3", "=SUM(F5:F$lastDataRow)");
                        $objSummarySheet->getStyle("D3:F3")->applyFromArray($styleArray);

                        $objSummarySheet->getStyle("D3:F3")->getFont()->setBold(true);
                        $objSummarySheet->getStyle("E3:F3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    }

                    $objSheet->getSheetView()->setZoomScale(95);
                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ReceivedReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
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

    public function generate_exported_report()
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

                $getSawmillContainerData = $this->Sawmill_model->get_sawmill_container_data($this->input->get("originid"));

                if (count($getSawmillContainerData) > 0) {

                    $this->deletefilesfromfolder();

                    $this->excel->setActiveSheetIndex(0);
                    $objContainerSheet = $this->excel->getActiveSheet();
                    $objContainerSheet->setTitle($this->lang->line('detailed_data'));
                    $objContainerSheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );

                    $objContainerSheet->SetCellValue('A3', $this->lang->line('container_number'));
                    $objContainerSheet->SetCellValue('B3', $this->lang->line('pieces'));
                    $objContainerSheet->SetCellValue('C3', $this->lang->line('circumference'));
                    $objContainerSheet->SetCellValue('D3', $this->lang->line('length'));
                    $objContainerSheet->SetCellValue('E3', $this->lang->line('gross_volume'));
                    $objContainerSheet->SetCellValue('F3', $this->lang->line('net_volume'));
                    $objContainerSheet->SetCellValue('G3', $this->lang->line('ica_number'));

                    $objContainerSheet->getStyle("A3:G3")->getFont()->setBold(true);
                    $objContainerSheet->setAutoFilter('A3:G3');
                    $objContainerSheet->getStyle('A3:G3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('add8e6');
                    $objContainerSheet->getStyle("A3:G3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objContainerSheet->getColumnDimension('A')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('B')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('C')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('D')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('E')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('F')->setAutoSize(true);
                    $objContainerSheet->getColumnDimension('G')->setAutoSize(true);

                    $rowContainerData = 4;

                    foreach ($getSawmillContainerData as $containerdata) {

                        $objContainerSheet->SetCellValue("A$rowContainerData", $containerdata->container_number);
                        $objContainerSheet->SetCellValue("B$rowContainerData", $containerdata->dispatch_pieces + 0);
                        $objContainerSheet->SetCellValue("C$rowContainerData", $containerdata->circumference_bought + 0);
                        $objContainerSheet->SetCellValue("D$rowContainerData", $containerdata->length_bought + 0);
                        $objContainerSheet->SetCellValue("E$rowContainerData", $containerdata->gross_volume + 0);
                        $objContainerSheet->SetCellValue("F$rowContainerData", $containerdata->net_volume + 0);
                        $objContainerSheet->SetCellValue("G$rowContainerData", $containerdata->salvoconducto);
                        $objContainerSheet->getStyle("G$rowContainerData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        $rowContainerData++;
                    }

                    $lastRowContainerData = $rowContainerData - 1;

                    $objContainerSheet->getStyle("A3:G$lastRowContainerData")->applyFromArray($styleArray);
                    $objContainerSheet->getStyle("E4:F$lastRowContainerData")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $objContainerSheet->SetCellValue("B2", "=SUM(B4:B$lastRowContainerData)");
                    $objContainerSheet->SetCellValue("E2", "=SUM(E4:E$lastRowContainerData)");
                    $objContainerSheet->SetCellValue("F2", "=SUM(F4:F$lastRowContainerData)");

                    $objContainerSheet->getStyle("B2")->applyFromArray($styleArray);
                    $objContainerSheet->getStyle("E2")->applyFromArray($styleArray);
                    $objContainerSheet->getStyle("F2")->applyFromArray($styleArray);

                    $objContainerSheet->getStyle("B2")->getFont()->setBold(true);
                    $objContainerSheet->getStyle("E2:F2")->getFont()->setBold(true);

                    $objContainerSheet->getStyle("E2:F2")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                    $getSawmillSummaryReport = $this->Sawmill_model->fetch_exported_summary_data_sawmill($this->input->get("originid"));
                    $sheetNo = 0;
                    if (count($getSawmillSummaryReport) > 0) {
                        $sheetNo++;
                        $objSummarySheet = $this->excel->createSheet($sheetNo);
                        $objSummarySheet->setTitle($this->lang->line("report_summary"));
                        $objSummarySheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

                        $objSummarySheet->SetCellValue('A4', $this->lang->line('container_#'));
                        $objSummarySheet->SetCellValue('B4', $this->lang->line('loading_date'));
                        $objSummarySheet->SetCellValue('C4', $this->lang->line('pieces'));
                        $objSummarySheet->SetCellValue('D4', $this->lang->line('gross_volume'));
                        $objSummarySheet->SetCellValue('E4', $this->lang->line('net_volume'));

                        $objSummarySheet->getStyle("A4:E4")->getFont()->setBold(true);
                        $objSummarySheet->setAutoFilter('A4:E4');
                        $objSummarySheet->getStyle('A4:E4')->applyFromArray($styleArray);

                        // HEADER ALIGNMENT
                        $objSummarySheet->getStyle("A4:E4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objSummarySheet->getColumnDimension('A')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('B')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('C')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('D')->setAutoSize(true);
                        $objSummarySheet->getColumnDimension('E')->setAutoSize(true);

                        $rowSummaryCountData = 5;

                        foreach ($getSawmillSummaryReport as $summarydata) {

                            $objSummarySheet->SetCellValue("A$rowSummaryCountData", $summarydata->container_number);
                            $objSummarySheet->getStyle("A$rowSummaryCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $objSummarySheet->SetCellValue("B$rowSummaryCountData", $summarydata->dispatch_date);
                            $objSummarySheet->SetCellValue("C$rowSummaryCountData", $summarydata->dispatch_pieces + 0);
                            $objSummarySheet->SetCellValue("D$rowSummaryCountData", $summarydata->gross_volume + 0);
                            $objSummarySheet->SetCellValue("E$rowSummaryCountData", $summarydata->net_volume + 0);
                            $rowSummaryCountData++;
                        }

                        $lastDataRow = $rowSummaryCountData - 1;
                        $objSummarySheet->getStyle("A5:E$lastDataRow")->applyFromArray($styleArray);
                        $objSummarySheet->getStyle("E3:E$lastDataRow")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');

                        $objSummarySheet->SetCellValue("A3", "=COUNTA(A5:A$lastDataRow)");
                        $objSummarySheet->getStyle("A3")->applyFromArray($styleArray);
                        $objSummarySheet->getStyle("A3")->getFont()->setBold(true);

                        $objSummarySheet->SetCellValue("C3", "=SUM(C5:C$lastDataRow)");
                        $objSummarySheet->SetCellValue("D3", "=SUM(D5:D$lastDataRow)");
                        $objSummarySheet->SetCellValue("E3", "=SUM(E5:E$lastDataRow)");
                        $objSummarySheet->getStyle("C3:E3")->applyFromArray($styleArray);

                        $objSummarySheet->getStyle("D3:E3")->getFont()->setBold(true);
                        $objSummarySheet->getStyle("E3:E3")->getNumberFormat()->setFormatCode('_(* #,##0.000_);_(* (#,##0.000);_(* "-"??_);_(@_)');
                    }

                    $objContainerSheet->getSheetView()->setZoomScale(95);
                    $this->excel->setActiveSheetIndex(0);

                    unset($styleArray);
                    $six_digit_random_number = mt_rand(100000, 999999);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  'ExportedReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save('./reports/ContractReports/' . $filename);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . 'reports/ContractReports/' . $filename;
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

    public function inventory_chart_data()
    {
        $originid = intval($this->input->get("origin_id"));
        $data = $this->Sawmill_model->fetch_inventory_chart_data($originid);

        $categories[] = $this->lang->line("received");  // x-axis labels
        $values[] = $data[0]->total_volume + 0; // y-axis values

        $categories[] = $this->lang->line("processed");  // x-axis labels
        $values[] = $data[0]->processed_volume + 0; // y-axis values

        $categories[] = $this->lang->line("exported");  // x-axis labels
        $values[] = $data[0]->exported_volume + 0; // y-axis values

        $categories[] = $this->lang->line("in_inventory");  // x-axis labels
        $values[] = $data[0]->processed_volume  - $data[0]->exported_volume + 0; // y-axis values


        echo json_encode(['categories' => $categories, 'values' => $values]);
    }

    public function inventory_summary_data()
    {
        $originid = intval($this->input->get("origin_id"));
        $data = $this->Sawmill_model->fetch_inventory_summary_data($originid);
        $dataReceived = $this->Sawmill_model->fetch_farm_summary_data($originid);
        $dataExported = $this->Sawmill_model->fetch_dispatch_summary_data($originid);

        $totalProcessedVolume = $data[0]->processed_volume + 0;
        $totalProcessedCost = $data[0]->processed_cost + 0;
        $totalProcessedPieces = $data[0]->processed_pieces + 0;

        $costPerCbm = 0;
        if ($totalProcessedVolume > 0 & $totalProcessedPieces > 0) {
            $costPerCbm = round($totalProcessedCost / $totalProcessedPieces, 2);
        } else {
            $costPerCbm = 0;
        }

        $totalBins = 0;
        if ($totalProcessedVolume > 0) {
            $totalBins = round($totalProcessedVolume / 28);
        }

        // Format to Colombian Peso
        $formattedCost = '$ ' . number_format($totalProcessedCost, 2, ',', '.');
        $formattedCostPerCbm = '$ ' . number_format($costPerCbm, 2, ',', '.');

        //RECEIVED
        $receivedVolume = $dataReceived[0]->received_volume + 0;
        $receivedPieces = $dataReceived[0]->pieces + 0;
        $receivedICAs = $dataReceived[0]->total_ica + 0;

        //EXPORTED
        $exportedVolume = $dataExported[0]->volume + 0;
        $exportedPieces = $dataExported[0]->pieces + 0;
        $exportedContainers = $dataExported[0]->total_containers + 0;

        //UNPROCESSED
        $unprocessedVolume = ($dataReceived[0]->received_volume - $data[0]->processed_volume) + 0;
        $unprocessedPieces = ($dataReceived[0]->pieces - $data[0]->processed_pieces) + 0;

        echo json_encode([
            'totalProcessedVolume' => number_format($totalProcessedVolume, 3),
            'totalProcessedPieces' => $totalProcessedPieces,
            'totalProcessedCost' => $formattedCost,
            'costPerCbm' => $formattedCostPerCbm,
            'totalBins' => $totalBins,
            'receivedVolume' => number_format($receivedVolume, 3),
            'receivedPieces' => $receivedPieces,
            'receivedICAs' => $receivedICAs,
            'exportedVolume' => number_format($exportedVolume, 3),
            'exportedPieces' => $exportedPieces,
            'exportedContainers' => $exportedContainers,
            'unprocessedVolume' => number_format($unprocessedVolume, 3),
            'unprocessedPieces' => $unprocessedPieces
        ]);
    }

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . "reports/ContractReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
