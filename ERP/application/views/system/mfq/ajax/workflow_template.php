<?php
$disablebutton = "";
if($type ==2){
    $disablebutton="disabledbutton";
}
if ($detail) {
    ?>
    <div class="col-md-12 bhoechie-tab-container">
        <div class="col-lg-2 bhoechie-tab-menu">
            <div class="list-group">
                <?php
                $i = 0;
                foreach ($detail as $val) {
                    $active = '';
                    if ($i == 0) {
                        $active = 'active';
                    }
                    ?>
                    <div class="pull-right" style="position: relative;z-index: 3;margin-top: 2px;margin-right: 5px"><i class="fa fa-trash" onclick="delete_workflow_detail(<?php echo $templateMasterID; ?>,<?php echo $val['templateDetailID']; ?>)" style="color: red"></i></div>
                    <a href="#" onclick="get_workflow_template('<?php echo $val['pageNameLink']; ?>','workflow_template','<?php echo $val['workFlowID']; ?>','<?php echo $val['documentID']; ?>',<?php echo $type; ?>,<?php echo $val['templateDetailID']; ?>,<?php echo $val['linkworkFlow']; ?>)"
                       class="list-group-item text-center <?php echo $active; ?> <?php if($i != 0) echo $disablebutton; ?>">

                        <h4 class="glyphicon"><span class="badge"><?php echo $val['sortOrder']; ?></span></h4>
                        <br/><?php echo $val['description']; ?>
                    </a>
                    <?php
                    $i++;
                }
                ?>
            </div>
        </div>
        <div class="col-lg-10 bhoechie-tab">
            <?php
            $i = 0;
            /*foreach ($detail as $val) {
                $active = '';
                if ($i == 0) {
                    $active = 'active';
                }*/
                ?>
                <div class="bhoechie-tab-content <?php echo $active;?>" id="workflow_template">

                </div>
                <?php
            /*    $i++;
            }*/
            ?>
        </div>
    </div>
    <?php
}
?>
<script>
    $('.list-group a:first').trigger("click");
    $("div.bhoechie-tab-menu>div.list-group>a").click(function (e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
        $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });
</script>