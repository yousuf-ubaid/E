<?php
echo head_page('<i class="fa fa-archive"></i> Bill Status', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';print_r($locations);echo '<pre>';*/
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

<div id="filter-panel" class="collapse filter-panel"></div>



<div class="col-md-2 pull-right">


</div>
</div>


<div class="table-responsive">
    <table id="bill_status_table" class="<?php echo table_class(); ?> table-hover">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Invoice Code</th>
            <th>Menu Sales Date</th>
            <th>Created Date</th>
            <th style="min-width: 150px;">Created User</th>
            <th>Status</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function () {
        load_bill_status_table();
    });
    function load_bill_status_table(){
        TMRpt_table = $('#bill_status_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant_report/bill_status'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "menuSalesID"},//this will overwrite by the sequence.
                {"mData": "invoiceCode"},
                {"mData": "menuSalesDate"},
                {"mData": "createdDateTime"},
                {"mData": "modifiedUserName"},
                {"mData": "isUpdated"}
            ],
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
</script>



