<?php
$templates = array_group_by($templates, 'FormCatID');
?>
<style>
    .td-formCat > .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single{
        height: 28px !important;
        padding: 3px 12px;
    }
</style>
<div class="" style="height: 400px; overflow-y: scroll">
    <table class="<?=table_class()?>" id="tbl-default-template">
        <thead>
            <tr>
                <th>#</th>
                <th>Page Description</th>
                <th style="z-index: 10">Template</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $i = 1;
        foreach($templates as $fromCatID=>$row){        
            $this_tmp = []; 
            $selected = $row[0]['curntTemplate'];
            foreach($row as $t){                 
                $this_tmp[ $t['TempMasterID'] ] = $t['TempPageName'].' | '.$t['TempPageNameLink'];
            }

            echo '<tr>
                    <td style="width: 15px; vertical-align: middle">'.$i.'</td>
                    <td style="width: 170px; vertical-align: middle">'.$row[0]['TempDes'].'</td>
                    <td class="td-formCat">'.
                    form_dropdown('formCat['.$fromCatID.']', $this_tmp, $selected, 'class="form-control nav-drop"')
                    .'</td>
                </tr>';

            $i++;
        }    
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript" src="<?=base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>

<script>    
    $('.nav-drop').select2();
    //$('#tbl-default-template').DataTable();

    $('#tbl-default-template').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 0
    });
</script>