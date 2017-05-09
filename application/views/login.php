<!-- Login form -->
<section id="register" class="sections">
    <div class="container">
        <div class="row"> 
            <!-- Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center">
                    <h1>Login</h1>
                </div>
                <div class="separator_wrap">
                    <div class="separator2"></div>
                </div>
            </div>
            <div class="col-sm-6 col-sm-offset-3"> 
                <?php
                if ($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success">' . $this->session->flashdata('success') . '</div>';
                }
                ?>
                <?php
                if ($this->session->flashdata('error')) {
                    echo '<div class="alert alert-danger">' . $this->session->flashdata('error') . '</div>';
                }
                ?>
                <!-- LOGIN FORM -->
                <div class="reg-wrap login-reg-wrap">
                    <form method="post" action="" class="login-form" id="loginform">
                        <div class="step-block" id="firstblock">
                            <?php
                            if (!empty(validation_errors())) {
                                echo '<div class="alert alert-danger">' . validation_errors() . '</div>';
                            };
                            ?>
                            <div id="fancy-inputs">
                                <div class="form-group">
                                    <label class="input">
                                        <input type="text" name="email" id="email" class="name" required>
                                        <span><span>Email</span></span> 
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="input">
                                        <input type="password" name="password" id="password" class="name" required>
                                        <span><span>Password</span></span> 
                                    </label>
                                </div>

                            </div>
                            <div class="submit-button login_btns">
                                <div class="col-sm-12 col-md-6 col-lg-6 text-left">
                                    <button class="btn btn-primary btnxs first_btn" name="first"><i class="fa fa-paper-plane"></i> &nbsp;Login</button>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6 text-right" style="padding-top: 10px;">
                                    <a href="<?php echo site_url('reset_password') ?>" class="forgot_pwd_link">Forgot password?</a>
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
    $('input:password').on('change', function () {
        if ($(this).val().length) {
            $(this).next('span').hide();
        } else {
            $(this).next('span').show();
        }
    });
</script>