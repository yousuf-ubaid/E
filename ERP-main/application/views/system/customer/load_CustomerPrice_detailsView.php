<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .tdIn {
        width: 140px;
        padding: 2px;

    }

    .customerPriceAmount {
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 70px;
    }

    tr:hover, tr.selected {
        background-color: #E3E1E7;
        opacity: 1;
        z-index: -1;
    }

    .table-striped tbody tr.highlight td {
        background-color: #E3E1E7;
    }

    body table tr.selectedRow {
        background-color: #fff2e1;
    }

    tfoot {
        font-size: 11px;
    }

    .customerPriceAmount {
        text-align: right;
    }

    a.hoverbtn {
        margin-left: 5px;
    }

</style>

<div class="col-md-12" style="">
    <div style="height: 600px">
    <table id="customerPriceDetails_view" class="" style="width: 100%">
        <thead>
        <tr>
            <th style="border: 2px solid #ffffff; min-width: 40px; padding: 8px">#</th>
            <th style="border: 2px solid #ffffff; min-width: 150px; padding: 8px"><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
            <?php
            if ($header) {
                foreach ($header as $items) {?>
                    <th style="border: 2px solid #ffffff; padding: 8px"><span title="" rel="tooltip" class=""><?php echo $items['itemSystemCode'] . "<br>(" . $items['seconeryItemCode'] . ")<br>" . $items['itemDescription']?></span></th>
        <?php    }
            } ?>
        </tr>
        </thead>

        <tbody>
        <?php if ($details) {
            $a = 1;
            $category = array_group_by($details, 'partyCategoryID');
            foreach ($category as $key => $partyCategoryID) {
                echo "<tr style='line-height: 24px; font-weight: bold; '><td colspan='13'><div style='color: darkblue'><strong>" . $key . "</strong></div></td></tr>";
                foreach ($partyCategoryID as $key => $det) {
                    echo '<tr>';
                    echo '<td>' . $a .  '</td>';
                    echo '<td> &nbsp;&nbsp;' . $det['customerSystemCode'] .  '&nbsp;&nbsp;</td>';

                    if ($header) {
                        $b = 1;
                        foreach ($header as $items) { ?>
                            <td class="tdIn" style="text-align:center; <?php if($b%2 != 0) { echo 'background-color: #f2f2f2'; } ?>">
                                <input class="form-control customerPriceAmount" type="text" name="customerPriceAmount" id="groupAdd_<?php echo $a . '_' . $b;?>"
                                       data-cpsAutoID="<?php echo $det['cpsAutoID']?>"
                                       data-customerAutoID="<?php echo $det['customerAutoID'] ?>"
                                       data-customerPriceID="<?php echo $det[$items['itemAutoID'] . '_customerPriceID'] ?>"
                                       data-itemID="<?php echo ($det[$items['itemAutoID'] . '_1'])?>"
                                       value="<?php echo number_format($det[$items['itemAutoID']],$this->common_data['company_data']['company_default_decimal'],'.', '') ?>"
                                ><a class="hoverbtn" onclick="applyPriceBtn(this)"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a>
                            </td>
                            <?php $b++;
                        }
                    }
                    echo '</tr>';
                    $a++;
                }
            }
        } ?>
        </tbody>
    </table>
</div>
</div>

<br>
<script>
    $('#customerPriceDetails_view').tableHeadFixer({
        head: true,
        foot: true,
        left: 2,
        right: 0,
        'z-index': 10
    });

    $('.hoverbtn').hide();
    $('table').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
        $('.hoverbtn').hide();
        $(this).find(".hoverbtn").show();
    });


    $(".customerPriceAmount").change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }

        var cpsAutoID = $(this).attr('data-cpsAutoID');
        var customerAutoID = $(this).attr('data-customerAutoID');
        var customerPriceID = $(this).attr('data-customerPriceID');
        var itemID = $(this).attr('data-itemID');
        var customerPrice = $(this).val();
        $(this).val(parseFloat(customerPrice).toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        update_customer_price_row(cpsAutoID, customerAutoID, customerPriceID, customerPrice, itemID);
    });

    function applyPriceBtn(element) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure')?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_apply_this_to_all')?>!",/*You want to apply this to all"*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm')?>"/*Confirm*/
            },
            function () {
                var myArray = [];
                var maxLength = ($('#customerPriceDetails_view tbody tr').length);
                var thisNum = $(element).closest('td').find('input').attr('id');
                var thisGroupID = $('#'+thisNum).val();
                thisNum = thisNum.split('_');
                var thisNumCol = parseInt(thisNum[1]);
                var thisNumRow = parseInt(thisNum[2]);

                xi = 0;
                while(maxLength > thisNumCol ){
                    thisNumCol++;
                    var thisTD = $('#groupAdd_'+thisNumCol + '_' + thisNumRow);

                    $(thisTD).val(thisGroupID);

                        myArray[xi] = {};
                        myArray[xi]['cpsAutoID'] = $(thisTD).attr('data-cpsAutoID');
                        myArray[xi]['customerAutoID'] = $(thisTD).attr('data-customerAutoID');
                        myArray[xi]['customerPriceID'] = $(thisTD).attr('data-customerPriceID');
                        myArray[xi]['itemID'] = $(thisTD).attr('data-itemID');
                        myArray[xi]['customerPrice'] = thisGroupID;

                    xi++;
                }
                update_all_customer_price(myArray);
            });
    }

    function update_all_customer_price(myArray) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                myArray: myArray

            },
            url: "<?php echo site_url('Customer/update_all_customer_price'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                //
            },
            error: function () {

            }
        });
    }

    function update_customer_price_row(cpsAutoID, customerAutoID, customerPriceID, customerPrice, itemID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                cpsAutoID: cpsAutoID,
                customerAutoID: customerAutoID,
                customerPriceID: customerPriceID,
                customerPrice: customerPrice,
                itemID: itemID
            },
            url: "<?php echo site_url('Customer/update_customer_price'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                if(data == false){
                    myAlert('e', 'An Error Occurred, Please try Again');
                }
            },
            error: function () {

            }
        });
    }

</script>