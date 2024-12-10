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
$title = translate_fn('hrms_payroll_access_rights_title', 1);
echo head_page($title, false);

$groups_arr = fetch_payroll_access_group();
$employee_arr = load_employee_drop();
?>
    <style type="text/css">
        .symbolSty{
            font-weight: bolder;
        }

        legend{
            font-size: 16px !important;
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
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_assign_groupModal()" >
                <i class="fa fa-plus"></i> <?php translate_fn('hrms_payroll_access_rights_assign_in_charge');?>
            </button>
        </div>
    </div><hr>
    <div class="col-sm-12 table-responsive">
        <table class="<?php echo table_class(); ?>"  id="group-master-table" style="margin-top: 1%">
            <thead>
            <tr>
                <th style="width: auto"> # </th>
                <th style="width: auto"> <?php translate_fn('common_description');?></th>
                <th style="width: 70px">  </th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="row">
        <div class="col-md-12"><hr/></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php translate_fn('hrms_payroll_access_unassigned_employees');?></legend>
                <div class="table-responsive" style="margin-top: 25px">
                    <table id="un_assignedTB" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 25%"><?php translate_fn('hrms_payroll_emp_code');?><!--EMP Code--></th>
                            <th style="width:auto"><?php translate_fn('hrms_payroll_employee_name');?><!--Employee Name--></th>
                            <th style="width:auto"><?php translate_fn('common_segment');?><!--Segment--></th>
                            <th style="width:auto"><?php translate_fn('common_designation');?><!--Designation--></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>
 

<div class="modal fade" id="create_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php translate_fn('hrms_payroll_access_rights_new_group');?></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="groupMaster_form"'); ?>
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('common_description'); required_mark(); ?>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" name="description" value=""  class="form-control" />
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

<div class="modal fade" id="assign_group" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php translate_fn('hrms_payroll_access_rights_assign_in_charge');?></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="groupAssign_form"'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('hrms_payroll_access_rights_groups'); required_mark(); ?>
                    </label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('groups[]', $groups_arr, '', 'class="form-control" id="groups" multiple="multiple"'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description">
                        <?php translate_fn('hrms_payroll_access_rights_in_charges'); required_mark(); ?>
                    </label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('empID[]', $employee_arr, '', 'class="form-control" id="empID" multiple="multiple"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="assign_group()"><?php translate_fn('common_save');?></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php translate_fn('common_Close');?></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var groupMaster_form = $('#groupMaster_form');
    var groupAssign_form = $('#groupAssign_form');

    $('#groups').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#empID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function(){
        $('.headerclose').click(function(){
            fetchPage('system/hrm/payroll-access-rights/payroll-access-rights','Test','HRMS');
        });

        load_groupMaster();

        un_assigned_employees();

        groupMaster_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php translate_fn('common_description_is_required');?>.'}}}
            },
        })
         .on('success.form.bv', function (e) {
            e.preventDefault();
            var postData = groupMaster_form.serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_access_right_master'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#create_modal').modal('hide');
                        load_groupSetup(data[2], data[3]);
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
        $('.my-title').text();
        groupMaster_form[0].reset();
        $('#create_modal').modal({backdrop: "static"});
    }

    function open_assign_groupModal(){
        groupAssign_form[0].reset();
        $("#groups, #empID").multiselect2("refresh");
        $('#assign_group').modal({backdrop: "static"});
    }

    function assign_group(){
        var postData = groupAssign_form.serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/assign_in_charges'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();

                if(data[0] == 's'){
                    myAlert(data[0], data[1]);
                    $('#assign_group').modal('hide');
                }
                else if(data[0] == 'w'){
                    bootbox.alert( data[1] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function load_groupMaster(selectedID=null){
        $('#group-master-table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_payroll_group_master'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['groupID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $(".dataTables_empty").text('<?php translate_fn('common_no_data_available_in_table'); ?>');
                $(".previous a").text('<?php translate_fn('common_previous'); ?>');
                $(".next  a").text('<?php translate_fn('common_next'); ?>');

            },
            "aoColumns": [
                {"mData": "groupID"},
                {"mData": "groupName"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function load_groupSetup(id, descriptipn){
        fetchPage('system/hrm/payroll-access-rights/payroll-access-rights-details',id,'HRMS', '', descriptipn)
    }

    function delete_groupSetup(masterID) {
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
                    url: "<?php echo site_url('Employee/delete_groupSetup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if(data[0] == 's'){
                            myAlert(data[0], data[1]);
                            load_groupMaster();
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

    function un_assigned_employees(){

        $('#un_assignedTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/get_employees_for_access_rights'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "segTBCode"},
                {"mData": "designationStr"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'groupID', 'value':''});
                aoData.push({'name':'empType', 'value':'employee'});
                aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});
                aoData.push({'name':'designationFilter', 'value':$('#designationID').val()});
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
</script>

<?php
