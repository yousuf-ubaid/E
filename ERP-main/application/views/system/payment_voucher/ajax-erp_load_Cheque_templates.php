<div class="form-group col-sm-12">
    <select name="coaChequeTemplateID" class="form-control select2" id="coaChequeTemplateID">
        <option value="">Select Template</option>
        <?php
        if (!empty($extra)) {
            foreach ($extra as $val) {
                ?>
                <option value="<?php echo $val['coaChequeTemplateID']; ?>"><?php echo $val['Description']; ?></option>
                <?php
            }
        }
        ?>
    </select>
</div>



