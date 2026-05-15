<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Pushnotifications extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('fcm');
    }

    public function output($Return = array())
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($Return));
    }

    public function index()
    {

        $tokens = [
            'dNr-bhZYTg2LrMtKU1UVYr:APA91bFOAkjwxk9mGj8iRUYWZXjdkHzCwlZehngSIhrOB7XyKENKdzZ0lUc2o5yesesXKhOjj5BOfcPPoTCEisEQPqS3c9zqii6wSjKDqPaQgA9ZXfKHpXA'
        ];

        $response = send_fcm_notification_v1(

                $tokens,

                'ERP Test Notification',

                'Firebase HTTP v1 working'
            );

        print_r($response);
    }
}
