<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_sales_commission_generate_sales_commission');
echo head_page($title, false);

/*echo head_page($_POST['page_name'], false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$sales_person_arr = all_srp_erp_sales_person_drop();
unset($sales_person_arr['']);
$financeyear_arr = all_financeyear_drop(true);
$financeyearperiodYN = getPolicyValues('FPC', 'All');

?>

<div id="filter-panel" class="collapse filter-panel"></div>
    <div class="m-b-md" id="wizardControl">

            <div class="steps">
                <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                    <span class="step__icon"></span>
                    <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_one');?> - <?php echo $this->lang->line('sales_markating_transaction_header');?></span>
                </a>
                <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab">
                    <span class="step__icon"></span>
                    <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_two');?> - <?php echo $this->lang->line('sales_markating_transaction_detail');?></span>
                </a>
                <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                    <span class="step__icon"></span>
                    <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_three');?> - <?php echo $this->lang->line('sales_markating_transaction_confirmation');?></span>
                </a>
            </div>

    </div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="sales_commission_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="asOfDate"><?php echo $this->lang->line('common_as_of_date');?> <?php required_mark(); ?></label><!--As Of Date-->
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="asOfDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="asOfDate" class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="salesPersonID"><?php echo $this->lang->line('sales_markating_transaction_sales_person');?> <?php required_mark(); ?></label><!--Sales Person-->
                <?php echo form_dropdown('salesPersonID[]', $sales_person_arr,'','class="form-control" multiple="multiple" id="salesPersonID"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label><!--Currency-->
                <?php  echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'] ,'class="form-control select2"  id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="referenceNo"><?php echo $this->lang->line('sales_markating_transaction_sales_reference_no');?></label><!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
        </div>
        <div class="row">
            <?php if($financeyearperiodYN == 1){ ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year');?> <?php required_mark(); ?></label><!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <?php }?>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?></button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">

    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="goodReceiptVoucher_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title');?> </h4><!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="goodReceiptVoucher_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?></button><!--Save as Draft-->
            <button class="btn btn-success-new size-lg submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?> </button><!--Confirm-->
        </div>
    </div>
</div>
<script type="text/javascript">
    var salesCommisionID;
    var documentCurrency;
    $(document).ready(function () {
        $("#transactionCurrencyID").prop("disabled", true);
        $('.headerclose').click(function () {
            fetchPage('system/sales/sales_commission', salesCommisionID, 'Sales Commission');
        });
        $('.select2').select2();
        salesCommisionID = null;
        documentCurrency = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#sales_commission_form').bootstrapValidator('revalidateField', 'asOfDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            salesCommisionID = p_id;
            laad_sales_commision_header();
            //$("#a_link").attr("href", "<?php //echo site_url('Sales/load_grv_conformation'); ?>/" + salesCommisionID);
            ///$("#de_link").attr("href", "<?php //echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + salesCommisionID + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('#salesPersonID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '100%',
                maxHeight: '10px'
            });
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        if (!p_id) {
            fetch_finance_year_period(FinanceYearID, periodID);
        }

        $('#sales_commission_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                asOfDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_sales_commission_as_of_date_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#transactionCurrencyID").prop("disabled", false);
            $("#salesPersonID").prop("disabled", false);
            $("#asOfDate").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesCommisionID', 'value': salesCommisionID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Sales/save_sales_commision_header'); ?>",
                beforeSend: function () {
                    startLoad();
                    $("#transactionCurrencyID").prop("disabled", true);
                },
                success: function (data) {
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        salesCommisionID = data['last_id'];
                        fetch_detail();
                        $("#a_link").attr("href", "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + salesCommisionID);
                        $("#de_link").attr("href","<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + salesCommisionID + '/SC');
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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
                    };
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function currency_validation(CurrencyID,documentID){
        if (CurrencyID) {
            partyAutoID = $('#salesPersonID').val();
            currency_validation_modal(CurrencyID,documentID,partyAutoID,'SUP');
        }
    }

    function laad_sales_commision_header() {
        if (salesCommisionID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'salesCommisionID': salesCommisionID},
                url: "<?php echo site_url('Sales/laad_sales_commision_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('.currency').html('( ' + data['header']['transactionCurrency'] + ' )');
                        $('#asOfDate').val(data['header']['asOfDate']);
                        $('#referenceNo').val(data['header']['referenceNo']);
                        salesCommisionID = data['header']['salesCommisionID'];
                        documentCurrency = data['header']['transactionCurrencyID'];
                        //$("#salesPersonID").val(data['header']['salesPersonID']).change();
                        $('#transactionCurrencyID').val(data['header']['transactionCurrencyID']).change();
                        $('#narration').val(data['header']['Description']);
                        $('#companyFinanceYearID').val(data['header']['companyFinanceYearID']).change();
                        $('#contactPersonName').val(data['header']['contactPersonName']);
                        $('#contactPersonNumber').val(data['header']['contactPersonNumber']);
                        $('#referenceNo').val(data['header']['referenceNo']);
                        //$('#salesPersonID').select2('val', data['person']);
                        //$('#').val(['6', '22']);
                        $('#salesPersonID').val(data['person']);
                        $('#financeyear').val(data['header']['companyFinanceYearID']);
                        fetch_finance_year_period(data['header']['companyFinanceYearID'], data['header']['companyFinancePeriodID']);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        fetch_detail();
                        $('#salesPersonID').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            buttonWidth: '100%',
                            maxHeight: '10px'
                        });
                        $("#a_link").attr("href", "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + salesCommisionID);
                        $("#de_link").attr("href","<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + salesCommisionID + '/SC');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'salesCommisionID': salesCommisionID,'currencyID': documentCurrency},
            url: "<?php echo site_url('Sales/fetch_inv_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                check_detail_dataExist(salesCommisionID);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_detail_dataExist(salesCommisionID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'salesCommisionID':salesCommisionID},
            url :"<?php echo site_url('Sales/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if(jQuery.isEmptyObject(data)){
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#asOfDate").prop("disabled", false);
                    $("#salesPersonID").prop("disabled", false);
                }else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#asOfDate").prop("disabled", true);
                    $("#salesPersonID").prop("disabled", true);
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_conformation() {
        if (salesCommisionID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesCommisionID': salesCommisionID, 'html': true},
                url: "<?php echo site_url('Sales/load_sc_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + salesCommisionID);
                    $("#de_link").attr("href","<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + salesCommisionID + '/SC');
                    attachment_modal_sc(salesCommisionID, "<?php echo $this->lang->line('sales_markating_transaction_sales_commission');?>", "SC");/*Sales Commission*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (salesCommisionID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want confirm this document!*/
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
                        data: {'salesCommisionID': salesCommisionID},
                        url: "<?php echo site_url('Sales/sc_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if(data["type"] == 'e'){
                                notification(data["message"],'w')
                            }else{
                                refreshNotifications(true);
                                fetchPage('system/sales/sales_commission', salesCommisionID, 'Sales Commission');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function save_draft() {
        if (salesCommisionID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/sales/sales_commission', salesCommisionID,'Sales Commission');
                });
        };
    }

    function edit_addon_cost_model(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': id},
            url: "<?php echo site_url('Sales/get_addon_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#addon_cost_form')[0].reset();
                    $('#addon_cost_form').bootstrapValidator('resetForm', true);
                    $("#addon_cost_modal").modal({backdrop: "static"});
                    $("#id").val(data['id']);
                    $("#addonCatagory").val(data['addonCatagory']);
                    $("#GLAutoID").val(data['GLAutoID']);
                    $("#narrations").val(data['narrations']);
                    $("#isChargeToExpense").val(data['isChargeToExpense']);
                    $("#bookingCurrencyID").val(data['bookingCurrencyID']);
                    fetch_all_item(data['impactFor']);
                    show_gl(data['isChargeToExpense']);
                    $("#referenceNos").val(data['referenceNo']);
                    $('#supplier').val(data['salesPersonID']);
                    $('#addon_uom').val(data['unitOfMeasure']);
                    $('#addon_qty').val(data['qty']);
                    $('#paid_by').val(data['paidBy']);
                    $('#total_amount').val(data['bookingCurrencyAmount']);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function attachment_modal_sc(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#goodReceiptVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#goodReceiptVoucher_attachment').empty();
                    $('#goodReceiptVoucher_attachment').append('' +data+ '');

                    //$("#attachment_modal_sc").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_goodReceiptVoucher_attachement(attachmentID, DocumentSystemCode,myFileName) {
        if (attachmentID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': attachmentID,'myFileName': myFileName},
                        url: "<?php echo site_url('Attachment/delete_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s','Deleted Successfully');
                                attachment_modal_sc(DocumentSystemCode, "Sales Commission", "SC");
                            }else{
                                myAlert('e','<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
</script>