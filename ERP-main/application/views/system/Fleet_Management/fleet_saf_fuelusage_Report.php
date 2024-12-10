<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$date_format_policy = date_format_policy();
$title=$this->lang->line('fleet_fuel_usage');
echo head_page($title, false);

$this->load->helper('fleet_helper');
$Supp_drop = fetch_supplier_drop(true,1);
$Vehicle_drop = fetch_all_vehicle();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);

?>
    <style>
        .bgc {
            background-color: #e1f1e1;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
          <!--  <form method="post" name="form_rpt_fuelusage" id="frm_rpt_leave_history" class="form-horizontal">-->
                <?php echo form_open('login/loginSubmit', ' name="frm_rpt_leave_history" id="frm_rpt_leave_history" class="form-horizontal" role="form"'); ?>

                <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                    <div class="input-group datepic col-sm-10">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                    <div class="input-group datepicto col-sm-10">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2 ">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" onchange="loadSupplier()" '); ?>
                
                </div>
                <div class="form-group col-sm-3">
                    <label for=""><?php echo $this->lang->line('common_supplier'); ?><!--Supplier--></label>
                    <?php //echo form_dropdown('supplierAutoID[]', $Supp_drop, '', ' class="form-control" multiple="multiple" id="supplierAutoID" required'); ?>
                    <div id="div_load_supplier">
                        <select name="supplierAutoID[]" class="form-control" id="supplierAutoID" multiple="multiple" required>
                            <?php
                                if (!empty($Supp_drop)) {
                                    foreach ($Supp_drop as $key => $val) {
                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                
                </div>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('fleet_vehicle_usage'); ?><!--Vehicle--></label>
                    <?php echo form_dropdown('vehicleMasterID[]', $Vehicle_drop, '', ' class="form-control" multiple="multiple" id="vehicleMasterID" required'); ?>
                </div>

                <div class="form-group col-sm-1 pull-right">
                    <label for=""></label>
                    <button style="margin-top: 25px" type="button" onclick="get_fuelusageReport()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?><!--Generate--></button>
                </div>


            </div>
            <?php echo form_close(); ?>
        </fieldset>
    </div>
    <hr style="margin: 0px;">
    <div id="div_fuelusage_history">
    </div>


    <div class="modal fade" id="returndrilldownModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th>Document Code</th>
                            <th>Document Date</th>
                            <th>Currency</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody id="salesreturn">

                        </tbody>
                        <tfoot id="salesreturnfooter" class="table-borded">

                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script>
        $(document).ready(function (e) {
            $('.select2').select2();

            $('#vehicleMasterID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                maxHeight: 200,
                numberDisplayed: 2,
                buttonWidth: '180px'
            });
            $("#vehicleMasterID").multiselect2('selectAll', false);
            $("#vehicleMasterID").multiselect2('updateButtonText');
            $("#status_filter").change(function () {
                load_statusbase_supplier()
            });
            $('#supplierAutoID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 200,
                numberDisplayed: 2,
                buttonWidth: '180px'
            });
            $("#supplierAutoID").multiselect2('selectAll', false);
            $("#supplierAutoID").multiselect2('updateButtonText');
            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_fuelusage_Report', '', 'Fuel Usage')
            });
            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

            });
            $('.datepicto').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

            });
            get_fuelusageReport();
        });

        function get_fuelusageReport() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Fleet/get_fuelusage_report') ?>",
                data: $("#frm_rpt_leave_history").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_fuelusage_history").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateReportPdf() {
            var form = document.getElementById('frm_rpt_leave_history');
            form.target = '_blank';
            form.action = '<?php echo site_url('Fleet/get_fuel_usage_report_pdf'); ?>';
            form.submit();
        }


        function openreturnDD(invoiceAutoID){
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('sales/get_sales_order_return_drilldown_report') ?>",
                data: {'invoiceAutoID': invoiceAutoID},
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#salesreturn').empty();
                    $('#salesreturnfooter').empty();
                    if (jQuery.isEmptyObject(data)) {
                        $('#salesreturn').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    } else {
                        tot_amount = 0;
                        var currency;
                        var amount;
                        var decimalPlaces=2;
                        var total=0;
                        $.each(data, function (key, value) {
                            if($('#currency').val()==1){
                                currency=value['transactionCurrency'];
                                amount=value['totalValue']/value['transactionExchangeRate'];
                                decimalPlaces=value['transactionCurrencyDecimalPlaces'];
                            }else if($('#currency').val()==2){
                                currency=value['companyLocalCurrency'];
                                amount=value['totalValue']/value['companyLocalExchangeRate'];
                                decimalPlaces=value['companyLocalCurrencyDecimalPlaces'];
                            }else{
                                currency=value['companyReportingCurrency'];
                                amount=value['totalValue']/value['companyReportingExchangeRate'];
                                decimalPlaces=value['companyReportingCurrencyDecimalPlaces'];
                            }
                            total += amount;
                            $('#salesreturn').append('<tr><td><a href="#" class="" onclick="documentPageView_modal(\'SLR\' , ' + value["salesReturnAutoID"] + ')">' + value["salesReturnCode"] + '</a></td><td>' + value["returnDate"] + '</td><td >' + currency + '</td><td class="text-right">' + parseFloat(amount).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                        });
                        $('#salesreturnfooter').append('<tr><td colspan="3" >&nbsp;</td> <td class="text-right reporttotal" style="font-weight: bold;">' + parseFloat(total).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                    }
                    $('#returndrilldownModal').modal('show');
                    $('.drilldown-title').html("Sales Return Drill Down");

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }



        function openrecreditDD(invoiceAutoID){
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('sales/get_sales_order_credit_drilldown_report') ?>",
                data: {'invoiceAutoID': invoiceAutoID},
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#salesreturn').empty();
                    $('#salesreturnfooter').empty();
                    if (jQuery.isEmptyObject(data)) {
                        $('#salesreturn').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    } else {
                        tot_amount = 0;
                        var currency;
                        var amount;
                        var decimalPlaces=2;
                        var total=0;
                        $.each(data, function (key, value) {
                            if($('#currency').val()==1){
                                currency=value['transactionCurrency'];
                                amount=value['transactionAmount'];
                                decimalPlaces=value['transactionCurrencyDecimalPlaces'];
                            }else if($('#currency').val()==2){
                                currency=value['companyLocalCurrency'];
                                amount=value['companyLocalAmount'];
                                decimalPlaces=value['companyLocalCurrencyDecimalPlaces'];
                            }else{
                                currency=value['companyReportingCurrency'];
                                amount=value['companyReportingAmount'];
                                decimalPlaces=value['companyReportingCurrencyDecimalPlaces'];
                            }
                            //alert(amount);
                            total += parseFloat(amount);
                            $('#salesreturn').append('<tr><td><a href="#" class="" onclick="documentPageView_modal(\'' + value["docID"] + '\' , ' + value["masterID"] + ')">' + value["documentCode"] + '</a></td><td>' + value["documentDate"] + '</td><td >' + currency + '</td><td class="text-right">' + parseFloat(amount).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                        });
                        $('#salesreturnfooter').append('<tr><td colspan="3" >&nbsp;</td> <td class="text-right reporttotal" style="font-weight: bold;">' + parseFloat(total).formatMoney(+decimalPlaces + ',', '.') + '</td></tr>');
                    }
                    $('#returndrilldownModal').modal('show');
                    $('.drilldown-title').html("Receipt/Credit Note Drill Down");

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadEmployees(){
            var supplierAutoID  = $('#supplierAutoID').val();

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('template_paySheet/dropdown_payslipemployees_his_report') ?>",
                data: {supplierAutoID:supplierAutoID},
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if(data[0] == 's'){
                        $('#vehicleMasterID').multiselect('refresh');
                        $("#div_employee").html( data[1] );
                        $('#vehicleMasterID').multiselect2({
                            includeSelectAllOption: true,
                            selectAllValue: 'select-all-value',
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            maxHeight: 200,
                            numberDisplayed: 2,
                            buttonWidth: '180px'
                        });
                        $("#vehicleMasterID").multiselect2('selectAll', false);
                        $("#vehicleMasterID").multiselect2('updateButtonText');

                    }
                    else{
                        $('#vehicleMasterID').multiselect('refresh');
                        $('#vehicleMasterID').multiselect2({
                            includeSelectAllOption: true,
                            maxHeight: 200,
                            numberDisplayed: 1
                        });
                        $("#vehicleMasterID").multiselect2('updateButtonText');

                        $("#div_paySlips").html(data[1]);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });

        }

        function PageView_modal(documentID, para1, para2, approval=1) {

            $("#profile-v").removeClass("active");
            $("#home-v").addClass("active");
            $("#TabViewActivation_attachment").removeClass("active");
            $("#tab_itemMasterTabF").removeClass("active");
            $("#TabViewActivation_view").addClass("active");
            attachment_View_modal(documentID, para1);
            $('#loaddocumentPageView').html('');

            var siteUrl;
            var paramData = new Array();
            var title = '';
            var a_link;
            var de_link;

            $("#itemMasterSubTab_footer_div").html('');
            $(".itemMasterSubTab_footer").hide();

            switch (documentID) {
                case "FU": // Rental Item Issue
                    siteUrl = "<?php echo site_url('Fleet/load_purchase_order_report'); ?>";
                    paramData.push({name: 'documentCode', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('fleet_fuel_usage_approval');?>";
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
        function load_statusbase_supplier() {
        var status_filter = $('#status_filter').val();
        if (status_filter) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {activeStatus: status_filter},
                url: "<?php echo site_url('Procurement/fetch_supplier'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_supplier').html(data);
                    $('#supplierAutoID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: '220px',
                        maxHeight: '30px',
                        numberDisplayed: 1
                    });
                    $("#supplierAutoID").multiselect2('selectAll', false);
                    $("#supplierAutoID").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } 
    }
    </script>

