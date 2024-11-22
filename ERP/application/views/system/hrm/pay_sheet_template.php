
<!--Translation added by Naseek-->
<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_pay_sheet_template_master');
echo head_page($title, false);



?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody><tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> /</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span></span> <?php echo $this->lang->line('common_not_confirmed');?><!-- Not Confirmed-->/ </span> <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td>
                    <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> </span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
            </tbody></table>
    </div>
    <div class="col-md-2 pull-right">
        <div class="clearfix hidden-lg">&nbsp;</div>
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_template()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
    <div class="col-md-5">
        <form class="form-inline pull-right">
            <div class="form-group">
                <label for="isNonPayroll"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> &nbsp;</label>
                <select name="isNonPayroll" id="isNonPayroll" class="form-control" onchange="fetch_templates()">
                    <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                    <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                </select>
            </div>
        </form>
    </div>

</div><hr>
<div class="table-responsive">
    <table id="templateTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 50%"><?php echo $this->lang->line('hrms_payroll_template_name');?><!--Template Name--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_default');?><!--Default--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--status--></th>
            <th style="min-width: 6%"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="newTemplateModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_new_template');?><!--New Template--></h4>
            </div>
            <form class="form-horizontal" role="form" id="newTemplate_form" >
                <div class="modal-body">
                    <div class="form-group has-feedback">
                        <label class="col-sm-4 control-label" for="templateName" ><?php echo $this->lang->line('hrms_payroll_template_name');?><!--Template Name--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="templateName" id="templateName"  class="form-control saveInputs">
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="col-sm-4 control-label" for="payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="payrollType" id="payrollType" class="form-control">
                                <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cloneTemplateModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_template_clone');?><!--Template Clone--></h4>
            </div>
            <form class="form-horizontal" role="form" id="cloneTemplate_form" >
                <div class="modal-body">
                    <div class="form-group has-feedback">
                        <label class="col-sm-4 control-label" for="original-templateName"><?php echo $this->lang->line('hrms_payroll_original_template');?><!--Original Template--><?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="original-templateName" id="original-templateName"  class="form-control saveInputs" disabled>
                            <input type="hidden" name="original-templateNameID" id="original_templateID" >
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="col-sm-4 control-label" for="clone_templateName"><?php echo $this->lang->line('hrms_payroll_new_template');?><!--New Template--><?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="clone_templateName" id="clone_templateName"  class="form-control saveInputs">
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="col-sm-4 control-label" for="clone_payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="clone_payrollType" id="clone_payrollType" class="form-control" disabled>
                                <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                            </select>
                            <input type="hidden" name="clone_templateType" id="clone_templateType" >
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">

    $(document).ready(function() {
        fetch_templates();
        $('.headerclose').click(function(){
            fetchPage('system/hrm/pay_sheet_template','Test','HRMS');
        });

        $('#newTemplate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                templateName     : {validators : {notEmpty:{message:'Template Name is required.'}}}
            },
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();
            var $form       = $(e.target);
            var bv          = $form.data('bootstrapValidator');
            var postData    = $form.serializeArray();

            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Template_paysheet/createTemplate'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#newTemplateModel').modal('hide');
                        setTimeout(function(){
                            templateLoad(data[2], '' );
                        }, 300);

                    }
                },
                error : function() {
                    stopLoad();
                    myAlert('e','An Error Occurred! Please Try Again.');
                }
            });

        });

        $('#cloneTemplate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                clone_templateName     : {validators : {notEmpty:{message:'New Template Name is required.'}}}
            }
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();
            var $form       = $(e.target);
            var bv          = $form.data('bootstrapValidator');
            var postData    = $form.serializeArray();

            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Template_paysheet/cloneTemplate'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#cloneTemplateModel').modal('hide');
                        setTimeout(function(){
                            fetch_templates(data[2]);
                        }, 200);

                    }
                },
                error : function() {
                    stopLoad();
                    myAlert('e','An Error Occurred! Please Try Again.');
                }
            });

        });

    });

    function fetch_templates(selectedID=null){
        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);

        var Otable = $('#templateTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Template_paysheet/fetch_templates'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['templateID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".switch-chk").bootstrapSwitch();
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_matching_records_found'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "templateID"},
                {"mData": "documentCode"},
                {"mData": "templateDescription"},
                {"mData": "status"},
                {"mData": "defaultTemplate"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "isNonPayroll","value": $("#isNonPayroll").val()});
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

    function new_template(){
        $('.saveInputs').val('');
        $('#newTemplate_form').bootstrapValidator('resetForm', true);
        $('#newTemplateModel').modal({backdrop: "static"});
    }

    function referBackConformation(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'referID': id},
                    url: "<?php echo site_url('Template_paysheet/referBack'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            fetch_templates(id);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function templateDelete(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'referID': id},
                    url: "<?php echo site_url('Template_paysheet/deleteTemplate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            fetch_templates();
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function templateLoad(id, action ){
        fetchPage('system/hrm/pay_sheet_template_build',0,'HRMS','', id)
    }

    function changeStatus(id, templateType){
        var status = ( $('#status_'+id).is(":checked") )? 1 : 0;
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Template_paysheet/statusChangePaysheetTemplate'); ?>',
            data: {'hidden-id': id, 'status':status, 'payrollType':templateType},
            dataType: 'json',
            beforeSend: function () { startLoad(); },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                setTimeout(function(){ fetch_templates(id); }, 400);

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })
    }

    function templateClone(id, description){
        $('#cloneTemplate_form').bootstrapValidator('resetForm', true);
        $('#cloneTemplateModel').modal('show');
        $('#original-templateName').val($.trim(description));
        $('#original_templateID').val($.trim(id));
        $('#clone_payrollType').val( $('#isNonPayroll').val() );
        $('#clone_templateType').val( $('#isNonPayroll').val() );

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-06-16
 * Time: 12:18 PM
 */