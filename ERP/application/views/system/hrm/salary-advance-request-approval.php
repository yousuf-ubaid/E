<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_salary_advance_request_approval');
echo head_page($title, false);
$status_arr = ['0'=>$this->lang->line('common_pending'),'1'=>$this->lang->line('common_approved')];

$appDrop = [
    ''=>$this->lang->line('common_please_select'),
    '1'=>$this->lang->line('common_approved'),
    '2'=>$this->lang->line('common_refer_back')
];
?>

<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }
</style>

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
        <?php echo form_dropdown('approvedYN', $status_arr, '','class="form-control" id="approvedYN" required onchange="sal_advance_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="sal_advance_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee_details');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_amount');?></th>
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
    var sal_advance_tb = '';
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/salary-advance-request-approval','','HRMS');
        });

        sal_advance_table();

        $('#sal_advance_form').bootstrapValidator({
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
                url :"<?php echo site_url('Employee/salary_advance_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#sal_advance_modal").modal('hide');
                        sal_advance_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function sal_advance_table(selectedID=null) {
        sal_advance_tb = $('#sal_advance_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_salary_advanceMasters_on_approval'); ?>",
            "aaSorting": [[1, 'desc']],
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
                {"mData": "request_amount_str"},
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
        $("#sal_advance_modal").modal({backdrop: "static"});
        $('#app_masterID').val(id);
        $('#level').val(level);

        $('.form_items').show();

        if(isApproved == 1){
            $('.form_items').hide();
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/load_salary_advance_request_view/view'); ?>",
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
        documentPageView_modal('SAR', docID)
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<div class="modal fade" id="sal_advance_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></span></h4>
            </div>
            <form class="form-horizontal" id="sal_advance_form">
                <div class="modal-body">

                    <div class="panel-body" id="ajax-container">

                    </div>
                    <hr class="form_items">
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', $appDrop, '','class="form-control controlCls" id="status" required'); ?>
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="masterID" id="app_masterID">
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
<?php
