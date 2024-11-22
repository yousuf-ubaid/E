<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('project_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_common_project');
echo head_page($title, false);
/*echo head_page('Project', FALSE);*/
$current_date = format_date(date('Y-m-d'));
$customer_arr = all_customer_drop();
$currencyCoversion_arr = all_currency_drop(TRUE, 'ID');
$currency_arr = all_currency_new_drop();
$service_line_arr = fetch_segment(TRUE);
$category_arr = get_category();
$unit_array = load_unit_drop();
$companyname = current_companyName();
$project = get_all_boq_project();
$subcategory_arr = array('' => 'Select  Sub Category');
$date_format_policy = date_format_policy();

?>

<link href=" <?php echo base_url('plugins/jsGantt/jsgantt.css'); ?>" rel="stylesheet" type="text/css"/>
<script src=" <?php echo base_url('plugins/jsGantt/jsgantt.js'); ?>" type="text/javascript"></script>
<style>
    .custometbl .form-control {

        height: 20px;
        vertical-align: middle;
        padding: 0px;
    }

    .custometbl > thead > tr > th, .custometbl > tbody > tr > th, .custometbl > tfoot > tr > th, .custometbl > thead > tr > td, .custometbl > tbody > tr > td, .custometbl > tfoot > tr > td {
        padding: 0px;
        line-height: 1;
        padding: 5px;

    }

    .gtaskname div, .gtaskname {

        font-size: 10px;
        margin: 5px;

    }

    .gtaskcelldiv {
        font-size: 10px;
        margin: 5px;
    }

    td.gmajorheading div {
        margin: 5px;
        font-size: 10px;
    }

    .gresource, .gduration, .gpccomplete, .gstartdate div, .gstartdate {

        font-size: 10px;
    }

    .genddate div, .genddate {

        font-size: 10px;
    }

    .gpccomplete div {
        font-size: 10px;
    }

    .gduration div {
        font-size: 10px;
    }

    .gresource div {
        font-size: 10px;
    }


</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_one'); ?><!--Step 1-->
        - <?php echo $this->lang->line('promana_pm_header'); ?> <!--Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_two'); ?><!--Step 2-->
        - <?php echo $this->lang->line('promana_pm_cost'); ?> <!--Cost--> </a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="show_summary_data()" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_three'); ?><!--Step 3-->
        - <?php echo $this->lang->line('promana_pm_cost_reveiew'); ?> <!--Cost Review--></a>
    <a class="btn btn-default btn-wizard" href="#step4" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_four'); ?><!--Step 4--> -
        <?php echo $this->lang->line('promana_pm_cost_project_planning'); ?><!--Project Planning--> </a>
    <a class="btn btn-default btn-wizard" onclick="getchart()" href="#step5" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_five'); ?><!--Step 5 -->
        - <?php echo $this->lang->line('promana_pm_cost_project_planning_review'); ?>
        <!--Project Planning Review--> </a>
</div>
<hr>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('login/loginSubmit', 'role="form" id="boq_create_form"') ?>
        <div class="row">
            <input type="hidden" name="headerID" id="headerID" value="<?php echo $_POST['page_id'] ?>">
            <!--    <div class="form-group col-sm-4">
                <label for="company">Company</label>
                <input type="text" name="companyID" readonly id="companyID" class="form-control"
                       value="<?php /*echo $companyname */ ?>">
            </div>-->

            <div class="form-grou col-sm-4">
                <label for="" class=" control-label">
                    <?php echo $this->lang->line('common_project'); ?><!--Project--></label>

                <?php echo form_dropdown('projectID', $project, '',
                    'class="form-control searchbox" onchange="get_project(this.value)" id="projectID"  required'); ?>

            </div>

            <div class="form-group col-sm-4">
                <label for="servicelinecode">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segement', $service_line_arr, '',
                    'class="form-control searchbox" id="segement" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="servicelinecode">Project Name<?php required_mark(); ?></label>
                <input type="text" class="form-control" id="projectname" name="projectname">
            </div>


        </div>
        <div class="row" id="">
            <div class="form-group col-sm-4">
                <label for="location">
                    <?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customer', $customer_arr, '',
                    '  class="form-control searchbox" id="customer" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label>
                    <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?><!--Project Start Date--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="prjStartDate" value="" id="prjStartDate"
                           class="form-control dateFields"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label>
                    <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?><!--Project End Date--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                    <input type="text" name="prjEndDate" value="" id="prjEndDate"
                           class="form-control dateFields"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                </div>
            </div>


            <!--
            -->


            <!-- <div class="form-group col-sm-4">
                    <label for="priority">Currency Conversion <?php /*required_mark(); */ ?></label>
                    <?php /* echo form_dropdown('currncyconversion', $currencyCoversion_arr, '','class="form-control" id="currncyconversion" required'); */ ?>

                </div>-->
            <!--<div class="form-group col-sm-4">
                    <label for="priority">Reporting Currency <?php /*required_mark(); */ ?></label>
                    <input type="text" name="reportingCurrency"  id="reportingCurrency" readonly class="form-control" >
                </div>-->

        </div>
        <div class="row">

            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--> </label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                    <input type="text" name="documentdate" value="" id="documentdate"
                           class="form-control dateFields"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                </div>
            </div>


            <div class="form-group col-sm-4">
                <label>
                    <?php echo $this->lang->line('common_currency'); ?><!--Currency--><?php required_mark(); ?></label>
                <?php echo form_dropdown('currency', $currency_arr, '',
                    ' class="form-control searchbox" id="currency" required'); ?>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="comments"><?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>
                    <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="form-group col-sm-4">
                <label>
                    Retention Percentage (%)</label>
                <input  autocomplete="off" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="retentionpercentage"  class="form-control" id="retentionpercentage">
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary saveandnext" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row" style="margin: 0px">
            <div class="col-sm-12">
                <h5 style="text-align: center">
                    <div id="pcode"></div>
                </h5>
            </div>
            <div class="col-sm-12" style="padding: 5px;margin-bottom: 4px;
    background-color: rgba(175, 213, 175, 0.27);">
                <table width="100%" class="">

                    <tr>
                        <td><b><?php echo $this->lang->line('common_project'); ?><!--Project-->:</b></td>
                        <td>
                            <div id="pcompany">
                        </td>
                        <td><b><?php echo $this->lang->line('common_segment'); ?><!--Segment-->:</b></td>
                        <td>
                            <div id="psegement">
                        </td>
                        <td><b><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name-->:</b></td>
                        <td>
                            <div id="pcustomer">
                        </td>
                    </tr>

                    <tr>

                        <td><b>
                                <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?><!--Project Start Date-->
                                :</b></td>
                        <td>
                            <div id="pstartdate"></div>
                        </td>
                        <td><b>
                                <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?><!--Project End Date-->
                                :</b></td>
                        <td>
                            <div id="penddate"></div>
                        </td>

                        <td><b><?php echo $this->lang->line('common_document_date'); ?><!--Document Date-->:</b></td>
                        <td>
                            <div id="pdocumentdate"></div>
                        </td>

                    </tr>

                    <tr>
                        <td><b>
                                <?php echo $this->lang->line('promana_pm_cost_customer_currency'); ?><!--Customer Currency-->
                                :</b></td>
                        <td>
                            <div id="pcurrency"></div>
                        </td>

                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>


                </table>
            </div>


        </div>

        <div class="row editview">
            <div class="col-sm-12">
                <a onclick="modalheaderdetails()" class="btn btn-primary btn-xs pull-right">
                    <?php echo $this->lang->line('common_create_new'); ?><!--Create New--></a>
            </div>

        </div>
        <br>


        <div class="row" style="margin: 0px;">
            <div class="">
                <div id="loadheaderdetail">
                    <table id="loadcosttable" class="<?php echo table_class() ?> custometbl">
                        <thead>
                        <tr>
                            <th rowspan="3"><?php echo $this->lang->line('common_category'); ?><!--Category--></th>
                            <th rowspan="3">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th rowspan="3"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>

                            <th rowspan="2" colspan="3">
                                <?php echo $this->lang->line('promana_common_selling_price'); ?><!--Selling Price--></th>
                            <th rowspan="3" width="70px">
                                <?php echo $this->lang->line('promana_pm_markup'); ?><!--Markup--> %
                            </th>
                            <th colspan="4"><?php echo $this->lang->line('common_cost'); ?><!--Cost--></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <?php echo $this->lang->line('promana_pm_material_cost'); ?><!--Material Cost--></th>
                            <th rowspan="2">
                                <?php echo $this->lang->line('promana_pm_total_labour_cost'); ?><!--Total Labour Cost--></th>
                            <th rowspan="2">
                                <?php echo $this->lang->line('promana_pm_total_cost'); ?><!--Total Cost--></th>
                            <th rowspan="2"></th>
                        </tr>

                        <tr>
                            <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th><?php echo $this->lang->line('promana_common_unit_rate'); ?><!--Unit Rate--></th>
                            <th><?php echo $this->lang->line('common_total_value'); ?><!--Total Value--></th>
                            <th><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <th><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>


            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">
            <a class="btn btn-default prev" onclick="">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></a>
            <a class="btn btn-primary " onclick="show_summary_data();">
                <?php echo $this->lang->line('common_next'); ?><!--Next--></a>
        </div>
    </div>

    <div id="step3" class="tab-pane ">
        <div class="row" style="margin: 0px;">
            <div class="col-md-12">
                <!--<h5>
                    <div id="p2code">df</div>
                </h5>-->
                <h5 style="text-align: center"><span id="p2code"></span></h5>

                <!--<span class="pull-right"><a onclick="printpage();" target="_blank"><span
                                class="glyphicon glyphicon-print"></span></a></span>-->

                <div class="pull-right">

                    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportPdf()">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        <?php echo $this->lang->line('promana_common_pdf_for_client'); ?><!--PDF for client-->
                    </button>


                    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="project.xls"
                       onclick="var file = tableToExcel('tablesheet', 'project'); $(this).attr('href', file);">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        <?php echo $this->lang->line('promana_common_pdf_for_excel'); ?><!-- Excel-->
                    </a>

                </div>

            </div>
            <div id="tablesheet">
                <div class="col-md-12" style="padding: 5px;margin-bottom: 4px;
    background-color: rgba(175, 213, 175, 0.27);">
                    <table id="" width="100%" class="">

                        <tr>
                            <td><b><?php echo $this->lang->line('promana_common_project'); ?><!--Project-->:</b></td>
                            <td>
                                <div id="p2company"></div>
                            </td>
                            <td><b><?php echo $this->lang->line('common_segment'); ?><!--Segment-->:</b></td>
                            <td>
                                <div id="p2segement"></div>
                            </td>
                            <td><b><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name-->:</b>
                            </td>
                            <td>
                                <div id="p2customer"></div>
                            </td>
                        </tr>

                        <tr>

                            <td><b>
                                    <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?><!--Project Start Date-->
                                    :</b></td>
                            <td>
                                <div id="p2startdate"></div>
                            </td>
                            <td><b>
                                    <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?><!--Project End Date-->
                                    :</b></td>
                            <td>
                                <div id="p2enddate"></div>
                            </td>

                            <td><b><?php echo $this->lang->line('common_document_date'); ?><!--Document Date-->:</b>
                            </td>
                            <td>
                                <div id="p2documentdate"></div>
                            </td>

                        </tr>

                        <tr>
                            <td><b>
                                    <?php echo $this->lang->line('promana_pm_cost_customer_currency'); ?><!--Customer Currency-->
                                    :</b></td>
                            <td>
                                <div id="p2currency"></div>
                            </td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>


                    </table>
                </div>
                <div class="col-md-12" style="    padding: 0px;">


                    <div id="summaryTable">

                    </div>

                    <hr>

                </div>
            </div>
            <div class="col-md-12">
                <div class="text-right m-t-xs">
                    <a class="btn btn-default prev" onclick="">
                        <?php echo $this->lang->line('common_previous'); ?><!--Previous--></a>
                    <button class="confirmYNbtn btn btn-success submitWizard" onclick="confirm_boq()">
                        <?php echo $this->lang->line('common_confirmation'); ?><!--Confirmation-->
                    </button>
                </div>
            </div>

        </div>
    </div>
    <div id="step4" class="tab-pane ">
        <div class="row ">
            <div class="col-md-12">
                <div class="text-right m-t-xs editview">
                    <button onclick="addMasterTask()" type="button" class="btn btn-sm btn-primary">
                        <?php echo $this->lang->line('common_add'); ?><!--Add--> <span
                            class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>

                </div>
            </div>

            <div class="col-md-12">
                <div id="loadtaskData"></div>
            </div>
        </div>

    </div>
    <div id="step5" class="tab-pane ">
        <div id="gantchartview"></div>


    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>
<div style="z-index: 1000000000" aria-hidden="true" role="dialog" tabindex="-1" id="itemSeachModal"
     class="modal fade"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 id="" class="modal-title">
                    <?php echo $this->lang->line('promana_common_item_search'); ?><!--Item Search--></h5>
            </div>

            <div class="modal-body">
                keyword <input type="text" id="searchKeyword" onkeyup="searchByKeyword()"/> <span
                    id="loader_itemSearch" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
                <br>
                <br>

                <div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th><?php echo $this->lang->line('promana_common_item_code'); ?><!--Item Code--></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>

                            <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                            <th><?php echo $this->lang->line('common_cost'); ?><!--Cost--></th>

                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="itemSearchResultTblBody">

                        </tbody>
                    </table>
                </div>
            </div>


            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

            </div>
        </div>

    </div>

</div>
</div>
<div aria-hidden="true" role="dialog" id="modalheaderdetails" class="modal fade"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 id="" class="modal-title">
                    <?php echo $this->lang->line('promana_pm_create_detail_header'); ?><!--Create Detail header--></h5>
            </div>
            <form role="form" id="boq_create_detail_header" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_category'); ?><!--Category--> <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="htmlCategory">
                            <!--   <?php /*echo form_dropdown('category', $category_arr, '',
                            'onchange="getSubcategory()" class="form-control searchbox" id="categoryID" required'); */ ?>-->
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_common_sub_cat'); ?><!--Sub Category--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown('subcategory', $subcategory_arr, '',
                                'onchange="subcategorychange()" class="form-control searchbox" id="subcategoryID" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="description" id="description" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_unit'); ?><!--Unit--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <!-- --><?php /*echo form_dropdown('unitID', $unit_array, 'disabled', 'class="form-control searchbox" id="unitID" required'); */ ?>
                            <input type="text" name="unitshortcode" id="unitshortcode" readonly class="form-control">
                            <input type="hidden" name="unitID" id="unitID" readonly>

                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>


        </div>
        </form>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="modalcostsheet" class="modal fade"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 id="" class="modal-title"><?php echo $this->lang->line('common_cost'); ?><!--Cost--></h5>
            </div>

            <form role="form" id="boq_cost_form_sheet" class="form-horizontal">
                <div class="modal-body" style="overflow: auto;max-height: 400px">

                    <input type="hidden" name="categoryID" id="categoryID1">
                    <input type="hidden" name="subcategoryID" id="subcategoryID1">
                    <input type="hidden" name="customerCurrencyID" id="customerCurrencyID">
                    <input type="hidden" name="detailID" id="detailID">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_item'); ?><!--Item--></label>
                                <div class="  col-sm-9">
                                    <div class="input-group">

                                        <input readonly type="text" class="form-control col-sm-6 " name="search"
                                               id="search"
                                               placeholder="Item ID, Item Description..." required>
                                        <div onclick="itemSearchModal()" class="input-group-addon"><i
                                                class="	glyphicon glyphicon-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="uom" name="uom"
                                           placeholder="UOM">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('promana_pm_total_cost'); ?><!--Total Cost--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="totalcost1"
                                           name="totalcost"
                                           value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label"></label>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-xs btn-primary" id="submitcostsheet">
                                        <?php echo $this->lang->line('common_add'); ?><!--Add-->
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--></label>
                                <div class="col-sm-4">
                                    <input type="number" step="any" class="form-control" value="0"
                                           onchange="calculate()"
                                           id="qty1"
                                           name="qty"
                                           placeholder="Qty">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--></label>
                                <div class="col-sm-9">
                                    <input type="number" step="any" class="form-control" id="unitcost"
                                           onchange="calculate()"
                                           name="unitcost"
                                           placeholder="Unit Cost">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="currency1" name="currency"
                                           placeholder="Currency">
                                </div>
                            </div>

                        </div>
                    </div>


                    <br>

                    <div id="loadcostsheettable"></div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                </div>


        </div>
        </form>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="addMasterTask" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 id="title" class="modal-title">
                    <?php echo $this->lang->line('promana_pm_cost_project_planning'); ?><!--Project Planning--></h5>
            </div>
            <form role="form" id="formProjectPlanning" class="form-horizontal">
                <input type="hidden" id="projectPlannningID" name="projectPlannningID" value="0">
                <div class="modal-body">
                    <div class="form-group">
                        <label for=""
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="">
                            <input type="text" name="description" id="planningdescription" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_note'); ?><!--Note--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="note" id="planningnote" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Project Category</label>
                        <div class="col-sm-8" id="load_project_category">
                            <?php // echo form_dropdown('project_category', load_all_project_categories(), '', 'class="form-control select2" id="project_category" '); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_pm_assign_employee'); ?><!--Assign Employee--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <?php
                            $empArr = array();
                            $emp = all_employee_drop_with_non_payroll(FALSE);
                            if (!empty($emp)) {
                                foreach ($emp as $row) {
                                    $empArr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                                }
                            }
                            ?>
                            <?php echo form_dropdown('assignedEmployee[]', $empArr, '',
                                ' class="form-control " multiple id="assignedEmployee" required'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Depended Task</label>
                        <div class="col-sm-8">
                            <div id="div_load_project">
                                <select name="relatedprojectID" class="form-control select2" id="relatedprojectID">
                                    <option value="" selected="selected">Select Project</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Relationship</label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown('relationship', project_relationship(), '', 'class="form-control select2" id="relationship" '); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for=""
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_start_date'); ?><!--Start Date--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group datepic_addtask">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                                <input type="text" name="startDate" value="" id="planningStartDate"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for=""
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_end_date'); ?><!--End Date--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">

                            <div class="input-group datepic_addtask">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                                <input type="text" name="endDate" value="" id="planningEndDate"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_common_completed'); ?><!--Completed--> %</label>
                        <div class="col-sm-8">
                            <input type="number" max="100" min="0" name="percentage" id="percentage"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_pm_color'); ?><!--Color--></label>
                        <div class="col-sm-8">
                            <select id="color" name="color" class="form-control searchbox">
                                <option value="ggroupblack">
                                    <?php echo $this->lang->line('promana_pm_black'); ?><!--Black--></option>
                                <option value="gtaskblue">
                                    <?php echo $this->lang->line('promana_pm_blue'); ?><!--Blue--></option>
                                <option value="gtaskred">
                                    <?php echo $this->lang->line('promana_pm_red'); ?><!--Red--></option>
                                <option value="gtaskpurple">
                                    <?php echo $this->lang->line('promana_pm_purple'); ?><!--Purple--></option>
                                <option value="gtaskgreen">
                                    <?php echo $this->lang->line('promana_pm_green'); ?><!--Green--></option>
                                <option value="gtaskpink">
                                    <?php echo $this->lang->line('promana_pm_pink'); ?><!--Pink--></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_sort_order'); ?><!--Sort Order--></label>
                        <div class="col-sm-8">
                            <input type="text" readonly name="sortOrder" id="planningSortOrder" class="form-control">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>


        </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    var pID = null;
    $(document).ready(function () {
        $(".select2").select2()
        $('.headerclose').click(function () {
            fetchPage('system/pm/boq', '', 'Project');
        });
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
            $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
            $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');
        });
        $('.datepic_addtask').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#formProjectPlanning').bootstrapValidator('revalidateField', 'startDate');
            $('#formProjectPlanning').bootstrapValidator('revalidateField', 'endDate');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        $("#currency").prop('disabled', false);
        $("#segement").prop('disabled', false);
        $("#customer").prop('disabled', false);
        $(".searchbox").select2();

        /*  var from = $('.from').datepicker({ autoclose: true }).on('changeDate', function(e){
         $('.to').datepicker({ autoclose: true}).datepicker('setStartDate', e.date).focus();
         });*/

        // $('#datepicker').datepicker();
        if ($('#headerID').val() != '') {
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }


        if ($('#headerID').val() != '') {
            getallsavedvalues($('#headerID').val());
            loadTaskData($('#headerID').val());
            loadheaderdetails();
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        $('#assignedEmployee').multiselect2({
            enableFiltering: true,
            /* filterBehavior: 'value',*/
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200
        });

    });

    function generateReportPdf() {
        var form = document.getElementById('boq_create_form');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_project_pdf'); ?>';
        form.submit();
    }

    function searchByKeyword(initialSearch = null) {


        /*reset Cost form */
        $("#itemSearchResultTblBody").html('');
        var keyword = (initialSearch == null) ? $("#searchKeyword").val() : '-';


        $.ajax({
            async: true,

            data: {q: keyword, currency: $('#currency').val()},
            type: 'post',
            dataType: 'json',
            url: '<?php echo site_url('Boq/item_search'); ?>',
            beforeSend: function () {
                $("#itemSearchResultTblBody").html('');

                //startLoad();
            },
            success: function (data) {

                $("#itemSearchResultTblBody").html('');
                if (data == null || data == '') {

                } else {

                    $.each(data, function (i, v) {
                        ''
                        var tr_data = '<tr><td>' + v.itemSystemCode + '</td> <td>' + v.itemDescription + '</td> <td>' + v.defaultUnitOfMeasure + '</td> <td>' + v.subCurrencyCode + '</td> <td style="text-align: right">' + parseFloat(v.cost).toFixed(2) + '</td><td><button type="button" ' + 'onclick="fetchItemRow(\'' + v.itemSystemCode + '\',\'' + v.itemDescription + '\',\'' + v.defaultUnitOfMeasure + '\',\'' + v.subCurrencyCode + '\',' + parseFloat(v.cost).toFixed(2) + ')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add--> </button></td></tr>';
                        $("#itemSearchResultTblBody").append(tr_data);
                    });
                }

            }, error: function () {

                myAlert('e', 'Error while loading')
            }
        });


    }

    function addMasterTask() {

        $('#percentage').prop('readonly', true);
        project_planningSortOrder($('#headerID').val(), 1);

        load_project_category();
        $('#addMasterTask').modal('show');
        $('#projectPlannningID').val(0);
        $('#title').html('Add Task');
        $('#formProjectPlanning')[0].reset();
        $('#formProjectPlanning').bootstrapValidator('resetForm', true);

        // loadTaskData($('#headerID').val());
    }

    function load_project_category() {
        if (pID) {
            $('#load_project_category').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'projectID': pID},
                url: "<?php echo site_url('Boq/get_project_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_project_category').html(data);
                    $('#project_category').select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function addplanningSub(projectPlannningID, title) {
        $('#percentage').prop('readonly', false);
        $('#projectPlannningID').val(projectPlannningID);
        $('#title').html(title);
        get_relatedproject($('#headerID').val());
        load_project_category();
        $('#addMasterTask').modal('show');

        project_planningSortOrder(projectPlannningID, 2);
    }
    function get_relatedproject(projectPlannningID) {

        //$('#div_load_project').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectPlannningID: projectPlannningID},
            url: "<?php echo site_url('Boq/get_project_relatedtask'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_project').html(data);
                $('#relatedprojectID').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function get_project(projectID) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    projectID: projectID

                },
                url: "<?php echo site_url('Boq/get_project'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#currency').val(data['projectCurrencyID']).change();

                    $('#prjEndDate').val(data['projectEndDate']);

                    $('#prjStartDate').val(data['projectStartDate']);

                    $('#segement').val(data['segmentID']).change();


                }, error: function () {

            }
            });
    }

    function loadTaskData(headerID) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    headerID: headerID

                },
                url: "<?php echo site_url('Boq/loadTaskData'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#loadtaskData').html(data);


                }, error: function () {

            }
            });

    }

    function project_planningSortOrder(headerID, url) {
        if (url == 1) {
            siteurl = '<?php echo site_url('Boq/project_planningSortOrder'); ?>';
        } else {
            siteurl = '<?php echo site_url('Boq/project_subplanningSortOrder'); ?>';
        }


        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    headerID: headerID

                },
                url: siteurl,
                beforeSend: function () {

                },
                success: function (data) {
                    $('#planningSortOrder').val(data['sortOrder']);


                }, error: function () {

            }
            });
    }

    $('#formProjectPlanning').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
        /* feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {


            description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
            note: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_note_is_required');?>.'}}}, /*note is required*/
            assignedEmployee: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_assign_employee_is_required');?>.'}}}, /*Assign Employee is required*/
            startDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_start_date_category_is_required');?>.'}}}, /*Start Date Cateogry is required*/
            endDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_end_date_category_is_required');?>.'}}}, /*End Date Cateogry is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'headerID', 'value': $('#headerID').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Boq/save_boq_projectPlanning'); ?>",
                beforeSend: function () {
                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {

                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#addMasterTask').modal('hide');
                    }
                    getchart();
                    loadTaskData($('#headerID').val());
                    HoldOn.close();
                    refreshNotifications(true);
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    });

    function getchart() {
        var headerID = $('#headerID').val();
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    headerID: headerID

                },
                url: "<?php echo site_url('Boq/load_gantchart'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#gantchartview').html(data);


                }, error: function () {

            }
            });

    }


    function fetchItemRow(itemSystemCode, itemDescription, defaultUnitOfMeasure, subCurrencyCode, cost) {

        $('#search').val(itemDescription + '(' + itemSystemCode + ')');
        $('#uom').val(defaultUnitOfMeasure);
        $('#unitcost').val(cost);
        $('#searchKeyword').val('');
        $('#searchKeyword').trigger('onkeyup');
        $('#itemSeachModal').modal('hide');

    }


    function itemSearchModal() {
        $('#itemSeachModal').modal('show');
        $('#searchKeyword').val('');
        $('#searchKeyword').trigger('onkeyup');
    }

    function printpage() {
        url = "Boq/printBoqPdf/" + $('#headerID').val();
        window.open(url, '_blank');
    }


    function show_summary_data() {
        $('[href=#step3]').tab('show');
        $('.btn-wizard').removeClass('disabled');
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $('[href=#step3]').removeClass('btn-default');
        $('[href=#step3]').addClass('btn-primary');
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    headerID: $('#headerID').val()

                },
                url: "<?php echo site_url('Boq/loadsummaryTable'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#summaryTable').html(data);


                }, error: function () {

            }
            });
    }


    function modalheaderdetails() {
        $('#subcategoryID').val(null).trigger("change");
        $('#categoryID').val(null).trigger("change");
        $('#unitshortcode').val('');
        $('#boq_create_detail_header')[0].reset();
        $('#boq_create_detail_header').bootstrapValidator('resetForm', true);
        $("#modalheaderdetails").modal({backdrop: "static"});

//form
        /**/
        loadCategory(pID);
    }

    function calculatetotalchangemarkup(id) {
        if ($('#markUp_' + id).val() == '') {
            $('#markUp_' + id).val(0);
        }

        markup = $('#markUp_' + id).val();
        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        qty = $('#Qty_' + id).val();
        if ($('#Qty_' + id).val() == 0) {

            unit = $('#unitCostTranCurrency_' + id).val().replace(/,/g, "");
            labour = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
            totalcost = parseFloat(unit) + parseFloat(labour);


            unitrate = ((parseFloat(totalcost)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrency_' + id).val(unitrate);
        } else {
            unitrate = ((parseFloat(totalcost) / parseFloat(qty)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrency_' + id).val(unitrate);
        }


        qty = $('#Qty_' + id).val();
        unitrate = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");

        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrency_' + id).val(totalvalue);

        savecalculatetotal(id, $('#Qty_' + id).val(), $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, ""), $('#totalTransCurrency_' + id).val().replace(/,/g, ""), $('#markUp_' + id).val(),
            $('#totalCostTranCurrency_' + id).val().replace(/,/g, ""), $('#totalLabourTranCurrency_' + id).val().replace(/,/g, ""), $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, ""));


        unitRateTransactionCurrency = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");
        unitRateTransactionCurrency = parseFloat(unitRateTransactionCurrency).toFixed(2);
        $('#unitRateTransactionCurrency_' + id).val(commaSeparateNumber(unitRateTransactionCurrency));

        totalTransCurrency = $('#totalTransCurrency_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrency_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalLabourTranCurrency = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalLabourTranCurrency = parseFloat(totalLabourTranCurrency).toFixed(2);
        $('#totalLabourTranCurrency_' + id).val(commaSeparateNumber(totalLabourTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrency_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));


    }


    function calculateonchangqty(id) {
        if ($('#Qty_' + id).val() == '') {
            $('#Qty_' + id).val(0);
        }


        qty = $('#Qty_' + id).val();
        unitrate = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");
        /*1*/
        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrency_' + id).val(totalvalue);

        qty = $('#Qty_' + id).val();
        unit = $('#unitCostTranCurrency_' + id).val().replace(/,/g, "");
        /*2*/
        total = parseFloat(qty) * parseFloat(unit);
        $('#totalCostTranCurrency_' + id).val(total);

        /*3*/


        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        ;


        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        ;
        labour = parseFloat(totalcost) - parseFloat(total);

        $('#totalLabourTranCurrency_' + id).val(labour);


        totalTransCurrency = $('#totalTransCurrency_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrency_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalCostTranCurrency = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        totalCostTranCurrency = parseFloat(totalCostTranCurrency).toFixed(2);
        $('#totalCostTranCurrency_' + id).val(commaSeparateNumber(totalCostTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrency_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));

        // savecalculatetotal(id, $('#Qty_'+ id).val(), $('#unitRateTransactionCurrency_'+id).val(), $('#totalTransCurrency_' + id).val(), $('#markUp_'+id).val(),
        //   $('#totalCostTranCurrency_' + id).val(),$('#totalLabourTranCurrency_'+id).val(),$('#totalCostAmountTranCurrency_'+id).val());

        calculatetotalchangemarkup(id);

    }

    function calculatelabourcost(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }


        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        labour = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalcost = parseFloat(total) + parseFloat(labour);
        $('#totalCostAmountTranCurrency_' + id).val(totalcost);

        calculatetotalchangemarkup(id);


    }

    $('input:text[name=totalCostAmountTranCurrency]').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    $('input:text[name=totalLabourTranCurrency]').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    function calculatetotalamount(id) {
        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }

        a = $('#totalCostAmountTranCurrency_' + id).val();

        var numericReg = /^\s*?([\d\,]+(\.\d{1,3})?|\.\d{1,3})\s*$/;
        if (numericReg.test(a)) {

        } else {

        }

        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        labour = parseFloat(totalcost) - parseFloat(total);
        $('#totalLabourTranCurrency_' + id).val(labour);

        calculatetotalchangemarkup(id);

    }

    function ccalculatetotallabour(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }


        labour = $('#totalLabourTranCurrency_' + id).val();
        tct = $('#totalCostTranCurrency_' + id).val();

        totalcost = parseFloat(tct) + parseFloat(labour);
        $('#totalCostAmountTranCurrency_' + id).val(totalcost);

        savelabourtotalcost(id, $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function ccalculatetotalamount(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }
        tam = $('#totalCostAmountTranCurrency_' + id).val();
        tct = $('#totalCostTranCurrency_' + id).val();

        lc = parseFloat(tam) - parseFloat(tct);
        $('#totalLabourTranCurrency_' + id).val(lc);

        savelabourtotalcost(id, $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function calculatetotal(id) {
        if ($('#markUp_' + id).val() == '') {
            $('#markUp_' + id).val(0);
        }
        if ($('#Qty_' + id).val() == '') {
            $('#Qty_' + id).val(0);
        }


        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }


        u = $('#unitRateTransactionCurrency_' + id).val();
        q = $('#Qty_' + id).val();
        t = parseFloat(u) * parseFloat(q);


        $('#totalTransCurrency_' + id).val(t);

        q = $('#Qty_' + id).val();
        c = $('#unitCostTranCurrency_' + id).val();
        ct = parseFloat(c) * parseFloat(q);

        $('#totalCostTranCurrency_' + id).val(ct);
        calculatetotallabour(id);
        calculatetotalamount(id);

        m = $('#markUp_' + id).val();
        c = $('#unitCostTranCurrency_' + id).val();
        lb = $('#totalCostAmountTranCurrency_' + id).val();
        q = $('#Qty_' + id).val();

        ur = ((parseFloat(lb) / parseFloat(q)) * (100 + parseFloat(m))) / 100;


        $('#unitRateTransactionCurrency_' + id).val(ur);

        savecalculatetotal(id, q, u, $('#totalTransCurrency_' + id).val(), m, $('#totalCostTranCurrency_' + id).val(), $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#unitRateTransactionCurrency_' + id).val(parseFloat($('#unitRateTransactionCurrency_' + id).val()).toFixed(2));
        $('#totalTransCurrency_' + id).val(parseFloat($('#totalTransCurrency_' + id).val()).toFixed(2));
        $('#totalCostTranCurrency_' + id).val(parseFloat($('#totalCostTranCurrency_' + id).val()).toFixed(2));
        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function savecalculatetotal(detailID, Qty, unitRateTransactionCurrency, totalTransCurrency, markUp, totalCostTranCurrency, totalLabourTranCurrency, totalCostAmountTranCurrency) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    detailID: detailID,
                    Qty: Qty,
                    unitRateTransactionCurrency: unitRateTransactionCurrency,
                    totalTransCurrency: totalTransCurrency,
                    markUp: markUp,
                    totalCostTranCurrency: totalCostTranCurrency,
                    totalLabourTranCurrency: totalLabourTranCurrency,
                    totalCostAmountTranCurrency: totalCostAmountTranCurrency
                },
                url: "<?php echo site_url('Boq/saveboqdetailscalculation'); ?>",
                beforeSend: function () {

                },
                success: function (data) {

                    refreshNotifications(true);

                }, error: function () {

            }
            });
    }

    function savelabourtotalcost(detailID, totalLabourTranCurrency, totalCostAmountTranCurrency) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    detailID: detailID,
                    totalLabourTranCurrency: totalLabourTranCurrency,
                    totalCostAmountTranCurrency: totalCostAmountTranCurrency
                },
                url: "<?php echo site_url('Boq/savelabourtotalcost'); ?>",
                beforeSend: function () {

                },
                success: function (data) {

                    refreshNotifications(true);

                }, error: function () {

            }
            });
    }


    /*    $('#prjStartDate').datepicker({
     format: 'yyyy-mm-dd',

     });
     $('#prjEndDate').datepicker({
     format: 'yyyy-mm-dd',

     });

     $('#documentdate').datepicker({
     format: 'yyyy-mm-dd'
     });*/

    /*    $('#prjEndDate,#documentdate,#prjStartDate').datepicker({
     format: 'yyyy-mm-dd'
     }).on('changeDate', function(ev){
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
     $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
     $("#prjEndDate").datepicker("option","max", ev);
     $("#prjStartDate").datepicker("option","minDate", ev);
     $(this).datepicker('hide');
     });*/


    /*    $('#prjStartDate').on('changeDate', function (ev) {
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
     $("#prjEndDate").datepicker("option", "max", ev);
     $("#prjStartDate").datepicker('hide');
     });*/

    /* $('#prjStartDate').on('changeDate', function (ev) {
     alert();
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');

     $(this).datepicker('hide');
     });*/

    /*    $('#documentdate').on('changeDate', function (ev) {

     $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');

     $(this).datepicker('hide');
     });
     $('#prjEndDate').on('changeDate', function (ev) {
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
     $("#prjStartDate").datepicker("option", "minDate", ev);
     $(this).datepicker('hide');
     });*/


    function xloadheaderdetails() {
        var Otable = $('#loadheaderdetail').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Boq/loadheaderdetailstable'); ?>",
            "bJQueryUI": true,
            "iDisplayStart ": 4,
            "sEcho": 1,
            "sAjaxDataProp": "aaData",
            "aaSorting": [[4, 'desc']],

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [

                {"mData": "categoryName"},
                {"mData": "subCategoryName"},
                {"mData": "itemDescription"},
                {"mData": "unitID"},
                {"mData": "Qty"},
                {"mData": "unitRateTransactionCurrency"},
                {"mData": "totalTransCurrency"},

                {"mData": "markUp"},
                {"mData": "cost"},
                {"mData": "totalCostTranCurrency"},
                {"mData": "action"}

            ],
            //  "columnDefs": [ { "targets": [0,1,5], "orderable": false } ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "headerID", "value": $("#headerID").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }


    function loadheaderdetails() {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    headerID: $('#headerID').val(),

                },
                url: "<?php echo site_url('Boq/loadcostheaderdetailstable'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#loadheaderdetail').html(data);


                }, error: function () {

            }
            });
    }

    function loadcostsheettable(detailID) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    detailID: detailID

                },
                url: "<?php echo site_url('Boq/loadboqcosttable'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#loadcostsheettable').html(data);


                }, error: function () {

            }
            });

    }


    function calculate() {
        if ($('#unitcost').val() != '') {

            q = $('#qty1').val();
            u = $('#unitcost').val();

            t = u * q;

            x = t.toFixed(2);


            $('#totalcost1').val(x);

        } else {
            $('#totalcost1').val(0);
        }
    }

    function modalcostsheet(categoryID, subcategoryID, customerCurrencyID, detailID) {
        $("#modalcostsheet").modal({backdrop: "static"});
        $('#categoryID1').val(categoryID);
        $('#subcategoryID1').val(subcategoryID);
        $('#customerCurrencyID').val(customerCurrencyID);
        $('#detailID').val(detailID);
        loadcostsheettable(detailID);


        $("#currency1").val($("#currency option:selected").text());
    }

    function subcategorychange() {
        //  $('#unitID').select2('val', '');
        $('#unitID').val('');
        $('#unitshortcode').val('');

        var unitshortcode = $('#subcategoryID option:selected').attr('data-title');
        var unitID = $('#subcategoryID option:selected').attr('data-val');
        $('#unitID').val(unitID);
        $('#unitshortcode').val(unitshortcode);
        /*    $('#boq_create_detail_header').bootstrapValidator('revalidateField', 'subcategoryID');*/
        //$('#boq_create_detail_header').bootstrapValidator('revalidateField', 'unitshortcode');

    }

    function getSubcategory() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getSubcategoryDropDown'); ?>",
            data: {categoryID: $('#categoryID').val()},
            dataType: "json",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                //$('#unitID').select2('val', '');
                $('#unitID').val('');
                $('#unitshortcode').val('');
                $('#subcategoryID').select2('data', null);


                $('#subcategoryID').empty();


                if (!jQuery.isEmptyObject(data)) {

                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option data-val=" " data-title=" " ></option>').val('').html('Select Subcategory'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).attr({
                            'data-val': text['unitID'],
                            'data-title': text['unitID']
                        }).html(text['description']));

                    });

                }
              //  $('#boq_create_detail_header').bootstrapValidator('revalidateField', 'unitshortcode');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });

        return false;
    }


    function setcurrencycode(code) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getReportingCurrency'); ?>",
            data: {customerCode: $('#customer').val(), currency: $('#currency').val()},
            dataType: "json",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {

                $('#currency').select2('val', '');
                $('#currency').empty();
                if (!jQuery.isEmptyObject(data)) {

                    var mySelect = $('#currency');
                    // mySelect.append($('<option data-val=" "></option>').val('').html('Select Currency Conversion'));
                    $.each(data, function (val, text) {

                        mySelect.append($('<option></option>').val(text['currencyID']).html(text['CurrencyCode']));
                        $('#currency').select2('val', code);


                    });
                }

                $('#pcurrency').html($('#currency').select2('data')[0].text);
                $('#p2currency').html($('#currency').select2('data')[0].text);
                $('#currency').select2('disable');


            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;

    }

    function get_currency_code() {
        // var option = $('option:selected', this).attr('data-val');
        currency = $('#currncyconversion option:selected').attr('data-val');
        $('#currency').html(currency);


    }


    $('#boq_create_detail_header').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        /* feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {


            description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
            unitshortcode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_common_unit_is_required');?>.'}}}, /*Unit is required*/
            category: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_category_is_required');?>.'}}}, /*Category is required*/
            subcategory: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_common_sub_category_is_required');?>.'}}}/*Sub Cateogry is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'headerID', 'value': $('#headerID').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Boq/save_boq_header_details'); ?>",
                beforeSend: function () {
                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {
                    /*$('#modalheaderdetails').modal('hide');*/
                    /*   $form.bootstrapValidator('resetForm', true);*/
                    $('#description').val('');
                    loadheaderdetails();
                    HoldOn.close();
                    refreshNotifications(true);
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    });

    $('#boq_create_form').bootstrapValidator({

        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {


            CompanyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_common_company_id_is_required');?>.'}}}, /*CompanyID is required*/
            segement: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}, /*Segment is required*/
            documentdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_document_date_line_is_required');?>.'}}}, /*Document date line is required*/
            customer: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_customer_name_is_required');?>.'}}}, /*Customer name is required*/
            currncyconversion: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}, /*Currency is required*/

            prjStartDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_project_start_date_is_required');?>.'}}}, /*Project start date is required*/
            prjEndDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_project_end_date_is_required');?>.'}}}, /*Project end date is required*/
            projectname: {validators: {notEmpty: {message: 'Project Name is required.'}}}, /*Project end date is required*/
            // comments: {validators: {notEmpty: {message: 'Comments is required.'}}}


        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'customerName', 'value': $('#customer').select2('data')[0].text});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Boq/save_boq_header'); ?>",
                beforeSend: function () {
                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {

                    if (data[0] == 's') {


                        $('.btn-wizard').removeClass('disabled');
                        $('#headerID').val(data[2]);
                        $('#pcode').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                        $('.panel-heading').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                        $('#pcompany').html($('#projectID').select2('data')[0].text);
                        $('#psegement').html($('#segement').select2('data')[0].text);
                        $('#pcustomer').html($('#customer').select2('data')[0].text);

                        $('#pdocumentdate').html($('#documentdate').val());
                        $('#penddate').html($('#prjEndDate').val());
                        $('#pstartdate').html($('#prjStartDate').val());

                        $('#p2code').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                        $('#p2company').html($('#projectID').select2('data')[0].text);
                        $('#p2segement').html($('#segement').select2('data')[0].text);
                        $('#p2customer').html($('#customer').select2('data')[0].text);

                        $('#p2documentdate').html($('#documentdate').val());
                        $('#p2enddate').html($('#prjEndDate').val());
                        $('#p2startdate').html($('#prjStartDate').val());

                        $('#pcurrency').html($('#currency').select2('data')[0].text);
                        $('#p2currency').html($('#currency').select2('data')[0].text);

                        pID = $('#projectID').val();

                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }

                    myAlert(data[0], data[1]);

                    HoldOn.close();
                    refreshNotifications(true);
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    });

    $('#boq_cost_form_sheet').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
        /*     feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {

            search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_item_is_required');?>.'}}}, /*Item is required*/
            // uom: {validators: {notEmpty: {message: 'UOM is required.'}}},
            qty: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_qty_is_required');?>.'}}}, /*Qty is required*/
            unitcost: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_unit_cost_is_required');?>.'}}}, /*Unit Cost is required*/
            // totalcost: {validators: {notEmpty: {message: 'Currncy conversion is required.'}}},

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'headerID', 'value': $('#headerID').val()});


        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Boq/save_boq_cost_sheet'); ?>",
                beforeSend: function () {
                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {
                    if (data[0] == 's') {
                        loadheaderdetails();
                        loadcostsheettable($('#detailID').val());
                        $('#totalcost1').val(0);
                        $('#uom').val('');
                        $form.bootstrapValidator('resetForm', true);
                        // updateunitratebymarkup($('#detailID').val());

                        //$('#headerID').val(data['last_id']);
                    }


                    HoldOn.close();
                    refreshNotifications(true);
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    });

    function updateunitratebymarkup(detailID) {

        $('#unitRateTransactionCurrency_1').val(1);
        $('#unitCostTranCurrency_1').val(2)


    }

    function loadCategory(projectID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {'projectID': projectID},
            url: "<?php echo site_url('Boq/loadCategory'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#htmlCategory').html(data);
                $('#categoryID').select2();


            }, error: function () {


                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/

            }
        });

    }


    function getallsavedvalues(headerID) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {headerID: headerID},
                url: "<?php echo site_url('Boq/getallsavedvalues'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('[href=#step2]').tab('show');
                    $('.btn-wizard').removeClass('disabled');
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step2]').removeClass('btn-default');
                    $('[href=#step2]').addClass('btn-primary');
                    if (!jQuery.isEmptyObject(data)) {
                        pID = data['projectID'];
                        $('#currency').val(data['customerCurrencyID']).change();

                        $('#projectID').val(data['projectID']).change();
                        $("#projectID").prop('disabled', true);
                        $('#documentdate').val(data['projectDocumentDate']);
                        $('#prjStartDate').val(data['projectDateFrom']);
                        $('#prjEndDate').val(data['projectDateTo']);
                        $('#comments').val(data['comment']);
                        $('#retentionpercentage').val(data['retensionPercentage']);
                        $('#customer').val(data['customerCode']).change();
                        $('#segement').val(data['segementID']).change();
                        /*  $('#projectname').prop('disabled',true);*/
                        $('#projectname').attr("readonly", true);
                        $('#projectname').val(data['projectDescription']);
                        $('#pcode').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                        $('.panel-heading').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                        $('#pcompany').html($('#projectID').select2('data')[0].text);
                        $('#psegement').html($('#segement').select2('data')[0].text);
                        $('#pcustomer').html($('#customer').select2('data')[0].text);

                        $('#pdocumentdate').html($('#documentdate').val());
                        $('#penddate').html($('#prjEndDate').val());
                        $('#pstartdate').html($('#prjStartDate').val());
                        $("#segement").prop('disabled', true);
                        $("#customer").prop('disabled', true);
                        /* $('#customer').select2('disable');
                         $('#customer').select2('disable');*/

                        $('#p2code').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                        $('#p2company').html($('#projectID').select2('data')[0].text);
                        $('#p2segement').html($('#segement').select2('data')[0].text);
                        $('#p2customer').html($('#customer').select2('data')[0].text);

                        $('#p2documentdate').html($('#documentdate').val());
                        $('#p2enddate').html($('#prjEndDate').val());
                        $('#p2startdate').html($('#prjStartDate').val());
                        $('#pcurrency').html($('#currency').select2('data')[0].text);
                        $('#p2currency').html($('#currency').select2('data')[0].text);
                        $("#currency").prop('disabled', true);
                        $('.confirmYNbtn').show();
                        if (data['confirmedYN'] == 1) {
                            $('.confirmYNbtn').hide();
                            $('.saveandnext').hide();
                            $('.editview').hide();
                        }
                    }
                    HoldOn.close();
                    refreshNotifications(true);


                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    }

    function deleteBoqCost(costingID, detailID) {
        if (costingID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able');?>", /*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>", /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'costingID': costingID, 'detailID': detailID},
                        url: "<?php echo site_url('Boq/deleteboqcost'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            loadcostsheettable($('#detailID').val());
                            loadheaderdetails();
                            HoldOn.close();
                            refreshNotifications(true);

                        }, error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }

    function deleteBoqdetail(detailID) {
        if (detailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*Your want to delete this record*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>", /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'detailID': detailID},
                        url: "<?php echo site_url('Boq/deleteboqdetail'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {
                            HoldOn.close();
                            myAlert(data[0], data[1]);
                            loadheaderdetails();
                        }, error: function () {
                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }


    function confirm_boq() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able_to_change');?>", /*Your will not be able to changes in this file!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_confirm_it');?>", /*Yes, Confirm it!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'headerID': $('#headerID').val()},
                    url: "<?php echo site_url('Boq/confirm_boq'); ?>",
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/pm/boq', '', 'Project');
                        }
                        ;
                    }, error: function () {
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function commaSeparateNumber(val) {
        while (/(\d+)(\d{3})/.test(val.toString())) {
            val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return val;
    }
    function varianceamount(amount, id) {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {'amount': amount, 'detailID': id},
                url: "<?php echo site_url('Boq/udate_varianceamt'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (data[0] == 's') {

                    }


                    HoldOn.close();
                    refreshNotifications(true);
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
            });
    }
    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }


</script>
