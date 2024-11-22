<?php
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    $masterID = $_SERVER['HTTP_REFERER'];
    $newID = explode("_", $masterID);
    $compID = $newID[2];
    $supID = $newID[1];
    $_SESSION['sup_por_company_id']=$compID;

}
include('../includes/medoo/medoo.php');
include('../includes/database.php');

if (isset($_POST['inquiryID']) && !empty($_POST['inquiryID'])) {
    $items = $database_sup->query("SELECT *, srp_erp_itemmaster.itemName, srp_erp_itemmaster.itemImage, srp_erp_itemmaster.itemSystemCode, UnitShortCode, DATE_FORMAT(srp_erp_srm_orderinquirydetails.expectedDeliveryDate,'%d-%m-%Y') AS expectedDeliveryDate,DATE_FORMAT(srp_erp_srm_orderinquirydetails.supplierExpectedDeliveryDate,'%d-%m-%Y') AS supplierExpectedDeliveryDate,srp_erp_srm_orderinquirydetails.lineWiseComment as lineWiseComment FROM srp_erp_srm_orderinquirydetails LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID LEFT JOIN srp_erp_unit_of_measure ON srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID WHERE srp_erp_srm_orderinquirydetails.inquiryMasterID = " . $_POST['inquiryID'] . " AND srp_erp_srm_orderinquirydetails.supplierID = " . $_POST['supplierID'] . "")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <style>
        .headrowtitle {
            font-size: 11px;
            line-height: 15px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }

        .mailbox-star {
            font-size: 10px;
            line-height: 15px;
            height: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }

        .number {
            text-align: right;
        }
    </style>
    <div class="table-responsive mailbox-messages">

        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Name</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">UOM</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Expected QTY</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Expected Delivery Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Comment</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">QTY</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Unit Price</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Delivery Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Narration</td>
            </tr>
            <?php
            if (!empty($items)) {
                $num = 1;
                foreach ($items as $val) { ?>
                    <tr>
                        <td class="mailbox-star"><?php echo $num; ?>.&nbsp;</td>
                        <td class="mailbox-star"><?php echo $val['itemSystemCode']; ?></td>
                        <td class="mailbox-star"><?php echo $val['itemName']; ?></td>
                        <td class="mailbox-star"><?php echo $val['UnitShortCode']; ?></td>
                        <td class="mailbox-star"><?php echo $val['requestedQty']; ?></td>
                        <td class="mailbox-star"><?php echo $val['expectedDeliveryDate']; ?></td>
                        <td class="mailbox-star"><?php echo $val['lineWiseComment']; ?></td>
                        <td class="mailbox-star">
                            <?php
                            if (!empty($val['supplierQty'])) {
                                echo $val['supplierQty'];
                            } else { ?>
                                <input class="number userfill" type="text" name="supplierQTY[]"
                                       style="background: honeydew;">
                            <?php } ?>
                        </td>
                        <td class="mailbox-star">
                            <?php
                            if (!empty($val['supplierPrice'])) {
                                echo $val['supplierPrice'];
                            } else { ?>
                                <input type="hidden" name="detailID[]" value="<?php echo $val['inquiryDetailID']; ?>">
                                <input class="number userfill" type="text" name="unitprice[]"
                                       style="background: honeydew;">
                            <?php } ?>
                        </td>
                        <td class="mailbox-star">
                            <?php
                            if (!empty($val['supplierExpectedDeliveryDate'])) {
                                echo $val['supplierExpectedDeliveryDate'];
                            } else { ?>
                                <input type="text" class="supplierdelivery" name="supplierDate[]" id="supplierDate[]"
                                       style="background: honeydew;">
                            <?php } ?>
                        </td>
                        <td class="mailbox-star">
                            <?php
                            if (!empty($val['SupplierNarration'])) {
                                echo $val['SupplierNarration'];
                            } else { ?>
                                <input type="text" name="narration[]" style="background: honeydew;">
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $num++;
                }
            } else {
                echo '<tr class="danger"><td colspan="10" class="mailbox-star" style="text-align: center">No Records Found</td></tr>';
            } ?>
            </tbody>
        </table><!-- /.table -->
    </div>
<?php } ?>

<script>
    $(document).ready(function () {
        number_validation();

        $('.supplierdelivery').datepicker({
            format: 'dd-mm-yyyy'
        });
    });
</script>


