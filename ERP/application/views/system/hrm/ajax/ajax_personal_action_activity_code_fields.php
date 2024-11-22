<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="pull-right">
            <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                <table class="table table-bordered table-striped table-condensed mx-auto" style="width:800px;">
                    <thead>
                    <tr>
                        <th style="min-width: 10%">FIELD TYPE</th>
                        <th style="min-width: 10%">CURRENT VALUE</th>
                        <th style="min-width: 10%">NEW VALUE</th>
                    </tr>
                    </thead>
                    <tbody id="table_body">
                    <?php if ($details) { ?>
                        <?php foreach ($details as $row) {
                            ?>

                            <tr>
                                <td class="text-left">
                                    <span><?php echo $row['fieldType'] ?? 'N/A'; ?></span></td>
                                <td class="text-left">
                                    <span><?php echo $row['currentText'] ?? 'N/A'; ?></span></td>
                                <td><span><?php echo $row['NewValueText'] ?? ''; ?></span></td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>