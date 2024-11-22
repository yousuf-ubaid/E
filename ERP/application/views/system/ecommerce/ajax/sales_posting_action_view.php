<style>
   
</style>

<div class="form-group">

<div class="table-responsive">
    <table id="clent_double_entry" class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <!-- <th style="min-width: 10%">Clent Column</th>Code -->
                <th style="min-width: 10%">Date</th><!--Code-->
                <th style="min-width: 10%">Message</th><!--Code-->
            </tr>
        </thead>
        <tbody>
            <?php foreach($processed_log as $value){ ?>
            <tr>
                <td><?php echo $value['date'] ?></td>
                <td style="color:<?php echo $value['alert_color'] ?>"><?php echo $value['message'] ?>
                    <?php if($value['status'] == 2) { ?>
                        <button type="button" class="btn btn-success pull-right" onClick="reGenerateInvoice(<?php echo $value['invoice_type'] ?>)"><i class="fa fa-check"> </i> Re-run</button>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>