<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-key"></i> <span class="text-semibold"><?php echo $heading; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
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
            <?php } elseif ($this->session->flashdata('error')) {
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
            <form class="form-horizontal form-validate-jquery" action="" id="reset_password-form" method="post" >

                <div class="form-group">
                    <label class="col-lg-3 control-label">Old Password <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control" required="required">
                        <?php
                        echo '<label id="old_password" class="validation-error-label" for="old_password">' . form_error('old_password') . '</label>';
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">New Password <span class="text-danger">*</span></label>
                    <div class="col-lg-4">     
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required="required">
                        <?php
                        echo '<label id="new_password" class="validation-error-label" for="new_password">' . form_error('new_password') . '</label>';
                        ?>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-lg-3 control-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" data-rule-equalto="#new_password" required="required">
                        <?php
                        echo '<label id="confirm_password" class="validation-error-label" for="confirm_password">' . form_error('confirm_password') . '</label>';
                        ?>
                    </div>
                </div>
                <div class="text-right col-lg-3 col-lg-offset-4">
                    <button class="btn btn-success" type="submit" name="reset_submit" id="reset_submit">Change Password <i class="icon-key position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
