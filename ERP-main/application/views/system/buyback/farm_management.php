<?php echo head_page('Farms', true);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$farmer = load_all_farms();
$location_arr = load_all_locations();
$field_Officer = buyback_farm_fieldOfficers_drop();
$usergroupcompanywiseallow = getPolicyValuesgroup('F','All');
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
</style>
<form id="farmmanagement_filter_frm">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-2" style="left: 2%">
                <label for="farer">Area</label><br>
                <?php echo form_dropdown('locationID', $location_arr, " ", 'class="form-control select2" id="locationID"'); ?>
            </div>
            <div class="form-group col-sm-2" style="left: 10px">
                <label for="farer">Sub Area</label><br>
                <?php echo form_dropdown('subLocationID', array("" => "Select Sub Area"), "", 'class="form-control select2" id="subLocationID" onchange="startMasterSearch()"'); ?>
            </div>

            <div class="form-group col-sm-2" style="">
                <label for="fieldofficer">Field Officer</label><br>
                <?php echo form_dropdown('fieldofficer', $field_Officer, '', 'class="form-control select2" id="fieldofficer" onchange="startMasterSearch()"'); ?>
            </div>

         <!--   <div class="form-group col-sm-2" style="left: 117px">
                <label for="farmer">Farm Type </label><br>
                <?php echo form_dropdown('farm_type', array('' => 'Select Farm Type', '1' => 'Third Party', '2' => 'Own'), '', 'class="form-control"  id="farm_type" onchange="startMasterSearch()"'); ?>
            </div> -->

        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <a href="#" type="button" class="btn btn-success btn-sm pull-right" style="margin-left: 2px" onclick="excel_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <?php if($usergroupcompanywiseallow == 0){?>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="createfarmCompany()"><i
                        class="fa fa-plus"></i> New Farm
            </button>
            <?php } else if ($usergroupcompanywiseallow != 0) { ?>
                <button type="button" class="btn btn-primary pull-right"
                        onclick="fetchPage('system/buyback/create_farm',null,'Add New Farm','BUYBACK');"><i
                            class="fa fa-plus"></i> New Farm
                </button>
            <?php }?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-4" style="padding-left: 4%">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Farm"
                                       id="searchTask" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>


                        </div>
                    </div>

                    <div class="col-sm-2" style="margin-left: 1%;">
                        <?php echo form_dropdown('status', array('' => 'Select Status', '1' => 'Active', '2' => 'Not Active'), '', 'class="form-control"  id="status" onchange="startMasterSearch()"'); ?>
                    </div>

                    <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
                <br>

                <div class="row">
                    <ul class="nav nav-tabs" id="main-tabs">
                        <li class="active"><a href="#ownFarm" data-toggle="tab" onclick="getFarmManagement_tableView('#')">Own Farms</a></li>
                        <li><a href="#ThirdPartyFarm" data-toggle="tab" onclick="getThirdPartyFarmManagement_tableView('#')">Third Party Farms</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="ownFarm">
                        <div class="row">
                            <div class="col-sm-11">
                                <div id="FarmMaster_view"></div>
                            </div>
                            <div class="col-sm-1">
                                <ul class="alpha-box">
                                    <li><a href="#" class="farmsorting selected" id="sorting_1"
                                           onclick="load_farm_filter('#',1)">#</a></li>
                                    <li><a href="#" class="farmsorting" id="sorting_2" onclick="load_farm_filter('A',2)">A</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_3" onclick="load_farm_filter('B',3)">B</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_4" onclick="load_farm_filter('C',4)">C</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_5" onclick="load_farm_filter('D',5)">D</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_6" onclick="load_farm_filter('E',6)">E</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_7" onclick="load_farm_filter('F',7)">F</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_8" onclick="load_farm_filter('G',8)">G</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_9" onclick="load_farm_filter('H',9)">H</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_10" onclick="load_farm_filter('I',10)">I</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_11" onclick="load_farm_filter('J',11)">J</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_12" onclick="load_farm_filter('K',12)">K</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_13" onclick="load_farm_filter('L',13)">L</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_14" onclick="load_farm_filter('M',14)">M</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_15" onclick="load_farm_filter('N',15)">N</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_16" onclick="load_farm_filter('O',16)">O</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_17" onclick="load_farm_filter('P',17)">P</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_18" onclick="load_farm_filter('Q',18)">Q</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_19" onclick="load_farm_filter('R',19)">R</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_20" onclick="load_farm_filter('S',20)">S</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_21" onclick="load_farm_filter('T',21)">T</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_22" onclick="load_farm_filter('U',22)">U</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_23" onclick="load_farm_filter('V',23)">V</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_24" onclick="load_farm_filter('W',24)">W</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_25" onclick="load_farm_filter('X',25)">X</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_26" onclick="load_farm_filter('Y',26)">Y</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_27" onclick="load_farm_filter('Z',27)">Z</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xs-12" style="padding-right: 5px;">
                            <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                                <p id="filterDisplay"></p>
                                <nav>
                                    <ul class="list-inline" id="pagination-ul"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="ThirdPartyFarm">
                        <div class="row">
                            <div class="col-sm-11">
                                <div id="FarmMaster_ThirdPartyFarmView"></div>
                            </div>
                            <div class="col-sm-1">
                                <ul class="alpha-box">
                                    <li><a href="#" class="farmsorting selected" id="sorting_28"
                                           onclick="load_farm_thirdParty_filter('#',28)">#</a></li>
                                    <li><a href="#" class="farmsorting" id="sorting_29" onclick="load_farm_thirdParty_filter('A',29)">A</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_30" onclick="load_farm_thirdParty_filter('B',30)">B</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_31" onclick="load_farm_thirdParty_filter('C',31)">C</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_32" onclick="load_farm_thirdParty_filter('D',32)">D</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_33" onclick="load_farm_thirdParty_filter('E',33)">E</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_34" onclick="load_farm_thirdParty_filter('F',34)">F</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_35" onclick="load_farm_thirdParty_filter('G',35)">G</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_36" onclick="load_farm_thirdParty_filter('H',36)">H</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_37" onclick="load_farm_thirdParty_filter('I',37)">I</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_38" onclick="load_farm_thirdParty_filter('J',38)">J</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_39" onclick="load_farm_thirdParty_filter('K',39)">K</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_40" onclick="load_farm_thirdParty_filter('L',40)">L</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_41" onclick="load_farm_thirdParty_filter('M',41)">M</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_42" onclick="load_farm_thirdParty_filter('N',42)">N</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_43" onclick="load_farm_thirdParty_filter('O',43)">O</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_44" onclick="load_farm_thirdParty_filter('P',44)">P</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_45" onclick="load_farm_thirdParty_filter('Q',45)">Q</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_46" onclick="load_farm_thirdParty_filter('R',46)">R</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_47" onclick="load_farm_thirdParty_filter('S',47)">S</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_48" onclick="load_farm_thirdParty_filter('T',48)">T</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_49" onclick="load_farm_thirdParty_filter('U',49)">U</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_50" onclick="load_farm_thirdParty_filter('V',50)">V</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_51" onclick="load_farm_thirdParty_filter('W',51)">W</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_52" onclick="load_farm_thirdParty_filter('X',52)">X</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_53" onclick="load_farm_thirdParty_filter('Y',53)">Y</a>
                                    </li>
                                    <li><a href="#" class="farmsorting" id="sorting_54" onclick="load_farm_thirdParty_filter('Z',54)">Z</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xs-12" style="padding-right: 5px;">
                            <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                                <p id="filterDisplay_third"></p>

                                <nav>
                                    <ul class="list-inline" id="pagination-ul_third">

                                    </ul>
                                </nav>
                            </div>
                        </div>
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
        $('.headerclose').click(function () {
            fetchPage('system/buyback/farm_management', '', 'Farms');
        });
        load_farm_filter('#', 1);
        load_farm_thirdParty_filter('#', 1);
        //getFarmManagement_tableView();
    });
    $('.select2').select2();

   /* $('#searchTask').bind('input', function () {
        startMasterSearch();
    });*/

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getFarmManagement_tableView(filtervalue,data_pagination, uriSegment);
        getThirdPartyFarmManagement_tableView(filtervalue,data_pagination, uriSegment);
    }

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function getFarmManagement_tableView(filtervalue,pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#farmmanagement_filter_frm').serializeArray();
        data.push({'name': 'farmType', 'value': 2});
        data.push({'name': 'filter_letter', 'value': filtervalue});
        data.push({'name': 'pageID', 'value': pageID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_farmManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#FarmMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getThirdPartyFarmManagement_tableView(filtervalue,pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#farmmanagement_filter_frm').serializeArray();
        // alert(farmType);
        data.push({'name': 'farmType', 'value': 1});
        data.push({'name': 'filter_letter', 'value': filtervalue});
        data.push({'name': 'pageID', 'value': pageID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_farmManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#FarmMaster_ThirdPartyFarmView').html(data['view']);
                $('#pagination-ul_third').html(data.pagination);
                $('#filterDisplay_third').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function excel_Export()
    {
        var form = document.getElementById('farmmanagement_filter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/export_farmManagement_excel'); ?>';
        form.submit();
    }

    function delete_farm_buyback(id) {
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
                    data: {'farmID': id},
                    url: "<?php echo site_url('Buyback/delete_farm_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getFarmManagement_tableView();
                            getThirdPartyFarmManagement_tableView();
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getFarmManagement_tableView();
        getThirdPartyFarmManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.farmsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#fieldofficer').val('');
        $('#farm_type').val('');
        $("#locationID").val(null).trigger("change");
        $("#subLocationID").val(null).trigger("change");
        $("#fieldofficer").val(null).trigger("change");
        $('#status').val('');
        $('#sorting_1').addClass('selected');
        getFarmManagement_tableView();
        getThirdPartyFarmManagement_tableView();
    }

    function load_farm_filter(value, id) {
        $('.farmsorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        if (value != '#') {
            $('#search_cancel').removeClass('hide');
        }
        getFarmManagement_tableView(value);
    }
    $("#locationID").change(function () {
        get_buyback_subArea($(this).val())
    });

    function load_farm_thirdParty_filter(value, id) {
        $('.farmsorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        if (value != '#') {
            $('#search_cancel').removeClass('hide');
        }
        getThirdPartyFarmManagement_tableView(value);
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

    function createfarmCompany() {
        swal(" ", "You do not have permission to create Farm master at company level,please contact your system administrator.", "error");
    }
</script>