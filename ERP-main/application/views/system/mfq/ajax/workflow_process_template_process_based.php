<?php
$disablebutton = "";
if ($type == 2) {
    $disablebutton = "disabledbutton";
}
$z = 0;
$x = 0;

$output = "";
if($customDetail){
    $output = $customDetail;
}else{
    $output = $detail;
}

if ($output) {
    ?>
    <div class="col-md-12 bhoechie-tab-container">
        <div class="col-lg-2 bhoechie-tab-menu">
            <div class="list-group">
                <?php
                $i = 0;
                foreach ($output as $key => $val) {
                    $active = '';
                    if ($z == 0 && $val["status"] == 0) {
                        $active = 'active';
                        $z++;
                    }
                    $prevStatus = 0;
                    if ($i != 0) {
                        $prevStatus = $output[$key - 1]['status'];
                    }
                    if ($val["status"] == 1) {
                        ?>
                        <div class="pull-right"
                             style="position: absolute;z-index: 3;margin-top: 60px;margin-left: 150px" id="complete_process_<?php echo $val['templateDetailID']; ?>">
                            <i class="fa fa-check" style="color: green"></i></div>
                    <?php }else{?>
                        <div class="pull-right"
                             style="position: absolute;z-index: 3;margin-top: 60px;margin-left: 150px" id="complete_process_<?php echo $val['templateDetailID']; ?>">
                           </div>
                    <?php } ?>
                    <a href="#" id="Tab_<?php echo $i ?>"
                       onclick="get_workflow_template_process_based('<?php echo $val['pageNameLink']; ?>','workflow_template','<?php echo $val['workFlowID']; ?>','<?php echo $val['documentID']; ?>',<?php echo $type; ?>,'<?php echo $i; ?>',<?php echo $val['templateDetailID']; ?>,<?php echo $val['linkworkFlow']; ?>,<?php echo $val['workFlowTemplateID']; ?>)"
                       class="list-group-item text-center <?php echo $active; ?> ">
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
            <?php $i = 0; ?>
            <div class="bhoechie-tab-content <?php echo $active; ?>" id="workflow_template"></div>
        </div>
    </div>
    <?php
} else {
    ?>
    <header class="infoarea">
        <div class="search-no-results">WORKFLOW NOT CONFIGURED
        </div>
    </header>
    <?php
}
?>
<script>
    <?php if ($z == 0){
    ?>
    $('#Tab_0').addClass('active');
    $('.list-group a:first').trigger("click");

    <?php
    }else { ?>
    $('.active').trigger("click");
    <?php } ?>
    $("div.bhoechie-tab-menu>div.list-group>a").click(function (e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
        $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });
</script>