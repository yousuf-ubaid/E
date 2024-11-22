<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('erp_data_sync');
    $this->load->library('sequence');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Sales Data Manual Posting', false);
?>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
           
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
       <button class="btn btn-block btn-success-new size-sm" onclick="add_report_view()"><i class="fa fa-plus"></i>&nbsp Create Report</button>
    </div>
</div>
<hr>


<div class="table-responsive">
    <table id="mis_reports" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Report ID</th><!--Code-->
            <th style="min-width: 10%">Report Name</th><!--Code-->
            <th style="min-width: 10%">Type</th><!--Code-->
            <th style="min-width: 10%">Status</th><!--Code-->
            <th style="min-width: 10%">Share To</th><!--Code-->
            <th style="min-width: 10%">Action</th><!--Code-->
        </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>
<hr>


<script type="text/javascript">

    load_mis_reports();

    function add_report_view(){
        fetchPage('system/mis/add_report','Test','Add New Report');
    }

/////////////////////////////////////////////

    function load_mis_reports(){

        var Otable = $('#mis_reports').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Mis/fetch_mis_report_list'); ?>",
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
            "createdRow": function( row, data, dataIndex){
                if( data.header_type1 ==  `Total`){
                    $(row).css('background','#ffe6e6');
                } else if(data.header_type1 ==  `Group Total`){
                    $(row).css('background','#ffe6e6');
                }else if(data.header_type1 ==  `Group Group Total`){
                    $(row).css('background','#ffe6e6');
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "report_id"},
                {"mData": "report_name"},
                {"mData": "type"},
                {"mData": "status"},
                {"mData": "share_group"},
                {"mData": "view"},
                // {"mData": "mapping_type"},
                // {"mData": "control_acc"},
                // {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({ "name": "config_id","value": $("#config_id").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
        });

    }

//////////////////////////////////////////

    function edit_config_report(id){
        fetchPage('system/mis/add_report','Test','Add New Report','',id);
    }

</script>