<?php 
$data['nav_modules_arr'] = get_navigation_modules(['isDrop'=> true]);
?>
<style>
   .sub-container button.multiselect2.dropdown-toggle{
        padding: 0px;
    }

    .form-inline.editableform{
        padding-left: 10px;
        padding-right: 10px;
    }

    .frm-filtter-label{
        padding-right: 10px;
    }

    .panel-heading-nav2 {
        padding-right: 4px;
        //background-color: #303a4a !important;
        background-color: #b2ad7f !important;
        /* background-color: #337ab7 !important;
        border-color: #337ab7 !important; */

        color: #fff !important;
    }

    .tool-box-icon{
        color: black !important;
    }
    .nav-common-collapse, .prod-common-collapse{
        color: #97a0b3;   
    }

    .nav-collapse-body, .prod-collapse-body{
        height: auto !important;
    }

    #common-btn-container{
        margin-top: 12px;
        padding-right: 10px
    }

    .affix {
        width: 100%;
        top: 0px;
        position: fixed;
        overflow: visible!important;
        padding: 28px 0px 0px 0px;
        z-index: 9998;
        margin: 0 auto;
        border: 1px solid #ccc;
        background: #000
    }

    .nav-srh-div > .sidebar-form{        
        margin: 0px 0px 5px;
    }

    #ajax-nav-search{
        background-color: #fff;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">                        
            <div class="box-header with-border">
                <h3 class="box-title">Navigation Setup</h3>
                <span class="">
                                                                 
                </span>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
                    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">                       
                        <li class="active">
                            <a href="#nav-tab" data-toggle="tab" aria-expanded="false" onclick="common_btn_display('nav-tab-btn')">
                                Navigations
                            </a>
                        </li>

                        <li class="active1">
                            <a href="#product-tab" data-toggle="tab" aria-expanded="true" onclick="common_btn_display('product-tab-btn')">
                                Products Setup
                            </a>
                        </li>
                        

                        <div id="common-btn-container">                        
                            <span class="common-btn-top" id="nav-tab-btn" style="display: none1">                                
                                <div class="col-sm-2 pull-right">
                                    <button type="button" class="btn btn-primary btn-xs pull-right" onclick="add_navigation()" style="margin-left: 10px;">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    
                                    <button type="button" class="btn btn-primary btn-xs pull-right" onclick="fn_nav_common_collapse('nav')">
                                        <i class="fa fa-plus" id="nav-toggle"></i> &nbsp; Toggle All
                                    </button>
                                </div>

                                <div class="col-sm-5">&nbsp;</div>
                            </span>

                            <span class="common-btn-top" id="product-tab-btn" style="display: none">
                                <button type="button" class="btn btn-primary btn-xs pull-right" onclick="fn_nav_common_collapse('prod')"
                                    style="margin-left: 10px;">
                                    <i class="fa fa-plus" id="prod-toggle"></i> &nbsp; Toggle All
                                </button> 

                                <select class="pull-right" id="drop-module-disp" onchange="selected_modules_only(this)">
                                    <option value="all">Display All modules</option>
                                    <option value="selected">Display Selected modules</option>
                                </select>
                            </span>
                        </div>
                    </ul>
                    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                        <div class="tab-pane active" id="nav-tab">
                            <div class="form-group" style="margin:20px 0px;">                                
                                <select id="select2-nav-srch" onchange="edit_nav()"></select>
                            </div>
                            <?php $this->load->view('nav-setup/nav-setup-view', $data);?> 
                        </div>
                        
                        <div class="tab-pane active1" id="product-tab">                             
                            <?php $this->load->view('nav-setup/product-nav-setup-view', $data);?> 
                        </div>
                    </div>
                </div>                                                                            
            </div>                        
        </div>        
    </div>
</section>

<script type="text/javascript" src="<?= base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
