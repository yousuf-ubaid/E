<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title= $this->lang->line('manufacturing_standard_details');
echo head_page($title, false); ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #mfq_standard_details th{
        text-transform: uppercase;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" style="margin-right: 17px;" class="btn btn-primary pull-right"
                onclick="open_mfq_standard_details()"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_add_standard_details');?><!--Add Standard Details-->
        </button>
    </div>
</div>
<hr style="margin-top: 7px;margin-bottom: 7px;">
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="mfq_standard_details" class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 10%"><?php echo $this->lang->line('common_type');?><!--TYPE--></th>
                    <th style="width: 12%"><?php echo $this->lang->line('common_description');?><!--DESCRIPTION--></th>
                    <th style="width: 5%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="standard_detail">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="standard_detail_header"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="standard_detail_master_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_type');?><!--Type--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('type', array('' => 'Select Type', '1' => 'Terms and Conditions', '2' => 'Warranty', '3' => 'Validity'), '', 'class="form-control" id="type"'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <input type="hidden" id="mfqStandardDetailMasterID" name="mfqStandardDetailMasterID">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                    </div>
                    <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <textarea class="form-control richtext" id="description"
                              name="description"
                              rows="2"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span><?php echo $this->lang->line('common_save');?><!-- Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_standard_details', 'Test', 'Standard Details');
        });
        template();

        $('#standard_detail_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //description: {validators: {notEmpty: {message: 'Description is required.'}}},
                type: {validators: {notEmpty: {message: 'Type is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('mfq_standard_details/save_mfq_standard_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        template();
                        $('#standard_detail').modal('hide');
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

        tinymce.init({
            selector: ".richtext",
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

    });

    function open_mfq_standard_details() {
        $('#standard_detail_header').text('<?php echo $this->lang->line('manufacturing_add_standard_details');?>');
        $('#standard_detail_master_form')[0].reset();
        $('#standard_detail_master_form').bootstrapValidator('resetForm', true);
        $('#standard_detail').modal('show');
    }

    function template() {
        oTable = $('#mfq_standard_details').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('mfq_standard_details/fetch_standard_details'); ?>",
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
                {"mData": "mfqStandardDetailMasterID"},
                {"mData": "typeID"},
                {"mData": "Description"},
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

    function delete_standard_details(mfqStandardDetailMasterID) {
        swal({
                title: "Are you sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mfqStandardDetailMasterID': mfqStandardDetailMasterID},
                    url: "<?php echo site_url('mfq_standard_details/delete_standard_details'); ?>",
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

    function edit_standard_details(mfqStandardDetailMasterID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {mfqStandardDetailMasterID: mfqStandardDetailMasterID},
            url: "<?php echo site_url('mfq_standard_details/load_mfq_standard_details'); ?>",
            beforeSend: function () {
                $('#standard_detail_header').text('<?php echo $this->lang->line('manufacturing_edit_standard_details');?>');
                startLoad();

            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#standard_detail_master_form').bootstrapValidator('resetForm', true);
                    $('#mfqStandardDetailMasterID').val(data['mfqStandardDetailMasterID']);
                    //$('#description').val(data['Description']);
                    $('#type').val(data['typeID']).change();
                    setTimeout(function () {
                        tinyMCE.get("description").setContent(data['Description']);
                    }, 1000);
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