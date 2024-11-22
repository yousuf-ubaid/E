<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_Location');
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
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDepartment_modal()" ><i class="fa fa-plus-square"></i>&nbsp;
            <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_location" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width:15%"><?php echo $this->lang->line('common_location_code');?><!--Location Code--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 10%"><?php echo $this->lang->line('assetmanagement_is_free_trade_zone');?><!--Is Free Trade Zone--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_department" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 40%" role="document">
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
                            <th><?php echo $this->lang->line('common_location_code');?><!--Location Code--></th>
                            <th><?php echo $this->lang->line('common_Location');?><!--Location--></th>
                            <th><?php echo $this->lang->line('assetmanagement_is_free_trade_zone');?><!--Is Free trade Zone--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" disabled><i class="fa fa-plus" ></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="locationcode[]" class="form-control saveInputs new-itemslocationcode" />
                            </td>
                            <td>
                                <input type="text" name="location[]" class="form-control saveInputs new-items" />
                            </td>
                            <td style='text-align: center'>
                                <div class='skin-section extraColumnsgreen'>
                                    <input type='checkbox' class='columnSelected locationType' id='locationType' name ='locationType[]' value = '1'>
                                    <!-- <input type="checkbox" class="columnSelected" data-value="req" onchange="changeMandatory(this)" checked >
                                    <input type="hidden" name="locationType[]" class="changeMandatory-req" value="1"> -->
                                </div>
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
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('common_location_code');?><!--Location Code--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="assetLocationcode" name="assetLocationcode">

                                </div>
                            </div>
                        </div>
                    </div>

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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('assetmanagement_is_free_trade_zone');?><!--Is Free Trade Zone--></label>
                                <div class="col-sm-8">

                                <td style='text-align: center'>
                                    <div class='skin-section extraColumnsgreen'>
                                        <input type='checkbox' class='columnSelected assetLocationType' id='assetLocationType' name ='assetLocationType' value = '1'>
                                        
                                    </div>
                                </td>
                                   
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

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
     id="asset_user_groupdetail_model">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="empmodelid">Employee</h4>
            </div>
            <div class="modal-body">

                <div id="sysnc">
                    <div class="table-responsive">
                        <input type="hidden" name="locationid" id="locationid">
                        <table id="employee_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 12%">EMPLOYEE NAME</abbr></th>
                                <th style="width: 12%">EMAIL</th>
                                <th style="width: 5%">&nbsp;
                                    <button type="button" data-text="Add" onclick="addemployee()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Add Employee
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>

            <h4 class="modal-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Added Employees </h4>
            <hr>
            <div id="sysnc">
                <div class="table-responsive">
                    <input type="hidden" name="groupform" id="group_employee">
                    <table id="savedemployee" class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 12%">EMPLOYEE NAME</abbr></th>
                            <th style="min-width: 12%">EMAIL</th>
                            <th style="min-width: 5%">&nbsp;

                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var department_tb = $('#department-add-tb');
    var selectedItemsSync = [];
    var Otable;
    var oTable2;
    var oTable3;
    $(document).ready(function() {
        load_departments();
        $('.headerclose').click(function(){
            fetchPage('system/asset_location_config','Test','<?php echo $this->lang->line('common_Location'); ?>');
        });

        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    });

    function load_departments(selectedRowID=null){
         Otable = $('#load_location').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/location_master_code'); ?>",
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
                {"mData": "locationCode"},
                {"mData": "locationName"},
                {"mData": "locationtype_status"},
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
        $('.extraColumnsgreen input').iCheck('uncheck');
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
                url: '<?php echo site_url('AssetManagement/saveAssetLocationcode'); ?>',
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

    function edit_location(id, des,locationCode,locationType){
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#assetLocationDesc').val( $.trim(des) );
        $('#assetLocationcode').val( $.trim(locationCode) );
        if (locationType == 1) {
            $('#assetLocationType').iCheck('check');
        } else {
            $('#assetLocationType').iCheck('uncheck');
        }
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
                    url :"<?php echo site_url('AssetManagement/deleteAssetLocationcode'); ?>",
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
        var appendData = '<tr><td><input type="text" name="locationcode[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td><input type="text" name="location[]" class="form-control saveInputs new-items" /></td>';
        appendData += "<td style='text-align: center'><div class='skin-section extraColumnsgreen'><input type='checkbox' class='columnSelected locationType' id='locationType' name ='locationType[]' value = '1'></div></td>";
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';
        department_tb.append(appendData);
        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    }

    function updateAssetLocation(){
        var postData = $('#editAssetLocation_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('AssetManagement/updateAssetLocationcode'); ?>',
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

    function add_location_emp(locationid,$locationName,$locationCode,loccodename) {
        selectedItemsSync = [];
        asset_location_user_saved(locationid);
       asset_location_user(locationid);
        $('#locationid').val(locationid);
        $('#empmodelid').html(loccodename);
        $('#asset_user_groupdetail_model').modal('show');
    }

    function asset_location_user(locationid) {
        oTable2 = $('#employee_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('AssetManagement/fetch_employees'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
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
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },

            "aoColumns": [
                {"mData": "primaryKey"},
                {"mData": "employeename"},
                {"mData": "email"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "locationid", "value": locationid});
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
    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function asset_location_user_saved(locationid) {
        oTable3 = $('#savedemployee').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('AssetManagement/fetch_savedemplocation'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
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
                {"mData": "primaryKey"},
                {"mData": "employeename"},
                {"mData": "email"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "locationid", "value": locationid});
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
    function addemployee() {
        var locationid = $('#locationid').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("AssetManagement/link_employee_asset_code"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync, 'locationid': locationid},
            async: false,
            success: function (data) {
                //   myAlert(data[0], data[1]);
                if (data['status']) {
                    refreshNotifications(true);
                    oTable2.draw();
                    Otable.draw();
                    oTable3.draw();
                    $('.extraColumns input').iCheck('uncheck');
                    $("#asset_user_groupdetail_model").modal('show');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function delete_emplocation_code(employeelocationid,locationID,empID) {
        swal({
                title: "Are You Sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('AssetManagement/delete_employee'); ?>",
                    type: 'post',
                    data: {'employeelocationid': employeelocationid,'locationID':locationID,'empID':empID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable3.draw();
                            oTable2.draw();
                            $('.extraColumns input').iCheck('uncheck');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    /* $('input').on('ifChanged', function(){
        changeMandatory(this);
    });
    function changeMandatory(obj, str){
        var status = ($(obj).is(':checked')) ? 1 : 0;
        var str = $(obj).attr('data-value');
        $(obj).closest('tr').closest('tr').find('.changeMandatory-'+str).val(status);
    } */
</script>

