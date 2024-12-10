<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

$checked = '';

?>

<style>
    ul {
        list-style-type: none;
        padding: 0;
        margin-bottom: 4px;
    }

    .header {
        margin: 8px;
        padding-left: 40px;
    }
</style>

<ul>

    <?php
    if (!empty($templates)) {
    ?>
        <?php
        foreach ($templates as $value) {
            // Check if the template ID is in the selectedTemplates array
            $checked = in_array($value['id'], $selectedTemplates) ? 'checked' : '';
        ?>
            <li class="header">
                <input type="checkbox" id="<?php echo $value['id'] ?>" name="template_ids[]" value="<?php echo $value['id'] ?>" <?php echo $checked; ?>>
                <label for="<?php echo $value['id'] ?>"> <?php echo $value['templateName'] ?></label>
            </li>
        <?php
        }
        ?>
    <?php
    }
    ?>

</ul>
<hr>

<button type="submit" onclick="saveAssetTemplates()" class="btn btn-primary-new size-lg pull-right"><?php echo $this->lang->line('common_save_change'); ?>
</button>

<script>
    function saveAssetTemplates() {
        var selectedTemplates = [];
        $("input[name='template_ids[]']:checked").each(function() {
            selectedTemplates.push($(this).val());
        });

        var vehicleMasterID = "<?php echo $vehicleMasterID; ?>";

        if (selectedTemplates.length === 0) {
            alert('Please select at least one template.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Fleet/saveAssetTemplates'); ?>",
            data: {
                vehicleMasterID: vehicleMasterID,
                templates: selectedTemplates
            },
            beforeSend: function() {
                startLoad(); // Show loading indicator if needed
            },
            success: function(response) {
                stopLoad(); // Hide loading indicator
                myAlert('s', response, ''); // Call myAlert with the response text
            },
            error: function() {
                stopLoad(); // Hide loading indicator
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.', ''); // Custom error message
            }
        });
    }
</script>