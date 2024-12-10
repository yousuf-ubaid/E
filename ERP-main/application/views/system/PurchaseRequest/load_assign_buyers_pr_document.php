<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
</style>
<?php
if (!empty($emp)) { ?>
    <div class="table-responsive mailbox-messages">
        <input type="hidden" name="current_user_access" id="current_user_access" value="<?php echo $buyer_access ?>">

        <?php if($buyer_access==1){ ?>

            <div class="row" style="margin-left:0px !important;">

                       

                    <?php echo form_open('', 'role="form" class="form-horizontal" id="category_buyers_form_document"') ?>
                        <input type="hidden" name="selected_master_id" id="selected_master_id" value ="<?php echo $purchaseRequestID ?>">
                        <input type="hidden" name="selected_detail_id" id="selected_detail_id" value ="<?php echo $purchaseRequestDetailsID ?>">
                        <div class="form-group col-sm-4">
                            <label for="supplierPrimaryCode">Buyers</label><br>
                            <?php echo form_dropdown('buyers_for_cat[]', load_employee_with_group_drop(), '', 'class="form-control" id="buyers_for_cat" onchange="" multiple="multiple"'); ?>
                        </div>
                    </form>

                    <div class="form-group col-sm-4">
                    <label for="supplierPrimaryCode">&nbsp;&nbsp;</label><br>
                    <button class="btn btn-success btn-sm" id="addAllBtn" style="font-size:12px;"
                        onclick="addAllRows_buyers_on_document()"> <?php echo $this->lang->line('common_save');?>
                    </div>
                </div>

                <hr>
        <?php } ?>
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Buyer Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Buyer Name</td>

                <!-- <?php if($type==2){ ?>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">
                        <?php if($buyer_access==1){ ?>
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns">
                                <input id="buyersAssign_MasterCheck" type="checkbox"
                                    data-caption="" class="columnSelected"
                                    name="isActive" onclick=""
                                    value="">
                            </div>
                        </div>
                        <?php } ?>
                    </td>
                <?php }else{ ?>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Action</td>
                <?php } ?> -->
            </tr>
            <?php
            $x = 1;
            foreach ($emp as $val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['ECode']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['Ename1']; ?>
                    </td>

                    <?php if($val['assignMasterID']== null){ ?>
                        <td>
                        <a class="text-yellow" onclick="remove_assign_buyers_pr_item(<?php echo $val['autoID'] ?>,<?php echo $val['activityMasterID'] ?>,<?php echo $val['activityDetailID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </td>
                    <?php } ?>
                    <!-- <?php if($type==2){ ?>
                        <td width="5%">
                            <?php if($buyer_access==1){ ?>
                                <?php  if($val['activityIsActive']==0) { ?>
                                    <div class="skin skin-square">
                                        <div class="skin-section extraColumns"><input
                                                id="supplier_<?php echo $val['autoID'] ?>" type="checkbox"
                                                data-caption="" class="columnSelected buyers_checkbox_template"
                                                name="" onclick="assign_buyers_selected_check(this)"
                                                value="<?php echo $val['autoID'] ?>"><label for="checkbox">&nbsp;</label>
                                        </div>
                                    </div>
                                <?php }else{ ?>
                                  

                                    <span class="pull-right">
                                    <a onclick="remove_assign_buyers_pr(<?php echo $val['autoID'] ?>)">
                                        <span title="Remove" class="glyphicon glyphicon-trash" style="" rel="tooltip"></span>
                                    </a>
                                </span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php }else{ ?>
                        <td width="10%">
                        <?php if($val['activityIsActive']==1){ ?>
                            <span class="label label-success">Selected</span>
                        <?php }else{ ?>
                            <span class="label label-warning">Not Selected</span>
                        <?php } ?>
                        </td>
                    <?php } ?> -->
                </tr>
                <?php
                $x++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO BUYERS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">

    
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('.buyers_checkbox_template').on('ifChecked', function (event) {
            assign_buyers_selected_check(this);
        });
        $('.buyers_checkbox_template').on('ifUnchecked', function (event) {
            assign_buyers_selected_check(this);
        });

        $('#buyersAssign_MasterCheck').on('ifChecked', function (event) {
            $('.buyers_checkbox_template').iCheck('check');
        });

        $('#buyersAssign_MasterCheck').on('ifUnchecked', function (event) {
            $('.buyers_checkbox_template').iCheck('uncheck');
        });

        $('#buyers_for_cat').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

    });
</script>