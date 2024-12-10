<div style="padding: 5%;">
    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-right: none;border-left: none;border-bottom: none;border-top:none;"> <strong style="margin-left: 1%;font-size: 135%"> <U>PROJECT SUMMARY</U></strong></td>
                </tr>

            </table>
            <br>

          <!--  <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 8%;text-align: center;border-left: none;"> <strong style="font-size: 100%">&nbsp;</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left;border-left: none;"> <strong style="font-size: 100%">Project No:</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 40%;text-align: left;border-left: none;"> <strong style="font-size: 100%">Project Location:</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left;border-left: none;"> <strong style="font-size: 100%">Completion of Project:</strong></td>
                </tr>
            </table>-->
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 10%;text-align: left;border-left: none;"> &nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="width: 31%;">&nbsp;</td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 14%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 110%">	Project No:</strong></td>
                    <td style="width: 23%;">
                        <label style="font-family:Arial, sans-serif;font-size: 110%"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSPRONO');?></label>
                    </td>




                </tr>
            </table>
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 20%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 110%">Project Location:</strong></td>
                    <td>  <label style="font-family:Arial, sans-serif;font-size: 110%"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSPL');?></label></td>
                    <td style="width: 31%;">&nbsp;</td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 25%;text-align: left;border-left: none;"> <strong style="margin-left:1%;font-size: 110%">Completion of Project:</strong></td>
                    <td style="width: 23%;">
                        <label style="font-family:Arial, sans-serif;font-size: 110%">
                            <?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSCOP'),$convertFormat);
                            echo $date;
                            ?>
                        </label>
                    </td>
                    <!-- <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left;border-left: none;"> <strong style="margin-left: -30%;font-size: 125%">Completion of Project:</strong></td>-->
                </tr>
            </table>

            <table style="width: 80%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-top: none;border-left: none;"> <strong style="margin-left: 1%;font-size: 125%">&nbsp;</strong></td>
                    <td colspan="2" style=";height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: center;border-left: none;"> <strong style="margin-left: 1%;font-size: 100%">Amount(RO)</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 100%">Total Approved Amount</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSTAA1');?></label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSTAA2');?></label> </td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 100%">Civil + MEP</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">
                        <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSCIM1');?></label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">   <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSCIM2');?></label> </td>

                </tr>

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 100%">Salaries</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">  <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSSAL1');?></label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">   <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSSAL2');?></label></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 100%">Others</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSOTH1');?></label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSOTH2');?></label></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <label style="font-weight: 500;margin-left: 1%;font-size: 100%">Handling Charges</label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">  <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSHC1');?></label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">  <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSHC2');?></label></td>

                </tr>

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 100%">Total Claimed Amount</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">  <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSTOTCL1');?></label></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right">   <label style="font-family:Arial, sans-serif"> <?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSTOTCL2');?></label></td>

                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: left"> <strong style="margin-left: 1%;font-size: 100%">Savings</strong></td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSSAV1');?></label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;width: 3%;text-align: right"> <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSSAV2');?></label></td>

                </tr>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 100%">Remarks / Notes:</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 100%"><u>1 Major Problems Encountered:</u></strong></td>
                </tr>
                <tr>
                    <td>
                        <p><?php echo str_replace(PHP_EOL, '<br /> ',  fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSMP'));?> </p>
                    </td>

                </tr>

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 100%"><u>2 Other Insights / Recommendations:</u></strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left">
                        <p><?php echo str_replace(PHP_EOL, '<br /> ',  fetch_boq_tempdetails($headerID,'PROJECTSUMHEADER','PS','PSRD'));?></p>

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
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 100%">Prepared By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 100%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 100%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 100%">Checked By :</strong> </td>
                <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;width: 3%;text-align: left"><strong style="margin-left:1%;font-size: 100%">Approved By :</strong> </td>
            </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 100%">Office Assistant</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 100%">Team Leader</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 100%"> Finance Manager</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;border-right: none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 100%">Auditor</label> </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-left: none;border-top:none;width: 3%;text-align: left"><label style="font-weight: 500;margin-left:1%;font-size: 100%"> </label> </td>
                </tr>
            </table>

        </div>
    </div>
</div>


<script type="text/javascript">
    var search_id = 1;
    $(document).ready(function () {


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
    function save_projectsummary() {
        var data = $('#project_summary').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_tem_repdetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],data[2])
                if(data[0]=='s')
                {
                    fetch_multiple_details();
                    $('#tempmasterID').val(data[2]);
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
</script>
