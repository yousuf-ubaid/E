<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_custodian_master');
echo head_page($title, false);
?>
<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #custodianType-add-tb td{ padding: 2px; }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openCustodianType_modal()" ><i class="fa fa-plus-square"></i>&nbsp;<?php echo $this->lang->line('common_add');?><!--Add-->  </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_custodian" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('assetmanagement_custodian_type');?><!--Custodian Type--></th>
            <th style="width: 100px"><?php echo $this->lang->line('common_action');?></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="custodian_type_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('assetmanagement_custodian_type');?><!--Custodian Type--></h4>
            </div>
            <form class="form-horizontal" id="custodian_type_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="custodianType-add-tb">
                        <thead>
                        <tr>
                        <th></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs pull-right" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="CustodianTypes[]" placeholder="Type Name" class="form-control saveInputs new-items" />
                            </td>
                            
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_custodian_types()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCustodianModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('assetmanagement_edit_custodian_types');?><!--Edit Custodian Types--></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="editAssetCustodian_form" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('assetmanagement_custodian_type');?><!--Custodian Type--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="assetCustodianTypes" name="assetCustodianTypes">
                                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateAssetCustodian()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

var custodian_tb = $('#custodianType-add-tb');

$(document).ready(function() {
    load_custodian();
        $('.headerclose').click(function(){
            fetchPage('');
        });
    });

function load_custodian(selectedRowID=null){
        var Otable = $('#load_custodian').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/custodian_master'); ?>",
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

                    if( parseInt(oSettings.aoData[x]._aData['']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "custodianName"},
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


    function openCustodianType_modal(){
        $('#custodianType-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#custodian_type_modal').modal({backdrop: "static"});

    }
    function edit_custodian(id, des){
        $('#editCustodianModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#assetCustodianTypes').val( $.trim(des) );
    }
    function add_more(){
        var appendData = '<tr><td><input type="text" name="CustodianTypes[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        custodian_tb.append(appendData);
    }
    function save_custodian_types(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#custodian_type_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('AssetManagement/save_Asset_Custodian'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#custodian_type_modal').modal('hide');
                        load_custodian();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', 'Please fill all fields');
        }
    }
    function delete_custodian(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('AssetManagement/deleteCustodian'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_custodian() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
    function updateAssetCustodian(){
        var postData = $('#editAssetCustodian_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('AssetManagement/updateAssetCustodian'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editCustodianModal').modal('hide');
                    load_custodian($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }
    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>