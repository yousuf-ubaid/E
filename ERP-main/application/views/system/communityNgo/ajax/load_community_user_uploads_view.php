<?php
$companyID = $this->common_data['company_data']['company_id'];
$convertFormat = convert_date_format_sql();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_dtTime = date('Y-m-d H:i:s');

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('communityngo_lang', $primaryLanguage);
?>
    <style>
        /*! Modals v10.1.2 | (c) 2017 Chris Ferdinandi | MIT License | http://github.com/cferdinandi/modals */
        .videoModal{max-width:100%;padding:.5em 1em;visibility:hidden;z-index:2}@media (min-width:40em){.videoModal{max-width:98%}}.videoModal.active{display:block;height:100%;left:0;max-height:100%;overflow:auto;position:fixed;right:0;top:0;visibility:visible;-webkit-overflow-scrolling:touch}@media (min-width:30em){.videoModal.active{height:auto;left:3%;margin-left:auto;margin-right:auto;right:3%;top:50px}}@media (min-width:20em){.videoModal.active{left:20%;right:8%}.videoModal.active.videoModal-medium{width:35em}.videoModal.active.videoModal-small{width:25em}}.videoModal:focus{outline:none}.videoModal-bg{bottom:0;position:fixed;left:0;opacity:.9;right:0;top:0;z-index:1}  .close{color:gray;cursor:pointer;float:right;font-weight:700;font-size:1.5em;text-decoration:none}  .close:hover{color:#5a5a5a;cursor:pointer}

    </style>
    <style>

        .blink_div {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0.5;
            }
        }

    </style>
<?php
if(!empty($userUploadByDate)) {
    foreach ($userUploadByDate as $resReNd) {

        $publisedDat = input_format_date($resReNd['UploadPublishedDt'], $date_format_policy);

        ?>

        <div class="tab-pane" id="timelineBrd">
            <!-- The timeline -->
            <div class="box box-solid">
                <div class="box-header with-border small-box bg-teal" style="height:30px;">
                    <div class="" style="">
                        <p> <?php echo $resReNd['UploadPublishedDt']; ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-image" style="font-size:80px;" aria-hidden="true"></i>
                    </div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body" style="margin-top:-20px;">
                    <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">
                        <ul class="timeline timeline-inverse">
                            <!-- timeline for general -->
                            <?php
                            $events1List= $this->db->query("SELECT srp_erp_ngo_com_uploads.ComUploadID,familyUplaod_active, srp_erp_ngo_com_uploads.ComUploadType, (DATE_FORMAT(UploadPublishedDate,'{$convertFormat}'))as UploadPublishedDate, ComUploadSubject,ComUpload_url, ComUploadDescription, (DATE_FORMAT(ComUploadExpireDate,'{$convertFormat}'))as ComUploadExpireDate,  ComUploadSubmited,srp_erp_ngo_com_uploads.CreatedDate FROM srp_erp_ngo_com_uploads WHERE srp_erp_ngo_com_uploads.companyID = {$companyID} AND familyUplaod='1' AND ComUploadType='1' AND srp_erp_ngo_com_uploads.UploadPublishedDate ='".$publisedDat."' ORDER BY srp_erp_ngo_com_uploads.ComUploadID DESC ");
                            $resEvn1List = $events1List->result();
                            if(!empty($resEvn1List)){
                                foreach ($resEvn1List as $res1Elist) {

                                    $date1 = new DateTime($res1Elist->CreatedDate);
                                    $date2 = $date1->diff(new DateTime($current_dtTime));
                                    $upYears= $date2->y.' years'."\n";
                                    $upMonths= $date2->m.' months'."\n";
                                    $upDays = $date2->d.' days'."\n";
                                    $upHours = $date2->h.' hours'."\n";
                                    $upMin = $date2->i.' minutes'."\n";
                                    $upSec = $date2->s.' seconds'."\n";

                                    if($date2->y != 0 && $date2->m !=0 && $date2->d != 0 && $date2->h !=0 && $date2->i != 0 && $date2->s != 0){
                                        $get_tmDiff=  $upYears .$upMonths .$upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif($date2->y ==0 && $date2->m !=0){
                                        $get_tmDiff=  $upMonths .$upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y ==0 && $date2->m == 0) && $date2->d != 0){
                                        $get_tmDiff=  $upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y == 0 && $date2->m == 0 && $date2->d == 0) && ($date2->h !=0)){
                                        $get_tmDiff=  $upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y == 0 && $date2->m == 0 && $date2->d == 0 && $date2->h == 0) && $date2->i != 0){
                                        $get_tmDiff=  $upMin.$upSec .'ago';
                                    }
                                    else{
                                        $get_tmDiff=  $upSec .'ago';
                                    }
                                    ?>
                                    <?php if ($res1Elist->ComUploadType == 1) { ?>
                                        <li>
                                            <i class="fa fa-file-movie-o bg-green"></i>

                                            <div class="timeline-item">

                                                <div class="btn-group" role="group">
                                                    <button title="Edit" rel="tooltip" class="btn btn-xs btn-default" type="button"
                                                            onclick="com_user_uploadEdit(<?php echo $res1Elist->ComUploadID; ?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-danger" onclick="com_user_uploadDelete(<?php echo $res1Elist->ComUploadID; ?>)"
                                                            title="Delete" rel="tooltip" type="button">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <span class="time"><i class="fa fa-clock-o"></i>  <?php echo $get_tmDiff; ?></span>
                                                <h4 style="font-size: 12px;" class="timeline-header"><a href="#"><?php echo $res1Elist->ComUploadSubject; ?></a></h4>

                                                <div class="timeline-body">
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('communityngo_upload_url');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4"><a href="<?php echo $res1Elist->ComUpload_url; ?>" target="_blank"><?php echo $res1Elist->ComUpload_url; ?></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('communityngo_upload_publish');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td><?php echo $res1Elist->UploadPublishedDate; ?></td>
                                                            <td><strong><?php echo $this->lang->line('common_expire_date');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td><?php echo $res1Elist->ComUploadExpireDate; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('common_description');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4"><?php echo $res1Elist->ComUploadDescription; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('common_status');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4">
                                                                <?php if($res1Elist->ComUploadSubmited==1) {?>
                                                                    <span class="label label-success">Submitted for Public</span>
                                                                <?php } else{ ?>
                                                                    <span class="label label-warning">Save As Draft</span>
                                                                <?php }  ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6" style="color: transparent;">row space control</td>
                                                        </tr>
                                                        <tr style="text-align: center;">
                                                            <td colspan="6">

                                                        <span class="tipped-top"><a data-modal="#modal_upVideo" onclick="openUploadVideo_mod('<?php echo $res1Elist->ComUpload_url; ?>');"><img
                                                                    src="<?php echo base_url("images/community/videoImg2.jpg") ?>"></a></span>

                                                            </td>
                                                        </tr>
                                                        <tr style="text-align: center;"><td colspan="6" style="color: transparent;">hidden space</td></tr>
                                                        </tbody>
                                                        <tfoot style="border: none;"><tr style="text-align: center;"><td colspan="6">
                                                                <?php if($res1Elist->familyUplaod_active == 1) { ?>
                                                                    <div class="blink_div"><strong
                                                                            style=" color: green;font-weight:600;">
                                                                            Approved    </strong></div>
                                                                <?php } else{ ?>
                                                                    <div class="blink_div"><strong
                                                                            style=" color: red;font-weight:600;">
                                                                            Not Approved Yet    </strong></div>
                                                                <?php } ?>

                                                            </td> </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>
                                                <div class="timeline-footer">
                                                    <div class="text-muted" style="float:right;font-size: 12px;color: #c3774d;">Video</div>
                                                </div>
                                            </div>
                                        </li>

                                    <?php } ?>

                                <?php } ?>
                            <?php }   else{ ?>
                                <!-- END timeline for video-->
                                <li>No Videos Available</li>
                            <?php } ?>
                            <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">
                        <ul class="timeline timeline-inverse">
                            <!-- timeline for general -->
                            <?php
                            $eventsList= $this->db->query("SELECT srp_erp_ngo_com_uploads.ComUploadID,familyUplaod_active, srp_erp_ngo_com_uploads.ComUploadType, (DATE_FORMAT(UploadPublishedDate,'{$convertFormat}'))as UploadPublishedDate, ComUploadSubject,ComUpload_url, ComUploadDescription, (DATE_FORMAT(ComUploadExpireDate,'{$convertFormat}'))as ComUploadExpireDate,  ComUploadSubmited,srp_erp_ngo_com_uploads.CreatedDate FROM srp_erp_ngo_com_uploads WHERE srp_erp_ngo_com_uploads.companyID = {$companyID} AND familyUplaod='1' AND ComUploadType='2' AND srp_erp_ngo_com_uploads.UploadPublishedDate ='".$publisedDat."' ORDER BY srp_erp_ngo_com_uploads.ComUploadID DESC ");
                            $resEvnList = $eventsList->result();
                            if(!empty($resEvnList)){
                                foreach ($resEvnList as $resElist) {

                                    $date1 = new DateTime($resElist->CreatedDate);
                                    $date2 = $date1->diff(new DateTime($current_dtTime));
                                    $upYears= $date2->y.' years'."\n";
                                    $upMonths= $date2->m.' months'."\n";
                                    $upDays = $date2->d.' days'."\n";
                                    $upHours = $date2->h.' hours'."\n";
                                    $upMin = $date2->i.' minutes'."\n";
                                    $upSec = $date2->s.' seconds'."\n";

                                    if($date2->y != 0 && $date2->m !=0 && $date2->d != 0 && $date2->h !=0 && $date2->i != 0 && $date2->s != 0){
                                        $get_tmDiff=  $upYears .$upMonths .$upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif($date2->y ==0 && $date2->m !=0){
                                        $get_tmDiff=  $upMonths .$upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y ==0 && $date2->m == 0) && $date2->d != 0){
                                        $get_tmDiff=  $upDays.$upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y == 0 && $date2->m == 0 && $date2->d == 0) && ($date2->h !=0)){
                                        $get_tmDiff=  $upHours.$upMin.$upSec .'ago';
                                    }
                                    elseif(($date2->y == 0 && $date2->m == 0 && $date2->d == 0 && $date2->h == 0) && $date2->i != 0){
                                        $get_tmDiff=  $upMin.$upSec .'ago';
                                    }
                                    else{
                                        $get_tmDiff=  $upSec .'ago';
                                    }

                                    ?>
                                    <?php if ($resElist->ComUploadType == 2) { ?>
                                        <li>
                                            <i class="fa fa-file-audio-o bg-green"></i>

                                            <div class="timeline-item">
                                                <div class="btn-group" role="group">
                                                    <button title="Edit" rel="tooltip" class="btn btn-xs btn-default" type="button"
                                                            onclick="com_user_uploadEdit(<?php echo $resElist->ComUploadID; ?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-danger" onclick="com_user_uploadDelete(<?php echo $resElist->ComUploadID; ?>)"
                                                            title="Delete" rel="tooltip" type="button">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <span class="time"><i class="fa fa-clock-o"></i>  <?php echo $get_tmDiff; ?></span>

                                                <h4 style="font-size: 12px;" class="timeline-header"><a href="#"><?php echo $resElist->ComUploadSubject; ?></a></h4>


                                                <div class="timeline-body">
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('communityngo_upload_url');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4"><a href="<?php echo $resElist->ComUpload_url; ?>" target="_blank"><?php echo $resElist->ComUpload_url; ?></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('communityngo_upload_publish');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td><?php echo $resElist->UploadPublishedDate; ?></td>
                                                            <td><strong><?php echo $this->lang->line('common_expire_date');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td><?php echo $resElist->ComUploadExpireDate; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('common_description');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4"><?php echo $resElist->ComUploadDescription; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong><?php echo $this->lang->line('common_status');?></strong></td>
                                                            <td><strong>:</strong></td>
                                                            <td colspan="4">
                                                                <?php if($resElist->ComUploadSubmited==1) {?>
                                                                    <span class="label label-success">Submitted for Public</span>
                                                                <?php } else{ ?>
                                                                    <span class="label label-warning">Save As Draft</span>
                                                                <?php }  ?>
                                                            </td>
                                                        </tr>
                                                        <tr style="text-align: center;"><td colspan="6" style="color: transparent;">hidden space</td></tr>
                                                        </tbody>
                                                        <tfoot style="border: none;"><tr style="text-align: center;"><td colspan="6">
                                                                <?php if($resElist->familyUplaod_active == 1) { ?>
                                                                    <div class="blink_div"><strong
                                                                            style=" color: green;font-weight:600;">
                                                                            Approved    </strong></div>
                                                                <?php } else{ ?>
                                                                    <div class="blink_div"><strong
                                                                            style=" color: red;font-weight:600;">
                                                                          Not Approved Yet    </strong></div>
                                                                <?php } ?>

                                                            </td> </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <div class="timeline-footer">
                                                    <div class="text-muted" style="float:right;font-size: 12px;color: #c3774d;">Audio</div>
                                                </div>
                                            </div>
                                        </li>

                                    <?php }
                                    else{ ?>
                                        <!-- END timeline for Audio-->
                                        <div>No Audio Available</div>
                                    <?php } ?>
                                <?php } ?>
                            <?php }   else{ ?>
                                <!-- END timeline for Audio-->
                                <li>No Audios Available</li>
                            <?php } ?>
                            <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}else{ ?>

    <div>
        <font style="color: darkgrey;">No Data found </font>
    </div>

<?php } ?>

    <div class="modal videoModal" data-modal-window id="modal_upVideo">
        <iframe id="youtubeIframe" src="" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        <button data-modal-close>Close</button>
    </div>

    <script>

        function openUploadVideo_mod(ComUpload_url){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {ComUpload_url: ComUpload_url},
                url: "<?php echo site_url('CommunityNgo/load_user_uploadVideo'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#youtubeIframe').attr('src', data);

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        /*! Modals v10.1.2 | (c) 2017 Chris Ferdinandi | MIT License | http://github.com/cferdinandi/modals */
        !(function(e,t){"function"==typeof define&&define.amd?define([],t(e)):"object"==typeof exports?module.exports=t(e):e.modals=t(e)})("undefined"!=typeof global?global:this.window||this.global,(function(e){"use strict";var t,o,n,l={},c="querySelector"in document&&"addEventListener"in e&&"classList"in document.createElement("_"),r="closed",d={selectorToggle:"[data-modal]",selectorWindow:"[data-modal-window]",selectorClose:"[data-modal-close]",modalActiveClass:"active",modalBGClass:"modal-bg",preventBGScroll:!0,preventBGScrollHtml:!0,preventBGScrollBody:!0,backspaceClose:!0,stopVideo:!0,callbackOpen:function(){},callbackClose:function(){}},a=function(){var e={},t=!1,o=0,n=arguments.length;"[object Boolean]"===Object.prototype.toString.call(arguments[0])&&(t=arguments[0],o++);for(;o<n;o++){var l=arguments[o];!(function(o){for(var n in o)Object.prototype.hasOwnProperty.call(o,n)&&(t&&"[object Object]"===Object.prototype.toString.call(o[n])?e[n]=a(!0,e[n],o[n]):e[n]=o[n])})(l)}return e},s=function(e,t){for(Element.prototype.matches||(Element.prototype.matches=Element.prototype.matchesSelector||Element.prototype.mozMatchesSelector||Element.prototype.msMatchesSelector||Element.prototype.oMatchesSelector||Element.prototype.webkitMatchesSelector||function(e){for(var t=(this.document||this.ownerDocument).querySelectorAll(e),o=t.length;--o>=0&&t.item(o)!==this;);return o>-1});e&&e!==document;e=e.parentNode)if(e.matches(t))return e;return null},i=function(e,t){if(t.stopVideo&&e.classList.contains(t.modalActiveClass)){var o=e.querySelector("iframe"),n=e.querySelector("video");if(o){var l=o.src;o.src=l}n&&n.pause()}},u=function(){var e=document.createElement("div");e.style.visibility="hidden",e.style.width="100px",e.style.msOverflowStyle="scrollbar",document.body.appendChild(e);var t=e.offsetWidth;e.style.overflow="scroll";var o=document.createElement("div");o.style.width="100%",e.appendChild(o);var n=o.offsetWidth;return e.parentNode.removeChild(e),t-n},m=function(){if(!document.querySelector("[data-modal-bg]")){var e=document.createElement("div");e.setAttribute("data-modal-bg",!0),e.classList.add(n.modalBGClass),document.body.appendChild(e)}},p=function(){var e=document.querySelector("[data-modal-bg]");e&&document.body.removeChild(e)};l.closeModal=function(e){var t=a(n||d,e||{}),l=document.querySelector(t.selectorWindow+"."+t.modalActiveClass);l&&(i(l,t),l.classList.remove(t.modalActiveClass),p(),r="closed",t.preventBGScroll&&(document.documentElement.style.overflowY="",document.body.style.overflowY="",document.body.style.paddingRight=""),t.callbackClose(o,l),o&&(o.focus(),o=null))},l.openModal=function(e,c,s){var i=a(n||d,s||{}),u=document.querySelector(c);"open"===r&&l.closeModal(i),e&&(o=e),u.classList.add(i.modalActiveClass),m(),r="open",u.setAttribute("tabindex","-1"),u.focus(),i.preventBGScroll&&(i.preventBGScrollHtml&&(document.documentElement.style.overflowY="hidden"),i.preventBGScrollBody&&(document.body.style.overflowY="hidden"),document.body.style.paddingRight=t+"px"),i.callbackOpen(e,u)};var v=function(e,t,o){if(o)return e.removeEventListener("touchstart",a,!1),e.removeEventListener("touchend",s,!1),void e.removeEventListener("click",i,!1);if(t&&"function"==typeof t){var n,l,c,r,d,a=function(e){n=!0,l=e.changedTouches[0].pageX,c=e.changedTouches[0].pageY},s=function(e){r=e.changedTouches[0].pageX-l,d=e.changedTouches[0].pageY-c,Math.abs(r)>=7||Math.abs(d)>=10||t(e)},i=function(e){if(n)return void(n=!1);t(e)};e.addEventListener("touchstart",a,!1),e.addEventListener("touchend",s,!1),e.addEventListener("click",i,!1)}},f=function(e){var t=e.target,o=s(t,n.selectorToggle),c=s(t,n.selectorClose),d=s(t,n.selectorWindow),a=e.keyCode;if(a&&"open"===r)(27===a||n.backspaceClose&&(8===a||46===a))&&l.closeModal();else if(t){if(d&&!c)return;!o||a&&13!==a?"open"===r&&(e.preventDefault(),l.closeModal()):(e.preventDefault(),l.openModal(o,o.getAttribute("data-modal"),n))}};return l.destroy=function(){n&&(v(document,null,!0),document.removeEventListener("keydown",f,!1),document.documentElement.style.overflowY="",document.body.style.overflowY="",document.body.style.paddingRight="",t=null,o=null,n=null)},l.init=function(e){c&&(l.destroy(),n=a(d,e||{}),t=u(),v(document,f),document.addEventListener("keydown",f,!1))},l}));

        /**
         * Autoplay a YouTube, Vimeo, or HTML5 video
         * @param  {Node} modal  The modal to search inside
         */
        var autoplayVideo = function (modal) {

            // Look for a YouTube, Vimeo, or HTML5 video in the modal
            var video = modal.querySelector('iframe[src*="www.youtube.com"], iframe[src*="player.vimeo.com"], video');

            // Bail if the modal doesn't have a video
            if (!video) return;

            // If an HTML5 video, play it
            if (video.tagName.toLowerCase() === 'video') {
                video.play();
                return;
            }

            // Add autoplay to video src
            // video.src: the current video `src` attribute
            // (video.src.indexOf('?') < 0 ? '?' : '&'): if the video.src already has query string parameters, add an "&". Otherwise, add a "?".
            // 'autoplay=1': add the autoplay parameter
            video.src = video.src + (video.src.indexOf('?') < 0 ? '?' : '&') + 'autoplay=1';

        };

        /**
         * Stop a YouTube, Vimeo, or HTML5 video
         * @param  {Node} modal  The modal to search inside
         */
        var stopVideo = function (modal) {

            // Look for a YouTube, Vimeo, or HTML5 video in the modal
            var video = modal.querySelector('iframe[src*="www.youtube.com"], iframe[src*="player.vimeo.com"], video');

            // Bail if the modal doesn't have a video
            if (!video) return;

            // If an HTML5 video, pause it
            if (video.tagName.toLowerCase() === 'video') {
                video.pause();
                return;
            }

            // Remove autoplay from video src
            video.src = video.src.replace('&autoplay=1', '').replace('?autoplay=1', '');

        };

        modals.init({
            callbackOpen: function ( toggle, modal ) {
                autoplayVideo(modal);
            },
            callbackClose: function ( toggle, modal ) {
                stopVideo(modal);
            }
        });
    </script>

<?php
