<?php
/**
 * Created by PhpStorm.
 * Date: 2020-08-19
 * Time: 11:25 AM
 */
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = "Status Master";
echo head_page($title  , false);
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openStatus_modal()" ><i class="fa fa-plus-square"></i>&nbsp;
            <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_status" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto">Status Description</th>
            <th style="width: auto"><?php echo $this->lang->line('common_type');?><!--Type--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="new_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="status_modal_header">Add Status</h4>
            </div>
            <form class="form-horizontal" id="add_status_form" autocomplete="off">
                <input type="hidden" class="form-control" id="hidden_statusid" name="hidden_statusid">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="statusDescription" name="statusDescription">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                 <!--2 - partialReleased  3 - declarationStatus 4 - processingStatus 5-paymentStatus 6 - reviewStatus -->
                                 <label class="col-sm-4 control-label">Type</label>
                                    <div class="col-sm-8">
                                        <?php echo form_dropdown('statusType', array('' =>  $this->lang->line('common_select_type')/*'Select Type'*/, '2' => 'Partial Released', '3' => 'Declaration Status','4' => 'Processing Status','5' => 'Payment Status','6' => 'Review Status'), 'Inventory', 'class="form-control select2" id="statusType" required'); ?>

                                       <!-- <input type="text" class="form-control" id="description" name="description">-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_status()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/logistics/status','Test','Status Master');
        });
        load_status();
    });

    function load_status(selectedRowID=null){
        $('#load_status').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Logistics/fetch_status'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [ {
                "targets": [0],
                "searchable": false,
                "orderable": false
            } ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if( parseInt(oSettings.aoData[x]._aData['statusID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "statusID"},
                {"mData": "statusDescription"},
                {"mData": "typeText"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [3], "orderable": false},{"visible":true,"searchable": false,"targets": [0,3] }],

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

    function openStatus_modal(){
        $('#status_modal_header').html('Add Status');
        $('#statusDescription').val('');
        $('#statusType').val('');
        $('#hidden_statusid').val('');
        $('#new_status').modal({backdrop: "static"});
    }

    function save_status(){
            var postData = $('#add_status_form').serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Logistics/save_status'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_status').modal('hide');
                        load_status();
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
    }

    function edit_status(id,description,type){
        $('#status_modal_header').html('Edit Status');
        $('#statusDescription').val($.trim(description));
        $('#statusType').val(type);
        $('#hidden_statusid').val(id);
        $('#new_status').modal({backdrop: "static"});
    }

    function delete_status(id, description){
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
                    async : true,
                    url :"<?php echo site_url('Logistics/delete_status'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_status() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
 </script>
