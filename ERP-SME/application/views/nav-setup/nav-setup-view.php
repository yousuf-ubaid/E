<?php
$yesNo_arr = [1=> 'Yes', 0=> 'No'];
$doc_drop = documentCode_drop(['placeHolder'=> 'Select a document']);
?>
<style>  
  .treeview-sub{
      padding-left: 25px !important;
  }

  #ajax-nav-view{
    margin-left: 50px;
    height: 320px;
    overflow-y: scroll;
  }

  .module-icon{
      font-size: 13px;
      color: black !important;
  }
</style>

<div class="row">    
    <?php
    $n = 0;
    $new_row_start = '<div class="col-md-12">';
    $new_row_end = '</div>';    
    
    foreach($nav_modules_arr as $module){
        $modID = $module['moduleID'];

        $is_new_row = ($n % 4 == 0)? 'Y': 'N';
        
        if($is_new_row == 'Y'){
            if($n != 0){
                echo $new_row_end;
            }

            echo $new_row_start;
        }

        $sub_nav_view = '';
        if( array_key_exists($modID, $sub_nav)){
            foreach($sub_nav[$modID] as $sub_nav_det){
                $sub_nav_view .= '<li class="list-group-item"> <i class="'.$sub_nav_det['pageIcon'].'"></i>';
                $sub_nav_view .= ' &nbsp;  &nbsp; '.$sub_nav_det['description'].'</li>';
            }
        }

        echo '<div class="col-md-3">
                <div class="panel-group" role="tablist">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-heading-nav" role="tab" id="moduleGroup_'.$modID.'">
                            <h4 class="panel-title">
                                <i class="'.$module['pageIcon'].' module-icon"></i> &nbsp; '.$module['description'].'
                                                                
                                <strong class="btn-box-tool pull-right"> 
                                    <i class="fa fa-eye tool-box-icon" title="View" onclick="view_module_nav('.$modID.')"></i> &nbsp; 
                                    <a class="" title="Toggle" role="button" data-toggle="collapse" href="#collapseListMod_'.$modID.'" 
                                        aria-expanded="true" aria-controls="collapseListMod_'.$modID.'"
                                        onclick="toggleSingle(\'nav_toggle_\', '.$modID.')">
                                        <i class="fa fa-plus tool-box-icon nav-common-collapse" id="nav_toggle_'.$modID.'"></i>
                                    </a>                          
                                </strong>  
                            </h4>
                        </div>
                        <div id="collapseListMod_'.$modID.'" class="panel-collapse collapse nav-collapse-body" role="tabpanel" 
                            aria-labelledby="moduleGroup_'.$modID.'" aria-expanded="true" style="">
                            <ul class="list-group">
                                '.$sub_nav_view.'
                            </ul>                    
                        </div>
                    </div>
                </div>
            </div>';

        $n++;
    }
 
    ?>    
    </div> <!-- finish start of col-sm-12 -->    
</div>

<div class="modal fade" id="add_nav_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="title_nav_modal">Add New Navigation</h3>
            </div>
            <?=form_open('', 'role="form" id="add_nav_form" autocomplete="off"'); ?>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="nav_edit_id" name="nav_edit_id" value="0">
                        
                        <div class="form-group col-sm-4">
                            <label>Description </label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>

                        <div class="form-group col-sm-4">
                            <label>Module</label>                           
                            <select name="pr_module" class="form-control select2 disable-input" onchange="load_sub_menus()" id="pr_module">
                                <option value="">None</option>
                                <?php
                                foreach($nav_modules as $key=>$row){
                                    echo '<option value="'.$key.'">'.$row.'</option>';
                                }
                                ?>                                
                            </select>
                        </div>

                        <div class="form-group col-sm-4">
                            <label>Sub Module</label>
                            <?=form_dropdown('sub_module', [], null, 'class="form-control select2 disable-input" onchange="sub_menus_data()" id="sub_module"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Sort Order </label>
                            <input type="text" class="form-control number" id="sortOrder" name="sortOrder" value="<?=$module_sort_order?>">
                        </div>

                        <div class="form-group col-sm-4">
                            <label>Page Icon </label>
                            <?=form_dropdown('pageIcons', [], '', 'class="form-control common-select" id="pageIcons"')?>                            
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Is Basic</label>
                            <?=form_dropdown('isBasic', $yesNo_arr, 0, 'class="form-control common-select select2 " id="isBasic"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Is Group</label>
                            <?=form_dropdown('isGroup', $yesNo_arr, 0, 'class="form-control common-select select2 " id="isGroup"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Is External</label>
                            <?=form_dropdown('isExternal', $yesNo_arr, 0, 'class="form-control common-select select2 " id="isExternal"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Is Report</label>
                            <?=form_dropdown('isReport', $yesNo_arr, 0, 'class="form-control common-select select2 " 
                                id="isReport" onchange="isReport_change()"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Report ID</label>
                            <input type="text" class="form-control" id="reportID" name="reportID" value="" maxlength="7">
                        </div>

                        <div class="form-group col-sm-8">
                            <label>Page Url </label>
                            <input type="text" class="form-control" id="page_url" name="page_url" value="#">
                        </div>

                        <div class="form-group col-sm-5">
                            <label>Create Page Link</label>
                            <input type="text" class="form-control" id="createPage" name="createPage" value="">                            
                        </div>

                        <!-- <div class="form-group col-sm-5">
                            <label>Document Code</label>
                            <?=form_dropdown('documentCode', $doc_drop, [], 'class="form-control common-select select2" id="documentCode"')?>
                        </div>

                        <div class="form-group col-sm-2">
                            <label>Template Key</label>
                            <input type="text" class="form-control" id="templateKey" name="templateKey" value="">
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm nav-cus-btn pull-left" id="manage-btn"
                        onclick="load_manage_view()">Manage
                    </button>         

                    <button type="button" class="btn btn-primary btn-sm" onclick="save_navigation()">Save</button>
                    <button type="button" class="btn btn-danger btn-sm nav-cus-btn" onclick="delete_navigation()">Delete</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" onclick="close_nav_modal()">Close</button>
                </div>
            <?=form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="nav-view-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="view-modal-title"></h3>
            </div>        
            <div class="modal-body" id="ajax-nav-view">                
            </div>
            <div class="modal-footer">                    
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" >
                    Close
                </button>
            </div>        
        </div>
    </div>
</div>

<div class="modal fade" id="temp_management_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Navigation Template Management</h3>
            </div>        
            <div class="modal-body" id="temp_management_content"></div>
            <div class="modal-footer">                
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" onclick="close_nav_modal()">Close</button>
            </div>            
        </div>
    </div>
</div>