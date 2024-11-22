<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_revenue_details');
$date_format_policy = date_format_policy();
$customer="";
$type = $this->session->userdata("companyType");
$segment_arr = "";
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
echo head_page($title, false);
$companyType = $this->session->userdata("companyType");
if($companyType == 1){
    $customer_category_arr=all_customer_category_report_drop();
    $customer = all_customer_drop(false,1);
    #$customer[0] = ('') . 'Sundry' . ('');
    $customer[-1] = ('') . 'POS Customers' . ('');
    $customer[-2] = ('') . 'Direct Receipt voucher' . ('');

    $segment_arr = fetch_segment(true,false);
}else{
    $customer = all_group_customer_drop(false);
    $segment_arr = fetch_group_segment(true,false);
    #$customer[0] = ('') . 'Sundry' . ('');
    $customer[-1] = ('') . 'POS Customers' . ('');
    $customer[-2] = ('') . 'Direct Receipt voucher' . ('');
}
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_invoice" id="frm_rpt_customer_invoice" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?><!--Currency--></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_customer_invoice()" tabindex="-1" aria-hidden="true" data-bv-field="currency">
                        <option value="1">Transaction Currency</option>
                        <option value="2">Local Currency</option>
                        <option value="3" selected>Reporting Currency</option>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                    <div class="input-group datepicto">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for="segment">
                        <?php echo $this->lang->line('common_segment'); ?><!--Segment -->
                    </label>
                    <?php echo form_dropdown('segmentID[]', $segment_arr, '', 'multiple class="form-control select2" id="segmentID" required'); ?>
                </div>
                <?php if($companyType == 1){ ?>
                <div class="form-group col-sm-2">
                    <label><?php echo $this->lang->line('common_customer_category'); ?><!--Customer Category --> </label>
                    <?php echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID"  multiple="multiple"'); ?>
                </div>
                <?php } ?>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_search'); ?><!--Search --></label>
                        <input type="text" id="search" name="search" class="form-control">
                </div>
                <?php if($companyType == 1){ ?>
                <div class="form-group col-sm-2 ">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                </div>
                <?php }  ?>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <?php // echo form_dropdown('customerID[]', $customer, '', 'multiple  class="form-control" id="customerID" required'); ?>
                    <div id="div_load_customers">
                        <select name="customerID[]" class="form-control" id="filter_customerID" multiple="">
                            <?php
                            unset($customer[""]);
                            if (!empty($customer)) {
                                foreach ($customer as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-1">
                    <label for=""></label>
                    <button style="margin-top: 30%" type="button" onclick="get_customer_invoice()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?><!--Generate --></button>
                </div>


            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_customer_invoice">
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
    var type;
    var url;
    var urlPdf;
    var urlDrill1;
    var urlDrill2;
    var selectall = ''
    var selectdeselectall = ''
    $(document).ready(function (e) {
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
       // load_categorybase_customer_ci();
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
        $("#customerCategoryID").change(function () {

            load_categorybase_customer_ci();
        });
        $("#status_filter").change(function () {

            load_categorybase_customer_ci();
        });
        $("#customerCategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1,
            onSelectAll: function() {
                selectall = 'All'

            },
            onDeselectAll: function() {
                selectdeselectall = 'DAll'
            }


        });
        $("#customerCategoryID").multiselect2('selectAll', false);
        $("#customerCategoryID").multiselect2('updateButtonText');

        $('#filter_customerID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_customerID").multiselect2('selectAll', false);
        $("#filter_customerID").multiselect2('updateButtonText');

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#segmentID").multiselect2('selectAll', false);
        $("#segmentID").multiselect2('updateButtonText');
        $('.headerclose').click(function () {
            fetchPage('system/sales/erp_customer_invoice_report', '', 'Sales Order')
        });

        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('sales/get_customer_invoice_report'); ?>';
            urlPdf = '<?php echo site_url('sales/get_customer_invoice_report_pdf'); ?>';
            urlDrill1 = '<?php echo site_url('sales/get_sales_order_return_drilldown_report'); ?>';
            urlDrill2 = '<?php echo site_url('sales/get_sales_order_credit_drilldown_report'); ?>';
        }else{
            url = '<?php echo site_url('sales/get_group_customer_invoice_report'); ?>';
            urlPdf = '<?php echo site_url('sales/get_group_customer_invoice_report_pdf'); ?>';
            urlDrill1 = '<?php echo site_url('sales/get_group_sales_order_return_drilldown_report'); ?>';
            urlDrill2 = '<?php echo site_url('sales/get_group_sales_order_credit_drilldown_report'); ?>';
        }
        get_customer_invoice();
    });

    function get_customer_invoice() {
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frm_rpt_customer_invoice").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_customer_invoice").html(data);
                applyAlternateColor();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_customer_invoice');
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }


    function openreturnDD(invoiceAutoID){
        $.ajax({
            type: "POST",
            url: urlDrill1,
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
            url: urlDrill2,
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

    function load_categorybase_customer_ci() {
        var customerCategoryID = $('#customerCategoryID').val();
        var status_filter = $('#status_filter').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'customerCategoryID': customerCategoryID, 'type':<?php echo $type;?>,activeStatus:status_filter,'selectall':selectall,'selectdeselectall':selectdeselectall },
            url: "<?php echo site_url('Report/fetch_customerDropdown_rev'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_customers').html(data);
                $('#filter_customerID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',

                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1

                });
                $("#filter_customerID").multiselect2('selectAll', false);
                $("#filter_customerID").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function applyAlternateColor() {
        const rows = document.querySelectorAll("#tbl_rpt_salesorder tbody tr");
        let toggleClass = false;

        rows.forEach(function(row) {
            if (row.classList.contains("hoverTr") || row.tagName.toLowerCase() === 'tr') {
                toggleClass = !toggleClass;
                row.style.backgroundColor = toggleClass ? "#efeffc" : "";
            } else {
                row.style.backgroundColor = "";
                toggleClass = false;
            }
        });
    }
</script>
