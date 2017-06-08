<style>
    @media screen and (max-height: 575px){
        #rc-imageselect, .g-recaptcha {transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;}
    }
</style>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript">
    var site_url = "<?php echo site_url() ?>";
</script>
<!-- Register form -->
<section id="register" class="sections">
    <div class="container">
        <div class="row"> 

            <!-- Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center">
                    <h1>Register</h1>
                </div>
                <div class="subtitle text-center ">
                    <div id="secondblockmsg" style="display:none"><h6>Help us understand your business better, we use this information for managing and projecting data and processing requirements and for our internal product development only</h6></div>
                </div>
                <div class="separator_wrap">
                    <div class="separator2"></div>
                </div>
            </div>
            <div class="col-md-12 "> 
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
                <!-- CONTACT FORM -->
                <?php
                $first_block_style = '';
                $second_block_style = 'style="display:none"';
                $second_done = '';
                if (!empty(validation_errors()) && (empty(form_error('firstname')) && empty(form_error('lastname')) && empty(form_error('email')) && empty(form_error('phone_no')) && empty(form_error('password')) && empty(form_error('confirm_password')))) {
                    $first_block_style = 'style="display:none"';
                    $second_block_style = '';
                    $second_done = 'done';
                }
                ?>
                <div class="reg-wrap">
                    <div class="step-wrap">
                        <ul>
                            <li class="first done" id="firstblocktab"><a href="javascript:void(0)" class="first_btn_prev"><span class="number">1</span> Contact</a></li>
                            <li class="current <?php echo $second_done ?>" id="secondblocktab"><a href="javascript:void(0)" class="first_btn"><span class="number">2</span> Additional info</a></li>
                        </ul>
                    </div>
                    <div id="error-message" class="text-center"></div>

                    <form method="post" action="" class="contact-form" id="registerform">
                        <div class="step-block" id="firstblock" <?php echo $first_block_style ?>>
                            <?php
                            if (!empty(validation_errors())) {
                                echo '<div class="alert alert-danger" ' . $first_block_style . '>Please fill out valid required fields!</div>';
                            };
                            ?>
                            <div id="fancy-inputs">
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label class="input">
                                                <!--<input type="text" name="firstname" id="firstname" class="name error-input">-->
                                                <input type="text" name="firstname" id="firstname" class="name <?php echo (form_error('firstname') ? 'error-input' : '') ?>" value="<?php echo (!empty($this->session->userdata('subscribe_first_name'))) ? $this->session->userdata('subscribe_first_name') : set_value('firstname'); ?>">
                                                <span <?php echo (!empty(set_value('firstname')) || !empty($this->session->userdata('subscribe_first_name'))) ? 'style="display:none"' : '' ?>><span>First name</span></span> 
                                                <?php echo form_error('firstname'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label class="input">
                                                <input type="text" name="lastname" id="lastname" class="name <?php echo (form_error('lastname') ? 'error-input' : '') ?>" value="<?php echo (!empty($this->session->userdata('subscribe_last_name'))) ? $this->session->userdata('subscribe_last_name') : set_value('lastname'); ?>">
                                                <span <?php echo (!empty(set_value('lastname')) || !empty($this->session->userdata('subscribe_last_name'))) ? 'style="display:none"' : '' ?>><span>Last name</span></span> 
                                                <?php echo form_error('lastname'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label class="input">
                                                <input type="text" name="email" id="email" class="name <?php echo (form_error('email') ? 'error-input' : '') ?>" value="<?php echo (!empty($this->session->userdata('subscribe_email'))) ? $this->session->userdata('subscribe_email') : set_value('email'); ?>">
                                                <span <?php echo (!empty(set_value('email')) || !empty($this->session->userdata('subscribe_email'))) ? 'style="display:none"' : '' ?>><span>Email</span></span> 
                                                <?php echo form_error('email'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label class="input">
                                                <input type="text" name="phone_no" id="phone_no" class="name <?php echo (form_error('phone_no') ? 'error-input' : '') ?>" value="<?php echo set_value('phone_no'); ?>">
                                                <span <?php echo (!empty(set_value('phone_no'))) ? 'style="display:none"' : '' ?>><span>Phone No</span></span> 
                                                <?php echo form_error('phone_no'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <!--<label class="input margin-top-10">-->
                                            <label class="input">
                                                <input type="password" name="password" id="password" class="<?php echo (form_error('password') ? 'error-input' : '') ?>"  value="<?php echo set_value('password'); ?>">
                                                <span <?php echo (!empty(set_value('password'))) ? 'style="display:none"' : '' ?>><span>Please create a Password</span></span> 
                                                <?php echo form_error('password'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label class="input">
                                                <input type="password" name="confirm_password" id="confirm_password" class="<?php echo (form_error('confirm_password') ? 'error-input' : '') ?>"  value="<?php echo set_value('confirm_password'); ?>">
                                                <span <?php echo (!empty(set_value('confirm_password'))) ? 'style="display:none"' : '' ?>><span>Confirm Password</span></span> 
                                                <?php echo form_error('confirm_password'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="multi margin-top-10 accept_cls">
                                            <div class="checkbox-wrapper display-block">
                                                <?php
                                                $accept_selected = '';
                                                if ($this->input->post('accept_terms'))
                                                    $accept_selected = 'checked';
                                                ?>
                                                <input type="checkbox" name="accept_terms" class="form-control <?php if (form_error('accept_terms')) echo 'error-input'; ?>" id="accept_terms" <?php echo $accept_selected ?>/>&nbsp;
                                                <label for="accept_terms">I accept facetags' <a class="terms-link" target="_blank" href="<?php echo site_url('legal/terms'); ?>">Terms and Conditions</a></label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                        </div>
                                        <?php echo form_error('accept_terms'); ?>
                                    </div>
                                </div>
                                <div class="submit-button">
                                    <button class="btn btn-primary btnxs first_btn" name="first"><i class="fa fa-paper-plane"></i> &nbsp;Next</button>
                                </div>
                            </div>
                        </div>

                        <div class="step-block" id="secondblock" <?php echo $second_block_style ?>>
                            <?php
                            if (!empty(validation_errors())) {
                                echo '<div class="alert alert-danger">Please fill out valid required fields!</div>';
                            };
                            ?>
                            <div id="fancy-inputs">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="input">
                                                <input type="text" name="business_name" id="business_name" class="name <?php echo (form_error('business_name') ? 'error-input' : '') ?>" value="<?php echo set_value('business_name'); ?>">
                                                <span <?php echo (!empty(set_value('business_name'))) ? 'style="display:none"' : '' ?>><span>Attraction, Place or Business Name</span></span> 
                                                <?php echo form_error('business_name'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="multi">
                                            <label>Select all that apply:</label>
                                            <select id="business_type" multiple="multiple" name="business_type[]">
                                                <?php
                                                $posted_array = array();
                                                if ($this->input->post('business_type'))
                                                    $posted_array = $this->input->post('business_type');
                                                foreach ($business_types as $business_type) {
                                                    $selected = '';
                                                    if (in_array($business_type['id'], $posted_array)) {
                                                        $selected = 'selected';
                                                    }
                                                    echo '<option value="' . $business_type['id'] . '" ' . $selected . '>' . $business_type['name'] . '</option>';
                                                }
                                                ?>
                                                <option value="0" <?php if (set_value('business_type[]') && in_array(0, $posted_array)) echo 'selected'; ?>>Other</option>
                                            </select>
                                        </div>
                                        <?php if (form_error('business_type[]')) echo '<br/>' . form_error('business_type[]'); ?>
                                        <?php
                                        $type_style = 'style="display: none;"';
                                        $type_class = '';
                                        if (form_error('other_business_type')) {
                                            $type_style = '';
                                            $type_class = 'error-input';
                                        }
                                        ?>
                                        <div id="other_business_type_txtbox"  <?php echo $type_style ?>>
                                            <input type="text" name="other_business_type" class="sale_value-form-control <?php echo $type_class ?>" />
                                            <?php echo form_error('other_business_type'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="multi margin-top-10">
                                            <label>Average daily visitor attendance?</label>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor" class="form-control <?php if (form_error('visitor')) echo 'error-input'; ?>" value="<500" id="visitor1" <?php if ($this->input->post('visitor') == '<500') echo 'checked' ?>/>&nbsp;
                                                <label for="visitor1"><500</label> 
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor" class="form-control" value="501-2000" id="visitor2" <?php if (set_value('visitor') == '501-2000') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor2">501-2000</label> 
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor" class="form-control" value="2001-10,000" id="visitor3" <?php if (set_value('visitor') == '2001-10,000') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor3">2001-10,000</label> 
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor" class="form-control" value="10,000+" id="visitor4" <?php if (set_value('visitor') == '10,000+') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor4">10,000+ </label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                        </div>
                                        <?php echo form_error('visitor'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="multi margin-top-10">
                                            <label>Do you currently take Photographs of your Visitor/Guests?</label>
                                            <?php
                                            $posted_array = array();
                                            if ($this->input->post('take_photos'))
                                                $posted_array = $this->input->post('take_photos');
                                            ?>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="take_photos" value="1" id="take_photos_radio1" class="take_photos" <?php if (set_value('take_photos') && $posted_array == 1) echo 'checked'; ?> checked=""/>&nbsp;
                                                <label for="take_photos_radio1">YES</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="take_photos" value="0" id="take_photos_radio2" class="take_photos" <?php if (set_value('take_photos') && $posted_array == 0) echo 'checked'; ?>/>&nbsp;
                                                <label for="take_photos_radio2">NO</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 avg-num-of-photos">
                                        <div class="multi margin-top-10">
                                            <label>Average number of Visitor photographs taken daily?</label>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor_photo" value="<100" id="visitor_photo1" <?php if ($this->input->post('visitor_photo') == '<100') echo 'checked' ?> class="<?php if (form_error('visitor_photo')) echo 'error-input'; ?>"/>&nbsp;
                                                <label for="visitor_photo1">less than 100</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor_photo" value="101-300" id="visitor_photo2" <?php if (set_value('visitor_photo') == '101-300') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor_photo2">101-300</label> 
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor_photo" value="301-600" id="visitor_photo3" <?php if (set_value('visitor_photo') == '301-600') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor_photo3">301-600</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor_photo" value="601-1000" id="visitor_photo4" <?php if (set_value('visitor_photo') == '601-1000') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor_photo4">601-1000</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <div class="checkbox-wrapper display-block">
                                                <input type="checkbox" name="visitor_photo" value="1000+" id="visitor_photo5" <?php if (set_value('visitor_photo') == '1000+') echo 'checked' ?>/>&nbsp;  
                                                <label for="visitor_photo5">1000+</label>
                                                <span class="checkbox-checked"></span>
                                            </div>
                                            <?php echo form_error('visitor_photo'); ?>
                                        </div>
                                    </div>
                                    <!--                                    <div class="col-sm-6">
                                                                            <div class="multi margin-top-10">
                                                                                <label>Would you like to distribute your Visitor photographs:</label>
                                                                                <div class="checkbox-wrapper display-block">
                                                                                    <input type="checkbox" name="distribute" value="free" id="free" <?php if (set_value('distribute') == 'free') echo 'checked' ?> class="<?php if (form_error('distribute')) echo 'error-input'; ?>"/>&nbsp; 
                                                                                    <label for="free">Free</label>
                                                                                    <span class="checkbox-checked"></span>
                                                                                </div>
                                                                                <div class="checkbox-wrapper display-block">
                                                                                    <input type="checkbox" name="distribute" value="forsale" id="forsale" <?php if (set_value('distribute') == 'forsale') echo 'checked' ?>/>&nbsp;
                                                                                    <label for="forsale">For Sale (approx. average price)</label>
                                                                                    <span class="checkbox-checked"></span>
                                                                                </div>
                                                                                <div class="checkbox-wrapper display-block">
                                                                                    <input type="checkbox" name="distribute" value="both" id="both" <?php if (set_value('distribute') == 'both') echo 'checked' ?>/>&nbsp;
                                                                                    <label for="both"> Both</label> 
                                                                                    <span class="checkbox-checked"></span>
                                                                                </div>
                                    <?php // echo form_error('distribute'); ?>
                                    <?php
//                                    $sale_style = 'style="display: none;"';
//                                    $sale_class = '';
//                                    if (form_error('sale_value')) {
//                                        $sale_style = '';
//                                        $sale_class = 'error-input';
//                                    } else if (set_value('distribute') == 'forsale' || set_value('distribute') == 'both') {
//                                        $sale_style = '';
//                                    }
                                    ?>
                                                                                <div id="sale_value_txtbox" <?php echo $sale_style ?>>
                                                                                    <input type="text" name="sale_value" class="sale_value-form-control <?php echo $sale_class ?>" placeholder="Enter Approx. average price" value="<?php echo set_value('sale_value') ?>"/>
                                                                                    <div style="margin-left: 15px;"><?php echo form_error('sale_value'); ?></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>-->
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="input margin-top-10">
                                                <input type="text" name="hear_about" id="hear_about" class="name <?php echo (form_error('hear_about') ? 'error-input' : '') ?>" value="<?php echo set_value('hear_about') ?>">
                                                <span <?php echo (!empty(set_value('hear_about'))) ? 'style="display:none"' : '' ?>><span>How did you hear about us?</span></span> 
                                                <?php echo form_error('hear_about'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_SITE_KEY ?>" style="margin-top:20px;"></div>
                                            <?php echo form_error('g-recaptcha-response'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-button">
                                <button class="btn btn-primary btnxs first_btn_prev" name="submit" data-loading-text="Loading..."><i class="fa fa-paper-plane"></i> &nbsp;Previous</button>
                                <button class="btn btn-primary btnxs" id="finish_btn" name="submit" data-loading-text="Loading..." type="submit"><i class="fa fa-paper-plane"></i> &nbsp;Join</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {

        //-- Intialize business type as mulriselect box
        $('#business_type').multiselect();

        //-- If Other type is selected then show the teztbox to enter the other types
        $('#business_type').change(function () {
            if ($.inArray('0', $(this).val()) != -1)
            {
                $('#other_business_type_txtbox').show();
            } else {
                $('#other_business_type_txtbox').hide();
            }
        });
        $("#registerform").validate({
            errorClass: 'error-custom error-input',
            errorElement: 'span',
            errorPlacement: function (error, element) {
                if (element.parents('div').hasClass("checkbox-wrapper")) {
                    error.insertAfter(element.next('label').next('span'));
                } else {
                    error.insertAfter(element.next('span'));
                }
            },
            rules: {
                firstname: "required",
                lastname: "required",
                email: {
                    required: true,
                    email: true,
                    remote: site_url + "register/checkUniqueEmail"
                },
                phone_no: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    digits: true,
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                },
                accept_terms: "required"
            },
            messages: {
                password: {
                    required: "Please provide a password",
//                        minlength: "Your password must be at least 5 characters long"
                },
                email: {
                    remote: $.validator.format("Email address is already in use!")
                },
                accept_terms: "Please accept our policy",
            }
        });


        //-- Next Button click show second step
        $('.first_btn').click(function () {

            if ($("#registerform").valid()) {
                $('#firstblock').fadeOut();
                $('#secondblock,#secondblockmsg').fadeIn();

                $('#secondblocktab').addClass('done');
                $('html, body').animate({
                    scrollTop: $("#register").offset().top
                });
            }
            return false;
        });

        //-- First Button click show first step
        $('.first_btn_prev').click(function () {
            $('#firstblock').fadeIn();
            $('#secondblock,#secondblockmsg').fadeOut();
            $('#secondblocktab').removeClass('done');
            $('#secondblocktab').addClass('current');
            $('html, body').animate({
                scrollTop: $("#register").offset().top
            });
            return false;
        });

        //-- Distribute checkbox selection
        $("input:checkbox[name='distribute']").change(function () {
            if (($(this).val() == 'forsale' && $(this).is(':checked')) || ($(this).val() == 'both' && $(this).is(':checked'))) {
                $('#sale_value_txtbox').show();
            } else {
                $('#sale_value_txtbox').hide();
            }
        });


        //-- Allow only one checkbox selection
        $("input:checkbox").on('click', function () {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method                 
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });
        $('input:password').on('change', function () {
            if ($(this).val().length) {
                $(this).next('span').hide();
            } else {
                $(this).next('span').show();
            }
        });

        //-- Show textbox span on focus
        $('input:text').on('focus', function () {
            $(this).next('span').show();
        });
    });
</script>