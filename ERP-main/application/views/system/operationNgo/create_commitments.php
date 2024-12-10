<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


echo head_page($_POST['page_name'], FALSE);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$supplier_arr = all_supplier_drop();

$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(TRUE);
$umo_arr = array('' => 'Select UOM');
$gl_code_arr = fetch_all_gl_codes();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .ui-datepicker {
        z-index: 99999;
    !important;
    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('operationngo_step_one'); ?><!--Step 1 -->-
        <?php echo $this->lang->line('operationngo_donor_commitment_header'); ?><!--Donor Commitment Header--></a>
    <!--    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details()" data-toggle="tab">Step 2 - Dispatch
            Note
            Detail</a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_addon_cost()" data-toggle="tab">Step 3 - Dispatch
            Note
            Addon Cost</a>-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab">
        <?php echo $this->lang->line('operationngo_step_two'); ?><!--Step 2--> -
        <?php echo $this->lang->line('operationngo_donor_commitment_cofirmation'); ?><!--Donor Commitment Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="dispatchNote_header_form"'); ?>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('operationngo_donor_commitments_header'); ?><!--DONOR COMMITMENTS HEADER--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input onchange="$('#commitmentExpiryDate').val(this.value);" type="text" name="documentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_expiry_date'); ?><!--Expiry Date--></label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="commitmentExpiryDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="commitmentExpiryDate" class="form-control">
                </div>

                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('operationngo_donor'); ?><!--Donor--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <select onchange="fetchdonorcurrency(this)" id="donorsID" class="form-control select2"
                                    name="donorsID">
                                <option data-currency=""
                                        value=""><?php echo $this->lang->line('operationngo_select_donor'); ?><!--Select Donor--></option>
                                <?php

                                $donor_drop = fetch_contact_donor_drop();
                                if (!empty($donor_drop)) {
                                    foreach ($donor_drop as $val) {
                                        ?>
                                        <option value="<?php echo $val['contactID'] ?>"
                                                data-currency="<?php echo $val['currencyID'] ?>"><?php echo $val['name'] ?></option>
                                        <?php

                                    }
                                }
                                ?>
                            </select>

                            <span class="input-req-inner"></span></span>

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
               <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '',
                   'class="form-control select2" id="transactionCurrencyID"  required'); ?>
                                <span class="input-req-inner"></span></span>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_ref_no'); ?><!--Reference No--><label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="referenceNo"

                                 value="" id="referenceNo" class="form-control">

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('common_narration'); ?><!--Narration--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea id="narration" name="narration" class="form-control"></textarea>

                    </div>

                </div>


                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button id="save_btn" class="btn btn-primary pull-right" type="submit">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>
        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('operationngo_cash_details'); ?><!--CASH DETAILS--></h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="rv_detail_modal()">
                            <i class="fa fa-plus"></i>
                            <?php echo $this->lang->line('operationngo_add_cash'); ?><!--Add Cash-->
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="dispatchDetial_addonCost"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('operationngo_item_details'); ?><!--ITEM DETAILS--></h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="rv_item_detail_modal()">
                            <i class="fa fa-plus"></i>
                            <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="dispatchDetial_item"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>

    </div>
    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <!--        <div id="conform_body_attachement">
                    <h4 class="modal-title" id="purchaseOrder_attachment_label">Modal title</h4>
                    <br>
                    <div class="table-responsive" style="width: 60%">
                        <table class="table table-striped table-condensed table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>File Name</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="purchaseOrder_attachment" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">No Attachment Found</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        -->
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="rv_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('operationngo_add_item_detail'); ?><!--Add Item Detail--></h5>
            </div>
            <form role="form" id="rv_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="item_add_table">
                        <thead>
                        <tr>
                            <th>
                                <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                            <th>
                                <?php echo $this->lang->line('operationngo_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <!--    <th>Warehouse <?php /*required_mark(); */ ?></th>-->


                            <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency">
                                    </span> <?php required_mark(); ?></th>
                            <th style=""><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="">
                                <?php echo $this->lang->line('operationngo_expiry_date'); ?><!--Expiry Date--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                            <td>

                                <?php echo form_dropdown('projectID[]', fetch_project_donor_drop(), '', 'class="form-control project" onchange="validatetb_row(this)"   required'); ?>
                            </td>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search" name="search[]" id="f_search_1"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?> , <?php echo $this->lang->line('common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <!--    <td>
                              <?php /*echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '',
                                'class="form-control select2"  required'); */ ?>
                            </td>-->


                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each',
                                    'class="form-control umoDropdown" disabled required'); ?>
                            </td>
                            <td>
                                <input type="text" name="quantityRequested[]"
                                       onkeyup="validatetb_row(this)"
                                       placeholder="0.00" class="form-control quantityRequested number" required>
                            </td>
                            <td>
                                <input type="text" name="estimatedAmount[]"
                                       placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)"
                                       onkeyup="validatetb_row(this)"
                                       class="form-control number estimatedAmount">
                            </td>
                            <td style="">
                                <input type="text" name="description[]"
                                       placeholder="Description" class="form-control " required>
                            </td>
                            <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="saveRvItemDetail()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="rv_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('operationngo_add_cash'); ?><!--Add Cash--></h5>
            </div>
            <form role="form" id="rv_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="income_add_table">
                        <thead>
                        <tr>
                            <th>
                                <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>

                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"> </span><?php required_mark(); ?></th>

                            <th><?php echo $this->lang->line('operationngo_expiry_date'); ?><!--Expiry Date--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_income()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>  <?php echo form_dropdown('projectID[]', fetch_project_donor_drop(), '',
                                    'class="form-control"  required'); ?>
                            </td>
                            <td style=""><input type="text" name="description[]" placeholder="Description"
                                                class="form-control " required></td>


                            <td>
                                <input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)"
                                       value="00"
                                       class="form-control number">
                            </td>

                            <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="saveDirectRvDetails()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="edit_rv_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('operationngo_edit_item_detail'); ?><!--Edit Item Detail--></h4>
            </div>
            <form role="form" id="edit_rv_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="item_edit_table">
                        <thead>
                        <tr>
                            <th>
                                <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                            <th>
                                <?php echo $this->lang->line('operationngo_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <!--   <th>Ware House <?php /*required_mark(); */ ?></th>-->


                            <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"></span> <?php required_mark(); ?></th>
                            <th style=""><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="">
                                <?php echo $this->lang->line('operationngo_expiry_date'); ?><!--Expiry Date--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('projectID', fetch_project_donor_drop(), '',
                                    'class="form-control" id="edit_projectID" required'); ?>
                            </td>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control"
                                       name="search" id="search"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control" name="itemAutoID"
                                       id="edit_itemAutoID">
                            </td>
                            <!--    <td>
                              <?php /*echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '',
                                'class="form-control select2" id="edit_wareHouseAutoID"  required'); */ ?>
                            </td>-->


                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each',
                                    'class="form-control" disabled required id="edit_UnitOfMeasureID"'); ?>
                            </td>
                            <td>
                                <input type="text" name="quantityRequested"
                                       placeholder="0.00" class="form-control number"
                                       id="edit_quantityRequested" required>
                            </td>
                            <td>
                                <input type="text" name="estimatedAmount"
                                       placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number" id="edit_estimatedAmount">
                            </td>
                            <td style=""><input type="text" name="description" id="edit_description"
                                                placeholder="Description" class="form-control " required></td>
                            <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate" id="edit_commitmentExpiryDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="update_Rv_ItemDetail()">
                        <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="edit_rv_income_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('operationngo_edit_cash'); ?><!--Edit Cash--></h4>
            </div>
            <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                        <thead>
                        <tr>
                            <th>
                                <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>

                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"> </span><?php required_mark(); ?></th>

                            <th><?php echo $this->lang->line('operationngo_expiry_date'); ?><!--Expiry Date--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('projectID', fetch_project_donor_drop(), '',
                                    'class="form-control" id="edit2_projectID" required'); ?>
                            </td>
                            <td>
                                        <textarea class="form-control" rows="1" name="description"
                                                  id="edit2_description"></textarea>
                            </td>


                            <td>
                                <input type="text" name="amount" onkeypress="return validateFloatKeyPress(this,event)"
                                       value="00" id="edit_amount"
                                       class="form-control number">
                            </td>

                            <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate" id="edit2_commitmentExpiryDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="updateDirectRvDetails()">
                        <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $('#save_btn').html('Save');
    $('.addTableView').removeClass('hide');
    var documentCurrency;
    var commitmentAutoId;
    var search_id = 1;
    var currency_decimal = 1;
    var batchMasterID;
    var dispatchDetailsID;
    var commitmentDetailAutoID;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/donor_commitments', '', 'Donor Commitments');
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        commitmentDetailAutoID = null;
        documentCurrency = null;
        commitmentAutoId = null;
        dispatchDetailsID = null;


        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            commitmentAutoId = p_id;
            load_commitmentHeader();
            fetch_rv_details(commitmentAutoId);
            getDispatchDetailAddonCost_tableView(commitmentAutoId);
            load_confirmation();
            commitmentdetailsexist();
            $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + commitmentAutoId);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + commitmentAutoId + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');

        }
        initializeitemTypeahead_edit();

        number_validation();
        currency_decimal = 2;

        $('#dispatchNote_header_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_document_date_is_required');?>.'}}}, /*documentDate is required*/
                donorsID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_donors_is_required');?>.'}}}, /*donors is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}/*Currency is required*/

            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});

            data.push({'name': 'commitmentAutoId', 'value': commitmentAutoId});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_commitments'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.addTableView').removeClass('hide');
                        fetch_rv_details(data[2]);
                        getDispatchDetailAddonCost_tableView(data[2]);
                        commitmentAutoId = data[2];
                        commitmentdetailsexist();
                        $('#save_btn').html('Update');
                        $('#save_btn').html('Update');
                        $('.btn-wizard').removeClass('disabled');
                        /*  $('[href=#step2]').tab('show');*/
                        $('#save_btn').removeAttr('disabled');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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


    });
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function fetchdonorcurrency(thes) {
        currencyID = $('#donorsID  option:selected').attr('data-currency');
        $('#transactionCurrencyID').val(currencyID).change();

    }

    function updateDirectRvDetails() {
        var data = $('#edit_rv_income_detail_form').serializeArray();
        data.push({'name': 'commitmentAutoId', 'value': commitmentAutoId});
        data.push({'name': 'commitmentDetailAutoID', 'value': commitmentDetailAutoID});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_commitment_cash_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    commitmentDetailAutoID = null;
                    getDispatchDetailAddonCost_tableView(commitmentAutoId);
                    $('#edit_rv_income_detail_modal').modal('hide');
                    $('#edit_rv_income_detail_form')[0].reset();
                    $('.select2').select2('')

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function commitmentdetailsexist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'commitmentAutoId': commitmentAutoId},
            url: "<?php echo site_url('OperationNgo/commitmentdetailsexist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                    $("#donorsID").attr('disabled', 'disabled');
                    $("#transactionCurrencyID").attr('disabled', 'disabled');
                } else {
                    $("#donorsID").removeAttr('disabled');
                    $("#transactionCurrencyID").removeAttr('disabled');
                }
                stopLoad();
                //refreshNotifications(true);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }


    function edit_income_item(id) {
        if (commitmentAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'commitmentDetailAutoID': id},
                        url: "<?php echo site_url('OperationNgo/fetch_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            commitmentDetailAutoID = data['commitmentDetailAutoID'];

                            $('#edit2_projectID').val(data['projectID']).change();
                            $('#edit_gl_code').val(data['GLAutoID']).change();
                            $('#edit2_description').val(data['description']);
                            $('#edit_amount').val(data['transactionAmount']);
                            $('#edit2_commitmentExpiryDate').val(data['commitmentExpiryDate']);
                            $("#edit_rv_income_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                            //refreshNotifications(true);
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }


    function edit_item(id) {
        $('#edit_rv_item_detail_form')[0].reset();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'commitmentDetailAutoID': id},
                    url: "<?php echo site_url('OperationNgo/fetch_item_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        //pv_item_detail_modal();

                        commitmentDetailAutoID = data['commitmentDetailAutoID'];
                        $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_projectID').val(data['projectID']);
                        $('#edit_quantityRequested').val(data['itemQty']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                        $('#edit_search_id').val(data['itemSystemCode']);
                        $('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_description').val(data['description']);
                        $('#edit_commitmentExpiryDate').val(data['commitmentExpiryDate']);
                        $("#edit_rv_item_detail_modal").modal({backdrop: "static"});

                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function fetch_related_uom_id_edit(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#edit_UnitOfMeasureID').empty();
                var mySelect = $('#edit_UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#edit_UnitOfMeasureID').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }


    function add_more_income() {
        $('select.select2').select2('destroy');
        var appendData = $('#income_add_table tbody tr:first').clone();

        appendData.find('input,select,textarea').val('');
        appendData.find("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#income_add_table").append(appendData);
        $(".select2").select2();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });
    }

    function add_more_item() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        //appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        var lenght = $('#item_add_table tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializeitemTypeahead(search_id);
        initializeitemTypeahead_edit();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });
    }

    function initializeitemTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>OperationNgo/fetch_itemrecode_donor/',
            onSelect: function (suggestion) {
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                // fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                $(this).closest('tr').css("background-color", 'white');
            }
        });
    }

    function initializeitemTypeahead_edit() {
        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>OperationNgo/fetch_itemrecode_donor/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                // fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
            }
        });
    }

    function fetch_related_uom_id_edit(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#edit_UnitOfMeasureID').empty();
                var mySelect = $('#edit_UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#edit_UnitOfMeasureID').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_sales_price_edit(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: commitmentAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_ngo_commitmentmasters',
                primaryKey: 'commitmentAutoId'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $('#edit_estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }


    function fetch_sales_price(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: commitmentAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_ngo_commitmentmasters',
                primaryKey: 'commitmentAutoId'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                /*var mySelect = $('#UnitOfMeasureID');*/
                //var mySelect = $(element).closest('tr').find('input[name="UnitOfMeasureID"]')
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value)
                        /*$("#UnitOfMeasureID").val(select_value);*/
                        /*$('#invoice_item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');*/
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function rv_item_detail_modal() {
        if (commitmentAutoId) {
            $('.search').typeahead('destroy');
            $("#wareHouseAutoID").val(null).trigger("change");
            $('#rv_item_detail_form')[0].reset();
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(1);
            $('.select2').select2('');
            $('#item_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.quantityRequested').closest('tr').css("background-color", 'white');
            $('.estimatedAmount').closest('tr').css("background-color", 'white');
            $('.project').closest('tr').css("background-color", 'white');
            $('.estimatedAmount').closest('tr').css("background-color", 'white');
            $("#rv_item_detail_modal").modal({backdrop: "static"});
            $("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());
        }
    }

    function rv_detail_modal() {
        if (commitmentAutoId) {
            $("#gl_code").val(null).trigger("change");
            $('#rv_detail_form')[0].reset();
            $("#rv_detail_modal").modal({backdrop: "static"});
            $('#income_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color", 'white');
            $("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());
        }
    }


    function load_commitmentHeader() {
        if (commitmentAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'commitmentAutoId': commitmentAutoId},
                url: "<?php echo site_url('OperationNgo/load_commitmentHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        commitmentAutoId = data['commitmentAutoId'];

                        $('#documentDate').val(data['documentDate']);
                        $('#commitmentExpiryDate').val(data['commitmentExpiryDate']);
                        $('#narration').val(data['narration']);
                        $("#donorsID").val(data['donorsID']).change();

                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        $('#referenceNo').val(data['referenceNo']);

                        /* getDispatchDetailItem_tableView(data['commitmentAutoId']);
                         getDispatchDetailAddonCost_tableView(data['commitmentAutoId']);
                         load_confirmation();*/
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        $('#save_btn').html('<?php echo $this->lang->line('common_update');?>');
                        /*Update*/
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function saveDirectRvDetails() {
        var data = $('#rv_detail_form').serializeArray();
        data.push({'name': 'commitmentAutoId', 'value': commitmentAutoId});
        /*    data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});*/


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/save_donor_cash_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                /*receiptVoucherDetailAutoID = null;*/
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    setTimeout(function () {
                        getDispatchDetailAddonCost_tableView(commitmentAutoId);
                        commitmentdetailsexist();
                    }, 300);
                    $('#rv_detail_modal').modal('hide');
                    $('#rv_detail_form')[0].reset();
                    $('.select2').select2('')
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#edit_itemAutoID').val('');
        }

    }

    function update_Rv_ItemDetail() {
        $('#edit_UnitOfMeasureID').prop("disabled", false);
        var data = $('#edit_rv_item_detail_form').serializeArray();
        if (commitmentAutoId) {
            data.push({'name': 'commitmentAutoId', 'value': commitmentAutoId});
            data.push({'name': 'commitmentDetailAutoID', 'value': commitmentDetailAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/update_commitment_itemDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        commitmentDetailAutoID = null;
                        setTimeout(function () {
                            fetch_rv_details(commitmentAutoId);
                        }, 300);
                        $('#edit_rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });
        }
    }


    function delete_commitmentDetails(id, type) {
        if (commitmentAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'commitmentDetailAutoID': id},
                        url: "<?php echo site_url('OperationNgo/delete_commitmentDetail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (type == 1) {
                                fetch_rv_details(commitmentAutoId);
                            } else {
                                getDispatchDetailAddonCost_tableView(commitmentAutoId);

                            }
                            commitmentdetailsexist();


                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }


    function saveRvItemDetail() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#rv_item_detail_form').serializeArray();
        if (commitmentAutoId) {
            data.push({'name': 'commitmentAutoId', 'value': commitmentAutoId});
            /* data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});*/
            $('select[name="wareHouseAutoID[]"] option:selected').each(function () {
                data.push({'name': 'wareHouse[]', 'value': $(this).text()})
            });

            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_donor_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {

                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_rv_details(commitmentAutoId);
                            commitmentdetailsexist();
                        }, 300);
                        $('#rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });
        }
    }

    function getDispatchDetailAddonCost_tableView(commitmentAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {commitmentAutoId: commitmentAutoId},
            url: "<?php echo site_url('OperationNgo/load_commitment_cash_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dispatchDetial_addonCost').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function fetch_rv_details(commitmentAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {commitmentAutoId: commitmentAutoId},
            url: "<?php echo site_url('OperationNgo/load_commitment_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dispatchDetial_item').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_draft() {
        if (commitmentAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/operationNgo/donor_commitments', '', 'Donor Commitments');
                });
        }
    }

    function validateFloatKeyPress(el, evt) {
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


    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function load_confirmation() {
        if (commitmentAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'commitmentAutoId': commitmentAutoId, 'html': true},
                url: "<?php echo site_url('operationNgo/load_donor_commitment_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    //$("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + dispatchAutoID);
                    //$("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + dispatchAutoID + '/GRV');
                    /*attachment_modal_dispatchNote(dispatchAutoID, "Dispatch Note", "DPN");*/
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }


    function confirmation() {
        if (commitmentAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('operationngo_you_want_to_confirm_this_doc');?>", /*You want confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'commitmentAutoId': commitmentAutoId},
                        url: "<?php echo site_url('operationNgo/donor_commitment_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data[0] == 'e') {
                                myAlert(data[0], data[1]);
                            } else {
                                myAlert(data[0], data[1]);
                                fetchPage('system/operationNgo/donor_commitments', '', 'Donor Commitments');
                                refreshNotifications(true);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
</script>
