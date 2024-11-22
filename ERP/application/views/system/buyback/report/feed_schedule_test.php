<?php echo head_page($_POST["page_name"], True);
$this->load->helper('buyback_helper');
$batchMasterID_arr = load_buyBack_batches_report();
$farms_arr = load_all_farms();
$date_format_policy = date_format_policy();
$location_arr = load_all_locations();
$field_Officer = buyback_farm_fieldOfficers_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
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
</style>
<form id="dispatch_filter_frm">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">Date From</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="feedscheduleDatefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="feedscheduleDatefrom" class="form-control"  value=""  >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="feedscheduleDateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="feedscheduleDateto"  class="form-control" value="" >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="area">Area</label><br>
                <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID" onchange="startMasterSearch()"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="area">Sub Area</label><br>
                <?php echo form_dropdown('subLocationID', array(" " => "Select Sub Area"), '', 'class="form-control select2" id="subLocationID" onchange="startMasterSearch()"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="farmer">Farmer </label><br>
                <?php echo form_dropdown('farmer', $farms_arr, '', 'class="form-control select2" id="farmer" onchange="startMasterSearch()"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="fieldofficer">Field Officer</label><br>
                <?php echo form_dropdown('fieldofficer', $field_Officer, '', 'class="form-control select2" id="fieldofficer" onchange="startMasterSearch()"'); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-4" style="margin-left: 1.5%;">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Farmer or Batch"
                                       id="searchTask" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <?php echo form_dropdown('feed_status', array(''=>'Status','0' => 'Active', '1' => 'Closed'), '', 'class="form-control" onchange="startMasterSearch()" id="feed_status"'); ?>
                    </div>
                    <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="BatchMaster_view"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo footer_page('Right foot', 'Left foot', false); ?>
    <!--modal report-->
    <div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 90%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Feed Schedule</h4>
                </div>
                <div class="modal-body">
                    <div id="reportContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="buyback_production_report_modal" tabindex="2" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 95%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Production Statement<span class="myModalLabel"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="productionReportDrilldown"></div>
                </div>
                <div class="modal-body" id="PaymentHistoryModal" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979">
                    <div id="PaymentHistory"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="buyback_dispatch_note_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 95%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Dispatch Note<span class="myModalLabel"></span></h4>
                </div>
                <div class="modal-body">
                    <div id="dispatchNoteReportDrilldown"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var type;
        var url;
        var url2;
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $(document).ready(function () {
            getBatchManagement_tableView();
            $('.select2').select2();
            $('.modal').on('hidden.bs.modal', function (e) {
                if ($('.modal').hasClass('in')) {
                    $('body').addClass('modal-open');
                }
            });
        });

        function getBatchManagement_tableView() {
            var searchTask = $('#searchTask').val();
            var data = $('#dispatch_filter_frm').serialize();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: data,
                url: "<?php echo site_url('Buyback/load_feed_schedule_report'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#BatchMaster_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function startMasterSearch() {
            $('#search_cancel').removeClass('hide');
            getBatchManagement_tableView();
        }

        function clearSearchFilter() {
            $('#search_cancel').addClass('hide');
            $('#searchTask').val('');
            $('#farmer').val('');
            $('#locationID').val('');
            $('#fieldofficer').val('');
            $('#subLocationID').val('');
            $('#feedscheduleDatefrom').val('');
            $('#feedscheduleDateto').val('');
            $('#feed_status').val('');
            getBatchManagement_tableView();
        }

        $('#searchTask').bind('input', function () {
            startMasterSearch();
        });

        /*call report content*/
        function feedScheduleReport_view(batchMasterID) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {batchMasterID: batchMasterID},
                url: '<?php echo site_url('Buyback/load_feedSchedule_report'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#reportContent").html(data);
                    $('#finance_report_modal').modal("show");
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function generateProductionReport(batchMasterID) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {batchMasterID: batchMasterID},
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

        function generateDispatchNoteReport(dispatchAutoID) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {dispatchAutoID: dispatchAutoID, html: 'html'},
                url: '<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#dispatchNoteReportDrilldown").html(data);
                    $('#buyback_dispatch_note_modal').modal("show");
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        $("#locationID").change(function () {
            get_buyback_subArea($(this).val())
        });

        function generatetestmodal(batchMasterID) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {batchMasterID: batchMasterID},
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


        function get_buyback_subArea(locationID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {locationID: locationID},
                url: "<?php echo site_url('Buyback/fetch_buyback_subArea'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#subLocationID').empty();
                    var mySelect = $('#subLocationID');
                    mySelect.append($('<option></option>').val("").html("Select"));
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, text) {
                            mySelect.append($('<option></option>').val(text['locationID']).html(text['description']));
                        });
                    }
                    if(subLocationID){
                        mySelect.val(subLocationID).change();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (e) {
            startMasterSearch();
        });
        Inputmask().mask(document.querySelectorAll("input"));
    </script>