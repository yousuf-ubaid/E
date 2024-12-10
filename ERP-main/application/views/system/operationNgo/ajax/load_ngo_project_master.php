<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="table-responsive">
    <table id="" class="table" style="">
        <thead>
        <tr>
            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
            <th style="width:70px"></th>
            <!-- <th style="width:20px">Edit</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        $CI = get_instance();
        if ($master) {
            foreach ($master as $value) {
                if (trim($value['masterID'] ?? '') == 0) {
                    ?>
                    <tr class="header">
                        <td style="">
                            <a class="link-person noselect" href="#" onclick="fetchPage('system/operationNgo/project_edit_view','<?php echo $value['ngoProjectID'] ?>','View Project','CRM')"><i class="fa fa-minus-square" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $value['documentSystemCode'] . " - " . $value['description'] ?></a>
                        </td>
                        <td><span><a href="#" onclick="project_SubCategoryAdd(<?php echo $value['ngoProjectID'] ?>,'<?php echo $value['description'] ?>')">
                                    <button type="button" class="btn btn-xs btn-default"><span
                                            class="glyphicon glyphicon-plus"
                                            style="color:green;"></span>
                                    </button>
                                </a></span><span class="pull-right"><a href="#" onclick="fetchPage('system/operationNgo/create_project','<?php echo $value['ngoProjectID'] ?>','Edit Sub Project')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>
                        </td>
                    </tr>
                    <?php
                    $subtable = $CI->db->query("SELECT ngoProjectID,projectName,documentSystemCode,masterID FROM srp_erp_ngo_projects WHERE masterID={$value['ngoProjectID']} ORDER BY ngoProjectID DESC ")->result_array();
                    if ($subtable) {
                        foreach ($subtable as $row) {
                            if(trim($row['masterID'] ?? '')==trim($value['ngoProjectID'] ?? '')) {
                                ?>
                                <tr>
                                    <td style="padding-left: 30px"><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<?php echo $row['documentSystemCode'] . " - " .$row['projectName'] ?>
                                    </td>
                                    <td><span class="pull-right"><a href="#" onclick="project_SubCategoryEdit(<?php echo $row['ngoProjectID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
            }
        }
        ?>
        </tbody>
    </table>
</div>