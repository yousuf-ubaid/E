<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
global $typeDropdown;
$typeDropdown=group_structure_type();


?>


<table id="cTable" class="table " style="width: 100%">
    <thead>
    <tr>
        <th>Company</th>
        <th style="text-align: left">Type</th>

        <th><div class="pull-right"><button onclick="pullcompanies()" class="btn btn-primary btn-xs">Add Companies</div></th>

    </tr>
    </thead>
    <tbody>
    <?php  $group = $this->db->query("SELECT companyGroupID,description,groupCode,reportingTo FROM srp_erp_companygroupmaster ORDER BY reportingTo ASC")->result_array();
    $companies = $this->db->query("SELECT
	companyGroupID,company_code,company_name,srp_erp_companygroupdetails.companyID,typeID,companyGroupDetailID,description
FROM
	srp_erp_companygroupdetails
	LEFT JOIN srp_erp_groupstructuretype ON typeID=groupStructureTypeID
	INNER JOIN srp_erp_company ON srp_erp_companygroupdetails.companyID = srp_erp_company.company_id")->result_array();
    function buildTree(array $elements, $parentId = 0,$i=0, array $companies,$typeDropdown) {
        $branch = array();
        if($parentId !=0){
            $i+=50;
        }
        foreach ($elements as $element) {
            if ($element['reportingTo'] == $parentId) {
                ?>
                <tr class="subheader ">
                    <td style="padding-left: <?php echo $i?>px"><i class="fa fa-minus" aria-hidden="true"></i> <?php echo $element['groupCode'] ?>
                        | <?php echo $element['description'] ?></td>
                    <td></td>
                    <td style="text-align: right">&nbsp; <span style="cursor: pointer" onclick="editGroup(<?php echo $element['companyGroupID']?>,'<?php echo $element["groupCode"] ?>','<?php echo  $element["description"] ?>')"><i style="color: #0d6aad" class="fa fa-edit"></i></span</td>
                </tr>




                <?php

                $keys = array_keys(array_column($companies, 'companyGroupID'), $element['companyGroupID']);
                $company = array_map(function ($k) use ($companies) {
                    return $companies[$k];
                }, $keys);

                if (!empty($company)) {
                    foreach ($company as $c) {
                        ?>

                        <tr class="">


                            <td style="padding-left: 200px"><?php echo $c['company_code'] ?> | <?php echo $c['company_name'] ?></td>
                            <td style="width: 100px">  <?php echo form_dropdown('typeID', $typeDropdown, $c['typeID'], 'class=" select2" onchange="updatefield(this.value,'.$c['companyGroupDetailID'].')" id="companyID" required"'); ?>  </td>
                            <td style="width: 100px;text-align: right"><span style="cursor: pointer" onclick="modalOpen(<?php echo $c['companyID']?>)"><i style="color: #0d6aad" class="fa fa-plus"></i></span> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <span style="cursor: pointer" onclick="modalview(<?php echo $c['companyID']?>)"><i style="color: #0d6aad" class="fa fa-eye"></i></span></td>


                        </tr>


                        <?php
                    }
                }
                ?>
                <?php

                $children = buildTree($elements, $element['companyGroupID'],$i,$companies,$typeDropdown);
                if ($children) {
                    $element['children'] = $children;

                    ?>

                    <?php
                }
                ?>

                <?php

                $branch[] = $element;
            }
        }

        return $branch;
    }
    $i=0;

    $tree = buildTree($group,0,$i,$companies,$typeDropdown);



    ?>



    </tbody>
</table>
