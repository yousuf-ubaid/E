<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_request_letters');

?>

<div class="table-responsive">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?=$this->lang->line('common_document_code')?> : <?=$masterData['documentCode']?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_date')?> </label></div>
                <div class="col-sm-7">: <?=$masterData['request_date']?></div>
            </div>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_letter_type')?> </label></div>
                <div class="col-sm-7">: <?=$masterData['letter_type_des']?></div>
            </div>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_letter_addressed')?> </label></div>
                <div class="col-sm-7">: <?=$masterData['address_to']?></div>
            </div>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_identity')?> </label></div>
                <div class="col-sm-7">: <?=($masterData['identity_type'] == 2)?'Resident Card/ID No': 'Passport'?></div>
            </div>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_identity_no')?> </label></div>
                <div class="col-sm-7">: <?=$masterData['identity_no']?></div>
            </div>

            <?php if($masterData['letter_type'] == 1){?>
            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_bank')?> </label></div>
                <div class="col-sm-7">: <?=$bankData['bankName'].' | '.$bankData['accountNo']?></div>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_language')?> </label></div>
                <div class="col-sm-7">: <?=($masterData['letter_language'] == 'E')? 'English': 'Arabic'?></div>
            </div>

            <?php if($masterData['approvedYN'] == 1){?>
                <div class="row">
                    <div class="col-sm-5"><label><?=$this->lang->line('common_signature')?> </label></div>
                    <div class="col-sm-7">: <?=$signature_data?></div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-sm-5"><label><?=$this->lang->line('common_narration')?> </label></div>
                <div class="col-sm-7">: <?=$masterData['narration']?></div>
            </div>
        </div>
    </div>
</div>
