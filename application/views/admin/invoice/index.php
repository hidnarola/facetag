<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-office"></i> <span class="text-semibold">Select Businesse to view weekly invoice</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Businesses</li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('invitation_success_msg')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->userdata('invitation_success_msg') ?></strong>
                </div>
                <?php $this->session->unset_userdata('invitation_success_msg') ?>
            <?php } ?>
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
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Business Logo</th>
                    <th>Business Name</th>
                    <th>Address</th>
                    <th>Business User</th>
                    <th>Payment</th>
                    <th>Added On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($business) {
                    $i = 1;
                    foreach ($business as $row) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <?php
                                if ($row["business_details"]['logo'] != '') {
                                    echo '<img src="assets/timthumb.php?src=' . base_url() . BUSINESS_LOGO_IMAGES . $row["business_details"]['logo'] . '&w=60&h=60&q=100&zc=2"">';
                                } else {
                                    echo '<img src="assets/timthumb.php?src='.base_url().'assets/admin/images/no_logo.png&w=60&h=60&q=100&zc=2" height="55px" width="55px">';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $row["business_details"]['name']; ?>
                            </td>
                            <td><?php echo $row["business_details"]['address1']; ?></td>
                            <td><?php echo $row["business_details"]['firstname'] . ' ' . $row["business_details"]['lastname']; ?></td>
                            <td><?php echo $row['payment']; ?></td>
                            <td><?php echo $row["business_details"]['created']; ?></td>
                            <td>
                                <?php
                                $status = '';
                                if ($row["business_details"]['user_verified'] == 0) {
                                    $status = '<span class="label bg-warning">Mail not verified by User</span>';
                                } else if ($row["business_details"]['is_delete'] == 1) {
                                    $status = '<span class="label bg-danger">Blocked</span>';
                                } else if ($row["business_details"]['is_active']) {
                                    $status = '<span class="label bg-success">Active</span>';
                                }
                                echo $status;
                                ?>
                            </td>
                            <td>
                                <?php
//                                echo '<a href="' . site_url() . 'business/orders/change_status/' . $order['id'] . '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'admin/invoice/invoices/' . $row["business_details"]['id'] . '" class="btn border-teal text-teal-600 btn-flat btn-icon btn-rounded btn-xs" title="View Order"><i class="icon-eye4"></i></a>';
//                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'business/orders/delete/' . $order['id'] . '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Order"><i class="icon-trash"></i></a>';
                                ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
                    echo "<td colspan='9'><center>No business found</center></td>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    // Lightbox
    $('[data-popup="lightbox"]').fancybox({
        padding: 3
    });
    $(function () {
        $('.datatable-basic').dataTable({
            autoWidth: false,
            pageLength: 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'},
                searchPlaceholder: "Type name,address,user"
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[6, "desc"]]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

    });
    function invite_alert(e) {
        var email = $(e).attr('data-email');
        swal({
            title: "Are you sure?",
            text: "Invitation email will be sent to " + email + " user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, send it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = $(e).attr('href');
                        return true;
                    } else {
                        return false;
                    }
                });
        return false;
    }
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this business!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, delete it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = $(e).attr('href');
                        return true;
                    } else {
                        return false;
                    }
                });
        return false;
    }
    function block_alert(e, type) {
        swal({
            title: "Are you sure?",
            text: "The Business will be " + type + "ed!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, " + type + " it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = $(e).attr('href');
                        return true;
                    } else {
                        return false;
                    }
                });
        return false;
    }
</script>