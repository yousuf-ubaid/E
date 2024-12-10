<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #designation-add-tb td{ padding: 2px; }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_designation_master');
echo head_page($title  , false);
$jobCategoryList = load_job_category_list();
$academicDegreeTypeList = array_filter(fetch_degree());
$professionalDegreeTypeList = array_filter(fetch_degree(null, 'professional'));
$technicalDegreeTypeList = array_filter(fetch_degree(null, 'technical'));
?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading panel-bg-gray panel-heading-nav">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                    <a href="#one" aria-controls="one" role="tab" data-toggle="tab">Designation</a>
                    </li>
                    <li role="presentation">
                    <a href="#two" aria-controls="two" role="tab" data-toggle="tab">Job Category</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="one">
                        <div class="row">
                            <div class="col-md-7 pull-right">
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDesignation_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
                            </div>
                        </div><hr>
                        <div class="table-responsive">
                            <table id="load_designation" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="width: auto"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
                                    <th style="width: 220px"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="two">
                        <div class="row">
                            <div class="col-md-7 pull-right">
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_jobcategory_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
                            </div>
                        </div><hr>
                        <div class="table-responsive">
                            <table id="load_job_category" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="width: auto">Job Category</th>
                                    <th style="width: 60px"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      
</div>



<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_designation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_add_designation');?><!--Add Designation--></h4>
            </div>
            <form id="add-designation_form" >
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Designation Name<!--Designation Name--></label>
                            <input type="text" name="designationName" id="designationName" class="form-control inputControl tt-input" placeholder="Designation name" >
                        </div>
                        <div class="form-group col-md-6">
                            <label>Job Category</label>
                            <?php echo form_dropdown('jobCategoryID', $jobCategoryList, '', 'class="form-control inputControl" id="jobCategoryID"'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_designation()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="new_job_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Add Job Category<!--Add Job category--></h4>
            </div>
            <form class="form-horizontal" id="add-job-category-form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="designation-add-tb">
                        <thead>
                        <tr>
                            <th>Job Category Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="job_category_name" class="form-control" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_job_category()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="JDDescription_modal" tabindex="-1" aria-labelledby="myModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">JD Description</h4>
            </div>
            <form id="JDDescription_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Job Description</label>
                                <textarea name="JDDescription" id="JDDescription" class="form-control tinymce" rows="4" cols="50"></textarea>
                                <input type="hidden" id="de_id" name="id" value="0">
                            </div>
                            <div class="form-group">
                                <label>Job Responsibilities</label>
                                <textarea type="text" name="jobResponsibilities" id="jobResponsibilities" class="form-control tinymce" rows="4" cols="50"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Job Roles</label>
                                <textarea name="jobRoles" id="jobRoles" class="form-control tinymce" rows="4" cols="50"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Experience</label>
                                <input type="number" name="experience" id="experience" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Professional Qualification</label> <button type="button" class="btn btn-xs btn-primary" onclick="openDegreeType_modal('professional','Professional Qualification')"><i class="fa fa-plus"></i></button>
                                <?php echo form_dropdown('professionalQualifications[]', $professionalDegreeTypeList, '', 'class="form-control select2" id="professionalQualifications" multiple=""'); ?>
                            </div>
                            <div class="form-group">
                                <label>Technical Qualification</label> <button type="button" class="btn btn-xs btn-primary" onclick="openDegreeType_modal('technical','Technical Qualification')"><i class="fa fa-plus"></i></button>
                                <?php echo form_dropdown('technicalQualifications[]', $technicalDegreeTypeList, '', 'class="form-control select2" id="technicalQualifications" multiple=""'); ?>
                            </div>
                            <div class="form-group">
                                <label>Academic Qualification</label> <button type="button" class="btn btn-xs btn-primary" onclick="openDegreeType_modal('academic','Academic Qualification')"><i class="fa fa-plus"></i></button>
                                <?php echo form_dropdown('academicQualifications[]', $academicDegreeTypeList, '', 'class="form-control select2" id="academicQualifications" multiple=""'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Other Notes</label>
                                <textarea name="otherNotes" id="otherNotes" class="form-control" rows="4" cols="50"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateJDDescription()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_edit_designation_description');?><!--Edit Designation Description--></h4>
            </div>

            <form role="form" id="editDesignation_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="designationDes" name="designationDes">
                                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateDesignation()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="degreeTypeModal" tabindex="-1" role="dialog" aria-labelledby="degreeTypeModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="degreeTypeModalLabel">Degree Type</h4>
            </div>

            <form role="form" id="degreeType_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="description" name="description">
                                    <input type="hidden" id="type" name="type" value="academic">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="creatDegreeType()"><?php echo $this->lang->line('common_create');?><!--Create--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var designation_tb = $('#designation-add-tb');

    tinymce.init({
        selector: ".tinymce",
        height: 200,
        browser_spellcheck: true,
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
        ],
        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

        menubar: false,
        toolbar_items_size: 'small',

        style_formats: [{
            title: 'Bold text',
            inline: 'b'
        }, {
            title: 'Red text',
            inline: 'span',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Red header',
            block: 'h1',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Example 1',
            inline: 'span',
            classes: 'example1'
        }, {
            title: 'Example 2',
            inline: 'span',
            classes: 'example2'
        }, {
            title: 'Table styles'
        }, {
            title: 'Table row 1',
            selector: 'tr',
            classes: 'tablerow1'
        }],

        templates: [{
            title: 'Test template 1',
            content: 'Test 1'
        }, {
            title: 'Test template 2',
            content: 'Test 2'
        }]
    });

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/designation_master','Test','HRMS');
        });
        load_designation();
        load_job_category();
        $('.select2').select2();
    });

    function load_designation(selectedRowID=null){
        var Otable = $('#load_designation').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_designation'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();


                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['DesignationID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }


            },
            "aoColumns": [
                {"mData": "DesignationID"},
                {"mData": "DesDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,2]}],
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

    function openDesignation_modal(){
        $('#designation-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_designation').modal({backdrop: "static"});
    }

    function openJDDescription_modal(id, des){        

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Designation/getById'); ?>',
            data: {'id': id},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                data = data.data;
                $('#JDDescription_form')[0].reset();

                $('#de_id').val( $.trim(id) );
                tinymce.get('JDDescription').setContent(decodeHtmlEntities(data.jobDescription));
                tinymce.get('jobResponsibilities').setContent(decodeHtmlEntities(data.jobResponsibilities));
                tinymce.get('jobRoles').setContent(decodeHtmlEntities(data.jobRoles));
                $('#otherNotes').val(data.otherNotes);
                $('#experience').val(data.experience).change();
                $('#academicQualifications').val(data.academicQualifications).change();
                $('#professionalQualifications').val(data.professionalQualifications).change();
                $('#technicalQualifications').val(data.technicalQualifications).change();
                $('#JDDescription_modal').modal({backdrop: "static"});

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function decodeHtmlEntities(str) {
        let doc = new DOMParser().parseFromString(str, 'text/html');
        return doc.documentElement.innerHTML || doc.body.innerHTML;
    }

    function load_job_category(selectedRowID=null){
        var Otable = $('#load_job_category').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_job_category'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
               
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['DesignationID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }


            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "JobCategory"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,2]}],
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

    function open_jobcategory_modal(){
        $('#designation-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_job_category').modal({backdrop: "static"});
    }

    function save_designation(){
    
            var postData = $('#add-designation_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/saveDesignation'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_designation').modal('hide');
                        load_designation();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        
    }

    function edit_designation(id, des){
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#designationDes').val( $.trim(des) );
    }

    function delete_designation(id, description){
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
                    async : true,
                    url :"<?php echo site_url('Designation/delete'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data.status, data.message);
                        if(data.status == 's'){ load_designation() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function deleteJobCategory(id, description){
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
                    async : true,
                    url :"<?php echo site_url('Employee/deleteJobCategory'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_job_category() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function save_job_category(){
    
            var postData = $('#add-job-category-form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/saveJobCategory'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_job_category').modal('hide');
                        load_job_category();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        
    }

    function updateJDDescription(){
        tinymce.triggerSave();
        var postData = $('#JDDescription_form').serialize();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Designation/update'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data.status, data.message);

                if(data.status == 's'){
                    $("#JDDescription_form")[0].reset();
                    $('#JDDescription_modal').modal('hide');
                    load_designation();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }

    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });

    function add_more(){
        var appendData = '<tr><td><input type="text" name="description[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        designation_tb.append(appendData);
    }

    function updateDesignation(){
        var postData = $('#editDesignation_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/editDesignation'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_designation( $('#hidden-id').val() );
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function openDegreeType_modal(type, header){
        $('#degreeType_form')[0].reset();
        $('#type').val(type);
        $('#degreeTypeModalLabel').html(header);

        $('#degreeTypeModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    function creatDegreeType(){
        let postData = $('#degreeType_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('DegreeType/create'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data.status, data.message);
                if(data.status === 's') {
                    $('#degreeTypeModal').modal('hide');
                    getDegreeType($('#type').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function getDegreeType(type){
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('DegreeType/getByType'); ?>',
            data: {type:type},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                if (type === 'professional') {
                    $.each(data.data, function(index, option) {
                        if ($('#professionalQualifications option[value="' + option.id + '"]').length === 0) {
                            $('#professionalQualifications').append(
                                $('<option>', {
                                    value: option.id,
                                    text: option.description
                                })
                            );
                        }
                    });
                    $('#professionalQualifications').select2();
                }

                if (type === 'academic') {
                    $.each(data.data, function(index, option) {
                        if ($('#academicQualifications option[value="' + option.id + '"]').length === 0) {
                            $('#academicQualifications').append(
                                $('<option>', {
                                    value: option.id,
                                    text: option.description
                                })
                            );
                        }
                    });
                    $('#academicQualifications').select2();
                }

                if (type === 'technical') {
                    $.each(data.data, function(index, option) {
                        if ($('#technicalQualifications option[value="' + option.id + '"]').length === 0) {
                            $('#technicalQualifications').append(
                                $('<option>', {
                                    value: option.id,
                                    text: option.description
                                })
                            );
                        }
                    });
                    $('#technicalQualifications').select2();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

</script>