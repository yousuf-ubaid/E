<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

$arr = $category;

$new = array();
foreach ($arr as $a) {
    $new[$a['parentid']][] = $a;
}
$tree="";
if(isset($new[0])){
    $tree = createTree($new, $new[0]);
}

function createTree(&$list, $parent)
{
    $tree = array();
    foreach ($parent as $k => $l) {
        if (isset($list[$l['id']])) {
            $l['children'] = createTree($list, $list[$l['id']]);
        }
        $tree[] = $l;
    }
    return $tree;
}


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
    return ' onclick="add_Modal(' . $id . ',' . $level . ',\'' . $description . '\')" ';
}
function generateFunctionedit($id, $level, $description, $well_type_op, $well_no_op)
{
    return ' onclick="edit_Modal(' . $id . ',' . $level . ',\'' . $description . '\',\'' . $well_type_op .'\',\'' . $well_no_op .'\')" ';
}
function generateFunctionDelete($id, $level, $description)
{
    return ' onclick="delete_modal(' . $id . ',' . $level . ',\'' . $description . '\')" ';
}



/*echo '<pre>';
print_r($tree);
echo '</pre>';*/
?>
<style>
    .item-2 {
        display:    flex;
        flex-direction: row;
        justify-content: space-between;
    }   

    .deeper-sub{
        padding-left: 50px;
    }
</style>

<div class="row">
    <div id="left" class="col-sm-10 col-md-10">
        <?php
        if (!empty($tree)) {
            foreach ($tree as $val_1) {
                ?>
                <ul id="menu-group-<?php echo $val_1['id'] ?>" class="nav menu">
                    <li class="item-1 deeper parent active">
                        <a class="" href="#">
                        <span data-toggle="collapse"
                              data-parent="#menu-group-<?php echo $val_1['id'] ?>"
                              href="#sub-item-<?php echo $val_1['id'] ?>" class="sign">
                            <?php
                            echo isset($val_1['children']) ? genIco($val_1['children']) : genIco();

                            /*echo '<pre>';
                            print_r($val_1['children']);
                            echo '</pre>';*/
                            //exit;
                            ?>
                            <span class="lbl"><?php echo $val_1['name'] ?></span>

                            <button
                                class="pull-right btn btn-xs btn-link" <?php echo generateFunction($val_1['id'], $val_1['levelNo'], $val_1['name']) ?>>
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                            </button>
                            <button
                                class="pull-right btn btn-xs btn-link" <?php echo generateFunctionedit($val_1['id'], $val_1['levelNo'], $val_1['name'], $val_1['well_type_op'], $val_1['well_no_op']) ?>>
                                <i class="fa fa-pencil"></i> <?php echo $this->lang->line('common_edit') ?><!--Edit-->
                            </button>

                        </a>
                        <?php
                        if (isset($val_1['children']) && !empty($val_1['children'])) {
                            ?>
                            <ul class="children nav-child small collapse list-unstyled"
                                id="sub-item-<?php echo $val_1['id'] ?>">
                                <?php
                                foreach ($val_1['children'] as $val_2) {
                                    ?>
                                    <li class="item-2 deeper-sub deeper parent active">
                                            <div>
                                                <span data-toggle="collapse"
                                                    data-parent="#menu-group-<?php echo $val_2['id'] ?>"
                                                    href="#sub-item-<?php echo $val_2['id'] ?>"
                                                    class="sign">
                                                    <?php echo isset($val_2['children']) ? genIco($val_2['children']) : genIco(); ?>
                                                </span>
                                                <span class="lbl"><?php echo $val_2['name'] .' / '. $val_2['well_type_op'] .' / '. $val_2['well_no_op'] ?></span>
                                            </div>
                                            <div>
                                               
                                              
                                                 <button
                                                    class="pull-right btn btn-xs btn-link" <?php echo generateFunctionDelete($val_2['id'], $val_2['levelNo'], $val_2['name']) ?>>
                                                    <i class="fa fa-trash text-danger"></i> <?php echo $this->lang->line('common_delete') ?>
                                                </button>
                                                <button
                                                    class="pull-right btn btn-xs btn-link milee" <?php echo generateFunctionedit($val_2['id'], $val_2['levelNo'], $val_2['name'], $val_2['well_type_op'], $val_2['well_no_op']) ?>>
                                                    <i class="fa fa-pencil"></i> <?php echo $this->lang->line('common_edit') ?>
                                                </button>
                                            </div>
                                        
                                        
                                        <!-- </a> -->
                                        <?php
                                        if (isset($val_2['children']) && !empty($val_2['children'])) {
                                            ?>
                                            <ul class="children nav-child small collapse list-unstyled"
                                                id="sub-item-<?php echo $val_2['id'] ?>">
                                                <?php
                                                foreach ($val_2['children'] as $val_3) {
                                                   // $name = htmlspecialchars($val_3);
                                                    ?>
                                                    <li class="item-4 current active">
                                                        <a class="" href="# ">
                                                            <span class="sign">
                                                                <?php echo isset($val_3['children']) ? genIco($val_3['children']) : genIco(); ?>
                                                            </span>
                                                            <span class="lbl">
                                                                <?php echo $val_3['name'] ?>
                                                            </span>

                                                            <button
                                                                class="pull-right btn btn-xs btn-link" <?php echo generateFunctionedit($val_3['id'], $val_3['levelNo'],htmlspecialchars($val_3['name']), $val_3['well_type_op'], $val_3['well_no_op'])?>>
                                                                <i class="fa fa-pencil"></i> <?php echo $this->lang->line('common_edit') ?><!--Edit-->
                                                            </button>
                                                        </a>
                                                    </li>
                                                    <?php
                                                } ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </li>
                </ul>
                <?php
            }
        }
        ?>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $(".sign").click();
    })

    function add_Modal(id,level,name){

        if(level == 1){
            $('#field_well_title').html('Add Well');
            $('#filed_well_type').val(2);
            $('#filed_well_id').val(id);
            $('#filed_well_name').val('');
            $('#action').val('add');
        }
        
        $('#add_feild_well').modal('show');

    }

    function edit_Modal(id,level,name,well_type,well_no){
        if(level == 1){
            $('#field_well_title').html('Edit Field');
            $('#filed_well_id').val(id);
            $('#filed_well_type').val(level);
            $('#action').val('edit');
            $('#filed_well_name').val(name);
            $('#well-type').hide();
            $('#well-no').hide();
        }else if(level == 2){
            $('#field_well_title').html('Edit Well');
            $('#filed_well_id').val(id);
            $('#filed_well_type').val(level);
            $('#action').val('edit');
            $('#filed_well_name').val(name);
            $('#well_type_op').val(well_type);
            $('#well_no_op').val(well_no);
            $('#well-type').show();
            $('#well-no').show();
        }
       
        $('#add_feild_well').modal('show');

    }

   


</script>