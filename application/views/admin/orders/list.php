<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/ui/moment/moment.min.js"></script>
<link href="assets/admin/css/bootstrap-datetimepicker.css">
<script type="text/javascript" src="assets/admin/js/bootstrap-datetimepicker.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-list2"></i> <span class="text-semibold">Manage Orders</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
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
                        <option value="custom" id="custom_range">Custom Range</option>
                    </select>
                </div>
            </div>
            <div class="row row-list" id="custom_range_picker">
                <div class="form-group col-xs-5">
                    <div class='input-group date' id='datetimepicker1'>
                        <span class="input-group-addon"><i class="icon-calendar3"></i></span>
                        <input type='text' class="form-control" name="start_date" id="start_date" value=""/>
                    </div>
                </div>
                <div class="form-group col-xs-5">
                    <div class='input-group date' id='datetimepicker1'>
                        <span class="input-group-addon"><i class="icon-calendar3"></i></span>
                        <input type='text' class="form-control" name="end_date" id="end_date" value=""/>
                    </div>
                </div>
                <div class="form-group col-xs-2">
                    <a href="javascript:void(0);" class="btn btn-success apply-range">Apply</a>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Order By</th>
                    <th>Amount</th>
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
                                    echo '<img src="assets/timthumb.php?src=' . base_url() . USER_IMAGE_SITE_PATH . $order['user_bioimage'] . '&w=70&h=70&q=100&zc=2" class="">';
                                } else {
                                    echo '<img src="assets/timthumb.php?src=' . base_url() . 'assets/admin/images/no_logo.png&w=60&h=60&q=100&zc=2" height="55px" width="55px" alt="' . $order['firstname'] . ' ' . $order['lastname'] . '">';
                                }
                                echo '<br/>';
                                echo $order['firstname'];
                                ?>
                            </td>
                            <td><?php echo $order['total_amount'] ?></td>
                            <td>
                                <?php echo $order['created']; ?>
                                <?php // echo date('h:i A, d-M-Y', strtotime($order['created'])); ?>
                            </td>
                            <td>
                                <?php
//                                echo '<a href="' . site_url() . 'business/orders/change_status/' . $order['id'] . '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Change status"><i class="icon-pencil7"></i></a>';
                                echo '&nbsp;&nbsp;<a href="' . site_url() . 'admin/orders/view/' . $order['id'] . '" class="btn border-teal text-teal-600 btn-flat btn-icon btn-rounded btn-xs" title="View Order"><i class="icon-eye4"></i></a>';
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
        $("#custom_range_picker").hide();
        $('#start_date').datetimepicker({
            maxDate: 'now'
        });
        $('#end_date').datetimepicker({
            maxDate: 'now'
        });
        var oTable = $('.datatable-basic').dataTable({
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 4]}],
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[3, "desc"]]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

        $("#filtertable").change(function (e) {


            var currentDate = new Date();

            if ($(this).val() == 'week') {
                $("#custom_range_picker").hide();
                var weekDate = new Date();
                var first = weekDate.getDate() - weekDate.getDay();
                var firstDayofWeek = new Date(weekDate.setDate(first));
                minDateFilter = firstDayofWeek.getTime();
                maxDateFilter = currentDate.getTime();
                oTable.fnDraw();

            } else if ($(this).val() == 'month') {
                $("#custom_range_picker").hide();
                var monthDate = new Date();
                var firstDayOfMonth = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
                minDateFilter = firstDayOfMonth.getTime();
                maxDateFilter = currentDate.getTime();
                oTable.fnDraw();

            } else if ($(this).val() == 'today') {
                $("#custom_range_picker").hide();
                var weekDate = new Date();
                var first = weekDate.getDate();
                var firstDayofWeek = new Date(weekDate.setDate(first));
                minDateFilter = firstDayofWeek.getTime();
                maxDateFilter = currentDate.getTime();
                oTable.fnDraw();
            } else if ($(this).val() == 'custom') {
                e.preventDefault();
                $("#custom_range_picker").show();
            } else {
                $("#custom_range_picker").hide();
                $("input:radio[name='filtertable']").each(function (i) {
                    this.checked = false;
                });
                minDateFilter = "";
                maxDateFilter = "";
                oTable.fnDraw();
            }

        });
        $(".apply-range").on("click", function (e) {
            var weekDate = new Date();
            var first = $("#start_date").val();
            var last = $("#end_date").val();
            var firstDay = new Date(first);
            var lastDay = new Date(last);
            minDateFilter = firstDay.getTime();
            maxDateFilter = lastDay.getTime();
            oTable.fnDraw();
        });
    });

// Date range filter
    minDateFilter = "";
    maxDateFilter = "";


    $.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {

                if (typeof aData._date == 'undefined') {
                    var date = aData[3],
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
                        return false;
                    }
                }

                if (maxDateFilter && !isNaN(maxDateFilter)) {
                    if (aData._date > maxDateFilter) {
                        return false;
                    }
                }

                return true;
            }
    );
</script>
