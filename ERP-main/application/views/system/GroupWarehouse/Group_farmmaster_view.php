<?php echo head_page('Farm Master', true);
$this->load->helper('buyback_helper');
$policydescription = getPolicydescription_masterid(6);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID']);
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID']);
?>

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
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <!-- <label for="supplierPrimaryCode"> <?php /*echo $this->lang->line('common_customer_name');*/ ?></label><br>
            --><?php /*echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); */ ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">

    </div>

    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/GroupWarehouse/Group_farmmaster_create',null,'Add New Farm','BUYBACK');">
            <i class="fa fa-plus"></i> Create Farm
        </button>

    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?></label>
    </div>
    <div class="col-md-1">
        <?php echo form_dropdown('isallow',$policyvalue, $policyvalue_detail['value'], 'class="form-control" id="isallow" onchange="updatepolicy(this.value)" '); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">

        </div>
        <br>
        <div class="row">
            <ul class="nav nav-tabs" id="main-tabs">
                <li class="active"><a href="#ownFarm_group" data-toggle="tab" onclick="getOwnFarm_tableView()">Own
                        Farms</a></li>
                <li><a href="#ThirdPartyFarm_group" data-toggle="tab" onclick="getThirdPartyFarm_tableView()">Third
                        Party Farms</a></li>
            </ul>
        </div>
        <br>
        <div class="tab-content">
            <div class="tab-pane active" id="ownFarm_group">
                <div class="table-responsive">
                    <table id="ownFarm_group_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 20%">Farm Name</th>
                            <th style="min-width:20%">Area</th>
                            <th style="min-width: 20%">Sub Area</th>
                            <th style="min-width: 20%">Contact No</th>
                            <th style="min-width: 10%">Action</th>

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="ThirdPartyFarm_group">
                <div class="table-responsive">
                    <table id="thirdPartyFarm_group_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 20%">Farm Name</th>
                            <th style="min-width:20%">Area</th>
                            <th style="min-width: 20%">Sub Area</th>
                            <th style="min-width: 20%">Contact No</th>
                            <th style="min-width: 10%">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="farmerLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="farmerlink_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Farm Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName"><h4>Farm Name :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="farmerName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadCompanyFarmers">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave">Add Link
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="farmerDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="farm_master_duplicate_form"'); ?>
            <input type="hidden" name="groupfarmIDDuplicatehn" id="groupfarmIDDuplicatehn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Customer Replicate <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyFarmMasterDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedup">Replicate
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Chart of account or category or Area not linked</h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th >Company</th>
                            <th>Message</th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupWarehouse/Group_farmmaster_view', '', 'Farm Master');
        });
        getOwnFarm_tableView();

        $('#farmerlink_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'groupfarmID', 'value': groupfarmID});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Buyback/save_farmer_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled',false);
                        if (data[0] == 's') {
                            $('#farmerLinkModal').modal('hide');
                            load_all_companies_farmer();
                        }

                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        $('#btnSave').attr('disabled',false);
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#farm_master_duplicate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Buyback/save_farmer_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            load_all_companies_duplicate();
                            $('#farmerLinkModal').modal('hide');
                        }
                        if (jQuery.isEmptyObject(data[2])) {

                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#farmerLinkModal').modal('hide');
                        }


                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });
    });

    function getOwnFarm_tableView() {
        var Otable = $('#ownFarm_group_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_group_ownFarm'); ?>",
            "aaSorting": [[1, 'desc']],
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
            "aoColumns": [
                {"mData": "groupfarmID"},
                {"mData": "farmName"},
                {"mData": "groupLocation"},
                {"mData": "groupSubLocation"},
                {"mData": "phoneMobile"},
                {"mData": "edit"}
            ],
         /*   "columnDefs": [{"targets": [2], "orderable": false}, {
                "visible": false,
                "searchable": true
            }],*/

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "farmType", "value": 2});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function getThirdPartyFarm_tableView() {
        var Otable = $('#thirdPartyFarm_group_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_group_ownFarm'); ?>",
            "aaSorting": [[1, 'desc']],
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
            "aoColumns": [
                {"mData": "groupfarmID"},
                {"mData": "farmName"},
                {"mData": "groupLocation"},
                {"mData": "groupSubLocation"},
                {"mData": "phoneMobile"},
                {"mData": "edit"}
            ],
            /*   "columnDefs": [{"targets": [2], "orderable": false}, {
                   "visible": false,
                   "searchable": true
               }],*/

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "farmType", "value": 1});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function openLink_farmModal(id) {
        $('#farmerLinkModal').modal({backdrop: "static"});
        $('#companyIDdrp').val('').change();
        $('#loadCompanyFarmers').addClass('hidden');
        $('#btnSave').attr('disabled', false);
        groupfarmID = id;
        load_all_companies_farmer();
        load_farmer_header();
    }
    function load_all_companies_farmer(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupfarmID: groupfarmID},
            url: "<?php echo site_url('Buyback/load_all_companies_farmer'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadCompanyFarmers').removeClass('hidden');
                $('#loadCompanyFarmers').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }
    function clearFarmer(id) {
        $('#farmID_'+id).val('').change();
    }
    function load_farmer_header() {
        if (groupfarmID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupfarmID': groupfarmID},
                url: "<?php echo site_url('Buyback/load_farmer_group_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#farmerName').html(data['farmName']);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_duplicate_farmMaster(groupfarmID) {
        $('#farmerDuplicateModal').modal({backdrop: "static"});
        $('#groupfarmIDDuplicatehn').val(groupfarmID);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate();
    }

    function load_all_companies_duplicate(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupFarmID: $('#groupfarmIDDuplicatehn').val()},
            url: "<?php echo site_url('Buyback/load_all_farm_master_duplicate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyFarmMasterDuplicate').removeClass('hidden');
                $('#loadComapnyFarmMasterDuplicate').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function updatepolicy(value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {policyValue: value,groupPolicymasterID:6},
            url: "<?php echo site_url('Buyback/update_group_policy'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {

            }
        });
    }
</script>