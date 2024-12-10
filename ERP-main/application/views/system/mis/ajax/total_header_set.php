<?php
     $primaryLanguage = getPrimaryLanguage();
     $this->lang->load('common', $primaryLanguage);
     $plus_arr = array(''=> 'Select Transaction Type', '1'=>'Plus ( + )','-1'=>'Minus ( - )');
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo 'Add chart of accounts';?></h4>
</div>

<div class="modal-body">

    <input type="hidden" name="config_row_id" id="config_row_id" value="<?php echo $config_row_id ?>" > 
    
    <table class="<?php echo table_class() ?>">
        <tr>
            
            <td> 
                <label for="inputEmail3" class="col-sm-2 control-label">Headers to Add</label><!--Status-->

                <div class="col-sm-4">
                    <?php echo form_dropdown('added_headers',$added_reports,'', 'class="form-control" id="added_headers"'); ?>
                </div>

                <div class="col-sm-4">
                    <?php echo form_dropdown('plus_minus',$plus_arr,'', 'class="form-control" id="plus_minus"'); ?>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success" id="btn_add_chart">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </td><!--Approved-->

        </tr>
    <table>

    <hr>

    <table id="config_mapping_details" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Header Type 2</th><!--Code-->
            <th style="min-width: 10%">Category ID</th><!--Code-->
            <th style="min-width: 10%">Category Descripiton</th><!--Code-->
            <th style="min-width: 10%">Transaction Type</th><!--Code-->
            <th style="min-width: 10%">Action</th><!--Code-->
        </tr>
        </thead>
        <tbody>
          
            
        </tbody>
    </table>

</div>


<script type="text/javascript">
    
    added_config_details_table();

    // $('.select2').select2();

    $('#btn_add_chart').click( () => {

        var added_headers = $('#added_headers').val();
        var plus_minus = $('#plus_minus').val();
        var config_row_id = $('#config_row_id').val();

        $.ajax({
            async: true,
            type: 'post',
            data: {'config_row_id': config_row_id,'added_headers': added_headers,'plus_minus': plus_minus},
            url: "<?php echo site_url('Mis/add_config_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                added_config_details_table();
                
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    });

    //////////////////////////////////////////////////////////////////////////

    function added_config_details_table(){

        var Otable = $('#config_mapping_details').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Mis/fetch_config_details'); ?>",
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
                {"mData": "header_type_mapped"},
                {"mData": "category_id"},
                {"mData": "category_description"},
                {"mData": "value"},
                {"mData": "view"},
                // {"mData": "view"},
                // {"mData": "mapping_type"},
                // {"mData": "control_acc"},
                // {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

               aoData.push({ "name": "config_row_id","value": $("#config_row_id").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
           // "order": [6]
        });

    }

    ///////////////////////////////////////////////////////////////////////////

    function delete_config_detail(id){

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this');?>",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'config_detail_id': id},
                    url: "<?php echo site_url('Mis/delete_config_row_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        added_config_details_table();

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }
   

</script>


           