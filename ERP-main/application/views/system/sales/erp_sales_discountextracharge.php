<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_discount_extracharges');
echo head_page($title, false);

/*echo head_page('Customer Category', false);*/ ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row table-responsive">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="opendiscountmodel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_create_category');?>  </button><!--Create Category-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="discount_extra_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_description');?> <!--Description--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_type');?><!-- Type--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_is_charge_to_expense');?><!-- Is charged to expense--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_is_tax_applicable');?><!-- Is Tax Applicable--></th>
            <th style="min-width: 28%"><?php echo $this->lang->line('common_gl_code');?> <!--GL Code--></th>
            <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog"  id="discount_extra_modal" class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="categoryHead"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="discount_extra_form"'); ?>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="discountExtraChargeID" name="discountExtraChargeID">
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('common_type');?><!--Type--> </label>
                            <select name="type" class="form-control" id="type" onchange="show_extra_charge()">
                                <option value="" selected="selected"><?php echo $this->lang->line('common_select_type');?><!--Select Type--></option>
                                <option value="1">Discount</option>
                                <option value="2">Extra Charges</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('common_description');?><!--Description --></label>
                            <input type="text" name="Description" id="Description" class="form-control">

                        </div>

                    </div>
                    <div class="row" >
                        <div class="col-md-6" id="extrachargesrow">
                            <div class="form-group">
                                <label for=""><?php echo $this->lang->line('sales_maraketing_masters_is_charge_to_expense');?><!-- Is charged to expense--></label>
                                <div class="skin skin-square">
                                    <div class="skin-section" id="extraColumns">
                                        <input id="isChargeToExpense" type="checkbox"
                                               data-caption="" class="columnSelected" name="isChargeToExpense" value="1" checked>
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Is Default<!-- Is Default--></label>
                                <div class="skin skin-square">
                                    <div class="skin-section" id="extraColumns">
                                        <input id="isDefault" type="checkbox"
                                               data-caption="" class="" name="isDefault" value="1" checked>
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="glCodehide">
                            <label><?php echo $this->lang->line('common_gl_code');?> <!--GL Code--></label>
                            <?php echo form_dropdown('glCode', company_PL_account_drop(), '', 'class="form-control select2" id="glCode"'); ?>
                        </div>
                        <div class="col-md-6 hidden" id="taxapplicablrrow">
                            <div class="form-group">
                                <label for=""><?php echo $this->lang->line('sales_maraketing_masters_is_tax_applicable');?><!-- Is Tax Applicable--></label>
                                <div class="skin skin-square">
                                    <div class="skin-section" id="extraColumns">
                                        <input id="isTaxApplicable" type="checkbox"
                                               data-caption=""  name="isTaxApplicable" value="1" checked>
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-primary " onclick="saveDiscount()"><?php echo $this->lang->line('common_save');?>
                        </button><!--Save-->
                        <button data-dismiss="modal" class="btn btn-sm btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        discount_extra_table_table();
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



    function discount_extra_table_table() {
        var Otable = $('#discount_extra_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('DiscountAndExtraCharges/fetch_discount_extra_charges'); ?>",
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
                {"mData": "discountExtraChargeID"},
                {"mData": "Description"},
                {"mData": "typeDesc"},
                {"mData": "isChargeToExp"},
                {"mData": "isTaxAppl"},
                {"mData": "glView"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [0,3,4,6], "searchable": false}],
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


    function saveDiscount(){
        var isChargeToExpenseval=0;
        var isTaxApplicableval=0;

        if ($('#isChargeToExpense').is(':checked')){
            isChargeToExpenseval=1;
        }
        if ($('#isTaxApplicable').is(':checked')){
            isTaxApplicableval=1;
        }
        if ($('#type').val()==2){
            isChargeToExpenseval=0;
        }
        if ($('#type').val()==1){
            isTaxApplicableval=0;
        }
        var data = $("#discount_extra_form").serializeArray();
        data.push({'name' : 'isChargeToExpenseval', 'value' : isChargeToExpenseval });
        data.push({'name' : 'isTaxApplicableval', 'value' : isTaxApplicableval });
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data: data,
            url :"<?php echo site_url('DiscountAndExtraCharges/saveDiscountCategory'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    discount_extra_table_table();
                    $('#discount_extra_modal').modal('hide');
                    $('#discount_extra_form')[0].reset();
                    $('#discountExtraChargeID').val('');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function delete_discount_category(discountExtraChargeID){
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
                    data : {'discountExtraChargeID':discountExtraChargeID},
                    url :"<?php echo site_url('DiscountAndExtraCharges/delete_discount_category'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            discount_extra_table_table();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function open_discount_edit_model(discountExtraChargeID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'discountExtraChargeID':discountExtraChargeID},
            url :"<?php echo site_url('DiscountAndExtraCharges/getDiscount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data) {
                    $('#type').val(data['type']);
                    $('#Description').val(data['Description']);
                    $('#glCode').val(data['glCode']).change();
                    var type=data['type'];
                    if (data['isChargeToExpense'] == 1) {
                        if(type==1){
                            $('#glCodehide').removeClass('hidden');
                        }
                        $('#isChargeToExpense').iCheck('check');
                     } else {
                        if(type==1){
                            $('#glCodehide').addClass('hidden');
                        }else{
                            $('#glCodehide').removeClass('hidden');
                        }
                        $('#isChargeToExpense').iCheck('uncheck');
                     }
                    if (data['isTaxApplicable'] == 1) {
                        $('#isTaxApplicable').iCheck('check');
                     } else {
                        $('#isTaxApplicable').iCheck('uncheck');
                     }
                     if (data['isDefault'] == 1) {
                        $('#isDefault').iCheck('check');
                     } else {
                        $('#isDefault').iCheck('uncheck');
                     }
                    if(type==2){
                        $('#extrachargesrow').addClass('hidden');
                        $('#taxapplicablrrow').removeClass('hidden');
                    }else{
                        $('#extrachargesrow').removeClass('hidden');
                        $('#taxapplicablrrow').addClass('hidden');
                    }
                    $('#discountExtraChargeID').val(discountExtraChargeID);
                    $('#categoryHead').html('<?php echo $this->lang->line('sales_maraketing_masters_edit_category');?>');/*Edit Category*/
                    $('#discount_extra_modal').modal('show');

                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function opendiscountmodel(){
        $('#glCodehide').removeClass('hidden');
        $('#extrachargesrow').removeClass('hidden');
        $('#taxapplicablrrow').addClass('hidden');
        $('#isChargeToExpense').iCheck('check');
        $('#discountExtraChargeID').val('');
        $('#glCode').val('').change();
        $('#categoryHead').html('<?php echo $this->lang->line('sales_maraketing_masters_add_new_discount');?>');/*Add New Discount*/
        $('#discount_extra_form')[0].reset();
        $('#discount_extra_modal').modal('show');
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



</script>