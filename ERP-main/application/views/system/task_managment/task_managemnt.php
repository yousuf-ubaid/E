<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('crm_tasks');
echo head_page($title, false);
$this->load->helper('task_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$category_arr_filter = load_all_categories(false);
$status_arr_filter = all_task_status(false);
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$cuurentuser = current_userID();
$employees = load_employee_drop_crm();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
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
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .custome {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
    }

    .customestyle {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -46%
    }

    .customestyle2 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    .customestyle3 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;

        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }
    .btn-secondary.active{
        border: solid 1px #3c8dbc;
        background-color:darkgray;
        color: black;
        font-weight: 800;
    }
    .btn-sm{
        border: solid 1px #dcdcdc;
        width: 120px;
        

    }

</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel">

</div>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="fetchPage('system/task_managment/create_new_task',null,'<?php echo $this->lang->line('crm_add_new_task');?>','CRM','CRM');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('crm_create_task');?>
        </button><!--Add New Task--><!--Create Task-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row" style="margin-top: 2%;">
                <div class="col-sm-8" style="margin-left: 34%;">
                    <div class="btn-group" role="group" aria-label="Basic">
                        <!-- Button for "Created By Me" -->
                        <button type="button" class="btn btn-sm btn-primary-new btn-secondary" onclick="setButtonValue(1)">
                            Created By Me
                        </button>
                        <!-- Button for "Assigned To Me" -->
                        <button type="button" class="btn btn-sm btn-primary-new btn-secondary" onclick="setButtonValue(2)">
                            Assigned To Me
                        </button>
                        <!-- Button for "All Tasks" -->
                        <button type="button" class="btn btn-sm btn-primary-new btn-secondary" onclick="setButtonValue(3)">
                            All Tasks
                        </button>
                               
                    </div>

                </div>
                <!-- Input field to store button value -->
                <input type="hidden" id="tasktype" value="">      
            </div>
            <div class="row" style="margin-top: 2%;">
                <div class="col-sm-3" style="margin-left: 2%;">
                    <div class="col-sm-2">
                        <div class="mailbox-controls">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">&nbsp;<label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="<?php echo $this->lang->line('crm_search_task');?>"
                                       id="searchTask" onkeypress="startMasterSearch()"><!--Search Task-->
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="col-sm-4">
                        <?php echo form_dropdown('Category', $category_arr_filter, '', 'class="form-control" id="filter_categoryID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="filter_statusID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('Priority', array('' =>  $this->lang->line('crm_priority')/*'Priority'*/, '1' => $this->lang->line('crm_low')/*'Low'*/, '2' => $this->lang->line('crm_medium')/*'Medium'*/, '3' => $this->lang->line('crm_high')/*'High'*/), '', 'class="form-control" id="filter_Priority" onchange="startMasterSearch()"'); ?>
                    </div>

                    <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </div>
            <div id="taskMaster_view"></div>
        </div>
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
   var per_page = 10;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/Task_management/task_management', '', 'Tasks');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        getTaskManagement_tableView();

    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getTaskManagement_tableView(data_pagination, uriSegment);
    }
    $('#searchTask').bind('input', function(){
        startMasterSearch();

    });

    function setButtonValue(value) {
    $('.btn-secondary').removeClass('active');
    $('.btn-secondary').eq(value - 1).addClass('active');
    $('#tasktype').val(value); 
    getTaskManagement_tableView(); 
}

    function getTaskManagement_tableView(pageID,uriSegment = 0) {
        let searchTask = $('#searchTask').val();
        let category = $('#filter_categoryID').val();
        let status = $('#filter_statusID').val();
        let priority = $('#filter_Priority').val();
        let assignees = $('#filter_assigneesID').val();
        let createdby = $('#createdby').val();
        let tasktype = $('#tasktype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'searchTask': searchTask, status: status, priority: priority, assignees: assignees, category:category,'pageID':pageID,createdby:createdby,tasktype:tasktype},
            url: "<?php echo site_url('Task_management/load_taskManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taskMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                $(".taskprojecteditview").hide();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_task(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*/!*Are you sure?*!/*/
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
                    data: {'taskID': id},
                    url: "<?php echo site_url('Task_management/delete_task_task'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                         if(data[0] == 's')
                        {
                            getTaskManagement_tableView();
                        }

                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getTaskManagement_tableView();
    }



    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#searchTask').val('');
        $('#filter_statusID').val('');
        $('#filter_Priority').val('');
        $('#filter_assigneesID').val('');
        $('#filter_categoryID').val('');
        $('#tasktype').val();
        $('#createdby').val(null).trigger("change");
        getTaskManagement_tableView();
    }


    function edit_task(taskid,createdUserIDtask,assignuser)
    {
        if((createdUserIDtask == '<?php echo $cuurentuser ?>') || ('<?php echo $admin['isSuperAdmin'] ?? 0?>' == 1) || ('<?php echo $isgroupadmin['adminYN'] ?? 0 ?>' == 1) || (assignuser == 1))
        {
            fetchPage('system/task_managment/create_new_task',taskid,'Edit Task','CRM','CRM')
        }else
        {
           myAlert('w','You do not have the permission to edit');
        }


    }
</script>