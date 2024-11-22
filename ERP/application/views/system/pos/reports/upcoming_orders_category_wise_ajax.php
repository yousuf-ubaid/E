<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        font-size: medium !important;
        text-align: center !important;
    }

    .ar {
        text-align: right !important;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
/*
echo '<pre>';
print_r($itemizedSalesReport);
echo '</pre>';*/
?>
<span class="pull-right">
    <!--<button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateItemSalesReportPdf()"> <i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF </button>-->
</span>

<div id="printContainer_itemizedReport">
    <!--<div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php /*echo $companyInfo['company_name'] */?></strong></h4>
    </div>-->


    <hr style="margin:2px 0px;">


    <h3 class="text-center">Upcoming orders - Summary</h3>

    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>
            <th class="al"> Description</th>
            <th class="al"> <?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></th>
        </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($invoiceList)){
                foreach($invoiceList as $key => $list){
                    ?>
                    <tr>
                        <td colspan="2" style="font-size: small !important;"><strong><?php echo $key;?></strong></td>
                    </tr>

                <?php
                    foreach ($list as $row){
                        ?>
                                            <tr style="margin-left: 5px !important;">
                                                <td style="text-indent: 10% !important;"><?php echo $row['menuMasterDescription']  ?></td>
                                                <td class="text-right text-bold"><?php echo $row['qty']  ?></td>
                                            </tr>
                        <?php
                    }
                }
            }

            ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_itemizedReport");
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

    

</script>