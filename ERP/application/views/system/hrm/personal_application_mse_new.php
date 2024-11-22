<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();

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

$actions_arr = load_personal_action_types(0);
$count = count($actions_arr);

$employee_arr = fetch_all_employees();
// $groups = all_group_drop();
$designations_arr = all_designation_drop();
$grades_arr = employee_grade_drop();
$transfer_type_arr = transferType();
$transfer_term_arr = transferTerm();
$group_arr = all_group_drop();

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>

<style>
    #step3 {
        margin-left: 200px; /* Adjust the left margin as needed */
        margin-right: 200px; /* Adjust the right margin as needed */
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">Personal Action Header <!--Step 1 - Personal Action Header--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_persional_view();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">Personal Action Detail <!--Step 2 - Personal Action Detail--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation_mse();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">Personal Action Confirmation <!--Step 3 - Personal Action Confirmation--></span>
            </a>
           
        </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="persional_action_header_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="actionType">Action Type<?php required_mark(); ?></label>
                <?php echo form_dropdown('actionType', $actions_arr, '', 'class="form-control select2" id="actionType" required'); ?>
            </div>
         
            <div class="form-group col-sm-4">
                <label for="empName">Employee No/ Name<?php required_mark(); ?></label>
                <?php echo form_dropdown('empName', $employee_arr, '', 'class="form-control select2" id="empName" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="actionDate">Document Date<?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="actionDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="actionDate" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="form-group ">
                    <label for="shippingAddressDescription">Remark</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                </div>
            </div>
        </div>

        <hr>

        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit" id="header_save">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next-->
            </button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div id="action_view"></div>
    </div>

    <div id="step3" class="tab-pane">
        <div id="conform_body">

        </div>
        <hr>

        <div class="row">
                <hr>
                <h4 class="modal-title" id="personal_application_mse_attachment_lable">Personal Action Attachments</h4>
                <!-- <div class="col-md-2">&nbsp;</div> -->
                <div class="col-md-6">
                    <span class="pull-right">
                    <form id="pa_attachment_uplode_form" class="form-inline" enctype="multipart/form-data" method="post">
                   
                        <div class="form-group">
                        <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                      
                       <input type="hidden" class="form-control" id="documentSystemCode" name="documentSystemCode">
                        <input type="hidden" class="form-control" id="documentID" value="PAa" name="documentID">
                        <input type="hidden" class="form-control" id="document_name" value="Personal Action" name="document_name">
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
                        <button type="button" class="btn btn-default" onclick="personal_action_document_upload()"><span
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
                    <tbody id="personal_action_mse_attachment" class="no-padding">
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
            <button class="btn btn-success submitWizard" onclick="confirmation_mse()"><?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>



<script type="text/javascript">
    var search_id = 1;
    var itemAutoID;
    var empID;
    var actionMasterAutoID;
    var actionDetailsID;
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
            fetchPage('system/hrm/personal_application_mse', actionMasterAutoID, 'Purchase Order');
        });

        $('.select2').select2();
        actionMasterAutoID = null;
        actionDetailsID = null;
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
            $('#persional_action_header_form').bootstrapValidator('revalidateField', 'actionDate');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {

            actionMasterAutoID = p_id;
            
            fetch_persional_view();
            $('[href=#step2]').tab('show');
            
            $('#actionType').prop('disabled', true);
            $('#empName').prop('disabled', true);
            $('#actionDate').prop('disabled', true);
            //$('#header_save').addClass('disabled');
            //$('#header_save').attr('disabled', true);
            
            //fetch_personal_action_head();.......
            //fetch_persional_action_det();...
            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + actionMasterAutoID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }


        $('#persional_action_header_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                actionType: {validators: {notEmpty: {message: 'Action Type is required.'}}},
                empName: {validators: {notEmpty: {message: 'Employee Name is required.'}}},
                actionDate: {validators: {notEmpty: {message: 'Action Create Date is required.'}}}
                //remarks: {validators: {notEmpty: {message: 'Remarks is required.'}}}
                
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#actionType").prop("disabled", false);
            $("#empName").prop("disabled", false);
            
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'id', 'value': actionMasterAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_mse_personal_action_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#actionType').prop('disabled', true);
                    $('#empName').prop('disabled', true);
                    $('#actionDate').prop('disabled', true);

                     refreshNotifications(true);

                    if(data['status'] == 1){
                        myAlert('e', data['message']);
                    };
                    if(data['status'] == 0) {
                        myAlert('s', data['message']);
                        // $('#header_save').addClass('disabled');
                        $('.btn-wizard').removeClass('disabled');
                        actionMasterAutoID = data['masterID'];
                        

                        $('[href=#step2]').tab('show');
                        //fetch_persional_action_det();
                        fetch_persional_view();
                    }
                    stopLoad();
                    
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
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

//ok....
    function fetch_persional_view(){
        if (actionMasterAutoID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'id': actionMasterAutoID},
                    url: "<?php echo site_url('Employee/fetch_personal_action_mse_view'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            $('#action_view').empty();
                            $('#action_view').html(data);
                        }
                        fetch_personal_action_head();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

//.....
    function fetch_personal_action_head() {
        if (actionMasterAutoID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': actionMasterAutoID},
                    url: "<?php echo site_url('Employee/fetch_personal_Action_header_mse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            //$('#segmentID').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#remarks').val(data['Remarks']);
                            $('#actionDate').val(data['documentDate']);
                            $('#empName').val(data['empID']).change();
                            $('#actionType').val(data['actionType']).change();
                            empID = data['empID'];

                            // $('[href=#step2]').tab('show');
                            // $('a[data-toggle="tab"]').removeClass('btn-primary');
                            // $('a[data-toggle="tab"]').addClass('btn-default');
                            // $('[href=#step2]').removeClass('btn-default');
                            // $('[href=#step2]').addClass('btn-primary');
                        }
                        stopLoad();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

        
    function move_confirm_tab() {
        $('[href=#step3]').tab('show');
        attachment_modal_personalAction(actionMasterAutoID, "Personal Action", "PAA");
    }

//..
    function fetch_persional_action_det() {
        if (actionMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id': actionMasterAutoID},
                url: "<?php echo site_url('Employee/fetch_personal_Action_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body').empty();
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    } else {
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + value['fieldType'] + '</td><td>' + value['currentValue'] + '</td><td >' + value['NewValue'] + '</td></tr>');
                        });
                    }
                    stopLoad();
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

//.....
    function load_conformation_mse() {
        if (actionMasterAutoID) {
            $("#documentSystemCode").val(actionMasterAutoID);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'id': actionMasterAutoID, 'html': true},
                url: "<?php echo site_url('Employee/load_personal_action_conformation_mse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    attachment_modal_personalAction(actionMasterAutoID, "Personal Action", "PAA");
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

//..
    function confirmation_mse() {
        if (actionMasterAutoID) {
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
                        data: {'id': actionMasterAutoID},
                        url: "<?php echo site_url('Employee/personal_action_confirmation_mse'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if (data[0] == 's') {
                                fetchPage('system/hrm/personal_application_mse', actionMasterAutoID, 'Personal Action / Payroll Authorization Form');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
  
//..
    function save_draft() {
        if (actionMasterAutoID) {
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
                    fetchPage('system/hrm/personal_application_mse', actionMasterAutoID, 'Personal Action / Payroll Authorization Form');
                });
        }
    }


//...
    function delete_item_mse(id) {
        if (actionMasterAutoID) {
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
                        data: {'id': id},
                        url: "<?php echo site_url('Employee/delete_personal_action_mse'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_persional_action_det();
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

//..
    function attachment_modal_personalAction(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#personal_application_mse_attachment_lable').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");
                    $('#personal_action_mse_attachment').empty();
                    $('#personal_action_mse_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

//..
    function personal_action_document_upload() {
        var formData = new FormData($("#pa_attachment_uplode_form")[0]);
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
                    attachment_modal_personalAction($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val());
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


    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }


</script>