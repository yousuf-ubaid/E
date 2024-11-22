<?php
/** Translation added  */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_travel_request_approval');
echo head_page($title, false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved'); ?> <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="travel_request_table_approval()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="travel_request_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_employee_name'); ?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('commom_trip'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Approved--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="travelApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('common_travel_Request_Approval'); ?><!--Travel Request Approval-->
            </div>
            <form class="form-horizontal" id="request_approval_form">
                <div class="modal-body">
                    <div class="panel-body" id="load_travelRequest">

                    </div>
                    <hr>
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select'), '1' => $this->lang->line('common_approved'), '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control controlCls" id="status" required'); ?><!-- /*Please Select*/-->
                            <!--'/*Approved*/'-->
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="hidden_travelRequestID" id="hidden_travelRequestID">
                            <input type="hidden" name="hidden_travelRequest_Code" id="hidden_travelRequest_Code">
                        </div>
                    </div>
                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items">
                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/travel/travel_request_approval', '', 'TRQ');
        });
        travel_request_table_approval();

        $('#request_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_travel_Request_status_required'); ?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_travel_Request_order_status_is_required'); ?>.'}}},/*Level Order Status is required*/
                hiddenPaysheetID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_travel_Request_id_is_required'); ?>.'}}}/*Travel Request ID is required*/
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
                url: "<?php echo site_url('Employee/travelRequestApproval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $("#travelApprove_modal").modal('hide');
                        travel_request_table_approval();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function travel_request_table_approval() {
        var Otable = $('#travel_request_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_TravelRequest_conformation'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "travelRequestID"},
                {"mData": "documentCode"},
                {"mData": "Ename2"},
                {"mData": "requestType"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}

            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :selected").val()});
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

    function load_travelRequestApproval(travelRequestlID, approvalLevel, RequestCode, appYN) {
       var id=travelRequestlID;
        $('.form_items').show();
        $('#request_approval_form')[0].reset();
        $('#request_approval_form').bootstrapValidator('resetForm', true);
        
        $('#hidden_travelRequestID').val(travelRequestlID);
        $('#hidden_travelRequest_Code').val(RequestCode);
        $('#level').val(approvalLevel);
        $('#status').val(1);
       
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/travel_request_approval') ?>',
            data: {'hidden_payrollID': travelRequestlID, 'from_approval': 'Y'},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                
                if (appYN == 1) {
                    $('.form_items').hide();
                   
                } else {
                    $('.form_items').show();
                   
                }
               
                $("#travelApprove_modal").modal({backdrop: "static"});
                $('#load_travelRequest').html(data);

                $('#paysheet-tb').tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 0,
                    right: 0,
                    'z-index': 0
                });
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
            }
        });
    }

</script>
