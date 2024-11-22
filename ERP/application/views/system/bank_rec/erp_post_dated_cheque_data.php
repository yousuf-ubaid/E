<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


echo export_buttons('table1', 'Received Post Dated Cheques', True, False);
?>

<div class="page-header"><h4><?php echo $this->lang->line('treasury_tr_lm_received_post_dated_cheques');?><!--Received Post Dated Cheques--></h4></div>
<table id="table1" class="<?php echo table_class() ?>">
    <thead>
    <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
    <th><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>

    <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
    <th><?php echo $this->lang->line('treasury_ap_br_un_party_id');?><!--Party ID--></th>
    <th><?php echo $this->lang->line('treasury_tr_lm_party_name');?><!--Party Name--></th>
    <th><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
    <th><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></th>
    <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
    <th><?php echo $this->lang->line('treasury_tr_lm_remind_in');?><!--Remind In--></th>
    </thead>
    <tbody> <?php $recieptTotal = 0;
    $numformat = 2;
    $recieptcheckedTotal = 0;
    if ($recieptAmount) {
        foreach ($recieptAmount as $reciptHeader) {
            $x = 0;
            if ($details) {
                foreach ($details as $value) {
                    if ($x == 0) { ?>
                        <tr>
                            <td colspan="9" style="font-weight: bolder;color: #000080 "><?php echo $reciptHeader['bankName'] ?></td>
                        </tr> <?php }
                    if ($reciptHeader['bankGLAutoID'] == $value['bankGLAutoID']) {
                        $recieptTotal += $value['bankCurrencyAmount'];
                        $numformat = $value['bankCurrencyDecimalPlaces'];
                        $tr = '<tr>';
                        if ($value['isThirdPartyCheque'] == 1) {
                            $tr = '<tr style="background-color: rgba(243, 156, 18, 0.17)">';
                        } else {
                            $tr = '<tr>';
                        } ?><?php echo $tr; ?>
                        <td><?php echo $value['documentDate'] ?></td>
                        <td><?php echo $value['documentSystemCode'] ?></td>

                        <td><?php echo $value['bankCurrency'] ?></td>
                        <td><?php echo $value['partyCode'] ?></td>
                        <td><?php echo $value['partyName'] ?></td>
                        <td><?php echo $value['chequeNo'] ?></td>
                        <td><?php echo $value['chequeDate'] ?></td>
                        <td style="text-align: right"><?php echo number_format($value['bankCurrencyAmount'], $value['bankCurrencyDecimalPlaces']); ?></td>
                        <td style=" width:30px;text-align: center;font-weight: bold">
                            <a href="#" data-type="number"
                               data-url="<?php echo site_url('Bank_rec/ajax_update_postdated_cheque_remainIn') ?>"
                               data-pk="<?php echo $value['bankLedgerAutoID'] ?>"
                               data-name="remainIn"
                               data-title="Remain In" class="xeditable "
                               data-value="<?php echo $value['remainIn'] ?>">
                                <?php echo $value['remainIn'] ?>
                            </a>

                            <?php
                            $date=date('Y-m-d');
                            if($date<=$value['chequeDate'] && $value['chequeDate']<=$value['remainingDays']){
                                ?>
                                <div style="width:10px; float: right" class="pull-right blink"><i style="color: #b62d34" class="fa fa-exclamation-triangle"
                                                                                                  aria-hidden="true"></i>
                                </div>
                            <?php } ?>
                        </td> </tr> <?php
                    }

                    $x++;
                }

            }
            ?>
            <tr>
                <td colspan="7" style="text-align: right;font-weight: bolder"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <td style="text-align: right;font-weight: bolder"><?php echo number_format($reciptHeader['totalbankCurrencyAmount'], $numformat) ?></td>
                <td></td>
            </tr>
            <?php

        }

    } ?> </tbody>


</table>
<div class="hide" style="font-weight: bolder"><?php echo $this->lang->line('treasury_tr_lm_as_of_date_book_balance');?><!--As of Date Book Balance --><?php echo number_format($bookbalance,$numformat)?></div>

<script>
    $('#tab2').addClass('btn-default');
    $('#tab2').removeClass('btn-primary');
    $('#tab1').removeClass('btn-default');
    $('#tab1').addClass('btn-primary');
    $('.xeditable').editable({placement: 'left',  mode: 'inline'});
    function blink(selector) {
        $(selector).fadeOut('slow', function () {
            $(this).fadeIn('slow', function () {
                blink(this);
            });
        });
    }

    blink('.blink');
</script>