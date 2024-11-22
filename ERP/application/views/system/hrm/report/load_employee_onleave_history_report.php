<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if (!empty($details)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') { ?>
                <div class="row" style="margin-top: 5px">
                    <div class="col-md-12">
                        <div class="pull-right"><button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportPdf_ol()">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </button> <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Employee onleave.xls" onclick="var file = tableToExcel('onLeaveReport', 'Onleave Report'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a></div>        </div>
                </div>
            <?php                 //echo export_buttons('onLeaveReport', 'Leave History', True, True,'btn-xs','generateReportPdf_ol()');
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="onLeaveReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('hrms_reports_leave_history')?><!--Leave History--></strong></div>
            <div style="">
                <table id="tbl_rpt_onleave" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_name')?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_document_code')?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('hrms_reports_employee_leave_type')?><!--Leave Type--></th>
                        <th><?php echo $this->lang->line('common_start_date')?><!--Start Date--></th>
                        <th><?php echo $this->lang->line('common_end_date')?><!--End Date--></th>
                        <th><?php echo $this->lang->line('common_days')?><!--Days--></th>
                        <th><?php echo $this->lang->line('common_remarks')?><!--Remarks--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $details = array_group_by($details, 'empname');
                        foreach ($details as $key => $value) {
                            ?>
                            <tr>
                                <td class="" colspan="7"></td>
                            </tr>
                            <?php
                            //$total=0;
                            foreach ($value as $val) {
                                ?>
                                <tr>
                                    <td ><?php echo $key ?></td>
                                    <td ><?php echo $val["documentCode"] ?></td>
                                    <td ><?php echo $val["description"] ?></td>
                                    <td ><?php echo $val["startDate"] ?></td>
                                    <td ><?php echo $val["endDate"] ?></td>
                                    <td class="text-center"><?php echo $val["comments"] ?></td>
                                </tr>
                                <?php

                               // $total += $val["days"];
                            }
                            ?>
                            <?php
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_onleave').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>