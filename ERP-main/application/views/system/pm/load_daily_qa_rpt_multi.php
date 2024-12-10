<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
$date = '';
?>


<div style="padding: 5%">
    <?php echo form_open('', 'role="form" id="daily_qr_report"'); ?>
    <input type="hidden" class="" value="DQRHEADER" name="tempElementKey" id="tempElementKey">
    <input type="hidden" class="" value="<?php echo $projectID?>" name="headerID" id="headerID">
    <input type="hidden" class="" value="<?php echo $headerID?>"  name="tempmasterID" id="tempmasterID">
    <input type="hidden" class="" value="DQR"  name="tempkey" id="tempkey">
    <?php if($is_print == 'N') { ?>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12">
                <?php echo export_buttons('', 'DAILY QUALITY REPORT', false, True); ?>
            </div>
        </div>
    <?php } ?>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td rowspan="2" colspan="3" style="width: 10%;background: black;vertical-align: center; border: 1px solid black; border-collapse: collapse;">
                        <div style="height: 75px; overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <strong style="font-size: 150%;color: white">DAILY QUALITY REPORT</strong>
                        </div>
                    </td>
                    <td style="width: 3%; border: 1px solid black; border-collapse: collapse;"> <strong style="margin-left: 1%">DATE :</strong></td>
                    <td style="width: 5%; border: 1px solid black; border-collapse: collapse;">
                        <div class="input-group datepic" style="margin-left: 2%">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                            <?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRDA'),$convertFormat);
                            ?>


                            <input type="text" name="date" value="<?php echo $date?>" id="dailyqualityrep"  style="width: 89%;"
                                   class="form-control dateFields"

                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td style=" border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">REPORT NO  :</strong></td>
                    <td style=" border: 1px solid black; border-collapse: collapse;">
                        <input style="width: 90%; margin-left: 1%;" type="text" name="reportno" id="reportno" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRRN');?>">
                    </td>
                </tr>
                <tr>

                </tr>
                <tr>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">PROJECT :</strong></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%"><?php echo $project?></strong></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">CONTRACT NO :</strong></td>
                    <td colspan="2"  style="width: 5%;height: 35px;"> <input style="width: 90%; margin-left: 1%;" type="text" name="contractno" id="contractno" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRCN');?>" class="form-control"></td>
                </tr>
                <tr>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">LOCATION :</strong></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"> <input style="width: 90%; margin-left: 1%;" type="text" name="location" id="location" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRLN');?>"></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">DISCIPLINE :</strong></td>
                    <td colspan="2"  style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"> <input style="width: 90%; margin-left: 1%;" type="text" name="discipline" id="discipline" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRDP');?>"></td>
                </tr>
                <tr>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">ISSUED BY :</strong></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"> <input style="width: 90%; margin-left: 1%;" type="text" name="issuedby" id="issuedby" class="form-control"  value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRIB');?>"></td>
                    <td style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">DRAWINGS :</strong></td>
                    <td colspan="2" style="width: 5%;height: 35px; border: 1px solid black; border-collapse: collapse;"> <input style="width: 90%; margin-left: 1%;" type="text" name="drawings" id="drawings" class="form-control"  value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRIDR');?>"></td>
                </tr>
                <tr>
                    <td  colspan="5" style="width: 5%;height: 35px;text-align: center;background: black; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;color: white;font-size: 127%; ">INSPECTION SUMMARY</strong></td>
                </tr>
                <tr>
                    <td  colspan="5" style="width: 5%;height: 200px;text-align: center; border: 1px solid black; border-collapse: collapse;">
                        <div style="overflow:hidden;Padding-top: 19px;padding-left: 7px;margin-top: -2%;width: 99%;">
                            <textarea class="form-control" rows="8" name="inspectionsummary" id="inspectionsummary"><?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRIS');?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td  colspan="5" style="width: 5%;height: 35px;text-align: center;background: black; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;color: white;font-size: 127%; ">INSPECTION REQUEST STATUS</strong></td>
                </tr>
            </table>

            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;">
                <input type="hidden" class="" value="IRS" name="tempElementKeymultiple" id="tempElementKeymultiple">
                <table id="dailyqualityreportviewtbl"
                       class="table table-condensed">
                    <thead>
                    <tr>
                        <th rowspan="2"  style="width: 20%;height: 35px;text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;font-size: 98%">INSPECTION REQUEST NO'S</strong></th>
                        <th colspan="3"  style="width: 5%;height: 35px;text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">MAIN CONTRACTOR</strong></th>
                        <th colspan="1"  style="width: 5%;height: 35px;text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">Consultant Status</strong></th>
                        <th rowspan="2"  style="width: 2%;height: 35px;text-align: center; border: 1px solid black; border-collapse: collapse;">  <button type="button" onclick="add_more_row()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i></button></th>
                    </tr>
                    <tr>
                        <th style="text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">Not Ready</strong></th>
                        <th style="text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">Accepted </strong></th>
                        <th style="text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">Not Accepted </strong></th>
                        <th style="text-align: center; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%">Rejected / Accepted</strong></th>
                    </tr>
                    </thead>
                    <tbody id="dailyqualityreportview">

                    </tbody>
                </table>

            </table>
            <br>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td   style="width: 5%;height: 35px;text-align: center;background: black; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;color: white;font-size: 127%; ">MATERIAL INSPECTION (if any)</strong></td>
                    <td   style="width: 5%;height: 35px;text-align: center;background: white; border: 1px solid black; border-collapse: collapse;">
                        <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="materialinspection" id="materialinspection" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRMIA');?>">
                    </td>
                    <td  style="width: 5%;height: 35px;text-align: center;background: black; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;color: white;font-size: 127%; ">TEST CONDUCTED (if any)</strong></td>
                    <td   style="width: 5%;height: 35px;text-align: center;background: white; border: 1px solid black; border-collapse: collapse;">
                        <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="testconducted" id="testconducted" class="form-control" value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRTCA');?>">
                    </td>
                    <td   style="width: 5%;height: 35px;text-align: center;background: black; border: 1px solid black; border-collapse: collapse;"><strong style="margin-left: 1%;color: white;font-size: 127%; ">NCR ISSUED (if any)</strong></td>
                    <td   style="width: 5%;height: 35px;text-align: center;background: white; border: 1px solid black; border-collapse: collapse;">
                        <input style="width: 90%; margin-left: 3%;" type="text" onkeypress="return validateFloatKeyPress(this,event)" name="ncrissued" id="ncrissued" class="form-control"  value="<?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRNCRA');?>">
                    </td>
                </tr>
            </table>
            <p style="font-weight: 500">Attach register of above reports issued and comment by exeption</p>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;">

                <tr>
                    <td  colspan="5" style="width: 5%;height: 150px;text-align: left; border: 1px solid black; border-collapse: collapse;">
                        <div style="overflow:hidden;Padding-top: 19px;padding-left: 7px;margin-top: -2%;width: 99%;">
                            <strong>REMARKS :</strong>
                            <textarea class="form-control" rows="6" name="remarks" id="remarks"><?php echo fetch_boq_tempdetails($headerID,'DQRHEADER','DQR','DQRREM');?></textarea>
                        </div>
                    </td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;">

                <tr>
                    <td  style="width: 5%;height: 30px;padding-left: 1%; border: 1px solid black; border-collapse: collapse;">
                        Prepared by : <strong>QC Inspector</strong>
                    </td>
                    <td  style="width: 5%;height: 30px;padding-left: 1%; border: 1px solid black; border-collapse: collapse;">
                        Checked by : <strong>QA/QC Manager</strong>
                    </td>
                    <td  style="width: 5%;height: 30px;padding-left: 1%; border: 1px solid black; border-collapse: collapse;">
                        Reviewed by : <strong>Project Manager</strong>
                    </td>
                </tr>
            </table>





        </div>
    </div>
</div>


</form>

<script type="text/javascript">
    var search_id = 1;
    var rec_tbl = null;
    $(document).ready(function () {
        load_qa_qc_records();
        inspectionrequeststatus_row();
        fetch_multiple_details();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

    });

    function inspectionrequeststatus_row() {
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('#dailyqualityreportview').html('');
        $('#dailyqualityreportview').append('<tr>' +
            '<td style="width: 31%; border: 1px solid black; border-collapse: collapse;"><input type="text" class="form-control f_search" name="fieldvaluemulti[]" id="f_search_1"> <input type="hidden" value="IRSD" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_1"><input type="hidden" value="1" class="sortorder" name="sortorder[]" id="sortorder_1"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="notready_1" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_1" value="IRSNR"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="accepted_1" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_1" value="IRSA"> </td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="notaccepted_1" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_1" value="IRSNA"></td>' +
            '<td style="width: 15%; border: 1px solid black; border-collapse: collapse;text-align: center"><input id="rejected_1" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_1" value="IRSCS"> </div></div></td>' +

            '<td class="remove-td" style="vertical-align: middle;text-align: center"></td></tr>')



    }
    function add_more_row()
    {

        search_id += 1;

        var appendData = $('#dailyqualityreportviewtbl tbody tr:first').clone();
        appendData.find('.f_search').val('');
        appendData.find('.notready').prop('checked', false);
        appendData.find('.accepted').prop('checked', false);
        appendData.find('.notaccepted').prop('checked', false);
        appendData.find('.rejected').prop('checked', false);
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.notready').attr('id', 'notready_' + search_id);
        appendData.find('.accepted').attr('id', 'accepted_' + search_id);
        appendData.find('.sortorder').attr('id', 'sortorder_' + search_id);
        appendData.find('.sortorder').val(search_id);
        appendData.find('.notaccepted').attr('id', 'notaccepted_' + search_id);
        appendData.find('.rejected').attr('id', 'rejected_' + search_id);

        appendData.find('.notready').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.accepted').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.notaccepted').attr('name', 'fieldvaluemultiradio_' + search_id);
        appendData.find('.rejected').attr('name', 'fieldvaluemulticheck_' + search_id);
        appendData.find('.tempElementSubKey').attr('id', 'tempElementSubKey_' + search_id);




        /* appendData.find('.fieldvaluemultiradio').attr('id', 'accepted_' + search_id);
         appendData.find('.notaccepted').attr('id', 'notaccepted_' + search_id);
         appendData.find('.rejected').attr('id', 'rejected_' + search_id);
         appendData.find('.fieldvaluemultiradio').attr('name', 'fieldvaluemultiradio_' + search_id+'[]');
         appendData.find('.fieldvaluemulticheck').attr('name', 'fieldvaluemulticheck_' + search_id+'[]');
 */        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#dailyqualityreportviewtbl').append(appendData);
    }
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });

    function fetch_multiple_details() {
        var ProjectID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:{'headerID':'<?php echo $headerID?>'},
            url: "<?php echo site_url('Boq/fetch_dailyqulityrep_multidetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#dailyqualityreportview').html('');
                var i = 1;
                var checkedradio = '';
                var checkedradio2 = '';
                var checkedradio3 = '';
                var checkboxval = '';
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {

                        if((v.Radiovalue=='IRSNR'))
                        {
                            checkedradio = '<input id="notready_'+i+'" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_'+i+'" value="IRSNR" checked>'
                        }else
                        {
                            checkedradio = '<input id="notready_'+i+'" type="radio"data-caption="" class="notready" name="fieldvaluemultiradio_'+i+'" value="IRSNR">';
                        }
                        if((v.Radiovalue=='IRSA'))
                        {
                            checkedradio2 = '<input id="accepted_'+i+'" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_'+i+'" value="IRSA" checked>'
                        }else
                        {
                            checkedradio2 = '<input id="accepted_'+i+'" type="radio"data-caption="" class="accepted" name="fieldvaluemultiradio_'+i+'" value="IRSA">';
                        }
                        if((v.Radiovalue=='IRSNA'))
                        {
                            checkedradio3 = '<input id="notaccepted_'+i+'" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_'+i+'" value="IRSNA" checked>'
                        }else
                        {
                            checkedradio3 = '<input id="notaccepted_'+i+'" type="radio"data-caption="" class="notaccepted" name="fieldvaluemultiradio_'+i+'" value="IRSNA">';
                        }


                        if(v.checkboxvalue=='IRSCS')
                        {
                            checkboxval = '<input id="rejected_'+i+'" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_'+i+'" value="IRSCS" checked>';
                        }else
                        {
                            checkboxval = '<input id="rejected_'+i+'" type="checkbox"data-caption="" class="rejected" name="fieldvaluemulticheck_'+i+'" value="IRSCS" >';
                        }


                        $('#dailyqualityreportview').append('<tr><td style=" border: 1px solid black; border-collapse: collapse;width: 31%;"><input type="text" class="form-control f_search" value="'+v.detail+'" name="fieldvaluemulti[]" id="f_search_'+i+'"> <input type="hidden" value="IRSD" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_'+i+'"><input type="hidden" value="'+v.sortOrder+'" class="sortorder" name="sortorder[]" id="sortorder_'+i+'"></td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio2+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkedradio3+' </td><td style=" border: 1px solid black; border-collapse: collapse;width: 15%;text-align: center">'+checkboxval+' </td> <td class="remove-td" style=" border: 1px solid black; border-collapse: collapse;vertical-align: middle;text-align: center"><span onclick="delete_inspectionreq_status(' + v.detailID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');


                        search_id++;
                        $('sortorder_'+i).val(i);
                        i++;
                    });

                }else
                {
                    inspectionrequeststatus_row();
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });



    }
    function delete_inspectionreq_status(detail_id)
    {

        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('Boq/delete_inspectionreqstatus'); ?>",
                    type: 'post',
                    data: {detail_id: detail_id},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            fetch_multiple_details();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
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




<!--<div class="row" style="margin-top: -4%">
    <div class="col-sm-11">
        <div class="text-right m-t-xs"><button class="btn btn-primary next" onclick="save_quality_report();">Save</div>
    </div>
</div>-->