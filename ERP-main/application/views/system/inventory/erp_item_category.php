<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$assignBuyersPolicy = getPolicyValues('ABFC', 'All');
?>

<div class="table-responsive">
    <table id="" class="table" style="">
        <thead>
        <tr>

            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width:15%">Action</th>
            <!-- <th style="width:20px">Edit</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        $CI=get_instance();

       /* $table=$CI->db->query("select itemCategoryID,description,masterID,revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID from srp_erp_itemcategory where masterID={$pageID} order by itemCategoryID desc ")->result_array();*/
        if($table){
            foreach($table as $value) {
                if (trim($value['masterID'] ?? '') == trim($pageID)) {

                   // $depMaster = $this->db->query("SELECT itemCategoryID,categoryTypeID,masterID FROM `srp_erp_itemcategory` WHERE `itemCategoryID` = '{$pageID}'")->row_array();

                    if ($depMaster['categoryTypeID'] != 3) {
                        /*      $test = 'itemCategoryID,revenueGL,costGL,assetGL';*/
                        $param = $value['itemCategoryID'] . ',' . $value['revenueGL'] . ',' . $value['costGL'] . ',' . $value['assetGL'];
                    } else {
                        /*     $test = 'itemCategoryID,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID';*/
                        $param = $value['itemCategoryID'] . ',' . $value['faCostGLAutoID'] . ',' . $value['faACCDEPGLAutoID'] . ',' . $value['faDEPGLAutoID'];
                    }

                    ?>
                    <tr class="header">

                        <td style=""><i class="fa fa-minus-square"
                                        aria-hidden="true"></i> <?php echo $value['description'] ?></td>
                        <!--   <td></td>-->
                        <td>
                                <span class="pull-right">
                                    <a onclick="subsubcategory(<?php echo $value['itemCategoryID'] ?>),resetform()">
                                        <button
                                            type="button" class="btn btn-xs btn-default">
                                            <span
                                            class="glyphicon glyphicon-plus"
                                            style="color:green;"></span>
                                        </button>
                                    </a>&nbsp;&nbsp;
                                    <!-- </span> -->
                                    <!-- //&nbsp;&nbsp; -->
                                    <!-- <span class="pull-right"> -->
                                        <a
                                            onclick="opensubcategoryedit(<?php echo $value['itemCategoryID'] ?>)">
                                            <span title="Edit" class="glyphicon glyphicon-pencil" style="" rel="tooltip"></span>
                                        </a> &nbsp;&nbsp;
                                    <!-- </span> -->

                                    <?php if ($assignBuyersPolicy ==1 ) { ?>
                                    <!-- <span class="pull-right" -->
                                               
                                        <a onclick="open_buyers_model(<?php echo $value['itemCategoryID'] ?>,1)">
                                        <span title="Add Buyers" class="glyphicon glyphicon-user" style="" rel="tooltip"></span>
                                        </a>
                                    <?php } ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                    $subtable = $CI->db->query("select itemCategoryID,description,masterID,revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID from srp_erp_itemcategory where masterID={$value['itemCategoryID']} order by itemCategoryID desc ")->result_array();
                    if ($subtable) {
                        foreach ($subtable as $val) {
                            if(trim($val['masterID'] ?? '')==trim($value['itemCategoryID'] ?? '')) {
                                ?>
                                <tr>

                                    <td style="padding-left: 30px"><?php echo $val['description'] ?></td>
                                    <!--   <td></td>-->
                                    <td>
                                        <span class="pull-right">
                                        <a onclick="subsubcategory(<?php echo $val['itemCategoryID'] ?>),resetform()">
                                            <button
                                                type="button" class="btn btn-xs btn-default">
                                                <span
                                                class="glyphicon glyphicon-plus"
                                                style="color:green;"></span>
                                            </button>
                                        </a>&nbsp;&nbsp;
                                            <a onclick="opensubsubcategoryedit(<?php echo $val['itemCategoryID'] ?>)">
                                                <span title="Edit" class="glyphicon glyphicon-pencil" rel="tooltip"></span>
                                            </a>
                                            <?php if ($assignBuyersPolicy ==1 ) { ?>
                                                &nbsp;&nbsp;
                                                <a onclick="open_buyers_model(<?php echo $val['itemCategoryID'] ?>,2)">
                                                    <span title="Add Buyers" class="glyphicon glyphicon-user" style="" rel="tooltip"></span>
                                                </a>
                                            <?php } ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                $subsubtable = $CI->db->query("select itemCategoryID,description,masterID,revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID from srp_erp_itemcategory where masterID={$val['itemCategoryID']} order by itemCategoryID desc ")->result_array();
                                if ($subsubtable) {
                                    foreach ($subsubtable as $subsub) {
                                        if(trim($subsub['masterID'] ?? '')==trim($val['itemCategoryID'] ?? '')) {
                                            ?>
                                            <tr>
                                                <td style="padding-left: 60px"><?php echo $subsub['description'] ?></td>
                                                <td>
                                                    <span class="pull-right">
                                                        <a onclick="opensubsubcategoryedit(<?php echo $subsub['itemCategoryID'] ?>)">
                                                        <span title="Edit" class="glyphicon glyphicon-pencil" rel="tooltip"></span>
                                                        </a>
                                                        <?php if ($assignBuyersPolicy ==1 ) { ?>
                                                            &nbsp;&nbsp;
                                                            <a onclick="open_buyers_model(<?php echo $subsub['itemCategoryID'] ?>,2)">
                                                                <span title="Add Buyers" class="glyphicon glyphicon-user" rel="tooltip"></span>
                                                            </a>
                                                        <?php } ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
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