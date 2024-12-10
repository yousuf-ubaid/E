<style>
    .form-inline {
        background: rgb(255, 255, 255) !important;
        margin-bottom: 0px !important;
        border-bottom: 2px solid rgb(241, 241, 241) !important;
        box-shadow: none !important;
        padding: 10px !important;
    }

    .orderTypeBtn {
        height: 55px;
        width: 150px !important;
        font-size: 21px;
    }

    #holdListDT td {
        font-size: 14px !important;
    }

    #DeliveryOrderHeldBillListDT td {
        font-size: 14px !important;
    }

</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


?>
<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#home" aria-controls="home" role="tab"
               data-toggle="tab"><i class="fa fa-list"></i> Held Bills </a></li>
        <li role="presentation">
            <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                <i class="fa fa-truck"></i> Delivery Orders
            </a>
        </li>
        <?php if ($is_wowfood_enabled) { ?>
            <li role="presentation">
                <a href="#wowfood" aria-controls="wowfood" role="tab" data-toggle="tab">
                    <i class="fa fa-truck"></i> Wow Food Orders
                </a>
            </li>
        <?php } ?>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">

            <div class="table-responsive">
                <div class="row">
                    <div class="col-md-12" style="text-align: right;">
                        <div class="col-md-6"></div>
                        <div class="col-md-3"><input type="button" id="waiterFilterbtn" class="btn btn-lg btn-warning"
                                                     style="margin: 12px 12px 0 0;width: 150px;height: 55px;font-size: 21px !important;"
                                                     value="Clear" onclick="openWaiterFilter()"/></div>
                        <div class="col-md-3"><input type="button" id="selectOrderTypeBtn"
                                                     class="btn btn-lg btn-primary"
                                                     style="margin: 12px 12px 0 0;width: 150px;height: 55px;font-size: 21px !important;"
                                                     value="Select Order Type" onclick="openOrderTypeFilter();"/></div>

                    </div>
                </div>
                <table class="<?php echo table_class_pos() ?>" id="holdListDT" style="width: 100%">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> Bill No.</th>
                        <th> <?php echo $this->lang->line('posr_hold_remarks'); ?><!--Hold Remarks--></th>
                        <th> <?php echo $this->lang->line('posr_waiter') ?><!--Staff--></th>
                        <th> <?php echo $this->lang->line('posr_hold_date'); ?><!--Hold Date--></th>
                        <th> <?php echo $this->lang->line('posr_created_date'); ?><!--Created Date--> </th>
                        <th>Amount (<?php echo $currency; ?>)</th>
                        <th><i class="fa fa-life-ring" aria-hidden="true"></i> Table</th>
                        <th> Device - Status</th>
                        <th> &nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">
            <div class="table-responsive">
                <table class="<?php echo table_class_pos() ?>" id="DeliveryOrderHeldBillListDT" style="width: 100%">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> Bill No.</th>
                        <th> <?php echo $this->lang->line('posr_hold_remarks'); ?><!--Hold Remarks--></th>
                        <th> Customer Name</th>
                        <th> Customer Telephone</th>
                        <th> <?php echo $this->lang->line('posr_hold_date'); ?><!--Hold Date--></th>
                        <th> <?php echo $this->lang->line('posr_created_date'); ?><!--Created Date--> </th>
                        <th>Amount (<?php echo $currency; ?>)</th>
                        <th> &nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="wowfood">
            <div class="table-responsive">
                <table class="<?php echo table_class_pos() ?>" id="wowfood_orders_table" style="width: 100%">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> Bill No.</th>
                        <th> <?php echo $this->lang->line('posr_hold_date'); ?><!--Hold Date--></th>
                        <th> <?php echo $this->lang->line('posr_created_date'); ?><!--Created Date--> </th>
                        <th>Amount (<?php echo $currency; ?>)</th>
                        <th> Device - Status</th>
                        <th>Wow Food Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!--Hold Invoice Preview Model-->
<div aria-hidden="true" role="dialog" tabindex="2" id="preview_hold_bill_modal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="closePreviewRecipt()" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Preview Hold Bill</h4>
            </div>
            <div class="modal-body" id="preview_hold_bill_body">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" onclick="closePreviewRecipt()"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:black; 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left"
                       aria-hidden="true"></i> <?php echo $this->lang->line('posr_back'); ?> <!--Back-->
                </button>
            </div>
        </div>
    </div>
</div>
<!--end of Hold Invoice Preview Model-->

<div aria-hidden="true" role="dialog" tabindex="2" id="orderTypeFilterModal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="orderTypeClose();" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Select Order Type</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                        <input type="button" class="btn btn-default orderTypeBtn" value="All"
                               style="margin: 5px;width: 100%;" data-otype="all" onclick="selectOrderType.call(this);">
                        <input type="button" class="btn btn-default orderTypeBtn" value="Dine In"
                               style="margin: 5px;width: 100%;" data-otype="Eat-in"
                               onclick="selectOrderType.call(this);">
                        <input type="button" class="btn btn-default orderTypeBtn" value="Take Away"
                               style="margin: 5px;width: 100%;" data-otype="Take-away"
                               onclick="selectOrderType.call(this);">
                    </div>

                </div>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <input type="button" value="Close" onclick="orderTypeClose();"/>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="2" id="orderWaiterModal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill" style="width: 600px;">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="closeWaiterFilter();" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Select Waiter</h4>
            </div>
            <div class="modal-body">
                <div class="row">


                    <?php
                    if ($pinBasedAccess) {
                        if (isset($waiters)) {
                            $waiterIndex = 1;
                            foreach ($waiters as $waiter) {


                                ?>

                                <div class="col-md-4">
                                    <input type="button" class="btn btn-default waiterFilterBtn"
                                           style="width: 100%;margin: 5px 0;height: 64px;"
                                           data-wid="<?php echo $waiter['crewMemberID']; ?>"
                                           onclick="selectWaiterFilter.call(this)"
                                           value="<?php echo $waiter['crewFirstName']; ?>"/>
                                </div>

                                <?php


                            }
                        }
                    }

                    ?>
                    <div class="col-md-4">
                        <input type="button" class="btn btn-default waiterFilterBtn"
                               style="width: 100%;margin: 5px 0;height: 64px;"
                               data-wid="clear"
                               onclick="selectWaiterFilter.call(this)"
                               value="All"/>
                    </div>

                </div>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <input type="button" value="Close" onclick="closeWaiterFilter();"/>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function (e) {
        loadHoldListPOS();
        loadDeliveryOrderHeldListPOS();
        load_wowfood_orders();
        $('#myTabs a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });

        var OrderTypeLabel = '';
        switch (terminalGlobalVariables.holdBillWindowDefaultOrderType) {
            case "all":
                OrderTypeLabel = 'All';
                break;
            case "Eat-in":
                OrderTypeLabel = 'Dine In';
                break;
            case "Take-away":
                OrderTypeLabel = 'Take Away';
                break;
        }
        $("#selectOrderTypeBtn").val(OrderTypeLabel);

        var waiterName = '';
        switch (terminalGlobalVariables.holdBillWindowDefaultWaiter) {
            case "clear":
                waiterName = 'All';
                break;
            default:
                waiterName = terminalGlobalVariables.holdBillWindowWaiterName;
        }
        $("#waiterFilterbtn").val(waiterName);
    });

    function load_wowfood_orders() {
        $('#wowfood_orders_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/get_wowfood_orders'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "invoiceID"},
                {"mData": "invoiceCode"},
                {"mData": "holdDate"},
                {"mData": "createdDate"},
                {"mData": "amount"},
                {"mData": "status"},
                {"mData": "wowfood_status"},
                {"mData": "openHoldPreview"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function loadHoldListPOS() {
        $('#holdListDT').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadHoldListPOS'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "invoiceID"},
                {"mData": "invoiceCode"},
                {"mData": "remarks"},
                {"mData": "waiterName"},
                {"mData": "holdDate"},
                {"mData": "createdDate"},
                {"mData": "amount"},
                {"mData": "diningTableDescription"},
                {"mData": "status"},
                {"mData": "openHoldPreview"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "orderType", "value": terminalGlobalVariables.holdBillWindowDefaultOrderType});
                aoData.push({"name": "waiter", "value": terminalGlobalVariables.holdBillWindowDefaultWaiter});
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

    function loadDeliveryOrderHeldListPOS() {
        $('#DeliveryOrderHeldBillListDT').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadDeliveryOrderPending'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "invoiceID"},
                {"mData": "invoiceCode"},
                {"mData": "remarks"},
                {"mData": "CustomerName"},
                {"mData": "customerTelephone"},
                {"mData": "holdDate"},
                {"mData": "createdDate"},
                {"mData": "amount"},
                {"mData": "openHold"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function previewHoldSales(id, outletID) {

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/preview_hold_sales'); ?>",
            data: {id: id, outletID: outletID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#preview_hold_bill_body").html(data);
                $('#preview_hold_bill_modal').modal('show');

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
    }

    function closePreviewRecipt() {
        $('#preview_hold_bill_modal').modal('hide');
        setTimeout(function () {
            modalFix()
        }, 450);
    }

    function openOrderTypeFilter() {
        $("#orderTypeFilterModal").modal('show');
    }

    function orderTypeClose() {
        $("#orderTypeFilterModal").modal('hide');
    }

    function openWaiterFilter() {
        $("#orderWaiterModal").modal('show');
    }

    function closeWaiterFilter() {
        $("#orderWaiterModal").modal('hide');
    }

    function selectOrderType() {
        $(".orderTypeBtn").removeClass('btn-primary');
        $(".orderTypeBtn").addClass('btn-default');
        $(this).addClass('btn-primary');
        $(this).removeClass('btn-default');
        terminalGlobalVariables.holdBillWindowDefaultOrderType = $(this).data('otype');
        loadHoldListPOS();
        orderTypeClose();
        $("#selectOrderTypeBtn").val($(this).val());
    }

    function selectWaiterFilter() {
        $(".waiterFilterBtn").removeClass('btn-primary');
        $(".waiterFilterBtn").addClass('btn-default');
        $(this).addClass('btn-primary');
        $(this).removeClass('btn-default');
        terminalGlobalVariables.holdBillWindowDefaultWaiter = $(this).data('wid');
        terminalGlobalVariables.holdBillWindowWaiterName = $(this).val();
        loadHoldListPOS();
        closeWaiterFilter();
        $("#waiterFilterbtn").val($(this).val());
    }

</script>
