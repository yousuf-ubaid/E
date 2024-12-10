<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_quotation_Contract_sales_order');
echo head_page($title, true);
$current_date = current_format_date();
/*echo head_page('Quotation / Contract / Sales Order',true);*/
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$financeyear_arr = all_financeyear_drop(true);
$SalesPerson = all_sales_person_drop();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?></label><br><!--Date-->
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?></label><!--From-->
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw();" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw();" value="" id="IncidateDateTo"
                       class="input-small">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="customerCode"><?php echo $this->lang->line('common_customer_name');?> </label><br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw();" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-2">
            <label for="contractType"><?php echo $this->lang->line('sales_markating_transaction_document_type');?> </label><br><!--Document type-->
            <?php echo form_dropdown('contractType[]', array('Quotation' => $this->lang->line('sales_markating_transaction_quotation')/*'Quotation'*/,'Contract' => $this->lang->line('sales_markating_transaction_contract')/*'Contract'*/,'Sales Order' => $this->lang->line('sales_markating_transaction_sales_order')/*'Sales Order'*/), '', 'class="form-control" id="contractType" onchange="Otable.draw();" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?></label><br><!--Status-->
            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' =>$this->lang->line('common_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back','5'=>'Closed'), '', 'class="form-control" id="status" onchange="Otable.draw();"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?> </td><!--Confirmed / Approved-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?> / <?php echo $this->lang->line('common_not_approved');?></td><!--Not Confirmed / Not Approved-->
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?></td><!--Refer-back-->
                <td><span class="label label-info">&nbsp;</span> <?php echo $this->lang->line('common_closed');?></td><!--Closed-->
            </tr>
        </table>
    </div>
    <div class="col-md-3 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/quotation_contract/erp_quotation_contract',null,'Add New Quotation or Contract','CNT');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create');?> </button><!--Create-->
    </div>
</div><hr>
<div class="table-responsive">
    <table id="quotation_contract_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 13%"> <?php echo $this->lang->line('common_code');?></th><!--Code-->
                <th style="min-width: 40%"> <?php echo $this->lang->line('common_details');?></th><!--Details-->
                <th style="min-width: 7%"><?php echo $this->lang->line('common_type');?></th><!--Type-->
                <th style="min-width: 13%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="document_version_View" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="document_version_ViewTitle"><?php echo $this->lang->line('sales_markating_transaction_model_title');?></h4><!--Modal title-->
                <input type="hidden" name="contractAutoID" id="contractAutoID">
            </div>
            <div class="modal-body" id="loaddocument_version_View">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" onclick="quotation_version()" role="button"><?php echo $this->lang->line('sales_markating_transaction_quotation_version');?> </a><!--Quotation Version-->
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_closed');?> </button><!--Close-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="drill_down_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="document_version_ViewTitle"><?php echo $this->lang->line('sales_markating_transaction_drill_down');?></h4><!--Drill Down-->
            </div>
            <div class="modal-body" >
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></td><!--Invoice code-->
                            <td><?php echo $this->lang->line('sales_markating_transaction_invoice_date');?></td><!--Invoice Date-->
                            <td><?php echo $this->lang->line('common_customer_name');?></td><!--Customer Name-->
                            <td><?php echo $this->lang->line('common_total');?></td><!--Total-->
                            <td><?php echo $this->lang->line('common_action');?></td> <!--Action-->
                        </tr>
                    </thead>
                    <tbody id="drill_down_table">
                        <tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr><!--No Records Founds-->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('sales_markating_transaction_close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <form method="post" id="Send_Email_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="contractid" id="email_contractid" value="">
                    <h4 class="modal-title" id="EmailHeader">Email</h4>
                </div>
                <div class="modal-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1"  data-toggle="tab" aria-expanded="false">Send Email</a></li>
                            <li class=""><a href="#tab_2" onclick="load_mail_history()"  data-toggle="tab" aria-expanded="true">History</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="emailContent">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
                                        </div>
                                    </div>
                                    <div class="append_data_nw">
                                        <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                                            <div class="col-sm-1">
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="email" name="email" id="email" class="form-control"
                                                       placeholder="example@example.com" style="margin-left: -10px">
                                            </div>
                                            <div class="col-sm-1 remove-tdnw">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="SendQuotationMail()">Send Email</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                            <div class="tab-pane " id="tab_2">
                                <div class="modal-body">
                                    <table id="mailhistory" class="<?php echo table_class() ?>">
                                        <thead>
                                        <tr>
                                            <th style="">#</th>
                                            <th style="">documentID</th>
                                            <th style="">Sent By</th>
                                            <th style="">Sent to Email</th>
                                            <th style="">Sent Date time</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing
                    <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i></button>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delivery_order_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="deliveryorder_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Delivery Order/ Invoice</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="dotype" name="dotype" value="">
                    <input type="hidden" id="contractID" name="contractID">
                    <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="customerID" name="customerID">
                    <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">


                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Type <?php required_mark(); ?></label>
                            <?php echo form_dropdown('type', array('' => 'Select Type', '1' => 'Delivery Order', '2' => 'Invoice'), '', 'class="form-control select2" id="type"  srequired'); ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Delivery Order/ Invoice Date<?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="DOdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="DOdate"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                            <input type="text" name="referenceno" id="referenceno" class="form-control">
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('sales_markating_transaction_sales_person');?> </label><!--Sales Person-->
                            <?php echo form_dropdown('salesperson', $SalesPerson, '', 'class="form-control select2" id="salesperson"'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if($financeyearperiodYN==1){
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        <?php } ?>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_warehouse');?><!-- WareHouse --> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('warehouseAutoIDtemp', all_delivery_location_drop_active(), '', ' id="warehouseAutoIDtemp" class="form-control select2 wareHouseAutoID"'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                            <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('sales_markating_transaction_document_persons_telephone_number');?> </label><!--Person's Telephone Number-->

                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                            <textarea class="form-control" rows="3" name="narration" id="narration"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Generate</button>
                    <button class="btn btn-success submitWizard" type="button" onclick="confirm()">Generate and Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>




<div class="modal fade" id="ViewGeneratedPaymentApplications_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form method="post" id="generated_payment_application_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #696CFF;font-weight: 600;">Generated Payment Application List</h4>
                </div>
                <div class="modal-body">                    
                    <input type="hidden" id="contractID" name="contractID">
                    <input type="hidden" id="customerID" name="customerID">
                    <input type="hidden" id="autoID" name="autoID">

                    <div class="row">
                        <div class="col-sm-12 col-md-7 text-left pb-20">
                        <table class="table table-bordered table-striped table-condensed table-row-select">
                            <tbody>
                                <tr>
                                <td><span class="label label-success">&nbsp;</span> Confirmed / Approved </td><!--Confirmed / Approved-->
                                <td><span class="label label-danger">&nbsp;</span> Not Confirmed / Not Approved</td><!--Not Confirmed / Not Approved-->
                                </tr>
                            </tbody>
                        </table>   
                        </div>
                        <div class="col-sm-12 col-md-5 text-right pb-10">
                            <a class="btn btn-primary-new size-sm" onclick="open_payment_application_modal()"><i class="fa fa-plus"></i> Generate New</a>   
                        </div>
                    </div>    
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <table class="table table-hover" id="generated_payment_application_list">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Document ID</th>
                                        <th scope="col">Confirmed</th>
                                        <th scope="col">Confirmed By</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">                    
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="payment_application_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #696CFF;font-weight: 600;">Change Quantity</h4>
                </div>
                <div class="modal-body">                    
                    <input type="hidden" id="contractID" name="contractID">
                    <input type="hidden" id="PAAutoID" name="PAAutoID">
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <table class="table table-hover" id="records_table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Item</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Cumulative Qty</th>
                                        <th scope="col">Previous Qty</th>
                                        <th scope="col">Current Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">               
                    <button type="submit" class="btn btn-success-new size-sm">Confirm</button>     
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="payment_application_details_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form method="post" id="payment_application_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #696CFF;font-weight: 600;">View Payment Application Details</h4>
                </div>
                <div class="modal-body">                    
                    <input type="hidden" id="contractAutoID" name="contractAutoID">
                    <input type="hidden" id="PAAutoID" name="PAAutoID">
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <table class="table table-hover" id="records_table_details">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Item</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Cumulative Qty</th>
                                        <th scope="col">Previous Qty</th>
                                        <th scope="col">Current Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">  
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="payment_application_details_modal_edit" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form method="post" id="payment_application_form_edit">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #696CFF;font-weight: 600;">Edit Payment Application Details</h4>
                </div>
                <div class="modal-body">                    
                    <input type="hidden" id="contractAutoID" name="contractAutoID">
                    <input type="hidden" id="PAAutoID" name="PAAutoID">
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <table class="table table-hover" id="records_table_details_edit">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Item</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Document Qty</th>
                                        <th scope="col">Cumulative Qty</th>
                                        <th scope="col">Previous Qty</th>
                                        <th scope="col">Current Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">  
                    <button type="submit" class="btn btn-success-new size-sm">Confirm</button>
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="contract_close_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Close Quotation / Contract</h4>
            </div>
            <form class="form-horizontal" id="contract_close_form">
                <div class="modal-body">
                    <div id="contract_close"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_date'); ?><!--Date--></label>
                        <div class="col-sm-4">
                            <div class="input-group datepickerClose">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="closedDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="closedDate" class="form-control" required>
                            </div>
                            <!--<div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="closedDate" value="<?php /*echo date('Y-m-d'); */?>"
                                       id="closedDate" class="form-control" required>
                            </div>-->
                            <input type="hidden" name="contractAutoID" id="contractAutoID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary">Close Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="closed_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="closed_user_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt>Document code</dt>
                    <dd id="c_document_code">...</dd>
                    <dt>Document Date</dt>
                    <dd id="c_document_date">...</dd>
                    <dt>Confirmed Date</dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="6">Approved Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th>Approved Date</th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="approved_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="6" class="text-center">
                            <?php echo $this->lang->line('footer_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
                <br><br>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">Closed Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th>Closed Date</th>
                        <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody id="closed_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="4" class="text-center">Document not closed yet.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="insufficient_QUT_item_modal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Insufficient Items</h4>
            </div>

            <form class="form-horizontal" id="insufficient_form">
                <div class="modal-body">
                    <div id="insufficient_item">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Current Stock</th>
                            </tr>
                            </thead>
                            <tbody id="insufficient_item_body">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="paymentcollection_drilldown">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Approved Details </h4>
            </div>
            <?php echo form_open('', 'role="form" id="quotaion_status"'); ?>
            <div class="modal-body">
                <input type="hidden" id="contractAutoIDpv" name="contractAutoID">
                <div class="row" style="margin-top: 10px;" id="statusvoucher">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('statuspv', array('1'=>'Draft','2'=>'Send to Customer','3'=>'Approved','4'=>'Rejected'), '', 'class="form-control select2" id="statuspv" onchange="update_documentStatus(this)" required'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="employeename">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Approved By</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control" id="colectedbyemp" name="colectedbyemp">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="collectiondate">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Approved Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                      <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="collectiondatepv"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="collectiondatepv" class="form-control">
                        </div>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="comment">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                 <textarea class="form-control" rows="3" id="commentpv" name="commentpv"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="commentonhold">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                 <textarea class="form-control" rows="3" id="commentpvonhold" name="commentpvonhold"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" onclick="updatepvstatus()"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
var contractAutoID;
var Otable;
$(document).ready(function() {



    $('.headerclose').click(function(){
        fetchPage('system/quotation_contract/quotation_contract_management','','Quotation or Contracts');
    });
    contractAutoID = null;
    number_validation();
    quotation_contract_table();

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepickerClose').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
    });

    $('#customerCode').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#contractType').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    Inputmask().mask(document.querySelectorAll("input"));

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        $('#deliveryorder_form').bootstrapValidator('revalidateField', 'DOdate');
    });
    FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
    periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
    fetch_finance_year_period(FinanceYearID, periodID);



function quotation_contract_table(selectedID=null){
    Otable = $('#quotation_contract_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_Quotation_contract'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {

                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                if( parseInt(oSettings.aoData[x]._aData['contractAutoID']) == selectedRowID ){
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');

                }
                x++;
            }
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');

        },
        "aoColumns": [
            {"mData": "contractAutoID"},
            {"mData": "contractCode"},
            {"mData": "detail"},
            {"mData": "contractType"},
            {"mData": "total_value"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"},
            {"mData": "contractNarration"},
            {"mData": "customerMasterName"},
            {"mData": "contractDate"},
            {"mData": "contractExpDate"},
            {"mData": "contractType"},
            {"mData": "isDeleted"},
            {"mData": "detTransactionAmount"},
            {"mData": "referenceNo"}
        ],
        "columnDefs": [{"targets": [7], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10,11,12,13,14,15] },{"visible":true,"searchable": false,"targets": [0,2,4,5,7] }],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
            aoData.push({"name": "status", "value": $("#status").val()});
            aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
            aoData.push({"name": "contractType", "value": $("#contractType").val()});
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
    $('#deliveryorder_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            DOdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}},/*Date is required*/
            type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}}/* Type is required */
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
        data.push({'name': 'contractAutoID', 'value': $('#contractID').val()});
        data.push({'name': 'confirm', 'value': 0});
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to create this document!",/*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/save_deliveryorder_from_quotation_contract_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0],data[1]);
                        if(data[0]=='s'){
                            $("#segment").val('');
                            $("#customerID").val('');
                            $("#contractID").val('');
                            $("#transactionCurrencyID").val('');
                            $("#referenceno").val('');
                            $('#narration').val('');
                            $('#salesperson').val('').change();
                            $('#contactPersonName').val('');
                            $('#contactPersonNumber').val('');

                            $("#delivery_order_modal").modal('hide');
                            stopLoad();

                            //confirmDeliveryOrder(data[2])
                        }else  if(!$.isEmptyObject(data['itemInsufficient'])){
                            $("#segment").val('');
                            $("#customerID").val('');
                            $("#contractID").val('');
                            $("#transactionCurrencyID").val('');
                            $("#referenceno").val('');
                            $('#narration').val('');
                            $('#salesperson').val('').change();
                            $('#contactPersonName').val('');
                            $('#contactPersonNumber').val('');

                            $('#insufficient_item_body').html('');
                            $.each(data['itemInsufficient'], function (item, value) {
                                $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['currentStock'] + '</td></tr>')
                            });

                            $("#delivery_order_modal").modal('hide');
                            $("#insufficient_QUT_item_modal").modal({backdrop: "static"});
                            stopLoad();
                        } else {
                            stopLoad();
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    });


    $('#contract_close_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            // contractAutoID: {validators: {notEmpty: {message: 'contractAutoID is required.'}}},
            comments: {validators: {notEmpty: {message: 'comments is required.'}}},
            closedDate: {validators: {notEmpty: {message: 'Date is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'contractAutoID', 'value': $('#contractAutoID').val()});
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to close this document!",/*You want to close this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/close_contract'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        $("#contract_close_modal").modal('hide');
                        stopLoad();
                        Otable.draw();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    });
});
$('.table-row-select tbody').on('click', 'tr', function () {
    $('.table-row-select tr').removeClass('dataTable_selectedTr');
    $(this).toggleClass('dataTable_selectedTr');
});

function referback_customer_contract(id,code,isSystemGenerated){
    if(isSystemGenerated!=1)
    {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'contractAutoID': id,'code':code},
                    url: "<?php echo site_url('Quotation_contract/referback_Quotation_contract'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    },
                    error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            })
    }
    else
    {
        swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
    };
}
function issystemgenerateddoc() {
    swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
}
function delete_item(id,value){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'contractAutoID':id},
                url :"<?php echo site_url('Quotation_contract/delete_con_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    Otable.draw();
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });        
}

    function referback_customer_invoice(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'contractAutoID':id},
                    url :"<?php echo site_url('Quotation_contract/referback_customer_invoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function quotation_version(){
        $('#document_version_View').modal('hide');
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_reversing_this_quotation');?> ",/*You want to reverse this version*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'contractAutoID':$('#contractAutoID').val()},
                    url :"<?php echo site_url('Quotation_contract/quotation_version'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data['type'], data['message'], 1000);
                        if (data['status']) {
                            Otable.draw();
                        }   
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function document_version_View_modal(documentID, para1){
        title = "Quotation";
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'contractAutoID': para1,'html': true},
            url: "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#document_version_ViewTitle').html(title);
                $('#loaddocument_version_View').html(data);
                $('#document_version_View').modal('show');
                $("#contractAutoID").val(para1);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function document_drill_down_View_modal(documentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': documentID},
            url: "<?php echo site_url('Quotation_contract/document_drill_down_View_modal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#drill_down_table').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                 $('#drill_down_table').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                }
                else {
                    $.each(data, function (key, value) {
                        $('#drill_down_table').append('<tr><td>' + x + '</td><td><a target="_blank" onclick="documentPageView_modal(\'CINV\',' + value['invoiceAutoID'] + ')" >' + value['invoiceCode'] + '</a></td><td>' + value['invoiceDueDate'] + '</td><td>' + value['customerName'] + '</td><td class="pull-right">' + parseFloat(value['contractAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td ><a class="pull-right" target="_blank" onclick="documentPageView_modal(\'CINV\',' + value['invoiceAutoID'] + ')" ><span class="glyphicon glyphicon-eye-open"></span></a></td></tr>');
                            x++;
                    });
                    //<td class="text-right"><a onclick="edit_addon_cost_model(' + value['id'] + ')"><span class="glyphicon glyphicon-pencil" style="color:blue;"></span></a></td>
                }
                $('#drill_down_modal').modal('show');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

function reOpen_contract(id){
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'contractAutoID':id},
                url :"<?php echo site_url('Quotation_contract/re_open_contract'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    Otable.draw();
                    stopLoad();
                    refreshNotifications(true);
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function clear_all_filters(){
    $('#IncidateDateFrom').val("");
    $('#IncidateDateTo').val("");
    $('#status').val("all");
    $('#customerCode').multiselect2('deselectAll', false);
    $('#customerCode').multiselect2('updateButtonText');
    $('#contractType').multiselect2('deselectAll', false);
    $('#contractType').multiselect2('updateButtonText');
    Otable.draw();
}

function sendemail(id) {
    $('#email_contractid').val(id);
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': id},
        url: "<?php echo site_url('Quotation_contract/loademail'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $("#Email_modal").modal();
            if (!jQuery.isEmptyObject(data)) {
                $('#email').val(data['customerEmail']);
            }
            load_mail_history();
            stopLoad();
            refreshNotifications(true);
        }, error: function () {
            stopLoad();
            alert('An Error Occurred! Please Try Again.');
            refreshNotifications(true);
        }
    });
}

function SendQuotationMail() {
    var form_data = $("#Send_Email_form").serialize();
    swal({
            title: "Are You Sure?",
            text: "You Want To Send This Mail",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55 ",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: form_data,
                url: "<?php echo site_url('Quotation_contract/send_quatation_email'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#Email_modal").modal('hide');
                        save_document_email_history(data[2],data[4],data[3]);
                    }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function traceDocument(cntID,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': cntID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/trace_cnt_document'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            //myAlert(data[0], data[1]);
            $(window).scrollTop(0);
            load_document_tracing(cntID,DocumentID);
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function load_document_tracing(id,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {'purchaseOrderID': id,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $("#mcontainer").empty();
            $("#mcontainer").html(data);
            $("#tracingId").val(id);
            $("#tracingCode").val(DocumentID);
            $("#tracing_modal").modal('show');

        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function deleteDocumentTracing(){
    var purchaseOrderID=$("#tracingId").val();
    var DocumentID=$("#tracingCode").val();
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'purchaseOrderID': purchaseOrderID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $("#tracing_modal").modal('hide');
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });

}

function load_mail_history(){
    var Otables = $('#mailhistory').DataTable({"language": {
        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
    },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Quotation_contract/load_mail_history'); ?>",
        aaSorting: [[0, 'desc']],
        "bFilter": false,
        "bInfo": false,
        "bLengthChange": false,
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }
        },
        "aoColumns": [
            {"mData": "autoID"},
            {"mData": "contractCode"},
            {"mData": "ename"},
            {"mData": "toEmailAddress"},
            {"mData": "sentDateTime"}

        ],
        //"columnDefs": [{"targets": [0], "visible": false,"searchable": true}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "contractAutoID", "value": $("#email_contractid").val()});
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
function open_delivery_order_modal(id){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': id},
        url: "<?php echo site_url('Quotation_contract/open_delivery_order_modal'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();

            $("#contractID").val(id);
            $("#dotype").val(data['master']['contractType']);
            $("#segment").val(data['master']['segmentID']);
            $("#customerID").val(data['master']['customerID']);
            $("#transactionCurrencyID").val(data['master']['transactionCurrencyID']);
            $("#referenceno").val(data['master']['referenceNo']);
            $('#narration').val(data['master']['contractNarration']);
            $('#salesperson').val(data['master']['salesPersonID']).change();
            $('#contactPersonName').val(data['master']['contactPersonName']);
            $('#contactPersonNumber').val(data['master']['contactPersonNumber']);
            
            $("#delivery_order_modal").modal('show');
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
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
            mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
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

function check_item_balance_from_quotation_contract(contractAutoId){
    if (contractAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoId': contractAutoId},
            url: "<?php echo site_url('Quotation_contract/check_item_balance_from_quotation_contract'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['error']==0){
                    myAlert('w',data['message']);
                }else if(data['error']==1){
                   // myAlert('s',data['message']);
                    open_delivery_order_modal(contractAutoId)
                }else{
                //    myAlert('e',data['message']);
                }

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
}
function confirm() {
    var data = $('#deliveryorder_form').serializeArray();
    data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
    data.push({'name': 'contractAutoID', 'value': $('#contractID').val()});
    data.push({'name': 'confirm', 'value': 1});
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "You want to create this document!",/*You want to confirm this document!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_deliveryorder_from_quotation_contract_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    if(data[0]=='s'){
                        $("#segment").val('');
                        $("#customerID").val('');
                        $("#contractID").val('');
                        $("#transactionCurrencyID").val('');
                        $("#referenceno").val('');
                        $('#narration').val('');
                        $('#salesperson').val('').change();
                        $('#contactPersonName').val('');
                        $('#contactPersonNumber').val('');

                        $("#delivery_order_modal").modal('hide');
                        Otable.draw();
                        stopLoad();
                        myAlert(data[0],data[1]);
                        //confirmDeliveryOrder(data[2])
                    }else  if(!$.isEmptyObject(data['itemInsufficient'])){
                        $("#segment").val('');
                        $("#customerID").val('');
                        $("#contractID").val('');
                        $("#transactionCurrencyID").val('');
                        $("#referenceno").val('');
                        $('#narration').val('');
                        $('#salesperson').val('').change();
                        $('#contactPersonName').val('');
                        $('#contactPersonNumber').val('');

                        $("#delivery_order_modal").modal('hide');

                        $('#insufficient_item_body').html('');
                        $.each(data['itemInsufficient'], function (item, value) {
                            $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['currentStock'] + '</td></tr>')
                        });
                        $("#insufficient_QUT_item_modal").modal({backdrop: "static"});
                        stopLoad();
                    } else{
                        stopLoad();
                        myAlert(data[0],data[1]);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        });
}

function contract_close(contractAutoID) {
    if(contractAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'contractAutoID': contractAutoID, 'html': true},
            url: "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contractAutoID').val(contractAutoID);
                $("#contract_close_modal").modal({backdrop: "static"});
                $('#contract_close').html(data);
                $('#comments').val('');
                stopLoad();
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
}

function fetch_approval_closed_user_modal(documentID, documentSystemCode) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
        url: '<?php echo site_url('Approvel_user/fetch_document_closed_users_modal'); ?>',
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $('#closed_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Closed User');
            /* Approval user */
            $('#closed_user_body').empty();
            $('#approved_user_body').empty();
            /** Approval Details*/
            x = 1;
            if (jQuery.isEmptyObject(data['approved'])) {
                $('#approved_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                /* No Records Found */
            } else {
                $.each(data['approved'], function (key, value) {
                    comment = ' - ';
                    if (value['approvedComments'] !== null) {
                        comment = value['approvedComments'];
                    }
                    approvalDate = ' - ';
                    if (value['approveDate'] !== null) {
                        approvalDate = value['approveDate'];
                    }
                    bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                    $('#approved_user_body').append('<tr><td>' + x + '</td><td>' +  value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">' + approvalDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                    x++;
                });
            }

            /** Closed Details*/
            if (data['closedYN'] == 1) {
                $('#closed_user_body').append('<tr><td> 1 </td><td>' + data['closedBy'] + '</td><td class="text-center">' + data['closedDate'] + '</td><td class="text-center">' + data['closedReason'] + '</td></tr>');
            } else {
                $('#closed_user_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                 /*No Records Found*/
            }

            /** confirmed Details*/
            $("#closed_user_modal").modal({backdrop: "static", keyboard: true});
            $("#c_document_code").html(data['document_code']);
            $("#c_document_date").html(data['document_date']);
            $("#c_confirmed_date").html(data['confirmed_date']);
            $("#c_conformed_by").html(data['conformed_by']);

            if (documentID == 'LA' && (data.requestForCancelYN !== undefined)) {
                $('#closed_user_label').append(' - Cancellation');
            }
            stopLoad();
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            /*An Error Occurred! Please Try Again*/
            stopLoad();
            refreshNotifications(true);
        }
    });
}


function open_generated_payment_applications_modal(id){
    var contractID = id;
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': id},
        url: "<?php echo site_url('Quotation_contract/fetch_payment_applications_headers'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();

            $("#generated_payment_application_form input#contractID").val(contractID);

            // remove all existing rows
            $("#generated_payment_application_list tr:has(td)").remove();

            // get all item
           var trHTML2 = '';
           var itemNo = 1;
           var itemCount = -1;
           var conf = '';
           $.each(data, function (index, itemData) {      
                if(itemData.confirmedPA == 0){ 
                    var conf = '<span class="label label-danger">&nbsp;</span>';
                    var action = '<a onclick="edit_payment_application_details('+itemData.autoID+','+itemData.contractAutoID+')" role="button"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="View"></span></a> <a onclick="open_payment_application_details('+itemData.autoID+')" role="button"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a> <a onclick="load_payment_advice('+itemData.contractAutoID+','+itemData.autoID+')" target="_blank" role="button"><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="View"></span></a>';
                }else{
                    var conf = '<span class="label label-success">&nbsp;</span>';
                    var action = '<a onclick="open_payment_application_details('+itemData.autoID+')" role="button"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a> <a onclick="load_payment_advice('+itemData.contractAutoID+','+itemData.autoID+')" target="_blank" role="button"><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="View"></span></a>';
                }          
                trHTML2 += '<tr><td>' + itemNo + '</td><td>' + itemData.documentID + '</td><td align="center">' + conf + '</td><td align="center">' + itemData.confirmedBy + '</td><td align="right">'+ action +'</td></tr>';
                ++itemNo;   
                ++itemCount;                
           });      
           if(data == ''){
            $("#generated_payment_application_form input#autoID").val(0);
           } else{
            $("#generated_payment_application_form input#autoID").val(data[itemCount]['autoID']);
           }

           $('#generated_payment_application_list').append(trHTML2);
            
           $("#ViewGeneratedPaymentApplications_modal").modal('show');
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function open_payment_application_modal(){
    var contractAutoID= $("#generated_payment_application_form input#contractID").val();
    var autoID= $("#generated_payment_application_form input#autoID").val();
    
    swal({
        title: "Payment Application !",
        text: "Are you sure want to genarate payment application?",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Ok*/
        closeOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                save_payment_application_header_and_details(contractAutoID,autoID);                
            }
    });
    
}

function checkConfirmedPA(contractAutoID){
    if(contractAutoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID},
            url: "<?php echo site_url('Quotation_contract/checkConfirmedPA'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();     
                }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
}

function open_payment_application_details(autoID){
    
    if(autoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'autoID': autoID},
            url: "<?php echo site_url('Quotation_contract/fetch_payment_application_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                
            // remove all existing rows
            $("#records_table_details tr:has(td)").remove();

                // get each item
            var trHTMLrow = '';
            var itemNo = 1;
            $.each(data['detail'], function (index, itemData) {
                trHTMLrow += '<tr><td>' + itemNo + '</td><td>' + itemData.itemSystemCode + '</td><td align="left">' + itemData.itemDescription + '</td><td align="right" class="cum-qty">' + itemData.PAcuQty + '</td><td align="right">' + itemData.prevQty + '</td><td align="right" class="current-qty">' + itemData.currentQty + '</td></tr>';
                    ++itemNo;                   
            });
            $('#records_table_details').append(trHTMLrow);            
                
            $("#payment_application_details_modal").modal('show');
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
}

function edit_payment_application_details(autoID,contractAutoID){
    
    if(autoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'autoID': autoID},
            url: "<?php echo site_url('Quotation_contract/edit_payment_application_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

            // remove all existing rows
            $("#records_table_details_edit tr:has(td)").remove();

                // get each item
            var trHTMLrow = '';
            var itemNo = 1;
            $.each(data['detail'], function (index, itemData) {
                trHTMLrow += '<tr id="pa_item_row_'+itemData.PADetailsAutoID+'"><td>' + itemNo + '</td><td>' + itemData.itemSystemCode + '</td><td align="left">' + itemData.itemDescription + '</td><td align="right" class="cum-qty">' + itemData.cuQty + '</td><td align="right" class="cum-qty"><span id="PAcuQty_'+itemData.PADetailsAutoID+'">' + itemData.PAcuQty + '</span></td><td align="right"><span id="prevQty_'+itemData.PADetailsAutoID+'">' + itemData.prevQty + '</span></td><td align="right"><input type="number" min="0" class="input_st_1" name="current_qty" id="current_qty_'+itemData.PADetailsAutoID+'" value="'+ itemData.currentQty +'" onchange="payment_application_change_qty('+ itemData.PADetailsAutoID +','+ itemData.PAAutoID +','+ itemData.unittransactionAmount +')"></td></tr>';
                    ++itemNo;                   
            });
            $('#records_table_details_edit').append(trHTMLrow);   

            $("#payment_application_form_edit input#contractAutoID").val(contractAutoID);
            $("#payment_application_form_edit input#PAAutoID").val(data['detail'][0]['PAAutoID']);

            $("#payment_application_details_modal_edit").modal('show');
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
}

function update_payment_application_item_details(id,PAAutoID,unittransactionAmount){
    var currentQty = $("#payment_application_form_edit input#current_qty_"+id).val();
    var prevQty = $("#payment_application_form_edit #prevQty_"+id).text();
    var PAcuQty = $("#payment_application_form_edit #PAcuQty_"+id).text();
    var contractAutoID = $("#payment_application_form_edit #contractAutoID").val();

    if(PAcuQty==0){
        PAcuQtyUpdate = currentQty;
    }else{
        PAcuQtyUpdate = PAcuQty;
    }
    if(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'PADetailsAutoID': id,'currentQty': currentQty,'unittransactionAmount': unittransactionAmount, 'prevQty':prevQty, 'PAcuQty':PAcuQtyUpdate},
            url: "<?php echo site_url('Quotation_contract/update_payment_application_item_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                edit_payment_application_details(PAAutoID,contractAutoID);

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
}

function payment_application_change_qty(id,PAAutoID,unittransactionAmount){

    var column = $("#pa_item_row_"+id)[0];
    var cum_qty = column.cells[3]['innerText']; // Get the Cumulative Qty	
    var prev_qty = column.cells[5]['innerText']; // Get the Previous Qty	
    var current_qty = $("#current_qty_"+id).val(); // Get the Current Qty	

    var qty_check_max = (cum_qty - prev_qty);
    if(current_qty <= qty_check_max){
        update_payment_application_item_details(id,PAAutoID,unittransactionAmount);
    } else{
        myAlert('e', 'You have an entered wrong quantity');       
    }

}

function save_payment_application_header_and_details(contractAutoID,autoID){
    
    if (contractAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID,'autoID':autoID},
            url: "<?php echo site_url('Quotation_contract/save_payment_application_header_and_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
               stopLoad();
               if(data == 'pending'){
                    myAlert('w', 'Please confirm previous payment application');
               }else if(data == 'qtyOver'){
                    myAlert('w', 'Document Quantity Reached the maximum');
               }else if(data == true){
                    myAlert('s', 'New Payment Application Generated');
               }else{
                    myAlert('e', 'Something went wrong!');
               }               
               open_generated_payment_applications_modal(contractAutoID);
                
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe:)", "error");
            }
        });

    }
}

function load_payment_advice(contractAutoID, autoID){
    window.open("Quotation_contract/load_payment_advice_new/"+contractAutoID+'/'+autoID, '_blank');
}
</script>

<script>
$(document).ready(function(){
    $("#payment_application_form_edit").on("submit", function(event){
        event.preventDefault();
        var PAAutoID = $("#payment_application_form_edit input#PAAutoID").val();
        var contractAutoID = $("#payment_application_form_edit input#contractAutoID").val();
        
        swal({
        title: "Payment Application Confirmation!",
        text: "Are you sure want to confirm?",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Ok*/
        closeOnConfirm: true
        },
        function (isConfirm) {
            if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'autoID': PAAutoID},
                        url: "<?php echo site_url('Quotation_contract/confirm_payment_application'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();                            
                            myAlert('s', 'Confirmation Success!');                            
                            open_generated_payment_applications_modal(contractAutoID);
                            $('#payment_application_details_modal_edit').modal('hide');
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
            }
        });

    });
});


function generatepaymentcollection_drilldown(code,autoID,collectedStatus) {
        $('#statuspv').val(null).trigger('change');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'autoID': autoID},
            url: "<?php echo site_url('Quotation_contract/Quotation_contract_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#statuspv').val(data['documentStatus']).change();
                $('#contractAutoIDpv').val(data['contractAutoID']);
                $('#colectedbyemp').val(data['approved_by']);
                $('#collectiondatepv').val(data['approved_date']);
                $('#commentpv').val(data['approved_comment']);

                if(data['documentStatus']== 1)
                {
                   
                    
                } else if(data['documentStatus']== 2)
                {
                    $('#employeename').removeClass('hide');
                    $('#collectiondate').removeClass('hide');
                    $('#comment').removeClass('hide');
                    $('#commentonhold').addClass('hide');
                  
                } else if(data['documentStatus']== 3)
                {
                    $('#employeename').removeClass('hide');
                    $('#collectiondate').removeClass('hide');
                    $('#comment').removeClass('hide');
                    $('#commentonhold').addClass('hide');
                    $('#commentpv').val(data['approved_comment']);
                    $('#commentpvonhold').val(' ');

                } else
                {
                    $('#employeename').addClass('hide');
                    $('#collectiondate').addClass('hide');
                    $('#comment').removeClass('hide');
                    $('#commentonhold').addClass('hide');
                    // $('#commentpvonhold').val(' ');
                    // $('#commentpv').val(' ')
                }

                $('#paymentcollection_drilldown').modal("show");

                stopLoad();
                refreshNotifications(true);Report
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function update_documentStatus(ev){

        var documentStatus = $(ev).val();

        if(documentStatus == 3){

            $('#employeename').removeClass('hide');
            $('#collectiondate').removeClass('hide');
            $('#comment').removeClass('hide');
            $('#commentonhold').addClass('hide');
            $('#commentpv').val('');
            $('#commentpvonhold').val(' ');
            
        }else if(documentStatus == 4){
            $('#employeename').addClass('hide');
            $('#collectiondate').addClass('hide');
            $('#comment').removeClass('hide');
        }else if(documentStatus == 1 || documentStatus == 2){
            $('#employeename').addClass('hide');
            $('#collectiondate').addClass('hide');
            $('colectedbyemp').addClass('hide');
            $('#commentonhold').addClass('hide');
        }

    }

    function updatepvstatus()
    {
        var data = $('#quotaion_status').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Quotation_contract/update_approved_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                   Otable.draw();
                   // $('#paymentcollection_drilldown').modal("hide");
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
</script>