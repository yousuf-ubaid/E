<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('ap_automation');
    $this->load->library('sequence');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('sales_maraketing_transaction', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('New Payment', false, 'get_back()');

    $master_id = $data_arr;
    $date = current_date();
    $bank_currency_id = '';
    $fund_availability = 2;
    $transaction_currency_id = '';
    $doc_id = '';

    if($master_id){
        $posting_detials = get_automation_payment_master_by_id($master_id);
        $date = $posting_detials['date'];
        $dateFrom = date('Y-m-d',strtotime($posting_detials['selection_date_from']));
        $dateTo = date('Y-m-d',strtotime($posting_detials['selection_date_to']));
        $bank_currency_id = $posting_detials['bank_currency'];
        $transaction_currency_id = $posting_detials['transaction_currency_id'];
        $funding_availability =  $posting_detials['funding_availability'];
    }else{
        $doc_id = $this->sequence->sequence_generator('ASP/');
    }

    $date_formated = date('Y-m-d',strtotime($date));
    //Data streams
    $financeyear_arr = all_financeyear_drop(true);
    $currency_arr = all_currency_new_drop();
    $segment_arr = fetch_segment();
    $inv_currency = '';
    $bank_currency = '';
   
?>

<style>
    .table-responsive{
        width:100%;
    }

    .sorting_1{
        text-align:center;
    }

</style>

<div id="filter-panel" class="collapse filter-panel">
    
</div>


<form class="form-horizontal" id="ap_automation_payment_creation">
    <div class="row">
        <div class="col-md-12">

                <input type="hidden" name="master_id" id="master_id" value="<?php echo $master_id ?>" />

                <table class="<?php echo table_class() ?>">
                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Document ID</label>

                            <div class="col-sm-8">

                                <div class="col-sm-8">
                                    <?php if(!isset($posting_detials)) { ?>
                                        <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" />
                                    <?php } else { ?>
                                        <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $posting_detials['doc_id'] ?>" />
                                    <?php } ?>
                                </div>
                                
                            </div>
                        </td>

                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Date</label><!--Status-->

                            <div class="col-sm-8">
                                <input type="date" name="payment_date" id="payment_date" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo $date_formated ?>" id="date" class="input-small form-control">
                            </div>
                        
                        </td><!--Approved-->
                    </tr>

                    <tr>
                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Document Description</label><!--Status-->

                            <div class="form-group col-sm-8">
                                <textarea class="form-control" required rows="3" name="comments" id="comments"><?php echo isset($posting_detials['narration']) ? $posting_detials['narration'] : '' ?></textarea>
                            </div>
                        </td>

                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Financial Year</label><!--Status-->

                            <div class="col-sm-8">
                            <?php echo form_dropdown('financeyear',$financeyear_arr,  isset($posting_detials['financial_year']) ? $posting_detials['financial_year']: '', 'class="form-control" id="financeyear" onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Bank</label><!--Status-->

                            <div class="form-group col-sm-8">
                                <?php echo form_dropdown('PVbankCode', company_bank_account_drop(), isset($posting_detials['bank_gl']) ? $posting_detials['bank_gl']: '', 'class="form-control select2" id="PVbankCode" required'); ?>
                            </div>
                        
                        </td>

                        <td class="hide">
                            <label for="inputEmail3" class="col-sm-4 control-label">Segment</label><!--Status-->

                            <div class="col-sm-8">
                                <?php echo form_dropdown('segment',$segment_arr,  $this->common_data['company_data']['default_segment'], 'class="form-control" required id="segment" onchange=""'); ?>
                            </div>
                        </td>

                    </tr>


                    <tr>
                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Payment Mode</label><!--Status-->

                            <div class="col-sm-8">
                                <?php echo form_dropdown('paymentType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '1' => 'Cheque ', '2' =>'Bank Transfer'), isset($posting_detials['payment_mode']) ? $posting_detials['payment_mode']: '', 'class="form-control select2" id="paymentType" onchange="show_payment_method(this.value)"'); ?>
                            </div>
                        
                        </td>

                        <td id="area_check_num" class="hide">
                            <label for="inputEmail3" class="col-sm-4 control-label">Starting cheque number</label><!--Status-->

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" disabled/>
                            </div>
                        
                        </td>
                    </tr>

                
                    <tr>
                        
                    <tr>
                </table>
           
        </div>
        
    </div>

    <div class="row" style="background-color:#e9d9e3;padding:10px 0px">
        <div class="col-md-12">
            <div class="text-bold p2" style="margin-top:20px;">
                <span class="control-label" style="padding:0px 20px;"><u><i class="fa fa-user"></i> Selection Criteria</u></span>
            </div>
                
                <table class="<?php echo table_class() ?>">
                    <tr>
                        <td> 
                            <label for="all_bills" class="col-sm-4 control-label">All Bills</label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="selection_type" id="all_bills" value="1" <?php echo ( empty($posting_detials) || (isset($posting_detials['selection_type']) && $posting_detials['selection_type'] == 1)) ? 'checked':'' ?>>
                                </div>
                            </div>
                        
                        </td>

                        <td> 
                            <label for="over_due_radio" class="col-sm-4 control-label">Overdue Bills</label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="selection_type" id="over_due_radio" value="2" <?php echo (isset($posting_detials['selection_type']) && $posting_detials['selection_type'] == 2) ? 'checked':'' ?>>
                                </div>
                            </div>
                        </td>

                        <td> 
                            <label for="date_range" class="col-sm-4 control-label">Date Range Bills</label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="selection_type" id="date_range" value="3" <?php echo (isset($posting_detials['selection_type']) && $posting_detials['selection_type'] == 3) ? 'checked':'' ?>>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr id="selection_date" class=" <?php echo ( empty($posting_detials) || (isset($posting_detials['selection_type']) && $posting_detials['selection_type'] != 3)) ? 'hide':'' ?> pt-3">
                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Date From</label><!--Status-->

                            <div class="col-sm-8">
                            <input type="date" name="BillsDataRangeFrom" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="change_date(this,'from')" value="<?php echo isset($dateFrom) ? $dateFrom : '' ?>" id="dateFrom" class="input-small form-control">
                            </div>
                        </td>
                        <td>
                            <label for="inputEmail3" class="col-sm-4 control-label">Date To <br> </label><!--Status-->

                            <div class="col-sm-8">
                            <input type="date" name="BillsDataRangeTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="change_date(this,'to')" value="<?php echo isset($dateTo) ? $dateTo : '' ?>" id="dateTo" class="input-small form-control">
                            </div>
                        </td>


                    </tr>

                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Invoice Currency</label><!--Status-->

                            <div class="col-sm-8">
                                <?php echo form_dropdown('transactionCurrencyID', $currency_arr,$transaction_currency_id, 'class="form-control select2" onchange="change_invoice_currency(this)" id="transactionCurrencyID" required'); ?>
                            </div>
                        
                        </td>

                    </tr>
                </table>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="text-bold p2" style="margin-top:20px;">
                <span class="control-label" style="padding:0px 20px;"><u><i class="fa fa-user"></i> Funding Allocation</u></span>
            </div>
        
                
                <table class="<?php echo table_class() ?>">

                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Bank Currency</label><!--Status-->

                            <div class="col-sm-8">
                                <?php echo form_dropdown('bank_currency_id', $currency_arr,$transaction_currency_id, 'class="form-control select2" disabled onchange="" id="bank_currency_id"'); ?>
                                <?php // echo form_dropdown('bank_currency_id', $currency_arr,$bank_currency_id, 'class="form-control select2" disabled onchange="" id="bank_currency_id"'); ?>
                            </div>
                        
                        </td>

                    </tr>
                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Funding Availability</label><!--Status-->

                            <div class="col-sm-8">
                                <?php $disabled_drop = isset($posting_detials['funding_availability']) ? 'disabled': '' ?>
                                <?php echo form_dropdown('fund_availablity', array('1'=>'Open'),  isset($posting_detials['funding_availability']) ? $posting_detials['funding_availability']: '', "class='form-control' id='posting_type' {$disabled_drop}  " ); ?>
                            </div>
                        
                        </td>

                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Available Funds <span id="fund_currency"><?php echo $bank_currency ?></span></label><!--Status-->

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="available_funds" id="available_funds" value="<?php echo isset($posting_detials['fund_available']) ? $posting_detials['fund_available']: '' ?>" />
                            </div>
                        
                        </td>
                    </tr>
                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Allocation Mode</label><!--Status-->

                            <div class="col-sm-8">
                                <?php echo form_dropdown('fund_allocation_mode', array('1' =>'Manual','2' =>'AI - Auto'),  isset($posting_detials['posting_type']) ? $posting_detials['posting_type']: '', 'class="form-control" disabled id="posting_type"'); ?>
                            </div>
                        
                        </td>
                
                    </tr>
                    <tr>

                    
                    </tr>
                </table>

        </div>
        
    </div>

    <div class="col-sm-12 text-right" style="padding-top:25px;">
        <button type="button" class="btn btn-primary" id="fetchSupplier" onclick="add_more_supplier()"><i class="fa fa-plus"></i> Add Supplier</button>
        <button type="submit" class="btn btn-primary" id="fetchbtn"> <i class="fa fa-user"></i> Fetch Vendors</button>
    </div>


</form>

<hr>


<div class="table-responsive">
    <div class="row">
        <ul class="nav nav-tabs pull-left">
            <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                    Vendor Details</a>
            </li>
        
            <li class="">
                <a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false" style="display:none">
                    Bank Details</a>
            </li>
        
        </ul>
    </div>
    
    <div class="row">
        <div class="tab-content">
            <div id="tab_1" class="tab-pane active">
                    <div id="filter-panel" class="collapse filter-panel"></div>
                        <table id="vendor_allocation" class="<?php echo table_class() ?>">
                            <thead>
                            <tr>
                                <th class="pull " style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Vendor Code</th><!--Code-->
                                <th style="min-width: 10%">Name</th><!--Code-->
                                <!-- <th style="min-width: 10%">Local Balance Due <span id="inv_currency"><?php echo $inv_currency ?></span></th>Code -->
                                <th style="min-width: 10%">Bank Balance Due <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
                                <th style="min-width: 10%">Schedule for PMT <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
                                <th style="min-width: 10%">Allocation <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
                                <th style="min-width: 10%">Action</th><!--Code-->
                                <!-- <th style="min-width: 10%">Action</th>Code -->
                                <th style="min-width: 10%">Voucher Number</th><!--Code-->
                            
                            </tr>
                            </thead>
                        </table>
                        
                    <div id="total_allocations_field"></div>

                    
            </div><!-- /.tab-pane -->

            <div id="tab_2" class="tab-pane">

                    

            </div><!-- /.tab-pane -->
        </div>
    </div>
</div>


<div id="btnConfirmArea" class="col-sm-12 text-right <?php if(isset($posting_detials['confirmedYN']) && $posting_detials['confirmedYN'] == 1) { echo 'hide'; } ?>" style="padding-top:25px;">
    <button type="button" class="btn btn-primary btn-md pull-left" id="btn_save_draft_doc"><i class="fa fa-doc"></i> Save as Draft</button>
    <button type="button" class="btn btn-primary btn-md" id="btn_confirm_doc"><i class="fa fa-check"></i>Confirm</button>
   
</div>

<div id="btnApprovedArea" class="col-sm-12 hide <?php if(empty($posting_detials) ||(!isset($posting_detials['status'])) ||(isset($posting_detials['status']) && $posting_detials['status'] != 1)) { echo 'hide'; } ?>" style="padding-top:25px;">
        <div class="row">
            <div class="text-bold p2" style="padding-bottom:25px;">
                <span class="control-label"><u><i class="fa fa-user"></i> Approvels</u></span>
            </div>
        </div>
        <div class="row text-center">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Status </label>
                <div class="col-sm-8">
                    <select name="status" class="form-control" id="status" required="" data-bv-field="status">
                        <option value="" selected="selected">Please select</option>
                        <option value="1">Approved</option>
                        <option value="2">Refer Back</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row text-center">
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"> Comment</label>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

</div>


<!-- Modals -->
<div class="modal fade" id="modify_allocation_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
                <button class="btn btn-primary" onclick="add_new_invoice()"><i class="fa fa-plus"></i> Add New Invoice</button>
            </div>
            <form class="form-horizontal" id="invoice_list_payment_form">
                <div class="modal-body">
                <input type="hidden" name="invoice_id" id="invoice_id" value="" />
                <table id="vendor_invoice_wise" class="<?php echo table_class() ?>">
                    <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Document Number</th><!--Code-->
                            <th style="min-width: 10%">Invoice Number</th><!--Code-->
                            <th style="min-width: 10%">Ref No</th><!--Code-->
                            <th style="min-width: 10%">Invoice Date</th><!--Code-->
                            <th style="min-width: 10%">Invoice Due Date</th><!--Code-->
                            <th style="min-width: 15%">Local Currency Amount</th><!--Code-->
                            <th style="min-width: 15%">Bank Currency Amount</th><!--Code-->
                            <th style="min-width: 15%">Allocation Amount</th><!--Code-->
                            <th style="min-width: 10%">Allocation Status</th><!--Code-->
                            <th style="min-width: 15%">Action</th><!--Code-->
                            <th style="min-width: 5%">Delete
                                <button type="button" class="btn btn-danger" id="delete_all_invoices" onclick="delete_all_invoices_fn(this)" ><i class="fa fa-trash"></i></button>
                            </th><!--Code-->
                        </tr>
                    </thead>
                </table>

                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modify_allocation_individual" tabindex="2" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Update Invoice allocated amount';?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">
                    
                <table class="<?php echo table_class() ?>">
                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">
                              
                                Invoice Amount
                                
                            </label>

                          
    
                            <div class="col-sm-8">
                                <span class="text-bold"><span id="edit_invoice_amount"></span></span> &nbsp
                                <button type="button" class="btn btn-sm btn-success" id="btn_copy_value"><i class="fa fa-arrow-down"></i></button>
                            </div>
                                
                        </td>
                    </tr>

                    <tr>
                        <td> 
                            <label for="inputEmail3" class="col-sm-4 control-label">Allocated Amount</label>
                            <input type="hidden" name="edit_allocation_id" id="edit_allocation_id" value="" />
                            <div class="col-sm-8">
                                <div>
                                    <span class="col-sm-4"><input type="text" class="form-control" name="edit_allocation_amount" id="edit_allocation_amount" value="" /> </span>                         
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-success btn-md" onclick="update_allocation()"><i class="fa fa-check"></i>Update</button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="col-sm-12 hide">
                                <label for="inputEmail3" class="col-sm-4 control-label">Remaining Funds : </label>
                                <div class="col-sm-8">
                                    <span class="text-bold">USD <span id="remaing_in_modal"></span></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                    
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_more_supplier_model" tabindex="2" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Supplier';?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="add_supplier_form">
                <div class="modal-body">
                    
                <table class="<?php echo table_class() ?>" id="add_supplier_tbl">
                    <thead>
                        <th></th>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Action</th>
                    </thead>
                    <tbody>

                        

                    </tbody>
                </table>
                
                </div>
                
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_more_supplier_invoice" tabindex="2" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Supplier Invoice';?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="add_supplier_invoice_form">
                <div class="modal-body">

                <input type="hidden" name="selected_invoice" id="selected_invoice" />
                    
                <table class="<?php echo table_class() ?>" id="add_supplier_invoice_tbl">
                    <thead>
                        <th></th>
                        <th>Invoice Code</th>
                        <th>Booking Date</th>
                        <th>Ref No</th>
                        <th>Transaction Amount</th>
                        <th></th>
                    </thead>
                    <tbody>

                        

                    </tbody>
                </table>
                
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="add_more_invoice_btn" ><i class="fa fa-plus"></i> Add </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>




<script type="text/javascript">

    $('.select2').select2();
    fetch_vendor_allocations();
    
    function get_back(){
        fetchPage('system/ap_automation/index.php','','Payment Voucher','');
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_select_financial_period');?>'));/*Select Financial Period*/
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_cheque_number(GLAutoID) {
        if (!jQuery.isEmptyObject(GLAutoID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'GLAutoID': GLAutoID,'PvID':p_id},
                url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
                success: function (data) {
                   
                }
            });
        }else{
            $('.paymentType').addClass('hide');
            $('.banktrans').addClass('hide');
        }

    }

    

    ////////////////////////////////////////////////////////////

    $('input[type=radio][name=selection_type]').change(function() {
        if(this.value == 3){
            $('#selection_date').removeClass('hide');
        }else{
            $('#selection_date').addClass('hide');
        }
    });

    ///////////////////////////////////////////////////////////////

   
    $('#ap_automation_payment_creation').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                PVbankCode: {validators: {notEmpty: {message: 'Bank code is required'}}}
            },
    }).on('success.form.bv', function (e) {

        e.preventDefault();

        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name':'fund_availablity', 'value':$('#posting_type').val()});

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "This will reset the fetched data, Are you need to continue.<?php //echo $this->lang->line('config_you_want_to_delete_this_customer');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {

                $.ajax({
                    async: true,
                    type: 'post',
                // dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Ap_automation/get_vendor_bills'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data_temp) {
                        stopLoad();
                        refreshNotifications(true);
                        data = data_temp.replaceAll('+','');
                        data = JSON.parse(data);


                        $('#master_id').val(data);
                        $('#fetchbtn').prop('disabled',false);
                        fetch_vendor_invoice_allocation(data);
                        fetch_vendor_allocations();
                        fetch_total_allocations();
                    
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        });
    
    });


    ////////////////////////////////////////////////////////////////
    var OtableVendor = '';
    function fetch_vendor_allocations(){
        
        var OtableVendor = $('#vendor_allocation').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "iDisplayLength": 100,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Ap_automation/fetch_vendor_allocation'); ?>",
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
                    {"mData": "vendor_code"},
                    {"mData": "vendor_name"},
                    //  {"mData": "local_balance_due"},
                    {"mData": "balance_due"},
                    {"mData": "schedule_pmt"},
                    {"mData": "allocation"},
                    {"mData": "modify"},
                    //  {"mData": "view"},
                    {"mData": "voucher"}

                ],
                "columnDefs": [{"searchable": false, "targets": [0,3,4,5]},{"type": "string-case", "targets": [1,2]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "master_id","value": $("#master_id").val()});
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

        //update total allocations
        fetch_total_allocations();
    }

    ////////////////////////////////////////////////////////////////
    var Otable = '';
    function fetch_vendor_invoice_allocation(id){ 
        
        Otable = $('#vendor_invoice_wise').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "iDisplayLength": 25,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Ap_automation/fetch_vendor_invoice_wise'); ?>",
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
                    {"mData": "bookingInvCode"},
                    {"mData": "supplierInvoiceNo"},
                    {"mData": "RefNo"},
                    {"mData": "invoiceDate"},
                    {"mData": "invoiceDueDate"},
                    {"mData": "current_amount"},
                    {"mData": "bank_amount_due"},
                    {"mData": "allocation_amount"},
                    {"mData": "status"},
                    {"mData": "action"},
                    {"mData": "delete"},
                    // {"mData": "view"},
                    // {"mData": "voucher_number"}

                ],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "payment_id","value": id});
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

    ////////////////////////////////////////////////////////////////

    function fetch_total_allocations(){
        
        var id = $('#master_id').val();

        if(id){
            $.ajax({
                async: true,
                type: 'post',
               // dataType: 'json',
                data: {'master_id': id},
                url: "<?php echo site_url('Ap_automation/fetch_total_allocations'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    $('#total_allocations_field').empty().html(data);
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
       

    }

    ///////////////////////////////////////////////////////////////

    function modify_allocations(id){
        
        $('#modify_allocation_modal').modal('toggle');

        $('#invoice_id').val(id);
        $('#selected_invoice').val(id);

        fetch_vendor_invoice_allocation(id);
        
    }

    /////////////////////////////////////////////////////////////////

    function modify_invoice_allocation(id){

        $('#edit_allocation_id').val(id);
        var remaings = $('#total_allocation_reamin').text();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'allocation_id': id},
                url: "<?php echo site_url('Ap_automation/get_invoice_allocation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data){

                        var bank_currency  = (data.bank_currency).split('|');
                        $('#edit_invoice_amount').empty().html(bank_currency[1]+' '+data.bank_amount_due)
                        $('#edit_allocation_amount').val(data.allocation_amount);
                        $('#remaing_in_modal').empty().html(remaings);
                        $('#modify_allocation_individual').modal('toggle');

                    }
                    
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    //////////////////////////////////////////////////////////////////

    function update_allocation(){
        
        var edit_allocation_id = $('#edit_allocation_id').val();
        var edit_allocation_amount = $('#edit_allocation_amount').val();
        var invoice_id =  $('#invoice_id').val();

        $.ajax({
                async: true,
                type: 'post',
               // dataType: 'json',
                data: {'allocation_id': edit_allocation_id,'allocation_amount':edit_allocation_amount},
                url: "<?php echo site_url('Ap_automation/set_invoice_allocation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    $('#modify_allocation_individual').modal('toggle');
                    set_modal_focus();
                    fetch_vendor_invoice_allocation(invoice_id);
                    fetch_vendor_allocations();
                    fetch_total_allocations();
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    /////////////////////////////////////////////////

    $('#btn_confirm_doc').on('click', function(){
            
            var master_id = $('#master_id').val();

            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You will confirm this payment plan for the generating of payment vouchers.<?php //echo $this->lang->line('config_you_want_to_delete_this_customer');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    // dataType: 'json',
                    data: {'master_id': master_id},
                    url: "<?php echo site_url('Ap_automation/confirm_payment_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data){
                          // $('#btnConfirmArea').addClass('hide');
                           // $('#btnApprovedArea').removeClass('hide');
                        }
                        fetch_vendor_allocations();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });


        });

    ////////////////////////////////////////////////////////////

    $('#btn_save_draft_doc').on('click', function(){ 
        myAlert('s','Successfully save the Draft');
    });

    /////////////////////////////////////////////////////////////

    $('#paymentType').on('change',function(){
        
        var paymentType = $('#paymentType').val();

        if(paymentType == 1){
            $('#area_check_num').removeClass('hide');
        }else{
            $('#area_check_num').addClass('hide');
        }

    });

    /////////////////////////////////////////////////////////////

    $('#PVbankCode').on('change',function(){
        var PVbankCode = $('#PVbankCode').val();
        var p_id = 1;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'GLAutoID': PVbankCode,'PvID':p_id},
            url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
            success: function (data) {
                var cheque_number = 0;
                if(data){
                    var cheque_number  = data.master.bankCheckNumber;
                }
                $('#cheque_number').val(cheque_number)
            
            }
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'GLAutoID': PVbankCode,'PvID':p_id},
            url: "<?php echo site_url('Chart_of_acconts/load_chart_of_accont_header'); ?>",
            success: function (data) {
            
                $.each($("#fund_currency"), function() {
                    $(this).html('('+data.bankCurrencyCode+')');
                });
                //    $('#fund_currency').empty().html('('+data.bankCurrencyCode+')');
               // $("#bank_currency_id").select2().val(data.bankCurrencyID).trigger("change");
              //  $("#bank_currency_id").prop('disabled',true);
            
            }
        });
   
    })

    /////////////////////////////////////////////////////

        $('#transactionCurrencyID').on('change',function(){
            var curency_text = $('#transactionCurrencyID :selected').text().split('|');

            $.each($("#inv_currency"), function() {
                 $(this).html(curency_text[0]);
            });
        })

    ///////////////////////////////////////////////////////////

        $('#btn_copy_value').on('click',function(){
            var edit_invoice_amount = $('#edit_invoice_amount').text().split(' ');
            $('#edit_allocation_amount').val(edit_invoice_amount[1]);
        })

    /////////////////////////////////////////////////////////

    function set_modal_focus(){
        $('body').on('hidden.bs.modal', function () {
            if($('.modal.in').length > 0)
            {
                $('body').addClass('modal-open');
            }
        });
    }

    function change_invoice_currency(ev){

        var val = $(ev).val();
        $('#bank_currency_id').val(val).change();

    }

    function delete_pulled_invoice(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure you want to remove the document from the list.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    // dataType: 'json',
                    data: {'id': id},
                    url: "<?php echo site_url('Ap_automation/remove_added_invoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data){
                            Otable.draw();
                        }
                        fetch_vendor_allocations();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });


    }

    function delete_vendor_wise_allocations(id){

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure you want to remove the document from the list.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    // dataType: 'json',
                    data: {'id': id},
                    url: "<?php echo site_url('Ap_automation/remove_complete_vendor_payment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                       
                        fetch_vendor_allocations();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });


    }

    function add_more_supplier(){
        var master_id = $('#master_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'master_id': master_id},
            url: "<?php echo site_url('Ap_automation/get_more_suppliers_add'); ?>",
            success: function (data) {
                $('#add_supplier_tbl').DataTable().clear().destroy();
                $('#add_supplier_tbl tbody').empty();
                var table = $('#add_supplier_tbl tbody');
            
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, val) {
                        table.append('<tr><td>'+key+'</td><td>'+val.supplierSystemCode+'</td><td>'+val.supplierName+'</td><td><input type="checkbox" onchange="add_supplier_to_list('+val.supplierAutoID+',this)" ?></td></tr>');
                    });
                  
                }
                	
                $('#add_supplier_tbl').DataTable({});
               
            
            }
        });

        $('#add_more_supplier_model').modal('toggle');
    }

    function add_supplier_to_list(supplierID,ev){

        var master_id = $('#master_id').val();

        $.ajax({
            async: true,
            type: 'post',
            data: {'supplierID': supplierID,'master_id':master_id},
            url: "<?php echo site_url('Ap_automation/add_supplier_to_payment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
                refreshNotifications(true);
                
                $(ev).prop('disabled',true);
                fetch_vendor_allocations();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function add_new_invoice(){

        var payment_id = $('#selected_invoice').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payment_id': payment_id},
            url: "<?php echo site_url('Ap_automation/get_more_invoice_for_supplier'); ?>",
            success: function (data) {
                $('#add_supplier_invoice_tbl').DataTable().clear().destroy();
                $('#add_supplier_invoice_tbl tbody').empty();
                var table = $('#add_supplier_invoice_tbl tbody');
            
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, val) {
                        if(val.type == 'debitnote'){
                            table.append('<tr><td>'+key+'</td><td>'+val.bookingInvCode+'</td><td>'+val.bookingDate+'</td><td>'+val.RefNo+'</td><td class="text-right"><span class="text-bold">'+val.transactionCurrency+'</span> '+(parseFloat(val.transactionAmount, 3).toFixed(2))+'</td><td><input type="checkbox" name="invoice_debitnote[]" value="'+val.InvoiceAutoID+'" ?></td></tr>');
                        }else{
                            table.append('<tr><td>'+key+'</td><td>'+val.bookingInvCode+'</td><td>'+val.bookingDate+'</td><td>'+val.RefNo+'</td><td class="text-right"><span class="text-bold">'+val.transactionCurrency+'</span> '+(parseFloat(val.transactionAmount, 3).toFixed(2))+'</td><td><input type="checkbox" name="invoice[]" value="'+val.InvoiceAutoID+'" ?></td></tr>');
                        }
                        
                    });
                  
                }
                
                $('#add_more_invoice_btn').prop('disabled',false);
                $('#add_supplier_invoice_tbl').DataTable({});
               
            
            }
        });


        $('#add_more_supplier_invoice').modal('toggle');
    }

    // function add_supplier_invoice_to_list(invoiceAutoID,ev){

    //     var master_id = $('#master_id').val();
    //     var selected_supplier = $('#selected_invoice').val();

    //     $.ajax({
    //         async: true,
    //         type: 'post',
    //         data: {'invoiceAutoID': invoiceAutoID,'master_id':master_id,'selected_supplier':selected_supplier},
    //         url: "<?php echo site_url('Ap_automation/add_supplier_invoice_payment'); ?>",
    //         beforeSend: function () {
    //             startLoad();
    //         },
    //         success: function (data) {

    //             stopLoad();
    //             refreshNotifications(true);
                
    //             // $(ev).prop('read',true);
    //             // fetch_vendor_allocations();

    //         }, error: function () {
    //             swal("Cancelled", "Your file is safe :)", "error");
    //         }
    //     });

    // }

    $('#add_supplier_invoice_form').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                PVbankCode: {validators: {notEmpty: {message: 'Bank code is required'}}}
            },
    }).on('success.form.bv', function (e) {

        e.preventDefault();

        var selected_invoice = $('#selected_invoice').val();

        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name':'fund_availablity', 'value':$('#posting_type').val()});
        data.push({'name':'master_id', 'value':$('#master_id').val()});
        
        $.ajax({
                async: true,
                type: 'post',
               // dataType: 'json',
                data: data,
                url: "<?php echo site_url('Ap_automation/set_vendor_additionl_bill'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_temp) {
                    stopLoad();
                    refreshNotifications(true);
                    $('#add_more_supplier_invoice').modal('toggle');
                    fetch_vendor_invoice_allocation(selected_invoice);
                    fetch_vendor_allocations();
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    
    });


    function delete_all_invoices_fn(ev){

        var data = $('#invoice_list_payment_form').serializeArray();
        data.push({'name':'master_id', 'value':$('#master_id').val()});

        var selected_invoice = $('#selected_invoice').val();

        $.ajax({
                async: true,
                type: 'post',
               // dataType: 'json',
                data: data,
                url: "<?php echo site_url('Ap_automation/delete_all_invoice'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_temp) {
                    stopLoad();
                    refreshNotifications(true);

                    if(data_temp){
                        fetch_vendor_invoice_allocation(selected_invoice);
                        fetch_vendor_allocations();
                    }
                    
                  
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    function change_date(ev,type){

        var date = $(ev).val();
        var payment_date = $('#payment_date').val();

        if(payment_date < date){
            myAlert('e','Date can not be greater than Payment Date.');
            $(ev).val(payment_date).change();
        }

    }

    function delete_master_record(){

        var master_id = $('#master_id').val();
        
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure you want to remove the document from the list.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    // dataType: 'json',
                    data: {'master_id': master_id},
                    url: "<?php echo site_url('Ap_automation/payment_delete_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        
                        fetch_vendor_allocations();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        });

    }

</script>
