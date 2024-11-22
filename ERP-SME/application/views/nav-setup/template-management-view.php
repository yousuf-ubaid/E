<?php
$formCatID = $det['FormCatID'];
?>
<style>
    .nav-des-cls{
        font-weight: bold
    }

    .nav-des-cls span{
        padding-right: 10px;
        font-size: 16px
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-11">            
            <div class="nav-des-cls">
                <span style="font-weight: normal; paddng-right:10px">
                    Navigation :
                </span>
                <span><?=$det['moduleDes']?></span> <span> > </span>
                <span><?=$det['masterDes']?></span> <span> > </span>
                <span><?=$det['navDes']?></span>
            </div>
        </div>
        <div class="col-sm-1"> 
            <button class="btn btn-primary btn-sm pull-right tmp-btn" id="new-tmp-btn" onclick="templateTabView('frm-view')">
                <i class="fa fa-plus-circle"></i> &nbsp; New Template
            </button>

            <button class="btn btn-primary btn-sm pull-right tmp-btn" id="back-tmp-btn" onclick="templateTabView('tbl-view')">
                <i class="fa fa-backward"></i> &nbsp; Back 
            </button>
        </div>
    </div>

    <div class="col-sm-12"> <hr/> </div>

    <div class="col-sm-12 template-tab" id="tbl-view">
        <div class="table-responsive">
            <table class="<?=table_class()?>" id="template_tbl">
                <thead>
                    <tr>
                        <th>#</th>                        
                        <th>Template Name</th>
                        <th>Page Url</th>
                        <th>Create Page Url</th>
                        <th>Is Default</th>                        
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>    
    </div>

    <div class="col-sm-12 template-tab" id="frm-view">
        <?=form_open('', 'role="form" id="template_form" autocomplete="off"');?>
        <fieldset class="scheduler-border" style="margin-top: 0px">
            <legend class="scheduler-border" id="frm-temp-title">New Template</legend>

            <input type="hidden" name="nav_description" value="<?=$det['navDes']?>">
            <input type="hidden" name="formCatID" value="<?=$formCatID?>">
            <input type="hidden" name="templateID" id="templateID" value="0">

            <div class="form-group col-sm-4">
                <label>Template Name </label>
                <input type="text" class="form-control" id="template_name" name="template_name">
            </div>
            
            <div class="form-group col-sm-8">
                <label>Page Url </label>
                <input type="text" class="form-control" id="tem_page_url" name="page_url">
            </div>

            <div class="form-group col-sm-8">
                <label>Create Page Url </label>
                <input type="text" class="form-control" id="tem_create_page_url" name="create_page_url">
            </div>      

            <div class="col-sm-12"> <hr/> </div>

            <div class="col-sm-12">
                <button type="button" class="btn btn-default btn-sm pull-right" onclick="reset_frm()" style="margin-left: 10px">
                    Reset
                </button>
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_template()">Save</button>
            </div>
        </fieldset>
        <?=form_close();?>
    </div>
</div>

<script>
    let template_tbl = null;

    $(document).ready(function () {
        load_template_tbl();
        templateTabView('tbl-view');
    });

    function save_template(){
        let postData = $('#template_form').serializeArray();
        let urlSlug = ($('#templateID').val() == 0)? 'template_save': 'template_update';

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "<?=site_url('Dashboard/');?>"+urlSlug,
            data: postData,
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    let type = ($('#templateID').val() == 0)? 'insert': 'update';  
                    templateTabView('tbl-view');
                    load_template_tbl();

                    if( data['failed_db'] !== null ){                        
                        template_failDB_msg( data['failed_db'], type );
                        return false;
                    }
                }             
                ajax_toaster(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_template_tbl(){
        template_tbl = $('#template_tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_nav_templates'); ?>",
            "aaSorting": [[0, 'DESC']],
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
            "initComplete": function() { 
            },
            "columnDefs": [
                {'orderable': false, 'targets': [4,5]}
            ],
            "aoColumns": [                              
                {"mData": "TempMasterID"},                
                {"mData": "TempPageName"},
                {"mData": "TempPageNameLink"},
                {"mData": "createPageLink"},
                {"mData": "isDefault"},                
                {"mData": "action"}                
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'formCatID', 'value':  '<?=$formCatID?>'});

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

    function templateTabView(view){
        $('.template-tab, .tmp-btn').hide();
        $('#'+view).fadeIn('slow').show()
    
        let showBtn = (view == 'tbl-view')? $('#new-tmp-btn'): $('#back-tmp-btn');
        showBtn.show();

        reset_frm();
    }

    function reset_frm(){
        $('#frm-temp-title').html(' &nbsp; New Template &nbsp; ');
        $('#template_form')[0].reset();
        $('#templateID').val(0);
    }

    function edit_template(obj){
        templateTabView('frm-view');
        
        $('#frm-temp-title').html(' &nbsp; Edit Template &nbsp; ');

        let det = get_dataTable_det('template_tbl', obj);
                
        $('#templateID').val( det['TempMasterID'] );
        $('#template_name').val( det['TempPageName'] );
        $('#tem_page_url').val( det['TempPageNameLink'] );
        $('#tem_create_page_url').val( det['createPageLink'] );        
    }

    function delete_template_conf(id, des){
        swal(
            {
                title: "Are you sure?",
                text: "You want to delete this ( "+des+" ) template.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConf) {
                if(isConf){
                    delete_template(id);
                }                 
            }
        );
    }

    function delete_template(id){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "<?=site_url('Dashboard/template_delete');?>",
            data: {'templateID': id},
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){                    
                    load_template_tbl();
                    if( data['failed_db'] !== null ){
                        template_failDB_msg( data['failed_db'], 'delete' );
                        return false;
                    }
                }
                ajax_toaster(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function template_failDB_msg(data, type){
        let msg = '<b>Please note that, Fail to ' + type;
        msg += ' the template in following DB`s.</b>';
        msg += '<br/> &nbsp; - &nbsp; '+data;

        bootbox.alert({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Warning!</strong>',
            message: msg, 
            callback: function() {

            }
        });
    }
</script>