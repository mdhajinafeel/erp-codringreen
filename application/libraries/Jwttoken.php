<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/jwt/JWT.php";

class Jwttoken extends JWT {
	public function __construct() {
		parent::__construct();
	}
}