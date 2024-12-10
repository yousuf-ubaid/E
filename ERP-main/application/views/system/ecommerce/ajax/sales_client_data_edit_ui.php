<?php

    $primaryLanguage = getPrimaryLanguage();

    $this->load->helper('erp_data_sync');

    $segment_arr = fetch_segment();

    $base_segment_arr = array();
    foreach($segment_arr as $key => $value){
        $key_temp = trim(explode('|',$key)[0]);
        $base_segment_arr[$key_temp] = $value;
    }

    $client_data_headers = getClientSalesHeaders();
    $client_data_headers = getClientSalesHeaders();

    $gl_codes = getChartofAccounts();
    $rebate_gl_code_arr = fetch_all_gl_codes_ecommerce();

?>
 
               
 
    <input type="hidden" name="action" id="action" value="edit" />
    <input type="hidden" name="mapping_id" id="mapping_id" value="<?php echo $id ?>" />

                <div class="modal-body">
                   
                    <div class="col-sm-12">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                
                                <hr>

                                <div class="form-group">
                                    <div>
                                        <label for="inputEmail3" class="col-sm-2 control-label">Mapping Type</label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('mapping_type', array('' =>  'Please Select','1' =>'Vendor', '2' =>'Customer', '3' =>'3PL Vendor', '4' =>'3PL Customer','5' =>'Direct Income Receipt Voucher','6' =>'Journel Voucher'), $mapping_type, 'class="form-control" id="mapping_type_edit" required'); ?>
                                        </div>
                                    </div>

                                    <div id="customer_doc_area_edit" <?php echo ($mapping_type == 2) ? 'style="display:block"' : 'style="display:none"' ?> >
                                        <label for="inputPassword3" class="col-sm-2 control-label">Document Type</label><!--Comments-->

                                        <div class="col-sm-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_customer_edit" id="inlineRadio18" value="2" <?php echo ($invoice_type == 2) ? 'checked' : '' ?> >
                                                <label class="form-check-label pl-2" for="inlineRadio18">Customer Invoice</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_customer_edit" id="inlineRadio25" value="8" <?php echo ($invoice_type == 8) ? 'checked' : '' ?>>
                                                <label class="form-check-label pl-2" for="inlineRadio25">Credit Note</label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div id="vendor_doc_area_edit" <?php echo ($mapping_type == 1) ? 'style="display:block"' : 'style="display:none"' ?>>
                                        <label for="inputPassword3" class="col-sm-2 control-label">Document Type</label><!--Comments-->

                                        <div class="col-sm-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_vendor_edit" id="inlineRadio134" value="1" <?php echo ($invoice_type == 1) ? 'checked' : '' ?>>
                                                <label class="form-check-label pl-2" for="inlineRadio134">Supplier Invoice</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="doc_type_vendor_edit" id="inlineRadio23" value="9" <?php echo ($invoice_type == 9) ? 'checked' : '' ?>>
                                                <label class="form-check-label pl-2" for="inlineRadio23">Debit Note</label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>

                                <div class="form-group">
                                    <!-- <label for="inputEmail3" class="col-sm-2 control-label">Select Group</label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('group', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'Food'/*'Approved'*/, '2' =>'Taxi'/*'Referred-back'*/), $erp_group_id , 'class="form-control" id="status" required'); ?>
                                    </div> -->

                                    <label for="inputEmail3" class="col-sm-2 control-label">Select Segment</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('segment', $base_segment_arr, $erp_segment_id, 'class="form-control" id="status" required'); ?>
                                       
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Select Client Header</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('client_header', $client_data_headers, $client_sales_header_id, 'class="form-control" id="client_header_edit" required'); ?>
                                        <input type="hidden" name="client_header_name" id="client_header_name_edit" value="<?php echo $client_sales_header ?>" />
                                    </div>

                                    <label for="inputPassword3" class="col-sm-2 control-label">Client Header ID</label><!--Comments-->

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="client_header_id" id="client_header_id_edit" value="<?php echo $client_sales_header_id?>"/>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">GL Code</label><!--Status-->

                                    <div class="col-sm-10">
                                        <?php echo form_dropdown('gl_code', $rebate_gl_code_arr, trim($erp_gl_code) , 'class="form-control select2" id="gl_codes" required'); ?>
                                       
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Transaction Type (+/-)</label><!--Status-->

                                    <div class="col-sm-10">
                                        <?php echo form_dropdown('transaction_type', array('' =>  'Please Select'/*'Please Select'*/,'cr' =>'Credit Record'/*'Approved'*/,'dr' =>'Debit Record'), $erp_cr_dr, 'class="form-control" id="status" required'); ?>
                                    
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Description</label>

                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="description" id="description"><?php echo $erp_description ?></textarea>
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
                                    <button type="submit" class="btn btn-primary" id="btn_edit_mapping"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
        </div>


<script>
    $('#client_header_edit').on('change',function(){
       
        var client_header_id = $('#client_header_edit option:selected').val();
        var client_header_name = $('#client_header_edit option:selected').text();
    
        $('#client_header_id_edit').val(client_header_id);
        $('#client_header_name_edit').val(client_header_name);

    });

    ///////////////////////////////////////////////////////////////

    $('#mapping_type_edit').on('change',function(){
   
        var mapping_type = $('#mapping_type_edit').val();

        if(mapping_type == 1){
            //vendor
            $('#customer_doc_area_edit').css('display','none');
            $('#vendor_doc_area_edit').css('display','block');
        }else if(mapping_type == 2){
            //customer
            $('#customer_doc_area_edit').css('display','block');
            $('#vendor_doc_area_edit').css('display','none');
        }else{
            $('#customer_doc_area_edit').css('display','none');
            $('#vendor_doc_area_edit').css('display','none');
        }

    });

</script>