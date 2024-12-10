<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$companyID = current_companyID();
$current_user = current_userID(); 

?>
<style>
    .main-footer{
        margin-left: 0px;
    }

    .action-div{
        text-align: center;
    }

    .btn-subscription{
        padding: 4px 6px;
        background: #fff;
        border: 2px solid #ccc;
        border-radius: 4px;
        width: 80px;
        height: 35px;
    }

    .btn-paid{
        color: #88cc5d;
        font-weight: bold;
    }

    .btn-un-paid{
        color: #a61700;
        font-weight: bold;
    }

    .pay-input{
        float: left;
        margin-right: 10px;
    }

    .sweet-alert {
        z-index: 20000000 !important;
    }
</style>

<link href="<?php echo base_url('plugins/datatables/subscription-style-data-table.css'); ?>" rel="stylesheet">

<div class="" style="margin-top: 70px; min-height: 560px;">
    <div class="row">
        <div class="col-md-5 pull-right">
            <div class="table-responsive" style="margin-bottom: 15px;">
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr>
                        <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_paid');?></td>
                        <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_unpaid');?></td>
                        <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?=$this->lang->line('common_pending_for_verification');?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="subscription_master_table" class="table table-condensed custom_data_table">
            <thead>
            <tr>
                <th style="min-width: 10%"><?=$this->lang->line('common_invoice_no');?></th>
                <th style="min-width: 10%"><?=$this->lang->line('common_invoice_date');?></th>
                <th style="min-width: 10%">Due Date</th>
                <th style="min-width: 10%"><?=$this->lang->line('common_total');?></th>
                <th style="min-width: 10%"><?=$this->lang->line('common_status');?></th>
                <th style="min-width: 10%"></th>
            </tr>
            </thead>

            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="invoice_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="modal-body" id="invoice_body">
            </div>
            <div class="modal-footer" style="border-top: 1px solid #bdbdbd;">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?=$this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="invoice_modal2" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 80%">
            <div class="modal-content">
                <div class="modal-body" id="invoice_body1">
                </div>
                <div class="modal-footer" style="border-top: 1px solid #bdbdbd;">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?=$this->lang->line('common_Close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_creditcard_receipt" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 38%">
        <div class="modal-content">
            <div class="modal-body" id="credit_card_receiptview">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?=$this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
    </div>
    
    <div class="modal fade" id="test_modal_view" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 38%">
        <div class="modal-content">
            <div class="modal-body" id="credit_card_receiptview_test">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?=$this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
    </div>
<?php if(PAY_PAL_ENABLED){ ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?=$pay_pal_client_id?>&currency=USD"></script>
<?php }
