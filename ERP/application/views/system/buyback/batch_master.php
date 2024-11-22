<?php echo head_page('Batches', True);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$farmer = load_all_farms();
$location_arr = load_all_locations();
$field_Officer = buyback_farm_fieldOfficers_drop();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
    .stars
    {
        display: inline-block;color: #F0F0F0;text-shadow: 0 0 1px #666666;font-size:30px;
    }  .highlights,
    .selectedstars {color:#F4B30A;text-shadow: 0 0 1px #F48F0A;}
</style>
<form id="dispatch_filter_frm">
<div id="filter-panel" class="collapse filter-panel">

    <div class="row">
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="batchmasterDatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="batchmasterDatefrom" class="form-control">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="batchmasterDateto"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="batchmasterDateto"  class="form-control">
            </div>
        </div>

        <div class="form-group col-sm-2">
            <label for="area">Area</label><br>
            <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID" onchange="startMasterSearch()"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label for="area">Sub Area</label><br>
            <?php echo form_dropdown('subLocationID', array(" " => "Select Sub Area"), '', 'class="form-control select2" id="subLocationID" onchange="startMasterSearch()"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label for="farmer">Farmer </label><br>
            <?php echo form_dropdown('farmer', $farmer, '', 'class="form-control select2" id="farmer" onchange="startMasterSearch()"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label for="fieldofficer">Field Officer</label><br>
            <?php echo form_dropdown('fieldofficer', $field_Officer, '', 'class="form-control select2" id="fieldofficer" onchange="startMasterSearch()"'); ?>
        </div>
    </div>

    <div class="row" style="margin-top: 2%">
        <div class="col-sm-4" style="">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Search Batch"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-2" style="">
            <?php echo form_dropdown('batches_status', array('' => 'Status', '1' => 'Draft', '2' => 'Confirmed', '3' => 'Approved'), '', 'class="form-control"  id="batches_status" onchange="startMasterSearch()"'); ?>
        </div>
        <div class="col-sm-2">
            <div class="input-group" id="hide_total_row">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="viewClosedBatch" id="viewClosedBatch" value="1" onclick="getBatchManagement_tableView()">
                                </span>
                <input type="text" class="form-control" disabled="" value="View Closed Batches">
            </div>
        </div>
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
</div>
</form>

<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right hide">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/buyback/create_farm',null,'Add New Batch','BUYBACK');"><i
                    class="fa fa-plus"></i> New Batch
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="BatchMaster_view"></div>
                </div>
            </div>
            <div class="col-xs-12" style="padding-right: 5px;">
                <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                    <p id="filterDisplay"></p>

                    <nav>
                        <ul class="list-inline" id="pagination-ul">

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal report-->

<div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 85%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Production Statement</h4>
            </div>
            <div class="modal-body" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979" >
                <div id="reportContent"></div>
            </div>
            <div class="modal-body" id="PaymentHistoryModal" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979">
                <div id="PaymentHistory"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--modal report-->
<div class="modal fade" id="feedSchedule_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Feed Schedule Report</h4>
            </div>
            <div class="modal-body">
                <div id="feedSchedule_reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batch_confirmation_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Production Statement</h4>
            </div>
            <form class="form-horizontal" id="paymentVoucher_approval_form">
                <input type="hidden" id="lock_batchMasterID" name="batchMasterID">
                <input type="hidden" id="profitLossDetails" name="profitLossDetails">
                <input type="hidden" id="profitLossColor" name="profitLossColor">
                <div class="">
                    <div class="col-sm-1">
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                         data-toggle="tab"
                                                                                         onclick="tabView()">View</a>
                            </li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab"
                                                                          onclick="tabAttachement()">Attachment</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <br>
                                <br>
                                <div class="row" style="margin-bottom: 5px; margin-top: 5px; padding-left: 5%; padding-right: 5%">
                                    <div class="form-group col-sm-2">
                                        <strong>Cage :</strong>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <!-- <input type="text" id="cageName" name="cageName"
                                               style="color: #000000;font-size: 18px;font-weight: 600; border: none">-->
                                        <span class="head-title" id="cageName"
                                              style="color: #000000;font-size: 18px;font-weight: 600;"></span>
                                    </div>
                                    <input type="text" name="cageID" id="cageID" class="hidden" style="">
                                </div>
                                <div class="row" style="margin-bottom: 5px; margin-top: 5px; padding-left: 5%; padding-right: 5%">
                                    <div class="form-group col-sm-2">
                                        <strong>Next Input :</strong>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-group restdatepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="restEndDay"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   value="<?php echo $current_date; ?>" id="restEndDay" class="form-control">
                                        </div>
                                        <input type="text" name="currentDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="currentDate" class="hidden">
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 5px; margin-top: 5px; padding-left: 5%; padding-right: 5%">
                                    <div class="form-group col-sm-2">
                                        <strong>Rest days :</strong>
                                    </div>
                                    <div class="form-group col-sm-6">
                                         <span class="head-title" id="restDays"
                                               style="color: #000000;font-size: 18px;font-weight: 600;">0 days</span>
                                        <!--<input type="text" name="restDaysText" id="restDaysText" class="form-control" style="border: none">-->
                                    </div>
                                </div>
                                <div class="row" style=" padding-left: 5%; padding-right: 5%">
                                    <div class="col-sm-2 form-group" style="margin-top: 1.5%">
                                        <strong> Comments :</strong>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                      <textarea class="form-control" rows="3" name="comments"
                                                id="comments"></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="pull-right" style=" padding-left: 5%; padding-right: 5%">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success" onclick="confirmation()">Confirm
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong>Payment Voucher Attachments</strong>
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>File Name</th>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="po_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center">No Attachment Found</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">&nbsp;</div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var per_page = 10;
    var Otable;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/batch_master', '', 'Batch');
        });
        //load_farm_filter('#', 1);
        getBatchManagement_tableView();

        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    $('.restdatepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        calculateDays();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        getBatchManagement_tableView(data_pagination, uriSegment);
    }


    function getBatchManagement_tableView(pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#dispatch_filter_frm').serializeArray();
        var viewclosedbatch = ($('#viewClosedBatch').prop('checked'))? '1' : '0';
        data.push({'name':'viewclosedbatch', 'value':viewclosedbatch});
        data.push({'name': 'pageID', 'value': pageID});
        //data.push({'name': 'searchTask', 'value': searchTask});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_batch_Master_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#BatchMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function highlightStar(obj) {
        removeHighlight();
        $('.starsgrading').each(function(index) {
            $(this).addClass('highlights');
            if(index == $(".starsgrading").index(obj)) {
                return false;
            }
        });
    }

    function removeHighlight() {
        $('.starsgrading').removeClass('selectedstars');
        $('.starsgrading').removeClass('highlights');
    }

    function addRating(obj) {
        var id = $('#lock_batchMasterID').val();

        $('.starsgrading').each(function(index) {
            $(this).addClass('selectedstars');
            $('#rating').val((index+1));
            var ratings = $('#rating').val();
            if(index == $(".starsgrading").index(obj)) {
                myAlert('s', 'Grading Updated '+ ratings +' Out Of 5.')
                return false;
            }
        });
    }

    function resetRating() {
        if($("#rating").val()) {
            $('.starsgrading').each(function(index) {
                $(this).addClass('selectedstars');
                if((index+1) == $("#rating").val()) {
                    return false;
                }
            });
        }
    }

    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });

    function delete_farm(id) {
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
                    data: {'farmID': id},
                    url: "<?php echo site_url('Crm/delete_farm_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getBatchManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getBatchManagement_tableView();
    }

    function clearSearchFilter() {
        $('#viewClosedBatch').iCheck('uncheck');
        $('#search_cancel').addClass('hide');
        $('.farmsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#farmer').val('');
        $('#locationID').val(null).trigger('change');
        $('#fieldofficer').val('');
        $('#batches_status').val('');
        $('#subLocationID').val(null).trigger('change');
        $('#farmer').val(null).trigger('change');
        $('#fieldofficer').val(null).trigger('change');
        $('#batchmasterDatefrom').val('');
        $('#batchmasterDateto').val('');
        $('#sorting_1').addClass('selected');
        getBatchManagement_tableView();
    }

    /*    /!*call report content*!/
        function generateBatchProductionReport(batchMasterID) {
            $('#btn_lockGenerateReport').removeClass('hide');
            $('#lock_batchMasterID').val(batchMasterID);
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {batchMasterID:batchMasterID},
                url: '',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#finance_report_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }*/

    function generateBatchProductionReport_view(batchMasterID) {
        $('#btn_lockGenerateReport').addClass('hide');
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {batchMasterID: batchMasterID},
            url: '<?php echo site_url('Buyback/buyback_production_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
//                generatePaymentHistory(batchMasterID);
                $("#finance_report_modal").modal({backdrop: "static"});
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function generatePaymentHistory(batchMasterID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'batchMasterID': batchMasterID},
            url: '<?php echo site_url('Buyback/production_report_paymentHistory'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#PaymentHistory").html(data);
                $("#finance_report_modal").modal({backdrop: "static"});
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function confirmation() {
        var profitLossDetails = $('#profitLossDetails').val();
        var profitLossColor = $('#profitLossColor').val();
        swal({
                title: "Are you sure?",
                text: "You want to Lock this Batch !",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: profitLossColor,
                confirmButtonText: profitLossDetails
            },
            function () {
                var restEndDay = $('#restEndDay').val();
                var cageID = $('#cageID').val();
                var batchMasterID = $('#lock_batchMasterID').val();
                var rating =  $('#rating').val();
                var comments =  $('#comments').val();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'batchMasterID': batchMasterID,'Grading':rating,'comments':comments, 'restEndDay': restEndDay, 'cageID': cageID},
                    url: "<?php echo site_url('Buyback/buyback_batchLock_confirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getBatchManagement_tableView();
                            $('#batch_confirmation_modal').modal('hide');
                            $('#comments').val('');
                            removeHighlight();
                          //  $('.starsgrading').val(null).trigger('change');
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
        $(".sweet-alert").css('background-color', '#eaebea');
    }

    function calculateDays()
    {
        var startDate = moment($("#currentDate").val(), "DD.MM.YYYY");
        var endDate = moment($("#restEndDay").val(), "DD.MM.YYYY");
        var formattedDate = endDate.diff(startDate, 'days');

        if(formattedDate < 0){
            myAlert('w', 'Invalid Date Selection');
            $("#restEndDay").val('<?php echo $current_date ?>');
            $('#restDays').html('0 days');
        } else{
            $('#restDays').html(formattedDate + ' days');
        }
    }

    function generateBatchProductionReport(batchMasterID, documentApprovedID, Level) {

        if (batchMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'batchMasterID': batchMasterID, 'html': true, 'View': "Batch Closing"},
                url: "<?php echo site_url('Buyback/buyback_production_report'); ?>",
                beforeSend: function () {

                    startLoad();
                },
                success: function (data) {
                    $('#lock_batchMasterID').val(batchMasterID);
                    $("#batch_confirmation_modal").modal({backdrop: "static"});
                    $('#comments').val('');
                    removeHighlight();
                    findBatchCage(batchMasterID);
                    checkBatchStatus(batchMasterID);
                    $('#conform_body').html(data);
                    $('#rating').html(data['fcr']);
                    //paymentVoucher_attachment_View_modal('BBPV', pvMasterAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function findBatchCage(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchMasterID: id},
            url: '<?php echo site_url('Buyback/batchMaster_findBatchCage'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data){
                    $('#cageName').html(data['cageName']);
                    $('#cageID').val(data['cageID']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function checkBatchStatus(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {batchMasterID: id},
            url: '<?php echo site_url('Buyback/batchMaster_BatchStatus'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data == 1){
                    $('#profitLossDetails').val('Profit');
                    $('#profitLossColor').val('green');

                } else{
                    $('#profitLossDetails').val('Loss');
                    $('#profitLossColor').val('red');
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function feedScheduleReport_view(batchMasterID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {batchMasterID: batchMasterID},
            url: '<?php echo site_url('Buyback/load_feedSchedule_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#feedSchedule_reportContent").html(data);
                $("#feedSchedule_report_modal").modal({backdrop: "static"});
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }
    $("#locationID").change(function () {
        get_buyback_subArea($(this).val())
    });
    function get_buyback_subArea(locationID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {locationID: locationID},
            url: "<?php echo site_url('Buyback/fetch_buyback_subArea'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#subLocationID').empty();
                var mySelect = $('#subLocationID');
                mySelect.append($('<option></option>').val("").html("Select"));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['locationID']).html(text['description']));
                    });
                }
                if(subLocationID){
                    mySelect.val(subLocationID).change();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });
    $('.select2').select2();
Inputmask().mask(document.querySelectorAll("input"));

</script>