<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_receivable_tr_rm_receipt_matching');
echo head_page($title, false);
/*echo head_page('Receipt Matching',false); */?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed --> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('accounts_receivable_common_refer_back');?><!--Refer-back-->
                    </td>
                </tr>
            </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/receipt_voucher/erp_receipt_match',null,'<?php echo $this->lang->line('accounts_receivable_tr_rm_add_new_receipt_matching');?>'/*Add New Receipt Matching*/,'RVM');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_receivable_tr_rm_create_receipt_matching');?><!--Create Receipt Matching--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="receipt_match_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('accounts_receivable_common_rvm_code');?></th><!--RVM Code-->
                <th style="min-width: 45%"><?php echo $this->lang->line('common_details');?><!--Details--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value');?><!--Total Value--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
var receiptVoucherAutoId;
var Otable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/receipt_voucher/receipt_match_management','','Receipt Matching');
    });
    receiptVoucherAutoId = null;
    number_validation();
    receipt_match_table(); 
});

function receipt_match_table(selectedID=null){
     Otable = $('#receipt_match_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Receipt_voucher/fetch_receipt_match'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                if( parseInt(oSettings.aoData[x]._aData['matchID']) == selectedRowID ){
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');
                }
                x++;
            }
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');
        },
        "aoColumns": [
            {"mData": "matchID"},
            {"mData": "matchSystemCode"},
            {"mData": "detail"},
            {"mData": "total_value"},
            {"mData": "confirmed"},
            {"mData": "edit"},
            {"mData": "Narration"},
            {"mData": "cusmascustomername"},
            {"mData": "matchDate"},
            {"mData": "transactionCurrency"},
            {"mData": "detTransactionAmount"},
            {"mData": "refNo"}
            //{"mData": "edit"},
        ],
         "columnDefs": [{"targets": [5], "orderable": false},{"visible":false,"searchable": true,"targets": [6,7,8,9,10,11] }, {"searchable": false, "targets": [0]}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
            //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

function delete_rvm_item(id,value){
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
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'matchID':id},
                url :"<?php echo site_url('Receipt_voucher/delete_rv_match'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    Otable.draw();
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });        
}

    function referbackReceiptMatch(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'matchID':id},
                    url :"<?php echo site_url('Receipt_voucher/referback_receipt_match'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

function reOpen_contract(id){
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'matchID':id},
                url :"<?php echo site_url('Receipt_voucher/re_open_receipt_match'); ?>",
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
function issystemgenerateddoc_rvm(type) {
    swal(" ", "This is System Generated Document,You Cannot "+type+" this document", "error");
}
</script>