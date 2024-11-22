<?php
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>


<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

</style>

    <!--<div class="table-responsive mailbox-messages" id="advancerecid">-->
<div>
    <form role="form" id="jp_detail_add_form" class="form-horizontal">
        <input type="hidden" class="form-control" name="jpnumberadd" id="jpnumberadd" value="<?php echo $jpnumber ?>">
        <table class="table table-bordered table-condensed no-color" id="jp_detail_add_table">
            <thead>
            <tr>
                <th>Place Names<?php required_mark(); ?></th>
                <th colspan="2">Time Arrive<?php required_mark(); ?></th>
                <th colspan="2">Time Depart <?php required_mark(); ?></th>

                <th>Rest</th>
                <th>Sleep Motel Name</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr>
                <td>
                    <input type="text" class="form-control" name="placenames[]">
                </td>
                <td>
                    <div class="input-group datepicdetails hide arrivecls">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="arrivedate[]"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control"
                               required>
                    </div>
                </td>
                <td>
                    <div class="input-group datetimepicker4 hide arivetimecls">
                        <input type="text" class="form-control " name="arrivetime[]" /><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                    </div>
                </td>
                <td>
                    <div class="input-group datepicdetails">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="departdate[]"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control departdatecls"
                               required>
                    </div>
                </td>
                <td>
                    <div class="input-group datetimepicker4">
                        <input type="text" class="form-control departtimecls" name="departtime[]" /><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                    </div>
                </td>
                <td>
                    <?php echo form_dropdown('restyn[]', array('1'=>'Yes','2'=>'No'), '', 'class="form-control select2" name ="restyn[]" '); ?>
                </td>
                <td>
                    <input type="text" class="form-control" name="sleepmotelname[]">
                </td>

            </tr>
            </tbody>
        </table>
    </form>
    </div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var jpnumber;
    var EIdNo;
    var currency_decimal;
    $(document).ready(function () {
        fetch_detail();
        number_validation();
        $('.select2').select2();
        $("[rel=tooltip]").tooltip();
        $(".paymentmoad").hide();


        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#grv_forjourneyplanheader_formm').bootstrapValidator('revalidateField', 'departuredate');
        });
        $('.datepicdetails').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : 'HH:mm',
            widgetPositioning: {
                vertical: 'top'
            }
        });

    });
    function add_more_vouchers() {
        $('select.select2').select2('destroy');
        var appendData = $('#jp_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.arrivecls').removeClass('hide');
        appendData.find('.arivetimecls').removeClass('hide');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#jp_detail_add_table').append(appendData);

        var lenght = $('#jp_detail_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepicdetails').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
           // $('#jp_detail_add_table').bootstrapValidator('revalidateField', 'departdatecls');
        });
        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : 'HH:mm',
            widgetPositioning: {
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
          //  $('#jp_detail_add_table').bootstrapValidator('revalidateField', 'departtimecls');
        });
    }
    function save_details() {
        var data = $('#jp_detail_add_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Journeyplan/save_jp_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_detail();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_detail() {
        var jpnumber = $('#jpnumberadd').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'jpnumber': jpnumber},
                url: "<?php echo site_url('Journeyplan/fetch_item_jv_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#item_table_body').empty();
                    $('#table_tfoot').empty();
                        var x = 0;

                    $.each(data['detail'], function (key, value) {

                        if (x == 0) {
                            var arrive = '<div class="input-group datepicdetails hide arrivecls"><div class="input-group-addon"><i class="fa fa-calendar"></i></div> <input type="text" name="arrivedate[]"  value="" class="form-control"required </div>'
                            var artime = '<div class="input-group datetimepicker4 hide arivetimecls"><input type="text" class="form-control" value="" name="arrivetime[]" /><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>'
                        } else {
                            var arrive = '<div class="input-group datepicdetails arrivecls"><div class="input-group-addon"><i class="fa fa-calendar"></i></div> <input type="text" name="arrivedate[]"  value="'+value['dateArivedcon']+'" class="form-control"required readonly> </div>'

                            var artime = '<div class="input-group datetimepicker4 arivetimecls"><input type="text" class="form-control" value="' + value['timeArrive'] + '" name="arrivetime[]" readonly /><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>'
                        }
                        if (value['restTick'] == 1)
                        {
                           var selectstatus =  '<select name="restyn[]" class="form-control" tabindex="-1" aria-hidden="true" readonly> <option value="1" selected>Yes</option> <option value="2" >No</option></select>';
                        }else
                        {
                            var selectstatus =  '<select name="restyn[]" class="form-control" tabindex="-1" aria-hidden="true" readonly> <option value="1" >Yes</option> <option value="2" selected>No</option></select>';
                        }



                        if(value['dateDepart'] == " ")
                        {
                            var dateformatpolicy = '<div class="input-group datepicdetails"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="departdate[]"   value= "" class="form-control departdatecls"required readonly></div>'
                        }else
                        {
                            var dateformatpolicy = '<div class="input-group datepicdetails"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="departdate[]"   value= "'+ value['dateDepartcon']+'" class="form-control departdatecls"required readonly></div>'
                        }
                        if(value['timeDepart'] == " ")
                        {
                            var departtime = '<div class="input-group datetimepicker4"><input type="text" class="form-control departtimecls" value="" name="departtime[]" readonly/><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>'
                        }else
                        {
                            var departtime = '<div class="input-group datetimepicker4"><input type="text" value="'+ value['timeDepart']+'" class="form-control departtimecls" name="departtime[]" readonly/><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>'
                        }

                            $('#item_table_body').append('<tr><td><input type="text" class="form-control" value="'+ value['placeName'] +'" name="placenames[]" readonly></td><td>'+arrive+'</td><td>' + artime + '</td><td>' + dateformatpolicy + '</td><td>'+ departtime +'</td><td>'+ selectstatus +'</td><td><input type="text" class="form-control" value="'+ value['sleep'] +'" name="sleepmotelname[]" readonly></td></tr>');
                            x++;
                        });

                    $('.datepicdetails').datetimepicker({
                        useCurrent: false,
                        format: date_format_policy,
                    }).on('dp.change', function (ev) {

                    });
                    $('.datetimepicker4').datetimepicker({
                        useCurrent: false,
                        format : 'HH:mm',
                        widgetPositioning: {
                            vertical: 'top'
                        }
                    }).on('dp.change', function (ev) {

                    });
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }


</script>


