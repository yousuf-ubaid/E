<?php echo head_page('Live Collection', True);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$farmer = load_all_farms();
$location_arr = load_all_locations();
$field_Officer = buyback_farm_fieldOfficers_drop();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
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
    .stars
    {
        display: inline-block;color: #F0F0F0;text-shadow: 0 0 1px #666666;font-size:30px;
    }  .highlights,
       .selectedstars {color:#F4B30A;text-shadow: 0 0 1px #F48F0A;}
</style>
<form id="buyback_collection_filter_frm">
    <div id="filter-panel" class="collapse filter-panel">

        <div class="row" style="padding-left: 2%">
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">Date From</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="buybackcollectionfrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="buybackcollectionfrom" class="form-control"  value="">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="buybackcollectionto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="buybackcollectionto"  class="form-control" value="" >
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/buyback/create_new_collection',null,'Add New Collection','BUYBACK');"><i
                    class="fa fa-plus"></i> New Collection
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-4" style="padding-left: 4%;">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Collection"
                                       id="searchTask" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php echo form_dropdown('status', array('' => 'Status', '1' => 'Draft', '2' => 'Confirmed'), '', 'class="form-control" onchange="startMasterSearch()" id="status"'); ?>
                    </div>

</form>
<div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
</div>
</div>
<br>

<div class="row">
    <div class="col-sm-12">
        <div id="buyback_collection_view"></div>
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


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var per_page = 10;
    var Otable;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/buyback_collection', '', 'Buyback Collection');
        });

        //load_farm_filter('#', 1);
        getBatchManagement_tableView();

    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getBatchManagement_tableView(data_pagination, uriSegment);
    }

    function getBatchManagement_tableView(pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#buyback_collection_filter_frm').serializeArray();
        data.push({'name': 'pageID', 'value': pageID});
        $.ajax({
            async: true,
            type: 'post',
            data: data,
            dataType: 'json',
            url: "<?php echo site_url('Buyback/load_buyback_collection'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#buyback_collection_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });


    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    $('.select2').select2();
    Inputmask().mask(document.querySelectorAll("input"));


    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getBatchManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#searchTask').val('');
        $('#status').val('');
        $('#buybackcollectionfrom').val('');
        $('#buybackcollectionto').val('');
        getBatchManagement_tableView();
    }
</script>