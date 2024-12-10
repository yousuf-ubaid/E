<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_item_category');
echo head_page($title, false);


/*echo head_page('Item Category',false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .form1{
        width:290px !important;
    }
    .btn-primary {
        background-color: #34495e;
        border-color: #34495e;
        color: #FFFFFF;
    }
</style>
<!--<div class="row">
    <div class="col-md-9"></div>
    <div class="col-md-3" style="margin-bottom: 15px; margin-top:15px;">
        <button type="button" onclick="reset_form()" class="btn btn-xs btn-primary pull-right" data-toggle="modal" data-target="#itemcategory_model"><span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
            New
        </button>
    </div>
</div>-->
<div class="table-responsive">
    <table id="itemcategory_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 70%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('transaction_sub_category');?><!--Sub Category--></th>
                <!--<th style="min-width: 5%">Edit</th>-->
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="itemcategory_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_category_add_new_item_category');?><!--Add New Item Category--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="itemcategory_form"') ?>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="itemcategoryedit" name="itemcategoryedit">
                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <input type="text" class="form-control form1" id="description" name="description">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('erp_item_category_code_prefix');?><!--Code Prefix--></label>
                        <input type="text" class="form-control form1" id="codeprefix" name="codeprefix">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('erp_item_category_start_serial');?><!--Start Serial--></label>
                        <input type="number" class="form-control form1" id="startserial" name="startserial">
                    </div>
                </div>
                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('erp_item_category_code_length');?><!--Code Length--></label>
                        <input type="number" class="form-control form1" id="codelength" name="codelength">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('transaction_item_type');?><!--Item Type--></label>
                        <select name="itemtype" id="itemtype" class="form-control form1 searchbox">
                            <option value=""><?php echo $this->lang->line('common_please_select');?><!--Please Select--></option>
                            <option value="Inventory Item"><?php echo $this->lang->line('erp_item_category_inventory_item');?><!--Inventory Item--></option>
                            <option value="Non Inventory Item"><?php echo $this->lang->line('erp_item_category_non_inventory_item');?><!--Non Inventory Item--></option>
                            <option value="Service"><?php echo $this->lang->line('erp_item_category_service');?><!--Service--></option>
                            <option value="Fixed Assets"><?php echo $this->lang->line('erp_item_category_fixed_assets');?><!--Fixed Assets--></option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="categoryTypeID"><?php echo $this->lang->line('erp_item_category_cat_type');?><!--Category Type--></label>
                        <?php echo form_dropdown('categoryTypeID', category_type(),'','class="form-control" id="categoryTypeID" required'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--> <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> </button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemcategoryedit_model" role="dialog" >
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_category_edit');?><!--Item Category Edit--></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" class="form-horizontal" id="itemcategoryedit_form"') ?>
                <input type="hidden" class="form-control" id="itemcategoryeditfrm" name="itemcategoryeditfrm" value="<?php if(isset($_POST["page_id"])) echo $_POST["page_id"]; ?>">
                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('common_description');?><!--Description--></label>
                    <input type="text" class="form-control form1" id="description" name="description">
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--> <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> </button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/item/srp_itemcategory_view','','Item Category');
        });
        $(".searchbox").select2({
            placeholder: "Please Select"
        });

        itemcategoryview();
        $('#itemcategory_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                codeprefix: {validators: {notEmpty: {message: 'Code Prefix is required.'}}},
                startserial: {validators: {notEmpty: {message: 'Start Serial is required.'}}},
                codelength: {validators: {notEmpty: {message: 'Code Length is required.'}}},
                itemtype: {validators: {notEmpty: {message: 'Item Type is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
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
                url: "<?php echo site_url('ItemCategory/save_itemcategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if(data){
                        $("#itemcategory_model").modal("hide");
                        itemcategoryview();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });
    });

    function itemcategoryview() {
        var Otable = $('#itemcategory_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('ItemCategory/load_category'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "itemCategoryID"},
                {"mData": "description"},
                {"mData": "addsub"}
            ],
            "columnDefs": [{
                "targets": [2],
                "orderable": false
            }],
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

    function reset_form() {
        $("#itemtype").select2("val", "");
        $('#itemcategoryedit').val("");
        document.getElementById('itemcategory_form').reset();
    }

    $("#itemcategory_model").on("hidden.bs.modal", function () {
        itemcategoryview();
    });

    function openitemcateditmodel(id){
        $("#itemcategory_model").modal("show");
        $('#itemcategoryedit').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('ItemCategory/edit_itemcategory'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#description').val(data['description']);
                $('#codeprefix').val(data['codePrefix']);
                $('#startserial').val(data['StartSerial']);
                $('#codelength').val(data['codeLength']);
                $('#categoryTypeID').val(data['categoryTypeID']);
                $('#itemtype').select2('val', data['itemType']);
            }, 
            error: function () {
                alert('An Error Occurred! Please Try Again.');

            }
        });
    }
</script>