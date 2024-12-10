<div style="margin-top: 5%" > &nbsp; </div>
<?php
$leaveDet = $masterData['leaveDet'];
$empDet = $masterData['empDet'];
$entitleDet = $masterData['entitleDet'];

$title = '';
if($leaveDet['requestForCancelYN'] == 1){
    $title = ($leaveDet['cancelledYN'] == 1)? 'cancelled': 'pending for cancellation';
    $title = '- <i style="font-size: 12px">'.$title.'</i>';
}


?>
<div class="table-responsive">
    <table style="width: 100%"  border="1px solid #ffff">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>

                    </tr>

                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name'] ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px">Leave Application <?php echo $title;  ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>
<?php $convertFormat=convert_date_format(); ?>
<div class="table-responsive" style="margin-top: 2%">

    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:16%;"><strong>Date</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;"><?php  echo format_date($leaveDet['entryDate'],$convertFormat) ;  ?></td>
            <td style="width:20%;"><strong>Employee ID</strong></td>
            <td style="width:3%;"><strong>:</strong></td>
            <td style="width:30%;"><?php echo $empDet['EmpSecondaryCode']; ?></td>
        </tr>
        <tr>
            <td style="width:16%;"><strong>Document Code</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:16%;"><?php echo $leaveDet['documentCode']; ?></td>

            <td><strong>Designation</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $empDet['DesDescription']; ?></td>
        </tr>

        <tr>
            <td style="width:12%;"><strong>Employee Name</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;"><?php echo $empDet['ECode'].' | '.$empDet['employee']; ?></td>

            <td style="width:16%;"><strong>Department</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:16%;"><?php echo $empDet['department']; ?></td>
        </tr>
        <tr>
            <td><strong>Reporting Manager</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $empDet['manager']; ?></td>
            <td style="width:16%;"><strong>Date of Join</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:16%;"><?php echo $empDet['DateAssumed']; ?></td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive" style="width;100%">
    <div style="width: 50%; float: left">
        <table  style="width:100%;margin-top: 2%">
            <tbody>
            <tr>
                <td style="width:32%;"><strong>Leave Policy</strong></td>
                <td style="width:3%;"><strong>:</strong></td>
                <td style="width:65%"><?php echo (!empty($entitleDet['policyDescription']))? $entitleDet['policyDescription'] : 'none';  ?></td>

            </tr>
            <tr>
                <td style="width:32%;"><strong>Leave Type</strong></td>
                <td style="width:3%;"><strong>:</strong></td>
                <td style="width:65%"><?php echo $leaveDescription  ?></td>


            </tr>

            <tr>
                <td style="width:32%;"><strong>Start Date</strong></td>
                <td style="width:3%;"><strong>:</strong></td>
                <td style="width:65%"> <?php  echo format_date($leaveDet['startDate'],$convertFormat) ;  ?> </td>

            </tr>
            <tr>
                <td style="width:32%;"><strong>End Date</strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style=""><?php  echo format_date($leaveDet['endDate'],$convertFormat) ;  ?> </td>
            </tr>
            <?php
            if(!empty($leaveDet['shift'])){
                if($leaveDet['shift']==1 || $leaveDet['shift']==2){
                    $shft='Evening Shift';
                    if($leaveDet['shift']==1){
                        $shft='Morning Shift';
                    }
               ?>
                <tr>
                    <td style="width:32%;"><strong>Shift</strong></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style=""><?php  echo $shft ;  ?> </td>
                </tr>
            <?php

                }
            }
            ?>

            </tbody>
        </table>
    </div>
    <div style="width: 50%;float: left">
        <table  class="table table-bordered" style="width: 100%; margin-top: 2%;font-size: 10px">
            <tr>
                <th class="theadtr" style="">No of Days Available</th>
                <th align="right" style="font-size: 10px"><?php echo $leaveDet['leaveAvailable']; echo (!empty($leaveDet['leaveAvailable']))?'':'0'; ?> </th>
            </tr>

            <tr>
                <th class="theadtr" style="">No. of Days</th>
               <!--leave enetiled=balance-->
                <th align="right"  style="font-size: 10px"><?php echo $leaveDet['days']; ?></th>
            </tr>
            <tr>
                <th class="theadtr" style="">Leave Balance</th>
                <!--leave enetiled=balance-->
                <th align="right"  style="font-size: 10px"><?php   echo( !empty($entitleDet['balance']) ) ? ( $leaveDet['leaveAvailable']-$leaveDet['days'] ) : '0'; ?></th>
            </tr>
        </table>
    </div>
</div>

<div class="table-responsive" style="margin-top: 2%">
    <table style="width: 50%;">
        <tbody>
        <tr>
    <td style="width:32%;"><strong>Covering Employee</strong></td>
    <td style="width:3%;"><strong>:</strong></td>
    <td style="width:65%">
        <?php
        if (!empty($coveringEmp)) {
            foreach ($coveringEmp as $value) {
                echo isset($value->coveringEmp) ? $value->coveringEmp . '<br>' : '-';
            }
        } else {
            echo '-';
        }
        ?>
    </td>
</tr>


        <tr>
            <td style="width:32%;"><strong>Comment</strong></td>
            <td style="width:3%;"><strong>:</strong></td>
            <td style="width:65%"><?php echo (!empty($leaveDet['comments']))? $leaveDet['comments'] : '';  ?></td>
        </tr>

        <tr>
            <td style="width:32%;"><strong>Reason</strong></td>
            <td style="width:3%;"><strong>:</strong></td>
            <td style="width:65%"><?php echo (!empty($leaveDet['leaveReasonText']))? $leaveDet['leaveReasonText'] : '';  ?></td>
        </tr>

        <tr>
            <td style="width:32%;"><strong>Contact Details</strong></td>
            <td style="width:3%;"><strong>:</strong></td>
            <td style="width:65%"><?php echo (!empty($leaveDet['annualComment']))? $leaveDet['annualComment'] : '';  ?></td>
        </tr>

        <?php if( $leaveDet['confirmedYN'] == 1 ){ ?>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr>
                <td><strong>Confirmed By</strong></td>
                <td><strong>:</strong></td>
                <td>
                    <?php echo $leaveDet['confirmedByName']; ?>
                    On
                    <?php
                    $confirmedDate = date('Y-m-d', strtotime($leaveDet['confirmedDate']));
                    $confirmedTime = date('H:i:s', strtotime($leaveDet['confirmedDate']));
                    $confirmedTime = ($confirmedTime == '00:00:00')? '' : $confirmedTime;
                    echo $confirmedDate.' '.$confirmedTime;
                    ?>
                </td>
            </tr>
            <?php
        }
        if( $leaveDet['confirmedYN'] == 3 ){ ?>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr>
                <td><strong>Referred back by</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $leaveDet['confirmedByName']; ?></td>
                <td><strong> Comment </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $leaveDet['approvalComments']; ?></td>
            </tr>
          <?php
        }
        ?>
        </tbody>
    </table>
</div>

<?php
if( !empty($appData) ) {
    ?>
    <div class="table-responsive" style="width;100%">
        <!--<div style="width: 50%; float: left">-->
            <table  style="width:100%;margin-top: 2%;">
                <tbody>
                    <?php
                    foreach($appData as $key=>$app_row){
                        $str = '';
                        $str = ($key == 0)? 'Approved By': '&nbsp;';
                        $str1 = ($key == 0)? ':': '&nbsp;';

                        if($app_row['isCancel'] == 0){
                            echo '<tr>
                                  <td style="width:12%; vertical-align: top">'.$str.'</td>
                                  <td style="width:1%; vertical-align: top">'.$str1.'</td>
                                  <td style="width:65%;">
                                    <strong>'.$app_row['approvalLevelID'].' . '.$app_row['Ename2'].'</strong> 
                                    On <strong>'.$app_row['approvedDate'].'&nbsp; - &nbsp;'.$app_row['approvedComments'].'</strong> 
                                  </td>
                              </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        <!--</div>-->
        <div style="width: 50%; float: left">
            <table style="width: 50%;">
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>
<?php
    if( $leaveDet['requestForCancelYN'] == 1 ) {
    ?>
        <div class="table-responsive" style="width;100%">
            <table  style="width:100%;">
                <tbody>
                <tr>
                    <td><h5>Cancellation Details</h5></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive" style="width;100%">
            <table  style="width:100%;">
                <tbody>
                <?php
                    echo '<tr>
                              <td style="width:12%;">Requested By</td>
                              <td style="width: 5px;">:</td>
                              <td style="width:65%;">
                                <strong>'.$cancelData['Ename2'].'  </strong> 
                                On <strong>'.$cancelData['cancelRequestedDate'].'&nbsp; - &nbsp;'.$cancelData['cancelRequestComment'].'</strong> 
                              </td>
                          </tr>';

                $n = 0;
                foreach($appData as $key=>$app_row){
                    $str = '';
                    $str = ($n == 0)? 'Approved By': '&nbsp;';
                    $str1 = ($n == 0)? ':': '&nbsp;';

                    if($app_row['isCancel'] == 1){
                        echo '<tr>
                                  <td style="width:12%; vertical-align: top">'.$str.'</td>
                                  <td style="width:5px; vertical-align: top">'.$str1.'</td>
                                  <td style="width:65%;">
                                    <strong>'.$app_row['approvalLevelID'].' . '.$app_row['Ename2'].'</strong> 
                                    On <strong>'.$app_row['approvedDate'].'&nbsp; - &nbsp;'.$app_row['approvedComments'].'</strong> 
                                  </td>
                              </tr>';
                        $n++;
                    }
                }
                ?>
                </tbody>
            </table>
            <div style="width: 50%; float: left">
                <table style="width: 50%;">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div>
    <?php
    }
}
?>
<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-09-07
 * Time: 12:13 PM
 */