<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_salary_comparison_configuration');
echo head_page($title  , false);


$comparisonData = get_salaryComparison('left');
$salary_categories = salary_categories(array('A', 'D'));
$pay_groups = get_payGroup(1);
?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <form id="companyLevelConf_form">
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 20px">#</th>
                    <th style="width: 150px"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th><?php echo $this->lang->line('hrms_reports_formula');?><!--Formula--></th>
                    <th style="width: 20px"></th>
                </tr>
                </thead>

                <tbody>
                <?php
                $decodeUrl = site_url('Employee/formulaDecode/isSalaryComparison');
                if(!empty($comparisonData)){
                    foreach($comparisonData as $key=>$row){
                        $description = $row['description'];
                        $formula = $row['formulaStr'];
                        $encode = payGroup_formulaBuilder_to_sql('encode', $formula, $salary_categories, $pay_groups);
                        $inputStr = '<a onclick="formulaModalOpen(\''.$description.'\', \''.$row['id'].'\', \''.$decodeUrl.'\', \'row_'.($key+1).'\')">';
                        $inputStr .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></a>';

                        echo '<tr>
                                <td>'.($key+1).'</td>
                                <td>'.$description.'</td>
                                <td id="row_'.($key+1).'" >'.$encode[0].'</td>
                                <td>'.$inputStr.'</td>
                              </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="clearfix">&nbsp;</div>
            <input type="hidden" name="masterID" value="1">
            <input type="hidden" name="reportType" value="EPF">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_companyLevelReportDetails()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
        </div>
    </form>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>

<?php
$items = [
    'MA_MD' => true,
    'balancePay' => true,
    'SSO' => true,
    'payGroup' => false,
    'only_salCat_payGroup' => true
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var urlSave = '<?php echo site_url('Report/save_salaryComparisonFormula') ?>';
    var isPaySheetGroup = 0;

    $("[rel=tooltip]").tooltip();

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/report_master', '', 'Reports Master');
    });
</script>




<?php
