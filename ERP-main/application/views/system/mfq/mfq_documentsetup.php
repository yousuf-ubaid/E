<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title= $this->lang->line('manufacturing_document_setup');
echo head_page($title, false); ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #mfq_standard_details th{
        text-transform: uppercase;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>



<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_public_1" data-toggle="tab" aria-expanded="true" >Document Setup</a></li>
        <li class=""><a href="#tab_public_2" data-toggle="tab" aria-expanded="false" >Document Notes</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_public_1">
            <div class="row">
                <div class="col-md-5">

                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-right">
                    <button type="button" style="margin-right: 17px;" class="btn btn-primary pull-right"
                            onclick="add_new_document_setup()"><i
                                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
                    </button>
                </div>
            </div>
            <br>
            <hr style="margin-top: 7px;margin-bottom: 7px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="mfq_document_setup" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 30%"><?php echo $this->lang->line('manufacturing_description');?><!--Description--></th>
                                <th style="width: 12%"><?php echo $this->lang->line('common_mandatory');?><!--Mandatory--></th>
                                <th style="width: 12%"><?php echo $this->lang->line('manufacturing_is_active');?><!--Is Active--></th>
                                <th style="width: 5%">&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab_public_2">

            <div id="filter-panel" class="collapse filter-panel">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <input type="hidden" value="MDN" id="documentIDfltr" name="documentIDfltr"/>
                    </div>

                    <div class="form-group col-sm-4">
                        <br>
                        <button type="button" class="btn btn-primary"
                                onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">

                </div>
                <div class="col-md-2 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-right">

                    <!--Add Expense Claim-->
                    <button type="button" class="btn btn-primary pull-right"
                            onclick="openNotesModal()">
                        <i class="fa fa-plus"></i>
                        <?php echo $this->lang->line('common_create');?><!--Create-->
                    </button>
                </div>
            </div>
            <br>
            <hr style="margin-top: 7px;margin-bottom: 7px;">
            <div class="table-responsive">
                <table id="termsandconditions_table" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 2%">#</th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_document');?><!--Document--></th>
                        <th style="min-width: 50%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_is_default');?><!--Is Default--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                </table>
            </div>
            <?php echo footer_page('Right foot', 'Left foot', false); ?>


            <div aria-hidden="true" role="dialog" id="note_modal" class="modal fade"
                 style="display: none;">
                <div class="modal-dialog modal-lg" style="width: 60%;">
                    <div class="modal-content">
                        <div class="color-line"></div>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h5 class="modal-title"><?php echo $this->lang->line('config_add_notes');?><!--Add Notes--></h5>
                        </div>
                        <div class="modal-body">
                            <form role="form" id="notes_form" class="form-group">
                                <input type="hidden" id="autoIDhn" name="autoIDhn">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group col-sm-4">
                                            <input type="hidden" value="MDN" id="documentID" name="documentID"/>
                                            <label for="">
                                                <?php echo $this->lang->line('common_is_default');?><!--Is Default-->
                                            </label>
                                            <div class="skin-section extraColumns"><input id="isDefault" type="checkbox" data-caption="" class="columnSelected" name="isDefault" value="1" ><label for="checkbox">&nbsp;</label></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group col-sm-12">
                                            <label><?php echo $this->lang->line('common_notes');?> <?php required_mark(); ?></label><!--Notes-->
                                            <textarea class="form-control notes_termsandcond" rows="3" name="description" id="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                            <button class="btn btn-primary" type="button" onclick="saveNotes()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>


            <div aria-hidden="true" role="dialog" id="attribute_assign_modal_edit" class="modal fade"
                 style="display: none;">
                <div class="modal-dialog modal-lg" style="width: 60%;">
                    <div class="modal-content">
                        <div class="color-line"></div>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h5 class="modal-title">Edit Attribute</h5>
                        </div>
                        <div class="modal-body">
                            <form role="form" id="attribute_assign_form_edit" class="form-horizontal">

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                            <button class="btn btn-primary" type="button" onclick="updateAssignedAttributes()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                var Otable;
                $(document).ready(function () {
                    $('.headerclose').click(function () {
                        fetchPage('system/terms_conditions/terms_condition_management', 'Test', 'Terms and conditions');
                    });
                    tinymce.init({
                        selector: ".notes_termsandcond",
                        height: 400,
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


                    terms_and_condition_table();
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-blue',
                        radioClass: 'iradio_square_relative-blue',
                        increaseArea: '20%'
                    });
                });
                //$("#description").wysihtml5();
                function terms_and_condition_table(selectedID=null) {
                    Otable = $('#termsandconditions_table').DataTable({
                        "bProcessing": true,
                        "bServerSide": true,
                        "bDestroy": true,
                        "StateSave": true,
                        "sAjaxSource": "<?php echo site_url('TermsAndConditions/fetch_terms_and_condition'); ?>",
                        "aaSorting": [[5, 'desc']],
                        "columnDefs": [
                            {
                                "targets": [3,4],
                                "searchable": false
                            }
                        ],
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
                                if (parseInt(oSettings.aoData[x]._aData['autoID']) == selectedRowID) {
                                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                                    $(thisRow).addClass('dataTable_selectedTr');
                                }
                                x++;
                            }
                            $('.extraColumns input').iCheck({
                                checkboxClass: 'icheckbox_square_relative-blue',
                                radioClass: 'iradio_square_relative-blue',
                                increaseArea: '20%'
                            });

                            $('input').on('ifChecked', function (event) {
                                change_isDefault(this.value,0);
                            });

                            $('input').on('ifUnchecked', function (event) {
                                change_isDefault(this.value,1);
                            });
                        },
                        "aoColumns": [
                            {"mData": "autoID"},
                            {"mData": "docdescription"},
                            {"mData": "description"},
                            {"mData": "isDefaultChk"},
                            {"mData": "edit"},
                            {"mData": "documentID"}
                        ],
                        "columnDefs": [{"visible":false,"targets": [5] }],
                        "fnServerData": function (sSource, aoData, fnCallback) {
                            aoData.push({"name": "documentID", "value": $("#documentIDfltr").val()});
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

                $('.table-row-select tbody').on('click', 'tr', function () {
                    $('.table-row-select tr').removeClass('dataTable_selectedTr');
                    $(this).toggleClass('dataTable_selectedTr');
                });

                function openNotesModal(){
                    $('#note_modal').modal('show');
                    $('#autoIDhn').val('');
                    // $('#documentID').val('');
                    /*  $('#description ~ iframe').contents().find('.wysihtml5-editor').html('');*/
                    tinyMCE.get("description").setContent('');

                    $('#isDefault').iCheck('uncheck');
                }

                function saveNotes(){
                    var isDefault=0;
                    if ($('#isDefault').is(':checked')){
                        isDefault=1;
                    }
                    tinymce.triggerSave();
                    var data = $("#notes_form").serializeArray();
                    data.push({'name': 'isDefault', 'value': isDefault});
                    $.ajax({
                        async: true,
                        type: 'post',
                        data: data,
                        dataType: "json",
                        url: "<?php echo site_url('TermsAndConditions/save_notes'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0]=='s'){
                                $('#note_modal').modal('hide');
                                Otable.draw();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();

                        }
                    });
                }

                function openNoteEdit(autoID){
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'autoID': autoID},
                        url: "<?php echo site_url('TermsAndConditions/get_notes_edit'); ?>",
                        beforeSend: function () {
                            startLoad();

                        },
                        success: function (data) {
                            stopLoad();
                            $('#autoIDhn').val(autoID);
                            $('#documentID').val(data['documentID']);
                            if(data['isDefault']==1){
                                $('#isDefault').iCheck('check');
                            }else{
                                $('#isDefault').iCheck('uncheck');
                            }

                            tinyMCE.get("description").setContent(data['description']);
                            $('#note_modal').modal('show');

                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();

                        }
                    });
                }



                function delete_notes(autoID) {
                    swal({
                            title: "Are you sure?",
                            text: "You want to delete this record!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Delete"
                        },
                        function () {
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: {'autoID': autoID},
                                url: "<?php echo site_url('TermsAndConditions/delete_notes'); ?>",
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    myAlert(data[0],data[1]);
                                    Otable.draw();
                                }, error: function () {
                                    swal("Cancelled", "Your file is safe :)", "error");
                                }
                            });
                        });
                }

                function change_isDefault(autoID,isdefault){
                    $.ajax({
                        async: true,
                        type: 'post',
                        data: {'autoID': autoID,'isdefault': isdefault},
                        dataType: "json",
                        url: "<?php echo site_url('TermsAndConditions/change_isDefault'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0]=='s'){
                                Otable.draw();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();

                        }
                    });
                }

                function clear_all_filters(){
                    $("#documentIDfltr").val('all')
                    Otable.draw();
                }



            </script>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="standard_detail">
    <div class="modal-dialog modal-lg" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_add_document_setup');?></h4>
            </div>
            <?php echo form_open('', 'role="form" id="document_setup_master_form"'); ?>
            <input class="hidden" name="docSetupID" id="docSetupID">
            <div class="modal-body">
                <div class="row" style="margin-left: 3px">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('common_description');?> :<!--Description--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <input class="form-control" name="description" id="description" placeholder="<?php echo $this->lang->line('common_description');?>.....">
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px; margin-left: 3px">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('common_mandatory');?> :<!--Mandatory--></label>
                    </div>
                    <div class="form-group col-sm-8">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="isMandatory" type="checkbox" class="isMandatory" value="1">
                                <label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px; margin-left: 3px">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('common_active');?> :<!--Active--></label>
                    </div>
                    <div class="form-group col-sm-8">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="IsActive" type="checkbox" class="IsActive" value="1">
                                <label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span><?php echo $this->lang->line('common_save');?><!-- Save--></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_documentsetup', 'Test', '<?php echo $this->lang->line('manufacturing_document_setup'); ?>');
        });
        template();

        $('#document_setup_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required'); ?>.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            if ($("#IsActive").is(':checked')) { IsActive = 1; } else { IsActive = 0; }
            if ($("#isMandatory").is(':checked')) { isMandatory = 1; } else { isMandatory = 0; }
            data.push({name: "IsActive", value: IsActive});
            data.push({name: "isMandatory", value: isMandatory});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Job/save_document_setup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data['status'] == true) {
                        oTable.draw();
                        $('#standard_detail').modal('hide');
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-purple',
        radioClass: 'iradio_square_relative-purple',
        increaseArea: '20%'
    });

    function add_new_document_setup() {
        $('.extraColumns input').iCheck('uncheck');
        $('#document_setup_master_form')[0].reset();
        $('#document_setup_master_form').bootstrapValidator('resetForm', true);
        $('#standard_detail').modal('show');
    }

    function template() {
        oTable = $('#mfq_document_setup').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Job/fetch_document_setup'); ?>",
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
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
            },
            "aoColumns": [
                {"mData": "docSetupID"},
                {"mData": "description"},
                {"mData": "isMandatory"},
                {"mData": "isActive"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0], "searchable": false}],
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

    function delete_document_setup(docSetupID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'docSetupID': docSetupID},
                    url: "<?php echo site_url('MFQ_Job/delete_document_setup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                            refreshNotifications(true);
                            stopLoad();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function edit_document_setup(docSetupID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {docSetupID: docSetupID},
            url: "<?php echo site_url('MFQ_Job/load_document_setup'); ?>",
            beforeSend: function () {
                $('#standard_detail_header').text('<?php echo $this->lang->line('manufacturing_edit_standard_details');?>');
                startLoad();

            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#document_setup_master_form').bootstrapValidator('resetForm', true);
                    $('#docSetupID').val(data['docSetupID']);
                    $('#description').val(data['description']);
                    if (data['isActive'] == 1) {
                        $('#IsActive').iCheck('check');
                    }
                    else if (data['isActive'] == 0) {
                        $('#IsActive').iCheck('uncheck');
                    }
                    if (data['isMandatory'] == 1) {
                        $('#isMandatory').iCheck('check');
                    }
                    else if (data['isMandatory'] == 0) {
                        $('#isMandatory').iCheck('uncheck');
                    }
                    $('#standard_detail').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>