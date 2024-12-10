<style>

    blink {
        -webkit-animation: 1s linear infinite condemned_blink_effect;
    / / for android animation: 1 s linear infinite condemned_blink_effect;
    }

    @-webkit-keyframes condemned_blink_effect {

    /
    /
    for android

    0
    %
    {
        visibility: hidden
    ;
    }
    50
    %
    {
        visibility: hidden
    ;
    }
    100
    %
    {
        visibility: visible
    ;
    }
    }
    @keyframes condemned_blink_effect {
        0% {
            visibility: hidden;
        }
        50% {
            visibility: hidden;
        }
        100% {
            visibility: visible;
        }
    }
</style>

<?php
if (!empty($items)) { ?>
    <div class="table-responsive mailbox-messages" >
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>#</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Main Category</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Sub Category</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Description</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Secondary Code</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Item Type</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Feed Type</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;"><strong>Current Stock</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; color: #f76f01;">&nbsp;</td>
            </tr>
            <?php
            $x = 1;
            foreach ($items as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><a><?php echo $x; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php echo $val['mainCategory']; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php echo $val['SubCategoryDescription']; ?></a></td>
                    <td class="mailbox-star" width="20%"><a><?php echo $val['itemName']; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php echo $val['secondaryItemCode']; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php echo $val['BuybackItemType']; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php echo $val['feedName']; ?></a></td>
                    <td class="mailbox-star" width="10%"><a><?php

                            if($val['reorderPoint'] >= $val['currentStock']){
                        ?>
                            <blink>
                                <span class="contacttitle " style=" color: red;font-weight: 800;">
                                    <?php echo $val['CurrentStock'] ?>
                                </span>
                            </blink>
                           <?php } else {
                                echo $val['CurrentStock'];
                            }?></a>
                    </td>
                    <td class="mailbox-star" width="5%">
                    <?php
                    $companyID = $this->common_data['company_data']['company_id'];
                    $buybackItemID = $val['buybackItemID'];
                    $itemMasterCode = $val['itemMasterCode'];

                    $result = $this->db->query("SELECT dpd.dispatchDetailsID FROM srp_erp_buyback_dispatchnotedetails dpd LEFT JOIN srp_erp_buyback_dispatchnote dpm ON dpm.dispatchAutoID = dpd.dispatchAutoID WHERE dpd.companyID={$companyID} AND dpd.itemAutoID = {$itemMasterCode} AND dpm.confirmedYN = 1 ")->row_array();
                    if(empty($result)){ ?>
                        <span class="pull-right">
                            <a onclick="edit_buyback_itemMaster( <?php echo $buybackItemID; ?> )"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;
                            | &nbsp;<a onclick="delete_item_master( <?php echo $buybackItemID; ?> );"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                     <?php
                    }else{
                        ?>
                        <span class="pull-right">
                            <i class="fa fa-check" aria-hidden="true" style="color: green;font-size: 15px;"></i>
                        </span>
                        <?php
                    }
                    ?>
                    </td>
                </tr>
            <?php
            $x++;
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO ITEMS TO DISPLAY, PLEASE <b>PULL ITEMS FROM ERP</b>.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>



<?php
