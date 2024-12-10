<?php
    $this->load->helper('ap_automation');
    $inv_currency = '';
    $bank_currency = '';
    if($master_id){
        $posting_detials = get_automation_payment_master_by_id($master_id);
        $date = $posting_detials['date'];
        $dateFrom = date('Y-m-d',strtotime($posting_detials['selection_date_from']));
        $dateTo = date('Y-m-d',strtotime($posting_detials['selection_date_to']));
        $bank_currency_id = $posting_detials['bank_currency'];
        $transaction_currency_id = $posting_detials['transaction_currency_id'];
        $funding_availability =  $posting_detials['funding_availability'];
        //$posting_detials['status'] = 1;
      
    }
?>

<input type="hidden" id="master_id" name="master_id" value="<?php echo $master_id ?>">


<div style="padding:2%">
    <p class="text-bold">Vendor Details</p>
    <table id="vendor_allocation" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th class="pull " style="min-width: 5%">#</th>
            <th style="min-width: 10%">Vendor Code</th><!--Code-->
            <th style="min-width: 10%">Name</th><!--Code-->
            <!-- <th style="min-width: 10%">Local Balance Due <span id="inv_currency"><?php echo $inv_currency ?></span></th>Code -->
            <th style="min-width: 10%">Bank Balance Due <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
            <th style="min-width: 10%">Schedule for PMT <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
            <th style="min-width: 10%">Allocation <span id="fund_currency"><?php echo $bank_currency ?></span></th><!--Code-->
            <th style="min-width: 5%"></th><!--Code-->
            
        
        </tr>
        </thead>
    </table>
</div>

<script>

fetch_vendor_allocations();


var OtableVendor = '';
    function fetch_vendor_allocations(){
        
        var OtableVendor = $('#vendor_allocation').DataTable({
                
                "bProcessing": true,
                "iDisplayLength": 100,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Ap_automation/fetch_vendor_allocation'); ?>",
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
                    {"mData": "vendor_code"},
                    {"mData": "vendor_name"},
                    //  {"mData": "local_balance_due"},
                    {"mData": "balance_due"},
                    {"mData": "schedule_pmt"},
                    {"mData": "allocation"},
                    {"mData": "view"}
                  
                ],
                "columnDefs": [{"searchable": false, "targets": [0,3,4,5]},{"searchable": true, "targets": [0,3,4,5]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "master_id","value": $("#master_id").val()});
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