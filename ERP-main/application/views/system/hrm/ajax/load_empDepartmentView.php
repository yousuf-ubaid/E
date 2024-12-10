<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if($isInitialLoad == 'Y'){ ?>
    <div id="department-container" class="col-sm-5">
<?php } ?>


<style type="text/css">
    .items{ margin:0px !important; }
</style>


    <fieldset>
        <legend><?php echo $this->lang->line('emp_attendance_department');?><!--Department--></legend>
        <div class="row" style="">
            <div class="col-sm-6 col-xs-6" style="margin-left: 15px;">
                <table class="table table-bordered table-striped table-condensed ">
                    <tbody>
                    <tr>
                        <td>
                            <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('emp_active');?><!--Active-->
                        </td>
                        <td>
                            <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('emp_in_active');?><!--In-Active-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4 col-xs-5 pull-right" style="margin-right: 15px;">
                <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="openEmpDepartment_modal()"><i
                        class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add');?><!--Add-->
                </button>
            </div>
        </div>

         <div class="table-responsive" style="margin-top: 1%;">
            <table id="load_empDepartments" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="width: auto"><?php echo $this->lang->line('emp_attendance_department');?><!--Department--></th>
                    <th style="width: 60px"><?php echo $this->lang->line('emp_is_primary');?></th>
                    <th style="width: 60px"><?php echo $this->lang->line('emp_status');?><!--Status--></th>
                    <th style="width: 60px"></th>
                </tr>
                </thead>
            </table>
         </div>
    </fieldset>



<div class="modal fade" id="new_empDepartment_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('emp_attendance_department');?><!--Add Departments--></h4>
            </div>
            <form class="form-horizontal" id="add-empDepartment_form">
                <div class="modal-body">

                    <table class="table table-bordered" id="designations-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('emp_attendance_department');?><!--Departments--></th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($moreDepartment as $key => $item) {
                            echo '<tr>
                                    <td>' . $item['DepartmentDes'] . '</td>
                                    <td align="center">
                                        <input type="checkbox" name="items[]" class="items" value="' . $item['DepartmentMasterID'] . '" />
                                    </td>
                                 </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="empID" value="<?php echo $empID ?>">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_empDepartments()"><?php echo $this->lang->line('emp_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    var religion_tb = $('#designations-tb');
    var editModal = $('#editModal');
    var empID = '<?php echo $empID; ?>';

    $(document).ready(function () {
        load_empDepartments();
    });


    function load_empDepartments() {
        $('#load_empDepartments').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_empDepartments'); ?>",
            "aaSorting": [[1, 'asc']],
           "columnDefs": [
               {"orderable": false, "targets": [0,2,3,4]}
           ],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".switch-chk").bootstrapSwitch();
                if(fromHiarachy==1){
                    Otable.column( 3 ).visible( false );
                    $(".switch-chk").bootstrapSwitch("disabled",true);
                }
            },
            "aoColumns": [
                {"mData": "DepartmentMasterID"},
                {"mData": "DepartmentDes"},
                {"mData": "primary_str"},
                {"mData": "status"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'empID', 'value': empID});
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

    function openEmpDepartment_modal() {
        $('.saveInputs').val('');
        $('#new_empDepartment_model').modal({backdrop: "static"});
    }

    function save_empDepartments() {
        var selectedCount = 0;
        $('.items').each(function () {
            if ( $(this).prop('checked') == true ){
                selectedCount++;
                return false;
            }
        });

        if (selectedCount > 0) {
            var postData = $('#add-empDepartment_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_empDepartments'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () { startLoad(); },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#new_empDepartment_model').modal('hide');

                        setTimeout(function(){ fetch_departments(); }, 400);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else {
            myAlert('e', '<?php echo $this->lang->line('common_please_select_at_least_one_designation');?>');/*Please select at least one designation*/
        }
    }

    function delete_empDepartments(id, description) {
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
                    async: true,
                    url: "<?php echo site_url('Employee/delete_empDepartments'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'hidden-id': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function(){ fetch_departments(); }, 400);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_error');?>');/*error*/
                    }
                });
            }
        );
    }

    function primaryDepartment(obj, id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_make_this_primary');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Employee/make_primary_department'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'hidden-id': id, 'empID': empID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] != 's') {
                                    var thisChk = $('#depPr_' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);
                                }
                                else{
                                    load_empDepartments();
                                    }
                            
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
                else {
                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                    $('#depPr_' + id).prop('checked', changeStatus).change();
                }
            }
        );
    }

    function changeStatus(id){
        var status = ( $('#status_'+id).is(":checked") )? 1 : 0;
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/statusChangeEmpDepartments'); ?>',
            data: {'hidden-id': id, 'status':status},
            dataType: 'json',
            beforeSend: function () { startLoad(); },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 'e') {
                    setTimeout(function(){ load_empDepartments(); }, 400);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }
    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
        $('.form-control:not([type="search"], #parentCompanyID)').attr('disabled', true);
        //$('#parentCompanyID').attr('disabled', false);
    }
</script>

<?php
if($isInitialLoad == 'Y'){
    echo '</div>';
    echo '<div>'; // Close of <div class="row"> in load_empDesignationView.php
}
?>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-30
 * Time: 12:11 PM
 */
