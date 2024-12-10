<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo head_page($this->lang->line('iou_users'), false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employeedrop = all_employee_drop();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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
    .actionicon{
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
    .headrowtitle{
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
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/iou/create_iou_user',null,'<?php echo $this->lang->line('iou_add_new_iou_user') ?>',' ');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new') ?>
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="<?php echo $this->lang->line('iou_enter_your_text_here') ?>"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
    <div class="col-md-2">
        <?php echo form_dropdown('iouuserstatus', array('' => $this->lang->line('iou_status'), '1' => $this->lang->line('common_active'), '2' => $this->lang->line('common_not_active')), '', 'class="form-control" onchange="startMasterSearch()" id="iouuserstatus"'); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="iou_user_master_view">
            <!-- /.table -->
        </div>

    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/iou/iou_user','','<?php echo $this->lang->line('iou_users'); ?>');
        });
        getiouvoucherbookingtable();
        Inputmask().mask(document.querySelectorAll("input"));
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function getiouvoucherbookingtable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var iouuserstatus = $('#iouuserstatus').val();


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask,'iouuserstatus':iouuserstatus},
            url: "<?php echo site_url('Iou/load_iou_user_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_user_master_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getiouvoucherbookingtable();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.donorsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#iouuserstatus').val('');
        $('#sorting_1').addClass('selected');
        getiouvoucherbookingtable();
    }
    function delete_iou_user(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'userID': id},
                    url: "<?php echo site_url('Iou/delete_iou_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getiouvoucherbookingtable();
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }



    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });
</script>