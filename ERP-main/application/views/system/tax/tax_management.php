<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_tax');
echo head_page($title, false);

/*echo head_page('Tax',false); */?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/tax/erp_tax_new',null,'<?php echo $this->lang->line('tax_add_tax')?>','Tax');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('tax_create_tax');?><!--Create Tax--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="tax_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_type');?><!--Type--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('tax_description');?><!--Tax Description--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('tax_authority');?><!--Authority--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
$(document).ready(function () {
    $('.headerclose').click(function(){
        fetchPage('system/tax/tax_management','Test','TAX');
    });
    tax_table();
});
    function tax_table() {
        var Otable = $('#tax_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Tax/load_tax'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

	        },
	        "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
	            var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
	        },
            "aoColumns": [
                {"mData": "taxMasterAutoID"},
                {"mData": "type"},
                {"mData": "taxShortCode"},
                {"mData": "taxDescription"},
                {"mData": "supplier"},
                {"mData": "status"},
                {"mData": "action"},
                {"mData": "supplierSystemCode"},
                {"mData": "supplierName"}
            ],
            "columnDefs": [{"searchable": true, "visible": false, "orderable": false, "targets": [7,8]},{"searchable": false, "targets": [0,4]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
	}

    function delete_tax(id, value) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'taxMasterAutoID': id,'value':value},
                    url: "<?php echo site_url('Tax/delete_tax'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        tax_table();
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>