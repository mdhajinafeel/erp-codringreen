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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<!-- ===============================================-->
	<!--    Stylesheets-->
	<!-- ===============================================-->
	<link href="<?php echo base_url() . 'assets/css/OverlayScrollbars.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/css/theme.min.css'; ?>" rel="stylesheet" id="style-default">
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
		<div class="container-fluid">

			<div class="row min-vh-100 bg-100">
				<div class="col-12 position-relative">
					<div class="bg-holder d-none d-lg-block" style="background-image:url(<?php echo base_url() . 'assets/img/login.png'; ?>);">
					</div>
					<!--/.bg-holder-->
					<div class="col-sm-12 col-md-6 px-sm-0 align-self-center mx-auto py-5 login-form">
						<div class="row justify-content-center g-0">
							<div class="col-lg-9 col-xl-8 col-xxl-6">
								<div class="card">
									<div class="card-header bg-circle-shape text-center p-2"><a class="font-sans-serif fw-bolder fs-4 z-index-1 position-relative link-light light"><img class="img-fluid" src="<?php echo base_url() . 'assets/img/iconz/cgrlogo_new.png'; ?>"></a></div>
									<div class="card-body p-4">

										<?php $attributes = array('class' => 'form-ttkerp', 'name' => 'ttkerp-form', 'id' => 'ttkerp-form', 'data-redirect' => 'dashboard', 'data-form-table' => 'login', 'data-is-redirect' => '1', 'autocomplete' => 'off'); ?>
										<?php $hidden = array('user_id' => 0); ?>
										<?php echo form_open('auth/login', $attributes, $hidden); ?>
										<div class="mb-3">
											<div class="d-flex justify-content-between">
												<label class="form-label" for="login-username">Username</label>
											</div>
											<input class="login-form form-control" name="login-username" id="login-username" type="text" />
											<label id="error-username" class="error-text">Please enter the username</label>
										</div>
										<div class="mb-3">
											<div class="d-flex justify-content-between">
												<label class="form-label" for="login-password">Password</label>
											</div>
											<div class="form-group icon-div">
												<input class="login-form form-control" name="login-password" id="login-password" type="password" />
												<i class="bi bi-eye-slash" id="togglePassword"></i>
												<label id="error-password" class="error-text">Please enter the password</label>
											</div>
										</div>
										<!--<div class="row flex-between-center">-->
											<!--<div class="col-auto">-->
											<!--	<div class="login-form-check mb-0 ml-20">-->
											<!--		<input class="form-check-input" type="checkbox" id="split-checkbox" />-->
											<!--		<label class="form-check-label mb-0" for="split-checkbox">Remember me</label>-->
											<!--	</div>-->
											<!--</div>-->
											<!-- <div class="col-auto"><a class="fs--1" href="../../../pages/authentication/split/forgot-password.html">Forgot Password?</a></div> -->
										<!--</div>-->
										<div class="mb-3">
											<?php echo form_button(array('name' => 'cgrerp_form', 'type' => 'submit', 'class' => 'btn btn-primary d-block w-100 mt-3 login', 'content' => '<i class="fa fa-lock"></i> Login')); ?>
										</div>
										<?php echo form_close(); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</main>
	<!-- ===============================================-->
	<!--    End of Main Content-->
	<!-- ===============================================-->

	<!-- ===============================================-->
	<!--    JavaScripts-->
	<!-- ===============================================-->
	<script src="<?php echo base_url() . 'assets/js/jquery191.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.js' ?>"></script>
	<script type="text/javascript">
		var site_url = '<?php echo base_url(); ?>';
		var processing_request = '<?php echo "Processing Request"; ?>';
		$(document).ready(function() {


			toastr.options.closeButton = true;
			toastr.options.progressBar = true;
			toastr.options.timeOut = 3000;
			toastr.options.preventDuplicates = true;
			toastr.options.positionClass = "toast-bottom-right";
			var site_url = '<?php echo site_url(); ?>';
		});

        const togglePassword = document.querySelector('#togglePassword');
  
        const password = document.querySelector('#login-password');
  
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
  
            //this.classList.toggle('bi-eye-slash');
			togglePassword.classList.toggle('bi-eye');
        });
	</script>
	<script type="text/javascript" src="<?php echo base_url() . 'assets/scripts/cgr_login.js'; ?>"></script>
</body>

</html>