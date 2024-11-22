<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Data Mappings', false);
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
       <button class="btn btn-block btn-success" onclick="create_edit_posting()"><i class="fa fa-plus"></i>&nbsp Create New</button>
    </div>
</div>
<hr>


<div class="table-responsive">
    <table id="clent_general" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Type</th><!--Code-->
            <th style="min-width: 10%">Description</th><!--Code-->
            <th style="min-width: 10%">Service Type</th><!--Code-->
            <th style="min-width: 10%">Mode of Collection</th><!--Code-->
            <th style="min-width: 10%">Status</th><!--Code-->
            <th style="min-width: 10%">Action</th><!--Code-->
            <th style="min-width: 10%">Activate</th><!--Code-->
        </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

<!-- Scripting area -->
<script type="text/javascript">

    function create_edit_posting(){
        fetchPage('system/ecommerce/sales_new_data_mapping','Test','Sales Data Mapping');
    }

    sales_client_mapping_table();

    function sales_client_mapping_table() {
        var Otable = $('#clent_general').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('DataSync/fetch_sales_posting'); ?>",
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
                {"mData": "group"},
                {"mData": "description"},
                {"mData": "service_type"},
                {"mData": "mode_collection"},
                {"mData": "status"},
                {"mData": "edit_ui"},
                {"mData": "switch"},
                // {"mData": "erp_c"},
                // {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({ "name": "posting_id","value": $("#posting_id").val()});
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

//////////////////////////////////////////////////////////////

    function data_edit_posting(id){
        fetchPage('system/ecommerce/sales_new_data_mapping','Test','Sales Data Mapping','',id);
    }

///////////////////////////////////////////////////////////

    function change_posting_active_inactive(id){

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure to change this posting status",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mapping_id': id},
                    url: "<?php echo site_url('DataSync/change_posting_status'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        sales_client_mapping_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
      
    }

///////////////////////////////////////////////////////////

    function data_delete_posting(id){

            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure you want to delete this posting",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mapping_id': id},
                    url: "<?php echo site_url('DataSync/delete_posting_status'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        sales_client_mapping_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
      
    }

</script>