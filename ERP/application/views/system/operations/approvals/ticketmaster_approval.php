<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('transaction_approval_grv_approval');
echo head_page($title, false);


/*echo head_page('GRV Approval', false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?>
                </td><!-- Approved-->
                <td><span class="label label-danger">&nbsp;</span><?php echo $this->lang->line('common_not_approved');?>
                </td><!--Not Approved-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="ticket_table_approval" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%">Job No</th>
            <th style="min-width: 10%">Job Created Date</th>
            <th style="min-width: 15%">Description</th>
            <th style="min-width: 15%">Location</th>
            <th style="min-width: 15%">Well No</th>
            <th style="min-width: 15%">Total</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?></th><!--Level-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="ticket_approval_modal" tabindex="-1" role="dialog" aria-labelledby="grv_modal_lbl">
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ticket Approval</h4>
            </div>
            <form id="ticket_approval_form">
            <div class="modal-body">
                <div class="row">
                    <div id="conform_body" style="padding: 5px;"></div>
                    <hr>
                </div>
                <div class="row">
                    <div class="form-horizontal">
                        <div class="form-group ">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => 'Approved', '2' => 'Reject'), '', 'class="form-control" id="status" required'); ?>
                                <input type="hidden" name="Level" id="Level">
                                <input type="hidden" name="ticketidAtuto" id="ticketidAtuto">
                                <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?> </label><!--Comments-->

                            <div class="col-sm-8">
                                    <textarea class="form-control" rows="3" name="comments"
                                              id="comments"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                </div>
            </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var Otable;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operations/approvals/ticketmaster_approval', 'Test', 'Ticket Approval');
        });

        ticket_table_approval();
        $('#ticket_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                ticketidAtuto: {validators: {notEmpty: {message: 'Ticket ID.'}}},
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_grv_document_id_is_required');?>.'}}}/*Document Approved ID is required*/
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
                url: "<?php echo site_url('Operation/save_ticket_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data == true) {
                        $("#ticket_approval_modal").modal('hide');
                        Otable.draw();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function ticket_table_approval() {
        Otable = $('#ticket_table_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/fetch_ticket_approval'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "ticketidAtuto"},
                {"mData": "ticketNo"},
                {"mData": "createdDate"},
                {"mData": "comments"},
                {"mData": "fieldName"},
                {"mData": "wellNo"},
                {"mData": "total_prod_servc"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"searchable": false, "targets": [0,6,7,8,9]}],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function approveJob(ticketidAtuto, documentApprovedID, Level) {

        if (ticketidAtuto) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'ticketidAtuto': ticketidAtuto, 'html': true,'approval':1},
                url: "<?php echo site_url('Operation/load_ticket_master_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#ticketidAtuto').val(ticketidAtuto);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#ticket_approval_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    //expenseClaim_attachment_View_modal('EC',expenseClaimMasterAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }

    }
</script>