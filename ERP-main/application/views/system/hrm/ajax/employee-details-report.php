<?php if($isForPrint == 'Y') { ?>
    <div class="table-responsive">
        <table style="width: 100%" border="0px">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;" valign="top">
                    <table border="0px">
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong>Employee Details</strong>
                                </h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
<?php } ?>
<div class="row" style="margin-top: 2%">
    <?php if($isForPrint != 'Y') {
        $companyTitle = "'<h3>".$this->common_data['company_data']['company_name']."</h3>";
        $companyTitle .= "<h4>Employee details</h4>'";
    ?>
    <div class="col-sm-12">
        <div class="pull-right">
        <button type="button" class="btn btn-excel btn-sm hideBtn pull-right-btn" id="printBtn" onclick="excel_btn()"
                style=""> <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </button>
        &nbsp;
        <button type="button" class="btn btn-primary btn-sm hideBtn pull-right-btn" id="printBtn" onclick="print_btn()"
                style=""> <?php echo $this->lang->line('common_print');?><!--Print-->
        </button>
        </div>
    </div>
    <?php } ?>
    <div class="col-sm-12">&nbsp;</div>

    <div class="col-sm-12">
        <div style="height: 400px">
            <table id="report-tb" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <?php
                    foreach($columnTitle as $title){
                        echo '<th>'.$title['columnTitle'].'</th>';
                    }
                    ?>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach($detail as $row){
                    echo '<tr>';
                    foreach($columns as $column_row){
                        echo '<td>'.$row[$column_row].'</td>';
                    }
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('#report-tb').tableHeadFixer({
        head: true,
        foot: true,
        left: 3,
        right: 0,
        'z-index': 0
    });
</script>
<?php
