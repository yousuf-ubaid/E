<br>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-12">
        <div class="form-group col-sm-2" style="width: 10%;">
            <label class="title">No of Rows</label>
        </div>
        <div class="form-group col-sm-4" style="width: 10%;">
            <?php
            $type = '';
            if($noofrows> 0)
            {
                $type = 'readonly';
            }else
            {
                $type = '';
            }
            ?>
            <input type="text" class="form-control " id="noofrows" name="noofrows" value="<?php echo $noofrows?>" <?php echo $type?>>
        </div>
        <?php if($noofrows==0){?>
            <div class="form-group col-sm-4" style="width: 10%;">
                <button  type="button" onclick="generate_noofrows('<?php echo $checklistID?>')" class="btn btn-primary pull-right">
                    <i class="fa fa-plus"></i>
                    Generate
                </button>
            </div>
        <?php }else {?>
            <div class="form-group col-sm-4" style="width: 4%;">
                <button  type="button" onclick="add_new_update_critiriadetil('<?php echo $checklistID?>')" class="btn btn-primary pull-right">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        <?php }?>
    </div>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <?php foreach ($coltype as $val){?>
                <th style="width: <?php echo $val['width']?>%;color:<?php echo $val['fontColor']?>;background-color: <?php echo $val['bgColor']?>" class="text-center"><?php echo $val['detailDescription']?></th>

            <?php }?>
            <th class="text-center" style="width: 1%;">Is Header</th>
            <th class="text-center" style="width: 1%;">Action</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($coltype)) { ?>
            <?php foreach($checklisttempdetail AS $val){?>
                <tr>

                    <?php foreach ($coltype as $val2){?>

                        <?php if($val2['columnTypeID'] == 5) {?>
                            <?php if($val['isTitle'] == 1) {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                            <?php }else {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                    <div class="skin-section extraColumns"><input id="checkbox_demo" type="checkbox"
                                                                                  data-caption="" class="columnSelected"
                                                                                  name="checkbox_demo"
                                                                                  value=" "><label for="checkbox"> </label>
                                    </div>
                                </th>
                            <?php }?>
                        <?php }?>
                        <?php if($val2['columnTypeID'] == 4) {?>
                            <?php if($val['isTitle'] == 1) {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                            <?php }else {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                    <div class="skin-section extraColumns"><input id="checkbox_demo_<?php echo $val['criteriaID']?>" type="radio"
                                                                                  data-caption="" class="columnSelected"
                                                                                  name="checkbox_demo_<?php echo $val['criteriaID']?>"
                                                                                  value=" "><label for="checkbox">&nbsp; </label>
                                    </div>
                                </th>
                            <?php }?>
                        <?php }?>
                        <?php if($val2['columnTypeID'] == 3) {?>
                            <?php if($val['isTitle'] == 1) {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                            <?php }else {?>

                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                    <textarea class="form-control" id="demo_checklisttxtarea" name="demo_checklisttxtarea" rows="2" readonly></textarea>
                                </th>

                            <?php }?>
                        <?php }?>
                        <?php if($val2['columnTypeID'] == 2) {?>

                            <?php if($val['isTitle'] == 1) {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                            <?php }else {?>
                                <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                    <input type="text" class="form-control " id="demo_checklisttxt" name="demo_checklisttxt" readonly>
                                </th>
                            <?php }?>
                        <?php }?>
                        <?php if($val2['columnTypeID'] == 1) {
                            $isTitle = '';
                            if($val['isTitle']==1)
                            {
                                $isTitle ='color:black';
                            }

                            ?>
                            <?php if($val2['checklistdetailID'] ==$val['checklistDetailID']  ){
                                $query=check_label_text($val['checklistmasterID'] );
                                ?>
                                <?php if($query['checklistone']==1){?>
                                    <th style="text-align: left;font-size: 80%; width:<?php echo $val2['width']?>%">

                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Boq/update_checklistlabel') ?>"
                                           data-pk="<?php echo $val['criteriaID'] ?>"
                                           data-name="criteriaDescription"
                                           data-title="<?php echo $val2['detailDescription']?>" class="xeditable "
                                           data-value="<?php echo $val['criteriaDescription'] ?>"
                                           style="<?php echo $isTitle?>">
                                        </a>

                                    </th>
                                <?php }?>
                                <?php if($query['checklisTwo']==1){?>
                                    <th style="text-align: left;font-size: 80%;width:<?php echo $val2['width']?>%">
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Boq/update_checklistlabel') ?>"
                                           data-pk="<?php echo $val['criteriaID']?>"
                                           data-name="criteriaDescriptionOne"
                                           data-title="<?php echo $val2['detailDescription']?>" class="xeditable "
                                           data-value="<?php echo $val['criteriaDescriptionOne'] ?>"
                                           style="<?php echo $isTitle?>">

                                        </a>
                                    </th>
                                <?php }?>
                                <?php if($query['checklistthree']==1){?>
                                    <th style="text-align: left;font-size: 80%;width:<?php echo $val2['width']?>%">
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Boq/update_checklistlabel') ?>"
                                           data-pk="<?php echo $val['criteriaID'] ?>"
                                           data-name="criteriaDescriptionTwo"
                                           data-title="<?php echo $val2['detailDescription']?>" class="xeditable "
                                           data-value="<?php echo $val['criteriaDescriptionTwo'] ?>"
                                           style="<?php echo $isTitle?>"
                                        >
                                        </a>
                                    </th>
                                <?php }?>
                                <?php if($query['checklistfour']==1){?>
                                    <th style="text-align: left;font-size: 80%;width:<?php echo $val2['width']?>%">
                                        <a href="#" data-type="text"
                                           data-url="<?php echo site_url('Boq/update_checklistlabel') ?>"
                                           data-pk="<?php echo $val['criteriaID'] ?>"
                                           data-name="criteriaDescriptionThree"
                                           data-title="<?php echo $val2['detailDescription']?>" class="xeditable "
                                           data-value="<?php echo $val['criteriaDescriptionThree'] ?>"
                                           style="<?php echo $isTitle?>"
                                        >

                                        </a>
                                    </th>
                                <?php }?>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                    <th>
                        <div class="TriSea-technologies-Switch" style="margin-left: 30%; margin-top: 7px">
                            <input id="istitle_<?php echo $val['criteriaID'] ?>" name="istitle_<?php echo $val['criteriaID'] ?>" type="checkbox"
                                   onchange="update_checklist_isheader(<?php echo $val['criteriaID'] ?>)"
                                <?php echo ($val['isTitle']!=1 ?'checked':'')?>/>
                            <label for="istitle_<?php echo $val['criteriaID'] ?>" class="label-danger"> </label>
                        </div>
                    </th>
                    <th style="text-align: right">
                        <a onclick="delete_recovery_attachment(<?php echo $val['criteriaID'] ?>)">
                            <span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a>
                    </th>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="7" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.xeditable').editable();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

</script>