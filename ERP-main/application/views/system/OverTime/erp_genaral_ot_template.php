<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->lang->line('hrms_attendance_summary'), false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row" >
    <div class="col-md-7">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>   <?php echo $this->lang->line('common_confirmed').' / '.$this->lang->line('common_approved');?></td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?></td>
                <td><span class="label label-warning">&nbsp;</span>   <?php echo $this->lang->line('common_refer_back');?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-sm-3">
        <button type="button" class="btn btn-primary pull-right" style="margin-top: 23px;" onclick="openOtMasterModel()"><i
            class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_leave_management_createattendancesummary') ?> <!--Create Attendance Summary-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">

    <table id="general_ot_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width:7%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="OtMasterModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'class="form-group" role="form" id="ot_master_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_attendance_summary') ?><!--Attendance Summary--></h4>
            </div>
            <div class="modal-body" style="margin-left: 20px">
                <div class="row">
                    <div class="form-group col-sm-8">
                    <label for=""><?php echo $this->lang->line('common_date') ?><!--Date--> <?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="documentDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-8">
                        <label for="currency"><?php echo $this->lang->line('common_currency') ?> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('currencyID', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="currencyID"  required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group ">
                            <label for="description"><?php echo $this->lang->line('common_description') ?> <?php required_mark(); ?></label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save') ?><!--Save-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="OtMasterViewModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_attendance_summary');?> </h4><!--Attendance Summary-->
            </div>
            <div class="modal-body">
                <div class="row" style="padding: 1%;" id="GOTview">

                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/OverTime/erp_genaral_ot_template', 'Test', 'Attendance Summary');
        });
        general_ot_master_table();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#ot_master_form').bootstrapValidator('revalidateField', 'expenseClaimDate');
        });

        $('#ot_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: 'Date is required.'}}},
                currencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            //data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OverTime/save_ot_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $("#OtMasterModal").modal('hide');
                        general_ot_master_table();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    // myAlert(data[0], data[1]);
                }
            });
        });

    });

    function general_ot_master_table(selectedID=null) {
        Otable = $('#general_ot_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('OverTime/fetch_general_ot_master_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['generalOTMasterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "generalOTMasterID"},
                {"mData": "otCode"},
                {"mData": "description"},
                {"mData": "documentDate"},
                {"mData": "currency"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}

            ],
            "columnDefs": [{"targets": [5], "orderable": false}, {"searchable": false, "targets": [0,5,6]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    function openOtMasterModel(){
        $('#ot_master_form')[0].reset();
        $("#OtMasterModal").modal({backdrop: "static"});
    }

    function over_time_templates() {
        var groupSegmentID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSegmentID: groupSegmentID, All: 'true'},
            url: "<?php echo site_url('OverTime/fetch_over_time_templates'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#over_time_template_table').html(data);
                //$('.select2').select2();
            }, error: function () {

            }
        });
    }

    function referback_general_ot(generalOTMasterID){
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'generalOTMasterID': generalOTMasterID},
                    url: "<?php echo site_url('OverTime/referback_general_ot'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            general_ot_master_table();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function general_ot_view_model(generalOTMasterID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {generalOTMasterID: generalOTMasterID, All: 'true'},
            url: "<?php echo site_url('OverTime/fetch_over_time_template_view'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#GOTview').html(data);
                $("#OtMasterViewModal").modal({backdrop: "static"});
            }, error: function () {

            }
        });
    }

    function delete_general_ot_template(generalOTMasterID){
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'generalOTMasterID': generalOTMasterID},
                    url: "<?php echo site_url('OverTime/delete_general_ot_template'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            general_ot_master_table();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>