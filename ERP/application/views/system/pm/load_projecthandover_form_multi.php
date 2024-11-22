<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
$date = '';
?>
<div style="padding: 5%">
    <?php echo form_open('', 'role="form" id="project_hand_over_frm"'); ?>
    <input type="hidden" class="" value="SOHOHEADER" name="tempElementKey" id="tempElementKey">
    <input type="hidden" class="" value="<?php echo $projectID?>" name="headerID" id="headerID">
    <input type="hidden" class="" value="<?php echo $headerID?>"  name="tempmasterID" id="tempmasterID">
    <input type="hidden" class="" value="SOHO"  name="tempkey" id="tempkey">
    <?php if($is_print == 'N') { ?>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12">
                <?php echo export_buttons('', 'SHEET OF HANDING OVER', false, true,'btn-xs','generateReportPdf_soho()'); ?>
            </div>
        </div>
    <?php } ?>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-bottom: none">
                <tr>
                    <td rowspan="2" colspan="3" style="width: 10%;background: white;vertical-align: center; border: 1px solid black; border-collapse: collapse;border-bottom: none">
                        <div style="height: 75px; overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <img alt="Logo" style="height: 70px;width: 18%;margin-top: -1%;" src="<?php echo htmlImage.$this->common_data['company_data']['company_logo']; ?>">
                        </div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">
                <tr>
                    <td colspan="2" style="height: 35px;background: white;text-align: center;border-left: 1px solid black;">
                        <strong style="margin-left: 5%;font-size: 170%">SHEET OF HANDING OVER</strong>
                    </td>
                </tr>
                <tr>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;">
                        <strong style="margin-left: 5%;font-size: 120%">PROJECT:</strong>
                    </td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <strong style="margin-left: 5%;font-size: 120%"><?php echo $project?></strong>
                    </td>
                </tr>
                <tr>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-left: none">
                        <strong style="margin-left: 5%;font-size: 120%">SECTOR</strong>
                    </td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <input style="width: 23%; margin-left: 2%;" type="text" name="sector" id="sector" value="<?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHOSEC')?>" class="form-control">
                    </td>
                </tr>


                <tr>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none">
                        <strong style="margin-left: 5%;font-size: 120%">NAME OF WORK:</strong>
                    </td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <input style="width: 23%; margin-left: 2%;" type="text" name="nameofwork" id="nameofwork" value="<?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHONOW')?>" class="form-control">
                    </td>
                </tr>

                <tr>
                    <td  style="width: 15%;height: 35px;background: white;text-align: left;border-left: 1px solid black;border-left: none">
                        <strong style="margin-left: 5%;font-size: 120%">DATE</strong>
                    </td>

                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <div class="input-group datepic" style="margin-left: 2%">

                            <?php
                            $convertFormat=convert_date_format();
                            $date = format_date(fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHODATE'),$convertFormat);
                            ?>
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="sohodate" value="<?php echo $date?>" id="sohodate"  style="width: 20%;"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">
                <tr>
                    <td  style="height: 60px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">&nbsp;</td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <table class="table table-condensed" style="width: 80%;border: 1px solid black; border-collapse: collapse;" id="sheetofhandingovertbl">
                            <input type="hidden" class="" value="SOHODETAIL" name="tempElementKeymultiple" id="tempElementKeymultiple">
                            <thead>
                            <tr>
                                <th  style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">ITEM DESCRIPTION</strong></th>
                                <th  style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">COMPLETED</strong></th>
                                <th  style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">REMARKS</strong></th>
                                <th  style="border: 1px solid black; border-collapse: collapse;width: 2%;height: 35px;text-align: center;">  <button type="button" onclick="add_more_row()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i></button></th>
                            </tr>

                            </thead>
                            <tbody id="sheetofhandoverView">
                            </tbody>
                        </table>
            </table>
            </td>
            </tr>
            </table>

            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;border-right: none;">

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 125%">&nbsp;</strong></td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 3%;text-align: left"> <strong style="margin-left:1%;font-size: 125%">SNAGS / COMMENTS</strong></td>
                </tr>

                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 3%;text-align: left">
                        <div style="overflow:hidden;Padding-top: 19px;padding-left: 7px;">
                            <textarea  style="text-align: left;width: 60%;" class="form-control" rows="8" name="snagcomment" id="snagcomment">
                            <?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHOSNAG')?>

                            </textarea>
                        </div>

                    </td>
                </tr>
            </table>

            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;">

                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 30%;border-right:none;text-align: left">
                        <strong style="margin-left:1%;font-size: 118%">Handed Over By </strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left:none;">

                    </td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-right: none">
                        <strong style="margin-left:1%;font-size: 118%">Name :</strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left: none">
                        <strong style="margin-left:1%;font-size: 118%">Signature :</strong>
                    </td>
                </tr>
                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 30%;border-right:none;text-align: left">
                        <strong style="margin-left:1%;font-size: 118%">Taken Over By </strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left:none;">

                    </td>
                </tr>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-top:none;border-right: none">
                        <strong style="margin-left:1%;font-size: 118%">Name :</strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-top:none;border-left: none">
                        <strong style="margin-left:1%;font-size: 118%">Signature :</strong>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>


</form>


<script type="text/javascript">
    var search_id = 1;
    var soho_tbl = null;
    $(document).ready(function () {
        load_prohand_records();
        sheetofhandover();
        fetch_multiple_details_sofO();
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

    function sheetofhandover() {
        $('#sheetofhandoverView').html('');
        $('#sheetofhandoverView').append('<tr>' +
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control f_search" name="itemdescription[]" id="f_search_1"> <input type="hidden" value="SOHODETITM" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_1"><input type="hidden" value="1" class="sortorder" name="sortorder[]" id="sortorder_1"></td>'+
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control completed" name="completed_1" id="completed_1"></td>'+
            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control remarks" name="remarks_1" id="remarks_1"></td>' +
            '<td class="remove-td" style="border: 1px solid black; border-collapse: collapse;vertical-align: middle;text-align: center"></td></tr>')
    }
    function add_more_row()
    {
        search_id += 1;
        var appendData = $('#sheetofhandingovertbl tbody tr:first').clone();
        appendData.find('.f_search').val('');
        appendData.find('.completed').val('');
        appendData.find('.remarks').val('');
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.completed').attr('id', 'completed_' + search_id);
        appendData.find('.completed').attr('name', 'completed_' + search_id);
        appendData.find('.remarks').attr('id', 'remarks_' + search_id);
        appendData.find('.remarks').attr('name', 'remarks_' + search_id);
        appendData.find('.sortorder').attr('id', 'sortorder_' + search_id);
        appendData.find('.sortorder').val(search_id);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#sheetofhandingovertbl').append(appendData);
    }
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });
    function fetch_multiple_details_sofO() {
        var ProjectID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:{'headerID':'<?php echo $headerID?>'},
            url: "<?php echo site_url('Boq/fetch_sheetofhandover'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#sheetofhandoverView').html('');
                var i = 1;

                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        $('#sheetofhandoverView').append('<tr>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;"><input type="text" class="form-control f_search" value="'+v.itemDescription+'" name="itemdescription[]" id="f_search_'+i+'"> <input type="hidden" value="SOHODETITM" class="tempElementSubKey" name="tempElementSubKey[]" id="tempElementSubKey_'+i+'"><input type="hidden" value="'+v.sortOrder+'" class="sortorder" name="sortorder[]" id="sortorder_'+i+'"></td>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;text-align: center"><input type="text" class="form-control completed" value="'+v.completed+'" name="completed_'+i+'" id="completed_'+i+'"> </td>' +
                            '<td style="border: 1px solid black; border-collapse: collapse;width: 10%;text-align: center"><input type="text" class="form-control completed" value="'+v.remarks+'" name="remarks_'+i+'" id="remarks_'+i+'">  </td>' +
                            '<td class="remove-td" style="border: 1px solid black; border-collapse: collapse;text-align: center"><span onclick="delete_inspectionreq_status(' + v.detailID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                        search_id++;
                        $('sortorder_'+i).val(i);
                        i++;
                    });

                }else
                {
                    sheetofhandover();
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
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