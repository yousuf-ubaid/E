<?php
//echo '<pre>';print_r($countries); echo '</pre>'; die();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);

?>
    <div style="margin-left: -20px;">
        <label for="gsDivitnId" class="title"> <?php echo $this->lang->line('communityngo_GS_Division');?><!--GS--></label><br>
        <select name="gsDivitnId[]" class="form-control" id="gsDivitnId" multiple="multiple">
            <?php
            foreach ($gsDiviDrop as $val){
                ?>
                <option value="<?php echo $val['stateID'] ?>"><?php echo $val['Description'] ?></option>
                <?php
            }
            ?>
        </select>
    </div>


<?php
