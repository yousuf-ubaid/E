<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_request_note');
echo head_page($title, false);
?>
<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #assetRequest-add-tb td{ padding: 2px; }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12 pull-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/asset_management/asset_request_addnew',null,'Add Asset Request','ARN');"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('assetmanagement_add_request');?><!--Create Asset Request-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">

<input type="hidden" name="masterID" id="masterID">
    <table id="request_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('assetmanagement_request_note_code');?><!--Request Note Code--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?><!--Details--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_requested_by');?><!--Requested By--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
        
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">

var masterID = null;
    $(document).ready(function() {
        // Log when the page is loaded
    load_request_table(); 
       
    });
   

    function load_request_table(selectedRowID=null){
       
    var Otable = $('#request_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('AssetManagement/load_request'); ?>",
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

                if( parseInt(oSettings.aoData[x]._aData['id']) == selectedRowID ){
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');
                    masterID = parseInt(oSettings.aoData[x]._aData['id']); // Set masterID equal to id
                    // Log masterID
                }

                x++;
            }
        },
        "aoColumns": [
            {"mData": "id"},
            {"mData": "documentID"},
            {"mData": "documentDate"},
            {"mData": "requestedByName"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"}
        ],
        "columnDefs": [{"searchable": false, "targets": [0]}],
        "fnServerData": function (sSource, aoData, fnCallback) {
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


function asset_master_delete_item(id) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id},
                    url: "<?php echo site_url('AssetManagement/delete_asset_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (data.success) {
                            // Deletion successful
                            Otable.draw();
                            stopLoad();
                            refreshNotifications(true);
                        } else {
                            // Server returned an error
                            swal("Error", data.message, "error");
                            stopLoad();
                        }
                    },  
                });
            });
    }
// function delete_item(id) {
//         if (masterID) {
//             swal({
//                     title: "<?php echo $this->lang->line('common_are_you_sure');?>",
//                     text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
//                     type: "warning",
//                     showCancelButton: true,
//                     confirmButtonColor: "#DD6B55 ",
//                     confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
//                     cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
//                 },
//                 function () {
//                     $.ajax({
//                         async: true,
//                         type: 'post',
//                         dataType: 'json',
//                         data: {'id': requestID},
//                         url: "<?php echo site_url('AssetManagement/delete_asset_request'); ?>",
//                         beforeSend: function () {
//                             startLoad();
//                         },
//                         success: function (data) {
//                             load_request_table();
//                             stopLoad();
//                             refreshNotifications(true);
//                         }, error: function () {
//                             swal("Cancelled", "Your file is safe :)", "error");
//                         }
//                     });
//                 });
//         }
//     }
    

    


</script>
