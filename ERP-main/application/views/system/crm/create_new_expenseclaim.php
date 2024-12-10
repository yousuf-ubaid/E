<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$leadsArr = fetch_all_leads();
$opportunitiesArr = fetch_all_opportunities();
$projectArr = fetch_project_multiple();
$currency_arr = all_currency_new_drop();
$umo_arr = array('' => 'Select UOM');
$claim_arr = fetch_claim_category();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .posts-holder {
        padding: 0 0 10px 4px;
        margin-right: 10px;
    }

    #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        height: 20px;
    }

    .past-info {
        background: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 0 0 8px 10px;
        margin-left: 2px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .custome {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
    }

    .customestyle {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -46%
    }

    .customestyle2 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    .customestyle3 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;

        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }
</style>

<?php echo form_open('', 'role="form" id="expense_claim_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_expence_claim_information')?></h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_document_date')?></label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control">
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                <input type="hidden" name="claimedByEmpID" value="<?php echo current_userID() ;?> | <?php echo current_user() ;?>"
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_description')?></label>
            </div>
            <div class="form-group col-sm-4">
                            <textarea class="form-control" rows="3"
                                      name="comments" id="comments"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="text-right m-t-xs">
                <div class="form-group col-sm-6" style="margin-top: 10px;">
                    <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save')?></button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<br>

<div class="row" id="expenseClaimDetail_div">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>EXPENSE CLAIM DETAILS</h2>
        </header>
        <div class="row">
            <div class="col-sm-1">
                &nbsp;
            </div>
            <div class="col-sm-9 text-right">
                <button type="button" class="btn btn-primary "
                        onclick="expense_claim_detail_modal()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="col-sm-2">
                &nbsp;
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-1">
                &nbsp;
            </div>
            <div class="col-sm-9">
                <div id="taskMaster_view"></div>
            </div>
            <div class="col-sm-2">
                &nbsp;
            </div>
        </div>
    </div>
</div>

<br>
<div aria-hidden="true" role="dialog" id="expense_claim_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Item Detail</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="expanse_claim_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="ec_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Expense Claim Category <?php required_mark(); ?></th>
                            <th style="width: 150px;">Description <?php required_mark(); ?></th>
                            <th style="width: 100px;">Doc Ref<?php required_mark(); ?></th>
                            <th style="width: 100px;">Currency</th>
                            <th style="width: 150px;">Amount</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('expenseClaimCategoriesAutoID[]', $claim_arr, ' ', 'class="form-control select2 expenseClaimCategoriesAutoID"  required'); ?></td>

                            <td><input type="text" name="description[]" class="form-control description"></td>

                            <td><input type="text" name="referenceNo[]" class="form-control referenceNo"></td>

                            <td><?php echo form_dropdown('transactionCurrencyID[]', $currency_arr, $defaultCurrencyID, 'class="form-control select2 transactionCurrencyID"  required'); ?></td>

                            <td><input type="text" name="transactionAmount[]"
                                       class="form-control numeric transactionAmount" style="text-align: right;"
                                       onkeypress="return validateFloatKeyPress(this,event)" value="0"
                                       placeholder="0.00"></td>

                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="saveExpenseClaimDetails()">
                    <?php echo $this->lang->line('common_save'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">

    var expenseClaimMasterAutoID;
    var expenseClaimDetailsID;
    $(document).ready(function () {


        $('.headerclose').click(function () {
            fetchPage('system/crm/expense_claim_management', '', 'Expense Claims')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();
        expenseClaimMasterAutoID = null;
        expenseClaimDetailsID = null;

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            quotationAutoID = p_id;
            $('#expenseClaimDetail_div').removeClass('hide');
            load_customerOrder_header();

        } else {
            $('#expenseClaimDetail_div').addClass('hide');
            $('.btn-wizard').addClass('disabled');
        }

        $('#expense_claim_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                comments: {validators: {notEmpty: {message: 'Description is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'expenseClaimMasterAutoID', 'value': expenseClaimMasterAutoID});
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
                    $("#segmentID").prop("disabled", true);
                    /*var result = $('#transactionCurrencyID option:selected').text().split('|');
                     $('.currency').html('( ' + result[0] + ' )');*/
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        expenseClaimMasterAutoID = data['last_id'];
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


    function expense_claim_detail_modal() {

        if (expenseClaimMasterAutoID) {
            expenseClaimDetailsID = null;
            $('#expanse_claim_detail_form')[0].reset();
            $('#transactionAmount').val(0);
            $(".expenseClaimCategoriesAutoID").val(null).trigger("change");
            $(".transactionCurrencyID").val(<?php echo $defaultCurrencyID ?>).trigger("change");
            $('#ec_detail_add_table tbody tr').not(':first').remove();
            $("#expense_claim_detail_modal").modal({backdrop: "static"});
        }

    }


</script>