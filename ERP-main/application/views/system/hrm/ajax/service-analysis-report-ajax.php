<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_service_analysis_report');
$current_date = current_format_date();
$yearFirst = convert_date_format( date('Y-01-01') );

$th_class = ($isForPrint == 'Y')? 'theadtr': '';
$segmentStyle = ($isForPrint == 'Y')? 'font-size: 11px;': '';
?>

<?php if($isForPrint == 'Y') { ?>
    <div class="table-responsive">
        <table style="width: 100%" border="0px">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;" valign="top">
                    <table border="0px">
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h5><strong><?=$title?></strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5><strong><?=$this->lang->line('common_category') .' : ' .ucfirst($category)?> </strong></h5>
                            </td>
                            <td>
                                <h5><?=$period?></h5>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
<?php } ?>
<div class="row" style="margin-top: 2%; ">
    <div class="col-sm-12" style="margin-bottom: 10px; <?=$segmentStyle?>">
        <span style="font-weight: bold"><?php echo $this->lang->line('common_segment');?></span> : <?=$segmentList?>
    </div>

    <div class="col-sm-12">
        <div style="height: 400px">
            <table id="report-tb" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                     <th class="<?=$th_class?>" rowspan="2">#</th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_emp_no');?></th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_employee_name');?></th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_designation');?></th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_segment');?></th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_joined_date');?></th>
                     <th class="<?=$th_class?>" rowspan="2"><?php echo $this->lang->line('common_discharge_date');?></th>
                     <th class="<?=$th_class?>" colspan="3"><?php echo $this->lang->line('common_service');?></th>
                </tr>
                <tr>
                    <th class="<?=$th_class?>"><?php echo $this->lang->line('common_years');?></th>
                    <th class="<?=$th_class?>"><?php echo $this->lang->line('common_months');?></th>
                    <th class="<?=$th_class?>"><?php echo $this->lang->line('common_days');?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                if(!empty($detail)) {
                    $r = 1;
                    $currentData = date('Y-m-d');
                    foreach ($detail as $row) {
                        $isDischarged = $row['isDischarged'];
                        $join = date('Y-m-d', strtotime($row['EDOJ']));
                        $endDate = ($isDischarged == 1) ? date('Y-m-d', strtotime($row['dischargedDate'])) : $currentData;

                        $s_year = '';
                        $s_month = '';
                        $s_day = '';
                        if ($join <= $endDate) {
                            $d1 = new DateTime($join);
                            $d2 = new DateTime($endDate);

                            $diff = $d2->diff($d1);
                            $s_year = $diff->y;
                            $s_month = $diff->m;
                            $s_day = $diff->d;
                        }

                        echo '<tr>
                            <td>' . $r . '</td>
                            <td>' . $row['ECode'] . '</td>
                            <td>' . $row['Ename2'] . '</td>                                                       
                            <td>' . $row['designationStr'] . '</td>
                            <td>' . $row['segmentStr'] . '</td>
                            <td>' . $row['EDOJ2'] . '</td>
                            <td>' . $row['dischargedDate2'] . '</td>
                            <td>' . $s_year . '</td>
                            <td>' . $s_month . '</td>
                            <td>' . $s_day . '</td>
                          </tr>';
                        $r++;
                    }
                }
                else{
                    echo '<tr>
                              <td colspan="10" style="text-align: center">'.$this->lang->line('common_no_records_found').'</td>
                          </tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('#report-tb').tableHeadFixer({
        head: true,
        foot: true,
        left: 3,
        right: 0,
        'z-index': 0
    });
</script>
<?php
