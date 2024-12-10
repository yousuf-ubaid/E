<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_insurance_types');
echo head_page($title, false);

/*echo head_page('Customer Category', false);*/ ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row table-responsive">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="openinsurancemodal()"><i class="fa fa-plus"></i><?php echo $this->lang->line('common_create'); ?> <!--Create-->
</div>
<hr>
    <div id="insurancebody"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog"  id="insurancetypemodal" class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="insurancetypeheader"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="insurance_type_form"'); ?>
                <div class="modal-body">
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_insurance_type'); ?> <!--Insurance Type--> <?php required_mark(); ?></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <input type="text" class="form-control " id="insurancetype" name="insurancetype" autocomplete="off">
                            <input type="hidden" class="form-control " id="insurancetypeId" name="insurancetypeId">
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('common_gl_code'); ?> <!--GL Code--> <?php required_mark(); ?></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('gl_code', dropdown_all_revenue_gl('PLE'), '', 'class="form-control select2" id="gl_code"'); ?>
                        </div>
                    </div>

                    <div class="row hide" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_margin_percentage'); ?> <!--Margin Percentage--><?php required_mark(); ?></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <input type="number" step="any"  class="form-control " id="marginPercentage" name="marginPercentage" autocomplete="off">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?>
                        </button><!--Save-->
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    </div>
            </form>
        </div>
    </div>
</div>
</div>




<div aria-hidden="true" role="dialog"  id="subinsurancetypemodal" class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="subinsurancetypeheader"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="sub_insurance_type_form"'); ?>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_insurance_sub_type'); ?> <!--Insurance Sub Type--><?php required_mark(); ?></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control " id="insuranceTypeSub" name="insuranceType" autocomplete="off">
                        <input type="hidden" class="form-control " id="insuranceTypeIDSub" name="insuranceTypeID">
                        <input type="hidden" class="form-control " id="masterTypeID" name="masterTypeID">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_no_of_months'); ?> <!--No Of Months--> <?php required_mark(); ?></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control " id="noofMonths" name="noofMonths" autocomplete="off">
                    </div>
                </div>
                <div class="row " style="margin-top: 10px;">
                    <div class="form-group col-sm-4 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('sales_maraketing_masters_margin_percentage'); ?> <!--Margin Percentage--><?php required_mark(); ?></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="number" step="any"  class="form-control " id="marginPercentageSub" name="marginPercentage" autocomplete="off">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?>
                    </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        insurancetypetable_load();
        $('.select2').select2();
    });
    $('#extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
        $('.columnSelected').on('ifChecked', function(event){
            var type=$('#type').val();
            if(type==1){
                $('#glCodehide').removeClass('hidden');
            }
        });
        $('.columnSelected').on('ifUnchecked', function(event){
            var type=$('#type').val();
            if(type==1){
                $('#glCodehide').addClass('hidden');
            }
        });



    function insurancetypetable() {
        var Otable = $('#insurance_type_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Invoices/fetchinsurancetype'); ?>",
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
                {"mData": "insuranceTypeID"},
                {"mData": "insuranceType"},
                {"mData": "mastertype"},
                {"mData": "marginPercentage"},
                {"mData": "noofMonths"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    $('#insurance_type_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            insurancetype: {validators: {notEmpty: {message: 'Insurance Type is required.'}}}
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
            url: "<?php echo site_url('Invoices/saveinsurancetype'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    insurancetypetable_load();
                    $('#insurancetypemodal').modal('hide');
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function delete_insurancetype(insuranceTypeID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_delete_this_category');?>",/*You want to delete this Category!*/
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
                    data : {'insuranceTypeID':insuranceTypeID},
                    url :"<?php echo site_url('Invoices/deleteinsurancetype'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            insurancetypetable_load();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function openinsuranceeditmodel(insuranceTypeID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'insuranceTypeID':insuranceTypeID},
            url :"<?php echo site_url('Invoices/getinsurancetype'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data) {
                   $('#insurancetypeId').val(insuranceTypeID);
                   $('#insurancetype').val(data['insuranceType']);
                   $('#marginPercentage').val(data['marginPercentage']);
                   $('#gl_code').val(data['GLAutoID']).change();
                    $('#insurancetypeheader').html('<?php echo $this->lang->line('sales_maraketing_masters_edit_insurance_type') ?>');/*Edit Insurance Type*/
                    $('#insurancetypemodal').modal('show');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function openinsurancemodal()
    {
        $('#insurance_type_form')[0].reset();
        $('#insurancetypeId').val('');
        $('#gl_code').val('').change();
        $('#insurancetypeheader').html('<?php echo $this->lang->line('sales_maraketing_masters_add_new_insurance_type') ?>');/*Add New Insurance Type*/
        $('#insurancetypemodal').modal('show');

    }


    function show_extra_charge(){
        var type=$('#type').val();
        if(type==2){
            $('#isChargeToExpense').iCheck('unchecked');
            $('#extrachargesrow').addClass('hidden');
            $('#taxapplicablrrow').removeClass('hidden');
        }else{
            $('#isChargeToExpense').iCheck('check');
            $('#extrachargesrow').removeClass('hidden');
            $('#taxapplicablrrow').addClass('hidden');
        }
    }

    function sub_insurance_type(insuranceTypeID){
        $('#sub_insurance_type_form')[0].reset();
        $('#insuranceTypeIDSub').val('');
        $('#masterTypeID').val(insuranceTypeID);
        $('#insuranceTypeSub').val('');
        $('#noofMonths').val('');
        $('#subinsurancetypeheader').html('<?php echo $this->lang->line('sales_maraketing_masters_add_new_sub_insurance_type') ?>');/*Add New Sub Insurance Type*/
        $('#subinsurancetypemodal').modal('show');
    }

    $('#sub_insurance_type_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            insuranceTypeSub: {validators: {notEmpty: {message: 'Insurance Type is required.'}}}
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
            url: "<?php echo site_url('Invoices/savesubinsurancetype'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    insurancetypetable_load();
                    $('#subinsurancetypemodal').modal('hide');
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });


    function opensubinsuranceeditmodel(insuranceTypeID,masterTypeID){
        $('#insuranceTypeIDSub').val(insuranceTypeID);
        $('#masterTypeID').val(masterTypeID);
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'insuranceTypeID':insuranceTypeID},
            url :"<?php echo site_url('Invoices/getinsurancetype'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data) {
                    $('#insuranceTypeSub').val(data['insuranceType']);
                    $('#noofMonths').val(data['noofMonths']);
                    $('#marginPercentageSub').val(data['marginPercentage']);
                    $('#subinsurancetypeheader').html('<?php echo $this->lang->line('sales_maraketing_masters_edit_sub_insurance_type') ?>');/*Edit Sub Insurance Type*/
                    $('#subinsurancetypemodal').modal('show');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function insurancetypetable_load(){
        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {accountTYpe: 0},
            url: "<?php echo site_url('Invoices/load_insurancetypetable'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#insurancebody').html(data);
                $("[rel=tooltip]").tooltip();

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }



</script>