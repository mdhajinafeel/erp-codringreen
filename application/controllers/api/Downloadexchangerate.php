<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Downloadexchangerate extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Exchange_model");
    }

    public function index()
    {
        try {

            $url = "https://www.datos.gov.co/resource/mcec-87by.json";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error when reading the response from API: ' . curl_error($ch);
                return;
            }
            curl_close($ch);

            $json = json_decode($response, true);
            if (!$json) {
                echo "Error when decode JSON.";
                return;
            }

            if ($this->Exchange_model->delete_exchange_rate()) {

                foreach ($json as $item) {
                    $valor = (float)$item['valor'];
                    $unidad = $item['unidad'];
                    $desde = new DateTime(substr($item['vigenciadesde'], 0, 10));
                    $hasta = new DateTime(substr($item['vigenciahasta'], 0, 10));

                    while ($desde <= $hasta) {
                        $fechaStr = $desde->format('Y-m-d');

                        $insertDataExRate = array(
                            "exchange_date" => $fechaStr,
                            "value" => $valor,
                            "created_by" => 0,
                            "updated_by" => 0,
                            "is_active" => 1,
                        );

                        $insertFarm = $this->Exchange_model->add_exchange_rate($insertDataExRate);

                        $desde->modify('+1 day');
                    }
                }

                echo "Exchange rate data downloaded and inserted successfully.";
                return;
            } else {
                echo "Error deleting exchange rate data.";
                return;
            }
        } catch (Exception $e) {
            echo "Error - " . $e->getMessage();
        }
    }
}
