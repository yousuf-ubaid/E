<?php
$this->load->helper('journeyplan_helper');
$items = item_dropdown_tour_price();
?>

<div>
    <form role="form" id="jp_tour_price_detail_add_form" class="form-horizontal">
        <input type="hidden" class="form-control" name="jpnumberadd" id="jpnumberadd" value="<?php echo $jpnumber ?>">
        <table class="table table-bordered table-condensed no-color" id="jp_tour_price_detail_add_table"  >
            <thead>
            <tr>
                <th>Item <?php required_mark(); ?></th>
                <th>Amount<?php required_mark(); ?></th>
                <th>Remark<?php required_mark(); ?></th>
                <th style="width: 40px;" class="btnsave">
                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_price()">
                        <i class="fa fa-plus"></i></button>
                </th>
            </tr>
            </thead>
            <tbody id="item_table_body_tour_price">
            <tr>
                <td>
                <td style="width: 31%;">
                    <input type="text" class="form-control f_search" name="searchitems[]" placeholder="Item Description..." id="f_search_1">
                    <input type="hidden" class="form-control itemAutoID_tour" name="itemAutoID[]">
                </td>
                <td>
                    <input type="text" class="form-control number" name="amount[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="remarks[]">
                </td>
                <td class="remove-td btnsave hide" style="vertical-align: middle;text-align: center"></td>
            </tr>
            </tbody>
        </table>
    </form>
    <div class="row btnsave hide" style="margin-top: 10px;">
        <div class="form-group">
            <button class="btn btn-primary pull-right" type="button" id="save_btn" onclick="save_tour_price_details()">Save Price</button>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var jpnumber;
    var EIdNo;
    var currency_decimal;
    var search_id = 1;
    $(document).ready(function () {
        number_validation();
        fetch_detail_tour_price();
        $('.select2').select2();
        $("[rel=tooltip]").tooltip();

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function add_more_price() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#jp_tour_price_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#jp_tour_price_detail_add_table').append(appendData);
        initializetour_price_item(search_id);
        number_validation();
        $(".select2").select2();
    }
    function initializetour_price_item(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Journeyplan/fetch_tour_price_items/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID_tour').val(suggestion.itemAutoID);
                }, 200);
                checkRevenueGL(suggestion.itemAutoID, id);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function save_tour_price_details() {
        var data = $('#jp_tour_price_detail_add_form').serializeArray();
        data.push({'name': 'chargeType', 'value': 1});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Journeyplan/save_tour_price_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_detail_tour_price();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_detail_tour_price() {
        var jpnumber = $('#jpnumberadd').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'jpnumber': jpnumber, 'chargeType': 1},
            url: "<?php echo site_url('Journeyplan/fetch_tour_price_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#item_table_body_tour_price').empty();
                if (!jQuery.isEmptyObject(data['details'])) {
                    var x = 0;
                    $.each(data['details'], function (key, value) {
                        if(x == 0)
                        {
                            var deleterecordds = ' ';
                        } else
                        {
                            // var deleterecordds = ' ';
                            var deleterecordds = '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>';
                        }
                        <?php if($view == 1){?>
                        $('.btnsave').addClass('hide');
                        $('#item_table_body_tour_price').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" value="' + value['itemDescription'] + '" name="search[]" placeholder="Item Description..." id="f_search_' + search_id + '" readonly> <input type="hidden" class="form-control itemAutoID_tour" name="itemAutoID[]" value="' + value['itemAutoID'] + '" readonly></td><td><input type="text" class="form-control number" value="'+ value['amount'] +'" name="amount[]" readonly></td><td><input type="text" class="form-control" value="'+ value['remarks'] +'" name="remarks[]" readonly></td></tr>');
                        <?php }else {?>
                        $('.btnsave').removeClass('hide');
                        $('#item_table_body_tour_price').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" value="' + value['itemDescription'] + '" name="search[]" placeholder="Item Description..." id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemAutoID_tour" name="itemAutoID[]" value="' + value['itemAutoID'] + '"></td><td><input type="text" class="form-control number" value="'+ value['amount'] +'" name="amount[]"></td><td><input type="text" class="form-control" value="'+ value['remarks'] +'" name="remarks[]"></td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                       <?php }?>
                        initializetour_price_item(search_id);
                        search_id++;
                        x++;
                    });

                }else  {
                    var deleterecordds = ' ';
                    $('#item_table_body_tour_price').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." id="f_search_1"> <input type="hidden" class="form-control itemAutoID_tour" name="itemAutoID[]"></td>' +
                        ' <td><input type="text" class="form-control number" name="amount[]" placeholder="0.00"></td><td><input type="text" class="form-control" name="remarks[]"></td><td class="remove-td btnsave" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                    number_validation();
                    $('.select2').select2();
                    setTimeout(function () {
                        initializetour_price_item(1);
                    }, 500);
                }
                <?php
                if($view == 1){ ?>
                $('.btnsave').addClass('hide');
                <?php } else { ?>
                $('.btnsave').removeClass('hide');
                <?php } ?>
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function checkRevenueGL(itemAutoID, id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Journeyplan/checkRevenueGL_tour_price'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data){
                    myAlert(data[0], data[1]);
                    $('#f_search_' + id).closest('tr').find('.itemAutoID_tour').val('');
                    $('#f_search_' + id).val('');
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>


