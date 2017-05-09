<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($hotel_data))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?> 
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/icps'); ?>"><i class="icon-lan2 position-left"></i> ICPs</a></li>            
            <li><a href="<?php echo site_url('business/hotels/index/' . $icp_data['id']); ?>"><i class="icon-city"></i> Hotels</a></li>            
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
            <form class="form-horizontal form-validate-jquery" action="" id="business_info" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Hotel Information</legend>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Hotel Name<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" name="name" id="name" placeholder="Enter Hotel Name" required="required" class="form-control" value="<?php echo (isset($hotel_data)) ? $hotel_data['name'] : set_value('name'); ?>">
                            <?php
                            echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Hotel Address<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <textarea name="address" id="address" placeholder="Enter Hotel Address" required="required" class="form-control" rows="3"><?php echo (isset($hotel_data)) ? $hotel_data['address'] : set_value('address'); ?></textarea>
                            <?php
                            echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div>
                    <button class="btn btn-success" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
