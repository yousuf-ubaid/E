<?php
$liabilityGL = liabilityGL_drop();
$expenseGL = expenseIncomeGL_drop();
$payableGL = payableGL_drop();
$bankWithCardGL = load_bank_with_card();
$payConData = posrPaymentConfig_data();
$outlets = get_active_outlets();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_gl_code_configuratio');
echo head_page($title, false);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script type="text/javascript">

    function glDropMake(thisCombo, dropType, selectedID = null, ID = null) {

        /*return false;*/
        if (ID != null) {
            var glArray = null;
            switch (dropType) {
                case 1:
                    glArray = JSON.stringify(<?php echo json_encode($payableGL) ?>);
                    break;

                case 2:
                    glArray = JSON.stringify(<?php echo json_encode($bankWithCardGL) ?>);
                    break;

                case 3:
                    glArray = JSON.stringify(<?php echo json_encode($liabilityGL) ?>);
                    break;

                case 4:
                    glArray = JSON.stringify(<?php echo json_encode($expenseGL) ?>);
                    break;

                case 5:
                    glArray = JSON.stringify(<?php echo json_encode($outlets) ?>);
                    break;
            }

            var row = JSON.parse(glArray);
            var drop = '<option value=""></option>';

            if (dropType == 5) {
                $.each(row, function (i, obj) {
                    var selected = (selectedID == obj.wareHouseAutoID) ? 'selected' : '';
                    drop += '<option value="' + obj.wareHouseAutoID + '" ' + selected + '>';
                    drop += obj.wareHouseCode + ' | ' + obj.wareHouseDescription + ' | ' + obj.wareHouseLocation + '</option>';
                });

                var thisDropDown = $('#gl_' + thisCombo + '_' + ID + '_outlet');

            } else {
                $.each(row, function (i, obj) {
                    var selected = (selectedID == obj.GLAutoID) ? 'selected' : '';
                    drop += '<option value="' + obj.GLAutoID + '" ' + selected + ' data-sys="' + obj.systemAccountCode + '" data-sec="' + obj.GLSecondaryCode + '" >';
                    drop += obj.GLSecondaryCode + ' | ' + obj.GLDescription + '</option>';
                });

                var thisDropDown = $('#gl_' + thisCombo + '_' + ID);
            }

            thisDropDown.append(drop);
            thisDropDown.change();
        }
        }

    function openGLConfig_paymentsModal() {
        $("#posr_gl_config_payment_modal").modal('show');
    }

    function save_GLConfig_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#frm_gl_config_payment").serialize(),
            url: "<?php echo site_url('Pos_config/saveGLConfigDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#posr_gl_config_payment_modal").modal('hide');
                    myAlert('s', data['message']);
                    fetchPage('system/pos/posr_gl_config', '', 'POS');
                } else {
                    myAlert('e', data['message']);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function ajax_load_chartOfAccountData(data_tmp) {

        var GLConfigMasterID = data_tmp.value;
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {GLConfigMasterID: GLConfigMasterID},
                url: "<?php echo site_url('Pos_config/loadChartOfAccountData'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data.error == 0) {
                        $("#GLCode_frm2").html('<option value="">please select</option>');
                        $.each(data['e'], function (key, value) {
                            var optionVal = '<option value="' + value['GLAutoID'] + '">' + value['GLSecondaryCode'] + ' | ' + value['GLDescription'] + '</option>';
                            $("#GLCode_frm2").append(optionVal);
                        });
                        $("#GLCode_frm2").select2();

                    } else {
                        $("#GLCode_frm2").html('<option>please select</option>');
                        myAlert('e', data.message);
                    }
                    stopLoad();
                },
                error: function () {
                    $("#GLCode_frm2").html('<option>please select</option>');
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            }
        )
    }


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/pos/gl_config', 'Test', 'POS');
        });
    });

    function load_glCodes(obj) {

        var systemCode = $(obj).find(':selected').attr('data-sys');
        var secondaryCode = $(obj).find(':selected').attr('data-sec');
        var systemCodeTxt = $(obj).closest('tr').find('td:eq(2) .systemCode');
        var secondaryCodeTxt = $(obj).closest('tr').find('td:eq(3) .secondaryCode');
        systemCodeTxt.val(systemCode);
        secondaryCodeTxt.val(secondaryCode);


        systemCodeTxt.hide();
        systemCodeTxt.fadeIn();

        secondaryCodeTxt.hide();
        secondaryCodeTxt.fadeIn();
    }

    $(document).ready(function (e) {
        $('.select2').select2();
    })

    function posGL_config(dropDown, autoID, ID, warehouseID) {
        var glAutoID = $('#gl_' + dropDown + '_' + ID).val();
        var outletID = $('#gl_' + dropDown + '_' + ID + '_outlet').val();
        data = [
            {'name': 'glAutoID', 'value': glAutoID},
            {'name': 'paymentTypeID', 'value': autoID},
            {'name': 'ID', 'value': ID},
            {'name': 'warehouseID', 'value': outletID}
        ];

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_config/POSR_posGL_config'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>
<style type="text/css">
    .glInputs {
        height: 25px;
        padding: 2px 10px;
        font-size: 12px;
    }

    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        height: 25px;
        padding: 1px 5px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 25px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>


<div class="container-fluid">
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"></div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <button class="btn btn-sm btn-primary" onclick="openNewPaymentModal()" type="button">
                Add New Payment Method <i class="fa fa-plus"></i></button>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button class="btn btn-sm btn-primary" onclick="openGLConfig_paymentsModal()" type="button">
                Add <i class="fa fa-plus"></i></button>
        </div>

    </div>
</div>
<div>

    <form class="form-horizontal">
        <fieldset>


            <!-- Select Basic -->
            <div class="form-group">
                <label class="col-md-4 control-label"
                       for="selectbasic"><?php echo $this->lang->line('posr_master_outlet_users'); ?></label>
                <div class="col-md-4">

                    <?php
                    echo form_dropdown('warehouseID', get_active_outlets_drop(), '', 'id="warehouseID"  class="form-control select2" onchange="filterOutlet(this)" ')
                    ?>

                </div>
            </div>

        </fieldset>
    </form>

</div>
<div class="table-responsive">
    <table class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th></th>
            <th style="width: 300px">
                <?php echo $this->lang->line('posr_master_account_name'); ?><!--Account Name--></th>
            <th><?php echo $this->lang->line('posr_master_system_code'); ?><!--System Code--></th>
            <th><?php echo $this->lang->line('posr_master_secondary_code'); ?><!--Secondary Code--></th>
            <th><?php echo $this->lang->line('posr_master_outlet_users'); ?><!--Outlets--></th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <?php
        if (!empty($payConData)) {
        foreach ($payConData as $row) {
            $selectedID = ($row['GLCode']) ? $row['GLCode'] : 0;
            $selectedID2 = ($row['warehouseID']) ? $row['warehouseID'] : 0;
            $save = $this->lang->line('common_save');
            $id = 'gl_' . $row['selectBoxName'] . '_' . $row['ID'];
            $onclick = 'onclick="posGL_config(\'' . $row['selectBoxName'] . '\',' . $row['autoID'] . ',' . $row['ID'] . ',' . $selectedID2 . ')"';
            ?>
            <tr class="allRows outletID_<?php echo $row['warehouseID'] ?>">
                <td style="vertical-align: middle"><?php echo $row['description'] ?></td>
                <td>
                    <select name="GLDescription" id="<?php echo $id ?>" class="form-control glInputs "
                            onchange="load_glCodes(this)"></select>
                </td>
                <td><input type="text" class="form-control glInputs systemCode" name="systemCode" readonly></td>
                <td><input type="text" class="form-control glInputs secondaryCode" name="secondaryCode" readonly>
                </td>
                <td>
                    <select id="<?php echo $id ?>_outlet" class="form-control"></select></td>
                <td align="center">
                    <button class="btn btn-default btn-xs" <?php echo $onclick ?>><?php echo $save ?></button>
                    <?php //echo $row['warehouseID'] ?>
                </td>
            </tr>
            <script>
                $(document).ready(function (e) {
                    glDropMake('<?php echo $row['selectBoxName']?>',<?php echo $row['glAccountType']?> ,<?php echo $selectedID ?>, <?php echo $row['ID'] ?>);
                    glDropMake('<?php echo $row['selectBoxName']?>', 5,<?php echo $selectedID2 ?>, <?php echo $row['ID']?>);
                });
            </script>
        <?php

        }
        }else {
        ?>
            <tr>
                <td colspan="5">
                    No Records Found!.
                </td>

            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" id="newPaymentModal" class="modal fade"
     data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog"
         style="width: <?php echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '50%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">New Payment Method</h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="newPaymentName">Payment Name</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="newPaymentName"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="newPaymentType">Payment Type</label>
                        <div class="col-md-7">
                            <select class="form-control" id="newPaymentType">
                                <option value="">Select</option>
                                <option value="2">Card</option>
                                <option value="1">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="newPaymentIcon">Payment Icon</label>
                        <div class="col-md-7">
                            <input type="file" class="form-control" id="newPaymentIcon"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7">

                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="paymentMasteTable" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 20%">Payment Method Name</th>
                            <th style="width: 10%">&nbsp;</th>
                            <th style="width: 10%">&nbsp;</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <input type="button" class="btn btn-primary" value="Save" onclick="saveNewPayment()"/>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="editPaymentModal" class="modal fade"
     data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog"
         style="width: <?php echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '50%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Edit Payment Method</h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="editPaymentName">Payment Name</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="editPaymentName"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="editnewPaymentType">Payment Type</label>
                        <div class="col-md-7">
                            <select class="form-control" id="editnewPaymentType">
                                <option value="">Select</option>
                                <option value="2">Card</option>
                                <option value="1">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="editPaymentIcon">Payment Icon</label>
                        <div class="col-md-7">
                            <img id="paymentIconPreview" src=""/><input type="file" class="form-control" id="editPaymentIcon"/>
                        </div>
                    </div>
                   
                </form>

            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <input type="button" class="btn btn-primary" value="Save" onclick="editPayment()"/>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="posr_gl_config_payment_modal" class="modal fade"
     data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog"
         style="width: <?php echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '50%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form class="form-horizontal" method="post" id="frm_gl_config_payment">
                    <fieldset>


                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="warehouseID"><?php echo $this->lang->line('posr_master_outlet_users'); ?></label>
                            <div class="col-md-5">
                                <?php
                                echo form_dropdown('warehouseID', get_active_outlets_drop(), '', 'id="warehouseID"  class="form-control select2"  ');
                                //onchange="ajax_load_chartOfAccountData(this)"
                                ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-4 control-label" for="paymentConfigMasterID">Payment Type </label>
                            <div class="col-md-5" id="paymentDropdownDiv">
                                <?php
                                echo form_dropdown('paymentConfigMasterID', get_payment_config_master_drop(), '', 'id="paymentConfigMasterID"  class="form-control select2" onchange="ajax_load_chartOfAccountData(this)" ')
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="charOfAccountDropDown">Account Name</label>
                            <div class="col-md-7">
                                <?php
                                echo form_dropdown('GLCode', array('' => 'please select'), '', 'id="GLCode_frm2"  class="form-control select2" ')
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-5">
                                <button class="btn btn-primary" type="button" onclick="save_GLConfig_detail()">
                                    Add <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>

                    </fieldset>
                </form>


            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    function filterOutlet(thisTmp) {
        if (thisTmp.value > 0) {
            $(".allRows").hide();
            $(".outletID_" + thisTmp.value).show();
        } else {
            $(".allRows").show();
        }
    }

    var _URL = window.URL || window.webkitURL;
    $("#newPaymentIcon").change(function (e) {
        var file, img;
        if ((file = this.files[0])) {
            img = new Image();
            var objectUrl = _URL.createObjectURL(file);
            img.onload = function () {
                var width = this.width;
                var height = this.width;
                _URL.revokeObjectURL(objectUrl);
                if(width!=32 || height!=32){
                    $("#newPaymentIcon").val('');
                    myAlert('e','Image size should be 32x32');
                }

            };
            img.src = objectUrl;
        }
    });

    function saveNewPayment() {
        var paymentname =  $("#newPaymentName").val();
        var newPaymentType = $("#newPaymentType").val();
        var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

        if(format.test(paymentname)){
            myAlert('w','Paymnet name cannot contain special characters');
        } else {
            data = new FormData();
            data.append('newPaymentName', $("#newPaymentName").val());
            data.append('newPaymentType', newPaymentType);//value 2 is defined in requirement. get all active
            data.append('newPaymentIcon', $('#newPaymentIcon')[0].files[0]);
            if(validationNewPayment()){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    processData: false,
                    contentType: false,
                    url: "<?php echo site_url('Pos_config/save_new_payment_method'); ?>",
                    beforeSend: function () {
                    },
                    success: function (data) {
                        if(data.status=='updated'){
                            myAlert('s','Successfully added a new payment method.');
                            $("#newPaymentModal").modal('hide');
                            resetForm();
                            $("#paymentDropdownDiv").html(data.paymentDropdown);
                            $("#paymentConfigMasterID").select2();
                        }else{
                            myAlert('e',data.message);
                        }
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }
    }

    function resetForm(){
        $("#newPaymentName").val('');
        $("#newPaymentType").val('');
        $("#newPaymentIcon").val('');
    }

    function validationNewPayment() {
        var newPaymentName = $("#newPaymentName").val();
        //var newPaymentType = $("#newPaymentType").val();
        var newPaymentIcon = $("#newPaymentIcon").val();
        var isValid = true;

        erm = '';
        if (newPaymentName.replace(/\s/g, "") == '') {
            isValid = false;
            erm += '<br>Payment name is required.';
        }else{
            res = newPaymentName.match(/-/g);
            if(res!=null){
                erm += '<br>Cannot include dashes in payment name.';
            }
        }

        if(!isValid){
            myAlert('e',erm);
        }
        return isValid;
    }

    function validationEditPayment() {
        var newPaymentName = $("#editPaymentName").val();
        var isValid = true;

        erm = '';
        if (newPaymentName.replace(/\s/g, "") == '') {
            isValid = false;
            erm += '<br>Payment name is required.';
        }else{
            res = newPaymentName.match(/-/g);
            if(res!=null){
                erm += '<br>Cannot include dashes in payment name.';
            }
        }

        if(!isValid){
            myAlert('e',erm);
        }
        return isValid;
    }

    function openNewPaymentModal() {
        load_paymentDetails();
        $("#newPaymentModal").modal('show');
    }

    function load_paymentDetails() {
        counterMaster_table = $('#paymentMasteTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/load_payment_master'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".accessControlSwitch").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "autoID"},
                {"mData": "description"},
                {"mData": "enableDisable"},
                {"mData": "action"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
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

    function deletePaymentMaster() {
        var id = $(this).data('autoid');
        var status = this.checked?1:0;
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able');?>",/*Your will not be able to recover this data*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>",/*Yes, delete it!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function (input) {
                if(input){
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {id: id,status:status},
                        url: "<?php echo site_url('Pos_config/delete_payment_master'); ?>",
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if(data.status=='updated'){
                                myAlert('s',data.message);
                            }else{
                                myAlert('e',data.message);
                            }
                            load_paymentDetails();

                        }, error: function () {

                        }
                    });
                }else{
                    load_paymentDetails();
                }

            });

    }

    function editPaymentModalDialog(){
        var id = $(this).data('id');
        localStorage.setItem('paymentId',id);
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('Pos_config/get_payment_method_details'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                $("#editPaymentName").val(data.description);
                var path = "<?php echo base_url(); ?>"+data.image;
                $("#paymentIconPreview").attr('src',path);
                $('#editnewPaymentType option[value='+data['glAccountType']+']').prop('selected','selected').change();

            }, error: function () {

            }
        });
        $("#editPaymentModal").modal('show');
    }


    function editPayment() {
        var paymentname =  $("#editPaymentName").val();
        var editnewPaymentType =  $("#editnewPaymentType").val();
        var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

        if(format.test(paymentname)){
            myAlert('w','Paymnet name cannot contain special characters');
        } else {

            data = new FormData();
            data.append('newPaymentName', $("#editPaymentName").val());
            data.append('newPaymentType', editnewPaymentType);//value 3 is defined in requirement.
            data.append('newPaymentIcon', $('#editPaymentIcon')[0].files[0]);
            data.append('id', localStorage.getItem('paymentId'));

            if(validationEditPayment()){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    processData: false,
                    contentType: false,
                    url: "<?php echo site_url('Pos_config/modify_payment_method'); ?>",
                    beforeSend: function () {
                    },
                    success: function (data) {
                        if(data.status=='updated'){
                            myAlert('s','Successfully modified the payment method.');
                            $("#editPaymentModal").modal('hide');
                            //resetForm();
                            $("#paymentDropdownDiv").html(data.paymentDropdown);
                            $("#paymentConfigMasterID").select2();
                        }else{
                            myAlert('e',data.message);
                        }
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }
    }


</script>

