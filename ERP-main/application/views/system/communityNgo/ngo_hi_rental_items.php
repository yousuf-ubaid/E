<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('communityngo_rentalItems');
echo head_page($title, false);
$this->load->helper('community_ngo_helper');

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false);

?>

    <div class="row">
        <div class="col-md-7">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span>
                        <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--></td>
                    <td><span class="label label-danger">&nbsp;</span>
                        <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed--></td>
                </tr>
            </table>
        </div>
        <div class="col-md-2 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/communityNgo/ngo_hi_rental_item_issue_new',null,'<?php echo $this->lang->line('communityngo_CreateItemRequest'); ?>','ITM');">
                <i
                    class="fa fa-plus"></i>
                <?php echo $this->lang->line('communityngo_CreateItemRequest'); ?><!--Create Item Request-->
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="item_issue_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="min-width: 4%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('communityngo_issueNo'); ?><!--Issue NO--></th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?><!--Total Value--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                <th style="min-width: 10%">
                    <?php echo $this->lang->line('communityngo_returned_status'); ?><!--Returned--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
        </table>
    </div>


    <div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         style="z-index: 1000000000;">
        <div class="modal-dialog" role="document" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-1">
                                <!-- Nav tabs -->
                                <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                    <li id="TabViewActivation_view" class="active"><a href="#home-v"
                                                                                      data-toggle="tab">
                                            <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                                </ul>
                            </div>
                            <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                <!-- Tab panes -->
                                <div class="zx-tab-content">
                                    <div class="zx-tab-pane active" id="home-v">
                                        <div id="loaddocumentPageView" class="col-md-12"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="returnPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         style="z-index: 1000000000;">
        <div class="modal-dialog" role="document" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="returnPageViewTitle">Modal title</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" style="">
                            <div id="loadreturnPageView" class="col-md-12"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-success ReturnsubmitBtn" onclick="return_confirmation()" id="ReturnsubmitBtn"
                            disabled>
                        <?php echo $this->lang->line('communityngo_return'); ?><!--Return--></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="return_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?php echo $this->lang->line('communityngo_return_items_detils'); ?><!--Returned Items--></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-condesed ">
                        <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 20%">
                                <?php echo $this->lang->line('communityngo_item_code'); ?><!--Item Code--></th>
                            <th style="width: 20%">
                                <?php echo $this->lang->line('communityngo_Description'); ?><!--Ddescription--></th>
                            <th style="width: 15%">
                                <?php echo $this->lang->line('communityngo_qty'); ?><!--Balance--></th>
                            <th style="width: 15%">
                                <?php echo $this->lang->line('communityngo_item_expected_return_date'); ?><!--Expected returned date--></th>
                            <th style="width: 15%">
                                <?php echo $this->lang->line('communityngo_returned_date'); ?><!--Returned Date--></th>
                            <th style="width: 15%">
                                <?php echo $this->lang->line('communityngo_returned_Qty'); ?><!--Returned Quantity--></th>
                            <th style="width: 15%">
                                <?php echo $this->lang->line('communityngo_balance'); ?><!--Balance--></th>
                        </tr>
                        </thead>
                        <tbody id="table_body_return">
                        <tr class="danger">
                            <td colspan="8" class="text-center"><b>No Records Found</b></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="save_returned_items()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_rental_items', 'RTL', 'Rental Items');
            });
            item_issue_table();

            $('#supplierPrimaryCode').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });

        function item_issue_table(selectedID=null) {
            Otable = $('#item_issue_table').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_purchase_request'); ?>",
                "aaSorting": [[0, 'desc']],
                "columnDefs": [],

                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if (parseInt(oSettings.aoData[x]._aData['itemIssueAutoID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');

                },
                "aoColumns": [
                    {"mData": "itemIssueAutoID"},
                    {"mData": "itemIssueCode"},
                    {"mData": "prq_detail"},
                    {"mData": "total_value"},
                    {"mData": "confirmed"},
                    {"mData": "returned"},
                    {"mData": "edit"}
                ],

                "fnServerData": function (sSource, aoData, fnCallback) {

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

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });

        function delete_item(id, value) {
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
                        data: {'itemIssueAutoID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_itemissuemaster'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            Otable.draw();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function PageView_modal(documentID, para1, para2, approval=0) {

            $("#home-v").addClass("active");
            $("#TabViewActivation_view").addClass("active");
            $('#loaddocumentPageView').html('');

            var siteUrl;
            var paramData = new Array();
            var title = '';
            var a_link;
            var de_link;

            $("#itemMasterSubTab_footer_div").html('');
            $(".itemMasterSubTab_footer").hide();

            switch (documentID) {

                case "RTL": // Rental Item Issue
                    siteUrl = "<?php echo site_url('CommunityNgo/load_item_issue_conformation'); ?>";
                    paramData.push({name: 'itemIssueAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('communityngo_rentalItems');?>";
                    /*Rental Items*/
                    break;

                default:
                    notification('Document ID is not set .', 'w');
                    /*Document ID is not set*/
                    return false;
            }
            paramData.push({name: 'html', value: true});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: paramData,
                url: siteUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    refreshNotifications(true);
                    $('#documentPageViewTitle').html(title);
                    $('#loaddocumentPageView').html(data);
                    $('#documentPageView').modal('show');
                    $("#a_link").attr("href", a_link);
                    $("#de_link").attr("href", de_link);
                    $('.review').removeClass('hide');
                    stopLoad();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

        function return_item_modal_old(ID) {

            $('#loadreturnPageView').html('');

            var paramData = new Array();

            var siteUrl = "<?php echo site_url('CommunityNgo/load_returned_item_details'); ?>";
            paramData.push({name: 'itemIssueAutoID', value: ID});
            var title = "<?php echo $this->lang->line('communityngo_return_items');?>";

            paramData.push({name: 'html', value: true});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: paramData,
                url: siteUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    refreshNotifications(true);
                    $('#returnPageViewTitle').html(title);
                    $('#loadreturnPageView').html(data);
                    $('#returnPageView').modal('show');


                    stopLoad();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }




        function return_confirmation() {
            var itemIssueAutoID = document.getElementById('Re_itemIssueAutoID').value;
            var isReturned = document.getElementById('isReturned').value;
            var ReturnedDate = document.getElementById('ReturnedDate').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data:   {  'itemIssueAutoID': itemIssueAutoID,
                'isReturned': isReturned,
                'ReturnedDate': ReturnedDate},
                url: "<?php echo site_url('CommunityNgo/return_item_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    /*receiptVoucherDetailAutoID = null;*/
                    refreshNotifications(true);
                    stopLoad();
                    myAlert(data[0], data[1]);

                   // if (data[0] == 's') {

                            $('#returnPageView').modal('hide');
                            fetchPage('system/CommunityNgo/ngo_hi_rental_items', itemIssueAutoID, 'Rental Items');
                   // }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function return_item_modal(itemIssueAutoID) {
            if (itemIssueAutoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemIssueAutoID': itemIssueAutoID},
                    url: "<?php echo site_url('CommunityNgo/fetch_item_for_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        $('#return_item_modal').modal('show');
                        $('#table_body_return').empty();

                        x = 1;
                        if (jQuery.isEmptyObject(data['detail'])) {

                            $('#table_body_return').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');

                        } else {

                            $.each(data['detail'], function (key, value) {

                                var date_format = '<?php echo $date_format_policy ?>';
                                var current_date = '<?php echo $current_date ?>';

                                $('#table_body_return').append('<tr><td class="text-center">' + x + '</td><td class="text-center">' + value['itemSystemCode'] + '</td><td class="text-center">' + value['itemDescription'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-center">' + value['expectedReturnDate'] + '</td><td class="text-center"><input type="text" name="returned_date" data-inputmask="alias : ' + date_format + '"  value = "' + current_date + '" id="returned_date_' + value['itemIssueDetailAutoID'] + '" class="form-control" ></td><td class="text-center"><input type="text" class="form-control" name="r_quantity[]" id="r_quantity_' + value['itemIssueDetailAutoID'] + '" onkeyup="select_check_box(this,' + value['itemIssueDetailAutoID'] + ',' + value['requestedQty'] + ')" class="number"></td><td class="text-center" id="td_' + value['itemIssueDetailAutoID'] + '">' + value['requestedQty'] + '</td><td class="text-right" style="display:none;"><input class="checkbox" id="check_' + value['itemIssueDetailAutoID'] + '" type="checkbox" value="' + value['itemIssueDetailAutoID'] + '"></td></tr>');
                                x++;

                            });

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


        function save_returned_items() {
            var selected = [];
            var r_quantity = [];
            $('#table_body_return input:checked').each(function () {
                selected.push($(this).val());
                r_quantity.push($('#r_quantity_' + $(this).val()).val());
            });
            if (!jQuery.isEmptyObject(selected)) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'invoiceAutoID': selected,
                        'receiptVoucherAutoId': receiptVoucherAutoId,
                        'r_quantity': r_quantity
                    },
                    url: "<?php echo site_url('CommunityNgo/save_returned_items'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#return_item_modal').modal('hide');
                        refreshNotifications(true);
                        setTimeout(function () {
                            fetchPage('system/CommunityNgo/ngo_hi_rental_items', selected, 'Rental Items');
                        }, 300);
                    }, error: function () {
                        $('#return_item_modal').modal('hide');
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            }
        }

        function select_check_box(data, id, total) {
            $("#check_" + id).prop("checked", false);
            if (data.value > 0) {
                if (total >= data.value) {
                    $("#check_" + id).prop("checked", true);
                } else {
                    $("#check_" + id).prop("checked", false);
                    $("#r_quantity_" + id).val('');
                    myAlert('w', '<?php echo $this->lang->line('communityngo_return_alert');?>');
                }
            }

            document.getElementById("td_" + id).innerHTML = total - data.value;
        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 4/4/2018
 * Time: 10:14 AM
 */