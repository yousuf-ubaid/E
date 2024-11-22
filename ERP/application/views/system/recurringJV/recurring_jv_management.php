<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('finance_rj_recurring_journal_voucher_view');
echo head_page($title, false);


/*echo head_page('Recurring Journal Voucher',false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('finance_common_refer_back');?><!--Refer-back-->
                    </td>
                </tr>
            </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/recurringJV/recurring_je_new',null,'<?php echo $this->lang->line('finance_tr_rj_add_new_recurring_journal_voucher');?>'/*Add New Recurring Journal Entry*/,'Recurring Journal Entry');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('finance_tr_rj_create_recurring_journal_voucher');?><!--Create Recurring Journal Voucher--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="recurring_journal_entry_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 12%"><?php echo $this->lang->line('finance_common_rjv_code');?><!--RJV Code--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                <th style="min-width: 13%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    var Otable
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/recurringJV/recurring_jv_management','','Recurring Journal Voucher');
    });
    recurring_journal_entry_table();
});

function recurring_journal_entry_table(){
     Otable = $('#recurring_journal_entry_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Recurring_je/fetch_recurring_journal_entry'); ?>",
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
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');
        },
        "aoColumns": [
            {"mData": "RJVMasterAutoId"},
            {"mData": "RJVcode"},
            {"mData": "RJVNarration"},
            {"mData": "RJVStartDate"},
            {"mData": "RJVEndDate"},
            {"mData": "total_value"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "action"},
            /*{"mData": "JVType"},*/
            {"mData": "RJVNarration"}
            //{"mData": "edit"},
        ],
         "columnDefs": [{"targets": [8], "orderable": false},{"visible":false,"searchable": true,"targets": [9] }],
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

function delete_recurring_journal_entry(id){
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
                data : {'RJVMasterAutoId':id},
                url :"<?php echo site_url('Recurring_je/delete_recurring_journal_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    Otable.draw();
                    stopLoad();
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });        
}

    function referback_journal_entry(id){
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
                    data : {'RJVMasterAutoId':id},
                    url :"<?php echo site_url('Recurring_je/referback_journal_entry'); ?>",
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
                    data : {'RJVMasterAutoId':id},
                    url :"<?php echo site_url('Recurring_je/re_open_journal_entry'); ?>",
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
</script>