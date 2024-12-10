<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$title = $this->lang->line('hrms_final_settlement_title');

$masterID = $this->input->post('page_id');

$fn_data = final_settlement_data($masterID);
$masterData = $fn_data['masterData'];
$isConfirmed = $masterData['confirmedYN'];
$payrollSal = $fn_data['payroll'];
$non_payrollSal = $fn_data['non_payroll'];
$docDate = convert_date_format($masterData['createdDateTime']);
$dateJoin = convert_date_format($masterData['dateOfJoin']);
$lastWorkingDay = convert_date_format($masterData['lastWorkingDay']);
$dPlaces = $masterData['trDPlace'];
$fn_items_drop = fetch_final_settlement_items();
?>
<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .row-centered {
        text-align:center;
    }

    .col-centered {
        display:inline-block;
        float:none;
        /* reset the text-align */
        text-align:left;
        /* inline-block space fix */
        margin-right:-4px;
        text-align: center;
    }

    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    .hide-div{ display: none; }

    .fs-actionBtn{ font-size: 9px !important; }

    .total-sd-single {
        border-top: 1px solid  #151313 !important;
        border-bottom: 1px solid  #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    .head-tot{
        font-weight: bold;
    }
</style>

<div class="row well" style="padding: 10px;">
    <div class="col-md-4">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                <td class="bgWhite details-td" id="documentCode" width="200px"><?php echo $masterData['documentCode']; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_employee');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $masterData['ECode'].' | '.$masterData['Ename2']; ?></td>
            </tr>

        </table>
    </div>

    <div class="col-md-2">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_date');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $docDate; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo get_currency_code($masterData['trCurrencyID']); ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('emp_date_joined');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $dateJoin; ?></td>
            </tr>
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('emp_lastworking_date');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $lastWorkingDay; ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $masterData['narration']; ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="font-weight: bold">
            <tr>
                <td style="padding: 2px 0px;" width="200px">
                    <button class="btn btn-default btn-xs pull-right" style="font-size: 11px; font-weight: bold; margin-right: 10px;"
                            onclick="account_review(<?=$masterID?>, '<?=$masterData['documentCode']?>')">
                        <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp; <?php echo $this->lang->line('common_account_review');?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row" style="margin-top: -20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <div class="box collapsed-box" style="margin-top: 10px; border: 1px solid #ccc;; border-top: 3px solid #ccc;">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title" id="box-header-title"><?php echo $this->lang->line('emp_bank_payroll');?></h3>
                    <span id="totPayroll-span" class="head-tot"></span>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <table class="<?php echo table_class(); ?> add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $totPayroll = 0;
                        if( !empty($payrollSal) ){
                            foreach($payrollSal as $rowAdd){
                                echo '<tr>
                                    <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                    <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                                  </tr>';
                                $totPayroll += round( $rowAdd['amount'], $dPlaces);
                            }
                        }else{
                            echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                        }
                        ?>
                        </tbody>

                        <?php if( !empty($payrollSal) ){ ?>
                            <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                                <td align="right" class="total-sd"><?php echo number_format( $totPayroll, $dPlaces ) ?></td></tr></tfoot>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box collapsed-box" style="margin-top: 10px; border: 1px solid #ccc;; border-top: 3px solid #ccc;">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title" id="box-header-title"><?php echo $this->lang->line('emp_bank_non_payroll');?></h3>
                    <span id="totNonPayroll-span" class="head-tot"></span>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <table class="<?php echo table_class(); ?> add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $totNonPayroll = 0;
                        if( !empty($non_payrollSal) ){
                            foreach($non_payrollSal as $rowAdd){
                                echo '<tr>
                                    <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                    <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                                  </tr>';
                                $totNonPayroll += round( $rowAdd['amount'], $dPlaces);
                            }
                        }else{
                            echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                        }
                        ?>
                        </tbody>

                        <?php if( !empty($non_payrollSal) ){ ?>
                            <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                                <td align="right" class="total-sd"><?php echo number_format( $totNonPayroll, $dPlaces ) ?></td></tr></tfoot>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: -20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <fieldset>
                <legend><?php echo $this->lang->line('common_addition');?></legend>
                <table class="<?php echo table_class(); ?>" id="addition-tb"></table>
            </fieldset>
        </div>

        <div class="col-sm-6">
            <fieldset>
                <legend><?php echo $this->lang->line('common_deduction');?></legend>
                <table class="<?php echo table_class(); ?>" id="deduction-tb"></table>
            </fieldset>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
    <div class="total-sd" style="margin: 10px 30px; font-size: 14px !important;">
        <?php echo $this->lang->line('common_net_amount');?> <span class="pull-right" id="fnNetAmount"></span>
    </div>
</div>


<script>
    var masterID = '<?php echo $masterID; ?>';
    var totPayroll = '<?php echo ' : '. number_format( $totPayroll, $dPlaces ); ?>';
    var totNonPayroll = '<?php echo ' : '. number_format( $totNonPayroll, $dPlaces ); ?>';

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        $('#totPayroll-span').text(totPayroll);
        $('#totNonPayroll-span').text(totNonPayroll);
        load_FS_detail_view();
    });

    function load_FS_detail_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID},
            url: '<?php echo site_url("Employee/FS_detail_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $('#addition-tb').html(data['addView']);
                    $('#deduction-tb').html(data['dedView']);
                    setNetAmount( data['netAmount'] );
                }else{
                    myAlert('e', data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function more_fs_item(typeID, autoID){
        $('.more-det-tb').hide();
        $('#more-det-body').removeClass('modal-lg');

        if(typeID == 1 || typeID == 7 || typeID == 12){
            $('#more-det-body').addClass('modal-lg');
        }

        $('#more-detail-tb-'+typeID).show();
        var respDiv = $('#more-detail-body-'+typeID);
        $('#modal_moreDetailTitle').text('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID, 'autoID': autoID, 'typeID': typeID},
            url: '<?php echo site_url("Employee/FS_more_detail_view"); ?>',
            beforeSend: function () {
                respDiv.html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    respDiv.html(data['view']);
                    $('#modal_moreDetailTitle').html(data['title']);
                    $('#fn_item_moreDetail_modal').modal('show');
                }else{
                    myAlert('e', data[1]);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function setNetAmount(netAmount){
        $('#fnNetAmount').text(netAmount)
    }

    function account_review(docID, docCode){
        window.open("<?php echo site_url('Employee/final_settlement_account_review'); ?>/"+docID+"/"+docCode, "blank");
    }
</script>


<?php
