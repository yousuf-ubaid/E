<?php if ($html){ ?>
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
}
$total = 0;

echo fetch_account_review(true, true,$approval); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>

            <td>
                <table>
                    <tr>
                        <td style="text-align: center;">
                            <h4 >Return</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>

<?php if($extra['master']['approvedYN']== 1 && $extra['master']['confirmedYN']== 1) {
    echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="labellabelbuyback file bgcolour">Approved</div>
    </article>';
}?>
<?php if($extra['master']['confirmedYN']==1 && $extra['master']['approvedYN']!= 1 ) {
    echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="labellabelbuyback file bgcolourconfirm">Confirmed</div>
    </article>';
}?>


<hr style="margin-top: -1%">
<br>
<div class="table-responsive">
    <table style="width: 90%">
        <tbody>
        <tr>
            <td ><strong>Return Number </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['documentSystemCode'] ?></td>

            <td><strong>Return Date </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['returnedDate'] ?> </td>
        </tr>
        <tr>
            <td ><strong>Farm </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['farmdescription'] ?></td>

            <td><strong>Batch </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['batchCode'] ?> </td>
        </tr>
        <tr>
            <td ><strong>Document Date </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['documentDate'] ?></td>

            <td><strong>Currency </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['currency'] ?> </td>
        </tr>
        <tr>
            <td ><strong>Segment </strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['segmentCode'] ?></td>

            <td><strong>Narration </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['Narration'] ?> </td>
        </tr>
        <tr>
            <td ><strong>Reference Number</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['referenceNo'] ?> </td>

            <td><strong>Warehouse Location </td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['wareHouseLocation'] ?>  </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="4">Item Details</th>

            <th colspan="1">Qty</th>
            <th colspan="2">Amount</th>

        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%">Item Code </th>
            <th style="min-width: 30%">Item Description </th>
            <th style="min-width: 10%">UOM</th>
            <th style="min-width: 15%">Return</th>
            <th style="min-width: 15%">Unit</th>
            <th style="min-width: 15%">Total</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php
        if (!empty($extra['detail'])) {
            $x = 1;

            foreach ($extra['detail'] as $val) {
               $total += $val['totalTransferCost'];

                ?>
                <tr>
                    <td><?php echo $x;?></td>
                    <td><?php echo $val['itemSystemCode'];?></td>
                    <td><?php echo $val['description'];?></td>
                    <td><?php echo $val['unitOfMeasure'];?></td>
                    <td style="text-align: right"><?php echo $val['qty'];?></td>
                    <td style="text-align: right"><?php echo number_format($val['unitTransferCost'],2) ;?></td>
                    <td style="text-align: right"><?php echo number_format($val['totalTransferCost'],2) ;?></td>

                </tr>
                <?php
                $x++;
            }
        } else {
            echo '<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="6">Total</span></td>
            <td class="text-right total"><?php echo number_format($total,2); ?></td>
        
        </tr>
        </tfoot>
    </table>
</div>
<br>
<br>
<?php if ($extra['master']['approvedYN']) { ?>
<div class="table-responsive">
    <hr>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:30%;"><b>Electronically Approved By </b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
        </tr>
        <tr>
            <td style="width:30%;"><b>Electronically Approved Date</b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<?php }?>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/<?php echo $extra['master']['returnAutoID'] ?>";
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatch_return'); ?>/" + <?php echo $extra['master']['returnAutoID'] ?> +'/BBDRN';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>