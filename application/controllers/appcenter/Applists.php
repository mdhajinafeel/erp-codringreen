<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Applists extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
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
        $data['title'] = $this->lang->line('app_lists') . " | " . $this->lang->line('app_center') . " - " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');

        if (empty($session)) {
            redirect("/logout");
        }

        $data['path_url'] = 'cgr_appcenter';
        if (!empty($session)) {
            $data['subview'] = $this->load->view("appcenter/app_lists", $data, TRUE);
            $this->load->view('layout/layout_main', $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function dialog_appcenter_action() 
    {
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $session = $this->session->userdata('fullname');

        if (!empty($session)) {
            if ($this->input->get('type') == "add_appcenter") {
                $data = array(
                    'pageheading' => $this->lang->line('add_apps'),
                    'pagetype' => "add",
                    'app_id' => 0,
                    'csrfcgr' => $this->security->get_csrf_hash(),
                );

                $this->load->view('appcenter/dialog_add_app', $data);
            }
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }
}