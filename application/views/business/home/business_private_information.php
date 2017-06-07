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
                echo '<div class="alert alert-success">Hello ' . $this->session->userdata('facetag_admin')['firstname'] . ' ' . $this->session->userdata('facetag_admin')['lastname'] . ', Welcome to Facetag Business Admin! Please fill up more details of your Business.</div>';
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
                <fieldset class="content-group">
                    <legend class="text-bold">Business Information</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Business/Company Legal Name<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="name" id="name" placeholder="Business Name" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['name'] : set_value('name'); ?>">
                            <?php
                            echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Company/Business Registration Number<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" name="reg_no" id="reg_no" placeholder="Registration Number" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['reg_no'] : set_value('reg_no'); ?>">
                            <?php
                            echo '<label id="reg_no-error" class="validation-error-label" for="reg_no">' . form_error('reg_no') . '</label>';
                            ?>
                        </div>
                    </div>
                    <?php
                    $is_gst_yes = '';
                    $is_gst_no = '';
                    if ($this->input->post('is_gst_no') == 1) {
                        $is_gst_yes = 'checked="checked"';
                    } elseif (isset($business_data) && $business_data['is_gst_registered'] == 1) {
                        $is_gst_yes = 'checked="checked"';
                    } else {
                        $is_gst_no = 'checked="checked"';
                    }
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Are you GST/VAT/Sales tax registered?<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <label class="radio-inline">
                                <input type="radio" name="is_gst_no" class="styled is_gst_no_radio" value="1" required="required" <?php echo $is_gst_yes; ?>>
                                Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_gst_no" class="styled is_gst_no_radio" value="0" required="required" <?php echo $is_gst_no; ?>>
                                No
                            </label>
                            <?php
                            echo '<label id="is_gst_no-error" class="validation-error-label" for="is_gst_no">' . form_error('is_gst_no') . '</label>';
                            ?>
                        </div>
                    </div>
                    <?php
                    $style_gst_no = 'style="display:none"';
                    if ($this->input->post('is_gst_no') == 1) {
                        $style_gst_no = '';
                    } elseif (!empty(form_error('gst_no'))) {
                        $style_gst_no = '';
                    } elseif (isset($business_data) && $business_data['is_gst_registered'] == 1)
                        $style_gst_no = '';
                    ?>
                    <div class="form-group" <?php echo $style_gst_no ?> id="gst_vat_no_div">
                        <label class="col-lg-3 control-label">GST/VAT Number<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" name="gst_no" id="gst_no" placeholder="GST/VAT Number" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['gst_no'] : set_value('gst_no'); ?>">
                            <?php
                            echo '<label id="gst_no-error" class="validation-error-label" for="gst_no">' . form_error('gst_no') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="content-group">
                    <legend class="text-bold">Bank Details <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="This is where you want us to credit funds"><i class="icon-question4"></i></a></legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Account Name<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="account_name" id="account_name" placeholder="Account Name" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['account_name'] : set_value('account_name'); ?>">
                            <?php
                            echo '<label id="account_name-error" class="validation-error-label" for="account_name">' . form_error('account_name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">BSB<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="bsb" id="bsb" placeholder="BSB" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['bsb'] : set_value('bsb'); ?>">
                            <?php
                            echo '<label id="bsb-error" class="validation-error-label" for="bsb">' . form_error('bsb') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Account Number<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="account_number" id="account_number" placeholder="Account Number" required="required" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['account_number'] : set_value('account_number'); ?>">
                            <?php
                            echo '<label id="account_number-error" class="validation-error-label" for="account_number">' . form_error('account_number') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">PayPal Details <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="If you want facetag to transfer payment to your PayPal account then please input your paypal Email address below."><i class="icon-question4"></i></a></legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">PayPal Email Address</label>
                        <div class="col-lg-6">
                            <input type="email" name="paypal_email_address" id="paypal_email_address" placeholder="PayPal email address" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['paypal_email_address'] : set_value('paypal_email_address'); ?>">
                            <?php
                            echo '<label id="paypal_email_address-error" class="validation-error-label" for="paypal_email_address">' . form_error('paypal_email_address') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-bold control-label private-info-texts">Don't have PayPal account, Please click below to create your acount </label>&nbsp;<a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content='Please select "Business Account" as account type and connect your bank account. So you can receive payment easily.'><i class="icon-question4"></i></a>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-primary private-info-texts" href="https://www.paypal.com/us/webapps/mpp/account-selection" target="_BLANK">Create PayPal Account</a>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">ORDER PROCESSING FEES</legend>
                    <div class="form-group">
                        <label class="control-label private-info-texts">A Credit/Debit Card Processing fee of <span class="text-bold"><?php echo $settings['creditcard_debitcard_processing_fees']?>%</span> for a Domestic Card or <span class="text-bold"><?php echo $settings['international_card_processing_fees']?>%</span> for an International Card, plus an Order fee of <span class="text-bold">AU$<?php echo $settings['transaction_fees']?></span> will be applied to each gross Order value.</label>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">COMMISSION</legend>
                    <div class="form-group">
                        <label class="control-label private-info-texts">Commission charged on total digital image revenue is <span class="text-bold"><?php echo (isset($business_data) && !empty($business_data['commission_on_digital_image_sales'])) ? $business_data['commission_on_digital_image_sales_percentage'] : '25.00'; ?>%</span> or <span class="text-bold">$<?php echo (isset($business_data) && !empty($business_data['commission_on_digital_image_sales'])) ? $business_data['commission_on_digital_image_sales'] : '0.50'; ?></span> per item whichever is the greater, calculated per order.</label>
                    </div>
                    <div class="form-group">
                        <label class="control-label private-info-texts">Commission charged on total printed souvenir revenue and shipping fees is <span class="text-bold"><?php echo (isset($business_data) && !empty($business_data['commission_on_product_sales'])) ? $business_data['commission_on_product_sales_percentage'] : '25.00'; ?>%</span> or <span class="text-bold">$<?php echo (isset($business_data) && !empty($business_data['commission_on_product_sales'])) ? $business_data['commission_on_product_sales'] : '1.00'; ?></span> per item whichever is the greater.</label>
                    </div>
                    <span><b>Note :</b> Commission is only charged for sales processed through facetag, your existing sales distribution is not affected.</span>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">PROMO QUOTA</legend>
                    <div class="form-group">
                        <label class="control-label private-info-texts"><span class="text-bold"><?php echo (isset($business_data) && !empty($business_data['quota'])) ? $business_data['quota'] : '100'; ?></span></label>
                    </div>
                    <div class="form-group">
                    <label class="control-label private-info-texts">This is the number of images you can give away 'free of charge' through facetag each month. This quota does not include 'buy one, get one free' and other bundling promotions.</label>
                    </div>
                    <span><b>Note :</b> If you would like to increase your 'Promo Quota' please contact: <a href="mailto:sales@facetag.com.au">sales@facetag.com.au</a></span>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">MONTHLY SUBSCRIPTION</legend>
                    <div class="form-group">
                        <label class="control-label private-info-texts"><span class="text-bold">$<?php echo (isset($business_data) && !empty($business_data['monthly_subscription'])) ? $business_data['monthly_subscription'] : '0.00'; ?></span></label>
                    </div>
                </fieldset>
                <!--<div class="text-right">-->
                <div>
                    <button class="btn btn-success form_submit" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });
    //Google auotocomplete for business address
    //-- Hide show the start time and end time text box on open/close select for Days& Hours of Operation  
    $(".is_gst_no_radio").on("change", function () {
        if ($(this).val() == 1) {
            $("#gst_vat_no_div").show();
        } else {
            $("#gst_vat_no_div").hide();
        }
    });


    //-- Validation for startime and endtime on end time change event

    //-- Validation for startime and endtime on start time change event

    //On form submit validate the hours of availabilty
    function validate() {
        var flag = 0;
        if (flag == 1) {
            return false;
        }
    }
    // Default initialization
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });

    $(document).ready(function () {
        // Select with search
        $('.select-search').select2();
    });
    //-- Checks validation for proper URL
    function checkURL(value) {
        return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value)
    }
</script>
