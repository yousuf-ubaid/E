<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$typeDropdown=group_structure_type();
//$company_groupmaster_dropdown=company_groupmaster_dropdown();
$title = $this->lang->line('finance_group_structure');
echo head_page($title, false);

?>

<style>

    #cTable tbody tr.highlight td {
        background-color: #FFEE58;
    }

    /**
     * Framework starts from here ...
     * ------------------------------
     */
    .tree,
    .tree ul {
        margin: 0 0 0 1em; /* indentation */
        padding: 0;
        list-style: none;
        color: #cbd6cc;
        position: relative;
    }

    .tree ul {
        margin-left: .5em
    }

    /* (indentation/2) */

    .tree:before,
    .tree ul:before {
        content: "";
        display: block;
        width: 0;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        border-left: 1px solid;
    }

    .tree li {
        margin: 0;
        padding: 0 1.5em; /* indentation + .5em */
        line-height: 2em; /* default list item's `line-height` */
        font-weight: bold;
        position: relative;
        font-size: 11px
    }

    .tree li:before {
        content: "";
        display: block;
        width: 10px; /* same with indentation */
        height: 0;
        border-top: 1px solid;
        margin-top: -1px; /* border top width */
        position: absolute;
        top: 1em; /* (line-height/2) */
        left: 0;
    }

    .tree li:last-child:before {
        background: white; /* same with body background */
        height: auto;
        top: 1em; /* (line-height/2) */
        bottom: 0;
    }

    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 11px;
        background-color: #fbfbfb;
    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 11px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;
        /* color:#555;*/
    }


</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">

        </div>
    <hr>
    <div class="col-md-12">
        <div id="loadstructurePage">



            <table id="cTable" class="table " style="width: 100%">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('common_company')?></th>
                    <th style="text-align: left"><?php echo $this->lang->line('common_type')?></th>
                    <th></th>

                </tr>
                </thead>
                <tbody>

            <?php
            $group = $this->db->query("SELECT companyGroupID,description,groupCode,reportingTo FROM srp_erp_companygroupmaster ORDER BY reportingTo ASC")->result_array();
            $companies = $this->db->query("SELECT
	companyGroupID,company_code,company_name,srp_erp_companygroupdetails.companyID,typeID,companyGroupDetailID,description
FROM
	srp_erp_companygroupdetails
	LEFT JOIN srp_erp_groupstructuretype ON typeID=groupStructureTypeID
	INNER JOIN srp_erp_company ON srp_erp_companygroupdetails.companyID = srp_erp_company.company_id")->result_array();
            function buildTree(array $elements, $parentId = 0,$i=0, array $companies) {
                $branch = array();
                if($parentId !=0){
                    $i+=50;
                }
                foreach ($elements as $element) {
                    if ($element['reportingTo'] == $parentId) {
                        ?>
                        <tr class="subheader ">
                            <th style="padding-left: <?php echo $i?>px"><i class="fa fa-minus" aria-hidden="true"></i> <?php echo $element['groupCode']?> | <?php echo $element['description']?></th>
                            <th></th>
                            <th></th>
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


                                    <td style="padding-left: 400px;width: 200px"><?php echo $c['company_code'] ?> | <?php echo $c['company_name'] ?></td>
                                    <td style="width: 100px">  <?php echo $c['description'] ?>  </td>
                                    <td style="width: 100px"> <span style="cursor: pointer" onclick="modalview(<?php echo $c['companyID']?>)"><i style="color: #0d6aad" class="fa fa-eye"></i></span></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <?php

                        $children = buildTree($elements, $element['companyGroupID'],$i,$companies);
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

            $tree = buildTree($group,0,$i,$companies);



            ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>







<div class="modal fade" id="groupStructureView" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"></h3>
            </div>

            <div class="modal-body" id="htmlView">




            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>

            </div>

        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    function modalview(companyID){
        $('#groupStructureView').modal('show');
        company_ID = companyID;
        loadPageView();
    }

    function loadPageView(){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: company_ID,hide:true},
            url: '<?php echo site_url('Group_structure/get_groupStructure'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#htmlView').html(data);


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    </script>