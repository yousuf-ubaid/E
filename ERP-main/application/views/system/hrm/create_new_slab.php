
<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_slab_master');
echo head_page($title, false);




$currency_arr = all_currency_new_drop();
//$current_date = format_date($this->common_data['current_date']);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<form class="form-horizontal" id="slab_master_form">
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></label>
        </div>
        <div class="form-group col-sm-4">
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <!--<input type="text" name="documentDate" value="<?php /*echo $current_date; */?>"
                       id="documentDate"
                       class="form-control" readonly>-->
                <input type='text' class="form-control" id="documentDate" name="documentDate" value="" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
            </div>
        </div>
    </div>
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
        </div>
        <div class="form-group col-sm-4">
            <?php echo form_dropdown('MasterCurrency', $currency_arr, '', 'class="form-control select2" id="MasterCurrency" required'); ?>
        </div>
    </div>
    <div class="row" style="margin-left: 2px">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
        </div>
        <div class="form-group col-sm-8">
                            <textarea class="form-control" id="description" name="description"
                                      rows="2"></textarea>
        </div>
    </div>
    <div class="text-right m-t-xs">
        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
    </div>
</form>
<div id="slab_detail"></div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="slabModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="slab_detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_slab_detail');?><!--Slab Detail--></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_payroll_start_range_amount');?><!--Start Range Amount--></label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="start_amount" id="start_amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_payroll_end_range_amount');?><!--End Range Amount--></label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="end_amount" id="end_amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-4 control-label"><?php echo $this->lang->line('common_percentage');?><!--Percentage--></label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="percentage" id="percentage"
                                   value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_payroll_threshold_amount');?><!--Threshold Amount--></label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="threshold_amount"
                                   id="threshold_amount">
                            <input type="hidden" name="slabMasterID" id="slabMasterID">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/hrm/slab_master', p_id, 'Slab Master');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            LoadSlabDetail(p_id);
        }
        $('.select2').select2();

        /*$('#documentDate').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#slab_master_form').bootstrapValidator('revalidateField', 'documentDate');
            $(this).datepicker('hide');
        });*/
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#slab_master_form').bootstrapValidator('revalidateField', 'documentDate');
        });

        $('#slab_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                MasterCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#MasterCurrency option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_pay_slabs_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        LoadSlabDetail(data['last_id']);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#slab_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                start_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_start_range_amount_is_required');?>.'}}},/*Start Range Amount is required*/
                end_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_end_range_amount_is_required');?>.'}}},/*End Range Amount is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_percentage_is_required');?>.'}}},/*Percentage is required*/
                threshold_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_threshold_amount_is_required');?>.'}}}/*Threshold Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_pay_slabs_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        LoadSlabDetail(data[2]);
                        $("#slabModal").modal('hide');
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



    });

    $(document).on('keypress', '.number',function (event) {
        var amount = $(this).val();
        if(amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }

    });

    function LoadSlabDetail(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/Load_pay_slab_master_detail') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#slab_master_form').hide();
                $("#slab_detail").html(data);
                $("#slabMasterID").val(id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

    function save_salaryDeclarationDetail() {
        LoadSlabStartAmount();
        $('#slab_detail_form').bootstrapValidator('resetForm', true);
        $("#slabModal").modal({backdrop: "static"});
    }

    function delete_item(detailID, masterID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID},
                    url: "<?php echo site_url('Employee/delete_payee_slab_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        LoadSlabDetail(masterID);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function LoadSlabStartAmount(id) {
        var masterID = $("#slabMasterID").val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/Load_slab_start_amount') ?>",
            data: {masterID: masterID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data != null ){
                    if(data['rangeStartAmount'] != ''){
                        var endAmount = parseInt(data['rangeEndAmount']) + 1;
                        $("#start_amount").val(endAmount);
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

</script>
