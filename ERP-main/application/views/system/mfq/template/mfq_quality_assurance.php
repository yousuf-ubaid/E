<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$disablebutton = "";
if ($type == 1) {
    $disablebutton = "disabledbutton";
}
?>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .arrow-steps .step.current {
        color: #fff !important;
        background-color: #657e5f !important;
    }
</style>
<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#qualityassurance" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_quality_assurance') ?><!--QA-->
        </a></li>
    <li><a href="#crew_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_crew') ?><!--Crew--> </a></li>
    <li><a href="#machine_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_machine') ?><!--Machine--> </a>
    </li>
    <li><a href="#attachment_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_attachment') ?><!--Attachment-->
        </a></li>
    <li><a href="#review_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_review_or_print') ?><!--Review/Print-->
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="qualityassurance">
        <?php if ($type == 1) { ?>
            <div class="row">
                <br>
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_quality_assurance_criteria') ?><!--QUALITY ASSURANCE CRITERIA--></h2>
                    </header>
                    <form action="" role="form" id="mfq_qa_criteria_form"
                          method="post">
                        <input type="hidden" name="workFlowID" value="<?php echo $workFlowID ?>">
                        <input type="hidden" name="workProcessID" value="<?php echo $workProcessID ?>">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <input type="text" class="form-control" name="description" id=""
                                       placeholder="<?php echo $this->lang->line('common_description') ?>">
                            </div>
                            <div class="form-group col-sm-3" style="padding-left: 0px;">
                                <select name="inputType" class="form-control" id="inputType">
                                    <option value=""><?php echo $this->lang->line('common_select_type') ?><!--Select Type--></option>
                                    <option value="text">Text</option>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <select name="sortOrder" class="form-control" id="sortOrder">
                                    <option value=""><?php echo $this->lang->line('manufacturing_select_order') ?><!--Select Order--></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-2">
                                <button type="submit" class="btn btn-primary btn-small "><i
                                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <br>
        <div class="row <?php echo $disablebutton ?>">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_specification') ?><!--Specification--></h2>
                </header>
                <form action="" role="form" id="mfq_qa_criteria_form"
                      method="post">
                    <input type="hidden" name="workProcessID" id="workProcessID"
                           value="<?php echo $workProcessID ?>">
                    <input type="hidden" name="workFlowID" id="workFlowID" value="<?php echo $workFlowID ?>">
                    <div class="row">
                        <div class="col-sm-12" id="qa_specification">

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
    </div>
    <div class="tab-pane  <?php echo $disablebutton ?>" id="crew_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_crew') ?><!--Crew--></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_crew_<?php echo $documentID ?>">
                            <input type="hidden" name="workProcessID" id="workProcessID_<?php echo $documentID ?>"
                                   value="<?php echo $workProcessID ?>">
                            <input type="hidden" name="workFlowID" id="workFlowID_<?php echo $documentID ?>"
                                   value="<?php echo $workFlowID ?>">
                            <div class="table-responsive">
                                <table id="mfq_crew_<?php echo $documentID ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_name') ?><!--Name--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_designation') ?><!--Designation--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_gender') ?><!--Gender--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_telephone') ?><!--Telephone--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_email') ?><!--Email--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add" id="btnAdd"
                                                        onclick="add_more_crew('<?php echo $documentID ?>')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="crew_body_<?php echo $documentID ?>">
                                    <tr>
                                        <td>
                                            <input type="text" onkeyup="clearitemAutoID(event,this)"
                                                   class="form-control c_search"
                                                   name="search[]"
                                                   placeholder="<?php echo $this->lang->line('common_crew') ?>" id="c_search_<?php echo $documentID ?>_1">
                                            <input type="hidden" class="form-control crewID" name="crewID[]">
                                            <input type="hidden" class="form-control workProcessCrewID"
                                                   name="workProcessCrewID[]">
                                        </td>
                                        <td><input type="text" name="designation" class="form-control designation"
                                                   readonly>
                                        </td>
                                        <td><input type="text" name="gender" class="form-control gender" readonly></td>
                                        <td><input type="text" name="telephone" class="form-control telephone" readonly>
                                        </td>
                                        <td><input type="text" name="email" class="form-control email" readonly></td>
                                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary-new size-lg" type="button"
                                                onclick="save_crew('<?php echo $documentID ?>',<?php echo $workFlowID ?>)">
                                            <?php echo $this->lang->line('common_save') ?><!--Save-->
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane  <?php echo $disablebutton ?>" id="machine_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_machine') ?><!--Machine--></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_machine_<?php echo $documentID ?>">
                            <input type="hidden" name="workProcessID" id="workProcessID_<?php echo $documentID ?>"
                                   value="<?php echo $workProcessID ?>">
                            <input type="hidden" name="workFlowID" id="workFlowID_<?php echo $documentID ?>_1"
                                   value="<?php echo $workFlowID ?>">
                            <div class="table-responsive">
                                <table id="mfq_machine_<?php echo $documentID ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_machine') ?><!--Machine--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_machine_category') ?><!--Main Cat--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_sub_category') ?><!--Sub Cat--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_sub_sub_category') ?><!--Sub Sub Cat--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_asset_code') ?><!--Asset Code--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_part_no') ?><!--Part No--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_description') ?><!--Description--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add" id="btnAdd"
                                                        onclick="add_more_machine('<?php echo $documentID ?>')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="machine_body_<?php echo $documentID ?>">
                                    <tr>
                                        <td>
                                            <input type="text" onkeyup="clearitemAutoID(event,this)"
                                                   class="form-control m_search"
                                                   name="search[]"
                                                   placeholder="<?php echo $this->lang->line('manufacturing_machine') ?>" id="m_search_<?php echo $documentID ?>_1">
                                            <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]">
                                            <input type="hidden" class="form-control workProcessMachineID"
                                                   name="workProcessMachineID[]">
                                        </td>
                                        <td><input type="text" name="faCat" id="faCat" class="form-control faCat"
                                                   readonly>
                                        </td>
                                        <td><input type="text" name="faSubCat" id="faSubCat"
                                                   class="form-control faSubCat"
                                                   readonly></td>
                                        <td><input type="text" name="faSubSubCat" id="faSubSubCat"
                                                   class="form-control faSubSubCat" readonly></td>
                                        <td><input type="text" name="faCode" id="faCode" class="form-control faCode"
                                                   readonly></td>
                                        <td><input type="text" name="partNumber" id="partNumber"
                                                   class="form-control partNumber" readonly></td>
                                        <td><input type="text" name="assetDescription" id="assetDescription"
                                                   class="form-control assetDescription" readonly></td>
                                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary-new size-lg" type="button"
                                                onclick="save_machine('<?php echo $documentID ?>')"><?php echo $this->lang->line('common_save') ?><!--Save-->
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane  <?php echo $disablebutton ?>" id="attachment_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_attachment') ?><!--Attachment--></h2>
                </header>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="attachment_uplode_form_' . $documentID . '" class="form-inline"'); ?>
                    <input type="hidden" name="workProcessID" id="workProcessID_<?php echo $documentID ?>"
                           value="<?php echo $workProcessID ?>">
                    <input type="hidden" name="workFlowID" id="workFlowID_<?php echo $documentID ?>"
                           value="<?php echo $workFlowID ?>">
                    <input type="hidden" name="documentID" id="documentID_<?php echo $documentID ?>"
                           value="<?php echo $documentID ?>">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control"
                                       name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description') ?>" style="width: 240%;">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                               aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default"
                                    onclick="workflow_document_uplode('<?php echo $documentID; ?>',<?php echo $workProcessID ?>,<?php echo $workFlowID ?>)"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" id="show_all_attachments_<?php echo $documentID; ?>">
                        <header class="infoarea">
                            <div class="search-no-results"><?php echo $this->lang->line('common_no_attachment_found') ?><!--NO ATTACHMENT FOUND-->
                            </div>
                        </header>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var itemAutoID;
    var currency_decimal;
    <?php if($type == 2){?>
    load_workprocess_crew('<?php echo $documentID ?>',<?php echo $workFlowID ?>);
    load_workprocess_machine('<?php echo $documentID ?>',<?php echo $workFlowID ?>);
    load_attachments('<?php echo $documentID ?>',<?php echo $workProcessID ?>,<?php echo $workFlowID ?>);
    <?php } ?>
    $(".select2").select2();

    $('#mfq_qa_criteria_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            templateMasterID: {validators: {notEmpty: {message: 'Template is required.'}}},
            workFlowID: {validators: {notEmpty: {message: 'Workflow is required.'}}},
            description: {validators: {notEmpty: {message: 'Description is required.'}}},
            inputType: {validators: {notEmpty: {message: 'Type is required.'}}},
            sortOrder: {validators: {notEmpty: {message: 'Sortorder is required.'}}},
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
            url: "<?php echo site_url('MFQ_Template/save_mfq_qa_criteria'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    workflow_detail();
                    $('#mfq_qa_criteria_form')[0].reset();
                    $('#mfq_qa_criteria_form').bootstrapValidator('resetForm', true);
                    load_specification(<?php echo $templateMasterID ?>,<?php echo $workFlowID ?>);
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

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
    load_specification(<?php echo $templateMasterID ?>,<?php echo $workFlowID ?>);

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_specification(templateMasterID, workFlowID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {templateMasterID: templateMasterID, workFlowID: workFlowID},
            url: "<?php echo site_url('MFQ_Template/load_qa_specification'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#qa_specification').html(data);
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>




