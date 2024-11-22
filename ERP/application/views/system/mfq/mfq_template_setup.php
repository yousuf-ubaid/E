<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<?php
$title = $this->lang->line('common_template');
echo head_page($title, false);
//echo head_page('TEMPLATE', false); ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #workflow_template th{
        text-transform: uppercase;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class=" pull-right">
            <button type="button" data-text="Add" id="btnAdd" onclick="addWorkFlowTemplate()"
                    class="btn btn-sm btn-primary">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
            </button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="workflow_template" class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th style="min-width: 5%">&nbsp;</th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_workflow_category');?><!--WORKFLOW CATEGORY--></th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_description');?><!--DESCRIPTION--></th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_page_link');?><!--PAGE LINK--></th>
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Work Flow Modal" data-backdrop="static"
     data-keyboard="false"
     id="workflowTemplateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times text-red"></i></span></button>
                <h4 class="modal-title" id="modal_title_category"><?php echo $this->lang->line('manufacturing_workflow_template');?><!--Workflow Template--> </h4>
            </div>
            <form id="frm_mfq_workflow_template">
                <div class="modal-body">
                    <input type="hidden" value="0" id="workFlowTemplateID" name="workFlowTemplateID">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('manufacturing_workflow_category');?><!--WORKFLOW CATEGORY--></label>
                        </div>
                        <div class="form-group col-sm-6">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('workFlowID', get_all_system_workflow(), '', 'class="form-control" id="workFlowID"  required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('common_description');?><!--DESCRIPTION--> </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('manufacturing_page_link');?><!--PAGE LINK--> </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="pageNameLink" id="pageNameLink"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    var oTable2;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_template_setup', 'Test', 'Item Master');
        });
        work_flow();

        $('#frm_mfq_workflow_template').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                workFlowID: {validators: {notEmpty: {message: 'Work FlowID is required'}}},
                description: {validators: {notEmpty: {message: 'Description is required'}}},
                pageNameLink: {validators: {notEmpty: {message: 'Page Link is required'}}}
            }
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
                url: "<?php echo site_url('MFQ_Template/save_work_flow_template'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        $('#workflowTemplateModal').modal('hide');
                        work_flow();
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    function addWorkFlowTemplate() {
        $('#frm_mfq_workflow_template')[0].reset();
        $('#frm_mfq_workflow_template').bootstrapValidator('resetForm', true);
        $('#workFlowTemplateID').val('');
        $('#workflowTemplateModal').modal();
    }

    function work_flow() {
        oTable = $('#workflow_template').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Template/fetch_workflow_template'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },

            "aoColumns": [
                {"mData": "workFlowTemplateID"},
                {"mData": "workFlowDescription"},
                {"mData": "description"},
                {"mData": "pageNameLink"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [0], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function edit_work_flow_template(workFlowTemplateID){
        $('#workFlowTemplateID').val(workFlowTemplateID);
        $.ajax({
            type: 'post',
            dataType: 'json',
            data:{workFlowTemplateID:workFlowTemplateID},
            url: "<?php echo site_url('MFQ_Template/edit_work_flow_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#frm_mfq_workflow_template').bootstrapValidator('resetForm', true);
                    $('#description').val(data['description']);
                    $('#workFlowID').val(data['workFlowID']).change();
                    $('#pageNameLink').val(data['pageNameLink']);
                    $('#workflowTemplateModal').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>