
    <?php
    $checked = '';
    ?>
    <ul id="menu-group-1" class="nav menu">
        <?php
        if ($menu) {
            $i = 0;
            foreach ($menu as $level1) {

                if ($level1['levelNo'] == 0) {
                    $checked = '';
                    /*if ($level1['navID'] != 0) {
                        $checked = 'checked';
                    }*/
                    $i++;
                    ?>
                    <li class="item-8 deeper parent">
                        <a class="" href="#">
                            <?php
                    $exist2 = whatever($menu, 'levelNo', 1, 'masterID', $level1['navigationMenuID']);
                    if ($exist2) {
                        ?>
                        <span data-toggle="collapse" data-parent="#menu-group-<?php echo $i; ?>" href="#sub-item-<?php echo $i; ?>" class="sign"><i class="fa fa-plus"></i></span>
                        <span class="lbl" for="tall<?php echo $i; ?>"> &nbsp;<?php echo $level1['description'] ?></span>
                        <span  class="btn btn-default btn-sm pull-right" onclick="open_add_singl_nav_model('2','1',<?php echo $level1['navigationMenuID'];?>)"><i class="fa fa-plus"></i> Add</span>
                        <?php
                    }else{
                        ?>
                        <span class="sign2"><i class="fa fa-caret-right" style=""></i></span>
                        <span class="lbl" for="tall<?php echo $i; ?>"> &nbsp;<?php echo $level1['description'] ?></span>
                        <?php
                        if ($level1['isSubExist']==1) {
                        ?>
                        <span class="btn btn-default btn-sm pull-right"
                              onclick="open_add_singl_nav_model('2','1',<?php echo $level1['navigationMenuID']; ?>)"><i
                                class="fa fa-plus"></i> Add</span>
                        <?php
                        }
                    }
                        ?>
                        </a>

                        <?php
                        $exist2 = whatever($menu, 'levelNo', 1, 'masterID', $level1['navigationMenuID']);
                        if ($exist2) {
                            ?>
                            <ul class="children nav-child unstyled small collapse" id="sub-item-<?php echo $i; ?>">
                                <?php
                                if ($menu) {
                                    $x = 0;
                                    foreach ($menu as $level2) {
                                        $x++;
                                        if ($level2['levelNo'] == 1 && $level2['masterID'] == $level1['navigationMenuID']) {
                                            $checked = '';
                                            ?>
                                            <li class="item-9 deeper parent">
                                                <a class="" href="#">
                                                    <?php
                                            $exist3 = whatever($menu, 'levelNo', 2, 'masterID', $level2['navigationMenuID']);
                                            if ($exist3) {
                                                ?>
                                                <span data-toggle="collapse" data-parent="#menu-group-<?php echo $x; ?>"
                                                      href="#sub-item-<?php echo $x; ?>" class="sign"><i class="fa fa-plus"></i></span>
                                                <span class="lbl" href="#" for="tall<?php echo $i; ?>-<?php echo $x; ?>">
                                                        &nbsp;<?php echo $level2['description'] ?></span>
                                                <span  class="btn btn-default btn-xs pull-right" onclick="open_add_singl_nav_model('2','2',<?php echo $level1['navigationMenuID'];?>,<?php echo $level2['navigationMenuID'];?>)"><i class="fa fa-plus"></i> Add</span>
                                                <?php
                                            }else{
                                                ?>
                                                <span class="sign2"><i class="fa fa-caret-right" style=""></i></span>
                                                <span class="lbl" href="#" for="tall<?php echo $i; ?>-<?php echo $x; ?>">
                                                        &nbsp;<?php echo $level2['description'] ?></span>
                                                <?php
                                                if ($level2['isSubExist']==1) {
                                                    ?>
                                                    <span class="btn btn-default btn-sm pull-right"
                                                          onclick="open_add_singl_nav_model('2','1',<?php echo $level1['navigationMenuID']; ?>)"><i
                                                            class="fa fa-plus"></i> Add</span>
                                                    <?php
                                                }
                                            }
                                                ?>

                                                </a>



                                                <?php

                                                $exist3 = whatever($menu, 'levelNo', 2, 'masterID', $level2['navigationMenuID']);
                                                if ($exist3) {
                                                    ?>
                                                    <ul class="children nav-child unstyled small collapse" id="sub-item-<?php echo $x; ?>">
                                                        <?php
                                                        if ($menu) {
                                                            $y = 0;
                                                            foreach ($menu as $level3) {
                                                                if ($level3['levelNo'] == 2 && $level3['masterID'] == $level2['navigationMenuID']) {
                                                                    $checked = '';
                                                                    $y++;
                                                                    ?>
                                                                    <li class="item-10">
                                                                        <a class="" href="#">
                                                                            <span class="sign2"><i class="fa fa-caret-right fa-lg" style="color: black;"></i></span>
                                                                            <span class="lbl"
                                                                               for="tall<?php echo $i; ?>-<?php echo $x; ?>-<?php echo $y; ?>">
                                                                                &nbsp;<?php echo $level3['description'] ?></span>
                                                                        </a>

                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }

                                                ?>

                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>


                    </li>
                    <?php

                }
            }
        }
        ?>
    </ul>

    <div class="modal fade" id="navigation_menu_add_model" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Navigation Menu</h4>
                </div>
                <form class="form-group" id="navigation_menu_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-4" id="typedv">
                                <label for="Type">Type </label>
                                <select class="form-control" id="type" name="type" onchange="hiderelaventfields()">
                                    <option value="">Select Type</option>
                                    <option value="1">Module</option>
                                    <option value="2">Menu</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4" id="leveldv">
                                <label for="Type">Level </label>
                                <select class="form-control" id="level" name="level" onchange="hidemaster()">
                                    <option value="0">Level - 0</option>
                                    <option value="1">Level - 1</option>
                                    <option value="2">Level - 2</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4" id="modulesdv">
                                <label for="Type">Modules </label>
                                <?php echo form_dropdown('modules', $module, '', 'class="form-control" onchange="loadMaster()" id="modules" required'); ?>
                            </div>
                            <div class="form-group col-sm-4" id="mastersdv">
                                <label for="Type">Masters </label>
                                <select class="form-control" id="masters" name="masters">

                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="form-group ">
                                    <label for="">Description </label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="form-group ">
                                    <label for="">Page Title </label>
                                    <input type="text" class="form-control" id="pagetitle" name="pagetitle">
                                </div>
                            </div>
                            <div class="form-group col-sm-4" id="urldv">
                                <div class="form-group ">
                                    <label for="">URL </label>
                                    <input type="text" class="form-control" id="url" name="url">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="form-group ">
                                    <label for="">Icon </label>
                                    <input type="text" class="form-control" id="icon" name="icon">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="Type">Is Sub Exist </label>
                                <select class="form-control" id="subexist" name="subexist" onchange="showURL()">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="savbtn" onclick="saveNavigation()" class="btn btn-sm btn-primary"><i class="fa fa-floppy-o"
                                                                                                                       aria-hidden="true"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>




<?php

function whatever($array, $key, $val, $key2, $val2)
{
    foreach ($array as $item)
        if ((isset($item[$key]) && $item[$key] == $val) && (isset($item[$key2]) && $item[$key2] == $val2))
            return true;
    return false;
}

?>
<script>
    $('input[type=checkbox]').click(function () {
        if (this.checked) {
            $(this).parents('li').children('input[type=checkbox]').prop('checked', true);
        }
        $(this).parent().find('input[type=checkbox]').prop('checked', this.checked);
    });
    </script>