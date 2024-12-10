<?php

function translate_fn($key, $rType=null){
    $primaryLanguage = getPrimaryLanguage();
    $CI =& get_instance();
    $CI->lang->load('hrms_payroll', $primaryLanguage);
    $CI->lang->load('common', $primaryLanguage);

    if($rType == 1){
        return $CI->lang->line($key);
    }
    echo $CI->lang->line($key);
}
$title = translate_fn('hrms_payroll_period_setup', 1);
echo head_page($title, false);

$periodType_arr = system_hrPeriodTypes_drop();
$date_format_policy = date_format_policy();
?>
<style>
    .acc-modal-title{
        font-weight: bold;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7">&nbsp;</div>
    <div class="col-md-5 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-left: 6px;" onclick="new_group()" >
            <i class="fa fa-plus"></i> <?php translate_fn('hrms_payroll_access_rights_new_group');?>
        </button>
    </div>
</div><hr>
<div class="col-sm-12 table-responsive">
    <table class="<?php echo table_class(); ?>"  id="hrPeriod-group-tbl" style="margin-top: 1%">
        <thead>
        <tr>
            <th style="width: auto"> # </th>
            <th style="width: auto"> <?php translate_fn('common_description');?></th>
            <th style="width: auto"> <?php translate_fn('common_type');?></th>
            <th style="width: auto"> <?php translate_fn('common_start_date');?></th>
            <th style="width: auto"> <?php translate_fn('hrms_payroll_access_group');?></th>
            <th style="width: 70px">  </th>
        </tr>
        </thead>
    </table>
</div>
<div class="row">
    <div class="col-md-12"><hr/></div>
</div>


<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="create_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php translate_fn('hrms_payroll_access_rights_new_group');?></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="hrPeriod_frm"'); ?>
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('common_description'); required_mark(); ?>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" name="description" value=""  class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="hr_type">
                        <?php translate_fn('common_type'); required_mark(); ?>
                    </label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('hr_type', $periodType_arr, '', 'class="form-control" id="hr_type"'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="start_date"><?=$this->lang->line('common_start_date');required_mark();?></label>
                    <div class="col-sm-6">
                        <div class="input-group date_pic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="start_date" style="width: 94%;" data-inputmask="'alias': '<?=$date_format_policy?>'"
                                   value="" id="start_date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('hrms_payroll_access_group'); ?>
                    </label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('groups[]', [], '', 'class="form-control access_grp" id="groups1" multiple="multiple"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm " ><?php translate_fn('common_save');?></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php translate_fn('common_Close');?></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="assign_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= translate_fn('hrms_payroll_assign_access_group');?> -  <span class="grp-description"></span>
                </h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="assign_frm"'); ?>
            <div class="modal-body">
                <input type="hidden" name="hrGroupID" id="assign_hrGroupID" value="">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('hrms_payroll_access_group'); required_mark();?>
                    </label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('groups[]', [], '', 'class="form-control access_grp" id="groups2" multiple="multiple"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="assign_access_group()" ><?php translate_fn('common_save');?></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php translate_fn('common_Close');?></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="periods_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-5">
                        <span class="acc-modal-title"><?php translate_fn('common_group');?> : </span> <span class="grp-description"></span>
                    </div>
                    <div class="col-sm-5">
                        <span class="acc-modal-title"><?php translate_fn('common_type');?> : </span> <span class="grp-type">Weekly</span>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>

                <hr style="margin-top: 10px; margin-bottom: 10px;" />

                <div class="row">
                    <div class="col-sm-5">
                        <fieldset class="scheduler-border" style="margin-top: 10px">
                            <legend class="scheduler-border"><?=translate_fn('hrms_payroll_access_group');?></legend>
                            <br/>

                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>"  id="hrAssignGrp-tbl" style="margin-top: 1%">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px"> # </th>
                                        <th style="width: auto"> <?php translate_fn('common_group');?></th>
                                        <th style="width: auto"> <?php translate_fn('common_assign_date');?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border" style="margin-top: 10px">
                            <legend class="scheduler-border"><?=translate_fn('hrms_payroll_hr_period_master');?></legend>
                            <br/>

                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>"  id="hrPeriodMaster-tbl" style="margin-top: 1%">
                                        <thead>
                                        <tr>
                                            <th style="width: 25px"> # </th>
                                            <th style="width: auto"> <?php translate_fn('common_start_date');?></th>
                                            <th style="width: auto"> <?php translate_fn('common_end_date');?></th>
                                            <th style="width: auto"> <?php translate_fn('common_status');?></th>
                                            <th style="width: 25px">  </th>
                                        </tr>
                                        </thead>
                                    </table>
                            </div>
                        </fieldset>
                    </div>


                    <div class="col-sm-7">
                        <fieldset class="scheduler-border" style="margin-top: 10px">
                            <legend class="scheduler-border"><?=translate_fn('common_period');?></legend>
                            <br/>

                            <div class="hr-det alert alert-info" id="no-item-msg">
                                No item selected
                            </div>

                            <div class="hr-det table-responsive" id="tbl-container">
                                <table class="<?php echo table_class(); ?>"  id="hrPeriodDet-tbl" style="margin-top: 1%">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px"> # </th>
                                        <th style="width: auto"> <?php translate_fn('common_start_date');?></th>
                                        <th style="width: auto"> <?php translate_fn('common_end_date');?></th>
                                        <th style="width: 25px">  </th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php translate_fn('common_Close');?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let hrPeriod_frm = $('#hrPeriod_frm');

    $('.access_grp').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    Inputmask().mask(document.querySelectorAll("input"));
    let date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.date_pic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
        widgetPositioning: {
            vertical: 'bottom'
        }
    });

    $(document).ready(function(){
        $('.headerclose').click(function(){
            fetchPage('system/hrm/payroll-period-master','','HRMS');
        });

        load_hr_groupMaster();

        hrPeriod_frm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php translate_fn('common_description_is_required');?>.'}}}
            },
        })
        .on('success.form.bv', function (e) {
            e.preventDefault();
            let postData = hrPeriod_frm.serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_hrPeriod'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        load_hr_groupMaster();
                        $('#create_modal').modal('hide');
                        setTimeout(function(){
                            load_periodDet(data['group_id'], data['description'], data['pr_type']);
                        }, 300);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });

            return false;
        });

    });

    function new_group(){
        hrPeriod_frm[0].reset();

        load_unassigned_access_groups('groups1');

        $('#hrPeriod_frm').bootstrapValidator('resetForm', true);
        $('#create_modal').modal({backdrop: "static"});
    }

    function generate_next_hrPeriod(masterID) {
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/generate_next_hrPeriod'); ?>',
            data: {'groupID': masterID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    load_periodDet(data['group_id'], data['description'], data['pr_type']);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function load_hr_groupMaster(selectedID=null){
        $('#hrPeriod-group-tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_group_masters'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                let selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                let tmp_i = oSettings._iDisplayStart;
                let iLen = oSettings.aiDisplay.length;
                let x = 0;
                for (let i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['hrPeriodID']) == selectedRowID) {
                        let thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $(".dataTables_empty").text('<?php translate_fn('common_no_data_available_in_table'); ?>');
                $(".previous a").text('<?php translate_fn('common_previous'); ?>');
                $(".next  a").text('<?php translate_fn('common_next'); ?>');

                $('.description_xEditable').editable({
                    title: 'Edit Description',
                    placement: 'right',
                    url: '<?php echo site_url('Employee/ajax_update_hrPeriodDescription') ?>',
                    send: 'always',
                    ajaxOptions: {
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            myAlert(data[0], data[1]);
                        },
                        error: function (xhr) {
                            myAlert('e', xhr.responseText);
                        }
                    }
                });

            },
            "aoColumns": [
                {"mData": "hrGroupID"},
                {"mData": "description"},
                {"mData": "periodSysType"},
                {"mData": "active_period"},
                {"mData": "groupName"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                { "searchable": false, "targets": [0]},
                { "orderable": false , "targets": [4,5]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function load_periodDet(hrGroupID, des, periodType){
        $('.grp-description').text(des);
        $('.grp-type').text(periodType);
        $('#periods_modal').modal({backdrop: "static"});

        load_period_setup_groups(hrGroupID);
        $('.hr-det').hide();
        $('#no-item-msg').show();

        $('#hrPeriodMaster-tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_period_master'); ?>",
            //"aaSorting": [[1, 'asc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                let tmp_i = oSettings._iDisplayStart;
                let iLen = oSettings.aiDisplay.length;
                let x = 0;
                for (let i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $(".dataTables_empty").text('<?php translate_fn('common_no_data_available_in_table'); ?>');
                $(".previous a").text('<?php translate_fn('common_previous'); ?>');
                $(".next a").text('<?php translate_fn('common_next'); ?>');

                $('#hrPeriodMaster-tbl tbody').on('click', 'tr', function () {
                    let tbl_id = $(this).parent().parent().attr('id');

                    $('#hrPeriodMaster-tbl tr').removeClass('dataTable_selectedTr');
                    $(this).toggleClass('dataTable_selectedTr');
                });

            },
            "aoColumns": [
                {"mData": "hrPeriodID"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "per_status"},
                {"mData": "action"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0]},
                {"orderable": false , "targets": [3]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'hrGroupID', 'value':hrGroupID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function load_period_setup_groups(hrGroupID){

        $('#hrAssignGrp-tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_period_setup_groups'); ?>",
            //"aaSorting": [[1, 'asc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                let tmp_i = oSettings._iDisplayStart;
                let iLen = oSettings.aiDisplay.length;
                let x = 0;
                for (let i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $(".dataTables_empty").text('<?php translate_fn('common_no_data_available_in_table'); ?>');
                $(".previous a").text('<?php translate_fn('common_previous'); ?>');
                $(".next a").text('<?php translate_fn('common_next'); ?>');

                $('#hrAssignGrp-tbl tbody').on('click', 'tr', function () {
                    let tbl_id = $(this).parent().parent().attr('id');

                    $('#hrAssignGrp-tbl tr').removeClass('dataTable_selectedTr');
                    $(this).toggleClass('dataTable_selectedTr');
                });

            },
            "aoColumns": [
                {"mData": "ID"},
                {"mData": "groupName"},
                {"mData": "createdDate"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'hrGroupID', 'value':hrGroupID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function load_periodSubDet(hrPeriodID){
        $('.hr-det').hide();
        $('#tbl-container').show();
        $('#hrPeriodDet-tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "pageLength": 25,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_period_det'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                let tmp_i = oSettings._iDisplayStart;
                let iLen = oSettings.aiDisplay.length;
                let x = 0;
                for (let i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $(".dataTables_empty").text('<?php translate_fn('common_no_data_available_in_table'); ?>');
                $(".previous a").text('<?php translate_fn('common_previous'); ?>');
                $(".next  a").text('<?php translate_fn('common_next'); ?>');

                $('#hrPeriodDet-tbl tbody').on('click', 'tr', function () {
                    let tbl_id = $(this).parent().parent().attr('id');

                    $('#hrPeriodDet-tbl tr').removeClass('dataTable_selectedTr');
                    $(this).toggleClass('dataTable_selectedTr');
                });

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "action"}
            ],
            "columnDefs": [
                { "searchable": false, "targets": [0]},
                { "orderable": false , "targets": [3]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'hrPeriodID', 'value':hrPeriodID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_hr_periodMaster(masterID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': masterID},
                    url: "<?php echo site_url('Employee/delete_hr_periodMaster'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if(data[0] == 's'){
                            myAlert(data[0], data[1]);
                            load_hr_groupMaster();
                        }
                        else if(data[0] == 'w'){
                            bootbox.alert( data[1] );
                        }
                        else{
                            myAlert(data[0], data[1]);
                        }


                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in delete process');
                    }
                });
            });
    }

    function setup_access_group(hrGroupID, des){
        $('.grp-description').text(des);
        $('#assign_hrGroupID').val(hrGroupID);
        $('#assign_modal').modal({backdrop: "static"});

        load_unassigned_access_groups('groups2');
    }

    function assign_access_group(){
        let postData = $('#assign_frm').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/payroll_assign_access_group'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    load_hr_groupMaster();
                    $('#assign_modal').modal('hide');
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });

    }

    function load_unassigned_access_groups(grp_id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Employee/load_unassigned_access_groups'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                let drop_obj = $('#'+grp_id);
                drop_obj.empty();

                if(!$.isEmptyObject(data)){
                    $.each(data, function(i, val){
                        drop_obj.append(
                            $('<option></option>').val(val['groupID']).html(val['groupName'])
                        );
                    });
                }
                drop_obj.multiselect2('refresh');
                drop_obj.multiselect2('rebuild');

            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in grop loading process');
            }
        });
    }

    function load_access_group(){
        let drop = $("#groups");
        drop.multiselect2("refresh");
        drop.multiselect2("rebuild");
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        let tbl_id = $(this).parent().parent().attr('id');

        $('#'+tbl_id+' tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>
