<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$tittle = $this->lang->line('config_template_setup');
echo head_page($tittle, false);

$masterID = trim($this->input->post('page_id'));


$description = trim($this->input->post('data_arr'));
$master_page_url = trim($this->input->post('master_page_url'));

$type = [
    '' => 'Select a type',
    '2' => 'Header',
    '3' => 'Group Total',
];

$details = get_fm_templateDetails($masterID);
$templateType = $details['master_data']['templateType']; /* 1 => Fund management, 2 => MPR*/
$rid=$details['master_data']['reportID'];
if($rid==6)
{
   $masterCategory='BS'; 
}
else if($rid==5)
{
    $masterCategory='PL'; 
}


$reportID = $details['master_data']['reportID'];
$detData = $details['view'];
$confirmedYNstatus = $details['master_data']['confirmedYN'];
$companyID = current_companyID();
$reportcheck = $this->db->query("select count(t1.GLAutoID) as CountGl from  (SELECT * FROM
srp_erp_chartofaccounts  WHERE companyID = $companyID
AND masterAccountYN = 0 
AND masterCategory = '$masterCategory'
AND GLAutoID NOT IN (SELECT GLAutoID FROM srp_erp_companyreporttemplatelinks 
WHERE templateMasterID = $masterID  And  GLAutoID is not null  group by GLAutoID  ))t1")->result_array();
$reportval=$reportcheck[0]['CountGl'];


?>
<style>
    legend{ font-size: 16px !important; }

    .mini-header{
        //background: #596b8e;
        background: #05668d;
        color: #f5f5f5;
        font-weight: bolder;
        font-size: 13px;
    }

    .sub1{
        background: #E8F1F4;
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
    }

    .sub2{
        background: #ffffff;
        color: #000;
        font-weight: bolder;
        font-size: 13px;
    }

    .sortTxt{
        width: 50px;
        color: #000000;
    }

    #detailMaster_table th{
        background-color: #f0f3bd;
        border: 1px solid #ccc;
    }

    #gross_column{
        width: 200px !important;
    }

    .table-responsive {
        width: 100% !important;
    }
  
</style>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row" style="margin-top: 15px"> </div>
<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td width="85px" style="vertical-align: middle; font-weight: bold"><?php echo $this->lang->line('common_description');?> : <!--Description--></td>
                    <td class="bgWhite" colspan="2" style="vertical-align: middle;">
                        <a href="#" data-type="text" data-placement="bottom" data-title="<?php echo $this->lang->line('config_edit_description');?> :"
                           data-pk="<?php echo $description?>" id="description_xEditable" data-value="<?php echo $description; ?>">
                            <?php echo $description?>
                        </a>
                    </td>

                    <?php

                    if($templateType == 2){
                        $gross_rows_arr = $details['gross_rows_arr'];
                        $is_gross_rev = $details['is_gross_rev'];
                        ?>
                
                    <?php } ?>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>

<div class="row" style="margin-top: 15px;cursor: pointer;">
<?php if($reportval!=0){ ?>
<div class="col-md-12" onclick="glmismatch('<?php echo $companyID ?>','<?php echo $masterCategory?>','<?php echo $masterID?>')">

<?php $norecfound='Not all GL codes are linked. Click Here to See Details';
             echo warning_message($norecfound);/*"No Records Found!"*/?>
</div>
<?php } ?>
    <div class="col-md-12">
        <fieldset class="scheduler-border" style="">
            <legend class="scheduler-border"><?php echo $this->lang->line('common_details');?> <!--Details--></legend>
            <div class="row" style="margin-bottom: 4px;">&nbsp;</div>

            <div class="row">
                <div class="col-sm-12" style="margin-bottom: 10px">
                  <!--Confirm-->
                    <button class="btn btn-primary pull-right" type="button" id=" " onclick="new_header_or_group(null,'<?php echo $confirmedYNstatus?>')">
                        <?php echo $this->lang->line('config_new_header_or_group_total');?><!--New Header/ Group Total-->
                    </button>
                    <button class="btn btn-primary pull-right" type="button" id=" " onclick="update_sortOrder()" style="margin-right: 10px;">
                        <?php echo $this->lang->line('config_update_sort_order');?><!--Update Sort Order-->
                    </button>
                <?php if($templateType != 1){?>
                   <?php if($confirmedYNstatus==1)
                       {?>
                           <button class="btn btn-warning pull-right" type="button" id=" " onclick="Un_confirmation()" style="margin-right: 10px;">
                               <?php /* echo $this->lang->line(''); */?>Un Confirm
                           </button>
                    <?php }else {?>
                       <button class="btn btn-success pull-right" type="button" id=" " onclick="confirmation()" style="margin-right: 10px;">
                           <?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                       </button>
                    <?php }?>
                    <?php }?>
                </div>
                <?php echo form_open('', 'role="form" id="sortOrderUpdate_frm" autocomplete="off"'); ?>
                <div class="table-responsive">
                    <table id="detailMaster_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 80%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_type');?></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_sort_order');?></th>
                            <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php echo  $detData;?>
                        </tbody>
                    </table>
                </div>
                <?php echo form_close(); ?>
            </div>
        </fieldset>
    </div>
</div>
<div class="modal fade" id="templateConfig_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_new_item');?><!--New Item--><span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('','role="form" id="frm_add_new_item" autocomplete="off"'); ?>
                <input type="hidden" name="masterID" value="<?php echo $masterID; ?>">
                <input type="hidden" name="subMaster" id="subMaster" value="null">

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label><?php echo $this->lang->line('common_type');?></label>
                        <select name="itemType" id="itemType" class="form-control itemType" onchange="displayAccountType()"> </select>
                    </div>

                    <?php if($templateType == 1){ ?>
                    <div class="form-group col-sm-3 accountType-container">
                        <label>Account Type</label>
                        <select name="accountType" id="accountType" class="form-control">
                        <option value="I"><?php echo $this->lang->line('common_income');?><!--Income--></option>
                            <option value="E"><?php echo $this->lang->line('common_expence');?><!--Expense--></option>
                            <option value="A"><?php echo $this->lang->line('common_essets');?><!--Assets--></option>
                            <option value="L"><?php echo $this->lang->line('common_liability');?><!--Liability--></option>
                          
                        </select>
                    </div>
                    <?php } ?>



                    <div class="form-group col-sm-6">
                        <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>
                    <div class="form-group col-sm-3">
                        <label><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                        <input type="text" name="sortOrder" id="sortOrder" class="form-control number">
                    </div>
                    <?php if($templateType != 1){ ?>
                    <div class="form-group col-sm-6 hide defaulttype">
                        <label class="title">Gross Revenue</label>
                        <div class="skin skin-square">
                            <div class="skin-section extraColumnsdefaulttype" id="extraColumns">
                                <input id="defaulttype" type="checkbox"
                                       data-caption="" class="columnSelected" name="defaulttype" value="0">
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-4 defaulttypecate hide">
                        <label>Default Type</label>
                        <?php echo form_dropdown('accountTypecater', array('' => 'Select Default Type', '1' => 'Uncategorized Income', '2' => 'Uncategorized Expense'), '', 'class="form-control select2" id="accountTypecater"'); ?>
                    </div>
                    <?php }?>
                </div>

                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="" onclick="save_templateDetail()">
                    <?php echo $this->lang->line('common_save');?>
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="glConfig_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="glConfig_from" autocomplete="off"'); ?>
            <input type="hidden" name="masterID" value="<?php echo $masterID; ?>">
            <input type="hidden" name="detID" id="detID">
            <input type="hidden" name="linkType" id="linkType" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span id="gl-link-title"></span> </h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12" id="gl-config-container"> </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onclick="save_reportTemplateLink()">
                    <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="glmismatch_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="">
           
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <span id="gl-mismatch-title"></span> Unlinked GL codes</h4>
            </div>
            <?php

$reportglcheck = $this->db->query("SELECT 
systemAccountCode,GLSecondaryCode,GLDescription,subCategory
FROM
srp_erp_chartofaccounts 
WHERE
companyID = $companyID
AND masterAccountYN = 0 
AND masterCategory = '$masterCategory'
AND GLAutoID NOT IN (SELECT GLAutoID FROM srp_erp_companyreporttemplatelinks 
WHERE templateMasterID =  $masterID And  GLAutoID is not null  group by GLAutoID)")->result_array();

?>
     
            <div class="modal-body" style="margin-left: 10px">


                 <div class="row">
                    <div class="col-sm-12" id="gl-unlink-head" style="font-weight: bold;">Glcode - GLScondaryCode - GLDescription-Type</div>
                </div>
            <?php  foreach ($reportglcheck as $reportgl) {
                $systemAccountCode=$reportgl['systemAccountCode'];
                $GLSecondaryCode=$reportgl['GLSecondaryCode'];
                $GLDescription=$reportgl['GLDescription'];
                $subCategory=$reportgl['subCategory'];

                if($subCategory=='PLE')
                {
                   $type='Expense';
                }
                else if($subCategory=='PLI')
                {
                    $type='Income';
                }
                else if($subCategory=='BSA')
                {
                    $type='Assets';
                }
                else if($subCategory=='BSL')
                {
                    $type='Liabilities';
                }



                ?>

                <div class="row">
                    <div class="col-sm-12" id="gl-unlink"><?php echo $systemAccountCode; ?>-<?php echo $GLSecondaryCode; ?>-<?php echo $GLDescription; ?>-<?php echo $type; ?></div>
                </div>
               <?php } ?>

            </div>
            <div class="modal-footer">
                
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
          
        </div>
    </div>
</div>




















<div class="modal fade" id="editTitle_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="width: 124%;">
            <?php echo form_open('', 'role="form" id="editTitle_form" autocomplete="off"'); ?>
            <input type="hidden" name="title_id" id="title_id">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('config_title_Edit');?><!--Title Edit--> </h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-md-12" style="height: 45px;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="title_str"> <?php echo $this->lang->line('common_title');?> </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="title_str" name="title_str">
                            </div>

                        </div>
                    </div>
                    <?php if($templateType != 1){ ?>
                    <div class="col-md-12 defaulttypecate hide" style="height: 45px;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="title_str"> Default Type </label>
                            <div class="col-sm-10">
                                <?php echo form_dropdown('accountTypecater_edit', array('' => 'Select Default Type', '1' => 'Uncategorized Income', '2' => 'Uncategorized Expense'), '', 'class="form-control select2" id="accountTypecater_edit"'); ?>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 defaulttype hide" style="height: 45px;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="title_str"> Gross Revenue </label>
                            <div class="col-sm-10">
                                <div class="skin skin-square">
                                    <div class="skin-section extraColumnsdefaulttype" id="extraColumns">
                                        <input id="defaulttype_edit" type="checkbox"
                                               data-caption="" class="columnSelected" name="defaulttype_edit" value="0">
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php }?>

                    <!-- <div class="col-md-12 defaulttypecate hide" style="height: 45px;">
                        <div class="form-group">
                            <label>Default Type</label>
                            <div class="col-sm-10">
                                <?php /*echo form_dropdown('accountTypecater', array('' => 'Select Default Type', '1' => 'Uncategorized Income', '2' => 'Uncategorized Expense'), '', 'class="form-control select2" id="accountTypecater"'); */?>
                            </div>

                        </div>
                    </div>-->
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onclick="update_title()">
                    <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var masterID = '<?php echo $masterID; ?>';
    var description = <?php echo json_encode($description); ?>;
    $(".sortTxt, #sortOrder").numeric({negative: false});
    $('#gross_column').select2({width: '200px !important'});

    $(document).ready(function () {
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('.headerclose').click(function () {
            fetchPage('<?=$master_page_url?>', 'Test', 'Report Template');
        });
    });

    $('#description_xEditable').editable({
        url: '<?php echo site_url('ReportTemplate/update_templateDescription?masterID='.$masterID) ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    var description_xEditable = $('#description_xEditable');
                    setTimeout(function (){
                        description_xEditable.attr('data-pk', description_xEditable.html());
                        description = $.trim(description_xEditable.html());
                    },400);

                }else{
                    var oldVal = $('#description_xEditable').data('pk');
                    setTimeout(function (){
                        $('#description_xEditable').editable('setValue', oldVal );
                    },300);
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    function delete_template_data(linkID, d_type,confirmedYN){
        if(confirmedYN == 0)
        {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>" /*Delete*/,
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>" /*cancel */
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'linkID': linkID, 'd_type': d_type},
                        url: "<?php echo site_url('ReportTemplate/delete_template_data'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                refresh_page();
                                refresh_page();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', errorThrown);
                        }
                    });
                });
        }else
        {
            swal(" ", "Template setup is confirmed already. please Refer back and try again.", "warning");
        }

    }

    function new_header_or_group(subID=null,confirmedYN){
        if(confirmedYN == 0)
        {
            $('.extraColumnsdefaulttype input').iCheck('uncheck');
            $('#frm_add_new_item')[0].reset();
            $('.accountType-container').show();
            var dropData = '<option value="2">Header</option>';
            dropData += '<option value="3">Group Total</option>';

            if(subID != null){
                $('.accountType-container').hide();
                dropData = '<option value="1">Sub Category</option>';
                dropData += '<option value="3">Group Total</option>';
            }


            $('.defaulttypecate').removeClass('hide');
            $('.defaulttype').addClass('hide');

            $('#subMaster').val(subID);
            $('.defaulttype').addClass('hide');
            $('#itemType').empty().append(dropData);
            if($('#itemType').val()==1)
            {
                $('.defaulttypecate').removeClass('hide');
                $('.defaulttype').addClass('hide');

            }else
            {
                $('.defaulttype').addClass('hide');
                $('.defaulttypecate').addClass('hide');
            }

            $('#templateConfig_modal').modal('show');
        }else
        {
            swal(" ", "Document Already Confirmed Please Un Confirm And try Again", "warning");
        }

    }

    function displayAccountType(){
        $('.accountType-container').hide();

        if($('#itemType').val() == 2){
            $('.accountType-container').show();
        }
    }

    function edit_title(id, dis,confirmYn,type,defaulttype,isgrossrev){
        $('.extraColumnsdefaulttype input').iCheck('uncheck');
        if(confirmYn == 0)
        {
            $('#title_id').val(id);
            $('#title_str').val(dis);

            if(type == 1)
            {

                $('#accountTypecater_edit').val(defaulttype).change();
                $('.defaulttypecate').removeClass('hide');
                $('.defaulttype').addClass('hide');
            }else if(type == 3) {
                if (isgrossrev == 1) {
                    $('#defaulttype_edit').iCheck('check');
                }
                else if (isgrossrev == 0) {
                    $('#defaulttype_edit').iCheck('uncheck');
                }
                $('.defaulttypecate').addClass('hide');
                $('.defaulttype').removeClass('hide');
            }else {

                $('.defaulttypecate').addClass('hide');
                $('.defaulttype').addClass('hide');
            }
            $('#editTitle_modal').modal('show');



        }else
        {
            swal(" ", "Template setup is confirmed already. please Refer back and try again.", "warning");

        }

    }

    function sub_item_config(id, dis, reqType){
        $('#detID').val(id);
        $('#linkType').val(reqType);
        dis += (reqType == 'G')? ' - link sub category': ' - link GL';
        $('#gl-link-title').html(dis);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyReportTemplateID: masterID, reqType: reqType, 'id': id},
            url: "<?php echo site_url('ReportTemplate/load_gl_data'); ?>",
            beforeSend: function () {
                startLoad();
                $('#gl-config-container').html('');
            },
            success: function (data) {
                stopLoad();
                $('#glConfig_modal').modal('show');
                $('#gl-config-container').html(data);
                setTimeout(function(){

                }, 400);
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in call back');
            }
        });
    }

    function update_title(){
        var data = $("#editTitle_form").serializeArray();
        var isDefault;
        if ($("#defaulttype_edit").is(':checked')) {
            Isdefault = 1;
        } else {
            Isdefault = 0;
        }
        data.push({name: "Isdefault", value: Isdefault});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/update_title'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#editTitle_modal').modal('hide');

                    refresh_page();
                }
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function save_reportTemplateLink(){
        var data = $("#glConfig_from").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/save_reportTemplateLink'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#glConfig_modal').modal('hide');

                    refresh_page();
                }
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function save_templateDetail(){
        var data = $("#frm_add_new_item").serializeArray();
        var isDefault;
        if ($("#defaulttype").is(':checked')) {
            Isdefault = 1;
        } else {
            Isdefault = 0;
        }
        data.push({name: "Isdefault", value: Isdefault});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/save_reportTemplateDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#templateConfig_modal').modal('hide');
                    refresh_page();
                }
            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
            }
        });
    }
    function glmismatch(cid,mcid,mid){
        $('#glmismatch_modal').modal('show');
      
    }


    function get_configData(id, itemType){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'id': id, 'itemType':itemType},
            url: "<?php echo site_url('ReportTemplate/load_templateConfig'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#templateConfig_modal').modal('show');
                $('#templateConfig-container').html(data);

            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
            }
        });
    }

    function gross_column_update(){
        var gross_col = $('#gross_column').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID, 'gross_column':gross_col},
            url: "<?php echo site_url('ReportTemplate/update_gross_revenue_column'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
            }
        });
    }

    function update_sortOrder(){
        var data = $("#sortOrderUpdate_frm").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/update_sortOrder'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    refresh_page();
                }
            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
            }
        });
    }

    function refresh_page(){
        setTimeout(function(){
            setupTemplate( masterID, description );
        }, 400);
    }
    $( ".itemType" ).change(function() {
        $('.extraColumnsdefaulttype input').iCheck('uncheck');
        if (this.value == 3) {
            $('.defaulttype').removeClass('hide');
            $('.defaulttypecate').addClass('hide');
        } else {
            if(this.value == 1)
            {
                $('.defaulttypecate').removeClass('hide');
                $('.defaulttype').addClass('hide');
            }else
            {

                $('.defaulttype').addClass('hide');
                $('.defaulttypecate').addClass('hide');
            }

        }

    });
    function confirmation() {
        swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {companyReportTemplateID: masterID},
                        url: "<?php echo site_url('ReportTemplate/template_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0]=='s'){
                                refresh_page()
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        ;
    }
    function Un_confirmation() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "You want to Un confirm this document",/*You want to confirm this document*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {companyReportTemplateID: masterID},
                    url: "<?php echo site_url('ReportTemplate/template_unconfirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0]=='s'){
                            refresh_page()
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

        ;
    }
</script>
