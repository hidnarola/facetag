<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>facetag | Login</title>

        <!-- Global stylesheets -->
        <base href="<?php echo base_url(); ?>">
        <link href="assets/images/favicon.ico" rel='shortcut icon' type='image/x-icon'>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="assets/admin/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
        <link href="assets/admin/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="assets/admin/css/core.css" rel="stylesheet" type="text/css">
        <link href="assets/admin/css/components.css" rel="stylesheet" type="text/css">
        <link href="assets/admin/css/colors.css" rel="stylesheet" type="text/css">
        <!-- /global stylesheets -->

        <!-- Core JS files -->
        <script type="text/javascript" src="assets/admin/js/plugins/loaders/pace.min.js"></script>
        <script type="text/javascript" src="assets/admin/js/core/libraries/jquery.min.js"></script>
        <script type="text/javascript" src="assets/admin/js/core/libraries/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/admin/js/plugins/loaders/blockui.min.js"></script>
        <!-- /core JS files -->

        <!-- Theme JS files -->
        <script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
        <script type="text/javascript" src="assets/admin/js/plugins/forms/styling/uniform.min.js"></script>

        <script type="text/javascript" src="assets/admin/js/core/app.js"></script>
        <!--<script type="text/javascript" src="assets/admin/js/pages/login_validation.js"></script>-->
        <!-- /theme JS files -->
        <script>
            $(document).ready(function () {
                //--Hide the alert message 
                window.setTimeout(function () {
                    $(".alert").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 7000);
            });
        </script> 
    </head>
    <body class="login-container login-cover">
        <!-- Page container -->
        <div class="page-container">
            <!-- Page content -->
            <div class="page-content">
                <!-- Main content -->
                <div class="content-wrapper">
                    <!-- Content area -->
                    <div class="content pb-20">
                        <!-- Form with validation -->
                        <form action="" class="form-validate" method="POST">
                            <div class="panel panel-body login-form">
                                <div class="text-center">
                                    <div class="icon-object border-slate-300 text-slate-300">
                                        <!--<i class="icon-reading"></i>-->
                                        <img src="assets/images/favicon_img.png"/>
                                    </div>
                                    <h5 class="content-group">Welcome to facetag  <small class="display-block">Login to your account</small></h5>
                                </div>
                                <?php
                                if (isset($error) && !empty($error)) {
                                    echo '<div class="alert alert-danger">' . $error . '</div>';
                                }
                                ?>
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="email" class="form-control" placeholder="Email" name="email" required="required">
                                    <div class="form-control-feedback">
                                        <i class="icon-user text-muted"></i>
                                    </div>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="password" class="form-control" placeholder="Password" name="password" required="required">
                                    <div class="form-control-feedback">
                                        <i class="icon-lock2 text-muted"></i>
                                    </div>
                                </div>
                                <!-- <div class="form-group login-options">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" class="styled" checked="checked">
                                                Remember
                                            </label>
                                        </div>
    
                                        <div class="col-sm-6 text-right">
                                            <a href="login_password_recover.html">Forgot password?</a>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <button type="submit" class="btn bg-blue btn-block">Login <i class="icon-arrow-right14 position-right"></i></button>
                                </div>
                            </div>
                        </form>
                        <!-- /form with validation -->
                    </div>
                    <!-- /content area -->
                </div>
                <!-- /main content -->
            </div>
            <!-- /page content -->
        </div>
        <!-- /page container -->
        <script type="text/javascript">
            $(function () {

                // Style checkboxes and radios
                $('.styled').uniform();


                // Setup validation
                $(".form-validate").validate({
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
                        label.addClass("validation-valid-label")
                    },
                    rules: {
                        password: {
                            minlength: 5
                        }
                    },
                    messages: {
                        email: "Enter your Email",
                        password: {
                            required: "Enter your password",
                            minlength: jQuery.validator.format("At least {0} characters required")
                        }
                    }
                });

            });

        </script>
    </body>
</html>
