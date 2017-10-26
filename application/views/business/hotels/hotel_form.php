<style>
    .img-wrap{position:relative;}
    .img-wrap .delete {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        line-height: 32px;
        font-size: 10px !important;
        text-align: center;
        background: rgba(0,0,0,0.8);
        color: #fff;
        /* border: solid 1px #ccc; */
        border-radius: 33px;}
    .img-wrap .delete i{font-size: 12px;}
    .img-wrap .delete:hover{background:rgba(0,0,0,1);}

    .preview_images
    {
        position:absolute;
        top: 0px;
        left: 0px;
    }
    #preview_photo_img1
    {
        z-index: 10;
    }
    #preview_photo_img2
    {
        z-index: 20;
    }
    #preview_photo_div{
        width: 90px;
    }
    #preview_photo_div +  .media-body{
        width: 86%;
    }
    @media(max-width:767px){
        .icp_logo_wrapper .uploader,
        .preview_photo_div_wrapper .uploader{
            width: 82%;
            display: block;
        }
        .icp_logo_wrapper .filename,.preview_photo_div_wrapper .filename{    
            display: block;
        }
        .icp_logo_wrapper  .action,.preview_photo_div_wrapper  .action{    
            position: absolute;
            right: 0;
            top: 0;
        }
    }
    @media(max-width:767px){
        .icp_logo_wrapper .media-body{
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .preview_photo_div_wrapper .media-body{
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .preview_photo_div_wrapper .media-left #preview_photo_img1 {
            position: relative;
        }

    }
    @media(max-width:380px){
        .icp_logo_wrapper .uploader,
        .preview_photo_div_wrapper .uploader{
            width: 97%;
            display: block;
        }#preview_photo_div +  .media-body{
            width: 100%;
        }
    }
    .hidden {
        display: none;
    }

    .img-export {
        display: block;
    }
    .js-editorcanvas, .js-export.img-export{
        display: none;
    }
    .btn_img_crop{
        border:2px solid #166dba;
        color:#166dba;
        padding:10px 30px;
        top:10px;
        font-weight: 500;
    }
    .btn_img_crop:hover{
        background-color: #166dba;
        color:#fff;
        text-decoration: none;
    }
</style>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($hotel_data))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?> 
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/hotels/index/' . $business_data['id']); ?>"><i class="icon-city"></i> Hotels</a></li>            
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->flashdata('success')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('success') ?></strong>
                </div>
            <?php } ?>
            <?php if ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel panel-flat">
        <div class="panel-body">
            <form class="form-horizontal form-validate-jquery" action="" id="business_info" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Hotel Information</legend>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Upload Hotel Image</label>
                        <div class="col-lg-5">
                            <div class="media no-margin-top preview_photo_div_wrapper">
                                <div class="media-left" id="preview_photo_div">
                                    <?php
                                    if (isset($hotel_data) && $hotel_data['hotel_pic'] != '') {
                                        $required = '';
                                        ?>
                                        <img src="<?php echo HOTEL_IMAGES . $hotel_data['hotel_pic'] ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php
                                    } else {
                                        $required = 'required="required"';
                                        ?>
                                        <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                    <?php } ?>
                                </div>
                                <div class="media-body">
                                    <input type="file" name="preview_photo" id="preview_photo" class="previewfile-styled" onchange="readpreview_photo(this);" <?php echo $required; ?>>
                                    <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                </div>
                                <div style="opacity: 0;height: 0;">
                                    <canvas id="canvas1" width="58" height="58" border="0"></canvas>
                                </div>
                            </div>
                            <?php
                            if (isset($preview_photo_validation))
                                echo '<label id="preview_photo-error" class="validation-error-label" for="preview_photo">' . $preview_photo_validation . '</label>';
                            ?>
                            <span id="spn-preview_photo-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Hotel Name<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" name="name" id="name" placeholder="Enter Hotel Name" required="required" class="form-control" value="<?php echo (isset($hotel_data)) ? $hotel_data['name'] : set_value('name'); ?>">
                            <?php
                            echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group address1">
                        <label class="col-lg-2 control-label">Hotel Address
                            <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" name="address" id="address" class="form-control" required="required" placeholder="Enter Hotel Address" value="<?php echo (isset($hotel_data)) ? $hotel_data['address'] : set_value('address'); ?>"/>
                            <?php
                            echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group" id="mapContainer">
                        <div id="map-canvas" style="height:350px;display: none"></div>
                    </div>

                    <div class="form-group" style="display: none" id="lat-lng-div">
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Latitude" id="latitude" name="latitude" value="<?php echo (isset($hotel_data)) ? $hotel_data['latitude'] : set_value('latitude'); ?>" readonly>
                            <?php
                            echo '<label id="latitude-error" class="validation-error-label" for="latitude">' . form_error('latitude') . '</label>';
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Longitude" id="longitude" name="longitude" value="<?php echo (isset($hotel_data)) ? $hotel_data['longitude'] : set_value('longitude'); ?>" readonly>
                            <?php
                            echo '<label id="longitude-error" class="validation-error-label" for="longitude">' . form_error('longitude') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div>
                    <button class="btn btn-success" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var img1 = new Image();
    var canvas1 = document.getElementById("canvas1");
    var ctx1 = canvas1.getContext("2d");
    var valid_preview_image = 0;
    // Custom color
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });

    var infowindow = new google.maps.InfoWindow();
    var map = new google.maps.Map(document.getElementById("map-canvas"));
    var hotel_address = "";
<?php if (isset($hotel_data) && $hotel_data['latitude'] != '' && $hotel_data['longitude'] != '') { ?>
        lat = '<?php echo $hotel_data['latitude'] ?>';
        long = '<?php echo $hotel_data['longitude'] ?>';
        hotel_address = '<?php echo $hotel_data['address'] ?>';
        generateMap(lat, long);
<?php } ?>
    //Google auotocomplete for hotel address
    google.maps.event.addDomListener(window, 'load', function () {
        var options = {
            types: ['establishment'],
        };
        var hotelAddress = new google.maps.places.Autocomplete(document.getElementById('address'));
        google.maps.event.addListener(hotelAddress, 'place_changed', function () {
            var place = hotelAddress.getPlace();
            var address = place.formatted_address;
            /*Use to get latitude and longitude */

            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            var mesg = "Address: " + address;
            mesg += "\nLatitude: " + latitude;
            mesg += "\nLongitude: " + longitude;

            $("#latitude").val(latitude);
            $("#longitude").val(longitude);
            hotel_address = $("#address").val();
            generateMap(latitude, longitude);
//            setAddressFromPlace(place);
        });
    });

    //Generates the map from latitude and longitude
    function generateMap(latitude, longitude) {
        $('#map-canvas').show();
        $('#lat-lng-div').show();
        var latlngPos = new google.maps.LatLng(latitude, longitude);
        //var infowindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById("map-canvas"), {
            center: new google.maps.LatLng(latitude, longitude),
            zoom: 13,
            mapTypeId: 'roadmap'
        });
        marker = new google.maps.Marker({
            map: map,
            position: latlngPos,
            draggable: true
        });
        geocoder.geocode({'latLng': latlngPos}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                }
            }
        });
        google.maps.event.addListener(marker, 'dragend', function () {
            geocoder.geocode({
                latLng: marker.getPosition()
            }, function (responses) {
                if (responses && responses.length > 0) {
                    $('#address').val(responses[0].formatted_address);
                    infowindow.setContent(responses[0].formatted_address);
                    $("#latitude").val(marker.getPosition().lat());
                    $("#longitude").val(marker.getPosition().lng());
                    setAddressFromPlace(responses[0]);
                    //updateMarkerAddress(responses[0].formatted_address);
                } else {
                    $('#address').val('');
                    infowindow.setContent('');
                    $("#latitude").val('');
                    $("#longitude").val('');
                    //updateMarkerAddress('Cannot determine address at this location.');
                }
            });
        });
    }
    //--Foucs out event of address textbox
    $("#address").focusout(function () {
        window.setTimeout(function () {
            if ($("#latitude").val() == '' && $("#longitude").val() == '') {
                if ($("#address").val() != "") {
                    getLatLongForAdd($("#address").val());
                }
            } else if (hotel_address != $("#address").val()) {
                if ($("#address").val() != "") {
                    getLatLongForAdd($("#address").val());
                }
            }
        }, 3000);
    });
    //-- Gets the latitude and longitude from the address if its not selected from google autocomplete
    function getLatLongForAdd(address) {
        var replaced = address.split(' ').join('+');
        var replaced = replaced.split(',').join('');
        var url = "http://maps.google.com/maps/api/geocode/json?address=" + replaced + "&sensor=false";
        $.ajax({
            url: url,
            success: function (data) {
                if (data.results && data.results.length > 0) {
                    var latitude = data.results[0].geometry.location.lat;
                    var longitude = data.results[0].geometry.location.lng;
                    if (latitude && longitude) {
                        $("#latitude").val(latitude);
                        $("#longitude").val(longitude);
                        generateMap(latitude, longitude);
                    }
                }
            }
        });
    }

    // File input
    $(".previewfile-styled").uniform({
        fileButtonClass: 'action btn bg-pink-400'
    });
    // Display the preview of image on image upload
    function readpreview_photo(input) {
        $('#addlogo_to_sharedimage_div').show();
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var html = '<img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img1" class="preview_images"><img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img2" class="preview_images">';
                $('#preview_photo_div').html(html);
//                img1.onload = start1;
                img1.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
