<?php
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$farmer = load_all_farms();
$location_arr = load_all_locations(false);
$field_Officer = buyback_farm_fieldOfficers_drop();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
echo head_page('Buyback Performance', True);
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

?>
<style>
    tr.highlighted td {
        background: rgb(161, 191, 252);
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<form name="buybackpreformance_rpt" id="buybackpreformance_rpt" method="post">
    <div id="filter-panel" class="collapse filter-panel">
        <div>
            <div class="col-md-12" >
                <div class="form-group col-sm-2">
                            <label for="">Date From</label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="" id="datefrom" class="form-control">
                            </div>
                        </div>
                <div class="form-group col-sm-2">
                            <label for="">Date To</label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="" id="dateto" class="form-control">
                            </div>
                        </div>
                <div class="form-group col-sm-2">
                            <label for="area">Area</label><br>
                            <?php echo form_dropdown('locationID[]', $location_arr, '', 'class="form-control" id="locationID_filter" multiple="" '); ?>
                        </div>
                <div class="form-group col-sm-2">
                            <label for="area">Sub Area</label><br>
                            <div id="div_load_subloacations">
                                <select name="subLocationID[]" class="form-control" id="filter_sublocation" multiple="">

                                </select>
                            </div>
                        </div>
                <div class="form-group col-sm-2">
                            <label for="area">Farm</label><br>
                            <div id="div_load_farm">
                                <select name="farmer[]" class="form-control" id="filter_farm" multiple="">
                                </select>
                            </div>
                        </div>
                <div class="form-group col-sm-1 pull-right">
                    <label for=""></label>
                    <button style="margin-top: 5px" type="button" onclick="get_buyback_prefoormance_report()" class="btn btn-primary btn-xs">
                                Generate
                    </button>
                </div>
            </div>
        </div>
        <div>
            <div class="col-md-12" >
                <div class="col-sm-4">
                    <input type="text" id="search" name="search" style="margin-top: 2%" class="form-control" placeholder="search Farm or Batch..." onkeyup="get_buyback_prefoormance_report()">
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                </div>
            </div>
        </div>
    </div>
    <br>
</form>
<div id="div_buyback_preformance_rpt">
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


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    $('.headerclose').click(function () {
        fetchPage('system/buyback/report/buyback_performance', '', 'Buyback Preformance')
    });
    $(document).ready(function (e) {
        get_buyback_prefoormance_report();
        load_locationbase_sub_location();
        $('.select2').select2();


        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    $("#locationID_filter").change(function () {
        if ((this.value)) {
            load_locationbase_sub_location(this.value);
            return false;
        }

    });
    $('#locationID_filter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#locationID_filter").multiselect2('selectAll', false);
    $("#locationID_filter").multiselect2('updateButtonText');

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

    $('#filter_farm').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#filter_farm").multiselect2('selectAll', false);
    $("#filter_farm").multiselect2('updateButtonText');

    function generateReportPdf() {
        var form = document.getElementById('buybackpreformance_rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/get_buy_back_preformance_rpt_pdf'); ?>';
        form.submit();
    }

    function load_locationbase_sub_location() {
        var locationid = $('#locationID_filter').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {locationid: locationid},
            url: "<?php echo site_url('Buyback/fetch_buyback_preformance_sublocationDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
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
                fetch_farm();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function fetch_farm() {
        var sublocationid = $('#filter_sublocation').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {sublocationid: sublocationid},
            url: "<?php echo site_url('Buyback/fetch_farm_by_sub_location'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_farm').html(data);
                $('#filter_farm').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_farm").multiselect2('selectAll', false);
                $("#filter_farm").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_buyback_prefoormance_report() {
        var search = $('#search').val();
        var locationID = $('#locationID_filter').val();
        var dateto = $('#dateto').val();
        var datefrom = $('#datefrom').val();
        var subLocationID = $('#filter_sublocation').val();
        var farmer = $('#filter_farm').val();
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('Buyback/buy_back_preformance_rpt'); ?>',
            data: {'search' : search, 'dateto' : dateto, 'datefrom' : datefrom, 'locationID' : locationID, 'subLocationID' : subLocationID, 'farmer' : farmer },
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $("#div_buyback_preformance_rpt").html(data);
                $('.select2').select2();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
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



</script>
