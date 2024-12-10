<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
?>

    <div id="tbl_purchase_order_list">
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="width:50%;">
                        <table>
                            <tr>
                                <td>
                                    <img alt="Logo" style="height: 140px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                                </td>
                                <td style="text-align:center;font-size: 18px;font-family: tahoma;">
                                    <strong style="font-weight: bold;"><?php echo $this->common_data['company_data']['company_name']; ?>.</strong><br>

                                    <strong style="font-weight: bold;">  <?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?>.</strong><br>
                                    <strong style="font-weight: bold;">  Tel :  <?php echo $this->common_data['company_data']['company_phone'] ?></strong><br>
                                    <br>
                                    Public Property Damage Assessment Summary Report
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <br>
        <div class="col-sm-12" style="width:100%;font-size: 15px;font-family: tahoma;font-weight: 900;">
            <strong>Project : </strong> <?php echo $publicProperty_da['projectName'] ?>
        </div>
        <br>

        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Country &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><?php echo $publicProperty_da['CountryDes'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Province &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong> <?php echo $publicProperty_da['provinceNam'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> District &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong> <?php echo $publicProperty_da['districtName'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Division &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><?php echo $publicProperty_da['divisionName'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 14px;font-family: tahoma;">
                <strong> Sub Division : </strong><?php echo $publicProperty_da['subDivisionName'] ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="reportHeaderColor" style="font-size: 15px;font-family: tahoma; font-weight: 900">Summary</div>
            </div>
        </div>
        <?php
        if (!empty($benificiaryArray)) {

            foreach ($benificiaryArray as $damageInfo) {
                ?>
                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h4>PROPERTY ASSESSMENT</h4>
                        </header>
                        <hr>
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tr>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"><label class="title">Type of Damage :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"><?php echo $damageInfo['TypeOfHouseDamage'] ?></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"> <label class="title">House Type :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"> <?php echo $damageInfo['buildingtype'] ?></td>
                            </tr>
                            <tr>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"><label class="title">Property Condition :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"><?php echo $damageInfo['houseCondition'] ?></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"> <label class="title">Building Damages :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"> <?php echo $damageInfo['damagetype'] ?></td>
                            </tr>
                            <tr>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"><label class="title">Estimated Cost for Repair :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"><?php echo $damageInfo['da_estimatedRepairingCost'] ?></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"> <label class="title">Need assistance to repair?</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"> <?php if($damageInfo['da_needAssistancetoRepairYN']==1){  ?>
                                        Yes
                                    <?php } else{ ?>
                                        No
                                    <?php }  ?></td>
                            </tr>
                            <tr>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"><label class="title">Total Paid Amount :</label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"><?php echo $damageInfo['da_paidAmount'] ?></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;"> <label class="title"></label></td>
                                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 13px;text-align: left;"> </td>
                            </tr>
                        </table>
                    </div>
                        </div>
                    </div>
                    <br>

            <?php }
        } else {

            ?>
            <div id="familydetails" style="">
                <div class="alert alert-danger" role="alert">
                    <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
                    <span class="sr-only">Not Found:</span>
                    No Details Found!
                </div>
            </div>
            <?php
            exit;
        }
        ?>
        <br>
        <?php
        if (!empty($header)) { ?>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h4>DAMAGE ASSESSMENT FOR PROPERTY ITEMS</h4>
                    </header>
                </div>
            </div>
            <hr>
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 12px;">#</td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 12px;">Item Description</td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;font-size: 12px;">Damage</td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;font-size: 12px;">Damage Assessment Amount<br> as per the property</td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;font-size: 12px;">Brand
                        </td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;font-size: 12px;">Assessed
                            Value
                        </td>
                        <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;font-size: 12px;">Total Amount Paid
                        </td>
                    </tr>
                    <?php
                    $x = 1;
                    foreach ($header as $val) { ?>
                        <tr>
                            <td colspan="8" class="mailbox-name"><span style="font-weight: 600;font-size: 13px"><?php echo $val['Description']; ?> </span></td>
                        </tr>
                        <?php
                        $x = 1;
                        foreach ($detail as $row) {
                            if ($row['damageItemCategoryID'] == $val['damageItemCategoryID']) { ?>
                                <tr>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#" class="numberColoring">&nbsp;&nbsp;&nbsp;<?php echo $x; ?></a></td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"><?php echo $row['itemDescription']; ?></a></td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"><?php echo $row['Description']; ?></a></td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"
                                                                class="pull-right"><?php echo number_format($row['damagedAmountClient'], 2); ?></a>
                                    </td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"><?php echo $row['Brand']; ?></a></td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"
                                                                class="pull-right"><?php echo number_format($row['assessedValue'], 2); ?></a>
                                    </td>
                                    <td class="mailbox-name" style="font-size: 12px;"><a href="#"
                                                                class="pull-right"><?php echo number_format($row['paidAmount'], 2); ?></a>
                                    </td>
                                </tr>
                                <?php
                                $x++;
                            }
                            ?>

                        <?php }
                    }
                    ?>
                    </tbody>
                </table><!-- /.table -->
            </div>
            <?php
        } else { ?>
            <div class="search-no-results">THERE ARE NO HOUSE ITEMS TO DISPLAY.</div>
            <?php
        }
        ?>
    </div>

<?php
