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
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/picker_date.js"></script>-->
<!--<script type="text/javascript" src="assets/admin/js/pages/form_checkboxes_radios.js"></script>-->
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-profile"></i> <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('facetag_admin')['is_ever_loggedin'] == 0) {
                echo "<div class='alert alert-success'>Hello " . $this->session->userdata('facetag_admin')['firstname'] . " " . $this->session->userdata('facetag_admin')['lastname'] . ", Welcome to your business admin panel. Let's get your facetag app profile page created first and then we can set up your photo store by clicking on the 'Manage Image Capture Point's (ICP's)' in the menu on the left.</div>";
            }
            ?>
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
            <form class="form-horizontal form-validate-jquery" action="" id="business_info" method="post" enctype="multipart/form-data" onsubmit="return validate()">
                <div class="row">
                    <div class="col-md-8 col-sm-8">
                        <fieldset class="content-group">
                            <legend class="text-bold">Business Profile</legend>
                            <div class="form-group">
                                <label class="control-label col-lg-3">Profile logo/image
                                    <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="This will be displayed as your main profile image on the facetag app. The ideal size for this image is: 800 x 800 pixels and must be in file format png or jpg and maximum 2Mb."><i class="icon-question4"></i></a>
                                </label>
                                <div class="col-lg-9">
                                    <div class="media no-margin-top">
                                        <div class="media-left" id="image_preview_div">
                                            <?php
                                            if (isset($business_data) && $business_data['logo'] != '') {
                                                $required = '';
                                                ?>
                                                <img src="assets/timthumb.php?src=<?php echo base_url() . BUSINESS_LOGO_IMAGES . $business_data['logo'] ?>&w=60&h=60&q=100&zc=2" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                                <?php
                                            } else {
                                                $required = 'required="required"';
                                                ?>
                                                <img src="assets/timthumb.php?src=<?php echo base_url(); ?>assets/admin/images/placeholder.jpg&w=58&h=58&q=100&zc=2" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } ?>
                                        </div>
                                        <div class="media-body">
<!--                                            <input type="file" name="logo" id="logo" class="file-styled" onchange="readURL(this);" <?php echo $required ?>>
                                            <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>-->

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
                                <div class="col-lg-9">
                                    <input type="text" name="name" id="name" placeholder="Business Name" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['name'] : set_value('name'); ?>">
                                    <?php
                                    echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">Address1
                                    <a data-html="true" data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="It is important your public business 'Place' address is 'pinned' accurately, as this will be the trigger for your Visitors App proximity notification for them to 'check-in'. Once the google map populates you can zoom and manually drag the pin exactly to the correct coordinates. If you have a large Venue we recommend you position the pin close to the Guest entrance. <br/><br/> <b>Note:</b> If you are a freelance photographer working 'on-location' shoots, just enter your business/home address here and later we will show you how to create different check-in locations for each of your events when we create an Image Capture Point or ICP."><i class="icon-question4"></i></a>
                                    <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="address1" id="address1" class="form-control" required="required" placeholder="Address1" value="<?php echo (isset($business_data)) ? $business_data['address1'] : set_value('address1'); ?>"/>
                                    <?php
                                    echo '<label id="address1-error" class="validation-error-label" for="address1">' . form_error('address1') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group" id="mapContainer">
                                <div id="map-canvas" style="height:350px;display: none"></div>
                            </div>
                            <div class="form-group" style="display: none" id="lat-lng-div">
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" placeholder="Latitude" id="latitude" name="latitude" value="<?php echo (isset($business_data)) ? $business_data['latitude'] : set_value('latitude'); ?>" readonly>
                                    <?php
                                    echo '<label id="latitude-error" class="validation-error-label" for="latitude">' . form_error('latitude') . '</label>';
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" placeholder="Longitude" id="longitude" name="longitude" value="<?php echo (isset($business_data)) ? $business_data['longitude'] : set_value('longitude'); ?>" readonly>
                                    <?php
                                    echo '<label id="longitude-error" class="validation-error-label" for="longitude">' . form_error('longitude') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <?php
                            $check_val = 0;
                            $checked = '';
                            if ($business_data['display_text'] == 1) {
                                $check_val = 1;
                                $checked = 'checked';
                            } else {
                                $check_val = 0;
                                $checked = '';
                            }
                            ?>
                            <div class="form-group">
                                <input type="checkbox" name="address_text" id="address_text" class="" onclick="$(this).val(this.checked ? 1 : 0)" value="<?php echo $check_val; ?>" <?php echo $checked; ?>/>&nbsp;
                                <span class="checkbox-checked"><b>Display text instead of google address</b></span>
                            </div>
                            <div class="form-group address-text">
                                <label class="col-lg-3 control-label">Address display text
                                    <a data-html="true" data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="If you check 'Display text instead of google address' option, this address will be display to user."><i class="icon-question4"></i></a>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" name="address_display_text" id="address_display_text" class="form-control" required="required" placeholder="Address Text" value="<?php echo (isset($business_data)) ? $business_data['address_text'] : set_value('address_text'); ?>"/>
                                    <?php
                                    echo '<label id="address_display_text-error" class="validation-error-label" for="address_display_text">' . form_error('address_display_text') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <!--                <div class="form-group">
                                                <label class="col-lg-3 control-label">Street Number<span class="text-danger">*</span></label>
                                                <div class="col-lg-3">
                                                    <input type="text" name="street_no" id="street_no" placeholder="Street Number" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['street_no'] : set_value('street_no'); ?>">
                            <?php
                            echo '<label id="street_no-error" class="validation-error-label" for="street_no">' . form_error('street_no') . '</label>';
                            ?>
                                                </div>
                                                <label class="col-lg-2 control-label">Street Name<span class="text-danger">*</span></label>
                                                <div class="col-lg-4">
                                                    <input type="text" name="street_name" id="street_name" placeholder="Street Name" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['street_name'] : set_value('street_name'); ?>">
                            <?php
                            echo '<label id="street_name-error" class="validation-error-label" for="street_name">' . form_error('street_name') . '</label>';
                            ?>
                                                </div>
                                            </div>-->
                            <!--                <div class="form-group">
                                                <label class="col-lg-3 control-label">Address2</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="address2" class="form-control" placeholder="Address2" value="<?php echo (isset($business_data)) ? $business_data['address2'] : set_value('address2'); ?>"/>
                            <?php
                            echo '<label id="address2-error" class="validation-error-label" for="address2">' . form_error('address2') . '</label>';
                            ?>
                                                </div>
                                            </div>-->
                            <!--                <div class="form-group">
                                                <label class="col-lg-3 control-label">Country<span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select class="select-search" name="country_id" id="country_id" data-placeholder="Select a country..." required="required">
                                                        <option value=""></option>
                            <?php
                            foreach ($countries as $country) {
                                $selected = '';
                                if (isset($business_data) && $business_data['country_id'] == $country['id'])
                                    $selected = 'selected';
                                echo '<option value="' . $country['id'] . '" ' . $selected . ' data-shortname="' . $country['shortname'] . '">' . $country['name'] . '</option>';
                            }
                            ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">State<span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select class="select-search" name="state_id" id="state_id" data-placeholder="Select a state..." required="required">
                            <?php
                            foreach ($states as $state) {
                                $selected = '';
                                if (isset($business_data) && $business_data['state_id'] == $state['id'])
                                    $selected = 'selected';
                                echo '<option value="' . $state['id'] . '" ' . $selected . '>' . $state['name'] . '</option>';
                            }
                            ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">City<span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select class="select-search" name="city_id" id="city_id" data-placeholder="Select a city..." required="required">
                            <?php
                            foreach ($cities as $city) {
                                $selected = '';
                                if (isset($business_data) && $business_data['city_id'] == $city['id'])
                                    $selected = 'selected';
                                echo '<option value="' . $city['id'] . '" ' . $selected . '>' . $city['name'] . '</option>';
                            }
                            ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">Suburb</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="suburb" id="suburb" placeholder="Suburb" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['suburb'] : set_value('suburb'); ?>">
                            <?php
                            echo '<label id="suburb-error" class="validation-error-label" for="suburb">' . form_error('suburb') . '</label>';
                            ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">Zipcode<span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="zipcode" id="zipcode" placeholder="Zipcode" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['zipcode'] : set_value('zipcode'); ?>">
                            <?php
                            echo '<label id="zipcode-error" class="validation-error-label" for="zipcode">' . form_error('zipcode') . '</label>';
                            ?>
                                                </div>
                                            </div>-->
                        </fieldset>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="app-profile-screen">
                            <h4>Business profile screen in App
                                <a data-html="true" data-popup="popover-custom" data-trigger="hover" data-placement="top" data-content="This is the preview of business profile screen in App! Your business profile will look simillar like this"><i class="icon-question4"></i></a>
                            </h4>
                            <img src="<?php echo base_url(); ?>assets/images/app_profile_screen.png">
                        </div>
                    </div>
                </div>
                <fieldset class="content-group">
                    <legend class="text-bold">Social links</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Facebook 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.facebook.com/facetagApp/"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-facebook2 text-indigo"></i></span>
                                <input type="text" name="facebook_url" id="facebook_url" placeholder="Facebook" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['facebook_url'] : set_value('facebook_url'); ?>">
                            </div>
                            <span id="spn_facebook_url-error" class="validation-error-label" for="facebook_url"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Twitter 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.twitter.com/facetagApp/"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-twitter2 text-blue"></i></span>
                                <input type="text" name="twitter_url" id="twitter_url" placeholder="Twitter" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['twitter_url'] : set_value('twitter_url'); ?>">
                            </div>
                            <span id="spn_twitter_url-error" class="validation-error-label" for="twitter_url"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Instagram 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.instagram.com/facetagApp/"><i class="icon-question4"></i></a></label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-instagram text-brown"></i></span>
                                <input type="text" name="instagram_url" id="instagram_url" placeholder="Instagram" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['instagram_url'] : set_value('instagram_url'); ?>">
                            </div>
                            <span id="spn_instagram_url-error" class="validation-error-label" for="instagram_url"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Website URL 
                            <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="Eg: https://www.facetag.com"><i class="icon-question4"></i></a>
                        </label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-earth text-green"></i></span>
                                <input type="text" name="website_url" id="website_url" placeholder="Website URL" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['website_url'] : set_value('website_url'); ?>">
                            </div>
                            <span id="spn_website_url-error" class="validation-error-label" for="website_url"></span>
                        </div>
                    </div>
                    <!--                    <div class="form-group">
                                            <label class="col-lg-3 control-label">Buy Ticket URL </label>
                                            <div class="col-lg-9">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-ticket"></i></span>
                                                    <input type="text" name="ticket_url" id="ticket_url" placeholder="Buy Ticket URL" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['ticket_url'] : set_value('ticket_url'); ?>">
                                                </div>
                                                <span id="spn_ticket_url-error" class="validation-error-label" for="ticket_url"></span>
                                            </div>
                                        </div>-->
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Contact (customer service/enquiry)</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Phone number </label>
                        <div class="col-lg-9">
                            <input type="text" name="digits" class="form-control" placeholder="Phone number" value="<?php echo (isset($business_data)) ? $business_data['contact_no'] : set_value('digits'); ?>">
                            <?php
                            echo '<label id="digits-error" class="validation-error-label" for="digits">' . form_error('digits') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email </label>
                        <div class="col-lg-9">
                            <input type="text" name="contact_email" class="form-control" placeholder="Contact Eamil" value="<?php echo (isset($business_data)) ? $business_data['contact_email'] : set_value('contact_email'); ?>">
                            <?php
                            echo '<label id="contact_email-error" class="validation-error-label" for="contact_email">' . form_error('contact_email') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">About Business</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Business promotional description <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <textarea rows="3" cols="5" name="description" id="description" class="form-control" placeholder="Enter Business Description"><?php echo (isset($business_data)) ? $business_data['description'] : set_value('description'); ?></textarea>
                            <?php
                            echo '<label id="description-error" class="validation-error-label" for="description">' . form_error('description') . '</label>';
                            ?>
                            <span id="spn_description-error" class="validation-error-label" for="description"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Business promotional description (in chinese) </label>
                        <div class="col-lg-9">
                            <textarea rows="3" cols="5" name="ch_description" id="ch_description" class="form-control" placeholder="Enter Business Description in chinese"><?php echo (isset($business_data)) ? $business_data['ch_description'] : set_value('ch_description'); ?></textarea>
                            <span id="ch_description-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-lg-3 control-label' for='open_times'>Open times</label>
                        <div class="col-lg-9">
                            <?php foreach ($days as $key => $val) { ?>
                                <div class="form-group" id="div_mon">
                                    <label class='control-label col-sm-1' for='<?php echo $key ?>_active'><?php echo $val ?></label>
                                    <div class='col-sm-11 controls'>
                                        <div class="col-sm-4 col-lg-3">
                                            <?php
                                            $open_checked = '';
                                            $close_checked = '';
                                            $start_time = '';
                                            $end_time = '';
                                            $time_div_style = '';
                                            if (!empty($open_times)) {
                                                if ($open_times->$key->open == 1) {
                                                    $open_checked = 'checked="checked"';
                                                    $start_time = date('h:i A', strtotime($open_times->$key->starttime));
                                                    $end_time = date('h:i A', strtotime($open_times->$key->endtime));
                                                } else {
                                                    $close_checked = 'checked="checked"';
                                                    $open_checked = '';
                                                    $time_div_style = 'style="display:none"';
                                                }
                                            }
                                            ?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $key ?>_active" id="<?php echo $key ?>_active" class="styled availabilty_radio" value="1" <?php echo $open_checked ?>>
                                                Open
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $key ?>_active" class="styled availabilty_radio" value="0" <?php echo $close_checked ?>>
                                                Close
                                            </label>
                                        </div>
                                        <div class="<?php echo $key ?>_time_div" <?php echo $time_div_style ?>>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                                    <input type="text" name="<?php echo $key ?>_starttime" id="<?php echo $key ?>_starttime" class="form-control pickatime starttime_pick" placeholder="Start time" value="<?php echo $start_time ?>">
                                                </div>
                                                <div id="<?php echo $key ?>_starttime-error" class="validation-error-label" for="<?php echo $key ?>_starttime" style="display: none">This field is required.</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                                    <input type="text" name="<?php echo $key ?>_endtime" id="<?php echo $key ?>_endtime" class="form-control pickatime endtime_pick" placeholder="End time" value="<?php echo $end_time ?>">
                                                </div>
                                                <div id="<?php echo $key ?>_endtime-error" class="validation-error-label" for="<?php echo $key ?>_endtime" style="display: none">This field is required.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group"></fieldset>
                <!--<div class="text-right">-->
                <div>
                    <button class="btn btn-success form_submit" id="btnsubmit" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    /**
     * @file Allows uploading, cropping (with automatic resizing) and exporting
     * of images.
     * @author Billy Brown
     * @license MIT
     * @version 2.1.0
     */

    /** Class used for uploading images. */
    class Uploader {
        /**
         * <p>Creates an Uploader instance with parameters passed as an object.</p>
         * <p>Available parameters are:</p>
         * <ul>
         *  <li>exceptions {function}: the exceptions handler to use, function that takes a string.</li>
         *  <li>input {HTMLElement} (required): the file input element. Instantiation fails if not provided.</li>
         *  <li>types {array}: the file types accepted by the uploader.</li>
         * </ul>
         *
         * @example
         * var uploader = new Uploader({
         *  input: document.querySelector('.js-fileinput'),
         *  types: [ 'gif', 'jpg', 'jpeg', 'png' ]
         * });
         * *
         * @param {object} options the parameters to be passed for instantiation
         */
        constructor(options) {
            if (!options.input) {
                throw '[Uploader] Missing input file element.';
            }
            this.fileInput = options.input;
            this.types = options.types || ['gif', 'jpg', 'jpeg', 'png'];
        }

        /**
         * Listen for an image file to be uploaded, then validate it and resolve with the image data.
         */
        listen(resolve, reject) {
            this.fileInput.onchange = (e) => {
                // Do not submit the form
                e.preventDefault();

                // Make sure one file was selected
                if (!this.fileInput.files || this.fileInput.files.length !== 1) {
                    reject('[Uploader:listen] Select only one file.');
                }

                let file = this.fileInput.files[0];
                let reader = new FileReader();
                // Make sure the file is of the correct type
                if (!this.validFileType(file.type)) {
                    reject(`[Uploader:listen] Invalid file type: ${file.type}`);
                } else {
                    // Read the image as base64 data
                    reader.readAsDataURL(file);
                    // When loaded, return the file data
                    reader.onload = (e) => resolve(e.target.result);
                }
            };
        }

        /** @private */
        validFileType(filename) {
            // Get the second part of the MIME type
            let extension = filename.split('/').pop().toLowerCase();
            // See if it is in the array of allowed types
            return this.types.includes(extension);
        }
    }

    function squareContains(square, coordinate) {
        return coordinate.x >= square.pos.x
                && coordinate.x <= square.pos.x + square.size.x
                && coordinate.y >= square.pos.y
                && coordinate.y <= square.pos.y + square.size.y;
    }

    /** Class for cropping an image. */
    class Cropper {
        /**
         * <p>Creates a Cropper instance with parameters passed as an object.</p>
         * <p>Available parameters are:</p>
         * <ul>
         *  <li>size {object} (required): the dimensions of the cropped, resized image. Must have 'width' and 'height' fields. </li>
         *  <li>limit {integer}: the longest side that the cropping area will be limited to, resizing any larger images.</li>
         *  <li>canvas {HTMLElement} (required): the cropping canvas element. Instantiation fails if not provided.</li>
         *  <li>preview {HTMLElement} (required): the preview canvas element. Instantiation fails if not provided.</li>
         * </ul>
         *
         * @example
         * var editor = new Cropper({
         *  size: { width: 128, height: 128 },
         *  limit: 600,
         *  canvas: document.querySelector('.js-editorcanvas'),
         *  preview: document.querySelector('.js-previewcanvas')
         * });
         *
         * @param {object} options the parameters to be passed for instantiation
         */
        constructor(options) {
            // Check the inputs
            if (!options.size) {
                throw 'Size field in options is required';
            }
            if (!options.canvas) {
                throw 'Could not find image canvas element.';
            }
            if (!options.preview) {
                throw 'Could not find preview canvas element.';
            }

            // Hold on to the values
            this.imageCanvas = options.canvas;
            this.previewCanvas = options.preview;
            this.c = this.imageCanvas.getContext("2d");


            // Images larger than options.limit are resized
            this.limit = options.limit || 500; // default to 600px
            // Create the cropping square with the handle's size
            this.crop = {
                size: {x: options.size.width, y: options.size.height},
                pos: {x: 0, y: 0},
                handleSize: 10
            };

            // Set the preview canvas size
            this.previewCanvas.width = options.size.width;
            this.previewCanvas.height = options.size.height;

            // Bind the methods, ready to be added and removed as events
            this.boundDrag = this.drag.bind(this);
            this.boundClickStop = this.clickStop.bind(this);
        }

        /**
         * Set the source image data for the cropper.
         *
         * @param {String} source the source of the image to crop.
         */
        setImageSource(source) {
            this.image = new Image();
            this.image.src = source;
            this.image.onload = (e) => {
                // Perform an initial render
                this.render();
                // Listen for events on the canvas when the image is ready
                this.imageCanvas.onmousedown = this.clickStart.bind(this);
            }
        }

        /**
         * Export the result to a given image tag.
         *
         * @param {HTMLElement} img the image tag to export the result to.
         */
        export(img) {
//                                        img.setAttribute('src', this.previewCanvas.toDataURL());
            $("#cropimg").val(this.previewCanvas.toDataURL());
            var html = '<img src="' + this.previewCanvas.toDataURL() + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
            $('#image_preview_div').html(html);
        }

        /** @private */
        render() {
            this.c.clearRect(0, 0, this.imageCanvas.width, this.imageCanvas.height);
            this.displayImage();
            this.preview();
            this.drawCropWindow();
            $('.js-editorcanvas').show();
            $('.js-export.img-export').show();
        }

        /** @private */
        clickStart(e) {
            // Get the click position and hold onto it for the expected mousemove
            const position = {x: e.offsetX, y: e.offsetY};
            this.lastEvent = {
                position: position,
                resizing: this.isResizing(position),
                moving: this.isMoving(position)
            };
            // Listen for mouse movement and mouse release
            this.imageCanvas.addEventListener('mousemove', this.boundDrag);
            this.imageCanvas.addEventListener('mouseup', this.boundClickStop);
        }

        /** @private */
        clickStop(e) {
            // Stop listening for mouse movement and mouse release
            this.imageCanvas.removeEventListener("mousemove", this.boundDrag);
            this.imageCanvas.removeEventListener("mouseup", this.boundClickStop);
        }

        /** @private */
        isResizing(coord) {
            const size = this.crop.handleSize;
            const handle = {
                pos: {
                    x: this.crop.pos.x + this.crop.size.x - size / 2,
                    y: this.crop.pos.y + this.crop.size.y - size / 2
                },
                size: {x: size, y: size}
            };
            return squareContains(handle, coord);
        }

        /** @private */
        isMoving(coord) {
            return squareContains(this.crop, coord);
        }

        /** @private */
        drag(e) {
            const position = {
                x: e.offsetX,
                y: e.offsetY
            };
            // Calculate the distance that the mouse has travelled
            const dx = position.x - this.lastEvent.position.x;
            const dy = position.y - this.lastEvent.position.y;
            // Determine whether we are resizing, moving, or nothing
            if (this.lastEvent.resizing) {
                this.resize(dx, dy);
            } else if (this.lastEvent.moving) {
                this.move(dx, dy);
            }
            // Update the last position
            this.lastEvent.position = position;
            this.render();
        }

        /** @private */
        resize(dx, dy) {
            let handle = {
                x: this.crop.pos.x + this.crop.size.x,
                y: this.crop.pos.y + this.crop.size.y
            };
            // Maintain the aspect ratio
            const amount = Math.abs(dx) > Math.abs(dy) ? dx : dy;
            // Make sure that the handle remains within image bounds
            if (this.inBounds(handle.x + amount, handle.y + amount)) {
                this.crop.size.x += amount;
                this.crop.size.y += amount;
            }
        }

        /** @private */
        move(dx, dy) {
            // Get the opposing coordinates
            const tl = {
                x: this.crop.pos.x,
                y: this.crop.pos.y
            };
            const br = {
                x: this.crop.pos.x + this.crop.size.x,
                y: this.crop.pos.y + this.crop.size.y
            };
            // Make sure they are in bounds
            if (this.inBounds(tl.x + dx, tl.y + dy) &&
                    this.inBounds(br.x + dx, tl.y + dy) &&
                    this.inBounds(br.x + dx, br.y + dy) &&
                    this.inBounds(tl.x + dx, br.y + dy)) {
                this.crop.pos.x += dx;
                this.crop.pos.y += dy;
            }
        }

        /** @private */
        displayImage() {
            // Resize the original to the maximum allowed size
            const ratio = this.limit / Math.max(this.image.width, this.image.height);
            this.image.width *= ratio;
            this.image.height *= ratio;
            // Fit the image to the canvas
            this.imageCanvas.width = this.image.width;
            this.imageCanvas.height = this.image.height;
            this.c.drawImage(this.image, 0, 0, this.image.width, this.image.height);
        }

        /** @private */
        drawCropWindow() {
            const pos = this.crop.pos;
            const size = this.crop.size;
            const radius = this.crop.handleSize / 2;
            this.c.strokeStyle = 'red';
            this.c.fillStyle = 'red';
            // Draw the crop window outline
            this.c.strokeRect(pos.x, pos.y, size.x, size.y);
            // Draw the draggable handle
            const path = new Path2D();
            path.arc(pos.x + size.x, pos.y + size.y, radius, 0, Math.PI * 2, true);
//                                        path.arc(100,75,50,0,2*Math.PI);
            this.c.fill(path);
        }

        /** @private */
        preview() {
            const pos = this.crop.pos;
            const size = this.crop.size;
            // Fetch the image data from the canvas
            const imageData = this.c.getImageData(pos.x, pos.y, size.x, size.y);
            if (!imageData) {
                return false;
            }
            // Prepare and clear the preview canvas
            const ctx = this.previewCanvas.getContext('2d');
            ctx.clearRect(0, 0, this.previewCanvas.width, this.previewCanvas.height);
            // Draw the image to the preview canvas, resizing it to fit
            ctx.drawImage(this.imageCanvas,
                    // Top left corner coordinates of image
                    pos.x, pos.y,
                    // Width and height of image
                    size.x, size.y,
                    // Top left corner coordinates of result in canvas
                    0, 0,
                    // Width and height of result in canvas
                    this.previewCanvas.width, this.previewCanvas.height);
        }

        /** @private */
        inBounds(x, y) {
            return squareContains({
                pos: {x: 0, y: 0},
                size: {
                    x: this.imageCanvas.width,
                    y: this.imageCanvas.height
                }
            }, {x: x, y: y});
        }
    }

    function exceptionHandler(message) {
        console.log(message);
    }

// Auto-resize the cropped image
    var dimensions = {width: 128, height: 128};

    try {
        var uploader = new Uploader({
            input: document.querySelector('.js-fileinput'),
            types: ['gif', 'jpg', 'jpeg', 'png']
        });

        var editor = new Cropper({
            size: dimensions,
            canvas: document.querySelector('.js-editorcanvas'),
            preview: document.querySelector('.js-previewcanvas')
        });

        // Make sure both were initialised correctly
        if (uploader && editor) {
            // Start the uploader, which will launch the editor
            uploader.listen(editor.setImageSource.bind(editor), (error) => {
                throw error;
            });
        }
        // Allow the result to be exported as an actual image
        var img = document.createElement('img');
        document.body.appendChild(img);
        document.querySelector('.js-export').onclick = (e) => editor.export(img);

    } catch (error) {
        exceptionHandler(error.message);
    }
</script>
<script type="text/javascript">
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });
    var infowindow = new google.maps.InfoWindow();
    var map = new google.maps.Map(document.getElementById("map-canvas"));
    var business_address = "";
<?php if (isset($business_data) && $business_data['latitude'] != '' && $business_data['longitude'] != '') { ?>
        lat = '<?php echo $business_data['latitude'] ?>';
        long = '<?php echo $business_data['longitude'] ?>';
        business_address = '<?php echo $business_data['address1'] ?>';
        generateMap(lat, long);
<?php } ?>

    //Google auotocomplete for business address
    google.maps.event.addDomListener(window, 'load', function () {
        var options = {
            types: ['establishment'],
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
            setAddressFromPlace(place);
        });
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
    $(".is_gst_no_radio").on("change", function () {
        if ($(this).val() == 1) {
            $("#gst_vat_no_div").show();
        } else {
            $("#gst_vat_no_div").hide();
        }
    });

    $('.pickatime').pickatime({
//        format: "H:i"
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

            /*
             console.log('startval is ' + parseInt(start_val[0]));
             console.log('start after value' + parseFloat(start_timezone[0]) / 100);
             console.log('starttime is ' + start_timezone[1]);
             
             console.log('endval is ' + parseInt(end_val[0]));
             console.log('endtime is ' + end_timezone[1]); */


            start_val = parseInt(start_val[0]) + (parseFloat(start_timezone[0]) / 100);
            end_val = parseInt(end_val[0]) + (parseFloat(end_timezone[0]) / 100);

//        console.log('start value is' + start_val);
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
//                    $('#' + val[0] + '_starttime').val('');
            }

            /*
             if (parseInt(start_val[0]) >= parseInt(end_val[0])) {
             v = parseInt(start_val[0]) + 1;
             console.log(v);
             }*/
        }
    });

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
    //Get the states for state drop down on country selection
    function getStates() {
        var country_id = $('#country_id').val();
        $('#state_id').val('');
        $('#city_id').val('');
        $('#city_id').select2();
        $.ajax({
            url: site_url + 'business/home/getStates',
            type: 'POST',
            dataType: 'json',
            data: {country_id: country_id},
            success: function (data) {
                var options = "<option value=''>Please select state</option>";
                for (var i = 0; i < data.length; i++) {
                    options += '<option value=' + data[i]['id'] + '>' + data[i]['name'] + '</option>';
                }
                $('#state_id').empty().append(options);
                $('#state_id').select2();
            }
        });
    }
    //Get the cities for city drop down on state selection
    function getCities() {
        var state_id = $('#state_id').val();
        $('#city_id').val('');
        $.ajax({
            url: site_url + 'business/home/getCities',
            type: 'POST',
            dataType: 'json',
            data: {state_id: state_id},
            success: function (data) {
                var options = "<option value=''>Please select city</option>";
                for (var i = 0; i < data.length; i++) {
                    options += '<option value=' + data[i]['id'] + '>' + data[i]['name'] + '</option>';
                }
                $('#city_id').empty().append(options);
                $('#city_id').select2();
            }
        });
    }
    //On form submit validate the hours of availabilty
    function validate() {
        var flag = 0;
        if ($('#facebook_url').val() != '') {
            if (!checkURL($('#facebook_url').val())) {
                $('#facebook_url').focus();
                $('#spn_facebook_url-error').html('Please enter valid Facebook URL');
                flag = 1;
            } else {
                $('#spn_facebook_url-error').html('');
            }
        }
        if ($('#twitter_url').val() != '') {
            if (!checkURL($('#twitter_url').val())) {
                $('#twitter_url').focus();
                $('#spn_twitter_url-error').html('Please enter valid Twitter URL');
                flag = 1;
            } else {
                $('#spn_twitter_url-error').html('');
            }
        }
        if ($('#instagram_url').val() != '') {
            if (!checkURL($('#instagram_url').val())) {
                $('#instagram_url').focus();
                $('#spn_instagram_url-error').html('Please enter valid Instagram URL');
                flag = 1;
            } else {
                $('#spn_instagram_url-error').html('');
            }
        }
        if ($('#website_url').val() != '') {
            if (!checkURL($('#website_url').val())) {
                $('#website_url').focus();
                $('#spn_website_url-error').html('Please enter valid Website URL');
                flag = 1;
            } else {
                $('#spn_website_url-error').html('');
            }
        }

        if ($('#description').val().length < 5) {
            $('#description').focus();
            $('#spn_description-error').html('Please enter atleast 5 characters');
            flag = 1;
        } else {
            $('#spn_description-error').html('');
        }
//        var ch_desc = $("#ch_description").val();
//        if (!ch_desc.match(/[\u3002\uff0c\uff1b]/)) {
//            $('#ch_description').focus();
//            $('#ch_description-error').html('Please enter valid chinese description');
//            flag = 1;
//        } else {
//            $('#ch_description-error').html('');
//        }

        /*
         if ($('#ticket_url').val() != '') {
         if (!checkURL($('#ticket_url').val())) {
         $('#ticket_url').focus();
         $('#spn_ticket_url-error').html('Please enter valid Ticket URL');
         flag = 1;
         } else {
         $('#spn_ticket_url-error').html('');
         }
         } */
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
        } else {
            $.ajax({
                url: '<?php echo base_url(); ?>business/profile',
                type: 'POST',
                data: $(this).serialize(),
                dataType: "json",
                success: function (result) {
//                    alert(result.icpexist);
                    if (result.icpexist == 1) {
                        window.location.href = 'business/home';
                    } else {
                        swal({
                            title: "Would you like to create your facetag photo store?",
                            text: "It only takes a few minutes and its FREE!",
                            type: "info",
                            showCancelButton: true,
                            cancelButtonText: 'Later',
                            confirmButtonColor: "#297DCE",
                            confirmButtonText: "Yes"
                        },
                                function (isConfirm) {
                                    if (!isConfirm) {
                                        // if later:
                                        window.location.href = 'business/promo_images';
                                    } else {
                                        // if yes:
                                        window.location.href = 'business/icps/add';
                                    }
                                });
                        return false;
                    }
                }
            });
        }
    }
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
                    setAddressFromPlace(responses[0]);
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
    // Default initialization
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });

    $(document).ready(function () {
        var display_address = <?php echo $business_data["display_text"]; ?>;
        if (display_address == 0) {
            $(".address-text").hide();
        }
        $("#address_text").change(function () {
            if (this.checked) {
                $(".address-text").show();
            } else {
                $(".address-text").hide();
            }
        });
        //Get states of particular country on country selection
        $('#country_id').on('change', function () {
            getStates();
        });
        //Get cities of particular states on state selection
        $('#state_id').on('change', function () {
            getCities();
        });
        // Select with search
        $('.select-search').select2();
    });
    function extractFromAdress(components, type, address) {
        var strNearByTypeValue = '';
        for (var i = 0; i < components.length; i++) {
            for (var j = 0; j < components[i].types.length; j++) {
                if (components[i].types[j] == type) {
                    //return getExcetAddress(components[i].short_name,components[i].long_name,address);
                    return components[i].short_name;
                } else {
                    if (type && components[i].types[j].indexOf(type) > -1) {
                        //strNearByTypeValue=getExcetAddress(components[i].short_name,components[i].long_name,address);
                        strNearByTypeValue = components[i].short_name;
                    }
                }
            }
        }
        return strNearByTypeValue;
    }
    //-- Find address component from google autocomplete address
    function setAddressFromPlace(place) {
        objUserAddress = document.getElementById('address1');
        biz_street_no = extractFromAdress(place.address_components, "street_number", objUserAddress.value);
        biz_street_name = extractFromAdress(place.address_components, "premise", objUserAddress.value);
        biz_address = extractFromAdress(place.address_components, "route", objUserAddress.value);
        biz_city = '';
        tmpcity1 = extractFromAdress(place.address_components, "locality", objUserAddress.value);
        if (tmpcity1 && (objUserAddress.value).indexOf(tmpcity1) > -1) {
            biz_city = tmpcity1;
        }
        if (biz_city == '') {
            tmpcity2 = extractFromAdress(place.address_components, "sublocality", objUserAddress.value);
            if (tmpcity2 && (objUserAddress.value).indexOf(tmpcity2) > -1) {
                biz_city = tmpcity2;
            }
        }
        if (biz_city == '') {
            tmpcity3 = extractFromAdress(place.address_components, "city", objUserAddress.value);
            if (tmpcity3 && (objUserAddress.value).indexOf(tmpcity3) > -1) {
                biz_city = tmpcity3;
            }
        }
        if (biz_city == '') {
            tmpcity4 = extractFromAdress(place.address_components, "neighborhood", objUserAddress.value);
            if (tmpcity4 && (objUserAddress.value).indexOf(tmpcity4) > -1) {
                biz_city = tmpcity4;
            } else if (tmpcity1 != '') {
                biz_city = tmpcity1;
            } else if (tmpcity2 != '') {
                biz_city = tmpcity2;
            } else if (tmpcity3 != '') {
                biz_city = tmpcity3;
            }
        }
        biz_state = extractFromAdress(place.address_components, "administrative_area_level_1", objUserAddress.value);
        if (biz_state == '') {
            biz_state = extractFromAdress(place.address_components, "state", objUserAddress.value);
        }
        if (biz_state == '') {
            biz_state = extractFromAdress(place.address_components, "province", objUserAddress.value);
        }
        if (biz_state == '') {
            biz_state = extractFromAdress(place.address_components, "administrative_area_level_2", objUserAddress.value);
        }

        biz_zipcode = extractFromAdress(place.address_components, "postal_code", objUserAddress.value);
        if (biz_zipcode == '') {
            biz_zipcode = extractFromAdress(place.address_components, "zip_code", objUserAddress.value);
        }
        if (biz_zipcode == '') {
            biz_zipcode = extractFromAdress(place.address_components, "postal", objUserAddress.value);
        }
        if (biz_zipcode == '') {
            biz_zipcode = extractFromAdress(place.address_components, "zip", objUserAddress.value);
        }

        biz_country = extractFromAdress(place.address_components, "country", objUserAddress.value);
        if (!biz_street_no) {
            if (objUserAddress.value.match(/^[0-9]+/)) {
                arrMatchStreet = objUserAddress.value.match(/^[0-9]+/);
                biz_street_no = arrMatchStreet[0];
            }
        }

        var address = '';
        if (place.address_components) {
            address = [
                (biz_street_no || ''),
                (biz_address || ''),
                (biz_city || ''),
                (biz_state || ''),
                (biz_country || '')
            ].join(' ');
        }
        if (typeof place.name == 'undefined') {
            place.name = biz_street_no + ' ' + biz_address;
        }
//        console.log('<div><strong>' + place.name + '</strong><br>' + address);
        $('#street_no').val(biz_street_no);
        $('#street_name').val(biz_street_name);
        $('#zipcode').val(biz_zipcode);

        sel_country_val = $("#country_id option[data-shortname='" + biz_country + "']").val();
        $('#country_id').val(sel_country_val);
        $('#country_id').select2();

        console.log('city ' + biz_city);
        console.log('state ' + biz_state);
        if (sel_country_val != '') {
            getStates();
            setTimeout(function () {
                sel_state_val = $("#state_id option").filter(function () {
                    return $(this).text() === biz_state;
                }).first().attr("value");
                if (sel_state_val != '' && sel_state_val != undefined) {
                    $('#state_id').val(sel_state_val);
                    $('#state_id').select2();
                    getCities();

                    setTimeout(function () {
                        sel_city_val = $("#city_id option").filter(function () {
                            return $(this).text() === biz_city;
                        }).first().attr("value");
                        if (sel_city_val != '' && sel_city_val != undefined) {
                            $('#city_id').val(sel_city_val);
                            $('#city_id').select2();
                        }
                    }, 3000);
                }
            }, 3000);
        }
    }
    //-- Checks validation for proper URL
    function checkURL(value) {
        return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value)
    }
</script>
