<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<form id="general_ot_template_sort_frm">
<table class="table table-bordered table-striped table-condensed">
    <thead>
    <tr>
        <th>#</th>
    <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
        <th><?php echo $this->lang->line('common_sort_order'); ?><!--Sort Order--></th>
        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
    </tr>
    </thead>
    <tbody>
<?php
if(!empty($detail)){
    $i=1;
    foreach($detail as $val){
        ?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php if(empty($val['defaultcategoryID'])){echo $val['categoryDescription'];}else{echo $val['defultDescription'];}  ?></td>
            <td><input type="hidden" id="templatedetailID" name="templatedetailID[]" value="<?php echo $val['templatedetailID']; ?>"><input type="number" id="sortOrder" name="sortOrder[]" value="<?php echo $val['sortOrder']; ?>"></td>
            <td><a onclick="delete_ot_template(<?php echo $val['templatedetailID']; ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" ></span></a></td>
        </tr>
        <?php
        $i=$i+1;
    }
}else{
    ?>
    <tr>
        <td colspan="3">No records found</td>
    </tr>
    <?php
}


?>
    </tbody>
</table>
</form>
<div class="row">
    <button type="button" style="margin-right: 15px"  class="btn btn-primary btn-sm pull-right"
            onclick="save_ot_template_sortorder()"><i class="fa fa-floppy-o"></i> Save
    </button>
</div>

<script>
    function save_ot_template_sortorder(){
        var postData = $('#general_ot_template_sort_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('OverTime/general_ot_template_sort_frm'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    over_time_template_table();
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>
