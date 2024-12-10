<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = '';

include_once(APPPATH . 'helpers/report_helper.php');

echo head_page($title, true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$company_arr = all_company_drom();
$companyID = current_companyID();

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
        font-weight: bold;
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
        min-width:300px !important;
    }
    #td_icon{
        min-width:50px !important;
    }

    #td_width2_config{
        min-width:100px;
    }
    .myUL_li{
        width:auto;
    }
    #myUL_li_div_id{
        min-width:616px;
        max-width:auto;
    }
    
</style>


<div class="row">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="form-group col-sm-4 pull-right">
            <label for="company">Company</label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('company', $company_arr, $companyID, 'class="form-control select2" id="company"'); ?></div>
                <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style="margin-top: -10%;">
                <i class="fa fa-times-circle-o"></i>
            </button>
            <button type="button" class="btn btn-primary pull-right" onclick="search_activity_code()" style="margin-top: -9%; margin-left: 250px; position: absolute;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="d-flex justify-content-center"><h4><strong>Activity Code</strong></h4></div>
</div>
<div class="row">
    <div class="form-group col-sm-12">
        <button type="button" onclick="open_activityCode_Modal()" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i>&nbsp;&nbsp;
            Add New
        </button>
    </div>
</div>
<br>
<hr>
        <div class="table-responsive">
            <table id="activity_code_table" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <th style="min-width: 4%">#</th>
                    <th style="min-width: 15%">Activity Code</th>
                    <th style="min-width: 10%">Company</th>
                    <th style="min-width: 20%">Naration</th>
                    <th style="min-width: 5%">Status</th>
                    <th style="min-width: 5%">Action</th>
                </tr>
                </thead>
            </table>
        </div>


<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Activity Code Add modal" id="open_activityCode_Modal">
    <div class="modal-dialog" style="width:40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title add_model_header"><span id="model_title">Add Activity Code</span></h4>
  
            </div>
            <div class="modal-body">
                <form id="activity_code_form" method="post">
                   <input type="hidden" id="activityCode_ID" name="activityCode_ID" val="">
                    <div class="row">
                        <div class="row" style="marging-top:0px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Activity Code</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <input type="text" id="activityCode_name" name="activityCode_name"
                                        class="form-control" required>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Company</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('companyID', $company_arr, '', 'class="form-control select2" id="companyID" required'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row master" style="margin-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Is_Active</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input type="checkbox" class="myCheckbox" id="isActive" name="isActive" value="1">
                                <!-- <input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1"> -->
                            </div>
                        </div>

                        <div class="row" style="marging-top:15px;">
                            <div class="form-group col-sm-4">
                                <label class="title">Narration</label>
                            </div>
                            <div class="form-group col-sm-6">
                            <div class="form-group ">
                                <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                            </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="form-group col-sm-8"></div>
                <div class="row form-group col-sm-4">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="button" onclick="save_activity_code()" class="btn btn-primary btn-sm pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_save') ?><!--Add-->
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Activity Code Config modal" id="open_config_Modal">
    <div class="modal-dialog" style="width:50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div>
                    <h3 class="modal-title open_config_Modal"><span id="model_title"><strong>Activity Code Config - <span id="activity_code_name" style="color:blue;"></span></strong></span></h3>
                    
                </div>
                
            </div>
            <div class="modal-body">
                    <input type="hidden" name="activityCodeID" val="">
                    <div class="row">
                        <div id="treeContainer_config" style="min-height: 700px; overflow-x: auto; margin-left:50px">
                        </div>
                    </div>
            </div>

            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    var activityCode_AutoID;
    $(document).ready(function (e) {
        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));
        activity_code_table();
    })

    function activity_code_table(selectedID = null) {
    Otable = $('#activity_code_table').DataTable({
        "language": {
            "url": "<?php echo base_url('plugins/datatables/i18n/'.$primaryLanguage.'.json') ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Report/activity_code_table'); ?>",
        "aaSorting": [[1, 'desc']],
        "fnInitComplete": function () {},
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                if (parseInt(oSettings.aoData[x]._aData['id']) == selectedRowID) {
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');
                }
                x++;
            }
        },
        "aoColumns": [
            {"mData": "id"},
            {"mData": "activity_code"},
            {"mData": "company_code"},
            {"mData": "narration"},
            {"mData": "status"},
            {"mData": "edit"},
        ],
        "columnDefs": [
            {"targets": [0], "orderable": false, "searchable": false}, 
        ],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name:'company', value: $('#company option:selected').val()});
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


function open_activityCode_Modal() {
        $("#open_activityCode_Modal").modal('show');
        $("#activityCode_name").val('');
        $("#companyID").val('').trigger('change');
        $("#isActive").prop('checked', false);
        $("#narration").val('');
        setTimeout(function () {
            $("#narration").focus();
        }, 500);
}

function save_activity_code(){
    var formData = new FormData($("#activity_code_form")[0]);

    formData.append('company_id', $('#companyID').val());
    formData.append('is_active', $('#isActive').prop('checked') ? 1 : 0);
    formData.append('company_text', $('#companyID option:selected').text());
    
    // var data = $("#activity_code_form").serializeArray();
    // data.push({'name': 'company_id', 'value': $('#companyID').val() });
    // data.push({'name': 'is_active', 'value': $('#isActive').prop('checked') ? 1 : 0});
    // data.push({'name': 'company_text', 'value': $('#companyID option:selected').text()});
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Report/save_activity_code'); ?>",
            processData: false,
            contentType: false, 
            data: formData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if (data[0] == 's') {
                    $('#activityCode_ID').val('');
                    $("#activity_code_form")[0].reset();
                    $('#open_activityCode_Modal').modal('hide');
                    activity_code_table();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
}

function edit_activityCode(id, title){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'id': id},
        url: "<?php echo site_url('Report/load_activity_code_edit'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            if(data){
                $("#model_title").val(title);
                $("#activityCode_name").val(data.activity_code);
                $("#companyID").val(data.company_id).trigger('change');
                $("#activityCode_ID").val(data.id);
                $("#isActive").prop('checked', data.is_active == 1);
                $("#narration").val(data.narration);
                setTimeout(function () {
                    $("#narration").focus();
                }, 500);
                $("#open_activityCode_Modal").modal('show');
            }  
        },
        error: function () {
            stopLoad();
            myAlert('e', 'An Error Occurred! Please Try Again.');
            refreshNotifications(true);
        }
    });
}


function load_report_config(id, activity_code) {
    activityCode_AutoID = id;
    var activity_code_name = activity_code;
    $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Report/load_config_report'); ?>",
            data: {'categoryType': 1, 'activityCode_AutoID': activityCode_AutoID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#activity_code_name').text(activity_code_name);
                $('#activityCodeID').val(activityCode_AutoID);
                $('#treeContainer_config').html('');
                $('#treeContainer_config').html(data);
                $("#open_config_Modal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function delete_activityCode(id) {
        if (id) {
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
                        data: {'id': id},
                        url: "<?php echo site_url('Report/delete_activity_code'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            activity_code_table();
                           
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }else{
            return false;
        }
    }


    function search_activity_code(){
        Otable.draw()
    }

    function clear_all_filters() {
        $('#company').val(<?php echo current_companyID(); ?>).trigger('change');
        search_activity_code();
        setTimeout(function(){
            search_activity_code();
        }, 150);
    }
   
</script>