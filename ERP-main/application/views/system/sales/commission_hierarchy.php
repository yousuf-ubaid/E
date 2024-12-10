<?php

$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);

$designation_arr = array('' => 'Select Designation');
$reportingDesignation_arr = array('' => 'Select Reporting Designation');
$employee_arr=load_employee_drop(true);
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <?php echo form_open('', 'role="form" id="commission_hierarchy_filter"'); ?>
            <div class="form-group col-sm-8">
            </div>
            <div class="form-group col-sm-4">
            </div>
        </form>
    </div>  
</div>
<div class="row">
    <div class="col-md-5">
    </div>
   <div class="col-md-7 text-right">
   <button type="button" class="btn btn-primary pull-right" onclick="openAddCommissionHierachyModel()"><i class="fa fa-plus"></i> Generate Commission Hierarchy </button><!--Generate Commission Hierachy-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="commission_hierarchy_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee');?></th> <!--Employee-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_designation');?></th><!--Designation-->
            <th style="min-width: 20%">Reporting Employee</th>
            <th style="min-width: 20%">Reporting Designation</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<div aria-hidden="true" role="dialog"  id="add_commission_hierarchy_modal" class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="chHead"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="add_commission_hierarchy_form"'); ?>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="commissionHierarchyID" name="commissionHierarchyID">
                        <div class="form-group col-md-6">
                            <label for="employeeID"><?php echo $this->lang->line('common_employee');?><span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label><!--Employee-->
                            <?php echo form_dropdown('employeeID', $employee_arr, "Select Employee", 'class="form-control select2" id="employeeID" onchange="loadDesignation(this.value)" required'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designationID"><?php echo $this->lang->line('common_designation');?><span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label><!--Designation-->
                            <?php echo form_dropdown('designationID', $designation_arr, "Select Designation", 'class="form-control select2" id="designationID"  required'); ?>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="form-group col-md-6">
                            <label for="reportingEmployeeID">Reporting Employee </label>
                            <?php echo form_dropdown('reportingEmployeeID', $employee_arr, "Select Reporting Employee ", 'class="form-control select2" id="reportingEmployeeID" onchange="loadReportingDesignation(this.value)" required'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="reportingDesignationID">Reporting Designation</label>
                            <?php echo form_dropdown('reportingDesignationID', $reportingDesignation_arr, "Select Reporting Designation ", 'class="form-control select2" id="reportingDesignationID"  required'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary " onclick="saveCommissionHierarchy()"><?php echo $this->lang->line('common_save');?>
                    </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var commissionHierarchyID;

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/sales/commission_hierarchy','','Commission Hierarchy');
        });
        commissionHierarchyID = null;
        $('.select2').select2();
        commission_hierarchy_table();
    });

    function commission_hierarchy_table(selectedID=null) {
         Otable = $('#commission_hierarchy_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('CommissionScheme/fetch_commission_hierarchy'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if( parseInt(oSettings.aoData[x]._aData['salesCommisionID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "commissionHierarchyID"},
                {"mData": "employee"},
                {"mData": "DesDescription"},
                {"mData": "reportingEmployee"},
                {"mData": "reportingDesDescription"},
                {"mData": "edit"},
                {"mData": "Ename2"},
                {"mData": "EmpSecondaryCode"},
                {"mData": "repEname2"},
                {"mData": "repEmpSecondaryCode"},
            ],
             "columnDefs": [{"targets": [5], "orderable": false},{"targets": [0,1,3], "visible": true,"searchable": false},
             {"targets": [6,7,8,9], "visible": false, "searchable": true}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "statusFilter", "value": $("#statusFilter").val()});
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


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
    
    function openAddCommissionHierachyModel(){
        commissionHierarchyID = null;
        $('#commissionHierarchyID').val('')
        $('#employeeID').val('').change();
        $('#reportingEmployeeID').val('').change();
        $('#designationID').val('').change();
        $('#reportingDesignationID').val('').change();
        $('#chHead').html('Add New Commission Hierarchy');
        $('#add_commission_hierarchy_form')[0].reset();
        $('#add_commission_hierarchy_modal').modal('show');
    }

    function loadDesignation(employee) {
        if(employee) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'commissionHierarchyID':commissionHierarchyID, 'employeeID': employee},
                url: "<?php echo site_url('CommissionScheme/fetchEmployeeRelatedDesignation'); ?>",
                success: function (data) {
                    $('#designationID').empty();
                    var mySelect = $('#designationID');
                    mySelect.append($('<option></option>').val('').html('Select  Designation'));

                    var select_value='';
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['DesignationID']).html(text['DesDescription']));
                            if (text['isMajor']==2) {
                                select_value = text['DesignationID'];
                            }else if(text['isMajor']==1){
                                select_value = text['DesignationID'];
                            }
                        });
                         }
                    $("#designationID").val(select_value);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function loadReportingDesignation(employee) {
        if(employee) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'commissionHierarchyID':commissionHierarchyID, 'employeeID': employee},
                url: "<?php echo site_url('CommissionScheme/fetchReportingEmployeeRelatedDesignation'); ?>",
                success: function (data) {
                     $('#reportingDesignationID').empty();
                     var mySelect = $('#reportingDesignationID');
                     mySelect.append($('<option></option>').val('').html('Select  Reporting Designation'));

                     var select_value='';
                     if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['DesignationID']).html(text['DesDescription']));
                            if (text['isMajor']==2) {
                                select_value = text['DesignationID'];
                            }else if(text['isMajor']==1){
                                select_value = text['DesignationID'];
                            }
                        });
                     }
                    $("#reportingDesignationID").val(select_value);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function saveCommissionHierarchy(){
        var data = $("#add_commission_hierarchy_form").serializeArray();
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data: data,
            url :"<?php echo site_url('CommissionScheme/saveCommissionHierarchy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    Otable.draw();
                    $('#add_commission_hierarchy_modal').modal('hide');
                    $('#add_commission_hierarchy_form')[0].reset();
                    $('#commissionHierarchyID').val('');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function open_edit_commission_hierarchy_model(id){
        commissionHierarchyID = id;
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'commissionHierarchyID':id},
            url :"<?php echo site_url('CommissionScheme/getCommissionHierarchy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data) {
                    $('#employeeID').val(data['employeeID']).change();
                    $('#reportingEmployeeID').val(data['reportingEmployeeID']).change();
                    $('#commissionHierarchyID').val(id);
                    $('#chHead').html('Edit Commission Hierarchy');/*Edit Category*/
                    $('#add_commission_hierarchy_modal').modal('show');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function delete_commission_hierarchy(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>"/*Delete*/
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'commissionHierarchyID':id},
                    url :"<?php echo site_url('CommissionScheme/delete_commission_hierarchy'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>