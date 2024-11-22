<?php
$type = company_type('All');
?>

<style>
    #mainContainer{
        min-height: 700px
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }

    .datatTblBtn{
        margin-left: 5px;
    }
</style> 
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Company Setup</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="tmpCompanyID" value="0">
                        <div id="div_loadCompanyAdminUsers" class="ajaxContainer"></div>
                        <div id="mainContainer" class="ajaxContainer">
                            <div class="row">
                                <div class="col-md-1" style="padding-top: 5px"><label>Filter </label></div>
                                <div class="col-md-2">                                 
                                    <?=form_dropdown('', $type, null, 'class="form-control" id="sys_company_type" onchange="company_table()"')?>
                                </div>
                                <div class="col-md-2"> </div>
                                <div class="col-md-7 text-right">
                                    <a class="btn btn-primary pull-right" href="<?=site_url('companyAdmin/AddCompany/null'); ?>">
                                        <i class="fa fa-plus"></i> New Company
                                    </a>
                                </div>                                
                            </div>

                            <div class="row"><hr/></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <table id="company_table" class="table table-bordered table-striped table-condensed">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 5%">#</th>
                                            <th style="min-width: 10%">Logo</th>
                                            <th style="min-width: 35%">Company Details</th>
                                            <th style="width: 100px">Company Name</th>
                                            <th style="min-width: 25%">Busniess Name</th>
                                            <th style="width: 100px">Type</th>
                                            <th style="width: 100px">Subscription</th>
                                            <th style="min-width: 18%">&nbsp;</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="token_update_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="">
    <div class="modal-dialog">
        <?php echo form_open('', 'role="form" id="token_updtae_form" autocomplete="off" class="form-horizontal"'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Update Token - <span id="tokenchange_companyName" style="font-size: 14px;"></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="token_companyID" id="token_companyID" value="">
                <div class="form-group">
                    <label for="paymentEnabled" class="col-sm-3 control-label">Token</label>
                    <div class="col-sm-6">
                        <input type="text" name="supportToken" id="supportToken" class="form-control " required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm " type="button" onclick="update_company_token()">Update</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<link href="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript" src="<?=base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>

<script type="text/javascript">
    var company_tbl = null;
    $(document).ready(function () {
        company_table();
    });

    function company_table() {
        refresh_tbl = company_tbl = $('#company_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_companyAdmin'); ?>",
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

                $(".switch-chk").bootstrapSwitch();
            },
            "columnDefs": [
                {"targets": [ 3 ], "visible": false },
                {"targets": [ 1,6,7 ], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "company_id"},
                {"mData": "img"},
                {"mData": "company_detail"},
                {"mData": "company_name"},
                {"mData": "company_business_name"},
                {"mData": "tyDes"},
                {"mData": "isDisabled_str"},
                {"mData": "edit"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'sys_company_type', 'value':  $('#sys_company_type').val()});

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

    function loadCompanyAdminUsers(companyID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Dashboard/loadCompanyAdminUsers'); ?>",
            data: {companyid: companyID},
            cache: false,
            beforeSend: function () {
                openDivToggle('div_loadCompanyAdminUsers');
                startLoad();
            },
            success: function (data) {
                $("#tmpCompanyID").val(companyID);
                $("#div_loadCompanyAdminUsers").html(data);
                $("#companyID_cAdmin").val(companyID);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $("#div_loadCompanyAdminUsers").html(errorThrown)
            }
        });
    }

    function backToCompanyList() {
        openDivToggle('mainContainer');
    }

    function openDivToggle(id) {
        $(".ajaxContainer").hide();
        $("#" + id).show();
    }

    function save_companyAdmin() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/save_companyAdmin'); ?>",
            data: $("#addCompanyAdminFrm").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                console.log(data);
                if (data['error'] == 0) {

                    myAlert('s', data['message']);
                    loadCompanyAdminUsers(data['code']);
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function request_pin(id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/request_pin'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function change_company_status(com_id){
        let obj = $('#com_status_'+com_id);
        let status = ( obj.is(":checked") )? 1 : 0;
        let msg = (status)? 'activate': 'inactivate';

        swal({
                title: "Are you sure?",
                text: "You want to " + msg + " this company",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        url: '<?php echo site_url('Dashboard/companyStatusChange'); ?>',
                        data: {'com_id': com_id, 'status':status},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] != 's') {
                                let changeStatus = ( !obj.prop('checked') );
                                let changeFn = obj.attr('onchange');

                                obj.removeAttr('onchange');
                                obj.prop('checked', changeStatus).change().attr('onchange', changeFn);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');

                            let changeStatus = ( !obj.prop('checked') );
                            let changeFn = obj.attr('onchange');

                            obj.removeAttr('onchange');
                            obj.prop('checked', changeStatus).change().attr('onchange', changeFn);
                        }
                    });
                }
                else {
                    let changeStatus = ( !obj.prop('checked') );
                    obj.prop('checked', changeStatus).change();
                }
            }
        );
    }

    function flush_data_conf(obj){
        let det = get_dataTable_det('company_table', obj);
        
        let id = det.company_id;
        let comName = det.company_name;
        let comCode = det.company_code;

        bootbox.confirm({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Confirmation!</strong>',
            message: '<b>Are you sure?</b><br/>You want to flush the data of  <b>'+comName+' [ '+comCode+' ] !',
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    flush_data(id);
                }                 
            }
        });
    }

    function flush_data(id){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?=site_url('Flush_data/flush'); ?>",
            data: {companyID: id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                console.log(data);
                ajax_toaster(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function open_tokenChange_modal(company_id,company_name,code,supportToken) {

        $('#token_update_modal').modal('show');
        var name = company_name +' ( '+code+' )';
        $('#tokenchange_companyName').text(name);
        $('#supportToken').val(supportToken);
        $('#token_companyID').val(company_id);


    }
    function update_company_token() {
        var post_data = $('#token_updtae_form').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/update_company_token'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    company_tbl.draw();
                    $('#token_update_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }


</script>

