
<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_over_time_slab');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false); ?>

<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-7">

    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">

        <!--Add Expense Claim-->
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_slab_new',null,'Over Time Slab','EC');">
            <i
                class="fa fa-plus"></i>
            <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="over_time_slab_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_slab', 'Test', 'Expense Claim');
        });
        over_time_slab_table();
    });

    function over_time_slab_table(selectedID=null) {
        Otable = $('#over_time_slab_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('OverTimeSlab/fetch_over_time_slab'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['otSlabsMasterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "otSlabsMasterID"},
                {"mData": "Description"},
                {"mData": "CurrencyCode"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [3], "orderable": false}, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'expenseClaimMasterAutoID': id},
                    url: "<?php echo site_url('ExpenseClaim/delete_expense_claim'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

</script>