<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_group');
echo head_page($title, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">


    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/hrm/new_leave_group','','Add Leave group','Leave Group');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_leave_management_create_new');?><!--Create New --></button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="leave_Group_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 5%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

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


<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave_group','','HRMS')
        });
        leave_Group_table();
    });

    function leave_Group_table(selectedRowID=null){
        $('#leave_Group_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/leave_group_master'); ?>",
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


                    if( parseInt(oSettings.aoData[x]._aData['leaveGroupID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "leaveGroupID"},
                {"mData": "description"},
                {"mData": "action"}
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

    function deleteLeave(id){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'leaveGroupID':id},
                    url :"<?php echo site_url('Employee/delete_leaveGroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        myAlert('s','Successfully deleted');
                        leave_Group_table();

                        stopLoad();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_leaveGrpEmployees(id, dec){
        $('#emp-list-title').text(dec);

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
                aoData.push({'name': 'grp', 'value': 'leaveGroupID'});
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
