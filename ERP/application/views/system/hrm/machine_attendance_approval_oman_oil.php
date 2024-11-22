<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_monthly_attendance_summary');
echo head_page($title, false);
$current_period = date('Y-m');
$floors_arr = floors_drop();
$filter_arr = ['0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')];
?>
<style>
    .control-label {
        margin-top: 5px;
        padding:0px;
    }
</style>


<div class="row" style="">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_approved');?>
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td>
                    <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_partially_approved');?><!--Partially Approved-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', $filter_arr, '', 'class="form-control" id="approvedYN" required onchange="filterAttendanceTable()"'); ?>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-xs pull-right" style="margin-top: 5px;" onclick="create_newSummary()">
            <i class="fa fa-plus"></i> New summary
        </button>
    </div>
</div>
<hr>

<div class="table-responsive" style="">
    <table id="attendanceMasterTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 20px">#</th>
            <th><?php echo $this->lang->line('hrms_payroll_attendance_date');?></th>
            <th style="width: 80px;text-align: center"><?php echo $this->lang->line('common_status');?></th>
            <th style="width: 70px"></th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="modal-newSummary" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Attendance Summary</h4>
            </div>
            <?php echo form_open('','role="form" class="" id="newSummary_form" autocomplete="off"' ); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="att_period" style="text-align: right">Period</label>
                        <div class="col-md-7">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type='text' class="form-control" id="att_period" name="att_period" value="<?= $current_period; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="saveBtn"><?=$this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?=$this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-summary-view" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="width: 98%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Attendance Summary</h4>
            </div>
            <?php echo form_open('','role="form" class="" id="summaryView_form" autocomplete="off"' ); ?>
            <div class="modal-body" id="attendanceReview-div">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="approve-btn" onclick="approve_att()"><?=$this->lang->line('common_approve');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?=$this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    let attMasterTB;

    $('#att_period').datepicker({
        format: 'yyyy-mm',
        viewMode: "months",
        minViewMode: "months"
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
    });

    let summary_frm = $('#newSummary_form');

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/hrm/machine_attendance_approval_oman_oil','','HRMS');
        });

        summary_frm.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
            }
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();

            var postData = summary_frm.serializeArray();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '<?php echo site_url('Employee/create_attendance_summary') ?>',
                data: postData,
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#modal-newSummary').modal('hide');
                        setTimeout(function(){
                            load_attendance_review( data['id'] );
                        }, 300);
                    }

                    attMasterTB.ajax.reload();

                    $('#saveBtn').prop('disabled', false);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#saveBtn').prop('disabled', false);
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        });

        load_attendanceTB();
    });

    function load_attendanceTB(selectedRowID=null) {
        selectedRowID = (selectedRowID == null) ? '<?php echo $this->input->post('page_id'); ?>' : selectedRowID;

        attMasterTB = $('#attendanceMasterTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_attendance_summary'); ?>",
            "aaSorting": [[1, 'desc']],
            "columnDefs": [
                { "orderable": false , "targets": [2,3]}
            ],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if (parseInt(oSettings.aoData[x]._aData['id']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "attendancePeriod"},
                {"mData": "conf_status"},
                {"mData": "edit"}
            ],
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

    function create_newSummary(){
        $('#saveBtn').prop('disabled', false);
        $('#modal-newSummary').modal('show');
    }

    function load_attendance_review(id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'id': id},
            url: '<?php echo site_url('Employee/load_attendance_approval_view'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='e'){
                    myAlert(data[0], data[1]);
                }else {
                    $('#modal-summary-view').modal('show');
                    $('#attendanceReview-div').html(data['view']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_attendance_review(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record !*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/delete_attendance_review'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0]=='s'){
                            attMasterTB.ajax.reload();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
</script>
