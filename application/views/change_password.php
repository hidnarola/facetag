<!-- Reset Password form -->
<section id="register" class="sections">
    <div class="container">
        <div class="row"> 
            <!-- Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center">
                    <h1>Reset Password</h1>
                </div>
                <div class="subtitle text-center ">
                    <h6>Please enter your new password and confirm password.</h6>
                </div>
                <div class="separator_wrap">
                    <div class="separator2"></div>
                </div>
            </div>
            <div class="col-sm-6 col-sm-offset-3"> 
                <?php
                if ($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success" id="msg_success">' . $this->session->flashdata('success') . '</div>';
                }
                ?>
                <?php
                if ($this->session->flashdata('error')) {
                    echo '<div class="alert alert-danger" id="msg_error">' . $this->session->flashdata('error') . '</div>';
                }
                ?>
                <!-- LOGIN FORM -->
                <div class="reg-wrap login-reg-wrap">
                    <form method="post" action="<?php echo site_url('reset_password/update_password') ?>" class="login-form" id="loginform">
                        <div class="step-block" id="firstblock">
                            <div id="fancy-inputs">
                                <div class="form-group">
                                    <label class="input">
                                        <input type="password" id="password" name="password" class="name <?php echo (form_error('password') ? 'error-input' : '') ?>">
                                        <span><span>Password</span></span> 
                                        <div id="err_password"><?php echo form_error('password'); ?></div>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="input">
                                        <input type="password" id="con_password" name="con_password" class="name <?php echo (form_error('con_password') ? 'error-input' : '') ?>">
                                        <span><span>Confirm Password</span></span> 
                                        <div id="err_con_password"><?php echo form_error('con_password'); ?></div>
                                    </label>
                                </div>
                            </div>
                            <div class="submit-button">
                                <input type="hidden" name="verification_code" value="<?php echo $verification_code ?>">
                                <button class="btn btn-primary btnxs first_btn" name="first"><i class="fa fa-paper-plane"></i> &nbsp;Reset Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $('#msg_success').delay(5000).fadeOut('slow');
    $('#msg_error').delay(5000).fadeOut('slow');

    $('input:password').on('change', function () {
        if ($(this).val().length) {
            $(this).next('span').hide();
        } else {
            $(this).next('span').show();
        }
    });
</script>