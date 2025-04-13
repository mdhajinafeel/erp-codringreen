<?php

defined("BASEPATH") or exit("No direct script access allowed");

class Dispatchtracking extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dispatch_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
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
        $data["title"] = $this->lang->line("viewtracking_title") . " - " . $this->lang->line("dispatch_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_dispatchtracking";
        if (!empty($session)) {
            $data["subview"] = $this->load->view("dispatches/dispatch_tracking", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function dispatchtracking_list()
    {
        $session = $this->session->userdata("fullname");

        if (!empty($session)) {
            $originid = intval($this->input->get("originid"));
            $status = intval($this->input->get("status"));

            $receptions = $this->Dispatch_model->fetch_dispatch_by_status($originid, $status);

            $data = array();

            foreach ($receptions as $r) {
                $editReception = "";
                if ($r->isexport == 0) {
                    $editReception = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="edittracking" data-toggle="modal" data-target=".edit-modal-data" data-dispatch_id="' . $r->dispatch_id . '" data-container_number="' . $r->container_number . '"><span class="fas fa-edit"></span></button></span>';
                } else {
                    $editReception = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("exported") . '"><button type="button" class="btn btn-secondary disabled-button" data-role="exported" data-toggle="modal" data-target=".edit-modal-data">Exported</span>';
                }

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $editReception,
                    $r->container_number,
                    $r->shipping_line,
                    $r->dispatch_date,
                    $product,
                    ($r->total_volume + 0),
                    $r->origin,
                    ucwords(strtolower($r->uploadedby)),
                );
            }

            $output = array(
                "data" => $data
            );
            echo json_encode($output);
            exit();
        } else {
            redirect("/logout");
        }
    }

    public function dialog_dispatchtracking_action()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "editdispatchtracking") {
                $dispatchId = $this->input->get("did");
                $containerNumber = $this->input->get("io");

                $data = array(
                    "pageheading" => $this->lang->line("tracking_users"),
                    "pagetype" => "update",
                    "dispatchid" => $dispatchId,
                    "containernumber" => $containerNumber,
                    "openusers" => $this->Dispatch_model->fetch_dispatch_tracking_users($dispatchId, 0),
                    "closedusers" => $this->Dispatch_model->fetch_dispatch_tracking_users($dispatchId, 1),
                    "csrf" => $this->security->get_csrf_hash(),
                );
                $this->load->view("dispatches/dialog_dispatch_tracking", $data);
            }
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function dispatch_update_tracking()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");

        if (!empty($session)) {
            if ($this->input->post("actiontype") == "closedispatch") {

                $userid = $this->input->post("userid");
                $dispatchId = $this->input->post("dispatchid");

                $updateTracking = $this->Dispatch_model->update_dispatch_tracking_users($userid, $dispatchId, 1, $session["user_id"]);
                if ($updateTracking == true) {

                    //CHECK ALL CLOSED DISPATCH
                    $checkClosedDispatch = $this->Dispatch_model->get_dispatch_closed_status($dispatchId);
                    if ($checkClosedDispatch[0]->isclosed == 1) {

                        //SEND MAIL
                        $getDispatchDetail = $this->Dispatch_model->get_dispatch_details_by_id($dispatchId);
                        $fetchEmailTemplate = $this->Master_model->get_email_template_by_code("DISPATCHCLOSE");

                        $mailSubject = $fetchEmailTemplate[0]->template_subject . " ". $getDispatchDetail[0]->container_number;
                        $logo = base_url() . 'assets/img/iconz/cgrlogo_new.png';

                        $woodtype = $this->lang->line($getDispatchDetail[0]->product_type_name);
                        $netvolume = ($getDispatchDetail[0]->total_volume + 0) . " " . $this->lang->line("volume_unit");
                        $message = '<div style="background:#f6f6f6;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;padding: 20px;">
					        <img width="74px" src="' . $logo . '" title="Codrin Green"><br>' . str_replace(
                            array("{var containernumber}", "{var woodspecies}", "{var woodtype}", "{var warehouse}", "{var shippingline}", "{var sealnumber}", "{var totalpieces}", "{var netvolume}", "{var closedby}", "{var origin}"),
                            array(
                                $getDispatchDetail[0]->container_number, $getDispatchDetail[0]->product_name, $woodtype, 
                                $getDispatchDetail[0]->warehouse_name, $getDispatchDetail[0]->shipping_line, $getDispatchDetail[0]->seal_number,
                                $getDispatchDetail[0]->total_pieces, $netvolume, $getDispatchDetail[0]->closedby, $getDispatchDetail[0]->origin
                            ),
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

                        $list = array('priyank@codringroup.com', 'jonathan.batista@codringroup.com', 'nafeel@codringroup.com');
                        $this->email->to($list);
                        $this->email->from("codrinsystems@codringreen.com", "Codrin Systems");
                        $this->email->bcc("nafeel@codringroup.com");
                        $this->email->subject($mailSubject);
                        $this->email->message("$message");
                        $resultSend = $this->email->send();
                    }

                    $Return["result"] = $this->lang->line('data_updated');
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return["error"] = $this->lang->line('error_updating');
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else if ($this->input->post("actiontype") == "opendispatch") {

                $userid = $this->input->post("userid");
                $dispatchId = $this->input->post("dispatchid");

                $updateTracking = $this->Dispatch_model->update_dispatch_tracking_users($userid, $dispatchId, 0, $session["user_id"]);
                if ($updateTracking == true) {
                    $Return["result"] = $this->lang->line('data_updated');
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                } else {
                    $Return["error"] = $this->lang->line('error_updating');
                    $Return["result"] = "";
                    $Return["redirect"] = false;
                    $Return["csrf_hash"] = $this->security->get_csrf_hash();
                    $this->output($Return);
                    exit;
                }
            } else {
                $Return["error"] = $this->lang->line("invalid_request");
                $Return["csrf_hash"] = $this->security->get_csrf_hash();
                $this->output($Return);
                exit;
            }
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }
}
