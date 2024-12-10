<?php
$this->load->helpers('codification');
$companyID=$this->common_data['company_data']['company_id'];
?>

<?php
$i=0;
foreach ($codificatntbl as $val){
    $i++;
    $valueType='Numeric';
    if($val['valueType']==0){
        $valueType='Text';
    }
    $masterID=$val['masterID'];
    $attributeID=$val['attributeID'];
    $attributeDescription=$val['attributeDescription'];
    $status = '<span class="pull-right">';
    if($masterID==0){
        $status .= '<a onclick=\'addSubAttribute("' . $attributeID . '",1); \'><span title="Add Sub Attribute" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
    }
    $status .= '<a onclick=\'addAttrDetail("' . $attributeID . '","' . $masterID . '","' . $attributeDescription . '"); \'><span title="Add Attribute Detail" rel="tooltip" class="glyphicon glyphicon-align-justify" ></span></a>';
    $status .= '</span>';

    $subitem = $this->db->query("SELECT
	`attributeID`,
	`valueType`,
	`masterID`,
	`attributeDescription` 
FROM
	`srp_erp_itemcodificationattributes` 
WHERE
	`companyID` = $companyID 
AND masterID =$attributeID 
AND levelNo=1  ")->result_array();
    ?>
    <tr>
        <td style="font-weight: bold;"><?php echo $i ?></td>
        <td style="font-weight: bold;"><?php echo $val['attributeDescription'] ?></td>
        <td style="font-weight: bold;"><?php echo $valueType ?></td>
        <td><?php echo $status ?></td>
    </tr>

    <?php
    if(!empty($subitem)){
        foreach ($subitem as $valsub){
            //$i++;

            $valueTypesub='Numeric';
            if($valsub['valueType']==0){
                $valueTypesub='Text';
            }
            $attributeID=$valsub['attributeID'];
            $attributeDescription=$valsub['attributeDescription'];
            $masterID=$valsub['masterID'];
            $statussub = '<span class="pull-right">';
            $statussub .= '<a onclick=\'addSubAttribute("' . $attributeID . '",2); \'><span title="Add Sub Attribute" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $statussub .= '<a onclick=\'addAttrDetail("' . $attributeID . '","' . $masterID . '","' . $attributeDescription . '"); \'><span title="Add Attribute Detail" rel="tooltip" class="glyphicon glyphicon-align-justify" ></span></a>';
            $statussub .= '</span>';

            $subsubitem = $this->db->query("SELECT
	`attributeID`,
	`valueType`,
	`masterID`,
	`attributeDescription` 
FROM
	`srp_erp_itemcodificationattributes` 
WHERE
	`companyID` = $companyID 
AND masterID =$attributeID 
AND levelNo=2  ")->result_array();
     ?>
            <tr>
                <td style="padding-left: 30px;">-</td>
                <td style="padding-left: 30px;"><?php echo $valsub['attributeDescription'] ?></td>
                <td style="padding-left: 30px;"><?php echo $valueTypesub ?></td>
                <td ><?php echo $statussub ?></td>
            </tr>

            <?php
            foreach ($subsubitem as $levltw){
                $valueTypesubsub='Numeric';
                if($levltw['valueType']==0){
                    $valueTypesubsub='Text';
                }
                $attributeID=$levltw['attributeID'];
                $masterID=$levltw['masterID'];
                $attributeDescription=$levltw['attributeDescription'];
                $statussubsub = '<span class="pull-right">';
                $statussubsub .= '<a onclick=\'addAttrDetail("' . $attributeID . '","' . $masterID . '","' . $attributeDescription . '"); \'><span title="Add Attribute Detail" rel="tooltip" class="glyphicon glyphicon-align-justify" ></span></a>';
                $statussubsub .= '</span>';
                ?>
                <tr>
                    <td style="padding-left: 50px;">-</td>
                    <td style="padding-left: 50px;"><?php echo $levltw['attributeDescription'] ?></td>
                    <td style="padding-left: 50px;"><?php echo $valueTypesubsub ?></td>
                    <td ><?php echo $statussubsub ?></td>
                </tr>
                <?php
            }
            ?>


     <?php
    }
    }
    ?>
<?php
}

?>