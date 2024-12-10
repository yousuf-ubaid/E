<?php
$groupArr =array_group_by($empDetails, 'empID');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
    ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('hrms_attendance_generalovertime') ?><!--General Over Time--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_attendance_overtimecode') ?><!--Over Time Code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['otCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('hrms_attendance_overtimedate') ?><!--Over Time Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_description') ?><!--Description--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['description']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<table class="table table-bordered table-striped table-condensed">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('hrms_attendance_empnum') ?><!--Emp Number--></th>
        <th><?php echo $this->lang->line('hrms_attendance_empname') ?><!--Emp Name--></th>
        <?php
        if (!empty($detail)){
        foreach ($detail as $val){
        ?>
        <th><?php if ($val['defaultcategoryID']==0) {
                echo $val['categoryDescription'];
            } else {
                echo $val['defultDescription'];
            } ?></th>
            <th><?php echo $this->lang->line('hrms_attendance_rate') ?><!--Rate--></th>

    <?php
    }
    }
    ?>
    </tr>
    </thead>
    <?php
    if (!empty($empDetails)){
        echo'<tbody>';
        foreach ($groupArr as $val2){
            $generalOTMasterID = $val2[0]['generalOTMasterID'];
            $empID = $val2[0]['empID'];
            echo '<tr>';
            echo '<td>'.$val2[0]['ECode'].'</td><td>'.$val2[0]['empname'].'</td>';
            foreach ($detail as $val) {
                $tempID = $val['templatedetailID'];
                $inputType = $val['inputType'];
                $hours = search_otElement($val2, $tempID);
                $amount = search_otAmount($val2, $tempID);
                if($val['inputType']==1){
                    $hrs=floor($hours/60);
                    $min=$hours%60;
                    echo '<td style="text-align: right;">'.$hrs.' : '.$min.'</td>';
                }else{
                    echo '<td style="text-align: right;"> '.$hours.' </td>';
                }
                echo '<td style="text-align: right;">'.$amount.'</td>';

                //echo '<td> <input type="number" id="" value="'.$hours.'" name="hourorDays[]"/> </td>';
            }
            echo '</tr>';
        }
        echo'</tbody>';
    }
    ?>
</table>


