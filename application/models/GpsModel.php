<?php
class GpsModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Function to insert GPS data into the database
    public function insert_gps_data($data) {
        foreach ($data as $record) {
            
            $deviceId = $record['device_id'];
        $timestamp = $record['timestamp'];
        $latitude = $record['latitude'];
        $longitude = $record['longitude'];
        $satellites = $record['satellites'];
        $altitude = $record['altitude'];
        $speed = $record['speed'];
            
            // Extract the data from the "data" field
            //$gpsData = explode(',', $record['data']); // Splitting by comma

            // Ensure there are exactly 4 elements (latitude, longitude, satellites, altitude)
           // if (count($gpsData) == 6) {
                // Prepare data to insert
                $insertData = [
                    'device_id'  => $deviceId,  // Latitude
                    'latitude'  => $latitude,  // Latitude
                    'longitude' => $longitude,  // Longitude
                    'satellites' => $satellites, // Satellites
                    'altitude'  => $altitude,   // Altitude
                    'speed'  => $speed,   // Altitude
                    'captured_at'  => $timestamp,    // Time
                     'created_by' => 1,
                       'updated_by' => 1,
                        'is_active' =>1
                ];
                
                // Insert into the database
                $this->db->insert('tbl_gps_tracking', $insertData);
           // } else {
                // Handle invalid data (optional)
           //     log_message('error', 'Invalid GPS data format: ' . $record['data']);
          //  }
        }
    }
}