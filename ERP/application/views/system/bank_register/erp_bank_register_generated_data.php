
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$convertFormat = convert_date_format();
?>



    <div class="page-header">
        <h4><?php echo $this->lang->line('treasury_tr_lm_bank_cash_register');?><!--Bank/Cash Register--></h4>

        <span style="font-size: 14px">
        <b><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> : </b><?php echo $GLdetail['GLSecondaryCode']; ?>&nbsp;&nbsp;&nbsp;
         <b><?php echo $this->lang->line('treasury_common_gl_description');?><!--GL Description--> : </b><?php echo $GLdetail['GLDescription']; ?>&nbsp;&nbsp;&nbsp;
          <b><?php echo $this->lang->line('treasury_common_bank_name');?><!--Bank Name--> : </b><?php echo $GLdetail['bankName']; ?>&nbsp;&nbsp;&nbsp;
            <b><?php echo $this->lang->line('treasury_common_facility_bank_account_no');?><!--Bank Account No-->. : </b><?php echo $GLdetail['bankAccountNumber']; ?>&nbsp;&nbsp;&nbsp;
        <b><?php echo $this->lang->line('common_currency');?><!--Currency--> : </b><?php echo $currencycode ?>&nbsp;&nbsp;&nbsp;
    </span>
    </div>

    <form class="form-horizontal">

        <div class="control-group">

            <?php
            $datetodate='';
            $datefromdate='';
            $dateto=$this->input->post('dateto');
            $datefrom=$this->input->post('datefrom');
            if($dateto !=''){
                $datetodate=  format_date($dateto,$convertFormat) ;
            }
            else{
                $datetodate=  format_date(date('Y-m-t'),$convertFormat) ;
            }
            if($datefrom !=''){
                $datefromdate=  format_date($datefrom,$convertFormat) ;
            }
            else{
                $datefromdate=  format_date(date('Y-m-d'),$convertFormat) ;
            }
            ?>
            <fieldset class="scheduler-border">

                <div class="" style="">
                    <div class="input-daterange input-group" id="datepicker">
                        <div class="form-group col-sm-6 col-md-4" style="margin-bottom: 0px">
                            <label class="col-md-4 control-label text-left"
                                   for="employeeID"><?php echo $this->lang->line('common_from');?><!--From-->:</label>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class='input-group date filterDate' id="">
                                        <input type='text' id="datefrom" class="form-control" name="datefrom" value="<?php echo $datefromdate; ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-md-4" style="margin-bottom: 0px">
                            <label class="col-md-4 control-label text-left"
                                   for="employeeID"><?php echo $this->lang->line('common_to');?><!--To-->:</label>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class='input-group date filterDate' id="">
                                        <input type='text' class="form-control" id="dateto"  name="dateto" value="<?php echo $datetodate ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-md-4" style="margin-bottom: 0px">
                            <label class="col-md-4 control-label text-left"
                                   for="employeeID">CLRD</label>
                            <div class="col-md-8">
                                <div class="form-group">
                                <select name="filter_status" class="form-control" id="filter_status">
                                    <option value="-1">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="margin-bottom: 0px;">
                    <label class="col-sm-1 control-label text-left"
                           for="employeeID"></label>
                    <div class="col-md-9">
                    <button type="button" class="btn btn-primary btn-xs" onclick="filtersearch()"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                    <button type="button" class="btn btn-primary btn-xs" onclick="clearsearch()"><?php echo $this->lang->line('common_clear');?><!--Clear--></button>
                    </div>
                </div>
            </fieldset>


        </div>
    </form>

<div id="confrimDiv" style="margin-bottom: 60px">
    <span class="pull-right"><?php echo export_buttons('table1', 'Bank/Cash Register', True, False, 'btn btn-success-new size-sm') ?></span>
    <table id="table1" class="<?php echo table_class() ?>">
        <thead>
        <th><?php echo $this->lang->line('treasury_common_document_code');?><!--Document Code--></th>
        <th><?php echo $this->lang->line('treasury_ap_br_doc_date');?><!--Doc Date--></th>

     <!--   <th>Party Type</th>-->
        <th style="width: 250px"><?php echo $this->lang->line('treasury_common_party');?><!--Party--> </th>
      <!--  <th>Party No</th>-->
        <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
        <th><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
        <th><?php echo $this->lang->line('common_payment');?><!--Payment--></th>        
        <th><?php echo $this->lang->line('treasury_common_receipt');?><!--Receipt--></th>
        <th><?php echo $this->lang->line('treasury_tr_lm_clrd');?><!--CLRD--> </th>
        <th><?php echo "Cleared Date"; ?><!--CLRD--> </th>
        <th><?php echo $this->lang->line('treasury_common_balance');?><!--Balance--></th>


        </thead>
        <tbody>

        <?php
        $payment=0;
        $recieved=0;
        if(!empty($openingbalance)){
            ?>
            <tr>
                <td colspan="9"><strong><?php echo $this->lang->line('treasury_bta_opening_balance');?><!--Opening Balance--></strong></td>
                <td style="text-align: right"><?php echo number_format($openingbalance['total'],2) ?></td>
                </tr>
            <?php

            if($openingbalance['total']< 0){
                $payment=$openingbalance['total'];
            }
            if($openingbalance['total']> 0){
                $recieved=$openingbalance['total'];
            }
        }
        if($details){
            $totalAmount=0;
            foreach($details as $val){
                ?>
        <tr>

            <td><a target="_blank" onclick="documentPageView_modal('<?php echo $val['documentType'] ?>',<?php echo $val['documentMasterAutoID']?>)" > <?php echo $val['documentSystemCode']; ?></a></td>
            <td><?php echo $val['documentDate']; ?></td>

          <!--  <td><?php /*echo $val['partyType']; */?></td>-->
            <td><b><?php echo ($val['partyCode'] !=''?$val['partyCode'].'&nbsp; &mdash;':''); ?></b>  <?php echo $val['partyName']; ?></td>
           <!-- <td><?php /*echo $val['partyName']; */?></td>-->
             <td><?php echo $val['memo']; ?></td>
            <td><?php echo $val['chequeNo']; ?></td>
            <?php if($val['transactionType']==2){
                $payment+=-1 * abs($val['amount']);
                ?>
            <td style="text-align: right"><?php echo $val['bankCurrencyAmount']; ?></td>
                    <?php }else{ ?>
                <td style="text-align: right"></td>
                  <?php  } ?>
            
            <?php if($val['transactionType']==1){
                $recieved+=$val['amount'];
                ?>
                <td style="text-align: right"><?php echo $val['bankCurrencyAmount']; ?></td>
            <?php }else{ ?>
                <td style="text-align: right"></td>
            <?php  } ?>

            <td>
                <?php if($val['clearedYN']==1){ ?>
                    <span>Yes</span>
                <?php } else { ?> <span>No</span> <?php } ?>
            </td>
            <td>
                <?php echo format_date($val['clearedDate'], $convertFormat) ?>
            </td>
            <td  style="text-align: right"><?php
                $totalAmount=$payment+$recieved;
                echo number_format($totalAmount,$val['bankCurrencyDecimalPlaces']); ?></td>


            </tr>
        <?php
            }
        }
?>
        </tbody>

</div>

<script>

    </script>





<script>
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });
    Inputmask().mask(document.querySelectorAll("input"));
    </script>






