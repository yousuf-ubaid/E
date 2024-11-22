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
if (!empty($common_asset)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Asset Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">
                    <!-- <div class="skin skin-square">
                        <div class="skin-section extraColumns">
                            <input id="asset_common_checkbox_MasterCheck" type="checkbox"
                                   data-caption="" class="columnSelected"
                                   name="isActive" onclick=""
                                   value="">
                        </div>
                    </div> -->
                </td>
            </tr>
            <?php
            $x = 1;
            foreach ($common_asset as $val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['faCode']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['assetDescription']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                    <div class="text-center"><span class="label label-success">&nbsp;</span></div>
                    </td>
                   
                    <td width="5%">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input
                                    id="supplier_<?php echo $val['faID'] ?>" type="checkbox"
                                    data-caption="" class="columnSelected asset_common_checkbox"
                                    name="" onclick="assign_checklist_selected_check(this)"
                                    value="<?php echo $val['faID'] ?>"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>

            <tr>
                <td class="mailbox-name" colspan="4">       
                </td>
                <td class="mailbox-name pull-right" >
                <button class="btn btn-primary-new size-sm" onclick="assign_job_common_assets()">Assign</button>       
                </td>
            </tr>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO ASSET TO DISPLAY.</div>
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

        $('.asset_common_checkbox').on('ifChecked', function (event) {
            assign_asset_common_selected_check(this);
        });
        $('.asset_common_checkbox').on('ifUnchecked', function (event) {
            assign_asset_common_selected_check(this);
        });

        $('#asset_common_checkbox_MasterCheck').on('ifChecked', function (event) {
            $('.asset_common_checkbox').iCheck('check');
        });

        $('#asset_common_checkbox_MasterCheck').on('ifUnchecked', function (event) {
            $('.asset_common_checkbox').iCheck('uncheck');
        });

    });
</script>