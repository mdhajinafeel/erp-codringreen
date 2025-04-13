$(document).ready(function () {

    $("#loading").hide();
    $("#error-origin").hide();

    $("#origin_gcreport").change(function () {
        fetchBuyers($("#origin_gcreport").val());
    });

    $("#btn_download_gcreports").click(function () {

        var isValid1 = true;

        var originid = $("#origin_gcreport").val();
        var buyername = $("#buyer_name").val();

        if (originid == 0) {
            $("#error-origin").show();
            isValid1 = false;
        } else {
            $("#error-origin").hide();
            isValid1 = true;
        }

        if (isValid1) {

            var fd = new FormData();
            fd.append("originid", originid);
            fd.append("buyername", buyername);
            fd.append("csrf_cgrerp", $("#hdnCsrf").val());

            toastr.clear();
            toastr.info(processing_request);
            $("#loading").show();
            $.ajax({
                type: "POST",
                url: base_url + "/generate_report",
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $("#loading").hide();
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                    } else {
                        toastr.success(response.successmessage);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        window.location = response.result;
                        wait(3000);
                        deletefilesfromfolder();
                    }
                }
            });
        }
    });
});

function fetchBuyers(originid) {
    $("#loading").show();
    $.ajax({
        url: base_url + "/fetch_buyers?originid=" + originid,
        cache: false,
        method: "GET",
        dataType: 'json',
        success: function (JSON) {
            $("#loading").hide();
            if (JSON.redirect == true) {
                window.location.replace(login_url);
            } else if (JSON.result != '') {
                $("#buyer_name").empty();
                $("#buyer_name").append(JSON.result);
            }
        }
    });
}

function deletefilesfromfolder() {
    $.ajax({
        type: "GET",
        url: base_url + "/deletefilesfromfolder",
        contentType: false,
        cache: false,
        processData: false,
        success: function (JSON) {
            //$("#loading").hide();
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