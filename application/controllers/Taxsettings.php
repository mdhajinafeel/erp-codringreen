<?php


 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        ini_set('display_errors', '0');

defined('BASEPATH') or exit('No direct script access allowed');

class Taxsettings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Settings_model");
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("taxsettings_title") . " - " . $this->lang->line("master_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata('fullname');
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_taxsettings";
        if (!empty($session)) {
            $data["subview"] = $this->load->view("masters/taxsettings", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function tax_lists()
    {
        $session = $this->session->userdata("fullname");

        if (empty($session)) {
            redirect("/logout");
        } else {
            $originid = intval($this->input->get("originid"));

            $taxSettings = $this->Master_model->all_taxsettings_origin($originid);

            $data = array();

            foreach ($taxSettings as $r) {

                $editSettings = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="edit" data-tax_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>';

                if ($r->is_active == 1) {
                    $status = $this->lang->line('active');
                } else {
                    $status = $this->lang->line('inactive');
                }

                if ($r->number_format == 1) {
                    $numberformat = "Numeric";
                } else {
                    $numberformat = "Percentage";
                }

                if ($r->arithmetic_type == 1) {
                    $operands = "Addition (+)";
                } else {
                    $operands = "Deduction (-)";
                }

                $enabledrole = array();
                if ($r->is_enabled_supplier == 1) {
                    array_push($enabledrole, "Supplier");
                }

                if ($r->is_enabled_transporter == 1) {
                    array_push($enabledrole, "Transporter");
                }

                $roles = implode(', ', $enabledrole);

                $data[] = array(
                    $editSettings,
                    $r->tax_name,
                    $numberformat,
                    $operands,
                    $roles,
                    $r->origin,
                    $status
                );
            }

            $output = array(
                "data" => $data
            );
            echo json_encode($output);
            exit();
        }
    }

    public function dialog_tax_add()
    {
        $session = $this->session->userdata('fullname');
        $Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

        if ($this->input->get('type') == "addtax") {
            if (!empty($session)) {
                $data = array(
                    'pageheading' => $this->lang->line('add_tax'),
                    'pagetype' => "add",
                    'taxid' => 0,
                    'csrf_hash' => $this->security->get_csrf_hash(),
                );
                $this->load->view('masters/dialog_tax_setting', $data);
            } else {
                $Return['pages'] = "";
                $Return['redirect'] = true;
                $this->output($Return);
            }
        } else if ($this->input->get('type') == "edittax") {

            if (!empty($session)) {

                $getTaxDetails = $this->Master_model->get_supplier_taxes_by_edit($this->input->get('tid'));

                $data = array(
                    'pageheading' => $this->lang->line('edit_tax'),
                    'pagetype' => "edit",
                    'taxid' => $getTaxDetails[0]->id,
                    'csrf_hash' => $this->security->get_csrf_hash(),
                    'get_tax_details' => $getTaxDetails,
                );

                $this->load->view('masters/dialog_tax_setting', $data);
            } else {
                $Return['pages'] = "";
                $Return['redirect'] = true;
                $this->output($Return);
            }
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

        if ($this->input->post('add_type') == 'taxsettings') {

            if (!empty($session)) {

                if ($this->input->post('action_type') == 'add') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $tax_origin = $this->input->post('tax_origin');
                    $tax_name = strtoupper($this->input->post('tax_name'));
                    $number_format = $this->input->post('number_format');
                    $operands = $this->input->post('operands');
                    $enable_supplier = $this->input->post('enable_supplier');
                    $enable_transporter = $this->input->post('enable_transporter');
                    $default_tax_value_supplier = $this->input->post('default_tax_value_supplier');
                    $default_tax_value_transporter = $this->input->post('default_tax_value_transporter');
                    $status_taxsettings = $this->input->post('status_taxsettings');
                    $apply_supplier = $this->input->post('apply_supplier');
                    $apply_transporter = $this->input->post('apply_transporter');
                    $enable_purchase_manager = $this->input->post('enable_purchase_manager');

                    if ($status_taxsettings == 0) {
                        $status_taxsettings = false;
                    } else {
                        $status_taxsettings = true;
                    }

                    if ($enable_supplier == "true") {
                        $enable_supplier = true;
                    } else {
                        $enable_supplier = false;
                    }

                    if ($enable_transporter == "true") {
                        $enable_transporter = true;
                    } else {
                        $enable_transporter = false;
                    }

                    if ($apply_supplier == "true") {
                        $apply_supplier = true;
                    } else {
                        $apply_supplier = false;
                    }

                    if ($apply_transporter == "true") {
                        $apply_transporter = true;
                    } else {
                        $apply_transporter = false;
                    }

                    if($enable_purchase_manager == "true") {
                        $enable_purchase_manager = true;
                    } else {
                        $enable_purchase_manager = false;
                    }

                    $dataTaxes = array(
                        "origin_id" => $tax_origin, "tax_name" => $tax_name,
                        "number_format" => $number_format, "arithmetic_type" => $operands,
                        "is_enabled_supplier" => $enable_supplier, "is_enabled_transporter" => $enable_transporter,
                        "default_tax_value_supplier" => $default_tax_value_supplier, "default_tax_value_provider" => $default_tax_value_transporter,
                        "is_applicable_purchase_manager" => $enable_purchase_manager, "created_by" => $session['user_id'], "updated_by" => $session['user_id'],
                        "is_active" => $status_taxsettings,
                    );

                    $insertTaxes = $this->Master_model->add_tax_settings($dataTaxes);

                    if ($insertTaxes > 0) {

                        //APPLY TO THE SUPPLIERS

                        if ($apply_supplier == true && $enable_supplier == true) {
                            $this->Master_model->tax_apply_suppliers(
                                $insertTaxes,
                                $default_tax_value_supplier,
                                $session['user_id'],
                                $tax_origin,
                                $status_taxsettings
                            );
                        }

                        //END APPLY SUPPLIERS

                        //APPLY TO THE TRANSPORTER

                        if ($apply_transporter == true && $enable_transporter == true) {
                            $this->Master_model->tax_apply_tranporter(
                                $insertTaxes,
                                $default_tax_value_transporter,
                                $session['user_id'],
                                $tax_origin,
                                $status_taxsettings
                            );
                        }

                        //END APPLY TRANSPORTER

                        $Return['result'] = $this->lang->line('data_added');
                        $this->output($Return);
                        exit;
                    } else {
                        $Return['error'] = $this->lang->line('error_adding');
                        $this->output($Return);
                        exit;
                    }
                } else if ($this->input->post('action_type') == 'edit') {

                    $Return['csrf_hash'] = $this->security->get_csrf_hash();

                    $tax_id = $this->input->post('tax_id');
                    $tax_origin = $this->input->post('tax_origin');
                    $tax_name = strtoupper($this->input->post('tax_name'));
                    $number_format = $this->input->post('number_format');
                    $operands = $this->input->post('operands');
                    $enable_supplier = $this->input->post('enable_supplier');
                    $enable_transporter = $this->input->post('enable_transporter');
                    $default_tax_value_supplier = $this->input->post('default_tax_value_supplier');
                    $default_tax_value_transporter = $this->input->post('default_tax_value_transporter');
                    $status_taxsettings = $this->input->post('status_taxsettings');
                    $apply_supplier = $this->input->post('apply_supplier');
                    $apply_transporter = $this->input->post('apply_transporter');
                    $enable_purchase_manager = $this->input->post('enable_purchase_manager');

                    if ($status_taxsettings == 0) {
                        $status_taxsettings = false;
                    } else {
                        $status_taxsettings = true;
                    }

                    if ($enable_supplier == "true") {
                        $enable_supplier = true;
                    } else {
                        $enable_supplier = false;
                    }

                    if ($enable_transporter == "true") {
                        $enable_transporter = true;
                    } else {
                        $enable_transporter = false;
                    }

                    if ($apply_supplier == "true") {
                        $apply_supplier = true;
                    } else {
                        $apply_supplier = false;
                    }

                    if ($apply_transporter == "true") {
                        $apply_transporter = true;
                    } else {
                        $apply_transporter = false;
                    }

                    if($enable_purchase_manager == "true") {
                        $enable_purchase_manager = true;
                    } else {
                        $enable_purchase_manager = false;
                    }

                    $dataTaxes = array(
                        "tax_name" => $tax_name, "number_format" => $number_format, "arithmetic_type" => $operands,
                        "is_enabled_supplier" => $enable_supplier, "is_enabled_transporter" => $enable_transporter,
                        "default_tax_value_supplier" => $default_tax_value_supplier, "default_tax_value_provider" => $default_tax_value_transporter,
                        "is_applicable_purchase_manager" => $enable_purchase_manager, "updated_by" => $session['user_id'],
                        "is_active" => $status_taxsettings,
                    );

                    $updateTaxes = $this->Master_model->update_tax_settings($dataTaxes, $tax_id);

                    if ($updateTaxes == true) {

                        //APPLY TO THE SUPPLIERS

                        if ($this->Master_model->update_apply_suppier($tax_id, $session['user_id'])) {

                            if ($apply_supplier == true && $enable_supplier == true) {
                                $this->Master_model->tax_apply_suppliers(
                                    $tax_id,
                                    $default_tax_value_supplier,
                                    $session['user_id'],
                                    $tax_origin,
                                    $status_taxsettings
                                );
                            }
                        } else {
                            if ($apply_supplier == true && $enable_supplier == true) {
                                $this->Master_model->tax_apply_suppliers(
                                    $tax_id,
                                    $default_tax_value_supplier,
                                    $session['user_id'],
                                    $tax_origin,
                                    $status_taxsettings
                                );
                            }
                        }

                        //END APPLY SUPPLIERS

                        //APPLY TO THE TRANSPORTER

                        if ($this->Master_model->update_apply_transporter($tax_id, $session['user_id'])) {

                            if ($apply_transporter == true && $enable_transporter == true) {
                                $this->Master_model->tax_apply_tranporter(
                                    $tax_id,
                                    $default_tax_value_transporter,
                                    $session['user_id'],
                                    $tax_origin,
                                    $status_taxsettings
                                );
                            }
                        } else {
                            if ($apply_transporter == true && $enable_transporter == true) {
                                $this->Master_model->tax_apply_tranporter(
                                    $tax_id,
                                    $default_tax_value_transporter,
                                    $session['user_id'],
                                    $tax_origin,
                                    $status_taxsettings
                                );
                            }
                        }

                        //END APPLY TRANSPORTER


                        $Return['result'] = $this->lang->line('data_updated');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    } else {
                        $Return['error'] = $this->lang->line('error_updating');
                        $Return['csrf_hash'] = $this->security->get_csrf_hash();
                        $this->output($Return);
                        exit;
                    }
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
