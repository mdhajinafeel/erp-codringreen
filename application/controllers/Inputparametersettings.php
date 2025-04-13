<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inputparametersettings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model');
        $this->load->model("Settings_model");
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
        $data['title'] = $this->lang->line('inputparam_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
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
                $data['subview'] = $this->load->view("masters/inputparametersettings", $data, TRUE);
                $this->load->view('layout/layout_main', $data); //page load
            } else {
                redirect("/logout");
            }
        }
    }

    public function get_product_by_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getProducts = $this->Master_model->get_product_byorigin($this->input->get('originid'));
                foreach ($getProducts as $products) {
                    $result = $result . "<option value='" . $products->product_id . "'>" . $products->product_name . "</option>";
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_product_type_origin()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $result = "<option value='0'>" . $this->lang->line('select') . "</option>";
            if ($this->input->get('originid') > 0) {
                $getProductType = $this->Master_model->get_product_type();
                foreach ($getProductType as $producttype) {
                    $result = $result . "<option value='" . $producttype->type_id . "'>" . $producttype->product_type_name . "</option>";
                }
            }

            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['pages'] = "";
            $Return['redirect'] = true;
            $this->output($Return);
        }
    }

    public function get_input_parameters_by_origin()
    {

        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
        $Return['csrf_hash'] = $this->security->get_csrf_hash();
        if (!empty($session)) {

            $origin_id = $_GET["originid"];
            $product_id = $_GET["productid"];
            $product_type_id = $_GET["producttypeid"];
            $result = "";
            $getInputParameterSettings = $this->Master_model->all_input_parameters_origin($product_type_id, $origin_id);

            $i = 1;
            foreach ($getInputParameterSettings as $inputparameter) {

                $getInputParameterSettingsValue = $this->Master_model->get_input_parameter_settings($product_id, $product_type_id, $inputparameter->input_parameter_id, $origin_id);

                $minrange = "";
                $maxrange = "";
                $isenable = "";
                if (count($getInputParameterSettingsValue) == 1) {
                    $minrange = $getInputParameterSettingsValue[0]->minrange + 0;
                    $maxrange = $getInputParameterSettingsValue[0]->maxrange + 0;

                    if ($getInputParameterSettingsValue[0]->isenable == 1) {
                        $isenable = "checked";
                    }
                }

                $result = $result . "<div class='row mb-3 DataRow'>";
                $result = $result . "<h4 style='padding-left: 15px;'><strong><u>" . $inputparameter->parametername . "</u></strong></h4>";

                $result = $result . "<div class='col-md-3' id='idMinRanges'>";
                $result = $result . "<label for='min_range'>" . $this->lang->line('min_range') . "</label>";
                $result = $result . "<input class='form-control min_range' placeholder='" . $this->lang->line('min_range') . "' name='min_range' id='min_range' type='number' step='any' value='" . $minrange . "'>";
                $result = $result . "</div>";

                $result = $result . "<div class='col-md-3' id='idMaxRanges'>";
                $result = $result . "<label for='max_range'>" . $this->lang->line('max_range') . "</label>";
                $result = $result . "<input class='form-control max_range' placeholder='" . $this->lang->line('max_range') . "' name='max_range' id='max_range' type='number' step='any' value='" . $maxrange . "'>";
                $result = $result . "</div>";

                $result = $result . "<input type='hidden' name='inputParameterName' id='inputParameterName' value ='" . $inputparameter->parametername . "' />";
                $result = $result . "<input type='hidden' name='inputParameterId' id='inputParameterId' value ='" . $inputparameter->input_parameter_id . "' />";
                $result = $result . "<div class='col-md-3 form-check' id='idRangeEnabled' style='display: flex; align-items: center;'>";
                $result = $result . "<input class='form-check-input' id='enablevalidation_" . $i . "' name='enablevalidation_" . $i . "' type='checkbox' value='1' " . $isenable . ">";
                $result = $result . "<label for='enablevalidation_" . $i . "'>" . $this->lang->line('enable_range_validation') . "</label>";
                $result = $result . "</div>";

                $result = $result . "</div>";

                $i++;
            }
            $Return['result'] = $result;
            $Return['redirect'] = false;
            $this->output($Return);
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

        if ($this->input->post('add_type') == 'ip_settings') {

            if (!empty($session)) {

                if ($this->input->post('action_type') == 'add') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $originId = $this->input->post('originId');
                    $productId = $this->input->post('productId');
                    $productTypeId = $this->input->post('productTypeId');
                    $parameterData = json_decode($this->input->post('parameterData'), true);
                    
                    foreach ($parameterData as $parameter) {

                        $enableValidation = 0;

                        if ($parameter['enableValidation'] == true) {
                            $enableValidation = 1;
                        }

                        $getCountIPSettings = $this->Master_model->get_count_ip_settings($productId, $productTypeId, $parameter['inputParameterId'], $originId);

                        if ($getCountIPSettings[0]->cnt == 1) {

                            $dataIPSettings = array(
                                "minrange" => $parameter['minRange'],
                                "maxrange" => $parameter['maxRange'], "isenable" => $enableValidation,
                                "updatedby" => $session['user_id'],
                            );
                            $updateIPSettings = $this->Master_model->update_ip_settings($dataIPSettings, $parameter['inputParameterId'], $productId, $productTypeId, $originId);
                        } else {
                            $dataIPSettings = array(
                                "input_parameter_id" => $parameter['inputParameterId'], "product_id" => $productId,
                                "product_type_id" => $productTypeId, "minrange" => $parameter['minRange'],
                                "maxrange" => $parameter['maxRange'], "isenable" => $enableValidation,
                                "createdby" => $session['user_id'],
                                "updatedby" => $session['user_id'], 'isactive' => 1,
                                'origin_id' => $originId,
                            );

                            $insertIPSettings = $this->Master_model->add_ip_settings($dataIPSettings);
                        }
                    }

                    $Return['result'] = $this->lang->line('data_updated');
                    $this->output($Return);
                    exit;
                } else {
                    $Return['error'] = $this->lang->line('invalid_request');
                    $Return['csrf_hash'] = $this->security->get_csrf_hash();
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
        } else {
            $Return['error'] = $this->lang->line('invalid_request');
            $Return['csrf_hash'] = $this->security->get_csrf_hash();
            $this->output($Return);
        }
    }
}
