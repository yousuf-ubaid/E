<?php
     $primaryLanguage = getPrimaryLanguage();
     $this->lang->load('common', $primaryLanguage);
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
                <label for="inputEmail3" class="col-sm-2 control-label">Chart of Accounts</label><!--Status-->

                <div class="col-sm-8">
                    <?php echo form_dropdown('added_chartofaccounts',$chart_of_accounts,'', 'class="form-control select2" id="added_chartofaccounts" required'); ?>
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

    <table id="config_mapping_chart_accounts" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">GL Code</th><!--Code-->
            <th style="min-width: 10%">GL Description</th><!--Code-->
            <th style="min-width: 10%">Type</th><!--Code-->
            <th style="min-width: 10%">Action</th><!--Code-->
        </tr>
        </thead>
        <tbody>
          
            
        </tbody>
    </table>

</div>


<script type="text/javascript">
    
    added_chart_of_accounts_table();

    $('.select2').select2();

    $('#btn_add_chart').click( () => {

        var selected_chart_of_account = $('#added_chartofaccounts').val();
        var selected_chart_of_account_text = $('#added_chartofaccounts :selected').text();
        var config_row_id = $('#config_row_id').val();

        $.ajax({
            async: true,
            type: 'post',
            data: {'config_row_id': config_row_id,'selected_chart_of_account': selected_chart_of_account,'selected_chart_of_account_text': selected_chart_of_account_text,'is_chartofaccount': 1},
            url: "<?php echo site_url('Mis/add_config_chartofaccount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                added_chart_of_accounts_table();
                
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    });

    //////////////////////////////////////////////////////////////////////////

    function added_chart_of_accounts_table(){

        var Otable = $('#config_mapping_chart_accounts').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Mis/fetch_mis_report_added_chart_of_accounts'); ?>",
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
                {"mData": "gl_code"},
                {"mData": "gl_code_description"},
                {"mData": "type"},
                {"mData": "view"},
                // {"mData": "sort_order"},
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

  

    function delete_chartof_records(id){
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
                        data: {'config_row_id': id},
                        url: "<?php echo site_url('Mis/delete_added_chart_of_account'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            added_chart_of_accounts_table();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


</script>


           