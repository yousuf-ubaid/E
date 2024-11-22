<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$compid = current_companyID();

$empdesignation = $this->db->query("SELECT
srp_designation.DesDescription,
	COUNT(srp_designation.DesDescription)as designationCount
FROM
	srp_employeesdetails
JOIN srp_designation on srp_employeesdetails.EmpDesignationId=srp_designation.DesignationID
WHERE
	srp_employeesdetails.isDischarged = 0 AND 
	srp_employeesdetails.isSystemAdmin = 0 AND 
	srp_employeesdetails.isPayrollEmployee = 1
AND srp_employeesdetails.Erp_companyID = $compid
GROUP BY srp_employeesdetails.EmpDesignationId ")->result_array();
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_head_count_by_designation');?><!--Head Count by Designation-->(<small><?php echo $this->lang->line('dashboard_payroll_employees_only');?><!--Payroll employees only--></small>)</h4>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <ul class="nav nav-stacked">
            <?php
            foreach ($empdesignation as $val) {
                ?>
                <li class="active"><a  href="#" style="padding-bottom:5px !important; padding-top: 5px!important;" ><?php echo $val['DesDescription']; ?> <span class="pull-right badge bg-green"><?php echo $val['designationCount']; ?></span></a></li>
            <?php
            }
            ?>

        </ul>
    </div>
    <div class="overlay" id="overlay16<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>
