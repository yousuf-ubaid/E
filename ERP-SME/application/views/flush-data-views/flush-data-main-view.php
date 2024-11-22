<?php 
if($company_det['isPartnerCompany'] != 3){
    //if this company not a demo one
?>    

<div class="col-md-12" style="text-align: center; margin-top: 10%">    
    <span class="alert alert-danger" >
        <b><?=$company_det['company_name']?> [ <?=$company_det['company_code']?> ] </b> is not a demo company
    </span>
    <br/><br/>
    <span style="">
        <a href="<?=site_url('/companyAdmin')?>"> <i class="fa fa-backward"></i> &nbsp; back</a>
    </span>
</div>
<?php 
   exit;
}
?>
<style>
    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }

    .multiselect2-container.dropdown-menu{
        width: 300px !important;
    }

    #close-btn{
        color: red;
        font-weight: bolder;
        padding: 0px 4px;
    }

    .product-assign{
        display: none;
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }

    .dataTableBtn{
        padding: 3px 5px;
        font-size: 10px;
    }

    #loding-cls{
        font-size: 13px;
    }

    .flush-loadin-cls{
        color: chocolate;
        font-weight: bolder;
    }

    #log-module-title{
        padding: 0 4px 0 4px;
    }
</style>

<section class="content" style="padding-top: 50px">
    <div class="col-md-12">
        <div class="box">                        
            <div class="box-header with-border">
                <h3 class="box-title">Flush Data</h3>
                
                <span class="pull-right">
                    Company : <b><?=$company_det['company_name']?> [ <?=$company_det['company_code']?> ] </b>                                                                 
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border" id="flushHeader-frm-title"> New Flush Header</legend>
                            <?=form_open('', 'id="frm-flushHeader" class="form-horizontal" autocomplete="off" role="form" 
                                onsubmit="save_flushHeader(event)"'); ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>
                                <div class="col-sm-5">
                                    <input type="text" name="description" id="description" class="form-control">
                                </div>
                                <input type="hidden" name="company_id" value="<?=$companyID?>">
                                <input type="hidden" name="flush_id" id="flush_id" value="">
                            </div>

                            <div class="box-footer">
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" id="flushHeader-btn">
                                        Save
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-default btn-sm" type="button" onclick="reset_flushHeader_frm()">
                                        Reset
                                    </button>
                                </div>
                            </div>
                            <?=form_close(); ?>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                    <fieldset class="scheduler-border">
                            <legend class="scheduler-border"> Flush Data Headers </legend>
                            <div class="table-responsive">
                                <table id="headers_table" class="<?=table_class()?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th> 
                                        <th style="width: 100px">Description</th> 
                                        <th style="width: 100px">Date</th> 
                                        <th style="min-width: 18%">Status </th>
                                        <th style="min-width: 18%">&nbsp;</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>                    
                        </fieldset>                        
                    </div>
                </div>                            
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="flushDet-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" id="flushDet-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    Modules - <span id="flushHeaders" style="font-size: 14px"></span>                
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 det-content" id="status-content">
                        <div class="table-responsive">
                            <table id="flush_modules_table" class="<?=table_class()?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th> 
                                    <th style="width: 100px">Module</th>                                        
                                    <th style="min-width: 18%">Status </th>
                                    <th style="min-width: 18%">&nbsp;</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="col-sm-12 det-content" id="log-content">

                    <div class="col-md-12">
                        <div class="box" style="border: 1px solid #d2d6de; border-top: 3px solid #d2d6de;">                        
                            <div class="box-header with-border">
                                <h3 class="box-title" id="log-module-title"></h3>                                

                                <div class="box-tools pull-right">                                    
                                    <button id="" class="btn btn-box-tool" type="button" onclick="errorLog_toggle()">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="errLog_table" class="<?=table_class()?>">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 5%">#</th>
                                            <th style="width: 100px">Process Time</th>
                                            <th style="width: 100px">Error Message</th>
                                            <th style="width: 100px">Query</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>          
                            </div>                            
                        </div>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" >
                    Close
                </button>
            </div>            
        </div>
    </div>
</div>
