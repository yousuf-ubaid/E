<?php
/** Translation added  */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
    <div class="panel panel-default animated zoomIn">
        <div class="panel-heading"><?php echo $this->lang->line('profile_monthly_allowance'); ?><!--Pay Slip - Monthly Allowance--></div>
        <div class="tab-content">
            <div class="panel-body">
                <form class="form-horizontal" method="post" id="passwordForm">
                    <div class="form-group">
                        <label for="confirmPassword" class="col-sm-3 control-label"><?php echo $this->lang->line('common_month'); ?><!--Month--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('payrollMasterID', payroll_dropdown('Y'), '', ' onchange="get_paySlip()" class="form-control select2"
                        id="payrollMasterID" required'); ?>
                        </div>
                    </div>
                </form>
                <div id="load-pay-slip">

                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });

        function get_paySlip() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Template_paysheet/response_payslip_pdf') ?>",
                data: {
                    payrollMasterID: $('#payrollMasterID').val(),
                    empID:<?php echo current_userID() ?>,
                    isNonPayroll: 'Y'
                },
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                    //$("#load-pay-slip").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i> Loading</div>");
                },
                success: function (data) {
                    stopLoad();
                    $("#load-pay-slip").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    //$("#load-pay-slip").html('');
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }
    </script>


<?php
