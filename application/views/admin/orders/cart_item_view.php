<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-eye4"></i> <span class="text-semibold"><?php echo $heading; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/orders'); ?>"><i class="icon-list2"></i> Orders</a></li>
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-body border-top-info">
                <div class="row">
                    <div class="col-md-4">Order BY</div>
                    <div class="col-md-8">
                        <?php
                        if ($order['user_bioimage'] != '') {
                            echo '<img src="' . USER_IMAGE_SITE_PATH . $order['user_bioimage'] . '" class="img-rounded img-preview">';
                        } else {
                            echo '<img src="assets/admin/images/no_logo.png" alt="' . $order['firstname'] . ' ' . $order['lastname'] . '" class="img-rounded img-preview">';
                        }
                        echo '<br/>';
                        echo $order['firstname'] . ' ' . $order['lastname'] . '[' . $order['email'] . ']';
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">ICP</div>
                    <div class="col-md-8">
                        <?php echo $order['icp_name']; ?>
                        <?php
                        if ($order['icp_address'] != '') {
                            echo $order['icp_address'];
                        }
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">ICP Image</div>
                    <div class="col-md-8">
                            <!--<a href="<?php echo ICP_IMAGES . $order['image'] ?>" data-popup="lightbox">-->
                        <img src="<?php echo site_url() . 'admin/businesses/get_image?image='. ICP_IMAGES . $order['image'] ?>" class="img-rounded img-preview">
                        <!--</a>-->
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">Purchased Image Type</div>
                    <div class="col-md-8">
                        <?php
                        if ($order['is_small_photo'] == 1) {
                            echo "Low resolution version";
                        } elseif ($order['is_large_photo'] == 1) {
                            echo "High resolution version";
                        } elseif ($order['is_frame'] == 1) {
                            echo "Printed Souvenir";
                        }
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">Purchased Amount</div>
                    <div class="col-md-8">
                        <?php
                        if ($order['is_small_photo'] == 1) {
                            if ($order['is_low_image_free'] == 1)
                                echo "Free";
                            else
                                echo $order['low_resolution_price'];
                        } elseif ($order['is_large_photo'] == 1) {
                            if ($order['is_high_image_free'] == 1)
                                echo "Free";
                            else
                                echo $order['high_resolution_price'];
                        } elseif ($order['is_frame'] == 1) {
                            echo $order['printed_souvenir_price'];
                        }
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">Delivery Address</div>
                    <div class="col-md-8">
                        <?php
                        echo $order['shipping_company'] . '<br/>';
                        echo $order['building_description'] . '<br/>';
                        echo $order['shipping_post_code'] . '<br/>';
                        echo "<b>Phone No</b>: " . $order['shipping_phone_no'] . '<br/>';
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">Is Delivered</div>
                    <div class="col-md-8">
                        <?php
                        if ($order['is_delivered'] == 1)
                            echo 'Yes';
                        else
                            echo 'No';
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">Ordered On</div>
                    <div class="col-md-8">
                        <?php echo date('h:i A, d-M-Y', strtotime($order['created'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
