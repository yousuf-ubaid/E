<style>
    .hideTr {
        display: none
    }

    .oddTR td {
        background: #f9f9f9 !important;
    }

    .evenTR td {
        background: #ffffff !important;
    }
</style>
<?php
$documentID = $this->input->post('documentID');

switch ($documentID) {
    case "CINV":
        $qty = $detail['requestedQty'];
        break;

    case "RV":
        $qty = $detail['requestedQty'];
        break;

    case "SR":
        $qty = $detail['return_Qty'];
        break;

    case "MI":
        $qty = $detail['qtyIssued'];
        break;

    case "ST":
        if($subItemapplicableon == 2){
            $qty = $detail['SUOMQty'];
        }else{
            $qty = $detail['transfer_QTY'];
        }
        break;

    case "SA":
        $qty = abs($detail['adjustmentStock']);
        break;

    case "DO":
        $qty = abs($detail['requestedQty']);
        break;
        
    case "JOB":
        if($subItemapplicableon == 2){
            $qty = abs($detail['secondaryQty']);
        }else{
            $qty = abs($detail['usageQty']);
        }
        
        break;

    default:
        echo $documentID . ' Error: Code not configured!<br/>';
        echo 'File: ' . __FILE__ . '<br/>';
        echo 'Line No: ' . __LINE__ . '<br><br>';
}

?>
<div class="row">
    <div class="col-sm-12">
        <h4>
            <?php echo $detail['itemDescription'] ?> 
        </h4>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <h4>
            Quantity: <strong><span id="exact_quantity"><?php echo $qty ?></span></strong> item/s
        </h4>
    </div>
        <?php if(($status != 1) && ($subItemapplicableon == 2) && ($documentID == 'ST' || $documentID == 'JOB')){
             $secondaryUOMID = $detail['secondaryUOMID'];
        ?>
        <div class="col-sm-4 secondaryDiv text-center" id="secondaryDiv" >
            <label for="">Change Secondary UOM Qty <br>(<?php echo $detail['secUOMDes']; ?>) &nbsp;&nbsp;</label>
            <br>

            <input type="text" id="sec_qty" name="sec_qty" onkeyup="update_quantity_sec_uom();">
            <input type="hidden" id="secondaryUOMID" name="secondaryUOMID" value="<?php echo $secondaryUOMID ?>">
            <input type="hidden" id="detail_autoID" name="detail_autoID" value="">
        </div>
    <?php }?>
    
</div>

<div>
    <div class="btn-group">
        <button onclick="selectNItems(<?php echo $qty ?>)" type="button" class="btn btn-default">
            Select First <?php echo $qty ?> item/s.
        </button>
        <button onclick="unSelectAll()" type="button" class="btn btn-default">un-select all</button>
    </div>
</div>
<h4>
    <input type="text" id="searchItem" placeholder="Search">
    <span class="pull-right">
    <strong> <span id="subItemCount">0</span> </strong> item/s selected <!--out of --> <?php //echo $qty ?> </span>
</h4>


<input type="hidden" value="<?php echo $this->input->post('documentID') ?>" id="soldDocumentID" name="soldDocumentID"/>  <!--CINV / RV-->
<input type="hidden" value="0" id="soldDocumentAutoID" name="soldDocumentAutoID"/>
<input type="hidden" value="0" id="soldDocumentDetailID" name="soldDocumentDetailID"/>
<input type="hidden" value="<?php echo $qty ?>" id="qty" name="qty" class="qty"/>

<?php // print_r($detail) ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div style="max-height: 300px; overflow: auto">
            <table class="table table-bordered table-condensed table-hover " id="subItemListTbl"
                   style="margin-top:-1px;">
                <thead>
                <tr>
                    <th>#</th>
                    <th style="width:13%">SubItem Code</th>
                    <?php
                    $i=1;
                    foreach($attributes as $valu){
                        ?>
                        <th><?php echo $valu['attributeDescription'] ?></th>
                    <?php
                        $i++;
                    }
                    ?>
                    <th>Select</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($subItems) {
                    $nthItem = 0;
                    $selected = 0;
                    $i = 1;
                    foreach ($subItems as $item) {
                        ?>
                        <tr style="cursor: hand !important;"
                            id="rowId_subItem_<?php echo $item['subItemAutoID'] ?>"
                            data-value="<?php echo $item['subItemCode'] ?> <?php echo $item['description'] ?> <?php echo $item['productReferenceNo'] ?>">
                            <td><?php echo $i; $i++; ?></td>
                            <td>
                                <label style="cursor: pointer !important;"
                                       for="checkBox<?php echo $item['subItemAutoID'] ?>">
                                    <?php echo $item['subItemCode'] ?>
                                </label>

                            </td>
                            <?php
                            foreach($attributes as $valu){
                                ?>
                                <td><?php echo $item[$valu['columnName']] ?></td>
                                <?php
                            }
                            ?>
                            <td class="text-center">
                                <input class="subItem <?php if ($nthItem < $qty) {
                                    echo ' nthItem ';
                                } ?>" type="checkbox"
                                    <?php if (!empty(trim($item['soldDocumentDetailID'] ?? '')) && $item['soldDocumentDetailID'] > 0) {
                                        echo ' checked ';
                                        $selected++;
                                    } ?>

                                       id="checkBox<?php echo $item['subItemAutoID'] ?>"
                                       name="subItemCode[]" value="<?php echo $item['subItemAutoID'] ?>">
                            </td>
                            <td>
                                <!--<span id="selectedContainer"><i class="fa fa-check text-green"></i></span>--></td>
                        </tr>
                        <?php
                        $nthItem++;

                    }
                }
                ?>
                </tbody>
            </table>

        </div>

    </div>
</div>

<script>
    function unSelectAll() {
        $(".subItem").prop('checked', false);
        $("#subItemCount").html(0);
    }

    function selectNItems(n) {
        unSelectAll();

        var k = 0;
        $(".nthItem").each(function(){
            if( k < n ){
                $(this).prop('checked', true);
                k++;
            }
            else{ return false; }
        });

        $("#subItemCount").text(k);
    }

    function updateCount() {
        $("#subItemCount").html('<?php echo $selected ?>');
    }


    function setupValues() {

        <?php
        $documentID = $this->input->post('documentID');
        switch ($documentID) {
            case "CINV":
                $documentAutoID = $detail['invoiceAutoID'];
                $documentDetailsAutoID = $detail['invoiceDetailsAutoID'];
                break;

            case "RV":
                $documentAutoID = $detail['receiptVoucherAutoId'];
                $documentDetailsAutoID = $detail['receiptVoucherDetailAutoID'];
                break;

            case "SR":
                $documentAutoID = $detail['stockReturnAutoID'];
                $documentDetailsAutoID = $detail['stockReturnDetailsID'];
                break;

            case "MI":
                $documentAutoID = $detail['itemIssueAutoID'];
                $documentDetailsAutoID = $detail['itemIssueDetailID'];
                break;
            case "ST":
                $documentAutoID = $detail['stockTransferAutoID'];
                $documentDetailsAutoID = $detail['stockTransferDetailsID'];
                break;

            case "SA":
                $documentAutoID = $detail['stockAdjustmentAutoID'];
                $documentDetailsAutoID = $detail['stockAdjustmentDetailsAutoID'];
                break;

            case "DO":
                $documentAutoID = $detail['DOAutoID'];
                $documentDetailsAutoID = $detail['DODetailsAutoID'];
                break;
            case "JOB":
                $documentAutoID = $detail['workProcessID'];
                $documentDetailsAutoID = $detail['jcMaterialConsumptionID'];
                break;

            default:
                echo 'alert("' . $documentID . ' Line No: ' . __LINE__ . ' in File: ' . __FILE__ . ' ")';
        }
        ?>
        $('#detail_autoID').val('<?php echo $documentDetailsAutoID ?>');
        $('#soldDocumentAutoID').val('<?php echo $documentAutoID ?>');
        $('#soldDocumentDetailID').val('<?php echo $documentDetailsAutoID ?>');
    }

    $(document).ready(function (e) {
        setupValues();

        $('#searchItem').keyup(function () {

            var searchKey = $.trim($(this).val()).toLowerCase();
            var tableTR = $('#subItemListTbl tbody>tr');
            tableTR.removeClass('hideTr evenTR oddTR');

            tableTR.each(function () {
                var dataValue = '' + $(this).attr('data-value') + '';
                dataValue = dataValue.toLocaleLowerCase();

                if (searchKey != '') {
                    if (dataValue.indexOf('' + searchKey + '') == -1) {
                        $(this).addClass('hideTr');
                    }
                }
                else {

                }
            });

            //applyRowNumbers();
        });

        function applyRowNumbers() {
            var m = 1;
            $('#details_table tbody>tr').each(function (i) {
                if (!$(this).hasClass('hideTr')) {
                    var isEvenRow = ( m % 2 );
                    if (isEvenRow == 0) {
                        $(this).addClass('evenTR');
                    } else {
                        $(this).addClass('oddTR');
                    }

                    $(this).find('td:eq(0)').html(m);
                    m += 1;
                }
            });

            $('#showingCount').text((m - 1));
        }

        $('#subItemListTbl').tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0
        });

        $(".subItem").change(function () {
            var maxCount = $('.qty').val();
            var totSelected = $(".subItem:checked").length;
            if ($(this).is(':checked')) {
                if(totSelected > maxCount){
                    $(this).prop('checked', false);
                    myAlert('w','You have selected maximum amount of item/s');
                    totSelected -= 1;
                }
            }

            $('#subItemCount').text( totSelected );
        });


        updateCount();
    });

    function update_quantity_sec_uom(){
        var sec_qty = $('#sec_qty').val();
        var secondaryUOMID = $('#secondaryUOMID').val();
        var detail_autoID = $('#detail_autoID').val();
        var documentID = '<?php echo $documentID ?>';
        if(sec_qty > 0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {documentID: documentID,detail_autoID: detail_autoID,secondaryUOMID: secondaryUOMID,sec_qty: sec_qty},
                url: "<?php echo site_url('Inventory/update_quantity_sec_uom'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(data[0] == 's'){
                        myAlert(data[0],data[1]);
                    }
                    $('.qty').val(data[2]);
                    $('#exact_quantity').html(data[2]);
                    $('#sec_qty').val('');
                    $('.btn-group').html('<button onclick="selectNItems('+ data[2] +')" type="button" class="btn btn-default"> Select First '+ data[2] +' item/s. </button> <button onclick="unSelectAll()" type="button" class="btn btn-default">un-select all</button>');

                }, error: function (xhr, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', xhr.responseText + ' : ' + errorThrown);
                }
            });
        }else{
            $('#sec_qty').val('');
            myAlert('w','Changed quantity value should be greater than 0!');
        }   
    }
</script>
