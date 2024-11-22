<div id="left" class="col-sm-12 col-md-12" style="width: auto;">
        <?php 
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $arr = $report;
        $des = $descriptions;


        function genIco($array = array())
        {
            if (isset($array) && !empty($array)) {
                return '<i style="font-size:16px;" class="fa fa-caret-down ico-color"></i></span>';
            } else {
                return '<i class="fa fa-arrow-right" style="color:#beab3c"></i></span>';
            }
        }

        function generateFunction($id, $level, $description)
        {
            return ' onclick="add_subCategoryModal(' . $id . ',' . $level . ',\'' . $description . '\')" ';
        }
        function generateFunctionedit($id, $level, $description, $sortOrder, $captureCostYN, $captureHRYN, $system_type)
        {
            // Convert boolean values to JavaScript strings
            $captureCostYN = $captureCostYN ? 'true' : 'false';
            $captureHRYN = $captureHRYN ? 'true' : 'false';

            // Wrap string parameters in quotes to ensure proper JavaScript syntax
            $description = addslashes($description);
            $system_type = addslashes($system_type);
            
            return 'onclick="edit_subCategoryModal(' . $id . ', ' . $level . ', \'' . $description . '\', ' . $sortOrder . ', ' . $captureCostYN . ', ' . $captureHRYN . ', \'' . $system_type . '\')"';
        }
        function generateFunctionconfig($masterid, $name, $sortOrder)
        {
            return ' onclick="add_description(' . $masterid . ',\'' . $name . '\',' . $sortOrder .')"';
        }
        function generate_Function_description_delete($description_id)
        {
            return ' onclick="description_delete(' . $description_id . ')"';
        }
        function generate_Function_description_edit($description_id, $detail_description, $name, $detail_code)
        {
            return ' onclick="description_edit(' . $description_id . ',\'' . $detail_description . '\',\'' . $name . '\', \'' . $detail_code . '\')"';
        }

        // Build tree
        $parent = 0;
        $tree = createTree($arr, $parent, $des, $config_details);

        function createTree($arr, $parent, $des, $config_details = null) {
            $tree = '<ul id="myUL">';
            foreach ($arr as $item) {
                if ($item['parentid'] == $parent) {             
                    
                    /**create array for details in srp_erp_reporting_structure_details*/
                    $infomations = array();
                    foreach($des as $info){
                        if($info['structureMasterID'] == $item['id'] && $info['sortOrder'] == $item['sortOrder']){
                           // $infomations[] = $info['detail_description'];
                           $infomations[] = array(
                            'id' => $info['id'],
                            'detail_description'=> $info['detail_description'],
                            'name' => $item['name'],
                            'detail_code' => $info['detail_code'],
                            'combined_description' => $info['combined_description'],
                            'structureMasterID' => $info['structureMasterID']
                           ); 
                        }
                    }
                    
                    /**create array for checked details in srp_erp_activity_code_sub*/
                    $chk = array();
                    if($config_details){
                    foreach ($infomations as $val) {
                        foreach($config_details as $conf){
                            if($conf['rpt_struc_master_id'] == $item['id'] && $conf['rpt_struc_detail_id'] == $val['id']){
                                $chk[] = array(
                                    'chk' => 'checked',
                                    'detail_id' => $conf['rpt_struc_detail_id']
                                );
                            }
                        }
                    }
                    }

                    $tree   .=  '<li class="myUL_li" >
                                        <div class="row cat" id="myUL_li_div_id">
                                                <span id="li_name" class="test-cat">'.$item['name'].'</span>
                                        </div>

                                        <ul class="neted">
                                            <li id="info">
                                                    <table class="table table-hover"> ';
                                                        $x = 1;
                                                        foreach ($infomations as $val) {
                                                            
                                                            $tree .= '<tr>
                                                                        <td id="td_id" style="text-align:left;font-size: 12px !important;color:#0000FF;">' . $x . '</td>';
                                                            if(!empty($val['combined_description'])){
                                                                $tree .=  '<td id="td_name" style="text-align:left;font-size: 14px !important;color:#0000FF;font-weight: 400;">' . $val['combined_description'] . '</td>';
                                                            }else{
                                                                $tree .=  '<td id="td_name" style="text-align:left;font-size: 14px !important;color:#0000FF;font-weight: 400;">' . $val['detail_description'] . '</td>';
                                                            }

                                                            $tree .=  ' <td id="td_width2_config"></td>
                                                                        <td id="td_icon"class="pull-right" style="text-align:right;font-size: 14px !important;font-weight: 300;">
                                                                            <span class="form-check form-switch">';
                                                                                if($chk){
                                                                                foreach($chk as $ch){
                                                                                    if($ch['detail_id'] == $val['id']){
                                                                                        $tree .=' <input class="myCheckbox configCheckbox" type="checkbox" id="isActivityCodeBased_'.  $val['id'] .'" name="isActivityCodeBased_'.  $val['id'] .'" data-text="'.  $item['id'] .'" value="1" ' . $ch['chk'] . '> ';
                                                                                    }else{
                                                                                        $tree .=' <input class="myCheckbox configCheckbox" type="checkbox" id="isActivityCodeBased_'.  $val['id'] .'" name="isActivityCodeBased_'.  $val['id'] .'" data-text="'.  $item['id'] .'" value="1"> ';
                                                                                    }
                                                                                }
                                                                                }else{
                                                                                    $tree .=' <input class="myCheckbox configCheckbox" type="checkbox" id="isActivityCodeBased_'.  $val['id'] .'" name="isActivityCodeBased_'.  $val['id'] .'" data-text="'.  $item['id'] .'" value="1"> ';
                                                                                }
                                                            $tree .='       </span>
                                                                        </td>
                                                                    </tr>'; 
                                                        $x++;  
                                                        }
                                            
                                            $tree .= '</table></li>';

                                            // Check if the current item has children
                                            $children = array_filter($arr, function($child) use ($item) {
                                                return $child['parentid'] == $item['id'];
                                            });
                                            if (!empty($children)) {
                                                // Recursively build the tree for children
                                                $tree .= createTree($arr, $item['id'], $des);
                                            }

                    $tree   .=          '</ul>
                                </li>';
                }
            }

            $tree .= '</ul><hr>';

            return $tree;
        }

        echo $tree; 
        ?>
</div>
<div class="row form-group col-sm-12 text-right">
    <button type="button" class="btn btn-default btn-sm " data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?></button>
    <!-- &nbsp;&nbsp;
    <button type="button" onclick="save_rep_config()" class="btn btn-primary btn-sm">Save</button> -->
</div>

<script>
    $(document).ready(function (e) {
        $(".sign").click();

        // Get all elements with the class "test-cat"
        var toggler = document.getElementsByClassName("test-cat");
        // Iterate over all elements with the class "test-cat"
        for (var i = 0; i < toggler.length; i++) {
            // Toggle the visibility of nested elements and change appearance
            toggler[i].parentElement.parentElement.querySelector(".neted").classList.toggle("active");
            toggler[i].classList.toggle("caret-down");
        }


        $('.configCheckbox').change(function() {
            var checkbox_ID = $(this).attr('id');
            var detail_id = checkbox_ID.split('_')[1];
            var master_id = $(this).attr('data-text');
            var isCheck = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'activityCodeID': activityCode_AutoID, 'detail_id': detail_id, 'master_id':master_id, 'isCheck': isCheck},
                    url: "<?php echo site_url('Report/save_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if(data[0] == 's'){
                                data[2].forEach(function(item) {
                                    $('.configCheckbox').each(function() {
                                            if($(this).attr('data-text') == item.rpt_struc_master_id){
                                                if($(this).attr('id').split('_')[1] == item.rpt_struc_detail_id)
                                                {
                                                    $(this).prop('checked', true);
                                                }
                                                else{
                                                    $(this).prop('checked', false);
                                                }
                                            }
                                    });
                                }); 
                        }  
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        });

    })


    var toggler = document.getElementsByClassName("test-cat");
    for (var i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function(event) {
            this.parentElement.parentElement.querySelector(".neted").classList.toggle("active");
            this.classList.toggle("caret-down");
        });
    }
 
</script>
