<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_location');
echo head_page($title, false);
?>

<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #department-add-tb td{ padding: 2px; }
</style>

<?php /*echo head_page('Asset Location',false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDepartment_modal()" ><i class="fa fa-plus-square"></i>&nbsp;<?php echo $this->lang->line('common_add');?><!--Add-->  </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_location" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_department" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('assetmanagement_add_location');?><!--Add Location--></h4>
            </div>
            <form class="form-horizontal" id="add-department_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="department-add-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_Location');?><!--Location--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="location[]" class="form-control saveInputs new-items" />
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_department()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('assetmanagement_edit_location_description');?><!--Edit Location Description--></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="editAssetLocation_form" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="assetLocationDesc" name="assetLocationDesc">
                                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateAssetLocation()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var department_tb = $('#department-add-tb');

    $(document).ready(function() {
        load_departments();
        $('.headerclose').click(function(){
            fetchPage('system/hrm/department_master','Test','HRMS');
        });
    });

    function load_departments(selectedRowID=null){
        var Otable = $('#load_location').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/location_master'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                /*if (oSettings.bSorted || oSettings.bFiltered) {
                 for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                 $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                 if( parseInt(oSettings.aoData[i]._aData['DepartmentMasterID']) == selectedRowID ){
                 var thisRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
                 $(thisRow).addClass('dataTable_selectedTr');
                 }
                 }
                 }*/


                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['DepartmentMasterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "locationID"},
                {"mData": "locationName"},
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

    function openDepartment_modal(){
        $('#department-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_department').modal({backdrop: "static"});
    }

    function save_department(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#add-department_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('AssetManagement/saveAssetLocation'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_department').modal('hide');
                        load_departments();
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

    function edit_location(id, des){
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#assetLocationDesc').val( $.trim(des) );
    }

    function delete_location(id){
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
                    url :"<?php echo site_url('AssetManagement/deleteAssetLocation'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'locationID':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_departments() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });

    function add_more(){
        var appendData = '<tr><td><input type="text" name="location[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        department_tb.append(appendData);
    }

    function updateAssetLocation(){
        var postData = $('#editAssetLocation_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('AssetManagement/updateAssetLocation'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_departments($('#hidden-id').val());
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

