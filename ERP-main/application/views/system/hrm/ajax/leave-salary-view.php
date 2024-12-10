<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);

$isConfirmed = $master_data['confirmedYN'];
$isConfirmed = ($is_view == 'Y')? 1: $isConfirmed;
$isApproved = $master_data['approvedYN'];
$masterID = $master_data['masterID'];
$paymentVoucherID = $master_data['paymentVoucherID'];

$encash_policy = getPolicyValues('LEB', 'All'); //Leave encashment policy
$salary_days = 22;
$readonly = '';
if($encash_policy == 1){
    $salaryProportionFormulaDays = getPolicyValues('SPF', 'All'); // Salary Proportion Formula
    $salary_days = ($salaryProportionFormulaDays == 365)? 30.42: 30;
    $readonly = 'readonly';
}
?>

<style>
    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }
</style>

<div class="row well" style="padding: 10px;">
    <div class="col-md-3">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                <td class="bgWhite details-td" id="documentCode" width="200px"><?php echo $master_data['documentCode']; ?></td>
            </tr>

        </table>
    </div>

    <div class="col-md-2">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_date');?></td>
                <td class="bgWhite details-td" id="" width="200px">
                    <?=convert_date_format($master_data['encashment_date']);?>
                    <input type="hidden" id="doc_data" value="<?=$master_data['encashment_date'];?>">
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-2">
        <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
            <tr>
                <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                <td class="bgWhite details-td" id="" width="200px">
                    <?php echo get_currency_code($master_data['trCurrencyID']); ?>
                    <input type="hidden" id="docCurrency" value="<?=$master_data['trCurrencyID'];?>">
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-5">
        <table class="table table-bordered table-condensed" style="font-weight: bold">
            <tr style="background-color: #bed4ea; font-weight: bold">
                <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                <td class="bgWhite details-td" id="" width="200px"><?php echo $master_data['narration']; ?></td>
            </tr>
            <tr style="">
                <td style="padding: 2px 0px; width: 100%; vertical-align: middle; height: 40px;" colspan="2">
                    <?php if($isConfirmed != 1){ ?>
                        <button class="btn btn-success btn-xs pull-right" style="font-size: 11px; font-weight: bold;" onclick="doc_confirm()">
                            <?php echo $this->lang->line('common_confirm');?>
                        </button>
                    <?php } ?>

                    <?php if($isApproved == 1 && $paymentVoucherID == 0 && $from_master == 'Y'){
                        ?>
                        <button class="btn btn-primary btn-xs pull-right" style="font-size: 11px; font-weight: bold;" onclick="open_bankTransferModal(<?=$masterID?>)">
                            <?php $isBankTransferProcessed = 1; echo $this->lang->line('common_bank_transfer'); ?>
                        </button>
                    <?php } ?>

                    <button class="btn btn-default btn-xs pull-right" style="font-size: 11px; font-weight: bold; margin-right: 10px;"
                            onclick="account_review(<?=$masterID?>, '<?=$master_data['documentCode']?>')">
                        <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp; <?php echo $this->lang->line('common_account_review');?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?php
        if($isConfirmed != 1){
            echo '<button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_details_modal()" style="margin-right: 10px; margin-bottom: 10px;">
                  <i class="fa fa-plus"></i>'.$this->lang->line('hrms_payroll_add_employee').'
              </button>';
        }
        ?>
    </div>

    <div class="col-sm-12">
        <div class="table-responsive">
            <table class="<?php echo table_class() ?> drill-table" >
                <thead>
                <tr>
                    <th style="width: 30px"> # </th>
                    <th style=""> <?php echo $this->lang->line('common_employee_name');?> </th>
                    <th style=""> <?php echo $this->lang->line('common_month');?> </th>
                    <th style="width: 105px"> <?php echo $this->lang->line('common_basic_gross');?> </th>
                    <th style="width: 100px"> <abbr title="<?php echo $this->lang->line('common_leave_days');?>"> <?php echo $this->lang->line('hrms_leave_management_leave_days');?> </abbr> </th>
                    <th style="width: 100px"> <abbr title="<?php echo $this->lang->line('common_no_of_working_days');?>"> <?php echo $this->lang->line('hrms_leave_management_no_of_day');?> </abbr> </th>
                    <th style="width: 105px;text-align: right">  <?php echo $this->lang->line('common_amount');?> </th>
                    <th style="width: 250px">  <?php echo $this->lang->line('common_narration');?> </th>
                    <?php
                    if($isConfirmed != 1){
                        if(!empty($details)){
                            echo '<th><span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_all_item()"></span></th>';
                        }
                    }
                    ?>
                </tr>
                </thead>

                <tbody>
                <?php
                $i = 1; $dPlace = $master_data['trDPlace']; $total = 0;
                if(!empty($details)){
                    foreach ($details as $row){
                        $total += $row['amount'];

                        echo '<tr>
                            <td style="text-align: right">'.$i.'</td>                                                                                                   
                            <td style="text-align: center">'.$row['empName'].'</td>
                            <td style="text-align: center">'.$row['salary_pay_date'].'</td>
                            <td style="text-align: center">'.number_format($row['gross_amount'], $dPlace).'</td>
                            <td style="text-align: center">'.$row['salary_days'].'</td>
                            <td style="text-align: center">'.$row['noOfWorkingDaysInMonth'].'</td>
                            <td style="text-align: right">'.number_format($row['amount'], $dPlace).'</td>';

                        if($isConfirmed != 1) {
                            echo '<td style="text-align: center">
                                  <input type="text" style="width: 99%;" onchange="update_narration('.$row['id'].', this)" value="'.$row['narration'].'" />
                              </td>';
                        }
                        else{
                            echo '<td style="text-align: center">'.$row['narration'].'</td>';
                        }

                        if($isConfirmed != 1) {
                            echo '<td style="text-align: center">
                                  <span rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" title="Delete" onclick="delete_item('.$row['id'].')"></span>
                              </td>';
                        }
                        echo  '</tr>';
                        $i++;
                    }

                    $cols_pan = ($isConfirmed != 1)? 'colspan="2"': '';
                    echo '<tr>
                        <td colspan="5" class="total-sd">&nbsp;&nbsp;</td>                                       
                        <td class="total-sd"><b>Total</b></td>                                                    
                        <td class="total-sd" style="text-align: right">'.number_format($total, $dPlace).'</td>                                                    
                        <td class="total-sd" '.$cols_pan.'> </td>                                              
                      </tr>';

                    $i++;

                }
                else{
                    $no_record_found = $this->lang->line('common_no_records_found');
                    echo '<tr><td colspan="10" align="center">'.$no_record_found.'</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" id="leave_en_tot" value="<?=number_format($total, $dPlace)?>">


<script>
    function doc_confirm() {
        bootbox.confirm("Are you sure want to confirm this document?", function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Employee/leave_encashment_document_confirm'); ?>",
                    data: {'masterID': en_masterID},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/hrm/leave-encashment', '', 'HRMS');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });
            }
        });
    }

    function update_narration(id, obj){
        var nar = $(obj).val();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/leave_encashment_update_narration'); ?>",
            data: {'masterID': en_masterID, 'id': id, 'narration': nar},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function account_review(docID, docCode){
        window.open("<?php echo site_url('Employee/leave_salary_account_review'); ?>/"+docID+"/"+docCode, "blank");
    }

    function max_month_days(){
        var obj = $('#no_of_working_days');

        if( parseInt(obj.val()) > 31 ){
            obj.val('');
            myAlert('w', 'Maximum days can not be greater than 31')
        }
    }
</script>

<?php
