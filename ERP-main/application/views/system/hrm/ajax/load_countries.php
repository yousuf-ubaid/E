<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//echo '<pre>';print_r($countries); echo '</pre>'; die();
?>

<style type="text/css">
    .hideTr{ display: none }

    .oddTR td{ background: #f9f9f9 !important; }

    .evenTR td{ background: #ffffff !important; }

    @media (max-width: 767px) {
        #un-check-btn { float: left !important;}
    }

    .fixHeader_Div {
        height: 240px;
        border: 1px solid #c0c0c0;
    }

    div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    div.fixHeader_Div::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb  {
        margin-left: 30px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        width: 3px;
        position: absolute;
        top: 0px;
        opacity: 0.4;
        border-radius: 7px;
        z-index: 99;
        right: 1px;
        height: 40px;
    }
</style>
<script type="text/javascript"> var selectedItems = []; </script>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-9" style="">&nbsp;</div>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="searchItem" value="" placeholder="<?php echo $this->lang->line('common_search');?>"><!--Search-->
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php echo form_open('','role="form" id="proItem_form" autocomplete="off"'); ?>
        <div class="fixHeader_Div" style="max-width: 100%; height: 253px">
            <table class="<?php echo table_class();?>" id="countryMasterTB">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th><?php echo $this->lang->line('common_country_name');?><!--Country Name--></th>
                    <th>
                        <input type="checkbox" onclick="allToggle(this)" />
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach($countries as $key=>$item){
                    $tr_data = $item['countryShortCode'].''.$item['CountryDes'];

                    $outPut = '<div align="center">';
                    $outPut .= '<input type="checkbox" name="countrySelChk[]" class="countrySelChk" style="margin: 0px" onclick="countrySelect(this)"';
                    $outPut .= 'value="' . $item['countryID'] . '" data-name="' . $item['CountryDes'] . '" data-code="' . $item['countryShortCode'] . '">';
                    $outPut .= '</div>';

                    echo '<tr data-value="'.$tr_data.'">
                            <td align="right">'.($key+1).'</td>
                            <td>'.$item['countryShortCode'].'</td>
                            <td>'.$item['CountryDes'].'</td>
                            <td align="center">'.$outPut.'</td>
                          </tr>';

                    echo '<script>selectedItems.push('.$item["countryID"].');</script>';
                }
                ?>
                </tbody>
            </table>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="" style="margin-top: 1% !important;">
        <div class="col-sm-9" style="margin-top: 1%">
            <label>
                <?php echo $this->lang->line('common_showing');?><!--Showing--> <span id="showingCount"><?php echo count($countries);?></span> <?php echo $this->lang->line('common_of');?><!--of-->
                <span id="totalRowCount"><?php echo count($countries);?></span>  <?php echo $this->lang->line('common_entries');?><!--entries-->
            </label>
        </div>

        <div class="col-sm-3">
            <input type="button" class="btn btn-primary btn-xs pull-right" id="un-check-btn" value=" <?php echo $this->lang->line('common_un_check_all');?>" onclick="unCheckAll()"><!--Un check all-->
        </div>
    </div>
</div>


<script type="text/javascript">
    var countryMasterTB = $('#countryMasterTB');
    var countryArray = [];

    $(document).ready(function () {
        var title = $('#promoType').find(':selected').text();
        $('#title-label').text(title);

        countryMasterTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });
    });

    function save_country(){
        if(countryArray.length > 0){
            var postData = JSON.stringify(countryArray);
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/saveCountry'); ?>',
                data: {'country':postData},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        countryArray = [];
                        $('#country_modal').modal('hide');
                        load_country();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('hrms_others_master_please_select_at_least_one_country');?>');/*Please select at least one country*/
        }
    }

    function countrySelect(obj){
        var thisVal = $(obj).val();

        if ($(obj).is(':checked')) {
            var inArray = $.inArray(thisVal, countryArray);
            if (inArray == -1) {
                var cName = $(obj).attr('data-name');
                var cCode = $(obj).attr('data-code');

                countryArray.push({'id':thisVal, 'name': cName, 'code': cCode});
            }
        }
        else {
            countryArray = $.grep(countryArray, function(data, index) {
                return parseInt(data.id) != thisVal
            });
        }
    }

    $('#searchItem').keyup(function(){
        var searchKey = $.trim($(this).val()).toLowerCase();
        var tableTR = $('#countryMasterTB tbody>tr');
        var row = 0;

        tableTR.removeClass('hideTr evenTR oddTR');

        tableTR.each(function(){
            var dataValue = ''+$(this).attr('data-value')+'';
            dataValue = dataValue.toLocaleLowerCase();

            if(dataValue.indexOf(''+searchKey+'') == -1){
                $(this).addClass('hideTr');
            }
            else{ row++; }

            $('#showingCount').text(row);
        });

        applyRowNumbers();
    });



    function applyRowNumbers(){
        var m = 1;
        $('#countryMasterTB tbody>tr').each(function(i){
            if( !$(this).hasClass('hideTr') ){
                var isEvenRow = ( m % 2 );
                if( isEvenRow == 0 ){
                    $(this).addClass('evenTR');
                }else{
                    $(this).addClass('oddTR');
                }

                $(this).find('td:eq(0)').html( m );
                m += 1;
            }
        });
    }

    function countTotalRow(){
        $('#totalRowCount').text( $('#countryMasterTB tbody>tr').length );
    }

    function unCheckAll(){
        $('.countrySelChk').prop('checked', false);
        countryArray = [];
    }

    function allToggle(obj){
        var isChecked = $(obj).prop('checked');

        $('#countryMasterTB tbody>tr').each(function(i){
            if( !$(this).hasClass('hideTr') ){
                var obj = $(this).find('.countrySelChk');
                $(obj).prop('checked', isChecked);
                var thisVal = $(obj).val();

                if (isChecked) {
                    var inArray = $.inArray(thisVal, countryArray);
                    if (inArray == -1) {
                        var cName = $(obj).attr('data-name');
                        var cCode = $(obj).attr('data-code');

                        countryArray.push({'id':thisVal, 'name': cName, 'code': cCode});
                    }
                }
                else {
                    countryArray = $.grep(countryArray, function(data, index) {
                        return parseInt(data.id) != thisVal
                    });
                }
            }
        });
    }

</script>



<?php
