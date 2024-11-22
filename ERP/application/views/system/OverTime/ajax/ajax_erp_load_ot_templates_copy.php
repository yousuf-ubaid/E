<?php
$groupArr =array_group_by($empDetails, 'empID');
echo '<pre>';
//print_r($g);
echo '</pre>';
foreach ($g as $val2) {

    foreach ($val2 as $val) {
        echo $val['ECode'] . ' ' . $val['empname'].'<br/>';
    }
}



    ?>

<table class="table table-bordered table-striped table-condensed">
    <tr>
        <th>Emp Number</th>
        <th>Emp Name</th>
        <?php
        if (!empty($detail)){
        foreach ($detail as $val){
        ?>
        <th><?php if ($val['defaultcategoryID']==0) {
                echo $val['categoryDescription'];
            } else {
                echo $val['defultDescription'];
            } ?></th>

    <?php
    }
    }
    ?>
    </tr>
        <?php
        if (!empty($empDetails)){

            foreach ($groupArr as $val2){
                echo '<tr>';
                foreach ($val2 as $key=>$val){
                    echo ($key == 0)? '<td>'.$val['ECode'].'</td><td>'.$val['empname'].'</td>' : '';
                }
                echo '</tr>';
                ?>
    <tr>
                <td><?php echo $val['ECode']; ?></td>
                <td><?php echo $val['empname']; ?></td>
        <?php
        $empid='';
        $templatedetailID='';
        $inputType='';
        $hour='';
        $minute='';
        $Days='';
            foreach ($detail as $valu){
                ?>
                <td><?php if ($valu['inputType']==1) {
                        $empid=$val['empID'];
                        $templatedetailID=$valu['templatedetailID'];
                        $inputType=$valu['inputType'];
                        $hour='h';
                        $minute='m';
                        $Days='d';
                        ?>
                       <input type="text" id="<?php echo $hour.'|'.$empid.'|'.$templatedetailID ?>" onchange="saveTemplateData('h',$empid,$templatedetailID,this)" name="templatedetailID" style="width:27px !important;"> :
                       <input type="text" id="<?php echo $minute.'|'.$empid.'|'.$templatedetailID ?>" onchange="saveTemplateData('m',$empid,$templatedetailID,this)" name="templatedetailID" style="width:27px!important;">
                    <?php
                    } else {
                        ?>
                        <input type="number" id="<?php echo $Days.'|'.$empid.'|'.$templatedetailID ?>" onchange="saveTemplateData('d',$empid,$templatedetailID,this)" name="templatedetailID" style="width:50px !important;">
                    <?php
                    } ?></td>
                <?php
            }
        ?>
    </tr>
                <?php
            }
        }
        ?>

</table>

<script>
    function saveTemplateData(type,empid,templateid,data){
        if(type=='h'){
            var value=$
        }

    }
</script>


