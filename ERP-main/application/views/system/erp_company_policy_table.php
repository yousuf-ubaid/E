<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>

<table id="company_policy_table" class="<?php echo table_class(); ?> mb-1">
    <thead>
    <tr>
        <th style="width: 5%;">#</th>
        <th style="width: 55%;"><?php echo $this->lang->line('common_description');?><!--Description--></th>
        <th style="width: 10%;"><?php echo $this->lang->line('config_document_id');?><!--Document ID--></th>
        <th style="width: 30%;"><?php echo $this->lang->line('config_default_value');?><!--Default Value--></th>
    </tr>
    </thead>
</table>

<div class="col-md-12 p-0">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <?php if(is_array($moduleID) && is_array($detail)){
        
        foreach($moduleID as $valueT){             
            if($valueT['moduleID'] == 31){ ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Procurement
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $a=1;
                                   foreach($detail as $valueA){ 
                                   
                                    if($valueA['moduleID'] == 31){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $a;?></td>
                                            <td style="width: 55%;"><?php echo $valueA['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueA['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueA['fieldType'], $valueA['companypolicymasterID'],$valueA['companyValue'],$valueA['documentID'],$valueA['isCompanyLevel'],$valueA['code']);  ?></td>
                                        </tr>
                                <?php  $a++; } }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 32) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading2">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        Inventory
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading2">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $b=1;
                                   foreach($detail as $valueB){ 
                                    
                                    if($valueB['moduleID'] == 32){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $b;?></td>
                                            <td style="width: 55%;"><?php echo $valueB['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueB['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueB['fieldType'], $valueB['companypolicymasterID'],$valueB['companyValue'],$valueB['documentID'],$valueB['isCompanyLevel'],$valueB['code']);  ?></td>
                                        </tr>
                                <?php  $b++; }                                   
                                }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 33) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading3">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                        Accounts Payable
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading3">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $c=1;
                                   foreach($detail as $valueC){ 
                                   
                                    if($valueC['moduleID'] == 33){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $c;?></td>
                                            <td style="width: 55%;"><?php echo $valueC['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueC['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueC['fieldType'], $valueC['companypolicymasterID'],$valueC['companyValue'],$valueC['documentID'],$valueC['isCompanyLevel'],$valueC['code']);  ?></td>
                                        </tr>
                                    <?php  $c++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 34) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading4">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                        Accounts Receivable
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading4">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $d=1;
                                   foreach($detail as $valueD){ 
                                   
                                    if($valueD['moduleID'] == 34){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $d;?></td>
                                            <td style="width: 55%;"><?php echo $valueD['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueD['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueD['fieldType'], $valueD['companypolicymasterID'],$valueD['companyValue'],$valueD['documentID'],$valueD['isCompanyLevel'],$valueD['code']);  ?></td>
                                        </tr>
                                    <?php  $d++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 35) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading5">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                        Finance
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse5" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading5">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $e=1;
                                   foreach($detail as $valueE){ 
                                   
                                    if($valueE['moduleID'] == 35){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $e;?></td>
                                            <td style="width: 55%;"><?php echo $valueE['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueE['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueE['fieldType'], $valueE['companypolicymasterID'],$valueE['companyValue'],$valueE['documentID'],$valueE['isCompanyLevel'],$valueE['code']);  ?></td>
                                        </tr>
                                    <?php  $e++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 36) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading6">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
                        Asset Management
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse6" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading6">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $f=1;
                                   foreach($detail as $valueF){ 
                                  
                                    if($valueF['moduleID'] == 36){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $f;?></td>
                                            <td style="width: 55%;"><?php echo $valueF['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueF['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueF['fieldType'], $valueF['companypolicymasterID'],$valueF['companyValue'],$valueF['documentID'],$valueF['isCompanyLevel'],$valueF['code']);  ?></td>
                                        </tr>
                                    <?php  $f++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 37) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading7">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse7" aria-expanded="false" aria-controls="collapse7">
                        Treasury
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse7" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading7">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $g=1;
                                   foreach($detail as $valueG){ 
                                  
                                    if($valueG['moduleID'] == 37){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $g;?></td>
                                            <td style="width: 55%;"><?php echo $valueG['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueG['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueG['fieldType'], $valueG['companypolicymasterID'],$valueG['companyValue'],$valueG['documentID'],$valueG['isCompanyLevel'],$valueG['code']);  ?></td>
                                        </tr>
                                    <?php  $g++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 38) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading8">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse8" aria-expanded="false" aria-controls="collapse8">
                        HRMS
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse8" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading8">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $h=1;
                                   foreach($detail as $valueH){ 
                                   
                                    if($valueH['moduleID'] == 38){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $h;?></td>
                                            <td style="width: 55%;"><?php echo $valueH['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueH['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueH['fieldType'], $valueH['companypolicymasterID'],$valueH['companyValue'],$valueH['documentID'],$valueH['isCompanyLevel'],$valueH['code']);  ?></td>
                                        </tr>
                                    <?php  $h++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 39) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading9">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse9" aria-expanded="false" aria-controls="collapse9">
                        Configuration
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse9" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading9">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $i=1;
                                   foreach($detail as $valueI){ 
                                   
                                    if($valueI['moduleID'] == 39){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $i;?></td>
                                            <td style="width: 55%;"><?php echo $valueI['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueI['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueI['fieldType'], $valueI['companypolicymasterID'],$valueI['companyValue'],$valueI['documentID'],$valueI['isCompanyLevel'],$valueI['code']);  ?></td>
                                        </tr>
                                    <?php  $i++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 40) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading10">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse10" aria-expanded="false" aria-controls="collapse10">
                        TAX
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse10" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading10">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $j=1;
                                   foreach($detail as $valueJ){ 
                                  
                                    if($valueJ['moduleID'] == 40){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $j;?></td>
                                            <td style="width: 55%;"><?php echo $valueJ['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueJ['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueJ['fieldType'], $valueJ['companypolicymasterID'],$valueJ['companyValue'],$valueJ['documentID'],$valueJ['isCompanyLevel'],$valueJ['code']);  ?></td>
                                        </tr>
                                    <?php  $j++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 41) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading11">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse11" aria-expanded="false" aria-controls="collapse11">
                        POS Restaurant
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse11" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading11">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $k=1;
                                   foreach($detail as $valueK){ 
                                   
                                    if($valueK['moduleID'] == 41){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $k;?></td>
                                            <td style="width: 55%;"><?php echo $valueK['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueK['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueK['fieldType'], $valueK['companypolicymasterID'],$valueK['companyValue'],$valueK['documentID'],$valueK['isCompanyLevel'],$valueK['code']);  ?></td>
                                        </tr>
                                    <?php  $k++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 42) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading12">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse12" aria-expanded="false" aria-controls="collapse12">
                        Administration
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse12" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading12">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $l=1;
                                   foreach($detail as $valueL){ 
                                    
                                    if($valueL['moduleID'] == 42){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $l;?></td>
                                            <td style="width: 55%;"><?php echo $valueL['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueL['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueL['fieldType'], $valueL['companypolicymasterID'],$valueL['companyValue'],$valueL['documentID'],$valueL['isCompanyLevel'],$valueL['code']);  ?></td>
                                        </tr>
                                    <?php  $l++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 361) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading13">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse13" aria-expanded="false" aria-controls="collapse13">
                        Sales & Marketing
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse13" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading13">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $m=1;
                                   foreach($detail as $valueM){ 
                                  
                                    if($valueM['moduleID'] == 361){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $m;?></td>
                                            <td style="width: 55%;"><?php echo $valueM['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueM['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueM['fieldType'], $valueM['companypolicymasterID'],$valueM['companyValue'],$valueM['documentID'],$valueM['isCompanyLevel'],$valueM['code']);  ?></td>
                                        </tr>
                                    <?php  $m++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif($valueT['moduleID'] == 399) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading14">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse14" aria-expanded="false" aria-controls="collapse14">
                        CRM
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse14" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading14">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $n=1;
                                   foreach($detail as $valueN){ 
                                 
                                    if($valueN['moduleID'] == 399){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $n;?></td>
                                            <td style="width: 55%;"><?php echo $valueN['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueN['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueN['fieldType'], $valueN['companypolicymasterID'],$valueN['companyValue'],$valueN['documentID'],$valueN['isCompanyLevel'],$valueN['code']);  ?></td>
                                        </tr>
                                    <?php  $n++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } elseif(empty($valueP['moduleID'] )) { ?>   
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading15">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse15" aria-expanded="false" aria-controls="collapse15">
                        Other
                        <span> </span>
                        </a>
                    </h4>
                    </div>
                    <div id="collapse15" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading15">
                        <div class="panel-body">
                            <table class="<?php echo table_class(); ?>">
                                <?php 
                                    $z=1;
                                   foreach($detail as $valueP){
                                    if(empty($valueP['moduleID'] )){ ?>
                                        <tr>
                                            <td style="width: 5%;"><?php echo $z;?></td>
                                            <td style="width: 55%;"><?php echo $valueP['companyPolicyDescription']?></td>
                                            <td style="width: 10%;"><?php echo $valueP['documentID']?></td>
                                            <td style="width: 30%;"><?php echo get_policy($valueP['fieldType'], $valueP['companypolicymasterID'],$valueP['companyValue'],$valueP['documentID'],$valueP['isCompanyLevel'],$valueP['code']);  ?></td>
                                        </tr>
                                    <?php  $z++; }                                   
                                    }  ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php }
        } }
        ?>
    </div>

</div>


  