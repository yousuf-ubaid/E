<?php
echo fetch_account_review(false, true); ?>
<br>
<div class="table-responsive">
    <table style="width: 100%">
    <tbody>
    <tr>
        <td style="width:50%;">
            <table>
                <tr>
                    <td>
                        <img alt="Logo" style="height: 80px"
                             src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:50%;">
            <table>
                <tr>
                    <td colspan="3">
                        <h3>
                            <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                        </h3>
                        <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                        <h4>Journey Plan</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>JP Number</strong>
                    </td>
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['detail']['documentCode']?></td>
                </tr>
                <tr>
                    <td>
                        <strong>Document Date</strong>
                    </td><!--Payment Voucher Number-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['detail']['createdDate']?></td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="td" style = "text-align: center; padding-left: 0;padding-right: 0; background:#ddd;font-family: arial; font-size: 15px; color: #333;font-weight: 800;"><strong>Journey Plan Details</strong> </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>

                    </tr>
                    <table>
                        <tbody>
                        <tr>
                            <td style='border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;'>
                                <table>
                                    <tbody>

                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; "><b>Tour Date & Type</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['departureDatecon']?> | <?php echo $extra['detail']['tourdescription']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; "><b>Pick Up Time</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['depatureTime']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; "><b>Guide Name & Phone No</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['guideName']?> | <?php echo $extra['detail']['guidePhoneNumber']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; "><b>Guest Name</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['guestName']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; "><b>Villa Host</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['agentName']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800"><b>Driver Name & Phone No</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['driverName']?> | <?php echo $extra['detail']['driverMobileNumber']?></td>
                                    </tr>

                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800"><b>Offline Tracking Number</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['offlineTrackingRefNo']?></td>

                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <table>
                                <tbody>
                                <tr>
                                    <td height="25" class="td" style = "text-align: center; background: #ddd;font-weight: 800;"> <img alt="passengers" style="height: 25px" src="<?php echo base_url("images/journeyplan/passenger.png") ?>"> <strong style="font-family: arial; font-size: 13px; color: #333;" > Number Of Passengers </strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>

                    </tr>
                    <table>
                        <tbody>
                        <tr>
                            <td style='border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;'>
                                <table>
                                    <tbody>

                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; text-align: center;" ><?php echo $extra['detail']['noOfPassengers'] ?></td>
                                    </tr>


                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800; text-align: center;" >&nbsp;</td>
                                    </tr>

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </table>
            </td>

        </tr>
        <tr>
        <td style="width:60%;">
            <table>
                <tr>
                    <td>
                        <table>
                            <tbody>
                            <tr>
                                <td class="td" style = "text-align: center; background: #ddd;"> <strong style="font-family: arial;font-weight: 800; font-size: 15px; color: #333;" > Vehicle Details </strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>

                </tr>
                <table>
                    <tbody>
                    <tr>
                        <td style='border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;'>
                            <table>
                                <tbody>
                                <?php if($extra['detail']['vehicleID']>0){?>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;"><b>Vehicle Number</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['VehicleNo']?></td>

                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;"><b>IVMS Number</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['invmsnovehicalemaster']?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;"><b>Vehicle Description</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['vehDescription']?></td>

                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;"><b>Insurance Date</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['insurancedate']?></td>

                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;"><b>Licenced Date</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['licensedate']?></td>

                                </tr>
                                <?php }else {?>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800;"><b>Vehicle Number</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['vehicleNumber']?></td>

                                    </tr>
                                    <tr>
                                        <td style="font-size: 11px;font-weight: 800;"><b>IVMS Number</b></td>
                                        <td>:</td>
                                        <td style="font-size: 11px;"><?php echo $extra['detail']['ivmsNumber']?></td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </table>
    </td>
    <td style="width:40%;">
        <table>
            <tr>
                <td>
                    <table>
                        <tbody>
                        <tr>
                            <td  class="td" style = "text-align: center; background: #ddd;font-weight: 800;"> <img alt="passengers" style="height: 25px;text-align: left;" src="<?php echo base_url("images/journeyplan/ringjm.png") ?>"> <strong style="font-family: arial; font-size: 13px; color: #333;" > Ring Journey Manager</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </td>

            </tr>
            <table>
                <tbody>
                <tr>
                    <td style='border-width:thin;height:40px;border: 1px solid #ddd;;'>
                        <table>
                            <tbody>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Office Number</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['journeyManagerOfficeNo']?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Mobile Number</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['journeyManagerMobileNo']?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

        </table>
         </td>
        </tr>


<tr>
    <td style="width:60%;">
        <table>
            <tr>
                <td>
                    <table class="table table-bordered ">
                        <thead>
                        <tr>
                            <th class='theadtr' style="min-width: 5%;background:#ddd;">Place Names</th>
                            <th class='theadtr' style="min-width: 5%;background:#ddd;">Time Arrive</th>
                            <th class='theadtr' style="min-width: 5%;background:#ddd;">Time Depart</th>
                            <th class='theadtr' style="min-width: 5%;background:#ddd;">Rest</th>
                            <th class='theadtr' style="min-width: 5%;background:#ddd;">Motel Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($extra['routedetail'])) {
                            foreach ($extra['routedetail'] as $val) { ?>
                                <tr>
                                    <td style="text-align:right;"><?php echo $val['placeName'] ?></td>
                                    <td style="text-align:right;">

                                        <?php
                                        if(!empty($val['arrivedcon'] ))
                                        {
                                            echo $val['arrivedcon'];
                                        }else
                                        {
                                            echo '-';
                                        }


                                        ?>
                                    </td>
                                    <td style="text-align:right;">
                                        <?php
                                        if(!empty($val['departureDatecon']))
                                        {
                                            echo $val['departureDatecon'] ;
                                        } else
                                        {
                                            echo '-' ;
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:right;"><?php echo $val['restTick'] ?></td>
                                    <td style="text-align:right;"><?php echo $val['sleep'] ?></td>
                                </tr>

                            <?php }
                        }?>

                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>


        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td class="td" style = "text-align: left; padding-left: 0;padding-right: 0;font-family: arial; font-size: 15px; font-weight: 500;"><strong>Tour Price Details</strong> </td>
                    </tr>
                    <tr>


                        <td>
                            <table class="table table-bordered ">
                                <thead>

                                <tr>
                                    <th class='theadtr' style="width: 20%;background:#ddd;">Item</th>
                                    <th class='theadtr' style="width: 20%;background:#ddd;">Amount</th>
                                    <th class='theadtr' style="width: 15%;background:#ddd;">Remark</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($extra['tourcharge'])) {
                                    foreach ($extra['tourcharge'] as $val) { ?>
                                        <tr>
                                            <td style="text-align:left;"><?php echo $val['itemdesc'] ?></td>
                                            <td style="text-align:right;"><?php echo number_format($val['amount'],$extra['detail']['deci'])?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?php echo $val['remarks']



                                                ?>
                                            </td>
                                        </tr>

                                    <?php }
                                }?>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <td style="width:40%;">
            <table>
                <tr>
                    <td>
                        <table>
                            <tbody>
                            <tr>
                                <td  class="td" style = "text-align: center; background: #ddd;font-weight: 800;"><strong style="font-family: arial; font-size: 13px; color: #333;" > Comment For Drivers</strong></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>

                </tr>

                <table>
                    <tbody>
                    <tr>
                        <td style='border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;'>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;">
                                        <table>
                                            <tr>
                                                <td><b><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['detail']['commentsForDriver']);?></b></td>
                                            </tr>
                                        </table>
                                        <?php //echo $extra['detail']['commentsForDriver']?>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style='border-width:thin;height:10px;border: 1px solid #ddd;;'>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;text-align: center;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;text-align: center;"><b>...................................................</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: 800;text-align: center;"><b>Drivers Signature</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </table>
        </td>

        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td class="td" style = "text-align: left; padding-left: 0;padding-right: 0;font-family: arial; font-size: 15px; font-weight: 500;"><strong>Additional Charges</strong> </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="table table-bordered ">
                                <thead>
                                <tr>
                                    <th class='theadtr' style="width: 20%;background:#ddd;">Item</th>
                                    <th class='theadtr' style="width: 20%;background:#ddd;">Amount</th>
                                    <th class='theadtr' style="width: 15%;background:#ddd;">Remark</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($extra['additionalcharge'])) {
                                    foreach ($extra['additionalcharge'] as $val) { ?>
                                        <tr>
                                            <td style="text-align:left;"><?php echo $val['itemdesc'] ?></td>
                                            <td style="text-align:right;"><?php echo number_format($val['amount'],$extra['detail']['deci'])?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?php echo $val['remarks']



                                                ?>
                                            </td>
                                        </tr>

                                    <?php }
                                }?>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

<tr>
    <td style="width:70%;">
        <table>
            <tr>
                <td>
                    <table>
                        <tbody>
                        <tr>
                            <td  class="td" style = "text-align: center; background: #ddd;font-weight: 800;"><strong style="font-family: arial; font-size: 13px; color: #333;" > Journey Manager Remarks</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </td>

            </tr>
            <table>
                <tbody>
                <tr>
                    <td style='border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;'>
                        <table>
                            <tbody>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Journey Manager Name</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['journeyManagerName']?></td>

                            </tr>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Vehicle Daily Check</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['vehicleDailyCheckyn']?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Counselling for Drivers</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['counsellingForDriveryn']?></td>

                            </tr>
                            <tr>
                                <td style="font-size: 11px;font-weight: 800;"><b>Signature</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;">..................................................</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style='border-width:thin;height:30px;border: 1px solid #ddd;;'>
                        <table>
                            <tbody>
                            <tr>
                                <td style="font-size: 9px;font-weight: 800;"><b>if you speed,death may overtake you.</b></td>
                            </tr>
                            <tr>
                                <td style="font-size: 9px;font-weight: 800;"><b>Ensure that seat belts are worn by all.</b></td>
                            </tr>
                            <tr>
                                <td style="font-size: 9px;font-weight: 800;"><b>Road signs must be obeyed.</b></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </table>
    </td>
</tr>
</tbody>
    </table>
</div>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <?php if ($extra['detail']['confirmedYN']==1) { ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['detail']['confirmedByName']; ?></td>
            </tr>
        <?php }?>
        <?php if ($extra['detail']['approvedYN']) { ?>
            <tr>
                <td style="width:30%;">
                    <b>Electronically Approved By</b>
                </td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['detail']['approvedByEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;">
                    <b>Electronically Approved Date</b>
                </td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['detail']['approvedDatemaster']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Journeyplan/load_jp_view'); ?>/<?php echo $extra['detail']['journeyPlanMasterID']?>";
    $("#a_link").attr("href", a_link);
</script>