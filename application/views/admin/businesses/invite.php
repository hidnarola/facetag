<link rel="stylesheet" href="assets/css/cropper.css">
<link rel="stylesheet" href="assets/css/cropper_main.css">
<style>
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
<script type="text/javascript" src="assets/admin/js/plugins/notifications/jgrowl.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/dropzone.min.js"></script>


<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-mail5"></i> <span class="text-semibold"><?php echo $heading; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/businesses'); ?>"><i class="icon-office"></i> Businesses</a></li>
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

            <form class="form-horizontal form-validate-jquery" action="<?php echo site_url('admin/businesses/invite') ?>" id="business_info" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Business Profile</legend>
                    <div class="form-group">
                        <label class="control-label col-lg-3">Profile logo/image
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="This will be displayed as your main profile image on the facetag app. The ideal size for this image is: 800 x 800 pixels and must be in file format png or jpg and maximum 2Mb."><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-6">
                            <div class="media no-margin-top">
                                <div class="media-left" id="image_preview_div">
                                    <?php
                                    if (isset($business_data) && $business_data['logo'] != '') {
                                        ?>
                                        <img src="assets/timthumb.php?src=<?php echo base_url() . BUSINESS_LOGO_IMAGES . $business_data['logo'] ?>&w=60&h=60&q=100&zc=2" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php
                                    } else {
                                        ?>
                                        <img src="assets/timthumb.php?src=<?php echo base_url(); ?>assets/admin/images/placeholder.jpg&w=58&h=58&q=100&zc=2" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                    <?php } ?>
                                </div>
                                <div class="media-body">
                                    <!--<input type="file" name="logo" id="logo" class="file-styled" onchange="readURL(this);">-->
                                    <!--<input type="file" class="file-styled js-fileinput img-upload" accept="image/jpeg,image/png,image/gif">-->
                                    <input type="file" class="file-styled js-fileinput img-upload" accept="image/jpeg,image/png,image/gif">
                                    <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                    <canvas class="js-editorcanvas"></canvas>
                                    <canvas class="js-previewcanvas" style="display: none;"></canvas><br>
                                    <a href="javascript:void(0);" class="js-export img-export btn_img_crop btn">Crop</a><br><br>
                                    <input type="hidden" name="cropimg" id="cropimg" value="">
                                </div>
                            </div>
                            <?php
                            if (isset($business_logo_validation))
                                echo '<label id="logo-error" class="validation-error-label" for="logo">' . $business_logo_validation . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Business/Place/Attraction Name<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="name" id="name" placeholder="Business Name" class="form-control" value="<?php echo set_value('name'); ?>">
                            <?php
                            echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <!--                    <div class="form-group">
                                            <label class="col-lg-3 control-label">Email <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" name="email" id="email" placeholder="Email" class="form-control" required="required" value="<?php echo set_value('email'); ?>">
                    <?php
                    echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                    ?>
                                            </div>
                                        </div>-->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Location
                            <a data-html="true" data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="It is important your public business 'Place' address is 'pinned' accurately, as this will be the trigger for your Visitors App proximity notification for them to 'check-in'. Once the google map populates you can zoom and manually drag the pin exactly to the correct coordinates. If you have a large Venue we recommend you position the pin close to the Guest entrance. <br/><br/> <b>Note:</b> If you are a freelance photographer working 'on-location' shoots, just enter your business/home address here and later we will show you how to create different check-in locations for each of your events when we create an Image Capture Point or ICP."><i class="icon-question4"></i></a>
                            <span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="address1" id="address1" placeholder="Address" class="form-control" value="<?php echo set_value('address1'); ?>">
                            <?php
                            echo '<label id="address1-error" class="validation-error-label" for="address1">' . form_error('address1') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group" id="mapContainer">
                        <div id="map-canvas" style="height:350px;display: none"></div>
                    </div>
                    <?php
                    $lat_lng_div_style = 'style="display: none"';
                    if (!empty(form_error('latitude')) || !empty(form_error('longitude'))) {
                        $lat_lng_div_style = '';
                    }
                    ?>
                    <div class="form-group" <?php echo $lat_lng_div_style ?> id="lat-lng-div">
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Latitude" id="latitude" name="latitude" value="<?php echo set_value('latitude'); ?>" readonly>
                            <?php
                            echo '<label id="latitude-error" class="validation-error-label" for="latitude">' . form_error('latitude') . '</label>';
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Longitude" id="longitude" name="longitude" value="<?php echo set_value('longitude'); ?>" readonly>
                            <?php
                            echo '<label id="longitude-error" class="validation-error-label" for="longitude">' . form_error('longitude') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Social links</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Facebook 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.facebook.com/facetagApp/"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-facebook2 text-indigo"></i></span>
                                <input type="text" name="facebook_url" id="facebook_url" placeholder="Facebook" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['facebook_url'] : set_value('facebook_url'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Twitter
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.twitter.com/facetagApp/"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-twitter2 text-blue"></i></span>
                                <input type="text" name="twitter_url" id="twitter_url" placeholder="Twitter" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['twitter_url'] : set_value('twitter_url'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Instagram
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.instagram.com/facetagApp/"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-instagram text-brown"></i></span>
                                <input type="text" name="instagram_url" id="instagram_url" placeholder="Instagram" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['instagram_url'] : set_value('instagram_url'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Website URL 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.facetag.com"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-earth text-green"></i></span>
                                <input type="text" name="website_url" id="website_url" placeholder="Website URL" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['website_url'] : set_value('website_url'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Buy Ticket URL </label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-ticket"></i></span>
                                <input type="text" name="ticket_url" id="ticket_url" placeholder="Buy Ticket URL" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['ticket_url'] : set_value('ticket_url'); ?>">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Contact (customer service/enquiry)</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Phone number </label>
                        <div class="col-lg-6">
                            <input type="text" name="digits" class="form-control" placeholder="Phone number" value="<?php echo (isset($business_data)) ? $business_data['contact_no'] : set_value('digits'); ?>">
                            <?php
                            echo '<label id="digits-error" class="validation-error-label" for="digits">' . form_error('digits') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email<span class="text-danger">*</span> </label>
                        <div class="col-lg-6">
                            <input type="text" name="contact_email" class="form-control" placeholder="Contact Email" value="<?php echo (isset($business_data)) ? $business_data['contact_email'] : set_value('contact_email'); ?>">
                            <?php
                            echo '<label id="contact_email-error" class="validation-error-label" for="contact_email">' . form_error('contact_email') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">About Business</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Business promotional description <span class="text-danger">*</span><br>(max 3000 (or 4000? Can't remember) characters)</label>
                        <div class="col-lg-6">
                            <textarea rows="3" cols="5" name="description" class="form-control" placeholder="Enter Business Description"><?php echo (isset($business_data)) ? $business_data['description'] : set_value('description'); ?></textarea>
                            <?php
                            echo '<label id="description-error" class="validation-error-label" for="description">' . form_error('description') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-lg-3 control-label' for='open_times'>Open times</label>
                        <div class="col-lg-7">
                            <?php foreach ($days as $key => $val) { ?>
                                <div class="form-group" id="div_mon">
                                    <label class='control-label col-sm-1' for='<?php echo $key ?>_active'><?php echo $val ?></label>
                                    <div class='col-sm-11 controls'>
                                        <div class="col-sm-4 col-lg-3">
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $key ?>_active" id="<?php echo $key ?>_active" class="styled availabilty_radio" value="1">
                                                Open
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $key ?>_active" class="styled availabilty_radio" value="0">
                                                Close
                                            </label>
                                        </div>
                                        <div class="<?php echo $key ?>_time_div">
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                                    <input type="text" name="<?php echo $key ?>_starttime" id="<?php echo $key ?>_starttime" class="form-control pickatime starttime_pick" placeholder="Start time">
                                                </div>
                                                <div id="<?php echo $key ?>_starttime-error" class="validation-error-label" for="<?php echo $key ?>_starttime" style="display: none">This field is required.</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                                    <input type="text" name="<?php echo $key ?>_endtime" id="<?php echo $key ?>_endtime" class="form-control pickatime endtime_pick" placeholder="End time">
                                                </div>
                                                <div id="<?php echo $key ?>_endtime-error" class="validation-error-label" for="<?php echo $key ?>_endtime" style="display: none">This field is required.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                    <legend class="text-semibold">
                        Upload promo feature images
                    </legend>
                    <div action="#" class="dropzone" id="dropzone_remove"></div>
                </fieldset>
                <!--<div class="text-right col-lg-9">-->
                <div>
                    <div class="form-group">
                        <div class="col-lg-1 col-sm-12">
                            <button class="btn btn-primary" type="submit" class="btn_submit" id="btn_save" data-id='2' onclick="changeAction(2)">Save <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                        <div class="col-lg-2 col-sm-3">
                            <button class="btn btn-success" type="submit" class="btn_submit"  id="btn_invite" data-id='1' onclick="changeAction(1)">Invite Business <i class="icon-mail5 position-right"></i></button>
                        </div>
                        <div class="col-lg-6 col-sm-9">
                            <div class="form-group-material">
                                <input type="email" name="email" class="form-control" placeholder="Enter Email If different from above" value="<?php echo set_value('email'); ?>">
                                <?php
                                echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript" src="assets/js/cropper.js"></script>
<script type="text/javascript" src="assets/js/invite_cropper.js"></script>
<!--<script>

</script>-->
<script type="text/javascript">
    //-- Start time and end time picker 
    $('.pickatime').pickatime({
//        format: "H:i"
    });
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });
    //-- Validation for startime and endtime on end time change event
    $(".endtime_pick").on("change", function (e) {
        ar = $(this).attr('id');
        val = ar.split('_');
        start_value = $('#' + val[0] + '_starttime').val();
        //-- Start time picker
        if (start_value != '') {
            start_val = start_value.split(':');
            start_timezone = start_val[1].split(' ');

            //-- End time picker
            end_time = $(this).val();
            end_val = end_time.split(':');
            end_timezone = end_val[1].split(' ');

            start_val = parseInt(start_val[0]) + (parseFloat(start_timezone[0]) / 100);
            end_val = parseInt(end_val[0]) + (parseFloat(end_timezone[0]) / 100);

            //-- If startime and endtime pickers timezone is equal(AM/PM)
            if (start_timezone[1] == end_timezone[1]) {
                if (start_val == end_val) {
                    $('#' + val[0] + '_starttime').val('');
                } else {
                    if (start_val == 12.3 || end_val == 12.3 || start_val == 12 || end_val == 12) {
                        if (start_val < end_val) {
                            $('#' + val[0] + '_starttime').val('');
                        }
                    } else {
                        if (start_val > end_val) {
                            $('#' + val[0] + '_starttime').val('');
                        }
                    }
                }
            } else if (start_timezone[1] == 'PM' && end_timezone[1] == 'AM') {
//                    $('#' + val[0] + '_starttime').val('');
            }
        }
    });

    //-- Validation for startime and endtime on start time change event
    $(".starttime_pick").on("change", function (e) {
        ar = $(this).attr('id');
        val = ar.split('_');
        //-- End time picker
        end_time = $('#' + val[0] + '_endtime').val();
        if (end_time != '') {
            end_val = end_time.split(':');
            end_timezone = end_val[1].split(' ');

            //-- Start time picker
            start_val = $(this).val().split(':');
            start_timezone = start_val[1].split(' ');

            start_val = parseInt(start_val[0]) + (parseFloat(start_timezone[0]) / 100);
            end_val = parseInt(end_val[0]) + (parseFloat(end_timezone[0]) / 100);

            //-- If startime and endtime pickers timezone is equal(AM/PM)
            if (start_timezone[1] == end_timezone[1]) {
                if (start_val == end_val) {
                    $('#' + val[0] + '_endtime').val('');
                } else {
                    if (start_val == 12.3 || end_val == 12.3 || start_val == 12 || end_val == 12) {
                        if (start_val < end_val) {
                            $('#' + val[0] + '_endtime').val('');
                        }
                    } else {
                        if (start_val > end_val) {
                            $('#' + val[0] + '_endtime').val('');
                        }
                    }
                }
            } else if (start_timezone[1] == 'PM' && end_timezone[1] == 'AM') {
//                    $('#' + val[0] + '_endtime').val('');
            }
        }
    });
    //-- Hide show the start time and end time text box on open/close select for Days& Hours of Operation  
    $(".availabilty_radio").on("change", function () {
        var div_id = $(this).attr("name");
        div_id = div_id.split("_");
        if ($(this).val() == 1) {
            $("." + div_id[0] + "_time_div").show();
            $('#' + div_id[0] + "_starttime").val('');
            $('#' + div_id[0] + "_endtime").val('');
        } else {
            $("." + div_id[0] + "_time_div").hide();
        }
    });

    //On form submit validate the hours of availabilty
    function validate() {
        var flag = 0;
        var days = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
        $.each(days, function (i, item) {
            if ($('#' + item + '_active').is(':checked')) {
                if ($('#' + item + '_starttime').val() == '') {
                    $('#' + item + '_starttime-error').show();
//                    $('#' + item + '_starttime').focus();
                    flag = 1;
                } else {
                    $('#' + item + '_starttime-error').hide();
                }
                if ($('#' + item + '_endtime').val() == '') {
                    $('#' + item + '_endtime-error').show();
//                    $('#' + item + '_endtime').focus();
                    flag = 1;
                } else {
                    $('#' + item + '_endtime-error').hide();
                }
            } else {
                $('#' + item + '_starttime-error').hide();
                $('#' + item + '_endtime-error').hide();
            }
        });
        if (flag == 1) {
            return false;
        }
    }
    function changeAction(invite) {
        console.log('in funct' + invite);
        url = site_url + 'admin/businesses/invite/0/' + invite;
        $('#business_info').attr('action', url);
    }


    // Display the preview of image on image upload
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    //-- google autocomplete with map
    var infowindow = new google.maps.InfoWindow();
    var map = new google.maps.Map(document.getElementById("map-canvas"));
<?php if (set_value('latitude') != '' && set_value('longitude') != '') { ?>
        lat = '<?php echo set_value('latitude') ?>';
        long = '<?php echo set_value('longitude') ?>';
        generateMap(lat, long);
<?php } ?>
    var business_address = "";
    //Google auotocomplete for business address
    google.maps.event.addDomListener(window, 'load', function () {
        var options = {
            types: ['establishment'],
        };
        var options1 = {
            types: ['geocode'],
        };
//        var businessAddress = new google.maps.places.Autocomplete(document.getElementById('address1'), options);
        var businessAddress = new google.maps.places.Autocomplete(document.getElementById('address1'));
        google.maps.event.addListener(businessAddress, 'place_changed', function () {
            var place = businessAddress.getPlace();
            var address = place.formatted_address;
            /*Use to get latitude and longitude */

            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            var mesg = "Address: " + address;
            mesg += "\nLatitude: " + latitude;
            mesg += "\nLongitude: " + longitude;

            $("#latitude").val(latitude);
            $("#longitude").val(longitude);
            business_address = $("#address1").val();
            generateMap(latitude, longitude);
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
                    $('#address1').val(responses[0].formatted_address);
                    infowindow.setContent(responses[0].formatted_address);
                    $("#latitude").val(marker.getPosition().lat());
                    $("#longitude").val(marker.getPosition().lng());
                    //updateMarkerAddress(responses[0].formatted_address);
                } else {
                    $('#address1').val('');
                    infowindow.setContent('');
                    $("#latitude").val('');
                    $("#longitude").val('');
                    //updateMarkerAddress('Cannot determine address at this location.');
                }
            });
        });
    }
    //--Foucs out event of address textbox
    $("#address1").focusout(function () {
        window.setTimeout(function () {
            if ($("#latitude").val() == '' && $("#longitude").val() == '') {
                if ($("#address1").val() != "") {
                    getLatLongForAdd($("#address1").val());
                }
            } else if (business_address != $("#address1").val()) {
                if ($("#address1").val() != "") {
                    getLatLongForAdd($("#address1").val());
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

    Dropzone.autoDiscover = false;
    // Removable thumbnails
    $("#dropzone_remove").dropzone({
        paramName: "files", // The name that will be used to transfer the file
        dictDefaultMessage: 'Drop files to upload <span>or CLICK</span>',
        maxFilesize: 2, // MB
        addRemoveLinks: true,
        autoProcessQueue: false,
        maxFiles: 12,
        acceptedFiles: '.jpg, .png, .jpeg',
        init: function () {
            this.on("error", function (file) {
                if (!file.accepted)
                    this.removeFile(file);
            });
            var submitButton = document.querySelector("#btn_invite");
            myDropzone = this;

//            $('.btn_submit').click(function (submit_e) {
            submitButton.addEventListener("click", function (submit_e) {
                if ($('#business_info').valid()) {
                    var flag = 0;
                    var days = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                    $.each(days, function (i, item) {
                        if ($('#' + item + '_active').is(':checked')) {
                            if ($('#' + item + '_starttime').val() == '') {
                                $('#' + item + '_starttime-error').show();
                                $('#' + item + '_starttime').focus();
                                flag = 1;
                            } else {
                                $('#' + item + '_starttime-error').hide();
                            }
                            if ($('#' + item + '_endtime').val() == '') {
                                $('#' + item + '_endtime-error').show();
                                $('#' + item + '_endtime').focus();
                                flag = 1;
                            } else {
                                $('#' + item + '_endtime-error').hide();
                            }
                        } else {
                            $('#' + item + '_starttime-error').hide();
                            $('#' + item + '_endtime-error').hide();
                        }
                    });
                    if (flag == 0) {

                        $('.loading').show();
                        if (myDropzone.files.length > 0) {
                            var formElement = document.querySelector("#business_info");
                            var fd = new FormData(formElement);
                            url = 'admin/businesses/invite/1/1';
                            submit_e.preventDefault();
                            $('#btn_invite').prop('disabled', true);
                            $('#btn_invite').html('Loading <i class="icon-spinner2 spinner"></i>');
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false,
//                                async: false,
                                success: function (response) {
                                    data = JSON.parse(response);

                                    if (data.error == 0) {
                                        myDropzone.options.autoProcessQueue = true;
                                        myDropzone.options.url = "admin/businesses/upload_promo_image/" + data.business_id;
                                        myDropzone.processQueue();
                                    } else {
                                        $('#business_info').submit();
                                    }
                                }
                            });
                        }
                    } else {
                        submit_e.preventDefault();
                    }
                }
            });

            var submitButton = document.querySelector("#btn_save");

//            $('.btn_submit').click(function (submit_e) {
            submitButton.addEventListener("click", function (submit_e) {
                if ($('#business_info').valid()) {
                    var flag = 0;
                    var days = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                    $.each(days, function (i, item) {
                        if ($('#' + item + '_active').is(':checked')) {
                            if ($('#' + item + '_starttime').val() == '') {
                                $('#' + item + '_starttime-error').show();
                                $('#' + item + '_starttime').focus();
                                flag = 1;
                            } else {
                                $('#' + item + '_starttime-error').hide();
                            }
                            if ($('#' + item + '_endtime').val() == '') {
                                $('#' + item + '_endtime-error').show();
                                $('#' + item + '_endtime').focus();
                                flag = 1;
                            } else {
                                $('#' + item + '_endtime-error').hide();
                            }
                        } else {
                            $('#' + item + '_starttime-error').hide();
                            $('#' + item + '_endtime-error').hide();
                        }
                    });
                    if (flag == 0) {

                        $('.loading').show();
                        if (myDropzone.files.length > 0) {
                            var formElement = document.querySelector("#business_info");
                            var fd = new FormData(formElement);
                            url = 'admin/businesses/invite/1/2';
                            submit_e.preventDefault();
                            $('#btn_save').prop('disabled', true);
                            $('#btn_save').html('Loading <i class="icon-spinner2 spinner"></i>');
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false,
//                                async: false,
                                success: function (response) {
                                    data = JSON.parse(response);

                                    if (data.error == 0) {
                                        myDropzone.options.autoProcessQueue = true;
                                        myDropzone.options.url = "admin/businesses/upload_promo_image/" + data.business_id;
                                        myDropzone.processQueue();
                                    } else {
                                        $('#business_info').submit();
                                    }
                                }
                            });
                        }
                    } else {
                        submit_e.preventDefault();
                    }
                }
            });
            myDropzone.on("addedfile", function (file) {
                if (!file.type.match(/image.*/)) {
                    if (file.type.match(/application.zip/)) {
                        myDropzone.emit("thumbnail", file, "path/to/img");
                    } else {
                        myDropzone.emit("thumbnail", file, "path/to/img");
                    }
                }
                //-- Added below code to don't allow duplicate file upload
                if (this.files.length) {
                    var _i, _len;
                    for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
                    {
                        if (this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString())
                        {
                            this.removeFile(file);
                        }
                    }
                }

            });
            myDropzone.on("complete", function (file) {
                $('.loading').hide();
                if (file.size > 20 * 1024 * 1024) {
                    return false;
                } else if (!file.type.match('image.*')) {
                    return false;
                } else {
                    if (file.type == 'image/svg+xml')
                        return false;
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        myDropzone.removeFile(file);
                        window.location.href = site_url + 'admin/businesses';

                    }
                }
            });
        },
    });


    // Initialize
    $(function () {
        // Bootstrap multiselect
        $('.multiselect').multiselect({
            checkboxName: 'vali'
        });

        // Touchspin
        $(".touchspin-postfix").TouchSpin({
            min: 0,
            max: 100,
            step: 0.1,
            decimals: 2,
            postfix: '%'
        });

        // Select2 select
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });

        // Styled checkboxes, radios
        $(".styled, .multiselect-container input").uniform({radioClass: 'choice'});

        // Styled file input
        $(".file-styled").uniform({
            fileButtonClass: 'action btn bg-blue'
        });

        var validator = $(".form-validate-jquery").validate({
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
            errorClass: 'validation-error-label',
            successClass: 'validation-valid-label',
            highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
            unhighlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
            // Different components require proper error label placement
            errorPlacement: function (error, element) {

                // Styled checkboxes, radios, bootstrap switch
                if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container')) {
                    if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                        error.appendTo(element.parent().parent().parent().parent());
                    } else {
                        error.appendTo(element.parent().parent().parent().parent().parent());
                    }
                }

                // Unstyled checkboxes, radios
                else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                    error.appendTo(element.parent().parent().parent());
                }

                // Input with icons and Select2
                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo(element.parent());
                }

                // Inline checkboxes, radios
                else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo(element.parent().parent());
                }

                // Input group, styled file input
                else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                    error.appendTo(element.parent().parent());
                } else {
                    error.insertAfter(element);
                }
            },
            validClass: "validation-valid-label",
            success: function (label) {
                label.addClass("validation-valid-label")
            },
            rules: {
                name: {
                    required: true,
                    maxlength: 100
                },
                address1: "required",
                facebook_url: "url",
                twitter_url: "url",
                instagram_url: "url",
                website_url: "url",
                ticket_url: "url",
                digits: {
                    digits: true,
                    minlength: 6,
                    maxlength: 20
                },
                contact_email: {
                    required: true,
                    email: true,
                    remote: site_url + "admin/businesses/check_contact_email"

                },
                description: {
                    minlength: 5,
                    maxlength: 4000
                },
                email: {
                    email: true,
                    remote: site_url + "admin/businesses/checkUniqueEmail"
                }

            },
            messages: {
                contact_email: {
                    remote: $.validator.format("Email address already exist!")
                },
                email: {
                    remote: $.validator.format("Email address already exist!")
                },
            }
        });
    });
</script>
