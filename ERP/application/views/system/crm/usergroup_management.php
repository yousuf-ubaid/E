<style>
    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .flex {
        display:
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <img class="title-icon"
                                 src="https://d30chhj7mra175.cloudfront.net/img/user-icon.png"> <?php echo $this->lang->line('crm_user_group');?>
                        </div>
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" data-toggle="modal"
                                    data-target="#add-addusergroupmodal-modal">Add Group
                            </button>
                        </div>

                        <!--User Group-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <table id="usersTable" class="table ">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('crm_group_name');?> </th><!--Group Name-->
                                        <th><?php echo $this->lang->line('crm_admin_group_yn');?> </th><!--AdminGroupYN-->
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div id="add-user-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('crm_assign_employee');?> </h4><!--Assign Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_employee">
                        <input type="hidden" id="groupID" name="groupID">

                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_select_an_employee');?> </label><!--Select an Employee-->
                            <div class="col-md-6" id="div_loaduser">


                            </div>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitusersgroupemployee()"
                                        name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?>
                                </button><!--Submit-->
                            </div>
                        </div>


                    </form>
                    <table id="assignedEmployee" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_employee_name');?> </th><!--Employee Name-->
                            <th></th>
                        </tr>
                        </thead>

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>

        </div>
    </div>
    <div id="add-addusergroupmodal-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add User Group</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_usergroup_modal">
                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">User Group</label>
                            </div>
                            <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="groupName" name="groupName" placeholder="Group Name" required>
                    <span class="input-req-inner"></span>
                            </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary" onclick="submitusersgroup();"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                             aria-hidden="true"></span> Save
                    </button>
                </div>
            </div>

        </div>
    </div>


    <script>
        var groupID;
        fetch_usersgroups();

        function fetch_assignedEmployee() {
            $('#assignedEmployee').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Crm/load_assigned_employee'); ?>",
                "aaSorting": [[1, 'desc']],
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

                "columnDefs": [
                    {"width": "2%", "targets": 0},

                    {"width": "6%", "targets": 1},
                    {"width": "7%", "targets": 2}
                ],
                "aoColumns": [
                    {"mData": "groupDetailID"},

                    {"mData": "employeeName"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "groupID", "value": $("#groupID").val()});
                    //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


        function loadassignedemployee(groupID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {groupID: groupID},
                url: "<?php echo site_url('crm/load_assigned_employee'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loaduser').html(data);

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function view_group(id) {
            groupID = id;
            $('#groupID').val(groupID);
            loaduserDropDown(groupID);

            $('#add-user-modal').modal('show');
            fetch_assignedEmployee();

        }
function delete_usersgroupdetail(groupDetailID){
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
                data: {'groupDetailID': groupDetailID},
                url: "<?php echo site_url('Crm/delete_usergroupdetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    myAlert('s', 'Deleted Successfully');
                    loaduserDropDown($('#groupID').val());
                    fetch_assignedEmployee();

                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

        function delete_users(groupID) {
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
                        data: {'groupID': groupID},
                        url: "<?php echo site_url('Crm/delete_usergroup'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_usersgroups();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function fetch_usersgroups() {
            var Otable = $('#usersTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_usergroups'); ?>",
                "aaSorting": [[1, 'desc']],
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

                "columnDefs": [
                    {"width": "10%", "searchable": false, "targets": 0},
                    {"width": "60%", "targets": 1},
                    {"width": "10%", "searchable": false, "targets": 2},
                    {"width": "20%", "targets": 3}
                ],
                "aoColumns": [
                    {"mData": "groupID"},
                    {"mData": "groupName"},
                    {"mData": "isAdmin"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                    //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


        function loaduserDropDown(groupID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {groupID: groupID},
                url: "<?php echo site_url('crm/fetch_userassignedDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loaduser').html(data);
                    $('#employeesID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function submitusersgroupemployee() {
            var data = $('#crm_employee').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/assign_employee_usergroup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    loaduserDropDown(groupID);
                    fetch_assignedEmployee();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function submitusersgroup() {
            var data = $('#crm_usergroup_modal').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/srp_erp_add_usergroup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if(data[0]=='s')
                    {
                        $('#groupName').val('');
                        fetch_usersgroups();
                        $('#add-addusergroupmodal-modal').modal('hide')
                    }

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function activateGroup(thisID,masterID){
            value=0;
            if ($(thisID).is(':checked')) {
                value=1;
            }

            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'masterID':masterID,value:value},
                url :"<?php echo site_url('Crm/activateGroupAdmin'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    myAlert('s','Sucessfully updated');
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });


        }
    </script>