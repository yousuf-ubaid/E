<table id="mfq_crew" class="table table-condensed">
    <thead>
        <tr>
            <th style="min-width: 60%">Checklist<!--Stage--></th>
            <th style="min-width: 40%">Action<!--Stage Progress--></th>
        </tr>
    </thead>

    <tbody>
        <?php  if(count($data) > 0){ foreach($data as $val){

            $status_yes = '';
            $status_no = '';
            if($val['value'] == 1){
                $status_yes = 'checked="checked"';
            }else{
                $status_no  = 'checked="checked"';
            }

            echo '<tr>';
            echo '<td>'.$val['checklistName'].'</td>';
            echo '<td><input type="radio" onclick="change_job_checklist_val('.$val['id'].',this)" value="1" name="itemCheck_'.$val['id'].'" '.$status_yes.'> Yes &nbsp &nbsp &nbsp &nbsp <input name="itemCheck_'.$val['id'].'" type="radio" onclick="change_job_checklist_val('.$val['id'].')" value="0" '.$status_no.'> No </td>';
            echo '</tr>';
            } } else { echo '<tr><td colspan="3">No Checklist Added</td></tr>'; } ?>
    </tbody>

</table>

<script>
    function change_job_checklist_val(id,ev){

        var val = $(ev).val();

        $.ajax({
                type: 'POST',
                url: "<?php echo site_url('MFQ_Job/change_job_checklist_value'); ?>",
                data: {'id': id, 'val': val},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);

                },

                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

    }

</script>