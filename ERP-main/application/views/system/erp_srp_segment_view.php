<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_segment');
echo head_page($title, false);

$user_group = all_group_drop(true,1,1);

/*echo head_page('Segment', false);*/
$usergroup_assign = getPolicyValues('UGSE','All');

?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<style>
    .select2-selection__choice {background-color:#696CFF !important; padding:5px 10px !important; font-size:12px;border-radius:4px;}
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">

    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" data-toggle="modal" onclick="resetfrm()"
                data-target="#segment_model"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="segment_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 10%">#</th>
            <!--<th>Company ID</th>-->
            <!--<th>#</th>-->
            <th><?php echo $this->lang->line('finance_ms_segment_code');?><!--Segment Code--></th>
            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th><?php echo $this->lang->line('common_master_segment');?><!--Description--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_is_default'); ?> </th>
            <!--Is Default-->
            <th style="min-width: 7%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
        </tr>
        </thead>
        <tbody id="segmentBody">

        </tbody>
    </table>
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
                            <label for="paymentTerms"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <textarea class="form-control" id="description" name="description" style="width:255px;"
                                      rows="2"></textarea>
                            <input type="hidden" class="form-control" id="segmentID" name="segmentID">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_segment_code');?><!--Segment Code--></label>
                            <input type="text" class="form-control" id="segmentcode" name="segmentcode">
                        </div>
                    </div>
                    <?php if($usergroup_assign == 1){ ?>
                        
                        <div class="row">
                            <div class="form-group col-sm-6" style="margin-left: 0px;">
                                <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_user_group');?><!--Segment Code--></label><br>
                                <?php echo form_dropdown('user_group[]', $user_group,null, 'class="form-control user_group" id="user_group_multi"  multiple="multiple"'); ?>
                            </div>
                        </div>
                    
                    <?php } ?>

                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <input type="checkbox" class="form-check-input" id="isShow" name="isShow" checked value="1">
                            <label for="isShow"><?php echo $this->lang->line('finance_ms_is_show');?><!--Segment Code--></label>
                        </div>
                    </div>

                    <div class="row" id="subSegment">
                        <div class="form-group col-sm-12" style="margin-left: 0px;">
                            <label for="subsegment"><?php echo $this->lang->line('finance_ms_sub_segment');?><!--Sub Segment--></label>
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
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--> <span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
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
                    <?php if($usergroup_assign == 1){ ?>
                        <div class="row">
                            <div class="form-group col-sm-6" style="margin-left: 0px;">
                                <label for="paymentTerms"><?php echo $this->lang->line('finance_ms_user_group'); ?></label><br>
                                <?php echo form_dropdown('user_group[]', $user_group,null, 'class="form-control user_group" id="sub_user_group_multi" multiple="multiple"'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <input type="checkbox" class="form-check-input" id="sub_Show" name="sub_Show" checked value="1">
                            <label for="sub_Show"><?php echo $this->lang->line('finance_ms_is_show');?><!--Segment Code--></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save'); ?> <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button onclick="resetSubModel()" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#user_group_multi").select2();
        $("#sub_user_group_multi").select2();
        $('#subSegment').hide();

        $('.headerclose').click(function(){
            fetchPage('system/erp_srp_segment_view','','Segment');
        });

        segmentview();

        $('#segment_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                segmentcode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_ms_segment_code_is_required');?>'/*Segment Code is required*/
                        },
                        stringLength: {
                            max: 10,
                            message: '<?php echo $this->lang->line('finance_ms_character_must_be');?>'/*Character must be below 6 character*/
                        }

                    }
                },
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Segment/save_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $("#segment_model").modal("hide");
                        segmentview();
                        $('#subSegment').hide();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('#sub_segment_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',
            excluded: [':disabled'],
            fields: {
                sub_segmentcode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_ms_segment_code_is_required');?>'
                        },
                        stringLength: {
                            max: 10,
                            message: '<?php echo $this->lang->line('finance_ms_character_must_be');?>'
                        }
                    }
                },
                sub_description: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('common_description_is_required');?>'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            if ($('#sub_Show').prop('checked')) {
                data.push({name: 'sub_Show', value: '1'});
            } else {
                data.push({name: 'sub_Show', value: '0'});
            }

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Segment/save_sub_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        getSubSegment(); 
                        resetSubModel();
                        $('#sub_segment_form').bootstrapValidator('resetForm', true);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    // sub segment after the parent segment
    // function segmentview() {
    //     var Otable = $('#segment_table').DataTable({
    //         "language": {
    //             "url": "<?php echo base_url('plugins/datatables/i18n/$primaryLanguage.json') ?>"
    //         },
    //         "bProcessing": true,
    //         "bServerSide": true,
    //         "bDestroy": true,
    //         "sAjaxSource": "<?php echo site_url('Segment/load_segment'); ?>",
    //         "aaSorting": [[0, 'desc']],
    //         "fnInitComplete": function () {},
    //         "fnDrawCallback": function (oSettings) {
    //             $("[rel=tooltip]").tooltip();

    //             var data = oSettings.aoData;
    //             var parentRows = [];
    //             var childRows = [];
    //             var otherRows = [];
    //             var rowElementMap = new Map();

                
    //             for (var i = 0; i < data.length; i++) {
    //                 var rowData = data[i]._aData;
    //                 var segmentID = rowData.segmentID;
    //                 var masterID = rowData.masterID;
    //                 var masterIDStatus = rowData.masterIDStatus; 
    //                 var rowElement = $(data[i].nTr);

    //                 rowElementMap.set(segmentID, { rowElement: rowElement, masterID: masterID });

    //                 if (masterIDStatus === 'Has Sub Record') {
    //                     parentRows.push({ segmentID: segmentID, rowElement: rowElement });
    //                 } else if (masterID === null || masterID === '') {
    //                     otherRows.push({ rowElement: rowElement });
    //                 } else {
    //                     childRows.push({ rowElement: rowElement, masterID: masterID });
    //                 }
    //             }

                
    //             $('#segment_table tbody').empty().append(parentRows.map(parent => parent.rowElement));
    //             $('#segment_table tbody').append(otherRows.map(other => other.rowElement));

               
    //             parentRows.forEach(function (parent) {
    //                 var parentSegmentID = parent.segmentID;
    //                 var parentRow = parent.rowElement;

                    
    //                 childRows.forEach(function (child) {
    //                     if (child.masterID === parentSegmentID) {
    //                         parentRow.after(child.rowElement);
    //                     }
    //                 });
    //             });

    //             // Row #
    //             var rowNumber = 1;
    //             $('#segment_table tbody tr').each(function () {
    //                 var rowElement = $(this);
    //                 var segmentID = rowElement.find('td').eq(0).text();
    //                 var masterID = rowElementMap.get(segmentID).masterID;

    //                 if (masterID === null || masterID === '') {
    //                     rowElement.find('td:eq(0)').html(rowNumber);
    //                     rowNumber++;
    //                 } else {
    //                     rowElement.find('td:eq(0)').html('Sub segment of '+(rowNumber-1));
    //                 }
    //             });
    //         },
    //         "aoColumns": [
    //             {"mData": "segmentID"},
    //             {"mData": "segmentCode"},
    //             {"mData": "description"},
    //             {"mData": "default"},
    //             {"mData": "action"},
    //             {"mData": "status"}
    //         ],
    //         "columnDefs": [{"searchable": false, "targets": [0]}],
    //         "fnServerData": function (sSource, aoData, fnCallback) {
    //             $.ajax({
    //                 'dataType': 'json',
    //                 'type': 'POST',
    //                 'url': sSource,
    //                 'data': aoData,
    //                 'success': fnCallback
    //             });
    //         }
    //     });
    // }

    function segmentview() {
        var Otable = $('#segment_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Segment/load_segment'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
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
                {"mData": "segmentID"},
                //{"mData": "companyID"},
                {"mData": "segmentCode"},
                {"mData": "description"},
                {"mData": "masterSegmentInfo"}, 
                {"mData": "default"},
                {"mData": "action"},
                {"mData": "status"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function edit_segmrnt(id) {
        $('#segment_form').bootstrapValidator('resetForm', true);
        $("#segment_model").modal("show");
        $('#segmentID').val(id);
        $('#segmentHead').html('<?php echo $this->lang->line('finance_edit_segment')?>');/*Edit Segment*/
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {segmentID: id},
            url: "<?php echo site_url('Segment/edit_segment'); ?>",
            success: function (data) {
                $('#user_group_multi').val(data['user_groups']).change();
                $('#description').val(data['description']);
                $('#segmentcode').val(data['segmentCode']);
             
                if(data['isShow'] == 1){
                    $('#isShow').prop('checked',true);
                }else{
                    $('#isShow').prop('checked',false);
                }
                showSubSegment();
                getSubSegment();
                resetSubModel();

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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
                data: {segmentID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('Segment/update_segmentstatus'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        segmentview();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else if (!$('#statusactivate_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {segmentID: id, chkedvalue: 0},
                url: "<?php echo site_url('Segment/update_segmentstatus'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        segmentview();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function resetfrm() {
        $('#segmentID').val('');
        $('#segmentHead').html('<?php echo $this->lang->line('finance_ms_add_new_segment');?>');/*Add New Segment*/
        $('#segment_form')[0].reset();
        $('#segment_form').bootstrapValidator('resetForm', true);

        resetSubModel();
    }


    /* Function added */
    function setDefaultWarehouse(thisID, segmentID) {
        var checked = 0;
        if ($(thisID).is(':checked')) {
            checked = 1;
        }
        else {
            checked = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: checked, segmentID: segmentID},
            url: "<?php echo site_url('Segment/setDefaultsegment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] = 's') {
                    segmentview();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    /* End  Function */ 

    function showSubSegment(){
        var segmentID=$('#segmentID').val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {segmentID: segmentID},
            url: "<?php echo site_url('Segment/checkForSubSegment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                if (!data || !data.masterID) {
                    $('#subSegment').show();
                } else {
                    $('#subSegment').hide();
                }
            },
            error:function(){
                myAlert('e','Something went wrong');
            }
        });
    }

    function getSubSegment() {
        var segmentID = $('#segmentID').val();
        
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {segmentID: segmentID},
            url: "<?php echo site_url('Segment/getSubSegment'); ?>",
            beforeSend: function () {
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

    function openSubgSegment(){
        var segmentID=$('#segmentID').val();
        $('#sub_segment_model').modal({backdrop: 'static', keyboard: false});
        $('#subsegmentID').val(segmentID);
    }

    function resetSubModel(){
        $('#sub_segment_model').modal("hide");
        $('#sub_segment_model input, #sub_segment_model select, #sub_segment_model textarea').val(''); 
    }
</script>