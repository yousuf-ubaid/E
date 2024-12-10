<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Data Mappings', false);

    $this->load->helper('erp_data_sync');

    $segment_arr = fetch_segment();
    $client_data_headers = getClientSalesHeaders();
    $client_data_headers = getClientSalesHeaders();
    $gl_codes = getChartofAccounts();
    $rebate_gl_code_arr = fetch_all_gl_codes_ecommerce();
    $get_posting_data = array();
    $get_service_types = service_type_get(1);

    $posting_id = $data_arr;

    if($posting_id){
        $get_posting_data = fetch_all_posting_data($posting_id);
    }

?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
     
    </div>
</div>
<hr>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="sales_column_posting">

            <input type="hidden" name="posting_id" id="posting_id" value="<?php echo $posting_id ?>" />

            <table class="<?php echo table_class() ?>">
                <tr>
                    
                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Select Group</label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('group', array('' =>  'Please Select'/*'Please Select'*/,'Food' =>'Food'/*'Approved'*/, 'Taxi' =>'Taxi'/*'Referred-back'*/), isset($get_posting_data['group']) ? $get_posting_data['group'] : '', 'class="form-control" id="status" required'); ?>
                        </div>
                    </td><!--Approved-->

                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Invoice Type</label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('invoice_type', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Direct Income'/*'Approved'*/, '2' =>'Direct Item'/*'Referred-back'*/),isset($get_posting_data['invoice_type']) ? $get_posting_data['invoice_type'] : '', 'class="form-control" id="status" required'); ?>
                        </div>
                    </td><!--Approved-->

                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Posting method</label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('posting_method', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Group by Date Range'/*'Approved'*/, '2' =>'Each Document'/*'Referred-back'*/), isset($get_posting_data['posting_method']) ? $get_posting_data['posting_method'] : '', 'class="form-control" id="posting_method" required'); ?>
                        </div>
                    </td><!--Approved-->
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Posting Type</label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('posting_type', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Manual'/*'Approved'*/, '2' =>'Auto'/*'Referred-back'*/),  isset($get_posting_data['posting_type']) ? $get_posting_data['posting_type']: '', 'class="form-control" id="status" required'); ?>
                        </div>
                    </td><!--Approved-->
                   
                </tr>
                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Status <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('posting_status', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Active Posting'/*'Approved'*/, '2' =>'Inactive Posting'/*'Referred-back'*/), isset($get_posting_data['status']) ? $get_posting_data['status']: '', 'class="form-control" id="posting_status" required'); ?>
                        </div>
                    </td><!--Approved-->
                            
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Service Type <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('service_type',$get_service_types, isset($get_posting_data['service_type']) ? $get_posting_data['service_type']: '', 'class="form-control" id="service_type" required'); ?>
                        </div>
                    </td><!--array('' =>  'Please Select'/*'Please Select'*/,'1' =>'TMDONE'/*'Approved'*/, '2' =>'MARKET PLACE','3'=>'PICKUP','4'=>'RECOVERY')-->

                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Mode of Collection <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('mode_collection', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'All','2' =>'Cash', '3'=> 'Card'), isset($get_posting_data['mode_collection']) ? $get_posting_data['mode_collection']: '', 'class="form-control" id="mode_collection" required'); ?>
                        </div>
                    </td><!--Approved-->
                  
                </tr>
            
                <tr style="margin-top:20px;">
                   
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label"><br> </label><!--Status-->
                        <button class="btn btn-success" type="submit" id="create_posting_btn"> <i class="fa fa-plus"></i> &nbsp Create Posting </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    
</div>
<hr>


<div class="table-responsive" id="mapping_table" style="display:none">
    <button class="btn btn-success" data-toggle="modal" data-target="#sales_return_approval_modal" onclick="add_mapping_btn()"><i class="fa fa-plus"></i></button>
    <table id="clent_data_mapping" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Client Header</th><!--Code-->
            <th style="min-width: 10%">Segment Code</th><!--Code-->
            <th style="min-width: 10%">GL Code</th><!--Code-->
            <th style="min-width: 10%">GL Description</th><!--Code-->
            <th style="min-width: 10%">Transaction Type (+/-)</th><!--Code-->
            <th style="min-width: 10%">Mapping Type</th><!--Code-->
            <th style="min-width: 10%">Control Account</th><!--Code-->
            <th style="min-width: 10%">Edit</th><!--Code-->
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="sales_return_approval_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Create Column Mapping'?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="sales_column_mapping_form">
                <div class="modal-body">
                   
                    <div class="col-sm-12">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                
                                <hr>

                                <div class="form-group">
                                    <div>
                                        <label for="inputEmail3" class="col-sm-2 control-label">Mapping Type</label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('mapping_type', array('' =>  'Please Select','1' =>'Vendor', '2' =>'Customer', '3' =>'3PL Vendor', '4' =>'3PL Customer','5' =>'Direct Income Receipt Voucher','6' =>'Journel Voucher'), '', 'class="form-control" id="mapping_type" required'); ?>
                                        </div>
                                    </div>

                                    <div id="customer_doc_area" style="display:none">
                                        <label for="inputPassword3" class="col-sm-2 control-label">Document Type</label><!--Comments-->

                                        <div class="col-sm-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_customer" id="inlineRadio18" value="2" checked>
                                                <label class="form-check-label pl-2" for="inlineRadio18">Customer Invoice</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_customer" id="inlineRadio25" value="8" >
                                                <label class="form-check-label pl-2" for="inlineRadio25">Credit Note</label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div id="vendor_doc_area" style="display:none">
                                        <label for="inputPassword3" class="col-sm-2 control-label">Document Type</label><!--Comments-->

                                        <div class="col-sm-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_vendor" id="inlineRadio134" value="1" checked>
                                                <label class="form-check-label pl-2" for="inlineRadio134">Supplier Invoice</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_vendor" id="inlineRadio23" value="9" >
                                                <label class="form-check-label pl-2" for="inlineRadio23">Debit Note</label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>

                                <div class="form-group">
                                    <!-- <label for="inputEmail3" class="col-sm-2 control-label">Select Group</label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('group', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Food'/*'Approved'*/, '2' =>'Taxi'/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                    </div> -->

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

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Control Account</label><!--Comments-->

                                    <div class="col-sm-10">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="control_acc" id="inlineRadio1" value="1">
                                            <label class="form-check-label pl-2" for="inlineRadio1">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="control_acc" id="inlineRadio2" value="0" checked>
                                            <label class="form-check-label pl-2" for="inlineRadio2">No</label>
                                        </div>
                                    </div>
                                </div>

                                

                                <hr>

                                <!-- <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">ERP Mapped Column</label>

                                    <div class="col-sm-4">
                                        <div class="col-sm-10">
                                            <?php echo form_dropdown('mapped_column', $client_data_headers, '', 'class="form-control" id="mapped_column" required'); ?>
                                            <input type="hidden" name="mapped_column_name" id="mapped_column_name" />
                                        </div>
                                    </div>

                                    <label for="inputPassword3" class="col-sm-2 control-label">ERP Mapped Column ID</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="erp_header_id" id="erp_header_id" />
                                    </div>
                                </div> -->


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
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Mapping'?></h4><!--Invoice Approval-->
            </div>

            <form class="form-horizontal" id="sales_column_mapping_form_edit">
                <div id="sales_clent_data_edit_content"> </div>
            </form>
                                        
        </div>
    </div>
</div>


<script type="text/javascript">

    sales_client_mapping_table();
    var posting_id = $('#posting_id').val();

    if(posting_id > 0){
        $('#clent_data_mapping').css('display','block');
        $("#sales_column_posting select").prop("disabled", true);
        $('#mapping_table').css('display','block');
        $('#create_posting_btn').css('visibility','hidden');
        $('#posting_method').prop('disabled',false);
        // sales_client_mapping_table();
    }

//////////////////////////////////////////////////////

    
    function add_mapping_btn(){
       $('#sales_column_mapping_form').trigger('reset');
    }

//////////////////////////////////////////////////////

    function sales_client_mapping_table() {
        $('.select2').select2();
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
                {"mData": "client_sales_header"},
                {"mData": "segmentCode"},
                {"mData": "systemAccountCode"},
                {"mData": "GLDescription"},
                {"mData": "erp_c"},
                {"mData": "mapping_type"},
                {"mData": "control_acc"},
                {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({ "name": "posting_id","value": $("#posting_id").val()});
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

//////////////////////////////////////////////////////////////////////

    
    $('#sales_column_posting').bootstrapValidator({
            
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
          
            // data.push({'posting' : 'customerAutoID', 'value' : customerAutoID });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('DataSync/save_data_mapping_posting'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);

                        if(data.id){
                            $('#posting_id').val(data.id);
                            $('#clent_data_mapping').css('display','block');
                            $("#sales_column_posting select").prop("disabled", true);
                            $('#mapping_table').css('display','block');
                            sales_client_mapping_table()
                        }
                       
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

    });

//////////////////////////////////////////////////////////////////////

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
            data.push({'name':'posting_id', 'value':$('#posting_id').val() });
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
                            sales_client_mapping_table();
                            // fetchPage('system/ecommerce/sales_data_mapping','Test','Sales Data Mapping');
                        }
                    },
                    error: function () {
                        // alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });


//////////////////////////////////////////////////////////////////////////////

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

        function edit_mapping(id){
           
           $.ajax({
               async: true,
               type: 'post',
               data: {'mapping_id': id,'edit':'new'},
               url: "<?php echo site_url('DataSync/edit_mapping_view'); ?>",
               beforeSend: function () {
                   startLoad();
               },
               success: function (data) {
                   stopLoad();
                   refreshNotifications(true);
                   $('#sales_clent_data_edit_content').html(data);
                   $('.select2').select2();
                   $('#sales_clent_data_edit_modal').modal('toggle');
               }, error: function () {
                   stopLoad();
                   swal("Cancelled", "Your file is safe :)", "error");
               }
           });
        

       }

/////////////////////////////////////////////////////////////////

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

    //////////////////////////////////////////////////

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

    //////////////////////////////////////////////////////////////////////

    $('#posting_method').change(function(){

            var posting_id = $('#posting_id').val();
            var posting_method = $('#posting_method').val();

            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure to change this posting method",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mapping_id': posting_id,'posting_method': posting_method},
                    url: "<?php echo site_url('DataSync/change_posting_method'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    })

    /////////////////////////////////////////////////////////////////////

    $('#mapping_type').change(function(){

        var mapping_type = $('#mapping_type').val();

        if(mapping_type == 1){
            //vendor
            $('#customer_doc_area').css('display','none');
            $('#vendor_doc_area').css('display','block');
        }else if(mapping_type == 2){
            //customer
            $('#customer_doc_area').css('display','block');
            $('#vendor_doc_area').css('display','none');
        }else{
            $('#customer_doc_area').css('display','none');
            $('#vendor_doc_area').css('display','none');
        }

    });
    

</script>