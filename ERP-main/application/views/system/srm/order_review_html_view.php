<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


echo fetch_account_review(false,true,1 && 0);
?>
<div class="table-responsive">

<hr>
<div class="table-responsive">
    <input type="hidden" name="inquiryMasterID" id="inquiryMasterID" value="<?php echo $inquiryMasterID ?>">
    <input type="hidden" name="reviewMasterID" id="reviewMasterID" value="<?php echo $reviewMasterID ?>">
    <div style="text-align: center"><h4>Order Review</h4></div>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style=""><strong>Inquiry Id</strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""> <?php echo $reviewmaster['inquirycode']; ?></td>

            <td width="20%"><strong>Narration</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $reviewmaster['narration']; ?>


        </tr>

        <tr>
            <?php
            if($reviewmaster['inquiryType']=='PRQ'){
                ?>
                <td width="20%"><strong>Segment</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $reviewmaster['segmentCode']; ?> | <?php echo $reviewmaster['segdescription']; ?></td>
            <?php
            }else{
                ?>
                <td width="20%"><strong>Customer</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $reviewmaster['customerName']; ?></td>
                <?php
            }
            ?>

            <td width="20%"><strong>Referance Number</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $reviewmaster['referenceNo']; ?></td>
        </tr>
       </tbody>
    </table>
</div><br>



    <?php
    if (!empty($item)) {
    foreach ($item as $row) { ?>

    <table class="order-review-tbl-style">
        <tbody>
        <tr>
            <td  style="vertical-align: top; width: 10%;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width: 100%">

                                <?php
                                $imageuploadlocal = $this->config->item('ftp_image_uplod_local');
                                if($imageuploadlocal == 2)
                                {
                                    $emp_image = base_url() . "uploads/itemMaster/{$row['itemImage']}";
                                    //$emp_image = $row['awsImage'];
                                }else
                                {
                                    $emp_image = $row['awsImage'];
                                }

                                ?>


                                <img class="align-left" src="<?php echo $row['awsImage'] ?>"
                                     alt="" width="80" height="80">

                            </td>
                        </tr>
                        <tr><td style="width: 100%" ><?php echo $row['itemName'] ?></td></tr>
                        <tr><td style="width: 100%"><?php echo $row['itemSystemCode'] ?></td></tr>
                        <tr><td style="width: 100%"><?php echo $row['requestedQty'] . " (" . $row['UnitShortCode'] . ")" ?></td></tr>
                    </tbody>
                </table>


            </td>
            <td style="width: 90%;">
                <?php
                $supplers = $this->db->query("SELECT supplierID,supplierName,supplierImage from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND itemAutoID = " . $row['itemAutoID'] . " AND srp_erp_srm_orderinquirydetails.isRfqCreated = 1")->result_array();

                    if (!empty($supplers)) {

                        $tot = 0;
                        $active = "active";
                        ?>

                        <table>
                            <thead>
                            <tr>
                            <?php
                                foreach ($supplers as $sup) {
                            ?>
                                    <td colspan="3"> <label class="fs-14 fw-600"><?php echo $sup['supplierName']; ?> </label></td>
                            <?php
                        }
                        ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <?php
                            foreach ($supplers as $sup1) {
                                $supplersitm = $this->db->query("SELECT requestedQty from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND srp_erp_srm_orderinquirydetails.supplierID =".$sup1['supplierID']." AND itemAutoID = " . $row['itemAutoID'] . "")->result_array();
                            ?>

                                <?php
                                foreach ($supplersitm as $supitm) {
                                ?>
                                    <td class="fw-400 fs-12">Req QTY</td>
                                    <td >:</td>
                                    <td class="fw-400 fs-12" style="border-right: 1px solid bisque; "><?php echo $supitm['requestedQty']; ?></td>


                                <?php
                            }?>

                                    <?php
                            }
                            ?>
                            </tr>
                            <tr>
                            <?php
                            foreach ($supplers as $sup1) {
                                $supplersitm = $this->db->query("SELECT supplierQty from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND srp_erp_srm_orderinquirydetails.supplierID =".$sup1['supplierID']." AND itemAutoID = " . $row['itemAutoID'] . "")->result_array();
                            ?>

                                <?php
                                foreach ($supplersitm as $supitm) {
                                ?>
                                    <td class="fw-400 fs-12">QTY</td>
                                    <td >:</td>
                                    <td class="fw-400 fs-12" style="border-right: 1px solid bisque; "> <?php echo $supitm['supplierQty']; ?></td>


                                <?php
                            }?>

                                    <?php
                            }
                            ?>
                            </tr>
                            <tr>
                                <?php
                                foreach ($supplers as $sup1) {
                                    $supplersitm = $this->db->query("SELECT supplierPrice from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND srp_erp_srm_orderinquirydetails.supplierID =".$sup1['supplierID']." AND itemAutoID = " . $row['itemAutoID'] . "")->result_array();
                                    ?>

                                    <?php
                                    foreach ($supplersitm as $supitm) {
                                        ?>
                                        <td class="fw-400 fs-12">Unit Price</td>
                                        <td >:</td>
                                        <td class="fw-400 fs-12" style="border-right: 1px solid bisque; "> <?php echo number_format($supitm['supplierPrice'], 3); ?></td>


                                        <?php
                                    }?>

                                    <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <?php
                                foreach ($supplers as $sup1) {
                                    $supplersitm = $this->db->query("SELECT supplierQty,supplierPrice from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND srp_erp_srm_orderinquirydetails.supplierID =".$sup1['supplierID']." AND itemAutoID = " . $row['itemAutoID'] . "")->result_array();
                                    ?>

                                    <?php
                                    foreach ($supplersitm as $supitm) {
                                        ?>
                                        <td class="fw-400 fs-12">Total </td>
                                        <td >:</td>
                                        <td class="fw-400 fs-12" style="border-right: 1px solid bisque; "> <?php $tot = $supitm['supplierQty'] * $supitm['supplierPrice'];
                                            echo "<span style='color: blue;'>" . number_format($tot, 3) . "</span>";
                                            ?></td>
                                        <?php
                                    }?>

                                    <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <?php
                                foreach ($supplers as $sup1) {
                                    $supplersitm = $this->db->query("SELECT itemAutoID,supplierID,supplierQty,isSelected from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND srp_erp_srm_orderinquirydetails.supplierID =".$sup1['supplierID']." AND itemAutoID = " . $row['itemAutoID'] . "")->result_array();
                                    ?>

                                    <?php
                                    foreach ($supplersitm as $supitm) {
                                        ?>
                                        <td colspan="3" style="text-align: center;"><?php
                                            if($supitm['supplierQty']>0){
                                                if($supitm['isSelected']==1){
                                                    if (in_array($supitm['supplierID'].'_'.$supitm['itemAutoID'], $supplierIDarr)){

                                                        if($typepdf=='pdf'){
                                                            ?>
                                                            <img src="<?php echo $logo.'images/cheackedclearance.svg';?>"  style="width: 25px; height: 20px;">
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <img src="<?php echo base_url('images/cheackedclearance.svg'); ?>" id="changeImg" style="width: 30px; height: 17px;">
                                                            <?php
                                                        }
                                                        ?>


                                                    <?php } else {?>

                                                        <?php
                                                        if($typepdf=='pdf'){
                                                            ?>
                                                            <img src="<?php echo $logo.'images/close.svg'; ?>"  style="width: 25px; height: 20px;">
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <img src="<?php echo base_url('images/close.svg'); ?>" id="changeImg" style="width: 30px; height: 17px;">
                                                            <?php
                                                        }
                                                        ?>
                                                    <?php }

                                                }else{ ?>
                                                    <span class="or-notsubmit">Item Not Selected(PR Price Loaded)</span>
                                            <?php  }
                                            }else{
                                                ?>
                                                <span class="or-notsubmit">Not Submitted</span>
                                                <?php
                                            }
                                            ?></td>
                                        <?php
                                    }?>

                                    <?php
                                }
                                ?>
                            </tr>
                            </tbody>
                        </table>


                        <?php
                    }
                    ?>

                <br><br>


            </td>
        </tr>
        </tbody>
    </table>








</div>
<?php
}}
?>









<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Srm_master/load_ordereview_conformation'); ?>/<?php echo $reviewmaster['orderreviewID'] ?>/<?php echo $reviewmaster['orderreviewID'] ?>";
    $("#a_link").attr("href",a_link);
</script>