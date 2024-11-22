<?php

echo head_page('Journey Plan', false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employeedrop = all_employee_drop();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }
    .actionicon{
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .headrowtitle{
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="bookingdatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="bookingdatefrom" class="form-control"  value=""  >
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="bookingdateto"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="bookingdateto"  class="form-control" value="" >
            </div>
        </div>


        <div class="col-sm-3" style="margin-top: 26px;">
            <?php echo form_dropdown('employeesearch',$employeedrop, '', 'class="form-control select2" onchange="startMasterSearch()" id="employeesearch"'); ?>
        </div>
        <br>

    </div>
    <br>
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/journeyplan/create_journey_plan',null,'Add New Journey Plan',' ');"><i
                class="fa fa-plus"></i> Create New
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Enter Your Text Here"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
    <div class="col-md-2">
        <?php echo form_dropdown('documentstatus', array('' => 'Document Status', '1' => 'Draft', '2' => 'Confirmed', '3' => 'Approved'), '', 'class="form-control select2" onchange="startMasterSearch()" id="documentstatus"'); ?>
    </div>

    <div class="col-md-2">
        <?php echo form_dropdown('jpstatus', array('' => 'JP Status', '1' => 'Not Started', '2' => 'Started', '3' => 'Closed','4' => 'Cancelled','5' => 'On Hold'), '', 'class="form-control select2" onchange="startMasterSearch()" id="jpstatus"'); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="ioubookingmasterview">
            <!-- /.table -->
        </div>

    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="jp_status_drilldown">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Journey Plan</h4>
            </div>
            <?php echo form_open('', 'role="form" id="payment_vocher_Status"'); ?>
            <div class="modal-body">
                <input type="hidden" name="jpmasterid" id="jpmasterid">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Journey Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                   <?php echo form_dropdown('jpstatusupdate', array('0' => 'Not Started','2' => 'Started','3' => 'Closed','4' => 'Cancelled','5' => 'On Hold'), '', 'class="form-control" id="jpstatusupdate" required'); ?>

                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comments</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" rows="3" id="Commentsjp"
                                  name="Commentsjp"></textarea>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" onclick="submit_jp_status()"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                          aria-hidden="true"></span> Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="ivms_no_congif">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Journey Tracing</h4>
            </div>
            <?php echo form_open('', 'role="form" id="ivmsnoconfig"'); ?>
            <div class="modal-body">
                <input type="hidden" name="jpmasterid" id="jpmasterid">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <img src="<?php echo base_url('images/journeyplan/ivmsmap.jpg'); ?>" style="width: 100%; opacity: 0.3;
    filter: alpha(opacity=30);">
                        <div style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-weight:bold;font-size:22px;color: #ca0000"><strong>Tracing Not Configured</strong></div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/journeyplan/journey_plan','','Journey Plan');
        });
        getiouvoucherbookingtable();
        Inputmask().mask(document.querySelectorAll("input"));
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function getiouvoucherbookingtable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var status = $('#documentstatus').val();
        var jpstatus = $('#jpstatus').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask,'filtervalue':filtervalue,'status':status,'jpstatus':jpstatus},
            url: "<?php echo site_url('Journeyplan/load_journeyplan_masterview'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ioubookingmasterview').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_iou_booking(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'bookingMasterID': id},
                    url: "<?php echo site_url('Iou/delete_iou_booking_delete'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getiouvoucherbookingtable();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getiouvoucherbookingtable();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.donorsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#bookingdatefrom').val('');
        $('#bookingdateto').val('');
        $('#documentstatus').val(null).trigger("change");
        $('#jpstatus').val(null).trigger("change");
        $('#sorting_1').addClass('selected');
        getiouvoucherbookingtable();
    }



    function reopen_iou_booking(id) {
        swal({
                title: "Are you sure?",
                text: "You want to reopen IOU Booking!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'bookingMasterID': id},
                    url: "<?php echo site_url('Iou/reopen_iou_booking'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getiouvoucherbookingtable();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function referback_ioubooking(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'bookingMasterID': id},
                    url: "<?php echo site_url('Iou/iou_referback_booking_master_view'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getiouvoucherbookingtable();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function referback_ioubooking_emp(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'bookingMasterID': id},
                    url: "<?php echo site_url('Iou/iou_referback_booking_emp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getiouvoucherbookingtable();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    function documentPageView_modal_ioue(documentID, para1, para2, approval=0) {
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;


        switch (documentID) {
            case "IOUE":
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                paramData.push({name: 'IOUbookingmasterid', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "IOU Booking";
                /**/
                a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                break;

            default:
                notification('Document ID is not set.', 'w');
                return false;
        }
        paramData.push({name: 'html', value: true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: paramData,
            url: siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#documentPageViewTitle').html(title);
                $('#loaddocumentPageView').html(data);
                $('#documentPageView_iou').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');
                stopLoad();

                if (documentID = 'SP') {
                    $('#paysheet-tb').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function referback_jp(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>", /*You want to refer back!*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'jurneyplanid': id},
                    url: "<?php echo site_url('Journeyplan/jp_referback'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getiouvoucherbookingtable();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }
    function fetch_jp_status_modal(id)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'jpnumber': id},
            url: "<?php echo site_url('Journeyplan/load_jp_header'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#jpmasterid').val(data['journeyPlanMasterID']);
                    $('#jpstatusupdate').val(data['status']).change();
                    $('#Commentsjp').val(data['statusComment']);

                    if(data['status'] == 3)
                    {
                        $('#Commentsjp').val(data['closedComment']);
                    }
                    if (data['status'] == 4)
                    {
                        $('#Commentsjp').val(data['canceledComment']);
                    }


                }
                $('#jp_status_drilldown').modal("show");
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });



    }

    function submit_jp_status() {
        var  masterid = $('#jpmasterid').val();
        var  status = $('#jpstatusupdate').val();
        var  comment = $('#Commentsjp').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterid':masterid,'status':status,'comment':comment},
            url: "<?php echo site_url('Journeyplan/save_jp_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getiouvoucherbookingtable();
                    $('#jp_status_drilldown').modal("hide");
                }
            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }

    function configure_ivms_no() {
        $('#ivms_no_congif').modal("show");
    }


</script>