<!-- Reset Password form -->
<section id="register" class="sections">
    <div class="container">
        <div class="row"> 
            <!-- Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center">
                    <h1>Forgot your password?</h1>
                </div>
                <div class="subtitle text-center ">
                    <h6>Please enter your email address and we will send you an email about how to reset your password.</h6>
                </div>
                <div class="separator_wrap">
                    <div class="separator2"></div>
                </div>
            </div>
            <div class="col-sm-6 col-sm-offset-3"> 
                <?php
//                if ($this->session->flashdata('success')) {
//                    echo '<div class="alert alert-success" id="msg_success">' . $this->session->flashdata('success') . '</div>';
//                }
                ?>
                <?php
                if ($this->session->flashdata('error')) {
                    echo '<div class="alert alert-danger" id="msg_error">' . $this->session->flashdata('error') . '</div>';
                }
                ?>
                <!-- LOGIN FORM -->
                <div class="reg-wrap login-reg-wrap">
                    <form method="post" action="" class="login-form" id="loginform">
                        <div class="step-block" id="firstblock">
                            <div id="fancy-inputs">
                                <div class="form-group">
                                    <label class="input">
                                        <input type="text" name="email" id="email" class="name <?php echo (form_error('email') ? 'error-input' : '') ?>" required value="<?php echo set_value('email'); ?>">
                                        <?php
                                        $style = '';
                                        if (!empty(set_value('email'))) {
                                            $style = "style='display:none'";
                                        }
                                        ?>
                                        <span <?php echo $style ?>><span>Email</span></span> 
                                        <div id="err_email"><?php echo form_error('email'); ?></div>
                                    </label>
                                </div>
                            </div>
                            <div class="submit-button">
                                <div class="col-sm-6 text-left">
                                    <button class="btn btn-primary btnxs first_btn" name="first"><i class="fa fa-paper-plane"></i> &nbsp;Reset Password</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a href="<?php echo site_url('login') ?>" class="forgot_pwd_link">Return to login</a>
                                </div>
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
   
    $('input:text').on('focus', function () {
        $(this).next('span').show();
    });
</script>
