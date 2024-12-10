<style>
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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th> </th>
                <th style="text-align: left" class="theadtr"> </th>
                <th style="text-align: left" class="theadtr"> </th>
                <!-- <th style="text-align: center" class="theadtr" colspan="2">Subscription Amount</th>
                <th style="text-align: center" class="theadtr"  colspan="2">Implementation Amount</th> -->
                <th style="text-align: center" class="theadtr" colspan="2">Amount</th>
                <th style="text-align: center" class="theadtr">Action</th>
            </tr>
            <tr>
                <th>#</th>
                <th style="text-align: center" class="theadtr">Product Name</th>
                <th style="text-align: center" class="theadtr">Product Description</th>
                <!-- <th style="text-align: left" class="theadtr">Transaction Amount	</th>
                <th style="text-align: left" class="theadtr">Local Amount</th>
                <th style="text-align: left" class="theadtr">Transaction Amount	</th>
                <th style="text-align: left" class="theadtr">Local Amount</th> -->
                <th style="text-align: center" class="theadtr">Transaction Amount	</th>
                <th style="text-align: center" class="theadtr">Local Amount</th>
                <th style="text-align: center" class="theadtr"> </th>
            </tr>


            </thead>
            <tbody>
            <?php
            $x = 1;
            $total = 0;
            $totallocal=0;
            $totalcurr=0;
            $reportingCurrency = '';
            $totallocalimpleentation = 0;
            $totallocalsubscription = 0;
            foreach ($header as $val) {

                //Only when currency settings not presents
                $val['companyLocalCurrencyExchangeRate']  = ($val['companyLocalCurrencyExchangeRate'] != 0) ? $val['companyLocalCurrencyExchangeRate'] : 1;

                $transactioncurrency = $val['transactioncurrency'];
                $transactionprice = ($val['price'] / $val['transactionExchangeRate']);

                $localcurrency =  $val['currencycodelocal'];
                $localcurrencyprice = ($val['price'] / $val['companyLocalCurrencyExchangeRate']);


                $localcurrencyimplemetation =  $val['currencycodelocal'];
                $localcurrencypriceimplemetation = ($val['ImplementationAmount'] / $val['companyLocalCurrencyExchangeRate']);

                $transactioncurrencyimplemetation =  $val['transactioncurrency'];
                $transactioncurrencypriceimplemetation = ($val['ImplementationAmount'] / $val['transactionExchangeRate']);

                $transactioncurrencysubscription =  $val['transactioncurrency'];
                $transactioncurrencypricesubscription = ($val['subscriptionAmount'] / $val['transactionExchangeRate']);
                $localcurrencysubscription =  $val['currencycodelocal'];
                $localcurrencypricesubscription = ($val['subscriptionAmount'] / $val['companyLocalCurrencyExchangeRate']);
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x; ?></a></td>
                    <td class="mailbox-name" style="text-align: left;width: 20%;"><a href="#"><?php echo $val['productName']; ?></a></td>
                    <td class="mailbox-name" style="width: 14%;"><a href="#"><?php echo $val['productDescription']; ?></a></td>
<!-- 
                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrencysubscription .':'. number_format($transactioncurrencypricesubscription, $val['transactionCurrencyDecimalPlaces']) ?></a></td>


                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrencysubscription .':'. number_format($localcurrencypricesubscription, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>


                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrencyimplemetation .':'. number_format($transactioncurrencypriceimplemetation, $val['transactionCurrencyDecimalPlaces']) ?></a>
                    </td>
                   <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrencyimplemetation .':'. number_format($localcurrencypriceimplemetation, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>
 -->

                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrency .':'. number_format($transactionprice, $val['transactionCurrencyDecimalPlaces']) ?></a></td>
                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrency .':'. number_format($localcurrencyprice, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>
                   <!-- <td class="mailbox-name" style="text-align: right">
                        <a href="#"><?php /*echo  number_format($reportingPrice, $val['companyReportingCurrencyDecimalPlaces']) */?></a>
                    </td>-->
                    <td class="mailbox-attachment taskaction_td"><span class="pull-right">
                            <a onclick="edit_opportunity_product(<?php echo $val['opportunityProductID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-pencil" style="color:blue;"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_opportunity_product(<?php echo $val['opportunityProductID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                    </td>
                </tr>
                <?php
                $x++;
                $total += isset($reportingPrice) ? $reportingPrice : 0;
                $totallocal += $localcurrencyprice;
                $totallocalimpleentation += $localcurrencypriceimplemetation;
                $totallocalsubscription += $localcurrencypricesubscription;
                $totalcurr += $transactionprice;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total</td>

                <td style="text-align: right"></td>
                <!-- <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocalsubscription,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td>

                <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocalimpleentation,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td> -->

                <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocal,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td>
                <td>&nbsp;</td>

            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO PRODUCTS TO DISPLAY.</div>
    <?php
}
?>
<br>
<div class="row" id="show_add_product_button">
    <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> Lead Products </h4></div>
</div>
<br>
<?php
if (!empty($lead)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th> </th>
                <th style="text-align: left" class="theadtr"> </th>
                <th style="text-align: left" class="theadtr"> </th>
                <!-- <th style="text-align: center" class="theadtr" colspan="2">Subscription Amount</th>
                <th style="text-align: center" class="theadtr"  colspan="2">Implementation Amount</th> -->
                <th style="text-align: center" class="theadtr" colspan="2">Amount</th>
            </tr>
            <tr>
                <th>#</th>
                <th style="text-align: left" class="theadtr">Product Name</th>
                <th style="text-align: left" class="theadtr">Product Description</th>
                <!-- <th style="text-align: left" class="theadtr">Transaction Amount	</th>
                <th style="text-align: left" class="theadtr">Local Amount</th>
                <th style="text-align: left" class="theadtr">Transaction Amount	</th>
                <th style="text-align: left" class="theadtr">Local Amount</th> -->
                <th style="text-align: left" class="theadtr">Transaction Amount	</th>
                <th style="text-align: left" class="theadtr">Local Amount</th>
            </tr>

            </thead>
            <tbody>
            <?php
            $x = 1;
            $total = 0;
            $totallocal=0;
            $totalcurr=0;
            $reportingCurrency = '';
            $totallocalimpleentation = 0;
            $totallocalsubscription = 0;
            foreach ($lead as $val) {
                $transactioncurrency = $val['transactioncurrency'];
                $transactionprice = ($val['price'] / $val['transactionExchangeRate']);

                $localcurrency =  $val['currencycodelocal'];
                $localcurrencyprice = ($val['price'] / $val['companyLocalCurrencyExchangeRate']);


                $localcurrencyimplemetation =  $val['currencycodelocal'];
                $localcurrencypriceimplemetation = ($val['ImplementationAmount'] / $val['companyLocalCurrencyExchangeRate']);

                $transactioncurrencyimplemetation =  $val['transactioncurrency'];
                $transactioncurrencypriceimplemetation = ($val['ImplementationAmount'] / $val['transactionExchangeRate']);

                $transactioncurrencysubscription =  $val['transactioncurrency'];
                $transactioncurrencypricesubscription = ($val['subscriptionAmount'] / $val['transactionExchangeRate']);
                $localcurrencysubscription =  $val['currencycodelocal'];
                $localcurrencypricesubscription = ($val['subscriptionAmount'] / $val['companyLocalCurrencyExchangeRate']);

                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x; ?></a></td>
                    <td class="mailbox-name" style="text-align: left;width: 20%;"><a href="#"><?php echo $val['productName']; ?></a></td>
                    <td class="mailbox-name" style="width: 14%;"><a href="#"><?php echo $val['productDescription']; ?></a></td>

                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrencysubscription .':'. number_format($transactioncurrencypricesubscription, $val['transactionCurrencyDecimalPlaces']) ?></a></td>


                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrencysubscription .':'. number_format($localcurrencypricesubscription, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>


                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrencyimplemetation .':'. number_format($transactioncurrencypriceimplemetation, $val['transactionCurrencyDecimalPlaces']) ?></a>
                    </td>
                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrencyimplemetation .':'. number_format($localcurrencypriceimplemetation, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>


                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $transactioncurrency .':'. number_format($transactionprice, $val['transactionCurrencyDecimalPlaces']) ?></a></td>
                    <td class="mailbox-name" style="text-align: right;width: 15%"><a href="#"><?php echo $localcurrency .':'. number_format($localcurrencyprice, $val['companyLocalCurrencyDecimalPlaces']) ?></a></td>
                    <!-- <td class="mailbox-name" style="text-align: right">
                        <a href="#"><?php /*echo  number_format($reportingPrice, $val['companyReportingCurrencyDecimalPlaces']) */?></a>
                    </td>-->

                </tr>
                <?php
                $x++;
                $total += $reportingPrice;
                $totallocal += $localcurrencyprice;
                $totalcurr += $transactionprice;
                $totallocalimpleentation += $localcurrencypriceimplemetation;
                $totallocalsubscription += $localcurrencypricesubscription;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total</td>

                <td style="text-align: right"></td>
                <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocalsubscription,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td>

                <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocalimpleentation,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td>

                <td style="text-align: right"> </td>
                <td style="text-align: right"><?php echo $localcurrency .' ('. format_number($totallocal,$val['companyLocalCurrencyDecimalPlaces']).')' ?></td>

            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO PRODUCTS TO DISPLAY.</div>
    <?php
}
?>



<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>