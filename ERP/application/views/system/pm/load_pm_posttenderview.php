<?php

$primaryLanguage = getPrimaryLanguage();
$this->load->helper('boq_helper');
$this->lang->load('project_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$sumtotalTransCurrency = 0;
$sumtotalCostTranCurrency = 0;
$sumtotalLabourTranCurrency = 0;
$sumtotalCostAmountTranCurrency = 0;
$varianceamount = 0;
?>
<style>
    .custometbl .form-control {

        height: 20px;
        vertical-align: middle;
        padding: 0px;
    }

    .custometbl>thead>tr>th,
    .custometbl>tbody>tr>th,
    .custometbl>tfoot>tr>th,
    .custometbl>thead>tr>td,
    .custometbl>tbody>tr>td,
    .custometbl>tfoot>tr>td {
        padding: 0px;
        line-height: 1;
        padding: 5px;

    }

    .gtaskname div,
    .gtaskname {

        font-size: 10px;
        margin: 5px;

    }

    .gtaskcelldiv {
        font-size: 10px;
        margin: 5px;
    }

    td.gmajorheading div {
        margin: 5px;
        font-size: 10px;
    }

    .gresource,
    .gduration,
    .gpccomplete,
    .gstartdate div,
    .gstartdate {

        font-size: 10px;
    }

    .genddate div,
    .genddate {

        font-size: 10px;
    }

    .gpccomplete div {
        font-size: 10px;
    }

    .gduration div {
        font-size: 10px;
    }

    .gresource div {
        font-size: 10px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: left;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .fontweightcls {
        font-weight: 500;
    }

    .bootBox-btn-margin {
        margin-right: 10px;
    }
</style>

<div class="col-sm-12">
    <h5 style="text-align: center"><?php echo $header->projectnamepm . ' - ' . $header->projectCode ?></h5>
</div>
<div class="col-sm-12" style="padding: 5px;margin-bottom: 4px;background-color: rgba(175, 213, 175, 0.27);">
    <table width="100%" class="">
        <tr>
            <td><b>Project:</b></td>
            <td>
                <?php echo $header->projectnamepm ?>
            </td>
            <td><b>Segment:</b></td>
            <td>
                <?php echo $header->segmentdesc ?>
            </td>
            <td><b>Customer Name:</b></td>
            <td>
                <?php echo $header->customerName ?>
            </td>
        </tr>
        <tr>

            <td><b>
                    <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?>
                    <!--Project Start Date-->
                    :</b></td>
            <td>
                <?php echo $header->projectDateFrom ?>
            </td>
            <td><b>
                    <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?>
                    <!--Project End Date-->
                    :</b></td>
            <td>
                <?php echo $header->projectDateTo ?>
            </td>

            <td><b><?php echo $this->lang->line('common_document_date'); ?>
                    <!--Document Date-->:</b></td>
            <td>
                <?php echo $header->projectDocumentDate ?>
            </td>

        </tr>
        <tr>
            <td><b>
                    <?php echo $this->lang->line('promana_pm_cost_customer_currency'); ?>
                    <!--Customer Currency-->
                    :</b></td>
            <td>
                <?php echo $header->currencypm ?>

            </td>
        </tr>
    </table>
</div>

<div class="row editview">
    <div class="col-sm-12">
        <a onclick="modalheaderdetails(1)" class="btn btn-primary btn-xs pull-right">
            <?php echo $this->lang->line('common_create_new'); ?>
            <!--Create New--></a>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-sm-12">
        <table id="loadcosttable" class="<?php echo table_class(); ?> custometbl">
            <thead>
                <tr>
                    <th rowspan="3">Category</th>
                    <th rowspan="3">Description</th>
                    <th rowspan="3">UOM</th>
                    <th rowspan="2" colspan="3">Selling Price</th>
                    <th rowspan="3" width="70px">Markup %</th>
                    <th colspan="4">Cost</th>
                    <th>&nbsp;</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="2">Material Cost</th>
                    <th rowspan="2">Total Labour Cost</th>
                    <th rowspan="2">Total Cost</th>
                    <th rowspan="2">Variation</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <th>Qty</th>
                    <th>Unit Rate</th>
                    <th>Total Value</th>
                    <th>Unit
                        <!--cost-->
                    </th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($details) {
                    foreach ($details as $val) {
                        $subdetails = $this->db->query("SELECT
                                        `pretenderConfirmedYN`,
                                        `issendforrfq`,
                                        `confirmedYN`,
                                        `detailID`,
                                        `categoryName`,
                                        `unitID`,
                                        `unitRateTransactionCurrencyAfterConfirmedYN`,
                                        `categoryID`,
                                        `totalTransCurrencyAfterConfirmedYN`,
                                        `subCategoryID`,
                                        `subCategoryName`,
                                        `markUpAfterConfirmedYN`,
                                        `itemDescription`,
                                        `QtyAfterConfirmedYN`,
                                        `unitCostTranCurrencyAfterConfirmedYN`,
                                        `totalCostTranCurrencyAfterConfirmedYN`,
                                        `totalLabourTranCurrencyAfterConfirmedYN`,
                                        `totalCostAmountTranCurrencyAfterConfirmedYN`,
                                        `srp_erp_boq_header`.`customerCurrencyID` AS `customerCurrencyID`,
                                        `variationAmountAfterConfirmedYN`,
                                        tendertype
                                    FROM `srp_erp_boq_details`
                                    INNER JOIN `srp_erp_boq_header` ON `srp_erp_boq_header`.`headerID` = `srp_erp_boq_details`.`headerID` 
                                    WHERE `srp_erp_boq_header`.`headerID` = '{$val['headerID']}' 
                                        AND `srp_erp_boq_details`.`categoryID` = '{$val['categoryID']}'")->result_array(); ?>
                        <tr>
                            <td colspan="13"><b><?php echo $val['categoryName'] ?></b></td>
                        </tr>

                        <?php if ($subdetails) {
                            foreach ($subdetails as $value) {
                                $sumtotalTransCurrency += $value['totalTransCurrencyAfterConfirmedYN'];
                                $sumtotalCostTranCurrency += $value['totalCostTranCurrencyAfterConfirmedYN'];
                                $sumtotalLabourTranCurrency += $value['totalLabourTranCurrencyAfterConfirmedYN'];
                                $sumtotalCostAmountTranCurrency += $value['totalCostAmountTranCurrencyAfterConfirmedYN'];
                        ?>
                            <tr>
                                <td style="vertical-align: middle"><?= $value['subCategoryName'] ?></td>
                                <td style="vertical-align: middle"><?= $value['itemDescription'] ?></td>
                                <td style="vertical-align: middle" width="40px"><?= $value['unitID'] ?></td>
                                <td width="60px"><input class="form-control srmbtnhn" style="text-align: right;" min="0" type="number" name="Qty" id="Qtyposttender_<?= $value['detailID'] ?>" value="<?= $value['QtyAfterConfirmedYN'] ?>" onchange="calculateonchangqty_posttender(<?= $value['detailID'] ?>)"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="unitRateTransactionCurrency" id="unitRateTransactionCurrencyposttender_<?= $value['detailID'] ?>" value="<?= number_format((float)$value['unitRateTransactionCurrencyAfterConfirmedYN'], 2, '.', ','); ?>"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="totalTransCurrency" id="totalTransCurrencyposttender_<?= $value['detailID'] ?>" value='<?= number_format((float)$value['totalTransCurrencyAfterConfirmedYN'], 2, '.', ','); ?>'></td>
                                <td width="60px"><input class="form-control srmbtnhn" style="text-align: right;" type="number" min="0" name="markUp" id="markUpposttender_<?= $value['detailID'] ?>" value="<?= $value['markUpAfterConfirmedYN'] ?>" onchange="calculatetotalchangemarkup_posttender(<?= $value['detailID'] ?>)"></td>
                                <td width="110"><a onclick="modalcostsheet(<?= $value['categoryID'] ?>,<?= $value['subCategoryID'] ?>,<?= $value['customerCurrencyID'] ?>,<?= $value['detailID'] ?>,0,1)" class="btn btn-default btn-xs fa fa-plus srmbtnhn"></a><input class="form-control" style="width: 70px;
                                                text-align: right;float: right; text-align: right;" type="text" readonly="readonly" id="unitCostTranCurrencyposttender_<?= $value['detailID'] ?>" name="unitCostTranCurrency" id="" value="<?= number_format((float)$value['unitCostTranCurrencyAfterConfirmedYN'], 2, '.', ','); ?>"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" readonly="readonly" type="text" name="totalCostTranCurrency" id="totalCostTranCurrencyposttender_<?= $value['detailID'] ?>" value="<?= number_format((float)$value['totalCostTranCurrencyAfterConfirmedYN'], 2, '.', ','); ?>"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalLabourTranCurrencyposttender_<?= $value['detailID'] ?>" type="text" step="any" value="<?= number_format((float)$value['totalLabourTranCurrencyAfterConfirmedYN'], 2, '.', ','); ?>" name="totalLabourTranCurrency" onchange="calculatelabourcost_posttender(<?= $value['detailID'] ?>)"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalCostAmountTranCurrencyposttender_<?= $value['detailID'] ?>" type="text" step="any" value="<?= number_format((float)$value['totalCostAmountTranCurrencyAfterConfirmedYN'], 2, '.', ','); ?>" name="totalCostAmountTranCurrency" onchange="calculatetotalamount_posttender(<?= $value['detailID'] ?>)"></td>
                                <td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="varianceamountposttender_<?= $value['detailID'] ?>" type="text" step="any" value="<?= number_format((float)$value['variationAmountAfterConfirmedYN'], 2, '.', ','); ?>" name="varianceamount" onchange="varianceamount(this.value,<?= $value['detailID'] ?>)"></td>
                                <td class="pull-right">
                                    <?php if ($value['tendertype'] != 0) { ?>
                                        <?php if ($value['issendforrfq'] != 1) { ?>
                                            <span><a onclick="sendtorfq(<?= $value['detailID'] ?>) "><span title="" rel="tooltip" class="glyphicon glyphicon-ok" data-original-title="Send To RFQ"></span></a>&nbsp;|&nbsp
                                        <?php } ?>
                                        <a class="" onclick="fetch_activityplanning(<?= $value['detailID'] ?>,<?= $val['headerID'] ?>,1)"><span title="" rel="tooltip" class="fa fa-plus" data-original-title="Add"></span></a>
                                        &nbsp;|&nbsp;<a class="" onclick="deleteBoqdetail(<?= $value['detailID'] ?>)"><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php }
                        } ?>
                <?php }
                } ?>
                <tr>
                    <td style="text-align: right" colspan="5"><strong>Total</strong></td>
                    <td style="text-align: right"><strong><?= number_format((float)$sumtotalTransCurrency, 2, '.', ',') ?></strong></td>
                    <td colspan="3" style="text-align: right"><?= number_format((float)$sumtotalCostTranCurrency, 2, '.', ',') ?></strong></td>
                    <td style="text-align: right"><strong><?= number_format((float)$sumtotalLabourTranCurrency, 2, '.', ',') ?></strong></td>
                    <td style="text-align: right"><strong><?= number_format((float)$sumtotalCostAmountTranCurrency, 2, '.', ',') ?></strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right"><strong>Estimated Revenue</strong></td>
                    <td style="text-align: right"><strong><?= number_format($sumtotalTransCurrency, 2) ?></strong></td>
                    <td colspan="4" style="text-align: right"><strong>Estimated Cost</strong></td>
                    <td style="text-align: right"><strong><?= number_format($sumtotalCostAmountTranCurrency, 2) ?></strong>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right"><strong>Actual Revenue</strong></td>
                    <td style="text-align: right"><strong><?= number_format((-1 * $actualrevenue), 2) ?></strong></td>
                    <td colspan="4" style="text-align: right"><strong>Actual Cost</strong></td>
                    <td style="text-align: right"><strong><?= number_format($actualCost, 2) ?></strong></td>
                    <td></td>
                </tr>

            </tbody>
        </table>

    </div>
</div>

<script type="text/javascript">
    function calculateonchangqty_posttender(id) {
        if ($('#Qtyposttender_' + id).val() == '') {
            $('#Qtyposttender_' + id).val(0);
        }
        qty = $('#Qtyposttender_' + id).val();

        unitrate = $('#unitRateTransactionCurrencyposttender_' + id).val().replace(/,/g, "");
        /*1*/
        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrencyposttender_' + id).val(totalvalue);

        qty = $('#Qtyposttender_' + id).val();
        unit = $('#unitCostTranCurrencyposttender_' + id).val().replace(/,/g, "");
        /*2*/
        total = parseFloat(qty) * parseFloat(unit);
        $('#totalCostTranCurrencyposttender_' + id).val(total);

        /*3*/
        total = $('#totalCostTranCurrencyposttender_' + id).val().replace(/,/g, "");;

        totalcost = $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, "");;
        /*labour = parseFloat(totalcost) - parseFloat(total);

        $('#totalLabourTranCurrencyposttender_' + id).val(labour);*/

        totalTransCurrency = $('#totalTransCurrencyposttender_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrencyposttender_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalCostTranCurrency = $('#totalCostTranCurrencyposttender_' + id).val().replace(/,/g, "");
        totalCostTranCurrency = parseFloat(totalCostTranCurrency).toFixed(2);
        $('#totalCostTranCurrencyposttender_' + id).val(commaSeparateNumber(totalCostTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, "");
        totalCostTranCurrencyposttender = $('#totalCostTranCurrencyposttender_'+id).val().replace(/,/g, "");
        totalLabourTranCurrencyposttender = $('#totalLabourTranCurrencyposttender_'+id).val();

        totalCostAmountTranCurrency = ((parseFloat(totalCostTranCurrencyposttender))+(parseFloat(totalLabourTranCurrencyposttender)));
        $('#totalCostAmountTranCurrencyposttender_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));

        savecalculatetotal_posttender(id, $('#Qtyposttender_'+ id).val(), $('#unitRateTransactionCurrencyposttender_'+id).val(), $('#totalTransCurrencyposttender_' + id).val(), $('#markUpposttender_'+id).val(),
          $('#totalCostTranCurrencyposttender' + id).val(),$('#totalLabourTranCurrencyposttender_'+id).val(),$('#totalCostAmountTranCurrencyposttender_'+id).val());
        calculatetotalchangemarkup_posttender(id);
    }

    function calculatetotalchangemarkup_posttender(id) {
        if ($('#markUpposttender_' + id).val() == '') {
            $('#markUpposttender_' + id).val(0);
        }

        markup = $('#markUpposttender_' + id).val();
        totalcost = $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, "");
        qty = $('#Qtyposttender_' + id).val();
        if ($('#Qtyposttender_' + id).val() == 0) {

            unit = $('#unitCostTranCurrencyposttender_' + id).val().replace(/,/g, "");
            labour = $('#totalLabourTranCurrencyposttender_' + id).val().replace(/,/g, "");
            totalcost = parseFloat(unit) + parseFloat(labour);


            unitrate = ((parseFloat(totalcost)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrencyposttender_' + id).val(unitrate);
        } else {
            unitrate = ((parseFloat(totalcost) / parseFloat(qty)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrencyposttender_' + id).val(unitrate);
        }


        qty = $('#Qtyposttender_' + id).val();
        unitrate = $('#unitRateTransactionCurrencyposttender_' + id).val().replace(/,/g, "");

        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrencyposttender_' + id).val(totalvalue);
        savecalculatetotal_posttender(id, $('#Qtyposttender_' + id).val(), $('#unitRateTransactionCurrencyposttender_' + id).val().replace(/,/g, ""), $('#totalTransCurrencyposttender_' + id).val().replace(/,/g, ""), $('#markUpposttender_' + id).val(),
        $('#totalCostTranCurrencyposttender_' + id).val().replace(/,/g, ""), $('#totalLabourTranCurrencyposttender_' + id).val().replace(/,/g, ""), $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, ""));

        unitRateTransactionCurrency = $('#unitRateTransactionCurrencyposttender_' + id).val().replace(/,/g, "");
        unitRateTransactionCurrency = parseFloat(unitRateTransactionCurrency).toFixed(2);
        $('#unitRateTransactionCurrencyposttender_' + id).val(commaSeparateNumber(unitRateTransactionCurrency));

        totalTransCurrency = $('#totalTransCurrencyposttender_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrencyposttender_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalLabourTranCurrency = $('#totalLabourTranCurrencyposttender_' + id).val().replace(/,/g, "");
        totalLabourTranCurrency = parseFloat(totalLabourTranCurrency).toFixed(2);
        $('#totalLabourTranCurrencyposttender_' + id).val(commaSeparateNumber(totalLabourTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrencyposttender_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));
    }

    function savecalculatetotal_posttender(detailID, Qty, unitRateTransactionCurrency, totalTransCurrency, markUp, totalCostTranCurrency, totalLabourTranCurrency, totalCostAmountTranCurrency) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                detailID: detailID,
                Qty: Qty,
                unitRateTransactionCurrency: unitRateTransactionCurrency,
                totalTransCurrency: totalTransCurrency,
                markUp: markUp,
                totalCostTranCurrency: totalCostTranCurrency,
                totalLabourTranCurrency: totalLabourTranCurrency,
                totalCostAmountTranCurrency: totalCostAmountTranCurrency,
                tendertype: 1
            },
            url: "<?php echo site_url('Boq/saveboqdetailscalculation'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                setTimeout(function () {
                    fetch_posttender();
                }, 500);
                refreshNotifications(true);
            },
            error: function() {
                stopLoad();
            }
        });
    }

    function calculatelabourcost_posttender(id) {
        if ($('#totalLabourTranCurrencyposttender_' + id).val() == '') {
            $('#totalLabourTranCurrencyposttender_' + id).val(0);
        }
        total = $('#totalCostTranCurrencyposttender_' + id).val().replace(/,/g, "");
        labour = $('#totalLabourTranCurrencyposttender_' + id).val().replace(/,/g, "");
        totalcost = parseFloat(total) + parseFloat(labour);
        $('#totalCostAmountTranCurrencyposttender_' + id).val(totalcost);
        calculatetotalchangemarkup_posttender(id);
    }

    // $('input:text[name=totalCostAmountTranCurrency]').keypress(function(event) {
    //     if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
    //         event.preventDefault();
    //     }
    // });
    //
    // $('input:text[name=totalLabourTranCurrency]').keypress(function(event) {
    //     if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
    //         event.preventDefault();
    //     }
    // });

    function calculatetotalamount_posttender(id) {
        if ($('#totalCostAmountTranCurrencyposttender_' + id).val() == '') {
            $('#totalCostAmountTranCurrencyposttender_' + id).val(0);
        }
        a = $('#totalCostAmountTranCurrencyposttender_' + id).val();

        var numericReg = /^\s*?([\d\,]+(\.\d{1,3})?|\.\d{1,3})\s*$/;
        if (numericReg.test(a)) {

        } else {

        }

        totalcost = $('#totalCostAmountTranCurrencyposttender_' + id).val().replace(/,/g, "");
        total = $('#totalCostTranCurrencyposttender_' + id).val().replace(/,/g, "");
        labour = parseFloat(totalcost) - parseFloat(total);
        $('#totalLabourTranCurrencyposttender_' + id).val(labour);
        calculatetotalchangemarkup_posttender(id);
    }

    // function ccalculatetotallabour(id) {
    //     if ($('#totalLabourTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalLabourTranCurrencyposttender_' + id).val(0);
    //     }
    //
    //     if ($('#totalCostAmountTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalCostAmountTranCurrencyposttender_' + id).val(0);
    //     }
    //
    //     labour = $('#totalLabourTranCurrencyposttender_' + id).val();
    //     tct = $('#totalCostTranCurrencyposttender_' + id).val();
    //
    //     totalcost = parseFloat(tct) + parseFloat(labour);
    //     $('#totalCostAmountTranCurrencyposttender_' + id).val(totalcost);
    //     savelabourtotalcost(id, $('#totalLabourTranCurrencyposttender_' + id).val(), $('#totalCostAmountTranCurrencyposttender_' + id).val());
    //     $('#totalLabourTranCurrencyposttender_' + id).val(parseFloat($('#totalLabourTranCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalCostAmountTranCurrencyposttender_' + id).val(parseFloat($('#totalCostAmountTranCurrencyposttender_' + id).val()).toFixed(2));
    // }

    // function ccalculatetotalamount(id) {
    //     if ($('#totalLabourTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalLabourTranCurrencyposttender_' + id).val(0);
    //     }
    //
    //     if ($('#totalCostAmountTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalCostAmountTranCurrencyposttender_' + id).val(0);
    //     }
    //     tam = $('#totalCostAmountTranCurrencyposttender_' + id).val();
    //     tct = $('#totalCostTranCurrencyposttender_' + id).val();
    //
    //     lc = parseFloat(tam) - parseFloat(tct);
    //     $('#totalLabourTranCurrencyposttender_' + id).val(lc);
    //     savelabourtotalcost(id, $('#totalLabourTranCurrencyposttender_' + id).val(), $('#totalCostAmountTranCurrencyposttender_' + id).val());
    //     $('#totalLabourTranCurrencyposttender_' + id).val(parseFloat($('#totalLabourTranCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalCostAmountTranCurrencyposttender_' + id).val(parseFloat($('#totalCostAmountTranCurrencyposttender_' + id).val()).toFixed(2));
    // }

    // function calculatetotal(id) {
    //     if ($('#markUpposttender_' + id).val() == '') {
    //         $('#markUpposttender_' + id).val(0);
    //     }
    //     if ($('#Qtyposttender_' + id).val() == '') {
    //         $('#Qtyposttender_' + id).val(0);
    //     }
    //
    //     if ($('#totalLabourTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalLabourTranCurrencyposttender_' + id).val(0);
    //     }
    //
    //     if ($('#totalCostAmountTranCurrencyposttender_' + id).val() == '') {
    //         $('#totalCostAmountTranCurrencyposttender_' + id).val(0);
    //     }
    //
    //     u = $('#unitRateTransactionCurrencyposttender_' + id).val();
    //     q = $('#Qtyposttender_' + id).val();
    //     t = parseFloat(u) * parseFloat(q);
    //
    //
    //     $('#totalTransCurrencyposttender_' + id).val(t);
    //
    //     q = $('#Qtyposttender_' + id).val();
    //     c = $('#unitCostTranCurrencyposttender_' + id).val();
    //     ct = parseFloat(c) * parseFloat(q);
    //
    //     $('#totalCostTranCurrencyposttender_' + id).val(ct);
    //     calculatetotallabour(id);
    //     calculatetotalamount(id);
    //
    //     m = $('#markUpposttender_' + id).val();
    //     c = $('#unitCostTranCurrencyposttender_' + id).val();
    //     lb = $('#totalCostAmountTranCurrencyposttender_' + id).val();
    //     q = $('#Qtyposttender_' + id).val();
    //
    //     ur = ((parseFloat(lb) / parseFloat(q)) * (100 + parseFloat(m))) / 100;
    //
    //     $('#unitRateTransactionCurrencyposttender_' + id).val(ur);
    //     savecalculatetotal_posttender(id, q, u, $('#totalTransCurrencyposttender_' + id).val(), m, $('#totalCostTranCurrencyposttender_' + id).val(), $('#totalLabourTranCurrencyposttender_' + id).val(), $('#totalCostAmountTranCurrencyposttender_' + id).val());
    //     $('#unitRateTransactionCurrencyposttender_' + id).val(parseFloat($('#unitRateTransactionCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalTransCurrencyposttender_' + id).val(parseFloat($('#totalTransCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalCostTranCurrencyposttender_' + id).val(parseFloat($('#totalCostTranCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalLabourTranCurrencyposttender_' + id).val(parseFloat($('#totalLabourTranCurrencyposttender_' + id).val()).toFixed(2));
    //     $('#totalCostAmountTranCurrencyposttender_' + id).val(parseFloat($('#totalCostAmountTranCurrencyposttender_' + id).val()).toFixed(2));
    // }
</script>