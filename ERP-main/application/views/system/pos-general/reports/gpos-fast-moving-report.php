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

    .ac {
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
/*echo '<pre>';
print_r($deliveryPersonReport);
echo '</pre>';*/


?>

<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
<a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Fast_Moving_Items_Report.xls"
   onclick="var file = tableToExcel('printContainer_itemizedSalesReport', 'Fast Moving Items Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<div id="printContainer_itemizedSalesReport">

    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>

    <div class="row">

        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                <br/><?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>

    <hr style="margin:2px 0px;">


    <h3 class="text-center">Fast Moving Items Report </h3>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
                <strong>Filters <i class="fa fa-filter"></i></strong><br/>
                    Date  <strong>
                    <?php
                    $filterFrom = $this->input->post('startdate');
                    $filterTo = $this->input->post('enddate');
                    $from = $this->lang->line('common_from');
                    $to = $this->lang->line('common_to');
                    $today = $this->lang->line('posr_today');
                    if (!empty($filterFrom) && !empty($filterTo)) {
                        echo '  <i>' . $from . '<!--From--> : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;/*To*/
                    } else {

                    }
                    ?>
                </strong>
        </div>
    </div>




    <br>
    <table class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th class="">Main Category</th>
            <th class="">Sub Category</th>
            <th class="">Sub Sub Category</th>
            <th class="">Barcode</th>
            <th class="">Item Code</th>
            <th class="">Item Name</th>
            <th class="">UOM</th>
            <th class="">Qty</th>
            <th class="">Total Sales</th>
            <th class="">Qty In Hand</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalSales = 0;
        $i = 1;
        if (!empty($fastMovingRpt)) {
            foreach ($fastMovingRpt as $val) {
                $d = $val['Decimlpls'];
                $totalSales += $val['totalSales'];
                ?>
                <tr>
                    <td class=""> <?php echo $val['mainCategory'] ?> </td>
                    <td class=""> <?php echo $val['subCategory'] ?> </td>
                    <td class=""> <?php echo $val['subsubCategory'] ?> </td>
                    <td class=""> <?php echo $val['barcode'] ?> </td>
                    <td class=""> <?php echo $val['seconeryItemCode'] ?> </td><!--itemSystemCode-->
                    <td class=""> <?php echo $val['itemDescription'] ?> </td>
                    <td class=""> <?php echo $val['UOM'] ?> </td>
                    <td class=""> <?php echo $val['transactionQTY'] ?> </td>
                    <td class="ar"> <?php echo number_format($val['totalSales'], $d); ?> </td>
                    <td class=""> <?php echo $val['currentStock'] ?> </td>
                </tr>

                <?php
                $i = $i + 1;
            }
        } else {
            ?>
            <tr>
                <td class="alin" colspan="10">
                    <?php echo $this->lang->line('common_records_not_found'); ?><!--Records not Found--></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:12px !important;" class="t-foot">
            <td colspan="8"><strong> Total </strong></td>
            <td class="ar">
                <strong><?php echo number_format($totalSales, $d); ?></strong>
            </td>
            <td>&nbsp;</td>


        </tr>
        </tfoot>

    </table>
    <br>

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
        $(".pcCurrentTime").html(date);
    })

    function generateDeliveryPersonPdf() {
        var form = document.getElementById('frm_deliverypersonReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadDeliveryPersonReportPdf'); ?>';
        form.submit();
    }

</script>