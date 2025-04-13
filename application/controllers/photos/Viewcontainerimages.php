<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Viewcontainerimages extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dispatch_model");
        $this->load->model("Settings_model");
        $this->load->model("Master_model");
        $this->load->library('excel');
        $this->load->library('zip');
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {
        $data["title"] = $this->lang->line("dispatch_title") . " - " . $this->lang->line("inventory_title") .  " | " . $this->Settings_model->site_title();
        if ($this->input->get("did") > 0) {

            $data["dispatchId"] = $this->input->get("did");
            $data["containerNumber"] = $this->Dispatch_model->get_dispatch_detail_bydid($this->input->get("did"))[0]->container_number;
            $data["images"] = $this->Dispatch_model->get_dispatch_photos($this->input->get("did"));
        } else {
            $data["dispatchId"] = 0;
            $data["containerNumber"] = "-";
            $data["images"] = array();
        }

        $this->load->view("dispatches/view_container_images", $data);
    }

    public function download_photos()
    {
        $Return = array(
            "result" => "", "error" => "", "redirect" => false, "csrf_hash" => "", "successmessage" => ""
        );
        if ($this->input->get("did") > 0) {
            $dispatchId = $this->input->get("did");
        } else {
            $dispatchId = 0;
        }

        if ($this->input->get("cnum") != "") {
            $containerNumber = $this->input->get("cnum");
        } else {
            $containerNumber = "";
        }

        $this->zip = new ZipArchive;
        $pathdir = "./uploads/containerimages/";
        $zipcreated = FCPATH . "uploads/downloadedphotos/" . "Photos_" . $containerNumber .".zip";

        foreach (glob("*.zip") as $filename) {
            unlink($filename);
        }

        $dispatchPhotos = $this->Dispatch_model->get_dispatch_photos($dispatchId);
        if (count($dispatchPhotos) > 0) {
            if ($this->zip->open($zipcreated, ZipArchive::CREATE) === TRUE) {
                foreach ($dispatchPhotos as $photo) {
                    $image_url = str_replace('https://portal.codringreen.com/uploads/containerimages/', '', $photo->image_url);
                    if (is_file($pathdir . $image_url)) {
                        $this->zip->addFile($pathdir . $image_url, $image_url);
                    }
                }

                $this->zip->close();
            }


            $Return['result'] = $this->lang->line('file_downloaded');
            $Return['downloadfile'] = site_url() . "uploads/downloadedphotos/" . "Photos_" . $containerNumber .".zip";
            $Return['pages'] = "";
            $Return['redirect'] = false;
            $this->output($Return);
        } else {
            $Return['error'] = "No photos available!!!...";
            $Return['downloadfile'] = "";
            $Return['pages'] = "";
            $Return['redirect'] = false;
        }
    }

    public function deletefilesfromfolder()
	{
		$files = glob(FCPATH . "uploads/downloadedphotos/*");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
}