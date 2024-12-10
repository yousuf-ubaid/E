<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = 'Budget Approval';
echo head_page($title, false);

/*echo head_page('Budget Detail', false);*/
$page_id=trim($this->input->post('page_id'));
$policy_id=trim($this->input->post('policy_id'));
?>
<div id="filter-panel" class="collapse filter-panel"></div>




<div class="row" id="detailData">



</div>
<br>
<form class="form-horizontal" id="bd_approval_form">
<div class="row">
    <div class="col-sm-12">
        <div class="form-group col-sm-4">
            <label for="">Status</label>
            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
            <input type="hidden" name="Level" value="<?php echo $policy_id; ?>" id="Level">
            <input type="hidden" name="budgetAutoID" id="budgetAutoID" value="<?php echo $page_id; ?>">
        </div>
    </div>

</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group col-sm-4">
            <label for="">Comments</label>
            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
        </div>
    </div>
</div>
<div class="">
    <button class="btn btn-success submitWizard" type="submit">Approve</button>
</div>
</form>
<br>




<script type="text/javascript">
    $(document).ready(function () {
        masterID = '<?php if(isset($_POST['data_arr']) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; }; ?>';
    $('#bd_approval_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: 'Budget Transfer Status is required.'}}},
            budgetAutoID: {validators: {notEmpty: {message: 'Budget ID is required.'}}}
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Budget/save_budget_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                if(masterID == 'ALLApproval')
                {
                    fetchPage('system/documentallapprovalview','','Document Approval ');
                }else
                {
                    fetchPage('system/finance/budget_approval','','Budget ');
                }

                $form.bootstrapValidator('disableSubmitButtons', false);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
        $('.headerclose').click(function(){
            if(masterID == 'ALLApproval')
            {
                fetchPage('system/documentallapprovalview','','Document Approval ');
            }else
            {
                fetchPage('system/finance/budget_approval','','Budget ');
            }
        });
    });

    budgetAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    budgetAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    get_budget_detail(budgetAutoID);

        function get_budget_detail(budgetAutoID) {
            var viewtype='approval';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'budgetAutoID': budgetAutoID,'viewtype': viewtype},
                url: "<?php echo site_url('Budget/get_budget_detail_data'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                        $('#detailData').html(data);
                    stopLoad();
                    refreshNotifications(true);
                },
                error: function () {

                }
            });
        }





</script>