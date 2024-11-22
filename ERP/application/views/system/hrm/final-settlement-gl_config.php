<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_final_settlement_GL_configuration_title');
echo head_page($title, false);
$configData = finalSettlement_gl_config();
$glCode_ex = expenseGL_drop(1);

$additionStr = '';
$deductionStr = '';
foreach ($configData as $item){
    $isDeduction = $item['isDedction'];
    $isDeductionDes = ($isDeduction == 1)? 'Deduction': 'Addition';
    $selVal = $item['typeID'];
    $gl_ID = $item['GLID'];

    if($isDeduction == 1){
        $deductionStr .= '<tr>                            
                            <td style="width: 150px">'.$item['description'].'</td>
                            <td style="width: 100px">'.$isDeductionDes.'</td>
                            <td>                                        
                                '.form_dropdown('glCode_ex[]', $glCode_ex, $gl_ID, 'class="form-control select-drop" data-val="'.$selVal.'" onchange="update_gl(this)" ').'                                       
                            </td>
                          </tr>';
    }
    else{
        $additionStr .= '<tr>                            
                            <td style="width: 150px">'.$item['description'].'</td>
                            <td style="width: 100px">'.$isDeductionDes.'</td>
                            <td>                                        
                                '.form_dropdown('glCode_ex[]', $glCode_ex, $gl_ID, 'class="form-control select-drop" data-val="'.$selVal.'" onchange="update_gl(this)" ').'                                       
                            </td>
                          </tr>';
    }
}
?>

<div class="row" style="margin-top: 3%;">
    <div class="col-md-12">
        <table class="<?php echo table_class() ?>" id="setup_tb" style="margin-bottom: 10px">
            <thead>
            <tr>
                <th> <?php echo $this->lang->line('hrms_final_addition');?><!--Addition--> </th>
                <th><?php echo $this->lang->line('hrms_final_deduction');?> <!--Deduction--> </th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td class="top-align">
                    <table class="<?php echo table_class() ?>" id="">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_description')?></th>
                        <th><?php echo $this->lang->line('common_type');?></th>
                            <th><?php echo $this->lang->line('hrms_final_glaccount')?> <!--GL Account--></th>
                        </tr>
                        </thead>

                        <tbody> <?php echo $additionStr ?> </tbody>
                    </table>
                </td>

                <td class="top-align">
                    <table class="<?php echo table_class() ?>" id="">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_description')?></th>
                            <th><?php echo $this->lang->line('common_type');?></th>
                            <th><?php echo $this->lang->line('hrms_final_glaccount')?></th>
                        </tr>
                        </thead>

                        <tbody> <?php echo $deductionStr ?> </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">

    $('.select-drop').select2();
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/final-settlement-gl_config','','HRMS');
        });
    });

    function update_gl(obj){
        var glCode = $(obj).val();
        var itemID = $(obj).attr('data-val');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {glCode: glCode, itemID: itemID},
            url: '<?php echo site_url("Employee/final_settlement_GL_config"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>

<?php
