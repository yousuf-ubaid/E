<?php
$to_day = date('Y-01-01');
$next_year = date('Y-01-01', strtotime("$to_day +1year"));
$payment_drop =  [0 => 'No', 1 => 'Yes'];
$company_id = trim($this->uri->segment(3));

$contracts_arr = [];
$userType = current_userType();
//$userType = 2;
if($userType == 1) {
    $contracts = PBS_contract_api_requests();
    if($contracts['status'] == 'e'){
        $msg = '<div class="alert alert-danger" style="margin-top: 20px"><b>Error !</b>'.$contracts['message'] .'</div>';
        die( $msg );
    }
    $contracts_arr = (array)$contracts['data'];
    $first_column = [''=> 'Select a contact'];
    $contracts_arr = array_merge($first_column, $contracts_arr);
}

$tab = $this->input->get('tab');
$tab = empty($tab)? '': trim($tab);

$clientDB_host = $this->config->item('clientDB_host');
$clientDB_user = $this->config->item('clientDB_user');

//echo '<br/><br/><br/><pre>'; print_r($n);exit;
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<style> 
    .att_upload_btn{
        background-color: #367fa9 !important;
    }

    .att_upload_btn:hover{
        border-color: #204d74 !important;
    }

    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }

    .x-editable-number{
        text-align: right;
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }

    .user_type_drop{
        padding: 2px;
        font-size: 12px;
        height: 20px;
    }
</style>
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Company Setup</h3>

                <span class="pull-right" id="company-name-container" style="display: none">
                    Company : &nbsp; <b class="company-name-header"></b>
                </span>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="filter-panel" class="collapse filter-panel"></div>
                        <div class="m-b-md" id="wizardControl">
                            <a class="btn btn-primary btn-wizard" href="#step9" data-toggle="tab">Company Host</a>
                            <a class="btn btn-default btn-wizard" href="#step1" data-toggle="tab"
                               onclick="fetch_company_header()">Step 1 - Header</a>
                            <a class="btn btn-default btn-wizard" href="#step2" onclick="load_users_data_table()"
                               data-toggle="tab">Step 2 - User</a>
                            <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_segment()"
                               data-toggle="tab">Step 3 - Segment </a>
                            <a class="btn btn-default btn-wizard" href="#step4" onclick="fetch_financial_year()"
                               data-toggle="tab">Step 4 - Financial Year</a>
                            <a class="btn btn-default btn-wizard" href="#step5" onclick="fetch_Warehouse()"
                               data-toggle="tab">Step 5 - Warehouse</a>
                            <a class="btn btn-default btn-wizard" href="#step6" onclick="fetch_assigned_currency()"
                               data-toggle="tab">Step 6 - Currency Exchange</a>
                            <a class="btn btn-default btn-wizard" href="#step7" onclick="load_modulus();"
                               data-toggle="tab">Step 7 - Modules</a>
                            <!-- temporary commented as per Hishams request 
                            <a class="btn btn-default btn-wizard" href="#company_nav_tab" 
                             onclick="$('#com_productID').change()" data-toggle="tab">
                               Step 7 - Navigation
                            </a> -->
                            <a class="btn btn-default btn-wizard" href="#stepSubscription" onclick="load_subscription()"
                               data-toggle="tab">Step 8 - Subscription</a>
                            <a class="btn btn-default btn-wizard" href="#step8" onclick="load_conformation();"
                               data-toggle="tab">Step 9 - Complete </a>
                        </div>
                        <hr>
                        <div class="tab-content">
                            <div id="step1" class="tab-pane">
                                <div id="company_header"></div>
                            </div>
                            <div id="step2" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-inline">
                                            <label class="">Number of Module Users &nbsp;  &nbsp; </label>
                                            <a href="#" data-type="text" id="noOf_module_user" data-placement="bottom" data-pk="<?=$company_id?>"
                                              data-title="Number of Module Users" data-url="<?=site_url('Dashboard/update_noOf_module_user')?>"></a>
                                        </div>
                                    </div>
                                </div>
                                <hr/>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-inline">
                                            <label class="">User Status &nbsp;  &nbsp; </label>
                                            <?php
                                            $user_status_arr = [ ''=> 'All', '0' => 'Active', '1'=> 'Discharged' ];
                                            echo form_dropdown('discharge_status', $user_status_arr, '', 'class="form-control"
                                                id="user_status" onchange="fetch_admin_users_data_table()"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-inline">
                                            <label class="">Login Status &nbsp;  &nbsp; </label>
                                            <?php
                                            $user_status_arr = [ ''=> 'All', '1' => 'Active', '2'=> 'In active' ];
                                            echo form_dropdown('login_status_drop', $user_status_arr, '', 'class="form-control"
                                                id="login_status_drop" onchange="fetch_admin_users_data_table()"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-primary pull-right" onclick="add_user_model();">
                                            <i class="fa fa-plus"></i> New User
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-striped table-condensed table-bordered" id="com_user_tb">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%">Code</th>
                                        <th style="min-width: 40%">Employee Name</th>
                                        <th style="min-width: 15%">UserName</th>
                                        <th style="min-width: 10%">Gender</th>
                                        <th style="min-width: 10%">Date Joined</th>
                                        <th style="">Status</th>
                                        <th style="">Login</th>
                                        <th style="">User type</th>
                                        <th>Last Login</th>
                                        <th style="">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="company_users">
                                    </tbody>
                                </table>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev" onclick="">Previous</button>
                                </div>
                            </div>
                            <div id="step3" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-5">
                                        &nbsp;
                                    </div>
                                    <div class="col-md-7 text-right">
                                        <button type="button" class="btn btn-primary pull-right"
                                                onclick="segment_model();"><i
                                                    class="fa fa-plus"></i> New Segment
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <table id="segment_table" class="table table-striped table-condensed table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 15%">Segment Code</th>
                                        <th style="min-width: 50%">Description</th>
                                        <!-- <th style="min-width: 7%">&nbsp;</th>
                                        <th style="min-width: 5%">Status</th> -->
                                    </tr>
                                    </thead>
                                </table>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev" onclick="">Previous</button>
                                </div>
                            </div>
                            <div id="step4" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-5">
                                        <table class="table table-striped table-condensed table-bordered">
                                            <tr>
                                                <td><span class="label label-success">&nbsp;</span> Active</td>
                                                <td><span class="label label-danger">&nbsp;</span> Closed</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        &nbsp;
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button type="button" onclick="financial_year_model()"
                                                class="btn btn-primary pull-right"><i
                                                    class="fa fa-plus"></i> Create Financial Year
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <table id="Financial_year_table"
                                       class="table table-striped table-condensed table-bordered">
                                    <thead>
                                    <tr>
                                        <th colspan="3">Financial Year</th>
                                        <th colspan="4">Status</th>                                        
                                    </tr>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 20%">Financial Year</th>
                                        <th style="min-width: 40%">Comments</th>
                                        <th style="min-width: 5%">Active</th>
                                        <th style="min-width: 5%">Current</th>
                                        <th style="min-width: 5%">Closed</th>
                                        <th style="min-width: 5%">Active</th> 
                                    </tr>
                                    </thead>
                                </table>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev" onclick="">Previous</button>
                                </div>
                            </div>
                            <div id="step5" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-5">
                                        <table class="table table-striped table-condensed table-bordered">
                                            <tr>
                                                <td><span class="label label-success">&nbsp;</span> Active</td>
                                                <td><span class="label label-danger">&nbsp;</span> Closed</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        &nbsp;
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button type="button" onclick="open_warehouse_model()"
                                                class="btn btn-primary pull-right"><i
                                                    class="fa fa-plus"></i> Create warehouse
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <table id="warehousemaster_table"
                                       class="table table-striped table-condensed table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%">Warehouse Code</th>
                                        <th style="min-width: 40%">Description</th>
                                        <th style="min-width: 20%">Location</th>
                                        <!-- <th style="min-width: 15%">&nbsp;</th> -->
                                    </tr>
                                    </thead>
                                </table>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev" onclick="">Previous</button>
                                </div>
                            </div>
                            <div id="step6" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-striped table-condensed table-bordered"
                                               id="company_currency_table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Currency Name</th>
                                                <th>Currency Code</th>
                                                <th>Decimal Place</th>
                                                <th>#</th>
                                            </tr>
                                            </thead>
                                            <tbody id="company_currency_body">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6" id="set_conversion_div">

                                    </div>
                                </div>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev" onclick="">Previous</button>
                                </div>
                            </div>
                            <div id="step7" class="tab-pane">
                                <table class="table table-striped table-condensed table-bordered" id="menu_table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modules Name</th>
                                        <th><input type="checkbox" id="ckbCheckAll"/></th>
                                    </tr>
                                    </thead>
                                    <tbody id="modulus_body">

                                    </tbody>
                                </table>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev">Previous</button>
                                    <button type="button" class="btn btn-primary" onclick="save_nav()">Save changes
                                    </button>
                                </div>
                            </div>
                            <div id="company_nav_tab" class="tab-pane">
                                 <?php $this->load->view('nav-setup/company-nav-setup') ?>
                            </div>


                            <div id="stepSubscription" class="tab-pane">
                                <?php echo form_open('', 'role="form" id="subscription_form" autocomplete="off"'); ?>
                                <div class="row form-horizontal">
                                    <div class="col-sm-12">
                                        <div class="form-group col-sm-6">
                                            <label for="registeredDate" class="col-sm-5 control-label">Registered Date</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                                    <input type="text" class="form-control subscription_fields" id="registeredDate" value="<?=$to_day?>" name="registeredDate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="subscriptionStartDate" class="col-sm-5 control-label">Subscription Start Date</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                                    <input type="text" class="form-control subscription_fields" id="subscriptionStartDate" value="<?=$to_day?>" name="subscriptionStartDate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="nextRenewalDate" class="col-sm-5 control-label">Next Renewal
                                                Date</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                                    <input type="text" disabled class="form-control" id="nextRenewalDate" value="<?=$next_year?>"
                                                           name="nextRenewalDate">
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="form-group col-sm-6">
                                            <label for="lastRenewedDate" class="col-sm-5 control-label">Last Renewed Date</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </div>
                                                    <input type="text" class="form-control" id="lastRenewedDate" value="<?php /*echo date('Y-01-01'); */?>" name="lastRenewedDate">
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="form-group col-sm-6">
                                            <label for="currencyID" class="col-sm-5 control-label">Currency</label>
                                            <div class="col-sm-6">
                                                <?php echo form_dropdown('currencyID', all_currency_drop(), 1, 'class="form-control subscription_fields select2" id="subscription_currencyID" required'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="subscriptionAmount" class="col-sm-5 control-label">Subscription Amount</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control number subscription_fields" id="subscriptionAmount" name="subscriptionAmount">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="implementationAmount" class="col-sm-5 control-label">Implementation Amount</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control number subscription_fields" id="implementationAmount" name="implementationAmount">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="paymentEnabled" class="col-sm-5 control-label">Payment Enabled</label>
                                            <div class="col-sm-6">
                                                <?php echo form_dropdown('paymentEnabled', $payment_drop, '', 'class="form-control select2 subscription_fields1" 
                                                id="paymentEnabled" required onchange="update_paymentEnabled()"');?>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="col-sm-12">
                                        <hr>
                                        <div class="text-right m-t-xs">
                                            <button type="button" class="btn btn-default prev">Previous</button>
                                            <input type="hidden" name="isConfirmedYN" id="sub_isConfirmedYN" value="0">
                                            <button type="button" class="btn btn-primary subscription_fields" onclick="subscription_form_submit(0)">Save changes</button>
                                            <button type="button" class="btn btn-primary subscription_fields" onclick="subscription_form_submit(1)">Save & confirm</button>
                                            <input type="hidden" id="isInitialSubscriptionConfirmed" value="0">
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>

                                <hr/>

                                <div class="row" id="add_attachment_show">
                                    <div class="col-sm-6">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"> Document upload </legend>

                                            <br/>
                                            <?php echo form_open('', 'id="attachment_upload_form" role="form" class="form-horizontal"'); ?>
                                            <div class="form-group">
                                                <label for="doc_no" class="col-sm-4 control-label">Description</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="sub_att_attachmentDescription" name="attachmentDescription">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="doc_file" class="col-sm-4 control-label">File</label>
                                                <div class="col-sm-6">
                                                    <input type="file" name="document_file" class="form-control" id="document_file" placeholder="Brows Here">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="expireDate" class="col-sm-4 control-label">Expire date</label>
                                                <div class="col-sm-5">
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input type="text" id="expireDate" name="expireDate" value="" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="up-company-id" value="<?=$company_id?>">
                                            <div class="box-footer">
                                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="upload_attachment()">
                                                    Upload
                                                </button>
                                            </div>
                                            <?=form_close();?>

                                            <hr/>

                                            <div style="padding: 15px" >
                                                <table class="table table-striped table-condensed table-hover" style="">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>File Name</th>
                                                        <th>Description</th>
                                                        <th>Expire date</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="sub_attachment_modal_body" class="no-padding">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-sm-6">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"> Outlets </legend>

                                            <div class="col-md-12" style="margin-bottom: 10px;">
                                                <button class="btn btn-primary btn-xs pull-right" onclick="new_outlet()">
                                                    New Outlet
                                                </button>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="warehouse_tb" class="<?=table_class()?>">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15px">#</th>
                                                            <th style="width: auto">Code</th>
                                                            <th style="width: auto">Description</th>
                                                            <th style="width: auto">Location</th>
                                                            <th style="width: 55px">Status</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>


                            <div id="step8" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="no-print pull-right">
                                        <button class="btn btn-default btn-sm" onclick="print_confirmation()">
                                             <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                                        </button>
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <div id="conform_body"></div>
                                <hr>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-default prev">Previous</button>
                                    <button class="btn btn-primary " onclick="save_draft()">Save & Draft</button>
                                    <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm
                                    </button>
                                </div>
                            </div>
                            <div id="step9" class="tab-pane active">
                                <?php echo form_open('', 'role="form" id="company_host_form"'); ?>
                                
                                <div class="row" style="margin-bottom: 15px">
                                    <?php if($userType == 1){?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-2">
                                                <label style="padding-top: 5px !important;">Contract &nbsp;  &nbsp; </label>
                                            </div>
                                            <div class="col-md-10">
                                                <?=form_dropdown('pbs_contract', $contracts_arr, '', 'id="pbs_contract" class="form-control select2"');?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">&nbsp;</div>
                                    <?php }?>                                    

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-md-6" style="padding-top: 5px"><label>Company Type </label></div>
                                            <div class="col-md-6">                                 
                                                <?=form_dropdown('company_type', $sys_types, null, 'class="form-control" id="company_type"')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr style="<?=($userType == 1)? 'margin-top: 0px' : ''?>"/>
                                
                                <h4>Database Details</h4>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label>Host </label>
                                        <input type="text" class="form-control" value="<?=$clientDB_host?>" readonly/>
                                    </div>                                    
                                    <div class="form-group col-sm-4">
                                        <label>Database User Name </label>
                                        <input type="text" class="form-control" value="<?=$clientDB_user?>" readonly>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Database Name </label>
                                        <input type="input" class="form-control" id="db_name" name="db_name">
                                    </div> 
                                </div>
                                <hr>
                                <h4>Attachment Details</h4>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label>Attachment Host </label>
                                        <input type="text" class="form-control" id="attachmentHost"
                                               name="attachmentHost">
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Attachment Folder Name </label>
                                        <input type="text" class="form-control" id="attachmentFolderName"
                                               name="attachmentFolderName">
                                    </div>
                                </div>
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-primary" type="submit">Save & Next</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>

    <div class="modal fade" id="user_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false"
         data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">User Account</h4>
                </div>
                <form class="form-horizontal" id="user_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Ename1" class="col-sm-4 control-label">First Name</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="Ename1" name="Ename1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Ename2" class="col-sm-4 control-label">Last Name</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="Ename2" name="Ename2">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Gender" class="col-sm-4 control-label">Gender</label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('Gender', array('' => 'Select Gender', '1' => 'Male', '2' => 'Female'), '', 'class="form-control" id="Gender" required'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="EDOJ" class="col-sm-4 control-label">Date of Joined</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="EDOJ" value="<?php echo date('Y-m-d'); ?>"
                                                   id="EDOJ" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_group_id" class="col-sm-4 control-label">User Group</label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('user_group_id', array('' => 'Select User Group', '1' => 'Male', '2' => 'Female'), '', 'class="form-control" id="user_group_id" required'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="EEmail" class="col-sm-4 control-label">User Name</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-envelope-o"></i></div>
                                            <input type="email" class="form-control" id="EEmail" name="EEmail">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: none;">
                                    <label for="EmpDesignationId" class="col-sm-4 control-label">Designation</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="EmpDesignationId"
                                               name="EmpDesignationId">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Password" class="col-sm-4 control-label">Password</label>
                                    <div class="col-sm-7">
                                        <input type="Password" class="form-control" id="Password" name="Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payCurrencyID" class="col-sm-4 control-label">Currency</label>
                                    <div class="col-sm-7">
                                        <?php echo form_dropdown('payCurrencyID', all_currency_drop(), '', 'class="form-control" id="payCurrencyID" required'); ?>
                                    </div>
                                </div>
                                <!--<div class="form-group">
                                    <label for="UserName" class="col-sm-4 control-label">User Name</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="UserName" name="UserName">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="Submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" tabindex="-1" id="segment_model" class="modal fade bs-example-modal-lg">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Add New Segment</h5>
                </div>
                <form class="form-horizontal" id="segment_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="UserName" class="col-sm-4 control-label">Segment Code</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="segmentcode" name="segmentcode">
                                <input type="hidden" class="form-control" id="segmentID" name="segmentID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="description" name="description">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-primary">Save <span
                                        class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" tabindex="-1" id="financial_year_model" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Add New Financial Year</h3>
                </div>
                <form role="form" id="financial_year_form" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Beginning Date</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"
                                                                          aria-hidden="true"></i>
                                        </div>
                                        <input type="text" class="form-control" id="beginningdate"
                                               value="<?php echo date('Y-01-01'); ?>" name="beginningdate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Ending Date</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"
                                                                          aria-hidden="true"></i>
                                        </div>
                                        <input type="text" class="form-control" id="endingdate"
                                               value="<?php echo date('Y-12-31'); ?>" name="endingdate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Comments</label>
                                <div class="col-sm-6">
                                        <textarea class="form-control" id="comments" name="comments"
                                                  rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="warehousemaster_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Add New Warehouse</h3>
                </div>
                <form role="form" id="warehousemaster_form" class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="warehouseredit" name="warehouseredit">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> Code</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="warehousecode" name="warehousecode">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> Description</label>
                                <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="warehousedescription"
                                          name="warehousedescription"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> Location</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="warehouselocation"
                                           name="warehouselocation">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-6">
                                <textarea rows="2" class="form-control" id="warehouseAddress"
                                          name="warehouseAddress"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> Telephone</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="warehouseTel" name="warehouseTel">
                                </div>
                            </div>
                        </div>
                        <div class="row hide">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"> Pos Location</label>
                                <div class="col-sm-6" style="">
                                    <input type="checkbox" value="1" id="isPosLocation" name="isPosLocation">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="invoice_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="">
    <div class="modal-dialog modal-lg">
        <?php echo form_open('', 'role="form" id="subscription_inv_form" autocomplete="off"'); ?>
        <div class="modal-content">
            <div class="modal-body" id="invoice_body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" onclick="subscription_invoice_generation()">Generate</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="user_setup_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="">
    <div class="modal-dialog">
        <?php echo form_open('', 'role="form" id="user_setup_form" class="form-horizontal" autocomplete="off"'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"> User Password Reset </h3>
            </div>
            <div class="modal-body" id="">
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">Employee Name</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="user_setup_name" readonly>
                        <input type="hidden" id="user_setup_id" name="user_id">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">User Name</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="user_setup_userName" name="userName" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">Password</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" onclick="reset_password()">Reset password</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>


<div class="modal fade" id="newOutlet_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <?=form_open('', 'id="new_outlet_frm" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Create Outlet : <span class="outlet_title" style="font-size: 16px;"></span></h3>
            </div>

            <div class="modal-body" >
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">POS Type <?required_mark()?></label>
                        <div class="col-sm-6">
                            <select name="pos_type" id="pos_type" class="form-control" onchange="change_form_content(this)">
                                <option value="1">General</option>
                                <option value="0">Restaurant</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Segment <?required_mark()?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('pos_segment', null, '', 'id="pos_segment" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group" id="pos-related-div">
                        <label class="col-sm-4 control-label">POS Template <?required_mark()?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('posTemplateID', null, '', 'id="posTemplateID" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Code <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_code" name="outlet_code" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Name <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_name" name="outlet_name" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Location <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_location" name="outlet_location" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Address</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="outlet_address" name="outlet_address" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Telephone</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_tel" name="outlet_tel" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Foot Note</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="foot_note" name="foot_note" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="create_outlet()">Create</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
            <?=form_close()?>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="financePeriod_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Financial Period</h3>
            </div>            
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="financePer_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Beginning Date</th>
                            <th style="min-width: 30%">Ending Date</th>
                            <th style="min-width: 5%">Is Active</th>
                            <th style="min-width: 5%">Is Current</th>
                            <th style="min-width: 5%">Is Closed</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <br>

                <div class="Usernote">
                    <strong style="color: red">Note :</strong>
                    <ul>
                        <li><p>You can have multiple period active at the same time. Click the necessary period</p></li>

                        <li><p>Only one period should be kept as Current. By default system will take the Current Period based on this selection.</p></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>            
        </div>
    </div>
</div>
<?php

echo form_open('', 'id="frm_print_conf"');
echo '<input type="hidden" name="companyid" id="print_conf_company_id" value="0">';
echo form_close();

?>
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<link rel="stylesheet" href="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.css');?>">
<script type="text/javascript" src="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.js');?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>


<script type="text/javascript">
    var companyid;
    let _tab = <?=json_encode($tab);?>;
    let noOf_module_user_obj = $('#noOf_module_user');

    noOf_module_user_obj.editable({
        ajaxOptions: { dataType: 'json' },
        params: function(params) {
            // add additional params from data-attributes of trigger element
            params.company_id = $(this).editable().data('pk');
            return params;
        },
        success: function(data, newValue) {
            myAlert(data[0], data[1]);
        }
    });

    noOf_module_user_obj.on("click",function(){
        $(this).next().find(".editable-input input").addClass('x-editable-number');
        $('.x-editable-number').numeric({decimal: false, negative:false});
    });

    $(document).ready(function () {
        $("#timezone").select2();
        $('.select2').select2();
        $('#subscriptionAmount, #implementationAmount').numeric({decimalPlaces:3, negative:false});

        $('#expireDate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "days"
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
        });

        companyid = null;
        p_id = <?=json_encode($company_id); ?>;
        
        if (p_id != 'null') {
            companyid = p_id;
            fetch_host_detail();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }


        $('#beginningdate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "months"
        }).on('changeDate', function (ev) {
            $('#financial_year_form').bootstrapValidator('revalidateField', 'beginningdate');
            $(this).datepicker('hide');
        });

        $('#endingdate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "months"
        }).on('changeDate', function (ev) {
            $('#financial_year_form').bootstrapValidator('revalidateField', 'endingdate');
            $(this).datepicker('hide');
        });

        $('#companystartdate').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#company_form').bootstrapValidator('revalidateField', 'companystartdate');
            $(this).datepicker('hide');
        });

        $('#EDOJ').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#user_form').bootstrapValidator('revalidateField', 'EDOJ');
            $(this).datepicker('hide');
        });

        $('#company_host_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                <?php if($userType == 1){?>
                pbs_contract: {validators: {notEmpty: {message: 'Contract is required.'}}},
                <?php }?>
                host: {validators: {notEmpty: {message: 'Host is required.'}}},
                db_name: {validators: {notEmpty: {message: 'Database Name is required.'}}},
                db_username: {validators: {notEmpty: {message: 'Database User Name is required.'}}},
                db_password: {validators: {notEmpty: {message: 'Database Password is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            submit_company_hostDet(0);
        });


        $('#subscription_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                registeredDate: {validators: {notEmpty: {message: 'Registered date is required.'}}},
                subscriptionStartDate: {validators: {notEmpty: {message: 'Subscription start date is required.'}}},
                currencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                subscriptionAmount: {validators: {notEmpty: {message: 'Subscription amount is required.'}}},
                paymentEnabled: {validators: {notEmpty: {message: 'Payment Enabled is required.'}}}
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            let $form = $(e.target);
            let bv = $form.data('bootstrapValidator');
            let data = $form.serializeArray();
            data.push({'name': 'company_id', 'value': companyid});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Dashboard/save_subscription'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){

                        if(data['isConfirmedYN'] == 1){
                            $('#isInitialSubscriptionConfirmed').val(1);
                            $('.subscription_fields').prop('disabled', true);

                            if(data['paymentEnabled'] == 1){
                                setTimeout(function(){
                                    swal({
                                            title: "",
                                            text: "Do you want to generate invoice for subscription amount",
                                            type: "warning",
                                            showCancelButton: true,
                                            confirmButtonColor: "#DD6B55",
                                            cancelButtonText: "No",
                                            confirmButtonText: "Yes"
                                        },
                                        function (isConfirm) {
                                            if(isConfirm){
                                                $('#invoice_body').html(data['built_view']);
                                                $('#invoice_modal').modal('show');
                                            }
                                            else{
                                                if(data['is_implementation_billing'] == 1){
                                                    setTimeout(function() {
                                                        generate_implementation_invoice();
                                                    }, 300);
                                                }
                                            }
                                        }
                                    );
                                }, 200);
                            }
                        }
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        });

        $('#financial_year_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                beginningdate: {validators: {notEmpty: {message: 'Beginning Date is required.'}}},
                endingdate: {validators: {notEmpty: {message: 'Ending Date is required.'}}},
                comments: {validators: {notEmpty: {message: 'Comments is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Dashboard/save_financial_year'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#financial_year_form')[0].reset();
                        $('#financial_year_form').bootstrapValidator('resetForm', true);
                        $("#financial_year_model").modal("hide");
                        fetch_financial_year();
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });


        $('#user_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                Ename1: {validators: {notEmpty: {message: 'First Name is required.'}}},
                Ename2: {validators: {notEmpty: {message: 'Last Name is required.'}}},
                Gender: {validators: {notEmpty: {message: 'Gender is required.'}}},
                EDOJ: {validators: {notEmpty: {message: 'EDOJ is required.'}}},
                EEmail: {validators: {notEmpty: {message: 'UserName is required.'}}},
                Password: {validators: {notEmpty: {message: 'Password is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Dashboard/save_user'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.btn-wizard').removeClass('disabled');
                        $('#user_form')[0].reset();
                        $('#user_form').bootstrapValidator('resetForm', true);
                        $("#user_model").modal("hide");
                        fetch_admin_users_data_table();
                    }

                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#segment_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                segmentcode: {
                    validators: {
                        notEmpty: {
                            message: 'Segment Code is required'
                        },
                        stringLength: {
                            max: 3,
                            message: 'Character must be below 4 character'
                        }
                    }
                },
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Dashboard/save_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $("#segment_model").modal("hide");
                        fetch_segment();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#warehousemaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                warehousecode: {validators: {notEmpty: {message: ' Code is required.'}}},
                warehousedescription: {validators: {notEmpty: {message: ' Description is required.'}}},
                warehouselocation: {validators: {notEmpty: {message: ' Location is required.'}}},
                warehouseAddress: {validators: {notEmpty: {message: ' Address is required.'}}},
                warehouseTel: {validators: {notEmpty: {message: ' Telephone is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Dashboard/save_warehousemaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $("#warehousemaster_model").modal("hide");
                        fetch_Warehouse();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        $('#registeredDate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "days"
        }).on('changeDate', function (ev) {
            $('#subscription_form').bootstrapValidator('revalidateField', 'registeredDate');
            $(this).datepicker('hide');
        });

        $('#subscriptionStartDate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "days"
        }).on('changeDate', function (ev) {
            $('#subscription_form').bootstrapValidator('revalidateField', 'subscriptionStartDate');
            $(this).datepicker('hide');

            var nrd = $(this).datepicker('getDate');
            if (nrd) {
                nrd.setDate(nrd.getDate() + 365);
                //$( ".selector" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
                $('#nextRenewalDate').datepicker('setDate', nrd);
            }
        });

        $('#nextRenewalDate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "days"
        }).on('changeDate', function (ev) {
            $('#subscription_form').bootstrapValidator('revalidateField', 'nextRenewalDate');
            $(this).datepicker('hide');
        });

        $('#lastRenewedDate').datepicker({
            format: "yyyy-mm-dd",
            viewMode: "months",
            minViewMode: "days"
        }).on('changeDate', function (ev) {
            $('#subscription_form').bootstrapValidator('revalidateField', 'lastRenewedDate');
            $(this).datepicker('hide');
        });

        $('.btn-wizard').click( function(){
            let this_href = $(this).attr('href');
            updateBrowserUrl( this_href );
        });
    });

    function submit_company_hostDet(isVerified, newDBVerify=0){
        let data = $('#company_host_form').serializeArray();
        data.push({'name': 'companyid', 'value': companyid});
        <?php if($userType == 1){?>
        data.push({'name': 'pbs_contract_display', 'value': $('#pbs_contract :selected').text()});
        data.push({'name': 'isVerified', 'value': isVerified});
        <?php }?>

        data.push({'name': 'newDBVerify', 'value': newDBVerify});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Dashboard/save_company_host'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                let nxtFn = null;

                switch(data[0]){
                    case 'w':
                        nxtFn = confirm_contractDet; break;                            
                    case 'newDB':
                        nxtFn = verifyNewDB_creation; break;
                }
                
                if (data[0] === 's') {
                    if(companyid == null){                        
                        swal(
                            {                                
                                title: '',
                                text: data[1],
                                type: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",                                
                                confirmButtonText: "Ok"
                            },
                            function () {
                                startLoad();
                                window.location = '<?=site_url('companyAdmin/AddCompany/')?>'+data['last_id']+'?tab=step1';
                            }
                        );
                        return false;        
                    }

                    myAlert('s', data[1]);
                    updateBrowserUrl('#step1');
                    $('[href=#step1]').removeClass('disabled');
                    $('[href=#step1]').tab('show');
                    companyid = data['last_id'];
                    fetch_company_header()
                }
                else{
                    ajax_toaster(data, null, nxtFn);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function confirm_contractDet(data){
        bootbox.confirm({
            title: '<strong>Confirmation!</strong>',
            message: data[1],
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    submit_company_hostDet(1);
                }
            }
        });
    }

    function verifyNewDB_creation(data){
        bootbox.confirm({
            title: '<strong>Confirmation!</strong>',
            message: data[1],
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    submit_company_hostDet(0,1);
                }
            }
        });
    }

    function subscription_form_submit(status){
        $('#sub_isConfirmedYN').val(status);

        if(status == 1){
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm subscription details",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: "No",
                    confirmButtonText: "Yes"
                },
                function () {
                    $('#subscription_form').submit();
                }
            );
        }
        else{
            $('#subscription_form').submit();
        }

    }

    function subscription_invoice_generation(){
        let post_data = $('#subscription_inv_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: post_data,
            url: "<?php echo site_url('Dashboard/initial_invoice_generate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#invoice_modal').modal('hide');

                    if(data['is_implementation_billing'] == 1){
                        setTimeout(function(){
                            generate_implementation_invoice();
                        }, 300);
                    }
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function open_warehouse_model() {
        $('#warehousemaster_form')[0].reset();
        $('#warehousemaster_form').bootstrapValidator('resetForm', true);
        $("#warehousemaster_model").modal({backdrop: "static"});
    }

    function fetch_assigned_currency() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid},
                url: "<?php echo site_url('Dashboard/fetch_assigned_currency'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#company_currency_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#company_currency_body').append('<tr class="danger"><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            $('#company_currency_body').append('<tr><td>' + x + '</td><td>' + value['CurrencyName'] + '</td><td>' + value['CurrencyCode'] + '</td><td style="text-align: right">' + value['DecimalPlaces'] + '</td><td><button class="btn btn-primary btn-xs" onclick="set_conversion(' + value['currencyassignAutoID'] + ')">Set conversion</button></td></tr>');
                            x++;
                        });
                        $('#company_currency_table').DataTable();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    $(document).ready(function () {
        $("#ckbCheckAll").click(function () {
            $(".checkbox").prop('checked', $(this).prop('checked'));
        });
    });

    function load_modulus() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid},
                url: "<?php echo site_url('Dashboard/fetch_assigned_modulus'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#modulus_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#modulus_body').append('<tr class="danger"><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            var status = '';
                            if (value['status'] != 0) {
                                status = 'checked';
                            }
                            $('#modulus_body').append('<tr><td>' + x + '</td><td>' + value['description'] + '</td><td><center><input class="checkbox" id="check_' + value['navigationMenuID'] + '" type="checkbox" value="' + value['navigationMenuID'] + '" ' + status + '></center></td></tr>');
                            x++;
                        });
                        //$('#menu_table').DataTable();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }
    
    function remove_modul(navigationMenuID) {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid, 'navigationMenuID': navigationMenuID},
                url: "<?php echo site_url('Dashboard/remove_modul'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    load_modulus();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_nav() {
        var selected = [];
        $('#modulus_body input:checked').each(function () {
            selected.push($(this).val());
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid, 'navigationMenuID': selected},
                url: "<?php echo site_url('dashboard/save_nav_menu'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function set_conversion(mastercurrencyassignAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'mastercurrencyassignAutoID': mastercurrencyassignAutoID, 'companyid': companyid},
            url: "<?php echo site_url('Dashboard/set_conversion'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#set_conversion_div').html(data);
            }, error: function () {

            }
        });
    }

    function load_users_data_table(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'company_id': companyid },
            url: "<?php echo site_url('Dashboard/get_noOf_module_user'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                noOf_module_user_obj.editable('setValue', data['noOfUsers']);
                stopLoad();
            }, error: function () {
                alert('An unexpected error occurred');
                stopLoad();
            }
        });

        fetch_admin_users_data_table();
    }

    var com_user_tb = $('#com_user_tb');
    function fetch_admin_users_data_table() {
        com_user_tb = $('#com_user_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_users'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                $(".switch-chk").bootstrapSwitch();
            },
            "columnDefs": [
                {"targets": [ 0,6,7,8,10 ], "orderable": false },
            ],
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "Ename2"},
                {"mData": "UserName"},
                {"mData": "gender_str"},
                {"mData": "date_join"},
                {"mData": "discharge_str"},
                {"mData": "login_act"},
                {"mData": "user_type_str"},
                {"mData": "last_login_str"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value': companyid});
                aoData.push({'name': 'user_status', 'value': $('#user_status').val()});
                aoData.push({'name': 'login_status', 'value': $('#login_status_drop').val()});
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

    function fetch_admin_users() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid},
                url: "<?php echo site_url('Dashboard/fetch_admin_users'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#company_users').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#company_users').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            var gender = 'Male';
                            var action = '<button onclick="make_admin(' + value['EIdNo'] + ')" class="btn btn-default btn-xs" type="submit">Make Admin</button>';
                            if (value['Gender'] == 2) {
                                gender = 'Female';
                            }
                            if (value['userGroupID'] != null) {
                                action = value['userGroupID'];
                            }
                            $('#company_users').append('<tr><td>' + x + '</td><td>' + value['ECode'] + '</td><td>' + gender + '</td><td>' + value['UserName'] + '</td><td>' + value['Ename1'] + ' ' + value['Ename2'] + '</td><td>' + value['EDOJ'] + '</td><td>' + action + '</td></tr>');
                            x++;
                        });
                        $('#com_user_tb').DataTable();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function make_admin(emp_id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyid': companyid, 'emp_id': emp_id},
            url: "<?php echo site_url('dashboard/make_admin'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('.btn-wizard').removeClass('disabled');
                fetch_admin_users_data_table();
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function fetch_Warehouse() {
        $('#warehousemaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_warehouse'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"}
            ],
            "columnDefs": [{ "targets": [0], "orderable": false }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "company_id", "value": companyid});
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

    let finYearTbl;    
    function fetch_financial_year() {
        finYearTbl = $('#Financial_year_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?=site_url('Dashboard/load_financial_year');?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {                                       
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

                setTimeout(() => {
                    $('.finYearRadio').each( (i, obj)=>{
                        $(obj).prop('checked', ($(obj).data('status') == 1));
                    });
                }, 300);
            },            
            "aoColumns": [
                {"mData": "companyFinanceYearID"},
                {"mData": "financial_year"},
                {"mData": "comments"},
                {"mData": "active_status"},
                {"mData": "current_status"},
                {"mData": "closed_status"},                
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [3, 4, 5, 6],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "company_id", "value": companyid});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },            
        });
    }

    function fetch_segment() {
        $('#segment_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/load_segment'); ?>",
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "segmentID"},
                //{"mData": "segmentID"},
                {"mData": "segmentCode"},
                {"mData": "description"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "company_id", "value": companyid});
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

    function financial_year_model() {
        //$('#financial_year_form')[0].reset();
        //$('#financial_year_form').bootstrapValidator('resetForm', true);
        $("#financial_year_model").modal({backdrop: "static"});
    }

    function open_period_modal(id) {
        $("#financePeriod_modal").modal({backdrop: "static"});
        load_period_data(id);
    }

    let finPeridTbl = null;
    function load_period_data(id) {
        finPeridTbl = $('#financePer_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "iDisplayLength": 25,
            "sAjaxSource": "<?=site_url('Dashboard/load_finance_period'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }                
            },
            "aoColumns": [
                {"mData": "companyFinancePeriodID"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "status"},
                {"mData": "current"},
                {"mData": "closed"}
            ],
            "columnDefs": [{
                "targets": [3, 4, 5],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "company_id", "value": companyid});
                aoData.push({"name": "companyFinanceYearID", "value": id});

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

    function add_user_model() {
        GLAutoID = null;
        $("#user_model").modal({backdrop: "static"});
        $('#user_form')[0].reset();
        fetch_user_group();
        $('#user_form').bootstrapValidator('resetForm', true);
    }

    function fetch_user_group() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyid': companyid},
            url: "<?php echo site_url('Dashboard/fetch_user_group'); ?>",
            success: function (data) {
                $('#user_group_id').empty();
                var mySelect = $('#user_group_id');
                //mySelect.append($('<option></option>').val('0').html('All User Group'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['userGroupID']).html(text['description']));
                    });
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function segment_model() {
        $("#segment_model").modal({backdrop: "static"});
        // $('#user_form')[0].reset();
        // $('#user_form').bootstrapValidator('resetForm', true);
    }

    function save_draft() {
        if (companyid) {
            swal({
                    title: "Are you sure?",
                    //text: "You will not be able to recover this file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save & Draft"
                },
                function () {
                    window.location.href = "<?php echo site_url('Dashboard'); ?>";
                });
        }
        ;
    }

    function load_conformation() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'companyid': companyid, 'html': true},
                url: "<?php echo site_url('Dashboard/load_company_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_company_header() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'companyid': companyid, 'html': true},
                url: "<?php echo site_url('Dashboard/load_company_header_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#company_header').html(data);
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (companyid) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this  file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'companyid': companyid},
                        url: "<?php echo site_url('Dashboard/company_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            window.location.href = "<?php echo site_url('Dashboard'); ?>";
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function fetch_host_detail() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid},
                url: "<?php echo site_url('Dashboard/load_company_host_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if ($.isEmptyObject(data)) {
                        swal(
                            {
                                title: "Error",
                                text: "Database details not found",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",                                    
                                confirmButtonText: "Ok"
                            },
                            function () {
                                window.location = "<?=site_url('companyAdmin')?>";
                            }
                        );   
                        stopLoad();
                        return false;
                    }

                    if( data['company_name_str'] != null ){
                        $('#company-name-container').show();
                        $(".company-name-header").text(data['company_name_str']);
                    }
                    
                    $("#company_type").val(data['isPartnerCompany']);
                    $("#host").val(data['host']);
                    $("#db_name").val(data['db_name']);
                    $("#db_username").val(data['db_username']);
                    $("#db_password").val(data['db_password']);
                    $("#attachmentHost").val(data['attachmentHost']);
                    $("#attachmentFolderName").val(data['attachmentFolderName']);

                    $("#com_productID").val(data['product_id']);

                    <?php if($userType == 1){?>
                    if( parseInt(data['contractID']) !== 0 ){
                        $("#pbs_contract").val(data['contractID']).change();
                    }
                    <?php }?>

                    if(_tab){
                        let temTab = '#'+_tab;

                        $('.btn-wizard').each( (i, wiz) => {
                            let this_href = $(wiz).attr('href');
                            if( this_href == temTab ){                                    
                                $(wiz).click()    
                            }                                
                        });                                               
                    }      

                    stopLoad();                                  
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function updateBrowserUrl(tab){        
        let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        if( tab ){
            tab = tab.split('#');            
            if(tab[1] && tab[1] !== 'step9'){
                newurl += '?tab='+tab[1];
            }
        }
        window.history.pushState({path:newurl},'',newurl);            
    }

    function load_subscription() {
        if (companyid) {
            load_warehouse(companyid);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'company_id': companyid},
                url: "<?php echo site_url('Dashboard/load_company_subscription_detail'); ?>",
                beforeSend: function () {
                    $('.subscription_fields').prop('disabled', false);
                    $('#isInitialSubscriptionConfirmed').val(0);
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        let cur = (data['subscriptionCurrency_str'] == 0 )? 1: data['subscriptionCurrency_str'];
                        $("#registeredDate").val(data['registeredDate']);
                        $("#subscriptionStartDate").val(data['subscriptionStartDate']);
                        $("#nextRenewalDate").val(data['nextRenewalDate']);
                        $("#lastRenewedDate").val(data['lastRenewedDate']);                         
                        $("#subscription_currencyID").val(cur).change();
                        $("#subscriptionAmount").val(data['subscriptionAmount']);
                        $("#implementationAmount").val(data['implementationAmount']);
                        $("#paymentEnabled").val(data['paymentEnabled']).change();

                        $('#subscription_form').bootstrapValidator('revalidateField', 'currencyID');
                        if(data['isInitialSubscriptionConfirmed'] == 1){
                            $('.subscription_fields').prop('disabled', true);
                            $('#isInitialSubscriptionConfirmed').val(1);
                        }
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

            load_subscription_attachment_view();
        }
    }


    function generate_implementation_invoice(){
        if (companyid) {
            swal({
                    title: "",
                    text: "Do you want to generate invoice for implementation amount",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: "No",
                    confirmButtonText: "Yes"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'company_id': companyid},
                        url: "<?php echo site_url('Dashboard/fetch_data_implementation_inv_generation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);

                            if(data[0] == 's'){
                                $('#invoice_body').html(data['built_view']);
                                $('#invoice_modal').modal('show');
                            }
                            stopLoad();
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            );
        }
    }

    function print_confirmation(){
        $('#print_conf_company_id').val(companyid);
        var form = document.getElementById('frm_print_conf');
        form.target = '_blank';
        form.action = '<?php echo site_url('Dashboard/load_company_conformation/Company-data-print'); ?>';
        form.submit();
    }
    
    function update_paymentEnabled() {
        var status = $('#isInitialSubscriptionConfirmed').val();
        if(status == 1){
            swal({
                title: "Are you sure?",
                text: "You want to update this field",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var paymentEnabled = $('#paymentEnabled').val();

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'company_id': companyid, 'paymentEnabled': paymentEnabled},
                    url: "<?php echo site_url('Dashboard/update_paymentEnabled'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if(data[0] == 'e'){
                            myAlert(data[0], data[1]);
                        }

                    }, error: function () {
                        stopLoad();

                    }
                });

            });
        }
    }

    function upload_attachment() {
        var formData = new FormData($("#attachment_upload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Dashboard/upload_attachments'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#sub_att_remove_id').click();
                    $('#sub_att_attachmentDescription').val('');
                    load_subscription_attachment_view();
                }
            },
            error: function (data) {
                stopLoad();
                myAlert('e', 'Error in process');
            }
        });
        return false;
    }

    function load_subscription_attachment_view(){
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'html',
            data: {'company_id': companyid},
            url: "<?php echo site_url('Dashboard/load_subscription_attachment_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#sub_attachment_modal_body').html(data);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function sub_attachment_delete(id, fileName){
        swal(
            {
                title: "Are you sure?",
                text: "You will not be able to recover this  file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Dashboard/subscription_attachment_delete'); ?>",
                    data: {'company_id': companyid,'attachmentID': id, 'fileName': fileName},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_subscription_attachment_view();
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'Error in attachment delete process');
                    }
                });
            }
        );
    }

    function user_setup(userID, empName, userName){
        $('#user_setup_id').val(userID);
        $('#user_setup_name').val(empName);
        $('#user_setup_userName').val(userName);
        $('#password').val('');

        $('#user_setup_modal').modal('show');
    }

    function reset_password(){
        let post_data = $('#user_setup_form').serializeArray();
        post_data.push({'name': 'company_id', 'value': companyid});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: post_data,
            url: "<?php echo site_url('Dashboard/user_password_rest'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#user_setup_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function change_login_status(obj, userID, empName, user){
        let msg, log_status;
        if ($(obj).prop('checked')) {
            msg = 'activate';
            log_status = 0;
        } else {
            msg = 'inactivate';
            log_status = 4;
        }

        swal({
                title: "Are you sure?",
                text: "You want to "+msg+" the login of "+empName+" ("+user+")",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'userID': userID, 'company_id': companyid, 'type': log_status},
                        url: "<?php echo site_url('Dashboard/reset_login_attempts'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_admin_users_data_table();
                            }
                        },
                        error: function () {
                            stopLoad();
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            let changeStatus = ( !$(obj).prop('checked') );
                            $('#login_status' + userID).prop('checked', changeStatus).change();
                        }
                    });
                }
                else {
                    let changeStatus = ( !$(obj).prop('checked') );
                    $('#login_status' + userID).prop('checked', changeStatus).change();
                }
            }
        );
    }

    function update_userType(type, EIdNo) {
        var initialVal = (parseInt(type.value) === 0)? 1: 0;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID':EIdNo, 'type': type.value, 'company_id': companyid},
            url: "<?= site_url('Dashboard/update_userType'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] != 's') {
                    $(type).val( initialVal );
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();                
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown); 
                $(type).val( initialVal );
            }
        });
    }

    function finanCloseReactiveConf(id, type){
        bootbox.confirm({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Confirmation!</strong>',
            message: "<b>Are you sure?</b><br/>You want to Activate this financial "+type+"!",
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    finanCloseReactive(id, type);
                }
                else {
                    reCheck_financeChk(id, type);
                }
            }
        });
 
    } 

    function finanCloseReactive(id, type){        
        $.ajax({
            type: 'post',
            async: true,
            dataType: 'json',
            data: {'id': id, 'company_id': companyid},
            url: "<?=site_url('Dashboard/activate_finance_');?>"+type,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();                          
                ajax_toaster(data);

                if(data[0] == 'e'){
                    reCheck_financeChk(id, type);                                
                }
                
                if(data[0] == 's'){
                    if(type == 'year'){
                        finYearTbl.ajax.reload();
                    }
                    else{
                        finPeridTbl.ajax.reload();
                    }                
                }
            }, 
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                reCheck_financeChk(id, type);
                stopLoad();
                myAlert('e', ""+errorThrown);
            }
        });
    }

    function reCheck_financeChk(id, type){
        let chk = (type == 'year')? '#finYearCloseChk_': '#finPeriodCloseChk_';
        chk += id;
        $(chk).prop("checked", true);
    }
</script>


<script>
    function load_warehouse(company_id) {
        $('#warehouse_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_warehouse'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $(".switch-chk").bootstrapSwitch();
            },
            "columnDefs": [
                { "targets": [0,4], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"},
                {"mData": "wr_status"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value': company_id});
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

    function change_warehouse_status(obj, id, wareHouse){
        let msg, status;
        if ($(obj).prop('checked')) {
            msg = 'activate';
            status = 1;
        } else {
            msg = 'inactivate';
            status = 0;
        }

        swal({
                title: "Are you sure?",
                text: "You want to "+msg+" the outlet "+wareHouse,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'warehouseID': id, 'company_id': companyid, 'status': status},
                        url: "<?php echo site_url('Dashboard/change_warehouse_status'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                        },
                        error: function () {
                            stopLoad();
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            let changeStatus = ( !$(obj).prop('checked') );
                            $('#warehouseStatus' + id).prop('checked', changeStatus).change();
                        }
                    });
                }
                else {
                    let changeStatus = ( !$(obj).prop('checked') );
                    $('#warehouseStatus' + id).prop('checked', changeStatus).change();
                }
            }
        );
    }

    function new_outlet() {
        $('#new_outlet_frm')[0].reset();
        $('#newOutlet_modal').modal('show');
        $('#pos-related-div').hide();
        load_pos_dropDown_data();
    }

    function create_outlet() {
        let postDate = $('#new_outlet_frm').serializeArray();
        postDate.push({'name': 'company_id', 'value': companyid});

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/create_outlet'); ?>",
            data: postDate,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    warehouse_tb.ajax.reload();
                    $('#newOutlet_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_pos_dropDown_data() {
        let segment_drop = $('#pos_segment');
        let temp_drop = $('#posTemplateID');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/get_pos_template_master_drop'); ?>",
            data: {'company_id': companyid},
            cache: false,
            beforeSend: function () {
                startLoad();
                segment_drop.empty();
                temp_drop.empty();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    segment_drop.append( '<option value="">Select a segment</option>' );
                    temp_drop.append( '<option value="">Select a template</option>' );

                    if(data['drop_segment'].length > 0){
                        $.each(data['drop_segment'], function(i, val){
                            segment_drop.append( '<option value="'+val['segmentID']+'">'+val['description']+'</option>' );
                        });
                    }

                    if(data['drop_template'].length > 0){
                        $.each(data['drop_template'], function(i, val){
                            temp_drop.append( '<option value="'+val['posTemplateID']+'">'+val['posTemplateDescription']+'</option>' );
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function change_form_content(obj){
        if($(obj).val() == 1){
            $('#pos-related-div').fadeOut('slow');
        }
        else{
            $('#pos-related-div').fadeIn('slow');
        }
    }
</script>
