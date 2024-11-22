<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('project_management', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_pm_project_category');
echo head_page($title, false);

/*echo head_page('Project Category',false);*/
  $nextSortOrder = nextSortOrder() + 1;
  $GLcode= fetch_glcode_claim_category();
  $unit_array=all_umo_drop();
  $project=get_all_boq_project();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">


    <div class="col-md-12 text-center">
        <button type="button" class="btn btn-primary pull-right"
                onclick="addNewCategory();"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="boq_category_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style=""><?php echo $this->lang->line('promana_common_id');?><!--ID--></th>
            <th style=""><?php echo $this->lang->line('common_project');?><!--Project--></th>
            <th style=""><?php echo $this->lang->line('common_category');?><!--Category--></th>
            <th style=""><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
            <th style=""><?php echo $this->lang->line('promana_pm_project_revenue_gl_code');?><!--Revenue GL Code--></th>
            <th style=""><?php echo $this->lang->line('common_action');?><!--Action--></th>

        </tr>
        </thead>
    </table>
</div>

<div aria-hidden="true" role="dialog"  id="category_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('promana_pm_create_new_project_category');?><!--Create New project Category--></h4>
            </div>
            <form role="form" id="boq_create_category_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('common_project');?><!--Project--></label>
                        <div class="col-sm-8">
                          <?php echo form_dropdown('projectID', $project, '', 'class="form-control searchbox" onchange="getCategorySortID()" id="projectID"  required'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('promana_pm_category_code');?><!--Category Code--></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="CatCode" name="CatCode"
                                   placeholder="Category Code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="CatDescrip" name="CatDescrip"
                                   placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" readonly id="SortOrder" name="SortOrder"
                                   value="<?php /*echo $nextSortOrder */?>" placeholder="Sort Order">

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('promana_pm_project_revenue_gl_code');?><!--Revenue GL Code--></label>
                        <div class="col-sm-8">
                          <?php echo form_dropdown('GLcode', $GLcode, '', 'class="form-control searchbox" id="GLcode"  required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"></label>
                        <div class="col-sm-4">
                            <button class="btn btn-primary btn-xs" type="submit"><?php echo $this->lang->line('common_add');?><!--Add--></button>
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>

                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog"  id="modal_boq_subcategory" class="modal fade"
     style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 id="title" class="modal-title"><?php echo $this->lang->line('promana_common_sub_cat');?><!--Sub Category--></h5>
            </div>
            <form role="form" id="boq_create_subcategory_form" class="form-horizontal">
                <div class="modal-body">

                    <input type="hidden" name="MainCatID" id="MainCatID">
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="SubCatDes" name="SubCatDes"
                                   placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" readonly id="subSortOrder" name="subSortOrder"
                                   value="<?php echo $nextSortOrder ?>" placeholder="Sort Order">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle"
                               class="col-sm-3 control-label"><?php echo $this->lang->line('common_uom');?><!--UOM--> </label>
                        <div class="col-sm-4">
                          <?php echo form_dropdown('unitID', $unit_array, '', 'class="form-control" id="unitID" required'); ?>
                        </div>
                    </div>
                    <br>
                    <table id="loadsubcategoryTable" class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                            <td><?php echo $this->lang->line('promana_common_id');?><!--ID--></td>
                            <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                            <td><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></td>
                            <td><?php echo $this->lang->line('common_uom');?><!--UOM--></td>
                            <td></td>

                            <!--<td></td>-->
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
                </div>


        </div>
        </form>
    </div>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.searchbox').select2();
        $('.headerclose').click(function(){
            fetchPage('system/pm/category','','Project Category');
        });

        loadcategorytable();

    });


    function loadcategorytable(){
        window.Otable=  $('#boq_category_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_Boq_categoryTable'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] },{
                "targets": [3,4,5,0],
                "orderable": false
            } ],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var
                    tmp_i = oSettings._iDisplayStart;
                var
                    iLen = oSettings.aiDisplay.length;
                var
                    x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;

                api.column(1, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="5"><b>&nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;  '+group+'</b></td></tr>'
                        );

                        last = group;
                    }
                } );

            },
            "aoColumns": [
                {"mData": "categoryCode"},
                {"mData": "project"},
                {"mData": "category"},
                {"mData": "sortOrder"},
                {"mData": "GLcode"},
                 {"mData": "action"}
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

        $('#boq_category_table').on( 'click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 1 && currentOrder[1] === 'asc' ) {
                Otable.order( [ 1, 'desc' ] ).draw();
            }
            else {
                Otable.order( [ 1, 'asc' ] ).draw();
            }
        } );

    }


    function addNewCategory() {
       $('#projectID,#GLcode').val(null).trigger('change');
       $('#CatCode,#CatDescrip,#SortOrder').val('');
        $('#boq_create_category_form')[0].reset();
        $('#boq_create_category_form').bootstrapValidator('resetForm', true);
        $("#category_modal").modal({backdrop: "static"});
    }

    $('#boq_create_category_form').bootstrapValidator({

        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            CatCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_category_code_is_required');?>.'}}},/*Category Code is required*/
            CatDescrip: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
            GLcode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_project_revenue_gl_code_is_required');?>.'}}},/*Revenue GL Code is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_category'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {
                HoldOn.close();
                loadcategorytable();

                myAlert(data[0],data[1]);
               getCategorySortID();


            /*    $form.bootstrapValidator('resetForm', true);
                $("#category_modal").modal('hide');*/

            }, error: function () {

                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    });

    function getCategorySortID() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getCategorySortID'); ?>",
            dataType: "json",
            data: {projectID:$('#projectID').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                $('#SortOrder').val(data);


            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

    function addNewSubCategory(id, desc) {
        getSubcategorySortID(id);

        $('#boq_create_subcategory_form')[0].reset();
        $('#boq_create_subcategory_form').bootstrapValidator('resetForm', true);
        $('#SubCatDes').val('');
        $('#unitID').val('');
        $('#title').html(desc + '- Sub Category');
        $('#MainCatID').val(id);
        loadsubCategorytable();
        $("#modal_boq_subcategory").modal({backdrop: "static"});
    }

    function getSubcategorySortID(MainCatID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getSubcategorySortID'); ?>",
            data: {'MainCatID': MainCatID},
            dataType: "json",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                $('#subSortOrder').val(data);


            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

    function loadsubCategorytable() {

        var Otable = $('#loadsubcategoryTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Boq/load_sub_category_table'); ?>",
            "bJQueryUI": true,
            "iDisplayStart ": 2,
            "sEcho": 1,
            "sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "subCategoryID"},
                {"mData": "description"},
                {"mData": "sortOrder"},
                {"mData": "UnitShortCode"},
                {"mData": "action"}
            ],

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "MainCatID", "value": $("#MainCatID").val()});

                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    $('#boq_create_subcategory_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            SubCatDes: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_subcategory'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {
                HoldOn.close();
               myAlert(data[0],data[1]);
                $('#SubCatDes').val('');
                getSubcategorySortID($('#MainCatID').val());
                loadsubCategorytable($('#MainCatID').val());
            }, error: function () {
                HoldOn.close();

            }
        });
    });

    function deleteCategory(categoryID){
        if (categoryID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able');?>",/*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>",/*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'categoryID': categoryID},
                        url: "<?php echo site_url('Boq/delete_category'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            myAlert(data[0],data[1]);


                            loadcategorytable();
                            HoldOn.close();
                            refreshNotifications(true);

                        }, error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
    }

    function deletesubcategory(subCategoryID){
        if (subCategoryID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able');?>",/*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>",/*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'subCategoryID': subCategoryID},
                        url: "<?php echo site_url('Boq/deletesubcategory'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            myAlert(data[0],data[1]);

                            getSubcategorySortID($('#MainCatID').val());
                            loadsubCategorytable();
                            HoldOn.close();
                            refreshNotifications(true);

                        }, error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
    }





</script>