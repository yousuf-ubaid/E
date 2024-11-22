<?php
echo head_page('<i class="fa fa-archive"></i> Finance Posting', false);
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
<div class="col-md-2 pull-right"></div>
</div>
<div class="table-responsive">
    <table id="bill_status_table" class="<?php echo table_class(); ?> table-hover">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Employee Name</th>
            <th>Outlet</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th></th>
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
            "sAjaxSource": "<?php echo site_url('Pos_restaurant_report/shift_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id_store"},//this will overwrite by the sequence.
                {"mData": "Ename1"},
                {"mData": "wareHouseDescription"},
                {"mData": "startTime"},
                {"mData": "endTime"},
                {"mData": "action"}
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

    function manual_function_finance_posting(){
        var shift_id = $(this).data('shift_id');
        bootbox.confirm({
            message: 'Are you sure you want to run the finance function now?',
            buttons: {
                'cancel': {
                    label: 'Cancel', /*cancel*/
                },
                'confirm': {
                    label: 'Ok', /*Ok*/
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {"shift_id":shift_id},
                        url: "<?php echo site_url('Pos_restaurant/ManualFinancePosting'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if(data.status=='success'){
                                load_bill_status_table();
                                myAlert('s',data.message);
                            }else{
                                myAlert('e',data.message);
                            }
                            stopLoad();
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            }
        });
    }
</script>



