<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="table-responsive">
    <table class="table">
        <thead class='thead'>
        <tr>

           
            <th style="width: 5%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>#
            </th>
            <th style="width: 5%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class=' tablethcol2'>File Name
            </th>

            <!--Product-->
            <th style="min-width: 20%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Description
            </th><!--UOM-->
            <th style="width: 10%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Type
            </th><!--Delivery Date-->
            <th style="min-width: 5%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Action
            </th><!--Narration-->
            
        </tr>
        </thead>
        <tbody id="">
        <?php
        if (!empty($detail)) {
        foreach ($detail as $key=>$val) {
        ?>
        <tr>
            
            <td class=" tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo  $key+1 ?>
                    <label></td>
            </td>
            <td class="tablethcol2" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['myFileName']?>
                    <label></td>
            <td class="tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['attachmentDescription']?><label>
            </td>
            <td class="tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['fileType']?><label>
            </td>
            <td class="tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;">
                        <?php if($is_submit_rfq==0){ ?>
                        <a class="" onclick="srm_vendor_portal_document_delete('<?php echo $val['attachmentID']?>','<?=$csrf['name'];?>','<?=$csrf['hash'] ; ?>','<?php echo $val['companyID']?>')"><span
                                    class="glyphicon glyphicon-trash color" aria-hidden="true"></span></a> &nbsp; | &nbsp; &nbsp;
                            <?php } ?>
                                    <a target="_blank" href="<?php echo $val['url'] ?>" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;
                        <label>
            </td>

        </tr>
        <?php
   
        }
        } else {
            $norecfound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
        } ?><!--No Records Found-->
        </tbody>
       
    </table>
</div>

<script>


    var quatationdetailid = '<?php echo $quatationId?>'
    var companyID = '<?php echo $companyID?>'
    var quatationId = '<?php echo $quatationId?>'
   
    $(document).ready(function () {

    });
</script>