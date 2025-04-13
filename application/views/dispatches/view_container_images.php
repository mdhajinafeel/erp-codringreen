<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Container Photos</title>
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
    <link href="<?php echo base_url() . 'assets/css/materialdesignicons.min.css'; ?>" rel="stylesheet" id="style-default">
    <link href="<?php echo base_url() . 'assets/css/photostyle.css'; ?>" rel="stylesheet" id="style-default">
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.css'; ?>">
</head>

<body>
    <input type="hidden" id="hdnDispatchId" name="hdnDispatchId" value="<?php echo $dispatchId; ?>">
    <input type="hidden" id="hdnContainerNumber" name="hdnContainerNumber" value="<?php echo $containerNumber; ?>">

    <div id="loading">
        <img id="loading-image" src="<?php echo base_url() . 'assets/img/loader.gif'; ?>" alt="Loading...">
    </div>

    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            <div class="panel theme-panel" style="box-shadow: 0px 0px 0px;">
                <div class="panel-heading" style="border-bottom: none;">
                    <span class="panel-title"><img style="padding: 30px;" src="<?php echo base_url() . 'assets/img/iconz/cgrlogo_new.png'; ?>" width="200px;" height="120px" />
                    </span>
                </div>


                <div style="margin: 20px;">
                    <div class="form-group row mb-4" id="buttonsBulk">

                        <div class="col-md-9">
                            <?php if ($containerNumber == "") { ?>
                                <h3 style="font-size: 18px;">Container Number : <span>---</span>
                                </h3>
                            <?php } else { ?>
                                <h3 style="font-size: 18px;">Container Number : <span><b><u><?php echo $containerNumber; ?></u></b></span></h3>
                            <?php } ?>
                        </div>

                        <?php if (count($images) > 0) { ?>
                            <div class="col-md-3">
                                <button type="submit" id="downloadPhotos" name="downloadPhotos" class="btn btn-primary btn-block" style="display: block;float:right;">
                                    <i class="fa fa-download" style="margin-right: 10px;"></i>
                                    Download Photos
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="image-container">
                <?php if (count($images) > 0) { ?>
                    <?php foreach ($images as $image) { ?>
                        <a class="example-image-link" href="<?php echo $image->image_url; ?>" data-lightbox="example-set" data-title=""><img class="example-image" src="<?php echo $image->image_url; ?>" alt="" /></a>
                    <?php } ?>
                <?php } else { ?>
                    <span>No photos available!!!...</span>
                <?php } ?>
            </div>
        </div>
    </main>

    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/fontawesome/all.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap/bootstrap.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/lightbox/lightbox-plus-jquery.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.js' ?>"></script>
    <script type="text/javascript">

        var base_url = '<?php echo site_url(); ?>';
        $(document).ready(function() {

            $("#loading").hide();
            
            var processing_request = "Processing Request";

            toastr.options.closeButton = true;
            toastr.options.progressBar = true;
            toastr.options.timeOut = 5000;
            toastr.options.preventDuplicates = true;
            toastr.options.positionClass = "toast-bottom-right";

            $('#downloadPhotos').click(function(e) {
                $('#loading').show();
                e.preventDefault();

                var cNumber = $('input[name="hdnContainerNumber"]').val();
                var dId = $('input[name="hdnDispatchId"]').val();
                toastr.info(processing_request);
                $.ajax({
                    type: "GET",
                    url: base_url + "photos/viewcontainerimages/download_photos?cnum=" + cNumber + "&did=" + dId,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.error != '') {
                            toastr.clear();
                            toastr.error("There is an error. Please try again.");
                        } else {
                            toastr.clear();
                            downloadfile(JSON.downloadfile);
                            toastr.success(JSON.result);
                        }
                    },
                    error: function(jqXHR, exception) {}
                });
            });
        });

        function downloadfile(downloadurl) {
            $.ajax({
                url: downloadurl,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var filename = downloadurl.substring(downloadurl.lastIndexOf('/') + 1);
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = filename;
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    wait(3000);
                    deletefilesfromfolder();
                }
            });
        }

        function deletefilesfromfolder() {
            $.ajax({
                type: "GET",
                url: base_url + "photos/viewcontainerimages/deletefilesfromfolder",
                contentType: false,
                cache: false,
                processData: false,
                success: function(JSON) {
                    $("#loading").hide();
                }
            });
        }

        function wait(ms) {
            var start = new Date().getTime();
            var end = start;
            while (end < start + ms) {
                end = new Date().getTime();
            }
        }
    </script>
</body>

</html>