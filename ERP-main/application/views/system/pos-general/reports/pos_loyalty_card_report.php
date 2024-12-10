<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .outletInfo {

    }

    .subHeadingTitle {
        margin-top: 10px;
        color: #cd3b43;
        font-size: 15px;
        font-weight: bold;
        text-decoration: underline;
    }
    .clickable {
        cursor: pointer;
    }
</style>
<?php
if (!isset($pdf)) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"> <i
                class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>
        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Gift_Card_Report.xls"
           onclick="var file = tableToExcel('container_loyalty_card_report3', 'Loyalty Card Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>
<div id="container_loyalty_card_report3">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outletID_f');
                echo get_outletFilterInfo($outletInput);

                if (isset($outletID) && !empty($outletID)) {
                    $tmpArrayout = array();
                    foreach ($outletID as $c) {
                        $tmpArrayout[] = get_outletInfo_byid2($c);
                    }
                }
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


    <h3 class="text-center">Loyalty Card Report</h3>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>

            <?php //echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> <strong>
                <?php
                $filterTo = $this->input->post('filterTo');
                $today = $this->lang->line('posr_today');
                if (!empty($filterTo)) {
                    echo '  <i> As of Date: </i><span id="to_date">'.$filterTo.'</span>';
                }
                ?>
            </strong>
        </div>
    </div>

    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">
        <div class="subHeadingTitle">Loyalty Card Details</strong></div>
        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th>Card Number</th>
                <th>Customer Name</th>
                <th>Mobile No</th>
                <th>Top Up</th>
                <th>Redeemed</th>
                <th>Balance</th>
                <th>Available Amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($gift_card_details)) {
                foreach ($gift_card_details as $row) {
                    $balance_points = number_format($row['topUpAmount']-abs($row['redeemed_amount']), 2);
                    if($exchange_rate!=null){
                        $available_amount = number_format($balance_points*$exchange_rate,2);
                    }else{
                        $available_amount = "-";
                    }
                    ?>
                    <tr>
                        <td><?php echo $row['barcode'] ?></td>
                        <td><?php echo $row['customerName'] ?></td>
                        <td><?php echo $row['customerTelephone'] ?></td>
                        <td class="text-right"><?php echo number_format($row['topUpAmount'], 2) ?></td>
                        <td class="text-right"><?php echo number_format(abs($row['redeemed_amount']), 2) ?></td>
                        <td class="text-right"><?php echo number_format($row['topUpAmount']-abs($row['redeemed_amount']), 2) ?></td>
                        <td class="text-right"><?php echo $available_amount; ?></td>
                        <td class="text-center">
                            <?php if ($row['isActive'] == 1) {
                                echo "Active";
                            } else {
                                echo "In Active";
                            } ?>
                        </td>
                    </tr>
                    <?php
                }
            }else{
                ?>
                <tr><td colspan="7">No records found</td></tr>
                <?php
            }
            ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

    <hr>
    <div style="margin:4px 0px; ">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>







<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>

    $(document).ready(function (e) {
        $("#btn_print_sales2").click(function (e) {
            $.print("#container_loyalty_card_report3");
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
    });

    $("#container_loyalty_card_report3").on('click', '.show-card-details', function (e) {
        var barcode = $(this).data('card');
        var to_date = $('#to_date').text();
        $("#pos_giftCardHistory").modal('show');
        loadCardTransactionHistory(barcode,to_date);
    });

</script>