<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = "Reporting Structure";
echo head_page($title, false); 

include_once(APPPATH . 'helpers/report_helper.php');
$system_arr = reporting_structure_system_types();

?>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/tree.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">

<style>
    #displayText {
    color: rgb(102, 102, 255);
    }
    #sub_des{
        color: rgb(102, 102, 255);
        font-size: 10px; 
        text-align: right;
        /*font-weight: bold;*/
        padding: 0 0 0 40px;
    }

    #sub_parentCategory{
        color: rgb(255, 125, 0);
    }
    #describe_parentCategory{
        color: rgb(255, 125, 0);  
    }
    .add_head{
        /*color: rgb(102, 102, 255);*/
        /*font-size: 10px; 
        text-align: center;*/
        font-weight: bold;
        /*padding: 0 0 0 30px;*/
    }
    .tooltip-inner {
        max-width: 150px;
        font-size: 10px;
        padding: 4px 8px;
    }

    .cat::before {
        display: none;
    }
    
    .test-cat {
    cursor: pointer;
    user-select: none; /* Prevent text selection */
    }

    /* Create the caret/arrow with a unicode, and style it */
    .test-cat::before {
    content: "\25B6";
    color: #202020;
    display: inline-block;
    margin-right: 6px;
    }
    /* Rotate the caret/arrow icon when clicked on (using JavaScript) */
    .caret-down::before {
    transform: rotate(90deg);
    color: #990099;
    }
    /* Hide the nested list */
    .neted {
    display: none;
    width:auto !important;
    }
    .active {
    display: block;
    }

    #myUL{
        width:auto;
    }

    #info{
        list-style-type: none;
        width:auto;
    }
    #li_name{
        font-weight:600;
        min-width:500px;
        max-width:auto;
    }
    a{
        color:#202020;
    }

    table{
        width:auto;
    }

    tr{
        width:auto;
    }
    td{
        max-width:auto ;
    }
    #td_id{
        min-width:20px !important;
    }
    #td_name{
        min-width:400px !important;
    }
    #td_icon{
        min-width:200px !important;
    }

    #td_width2{
        min-width:500px;
    }
    .myUL_li{
        width:auto;
    }
    #myUL_li_div_id{
        min-width:616px;
        max-width:auto;
    }
    

</style>

<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">Reporting Structure</span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="load_activityCode_view();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">Activity Code<!--Step 2 - Activity Code--></span>
            </a>
           
        </div>

</div>
<hr>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="row">
            <div class="form-group col-sm-12">
                <button type="button" onclick="add_master_report_Modal()" class="btn btn-primary btn-sm pull-right"><i
                            class="fa fa-plus"></i>
                    <?php echo $this->lang->line('common_add') ?><!--Add-->
                </button>
            </div>
        </div>
        <div class="row">
            <div class="treeContainer" style="min-height: 700px; overflow-x: auto;"><!--via ajax --></div>
        </div>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div id="activity_code_view" ></div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function () {
        load_report();
        $('[data-toggle="tooltip"]').tooltip(); 
    });

    $('#edit_system_type').select2();


    //load activity code view
    function load_activityCode_view() {
        $('[href=#step3]').tab('show');
        $('.btn-wizard').removeClass('disabled');
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {categoryType: 1},
        url: "<?php echo site_url('Report/load_activity_code'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $('#activity_code_view').html('');
            $('#activity_code_view').html(data);
        },
        error: function () {
            stopLoad();
            myAlert('e', 'An Error Occurred! Please Try Again.');
            refreshNotifications(true);
        }
    });
}



//ok
    function load_report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Report/load_report'); ?>",
            data: {categoryType: 1},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $(".treeContainer").html('');
                $(".treeContainer").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

//ok
    function save_master_report() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Report/save_report'); ?>",
            data: $("#master_report_form").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#master_report_form")[0].reset();
                    //$('#structure_id').val(data.last_structure_id)
                    myAlert('s', data.message);
                    load_report();
                    $('#master_Report_Modal').modal('hide');
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

//ok
    function update_report() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Report/update_report'); ?>",
            data: $("#edit_report_form").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    myAlert('s', data.message);
                    load_report();
                    $('#itemCategoryeditModal').modal('hide');
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

//ok
    function save_children_report() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $("#add_report_children_form").serialize(),
            url: "<?php echo site_url('Report/save_report'); ?>",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#add_report_children_form")[0].reset();
                    myAlert('s', data.message);
                    load_report();
                    $("#itemCategoryAddModal").modal('hide');
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }
    
//ok
    function save_description() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Report/save_report_describe'); ?>",
            data: $("#report_describe_form").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#report_describe_form")[0].reset();
                    myAlert('s', data.message);
                    load_report();
                    $('#report_describe_Modal').modal('hide');
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

//ok
    function add_master_report_Modal() {
        $("#master_Report_Modal").modal('show');
        $("#master_parentCategory").html('');
        $("#master_masterID").val('');
        $("#master_levelNo").val('');
        setTimeout(function () {
            $("#master_description").focus();
        }, 500);

    }

//ok
    function add_subCategoryModal(auto_id, level, description) {
        $("#itemCategoryAddModal").modal('show');
        $("#sub_parentCategory").html(description);
        $("#sub_masterID").val(auto_id);
        $("#sub_levelNo").val(parseInt(level) + 1);
        setTimeout(function () {
            $("#sub_description").focus();
        }, 500);

    }

//ok
    function edit_subCategoryModal(auto_id, level, description, sortOrder, captureCostYN, captureHRYN, system_type ) {
        $("#itemCategoryeditModal").modal('show');
        $("#edit_auto_id").val(auto_id);
        $("#edit_sub_description").val(description);
        //$("#edit_sub_parentCategory").html(description);
        $("#edit_sub_levelNo").val(parseInt(level));
        $("#edit_sub_sortOrder").val(sortOrder);
        if(captureCostYN == 1){
            $('#edit_sub_captureCostYN').prop('checked',true);                    
        }else{
            $('#edit_sub_captureCostYN').prop('checked',false);
        }
        if(captureHRYN == true){
            $('#edit_sub_captureHRYN').prop('checked',true);                    
        }else{
            $('#edit_sub_captureHRYN').prop('checked',false);
        }
        $("#edit_sub_captureCostYN").val(captureCostYN);
        $("#edit_sub_captureHRYN").val(captureHRYN);
        $("#edit_system_type").val(system_type).trigger("change");
        setTimeout(function () {
            $("#edit_sub_description").focus();
        }, 500);
    }
   
    


//.........................................................................
//ok
    function add_description(masterID, name, sortOrder) {
        $("#report_describe_Modal").modal('show');
        $("#describe_masterID").val(masterID);
        $("#describe_parentCategory").html(name);
        $("#describe_sortOrder").val(parseInt(sortOrder));

        setTimeout(function () {
            $("#describe_text").focus();
        }, 500);
    }


//ok
    function generate_Function_description_edit(id, description, name, detail_code){
        $("#report_describe_Modal").modal('show');
       // load_description(id);
        $('#describe_autoID').val(id);
        $("#describe_text").val(description);
        $("#code").val(detail_code);
        $("#describe_parentCategory").html(name);
        setTimeout(function () {
            $("#describe_text").focus();
        }, 500);
   
    }

    // function description_edit(id, , name) {
    //     $("#report_describe_Modal").modal('show');
    //     $.ajax({
    //         type: 'POST',
    //         dataType: 'json',
    //         url: "<?php echo site_url('Report/description_edit'); ?>",
    //         data: {'id': id},
    //         cache: false,
    //         beforeSend: function () {
    //             startLoad();
    //         },
    //         success: function (data) {
    //             stopLoad();
    //             if (data) {
    //              $('#describe_text').val(data['detail_description']);
    //              $('#describe_autoID').val(data['id']);
    //             }
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             stopLoad();
    //             myAlert('e', '<br>Message: ' + errorThrown);
    //         }
    //     });
    //     return false;
    // }


    function load_description(id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Report/load_report_describe'); ?>",
            data: {'id': id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                 $('#describe_text').val(data.detail_description);
                 $('#describe_autoID').val(data.id);
                 $('#describe_masterID').val(data.structureMasterID);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }


    function generate_Function_description_delete(id){
        if(id){
            swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "You want to delete this Description !",/*You want to delete this attachment file*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Report/description_delete'); ?>",
                    data: {'id': id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data.error == 0) {
                            //$("#report_describe_form")[0].reset();
                            myAlert('s', data.message);
                            load_report();
                            //$('#report_describe_Modal').modal('hide');
                        } else if (data.error == 1) {
                            myAlert('e', data.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            });
        }
        return false;
    }

    $('#report_describe_Modal').on('hidden.bs.modal', function () {
        $("#describe_text").val('');
        $("#code").val('');
        $('#describe_autoID').val('');
        $('#describe_masterID').val('');
        $("#describe_sortOrder").val('');
        });

    function minusStyle() {

        !function ($) {

            // Le left-menu sign
            /*for older jquery version */
            $('#left ul.nav li.parent > a > span.sign').click(function () {
                $(this).find('i:first').toggleClass("fa fa-circle-thin");
            });

            $(document).on("click", "#left ul.nav li.parent > a > span.sign", function () {
                //$(this).find('i:first').toggleClass("fa fa-circle-thin");
            });

            // Open Le current menu
            //$("#left ul.nav li.parent.active > a > span.sign").find('i:first').addClass("fa fa-circle-thin");
            $("#left ul.nav li.current").parents('ul.children').addClass("in");


        }(window.jQuery);
    }

</script>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category Add modal" id="master_Report_Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title add_model_header">Add Structure</h4>
  
            </div>
            <div class="modal-body">
                <form id="master_report_form" method="post">
                    <input type="hidden" value="0" id="master_masterID" name="masterID">
                    <input type="hidden" value="1" id="master_levelNo" name="levelNo">
                    <input type="hidden" value="" id="auto_id" name="auto_id">
                    <!-- <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h4 id="master_parentCategory"></h4>
                            </header>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="row" style="marging-top:px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Report Description</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="master_description" name="description"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Sort Order</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="master_sortOrder" name="sortOrder"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture Cost Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="master_captureCostYN" type="checkbox" data-caption="" class="columnSelected" name="captureCostYN" value="1">
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture HR Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="master_captureHRYN" type="checkbox" data-caption="" class="columnSelected" name="captureHRYN" value="1">
                            </div>
                        </div>
                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">System Type</label>
                            </div>
                            <div class="col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="hidden" class="form-control" id="system_type_id" name="system_type_id">
                                    <?php echo form_dropdown('system_type', $system_arr, '', 'class="form-control" id="system_type" required'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                       
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="form-group col-sm-8"></div>
                <div class="row form-group col-sm-4">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="button" onclick="save_master_report()" class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category Add modal" id="itemCategoryAddModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="row">
                    <div class="col-sm-4">
                        <h4 class="modal-title add_model_header">Add Sub Structure</h4>
                    </div>
                    <div class="col-sm-1">
                        <h4 class="modal-title add_model_header add_head">-</h4>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="modal-title add_model_header" id="sub_parentCategory"></h4>
                    </div>
                </div>
                
            </div>
            <div class="modal-body">
                <form id="add_report_children_form" method="post">
                    <input type="hidden" value="0" id="sub_masterID" name="masterID">
                    <input type="hidden" value="1" id="sub_levelNo" name="levelNo">
                    <!-- <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h2 id="sub_parentCategory"></h2>
                            </header>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="row" style="marging-top:px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Report Description<!--Report Description--> </label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="sub_description" name="description"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Sort Order</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="sub_sortOrder" name="sortOrder" 
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture Cost Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="sub_captureCostYN" type="checkbox" data-caption="" class="columnSelected" name="captureCostYN" value="1">
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture HR Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="sub_captureHRYN" type="checkbox" data-caption="" class="columnSelected" name="captureHRYN" value="1">
                            </div>
                        </div>
                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">System Type</label>
                            </div>
                            <div class="col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="hidden" class="form-control" id="system_type_id" name="system_type_id">
                                    <?php echo form_dropdown('system_type', $system_arr, '', 'class="form-control" id="sub_system_type" required'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                       
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="form-group col-sm-8"></div>
                <div class="row form-group col-sm-4">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="button" onclick="save_children_report()" class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category edit modal" id="itemCategoryeditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title edit_model_header">Edit Structure</h4>
            </div>
            <div class="modal-body">
                <form id="edit_report_form" method="post">
                    <input type="hidden" value="" id="edit_auto_id" name="auto_id">

                    <!-- <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h2 id="edit_sub_parentCategory"></h2>
                            </header>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="row" style="marging-top:px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Report Description</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="edit_sub_description" name="description" 
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Sort Order</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="edit_sub_sortOrder" name="sortOrder" 
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture Cost Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="edit_sub_captureCostYN" type="checkbox" data-caption="" class="columnSelected" name="captureCostYN" value="1">
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Capture HR Yes/No</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input id="edit_sub_captureHRYN" type="checkbox" data-caption="" class="columnSelected" name="captureHRYN" value="1">
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">System Type</label>
                            </div>
                            <div class="col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="hidden" class="form-control" id="system_type_id" name="system_type_id">
                                    <?php echo form_dropdown('system_type', $system_arr, '', 'class="form-control" id="edit_system_type" required'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
            <div class="form-group col-sm-8"></div>
                <div class="row form-group col-sm-4">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="button" onclick="update_report()" class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--update-->
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category Add modal" id="report_describe_Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="row">
                    <div class="col-sm-5">
                        <h4 class="modal-title add_model_header">Add Report Description</h4>
                    </div>
                    <div class="col-sm-1">
                        <h4 class="modal-title add_model_header add_head">-</h4>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="modal-title add_model_header" id="describe_parentCategory"></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="report_describe_form" method="post">
                    <input type="hidden" value="0" id="describe_masterID" name="masterID">
                    <input type="hidden" value="" id="describe_sortOrder" name="sortOrder">
                    <input type="hidden" value="" id="describe_autoID" name="describe_autoID">

                    <div class="row">
                        <div class="row" style="marging-top:5px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Code</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="code" name="code"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="row" style="marging-top:5px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Report Description</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="describe_text" name="describe_text"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="form-group col-sm-8" id="description_table">
                        </div>
                    </div> -->

                </form>
            </div>

            <div class="modal-footer">
                <div class="form-group col-sm-8"></div>
                <div class="row form-group col-sm-4">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="button" onclick="save_description()" class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

