<?php
$date_format_policy = date_format_policy();
$disable = '';
if($templatemaster['checklistconfirmedYN']=='1' || $templatemaster['checklistconfirmedYN']=='2')
{
    $disable = 'disabled';
}
  $Isexist = 'disabled';
if($approvalexist['documentchecklistID'])
{
    $Isexist = '';
}

?>

<div class="row">
    <div class="form-group col-md-12">
        <div class="form-group col-md-8" style="background-color: black;border: 1px solid black;margin-left: 8%;width: 55%;height: 39px; ">
            <label style="color: white;font-size:120%;margin-top: 1%"><?php echo $templatemaster['description']?></label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 12%;height: 39px;border-right: 0;border-left: 0">
            <label style="margin-top: 12%">DATE:</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 19%;height: 39px;">
            <div class="input-group date_master" style="margin-top: 3%">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="date_master"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="date_master"  class="form-control" value="<?php echo $templatemaster['date']?>" <?php echo $disable?>>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.9%">
        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;width: 14%;height: 28px; ">
            <label style="color: black;font-size:90%;margin-top: 4%">PROJECT :</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 29%;height: 28px;border-right: 0;border-left: 0">
            <label style="margin-top: 1%"><?php echo $templatemaster['projectName']?></label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 12%;height: 28px;">
            <label style="color: black;font-size:90%;margin-top: 4%">CONTRACT No:</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 31%;height: 28px;border-left: 0;border-top: 0">
            <input type="text" style="height: 22px;margin-top: 1%" class="form-control " id="contractno" name="contractno" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'contractno',this.value);" value="<?php echo $templatemaster['contractno']?>" <?php echo $disable?>>
        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">

        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;width: 14%;height: 28px;border-top: 0 ">
            <label style="color: black;font-size:90%;margin-top: 4%">STRUCTURE :</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 21%;height: 28px;border-right: 0;border-left: 0;border-top: 0">
            <input type="text" style="height: 26px;margin-top: 1%;height: 23px;" class="form-control " id="structure" name="structure" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'structure',this.value);" value="<?php echo $templatemaster['structure']?>" <?php echo $disable?>>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 8.05%;height: 28px;border-top: 0">
            <label style="color: black;font-size:90%;margin-top: 4%">SECTION:</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 42.99%;height: 28px;border-left: 0;border-top: 0">
            <input type="text" style="height: 22px;margin-top: 0.5%;" class="form-control " id="section" name="section"  onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'section',this.value);" value="<?php echo $templatemaster['section']?>" <?php echo $disable?>>
        </div>

    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">
        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;margin-top: 0;border-top: 0;width: 14%;height: 28px">
            <label style="color: black;font-size:90%;margin-top: 5%">DRAWINGS :</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 72%;height: 28px;border-left: 0;border-top: 0">
            <input type="text" style="height: 22px;margin-top: 0.5%;" class="form-control " id="drawings" name="drawings" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'drawings',this.value);" value="<?php echo $templatemaster['drawings']?>" <?php echo $disable?>>
        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">

        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;width: 14%;height: 28px;border-top: 0 ">
            <label style="color: black;font-size:90%;margin-top: 4%">SE/QC :</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 21%;height: 28px;border-right: 0;border-left: 0;border-top: 0">
            <input type="text" style="height: 26px;margin-top: 1%;height: 23px;" class="form-control " id="se_qc" name="se_qc" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'se_qc',this.value);" value="<?php echo $templatemaster['se_qc']?>" <?php echo $disable?>>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 8.05%;height: 28px;border-top: 0">
            <label style="color: black;font-size:90%;margin-top: 4%">FOREMAN:</label>
        </div>
        <div class="form-group col-md-2" style="border: 1px solid black;width: 42.99%;height: 28px;border-left: 0;border-top: 0">
            <input type="text" style="height: 22px;margin-top: 0.5%;" class="form-control " id="Foreman" name="Foreman" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'Foreman',this.value);" value="<?php echo $templatemaster['Foreman']?>" <?php echo $disable?>>
        </div>

    </div>
    <div class="form-group col-md-12" style="margin-top: -0.9%">
        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;width: 86%;height: 28px;border-top: 0">
            <label style="color: black;font-size:90%;margin-top: 1%">Legend : FM-Foreman, SE-Site Engineer, S/C-Subcontractor</label>
        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">
        <div class="form-group col-md-8" style="background-color: black;border: 1px solid black;margin-left: 8%;width: 86%;height: 28px;text-align: center">
            <label style="color: white;font-size:90%;margin-top: 0.5%">CHECKLIST: - (Enter initials or N/A into the appropriate block)</label>
        </div>
    </div>

    <div class="form-group col-md-12" style="margin-top: -0.96%">
        <div class="table-responsive"  style="background-color: white;border: 1px solid black;margin-left: 8%;width: 86%;text-align: center;border-top: 0">
                <br>
                <table class="table table-bordered table-striped">
                    <thead class='thead'>
                    <tr>
                        <?php foreach ($coltype as $val){?>
                            <th style="width: <?php echo $val['width']?>%;color:<?php echo $val['fontColor']?>;background-color: <?php echo $val['bgColor']?>" class="text-center"><?php echo $val['detailDescription']?></th>
                        <?php }?>
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
                                        <?php }else {
                                            $ischecked = fetch_templateval_detail($val['criteriaID'], $val2['checklistdetailID'],$documentchecklistID);
                                            ?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                                <div class="skin-section extraColumns"><input id="checklistaction_<?php echo $val['criteriaID']?><?php echo $val2['checklistdetailID']?>" type="checkbox"
                                                                                              data-caption="" class="columnSelected cheackall"
                                                                                              name="checklistaction"
                                                                                              value="<?php echo $val['criteriaID']?>_<?php echo $val2['checklistdetailID']?>_<?php echo $documentchecklistID?>"
                                                                                                     <?php echo ($ischecked==1 ?'checked':'')?>
                                                                                                <?php echo $disable?>
                                                                                                ><label for="checkbox"></label>
                                                </div>
                                            </th>
                                        <?php }?>
                                    <?php }?>
                                    <?php if($val2['columnTypeID'] == 4) {?>
                                        <?php if($val['isTitle'] == 1) {?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                                        <?php }else {
                                            $ischecked = fetch_templateval_detail($val['criteriaID'], $val2['checklistdetailID'],$documentchecklistID);
                                            ?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                                <div class="skin-section extraColumns"><input id="radiobtnaction_<?php echo $val['criteriaID']?>" type="radio"
                                                                                              data-caption="" class="columnSelected radiactivein"
                                                                                              name="radiobtnaction_<?php echo $val['criteriaID']?>"
                                                                                                 <?php echo ($ischecked==1 ?'checked':'')?>
                                                                                              value="<?php echo $val['criteriaID']?>_<?php echo $val2['checklistdetailID']?>_<?php echo $documentchecklistID?>" <?php echo $disable?>><label for="checkbox">&nbsp; </label>
                                                </div>
                                            </th>
                                        <?php }?>
                                    <?php }?>
                                    <?php if($val2['columnTypeID'] == 3) {?>
                                        <?php if($val['isTitle'] == 1) {?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                                        <?php }else {?>

                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                                <textarea class="form-control" id="demo_checklisttxtarea" name="demo_checklisttxtarea" rows="2" onchange="update_textboxvalue('<?php echo $val['criteriaID']?>_<?php echo $val2['checklistdetailID']?>_<?php echo $documentchecklistID?>',this.value)" <?php echo $disable?>><?php echo fetch_templateval_detail($val['criteriaID'], $val2['checklistdetailID'],$documentchecklistID);?></textarea>
                                            </th>

                                        <?php }?>
                                    <?php }?>
                                    <?php if($val2['columnTypeID'] == 2) {?>

                                        <?php if($val['isTitle'] == 1) {?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">&nbsp;</th>
                                        <?php }else {?>
                                            <th style="text-align: center;width:<?php echo $val2['width']?>%;">
                                                <input type="text" class="form-control " id="textbox_<?php echo $val['criteriaID']?><?php echo $val2['checklistdetailID']?>" name="textbox_<?php echo $val['criteriaID']?><?php echo $val2['checklistdetailID']?>" onchange="update_textboxvalue('<?php echo $val['criteriaID']?>_<?php echo $val2['checklistdetailID']?>_<?php echo $documentchecklistID?>',this.value)"
                                                value='<?php echo fetch_templateval_detail($val['criteriaID'], $val2['checklistdetailID'],$documentchecklistID);?>'
                                                    <?php echo $disable?>
                                                >
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
                                                        <label><?php echo $val['criteriaDescription'] ?></label>
                                                </th>
                                            <?php }?>
                                            <?php if($query['checklisTwo']==1){?>
                                                <th style="text-align: left;font-size: 80%; width:<?php echo $val2['width']?>%">
                                                    <label><?php echo $val['criteriaDescriptionOne'] ?></label>
                                                </th>
                                            <?php }?>
                                            <?php if($query['checklistthree']==1){?>
                                                <th style="text-align: left;font-size: 80%; width:<?php echo $val2['width']?>%">
                                                    <label><?php echo $val['criteriaDescriptionTwo'] ?></label>
                                                </th>
                                            <?php }?>
                                            <?php if($query['checklistfour']==1){?>
                                                <th style="text-align: left;font-size: 80%;width:<?php echo $val2['width']?>%">
                                                    <label><?php echo $val['criteriaDescriptionThree'] ?></label>
                                                </th>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                <?php }?>
                            </tr>
                            <?php
                            $num++;
                        }
                    } else {
                        echo '<tr class="danger"><td colspan="7" class="text-center">No Records Found</td></tr>';
                    } ?>
                    </tbody>

                </table>
                <br>

        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">
        <div class="form-group col-md-8" style="background-color: white;border: 1px solid black;margin-left: 8%;width: 86%;height: 160px;text-align: left">
            <label>Remarks</label>
            <textarea class="form-control" rows="7"  id="remarks" name="remarks" onchange="update_masterchecklist(<?php echo $documentchecklistID?>,'remarks',this.value);" <?php echo $disable?>><?php echo $templatemaster['remarks'] ?></textarea>
        </div>
    </div>
    <div class="form-group col-md-12" style="margin-top: -0.96%">
        <div class="form-group col-md-4" style="background-color: black;border: 1px solid black;margin-left: 8%;width: 35%;height: 28px;margin-top: 1.4%;text-align: center">
            <label style="color: white;font-size:90%;margin-top: 1%">Approved</label>
        </div>
        <div class="form-group col-md-2" style="background-color: white;border: 0px solid black;margin-left: 1%;width: 5%;height: 28px;text-align: center;margin-top: 1.8%">
            <div class="skin-section extraColumns approvedcheckYes"><input id="approvedcheck_yes" type="radio"
                                                          data-caption="" class="columnSelected approvedcheckYes"
                                                          name="approvedcheck"
                                                          value="<?php echo $documentchecklistID?>" <?php echo ($templatemaster['approvedYN']==1?'checked':'')?> <?php echo $Isexist?>><label for="checkbox"> </label>
            </div>
        </div>
        <div class="form-group col-md-4" style="background-color: black;border: 1px solid black;margin-left: 5%;width: 35%;height: 28px;;margin-top: 1.4%;text-align: center">
            <label style="color: white;font-size:90%;margin-top: 1%">Not Approved</label>
        </div>
        <div class="form-group col-md-2" style="background-color: white;border: 0px solid black;margin-left: 1%;width: 5%;height: 28px;text-align: center;margin-top: 1.8%">
            <div class="skin-section extraColumns approvedcheckno"><input id="approvedcheck_no" type="radio"
                                                          data-caption="" class="columnSelected approvedcheckno"
                                                          name="approvedcheck"
                                                          value="<?php echo $documentchecklistID?>" <?php echo ($templatemaster['checklistconfirmedYN']==2?'checked':'')?>   <?php echo $Isexist?>><label for="checkbox"> </label>
            </div>
        </div>
    </div>
    </div>



</div>
<script type="text/javascript">
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('cheackall')) {
          update_checkliststatus_template(this,1);
        }
    });
    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('cheackall')) {
            update_checkliststatus_template(this,0);
        }
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('radiactivein')) {
            update_checkliststatus_template(this,1);
        }
    });
    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('radiactivein')) {
            update_checkliststatus_template(this,0);
        }
    });

    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('approvedcheckYes')) {
            updateapprovedyn(this.value,1,'Approve',<?php echo $approvalexist['approvalLevelID'] ?>);
        }
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('approvedcheckno')) {
            updateapprovedyn(this.value,2,'Reject',<?php echo $approvalexist['approvalLevelID']?>);
        }
    });

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.date_master').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        var Date = $('#date_master').val();
        update_masterchecklist(<?php echo $documentchecklistID?>,'date',Date);
    });
</script>


