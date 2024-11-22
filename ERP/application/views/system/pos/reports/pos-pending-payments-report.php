<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        text-align: left !important;
    }

    .ar {
        text-align: right !important;
    }

    tbody td {
        font-size: 12px !important;
        padding: 1px 10px;
    }

    thead th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

    tfoot th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

    .alin {
        text-align: right;
        padding-right: 3px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$time = time();
?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePendngPayPdf()"><i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF
    </button>
    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Franchise_Report.xls"
       onclick="var file = tableToExcel('printContainer_<?php echo $time ?>', 'Franchise Report'); $(this).attr('href', file);">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </a>
</span>
<div id="printContainer_<?php echo $time ?>">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outlet');
                echo get_outletFilterInfo($outletInput);
                ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                <br/><?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>
    <hr style="margin:2px 0px;">
    <h3 class="text-center">Pending Payments Report</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> : <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $from = $this->lang->line('common_from');
            $to = $this->lang->line('common_to');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo '  <i>' . $from . ' : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                echo $curDate . ' (' . $today . ')';
            }
            ?>
        </strong>
        </div>
    </div>


    <br>

    <table class="" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class=""> #</th>
            <th class="">Bill No</th>
            <th class="">Type</th>
            <th class="">Customer Name</th>
            <th class="">Telephone Number</th>
            <th class="">Invoice Code</th>
            <th class="">Date</th>
            <th class="">Invoice Amount</th>
            <th class="">Received Amount</th>
            <th class="">Balance Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        $invTot = 0;
        $recptTot = 0;
        $balTot = 0;
        if (!empty($penPayReport)) {
            foreach ($penPayReport as $val) {
                if(number_format($val['transactionAmount']-$val['receiptamnts'], $d)>0){
                    if($val['invoiceCode']=='Delivery Order'){
                        $invoiceCode='-';
                        $type='Delivery Order';
                    }else{
                        $invoiceCode=$val['invoiceCode'];
                        $type='Credit Sales';
                    }
                    $i += 1;
                    ?>
                    <tr>
                        <td class="" style="text-align: center;"><?php echo $i ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['billNo'] ?></td>
                        <td class="" style="text-align: center;"><?php echo $type ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['customerDetal'] ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['customerTelephone'] ?></td>
                        <td class="" style="text-align: center;"><a onclick="documentPageView_modal('CINV','<?php echo $val['invID'] ?>')"><?php echo $invoiceCode ?></a></td>
                        <td class=""><?php echo $val['dat'] ?></td>
                        <td class="alin"><?php echo number_format($val['transactionAmount'], $d); ?></td>
                        <td class="alin" style="cursor: pointer">
                            <?php if($val['receiptamnts']>0 && $val['invoiceCode']!='Delivery Order'){?>
                                <a onclick="openPendingPaymentDD(<?php echo $val['invoiceAutoID'] ?>)"><?php echo number_format($val['receiptamnts'], $d); ?></a>
                            <?php }else{?>
                                <?php echo number_format($val['receiptamnts'], $d); ?>
                            <?php }?>

                        </td>
                        <td class="alin"><?php echo number_format($val['transactionAmount']-$val['receiptamnts'], $d); ?></td>
                    </tr>
        <?php
                    $invTot+=$val['transactionAmount'];
                    $recptTot+=$val['receiptamnts'];
                    $balTot+=$val['transactionAmount']-$val['receiptamnts'];
                }
            }
        } else {
            ?>
            <tr>
                <td class="" style="text-align: center;" colspan="9">
                    <?php echo $this->lang->line('common_records_not_found'); ?><!--Records not Found--></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
            <tr style="font-size:12px !important;" class="t-foot">
                <td colspan="7" style="padding-right:2px;font-weight: bold; text-align: right"><strong>
                        <?php echo $this->lang->line('common_total'); ?><!--Total--></strong></td>
                <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($invTot, $d); ?></strong></td>
                <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($recptTot, $d); ?></strong></td>
                <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($balTot, $d);  ?></strong></td>
            </tr>
        </tfoot>

    </table>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_<?php echo $time ?>");
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
        $(".pcCurrentTime").html(date);
    })
    function generatePendngPayPdf() {
        var form = document.getElementById('pendngPaymnt_rpt_form');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadPendingPaymentsReportPdf'); ?>';
        form.submit();
    }
</script>