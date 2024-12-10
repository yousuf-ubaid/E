<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
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

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
</style>
<?php
if (!empty($header)) {
?>
<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
    <li><a href="#notes" onclick="project_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes</a></li>
    <li><a href="#files" onclick="project_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Files</a>
    </li>
</ul>
<input type="hidden" id="edit_customerOrderID" value="<?php echo $header['customerOrderID'] ?>">
<div class="tab-content">
    <div class="tab-pane active" id="about">
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Customer Order Details</h2>
                </header>
            </div>
        </div>
        <table class="property-table">
            <tbody>
            <tr>
                <td class="ralign"><span class="title">Order ID</span></td>
                <td><span class="tddata"><?php echo $header['customerOrderCode'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Currency</span></td>
                <td><span class="tddata"><?php echo $header['CurrencyCode'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Inquery Date</span></td>
                <td><span class="tddata"><?php echo $header['documentDate'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Expiry Date</span></td>
                <td><span class="tddata"><?php echo $header['expiryDate'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Customer Name</span></td>
                <td><span class="tddata"><?php echo $header['CustomerName'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Customer Ref No</span></td>
                <td><span class="tddata"><?php echo $header['CustomerName'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Phone No</span></td>
                <td><span class="tddata"><?php echo $header['contactPersonNumber'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Address</span></td>
                <td><span class="tddata"><?php echo $header['CustomerAddress'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Reference Number</span></td>
                <td><span class="tddata"><?php echo $header['referenceNumber'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Narration</span></td>
                <td><span class="tddata"><?php echo $header['narration'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">BID Start Date</span></td>
                <td><span class="tddata"><?php echo $header['bidStartDate'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">BID End Date</span></td>
                <td><span class="tddata"><?php echo $header['bidEndDate'] ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Type</span></td>
                <td><span class="tddata"><?php echo ($header['isBackToBack'] == 1) ? 'Back to Back' : 'Standard'; ?></span></td>
            </tr>
            <tr>
                <td class="ralign"><span class="title">Supplier Name</span></td>
                <td><span class="tddata"><?php echo $header['supplierName'] ?></span></td>
            </tr>

            <tr>
                <td class="ralign"><span class="title">Status</span></td>
                <td><span class="tddata"><span class="label"
                                               style="background-color: <?php echo $header['backgroundColor'] ?>; color: <?php echo $header['fontColor'] ?>; font-size: 11px;"><?php echo $header['statusDescription'] ?></span></span>
                </td>
            </tr>
            </tbody>
        </table>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Customer Order Items</h2>
                </header>
            </div>
        </div>
        <?php
        if (!empty($orderitem)) { ?>
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr class="task-cat-upcoming">
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Name</td>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">QTY</td>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Unit
                            Price
                        </td>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Total
                            Price
                        </td>
                    </tr>
                    <?php
                    $x = 1;
                    foreach ($orderitem as $row) {
                        ?>
                        <tr>
                            <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                            <td class="mailbox-star" width="5%"><?php echo $row['itemSystemCode'] ?></td>
                            <td class="mailbox-star"><?php echo $row['itemName'] ?></td>
                            <td class="mailbox-star"><?php echo $row['requestedQty'] ?></td>
                            <td class="mailbox-star"
                                style="text-align: right"><?php echo number_format($row['unitAmount'], 2) ?></td>
                            <td class="mailbox-star"
                                style="text-align: right"><?php echo number_format($row['totalAmount'], 2) ?></td>
                        </tr>
                        <?php
                        $x++;
                    }
                    ?>
                    </tbody>
                </table><!-- /.table -->
            </div>
            <?php
        } else { ?>
            <br>
            <div class="search-no-results">THERE ARE NO CUSTOMER ORDERED ITEMS TO DISPLAY.</div>
            <?php
        }
        ?>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>GENERATED RFQ</h2>
                </header>
            </div>
        </div>
        <?php
        if (!empty($detailrfq)) { ?>
            <div class="row">
                <div class="col-md-10">
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                            <tr>
                                <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">#</td>
                                <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">Code</td>
                                <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">Supplier Name</td>
                                <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d; text-align: center">
                                    Action
                                </td>
                            </tr>
                            <?php
                            $z = 1;
                            foreach ($detailrfq as $val) {
                                ?>
                                <tr>
                                    <td class="mailbox-name">
                                        <?php echo $z; ?>
                                    </td>
                                    <td class="mailbox-star"><?php echo $val['supplierSystemCode']; ?></td>
                                    <td class="mailbox-star"><?php echo $val['supplierName']; ?></td>
                                    <td class="mailbox-star"><span class="pull-right"><div class="actionicon"><a
                                                    target="_blank"
                                                    onclick="view_rfq_printModel(<?php echo $val['inquiryMasterID']; ?>,<?php echo $val['supplierID']; ?>)"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                                        data-original-title="View" style="color: white;"></span></a>
                                            </div></span></td>
                                </tr>
                                <?php
                                $z++;
                            }
                            ?>

                            </tbody>
                        </table><!-- /.table -->
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <br>
            <div class="search-no-results">THERE ARE NO GENERATED RFQ TO DISPLAY.</div>
            <?php
        } ?>


    </div>
    <div class="tab-pane" id="notes">
        <br>

        <div class="row" id="show_add_notes_button">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Order Notes </h4></div>
            <div class="col-md-4">
                <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Add Note
                </button>
            </div>
        </div>
        <br>
        <?php echo form_open('', 'role="form" id="frm_customerOrder_add_notes"'); ?>
        <input type="hidden" name="customerOrderID" value="<?php echo $header['customerOrderID']; ?>">
        <input type="hidden" name="paath" value="project">

        <div id="show_add_notes" class="hide">
            <div class="row">
                <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="description"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <button class="btn btn-primary" type="submit">Add</button>
                    <button class="btn btn-danger" type="button" onclick="close_add_note()">Close</button>
                </div>
                <div class="form-group col-sm-6" style="margin-top: 10px;">
                    &nbsp
                </div>
            </div>
        </div>
        </form>
        <div id="show_all_notes"></div>
    </div>
    <div class="tab-pane" id="files">
        <br>

        <div class="row" id="show_add_files_button">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Customer Order Files </h4></div>
            <div class="col-md-4">

                <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Add Files
                </button>
            </div>
        </div>
        <div class="row hide" id="add_attachemnt_show">
            <?php echo form_open_multipart('', 'id="customerOrder_attachment_uplode_form" class="form-inline"'); ?>
            <div class="col-sm-10" style="margin-left: 3%">
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="customerOrderattachmentDescription"
                               name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                        <input type="hidden" class="form-control" id="documentID" name="documentID" value="3">
                        <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                               value="CustomerOrder">
                        <input type="hidden" class="form-control" id="customerOrder_documentAutoID"
                               name="documentAutoID"
                               value="<?php echo $header['customerOrderID']; ?>">
                    </div>
                </div>
                <div class="col-sm-8" style="margin-top: -8px;">
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
                    <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                            class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                    </form>
                </div>
            </div>

        </div>
        <br>

        <div id="show_all_attachments"></div>
    </div>
    <?php
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {

            $('#frm_customerOrder_add_notes').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    //campaign_name: {validators: {notEmpty: {message: 'Campaign Name is required.'}}},
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srm_master/add_customer_order_notes'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            close_add_note();
                            project_notes();
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

        });

        function project_notes() {
            var customerOrderID = $('#edit_customerOrderID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {customerOrderID: customerOrderID},
                url: "<?php echo site_url('srm_master/load_customer_order_all_notes'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_all_notes').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function show_add_note() {
            $('#show_all_notes').addClass('hide');
            $('#show_add_notes_button').addClass('hide');
            $('#show_add_notes').removeClass('hide');
            $('#frm_customerOrder_add_notes')[0].reset();
            $('#frm_customerOrder_add_notes').bootstrapValidator('resetForm', true);
        }

        function close_add_note() {
            $('#show_add_notes').addClass('hide');
            $('#show_all_notes').removeClass('hide');
            $('#show_add_notes_button').removeClass('hide');
        }

        function project_attachments() {
            var customerOrderID = $('#edit_customerOrderID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {customerOrderID: customerOrderID},
                url: "<?php echo site_url('srm_master/load_order_multiple_attachemts'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_all_attachments').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function show_add_file() {
            $('#add_attachemnt_show').removeClass('hide');
        }

        function document_uplode() {
            var formData = new FormData($("#customerOrder_attachment_uplode_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('srm_master/attachement_upload'); ?>",
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
                        $('#add_attachemnt_show').addClass('hide');
                        $('#remove_id').click();
                        $('#opportunityattachmentDescription').val('');
                        project_attachments();
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }


        function delete_srm_attachment(id, fileName) {
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
                        data: {'attachmentID': id, 'myFileName': fileName},
                        url: "<?php echo site_url('srm_master/delete_srm_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                project_attachments();
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
    </script>
