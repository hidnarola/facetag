<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>-->

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
            <form class="form-horizontal form-validate-jquery" action="" id="user_info" method="post" enctype="multipart/form-data">

                <fieldset class="content-group">
                    <legend class="text-bold">Profile Details</legend>
                    <div class="form-group">
                        <label class="control-label col-lg-3">Profile Picture</label>
                        <div class="col-lg-6">
                            <div class="media no-margin-top">
                                <div class="media-left" id="image_preview_div">
                                    <?php if ($this->session->userdata('facetag_admin')['profile_image'] != '' && file_exists(PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'])) { ?>
                                        <img src="<?php echo PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'] ?>" alt="<?php echo $this->session->userdata('facetag_admin')['firstname'] ?>" style="width: 58px; height: 58px; border-radius: 2px;" >
                                        <?php
                                    } else {
                                        ?>
                                        <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                    <?php } ?>
                                </div>

                                <div class="media-body">
                                    <input type="file" name="profile_image" id="profile_image" class="file-styled" onchange="readURL(this);">
                                    <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                </div>
                            </div>
                            <?php
                            if (isset($user_image_validation))
                                echo '<label id="profile_image-error" class="validation-error-label" for="logo">' . $user_image_validation . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="email" name="email" id="email" placeholder="Enter Email" required="required" class="form-control" value="<?php echo (isset($user_data)) ? $user_data['email'] : set_value('email'); ?>">
                            <?php
                            echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">First Name<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" required="required" class="form-control" value="<?php echo (isset($user_data)) ? $user_data['firstname'] : set_value('firstname'); ?>">
                            <?php
                            echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Last Name<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" required="required" class="form-control" value="<?php echo (isset($user_data)) ? $user_data['lastname'] : set_value('lastname'); ?>">
                            <?php
                            echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Gender<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="male" class="styled" value="male" <?php if (isset($user_data) && $user_data['gender'] == 'male') echo 'checked' ?>>
                                Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="female" class="styled" value="female" <?php if (isset($user_data) && $user_data['gender'] == 'female') echo 'checked' ?>>
                                Female
                            </label>
                            <?php
                            echo '<label id="gender-error" class="validation-error-label" for="gender">' . form_error('gender') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Phone No<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="phone_no" id="phone_no" placeholder="Enter Phone No" required="required" class="form-control" value="<?php echo (isset($user_data)) ? $user_data['phone_no'] : set_value('phone_no'); ?>">
                            <?php
                            echo '<label id="phone_no-error" class="validation-error-label" for="phone_no">' . form_error('phone_no') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div class="text-right col-lg-9">
                    <button class="btn btn-success form_submit" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
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
        // Initialize
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
                    }
                    else {
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
                }

                else {
                    error.insertAfter(element);
                }
            },
            validClass: "validation-valid-label",
            success: function (label) {
//            label.addClass("validation-valid-label").text("Success.")
                label.addClass("validation-valid-label")
            },
            rules: {
                email: {
                    required: true,
                    email: true,
                    remote: site_url + "business/home/checkUniqueEmail"

                },
                firstname: "required",
                lastname: "required",
                gender: "required",
                phone_no: {
                    required: true,
                    phonenumber: true
                },
            },
            messages: {
                email: {
                    remote: $.validator.format("Email address already exist!")
                },
            }
        });
    });

    /*Validate Phone number field..[author KU]*/
    jQuery.validator.addMethod("phonenumber", function (value, element) {
        return this.optional(element) || /^[0-9\-\(\)\s]+$/.test(value);
    }, "Please enter valid phone number");
</script>
