<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_birthday_report');
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
                            <td colspan="2">
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
<div class="row" style="margin-top: 2%">
    <div class="col-sm-12" style="margin-bottom: 10px; <?=$segmentStyle?>">
        <span style="font-weight: bold"><?php echo $this->lang->line('common_segment');?></span> : <?=$segmentList?>
    </div>


    <div class="col-sm-12">
        <div style="height: 400px">
            <table id="report-tb" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <th class="<?=$th_class?>" >#</th>
                    <th class="<?=$th_class?>" ><?php echo $this->lang->line('common_emp_no');?></th>
                    <th class="<?=$th_class?>" ><?php echo $this->lang->line('common_employee_name');?></th>
                    <th class="<?=$th_class?>" ><?php echo $this->lang->line('common_designation');?></th>
                    <th class="<?=$th_class?>" ><?php echo $this->lang->line('common_segment');?></th>
                    <th class="<?=$th_class?>" ><?php echo $this->lang->line('common_date_of_birth');?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                if(!empty($detail)) {
                    $r = 1;

                    foreach ($detail as $row) {
                        echo '<tr>
                                <td>' . $r . '</td>
                                <td>' . $row['ECode'] . '</td>
                                <td>' . $row['Ename2'] . '</td>                                                       
                                <td>' . $row['designationStr'] . '</td>
                                <td>' . $row['segmentStr'] .'</td>
                                <td>' . $row['dob'] . '</td>                                               
                              </tr>';
                        $r++;
                    }
                }
                else{
                    echo '<tr>
                              <td colspan="6" style="text-align: center">'.$this->lang->line('common_no_records_found').'</td>
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
