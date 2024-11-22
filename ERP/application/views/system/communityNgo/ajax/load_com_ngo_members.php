<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>


    <label for="Com_MasterID"> <?php echo $this->lang->line('communityngo_member_name');?><!--Employee Name--></label><br>
    <select name="Com_MasterID[]" class="form-control" id="Com_MasterID" onchange="callOTable('Com_MasterID')" multiple="multiple">
        <?php
        foreach ($members as $val){
            ?>
            <option value="<?php echo $val['Com_MasterID'] ?>"><?php echo $val['CName_with_initials'] ?></option>
            <?php
        }
        ?>
    </select>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 1/23/2018
 * Time: 9:21 AM
 */