<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_segment');
echo head_page($title, true);
$user_group = all_group_drop(true, 1, 1);

/*echo head_page('Segment', false);*/
$usergroup_assign = getPolicyValues('UGSE', 'All');
$masterIDOptions = load_segment_masterID_options();
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<style>
    .select2-selection__choice {
        background-color: #696CFF !important;
        padding: 5px 10px !important;
        font-size: 12px;
        border-radius: 4px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="col-sm-3">
            <label for="filter_masterID">Master Segment</label>
            <?php echo form_dropdown('filter_masterID[]', $masterIDOptions, '', "class='form-control' id='filter_masterID' onchange='segmentview()' multiple='multiple'"); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear'); ?>
            </button><!--Clear-->
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" data-toggle="modal" onclick="resetfrm()"
            data-target="#segment_model"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new'); ?><!--Create New-->
        </button>
    </div>
</div>
<hr>


<!-- <div class="table-responsive">
    <table id="segment_table" class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <th style="min-width: 10%">#</th> 
                <th><?php echo $this->lang->line('finance_ms_segment_code'); ?></th>
                <th><?php echo $this->lang->line('common_description'); ?></th>
                <th><?php echo $this->lang->line('common_master_segment'); ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_is_default'); ?> </th>
                <th style="min-width: 7%"><?php echo $this->lang->line('common_action'); ?></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th>
            </tr>
        </thead>
        <tbody id="segmentBody">

        </tbody>
    </table>
</div> -->
<div id="segment_tableDiv">
    <!--Segment Table-->
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="segment_model" class=" modal fade bs-example-modal-lg"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="segmentHead"></h5>
            </div>
            <form role="form" id="segment_form" class="form-horizontal">
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group col-sm-4" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                            <textarea class="form-control" id="description" name="description" style="width:255px;"
                                rows="2"></textarea>
                            <input type="hidden" class="form-control" id="segmentID" name="segmentID">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_segment_code'); ?><!--Segment Code--></label>
                            <input type="text" class="form-control" id="segmentcode" name="segmentcode">
                        </div>
                    </div>
                    <?php if ($usergroup_assign == 1)
                    { ?>

                        <div class="row">
                            <div class="form-group col-sm-6" style="margin-left: 0px;">
                                <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_user_group'); ?><!--Segment Code--></label><br>
                                <?php echo form_dropdown('user_group[]', $user_group, null, 'class="form-control user_group" id="user_group_multi"  multiple="multiple"'); ?>
                            </div>
                        </div>

                    <?php } ?>
                    <div class="row" id="masterSegment_ID">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('common_master_segment'); ?></label>
                            <?php echo form_dropdown('masterSegmentID', $masterIDOptions, '', "class='form-control select2' id='masterSegmentID' style='width:255px;'
                                rows='2'"); ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <input type="checkbox" class="form-check-input" id="isShow" name="isShow" checked value="1">
                            <label for="isShow"><?php echo $this->lang->line('finance_ms_is_show'); ?><!--Segment Code--></label>
                        </div>
                    </div>

                    <div class="row" id="subSegment">
                        <div class="form-group col-sm-12" style="margin-left: 0px;">
                            <label for="subsegment"><?php echo $this->lang->line('finance_ms_sub_segment'); ?><!--Sub Segment--></label>
                            <button type="button" class="btn btn-primary-new pull-right" onclick="openSubgSegment()"><i class="fa fa-plus"></i>
                            </button>

                        </div>

                        <div class="table-responsive" style="width: 90%; margin-left:20px;">
                            <table class="table table-striped table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('finance_ms_segment_code'); ?><!--File Name--></th>
                                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                    </tr>
                                </thead>
                                <tbody id="subTbody" class="no-padding">
                                    <tr class="danger">
                                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?><!--No record Found--></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save'); ?><!--Save--> <span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="sub_segment_model" class="modal fade bs-example-modal-lg" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="subSegmentHead"></h5>
            </div>
            <form role="form" id="sub_segment_form" class="form-horizontal">
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group col-sm-4" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('common_description'); ?></label>
                            <textarea class="form-control" id="sub_description" name="sub_description" style="width:255px;" rows="2"></textarea>
                            <input type="hidden" class="form-control" id="subsegmentID" name="subsegmentID">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_segment_code'); ?></label>
                            <input type="text" class="form-control" id="sub_segmentcode" name="sub_segmentcode">
                        </div>
                    </div>
                    <?php if ($usergroup_assign == 1)
                    { ?>
                        <div class="row">
                            <div class="form-group col-sm-6" style="margin-left: 0px;">
                                <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_user_group'); ?></label><br>
                                <?php echo form_dropdown('user_group[]', $user_group, null, 'class="form-control user_group" id="sub_user_group_multi" multiple="multiple"'); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <input type="checkbox" class="form-check-input" id="sub_Show" name="sub_Show" checked value="1">
                            <label for="sub_Show"><?php echo $this->lang->line('finance_ms_is_show'); ?><!--Segment Code--></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save'); ?> <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button onclick="resetSubModel()" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        segmentview();
        $("#filter_masterID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#user_group_multi").select2();
        $("#sub_user_group_multi").select2();
        $('#subSegment').hide();
        $('#masterSegment_ID').hide();

        $('#masterSegmentID ').prop('disabled', true);

        $('.headerclose').click(function() {
            fetchPage('system/erp_srp_segment_view', '', 'Segment');
        });
        $('.select2').select2();

        // Event listener for filter change
        // $('#filter_masterID').on('change', function() {
        //     segmentview(); // Trigger segment view update on filter change
        // });
        $('#segment_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                segmentcode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_ms_segment_code_is_required'); ?>' /*Segment Code is required*/
                        },
                        stringLength: {
                            max: 10,
                            message: '<?php echo $this->lang->line('finance_ms_character_must_be'); ?>' /*Character must be below 6 character*/
                        }

                    }
                },
                description: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('common_description_is_required'); ?>.'
                        }
                    }
                },
                /*Description is required*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Segment/save_segment'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $("#segment_model").modal("hide");
                        segmentview();
                        $('#subSegment').hide();
                        $('#masterSegment_ID').hide();


                        $('#masterSegmentID ').prop('disabled', true);


                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('#sub_segment_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            excluded: [':disabled'],
            fields: {
                sub_segmentcode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_ms_segment_code_is_required'); ?>'
                        },
                        stringLength: {
                            max: 10,
                            message: '<?php echo $this->lang->line('finance_ms_character_must_be'); ?>'
                        }
                    }
                },
                sub_description: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('common_description_is_required'); ?>'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
            e.preventDefault();

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            if ($('#sub_Show').prop('checked')) {
                data.push({
                    name: 'sub_Show',
                    value: '1'
                });
            } else {
                data.push({
                    name: 'sub_Show',
                    value: '0'
                });
            }

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Segment/save_sub_segment'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        getSubSegment();
                        resetSubModel();
                        $('#sub_segment_form').bootstrapValidator('resetForm', true);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });


    function segmentview() {
        // Get selected filter values
        var selectedFilters = $("#filter_masterID").val();

        // Check if there are any filters selected
        var filterData = selectedFilters ? {
            filter_masterID: selectedFilters.join()
        } : {};

        $.ajax({
            url: "<?php echo site_url('Segment/load_segment_table'); ?>",
            type: 'POST',
            dataType: 'html',
            data: filterData,
            beforeSend: function() {},
            success: function(data) {
                $('#segment_tableDiv').html(data); // Update the segment table view
                $("[rel=tooltip]").tooltip(); // Reinitialize tooltips
            },
            error: function() {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
            }
        });
    }

    function clear_all_filters() {
        $('#filter_masterID').multiselect2('deselectAll', false);
        $('#filter_masterID').multiselect2('updateButtonText');
        segmentview(); // Reload all data without filters
    }

    function edit_segmrnt(id) {
        $('#segment_form').bootstrapValidator('resetForm', true);
        $("#segment_model").modal("show");

        $('#segmentID').val(id);
        $('#segmentHead').html('<?php echo $this->lang->line('finance_edit_segment') ?>'); /*Edit Segment*/
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                segmentID: id
            },
            url: "<?php echo site_url('Segment/edit_segment'); ?>",
            success: function(data) {
                $('#user_group_multi').val(data['user_groups']).change();



                if (data['masterID'] != null && data['masterID'] != 0) {
                    $('#masterSegmentID').val(data['masterID']).change();
                    $('#masterSegmentID').prop('disabled', false);
                } else {
                    $('#masterSegmentID').prop('disabled', true);
                }


                $('#description').val(data['description']);
                $('#segmentcode').val(data['segmentCode']);

                if (data['isShow'] == 1) {
                    $('#isShow').prop('checked', true);
                } else {
                    $('#isShow').prop('checked', false);
                }
                showSubSegment();
                getSubSegment();
                resetSubModel();

            },
            error: function() {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
            }
        });
    }

    function changesegmentsatus(id) {
        var compchecked = 0;
        if ($('#statusactivate_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    segmentID: id,
                    chkedvalue: compchecked
                },
                url: "<?php echo site_url('Segment/update_segmentstatus'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        segmentview();
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        } else if (!$('#statusactivate_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    segmentID: id,
                    chkedvalue: 0
                },
                url: "<?php echo site_url('Segment/update_segmentstatus'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        segmentview();
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function resetfrm() {
        $('#segmentID').val('');
        $('#segmentHead').html('<?php echo $this->lang->line('finance_ms_add_new_segment'); ?>'); /*Add New Segment*/
        $('#segment_form')[0].reset();
        $('#segment_form').bootstrapValidator('resetForm', true);
        $("#segment_model").modal("hide");

        $('#subSegment').hide();
        $('#masterSegment_ID').hide();
        $('#masterSegmentID ').prop('disabled', true);

        resetSubModel();
    }


    /* Function added */
    function setDefaultWarehouse(thisID, segmentID) {
        var checked = 0;
        if ($(thisID).is(':checked')) {
            checked = 1;
        } else {
            checked = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                chkdVal: checked,
                segmentID: segmentID
            },
            url: "<?php echo site_url('Segment/setDefaultsegment'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] = 's') {
                    segmentview();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    /* End  Function */

    function showSubSegment() {
        var segmentID = $('#segmentID').val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                segmentID: segmentID
            },
            url: "<?php echo site_url('Segment/checkForSubSegment'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                if (!data || data.masterID === null || data.masterID == 0) {

                    $('#subSegment').show();



                    $('#masterSegment_ID').hide();
                    $('#masterSegmentID').prop('disabled', true);


                } else {

                    $('#masterSegment_ID').show();
                    $('#subSegment').hide();
                    $('#masterSegmentID').prop('disabled', false);



                }
            },
            error: function() {
                myAlert('e', 'Something went wrong');
            }
        });
    }


    function getSubSegment() {
        var segmentID = $('#segmentID').val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                segmentID: segmentID
            },
            url: "<?php echo site_url('Segment/getSubSegment'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $('#subTbody').empty();

                if (data.length === 0) {
                    var row = `
                        <tr class="danger">
                            <td colspan="4" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?></td>
                        </tr>
                    `;
                    $('#subTbody').append(row);
                } else {
                    $.each(data, function(index, item) {
                        var row = `
                            <tr>
                                <td>${index + 1}</td>  <!-- # Numbering -->
                                <td>${item.segmentCode}</td>  <!-- Segment Code -->
                                <td>${item.description}</td>  <!-- Description -->
                            </tr>
                        `;
                        $('#subTbody').append(row);
                    });
                }
            },
            error: function() {
                myAlert('e', 'Something went wrong');
            }
        });
    }

    function openSubgSegment() {
        var segmentID = $('#segmentID').val();
        $('#sub_segment_model').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#subsegmentID').val(segmentID);
    }

    function resetSubModel() {
        $('#sub_segment_model').modal("hide");
        $('#sub_segment_model input, #sub_segment_model select, #sub_segment_model textarea').val('');
    }
</script>