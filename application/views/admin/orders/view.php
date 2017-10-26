<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
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
        <div class="col-lg-12">
            <div class="panel border-left-lg border-left-success invoice-grid timeline-content">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="media-right">
                                <a href="javascript:void(0);">
                                    <?php echo '<img src="' . USER_IMAGE_SITE_PATH . $cart_detail['user_bioimage'] . '" class="img-circle">'; ?>
                                </a>
                            </div>
                            <h6 class="text-semibold no-margin-top"><?php echo $cart_detail['firstname']; ?></h6>
                            <ul class="list list-unstyled">
                                <li>Email : &nbsp;<?php echo $cart_detail['email']; ?></li>
                                <li>Ordered on: <span class="text-semibold"><?php echo date('h:i A, d-M-Y', strtotime($cart_detail['created'])); ?></span></li>
                            </ul>
                        </div>

                        <div class="col-sm-6">
                            <h6 class="text-semibold text-right no-margin-top">$<?php echo $cart_detail['total_amount']; ?></h6>
                            <ul class="list list-unstyled text-right">
                                <!--<li>Method: <span class="text-semibold">SWIFT</span></li>-->
                                <li class="dropdown">
                                    Status: &nbsp;
                                    <a href="#" class="label bg-green-400 dropdown-toggle" data-toggle="dropdown">Paid</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--        <div class="col-md-12">
                    <div class="panel panel-body border-top-info">
                        <div class="row">
                            <div class="col-md-4">Order BY</div>
                            <div class="col-md-8">
        <?php
        if ($cart_detail['user_bioimage'] != '') {
            echo '<img src="' . USER_IMAGE_SITE_PATH . $cart_detail['user_bioimage'] . '" class="img-rounded img-preview">';
        } else {
            echo '<img src="assets/admin/images/no_logo.png" alt="' . $cart_detail['firstname'] . ' ' . $cart_detail['lastname'] . '" class="img-rounded img-preview">';
        }
        echo '<br/>';
//                        echo $cart_detail['firstname'] . ' ' . $cart_detail['lastname'] . '[' . $cart_detail['email'] . ']';
        ?>
                                <span class="cart-holder"><?php echo $cart_detail['firstname'] . ' ' . $cart_detail['lastname'] . '<br>[' . $cart_detail['email'] . ']'; ?></span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">Ordered On</div>
                            <div class="col-md-8">
        <?php echo date('h:i A, d-M-Y', strtotime($cart_detail['created'])); ?>
                            </div>
                        </div>
                    </div>
                </div>-->
    </div>
    <div class="panel panel-flat">
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Order By</th>
                    <th>ICP</th>
                    <th>ICP Image</th>
                    <th>Image Type</th>
                    <th>Business</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Ordered On</th>
                    <th style="width:13% !important">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($orders) {
                    $i = 1;
                    foreach ($orders as $order) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <?php
                                if ($order['user_bioimage'] != '') {
                                    echo '<img src="' . USER_IMAGE_SITE_PATH . $order['user_bioimage'] . '" height="55px" width="55px" class="img-circle">';
                                } else {
                                    echo '<img src="assets/admin/images/no_logo.png" height="55px" width="55px" alt="' . $order['firstname'] . ' ' . $order['lastname'] . '">';
                                }
                                echo '<br/>';
                                echo $order['firstname'];
                                ?>
                            </td>
                            <td><?php echo $order['icp_name']; ?></td>
                            <td>
        <!--                                <a href="<?php echo site_url() . 'business/icps/get_image?image=' . ICP_IMAGES . $order['image'] ?>" data-popup="lightbox">-->
                                <img src="<?php echo site_url() . 'admin/businesses/get_image?image=' . ICP_IMAGES . $order['image'] ?>" class="img-rounded img-preview">
                                <!--</a>-->
                            </td>
                            <td>
                                <?php
                                if ($order['is_small_photo'] == 1) {
                                    echo "Low resolution version";
                                } elseif ($order['is_large_photo'] == 1) {
                                    echo "High resolution version";
                                } elseif ($order['is_frame'] == 1) {
                                    echo "Printed Souvenir";
                                }
                                ?>
                            </td>
                            <td><?php echo $order['name']; ?></td>
                            <td>
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
                            </td>
                            <td>
                                <?php
                                if ($order['status'] == 0) {
                                    if ($order['is_frame'] == 1) {
                                        $lblclass = "label-default";
                                        $status = 'Not Shipped';
                                    } else {
                                        $lblclass = "label-default";
                                        $status = 'Not Completed';
                                    }
                                } elseif ($order['status'] == 1) {
                                    if ($order['is_frame'] == 1) {
                                        $lblclass = "label-success";
                                        $status = 'Shipped';
                                    } else {
                                        $lblclass = "label-success";
                                        $status = 'Completed';
                                    }
                                } elseif ($order['status'] == 2) {
                                    $lblclass = "label-primary";
                                    $status = 'Pending Collect';
                                } elseif ($order['status'] == 3) {
                                    $lblclass = "label-info";
                                    $status = 'Collected';
                                } elseif ($order['status'] == 4) {
                                    $lblclass = "label-warning";
                                    $status = 'Pending Delivery';
                                } elseif ($order['status'] == 5) {
                                    $lblclass = "label-info";
                                    $status = 'Delivered';
                                } elseif ($order['status'] == 6) {
                                    $lblclass = "label-danger";
                                    $status = 'Pending ship';
                                }
//                                echo $status;
                                ?>
                                <ul class="list list-unstyled">
                                    <li class="dropdown">
                                        <a href="#" id="main-status" class="label <?php echo $lblclass; ?>" data-toggle="dropdown"><?php echo $status; ?> <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <?php if ($order['is_frame'] == 1) { ?>
                                                <li class="<?php if($order['status'] == 0) echo "active"; ?>"><a href="javascript:void(0);" class="change-status" onclick="changeStatus(this)" data-id="<?php echo $order['id']; ?>" data-status="0" data-lblclass="label-default">Not Shipped</a></li>
                                                <li class="<?php if($order['status'] == 1) echo "active"; ?>"><a href="javascript:void(0);" class="change-status" onclick="changeStatus(this)" data-id="<?php echo $order['id']; ?>" data-status="1" data-lblclass="label-success">Shipped</a></li>
                                            <?php } else { ?>
                                                <li class="<?php if($order['status'] == 0) echo "active"; ?>"><a href="javascript:void(0);" class="change-status" onclick="changeStatus(this)" data-id="<?php echo $order['id']; ?>" data-status="0" data-lblclass="label-default">Not Completed</a></li>
                                                <li class="<?php if($order['status'] == 1) echo "active"; ?>"><a href="javascript:void(0);" class="change-status" onclick="changeStatus(this)" data-id="<?php echo $order['id']; ?>" data-status="1" data-lblclass="label-success">Completed</a></li>
                                            <?php } ?>
                                            <?php if ($order['is_frame'] == 1) { ?>
                                                <li class="<?php if($order['status'] == 2) echo "active"; ?>"><a href="javascript:void(0);" onclick="changeStatus(this)" class="change-status" data-id="<?php echo $order['id']; ?>" data-status="2" data-lblclass="label-primary">Pending Collect</a></li>
                                                <li class="<?php if($order['status'] == 3) echo "active"; ?>"><a href="javascript:void(0);" onclick="changeStatus(this)" class="change-status" data-id="<?php echo $order['id']; ?>" data-status="3" data-lblclass="label-info">Collected</a></li>
                                                <li class="<?php if($order['status'] == 4) echo "active"; ?>"><a href="javascript:void(0);" onclick="changeStatus(this)" class="change-status" data-id="<?php echo $order['id']; ?>" data-status="4" data-lblclass="label-warning">Pending Delivery</a></li>
                                                <li class="<?php if($order['status'] == 5) echo "active"; ?>"><a href="javascript:void(0);" onclick="changeStatus(this)" class="change-status" data-id="<?php echo $order['id']; ?>" data-status="5" data-lblclass="label-info">Delivered</a></li>
                                                <li class="<?php if($order['status'] == 6) echo "active"; ?>"><a href="javascript:void(0);" onclick="changeStatus(this)" class="change-status" data-id="<?php echo $order['id']; ?>" data-status="6" data-lblclass="label-danger">Pending Ship</a></li>
                                                <?php } ?>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <?php echo date('h:i A, d-M-Y', strtotime($order['created'])); ?>
                            </td>
                            <td>
                                <?php
//                                echo '<a href="' . site_url() . 'business/orders/change_status/' . $order['id'] . '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'admin/cart/view/' . $order['id'] . '/' . $order['businessId'] . '" class="btn border-teal text-teal-600 btn-flat btn-icon btn-rounded btn-xs" title="View Order"><i class="icon-eye4"></i></a>';
//                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'business/orders/delete/' . $order['id'] . '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Order"><i class="icon-trash"></i></a>';
                                ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
                    echo "<td colspan='9'><center>No orders found</center></td>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.datatable-basic').dataTable({
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 3, 9]}],
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[8, "desc"]]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });


    });
//Change order status.
    function changeStatus(e) {
        swal({
            title: "Are you sure?",
            text: "You are about to change the status of this order!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, change it!"
        },
                function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }
                    var id = $(e).data("id");
                    var status = $(e).data("status");
                    var $parent = $(e).parent();
                    console.log($parent);
                    var currentclass = $("#main-status").attr('class');
                    var lblclass = $(e).data("lblclass");
                    var text = $(e).text();
                    $.ajax({
                        url: "<?php echo base_url() ?>admin/orders/change_status/" + id + "/" + status,
                        success: function (response) {
                            swal("Done!", "Order status changed!", "success");
                             $('.dropdown-menu li').removeClass('active');
                            $("#main-status").html(text + '<span class="caret"></span>');
                            $("#main-status").removeClass(currentclass);
                            $("#main-status").addClass('label ' + lblclass);
                            if (!$parent.hasClass('active')) {
                                $parent.addClass('active');
                            }
                        }
                    });
                });
    }
</script>
