<style>
    .tdIn {
        width: 140px;
        padding: 2px;

    }

    .form-control {
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

    .amount {

        text-align: right;
        width: 60px;

    }

    a.hoverbtn {
        margin-left: 5px;
    }

</style>
<?php
      $budgetpolicy = getPolicyValues('BFOR', 'All');
?>

<div class="col-md-8" style="margin-bottom: 10px">
    <label>Document Code: &nbsp;&nbsp;&nbsp;</label> <?php echo $master['documentSystemCode']; ?></td>
    <label> &nbsp;&nbsp;&nbsp;Financial Year:
        &nbsp;&nbsp;&nbsp; </label><?php $finance = get_financial_from_to($master['companyFinanceYearID']);
    echo $finance['beginingDate'] . ' | ' . $finance['endingDate'] ?></td>
    <label> &nbsp;&nbsp;&nbsp;Segment:&nbsp;&nbsp;&nbsp; </label><?php echo $master['description']; ?>
    <label> &nbsp;&nbsp;&nbsp;Currency: &nbsp;&nbsp;&nbsp; </label><?php echo $master['transactionCurrency']; ?>

</div>
<?php

?>



<div class="col-md-12" style="">


    <table id="budget_view" class="table-striped" style="width: 100%">
        <thead>
        <tr>
            <th style="width: 20%">Category</th>
            <!--<th style="width: 120px">Master Account</th>
            <th>GL</th>-->
            <?php
            if ($months_all) {
                foreach ($months_all as $month) {
                    ?>
                    <th>
                        <?php echo $month['MonthName'] ?>
                    </th>
                    <?php
                }
            } ?>
        </tr>
        </thead>

        <tbody>
        <?php

        if ($detail) {
            $finaPeriod[] = '';

            foreach ($activeFP as $actvFP) {
                $dtfrm = explode('-', $actvFP['dateFrom']);
                $month = $dtfrm[1];
                array_push($finaPeriod, $month);
            }
            $accountCategoryDesc = "";
            $masterAccount = "";
            $category = array_group_by($detail, 'mainCategory', 'subCategory');

            foreach ($category as $key => $maincategory) {
                /* if ($accountCategoryDesc != $value['accountCategoryDesc']) {
                     $accountCategoryDesc = $value['accountCategoryDesc'];
                 } else {
                     $accountCategoryDesc = "";
                 }
                 if ($masterAccount != $value['masterAccount']) {
                     $masterAccount = $value['masterAccount'];
                 } else {
                     $masterAccount = "";
                 }*/
                echo "<tr style='line-height: 24px;
    font-weight: bold;'><td colspan='13'><div style='color: darkblue'><strong>" . $key . "</strong></div></td></tr>";


                $total['month_1'] =0;
                $total['month_2'] = 0;
                $total['month_3'] = 0;
                $total['month_4'] =0;
                $total['month_5'] = 0;
                $total['month_6'] = 0;
                $total['month_7'] = 0;
                $total['month_8'] =0;
                $total['month_9'] = 0;
                $total['month_10'] =0;
                $total['month_11'] =0;
                $total['month_12'] = 0;
                /*$totoalmyFeb = 0;
                $totoalmyJan = 0;
                $totoalmyMar = 0;
                $totoalmyApr = 0;
                $totoalmyMay = 0;
                $totoalmyJun = 0;
                $totoalmyJul = 0;
                $totoalmyAug = 0;
                $totoalmySep = 0;
                $totoalmyOct = 0;
                $totoalmyNov = 0;
                $totoalmyDec = 0;
                $totoal = 0;*/

                foreach ($maincategory as $key2 => $subcategory) {



                    $subtotal = array();
                    echo "<tr><td colspan='13'><div style='margin-left:15px;color: blue'><strong>" . $key2 . "</strong></div></td></tr>";

                    
                    $make_minus = 1;
                    if($budgetpolicy == 1){
                        $make_minus = $make_minus * -1;
                    }elseif($budgetpolicy == 2){
                        if($key == 'EXPENSE'){
                            $make_minus = $make_minus * -1;
                        }
                    }      

                    foreach ($subcategory as $value) {

                        // echo '<pre>'; print_r($subcategory); exit;

                        foreach ($months_all as $monthval) {                     
                      


                            if($monthval['month'] == 1){
                                $total['month_1'] += $value['month_1'] * $make_minus;
                            }else if($monthval['month'] == 2){
                                $total['month_2'] += $value['month_2'] * $make_minus;
                            }else if($monthval['month'] == 3){
                                $total['month_3'] += $value['month_3'] * $make_minus;
                            }else if($monthval['month'] == 4){
                                $total['month_4'] += $value['month_4'] * $make_minus;
                            }else if($monthval['month'] == 5){
                                $total['month_5'] += $value['month_5'] * $make_minus;
                            }else if($monthval['month'] == 6){
                                $total['month_6'] += $value['month_6'] * $make_minus;
                            }else if($monthval['month'] == 7){
                                $total['month_7'] += $value['month_7'] * $make_minus;
                            }else if($monthval['month'] == 8){
                                $total['month_8'] += $value['month_8'] * $make_minus;
                            }else if($monthval['month'] == 9){
                                $total['month_9'] += $value['month_9'] * $make_minus;
                            }else if($monthval['month'] == 10){
                                $total['month_10'] += $value['month_10'] * $make_minus;
                            }else if($monthval['month'] == 11){
                                $total['month_11'] += $value['month_11'] * $make_minus;
                            }else if($monthval['month'] == 12){
                                $total['month_12'] += $value['month_12'] * $make_minus;
                            }


                            /*$totoal += $value['month_'.$monthval['month']];*/
                            //$subtotal[""] = ;
                        }  ?>

                        
                        <tr>
                            <!--<td><?php /*echo $accountCategoryDesc */ ?></td>
                            <td><?php /*echo $masterAccount */ ?></td>-->
                            <td>
                                <div style='margin-left:25px'><?php echo $value['GLDescription'] ?></div>
                            </td>
                            <?php foreach ($months_all as $monthval) {?>
                            <td class="tdIn">
                                <?php
                                    $amount = $value['month_'.$monthval['month']];
                                   
                                    if($budgetpolicy == 1){
                                        $amount = $amount * -1;
                                    }elseif($budgetpolicy == 2){
                                        if($key == 'EXPENSE'){
                                            $amount = $amount * -1;
                                        }
                                    }                            


                                ?>

                                <input class="form-control amount" type="text" name="amount"
                                       data-name="<?php echo $key?>" data-budgetYear="<?php echo $monthval['budgetyear'] ?>"
                                       data-budgetMonth="<?php echo $monthval['month']?>"
                                       data-GLAutoID="<?php echo $value['GLAutoID'] ?>"  data-Type="<?php echo $key ?>"
                                       value="<?php echo number_format($amount ,2,'.', '') ?>"><a class="hoverbtn"
                                                                                onclick="applybtn(this)"><i
                                            class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                            </td>
                            <?php }?>
                        </tr>
                        <?php
                        /* $accountCategoryDesc = $value['accountCategoryDesc'];
                         $masterAccount = $value['masterAccount'];*/
                    }

                }
                ?>


                <tr style="margin: 10px;line-height: 24px;
                    font-weight: bold;background-color: #d4d2d2">
                    <td>TOTAL <?php echo $key ?></td>

                     <?php foreach ($months_all as $monthval) {
                    
                    ?>

                    <?php
                        $amount = $total['month_'.$monthval['month']];
                      //  echo '<pre>'; print_r($amount); exit;
                    ?>

                   <td style="text-align: right;padding-right: 29px; font-size: 11px"><span id="<?php echo $key.'_'.$monthval['month'] ?>"> <?php echo format_number($amount,2)  ?></span></td>
                     </td>
                    <?php }?>
                </tr>
                <?php
            }
        } ?>

        </tbody>
        <tfoot>
        <tr style="line-height: 24px;
            font-weight: bold;background-color: black;color: white">
            <td>Net Profit / (Loss)</td>
            <?php foreach ($months_all as $monthval) {?>
            <td style="text-align: right;padding-right: 29px; font-size: 11px" id="<?php echo $monthval['month']?>"></td>
            <?php }?>
        </tr>
        </tfoot>
    </table>
    <br>
    <div class="text-right m-t-xs">

        <button class="btn btn-warning" onclick="load_missing_gl_tobudget()">Load</button>
        <button class="btn btn-primary" onclick="save_draft()">Save &amp; Draft</button>
        <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
    </div>
    <br>
</div>

<br>
<script>
    var budgetpolicy = <?php echo ($budgetpolicy) ? $budgetpolicy : 1; ?>

    $('#budget_view').tableHeadFixer({
     head: true,
     foot: true,
     left: 0,
     right: 0,
     'z-index': 10
     });
    $(".amount").keydown(function (event) {
        if (event.shiftKey == true) {
            event.preventDefault();
        }
        if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110 || event.keyCode == 109 || event.keyCode == 109 || event.keyCode == 189
        ) {
        } else {
            event.preventDefault();
        }
        if ($(this).val().indexOf('.') !== -1 && (event.keyCode == 190 || event.keyCode == 110))
            event.preventDefault();
    });

    var budgetAutoID = <?php echo json_encode($this->input->post('budgetAutoID'))?>;
    get_budget_footer_total(budgetAutoID);
    /*$('hoverbtn').addClasses('hide');*/
    $('.hoverbtn').hide();
    $('table').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
        $('.hoverbtn').hide();
        $(this).find(".hoverbtn").show();
    });


    $('.amount').change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }

        var glAutoID = $(this).attr('data-GLAutoID');
        var budgetyear = $(this).attr('data-budgetYear');
        var budgetmonth = $(this).attr('data-budgetmonth');
        var amount = $(this).val();
        var type = $(this).attr('data-Type');
        $(this).val(parseFloat(amount).toFixed(2));

        update_budget_row(glAutoID, budgetyear, budgetmonth, amount, budgetAutoID,type);
    });

    function applybtn(id) {
        var myArray = [];
        $(id).closest('td').find("input").each(function () {

            baseAmount = this.value;//this.value
        });


        xi = 0;
        $(id).closest('td').nextAll().find('input').each(function (n) {
            $(this).val(baseAmount);
            myArray[xi] = {};
            myArray[xi]['GLAutoID'] = $(this).attr('data-GLAutoID');
            myArray[xi]['budgetYear'] = $(this).attr('data-budgetYear');
            myArray[xi]['budgetMonth'] = $(this).attr('data-budgetmonth');
            myArray[xi]['amount'] = baseAmount;

            xi++;
        });
        update_apply_all_row(myArray);


    }

    function update_apply_all_row(myArray) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                myArray: myArray, budgetAutoID: budgetAutoID

            },
            url: "<?php echo site_url('Budget/update_apply_all_row'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                get_budget_footer_total(budgetAutoID);

            },
            error: function () {

            }
        });
    }

    function update_budget_row(glAutoID, budgetyear, budgetmonth, amount, budgetAutoID,type) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                glAutoID: glAutoID,
                budgetyear: budgetyear,
                budgetmonth: budgetmonth,
                amount: amount,
                type: type,
                budgetAutoID: budgetAutoID
            },
            url: "<?php echo site_url('Budget/update_budget_row'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                get_budget_footer_total(budgetAutoID);

            },
            error: function () {

            }
        });
    }

    function save_draft() {
        fetchPage('system/finance/Budget_management', 'Test', 'Budget');
    }

    function confirmation() {
        if (budgetAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm this document !",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'budgetAutoID': budgetAutoID},
                        url: "<?php echo site_url('Budget/budget_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0],data[1]);
                            fetchPage('system/finance/Budget_management', 'Test', 'Budget');
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function get_budget_footer_total(budgetAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {budgetAutoID: budgetAutoID},
            url: "<?php echo site_url('Budget/get_budget_footer_total'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                /*    $('#detailData').html(data);*/
                /*     stopLoad();
                 refreshNotifications(true);*/
                myJan=0;
                myFeb=0;
                myMar=0;
                myApr=0;
                myMay=0;
                myJun=0;
                myJul=0;
                myAug=0;
                mySep=0;
                myOct=0;
                myNov=0;
                myDec=0;
                for (val of data['detail']) {
                    var make_minus = 1;
                    if(budgetpolicy == 2 && val['mainCategory'] == 'EXPENSE'){
                        make_minus = -1;
                    }

                    subMyJan =parseFloat(val['month_1'] * make_minus);
                    subMyFeb =parseFloat(val['month_2'] * make_minus);
                    subMyMar =parseFloat(val['month_3'] * make_minus);
                    subMyApr =parseFloat(val['month_4'] * make_minus);
                    subMyMay =parseFloat(val['month_5'] * make_minus);
                    subMyJun =parseFloat(val['month_6'] * make_minus);
                    subMyJul =parseFloat(val['month_7'] * make_minus);
                    subMyAug =parseFloat(val['month_8'] * make_minus);
                    subMySep =parseFloat(val['month_9'] * make_minus);
                    subMyOct =parseFloat(val['month_10'] * make_minus);
                    subMyNov =parseFloat(val['month_11'] * make_minus);
                    subMyDec =parseFloat(val['month_12'] * make_minus);


                    $('#'+val['mainCategory']+'_1').html(commaSeparateNumber(subMyJan * make_minus, 2));
                    $('#'+val['mainCategory']+'_2').html(commaSeparateNumber(subMyFeb * make_minus, 2));
                    $('#'+val['mainCategory']+'_3').html(commaSeparateNumber(subMyMar * make_minus, 2));
                    $('#'+val['mainCategory']+'_4').html(commaSeparateNumber(subMyApr * make_minus, 2));
                    $('#'+val['mainCategory']+'_5').html(commaSeparateNumber(subMyMay * make_minus, 2));
                    $('#'+val['mainCategory']+'_6').html(commaSeparateNumber(subMyJun * make_minus, 2));
                    $('#'+val['mainCategory']+'_7').html(commaSeparateNumber(subMyJul * make_minus, 2));
                    $('#'+val['mainCategory']+'_8').html(commaSeparateNumber(subMyAug * make_minus, 2));
                    $('#'+val['mainCategory']+'_9').html(commaSeparateNumber(subMySep * make_minus, 2));
                    $('#'+val['mainCategory']+'_10').html(commaSeparateNumber(subMyOct * make_minus, 2));
                    $('#'+val['mainCategory']+'_11').html(commaSeparateNumber(subMyNov * make_minus, 2));
                    $('#'+val['mainCategory']+'_12').html(commaSeparateNumber(subMyDec * make_minus, 2));


                    myJan += subMyJan;
                    myFeb += subMyFeb;
                    myMar += subMyMar;
                    myApr += subMyApr;
                    myMay += subMyMay;
                    myJun += subMyJun;
                    myJul += subMyJul;
                    myAug += subMyAug;
                    mySep += subMySep;
                    myOct += subMyOct;
                    myNov += subMyNov;
                    myDec += subMyDec;

                }


                $('#1').html(commaSeparateNumber(myJan, 2));
                $('#2').html(commaSeparateNumber(myFeb, 2));
                $('#3').html(commaSeparateNumber(myMar, 2));
                $('#4').html(commaSeparateNumber(myApr, 2));
                $('#5').html(commaSeparateNumber(myMay, 2));
                $('#6').html(commaSeparateNumber(myJun, 2));
                $('#7').html(commaSeparateNumber(myJul, 2));
                $('#8').html(commaSeparateNumber(myAug, 2));
                $('#9').html(commaSeparateNumber(mySep, 2));
                $('#10').html(commaSeparateNumber(myOct, 2));
                $('#11').html(commaSeparateNumber(myNov, 2));
                $('#12').html(commaSeparateNumber(myDec, 2));

            },
            error: function () {

            }
        });
    }


    function load_missing_gl_tobudget(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'budgetAutoID': <?php echo $master['budgetAutoID'] ?>},
            url: "<?php echo site_url('Budget/load_missing_gl_tobudget'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if(data[0]=='s'){
                    fetchPage("system/budget/erp_budget_detail_page","<?php echo $master['budgetAutoID'] ?>","Budget Detail ","Budget Detail");
                }

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


</script>



