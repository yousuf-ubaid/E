<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('communityngo', $primaryLanguage);

$this->load->helper('community_ngo_helper');

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();

$type_arr = array('' => 'Select Type', 'Standard' => 'Standard');
$currency_arr = all_currency_new_drop();
$umo_arr = array('' => $this->lang->line('common_select_uom')/*'Select UOM'*/);

?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
    <script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/lib/moment.min.js'); ?>"></script>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
            <?php echo $this->lang->line('communityngo_item_request_header'); ?><!--Item Request Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_pqr_detail_table()" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?><!--Step--> 2 -
            <?php echo $this->lang->line('communityngo_item_request_detail'); ?><!--Item Request Detail--></a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?><!--Step--> 3 -
            <?php echo $this->lang->line('communityngo_item_request_confirmation'); ?><!--Item Request Confirmation--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="item_request_form"'); ?>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">
                        <?php echo $this->lang->line('communityngo__issueDate'); ?><!--ITEM Issue Date--> <?php required_mark(); ?></label>

                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="issueDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="issueDate" class="form-control" required>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="segment">
                        <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="SalesPersonName">
                        <?php echo $this->lang->line('common_name'); ?><!--Name--> <?php required_mark(); ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="requestedMemberName" name="requestedMemberName"
                               required>
                        <input type="hidden" class="form-control" id="requestedMemberID" name="requestedMemberID">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_member_details()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Employee" rel="tooltip"
                                onclick="link_member_modal()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="form-group col-sm-4">
                    <label for="transactionCurrencyID">
                        <?php echo $this->lang->line('common_currency'); ?><!--Currency--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'PO\')" required'); ?>
                </div>
                <div class="col-sm-4">
                    <label>
                        <?php echo $this->lang->line('communityngo_return_date'); ?><!--Return Date--> <?php required_mark(); ?></label>

                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="expectedReturnDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="expectedReturnDate" class="form-control"
                               required>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label for="narration">
                            <?php echo $this->lang->line('communityngo_item_narration'); ?><!--Narration--> <?php required_mark(); ?></label>
                        <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                    </div>
                </div>

            </div>

            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary" type="submit">
                    <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
            </div>
            </form>
        </div>

        <div id="step2" class="tab-pane">
            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>
                        <?php echo $this->lang->line('communityngo_ItemDetails'); ?><!--Item Detail--> </h4>
                    <h4></h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="item_issue_detail_modal()" class="btn btn-primary pull-right">
                        <i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                    </button>
                </div>
            </div>
            <br>
            <table class="table table-bordered table-striped table-condesed">
                <thead>
                <tr>
                    <th colspan="7">
                        <?php echo $this->lang->line('communityngo_item_details'); ?><!--Item Details--></th>
                    <th colspan="2"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                            class="currency">(LKR)</span>
                    </th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                    <th style="min-width: 25%" class="text-left">
                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                    <th style="min-width: 10%" class="text-left">Expected
                        <?php echo $this->lang->line('communityngo_return_date'); ?><!--Expected Return Date--></th>
                    <th style="min-width: 10%">
                        <?php echo $this->lang->line('communityngo_item_no_of_days'); ?><!--Unit--></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td colspan="10" class="text-center"><b>
                            <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                </tr>
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
            <br>
            <hr>
            <div class="text-right m-t-xs">
                <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
                <button class="btn btn-primary next" onclick="load_conformation();">
                    <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
            </div>
        </div>

        <div id="step3" class="tab-pane">
            <div id="conform_body"></div>
            <hr>
            <!-- <div id="conform_body_attachement">
                <h4 class="modal-title" id="purchaseOrder_attachment_label">Modal title</h4>
                <br>

                <div class="table-responsive" style="width: 60%">
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php /*echo $this->lang->line('common_file_name'); */ ?></th>
                            <th><?php /*echo $this->lang->line('common_description'); */ ?></th>
                            <th><?php /*echo $this->lang->line('common_type'); */ ?></th>
                            <th><?php /*echo $this->lang->line('common_action'); */ ?></th>
                        </tr>
                        </thead>
                        <tbody id="purchaseOrder_attachment" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center">
                                <?php /*echo $this->lang->line('common_no_attachment_found'); */ ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
  <hr>-->

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
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div aria-hidden="true" role="dialog" id="item_issue_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('communityngo_add_item_details'); ?><!--Add Item Detail--></h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="purchase_request_detail_form" class="form-horizontal">
                        <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                            <thead>
                            <tr>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_return_date'); ?><!--Expected Return Date--> <?php required_mark(); ?></th>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_item_no_of_days'); ?><!--No of Days--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('communityngo_current_stock'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_qty'); ?><!--PO Qty--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--> <span
                                        class="currency"> (LKR)</span></th>
                                <th style="width: 100px;" class="hidden">
                                    <?php echo $this->lang->line('communityngo_net_unit_cost'); ?><!--Net Unit Cost--></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>

                                    <select id="rentalItemID" class="form-control select2 rentalItemID"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_ItemType'); ?>"
                                            name="rentalItemID[]" onchange="get_itemIDs(this)">
                                        <option value=""></option>
                                        <?php
                                        $item_drop = fetch_rental_item_issue();
                                        if (!empty($item_drop)) {
                                            foreach ($item_drop as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['rentalItemID'] ?>"><?php echo $val['rentalItemCode'] . ' | ' . $val['rentalItemDes'] ?></option>
                                                <?php

                                            }
                                        }
                                        ?>
                                    </select>

                                    <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                    <input type="hidden" class="form-control faID" name="faID[]">
                                    <input type="hidden" class="form-control rentalItemType" name="rentalItemType[]">
                                    <input type="hidden" class="form-control PeriodTypeID" name="PeriodTypeID[]">
                                </td>

                                <td><input type="text" name="expectedReturnDateDetail[]"
                                           onkeyup="deliverydate_val(this)"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="expectedReturnDateDetail"
                                           class="form-control deliverydat" required readonly></td>

                                <td><input type="text" name="no_of_days[]" id="no_of_days"
                                           class="form-control no_of_days" required disabled></td>

                                <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown UnitOfMeasureID"  required disabled'); ?></td>

                                <td><input type="text" name="currentStock[]" value="0"
                                           class="form-control number currentStock" disabled>
                                </td>
                                <td><input type="text" name="quantityRequested[]" value="0" onkeyup="change_qty(this)"
                                           class="form-control number quantityRequested" onfocus="this.select();"
                                           required>
                                </td>
                                <td><input type="text" name="estimatedAmount[]" value="0" placeholder="0.00"
                                           onkeyup="change_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number estimatedAmount" onfocus="this.select();"></td>

                                <td class="hidden">&nbsp;<span class="net_unit_cost pull-right "
                                                               style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>
                                <td>&nbsp;<span class="net_amount pull-right"
                                                style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>
                                <td><textarea class="form-control" rows="1" name="comment[]"
                                              placeholder="<?php echo $this->lang->line('communityngo_item_comment'); ?>..."></textarea>
                                </td><!--Item Comment-->
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="save_item_issue_details()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" tabindex="-1" id="item_issue_detail_edit_mod" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('communityngo_edit_item_details'); ?><!--Edit Item Detail--></h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="purchase_request_detail_edit_form" class="form-horizontal">
                        <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                            <thead>
                            <tr>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_return_date'); ?><!--Expected Return Date--> <?php required_mark(); ?></th>
                                <th style="width: 200px;">
                                    <?php echo $this->lang->line('communityngo_item_no_of_days'); ?><!--No of Days--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('communityngo_current_stock'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_qty'); ?><!--PO Qty--> <?php required_mark(); ?></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--> <span
                                        class="currency"> (LKR)</span></th>
                                <th style="width: 100px;" class="hidden">
                                    <?php echo $this->lang->line('communityngo_net_unit_cost'); ?><!--Net Unit Cost--></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                                <th style="width: 150px;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>

                                    <select id="rentalItemID_edit" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_ItemType'); ?>"
                                            name="rentalItemID" onchange="get_itemIDs_edit(this)">
                                        <option value=""></option>
                                        <?php
                                        $item_drop = fetch_rental_item_issue();
                                        if (!empty($item_drop)) {
                                            foreach ($item_drop as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['rentalItemID'] ?>"><?php echo $val['rentalItemCode'] . ' | ' . $val['rentalItemDes'] ?></option>
                                                <?php

                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" id="itemAutoID_edit" class="form-control" name="itemAutoID">
                                    <input type="hidden" id="faID_edit" class="form-control" name="faID">
                                    <input type="hidden" id="rentalItemType_edit" class="form-control"
                                           name="rentalItemType">
                                    <input type="hidden" id="PeriodTypeID_edit" class="form-control"
                                           name="PeriodTypeID">
                                </td>

                                <td><input type="text" name="expectedReturnDateDetail"
                                           onkeyup="deliverydate_val_edit(this)"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="expectedReturnDateDetailEdit"
                                           class="form-control deliverydat" required readonly></td>

                                <td><input type="text" name="no_of_daysEdit" id="no_of_daysEdit"
                                           class="form-control no_of_daysEdit" required disabled></td>

                                <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control uomdrop" id="UnitOfMeasureID_edit"  required disabled'); ?></td>

                                <td><input type="text" name="currentStock" id="currentStock_edit" value="0"
                                           class="form-control currentStock_edit" disabled></td>

                                <td><input type="text" name="quantityRequested" id="quantityRequested_edit" value="0"
                                           onkeyup="change_qty_edit()" class="form-control quantityRequested_edit"
                                           required onfocus="this.select();"></td>

                                <td><input type="text" name="estimatedAmount" value="0" id="estimatedAmount_edit"
                                           placeholder="0.00" onkeyup="change_amount_edit()"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control estimatedAmount_edit" onfocus="this.select();"></td>

                                <td class="hidden">&nbsp;<span id="net_unit_cost_edit" class="pull-right"
                                                               style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>
                                <td>&nbsp;<span id="totalAmount_edit" class="net_amount_edit pull-right"
                                                style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>
                                <td><textarea class="form-control" rows="1" id="comment_edit" name="comment"
                                              placeholder="<?php echo $this->lang->line('communityngo_item_comment'); ?>..."></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="update_item_issue_details()">
                        <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="member_modal" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalLabel">
                        <?php echo $this->lang->line('communityngo_link_member'); ?></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('communityngo_name_of_member'); ?><!--Memeber--></label>
                            <div class="col-sm-7">
                                <?php
                                $employee_arr = all_member_drop();
                                echo form_dropdown('Com_MasterID', $employee_arr, '', 'class="form-control select2" id="Com_MasterID" required'); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="fetch_member_details()">
                        <?php echo $this->lang->line('communityngo_add_member'); ?><!--Add employee--></button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var search_id = 1;
        var itemAutoID;
        var faID;
        var rentalItemType;
        var itemIssueAutoID;
        var itemIssueDetailAutoID;
        var currency_decimal;
        var documentCurrency;
        var deliverydat;
        var issuedat;
        var no_of_days;

        $(document).ready(function () {


            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_rental_items', itemIssueAutoID, 'Rental Items');
            });

            $('.select2').select2();
            itemIssueAutoID = null;
            itemIssueDetailAutoID = null;
            itemAutoID = null;
            faID = null;
            rentalItemType = null;
            currency_decimal = 2;
            documentCurrency = null;
            number_validation();

            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#item_request_form').bootstrapValidator('revalidateField', 'issueDate');
                $('#item_request_form').bootstrapValidator('revalidateField', 'expectedReturnDate');
            });
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

            if (p_id) {
                itemIssueAutoID = p_id;
                laad_pqr_header();
                $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + itemIssueAutoID);
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.btn-wizard').addClass('disabled');
            }


            $('#item_request_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    requestedMemberName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}}, /*Name is required*/
                    transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_supplier_currency_is_required');?>.'}}}, /*Supplier Currency is required*/
                    expectedReturnDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_return_date_required');?>.'}}}, /*Return Date is required*/
                    issueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_issue_date_required');?>.'}}}, /*PRQ Date is required*/
                    narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_narration_required');?>.'}}}, /*Narration is required*/
                    segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#segment").prop("disabled", false);
                $("#transactionCurrencyID").prop("disabled", false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
                data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CommunityNgo/save_item_request_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        var result = $('#transactionCurrencyID option:selected').text().split('|');
                        $('.currency').html('( ' + result[0] + ' )');
                        if (data['status']) {
                            $('.btn-wizard').removeClass('disabled');
                            itemIssueAutoID = data['last_id'];
                            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + itemIssueAutoID);
                            $("#segment").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $('[href=#step2]').tab('show');
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


        function fetch_pqr_detail_table() {
            if (itemIssueAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemIssueAutoID': itemIssueAutoID},
                    url: "<?php echo site_url('CommunityNgo/fetch_item_req_detail_table'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                        $('#table_body').empty();
                        $('#table_tfoot').empty();
                        x = 1;
                        if (jQuery.isEmptyObject(data['detail'])) {
                            $("#segment").prop("disabled", false);
                            $("#transactionCurrencyID").prop("disabled", false);

                            $('#table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                            <!--No Records Found-->
                        } else {
                            $("#segment").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            tot_amount = 0;
                            currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];

                            $.each(data['detail'], function (key, value) {
                                $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['expectedReturnDate'] + '</td><td class="text-center">' + value['no_of_days'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['itemIssueDetailAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['itemIssueDetailAutoID'] + ',\'' + value['itemDescription'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                                x++;
                                tot_amount += parseFloat(value['totalAmount']);
                            });
                            $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            }
        }


        function laad_pqr_header() {
            if (itemIssueAutoID) {
                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'itemIssueAutoID': itemIssueAutoID},
                        url: "<?php echo site_url('CommunityNgo/load_item_request_header'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {
                                fetch_pqr_detail_table();

                                $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                                $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                                documentCurrency = data['transactionCurrencyID'];
                                $('#expectedReturnDate').val(data['expectedReturnDate']);
                                $('#issueDate').val(data['issueDate']);
                                $('#narration').val(data['narration']);
                                $('#requestedMemberName').val(data['requestedMemberName']);
                                if (data['requestedMemberID'] > 0) {
                                    $('#requestedMemberName').prop('readonly', true);
                                    $('#requestedMemberID').val(data['requestedMemberID']);
                                }

                                $('[href=#step2]').tab('show');
                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                $('a[data-toggle="tab"]').addClass('btn-default');
                                $('[href=#step2]').removeClass('btn-default');
                                $('[href=#step2]').addClass('btn-primary');
                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                )
                ;
            }
        }


        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        function item_issue_detail_modal() {
            if (itemIssueAutoID) {

                itemIssueDetailAutoID = null;
                $('#purchase_request_detail_form')[0].reset();

                $('#po_detail_add_table tbody tr').not(':first').remove();
                $('.net_amount,.net_unit_cost').text('0.00');

                $('.itemAutoID').val('');
                $('.faID').val('');
                $('.rentalItemType').val('');
                $('.rentalItemID').val('').change();

                fetchExpectedDate();
                Inputmask().mask(document.querySelectorAll("input"));
                $("#item_issue_detail_modal").modal({backdrop: "static"});
                $('.rentalItemID').closest('tr').css("background-color", 'white');
                $('.deliverydat').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');

            }
        }

        function load_conformation() {
            if (itemIssueAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'itemIssueAutoID': itemIssueAutoID, 'html': true},
                    url: "<?php echo site_url('CommunityNgo/load_item_issue_conformation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#conform_body').html(data);
                        stopLoad();
                        refreshNotifications(true);
                        /*Purchase Request*/
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
            if (itemIssueAutoID) {
                swal({

                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55 ",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'itemIssueAutoID': itemIssueAutoID},
                            url: "<?php echo site_url('CommunityNgo/rental_issue_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    fetchPage('system/CommunityNgo/ngo_hi_rental_items', itemIssueAutoID, 'Rental Items');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function save_draft() {
            if (itemIssueAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                        text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        fetchPage('system/CommunityNgo/ngo_hi_rental_items', itemIssueAutoID, 'Rental Items');
                    });
            }
        }

        function currency_validation(CurrencyID, documentID) {
            if (CurrencyID) {
                partyAutoID = $('#supplierPrimaryCode').val();
                currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
            }
        }

        function delete_item(id, value) {
            if (itemIssueAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                        text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55 ",
                        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'itemIssueDetailAutoID': id},
                            url: "<?php echo site_url('CommunityNgo/delete_item_issue_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                fetch_pqr_detail_table();
                                stopLoad();
                                refreshNotifications(true);
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function edit_item(id, value) {
            if (itemIssueAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                        text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_edit');?>"/*Edit*/
                    },
                    function () {
                        $('#po_detail_add_table tbody tr').not(':first').remove();
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'itemIssueDetailAutoID': id},
                            url: "<?php echo site_url('CommunityNgo/fetch_item_issue_detail_edit'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {

                                var totAmount = parseFloat(data['totalAmount']);
                                var unitAmount = parseFloat(data['unitAmount']);
                                itemIssueDetailAutoID = data['itemIssueDetailAutoID'];

                                $('#rentalItemID_edit').val(data['rentalItemID']).change();

                                if(data['rentalItemID_edit'] == 0){
                                    $('#rentalItemID_edit option:not(:selected)').prop('disabled', false);
                                }else{
                                    $('#rentalItemID_edit option:not(:selected)').prop('disabled', true);
                                }

                                $('#itemAutoID_edit').val(data['itemAutoID']);
                                $('#faID_edit').val(data['faID']);
                                $('#rentalItemType_edit').val(data['rentalItemType']);
                                $('#PeriodTypeID_edit').val(data['rentalItemType']);
                                $('#expectedReturnDateDetailEdit').val(data['expectedReturnDate']);
                                $('#no_of_daysEdit').val(data['no_of_days']);
                                $('#quantityRequested_edit').val(data['requestedQty']);
                                $('#estimatedAmount_edit').val(data['unitAmount']);
                                //  $('#net_unit_cost_edit').text((unitAmount).formatMoney(2, '.', ','));
                                // $('#totalAmount_edit').text((totAmount).formatMoney(2, '.', ','));
                                $('#comment_edit').val(data['comment']);
                                issuedat = data['issueDate'];

                                var qut = data['requestedQty'];
                                var amount = data['unitAmount'];
                                var days = data['no_of_days'];

                                if (qut == null || qut == 0) {
                                    $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut)).formatMoney(2, '.', ','));
                                    $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                } else {
                                    $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                }

                                $("#item_issue_detail_edit_mod").modal({backdrop: "static"});
                                stopLoad();
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });
            }
        }


        function change_amount(element) {

            net_amount(element);
        }

        function change_qty(element) {

            var currentStock = $(element).closest('tr').find('.currentStock').val();
            if (element.value > parseFloat(currentStock)) {
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
                $(element).val(0);
                $(element).closest('tr').find('.net_amount').text('0.00');
            } else {
                net_amount(element);
            }

            if (element.value > 0) {
                $(element).closest('tr').css("background-color", 'white');
            }
        }

        function net_amount(element) {
            var qut = $(element).closest('tr').find('.quantityRequested').val();
            var amount = $(element).closest('tr').find('.estimatedAmount').val();
            var days = $(element).closest('tr').find('.no_of_days').val();

            if (qut == null || qut == 0) {
               /* $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));*/
                $(element).closest('tr').find('.net_amount').text('0.00');
                $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));

                //   $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0.00');
            } else {
                $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            }
        }


        function change_qty_edit() {

            var currentStock = $('#currentStock_edit').val();
            var qut = $('#quantityRequested_edit').val();

            if (qut > parseFloat(currentStock)) {
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
                $('#quantityRequested_edit').val(0);
            } else {
                net_amount_edit();
            }


        }

        function change_amount_edit() {
            net_amount_edit();
        }

        function net_amount_edit() {
            var qut = $('#quantityRequested_edit').val();
            var amount = $('#estimatedAmount_edit').val();
            var days = $('#no_of_daysEdit').val();

            if (qut == null || qut == 0) {
                /*$('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut)).formatMoney(2, '.', ','));*/
                $('#totalAmount_edit').text('0.00');
                $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            } else {
                $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            }
        }

        function add_more() {
            search_id += 1;

            $('select.select2').select2('destroy');
            var appendData = $('#po_detail_add_table tbody tr:first').clone();
            appendData.find('.umoDropdown,.item_text').empty();
            appendData.find('.net_amount,.net_unit_cost').text('0.00');
            appendData.find('.rentalItemID').attr('id', 'rentalItemID' + search_id);
            appendData.find('input').val('');
            appendData.find('.deliverydat').val(deliverydat);
            appendData.find('.no_of_days').val(no_of_days);
            appendData.find('textarea').val('');
            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
            $('#po_detail_add_table').append(appendData);
            var lenght = $('#po_detail_add_table tbody tr').length - 1;
            $('#rentalItemID' + search_id).closest('tr').css("background-color", 'white');

            $(".select2").select2();
            number_validation();

        }

        function save_item_issue_details() {
            var data = $('#purchase_request_detail_form').serializeArray();
            if (itemIssueAutoID) {
                data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
                data.push({'name': 'itemIssueDetailAutoID', 'value': itemIssueDetailAutoID});

                $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                    data.push({'name': 'uom[]', 'value': $(this).text()})
                });

                $('.no_of_days').each(function () {
                    data.push({'name': 'no_of_days[]', 'value': this.value})
                });

                $('.rentalItemType').each(function () {
                    if (this.value == '') {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });

                $('.deliverydat').each(function () {
                    if (this.value == '') {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });

                $('.quantityRequested').each(function () {
                    if (this.value == '' || this.value == 0) {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });

                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('CommunityNgo/save_item_issue_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                itemIssueDetailAutoID = null;
                                fetch_pqr_detail_table();
                                $('#item_issue_detail_modal').modal('hide');
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                    });
            } else {
                swal({
                    title: "Good job!",
                    text: "You clicked the button!",
                    type: "success"
                });
            }
        }

        function update_item_issue_details() {
            var data = $('#purchase_request_detail_edit_form').serializeArray();
            if (itemIssueAutoID) {
                data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
                data.push({'name': 'itemIssueDetailAutoID', 'value': itemIssueDetailAutoID});
                data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
                data.push({'name': 'no_of_daysEdit', 'value': $('#no_of_daysEdit').val()});

                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('CommunityNgo/update_item_issue_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data) {
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    itemIssueDetailAutoID = null;
                                    $('#item_issue_detail_edit_mod').modal('hide');
                                    fetch_pqr_detail_table();
                                }
                            }

                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                    });
            } else {
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

        //thanks: http://javascript.nwbox.com/cursor_position/
        function getSelectionStart(o) {
            if (o.createTextRange) {
                var r = document.selection.createRange().duplicate()
                r.moveEnd('character', o.value.length)
                if (r.text == '') return o.value.length
                return o.value.lastIndexOf(r.text)
            } else return o.selectionStart
        }

        /*.......................................................*/

        function clear_member_details() {
            $('#Com_MasterID').val('').change();
            $('#requestedMemberName').val('').trigger('input');
            $('#requestedMemberID').val('');
            $('#requestedMemberName').prop('readonly', false);
        }

        function link_member_modal() {
            $('#Com_MasterID').val('').change();
            $('#member_modal').modal('show');
        }

        function fetch_member_details() {
            var Com_MasterID = $('#Com_MasterID').val();
            if (Com_MasterID) {
                window.EIdNo = Com_MasterID;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'Com_MasterID': Com_MasterID},
                    url: "<?php echo site_url('CommunityNgo/fetch_member_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $('#requestedMemberName').val(data['CName_with_initials']).trigger('input');
                            $('#requestedMemberID').val(Com_MasterID);

                            $('#requestedMemberName').prop('readonly', true);
                            $('#member_modal').modal('hide');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            } else {

            }
        }

        function fetchExpectedDate() {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemIssueAutoID': itemIssueAutoID},
                    url: "<?php echo site_url('CommunityNgo/load_item_request_date'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#expectedReturnDateDetail').val(data['expectedReturnDate']);

                            deliverydat = data['expectedReturnDate'];
                            issuedat = data['MySQLissueDate'];

                           // var date1 = new Date(data['MySQLexpectedReturnDate']);
                           // var date2 = new Date(data['MySQLissueDate']);
                           // var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                            var a = moment(data['MySQLissueDate'], 'YYYY/MM/DD');
                            var b = moment(data['MySQLexpectedReturnDate'], 'YYYY/MM/DD');
                            var days = b.diff(a, 'days');

                            no_of_days = days;
                            $('#no_of_days').val(days);

                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
        }


        function deliverydate_val(det) {

            if (det.value != 0) {
                $(det).closest('tr').css("background-color", 'white');

                var date = det.value;

                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'date': date},
                        url: "<?php echo site_url('CommunityNgo/get_date_format'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {

                                var date1 = new Date(data);
                                var date2 = new Date(issuedat);
                                var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                                $(det).closest('tr').find('.no_of_days').val(diffDays);

                                var qut = $(det).closest('tr').find('.quantityRequested').val();
                                var amount = $(det).closest('tr').find('.estimatedAmount').val();
                                var days = diffDays;

                                if (qut == null || qut == 0) {
                                  //  $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_amount').text('0.00');
                                    $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                } else {
                                    $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                }
                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                );
            }
        }

        function deliverydate_val_edit(det) {

            if (det.value != 0) {
                $(det).closest('tr').css("background-color", 'white');

                var date = det.value;

                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'date': date},
                        url: "<?php echo site_url('CommunityNgo/get_date_format'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {

                                var date1 = new Date(data);
                                var date2 = new Date(issuedat);
                                var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                                $(det).closest('tr').find('.no_of_daysEdit').val(diffDays);

                                var qut = $(det).closest('tr').find('.quantityRequested_edit').val();
                                var amount = $(det).closest('tr').find('.estimatedAmount_edit').val();
                                var days = diffDays;

                                if (qut == null || qut == 0) {
                                    $(det).closest('tr').find('.net_amount_edit').text('0.00');
                                  //  $(det).closest('tr').find('.net_amount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                } else {
                                    $(det).closest('tr').find('.net_amount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                }

                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                );
            }
        }

        function get_itemIDs(det) {
            if (det.value) {

                var rentalItemID = det.value;

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'rentalItemID': rentalItemID},
                    url: "<?php echo site_url('CommunityNgo/fetch_rent_item_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $(det).closest('tr').find('.rentalItemType').val(data['rentalItemType']);
                            $(det).closest('tr').find('.itemAutoID').val(data['itemAutoID']);
                            $(det).closest('tr').find('.faID').val(data['faID']);
                            $(det).closest('tr').find('.PeriodTypeID').val(data['PeriodTypeID']);
                            $(det).closest('tr').find('.currentStock').val(data['currentStock']);
                            $(det).closest('tr').find('.estimatedAmount').val(data['RentalPrice']);

                            var qut = $(det).closest('tr').find('.quantityRequested').val();
                            var amount = data['RentalPrice'];
                            var days = $(det).closest('tr').find('.no_of_days').val();

                            if (qut == null || qut == 0) {
                                $(det).closest('tr').find('.net_amount').text('0.00');
                               // $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                            } else {
                                $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                            }

                            fetch_related_uom_id(data['defaultUnitOfMeasureID'], data['defaultUnitOfMeasureID'], det);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            }
        }

        function get_itemIDs_edit(det) {
            if (det.value) {

                var rentalItemID = det.value;

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'rentalItemID': rentalItemID},
                    url: "<?php echo site_url('CommunityNgo/fetch_rent_item_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $(det).closest('tr').find('.currentStock_edit').val(data['currentStock']);
                            fetch_related_uom_id_edit(data['defaultUnitOfMeasureID'], data['defaultUnitOfMeasureID'], det);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
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

                    var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                    mySelect.append($('<option></option>').val('').html('Select UOM'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                        if (select_value) {
                            $(element).closest('tr').find('.umoDropdown').val(select_value);
                        }
                    }
                }, error: function () {

                }
            });
        }


        function fetch_related_uom_id_edit(masterUnitID, select_value, element) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterUnitID': masterUnitID},
                url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.uomdrop').empty();

                    var mySelect = $(element).parent().closest('tr').find('.uomdrop');

                    mySelect.append($('<option></option>').val('').html('Select UOM'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                        if (select_value) {
                            $(element).closest('tr').find('.uomdrop').val(select_value);
                        }
                    }
                }, error: function () {

                }
            });
        }

        /* */

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 4/4/2018
 * Time: 2:12 PM
 */