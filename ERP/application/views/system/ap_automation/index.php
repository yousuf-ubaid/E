<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $title = 'Ap Automation Placement';
    $this->lang->line($title, $primaryLanguage);
    echo head_page($title, false);

?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Active </td><!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> Inactive </td><!-- Not Approved-->
            </tr>
        </table>
        
    </div>
    <div class="col-md-4 text-center pull-right">
       <button class="btn btn-success btn-block" onclick="create_placement()">Create Placement</button>
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('' => 'Select Here','0' => 'Active'/*'Pending'*/, '1' => 'Inactive'/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="payment_batch_master" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Document Number</th><!--Code-->
            <th style="min-width: 10%">Date</th><!--Code-->
            <th style="min-width: 10%">Narration</th><!--Code-->
            <th style="min-width: 10%">Confirmed</th><!--Code-->
            <th style="min-width: 6%">Action</th><!--Code-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>



<div class="modal fade" id="sales_return_approval_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Create GL Mapping'?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="sales_column_mapping_form">
                <div class="modal-body">
                   
                    <div class="col-sm-12">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                
                                <hr>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Invoice Type</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('invoice_type', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Direct Income'/*'Approved'*/, '2' =>'Direct Item'/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Select Group</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('group', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Food'/*'Approved'*/, '2' =>'Taxi'/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                    </div>

                                    <label for="inputEmail3" class="col-sm-2 control-label">Select Segment</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('segment', $segment_arr, '', 'class="form-control" id="status" required'); ?>
                                       
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Select Client Header</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('client_header', $client_data_headers, '', 'class="form-control" id="client_header" required'); ?>
                                        <input type="hidden" name="client_header_name" id="client_header_name" value=""/>
                                    </div>

                                    <label for="inputPassword3" class="col-sm-2 control-label">Client Header ID</label><!--Comments-->

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="client_header_id" id="client_header_id" />
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">GL Code</label><!--Status-->

                                    <div class="col-sm-10">
                                        <?php echo form_dropdown('gl_code', $rebate_gl_code_arr, '', 'class="form-control select2" id="gl_codes" required'); ?>
                                       
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Transaction Type (+/-)</label><!--Status-->

                                    <div class="col-sm-10">
                                        <?php echo form_dropdown('transaction_type', array('' =>  'Please Select'/*'Please Select'*/,'cr' =>'Credit Record'/*'Approved'*/,'dr' =>'Debit Record'), '', 'class="form-control" id="status" required'); ?>
                                    
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Description</label><!--Comments-->

                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                                    </div>
                                </div>

                                <hr>


                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="sales_clent_data_edit_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Mapping';?></h4>
            </div>
            
            <form class="form-horizontal" id="sales_column_mapping_form_edit">
                <div id="sales_clent_data_edit_content"> </div>
            <form>
                                        
        </div>
    </div>
</div>


<!-- Scripting area -->
<script type="text/javascript">
    fetch_batch_master();

    function create_placement(){
        fetchPage('system/ap_automation/add_new_placement','Test','Add new placement');
    }

    ///////////////////////////////////

    function fetch_batch_master(){

        var Otable = $('#payment_batch_master').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "iDisplayLength": 25,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Ap_automation/fetch_payment_master'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "id"},
                {"mData": "doc_id"},
                {"mData": "date"},
                {"mData": "narration"},
                {"mData": "confirm"},
                {"mData": "edit"},
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    /////////////////////////////////////////

    function edit_master_record(id){
        fetchPage('system/ap_automation/add_new_placement','Test','Add new placement','',id);
    }

    ////////////////////////////////////

    function delete_master_record(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure you want to completely remove the records.<?php //echo $this->lang->line('config_you_want_to_delete_this_customer');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {

            if(id){
                $.ajax({
                    async: true,
                    type: 'post',
                    data: {'master_id': id},
                    url: "<?php echo site_url('Ap_automation/delete_all_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        fetch_batch_master();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        });

    }

</script>
