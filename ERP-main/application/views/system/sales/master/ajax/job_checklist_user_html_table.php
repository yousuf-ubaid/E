<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('quotation_contract');

//$user =make_contract_checklist_user_dropDown();

$user = get_crew_list_for_checlist_contract($job_master['contract_po_id']);


?>

<hr>

<div class="table-responsive">
    <table class="table table-bordered table-striped checklist_details_tbl" >
        <thead class='thead'>

        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Code</th><!--Code-->
            <th style="min-width: 10%" class="text-left theadtr">Name</th><!--Description-->

            <th style="min-width: 10%" class="text-left theadtr">Confirm Users</th>
            <th style="min-width: 20%" class="text-left theadtr">Edit Users</th>
            <!-- <th style="min-width: 20%" class="text-left theadtr">User </th> -->
        </tr>
        </thead>
        <tbody>
        <?php
       
        if (!empty($details)) {
            foreach ($details as $key=> $val) { 

                if($val['confirmUsers']){
                    $users_arr = explode(",",$val['confirmUsers']);
                }else{
                    $users_arr =[];
                }

                if($val['editUsers']){
                    $users_arr_edit = explode(",",$val['editUsers']);
                }else{
                    $users_arr_edit =[];
                }
                
            ?>
                
                <tr>
                    <td class="text-center"><?php echo $key+1; ?></td>
                    
                    <td class="text-center"><?php echo $val['documentCode']; ?></td>
                    <td><?php echo $val['documentName'] ?></td>
                    <td><?php echo form_dropdown('confirmUsers[]', $user, $users_arr, 'class="form-control select_user" onchange="selectChecklistUserUpdate(this,' . $val['jobChecklistUserAutoID'] . ',1)" id="confirmUsers" multiple="multiple"'); ?></td>
                    <td><?php echo form_dropdown('editUsers[]', $user, $users_arr_edit, 'class="form-control select_user_edit" onchange="selectChecklistUserUpdate(this,' . $val['jobChecklistUserAutoID'] . ',2)" id="editUsers" multiple="multiple"'); ?></td>
                   
                </tr>
                <?php
              
            }
        } else {
            
                echo '<tr class="danger"><td colspan="9" class="text-center">No records found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>
        
    </table>
</div><br>

 <script>
   
</script>