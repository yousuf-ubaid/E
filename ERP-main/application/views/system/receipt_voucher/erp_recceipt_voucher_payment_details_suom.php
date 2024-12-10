
<table class="table table-bordered table-striped table-condesed">
    <thead>
    <tr>
        <th style="min-width: 5%">#</th>
        <th style="min-width: 10%">
            <?php echo $this->lang->line('accounts_receivable_common_bank_or_cash');?> <!--Bank or Cash--></th>
        <th class="chequeDet_header">
            <?php echo $this->lang->line('accounts_receivable_common_cheque_number'); ?><!--Cheque Number--> <?php required_mark(); ?></th>
        <th class="chequeDet_header">
            <?php echo $this->lang->line('accounts_receivable_common_cheque_date'); ?><!--Cheque Date--> <?php required_mark(); ?></th>
        <th>
            <?php echo $this->lang->line('accounts_receivable_common_memo'); ?><!--Memo--></th>
        <th style="min-width: 36%" class="text-left">
            <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
        <th style="min-width: 10%">
            <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
    </tr>
    </thead>
    <tbody id="payment_table_body">
    <tr class="danger">
        <td colspan="7" class="text-center"><b>
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
        </td>
    </tr>
    </tbody>
    <tfoot id="payment_table_tfoot">

    </tfoot>
</table>

<?php
