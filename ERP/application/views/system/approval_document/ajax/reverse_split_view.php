<?php
    $this->load->helper('reversing');
    $gl_codes = reversing_fetch_all_gl_codes();
    $base_segment_arr = array();
    $segment_arr = reversing_get_segemnt();

?>

<div class="col-md-12">
    <h6 class="modal-title" id="myModalLabel_title" style="color: red;font-size: 13px;">*** You cannot reverse this action.</h6>
    <div class="row">

        <input type="hidden" name="split_autoid" id="split_autoid" value="" >

        <div class="form-group">

            <label class="col-sm-6 control-label">Document Number </label><!--Comments-->
            <div class="col-sm-6">
                <input type="hidden" id="document_code" name="document_code" value="<?php echo $document_code ?>" >
                <span class="form-control"><?php echo $document_code ?> </span>
            </div>

        </div>
        <div class="form-group">

            <label class="col-sm-6 control-label">Document Date </label><!--Comments-->
            <div class="col-sm-6">
                <input type="hidden" id="document_date" name="document_date" value="<?php echo $document_date ?>" >
                <span class="form-control"><?php echo $document_date ?> </span>
            </div>

        </div>
        <div class="form-group">

            <label class="col-sm-6 control-label">Segment </label><!--Comments-->
            <div class="col-sm-6">
                <?php echo form_dropdown('segment', $segment_arr, '', 'class="form-control select2" id="segment" onchange="check_all_selected()" required'); ?>
            </div>

        </div>
        <div class="form-group">

            <label class="col-sm-6 control-label">Credit GL Code </label><!--Comments-->
            <div class="col-sm-6">
                <?php echo form_dropdown('cr_gl_code', $gl_codes, '', 'class="form-control select2" id="cr_gl_code" onchange="check_all_selected()" required'); ?>
            </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Debit GL Code </label><!--Comments-->
            <div class="col-sm-6">
                <?php echo form_dropdown('dr_gl_code', $gl_codes, '', 'class="form-control select2" id="dr_gl_code" onchange="check_all_selected()" required'); ?>
            </div>

        </div>
        
    </div>

    <div class="row">
        <div class="form-group">

        <div class="table-responsive">
       
            <table id="tbl_split_calc" class="<?php echo table_class() ?> hide">
                <thead>
                    <tr>
                        <th style="min-width: 10%">GL Auto ID</th><!--Code-->
                        <th style="min-width: 10%">GL Code</th><!--Code-->
                        <th style="min-width: 10%">GL Description</th><!--Code-->
                        <th style="min-width: 10%">Segment</th><!--Code-->
                        <th style="min-width: 10%">Record Type</th><!--Code-->
                        <th style="min-width: 10%">Type</th><!--Code-->
                        <th style="min-width: 10%">Amount</th><!--Code-->
                        <th style="min-width: 10%">Adjusted (+/-)</th><!--Code-->
                        <th style="min-width: 10%">Final Value</th><!--Code-->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($selected as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['generalLedgerAutoID'] ?></td>
                            <td><?php echo $value['GLCode'] ?></td>
                            <td><?php echo $value['GLDescription'] ?></td>
                            <td><?php echo $value['segmentCode'] ?></td>
                            <td><?php echo $value['amount_type'] ?></td>
                            <td><?php echo $value['GLType'] ?></td>
                            <td><span class="text-bold"><?php echo $value['transactionCurrency'] ?>&nbsp</span></span><span id="amount_<?php echo $value['amount_type'] ?>" class="amount value"><?php echo number_format($value['transactionAmount'],2) ?></span></td>
                            <td><input type="number" class="form-control adjust_value" id="adjust_value_<?php echo $value['amount_type'] ?>" onchange="recalculate_row($(this))"/></td>
                            <td><span class="text-bold"><?php echo $value['transactionCurrency'] ?></span>&nbsp<span id="final_amount_<?php echo $value['amount_type'] ?>" class="fianl_amount value"><?php echo number_format($value['transactionAmount'],2) ?></span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <br><br>

            <table id="tbl_split_amount" class="<?php echo table_class() ?> hide">
                <thead>
                    <tr>
                        <th style="min-width: 10%">GL Code</th><!--Code-->
                        <th style="min-width: 10%">GL Description</th><!--Code-->
                        <th style="min-width: 10%">Segment</th><!--Code-->
                        <th style="min-width: 10%">Record Type</th><!--Code-->
                        <th style="min-width: 10%">Type</th><!--Code-->
                        <th style="min-width: 10%">Amount</th><!--Code-->
                        <th style="min-width: 10%">Adjusted (+/-)</th><!--Code-->
                        <th style="min-width: 10%">Final Value</th><!--Code-->
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    
    $('.select2').select2({
        tags: true,
        dropdownParent: $("#myModalLabel_title")
    });

    function adjusment_recalcualte(){
        var adjusted_value = $('#adjusted_value').val();
 
        $(".adjust_value").each(function(){
            $(this).val(adjusted_value);
           // $(this).attr('disabled',true);
        });
    }

    function recalculate_row(val){

        var amount = val.closest('tr').find('td:eq(6) .value').text();
        var record_type = val.closest('tr').find('td:eq(4)').text();
        var change_amount = val.val();
        var final_value = parseFloat(amount) + parseFloat(change_amount);
     

        //record type transfer
        if(record_type == 'cr'){
            $('#cr_value').val(parseFloat(change_amount).toFixed(2));
            $('#cr_value').trigger('onchange');
            $('#cr_value').attr('disabled',true);
        }else{
            $('#dr_value').val(parseFloat(change_amount).toFixed(2));
            $('#dr_value').trigger('onchange');
            $('#dr_value').attr('disabled',true);
        }

        if(record_type == 'cr'){
            $('#final_amount_cr').text(parseFloat(final_value).toFixed(2));
        }else{
            $('#final_amount_dr').text(parseFloat(final_value).toFixed(2));
        }
     
        //val.closest('tr').find('td:eq(7)').text(parseFloat(final_value).toFixed(2));
     
    }

    function reverse_calc_split(val){

        var amount = val.closest('tr').find('td:eq(5)').text();
        var cr_dr = val.closest('tr').find('td:eq(4)').text();
        var record_type = val.closest('tr').find('td:eq(3)').text();
        var change_amount = val.val();
        var final_value = parseFloat(amount) + parseFloat(change_amount);

        if(record_type == 'Credit'){
            $('#cr_final_val').text(parseFloat(final_value).toFixed(2));
        }else{
            $('#dr_final_val').text(parseFloat(final_value).toFixed(2));
        }

        val.closest('tr').find('td:eq(8)').text(parseFloat(final_value).toFixed(2));

    }

    function check_all_selected(){

        var segment = $('#segment').val();
        var credit_gl = $('#cr_gl_code').val();
        var debit_gl = $('#dr_gl_code').val();

        var segment_text = $('#segment :selected').text().split('|');
        var credit_gl_text = $('#cr_gl_code :selected').text().split('|');
        var debit_gl_text = $('#dr_gl_code :selected').text().split('|');
       
        if(segment == '' || credit_gl == '' || debit_gl == ''){

        }else{

            //append rows
            $('#tbl_split_amount tbody').empty();

            var table_row_cr = '<tr><td>'+credit_gl_text[0]+'</td><td>'+credit_gl_text[2]+'</td><td>'+segment_text[1]+'</td><td>Credit</td><td>'+credit_gl_text[3]+'</td><td>0.00</td><td><input type="text" id="cr_value" onchange="reverse_calc_split($(this))" ></td><td><span class="text-bold"><?php echo $value['transactionCurrency'] ?></span>&nbsp<span id="cr_final_val">0.00</span></td></tr>';
            $('#tbl_split_amount tbody').append(table_row_cr);

            var table_row_dr = '<tr><td>'+debit_gl_text[0]+'</td><td>'+debit_gl_text[2]+'</td><td>'+segment_text[1]+'</td><td>Debit</td><td>'+debit_gl_text[3]+'</td><td>0.00</td><td><input type="text" id="dr_value" onchange="reverse_calc_split($(this))"></td><td><span class="text-bold"><?php echo $value['transactionCurrency'] ?></span>&nbsp<span id="dr_final_val">0.00</span></td></tr>';
            $('#tbl_split_amount tbody').append(table_row_dr);


            $('#tbl_split_calc').removeClass('hide');
            $('#tbl_split_amount').removeClass('hide');
        }
    }

    function submitSplitDocument(){

        var credit_adjust = $('#adjust_value_cr').val();
        var debit_adjust = $('#adjust_value_dr').val();
        var credit_amount = $('#amount_cr').text();
        var debit_amount = $('#amount_dr').text();
        var final_amount_credit = $('#final_amount_cr').text();
        var final_amount_debit = $('#final_amount_dr').text();

        var segment = $('#segment :selected').val();
        var cr_gl_code = $('#cr_gl_code :selected').val();
        var dr_gl_code = $('#dr_gl_code :selected').val();

        var document_code = $('#document_code').val();
        var document_date = $('#document_date').val();

        var gl_arr = new Array();

        if(parseFloat(Math.abs(final_amount_credit)) != parseFloat(Math.abs(final_amount_debit))){
            myAlert('e', 'Credit and debit amount not matched')
            return false;
        }

        $('#tbl_split_calc > tbody  > tr').each(function(index, tr) { 
            var gl_autoid = $(this).find('td:eq(0)').text();
            var cr_dr = $(this).find('td:eq(4)').text();
            var obj = {name:cr_dr,gl:gl_autoid};
            gl_arr.push(obj);
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'gl_arr': gl_arr,'credit_adjust':credit_adjust,'debit_adjust':debit_adjust,'credit_amount':credit_amount,'debit_amount':debit_amount,'final_amount_credit':final_amount_credit,'final_amount_debit':final_amount_debit,'segment':segment,'cr_gl_code':cr_gl_code,'dr_gl_code':dr_gl_code,'document_code':document_code,'document_date':document_date},
            url: "<?php echo site_url('Reversing_approval/set_gl_split_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
              
            }, error: function () {
                stopLoad();
            }
        });



        return false;
    }
   

</script>



