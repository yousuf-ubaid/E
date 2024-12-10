<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo head_page($this->lang->line('iou_booking'), true);
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
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_from'); ?></label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="bookingdatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="bookingdatefrom" class="form-control"  value=""  >
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to'); ?>&nbsp&nbsp</label>
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
                onclick="fetchPage('system/iou/create_iou_booking',null,'<?php echo $this->lang->line('iou_add_new_iou_booking'); ?>','IOUE');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new'); ?>
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="<?php echo $this->lang->line('iou_enter_your_text_here'); ?>"
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
        <?php echo form_dropdown('iouvoucherstatus', array('' => $this->lang->line('common_status'), '1' => $this->lang->line('common_draft'), '2' => $this->lang->line('common_confirmed'), '3' => $this->lang->line('common_approved'), '4' => $this->lang->line('common_submited')), '', 'class="form-control" onchange="startMasterSearch()" id="iouvoucherstatus"'); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="ioubookingmasterview">
            <!-- /.table -->
        </div>

    </div>
</div>
<div class="modal fade" id="documentPageView_iou" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                <li id="TabViewActivation_view" class="active"><a href="#home-v"
                                                                                  data-toggle="tab">
                                        <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                                <li id="TabViewActivation_attachment">
                                    <a href="#profile-v" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                                </li>
                                <li class="itemMasterSubTab_footer" id="tab_itemMasterTabF">
                                    <a href="#subItemMaster-v" data-toggle="tab">
                                        <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item&nbsp;Master&nbsp;Sub--></a>
                                </li>

                            </ul>
                        </div>
                        <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                            <!-- Tab panes -->
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="home-v">
                                    <div id="loaddocumentPageView" class="col-md-12"></div>
                                </div>
                                <div class="zx-tab-pane" id="profile-v">
                                    <div id="loadPageViewAttachment" class="col-md-8">
                                        <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                                            &nbsp <strong>
                                                <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                            <br><br>
                                            <table class="table table-striped table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                                    <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                                </tr>
                                                </thead>
                                                <tbody id="View_attachment_modal_body" class="no-padding">
                                                <tr class="danger">
                                                    <td colspan="5" class="text-center">
                                                        <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="zx-tab-pane" id="subItemMaster-v">
                                    <div class="itemMasterSubTab_footer">
                                        <h4>
                                            <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item Master Sub--></h4>
                                        <div id="itemMasterSubTab_footer_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
            fetchPage('system/iou/iou_booking','','<?php $this->lang->line('iou_booking')?>');
        });
        getiouvoucherbookingtable();
        Inputmask().mask(document.querySelectorAll("input"));
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function getiouvoucherbookingtable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var empid = $('#employeesearch').val();
        var datefrom = $('#bookingdatefrom').val();
        var dateto = $('#bookingdateto').val();
        var status = $('#iouvoucherstatus').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask,'filtervalue':filtervalue,'empid':empid,'datefrom':datefrom,'dateto':dateto,'status':status,'EmployeeYN':0},
            url: "<?php echo site_url('Iou/load_iou_voucherbooking_view_buyback'); ?>",
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
        $('#employeesearch').val(null).trigger("change");
        $('#iouvoucherstatus').val('');
        $('#sorting_1').addClass('selected');
        getiouvoucherbookingtable();
    }



    function reopen_iou_booking(id) {
        swal({
                title:  "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('iou_you_want_to_reopen_iou_booking'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>!",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
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
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").hide();
                $("#TabViewActivation_view").hide();
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                paramData.push({name: 'IOUbookingmasterid', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "IOU Booking";
                /**/
                a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                break;

            default:
                notification('<?php echo $this->lang->line('iou_document_id_is_not_set');?>.', 'w');
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



</script>