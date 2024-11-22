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

function generateFunctionDelete($id, $level, $description)
{
    return ' onclick="delete_modal(' . $id . ',' . $level . ',\'' . $description . '\')" ';
}

function generateFunctionedit($id, $level, $description)
{
    return ' onclick="edit_Modal(' . $id . ',' . $level . ',\'' . $description . '\')" ';
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


                            ?>
                            <span class="lbl"><?php echo $val_1['name'] ?></span>

                            
                            <button
                                class="pull-right btn btn-xs btn-link" <?php echo generateFunctionDelete($val_1['id'], $val_1['levelNo'], $val_1['name']) ?>>
                                <i class="fa fa-trash text-danger"></i> <?php echo $this->lang->line('common_delete') ?>
                            </button>
                            <button
                                class="pull-right btn btn-xs btn-link" <?php echo generateFunctionedit($val_1['id'], $val_1['levelNo'], $val_1['name']) ?>>
                                <i class="fa fa-pencil"></i> <?php echo $this->lang->line('common_edit') ?><!--Edit-->
                            </button>
                        </a>
                        <?php
                       
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

    function edit_Modal(id,level,name){

        if(level == 1){
            $('#field_well_title').html('Edit Rig / Hoist');
            $('#filed_well_id').val(id);
            $('#filed_well_type').val(level);
            $('#filed_well_type').val(3);
            $('#action').val('edit');
            $('#filed_well_name').val(name);
        }

        $('#add_feild_well').modal('show');

    }


</script>