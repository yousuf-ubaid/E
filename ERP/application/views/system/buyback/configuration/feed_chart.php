<?php echo head_page('Feed Chart', false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$uom_arr = fetch_buyback_umo_drop();
$feedTypes_arr = fetch_buyback_feedTypes(True);
$feedTypes_edit_arr = fetch_buyback_feedTypes();
$tasks = load_all_task_types();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>FEED CHART - HEADER</h2>
        </header>
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="feedChart_header_modal()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-11">
                <div id="feedChart_header"></div>
            </div>
            <div class="col-sm-1">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>FEED CHART - DETAIL</h2>
        </header>
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="feedChart_detail_modal()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-11">
                <div id="feedChart_detail"></div>
            </div>
            <div class="col-sm-1">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-success pull-right" onclick="feedChart_confirmation()">Confirm</button>
    </div>
</div>

<!--
<div class="row">
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary btn-sm pull-right"  onclick="AddNewTemplate()"
                style="margin-right: 4px"><i class="fa fa-plus"></i> Add
        </button>
    </div>
</div>
<br>
<div class="box box-solid" style="border: 1px solid rgba(158, 158, 158, 0.24);">
    <div class="box-header" >
        <div class="" style="">
            <p style="font-size: 20px; color: #898989">Feed Details</p>
        </div>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>

    </div>
    <div class="box-body collapse" style="margin-top:-20px;">
        <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>FEED CHART - HEADER</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="feedChart_header_modal()">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-11">
                            <div id="feedChart_header"></div>
                        </div>
                        <div class="col-sm-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        <br>
        <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>FEED CHART - DETAIL</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="feedChart_detail_modal()">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-11">
                            <div id="feedChart_detail"></div>
                        </div>
                        <div class="col-sm-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <button class="btn btn-success pull-right" onclick="feedChart_confirmation()">Confirm</button>
            </div>
        </div>

    </div>
</div>
-->

<div aria-hidden="true" role="dialog" id="fieldChart_header_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Feed Chart Header Detail</h4>
            </div>
            <form role="form" id="fieldChart_header_add_form" class="form-horizontal">
               <!-- <input type="hidden" name="feedScheduleID" id="edit_feedScheduleID">-->
                <div class="modal-body">
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Days</label>
                        </div>
                        <div class="form-group col-sm-3">
                            <input type="text" name="startDay" id="startDay" class="form-control number"
                                   title="Start Day" readonly>
                        </div>
                        <div class="form-group col-sm-1">
                            <label class="title">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;</label>
                        </div>
                        <div class="form-group col-sm-3">
                            <input type="text" name="endDay" id="endDay" class="form-control number" title="End Day">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Uom</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('uomID', $uom_arr, '', 'class="form-control select2" id="uomID"'); ?>
                        </div>

                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Feed Type</label>
                        </div>
                        <div class="form-group col-sm-6" id="feedAddTypes">
                            <?php echo form_dropdown('feedTypeID', $feedTypes_arr, 'Each', 'class="form-control select2" id="feedTypeID"'); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Feed %</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <input type="text" name="feedAmount" id="feedAmount" class="form-control number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="saveFieldChart_header()">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="fieldChart_header_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Feed Chart Header Detail</h4>
            </div>
            <form role="form" id="fieldChart_header_edit_form" class="form-horizontal">
                <input type="hidden" name="feedScheduleID" id="edit_feedScheduleID">
                <div class="modal-body">
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Days</label>
                        </div>
                        <div class="form-group col-sm-3">
                            <input type="text" name="startDay" id="startDay_edit" class="form-control number"
                                   title="Start Day" readonly>
                        </div>
                        <div class="form-group col-sm-1">
                            <label class="title">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;</label>
                        </div>
                        <div class="form-group col-sm-3">
                            <input type="text" name="endDay" id="endDay_edit" class="form-control number" title="End Day">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Uom</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('uomID', $uom_arr, '', 'class="form-control select2" id="uomID_edit"'); ?>
                        </div>

                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Feed Type</label>
                        </div>
                        <div class="form-group col-sm-6 hidden feedEditType"></div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Feed %</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <input type="text" name="feedAmount" id="feedAmount_edit" class="form-control number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="updateFieldChart_header()">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="fieldChart_detail_add_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Feed Chart Detail</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="fieldChart_detail_add_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="fieldChart_detail_add_table">
                        <thead>
                        <tr>
                            <th colspan="2">Day</th>
                            <th>&nbsp;</th>
                            <th colspan="2">Body Weight</th>
                            <th colspan="2">FCR</th>
                            <?php if($tasks)?>
                            <th colspan="<?php echo count($tasks)?>">Tasks</th>
                        </tr>
                        <tr>
                            <th>Age Day <?php required_mark(); ?></th>
                            <th>Feed Per Day <?php required_mark(); ?></th>
                            <th>UOM <?php required_mark(); ?></th>
                            <th>Min <?php required_mark(); ?></th>
                            <th>Max <?php required_mark(); ?></th>
                            <th>Min <?php required_mark(); ?></th>
                            <th>Max <?php required_mark(); ?></th>
                            <?php foreach ($tasks as $val)
                            {
                                echo "<th>" . $val['shortDescription'] . "</th>";
                            }?>
                          <!--  <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_fieldChart()"><i
                                        class="fa fa-plus"></i></button>
                            </th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><div id="age_day_div">
                                    <input type="text" name="age_day[]" id="age_day" class="form-control number" readonly>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="feedPer_day[]" class="form-control number">
                            </td>
                            <td>
                                <?php echo form_dropdown('uomID[]', $uom_arr, '', 'class="form-control" '); ?>
                            </td>
                            <td>
                                <input type="text" name="bodyWeight_min[]" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="bodyWeight_max[]" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="fcr_min[]" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="fcr_max[]" class="form-control number">
                            </td>
                            <?php foreach ($tasks as $val)
                            {
                                echo "<td style='text-align: center'> <div class='skin-section extraColumnsgreen'>
                                        <input type='checkbox' class='columnSelected taskType' id='". $val['shortDescription'] ."' name ='". $val['shortDescription'] ."[]' value = '1'></div></td>";
                            }?>

                           <!-- <td class="remove-td" style="vertical-align: middle;text-align: center"></td> -->
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="saveFieldChart_detail_multiple()">Save
                    changes
                </button>
            </div>

        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="fieldChart_detail_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Feed Chart Detail</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="fieldChart_detail_edit_form" class="form-horizontal">
                    <input type="hidden" id="edit_feedscheduledetailID" name="feedscheduledetailID">
                    <table class="table table-bordered table-condensed no-color" id="fieldChart_detail_edit_table">
                        <thead>
                        <tr>
                            <th colspan="2">Day</th>
                            <th>&nbsp;</th>
                            <th colspan="2">Body Weight</th>
                            <th colspan="2">FCR</th>
                            <?php if($tasks)?>
                            <th colspan="<?php echo count($tasks)?>">Tasks</th>
                        </tr>
                        <tr>
                            <th>Age Day <?php required_mark(); ?></th>
                            <th>Feed Per Day <?php required_mark(); ?></th>
                            <th>UOM <?php required_mark(); ?></th>
                            <th>Min <?php required_mark(); ?></th>
                            <th>Max <?php required_mark(); ?></th>
                            <th>Min <?php required_mark(); ?></th>
                            <th>Max <?php required_mark(); ?></th>
                            <?php foreach ($tasks as $val)
                            {
                                echo "<th>" . $val['shortDescription'] . "</th>";
                            }?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="age_day" id="edit_age_day" class="form-control number" readonly>
                            </td>
                            <td>
                                <input type="text" name="feedPer_day"  id="edit_feedPer_day" class="form-control number">
                            </td>
                            <td>
                                <?php echo form_dropdown('uomID', $uom_arr, '', 'class="form-control" id="edit_uomID"'); ?>
                            </td>
                            <td>
                                <input type="text" name="bodyWeight_min" id="edit_bodyWeight_min" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="bodyWeight_max" id="edit_bodyWeight_max" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="fcr_min" id="edit_fcr_min" class="form-control number">
                            </td>
                            <td>
                                <input type="text" name="fcr_max" id="edit_fcr_max" class="form-control number">
                            </td>
                            <?php foreach ($tasks as $val)
                            {
                                echo "<td style='text-align: center'> <div class='skin-section extraColumnsgreen'>
                                        <input type='checkbox' class='columnSelected edit_taskType' id='edit_". $val['shortDescription'] ."' name ='edit_". $val['shortDescription'] ."' value = '1'></div></td>";
                            }?>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="update_feedChart_detail()">Update
                    changes
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/buyback/configuration/feed_chart', '', 'Feed Chart')
        });


        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        number_validation();

        $('.select2').select2();

        getfeedChart_header_tableView();

        getfeedChart_detail_tableView();
    });

    function getfeedChart_header_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Buyback/load_feedChart_header_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#feedChart_header').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getfeedChart_detail_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Buyback/load_feedChart_detail_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#feedChart_detail').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function feedChart_header_modal() {
        $('#fieldChart_header_add_form')[0].reset();
        $("#uomID").val(null).trigger("change");
        $("#edit_feedScheduleID").val('');
        $("#feedTypeID").val(null).trigger("change");
        selectFeedType('');
        $('#endDay').prop('readonly', false);
        fetch_feedStartDay();
        $("#fieldChart_header_modal").modal({backdrop: "static"});
    }

    function fetch_feedStartDay() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('Buyback/fetch_feedStartDay_feedChart'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#startDay').val(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function feedChart_detail_modal() {
        fetch_feedDetailsAge();
        $('.taskType').iCheck('uncheck');
        $('#fieldChart_detail_add_form')[0].reset();
        $('#fieldChart_detail_add_table tbody tr').not(':first').remove();
        $("#fieldChart_detail_add_modal").modal({backdrop: "static"});
    }

    function fetch_feedDetailsAge() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Buyback/fetch_feedDetailsAge'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#age_day_div').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function add_more_fieldChart() {
        var appendData = $('#fieldChart_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('.umoDropdown').empty();
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#fieldChart_detail_add_table').append(appendData);
        var lenght = $('#fieldChart_detail_add_table tbody tr').length - 1;
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function saveFieldChart_detail_multiple() {
        var $form = $('#fieldChart_detail_add_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('buyback/save_fieldChart_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.taskType').iCheck('uncheck');
                    $('#fieldChart_detail_add_form')[0].reset();
                    getfeedChart_detail_tableView();
                    $('#fieldChart_detail_add_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function saveFieldChart_header() {
        var $form = $('#fieldChart_header_add_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('buyback/save_fieldChart_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#fieldChart_header_add_form')[0].reset();
                    getfeedChart_header_tableView();
                    $('#fieldChart_header_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_feedChart_header(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'feedScheduleID': id},
                    url: "<?php echo site_url('Buyback/delete_feedChart_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getfeedChart_header_tableView();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function delete_feedChart_detail(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'feedscheduledetailID': id},
                    url: "<?php echo site_url('Buyback/delete_feedChart_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getfeedChart_detail_tableView();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function edit_feedChart_header(feedScheduleID) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'feedScheduleID': feedScheduleID},
                    url: "<?php echo site_url('Buyback/edit_feedChart_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#edit_feedScheduleID').val(data['feedScheduleID']);
                        $('#startDay_edit').val(data['startDay']);
                        $('#endDay_edit').val(data['endDay']);
                        $('#endDay_edit').prop('readonly', true);
                        $('#uomID_edit').val(data['uomID']).change();
                        $('.feedEditType').addClass('hidden');
                        selectFeedType(data['feedTypeID']);
                        $('#feedAmount_edit').val(data['feedAmount']);
                        $("#fieldChart_header_edit_modal").modal('show');
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }


    function updateFieldChart_header() {
        var $form = $('#fieldChart_header_edit_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('buyback/save_fieldChart_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#fieldChart_header_edit_form')[0].reset();
                    getfeedChart_header_tableView();
                    $('#fieldChart_header_edit_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function selectFeedType(feedTypeID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'feedTypeID': feedTypeID},
            url: "<?php echo site_url('Buyback/selectFeedType'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                // $('#feedTypeID_edit').val(data['feedTypeID']).change();
                $('.feedEditType').html(data);
                $('#feedAddTypes').html(data);
                $('.feedEditType').removeClass('hidden');
                // stopLoad();
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });

    }

    function edit_feedChart_detail(id) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'feedscheduledetailID': id},
                    url: "<?php echo site_url('Buyback/edit_feedChart_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#edit_feedscheduledetailID').val(data['master']['feedscheduledetailID']);
                        $('#edit_age_day').val(data['master']['age']);
                        $('#edit_feedPer_day').val(data['master']['perDayFeed']);
                        $('#edit_uomID').val(data['master']['uomID']).change();
                        $('#edit_bodyWeight_min').val(data['master']['minBodyWeight']);
                        $('#edit_bodyWeight_max').val(data['master']['maxBodyWeight']);
                        $('#edit_fcr_min').val(data['master']['minFCR']);
                        $('#edit_fcr_max').val(data['master']['maxFCR']);

                        $.each(data['task'], function (i, val) {
                            if(val['isActive'] == 1)
                            {
                                $('#edit_' + val['shortDescription']).iCheck('check');
                            } else {
                                $('#edit_' + val['shortDescription']).iCheck('uncheck');
                            }
                        });

                        $("#fieldChart_detail_edit_modal").modal('show');
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function update_feedChart_detail(){
        var $form = $('#fieldChart_detail_edit_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('buyback/update_fieldChart_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.edit_taskType').iCheck('uncheck');
                    $('#fieldChart_detail_edit_form')[0].reset();

                    getfeedChart_detail_tableView();
                    $('#fieldChart_detail_edit_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function feedChart_confirmation() {

    }

</script>