<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('project_management', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_pm_project_category');


/*echo head_page('Project Category',false);*/
$nextSortOrder = nextSortOrder() + 1;
$GLcode= fetch_glcode_claim_category();
$unit_array=all_umo_drop();
$project=get_all_boq_project();
?>
<div class="table-responsive">
    <table id="boq_dashboard_projectschedule_table" class="<?php echo table_class() ?>">
        <input type="hidden" name="datefromconvert" id="datefromconvert" value="<?php echo $datefromconvert ?>">
        <input type="hidden" name="datetoconvert" id="datetoconvert" value="<?php echo $datetoconvert ?>">
        <input type="hidden" name="project" id="project" value="<?php echo $projectID ?>">
        <thead>
        <tr>
            <th style="">#</th>
            <th style="">Project Code</th>
            <th style="">Project</th>
            <th style="">Project Name</th>
            <th style="">Assigned Employee</th>
            <th style="">Task</th>
            <th style="">Note</th>
            <th style="">Start Date</th>
            <th style="">End Date</th>

        </tr>
        </thead>
    </table>
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
        window.Otable=  $('#boq_dashboard_projectschedule_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/load_project_tasksheduledata'); ?>",
            "aaSorting": [[6, 'desc']],
            "fnInitComplete": function () {

            },
        /*    "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] },{
                "targets": [4,5,0],
                "orderable": false
            } ],*/
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

              /*  var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
                api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="5"><b>&nbsp;<i class="fa fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;  '+group+'</b></td></tr>'
                        );

                        last = group;
                    }
                } );
                api.column(1, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="5"><b>&nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;  '+group+'</b></td></tr>'
                        );

                        last = group;
                    }
                } );*/




            },
            "aoColumns": [
             {"mData": "headerID"},
             {"mData": "projectCode"},
             {"mData": "projectName"},
             {"mData": "projectDescription"},
             {"mData": "ename2"},
             {"mData": "description"},
             {"mData": "note"},
             {"mData": "startDate"},
             {"mData": "endDate"},
            ],
            "columnDefs": [{"targets": [8], "orderable": false},{"targets": [0,1,2,3,4,5,6,7,8], "visible": true,"searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefromconvert", "value": $("datefromconvert").val()});
                aoData.push({"name": "datetoconvert", "value": $("#datetoconvert").val()});
                aoData.push({"name": "project", "value": $("#project").val()});
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
            "sAjaxSource": "<?php echo site_url('Boq/load_project_tasksheduledata'); ?>",
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