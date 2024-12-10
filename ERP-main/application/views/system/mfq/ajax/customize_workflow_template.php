<?php
/**
 * Created by PhpStorm.
 * Date: 12/22/2017
 * Time: 11:08 AM
 */ ?>
<script type="text/javascript" src="<?php echo base_url('plugins/manufacturing/js/jquery-sortable.js'); ?>"></script>
<style>
    .column {
        margin-left: 2%;

    }

    .column.first {
        margin-left: 0;
    }

    .sortable-list {
        background-color: #F93;
        list-style: none;
        margin: 0;
        min-height: 60px;
        padding: 5px;
    }

    .sortable-item {
        background-color: #FFF;
        border: 1px solid #000;
        cursor: move;
        display: block;
        font-weight: bold;
        margin: 5px;
        padding: 20px 0;
        text-align: center;
    }

</style>
<div class="row" style="">
    <div class="col-md-12">
        <div id="example-1-3">
            <div class="col-md-6">
                <div><h5><strong>Work process</strong></h5></div>
                <div class="column left first">
                    <ul class="sortable-list">
                        <?php
                        foreach ($detail as $val) {
                            ?>
                            <li class="sortable-item" data-linkworkflow="<?php echo $val["linkworkFlow"] ?>" data-workflowtemplateid="<?php echo $val["workFlowTemplateID"] ?>" data-templatedetailid="<?php echo $val["templateDetailID"] ?>" data-workflowid="<?php echo $val["workFlowID"] ?>" data-sortorder="<?php echo $val["sortOrder"] ?>" data-description="<?php echo $val["description"] ?>"><?php echo $val["description"] ?></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div><h5><strong>Drag and drop</strong> <!--<div class="pull-right">Link: <input type="checkbox" id="linkProcess" checked></div>--></h5></div>
                <div class="column left">
                    <ul class="sortable-list" id="customize_workprocess">

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var adjustment;
    $('#example-1-3 .sortable-list').sortable({
        connectWith: '#example-1-3 .sortable-list'
    });

</script>
