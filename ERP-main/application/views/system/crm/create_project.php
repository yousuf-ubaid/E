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
$category_arr = all_projects_category();
$valutype_arr = all_crm_valueType();
$employees_arr = all_crm_employees_drop();
$piplinename_arr = all_crm_project_pipelines();
$organization_arr = load_all_organizations(false);
$currency_arr = crm_all_currency_new_drop();
$status_arr = all_project_status();
$employees_multiple_arr = fetch_employees_by_company_multiple(false);
$groupmaster_arr = all_crm_groupMaster();
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$current_userid = current_userID();
$related_to = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '6' => $this->lang->line('common_contact')/*'Contact'*/,'4' => $this->lang->line('crm_opportunity'), '8' => $this->lang->line('crm_organizations')/*'Organizations'*/,'0'=>$this->lang->line('common_not_applicable'));
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
            <h2><?php echo $this->lang->line('crm_project_details');?> </h2><!--PROJECT DETAILS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_project_name');?> </label><!--Project Name-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="opportunityname"
                                                                                     id="opportunityname"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('crm_project_name');?>"
                                   ><span
                                       class="input-req-inner"></span></span><!--Project Name-->
                <input type="hidden" name="projectID" id="projectID_edit">
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
                                                                         required></textarea><span
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
                                  title="Required Field"><?php echo form_dropdown('projectStatus', $status_arr, '', 'class="form-control" onchange="statuscheack(this.value)" id="projectStatus" '); ?>
                                <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-3 hide">
                <?php echo form_dropdown('convertProject', array('0' => 'Select Close Status', '2' => 'Closed', '2' => 'Convert to Project'), '', 'class="form-control" id="convertProject"'); ?>
            </div>
        </div>
        <div class="row cancelDatedatehideshow hide" style="z-index: 100">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Cancel Date</label><!--Due Date-->
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                             <div class="input-group dateDatepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="cancelDate"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="cancelDate"
                                        class="form-control" required>
                             </div>
                             <span class="input-req-inner" style="z-index: 100;"></span></span>
            </div>
        </div>
        <div class="row cancelDatedatehideshow hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_reson');?> </label><!--Reason-->
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="reasoncancel" id="reasoncancel" class="form-control" placeholder="<?php echo $this->lang->line('crm_reson');?>"><!--Reason-->
                                <span class="input-req-inner"></span></span>
            </div>
        </div>

        <div class="row closedatehideshow hide" style="z-index: 100">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Close Date</label><!--Due Date-->
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                             <div class="input-group dateDatepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="closedate"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="closedate"
                                        class="form-control" required>
                             </div>
                             <span class="input-req-inner" style="z-index: 100;"></span></span>
            </div>
        </div>

        <div class="row closedatehideshow hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_reson');?> </label><!--Reason-->
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="reason" id="reason" class="form-control" placeholder="<?php echo $this->lang->line('crm_reson');?>"><!--Reason-->
                                <span class="input-req-inner"></span></span>
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
                <?php echo form_dropdown('categoryID', $category_arr, '', 'class="form-control" id="categoryID"'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row hide" style="margin-top: 10px;">
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
                <label class="title"><?php echo $this->lang->line('common_start_date');?> </label><!--Start Date-->
            </div>
            <div class="form-group col-sm-4">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="projectStartDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="projectStartDate" class="form-control">
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_end_date');?> </label><!--End Date-->
            </div>
            <div class="form-group col-sm-4">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="projectEndDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="projectEndDate" class="form-control">
                </div>
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
                <span class="input-req" title="Required Field"><?php echo form_dropdown('responsiblePersonEmpID', $employees_arr, $CI->session->userdata("empID"), 'class="form-control select2" id="responsiblePersonEmpID"  required'); ?>
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
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID"'); ?><span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="price" id="price" class="form-control number" placeholder="<?php echo $this->lang->line('crm_bid_amount');?>"><span class="input-req-inner"></span></span><!--Bid Amount-->
                    </div>
                    <div class="form-group col-sm-3 hide">
                        <?php echo form_dropdown('valueType', $valutype_arr, '1', 'class="form-control" id="valueType" onchange="fetch_valueType()"'); ?>
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1">
                        <label class="title"><?php echo $this->lang->line('crm_for');?> </label><!--For-->
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1" style="padding-left: 0px;padding-right: 0px;">
                        <input type="text" name="duration" id="duration" class="form-control number" placeholder="">
                    </div>
                    <div class="form-group notfixedbid hide col-sm-1">
                        <label class="title" id="durationlabel"><?php echo $this->lang->line('crm_for');?> </label><!--For-->
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
            <h2><?php echo $this->lang->line('crm_pipline_capital');?> </h2><!--PIPELINE-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_pipline_name');?> </label><!--Pipeline Name-->
            </div>
            <div class="form-group col-sm-3">
                <?php /*echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); */?>
                <?php echo form_dropdown('pipelineID', array('' => 'Select Pipeline'), '', 'class="form-control select2" id="pipelineID" onchange="load_sub_cat()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <select name="pipelineStageID" id="pipelineStageID" class="form-control"
                        onchange="opportunity_pipeline()">
                    <option value=""><?php echo $this->lang->line('crm_select_stage');?></option><!--Select Stage-->
                </select>
            </div>

            <div class="col-md-4 pipelinebtn">
                <button type="button" onclick="pipelinemodel()" name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('crm_add_pipeline');?><!-- Add Pipeline--></button>
            </div>

        </div>
        <br>

        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="col-sm-11" style="padding-left: 0px;">
                <div id="opportunityPipeline"></div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <div class="row">
                <div class="col-sm-1">
                    &nbsp;
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-2 hide stagenamepipeline">
                    <div style="font-weight: 500;font-size: 16px;color: slategrey;" id="statusstages">.....</div></div>
                <br>
                <div class="col-md-6 text-right hide stagenamepipeline">
                    <input type="hidden" name="taskdetailpieplineid" id="taskdetailpieplineid">
                    <button type="button" class="btn btn-primary pull-right btntaskclick">
                        <i class="fa fa-plus"></i> Task
                    </button>
                </div>
                <br>
                <br>
                <div class="col-sm-12">
                    <div class="piplineview"
                         id="taskMaster_view"></div>
                </div>
                <div class="col-sm-1">
                    &nbsp;
                </div>
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
        <!--<div class="row" id="linkmorerelation">
            <div class="form-group col-sm-9">
                <button type="button" class="btn btn-primary btn-xs pull-right " onclick="add_more()"><i
                        class="fa fa-plus"></i></button>
            </div>
            <div class="form-group col-sm-1">

            </div>
        </div>-->
        <div class="row">
            <div id="append_related_data">
                <div class="append_data">
                    <div class="row">
                        <div class="form-group col-sm-2" style="margin-top: 10px;">
                            <label class="title"></label>
                        </div>
                        <div class="form-group col-sm-3">
                             <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('relatedTo[]', $related_to, '', 'class="form-control relatedTo" id="relatedTo_1" onchange="relatedChange(this)"'); ?>
                                 <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-3" style="padding-left: 0px;">
                            <span class="input-req" title="Required Field">
                            <input type="text" class="form-control f_search" name="related_search[]" id="f_search_1"
                                   placeholder="<?php echo $this->lang->line('crm_contact_organization');?>"><!--Contact, Organization...-->
                            <input type="hidden" class="form-control relatedAutoID" name="relatedAutoID[]"
                                   id="relatedAutoID_1">
                            <input type="hidden" class="form-control linkedFromOrigin" name="linkedFromOrigin[]"
                                   id="linkedFromOrigin_1">
                                <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2 remove-td hide" style="margin-top: 10px;">
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
                        <label style="font-weight: 400"> <?php echo $this->lang->line('crm_select_a_group');?> </label><!--Select a Group-->
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
                        <label style="font-weight: 400"> <?php echo $this->lang->line('crm_select_multiple_pepole');?></label><!--Select Multiple People-->
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
                    <button class="btn btn-primary sbmtbtn" type="submit"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="pipelicemodal">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 130%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <div class="modal-body">
                <section class="past-posts">
                    <div class="posts-holder settings">
                        <div class="past-info">
                            <div id="toolbar">
                                <div class="toolbar-title">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $this->lang->line('crm_pipelines');?>
                                </div><!--Pipelines-->
                                <div class="btn-toolbar btn-toolbar-small pull-right">

                                </div>
                            </div>
                            <div class="post-area">
                                <article class="page-content">

                                    <div class="system-settings">
                                        <form id="form_pipeline">
                                            <table id="fetchpipeline" class="table ">
                                                <thead>
                                                <tr>
                                                    <th>#</th>

                                                    <th><?php echo $this->lang->line('crm_pipline_name');?> </th><!--Pipeline Name-->
                                                    <th><?php echo $this->lang->line('crm_for_opportunities');?></th><!--For Opportunities-->
                                                    <th> <?php echo $this->lang->line('crm_for_projects');?> </th><!--For Projects-->
                                                    <th><?php echo $this->lang->line('crm_for_lead');?>  </th><!--For Lead-->
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>

                                                <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="">
                                                        <input class="text" id="pipeLineName" name="pipeLineName"
                                                               placeholder="<?php echo $this->lang->line('crm_pipline_name');?>" type="text" value=""></td><!--Pipeline Name-->
                                                    <td style="text-align: center"><input name="opportunityYN" type="hidden" value="0"><input class="" id="opportunityYN" name="opportunityYN" type="checkbox" value="1"> </td>
                                                    <td style="text-align: center"><input name="projectYN" type="hidden" value="0"> <input class="" id="projectYN" name="projectYN" type="checkbox" value="1">
                                                    </td>
                                                    <td style="text-align: center"><input name="leadYN" type="hidden" value="0"> <input class="" id="leadYN" name="leadYN" type="checkbox" value="1">
                                                    </td>
                                                    <td colspan=""><a onclick="submitPipeline();" id="AddNewPipeline" class="btn btn-primary btn-xs"><?php echo $this->lang->line('crm_add_pipeline');?></a></td><!--Add Pipeline-->
                                                </tr>

                                                </tfoot>

                                            </table>
                                        </form>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>
                </form>
            </div>
        </div>
    </div>
</div>





<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="pipelicemodalview">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 130%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <div class="modal-body">
                <section class="past-posts">
                    <div class="posts-holder settings">
                        <div class="past-info">
                            <div id="toolbar">
                                <div class="toolbar-title">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $this->lang->line('crm_pipelines');?>
                                </div><!--Pipelines-->
                                <div class="btn-toolbar btn-toolbar-small pull-right">

                                </div>
                            </div>


                            <div class="post-area">
                                <article class="page-content">

                                    <div class="system-settings">

                                        <div class="further-link">
                                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i><strong id="pipelinename">....</strong></a>
                                        </div>


                                        <div id="settingsContainer">
                                            <!-- Old site solution -->
                                            <div id="div_load_pipeline">
                                                <!--<ul id="" class="pipeline bottom20" style="100%">
                                                    <li style="width: 10%"><a href="#" title="Prospecting"> Prospecting</a></li>
                                                    <li style="width: 10%"><a href="#" title="Qualification"> Qualification</a></li>
                                                    <li style="width: 50%"><a href="#" title="Needs Analysis"> Needs Analysis</a>
                                                    </li>
                                                    <li style="width: 10%"><a href="#" title="Proposal"> Proposal</a></li>
                                                    <li style="width: 10%"><a href="#" title="Negotiation"> Negotiation</a></li>
                                                    <li style="width: 10%"><a href="#" title="planning"> planning</a></li>
                                                </ul>-->
                                            </div>
                                            <br>

                                            <form id="form_pipelineStage">
                                                <table id="fetchpipelinestages" class="table ">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>

                                                        <th><?php echo $this->lang->line('crm_pipeline_stage_name');?> </th><!--Pipeline Stage Name-->
                                                        <th><?php echo $this->lang->line('crm_probability');?> </th><!--Probability-->

                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>

                                                    <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="">
                                                            <input type="hidden" id="masterID" name="masterID">
                                                            <input class="text" id="stageName" name="stageName"
                                                                   placeholder="<?php echo $this->lang->line('crm_pipeline_stage_name');?>" type="text" value=""></td><!--Pipeline Stage Name-->
                                                        <td style=""><input style="width: 120px" max="100" min="0" class="text"
                                                                            id="probability" name="probability"
                                                                            placeholder="<?php echo $this->lang->line('crm_probability');?> %" type="number" value=""><!--Probability-->
                                                        </td>


                                                        <td colspan=""><a onclick="submitPipelinestaged();" id="AddNewPipeline"
                                                                          class="btn btn-primary btn-xs"><?php echo $this->lang->line('crm_add_stage');?> </a></td><!--Add Stage-->
                                                    </tr>

                                                    </tfoot>

                                                </table>
                                            </form>


                                        </div>


                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">

    var search_id = 1;


    $(document).ready(function () {
        fetch_pipelicename();
        $('.headerclose').click(function () {
            fetchPage('system/crm/projects_management', '', 'Projects');
        });

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
            $('#opportunity_form').bootstrapValidator('revalidateField', 'projectStartDate');
            $('#opportunity_form').bootstrapValidator('revalidateField', 'projectEndDate');
        });
        $('.dateDatepic').datetimepicker({
            showTodayButton: true,
            format: date_format_policy,
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {

        });

        $('.select2').select2();

        search_id = 1;

        projectID = null;
         pieplineid = null;

        number_validation();

        initializeTaskTypeahead

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            projectID = p_id;
            load_project_header();
            $('#opportunityname').attr('Readonly', true);
            //$('#categoryID').attr('disabled', true);
            $('#transactionCurrencyID').attr('disabled', true);
            $('#price').attr('disabled', true);
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
                opportunityname: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_project_name_is_required');?>.'}}},/*Project Name is required*/
                categoryID: {categoryID: {notEmpty: {message: '<?php echo $this->lang->line('common_category_is_required');?>.'}}},/*Category is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                responsiblePersonEmpID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_user_responsible_is_required');?>.'}}},/*User Responsible is required*/
                //projectStatus: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_value_is_required');?>.'}}},/*Value is required*/
                price: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_price_is_required');?>.'}}},/*Price is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            //$("#categoryID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#price").prop("disabled", false);
            $(".relatedTo").prop("disabled", false);
            $(".relatedAutoID").prop("disabled", false);
            $(".linkedFromOrigin").prop("disabled", false);
            $(".f_search").prop("disabled", false);
            $('#responsiblePersonEmpID').prop("disabled",false);
            $('#pipelineID').prop("disabled",false);
            $('#isPermissionEveryone').iCheck('Enable');
            $('#isPermissionCreator').iCheck('Enable');
            $('#isPermissionGroup').iCheck('Enable');
            $('#isPermissionMultiple').iCheck('Enable');
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/save_project_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        employeeassigntype(data[1]);
                        fetchPage('system/crm/projects_management', '', 'Projects');
                    } else if(data[0] == 'e'){

                    }else {
                        $('.btn-primary').prop('disabled', false);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#price").prop("disabled", true);
                        $(".relatedTo").prop("disabled", true);
                        $(".relatedAutoID").prop("disabled", true);
                        $(".linkedFromOrigin").prop("disabled", true);
                        $(".f_search").prop("disabled", true);
                        $('#responsiblePersonEmpID').prop("disabled",true);
                        $('#pipelineID').prop("disabled",true);
                        $('#isPermissionEveryone').iCheck('disable');
                        $('#isPermissionCreator').iCheck('disable');
                        $('#isPermissionGroup').iCheck('disable');
                        $('#isPermissionMultiple').iCheck('disable');
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        if(('<?php echo $admin['isSuperAdmin'] ?? 0 ?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0 ?>' != 1))
        {
            $('#isPermissionEveryone').iCheck('check');

            $("#isPermissionEveryone").on("ifChanged", function () {
                //$("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
               // $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionCreator").on("ifChanged", function () {
               // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
               // $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionGroup").on("ifChanged", function () {
               // $("#employeesID").val(null).trigger("change");
                $("#show_groupPermission").removeClass('hide');
                $("#show_multiplePermission").addClass('hide');
            });

            $("#isPermissionMultiple").on("ifChanged", function () {
              //  $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
                $("#show_multiplePermission").removeClass('hide');
            });
        }else
        {
            $('#isPermissionEveryone').iCheck('check');
            $("#isPermissionEveryone").on("ifChanged", function () {
                // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });
            $("#isPermissionCreator").on("ifChanged", function () {

                // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionGroup").on("ifChanged", function () {

                //$("#employeesID").val(null).trigger("change");
                $("#show_groupPermission").removeClass('hide');
                $("#show_multiplePermission").addClass('hide');
            });

            $("#isPermissionMultiple").on("ifChanged", function () {
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
                $("#show_multiplePermission").removeClass('hide');
            });
        }

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
            data: {'MasterAutoID': projectID},
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
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_project_header() {
        if (projectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'projectID': projectID},
                url: "<?php echo site_url('CrmLead/load_project_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#projectID_edit').val(projectID);
                        $('#opportunityname').val(data['header']['projectName']);
                        $('#description').val(data['header']['description']);
                        $('#projectStatus').val(data['header']['projectStatus']);
                        $('#convertProject').val(data['header']['closeStatus']);
                        $('#reason').val(data['header']['reason']);
                        $('#responsiblePersonEmpID').val(data['header']['responsibleEmpID']).change();
                        $('#categoryID').val(data['header']['categoryID']);
                        $('#transactionCurrencyID').val(data['header']['transactionCurrencyID']).change();
                        $('#valueType').val(data['header']['valueType']);
                        $('#price').val(data['header']['transactionAmount']);
                        $('#duration').val(data['header']['valueAmount']);
                        //$('#forcastCloseDate').val(data['header']['forcastCloseDate']);
                        $('#projectStartDate').val(data['header']['projectStartDate']);
                        $('#projectEndDate').val(data['header']['projectEndDate']);
                        $('#probabilityofwinning').val(data['header']['probabilityofwinning']);
                        $('#pipelineID').val(data['header']['pipelineID']).change();
                        load_sub_cat(data['header']['pipelineStageID']);
                        if (data['header']['pipelineID'] != null) {
                            opportunity_pipeline();
                        }
                        if (data['header']['isClosed'] == 1) {
                            $('#isClosed').iCheck('check');
                            $('#closedDate').val(data['header']['closedDates']);
                            //$('.sbmtbtn').attr('disabled',true)
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
                            $("#relatedTo_" + id).prop("disabled", "disabled");
                            $("#f_search_" + id).prop("disabled", "disabled");
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

                    if((data['header']['responsibleEmpID'] == '<?php echo $current_userid?>') && ('<?php echo $admin['isSuperAdmin'] ?? 0?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0 ?>' != 1) && (data['header']['createdUserID'] != '<?php echo $current_userid?>'))
                    {
                        $('#responsiblePersonEmpID').prop("disabled", "disabled");
                        $('#pipelineID').prop("disabled", "disabled");
                        $(".pipelinebtn").addClass('hide');
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").addClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").addClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    setTimeout(function () {
                                        $('#groupID').val(value.permissionValue);
                                    }, 600)

                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                }
                            });
                        }

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
        if (projectID) {
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
            data: {pipelineID: pipelineID, pipelineStageID: pipelineStageID,projectID:projectID},
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

    function employeeassigntype(projectID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectID': projectID},
            url: "<?php echo site_url('CrmLead/load_project_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                    if((data['header']['responsibleEmpID'] == '<?php echo $current_userid?>') && ('<?php echo $admin['isSuperAdmin'] ?? 0 ?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0 ?>' != 1) && (data['header']['createdUserID'] != '<?php echo $current_userid?>'))
                    {
                        $('#responsiblePersonEmpID').prop("disabled", "disabled");
                        $('#pipelineStageID').prop("disabled", "disabled");
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    setTimeout(function () {
                                        $('#groupID').val(value.permissionValue);
                                    }, 600)
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").addClass('hide');
                                    $("#show_multiplePermission").removeClass('hide');
                                }
                            });
                        }
                    }
                    else
                    {
                        $('#responsiblePersonEmpID').prop("disabled",false);
                        $('#pipelineStageID').prop("disabled",false);

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
                                    $('#multipleemployeesID').val(selectedItems).change();
                                }
                            });
                        }
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
    function pipelinemodel()
    {
        fetch_pipeline();
       $('#pipelicemodal').modal('show');
    }

    function fetch_pipeline() {
        var Otable = $('#fetchpipeline').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_pipeline_project_dd'); ?>",
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

                {"width": "10%", "targets": 5}
            ],
            "aoColumns": [
                {"mData": "pipeLineID"},
                {"mData": "pipeLineName"},
                {"mData": "opportunityYN"},
                {"mData": "projectYN"},
                {"mData": "leadYN"},
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
    function submitPipeline() {

        var data = $('#form_pipeline').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('crm/save_pipleline'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                $('#pipeLineName').val('');
                $('#opportunityYN').attr('checked', false);
                $('#projectYN').attr('checked', false);
                $('#leadYN').attr('checked', false);
                fetch_pipeline();
                fetch_pipelicename();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function piepelineview(pipelinedetail,pipeLineID,pipeLineName)
    {
        $('#masterID').val(pipeLineID);
        $('#pipelinename').html(pipeLineName);
        fetch_pipelinestage();
        loadpipeline();
        $('#pipelicemodalview').modal('show');
    }
    function fetch_pipelinestage() {
        var masterid =  $('#masterID').val();
        var Otable = $('#fetchpipelinestages').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_pipeline_stage'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                /*  if (oSettings.bSorted || oSettings.bFiltered) {
                 for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                 $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                 }
                 }*/
            },
            "columnDefs": [

                {"width": "2%", "targets": 3}

            ],
            "aoColumns": [
                {"mData": "sortOrder"},
                {"mData": "stageName"},
                {"mData": "probability"},
                {"mData": "edit"}

            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "masterID", "value": masterid});
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
    function loadpipeline() {
        var masterid =  $('#masterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {masterID: masterid},
            url: "<?php echo site_url('crm/loadpipeline'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_pipeline').html(data);
                /*  $('.pipeline li:last-child a').css('background-image', 'none');*/
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function submitPipelinestaged() {

        var data = $('#form_pipelineStage').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('crm/save_piplelineStage'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#stageName').val('');
                    $('#probability').val('');
                }
                loadpipeline();
                fetch_pipelinestage();
                load_sub_cat();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function edit_pipeline(pipeLineDetailID) {
        $('.updatediv').addClass('hide');
        $('.canceldiv').removeClass('hide');
        $('.showinput').removeClass('hide');
        $('.hideinput').addClass('hide');
        $('.xxx_' + pipeLineDetailID).removeClass('hide');
        $('.xx_' + pipeLineDetailID).addClass('hide');
        $('#editpipeline_' + pipeLineDetailID).addClass('hide');
        $('#updatepipeline_' + pipeLineDetailID).removeClass('hide');

    }
    function pipelinestage_cancel(pipeLineDetailID) {

        $('.updatediv').addClass('hide');
        $('.canceldiv').removeClass('hide');
        $('.showinput').removeClass('hide');
        $('.hideinput').addClass('hide');
    }
    function pipelinestage_update(pipeLineDetailID) {
        var masterid =  $('#masterID').val();
        sortOrderID = $('#order_' + pipeLineDetailID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                masterID:masterid,
                pipeLineDetailID: pipeLineDetailID,
                stageName: $('#stagename_' + pipeLineDetailID).val(),
                probability: $('#percentage_' + pipeLineDetailID).val(),
                sortOrder: sortOrderID,
            },
            url: "<?php echo site_url('crm/save_piplelineStage'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#stagename_' + pipeLineDetailID).val('');
                    $('#percentage_' + pipeLineDetailID).val('');
                }
                loadpipeline();
                fetch_pipelinestage();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    function delete_pipeline(pipeLineDetailID) {
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
                    data: {'pipeLineDetailID': pipeLineDetailID},
                    url: "<?php echo site_url('Crm/delete_pipelineDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert('s', 'Deleted Successfully');
                        fetch_pipelinestage();
                        loadpipeline();

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function fetch_pipelicename() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Crm/fetch_pipelicename'); ?>",
            success: function (data) {
                $('#pipelineID').empty();
                var mySelect = $('#pipelineID');
                mySelect.append($('<option></option>').val('').html('Select Pipeline'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['pipeLineID']).html(text['pipeLineName']));
                    });
                    /*if (select_value) {
                        $("#pipelineID").val(select_value);
                    }*/
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function checkCurrentTabprojecttask(opporunityID,pipeLineDetailID,stageName) {
        $('.tapPipeLine').removeClass('active');
        $('#taskdetailpieplineid').val(pipeLineDetailID);
        $('.stagenamepipeline').removeClass('hide');
        $('#stageID_' + pipeLineDetailID).addClass('active');
        $('#statusstages').html(stageName);
        getTaskManagement_tableView(opporunityID, pipeLineDetailID)
    }

    function getTaskManagement_tableView(opporunityID, pipeLineDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opporunityID: opporunityID, pipeLineDetailID: pipeLineDetailID,type:1},
            url: "<?php echo site_url('crm/load_taskManagement_project_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taskMaster_view').html(data);
                $(".taskHeading_tr").hide();
                $(".taskaction_td").hide();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $(".btntaskclick").click(function(){
        var taskdetailpieplineid =   $('#taskdetailpieplineid').val();

        fetchPage('system/crm/create_new_task','','Create Task',9,[projectID,taskdetailpieplineid]);
    });

    function statuscheack(statusid)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid},
            url: "<?php echo site_url('Crm/crm_projects'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['isexist'] == 1)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to close this task!",
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
                                $('.cancelDatedatehideshow').addClass('hide');
                            } else {
                                $("#projectStatus").val('');
                                $('.closedatehideshow').addClass('hide');
                                $('.cancelDatedatehideshow').addClass('hide');
                            }
                        });
                }else if(data['isexist'] == 3)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to cancel this Project!",
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
                                $('.cancelDatedatehideshow').removeClass('hide');
                                $('.closedatehideshow').addClass('hide');
                            } else {
                                $("#projectStatus").val('');
                                $('.cancelDatedatehideshow').addClass('hide');
                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }


                else
                {
                    $('.closedatehideshow').addClass('hide');
                    $('.cancelDatedatehideshow').addClass('hide');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


</script>