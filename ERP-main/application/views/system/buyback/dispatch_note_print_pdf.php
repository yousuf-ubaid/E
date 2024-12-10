<?php echo fetch_account_review(true, true, $approval); ?>
<!--<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
/*                            echo mPDFImage . $this->common_data['company_data']['company_logo']; */ ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php /*echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; */ ?></strong>
                            </h3>

                            <p><?php /*echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; */ ?></p>
                            <h4>Dispatch Note</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>DPN Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['documentSystemCode']; */ ?></td>
                    </tr>
                    <tr>
                        <td><strong>DPN Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['documentDate']; */ ?></td>
                    </tr>
                    <tr>
                        <td><strong>Issued From</strong></td>
                        <td><strong>:</strong></td>
                        <td>
                            <?php /*if ($extra['master']['dispatchType'] == 1) {
                                echo 'Direct';
                            } else {
                                echo 'Load Change';
                            }; */ ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Reference Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['referenceNo']; */ ?></td>
                    </tr>
                    <tr>
                        <td><strong>Location</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['wareHouseLocation']; */ ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table>
        <tbody>
        <tr>
            <td style="width:20%;"><strong>Farmer</strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['farmName']; */ ?></td>
            <td style="width:20%;"><strong>Delivered Date </strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['dispatchedDate']; */ ?></td>
        </tr>
        <tr>
            <td style="width:20%;"><strong>Address </strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['farmAddress']; */ ?></td>
            <td style="width:20%;"><strong>Currency </strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; */ ?></td>
        </tr>
        <tr>
            <td style="width:20%;"><strong>Phone</strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['farmTelephone']; */ ?></td>
            <td style="width:20%;"><strong>Narration </strong></td>
            <td style="width:5%;"><strong>:</strong></td>
            <td style="width:25%;"><?php /*echo $extra['master']['Narration']; */ ?></td>
        </tr>
        </tbody>
    </table>
</div>-->
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 >Dispatch Note</h4><!-- -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%; font-family:Segoe,Roboto,Helvetica,arial,sans-serif">z

    <!-- <table style="width: 100%"> -->
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>DPN Number</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['documentSystemCode']; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>DPN Date</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Issued From</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;">
                <?php if ($extra['master']['dispatchType'] == 1) {
                    echo 'Direct';
                } else {
                    echo 'Load Change';
                }; ?>
            </td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Reference Number</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['referenceNo']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Location</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['wareHouseLocation']; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Farmer</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['farmName']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Delivered Date </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['dispatchedDate']; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Address</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['farmAddress']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Currency </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Phone</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['farmTelephone']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;vertical-align: top"><strong>Narration </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['Narration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['Narration']; ?>
            </td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Batch </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td  style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['batchCode']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped" style="font-family:Arial, Sans-Serif, Times, Serif;">

    <!-- <table id="add_new_grv_table" class="<?php //echo table_class(); ?>"> -->
        <thead>
        <tr>
            <th style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">#</th>
            <th style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">Item Code</th>
            <th style="font-size: 12px;font-weight:normal; min-width: 15%; border-bottom: 1px solid black">Item Description</th>
            <th style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">UOM</th>
            <th style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">Qty</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0;
        $received_total = 0;
        if (!empty($extra['detail'])) {
            for ($i = 0; $i < count($extra['detail']); $i++) {
                echo '<tr>';
                echo '<td style="font-size: 14px;">' . ($i + 1) . '</td>';
                echo '<td style="font-size: 14px;">' . $extra['detail'][$i]['itemSystemCode'] . '</td>';
                echo '<td style="font-size: 14px;">' . $extra['detail'][$i]['itemDescription'] . '</td>';
                echo '<td class="font-size: 14px;text-center">' . $extra['detail'][$i]['defaultUOM'] . '</td>';
                echo '<td class="font-size: 14px;text-right">' . $extra['detail'][$i]['qty'] . '</td>';
            }
        } else {
            echo '<tr class="danger"><td colspan="10" class="font-size: 14px;text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<br>

<div class="row">
    <h6 class="modal-title" align="center" style="font-family:Arial, Sans-Serif, Times, Serif;font-weight:normal;min-width: 5px">Feed Summary</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-striped  " style="width: 50%;font-family:Arial, Sans-Serif, Times, Serif;"align="right">
            <tbody>
            <thead>
            <tr>
                <th style="font-size: 12px;font-weight:normal;min-width: 5px">#</th>
                <th style="font-size: 12px;font-weight:normal;min-width: 50px">Item</th>
                <th style="font-size: 12px;font-weight:normal;min-width: 50px">Qty R</th>
                <th style="font-size: 12px;font-weight:normal;min-width: 10px">Qty D</th>
            </tr>
            </thead>
            <?php
            if (!empty($feed_header)) {
                $x=1;
                $qtrtot =0;
                foreach ($feed_header as $row) {
                    $qtrtot = ($row["feedAmount"] * $chicks['chicksTotal']) / 50;

                    echo "<tr>";
                    echo "<td style='font-size: 12px;'>" . $x. "</td>";
                    echo "<td style='font-size: 12px;'>" . $row['feedName'];
                    if($row['qtyD'] > $qtrtot){
                        echo '&nbsp;&nbsp;<span class="label label-danger" style="font-size: 10px">Over Feeded</span>';
                    }
                    echo "</td>";
                    echo "<td style='font-size: 12px;text-align: right'>" .$qtrtot . "</td>";
                    echo "<td style='font-size: 12px;text-align: right'>" .$row['qtyD']. "</td>";
                    echo "</tr>";
                    $x++;
                }
            } else {
                echo '<tr class="danger"><td colspan="10" class="text-center" style="font-size: 12px;"><b>No Records Found</b></td></tr>';
            }
            ?>

            </tbody>
            <tfoot>

            </tfoot>
        </table>
        <?php /*  foreach ($feed_header as $row) {
            $qtrtot = ($row["feedAmount"] * $chicks['chicksTotal']) / 50;

            if($row['qtyD'] > $qtrtot)
            {
                echo ' <div class="" align="center" style="margin: 40%;margin-top: -10%; padding-right: 10%">
        <div class="label label-danger" style="padding:3.5em 0.8em 2.0em; font-size: 10px;" > Over Feeded </div>
    </div>';
            }

        }
        */?>
    </div>
</div>

<br>
<div class="table-responsive">
    <table style="width: 100%" >
        <tr>
            <td style="width:50%;">
                <?php if ($extra['master']['confirmedYN'] && $extra['master']['approvedYN'] != 1) { ?>
                    <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
                        <tbody>
                        <tr>
                            <td style="font-size: 12px; text-align: center"><strong>Confirmed By </strong></td>
                            <td style="font-size: 12px; text-align: center"><strong>:</strong></td>
                            <td style="font-size: 12px; text-align: center"><?php echo $extra['master']['confirmedByName']; ?>
                                / <?php echo $extra['master']['confirmedDate']; ?> </td>
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
                <?php if ($extra['master']['approvedYN']) { ?>
                    <table style="width: 100%" style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
                        <tbody>
                        <tr>
                            <td style="font-size: 12px; text-align: center"><strong>Approved By </strong></td>
                            <td style="font-size: 12px; text-align: center"><strong>:</strong></td>
                            <td style="font-size: 12px; text-align: center"><?php echo $extra['master']['approvedbyEmpName']; ?>
                                / <?php echo $extra['master']['approvedDate']; ?> </td>

                        </tr>
                        <tr>
                            <td style="font-size: 12px; text-align: center"><strong>Confirmed By </strong></td>
                            <td style="font-size: 12px; text-align: center"><strong>:</strong></td>
                            <td style="font-size: 12px; text-align: center"><?php echo $extra['master']['confirmedByName']; ?>
                                / <?php echo $extra['master']['confirmedDate']; ?> </td>
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
<br><br><br><br><br><br>
<div class="table-responsive">
    <table style="width: 100%;font-family:'Arial, Sans-Serif, Times, Serif'; padding: 0px;">
        <tbody>
        <tr>
            <td style="width: 33%;text-align: center;font-size: 12px;">
                <span>.....................................</span><br><br><span><b>&nbsp; Prepared by</b></span>
            </td>
            <td style="width: 33%;text-align: center;font-size: 12px;">
                <span>.....................................</span><br><br><span><b>&nbsp; Approved by </b></span>
            </td>
            <td style="width: 33%;text-align: center;font-size: 12px;">
                <span>.....................................</span><br><br><span><b>&nbsp; Reviewed by</b></span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>/<?php echo $extra['master']['dispatchAutoID'] ?>";
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatchNote'); ?>/" + <?php echo $extra['master']['dispatchAutoID'] ?> +'/BBDPN';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>