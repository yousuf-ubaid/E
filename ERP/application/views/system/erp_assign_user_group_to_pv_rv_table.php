<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>
<?php echo form_open('', 'role="form" id="usergrouptopolicy_form"'); ?>
    <table class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 35px;">#</th>
            <th>User Group</th>
            <th>Payment Voucher</th>
            <th>Receipt Voucher</th>
        </tr>
        </thead>
        <tbody>
        <?php if($detail){
            $i=0;

            foreach($detail as $value){
            $i++;
            $pv='';
            $rv='';
            if(!empty($value['docID'])){
                $str=explode(",",$value['docID']);
                if($str[0]=='PV'){
                    $pv='PV';
                }else{
                    $rv='RV';
                }

                if(!empty($str[1])){
                    if($str[1]=='PV'){
                        $pv='PV';
                    }else{
                        $rv='RV';
                    }
                }
            }
            ?>
            <tr>
                <td style="width: 35px;"><?php echo $i;?></td>
                <td><?php echo $value['description']?></td>
                <?php
                if($pv=='PV'){
                    ?>
                    <td><input type="checkbox" class="switch-chk btn-sm" name="chkVal[]" value="PV_<?php echo $value['userGroupID']?>" checked></td>
                    <?php
                }else{
                    ?>
                    <td><input type="checkbox" class="switch-chk btn-sm" name="chkVal[]" value="PV_<?php echo $value['userGroupID']?>"></td>
                    <?php
                }
                ?>

                <?php
                if($rv=='RV'){
                    ?>
                    <td><input type="checkbox" class="switch-chk btn-sm" name="chkVal[]" value="RV_<?php echo $value['userGroupID']?>" checked></td>
                    <?php
                }else{
                    ?>
                    <td><input type="checkbox" class="switch-chk btn-sm" name="chkVal[]" value="RV_<?php echo $value['userGroupID']?>"></td>
                    <?php
                }
                ?>
            </tr>
        <?php
        } }



        ?>
        </tbody>
    </table>
</form>


