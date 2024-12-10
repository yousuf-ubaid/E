<?php if($type==true){?>
    <style>
        .bgcolour {
            background-color: #00a65a;
            margin-top: 3%;
        }
        .bgcolourconfirm {
            background-color: #f9ac38;
            margin-top: 3%;
        }
        .item-labellabelbuyback {
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
        .item-labellabelbuyback:after {
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
<?php }?>
<?php
$totalbalance = 0;
$totalcollectionamt = 0;

echo fetch_account_review(false, true,$extra['collectionmaster']['confirmedYN']); ?>
<!--<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
/*                            echo mPDFImage . $this->common_data['company_data']['company_logo']; */?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php /*echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; */?></strong>
                            </h3>

                            <p><?php /*echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; */?></p>
                            <h4>Good Receipt Note</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>GRN Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['documentSystemCode']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>GRN Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['documentDate']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>Reference Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['referenceNo']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>Location</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['wareHouseLocation']; */?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <hr>
    <table>
        <tr>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td class="td"><strong>Farmer</strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php /*echo $extra['master']['farmName']; */?></td>
                    </tr>
                    <tr>
                        <td style="width:15%;" class="td"><strong>Address </strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:83%;" class="td"><?php /*echo $extra['master']['farmAddress']; */?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong>Phone</strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php /*echo $extra['master']['farmTelephone']; */?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width:20%;" class="td"><strong>Delivered Date </strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:78%;" class="td"><?php /*echo $extra['master']['deliveryDate']; */?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong>Currency </strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php /*echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; */?></td>
                    </tr>
                    <tr>
                        <td class="td"><strong>Narration </strong></td>
                        <td><strong>:</strong></td>
                        <td class="td"><?php /*echo $extra['master']['Narration']; */?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>-->

<div class="table-responsive">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4 >Buyback Collection</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<br>
<hr style="margin-top: -1%">
<br>
<div class="table-responsive">
    <table style="width: 90%;font-family:Segoe,Roboto,Helvetica,arial,sans-serif">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Live Collection Number </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['collectionmaster']['collectionCode'] ?></td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Document Date </td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['collectionmaster']['createdDate'] ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Driver And Helper </strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['collectionmaster']['driverhelper'] ?> </td>

            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Narration </td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['collectionmaster']['Narration'] ?>  </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-striped"  style="font-family:Arial, Sans-Serif, Times, Serif;">
        <thead>
        <tr>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">#</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">Area</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">Sub Area</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">Farm</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 15%; border-bottom: 1px solid black">Batch Code</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">Balance</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">FVR</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 10%; border-bottom: 1px solid black">Weight</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">Collection</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 5%; border-bottom: 1px solid black">Age</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 12%; border-bottom: 1px solid black">Address</th>
            <th class='' style="font-size: 12px;font-weight:normal; min-width: 12%; border-bottom: 1px solid black">Contact No</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">

 <?php

        if (!empty($collectiondetails['collectiondetail'])) {
            $x = 1;

            foreach ($collectiondetails['collectiondetail'] as $detailVal) {
                $totalbalance +=$detailVal['balanceQty'];
                $totalcollectionamt +=$detailVal['collectionQty'];

                ?>
                <tr>
                    <td style="font-size: 14px;"><?php echo $x;?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['farmlocation'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['subarea'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['farmname'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['batchsystemcode'];?></td>
                    <td style="font-size: 14px;text-align: right"><?php echo $detailVal['balanceQty'];?></td>
                    <td style="font-size: 14px;text-align: right"><?php echo $detailVal['fvr'];?></td>
                    <td style="font-size: 14px;text-align: right"><?php echo $detailVal['avgBodyWeight'];?></td>
                    <td style="font-size: 14px;text-align: right"><?php echo $detailVal['collectionQty'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['age'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['farmeradd'];?></td>
                    <td style="font-size: 14px;"><?php echo $detailVal['phonemobilefarmer'];?></td>
                </tr>
                <?php
                $x++;
            }
        } else {
            echo '<tr class="danger"><td colspan="14" class="text-center" style="font-size: 14px;"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="font-size: 14px; font-weight: bold;"class="text-right sub_total" colspan="5">Total</span></td>
            <td style="font-size: 14px; font-weight: bold;"class="text-right total"><?php echo number_format($totalbalance,2); ?></td>
            <td style="font-size: 14px; font-weight: bold;"> </td>
            <td style="font-size: 14px; font-weight: bold;"> </td>

            <td style="font-size: 14px; font-weight: bold;" class="text-right total"><?php echo number_format($totalcollectionamt,2); ?></td>
            <td style="font-size: 14px; font-weight: bold;" colspan="5"></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
    <table style="width: 50%">
        <tr>
            <td style="width:30%;">

                    <table style="width: 100%;font-family:Arial, Sans-Serif, Times, Serif;">
                        <tbody>
                        <?php if ( $extra['collectionmaster']['confirmedYN'] == 1 ) { ?>
                        <tr>

                            <td style="font-size: 11px;"><b>Confirmed By</b></td>
                            <td style="font-size: 11px;"><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['collectionmaster']['confirmedByName']?></td>
                        </tr>
                        <?php } ?>
                        <tr>

                            <td style="font-size: 11px;"><b>Created Date And Time</b></td>
                            <td style="font-size: 11px;"><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['collectionmaster']['createdatetime']?></td>
                        </tr>
                        <tr>

                            <td style="font-size: 11px;"><b>Last Update Date And Time</b></td>
                            <td style="font-size: 11px;"><strong>:</strong></td>
                            <td style="font-size: 11px;">

                                <?php if(!empty( $extra['collectionmaster']['updatedatetime']))
                                {
                                    echo $extra['collectionmaster']['updatedatetime'];
                                }else
                                {
                                    echo  ' - ';
                                }
                               ?>

                            </td>
                        </tr>
                        </tbody>
                    </table>

            </td>
        </tr>
    </table>
</div>

<br>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>/<?php echo $extra['collectionmaster']['collectionID'] ?>";
    $("#a_link").attr("href", a_link);
</script>