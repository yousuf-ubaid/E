<?php

$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');

$memOccutn = fetch_memOccupation();
$occupation = load_Jobcategories();
$ngoSchools = load_ngoSchools();
$grades = load_grades();
$language = load_language();
$com_area = load_region_fo_members();
$comDivision = load_division_for_member();
$countries_arr = load_countries_compare();

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
    <style>
        #search_cancel img {
            background-color: #f3f3f3;
            border: solid 1px #dcdcdc;
            padding: 3px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }

        #search_dashMah_cancel img {
            background-color: #f3f3f3;
            border: solid 1px #dcdcdc;
            padding: 3px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 12px;
        }

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

        .with-nav-tabs.panel-success .mainpanel > li.active > a, .with-nav-tabs.panel-success .mainpanel > li.active > a:hover, .with-nav-tabs.panel-success .mainpanel > li.active > a:focus {
            color: #000000;
            background-color: #ecf0f5;
            border-color: #ecf0f5;
            border-bottom-color: transparent;
        }

        .check {
            opacity: 0.5;
            color: #996;

        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 12px;
        }

        .pagination > li > a, .pagination > li > span {
            padding: 2px 8px;
        }

        .fc-time {
            display: none !important;
        }

        .boxHeaderCustom {
            height: 29px !important;
            font-size: 16px !important;
            padding: 3px 9px !important;
            font-weight: 700 !important;
            background-color: rgba(222, 218, 218, 0.16) !important;
            color: #69697b !important;
        }

        .r-icon-stats {
            text-align: center;
        }

        .r-icon-stats i {
            width: 88px;
            height: 44px;
            text-align: center;
            color: white;
            font-size: 24px;
            display: inline-block;
            border-radius: 25px;
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

        .white-box {
            background: #ffffff;
            padding: 0px;
            margin-bottom: 15px;
        }

        .white-box .box-title {
            margin: 0px 0px 12px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
        }

        .bg-families {
            background-color: #00a65a !important;
        }

        .bg-committees {
            background-color: #f39c12 !important;
        }

        .bg-members {
            background-color: #00c0ef !important;
        }

        .bg-theme {
            background-color: #ff6849 !important;
        }

        .bg-inverse {
            background-color: #4c5667 !important;
        }

        .bg-purple {
            background-color: #9675ce !important;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        .text-muted {
            text-align: center;
        }

        .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {

            padding: 4px;
        }

        th {

            font-size: 12px;
        }

        .ti-stats-up:before {
            content: "";
        }

        .numberStar {
            text-align: center;
            font-weight: 600;
            color: teal;
            margin-top: 5%;
        }

        .countstar {
            font-size: 20px;
            font-weight: bold;
            margin: 3px 12px 0 0;
            white-space: nowrap;
            padding: 0;
        }

    </style>
    <style>
        .infoComm-box {
            display: block;
            min-height: 14px;
            background: #fff;
            width: 200px;
            height: 75px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }
        .infoComm-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 75px;
            width: 50px;
            text-align: center;
            font-size: 45px;
            line-height: 75px;
            background: rgba(0, 0, 0, 0.2);
        }
        .infoComm-box-content {
            padding: 5px 10px;
            margin-left: 50px;
        }

        .infoComm-box-text {
            display: block;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            margin-top: 8px;
            text-overflow: ellipsis;
        }
        .infoComm-box-number {
            display: block;
            font-weight: bold;
            margin-top: 2px;
            font-size: 16px;
        }
    </style>

    <style>
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .headrowtitle {
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: center;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }
    </style>

    <script src="<?php echo base_url('plugins/morris/morris.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/community_ngo/raphael-min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/morris/morris.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">


    <link href='<?php echo base_url('plugins/fullcalender/lib/cupertino/jquery-ui.min.css'); ?>' rel='stylesheet'/>
    <link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
    <link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>
    <script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
    <section class="content" id="ajax_body_container">
        <div id="dashboard_content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-success" style="border: none;">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs mainpanel">
                                <li class="active">
                                    <a onclick="community_generalDash()" id="" data-id="0"
                                       href="#comGeneral_dashboardTemp" data-toggle="tab"
                                       aria-expanded="true"><span><i class="fa fa-tachometer" aria-hidden="true"
                                                                     style="color: #50749f;font-size: 16px;"></i> Community</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body" style="background-color: #ecf0f5;">
                            <div class="tab-content">
                                <div class="tab-pane active" id="comGeneral_dashboardTemp">
                                    <div class="box box-warning">
                                        <div class="box-header with-border">
                                            <div class="row" style="margin-top: 5px">
                                                <div class="col-md-12" id="">
                                                    <div class="col-sm-11" style="">
                                                        <h4 class="box-title">Dashboard</h4>
                                                      </div>
                                                    <div class="col-sm-1 hide" id="search_dashMah_cancel">
                    <span class="tipped-top" style="margin-left: auto;"><a id="cancelSearchDashboard" href="#"
                                                                           onclick="clearDashboardSearchFilter()"><img
                                src="<?php echo base_url("images/community/cancel-search.gif") ?>"></a></span>
                                                    </div>
                                                </div>
                                                <form method="post" name="form_dash_famDivi" id="form_dash_famDivi" class="form-horizontal">
                                                    <div class="col-md-12" STYLE="width: 100%;">
                                                        <div style="display: none;" class="col-sm-2">
                                                            <label for="countyID" class="title"><?php echo $this->lang->line('communityngo_Country'); ?><!--Country--><label>
                                                                    <select id="countyID" name="countyID" class="form-control select2" onchange="loadDshcountry_Province();" data-placeholder="Select Country">
                                                                        <option value=""></option>
                                                                        <?php foreach ($countries_arr as $Val) { ?>
                                                                            <option value="<?php echo $Val['countryID']; ?>" selected="selected"><?php echo $Val['CountryDes']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                        </div>
                                                        <div style="margin-right:-40px;" class="col-sm-2">
                                                            <label for="provinceID" class="title"><?php echo $this->lang->line('communityngo_Province'); ?><!--Province--><label>
                                                                    <select name="provinceID" class="form-control select2" id="provinceID" style="width: 100%;"
                                                                            onchange="loadDshcountry_District();">
                                                                        <option value="" selected="selected"><?php echo $this->lang->line('comNgo_dash_select_a_province'); ?></option>
                                                                    </select>
                                                        </div>
                                                        <div style="margin-right:-45px;" class="col-sm-2">
                                                            <label for="districtID" class="title"><?php echo $this->lang->line('communityngo_District'); ?><!--District Division--><label>
                                                                    <select name="districtID" class="form-control select2" id="districtID" style="width: 100%;"
                                                                            onchange="loadDshcountry_districtDivision()">
                                                                        <option value="" selected="selected"><?php echo $this->lang->line('comNgo_dash_select_a_district'); ?></option>
                                                                    </select>
                                                        </div>
                                                        <div style="margin-left:-5px;" class="col-sm-2">
                                                            <label class="title"><?php echo $this->lang->line('communityngo_DistrictDivision'); ?><!--District Division--><label>
                                                                    <select name="districtDivisionID" class="form-control select2" id="districtDivisionID" style="width: 100%;"
                                                                            onchange="loadDshcountry_GSDivision(this.value);loadDshcountry_Division_Area(this.value)">
                                                                        <option value="" selected="selected"><?php echo $this->lang->line('comNgo_dash_select_a_district_division'); ?></option>
                                                                    </select>
                                                        </div>
                                                        <div style="margin-left:-12px;margin-right: 24px;" class="col-sm-2" id="areaMemIddrp">
                                                            <label class="title"><?php echo $this->lang->line('communityngo_region'); ?><!--Area--><label>
                                                                    <?php echo form_dropdown('areaMemId', $com_area, '', 'class="form-control select2 pull-right" id="areaMemId" multiple=""'); ?>
                                                        </div>
                                                        <div class="col-sm-2" id="gsMemIddrp">
                                                            <label class="title"><?php echo $this->lang->line('communityngo_GS_Division'); ?><!--GS Division--><label>
                                                                    <?php echo form_dropdown('gsDivitnId', $comDivision, '', 'class="form-control select2 pull-right" id="gsDivitnId" multiple=""'); ?>

                                                        </div>
                                                        <div class="col-sm-1" style="">
                                                        </div>
                                                        <div class="col-sm-1" style="">
                                                            <button class="btn btn-primary btn-sm btn-flat" onclick="reload_MHdashboard();" type="button" id="getGenDel" value="" style="margin-top:20px;" data-toggle="tooltip" title="Search"><i class="fa fa-search"></i></button>
                                                        </div>


                                                    </div>
                                                </form>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-lg-2 col-xs-6">
                                                    <div class="white-box">
                                                        <div class="r-icon-stats">
                                                            <i class="ti-stats-up bg-members">
                                                                <div id="total_members" class="countstar responsive">0</div>
                                                            </i>
                                                        </div>
                                                        <div class="bodystate">
                                                            <div class="numberStar"><?php echo $this->lang->line('comNgo_dash_members'); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-xs-6">
                                                    <div class="white-box">
                                                        <div class="r-icon-stats">
                                                            <i class="ti-stats-up bg-families">
                                                                <div id="total_families" class="countstar responsive">0</div>
                                                            </i>
                                                        </div>
                                                        <div class="bodystate">
                                                            <div class="numberStar"><?php echo $this->lang->line('comNgo_dash_families'); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-xs-6">
                                                    <div class="white-box">
                                                        <div class="r-icon-stats">
                                                            <i class="ti-stats-up bg-committees">
                                                                <div id="total_committees" class="countstar responsive">0</div>
                                                            </i>
                                                        </div>
                                                        <div class="bodystate">
                                                            <div class="numberStar"><?php echo $this->lang->line('comNgo_dash_committees'); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-xs-6">
                                                    <div class="white-box">
                                                        <div class="control-label infoComm-box">
                                                            <span class="infoComm-box-icon bg-aqua"><i class="fa fa-home" title="Houses"></i></span>

                                                            <div class="infoComm-box-content">
                                                                <span class="infoComm-box-text" style="color: #0099CC;" onclick="fetch_comHousingData();"><?php echo $this->lang->line('comNgo_dash_totalHouseEn'); ?></span>
                                                                <label class="infoComm-box-number" style="text-align: center;"><span class="badge" style="background-color: lightgrey;color: #006f00;font-size:16px;" id="noOfTotHouses" title="Total Houses"></span></label>
                                                            </div>
                                                            <!-- /.info-box-content -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="communityGenDiv">

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


    <div class="modal fade" id="housing_femDiv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:75%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="houseEnr_title"><?php echo $this->lang->line('comNgo_dash_community_housing_details'); ?></h4>
                </div>
                <form method="post" class="form-horizontal" id="housing_femDiv_form" name="housing_femDiv_form">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewHousing_view" class="active"><a href="#femHousingHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewHousingAttachment"><a href="#femHousing-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="femHousingHome-m">
                                            <div id="load_housing_femDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="femHousing-m">
                                            <div id="loadPageHousingAttachment" class="col-md-8">
                                                <div class="table-responsive">
                                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>&nbsp; <strong><?php echo $this->lang->line('common_attachments');?><!--Attachments--></strong>
                                                    <br><br>
                                                    <table class="table table-striped table-condensed table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                                            <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                                            <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="View_attachment_modal_body" class="no-padding">
                                                        <tr class="danger">
                                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            loadDshcountry_Province();
            communityDashSum_Count();
            commPopulation_Count();

            $('#areaMemId').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $('#gsDivitnId').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });



            Highcharts.getOptions().plotOptions.pie.colors = (function () {
                var colors = ['#5DADE2', '#85C1E9', '#AED6F1', '#D6EAF8'],
                    base = Highcharts.getOptions().colors[0],
                    i;

                for (i = 0; i < 10; i += 1) {
                    // Start out with a darkened base color (negative brighten), and end
                    // up with a much brighter color
                    colors.push(Highcharts.Color(base).brighten((i - 3) / 7).get());
                }
                return colors;
            }());

        });

        $('.sidebar-toggle').click(function () {
            //do something
            community_generalDash();

        });

        function reload_MHdashboard() {

            $('#search_dashMah_cancel').removeClass('hide');
            commPopulation_Count();
            communityDashSum_Count();
        }

        function clearDashboardSearchFilter() {
            $('#search_dashMah_cancel').addClass('hide');
            $('#areaMemId').multiselect2('deselectAll', false);
            $('#areaMemId').multiselect2('updateButtonText');
            $('#gsDivitnId').multiselect2('deselectAll', false);
            $('#gsDivitnId').multiselect2('updateButtonText');

            $('#provinceID').val('').change();
            $('#districtID').val('').change();
            $('#districtDivisionID').val('').change();

            communityDashSum_Count();
            commPopulation_Count();
        }

        function community_generalDash() {

            $('#mahalla_feeDiv').html('');
            communityDashSum_Count();
            commPopulation_Count();
        }

        function communityDashSum_Count() {

            var areaMemId = $('#areaMemId').val();
            var gsDivitnId = $('#gsDivitnId').val();
            var countyID= document.getElementById('countyID').value;
            var provinceID= document.getElementById('provinceID').value;
            var districtID= document.getElementById('districtID').value;
            var districtDivisionID= document.getElementById('districtDivisionID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {areaMemId:areaMemId,gsDivitnId:gsDivitnId,countyID:countyID,provinceID:provinceID,districtID:districtID,districtDivisionID:districtDivisionID},
                url: "<?php echo site_url('CommunityJammiyaDashboard/communityDashSum_Count'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {

                        $('#total_members').html(data['members']);
                        $('#total_families').html(data['families']);
                        $('#total_committees').html(data['committees']);
                        $('#noOfTotHouses').html(data['houseCount']);

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function commPopulation_Count() {

            var areaMemId = $('#areaMemId').val();
            var gsDivitnId = $('#gsDivitnId').val();
            var countyID= document.getElementById('countyID').value;
            var provinceID= document.getElementById('provinceID').value;
            var districtID= document.getElementById('districtID').value;
            var districtDivisionID= document.getElementById('districtDivisionID').value;


            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {areaMemId:areaMemId,gsDivitnId:gsDivitnId,countyID:countyID,provinceID:provinceID,districtID:districtID,districtDivisionID:districtDivisionID},
                url: "<?php echo site_url('CommunityJammiyaDashboard/commPopulation_Count'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    // document.getElementById('memberDetailDivs').style.display = 'block';

                    $('#communityGenDiv').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function fetch_comHousingData() {

            var areaMemId = $('#areaMemId').val();
            var gsDivitnId = $('#gsDivitnId').val();

            $("#femHousing-m").removeClass("active");
            $("#femHousingHome-m").addClass("active");
            $("#TabViewHousingAttachment").removeClass("active");
            $("#TabViewHousing_view").addClass("active");
            $('#load_housing_femDiv').html('');
            var titleHousing = '<?php echo $this->lang->line('comNgo_dash_community_housing_details'); ?>';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {areaMemId:areaMemId,gsDivitnId:gsDivitnId},
                url: "<?php echo site_url('CommunityJammiyaDashboard/load_houseEnrolling_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#housing_femDiv_form')[0].reset();
                    $('#housing_femDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_housing_femDiv').html(data);
                    $('#houseEnr_title').html(titleHousing);
                    $('#housing_femDiv_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function generateReportPdf() {

            var form = document.getElementById('housing_femDiv_form');
            form.target = '_blank';
            form.action = '<?php echo site_url('CommunityJammiyaDashboard/load_houseEnrolling_del_pdf'); ?>';
            form.submit();


        }

        //area setup
        function loadDshcountry_Province() {

            var countyID= document.getElementById('countyID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {countyID: countyID},
                url: "<?php echo site_url('CommunityJammiyaDashboard/fetch_provinceBased_countryDropdown'); ?>",
                success: function (data) {
                    $('#provinceID').html(data);
                    // $('#provinceID').val(province).change();
                    $('#form_dash_famDivi').data('bootstrapValidator').resetField($('#provinceID'));

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadDshcountry_District() {

            var provinceID= document.getElementById('provinceID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: provinceID},
                url: "<?php echo site_url('CommunityJammiyaDashboard/fetch_provinceBased_districtDropdown'); ?>",
                success: function (data) {
                    $('#districtID').html(data);
                    //  $('#districtID').val(district).change();
                    loadDshcountry_districtDivision();
                    $('#form_dash_famDivi').data('bootstrapValidator').resetField($('#districtID'));

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadDshcountry_districtDivision() {

            var districtID= document.getElementById('districtID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: districtID},
                url: "<?php echo site_url('CommunityJammiyaDashboard/fetch_district_divisionDropdown'); ?>",
                success: function (data) {
                    $('#districtDivisionID').html(data);
                    // $('#districtDivisionID').val(district_division).change();
                    $('#form_dash_famDivi').data('bootstrapValidator').resetField($('#districtDivisionID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadDshcountry_GSDivision(masterID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'masterID': masterID},
                url: '<?php echo site_url("CommunityJammiyaDashboard/fetch_division_based_GSDropdown"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#gsMemIddrp').html(data);
                    $('#gsDivitnId').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }

        function loadDshcountry_Division_Area(masterID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'masterID': masterID},
                url: '<?php echo site_url("CommunityJammiyaDashboard/fetch_distric_diviBase_Area_Dropdown"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#areaMemIddrp').html(data);
                    $('#areaMemId').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }

    </script>

<?php
