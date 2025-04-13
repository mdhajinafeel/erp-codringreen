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
	<div id="loading">
		<img id="loading-image" src="<?php echo base_url() . 'assets/img/loader.gif'; ?>" alt="Loading...">
	</div>
	<!-- ===============================================-->
	<!--    Main Content-->
	<!-- ===============================================-->
	<main class="main" id="top">
		<div class="container-fluid" data-layout="container">

			<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
				<script>
					var navbarStyle = localStorage.getItem("navbarStyle");
					if (navbarStyle && navbarStyle !== 'transparent') {
						document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
					}
				</script>
				<div class="d-flex align-items-center">
					<div class="toggle-icon-wrapper">
						<button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
					</div>
					<a class="navbar-brand" href="dashboard?module=dashboard">
						<div class="d-flex align-items-center py-3"><img class="me-2" src="<?php echo base_url() . $system[0]->application_icon; ?>" alt="" width="67" />
							<!-- <span class="font-ttkfont"><?php echo $system[0]->application_name; ?></span> -->
						</div>
					</a>
				</div>
				<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
					<div class="navbar-vertical-content scrollbar">
						<ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

							<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids) || in_array('4', $role_resources_ids) || in_array('8', $role_resources_ids)) { ?>

								<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)  || in_array('4', $role_resources_ids) || in_array('8', $role_resources_ids)) { ?>
									<li class="nav-item">
										<a class="nav-link <?php if (
																$this->router->fetch_class() == "welcome" ||
																$this->router->fetch_class() == "dashboard"
															) {
																echo 'active';
															} else {
																'';
															} ?>" href="<?php echo site_url('dashboard?module=dashboard'); ?>" role="button" aria-expanded="false">
											<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('dashboard_title'); ?></span>
											</div>
										</a>
									</li>

									<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids)) { ?>
										<li class="nav-item">
											<!-- parent pages-->
											<a class="nav-link <?php if ($this->router->fetch_class() == "users") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('users'); ?>" role="button" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-users"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('user_title'); ?></span>
												</div>
											</a>
										</li>
									<?php } ?>

								<?php } ?>

								<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)) { ?>

									<li class="nav-item">
										<!-- label-->
										<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
											<div class="col-auto navbar-vertical-label"><?php echo $this->lang->line('inventory_title'); ?>
											</div>
											<div class="col ps-0">
												<hr class="mb-0 navbar-vertical-divider" />
											</div>
										</div>

										<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids)) { ?>

											<a class="nav-link dropdown-indicator" href="#inventorymasters" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="email">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-gears"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('master_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse <?php if (
																		$this->router->fetch_class() == "products" || $this->router->fetch_class() == "warehouses"
																		|| $this->router->fetch_class() == "shippinglines" || $this->router->fetch_class() == "measurementsystems"
																		|| $this->router->fetch_class() == "inputparameters" || $this->router->fetch_class() == "qrcodegenerator"
																		|| $this->router->fetch_class() == "inputparametersettings"
																	) {
																		echo 'show';
																	} else {
																		'false';
																	} ?>" id="inventorymasters">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "products") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('products'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('product_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "warehouses") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('warehouses'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('warehouse_title'); ?></span></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "shippinglines") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('shippinglines'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('shipping_title'); ?></span></span>
														</div>
													</a>
												</li>
												<!-- <li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "measurementsystems") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('measurementsystems'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('measurement_title'); ?></span></span>
												</div>
											</a>
										</li> -->
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "inputparameters") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('inputparameters'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('inputparams_title'); ?></span></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "inputparametersettings") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('inputparametersettings'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('inputparam_title'); ?></span></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "qrcodegenerator") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('qrcodegenerator'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('qrcode_title'); ?></span></span>
														</div>
													</a>
												</li>
											</ul>

										<?php } ?>

										<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)) { ?>

											<a class="nav-link dropdown-indicator" href="#farms" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-tree"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('farm_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse <?php if ($this->router->fetch_class() == "farms") {
																		echo 'show';
																	} else {
																		'false';
																	} ?>" id="farms">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "farms") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('farms'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('farm_list_title'); ?></span>
														</div>
													</a>
												</li>
											</ul>

											<a class="nav-link dropdown-indicator" href="#reception" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-warehouse"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('reception_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse <?php if ($this->router->fetch_class() == "receptions" || $this->router->fetch_class() == "receptiontracking") {
																		echo 'show';
																	} else {
																		'false';
																	} ?>" id="reception">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "receptions") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('receptions'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('reception_list_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "receptiontracking") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('receptiontracking'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('viewtracking_title'); ?></span>
														</div>
													</a>
												</li>
											</ul>

											<a class="nav-link dropdown-indicator" href="#dispatch" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-truck"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('dispatch_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse <?php if ($this->router->fetch_class() == "dispatches" || $this->router->fetch_class() == "dispatchtracking") {
																		echo 'show';
																	} else {
																		'false';
																	} ?>" id="dispatch">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "dispatches") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('dispatches'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('dispatch_list_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "dispatchtracking") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('dispatchtracking'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('viewtracking_title'); ?></span>
														</div>
													</a>
												</li>
											</ul>

											<a class="nav-link dropdown-indicator" href="#export" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-ship"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('export_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse <?php if (
																		$this->router->fetch_class() == "exports" || $this->router->fetch_class() == "readyforexport"
																		|| $this->router->fetch_class() == "exportcreation"
																	) {
																		echo 'show';
																	} else {
																		'false';
																	} ?>" id="export">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "exports") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('exports'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('export_list_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "exportcreation") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('exportcreation'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('createexport_title'); ?></span>
														</div>
													</a>
												</li>
											</ul>

											<a class="nav-link dropdown-indicator" href="#inventoryreports" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
												<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-text"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('reports_title'); ?></span>
												</div>
											</a>
											<ul class="nav collapse  <?php if (
																			$this->router->fetch_class() == "missinginventoryorder"
																			|| $this->router->fetch_class() == "farmreports" || $this->router->fetch_class() == "exportorder"
																			|| $this->router->fetch_class() == "inventoryreports"
																		) {
																			echo 'show';
																		} else {
																			'false';
																		} ?>" id="inventoryreports">
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "farmreports") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('farmreports'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('farmreport_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "inventoryreports") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('inventoryreports'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('inventoryreport_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "exportorder") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('exportorder'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('exportorder_title'); ?></span>
														</div>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link <?php if ($this->router->fetch_class() == "missinginventoryorder") {
																			echo 'active';
																		} else {
																			'';
																		} ?>" href="<?php echo site_url('missinginventoryorder'); ?>" aria-expanded="false">
														<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('missinginventory_title'); ?></span>
														</div>
													</a>
												</li>
											</ul>

										<?php } ?>
									</li>
								<?php } ?>
							<?php } ?>

							<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)) { ?>

								<li class="nav-item">
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label"><?php echo $this->lang->line('sales'); ?>
										</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div>

									<a class="nav-link <?php if ($this->router->fetch_class() == "salesreport") {
															echo 'active';
														} else {
															'';
														} ?>" href="<?php echo site_url('salesreport'); ?>" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-invoice-dollar"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('sold_unsold'); ?></span>
										</div>
									</a>
									
									<a class="nav-link <?php if ($this->router->fetch_class() == "exportordersales") {
															echo 'active';
														} else {
															'';
														} ?>" href="<?php echo site_url('exportordersales'); ?>" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-ship"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('exportorder_title'); ?></span>
										</div>
									</a>

									<a class="nav-link <?php if ($this->router->fetch_class() == "claimtracker") {
															echo 'active';
														} else {
															'';
														} ?>" href="<?php echo site_url('claimtracker'); ?>" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-dollar"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('claimtracker_title'); ?></span>
										</div>
									</a>
									
								</li>
								<?php } ?>

							<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('4', $role_resources_ids)) { ?>
								<li class="nav-item">
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label"><?php echo $this->lang->line('finance_title'); ?>
										</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div>

									<a class="nav-link dropdown-indicator" href="#financemasters" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="email">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-gears"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('master_title'); ?></span>
										</div>
									</a>
									<ul class="nav collapse <?php if (
																$this->router->fetch_class() == "ledgertypes" || $this->router->fetch_class() == "suppliers" ||
																$this->router->fetch_class() == "accountheads" || $this->router->fetch_class() == "taxsettings"
															) {
																echo 'show';
															} else {
																'false';
															} ?>" id="financemasters">
										<!-- <li class="nav-item">
											<a class="nav-link" href="" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('currencies_title'); ?></span>
												</div>
											</a>
										</li> -->
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "suppliers") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('suppliers'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('supplier_title'); ?></span></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "taxsettings") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('taxsettings'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('taxsettings_title'); ?></span></span>
												</div>
											</a>
										</li>
										<!-- <li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "ledgertypes") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('ledgertypes'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('ledgertype_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "accountheads") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('accountheads'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('accounthead_title'); ?></span>
												</div>
											</a>
										</li> -->
									</ul>

									<!-- <a class="nav-link dropdown-indicator" href="#ledger" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-dollar"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('expenseledger_title'); ?></span>
										</div>
									</a>
									<ul class="nav collapse <?php if ($this->router->fetch_class() == "expenseadvanceregistry" || $this->router->fetch_class() == "expenseledger" || $this->router->fetch_class() == "expenseledgerreport") {
																echo 'show';
															} else {
																'false';
															} ?>" id="ledger">
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "expenseadvanceregistry") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('expenseadvanceregistry'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('advanceregistry_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "expenseledger") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('expenseledger'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('viewledger_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "expenseledgerreport") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('expenseledgerreport'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('ledgerreport_title'); ?></span>
												</div>
											</a>
										</li>
									</ul> -->

									<a class="nav-link <?php if ($this->router->fetch_class() == "purchasecontracts") {
															echo 'active';
														} else {
															'';
														} ?>" href="<?php echo site_url('purchasecontracts'); ?>" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-circle-check"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('contracts_title'); ?></span>
										</div>
									</a>

									<a class="nav-link dropdown-indicator" href="#credits" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-cash-register"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('credits_title'); ?></span>
										</div>
									</a>
									<ul class="nav collapse <?php if ($this->router->fetch_class() == "suppliercredit" || $this->router->fetch_class() == "purchasemanagercredit") {
																echo 'show';
															} else {
																'false';
															} ?>" id="credits">
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "suppliercredit") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('suppliercredit'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('suppliercredit_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "purchasemanagercredit") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('purchasemanagercredit'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('purchasemanager_title'); ?></span>
												</div>
											</a>
										</li>
									</ul>

									<a class="nav-link dropdown-indicator" href="#contractledger" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-coins"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('ledgers_title'); ?></span>
										</div>
									</a>
									<ul class="nav collapse <?php if ($this->router->fetch_class() == "supplierledger" || $this->router->fetch_class() == "purchasemanagerledger") {
																echo 'show';
															} else {
																'false';
															} ?>" id="contractledger">
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "supplierledger") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('supplierledger'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('suppliercredit_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "purchasemanagerledger") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('purchasemanagerledger'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('purchasemanager_title'); ?></span>
												</div>
											</a>
										</li>
									</ul>

									<a class="nav-link dropdown-indicator" href="#financereports" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="events">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-text"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('reports_title'); ?></span>
										</div>
									</a>
									<ul class="nav collapse <?php if ($this->router->fetch_class() == "liquidationreport" || $this->router->fetch_class() == "costsummaryreport" || $this->router->fetch_class() == "stockreport") {
																echo 'show';
															} else {
																'false';
															} ?>" id="financereports">
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "liquidationreport") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('liquidationreport'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('liquidationreport_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "costsummaryreport") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('costsummaryreport'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('costsummaryreport_title'); ?></span>
												</div>
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link <?php if ($this->router->fetch_class() == "stockreport") {
																	echo 'active';
																} else {
																	'';
																} ?>" href="<?php echo site_url('stockreport'); ?>" aria-expanded="false">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?php echo $this->lang->line('stockreport_title'); ?></span>
												</div>
											</a>
										</li>
									</ul>
								</li>
							<?php } ?>

							<?php if (in_array('1', $role_resources_ids)) { ?>
								<li class="nav-item">
									<!-- label-->
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label"><?php echo $this->lang->line('settings_title'); ?>
										</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div>
									<!-- parent pages-->
									<a class="nav-link <?php if ($this->router->fetch_class() == "origins") {
															echo 'active';
														} else {
															'';
														} ?>" href="<?php echo site_url('origins'); ?>" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-globe"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('origins_title'); ?></span>
										</div>
									</a>
									<a class="nav-link" href="#" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-person"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('roles_title'); ?></span>
										</div>
									</a>
									<a class="nav-link" href="#" role="button" aria-expanded="false">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-desktop"></span></span><span class="nav-link-text ps-1"><?php echo $this->lang->line('system_title'); ?></span>
										</div>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</nav>
			<div class="content">
				<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">

					<button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
					<a class="navbar-brand me-1 me-sm-3" href="dashboard?module=dashboard">
						<div class="d-flex align-items-center"><img class="me-2" src="<?php echo base_url() . $system[0]->application_icon; ?>" alt="" width="67" />
							<!-- <span class="font-ttkfont"><?php echo $system[0]->application_name; ?></span> -->
						</div>
					</a>

					<ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">

						<?php if ($system[0]->module_language == 1) { ?>
							<li class="nav-item dropdown">
								<a class="nav-link pe-0" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<div class="header-language-menu">
										<div class="avatar avatar-lg">
											<?php echo $flg_icn; ?>
										</div>
									</div>
								</a>
								<?php $languages = $this->Settings_model->all_languages(); ?>
								<div class="dropdown-menu dropdown-menu-end py-0 header-language" aria-labelledby="navbarDropdownUser">
									<div class="bg-white dark__bg-1000 rounded-2 py-2">
										<?php foreach ($languages as $lang) { ?>
											<a class="dropdown-item" tabindex="-1" href="<?php echo site_url('dashboard/set_language/' . $lang->language_name); ?>">
												<img src="<?php echo base_url() . 'assets/img/iconz/languages/' . $lang->language_flag; ?>" alt="" /> &nbsp; <?php echo $this->lang->line($lang->language_name); ?></a>
										<?php } ?>
									</div>
								</div>
							</li>
						<?php } ?>
						<li class="nav-item dropdown">
							<a class="nav-link pe-0" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<div class="header-profile-menu">
									<div class="avatar avatar-xl">
										<img class="rounded-circle" src="<?php echo base_url() . $session['profile_photo']; ?>" alt="" />

									</div>
									<span><?php echo $session['full_name']; ?></span>
									<i class="fas fa-angle-down"></i>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
								<div class="bg-white dark__bg-1000 rounded-2 py-2">

									<!-- <a class="dropdown-item" href="pages/user/profile.html">Profile &amp; account</a> -->
									<a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Logout</a>
								</div>
							</div>
						</li>
					</ul>
				</nav>
				<?php echo $subview; ?>
				<footer class="footer">
					<div class="row g-0 justify-content-between fs--1 mt-4 mb-3">
						<div class="col-12 col-sm-auto text-center">
							<p class="mb-0 text-600"><?php echo $this->lang->line('copyright'); ?> <span class="d-none d-sm-inline-block">| </span><br class="d-sm-none" /> <?php echo date("Y"); ?> &copy; <a href="https://codringroup.com" target="_blank"><?php echo $system[0]->application_name; ?></a></p>
						</div>
					</div>
				</footer>
			</div>
		</div>
	</main>
	<!-- ===============================================-->
	<!--    End of Main Content-->
	<!-- ===============================================-->

	<!-- ===============================================-->
	<!--    JavaScripts-->
	<!-- ===============================================-->
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/popper/popper.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap/bootstrap.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/anchorjs/anchor.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/is/is.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/fontawesome/all.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/lodash/lodash.min.js'; ?>"></script>
	<!--<script type="text/javascript" src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>-->
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/list.js/list.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/js/theme.js'; ?>"></script>
	<script src="<?php echo base_url() . 'assets/js/select2.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/scripts/' . $path_url . '.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.js' ?>"></script>
	<!-- INTERNAL Vector js -->
	<script type="text/javascript">
		var base_url = '<?php echo site_url() . $this->router->fetch_class(); ?>';
		var login_url = '<?php echo site_url() . "logout"; ?>';
		var site_url = '<?php echo base_url(); ?>';
		var lang_name = '<?php echo $language_name; ?>';
		var datatable_language = site_url + "assets/plugins/" + lang_name + ".json";
		var processing_request = "<?php echo $this->lang->line('processing_request'); ?>";
		$(document).ready(function() {

			//ZOOM LEVEL
			if (window.matchMedia("(max-width: 767px)").matches) {
				//DO NOTHING
			} else {
				document.body.style.zoom = "80%";
			}

			toastr.options.closeButton = true;
			toastr.options.progressBar = true;
			toastr.options.timeOut = 5000;
			toastr.options.preventDuplicates = true;
			toastr.options.positionClass = "toast-bottom-right";
			var site_url = '<?php echo site_url(); ?>';
		});
	</script>
</body>

</html>