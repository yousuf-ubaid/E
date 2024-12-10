<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], FALSE);

$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$noticeType = Notice_Type_drop();
$GSDivision = load_divisionForUploads();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>

    <style>
        .title {
            float: left;
            width: 170px;
            text-align: right;
            font-size: 13px;
            color: #7b7676;
            padding: 4px 10px 0 0;
        }
    </style>
<?php echo form_open('', 'role="form" id="NewNoticeForm"'); ?>
<input class="hidden" id="noticeId" name="noticeId">
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('common_description');?> </h2><!--DESCRIPTION-->
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_type');?> </label><!--Notice Type-->
                </div>
                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"> <?php
                    echo form_dropdown('NoticeTypeID', $noticeType, '', 'class="form-control select2" id="NoticeTypeID" required'); ?><span
                        class="input-req-inner"></span></span><!--Notice Type-->
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1" style="margin-top: 10px;">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_date');?> </label><!-- Notice Date -->
                </div>
                <div class="form-group col-sm-4">
                     <span class="input-req" title="Required Field">
                    <div class="input-group datepic_NoticePublishedDate">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                       <input type="text" name="NoticePublishedDate"
                               value="<?php echo $current_date ?>" id="NoticePublishedDate" class="form-control">
                    </div>
                    <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_GS_Division');?> </label><!--GS Division-->
                </div>

                <div class="form-group col-sm-4" id="noticeGS_div">

                <span class="input-req" title="Required Field">
                    <select id="noticestateID" name="noticestateID[]" class="form-control select2 noticestateID" data-placeholder="Select GS Division" multiple>
                                    <option value=""></option>
                        <?php foreach ($GSDivision as $Val) { ?>
                            <option value="<?php echo $Val['stateID']; ?>"><?php echo $Val['Description']; ?></option>
                        <?php } ?>
                                </select><span
                        class="input-req-inner"></span></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1" style="margin-top: 10px;">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_expiredate');?> </label><!-- Expiry Date-->
                </div>
                <div class="form-group col-sm-4">
                    <span class="input-req" title="Required Field">
                    <div class="input-group datepic_NoticeExpireDate">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="NoticeExpireDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="" id="NoticeExpireDate" class="form-control">
                    </div>
                        <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>
        </div>
    </div>
    <br>

    <!-- General Announcement -->
    <div class="row" id="GeneralAnnouncement" >
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('communityngo_notice_details');?> </h2><!--DESCRIPTION-->
            </header>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_subject');?> </label><!--Notice Subject-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                                <span class="input-req" title="Required Field"><input class="form-control" name="NoticeSubject" id="NoticeSubject"><span
                        class="input-req-inner"></span></span>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_description');?> </label><!--Description-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="4"
                                                                                         name="NoticeDescription"
                                                                                         id="NoticeDescription"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                </div>
            </div>
        </div>
    </div>


<!-- Bayan Announcement -->
    <div class="hide" id="BayanAnnouncement">
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('communityngo_bayan_details');?></h2><!--Bayan Details-->
            </header>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_venueDate');?>  </label><!--Venue date-->
                </div>
                <div class="form-group col-sm-3" style="margin-top: 5px;">
                     <span class="input-req" title="Required Field">
                    <div class='input-group date' id='datetimepickerbayan'>
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i></div>
                        <input type='text' class="form-control" id="bayanVenueDate" name="bayanVenueDate">
                    </div>
                         <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_venuePlace');?>  </label><!--Venue Place-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                     <span class="input-req" title="Required Field">
                    <input class="form-control" name="VenuePlace" id="VenuePlace">
                    <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_bayanSpeaker');?> </label><!--Bayan Speaker-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                     <span class="input-req" title="Required Field">
                         <input class="form-control" name="Speaker" id="Speaker">
                         <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_bayanOrganizer');?>  </label><!--Organizer-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <input class="form-control" name="bayanOrganizer" id="bayanOrganizer">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_subject');?> </label><!--Subject-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <span class="input-req" title="Required Field">
                                <input class="form-control" name="BayanSubject" id="BayanSubject">
                    <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_description');?> </label><!--Description-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                               <textarea class="form-control" rows="5"
                                         name="BayanDescription"
                                         id="BayanDescription"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Death Announcement -->
    <div class="hide" id="DeathAnnouncement">
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('communityngo_janaza_details');?></h2><!--Janaza Details-->
            </header>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_deadperson');?> </label><!--Passing of-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <span class="input-req" title="Required Field">
                        <input class="form-control" name="DeadPerson" id="DeadPerson">
                      <span class="input-req-inner" style="z-index: 100"></span></span>

                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_deadInformer');?> </label><!--Informer-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <span class="input-req" title="Required Field">
                   <input class="form-control" name="deathInformer" id="deathInformer">
                    <span class="input-req-inner" style="z-index: 100"></span></span>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_deadFamily');?> </label><!--Family Details-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <textarea class="form-control" rows="2"
                              name="DeadPrsnFamilBCR"
                              id="DeadPrsnFamilBCR"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_description');?> </label><!--Description-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <textarea class="form-control" rows="2"
                              name="DeathDescription"
                              id="DeathDescription"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_burialPlace');?>  </label><!--Burial place-->
                </div>
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <input class="form-control" name="burialPlace" id="burialPlace">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_notice_burialDate');?>  </label><!--Burial Date-->
                </div>

                <div class="form-group col-sm-3" style="margin-top: 5px;">
                    <div class='input-group date' id='datetimepickerdead'>
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i></div>
                        <input type='text' class="form-control" name="burialDate" id="burialDate">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<br>
            <div class="row">
                <div class="col-sm-1"></div>
                 <div class="form-group col-sm-4">
                    <div class="skin-section extraColumns">
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsgreen">
                                <label for="checkbox"><?php echo $this->lang->line('common_submit');?> &nbsp;&nbsp;</label>
                                <input id="publish" type="radio" data-caption="" class="columnSelected"
                                                 name="active" value="1">
                            </div>
                        </label>
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsgreen">
                                <label for="checkbox"><?php echo $this->lang->line('common_save_as_draft');?>&nbsp;&nbsp;</label>
                                <input id="draft" type="radio" data-caption="" class="columnSelected"
                                        name="active" value="0">
                            </div>
                        </label>
                    </div>
                 </div>
            </div>
            <div class="row">
                <div class="text-right m-t-xs">
                    <div class="form-group col-sm-10" style="margin-top: 10px;">
                        <button class="btn btn-primary" type="submit" id="save_btn"><?php echo $this->lang->line('common_submit');?> </button><!--Save-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>

    <script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>

    <script>

        $(document).ready(function () {

          //  var NoticeID;
            var NewNoticeForm = $('#NewNoticeForm');

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_saf_communityNoticeBoard', '', 'Announcements ');
            });

            noticeID = null;

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                noticeID = p_id;
                load_announcementDetails();
            }

             $('.select2').select2();

            $("#NoticeDescription").wysihtml5();
            $("#BayanDescription").wysihtml5();
            $("#DeathDescription").wysihtml5();

            $('#datetimepickerdead').datetimepicker();
            $('#datetimepickerbayan').datetimepicker();

            $('.extraColumnsgreen input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                radioClass: 'iradio_square_relative-green',
                increaseArea: '20%'
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
            Inputmask().mask(document.querySelectorAll("input"));

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            });

            $('.datepic_NoticeExpireDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            });

            $('.datepic_NoticePublishedDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            });

            NewNoticeForm.bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    NoticeTypeID: {validators: {notEmpty: {message: 'Required'}}},
                    NoticePublishedDate: {validators: {notEmpty: {message: 'Required.'}}},
                    noticestateID: {validators: {notEmpty: {message: 'Required.'}}},
                    NoticeExpireDate: {validators: {notEmpty: {message: 'Required.'}}}
                   },
            }).on('success.form.bv', function (e) {

                $('.saveBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var postData = $form.serializeArray();
                $.ajax({
                    type: 'post',
                    url: "<?php echo site_url('communityNgo/Save_new_notice') ?>",

                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/communityNgo/ngo_saf_communityNoticeBoard', '', 'Announcements');
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            });

        });

        $("#NoticeTypeID").change(function () {
            if (this.value == 1) {
                $('#BayanAnnouncement').addClass('hide');
                $('#DeathAnnouncement').removeClass('hide');
                $('#GeneralAnnouncement').addClass('hide');
            }else if (this.value == 2) {
                $('#BayanAnnouncement').removeClass('hide');
                $('#DeathAnnouncement').addClass('hide');
                $('#GeneralAnnouncement').addClass('hide');
            }else {
                $('#BayanAnnouncement').addClass('hide');
                $('#DeathAnnouncement').addClass('hide');
                $('#GeneralAnnouncement').removeClass('hide');
            }
        });

        function load_announcementDetails() {
            if (noticeID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'NoticeID': noticeID},
                    url: "<?php echo site_url('communityNgo/editNoticeAnnouncement'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#noticeId').val(data['master']['NoticeID']);

                            $('#NoticeTypeID').val(data['master']['NoticeTypeID']).change();
                            $('#NoticePublishedDate').val(data['master']['NoticePublishedDate']);

                            $('#NoticeExpireDate').val(data['master']['NoticeExpireDate']);
                            setTimeout(function () {
                                if (data['master']['isSubmited'] == 1) {
                                    $('#publish').iCheck('check');
                                }else if(data['master']['isSubmited'] == 0){
                                    $('#draft').iCheck('check');
                                } }, 500);

                            if (data['master']['NoticeTypeID'] == 1) {
                                $('#DeadPerson').val(data['master']['DeadPerson']);
                                $('#deathInformer').val(data['master']['NoticeInformer']);
                                $('#DeadPrsnFamilBCR').val(data['master']['DeadPrsnFamilBCR']);
                                $('#DeathDescription').val(data['master']['NoticeDescription']);
                                $('#burialPlace').val(data['master']['VenuePlace']);
                                $('#burialDate').val(data['master']['VenueDateTime']);

                            }else if(data['master']['NoticeTypeID'] == 2){

                                $('#bayanVenueDate').val(data['master']['VenueDateTime']);
                                $('#VenuePlace').val(data['master']['VenuePlace']);
                                $('#Speaker').val(data['master']['Speaker']);
                                $('#bayanOrganizer').val(data['master']['NoticeInformer']);
                                $('#BayanSubject').val(data['master']['NoticeSubject']);
                                $('#BayanDescription').val(data['master']['NoticeDescription']);

                            }else if(data['master']['NoticeTypeID'] == 3){

                                $('#NoticeSubject').val(data['master']['NoticeSubject']);
                                $('#NoticeDescription').val(data['master']['NoticeDescription']);

                            }
                            $('#save_btn').html('<?php echo $this->lang->line('common_update');?>');
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');

                    }
                });
                $.ajax({
                    type: 'POST',
                    url:"CommunityNgo/editNoticeAnnouncementGSDivision",
                    data:{'NoticeID': noticeID},
                    success:function(data){
                        $('#noticeGS_div').html(data);
                        $('.noticestateID').select2();
                        $('.noticestateID').on("select2:unselecting", function (e) {
                            var DropDownID = $(this).attr('id');
                            var OptionID = (e.params.args.data.id);
                            var waser = $('#'+DropDownID+' option[value="'+OptionID+'"]').text();
                        })
                    }
                });
            }
        }
    </script>
