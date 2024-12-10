<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_final_settlement_title');
echo head_page($title, false);
$status_arr = ['0'=>$this->lang->line('common_pending'),'1'=>$this->lang->line('common_approved')];
$appDrop = [
    ''=>$this->lang->line('common_please_select'),
    '1'=>$this->lang->line('common_approved'),
    '2'=>$this->lang->line('common_refer_back')
];
?>


<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved'); ?> <!--Not Approved-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', $status_arr, '','class="form-control" id="approvedYN" required onchange="final_settlement_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="final_settlement_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee_details');?></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_narration');?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var finalSet_tb = '';
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/final-settlement-approval','','HRMS');
        });

        final_settlement_table();

        $('#fs_approval_form').bootstrapValidator({
            live            : 'enabled',
            message         : 'This value is not valid.',
            excluded        : [':disabled'],
            fields          : {
                status     			    : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_status_is_required'); ?>.'}}},
                Level                   : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_level_order_status_is_required'); ?>.'}}},
                hiddenPaysheetID    	: {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_document_approved_id_is_required'); ?>.'}}}
            }
        }).
        on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Employee/final_settlement_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#finalSettlementApprove_modal").modal('hide');
                        final_settlement_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function final_settlement_table(selectedID=null) {
        finalSet_tb = $('#final_settlement_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_finalSettlement_on_approval'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['masterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>');

                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>');
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>');

            },
            "aoColumns": [
                {"mData": "masterID"},
                {"mData": "documentCode"},
                {"mData": "employee"},
                {"mData": "narration"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [ {
                "targets": [0,5,6],
                "orderable": false
            } ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'approvedYN', 'value': $('#approvedYN').val()});
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

    function load_approvalView(id, level, isApproved){
        $("#finalSettlementApprove_modal").modal({backdrop: "static"});
        $('#fs_masterID').val(id);
        $('#level').val(level);

        $('.form_items').show();

        if(isApproved == 1){
            $('.form_items').hide();
        }

        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': id,
                'page_url': 'system/hrm/ajax/final-settlement-approval-view',
                'page_name': 'HRMS',
                'isFromApproval': 1
            },
            beforeSend: function () {
                startLoad();
            },
            success: function (page_html) {
                stopLoad();
                $('#ajax-container').html(page_html);
            },
            error: function (jqXHR, status, errorThrown) {
                stopLoad();
                myAlert('e', jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
            }
        });
    }

    function view_modal( docID ){
        documentPageView_modal('FS', docID)
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<div class="modal fade" id="finalSettlementApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_final_settlement_approval_title'); ?></span></h4>
            </div>
            <form class="form-horizontal" id="fs_approval_form">
                <div class="modal-body">

                    <div class="panel-body" id="ajax-container">

                    </div>
                    <hr>
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', $appDrop, '','class="form-control controlCls" id="status" required'); ?>
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="masterID" id="fs_masterID">
                        </div>
                    </div>
                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items"><?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="fn_item_moreDetail_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" id="more-det-body" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal_moreDetailTitle"></h4>
            </div>
            <div class="modal-body">
                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-1"> <!--Salary-->
                    <thead>
                    <tr>
                        <th>Period</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>No of Days</th>
                        <th>GL Description</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-1"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-7"> <!--SSO-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_period');?></th>
                        <th><?php echo $this->lang->line('common_employee_contribution');?></th>
                        <th><?php echo $this->lang->line('common_employer_contribution');?></th>
                        <th><?php echo $this->lang->line('common_expense_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_liability_gl_code');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-7"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-8"> <!--Loan-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_code');?></th>
                        <th><?php echo $this->lang->line('common_description');?></th>
                        <th><?php echo $this->lang->line('common_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_amount');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-8"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-12"> <!--SSO adjustment-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_employee_contribution');?></th>
                        <th><?php echo $this->lang->line('common_employer_contribution');?></th>
                        <th><?php echo $this->lang->line('common_expense_gl_code');?></th>
                        <th><?php echo $this->lang->line('common_liability_gl_code');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-12"></tbody>
                </table>

                <table class="<?php echo table_class() ?> more-det-tb" id="more-detail-tb-14"> <!--Leave Payment-->
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('common_annual_leave');?></th>
                        <th><?php echo $this->lang->line('common_leave_balance');?></th>
                        <th><?php echo $this->lang->line('common_no_of_working_days');?></th>
                        <th><?php echo $this->lang->line('common_basic_gross');?></th>
                        <th><?php echo $this->lang->line('common_amount');?></th>
                    </tr>
                    </thead>
                    <tbody id="more-detail-body-14"></tbody>
                </table>
                <div class="leave-pay-formula" style="margin-left: 50px; margin-top: 15px; color: red;">
                    <?php echo $this->lang->line('common_leave_pay_formula');?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<?php
