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
                                    <td style="font-size: 11px;font-weight: bold;"><b>Departure Date</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['departureDatecon']?></td>

                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: bold;"><b>Driver Name</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['driverName']?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;font-weight: bold;"><b>Driver Telephone No</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php echo $extra['detail']['driverMobileNumber']?></td>

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
                    <td style="width:17%;">
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td height="25" class="td" style = "text-align: center; background: #67c2ef;font-weight: bold;"> <img alt="passengers" style="height: 25px" src="<?php echo base_url("images/journeyplan/passenger.png") ?>"> <strong style="font-family: Tahoma; font-size: 13px; color: white;" > Name Of Passengers </strong></td>
                            </tr>

                            </tbody>
                        </table>
                        <table style="width: 100%;">
                            <br>
                            <tbody>
                            <?php
                            $x = 1 ;
                            foreach ($extra['passengerdet'] as $val){?>
                                <tr>
                                    <td style="font-size: 11px;font-weight: bold; text-align: center;"><?php echo $x ?></td>
                                    <td style="font-size: 11px;font-weight: bold;text-align: center;"><?php echo $val['passengerName'] ?></td>

                                </tr>
                                <?php
                                $x++;
                            }?>
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
                                                <td style="font-size: 11px;font-weight: bold;"><b><?php echo $extra['detail']['commentsForDriver']?></b></td>
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
                    <td style="width:60%;">
                        <table>
                            <tr>
                                <td>
                                    <table style="border: 1px;">
                                        <tbody>
                                        <tr>
                                            <td height="25" class="td" style = "text-align: center; background: #67c2ef;"> <strong style="font-family: Tahoma;font-weight: bold; font-size: 15px; color: white;" > Reasons for Night Driving</strong></td>
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
                                            <br>
                                            <td  class="td" style = " font-weight: bold;"> <img alt="passengers"  style="height: 60px" src="<?php echo base_url("images/journeyplan/night.png") ?>"> <strong></strong>

                                            </td>
                                            <td  class="td" style = " font-weight: bold;">  <?php echo $extra['detail']['reasonForNightDriving']?></td>
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
    a_link = "<?php echo site_url('Journeyplan/load_jp_view'); ?>/<?php echo $extra['detail']['journeyPlanMasterID']?>";
    $("#a_link").attr("href", a_link);
</script>