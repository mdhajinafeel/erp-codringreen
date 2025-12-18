<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uploadexpenseattachments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        $this->load->library('jwttoken');
        $this->load->helper('url');
    }

    private function output($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function index()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->output([
                'status' => false,
                'message' => 'Invalid request method'
            ], 405);
        }

        $headers = apache_request_headers();
        foreach ($headers as $header => $value) {
            if ($header == "Authorization") {
                list($a, $b) = explode(" ", $value);
                $requestBearerToken = $b;
            }
        }
        $token = JWT::decode($requestBearerToken, JWT_SECRET);

        $userid   = $token->userid ?? 0;
        $originid = $token->originid ?? 0;
        $roleid   = $token->roleid ?? 0;

        if (!$this->Login_model->check_user_exists($userid, $originid, $roleid)) {
            return $this->output([
                'status' => false,
                'message' => 'Unauthorized user'
            ], 401);
        }

        
        /** ✅ Upload config */
        $uploadPath = FCPATH . 'uploads/expensedocuments/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // ---------- Get file extension ----------
        $ext = pathinfo($_FILES['attachmentFile']['name'], PATHINFO_EXTENSION);

        // ---------- Custom file name ----------
        $customFileName = 'att_' . $userid . '_' . time() . '.' . $ext;

        $config = [
            'upload_path'   => $uploadPath,
            'allowed_types' => '*',
            'file_name'     => $customFileName,
            'overwrite'     => false,
            'max_size'      => 20480 // 20 MB
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('attachmentFile')) {
            return $this->output([
                'status'  => false,
                'message' => $this->upload->display_errors('', '')
            ], 400);
        }

        $fileData = $this->upload->data();

        return $this->output([
            'status' => true,
            'url'    => base_url('uploads/expensedocuments/' . $fileData['file_name'])
        ]);
    }
}