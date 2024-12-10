<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('community_ngo_helper');

$segment_arr = fetch_all_segment();
$currency_arr = all_currency_new_drop();
$periodtype_arr = periodType_without_Daily();
$financeyear_arr = all_financeyear_drop(true);

?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" onclick="fetch_collection_details();"
           data-toggle="tab">Step 1 - Collection Details</a>
        <a class="btn btn-default btn-wizard disabled" href="#step2"
           data-toggle="tab">Step 2 - Collection Member Setup</a>
    </div>

    <div class="tab-content" style="margin-top: 2%">

        <div id="step1" class="tab-pane active">

            <div class="row">
                <div class="col-md-12 pull-right">
                    <div class="">
                        <div class="col-md-12 no-padding" style="margin-bottom: 10px;">
                            <button type="button" onclick="collection_details_modal()"
                                    class="btn btn-primary pull-right standedbtn"><i
                                    class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div id="collection_details_body">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('communityngo_collection_Financial_Year'); ?></th>
                        <th><?php echo $this->lang->line('communityngo_collection_PeriodType'); ?></th>
                        <th><?php echo $this->lang->line('communityngo_collection_Segment'); ?></th>
                        <th><?php echo $this->lang->line('communityngo_collection_Currency'); ?></th>
                        <th class="TH_Amount"><?php echo $this->lang->line('communityngo_collection_Amount'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="colDetail_table_body">
                    <tr class="danger">
                        <td colspan="7" class="text-center"><b>
                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot id="colDetail_table_tfoot">

                    </tfoot>
                </table>
            </div>

        </div>

        <div id="step2" class="tab-pane">
            <br>
            <form method="POST" id="collection_member_form" class="form-horizontal" action=""
                  name="collection_member_form">
                <div id="filters">

                </div>
            </form>

            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev1">
                    <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            </div>
        </div>
    </div>


    <div aria-hidden="true" role="dialog" id="collection_details_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo $this->lang->line('communityngo_collection_details'); ?></h4>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" id="collection_details_form"
                          class="form-horizontal">
                        <input type="hidden" value="" id="CollectionDetailID"
                               name="CollectionDetailID"/>

                        <div class="form-group">
                            <label class="col-md-3 control-label"
                                   for="companyFinanceYearID"><?php echo $this->lang->line('communityngo_collection_Financial_Year'); ?></label>
                            <div class="col-md-7">
                                <?php echo form_dropdown('companyFinanceYearID', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control select2" id="companyFinanceYearID" required '); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"
                                   for="PeriodTypeID"><?php echo $this->lang->line('communityngo_collection_PeriodType'); ?></label>

                            <div class="col-md-7">
                                <?php echo form_dropdown('PeriodTypeID', $periodtype_arr, '', 'class="form-control select2" id="PeriodTypeID" required '); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"
                                   for="segmentID"><?php echo $this->lang->line('communityngo_collection_Segment'); ?></label>
                            <div class="col-md-7">
                                <?php echo form_dropdown('segmentID', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segmentID" required '); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"
                                   for="transactionCurrencyID"><?php echo $this->lang->line('communityngo_collection_Currency'); ?></label>
                            <div class="col-md-7">
                                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                            </div>
                        </div>

                        <div class="form-group AmountDiv">
                            <label class="col-md-3 control-label"
                                   for="Amount"><?php echo $this->lang->line('communityngo_collection_Amount'); ?></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control " id="Amount" name="Amount">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="save_collection_details()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save Changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">


        $(document).ready(function () {
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                CollectionMasterID = p_id;
                fetch_collection_details();
            }

            $('.select2').select2();

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_collection_management', '', 'Collection Setup');
            });

            if (CollectionType == 3) {
                $('.TH_Amount').addClass('hide');
            } else {
                $('.TH_Amount').removeClass('hide');
            }

        });

        function fetch_collection_details() {
            if (CollectionMasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'CollectionMasterID': CollectionMasterID},
                    url: "<?php echo site_url('CommunityNgo/fetch_collection_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        $('#colDetail_table_body').empty();

                        $('#colDetail_table_body,#colDetail_table_tfoot').empty();
                        x = 1;
                        if (jQuery.isEmptyObject(data['detail'])) {

                            if (CollectionType == 3) {
                                $('#colDetail_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            } else {
                                $('#colDetail_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            }

                        } else {
                            Amount = 0;
                            $.each(data['detail'], function (key, value) {

                                if (CollectionType == 3) {
                                    currency_decimal = value['transactionCurrencyDecimalPlaces'];
                                    currency = value['transactionCurrency'];
                                    var segMentCode = value['segmentCode'];
                                    if (value['segmentCode'] == null) {
                                        segMentCode = '-';
                                    }
                                    $('#colDetail_table_body').append('<tr><td class="text-center">' + x + '</td><td class="text-center">' + value['companyFinanceYear'] + '</td><td class="text-center">' + value['PeriodDescription'] + '</td><td class="text-center">' + segMentCode + '</td><td class="text-center">' + value['transactionCurrency'] + '</td><td class="text-right"><a title="Member Adding" onclick="get_collection_member_details(' + value['CollectionDetailID'] + ');"><span class="glyphicon glyphicon-cog"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a title="Edit Period Setup" onclick="edit_details(' + value['CollectionDetailID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a title="Delete Period Setup" onclick="delete_details(' + value['CollectionDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                                    x++;

                                } else {
                                    currency_decimal = value['transactionCurrencyDecimalPlaces'];
                                    currency = value['transactionCurrency'];
                                    var segMentCode = value['segmentCode'];
                                    if (value['segmentCode'] == null) {
                                        segMentCode = '-';
                                    }
                                    $('#colDetail_table_body').append('<tr><td class="text-center">' + x + '</td><td class="text-center">' + value['companyFinanceYear'] + '</td><td class="text-center">' + value['PeriodDescription'] + '</td><td class="text-center">' + segMentCode + '</td><td class="text-center">' + value['transactionCurrency'] + '</td><td class="text-right">' + parseFloat(value['Amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a title="Member Adding" onclick="get_collection_member_details(' + value['CollectionDetailID'] + ');"><span class="glyphicon glyphicon-cog"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a title="Edit Period Setup" onclick="edit_details(' + value['CollectionDetailID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a title="Delete Period Setup" onclick="delete_details(' + value['CollectionDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                                    x++;
                                    Amount += (parseFloat(value['Amount']));
                                }

                            });
                            if (CollectionType == 3) {
                            } else {
                                $('#colDetail_table_tfoot').append('<tr><td colspan="5" class="text-right"> <?php echo $this->lang->line('common_total');?>(' + currency + ' ) </td><td class="text-right total">' + parseFloat(Amount).formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');
                            }

                            $('.btn-wizard').addClass('disabled');
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.prev1').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
            fetch_collection_details();
            $('.btn-wizard').addClass('disabled');
        });

        function delete_details(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'CollectionDetailID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            fetch_collection_details();
                            stopLoad();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function edit_details(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'CollectionDetailID': id},
                        url: "<?php echo site_url('CommunityNgo/edit_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            $('#collection_details_form')[0].reset();
                            $('#CollectionDetailID').val(data['CollectionDetailID']);

                            $('#companyFinanceYearID').val(data['companyFinanceYearID']).change();
                            $('#PeriodTypeID').val(data['PeriodTypeID']).change();
                            $('#segmentID').val(data['segmentID']).change();
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();

                            if (CollectionType == 3) {
                                $('.AmountDiv').addClass('hide');
                            } else {
                                $('.AmountDiv').removeClass('hide');
                                $('#Amount').val(data['Amount']);
                            }

                            $("#collection_details_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });

        }

        function collection_details_modal() {

            if (CollectionMasterID) {

                if (CollectionType == 3) {
                    $('.AmountDiv').addClass('hide');
                } else {
                    $('.AmountDiv').removeClass('hide');
                }

                $('#companyFinanceYearID').val('<?php echo $this->common_data['company_data']['companyFinanceYearID'];?>').change();
                $('#PeriodTypeID').val('').change();
                $('#transactionCurrencyID').val('<?php echo $this->common_data['company_data']['company_default_currencyID'];?>').change();
                $('#segmentID').val('').change();
                //   $('#Amount').val('');
                $('#collection_details_form')[0].reset();
                $('#CollectionDetailID').val('');
                $("#collection_details_modal").modal({backdrop: "static"});
            }
        }

        function save_collection_details() {
            var $form = $('#collection_details_form');
            var data = $form.serializeArray();
            data.push({'name': 'CollectionMasterID', 'value': CollectionMasterID});
            data.push({'name': 'CollectionType', 'value': CollectionType});
            data.push({'name': 'PeriodType_des', 'value': $('#PeriodTypeID option:selected').text()});
            data.push({'name': 'segment_des', 'value': $('#segmentID option:selected').text()});
            data.push({'name': 'companyFinanceYear', 'value': $('#companyFinanceYearID option:selected').text()});
            data.push({'name': 'transactionCurrency', 'value': $('#transactionCurrencyID option:selected').text()});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_collection_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_collection_details();
                    }

                    $('#collection_details_form')[0].reset();
                    $('#collection_details_modal').modal('hide');

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        // load member setup
        function get_collection_member_details(CollectionDetailID) {
            if (CollectionDetailID) {
                $.ajax({
                    async: true,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'CollectionMasterID': CollectionMasterID,
                        'CollectionType': CollectionType,
                        'CollectionDetailID': CollectionDetailID
                    },
                    url: "<?php echo site_url('CommunityNgo/get_collection_member_details'); ?>",
                    beforeSend: function () {
                        $("#filters").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i> Loading</div>");
                    },
                    success: function (data) {

                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');

                        $("#filters").html("");
                        $("#filters").html(data);

                        $('.submitBtn').addClass('hide');
                        $('.backBtn').addClass('hide');

                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }


    </script>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 2/23/2018
 * Time: 11:44 AM
 */