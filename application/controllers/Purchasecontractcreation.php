<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Purchasecontractcreation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Settings_model");
        $this->load->model("Farm_model");
        $this->load->library("fpdf_master");
    }

    public function generate_purchase_contract()
    {

        $getContractLists = $this->Farm_model->get_contract_list_to_create();

        if (count($getContractLists) > 0) {

            foreach ($getContractLists as $contract) {
                
                if ($contract->is_contract_created == 0) {
                    $this->contract_creation($contract);
                } else {
                    
                    if($contract->is_mail_sent == 0) {
                        if (strlen($contract->contract_link) > 0) {

                        //SEND MAIL
                        $this->send_contract_mail($contract, $contract->contract_link);
                        //END SEND MAIL
                        
                        } else {
                            $this->contract_creation($contract);
                        }
                    }
                }
            }
        }
    }

    public function contract_creation($contract)
    {
        $getContractSequence = $this->Master_model->get_contract_code_sequence($contract->origin_id);
        $contractCode = $getContractSequence[0]->contract_sequences + 1;

        $length = 8;
        if (strlen($contractCode) <= 8) {
            $contractCode = substr(str_repeat(0, $length) . $contractCode, -$length);
        }

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetY(20);
        $pdf->SetX(10);
        $pdf->SetFont("Arial", "", 10);
        $cell = "Cartagena, ";
        $pdf->Cell($pdf->GetStringWidth($cell), 3, $cell, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $boldCell = "$contract->purchase_date";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');

        $pdf->SetY(20);
        $pdf->SetX(140);
        $pdf->SetFont('Arial', '', 10);
        $cell = 'Cuenta de Cobra ';
        $pdf->Cell($pdf->GetStringWidth($cell), 3, $cell, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $boldCell = "# $contractCode";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');

        $pdf->SetY(40);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(100, 0, "TRANSPORTES Y SERVICIOS DE EXPORTACION C.I. S.A.S", 0, 0, "C");

        $pdf->SetY(45);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(100, 0, "Nit. 900.501.419 - 7", 0, 0, "C");

        $pdf->SetY(70);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(100, 0, "DEBE A: $contract->supplier_name", 0, 0, "C");

        $pdf->SetY(75);
        $pdf->SetX(40);
        $pdf->SetFont('Arial', '', 10);
        $cell = 'C.C. ';
        $pdf->Cell($pdf->GetStringWidth($cell), 3, $cell, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $boldCell = "$contract->supplier_id";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');

        $pdf->SetY(75);
        $pdf->SetX(140);
        $pdf->SetFont('Arial', '', 10);
        $cell = 'de ';
        $pdf->Cell($pdf->GetStringWidth($cell), 3, $cell, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $boldCell = "$contract->city";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');

        $amountFormatter = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
        $total_value = ($contract->total_value + 0);
        $amount = $amountFormatter->formatCurrency($total_value, 'COP');
        $amount = str_replace(',00', '', $amount);

        // // $amountFormatter = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
        // // $total_value = ($contract->total_value + 0);
        // // $amount = $amountFormatter->formatCurrency($total_value, 'COP');
        // $amount = str_replace(',00', '', $contract->total_value);

        $pdf->SetY(100);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(100, 0, iconv('UTF-8', 'windows-1252', "LA SUMA DE: $amount"), 0, 0, "C");

        $currencyInWords = "";
        $o = new  Num2Txt();
        list($whole, $decimal) = array_pad(explode(".", 1345000.23), 2, null);
        $wholeWords = $o->toString($whole) . " pesos";
        $decimalWords = "";
        if ($decimal > 0) {
            $decimalWords = $o->toString($decimal) . " centavos";
            $currencyInWords = ucfirst($wholeWords . " con " . $decimalWords);
        } else {
            $currencyInWords = ucfirst($wholeWords);
        }

        $pdf->SetY(108);
        $pdf->SetX(60);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(100, 0,  iconv('UTF-8', 'windows-1252', $currencyInWords), 0, 0, "C");

        $pdf->SetY(125);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  "Por concepto de:", 0, 0, "L");

        $pdf->SetY(135);
        $pdf->SetX(30);
        $pdf->SetFont('Arial', '', 10);
        $cell = chr(127);
        $pdf->Cell($pdf->GetStringWidth($cell), 3, $cell, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $boldCell = "  Venta de Madera Salvoconducto ";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $boldCell = "# $contract->inventory_order";
        $pdf->Cell($pdf->GetStringWidth($boldCell), 3, $boldCell, 0, 'L');

        $pdf->SetY(145);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(100, 0,  "Cordialmente,", 0, 0, "L");

        $pdf->SetY(165);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  "Nombre: $contract->supplier_name", 0, 0, "L");

        $pdf->SetY(170);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  "C.C. $contract->supplier_id", 0, 0, "L");

        $pdf->SetY(175);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  iconv('UTF-8', 'windows-1252', "Teléfono: $contract->contact_no"), 0, 0, "L");

        $pdf->SetY(180);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  iconv('UTF-8', 'windows-1252', "Dirección: $contract->supplier_address"), 0, 0, "L");

        $pdf->SetY(185);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 0,  "E-mail: $contract->email_id", 0, 0, "L");

        $pdf->Image("$contract->consent_url", 70, 145, 0, 0, "PNG", "");

        $filename = "Cuenta_de_Cobro_" . $contract->inventory_order . ".pdf";
        $pdf->Output(FCPATH . "reports/ContractReports/" . $filename, "F");

        $fileContents = $pdf->Output(FCPATH . "reports/ContractReports/" . $filename, "S");

        if (strlen($fileContents) > 0) {

            $dataUpdateContractCode = array(
                "contract_sequences" => $contractCode,
            );

            $updateContractCode = $this->Master_model->update_contract_code_sequence($contract->origin_id, $dataUpdateContractCode);

            $dataContractCreate = array(
                "is_contract_created" => 1, "contract_sequence_id" => $contractCode,
                "contract_link" => $filename,
            );

            $updateContract = $this->Farm_model->update_contract_create($contract->inventory_order, $dataContractCreate);

            //SEND MAIL
            $this->send_contract_mail($contract, $filename);
        }
    }

    public function send_contract_mail($contract, $filename)
    {
        $fileLink = base_url() . "reports/ContractReports/" . $filename;

        $fetchEmailTemplate = $this->Master_model->get_email_template_by_code("PURCHASECONTRACT");

        $mailSubject = $fetchEmailTemplate[0]->template_subject . " " . $contract->inventory_order;
        $logo = base_url() . 'assets/img/iconz/cgrlogo_new.png';

        $message = '<div style="background:#f6f6f6;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;padding: 20px;">
                <img width="74px" src="' . $logo . '" title="Codrin Green"><br>' . str_replace(
            array("{var inventoryorder}", "{var filelink}"),
            array($contract->inventory_order, $fileLink),
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

        $list = array('priyank@codringroup.com', 'leiry@codringroup.com', 'jonathan.batista@codringroup.com', 'nafeel@codringroup.com');
        $this->email->to($list);
        $this->email->from("codrinsystems@codringreen.com", "Codrin Systems");
        $this->email->bcc("nafeel@codringroup.com");
        $this->email->subject($mailSubject);
        $this->email->message("$message");
        $this->email->attach(FCPATH . "reports/ContractReports/" . $filename);
        $resultSend = $this->email->send();
        
        if($resultSend == 1) {
            
            $dataMailSent = array(
                "is_mail_sent" => 1,
            );

            $updateContract = $this->Farm_model->update_contract_create($contract->inventory_order, $dataMailSent);
        }
    }
}

class Num2Txt
{
    private $textoCentenas = ['uno', 'dos', 'tres', 'cuatro', 'quinientos ', 'seis', 'sete', 'ocho', 'nove'];
    private $textoDecenas = ['uno ', 'veinti', 'treinta ', 'cuarenta ', 'cincuenta ', 'sesenta ', 'setenta ', 'ochenta ', 'noventa '];
    private $textoDiezVeinte = ['diez ', 'once ', 'doce ', 'trece ', 'catorce ', 'quince ', 'dieciseis ', 'diecisiete ', 'dieciocho ', 'diecinueve '];
    private $textoUnidades = ['uno ', 'dos ', 'tres ', 'cuatro ', 'cinco ', 'seis ', 'siete ', 'ocho ', 'nueve '];

    public function toString($numero)
    {
        $valor = floatval($numero);
        if (empty($valor)) {
            return 'cero';
        }
        if ($valor > 999999999999.99) {
            return '';
        }

        $texto = '';

        $esNegativo = $valor < 0;
        if ($esNegativo) {
            $texto .= 'menos ';
            $valor = abs($valor);
        }

        $txtNumero = str_pad(number_format($valor, 2, '.', ''), 15, '0', STR_PAD_LEFT);

        for ($contador = 1; $contador < 6; $contador++) {
            switch ($contador) {
                case 1:
                    $modo = 'm';
                    break;
                case 2:
                    $modo = 'k';
                    break;
                case 3:
                    $modo = 'm';
                    break;
                case 4:
                    $modo = 'c';
                    break;
                case 5:
                    $modo = 'u';
                    break;
            }

            $temp = '';

            if ($contador < 5) {
                $posicion = ($contador - 1) * 3;
                if ($posicion + 3 > strlen($txtNumero)) {
                    $longitud = strlen($txtNumero) - $posicion;
                } else {
                    $longitud = 3;
                }

                $temp = substr($txtNumero, ($contador - 1) * 3, $longitud);
                if ($longitud < 3) {
                    $temp = str_pad($temp, 3, '0');
                }

                $numTemp = intval($temp);
                $c1 = substr($temp, 0, 1);
                $c2 = substr($temp, 1, 1);
                $c3 = substr($temp, 2, 1);
                $texto .= $this->centenas($c1, $c2, $c3);
                $texto .= $this->decenas($c2, $c3);
                $texto .= $this->unidades($c1, $c2, $c3, $modo);
            } else {
                $temp = substr($txtNumero, 13, 2);
                if (!empty($temp)) {
                    $numTemp = intval($temp);
                    if (strlen($temp) < 2) {
                        $temp = str_pad($temp, 2, '0');
                    }

                    $c1 = '0';
                    $c2 = substr($temp, 0, 1);
                    $c3 = substr($temp, 1, 1);
                    $texto .= $this->decenas($c2, $c3);
                    $texto .= $this->unidades($c1, $c2, $c3, $modo);
                }
            }

            if (empty($temp)) {
                continue;
            }

            $numTemp = intval($temp);

            if ($contador == 2 && (strlen($texto) != 0 && !$esNegativo || $esNegativo && strlen($texto) > 6)) {
                $texto .= $c3 == '1' && $c2 == '0' && $c1 == '0' ? 'millón ' : 'millones ';
            }

            if (($contador == 1 || $contador == 3) && $numTemp > 0) {
                $texto .= 'mil ';
            }

            if ($contador == 4 && strlen($txtNumero) >= 13) {
                if (!empty(substr($txtNumero, 13)) && intval(substr($txtNumero, 13)) > 0) {
                    if ($txtNumero[9] == '0' && $txtNumero[10] == '0' && $txtNumero[11] == '1') {
                        $texto .= 'o';
                    } else if (strlen($texto) == 0) {
                        $texto .= 'cero ';
                    }
                    $texto .= 'con ';
                }
            }
        }

        return trim($texto);
    }

    private function centenas($centenas, $decenas, $unidades)
    {
        if ($centenas == '0') {
            return '';
        }

        if ($centenas == '1') {
            if ($decenas == '0' && $unidades == '0') {
                return 'cien ';
            } else {
                return 'ciento ';
            }
        }

        $txt = '';
        for ($contador = 0; $contador <= 9; $contador++) {
            if ($centenas == $contador) {
                $indice = intval($contador) - 1;
                $txt .= $this->textoCentenas[$indice];
                break;
            }
        }

        if ($centenas != '5') {
            $txt .= 'cientos ';
        }

        return $txt;
    }

    private function decenas($decenas, $unidades)
    {
        $txt = '';
        $texto = '';

        if ($decenas == '0') {
            return '';
        }

        if ($decenas == '1') {
            for ($contador = 0; $contador <= 9; $contador++) {
                if ($unidades == $contador) {
                    $texto = $this->textoDiezVeinte[$contador];
                    break;
                }
            }
            return $texto;
        }

        for ($contador = 1; $contador <= 9; $contador++) {
            if ($contador == $decenas) {
                break;
            }
        }

        if ($contador > 9) {
            $indice = 9;
        } else {
            $indice = $contador - 1;
        }

        if ($unidades == '0') {
            if ($decenas == '2') {
                $txt = 'veinte ';
            } else {
                $txt = $this->textoDecenas[$indice];
            }

            return $txt;
        }

        if ($decenas != '2') {
            $txt = $this->textoDecenas[$indice] . 'y ';
        } elseif ($unidades != '0') {
            $txt = $this->textoDecenas[$indice];
        }

        return $txt;
    }

    private function unidades($centenas, $decenas, $unidades, $modo)
    {
        if ($unidades == '0' || $decenas == '1') {
            return '';
        }

        if ($unidades == '1') {
            if ($decenas == '0' && $centenas == '0') {
                if ($modo == 'm') {
                    return '';
                }
            }

            if ($modo == 'k') {
                return 'un ';
            }
        }

        for ($contador = 1; $contador <= 9; $contador++) {
            if ($contador == $unidades) {
                break;
            }
        }

        if ($contador > '9') {
            $indice = 9;
        } else {
            $indice = $contador - 1;
        }

        return $this->textoUnidades[$indice];
    }
}