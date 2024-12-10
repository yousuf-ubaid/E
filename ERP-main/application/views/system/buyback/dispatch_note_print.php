<style>
    .bgcolour {
        background-color: #00a65a;
        margin-top: 3%;
    }
   .bgcolourconfirm {
        background-color: #f9ac38;
        margin-top: 3%;
    }
    .labellabelbuyback {
        color: #fff;
        height: 21px;
        width: 90px;
        position: absolute;
        font-weight: bold;
        padding-left: 10px;
        padding-top: 0px;
        top: 10px;
        right: -59px;
        margin-right: 0;
        border-radius: 3px 3px 0 3px;
        box-shadow: 0 3px 3px -2px #ccc;
        text-transform: capitalize;
    }
    .labellabelbuyback:after {
        top: 20px;
        right: 0;
        border-top: 4px solid #1f1d1d;
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        position: absolute;
    }
    .item-labelapproval {
        color: #fff;
        height: 21px;
        width: 90px;
        position: absolute;
        font-weight: bold;
        padding-left: 10px;
        padding-top: 0px;
        top: 10px;
        right: -20px;
        margin-right: 0;
        border-radius: 3px 3px 0 3px;
        box-shadow: 0 3px 3px -2px #ccc;
        text-transform: capitalize;
    }
    .item-labelapproval:after {
        top: 20px;
        right: 0;
        border-top: 4px solid #1f1d1d;
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        position: absolute;
    }
</style>
<?php
echo fetch_account_review(true, true, $approval);
if(!empty($chicks)){
    $totalChicksGiven = $chicks['chicksTotal'];
}
?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <!-- <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php /*echo mPDFImage.$this->common_data['company_data']['company_logo']; */?>">
                        </td>
                    </tr>
                </table>
            </td>-->
            <td>
                <table>
                    <tr>
                        <td style="text-align: center;">
                            <!--<h3><strong><?php /*echo $this->common_data['company_data']['company_name']; */?>.</strong></h3>
                            <p><?php /*echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; */?></p>
                            <br>-->
                            <h4 >Dispatch Note</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php if($extra['master']['approvedYN']== 1 && $extra['master']['confirmedYN']== 1) {
    echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="labellabelbuyback file bgcolour">Approved</div>
    </article>';
}?>
<?php if($extra['master']['confirmedYN']==1 && $extra['master']['approvedYN']!= 1 && $size!=1) {
    echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="labellabelbuyback file bgcolourconfirm">Confirmed</div>
    </article>';
}?>
<?php if($size==1){?>
    <?php if($extra['master']['confirmedYN']==1 && $extra['master']['approvedYN']!= 1) {
        echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="item-labelapproval file bgcolourconfirm">Confirmed</div>
    </article>';
    }?>
<?php }?>
<hr style="margin-top: -1%">
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td ><strong>DPN Number</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['documentSystemCode']; ?></td>

            <td><strong>DPN Date</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
            <td ><strong>Issued From</strong></td>
            <td ><strong>:</strong></td>
            <td>
                <?php if ($extra['master']['dispatchType'] == 1) {
                                echo 'Direct';
                            } else {
                                echo 'Load Change';
                            }; ?>
            </td>

            <td><strong>Reference Number</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNo']; ?></td>
        </tr>
      <tr>
            <td ><strong>Location</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['wareHouseLocation']; ?></td>

            <td><strong>Farmer</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['farmName']; ?></td>
        </tr>
        <tr>
            <td ><strong>Delivered Date </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['dispatchedDate']; ?></td>

            <td><strong>Address</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['farmAddress']; ?></td>
        </tr>
        <tr>
            <td ><strong>Currency </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

            <td><strong>Phone</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['farmTelephone']; ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong>Narration </strong></td>
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['Narration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['Narration']; ?>
            </td>


            <td ><strong>Batch </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['batchCode']; ?></td>

        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 10%">Item Code</th>
            <th class='theadtr' style="min-width: 15%">Item Description</th>
            <th class='theadtr' style="min-width: 10%">UOM</th>
            <th class='theadtr' style="min-width: 5%">Qty</th>
            <th class='theadtr' style="min-width: 12%">Unit Cost</th>
            <th class='theadtr' style="min-width: 12%">Net Amount</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;
        $received_total = 0;
        if (!empty($extra['detail'])) {
            for ($i = 0; $i < count($extra['detail']); $i++) {
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
               if($extra['detail'][$i]['isSubitemExist'] == 1){
                   echo '<td><a style="cursor:pointer;" onclick="view_dispatch_subItems(' . $extra['detail'][$i]['dispatchDetailsID'] . ')" >' . $extra['detail'][$i]['itemSystemCode'] . '</a></td>';
               } else{
                   echo '<td>' . $extra['detail'][$i]['itemSystemCode'] . '</td>';
               }
                echo '<td>' . $extra['detail'][$i]['itemDescription'] . '</td>';
                echo '<td class="text-center">' . $extra['detail'][$i]['defaultUOM'] . '</td>';
                echo '<td class="text-right">' . $extra['detail'][$i]['qty'] . '</td>';
                echo '<td class="text-right">' . format_number($extra['detail'][$i]['unitTransferCost'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';

                echo '<td class="text-right">' . format_number(($extra['detail'][$i]['qty'] * $extra['detail'][$i]['unitTransferCost']), $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '</tr>';
                $requested_total += ($extra['detail'][$i]['qty'] * $extra['detail'][$i]['unitTransferCost']);
                $received_total += ($extra['detail'][$i]['totalTransferCost']);
            }
        } else {
            echo '<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="6">Total <span
                    class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($requested_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <h4 class="modal-title" align="center"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;Feed Summary</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed table-row-select" style="width: 50%" align="right">
                <tbody>
                <thead>
                <tr>
                    <th style="min-width: 5px">#</th>
                    <th style="min-width: 50px">Item</th>
                    <th style="min-width: 50px">Qty R</th>
                    <th style="min-width: 10px">Qty D</th>
                </tr>
                </thead>
                <?php
                if (!empty($feed_header)) {
                    $x=1;
                    $qtrtot =0;
                    foreach ($feed_header as $row) {
                        $qtrtot = ($row["feedAmount"] * $totalChicksGiven) / 50;

                        echo "<tr>";
                        echo "<td>" . $x. "</td>";
                        echo "<td>" . $row['feedName'];
                        if($row['qtyD'] > $qtrtot){
                            echo '&nbsp;&nbsp;<span class="label label-danger" style="font-size: 10px">Over Feeded</span>';
                        }
                        echo "</td>";
                        echo "<td style='text-align: right'>" .$qtrtot . "</td>";
                        echo "<td style='text-align: right'>" .$row['qtyD']. "</td>";
                        echo "</tr>";


                        $x++;
                    }

                } else {
                    echo '<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>';
                }
                ?>

                </tbody>
                <tfoot>

                </tfoot>
            </table>

        </div>
    </div>

    <?php /*  foreach ($feed_header as $row) {
        $qtrtot = ($row["feedAmount"] * $totalChicksGiven) / 50;

        if($row['qtyD'] > $qtrtot)
        {
            echo ' <div class="col-sm-12" align="center" style="padding-right: 12%;margin-top: -5%;">
                        <div class="label label-danger" style="padding:3.2em 0.8em 2.3em; font-size: 15px;" > Over Feeded </div>
                   </div>';
        }
    }
    */?>
</div>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:50%;">
                <?php if ($extra['master']['approvedYN']!=1  && $extra['master']['confirmedYN']) { ?>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td ><strong>Confirmed By </strong></td>
                            <td ><strong>:</strong></td>
                            <td><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
            <td style="width:70%;">
                &nbsp;
            </td>
        </tr>
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:50%;">
                <?php if ($extra['master']['approvedYN']!=1  && $extra['master']['confirmedYN']) { ?>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td ><strong>Confirmed By </strong></td>
                            <td ><strong>:</strong></td>
                            <td><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
            <td style="width:70%;">
                &nbsp;
            </td>
        </tr>
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:60%;">
                <?php if ($extra['master']['approvedYN']) { ?>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td ><strong>Approved By </strong></td>
                            <td ><strong>:</strong></td>
                            <td><?php echo $extra['master']['approvedbyEmpName']; ?> / <?php echo $extra['master']['approvedDate']; ?> </td>

                        </tr>
                        <tr>
                            <td ><strong>Confirmed By </strong></td>
                            <td ><strong>:</strong></td>
                            <td><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
            <td style="width:70%;">
                <?php
                if (!empty($extra['addon'])) { ?>
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Addons
                                    Details</strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'>Addon Catagory</th>
                            <th class='theadtr'>Reference No</th>
                            <th class='theadtr'>Booking Amount</th>
                            <th class='theadtr'>Amount ( <?php echo $extra['master']['transactionCurrency']; ?> )</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $x = 1;
                        $total_amount = 0;
                        foreach ($extra['addon'] as $value) {
                            echo '<tr>';
                            echo '<td>' . $x . '.</td>';
                            echo '<td>' . $value['addonCategoryName'] . '</td>';
                            echo '<td>' . $value['referenceNo'] . '</td>';
                            echo '<td class="text-right">' . $value['transactionCurrency'] . ' : ' . format_number($value['total_amount'], $value['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '<td class="text-right">' . format_number($value['total_amount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '</tr>';
                            $x++;
                            $total_amount += $value['total_amount'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-right sub_total">Total <span
                                    class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?>)</span>
                            </td>
                            <td class="text-right total"><?php echo format_number($total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>/<?php echo $extra['master']['dispatchAutoID'] ?>/<?php echo $batchid ?>";
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatchNote'); ?>/" + <?php echo $extra['master']['dispatchAutoID'] ?> +'/BBDPN';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>