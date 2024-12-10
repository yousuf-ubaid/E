<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_item_master');
echo head_page($title, false);

/*echo head_page('Item Master', false);*/
$main_category_arr = all_main_category_drop();
?>
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-6">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="glyphicon glyphicon-stop"
                          style="color:green; font-size:15px;"> </span><?php echo $this->lang->line('common_active'); ?>
                </td><!--Active-->
                <td><span class="glyphicon glyphicon-stop"
                          style="color:red; font-size:15px;"> </span><?php echo $this->lang->line('common_in_active'); ?>
                </td><!--Inactive-->
            </tr>
        </table>
    </div>
</div>
<br>
<div class="row">
    <div class="form-group col-sm-3">
        <label> <?php echo $this->lang->line('transaction_main_category'); ?> </label><!--Main Category-->
        <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
    </div>
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('transaction_sub_category'); ?> </label><!--Sub Category-->
        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox" onchange="LoadSubSubCategory()">
            <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
            <!--Select Category-->
        </select>
    </div>
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> </label><!--Sub Sub Category-->
        <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox">
            <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
            <!--Select Category-->
        </select>
    </div>
    <div class="col-sm-1" id="search_cancel" style="margin-top: 2%;">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>

</div>
<hr>
<div class="table-responsive">
    <form class="form-horizontal" id="percentage_form">
        <table id="item_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_main_category'); ?></th>
                <!--Main Category-->
                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category'); ?></th>
                <!--Sub Category-->
                <th style="min-width: 12%"><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?></th>
                <!--Sub Sub Category-->
                <th><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?></th>
                <!--Secondary Code-->
                <th style="min-width: 10%"><?php echo "FC (%)"; ?></th>
                <th style="min-width: 10%"><?php echo "PC (%)"; ?></th>
            </tr>
            </thead>
        </table>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <button type="button" class="btn btn-success"
                    onclick="confirmPercentage()">
                <?php echo $this->lang->line('common_confirm'); ?>
            </button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     id="item_img_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_master_image_upload'); ?></h4>
                <!--Image Upload-->
            </div>
            <div class="modal-body">
                <center>
                    <form id="img_uplode_form">
                        <input type="hidden" id="img_item_id" name="item_id">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 250px; height: 150px;">
                                <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="item_img" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"
                                 style="max-width: 250px; max-height: 150px;"></div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span
                                    class="fileinput-new"><?php echo $this->lang->line('erp_item_master_select_image'); ?></span>
                            <!--Select image-->
                            <span class="fileinput-exists"><?php echo $this->lang->line('common_change'); ?></span>
                            <!--Change-->
                            <input type="file" name="img_file" onchange="img_uplode()">
                        </span>
                                <a href="#" class="btn btn-default fileinput-exists"
                                   data-dismiss="fileinput"><?php echo $this->lang->line('transaction_remove'); ?></a>
                                <!--Remove-->
                            </div>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>
</div>
<?php

/*subItemConfigList_modal*/
$this->load->view('system/item/itemmastersub/item-master-list-view-modal');
?>
<script type="text/javascript">
    var Otable;
    var currency_decimal = 3;
    var draw = true;
    var originalPage = 0;
    var pageLen = 10;
    var originalPageLen = pageLen;
    var isChanged = 0;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_master', 'Test', 'Item Master');
        });
        item_table();

        $("#subcategoryID").change(function () {
            Otable.draw();
        });

        $("#subsubcategoryID").change(function () {
            Otable.draw();
        });

    });

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_sub_cat();
        Otable.draw();
    }

    function LoadSubSubCategory() {
        //$('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        //$('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        Otable.draw();
    }


    function item_table() {
        Otable = $('#item_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "pageLength": pageLen,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('itemmaster/fetch_item_percentage'); ?>",
            "aaSorting": [[0, 'desc']],
            "preDrawCallback": function (settings) {
                if (!draw) {
                    draw = true;
                    return false; // cancel draw
                }
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
                $("[name='itemchkbox']").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "SubSubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "fc"},
                {"mData": "pc"},
                {"mData": "itemDescription"},
                {"mData": "itemSystemCode"}
            ],
            "columnDefs": [{"targets": [2, 3], "orderable": false},
                {
                    "visible": false,
                    "searchable": true,
                    "targets": [8, 9]
                }],
            createdRow: function (row, data, dataIndex) {
                var a = $(row).addClass('common_row' + (dataIndex))
                //$(row).find('td:eq(2)').attr('data-validate', a);
            },
            /*"fnPreDrawCallback": function( oSettings ) {
                alert('hi')
                return true;
            },*/
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        Otable.on('page.dt length.dt', function (e, settings) {
            if(isChanged) {
                if (!confirm("Changes done to percentage. Are you sure you want to confirm?")) {
                    draw = false;
                    // Reset the current page and current page length
                    settings._iDisplayStart = originalPage;
                    settings._iDisplayLength = originalPageLen;
                    $('[name="item_table_length"]').val(originalPageLen);
                    isChanged = 0;
                } else {
                    originalPage = settings._iDisplayStart;
                    originalPageLen = settings._iDisplayLength;
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: $("#percentage_form").serialize(),
                        url: "<?php echo site_url('ItemMaster/save_item_percentage'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            isChanged = 0;

                        }, error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', '<br>Error : ' + errorThrown);
                        }
                    });

                }
            }else{
                originalPage = settings._iDisplayStart;
                originalPageLen = settings._iDisplayLength;
            }
        });
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_itemMaster_subsubCategory(select_val) {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function clearSearchFilter() {
        $('#mainCategoryID').val("");
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        $('#subcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        $('#subsubcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        Otable.draw();
    }

    function applyToAllCols(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var textValue = $(element).closest('td').find('input').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#item_table tr').length - 1;

                for (var i = elementTr; i <= totalTr; i++) {
                    var oldval = $(".common_row" + i + " td:eq(" + elementTd + ")").find('input[type="text"]').val();
                    $(".common_row" + i + " td:eq(" + elementTd + ")").find('input[type="text"]').val(textValue);
                    var fc = $(".common_row" + i).find('.fc').val();
                    var pc = $(".common_row" + i).find('.pc').val();
                    if (elementTd == 6) {
                        var remaining = (100 - parseFloat(fc));
                        $(".common_row" + i + " td:eq(7)").find('input[type="text"]').val(remaining);
                    }
                    if (elementTd == 7) {
                        var remaining = (100 - parseFloat(pc));
                        $(".common_row" + i + " td:eq(6)").find('input[type="text"]').val(remaining);
                    }
                    //$(".itemSubDesc" + i).val(textValue);
                }
            });
    }

    function confirmPercentage() {
        swal({
                title: "Are you sure?",
                text: "You want to confirm",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: $("#percentage_form").serialize(),
                    url: "<?php echo site_url('ItemMaster/save_item_percentage'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        isChanged = 0;

                    }, error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Error : ' + errorThrown);
                    }
                });
            });
    }

    function validatePercentage(element, field) {
        isChanged = 1;
        var value = $(element).val();
        if ((value !== '') && (value.indexOf('.') === -1) && $.isNumeric(value)) {
            $(element).val(Math.max(Math.min(value, 100), 0));
        }
        var fc = $(element).closest('tr').find('.fc').val();
        var pc = $(element).closest('tr').find('.pc').val();

        if (field == 'fc') {
            var remaining = 100 - parseFloat(fc);
            $(element).closest('tr').find('.pc').val(remaining);
        }
        if (field == 'pc') {
            var remaining = 100 - parseFloat(pc);
            $(element).closest('tr').find('.fc').val(remaining);
        }
    }

    function validateFloatKeyPress(el, evt, currency_decimal=3) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');

        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }


</script>