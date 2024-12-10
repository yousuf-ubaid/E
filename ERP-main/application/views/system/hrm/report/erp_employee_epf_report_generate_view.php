<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$epfMasterID = $this->input->post('epfMasterID');
$isConfirmed = $master['confirmedYN'];
$disabled = ( $isConfirmed == 1)? 'disabled' : '';

$segment_arr = fetch_segment(true, false);

$payrollYear = $master['payrollYear'];
$payrollMonth = $master['payrollMonth'];
?>
<style>
    .trInputs{
        width: 100%;
        padding: 2px 5px;
        height: 22px;
        font-size: 12px;
    }

    .tbTrash{
        color: rgb(209, 91, 71);
        display: block
    }

    .select-container .btn-group{
        width: 150px !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>


<?php echo form_open('', ' id="frm_rpt" role="form"'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-sm-3">
                <label for="payrollMasterID"><?php echo $this->lang->line('common_month')?><!--Month--></label>
                <div style="">

                    <?php
                    echo '<input type="hidden" name="epfMasterID" value="'.$epfMasterID.'" />';
                    echo form_dropdown('payrollMasterID', payrollMonth_dropDown(), $master['payrollPeriod'], 'class="form-control select2" id="payrollMasterID" disabled');
                    ?>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label for="submissionID"><?php echo $this->lang->line('hrms_reports_submissionid')?><!--Submission ID--></label>
                <div style="">
                    <input name="submissionID" id="submissionID" type="number" class="form-control"
                           value="<?php echo $master['submissionID']; ?>" <?php echo $disabled; ?>/>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label for="comment"><?php echo $this->lang->line('common_comment')?><!--Comment--></label>
                <div style="">
                    <input name="comment" id="comment" type="text" class="form-control"
                           value="<?php echo $master['comment']; ?>" <?php echo $disabled; ?>/>
                </div>
            </div>

            <div class="form-group col-sm-5">
                <?php if($isConfirmed != 1){ ?>
                <div class="hidden-sm hidden-xs" style="margin-top: 26px"></div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openEmployeeModal()">
                    <i class="fa fa-fw fa-user"></i> <?php echo $this->lang->line('common_add_employee')?><!--Add Employee-->
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="save_report()"> <?php echo $this->lang->line('common_save_as_draft')?><!--Save as draft--></button>
                <button type="button" class="btn btn-success btn-sm" onclick="save_report(1)"><?php echo $this->lang->line('common_save_and_confirm')?> <!--Save & confirm--></button>
                <?php }
                else{
                echo '<div class="hidden-sm hidden-xs" style="margin-top: 23px"></div>';
                }?>
                <button type="button" class="btn btn-default btn-sm" onclick="get_detFile(<?php echo $epfMasterID; ?>)">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    <div style="margin-top: 1%">&nbsp;</div>

    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
                <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                    <li class="active">
                        <a href="#configTab" id="" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('hrms_reports_config')?><!--Config--></a>
                    </li>
                    <li class="">
                        <a href="#viewTab" id="" data-toggle="tab" aria-expanded="false" onclick="previewData(<?=$epfMasterID;?>, 'V')">View</a>
                    </li>
                    <li class="">
                        <a href="#previewTab" id="" data-toggle="tab" aria-expanded="false" onclick="previewData(<?=$epfMasterID;?>, 'P')"><?php echo $this->lang->line('hrms_reports_preview')?><!--Preview--></a>
                    </li>
                </ul>
                <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                    <div class="tab-pane active disabled" id="configTab">
                        <div class="table-responsive">
                            <div class="fixHeader_Div" style="max-width: 100%; height: 400px">
                                <table id="empTB" class="<?php echo table_class(); ?>" style="margin-top: -2px">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('hrms_reports_empcode')?><!--EMP Code--></th>
                                        <th style="width:50%"><?php echo $this->lang->line('common_employee_name')?><!--Employee Name--></th>
                                        <th style="width:30%"><?php echo $this->lang->line('hrms_reports_occupationclassificationgrade')?><!--Occupation classification Grade--></th>
                                        <th style="width:5%; z-index:100">
                                            <?php
                                            if($isConfirmed != 1){
                                                echo '<span class="glyphicon glyphicon-trash tbTrash"  onclick="remove_allRow()"></span>';
                                            }
                                            ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if(!empty($employees)){

                                        foreach($employees as $key=>$emp){
                                            $empID = $emp['empID'];
                                            $id = $emp['id'];
                                            $removeStr = ($isConfirmed != 1)? '<span class="glyphicon glyphicon-trash tbTrash"  onclick="remove_Row(this, '.$id.')"></span>' : '';

                                            echo '<tr>
                                 <td>'.($key+1).'</td>
                                 <td>'.$emp['ECode'].'</td>
                                 <td>'.$emp['empName'].'</td>
                                 <td>
                                    <input type="text" name="ocGrade[]" value="'.$emp['ocGrade'].'" class="form-control trInputs"/>
                                    <input type="hidden" name="empID[]" value="'.$empID.'" '.$disabled.'/>
                                 </td>
                                 <td align="right">'.$removeStr.'</td>
                              </tr>';
                                        }
                                    }
                                    else{
                                        echo '<tr><td colspan="5">'.$this->lang->line('hrms_reports_norecords') .'<!--No records--></td></tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="viewTab">
                        <div id="preview" class="preview_container" style="display: none; height: 450px"></div>
                    </div>

                    <div class="tab-pane" id="previewTab">
                        <pre id="preview_preTag" class="preview_container" style="display: none;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>


<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999"  >
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Employees</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="form-group col-sm-4 col-xs-4 select-container">
                                <label for="segment">Segment</label>
                                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID"  multiple="multiple"'); ?>
                            </div>
                            <div class="form-group col-sm-4 col-xs-4 pull-right">
                                <label for="currency" class="visible-sm visible-xs">&nbsp;</label>
                                <button class="btn btn-primary btn-sm pull-right" id="selectAllBtn" style="font-size:12px;" onclick="selectAllRows()"> Select All </button>
                                <button type="button" onclick="load_employeeForModal()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px">Load</button>
                            </div>
                        </div>
                        <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 25%">EMP Code</th>
                                        <th style="width:auto">Employee Name</th>
                                        <th style="width:auto">Designation</th>
                                        <th style="width:auto">Segment</th>
                                        <th style="width: 5%"><div id="dataTableBtn"></div></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="table-responsive col-md-12" >
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;" onclick="addAllRows()"> Add All </button>
                                    <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;" onclick="clearAllRows()"> Clear All </button>
                                </div>
                                <hr style="margin-top: 7%">
                                <form id="tempTB_form">
                                    <input type="hidden" name="masterID" value="<?php echo $epfMasterID; ?>"/>
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%">EMP CODE</th>
                                            <th style="max-width: 95%">EMP NAME</th>
                                            <th><div id="removeBtnDiv"> </div></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var empTempory_arr = [];
    var tempTB = $('#tempTB').DataTable({ "bPaginate": false });
    var epfMasterID = "<?php echo $epfMasterID; ?>";

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function () {
        $('.select2').select2();

        $('#empTB').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 0
        });
    });

    function remove_Row(obj, id){
        swal({
                title: "Are you sure ?",
                text: "You want to delete this ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'epfMasterID' : epfMasterID, 'id' : id },
                    url: "<?php echo site_url('Report/delete_epfReportEmp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            $(obj).closest('tr').remove();

                            var j = 0;
                            $('#empTB tr').each(function(){
                                $(this).find('td:eq(0)').text(j);
                                j++;
                            });

                            if( j == 1 ){
                                remove_allRow();
                            }
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','An Error Occurred! Please Try Again.');
                    }
                });
            }
        );

    }

    function remove_allRow(){
        swal({
                title: "Are you sure ?",
                text: "You want to delete all records ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'epfMasterID' : epfMasterID},
                    url: "<?php echo site_url('Report/delete_epfReportAllEmp'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            $('#empTB tbody').html('<tr><td colspan="5">No records</td></tr>');
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
    }

    function save_report(isConfirm=0){
        var postData = $('#frm_rpt').serializeArray();
        postData.push({'name':'isConfirmed', 'value':isConfirm});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Report/save_reportDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                myAlert(data[0], data[1]);

                if( data[0] == 's'){

                    setTimeout(function(){
                        if(isConfirm==1){
                            get_reportData_view();
                        }
                    },300);

                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function previewData(id, rqt_type){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Report/epf_reportGenerate'); ?>/"+id+'/'+rqt_type,
            beforeSend: function () {
                $('.preview_container').html();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(rqt_type == 'V'){
                    $('#preview').html(data).css('display', 'block');
                    $('#rpt_table').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 10
                    });
                }
                else{
                    $('#preview_preTag').html(data).css('display', 'block');
                }

            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    /** Employee add functions **/
    function openEmployeeModal(){
        $('#employee_model').modal('show');
        load_employeeForModal();
    }

    function load_employeeForModal(){
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Report/getEmployeesDataTable'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "DesDescription"},
                {"mData": "segCode"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'epfMasterID', 'value':epfMasterID});
                aoData.push({"name": "payrollYear", "value": '<?php echo $payrollYear; ?>'});
                aoData.push({"name": "payrollMonth", "value": '<?php echo $payrollMonth; ?>'});
                aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});

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

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="empHiddenID[]"  class="modal_empID" value="'+empID+'">';
            empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="'+details.last_ocGrade+'">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0:  details.ECode,
                1:  details.empName,
                2:  empDet,
                3:  empID
            }]).draw();

            empTempory_arr.push(empID);
        }

    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="empHiddenID[]" class="modal_empID" value="' + empID + '">';
                empDet1 += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + data.last_ocGrade + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTempory_arr.push(empID);
            }
        } );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function(data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function addAllRows(){

        var postData = $('#tempTB_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Report/save_empEmployeeAsTemporary'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }else{
                    $('#employee_model').modal('hide');
                    clearAllRows();

                    setTimeout(function(){
                        get_reportData_view();
                    },300);

                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }
</script>

<?php
