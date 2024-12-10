


<table id="mfq_outputitems" class="table table-condensed">
    <thead>
    <tr>
        <th style="min-width: 15%">Item Name<!--Stage Progress--></th>
        <th style="min-width: 10%">ItemCode<!--Stage--></th>
        <th style="min-width: 20%">Quantity<!--Stage Progress--></th>
        <th style="min-width: 15%">Total Cost (%)<!--Stage Progress--></th>
        <th style="min-width: 15%"><button class="btn btn-priamry" onclick="add_outputitems()"><i class="fa fa-plus"></i></button></th>
     
    </tr>
    </thead>

    <tbody id="output_items_body">
        <?php foreach($details as $detail){ ?>
            <tr>
                <td><?php echo $detail['itemName'] ?></td>
                <td><?php echo $detail['itemSystemCode'] ?></td>
                <td><?php echo $detail['qty'] ?></td>
              
                <td><input type="number" name="percentage" class="form-control" value="<?php echo $detail['totalPercentage'] ?>" placeholder="%" onchange="update_total_percentage(this,<?php echo $detail['id'] ?>)"></td>
            </tr>

        <?php } ?>


    </tbody>
</table>


<script>


    function add_outputitems() {
        // Define the HTML content for the new row (5 columns)
        var newRow = `
            <tr>
                <td>
                <input type="hidden" id="itemAutoID" class="itemAutoID" /> 
                <input type="hidden" id="mfqItemID" class="mfqItemID" />
                <input type="text" name="ItemName" id="outputitemsSearch" class="form-control" placeholder=" Name"></td>
                <td><input type="text" name="ItemCode" id="ItemCode" class="form-control" placeholder=" Code" readonly></td>
                <td><input type="number" name="Quantity" class="form-control" placeholder=" Quantity"></td>
              
                <td><input type="number" name="percentage" class="form-control percentage" placeholder="Percetage"></td>
                <td>
                    <button class="btn save-row" onclick="add_row_save(this)"><i class="fa fa-plus"></i></button>
                    <button class="btn " onclick="delete_row(this)"><i class="fa fa-trash"></i></button></td>
            </tr>
        `;

        // Append the new row to the table body using jQuery
        $('#output_items_body').append(newRow);
        initializeitemTypeaheadOutputItems();
    }

    // Function to delete a row
    function delete_row(button) {
        // Remove the row using jQuery
        $(button).closest('tr').remove();
    }

    function initializeitemTypeaheadOutputItems() {
        $('#outputitemsSearch').autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_goods/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#itemSearchBox').find('.mfqItemID').val(suggestion.mfqItemID);
                    $('#ItemCode').val(suggestion.data);
                    $('#ItemCode').val(suggestion.data);
                    $('#mfqItemID').val(suggestion.mfqItemID);
                    $('#itemAutoID').val(suggestion.itemAutoID);
                }, 200);
            },
          
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function add_row_save(ev){

        var row = $(ev).closest('tr'); // Get the row containing the button
        var itemCode = row.find('input[name="ItemCode"]').val();
        var itemName = row.find('input[name="ItemName"]').val();
        var itemDescription = row.find('input[name="ItemDescription"]').val();
        var quantity = row.find('input[name="Quantity"]').val();
        var itemStatus = row.find('select[name="ItemStatus"]').val();
        var mfqItemID = row.find('.mfqItemID').val();
        var itemAutoID = row.find('.itemAutoID').val();
        var percentage = row.find('.percentage').val();
        

        // Prepare data to send via AJAX
        var rowData = {
            itemCode: itemCode,
            itemName: itemName,
            itemDescription: itemDescription,
            quantity: quantity,
            itemStatus: itemStatus,
            workProcessID: workProcessID,
            itemAutoID: itemAutoID,
            mfqItemID: mfqItemID
        };

        // Perform AJAX request to save the data
        $.ajax({
            url: "<?php echo site_url('MFQ_Job/save_outputitems'); ?>",  // Replace with your actual server endpoint
            method: 'POST',
            data: rowData,
            success: function(response) {
                refreshNotifications(true);
                loadItemList();
            },
            error: function(xhr, status, error) {
                alert('Error saving row: ' + error);
            }
        });
    };

    function update_total_percentage(ev,id){
        $.ajax({
            url: "<?php echo site_url('MFQ_Job/update_total_item_estimate'); ?>",  // Replace with your actual server endpoint
            method: 'POST',
            data: {'workProcessID':workProcessID,'value':$(ev).val(),'id':id},
            success: function(response) {
                refreshNotifications(true);
                loadItemList();
            },
            error: function(xhr, status, error) {
                alert('Error saving row: ' + error);
            }
        });
    }

</script>