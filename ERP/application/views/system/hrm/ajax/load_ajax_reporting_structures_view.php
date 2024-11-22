<style>
    .select2-container {
        min-width: 150px !important;
    }
    .select2-dropdown--below {
        min-width: 150px !important;
        /*z-index: 2000 !important;*/
    }

    /* Increase the size of the clear icon in Select2 */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        font-size: 18px;
        color: purple;
        margin-left: 10px;
    }

</style>


<div id="left" class="table-responsive col-sm-12">
    <!-- <?php //echo form_open('', 'role="form" id="reporting_structure_form"'); ?> -->
        <table class="table table-hover table-condensed">
            <tbody>
            <?php
                if(!empty($master)){
                        //echo '<pre>';print_r($master);exit;
                    $x = 1;
                    foreach ($master as $key => $val) {
                        // if($val['id'] == $x){

                            //for dropdown list
                            $infomations = array();
                            foreach($details as $info){
                                if($info['structureMasterID']== $val['id']){
                                    $infomations[''] = 'select description';
                                    if(!empty($info['detail_code'])){
                                        $infomations[trim($info['id'] ?? '')] = trim($info['detail_code'] ?? '') . ' - ' . trim($info['detail_description'] ?? '');
                                    }else{
                                        $infomations[trim($info['id'] ?? '')] = trim($info['detail_description'] ?? '');
                                    }
                                    
                                }else{
                                    $information = 'No Records Found';
                                }
                            }
                            
                            //for dropdown default value
                            foreach($description as $des){
                                if($des['reportingStructureID'] == $val['id']){
                                    $default = $des['reportingStructureDetailID'];
                                }
                            }
                            if(empty($default)){
                                $default= '';
                            }
                            //echo '<pre>';print_r($default);exit;

                            ?>
                            <tr>
                                <td style="text-align:left;min-width:20px;max-width:40px;font-size: 14px !important;color:#090080;"><?php echo $x; ?></td>
                                <td style="text-align:left;min-width:400px;max-width:700px;font-size: 14px !important;color:#090080;font-weight: 500;"><?php echo $val['description']; ?></td>
                                <?php if(!empty($infomations)){ ?>
                                <td style="text-align:right;min-width:200px;max-width:400px;font-size:10px !important;font-weight: 500;">
                                    <div class="form-group">
                                        <input type="hidden" class="form-control reportingStructureID" name="reportingStructureID[]" id="reportingStructureID_<?php echo $key; ?>"  value="<?php echo $val['id']; ?>">
                                        <input type="hidden" class="form-control description" name="description[]" id="reportingStructureName_<?php echo $key; ?>"  value="<?php echo $val['description']; ?>">
                                        <?php echo form_dropdown('structures[]', $infomations, $default, 'class="form-control select2 structures" id="structures_' . $val['id'] . '"  required onchange="description(this,'.$key.')"') ?> <!-- onchange="save_description(this,'.$key.') -->
                                    </div>
                                </td>
                                <!-- <td>
                                    <button type="button" class="btn" onclick="save_description()"><span class="glyphicon glyphicon-floppy-save" rel="tooltip" title="Save" style="color:#006600;"></span></button>
                                </td>  -->
                                <?php }else{ ?>
                                <td style="text-align:left;min-width:20px;max-width:40px;font-size: 12px !important;color:#FF6600;font-weight: 500;"><span class="fa fa-exclamation-circle" aria-hidden="true"></span>&nbsp;<?php echo $information; ?></td>
                                <?php }?>
                            </tr>
                            <?php  
                        //}
                        $x++; 
                    }
                } ?>
            </tbody>
        </table>
</div>

<div id="save" class="row col-sm-12" style="margin-top: 10px;">
    <button class="btn btn-primary pull-right rep_Strc_Save" type="button" onclick="save_description(<?php echo htmlspecialchars(json_encode($master)); ?>)"><?php echo $this->lang->line('common_save_change'); ?></button>
</div>

<script>
    var structures = [];

    $(document).ready(function (e) {
        
        $('.select2').select2({
            placeholder: 'Select Description',
            allowClear: true,
        });
     });

    function description(ths,id) {
        var reportingStructureDetailID = ths.value;
        var reportingStructureID = $('#reportingStructureID_' + id).val();
        var reportingStructureName = $('#reportingStructureName_' + id).val();

        // Create an object with the structure and its corresponding reportingStructureID
        var structureObject = {
            reportingStructureID: reportingStructureID,
            reportingStructureDetailID: reportingStructureDetailID,
            reportingStructureName: reportingStructureName
        };

        // Push the object into the structures array
        structures.push(structureObject);      
    }


    // function getstructureNameById(master, id) {
    //     for (var i = 0; i < master.length; i++) {
    //         if (master[i].id === id) {
    //             return master[i].description;
    //         }
    //     }
    //     return null; // Return null if ID is not found
    // }

    function save_description(master) {

        var isValid = true;
        //var errors = [];
        $('.structures').each(function() {
            if ($(this).val() === '') {
                // var explodedArray = $(this).attr('id').split('_');
                // var index = explodedArray[1];
                // var structureName = getstructureNameById(master, index);    //var structureName = master[parseInt(index)];
                        
                // if (structureName) {
                //     errors.push({"name": "message", "value": "Description for " + structureName + " is required"});
                //     //errors =  myAlert('e', 'Description for ' + structureName + ' is required');
                // }
                isValid = false;
            } 
        });

        if (!isValid) {
            myAlert('e', 'Please select a value for all dropdowns.', 3000);
            // $.each(errors, function(index, error) {
            //     myAlert('e', error.value, 5000);
            // });
            fetch_reporting_structure();
            return false;
        }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Employee/save_description'); ?>",
                data: {'structures':structures, 'empID':empID},
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);

                    if (data.error == 0) {
                        myAlert('s', data.message);
                        structures = []; //clear array
                        fetch_reporting_structure();

                    } else if (data.error == 1) {
                        myAlert('e', data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
    return false;
}

</script>