<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
	<div class="card-header table-responsive">
		<div class="row flex-between-end">
			<div class="col-auto align-self-center">
				<h3> <?php echo $this->lang->line('sawmill_title'); ?> </h3>
			</div>
			<div class="col-auto ms-auto">
				<button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_report">
					<span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
						<?php echo $this->lang->line('download_reports'); ?></span>
				</button>
			</div>
		</div>
	</div>
	<div class="card-body pt-0">
		<div class="row mb-3">
			<div class="col-md-4 align-self-center">
				<label for="origin"><?php echo $this->lang->line("origin"); ?></label>
				<select class="form-control" name="origin" id="origin" data-plugin="select_erp">

					<?php foreach ($applicable_origins as $origin) { ?>
						<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="row g-3 mb-3">
			<div class="col-md-3">
				<div class="card overflow-hidden" style="min-width: 12rem">
					<div class="bg-holder bg-card" style="background-image:url(assets/img/spot-illustration/corner-1.png);">
					</div>
					<div class="card-body position-relative">
						<h4><?php echo $this->lang->line('total_volume'); ?></h4>
						<div class="display-4 fs-3 mt-3 mb-2 fw-normal font-sans-serif text-warning" id="total_volume">0</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card overflow-hidden" style="min-width: 12rem">
					<div class="bg-holder bg-card" style="background-image:url(assets/img/spot-illustration/corner-2.png);">
					</div>
					<div class="card-body position-relative">
						<h4><?php echo $this->lang->line('total_no_of_pieces'); ?></h4>
						<div class="display-4 fs-3 mt-3 mb-2 fw-normal font-sans-serif text-info" id="total_pieces">0</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card overflow-hidden" style="min-width: 12rem">
					<div class="bg-holder bg-card" style="background-image:url(assets/img/spot-illustration/corner-3.png);">
					</div>
					<div class="card-body position-relative">
						<h4><?php echo $this->lang->line('total_cost'); ?></h4>
						<div class="display-4 fs-3 mt-3 mb-2 fw-normal font-sans-serif text-info" id="total_cost">0</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card overflow-hidden" style="min-width: 12rem">
					<div class="bg-holder bg-card" style="background-image:url(assets/img/spot-illustration/corner-4.png);">
					</div>
					<div class="card-body position-relative">
						<h4><?php echo $this->lang->line('cost_per_pcs'); ?></h4>
						<div class="display-4 fs-3 mt-3 mb-2 fw-normal font-sans-serif" id="cost_per_cbm">0</div>
					</div>
				</div>
			</div>
			<!-- <div class="col-md-3">
				<div class="card overflow-hidden" style="min-width: 12rem">
					<div class="bg-holder bg-card" style="background-image:url(assets/img/spot-illustration/corner-4.png);">
					</div>
					<div class="card-body position-relative">
						<h4><?php echo $this->lang->line('total_containers'); ?></h4>
						<div class="display-4 fs-3 mt-3 mb-2 fw-normal font-sans-serif" style="color: #0f619b;" id="total_bins">0</div>
					</div>
				</div>
			</div> -->
		</div>

		<div class="row g-3 mb-5">
			<div class="col-md-6">
				<div class="card h-100">
					<div class="card-header">
						<div class="row flex-between-end">
							<div class="col-auto align-self-center">
								<h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('sawmill_operations'); ?></h5>
							</div>

						</div>
					</div>
					<div class="card-body">
						<div class="tab-content">
							<div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-7106f20c-f055-4653-ab49-9867db7fd49f" id="dom-7106f20c-f055-4653-ab49-9867db7fd49f">
								<div class="echart-basic-bar" style="min-height: 450px;" data-echart-responsive="true"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="card mb-3">
					<div class="card-body">

						<div class="row mb-4 flex-between-end">
							<div class="col-auto align-self-center">
								<h4 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('received'); ?></h4>
							</div>

							<div class="col-auto ms-auto">
								<button class="btn btn-info btn-md btn-right-margin" style="padding: 0.6125rem 1rem;" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_received_report">
									<span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
								</button>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('text_volume'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="volumeReceived">0</p>
									</div>
								</div>
							</div>

							<div class="col-md-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('pieces'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="piecesReceived">0</p>
									</div>
								</div>
							</div>

							<div class="col-md-4 pt-3 pt-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('icas'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="icasReceived">0</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card mb-3">
					<div class="card-body">

						<div class="row mb-4 flex-between-end">
							<div class="col-auto align-self-center">
								<h4 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('unprocessed'); ?></h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('text_volume'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="volumeUnprocessed">0</p>
									</div>
								</div>
							</div>

							<div class="col-md-4 border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('pieces'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="piecesUnprocessed">0</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card mb-3">
					<div class="card-body">

						<div class="row mb-4 flex-between-end">
							<div class="col-auto align-self-center">
								<h4 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('exported'); ?></h4>
							</div>

							<div class="col-auto ms-auto">
								<button class="btn btn-warning btn-md btn-right-margin" style="padding: 0.6125rem 1rem;" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_exported_report">
									<span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
								</button>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('text_volume'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="volumeExported">0</p>
									</div>
								</div>
							</div>

							<div class="col-md-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('pieces'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="piecesExported">0</p>
									</div>
								</div>
							</div>

							<div class="col-md-4 pt-3 pt-lg-0">
								<div class="d-flex flex-between-center mb-3">
									<div class="d-flex align-items-center">
										<h5 class="mb-0"><?php echo $this->lang->line('containers'); ?></h5>
									</div>
								</div>
								<div class="d-flex">
									<div class="d-flex">
										<p class="font-sans-serif lh-1 mb-1 fs-3 pe-2" id="containersExported">0</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row mb-5">
			<div class="tab-content">
				<div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-ec0fa1e3-6325-4caf-a468-7691ef065d01" id="dom-ec0fa1e3-6325-4caf-a468-7691ef065d01">
					<div class="accordion" id="accordionExample">
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading1">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
									<?php echo $this->lang->line('detailed_data'); ?>
								</button>
							</h2>
							<div class="accordion-collapse collapse" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample">
								<div class="accordion-body table-responsive">
									<table class="datatables-demo table table-striped table-bordered" id="xin_table_sawmills" style="width: 100% !important;">
										<thead>
											<tr>
												<th><?php echo $this->lang->line('supplier_name'); ?></th>
												<th><?php echo $this->lang->line('total_trucks'); ?></th>
												<th><?php echo $this->lang->line('pieces'); ?></th>
												<th><?php echo $this->lang->line('volume'); ?></th>
												<th><?php echo $this->lang->line('total_cost'); ?></th>
											</tr>
										</thead>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/jquery-ui.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/charts/echarts.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/charts/echarts.js'; ?>"></script>

<!-- SCRIPT INITIALIZATION -->
<script>
	var summaryDataInit = function summaryDataInit() {
		$.ajax({
			url: base_url + "/inventory_summary_data?origin_id=" + $('#origin').val(),
			type: "GET",
			dataType: "json",
			beforeSend: function() {
				$("#loading").show(); // ✅ always show before request starts
			},
			success: function(response) {
				$("#total_volume").text(response.totalProcessedVolume);
				$("#total_cost").text(response.totalProcessedCost);
				$("#cost_per_cbm").text(response.costPerCbm);
				$("#total_pieces").text(response.totalProcessedPieces);

				$("#volumeReceived").text(response.receivedVolume);
				$("#piecesReceived").text(response.receivedPieces);
				$("#icasReceived").text(response.receivedICAs);

				$("#volumeExported").text(response.exportedVolume);
				$("#piecesExported").text(response.exportedPieces);
				$("#containersExported").text(response.exportedContainers);

				$("#volumeUnprocessed").text(response.unprocessedVolume);
				$("#piecesUnprocessed").text(response.unprocessedPieces);
			},
			error: function() {
				console.error("Error fetching chart data");
			},
			complete: function() {
				$("#loading").hide(); // ✅ always hide after request ends
			}
		});
	};

	/* -------------------------------------------------------------------------- */

	/*                             Echarts Bar Chart                             */

	/* -------------------------------------------------------------------------- */


	var echartsBasicBarChartInit = function echartsBasicBarChartInit() {
		var $barChartEl = document.querySelector('.echart-basic-bar');

		if ($barChartEl) {
			$.ajax({
				url: base_url + "/inventory_chart_data?origin_id=" + $('#origin').val(),
				type: "GET",
				dataType: "json",
				beforeSend: function() {
					$("#loading").show(); // ✅ always show before request starts
				},
				success: function(response) {
					$("#loading").hide();
					// Get options from data attribute
					var userOptions = utils.getData($barChartEl, 'options');
					var chart = window.echarts.init($barChartEl);
					var category = response.categories;
					var data = response.values;

					// Predefined color palette (you can expand this)
					var colors = [
						utils.getColor('primary'),
						utils.getColor('success'),
						utils.getColor('info'),
						utils.getColor('warning'),
						utils.getColor('danger'),
						'#6f42c1', // purple
						'#20c997', // teal
						'#fd7e14' // orange
					];

					var getDefaultOptions = function getDefaultOptions() {
						return {
							// tooltip: {
							// 	trigger: 'axis',
							// 	padding: [7, 10],
							// 	backgroundColor: utils.getGrays()['100'],
							// 	borderColor: utils.getGrays()['300'],
							// 	textStyle: {
							// 		color: utils.getColors().dark
							// 	},
							// 	borderWidth: 1,
							// 	formatter: tooltipFormatter,
							// 	transitionDuration: 0,
							// 	axisPointer: {
							// 		type: 'none'
							// 	}
							// },
							tooltip: {
								trigger: 'axis',
								padding: [7, 10],
								backgroundColor: utils.getGrays()['100'],
								borderColor: utils.getGrays()['300'],
								textStyle: {
									color: utils.getColors().dark
								},
								borderWidth: 1,
								transitionDuration: 0,
								axisPointer: {
									type: 'shadow' // better visibility on bars
								},
								formatter: function(params) {
									let tooltipText = params[0].axisValue + '<br/>';
									params.forEach(item => {
										tooltipText += item.marker + " " + item.seriesName + ": " + item.value.toFixed(3) + "<br/>";
									});
									return tooltipText;
								}
							},
							xAxis: {
								type: 'category',
								data: category,
								axisLine: {
									lineStyle: {
										color: utils.getGrays()['300'],
										type: 'solid'
									}
								},
								axisTick: {
									show: false
								},
								axisLabel: {
									color: utils.getGrays()['black'],
									formatter: function formatter(value) {
										return value; //.substring(0, 3);
									},
									margin: 15
								},
								splitLine: {
									show: false
								}
							},
							yAxis: {
								type: 'value',
								boundaryGap: true,
								axisLabel: {
									show: true,
									color: utils.getGrays()['black'],
									margin: 15
								},
								splitLine: {
									show: true,
									lineStyle: {
										color: utils.getGrays()['200']
									}
								},
								axisTick: {
									show: false
								},
								axisLine: {
									show: false
								},
								min: 0
							},
							series: [{
								type: 'bar',
								name: '<?php echo $this->lang->line('volume'); ?>',
								data: data,
								lineStyle: {
									color: utils.getColor('primary')
								},
								// itemStyle: {
								// 	color: utils.getColor('primary'),
								// 	barBorderRadius: [3, 3, 0, 0]
								// },

								itemStyle: {
									color: function(params) {
										// assign different color per bar
										return colors[params.dataIndex % colors.length];
									},
									barBorderRadius: [3, 3, 0, 0]
								},
								showSymbol: false,
								symbol: 'circle',
								smooth: false,
								hoverAnimation: true
							}],
							grid: {
								right: '3%',
								left: '10%',
								bottom: '10%',
								top: '5%'
							}
						};
					};

					echartSetOption(chart, userOptions, getDefaultOptions);
				},
				error: function() {
					console.error("Error fetching chart data");
				},
				complete: function() {
					$("#loading").hide(); // ✅ always hide after request ends
				}
			});
		}
	};
	/* eslint-disable */

	/* -------------------------------------------------------------------------- */

	/*                            Theme Initialization                            */

	/* -------------------------------------------------------------------------- */

	docReady(echartsBasicBarChartInit);
	docReady(summaryDataInit);
</script>