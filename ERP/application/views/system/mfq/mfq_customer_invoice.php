<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_customer_invoice');
$customer_arr = all_mfq_customer_drop(false);
echo head_page($title, true);
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
]; ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

<div id="filter-panel" class="collapse filter-panel">
    <form role="form" id="mfq_invoice_filter" class="" autocomplete="off">
        <input type="hidden" name="<?= $token_details['name']; ?>" value="<?= $token_details['hash']; ?>"/>
        <div class="row">
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name'); ?> </label> <br>
                <!--Customer Name-->
                <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Segment </label> <br>
                <?php echo form_dropdown('SegmentID[]', fetch_mfq_segment(true, false), '', 'class="form-control" id="SegmentID" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label> Job Code :</label><br>
                <?php echo form_dropdown('mfq_job[]', get_mfq_job(), '', 'multiple required id="mfq_job" onchange="oTable.draw()" class="form-control input-sm"'); ?>
            </div>
        </div>
    </form>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-7">

    </div>
    <div class="col-md-5 text-right">
        <button type="button" style="" class="btn btn-primary-new size-sm "
                onclick="fetchPage('system/mfq/mfq_add_customer_invoice',null,'<?php echo $this->lang->line('manufacturing_add_customer_invoice') ?>','MCINV');">
            <i
                    class="fa fa-plus"></i>
            <?php echo $this->lang->line('manufacturing_new_customer_invoice') ?><!--New Customer Invoice-->
        </button>
        <button type="button" data-text="Add" id="btnAdd"
                onclick="mfq_invoice_excel()"
                class="btn btn-sm btn-success-new size-sm">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            <?php echo $this->lang->line('common_excel') ?><!--Excel-->
        </button>
    </div>
</div>
<div id="" style="margin-top: 10px">
    <div class="table-responsive">
        <table id="customer_invoice_table" class="table table-striped table-condensed" width="100%">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th class="text-uppercase" style="min-width: 12%">
                    <?php echo $this->lang->line('manufacturing_invoice_code') ?><!--INVOICE CODE--></th>
                <th class="text-uppercase" style="min-width: 30%">
                    <?php echo $this->lang->line('common_details') ?><!--DETAILS--></th>
                <th class="text-uppercase" style="width: 5%;">SEGMENT</th>
                <th class="text-uppercase" style="min-width: 10%">
                    <?php echo $this->lang->line('common_total_value') ?><!--TOTAL VALUE--></th>
                <th class="text-uppercase" style="min-width: 3%;width: 362px">Job Number</th>
                <th class="text-uppercase" style="min-width: 10%">
                    <?php echo $this->lang->line('common_confirmed') ?><!--CONFIRMED--></th>
                <th style="min-width: 5%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<!-- <div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="z-index: 1000000000;">
    <div class="modal-dialog" role="document" id="doc-view-modal-dialog" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
</div>
</div>
</div>
</div>

<div class="modal fade" id="customer_inquiry_print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-capitalize" id="myModalLabel">
                    <?php echo $this->lang->line('manufacturing_customer_invoice') ?><!--Customer INVOICE--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                <li id="TabViewActivation_view" class="active"><a href="#home-v" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                                <li id="TabViewActivation_attachment">
                                    <a href="#profile-v" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="home-v">
                                    <div id="print"></div>
                                </div>
                                <div class="zx-tab-pane" id="profile-v">
                                    <div id="loadPageViewAttachment" class="col-md-8">
                                        <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                                            &nbsp <strong>
                                                <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                            <br><br>
                                            <table class="table table-striped table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                                    <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                                </tr>
                                                </thead>
                                                <tbody id="View_attachment_modal_body" class="no-padding">
                                                <tr class="danger">
                                                    <td colspan="5" class="text-center">
                                                        <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachment_pull_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Pull Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form_pull_attachments">
                            <input class="hidden" name="invoiceID_attach" id="invoiceID_attach">
                            <div class="table-responsive">
                                <table id="" class="table table-condensed table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">Document</th>
                                        <th style="min-width: 12%">File Name</th>
                                        <th style="min-width: 12%">Description</th>
                                        <th>Pull</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table_body_attachment_pull"></tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="pull_attachment()">Merge Attachment</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachment_modal_MCINV" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">INVOICE</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="MCINV_attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                <!--Description-->
                                <input type="hidden" class="form-control" id="documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="documentID" name="documentID">
                                <input type="hidden" class="form-control" id="document_name" name="document_name">
                                <input type="hidden" class="form-control" id="confirmYNadd" name="confirmYNadd">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
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
                          <button type="button" class="btn btn-default" onclick="document_uplode_MCINV()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="MCINV_attachment_modal_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing
                    <button class="btn btn-default pull-right" onclick="print_tracing_view()"><i
                                class="fa fa-print"></i></button>
            </div>
            </h4>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var oTable;
    var allSelected = 0;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_customer_invoice', 'Test', 'Customer Invoice');
        });
        $("#search_cancel").hide();
        customer_invoice_table();
        $(".filter").change(function () {
            oTable.draw();
            $("#search_cancel").show();
        });

        $("#search_cancel").click(function () {
            $(".filter").val('');
            oTable.draw();
            $(this).hide();
        });
        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#SegmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            onSelectAll: function () {
                allSelected = 1;
            },
            onChange: function () {
                allSelected = 0;
            },
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#mfq_job").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

    });


    function customer_invoice_table() {
        oTable = $('#customer_invoice_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            /*"bStateSave": true,*/
            "sAjaxSource": "<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "columnDefs": [{"targets": [6], "orderable": false}],
            "aoColumns": [
                {"mData": "invoiceAutoID"},
                {"mData": "invoiceCode"},
                {"mData": "invoice_detail"},
                {"mData": "segmentcode"},
                {"mData": "total_value"},
                {"mData": "job_codes"},
                {"mData": "confirmed"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
                aoData.push({"name": "mfqsegment", "value": $("#SegmentID").val()});
                aoData.push({"name": "segmentallSelected", "value": allSelected});
                aoData.push({name: 'jobID', value: $('#mfq_job').val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function viewDocument(invoiceAutoID) {
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                invoiceAutoID: invoiceAutoID
            },
            url: "<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                $("#print").html(data);
                load_attachment_view(invoiceAutoID);

                $("#customer_inquiry_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_attachment_view(invoiceAutoID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_CustomerInvoice/fetch_customer_invoice_attachment_print"); ?>',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            success: function (data) {
                $('#attachment_modal_label').html('' +
                    '<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "'s Attachments");
                $('#View_attachment_modal_body').empty();
                $('#View_attachment_modal_body').append('' + data + '');
                //$("#View_attachment_modal_body").modal({backdrop: "static", keyboard: true});
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });
    }

    function delete_attachments_mfq(id, fileName, documentID) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName, 'documentID': documentID},
                    url: "<?php echo site_url('MFQ_CustomerInvoice/delete_attachments_mfq'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                            /*Deleted Successfully*/
                            $('#' + id).hide();
                        } else {
                            myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                            /*Deletion Failed*/
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function referbackCustomerInquiry(ciMasterID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
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
                    data: {'ciMasterID': ciMasterID},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/referback_customer_inquiry'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            customer_inquiry_table()
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referbackCustomerInquiry_cus(ciMasterID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
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
                    data: {'ciMasterID': ciMasterID},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/referback_customer_inquiry_cus'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            customer_inquiry_table()
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function attachment_pull_modal(ciMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'ciMasterID': ciMasterID},
            url: "<?php echo site_url('MFQ_CustomerInvoice/fetch_attachment_for_invoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!$.isEmptyObject(data)) {
                    $("#table_body_attachment_pull").html("");
                    $.each(data, function (k, v) {
                        var checkbox = '';
                        if (!$.isEmptyObject(v.pulledID)) {
                            checkbox = 'checked';
                        }
                        $('#invoiceID_attach').val(ciMasterID);
                        $("#table_body_attachment_pull").append("<tr>" +
                            "<td><input class='hidden' id='attachmentID_" + v.attachmentID + "' name='attachmentID[]' value='" + v.attachmentID + "'><input class='hidden' id='pulledID_" + v.attachmentID + "' name='pulledID[]' value='" + v.pulledID + "'>" + v.documentName + "</td>" +
                            "<td><a target='_blank' href='" + v.link + "' >" + v.myFileName + "</a></td>" +
                            "<td>" + v.attachmentDescription + "</td>" +
                            '<td><div style="text-align: center;"><div class="skin skin-square item-iCheck"><div class="skin-section extraColumns"><input id="selectItem_' + v.attachmentID + '" type="checkbox" ' + checkbox + ' class="columnSelected"  value="' + v.attachmentID + '"><label for="checkbox">&nbsp;</label> </div></div></div></td>' +
                            "</tr>");
                    });
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                    $("#attachment_pull_modal").modal({backdrop: "static"});
                } else {
                    myAlert('w', 'No Attachments added to Pulled Documents!');
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function pull_attachment() {
        var selected = [];
        var attachmentID = [];
        var pulledID = [];
        var invoiceID = $('#invoiceID_attach').val();
        $('#table_body_attachment_pull input:checked').each(function () {
            selected.push($(this).val());
            attachmentID.push($('#attachmentID_' + $(this).val()).val());
            pulledID.push($('#pulledID_' + $(this).val()).val());
        });

        var data = $("#form_pull_attachments").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'selected': selected, 'invoiceID': invoiceID, 'attachmentID': attachmentID},
            url: "<?php echo site_url('MFQ_CustomerInvoice/save_attachment_for_invoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message']);
                if (data['type'] == 's') {
                    $("#attachment_pull_modal").modal('hide');
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function mfq_invoice_excel() {
        var form = document.getElementById('mfq_invoice_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice_excel'); ?>';
        form.submit();
    }

    function attachment_modal_MCINV(documentSystemCode, document_name, documentID, confirmedYN) {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        $('#confirmYNadd').val(confirmedYN);
        $('#remove_id').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                success: function (data) {
                    $('#MCINV_attachment_modal_body').empty();
                    $('#MCINV_attachment_modal_body').append('' + data + '');
                    $("#attachment_modal_MCINV").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function document_uplode_MCINV() {
        var formData = new FormData($("#MCINV_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_CustomerInvoice/upload_attachment_for_invoice'); ?>",
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
                    attachment_modal_MCINV($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
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

    function trace_mfq_document(invoiceAutoID, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': invoiceAutoID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/trace_MCINV_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(invoiceAutoID, DocumentID);
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_document_tracing(id, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'purchaseOrderID': id,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#mcontainer").empty();
                $("#mcontainer").html(data);
                $("#tracingId").val(id);
                $("#tracingCode").val(DocumentID);

                $("#tracing_modal").modal('show');
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function deleteDocumentTracing() {
        var purchaseOrderID = $("#tracingId").val();
        var DocumentID = $("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'purchaseOrderID': purchaseOrderID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#tracing_modal").modal('hide');
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function referBackCustomerInovice(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mfqInvoiceAutoID': id},
                    url: "<?php echo site_url('MFQ_CustomerInvoice/referback_customer_invoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            customer_invoice_table();//refreshing customer invoice table.
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>