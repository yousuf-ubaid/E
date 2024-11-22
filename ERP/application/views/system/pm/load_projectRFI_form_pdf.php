<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
?>
<br>
<style>

</style>
    <div class="row">
        <div class="col-sm-12">
                <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-bottom: none">
                    <tr>
                        <td rowspan="2" colspan="3" style="height: 90px;width: 10%;background: white;vertical-align: center;
                        border: 1px solid black; border-collapse: collapse;border-right: none ;border-bottom: none; text-align: right; padding-right: 10px">

                                <img alt="Logo" style="height: 70px;width: 18%;margin-top: -1%;" src="<?php echo htmlImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>

                    </tr>
                    <tr>
                        <td>


                        </td>
                    </tr>
                </table>

            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td style="text-align: center;background: #f9f0e8" >
                        <h4 style="margin-bottom: 5px; margin-top: 5px">REQUEST FOR INFORMATION  ( RFI )</h4>
                    </td>
                </tr>
            </table>


            <table>
                <tr>
                    <td style="width: 5%;background: white;vertical-align: center;border-left: 1px solid black;border-right: none;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">&nbsp;</strong>
                    </td>
                    <td colspan="2" style="width: 5%;border: 1px solid black;border-collapse: collapse;text-align: left;border-right: none ;border-left: none;border-top: none;border-right: none;border-bottom: none">&nbsp;</td>


                    <td style="width: 15%;background: white;vertical-align: center;border-left: 1px solid black;border-left: none ;border-right: none;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">RFI NO:</strong>
                    </td>
                    <td style="width: 20%;border: 1px solid black;border-collapse: collapse;text-align: left;border-left: none;border-top: none;border-bottom: none"> <strong style="font-size: 115%"><?=$documentCode;?> </td>


                </tr>
                <tr>
                    <td  style="width: 5%;border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 11%">
                        <strong style="font-size: 115%">To :</strong>
                    </td>
                    <td colspan="2" style="width: 5%; border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-right: none; border-top: none;border-bottom: none">
                        <?=$projectData['customerName']?>
                    </td>
                    <td style="background: white;vertical-align: center;border-left: 1px solid black;border-left: none ;border-right: none;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">Date:</strong>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;border-left: none;border-top: none;border-bottom: none">

                        <label style="font-family:Arial, sans-serif"><?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIDAT'),$convertFormat);
                            echo $date?> </label>

                    </td>
                </tr>
                <tr>
                    <td style="width: 5%;background: white;vertical-align: center;border-left: 1px solid black;border-right: none;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">&nbsp;</strong>
                    </td>
                    <td colspan="2" style="width: 5%;border: 1px solid black;border-collapse: collapse;text-align: left;border-right: none ;border-left: none;border-top: none;border-right: none;border-bottom: none">&nbsp;</td>


                    <td style="width: 25%;background: white;vertical-align: center;border-left: 1px solid black;border-left: none ;border-right: none;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">RFI DISCIPLINE:</strong>
                    </td>
                    <td style="width: 20%;border: 1px solid black;border-collapse: collapse;text-align: left;border-left: none;border-top: none;border-bottom: none">
                        <label style="font-family:Arial, sans-serif">
                        <?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIDEC')?></label>



                    </td>


                </tr>

            </table>

            <table class="table" style="width: 100%;border: 1px solid black; border-collapse: collapse;">
                <thead class="thead">
                <tr style="">
                    <th style="text-align: center; width: 25%;background: #f9cab4f5;">PROJECT</th>
                    <th style="text-align: center; width: 25%;background: #f9cab4f5;">CLIENT</th>
                    <th style="text-align: center; width: 25%;background: #f9cab4f5;">CONSULTANT</th>
                    <th style="text-align: center; width: 25%;background: #f9cab4f5;">CONTRACTOR</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="background: #f9f0e8">
                        <?= $projectData['projectDescription']?>
                    </td>
                    <td  style="background: #f9f0e8">

                    </td>
                    <td style="text-align: center;background: #f9f0e8">
                        <?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICONS')?>
                    </td>
                    <td style="text-align: center;background: #f9f0e8">
                        <?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICONT')?>
                    </td>
                </tr>
                </tbody>
            </table>

            <br>
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left">
                        &nbsp; <b>Subject</b> : <br>
                        <?php $subject_txt = fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFISUB');?>
                        <label style="font-family:Arial, sans-serif"><?= str_replace(PHP_EOL, '<br /> ', $subject_txt)?></label>
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid;">
                        &nbsp; Date Info. Required


                        <label style="font-family:Arial, sans-serif"><?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails(2,'RFI_HEADER','RFI','RFIDAT'),$convertFormat);
                            echo $date?> </label>

                    </td>
                </tr>
                <tr>
                    <td style="">&nbsp;</td>
                    <td style="border-left: 1px solid"></td>
                </tr>
            </table>
            <table style="width: 100%; border: 1px solid black;border-top: none ;border-collapse: collapse;">
                <tr style="height: 50px">
                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFICIV'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;CIVIL</th>

                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIARC'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;ARCHITECTURAL</th>

                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIMEC'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;MECHANICAL</th>

                    </th>
                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIELE'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;ELECTRICAL</th>
                </tr>
                <tr style="height: 50px">
                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIPLU'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;PLUMBING</th>

                    </th>
                    <th style="text-align: center; width: 25%;">
                        <?php
                        $img_sign = '';
                        $is_checked = (fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIOTH'));
                        if($is_checked==1) {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">
                        <?php }?>&nbsp;OTHERS (Pl.specify)
                    <p >
                        <?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIOTHSPC')?>
                    </p>

                    </th>

                    </th>
                </tr>
            </table>
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-top: none">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        &nbsp; Contractor Signature :
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date :
                    </td>
                </tr>
            </table>
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-top: none">
                <tr style="height: 50px">
                <td style="width: 50%; text-align: left; vertical-align: bottom">
                    &nbsp; Received By L&V :<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIRBLV')?>
                </td>
                <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                    &nbsp; Date & Time :
                </td>
                </tr>
            </table>

            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-top: none">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        Response
                    </td>
                </tr>
                <tr style="height: 50px">
                    <td>
                        <p> <?php echo str_replace(PHP_EOL, '<br /> ', fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFIRES'))?>
                        </p>
                    </td>

                </tr>
            </table>
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-top: none">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        &nbsp; Signed By L&V :<?=fetch_boq_tempdetails($headerID,'RFI_HEADER','RFI','RFISBLV')?>
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        &nbsp; Date :
                    </td>
                </tr>
            </table>
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-top: none">
                <tr style="height: 50px">
                    <td style="width: 50%; text-align: left; vertical-align: bottom">
                        &nbsp;Received By Contractor :
                    </td>
                    <td style="width: 50%; text-align: left; border-left: 1px solid; vertical-align: bottom">
                        Date & Time :
                    </td>
                </tr>
            </table>

        </div>
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
