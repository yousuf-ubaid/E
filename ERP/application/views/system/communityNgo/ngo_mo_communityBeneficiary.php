<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('community_ngo_helper');
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('CommunityNgo_beneficiary');
echo head_page($title, True);
$countries_arr = load_all_countries();
/*echo head_page('Beneficiary', false);*/

$date_format_policy = date_format_policy();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/commtNgo_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">

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

    .title {
        float: left;
        width: 170px;
        text-align: center;
        font-size: 13px;
        color: #8e8e8e;
        padding: 4px 10px 0 0;
        font-weight: bold;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        font-size: 12px !important;
        color: #b3b1b1 !important;
    }


</style>
<form id="beneficiary_filter_frm">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-2">
                <label class="title">Project</label><br>
                <?php echo form_dropdown('projectID', fetch_project_com_drop(), '', 'class="form-control select2"  id="projectID" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Country</label><br>
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" onchange="startMasterSearch()" id="countryID"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Province / State</label><br>
                <?php echo form_dropdown('province', array("" => "Select a Province"), "", 'class="form-control select2 " id="province" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Area / District</label><br>
                <?php echo form_dropdown('district', array("" => "Select a District"), "", 'class="form-control select2" id="district" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Division</label><br>
                <?php echo form_dropdown('division', array("" => "Select a Division"), "", 'class="form-control select2" id="division" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Mahalla</label><br>
                <?php echo form_dropdown('subDivision', array("" => "Select a Mahalla"), "", 'class="form-control select2" id="subDivision" onchange="startMasterSearch()"'); ?>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-2 text-center">
            &nbsp;
        </div>
        <div class="col-md-5 text-right">
            <button type="button" class="btn btn-primary" title="New Beneficiary"
                    onclick="fetchPage('system/communityNgo/ngo_mo_beneficiary_create',null,'<?php echo $this->lang->line('CommunityNgo_add_benificiary'); ?>'/*Add New Beneficiary*/,'CRM');">
                <i class="fa fa-plus"></i>
                <?php echo $this->lang->line('CommunityNgo_benificiary'); ?><!--New Beneficiary-->            </button> &nbsp;&nbsp;
            <button type="button" class="btn btn-success pull-right" title="New Community Beneficiary"
                    onclick="fetchPage('system/communityNgo/ngo_mo_ComBeneficiary_create',null,'<?php echo $this->lang->line('CommunityNgo_add_new_benificiary'); ?>'/*Add New Beneficiary*/,'NGO');">
                <i class="fa fa-plus"></i>
                <?php echo $this->lang->line('CommunityNgo_new_benificiary'); ?><!--New Community Beneficiary-->
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-2" style="margin-left: 2%;">
                        <select id="beneMemType" class="form-control select2"
                                       name="beneMemType"
                                       data-placeholder="Select Member Type" onchange="startMasterSearch();">
                            <option value=""></option>
                            <option value="1" selected>Community Members</option>
                            <option value="2">Non Community Members</option>
                            <option value="3">All Members</option>
                        </select>

                    </div>
                    <div class="col-sm-3" style="margin-left:;">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Beneficiary" id="searchTask">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-2">
                        <?php echo form_dropdown('status', array('' => $this->lang->line('common_status')/*'Status'*/, '1' => $this->lang->line('common_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirm')/*'Confirm'*/), '', 'class="form-control" id="filter_status" onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-11">
                        <div id="BeneficiaryMaster_view"></div>
                    </div>
                    <div class="col-sm-1">
                        <ul class="alpha-box">
                            <li><a href="#" class="beneficiarysorting selected" id="sorting_1"
                                   onclick="load_beneficiary_filter('#',1)">#</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_2"
                                   onclick="load_beneficiary_filter('A',2)">A</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_3"
                                   onclick="load_beneficiary_filter('B',3)">B</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_4"
                                   onclick="load_beneficiary_filter('C',4)">C</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_5"
                                   onclick="load_beneficiary_filter('D',5)">D</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_6"
                                   onclick="load_beneficiary_filter('E',6)">E</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_7"
                                   onclick="load_beneficiary_filter('F',7)">F</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_8"
                                   onclick="load_beneficiary_filter('G',8)">G</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_9"
                                   onclick="load_beneficiary_filter('H',9)">H</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_10"
                                   onclick="load_beneficiary_filter('I',10)">I</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_11"
                                   onclick="load_beneficiary_filter('J',11)">J</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_12"
                                   onclick="load_beneficiary_filter('K',12)">K</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_13"
                                   onclick="load_beneficiary_filter('L',13)">L</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_14"
                                   onclick="load_beneficiary_filter('M',14)">M</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_15"
                                   onclick="load_beneficiary_filter('N',15)">N</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_16"
                                   onclick="load_beneficiary_filter('O',16)">O</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_17"
                                   onclick="load_beneficiary_filter('P',17)">P</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_18"
                                   onclick="load_beneficiary_filter('Q',18)">Q</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_19"
                                   onclick="load_beneficiary_filter('R',19)">R</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_20"
                                   onclick="load_beneficiary_filter('S',20)">S</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_21"
                                   onclick="load_beneficiary_filter('T',21)">T</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_22"
                                   onclick="load_beneficiary_filter('U',22)">U</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_23"
                                   onclick="load_beneficiary_filter('V',23)">V</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_24"
                                   onclick="load_beneficiary_filter('W',24)">W</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_25"
                                   onclick="load_beneficiary_filter('X',25)">X</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_26"
                                   onclick="load_beneficiary_filter('Y',26)">Y</a></li>
                            <li><a href="#" class="beneficiarysorting" id="sorting_27"
                                   onclick="load_beneficiary_filter('Z',27)">Z</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var province;
    var district;
    var division;
    var subdivision;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/communityNgo/ngo_mo_communityBeneficiary', '', 'Beneficiary');
        });
        load_beneficiary_filter('#', 1);
        //getBeneficiaryManagement_tableView();

    });
    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });
    $('.select2').select2();

    function getBeneficiaryManagement_tableView(filtervalue) {
        var data = $('#beneficiary_filter_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('CommunityNgo/load_comBeneficiaryManage_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#BeneficiaryMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_beneficiary(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'benificiaryID': id},
                    url: "<?php echo site_url('CommunityNgo/delete_comBeneficiary_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getBeneficiaryManagement_tableView();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getBeneficiaryManagement_tableView();
    }

    function clearSearchFilter() {
        $('.beneficiarysorting').removeClass('selected');
        $('#searchTask').val('');
        $('#beneMemType').val('1').change();
        $('#filter_status').val('');
        $('#sorting_1').addClass('selected');
        $("#countryID").val(null).trigger("change");
        $("#province").val(null).trigger("change");
        $("#district").val(null).trigger("change");
        $("#division").val(null).trigger("change");
        $("#subDivision").val(null).trigger("change");
        $("#projectID").val(null).trigger("change");
        $('#search_cancel').addClass('hide');
        getBeneficiaryManagement_tableView();
    }

    function load_beneficiary_filter(value, id) {
        $('.beneficiarysorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        if (value != '#') {
            $('#search_cancel').removeClass('hide');
        }
        getBeneficiaryManagement_tableView(value)
    }

    $('#countryID').change(function () {
        get_beneficiary_province($(this).val())
    });

    function get_beneficiary_province(masterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {masterid: masterid},
            url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_province'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#province').empty();
                $('#district').empty();
                $('#division').empty();
                $('#subDivision').empty();
                $('.select2').select2();
                var mySelect = $('#province');
                var distric = $('#district');
                var division = $('#division');
                var mahalla = $('#subDivision');
                mySelect.append($('<option></option>').val("").html("Select a Province"));
                distric.append($('<option></option>').val("").html("Select a Distric"));
                division.append($('<option></option>').val("").html("Select a Division"));
                mahalla.append($('<option></option>').val("").html("Select a Mahalla"));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['stateID']).html(text['Description']));
                    });
                }
                if (province) {
                    mySelect.val(province).change();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#province').change(function () {
        get_beneficiary_area($(this).val())
    })

    function get_beneficiary_area(masterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {masterid: masterid},
            url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_province_area'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#district').empty();
                $('#division').empty();
                $('#subDivision').empty();
                $('.select2').select2();
                var mySelect = $('#district');
                var division = $('#division');
                var mahalla = $('#subDivision');
                mySelect.append($('<option></option>').val("").html("Select a District "));
                division.append($('<option></option>').val("").html("Select a Division "));
                mahalla.append($('<option></option>').val("").html("Select a Mahalla "));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['stateID']).html(text['Description']));
                    });
                }
                if (district) {
                    mySelect.val(district).change();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#district').change(function () {
        get_division($(this).val())
    })

    function get_division(masterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {masterid: masterid},
            url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_division'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#division').empty();
                $('.select2').select2();
                var mySelect = $('#division');
                mySelect.append($('<option></option>').val("").html("Select a Division "));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['stateID']).html(text['Description']));
                    });
                }
                if (division) {
                    mySelect.val(division).change();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#division').change(function () {
        get_sub_division($(this).val())
    })

    function get_sub_division(masterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {masterid: masterid},
            url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_sub_division'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#subDivision').empty();
                $('.select2').select2();
                var mySelect = $('#subDivision');
                mySelect.append($('<option></option>').val("").html("Select a Mahalla "));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['stateID']).html(text['Description']));
                    });
                }
                if (subdivision) {
                    mySelect.val(subdivision).change();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>