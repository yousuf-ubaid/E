<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->input->post('page_name'), false);
$main_grp = fetch_main_group();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="form-group col-sm-4 hidden">
        <label class="control-label"> <?php echo $this->lang->line('config_common_main_group');?><!--Main Group--></label>
        <div class="">
            <?php echo form_dropdown('companyGroupID', $main_grp, '', 'class="form-control select2" style="" onchange="load_sub_group(),load_sub_group_employees()" id="companyGroupID"'); ?>
        </div>
    </div>
    <div class="form-group col-sm-4" id="loaduserGroupdropdown">
    </div>
    <div class="form-group col-sm-4" id="">
        <div class="">
            <button type="button" style="margin-top: 28px;" class="btn btn-primary btn-xs pull-left"
                    onclick="loadform()"> <?php echo $this->lang->line('common_search');?><!--Search-->
            </button>
        </div>
    </div>
</div>

<div class="row">
    <button type="button" style="margin-right: 15px"  class="btn btn-primary btn-sm pull-right"
            onclick="modal_employees_add()"> <?php echo $this->lang->line('config_add_employees');?><!--Add Employees-->
    </button>
</div>

<hr style="margin-top: 10px">
<div class="row">
    <div class="col-sm-12" id="div_reload">
        <table id="table_sub_group_employees" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 20px">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('config_emp_id');?><!--EmpID--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_company');?><!--Company--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('config_common_sub_group');?><!--Sub Group--></th>
                <th style="min-width: 20%"></th>
            </tr>
            </thead>
        </table>

    </div>

</div>

<div class="modal fade" id="subGroupEmployeeModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_navigation_access');?><!--Navigation Access--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo form_open('', 'role="form" id="save_employee_access"'); ?>

                        <div class="form-group "><label style="width: 100px"
                                                       for=""><?php echo $this->lang->line('common_group');?><!--Group--> </label>
                            <?php echo form_dropdown('companyempGroupID', $main_grp, '', 'class="form-control select2" style="" onchange="loaddropdown(),load_sub_group_employees()" id="companyempGroupID"'); ?>

                        </div>

                        <div class="form-group " id="subgroup_dropdown"></div>
                        <div class="form-group " id="loaddropdown"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_employees()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/companyConfiguration/employee_sub_group_management', '', 'Employees Sub Groups');
        });


        var Otable;

        /*   $('#empID').multiselect2({
         enableFiltering: true,
         filterBehavior: 'value',
         includeSelectAllOption: true
         });*/
        table_sub_group_employees();
        load_sub_group()


    });

    function searchfilter() {
        loadform();
    }

    function load_sub_group() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupID: $('#companyGroupID').val(),All:'true'},
            url: "<?php echo site_url('Group_management/load_sub_group'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loaduserGroupdropdown').html(data);
                loadform();

            }, error: function () {

            }
        });
    }

    function save_employees() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $('#save_employee_access').serializeArray(),
            url: "<?php echo site_url('Group_management/save_assigned_sub_group_employees'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();

                if (data['status']) {
                    /*       loaddropdown();
                     userGroupDropdown();*/
                    $('#empID').multiselect2('deselectAll', true);
                    $('#empID').multiselect2('refresh');

                    Otable.ajax.reload();

                    $('#subGroupEmployeeModal').modal('hide');
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function loadform() {

        Otable.ajax.reload();
    }

    function loaddropdown() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyempGroupID: $('#companyempGroupID').val()},
            url: "<?php echo site_url('Group_management/load_dropdown_unassigned_employees'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loaddropdown').html(data);
                loadform();

            }, error: function () {

            }
        });

    }


    function table_sub_group_employees() {
        window.Otable = $('#table_sub_group_employees').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "recordsFiltered": 10,
            "sAjaxSource": "<?php echo site_url('Group_management/fetch_sub_group_employees'); ?>",
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
                {"mData": "subGroupEmpID"},
                {"mData": "ECode"},
                {"mData": "Ename2"},
                {"mData": "company_name"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,5]}],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "subGroupID", "value": $('#subGroupID').val()});
                //aoData.push({"name": "companyID", "value": $('#gcompanyID').val()});
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
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


    function saveNavigationgroupSetup() {
        var navigationID = [];
        $('.nVal:checked').each(function (i, e) {
            navigationID.push(e.value);
        });
        navigationID = navigationID.join(',');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {navigationID: navigationID, userGroupID: $('#userGroupID').val()},
            url: "<?php echo site_url('Access_menu/saveNavigationgroupSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function deleteemployee(subGroupEmpID){

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_common_you_want_to_delete_this_record');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'subGroupEmpID':subGroupEmpID},
                    url :"<?php echo site_url('Group_management/deleteemployeeSubgroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){

                        stopLoad();
                        myAlert(data[0],data[1]);

                        Otable.ajax.reload();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }



    function modal_employees_add() {
        loaddropdown();
        load_sub_group_employees();
        $('#subGroupEmployeeModal').modal({backdrop: "static"});
    }

    function load_sub_group_employees() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupID: $('#companyempGroupID').val()},
            url: "<?php echo site_url('Group_management/load_sub_group_employee'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#subgroup_dropdown').html(data);
                loadform();

            }, error: function () {

            }
        });

    }




</script>