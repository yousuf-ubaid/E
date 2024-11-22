<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($this->input->post('page_name'),false);



?>

<hr/>

<div id="filter-panel" class="collapse filter-panel"></div>
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
   var Otable;
    $(document).ready(function() {

        $('.headerclose').click(function(){
            fetchPage('system/bank_register/erp_bank_register','','Cash Register');
        });
        bank_rec();
    });

   Inputmask().mask(document.querySelectorAll("input"));

   function clearsearch(){
       $('#datefrom').val('');
       $('#dateto').val('');
       bank_loaddata();

   }

   function filtersearch() {

           if($('#dateto').val() ==''){
               myAlert('e','Please Select Date to');
               return false;
           }
           if($('#datefrom').val()==''){
               myAlert('e','Please Select Date From');
               return false;
           }

       bank_loaddata()


   }

   function bank_loaddata(){
       $.ajax({
           async: true,
           type: 'post',
           dataType: 'html',
           data: {GLAutoID: GLAutoID,filter_status: $('#filter_status').val(),dateto:$('#dateto').val(),datefrom:$('#datefrom').val()},
           url: "<?php echo site_url('Bank_rec/load_bank_register_details'); ?>",
           beforeSend: function () {
               startLoad();
           },
           success: function (data) {
               $('#load_generated_table').html(data);

               stopLoad();
               refreshNotifications(true);
           }, error: function () {
               alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
               stopLoad();
               refreshNotifications(true);
           }
       });
   }



    function bank_rec(){
        window.Otable = $('#bank_rec').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/fetch_bank_register_entry'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "systemAccountCode"},
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
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "dateto","value": $('#dateto').val()});
                aoData.push({ "name": "datefrom","value": $('#datefrom').val()});
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