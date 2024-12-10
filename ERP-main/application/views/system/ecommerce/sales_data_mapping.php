<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    
    $this->lang->line($title, $primaryLanguage);
    echo head_page($title, false);

    $this->load->helper('erp_data_sync');

    $segment_arr = fetch_segment();
    $client_data_headers = getClientSalesHeaders();
    $client_data_headers = getClientSalesHeaders();
    $gl_codes = getChartofAccounts();
    $rebate_gl_code_arr = fetch_all_gl_codes_ecommerce();

    // print_r($rebate_gl_code_arr); exit;

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
       <button class="btn btn-success btn-block" data-toggle="modal" data-target="#sales_return_approval_modal">Create Mapping</button>
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('' => 'Select Here','0' => 'Active'/*'Pending'*/, '1' => 'Inactive'/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="clent_data_mapping" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Segment Code</th><!--Code-->
            <th style="min-width: 10%">GL Code</th><!--Code-->
            <th style="min-width: 10%">GL Description</th><!--Code-->
            <th style="min-width: 10%">Credit / Debit</th><!--Code-->
            <th style="min-width: 10%">Client Header</th><!--Code-->
            <th style="min-width: 10%">Client ERP Header</th><!--Code-->
            <th style="min-width: 10%">Description</th><!--Code-->
            <th style="min-width: 10%">Edit</th><!--Code-->
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

                                <div class="form-group">
                                    <!-- <label for="inputPassword3" class="col-sm-2 control-label">ERP Mapped Column</label>

                                    <div class="col-sm-4">
                                        <div class="col-sm-10">
                                            <?php echo form_dropdown('mapped_column', $client_data_headers, '', 'class="form-control" id="mapped_column" required'); ?>
                                            <input type="hidden" name="mapped_column_name" id="mapped_column_name" />
                                        </div>
                                    </div>

                                    <label for="inputPassword3" class="col-sm-2 control-label">ERP Mapped Column ID</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="erp_header_id" id="erp_header_id" />
                                    </div> -->
                                </div>


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
    //fetchPage('system/customer/erp_customer_master','Test','customer Master');
    $('.select2').select2();

    //manipulations
    $('#client_header').on('change',function(){

        var client_header_id = $('#client_header option:selected').val();
        var client_header_name = $('#client_header option:selected').text();

        $('#client_header_id').val(client_header_id);
        $('#client_header_name').val(client_header_name);

    });

    

    $('#mapped_column').on('change',function(){

        var mapped_column = $('#mapped_column option:selected').val();
        var mapped_column_name = $('#mapped_column option:selected').text();

        $('#erp_header_id').val(mapped_column);
        $('#mapped_column_name').val(mapped_column_name);
        
    });

                            
    //Calling functions
    sales_client_mapping_table();
    
    $('#sales_column_mapping_form').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
    }).on('success.form.bv', function (e) {

            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            // data.push({'name' : 'customerAutoID', 'value' : customerAutoID });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('DataSync/save_sales_mapping'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if(data['status']){
                            $('#sales_return_approval_modal').modal('toggle');
                            fetchPage('system/ecommerce/sales_data_mapping','Test','Sales Data Mapping');
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });


        function sales_client_mapping_table() {
            var Otable = $('#clent_data_mapping').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('DataSync/fetch_sales_mapping'); ?>",
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
                    {"mData": "ID"},
                    {"mData": "segmentCode"},
                    {"mData": "systemAccountCode"},
                    {"mData": "GLDescription"},
                    {"mData": "erp_c"},
                    {"mData": "client_sales_header"},
                    {"mData": "erp_column_name"},
                    {"mData": "description"},
                    {"mData": "delete"},
                    // {"mData": "edit"}
                ],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
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

        function delete_mapping(id){
            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_customer');?>",/*You want to delete this customer!*/
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
                    data: {'mapping_id': id},
                    url: "<?php echo site_url('DataSync/delete_mapping'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        sales_client_mapping_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
        }

        function edit_mapping(id){
           
            $.ajax({
                async: true,
                type: 'post',
                data: {'mapping_id': id},
                url: "<?php echo site_url('DataSync/edit_mapping_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    $('#sales_clent_data_edit_content').html(data);
                    $('#sales_clent_data_edit_modal').modal('toggle');
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
         

        }


        $('#sales_column_mapping_form_edit').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
        }).on('success.form.bv', function (e) {
           
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            // data.push({'name' : 'customerAutoID', 'value' : customerAutoID });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('DataSync/save_sales_mapping'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                       // HoldOn.close();
                        stopLoad();
                        refreshNotifications(true);
                        if(data['status']){
                            $('#sales_clent_data_edit_modal').modal('toggle');
                            //fetchPage('system/ecommerce/sales_data_mapping','Test','Sales Data Mapping');
                            sales_client_mapping_table();
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });



       



</script>