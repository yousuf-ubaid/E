<?php echo head_page('', false);
$pageID = trim($this->input->post('page_id'));
$GLAutoID = $pageID;
$date_format_policy = date_format_policy();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<!--<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Bank Rec Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_journal_entry_detail()" data-toggle="tab">Step 2 - Bank Rec Detail</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()" data-toggle="tab">Step 3 - Bank Rec Confirmation</a>
</div><hr>-->


<div id="step1" class="active">


    <div class="row">

        <div class="col-sm-12" id="load_generated_table">

        </div>

    </div>
</div>


<script type="text/javascript">

    $(document).ready(function () {
        Inputmask().mask(document.querySelectorAll("input"));
        $('.headerclose').click(function(){
            fetchPage('system/bank_register/erp_bank_register','','Bank/Cash Register','Bank Register','');
        });
    });
        GLAutoID = <?php echo json_encode(trim($GLAutoID)); ?>;
        data_arr=<?php echo json_encode($data_arr)?>

        if (GLAutoID) {
            generateload();
        }
        /*   number_validation();*/
        $('#jv_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
            excluded: [':disabled'],
            fields: {
                gl_code: {validators: {notEmpty: {message: 'GL code is required.'}}},
                amount: {validators: {notEmpty: {message: 'Amount is required.'}}},
                segment_gl: {validators: {notEmpty: {message: 'Segment is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'JVMasterAutoId', 'value': JVMasterAutoId});
            data.push({'name': 'JVDetailAutoID', 'value': JVDetailAutoID});
            data.push({'name': 'gl_code_des', 'value': $('#gl_code option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Journal_entry/save_gl_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    debitNoteDetailsID = null;
                    refreshNotifications(true);
                    stopLoad();
                    $('#jv_detail_modal').modal('hide');
                    if (data['status']) {
                        fetch_journal_entry_detail();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



        function generateload() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {GLAutoID: GLAutoID,data_arr:data_arr},
                url: "<?php echo site_url('Bank_rec/load_bank_register_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_generated_table').html(data);

                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }



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
                        mySelect.append($('<option></option>').val(text['dateFrom'] + '|' + text['dateTo']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_journal_entry_header() {
        if (JVMasterAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'JVMasterAutoId': JVMasterAutoId},
                url: "<?php echo site_url('Journal_entry/load_journal_entry_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#JVType').val(data['JVType']);
                        $("#JVdate").val(data['JVdate']);
                        $('#JVNarration').val(data['JVNarration']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        $('#referenceNo').val(data['referenceNo']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['FYPeriodDateFrom'] + '|' + data['FYPeriodDateTo']);
                        fetch_journal_entry_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
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

    function fetch_journal_entry_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'JVMasterAutoId': JVMasterAutoId},
            url: "<?php echo site_url('Journal_entry/fetch_journal_entry_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                $('#gl_table_body,#gl_table_tfoot').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    creditAmount = 0;
                    debitAmount = 0;
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $.each(data['detail'], function (key, value) {
                        $('#gl_table_body').append('<tr><td>' + x + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(value['creditAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['debitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['JVDetailAutoID '] + ',\'' + value['GLDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="delete_item(' + value['JVDetailAutoID'] + ',\'' + value['GLDescription'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                        creditAmount += (parseFloat(value['creditAmount']));
                        debitAmount += (parseFloat(value['debitAmount']));
                    });
                    $('#gl_table_tfoot').append('<tr><td colspan="4" class="text-right"> Total (' + data['currency']['transactionCurrency'] + ' ) </td><td class="text-right total">' + parseFloat(creditAmount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right total">' + parseFloat(debitAmount).formatMoney(currency_decimal, '.', ',') + '</td></tr>');
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function jv_detail_modal() {
        if (JVMasterAutoId) {
            $('#jv_detail_form')[0].reset();
            $('#jv_detail_form').bootstrapValidator('resetForm', true);
            $("#jv_detail_modal").modal({backdrop: "static"});
        }
    }

    function load_conformation() {
        if (JVMasterAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'JVMasterAutoId': JVMasterAutoId, 'html': true},
                url: "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    //refreshNotifications(true);
                }
            });
        }
    }


    function save_draft() {
        if (JVMasterAutoId) {
            swal({
                    title: "Are you sure?",
                text:"You want to save this file !",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/finance/Journal_entry_management', 'Test', 'Journal Entry');
                });
        }
        ;
    }

    function delete_item(id, value) {
        if (JVMasterAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record !",
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
                        data: {'JVDetailAutoID': id},
                        url: "<?php echo site_url('Journal_entry/delete_Journal_entry_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            fetch_journal_entry_detail();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function edit_item(id, value) {
        if (JVMasterAutoId) {
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
                        data: {'JVDetailAutoID ': id},
                        url: "<?php echo site_url('Journal_entry/load_material_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            JVDetailAutoID = data['JVDetailAutoID '];
                            $('#search').val(data['itemDescription'] + " (" + data['itemPrimaryCode'] + ")");
                            fetch_related_uom(data['itemunitOfMeasure'], data['unitOfMeasureIssued']);
                            $('#quantityRequested').val(data['qtyIssued']);
                            $('#itemSystemCode').val(data['itemPrimaryCode']);
                            $('#itemAutoID').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment').val(data['comments']);
                            $('#remarks').val(data['remarks']);
                            $('#defaultUOM').val(data['itemunitOfMeasure']);
                            $("#jv_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
        ;
    }
</script>