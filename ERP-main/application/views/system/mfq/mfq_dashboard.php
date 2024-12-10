<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$segment = fetch_mfq_segment(true, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/normalize.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/tabs.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/tabstyles.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/dhtmlxGantt/codebase/skins/dhtmlxgantt_broadway.css'); ?>" rel="stylesheet">
<script type="text/javascript"
        src="<?php echo base_url('plugins/TabStylesInspiration/js/modernizr.custom.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/TabStylesInspiration/js/cbpFWTabs.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/dhtmlxGantt/codebase/ext/dhtmlxgantt_tooltip.js'); ?>"></script>
<style>

    .panel.with-nav-tabs .panel-heading {
        padding: 5px 5px 0 5px;
    }

    .panel.with-nav-tabs .nav-tabs {
        border-bottom: none;
    }

    .panel.with-nav-tabs .nav-justified {
        margin-bottom: -1px;
    }

    /********************************************************************/
    /*** PANEL SUCCESS ***/
    .with-nav-tabs.panel-success .nav-tabs > li > a,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > .open > a,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
        background-color: white;
        border-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #3c763d;
        background-color: #fff;
        border-color: #d6e9c6;
        border-bottom-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
        background-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
        color: #fff;
        background-color: #3c763d;
    }

    .panel-success > .panel-heading {
        background-color: white;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a, .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover, .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #000000;
        background-color: #ecf0f5;
        border-color: #ecf0f5;
        border-bottom-color: transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }

    .pagination > li > a, .pagination > li > span {
        padding: 2px 8px;
    }

    .content-wrap section {
        text-align: left;
    }

    #tbl_machine th {
        text-transform: uppercase;
    }

    #tble_jobstatus th {
        text-transform: uppercase;
    }

    .bubble {
        text-align: center;
        padding-top: 30px;
    }

    .bubble_number {
        font-size: 36px;
        cursor: pointer;
    }

    .bubble_text {
        font-size: 21px;
    }
    .b-1:hover{
        border-color: #e59501;
        border-width: 5px;
        border-style: solid;
    }

    .b-2:hover{
        border-color: #b446e2;
        border-width: 5px;
        border-style: solid;
    }

    .b-3:hover{
        border-color: #0d9564cc;
        border-width: 5px;
        border-style: solid;
    }
</style>
<section>
    <div class="panel with-nav-tabs panel-success" style="border: none;">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#section-bar-1" data-toggle="tab" aria-expanded="true"><span><i
                                    class="fa fa-tachometer"></i>
                            <?php echo $this->lang->line('manufacturing_dashboard') ?><!--Dashboard--></span>
                    </a>
                </li>
                <li class="">
                    <a href="#section-bar-2" onclick="loadGantt()" data-toggle="tab"
                       aria-expanded="true"><span> <i class="fa fa-calendar"></i>
                            <?php echo $this->lang->line('manufacturing_production_calendar') ?><!--Production Calendar--></span>
                    </a>
                </li>
                <!--<li class="">
                    <a href="#section-bar-3" onclick="fetchOngoingJob()" data-toggle="tab"
                       aria-expanded="true"><span> <i class="fa fa-refresh" aria-hidden="true"></i>
                            <?php //echo $this->lang->line('manufacturing_ongoing_job') ?><!--Ongoing Job--><!--</span>
                    </a>
                </li>-->
                <li class="">
                    <a href="#section-bar-4" data-toggle="tab"
                       aria-expanded="true"><span> <i class="fa fa-refresh"
                                                      aria-hidden="true"></i> Customer Inquiry </span>
                    </a>
                </li>
                <!--<li class="pull-right">
                    <button type="button" data-text="Sync" id="" class="btn button-royal" onclick="PullDataFromErp()"><i class="fa fa-level-down" aria-hidden="true"></i> Pull data from ERP </button>&nbsp;
                    <button type="button" data-text="Sync" id="" class="btn button-royal" onclick="openErpWarehouse()"><i class="fa fa-level-down" aria-hidden="true"></i> Pull data from ERP Warehouse </button>&nbsp;
                    <button type="button" data-text="Sync" id="" class="btn button-royal" onclick="UpdateWacFromErp()"><i class="fa fa-level-down" aria-hidden="true"></i> Update WAC from ERP </button>
                </li>-->
            </ul>
        </div>
        <div class="panel-body" style="background-color: #ecf0f5;">
            <div class="tab-content">
                <div class="tab-pane active" id="section-bar-1">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        <?php echo $this->lang->line('manufacturing_machine') ?><!--MACHINES--></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <div class="table-responsive">
                                        <table id="tbl_machine" class="table table-striped table-condensed">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 2%">#</th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('manufacturing_machine_id') ?><!--MACHINE ID--></th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('manufacturing_job_no') ?><!--JOB NO--></th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('common_hours') ?><!--HOURS--></th>
                                                <th style="min-width: 3%">
                                                    <?php echo $this->lang->line('common_end_date') ?><!--END DATE--></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        <?php echo $this->lang->line('manufacturing_job_status') ?><!--JOB STATUS--></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <div class="table-responsive">
                                        <table id="tble_jobstatus" class="table table-striped table-condensed">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 2%">#</th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('manufacturing_job_id') ?><!--JOB ID--></th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('common_start_date') ?><!--START DATE--></th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('manufacturing_close_date') ?><!--CLOSE DATE--></th>
                                                <th style="min-width: 12%">
                                                    <?php echo $this->lang->line('common_narration') ?><!--NARRATION--></th>
                                                <th style="min-width: 3%">
                                                    <?php echo $this->lang->line('common_status') ?><!--STATUS--></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">Estimate Vs Actual Bar</h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <div id="estimate_vs_actual_bar_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        <?php echo $this->lang->line('manufacturing_awarded_job_status') ?><!-- Awarded Job Status --></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row" style="margin-left: 2%;">
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode"> <?php echo $this->lang->line('manufacturing_client'); ?>
                                            :</label><br> <!--Client-->
                                        <?php echo form_dropdown('filter_ajs_mfqCustomerAutoID[]', all_mfq_customer_drop(false), '', 'class="form-control filter" id="filter_ajs_mfqCustomerAutoID" multiple="multiple" onchange="generate_awarded_job_status()"'); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_segment'); ?>
                                            :</label><br><!--Segment-->
                                        <?php echo form_dropdown('filter_ajs_DepartmentID[]', $segment, '', 'class="form-control filter" id="filter_ajs_DepartmentID" multiple="multiple" onchange="generate_awarded_job_status()"'); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode">Up
                                            to <?php echo $this->lang->line('common_date'); ?> :</label> <br>
                                        <!--Segment-->
                                        <input type="text" name="filter_ajs_date"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16"
                                               value="<?php echo $current_date; ?>" id="filter_ajs_date"
                                               class="input-small datepic" onchange="generate_awarded_job_status()">
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <div id="awarded_job_status_view"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        <?php echo $this->lang->line('manufacturing_estimated_job_return') ?><!-- Estimated Job Return --></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row" style="margin-left: 2%;">
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode"> <?php echo $this->lang->line('manufacturing_client'); ?>
                                            :</label><br> <!--Client-->
                                        <?php echo form_dropdown('filter_ejr_mfqCustomerAutoID[]', all_mfq_customer_drop(false), '', 'class="form-control filter" id="filter_ejr_mfqCustomerAutoID" multiple="multiple" onchange="generate_estimated_job_return()"'); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_start_date'); ?>
                                            :</label> <br><!--Start Date-->
                                        <input type="text" name="filter_ejr_date_from"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16"
                                               value="" id="filter_ejr_date_from" class="input-small datepic_ejr"
                                               onchange="generate_estimated_job_return()">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_end_date'); ?>
                                            :</label> <br><!--End Date-->
                                        <input type="text" name="filter_ejr_date_to"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16"
                                               value="" id="filter_ejr_date_to" class="input-small datepic_ejr"
                                               onchange="generate_estimated_job_return()">
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1" data-toggle="tab" onclick="changeTab(1)"
                                                              aria-expanded="true">Planned Job</a></li>
                                        <li class=""><a href="#tab_2" data-toggle="tab" onclick="changeTab(2)"
                                                        aria-expanded="true">Actual Job</a></li>
                                    </ul>
                                    <div class="tab-pane active" id="tab_1" style="overflow: auto;">
                                        <div id="planned_job_return_view"></div>
                                    </div>
                                    <div class="tab-pane hide" id="tab_2" style="overflow: auto;">
                                        <div id="actual_job_return_view"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        <?php echo $this->lang->line('manufacturing_jobs') ?><!--JOBS--></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%">
                                    <div class="table-responsive">
                                        <table id="tbl_jobs"
                                               class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 12%" class="text-uppercase">
                                                    <?php echo $this->lang->line('common_status') ?><!--STATUS--></th>
                                                <th style="min-width: 12%">JAN - <?php echo date('Y') ?></th>
                                                <th style="min-width: 12%">FEB - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">MAR - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">APR - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">MAY - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">JUN - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">JUL - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">AUG - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">SEP - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">OCT - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">NOV - <?php echo date('Y') ?></th>
                                                <th style="min-width: 3%">DEC - <?php echo date('Y') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $ci =& get_instance();
                                            $ci->load->model('MFQ_Dashboard_modal');
                                            $result = $ci->MFQ_Dashboard_modal->fetch_jobs();
                                            if ($result) {
                                                foreach ($result as $val) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $val["description"] ?></td>
                                                        <td class="text-center"><?php echo $val["jan"] == null ? 0 : $val["jan"]; ?></td>
                                                        <td class="text-center"><?php echo $val["feb"] == null ? 0 : $val["feb"]; ?></td>
                                                        <td class="text-center"><?php echo $val["mar"] == null ? 0 : $val["mar"]; ?></td>
                                                        <td class="text-center"><?php echo $val["apr"] == null ? 0 : $val["apr"]; ?></td>
                                                        <td class="text-center"><?php echo $val["may"] == null ? 0 : $val["may"]; ?></td>
                                                        <td class="text-center"><?php echo $val["jun"] == null ? 0 : $val["jun"]; ?></td>
                                                        <td class="text-center"><?php echo $val["jul"] == null ? 0 : $val["jul"]; ?></td>
                                                        <td class="text-center"><?php echo $val["aug"] == null ? 0 : $val["aug"]; ?></td>
                                                        <td class="text-center"><?php echo $val["sept"] == null ? 0 : $val["sept"]; ?></td>
                                                        <td class="text-center"><?php echo $val["oct"] == null ? 0 : $val["oct"]; ?></td>
                                                        <td class="text-center"><?php echo $val["nov"] == null ? 0 : $val["nov"]; ?></td>
                                                        <td class="text-center"><?php echo $val["dece"] == null ? 0 : $val["dece"]; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="section-bar-2">
                    <label for="scale1" class="radio-inline"><input type="radio" id="scale1" name="scale" value="1"/>
                        <?php echo $this->lang->line('manufacturing_day_scale') ?><!--Day scale--></label>
                    <label for="scale2" class="radio-inline"><input type="radio" id="scale2" name="scale" value="2"/>
                        <?php echo $this->lang->line('manufacturing_week_scale') ?><!--Week scale--></label>
                    <label for="scale3" class="radio-inline"><input type="radio" id="scale3" name="scale" value="3"
                                                                    checked/>
                        <?php echo $this->lang->line('manufacturing_month_scale') ?><!--Month scale--></label>
                    <div class="row">
                        <div id="gantt_here" style='width:100%; height:100%'></div>
                    </div>
                </div>


                <div class="tab-pane" id="section-bar-3">

                    <div class="col-sm-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h4 class="box-title text-uppercase">ongoing job</h4>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body" id="" style="display: block;width: 100%">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>WIP Total : <span id='wipTotal'>0.000</span>
                                            (<?php echo $this->common_data['company_data']['company_default_currency']; ?>
                                            )</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>BOM Total : <span id='bomTotal'>0.000</span>
                                            (<?php echo $this->common_data['company_data']['company_default_currency']; ?>
                                            )</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Estimate Total : <span id='esTotal'>0.000</span>
                                            (<?php echo $this->common_data['company_data']['company_default_currency']; ?>
                                            )</strong>
                                    </div>
                                    <div class="col-md-3 pull-right">
                                        <a href="<?php echo site_url('MFQ_Dashboard/ongoing_job_excel'); ?>"
                                           type="button" class="btn btn-success btn-sm pull-right">
                                            <i class="fa fa-file-excel-o"></i>
                                            <?php echo $this->lang->line('manufacturing_excel') ?><!--Excel-->
                                        </a>
                                    </div>
                                </div>
                                <br>
                                <div class="row" style="margin: 3px;">
                                    <div class="table-responsive">
                                        <table id="tbl_ongoin_job" class="<?php echo table_class(); ?>">
                                            <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('common_date') ?><!--DATE--></th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('manufacturing_job_no') ?><!--JOB NO--></th>
                                                <th class="text-uppercase" style="width: auto;">
                                                    <?php echo $this->lang->line('manufacturing_division') ?><!--DIVISION--></th>
                                                <th class="text-uppercase" style="width: auto;">
                                                    <?php echo $this->lang->line('manufacturing_job_description') ?><!--JOB DESCRIPTION--></th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('manufacturing_client_name') ?><!--CLIENT NAME--></th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('common_qty') ?><!--QTY--></th>
                                                <th class="text-uppercase" style="width: auto">Curency</th>
                                                <th class="text-uppercase" style="width: auto">BOM Cost</th>
                                                <th class="text-uppercase" style="width: auto">WIP/Cost</th>
                                                <th class="text-uppercase" style="width: auto">Estimated Selling Price
                                                </th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('manufacturing_quote_reference') ?><!--QUOTE REF--></th>
                                                <th class="text-uppercase" style="width: auto">
                                                    <?php echo $this->lang->line('manufacturing_job_completion') ?><!--JOB COMPLETION--></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="tab-pane" id="section-bar-4">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        QUOTATIONS<!-- Awarded Job Status --></h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%;min-height: 557px;">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-6">
                                            <label>Department</label>
                                            <div>
                                                <?php echo form_dropdown('quotationDepartmentID[]', $segment, '', 'class="form-control filter" onchange="loadQuotationsWidget()" id="quotationDepartmentID" multiple="multiple" '); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6">
                                            <label for="supplierPrimaryCode">As of Date :</label> <br>
                                            <!--Segment-->
                                            <input type="text" name="quotationDate"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   size="16"
                                                   value="<?php echo $current_date; ?>" id="quotationDate"
                                                   class="input-small quotation-datepic">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="bubble b-2"
                                             style="background-color: #b446e2c7;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 67px;
    left: 354px;">
                                            <div>
                                                <span class="bubble_number" id="totalConvertion"></span>
                                                <div class="bubble_text">Total<br>Convertion<br>%</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-1"
                                             style="background-color: #e59501c4;;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;top: 26px;
    left: 36px;">
                                            <div>
                                                <span class="bubble_number" id="totalSubmitted" onclick="submittedDrilldown()"></span>
                                                <div class="bubble_text">Total<br>Submitted</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-3"
                                             style="background-color: #0d9564cc;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 225px;
    left: 132px;">
                                            <div>
                                                <span class="bubble_number" id="totalAwarded" onclick="awardedDrilldown()"></span>
                                                <div class="bubble_text">Total<br>Awarded</div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        AWARDED QUOTATIONS</h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%;min-height: 557px;">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <label for="supplierPrimaryCode">Month-Year :</label> <br>
                                            <!--Segment-->
                                            <input type="text" name="awardedDate"
                                                   size="16"
                                                   value="<?php echo $current_date; ?>" id="awardedDate"
                                                   class="input-small awarded-datepic">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="bubble b-2"
                                             style="background-color: #b446e2c7;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 145px;
    left: 354px;">
                                            <div>
                                                <span class="bubble_number" id="change"></span>
                                                <div class="bubble_text">Change %</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-1"
                                             style="background-color: #e59501c4;;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;top: 124px;
    left: 36px;">
                                            <div>
                                                <span class="bubble_number" id="currentMonth" onclick="currentMonthDrilldown()"></span>
                                                <div class="bubble_text">Current<br>Month</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-3"
                                             style="background-color: #0d9564cc;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 333px;
    left: 132px;">
                                            <div>
                                                <span class="bubble_number" id="previousMonth" onclick="previousMonthDrilldown()"></span>
                                                <div class="bubble_text">Previous<br>Month</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title text-uppercase">
                                        JOB DELIVERY</h4>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" id="" style="display: block;width: 100%;min-height: 557px;">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-6">
                                            <label>Department</label>
                                            <div>
                                                <?php echo form_dropdown('deliveryDepartmentID[]', $segment, '', 'class="form-control filter" onchange="loadDeliveryWidget()" id="deliveryDepartmentID" multiple="multiple" '); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6">
                                            <label for="supplierPrimaryCode">As of Date :</label> <br>
                                            <!--Segment-->
                                            <input type="text" name=""
                                                   size="16"
                                                   value="<?php echo $current_date; ?>" id="deliveryDate"
                                                   class="input-small delivery-datepic">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="bubble b-2"
                                             style="background-color: #b446e2c7;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 67px;
    left: 354px;">
                                            <div>
                                                <span class="bubble_number" id="onTime"></span>
                                                <div class="bubble_text">On Time %</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-1"
                                             style="background-color: #e59501c4;;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;top: 26px;
    left: 36px;">
                                            <div>
                                                <span class="bubble_number" id="expected" onclick="expected_drilldown()"></span>
                                                <div class="bubble_text">Expected</div>
                                            </div>
                                        </div>
                                        <div class="bubble b-3"
                                             style="background-color: #0d9564cc;width: 200px;height: 200px;border-radius: 50%;text-align: center;position: absolute;    top: 225px;
    left: 132px;">
                                            <div>
                                                <span class="bubble_number" id="actuals" onclick="actuals_drilldown()"></span>
                                                <div class="bubble_text">Actuals</div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="tabs tabs-style-bar">
            <nav>
                <ul>
                    <li><a href="#section-bar-1" class="fa fa-tachometer"> <span>Dashboard</span></a></li>
                    <li><a href="#section-bar-2" class="fa fa-calendar" onclick="loadGantt()">
                            <span>Production Calendar</span></a></li>
                </ul>
            </nav>
            <div class="content-wrap">
                <section id="section-bar-1">


                </section>
                <section id="section-bar-2">

                </section>
            </div><!-- /content -->
        <!-- </div> /tabs -->
        <div class="modal fade" id="erp_warehouse_modal" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 50%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="">Warehouse</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Warehouse </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                            <?php echo form_dropdown('warehouseAutoID', array("" => "Select"), "", 'class="form-control select2" id="warehoueAutoID"'); ?>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onClick="PullDataFromErpWarehouse()">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="quotationDrilldownModal" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 50%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="drilldownTitle">QUOTATIONS</h4>
                    </div>
                    <div class="modal-body">
                        <div class="pull-right">
                            <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="mfq_dashboard.xls"
                                    onclick="var file = tableToExcel('quotationDrilldownTable', 'Manufacturing Dashboard'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="quotationDrilldownTable"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="awarded_job_drilldown_modal" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 50%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="drilldownTitle_awarded"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="pull-right">
                            <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="mfq_dashboard.xls"
                                    onclick="var file = tableToExcel('awarded_job_drilldown_Table', 'Manufacturing Dashboard'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="awarded_job_drilldown_Table"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
</section>
<script>
    $(document).ready(function () {

        changeTab(1);
        /*[].slice.call(document.querySelectorAll('.tabs')).forEach(function (el) {
            new CBPFWTabs(el);
        });*/
        machine_table();
        job_status_table();
        generate_estimated_job_return();
        generate_awarded_job_status();
        generate_estimate_vs_actual_barchart();
        $('.select2').select2();

        $('#filter_ajs_DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_ajs_DepartmentID").multiselect2('selectAll', false);
        $("#filter_ajs_DepartmentID").multiselect2('updateButtonText');

        $('#filter_ajs_mfqCustomerAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_ajs_mfqCustomerAutoID").multiselect2('selectAll', false);
        $("#filter_ajs_mfqCustomerAutoID").multiselect2('updateButtonText');

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            generate_awarded_job_status();
        });

        $('.quotation-datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            loadQuotationsWidget()
        });

        $('.awarded-datepic').datetimepicker({
            useCurrent: false,
            format: 'MM-YYYY',
        }).on('dp.change', function (ev) {
            loadAwardedWidget();
        });

        $('.delivery-datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            loadDeliveryWidget();
        });

        $('#filter_ejr_DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_ejr_DepartmentID").multiselect2('selectAll', false);
        $("#filter_ejr_DepartmentID").multiselect2('updateButtonText');


        $('#quotationDepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#quotationDepartmentID").multiselect2('selectAll', false);
        $("#quotationDepartmentID").multiselect2('updateButtonText');

        $('#deliveryDepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#deliveryDepartmentID").multiselect2('selectAll', false);
        $("#deliveryDepartmentID").multiselect2('updateButtonText');

        $('#filter_ejr_mfqCustomerAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_ejr_mfqCustomerAutoID").multiselect2('selectAll', false);
        $("#filter_ejr_mfqCustomerAutoID").multiselect2('updateButtonText');

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic_ejr').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            generate_estimated_job_return();
        });

        loadQuotationsWidget();
        var d = new Date();
        var m = d.getMonth()+1;
        var monthYear = (m<10)?('0'+m):m;
        monthYear += '-'+d.getFullYear();
        $("#awardedDate").val(monthYear);
        loadAwardedWidget();
        loadDeliveryWidget();
    });

    function loadQuotationsWidget() {
        var department = $("#quotationDepartmentID").val();
        var date = $("#quotationDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/load_quotation_widget'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#totalSubmitted").text(data.totalSubmitted);
                $("#totalAwarded").text(data.totalAwarded);
                var convertion = 0;
                if(data.totalAwarded!=0 && data.totalSubmitted!=0){
                     convertion =  (data.totalAwarded/data.totalSubmitted)*100;
                }
                $("#totalConvertion").text(convertion.toFixed(2));
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function loadAwardedWidget() {

        var date = $("#awardedDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {date: date},
            url: "<?php echo site_url('MFQ_Dashboard/load_awarded_widget'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#currentMonth").text(data.currentMonth);
                $("#previousMonth").text(data.previousMonth);
                var change = 0;
                if(data.currentMonth!=0 && data.previousMonth!=0){
                    // change =  (data.previousMonth/data.currentMonth)*100;
                    change =  ((data.currentMonth - data.previousMonth) / data.previousMonth) * 100;
                }
                $("#change").text(change.toFixed(2));
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function loadDeliveryWidget() {
        var department = $("#deliveryDepartmentID").val();
        var date = $("#deliveryDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/load_delivery_widget'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#expected").text(data.expected);
                $("#actuals").text(data.actuals);
                var ontime = 0;
                if(data.expected!=0 && data.actuals!=0){
                    ontime = (data.actuals/data.expected)*100;
                }
                $("#onTime").text(ontime.toFixed(2));
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function job_status_table() {
        oTable = $('#tble_jobstatus').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Dashboard/fetch_job_status'); ?>",
            //"aaSorting": [[1, 'desc']],
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 5,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $("[rel='tooltip']").tooltip();
            },
            "aoColumns": [
                {"mData": "workProcessID"},
                {"mData": "documentCode"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "description"},
                {"mData": "percentage"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function machine_table() {
        oTable = $('#tbl_machine').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Dashboard/fetch_machine'); ?>",
            //"aaSorting": [[1, 'desc']],
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 5,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "mfq_faID"},
                {"mData": "faCode"},
                {"mData": "documentCode"},
                {"mData": "hoursSpent"},
                {"mData": "endDate"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function setScaleConfig(value) {
        switch (value) {
            case "1":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.subscales = [{unit: "day", step: 1, date: "%d, %M"}];
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 60;
                gantt.templates.date_scale = null;
                break;
            case "2":
                var weekScaleTemplate = function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M, %Y");
                    var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                };
                gantt.config.scale_unit = "year";
                gantt.config.date_scale = "%Y";
                gantt.config.step = 1;
                gantt.config.subscales = [
                    {unit: "week", step: 1, date: "%d, %M"}
                ];
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 60;
                break;
            case "3":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;


                gantt.config.subscales = [
                    {unit: "month", step: 1, date: "%M"}
                ];
                break;
        }
    }

    function loadGantt() {

        setTimeout(function () {
            gantt.config.autosize = true;
            var demo_tasks = {
                /*"data":[
                 {"id":1, "text":"Project #1", "start_date":"28-03-2013", "duration":"11", "progress": 0.6},
                 {"id":2, "text":"Project #2", "start_date":"01-04-2013", "duration":"18", "progress": 0.4}
                 ]*/
                "data": <?php fetch_ongoing_jobs() ?>
            };

            /* gantt.config.scale_unit = "month";
             gantt.config.step = 1;
             gantt.config.date_scale = "%F, %Y";*/
            gantt.config.readonly = true;
            gantt.config.min_column_width = 50;
            gantt.config.scale_height = 90;
            /*gantt.config.drag_move = false;
             gantt.config.drag_links = false;
             gantt.config.drag_highlight = false;
             gantt.config.drag_progress = false;*/
            gantt.config.container_autoresize = true;
            gantt.config.row_height = 30;

            /*gantt.config.subscales = [
             {unit: "day", step: 1, date: "%j, %D"}
             ];*/

            gantt.config.columns = [
                {name: "text", label: "Job No", align: "left", tree: false, width: 150}
                /*{name:"start_date",label:"Description",  align: "left", tree:false }*/
            ];
            gantt.config.details_on_dblclick = false;
            gantt.templates.tooltip_text = function (start, end, task) {
                return "<div style='text-align: left;margin-bottom: 0px'><b>Task:</b> " + task.text + "</div>" +
                    "<div style='text-align: left'><b>Start date:</b> " + gantt.templates.tooltip_date_format(start) +
                    "</div>" +
                    "<div style='text-align: left'><b>End date:</b> " + gantt.templates.tooltip_date_format(end) + "</div><div style='text-align: left'><b>Description:</b> " + task.description + "</div>";
            };

            setScaleConfig('3');
            gantt.init("gantt_here");
            gantt.parse(demo_tasks);

            var func = function (e) {
                e = e || window.event;
                var el = e.target || e.srcElement;
                var value = el.value;
                setScaleConfig(value);
                gantt.render();
            };

            var els = document.getElementsByName("scale");
            for (var i = 0; i < els.length; i++) {
                els[i].onclick = func;
            }
        }, 100);
    }

    function fetchOngoingJob() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            // data: ,
            url: "<?php echo site_url('MFQ_Dashboard/ongoing_job_wip_total'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#wipTotal').text(data['amount']);
                $('#bomTotal').text(data['BOMCost']);
                $('#esTotal').text(data['estimateValue']);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });


        oTable = $('#tbl_ongoin_job').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            /*"bStateSave": true,*/

            "sAjaxSource": "<?php echo site_url('MFQ_Dashboard/fetch_ongoing_job'); ?>",
            "aaSorting": [[0, 'desc']],
            "lengthMenu": [[20, 50, 100], [20, 50, 100]],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [{"searchable": false, "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]}],
            "aoColumns": [
                {"mData": "workProcessID"},
                {"mData": "documentDate"},
                {"mData": "documentCode"},
                {"mData": "segment"},
                {"mData": "description"},
                {"mData": "CustomerName"},
                {"mData": "qty"},
                {"mData": "currencycode"},
                {"mData": "BOMAmount"},
                {"mData": "amount"},
                {"mData": "amount_estimated"},
                {"mData": "estimateCode"},
                {"mData": "percentage"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function PullDataFromErp() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('MFQ_Dashboard/pull_from_erp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function openErpWarehouse() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('MFQ_Dashboard/load_erp_warehouse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#warehoueAutoID').empty();
                var mySelect = $('#warehoueAutoID');
                mySelect.append($('<option></option>').val("").html("Select"));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['wareHouseAutoID']).html(text['wareHouseDescription']));
                    });
                }
                $("#erp_warehouse_modal").modal();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function PullDataFromErpWarehouse() {
        if ($('#warehoueAutoID').val() != "") {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {warehouseAutoID: $('#warehoueAutoID').val()},
                url: "<?php echo site_url('MFQ_Dashboard/pull_from_erp_warehouse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $("#erp_warehouse_modal").modal('hide');
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        } else {
            myAlert('e', "Please select a warehouse");
        }
    }

    function UpdateWacFromErp() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('MFQ_Dashboard/update_wac_from_erp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function generate_estimated_job_return() {
        var clientID = $('#filter_ejr_mfqCustomerAutoID').val();
        var segmentID = $('#filter_ejr_DepartmentID').val();
        var dateTo = $('#filter_ejr_date_to').val();
        var dateFrom = $('#filter_ejr_date_from').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'clientID': clientID, 'segmentID': segmentID, 'dateTo': dateTo, 'dateFrom': dateFrom},
            url: "<?php echo site_url('MFQ_Dashboard/planned_job_return'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                let plannedDelivery = 0;
                let actualDelivery = 0;
                if(data){
                    plannedDelivery = data['plannedDelivery'];
                    actualDelivery = data['actualDelivery'];
                }
                Highcharts.chart('planned_job_return_view', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: ''},
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}%</b><br>Count : <b>{point.count}</b><br>Value : <b>{point.SegmentValue}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Percentage',
                        data: plannedDelivery,
                        point:{
                            events:{
                                click: function (event) {
                                    planned_job_return_drill_down(this.segmentID, 1);
                                }
                            }
                        }
                    }]
                });

                Highcharts.chart('actual_job_return_view', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: ''},
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}%</b><br>Count : <b>{point.count}</b><br>Value : <b>{point.SegmentValue}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Percentage',
                        data: actualDelivery,
                        point:{
                            events:{
                                click: function (event) {
                                    planned_job_return_drill_down(this.segmentID, 2);
                                }
                            }
                        }
                    }]
                });
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function planned_job_return_drill_down(segment, type=1)
    {
        if(segment != '')
        {
            var clientID = $('#filter_ejr_mfqCustomerAutoID').val();
            var segmentID = $('#filter_ejr_DepartmentID').val();
            var dateTo = $('#filter_ejr_date_to').val();
            var dateFrom = $('#filter_ejr_date_from').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'segment':segment, 'clientID': clientID, 'dateTo': dateTo, 'dateFrom': dateFrom, 'type' : type},
                url: "<?php echo site_url('MFQ_Dashboard/planned_job_return_drill_down'); ?>",
                beforeSend: function () {
                },
                success: function (data) {
                     $("#drilldownTitle_awarded").text('Planned Job Return');
                     $("#awarded_job_drilldown_Table").html(data);
                     $("#awarded_job_drilldown_modal").modal('show');

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }
    
    function generate_awarded_job_status() {
        var clientID = $('#filter_ajs_mfqCustomerAutoID').val();
        var segmentID = $('#filter_ajs_DepartmentID').val();
        var date = $('#filter_ajs_date').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'clientID': clientID, 'segmentID': segmentID, 'date': date},
            url: "<?php echo site_url('MFQ_Dashboard/awarded_job_status'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                Highcharts.chart('awarded_job_status_view', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: ''},
                    tooltip: {
                        pointFormat: 'Count: <b>{point.count}</b> <br>Percentage: <b>{point.percen}%</b> <br>Value: <b>{point.value}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Data',
                        data: data,
                        point:{
                            events:{
                                click: function (event) {
                                    awarded_job_drill_down(this.name);
                                }
                            }
                        }      
                    }]
                });
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function awarded_job_drill_down(awardedType)
    {
        if(awardedType != '')
        {
            var clientID = $('#filter_ajs_mfqCustomerAutoID').val();
            var segmentID = $('#filter_ajs_DepartmentID').val();
            var date = $('#filter_ajs_date').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'awardedType':awardedType, 'clientID': clientID, 'segmentID': segmentID, 'date': date},
                url: "<?php echo site_url('MFQ_Dashboard/awarded_job_drill_down'); ?>",
                beforeSend: function () {
                },
                success: function (data) {
                     $("#drilldownTitle_awarded").text(awardedType);
                     $("#awarded_job_drilldown_Table").html(data);
                     $("#awarded_job_drilldown_modal").modal('show');

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function changeTab(tabID) {
        if (tabID == 1) {
            $('#tab_2').addClass('hide');
            $('#tab_1').removeClass('hide');
        } else {
            $('#tab_2').removeClass('hide');
            $('#tab_1').addClass('hide');
        }
    }

    function generate_estimate_vs_actual_barchart() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('MFQ_Dashboard/estimate_vs_actual_job'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                Highcharts.chart('estimate_vs_actual_bar_chart', {
                    chart: {
                        type: 'column'
                    },

                    title: {text: ''},

                    xAxis: {
                        categories: ['Jan-<?php echo date('Y') ?>', 'Feb-<?php echo date('Y') ?>', 'Mar-<?php echo date('Y') ?>', 'Apr-<?php echo date('Y') ?>', 'May-<?php echo date('Y') ?>', 'Jun-<?php echo date('Y') ?>', 'Jul-<?php echo date('Y') ?>', 'Aug-<?php echo date('Y') ?>', 'Sep-<?php echo date('Y') ?>', 'Oct-<?php echo date('Y') ?>', 'Nov-<?php echo date('Y') ?>', 'Dec-<?php echo date('Y') ?>']
                    },

                    yAxis: {
                        allowDecimals: false,
                        min: 0,
                        title: {
                            text: 'Amount (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)'
                        }
                    },

                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b>' + '<br/>' +
                                this.series.name + '(<?php echo $this->common_data['company_data']['company_default_currency']; ?>) : ' + this.y + '<br/>';
                        }
                    },

                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        },
                        series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    actual_drilldown(this.x, this.series.name);
                                }
                            }
                        }
                    }
                    },
                    series:data
                });
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
        // estimate_vs_actual_bar_chart
    }

    function actual_drilldown(category, type) 
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {category: category, type: type},
            url: "<?php echo site_url('MFQ_Dashboard/actual_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("QUOTATIONS");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function submittedDrilldown(){
        var department = $("#quotationDepartmentID").val();
        var date = $("#quotationDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/quotation_submitted_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("QUOTATIONS");
                     $("#quotationDrilldownTable").html(data);
                     $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function awardedDrilldown(){
        var department = $("#quotationDepartmentID").val();
        var date = $("#quotationDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/quotation_awarded_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("QUOTATIONS");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function currentMonthDrilldown(){
        var date = $("#awardedDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {date: date},
            url: "<?php echo site_url('MFQ_Dashboard/current_month_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("AWARDED QUOTATIONS");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function previousMonthDrilldown(){
        var date = $("#awardedDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {date: date},
            url: "<?php echo site_url('MFQ_Dashboard/previous_month_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("AWARDED QUOTATIONS");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function actuals_drilldown(){
        var department = $("#deliveryDepartmentID").val();
        var date = $("#deliveryDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/actuals_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("JOB DELIVERY");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function expected_drilldown(){
        var department = $("#deliveryDepartmentID").val();
        var date = $("#deliveryDate").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'text',
            data: {department: department, date: date},
            url: "<?php echo site_url('MFQ_Dashboard/expected_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("JOB DELIVERY");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }



</script>