<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;">
                    <center><form id="img_uplode_form"><input type="hidden" id="uplode_salesPersonID" value="<?php echo $extra['head']['salesPersonID']; ?>" name="salesPersonID"><div class="fileinput-new thumbnail" style="width: 160px; height: 170px;">
                   <?php if($extra['head']['salesPersonImage']!='images/users/default.gif'){?>
                          <img src="<?php echo $salespersonimage;?>" id="changeImg">
                                <?php }else {?>
                             <img src="<?php echo $noimage;?>" id="changeImg">
                                <?php }?>





                        <input type="file" name="img_file" id="img_file" style="display: none;"  onchange="loadImage(this)"/>
                    </div></form></center>
                </td>
                <td style="width:70%;">
                    <table>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_name');?>  </td><!--Name-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['SalesPersonName']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_code');?>  </td><!--Code-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['SalesPersonCode']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('sales_maraketing_masters_secondary_code');?>  </td><!--Secondary Code-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['SecondaryCode']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_telephone');?>   </td><!--Telephone-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['contactNumber']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_email');?>   </td><!--Email-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['SalesPersonEmail']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_Location');?>   </td><!--Location-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['wareHouseDescription']; ?></td>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('common_address');?>  </td><!--Address-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['SalesPersonAddress']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"> <?php echo $this->lang->line('common_currency');?>   </td><!--Currency-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['salesPersonCurrency']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('sales_maraketing_masters_target');?>  [ <?php echo ($extra['head']['salesPersonTargetType']==1 ? $this->lang->line('sales_maraketing_masters_yearly')/*'Yearly'*/:$this->lang->line('common_monthly')/*'Monthly'*/); ?> ] </td><!--Target-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo number_format($extra['head']['salesPersonTarget'],$extra['head']['salesPersonCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('sales_maraketing_masters_liability_account');?>   </td><!--liability Account-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['receivableDescription']; ?></td>
                        </tr>
                        <tr>
                            <td style="width:19%;"><?php echo $this->lang->line('sales_maraketing_masters_expense_account');?>  </td><!--Expense Account-->
                            <td style="width:10%;"><strong>:</strong></td>
                            <td style="width:70%;"><?php echo $extra['head']['expanseDescription']; ?></td>
                        </tr>
                    </table>    
                </td>
            </tr>
        </tbody>
    </table>
</div><hr>
<div>
    <table class="table table-bordered table-striped table-condensed table-row-select">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"> <?php echo $this->lang->line('sales_maraketing_masters_start_amount');?> <span class="currency"> (<?php echo $this->common_data['company_data']['company_default_currency'];?>)</span></th><!--Start Amount-->
                <th style="min-width: 15%"> <?php echo $this->lang->line('sales_maraketing_masters_end_amount');?>  <span class="currency"> (<?php echo $this->common_data['company_data']['company_default_currency'];?>)</span></th><!--End Amount-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_percentage');?> %</th><!--Percentage-->
            </tr>
        </thead>
        <tbody id="table_body">
        <!--No Records Found-->
           <?php
           $norecordfound=$this->lang->line('common_no_records_found');
                if (empty($extra['detail'])) {
                    echo '<tr class="danger"><td colspan="5" class="text-center"><b>'.$norecordfound.'</b></td></tr>';
                }else{
                    for ($i=0; $i < count($extra['detail']); $i++) { 
                        $x = ($i+1);
                        $fromTargetAmount = number_format($extra['detail'][$i]['fromTargetAmount'],$extra['head']['salesPersonCurrencyDecimalPlaces']);
                        $toTargetAmount = number_format($extra['detail'][$i]['toTargetAmount'],$extra['head']['salesPersonCurrencyDecimalPlaces']);

                        echo "<tr><td >{$x}</td><td style='text-align: right;'>{$fromTargetAmount}</td><td style='text-align: right;'>{$toTargetAmount}</td><td style='text-align: center;'>{$extra['detail'][$i]['percentage']}</td></tr>";
                    }
                }
           ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('#changeImg').click(function () {
        $('#img_file').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            img_uplode();
        }
    }

    function img_uplode(){
        var data = new FormData($('#img_uplode_form')[0]);
        $.ajax({
            url: "<?php echo site_url('Customer/img_uplode'); ?>",
            type: 'post',
            data: data,
            mimeType: "multipart/form-data",
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //load_table_registeredDoc();
            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });  
    }
</script>