<?php
$session = $this->session->userdata('fullname');
$system = $this->Settings_model->read_setting_info(1);

$role_resources_ids = explode(',', $session["role_id"]);
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>

<?php

$language_name = $system[0]->default_language;

if (!empty($wz_lang)) :
	$lang_code = $this->Settings_model->get_language_info($wz_lang);
	$language_name = $wz_lang;
	$flg_icn = $lang_code[0]->language_flag;
	$flg_icn = '<img src="' . base_url() . 'assets/img/iconz/languages/' . $flg_icn . '">';
elseif ($system[0]->default_language != '') :
	$lang_code = $this->Settings_model->get_language_info($system[0]->default_language);
	$flg_icn = $lang_code[0]->language_flag;
	$flg_icn = '<img src="' . base_url() . 'assets/img/iconz/languages/' . $flg_icn . '">';
else :
	$flg_icn = '<img src="' . base_url() . 'assets/img/iconz/languages/english.png">';
endif;
?>
<?php $this->load->view('dialogs/common_dialog'); ?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">


	<!-- ===============================================-->
	<!--    Document Title-->
	<!-- ===============================================-->
	<title><?php echo $title; ?></title>

	<!-- ===============================================-->
	<!--    Favicons-->
	<!-- ===============================================-->

	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-57x57.png'; ?>">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-60x60.png'; ?>">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-72x72.png'; ?>">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-76x76.png'; ?>">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-114x114.png'; ?>">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-120x120.png'; ?>">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-144x144.png'; ?>">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-152x152.png'; ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url() . 'assets/img/faviconz/apple-icon-180x180.png'; ?>">
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo base_url() . 'assets/img/faviconz/android-icon-192x192.png'; ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url() . 'assets/img/faviconz/favicon-32x32.png'; ?>">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url() . 'assets/img/faviconz/favicon-96x96.png'; ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url() . 'assets/img/faviconz/favicon-16x16.png'; ?>">
	<link rel="manifest" href="<?php echo base_url() . 'assets/img/faviconz/manifest.json'; ?>">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?php echo base_url() . 'assets/img/faviconz/ms-icon-144x144.png'; ?>">
	<meta name="theme-color" content="#ffffff">
	<script src="<?php echo base_url() . 'assets/js/OverlayScrollbars.min.js'; ?>"></script>


	<!-- ===============================================-->
	<!--    Stylesheets-->
	<!-- ===============================================-->
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/css/OverlayScrollbars.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/css/theme.min.css'; ?>" rel="stylesheet" id="style-default">
	<link href="<?php echo base_url() . 'assets/css/themify/themify.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/css/materialdesignicons.min.css'; ?>" rel="stylesheet" id="style-default">
	<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.css'; ?>">
	<link href='<?php echo base_url() . 'assets/css/dataTables.bootstrap.min.css'; ?>' rel='stylesheet' type='text/css'>
	<link href="<?php echo base_url() . 'assets/css/select2.min.css'; ?>" rel="stylesheet" />
	<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.css'; ?>">
</head>

<body>
	
	<!-- ===============================================-->
	<!--    Main Content-->
	<!-- ===============================================-->
	<main class="main" id="top">
		<div class="container-fluid" data-layout="container">
            <div class="row flex-center min-vh-100 py-6 text-center">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xxl-5"><img class="me-2" src="<?php echo base_url() . 'assets/img/iconz/cgrlogo_new.png'; ?>" alt="" width="200" />
                    <div class="card">
                        <div class="card-body p-4 p-sm-5">
                            <div class="fw-black lh-1 text-300 fs-error">403</div>
                            <p class="lead mt-4 text-800 font-sans-serif fw-semi-bold w-md-75 w-xl-100 mx-auto"><?php echo $this->lang->line('error_page_message'); ?></p>
                            <hr />
                            <a class="btn btn-primary btn-sm mt-3" href="dashboard?module=dashboard"><span class="fas fa-chart-pie me-2"></span><?php echo $this->lang->line('error_page_message1'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/fontawesome/all.min.js'; ?>"></script>