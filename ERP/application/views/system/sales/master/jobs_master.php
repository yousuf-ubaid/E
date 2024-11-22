<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $title = 'Jobs';
    echo head_page($title, true);

?>


<div class="row">

    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed'); ?> / <?php echo $this->lang->line('common_approved'); ?>
                    <!--Confirmed-->
                    <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed'); ?>
                    /<?php echo $this->lang->line('common_not_approved'); ?>
                    <!-- Not Confirmed-->
                    <!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back'); ?>
                    <!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm " onclick="fetchPage('system/sales/master/jobs_create',null,'<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice'); ?>','PV');"><i class="fa fa-plus"></i> <?php echo 'Create Job' ?></button>
        <!--Create Invoice-->
        <a href="#" type="button" class="btn btn-success-new size-sm " style="margin-left: 2px" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Excel <!--Excel-->
        </a>
    </div>

</div>

<hr>


<div class="table-responsive">
    <table id="jobs_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 15%"><?php echo 'Job Number'; ?></th>

                <th style="width: 20%"><?php echo 'Job Name'; ?></th>
                <!--Invoice Code-->
                <th style="width: 12%"><?php echo 'Doc Date'; ?></th>
                <!--Details-->
                <th style="width: 20%"><?php echo 'Description'; ?></th>
                <!--Total Value-->
                <th style="width: 5%"><?php echo 'Value' ?></th>
                <!--Total Value-->
                <th style="width: 10%"><?php echo 'Inv Status' ?></th>
                <!--Confirmed-->
                <th style="width: 5%"><?php echo $this->lang->line('common_confirmed'); ?></th>
                <!--Approved-->
                <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                <!--Action-->
            </tr>
        </thead>
    </table>
</div>

<script>

    $(document).ready(function() {

        $('.headerclose').click(function(){
            fetchPage('system/sales/master/jobs_master','','Contracts');
        });

    });

    load_jobs_master();

    function load_jobs_master(){

        Otable = $('#jobs_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_jobs_master'); ?>",
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
                {"mData": "job_code"},
                {"mData": "job_name"},
                {"mData": "doc_date"},
                {"mData": "job_description"},
                {"mData": "localTotalAmount"},
                {"mData": "invstatus"},
                {"mData": "confirmed"},
                {"mData": "action"},
        
            ],
            "columnDefs": [
              
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
              
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


