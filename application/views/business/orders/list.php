<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-list2"></i> <span class="text-semibold">Manage Orders</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Orders</li>
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
        <div class="row duration-filter">
            <div class="form-group">
                <div>
                    <select name="filtertable" id="filtertable" class="select">
                        <option value="">Select Time Duration</option>
                        <option value="all">All</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="today">Today</option>
                    </select>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Order By</th>
                    <th>ICP</th>
                    <th>ICP Image</th>
                    <th>Image Type</th>
                    <th>Amount</th>
                    <th>Is Delivered</th>
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
//                                    echo '<img src="' . USER_IMAGE_SITE_PATH . $order['user_bioimage'] . '" height="55px" width="55px" class="img-circle">';
                                    echo '<img src="'.base_url().'assets/timthumb.php?src='. base_url() . USER_IMAGE_SITE_PATH . $order['user_bioimage'] . '&w=65&h=65&q=100&zc=2" class="">';
                                } else {
                                    echo '<img src="assets/timthumb.php?src='.base_url().'assets/admin/images/no_logo.png&w=60&h=60&q=100&zc=2" height="55px" width="55px" alt="' . $order['firstname'] . ' ' . $order['lastname'] . '">';
                                }
                                echo '<br/>';
                                echo $order['firstname'] . ' ' . $order['lastname'];
                                ?>
                            </td>
                            <td><?php echo $order['icp_name']; ?></td>
                            <td>
        <!--                                <a href="<?php echo site_url() . 'business/icps/get_image?image=' . ICP_IMAGES . $order['image'] ?>" data-popup="lightbox">-->
                                <img src="<?php echo site_url() . 'business/icps/get_image?image=' . ICP_IMAGES . $order['image'] ?>" class="img-rounded img-preview">
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
                                if ($order['is_delivered'] == 1)
                                    echo 'Yes';
                                else
                                    echo 'No';
                                ?>
                            </td>
                            <td>
                                <?php echo $order['created']; ?>
                            </td>
                            <td>
                                <?php
//                                echo '<a href="' . site_url() . 'business/orders/change_status/' . $order['id'] . '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'business/orders/view/' . $order['id'] . '" class="btn border-teal text-teal-600 btn-flat btn-icon btn-rounded btn-xs" title="View Order"><i class="icon-eye4"></i></a>';
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
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this Order!",
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
    $(function () {
        var oTable = $('.datatable-basic').dataTable({
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 3, 8]}],
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[7, "desc"]]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
        $("#filtertable").change(function () {

            var currentDate = new Date();

            if ($(this).val() == 'week') {
                var weekDate = new Date();
                var first = weekDate.getDate() - weekDate.getDay();
                var firstDayofWeek = new Date(weekDate.setDate(first));
                minDateFilter = firstDayofWeek.getTime();


            } else if ($(this).val() == 'month') {

                var monthDate = new Date();
                var firstDayOfMonth = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
                minDateFilter = firstDayOfMonth.getTime();

            } else if ($(this).val() == 'today') {
                var weekDate = new Date();
                var first = weekDate.getDate();
                var firstDayofWeek = new Date(weekDate.setDate(first));
                minDateFilter = firstDayofWeek.getTime();
            } else {
                $("input:radio[name='filtertable']").each(function (i) {
                    this.checked = false;
                });
                minDateFilter = "";
                maxDateFilter = "";
                oTable.fnDraw();
            }
            maxDateFilter = currentDate.getTime();
            oTable.fnDraw();
        });
    });

// Date range filter
    minDateFilter = "";
    maxDateFilter = "";


    $.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                if (typeof aData._date == 'undefined') {
                    var date = aData[7],
                    values = date.split(/[^0-9]/),
                            year = parseInt(values[0], 10),
                            month = parseInt(values[1], 10) - 1,
                            day = parseInt(values[2], 10),
                            hours = parseInt(values[3], 10),
                            minutes = parseInt(values[4], 10),
                            seconds = parseInt(values[5], 10),
                            formattedDate;

                    formattedDate = new Date(year, month, day, hours, minutes, seconds);

                    aData._date = new Date(formattedDate).getTime();
                }

                if (minDateFilter && !isNaN(minDateFilter)) {
                    if (aData._date < minDateFilter) {
//                        alert('2');
                        return false;
                    }
                }

                if (maxDateFilter && !isNaN(maxDateFilter)) {
                    if (aData._date > maxDateFilter) {
//                        alert('3');
                        return false;
                    }
                }
//                alert("0");
                return true;
            }
    );
    /*
     $(function () {
     $('.datatable-basic').dataTable({
     autoWidth: false,
     processing: true,
     serverSide: true,
     language: {
     search: '<span>Filter:</span> _INPUT_',
     lengthMenu: '<span>Show:</span> _MENU_',
     paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
     },
     dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
     order: [[5, "desc"]],
     ajax: site_url + 'business/orders/get_orders',
     columns: [
     {
     data: "sr_no",
     visible: true,
     sortable: false,
     },
     {
     data: "user",
     visible: true,
     //                    render: function (data, type, full, meta) {
     //                        return full.firstname + ' ' + full.lastname;
     //                    }
     },
     {
     data: "total_amount",
     visible: true,
     },
     {
     data: "payment_type",
     visible: true,
     render: function (data, type, full, meta) {
     payment_type = '';
     if (data == 1) {
     payment_type = 'Card';
     } else if (data == 2) {
     payment_type = 'Paypal';
     }
     return payment_type;
     }
     },
     {
     data: "is_delivered",
     visible: true,
     render: function (data, type, full, meta) {
     payment_done = '';
     if (data == 1) {
     payment_done = 'Yes';
     } else if (data == 2) {
     payment_done = 'No';
     }
     return payment_done;
     }
     },
     {
     data: "created",
     visible: true,
     },
     {
     data: "status",
     visible: true,
     searchable: false,
     sortable: false,
     render: function (data, type, full, meta) {
     action = '<a href="' + site_url + 'business/orders/change_status/' + full.id + '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
     action = '<a href="' + site_url + 'business/orders/view/' + full.id + '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
     action += '&nbsp;&nbsp;<a href="' + site_url + 'business/orders/delete/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Order"><i class="icon-trash"></i></a>';
     return action;
     }
     },
     ]
     });
     
     $('.dataTables_length select').select2({
     minimumResultsForSearch: Infinity,
     width: 'auto'
     });
     
     });
     */
</script>