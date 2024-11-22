<?php
//echo '<pre>';print_r($countries); echo '</pre>'; die();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);

?>
    <div style="margin-left: -20px;">
    <label for="areaMemId" class="title"> <?php echo $this->lang->line('communityngo_region');?><!--Area--></label><br>
    <select name="areaMemId[]" class="form-control" id="areaMemId" multiple="multiple">
        <?php
        foreach ($areaDrop as $val){
            ?>
            <option value="<?php echo $val['stateID'] ?>"><?php echo $val['Description'] ?></option>
            <?php
        }
        ?>
    </select>
    </div>


<?php
