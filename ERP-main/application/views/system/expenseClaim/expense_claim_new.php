<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$type_arr = array('' => 'Select Type', 'Standard' => 'Standard');
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$sold_arr = sold_to();
$ship_arr = ship_to();
$invoice_arr = invoice_to();
$umo_arr = array('' => 'Select UOM');
$segment_arr = fetch_segment();
$segment_arr_detail = fetch_segment(true);
$transaction_total = 100;
$claim_arr = fetch_claim_category();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('profile_Step1_expense_claim_header'); ?><!--Step 1 - Expense Claim Header--> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_EC_detail_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('profile_Step2_expense_claim_detail'); ?><!--Step 2 - Expense Claim Detail--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('profile_Step3_expense_claim_confirmation'); ?><!--Step 3 - Expense Claim Confirmation--></span>
            </a>
           
        </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="expense_claim_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_date'); ?><!--Expense Claim Date --><?php required_mark(); ?></label>
                <input type="hidden" name="claimedByEmpID" value="<?php echo current_userID() ;?> | <?php echo current_user() ;?>" id="claimedByEmpID" class="form-control" >
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="expenseClaimDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="expenseClaimDate" class="form-control" required>
                </div>
            </div>
            <!-- <div class="col-sm-4">
                <div class="form-group">
                    <label for="purchaseOrderType">Type <?php /*required_mark(); */ ?></label>
                    <?php /*echo form_dropdown('purchaseOrderType', $type_arr, 'Standard', 'class="form-control select2" id="purchaseOrderType" required'); */ ?>
                </div>
            </div>-->
            <div class="form-group col-sm-4">
                <label for="segment">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment --><?php required_mark(); ?>
                </label>
                <?php echo form_dropdown('segmentID', $segment_arr, current_user_segemnt(), 'class="form-control select2" id="segmentID" required'); ?>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="shippingAddressDescription">
                        <?php echo $this->lang->line('common_description'); ?><!--Description-->
                    </label>
                    <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next-->
            </button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8">
                <h4><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('profile_expense_claim_detail'); ?><!--Expense Claim Detail-->
                </h4>
            </div>
            <div class="col-md-4">
                <button type="button" onclick="expense_claim_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('profile_expense_claim_category'); ?><!--Expense Claim Category--></th>
                <th style="min-width: 25%" class="text-left"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('profile_doc_reference'); ?><!--Doc Ref--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('profile_segment'); ?><!--Segment--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="table_body">
            <tr class="danger">
                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
            <!--<tfoot id="table_tfoot">

            </tfoot>-->
        </table>
        <br>
        <hr>
        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
            <button class="btn btn-primary next" onclick="load_conformation();"><?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>


<br>
        <div class="row">
                <hr>
                <h4 class="modal-title" id="purchaseOrder_attachment_label">Modal title</h4>
                <!-- <div class="col-md-2">&nbsp;</div> -->
                <div class="col-md-6">
                    <span class="pull-right">
                    <form id="ec_attachment_uplode_form" class="form-inline" enctype="multipart/form-data" method="post">
                   
                        <div class="form-group">
                        <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                      
                      <input type="hidden" class="form-control" id="documentSystemCode" name="documentSystemCode">
                      <input type="hidden" class="form-control" id="documentID" value="EC" name="documentID">
                      <input type="hidden" class="form-control" id="document_name" value="Expense Claim" name="document_name">
                      <input type="hidden" class="form-control" id="confirmYNadd" name="confirmYNadd">
                        </div>
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename set-w-file-name"></span></div>
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
                        <button type="button" class="btn btn-default" onclick="expense_claim_document_uplode()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form></span>
                </div>
                <div class="col-md-6"><span class="pull-right"></div>
            <div>
        <div id="conform_body_attachement">
            

            <br>


            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="purchaseOrder_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" id="expense_claim_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('profile_add_item_detail'); ?><!--Add Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="expanse_claim_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="ec_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('profile_expense_claim_category'); ?><!--Expense Claim Category--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('profile_doc_reference'); ?><!--Doc Ref--> <?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('common_segment'); ?><!--Segment--><?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('expenseClaimCategoriesAutoID[]', $claim_arr, ' ', 'class="form-control select2 expenseClaimCategoriesAutoID"  required'); ?></td>

                            <td><input type="text" name="description[]" class="form-edit_itemcontrol description"></td>

                            <td><input type="text" name="referenceNo[]" class="form-control referenceNo"></td>
                            <td><?php echo form_dropdown('segmentIDDetail[]', $segment_arr_detail, '', 'class="form-control select2 segmentIDDetail" id="segmentIDDetail" required'); ?></td>

                            <td><?php echo form_dropdown('transactionCurrencyID[]', $currency_arr, $defaultCurrencyID, 'class="form-control select2 transactionCurrencyID"  required'); ?></td>

                            <td><input type="text" name="transactionAmount[]"
                                       class="form-control numeric transactionAmount" style="text-align: right;" onkeypress="return validateFloatKeyPress(this,event)" value="0" placeholder="0.00"></td>

                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="saveExpenseClaimDetails()"><?php echo $this->lang->line('common_save'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="expense_claim_detail_edit_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('profile_edit_item_detail'); ?><!--Edit Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="expense_claim_detail_edit_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('profile_expense_claim_category'); ?><!--Expense Claim Category--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('profile_doc_reference'); ?><!--Doc Ref--> <?php required_mark(); ?></th>
                            <th style="width: 100px;">Segment <?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('expenseClaimCategoriesAutoIDEdit', $claim_arr, ' ', 'class="form-control select2 expenseClaimCategoriesAutoIDEdit" id="expenseClaimCategoriesAutoIDEdit"  required'); ?></td>

                            <td><input type="text" name="descriptionEdit" id="descriptionEdit"
                                       class="form-control descriptionEdit"></td>

                            <td><input type="text" name="referenceNoEdit" id="referenceNoEdit"
                                       class="form-control referenceNoEdit"></td>
                            <td><?php echo form_dropdown('segmentIDDetailEdit', $segment_arr_detail, '', 'class="form-control select2" id="segmentIDDetailEdit" required'); ?></td>

                            <td><?php echo form_dropdown('transactionCurrencyIDEdit', $currency_arr, ' ', 'class="form-control select2 transactionCurrencyIDEdit" id="transactionCurrencyIDEdit"  required'); ?></td>

                            <td><input type="text" name="transactionAmountEdit"
                                       class="form-control numeric transactionAmountEdit" style="text-align: right;" onkeypress="return validateFloatKeyPress(this,event)" id="transactionAmountEdit"
                                       value="0" placeholder="0.00"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="updateExpenseClaimDetails()"><?php echo $this->lang->line('common_update'); ?><!--Update changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_id = 1;
    var itemAutoID;
    var expenseClaimMasterAutoID;
    var expenseClaimDetailsID;
    var currency_decimal;
    var documentCurrency;
    var tax_total;
    var item;
    var segmentID;
    $(document).ready(function () {
        $("input.numeric").numeric();
        item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });
        item.initialize();

        $('.headerclose').click(function () {
            fetchPage('system/expenseClaim/expense_claim_management', expenseClaimMasterAutoID, 'Purchase Order');
        });

        $('.select2').select2();
        expenseClaimMasterAutoID = null;
        expenseClaimDetailsID = null;
        itemAutoID = null;
        currency_decimal = 2;
        documentCurrency = null;
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#expense_claim_form').bootstrapValidator('revalidateField', 'expenseClaimDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            expenseClaimMasterAutoID = p_id;
            laad_EC_header();
            fetch_EC_detail_table();
            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + expenseClaimMasterAutoID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }


        $('#expense_claim_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                expenseClaimDate: {validators: {notEmpty: {message: 'Expense Claim Date is required.'}}},
                comments: {validators: {notEmpty: {message: 'Description is required.'}}},
                segmentID: {validators: {notEmpty: {message: 'Segment is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#segmentID").prop("disabled", false);
            /*$("#purchaseOrderType").prop("disabled", false);
             $("#segment").prop("disabled", false);
             $("#supplierPrimaryCode").prop("disabled", false);
             $("#transactionCurrencyID").prop("disabled", false);*/
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'expenseClaimMasterAutoID', 'value': expenseClaimMasterAutoID});
            /*data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});*/
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('ExpenseClaim/save_expense_claim_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    /*$("#segmentID").prop("disabled", true);*/
                    /*var result = $('#transactionCurrencyID option:selected').text().split('|');
                     $('.currency').html('( ' + result[0] + ' )');*/
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        expenseClaimMasterAutoID = data['last_id'];
                        segmentID = data['segmentID'];
                        $('#segmentIDDetail').val(segmentID).change();
                        //$("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + expenseClaimMasterAutoID);

                        /*$("#purchaseOrderType").prop("disabled", true);
                         $("#segment").prop("disabled", true);
                         $("#supplierPrimaryCode").prop("disabled", true);
                         $("#transactionCurrencyID").prop("disabled", true);*/
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    refreshNotifications(true);
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
    });

    function fetch_EC_detail_table() {
        if (expenseClaimMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID},
                url: "<?php echo site_url('ExpenseClaim/fetch_Ec_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    } else {
                        tot_amount = 0;
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['claimcategoriesDescription'] + '</td><td>' + value['description'] + '</td><td >' + value['referenceNo'] + '</td><td >' + value['segmentCode'] + '</td><td class="text-center">' + value['transactionCurrency'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).toFixed(value['transactionCurrencyDecimalPlaces']) + '</td><td class="text-right"><a onclick="edit_item(' + value['expenseClaimDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['expenseClaimDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
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

    function clearitemAutoID(element) {
        $(element).closest('tr').find('.itemAutoID').val('');
    }

    function clearitemAutoIDEdit(element) {
        $(element).closest('tr').find('#itemAutoID_edit').val('');
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        poID = expenseClaimMasterAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': expenseClaimMasterAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data['amount']);
                    net_amount(element);
                }
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    
 

// function document_upload() {
//     var formData = new FormData($("#attachment_uplode_form")[0]);
//     $.ajax({
//         type: 'POST',
//         dataType: 'JSON',
//         url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
//         data: formData,
//         contentType: false,
//         cache: false,
//         processData: false,
//         beforeSend: function () {
//             startLoad();
//         },
//         success: function (data) {
//             stopLoad();
//             myAlert(data['type'], data['message'], 1000);
//             if (data['status']) {
//                 $('#documentSystemCode').val('');
//                 $('#document_name').val('');
//                 $('#documentID').val('');
//                 $('#confirmYNadd').val('');
//                 $('#remove_id').click();
//                 $('#attachmentDescription').val('');
//             }
//         },
//         error: function (xhr, status, error) {
//             stopLoad();
//             // Handle errors more gracefully
//             console.error(xhr.responseText);
//             swal("Error", "Failed to upload document: " + error, "error");
//         }
//     });
//     return false;
// }


    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = expenseClaimMasterAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': expenseClaimMasterAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#estimatedAmount_edit').val(data['amount']);
                    $('#discount_edit').val('');
                    $('#discount_amount_edit').val('');
                }
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
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

    function select_text(data) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), tax_total);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((tax_total / 100) * parseFloat(discount)).toFixed(2));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Procurement/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_EC_detail_table();
                            }, 300);
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        if (supplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
                success: function (data) {
                    if (documentCurrency) {
                        $("#transactionCurrencyID").val(documentCurrency).change()
                    } else {
                        if (data.supplierCurrencyID) {
                            $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                            currency_validation_modal(data.supplierCurrencyID, 'PO', supplierAutoID, 'SUP');
                        }
                    }

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
                        ;
                    }
                }
            },  error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                // swal("Cancelled", "Your file is safe :)", "error");
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
                $('#UnitOfMeasureID_edit').empty();
                var mySelect = $('#UnitOfMeasureID_edit');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasureID_edit").val(select_value);
                    }
                }
            },  error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                // swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function laad_EC_header() {
        if (expenseClaimMasterAutoID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID},
                    url: "<?php echo site_url('ExpenseClaim/load_expense_claim_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#segmentID').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#expenseClaimDate').val(data['expenseClaimDate']);
                            $('#comments').val(data['comments']);
                            segmentID=data['segmentID'];
                            $('#segmentIDDetail').val(data['segmentID']).change();
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

    function fetch_ship_to(val) {
        if (val) {
            var ship = $('#shippingAddressID option:selected').text();
            var res = ship.split(" | ");
            $('#shippingAddressDescription').val(res[2]);
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function expense_claim_detail_modal() {
        if (expenseClaimMasterAutoID) {
            $('#expanse_claim_detail_form')[0].reset();
            //$('.f_search').typeahead('destroy');
            $('#segmentIDDetail').val(segmentID).change();
            expenseClaimDetailsID = null;
            $('#transactionAmount').val(0);
            $(".expenseClaimCategoriesAutoID").val(null).trigger("change");
            $(".transactionCurrencyID").val(<?php echo $defaultCurrencyID ?>).trigger("change");
            $('#ec_detail_add_table tbody tr').not(':first').remove();
            $("#expense_claim_detail_modal").modal({backdrop: "static"});
        }
    }

    function load_conformation() {
        if (expenseClaimMasterAutoID) {
            $("#documentSystemCode").val(expenseClaimMasterAutoID);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID, 'html': true},
                url: "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    attachment_modal_expenseClaim(expenseClaimMasterAutoID, "<?php echo $this->lang->line('profile_expense_claim');?>", "EC");/*Expense Claim*/
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID},
                        url: "<?php echo site_url('ExpenseClaim/expense_claim_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if (data[0] == 's') {
                                fetchPage('system/expenseClaim/expense_claim_management', expenseClaimMasterAutoID, 'Expense Claim');
                            }
                        }, error: function () {
                            myAlert('e','error');
                        }
                    });
                });
        }
    }
    
    function save_draft() {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    fetchPage('system/expenseClaim/expense_claim_management', expenseClaimMasterAutoID, 'Expense Claim');
                });
        }
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierPrimaryCode').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function delete_item(id) {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'expenseClaimDetailsID': id},
                        url: "<?php echo site_url('ExpenseClaim/delete_expense_claim_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_EC_detail_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id, value) {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $('#ec_detail_add_table tbody tr').not(':first').remove();


                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'expenseClaimDetailsID': id},
                        url: "<?php echo site_url('ExpenseClaim/fetch_expense_claim_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            expenseClaimDetailsID = data['expenseClaimDetailsID'];
                            var transAmount = parseFloat(data['transactionAmount']);
                            $('#expenseClaimCategoriesAutoIDEdit').val(data['expenseClaimCategoriesAutoID']).change();
                            $('#descriptionEdit').val(data['description']);
                            $('#referenceNoEdit').val(data['referenceNo']);
                            $('#transactionCurrencyIDEdit').val(data['transactionCurrencyID']).change();
                            $('#transactionAmountEdit').val(data['transactionAmount']);
                            $('#segmentIDDetailEdit').val(data['segmentID']).change();
                            $("#expense_claim_detail_edit_modal").modal({backdrop: "static"});
                            stopLoad();
                        },  error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function cal_discount_amount(discount_amount, element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if (estimatedAmount) {
            $(element).closest('tr').find('.discount').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(2));
        }
        net_amount(element);
    }

    function cal_discount(discount, element) {
        if (discount < 0 || discount > 100) {
            /*Cancelled*/ /*Discount % should be between 0 - 100*/
            swal("<?php echo $this->lang->line('profile_cancelled'); ?>", "<?php echo $this->lang->line('profile_discount_0_100'); ?>", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(discount));
            }
            net_amount(element);
        }
    }

    function change_amount(element) {
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    function change_qty(element) {
        net_amount(element);
    }

    function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0.00');
        } else {
            $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(2, '.', ','));
            $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(2, '.', ','));
        }
    }

    //update function for inline editing
    function cal_discount_amount_edit() {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        var discount_amount = parseFloat($('#discount_amount_edit').val());
        if (discount_amount > estimatedAmount) {
            /*Cancelled*/ /*Discount Amount should be less than the Unit Cost*/
            swal("<?php echo $this->lang->line('profile_cancelled'); ?>", "<?php echo $this->lang->line('profile_discount_unit_cost'); ?>", "error");
            $('#discount_amount_edit').val(0);
            $('#discount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#discount_edit').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(3));
            }
            net_amount_edit(estimatedAmount);
        }
    }

    function cal_discount_edit(discount) {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        if (discount < 0 || discount > 100) {
            /*Cancelled*/ /*Discount % should be between 0 - 100*/
            swal("<?php echo $this->lang->line('profile_cancelled'); ?>", "<?php echo $this->lang->line('profile_discount_0_100'); ?>", "error");
            $('#discount_edit').val(0);
            $('#discount_amount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {

            if (estimatedAmount) {
                $('#discount_amount_edit').val((estimatedAmount / 100) * parseFloat(discount));
            }
            net_amount_edit(estimatedAmount);
        }

    }

    function change_qty_edit() {
        net_amount_edit();
    }

    function change_amount_edit() {
        $('#discount_edit').val(parseFloat(0));
        $('#discount_amount_edit').val(parseFloat(0));
        net_amount_edit();
    }

    function net_amount_edit() {
        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();
        var discoun = $('#discount_amount_edit').val();
        if (qut == null || qut == 0) {
            $('#totalAmount_edit').text('0.00');
            $('#net_unit_cost_edit').text('0.00');
        } else {
            $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(2, '.', ','));
            $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(2, '.', ','));
        }
    }

    function attachment_modal_expenseClaim(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseOrder_delete(expenseClaimMasterAutoID, DocumentSystemCode, fileName) {
        if (expenseClaimMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': expenseClaimMasterAutoID, 'myFileName': fileName},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            attachment_modal_expenseClaim(DocumentSystemCode, "Expense Claim", "EC");
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more() {

        $('select.select2').select2('destroy');
        var appendData = $('#ec_detail_add_table tbody tr:first').clone();
        // appendData.find('.expenseClaimCategoriesAutoID,.item_text').empty();
        appendData.find('.description').val('');
        appendData.find('.referenceNo').val('');
        //appendData.find('.transactionCurrencyID,.item_text').empty();
        appendData.find('.transactionAmount').val('0');
        appendData.find('.segmentIDDetail').val(segmentID).change();
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#ec_detail_add_table').append(appendData);
        var lenght = $('#ec_detail_add_table tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
    }

    function saveExpenseClaimDetails() {
        var data = $('#expanse_claim_detail_form').serializeArray();
        if (expenseClaimMasterAutoID) {
            data.push({'name': 'expenseClaimMasterAutoID', 'value': expenseClaimMasterAutoID});
            data.push({'name': 'expenseClaimDetailsID', 'value': expenseClaimDetailsID});
            $('select[name="transactionCurrencyID[]"] option:selected').each(function () {
                data.push({'name': 'tCurrencyID[]', 'value': $(this).text()})
            })
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('ExpenseClaim/save_expense_claim_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            expenseClaimDetailsID = null;
                            fetch_EC_detail_table();
                            $('#expense_claim_detail_modal').modal('hide');
                        }
                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
                });
        }
    }

    function updateExpenseClaimDetails() {
        var data = $('#expense_claim_detail_edit_form').serializeArray();
        if (expenseClaimMasterAutoID) {
            data.push({'name': 'expenseClaimMasterAutoID', 'value': expenseClaimMasterAutoID});
            data.push({'name': 'expenseClaimDetailsID', 'value': expenseClaimDetailsID});
            data.push({'name': 'tCurrencyID', 'value': $('#transactionCurrencyIDEdit option:selected').text()});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('ExpenseClaim/update_expense_claim_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                expenseClaimDetailsID = null;
                                $('#expense_claim_detail_edit_modal').modal('hide');
                                fetch_EC_detail_table();
                            }
                        }

                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
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
        if(number.length>1 && charCode == 46){
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");

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
    function expense_claim_document_uplode() {
        var formData = new FormData($("#ec_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
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
                if (data['status']) {
                    attachment_modal_expenseClaim($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val());
                    // attachment_modal($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
                     $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
</script>