<?php
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<style>
    .fc {
        height: 22px !important;
        width: 83% !important;
        display: inline !important;
        margin: 0px !important;
    }

    .arrowDown {
        vertical-align: sub;
        color: rgb(75, 138, 175);
        font-size: 13px;
    }

    .applytoAll {
        display: none;
        vertical-align: top;
    }

</style>
<?php
if(!empty($attributes)){
    ?>
    <table class="table table-bordered table-condensed table-hover " id="itemMasterSubItemListTbl" style="margin-top:-1px;">
        <thead>
        <tr>
            <th>#</th>
            <th style="width:13%">SubItem Code</th>
            <?php
            $x=1;
            foreach($attributes as $valu){
            ?>
            <th><?php echo $valu['attributeDescription'] ?></th>
                    <?php
                $x++;
                }
            ?>
        </tr>
        </thead>
        <tbody id="tbl_body_item_master_sub">
        <?php
        if (isset($itemMasterSubTemp) && !empty($itemMasterSubTemp)) {
            $i = 1;
            foreach ($itemMasterSubTemp as $key => $item) {
                ?>
                <tr>
                    <td> <?php echo $item['subItemSerialNo']; ?> </td>
                    <td> <?php echo $item['subItemCode']; ?></td>
                    <?php
                    $i = 1;
                    foreach ($attributes as $valu) {
                        ?>
                        <td> <?php echo $item[$valu['columnName']]; ?> </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
        }
        ?>

        </tbody>
    </table>
<?php
}else{
    ?>
    <div class="alert alert-danger" role="alert">
        <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
        <span class="sr-only">Not Found:</span><!--Not Found-->
        No Records Found!
    </div>
<?php
}
?>