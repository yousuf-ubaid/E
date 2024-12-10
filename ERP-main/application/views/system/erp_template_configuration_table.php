<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<table id="company_template_configuration" class="<?php echo table_class(); ?>">
    <thead>
    <tr>
        <th style="width: 35px;">#</th>
        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
        <th><?php echo $this->lang->line('config_default_value');?><!--Default Value--></th>
        <!--            <th style="width: 55px">Is Active</th>-->
    </tr>
    </thead>
    <tbody>
    <?php if ($detail) {

        $temp = array();
        foreach ($detail as $val) {
            $temp[$val["FormCatID"]] = $val;

        }
        foreach ($detail as $val) {
            $temp[$val["FormCatID"]]["detail"][] = $val;
        }
        $i = 0;
        foreach ($temp as $value) {
            $i++;
            ?>
            <tr>
                <td style="width: 35px;"><?php echo $i; ?></td>
                <td><?php echo $value['TempDes'] ?></td>
                <?php
                $temp2 = array();
                foreach ($value['detail'] as $val) {
                    $temp2[trim($val['TempMasterID']. ' | '.$val['FormCatID'])] = trim($val['TempDes'] ?? '');
                }
                ?>
                <td>
                <?php if ($value['detailTempMasterID']) {
                        $selectval = $value['detailTempMasterID'] . ' | ' . $value['FormCatID'];
                        echo form_dropdown('FormCatID',$temp2 , $selectval, 'class="form-control" style="width: 72%;" onchange="saveTemplate(this)" id="FormCatID"');
                    } else {
                        $selectval = $value['TempMasterID'] . ' | ' . $value['FormCatID'];
                        echo form_dropdown('FormCatID', $temp2, $selectval, 'class="form-control select2" style="width: 72%;" onchange="saveTemplate(this)" id="FormCatID"');
                    } ?>
                </td>
            </tr>
            <!--<tr>
                <td style="width: 35px;"><?php echo $i; ?></td>
                <td><?php echo $value['TempDes'] ?></td>
               <td></td>
            </tr>-->
            <?php
        }
    }


    ?>
    </tbody>
</table>



