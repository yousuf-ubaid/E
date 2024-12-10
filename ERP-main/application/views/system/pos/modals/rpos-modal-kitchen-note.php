<?php
/**
 * --- POS Open void Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .m-h-90 {
        min-height: 90px;
    }
</style>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_kitchen_note" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <div class="row">
                    <div class="col-md-8 col-sm-8 col-xs-6">
                        <h4 class="modal-title"><?php echo $this->lang->line('posr_kitchen_note'); ?> - <span
                                    id="kitchenNoteDescription"></span></h4>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-6">
                        <button type="button"  id="btn_kitchenNote"
                                class="btn btn-lg btn-primary btn_kitchenNote_ok"><?php echo $this->lang->line('common_ok'); ?> </button>
                        <button type="button" class="btn btn-lg btn-danger closeTouchPad" data-dismiss="modal" >Close</button>
                    </div>


                </div>


            </div>
            <div id="voidReceipt" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 100px;">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div>
                            <h5 class="add-on-heading">Kitchen Note </h5>
                            <?php
                            $kitchenNoteSamples = get_kitchenNoteSamples();
                            if (!empty($kitchenNoteSamples)) {
                                foreach ($kitchenNoteSamples as $kitchenNoteSample) {
                                    ?>
                                    <button onclick="loadToKitchenNote(this)"
                                            class="button button-border button-rounded button-primary button-small"
                                            style="margin: 2px;"><?php echo $kitchenNoteSample['noteDescription'] ?></button><?php
                                }
                            }
                            ?>
                        </div>

                        <form id="frm_kot" method="post">
                            <input type="hidden" name="kotID" id="kot_kotID" value="0"/>
                            <input type="hidden" name="tmpWarehouseMenuID" id="tmpWarehouseMenuID" value="0"/>
                            <textarea style="width: 100%" class="kitchen-note-txt-f custom_touch_keyboad" name="kitchenNote " id="kitchenNote"
                                      cols="30"
                                      rows="5"></textarea>
                            <button type="button" class="btn btn-xs btn-cus-3 pull-right" onclick="clearKOTNote()"><i
                                        class="fa fa-eraser"></i> Clear Note
                            </button>
                        </form>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <h5 class="add-on-heading">Add-on</h5>

                        <?php
                        $add_on_list = get_add_on_list();
                        if (!empty($add_on_list)) {
                            foreach ($add_on_list as $addOn) {
                                if ($addOn['showImageYN'] == 1) {
                                    $style = 'background-image: url(\'' . $addOn['menuImage'] . '\'); background-size: cover;';
                                } else {
                                    $style = 'background-color: ' . $addOn['bgColor'] . ';';
                                }
                                ?>

                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="font-size: small;">
                                <div class="kot-add-on">
                                    <div class="row">
                                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-3" style="padding-right: 0px;">
                                            <div class="kot-add-on-img"
                                                 style="<?php echo $style ?>"></div>
                                        </div>
                                        <div class="col-xs-7 col-sm-8 col-md-8 col-lg-7" style="padding-right: 0px;margin-left: -5px;display: flex;height: 50px;">
                                            <p><?php echo $addOn['menuMasterDescription'] ?></p>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-top: 4px;">
                                            <input class="kot-add-on-input-check" name="kotAddOn[]"
                                                   value="<?php echo $addOn['autoID'] ?>"
                                                   data-id="<?php echo $addOn['autoID'] ?>"
                                                   id="kot_add_on_id_<?php echo $addOn['autoID'] ?>" type="checkbox">
                                        </div>
                                    </div>
                                </div>
                        </div>

                                <?php
                            }
                        } else {
                            echo '<em>No Add-on found!</em>';
                        }
                        ?>
                    </div>
                </div>


            </div>

            <div class="modal-footer" style="margin-top: 0px; ">
                <div class="col-md-8 col-sm-8 col-xs-6">

                </div >
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <button type="button"  id="btn_kitchenNote"
                            class="btn btn-lg btn-primary btn_kitchenNote_ok"><?php echo $this->lang->line('common_ok'); ?> </button>
                    <button type="button" class="btn btn-lg btn-danger closeTouchPad" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="hold_reference_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Bill Reference <span class="loader_refresh text-warning pull-right"> <i
                                class="fa fa-refresh fa-spin"
                                aria-hidden="true"></i>Loading Please wait </span>
                </h4>
            </div>
            <div class="modal-body m-h-90">
                <div class="form-group">
                    <div class="col-md-12">
                        <textarea class="form-control custom_touch_keyboad" id="kot_hold_reference" name="kot_hold_reference"
                                  rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" onclick="save_hold_reference_beforeKOT()"
                        class="btn btn-lg btn-primary closeTouchPad"><?php echo $this->lang->line('common_ok'); ?> </button>
            </div>
        </div>
    </div>
</div>

<script>
    function before_kot_hold_reference() {
        $("#hold_reference_modal").modal('show');
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/get_hold_reference'); ?>",
            data: {menuSalesID: $("#holdInvoiceID").val()},
            cache: false,
            beforeSend: function () {
                $(".loader_refresh").show();
            },
            success: function (data) {
                $(".loader_refresh").hide();
                if (data['error'] == 0) {
                    $("#kot_hold_reference").val(data['reference']);
                }
                $("#kot_hold_reference").focus();
            }, error: function (jqXHR, textStatus, errorThrown) {
                $(".loader_refresh").hide();
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function save_hold_reference_beforeKOT() {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/save_hold_reference'); ?>",
            data: {menuSalesID: $("#holdInvoiceID").val(), reference: $("#kot_hold_reference").val()},
            cache: false,
            beforeSend: function () {
                $(".loader_refresh").show();
            },
            success: function (data) {
                $(".loader_refresh").hide();
                if (data['error'] == 0) {
                    $("#hold_reference_modal").modal('hide');
                    POS_SendToKitchen();
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                $(".loader_refresh").hide();
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    var kotAddOnList = [];
    var isPackCMbo = 0;

    function open_kitchen_note(id, kotID) {
        $("#tmpWarehouseMenuID").val(id);
        if (kotID > 0) {
            $("#pos_kitchen_note").modal('show');
        }
    }

    function selectMe(tmpThis) {
        var id = $(tmpThis).children().find('input').attr('data-id');
        $('#kot_add_on_id_' + id).iCheck('toggle');
    }

    $(document).ready(function (e) {
        $("#kot_hold_reference").keyup(function (e) {
            var keyValue = e.which;
            if (keyValue == 13) {
                save_hold_reference_beforeKOT();
            }
        })
        $(".kot-add-on").click(function (e) {
            selectMe(this);
        });

        $(".btn_kitchenNote_ok").click(function (e) {

            $("#pos_kitchen_note").modal('hide');
            var id = $("#tmpWarehouseMenuID").val();
            $.each($(".kot-add-on-input-check"), function (i, val) {
                if (val.checked == true) {
                    kotAddOnList.push(val.value);
                }
            });

            if(isPackCMbo==0){
                LoadToInvoice(id);
            }



        });


        $('#pos_kitchen_note').on('hidden.bs.modal', function () {
            $("#kitchenNote").val('');
            $("#kot_kotID").val(0);
            $(".kot-add-on-input-check").iCheck('uncheck');
        });

        $('input').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '20%' // optional
        });

    });

    function loadToKitchenNote(tmpThis) {
        var tmpValue = $("#kitchenNote").val();
        var noteText = tmpValue + '- ' + $(tmpThis).text().trim() + " \n ";

        $("#kitchenNote").val(noteText);
        //$("#kitchenNote").focus();
    }

    function clearKOTNote() {
        $("#kitchenNote").val('');
        //$("#kitchenNote").focus();
    }

    function closeTouchPad(e){
        if (!$(e.target).hasClass('touchEngKeyboard')) {
            $("div.touchEngKeyboard").hide();
        }
    }

    $(document).on('click','.closeTouchPad',function (e) {
        closeTouchPad(e);
    });

    $('#hold_reference_modal').on('hidden.bs.modal', function (e) {
        closeTouchPad(e);
    });


</script>