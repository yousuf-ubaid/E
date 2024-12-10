<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<!--<script src="<?php /*echo base_url('plugins/html5sortable/jquery.sortable.js'); */ ?>"></script>-->
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .affix-content .container .page-header {
        margin-top: 0;
    }

    .affix-sidebar {
        padding-right: 0;
        font-size: small;
        padding-left: 0;
    }

    .affix-row, .affix-container, .affix-content {
        height: 100%;
        overflow: scroll;
        margin-left: 0;
        margin-right: 0;
    }

    .affix-content {
        background-color: white;
    }

    .sidebar-nav .navbar .navbar-collapse {
        padding: 0;
        max-height: none;
    }

    .sidebar-nav .navbar {
        border-radius: 0;
        margin-bottom: 0;
        border: 0;
    }

    .sidebar-nav .navbar ul {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li a {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    }

    @media (min-width: 769px) {
        .affix-content .container {
            width: 600px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 992px) {
        .affix-content .container {
            width: 900px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 1220px) {
        .affix-row {
            overflow: hidden;
        }

        .affix-content {
            overflow: auto;
        }

        .affix-content .container {
            width: 1000px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }

        .affix-content {
            padding-right: 30px;
            padding-left: 10px;
        }

        .affix-title {
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .navbar-nav {
            margin: 0;
        }

        .navbar-collapse {
            padding: 0;
        }

        .sidebar-nav .navbar li a:hover {
            background-color: #428bca;
            color: white;
        }

        .sidebar-nav .navbar li a > .caret {
            margin-top: 8px;
        }
    }

    .sidebar {
        padding-bottom: 0px;
    }

    div.bhoechie-tab-container {
        background-color: #ffffff;
        padding: 0 !important;
        border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ddd;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        -moz-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        background-clip: padding-box;
        opacity: 0.97;
        filter: alpha(opacity=97);
    }

    div.bhoechie-tab-menu {
        padding-right: 0;
        padding-left: 0;
        padding-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon,
    div.bhoechie-tab-menu div.list-group > a .fa {
        color: #E78800;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #555;
        border: 2px solid #555;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a:first-child {
        border-top-right-radius: 0;
        -moz-border-top-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a:last-child {
        border-bottom-right-radius: 0;
        -moz-border-bottom-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a.active,
    div.bhoechie-tab-menu div.list-group > a.active .glyphicon,
    div.bhoechie-tab-menu div.list-group > a.active .fa {
        background-color: #E78800;
        color: #ffffff;
    }

    div.bhoechie-tab-menu div.list-group > a.active .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #ffffff;
        border: 2px solid #ffffff;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a.active:after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        margin-top: -13px;
        border-left: 0;
        border-bottom: 13px solid transparent;
        border-top: 13px solid transparent;
        border-left: 10px solid #E78800;
    }

    div.bhoechie-tab-content {
        background-color: #ffffff;
        /* border: 1px solid #eeeeee; */
        padding-left: 20px;
        padding-top: 10px;
    }

    div.bhoechie-tab div.bhoechie-tab-content:not(.active) {
        display: none;
    }

    .list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
        border: 1px solid #ddd;
    }

    .bhoechie-tab {
        border: solid 2px #E78800;
        margin-left: -2px;
        margin-top: 1px;
        margin-bottom: 1px;
        min-height: 300px;
    }

    .disabledbutton {
        pointer-events: none;
    }

</style>

<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('manufacturing_step_1').''.$this->lang->line('manufacturing_workflow_header');?><!--Step 1 - Workflow Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="workflow_detail()" data-toggle="tab">
        <?php echo $this->lang->line('manufacturing_step_2').''.$this->lang->line('manufacturing_workflow_configuration');?><!--2 - Workflow Configuration--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="template_master_form"'); ?>
        <input type="hidden" name="templateMasterID" id="templateMasterID">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 10px;">
                        <label class="title"><?php echo $this->lang->line('common_description') ?><!--Description--></label>
                    </div>
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                <span class="input-req" title="Required Field"><input type="text" name="description" id="description"
                                                                      class="form-control" placeholder="<?php echo $this->lang->line('common_description') ?>" required><span
                            class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_industry'); ?><!--Industry--></label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req"
                      title="Required Field"><?php echo form_dropdown('industryID', get_all_mfq_industry(), '', 'class="form-control" id="industryID"  required'); ?>
                    <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_is_default'); ?><!--Is Default--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="isDefault" type="checkbox"
                                                                          class="isDefault" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="text-right m-t-xs">
                        <div class="form-group col-sm-7" style="margin-top: 10px;">
                            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_workflow_detail'); ?><!--WORKFLOW DETAIL--></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <div class="append_data">
                            <?php echo form_open('', 'role="form" id="template_detail_form"'); ?>
                            <input type="hidden" name="templateMasterID" id="templateMasterID2">
                            <input type="hidden" name="templateDetailID" id="templateDetailID">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <?php
                                    $workflowID = get_all_workflow_template();
                                    $workflowID = array_group_by($workflowID, 'workFlowDescription');
                                    echo ' <select id="workFlowTemplateID" name="workFlowTemplateID" class="form-control select2">';
                                    foreach ($workflowID as $label => $opt) { ?>
                                        <option value=""></option>
                                        <optgroup label="<?php echo $label; ?>">
                                            <?php foreach ($opt as $id => $name) { ?>
                                                <option value="<?php echo $name["workFlowTemplateID"]; ?>|<?php echo $name["workFlowID"]; ?>"><?php echo $name["description"]; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                        <?php
                                    }
                                    echo '</select>'
                                    ?>
                                </div>
                                <div class="form-group col-sm-4" style="padding-left: 0px;">
                                    <input type="text" class="form-control f_search" name="description"
                                           id="f_search_1"
                                           placeholder="<?php echo $this->lang->line('common_description'); ?>">
                                </div>
                                <div class="form-group col-sm-2">
                                    <?php echo form_dropdown('sortorder', array('' => 'Select Order', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8'), '', 'class="form-control" id="sortorder"'); ?>
                                </div>

                                <div class="form-group col-sm-2">
                                    <button type="submit" class="btn btn-primary btn-small "><i
                                                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
                                    </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_workflow_design'); ?><!--WORKFLOW DESIGN--></h2>
                </header>
                <div id="workflow-design">
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); ?>"></script>
<script type="text/javascript">

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_template', '', 'Workflow');
        });

        $('#workFlowTemplateID').select2({
            placeholder: "Select a Workflow",
            allowClear: true
        });

        templateMasterID = null;

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            templateMasterID = p_id;
            load_template_master_header();
            load_workflow_design();
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#template_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                industryID: {validators: {notEmpty: {message: 'Industry is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            var isDefault;
            if($("#isDefault").is(':checked')){
                isDefault = 1;
            }else{
                isDefault = 0;
            }
            data.push({name:"isDefault",value:isDefault});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Template/save_mfq_template_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        templateMasterID = data[2];
                        $('#templateMasterID').val(templateMasterID);
                        $('#templateMasterID2').val(templateMasterID);
                        $('.btn-wizard').removeClass('disabled');
                        $('[href=#step2]').tab('show');
                        $(document).scrollTop(0);
                        //fetchPage('system/crm/task_management', '', 'Task');
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

        $('#template_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                workFlowTemplateID: {validators: {notEmpty: {message: 'Workflow is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                sortorder: {validators: {notEmpty: {message: 'Sortorder is required.'}}},
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
                url: "<?php echo site_url('MFQ_Template/save_mfq_template_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1],data[2]);


                    if (data[0] == 's') {
                        workflow_detail();
                        load_workflow_design();
                        save_configuration_workflow(templateMasterID,data[2])


                        $(document).scrollTop(0);
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

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-purple',
            radioClass: 'iradio_square_relative-purple',
            increaseArea: '20%'
        });
    });

    function load_template_master_header() {
        if (templateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {templateMasterID: templateMasterID},
                url: "<?php echo site_url('MFQ_Template/load_template_master_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        templateMasterID = data['templateMasterID'];
                        $('#templateMasterID').val(templateMasterID);
                        $('#templateMasterID2').val(templateMasterID);
                        $('#description').val(data['templateDescription']);
                        $('#industryID').val(data['industryID']).change();
                        if(data['isDefault'] == 1){
                            $("#isDefault").iCheck('check');
                        }
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
    }

    function load_workflow_design() {
        if (templateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {templateMasterID: templateMasterID, type: 1},
                url: "<?php echo site_url('MFQ_Template/load_workflow_design'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#workflow-design').html(data);
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
    }

    function get_workflow_template(pageNameLink, tabID, workFlowID, documentID, type, templateDetailID, linkworkFlow) {
        if (templateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    templateMasterID: templateMasterID,
                    pageNameLink: pageNameLink,
                    tabID: tabID,
                    workFlowID: workFlowID,
                    documentID: documentID,
                    type: type,
                    templateDetailID: templateDetailID,
                    linkworkFlow: linkworkFlow
                },
                url: "<?php echo site_url('MFQ_Template/get_workflow_template'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#workflow_template').addClass("active");
                    if (!jQuery.isEmptyObject(data)) {
                        $('#' + tabID).html(data);
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
    }


    function delete_workflow_detail(templateMasterID, templateDetailID) {
        if (templateMasterID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this!",
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
                        data: {'templateMasterID': templateMasterID, 'templateDetailID': templateDetailID},
                        url: "<?php echo site_url('MFQ_Template/delete_workflow_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                load_workflow_design();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function workflow_detail() {
        $('#template_detail_form')[0].reset();
        $('#template_detail_form').bootstrapValidator('resetForm', true);
        $('#workFlowTemplateID').val('').change();
    }
    function save_configuration_workflow(masterid,detailid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'templateMasterID': masterid, 'templateDetailID': detailid},
            url: "<?php echo site_url('MFQ_Template/check_link_job_card'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    save_work_flow_configuration(masterid,detailid);
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function save_work_flow_configuration(masterid,detailid)
    {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'templateMasterID': masterid,templateDetailID:detailid,typestart:1},
            url: "<?php echo site_url('MFQ_Template/link_workflow'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>