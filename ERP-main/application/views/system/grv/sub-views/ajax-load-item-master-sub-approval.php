<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);?>
<style>
    .colorBlue {
        color: #428ad4
    }

    .colorGray {
        color: #49494d;
        padding-left: 15px !important;
    }
</style>
<table class="table table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th> &nbsp;</th>
        <th style="max-width: 15px;"> #</th>
        <th><?php echo $this->lang->line('transaction_common_item_code');?> </th><!--Item Code-->
        <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
        <th><?php echo $this->lang->line('transaction_common_uom');?>  </th><!--UOM-->
    </tr>
    </thead>
    <tbody>
    <?php

    if (isset($output) && !empty($output)) {


        $i = 1;
        $detailID = '';
        foreach ($output as $item) {
            if ($receivedDocumentID == 'GRV') {
                $tmpDetailID = $item['grvDetailID'];
            } else if ($receivedDocumentID == 'PV') {
                $tmpDetailID = $item['detailID'];
            }

            if ($detailID != $tmpDetailID) {
                /** Header */
                ?>
                <tr>
                    <td style="width: 10px;">
                        <?php if ($item['isSubitemExist'] == 1) { ?>
                            <button class="btn btn-xs btn-link" type="button"
                                    onclick="toggleSubItemList(<?php echo $tmpDetailID ?>)">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </button>
                        <?php } ?>
                    </td>
                    <td><?php echo $i; ?></td>
                    <td class="colorBlue"><strong><?php echo $item['itemSystemCode']; ?></strong></td>
                    <td class="colorBlue"><strong><?php echo $item['itemDescription']; ?></strong></td>
                    <td class="colorBlue"><?php //echo $item['uom']; ?></td>

                </tr>
                <?php
                $i++;
                $x = 1;
            }

            $detailID = $tmpDetailID;

            /** Sub Item */
            if ($item['isSubitemExist'] == 0) {
                continue;
            }
            ?>

            <tr class="grvDetailID_<?php echo $tmpDetailID ?>" style="display: none;">
                <td class="colorGray">&nbsp;</td>
                <td class="colorGray"><?php echo $x; ?></td>
                <td class="colorGray"><?php echo $item['subItemCode']; ?></td>
                <td class="colorGray"><?php

                    $description= $this->lang->line('common_description');
                    $proref= $this->lang->line('transaction_common_product_reference');


                    echo '<strong>'.$description.'<!--Description-->:</strong> ' . $item['description'];
                    echo '<br/><strong>'.$proref.' <!--Product Reference--> :</strong>';
                    echo '' . !empty($item['productReferenceNo']) ? $item['productReferenceNo'] : '-';
                    ?></td>
                <td class="colorGray"><?php echo $item['uom']; ?></td>

            </tr>
            <?php
            $x++;

        }
    }
    ?>

    </tbody>
</table>
<script>
    function toggleSubItemList(className) {
        $(".grvDetailID_" + className).toggle();
    }


</script>
