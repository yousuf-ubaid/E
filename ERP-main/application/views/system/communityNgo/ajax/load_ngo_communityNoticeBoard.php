<?php
$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('communityNgo_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$convertdateformat = convert_date_format_sql();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
if ($output['noticeDate']) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">

        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-sm-12" id="timeline">
            <!-- The timeline -->
            <?php
            $b = 1;
            foreach ($output['noticeDate'] as $var){
            $publisedDat = input_format_date($var['NoticePublishedDate'], $date_format_policy);
            ?>

            <ul class="timeline timeline-inverse">
                    <li class="time-label">
                        <span class="bg-green">
                         <?php echo $var['NoticePublishedDate'];?>
                        </span>
                    </li>
                <?php $b ++; ?>


                    <?php
            $aa = 1;
                $date = $output['validateDate'];
                $type = $output['validateType'];
                $eventsList = $this->db->query("SELECT srp_erp_ngo_com_noticeboard.NoticeID, srp_erp_ngo_com_noticeboard.NoticeTypeID, (DATE_FORMAT(NoticePublishedDate,'{$convertdateformat}'))as NoticePublishedDate, NoticeSubject, DeadPerson, DeadPrsnFamilBCR, Speaker, NoticeInformer, VenuePlace, VenueDateTime, NoticeDescription, (DATE_FORMAT(NoticeExpireDate,'{$convertdateformat}'))as NoticeExpireDate,  isSubmited,srp_erp_ngo_com_noticeboardmaster.NoticeTypeID,srp_erp_ngo_com_noticeboardmaster.NoticeType,srp_erp_ngo_com_noticeboard.CreatedDate FROM srp_erp_ngo_com_noticeboard  INNER JOIN srp_erp_ngo_com_noticeboardmaster ON srp_erp_ngo_com_noticeboardmaster.NoticeTypeID = srp_erp_ngo_com_noticeboard.NoticeTypeID WHERE srp_erp_ngo_com_noticeboard.NoticePublishedDate ='".$publisedDat."' $date $type ORDER BY srp_erp_ngo_com_noticeboard.NoticeID DESC ")->result_array();
                if (!empty($eventsList)) {
                foreach ($eventsList as $item) {
                    ?>
                    <li>
                        <?php if($item['NoticeTypeID'] == 1){?>
                        <i class="fa fa-bed bg-red"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header"><a href="#"><?php echo $item['NoticeType'];?></a> </h3>
                            <div class="timeline-body" id="noticeContent">
                                        <table>
                        <?php if($item['NoticeExpireDate'] >= $current_date){?>
                                            <a class="btn btn-success btn-xs pull-right" onclick="addAttachment('<?php echo $item['NoticeID'] ?>')"><?php echo $this->lang->line('common_add_attachments');?></a>
                            <?php } else {}?>
                                            <tbody>
                                            <tr>
                                                <td style="width: 15%;"><strong><?php echo $this->lang->line('communityngo_notice_deadperson');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td style="width:80%;"><strong><?php echo $item['DeadPerson']; ?></strong></td>
                                            </tr>
                        <?php if($item['DeadPrsnFamilBCR'] !== ''){?>
                                            <tr>
                                                <td style="width: 15%;"><strong><?php echo $this->lang->line('communityngo_notice_deadFamily');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td style="width:80%;"><?php echo $item['DeadPrsnFamilBCR']; ?></td>
                                            </tr>
                            <?php } if($item['NoticeDescription'] !== ''){ ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_description');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td style="width:80%;"><?php echo $item['NoticeDescription'];?></td>
                                            </tr>
                            <?php } if($item['VenuePlace'] !== '' OR $item['VenueDateTime'] !== ''){ ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_burialDetails');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['VenuePlace']; ?><br><?php echo $item['VenueDateTime']; ?></td>
                                            </tr>
                            <?php } ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_deadInformer');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeInformer']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_expire_date');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeExpireDate']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_attachments');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><a class="" id="loadAttachmentLabel<?php echo $item['NoticeID'] ?>" onclick="loadattachments('<?php echo $item['NoticeID'] ?>')">Load Attachment</a>
                                                    <div id="show_all_attachments_<?php echo $item['NoticeID'] ?>"></div></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php }else if($item['NoticeTypeID'] == 2){?>
                                <i class="fa fa-users bg-blue"></i>
                                <div class="timeline-item">
                                <h3 class="timeline-header"><a href="#"><?php echo $item['NoticeSubject'];?></a> </h3>
                                <div class="timeline-body" id="noticeContent">
                                        <table>
                                            <?php if($item['NoticeExpireDate'] >= $current_date){?>
                                                <a class="btn btn-success btn-xs pull-right" onclick="addAttachment('<?php echo $item['NoticeID'] ?>')"><?php echo $this->lang->line('common_add_attachments');?></a>
                                            <?php } else {}?>
                                            <tbody>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_bayanSpeaker');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['Speaker']; ?></td>
                                            </tr>
                        <?php  if($item['NoticeDescription'] !== ''){ ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_description');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeDescription']; ?></td>
                                            </tr>
                        <?php } if($item['NoticeInformer'] !== ''){ ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_bayanOrganizer');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeInformer']; ?></td>
                                            </tr>
                            <?php } ?>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_venue');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['VenuePlace']; ?><br><?php echo $item['VenueDateTime']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_attachments');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><a class="" id="loadAttachmentLabel<?php echo $item['NoticeID'] ?>" onclick="loadattachments('<?php echo $item['NoticeID'] ?>')">Load Attachment</a>
                                                    <div id="show_all_attachments_<?php echo $item['NoticeID'] ?>"></div></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php }else if($item['NoticeTypeID'] == 3){?>
                                    <i class="fa fa-bullhorn bg-yellow"></i>
                                    <div class="timeline-item">
                                    <h3 class="timeline-header"><a href="#"><?php echo $item['NoticeSubject'];?></a> </h3>
                                    <div class="timeline-body" id="noticeContent">
                                        <table>
                                            <?php if($item['NoticeExpireDate'] >= $current_date){?>
                                                <a class="btn btn-success btn-xs pull-right" onclick="addAttachment('<?php echo $item['NoticeID'] ?>')"><?php echo $this->lang->line('common_add_attachments');?></a>
                                            <?php } else {}?>
                                            <tbody>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('communityngo_notice_subject');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeSubject']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_expire_date');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeExpireDate']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_description');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><?php echo $item['NoticeDescription']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo $this->lang->line('common_attachments');?></strong></td>
                                                <td><strong>:</strong></td>
                                                <td><a class="" id="loadAttachmentLabel<?php echo $item['NoticeID'] ?>" onclick="loadattachments('<?php echo $item['NoticeID'] ?>')">Load Attachment</a>
                                                    <div id="show_all_attachments_<?php echo $item['NoticeID'] ?>"></div></td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    <?php } ?>

                                </div>
                                <?php if($item['isSubmited'] == 1){ ?>
                                        <div class="timeline-item">
                                    <div class="timeline-footer">
                                        <a class="btn btn-danger btn-xs"><?php echo $this->lang->line('common_delete');?></a>
                                    </div>
                                <?php } else{?>
                                    <div class="timeline-footer">
                                        <a class="btn btn-primary btn-xs" onclick="fetchPage('system/communityNgo/ngo_saf_newNotice',<?php echo $item['NoticeID'] ?>,'Edit announcement','Announcement')"><?php echo $this->lang->line('common_edit');?></a>
                                        <a class="btn btn-danger btn-xs" onclick="deleteAnnouncement('<?php echo $item['NoticeID'] ?>')"><?php echo $this->lang->line('common_delete');?></a>
                                    </div>
                                <?php }?>
                            </div>
                        </li>
                    <?php $aa++;
                } ?>
                    <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                    </ul>
<br>
           <?php }} ?>
        </div>

    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
    <ul class="timeline timeline-inverse">

        <li>
            <div class="timeline-item">
                <h3 class="timeline-header no-border"><center>No Announcements Available</center></h3>
            </div>
        </li>

    </ul>

    <?php
}
?>

<script>

    function loadattachments(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {NoticeID: id},
            url: "<?php echo site_url('communityNgo/load_noticeboard_attachments'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#loadAttachmentLabel'+id).addClass('hide');
                $('#show_all_attachments_' + id).html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>
