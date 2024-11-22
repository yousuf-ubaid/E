<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_fuel_usage');
echo head_page($title  , false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false); ?>

    <div class="row">
        <div class="col-md-7">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved--></td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved--></td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back--></td>
                </tr>
            </table>
        </div>
        <div class="col-md-5 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/Fleet_Management/fleet_saf_newFuelUsage','','<?php echo $this->lang->line('fleet_new_fuel_usage');?>','Fleet');">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_new_fuel_usage');?>
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="fuel_usage_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 5%"><?php echo $this->lang->line('fleet_document_no'); ?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_details'); ?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('fleet_document_Date'); ?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_amount'); ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_confirmed'); ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_approved'); ?></th>
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            </tr>
            </tbody>
        </table>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1000000000;">
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


    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_fuelusage', '', 'Fuel Usage');
            });
            purchase_request_table();

        });

        function purchase_request_table() {
            Otable = $('#fuel_usage_table').DataTable({

                    "bProcessing": true,
                    "bServerSide": true,
                    "bDestroy": true,
                    "bStateSave": true,
                    "sAjaxSource": "<?php echo site_url('Fleet/fetch_fuel_usage_tble'); ?>",
                    "aaSorting": [[0, 'desc']],
                    "fnInitComplete": function () {
                    },


                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if (parseInt(oSettings.aoData[x]._aData['fuelusageID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');
                },

                "aoColumns": [
                    {"mData": "fuelusageID"},
                    {"mData": "documentCode"},
                    {"mData": "supplier"},
                    {"mData": "documentDate"},
                    {"mData": "total_value"},
                    {"mData": "confirmed"},
                    {"mData": "approved"},
                    {"mData": "action"},
                    {"mData": "detTransactionAmount"},
                    {"mData": "transactionCurrency"}
                ],
                "columnDefs": [{"searchable": true, "visible": false, "orderable": false, "targets": [8,9]},{"searchable": false, "targets": [0,4,5,6,7]}],
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

        function referbackFuelUsage(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'fuelusageID': id},
                        url: "<?php echo site_url('Fleet/referback_FuelUsage'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                Otable.draw();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function reOpen_fuel_usage(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'Fuelusageid':id},
                        url :"<?php echo site_url('Fleet/re_open_fuel_usage'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            Otable.draw();
                            stopLoad();
                            refreshNotifications(true);
                        },error : function(){
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function delete_document(id, description) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Fleet/delete_document'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'fuelusageID': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            Otable.draw();
                            refreshNotifications(true);

                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }
        function fetch_approval(fuelusageID, documentApprovedID, Level) {
            if (fuelusageID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'fuelusageID': fuelusageID, 'html': true},
                    url: "<?php echo site_url('Fleet/load_purchase_request_conformation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#fuelusageID').val(fuelusageID);
                        $('#documentApprovedID').val(documentApprovedID);
                        $('#Level').val(Level);
                        $("#jv_modal").modal({backdrop: "static"});
                        $('#conform_body').html(data);
                        $('#comments').val('');
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

    </script>

