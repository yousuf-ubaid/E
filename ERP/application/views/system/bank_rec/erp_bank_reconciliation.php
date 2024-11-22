<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_ap_br_bank_reconcilation');
echo head_page($title, false);
/*echo head_page('Bank Reconciliation',false); */?>

<div id="filter-panel" class="collapse filter-panel"></div>
<!--<div class="row">
    <div class="col-md-5">
        <table class="<?php /*echo table_class(); */?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Confirmed /
                    Approved
                </td>
                <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                    / Not Approved
                </td>

            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_new',null,'Add Bank Reconciliation','Bank Reconciliation');"><i class="fa fa-plus"></i> New Bank Reconciliation </button>
    </div>
</div><hr>-->
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_report',null,'Bank Reconciliation Report','Bank Reconciliation');"> Generate Report </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="bank_rec" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th><?php echo $this->lang->line('treasury_bta_gl_code_secondary');?><!--GL Code Secondary--></th>
            <th><?php echo $this->lang->line('treasury_common_gl_description');?><!--GL Description--></th>
            <th><?php echo $this->lang->line('common_bank');?><!--Bank--></th>
            <th><?php echo $this->lang->line('common_branch');?><!--Branch--></th>
            <th><?php echo $this->lang->line('treasury_common_swift');?><!--SWIFT--></th>
            <th><?php echo $this->lang->line('treasury_common_account_number');?><!--Account Number--></th>
            <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th><?php echo $this->lang->line('treasury_ap_br_un_book_balance');?><!--Book Balance--></th>
            <th></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/erp_bank_reconciliation','','Bank Reconciliation');
        });

    });

    window.Otable=  $('#bank_rec').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Bank_rec/fetch_bank_rec_entry'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var
                tmp_i = oSettings._iDisplayStart;
            var
                iLen = oSettings.aiDisplay.length;
            var
                x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }

        },
        "aoColumns": [
            {"mData": "GLAutoID"},
            {"mData": "systemAccountCode"},
            {"mData": "GLSecondaryCode"},
            {"mData": "GLDescription"},
            {"mData": "bankName"},
            {"mData": "bankBranch"},
            {"mData": "bankSwiftCode"},
            {"mData": "bankAccountNumber"},
            {"mData": "bankCurrencyCode"},
            {"mData": "totalAmount"},
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





</script>