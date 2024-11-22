<?php
$role = load_crew_role_drop();
$employee = load_employee_for_crew_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .f-wac input {
        font-size: 13px;
        font-weight: 600;
        text-align: right;
    }

    .f-wac {
        font-size: 13px;
        font-weight: 600;
    }

    .b-r {
        border: 1px #ff8582 solid;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483;
    }

    .w-100 {
        width: 100px !important;
    }

    .w-150 {
        width: 140px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title">
                    <button class="btn btn-link" onclick="goBack()">
                        <i class="fa fa-backward" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-link" onclick="refreshPage()">
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </button>
                    Edit Item Cost
                </h3>
                <div class="box-tools pull-right">
                    <button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button id="" class="btn btn-box-tool headerclose navdisabl"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" id="menu_item_cost_table"
                           style="width: 100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>UOM</th>
                            <th class="w-150">Existing <br/>Recipe WAC</th>
                            <th class="text-right w-150">
                                <span rel="tooltip"
                                      title="Unit Cost as per Item Master (WAC)">Item Master  <br/> WAC</span>
                            </th>
                            <th class="text-right w-150">
                                <span rel="tooltip"
                                      title="Unit Cost as per POS (WAC)">New Recipe <br/>WAC</span>
                            </th>

                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function (e) {
        //itemCostTable.state.clear();
        //itemCostTable.draw();
    })

    var siteURL = '<?php echo site_url(); ?>';
    var itemCostTable = $('#menu_item_cost_table').DataTable({
        bProcessing: true,
        bServerSide: true,
        bDestroy: true,
        bStateSave: true,
        sAjaxSource: siteURL + "/Pos_config/loadItemCostTableInfo",
        aaSorting: [[0, 'desc']],
        fnInitComplete: function () {
            $("[rel='tooltip']").tooltip();
        },
        fnDrawCallback: function (oSettings) {
            //debugger;
            /*if (oSettings.bSorted || oSettings.bFiltered) {*/
            var from = oSettings.oAjaxData.iDisplayStart;
            for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(from + 1);
                from++;
            }
            /*}*/
        },
        aoColumns: [
            {"mData": "menuDetailID"},
            {"mData": "itemSystemCode"},
            {"mData": "itemName"},
            {"mData": "defaultUnitOfMeasure"},
            {"mData": "existingWAC"},
            {"mData": "itemMasterCost"},
            {"mData": "avgCost"}
        ],
        fnServerData: function (sSource, aoData, fnCallback) {
            //aoData.push({'name': 'wareHouseAutoID', 'value': id});
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        }
    });

    function click_menu_detail_wac(menuDetailID, state) {
        var amount = $("#avg_" + menuDetailID).val();

        swal({
                title: "Are you sure?",
                text: "You want to change this New Recipe WAC",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Change it!"
            },
            function (isConfirm) {
                if (isConfirm) {
                    var avgCost = $("#avg_" + menuDetailID).val();
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: siteURL + "/Pos_config/update_menu_detail_wac",
                        data: {menuDetailID: menuDetailID, WACAmount: amount, state: state, avgCost: avgCost},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data.error, data.message);
                            if (data.error == 'e' || data.error == 'w') {
                                amount = data['avg_cost_value'];
                            } else if (data.error == 's') {
                                refreshPage();
                                //itemCostTable.draw();
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', '<br>Message: ' + errorThrown);
                            }
                        }
                    });
                } else {
                    var tmpVal = $("#" + menuDetailID).val();
                    $("#input_" + menuDetailID).val(tmpVal);
                }

            });
    }

    function update_menu_detail_wac(menuDetailID, neWACAmount, state) {
        swal({
                title: "Are you sure?",
                text: "You want to change this New Recipe WAC",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Change it!"
            },
            function (isConfirm) {
                if (isConfirm) {
                    var amount = neWACAmount.value;
                    var avgCost = $("#avg_" + menuDetailID).val();
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: siteURL + "/Pos_config/update_menu_detail_wac",
                        data: {menuDetailID: menuDetailID, WACAmount: amount, state: state, avgCost: avgCost},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data.error, data.message);
                            if (data.error == 'e' || data.error == 'w') {
                                neWACAmount.value = data['avg_cost_value'];
                            } else if (data.error == 's') {
                                //itemCostTable.draw();
                                refreshPage();
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', '<br>Message: ' + errorThrown);
                            }
                        }
                    });
                } else {
                    var tmpVal = $("#" + menuDetailID).val();
                    neWACAmount.value = tmpVal;
                }

            });


    }

    function goBack() {
        fetchPage('system/pos/settings/config-menu', '', 'Menu Master');
    }

    function refreshPage() {
        fetchPage('system/pos/settings/menu-item-cost-edit', '', 'Bulk Edit Item Cost ')
    }
</script>