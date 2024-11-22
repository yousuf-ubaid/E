<?php
$company_id = trim($this->uri->segment(3));

$product_list = product_list([
                    'placeHolder'=> 'Select a product',
                    'isCustom'=> true
                ]);
?>

<style>
    .wbrd{ border: 1px solid;}

    #nav-search{
        height: 28px;
        background-color: #fff;
    }

    .list-group-item{
        color: black !important;
    }

    .list-group-item.active{
        background-color: #d4d4d4;
    }

    .list-group-item:hover{        
        background-color: #FFFFAA !important;        
        opacity: 200;
    }

    .sub-nav-description{
        color : #fff
    }

    .list-group-item:hover .sub-nav-description{
        color: #000;        
    }

    .sub-nav-description:hover{     
        cursor: default;
    }

    .prod-sub-cls{
        font-weight: bold;
        color: black !important;
        padding-left: 50px;
    }

    .prod-top-level{
        margin-right: 30%;
    }

    .nav-sort-order, .search_in_module{
        width: 60px;
        margin: auto 50px auto 50px;
        color: black;
        font-size: 12px    
    }

    .search_in_module{
        width: 100px
    }

    .prod-srh > .panel-group {        
        padding-right: 10%;
        padding-left: 10%;
    }

    #drop-module-disp{
        height: 30px;
        display: none;
    }

    .tool-box-icon {
        color: black !important;
    }
</style>
 
<div class="row">
    <?=form_open('', 'id="frm-nav-company" class="form-horizontal"');?>
    <div class="col-sm-12">   
        <div class="col-sm-6 brd">
            <div class="form-group" style="margin-bottom: 0px">
                <label class="col-sm-1 control-label">Product</label>
                <div class="col-sm-4">
                    <input type="hidden" name="company_id" value="<?=$company_id?>"/>
                    <?=form_dropdown('productID', $product_list, null, 'id="com_productID" class="select2" onchange="load_product_nav(this)"')?>
                </div>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary btn-sm" style="margin-top: 2px" onclick="assign_product()">
                        Save
                    </button>

                    <button type="button" class="btn btn-default btn-sm" style="margin: 2px 5px" 
                        onclick="load_nav_template_setup_view()">
                        Manage Templates    
                    </button>
                </div>
            </div>
        </div>

        <div class="col-sm-1 brd"> &nbsp; </div>    
        <div class="col-sm-3 brd"> 
            <span class="common-btn-top" id="product-tab-btn">
                <select class="pull-right" id="drop-module-disp" onchange="selected_modules_only(this)">
                    <option value="all">Display All modules</option>
                    <option value="selected">Display Selected modules</option>
                </select>

                <button type="button" class="btn btn-default btn-sm pull-right" onclick="fn_nav_common_collapse('prod')"
                    style="margin-right: 10px;">
                    <i class="fa fa-plus" id="prod-toggle"></i> &nbsp; Toggle All
                </button>                 
            </span>
        </div>    
        <div class="col-sm-2 brd">        
            <div class="sidebar-form" style="margin: 0px">
                <div class="input-group">
                    <input type="text" id="nav-search" class="form-control" onkeyup="search_prod_nav()" 
                        placeholder="Search Modules..." autocomplete="off" value="">
                    <span class="input-group-addon" style="border: none;">
                        <i class="fa fa-search"></i>                    
                    </span>
                </div>             
            </div>             
        </div>
    </div>


    <div class="col-sm-12"> <hr/> </div>

    <div class="col-sm-12">          
    </div>
    
    <div class="col-sm-12" id="wrap-top" style="display: none">
        <div class="col-sm-12" id="search-no-data" style="display: none">No data found... </div>

        <span id="pr-module-container">
        <?php $this->load->view('nav-setup/product-modules-view');?> 
        </span>        
    </div>
    <?=form_close()?>
</div>

<div class="modal fade" id="company-change-template-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?=form_open('', 'id="company-change-template-frm"')?>
            <input type="hidden" name="company_id" value="<?=$company_id?>"/>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    Navigation Template Setup - 
                    <span class="company-name-header" style="font-size: 14px"></span>                
                </h3>
            </div>
            <div class="modal-body" id="company-change-template-view">                
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" onclick="update_default_template()">
                    Save
                </button>                
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" >
                    Close
                </button>
            </div>
            <?=form_close();?>
        </div>
    </div>
</div>

<script> 
function load_product_nav(obj){
    $('#wrap-top').hide();
    $('.prod-chk-all').prop('checked', false);
    let config = 'N'; 
    $('#drop-module-disp').hide();

    if(obj.value == ''){
        return false;
    }    

    if(obj.value == -1){ //If cutome navigation load as config othervice just a view
        config = 'Y'; 
        $('#drop-module-disp').val('all').show();
    }

    $.ajax({
        type: 'post',
        dataType: 'JSON',            
        url: "<?=site_url('Dashboard/load_product_modules');?>",
        data: {'productID': obj.value, 'config': config, 'company_id': '<?=$company_id?>'},
        cache: false,
        beforeSend: function () {                
            startLoad();     

            prod_toggle.removeClass('fa-plus').addClass('fa-minus');    
            fn_nav_common_collapse('prod');
        },
        success: function (data) {
            stopLoad();            
            $('#wrap-top').show();
            if( data[0] == 's' ){
                $('#pr-module-container').html(data['view']);                                    
            }                           
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', errorThrown);
        }
    });
}

function assign_product(verify=0){
    let post_data = $('#frm-nav-company').serializeArray();
    post_data.push({'name': 'warning_verify', 'value': verify})

    $.ajax({
        type: 'post',
        dataType: 'JSON',            
        url: "<?=site_url('Dashboard/clientDB_activate_navigations');?>",
        data: post_data,
        cache: false,
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            ajax_toaster(data, null, nav_activetion_verify);

            if(data[0] == 's'){
                confirm_change_template();                
            }       
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', errorThrown);
        }
    });
}

function nav_activetion_verify(data){
    bootbox.confirm({
        title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Warning!</strong>',
        message: '<b>Please note that.</b><br/>'+data[1],
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
                assign_product(1);
            }
        }
    });
}

function confirm_change_template(){
    swal(
        {
            title: "",
            text: "Do you want to make change on default templates?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "No",
            confirmButtonText: "Yes"
        },
        function (isConf) {
            if(isConf){
                load_nav_template_setup_view();
            }
        }
    );
}

function load_nav_template_setup_view(){
    $.ajax({
        type: 'post',
        dataType: 'JSON',            
        url: "<?=site_url('Dashboard/load_nav_template_setup_view');?>",
        data: {'company_id': '<?=$company_id?>'},
        cache: false,
        beforeSend: function () {                
            startLoad();
        },
        success: function (data) {
            stopLoad();                        
            if( data[0] == 's' ){
                $('#company-change-template-view').html( data['view'] );
                $('#company-change-template-modal').modal('show');
            }                           
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', errorThrown);
        }
    });
}

function update_default_template(){
    let post_data = $('#company-change-template-frm').serializeArray();    

    $.ajax({
        type: 'post',
        dataType: 'JSON',            
        url: "<?=site_url('Dashboard/client_default_template_setup');?>",
        data: post_data,
        cache: false,
        beforeSend: function () {                
            startLoad();
        },
        success: function (data) {
            stopLoad();
            ajax_toaster(data);
            if( data[0] == 's' ){
                $('#company-change-template-modal').modal('hide');
            }                           
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            myAlert('e', errorThrown);
        }
    });
}
</script>