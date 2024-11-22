<?php echo head_page($_POST['page_name'], FALSE);
  $this->load->helper('operation_ngo_helper');
  $date_format_policy = date_format_policy();
  $current_date       = current_format_date();
  $segment_arr        = fetch_segment();
  $supplier_arr       = all_supplier_drop();

  $currency_arr         = all_currency_new_drop();//array('' => 'Select Currency');
  $location_arr         = all_delivery_location_drop();
  $location_arr_default = default_delivery_location_drop();
  $financeyear_arr      = all_financeyear_drop(TRUE);
  $umo_arr              = array('' => 'Select UOM');
  $gl_code_arr          = fetch_all_gl_codes();
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

    .bootstrap-datetimepicker-widget {
        z-index: 100000000;
    !important;

    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Donor Collection Header</a>
    <!--    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details()" data-toggle="tab">Step 2 - Dispatch
            Note
            Detail</a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_addon_cost()" data-toggle="tab">Step 3 - Dispatch
            Note
            Addon Cost</a>-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab">Step 2 -
        Donor Collection
        Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
      <?php echo form_open('', 'role="form" id="dispatchNote_header_form"'); ?>
        <input type="hidden" name="modeOfPayment" id="modeOfPayment" value="">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>DONOR COLLECTION HEADER</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Document Date</label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input onchange="" type="text" name="documentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Reference No<label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="referenceNo"

                                 value="" id="referenceNo" class="form-control">

                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title">Donor</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <select onchange="fetchdonorcurrency(this)" id="donorsID" class="form-control select2"
                                    name="donorsID">
                                <option data-currency="" value="">Select Donor</option>
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
                        <label class="title">Currency</label>
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
                        <label class="title">Financial Year</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('financeyear', $financeyear_arr,
                          $this->common_data['company_data']['companyFinanceYearID'],
                          'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                             <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Financial Period</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                     <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '',
                       'class="form-control" id="financeyear_period" required'); ?>
                             <span class="input-req-inner"></span></span>
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Bank or Cash</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                      <?php echo form_dropdown('DCbankCode', company_bank_account_drop(), '',
                        'class="form-control select2" id="DCbankCode" onchange="set_payment_method()" required'); ?>
                             <span class="input-req-inner"></span></span>
                    </div>


                    <div class="form-group col-sm-2">
                        <label class="title">Narration</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea id="narration" name="narration" class="form-control"></textarea>

                    </div>

                </div>

                <div class="row paymentmoad" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Cheque Number</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="DCchequeNo" id="DCchequeNo" class="form-control">
                        <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Cheque Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="DCchequeDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="DCchequeDate"
                                   class="form-control" required>
                            <span class="input-req-inner"></span></span>
                        </div>


                    </div>


                </div>


                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button id="save_btn" class="btn btn-primary pull-right" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>


        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>CASH DETAILS</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="rv_detail_modal()">
                            <i class="fa fa-plus"></i> Add Cash
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="load_donor_collection_cash"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>ITEM DETAILS</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="rv_item_detail_modal()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="collection_item_detail"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>

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
            <button class="btn btn-default prev">Previous</button>
            <button class="btn btn-primary " onclick="save_draft()">Save as Draft</button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
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
                <h5 class="modal-title">Add Item Detail</h5>
            </div>
            <form role="form" id="rv_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="item_add_table">
                        <thead>
                        <tr>
                            <th style="width: 150px">Project <?php required_mark(); ?></th>
                            <th style="width: 150px">Commitment <?php required_mark(); ?></th>
                            <th style="width: 350px">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px">Warehouse <?php required_mark(); ?></th>


                            <th>UOM <?php required_mark(); ?></th>
                            <th style="width: 80px">Qty <?php required_mark(); ?></th>
                            <th>Amount <span class="currency">
                                    </span> <?php required_mark(); ?></th>
                            <th style="">Description</th>
                            <!--<th style="">Expiry Date</th>-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="">

                              <?php echo form_dropdown('projectID[]', fetch_project_donor_drop(), '',
                                'class="form-control projectID" onchange="getallcommitments(this)" required'); ?>
                            </td>

                            <td>
                              <?php echo form_dropdown('commitmentAutoId[]', array('' => ''), '',
                                'class="form-control commitmentDrop select2" onchange="clearitem(this)" required'); ?>
                            </td>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search" name="search[]" id="f_search_1"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td>
                              <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '',
                                'class="form-control select2"  required'); ?>
                            </td>



                            <td>
                              <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each',
                                'class="form-control umoDropdown" disabled required'); ?>
                            </td>
                            <td>
                                <input type="text" name="quantityRequested[]"
                                       placeholder="0.00" class="form-control number" required>
                            </td>
                            <td>
                                <input type="text" name="estimatedAmount[]"
                                       placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number estimatedAmount">
                            </td>
                            <td style="">
                                <input type="text" name="description[]"
                                       placeholder="Description" class="form-control " required>
                            </td>
                            <!--  <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate[]"
                                           data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>-->
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="saveRvItemDetail()">Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="rv_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Cash</h5>
            </div>
            <form role="form" id="rv_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="income_add_table">
                        <thead>
                        <tr>
                            <th>Project <?php required_mark(); ?></th>
                            <th style="width: 200px">Commitment </th>
                            <th>Description</th>




                            <th>Amount <span
                                        class="currency"> </span><?php required_mark(); ?></th>

                            <!-- <th>Expiry Date</th>-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_income()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                              <?php echo form_dropdown('projectID[]', fetch_project_donor_drop(), '',
                                'class="form-control projectID" onchange="getallcommitmentsDetails(this)"  required'); ?>
                            </td>
                            <td>
                              <?php echo form_dropdown('commitmentDetailAutoID[]', array('' => ''), '',
                                'class="form-control commitmentDrop select2" required'); ?>
                            </td>
                            <td style=""><input type="text" name="description[]" placeholder="Description"
                                                class="form-control " required></td>





                            <td>
                                <input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number amount" id="amount">
                            </td>

                            <!--<td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate[]"
                                           data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>-->
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="saveDirectRvDetails()">Save changes
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
                <h4 class="modal-title">Edit Item Detail</h4>
            </div>
            <form role="form" id="edit_rv_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="item_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 150px">Project <?php required_mark(); ?></th>
                            <th style="width: 150px">Commitment </th>
                            <th style="width: 350px">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px">Warehouse <?php required_mark(); ?></th>


                            <th>UOM <?php required_mark(); ?></th>
                            <th style="width: 80px">Qty <?php required_mark(); ?></th>
                            <th>Amount <span class="currency">
                                    </span> <?php required_mark(); ?></th>
                            <th style="">Description</th>
                            <!--<th style="">Expiry Date</th>-->

                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                            <td>
                              <?php echo form_dropdown('projectID', fetch_project_donor_drop(), '',
                                'class="form-control" id="edit_projectID"  onchange="getallcommitments(this)"  required'); ?>
                            </td>
                            <td>
                              <?php echo form_dropdown('commitmentAutoId', array('' => ''), '',
                                'class="form-control commitmentDrop select2" onchange="clearitem(this)" id="edit2_commitmentAutoId" required'); ?>
                            </td>

                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control"
                                       name="search" id="search" placeholder="Item ID, Item Description...">
                                <input type="hidden" class="form-control" name="itemAutoID"
                                       id="edit_itemAutoID">
                            </td>
                            <td>
                              <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '',
                                'class="form-control select2" id="edit_wareHouseAutoID"  required'); ?>
                            </td>



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
                            <!-- <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate" id="edit_commitmentExpiryDate"
                                           data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>-->
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="update_Rv_ItemDetail()">Update
                        changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="edit_rv_income_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Cash</h4>
            </div>
            <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                        <thead>
                        <tr>

                            <th>Project <?php required_mark(); ?></th>
                            <th style="width: 200px">Commitment </th>
                            <th>Description</th>



                            <th>Amount <span
                                        class="currency"> </span><?php required_mark(); ?></th>

                            <!-- <th>Expiry Date</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                              <?php echo form_dropdown('projectID', fetch_project_donor_drop(), '',
                                'class="form-control" id="edit2_projectID" onchange="getallcommitmentsDetails(this)" required'); ?>
                            </td>
                            <td class="">
                              <?php echo form_dropdown('commitmentDetailAutoID', array('' => ''), '',
                                'class="form-control commitmentDrop select2"  id="edit_commitmentAutoId" required'); ?>
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

                            <!-- <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expiryDate" id="edit2_commitmentExpiryDate"
                                           data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>-->
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="updateDirectRvDetails()">Update
                        changes
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
    var collectionAutoId;
    var search_id = 1;
    var currency_decimal = 1;

    var collectionDetailAutoID;
    $(document).ready(function () {
        $(".paymentmoad").hide();
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/donor_collections', '', 'Donor Collections')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {


            $(".usetwentyfour").css("z-index: 100000000; !important;");
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        collectionDetailAutoID = null;
        documentCurrency = null;
        collectionAutoId = null;


        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            collectionAutoId = p_id;
            load_collectionHeader();
            fetch_collection_item_details(collectionAutoId);
            load_donor_collection_cash(collectionAutoId);
            load_confirmation();
            collectiondetailsexist();
            $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + collectionAutoId);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + collectionAutoId + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');

        }
        initializeitemTypeahead_edit();

        number_validation();
        currency_decimal = 2;

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#dispatchNote_header_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: 'documentDate is required.'}}},
                donorsID: {validators: {notEmpty: {message: 'donors is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                financeyear: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
                financeyear_period: {validators: {notEmpty: {message: 'Financial Period is required.'}}},
                DCbankCode: {validators: {notEmpty: {message: 'Bank or Cash is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            data.push({'name': 'bank', 'value': $('#DCbankCode option:selected').text()});
            data.push({'name': 'collectionAutoId', 'value': collectionAutoId});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_donorCollections'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.addTableView').removeClass('hide');
                        fetch_collection_item_details(data[2]);
                        load_donor_collection_cash(data[2]);
                        collectionAutoId = data[2];
                        collectiondetailsexist();
                        $('#save_btn').html('Update');
                        $('.btn-wizard').removeClass('disabled');
                        /*  $('[href=#step2]').tab('show');*/

                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
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


    });

    function clearitem(id) {
        $(id).closest('td').nextAll().find('input').val('').change();
        $(id).closest('td').nextAll().find('select').val('').change();
    }



    function editgetallcommitments(projectID, thes, selectedvalue) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectID': projectID,'collectionAutoId':collectionAutoId},
            url: "<?php echo site_url('OperationNgo/getallcommitments'); ?>",
            success: function (data) {
                $(thes).closest('tr').find('.commitmentDrop').empty();
                var mySelect = $(thes).closest('tr').find('.commitmentDrop');
                mySelect.append($('<option>sd</option>').val('').html('Please Select'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['commitmentAutoId']).html(text['documentSystemCode']));
                    });


                }
                if (selectedvalue) {
                    $(thes).closest('tr').find('.commitmentDrop').val(selectedvalue).change();
                }
                $('#edit2_commitmentAutoId').attr('onchange','clearitem(this)');

            }, error: function () {

            }
        });
    }

    function getallcommitmentsDetails(thes,selectedvalue){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectID': thes.value,'collectionAutoId':collectionAutoId},
            url: "<?php echo site_url('OperationNgo/getallcommitmentsDetails'); ?>",
            success: function (data) {
                $(thes).closest('tr').find('.commitmentDrop').empty();
                var mySelect = $(thes).closest('tr').find('.commitmentDrop');
                mySelect.append($('<option></option>').val('').html('Please Select'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['ID']).html(text['description']));
                    });
                }
                if (selectedvalue) {
                    $(thes).closest('tr').find('.commitmentDrop').val(selectedvalue).change();
                }


            }, error: function () {

            }
        });
    }

    function editgetallcommitmentsDetails(projectID, thes, selectedvalue) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectID': projectID,'collectionAutoId':collectionAutoId},
            url: "<?php echo site_url('OperationNgo/getallcommitmentsDetails'); ?>",
            success: function (data) {
                $(thes).closest('tr').find('.commitmentDrop').empty();
                var mySelect = $(thes).closest('tr').find('.commitmentDrop');
                mySelect.append($('<option>sd</option>').val('').html('Please Select'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['ID']).html(text['description']));
                    });


                }

                if (selectedvalue) {
                    $(thes).closest('tr').find('.commitmentDrop').val(selectedvalue).change();
                }


            }, error: function () {

            }
        });
    }


    function getallcommitments(thes, selectedvalue) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectID': thes.value,'collectionAutoId':collectionAutoId},
            url: "<?php echo site_url('OperationNgo/getallcommitments'); ?>",
            success: function (data) {
                $(thes).closest('tr').find('.commitmentDrop').empty();
                var mySelect = $(thes).closest('tr').find('.commitmentDrop');
                mySelect.append($('<option></option>').val('').html('Please Select'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['commitmentAutoId']).html(text['documentSystemCode']));
                    });


                }
                if (selectedvalue) {
                    $(thes).closest('tr').find('.commitmentDrop').val(selectedvalue).change();
                }


            }, error: function () {

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
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function set_payment_method() {
        val = $('#DCbankCode option:selected').text();
        res = val.split(" | ");
        if (res[5] == 'Cash') {
            $('#modeOfPayment').val(1);
            $(".paymentmoad").hide();
        } else {
            $('#modeOfPayment').val(2);
            $(".paymentmoad").show();
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function fetchdonorcurrency(thes) {
        currencyID = $('#donorsID  option:selected').attr('data-currency');
        $('#transactionCurrencyID').val(currencyID).change();

    }

    function updateDirectRvDetails() {
        var data = $('#edit_rv_income_detail_form').serializeArray();
        data.push({'name': 'collectionAutoId', 'value': collectionAutoId});
        data.push({'name': 'collectionDetailAutoID', 'value': collectionDetailAutoID});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_collection_cash_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    collectionDetailAutoID = null;
                    load_donor_collection_cash(collectionAutoId);
                    $('#edit_rv_income_detail_modal').modal('hide');
                    $('#edit_rv_income_detail_form')[0].reset();
                    $('.select2').select2('')

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function collectiondetailsexist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'collectionAutoId': collectionAutoId},
            url: "<?php echo site_url('OperationNgo/collectiondetailsexist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                   // $("#donorsID").attr('disabled', 'disabled');
                    //$("#transactionCurrencyID").attr('disabled', 'disabled');
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

        if (collectionAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'collectionDetailAutoID': id},
                        url: "<?php echo site_url('OperationNgo/fetch_donor_collection_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            collectionDetailAutoID = data['collectionDetailAutoID'];

                            $('#edit2_projectID').val(data['projectID']);

        editgetallcommitmentsDetails(data['projectID'], '#edit2_projectID', data['commitmentAutoID']+' | '+data['commitmentDetailID']);

                            $('#edit2_description').val(data['description']);
                            $('#edit_amount').val(data['transactionAmount']);
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
        $('#edit2_commitmentAutoId').removeAttr('onchange');
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'collectionDetailAutoID': id},
                    url: "<?php echo site_url('OperationNgo/fetch_donor_collection_item_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        //pv_item_detail_modal();

                        collectionDetailAutoID = data['collectionDetailAutoID'];
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
                        $("#edit_rv_item_detail_modal").modal({backdrop: "static"});
                        editgetallcommitments(data['projectID'], '#edit_projectID', data['commitmentAutoID']);
                        // $('#edit2_commitmentAutoId').val(data['commitmentAutoID']).change();


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
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#income_add_table tbody tr:first').clone();

        appendData.find('input,select,textarea').val('');
        appendData.find("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#income_add_table").append(appendData);
        $(".select2").select2();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
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
        var lenght = $('#item_add_table tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializeitemTypeahead(search_id);
        initializeitemTypeahead_edit();
        Inputmask().mask(document.querySelectorAll("input"));
    }
     function getextraparameter(id){
         return $('#f_search_' + id).closest('tr').find('.commitmentDrop').val();
    }
    function getprojectID(id){
           return $('#f_search_' + id).closest('tr').find('select[name="projectID[]"] option:selected').val();
    }
    function initializeitemTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>OperationNgo/fetch_itemrecode_donor',
            params: {
                'commitmentID': function() {
                    return getextraparameter(id);
                }, 'projectID': function() {
                    return getprojectID(id);
                },'collectionAutoId':collectionAutoId
            },
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                // fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
            }
        });

    }

   function  editgetextraparameter(){
            return $('#edit2_commitmentAutoId').val();
    }

    function editgetprojectID(){
       return $('#edit_projectID').val();
    }


    function initializeitemTypeahead_edit() {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>OperationNgo/fetch_itemrecode_donor/',
            params: {
                'commitmentID': function() {
                    return editgetextraparameter();
                },'projectID': function() {
                    return editgetprojectID();
                },'collectionAutoId':collectionAutoId
            },
            onSelect: function (suggestion) {
                setTimeout(function () {




                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                //  fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
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
                id: collectionAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_ngo_donorcollectionmaster',
                primaryKey: 'collectionAutoId'
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
                id: collectionAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_ngo_donorcollectionmaster',
                primaryKey: 'collectionAutoId'
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
        if (collectionAutoId) {
            $('.search').typeahead('destroy');
            $("#wareHouseAutoID").val(null).trigger("change");
            $('#rv_item_detail_form')[0].reset();
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(1);
            $('.select2').select2('');
            $('#item_add_table tbody tr').not(':first').remove();
            $("#rv_item_detail_modal").modal({backdrop: "static"});
            $("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());
            $('#rv_item_detail_form .commitmentDrop').empty();
        }
    }

    function rv_detail_modal() {
        if (collectionAutoId) {
            $('#income_add_table .commitmentDrop').empty();
            $("#gl_code").val(null).trigger("change");
            $('#rv_detail_form')[0].reset();
            $("#rv_detail_modal").modal({backdrop: "static"});
            $('#income_add_table tbody tr').not(':first').remove();
            $("input[name='expiryDate[]']").val($('#commitmentExpiryDate').val());
        }
    }


    function load_collectionHeader() {
        if (collectionAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'collectionAutoId': collectionAutoId},
                url: "<?php echo site_url('OperationNgo/load_collectionHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        collectionAutoId = data['collectionAutoId'];
                        $(".paymentmoad").hide();
                        if (data['modeOfPayment'] == 2) {
                            $('#modeOfPayment').val(2);
                            $(".paymentmoad").show();
                            $('#DCchequeDate').val(data['DCchequeDate']);
                            $('#DCchequeNo').val(data['DCchequeNo']);
                        }
                        $('#documentDate').val(data['documentDate']);

                        $("#donorsID").val(data['donorsID']).change();

                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        $('#referenceNo').val(data['referenceNo']);

                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);


                        $('#narration').val(data['narration']);

                        $('#referanceNo').val(data['referanceNo']);
                        $('#DCbankCode').val(data['DCbankCode']).change();


                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        $('#save_btn').html('Update');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate();
            r.moveEnd('character', o.value.length);
            if (r.text == '') return o.value.length;
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function saveDirectRvDetails() {
        var data = $('#rv_detail_form').serializeArray();
        data.push({'name': 'collectionAutoId', 'value': collectionAutoId});
        /*    data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});*/
        $('select[name="gl_code[]"] option:selected').each(function () {

            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        });


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/save_donor_collection_cash_detail'); ?>",
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
                        load_donor_collection_cash(collectionAutoId);
                        collectiondetailsexist();
                    }, 300);
                    $('#rv_detail_modal').modal('hide');
                    $('#rv_detail_form')[0].reset();
                    $('.select2').select2('')
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
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
        if (collectionAutoId) {
            data.push({'name': 'collectionAutoId', 'value': collectionAutoId});
            data.push({'name': 'collectionDetailAutoID', 'value': collectionDetailAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/update_collection_itemDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        collectionDetailAutoID = null;
                        setTimeout(function () {
                            fetch_collection_item_details(collectionAutoId);
                        }, 300);
                        $('#edit_rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });
        }
    }


    function delete_commitmentDetails(id, type) {
        if (collectionAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'collectionDetailAutoID': id},
                        url: "<?php echo site_url('OperationNgo/delete_collectionDetail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (type == 1) {
                                fetch_collection_item_details(collectionAutoId);
                            } else {
                                load_donor_collection_cash(collectionAutoId);

                            }
                            collectiondetailsexist();


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
        if (collectionAutoId) {
            data.push({'name': 'collectionAutoId', 'value': collectionAutoId});
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
                url: "<?php echo site_url('OperationNgo/save_donor_collection_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {

                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_collection_item_details(collectionAutoId);
                            collectiondetailsexist();
                        }, 300);
                        $('#rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });
        }
    }

    function load_donor_collection_cash(collectionAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {collectionAutoId: collectionAutoId},
            url: "<?php echo site_url('OperationNgo/load_donor_collection_cash'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_donor_collection_cash').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function fetch_collection_item_details(collectionAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {collectionAutoId: collectionAutoId},
            url: "<?php echo site_url('OperationNgo/load_collection_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#collection_item_detail').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_draft() {
        if (collectionAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/operationNgo/donor_collections', '', 'Donor Collections')
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
        if (collectionAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'collectionAutoId': collectionAutoId, 'html': true},
                url: "<?php echo site_url('operationNgo/load_donor_collection_confirmation'); ?>",
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
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }


    function confirmation() {
        if (collectionAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want confirm this document!",
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
                        data: {'collectionAutoId': collectionAutoId},
                        url: "<?php echo site_url('operationNgo/donor_collection_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data[0] == 'e') {
                                myAlert(data[0], data[1]);
                            } else if(data[0] == 'w')
                            {
                                myAlert(data[0], data[1]);
                            }
                            else {
                                myAlert(data[0], data[1]);
                                fetchPage('system/operationNgo/donor_collections', '', 'Donor Collections');
                                refreshNotifications(true);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
</script>
