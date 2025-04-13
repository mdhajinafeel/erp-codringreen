<?php
$session = $this->session->userdata('fullname');
$system = $this->Settings_model->read_setting_info(1);

$role_resources_ids = explode(',', $session["role_id"]);
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang');
date_default_timezone_set($session['default_timezone']);
$todayDay = $this->lang->line(strtolower(date('l')));
$todayMonth = $this->lang->line(strtolower(date('F'))); ?>
<div class="row mb-3 g-3">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-0 flex-between-center">
                    <div class="col-sm-auto d-flex align-items-center">
                        <div class="avatar avatar-md">
                            <img class="rounded-circle" src="<?php echo base_url() . $session['profile_photo']; ?>" alt="" />
                        </div>
                        <div class="mar-left dashboard-name">
                            <h3><?php echo $this->lang->line('welcome') . ', '; ?><?php echo $session['full_name']; ?>!</h3>
                            <h4><?php echo $todayDay . ', ' . date('d') . ' ' . ucfirst($todayMonth) . ' ' . date('Y'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)) { ?>
    <!-- ROW-2 -->
    <div class="row" id="divExportContainers">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><?php echo $this->lang->line("export_containers"); ?></h3>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="row mb-4 alignitems-center-1">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8">
                            <div id="export-map" class="worldh world-map h-400"></div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4 text-align-center" id="divMapLegend">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ROW-2 END -->
<?php } ?>

<script src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>

<?php if (in_array('1', $role_resources_ids) || in_array('2', $role_resources_ids) || in_array('3', $role_resources_ids)) { ?>

    <script src="<?php echo base_url() . 'assets/js/vectormap/jquery-jvectormap-2.0.2.min.js'; ?>"></script>
    <script src="<?php echo base_url() . 'assets/js/vectormap/jquery-jvectormap-world-mill-en.js'; ?>"></script>

    <script>
        $(document).ready(function() {
            $("#loading").hide();
            $("#divExportContainers").hide();
            getExportMapDetails();
        });

        function getExportMapDetails() {
            dashboard_url = base_url.replace("welcome", "dashboard");

            $.ajax({
                type: "GET",
                url: dashboard_url + "/export_map_details",
                dataType: 'JSON',
                success: function(data) {

                    var markersArray = [];
                    if (data.mapdata.length > 0) {

                        for (var i = 0; i < data.mapdata.length; i++) {
                            markersArray.push({
                                latLng: [data.mapdata[i].latitude, data.mapdata[i].longitude],
                                name: data.mapdata[i].mapdata,
                                style: {
                                    fill: data.mapdata[i].colorcode,
                                }
                            });
                        }

                        var legenddata = "";
                        for (var j = 0; j < data.legenddata.length; j++) {
                            if (j == 0) {
                                legenddata = legenddata + "<div class='mt-4 mb-4'><div class='col-sm-10 col-lg-9 col-xl-9 col-xxl-10 ps-sm-0'><div class='d-flex align-items-end justify-content-between mb-1'><h6 class='mb-1'>" + data.legenddata[j].pod + "</h6><h6 class='fw-semibold mb-1'><span class='fs-11'>" + data.legenddata[j].contribution + "</span></h6></div><div class='progress h-2 mb-3'><div class='progress-bar' style='background-color: " + data.legenddata[j].colorcode + "; width: " + data.legenddata[j].contribution + ";' role='progressbar'></div></div></div></div>";
                            } else {
                                legenddata = legenddata + "<div class='mb-4'><div class='col-sm-10 col-lg-9 col-xl-9 col-xxl-10 ps-sm-0'><div class='d-flex align-items-end justify-content-between mb-1'><h6 class='mb-1'>" + data.legenddata[j].pod + "</h6><h6 class='fw-semibold mb-1'><span class='fs-11'>" + data.legenddata[j].contribution + "</span></h6></div><div class='progress h-2 mb-3'><div class='progress-bar' style='background-color: " + data.legenddata[j].colorcode + "; width: " + data.legenddata[j].contribution + ";' role='progressbar'></div></div></div></div>";
                            }
                        }

                        jQuery('#export-map').vectorMap({
                            map: 'world_mill_en',
                            backgroundColor: 'transparent',
                            borderColor: '#000',
                            borderOpacity: 0,
                            borderWidth: 0,
                            zoomOnScroll: true,
                            regionStyle: {
                                initial: {
                                    fill: '#5b6c4a',
                                    'stroke-width': 1,
                                    stroke: '#fff'
                                }
                            },
                            markerStyle: {
                                initial: {
                                    r: 8,
                                    'fill-opacity': 1,
                                    'stroke': '#93d5ed',
                                    'stroke-width': 1,
                                    'stroke-opacity': 1
                                }
                            },
                            enableZoom: true,
                            hoverColor: '#79e580',
                            markers: markersArray,
                            onMarkerTipShow: function(event, label, code) {
                                label.html(label.html().replace("##", "<br><br>").replace("#", "<br>").replace("###", "<br>"));
                            },
                            hoverOpacity: null,
                            normalizeFunction: 'linear',
                            scaleColors: ['#93d5ed', '#93d5ee'],
                            selectedColor: '#c9dfaf',
                            selectedRegions: [],
                            showTooltip: true,
                        });

                        if (legenddata != "") {
                            $("#divMapLegend").html(legenddata);
                        }

                        $("#divExportContainers").show();
                    } else {
                        $("#divExportContainers").hide();
                    }
                }
            });
        }
    </script>

<?php } ?>