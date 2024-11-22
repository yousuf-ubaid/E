<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($this->lang->line('hrms_reports_epfreports'),false);
$epfMasterID = $this->input->post('page_id');
?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="report-content">

</div>
<?php  echo  $this->lang->line('hrms_reports_lang')?>
    <!--EPF Reports-->



<?php echo footer_page('Right foot','Left foot',false); ?>


<script type="text/javascript">

    var epfMasterID = "<?php echo $epfMasterID; ?>";

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/report/erp_employee_epf', epfMasterID, 'EPF Reports');
        });

        get_reportData_view();
    });

    function get_reportData_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'epfMasterID' : epfMasterID},
            url: "<?php echo site_url('Report/epf_reportData_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#report-content').html(data);
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>

<?php
