
<input type="hidden" id="payment_id" value="<?php echo $payment_id ?>">

<div style="padding:2%">
    <p class="text-bold">Invoice Details</p>
    <table id="vendor_invoice_wise" class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">Document Number</th><!--Code-->
                <th style="min-width: 10%">Invoice Date</th><!--Code-->
                <th style="min-width: 10%">Invoice Due Date</th><!--Code-->
                <th style="min-width: 15%">Local Currency Amount</th><!--Code-->
                <th style="min-width: 15%">Bank Currency Amount</th><!--Code-->
                <th style="min-width: 15%">Allocation Amount</th><!--Code-->
                <th style="min-width: 10%">Allocation Status</th><!--Code-->
            
            </tr>
        </thead>
    </table>
</div>

<script>

fetch_vendor_invoice_allocation();

var Otable = '';
function fetch_vendor_invoice_allocation(id){ 
        
        Otable = $('#vendor_invoice_wise').DataTable({
               
                "bProcessing": true,
                "iDisplayLength": 25,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Ap_automation/fetch_vendor_invoice_wise'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

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

                },
                "aoColumns": [
                    {"mData": "id"},
                    {"mData": "bookingInvCode"},
                    {"mData": "invoiceDate"},
                    {"mData": "invoiceDueDate"},
                    {"mData": "current_amount"},
                    {"mData": "bank_amount_due"},
                    {"mData": "allocation_amount"},
                    {"mData": "status"}
                    // {"mData": "view"},
                    // {"mData": "voucher_number"}

                ],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "payment_id","value": $('#payment_id').val()});
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
</script>