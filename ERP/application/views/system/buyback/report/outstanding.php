<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST["page_name"], false);
$this->load->helper('buyback_helper');

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <div class="col-md-12">
            <?php echo form_open('login/loginSubmit', ' id="frm_filter_outstanding" class="form-horizontal" name="frm_filter_outstanding" role="form"'); ?>
            <input type="hidden" id="fieldNameChkpdf" name="fieldNameChkpdf" value="">
            <input type="hidden" id="captionChkpdf" name="captionChkpdf" value="">
            <input type="hidden" id="customerIDpdf" name="customerID" value="">
            <input type="hidden" id="customerNamepdf" name="customerName" value="">
            <input type="hidden" id="currencypdf" name="currency" value="">
            <input type="hidden" id="agepdf" name="age" value="">
            <div id="filters"> <!--load report content-->
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


    <!--modal report-->
    <div class="modal fade" id="outstanding_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 90%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $_POST["page_name"] ?></h4>
                </div>
                <div class="modal-body">
                    <div id="outstanding_reportContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
    <!--modal report-->
    <div class="modal fade" id="outstanding_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 95%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_receivable_rs_cl_drill_down');?><!--Drill Down--> - <span class="myModalLabel"></span></h4>
                </div>
                <div class="modal-body">
                    <div id="outstanding_reportContentDrilldown"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ProfitLossView" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title modalTitle" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <div class="Profit" id="Profit">
                        <table id="tbl_Profit" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header" id="profit_table_header">
                            </thead>
                            <tbody id="profitData">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="VoucherPaidDetailsView" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title PaidmodalTitle" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <div class="Profit" id="Profit">
                        <table id="tbl_voucherPaid" class="borderSpace report-table-condensed" style="width: 100%">
                            <thead class="report-header" id="loanAdvance_table_header">
                            <tr>
                                <!--   <th>#</th> -->
                                <th>Farm</th>
                                <th>Document Code</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                            </tr>
                            </thead>
                            <tbody id="VoucherPaidData">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

<script>
    var type;
    var url;
    var urlPdf;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/buyback/report/outstanding','<?php echo $_POST["page_id"] ?>','<?php echo $_POST["page_name"] ?>');
        });
        var typeArr = $('#parentCompanyID option:selected').val();
        typeArr  = typeArr.split('-');
        type = typeArr[1];

        if(type == 1){
            url = '<?php echo site_url('Buyback/get_outstanding_report'); ?>';
            urlPdf = '<?php echo site_url('Buyback/get_outstanding_report_pdf'); ?>';
        }else{
            url = '<?php echo site_url('Buyback/get_group_outstanding_report'); ?>';
            urlPdf = '<?php echo site_url('Buyback/get_group_outstanding_report_pdf'); ?>';
        }
        get_outstanding_filter();/*call filter for report method*/
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    function get_outstanding_filter() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {formName: "frm_filter_outstanding", reportID: '<?php echo $_POST["page_id"] ?>',type:type},
            url: "<?php echo site_url('Buyback/get_outstanding_filter'); ?>",
            beforeSend: function () {
                $("#filters").html("<div class='text-center'><i class='fa fa-refresh fa-spin fa-2'></i>Loading</div>");
            },
            success: function (data) {
                $("#filters").html("");
                $("#filters").html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function generateOutstandingReport(formName) {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
            captionChk.push({name: "subLocationID[]", value: $(this).data('subLocationID')});
        });
        var serializeArray = $("#"+formName).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: finalArray2,
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#outstanding_reportContent").html(data);
                $('#outstanding_report_modal').modal("show");
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function generateReportPdf() {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push($(this).val());
            captionChk.push($(this).data('caption'));
        });

        var form= document.getElementById('frm_filter_outstanding');
          document.getElementById('fieldNameChkpdf').value = fieldNameChk;
          document.getElementById('captionChkpdf').value = captionChk;
        form.target='_blank';
        form.action=urlPdf;
        form.submit();
    }

    function profitLossBatchDetails_modal(title, amount, farmID) {
        var from = $('#from').val();
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });
        if(amount)
        {
            $('#profit_table_header').empty();
            if(title == 'Profit Batch '){
                $('#profit_table_header').append('<tr><th>#</th><th>Farm</th><th>Batch</th><th>Profit Amount</th><th>Transaction Amount</th><th>Balance Amount</th></tr>');
            } else if (title == 'Loss Batch '){
                $('#profit_table_header').append('<tr><th>#</th><th>Farm</th><th>Batch</th><th>Loss Amount</th><th>Transaction Amount</th><th>Balance Amount</th></tr>');
            }
            $.ajax({
                type: 'post',
                data: {'farmID': farmID, 'title': title, 'from': from, 'fieldNameChk' : fieldNameChk},
                url: "<?php echo site_url('Buyback/fetchBatchProfitLoss_outstanding'); ?>",
                dataType: 'html',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#profitData').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
            $(".modalTitle").html(title);
            $('#ProfitLossView').modal('show');
        }

    }

    function AdvanceDetails_modal(title, farmID) {
        var from = $('#from').val();
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });

        $('#profit_table_header').empty();
        if(title == 'Loan Details '){
            $('#profit_table_header').append('<tr><th>Farm</th><th>Loan Amount</th><th>Paid Amount</th><th>Balance Amount</th></tr>');
        } else if (title == 'Advance Details '){
            $('#profit_table_header').append('<tr><th>Farm</th><th>Advance Amount</th><th>Paid Amount</th><th>Balance Amount</th></tr>');
        } else if (title == 'Deposit Details '){
          /*  $('#profit_table_header').append('<tr><th>#</th><th>Farm</th><th>Document Code</th><th>Deposit Amount</th></tr>');*/
            $('#profit_table_header').append('<tr><th>Farm</th><th>Deposited Amount</th><th>Paid Amount</th><th>Balance Amount</th></tr>');
        }
        $.ajax({
            type: 'post',
            data: { 'title': title, 'farmID': farmID, 'from': from, 'fieldNameChk' : fieldNameChk},
            url: "<?php echo site_url('Buyback/fetchFarmAdvanceLoan_outstanding'); ?>",
            dataType: 'html',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#profitData').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        $(".modalTitle").html(title);
        $('#ProfitLossView').modal('show');

    }

    function LoanPaidDetails_modal(title, farmID) {
        var from = $('#from').val();
        var fieldNameChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
        });

        $('#loanAdvance_table_header').empty();
        if(title == 'Loan Paid Details ' || title == 'Advance Paid Details ' || title == 'Deposit Paid Details '){
            $('#loanAdvance_table_header').append('<tr><th>#</th><th>Farm</th><th>Document Code</th><th>Total Amount</th><th>Paid Amount</th><th>Balance Amount</th></tr>');
        } else if (title == 'Loan Given Details ' || title == 'Advance Given Details ' || title == 'Deposit Given Details '){
            $('#loanAdvance_table_header').append('<tr><th>#</th><th>Farm</th><th>Document Code</th><th>Given Amount</th></tr>');
        }

        $.ajax({
            type: 'post',
            data: { 'title': title, 'farmID': farmID, 'from': from, 'fieldNameChk' : fieldNameChk},
            url: "<?php echo site_url('Buyback/fetchFarmAdvanceLoanPaid_outstanding'); ?>",
            dataType: 'html',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#VoucherPaidData').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        $(".PaidmodalTitle").html(title);
        $('#VoucherPaidDetailsView').modal('show');

    }
</script>



