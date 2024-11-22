<?php
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<style>
    .fc {
        height: 22px !important;
        width: 100% !important;
        display: inline !important;
        margin: 0px !important;
    }

    .arrowDown {
        vertical-align: sub;
        color: rgb(75, 138, 175);
        font-size: 13px;
    }

    .applytoAll {
        display: none;
        vertical-align: top;
    }


</style>
<?php
if(!empty($attributes)){
    ?>
    <table class="table table-bordered table-condensed " id="tbl_itemMasterSub_tmp" onmouseleave="clearDownArrow();">
        <thead>
        <tr>
            <th>#</th>
            <th style="width:13%">Code</th>
            <?php
            $i=1;
            foreach($attributes as $valu){
                if($i==2){
                    ?>
                    <th>UOM</th>
                    <th style="min-width:75px;"><?php echo $valu['attributeDescription'] ?></th>
                    <?php
                }else{
                    ?>
                    <th style="min-width:75px;"><?php echo $valu['attributeDescription'] ?></th>
                    <?php
                }
                $i++;
            }
            ?>
        </tr>
        </thead>
        <tbody id="tbl_body_item_master_sub">
        <?php
        if (isset($itemMasterSubTemp) && !empty($itemMasterSubTemp)) {
            $i = 1;
            foreach ($itemMasterSubTemp as $key => $item) {
                ?>
                <tr id="subiIemRow_<?php echo $item['subItemAutoID'] ?>" data-idnex="<?php echo $key; ?>"
                class="common_row<?php echo $key; ?>">
                <td> <?php echo $item['subItemSerialNo']; ?> </td>
                <td> <?php echo $item['subItemCode']; ?><input type="hidden" name="subItemAutoID[]" value="<?php echo $item['subItemAutoID'] ?>"> </td>
                <?php
                $i = 1;
                foreach($attributes as $valu){
                    if($i==2){
                        ?>
                        <td> <?php echo $item['uom']; ?> </td>
                        <?php
                        if($valu['attributeType']==1){
                        $type='Text';
                        $class='';
                        }else{
                        $type='Text';
                        $class='expiryDate';
                        }
                        ?>
                        <td class="tdCol">
                            <input style="width:100% !important;" type="<?php echo $type; ?>" class="form-control fc <?php echo $class; ?>"
                                   value="<?php echo $item[$valu['columnName']]; ?>"
                                   name="<?php echo $valu['columnName'] ?>[]">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button>
                    </span>
                        </td>
                        <?php
                    }else{
                        if($valu['attributeType']==1){
                            $type='Text';
                            $class='';
                        }else{
                            $type='Text';
                            $class='expiryDate';
                        }
                        ?>
                        <td class="tdCol">
                            <input style="width:100% !important;" type="<?php echo $type; ?>" class="form-control fc <?php echo $class; ?>"
                                   value="<?php echo $item[$valu['columnName']]; ?>"
                                   name="<?php echo $valu['columnName'] ?>[]">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button>
                    </span>
                        </td>
                        <!--<td> <?php /*echo $item[$valu['columnName']]; */?> </td>-->
                        <?php
                    }
                    $i++;
                }
            }
            ?>
            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
<?php
}else{
    ?>
    <div class="alert alert-danger" role="alert">
        <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
        <span class="sr-only">Not Found:</span><!--Not Found-->
        No Records Found!
    </div>
<?php
}
?>

<script>
    var date_format_policy = '<?php echo $date_format_policy ?>';
    $(document).ready(function (e) {
        //$("#tbl_itemMasterSub_tmp").dataTable();
        Inputmask({alias: date_format_policy}).mask(document.querySelectorAll('.expiryDate'));

        $(".tdCol").hover(function (eventObject) {
            $(".applytoAll").hide();
            $(this).closest('td').find('span').show()
        })
    });

    function clearDownArrow() {
        $(".applytoAll").hide();
    }

    function applyToAllCols(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var textValue = $(element).closest('td').find('input').val();
                var elementTr = $(element).closest('tr').index()
                var elementTd = $(element).closest('td').index()
                var totalTr = $('#tbl_body_item_master_sub tr').length - 1;


                for (var i = elementTr; i <= totalTr; i++) {
                    $(".common_row" + i + " td:eq(" + elementTd + ")").find('input').val(textValue);
                }
            });
    }



</script>