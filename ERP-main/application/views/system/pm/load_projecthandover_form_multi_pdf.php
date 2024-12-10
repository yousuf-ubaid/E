<?php
$date_format_policy = date_format_policy();
$disable = '';
$this->load->helper('boq_helper');
$date = '';
?>
<div style="padding: 5%">
    <div class="row">
        <div class="col-sm-12">
            <table style="width: 100%; border: 1px solid black; border-collapse: collapse;border-bottom: none">
                <tr>
                    <td>
                        <img alt="Logo" style="height: 60px"
                             src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                    </td>
                </tr>
            </table>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">
                <tr>
                    <td colspan="2" style="height: 35px;background: white;text-align: center;border-left: 1px solid black;">
                        <strong style="margin-left: 5%;font-size: 170%">SHEET OF HANDING OVER</strong>
                    </td>
                </tr>

            </table>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">
                <tr>
                    <td  style="width: 12%;height: 28px;background: white;vertical-align: center;border-left: 1px solid black;border-top: none;border-right: none;border-top: none;border-bottom: none">
                        <strong style="width: 20%;margin-left: 5%;font-size: 115%"><?php echo strtoupper(' Project')?>:</strong>
                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-right: none;border-bottom: none">
                        <label style="font-family:Arial, sans-serif"><?php echo $project?> </label>

                    </td>
                    <td style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 28px;border-left: none;border-top: none;border-left: none;border-bottom: none"> <strong style="margin-left: -60%;font-size: 115%"> </strong></td>

                </tr>
                <tr>

                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-left: none">
                        <strong style="width: 20%;margin-left: 5%;font-size: 115%">SECTOR :</strong>
                    </td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHOSEC')?></label>
                    </td>
                </tr>

            </table>




            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">

                <tr>
                    <td  style="width: 25%;height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none">
                        <strong style="margin-left: 5%;font-size: 115%">NAME OF WORK :</strong>
                    </td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <label style="font-family:Arial, sans-serif"><?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHONOW')?></label>
                    </td>
                </tr>

            </table>
            <table>
                <tr>
                    <td  style="border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-right: none;border-top: none;border-bottom: none;width: 10%">
                        <strong style="margin-left: 5%;font-size: 115%;"><?php echo strtoupper('Date')?>:</strong>
                    </td>
                    <td colspan="2" style="border: 1px solid black;border-collapse: collapse;text-align: left;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <?php
                        $convertFormat=convert_date_format();
                        $date = format_date(fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHODATE'),$convertFormat);
                        ?>
                        <label style="font-family:Arial, sans-serif"><?php echo $date;?> </label>
                    </td>

                </tr>

            </table>

            <table class="table" style="width: 100%;border: 1px solid black; border-collapse: collapse;" id="sheetofhandingovertbl">

                <thead  class="thead">
                <tr>
                    <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">ITEM DESCRIPTION</strong></th>
                    <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">COMPLETED</strong></th>
                    <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">REMARKS</strong></th>

                </tr>

                </thead>
                <tbody>

                <?php
                if(!empty($detailsoho))
                {  foreach ($detailsoho as $val){ ?>

                    <tr>
                        <td style="width: 31%; border: 1px solid black; border-collapse: collapse;border-bottom: none;font-size: 100%"><?php echo $val['itemDescription']?></td>
                        <td style="width: 15%; border: 1px solid black; border-collapse: collapse;border-bottom: none; text-align: center;font-size: 100%"> <?php echo $val['completed']?></td>
                        <td style="width: 15%; border: 1px solid black; border-collapse: collapse;border-bottom: none;text-align: center;font-size: 100%"> <?php echo $val['remarks']?></td>

                    </tr>
                <?php }}?>

                </tbody>
            </table>


         <!--   <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-top: none;border-bottom: none">
                <tr>
                    <td  style="height: 60px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">&nbsp;</td>
                    <td  style="height: 35px;background: white;text-align: left;border-left: 1px solid black;border-right: none;border-left: none">
                        <table class="table" style="width: 100%;border: 1px solid black; border-collapse: collapse;" id="sheetofhandingovertbl">

                            <thead  class="thead">
                            <tr>
                                <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 90px;text-align: center;"><strong style="margin-left: 1%">ITEM DESCRIPTION</strong></th>
                                <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">COMPLETED</strong></th>
                                <th  class="theadtr" style="border: 1px solid black; border-collapse: collapse;width: 5%;height: 35px;text-align: center;"><strong style="margin-left: 1%">REMARKS</strong></th>

                            </tr>

                            </thead>
                            <tbody id="sheetofhandoverView">
                            </tbody>
                        </table>
            </table>-->


            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-right: none;border-left: none ;border-top: none;border-bottom: none">
                <tr>
                    <td  style="width: 60%;border: 1px solid black;border-collapse: collapse;height: 35px;background: white;vertical-align: center;border-top: none;border-bottom: none">
                        <strong style="font-size: 115%">SNAGS / COMMENTS :</strong>
                    </td>

                </tr>

            </table>
            <table style="width: 100%;border: 1px solid black; border-collapse: collapse;border-right: none;border-top: none;border-bottom: none">
                <tr>

                    <td colspan="3" style="border: 1px solid black;border-collapse: collapse;text-align: left;width: 49%;height: 35px;border-left: none;border-top: none;border-bottom: none">

                        <p>  <?php echo fetch_boq_tempdetails($headerID,'SOHOHEADER','SOHO','SOHOSNAG')?></p>
                    </td>
                </tr>

            </table>



            <table style="width: 100%;border: none;border: 1px solid black;border-collapse: collapse;border-left: none;border-bottom: none;border-top:none;">

                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 55%;border-right:none;text-align: left">
                        <strong style="margin-left:1%;font-size: 118%">Handed Over By T & I E Representative</strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left:none;">

                    </td>
                </tr>

            </table>
            <table>
                <tr>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-right: none">
                        <strong style="margin-left:1%;font-size: 118%">Name :</strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left: none">
                        <strong style="margin-left:1%;font-size: 118%">Signature :</strong>
                    </td>
                </tr>


            </table>
            <table>
                <tr>

                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;width: 45%;border-right:none;text-align: left">
                        <strong style="margin-left:1%;font-size: 118%">Taken Over By </strong>
                    </td>
                    <td style="height: 35px;border: 1px solid black;border-collapse: collapse;border-bottom: none;border-top:none;border-left:none;">

                    </td>
                </tr>

            </table>
            <table>
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