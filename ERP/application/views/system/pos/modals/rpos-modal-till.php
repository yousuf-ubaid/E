<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="till_modal" class="modal fade" data-keyboard="false" style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <h5 class="modal-title" id="tillModal_title"><?php echo $this->lang->line('posr_start_day');?><!--Start Day--></h5>
            </div>
            <div class="modal-body" style="padding: 10px; height: auto">
                <div class="smallScroll" id="currencyDenomination_data" align="center"
                     style="height: auto; overflow-y: scroll"></div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <input type="hidden" id="isStart"/>
                <button class="btn btn-primary btn-xs" type="button" id="tillSave_Btn">
                    <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                </a>
                <a href="<?php echo site_url('dashboard') ?>" class="btn btn-default btn-xs"><?php echo $this->lang->line('common_Close');?><!--Close--></a>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="testModal" class="modal fade" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">

            <div class="modal-body" style="padding: 0px; height: 0px">
                <div class="alert alert-success fade in" style="margin-top:18px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"
                       style="text-decoration: none">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size:15px"></i>
                    </a>
                    <strong><?php echo $this->lang->line('posr_session_successfully_closed');?><!--Session Successfully closed-->.</strong> <?php echo $this->lang->line('posr_redirect_in');?><!--Redirect in--> <span id="countDown"> 5 </span>
                    <?php echo $this->lang->line('posr_seconds');?><!--Seconds-->.
                </div>
            </div>
        </div>
    </div>
</div>