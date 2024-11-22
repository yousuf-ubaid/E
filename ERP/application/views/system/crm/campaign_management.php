<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->load->helper('crm_helper');
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('crm_campaign');
echo head_page($title, false);
/*echo head_page('Campaign', false);*/
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$this->load->helper('crm_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$types_arr_filter = all_campaign_types(false);
$status_arr_filter = all_campaign_status(false);
$assignees_arr_filter = load_all_employees_campaignFilter(false);
$cuurentuser = current_userID();
$employees = load_employee_drop_crm();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
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
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
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

</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary "
                onclick="fetchPage('system/crm/create_new_campaign',null,'<?php echo $this->lang->line('crm_add_new_campaign');?>','CRM');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('crm_campaign');?>
        </button><!--Add New Campaign--><!--Campaign-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row" style="margin-top: 2%;">
                <div class="col-sm-4" style="margin-left: 2%;">
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
                                <input name="searchCampaign" type="text" class="form-control input-sm"
                                       placeholder="<?php echo $this->lang->line('crm_search_campaign');?>"
                                       id="searchCampaign" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div><!--Search Campaign-->
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="col-sm-2">
                        <?php echo form_dropdown('typeID', $types_arr_filter, '', 'class="form-control" id="filter_typeID" onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-2">
                        <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="filter_statusID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('assigneesID', $assignees_arr_filter, '', 'class="form-control" id="filter_assigneesID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('createdby', $employees, '', 'class="form-control select2" id="createdby"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </div>
            <br>
            <div id="campaignMaster_view"></div>
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
    var Otable;
    var per_page = 10;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/crm/campaign_management', '', 'Campaign');
        });
        getCampaignManagement_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });
    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getCampaignManagement_tableView(data_pagination, uriSegment);
    }
    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function getCampaignManagement_tableView(pageID,uriSegment = 0) {
        var searchCampaign = $('#searchCampaign').val();
        var status = $('#filter_statusID').val();
        var type = $('#filter_typeID').val();
        var assignee = $('#filter_assigneesID').val();
        var createdby = $('#createdby').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'searchCampaign': searchCampaign, status: status, type: type, assignee: assignee,'pageID':pageID,'createdby':createdby},
            url: "<?php echo site_url('crm/load_campaignManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#campaignMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getCampaignManagement_tableView();
    }

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#filter_typeID').val('');
        $('#filter_statusID').val('');
        $('#filter_assigneesID').val('');
        $('#searchCampaign').val('');
        $('#createdby').val(null).trigger("change");
        getCampaignManagement_tableView();
    }

    function delete_campaign(id) {
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
                    data: {'campaignID': id},
                    url: "<?php echo site_url('Crm/delete_campaign'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0],data[1]);
                        stopLoad();
                        if(data[0]=='s')
                        {
                            getCampaignManagement_tableView();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
   function edit_campaign(campaignid,createdUserIDtask,assignuser)
    {
        if((createdUserIDtask == '<?php echo $cuurentuser ?>') || ('<?php echo $admin['isSuperAdmin'] ?? 0 ?>' == 1) || ('<?php echo $isgroupadmin['adminYN'] ?? 0?>' == 1) || (assignuser == 1))
        {
            fetchPage('system/crm/create_new_campaign',campaignid,'Edit Task','CRM','CRM')
        }else
        {
            myAlert('w','You do not have the permission to edit');
        }


    }


</script>