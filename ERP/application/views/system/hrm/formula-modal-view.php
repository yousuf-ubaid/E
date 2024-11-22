<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);

$salary_categories = salary_categories(array('A', 'D'));

$pay_groups = [];
$withGroup = null;

$MA_MD = $items['MA_MD'];
$balancePay = $items['balancePay'];
$SSO = $items['SSO'];
$payGroup = $items['payGroup'];
$only_salCat_payGroup = $items['only_salCat_payGroup'];
$isForGratuity = 0;
$NoPay = isset($items['noPay']) ? $items['noPay'] : '';

if(array_key_exists('isForGratuity', $items)){
    $isForGratuity = 1;
}

if($SSO == true){ $withGroup = 0; }
if($payGroup == true){ $withGroup = 1; }

if($withGroup !== null){
    $pay_groups = get_payGroup($withGroup);
}

if($withGroup === null && $only_salCat_payGroup == true){
    $pay_groups = get_payGroup(2);
}


?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/formula-builder/formula-builder.css'); ?>">

<div class="modal fade" id="formula_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width:95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('common_formula_builder');?><!--Formula Builder-->
                    &nbsp;&nbsp;&nbsp;&nbsp;<span id="formula-description"></span>
                </h4>
            </div>

            <form method="post" name="frm_formulaBuilderMaster" id="frm_formulaBuilderMaster">
                <div class="modal-body" id="" style="min-height: 100px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="hidden" name="salaryCategoryContainer" id="salaryCategoryContainer" class="formula-item-container" value="">
                            <input type="hidden" name="SSOContainer" id="SSOContainer" class="formula-item-container" value="">
                            <input type="hidden" name="payGroupContainer" id="payGroupContainer" class="formula-item-container" value="">
                            <div class="formula-container">
                                <ul id="formula-ul"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 15px">
                        <div class="col-xs-10">
                            <div class="well well-sm" style="">
                                <div class="salary-items-title">
                                    <div class="col-sm-12" style="background: inherit; padding: 0px !important;">
                                        <div class="col-sm-4">&nbsp; <?php echo $this->lang->line('hrms_payroll_elements');?><!--Elements--></div>
                                        <div class="col-sm-6">&nbsp;</div>
                                        <div class="col-sm-2" style="padding: 0px !important;">
                                            <div class="input-group pull-right" style="    margin-top: 1px;">
                                                <input type="text" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>" id="item-search-box" style="height:24px" onkeyup="search_items(this)"
                                                       autocomplete="off"/>
                                                <span class="input-group-addon" id="basic-addon2">
                                                    <span class="pull-right close-item-search" onclick="search_items_clear()">
                                                        <i class="fa fa-times" style="font-size: font-size: 12px;"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 7px">&nbsp;</div>
                                <div class="tab-pane" id="field" style="min-height: 120px;">
                                    <?php
                                    if($isForGratuity == 0){
                                        /*Salary Categories*/
                                        foreach ($salary_categories as $salary_category) {
                                            echo '<a class="btn btn-sm salary-items salary-items-cat" href="#"
                                                onclick="appendFormula(\'' . $salary_category['salaryDescription'] . '\',\'' . $salary_category['salaryCategoryID'] . '\',
                                                \'' . 1 . '\',\'' . 0 . '\')">' . $salary_category['salaryDescription'] . '</a>';
                                        }
                                    }
                                    else{
                                        echo '<a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula(\'Basic Pay\',\'FG\',3 )" href="#">
                                                 Basic Pay
                                              </a>
                                              <a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula(\'Total working days\',\'TW\',3 )" href="#">
                                                  Total working days
                                              </a>';
                                    }

                                    /*Social Insurance & pay groups*/
                                    if(!empty($pay_groups)){

                                        foreach ($pay_groups as $pay_group) {
                                            $type = ($pay_group['isGroupTotal'] == 0)? 1 : 2;
                                            $pay_groupID = $pay_group['payGroupID'];
                                            echo '<a class="btn btn-sm salary-items salary-items-cat pay-group" data-id="' . $pay_groupID . '" href="#"
                                                     onclick="appendFormula(\'' . $pay_group['description'] . '\',\'' . $pay_groupID . '\',\'' . 1 . '\',
                                                     \'' . $type . '\')" >' . $pay_group['description'] . '</a>';
                                        }

                                    }

                                    if($MA_MD == true){
                                        $mAddition_lang = $this->lang->line('common__monthly_addition');
                                        $mDeduction_lang = $this->lang->line('common__monthly_deduction');

                                        ?>
                                        <a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('<?php echo $mAddition_lang;?>','MA',3 )" href="#">
                                            <?php echo $mAddition_lang;?><!--Monthly Addition-->
                                        </a>
                                        <a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('<?php echo $mDeduction_lang;?>','MD',3 )" href="#">
                                            <?php echo $mDeduction_lang;?><!--Monthly Deduction-->
                                        </a>
                                    <?php
                                    }
                                    if($balancePay == true){
                                        $balancePayment_lang = $this->lang->line('common_balance_payment');
                                        ?>
                                        <a class="btn btn-sm salary-items salary-items-cat" onclick="appendFormula('<?php echo $balancePayment_lang;?>','0','3',3)" href="#">
                                            <?php echo $balancePayment_lang;?><!--Balance Payment-->
                                        </a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="well well-sm" style="border-radius: 0;margin: 0">
                                <div class="salary-items-title"> &nbsp; <?php echo $this->lang->line('hrms_payroll_operation_and_others');?><!--Operation and Others--></div>
                                <div style="height: 7px">&nbsp;</div>
                                <div class="tab-pane" id="math">
                                    <div style="padding-bottom: 5px">
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('+','+',2)" href="#">+</a>
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('*','*',2)" href="#">*</a>
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('/','/',2)" href="#">/</a>
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('-','-',2)" href="#">-</a>
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('(','(',2)" href="#">(</a>
                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula(')',')',2)" href="#">)</a>
                                        <hr style="margin: 5px 0px; border-top: 1px solid #ccc;">

                                        <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('', '', 'no', '')" href="#"><?php echo $this->lang->line('common_number');?><!--Number--></a>
                                        <?php if($NoPay) { ?>
                                            <a class="btn btn-sm salary-items salary-items-margin" onclick="appendFormula('Month Days','NMD', '3')" href="#"><?php echo $this->lang->line('common_day_month');?><!--Number--></a>
                                        <?php } ?>

                                        <hr style="margin: 5px 0px; border-top: 1px solid #ccc;">
                                        <button type="button" class="btn btn-sm salary-items salary-items-margin locationBtn"
                                                onclick="appendLocation(this)" data-value="after">
                                            <?php echo $this->lang->line('common_after');?><!--After-->
                                        </button>
                                        <button type="button" class="btn btn-sm salary-items salary-items-margin locationBtn"
                                                onclick="appendLocation(this)" data-value="before">
                                            <?php echo $this->lang->line('common_before');?><!--Before-->
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">

                <button class="btn btn-primary btn-sm"  id="btn_add_formulaDetail" onclick="saveFormula()">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_formula');?><!--Add Formula-->
                </button>
                <button class="btn btn-primary btn-sm" type="button"  onclick="clear_formula()" id="clear">
                    <?php echo $this->lang->line('common_clear');?><!--Clear-->
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"> <?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url('plugins/formula-builder/formula-builder.js'); ?>"></script>

<script>
    var common_are_you_sure = "<?php echo $this->lang->line('common_are_you_sure');?>";
    var common_you_want_to_delete = "<?php echo $this->lang->line('common_you_want_to_delete');?>";
    var common_delete = "<?php echo $this->lang->line('common_delete');?>";
    var common_cancel = "<?php echo $this->lang->line('common_cancel');?>";
</script>

<?php
