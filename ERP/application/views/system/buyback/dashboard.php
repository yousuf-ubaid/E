<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('buyback_helper');

$yearfilter = load_yearfilter_dashboard();
$farmer = load_all_farms(FALSE);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$location_arr = load_all_locations(false);
?>
    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">

    <link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
    <link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>

    <script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
    <style>

        .pagination > li > a, .pagination > li > span {
            padding: 2px 8px;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .panel.with-nav-tabs .panel-heading {
            padding: 5px 5px 0 5px;
        }

        .panel.with-nav-tabs .mainpanel {
            border-bottom: none;
        }

        .panel.with-nav-tabs .nav-justified {
            margin-bottom: -1px;
        }

        /********************************************************************/

        .panel-success > .panel-heading {
            background-color: white;
        }

        .with-nav-tabs.panel-success .mainpanel > li.active > a, .with-nav-tabs.panel-success .mainpanel > li.active > a:hover, .with-nav-tabs.panel-success .mainpanel > li.active > a:focus {
            color: #000000;
            background-color: #ecf0f5;
            border-color: #ecf0f5;
            border-bottom-color: transparent;
        }

        .pagination > li > a, .pagination > li > span {
            padding: 2px 8px;
        }

        .r-icon-stats {
            text-align: center;
        }

        .r-icon-stats i {
            width: 66px;
            height: 66px;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
            display: inline-block;
            border-radius: 100%;
            vertical-align: top;
            background: #01c0c8;
        }

        .r-icon-stats .bodystate {
            padding-left: 20px;
            display: inline-block;
            vertical-align: middle;
        }

        .r-icon-stats .bodystate h4 {
            margin-bottom: 0px;
            font-size: 25px;
            font-weight: 800;
        }
        .white-box .box-title {
            margin: 0px 0px 12px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
        }

        .fullBody{
            background-color: #ffffff;
        }

        .batchIcon{
            background-color: #00a65a;
        }
        .batchBody {
            background-color: #ffffff;
        }

        .WidgetNo{
            color: #adadad;
        }
        .theme{
           /*-webkit-appearance: normal;*/
            border: none;
            background-color: inherit;
            color: black;
            -webkit-border-radius: 10px;
        }
        .theme:hover {
            background-color: #ededed;
        }


        #calendar a.fc-event {
            color: #ff4277; /* bootstrap default styles make it black. undo */
        }

    </style>

   <section class="content" id="ajax_body_container" >
        <div id="dashboard_content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-success" style="border: none;">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs mainpanel">
                                <li class="active">
                                    <a class="buybackTab" onclick="" id="" data-id="0"
                                       href="#buyback_dashboard" data-toggle="tab"
                                       aria-expanded="true"><span><i class="fa fa-tachometer tachometerColor" aria-hidden="true"
                                                                     style="color: #50749f;font-size: 16px;"></i>&nbsp;Buyback</span></a>
                                </li>
                                <li class="">
                                    <a class="buybackTab" onclick="" id="" data-id="0"
                                       href="#buyback_dash_Calender" data-toggle="tab"
                                       aria-expanded="true"><span><i class="fa fa-calendar tachometerColor" aria-hidden="true"
                                                                     style="color: #50749f;font-size: 16px;"></i>&nbsp;Calendar</span></a>
                                </li>
                                <li class="">
                                    <a class="buybackTab" onclick="" id="" data-id="0"
                                       href="#buyback_dash_Todo" data-toggle="tab"
                                       aria-expanded="true"><span><i class="fa fa-check-square tachometerColor" aria-hidden="true"
                                                                     style="color: #50749f;font-size: 16px;"></i>&nbsp;To Do
                                        </span><span class="badge badge-pill" style="background-color:#de84f3; padding-bottom: 5px" id="pending"></span></a>
                                </li>
                            </ul>
                        </div>

                        <div class="panel-body bodyBorder" style="background-color: #ecf0f5; box-shadow: 0px 2px 2px 0px #807979" >
                            <div class="tab-content">
                                <div class="tab-pane active buyback_dashboard" id="buyback_dashboard">
                                    <div class="fullBody">
                                        <div class="box-header with-border">
                                            <div class="row" style="margin-top: 5px; margin-bottom: 5px">
                                                <div class="col-md-12" id="">
                                                    <div class="col-sm-7">
                                                        <h4 class="box-title">Dashboard</h4>
                                                    </div>
                                                    <div class="col-sm-1" style="width: 100px">
                                                        <?php // echo form_dropdown('theme', array('1' => 'Default', '2' => 'Dark'), '', 'class="form-control select2 theme" id="theme" onchange="buybackDashboardChangeTheme(this)"'); ?>
                                                    </div>
                                                    <div class="col-sm-1" style="width: 100px">
                                                        <?php echo form_dropdown('companyFinanceYearID', $yearfilter, '', ' class="form-control theme" id="companyFinanceYearID" onchange="fetch_finance_year_period(this.value)"'); ?>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <?php echo form_dropdown('financeyear_period', array('' => 'Period'), '', 'class="form-control theme" id="financeyear_period" onchange="buybackDashboard_Data()"'); ?>                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <br>
                                            <div class="row">
                                                </div>
                                                <div class="col-md-3 col-sm-6 col-xs-12" style="cursor: pointer;">
                                                    <div class="info-box batchBody" onclick="Farm_View_model()">
                                                        <span class="info-box-icon batchIcon" style="background-color: #685D79"><i class="fa fa-home" style="color: #eee9e9"></i></span>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Farms</span>
                                                            <span class="info-box-number WidgetNo farmWeiget" id="total_active_farms" style="font-size: 35px; text-align: center">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-6 col-xs-12" style="cursor: pointer;">
                                                    <div class="info-box batchBody" onclick="BatchView_model()">
                                                        <span class="info-box-icon batchIcon" style="background-color: #AB6C82"><i class="fa fa-clipboard" aria-hidden="true" style="color: #eee9e9"></i></i></span>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Ongoing Batches</span>
                                                            <span class="info-box-number WidgetNo" id="total_active_batches" style="font-size: 35px; text-align: center">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-6 col-xs-12" style="cursor: pointer;">
                                                    <div class="info-box batchBody" onclick="ProfitView_model()">
                                                        <span class="info-box-icon batchIcon" style="background-color: #D8737F"><i class="fa fa-level-up" style="color: #eee9e9"></i></span>

                                                        <div class="info-box-content">
                                                           <span class="info-box-text">Profit Batches</span>
                                                            <span class="info-box-number WidgetNo" id="total_profit_batches" style="font-size: 35px; text-align: center">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-6 col-xs-12" style="cursor: pointer;">
                                                    <div class="info-box batchBody"  onclick="LossView_model()">
                                                        <span class="info-box-icon batchIcon" style="background-color: #FCBB60"><i class="fa fa-level-down" style="color: #eee9e9"></i></span>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Loss Batches</span>
                                                            <span class="info-box-number WidgetNo" id="total_loss_batches" style="font-size: 35px; text-align: center">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                      <!--  <div class="row">
                                            <iframe id="forecast_embed" frameborder="0" height="245" width="100%" src="//forecast.io/embed/#lat=42.3583&lon=-71.0603&name=Downtown Boston"></iframe>
                                        </div>-->

                                        <input class="hidden" id="Profitid" name="Profitid[]">
                                        <input class="hidden" id="Lossid" name="Lossid[]">

                                        <div class="row" id="buybackdashDiv">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane buyback_dash_Calander" id="buyback_dash_Calender">
                                    <div class="row" style="margin-top: 5px">
                                        <div class="col-md-12" id="1T17">
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <div class="col-sm-4">
                                                        <h4 class="box-title">Feed Schedule</h4>
                                                    </div>

                                                    <div class="col-sm-3 filtercalander" >

                                                    </div>

                                                    <div class="col-sm-2 datefilter hide">
                                                        <span style="font-weight: bold;">Start Date</span>
                                                        <br>
                                                        <div class="input-group datepic ">
                                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                            <input type="text" name="datefrom"
                                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                                   value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 datefilter hide">
                                                        <span style="font-weight: bold;">End Date</span>
                                                        <br>
                                                        <div class="input-group datepic">
                                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                            <input type="text" name="dateto"
                                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                                   value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body calanderview" style="display: block;width: 100%">
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <div id='buyback_calendar'></div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="box-body tasklistview hide" style="display: block;width: 100%">
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <div id='crm_calendar_report'></div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="overlay" id="overlay117" style="display: none;"><i
                                                        class="fa fa-refresh fa-spin"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane buyback_dash_Todo" id="buyback_dash_Todo">
                                    <div class="fullBody">
                                        <div class="box box-primary">
                                            <div class="box-header ui-sortable-handle" style="">
                                                <i class="fa fa-check-square"></i>
                                                <h3 class="box-title">To Do List</h3>
                                                <div class="pull-right col-sm-7">
                                                    <?php echo form_open('login/loginSubmit', ' name="toDoList_dashboard" id="toDoList_dashboard" class="form-horizontal" role="form"'); ?>
                                                    <label for="inputData" class="col-md-3 control-label" style="text-align: right">As Of Date :</label>
                                                    <div class="col-sm-4">
                                                        <div class="input-group datepicToDo">
                                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                            <input type="text" name="TodoDate"
                                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                                   value="<?php echo $current_date; ?>" id="TodoDate" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label style="margin-top: 3%">Pendings :
                                                            <small onclick="ViewPendingTasks()">&nbsp;
                                                                <span class="badge badge-pill" style="background-color:#de84f3; padding-bottom: 5px" id="taskpending"></span>
                                                                &nbsp;</small>
                                                        </label>
                                                    </div>
                                                    <?php echo form_close(); ?>
                                                    <div class="pull-right col-sm-2" style="margin-top: 4px;">
                                                        <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateToDoListPdf()">
                                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <div id="todayDoList"></div>
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
    </section>

    <div class="modal fade" id="ProfitView" role="dialog" tabindex="1" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title ProfitTitle" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-sm-3">
                                <label for="">Date From</label>
                                <div class="input-group datepic col-sm-10">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="datefrom"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="dateFrom" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="">Date To</label>
                                <div class="input-group datepicto col-sm-10">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateto"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="datesTo" class="form-control">
                                </div>
                            </div>
                            <div class=" col-sm-3">
                                <label for="">Select Farm :</label>
                                <?php echo form_dropdown('farmID[]', $farmer, '', ' class="form-control" multiple="multiple" id="farmID" onchange="ProfitView()"'); ?>
                            </div>
                            <div class="form-group col-sm-2 pull-right">
                                <label for=""></label>
                               <!-- <button style="margin-top: 25px" type="button" onclick="ProfitView()"
                                        class="btn btn-primary btn-xs">
                                    Generate</button>-->
                            </div>

                        </div>
                    </div>

                    <hr>
                    <div class="Profit" id="Profit">
                        <table id="tbl_Profit" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>#</th>
                                <th>Farm</th>
                                <th>Batch Code</th>
                                <th>Started Date</th>
                                <th>Input</th>
                                <th>Output</th>
                                <th>Balance</th>
                                <th>Age</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="profitData">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="LossView" role="dialog" tabindex="1" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title LossTitle" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-sm-3">
                                <label for="">Date From :</label>
                                <div class="input-group datepick col-sm-10">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="date_from"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="date_From" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="">Date To :</label>
                                <div class="input-group datepickerto col-sm-10">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="date_to"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="date_To" class="form-control">
                                </div>
                            </div>
                            <div class=" col-sm-3">
                                <label for="">Select Farm :</label>
                                <?php echo form_dropdown('farmID[]', $farmer, '', ' class="form-control" multiple="multiple" id="farmer" onchange="LossView()"'); ?>
                            </div>
                            <!--<div class="form-group col-sm-2 pull-right">
                                <label for=""></label>
                                <button style="margin-top: 25px" type="button" onclick="LossView()"
                                        class="btn btn-primary btn-xs">
                                    Generate</button>
                            </div>-->
                        </div>
                    </div>

                    <hr>
                    <div class="Loss" id="Loss">
                        <table id="tbl_ProfitLoss" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>#</th>
                                <th>Farm</th>
                                <th>Batch Code</th>
                                <th>Started Date</th>
                                <th>Input</th>
                                <th>Output</th>
                                <th>Balance</th>
                                <th>Age</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="lossData">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="batch_Details_View_modal" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 65%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title ModalTitle" id="myModalLabel">Batches</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                                <label for="">Select Farm :</label>
                                <?php echo form_dropdown('farmID_batch[]', $farmer, '', ' class="form-control" multiple="multiple" id="farmID_batch" onchange="BatchView_model()"'); ?>
                        </div>
                    </div>
                    <br>
                    <div class="Batch" id="Batch_Details_View">
                        <table id="tbl_batches" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>#</th>
                                <th>Farmer</th>
                                <th>Batch Code</th>
                                <th>Start Date</th>
                                <th>Age</th>
                                <th>Input</th>
                                <th>Output</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody id="batchDetails_table_data">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="farm_Details_View_modal" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title ModalTitle" id="myModalLabel">Farms</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="area">Area</label><br>
                            <?php echo form_dropdown('farm_area_filter[]', $location_arr, '', 'class="form-control" id="farm_area_filter" multiple="" '); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="subarea">Sub Area</label><br>
                            <div id="div_load_subloacations">
                                <select name="subLocationID[]" class="form-control" id="filter_sublocation" multiple="">
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="Batch" id="Farm_Details_View">
                        <table id="tbl_farmdash" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>#</th>
                                <th>Farm Code</th>
                                <th>Farm Name</th>
                                <th>Farm Type</th>
                                <th>Area</th>
                                <th>Sub Area</th>
                                <th>Contact No</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody id="farmDetails_table_data">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="buyback_production_report_modal" tabindex="2" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 90%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Production Statement<span class="myModalLabel"></span>
                    </h4>
                </div>
                <div class="modal-body" style="">
                    <div id="productionReportDrilldown"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="calenderFeedView" role="dialog" tabindex="1" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Feed Schedule</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="area">Area</label><br>
                            <?php echo form_dropdown('cal_area_filter[]', $location_arr, '', 'class="form-control" id="cal_area_filter" multiple="" '); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="subarea">Sub Area</label><br>
                            <div id="div_load_cal_subloacations">
                                <select name="cal_subLocationID[]" class="form-control" id="cal_subLocationID" multiple="">
                                </select>
                            </div>
                        </div>
                        <input id="calViewDate" name="calViewDate" class="hidden">
                        <input id="calViewFeedType" name="calViewFeedType" class="hidden">
                    </div>
                    <div class="" id="">
                        <table id="tbl_feedSchedule" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <!--<th>#</th>-->
                                <th>Date</th>
                                <th>Farm</th>
                                <th>Batch Code</th>
                                <th>Area</th>
                                <th>Sub area</th>
                                <th>Age</th>
                                <th>Feed Type</th>
                                <th>Required Qty</th>
                            </tr>
                            </thead>
                            <tbody id="feedScheduleBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            var sat = 2;
           /* load_locationbase_sub_location(0);
            buybackDashSum_Count();
            buybackDashboard_Data(sat);*/
            load_todayDoList();

            $('#farmID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 200,
                numberDisplayed: 2,
                buttonWidth: '180px'
            });
            $("#farmID").multiselect2('selectAll', false);
            $("#farmID").multiselect2('updateButtonText');

            $('#farmID_batch').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 200,
                numberDisplayed: 2,
                buttonWidth: '180px'
            });
            $("#farmID_batch").multiselect2('selectAll', false);
            $("#farmID_batch").multiselect2('updateButtonText');

            $('#farmer').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 200,
                numberDisplayed: 2,
                buttonWidth: '180px'
            });
            $("#farmer").multiselect2('selectAll', false);
            $("#farmer").multiselect2('updateButtonText');

            $('#LocationId').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $('.filterDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            });

            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                ProfitView();
            });
            $('.datepicto').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                ProfitView();
            });
            $('.datepick').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                LossView();
            });
            $('.datepickerto').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                LossView();
            });

            $('.datepicToDo').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                load_todayDoList();
            });

            FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
            fetch_finance_year_period(FinanceYearID);

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('#buyback_calendar').fullCalendar('render');
            });


            $('#buyback_calendar').fullCalendar({
                customButtons: {
                    myCustomButton: {
                        text: 'Task List',
                        click: function() {
                            subtask_task_rpt_dashboard(1);
                        }
                    }
                },
                header: {
                    left: 'title',
                    right: 'prev,next today',
                    center: '',
                  //  right: 'month,agendaWeek,agendaDay',
                },
                defaultDate: new Date(),
                defaultView: 'month',
                editable: false,
                eventLimit: true,
                events: {
                    url: '<?php echo site_url('BuybackDashboard/allCalenderEvents'); ?>',
                    data: function () {
                    },
                    type: "POST",
                    cache: false
                },

                eventRender: function (event, element) {
                    element.find(".fc-content").click(function () {
                        viewEvent(event.fetchdate, event.feedtype);
                    });
                    element.popover({
                        content: event.title,
                        trigger: 'hover',
                        placement: 'top',
                        container: 'body'
                    });
                }
            });

            $('.modal').on('hidden.bs.modal', function (e) {
                if($('.modal').hasClass('in')) {
                    $('body').addClass('modal-open');
                }
            });

        });

        $("#farm_area_filter").change(function () {
            if ((this.value)) {
                load_locationbase_sub_location(this.value , 'farm');
                Farm_View_model();
                return false;
            }

        });
        $('#farm_area_filter').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#farm_area_filter").multiselect2('selectAll', false);
        $("#farm_area_filter").multiselect2('updateButtonText');

        $('#filter_sublocation').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_sublocation").multiselect2('selectAll', false);
        $("#filter_sublocation").multiselect2('updateButtonText');

        $("#cal_area_filter").change(function () {
            if ((this.value)) {
                load_locationbase_sub_location(this.value, 'calander');
                viewEvent('','');
                return false;
            }

        });
        $('#cal_area_filter').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#cal_area_filter").multiselect2('selectAll', false);
        $("#cal_area_filter").multiselect2('updateButtonText');

        $('#cal_subLocationID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#cal_subLocationID").multiselect2('selectAll', false);
        $("#cal_subLocationID").multiselect2('updateButtonText');

        function viewEvent(date, feedtype) {
            if(date == ''){
                date = $('#calViewDate').val();
            }
            if(feedtype == ''){
                feedtype = ($('#calViewFeedType').val());
            }
            var locationID =  ($('#cal_area_filter').val());
            var subLocationID =  ($('#cal_subLocationID').val());
            $.ajax({
                type: 'post',
                data: {date: date, feedtype: feedtype, locationID: locationID, subLocationID: subLocationID},
                url: "<?php echo site_url('BuybackDashboard/viewNextInputBatch'); ?>",
                dataType: 'html',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(date){
                        $('#calViewDate').val(date);
                    }
                    if(feedtype){
                        $('#calViewFeedType').val(feedtype);
                    }
                    $('#feedScheduleBody').html(data);
                    $('#calenderFeedView').modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function load_locationbase_sub_location(val, modalfor) {
            var locationid = $('#farm_area_filter').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {locationid: locationid, modalType: modalfor},
                url: "<?php echo site_url('BuybackDashboard/fetch_farmModal_sublocationDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if(modalfor == 'calander'){
                        $('#div_load_cal_subloacations').html(data);
                        $('#cal_subLocationID').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            selectAllValue: 'select-all-value',
                            //enableFiltering: true
                            buttonWidth: 150,
                            maxHeight: 200,
                            numberDisplayed: 1
                        });
                        $("#cal_subLocationID").multiselect2('selectAll', false);
                        $("#cal_subLocationID").multiselect2('updateButtonText');

                        viewEvent('','');

                    } else {
                        $('#div_load_subloacations').html(data);
                        $('#filter_sublocation').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            selectAllValue: 'select-all-value',
                            //enableFiltering: true
                            buttonWidth: 150,
                            maxHeight: 200,
                            numberDisplayed: 1
                        });
                        $("#filter_sublocation").multiselect2('selectAll', false);
                        $("#filter_sublocation").multiselect2('updateButtonText');
                        if(val == 1){
                            Farm_View_model();
                        }
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function fetch_finance_year_period(companyFinanceYearID, select_value) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyFinanceYearID': companyFinanceYearID},
                url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
                success: function (data) {
                    $('#financeyear_period').empty();
                    var mySelect = $('#financeyear_period');
                    mySelect.append($('<option></option>').val('').html('Periods'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                        });
                        if (select_value) {
                            $("#financeyear_period").val(select_value);

                        };
                    }
                    buybackDashboard_Data();
                    buybackDashSum_Count();
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

        function ProfitView_model() {
            $("#farmID").multiselect2('selectAll', false);
            $("#farmID").multiselect2('updateButtonText');
            var year = $('#companyFinanceYearID').val();
            fetch_filter_date(year);
            ProfitView();
        }

        function fetch_filter_date(year) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'year': year},
                url: "<?php echo site_url('BuybackDashboard/fetch_filter_date'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#dateFrom').val(data['beginingDate']);
                        $('#datesTo').val(data['endingDate']);
                        $('#date_From').val(data['beginingDate']);
                        $('#date_To').val(data['endingDate']);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function ProfitView() {
                $('.ProfitTitle').text('Profit Batches');
                var id =  ($('#Profitid').val());
                var farmerid =  ($('#farmID').val());
                var date_from =  ($('#dateFrom').val());
                var date_To =  ($('#datesTo').val());
            $.ajax({
                type: 'post',
                data: {'id': id, 'date_from': date_from, 'date_To': date_To, 'farmerid': farmerid},
                url: "<?php echo site_url('BuybackDashboard/fetchBatchProfitLoss'); ?>",
                dataType: 'html',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#profitData').html(data);
                    $('#ProfitView').modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function LossView_model() {
            $("#farmer").multiselect2('selectAll', false);
            $("#farmer").multiselect2('updateButtonText');
            var year = $('#companyFinanceYearID').val();
            fetch_filter_date(year);
            LossView();
        }

        function LossView() {
            $('.LossTitle').text('Loss Batches');
            var id =  ($('#Lossid').val());
            var farmerid =  ($('#farmer').val());
            var date_from =  ($('#date_From').val());
            var date_To =  ($('#date_To').val());
            $.ajax({
                type: 'post',
                data: {'id': id, 'date_from': date_from, 'date_To': date_To, 'farmerid': farmerid},
                url: "<?php echo site_url('BuybackDashboard/fetchBatchProfitLoss'); ?>",
                dataType: 'html',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#lossData').html(data);
                    $('#LossView').modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateProductionReport_preformance(batchMasterID) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {batchMasterID: batchMasterID,'typecostYN':1},
                url: '<?php echo site_url('Buyback/buyback_production_report'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#productionReportDrilldown").html(data);
                    $('#buyback_production_report_modal').modal("show");
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function buybackDashSum_Count() {
            var year =  $.trim($('#companyFinanceYearID').val());
            var TodoDate =  <?php echo $current_date;?>;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FinanceYear': year, 'TodoDate': TodoDate},
                url: "<?php echo site_url('BuybackDashboard/buybackDashSum_Count'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#total_active_farms').html(data['farms']);
                        $('#total_active_batches').html(data['batches']);
                        $('#total_profit_batches').html(data['profit']);
                        $('#total_loss_batches').html(data['loss']);
                        $('#Profitid').val(data['Profitid']);
                        $('#Lossid').val(data['Lossid']);
                        $('#pending').text(data['countTodo']);
                        $('#taskpending').text(data['countTodo']);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function buybackDashboard_Data(sat) {
            var year =  $.trim($('#companyFinanceYearID').val());
            var themeSec =  ($('#theme').val());
            var financeyear_period =  ($('#financeyear_period').val());

            $.ajax({
              //  async: true,
                type: 'post',
                data: {'theme': sat, 'FinanceYear': year, 'themeSec': themeSec, 'financeyear_period': financeyear_period},
                url: "<?php echo site_url('BuybackDashboard/buybackDashboard_Data'); ?>",
                dataType: 'html',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#buybackdashDiv').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function buybackDashboardChangeTheme() {
            var theme = $.trim($('#theme').val());

            if(theme == 2){
                $(".buybackTab").css('background-color', '#2a2a2b');
                $(".buybackTab").css('color', 'white');
                $(".theme").css('color', 'white');
                $(".theme").css('background-color', '#2a2a2b');
              //  $(".theme:hover").css('background-color', 'black');
                $(".tachometerColor").css('color', '#2a2a2b');
                $(".panel-heading").css('background-color', '#2a2a2b');
                $(".bodyBorder").css('background-color', '#2a2a2b');
                $(".fullBody").css('background-color', '#2a2a2b');
                $(".box-title").css('color', 'white');
                $(".batchBody").css('background-color', '#2a2a2b');
                $(".batchBody").css('color', 'white');
                $(".WidgetNo").css('color', 'white');
                $(".batchIcon").css('background-color', '#2a2a2b');

                var sat = 1;
                buybackDashboard_Data(sat);

            } else{
                $(".buybackTab").css('background-color', '');
                $(".buybackTab").css('color', '');
                $(".theme").css('color', '#2a2a2b');
                $(".theme").css('background-color', 'white');
                $(".tachometerColor").css('color', '');
                $(".panel-heading").css('background-color', 'white');
                $(".bodyBorder").css('background-color', '#ecf0f5');
                $(".fullBody").css('background-color', 'white');
                $(".box-title").css('color', 'black');

                $(".batchBody").css('background-color', 'white');
                $(".batchBody").css('color', 'black');
                $(".WidgetNo").css('color', '#adadad');
                $(".batchIcon").css('background-color', '#00a65a');

                var sat = 2;
                buybackDashboard_Data(sat);
            }
        }

        function load_todayDoList() {
            var TodoDate =  ($('#TodoDate').val());
            $.ajax({
                async: true,
                type: 'post',
                data: {TodoDate : TodoDate},
                url: "<?php echo site_url('BuybackDashboard/load_todayDoList'); ?>",
                dataType: 'html',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#todayDoList').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateToDoListPdf() {
            var form = document.getElementById('toDoList_dashboard');
            form.target = '_blank';
            form.action = '<?php echo site_url('BuybackDashboard/load_todayDoList_pdf'); ?>';
            form.submit();
        }

        function BatchView_model() {
            var companyFinanceYearID =  ($('#companyFinanceYearID').val());
            var farmID =  ($('#farmID_batch').val());
            $.ajax({
                async: true,
                type: 'post',
                data: {companyFinanceYearID : companyFinanceYearID, farmID : farmID},
                url: "<?php echo site_url('BuybackDashboard/load_batch_detailView'); ?>",
                dataType: 'html',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#batchDetails_table_data').html(data);
                    $('#batch_Details_View_modal').modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function Farm_View_model() {
            var companyFinanceYearID =  ($('#companyFinanceYearID').val());
            var locationID =  ($('#farm_area_filter').val());
            var subLocationID =  ($('#filter_sublocation').val());
            $.ajax({
                async: true,
                type: 'post',
                data: {companyFinanceYearID : companyFinanceYearID, locationID : locationID, subLocationID : subLocationID},
                url: "<?php echo site_url('BuybackDashboard/load_farm_detailView'); ?>",
                dataType: 'html',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#farmDetails_table_data').html(data);
                    $('#farm_Details_View_modal').modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    </script>




<?php
