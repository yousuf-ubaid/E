<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('crm_opportunities');
echo head_page($title, false);


/*echo head_page('Opportunities', false);*/
$this->load->helper('crm_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$status_arr = all_opportunities_status();
$employees_arr = all_crm_users_responsible();
$employees = load_employee_drop_crm()
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }

    .arrow-steps .step {
        font-size: 12px !important;
        padding: 3px 10px 7px 30px !important;
    }
    .arrow-steps .step:after, .arrow-steps .step:before {
        border-top: 13px solid transparent !important;
        border-bottom: 14px solid transparent !important;
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
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="fetchPage('system/crm/create_opportunity',null,'<?php echo $this->lang->line('crm_add_new_opportunity');?>','CRM');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('crm_new_opportunity');?>
        </button><!--Add New Opportunity--><!--New Opportunity-->
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-1" style="margin-left: 2%;">
                    <div class="mailbox-controls">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns">&nbsp;<label
                                    for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchTask" type="text" class="form-control input-sm"
                                   placeholder="Search Opportunities"
                                   id="searchTask" onkeypress="startMasterSearch()">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                            <?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control" id="filter_statusID"  onchange="startMasterSearch()"'); ?>
                </div>
                <div class="col-sm-2">
                    <?php echo form_dropdown('responsiblePersonEmpID', $employees_arr, '', 'class="form-control select2" id="filter_responsiblePersonEmpID" onchange="startMasterSearch()" '); ?>
                </div>
                <div class="col-sm-2">
                    <?php echo form_dropdown('createdby', $employees, '', 'class="form-control select2" id="createdby"  onchange="startMasterSearch()"'); ?>
                </div>
                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-11">
                    <div id="OpportunityMaster_view"></div>
                </div>
                <div class="col-sm-1">
                    <ul class="alpha-box">
                        <li><a href="#" class="opportunitysorting" id="sorting_1"
                               onclick="load_opportunity_filter('#',1)">#</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_2"
                               onclick="load_opportunity_filter('A',2)">A</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_3"
                               onclick="load_opportunity_filter('B',3)">B</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_4"
                               onclick="load_opportunity_filter('C',4)">C</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_5"
                               onclick="load_opportunity_filter('D',5)">D</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_6"
                               onclick="load_opportunity_filter('E',6)">E</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_7"
                               onclick="load_opportunity_filter('F',7)">F</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_8"
                               onclick="load_opportunity_filter('G',8)">G</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_9"
                               onclick="load_opportunity_filter('H',9)">H</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_10"
                               onclick="load_opportunity_filter('I',10)">I</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_11"
                               onclick="load_opportunity_filter('J',11)">J</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_12"
                               onclick="load_opportunity_filter('K',12)">K</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_13"
                               onclick="load_opportunity_filter('L',13)">L</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_14"
                               onclick="load_opportunity_filter('M',14)">M</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_15"
                               onclick="load_opportunity_filter('N',15)">N</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_16"
                               onclick="load_opportunity_filter('O',16)">O</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_17"
                               onclick="load_opportunity_filter('P',17)">P</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_18"
                               onclick="load_opportunity_filter('Q',18)">Q</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_19"
                               onclick="load_opportunity_filter('R',19)">R</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_20"
                               onclick="load_opportunity_filter('S',20)">S</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_21"
                               onclick="load_opportunity_filter('T',21)">T</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_22"
                               onclick="load_opportunity_filter('U',22)">U</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_23"
                               onclick="load_opportunity_filter('V',23)">V</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_24"
                               onclick="load_opportunity_filter('W',24)">W</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_25"
                               onclick="load_opportunity_filter('X',25)">X</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_26"
                               onclick="load_opportunity_filter('Y',26)">Y</a></li>
                        <li><a href="#" class="opportunitysorting" id="sorting_27"
                               onclick="load_opportunity_filter('Z',27)">Z</a></li>
                    </ul>
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
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var per_page = 10;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/crm/opportunities_management', '', 'Opportunity');
        });
        getOpportunityManagement_tableView();

    });
    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getOpportunityManagement_tableView(filtervalue,data_pagination, uriSegment);
    }

    function getOpportunityManagement_tableView(filtervalue,pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var status = $('#filter_statusID').val();
        var responsible = $('#filter_responsiblePersonEmpID').val();
        var createdby = $('#createdby').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'searchTask': searchTask, 'filtervalue': filtervalue,status:status,responsible:responsible,'pageID':pageID,'createdby':createdby,'uriSegment':uriSegment},
            url: "<?php echo site_url('CrmLead/load_opportunityManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#OpportunityMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_opportunity(id) {
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
                    data: {'opportunityID': id},
                    url: "<?php echo site_url('CrmLead/delete_opportunity_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getOpportunityManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getOpportunityManagement_tableView();
    }

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.opportunitysorting').removeClass('selected');
        $('#searchTask').val('');
        $('#filter_statusID').val('');
        $('#filter_responsiblePersonEmpID').val(null).trigger("change");
        $('#createdby').val(null).trigger("change");
        getOpportunityManagement_tableView();
    }

    function load_opportunity_filter(value, id) {
        $('.opportunitysorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        $('#search_cancel').removeClass('hide');
        getOpportunityManagement_tableView(value)
    }

    function reopenOpportunities(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('crm_you_want_to_reopen');?>",/*You want to reopen this Opportunity!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('crm_reopen');?>",/*Reopen*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'opportunityID': id},
                    url: "<?php echo site_url('CrmLead/reopen_opportunity_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getOpportunityManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

</script>