<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$ci = &get_instance();
		$ci->load->helper('language');
		$this->load->library('session');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('url_helper');
		$this->load->helper('html');
		$this->load->database();
		$this->load->helper('security');
		$this->load->library('form_validation');

		// set default timezone  
		// $system = $this->read_setting_info(1);
		// date_default_timezone_set($system[0]->system_timezone);
		$siteLang = $ci->session->userdata('site_lang');
		$default_language = 'english';
		if ($siteLang) {
            $ci->lang->load('ttkerp',$siteLang);
        } else {
            $ci->lang->load('ttkerp',$default_language);
        } 
		// if ($system[0]->default_language == '') {
		// 	$default_language = 'english';
		// } else {
		// 	$default_language = $system[0]->default_language;
		// }
		// if ($siteLang) {
		// 	$ci->lang->load('hrsale', $siteLang);
		// } else {
		// 	$ci->lang->load('hrsale', $default_language);
		// }
	}
}
