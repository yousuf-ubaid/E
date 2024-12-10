<?php
echo head_page('Crew Group', true);
$customer_arr = all_customer_drop(true);
$customer_drp = all_customer_drop();
$customer_arr_masterlevel = array('' => 'Select Customer');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$contract_type = all_contarct_types();
$gl_code_arr    = fetch_all_gl_codes();
$uom_arr    = all_umo_new_drop();
$location_arr    = op_location_drop();
$segment_arr    = fetch_segment(True);
?>
<div id="filter-panel" class="filter-panel" xmlns="http://www.w3.org/1999/html">
    <div class="row">

    </div>
</div>

<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="openCrewmodel()"><i class="fa fa-plus"></i> Create Group </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="crew_group_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 15%">Crew Group</th>
            <th style="min-width: 2%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog"  id="crew_group_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Group</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="supplierPrimaryCode">Crew Group </label>
                        <input type="text" class="form-control" id="groupName" name="groupName">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="contractmastesavebtn" onclick="save_crew_group()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="crew_group_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Crew Members</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="groupID" name="groupID">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="supplierPrimaryCode">Crew Group </label>
                        <input type="text" class="form-control" onchange="update_crew_group()" id="groupNamehn" name="groupName">
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <select name="empID" id="empID" class="form-control searchbox" multiple="multiple"></select>
                    </div>
                    <div class="form-group col-sm-2">
                        <button class="btn btn-primary btn-xs" onclick="add_members_to_group()" type="button">Add Crew</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="crew_members_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 2%">#</th>
                            <th style="min-width: 15%">Emp Id</th>
                            <th style="min-width: 15%">Employee Name</th>
                            <th style="min-width: 10%">Supervisor Y/N</th>
                            <th style="min-width: 2%">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    var Otablecrew;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operations/masters/crew_master', '', 'Crew Group');
        });
        crew_master_table();
        $('.select2').select2();

        $("#empID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });

    });


    function crew_master_table(selectedID=null) {
        Otable = $('#crew_group_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/fetch_crew_master_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['groupID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "groupID"},
                {"mData": "groupName"},
                {"mData": "actiongrp"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
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

    function openCrewmodel() {
        $('#groupName').val('');
        $('#crew_group_modal').modal('show');
    }

    function save_crew_group() {
       var groupName= $('#groupName').val();

        if (jQuery.isEmptyObject(groupName)) {
            myAlert('e','Crew Group is required');
            return false;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'groupName': groupName},
            url: "<?php echo site_url('Operation/save_crew_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    Otable.draw();
                    $('#crew_group_modal').modal('hide');
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function deleteOpCrewGroup(groupID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'groupID': groupID},
                    url: "<?php echo site_url('Operation/deleteOpCrewGroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        Otable.draw();
                        myAlert(data[0], data[1]);

                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            });
    }

    function openEditcrewgroup(groupID,groupName) {
        $('#groupID').val(groupID);
        $('#groupNamehn').val(groupName);
        $('#crew_group_edit_modal').modal('show');
        load_crew_members_drop();
        load_crew_members_table()
    }

    function update_crew_group() {
       var groupID= $('#groupID').val();
       var groupName= $('#groupNamehn').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'groupID': groupID,'groupName': groupName},
            url: "<?php echo site_url('Operation/update_crew_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                Otable.draw();
                myAlert(data[0], data[1]);

            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });
    }

    function load_crew_members_drop() {
       var groupID= $('#groupID').val();
        $('#empID option').remove();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_crew_members_drop"); ?>',
            dataType: 'json',
            data: {'groupID': groupID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#empID').empty();
                    var mySelect = $('#empID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['EIdNo']).html(text['Ename2'] +' - '+ text['ECode']));
                    });
                }
                $('#empID').multiselect2('rebuild');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function load_crew_members_table(selectedID=null) {
        Otablecrew = $('#crew_members_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_crew_members_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['crewmemberID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });

                $('input').on('ifChecked', function (event) {
                    if ($(this).hasClass('supervisor')) {
                        update_supervisor_yn(this,1);
                    }
                });

                $('input').on('ifUnchecked', function (event) {
                    if ($(this).hasClass('supervisor')) {
                        update_supervisor_yn(this,0);
                    }
                });
            },
            "aoColumns": [
                {"mData": "crewmemberID"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "supervisorYN"},
                {"mData": "actionmember"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "groupID", "value": $("#groupID").val()});
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

    function add_members_to_group() {
        var empID=$('#empID').val();
        var groupID=$("#groupID").val();

        if (jQuery.isEmptyObject(empID)) {
            myAlert('e','Select Crew');
            return false;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID,'groupID': groupID},
            url: "<?php echo site_url('Operation/add_members_to_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                Otablecrew.draw();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    $('#crew_group_edit_modal').modal('hide');
                }


            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });

    }


    function update_supervisor_yn(ds,valu) {
        var crewmemberID=ds.value;
        var valu=valu;
        var groupID=$("#groupID").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'crewmemberID': crewmemberID,'valu': valu,'groupID': groupID},
            url: "<?php echo site_url('Operation/update_supervisor_yn'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    setTimeout(function () {
                        Otablecrew.draw();
                    }, 300);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function deleteOpCrewMember(crewmemberID) {
        var groupID=$("#groupID").val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'crewmemberID': crewmemberID,'groupID': groupID},
                    url: "<?php echo site_url('Operation/deleteOpCrewMember'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0]=='s'){
                            $('#crew_group_edit_modal').modal('hide');
                        }

                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            });
    }


</script>