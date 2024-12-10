<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('quotation_contract');

$user =make_contract_checklist_user_dropDown();


?>

<hr>

<div class="table-responsive">
    <table class="table table-bordered table-striped checklist_details_tbl" >
        <thead class='thead'>

        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Code</th><!--Code-->
            <th style="min-width: 10%" class="text-left theadtr">Name</th><!--Description-->

            <th style="min-width: 10%" class="text-left theadtr">Action</th>
            <th style="min-width: 20%" class="text-left theadtr">Calling</th>
            <!-- <th style="min-width: 20%" class="text-left theadtr">User </th> -->
        </tr>
        </thead>
        <tbody>
        <?php
       
        if (!empty($details)) {
            foreach ($details as $key=> $val) { 

                if($val['checklistAccessUser']){
                    $users_arr = explode(",",$val['checklistAccessUser']);
                }else{
                    $users_arr =[];
                }
                $calling =make_contract_calling_dropDown($val['callingCode'],$val['contractChecklistAutoID']);
            ?>
                
                <tr>
                    <td class="text-center"><?php echo $key+1; ?></td>
                    
                    <td class="text-center"><?php echo $val['documentID']; ?></td>
                    <td><?php echo $val['checklistDescription'] ?></td>

                    <td><a onclick="delete_contract_checklist(<?php echo $val['contractChecklistAutoID'] ?> );"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;<a onclick="open_contract_checklist(<?php echo $val['checklistID'] ?>)"><span title="view" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a></td>
                    <td><?php echo $calling ?></td>
                    <!-- <td><?php echo form_dropdown('select_user[]', $user, $users_arr, 'class="form-control select_user" onchange="selectChecklistUserUpdate(this,' . $val['contractChecklistAutoID'] . ')" id="customerCode" multiple="multiple"'); ?></td> -->
                   
                </tr>
                <?php
              
            }
        } else {
            $norecordsfound= $this->lang->line('common_no_records_found');;
                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>
        
    </table>
</div><br>

 <script>
   
</script>