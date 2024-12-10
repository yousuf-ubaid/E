<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_corporate_goal_title');
//echo head_page($title, false);exit;
?>
<style>
    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .cg-option {
        margin-left: 17px !important;
        margin-right: 1px !important;
    }

    .weight-label-div {
        margin-top: 10px;
    }
</style>
<style>
    /* The customcheck */
    .customcheck {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .customcheck input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 5px;
    }

    /* On mouse-over, add a grey background color */
    .customcheck:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .customcheck input:checked ~ .checkmark {
        background-color: #02cf32;
        border-radius: 5px;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .customcheck input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .customcheck .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
</style>
<section class="content" id="ajax_body_container">
    <div class="row">
        <div class="col-md-12" id="sub-container">
            <div class="box">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title" id="box-header-title">
                        <?php echo $this->lang->line('appraisal_master_corporate_goal_title') ?><!--Corporate Goal--></h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12" id="sub-container">
                            <div class="row" style="margin-right: 2px">
                                <div class="col-md-9 text-center">
                                    &nbsp;
                                </div>
                                <div class="col-md-3 text-right">
                                    
                                    <button style="margin-top: 5px;" type="button" class="btn btn-primary pull-right"
                                            onclick="create_corporate_goal_btn_click.call(this)">
                                        <!--Add New Supplier Invoices-->
                                        <i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('appraisal_master_create_corporate_goal_btn'); ?><!--Create Supplier Invoice-->
                                    </button>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="corporate_goal_table" class="<?php echo table_class(); ?>">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 15%">#</th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_deparment_appraisal_docref_column'); ?><!--Doc Ref-->
                                                </th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_narration_column'); ?></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_created_date_column'); ?></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_from_date_column'); ?></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_to_date_column'); ?></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_confirmed_column'); ?></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?></th>
                                                <th style="min-width: 4%">
                                                    <?php echo $this->lang->line('common_action'); ?></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="corporate_goal_modal_read_only_view" role="dialog"
                                 aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="CommonEdit_Title">
                                                <?php echo $this->lang->line('appraisal_master_corporate_goal_title'); ?><!--Corporate--></h4>
                                        </div>

                                        <div class="modal-body" style="overflow-y: scroll;height: 500px;">
                                            <div class="tab-content">
                                                <div id="step1" class="tab-pane active">
                                                    <input type="hidden" id="supplierCreditPeriodhn"
                                                           name="supplierCreditPeriodhn">
                                                    <div class="row">
                                                        <div class="form-group col-sm-4">
                                                            <label for="document_id">
                                                                <?php echo $this->lang->line('appraisal_activity_department_appraisal_document_id'); ?><!--Document Id-->
                                                                : </label>
                                                            <span id="document_id_read_only"></span>

                                                        </div>
                                                        <div class="form-group col-sm-4">
                                                        </div>
                                                        <div class="form-group col-sm-4">
                                                            <div id="closed_label_div">
                                                                <label for="closed_label"><?php echo $this->lang->line('common_status'); ?><!--Status-->:</label>
                                                                <span id="closed_label" class="label label-text-size"
                                                                      style="background-color: red;margin-right: 5px;display: none;"><?php echo $this->lang->line('common_closed'); ?><!--Closed--></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-8">
                                                            <label for="narration">
                                                                <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_narration_field'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                                            <div id="narration_read_only"></div>
                                                        </div>
                                                        <div class="form-group col-sm-4 created_date_form_group">
                                                            <label for="created_date">
                                                                <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_created_date_field'); ?></label>
                                                            <div id="created_date_read_only"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-12">
                                                            <fieldset style="margin-top:5px;">
                                                                <legend style="margin-bottom: 5px;font-size: 15px;font-weight: 600;">
                                                                    <?php echo $this->lang->line('appraisal_appraisal_period'); ?>
                                                                    <!--Appraisal Period-->
                                                                </legend>
                                                                <div class="form-group col-sm-4">
                                                                    <label for="from_date">
                                                                        <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_from_date_field'); ?><?php required_mark(); ?></label>
                                                                    <div id="from_date_read_only"></div>
                                                                </div>
                                                                <div class="form-group col-sm-4">
                                                                    <label for="to_date">
                                                                        <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_to_date_field'); ?><?php required_mark(); ?></label>
                                                                    <div id="to_date_read_only"></div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <div class="form-group col-md-12">
                                                                <input class="cg-option" type="radio"
                                                                       id="objective_based_read_only"
                                                                       name="goal_type_read_only"
                                                                       value="objective_based"/> <?php echo $this->lang->line('appraisal_objective_based_appraisal'); ?>
                                                                <!--Objective Based Appraisal-->
                                                                <input class="cg-option" type="radio"
                                                                       id="performance_based_read_only"
                                                                       name="goal_type_read_only"
                                                                       value="performance_based"/> <?php echo $this->lang->line('appraisal_performance_based_appraisal'); ?>
                                                                <!--Performance Based Appraisal-->
                                                                <input class="cg-option" type="radio"
                                                                       id="both_read_only"
                                                                       name="goal_type_read_only"
                                                                       checked value="both"/>
                                                                <?php echo $this->lang->line('appraisal_both'); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div>
                                                <br>

                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="objective_tab_read_only-tab"
                                                           data-toggle="tab"
                                                           href="#objective_tab_read_only"
                                                           role="tab" aria-controls="objective_tab_read_only"
                                                           aria-selected="true"><?php echo $this->lang->line('appraisal_objective_based_appraisal'); ?><!--Objective
                                                            Based
                                                            Appraisal--></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="performance_tab_read_only-tab"
                                                           data-toggle="tab"
                                                           href="#performance_tab_read_only"
                                                           role="tab" aria-controls="performance_tab_read_only"
                                                           aria-selected="false"><?php echo $this->lang->line('appraisal_performance_based_appraisal'); ?><!--Performance
                                                            Based
                                                            Appraisal--></a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade active" id="objective_tab_read_only"
                                                         role="tabpanel"
                                                         aria-labelledby="objective_tab_read_only-tab">
                                                        <div class="row">
                                                            <div class="col-md-3">

                                                            </div>
                                                            <div class="col-md-4">

                                                            </div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <table id="goal_objectives_table_read_only"
                                                                       class="<?php echo table_class(); ?>">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="min-width: 15%">
                                                                            <?php echo $this->lang->line('common_objective'); ?><!--Objective-->
                                                                        </th>
                                                                        <th style="min-width: 15%">
                                                                            <?php echo $this->lang->line('appraisal_weight'); ?><!--Weight-->
                                                                        </th>
                                                                        <th style="min-width: 15%">
                                                                            <?php echo $this->lang->line('appraisal_assigned_department'); ?><!--Assigned Department-->
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="performance_tab_read_only"
                                                         role="tabpanel"
                                                         aria-labelledby="performance_tab_read_only-tab">
                                                        <div class="form-group col-md-12">
                                                            <div class="form-group col-md-4">
                                                                <select disabled="disabled"
                                                                        id="soft_skills_templates_dropdown_read_only"
                                                                        onchange="templates_dropdown_change()"
                                                                        class="form-control">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <div id="softskills_template_read_only"></div>
                                                        </div>

                                                    </div>
                                                </div>


                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-4">
                                                <div id="closed_label_detail" style="text-align: left;">
                                                    <div><span><?php echo $this->lang->line('common_closed_by'); ?><!--Closed By-->:</span>&nbsp;<span id="closed_by"></span></div>
                                                    <div><span><?php echo $this->lang->line('common_closed_date'); ?><!--Closed Date-->:</span>&nbsp;<span id="closed_date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-right m-t-xs">
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="modal fade" id="corporate_goal_modal" role="dialog"
                                 aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title"
                                                id="CommonEdit_Title"> <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal') ?></h4>
                                        </div>

                                        <div class="modal-body" style="overflow-y: scroll;height: 500px;">
                                            <div class="tab-content">
                                                <div id="step1" class="tab-pane active">
                                                    <input type="hidden" id="supplierCreditPeriodhn"
                                                           name="supplierCreditPeriodhn">
                                                    <div class="row">
                                                        <div class="form-group col-sm-8">
                                                        </div>
                                                        <div class="form-group col-sm-4 document_id">
                                                            <label for="document_id"><?php echo $this->lang->line('appraisal_activity_department_appraisal_document_id') ?>
                                                                : </label>
                                                            <span id="document_id"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-8">
                                                            <label for="narration">
                                                                <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_narration_field'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                                            <input type="text" id="narration" class="form-control"/>
                                                            <div id="narrationError" class="error-message"></div>
                                                        </div>
                                                        <div class="form-group col-sm-4 created_date_form_group">
                                                            <label for="created_date">
                                                                <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_created_date_field'); ?></label>
                                                            <input id="created_date" class="form-control date-picker"
                                                                   disabled/>
                                                            <div id="created_date_error" class="error-message"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-12">
                                                            <fieldset style="margin-top:5px;">
                                                                <legend style="margin-bottom: 5px;font-size: 15px;font-weight: 600;">
                                                                    <?php echo $this->lang->line('appraisal_appraisal_period') ?>
                                                                </legend>
                                                                <div class="form-group col-sm-4">
                                                                    <label for="from_date">
                                                                        <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_from_date_field'); ?><?php required_mark(); ?></label>
                                                                    <input id="from_date"
                                                                           class="form-control date-picker"
                                                                           autocomplete="off"/>
                                                                    <div id="from_date_error"
                                                                         class="error-message"></div>
                                                                </div>
                                                                <div class="form-group col-sm-4">
                                                                    <label for="to_date">
                                                                        <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_to_date_field'); ?><?php required_mark(); ?></label>
                                                                    <input id="to_date"
                                                                           class="form-control date-picker"
                                                                           autocomplete="off"/>
                                                                    <div id="to_date_error" class="error-message"></div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <div class="form-group col-md-12">
                                                                <input class="cg-option" type="radio"
                                                                       id="objective_based"
                                                                       name="goal_type"
                                                                       value="objective_based"/><?php echo $this->lang->line('appraisal_objective_based_appraisal') ?>

                                                                <input class="cg-option" type="radio"
                                                                       id="performance_based"
                                                                       name="goal_type"
                                                                       value="performance_based"/><?php echo $this->lang->line('appraisal_performance_based_appraisal') ?>

                                                                <input class="cg-option" type="radio" id="both"
                                                                       name="goal_type" checked
                                                                       value="both"/><?php echo $this->lang->line('appraisal_both') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <br>
                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="objective_tab-tab"
                                                           data-toggle="tab"
                                                           href="#objective_tab"
                                                           role="tab" aria-controls="objective_tab"
                                                           aria-selected="true"><?php echo $this->lang->line('appraisal_objective_based_appraisal') ?></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="performance_tab-tab" data-toggle="tab"
                                                           href="#performance_tab"
                                                           role="tab" aria-controls="performance_tab"
                                                           aria-selected="false"><?php echo $this->lang->line('appraisal_performance_based_appraisal') ?></a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade active" id="objective_tab" role="tabpanel"
                                                         aria-labelledby="objective_tab-tab">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <button class="btn btn-primary" id="btn_add_objective">
                                                                    + <?php echo $this->lang->line('appraisal_btn_add_objective'); ?></button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="weight-label-div">
                                                                    <label>
                                                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_total_weight') ?><!--Total Weight-->
                                                                        :</label> <span
                                                                            id="total_weight">0</span> %
                                                                </div>
                                                                <div>
                                                                    <span id="total_weight_error" style="color: red;">
                                                                        <?php echo $this->lang->line('appraisal_total_weight_cannot_exceed_100') ?></span><!--Total weight cannot exceed 100-->
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div>
                                                                    <span id="objectives_list_error"
                                                                          style="color: red;"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <table id="goal_objectives_table"
                                                                       class="<?php echo table_class(); ?>">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="min-width: 15%"><?php echo $this->lang->line('appraisal_objective') ?>
                                                                            <!--Objective-->
                                                                        </th>
                                                                        <th style="min-width: 15%"><?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_weight') ?>
                                                                            <!--Weight-->
                                                                        </th>
                                                                        <th style="min-width: 15%"><?php echo $this->lang->line('appraisal_assigned_department') ?>
                                                                            <!--Assigned Department-->
                                                                        </th>
                                                                        <th style="min-width: 15%"><?php echo $this->lang->line('common_action') ?>
                                                                            <!--Actions-->
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="performance_tab" role="tabpanel"
                                                         aria-labelledby="performance_tab-tab">
                                                        <div class="form-group col-md-12">
                                                            <div class="form-group col-md-4">
                                                                <select id="soft_skills_templates_dropdown"
                                                                        onchange="templates_dropdown_change()"
                                                                        class="form-control">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <div id="softskills_template"></div>
                                                        </div>

                                                    </div>
                                                </div>


                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="text-right m-t-xs">
                                                <button type="button" class="btn btn-default"
                                                        data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                                                <button class="btn btn-primary" id="save_corporate_objective"
                                                        onclick="" type="button">
                                                    <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                                                <button class="btn btn-success" id="confirm_corporate_objective"
                                                        onclick="" type="button">
                                                    <?php echo $this->lang->line('appraisal_save_and_confirm'); ?><!--Save & Next--></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="goal_objectives_modal" role="dialog"
                                 aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="CommonEdit_Title"><?php echo $this->lang->line('common_goal_objectives'); ?><!--Goal Objectives--></h4>
                                        </div>

                                        <div class="modal-body" style="overflow-y: scroll;height: 500px;">

                                            <div class="row">
                                                <div class="form-group col-sm-12">
                                                    <label><?php echo $this->lang->line('common_objectives'); ?><!--Objectives--></label>
                                                    <div id="objectives_list_for_select">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-right m-t-xs">
                                                <button class="btn btn-primary" id="add_corporate_objective"
                                                        onclick="" type="button">
                                                    <?php echo $this->lang->line('common_add'); ?><!--Save & Next--></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</section>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    app = {};
    app.company_id = <?php echo current_companyID(); ?>;
    app.corporate_goal_table = $('#corporate_goal_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });
    app.goal_objectives_table = $('#goal_objectives_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });
    app.goal_objectives_table_read_only = $('#goal_objectives_table_read_only').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
    });

    app.id_list_for_delete = [];
    app.is_confirmed = '';
    app.from_date = '';
    app.to_date = '';

    $(document).ready(function () {

    });

    function templates_dropdown_change_read_only() {
        load_softskills_template_read_only();
    }

    function templates_dropdown_change() {
        load_softskills_template();
    }

    $('#save_corporate_objective').click(function () {
        app.is_confirmed = '';
        if (corporate_goal_form_validation()) {
            if (app.form_status == 'save') {
                save_goal();
            } else if (app.form_status == 'edit') {
                edit_goal();
            }
        }
    });

    $('input[name="goal_type_read_only"]').on('click change', function (e) {

        var status = e.target.value;

        if (status == 'objective_based') {
            $("#performance_tab_read_only-tab").hide();
            $("#performance_tab_read_only").attr('display', 'none');


            $("#objective_tab_read_only-tab").show();
            $("#objective_tab_read_only").attr('display', 'block');

            $("#objective_tab_read_only-tab").trigger("click");
        } else if (status == 'performance_based') {
            $("#objective_tab_read_only-tab").hide();
            $("#objective_tab_read_only").attr('display', 'none');

            $("#performance_tab_read_only-tab").show();

            $("#performance_tab_read_only").attr('display', 'none');
            $("#performance_tab_read_only-tab").trigger("click");
        } else if (status == 'both') {
            $("#performance_tab_read_only-tab").show();
            $("#performance_tab_read_only").attr('display', 'block');


            $("#objective_tab_read_only-tab").show();
            $("#objective_tab_read_only").attr('display', 'block');
            $("#objective_tab_read_only-tab").trigger("click");
        }
    });

    $('input[name="goal_type"]').on('click change', function (e) {

        var status = e.target.value;

        if (status == 'objective_based') {
            $("#performance_tab-tab").hide();
            $("#performance_tab").attr('display', 'none');


            $("#objective_tab-tab").show();
            $("#objective_tab").attr('display', 'block');
            $("#objective_tab-tab").trigger("click");
        } else if (status == 'performance_based') {
            $("#objective_tab-tab").hide();
            $("#objective_tab").attr('display', 'none');

            $("#performance_tab-tab").show();

            $("#performance_tab").attr('display', 'none');
            $("#performance_tab-tab").trigger("click");
        } else if (status == 'both') {
            $("#performance_tab-tab").show();
            $("#performance_tab").attr('display', 'block');


            $("#objective_tab-tab").show();
            $("#objective_tab").attr('display', 'block');
            $("#objective_tab-tab").trigger("click");
        }
    });


    $('#confirm_corporate_objective').click(function () {
        app.is_confirmed = '1';
        if (is_approval_setup_exist()) {
            if (corporate_goal_form_validation()) {
                if (app.form_status == 'save') {
                    save_goal();
                } else if (app.form_status == 'edit') {
                    edit_goal();
                }
            }
        } else {
            myAlert('e', '<?php echo $this->lang->line('appraisal_approval_setup_not_configured'); ?>');/*Approval setup not configured*/
            //myAlert('e', 'Approval setup not configured.');
        }

    });

    function is_approval_setup_exist() {
        app.is_approval_setup_exist = true;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {},
            url: '<?php echo site_url('Appraisal/is_cg_approval_setup_exist') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data.status == false) {
                    app.is_approval_setup_exist = false;
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
                //alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        return app.is_approval_setup_exist;
    }

    function edit_goal() {

        var new_goal_objective_array = [];//new records
        var edited_goal_objective_array = [];//existing records

        var appraisal_type = $('input[name="goal_type"]:checked').val();
        if (appraisal_type != "performance_based") {
            $('.goal_objective_description').each(function (index, value) {
                var objective_field_id = '#' + value.id;
                var weight_field_id = "#weight-" + value.id;
                var assigned_department_field_id = "#select-" + value.id;
                var corporate_objective_id = $(objective_field_id).data('corporate_objective_id');
                var weight = $(weight_field_id).val();
                var assigned_department_id = $(assigned_department_field_id).val();


                //existing record has a numeric id, but new record has a generated string id. so here it is creating two different list.
                if (isNaN(value.id)) {
                    new_goal_objective_array.push({
                        corporate_objective_id: corporate_objective_id,
                        weight: weight,
                        assigned_department_id: assigned_department_id
                    })
                } else {
                    edited_goal_objective_array.push({
                        goal_objective_mapping_id: value.id,
                        corporate_objective_id: corporate_objective_id,
                        weight: weight,
                        assigned_department_id: assigned_department_id
                    })
                }
            });
        }

        var narration = $('#narration').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var document_id = $('#document_id').text();
        var confirmed = app.is_confirmed;
        var appraisal_type = $('input[name="goal_type"]:checked').val();
        var selected_template = $('#soft_skills_templates_dropdown').val();
        startLoad();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/update_corporate_goal'); ?>",
            data: {
                goal_id: app.goal_id,
                company_id: app.company_id,
                new_goal_objective_array: new_goal_objective_array,
                edited_goal_objective_array: edited_goal_objective_array,
                id_list_for_delete: app.id_list_for_delete,
                narration: narration,
                from_date: from_date,
                to_date: to_date,
                confirmed: confirmed,
                document_id: document_id,
                appraisal_type: appraisal_type,
                selected_template: selected_template
            },
            success: function (data) {
                stopLoad();
                $("#corporate_goal_modal").modal('hide');
                load_corporate_goals_table(app.company_id);
            }
        });
    }

    function save_goal() {
        var goal_objective_array = [];
        var appraisal_type = $('input[name="goal_type"]:checked').val();
        if (appraisal_type != "performance_based") {
            $('.goal_objective_description').each(function (index, value) {
                var objective_field_id = '#' + value.id;
                var weight_field_id = "#weight-" + value.id;
                var assigned_department_field_id = "#select-" + value.id;
                var corporate_objective_id = $(objective_field_id).data('corporate_objective_id');
                var weight = $(weight_field_id).val();
                var assigned_department_id = $(assigned_department_field_id).val();

                goal_objective_array.push({
                    corporate_objective_id: corporate_objective_id,
                    weight: weight,
                    assigned_department_id: assigned_department_id
                })
            });
        }
        var narration = $('#narration').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var confirmed = app.is_confirmed;
        var appraisal_type = $('input[name="goal_type"]:checked').val();

        var selected_template = "";
        if(appraisal_type!='objective_based'){
            selected_template = $('#soft_skills_templates_dropdown').val();
        }

        startLoad();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/insert_corporate_goal'); ?>",
            data: {
                company_id: app.company_id,
                goal_objective_array: goal_objective_array,
                narration: narration,
                from_date: from_date,
                to_date: to_date,
                confirmed: confirmed,
                appraisal_type: appraisal_type,
                selected_template: selected_template
            },
            success: function (data) {
                stopLoad();
                $("#corporate_goal_modal").modal('hide');
                load_corporate_goals_table(app.company_id);
            }
        });
    }


    $(document).ready(function () {
        load_departments_list();
        $('.date-picker').datepicker({format: 'yyyy-mm-dd'});
        load_corporate_objectives_to_select(app.company_id);
        load_department_dropdown(app.company_id);
        load_corporate_goals_table(app.company_id);
        $("#objective_tab-tab").trigger("click");
        load_softskills_templates_dropdown(null, app.company_id);
        
        load_softskills_template();
    });

    function load_departments_list() {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_departments'); ?>",
            data: {company_id: app.company_id},
            success: function (data) {
                localStorage.setItem("departments", JSON.stringify(data));
            }
        });
    }

    function load_softskills_template_read_only() {
        var template_id = $("#soft_skills_templates_dropdown_read_only").val();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_template_details'); ?>",
            data: {template_id: template_id},
            success: function (data) {
                //var template_body = '<table class="table table-striped table-bordered" id="template_table_read_only"><thead><tr>';
                //template_body += '<th>Performance Area</th>';
                var template_body = '<table class="table table-striped table-bordered" id="template_table_read_only"><thead><tr>';
                template_body += '<th><?php echo $this->lang->line('appraisal_performance_area')?></th>';/*Performance Area*/
                data.skills_grades_list.forEach(function (item, index) {
                    template_body += '<th>' + item.grade + '</th>';
                });
                template_body += '</tr></thead>' +
                    '<tbody id="table_body_read_only"></tbody></table>';
                $("#softskills_template_read_only").html(template_body);

                var table_body = "";
                data.skills_performance_area_list.forEach(function (item, index) {
                    table_body += '<tr>' +
                        '<td>' + item.performance_area ;
                    table_body += '<br><ul>';
                    item.sub_performance_areas.forEach(function (item,index){
                        table_body += '<li>'+item.performance_area+'</li>'
                    });
                    table_body += '</ul>';
                    table_body += '</td>';
                    for (var i = 1; i <= data.skills_grades_list.length; i++) {
                        table_body += '<td></td>';
                    }
                    table_body += '</tr>'
                });
                $("#table_body_read_only").html(table_body);
            }
        });
    }


    function load_softskills_template() {
        var template_id = $("#soft_skills_templates_dropdown").val();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_template_details'); ?>",
            data: {template_id: template_id},
            success: function (data) {
               //check marking Type - srp_erp_apr_softskills_master
                if(data.skills_template_details[0].markingType == 2){
                    if (data.skills_performance_area_list.length == 0 || data.skills_template_details.length == 0) {
                        app.is_template_incomplete = 1;
                    } else {
                        app.is_template_incomplete = 0;
                    }
                }else{
                    if (data.skills_grades_list.length == 0 || data.skills_performance_area_list.length == 0 || data.skills_template_details.length == 0) {
                        app.is_template_incomplete = 1;
                    } else {
                        app.is_template_incomplete = 0;
                    }
                }
                var template_body = '<table class="table table-striped table-bordered" id="template_table"><thead><tr>';
                //template_body += '<th>Performance Area</th>';
                template_body += '<th><?php echo $this->lang->line('appraisal_performance_area')?></th>';/*Performance Area*/

                data.skills_grades_list.forEach(function (item, index) {
                    template_body += '<th>' + item.grade + '</th>';
                });
                template_body += '</tr></thead>' +
                    '<tbody id="table_body"></tbody></table>';
                $("#softskills_template").html(template_body);

                var table_body = "";
                data.skills_performance_area_list.forEach(function (item, index) {
                    table_body += '<tr>' +
                        '<td>' + item.performance_area ;
                    table_body += '<br><ul>';
                    item.sub_performance_areas.forEach(function (item,index){
                        table_body += '<li>'+item.performance_area+'</li>'
                    });
                    table_body += '</ul>';
                    table_body += '</td>';
                    for (var i = 1; i <= data.skills_grades_list.length; i++) {
                        table_body += '<td></td>';
                    }
                    table_body += '</tr>'
                });
                $("#table_body").html(table_body);
            }
        });
    }


    function load_corporate_goals_table(company_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_corporate_goals'); ?>",
            data: {company_id: company_id},
            success: function (data) {
                app.corporate_goal_table.clear().draw();
                var sequence = 1;
                data.forEach(function (item, index) {
                    var narration = item.narration;

                    var action = get_coporate_goal_action(item);

                    let d = new Date(item.from);
                    var month = format_for_two_digits((d.getMonth() + 1));
                    var date = format_for_two_digits(d.getDate());
                    var from = d.getFullYear() + '-' + month + '-' + date;
                    $('#from_date').val(from);

                    d = new Date(item.to);
                    var month = format_for_two_digits((d.getMonth() + 1));
                    var date = format_for_two_digits(d.getDate());
                    var to = d.getFullYear() + '-' + month + '-' + date;
                    $('#to_date').val(to);

                    d = new Date(item.created_date);
                    var month = format_for_two_digits((d.getMonth() + 1));
                    var date = format_for_two_digits(d.getDate());
                    var created_date = d.getFullYear() + '-' + month + '-' + date;
                    $('#created_date').val(created_date);

                    if (item.confirmedYN == '0') {
                        var confirmed = '<div style="text-align: center;"><span class="label label-danger">&nbsp;</span></div>';

                    } else if (item.confirmedYN == '2') {
                        var confirmed = '<div style="text-align: center;"><span class="label label-warning">&nbsp;</span></div>';

                    } else {
                        var confirmed = '<div style="text-align: center;"><span class="label label-success">&nbsp;</span></div>';
                    }

                    var approved = null;
                    if (item.confirmedYN == '1') {
                        if (item.approvedYN == '0') {
                            var approved = '<div style="text-align: center;"><a onclick="fetch_all_approval_users_modal(\'CG\',' + item.id + ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></div>';
                        } else {
                            var approved = '<div style="text-align: center;"><a onclick="fetch_all_approval_users_modal(\'CG\',' + item.id + ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></div>';
                        }
                    }else if (item.confirmedYN == '2'){
                        var approved = '<div style="text-align: center;"><span class="label label-danger">&nbsp;</span></div>';
                    } else {
                        var approved = '<div style="text-align: center;"><a onclick="fetch_all_approval_users_modal(\'CG\',' + item.id + ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></div>';
                    }

                    var action = '<div style="text-align: right;padding-right: 26px;">';


                    if (item.approvedYN == "1" && item.is_closed != "1") {
                        //close button
                        action += '<i title="Close corporate goal" onclick="corporate_goal_close.call(this)" data-goal_id="' + item.id + '" class="glyphicon glyphicon-ok corporate-goal-close act-btn-margin" style="color: #dd4b39;"></i>';
                    } else if (item.confirmedYN == '1' && item.is_closed != "1") {
                        //referback button
                        action += '<i onclick="corporate_goal_referback_popup.call(this)" data-goal_id="' + item.id + '" class=" glyphicon glyphicon-repeat act-btn-margin" style="color:#ff3f3a;"></i>';
                    } else if (item.is_closed != "1") {
                        //edit button
                        action += '<i onclick="corporate_goal_edit_popup.call(this)" data-goal_id="' + item.id + '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>';
                    }

                    if (item.is_closed != "1" && item.approvedYN != "1") {
                        action += ' <i onclick="corporate_goal_delete.call(this)" data-goal_id="' + item.id + '" class="glyphicon glyphicon-trash corporate-goal-delete act-btn-margin" style="color:#ff3f3a"></i>';

                    }
                    //action += '<a onclick="attachment_modal('+item.id+',\'Corporate Goal\',\'CG\',1);"><span title="" rel="tooltip" class="glyphicon glyphicon-paperclip" data-original-title="Attachment"></span></a> <i onclick="corporate_goal_view_popup.call(this)" data-goal_id="' + item.id + '" class="glyphicon glyphicon-eye-open act-btn-margin" style="color: #3c8dbc;"></i>';
                    action += '<a onclick="attachment_modal('+item.id+',\'<?php echo $this->lang->line('appraisal_master_corporate_goal_title') ?>\',\'CG\',1);"><span title="" rel="tooltip" class="glyphicon glyphicon-paperclip" data-original-title="Attachment"></span></a> <i onclick="corporate_goal_view_popup.call(this)" data-goal_id="' + item.id + '" class="glyphicon glyphicon-eye-open act-btn-margin" style="color: #3c8dbc;"></i>';
                    action += '</div>';

                    app.corporate_goal_table.row.add([sequence, item.document_id, narration, created_date, from, to, confirmed, approved, get_coporate_goal_action(item)]).draw(false);
                    sequence++;
                })
            }
        });
    }

    function get_coporate_goal_action(item) {
        var action = '<div class="btn-group" style="display: flex; justify-content: center;">';
        action += '<button type="button" class="btn btn-secondary dropdown-toggle" id="dropdownMenu' + item.id + '" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">';
        action += 'Actions <span class="caret"></span>';
        action += '</button>';
        action += '<ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left" aria-labelledby="dropdownMenu' + item.id + '">';

        if (item.approvedYN == "1" && item.is_closed != "1") {
            action += '<li><a href="#" onclick="corporate_goal_close.call(this)" data-goal_id="' + item.id + '"><span class="glyphicon glyphicon-ok"></span> Close Corporate Goal</a></li>';
        } else if (item.confirmedYN == "1" && item.is_closed != "1") {
            action += '<li><a href="#" onclick="corporate_goal_referback_popup.call(this)" data-goal_id="' + item.id + '"><span class="glyphicon glyphicon-repeat" style="color: "></span> Refer Back</a></li>';
        } else if (item.is_closed != "1") {
            action += '<li><a href="#" onclick="corporate_goal_edit_popup.call(this)" data-goal_id="' + item.id + '"><span class="glyphicon glyphicon-pencil" style="color: #116f5e"></span> Edit</a></li>';
        }

        if (item.is_closed != "1" && item.approvedYN != "1") {
            action += '<li><a href="#" onclick="corporate_goal_delete.call(this)" data-goal_id="' + item.id + '"><span class="glyphicon glyphicon-trash" style="color: red"></span> Delete</a></li>';
        }

        action += '<li><a href="#" onclick="attachment_modal(' + item.id + ',\'<?php echo $this->lang->line('appraisal_master_corporate_goal_title') ?>\',\'CG\',1);"><span class="glyphicon glyphicon-paperclip" style="color: #4caf50"></span> Attachments</a></li>';
        action += '<li><a href="#" onclick="corporate_goal_view_popup.call(this)" data-goal_id="' + item.id + '"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4"></span> View</a></li>';

        action += '</ul>';
        action += '</div>';

        return action;
    }

    function corporate_goal_referback_popup() {
        var goal_id = $(this).data('goal_id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_referback_this_corporate_goal'); ?>?",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('common_yes'); ?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo $this->lang->line('common_no'); ?>',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/confirmation_referback_corporate_goal'); ?>",
                        data: {goal_id: goal_id},
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('s', '<?php echo $this->lang->line('appraisal_corporate_goal_successfully_referred_back'); ?>');/*Corporate goal successfully referred back*/
                                //myAlert('s', 'Corporate goal successfully referred back.');
                                fetchPage('system/appraisal/activity/set_corporate_goal');
                            } else if (data.status == 'already_approved') {
                                myAlert('w', '<?php echo $this->lang->line('appraisal_this_corporate_goal_is_already_approved'); ?>');/*This corporate goal is already approved*/
                                //myAlert('w', 'This corporate goal is already approved.');
                                fetchPage('system/appraisal/activity/set_corporate_goal');
                            }
                            load_corporate_goals_table(app.company_id);
                            stopLoad();
                        }
                    });
                }
            }
        });
    }

    function corporate_goal_close() {
        var goal_id = $(this).data('goal_id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_close_this_corporate_goal'); ?>?",/*Are you sure you want to close this corporate goal?*/
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('common_yes'); ?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo $this->lang->line('common_no'); ?>',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/close_corporate_goal'); ?>",
                        data: {goal_id: goal_id},
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('s', '<?php echo $this->lang->line('appraisal_corporate_goal_successfully_closed'); ?>');/*Corporate goal successfully closed*/
                            }
                            load_corporate_goals_table(app.company_id);
                            stopLoad();
                        }
                    });
                }
            }
        });
    }

    function corporate_goal_delete() {
        var goal_id = $(this).data('goal_id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_this_goal'); ?>?",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('common_yes'); ?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo $this->lang->line('common_no'); ?>',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/delete_corporate_goal'); ?>",
                        data: {goal_id: goal_id},
                        success: function (data) {
                            stopLoad();
                            if (data.status == 'approved') {
                                myAlert('w', '<?php echo $this->lang->line('appraisal_this_corporate_goal_is_approved'); ?>');/*This Corporate Goal Is Approved*/
                            } else if (data.status == 'already_in_use') {
                                myAlert('w', '<?php echo $this->lang->line('appraisal_this_corporate_goal_is_already_in_use'); ?>');/*This Corporate Goal Is Already In Use*/
                            } else if (data.status == 'success') {
                                myAlert('s', '<?php echo $this->lang->line('appraisal_corporate_goal_deleted_successfully'); ?>');/*Corporate Goal Deleted Successfully*/
                            } else if (data.status == 'already_confirmed') {
                                myAlert('w', '<?php echo $this->lang->line('appraisal_this_corporate_goal_is_already_confirmed_please_referback_before_delete'); ?>');/*This Corporate Goal Is Already Confirmed. Please Referback Before Delete*/
                            } else if (data.status == 'db_error') {
                                myAlert('e', '<?php echo $this->lang->line('appraisal_database_transaction_error'); ?>');/*Database Transaction Error.*/
                            }
                            load_corporate_goals_table(app.company_id);
                        }
                    });
                }
            }
        });
    }

    function corporate_goal_view_popup() {
        startLoad();
        app.form_status = 'edit';
        app.id_list_for_delete = [];//old values are cleared
        app.goal_id = $(this).data('goal_id');
        $('.created_date_form_group').show();//this field only showing in edit mode.
        corporate_goal_form_hide_errors();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_corporate_goal_details'); ?>",
            data: {goal_id: app.goal_id},
            success: function (data) {

                if (data.goal_details[0].is_closed == 1) {
                    $("#closed_label").show();
                    $("#closed_label_div").show();

                    $("#closed_label_detail").show();
                    $("#closed_by").text(data.goal_details[0].Ename1);

                    var d = new Date(data.goal_details[0].modified_at);
                    var m = d.getMonth() + 1;
                    if (m < 10) {
                        m = "0" + m;
                    }
                    let date = d.getFullYear() + '-' + m + '-' + d.getDate();
                    $("#closed_date").text(date);

                } else {
                    $("#closed_label_div").hide();
                    $("#closed_label").hide();
                    $("#closed_label_detail").hide();
                }

                $('#document_id_read_only').html(data.goal_details[0].document_id);

                $('#narration_read_only').html(data.goal_details[0].narration);

                var d = new Date(data.goal_details[0].from);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                from = d.getFullYear() + '-' + month + '-' + date;
                $('#from_date_read_only').html(from);
                app.from_date = from;

                var d = new Date(data.goal_details[0].to);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                to = d.getFullYear() + '-' + month + '-' + date;
                $('#to_date_read_only').html(to);
                app.to_date = to;

                var d = new Date(data.goal_details[0].created_date);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                created_date = d.getFullYear() + '-' + month + '-' + date;
                $('#created_date_read_only').html(created_date);

                var goal_type = data.goal_details[0].appraisal_type;
                var selector = '#' + goal_type + '_read_only';


                $("#objective_based_read_only").removeAttr('disabled');
                $("#performance_based_read_only").removeAttr('disabled');
                $("#both_read_only").removeAttr('disabled');
                $(selector).trigger("click");
                $("#objective_based_read_only").attr('disabled', 'disabled');
                $("#performance_based_read_only").attr('disabled', 'disabled');
                $("#both_read_only").attr('disabled', 'disabled');

                //selecting goal type (objective based, performance based or both)
                var goal_type = data.goal_details[0].appraisal_type;
                var selector = '#' + goal_type + '_read_only';
                $(selector).trigger("click");

                var softskill_template_id = data.goal_details[0].softskills_template_id;

                load_softskills_templates_dropdown_read_only(softskill_template_id, app.company_id);
                templates_dropdown_change_read_only();

                app.goal_objectives_table_read_only.clear().draw();

                data.goal_objectives.map(function (value, index) {
                    var corporate_objective_id = value['objective_master_id'];
                    var obejctive_mapping_id = value['objective_mapping_id'];
                    var departments_drop_down_list_html = generated_dropdown_list_options_for_department(value['DepartmentMasterID']);
                    corporate_objective_description = '<span class="goal_objective_description" id="' + obejctive_mapping_id + '" data-corporate_objective_id="' + corporate_objective_id + '">' + value['objective_description'] + '</span>';
                    var departments_dropdown = '<div style="text-align: center">' + value['DepartmentDes'] + '</div>';

                    var weight_input = '<div style="text-align: center">' + value['weight'] + ' %</div>';
                    app.goal_objectives_table_read_only.row.add([corporate_objective_description, weight_input, departments_dropdown]).draw(false);
                });
                stopLoad();
                $("#corporate_goal_modal_read_only_view").modal('show');
            }
        });


    }

    function show_hidden_fields() {
        $('.document_id').show();
        $('.created_date_form_group').show();
    }

    function corporate_goal_edit_popup() {
        startLoad();
        app.form_status = 'edit';
        app.id_list_for_delete = [];//old values are cleared
        app.goal_id = $(this).data('goal_id');
        show_hidden_fields()//this fields are only showing in edit mode.
        corporate_goal_form_hide_errors();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_corporate_goal_details'); ?>",
            data: {goal_id: app.goal_id},
            success: function (data) {

                $('#document_id').html(data.goal_details[0].document_id);

                $('#narration').val(data.goal_details[0].narration);

                var d = new Date(data.goal_details[0].from);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                from = d.getFullYear() + '-' + month + '-' + date;
                $('#from_date').val(from);
                app.from_date = from;

                var d = new Date(data.goal_details[0].to);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                to = d.getFullYear() + '-' + month + '-' + date;
                $('#to_date').val(to);
                app.to_date = to;

                var d = new Date(data.goal_details[0].created_date);
                var month = format_for_two_digits((d.getMonth() + 1));
                var date = format_for_two_digits(d.getDate());
                created_date = d.getFullYear() + '-' + month + '-' + date;
                $('#created_date').val(created_date);

                if (data.goal_details[0].confirmedYN == "1") {
                    app.is_confirmed = '1';
                } else if (data.goal_details[0].confirmedYN == "2") {
                    app.is_confirmed = '2';
                } else {
                    app.is_confirmed = '';
                }

                if (app.is_confirmed == '1') {
                    $("#save_corporate_objective").hide();
                    $("#confirm_corporate_objective").hide();
                } else {
                    $("#save_corporate_objective").show();
                    $("#confirm_corporate_objective").show();
                }

                if (data.goal_details[0].approvedYN == '0') {
                    //$("#confirm_corporate_objective").show();
                }

                var goal_type = data.goal_details[0].appraisal_type;
                var selector = '#' + goal_type;

                $(selector).trigger("click");

                var softskill_template_id = data.goal_details[0].softskills_template_id;

                load_softskills_templates_dropdown(softskill_template_id, app.company_id);
                templates_dropdown_change();

                app.goal_objectives_table.clear().draw();

                data.goal_objectives.map(function (value, index) {
                    var corporate_objective_id = value['objective_master_id'];
                    var obejctive_mapping_id = value['objective_mapping_id'];
                    var departments_drop_down_list_html = generated_dropdown_list_options_for_department(value['DepartmentMasterID']);
                    corporate_objective_description = '<span class="goal_objective_description" id="' + obejctive_mapping_id + '" data-corporate_objective_id="' + corporate_objective_id + '">' + value['objective_description'] + '</span>';
                    var departments_dropdown = '<select id="select-' + obejctive_mapping_id + '" class="form-control department_dropdown">' + departments_drop_down_list_html + '</select>';
                    var action = '<button id="delete-' + obejctive_mapping_id + '" onclick="remove_goal_objective.call(this)" class="btn btn-danger btn-xs"><?php echo $this->lang->line('common_remove');?></button>';
                    var weight_input = '<input value="' + value['weight'] + '" id="weight-' + obejctive_mapping_id + '" type="number" class="goal-objective-weight-input form-control" onkeyup="on_weight_change.call(this)"/> %';
                    app.goal_objectives_table.row.add([corporate_objective_description, weight_input, departments_dropdown, action]).draw(false);
                });
                stopLoad();
                $("#corporate_goal_modal").modal('show');
            }
        });


    }

    $('#add_corporate_objective').click(function () {
        if ($("input[name='objectiveCheckBox[]']:checked").length == 0) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_please_select_an_objective'); ?>');/*Please select an objective*/
        } else {
            $("input[name='objectiveCheckBox[]']:checked").each(function (index, value) {
                var common_id = makeid();
                var corporate_objective_id = $(this).data('corporate_objective_id');
                var corporate_objective_description = '<span class="goal_objective_description" id="' + common_id + '" data-corporate_objective_id="' + corporate_objective_id + '">' + $(this).data('corporate_objective_description') + '</span>';
                var departments_dropdown = '<select id="select-' + common_id + '" class="form-control department_dropdown">' + app.departments_drop_down_list_html + '</select>';
                var action = '<button id="delete-' + common_id + '" onclick="remove_goal_objective.call(this)" class="btn btn-danger btn-xs"><?php echo $this->lang->line('common_remove');?></button>';
                var weight_input = '<input value="0" id="weight-' + common_id + '" type="number" class="goal-objective-weight-input form-control" onkeyup="on_weight_change.call(this)"/> %';
                app.goal_objectives_table.row.add([corporate_objective_description, weight_input, departments_dropdown, action]).draw(false);
            });
            $('#goal_objectives_modal').modal('hide');
        }

    });

    function makeid() {
        length = 5;
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function on_weight_change() {
        var that = this;
        var total_weight = 0;
        var is_minus = false;
        $('.goal-objective-weight-input').each(function (index, value) {

            let weight_textbox_id = $(that).attr('id');
            let current_textbox_id = $(this).attr('id');

            if (weight_textbox_id == current_textbox_id) {
                if ($(this).val() < 0) {
                    show_error('total_weight_error', '<?php echo $this->lang->line('appraisal_value_must_be_greater_than_zero') ?>');/*Value must be greater than zero*/
                    is_minus = true;
                    return false;
                }
            }
            var current_value = $(this).val();
            if (current_value == "") {
                current_value = 0;
            }
            total_weight += parseFloat(current_value);
        });
        if (!is_minus) {

            $('#total_weight').text(total_weight);
            if (total_weight > 100) {
                show_error('total_weight_error', '<?php echo $this->lang->line('appraisal_total_weight_cannot_exceed_100') ?>');/*Total weight cannot exceed 100*/

            } else {
                hide_error('total_weight_error');
            }
        }
    }

    function date_range_validation() {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        app.is_date_range_valid = null;
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/is_date_range_valid'); ?>",
            data: {from_date: from_date, to_date: to_date, company_id: app.company_id},
            success: function (data) {
                if (data.status == "valid") {
                    app.is_date_range_valid = true;
                } else {
                    app.is_date_range_valid = false;
                }
            }
        });
        return app.is_date_range_valid;
    }

    function corporate_goal_form_validation() {
        var total_weight = 0;
        var is_minus = false;
        var is_valid = true;

        var narration = $("#narration").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();


        if (narration == "") {
            is_valid = false;
            show_error('narrationError', '<?php echo $this->lang->line('common_narration_is_required'); ?>');/*Narration is required*/
        } else {
            hide_error('narrationError');
        }

        if (from_date == "") {
            is_valid = false;
            show_error('from_date_error', '<?php echo $this->lang->line('common_date_is_required'); ?>');/*Date is required*/
        } else {
            hide_error('from_date_error');
        }

        if (to_date == "") {
            is_valid = false;
            show_error('to_date_error', '<?php echo $this->lang->line('common_date_is_required'); ?>');/*Date is required*/
        } else {
            hide_error('to_date_error');
        }

        // if (app.from_date != from_date || app.to_date != to_date) {
        //
        //     if (from_date != "" && to_date != "") {
        //         var is_date_range_valid = date_range_validation();
        //         if (is_date_range_valid) {
        //             hide_error('from_date_error');
        //         } else {
        //             is_valid = false;
        //             show_error('from_date_error', 'This period range is already used');
        //         }
        //     }
        // }

        var appraisal_type = $('input[name="goal_type"]:checked').val();
        if(appraisal_type=='performance_based' || appraisal_type=='both') {
            if (app.is_template_incomplete == 1) {
                is_valid = false;
                toastr.remove();
                myAlert('e', '<?php echo $this->lang->line('appraisal_softskill_template_is_incomplete'); ?>.');
            }
        }

        let fd = new Date(from_date);
        let td = new Date(to_date);
        if (fd.getTime() > td.getTime()) {
            is_valid = false;
            show_error('from_date_error', '<?php echo $this->lang->line('common_date_is_invalid'); ?>.');/*Date is invalid*/
        } else {
            //hide_error('from_date_error');
        }


        var appraisal_type = $('input[name="goal_type"]:checked').val();
        if (appraisal_type != "performance_based") {
            if ($('.goal_objective_description').length == 0 && app.is_confirmed == 1) {
                show_error('objectives_list_error', '<?php echo $this->lang->line('appraisal_objective_list_is_empty'); ?>');/*Objectives list is empty*/
                toastr.remove();
                myAlert('e', '<?php echo $this->lang->line('appraisal_objective_list_is_empty'); ?>');
                is_valid = false;
                return false;
            } else {
                hide_error('objectives_list_error');
            }

        }


        $('.goal-objective-weight-input').each(function (index, value) {

            if ($(this).val() < 1) {
                show_error('total_weight_error', '<?php echo $this->lang->line('appraisal_each_value_must_be_greater_than_zero'); ?>');/*Each value must be greater than zero*/
                is_minus = true;
                is_valid = false;

                return false;
            }
            var current_value = $(this).val();
            if (current_value == "") {
                current_value = 0;
            }
            total_weight += parseFloat(current_value);
        });

        if (!is_minus && app.is_confirmed == 1) {

            $('#total_weight').text(total_weight);
            if (appraisal_type != "performance_based") {
                if (total_weight != 100) {

                    is_valid = false;
                    show_error('total_weight_error', '<?php echo $this->lang->line('appraisal_total_weight_should_be_equal_100'); ?>');/*Total weight should be equal to 100*/

                } else {
                    hide_error('total_weight_error');
                }
            }
        }

        //objectives can repeat in the list but departments cannot repeat within the same objective.
        var obj_dept_validation_arr = [];
        $('.goal_objective_description').each(function (index, value) {
            console.info("objectives can repeat in the list but departments cannot repeat within the same objective.");
            let row_id = $(this).attr('id');
            let objective_id = $(this).data('corporate_objective_id');
            let dep_select_id = "#select-" + row_id;
            let dep_id = $(dep_select_id).val();
            let hash = "" + objective_id + dep_id;
            var n = obj_dept_validation_arr.includes(hash);
            if (n) {
                is_valid = false;
                show_error('total_weight_error', '<?php echo $this->lang->line('appraisal_objectives_cannot_repeat_within_same_department'); ?>.');/*Objectives cannot repeat within same department*/
            } else {
                obj_dept_validation_arr.push(hash);
            }

        });

        let is_dep_valid = true;
        $('.department_dropdown').each(function (index, value) {
            let department_dropdown_value = $(this).val();
            if (department_dropdown_value == 0) {
                is_valid = false;
                is_dep_valid = false;
            }
        });
        if (is_dep_valid == false) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_please_select_department'); ?>.');/*Please Select Department*/
        }

        return is_valid;
    }

    function remove_goal_objective() {
        var id_with_prefix = $(this).attr('id');
        var id_splitted_array = id_with_prefix.split('-');
        id = id_splitted_array[1];
        app.id_list_for_delete.push(id);
        app.goal_objectives_table.row($(this).parents('tr')).remove().draw();
    }

    $('#btn_add_objective').click(function () {
        $(".objective-checkbox").prop("checked", false);
        $('#goal_objectives_modal').modal('show');
    });


    function create_corporate_goal_btn_click() {
        corporate_goal_form_hide_errors();
        corporate_goal_form_reset_for_new_record();
        app.form_status = 'save';
        $("#corporate_goal_modal").modal('show');
    }

    function corporate_goal_form_reset_for_new_record() {
        app.goal_objectives_table.clear().draw();

        $('#narration').val("");
        $('#created_date').val("");
        $('#from_date').val("");
        $('#to_date').val("");
        $('#document_id').html("");
        $('#total_weight').html("0");
        $('#save_corporate_objective').show();
        $('.document_id').hide();
        $('.created_date_form_group').hide();

    }

    function corporate_goal_form_hide_errors() {
        hide_error('total_weight_error');
        hide_error('objectives_list_error');

        hide_error('narrationError');
        hide_error('from_date_error');
        hide_error('to_date_error');

    }

    function load_department_dropdown(company_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_departments'); ?>",
            data: {company_id: company_id},
            success: function (data) {
                var departments = "";
                departments += '<option value="0">Select Department</option>';
                data.forEach(function (item, index) {
                    departments += '<option value="' + item.department_master_id + '" data-department_id="' + item.department_master_id + '">' + item.department_description + '</option>';
                });
                app.departments_drop_down_list_html = departments;
                $('#departments_drop_down').html(departments);
            }
        });
    }

    function generated_dropdown_list_options_for_department(selected_value) {
        var departments_drop_down_list_html;
        var departments = "";
        var data = JSON.parse(localStorage.getItem("departments"));
        data.forEach(function (item, index) {
            var select_status = "";
            if (selected_value == item.department_master_id) {
                select_status = "selected";
            } else {
                select_status = "";
            }
            departments += '<option ' + select_status + ' value="' + item.department_master_id + '" data-department_id="' + item.department_master_id + '">' + item.department_description + '</option>';
        });
        departments_drop_down_list_html = departments;
        return departments_drop_down_list_html;
    }


    function load_corporate_objectives_to_select(company_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_corporate_objectives'); ?>",
            data: {company_id: company_id},
            success: function (data) {
                var objectives_list = "";
                data.forEach(function (item, index) {
                    objectives_list += '<div class="row">' +
                        '<div class="col-md-10"><div style="padding: 7px 0;"><span data-corporate_objective_id="' + item.corporate_objective_id + '">' + item.corporate_objective_description + '</span></div></div>' +
                        '<div class="col-md-2"><label class="customcheck"><input class="objective-checkbox" name="objectiveCheckBox[]" type="checkbox" data-corporate_objective_id="' + item.corporate_objective_id + '" data-corporate_objective_description="' + item.corporate_objective_description + '"/><span class="checkmark"></span>' +
                        '</label></div></div>';
                });
                if (objectives_list == "") {
                    let msg = '<div style="color: red;"><?php echo $this->lang->line('common_no_records_found'); ?></div>'/*common_no_records_found*/
                    $('#objectives_list_for_select').html(msg);
                } else {
                    $('#objectives_list_for_select').html(objectives_list);
                }

            }
        });
    }


    function load_softskills_templates_dropdown_read_only(selected_value = null, company_id) {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_templates'); ?>",
            data: {
                company_id: company_id
            },
            success: function (data) {
                var options = "";
                data.forEach(function (item, index) {
                    var select_status = "";
                    if (item.id == selected_value) {

                        select_status = "selected";
                    }
                    options += '<option ' + select_status + ' value="' + item.id + '">' + item.name + '</option>';
                });
                $("#soft_skills_templates_dropdown_read_only").html(options);
            }
        });
    }

    function load_softskills_templates_dropdown(selected_value = null, company_id) {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_templates'); ?>",
            data: {
                company_id: company_id
            },
            success: function (data) {
                var options = "";
                data.forEach(function (item, index) {
                    var select_status = "";
                    if (item.id == selected_value) {

                        select_status = "selected";
                    }
                    options += '<option ' + select_status + ' value="' + item.id + '">' + item.name + '</option>';
                });
                $("#soft_skills_templates_dropdown").html(options);
            }
        });
    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function format_for_two_digits(num) {
        if (num < 10) {
            return '0' + num;
        } else {
            return num;
        }
    }
</script>
