<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Settings_model");
		$this->load->library('excel');
	}

	public function timezone() {

		$alltimezones = $this->Settings_model->all_timezones();
	
		foreach($alltimezones as $tz) {
			$timezone = timezone_open($tz->timezone_abbreviation);
			$datetime_eur = date_create("now", $timezone);
			if(timezone_offset_get($timezone, $datetime_eur) < 0) {

				$str = '-'. gmdate("H:i", abs(timezone_offset_get($timezone, $datetime_eur)));

				$dataTimeZone = array(
					"timezone_offset" => $str
				);
				$this->Settings_model->update_timezones($dataTimeZone, $tz->id);

				echo $str.'<br/>';
			
			} else {
				$str = gmdate("H:i", abs(timezone_offset_get($timezone, $datetime_eur)));

				$dataTimeZone = array(
					"timezone_offset" => $str
				);

				$this->Settings_model->update_timezones($dataTimeZone, $tz->id);

				echo $str.'<br/>';
			}
			
		}
	}
}
