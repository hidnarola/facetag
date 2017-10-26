<?php
if (isset($json)) {
    echo "<script> var data=" . $json . "</script>";
} else {
    echo "<script> var data=''</script>";
}
$get_date = $this->input->get('date');
$start_date = '';
$end_date = '';
if ($get_date != '') {
    $dates = explode('-', $get_date);
    $start_date = date('F j, Y', strtotime(@$dates[0]));
    $end_date = date('F j, Y', strtotime(@$dates[1]));
}
?>
<script type="text/javascript">
    DEFAULT_ADMIN_JS_PATH = base_url + 'assets/admin/js/';
</script>
<script type="text/javascript" src="assets/admin/js/plugins/visualization/echarts/echarts.js"></script>
<script type="text/javascript" src="assets/admin/js/charts/echarts/lines_areas_business_dashboard.js"></script>

<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home') ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Dashboard</li>
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
    <?php
    $date = ($this->input->get('date')) ? "date=" . $this->input->get('date') . "&" : "";
    $format = "m/d/Y";
    ?>
    <div class="row">
        <div class="form-group cst_date col-lg-3 col-offset-lg-9">
            <div class="date_div">
                <button type="button" class="btn bg-slate-400 daterange-ranges" id="date_range_pick">
                    <i class="icon-calendar22 position-left"></i><span>Select Date Range to filter data</span><b class="caret"></b>
                </button>
            </div>
        </div>
    </div>
    <div class="row dashboard_layout">
        <div class="col-lg-3">
            <div class="panel bg-teal-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white" style="border-style: inherit"><i class="icon-users"></i></div>
                    </div>
                    <h3 class="no-margin"><?php echo $dashboard_data['checked_in_users'] ?></h3>
                    Checkedin Users
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel bg-pink-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white" style="border-style: inherit"><i class="icon-images2"></i></div>
                    </div>
                    <h3 class="no-margin"><?php echo $dashboard_data['total_images'] ?></h3>
                    Total Images
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel bg-blue-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white" style="border-style: inherit"><i class="icon-stack-check"></i></div>
                    </div>
                    <h3 class="no-margin"><?php echo $dashboard_data['total_matches'] ?></h3>
                    Total matches(Verified) 
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel bg-slate-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white" style="border-style: inherit"><i class="icon-cash2"></i></div>
                    </div>
                    <h3 class="no-margin"><?php echo $dashboard_data['free_images_purchased'] ?></h3>
                    Free Images purchased
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="chart-container">
                <div class="chart has-fixed-height" id="line_point"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var get = '<?php echo $this->input->get('date') ?>';
        if (get != "") {
            var res = get.split('-');
            start = res[0];
            end = res[1];

            $("#date_range_pick").daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
//                opens: 'left',
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            },
                    function (start, end) {
                        $('.daterange-ranges span').html(start.format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + end.format('MMMM D, YYYY'));
                    }
            );
            $('#date_range_pick span').html('<?php echo $start_date ?>' + ' &nbsp; - &nbsp; ' + '<?php echo $end_date ?>');

        } else {
            //var start = moment().subtract(6, 'days');
            //var end = moment();
            $("#date_range_pick").daterangepicker({
                autoUpdateInput: false,
                maxDate: moment(),
                //opens: 'left',
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            });
        }

        $('#date_range_pick').on('apply.daterangepicker', function (ev, picker) {
            var url = window.location.href;
            var newurl = updateQueryStringParameter(url, "date", picker.startDate.format('MM/DD/YYYY') + '-' + picker.endDate.format('MM/DD/YYYY'));
            $('#date_range_pick span').html(picker.startDate.format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + picker.endDate.format('MMMM D, YYYY'));
            window.location.href = newurl;
        });

        $('#date_range_pick').on('cancel.daterangepicker', function (ev, picker) {
            if ($('#date_range_pick span').html() != '') {
                var url = window.location.href;
                var newurl = updateQueryStringParameter(url, "date", '');
                window.location.href = newurl;
            }
            $('#date_range_pick span').html('');
        });

        $('#date_range_pick').on('cancel.daterangepicker', function (ev, picker) {
            $('date_range_pick span').html('');
        });

    });

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>