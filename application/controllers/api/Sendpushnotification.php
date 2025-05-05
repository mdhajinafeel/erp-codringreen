<?php

use PHPUnit\Util\Xml\FailedSchemaDetectionResult;

defined("BASEPATH") or exit("No direct script access allowed");

class Sendpushnotification extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Farm_model");
        $this->load->library("jwttoken");
        $this->load->helper('url');
    }

    public function index()
    {
        try {

            $url = "https://fcm.googleapis.com/fcm/send";

            $serverKey = "AIzaSyDCWdye6WRs9BklS_TI1AO1MnU9H7_Yx6Y";

            $dataPayload = [
                "title" => "Test",
                "body"  => "Test",
                "type"  => "Data Synced"
            ];

            $notificationData = [
                "to" => "dO__H1HsQmuJyFE95wX9o9:APA91bElbwcWXpKnBT6XM-Gvyn9TM5AK79KrYZ0OYZZdhUzGZ3E3c8Ax2syjqjKXEua3qJkwYlv7q-kmR-oDkVs5OwtVmM6NQzfyDsOR6MPkkhQuFw5wn6M",
                "data" => $dataPayload
            ];

            $headers = [
                "Content-Type: application/json"
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notificationData));

            $response = curl_exec($ch);

            if ($response === FALSE) {
                log_message('error', 'FCM Send Error: ' . curl_error($ch));
            } else {
                log_message('info', 'FCM Response: ' . $response);
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}