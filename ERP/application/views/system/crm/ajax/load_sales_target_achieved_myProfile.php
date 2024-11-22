<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('crm', $primaryLanguage);

$currentUserID = current_userID();
$convertFormat = convert_date_format_sql();

//$title = $this->lang->line('crm_advance_reporting');
//echo head_page($title, false);

/*echo head_page('Advanced Reporting', false);*/
$this->load->helper('crm_helper');
$status_arr_filter = all_task_status(false);
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$groupmaster_arr = all_crm_groupMaster(false);
$assignees_arr_filter = load_all_employees_taskFilter(false);
$types_arr_filter = all_campaign_types(false);
$status_arr_filter_campaign = all_campaign_status(false);
$category_arr_filter_task = load_all_categories(false);
$assignees_campaign_arr_filter = load_all_employees_campaignFilter(false);
$employees_arr = all_crm_employees_drop(true);
$status_arr_filter_leads = lead_status();
$status_arr_oppo = all_opportunities_status();
$status_arr_pro = all_project_status();
$category_arr_pro = all_projects_category();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);

$seg_p = fetch_segment_v2();
$arr_employees = fetch_employees_by_company_multiple();
$arr_crm_products = all_crm_product_master();

?>

<style>
.p-0{
    padding:0px;
}
.p-1{
    padding:10px;
}
.p-2{
    padding:20px;
}
</style>


<div class="p-2">
    <div class="row mT20 s_target">
    <div class="col-sm-2 s_target">
            <span style="font-weight: bold;">Start Dates</span>
            <br>
            <div class="input-group datepicsales ">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="datefromtasksales"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                    value=" " id="datefromtasksales" class="form-control">
            </div>
        </div>

        <div class="col-sm-2 s_target">
            <span style="font-weight: bold;">End Dates</span>
            <br>
            <div class="input-group datepicsales">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="datetotasksales"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                    value=" " id="datetotasksales" class="form-control">
            </div>
        </div>
        <div class="col-sm-1 s_target" id="search_dashboard_cancel2" style="margin-top: 1%;">
                        <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                    onclick="clearDashboardSearchFilter('sales')"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
        <div class="col-sm-2 s_target">
            <span style="font-weight: bold;">Segment</span>
            <br>
            <?php echo form_dropdown('segmentID', $seg_p, '', 'class="form-control select2 pull-right " id="segmentID" onchange="loadreportfilterview()"'); ?>
        </div>

        <div class="col-sm-2 s_target">
            <span style="font-weight: bold;">Products</span>
        <br>
            <?php echo form_dropdown('arr_crm_productsID', $arr_crm_products, '', 'class="form-control select2 pull-right " id="arr_crm_productsID" onchange="loadreportfilterview()"'); ?>
        </div>
       



        <div class="col-sm-1 s_target " id="search_dashboard_cancelcamp2">
                        <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                    onclick="clearDashboardSearchFiltercampSales()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
</div>


<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
               
                    <article class="">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12 sales_target_report" id="taskreport">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" colspan="1" class="align-left">
                                                Segments
                                            </th>
                                            <th rowspan="2" colspan="1" class="align-left">
                                                Products
                                            </th>
                                            <th rowspan="2" colspan="1"  class="align-left">
                                                Employee
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-1">
                                                Target
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-2">
                                                Achieved
                                            </th>
                                            <th rowspan="1" colspan="4" class="bg-3" >
                                                Variance
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-1">
                                                In Opportunity
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="bg-1">Unit</th>
                                            <th class="bg-1">Value</th>
                                            <th class="bg-2">Unit </th>
                                            <th class="bg-2">Value</th>
                                            
                                            <th class="bg-3">Unit</th>
                                            <th class="bg-3">%</th>
                                            <th class="bg-3">Value</th>
                                            <th class="bg-3">%</th>

                                            <th class="bg-1">Unit</th>
                                            <th class="bg-1">Value</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        if (!empty($sales_target)) {

                                           
                                            foreach ($sales_target as $row) {  ?>
                                                <tr>
                                                <td><?php echo $row['description']; ?></td>
                                                    <td><?php echo $row['productName']; ?></td>
                                                    <td><?php echo $row['employeeName']; ?></td>
                                                    <td class="align-right"><?php echo $row['units']; ?></td>
                                                    <td class="align-right"><?php echo number_format($row['targetValue']);  ?></td>
                                                    <td class="align-right"><?php echo $row['achieved_units']; ?></td>
                                                    <td class="align-right"><?php echo number_format($row['acheivedValue']); ?></td>
                                                    <td class="align-right"><?php echo ($row['achieved_units']-$row['units']); ?></td>
                                                    <?php if($row['units'] > 0) { ?>
                                                        <td class="align-center"><?php echo number_format((($row['achieved_units']/$row['units']))*100,2);  ?>%</td>
                                                    <?php } ?>
                                                    <?php if($row['targetValue'] > 0) { ?>
                                                        <td class="align-right"><?php echo number_format(($row['acheivedValue'] - $row['targetValue'])); ?></td>
                                                    <?php } ?>
                                                    <td class="align-center"><?php echo round((($row['acheivedValue'] / $row['targetValue']) *100),2); ?>%</td>
                                                    <!--<td style="text-align: center"><?php /*echo $row['progress']." %" */?></td>-->
                                                    <td><?php echo $row['count']; ?></td>
                                                    <td><?php echo number_format($row['val']); ?></td>
                                                </tr>
                                        <?php  } ?>                                           
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="9" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?> </td><!--No Records Found -->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </article>
              
            </div>
        </div>
    </section>
</div>







<script>
    $('.coll').click(function () {
        var glcode = $(this).attr('data-id');
        var header = $(this).attr('data-head');
        var type = $(this).attr('data-type');
        if ($(this).hasClass('fa fa-plus-square')) {
            $('#table_' + glcode).addClass("hide");
            $(this).removeClass("fa fa-plus-square").addClass("fa fa-minus-square");
        }
        else {
            $(this).removeClass("fa fa-minus-square").addClass("fa fa-plus-square");
            $('#table_' + glcode).removeClass("hide");
        }
    });
</script>

<script type="text/javascript">

    $(document).ready(function () {
        $('.select2').select2();
        page_name = <?php echo json_encode(trim($this->input->post('page_name'))); ?>;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('#groupID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            configuration_page('task','html');
        });

        $('.datepicsales').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            configuration_page('sales_target','html');
        });


        $('#groupEmployeeID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

    });

    function configuration_page(sys, page) {
        var groupEmployeeID = $('#groupEmployeeID').val();
        var groupID = $('#groupID').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var dateyear = $('#dateyear').val();
        var category = $('#categoryID').val();
        var assigneeid =  $('#assigneeid').val();
        var statusid =  $('#statusid').val();
        var campcatid =  $('#typeID').val();
        var campstatusid = $('#statusidcamp').val();
        var catergorytask = $('#categorytaskassignee').val();
        var assigneescamp = $('#assigneescamp').val();
        var leadsrptuser = $('#leadsrptuser').val();
        var leadsstatus = $('#leadsstatus').val();
        var opporstatusid = $('#opporstatusid').val();
        var filter_userID = $('#filter_userID').val();
        var segmentID = $('#segmentID').val();
        var arr_crm_productsID = $('#arr_crm_productsID').val();
        var responsiblePersonEmpIDopprpt = $('#responsiblePersonEmpIDopprpt').val();
        var prostatusid = $('#prostatusid').val();
        var responsiblePersonEmpIDpro = $('#responsiblePersonEmpIDpro').val();
        var catergorypro = $('#catergorypro').val();
        var datefromtask = $('#datefromtask').val();
        var datetotask = $('#datetotask').val();
        var datefromtasksales = $('#datefromtasksales').val();
        var datetotasksales = $('#datetotasksales').val();
        var sortbyval = $('#sortbyval').val();        
       
        if(sys == 'sales_target') {
            $('.s_target').removeClass('hidden');
        }else
        {
            $('.s_target').addClass('hidden');
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {sys: sys, groupEmployeeID: groupEmployeeID, page: page, groupID: groupID, datefrom: datefrom, dateto: dateto, dateyear: dateyear, category: category,assigneeid:assigneeid,statusid:statusid,typeID:campcatid,statusidcamp:campstatusid,categorytaskassignee:catergorytask,assigneescamp:assigneescamp,leadsrptuser:leadsrptuser,leadsstatus:leadsstatus,opporstatusid:opporstatusid,responsiblePersonEmpIDopprpt:responsiblePersonEmpIDopprpt,prostatusid:prostatusid,responsiblePersonEmpIDpro:responsiblePersonEmpIDpro,filter_userID:filter_userID,arr_crm_productsID:arr_crm_productsID,segmentID:segmentID,catergorypro:catergorypro,datefromtask:datefromtask,datefromtasksales:datefromtasksales,datetotasksales:datetotasksales, datetotask: datetotask,sortbyvalue:sortbyval},
            url: "<?php echo site_url('CrmLead/reports_management'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_configuration_view').html(data);
                $('#list-main li').removeClass('active');
                $('.' + sys).addClass('active');


                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_contact(id) {
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
                    data: {'contactID': id},
                    url: "<?php echo site_url('Crm/delete_contact_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getContactManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getContactManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.contactsorting').removeClass('selected');
        $('#searchTask').val('');
        getContactManagement_tableView();
    }

    function load_contact_filter(value, id) {
        $('.contactsorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        $('#search_cancel').removeClass('hide');
        getContactManagement_tableView(value)
    }

    function clearDashboardSearchFilter(view = null) {
       
        if(view == 'sales'){
            $('#datefromtasksales').val(' ');
            $('#datetotasksales').val(' ');
        }else{
            $('#search_dashboard_cancel').addClass('hide');
            $('#assigneeid').val(null).trigger('change');
            $('#categorytaskassignee').val(null).trigger('change');
            $('#statusid').val(null).trigger('change');
            $('#datefromtask').val(' ');
            $('#datetotask').val(' ');
        }

        loadreportfilterview();
    }

    function clearDashboardSearchFiltercamp() {
        $('#search_dashboard_cancel').addClass('hide');
        $('#typeID').val(null).trigger('change');
        $('#statusidcamp').val(null).trigger('change');
        $('#assigneescamp').val(null).trigger('change');


        loadreportfilterview();
    }

    function clearDashboardSearchFiltercampSales(){
        
        $('#filter_userID').val(null).trigger('change');
        $('#segmentID').val(null).trigger('change');
        $('#arr_crm_productsID').val(null).trigger('change');

        loadreportfilterview();
    }

    function clearDashboardSearchFilterlead() {
        $('#search_dashboard_cancellead').addClass('hide');
        $('#leadsrptuser').val(null).trigger('change');
        $('#leadsstatus').val(null).trigger('change');


        loadreportfilterview();
    }
    function clearDashboardSearchFilteropprpt() {
        $('#search_dashboard_cancellead').addClass('hide');
        $('#opporstatusid').val(null).trigger('change');
        $('#responsiblePersonEmpIDopprpt').val(null).trigger('change');
        $('#sortbyval').val(1).trigger('change');


        loadreportfilterview();
    }
    function clearDashboardSearchFilterpro() {
        $('#search_dashboard_cancelpro').addClass('hide');
        $('#prostatusid').val(null).trigger('change');
        $('#responsiblePersonEmpIDpro').val(null).trigger('change');
        $('#catergorypro').val(null).trigger('change');
        loadreportfilterview();
    }

    function load_group_members() {
        $('#search_dashboard_cancel').removeClass('hide');
        var masterID = $('#groupID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("CrmLead/load_dashboard_groupEmployees"); ?>',
            dataType: 'html',
            data: {masterID: masterID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_groupemployee').html(data);
                $('#groupEmployeeID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    buttonWidth: '180px',
                    maxHeight: '30px'
                });
                $("#groupEmployeeID").multiselect2('selectAll', false);
                $("#groupEmployeeID").multiselect2('updateButtonText');
                loadreportfilterview();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function loadreportfilterview() {
        id = 9;
        if (id == 1) {
            configuration_page('contact', 'html');
        } else if (id == 2) {
            configuration_page('organization', 'html');
        } else if (id == 3) {
            $('#search_dashboard_cancel').removeClass('hide');
            configuration_page('task', 'html');
        } else if (id == 4) {
            $('#search_dashboard_cancelcamp').removeClass('hide');
            configuration_page('campaign', 'html');
        } else if (id == 5) {
            $('#search_dashboard_cancellead').removeClass('hide');
            configuration_page('leadnew', 'html');
        } else if (id == 6) {
            $('#search_dashboard_cancelopprpt').removeClass('hide');
            configuration_page('opportunity', 'html');
        } else if (id == 7) {
            $('#search_dashboard_cancelpro').removeClass('hide');
            configuration_page('project', 'html');
        } else if (id == 9) {
            $('#search_dashboard_cancel2').removeClass('hide');
            configuration_page('sales_target', 'html');
        }
    }
    /*call report content pdf*/
    function generateReportPdf(reportType) {
        var categoryID = $('#categoryID').val();
        var groupID = [];
        var groupEmployeeID = [];
        $('#reportpdftype').val('');
        $('#reportpdftype').val(reportType);
        var form = document.getElementById('frm_report_filter');
        document.getElementById('assigneeid');
        document.getElementById('statusid');
        document.getElementById('dateyear');
        document.getElementById('dateto');
        document.getElementById('datefrom');
        document.getElementById('categoryID');
        document.getElementById('assigneescamp');
        document.getElementById('categorytaskassignee');
        document.getElementById('typeID');
        document.getElementById('statusidcamp');
        document.getElementById('leadsrptuser');
        document.getElementById('leadsstatus');
        document.getElementById('opporstatusid');
        document.getElementById('responsiblePersonEmpIDopprpt');
        document.getElementById('prostatusid');
        document.getElementById('responsiblePersonEmpIDpro');
        document.getElementById('catergorypro');
        form.target = '_blank';
        form.action = '<?php echo site_url('CrmLead/reports_management'); ?>';
        form.submit();
    }

    function generateReportPdfmoni(reportType) {
        $('#reportpdftypepdf').val('');
        $('#reportpdftypepdf').val(reportType);
        var dateyearpdf=$('#dateyear').val();
        $('#dateyearpdf').val(dateyearpdf);
        var dateto=$('#dateto').val();
        $('#datetopdf').val(dateto);
        var datefrom=$('#datefrom').val();
        $('#datefrompdf').val(datefrom);
        var categoryID=$('#categoryID').val();
        $('#categoryIDpdf').val(categoryID);

        var form = document.getElementById('frm_report_filterPprojectmoni');

        form.post = '1';
        form.target = '_blank';
        form.action = '<?php echo site_url('CrmLead/reports_management'); ?>';
        form.submit();
    }

    function open_project_dd_model(projectIds,category){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'projectIds': projectIds ,'category': category},
            url: "<?php echo site_url('CrmLead/load_projectManagement_view_idwise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_dd_model').modal('show');
                $('#ProjectddMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_project_edit(path,projectID,heading,project){
        $('#project_dd_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'CRMDD') }, 50);

    }

    function open_task_dd_model(taskIds){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'taskIds': taskIds},
            url: "<?php echo site_url('CrmLead/load_taskManagement_view_idwise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_dd_model').modal('show');
                $('#taskddMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_task_edit(path,projectID,heading,project){
        $('#task_dd_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'CRMTSK') }, 40);

    }
</script>