<?php echo head_page('Loyalty Setup', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$com_currency = $this->common_data['company_data']['company_default_currency'];
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
    <style>
        .input-caption {
            font-weight: 700;
            vertical-align: -webkit-baseline-middle;
            text-align: right;
            width: 100%;
            display: inline-block;
        }

        .input-margin .form-control {
            width: 100%;
        }

        .input-margin {
            margin-bottom: 10px;
        }

    </style>
    <ul class="nav nav-tabs" id="jobTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="loyalty" data-toggle="tab" href="#cardsetup" role="tab" aria-controls="home"
               aria-selected="false">Card Setup</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="points" data-toggle="tab" href="#pointssetup" role="tab" aria-controls="profile"
               aria-selected="false">Points Setup</a>
        </li>
    </ul>
    <div class="tab-content" id="TabContent">
        <div class="tab-pane fade" id="cardsetup" role="tabpanel" aria-labelledby="home-tab">
            <br>
            <div class="row">
                <div class="col-md-5">

                </div>
                <div class="col-md-1 pull-right">
                    <button type="button" onclick="add_card()" class="btn btn-primary btn-sm pull-right"><i
                                class="fa fa-plus"></i> Add Card
                    </button>
                </div>
                <div class="col-md-2 pull-right">
                    <button type="button" style="margin-left: 33%" data-text="Sync" id="btnSync_fromErp"
                            onclick="add_customers_loyality()" class="btn button-royal "
                    ><i class="fa fa-level-down" aria-hidden="true"></i>Pull Customer
                    </button>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="loyalty_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Code</th>
                        <th style="width: 25%">Customer</th>
                        <th style="width: 25%">Customer Telephone</th>
                        <th style="width: 10%">Points</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pointssetup" role="tabpanel" aria-labelledby="profile-tab">
            <br>
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button class="btn btn-primary" onclick="load_points_setup_modal();">Add New Setup</button>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="points_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Currency</th>
                        <th style="width: 20%">Price to points earned ratio</th>
                        <th style="width: 20%">Points to price redeemed ratio</th>
                        <th style="width: 20%">Minimum reward points to use</th>
                        <th style="width: 50%">Exchange Amount</th>
                        <th style="width: 10%">Exchange Points</th>
                        <th style="width: 50%">Purchase Amount</th>
                        <th style="width: 50%">Reward Points</th>
                        <th style="width: 10%">Active</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="point_setup_modal" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="CommonEdit_Title">Points Setup</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="input-caption">Exchange rate</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-margin">
                                <span style="display: inline-block">1 reward point =</span> <input
                                        id="exchange_rate_amount" name="points"
                                        type="number" step="any"
                                        class="form-control"
                                        style="display: inline-block; width: 50%;"> <?php echo $com_currency; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="input-caption">Price to points earned ratio</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-margin">
                                <input id="price_to_point" name="" type="number" step="any"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="input-caption">Points to price redeemed ratio</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-margin">
                                <input id="point_to_price" name="" type="number" step="any" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="input-caption">Minimum reward points to use</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-margin">
                                <input id="minimum_points" name="" type="number" step="any" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="input-caption">Points for purchases</span>
                        </div>
                        <div class="col-md-8">
                            <div class="input-margin">
                                <span style="display: inline-block">Each </span> <input
                                        id="amount_val" name=""
                                        type="number" step="any"
                                        class="form-control"
                                        style="display: inline-block; width: 25%;"> <?php echo $com_currency; ?>
                                <span style="display: inline-block"> spent will earn</span>
                                <input
                                        id="number_of_points" name=""
                                        type="number" step="any"
                                        class="form-control"
                                        style="display: inline-block; width: 25%;">
                                <span style="display: inline-block"> reward points</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary" type="button" onclick="add_points();">
                            <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <div aria-hidden="true" role="dialog" id="loyalty_card_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Add Card</h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="loyalty_card_form" method="post" class="form-group">
                        <input type="hidden" name="cardMasterID" id="cardMasterID">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Barcode</label>
                                <div class="col-sm-6">
                                    <input id="barcode" name="barcode" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Telephone</label>
                                <div class="col-sm-6">
                                    <input id="gc_customerTelephone" name="customerTelephone" type="text"
                                           placeholder="Telephone" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Name</label>
                                <div class="col-sm-6">
                                    <input type="hidden" name="customerID" id="customerID" value="0">
                                    <input id="gc_CustomerName" name="CustomerName" type="text" placeholder="Name"
                                           readonly class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="save_loyalty_card()" type="button">Add</button>
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <!--Close--><?php echo $this->lang->line('common_Close'); ?></button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="loyality_customer_pos">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Customers</h4>
                </div>
                <div class="modal-body">

                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="customer_sync" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th class="text-uppercase" style="width: 5%;text-align: left">#</th>
                                    <th class="text-uppercase" style="width: 12%;text-align: left">Customer
                                        Name</abbr></th>
                                    <th class="text-uppercase" style="width: 12%;text-align: left">Customer Address</th>
                                    <th class="text-uppercase" style="width: 12%;text-align: left">Customer Telephone
                                    </th>
                                    <th class="text-uppercase" style="width: 5%">&nbsp;

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <button type="button" data-text="Add" onclick="addcustomer()"
                                                        class="btn btn-xs btn-primary">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>Pull Customer
                                                </button>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="skin skin-square item-iCheck">
                                                    <div class="skin-section extraColumns"><input type="checkbox"
                                                                                                  class="columnSelected cheackall"
                                                                                                  value="0"><label
                                                                for="checkbox">&nbsp;</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript"
            src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
    <script type="text/javascript">
        var loyalty_table;
        var loyalty_points;
        var selectedItemsSync;


        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/pos/loyalty_setup', 'Loyalty Setup', 'POS');
            });
            load_loyalty_table();
            load_points_table();

            /*$("#gc_customerTelephone").keyup(function (e) {
                var keyValue = e.which;
                if (keyValue == 13) {
                    load_customer_name_for_telephone_no($("#gc_customerTelephone").val());
                }
            });*/
            $('#gc_customerTelephone').autocomplete({
                serviceUrl: '<?php echo site_url();?>Pos/load_loyalty_cus/',
                onSelect: function (suggestion) {
                    $('#gc_customerTelephone').val(suggestion.tel);
                    $('#customerID').val(suggestion.data);
                    $('#gc_CustomerName').val(suggestion.customerName);
                }
            });
            $('#loyalty').click();

        });

        function load_points_setup_modal() {
            $("#point_setup_modal").modal("show");
        }

        function load_loyalty_table() {
            loyalty_table = $('#loyalty_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/load_loyalty_table'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $("[name='cardSetupID']").bootstrapSwitch();
                },
                "aoColumns": [
                    {"mData": "cardMasterID"},
                    {"mData": "barcode"},
                    {"mData": "customerName"},
                    {"mData": "customerTelephone"},
                    {"mData": "total_pts"},
                    {"mData": "Active"},
                    {"mData": "action"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    $.ajax
                    ({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }

        function add_card() {
            $('#loyalty_card_modal').modal('show');
            $("#gc_CustomerName").val('');
            $("#barcode").val('');
            $("#cardMasterID").val('');
            $("#gc_customerTelephone").val('');
            $("#customerID").val();
            $("#gc_CustomerName").prop('disabled', false);
            $("#barcode").prop('readonly', false);
            load_barcode_loyalty()
        }

        function load_customer_name_for_telephone_no(telephone) {
            //
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {telephone: telephone},
                url: "<?php echo site_url('Pos/load_customer_name_for_telephone_no'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        $("#gc_CustomerName").val(data.CustomerName);
                        $("#customerID").val(data.posCustomerAutoID);
                        $("#gc_CustomerName").prop('disabled', true);
                    } else {
                        $("#gc_CustomerName").val('');
                        $("#gc_CustomerName").focus();
                        $("#customerID").val(0);
                        $("#gc_CustomerName").prop('disabled', false);
                        myAlert('i', 'Not Registered yet.')
                    }
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error has occurred.')
                }
            });
        }

        function load_barcode_loyalty() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {telephone: 0},
                url: "<?php echo site_url('Pos/load_barcode_loyalty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#barcode').val(data)
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error has occurred.')
                }
            });
        }

        function save_loyalty_card() {
            var $form = $('#loyalty_card_form');
            var data = $form.serializeArray();
            var customerID = $("#customerID").val();
            if(customerID == '' || customerID == 0 ){
                myAlert('e', 'Please choose Customer.') 
            } else {   
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Pos/save_loyalty_card'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        $("#customerID").val(0);
                        if (data[0] == 's') {
                            $('#loyalty_card_modal').modal('hide');
                            $('#cardMasterID').val('');
                            $('#price_to_point').val('');
                            $('#point_to_price').val('');
                            $('#minimum_points').val('');


                            loyalty_table.draw()
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            }
        }


        function edit_loyalty(cardMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {cardMasterID: cardMasterID},
                url: "<?php echo site_url('Pos/edit_loyalty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#cardMasterID").val(data['cardMasterID']);
                    $("#gc_CustomerName").val(data['customerName']);
                    $("#barcode").val(data['barcode']);
                    $("#gc_customerTelephone").val(data['customerTelephone']);
                    $("#customerID").val(data['customerID']);
                    $("#gc_CustomerName").prop('disabled', true);
                    $("#barcode").prop('readonly', true);
                    $('#loyalty_card_modal').modal('show');
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }

        function load_points_table() {
            loyalty_points = $('#points_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/load_points_table'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $("[name='pointSetupID']").bootstrapSwitch();
                },
                "aoColumns": [
                    {"mData": "pointSetupID"},
                    {"mData": "CurrencyName"},
                    {"mData": "priceToPointsEarned"},
                    {"mData": "pointsToPriceRedeemed"},
                    {"mData": "minimumPointstoRedeem"},
                    {"mData": "amount"},
                    {"mData": "loyaltyPoints"},
                    {"mData": "poinforPuchaseAmount"},
                    {"mData": "purchaseRewardPoint"},
                    {"mData": "active"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    $.ajax
                    ({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }

        function add_points() {
            var price_to_point = $('#price_to_point').val();
            var point_to_price = $('#point_to_price').val();
            var minimum_points = $('#minimum_points').val();
            var exchange_rate_amount = $('#exchange_rate_amount').val();
            var amount_val = $('#amount_val').val();
            var number_of_points = $('#number_of_points').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    exchange_rate_amount: exchange_rate_amount,
                    price_to_point: price_to_point,
                    point_to_price: point_to_price,
                    minimum_points: minimum_points,
                    amount_val: amount_val,
                    number_of_points: number_of_points
                },
                url: "<?php echo site_url('Pos/add_points'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#exchange_rate_amount').val("");
                        $('#price_to_point').val("");
                        $('#point_to_price').val("");
                        $('#minimum_points').val("");
                        $('#amount_val').val("");
                        $('#number_of_points').val("");
                        $("#point_setup_modal").modal("hide");
                    }
                    loyalty_points.draw();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }


        function update_point_active(pointSetupID) {
            var compchecked = 0;
            if ($('#pointSetupID_' + pointSetupID).is(":checked")) {
                compchecked = 1;
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {pointSetupID: pointSetupID, chkedvalue: compchecked},
                    url: "<?php echo site_url('Pos/update_point_active'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        loyalty_points.draw()
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            } else if (!$('#pointSetupID_' + pointSetupID).is(":checked")) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {pointSetupID: pointSetupID, chkedvalue: 0},
                    url: "<?php echo site_url('Pos/update_point_active'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        loyalty_points.draw()
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }


        function delete_loyalty(cardMasterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "You want to delete",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'cardMasterID': cardMasterID},
                        url: "<?php echo site_url('Pos/delete_loyalty_card'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            stopLoad();
                            if (data[0] == 's') {
                                loyalty_table.draw()
                            }

                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        }
                    });
                });
        }


        function update_card_active_general(cardSetupID) {
            var compchecked = 0;
            if ($('#cardSetupID_' + cardSetupID).is(":checked")) {
                compchecked = 1;
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {cardSetupID: cardSetupID, chkedvalue: compchecked},
                    url: "<?php echo site_url('pos/update_card_active'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        loyalty_points.draw()
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            } else if (!$('#cardSetupID_' + cardSetupID).is(":checked")) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {cardSetupID: cardSetupID, chkedvalue: 0},
                    url: "<?php echo site_url('Pos_restaurant/update_card_active'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        loyalty_points.draw()
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }

        function add_customers_loyality() {
            selectedItemsSync = [];
            fetch_customer_detail();
            $('#loyality_customer_pos').modal('show');
        }

        function fetch_customer_detail() {
            otable = $('#customer_sync').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
                "sAjaxSource": "<?php echo site_url('pos/fetch_customers_loyality_general'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
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
                    $('.item-iCheck').iCheck('uncheck');
                    if (selectedItemsSync.length > 0) {
                        $.each(selectedItemsSync, function (index, value) {
                            $("#selectItem_" + value).iCheck('check');
                        });
                    }
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                    $('input').on('ifChecked', function (event) {
                        if ($(this).hasClass('check_customersall')) {
                            ItemsSelectedSync(this);
                        }
                    });
                    $('input').on('ifUnchecked', function (event) {
                        if ($(this).hasClass('check_customersall')) {
                            ItemsSelectedSync(this);
                        }
                    });


                    $('input').on('ifChecked', function (event) {
                        if ($(this).hasClass('cheackall')) {
                            $(".check_customersall").iCheck('check');
                        }
                    });


                    $('input').on('ifUnchecked', function (event) {
                        if ($(this).hasClass('cheackall')) {
                            $(".check_customersall").iCheck('uncheck');
                        }

                    });
                },

                "aoColumns": [
                    {"mData": "posCustomerAutoID"},
                    {"mData": "CustomerName"},
                    {"mData": "CustomerAddress1"},
                    {"mData": "customerTelephone"},

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

        function ItemsSelectedSync(item) {
            var value = $(item).val();
            if ($(item).is(':checked')) {
                var inArray = $.inArray(value, selectedItemsSync);
                if (inArray == -1) {
                    selectedItemsSync.push(value);
                }
            } else {
                var i = selectedItemsSync.indexOf(value);
                if (i != -1) {
                    selectedItemsSync.splice(i, 1);
                }
            }
        }

        function addcustomer() {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("pos/save_customers_loyality_card_general"); ?>',
                dataType: 'json',
                data: {'selectedItemsSync': selectedItemsSync},
                async: false,
                success: function (data) {
                    //   myAlert(data[0], data[1]);
                    if (data['status']) {
                        refreshNotifications(true);
                        otable.draw();
                        load_loyalty_table();
                        $('.extraColumns input').iCheck('uncheck');
                        $("#loyality_customer_pos").modal('show');
                    } else {
                        refreshNotifications(true);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-04
 * Time: 2:31 PM
 */