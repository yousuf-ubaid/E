<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
$companyType = $this->session->userdata("companyType");
if ($companyType == 1) {
    $load_company = fetch_company_by_id();
    $segment = fetch_segment();
} else {
    $load_company = get_group_company(false);
    $segment = array('' => 'Select Segment');
    $employee = array('' => 'Select Employee');
}
$current_date = current_format_date();
$date_format_policy = date_format_policy();
?>
    <div class="box box-info">
        <?php if (!empty($template_arr)){ ?>

        <div class="box-header with-border">
            <?php if ($companyType == 1) { ?>
            <div class="row">

                <div class="form-group  col-md-7">
                    <h4 class="box-title"><?php echo $this->lang->line('dashboard_monthly_performance_report'); ?></h4>
                    <a href="" class="btn btn-excel btn-xs" id="btn-excel-mpr-<?php echo $userDashboardID ?>"
                       download="MPR.xls" style="margin-bottom: 3px; display: inline-block;"
                       onclick="var file = tableToExcel('MPR_view<?php echo $userDashboardID ?>', 'MPR'); $(this).attr('href', file);"><i
                                class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a>
                </div>
                <div class="form-group col-md-2">
                    <div class="col-sm-12">
                        <label for="">Month</label>
                    </div>
                    <div class="col-sm-12">
                        <?php echo form_dropdown('finance_periods_mpr', $period_arr, $current_month, 'id="finance_periods_mpr' . $userDashboardID . '" '); ?>
                    </div>
                </div>

                <div class="form-group  col-md-2 ">
                    <div class="col-sm-12">
                        <label for="">Template</label>
                    </div>
                    <div class="col-sm-12">
                        <?php echo form_dropdown('mpr_template', $template_arr, '', 'id="mpr_template' . $userDashboardID . '"'); ?>
                    </div>
                </div>

                <div class="form-group  col-md-1">
                    <button type="button" class="btn btn-primary"
                            onclick="load_mpr_view<?php echo $userDashboardID ?>();"
                            style="position: absolute;margin-top: 19%">
                        <i class="fa fa-search"></i>
                    </button>

                </div>

            </div>
        </div>
    <?php } else { ?>

        <div class="row">
            <div class="form-group col-md-5">
                <h4 class="box-title"><?php echo $this->lang->line('dashboard_monthly_performance_report'); ?></h4>
                <a href="" class="btn btn-excel btn-xs" id="btn-excel-mpr-<?php echo $userDashboardID ?>"
                   download="MPR.xls" style="margin-bottom: 3px; display: inline-block;"
                   onclick="var file = tableToExcel('MPR_view<?php echo $userDashboardID ?>', 'MPR'); $(this).attr('href', file);"><i
                            class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a>
            </div>
            <div class="form-group col-md-2">
                <div class="col-sm-12">
                    <label for="">Month</label>
                </div>
                <div class="col-sm-12">
                    <?php echo form_dropdown('finance_periods_mpr', $period_arr, $current_month, 'id="finance_periods_mpr' . $userDashboardID . '" '); ?>
                </div>
            </div>
            <div class="form-group  col-md-2  ">
                <div class="col-sm-12">
                    <label for="">Company</label>
                </div>
                <div class="col-sm-12">
                    <?php echo form_dropdown('companygroupfilter[]', get_group_company(), '', 'multiple  class="form-control companygroupfilter" id="companygroupfilter"'); ?>
                </div>
            </div>

            <div class="form-group  col-md-2 ">
                <div class="col-sm-12">
                    <label for="">Template</label>
                </div>
                <div class="col-sm-12">
                    <?php echo form_dropdown('mpr_template', $template_arr, '', 'id="mpr_template' . $userDashboardID . '"'); ?>
                </div>
            </div>

            <div class="form-group  col-md-1 ">
                <button type="button" class="btn btn-primary"
                        onclick="load_mpr_view<?php echo $userDashboardID ?>();"
                        style="position: absolute;margin-top: 19%">
                    <i class="fa fa-search"></i>
                </button>

            </div>

        </div>
    <?php } ?>


        <div class="box-body" style="display: block;width: 100%">
            <div id="MPR_view<?php echo $userDashboardID ?>"></div>
        </div>
        <div class="box-header with-border">
        </div>

        <div class="box-body" style="display: block;width: 100%">
            <div class="row" style="margin: 0 auto;">
                <div class="form-group col-sm-10">
                    <h5 class="box-title"><strong style="font-family: Tahoma;font-size: 14px;">Action Tracker</strong></h5>
                </div>
                <div class="form-group col-sm-2 pull-right">
                    <button type="button" class="btn btn-mini btn-primary float-shadow"
                            onclick="add_action_tracker_View()" style="position: absolute;">
                        <i class="fa fa-plus">Add Action Tracker</i>
                    </button>
                </div>
            </div>
            <br>

            <div id="MPR_Actiontracker_view<?php echo $userDashboardID ?>"></div>
        </div>
    </div>


    <div class="overlay" id="overlay20<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
<?php } else { ?>
    <br>
    <br>
    <div class="row" style="margin: 0 auto; border: 0px solid">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                Template Not Configured
            </div>
        </div>
    </div>
<?php } ?>
    </div>


    <div aria-hidden="true" role="dialog" id="add_action_tracker_add_model" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width: 95%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Add Action Tracker</h5>
                </div>
                <form role="form" id="action_tracker_add_form" class="form-horizontal">
                    <input type="hidden" name="companyType" id="companyType" value="<?php echo $companyType; ?>">
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-condesed" id="item_add_table">
                            <thead>
                            <tr>
                                <th style="width: 200px;">Company</th>
                                <th style="width: 200px;">Segment</th>
                                <th style="width: 200px;">Add Description</th>
                                <th style="width: 200px;">Target Date</th>
                                <th style="width: 200px;">Employee</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>

                                    <?php if ($companyType == 1) { ?>
                                        <?php echo form_dropdown('selectedcomapnyID', $load_company, current_companyID(), 'class="form-control select2" id="selectedcomapnyID" disabled'); ?>
                                    <?php } else { ?>
                                        <?php echo form_dropdown('selectedcomapnyID', $load_company, '', 'class="form-control select2" id="selectedcomapnyID" onchange="select_company_wise_segment(this.value),select_company_wise_employees(this.value);"'); ?>
                                    <?php } ?>

                                </td>
                                <td>
                                    <?php if ($companyType == 1) { ?>
                                        <?php echo form_dropdown('segmentID', $segment, '', 'class="form-control select2" id="segmentID" '); ?>
                                    <?php } else { ?>
                                        <div id="div_loadcompanywisesegment">
                                            <?php echo form_dropdown('segmentID', $segment, ' ', 'class="form-control select2" id="segmentID" '); ?>
                                        </div>
                                    <?php } ?>

                                </td>

                                <td>
                                    <textarea class="form-control" rows="2" name="adddescription" id="adddescription"
                                              placeholder="Description..."></textarea>
                                </td>
                                <td>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="targetdate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="targetdate"
                                               class="form-control" required>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($companyType == 1) { ?>
                                        <?php echo form_dropdown('employeeID', all_employees_drop(), '', 'class="form-control select2" id="employeeID" '); ?>
                                    <?php } else { ?>
                                        <div id="div_loadcompanywiseemployee">
                                            <?php echo form_dropdown('employeeID', $employee, ' ', 'class="form-control select2" id="employeeID" '); ?>
                                        </div>
                                    <?php } ?>

                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="text-right m-t-xs">
                                <button class="btn btn-primary next" type="button" onclick="save_action_tracker();">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="companywiseView">

                    </div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button><!--Close-->
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
         id="action_tracker_view_model">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">MPR</h4>
            </div>

            <div class="modal-body">
                <div class="span6 well well-small" style="min-height: 274px;">
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Description</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="actiondescription">......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Company </label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="CompanyID">......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Segment </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="Segment" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Person Responsible </label>
                        </div>
                        <div class="form-group col-sm-8">
                          <p id="personresponsible" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Created Date </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="createddate" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Target Date </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="targetDate" >......</p>
                        </div>
                    </div>
                    <div class="row hide statuscomcls">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Date</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="completiondate_status">......</p>
                        </div>
                    </div>

                    <div class="row hide statuscomclscomment">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Comment</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="Comment_status">......</p>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button><!--Close-->
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
         id="action_tracker_view_model_edit">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Action Tracker</h4>
                </div>
                    <?php echo form_open('', 'role="form" id="edit_action_tracker"'); ?>
                    <input type="hidden" id="actiontrackerdetailID" name="actiontrackerdetailID"/>
                    <input type="hidden" id="companytypeedit" name="companytypeedit" value="<?php echo $companyType?>"/>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Company</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php if ($companyType == 1) { ?>
                                <?php echo form_dropdown('selectedcomapnyID_edit', $load_company, current_companyID(), 'class="form-control select2" id="selectedcomapnyID_edit" disabled'); ?>
                            <?php } else { ?>
                                <?php echo form_dropdown('selectedcomapnyID_edit', $load_company, '', 'class="form-control select2" id="selectedcomapnyID_edit" onchange="select_company_wise_segment_edit(this.value),select_company_wise_employees_edit(this.value);"'); ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Segment</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php if ($companyType == 1) { ?>
                                <?php echo form_dropdown('segmentID_edit', $segment, '', 'class="form-control select2" id="segmentID_edit" '); ?>
                            <?php } else { ?>
                                <div id="div_loadcompanywisesegment_edit">
                                    <?php echo form_dropdown('segmentID_edit', $segment, ' ', 'class="form-control select2" id="segmentID_edit" '); ?>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Action Description</label>
                        </div>
                        <div class="form-group col-sm-6">
                         <textarea class="form-control" rows="2" name="actiondescriptionedit" id="actiondescriptionedit"
                                   placeholder="Description..."></textarea>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Employee</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php if ($companyType == 1) { ?>
                                <?php echo form_dropdown('employeeID_edit', all_employees_drop(), '', 'class="form-control select2" id="employeeID_edit" '); ?>
                            <?php } else { ?>
                                <div id="div_loadcompanywiseemployee_edit">
                                    <?php echo form_dropdown('employeeID_edit', $employee, ' ', 'class="form-control select2" id="employeeID_edit" '); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary next" type="button" onclick="update_action_tracker();">
                        Save
                    </button>
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button><!--Close-->
                </div>
                </form>
            </div>
        </div>
    </div>

    <form id="print_form" method="post" action="" target="_blank">
        <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
        <input type="hidden" id="company_master_ID" name="CompanyID">
    </form>
    <script>
        load_mpr_view<?php echo $userDashboardID ?>();
        MPRActiontrackerview<?php echo $userDashboardID ?>();
        $(document).ready(function (e) {
            $('.select2').select2();
            $('#companygroupfilter').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#companygroupfilter").multiselect2('selectAll', false);
            $("#companygroupfilter").multiselect2('updateButtonText');

        });

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractDate');
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractExpDate');
        });

        function load_mpr_view<?php echo $userDashboardID ?>() {
            var periods_mpr = $('#finance_periods_mpr<?php echo $userDashboardID ?>').val();
            var mpr_template = $('#mpr_template<?php echo $userDashboardID ?>').val();
            var companygroupfilter = $('.companygroupfilter').val();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Finance_dashboard/load_mpr_view'); ?>",
                data: {
                    'periods_mpr': periods_mpr,
                    'mpr_template': mpr_template,
                    'userDashboardID':<?php echo $userDashboardID ?>,
                    'companygroupfilter': companygroupfilter
                },
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if (data[0] == 's') {
                        $("#MPR_view<?php echo $userDashboardID ?>").html(data['view']);
                        MPRActiontrackerview<?php echo $userDashboardID ?>();
                        $('#btn-excel-mpr-<?php echo $userDashboardID ?>').attr('download', data['file_name']);
                    } else {
                        myAlert(data[0], data[1]);
                    }
                },
                error: function () {
                    stopLoad();
                }
            });
        }


        function add_action_tracker_View() {
            var companyType = $('#companyType').val();
            $('#action_tracker_add_form')[0].reset();
            if (companyType != 1)
            {
                $('#selectedcomapnyID').val('').trigger('change');
            }

            $('#adddescription').val('');
            $('#segmentID').val('').trigger('change');
            $('#employeeID').val('').trigger('change');
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_action_tracker'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('.companywiseView').html(data);
                        $("#add_action_tracker_add_model").modal({backdrop: "static"});

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function select_company_wise_segment(companyID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyID: companyID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#div_loadcompanywisesegment').html(data);
                    $('.select2').select2();


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }




        function select_company_wise_employees(companyID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyID: companyID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_employee'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#div_loadcompanywiseemployee').html(data);
                    $('.select2').select2();


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function save_action_tracker() {
            $('#selectedcomapnyID').prop('disabled', false);
             var periods_mpr = $('#finance_periods_mpr<?php echo $userDashboardID ?>').val()
            var companyType = $('#companyType').val();
            var data = $('#action_tracker_add_form').serializeArray();
            data.push({name: 'periods_mpr', value: periods_mpr});
            data.push({name: 'companyType', value: companyType});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Finance_dashboard/save_add_action_tracker'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if (companyType == 1) {
                            $('#selectedcomapnyID').prop('disabled', true);

                        }

                        $('#segmentID').val('').trigger('change');
                        $('#employeeID').val('').trigger('change');
                        $('#adddescription').val('');

                    } else {
                        if (companyType == 1) {
                            $('#selectedcomapnyID').prop('disabled', true);
                        }
                    }
                    view_action_tracker_view();
                    MPRActiontrackerview<?php echo $userDashboardID ?>();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function view_action_tracker_view() {
            var companyID = $('#selectedcomapnyID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyID: companyID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_action_tracker'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('.companywiseView').html(data);
                    $('.select2').select2();


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function MPRActiontrackerview<?php echo $userDashboardID ?>() {
            var periods_mpr = $('#finance_periods_mpr<?php echo $userDashboardID ?>').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data : {'mprID':periods_mpr},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_master_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $("#MPR_Actiontracker_view<?php echo $userDashboardID ?>").html(data);
                    $('.select2').select2();


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function view_mpr_view(ID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data : {actionID:ID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_action_tracker_view_model'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#actiondescription').html(data['description']);
                    $('#CompanyID').html(data['companycode']);
                    $('#Segment').html(data['segment']);
                    $('#personresponsible').html(data['AssignedEmp']);
                    $('#createddate').html(data['createddate']);
                    $('#targetDate').html(data['targetDate']);
                    $('#completiondate').html(data['completedDate']);
                    if (data['status'] == 0) {
                        $('.inprogressstatus').addClass('hide');
                        $('.closedstatus').addClass('hide');
                        $('.openstatus').removeClass('hide');
                        $('.completedstatus').addClass('hide');
                        $('.statuscomcls').addClass('hide');
                        $('.statuscomclscomment').addClass('hide');

                    } else if (data['status'] == 1) {
                        $('.inprogressstatus').removeClass('hide');
                        $('.closedstatus').addClass('hide');
                        $('.openstatus').addClass('hide');
                        $('.completedstatus').addClass('hide');
                        $('.statuscomcls').addClass('hide');
                        $('.statuscomclscomment').addClass('hide');
                    } else if (data['status'] == 2) {
                        $('.inprogressstatus').addClass('hide');
                        $('.closedstatus').addClass('hide');
                        $('.openstatus').addClass('hide');
                        $('.completedstatus').removeClass('hide');
                        $('.statuscomcls').removeClass('hide');
                        $('.statuscomclscomment').removeClass('hide');

                        $('#completiondate_status').html(data['completedDate']);
                        $('#Comment_status').html(data['completoncomment']);


                    } else if (data['status'] == 3)
                    {
                        $('.inprogressstatus').addClass('hide');
                        $('.closedstatus').removeClass('hide');
                        $('.openstatus').addClass('hide');
                        $('.completedstatus').addClass('hide');
                        $('.statuscomcls').removeClass('hide');
                        $('.statuscomclscomment').removeClass('hide');
                        $('#completiondate_status').html(data['approvedDate']);
                        $('#Comment_status').html(data['approvecom']);

                    }

                    $("#action_tracker_view_model").modal({backdrop: "static"});

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }
    function edit_action_tracker(ID) {
        $('#edit_action_tracker')[0].reset();
        $('#actiontrackerdetailID').val(ID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {actionID:ID},
            url: "<?php echo site_url('Finance_dashboard/fetch_company_action_tracker_view_model'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#selectedcomapnyID_edit').val(data['assignedCompanyID']);
                $('#actiondescriptionedit').val(data['description']);

                if(data['companyType']==1)
                {
                    setTimeout(function () {
                        select_company_wise_employees_edit(data['assignedCompanyID'],data['responsibleEmpID']);
                        select_company_wise_segment_edit(data['assignedCompanyID'], data['assignedSegmentID'],data['segment']);
                    }, 300);

                }else
                {
                    setTimeout(function () {
                        select_company_wise_employees_edit(data['assignedCompanyID'],data['responsibleEmpID']);
                        select_company_wise_segment_edit(data['assignedCompanyID'], data['assignedSegmentID'],data['segment']);
                    }, 300);

                }


                $("#action_tracker_view_model_edit").modal({backdrop: "static"});

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });




    }
        function update_action_tracker() {
            $('#selectedcomapnyID_edit').prop('disabled', false);
            var periods_mpr = $('#finance_periods_mpr<?php echo $userDashboardID ?>').val()

            var mpr_template = $('#mpr_template<?php echo $userDashboardID ?>').val();
            var companyType = $('#companytypeedit').val();
            var data = $('#edit_action_tracker').serializeArray();
            data.push({name: 'periods_mpr', value: periods_mpr});
            data.push({name: 'companyType', value: companyType});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Finance_dashboard/update_add_action_tracker'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if (companyType == 1) {
                            $('#selectedcomapnyID_edit').prop('disabled', true);
                        }
                        $("#action_tracker_view_model_edit").modal('hide');
                    } else {
                        if (companyType == 1) {
                            $('#selectedcomapnyID_edit').prop('disabled', true);
                        }
                    }
                    MPRActiontrackerview<?php echo $userDashboardID ?>();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function update_close_status(ID) {

            swal({
                    title: "Are You Sure",
                    text: "You want to Close this record",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "cancel"
                },
                function () {
                    $.ajax({
                        url: "<?php echo site_url('Finance_dashboard/update_close_status'); ?>",
                        type: 'post',
                        data : {actionID:ID},

                        dataType: 'json',
                        cache: false,

                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                MPRActiontrackerview<?php echo $userDashboardID ?>();
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            stopLoad();
                            myAlert('e', xhr.responseText);
                        }
                    });
                });
        }
        function select_company_wise_employees_edit(companyID,empID = null) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyID: companyID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_employee_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#div_loadcompanywiseemployee_edit').html(data);
                    $('#employeeID_edit').val(empID).change();
                    $('.select2').select2();


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function select_company_wise_segment_edit(companyID,segmentID = null,segmentcode = null) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyID: companyID},
                url: "<?php echo site_url('Finance_dashboard/fetch_company_wise_segment_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#div_loadcompanywisesegment_edit').html(data);
                    $('.select2').select2();

                    setTimeout(function () {
                        //$('#segmentID_edit').val(segmentID + ' | ' + segmentcode).change();
                        $('#segmentID_edit').val(segmentID).change();
                    }, 300);


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    </script>


<?php
