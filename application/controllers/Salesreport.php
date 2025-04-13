<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Salesreport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->model("Financemaster_model");
        $this->load->model("Sales_model");
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
        $data["title"] = $this->lang->line("sold_unsold") . " - " . $this->lang->line("sales") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_sales";
        if (!empty($session)) {

            $data["productTypes"] = $this->Master_model->get_product_type();

            $data["csrf_cgrerp"] = $this->security->get_csrf_hash();
            $data["subview"] = $this->load->view("sales/soldunsold", $data, TRUE);
            $this->load->view("layout/layout_main", $data);
        } else {
            redirect("/logout");
        }
    }

    public function fetch_export_sa_number()
    {
        $session = $this->session->userdata("fullname");
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $Return["csrf_hash"] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $getExportSA = $this->Sales_model->get_sa_lists($this->input->get("originid"));

            $Return["result"] = $getExportSA;
            $Return["redirect"] = false;
            $this->output($Return);
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function dialog_upload_sales()
    {
        try {

            $session = $this->session->userdata('fullname');

            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'successmessage' => ''
            );

            if (!empty($session)) {

                $data["pageheading"] = $this->lang->line("upload_sales_data");
                $data["pagetype"] = "excelupload";
                $data["csrf_hash"] = $this->security->get_csrf_hash();
                $data["origin_id"] = $this->input->get("originId");

                $this->load->view("sales/dialog_upload_sales_data", $data);
            } else {
                $Return["error"] = "";
                $Return["result"] = "";
                $Return["redirect"] = true;
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } catch (Exception $e) {
            $Return["error"] = $this->lang->line("error_reports");
            $Return["result"] = "";
            $Return["redirect"] = false;
            $Return["csrf_hash"] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function upload_sales_template_data()
    {
        try {
            $session = $this->session->userdata('fullname');
            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                $originid = $this->input->post('originId');

                if ($_FILES['fileContainerExcel']['size'] > 0) {
                    $config['upload_path'] = FCPATH . 'reports/';
                    $config['allowed_types'] = 'xlsx';
                    $config['remove_spaces'] = TRUE;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('fileContainerExcel')) {
                        $Return['error'] = $this->lang->line('error_excel_upload');
                        $Return['result'] = "";
                        $Return['redirect'] = false;
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    } else {

                        $data = array('upload_data' => $this->upload->data());
                        $inputFileName = FCPATH . 'reports/' . $data['upload_data']['file_name'];
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);

                        if ($arrayCount > 0) {

                            $createArray = array(
                                'YEAR', 'CONTAINERS', 'SA NO', 'BL NO', 'BL DATE', 'LINER', 'ETA',
                                'PRODUCT', 'GROSS LENGTH', 'GROSS GIRTH', 'GROSS VOL', 'PCS', 'NET LENGTH', 'NET GIRTH', 'NET VOL',
                                'BASE PRICE', 'RATE CARD', 'CFT', 'POD', 'CONSIGNEE', 'SALE STATUS', 'SOLD DATE', 'PROFORMA INVOICE DATE',
                                'SALES TERM', 'USANCE', 'TT ADVANCE', 'ADVANCE %', 'SALES PRICE', 'INV. PRICE', 'INT / CBM', 'BANK NEGO VALUE',
                                'CLAIM', 'AOT', 'SALES REMARKS', 'PHYTO DATE', 'COO DATE', 'OBL DATE', 'LC NO',
                                'LC DATE', 'INVOICE NO', 'NEGO STATUS', 'NEGO DATE', 'PAYMENT STATUS', 'EXP ACCEP DATE', 'ACCEP/PAYMENT RECVD DATE', 'REPORT STATUS', 'REMARKS'
                            );

                            $makeArray = array(
                                'YEAR' => 'YEAR', 'CONTAINERS' => 'CONTAINERS', 'SANO' => 'SA NO', 'BLNO' => 'BL NO', 'BLDATE' => 'BL DATE', 'LINER' => 'LINER', 'ETA' => 'ETA',
                                'PRODUCT' => 'PRODUCT', 'GROSSLENGTH' => 'GROSS LENGTH', 'GROSSGIRTH' => 'GROSS GIRTH', 'GROSSVOL' => 'GROSS VOL', 'PCS' => 'PCS',
                                'NETLENGTH' => 'NET LENGTH', 'NETGIRTH' => 'NET GIRTH', 'NETVOL' => 'NET VOL',
                                'BASEPRICE' => 'BASE PRICE', 'RATECARD' => 'RATE CARD', 'CFT' => 'CFT', 'POD' => 'POD', 'CONSIGNEE' => 'CONSIGNEE', 'SALESTATUS' => 'SALE STATUS',
                                'SOLDDATE' => 'SOLD DATE', 'PROFORMAINVOICEDATE' => 'PROFORMA INVOICE DATE',
                                'SALESTERM' => 'SALES TERM', 'USANCE' => 'USANCE', 'TTADVANCE' => 'TT ADVANCE', 'ADVANCE%' => 'ADVANCE %', 'SALESPRICE' => 'SALES PRICE',
                                'INV.PRICE' => 'INV. PRICE', 'INT/CBM' => 'INT / CBM', 'BANKNEGOVALUE' => 'BANK NEGO VALUE',
                                'CLAIM' => 'CLAIM', 'AOT' => 'AOT', 'SALESREMARKS' => 'SALES REMARKS', 'PHYTODATE' => 'PHYTO DATE', 'COODATE' => 'COO DATE', 'OBLDATE' => 'OBL DATE', 'LCNO' => 'LC NO',
                                'LCDATE' => 'LC DATE', 'INVOICENO' => 'INVOICE NO', 'NEGOSTATUS' => 'NEGO STATUS', 'NEGODATE' => 'NEGO DATE', 'PAYMENTSTATUS' => 'PAYMENT STATUS',
                                'EXPACCEPDATE' => 'EXP ACCEP DATE', 'ACCEP/PAYMENTRECVDDATE' => 'ACCEP/PAYMENT RECVD DATE', 'REPORTSTATUS' => 'REPORT STATUS', 'REMARKS' => 'REMARKS'
                            );

                            $SheetDataKey = array();
                            foreach ($allDataInSheet as $dataInSheet) {
                                foreach ($dataInSheet as $key => $value) {

                                    if (in_array(trim($value), $createArray)) {
                                        $value = preg_replace('/\s+/', '', $value);
                                        $SheetDataKey[trim($value)] = $key;
                                    }
                                }
                            }

                            $data = array_diff_key($makeArray, $SheetDataKey);

                            if (empty($data)) {

                                for ($i = 2; $i <= $arrayCount; $i++) {

                                    $year = $SheetDataKey['YEAR'];
                                    $containerNumber = $SheetDataKey['CONTAINERS'];
                                    $referenceSA = $SheetDataKey['SANO'];
                                    $blNo = $SheetDataKey['BLNO'];
                                    $blDate = $SheetDataKey['BLDATE'];
                                    $liner = $SheetDataKey['LINER'];
                                    $etaDate = $SheetDataKey['ETA'];
                                    $product = $SheetDataKey['PRODUCT'];
                                    $grossLength = $SheetDataKey['GROSSLENGTH'];
                                    $grossGirth = $SheetDataKey['GROSSGIRTH'];
                                    $grossVolume = $SheetDataKey['GROSSVOL'];

                                    $pieces = $SheetDataKey['PCS'];
                                    $netLength = $SheetDataKey['NETLENGTH'];
                                    $netGirth = $SheetDataKey['NETGIRTH'];
                                    $netVolume = $SheetDataKey['NETVOL'];
                                    $basePrice = $SheetDataKey['BASEPRICE'];
                                    $rateCard = $SheetDataKey['RATECARD'];
                                    $cft = $SheetDataKey['CFT'];
                                    $pod = $SheetDataKey['POD'];
                                    $consignee = $SheetDataKey['CONSIGNEE'];
                                    $salesStatus = $SheetDataKey['SALESTATUS'];

                                    $soldDate = $SheetDataKey['SOLDDATE'];
                                    $proformaInvoice = $SheetDataKey['PROFORMAINVOICEDATE'];
                                    $salesTerm = $SheetDataKey['SALESTERM'];
                                    $usance = $SheetDataKey['USANCE'];
                                    $ttAdvance = $SheetDataKey['TTADVANCE'];
                                    $advancePercentage = $SheetDataKey['ADVANCE%'];
                                    $salesPrice = $SheetDataKey['SALESPRICE'];
                                    $invoicePrice = $SheetDataKey['INV.PRICE'];
                                    $interestCbm = $SheetDataKey['INT/CBM'];
                                    $bankNegoValue = $SheetDataKey['BANKNEGOVALUE'];

                                    $claim = $SheetDataKey['CLAIM'];
                                    $aot = $SheetDataKey['AOT'];
                                    $salesRemarks = $SheetDataKey['SALESREMARKS'];
                                    $phytoDate = $SheetDataKey['PHYTODATE'];
                                    $cooDate = $SheetDataKey['COODATE'];
                                    $oblDate = $SheetDataKey['OBLDATE'];
                                    $lcNo = $SheetDataKey['LCNO'];
                                    $lcDate = $SheetDataKey['LCDATE'];
                                    $invoiceNo = $SheetDataKey['INVOICENO'];
                                    $negoStatus = $SheetDataKey['NEGOSTATUS'];

                                    $negoDate = $SheetDataKey['NEGODATE'];
                                    $paymentStatus = $SheetDataKey['PAYMENTSTATUS'];
                                    $acceptanceDate = $SheetDataKey['EXPACCEPDATE'];
                                    $receivedDate = $SheetDataKey['ACCEP/PAYMENTRECVDDATE'];
                                    $reportStatus = $SheetDataKey['REPORTSTATUS'];
                                    $remarks = $SheetDataKey['REMARKS'];

                                    $yearVal = filter_var(trim($allDataInSheet[$i][$year]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $containerNumberVal = filter_var(trim($allDataInSheet[$i][$containerNumber]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $referenceSAVal = filter_var(trim($allDataInSheet[$i][$referenceSA]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $blNoVal = filter_var(trim($allDataInSheet[$i][$blNo]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $blDateVal = filter_var(trim($allDataInSheet[$i][$blDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $linerVal = filter_var(trim($allDataInSheet[$i][$liner]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $etaDateVal = filter_var(trim($allDataInSheet[$i][$etaDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $productVal = filter_var(trim($allDataInSheet[$i][$product]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $grossLengthVal = filter_var(trim($allDataInSheet[$i][$grossLength]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $grossGirthVal = filter_var(trim($allDataInSheet[$i][$grossGirth]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $grossVolumeVal = filter_var(trim($allDataInSheet[$i][$grossVolume]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    $piecesVal = filter_var(trim($allDataInSheet[$i][$pieces]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $netLengthVal = filter_var(trim($allDataInSheet[$i][$netLength]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $netGirthVal = filter_var(trim($allDataInSheet[$i][$netGirth]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $netVolumeVal = filter_var(trim($allDataInSheet[$i][$netVolume]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $basePriceVal = filter_var(trim($allDataInSheet[$i][$basePrice]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $rateCardVal = filter_var(trim($allDataInSheet[$i][$rateCard]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $cftVal = filter_var(trim($allDataInSheet[$i][$cft]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $podVal = filter_var(trim($allDataInSheet[$i][$pod]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $consigneeVal = filter_var(trim($allDataInSheet[$i][$consignee]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $salesStatusVal = filter_var(trim($allDataInSheet[$i][$salesStatus]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    $soldDateVal = filter_var(trim($allDataInSheet[$i][$soldDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $proformaInvoiceVal = filter_var(trim($allDataInSheet[$i][$proformaInvoice]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $salesTermVal = filter_var(trim($allDataInSheet[$i][$salesTerm]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $usanceVal = filter_var(trim($allDataInSheet[$i][$usance]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $ttAdvanceVal = filter_var(trim($allDataInSheet[$i][$ttAdvance]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $advancePercentageVal = filter_var(trim($allDataInSheet[$i][$advancePercentage]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $salesPriceVal = filter_var(trim($allDataInSheet[$i][$salesPrice]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $invoicePriceVal = filter_var(trim($allDataInSheet[$i][$invoicePrice]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $interestCbmVal = filter_var(trim($allDataInSheet[$i][$interestCbm]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $bankNegoValueVal = filter_var(trim($allDataInSheet[$i][$bankNegoValue]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    $claimVal = filter_var(trim($allDataInSheet[$i][$claim]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $aotVal = filter_var(trim($allDataInSheet[$i][$aot]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $salesRemarksVal = filter_var(trim($allDataInSheet[$i][$salesRemarks]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $phytoDateVal = filter_var(trim($allDataInSheet[$i][$phytoDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $cooDateVal = filter_var(trim($allDataInSheet[$i][$cooDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $oblDateVal = filter_var(trim($allDataInSheet[$i][$oblDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $lcNoVal = filter_var(trim($allDataInSheet[$i][$lcNo]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $lcDateVal = filter_var(trim($allDataInSheet[$i][$lcDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $invoiceNoVal = filter_var(trim($allDataInSheet[$i][$invoiceNo]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $negoStatusVal = filter_var(trim($allDataInSheet[$i][$negoStatus]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    $negoDateVal = filter_var(trim($allDataInSheet[$i][$negoDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $paymentStatusVal = filter_var(trim($allDataInSheet[$i][$paymentStatus]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $acceptanceDateVal = filter_var(trim($allDataInSheet[$i][$acceptanceDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $receivedDateVal = filter_var(trim($allDataInSheet[$i][$receivedDate]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $reportStatusVal = filter_var(trim($allDataInSheet[$i][$reportStatus]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                    $remarksVal = filter_var(trim($allDataInSheet[$i][$remarks]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                                    //VALID CONTAINER
                                    $getContainersCount = $this->Sales_model->get_count_containers($containerNumberVal, $referenceSAVal, $originid);

                                    if ($getContainersCount[0]->cnt == 1) {

                                        //UPDATE
                                        $dataSalesDataUpdate = array(
                                            "year" => $yearVal, "bl_no" => $blNoVal, "bl_date" => $blDateVal, "liner" => $linerVal, "eta_date" => $etaDateVal, "product" => $productVal,
                                            "gross_length" => $grossLengthVal, "gross_girth" => $grossGirthVal, "gross_volume" => $grossVolumeVal,
                                            "pieces" => $piecesVal, "net_length" => $netLengthVal, "net_girth" => $netGirthVal, "net_volume" => $netVolumeVal,
                                            "base_price" => $basePriceVal, "rate_card" => $rateCardVal, "cft" => $cftVal, "pod" => $podVal, "consignee" => $consigneeVal,
                                            "sales_status" => $salesStatusVal, "sold_date" => $soldDateVal, "proforma_invoice" => $proformaInvoiceVal, "sales_term" => $salesTermVal,
                                            "usance" => $usanceVal, "tt_advance" => $ttAdvanceVal, "advance_percentage" => $advancePercentageVal, "sales_price" => $salesPriceVal,
                                            "invoice_price" => $invoicePriceVal, "interest" => $interestCbmVal, "bank_nego_value" => $bankNegoValueVal, "claim_value" => $claimVal,
                                            "aot_value" => $aotVal, "sales_remarks" => $salesRemarksVal, "phyto_date" => $phytoDateVal, "coo_date" => $cooDateVal,
                                            "obl_date" => $oblDateVal, "lc_no" => $lcNoVal, "lc_date" => $lcDateVal, "invoice_no" => $invoiceNoVal,
                                            "nego_status" => $negoStatusVal, "nego_date" => $negoDateVal, "payment_status" => $paymentStatusVal, "expected_accept_date" => $acceptanceDateVal,
                                            "received_date" => $receivedDateVal, "report_status" => $reportStatusVal, "remarks" => $remarksVal,
                                            "updated_by" => $session['user_id'], "updated_date" => date('Y-m-d H:i:s')
                                        );

                                        $updateData = $this->Sales_model->update_sales_data($containerNumberVal, $referenceSAVal, $originid, $dataSalesDataUpdate);
                                    } else {

                                        //Add
                                        $dataSalesDataAdd = array(
                                            "year" => $yearVal, "container_number" => $containerNumberVal, "sa_no" => $referenceSAVal, "bl_no" => $blNoVal,
                                            "bl_date" => $blDateVal, "liner" => $linerVal, "eta_date" => $etaDateVal, "product" => $productVal,
                                            "gross_length" => $grossLengthVal, "gross_girth" => $grossGirthVal, "gross_volume" => $grossVolumeVal,
                                            "pieces" => $piecesVal, "net_length" => $netLengthVal, "net_girth" => $netGirthVal, "net_volume" => $netVolumeVal,
                                            "base_price" => $basePriceVal, "rate_card" => $rateCardVal, "cft" => $cftVal, "pod" => $podVal, "consignee" => $consigneeVal,
                                            "sales_status" => $salesStatusVal, "sold_date" => $soldDateVal, "proforma_invoice" => $proformaInvoiceVal, "sales_term" => $salesTermVal,
                                            "usance" => $usanceVal, "tt_advance" => $ttAdvanceVal, "advance_percentage" => $advancePercentageVal, "sales_price" => $salesPriceVal,
                                            "invoice_price" => $invoicePriceVal, "interest" => $interestCbmVal, "bank_nego_value" => $bankNegoValueVal, "claim_value" => $claimVal,
                                            "aot_value" => $aotVal, "sales_remarks" => $salesRemarksVal, "phyto_date" => $phytoDateVal, "coo_date" => $cooDateVal,
                                            "obl_date" => $oblDateVal, "lc_no" => $lcNoVal, "lc_date" => $lcDateVal, "invoice_no" => $invoiceNoVal,
                                            "nego_status" => $negoStatusVal, "nego_date" => $negoDateVal, "payment_status" => $paymentStatusVal, "expected_accept_date" => $acceptanceDateVal,
                                            "received_date" => $receivedDateVal, "report_status" => $reportStatusVal, "remarks" => $remarksVal,
                                            "created_by" => $session['user_id'], "updated_by" => $session['user_id'], "is_active" => 1, "origin_id" => $originid,
                                        );

                                        $insertData = $this->Sales_model->add_sales_data($dataSalesDataAdd);
                                    }
                                }

                                $Return['error'] = "";
                                $Return['result'] = $this->lang->line("data_updated");
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            } else {
                                $Return['error'] = $this->lang->line('error_excel_template');
                                $Return['result'] = "";
                                $Return['redirect'] = false;
                                $Return['csrf_hash'] = $this->security->get_csrf_hash();
                                $this->output($Return);
                                exit;
                            }
                        } else {
                            $Return['warning'] = $this->lang->line('error_nodata_excel');
                            $Return['error'] = "";
                            $Return['result'] = "";
                            $Return['redirect'] = false;
                            $Return['csrf_hash'] = $this->security->get_csrf_hash();
                            $this->output($Return);
                            exit;
                        }
                    }
                } else {
                    $Return['error'] = $this->lang->line('error_loadtemplate');
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
            $Return['error'] = $this->lang->line('error_loadtemplate');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_sales_report()
    {

        try {
            $session = $this->session->userdata('fullname');
            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                $applicable_origins = $session["applicable_origins"];

                $isOriginDataAvailable = false;

                $sheetNo = 0;

                foreach ($applicable_origins as $origin) {

                    $fetchSALists = $this->Sales_model->get_sa_lists($origin->id);

                    //START EXCEL

                    if ($sheetNo > 0) {
                        $objSheet = $this->excel->createSheet($sheetNo);
                    } else {
                        $this->excel->setActiveSheetIndex(0);
                        $objSheet = $this->excel->getActiveSheet();
                    }

                    $objSheet->setTitle(strtoupper($origin->origin_name));
                    $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => 'C0C0C0')
                            )
                        )
                    );

                    //HEADING

                    $objSheet->SetCellValue("A3", "CONTAINERS");
                    $objSheet->SetCellValue("B3", "YEAR");
                    $objSheet->SetCellValue("C3", "ORIGIN");
                    $objSheet->SetCellValue("D3", "SA NO");
                    $objSheet->SetCellValue("E3", "BL NO");
                    $objSheet->SetCellValue("F3", "BL DATE");
                    $objSheet->SetCellValue("G3", "SHIPPED MONTH");
                    $objSheet->SetCellValue("H3", "LINER");
                    $objSheet->SetCellValue("I3", "ETA");
                    $objSheet->SetCellValue("J3", "ETA MONTH");
                    $objSheet->SetCellValue("K3", "PRODUCT");
                    $objSheet->SetCellValue("L3", "CONTAINERS");
                    $objSheet->SetCellValue("M3", "GROSS LENGTH");
                    $objSheet->SetCellValue("N3", "GROSS GIRTH");
                    $objSheet->SetCellValue("O3", "GROSS VOL");
                    $objSheet->SetCellValue("P3", "PCS");
                    $objSheet->SetCellValue("Q3", "NET LENGTH");
                    $objSheet->SetCellValue("R3", "NET GIRTH");
                    $objSheet->SetCellValue("S3", "NET VOL");
                    $objSheet->SetCellValue("T3", "BASE PRICE");
                    $objSheet->SetCellValue("U3", "RATE CARD");
                    $objSheet->SetCellValue("V3", "CFT");
                    $objSheet->SetCellValue("W3", "POD");
                    $objSheet->SetCellValue("X3", "CONSIGNEE");
                    $objSheet->SetCellValue("Y3", "SALE STATUS");
                    $objSheet->SetCellValue("Z3", "SOLD DATE");
                    $objSheet->SetCellValue("AA3", "PROFORMA INVOICE DATE");
                    $objSheet->SetCellValue("AB3", "SALES TERM");
                    $objSheet->SetCellValue("AC3", "USANCE");
                    $objSheet->SetCellValue("AD3", "TT ADVANCE");
                    $objSheet->SetCellValue("AE3", "ADVANCE %");
                    $objSheet->SetCellValue("AF3", "SALES PRICE");
                    $objSheet->SetCellValue("AG3", "DIFF");
                    $objSheet->SetCellValue("AH3", "SALES VALUE");
                    $objSheet->SetCellValue("AI3", "INV. PRICE");
                    $objSheet->SetCellValue("AJ3", "INT / CBM");
                    $objSheet->SetCellValue("AK3", "INTEREST");
                    $objSheet->SetCellValue("AL3", "BANK NEGO VALUE");
                    $objSheet->SetCellValue("AM3", "CLAIM");
                    $objSheet->SetCellValue("AN3", "AOT");
                    $objSheet->SetCellValue("AO3", "TOTAL RECEIVABLES");
                    $objSheet->SetCellValue("AP3", "SALES REMARKS");
                    $objSheet->SetCellValue("AQ3", "PHYTO DATE");
                    $objSheet->SetCellValue("AR3", "COO DATE");
                    $objSheet->SetCellValue("AS3", "OBL DATE");
                    $objSheet->SetCellValue("AT3", "LC NO");
                    $objSheet->SetCellValue("AU3", "LC DATE");
                    $objSheet->SetCellValue("AV3", "INVOICE NO");
                    $objSheet->SetCellValue("AW3", "NEGO STATUS");
                    $objSheet->SetCellValue("AX3", "NEGO DATE");
                    $objSheet->SetCellValue("AY3", "PAYMENT STATUS");
                    $objSheet->SetCellValue("AZ3", "EXP ACCEP DATE");
                    $objSheet->SetCellValue("BA3", "ACCEP/PAYMENT RECVD DATE");
                    $objSheet->SetCellValue("BB3", "REPORT STATUS");
                    $objSheet->SetCellValue("BC3", "REMARKS");

                    $objSheet->getStyle("A3:BC3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle("A3:BC3")->getAlignment()->setWrapText(true);

                    $objSheet->getStyle("A3:BC3")->getFont()->setName("Calibri")->setSize(11)->setBold(true);
                    $objSheet->getStyle("A3:BC3")->applyFromArray($styleArray);
                    $objSheet->setAutoFilter("A3:BC3");

                    $objSheet->getColumnDimension("A")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setAutoSize(true);
                    $objSheet->getColumnDimension("B")->setWidth("15");
                    $objSheet->getColumnDimension("C")->setAutoSize(false);
                    $objSheet->getColumnDimension("C")->setWidth("15");
                    $objSheet->getColumnDimension("D")->setAutoSize(false);
                    $objSheet->getColumnDimension("D")->setWidth("15");
                    $objSheet->getColumnDimension("E")->setAutoSize(true);
                    $objSheet->getColumnDimension("F")->setAutoSize(false);
                    $objSheet->getColumnDimension("F")->setWidth("18");
                    $objSheet->getColumnDimension("G")->setAutoSize(true);
                    $objSheet->getColumnDimension("H")->setAutoSize(false);
                    $objSheet->getColumnDimension("H")->setWidth("15");

                    $objSheet->getColumnDimension("I")->setAutoSize(true);
                    $objSheet->getColumnDimension("J")->setAutoSize(false);
                    $objSheet->getColumnDimension("J")->setWidth("20");
                    $objSheet->getColumnDimension("K")->setAutoSize(false);
                    $objSheet->getColumnDimension("K")->setWidth("15");
                    $objSheet->getColumnDimension("L")->setAutoSize(true);
                    $objSheet->getColumnDimension("M")->setAutoSize(false);
                    $objSheet->getColumnDimension("M")->setWidth("18");
                    $objSheet->getColumnDimension("N")->setAutoSize(true);
                    $objSheet->getColumnDimension("O")->setAutoSize(false);
                    $objSheet->getColumnDimension("O")->setWidth("15");
                    $objSheet->getColumnDimension("P")->setAutoSize(false);
                    $objSheet->getColumnDimension("P")->setWidth("15");

                    $objSheet->getColumnDimension("Q")->setAutoSize(false);
                    $objSheet->getColumnDimension("Q")->setWidth("15");
                    $objSheet->getColumnDimension("R")->setAutoSize(false);
                    $objSheet->getColumnDimension("R")->setWidth("15");
                    $objSheet->getColumnDimension("S")->setAutoSize(false);
                    $objSheet->getColumnDimension("S")->setWidth("15");
                    $objSheet->getColumnDimension("T")->setAutoSize(false);
                    $objSheet->getColumnDimension("T")->setWidth("15");
                    $objSheet->getColumnDimension("U")->setAutoSize(true);
                    $objSheet->getColumnDimension("V")->setAutoSize(false);
                    $objSheet->getColumnDimension("V")->setWidth("15");
                    $objSheet->getColumnDimension("W")->setAutoSize(false);
                    $objSheet->getColumnDimension("W")->setWidth("15");

                    $objSheet->getColumnDimension("X")->setAutoSize(false);
                    $objSheet->getColumnDimension("X")->setWidth("40");
                    $objSheet->getColumnDimension("Y")->setAutoSize(false);
                    $objSheet->getColumnDimension("Y")->setWidth("15");
                    $objSheet->getColumnDimension("Z")->setAutoSize(false);
                    $objSheet->getColumnDimension("Z")->setWidth("15");
                    $objSheet->getColumnDimension("AA")->setAutoSize(false);
                    $objSheet->getColumnDimension("AA")->setWidth("15");
                    $objSheet->getColumnDimension("AB")->setAutoSize(false);
                    $objSheet->getColumnDimension("AB")->setWidth("15");

                    $objSheet->getColumnDimension("AC")->setAutoSize(false);
                    $objSheet->getColumnDimension("AC")->setWidth("15");
                    $objSheet->getColumnDimension("AD")->setAutoSize(false);
                    $objSheet->getColumnDimension("AD")->setWidth("15");
                    $objSheet->getColumnDimension("AE")->setAutoSize(false);
                    $objSheet->getColumnDimension("AE")->setWidth("15");
                    $objSheet->getColumnDimension("AF")->setAutoSize(true);
                    $objSheet->getColumnDimension("AG")->setAutoSize(false);
                    $objSheet->getColumnDimension("AG")->setWidth("15");
                    $objSheet->getColumnDimension("AH")->setAutoSize(false);

                    $objSheet->getColumnDimension("AH")->setWidth("15");
                    $objSheet->getColumnDimension("AI")->setAutoSize(false);
                    $objSheet->getColumnDimension("AI")->setWidth("15");
                    $objSheet->getColumnDimension("AJ")->setAutoSize(false);
                    $objSheet->getColumnDimension("AJ")->setWidth("15");
                    $objSheet->getColumnDimension("AK")->setAutoSize(false);
                    $objSheet->getColumnDimension("AK")->setWidth("15");
                    $objSheet->getColumnDimension("AL")->setAutoSize(false);
                    $objSheet->getColumnDimension("AL")->setWidth("15");
                    $objSheet->getColumnDimension("AM")->setAutoSize(false);
                    $objSheet->getColumnDimension("AM")->setWidth("15");

                    $objSheet->getColumnDimension("AN")->setAutoSize(false);
                    $objSheet->getColumnDimension("AN")->setWidth("15");
                    $objSheet->getColumnDimension("AO")->setAutoSize(false);
                    $objSheet->getColumnDimension("AO")->setWidth("15");
                    $objSheet->getColumnDimension("AP")->setAutoSize(false);
                    $objSheet->getColumnDimension("AP")->setWidth("15");
                    $objSheet->getColumnDimension("AQ")->setAutoSize(false);
                    $objSheet->getColumnDimension("AQ")->setWidth("15");

                    $objSheet->getColumnDimension("AR")->setAutoSize(false);
                    $objSheet->getColumnDimension("AR")->setWidth("15");
                    $objSheet->getColumnDimension("AS")->setAutoSize(false);
                    $objSheet->getColumnDimension("AS")->setWidth("15");
                    $objSheet->getColumnDimension("AT")->setAutoSize(false);
                    $objSheet->getColumnDimension("AT")->setWidth("15");
                    $objSheet->getColumnDimension("AU")->setAutoSize(false);
                    $objSheet->getColumnDimension("AU")->setWidth("15");

                    $objSheet->getColumnDimension("AV")->setAutoSize(false);
                    $objSheet->getColumnDimension("AV")->setWidth("15");
                    $objSheet->getColumnDimension("AW")->setAutoSize(false);
                    $objSheet->getColumnDimension("AW")->setWidth("15");
                    $objSheet->getColumnDimension("AX")->setAutoSize(false);
                    $objSheet->getColumnDimension("AX")->setWidth("15");
                    $objSheet->getColumnDimension("AY")->setAutoSize(false);
                    $objSheet->getColumnDimension("AY")->setWidth("15");

                    $objSheet->getColumnDimension("AZ")->setAutoSize(false);
                    $objSheet->getColumnDimension("AZ")->setWidth("15");
                    $objSheet->getColumnDimension("BA")->setAutoSize(false);
                    $objSheet->getColumnDimension("BA")->setWidth("15");
                    $objSheet->getColumnDimension("BB")->setAutoSize(false);
                    $objSheet->getColumnDimension("BB")->setWidth("15");
                    $objSheet->getColumnDimension("BC")->setAutoSize(false);
                    $objSheet->getColumnDimension("BC")->setWidth("15");

                    $objSheet->getStyle("B3:BC3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');

                    //END HEADING

                    if (count($fetchSALists) > 0) {

                        $rowCountData = 3;


                        $isOriginDataAvailable = true;

                        foreach ($fetchSALists as $saNo) {

                            $rowCountData++;

                            if ($rowCountData % 2 == 0) {
                                $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
                            } else {
                                $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');
                            }

                            $fetchSalesReportLists = $this->Sales_model->get_sales_report_lists($origin->id, $saNo->sa_no);

                            if (count($fetchSalesReportLists) > 0) {

                                //DATA FEEDING

                                foreach ($fetchSalesReportLists as $salesReport) {

                                    $rowCountData++;

                                    $objSheet->SetCellValue("A$rowCountData", strtoupper($salesReport->container_number));
                                    $objSheet->SetCellValue("B$rowCountData", $salesReport->year);
                                    $objSheet->SetCellValue("C$rowCountData", strtoupper($salesReport->origin));
                                    $objSheet->SetCellValue("D$rowCountData", strtoupper($salesReport->sa_no));
                                    $objSheet->SetCellValue("E$rowCountData", strtoupper($salesReport->bl_no));

                                    $blDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->bl_date));
                                    if ($blDate == FALSE) {
                                        $objSheet->setCellValue("F$rowCountData", $salesReport->bl_date);
                                        $objSheet->setCellValue("G$rowCountData", $salesReport->bl_date);
                                    } else {
                                        $objSheet->setCellValue("F$rowCountData", $blDate);
                                        $objSheet->getStyle("F$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                                        $objSheet->setCellValue("G$rowCountData", $blDate);
                                        $objSheet->getStyle("G$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17);
                                    }

                                    $objSheet->SetCellValue("H$rowCountData", $salesReport->liner);

                                    $etaDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->eta_date));
                                    if ($etaDate == FALSE) {
                                        $objSheet->SetCellValue("I$rowCountData", $salesReport->eta_date);
                                        $objSheet->SetCellValue("J$rowCountData", $salesReport->eta_date);
                                    } else {
                                        $objSheet->SetCellValue("I$rowCountData", $etaDate);
                                        $objSheet->getStyle("I$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                                        $objSheet->setCellValue("J$rowCountData", $etaDate);
                                        $objSheet->getStyle("J$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17);
                                    }

                                    $objSheet->SetCellValue("K$rowCountData", $salesReport->product);
                                    $objSheet->SetCellValue("L$rowCountData", strtoupper($salesReport->container_number));
                                    $objSheet->SetCellValue("M$rowCountData", $salesReport->gross_length);
                                    $objSheet->SetCellValue("N$rowCountData", $salesReport->gross_girth);
                                    $objSheet->SetCellValue("O$rowCountData", $salesReport->gross_volume);

                                    $objSheet->SetCellValue("P$rowCountData", $salesReport->pieces);
                                    $objSheet->SetCellValue("Q$rowCountData", $salesReport->net_length);
                                    $objSheet->SetCellValue("R$rowCountData", $salesReport->net_girth);
                                    $objSheet->SetCellValue("S$rowCountData", $salesReport->net_volume);
                                    $objSheet->SetCellValue("T$rowCountData", $salesReport->base_price);

                                    $objSheet->SetCellValue("U$rowCountData", $salesReport->rate_card);
                                    $objSheet->SetCellValue("V$rowCountData", $salesReport->cft);
                                    $objSheet->SetCellValue("W$rowCountData", $salesReport->pod);
                                    $objSheet->SetCellValue("X$rowCountData", $salesReport->consignee);
                                    $objSheet->SetCellValue("Y$rowCountData", $salesReport->sales_status);

                                    $soldDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->sold_date));
                                    if ($soldDate == FALSE) {
                                        $objSheet->setCellValue("Z$rowCountData", $salesReport->sold_date);
                                    } else {
                                        $objSheet->setCellValue("Z$rowCountData", $soldDate);
                                        $objSheet->getStyle("Z$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $proformaInvoiceDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->proforma_invoice));
                                    if ($proformaInvoiceDate == FALSE) {
                                        $objSheet->setCellValue("AA$rowCountData", $salesReport->proforma_invoice);
                                    } else {
                                        $objSheet->setCellValue("AA$rowCountData", $proformaInvoiceDate);
                                        $objSheet->getStyle("AA$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $objSheet->SetCellValue("AB$rowCountData", $salesReport->sales_term);
                                    $objSheet->SetCellValue("AC$rowCountData", $salesReport->usance);
                                    $objSheet->SetCellValue("AD$rowCountData", $salesReport->tt_advance);
                                    $objSheet->SetCellValue("AE$rowCountData", $salesReport->advance_percentage);

                                    $objSheet->SetCellValue("AF$rowCountData", $salesReport->sales_price);
                                    $objSheet->SetCellValue("AG$rowCountData", "=AF$rowCountData-U$rowCountData");
                                    $objSheet->SetCellValue("AH$rowCountData", "=AF$rowCountData*S$rowCountData");
                                    $objSheet->SetCellValue("AI$rowCountData", $salesReport->invoice_price);
                                    $objSheet->SetCellValue("AJ$rowCountData", $salesReport->interest);

                                    $objSheet->SetCellValue("AK$rowCountData", "=AJ$rowCountData*S$rowCountData");
                                    $objSheet->SetCellValue("AL$rowCountData", $salesReport->bank_nego_value);
                                    $objSheet->SetCellValue("AM$rowCountData", $salesReport->claim_value);
                                    $objSheet->SetCellValue("AN$rowCountData", $salesReport->aot_value);
                                    $objSheet->SetCellValue("AO$rowCountData", "=AD$rowCountData+AL$rowCountData+AN$rowCountData");

                                    $objSheet->SetCellValue("AP$rowCountData", $salesReport->sales_remarks);

                                    $phytoDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->phyto_date));
                                    if ($phytoDate == FALSE) {
                                        $objSheet->setCellValue("AQ$rowCountData", $salesReport->phyto_date);
                                    } else {
                                        $objSheet->setCellValue("AQ$rowCountData", $phytoDate);
                                        $objSheet->getStyle("AQ$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $cooDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->coo_date));
                                    if ($cooDate == FALSE) {
                                        $objSheet->setCellValue("AR$rowCountData", $salesReport->coo_date);
                                    } else {
                                        $objSheet->setCellValue("AR$rowCountData", $cooDate);
                                        $objSheet->getStyle("AR$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $oblDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->obl_date));
                                    if ($oblDate == FALSE) {
                                        $objSheet->setCellValue("AS$rowCountData", $salesReport->obl_date);
                                    } else {
                                        $objSheet->setCellValue("AS$rowCountData", $oblDate);
                                        $objSheet->getStyle("AS$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $objSheet->SetCellValue("AT$rowCountData", $salesReport->lc_no);

                                    $lcDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->lc_date));
                                    if ($lcDate == FALSE) {
                                        $objSheet->setCellValue("AU$rowCountData", $salesReport->lc_date);
                                    } else {
                                        $objSheet->setCellValue("AU$rowCountData", $lcDate);
                                        $objSheet->getStyle("AU$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $objSheet->SetCellValue("AV$rowCountData", $salesReport->invoice_no);
                                    $objSheet->SetCellValue("AW$rowCountData", $salesReport->nego_status);

                                    $negoDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->nego_date));
                                    if ($negoDate == FALSE) {
                                        $objSheet->setCellValue("AX$rowCountData", $salesReport->nego_date);
                                    } else {
                                        $objSheet->setCellValue("AX$rowCountData", $negoDate);
                                        $objSheet->getStyle("AX$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $objSheet->SetCellValue("AY$rowCountData", $salesReport->payment_status);

                                    $acceptDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->expected_accept_date));
                                    if ($acceptDate == FALSE) {
                                        $objSheet->setCellValue("AZ$rowCountData", $salesReport->expected_accept_date);
                                    } else {
                                        $objSheet->setCellValue("AZ$rowCountData", $acceptDate);
                                        $objSheet->getStyle("AZ$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $receivedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->received_date));
                                    if ($receivedDate == FALSE) {
                                        $objSheet->setCellValue("BA$rowCountData", $salesReport->received_date);
                                    } else {
                                        $objSheet->setCellValue("BA$rowCountData", $receivedDate);
                                        $objSheet->getStyle("BA$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                    }

                                    $objSheet->SetCellValue("BB$rowCountData", $salesReport->report_status);
                                    $objSheet->SetCellValue("BC$rowCountData", $salesReport->remarks);

                                    $objSheet->getStyle("A$rowCountData:BC$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objSheet->getStyle("AP$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objSheet->getStyle("BB$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                    $objSheet->getStyle("BC$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                    if ($rowCountData % 2 == 0) {
                                        $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
                                    } else {
                                        $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');
                                    }
                                }

                                //END DATA FEEDING
                            }
                        }

                        $objSheet->getStyle("M4:N$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("Q4:R$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("P4:P$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                        $objSheet->getStyle("O4:O$rowCountData")->getNumberFormat()->setFormatCode('0.000');
                        $objSheet->getStyle("S4:S$rowCountData")->getNumberFormat()->setFormatCode('0.000');
                        $objSheet->getStyle("T4:T$rowCountData")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("V4:V$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("AB4:AC$rowCountData")->getNumberFormat()->setFormatCode('#,##0;[Red]#,##0');
                        $objSheet->getStyle("AD4:AD$rowCountData")->getNumberFormat()->setFormatCode('#,##0.00');
                        $objSheet->getStyle("AE4:AE$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                        $objSheet->getStyle("AF4:AF$rowCountData")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("AG4:AG$rowCountData")->getNumberFormat()->setFormatCode('0_);[Red](0)');
                        $objSheet->getStyle("AH4:AH$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                        $objSheet->getStyle("AI4:AI$rowCountData")->getNumberFormat()->setFormatCode('0');
                        $objSheet->getStyle("AJ4:AJ$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                        $objSheet->getStyle("AK4:AL$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                        $objSheet->getStyle("AM4:AM$rowCountData")->getNumberFormat()->setFormatCode('#,##0_);(#,##0)');
                        $objSheet->getStyle("AN4:AO$rowCountData")->getNumberFormat()->setFormatCode('#,##0_);[Red](#,##0)');

                        $objSheet->getStyle("A4:BC$rowCountData")->applyFromArray($styleArray);
                    }

                    $objSheet->freezePane("E4");
                    $objSheet->getSheetView()->setZoomScale(95);
                    $sheetNo++;
                }

                if ($isOriginDataAvailable) {

                    $this->excel->setActiveSheetIndex(0);
                    unset($styleArray);
                    $month_name = ucfirst(date("dmY"));

                    $filename =  "Sold Unsold Report_" . $month_name . ".xlsx";

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');

                    $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                    $objWriter->save("./reports/CostSummaryReports/" . $filename);
                    $objWriter->setPreCalculateFormulas(true);
                    $Return['error'] = '';
                    $Return['result'] = site_url() . "reports/CostSummaryReports/" . $filename;
                    $Return['successmessage'] = $this->lang->line('report_downloaded');
                    if ($Return['result'] != '') {
                        $this->output($Return);
                    }
                } else {
                    $Return["error"] = $this->lang->line("no_data_reports");
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
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
            $Return['error'] = $e->getMessage(); //$this->lang->line('error_loadtemplate');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function generate_sa_report()
    {
        try {
            $session = $this->session->userdata('fullname');
            $Return = array(
                'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
                'warning' => '', 'success' => '',
            );

            if (!empty($session)) {

                $originId = $this->input->post('originId');
                $saNumber = $this->input->post('saNumber');
                $saNumberArray = explode(',', $saNumber);

                //START EXCEL

                $this->excel->setActiveSheetIndex(0);
                $objSheet = $this->excel->getActiveSheet();

                $objSheet->setTitle(strtoupper("report"));
                $objSheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => 'C0C0C0')
                        )
                    )
                );

                //HEADING

                $objSheet->SetCellValue("A3", "CONTAINERS");
                $objSheet->SetCellValue("B3", "YEAR");
                $objSheet->SetCellValue("C3", "ORIGIN");
                $objSheet->SetCellValue("D3", "SA NO");
                $objSheet->SetCellValue("E3", "BL NO");
                $objSheet->SetCellValue("F3", "BL DATE");
                $objSheet->SetCellValue("G3", "SHIPPED MONTH");
                $objSheet->SetCellValue("H3", "LINER");
                $objSheet->SetCellValue("I3", "ETA");
                $objSheet->SetCellValue("J3", "ETA MONTH");
                $objSheet->SetCellValue("K3", "PRODUCT");
                $objSheet->SetCellValue("L3", "CONTAINERS");
                $objSheet->SetCellValue("M3", "GROSS LENGTH");
                $objSheet->SetCellValue("N3", "GROSS GIRTH");
                $objSheet->SetCellValue("O3", "GROSS VOL");
                $objSheet->SetCellValue("P3", "PCS");
                $objSheet->SetCellValue("Q3", "NET LENGTH");
                $objSheet->SetCellValue("R3", "NET GIRTH");
                $objSheet->SetCellValue("S3", "NET VOL");
                $objSheet->SetCellValue("T3", "BASE PRICE");
                $objSheet->SetCellValue("U3", "RATE CARD");
                $objSheet->SetCellValue("V3", "CFT");
                $objSheet->SetCellValue("W3", "POD");
                $objSheet->SetCellValue("X3", "CONSIGNEE");
                $objSheet->SetCellValue("Y3", "SALE STATUS");
                $objSheet->SetCellValue("Z3", "SOLD DATE");
                $objSheet->SetCellValue("AA3", "PROFORMA INVOICE DATE");
                $objSheet->SetCellValue("AB3", "SALES TERM");
                $objSheet->SetCellValue("AC3", "USANCE");
                $objSheet->SetCellValue("AD3", "TT ADVANCE");
                $objSheet->SetCellValue("AE3", "ADVANCE %");
                $objSheet->SetCellValue("AF3", "SALES PRICE");
                $objSheet->SetCellValue("AG3", "DIFF");
                $objSheet->SetCellValue("AH3", "SALES VALUE");
                $objSheet->SetCellValue("AI3", "INV. PRICE");
                $objSheet->SetCellValue("AJ3", "INT / CBM");
                $objSheet->SetCellValue("AK3", "INTEREST");
                $objSheet->SetCellValue("AL3", "BANK NEGO VALUE");
                $objSheet->SetCellValue("AM3", "CLAIM");
                $objSheet->SetCellValue("AN3", "AOT");
                $objSheet->SetCellValue("AO3", "TOTAL RECEIVABLES");
                $objSheet->SetCellValue("AP3", "SALES REMARKS");
                $objSheet->SetCellValue("AQ3", "PHYTO DATE");
                $objSheet->SetCellValue("AR3", "COO DATE");
                $objSheet->SetCellValue("AS3", "OBL DATE");
                $objSheet->SetCellValue("AT3", "LC NO");
                $objSheet->SetCellValue("AU3", "LC DATE");
                $objSheet->SetCellValue("AV3", "INVOICE NO");
                $objSheet->SetCellValue("AW3", "NEGO STATUS");
                $objSheet->SetCellValue("AX3", "NEGO DATE");
                $objSheet->SetCellValue("AY3", "PAYMENT STATUS");
                $objSheet->SetCellValue("AZ3", "EXP ACCEP DATE");
                $objSheet->SetCellValue("BA3", "ACCEP/PAYMENT RECVD DATE");
                $objSheet->SetCellValue("BB3", "REPORT STATUS");
                $objSheet->SetCellValue("BC3", "REMARKS");

                $objSheet->getStyle("A3:BC3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle("A3:BC3")->getAlignment()->setWrapText(true);

                $objSheet->getStyle("A3:BC3")->getFont()->setName("Calibri")->setSize(11)->setBold(true);
                $objSheet->getStyle("A3:BC3")->applyFromArray($styleArray);
                $objSheet->setAutoFilter("A3:BC3");

                $objSheet->getColumnDimension("A")->setAutoSize(true);
                $objSheet->getColumnDimension("B")->setAutoSize(true);
                $objSheet->getColumnDimension("B")->setWidth("15");
                $objSheet->getColumnDimension("C")->setAutoSize(false);
                $objSheet->getColumnDimension("C")->setWidth("15");
                $objSheet->getColumnDimension("D")->setAutoSize(false);
                $objSheet->getColumnDimension("D")->setWidth("15");
                $objSheet->getColumnDimension("E")->setAutoSize(true);
                $objSheet->getColumnDimension("F")->setAutoSize(false);
                $objSheet->getColumnDimension("F")->setWidth("18");
                $objSheet->getColumnDimension("G")->setAutoSize(true);
                $objSheet->getColumnDimension("H")->setAutoSize(false);
                $objSheet->getColumnDimension("H")->setWidth("15");

                $objSheet->getColumnDimension("I")->setAutoSize(true);
                $objSheet->getColumnDimension("J")->setAutoSize(false);
                $objSheet->getColumnDimension("J")->setWidth("20");
                $objSheet->getColumnDimension("K")->setAutoSize(false);
                $objSheet->getColumnDimension("K")->setWidth("15");
                $objSheet->getColumnDimension("L")->setAutoSize(true);
                $objSheet->getColumnDimension("M")->setAutoSize(false);
                $objSheet->getColumnDimension("M")->setWidth("18");
                $objSheet->getColumnDimension("N")->setAutoSize(true);
                $objSheet->getColumnDimension("O")->setAutoSize(false);
                $objSheet->getColumnDimension("O")->setWidth("15");
                $objSheet->getColumnDimension("P")->setAutoSize(false);
                $objSheet->getColumnDimension("P")->setWidth("15");

                $objSheet->getColumnDimension("Q")->setAutoSize(false);
                $objSheet->getColumnDimension("Q")->setWidth("15");
                $objSheet->getColumnDimension("R")->setAutoSize(false);
                $objSheet->getColumnDimension("R")->setWidth("15");
                $objSheet->getColumnDimension("S")->setAutoSize(false);
                $objSheet->getColumnDimension("S")->setWidth("15");
                $objSheet->getColumnDimension("T")->setAutoSize(false);
                $objSheet->getColumnDimension("T")->setWidth("15");
                $objSheet->getColumnDimension("U")->setAutoSize(true);
                $objSheet->getColumnDimension("V")->setAutoSize(false);
                $objSheet->getColumnDimension("V")->setWidth("15");
                $objSheet->getColumnDimension("W")->setAutoSize(false);
                $objSheet->getColumnDimension("W")->setWidth("15");

                $objSheet->getColumnDimension("X")->setAutoSize(false);
                $objSheet->getColumnDimension("X")->setWidth("40");
                $objSheet->getColumnDimension("Y")->setAutoSize(false);
                $objSheet->getColumnDimension("Y")->setWidth("15");
                $objSheet->getColumnDimension("Z")->setAutoSize(false);
                $objSheet->getColumnDimension("Z")->setWidth("15");
                $objSheet->getColumnDimension("AA")->setAutoSize(false);
                $objSheet->getColumnDimension("AA")->setWidth("15");
                $objSheet->getColumnDimension("AB")->setAutoSize(false);
                $objSheet->getColumnDimension("AB")->setWidth("15");

                $objSheet->getColumnDimension("AC")->setAutoSize(false);
                $objSheet->getColumnDimension("AC")->setWidth("15");
                $objSheet->getColumnDimension("AD")->setAutoSize(false);
                $objSheet->getColumnDimension("AD")->setWidth("15");
                $objSheet->getColumnDimension("AE")->setAutoSize(false);
                $objSheet->getColumnDimension("AE")->setWidth("15");
                $objSheet->getColumnDimension("AF")->setAutoSize(true);
                $objSheet->getColumnDimension("AG")->setAutoSize(false);
                $objSheet->getColumnDimension("AG")->setWidth("15");
                $objSheet->getColumnDimension("AH")->setAutoSize(false);

                $objSheet->getColumnDimension("AH")->setWidth("15");
                $objSheet->getColumnDimension("AI")->setAutoSize(false);
                $objSheet->getColumnDimension("AI")->setWidth("15");
                $objSheet->getColumnDimension("AJ")->setAutoSize(false);
                $objSheet->getColumnDimension("AJ")->setWidth("15");
                $objSheet->getColumnDimension("AK")->setAutoSize(false);
                $objSheet->getColumnDimension("AK")->setWidth("15");
                $objSheet->getColumnDimension("AL")->setAutoSize(false);
                $objSheet->getColumnDimension("AL")->setWidth("15");
                $objSheet->getColumnDimension("AM")->setAutoSize(false);
                $objSheet->getColumnDimension("AM")->setWidth("15");

                $objSheet->getColumnDimension("AN")->setAutoSize(false);
                $objSheet->getColumnDimension("AN")->setWidth("15");
                $objSheet->getColumnDimension("AO")->setAutoSize(false);
                $objSheet->getColumnDimension("AO")->setWidth("15");
                $objSheet->getColumnDimension("AP")->setAutoSize(false);
                $objSheet->getColumnDimension("AP")->setWidth("15");
                $objSheet->getColumnDimension("AQ")->setAutoSize(false);
                $objSheet->getColumnDimension("AQ")->setWidth("15");

                $objSheet->getColumnDimension("AR")->setAutoSize(false);
                $objSheet->getColumnDimension("AR")->setWidth("15");
                $objSheet->getColumnDimension("AS")->setAutoSize(false);
                $objSheet->getColumnDimension("AS")->setWidth("15");
                $objSheet->getColumnDimension("AT")->setAutoSize(false);
                $objSheet->getColumnDimension("AT")->setWidth("15");
                $objSheet->getColumnDimension("AU")->setAutoSize(false);
                $objSheet->getColumnDimension("AU")->setWidth("15");

                $objSheet->getColumnDimension("AV")->setAutoSize(false);
                $objSheet->getColumnDimension("AV")->setWidth("15");
                $objSheet->getColumnDimension("AW")->setAutoSize(false);
                $objSheet->getColumnDimension("AW")->setWidth("15");
                $objSheet->getColumnDimension("AX")->setAutoSize(false);
                $objSheet->getColumnDimension("AX")->setWidth("15");
                $objSheet->getColumnDimension("AY")->setAutoSize(false);
                $objSheet->getColumnDimension("AY")->setWidth("15");

                $objSheet->getColumnDimension("AZ")->setAutoSize(false);
                $objSheet->getColumnDimension("AZ")->setWidth("15");
                $objSheet->getColumnDimension("BA")->setAutoSize(false);
                $objSheet->getColumnDimension("BA")->setWidth("15");
                $objSheet->getColumnDimension("BB")->setAutoSize(false);
                $objSheet->getColumnDimension("BB")->setWidth("15");
                $objSheet->getColumnDimension("BC")->setAutoSize(false);
                $objSheet->getColumnDimension("BC")->setWidth("15");

                $objSheet->getStyle("B3:BC3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');

                //END HEADING

                    $rowCountData = 3;

                    foreach ($saNumberArray as $saNo) {

                        $rowCountData++;

                        if ($rowCountData % 2 == 0) {
                            $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
                        } else {
                            $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');
                        }

                        $fetchSalesReportLists = $this->Sales_model->get_sales_report_lists($originId, $saNo);

                        if (count($fetchSalesReportLists) > 0) {

                            //DATA FEEDING

                            foreach ($fetchSalesReportLists as $salesReport) {

                                $rowCountData++;

                                $objSheet->SetCellValue("A$rowCountData", strtoupper($salesReport->container_number));
                                $objSheet->SetCellValue("B$rowCountData", $salesReport->year);
                                $objSheet->SetCellValue("C$rowCountData", strtoupper($salesReport->origin));
                                $objSheet->SetCellValue("D$rowCountData", strtoupper($salesReport->sa_no));
                                $objSheet->SetCellValue("E$rowCountData", strtoupper($salesReport->bl_no));

                                $blDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->bl_date));
                                if ($blDate == FALSE) {
                                    $objSheet->setCellValue("F$rowCountData", $salesReport->bl_date);
                                    $objSheet->setCellValue("G$rowCountData", $salesReport->bl_date);
                                } else {
                                    $objSheet->setCellValue("F$rowCountData", $blDate);
                                    $objSheet->getStyle("F$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                                    $objSheet->setCellValue("G$rowCountData", $blDate);
                                    $objSheet->getStyle("G$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17);
                                }

                                $objSheet->SetCellValue("H$rowCountData", $salesReport->liner);

                                $etaDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->eta_date));
                                if ($etaDate == FALSE) {
                                    $objSheet->SetCellValue("I$rowCountData", $salesReport->eta_date);
                                    $objSheet->SetCellValue("J$rowCountData", $salesReport->eta_date);
                                } else {
                                    $objSheet->SetCellValue("I$rowCountData", $etaDate);
                                    $objSheet->getStyle("I$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

                                    $objSheet->setCellValue("J$rowCountData", $etaDate);
                                    $objSheet->getStyle("J$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17);
                                }

                                $objSheet->SetCellValue("K$rowCountData", $salesReport->product);
                                $objSheet->SetCellValue("L$rowCountData", strtoupper($salesReport->container_number));
                                $objSheet->SetCellValue("M$rowCountData", $salesReport->gross_length);
                                $objSheet->SetCellValue("N$rowCountData", $salesReport->gross_girth);
                                $objSheet->SetCellValue("O$rowCountData", $salesReport->gross_volume);

                                $objSheet->SetCellValue("P$rowCountData", $salesReport->pieces);
                                $objSheet->SetCellValue("Q$rowCountData", $salesReport->net_length);
                                $objSheet->SetCellValue("R$rowCountData", $salesReport->net_girth);
                                $objSheet->SetCellValue("S$rowCountData", $salesReport->net_volume);
                                $objSheet->SetCellValue("T$rowCountData", $salesReport->base_price);

                                $objSheet->SetCellValue("U$rowCountData", $salesReport->rate_card);
                                $objSheet->SetCellValue("V$rowCountData", $salesReport->cft);
                                $objSheet->SetCellValue("W$rowCountData", $salesReport->pod);
                                $objSheet->SetCellValue("X$rowCountData", $salesReport->consignee);
                                $objSheet->SetCellValue("Y$rowCountData", $salesReport->sales_status);

                                $soldDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->sold_date));
                                if ($soldDate == FALSE) {
                                    $objSheet->setCellValue("Z$rowCountData", $salesReport->sold_date);
                                } else {
                                    $objSheet->setCellValue("Z$rowCountData", $soldDate);
                                    $objSheet->getStyle("Z$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $proformaInvoiceDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->proforma_invoice));
                                if ($proformaInvoiceDate == FALSE) {
                                    $objSheet->setCellValue("AA$rowCountData", $salesReport->proforma_invoice);
                                } else {
                                    $objSheet->setCellValue("AA$rowCountData", $proformaInvoiceDate);
                                    $objSheet->getStyle("AA$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $objSheet->SetCellValue("AB$rowCountData", $salesReport->sales_term);
                                $objSheet->SetCellValue("AC$rowCountData", $salesReport->usance);
                                $objSheet->SetCellValue("AD$rowCountData", $salesReport->tt_advance);
                                $objSheet->SetCellValue("AE$rowCountData", $salesReport->advance_percentage);

                                $objSheet->SetCellValue("AF$rowCountData", $salesReport->sales_price);
                                $objSheet->SetCellValue("AG$rowCountData", "=AF$rowCountData-U$rowCountData");
                                $objSheet->SetCellValue("AH$rowCountData", "=AF$rowCountData*S$rowCountData");
                                $objSheet->SetCellValue("AI$rowCountData", $salesReport->invoice_price);
                                $objSheet->SetCellValue("AJ$rowCountData", $salesReport->interest);

                                $objSheet->SetCellValue("AK$rowCountData", "=AJ$rowCountData*S$rowCountData");
                                $objSheet->SetCellValue("AL$rowCountData", $salesReport->bank_nego_value);
                                $objSheet->SetCellValue("AM$rowCountData", $salesReport->claim_value);
                                $objSheet->SetCellValue("AN$rowCountData", $salesReport->aot_value);
                                $objSheet->SetCellValue("AO$rowCountData", "=AD$rowCountData+AL$rowCountData+AN$rowCountData");

                                $objSheet->SetCellValue("AP$rowCountData", $salesReport->sales_remarks);

                                $phytoDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->phyto_date));
                                if ($phytoDate == FALSE) {
                                    $objSheet->setCellValue("AQ$rowCountData", $salesReport->phyto_date);
                                } else {
                                    $objSheet->setCellValue("AQ$rowCountData", $phytoDate);
                                    $objSheet->getStyle("AQ$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $cooDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->coo_date));
                                if ($cooDate == FALSE) {
                                    $objSheet->setCellValue("AR$rowCountData", $salesReport->coo_date);
                                } else {
                                    $objSheet->setCellValue("AR$rowCountData", $cooDate);
                                    $objSheet->getStyle("AR$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $oblDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->obl_date));
                                if ($oblDate == FALSE) {
                                    $objSheet->setCellValue("AS$rowCountData", $salesReport->obl_date);
                                } else {
                                    $objSheet->setCellValue("AS$rowCountData", $oblDate);
                                    $objSheet->getStyle("AS$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $objSheet->SetCellValue("AT$rowCountData", $salesReport->lc_no);

                                $lcDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->lc_date));
                                if ($lcDate == FALSE) {
                                    $objSheet->setCellValue("AU$rowCountData", $salesReport->lc_date);
                                } else {
                                    $objSheet->setCellValue("AU$rowCountData", $lcDate);
                                    $objSheet->getStyle("AU$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $objSheet->SetCellValue("AV$rowCountData", $salesReport->invoice_no);
                                $objSheet->SetCellValue("AW$rowCountData", $salesReport->nego_status);

                                $negoDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->nego_date));
                                if ($negoDate == FALSE) {
                                    $objSheet->setCellValue("AX$rowCountData", $salesReport->nego_date);
                                } else {
                                    $objSheet->setCellValue("AX$rowCountData", $negoDate);
                                    $objSheet->getStyle("AX$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $objSheet->SetCellValue("AY$rowCountData", $salesReport->payment_status);

                                $acceptDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->expected_accept_date));
                                if ($acceptDate == FALSE) {
                                    $objSheet->setCellValue("AZ$rowCountData", $salesReport->expected_accept_date);
                                } else {
                                    $objSheet->setCellValue("AZ$rowCountData", $acceptDate);
                                    $objSheet->getStyle("AZ$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $receivedDate = PHPExcel_Shared_Date::PHPToExcel(DateTime::createFromFormat('!d/m/Y', $salesReport->received_date));
                                if ($receivedDate == FALSE) {
                                    $objSheet->setCellValue("BA$rowCountData", $salesReport->received_date);
                                } else {
                                    $objSheet->setCellValue("BA$rowCountData", $receivedDate);
                                    $objSheet->getStyle("BA$rowCountData")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                                }

                                $objSheet->SetCellValue("BB$rowCountData", $salesReport->report_status);
                                $objSheet->SetCellValue("BC$rowCountData", $salesReport->remarks);

                                $objSheet->getStyle("A$rowCountData:BC$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $objSheet->getStyle("AP$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objSheet->getStyle("BB$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                $objSheet->getStyle("BC$rowCountData")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                                if ($rowCountData % 2 == 0) {
                                    $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
                                } else {
                                    $objSheet->getStyle("B$rowCountData:BC$rowCountData")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DBDBDB');
                                }
                            }

                            //END DATA FEEDING
                        }
                    }

                    $objSheet->getStyle("M4:N$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                    $objSheet->getStyle("Q4:R$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                    $objSheet->getStyle("P4:P$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                    $objSheet->getStyle("O4:O$rowCountData")->getNumberFormat()->setFormatCode('0.000');
                    $objSheet->getStyle("S4:S$rowCountData")->getNumberFormat()->setFormatCode('0.000');
                    $objSheet->getStyle("T4:T$rowCountData")->getNumberFormat()->setFormatCode('0');
                    $objSheet->getStyle("V4:V$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                    $objSheet->getStyle("AB4:AC$rowCountData")->getNumberFormat()->setFormatCode('#,##0;[Red]#,##0');
                    $objSheet->getStyle("AD4:AD$rowCountData")->getNumberFormat()->setFormatCode('#,##0.00');
                    $objSheet->getStyle("AE4:AE$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                    $objSheet->getStyle("AF4:AF$rowCountData")->getNumberFormat()->setFormatCode('0');
                    $objSheet->getStyle("AG4:AG$rowCountData")->getNumberFormat()->setFormatCode('0_);[Red](0)');
                    $objSheet->getStyle("AH4:AH$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                    $objSheet->getStyle("AI4:AI$rowCountData")->getNumberFormat()->setFormatCode('0');
                    $objSheet->getStyle("AJ4:AJ$rowCountData")->getNumberFormat()->setFormatCode('0.00');
                    $objSheet->getStyle("AK4:AL$rowCountData")->getNumberFormat()->setFormatCode('#,##0');
                    $objSheet->getStyle("AM4:AM$rowCountData")->getNumberFormat()->setFormatCode('#,##0_);(#,##0)');
                    $objSheet->getStyle("AN4:AO$rowCountData")->getNumberFormat()->setFormatCode('#,##0_);[Red](#,##0)');

                    $objSheet->getStyle("A4:BC$rowCountData")->applyFromArray($styleArray);
                

                $objSheet->freezePane("E4");
                $objSheet->getSheetView()->setZoomScale(95);

                $this->excel->setActiveSheetIndex(0);
                unset($styleArray);
                $month_name = ucfirst(date("dmY"));

                $filename =  "Sold Unsold Report_" . $month_name . ".xlsx";

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
                $objWriter->save("./reports/CostSummaryReports/" . $filename);
                $objWriter->setPreCalculateFormulas(true);
                $Return['error'] = '';
                $Return['result'] = site_url() . "reports/CostSummaryReports/" . $filename;
                $Return['successmessage'] = $this->lang->line('report_downloaded');
                if ($Return['result'] != '') {
                    $this->output($Return);
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
            $Return['error'] = $e->getMessage(); //$this->lang->line('error_loadtemplate');
            $Return['result'] = "";
            $Return['redirect'] = false;
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
            exit;
        }
    }

    public function deletefilesfromfolder()
    {
        $files = glob(FCPATH . 'reports/*.xlsx');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $files = glob(FCPATH . "reports/CostSummaryReports/*.xlsx");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function reformatDate($date, $from_format = 'd/m/Y', $to_format = 'Y-m-d')
    {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }
}