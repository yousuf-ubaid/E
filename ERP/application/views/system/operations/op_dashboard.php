<?php
$this->load->helpers('operation');
$customer_drp = all_customer_drop_frm_contract();
?>
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }
    .pagination>li>a, .pagination>li>span {
        padding: 2px 8px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="dashboard_content">
    <div class="box box-default">
        <div class="box-header with-border">
            <div class="box-tools pull-right">
                <!--<button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i>
                </button>-->
            </div>
        </div>
        <div class="box-body" style="display: block;">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label class="col-md-5 control-label">Client </label>
                    <div class="col-md-12">
                        <?php echo form_dropdown('customerAutoID[]', $customer_drp, '', 'multiple class="form-control" onchange="load_contract_from_customer()" id="customerAutoID"'); ?>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label class="col-md-5 control-label">Contract </label>
                    <div class="col-md-12">
                        <div id="div_contract_filter">
                            <select name="contractUID[]" class="form-control select2" id="contractUID"
                                    multiple="">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label class="col-md-5 control-label">Call Off </label>
                    <div class="col-md-12">
                        <div id="div_calloff_filter">
                            <select name="calloffID[]" class="form-control select2" id="calloffID"
                                    multiple="">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label class="col-md-5 control-label" style="color: white;">asda</label>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="loaddashbord()">Load</button>
                    </div>


                </div>


            </div>
        </div>
    </div>


    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div id="containercallOff" style="overflow-x:auto;width:1300px;height:300px;overflow-y: hidden;">

            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h4 class="box-title"><strong class="btn-box-tool">Active Call-Offs</strong></h4>
                    <div class="box-tools pull-right">

                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="calloff_table_dash" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Call Off</th>
                                <th style="min-width: 5%">Call Off Length</th>
                                <th style="min-width: 5%">Jobs Length</th>
                                <th style="min-width: 5%">% Completion</th>
                                <th style="min-width: 5%">Current month Amount</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h4 class="box-title"><strong class="btn-box-tool">Held Call off</strong></h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="calloff_hold_table_dash" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Call Off</th>
                                <th style="min-width: 5%">Call Off Length</th>
                                <th style="min-width: 5%">Jobs Length</th>
                                <th style="min-width: 5%">% Completion</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>







    <div class="row">
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h4 class="box-title"><strong class="btn-box-tool">Month Wise Retention Invoice</strong></h4>
                    <div style="margin-top: 7px"><label>Year :</label>
                        <select id="mnthyearfltr" onchange="load_month_wise_retention()">
                            <?php
                            $companyID=current_companyID();
                            $years = $this->db->query("SELECT YEAR(srp_erp_customerinvoicemaster.invoiceDate) as yr FROM `srp_erp_customerinvoicemaster` WHERE `srp_erp_customerinvoicemaster`.`companyID` = $companyID AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 AND srp_erp_customerinvoicemaster.isOpYN=1 AND srp_erp_customerinvoicemaster.retensionInvoiceID is not null GROUP BY YEAR(srp_erp_customerinvoicemaster.invoiceDate) ORDER BY YEAR(srp_erp_customerinvoicemaster.invoiceDate) DESC ")->result_array();
                            if(!empty($years)){
                                foreach ($years as $yr){
                                ?>
                                    <option value="<?php echo $yr['yr']; ?>"><?php echo $yr['yr']; ?></option>
                            <?php
                                }
                            }else{
                                $yearcr = date("Y");
                                ?>
                                <option value="<?php echo $yearcr; ?>"><?php echo $yearcr; ?></option>
                            <?php
                            }
                            ?>

                         </select>
                    </div>
                    <div class="box-tools pull-right">

                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="retention_month_wise_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 5%">Month</th>
                                <th style="min-width: 5%">Amount </th>
                            </tr>
                            </thead>
                            <tbody id="retention_month_wise_table_body">

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">

        </div>
    </div>






    <div class="box box-warning">
        <div class="box-header with-border">
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                        class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12" >
                    <div id="callofflocation" style="overflow-x:auto;width:100%;height:300px;overflow-y: hidden;">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>




<div aria-hidden="true" role="dialog"  id="calloff_drill_down" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Call Off Drill Down</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Job Ticket</th>
                                <th style="min-width: 10%">Invoice</th>
                                <th style="min-width: 10%">Amount</th>
                                <th style="min-width: 10%">Retention Invoice</th>
                                <th style="min-width: 10%">Amount</th>
                            </tr>
                            </thead>
                            <tbody id="calloff_dd_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="mnthwisecinvr_dd_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Month Wise Retention Invoice Drill Down</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Invoice</th>
                                <th style="min-width: 10%">Amount</th>
                            </tr>
                            </thead>
                            <tbody id="mnthwisecinvr_dd_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<script type="text/javascript">
    var Otable;
    var Otablehld;
    $(document).ready(function () {
        $("#customerAutoID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: '200px',
            maxHeight: '30px',
            numberDisplayed: 1
        });

        $("#customerAutoID").multiselect2('selectAll', false);
        $("#customerAutoID").multiselect2('updateButtonText');

        $('#contractUID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: '200px',
            maxHeight: '30px',
            numberDisplayed: 1
        });
        $("#contractUID").multiselect2('selectAll', false);
        $("#contractUID").multiselect2('updateButtonText');

        $('#calloffID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: '200px',
            maxHeight: '30px',
            numberDisplayed: 1
        });
        $("#calloffID").multiselect2('selectAll', false);
        $("#calloffID").multiselect2('updateButtonText');
        load_contract_from_customer();
        load_month_wise_retention();
    });


    function load_contract_from_customer() {
        var clients=$('#customerAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {clients: clients},
            url: "<?php echo site_url('Operation/load_contract_from_customer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_contract_filter').html(data);
                $('#contractUID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: '200px',
                    maxHeight: '30px',
                    numberDisplayed: 1
                });
                $("#contractUID").multiselect2('selectAll', false);
                $("#contractUID").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                load_calloff_from_contract();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_calloff_from_contract() {
        var contractUID=$('#contractUID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {contractUID: contractUID},
            url: "<?php echo site_url('Operation/load_calloff_from_contract'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_calloff_filter').html(data);
                $('#calloffID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: '200px',
                    maxHeight: '30px',
                    numberDisplayed: 1
                });
                $("#calloffID").multiselect2('selectAll', false);
                $("#calloffID").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                loaddashbord();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loaddashbord() {
        loadCallOffChart();
        loadCallOfftable_dash();
        loadCallOfftableHold_dash();
        loadCallOfflocation();
    }

    function loadCallOffChart() {
        var selectedJobContract = $('#contractUID').val();
        var tableViewClient = $('#customerAutoID').val();
        var selectedJobCalloff = $('#calloffID').val();

        if (jQuery.isEmptyObject(selectedJobContract)) {
            myAlert('e','Select Contract');
            return false;
        }

        if (jQuery.isEmptyObject(tableViewClient)) {
            myAlert('e','Select Client');
            return false;
        }

        if (jQuery.isEmptyObject(selectedJobCalloff)) {
            myAlert('e','Select Calloff');
            return false;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractUID': selectedJobContract,'customerAutoID': tableViewClient,'calloffID': selectedJobCalloff},
            url: "<?php echo site_url('Operation/loadCallOffChart'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var cont=data['description'].length;
                var columncnt=4000;
                if(cont<=10){
                    columncnt =900
                }else if(cont<=15){
                    columncnt =1300
                }else if(cont<=20){
                    columncnt =2000
                }else if(cont<=30){
                    columncnt =3500
                }else{
                    columncnt =5000
                }

                $('#containercallOff').highcharts({
                    chart: {
                        type: 'column',
                        width: columncnt
                    },
                    title: {
                        align: 'bottom',
                        verticalAlign: 'bottom',
                        y: 1,
                        text: ''
                    },
                    subtitle: {
                        align: 'bottom',
                        verticalAlign: 'bottom',
                        y: 1,
                        x: 50,
                        text: 'Call0ff'
                    },
                    xAxis: {
                        categories: data['description'],
                        scrollbar: {
                            enabled: true
                        },
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Length'
                        }
                    },
                    legend: {
                        align: 'top',
                        verticalAlign: 'top',

                        enabled: true
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:f}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Call off Length',
                        data: data['calloflength']

                    }, {
                        name: 'Job Length',
                        data: data['ticketlength']
                    }]
                });


            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadCallOfftable_dash(selectedID=null) {//
        var selectedJobContract = $('#contractUID').val();
        var tableViewClient = $('#customerAutoID').val();
        var selectedJobCalloff = $('#calloffID').val();

        if (jQuery.isEmptyObject(selectedJobContract)) {
            myAlert('e','Select Contract');
            return false;
        }

        if (jQuery.isEmptyObject(tableViewClient)) {
            myAlert('e','Select Client');
            return false;
        }

        if (jQuery.isEmptyObject(selectedJobCalloff)) {
            myAlert('e','Select Calloff');
            return false;
        }

        Otable = $('#calloff_table_dash').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/loadCallOfftable_dash'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['calloffID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "calloffID"},
                {"mData": "ticktdrldwn"},
                {"mData": "calloflength"},
                {"mData": "ticketlength"},
                {"mData": "txtcallpercen"},
                {"mData": "metersamnt"}
            ],
            "columnDefs": [{"targets": [0,2,3,4,5], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "selectedJobContract[]", "value": selectedJobContract});
                aoData.push({"name": "tableViewClient[]", "value": tableViewClient});
                aoData.push({"name": "selectedJobCalloff[]", "value": selectedJobCalloff});
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



    function loadCallOfftableHold_dash(selectedID=null) {//
        var selectedJobContract = $('#contractUID').val();
        var tableViewClient = $('#customerAutoID').val();
        var selectedJobCalloff = $('#calloffID').val();

        if (jQuery.isEmptyObject(selectedJobContract)) {
            myAlert('e','Select Contract');
            return false;
        }

        if (jQuery.isEmptyObject(tableViewClient)) {
            myAlert('e','Select Client');
            return false;
        }

        if (jQuery.isEmptyObject(selectedJobCalloff)) {
            myAlert('e','Select Calloff');
            return false;
        }

        Otablehld = $('#calloff_hold_table_dash').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/loadCallOfftableHold_dash'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['calloffID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "calloffID"},
                {"mData": "description"},
                {"mData": "calloflength"},
                {"mData": "ticketlength"},
                {"mData": "txtcallpercen"}
            ],
            "columnDefs": [{"targets": [0,2,3,4], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "selectedJobContract[]", "value": selectedJobContract});
                aoData.push({"name": "tableViewClient[]", "value": tableViewClient});
                aoData.push({"name": "selectedJobCalloff[]", "value": selectedJobCalloff});
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

    function loadCallOfflocation() {
        var selectedJobContract = $('#contractUID').val();
        var tableViewClient = $('#customerAutoID').val();
        var selectedJobCalloff = $('#calloffID').val();

        if (jQuery.isEmptyObject(selectedJobContract)) {
            myAlert('e','Select Contract');
            return false;
        }

        if (jQuery.isEmptyObject(tableViewClient)) {
            myAlert('e','Select Client');
            return false;
        }

        if (jQuery.isEmptyObject(selectedJobCalloff)) {
            myAlert('e','Select Calloff');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Operation/loadCallOfflocation'); ?>",
            data: { selectedJobContract: selectedJobContract, tableViewClient: tableViewClient, selectedJobCalloff: selectedJobCalloff},
            dataType: "html",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                $('#callofflocation').empty();
                $('#callofflocation').append(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function calloffDD(calloffID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/calloffDD"); ?>',
            dataType: 'json',
            data: {'calloffID': calloffID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#calloff_dd_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#calloff_dd_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                }else{
                    $.each(data, function (key, value) {
                        $('#calloff_dd_body').append('<tr><td>' + x + '</td><td>' + value['ticketNo'] + '</td><td style="cursor: pointer;"><a onclick="documentPageView_modal(\'' + value['documentID'] + '\' ,' + value['invoiceAutoID'] + '); ">' + value['invoiceCode'] + '</a></td><td>' + value['transactionAmount'] + '</td><td style="cursor: pointer;"><a onclick="documentPageView_modal(\'' + value['documentID'] + '\' ,' + value['invoiceAutoIDR'] + '); ">' + value['invoiceCodeR'] + '</a></td><td>' + value['transactionAmountR'] + '</td></tr>');
                        x++;
                    });
                }
                $('#calloff_drill_down').modal('show');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function load_month_wise_retention() {
        var mnthyearfltr=$('#mnthyearfltr').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_month_wise_retention"); ?>',
            dataType: 'json',
            data: {'mnthyearfltr': mnthyearfltr},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#retention_month_wise_table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#retention_month_wise_table_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                }else{

                    $.each(data, function (key, value) {
                        $('#retention_month_wise_table_body').append('<tr><td>' + x + '</td><td>' + value['mnthdesc'] + '</td><td style="text-align: right; cursor: pointer;"><a onclick="monthwiseretentionDD(' + value['yr'] + ',' + value['mnth'] + '); ">' + value['transactionAmount'] + '</a></td></tr>');
                        x++;
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function monthwiseretentionDD(yr,mnth) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/monthwiseretentionDD"); ?>',
            dataType: 'json',
            data: {'year': yr,'month': mnth},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#mnthwisecinvr_dd_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#mnthwisecinvr_dd_body').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                }else{
                    $.each(data, function (key, value) {
                        $('#mnthwisecinvr_dd_body').append('<tr><td>' + x + '</td><td style="cursor: pointer;"><a onclick="documentPageView_modal(\'' + value['documentID'] + '\' ,' + value['invoiceAutoID'] + '); ">' + value['bookingInvCode'] + '</a></td><td style="text-align: right;">' + value['transactionAmount'] + '</td></tr>');
                        x++;/*documentPageView_modal('CINV',484)*/
                    });
                }
                $('#mnthwisecinvr_dd_modal').modal('show');
            }, error: function () {
                stopLoad();
            }
        });
    }


</script>