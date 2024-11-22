<?php echo head_page('Journal Entry Report',false); ?>
<div class="table-responsive">
    <table id="journal_entry_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">Code</th>
                <th style="min-width: 10%">Document</th>
                <th style="min-width: 10%">Date</th>
                <th style="min-width: 10%">GL Code</th>
                <th style="min-width: 10%">Secondary Code</th>
                <th style="min-width: 20%">GL Code Description</th>
                <th style="min-width: 5%">Type</th>
                <th style="min-width: 10%">Segment</th>
                <th style="min-width: 15%">Debit </th>
                <th style="min-width: 15%">Credit </th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script>
    $('.headerclose').click(function(){
        fetchPage('system/finance/journal_entry_rpt','','Journal Entry Report');
    });
    </script>
