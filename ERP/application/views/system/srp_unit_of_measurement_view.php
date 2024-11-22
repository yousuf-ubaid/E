<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_uom_heading');
echo head_page($title, false);

/*echo head_page('Unit Of Measurements', false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-9 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_uom_model()" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i><?php echo $this->lang->line('common_create_new');?> <!--Create New--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="umo_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('erp_uom_code');?></th><!--UOM Code-->
            <th style="min-width: 50%"><?php echo $this->lang->line('erp_uom_description');?> </th><!--UOM Description-->
            <th style="min-width: 20%"><?php echo $this->lang->line('erp_uom_created_by');?> </th><!--Created By-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="uom_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="UOMHead"></h3>
            </div>
            <form role="form" id="uom_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="UnitID" name="UnitID">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_code');?><!--Code--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="UnitShortCode" name="UnitShortCode">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?> <!--Description--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="UnitDes" name="UnitDes">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div></div>

<div class="modal fade" id="uom_detail_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('erp_uom_edit_uom');?><!--Edit UOM Conversion--></h3>
            </div>
                <div class="modal-body">
                    <form class="form-inline pull-right" id="add_conversion_form">
                        <div class="form-group">
                            <label for="exampleInputName2"><?php echo $this->lang->line('common_uom');?><!--UOM--></label>
                            <?php echo form_dropdown('subUnitID', array('' => 'Select UOM'), '', 'class="form-control select2" id="subUnitID" required'); ?>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail2"><?php echo $this->lang->line('common_rate');?><!--Rate--></label>
                            <input type="text" class="form-control number" id="conversion" name="conversion">
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_add');?><!--Add--></button>
                    </form><br><br>
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td><?php echo $this->lang->line('erp_uom_master_uom');?><!--Master UOM--></td>
                                <td><?php echo $this->lang->line('erp_uom_sub_uom');?><!--Sub UOM--></td>
                                <td><?php echo $this->lang->line('erp_uom_conversion_uom');?><!--Conversion--></td>
                            </tr>
                        </thead>
                        <tbody id="table_body">
                            
                        </tbody>
                    </table>
                </div>    
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var masterUnitID;
    var desc;
    var code;
    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/srp_unit_of_measurement_view','Test','Unit Of Measurement');
        });
        masterUnitID = null;
        desc = null;
        code = null;
        fetch_umo_data(); 
        number_validation();
        $('#add_conversion_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                subUnitID  : {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_uom_uom_is_required');?>.'}}},/*UOM is required*/
                conversion : {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_uom_conversion_is_required');?> .'}}}/*Conversion is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'masterUnitID', 'value': masterUnitID });
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Procurement/save_uom_conversion'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if(data){
                            fetch_umo_detail_con(masterUnitID,desc,code);
                        }
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        var nospecial=/^[^*|=.,~!%^#\":<>[_/\]{}?`\\()+';@&$-]+$/;
        $('#uom_form').bootstrapValidator({  
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                    UnitShortCode: {
                        validators: {
                            notEmpty: {
                                message: '<?php echo $this->lang->line('erp_warehouse_code_is_required');?>.'
                            },
                            regexp: {
                                    regexp: nospecial,
                                    message: 'Special Characters not Allowed'
                                }
                        }
                    },/*Code is required*/
                    UnitDes: {
                        validators: {
                            notEmpty: {
                                message: '<?php echo $this->lang->line('common_description_is_required');?>.'
                            },
                        regexp: {
                            regexp: nospecial,
                            message: 'Special Characters not Allowed'
                        }
                    }/* Description is required*/
                }
            }
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
                    url: "<?php echo site_url('Procurement/save_uom'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if(data){
                            $("#uom_model").modal("hide");
                            fetch_umo_data();
                        }
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });
    });

    function fetch_umo_data() {
        var Otable = $('#umo_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Procurement/fetch_umo_data'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "UnitID"},
                {"mData": "UnitShortCode"},
                {"mData": "UnitDes"},
                {"mData": "modifiedUserName"},
                {"mData": "edit"}
            ],
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

    function fetch_umo_detail_con(id,desc,code){
        masterUnitID = id;
        desc = desc;
        code = code;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('Procurement/fetch_convertion_detail_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#table_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                }else {
                    $.each(data['detail'], function (key, value) {
                        status = '';
                        if (masterUnitID == value['subUnitID']) {
                            status = 'readonly';
                        }
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['m_dese'] +' - '+ value['m_code']+ '</td><td> = ' + value['sub_dese'] +' - '+ value['sub_code']+ '</td><td class="pull-right"><input type="text" class="form-control number" id="conversion" name="conversion" onchange="change_conversion('+masterUnitID+','+value['subUnitID']+',this.value)" value="'+value['conversion']+'" '+status+'></td></tr>');
                         x++;
                    });

                    $('#subUnitID').empty();
                    var mySelect = $('#subUnitID');
                    mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('common_select_uom');?>'));/*Select  UOM*/
                    if (!jQuery.isEmptyObject(data['drop'])) {
                        $.each(data['drop'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                    }
                }
                $('#add_conversion_form')[0].reset();
                $('#add_conversion_form').bootstrapValidator('resetForm', true);
                $("#uom_detail_model").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        
    }

    function open_uom_model() {
        $('#uom_form')[0].reset();
        $('#UOMHead').html('<?php echo $this->lang->line('erp_uom_add_new_uom');?>');/*Add New UOM*/
        $('#uom_form').bootstrapValidator('resetForm', true);
        $("#uom_model").modal({backdrop: "static"});
    }

    function change_conversion(masterUnitID,subUnitID,conversion){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID':masterUnitID,'subUnitID':subUnitID,'conversion':conversion},
            url: "<?php echo site_url('Procurement/change_conversion'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                HoldOn.close();
                refreshNotifications(true);
                // if(data){
                //     fetch_umo_detail_con(masterUnitID,desc,code);
                // }
            }, error: function () {
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }
</script>