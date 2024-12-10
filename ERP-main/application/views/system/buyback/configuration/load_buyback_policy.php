
<table id="buyback_policy_table" class="<?php echo table_class(); ?>">
    <thead>
    <tr>
        <th style="width: 35px;">#</th>
        <th>Code</th>
        <th>Description</th>
        <th>Default Value</th>
    </tr>
    </thead>
    <tbody>
    <?php if($detail){
        $i=0;
        foreach($detail as $value){
            $i++;
            ?>
            <tr>
                <td style="width: 35px;"><?php echo $i;?></td>
                <td><?php echo $value['policyCode']?></td>
                <td><?php echo $value['policyDescription']?></td>
                <td><?php echo get_buyback_policy($value['fieldType'], $value['buybackPolicyMasterID'], $value['value']);  ?></td>
            </tr>
            <?php
        }
    }    ?>
    </tbody>
</table>





<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/17/2019
 * Time: 11:05 AM
 */