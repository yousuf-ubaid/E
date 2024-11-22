<table id="" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header" style="position: sticky;">
                    <tr>
                        <th>Checklist Description</th>
                        <th>Yes </th>
                        <th>No </th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($checklist) {
                        
                            foreach ($checklist as $val) { ?>
                                    <tr>
                                        <td><?php echo $val['checklistDescription'] ?></td>
                                        <td><input type="radio" name="check_response_<?php echo $val['id'] ?>" value="1" <?php echo ($val['values'] == 1) ? 'checked': '' ?> onchange="record_checklist(<?php echo $val['id'] ?>,this)" /></td>
                                        <td><input type="radio" name="check_response_<?php echo $val['id'] ?>" value="0" <?php echo ($val['values'] == 0) ? 'checked': '' ?> onchange="record_checklist(<?php echo $val['id'] ?>,this)" /></td>
                                    </tr>
                                <?php
                                
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        
                    </tfoot>
                </table>


    <script>
        function record_checklist(id,ev){

            var value = $(ev).val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id': id,'value':value},
                url: "<?php echo site_url('MFQ_Job/update_checklist_response'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    myAlert(data[0],data[1]);

                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
       

        }

    </script>