<?php

 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Qrcodegenerator extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
		$this->load->library('fpdf_master');
		$this->load->library('printqrcode');
		$this->load->library('zip');
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
		$data['title'] = $this->lang->line('qrcode_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		$role_resources_ids = explode(',', $session["role_id"]);
		
		if (empty($session)) {
			redirect("/logout");
		}

		if (in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('5', $role_resources_ids) || in_array('6', $role_resources_ids)) {
			redirect("/errorpage");
		} else {
			$data['path_url'] = 'cgr_masters';
			if (!empty($session)) {
				$data['subview'] = $this->load->view("masters/qrcodegenerator", $data, TRUE);
				$this->load->view('layout/layout_main', $data); //page load
			} else {
				redirect("/logout");
			}
		}
	}

	public function qrcode_list()
	{
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$qrcodes = $this->Master_model->all_available_qrcodes();
		} else {
			$qrcodes = $this->Master_model->all_available_qrcodes_origin($originid);
		}
		$data = array();

		foreach ($qrcodes as $r) {

			$editQRCode = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_pdf') . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="download_pdf" data-qrcode_id="' . $r->id . '"><span class="fas fa-file-pdf"></span></button></span>
			<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('download_zip') . '"><button type="button" class="btn icon-btn btn-xs btn-download waves-effect waves-light" data-role="download_zip" data-qrcode_id="' . $r->id . '"><span class="fas fa-file-archive"></span></button></span>';

			$data[] = array(
				$editQRCode,
				$r->qrrange,
				$r->number_of_codes,
				$r->origin,
				$r->created_by
			);
		}

		$output = array(
			"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function getlastgeneratedqrcode()
	{

		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		$originid = intval($this->input->get("originid"));
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$lastQRCode = $this->Master_model->get_last_generated_qrcode($originid);

		if (!empty($session)) {
			$Return['pages'] = "";
			$Return['redirect'] = false;
			$Return['result'] = $lastQRCode[0]->lastcode;
			$this->output($Return);
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function generateqrcode()
	{

		$Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');

		$originid = $this->input->post('originid');

		if ($this->input->post('add_type') == 'qrcode') {

			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$minQrCodeRange = "";
				$maxQrCodeRange = "";

				if ($this->input->post('generatetype') == 1) {

					$noOfQrCodes = $this->input->post('inputqrcode');

					$companysettings = $this->Settings_model->read_company_setting($originid);
					$lastgeneratedqrcode = $this->Master_model->get_last_generated_qrcode_pdf($originid);
					$qr_add = $lastgeneratedqrcode[0]->lastcode;

					if ($qr_add == 0) {
						$qr_add = "202300000000";
					}

					$qrCount = 1;
					$countrow = 1;
					$photocount = 0;
					$isnextPage = false;

					for ($i = 0; $i < $noOfQrCodes; $i++) {

						if ($isnextPage == true) {
							$this->fpdf->AddPage();
							$isnextPage = false;
						}

						if ($qrCount == 1) {
							if ($lastgeneratedqrcode[0]->lastcode == 0) {
								$qr_add = $qr_add + 1;
							} else {
								$qr_add = $lastgeneratedqrcode[0]->lastcode + 1;
							}
						} else {
							$qr_add = $qr_add + 1;
						}

						$image = imagecreatetruecolor(240, 192);
						$srcImg = imagecreatefrompng(FCPATH . "assets/img/iconz/qrlogo.png");
						$background_color = imagecolorallocate($image, 255, 255, 255);
						imagefilledrectangle($image, 1, 1, 238, 190, $background_color);
						$text_color = imagecolorallocate($image, 0, 0, 0);
						imagecopy($image, $srcImg, 25, 60, 0, 0, 70, 70);
						$font = realpath(FCPATH . "assets/fonts/montserrat-bold.ttf");

						$fileName = $qr_add . '.png';
						$pngAbsoluteFilePath = FCPATH . 'assets/qrcode/' . $fileName;
						QRcode::png($qr_add, $pngAbsoluteFilePath, "L", 4, 4);

						$srcImg = imagecreatefrompng(FCPATH . 'assets/qrcode/' . $fileName);
						imagecopy($image, $srcImg, 120, 55, 0, 0, 100, 100);

						$monthname = $this->lang->line(lcfirst(date("F")));
						$year = date("Y");
						$month_year = ucfirst($monthname . ', ' . $year);

						imagettftext($image, 9, 0, 55, 30, $text_color, $font, $this->lang->line("month_name") . " :  " . $month_year);
						imagettftext($image, 9, 0, 55, 50, $text_color, $font, $this->lang->line("numeral") . " :  " . $qr_add);
						if ($companysettings[0]->company_id == null || $companysettings[0]->company_id == "") {
							//DO NOTHING
						} else {
							imagettftext($image, 5, 0, 25, 145, $text_color, $font, $this->lang->line("nit") . " :  " . $companysettings[0]->company_id);
						}


						imagepng($image, FCPATH . 'assets/generatedqrcode/' . $fileName);

						$photocount++;

						if ($countrow == 1) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 2) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 3) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 4) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 65);
							$countrow++;
						} else if ($countrow == 5) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 65);
							$countrow++;
						} else if ($countrow == 6) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 65);
							$countrow++;
						} else if ($countrow == 7) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 120);
							$countrow++;
						} else if ($countrow == 8) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 120);
							$countrow++;
						} else if ($countrow == 9) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 120);
							$countrow++;
						} else if ($countrow == 10) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 175);
							$countrow++;
						} else if ($countrow == 11) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 175);
							$countrow++;
						} else if ($countrow == 12) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 175);
							$countrow++;
						} else if ($countrow == 13) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 230);
							$countrow++;
						} else if ($countrow == 14) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 230);
							$countrow++;
						} else if ($countrow == 15) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 230);
							$countrow = 1;
							$photocount = 0;
							$isnextPage = true;
						}

						if ($qrCount == 1) {
							$minQrCodeRange = $qr_add;
						}

						if ($qrCount == $noOfQrCodes) {
							$maxQrCodeRange = $qr_add;
						}

						$dataScannedCode = array(
							"scannedcode" => $qr_add, "origin_id" => $originid,
							"createdby" => $session['user_id'],
							"updatedby" => $session['user_id'], 'isactive' => 1,
						);

						$insertScannedCode = $this->Master_model->add_scanned_code($dataScannedCode);

						if ($insertScannedCode > 0) {
							$dataQRCodeSequence = array(
								"qr_sequences" => $qr_add,
							);

							$insertQRCodeSequence = $this->Master_model->update_scanned_code($dataQRCodeSequence, $originid);
						}

						$qrCount++;
					}

					$dataScannedCodeFiles = array(
						"origin_id" => $originid, "min_range" => $minQrCodeRange,
						"max_range" => $maxQrCodeRange, "number_of_codes" => $noOfQrCodes,
						"created_by" => $session['user_id'],
						"updated_by" => $session['user_id'], 'is_active' => 1,
					);

					$insertScannedCodeFiles = $this->Master_model->add_scanned_code_files($dataScannedCodeFiles, $originid);

					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY_his")) . "_" . $six_digit_random_number;
					$pdfFileName = "CGR_QRCODES_" . $month_name . ".pdf";

					$this->fpdf->Output(FCPATH . 'assets/generatedpdfqrcode/' . $pdfFileName, 'F');
					$Return['result'] = $this->lang->line('file_downloaded');
					$Return['downloadfile'] = site_url() . 'assets/generatedpdfqrcode/' . $pdfFileName;
					$Return['pages'] = "";
					$Return['redirect'] = false;
					$this->output($Return);
				} else if ($this->input->post('generatetype') == 2) {

					$qrCodeNumber = $this->input->post('inputqrcode');

					$companysettings = $this->Settings_model->read_company_setting($originid);
					$existgeneratedqrcode = $this->Master_model->get_exist_generated_qrcode($originid, $qrCodeNumber);

					if ($existgeneratedqrcode[0]->cnt == 0) {
						$Return['redirect'] = false;
						$Return['pages'] = "";
						$Return['result'] = $this->lang->line('');
						$Return['error'] = $this->lang->line('invalid_qrcode');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
						exit();
					} else {

						$qr_add = $qrCodeNumber;

						$image = imagecreatetruecolor(240, 192);
						$srcImg = imagecreatefrompng(FCPATH . "assets/img/iconz/qrlogo.png");
						$background_color = imagecolorallocate($image, 255, 255, 255);
						imagefilledrectangle($image, 1, 1, 238, 190, $background_color);
						$text_color = imagecolorallocate($image, 0, 0, 0);
						imagecopy($image, $srcImg, 25, 60, 0, 0, 70, 70);
						$font = realpath(FCPATH . "assets/fonts/montserrat-bold.ttf");

						$fileName = $qr_add . '.png';
						$pngAbsoluteFilePath = FCPATH . 'assets/qrcode/' . $fileName;
						QRcode::png($qr_add, $pngAbsoluteFilePath, "L", 4, 4);

						$srcImg = imagecreatefrompng(FCPATH . 'assets/qrcode/' . $fileName);
						imagecopy($image, $srcImg, 120, 55, 0, 0, 100, 100);

						$monthname = $this->lang->line(lcfirst(date("F")));
						$year = date("Y");
						$month_year = ucfirst($monthname . ', ' . $year);

						imagettftext($image, 9, 0, 55, 30, $text_color, $font, $this->lang->line("month_name") . " :  " . $month_year);
						imagettftext($image, 9, 0, 55, 50, $text_color, $font, $this->lang->line("numeral") . " :  " . $qr_add);
						if ($companysettings[0]->company_id == null || $companysettings[0]->company_id == "") {
							//DO NOTHING
						} else {
							imagettftext($image, 5, 0, 25, 145, $text_color, $font, $this->lang->line("nit") . " :  " . $companysettings[0]->company_id);
						}

						imagepng($image, FCPATH . 'assets/generatedqrcode/' . $fileName);
						$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, $this->fpdf->GetY());

						$six_digit_random_number = mt_rand(100000, 999999);
						$month_name = ucfirst(date("dmY_his")) . "_" . $six_digit_random_number;
						$pdfFileName = "CGR_QRCODES_" . $month_name . ".pdf";

						$this->fpdf->Output(FCPATH . 'assets/generatedpdfqrcode/' . $pdfFileName, 'F');
						$Return['result'] = $this->lang->line('file_downloaded');
						$Return['downloadfile'] = site_url() . 'assets/generatedpdfqrcode/' . $pdfFileName;
						$Return['pages'] = "";
						$Return['redirect'] = false;
						$this->output($Return);
						exit();
					}
				}
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->post('add_type') == 'download_pdf') {
			echo $this->input->post('qrcode_id');
		} else {
			$Return['error'] = $this->lang->line('invalid_request');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}

	public function downloadfile()
	{

		$Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');

		$qrcode_id = $this->input->get('qrcode_id');

		if ($this->input->get('type') == 'download_pdf') {

			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$qrcodes = $this->Master_model->get_qr_code_details_origin($qrcode_id);

				if (count($qrcodes) > 0) {

					$minRange = $qrcodes[0]->min_range;
					$maxRange = $qrcodes[0]->max_range;
					$companysettings = $this->Settings_model->read_company_setting($qrcodes[0]->origin_id);

					$qrCount = 1;
					$countrow = 1;
					$photocount = 0;
					$isnextPage = false;

					foreach (range($minRange, $maxRange) as $i) {

						if ($isnextPage == true) {
							$this->fpdf->AddPage();
							$isnextPage = false;
						}

						$image = imagecreatetruecolor(240, 192);
						$srcImg = imagecreatefrompng(FCPATH . "assets/img/iconz/qrlogo.png");
						$background_color = imagecolorallocate($image, 255, 255, 255);
						imagefilledrectangle($image, 1, 1, 238, 190, $background_color);
						$text_color = imagecolorallocate($image, 0, 0, 0);
						imagecopy($image, $srcImg, 25, 60, 0, 0, 70, 70);
						$font = realpath(FCPATH . "assets/fonts/montserrat-bold.ttf");

						$fileName = $i . '.png';
						$pngAbsoluteFilePath = FCPATH . 'assets/qrcode/' . $fileName;
						QRcode::png($i, $pngAbsoluteFilePath, "L", 4, 4);

						$srcImg = imagecreatefrompng(FCPATH . 'assets/qrcode/' . $fileName);
						imagecopy($image, $srcImg, 120, 55, 0, 0, 100, 100);

						$monthname = $this->lang->line(lcfirst(date("F")));
						$year = date("Y");
						$month_year = ucfirst($monthname . ', ' . $year);

						imagettftext($image, 9, 0, 55, 30, $text_color, $font, $this->lang->line("month_name") . " :  " . $month_year);
						imagettftext($image, 9, 0, 55, 50, $text_color, $font, $this->lang->line("numeral") . " :  " . $i);
						if ($companysettings[0]->company_id == null || $companysettings[0]->company_id == "") {
							//DO NOTHING
						} else {
							imagettftext($image, 5, 0, 25, 145, $text_color, $font, $this->lang->line("nit") . " :  " . $companysettings[0]->company_id);
						}

						imagepng($image, FCPATH . 'assets/generatedqrcode/' . $fileName);

						$photocount++;

						if ($countrow == 1) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 2) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 3) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, $this->fpdf->GetY());
							$countrow++;
						} else if ($countrow == 4) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 65);
							$countrow++;
						} else if ($countrow == 5) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 65);
							$countrow++;
						} else if ($countrow == 6) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 65);
							$countrow++;
						} else if ($countrow == 7) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 120);
							$countrow++;
						} else if ($countrow == 8) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 120);
							$countrow++;
						} else if ($countrow == 9) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 120);
							$countrow++;
						} else if ($countrow == 10) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 175);
							$countrow++;
						} else if ($countrow == 11) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 175);
							$countrow++;
						} else if ($countrow == 12) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 175);
							$countrow++;
						} else if ($countrow == 13) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 6, 230);
							$countrow++;
						} else if ($countrow == 14) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 73, 230);
							$countrow++;
						} else if ($countrow == 15) {
							$this->fpdf->Image(FCPATH . 'assets/generatedqrcode/' . $fileName, 140, 230);
							$countrow = 1;
							$photocount = 0;
							$isnextPage = true;
						}

						$qrCount++;
					}

					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY_his")) . "_" . $six_digit_random_number;
					$pdfFileName = "CGR_QRCODES_" . $month_name . ".pdf";

					$this->fpdf->Output(FCPATH . 'assets/generatedpdfqrcode/' . $pdfFileName, 'F');
					$Return['result'] = $this->lang->line('file_downloaded');
					$Return['downloadfile'] = site_url() . 'assets/generatedpdfqrcode/' . $pdfFileName;
					$Return['pages'] = "";
					$Return['redirect'] = false;
					$this->output($Return);
				} else {
					$Return['error'] = "There is an error. Please try again.";
					$Return['redirect'] = false;
					$this->output($Return);
				}
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == 'download_zip') {
			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$qrcodes = $this->Master_model->get_qr_code_details_origin($qrcode_id);

				if (count($qrcodes) > 0) {

					$minRange = $qrcodes[0]->min_range;
					$maxRange = $qrcodes[0]->max_range;
					$companysettings = $this->Settings_model->read_company_setting($qrcodes[0]->origin_id);

					foreach (range($minRange, $maxRange) as $i) {


						$image = imagecreatetruecolor(240, 192);
						$srcImg = imagecreatefrompng(FCPATH . "assets/img/iconz/qrlogo.png");
						$background_color = imagecolorallocate($image, 255, 255, 255);
						imagefilledrectangle($image, 1, 1, 238, 190, $background_color);
						$text_color = imagecolorallocate($image, 0, 0, 0);
						imagecopy($image, $srcImg, 25, 60, 0, 0, 70, 70);
						$font = realpath(FCPATH . "assets/fonts/montserrat-bold.ttf");

						$fileName = $i . '.png';
						$pngAbsoluteFilePath = FCPATH . 'assets/qrcode/' . $fileName;
						QRcode::png($i, $pngAbsoluteFilePath, "L", 4, 4);

						$srcImg = imagecreatefrompng(FCPATH . 'assets/qrcode/' . $fileName);
						imagecopy($image, $srcImg, 120, 55, 0, 0, 100, 100);

						$monthname = $this->lang->line(lcfirst(date("M")));
						$year = date("Y");
						$month_year = ucfirst($monthname . ', ' . $year);

						imagettftext($image, 9, 0, 55, 30, $text_color, $font, $this->lang->line("month_name") . " :  " . $month_year);
						imagettftext($image, 9, 0, 55, 50, $text_color, $font, $this->lang->line("numeral") . " :  " . $i);
						if ($companysettings[0]->company_id == null || $companysettings[0]->company_id == "") {
							//DO NOTHING
						} else {
							imagettftext($image, 5, 0, 25, 145, $text_color, $font, $this->lang->line("nit") . " :  " . $companysettings[0]->company_id);
						}

						imagepng($image, FCPATH . 'assets/generatedqrcode/' . $fileName);
					}

					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY_his")) . "_" . $six_digit_random_number;
					$zipFileName = "CGR_QRCODES_" . $month_name . ".zip";

					foreach (glob(FCPATH . 'assets/generatedqrcode/' . '/*.*') as $file) {
						$this->zip->read_file($file);
					}

					$this->zip->archive(FCPATH . 'assets/generatedpdfqrcode/' . $zipFileName);

					$Return['result'] = $this->lang->line('file_downloaded');
					$Return['downloadfile'] = site_url() . 'assets/generatedpdfqrcode/' . $zipFileName;
					$Return['pages'] = "";
					$Return['redirect'] = false;
					$this->output($Return);
				} else {
					$Return['error'] = "There is an error. Please try again.";
					$Return['redirect'] = false;
					$this->output($Return);
				}
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else {
			$Return['error'] = $this->lang->line('invalid_request');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}

	public function deletefilesfromfolder()
	{
		$files = glob(FCPATH . "assets/generatedpdfqrcode/*");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		$files = glob(FCPATH . "assets/generatedqrcode/*");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		$files = glob(FCPATH . "assets/qrcode/*");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
}
