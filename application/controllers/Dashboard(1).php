<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Settings_model");
		$this->load->model("Dashboard_model");
	}

	public function output($Return = array())
	{
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		exit(json_encode($Return));
	}

	public function index()
	{
		$session = $this->session->userdata('fullname');
		$data['path_url'] = 'cgr_dashboard';
		if (empty($session)) {
			$data['title'] = "Login | " . $this->Settings_model->site_title();
			$this->load->view('auth/login', $data);
			redirect("/logout");
		} else {
			$data['title'] = $this->lang->line('dashboard_title'). " | " . $this->Settings_model->site_title();
			$data['subview'] = $this->load->view('dashboard', $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		}
	}

	public function set_language($language = "")
	{
		$language = ($language != "") ? $language : "english";
		$this->session->set_userdata('site_lang', $language);
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function export_map_details()
	{
		$Return = array('pages' => '', 'redirect' => false, 'mapdata' => '', 'legenddata' => '', 'error' => '', 'csrf_hash' => '');
		
		$exportMapDetails = $this->Dashboard_model->get_export_map_details();
		$returnMapDataArray = array();
		$returnLegendDataArray = array();
		foreach ($exportMapDetails as $r) {
			$volume = ($r->total_volume + 0) . ' '. $this->lang->line('volume_unit');
			$rowMapArrayData['mapdata'] = "$r->pod_name##Total Shipment: $r->total_shipment#Total Containers: $r->total_containers###Total Volume: $volume";
			$rowMapArrayData['latitude'] = ($r->latitude + 0);
			$rowMapArrayData['longitude'] = ($r->longitude + 0);
			$rowMapArrayData['colorcode'] = $r->color_code;
			array_push($returnMapDataArray, $rowMapArrayData);

			$rowLegendArrayData['pod'] = "$r->pod_name";
			$rowLegendArrayData['contribution'] = $r->contribution + 0 ."%";
			$rowLegendArrayData['totalvolume'] = $r->total_volume;
			$rowLegendArrayData['colorcode'] = $r->color_code;
			array_push($returnLegendDataArray, $rowLegendArrayData);
		}

		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		$Return['mapdata'] = $returnMapDataArray;
		$Return['legenddata'] = $returnLegendDataArray;

		$Return['pages'] = "";
		$Return['redirect'] = false;
		$this->output($Return);
	}
}
