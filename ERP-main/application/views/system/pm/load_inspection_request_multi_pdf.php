<?php
$date_format_policy = date_format_policy();
?>


<div style="padding: 5%">

    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;">
                <tr>
                    <td rowspan="2"  style="border: 1px solid black;border-collapse: collapse;width: 1%;background: white;vertical-align: center;border-right: none;border-bottom: none">
                        <div style="height: 75px; overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <img alt="Logo" style="height: 60px;width: 20%;" src="<?php echo htmlImage.$this->common_data['company_data']['company_logo']; ?>">
                        </div>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-left: none;"> <strong style="margin-left: 1%;font-size: 115%">TRADING & INVESTMENT ESTABLISHMENT</strong></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: center;border-left: none;border-bottom: none"><strong style="margin-left: 1%;font-size: 115%">INSPECTION REQUEST</strong></td>

                </tr>

            </table>
            <table>
                <tr>
                    <td  style="width: 12%;height: 28px;background: white;vertical-align: center;border-left: 1px solid black;border-top: none;border-right: none;border-top: none;border-bottom: none">
                        <strong style="width: 20%;margin-left: 5%;font-size: 115%">Project :</strong>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-right: none;border-bottom: none">
                            <label style="font-family:Arial, sans-serif"><?php echo $project?> </label>

                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-left: none;border-bottom: none"> <strong style="margin-left: -60%;font-size: 115%"> </strong></td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 19%">
                        <strong style="margin-left: 5%;font-size: 115%">Building Name :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRBN');?> </label>
                    </td>

                </tr>
            </table>

            <table>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 35%">
                        <strong style="margin-left: 5%;font-size: 115%">Subject/Item Of Inspection:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRINS');?></label>
                    </td>

                </tr>
            </table>
            <table>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 10%">
                        <strong style="margin-left: 5%;font-size: 115%;">Date :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-top: none;border-bottom: none">
                        <label style="font-family:Arial, sans-serif">
                        <?php
                        $convertFormat=convert_date_format();
                        $date = format_date(fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRDA'),$convertFormat);
                        echo $date
                        ?>
               </label>
                    </td>

                </tr>
                <tr>
                <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                    <strong style="margin-left: 5%;font-size: 115%">Time :</strong>
                </td>
                <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                    <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRTI');?></label>



                </td>

                </tr>
            </table>
            <table>
                <?php $type = fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRST');?>
                <tr>
                    <td  style="width: 30%; border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Approved :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <?php if($type == 'IRA') {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">&nbsp;
                        <?php }?>



                    </td>


                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Approved with Comments:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <?php if($type == 'IRAWC') {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">&nbsp;
                        <?php }?>

                    </td>

                </tr>
                <tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Rejected:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">
                        <?php if($type == 'IRR') {?>
                            <img src="<?php echo mPDFImage.'images/cheackedclearance.svg';?>"  style="height: 20px;">&nbsp;
                        <?php }?>


                    </td>

                </tr>

                </tr>

            </table>

            <table>
                <tr>
                    <td  style="width: 31%;border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">

                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <p><?php echo str_replace(PHP_EOL, '<br /> ', fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCOM'));?></p>
                    </td>
                </tr>

            </table>
            <table>
                <tr>
                    <td  style="width: 31%;border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                    <strong style="margin-left: 5%;font-size: 115%">Consultant Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">

                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <p><?php echo str_replace(PHP_EOL, '<br /> ',fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCONCOM'));?></p>
                    </td>
                </tr>

            </table>
            <table>
                <tr>
                    <td  style="width: 31%;border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Client Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                    </td>
                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">

                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">
                        <p><?php echo str_replace(PHP_EOL, '<br /> ',fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCLICOM'));?> </p>
                    </td>
                </tr>

            </table>
            <table>
                <tr>
                    <td  style="width: 12%;height: 28px;background: white;vertical-align: center;border-left: 1px solid black;border-top: none;border-right: none;border-top: none;border-bottom: none">
                        <strong style="width: 20%;margin-left: 5%;font-size: 115%"> </strong>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-right: none;border-bottom: none">
                        <label style="font-family:Arial, sans-serif">  </label>

                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-left: none;border-bottom: none"> <strong style="margin-left: -60%;font-size: 115%"> </strong></td>

                </tr>

            </table>

            <table>
                <tr>

                    <td colspan="3" style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Inspected By :</strong>
                    </td>



                </tr>

                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-right: none;border-bottom: none">
                        <strong style="font-weight: bold;margin-left: 6%;font-size: 110%">Main Contractor</strong>
                    </td>
                    <td  style="font-weight: bold; border: 1px solid black;border-collapse: collapse;padding-left: 15%;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-right: none;border-bottom: none">
                        <strong style="font-weight: bold;margin-left: 1%;font-size: 115%">Sub Contractor</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-bottom: none">
                        <strong style="font-weight: bold;margin-left: 1%;font-size: 115%">Client Representative</strong>
                    </td>


                </tr>
                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 6%;font-size: 115%">Signature :</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;padding-left: 15%;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Signature :</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Signature :</strong>
                    </td>


                </tr>
                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 6%;font-size: 115%">Name :</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;padding-left: 15%;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Name :</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Name :</strong>
                    </td>


                </tr>
                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-right: none">
                        <strong style="margin-left: 6%;font-size: 115%">Company Name :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;padding-left: 15%;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;">
                        <strong style="font-size: 115%">Company Name : </strong>
                    </td>
                </tr>

            </table>




        </div>
    </div>
    </div>
<script type="text/javascript">
    $(document).ready(function () {
        load_rec_records();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : 'HH:mm',
            widgetPositioning: {
                vertical: 'bottom'
            }
        });
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

    });
    </script>