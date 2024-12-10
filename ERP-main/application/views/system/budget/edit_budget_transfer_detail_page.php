<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Budget Transfer Detail';
echo head_page($title, false);

/*echo head_page('Budget', false);*/
$segment_arr = fetch_segment(true);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th style="font-size: 13px;"><strong>Budget Transfer Number</strong></th>
                <td><strong>: </strong></td>
                <td id="number">&nbsp;</td>
            </tr>
            <tr>
                <th style="font-size: 13px;"><strong>Budget Transfer Date</strong></th>
                <td><strong>: </strong></td>
                <td id="cdate">&nbsp;</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th style="font-size: 13px;"><strong>Financial Year</strong></th>
                <td><strong>: </strong></td>
                <td id="finacyr">&nbsp;</td>
            </tr>
            <tr>
                <th style="font-size: 13px;"><strong>Narration</strong></th>
                <td><strong>: </strong></td>
                <td>
                    <textarea class="form-control" rows="3" name="comment" id="commentedit" onchange="edit_transfer_comment()" ></textarea>
                    <!--<input type="text" id="commentedit" onchange="edit_transfer_comment()" name="comment" class="form-control">-->
                </td>
            </tr>
        </table>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="modal_budget_transfer_detail();"><i
                class="fa fa-plus"></i> Add Detail
        </button>
    </div>
</div>


<input type="hidden" id="budgetTransferAutoID" value="<?php echo json_encode(trim($this->input->post('page_id'))); ?>">
<div class="table-responsive">
    <table id="budget_transfer_detail_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="3">Transfer From</th>
            <th colspan="2">Transfer To</th>
            <th colspan="2">&nbsp;</th>
        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 8%">Segment</th>
            <th style="min-width: 8%">GlCode</th>
            <th style="min-width: 8%">Segment</th>
            <th style="min-width: 8%">GlCode</th>
            <th style="min-width: 8%">Transfer Amount (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</th>
            <th style="min-width: 5%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<br>
<button type="button" class="btn btn-success pull-right" onclick="confirm_budget_transfer();"> Confirm</button>
<button type="button" class="btn btn-primary pull-right" style="margin-right:5px;" onclick="sav_as_draft();"> Save As Draft</button>

<div class="modal fade" id="budget_transfer_detail_modal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Budget Transfer Detail</h4></div>
            <?php echo form_open('', 'role="form" id="budget_transfer_detail_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 animated zoomIn">
                        <fieldset class="scheduler-border" style="">
                            <legend class="scheduler-border"> From</legend>
                            <div class="col-sm-12">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('common_gl_code'); ?>  </label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('FromGLAutoID', fetch_all_gl_codes('PLE'), '', 'class="form-control select2" onchange="get_budget_amount()"  id="FromGLAutoID"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Segment</label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('fromSegmentID', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" onchange="get_budget_amount()" id="fromSegmentID" '); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Budget Amount</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="budgetAmount" name="budgetAmount" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Consumption Amount</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="consumptionAmount" name="consumptionAmount" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Balance Amount</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="balanceAmount" name="balanceAmount" readonly>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6 animated zoomIn">
                        <fieldset class="scheduler-border" style="">
                            <legend class="scheduler-border"> To</legend>
                            <div class="col-sm-12" style="">
                                <div class="form-group " style="">
                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('common_gl_code'); ?>  </label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('toGLAutoID', fetch_all_gl_codes('PLE'), '', 'class="form-control select2" id="toGLAutoID"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Segment</label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('toSegmentID', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="toSegmentID"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 5px;">
                                <div class="form-group" style="">
                                    <label class="col-sm-5 control-label">Transfer Amount</label>
                                    <div class="col-sm-7">
                                        <input type="number" step="any" class="form-control" onkeyup="validateadjestmentamount()" id="adjustmentAmount" name="adjustmentAmount">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var budgetTransferAutoID
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/finance/budget_transfer_management','','Budget Transfer');
        });
         budgetTransferAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        get_budget_transfer_detail();
        get_budget_transfer_master();
        $('.select2').select2();


        $('#budget_transfer_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                adjustmentAmount: {validators: {notEmpty: {message: 'Transfer Amount is required.'}}}

            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'budgetTransferAutoID', 'value': budgetTransferAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Budget_transfer/save_budget_transfer_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if(data[0]=='s'){
                        $('#budget_transfer_detail_modal').modal('hide');
                        get_budget_transfer_detail();
                    }else{
                        $('#adjustmentAmount').val('');
                    }

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });


    });

    function get_budget_transfer_detail(selectedID=null) {
        Otable = $('#budget_transfer_detail_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "searching": false,
            "paging": false,
            "info": false,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Budget_transfer/fetch_budget_transfer_detail'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [

            ],
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
                    if (parseInt(oSettings.aoData[x]._aData['budgetTransferDetailAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "budgetTransferDetailAutoID"},
                {"mData": "fsegment"},
                {"mData": "fGLC"},
                {"mData": "tsegment"},
                {"mData": "tGLC"},
                {"mData": "total_value"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "budgetTransferAutoID", "value": budgetTransferAutoID });
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

    function modal_budget_transfer_detail() {
        $('#FromGLAutoID').val('').change();
        $('#fromSegmentID').val('').change();
        $('#budgetAmount').val('');
        $('#consumptionAmount').val('');
        $('#balanceAmount').val('');
        $('#toGLAutoID').val('').change();
        $('#toSegmentID').val('').change();
        $('#adjustmentAmount').val('');
        $('#budget_transfer_detail_modal').modal({backdrop: "static"});
    }

    function get_budget_amount(){
        var FromGLAutoID=$('#FromGLAutoID').val();
        var fromSegmentID=$('#fromSegmentID').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'FromGLAutoID': FromGLAutoID,'fromSegmentID': fromSegmentID,'budgetTransferAutoID': budgetTransferAutoID},
            url: "<?php echo site_url('Budget_transfer/get_budget_amount'); ?>",
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if(data[0]=='s'){
                        $('#budgetAmount').val(data[2]);
                        $('#consumptionAmount').val(data[3]);
                        var bal=0;
                        if(!jQuery.isEmptyObject(data[2])){
                            bal=(parseFloat(data[2])-parseFloat(data[3]));
                        }
                        $('#balanceAmount').val(bal);
                    }else{
                        $('#budgetAmount').val('');
                        $('#consumptionAmount').val('');
                        $('#balanceAmount').val('');
                        $('#FromGLAutoID').val('').change();
                        $('#fromSegmentID').val('').change();
                        //myAlert(data[0],data[1])
                        swal("Cancelled", data[1], "error");
                    }
                }else{
                    $('#budgetAmount').val('');
                    $('#consumptionAmount').val('');
                    $('#balanceAmount').val('');
                }
                $('#adjustmentAmount').val('');
            }, error: function () {
                //swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function validateadjestmentamount(){
     var adjustmentAmount= parseFloat($('#adjustmentAmount').val());
     var budgetAmount=  parseFloat($('#budgetAmount').val());
     var balanceAmount=  parseFloat($('#balanceAmount').val());

         var frmGL=$('#FromGLAutoID').val();
         var frmseg=$('#fromSegmentID').val();
         var toseg=$('#toSegmentID').val();
         var toGL=$('#toGLAutoID').val();
        if(frmGL==toGL && frmseg==toseg){
            myAlert('w','From Details and To Details Canot be same');
            $('#FromGLAutoID').val('').change();
            $('#fromSegmentID').val('').change();
            $('#budgetAmount').val('');
            $('#toGLAutoID').val('').change();
            $('#toSegmentID').val('').change();
            $('#adjustmentAmount').val('');
            $('#balanceAmount').val();
            $('#consumptionAmount').val();
        }

        /*if(adjustmentAmount>=0){
            myAlert('w','Transfer Amount should be entered in minus value');
            $('#adjustmentAmount').val('');
            return false;
        }*/
        if(balanceAmount < adjustmentAmount){
            myAlert('w','Transfer Amount canot be greater than balance amount');
            $('#adjustmentAmount').val('');
        }
    }

    function confirm_budget_transfer(){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'budgetTransferAutoID': budgetTransferAutoID},
            url: "<?php echo site_url('Budget_transfer/confirm_budget_transfer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if(data[0]=='s'){
                    fetchPage('system/finance/budget_transfer_management','','Budget Transfer');
                }


            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_budget_transfer_detail(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'budgetTransferDetailAutoID': id},
                    url: "<?php echo site_url('Budget_transfer/delete_transfer_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0],data[1]);
                        if(data[0]=='s'){
                            get_budget_transfer_detail();
                        }
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function sav_as_draft(){
        myAlert('s','Saved as Draft');
        fetchPage('system/finance/budget_transfer_management','','Budget Transfer');
    }

    function get_budget_transfer_master(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'budgetTransferAutoID': budgetTransferAutoID},
            url: "<?php echo site_url('Budget_transfer/get_budget_master'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var finacneyr=data['beginingDate']+' - '+data['endingDate'];
                $('#number').html(data['documentSystemCode']);
                $('#cdate').html(data['createdDate']);
                $('#finacyr').html(finacneyr);
                $('#commentedit').val(data['comments']);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function edit_transfer_comment(){
        var commentedit= $('#commentedit').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'budgetTransferAutoID': budgetTransferAutoID,'comment': commentedit},
            url: "<?php echo site_url('Budget_transfer/edit_transfer_comment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }



</script>