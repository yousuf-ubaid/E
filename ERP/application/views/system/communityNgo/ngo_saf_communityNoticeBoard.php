<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('community_ngo_helper');
$title = $this->lang->line('communityngo_notice_board');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$noticeType = Notice_Type_drop();
$noticeTypefilter = Notice_Type_filter();
?>


    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">

    <div id="filter-panel" class="collapse filter-panel" xmlns="http://www.w3.org/1999/html"></div>
    <div class="row">
        <div class="col-md-9">
            <div class="form-group col-sm-4">
                <label for="">Date Range :</label>
                <div class="input-group" style="width:100% !important; height:28px !important;" >
                    <button type="button" class="btn btn-default btn-xs btn-block btn-flat" id="daterange-btn" style="background-color: white; height:28px !important;">
                        <span id="spanLid"></span>
                        <i class="fa fa-caret-down pull-right" style="margin-top: 3px;"></i>
                    </button>
                </div>
                <div id="receiptDateDiv" style="display:none;">
                    <input type="text" name="dateFrom" id="dateFrom" value="">
                    <input type="text" name="datesTo" id="datesTo" value="">
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
            </div>
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('communityngo_notice_type')?> :</label>
                <?php // echo form_dropdown('noticeType', array('' => 'All', '1' => 'Janaza Announcement', '2' => 'Bayan Announcement', '3' => 'General Announcement'), '', 'class="form-control select2" id="noticeType" onchange="FilterAnnouncements(this)"'); ?>
                <?php  echo form_dropdown('noticeType', $noticeTypefilter, '', ' class="form-control select2" id="noticeType" onchange="FilterAnnouncements(this)" '); ?>

                <span class="input-req-inner" style="z-index: 100"></span></span>
            </div>
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('common_status')?> :</label>
                <?php  echo form_dropdown('Expired', array('1' => 'Not Expired', '2' => 'Expired', '0' => 'All'), '', 'class="form-control select2" id="Expired" onchange="FilterAnnouncements(this)"'); ?>

                <span class="input-req-inner" style="z-index: 100"></span></span>
            </div>
          </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-info btn-sm pull-right"  onclick="fetchPage('system/communityNgo/ngo_saf_newNotice', '', 'Add New Announcement')"
                    style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('communityngo_notice_new')?>
            </button>
        </div>
    </div>

    <div class="col-md-12">
        <div id="divAnnouncements">
        </div>
    </div>



    <div class="modal fade bs-example-modal-lg" id="add_attachment_show" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                       Add attachment</h4>
                </div>
                <div class="modal-body">
                    <div class="row " >
                        <?php echo form_open_multipart('', 'id="attachment_Upload_form" class="form-inline"'); ?>
                        <div class="col-sm-12" style="">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        Description<?php required_mark(); ?></label>
                                    <input type="text" class="form-control" id="noticeattachmentDescription"
                                           name="attachmentDescription" placeholder="Description..." style="width: 115%;">
                                    <input type="hidden" class="form-control" id="documentID" name="documentID" value="7">
                                    <input type="hidden" class="form-control" id="NoticeID" name="NoticeID"
                                           value="">
                                </div>
                            </div>

                            <div class="col-sm-6" style="margin-top: -8px;">
                                <div class="form-group">
                                    <label class=" control-label" style="visibility: hidden;">UPLOAD</label>
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                         style="margin-top: 8px;">
                                        <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span style="width: 100%;"
                                                class="fileinput-filename" id="filename"></span></div>
                                        <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                        <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                           data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"></span></a>
                                    </div>
                                </div>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="Save_new_attachment()">
                        <?php echo $this->lang->line('common_Upload'); ?><!--Upload--></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="add_attachment_show" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="show_all_attachments">

            </div>
        </div>
    </div>



<?php echo footer_page('Right foot','Left foot',false); ?>



    <script type="text/javascript">

        $(document).ready(function () {

             $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_saf_communityNoticeBoard', '', 'Announcements')
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#AddVehicleForm').bootstrapValidator('revalidateField', 'registerDate');

            });

            $('.select2').select2();


            var start=moment();
            var end =moment();

            function cb(start, end) {
                document.getElementById('dateFrom').value = start.format('YYYY-MM-DD');
                document.getElementById('datesTo').value = end.format('YYYY-MM-DD');
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                FilterAnnouncements();

            }

            $('#daterange-btn').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


        });


        function addAttachment(id) {
            $('#add_attachment_show').modal('show');
            $('#NoticeID').val(id);
        }

    function FilterAnnouncements() {
        var noticeType = $.trim($('#noticeType').val());
        var status = $.trim($('#Expired').val());

        var date_from =document.getElementById('dateFrom').value;
        var date_To=document.getElementById('datesTo').value;

        $.ajax({
            type: "POST",
            data: {'noticeType': noticeType,'status':status,'dateTo': date_To, 'dateFrom': date_from},
            url: "<?php echo site_url('communityNgo/get_notice_announcement') ?>",
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#divAnnouncements").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    }

    function deleteAnnouncement(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('communityNgo/delete_notice'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'NoticeID': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            FilterAnnouncements();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function Save_new_attachment() {
            var formData = new FormData($("#attachment_Upload_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('communityNgo/ngo_NoticeBoardAttachment_upload'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['status'] == 1) {
                        $('#add_attachment_show').modal('hide');
                        $('#remove_id').click();
                        $('#noticeattachmentDescription').val('');
                        FilterAnnouncements();
                        myAlert('s', data['message']);
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

    </script>
