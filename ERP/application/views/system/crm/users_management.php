<?php  $employees_arr=fetch_employees_by_company_multiple(false);?>
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
                                 src="https://d30chhj7mra175.cloudfront.net/img/user-icon.png"> <?php echo $this->lang->line('crm_users');?>
                        </div><!--Users-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" data-toggle="modal"
                                    data-target="#add-user-modal"><?php echo $this->lang->line('crm_add_new_user');?>
                            </button><!--Add New User-->
                        </div>
                    </div>



                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <table id="usersTable" class="table ">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_employee_name');?> </th><!--Employee Name-->
                                        <th><?php echo $this->lang->line('crm_email_id');?> </th><!--Email ID-->
                                        <th>User Group</th>
                                        <th>Active Y/N</th><!--ActiveYN-->
                                        <th>SuperAdmin Y/N</th><!--SuperAdminYN-->
                                        <th></th>
                                    </tr>
                                    </thead>
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
                <h4 class="modal-title"><?php echo $this->lang->line('crm_add_new_user');?></h4><!--Add New User-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="crm_employee">


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
                                <button type="button" id="singlebutton" onclick="submitusers()" name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                            </div>
                        </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
            </div>
        </div>

    </div>
</div>

<script>
    loaduserDropDown();
    fetch_users();

    function activateUser(thisID,masterID){
            value=0;
        if ($(thisID).is(':checked')) {
            value=1;
        }

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'masterID':masterID,value:value},
            url :"<?php echo site_url('Crm/activateUser'); ?>",
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

    function activateSuperAdmin(thisID,masterID){
        value=0;
        if ($(thisID).is(':checked')) {
            value=1;
        }

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'masterID':masterID,value:value},
            url :"<?php echo site_url('Crm/activateSuperAdmin'); ?>",
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


    function delete_users(userID){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'userID':userID},
                    url :"<?php echo site_url('Crm/delete_user'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0],data[1])
                        if(data[0] == 's')
                        {
                            fetch_users();
                            loaduserDropDown();
                        }

                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetch_users() {
        var Otable = $('#usersTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_users'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"width": "10%", "targets": 0},

                    {"width": "10%", "targets": 4}
            ],
            "aoColumns": [
                {"mData": "useridcrm"},
                {"mData": "employeeName"},
                {"mData": "emailID"},
                {"mData": "usergroup"},
                {"mData": "activeYN"},
                {"mData": "isSuperAdmin"},
                {"mData": "edit"}

            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function loaduserDropDown() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('crm/fetch_userDropdown_employee'); ?>",
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

    function submitusers() {
        var data = $('#crm_employee').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('crm/srp_erp_add_users'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
              myAlert(data[0],data[1]);
                loaduserDropDown();
                fetch_users();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>