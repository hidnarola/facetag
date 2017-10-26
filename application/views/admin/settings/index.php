<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <i class="icon-cog3"></i> <span class="text-semibold">Settings</span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Settings</li>
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
            <form class="form-horizontal form-validate-jquery" action="" id="settings_info" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Purge Facial Recognition Database Settings</legend>
                    <!--                    <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Select Time Interval to Purge Facial Recognition Database<span class="text-danger">*</span></label>
                                                    <select name="purge_facerecogdb_time_type" id="purge_facerecogdb_time_type" required="required" class="select">
                                                        <option value="">Select Time Duration</option>
                                                        <option value="week" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'week')) ? 'selected' : '' ?>>Week</option>
                                                        <option value="month" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'month')) ? 'selected' : '' ?>>Month</option>
                                                        <option value="year" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'year')) ? 'selected' : '' ?>>Year</option>
                                                    </select>
                    <?php
                    echo '<label id="purge_facerecogdb_time_type-error" class="validation-error-label" for="purge_facerecogdb_time_type">' . form_error('purge_facerecogdb_time_type') . '</label>';
                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>
                                                        <div id="time_period_label">
                    <?php
                    if ($settings) {
                        echo "Enter number of " . $settings['purge_facerecogdb_time_type'] . "s";
                    } else {
                        echo "Enter Time Period";
                    }
                    ?>
                                                            <span class="text-danger">*</span>
                                                        </div>
                                                    </label>
                                                    <input type="number" name="purge_facerecogdb_time_value" id="purge_facerecogdb_time_value" placeholder="Enter Time Period" required="required" class="form-control" value="<?php echo ($settings) ? $settings['purge_facerecogdb_time_value'] : set_value('purge_facerecogdb_time_value'); ?>" required="required"/>
                    <?php
                    echo '<label id="purge_facerecogdb_time_value-error" class="validation-error-label" for="purge_facerecogdb_time_value">' . form_error('purge_facerecogdb_time_value') . '</label>';
                    ?>
                                                    <span id="spn_purge_facerecogdb_time_value-error" class="validation-error-label"></span>
                                                </div>
                                            </div>
                                        </div>-->


                    <div class="form-group">
                        <label class="col-lg-3 control-label">Select Time Interval to Purge Facial Recognition Database<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select name="purge_facerecogdb_time_type" id="purge_facerecogdb_time_type" required="required" class="select">
                                <option value="">Select Time Duration</option>
                                <option value="week" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'week')) ? 'selected' : '' ?>>Week</option>
                                <option value="month" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'month')) ? 'selected' : '' ?>>Month</option>
                                <option value="year" <?php echo ($settings && ($settings['purge_facerecogdb_time_type'] == 'year')) ? 'selected' : '' ?>>Year</option>
                            </select>
                            <?php
                            echo '<label id="purge_facerecogdb_time_type-error" class="validation-error-label" for="purge_facerecogdb_time_type">' . form_error('purge_facerecogdb_time_type') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                            <div id="time_period_label">
                                <?php
                                if ($settings) {
                                    echo "Enter number of " . $settings['purge_facerecogdb_time_type'] . "s";
                                } else {
                                    echo "Enter Time Period";
                                }
                                ?>
                                <span class="text-danger">*</span>
                            </div>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="purge_facerecogdb_time_value" id="purge_facerecogdb_time_value" placeholder="Enter Time Period" required="required" class="form-control" value="<?php echo ($settings) ? $settings['purge_facerecogdb_time_value'] : set_value('purge_facerecogdb_time_value'); ?>" required="required"/>
                            <?php
                            echo '<label id="purge_facerecogdb_time_value-error" class="validation-error-label" for="purge_facerecogdb_time_value">' . form_error('purge_facerecogdb_time_value') . '</label>';
                            ?>
                            <span id="spn_purge_facerecogdb_time_value-error" class="validation-error-label"></span>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="text-bold">Order Processing Fees
                    <a data-popup="popover-custom" data-trigger="hover" data-placement="right" data-content="This will be deducted from each gross transaction value."><i class="icon-question4"></i></a>
                    </legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                                Credit/Debit Card Processing fees for a Domestic Card (%)<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="creditcard_debitcard_processing_fees" id="creditcard_debitcard_processing_fees" placeholder="Creditcard/Debitcard Processing fees" required="required" class="form-control" value="<?php echo ($settings) ? $settings['creditcard_debitcard_processing_fees'] : set_value('creditcard_debitcard_processing_fees'); ?>" required="required"/>
                            <?php
                            echo '<label id="creditcard_debitcard_processing_fees-error" class="validation-error-label" for="creditcard_debitcard_processing_fees">' . form_error('creditcard_debitcard_processing_fees') . '</label>';
                            ?>
                            <span id="creditcard_debitcard_processing_fees-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                                Credit/Debit Card Processing fees for an International Card (%)<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="international_card_processing_fees" id="international_card_processing_fees" placeholder="Creditcard/Debitcard Processing fees" required="required" class="form-control" value="<?php echo ($settings) ? $settings['international_card_processing_fees'] : set_value('international_card_processing_fees'); ?>" required="required"/>
                            <?php
                            echo '<label id="international_card_processing_fees-error" class="validation-error-label" for="international_card_processing_fees">' . form_error('creditcard_debitcard_processing_fees') . '</label>';
                            ?>
                            <span id="international_card_processing_fees-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                                Transaction fees (AU$)<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-4">
                            <input type="number" name="transaction_fees" id="transaction_fees" placeholder="Transaction fees" required="required" class="form-control" value="<?php echo ($settings) ? $settings['transaction_fees'] : set_value('transaction_fees'); ?>" required="required"/>
                            <?php
                            echo '<label id="transaction_fees-error" class="validation-error-label" for="transaction_fees">' . form_error('transaction_fees') . '</label>';
                            ?>
                            <span id="transaction_fees-error" class="validation-error-label"></span>
                        </div>
                    </div>
                </fieldset>
                <div class="text-right col-lg-7">
                    <button class="btn btn-success" type="submit" onclick="return confirm_alert();">Save <i class="icon-arrow-right14 position-right"></i></button>
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
    $('#purge_facerecogdb_time_value').change(function () {
        $('#spn_purge_facerecogdb_time_value-error').html('');
    });
    $('#purge_facerecogdb_time_type').change(function () {
        if ($(this).val() != '') {
            $('#time_period_label').html('Enter number of ' + $(this).val() + 's<span class="text-danger">*</span>');
        } else {
            $('#time_period_label').html('Enter Time Period<span class="text-danger">*</span>');
        }
    });
    function confirm_alert() {
        if ($('#purge_facerecogdb_time_value').val() == 0) {
            $('#purge_facerecogdb_time_value-error').remove();
            $('#spn_purge_facerecogdb_time_value-error').html('Time Period can not be zero.Please enter valid numeric value!');
        } else {
            swal({
                title: "Are you sure?",
                text: "Face Recognition Images will be deleted after every " + $('#purge_facerecogdb_time_value').val() + " " + $('#purge_facerecogdb_time_type').val() + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                confirmButtonText: "Yes, Save it!"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $('#settings_info').submit();
                }
            });
        }
        return false;
    }
</script>