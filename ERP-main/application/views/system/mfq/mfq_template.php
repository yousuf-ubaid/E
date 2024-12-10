<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title= $this->lang->line('manufacturing_workflow_process_setup');

echo head_page($title, false); ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #mfq_template th{
        text-transform: uppercase !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class=" pull-right">
            <button type="button" data-text="Add" id="btnAdd" onclick="fetchPage('system/mfq/mfq_template_create',null,'<?php echo $this->lang->line('manufacturing_add_workflow')?>','MFQ');"
                    class="btn btn-sm btn-primary">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
            </button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="mfq_template" class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th style="min-width: 5%">&nbsp;</th>
                    <th style="min-width: 12%"></i> <?php echo $this->lang->line('common_description');?><!--DESCRIPTION--></th>
                    <th style="min-width: 12%"></i> <?php echo $this->lang->line('manufacturing_industry');?><!--INDUSTRY--></th>
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Work Flow Modal" data-backdrop="static"
     data-keyboard="false"
     id="workflowTemplateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times text-red"></i></span></button>
                <h4 class="modal-title" id="modal_title_category">Work Flow Template </h4>
            </div>
            <form id="frm_mfq_template">
                <div class="modal-body">
                    <input type="hidden" value="0" id="workFlowTemplateID" name="workFlowTemplateID">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Industry</label>
                        </div>
                        <div class="form-group col-sm-6">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('industryID', get_all_mfq_industry(), '', 'class="form-control" id="industryID"  required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Description </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Save
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Item to Work Flow"
     id="LinkitemMasterToWorkflow">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Items to Work Flow </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                  <!--  <input type="hidden" value="0" id="wftemplateMasterID" name="wftemplateMasterID">-->
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <select name="itemType" class="form-control" onchange="oTable2.draw(),oTable3.draw()" id="itemType" >
                                <option value=""><?php echo $this->lang->line('manufacturing_select_item_type'); ?><!--Select Item Type--></option>
                                <option value="3">Semi Finish good</option>
                                <option value="2">Finish good</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="padding-left: 2%">
                        <ul class="nav nav-tabs" id="main-tabs">
                            <li class="active"><a href="#commissionSchemes" onclick="oTable2.draw(),oTable3.draw()" data-toggle="tab" ">Items</a></li>
                            <li><a href="#activeItems" data-toggle="tab" onclick="oTable3.draw(),oTable2.draw()">Assigned Items</a></li>
                        </ul>
                    </div>
                    <br/>
                    <div class="tab-content">
                        <div class="tab-pane active" id="commissionSchemes">
                            <div class="table-responsive">
                                <table id="item_table_mfq" class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('manufacturing_main'); ?><!--Main--> <abbr title="Category"><?php echo $this->lang->line('manufacturing_category'); ?><!--Cat..--> </abbr></th>
                                        <th style="min-width: 15%">Item Type </th>
                                        <th style="min-width: 25%">Item Description</th>
                                        <th style="min-width: 10%"><abbr title="Secondary Code"><?php echo $this->lang->line('common_code'); ?><!--CODE--></abbr></th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"><?php echo $this->lang->line('manufacturing_current_stock_title'); ?> <!--Curr.&nbsp;Stock--></abbr></th>
                                        <th style="min-width: 10%;text-align: center !important;">
                                            <button type="button" data-text="Add" onclick="addItem_mfq()"
                                                    class="btn btn-xs btn-primary">
                                                <i class="fa fa-plus" aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?> <!--Add Items-->
                                            </button>
                                        </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                    </tr>
                                    </thead>
                                </table>
                           </div>
                        </div>
                        <div class="tab-pane" id="activeItems">
                            <div class="table-responsive">
                                <table id="pulled_item_table_mfq" class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('manufacturing_main'); ?><!--Main--> <abbr title="Category"><?php echo $this->lang->line('manufacturing_category'); ?><!--Cat..--> </abbr></th>
                                        <th style="min-width: 15%">Item Type </th>
                                        <th style="min-width: 25%">Item Description</th>
                                        <th style="min-width: 10%"><abbr title="Secondary Code"><?php echo $this->lang->line('common_code'); ?><!--CODE--></abbr></th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"><?php echo $this->lang->line('manufacturing_current_stock_title'); ?> <!--Curr.&nbsp;Stock--></abbr></th>


                                        <th style="min-width: 10%;text-align: center !important;">
                                        <button type="button" data-text="Add" onclick="addItem_mfq_default()"
                                                    class="btn btn-xs btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Mark as Default
                                         </button>  
                                        </th>


                                  

                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                        <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    var oTable;
    var oTable2;
    var oTable3;
    var selectedItemsSync = [];
    var selectedItemsSync_radio = [];
    var workFlowTemplateID ;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_template', 'Test', 'Workflow');
        });
        //workFlowTemplateID=$('#templateMasterID').val();
        template();
    });

    function addWorkFlowTemplate() {
        $('#frm_mfq_workflow_template')[0].reset();
        $('#frm_mfq_workflow_template').bootstrapValidator('resetForm', true);
        $('#workFlowTemplateID').val('');
        $('#workflowTemplateModal').modal();
    }

    function template() {
        oTable = $('#mfq_template').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Template/fetch_template'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },

            "aoColumns": [
                {"mData": "templateMasterID"},
                {"mData": "templateDescription"},
                {"mData": "industryTypeDescription"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
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

    function edit_work_flow_template(workFlowTemplateID){
        $('#workFlowTemplateID').val(workFlowTemplateID);
        $.ajax({
            type: 'post',
            dataType: 'json',
            data:{workFlowTemplateID:workFlowTemplateID},
            url: "<?php echo site_url('MFQ_Template/edit_work_flow_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#frm_mfq_workflow_template').bootstrapValidator('resetForm', true);
                    $('#description').val(data['description']);
                    $('#workFlowID').val(data['workFlowID']).change();
                    $('#pageNameLink').val(data['pageNameLink']);
                    $('#workflowTemplateModal').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_workflow_master(templateMasterID) {
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
                        data: {'templateMasterID': templateMasterID},
                        url: "<?php echo site_url('MFQ_Template/delete_workflow_master'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                oTable.draw();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }


    function link_item_master_mfq(id) {
        $('#workFlowTemplateID').val(id);
        selectedItemsSync_radio=[];
        workFlowTemplateID=id;
        loadAssignedItem(workFlowTemplateID);
        semifinished_or_finished_item_table();
        pulled_item_table_mfq();
    
        $("#LinkitemMasterToWorkflow").modal('show');
    }


    function semifinished_or_finished_item_table() {
     
        oTable2 = $('#item_table_mfq').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_ItemMaster/fetch_semifinished_or_finished_item'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {

                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync_mfq(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync_mfq(this);
                });
            },
            "aoColumns": [
                {"mData": "mfqItemID"},
                {"mData": "mainCategory"},
                {"mData": "itemTypeDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "secondaryItemCode"},
                {"mData": "CurrentStock"},
                {"mData": "edit"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "defaultUnitOfMeasure"},
                {"mData": "secondaryItemCode"}
            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [7,8,9,10] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "itemType", "value": $("#itemType").val()});
                aoData.push({"name": "workFlowTemplateID", "value": workFlowTemplateID});

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


    function ItemsSelectedSync_mfq(item) {

        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function ItemsSelectedSync_default(item){ 
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync_radio);
            if (inArray == -1) {
                selectedItemsSync_radio.push(value);
            }
        }
        else {
            var i = selectedItemsSync_radio.indexOf(value);
            if (i != -1) {
                selectedItemsSync_radio.splice(i, 1);
            }
        }
    }


    function addItem_mfq() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/add_item_mfq"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync,'workFlowTemplateID':workFlowTemplateID},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    oTable2.draw();
                    oTable3.draw();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });

    }

    function pulled_item_table_mfq() {
       
        oTable3 = $('#pulled_item_table_mfq').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_ItemMaster/fetch_pulled_item_mfq'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;1
                //selectedItemsSync_radio = [];
                loadAssignedItem(workFlowTemplateID);
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.radiocheck input').iCheck('uncheck');
                if (selectedItemsSync_radio.length > 0) {
       
                    $.each(selectedItemsSync_radio, function (index, value) { 
                        setTimeout(function () {
                            $("#linkIsDefault_"+value).iCheck('check');
                          }, 300);
                       
                     
                    }); 
                    }
                    
                    $('.radiocheck input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                    });
                  /*   
                    $('.radiocheck input').on('ifChecked', function (event) {
                        ItemsSelectedSyncDefault(this);
                    });
                    $('.radiocheck input').on('ifUnchecked', function (event) {
                        ItemsSelectedSyncDefault(this);
                    }); */
               
               
                    $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [
                {"mData": "mfqItemID"},
                {"mData": "mainCategory"},
                {"mData": "itemTypeDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "secondaryItemCode"},
                {"mData": "CurrentStock"},
                {"mData": "action"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "defaultUnitOfMeasure"},
                {"mData": "secondaryItemCode"}
            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [7,8,9,10] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "itemType", "value": $("#itemType").val()});
                aoData.push({"name": "workFlowTemplateID", "value": workFlowTemplateID});

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
    function delete_workFlowTemplate(workFlowTempID,mfqItemID){ 
        swal({
                title: "Are You Sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_ItemMaster/delete_workFlowTemplate'); ?>",
                    type: 'post',
                    data: {'workFlowID': workFlowTempID,'ItemAutoID':mfqItemID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable3.draw();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
   

    function ItemsSelectedSyncDefault(item) {
      
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync_radio);
            if (inArray == -1) {
                selectedItemsSync_radio.push(value);
            }
        }
        else {
            var i = selectedItemsSync_radio.indexOf(value);
            if (i != -1) {
                selectedItemsSync_radio.splice(i, 1);
            }
        }
    }
    function loadAssignedItem(id){ 
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {workFlowID: id},
                url: '<?php echo site_url('MFQ_ItemMaster/fetch_assigned_items'); ?>',
                beforeSend: function () {
                },
                success: function (data) {
                    selectedItemsSync_radio = [];
                    if (!$.isEmptyObject(data)) {
                        
                          selectedItemsSync_radio.push(data["mfqItemID"]);
                    }
                  
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
     
    }

    function addItem_mfq_default(){ 
        var selectedVal = $("input:radio.radioChk:checked");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/add_item_mfq_default"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedVal.val(),'workFlowTemplateID':workFlowTemplateID},
            async: false,
            success: function (data) {
                myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                      
                     }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

</script>