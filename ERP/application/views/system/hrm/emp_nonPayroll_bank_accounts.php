

<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_non_payroll_bank', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('emp_non_payroll_banks');
echo head_page($title, false);



?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-xs-6 col-sm-6">
            <table class="table table-bordered table-striped table-condensed ">
                <tbody><tr>
                    <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_active'); ?><!--Active--> </td>
                    <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_in_active'); ?><!--In Active--> </td>
                </tr>
                </tbody></table>
        </div>
        <div class="col-xs-6 col-sm-6 pull-right">

        </div>
    </div><hr>
    <div id="load-emp-non-payroll-bank-content">
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">

    $(document).ready(function(){
        $('.headerclose').click(function(){
            fetchPage('system/hrm/emp_nonPayroll_bank_accounts','Test','HRMS');
        });
        load_nonPayrollEmployees();
    });

    function load_nonPayrollEmployees(){
        $.ajax({
            async : true,
            url :"<?php echo site_url('Employee/load_nonPayrollEmployees'); ?>",
            type : 'post',
            dataType : 'html',
            beforeSend: function () {
                startLoad();

            },
            success : function(data){
                stopLoad();
                $('#load-emp-non-payroll-bank-content').html(data);

            },error : function(){
                stopLoad();
                myAlert('e', 'error');

            }
        });
    }
</script>

<?php
