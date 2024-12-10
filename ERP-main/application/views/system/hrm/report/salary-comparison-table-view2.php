<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$responseType = $this->uri->segment(3);
if($responseType == 'print') {
    $firstMonth = $this->input->post('firstMonth');
    $secondMonth = $this->input->post('secondMonth');
?>
    <table style="width: 100%">
        <tbody>
            <tr>
            <td style="width:20%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:80%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong>
                                    <?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?>
                                </strong>
                            </h3>
                            <h4>Salary Comparison</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?php echo date('Y F', strtotime($firstMonth)).' &nbsp;&nbsp;-&nbsp;&nbsp; '. date('Y F', strtotime($secondMonth)); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <?php
}
if ($emp_list) {
    ?>
    <span style="font-size: 12px; font-weight: bold;margin-right: 20px"><?php echo $this->lang->line('hrms_reports_first_month');?><!--FM - First Month--></span>
    <span style="margin-left:20px;font-size: 12px; font-weight: bold"><?php echo $this->lang->line('hrms_reports_scond_month');?><!--SM - Second Month--></span>
    <br/>
    <h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: 2px;"></h5>
    <div style="height: 450px">
        <table id="salaryComparisonDetTB" class="table table-bordered"  style="margin-top: -2px">
            <thead>
            <tr>
                <th class="first" rowspan="2" style="width:80px;"><?php echo $this->lang->line('hrms_reports_employee_id');?><!--Emp&nbsp;ID--></th>
                <th rowspan="2" ><div style="width:250px"><?php echo $this->lang->line('hrms_reports_employee_name');?><!--Employee&nbsp;Name--></div></th>
                <?php
                $nxt_row = '';
                foreach ($sal_cat as $key=>$sal_row){
                    $class = ($key%2 == 0)? '': 'odd_column';
                    echo '<th class="'.$class.'" colspan="3">'.$sal_row['salaryDescription'].'</th>';
                    $nxt_row .= '<th class="'.$class.'">FM</th><th class="'.$class.'">SM</th>
                                 <th class="'.$class.'">Diff</th>';
                }
                ?>
            </tr>
            <tr><?php echo $nxt_row; ?></tr>
            </thead>
            <tbody>
            <?php
            $dPlace = 3;
            $first_det = array_group_by($first_det, 'empID');
            $sec_det = array_group_by($sec_det, 'empID');

            $str = '';
            foreach ($emp_list as $item){
                $empID = $item['empID'];
                $str .= '<tr data-value="'.$item['empCode'].' - '.$item['empName'].'">
                         <td><b>'.$item['empCode'].'</b></td> 
                         <td>'.$item['empName'].'</td>';

                $this_first = []; $this_second = [];
                if(array_key_exists($empID, $first_det)){
                    $this_first = $first_det[$empID];
                }

                if(array_key_exists($empID, $sec_det)){
                    $this_second = $sec_det[$empID];
                }

                foreach ($sal_cat as $key=>$sal_row){
                    $catID = $sal_row['salaryCategoryID'];
                    $fm = 0; $sm = 0; $diff = 0;

                    if(!empty($this_first)){
                        $first_arr = array_group_by($this_first, 'salKey');
                        $fm = (array_key_exists($catID, $first_arr))? $first_arr[$catID][0]['amount']: 0;
                    }

                    if(!empty($this_second)){
                        $second_arr = array_group_by($this_second, 'salKey');
                        $sm = (array_key_exists($catID, $second_arr))? $second_arr[$catID][0]['amount']: 0;
                    }

                    $diff = $fm - $sm;

                    $class = ($key%2 == 0)? '': 'odd_column';

                    $str .= '<td class="'.$class.'">'.number_format($fm, $dPlace).'</td>'; //style="background-color: #EFEFF2"
                    $str .= '<td class="'.$class.'">'.number_format($sm, $dPlace).'</td>';
                    $str .= '<td class="'.$class.'">'.number_format($diff, $dPlace).'</td>';
                }

                $str .= '</tr>';
            }

            echo $str;
            ?>
            </tbody>
        </table>
    </div>
    <h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: -7px;"></h5>
    <?php
}
else {
    ?>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php if ($error) {
                    echo $error;
                } else { ?>
                    No Records found.
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

if($responseType != 'print') {
?>

<script>
    let selected_employee_det = $('.selected-employee-det');

    $('#salaryComparisonDetTB tbody').on('click', 'tr', function () {
        $(this).addClass('highlight').siblings().removeClass('highlight');

        let curEmp = $(this).attr('data-value');
        if(curEmp != undefined){
            selected_employee_det.css('display', 'block');
            selected_employee_det.html( curEmp+ ' <span class="pull-right">'+curEmp+'</span>' );
        }
        else{
            selected_employee_det.css('display', 'none');
        }
    });
</script>
<?php } ?>