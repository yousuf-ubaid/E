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
    <form role="form" id="jp_passanger_detail_add_form" class="form-horizontal">
        <input type="hidden" class="form-control" name="jpnumberadd" id="jpnumberadd" value="<?php echo $jpnumber ?>">
        <table class="table table-bordered table-condensed no-color" id="jp_passanger_detail_add_table"  style="width: 50%">
            <thead>
            <tr>
                <th>Name Of Passenger <?php required_mark(); ?></th>
                <th>Contact Number<?php required_mark(); ?></th>
                <th style="width: 40px;">
                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()">
                        <i class="fa fa-plus"></i></button>
                </th>
            </tr>
            </thead>
            <tbody id="item_table_body_passangers">
            <tr>
                <td>
                    <input type="text" class="form-control" name="passangername[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="contactno[]">
                </td>
                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
            </tr>
            </tbody>
        </table>
    </form>
    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-7">
            <button class="btn btn-primary pull-right" type="button" id="save_btn" onclick="save_details_passanger()">Save</button>
        </div>
    </div>
    </div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var jpnumber;
    var EIdNo;
    var currency_decimal;
    $(document).ready(function () {
        fetch_detail_passanger();

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

    });
    function add_more() {
       // $('select.select2').select2('destroy');
        var appendData = $('#jp_passanger_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#jp_passanger_detail_add_table').append(appendData);
    }
    function save_details_passanger() {
        var data = $('#jp_passanger_detail_add_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Journeyplan/save_jp_passanger_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_detail_passanger();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_detail_passanger() {
        var jpnumber = $('#jpnumberadd').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'jpnumber': jpnumber},
            url: "<?php echo site_url('Journeyplan/fetch_passenger_detail_tbl'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#item_table_body_passangers').empty();
                if (!jQuery.isEmptyObject(data['detail'])) {
                    var x = 0;
                    $.each(data['detail'], function (key, value) {

                        if(x == 0)
                        {
                            var deleterecordds = ' ';
                        } else
                        {
                            var deleterecordds = '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>';
                        }

                        $('#item_table_body_passangers').append('<tr><td><input type="text" class="form-control" value="'+ value['passengerName'] +'"name="passangername[]"></td><td><input type="text" class="form-control" value="'+ value['contactNo'] +'" name="contactno[]"></td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                        x++;
                    });

                }else
                {
                    var deleterecordds = ' ';
                    $('#item_table_body_passangers').append('<tr><td><input type="text" class="form-control"  name="passangername[]"></td><td><input type="text" class="form-control"  name="contactno[]"></td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                }
                }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>


