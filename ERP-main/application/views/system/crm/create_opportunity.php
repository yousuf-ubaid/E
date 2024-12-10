<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$CI =& get_instance();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$countries_arr = load_all_countrys();
$category_arr = all_Opportunities_category();
$opportunitytype = opportunitytype();
$valutype_arr = all_crm_valueType();
$employees_arr = all_crm_employees_drop();
$piplinename_arr = all_crm_pipelines();
$organization_arr = load_all_organizations(false);
$currency_arr = crm_all_currency_new_drop();
$status_arr = all_opportunities_status();
$employees_multiple_arr = fetch_employees_by_company_multiple(false);
$groupmaster_arr = all_crm_groupMaster();
$reason_arr = all_crm_critirias();
$reason_arr['-1'] = ('Other');

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    span.input-req-inner {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
    }

    span.input-req-inner:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }

    span.input-req-inner:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .progress-bar {
        border-right: 1px solid white;
    }

</style>

<?php echo form_open('', 'role="form" id="opportunity_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_opportunity_details');?> </h2><!--OPPORTUNITY DETAILS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_opportunity_name');?> </label><!--Opportunity Name-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="opportunityname"
                                                                                     id="opportunityname"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('crm_opportunity_name');?>"
                                                                                     > <span class="input-req-inner"></span></span><!--Opportunity Name-->
                <input type="hidden" name="opportunityID" id="opportunityID_edit">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_description');?> </label><!--Description-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea name="description" id="description"
                                                                         class="form-control" rows="3"
                                                                         ></textarea><span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_status');?> </label><!--Status-->
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req"
                                  title="Required Field"><?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control" id="statusID" onchange="statuscheack(this.value)"'); ?>
                                <span class="input-req-inner"></span></span>
            </div>
       
        </div>
        <div class="row closedatehideshow hide" style="z-index: 100">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Closed Date</label><!--Due Date-->
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                             <div class="input-group dateDatepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="closedate"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="closedate"
                                        class="form-control">
                             </div>
                             <span class="input-req-inner" style="z-index: 100;"></span></span>
            </div>
        </div>
        <div class="row closedatehideshow hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Criteria</label><!--Reason-->
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('reason', $reason_arr, '', 'class="form-control select2" onchange="reasoncheack(this.value)" id="reason"'); ?>
                                <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row showotherreson hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Remarks</label><!--Opportunity Name-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="otherreson"
                                                                                     id="otherreson"
                                                                                     class="form-control"
                                                                                     placeholder="Reason"
                                   > <span class="input-req-inner"></span></span><!--Opportunity Name-->

            </div>
        </div>


    </div>
</div>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_additional_information');?> </h2><!--ADDITIONAL INFORMATION-->
        </header>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_category');?> </label><!--Category-->

            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('categoryID', $category_arr, '', 'class="form-control select2" id="categoryID" '); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_probability_of_winning');?> </label><!--Probability Of Winning-->
            </div>
            <div class="form-group col-sm-2">
                <input type="number" name="probabilityofwinning" id="probabilityofwinning" class="form-control"
                       placeholder="EX : 50 %" min="0" max="100" autocomplete="off">
            </div>
            <div class="form-group col-sm-1">
                <label class="title" style="margin-left: -106%;">%</label>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_forcast_close_date');?></label><!--Forecast Close Date-->
            </div>
            <div class="form-group col-sm-4">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="forcastCloseDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="forcastCloseDate" class="form-control">
                </div>

            </div>
            <div class="form-group col-sm-1">
                <label class="title">Type</label>
            </div>
            <div class="form-group col-sm-2">
                <?php echo form_dropdown('opportunitytype', $opportunitytype, '', 'class="form-control select2" id="opportunitytype" '); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_user_responsible');?> </label><!--User Responsible-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req"
                      title="Required Field"><?php echo form_dropdown('responsiblePersonEmpID', $employees_arr, $CI->session->userdata("empID"), 'class="form-control select2" id="responsiblePersonEmpID"  '); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_value');?> </label><!--Value-->
            </div>
            <div class="form-group col-sm-7">
                <div class="row">
                    <div class="form-group col-sm-7">
                          <span class="input-req" title="Required Field">
                              <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" '); ?>
                        <span class="input-req-inner"></span></span>
                    </div>
                   <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <input type="text" value="0" name="price" id="price" class="form-control" placeholder="<?php echo $this->lang->line('crm_bid_amount');?>"><!--Bid Amount-->
                    <span class="input-req-inner"></span></span>
                    </div>
                    
                    <div class="form-group col-sm-3 hide">
                        <?php echo form_dropdown('valueType', $valutype_arr, '1', 'class="form-control" id="valueType" onchange="fetch_valueType()"'); ?>
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1">
                        <label class="title"><?php echo $this->lang->line('crm_for');?></label><!--For-->
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1" style="padding-left: 0px;padding-right: 0px;">
                        <input type="text" name="duration" id="duration" class="form-control number" placeholder="">
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1">
                        <label class="title" id="durationlabel"><?php echo $this->lang->line('crm_for');?></label><!--For-->
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2">
                &nbsp
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_pipline_capital');?></h2><!--PIPELINE-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_pipline_name');?> </label><!--Pipeline Name-->
            </div>
            <div class="form-group col-sm-3">
                <?php echo form_dropdown('pipelineID', $piplinename_arr, '', 'class="form-control" id="pipelineID" onchange="load_sub_cat()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <select name="pipelineStageID" id="pipelineStageID" class="form-control"
                        onchange="opportunity_pipeline(),load_pipeline_percentage(this.value);">
                    <option value=""><?php echo $this->lang->line('crm_select_stage');?></option><!--Select Stage-->
                </select>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="col-sm-10" style="padding-left: 0px;">
                <div id="opportunityPipeline"></div>
            </div>
            <div class="form-group col-sm-1">
                &nbsp
            </div>
        </div>

    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_related_to');?> </h2><!--RELATED TO-->
        </header>
        <div class="row" id="linkmorerelation">
            <div class="form-group col-sm-9">
                <button type="button" class="btn btn-primary btn-xs pull-right" onclick="add_more()"><i
                        class="fa fa-plus"></i></button>
            </div>
            <div class="form-group col-sm-1">

            </div>
        </div>
        <div class="row">
            <div id="append_related_data">
                <div class="append_data">
                    <div class="row">
                        <div class="form-group col-sm-2" style="margin-top: 10px;">
                            <label class="title"></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('relatedTo[]', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '6' => $this->lang->line('common_contact')/*'Contact'*/, '8' => $this->lang->line('crm_organizations')/*'Organizations'*/), '', 'class="form-control relatedTo" id="relatedTo_1" onchange="relatedChange(this)"'); ?>
                                <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-3" style="padding-left: 0px;">
                            <span class="input-req" title="Required Field">
                            <input type="text" class="form-control f_search" name="related_search[]" id="f_search_1"
                                   placeholder="<?php echo $this->lang->line('common_contact');?>,<?php echo $this->lang->line('crm_organization');?> .."><!--Contact--><!--Organization-->
                            <input type="hidden" class="form-control relatedAutoID" name="relatedAutoID[]"
                                   id="relatedAutoID_1">
                            <input type="hidden" class="form-control linkedFromOrigin" name="linkedFromOrigin[]"
                                   id="linkedFromOrigin_1">
                                <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2 remove-td" style="margin-top: 10px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_permissions');?> </h2><!--PERMISSIONS-->
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_visibility');?> </label><!--Visibility-->
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input id="isPermissionEveryone" type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          name="userPermission"
                                                                          value="1"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400"><?php echo $this->lang->line('crm_everyone');?> </label><!--Everyone-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionCreator"
                                                                          type="radio" data-caption=""
                                                                          class="columnSelected" value="2"><label
                                    for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400"> <?php echo $this->lang->line('crm_only_for_me');?></label><!--Only For Me-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionGroup"
                                                                          type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          onclick="leadPermission(3)"
                                                                          value="3"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_a_group');?> </label><!--Select a Group-->
                    </div>
                </div>
                <div class="row hide" id="show_groupPermission">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-4" style="margin-left: 2%;">
                        <?php echo form_dropdown('groupID', $groupmaster_arr, '', 'class="form-control" id="groupID"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="iradio_square-blue">
                            <div class="skin-section extraColumns"><input name="userPermission"
                                                                          id="isPermissionMultiple"
                                                                          type="radio"
                                                                          data-caption="" class="columnSelected"
                                                                          onclick="leadPermission(4)"
                                                                          value="4"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_multiple_pepole');?> </label><!--Select Multiple People-->
                    </div>
                </div>
                <div class="row hide" id="show_multiplePermission">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"></label>
                    </div>
                    <div class="form-group col-sm-4" style="margin-left: 2%;">
                        <?php echo form_dropdown('employees[]', $employees_multiple_arr, '', 'class="form-control select2" id="employeesID"  multiple="" style="z-index: 0;"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="text-right m-t-xs">
                <div class="form-group col-sm-10" style="margin-top: 10px;">
                    <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">

    var search_id = 1;

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/crm/opportunities_management', '', 'Lead');
        });

        $('.select2').select2();

        search_id = 1;

        opportunityID = null;

        number_validation();

        initializeTaskTypeahead

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {

        });

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            opportunityID = p_id;
            load_opportunity_header();
        } else {

        }

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });


        $('#opportunity_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                opportunityname: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_opportunity_name_is_required');?>.'}}},/*Opportunity Name is required*/
                categoryID: {categoryID: {notEmpty: {message: '<?php echo $this->lang->line('crm_category_is_required');?>.'}}},/*Category is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                responsiblePersonEmpID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_user_responsible_is_required');?>.'}}},/*User Responsible is required*/
                statusID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
               // reason: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_reson_is_required');?>.'}}}/*Reason is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'opportunityTypedescription', 'value': $('#opportunitytype option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/save_opportunity_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/crm/opportunities_management', '', 'Opportunities');
                    }  else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#isPermissionEveryone').iCheck('check');

        $("#isPermissionEveryone").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionCreator").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionGroup").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_groupPermission").removeClass('hide');
            $("#show_multiplePermission").addClass('hide');
        });

        $("#isPermissionMultiple").on("ifChanged", function () {
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
            $("#show_multiplePermission").removeClass('hide');
        });
    });

    function initializeTaskTypeahead(id) {
        var relatedType = $('#relatedTo_' + id).val();
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Crm/fetch_document_relate_search/?&t=' + relatedType,
            onSelect: function (suggestion) {
                $('#relatedAutoID_' + id).val(suggestion.DoucumentAutoID);
            }
        });
    }

    function relatedChange(elemant) {
        initializeTaskTypeahead(search_id);
        $('#f_search_' + search_id).val('');
    }


    function fetch_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': opportunityID},
            url: "<?php echo site_url('Crm/fetch_opportunity_employee_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                }
                else {
                    $.each(data, function (key, value) {
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + '</td><td>' + value['Ename2'] + '</td><td class="text-right"><a onclick="delete_opportunity_detail(' + value['AssingeeID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                        x++;
                    });
                    $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_opportunity_header() {
        if (opportunityID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'opportunityID': opportunityID},
                url: "<?php echo site_url('CrmLead/load_opportunity_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#opportunityID_edit').val(opportunityID);
                        $('#opportunityname').val(data['header']['opportunityName']);
                        $('#description').val(data['header']['description']);
                        $('#statusID').val(data['header']['statusID']);
                        $('#convertProject').val(data['header']['closeStatus']);
                        $('#reason').val(data['header']['reason']);
                        $('#responsiblePersonEmpID').val(data['header']['responsibleEmpID']).change();
                        $('#categoryID').val(data['header']['categoryID']).change();
                        $('#transactionCurrencyID').val(data['header']['transactionCurrencyID']).change();
                        $('#valueType').val(data['header']['valueType']);
                        $('#price').val(data['header']['transactionAmount']);
                        $('#duration').val(data['header']['valueAmount']);
                        $('#forcastCloseDate').val(data['header']['forcastCloseDate']);

                        $('#probabilityofwinning').val(data['header']['probabilityofwinning']);
                        $('#pipelineID').val(data['header']['pipelineID']);
                        $('#opportunitytype').val(data['header']['type']).change();
                        load_sub_cat(data['header']['pipelineStageID']);
                        $('#probabilityofwinning').prop('readonly',false);
                        if (data['header']['pipelineID'] != null) {
                            $('#probabilityofwinning').prop('readonly',true);
                            opportunity_pipeline();
                        }
                        fetch_valueType();
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        $.each(data['detail'], function (key, value) {
                            if (key > 0) {
                                add_more();
                            }

                        });
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var id = 1;
                        $.each(data['detail'], function (key, value) {
                            $('#relatedTo_' + id).val(value.relatedDocumentID);
                            $('#relatedAutoID_' + id).val(value.relatedDocumentMasterID);
                            $('#f_search_' + id).val(value.searchValue);
                            $('#linkedFromOrigin_' + id).val(value.originFrom);
                            if (value.originFrom == 1) {
                                $("#relatedTo_" + id).prop("disabled", "disabled");
                                $("#f_search_" + id).prop("disabled", "disabled");
                                $("#linkmorerelation").addClass("hide");
                            } else {
                                $("#linkmorerelation").removeClass("hide");
                            }
                            id++;
                        });
                    }
                    if (!jQuery.isEmptyObject(data['permission'])) {
                        var selectedItems = [];
                        $.each(data['permission'], function (key, value) {
                            if (value.permissionID == 1) {
                                $('#isPermissionEveryone').iCheck('check');
                            } else if (value.permissionID == 2) {
                                $('#isPermissionCreator').iCheck('check');
                            } else if (value.permissionID == 3) {
                                $('#isPermissionGroup').iCheck('check');
                                $('#groupID').val(value.permissionValue);
                            } else if (value.permissionID == 4) {
                                $('#isPermissionMultiple').iCheck('check');
                                selectedItems.push(value.empID);
                                $('#employeesID').val(selectedItems).change();
                            }
                        });
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function delete_opportunity_detail(id) {
        if (opportunityID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('crm_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
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
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('CrmLead/delete_opportunity_master'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function linkOrganization() {
        $('#organization').val('');
        $('#linkorganization_text').removeClass('hide');
        $('#organization_text').addClass('hide');
    }

    function unlinkOrganization() {
        $('#linkorganization_text').addClass('hide');
        $('#organization_text').removeClass('hide');
    }

    function linkContact() {
        $('#linkcontact_text').removeClass('hide');
        $('#contact_text').addClass('hide');
    }

    function unlinkContact() {
        $('#contactID').val('');
        $('.valcontact').val('');
        $("#countryID").val(null).trigger("change");
        $(".calcontactread").prop("readonly", false);
        $('#linkcontact_text').addClass('hide');
        $('#contact_text').removeClass('hide');
    }

    function initializeContactTypeahead() {
        $('#contactname').autocomplete({
            serviceUrl: '<?php echo site_url();?>Crm/fetch_contact_relate_search/',
            onSelect: function (suggestion) {
                $('#contactID').val(suggestion.contactID);
                $('#firstName').val(suggestion.firstName);
                $('#lastName').val(suggestion.lastName);
                $('#title').val(suggestion.title);
                $('#phoneMobile').val(suggestion.phoneMobile);
                $('#email').val(suggestion.email);
                $('#phoneHome').val(suggestion.phoneHome);
                $('#fax').val(suggestion.fax);
                $('#postalcode').val(suggestion.postalCode);
                $('#state').val(suggestion.state);
                $('#city').val(suggestion.city);
                $('#address').val(suggestion.address);
                $('#address').val(suggestion.organizationname);
                $('#countryID').val(suggestion.countryID).change();
                $("#firstName").prop("readonly", true);
                $("#lastName").prop("readonly", true);
                $("#title").prop("readonly", true);
            }
        });
    }

    function fetch_valueType() {
        var valueType = $('#valueType').val();
        if (valueType == 2) {
            $(".notfixedbid").removeClass('hide');
            $("#durationlabel").html('Hours');
        } else if (valueType == 3) {
            $(".notfixedbid").removeClass('hide');
            $("#durationlabel").html('Days');
        } else if (valueType == 4) {
            $(".notfixedbid").removeClass('hide');
            $("#durationlabel").html('Weeks');
        } else if (valueType == 5) {
            $(".notfixedbid").removeClass('hide');
            $("#durationlabel").html('Month');
        } else if (valueType == 6) {
            $(".notfixedbid").removeClass('hide');
            $("#durationlabel").html('Years');
        } else {
            $(".notfixedbid").addClass('hide');
        }

    }

    function add_more() {
        search_id += 1;
        var appendData = $('.append_data:first').clone();
        appendData.find('input').val('');
        appendData.find('#f_search_' + search_id).val('');
        appendData.find('.relatedTo').attr('id', 'relatedTo_' + search_id);
        appendData.find('.relatedAutoID').attr('id', 'relatedAutoID_' + search_id);
        appendData.find('.linkedFromOrigin').attr('id', 'linkedFromOrigin_' + search_id);
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#append_related_data').append(appendData);
        initializeTaskTypeahead(search_id);
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('.append_data').remove();
    });

    function load_sub_cat(select_val) {
        $('#pipelineStageID').val("");
        $('#pipelineStageID option').remove();
        $('#opportunityPipeline').html('');
       $('#probabilityofwinning').prop('readonly', false);
        var subid = $('#pipelineID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("CrmLead/load_pipelineSubStage"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#pipelineStageID').empty();
                    var mySelect = $('#pipelineStageID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['pipeLineDetailID']).html(text['stageName']));
                    });
                    if (select_val) {
                        $("#pipelineStageID").val(select_val);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function opportunity_pipeline() {
        var pipelineID = $('#pipelineID').val();
        var pipelineStageID = $('#pipelineStageID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {pipelineID: pipelineID, pipelineStageID: pipelineStageID},
            url: "<?php echo site_url('crm/show_opportunity_pipeline'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#opportunityPipeline').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function statuscheack(statusid)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid},
            url: "<?php echo site_url('CrmLead/crm_opportunity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['isexist'] == 1)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to close this Opportunity!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }else if(data['isexist'] == 3)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to change the status as lost!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }

                else if(data['isexist'] == 2)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to Covert this Opportunity to Project!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }

                else
                {
                    $('.closedatehideshow').addClass('hide');
                    $('.showotherreson').addClass('hide');
                     $("#reason").val(null).trigger("change");
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function reasoncheack(val) {
        if(val == -1)
        {
            $('.showotherreson').removeClass('hide');
        }else
        {
            $('.showotherreson').addClass('hide');
        }
    }
    function load_pipeline_percentage(id) {
        var pipelineID = $('#pipelineID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'pipelinestagedetailid': id,'pipelineID':pipelineID},
            url: "<?php echo site_url('Crm/pipeline_probabilitiy_cal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(id)
                {
                    $('#probabilityofwinning').prop('readonly', true);
                    $('#probabilityofwinning').val(data['probability']);

                }else
                {

                    $('#probabilityofwinning').val('');
                    $('#probabilityofwinning').prop('readonly', false);
                }

                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


</script>