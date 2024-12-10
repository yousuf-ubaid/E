<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>

<div class="table-responsive">
    <table id="segment_table" class="table">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('common_description'); ?></th>
                <th class="text-left"><?php echo $this->lang->line('common_master_segment'); ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_is_default'); ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th>
            </tr>
        </thead>
        <tbody id="segmentBody">
            <?php renderSegments($segments); ?>
        </tbody>
    </table>
</div>

<?php
function renderSegments($segments, $level = 0)
{
    foreach ($segments as $segment)
    {
        $indent = str_repeat('&nbsp;', $level * 4); // Indentation for child rows
        $hasChildren = isset($segment['children']) && !empty($segment['children']);
        $icon = $level == 0 && $hasChildren ? '<i class="fa fa-minus-square toggle-icon" aria-hidden="true" data-id="' . $segment['segmentID'] . '"></i>' : '<i class="fa fa-minus-square" aria-hidden="true" data-id="' . $segment['segmentID'] . '"></i>'; // Icon for parent rows

        echo '<tr class="parent-row" data-id="' . $segment['segmentID'] . '" data-level="' . $level . '">';
        echo '<td>' . $indent . $icon . ' ' . $segment['segmentCode'] . ' | ' . $segment['description'] . '</td>';
        echo '<td>' . $segment['masterSegmentInfo'] . '</td>';
        echo '<td>' . loadDefaultWarehousechkbx($segment['segmentID'], $segment['isDefault']) . '</td>';
        echo '<td>' . load_segment_action($segment['segmentID']) . '</td>';
        echo '<td>' . load_segment_status($segment['segmentID'], $segment['status']) . '</td>';
        echo '</tr>';

        // Recursively render child segments
        if ($hasChildren)
        {
            foreach ($segment['children'] as $childSegment)
            {
                echo '<tr class="child-row" data-parent-id="' . $segment['segmentID'] . '">'; // Initially visible
                echo '<td>' . str_repeat('&nbsp;', ($level + 1) * 4) . $childSegment['segmentCode'] . ' | ' . $childSegment['description'] . '</td>';
                echo '<td>' . $childSegment['masterSegmentInfo'] . '</td>';
                echo '<td>' . loadDefaultWarehousechkbx($childSegment['segmentID'], $childSegment['isDefault']) . '</td>';
                echo '<td>' . load_segment_action($childSegment['segmentID']) . '</td>';
                echo '<td>' . load_segment_status($childSegment['segmentID'], $childSegment['status']) . '</td>';
                echo '</tr>';
            }
        }
    }
}
?>

<script>
    $(document).ready(function() {
        // Handle toggle functionality
        $(document).on('click', '.toggle-icon', function() {
            var icon = $(this);
            var segmentId = icon.data('id'); // Get the parent segment ID

            // Find child rows associated with this parent ID
            var childRows = $('tr.child-row[data-parent-id="' + segmentId + '"]');

            // Toggle visibility of child rows
            childRows.each(function() {
                $(this).toggle(); // Toggle child rows
            });

            // Toggle icon between minus and plus
            if (icon.hasClass('fa fa-minus-square')) {
                icon.removeClass('fa fa-minus-square').addClass('fa fa-plus-square');
            } else {
                icon.removeClass('fa fa-plus-square').addClass('fa fa-minus-square');
            }
        });
    });
</script>