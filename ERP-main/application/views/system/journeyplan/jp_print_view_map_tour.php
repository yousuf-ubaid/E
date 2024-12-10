<?php
echo fetch_account_review(false, true); ?>
<br>
<div class="table-responsive">
    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                    <td style="width:17%;">
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td height="25" class="td" style = "text-align: center; background: #67c2ef;"> <strong style="font-family: Tahoma;font-weight: bold; font-size: 15px; color: white;" > Journey Plan </strong></td>
                            </tr>

                            </tbody>
                        </table>
                        <br>
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="font-size: 11px;font-weight: bold;"><b>JP No</b></td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $extra['detail']['documentCode']?></td>
                                <td style="font-size: 11px;font-weight: bold;">Company</td>
                                <td>:</td>
                                <td style="font-size: 11px;"><?php echo $this->common_data['company_data']['company_name'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="form-group col-sm-3">
            <table>
                <tr>
                    <td style="width:17%;">
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td height="25" class="td" style = "text-align: center; background: #67c2ef;font-weight: bold;"> <img alt="passengers" style="height: 25px" src="<?php echo base_url("images/journeyplan/passenger.png") ?>"> <strong style="font-family: Tahoma; font-size: 13px; color: white;" > Number Of Passengers </strong></td>
                            </tr>

                            </tbody>
                        </table>
                        <table style="width: 100%;">
                            <br>
                            <tbody >
                            <tr>
                                <td style="text-align: center;font-weight: bold;font-size: 13px;"><?php echo $extra['detail']['noOfPassengers']?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                    <td style="width:17%;">
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td height="25" class="td" style = "text-align: center; background: #67c2ef;"> <strong style="font-family: Tahoma;font-weight: bold; font-size: 15px; color: white;" > Journey Plan Details </strong></td>

                            </tr>

                            </tbody>
                        </table>
                        <br>
                        <table style="width: 100%;">

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
                        </table>
            </table>
            </td>
            </tr>
            </table>
        </div>
        <div class="form-group col-sm-3">
            <table>
                <tr>
                    <td style="width:40%;">
                        <table>
                            <tr>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td  class="td" style = "text-align: center; background: #67c2ef;font-weight: bold;"> <strong style="font-family: Tahoma; font-size: 13px; color: white;" > Ring Journey Manager</strong></td>
                                        </tr>
                                        <tr>
                                            <td  class="td" style = "text-align: center; font-weight: bold;"> <img alt="passengers" style="height: 60px" src="<?php echo base_url("images/journeyplan/ringjm.png") ?>"> </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>

                            </tr>
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 11px;font-weight: bold;"><b>Office Number</b></td>
                                                <td>:</td>
                                                <td style="font-size: 11px;"><?php echo $extra['detail']['journeyManagerOfficeNo']?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 11px;font-weight: bold;"><b>Mobile Number</b></td>
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
            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                    <table>
                        <tr>
                            <td>
                                <table class="table table-bordered ">
                                    <thead>
                                    <tr>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Place Names</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Time Arrive</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Time Depart</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Rest</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Motel Name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($extra['routedetail'])) {
                                        foreach ($extra['routedetail'] as $val) { ?>
                                            <tr>
                                                <td style="text-align:right;"><?php echo $val['placeName'] ?></td>
                                                <td style="text-align:right;"><?php echo $val['arrivedcon'] ?></td>
                                                <td style="text-align:right;"><?php echo $val['departureDatecon'] ?></td>
                                                <td style="text-align:right;"><?php echo $val['departureDatecon'] ?></td>
                                                <td style="text-align:right;"><?php echo $val['sleep'] ?></td>
                                            </tr>

                                        <?php }
                                    }?>

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </tr>
            </table>
        </div>
        <div class="form-group col-sm-3">
            <table>
                <tr>
                    <td style="width:40%;">
                        <table>
                            <tr>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td  class="td" style = "text-align: center; background: #67c2ef;font-weight: bold;"><strong style="font-family: Tahoma; font-size: 13px; color: white;" > Comment For Drivers</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>

                            </tr>
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 11px;font-weight: bold;">
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
                                </tbody>
                            </table>

                        </table>
                    </td>
                </tr>

            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                <tr>
                    <td class="td" style = "text-align: left; padding-left: 0;padding-right: 0;font-family: arial; font-size: 15px; font-weight: 500;"><strong>Tour Price Details</strong> </td>
                </tr>
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td>
                                <table class="table table-bordered ">
                                    <thead>
                                    <tr>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Item</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Amount</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Remark</th>
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
                </tr>
            </table>
        </div>
        <div class="form-group col-sm-3">
            <table>
                <tr>
                    <td style="width:40%;">
                        <table>
                            <tr>&nbsp;</tr>
                            <tr>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td  class="td" style = "text-align: center; background: #67c2ef;font-weight: bold;"><strong style="font-family: Tahoma; font-size: 13px; color: white;" > Reasons for Night Driving</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>

                            </tr>
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <br>
                                                <td  class="td" style = " font-weight: bold;"> <img alt="passengers"  style="height: 60px" src="<?php echo base_url("images/journeyplan/night.png") ?>"> <strong></strong>

                                                </td>
                                                <td  class="td" style = " font-weight: bold;">
                                                    <table>
                                                        <tr>
                                                            <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['detail']['reasonForNightDriving']);?></td>
                                                        </tr>
                                                    </table>
                                                    <?php //echo $extra['detail']['reasonForNightDriving']?>
                                                </td>
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

            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                <tr>
                    <td class="td" style = "text-align: left; padding-left: 0;padding-right: 0;font-family: arial; font-size: 15px; font-weight: 500;"><strong>Additional Charges</strong> </td>
                </tr>
                </tr>
                <tr>
                    <table>
                        <tr>
                            <td>
                                <table class="table table-bordered ">
                                    <thead>
                                    <tr>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Item</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Amount</th>
                                        <th class='theadtr' style="min-width: 5%;background: #67c2ef;color: white;">Remark</th>
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
                </tr>
            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-8">
            <table>
                <tr>
                    <td style="width:60%;">
                        <table>
                            <tr>
                                <td>
                                    <table style="border: 1px;">
                                        <tbody>
                                        <tr>
                                            <td height="25" class="td" style = "text-align: center; background: #67c2ef;"> <strong style="font-family: Tahoma;font-weight: bold; font-size: 15px; color: white;" > Journey Manager Details </strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: bold;"><b>Journey Manager Name</b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $extra['detail']['journeyManagerName']?></td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: bold;"><b>Vehicale Daily Check</b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $extra['detail']['vehicleDailyCheckyn']?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: bold;"><b>Counselling for Drivers</b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $extra['detail']['counsellingForDriveryn']?></td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: bold;"><b>Sigature</b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;">..................................................</td>

                                        </tr>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>


<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Journeyplan/load_jp_view_tour'); ?>/<?php echo $extra['detail']['journeyPlanMasterID']?>";
    $("#a_link").attr("href", a_link);
</script>