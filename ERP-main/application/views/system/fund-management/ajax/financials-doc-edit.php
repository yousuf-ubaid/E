<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$finID = trim($this->input->post('page_id'));

$tittle = 'Financial Details';
echo head_page($tittle, false);
?>
<style>
    .header-div{
        background-color: #afc6dc;
        padding: 1%;
    }

    .details-td{ font-weight: bold; }

    legend{ font-size: 16px !important; }

    .td-main-header{
        color: #000080;
        text-transform: uppercase;
        font-weight: bold;
    }

    .index-td, .sub1{ font-weight: bold; }

    .glDescription{ padding-left: 55px !important;}

    .sub_total_rpt {
        border-top: 1px solid #D2D6DE !important;
        border-bottom: 1px solid #D2D6DE !important;
        font-weight: bold;
    }

    .total_black_rpt {
        border-top: 1px double #000000 !important;
        border-bottom: 3px double #000000 !important;
        font-weight: bold;
        font-size: 12px !important;
        background-color: #DBDBDB;
    }

    tr .hoverTr:hover{
        background-color: #dee8fc !important;
    }

    tr .hoverTr:hover.numeric{
        background-color: #dee8fc !important;
    }

    #submission-confirm-btn{
        margin-top: 15px;
    }
</style>
<div class="masterContainer">
    <div class="row well">
        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                    <td class="bgWhite details-td" id="documentCode" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_statement');?></td>
                    <td class="bgWhite details-td" id="finStatement" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                    <td class="bgWhite details-td" id="fin_narration" width="200px">
                        <a href="#" data-type="text" data-placement="bottom" id="narration_xEditable"
                           data-title="<?php echo $this->lang->line('fn_man_edit_amount');?>"
                           data-pk="" data-value=" ">
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_investment_company');?></td>
                    <td class="bgWhite details-td" id="finCompany" width="200px"></td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                    <td class="bgWhite details-td" id="finCurrency" width="200px">

                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_period');?></td>
                    <td class="bgWhite details-td" id="finPeriod" width="200px" style="text-align: right">
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('fn_man_submission_date');?></td>
                    <td class="bgWhite details-td" width="200px" style="text-align: right" id="finSubDate">
                        <a href="#" data-type="combodate" data-placement="left" id="finSubDate_xEditable"
                           data-title="<?php echo $this->lang->line('fn_man_submission_date');?>" data-pk="1"  data-value="" >
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-12" style="margin-top: 10px; font-weight: bold" id="notice-txt">
            * Expense values must be entered in (-)
        </div>
    </div>
</div>

<div>
    <table class="borderSpace report-table-condensed" id="template-container">

    </table>
</div>

<div style="margin-top: 15px">
    <button type="button" class="btn btn-success btn-sm pull-right"  onclick="submission_confirm()">
        <?php echo $this->lang->line('common_confirm');?>
    </button>
</div>

<script>
    var fin_ID = <?php echo json_encode($finID); ?>;
    let dPlace = 2;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/financials-master-fm', fin_ID, 'Investment');
        });

        load_financial_details();
        //get_attachments_details();
    });

    function submission_confirm() {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
             submission_confirm_callBack();
        });
    }

    function submission_confirm_callBack(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'fin_ID': fin_ID},
            url: "<?php echo site_url('Fund_management/submission_confirm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'm'){
                    bootbox.alert('<div class="alert alert-danger" style="margin-top: 10px;">' + data[1] + '</div>');
                }else{
                    myAlert(data[0], data[1]);
                }


                if(data[0] == 's'){
                    setTimeout(function () {
                        fetchPage('system/fund-management/financials-master-fm', fin_ID, 'Investment');
                    }, 300);
                }

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_financial_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'fin_ID': fin_ID},
            url: "<?php echo site_url('Fund_management/financial_master_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }
                else{
                    var mData = data['masterData'];
                    $('#documentCode').text(mData['documentCode']);
                    $('#finCompany').text(mData['company_name']);
                    $('#finPeriod').text(mData['fn_period']);
                    $('#finStatement').text(mData['reportDes']);
                    $('#finCurrency').text(mData['CurrencyCode']);
                    $('#finSubDate_xEditable').editable('setValue', mData['submissionDate_org'],true);
                    $('#narration_xEditable').editable('setValue', mData['narration'] );
                    dPlace = mData['trCurrencyDPlace'];
                    dPlace = Number(dPlace);

                    let _msg = (mData['docType'] == 'FIN_IS')? 'Expense': 'Liability';

                    $('#notice-txt').text('* '+_msg+' values must be entered in (-)');

                    setTimeout(function () {
                        fin_template_view();
                    }, 300);
                }



            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    $('#narration_xEditable').editable({
        url: '<?php echo site_url('Fund_management/edit_submission_narration?masterID='.$finID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    $('#finSubDate_xEditable').editable({
        format: 'YYYY-MM-DD',
        viewformat: 'DD-MM-YYYY',
        template: 'D / MM / YYYY',
        combodate: {
            minYear: <?php echo format_date_getYear() - 10 ?>,
            maxYear: <?php echo format_date_getYear() + 10 ?>,
            minuteStep: 1
        },
        url: '<?php echo site_url('Fund_management/edit_submission_date?masterID='.$finID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){

                }else{
                    var oldVal = $('#finSubDate_xEditable').data('pk');
                    setTimeout(function (){
                        $('#finSubDate_xEditable').editable('setValue', oldVal, true);
                    },300);
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    function update_amount(obj, glID){
        var amount = $(obj).val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'fin_ID': fin_ID, 'glID': glID, 'amount': amount},
            cache: false,
            url: "<?php echo site_url('Fund_management/update_financialData'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }else{
                    var subTots = data['tot_arr'];
                    if(!$.isEmptyObject(data['tot_arr'])){
                        $.each(subTots, function(key, val){
                            $('#subTot_'+key).html(val);

                            let oldVal = $('#oldSubTot_'+key).text();
                            oldVal = Number( removeCommaSeparateNumber(oldVal) );

                            val = Number( removeCommaSeparateNumber(val) );
                            val = oldVal + val;
                            val = new Intl.NumberFormat('en-US', {
                                'minimumFractionDigits': dPlace, 'maximumFractionDigits': dPlace
                            }).format(val);

                            $('#newSubTot_'+key).html(val);
                        });
                    }
                }

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function fin_template_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'fin_ID': fin_ID},
            url: "<?php echo site_url('Fund_management/finance_template_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#template-container').html(data);
                setTimeout(function(){
                    $(".numeric").numeric({decimalPlaces: dPlace});
                    stopLoad();
                }, 300);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_attachments_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'documentSystemCode': fin_ID, 'systemDocumentID': 'FMIT'},
            url: "<?php echo site_url('Fund_management/get_attachment_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attach_details').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function calculate_new_amount(obj, glID){
        let amount = $(obj).val();
        amount = Number(amount);
        amount = ( isNaN(amount) )? 0: amount;

        let old_amount = $('#old_amount_'+glID).text();
        old_amount = Number( removeCommaSeparateNumber(old_amount) );

        amount = amount + old_amount;
        amount = new Intl.NumberFormat('en-US', {
            'minimumFractionDigits': dPlace, 'maximumFractionDigits': dPlace
        }).format(amount);

        $('#new_amount_'+glID).text(amount);
    }

</script>

<?php
