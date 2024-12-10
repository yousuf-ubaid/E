<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$standard = getStandardDetail();
$this->load->helper('mfq');
$markupPolicy = getPolicyValues('EFM', 'All');
if(!isset($markupPolicy)) {
    $markupPolicy = 1;
}
$page_id = isset($page_id) && $page_id ? $page_id : 0;
$main_category_arr = all_main_category_drop();

$manufacturing_Flow = getPolicyValues('MANFL', 'All');
if($manufacturing_Flow == 'GCC' || $manufacturing_Flow == 'Micoda'){
$colspan = 'colspan="8"';
}else{
    $colspan = 'colspan="7"';
}

$pricingFormula_arr = array(
    0 => 'Markup',
    1 => 'Margin',
);
?>
<?php echo head_page($_POST["page_name"], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .arrow-steps .step.current {
        color: #fff !important;
        background-color: #657e5f !important;
    }

    .table-responsive {
        overflow: visible !important
    }

</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#header" data-toggle="tab"><?php echo $this->lang->line('manufacturing_estimate_information') ?><!--Estimate Header--></a>
    <a class="btn btn-default btn-wizard" href="#detail" data-toggle="tab">
        <?php echo $this->lang->line('manufacturing_estimate_detail') ?><!--Estimate Detail--></a>
    <a class="btn btn-default btn-wizard" href="#attachment" data-toggle="tab">
        <?php echo $this->lang->line('common_attachment') ?><!--Attachment--></a>
    <a class="btn btn-default btn-wizard" href="#print" onclick="estimate_print();" data-toggle="tab">
        <?php echo $this->lang->line('common_confirmation') ?><!--Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div class="tab-pane active" id="header">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content">
                    <div class="row">
                        <div class="col-md-12 animated zoomIn">
                            <form class="frm_estimate" method="post">
                                <input type="hidden" id="estimateMasterID" name="estimateMasterID"
                                       value="<?php echo $page_id ?>">
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_estimate_information') ?><!--Estimate Information--> </h2>
                                </header>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title"><?php echo $this->lang->line('common_customer') ?><!--Customer--> </label>
                                            </div>

                                            <div class="form-group col-sm-4">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('mfqCustomerAutoID', all_mfq_customer_drop(), '', 'class="form-control select2" id="est-mfqCustomerAutoID"');
                                                    ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_estimate_date') ?><!--Estimate Date--> </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <div class="input-req" title="Required Field">
                                                    <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                    <div class='input-group date filterDate' id="">
                                                        <input type='text' class="form-control"
                                                               name="documentDate"
                                                               id="est_documentDate"
                                                               value="<?php echo $current_date; ?>"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                                    </div>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_delivery_date') ?><!--Delivery Date--> </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <div class='input-group date filterDate' id="">
                                                    <input type='text' class="form-control"
                                                           name="deliveryDate"
                                                           id="est-deliveryDate"
                                                           value="<?php echo $current_date; ?>"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title"><?php echo $this->lang->line('common_currency') ?><!--Currency--> </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('currencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="est-currencyID" onchange="currency_validation(this.value,\'BOM\')" required'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_approval_status') ?><!--Approval Status--> </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <div class="input-req" title="Required Field">
                                                    <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                    <?php echo form_dropdown('submissionStatus', all_mfq_status(2), 7, 'class="form-control" id="est-submissionStatus"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <?php if($manufacturing_Flow == 'GCC'){ ?>
                                                    <label class="title">Validity</label>
                                                <?php }else{?>
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_warranty') ?><!--Warranty--> </label>
                                                <?php } ?>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <?php $key = array_search(2, array_column($standard, 'typeID'));
                                                ?>
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <?php echo form_dropdown('warranty', all_mfq_month_drop(), $key != "" ? $standard[$key]["Description"] : "", 'class="form-control" id="est-warranty"'); ?>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title">Show Discount</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <?php echo form_dropdown('discountView', array('1'=> 'View Discount', '0'=>'Hide Discount'), '0', 'class="form-control" id="est-discountView"'); ?>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title">Pricing Formula</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('pricingFormula', $pricingFormula_arr, 1, 'class="form-control select2" id="pricingFormula" onchange="" required'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <label class="title"><?php echo $this->lang->line('common_description') ?><!--Description--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                            <textarea class="form-control" id="est-description"
                                                                      name="description" rows="2"></textarea>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-2 md-offset-2">
                                                <?php if($manufacturing_Flow == 'SOP'){ ?>
                                                    <label class="title"><?php echo 'General Notes' ?><!--Scope of Work--> </label>
                                                <?php } else { ?>
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_scope_of_work') ?><!--Scope of Work--> </label>
                                                <?php } ?>
                                            </div>
                                           <!--  <div class="form-group col-sm-6">
                                                <textarea class="form-control" id="est-scopeOfWork" name="scopeOfWork"
                                                          rows="2"></textarea>
                                            </div> -->
                                            <?php $key = array_search(4, array_column($standard, 'typeID')); ?>
                                            <div class="form-group col-sm-10">
                                                    <textarea class="form-control richtext" id="est-scopeOfWork"
                                                              name="scopeOfWork"
                                                              rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                            </div>
                                        </div>

                                     
                                      <hr>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_technical_detail') ?><!--Technical Detail--> </label>
                                                </div>
                                                <?php $key = array_search(4, array_column($standard, 'typeID')); ?>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="est-technicalDetail"
                                                                name="technicalDetail"
                                                                rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-2"></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                        data-typeid="2" onclick="open_all_notes(2, 'est-technicalDetail')" data-textareaid="est-technicalDetail">
                                                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"> <?php echo $this->lang->line('manufacturing_exclusions') ?><!--Exclusions--> </label>
                                                </div>
                                            
                                                <?php $key = array_search(4, array_column($standard, 'typeID')); ?>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="est-exclusions"
                                                                name="exclusions"
                                                                rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-2"></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                        data-typeid="3" onclick="open_all_notes(3, 'est-exclusions')" data-textareaid="est-exclusions">
                                                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                             <hr>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"><?php echo 'Deviation' ?></label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                    <textarea class="form-control richtext" id="est-scopeOfWork" name="scopeOfWork" rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-2"></div>
                                                <div class="form-group col-sm-10"> 
                                                    <button class="btn btn-primary open-notes-btn" type="button" onclick="open_all_notes(1, 'est-scopeOfWork')" data-typeid="1" data-textareaid="est-scopeOfWork">
                                                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>     
                                                </div>
                                            </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_payment_terms') ?><!--Payment Terms--> </label>
                                                </div>
                                                <?php $key = array_search(4, array_column($standard, 'typeID')); ?>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="est-paymentTerms"
                                                                name="paymentTerms"
                                                                rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row"> 
                                                <div class="col-sm-2"></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                        data-typeid="4"  onclick="open_all_notes(4, 'est-paymentTerms')"  data-textareaid="est-paymentTerms">
                                                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>

                                           <hr>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_terms_and_condition') ?><!--Terms & condition--> </label>
                                                </div>
                                                <?php $key = array_search(1, array_column($standard, 'typeID'));
                                                ?>
                                                <div class="form-group col-sm-10">
                                                    <textarea class="form-control richtext" id="est-termsAndCondition"
                                                            name="termsAndCondition"
                                                            rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row"> 
                                                <div class="col-sm-2"></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                        data-typeid="5" onclick="open_all_notes(5, 'est-termsAndCondition')" data-textareaid="est-termsAndCondition">
                                                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>   
                                            <hr>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"> <?php echo $this->lang->line('manufacturing_delivery_terms') ?><!--Delivery Terms--> </label>
                                                </div>
                                                <?php $key = array_search(4, array_column($standard, 'typeID'));?>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="est-deliveryTerms"
                                                                name="deliveryTerms"
                                                                rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-2"></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                    data-typeid="6" onclick="open_all_notes(6, 'est-deliveryTerms')" data-textareaid="est-deliveryTerms">
                                                    <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>

                                           <!-- <div class="form-group col-sm-6">
                                                    <textarea class="form-control" id="est-deliveryTerms"
                                                              name="deliveryTerms" rows="2"></textarea>
                                            </div> -->
                                        </div>
                                        
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title"> <?php echo $this->lang->line('manufacturing_validity') ?><!--Validity--> </label>
                                                </div>
                                                <?php $key = array_search(3, array_column($standard, 'typeID')); ?>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="est-validity"
                                                                name="validity"
                                                                rows="2"><?php echo $key != "" ? $standard[$key]["Description"] : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">   
                                                <div class="form-group col-sm-2 "></div>
                                                <div class="form-group col-sm-10">
                                                    <button class="btn btn-primary open-notes-btn" type="button"
                                                    data-typeid="7" onclick="open_all_notes(7, 'est-validity')" data-textareaid="est-validity">
                                                    <i class="fa fa-bookmark" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>

                                           <hr>
                                        </div>

                                    </div>
                                </div>
                                <br>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-12 ">
                                        <div class="pull-right">

                                            <button class="btn btn-primary" onclick="saveEstimate()"
                                                    type="button"
                                                    id="submitBtn">
                                                <?php echo $this->lang->line('common_save') ?><!--Save-->
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="detail">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <div class="row">
                    <div class="col-md-12">
                        <header class="head-title">
                            <h2><?php echo $this->lang->line('manufacturing_item_detail') ?><!--Item Detail--> </h2>
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-condesed mfqTable">
                            <thead>
                            <tr>
                                <th colspan="5"> <?php echo $this->lang->line('manufacturing_item_detail') ?><!--Item Detail--></th>
                                <th <?php echo $colspan ?>><?php echo $this->lang->line('manufacturing_cost_detail') ?><!--Cost Detail--> <span
                                            class="currency">(<?php echo $this->common_data['company_data']['company_default_currency']; ?>
                                        )</span></th>
                                <th style="width: 5%">
                                    <button type="button" onclick="estimate_detail_modal()"
                                            class="btn btn-primary btn-sm"><i
                                                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item') ?><!--Add Item-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 8%"><?php echo $this->lang->line('common_code') ?><!--Code--></th>
                                <th style="min-width: 15%" class="text-left"><?php echo $this->lang->line('common_description') ?><!--Description--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_uom') ?><!--UOM--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty') ?><!--Qty--></th>
                                <th style="min-width: 8%"><?php echo $this->lang->line('common_unit_cost') ?><!--Unit Cost--></th>
                                <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_total_cost') ?><!--Total Cost--></th>
                                <th class="pricingFormulaHeader" style="min-width: 5%">Markup(%)</th>
                                <th style="width: 8%"><?php echo $this->lang->line('manufacturing_selling_price') ?><!--Selling Price--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_discount') ?><!--Discount-->(%)</th>
                                <th style="width: 8%">Discounted Price</th>
                                <th class="actual_pricingFormulaHeader" style="width: 8%">Actual Margin</th>
                                <?php if($manufacturing_Flow == 'Micoda'){ ?>
                                    <th style="min-width: 5%">Allotted Manhours</th>
                                <?php } ?>
                                <?php if($manufacturing_Flow == 'GCC'){ ?>
                                    <th style="min-width: 5%">Unit Selling Price</th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_action') ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="est-table_body">
                            <tr class="danger">
                                <td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found') ?><!--No Records Found--></b></td>
                            </tr>
                            </tbody>
                            <tfoot id="table_tfoot">
                            <tr>
                                <td colspan="4">
                                    <div class="text-right"><?php echo $this->lang->line('common_total') ?><!--Total--></div>
                                </td>
                                <td>
                                    <div id="est-tot_qty"
                                         style="" class="text-right">0.00
                                    </div>
                                </td>
                                <td>
                                    <div id="est-tot_unitCost"
                                         style="" class="text-right">0.00
                                    </div>
                                </td>
                                <td>
                                    <div id="est-tot_totCost"
                                         style="" class="text-right">0.00
                                    </div>
                                </td>
                                <td colspan="2"></td>
                                <td colspan="2">
                                    <div>&nbsp;</div>
                                    <div>&nbsp;</div>
                                    <div class="pricingFormulaHeader" style="text-align: right">
                                            Margin(%) 
                                    </div>            
                                    <div style="text-align: right;margin-bottom:0px;margin-bottom:8px;margin-top:2px;">
                                        Total Selling Price
                                    </div>
                                    
                                    <div style="text-align: right">
                                        <?php echo $this->lang->line('manufacturing_discount') ?><!--Discount-->(%)
                                    </div>
                    
                                    <div style="text-align: right">
                                        Total Discounted Price
                                    </div>
                                    <div>&nbsp;</div>
                                        <div class="actual_pricingFormulaHeader" style="text-align: right;">
                                            Actual Margin
                                        </div>
                                    <?php if($manufacturing_Flow == 'SOP'){ ?>
                                        <div>&nbsp;</div>
                                        <div style="text-align: right;">
                                            Warranty Cost
                                        </div>
                                        <div>&nbsp;</div>
                                        <div style="text-align: right;">
                                            Commision
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <div>&nbsp;</div>
                                    <div id="est-tot_sellingPrice"
                                         style="text-align: right">0.00
                                    </div>
                                    <div style="text-align: right;">
                                        <input type="text" name="marginTot" placeholder="0" id="est-marginPerTot"
                                               class="number" value="0"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onfocus="this.select();" onkeyup="calculateItemTotal()"
                                               onchange="save_estimate_detail_margin_total()" style="width: 100%;">
                                    </div>
                                    <div id="est-tot_masterSellingPrice"
                                         style="text-align: right;">0.00
                                    </div>
                                    <div style="text-align: right;">
                                        <input type="text" name="discountTot" placeholder="0" id="est-discountPerTot"
                                               class="number" value="0"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onfocus="this.select();" onkeyup="calculateItemTotal()"
                                               onchange="save_estimate_detail_discount_total()" style="width: 100%">
                                    </div>
                                    <div id="est-tot_masterDiscountPrice"
                                         style="text-align: right;">0.00
                                    </div>
                                    <div>&nbsp;</div>
                                    <div  id="est-tot_actualMarginPrice"
                                        style="text-align: right;">0.00
                                    </div>
                                    <?php if($manufacturing_Flow == 'SOP'){ ?>
                                    <div>&nbsp;</div>
                                    <div id="est-tot_actualMarginPrice" style="text-align: right;">
                                        <input class="number" type="text" name="warrantyCost" id="warrantyCost" value="0.00" 
                                        onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                        onchange="save_estimate_detail_warranty_cost()" style="margin-bottom:10px;">
                                    </div>
                                   
                                    <div id="est-tot_actualMarginPrice" style="text-align: right;">
                                        <input class="number" type="text" name="commision" id="commision" value="0.00"
                                        onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                        onchange="save_estimate_detail_commision()">
                                    </div>
                                    <?php } ?>
                                </td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="attachment">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_attachment') ?><!--Attachment--></h2>
                </header>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="attachment_uplode_form" class="form-inline"'); ?>
                    <input type="hidden" name="documentSystemCode" id="documentSystemCode"
                           value="<?php echo $page_id ?>">
                    <input type="hidden" name="documentID" id="documentID" value="EST">
                    <input type="hidden" name="document_name" id="document_name" value="ESTIMATE">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control"
                                       name="attachmentDescription" placeholder="Description..." id="attachmentDescription"
                                       style="width: 240%;">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control filein" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                        aria-hidden="true"></span></span><span
                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                           aria-hidden="true"></span></span><input
                                            type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default"
                                    onclick="document_uplode()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" id="show_all_attachments">
                        <header class="infoarea">
                            <div class="search-no-results"><?php echo $this->lang->line('common_no_attachment_found') ?><!--NO ATTACHMENT FOUND-->
                            </div>
                        </header>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="print">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <div id="review">
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12 ">
                        <div class="pull-right">
                            <button class="btn btn-success" onclick="confirmEstimate()"
                                    type="button"
                                    id="">
                                <?php echo $this->lang->line('common_confirm') ?><!--Confirm-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="estimate_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_customer_inquiry_simple') ?><!--Customer Inquiry--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5><?php echo $this->lang->line('manufacturing_customer_inquiry_simple') ?><!--Customer Inquiry--></h5>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="ciCode">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <form id="frm_customerInquiry" class="fromCustomerInquiry" method="post">
                            <input type="hidden" id="customerID" name="customerID" value="">
                            <table class="table table-striped table-condesed mfqTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('common_code') ?><!--Code--></th>
                                    <th class="text-left"><?php echo $this->lang->line('common_description') ?><!--Description--></th>
                                    <th><?php echo $this->lang->line('common_uom') ?><!--UOM--></th>
                                    <th><?php echo $this->lang->line('manufacturing_total_qty') ?><!--Total Qty--></th>
                                    <th><?php echo $this->lang->line('manufacturing_balance_qty') ?><!--Balance Qty--></th>
                                    <th><?php echo $this->lang->line('common_qty') ?><!--Qty--></th>
                                    <th><?php echo $this->lang->line('common_cost') ?><!--Cost--></th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_ci_detail">

                                </tbody>
                            </table>
                        </form>

                        <!--if there is no item in customer inquiry display this form to add item to perticular customer inquiry-->
                        <!--initially this will be hidden-->
                        <form id="frm_customerInquiryDirect" class="directCustomerInquiry" method="post">
                            <input type="hidden" id="direct_mfqCustomerAutoID" name="mfqCustomerAutoID" value="">
                            <input type="hidden" id="" name="estimateMasterID" value="<?php echo $page_id ?>">
                            <table class="table table-striped table-condesed mfqTable"
                                    id="tbl_customerInquiryDirect">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('common_item') ?><!--Item--></th>
                                    <th><?php echo $this->lang->line('common_uom') ?><!--UOM--></th>
                                    <th><?php echo $this->lang->line('common_qty') ?><!--Qty--></th>
                                    <th><?php echo $this->lang->line('common_cost') ?><!--Cost--></th>
                                    <th style="min-width: 5%">
                                        <div class=" pull-right">
                                                        <span class="button-wrap-box">
                                                            <button type="button" data-text="Add" id="btnAdd"
                                                                    onclick="add_more_finish_goods()"
                                                                    class="button button-square button-tiny button-royal button-raised">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </span>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="table_body_ci_direct_detail">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_close') ?><!--Close--></button>
                <button type="button" class="btn btn-primary btnci" onclick="save_customer_inquiry_items()"><?php echo $this->lang->line('common_save_change') ?><!--Save changes-->
                </button>
                <button type="button" class="btn btn-primary btncid" onclick="save_customer_inquiry_direct_items()">
                    <?php echo $this->lang->line('common_save_change') ?><!--Save changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bom_detail_modal" role="dialog" aria-labelledby="myModalLabel"
        data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="bomHeader"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="bomContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_new_item_modal" role="dialog" aria-labelledby="myModalLabel"
        data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 65%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="bomHeader">Add New Item</h4>
            </div>
            <input type="hidden" name="row_id" id="row_id">
            <form method="post" id="add_estimate_item_form" autocomplete="off">

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2><?php echo $this->lang->line('manufacturing_item_information');?><!--Item Information--> </h2>
                                    </header>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('common_item_description');?><!--Item Description--> </label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <span class="input-req" title="Required Field">
                                                <input type="text" name="itemName" id="itemName" class="form-control" placeholder="<?php echo $this->lang->line('manufacturing_item_name');?>">
                                                <span class="input-req-inner"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_secondary_code');?><!--Secondary Code--></label>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <input type="text" name="secondaryItemCode" id="secondaryItemCode" class="form-control" placeholder="<?php echo $this->lang->line('manufacturing_item_code');?>">
                                        </div>
                                    </div>
                                    <div class="row hide" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('common_category');?><!--Category--></label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <select name="itemType" class="form-control" id="itemType">
                                                <option value=""><?php echo $this->lang->line('common_select');?><!--Select--></option>
                                                <option value="1">Raw material</option>
                                                <option value="2">Finish good</option>
                                                <option value="3">Semi finish good</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_finance_category');?><!--Finance Category--></label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, '', 'class="form-control select2" id="mainCategoryID"  onchange="load_sub_cat()"'); ?>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_sub_category');?><!--Sub Category --></label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <select name="subcategoryID" id="subcategoryID" class="form-control searchbox select2" onchange="load_sub_sub_cat()">
                                                <option value="">Select Category</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_sub_sub_category');?><!--Sub sub Category--></label>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox select2">
                                                <option value="">Select Category</option>
                                            </select>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_unit_of_measure');?><!--Units of measure--></label>
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('defaultUnitOfMeasureID', all_umo_new_drop(), '', 'class="form-control select2" id="defaultUnitOfMeasureID" '); ?>
                                                <span class="input-req-inner"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row hide unbilledservice" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title">unbilled Services Gl Code</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('unbilledServicesGLAutoID', fetch_all_gl_codes(), '', 'class="form-control select2" id="unbilledServicesGLAutoID" '); ?>
                                                <span class="input-req-inner"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2><?php echo $this->lang->line('manufacturing_categories'); ?><!--Categories--> </h2>
                                    </header>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_main'); ?><!--Main--> </label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <?php echo form_dropdown('mfqCategoryID', get_mfq_category_drop(), '', 'class="form-control" id="mfqCategoryID" '); ?>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_sub'); ?><!--Sub-->  </label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <select name="mfqSubCategoryID" class="form-control" id="frm_subCategory">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_sub_sub'); ?><!--Sub Sub-->  </label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <select name="mfqSubSubCategoryID" class="form-control" id="frm_subSubCategory">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit" id="submitItemBtn"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item');?><!--Add Item--></button>
                <button type="button" class="btn btn-default saveItem" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="update_linked_item_modal" role="dialog" aria-labelledby="myModalLabel"
        data-width="40%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Update Linked ERP Item</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input class="hidden" id="mfqItemID_glUpdate" name="mfqItemID_glUpdate">
                    <div class="form-group col-sm-3 md-offset-2">
                        <label class="title">ERP Item :</label>
                    </div>

                    <div class="form-group col-sm-6">
                        <div class="input-req" title="Required Field">
                            <?php echo form_dropdown('linkedItemAutoID', all_mfq_erp_item_drop(), '', 'class="form-control select2" id="linkedItemAutoID"'); ?>
                            <span class="input-req-inner"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_close') ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="update_revenue_gl()">Update Linked Item </button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script>
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var search_id = 1;
    var estimateMasterID = "";
    var currency_decimal = 3;

    var isFormulaChanged = 1;
    var textareaid = "";
   
    // $('.open-notes-btn').click(function() {
    //     var typeID = $(this).data('typeid');
    //     var textareaID = $(this).data('textareaid');
    //     open_all_notes(typeID, textareaID);
    // });




    // var isFormulaChanged = <?php // echo $markupPolicy; ?>;

    $(document).ready(function () {
        $('.select2').select2();
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_estimate', '', 'Estimate');
        });
        Inputmask().mask(document.querySelectorAll("input"));
        <?php
        if ($page_id) {
        ?>
        estimateMasterID = '<?php echo $page_id  ?>';
        load_estimate_detail('<?php echo $page_id  ?>');
        load_attachments('EST',<?php echo $page_id  ?>);
        load_default_note('EST');
        $('[href=#detail]').removeClass('disabled');
        //$('[href=#detail]').tab('show');
        <?php
        }else{
        ?>
        $('.btn-wizard').addClass('disabled');
        <?php
        }
        ?>
        loadEstimate();
        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.directCustomerInquiry').hide();
        $('.btncid').hide();

        tinymce.init({
            selector: ".richtext",
            height: 200,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],

            templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }]
        });

        $('#add_estimate_item_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                itemName: {validators: {notEmpty: {message: 'Item Description is required.'}}},/*Item Description is required*/
                defaultUnitOfMeasureID: {validators: {notEmpty: {message: 'Unit of measure is required.'}}},/*Unit of measure is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'defaultUnitOfMeasure', 'value': $('#defaultUnitOfMeasureID option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_ItemMaster/add_new_item_estimate'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(data['error'] == 0) {
                        var rowID = $('#row_id').val();
                        myAlert('s', data['message']);
                        if(rowID)
                        {
                            $('#ci_search_'+rowID).val(data['itemname']);
                            $('#mfqItemID_'+rowID).val(data['ItemID']);

                        }
                        $('#add_new_item_modal').modal('hide');
                        $("#add_estimate_item_form")[0].reset();
                    } else {
                        myAlert('e', data['message']);
                    }
                    refreshNotifications(true);
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    function loadEstimate() {
        if (estimateMasterID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_Estimate/load_mfq_estimate"); ?>',
                dataType: 'json',
                data: {estimateMasterID: estimateMasterID},
                async: false,
                success: function (data) {

                    isFormulaChanged = data['isFormulaChanged'];
                    $("#pricingFormula").val(data['isFormulaChanged']).change();
                    $('.currency').html('( ' + data['CurrencyCode']+ ' )');
                    $("#est-mfqCustomerAutoID").val(data['mfqCustomerAutoID']).change();
                    $("#direct_mfqCustomerAutoID").val(data['mfqCustomerAutoID']);
                    $("#est-documentDate").val(data['documentDate']).change();
                    $("#est-deliveryDate").val(data['deliveryDate']).change();
                    $("#est-description").val(data['description']);
                    //$("#est-scopeOfWork").val(data['scopeOfWork']);
                    //$("#est-technicalDetail").val(data['technicalDetail']);
                    $("#est-marginPerTot").val(data['totMargin']);
                    $("#est-discountPerTot").val(data['totDiscount']);
                    $("#est-submissionStatus").val(data['submissionStatus']);
                    //$("#est-paymentTerms").val(data['paymentTerms']);
                    //$("#est-termsAndCondition").val(data['termsAndCondition']);
                    //$("#est-deliveryTerms").val(data['deliveryTerms']);
                    //$("#est-validity").val(data['validity']);
                    $("#est-warranty").val(data['warranty']);
                    $("#est-discountView").val(data['showDiscountYN']).change();
                    //$("#est-exclusions").val(data['exclusions']);
                    $("#customerID").val(data['mfqCustomerAutoID']);
                    $("#est-currencyID").val(data['transactionCurrencyID']).change();
                    $("#est-currencyID").prop('disabled',false);
                    if(data['ciMasterID']>0)
                    {
                        $("#est-currencyID").prop('disabled',true);
                        $("#est-currencyID").val(data['currencyID']).change();

                    }else
                    {
                        $("#est-currencyID").prop('disabled',false);
                        $("#est-currencyID").val(data['currencyID']).change();

                    }

                    setTimeout(function () {
                        tinyMCE.get("est-termsAndCondition").setContent(data['termsAndCondition']);
                        tinyMCE.get("est-validity").setContent(data['validity']);
                        tinyMCE.get("est-deliveryTerms").setContent(data['deliveryTerms']);
                        tinyMCE.get("est-paymentTerms").setContent(data['paymentTerms']);
                        tinyMCE.get("est-exclusions").setContent(data['exclusions']);
                        tinyMCE.get("est-scopeOfWork").setContent(data['scopeOfWork']);
                        tinyMCE.get("est-technicalDetail").setContent(data['technicalDetail']);

                    }, 1000);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }

    function saveEstimate() {
        $("#est-currencyID").prop('disabled',false);
        tinymce.triggerSave();
        var data = $(".frm_estimate").serializeArray();

        data.push({'name': 'transactioncurrency', 'value': $('#currencyID option:selected').text()});
        $.ajax({
            url: "<?php echo site_url('MFQ_Estimate/save_Estimate'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    var result = $('#est-currencyID option:selected').text().split('|');
                    $('.currency').html('( ' + result[0] + ' )');
                    estimateMasterID = data[2];
                    $("#estimateMasterID").val(data[2]);

                    if (data[3]!='') {
                        $("#est-currencyID").prop('disabled',true);
                    } else
                    {
                        $("#est-currencyID").prop('disabled',false);
                    }
                    $("#customerID").val($('#est-mfqCustomerAutoID').val());
                    $('.btn-wizard').removeClass('disabled');
                    $('[href=#detail]').tab('show');
                    load_estimate_detail(data[2]);
                }else
                {
                    $("#est-currencyID").prop('disabled',true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function confirmEstimate() {
        //if($("#est-submissionStatus").val() == 4) {
        swal({
                title: "Are you sure?",
                text: "You want to confirm?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Estimate/confirm_Estimate'); ?>",
                    type: 'post',
                    data: {estimateMasterID: estimateMasterID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('.headerclose').trigger('click');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
        /*}else{
            myAlert('w','You can confirm if submission status is approved')
        }*/
    }

    function delete_estimateDetail(estimateDetailID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Estimate/delete_estimateDetail'); ?>",
                    type: 'post',
                    data: {estimateDetailID: estimateDetailID, estimateMasterID: estimateMasterID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            //$("#rowET_" + estimateDetailID).remove();
                            load_estimate_detail(estimateMasterID)
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function estimate_print() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: estimateMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#review").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function estimate_detail_modal() {
        if (estimateMasterID > 0) {
            $('.directCustomerInquiry').hide();
            $('.fromCustomerInquiry').show();
            load_customer_inquiry();
            $("#estimate_detail_modal").modal({backdrop: "static"});

        }
    }

    function load_customer_inquiry() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {mfqCustomerAutoID: $('#customerID').val(), estimateMasterID: estimateMasterID},
            url: "<?php echo site_url('MFQ_Estimate/fetch_customer_inquiry'); ?>",
            success: function (data) {
                $('#ciCode').empty();
                $('#table_body_ci_detail').html('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                var mySelect = $('#ciCode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        mySelect.append('<li><a onclick="fetch_customer_inquiry_detail(' + value['ciMasterID'] + ')">' + value['ciCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                    });
                } else {
                    mySelect.append('<li><a>No Records found</a></li>');
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_customer_inquiry_detail(ciMasterID) {
        if (ciMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ciMasterID': ciMasterID},
                url: "<?php echo site_url('MFQ_Estimate/load_mfq_customerInquiryDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.btnci').show();
                },
                success: function (data) {
                    $('#table_body_ci_detail').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#table_body_ci_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                        $('.directCustomerInquiry').show();
                        $('.fromCustomerInquiry').hide();
                        $('.btncid').show();
                        $('.btnci').hide();
                        init_customerInquiryDetailForm(ciMasterID);
                    } else {
                        $('.fromCustomerInquiry').show();
                        $('.directCustomerInquiry').hide();
                        $('.btncid').hide();
                        $('.btnci').show();
                        $.each(data, function (key, value) {
                            var itemSystemCode;
                            if (!value['itemSystemCode']) {
                                itemSystemCode = '' +
                                    '<div class="input-group">' +
                                    '<div class="input-group-addon" type="button" onclick="add_new_item('+x+')"><i class="fa fa-plus"></i></div>' +
                                    '<input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control ci_search" name="search[]" placeholder="Item ID, Item Description..." id="ci_search_' + x + '">' +
                                    '<div>';
                            } else {
                                itemSystemCode = value['itemSystemCode'];
                            }
                            $('#row_id').val(x);
                            $('#table_body_ci_detail').append('<tr><td>' + x + '</td><td>' + itemSystemCode + '</td><td>' + value['itemDescription'] + '</td><td >' + value['UnitDes'] + '</td><td>' + value['expectedQty'] + '</td><td>' + value['balanceQty'] + '</td><td><input type="text" class="number" size="10" id="" name="expectedQty[]" value="' + value['balanceQty'] + '"></td><td><input type="text" class="number estimatedCost" size="10" id="" name="estimatedCost[]" value="' + value["cost"] + '"></td><td><input type="checkbox" name="checked[]" value="1"><input type="hidden" class="mfqItemID" id="mfqItemID_' + x + '" name="mfqItemID[]" value="' + value["mfqItemID"] + '"><input type="hidden" name="ciMasterID[]" value="' + value["ciMasterID"] + '"><input type="hidden" name="ciDetailID[]" value="' + value["ciDetailID"] + '"><input type="hidden" name="bomMasterID[]" class="bomMasterID" value="' + value["bomMasterID"] + '"></td></tr>');
                            if (value['itemSystemCode'] === null) {
                                initializeCustomerInquiryDetailTypeahead2(x);
                            }
                            x++;
                        });
                    }
                    number_validation();
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function save_customer_inquiry_items() {
        //var data = $("#frm_customerInquiry").serializeArray();
        var values = [];
        var count = $("input[name='checked[]']:checked").length;
        if (count) {
            $.each($("input[name='checked[]']:checked"), function () {
                var data = $(this).parents('tr:eq(0)');
                var expectedQty = $(data).find("td:eq(6) input[name='expectedQty[]']").val();
                var estimatedCost = $(data).find("td:eq(7) input[name='estimatedCost[]']").val();
                var mfqItemID = $(data).find("td:eq(8) input[name='mfqItemID[]']").val();
                var ciMasterID = $(data).find("td:eq(8) input[name='ciMasterID[]']").val();
                var ciDetailID = $(data).find("td:eq(8) input[name='ciDetailID[]']").val();
                var bomMasterID = $(data).find("td:eq(8) input[name='bomMasterID[]']").val();
                values.push({name: 'expectedQty[]', value: expectedQty}, {
                        name: 'estimatedCost[]',
                        value: estimatedCost
                    },
                    {name: 'mfqItemID[]', value: mfqItemID}, {
                        name: 'ciMasterID[]',
                        value: ciMasterID
                    }, {name: 'ciDetailID[]', value: ciDetailID}, {name: 'bomMasterID[]', value: bomMasterID});
            });
            values.push({name: 'mfqCustomerAutoID', value: $("#customerID").val()});
            values.push({name: 'estimateMasterID', value: estimateMasterID});

            $.ajax({
                url: "<?php echo site_url('MFQ_Estimate/save_EstimateDetail'); ?>",
                type: 'post',
                data: values,
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#estimate_detail_modal").modal('hide');
                        load_estimate_detail(estimateMasterID);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                    myAlert('e', xhr.responseText);
                }
            });
        } else {
            myAlert('w', 'Please select an item');
        }
    }


    function save_customer_inquiry_direct_items() {
        var data = $("#frm_customerInquiryDirect").serializeArray();
        $.ajax({
            url: "<?php echo site_url('MFQ_Estimate/save_EstimateDetail'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#estimate_detail_modal").modal('hide');
                    load_estimate_detail(estimateMasterID);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function load_estimate_detail(estimateMasterID) {
        if (estimateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'estimateMasterID': estimateMasterID},
                url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#est-table_body').empty();
                    x = 1;
                    if ($.isEmptyObject(data)) {
                        $('.pricingFormulaHeader').text('Markup(%)');
                        $('#est-table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>');
                        $("#est-tot_qty").text("0.00");
                        $("#est-tot_unitCost").text("0.00");
                        $("#est-tot_totCost").text("0.00");
                        $("#est-tot_sellingPrice").text("0.00");
                        $("#est-tot_masterSellingPrice").text("0.00");
                        $("#est-tot_masterDiscountPrice").text("0.00");
                        $("#est-tot_actualMarginPrice").text("0.00");
                    } else {
                        if (data) {
                            var eqty = '';
                            $.each(data, function (key, value) {

                                var bomMasterID = value['bomMasterID'] ? 'Edit Bill of Material' : 'Add Bill of Material';

                                /**eqty :- td for qty th */
                                eqty = '<a href="#" data-type="text" data-placement="bottom"  id="itemdetailqty_'+value['estimateDetailID']+'"' +
                                        ' data-pk='+value['estimateDetailID']+'|'+value['estimatedCost']+'|'+value['margin']+'|'+value['sellingPrice']+'|'
                                        +value['sellingPrice']+'|'+value['discount']+'|'+value['actualMargin']+' data-name="expectedqty" data-title="Qty" class="xEditable itemdetailqty" ' +
                                        'data-value="" data-related="infectionOrDisease">'+value['expectedQty']+'</a>';
                                
                                var actualMargin = value['actualMargin'] ? value['actualMargin'] : 0;
                                var unitSellingPrice = value['unitSellingPrice'] ? value['unitSellingPrice'] : 0;
                                var allotedManHrs = value['allotedManHrs'] ? value['allotedManHrs'] : 0;

                                //**append table records dynamically */       
                                $('#est-table_body').append('<tr id="rowET_' + value['estimateDetailID'] + '"><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['UnitDes'] + '</td><td style="text-align: right">' + eqty + '</td><td style="text-align: right">' + commaSeparateNumber(value['estimatedCost'], value['transactionCurrencyDecimalPlaces']) + '</td><td style="text-align: right"><span id="totalcost_'+value['estimateDetailID'] +'">' + commaSeparateNumber(value['totalCost'], value['transactionCurrencyDecimalPlaces']) + '</td><td><input type="text" name="margin[]" placeholder="0" class="number marginPer" value="' + value['margin'] + '" onkeypress="return validateFloatKeyPress(this,event,5)" onkeyup="cal_item_line_total(this)" onfocus="this.select();" onchange="save_estimate_detail_margin(this,' + value['estimateDetailID'] + ')"> </td><td style="text-align: right"><input type="text" name="sellingPrice[]" id="sellingPrice_'+value['estimateDetailID']+'" placeholder="0" class="number sellingPrice" value="' + commaSeparateNumber(value['sellingPrice'], value['transactionCurrencyDecimalPlaces']) + '" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="cal_item_line_total_selling_price(this)" onfocus="this.select();" onchange="save_estimate_detail_selling_price(this,' + value['estimateDetailID'] + ')"></td><td><input type="text" name="discount[]" placeholder="0" class="number discountPer" value="' + value['discount'] + '" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="cal_item_line_total(this)" onfocus="this.select();" onchange="save_estimate_detail_discount(this,' + value['estimateDetailID'] + ')"> </td><td style="text-align: right"><span class="totDiscountPrice" id="totDiscountPrice_'+value['estimateDetailID']+'">' + commaSeparateNumber(value['discountedPrice'], value['transactionCurrencyDecimalPlaces']) + '</span><input type="hidden" name="discountedPrice" id="discountedPrice_'+value['estimateDetailID']+'" placeholder="0" class="discountedPrice" value="' + value['discountedPrice'] + '"> </td><td style="text-align: right"><span class="totactualMarginPrice" id="totactualMarginPrice_'+value['estimateDetailID']+'">' + commaSeparateNumber(actualMargin, value['transactionCurrencyDecimalPlaces']) + '</span><input type="hidden" name="actualMarginPrice" id="actualMarginPrice_'+value['estimateDetailID']+'" placeholder="0" class="actualMarginPrice" value="' + value['actualMargin'] + '"> </td><?php if($manufacturing_Flow == 'GCC'){ ?><td style="text-align: right"><input type="text" name="unit_SellingPrice" id="unit_SellingPrice_'+value['estimateDetailID']+'" placeholder="0" class="number unit_SellingPrice" value="' + commaSeparateNumber(unitSellingPrice, value['transactionCurrencyDecimalPlaces']) + '" onchange="save_unitSellingPrice(this,' + value['estimateDetailID'] + ')" ></td><?php } ?><?php if($manufacturing_Flow == 'Micoda'){ ?><td class="number" style="text-align: right;"><input type="text" name="allottedManhours" id="allottedManhours_'+value['estimateDetailID']+'" placeholder="0" class="number allottedManhours" value="' + allotedManHrs + '" onchange="save_allottedManhours(this,' + value['estimateDetailID'] + ')" ></td><?php } ?><td><a onclick="delete_estimateDetail(' + value['estimateDetailID'] + ')" title="Delete" rel="tooltip"><span style="color:red;" class="glyphicon glyphicon-trash"></span></a>&nbsp; | &nbsp;<a onclick="createBOM(\'system/mfq/mfq_add_new_bill_of_material\',' + value['bomMasterID'] + ',\'' + bomMasterID + '\',\'EST\',' + value["mfqItemID"] + ',' + value["estimateDetailID"] + ')" title="BOM" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>&nbsp; | &nbsp;<a onclick="update_mfq_erp_item(' + value["mfqItemID"] + ', ' + estimateMasterID + ')" title="Item Update" rel="tooltip"><i class="fa fa-refresh" aria-hidden="true"></i></a></td></tr>');
                                x++;
                                /**execute when changed the item quantity */
                                $(".itemdetailqty").editable({
                                    url: '<?php echo site_url('MFQ_Estimate/update_estimate_qty') ?>',
                                    send: 'always',
                                    ajaxOptions: {
                                        type: 'post',
                                        dataType: 'json',
                                        success: function (data) {
                                                myAlert(data[0], data[1]); //myAlert(data[0], data[1],data[2],data[3],data[4],data[5],data[6]);
                                            
                                            if( data[0] == 's'){
                                                var qty_xEditable = $('#itemdetailqty_' + data[3]); //var qty_xEditable = $('#itemdetailqty);
                                                
                                                setTimeout(function (){
                                                    qty_xEditable.attr('data-pk', qty_xEditable.html());
                                                    $('#totDiscountPrice_'+data[3]).html(commaSeparateNumber(data[4], value['transactionCurrencyDecimalPlaces']) );
                                                    $('#discountedPrice_'+data[3]).val(data[4]);
                                                    $('#sellingPrice_'+data[3]).val(data[6]);
                                                    $('#totalcost_'+data[3]).html(commaSeparateNumber(data[5], value['transactionCurrencyDecimalPlaces']) );
                                                    var actualMarginPrice = data[7] ? data[7] : 0.00;
                                                    $('#actualMarginPrice_'+data[3]).val(actualMarginPrice);
                                                    $('#totactualMarginPrice_'+data[3]).text(actualMarginPrice);
                                                    calculateItemTotal();
                                                    },400);

                                            }
                                        },
                                        error: function (xhr) {
                                            myAlert('e', xhr.responseText);
                                        }
                                    }
                                });
                                currency_decimal = value['transactionCurrencyDecimalPlaces'];
                                isFormulaChanged = value['isFormulaChanged'];
                                if(isFormulaChanged == 1){
                                    $('.pricingFormulaHeader').text('Margin(%)');
                                    $('.actual_pricingFormulaHeader').text('Actual Margin');
                                }else{
                                    $('.pricingFormulaHeader').text('Markup(%)');
                                    $('.actual_pricingFormulaHeader').text('Actual Markup');
                                }

                                <?php if($manufacturing_Flow == 'SOP'){ ?>
                                    var warrantyCost_val = value['warrantyCost']? value['warrantyCost'] : 0.00;
                                    var commision_val = value['commision']? value['commision'] : 0.00;
                                    $('#warrantyCost').val(commaSeparateNumber(warrantyCost_val, value['transactionCurrencyDecimalPlaces']));
                                    $('#commision').val(commaSeparateNumber(commision_val, value['transactionCurrencyDecimalPlaces']));
                                <?php } ?>
                            });
                            calculateItemTotal();


                        } else {
                            $("#est-tot_sellingPrice").text('0.00');
                        }
                    }

                    $("[rel=tooltip]").tooltip();
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function cal_item_line_total(element, estimatedDetailID) {
        var tot_totalCost_value = getNumberAndValidate($(element).closest('tr').find('td:eq(6)').text());
        var margin = getNumberAndValidate($(element).closest('tr').find('.marginPer').val());
        if(isFormulaChanged == 1) {
            $(element).closest('tr').find('.sellingPrice').val((tot_totalCost_value / (1-(margin/100))).toFixed(currency_decimal));
        } else {
            $(element).closest('tr').find('.sellingPrice').val((((tot_totalCost_value * margin) / 100) + tot_totalCost_value).toFixed(currency_decimal));
        }
        var tot_totalCost_value2 = $(element).closest('tr').find('.sellingPrice').val();
        var discount = getNumberAndValidate($(element).closest('tr').find('.discountPer').val());
        $(element).closest('tr').find('.totDiscountPrice').text(commaSeparateNumber((tot_totalCost_value2 - ((tot_totalCost_value2 * discount) / 100)).toFixed(currency_decimal)));
        $(element).closest('tr').find('.discountedPrice').val((tot_totalCost_value2 - ((tot_totalCost_value2 * discount) / 100)).toFixed(currency_decimal));
        $(element).closest('tr').find('.totactualMarginPrice').text(commaSeparateNumber(((tot_totalCost_value2 - ((tot_totalCost_value2 * discount) / 100)) - tot_totalCost_value).toFixed(currency_decimal)));
        
        calculateItemTotal();
    }

    function cal_item_line_total_selling_price(element, estimatedDetailID) {
        var tot_totalCost_value = getNumberAndValidate($(element).closest('tr').find('td:eq(6)').text());
        if(isFormulaChanged == 1) {
            var margin = ((1-(tot_totalCost_value / $(element).val()))*100).toFixed(currency_decimal);
        } else {
            var margin = (($(element).val() - tot_totalCost_value)/tot_totalCost_value) * 100;
        }
        $(element).closest('tr').find('.marginPer').val(margin);
        var tot_totalCost_value2 = $(element).val();
        var discount = getNumberAndValidate($(element).closest('tr').find('.discountPer').val());
        $(element).closest('tr').find('.totDiscountPrice').text(commaSeparateNumber((tot_totalCost_value2 - ((tot_totalCost_value2 * discount) / 100)).toFixed(currency_decimal)));
        $(element).closest('tr').find('.discountedPrice').val((tot_totalCost_value2 - ((tot_totalCost_value2 * discount) / 100)).toFixed(currency_decimal));

        calculateItemTotal();
    }


    function save_estimate_detail_margin(element, estimateDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateDetailID: estimateDetailID,
                sellingPrice: $(element).closest('tr').find('.sellingPrice').val(),
                discountedPrice: $(element).closest('tr').find('.discountedPrice').val(),
                margin: $(element).val()
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_margin'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], 6000);
                save_estimate_detail_margin_total();

                actual_Margin = $(element).closest('tr').find('.totactualMarginPrice').text();
                save_estimate_detail_actualMargin(element, estimateDetailID, actual_Margin);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_estimate_detail_discount(element, estimateDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateDetailID: estimateDetailID,
                discountedPrice: $(element).closest('tr').find('.discountedPrice').val(),
                discount: $(element).val()
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_discount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], 6000);
                save_estimate_detail_margin_total();

                actual_Margin = $(element).closest('tr').find('.totactualMarginPrice').text();
                save_estimate_detail_actualMargin(element, estimateDetailID, actual_Margin);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_estimate_detail_actualMargin(element, estimateDetailID, actual_Margin) {
        if(actual_Margin){
            actualMargin_price = actual_Margin;
        }else{
            actualMargin_price = $(element).closest('tr').find('.actualMarginPrice').val();
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateDetailID: estimateDetailID,
                //actualMargin: $(element).closest('tr').find('.actualMarginPrice').val(),
                actualMargin: actualMargin_price
                //actualMargin: $(element).closest('tr').find('.totactualMarginPrice').text();
                //find('.totactualMarginPrice').text
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_actualMargin'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
               // save_estimate_detail_margin_total();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_estimate_detail_selling_price(element, estimateDetailID) {
        var tot_totalCost_value = getNumberAndValidate($(element).closest('tr').find('td:eq(6)').text());
        var margin = (($(element).val() - tot_totalCost_value)/tot_totalCost_value) * 100;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateDetailID: estimateDetailID,
                sellingPrice: $(element).val(),
                margin:margin,
                discountedPrice: $(element).closest('tr').find('.discountedPrice').val(),
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_selling_price'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                save_estimate_detail_margin_total();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function save_estimate_detail_margin_total() {
        var totalSellingPrice = $('#est-tot_masterSellingPrice').text();
        var totalDiscountPrice = $('#est-tot_masterDiscountPrice').text();
        var totalActualMargin = $('#est-tot_actualMarginPrice').text();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                totalMargin: $("#est-marginPerTot").val(),
                estimateMasterID: estimateMasterID,
                totalSellingPrice: getNumberAndValidate(totalSellingPrice).toFixed(currency_decimal),
                totDiscountPrice: getNumberAndValidate(totalDiscountPrice).toFixed(currency_decimal),
                totActualMargin: getNumberAndValidate(totalActualMargin).toFixed(currency_decimal)
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_margin_total'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function save_estimate_detail_discount_total() {
        var totalDiscountPrice = $('#est-tot_masterDiscountPrice').text();
        var totalActualMargin = $('#est-tot_actualMarginPrice').text();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                totDiscount: $("#est-discountPerTot").val(),
                estimateMasterID: estimateMasterID,
                totDiscountPrice: getNumberAndValidate(totalDiscountPrice).toFixed(currency_decimal),
                totActualMargin: getNumberAndValidate(totalActualMargin).toFixed(currency_decimal)
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_discount_total'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function calculateItemTotal() {
        var tot_qty = 0;
        var tot_unitCost = 0;
        var tot_totalCost = 0;
        var tot_sellingPrice = 0;
        var tot_discountPrice = 0;
        $('#est-table_body tr').each(function () {
            var tot_qty_value = getNumberAndValidate($('td', this).eq(4).text());
            tot_qty += tot_qty_value;

            var tot_unitCost_value = getNumberAndValidate($('td', this).eq(5).text());
            tot_unitCost += tot_unitCost_value;

            var tot_totalCost_value = getNumberAndValidate($('td', this).eq(6).text());
            tot_totalCost += tot_totalCost_value;

            var tot_sellingPrice_value = getNumberAndValidate($('td', this).eq(8).find('.sellingPrice').val());
            tot_sellingPrice += tot_sellingPrice_value;

            var tot_discountPrice_value = parseFloat($('td', this).eq(10).find('.discountedPrice').val());
            tot_discountPrice += tot_discountPrice_value;
        });

        $("#est-tot_qty").text(tot_qty);
        $("#est-tot_unitCost").text(commaSeparateNumber(tot_unitCost, currency_decimal));
        $("#est-tot_totCost").text(commaSeparateNumber(tot_totalCost, currency_decimal));
        $("#est-tot_sellingPrice").text(commaSeparateNumber(tot_discountPrice, currency_decimal));
        if(isFormulaChanged == 1) {
            $("#est-tot_masterSellingPrice").text(commaSeparateNumber((tot_discountPrice/(1-($("#est-marginPerTot").val()/100))), currency_decimal));
        } else {
            $("#est-tot_masterSellingPrice").text(commaSeparateNumber((((tot_discountPrice * $("#est-marginPerTot").val()) / 100) + tot_discountPrice), currency_decimal));
        }
        var marginDiscountPrice = getNumberAndValidate($('#est-tot_masterSellingPrice').text());
        $("#est-tot_masterDiscountPrice").text(commaSeparateNumber((marginDiscountPrice - ((marginDiscountPrice * $("#est-discountPerTot").val()) / 100)), currency_decimal));

        //var totalSctualMarginPrice = commaSeparateNumber(($("#est-tot_masterDiscountPrice").text() - $("#est-tot_totCost").text()), currency_decimal) ? commaSeparateNumber(($("#est-tot_masterDiscountPrice").text() - $("#est-tot_totCost").text()), currency_decimal) : "0.00";
        var x = parseFloat($("#est-tot_masterDiscountPrice").text());
        var y = parseFloat($("#est-tot_totCost").text());
        var z = (x-y);
        $("#est-tot_actualMarginPrice").text(commaSeparateNumber(z,currency_decimal));
       
    }

    function getNumberAndValidate(thisVal, dPlace=currency_decimal) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        // thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    function validateFloatKeyPress(el, evt,currency_decimal=3) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');

        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function initializeCustomerInquiryDetailTypeahead(id) {
        $('#ci_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_CustomerInquiry/fetch_finish_goods/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#ci_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                    $('#ci_search_' + id).closest('tr').find('.uom').val(suggestion.uom);
                    // $('#ci_search_' + id).closest('tr').find('.estimatedCost').val(suggestion.cost);
                    $('#ci_search_' + id).closest('tr').find('.bomMasterID').val(suggestion.bomMasterID);
                }, 200);
                fetch_related_cost(suggestion.cost, this);
            },
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializeCustomerInquiryDetailTypeahead2(id) {
        $('#ci_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_CustomerInquiry/fetch_finish_goods/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#ci_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                    // $('#ci_search_' + id).closest('tr').find('.estimatedCost').val(suggestion.cost);
                    $('#ci_search_' + id).closest('tr').find('.bomMasterID').val(suggestion.bomMasterID);
                }, 200);
                fetch_related_cost(suggestion.cost, this);
            },
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function fetch_related_cost (cost, element) {
        if(cost > 0) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'estimateMasterID':estimateMasterID, 'cost':cost},
                url: "<?php echo site_url('MFQ_Estimate/estimate_item_cost'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.estimatedCost').val(data);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                }
            });
        } else {
            $(element).closest('tr').find('.estimatedCost').val(cost);
        }
    }

    function init_customerInquiryDetailForm(ciMasterID) {
        $('#table_body_ci_direct_detail').html('');
        $('#table_body_ci_direct_detail').append('<tr> ' +
            '<td>' +
            '<div class="input-group">' +
            '<div class="input-group-addon additem"  type="button" onclick="add_new_item(1)"><i class="fa fa-plus"></i></div>' +
            '<input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control ci_search" name="search[]" placeholder="Item ID, Item Description..." id="ci_search_1">' +
            '<input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control ciMasterID" name="ciMasterID[]" value="' + ciMasterID + '"> <input type="hidden" name="bomMasterID[]" value="" class="bomMasterID"> <input type="hidden" name="ciDetailID[]" value="" class="ciDetailID"></div></td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();"> </td> <td><input type="text" name="estimatedCost[]" id="estimatedCost" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number estimatedCost"> </td>   <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        number_validation();
        setTimeout(function () {
            initializeCustomerInquiryDetailTypeahead(1);
        }, 500);
    }

    function add_more_finish_goods() {

        search_id += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#tbl_customerInquiryDirect tbody tr:first').clone();
        appendData.find('.ci_search').attr('id', 'ci_search_' + search_id);
        appendData.find('.ci_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.additem').attr('onclick', 'add_new_item('+search_id+')');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#table_body_ci_direct_detail').append(appendData);
        var lenght = $('#tbl_customerInquiryDirect tbody tr').length - 1;

        number_validation();
        initializeCustomerInquiryDetailTypeahead(search_id);
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function createBOM(page_url, page_id, page_name, policy_id, data_arr, master_page_url=null) {
        var postData = {mfqItemID: data_arr, estimateDetailID: master_page_url};
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mfqItemID':data_arr},
            url: "<?php echo site_url('MFQ_Estimate/fetch_billofmaterialexist'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(data['billofmaterial'] == 2)
                {
                    fetch_page(page_url, data['pageID'], page_name, policy_id, data_arr, master_page_url);
                }else
                {
                    fetch_page(page_url, page_id, page_name, policy_id, data_arr, master_page_url);
                }

                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function fetch_page(page_url, page_id, page_name, policy_id, data_arr, master_page_url=null,postData)
    {
        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': page_id,
                'page_url': page_url,
                'page_name': page_name,
                'policy_id': policy_id,
                'data_arr': postData,
                'master_page_url': master_page_url
            },
            beforeSend: function () {
                startLoad();
            },
            success: function (page_html) {
                stopLoad();
                $('#bom_detail_modal').modal();
                $('#bomHeader').html(page_name);
                $('#bomContent').html(page_html);
                load_estimate_detail(estimateMasterID)
                $("html, body").animate({scrollTop: "0px"}, 10);
            },
            error: function (jqXHR, status, errorThrown) {
                stopLoad();
                $("html, body").animate({scrollTop: "0px"}, 10);
                $('#bomContent').html(jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
            }
        });
    }

    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                $('#attachmentDescription').val('');
                $('#remove_id').click();
                if (data['status']) {
                    load_attachments('EST', $("#documentSystemCode").val());
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
    function load_attachments(documentID, EstimateMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {documentID: documentID, documentSystemCode: EstimateMasterID},
            url: "<?php echo site_url('MFQ_CustomerInquiry/load_attachments'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_attachments').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function delete_attachment(id, myFileName) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {attachmentID: id, myFileName: myFileName},
                    url: "<?php echo site_url('Attachment/delete_attachments_AWS_s3'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            myAlert('s', 'Deleted Successfully');
                            load_attachments('EST', $('#documentSystemCode').val());
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function currency_validation(CurrencyID,documentID){
        if (CurrencyID) {
            currency_validation_modal(CurrencyID,documentID,'','');
        }
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function add_new_item(id)
    {
        $("#add_estimate_item_form")[0].reset();
        $('#row_id').val(id);
        $('#itemName').val('');
        $('#itemType').val('');
        $('#secondaryItemCode').val('');
        $('#mfqCategoryID').val('');
        $('#frm_subCategory').val('');
        $('#frm_subSubCategory').val('');
        $('#mainCategoryID').val(null).trigger('change');
        $('#subcategoryID').val(null).trigger('change');
        $('#subSubCategoryID').val(null).trigger('change');
        $('#defaultUnitOfMeasureID').val(null).trigger('change');
        $("#add_new_item_modal").modal({backdrop: "static"});

    }

    function update_mfq_erp_item(mfqItemID, estimateMasterID)
    {
            $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Estimate/validate_item_pulled"); ?>',
            dataType: 'json',
            data: {'mfqItemID': mfqItemID, 'estimateMasterID': estimateMasterID},
            async: false,
            success: function (data) {
                if (data['msg'] == 's') {
                    $('#linkedItemAutoID').val(data['itemAutoID']).change();
                    $('#mfqItemID_glUpdate').val(mfqItemID);
                    $("#update_linked_item_modal").modal({backdrop: "static"});
                } else {
                    myAlert('w', 'Item Already Pulled for Other Estimates!');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {}
        });
    }

    function update_revenue_gl()
    {
        var linkedItemAutoID = $('#linkedItemAutoID').val();
        var mfqItemID = $('#mfqItemID_glUpdate').val();
            $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Estimate/update_mfq_linked_item"); ?>',
            dataType: 'json',
            data: {'linkedItemAutoID': linkedItemAutoID, 'mfqItemID': mfqItemID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#update_linked_item_modal').modal('hide');
                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_default_note(docid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': docid},
            url: "<?php echo site_url('MFQ_Estimate/load_default_note'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                const noteTitle_Object = {
                    1: 'est-scopeOfWork',
                    2: 'est-technicalDetail',
                    3: 'est-exclusions',
                    4: 'est-paymentTerms',
                    5: 'est-termsAndCondition',
                    6: 'est-deliveryTerms',
                    7: 'est-validity'  
                };
                //tinyMCE.get("est-exclusions").setContent('hellow'); 
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        setTimeout(function () {
                             if (noteTitle_Object[value.typeID]) {
                                 tinyMCE.get(noteTitle_Object[value.typeID]).setContent(value.description);
                             }
                            // else{
                                // tinyMCE.get(noteTitle_Object[value.typeID]).setContent('');
                            // }
                        }, 300);
                    });
                }
            }, error: function () {
                stopLoad();
            }
        });
    }
    
    function save_estimate_detail_warranty_cost(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                warrantyCost: getNumberAndValidate($('#warrantyCost').val()).toFixed(currency_decimal),
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_warranty_cost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],3000);
               
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        }); 
    }

    function save_estimate_detail_commision(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                commision: getNumberAndValidate($('#commision').val()).toFixed(currency_decimal),
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/save_estimate_detail_commision'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],3000);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        }); 
    }

    function save_allottedManhours(element, estimateDetailID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                allotedManHrs: $(element).val(),
                estimateDetailID: estimateDetailID
            },
            url: "<?php echo site_url('MFQ_Estimate/save_allottedManhours'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],3000);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_unitSellingPrice(element, estimateDetailID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                unitSellingPrice: getNumberAndValidate($(element).val()).toFixed(currency_decimal),
                estimateDetailID: estimateDetailID
            },
            url: "<?php echo site_url('MFQ_Estimate/save_unitSellingPrice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],3000);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>
