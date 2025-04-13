<?php

defined("BASEPATH") or exit("No direct script access allowed");

class Receptiontracking extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Reception_model");
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
        $data["title"] = $this->lang->line("viewtracking_title") . " - " . $this->lang->line("reception_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        $session = $this->session->userdata("fullname");
        if (empty($session)) {
            redirect("/logout");
        }
        $data["path_url"] = "cgr_receptiontracking";
        if (!empty($session)) {
            $data["subview"] = $this->load->view("receptions/reception_tracking", $data, TRUE);
            $this->load->view("layout/layout_main", $data); //page load
        } else {
            redirect("/logout");
        }
    }

    public function receptiontracking_list()
    {
        $session = $this->session->userdata("fullname");

        if (!empty($session)) {
            $originid = intval($this->input->get("originid"));
            $status = intval($this->input->get("status"));

            $receptions = $this->Reception_model->fetch_reception_by_status($originid, $status);

            $data = array();

            foreach ($receptions as $r) {
                $editReception = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line("view") . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="edittracking" data-toggle="modal" data-target=".edit-modal-data" data-reception_id="' . $r->reception_id . '" data-inventory_order="' . $r->salvoconducto . '"><span class="fas fa-edit"></span></button></span>';

                $product = $r->product_name . ' - ' . $this->lang->line($r->product_type_name);

                $data[] = array(
                    $editReception,
                    $r->salvoconducto,
                    $r->supplier_name,
                    $r->received_date,
                    $product,
                    ($r->totalvolume + 0),
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

    public function dialog_receptiontracking_action()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata('fullname');
        if (!empty($session)) {

            if ($this->input->get('type') == "editreceptiontracking") {
                $receptionId = $this->input->get("rid");
                $inventoryOrder = $this->input->get("io");

                $data = array(
                    "pageheading" => $this->lang->line("tracking_users"),
                    "pagetype" => "update",
                    "receptionid" => $receptionId,
                    "inventoryorder" => $inventoryOrder,
                    "openusers" => $this->Reception_model->fetch_reception_tracking_users($receptionId, 0),
                    "closedusers" => $this->Reception_model->fetch_reception_tracking_users($receptionId, 1),
                    "csrf" => $this->security->get_csrf_hash(),
                );
                $this->load->view("receptions/dialog_reception_tracking", $data);
            }
        } else {
            $Return["pages"] = "";
            $Return["redirect"] = true;
            $this->output($Return);
        }
    }

    public function reception_update_tracking()
    {
        $Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
        $session = $this->session->userdata("fullname");

        if (!empty($session)) {
            if ($this->input->post("actiontype") == "closereception") {

                $userid = $this->input->post("userid");
                $receptionId = $this->input->post("receptionid");

                $updateTracking = $this->Reception_model->update_reception_tracking_users($userid, $receptionId, 1, $session["user_id"]);
                if ($updateTracking == true) {

                    //CHECK ALL CLOSED DISPATCH
                    $checkClosedReception = $this->Reception_model->get_reception_closed_status($receptionId);
                    if ($checkClosedReception[0]->isclosed == 1) {

                        //SEND MAIL
                        $getReceptionDetail = $this->Reception_model->get_reception_detail_by_id($receptionId);
                        $fetchEmailTemplate = $this->Master_model->get_email_template_by_code("RECEPTIONCLOSE");

                        $mailSubject = $fetchEmailTemplate[0]->template_subject . " ". $getReceptionDetail[0]->salvoconducto;
                        $logo = base_url() . 'assets/img/iconz/cgrlogo_new.png';

                        $woodtype = $this->lang->line($getReceptionDetail[0]->product_type_name);
                        $netvolume = ($getReceptionDetail[0]->total_volume + 0) . " " . $this->lang->line("volume_unit");
                        $message = '<div style="background:#f6f6f6;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;padding: 20px;">
					        <img width="74px" src="' . $logo . '" title="Codrin Green"><br>' . str_replace(
                            array("{var inventorynumber}", "{var suppliername}", "{var woodspecies}", "{var woodtype}", "{var warehouse}", "{var totalpieces}", "{var netvolume}", "{var closedby}", "{var origin}"),
                            array(
                                $getReceptionDetail[0]->salvoconducto, $getReceptionDetail[0]->supplier_name, $getReceptionDetail[0]->product_name, $woodtype, 
                                $getReceptionDetail[0]->warehouse_name, $getReceptionDetail[0]->total_pieces, $netvolume, 
                                $getReceptionDetail[0]->closedby, $getReceptionDetail[0]->origin
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
            } else if ($this->input->post("actiontype") == "openreception") {

                $userid = $this->input->post("userid");
                $receptionId = $this->input->post("receptionid");

                $updateTracking = $this->Reception_model->update_reception_tracking_users($userid, $receptionId, 0, $session["user_id"]);
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
