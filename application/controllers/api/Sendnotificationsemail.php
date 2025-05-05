<?php

use PHPUnit\Util\Xml\FailedSchemaDetectionResult;

defined("BASEPATH") or exit("No direct script access allowed");

class Sendnotificationsemail extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Farm_model");
        $this->load->library("jwttoken");
        $this->load->helper('url');
    }

    public function index()
    {
        try {

            if ($this->input->method(TRUE) == "POST") {

                $requestdata = json_decode(file_get_contents("php://input"), true);
                $farmData = $requestdata["farmData"];

                //SEND MAIL

                $fetchEmailTemplate = $this->Master_model->get_email_template_by_code("FARMDATA");
                $mailSubject = $fetchEmailTemplate[0]->template_subject;
                $logo = base_url() . 'assets/img/iconz/cgrlogo_new_small.png';

                $tableHeadData = "";
                $tableHeadData = $tableHeadData . "<tr>
                    <td width='250' style='border: 1px solid; background: #DEEDF2; font-size: 15px; font-weight: 600; text-align:center !important;'>Supplier</th>
                    <td width='130' style='border: 1px solid; background: #DEEDF2; font-size: 15px; font-weight: 600; text-align:center !important;'>Date</th>
                    <td width='130' style='border: 1px solid; background: #DEEDF2; font-size: 15px; font-weight: 600; text-align:center !important;'>ICA</th>
                    <td width='120' style='border: 1px solid; background: #DEEDF2; font-size: 15px; font-weight: 600; text-align:center !important;'>Pieces</th>
                    <td width='120' style='border: 1px solid; background: #DEEDF2; font-size: 15px; font-weight: 600; text-align:center !important;'>Volume (m³)</th>
                    </tr>";

                $tableRowData = "";
                foreach ($farmData as $key => $value) {

                    $inventoryOrder = $value["inventoryOrder"];
                    $supplierId = $value["supplierId"];

                    //GET FARM DETAILS
                    $getFarmDetails = $this->Farm_model->get_farm_details_byid_supplier($inventoryOrder, $supplierId);

                    if($getFarmDetails[0]->is_closed == 1 && $getFarmDetails[0]->is_notification_sent == 0){

                        $supplierName = $getFarmDetails[0]->supplier_name;
                        $purchaseDate = $getFarmDetails[0]->purchase_date;
                        $ica = $getFarmDetails[0]->inventory_order;
                        $totalPieces = $getFarmDetails[0]->total_pieces;
                        $totalVolume = sprintf("%0.3f", $getFarmDetails[0]->total_volume + 0);
                        
                        $tableRowData = $tableRowData . "<tr>
                            <td style='border: 1px solid; background: #FFFFFF; font-size: 14px; font-weight: 400; text-align:center !important;'>$supplierName</td>
                            <td style='border: 1px solid; background: #FFFFFF; font-size: 14px; font-weight: 400; text-align:center !important;'>$purchaseDate</td>
                            <td style='border: 1px solid; background: #FFFFFF; font-size: 14px; font-weight: 400; text-align:center !important;'>$ica</td>
                            <td style='border: 1px solid; background: #FFFFFF; font-size: 14px; font-weight: 400; text-align:center !important;'>$totalPieces</td>
                            <td style='border: 1px solid; background: #FFFFFF; font-size: 14px; font-weight: 400; text-align:center !important;'>$totalVolume</td>
                            </tr>";
                    }
                }
                
                if($tableRowData != "") {
                    
                    $tableData = '<table style="border: 1px solid black;border-spacing: 0;border-collapse: collapse;">' . $tableHeadData . '
                        ' . $tableRowData . '</table><br/>';
    
                    $message = '<div style="background:#f6f6f6;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;margin:0;padding:0;padding: 20px;">
                        <img src="' . $logo . '" title="Codrin Green"> <br/><br/>' . str_replace(
                        array("{var tabledata}"),
                        array($tableData),
                        htmlspecialchars_decode(stripslashes($fetchEmailTemplate[0]->template_message))
                    ) . '</div>';
    
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'smtp.titan.email',
                        'smtp_port' => 587,
                        'smtp_user' => 'codrinsystems@codringreen.com',
                        'smtp_pass' => "Tb]-(g3Bjh&t[,K5",
                        'mailtype'  => 'html',
                        'charset'   => 'utf-8',
                        'wordwrap' => TRUE
                    );
    
                    $this->load->library('email', $config);
                    $this->email->set_newline("\r\n");
    
                    $listReceivers = array('Raj Shekhar Singh <raj@codringroup.com>');
                    //$listReceivers = array('Mohamed Haji Nafeel <nafeel@codringroup.com>');
                    $this->email->to($listReceivers);
                    $this->email->from("codrinsystems@codringreen.com", "Codrin Systems");
                    $this->email->bcc("Mohamed Haji Nafeel <nafeel@codringroup.com>");
                    $this->email->subject($mailSubject);
                    $this->email->message("$message");
                   
                    if($this->email->send()) {
                        //UPDATE FARM DETAILS
                        foreach ($farmData as $key => $value) {
                            $inventoryOrder = $value["inventoryOrder"];
                            $supplierId = $value["supplierId"];
    
                            $dataFarm = array(
                                "is_notification_sent" => 1,
                                "notification_sent_date" => date("Y-m-d H:i:s"),
                            );
    
                            $this->Farm_model->update_farm_notifications($inventoryOrder, $supplierId, $dataFarm);
                        }
                    }
                }

                //END SEND MAIL
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}