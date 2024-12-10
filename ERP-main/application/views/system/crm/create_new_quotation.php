<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customerArr = load_all_organizations();
$currency_arr = all_currency_new_drop();
$policy_IFE = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);
$umo_arr = array('' => 'Select UOM');
$contactperson = array('' => 'Select Contact Person');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
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
    .bordertype {
        border-left: 3px solid #daa520;
    }
    .bordertypePRO {
        border-left: 3px solid #f7f4f4;
    }
</style>
<!--<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Quotation Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab">Step 2 -
        Quotation Confirmation</a>
</div>-->

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="quotation_form"'); ?>
        <input type="hidden" name="quotationAutoID" id="quotationAutoID_edit">
        <input type="hidden" name="opportunityID" id="opportunityID">

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_quotation_information')?><!--QUOTATION INFORMATION--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_document_date')?><!--Document Date--></label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_expire_date')?><!--Expiry Date--></label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="expiryDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="expiryDate" class="form-control">
                </div>
               
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_organizations')?><!--Organization --></label>
                    </div>
                    <div class="form-group col-sm-4">
                            
                        <?php echo form_dropdown('customerID', $customerArr, '', 'class="form-control select2" id="customerID" onchange="fetchcontact_person(this.value)"'); ?>
                            

                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_contact_person_name')?><!--Contact Person Name--></label>

                    </div>
                    <div class="form-group col-sm-4">
                        <div id="div_loadcontactpersonn">
                            <?php echo form_dropdown('contactPersonID', $contactperson, 'Each', 'class="form-control" '); ?>
                        </div>
                    </div>



                </div>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_persons_email')?><!--Person's Email--></label>

                    </div>
                    <div class="form-group col-sm-4">

                            <input type="text" class="form-control " id="contactpersonemail"
                                   name="contactpersonemail" autocomplete="off">

                        </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_person_telephone_number')?><!--Person's Telephone Number--></label>

                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group ">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="text" class="form-control " id="contactPersonNumber"
                                   name="contactPersonNumber"  autocomplete="off">
                        </div>
                        </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_currency')?><!--Currency--></label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                    <span class="input-req-inner"></span>
                    </div>


                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_reference_number')?><!--Reference Number--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="referenceNo" id="referenceNo" class="form-control"  autocomplete="off">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">



                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_narration')?><!--Narration--></label>
                    </div>
                    <div class="form-group col-sm-10">
                            <textarea class="form-control" rows="3"
                                      name="narration" id="narration"></textarea>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_terms_and_condition')?><!--Terms & Condition--></label>
                    </div>
                    <div class="form-group col-sm-9">
                            <textarea class="form-control" rows="5"
                                      name="termsAndConditions" id="termsAndConditions"></textarea>
                    </div>
                </div>

                    <br>

            </div>
        </div>

        <br>

        <div class="row" id="customerItemDetail_div">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_quotation_details')?><!--QUOTATION DETAILS--></h2>
                </header>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="QuotationDetailProduct_view"></div>
                    </div>
                  <!--  <div class="col-sm-2">
                        &nbsp;
                    </div>-->
                </div>


            </div>

        </div>

        <br>
        <div class="text-right m-t-xs">
            <button class="btn btn-default" type="button" onclick="save_draft()"><?php echo $this->lang->line('common_close')?></button><!--Close-->
            <button class="btn btn-primary savebtn" type="submit"><?php echo $this->lang->line('common_save_as_draft')?></button><!--Save-->

        <button class="btn btn-success submitWizard confirmYN" type="button" onclick="confirm_quotation()"><?php echo $this->lang->line('common_confirm')?></button><!--common_confrim_and_submit-->
        </div>
    </div>   </form>

</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="customer_order_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('crm_add_product_detail')?><!--Add Product Detail--></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="customer_order_detail_form" class="form-horizontal">
                    <input type="hidden" class="" id="quotationAutoID_orderDetail"
                           name="quotationAutoID_orderDetail">
                    <table class="table table-bordered table-condensed no-color" id="customer_order_detail_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('crm_product')?><!--Product--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_uom')?><!--UOM--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_qty')?><!--QTY--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_price')?><!--Price--> <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('crm_total_price')?><!--Total Price--> <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('crm_expected_delivery_date')?><!--xpected Delivery Date--><?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_comment')?><!--Comment--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control f_search"
                                       name="search[]"
                                       placeholder="Item ID, Item Description..." id="f_search_1">
                                <input type="hidden" class="form-control productID" name="productID[]">
                            </td>
                            <td><input type="text" name="UnitOfMeasureID[]" value="Each" placeholder="UOM"
                                       class="form-control UnitOfMeasureID"></td>
                            <td><input type="text" name="quantityRequested[]" value="0" onkeyup="change_qty(this)"
                                       class="form-control number quantityRequested" onfocus="this.select();"></td>
                            <td><input type="text" name="estimatedAmount[]" value="0" placeholder="0.00"
                                       onkeyup="change_amount(this)"
                                       class="form-control number estimatedAmount" onfocus="this.select();"></td>
                            <td><input type="text" name="net_amount[]" value="0" placeholder="0.00"
                                       class="form-control number net_amount" readonly></td>
                            <td style="width:140px">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="expectedDeliveryDate[]"
                                           data-inputmask="'alias': 'dd-mm-yyyy'" value=""
                                           class="form-control expectedDeliveryDate" required="">
                                </div>
                            </td>
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="Item Comment..."></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('crm_product')?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="saveCustomerOrderDetails()"><?php echo $this->lang->line('common_save_change')?><!--Save changes-->
                </button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var search_id = 1;
    var search_id_related = 1;
    var quotationAutoID;
    var email;
    var ContactPersonName;
    var ContactPersonNameTP;
    $(document).ready(function () {

        initializeoppTypeahead(1);
        email = null;
        ContactPersonName = null;
        ContactPersonNameTP = null;
        var masterID = '<?php if(isset($_POST['data_arr']) && !empty($_POST['data_arr'])) { echo json_encode($_POST['data_arr']); } ?>';
        var related_document = '<?php if(isset($_POST['policy_id']) && !empty($_POST['policy_id'])) { echo $_POST['policy_id']; } ?>';
        var item_policy = <?php echo $policy_IFE ?>;

        if (masterID != null && masterID.length > 0) {
            if (related_document == 4) {
                str1 = masterID.replace(/"/g, '');
                $('#opportunityID').val(str1);
                $('.headerclose').click(function () {
                    fetchPage('system/crm/opportunities_edit_view', str1, 'View Opportunity', 'Opportunityquatation');
                });
                //load_opprtunity_BaseOrganization(str1);
            }
        } else {
            $('.headerclose').click(function () {
                fetchPage('system/crm/quotation_management', '', 'Quotation')
            });
        }
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            quotationAutoID = p_id;
            load_customerOrder_header();
            load_confirmation();
            qut_exist();
            $('.submitWizard').prop('disabled',false);

        } else {
            $("#termsAndConditions").wysihtml5();
            getCustomerOrderItem_tableView();
            $('.submitWizard').prop('disabled',true);
        }

        $('#quotation_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                //customerID: {validators: {notEmpty: {message: 'Organization is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                //expiryDate: {validators: {notEmpty: {message: 'Expiry Date is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            $('#transactionCurrencyID').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name' : 'contactPersonName', 'value' : $('#contactPersonID option:selected').text()});


            $('.productID').each(function () {
                    var search =  $(this).closest('tr').find('.f_search').val();
                    if((search))
                    {
                        if ((this.value == '' || this.value == '0') && (item_policy == 0)) {
                            $(this).closest('tr').css("background-color", '#ffb2b2 ');
                        }
                    }

            });

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/update_quotation_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        quotationAutoID = data[2];
                        $('#quotationAutoID_edit').val(quotationAutoID);
                        $('#quotationAutoID_orderDetail').val(quotationAutoID);
                        $('#transactionCurrencyID').prop('disabled', true);
                        //load_quotation_autoGeneratedID(quotationAutoID);
                        getCustomerOrderItem_tableView(quotationAutoID);
                        $('#order_documentAutoID').val(quotationAutoID);
                        /*$('.btn-wizard').removeClass('disabled');*/
                        $('.savebtn').prop('disabled', false);
                        $('.submitWizard').prop('disabled',false);
                        qut_exist();
                    } else {

                        $('.btn-primary').prop('disabled', false);
                        $('.savebtn').prop('disabled', false);
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

    function getCustomerOrderItem_tableView(quotationAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {quotationAutoID: quotationAutoID},
            url: "<?php echo site_url('crm/load_quotation_detail_item_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#customerItemDetail_div').removeClass('hide');
                qut_exist();
                $('#QuotationDetailProduct_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function customer_order_detail_modal() {

        $('.f_search').typeahead('destroy');
        var result = $('#transactionCurrencyID option:selected').text().split('|');
        $('.currency').html('( ' + result[0] + ' )');
        customerOrderDetailsID = null;
        $('#customer_order_detail_form')[0].reset();
        $('#customer_order_detail_table tbody tr').not(':first').remove();
        $('.net_amount,.net_unit_cost').text('0.00');
        $('.f_search').typeahead('val', '');
        $('.itemAutoID').val('');
        $("#customer_order_detail_modal").modal({backdrop: "static"});

    }






    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });


    function add_more_opportunitie() {
        search_id_related += 1;
        var appendData = $('.append_data:first').clone();
        appendData.find('input').val('');
        appendData.find('#f_search_opp_' + search_id_related).val('');
        appendData.find('.relatedTo').attr('id', 'relatedTo_' + search_id_related);
        appendData.find('.relatedAutoID').attr('id', 'relatedAutoID_' + search_id_related);
        appendData.find('.linkedFromOrigin').attr('id', 'linkedFromOrigin_' + search_id_related);
        appendData.find('.f_search').attr('id', 'f_search_opp_' + search_id_related);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#append_related_data').append(appendData);
        initializeoppTypeahead(search_id_related);
    }

    function initializeoppTypeahead(id) {
        var relatedType = $('#relatedTo_' + id).val();
        $('#f_search_opp_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Crm/fetch_document_relate_search/?&t=' + relatedType,
            onSelect: function (suggestion) {
                $('#relatedAutoID_' + id).val(suggestion.DoucumentAutoID);
            }
        });
    }

    function relatedChange(elemant) {
        initializeoppTypeahead(search_id_related);
        $('#f_search_opp_' + search_id_related).val('');
    }

    function load_quotation_autoGeneratedID(quotationAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {quotationAutoID: quotationAutoID},
            url: "<?php echo site_url('crm/load_quotation_autoGeneratedID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#documentAutoGeneratedID').val(data[0]);
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_quotation() {
        var opportunityID = $('#opportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('crm/save_quotation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    $('#quotationAutoID_edit').val(data[2]);
                    $('#quotationAutoID_orderDetail').val(data[2]);
                    load_quotation_autoGeneratedID(data[2]);
                    getCustomerOrderItem_tableView(data[2]);
                    $('#order_documentAutoID').val(data[2]);
                    $('.btn-wizard').removeClass('disabled');
                    //$('[href=#step2]').tab('show');
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function saveCustomerOrderDetails() {
        var quotationAutoID = $('#quotationAutoID_orderDetail').val();
        var data = $('#frm_quotationdet').serializeArray();
        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/save_quotation_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getCustomerOrderItem_tableView(quotationAutoID);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    function load_customerOrder_header() {
        if (quotationAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'quotationAutoID': quotationAutoID},
                url: "<?php echo site_url('crm/load_quotation_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#documentIDShow').removeClass('hide');
                        $('#quotationAutoID_edit').val(quotationAutoID);
                        $('#quotationAutoID_orderDetail').val(quotationAutoID);
                        $('#documentAutoGeneratedID').val(data['quotationCode']);
                        $('#documentDate').val(data['quotationDate']);
                        $('#expiryDate').val(data['quotationExpDate']);
                        $('#customerID').val(data['customerID']).change();
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        //$('#contactPersonName').val(data['quotationPersonName']);
                       // $('#contactPersonNumber').val(data['quotationPersonNumber']);
                        $('#referenceNo').val(data['referenceNo']);
                        $('#narration').val(data['quotationNarration']);
                        $("#termsAndConditions").wysihtml5();
                        $('#termsAndConditions').val(data['termsAndConditions']);
                        email = data['quotationPersonEmail'];
                        ContactPersonName = data['quotationPersonID'];
                        ContactPersonNameTP = data['quotationPersonNumber'];
              /*          setTimeout(function () {
                            $('#contactPersonID').val(data['quotationPersonID']).change();
                        }, 500);*/
                        fetch_contact_details(ContactPersonName,1,email,ContactPersonNameTP);
                       /* if(data['quotationPersonEmail'])
                        {
                            $('#contactPersonID').select2("val",data['quotationPersonID']);
                        }else
                        {
                            setTimeout(function () {
                                $('#contactPersonID').val(data['quotationPersonID']).change();
                            }, 500);
                        }*/

                       // $('#contactpersonemail').val(data['quotationPersonEmail']);
                       // $('#contactPersonNumber').val(data['quotationPersonNumber']);
                        getCustomerOrderItem_tableView(quotationAutoID);
                        qut_exist();

                        //$('[href=#step2]').tab('show');
                       /* $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');*/
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        $.each(data['detail'], function (key, value) {
                            if (key > 0) {
                                add_more_opportunitie();
                            }
                        });
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var id = 1;
                        $.each(data['detail'], function (key, value) {
                            if(value.relatedDocumentID!=0)
                            {
                                $('#relatedTo_' + id).val(value.relatedDocumentID);
                            }else
                            {
                                $('#relatedTo_' + id).val('');
                            }

                            $('#relatedAutoID_' + id).val(value.relatedDocumentMasterID);
                            $('#f_search_opp_' + id).val(value.searchValue);
                            $('#linkedFromOrigin_' + id).val(value.originFrom);
                            if (value.originFrom == 1) {
                                $("#relatedTo_" + id).prop("disabled", "disabled");
                                $("#f_search_opp_" + id).prop("disabled", "disabled");
                                $("#linkmorerelation").addClass("hide");
                            } else {
                                $("#linkmorerelation").removeClass("hide");
                            }
                            id++;
                        });
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

    function update_quotation() {
        var data = $('#quotation_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('crm/update_quotation_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetchPage('system/crm/opportunities_edit_view', str1, 'View Opportunity', 'CRM');
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Something went wrong Please update again:)", "error");
            }
        });
    }

    function confirm_quotation() {
        var item_policy = <?php echo $policy_IFE ?>;
        $('#transactionCurrencyID').prop('disabled', false);
        swal({
                title: "Are you sure?",
                text: "You want to confirm this document!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var data = $('#quotation_form').serializeArray();
                data.push({'name': 'confirmedYN', 'value': 1});

                $('.productID').each(function () {
                    var search =  $(this).closest('tr').find('.f_search').val();
                    if((search))
                    {
                        if ((this.value == '' || this.value == '0') && (item_policy == 0)) {
                            $(this).closest('tr').css("background-color", '#ffb2b2 ');
                        }
                    }

                });

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('crm/update_quotation_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/crm/quotation_management', '', 'Quotation');
                            $('#transactionCurrencyID').prop('disabled', true);
                        } else {
                            $('#transactionCurrencyID').prop('disabled', true);
                        }
                        qut_exist();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Something went wrong Please update again :)", "error");
                    }
                });
            });

    }

    function load_customer_BaseDetail() {
        var customerID = $('#customerID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/load_customer_BaseDetail"); ?>',
            dataType: 'json',
            data: {'customerID': customerID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#transactionCurrencyID').val(data['customerCurrencyID']).change();
                    $('#customerTelephone').val(data['customerTelephone']);
                    $('#CustomerAddress1').val(data['CustomerAddress1']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function delete_order_detail(contractDetailsAutoID) {
        var qotation = $('#quotationAutoID_orderDetail').val();
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
                    data: {'contractDetailsAutoID': contractDetailsAutoID},
                    url: "<?php echo site_url('crm/delete_quotation_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            qut_exist();
                            myAlert('s', 'Deleted Successfully');
                            getCustomerOrderItem_tableView(qotation);
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

    function change_amount(element) {
        net_amount(element);
    }

    function change_qty(element) {
        net_amount(element);
    }

    function net_amount(element) {

        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount').val('0.00');
        } else {
            $(element).closest('tr').find('.net_amount').val((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut)).formatMoney(2, '.', ','));
        }
    }


    function load_opprtunity_BaseOrganization(opportunityID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('crm/load_opprtunity_BaseOrganization'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#customerID').val(data[0]).change();
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_confirmation() {
        if (quotationAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'quotationAutoID': quotationAutoID, 'html': true,'type': 1},
                url: "<?php echo site_url('crm/quotation_print_view_new'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data['view']);
                    $('#dateofissue').html(data['master']['quotationDate']);
                    $('#expiarydate').html(data['master']['quotationExpDate']);
                    $('#dateofissueright').html(data['master']['quotationDate']);
                    $('#expiarydateright').html(data['master']['quotationExpDate']);
                    $('#quotationcode').html(data['master']['quotationCode']);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_draft() {

     
            fetchPage('system/crm/quotation_management', '', 'Quotation');


    }
    function fetchcontact_person(id) {
        $('#contactpersonemail').val('');
        $('#contactPersonNumber').val('');
        $('#div_loadcontactpersonn').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {organizationID: id},
            url: "<?php echo site_url('crm/quotation_organization_detail_fetch'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#div_loadcontactpersonn').html(data);
                }
                $('.select2').select2();
                $('#contactPersonID').val(ContactPersonName);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function fetch_contact_details(contactid,type,email,ContactPersonNameTP) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contactid': contactid},
            url: "<?php echo site_url('crm/fetch_contact_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if(type == 1)
                    {
                        $('#contactpersonemail').val(email);
                        $('#contactPersonNumber').val(ContactPersonNameTP);
                    }else if (type == 2)
                    {
                        $('#contactpersonemail').val(data['email']);
                        $('#contactPersonNumber').val(data['phoneMobile']);
                    }
                }
                else
                {
                    $('#contactpersonemail').val(' ');
                    $('#contactPersonNumber').val(' ');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }
    function delete_quotation_detail(contractDetailsAutoID) {
        var quotationAutoID = $('#quotationAutoID_orderDetail').val();

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
                    url: "<?php echo site_url('Crm/delete_quotation_detail_line'); ?>",
                    type: 'post',
                    data: {QuotationDeatailAutoID: contractDetailsAutoID},
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
                            getCustomerOrderItem_tableView(quotationAutoID);
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
    function qut_exist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'quotationAutoID': quotationAutoID},
            url: "<?php echo site_url('Crm/qut_voucher_details_exist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#transactionCurrencyID").attr('disabled', 'disabled');


                } else {
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
    function quatationitemvalidation(element) {

        $('.productID').each(function () {
            alert(this.value );
            var search =  $(this).closest('tr').find('.f_search').val();
            if((search)) {
                if ((this.value == '') || (this.value == '0') && item_policy == 0) {

                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    swal({
                        title: " ",
                        text: "The Item You have selected does not exist!",
                        type: "error"
                    }, function () {
                        $(this).closest('tr').css("background-color", 'white');

                    });
                }
            }
        });
    }


</script>