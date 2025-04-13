<?php
class Gpstracker extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('GpsModel');
        // Load the helper for JSON responses
        $this->load->helper('url');
    }

    public function gps_data() {
        // Read the incoming raw POST data
        $jsonData = file_get_contents('php://input');
        
        // Decode the incoming JSON
        $data = json_decode($jsonData, true);
        
        // Check if data is received
        if ($data) {
            // Call model function to insert data into DB
            $this->GpsModel->insert_gps_data($data);
            // Respond with success message
            echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
        } else {
            // Respond with an error if the data is invalid
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        }
    }
}