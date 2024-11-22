<?php echo head_page('Farm Visit Report', true);
$date_format_policy = date_format_policy();

$this->load->helper('buyback_helper');
$fieldOfficer = buyback_farm_fieldOfficers_drop();
$farmer = load_all_farms();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$location_arr = load_all_locations();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
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

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }

    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
</style>

<form id="fieldVisit_filter_frm">
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-6 col-md-2 col-xs-12">
            <label for="supplierPrimaryCode">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="fieldVisitDatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="fieldVisitDatefrom" class="form-control"  value=""  >
            </div>
        </div>
        <div class="form-group col-sm-6 col-md-2 col-xs-12">
            <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="fieldVisitDateto"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="fieldVisitDateto"  class="form-control" value="" >
            </div>
        </div>
        <div class="form-group col-sm-6 col-md-2 col-xs-12">
            <label for="area">Area</label><br>
            <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID" onchange="load_locationBase_sub_location(this), startMasterSearch()"'); ?>
        </div>
        <div class="form-group col-sm-6 col-md-2 col-xs-12">
            <label for="area">Sub Area</label><br>
            <div id="div_load_subloacations">
                <select name="subLocationID" class="form-control" id="subLocationID">
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-2 col-xs-12" style="">
            <label for="area">Farmer</label><br>
            <?php echo form_dropdown('farmername',$farmer, '', 'class="form-control select2" onchange="startMasterSearch()" id="farmername"'); ?>
        </div>
        <div class="col-sm-6 col-md-2 col-xs-12" style="">
            <label for="area">Officer</label><br>
            <?php echo form_dropdown('FieldVisitID',$fieldOfficer, '', 'class="form-control select2" onchange="startMasterSearch()" id="FieldVisitID"'); ?>
        </div>
    </div>
    </br>
</div>
<div class="row">
    <div class="col-md-5 col-xs-5" style="margin-bottom: 1px">
    </div>
    <div class="col-md-4 col-xs-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 col-xs-3 text-right" style="margin-bottom: 3px">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/buyback/create_new_farm_visit_report',null,'Add New Farm Visit','BUYBACK');">
            <i
                    class="fa fa-plus"></i> New Farm Visit
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-4 col-xs-5">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchfarm" type="text" class="form-control input-sm"
                                   placeholder="Search Farm"
                                   id="searchfarm" onkeypress="startMasterSearch()">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 col-xs-5">
                    <?php echo form_dropdown('status', array('' => 'Select Status', '1' => 'Confirmed', '2' => 'Not Confirmed'), '', 'class="form-control"  id="status" onchange="startMasterSearch()"'); ?>
                </div>
                <div class="col-sm-1 col-xs-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div id="Farmvisitview"></div>
                </div>
            </div>
            <div class="col-xs-12" style="padding-right: 5px;">
                <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                    <p id="filterDisplay"></p>

                    <nav>
                        <ul class="list-inline" id="pagination-ul">

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var per_page = 10;
    var Otable;
    $(document).ready(function () {
        $('.select2').select2();

        $('.headerclose').click(function () {
            fetchPage('system/buyback/report/field_officer_report.php', '', 'Field Officer');
        });
        //load_farm_filter('#', 1);
        getMortalityManagement_tableView();
    });
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    $('#searchfarm').bind('input', function () {
        getMortalityManagement_tableView();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getMortalityManagement_tableView(data_pagination, uriSegment);
    }

    function getMortalityManagement_tableView(pageID,uriSegment = 0) {
        var searchfarm = $('#searchfarm').val();
        var data = $('#fieldVisit_filter_frm').serializeArray();
        data.push({'name': 'pageID', 'value': pageID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_farm_visit_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Farmvisitview').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getMortalityManagement_tableView();
    }


    function clearSearchFilter() {
        $('#searchfarm').val('');
        $('#fieldVisitDatefrom').val('');
        $('#fieldVisitDateto').val('');
        $('#FieldVisitID').val('').change();
        $('#status').val('').change();
        $('#farmername').val('').change();
        $('#locationID').val('').change();
        $('#subLocationID').val('').change();
        $('#search_cancel').addClass('hide');
        getMortalityManagement_tableView();
    }

    function load_locationBase_sub_location(){
        var locationid = $('#locationID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {locationid: locationid},
            url: "<?php echo site_url('Buyback/fetch_dispatch_filterSubLocation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_subloacations').html(data);
                startMasterSearch();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_farmVisitReport(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'farmerVisitID': id},
                    url: "<?php echo site_url('Buyback/delete_farmVisitReport_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        getMortalityManagement_tableView();
                    },
                  //  success: function (data) {
                  //      stopLoad();
                   //     getMortalityManagement_tableView();
                   //     myAlert('s','Farm Visit Report Deleted Successfully');
                 //   },
                     error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                        stopLoad();
                    }
                });
            });
    }

    function referback_farmVisitReport(farmerVisitID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'farmerVisitID': farmerVisitID},
                    url: "<?php echo site_url('Buyback/referback_farmVisit_Report'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getMortalityManagement_tableView();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>