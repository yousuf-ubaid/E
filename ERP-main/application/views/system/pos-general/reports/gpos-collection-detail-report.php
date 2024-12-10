<style>
    .customPad {
        padding: 3px 0px;
    }

    /*.al {
        text-align: left !important;
    }*/

    .ar {
        text-align: right !important;
    }

    .alin {
        text-align: center !important;
    }
</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$c_info = get_companyInfo();
$d = !empty($c_info['company_default_decimal']) ? $c_info['company_default_decimal'] : 2;

?>

<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>

    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Collection_Detail_Report.xls"
       onclick="var file = tableToExcel('printContainer_itemizedSalesReport', 'Collection Detail Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </a>
</span>
<div id="printContainer_itemizedSalesReport">
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <h4 style="margin:0px;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
    </div>
    <div style="margin:4px 0px; text-align: center;">
        <?php
        $cash = $this->lang->line('posr_cashier');
        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ': ';/*Cashier*/

            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = $cashierTmp[$c];
            }
            echo join(', ', $tmpArray);
        }

        $paymentCard = 0;
        if (!empty($paymentglConfigMaster)) {
            foreach ($paymentglConfigMaster as $config) {
                $paymentCard++;
            }
        }
        ?>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date -->: <strong>
                    <?php
                    $filterFrom = $this->input->post('startdate');
                    $filterTo = $this->input->post('enddate');
                    $from = $this->lang->line('common_from');
                    $to = $this->lang->line('common_to');
                    $today = $this->lang->line('posr_today');
                    if (!empty($filterFrom) && !empty($filterTo)) {
                        echo '  <i>' . $from . '<!--From--> : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;/*To*/
                    } else {
                        $curDate = date('d-m-Y');
                        echo $curDate . ' (' . $today . ')';
                    }
                    ?>
                </strong>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php echo $this->lang->line('common_date'); ?><!--Date-->: <strong><?php echo date('d/m/Y'); ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>

    <br>
    <div style="overflow-x:auto;">
        <table class="<?php echo table_class() ?>" >
            <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Bill No</th>
                <th rowspan="2">Payment Date</th>
                <th rowspan="2">Customer - Telephone</th>
                <th rowspan="2">Bill Amount</th>
                <th rowspan="2">Paid Amount </th>
                <th colspan="<?php echo $paymentCard; ?>">Paid Description</th>
                <th rowspan="2">Balance</th>
            </tr>
            <tr>
                <?php
                if (!empty($paymentglConfigMaster)) {
                    foreach ($paymentglConfigMaster as $config) {
                        echo "<th>".$config['description']."</th>";
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody >
            <?php
            $array_total = array();
            if (!empty($collection_detail)) {
                $i = 1;

                $total_billAmount = 0;
                $total_paid = 0;
                $total_balance = 0;
                foreach ($collection_detail as $collection) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo $i;
                            $i++;
                            ?>
                        </td>
                        <td>
                            <!--<a href="#" onclick="viewDrillDown_report(<?php /*echo $collection['invoiceID'] */?>)">
                                <?php /*echo $collection['invoiceCode'] */?>
                            </a>-->
                            <?php echo $collection['invoiceCode'] ?>
                        </td>
                        <td><?php echo $collection['paymentDate'] ?> </td>
                        <td><?php echo $collection['customerInfo'] ?> </td>
                        <td class="ar">
                            <?php
                            echo number_format(($collection['billAmount']-$collection['promotiondiscountAmount']-$collection['discountAmount']-$collection['generalDiscountAmount']), $d);
                            $total_billAmount += $collection['billAmount'];
                            ?>
                        </td>
                        <td class="ar">
                            <?php
                            echo number_format($collection['amountPaid'], $d);
                            $total_paid += $collection['amountPaid'];
                            ?>
                        </td>

                        <?php
                        if (!empty($paymentglConfigMaster)) {
                            foreach ($paymentglConfigMaster as $config) {
                                $newName = str_replace(' ', '', $config['description']);
                                echo "<td class='text-right'>".number_format($collection[$newName], $d)."</td>";
                                $array_total[$newName][] = $collection[$newName];
                            }
                        }
                        ?>

                        <td class="ar">
                            <?php
                            echo number_format($collection['balance'], $d);
                            $total_balance += $collection['balance'];
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tfooter>
                    <tr>
                        <th colspan="4" class="ar"> Total</th>
                        <th class="ar"><?php echo number_format($total_billAmount, $d); ?></th>
                        <th class="ar"><?php echo number_format($total_paid, $d); ?></th>
                        <?php
                        if (!empty($paymentglConfigMaster)) {
                            foreach ($paymentglConfigMaster as $config) {
                                $newName = str_replace(' ', '', $config['description']);
                                echo "<th class='text-right'>" . number_format(array_sum($array_total[$newName]), $d) . "</th>";

                            }
                        }
                        ?>


                        <th class="ar"><?php echo number_format($total_balance, $d); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfooter>
                <?php
            }
            ?>

            </tbody>

        </table>
    </div>

    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!-- Report print by--> : <?php echo current_user() ?>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_itemizedSalesReport");
        });

        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = hour + ":" + minute + " " + ampm;
        $("#pcCurrentTime").html(date);
    })

    function generateDeliveryPersonPdf() {
        var form = document.getElementById('frm_deliverypersonReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadDeliveryPersonReportPdf'); ?>';
        form.submit();
    }

    function viewDrillDown_report(invoiceID,outletID) {
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate_salesDetailReport'); ?>",
                data: {invoiceID: invoiceID, outletID:outletID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#rpos_print_template').modal('show');
                    $("#pos_modalBody_posPrint_template").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
        } else {
            myAlert('e', 'Load the invoice and click again.')
        }
    }

</script>

<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 500px">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="min-height: 400px;">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-block btn-sm" data-dismiss="modal">
                    <i class="fa fa-close text-red" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
