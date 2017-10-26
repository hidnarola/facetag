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
<!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>-->
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><?php
                if (isset($business_data))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?> <span class="text-semibold"><?php echo $heading; ?></span></h4>
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
            <form class="form-horizontal form-validate-jquery" action="" id="business_settings" method="post" enctype="multipart/form-data" onsubmit="return validate()">
                <fieldset class="content-group">
                    <legend class="text-bold">Business Private Information</legend>
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
                        <label class="col-lg-3 control-label">Business Registration Number</label>
                        <div class="col-lg-6">
                            <input type="text" name="reg_no" id="reg_no" placeholder="Registration Number" class="form-control" value="<?php echo (isset($business_data)) ? $business_data['reg_no'] : set_value('reg_no'); ?>">
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
                        <div class="col-lg-6">
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
                        <div class="col-lg-6">
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
                    <legend class="text-bold">ORDER PROCESSING FEES</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                            Credit/Debit Card Processing fees<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="creditcard_debitcard_processing_fees" id="creditcard_debitcard_processing_fees" placeholder="Creditcard/Debitcard Processing fees" required="required" class="form-control" value="<?php echo ($settings) ? $settings['creditcard_debitcard_processing_fees'] : set_value('creditcard_debitcard_processing_fees'); ?>" readonly=""/>
                            <?php
                            echo '<label id="creditcard_debitcard_processing_fees-error" class="validation-error-label" for="creditcard_debitcard_processing_fees">' . form_error('creditcard_debitcard_processing_fees') . '</label>';
                            ?>
                            <span id="creditcard_debitcard_processing_fees-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                            Transaction fees<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="transaction_fees" id="transaction_fees" placeholder="Transaction fees" required="required" class="form-control" value="<?php echo ($settings) ? $settings['transaction_fees'] : set_value('transaction_fees'); ?>" readonly=""/>
                            <?php
                            echo '<label id="transaction_fees-error" class="validation-error-label" for="transaction_fees">' . form_error('transaction_fees') . '</label>';
                            ?>
                            <span id="transaction_fees-error" class="validation-error-label"></span>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Commission</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Commission charged on digital image revenue<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <div class="col-lg-4">
                                <div class="form-group has-feedback has-feedback">
                                    <input type="number" name="commission_on_digital_image_sales_percentage" id="commission_on_digital_image_sales_percentage" placeholder="Commission on digital image sales" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['commission_on_digital_image_sales'])) ? $business_data['commission_on_digital_image_sales_percentage'] : '25.00'; ?>">
                                    <div class="form-control-feedback">
                                        %
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1" style="text-align: center;">OR</div>
                            <div class="col-lg-4">
                                <div class="form-group has-feedback has-feedback">
                                    <input type="number" name="commission_on_digital_image_sales" id="commission_on_digital_image_sales" placeholder="Commission on digital image sales" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['commission_on_digital_image_sales'])) ? $business_data['commission_on_digital_image_sales'] : '0.50'; ?>">
                                    <div class="form-control-feedback">
                                        $ 
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo '<label id="commission_on_digital_image_sales-error" class="validation-error-label" for="commission_on_digital_image_sales">' . form_error('commission_on_digital_image_sales') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Commission charged on total printed souvenir revenue<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <div class="col-lg-4">
                                <div class="form-group has-feedback has-feedback">
                                    <input type="number" name="commission_on_product_sales_percentage" id="commission_on_product_sales_percentage" placeholder="Commission on printed souvenir product sales" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['commission_on_product_sales'])) ? $business_data['commission_on_product_sales_percentage'] : '25.00'; ?>">
                                    <div class="form-control-feedback">
                                        %
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1" style="text-align: center;">OR</div>
                            <div class="col-lg-4">
                                <div class="form-group has-feedback has-feedback">
                                    <input type="number" name="commission_on_product_sales" id="commission_on_product_sales" placeholder="Commission on printed souvenir product sales" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['commission_on_product_sales'])) ? $business_data['commission_on_product_sales'] : '1.00'; ?>">
                                    <div class="form-control-feedback">
                                        $
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo '<label id="commission_on_product_sales-error" class="validation-error-label" for="commission_on_product_sales">' . form_error('commission_on_product_sales') . '</label>';
                            ?>
                        </div>
                    </div>
                    <span><b>Note :</b> Commission is only charged for sales processed through facetag.</span>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Promo Quota
                        <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="This is the number of images you can give away 'free of charge' through facetag each month. This quota does not include 'buy one, get one free' and other bundling promotions."><i class="icon-question4"></i></a>
                    </legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Quota for 'free of charge' images<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="quota" id="quota" placeholder="Quota for free of charge images" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['quota'])) ? $business_data['quota'] : '100'; ?>">
                            <?php
                            echo '<label id="quota-error" class="validation-error-label" for="quota">' . form_error('quota') . '</label>';
                            ?>
                        </div>
                    </div>
                    <span><b>Note :</b> If you would like to increase your 'Promo Quota' please contact: <a href="mailto:sales@facetag.com.au">sales@facetag.com.au</a></span>
                </fieldset>
                <fieldset class="content-group">
                    <legend class="text-bold">Monthly subscription</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Monthly subscription<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="number" name="monthly_subscription" id="monthly_subscription" required="required" class="form-control" value="<?php echo (isset($business_data) && !empty($business_data['monthly_subscription'])) ? $business_data['monthly_subscription'] : '0.00'; ?>">
                            <?php
                            echo '<label id="monthly_subscription-error" class="validation-error-label" for="monthly_subscription">' . form_error('monthly_subscription') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <!--                <fieldset class="content-group">
                                    <legend class="text-bold">Note :</legend>
                                    
                                    <div class="form-group">
                                        <div class="col-lg-10">
                <?php // $comments = (isset($business_data)) ? $business_data['comments'] : ""; ?>
                                            <textarea rows="5" cols="5" class="form-control" name="comments" id="comments"><?php // echo $comments;     ?></textarea>
                                        </div>
                                    </div>
                                </fieldset>-->
                <!--<div class="text-right col-lg-8">-->
                <div>
                    <button class="btn btn-success" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    $('input:checkbox').bootstrapSwitch();
</script>
<script type="text/javascript">
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });
    //On form submit validate the hours of availabilty
    function validate() {
        var flag = 0;
        if (flag == 1) {
            return false;
        }
    }

    $(document).ready(function () {
        // Select with search
        $('.select-search').select2();

        //-- Are you GST/VAT/Sales tax registered radio button change event
        //-- Display GST/VAT Number textbox on yes selection
        $(".is_gst_no_radio").on("change", function () {
            if ($(this).val() == 1) {
                $("#gst_vat_no_div").show();
            } else {
                $("#gst_vat_no_div").hide();
            }
        });
    });


    //-- Checks validation for proper URL
    function checkURL(value) {
        return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value)

    }
</script>
