<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Fpdf_master
{

	public function __construct()
	{

		require_once APPPATH . "third_party/fpdf/fpdf.php";

		$pdf = new FPDF('P', 'mm', 'A4');
		$pdf->AddPage();

		$CI = &get_instance();
		$CI->fpdf = $pdf;
	}
}
