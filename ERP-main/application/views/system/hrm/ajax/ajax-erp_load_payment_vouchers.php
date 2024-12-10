<div class="form-group col-sm-12">
    <?php
    if (!empty($extra)) {
        foreach ($extra as $val) {
            ?>
            <a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal('PV','<?php echo $val['payVoucherAutoId']; ?>')" ><?php echo $val['PVcode']; ?></a>
            <br>
            <?php
        }
    }
    ?>
</div>



