<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

?>
<table class="table table-bordered table-condensed" style="width: 100%;">
    <thead>
    <tr>
        <th>Navigation Menu</th>
        <th>Secondary Description</th>
    </tr>
    </thead>
    <tbody>
    <?php
    /**
     * Render the menu tree in a single column with indentation based on hierarchy levels.
     *
     * @param array $data The array of menu items.
     * @param int|null $parentID The parent ID to filter the children items.
     * @param int $level The current level in the hierarchy.
     */
    function renderMenuTree($data, $parentID = null, $level = 1) {
        foreach ($data as $item) {
            if ($item['masterID'] === $parentID) {
                echo '<tr>';
                echo '<td style="padding-left: ' . ($level * 20) . 'px;">' . htmlspecialchars($item['description']) . '</td>';
                echo '<td>';
                echo '<input type="text" class="form-control" name="secondaryDescription[]" value="'.htmlspecialchars($item['secondaryDescription']).'">';
                echo '<input type="hidden" name="navigationMenuID[]" value="'.$item['navigationMenuID'].'">';
                echo '</td>';
                echo '</tr>';
                renderMenuTree($data, $item['navigationMenuID'], $level + 1);
            }
        }
    }


    renderMenuTree($data);
    ?>
    </tbody>
</table>
