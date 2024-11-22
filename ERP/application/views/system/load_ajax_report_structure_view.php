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
        $tree = createTree($arr, $parent, $des);

        function createTree($arr, $parent, $des) {
            $tree = '<ul id="myUL">';
            foreach ($arr as $item) {
                if ($item['parentid'] == $parent) {
                    
                    if($item['captureCostYN'] == 1){
                        $Cost_checked = 'checked';
                    }else{
                        $Cost_checked = '';
                    }
                    if($item['captureHRYN'] == 1){
                        $hr_checked = 'checked';
                    }else{
                        $hr_checked = '';
                    }               
                    
                    $infomations = array();
                    foreach($des as $info){
                        if($info['structureMasterID'] == $item['id'] && $info['sortOrder'] == $item['sortOrder']){
                           // $infomations[] = $info['detail_description'];
                           $infomations[] = array(
                            'id' => $info['id'],
                            'detail_description'=> $info['detail_description'],
                            'name' => $item['name'],
                            'detail_code' => $info['detail_code'],
                            'combined_description' => $info['combined_description']
                           ); 
                        }
                    }
                    //echo '<pre>'; print_r($infomations);exit;

                    $tree   .=  '<li class="myUL_li" >
                                        <div class="row cat" id="myUL_li_div_id">
                                                <span id="li_name" class="test-cat">'.$item['name'].'</span>
                                                
                                                <button class="pull-right btn btn-xs" '.generateFunction($item['id'], $item['levelNo'], $item['name']).'><span style="color:black" class="glyphicon glyphicon-plus" title="Add Sub Structure" rel="tooltip"></span></button>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <button class="pull-right btn btn-xs" '.generateFunctionedit($item['id'], $item['levelNo'], $item['name'],$item['sortOrder'], $item['captureCostYN'], $item['captureHRYN'], $item['system_type']).'><span style="color:black" class="glyphicon glyphicon-edit" title="Edit Structure" rel="tooltip"></span></button>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <button class="pull-right btn btn-xs" '.generateFunctionconfig($item['id'], $item['name'], $item['sortOrder']).'><span style="color:black" class="glyphicon glyphicon-cog" title="Descriptions" rel="tooltip"></span></button>
                                               
                                                <span class="form-check form-switch pull-right">
                                                    <label class="form-check-label" for="mySwitch" style="font-weight: 400;">Capture Cost</label>
                                                    <input class="form-check-input" type="checkbox" id="captureCost" name="captureCost" value="1" '.$Cost_checked.'>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span class="form-check form-switch pull-right">
                                                    <label class="form-check-label" for="mySwitch" style="font-weight: 400;">Capture HR</label>
                                                    <input class="form-check-input" type="checkbox" id="captureHR" name="captureHR" value="1" '.$hr_checked.'>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                        </div>


                                        <ul class="neted">
                                            <li id="info">
                                                    <table class="table table-hover"> ';
                                                        $x = 1;
                                                        foreach ($infomations as $val) {
                                                            $tree .= '<tr>
                                                                        <td id="td_id" style="text-align:left;font-size: 10px !important;color:#0000FF;">' . $x . '</td>';
                                                            if(!empty($val['combined_description'])){
                                                                $tree .=  '<td id="td_name" style="text-align:left;font-size: 12px !important;color:#0000FF;font-weight: 400;">' . $val['combined_description'] . '</td>';
                                                            }else{
                                                                $tree .=  '<td id="td_name" style="text-align:left;font-size: 12px !important;color:#0000FF;font-weight: 400;">' . $val['detail_description'] . '</td>';
                                                            }
                                                            
                                                            $tree .=  '<td id="td_width2"></td>
                                                                        <td id="td_icon"class="pull-right" style="text-align:right;font-size: 10px !important;font-weight: 300;">
                                                                            <a onclick="generate_Function_description_edit(' . $val['id'] . ', \'' . $val['detail_description'] . '\', \'' . $val['name'] . '\', \'' . $val['detail_code'] . '\')" ><span style="color:#0000FF" class="glyphicon glyphicon-edit" title="Edit description" rel="tooltip"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            <a onclick="generate_Function_description_delete(' . $val['id'] . ')" ><span style="color:#0000FF" class="glyphicon glyphicon-trash" title="Delete description" rel="tooltip"></span></a>
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

    })


    var toggler = document.getElementsByClassName("test-cat");
    for (var i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function(event) {
            this.parentElement.parentElement.querySelector(".neted").classList.toggle("active");
            this.classList.toggle("caret-down");
        });
    }
</script>
