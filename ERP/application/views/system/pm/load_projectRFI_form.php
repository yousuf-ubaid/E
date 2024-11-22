<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
?>
<br>
<style>

</style>
<div style="">
    <?php echo form_open('', 'role="form" id="rfi_frm" autocomplete="off"'); ?>
    <input type="hidden" class="" value="RFI_HEADER" name="tempElementKey" id="tempElementKey">
    <input type="hidden" class="" value="<?php echo $project?>" name="headerID" id="headerID">
    <input type="hidden" class="" value="<?php echo $headerID?>"  name="tempmasterID" id="tempmasterID">
    <input type="hidden" class="" value="RFI"  name="tempkey" id="tempkey">

    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php echo export_buttons('', 'REQUEST FOR INFORMATION ( RFI )', false, True,'btn-xs','generateReportPdf_rfi()'); ?>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-bottom: none">
                <tr>
                    <td rowspan="2" colspan="3" style="width: 10%;background: white;vertical-align: center;
                        border: 1px solid black; border-collapse: collapse;border-bottom: none; text-align: right; padding-right: 10px">
                        <div style="height: 90px; overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <img alt="Logo" style="height: 70px;width: 18%;margin-top: -1%;" src="<?php echo htmlImage.$this->common_data['company_data']['company_logo']; ?>">
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <div style="border: 1px solid; background: #f9f0e8; width: 100%">
                <h3 style="margin-bottom: 5px; margin-top: 5px">REQUEST FOR INFORMATION  ( RFI )</h3>
            </div>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table style="width: 100%; border: 1px solid; border-top: none; border-collapse: collapse; text-align: left;">
                <tr>
                    <td rowspan="3" style="width: 50%"> &nbsp; To: <?=$projectData['customerName']?></td>
                    <td style="width: 15%">RFI NO</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 38%;height: 35px"><?=$documentCode;?></td>
                </tr>
                <tr>
                    <td style="width: 15%">DATE</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 38%; height: 35px">
                        <?php
                        $convertFormat=convert_date_format();
                        $date = format_date(fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIDAT'),$convertFormat);
                        ?>
                        <div class="input-group date_pic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="rfi_date" value="<?=$date?>" id="rfi_date"  style="width: 35%;"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?=$date_format_policy ?>'" required>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15%">RFI DISCIPLINE</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 38%; height: 35px">
                        <input type="text" name="rfi_decipline" id="rfi_decipline" class="form-control"  style="width: 90%;"
                               value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIDEC')?>">
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table>
                <tr style="background: #f9cab4f5; border: 1px solid; border-top: none;">
                    <th style="text-align: center; width: 25%">PROJECT</th>
                    <th style="text-align: center; width: 25%"">CLIENT</th>
                    <th style="text-align: center; width: 25%"">CONSULTANT</th>
                    <th style="text-align: center; width: 25%"">CONTRACTOR</th>
                </tr>
                <tr style="background: #f9f0e8; border: 1px solid; border-top: none;">
                    <td><?= $projectData['projectDescription'] ?></td>
                    <td></td>
                    <td style="text-align: center">
                        <input type="text" name="rfi_consultant" id="rfi_consultant" class="form-control"  style="width: 90%;"
                               value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICONS')?>"/>
                    </td>
                    <td style="text-align: center">
                        <input type="text" name="rfi_contractor" id="rfi_contractor" class="form-control"  style="width: 90%;"
                               value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICONT')?>"/>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center; margin-top: 10px">
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left">
                        &nbsp; <b>Subject</b> :
                        <?php $subject_txt = fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFISUB');?>
                        <textarea name="rfi_subject" id="rfi_subject" rows="3" style="width: 98%; margin-left: 5px; margin-right: 5px"><?=$subject_txt?></textarea>
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid;">
                        &nbsp; Date Info. Required
                        <?php
                        $convertFormat=convert_date_format();
                        $date = format_date(fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIDATINF'),$convertFormat);
                        ?>
                        <div class="input-group date_pic" style="margin-left: 5px">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="rfi_dateInfo" value="<?=$date?>" id="rfi_dateInfo"  style="width: 100px;"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?=$date_format_policy ?>'" required>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="">&nbsp;</td>
                    <td style="border-left: 1px solid"></td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <div class="col-sm-12" style="border: 1px solid; border-top: none">
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICIV'))? 'checked': ''; ?>
                    <input type="checkbox" name="rfi_civil" id="rfi_civil" <?=$is_checked?>> CIVIL
                </div>
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIARC'))? 'checked': ''; ?>
                    <input type="checkbox" name="rfi_architectural" id="rfi_architectural" <?=$is_checked?>> ARCHITECTURAL
                </div>
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIMEC'))? 'checked': ''; ?>
                    <input type="checkbox" name="rfi_mechanical" id="rfi_mechanical" <?=$is_checked?>> MECHANICAL
                </div>
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIELE'))? 'checked': ''; ?>
                    <input type="checkbox" name="rfi_electrical" id="rfi_electrical" <?=$is_checked?>> ELECTRICAL
                </div>
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIPLU'))? 'checked': ''; ?>
                    <input type="checkbox" name="rfi_plumbing" id="rfi_plumbing" <?=$is_checked?>> PLUMBING
                </div>
                <div class="col-sm-3">
                    <?php $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIOTH'))? 'checked': '';
                    $containerCls = ($is_checked == 'checked')? '': 'display: none';
                    //echo ' :'. $containerCls;
                    ?>
                    <input type="checkbox" name="rfi_others" id="rfi_others" <?=$is_checked?> onchange="changeOther()"> OTHERS (Pl.specify)
                </div>

                <div class="col-sm-5" id="other-specify-container" style="<?=$containerCls?>">
                    <input type="text" name="rfi_othersSpecify" id="rfi_othersSpecify" class="form-control" placeholder="Other specify field"
                           value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIOTHSPC')?>"/>
                </div>
            </div>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table style="width: 100%; border: 1px solid black; border-top: none; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        &nbsp; Contractor Signature :
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date :
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table style="width: 100%; border: 1px solid black; border-top: none; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 20%; text-align: left; vertical-align: bottom">
                        &nbsp; Received By L&V :
                    </td>
                    <td style="width: 30%; text-align: left; vertical-align: bottom">
                        <input type="text" name="receivedbylv" id="receivedbylv" class="form-control"
                               value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIRBLV')?>"/>
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date & Time :
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <div style="border: 1px solid; border-top: none; height: 95px; text-align: left">
                &nbsp; Response <br/>
                <?php $reponse_txt = fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIRES');?>
                <textarea name="rfi_response" id="rfi_response" rows="3" style="width: 98%; margin-left: 5px; margin-right: 5px"><?= $reponse_txt?></textarea>
            </div>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table style="width: 100%; border: 1px solid black; border-top: none; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 20%; text-align: left; vertical-align: bottom">
                        &nbsp; Signed By L&V :
                    </td>
                    <td style="width: 30%; text-align: left; vertical-align: bottom">
                        <input type="text" name="signedbylv" id="signedbylv" class="form-control"
                               value="<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFISBLV')?>"/>
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date :
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-12" style="text-align: center;">
            <table style="width: 100%; border: 1px solid black; border-top: none; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        &nbsp; Received By Contractor :
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date & Time :
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php echo form_close();?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

    });

    function changeOther(){
        $('#other-specify-container').hide();

        if($('#rfi_others').prop("checked")){
            $('#other-specify-container').show();
        }
        else {
            $('#rfi_othersSpecify').val('');
        }
    }


</script>
