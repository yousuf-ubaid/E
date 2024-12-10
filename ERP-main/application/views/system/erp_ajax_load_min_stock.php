<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="box box-success">
    <div class="box-header with-border">
        <h4 class="box-title">Minimum Stock</h4>
        <div class="box-tools pull-right">
            <strong class="btn-box-tool"><?php echo $this->lang->line('common_currency');?><!--Currency--> : (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                    class="fa fa-times"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;">
        <table class="<?php echo table_class(); ?>" id="mininum_stock_table<?php echo $userDashboardID ?>">
            <thead class="report-header">
            <th><?php echo $this->lang->line('dashboard_item_code');?><!--Item Code--></th>
            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th>Minimum Stock</th>
            <th>Current Stock</th>
            </thead>
        </table>
    </div>
    <!-- /.box-body -->
</div>

<script>
    load_minimum_stock<?php echo $userDashboardID ?>();
    function load_minimum_stock<?php echo $userDashboardID ?>() {
        var Otable = $('#mininum_stock_table'+<?php echo $userDashboardID ?>).DataTable({

            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            //"bFilter": true,
            "bLengthChange": false,
            "bInfo": true,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_minimum_stock'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "minimumQty"},
                {"mData": "currentQty"}
            ],
            "columnDefs": [{"targets": [1], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "period", "value": $("#period"+<?php echo $userDashboardID ?>).val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        })
    }

</script>