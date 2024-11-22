<?php
$product_list = product_list([
                    'placeHolder'=> 'Select a product'
                ]); 
?>

<style>  
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

</style>
 
<div class="row">
    <?=form_open('', 'id="frm-nav-product" class="form-horizontal"');?>
    <div class="col-sm-12">   
        <div class="col-sm-6 brd">
            <div class="form-group" style="margin-bottom: 0px">
                <label class="col-sm-1 control-label">Product</label>
                <div class="col-sm-4">
                    <?=form_dropdown('productID', $product_list, null, 'class="select2" onchange="load_product_nav(this)"')?>
                </div>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary btn-sm" style="margin-top: 2px" onclick="save_product_nav()">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <div class="col-sm-4 brd"> &nbsp; </div>    
        <div class="col-sm-2 brd">        
            <div class="sidebar-form">
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
