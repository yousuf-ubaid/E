<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_over_time_group_master');
echo head_page($title, false);

?>
    <style type="text/css">
        .saveInputs{ height: 25px; font-size: 11px }
        #otCat-add-tb td{ padding: 2px; }
    </style>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openGroupMaster_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="load_OTGroupSetup" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="width: 85px"></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_OTGroup"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="OTGroupMaster_from"'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="description"  id="description" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="editID" name="editID">
                <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="emp_list_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?=$title?> - <span id="emp-list-title"></span></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="group_employees_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="width: 15px">#</th>
                            <th style="width: auto"><?=$this->lang->line('common_employee');?></th>
                            <th style="width: auto"><?=$this->lang->line('common_designation');?></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?=$this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var OTGroupMaster_from = $('#OTGroupMaster_from');
    $('.select2').select2();

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/over_time_group_master','Test','HRMS');
        });

        load_OTGroupSetup();

        OTGroupMaster_from.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
            }
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var requestUrl = $form.attr('action');
            var postData = $form.serializeArray();


            $.ajax({
                type: 'post',
                url: requestUrl,
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_OTGroup').modal('hide');
                        //load_OTGroupSetup( data[2] );
                        setTimeout(function(){
                            fetchPage('system/hrm/ajax/load-over-time-group-setup', null,'HRMS', null, data[3]);
                        }, 300);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });

        });
    });

    function load_OTGroupSetup(selectedRowID=null){
        $('#load_OTGroupSetup').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_OTGroupMaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['groupID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "groupID"},
                {"mData": "description"},
                {"mData": "edit"}
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

    function openGroupMaster_modal(){
        $('#masterCat').val('').change();
        OTGroupMaster_from[0].reset();
        OTGroupMaster_from.bootstrapValidator('resetForm', true);
        OTGroupMaster_from.attr('action', '<?php echo site_url('Employee/save_OTGroupMaster'); ?>');

        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_new_over_time_group_master');?>');/*New OT Group Master*/
        $('#new_OTGroup').modal({backdrop: "static"});
    }

    function edit_OTCat( obj ){
        OTGroupMaster_from[0].reset();
        OTGroupMaster_from.bootstrapValidator('resetForm', true);
        OTGroupMaster_from.attr('action', '<?php echo site_url('Employee/edit_OTGroupMaster'); ?>');

        var details = getTableRowData(obj);

        $('#description').val( $.trim(details.description ) );
        $('#editID').val( $.trim(details.groupID) );

        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_new_over_time_group_master');?>');/*Edit OT Group Master*/

        $('#new_OTGroup').modal({backdrop: "static"});
    }

    function delete_OTCat(obj){
        var details = getTableRowData(obj);
        swal(
            {
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
                    async : true,
                    url :"<?php echo site_url('Employee/delete_OTGroupMaster'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'catID':details.groupID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's'){ load_OTGroupSetup(); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function setupDetails(obj){
        var details = getTableRowData(obj);
        details.edit = null;
        fetchPage('system/hrm/ajax/load-over-time-group-setup', null,'HRMS', null, details);
    }

    function getTableRowData(obj){
        var table = $('#load_OTGroupSetup').DataTable();
        var thisRow = $(obj);
        return table.row(thisRow.parents('tr')).data();
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function load_OTGrpEmployees(obj){
        let details = getTableRowData(obj);
        let id = $.trim(details.groupID);

        $('#emp-list-title').text(details.description);

        $('#emp_list_modal').modal('show');

        $('#group_employees_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/employees_group_tables'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "emp_name"},
                {"mData": "designation"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'grp', 'value': 'overTimeGroup'});
                aoData.push({'name': 'grpID', 'value': id});
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
/**
 * Created by PhpStorm.
 * Date: 1/29/2017
 * Time: 2:09 PM
 */
