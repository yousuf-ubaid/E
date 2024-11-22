<?php
$date_format_policy = date_format_policy();
?>
<div style="padding: 5%;">

    <?php echo form_open('', 'role="form" id="project_summary"'); ?>
    <input type="hidden" class="" value="PROJECTSUMHEADER" name="tempElementKey" id="tempElementKey">
    <input type="hidden" class="" value="<?php echo $headerID?>" name="headerID" id="headerID">
    <input type="hidden" class="" value="<?php echo $detailid?>"  name="tempmasterID" id="tempmasterID">
    <input type="hidden" class="" value="PS"  name="tempkey" id="tempkey">

    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php echo export_buttons('', 'PROJECT SUMMARY', false, True,'btn-xs','generateReportPdf_prosum()'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-right: none;border-left: none;border-bottom: none;border-top:none;"> <strong style="margin-left: 1%;font-size: 125%"><U>PROJECT SUMMARY </U></strong></td>
                </tr>

            </table>
            <br>

            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 10%;text-align: left;border-left: none;"> &nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="width: 31%;">&nbsp;</td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 14%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 125%">	Project No:</strong></td>
                    <td style="width: 23%;">
                        <input style="width: 68%; margin-left: 3%;" type="text"  name="projectno" id="projectno" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSPRONO');?>">

                    </td>




                </tr>
            </table>

            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 10%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 125%">Project Location:</strong></td>
                    <td><input style="width: 80%; margin-left: 3%;" type="text"  name="projectsummarylocaltion" id="projectsummarylocaltion" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSPL');?>"></td>
                    <td style="width: 31%;">&nbsp;</td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 14%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 125%">Completion of Project:</strong></td>
                    <td style="width: 23%;">
                        <div class="input-group datepic" style="margin-left: 2%">

                            <?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSCOP'),$convertFormat);
                            ?>
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="completionproject" value="<?php echo $date?>" id="completionproject"  style="width: 65%;"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>

                    </td>
                   <!-- <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left;border-left: none;"> <strong style="margin-left: -30%;font-size: 125%">Completion of Project:</strong></td>-->
                </tr>
            </table>

            <table style="width: 60%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-top: none;border-left: none;"> <strong style="margin-left: 1%;font-size: 125%">&nbsp;</strong></td>
                    <td colspan="2" style=";height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-left: none;"> <strong style="margin-left: 1%;font-size: 125%">Amount(RO)</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 125%">Total Approved Amount</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="totalapprovedamount1" id="totalapprovedamount1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSTAA1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="totalapprovedamount2" id="totalapprovedamount2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSTAA2');?>"></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 125%">Civil + MEP</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="civilmep1" id="civilmep1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSCIM1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left">  <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="civilmep2" id="civilmep2" class="form-control"  value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSCIM2');?>"></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 125%">Salaries</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"><input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="salaries1" id="salaries1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSSAL1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"><input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="salaries2" id="salaries2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSSAL2');?>"></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 125%">Others</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="Others1" id="Others1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSOTH1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="Others2" id="Others2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSOTH2');?>"> </td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 125%">Handling Charges</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="handlingchar1" id="handlingchar1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSHC1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="handlingchar2" id="handlingchar2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSHC2');?>"> </td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 125%">Total Claimed Amount</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="totalclaimed1" id="totalclaimed1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSTOTCL1');?>"></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left">  <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="totalclaimed2" id="totalclaimed2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSTOTCL2');?>"></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 125%">Savings</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="savings1" id="savings1" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSSAV1');?>"> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="savings2" id="savings2" class="form-control" value="<?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSSAV2');?>"></td>

                </tr>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 125%">Remarks / Notes:</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 125%"><u>1 Major Problems Encountered:</u></strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><textarea class="form-control" rows="8" name="majorprobenc" id="majorprobenc"><?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSMP');?> </textarea>

                    </td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 125%"><u>2 Other Insights / Recommendations:</u></strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><textarea class="form-control" rows="8" name="recommendation" id="recommendation"><?php echo fetch_boq_tempdetails($detailid,'PROJECTSUMHEADER','PS','PSRD');?> </textarea>

                    </td>
                </tr>
            </table>
            <br>
            <br>
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;">

                <tr>
                    <td style="height: 70px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 3%;text-align: left"> </td>
                </tr>

            </table>

            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
            <tr>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 125%">Prepared By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 125%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 125%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 125%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 125%">Approved By :</strong> </td>
            </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 125%">Office Assistant</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 125%">Team Leader</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 125%"> Finance Manager</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 125%">Auditor</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 125%"> </label> </td>
                </tr>
            </table>

        </div>
    </div>
</div>
</form>
<div class="row" style="margin-top: -4%">
    <div class="col-sm-11">
        <div class="text-right m-t-xs"><button class="btn btn-primary next" onclick="save_projectsummary();">Save</div>
    </div>
</div>



<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <header class="head-title">
            <h2>completion Certificate ATTACHMENT</h2>
        </header>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="ccattachment"> </div>
    </div>
</div>


<script type="text/javascript">
    var search_id = 1;
    $(document).ready(function () {
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

    });


    function validateFloatKeyPress(el, evt) {
//alert(currency_decimal);
        currency_decimal = 3;
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
//just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
//get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

</script>
