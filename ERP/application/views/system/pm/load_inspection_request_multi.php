<?php
$date_format_policy = date_format_policy();
?>


<div style="padding: 5%">
    <?php echo form_open('', 'role="form" id="inspection_report"'); ?>
    <input type="hidden" class="" value="IRHEADER" name="tempElementKey" id="tempElementKey">
    <input type="hidden" class="" value="<?php echo $projectID?>" name="headerID" id="headerID">
    <input type="hidden" class="" value="<?php echo $headerID?>"  name="tempmasterID" id="tempmasterID">
    <input type="hidden" class="" value="IR"  name="tempkey" id="tempkey">

    <?php if($is_print == 'N') { ?>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12">
                <?php echo export_buttons('', 'Inspection Request', false, True,'btn-xs','generateReportPdf_inspection()'); ?>
            </div>
        </div>
    <?php } ?>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;">
                <tr>
                    <td rowspan="2"  style="border: 1px solid black;border-collapse: collapse;width: 1%;background: white;vertical-align: center;border-right: none;border-bottom: none">
                        <div style="height: 75px; overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <img alt="Logo" style="height: 60px;width: 60%;margin-top: -4%;" src="<?php echo htmlImage.$this->common_data['company_data']['company_logo']; ?>">
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
                    <td  style="height: 35px;background: white;vertical-align: center;border-left: 1px solid black;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Project :</strong>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-right: none;border-bottom: none"> <strong style="margin-left: -20%;font-size: 115%"><?php echo $project?></strong></td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 45%;height: 35px;border-left: none;border-top: none;border-left: none;border-bottom: none"> <strong style="margin-left: -60%;font-size: 115%"> </strong></td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 11%">
                        <strong style="margin-left: 5%;font-size: 115%">Building Name :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">
                        <input style="width: 30%; margin-left: 1%;" type="text" name="buildingname" value="<?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRBN');?>"  id="buildingname" class="form-control">
                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 17%">
                        <strong style="margin-left: 5%;font-size: 115%">Subject/Item Of Inspection:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <input style="width: 80%; margin-left: 1%;" type="text" name="subject" value="<?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRINS');?>" id="subject" class="form-control">
                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Date :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <?php
                        $convertFormat=convert_date_format();
                        $date = format_date(fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRDA'),$convertFormat);
                        ?>
                        <div class="input-group datepic" style="margin-left: -10%">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="inspectionrepdate" value="<?php echo $date?>" id="inspectionrepdate"  style="width: 120%;"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>


                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Time :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <div class="input-group datetimepicker4" style="margin-left: -10%;width: 24%;">
                            <input type="text" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRTI');?>" name="inspectionreptime" id="inspectionreptime"><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                        </div>


                    </td>

                </tr>
                <?php
                $type = fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRST');

                ?>

                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Approved :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">

                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumns">
                                    <input id="approved" type="radio" data-caption="" class="columnSelected"
                                           name="appradio" value="IRA" <?php echo ($type=='IRA')?'checked':'' ?>  >
                                    <label for="checkbox">&nbsp;&nbsp;</label></div>
                            </label>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;">
                        <strong style="margin-left: 5%;font-size: 115%">Approved with Comments:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none;">
                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumns">
                                    <input id="approvedwithcomme" type="radio" data-caption="" class="columnSelected"
                                           name="appradio" value="IRAWC" <?php echo ($type=='IRAWC')?'checked':'' ?>  >
                                    <label for="checkbox">&nbsp;&nbsp;</label></div>
                            </label>
                        </div>

                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Rejected:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumns">
                                    <input id="rejected" type="radio" data-caption="" class="columnSelected"
                                           name="appradio" value="IRR" <?php echo ($type=='IRR')?'checked':'' ?>>
                                    <label for="checkbox">&nbsp;&nbsp;</label></div>
                            </label>
                        </div>

                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none"">
                    <strong style="margin-left: 5%;font-size: 115%">Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <textarea style="width: 70%;" class="form-control" rows="3" name="comments" id="comments"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCOM');?></textarea>
                    </td>

                </tr>
                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">

                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                    </td>
                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none"">
                    <strong style="margin-left: 5%;font-size: 115%">Consultant Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <textarea style="width: 70%;" class="form-control" rows="3" name="consultantcomments" id="consultantcomments"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCONCOM');?></textarea>
                    </td>

                </tr>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">

                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                    </td>
                </tr>
                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none">
                        <strong style="margin-left: 5%;font-size: 115%">Client Comments :</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <textarea style="width: 70%;" class="form-control" rows="3" name="clientcomments" id="clientcomments"><?php echo fetch_boq_tempdetails($headerID,'IRHEADER','IR','IRCLICOM');?> </textarea>
                    </td>

                </tr>

                <tr>

                    <td colspan="3" style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Inspected By :</strong>
                    </td>



                </tr>

                <tr>

                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 6%;font-size: 115%">Main Contractor</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;padding-left: 15%;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-right: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Sub Contractor</strong>
                    </td>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-left: none;border-bottom: none">
                        <strong style="margin-left: 1%;font-size: 115%">Client Representative</strong>
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
                        <strong style="font-size: 115%">Company Name :</strong>
                    </td>
                </tr>


            </table>
        </div>
    </div>

</div>
</form>
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