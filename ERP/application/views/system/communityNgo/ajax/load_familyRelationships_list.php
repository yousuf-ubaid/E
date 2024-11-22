<!--OrgChart-master-->
<link rel="stylesheet" href="<?php echo base_url('plugins/OrgChart-master/dist/css/jquery.orgchart.css'); ?>"/>
<style>
    table {
        border-collapse: inherit;
    }

    .link {
        color: #ffffff;
    }

    /* visited link */
    .link:visited {
        color: #ffffff;
    }

    /* mouse over link */
    .link:hover {
        color: #ffffff;
    }

    /* selected link */
    .link:active {
        color: #ffffff;
    }

    .orgchart .node .content {
        min-height: 0px;
        padding: 0px;
    }

    .orgchart td.left {
        border-left: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart td.top {
        border-top: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart td {
        text-align: center;
        vertical-align: top;
        padding: 0;
    }

    .orgchart td > .down {
        background-color: rgba(70, 155, 218, 0.8);
        margin: 0px auto;
        height: 20px;
        width: 2px;
    }

    .orgchart td.top {
        border-top: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart .node.focused {
        background-color: rgba(238, 217, 54, 0.5);
    }

    .orgchart .node {
        display: inline-block;
        position: relative;
        margin: 0;
        padding: 3px;
        border: 2px dashed transparent;
        text-align: center;
        width: 130px;
    }

    .node {
        transition: all 0.3s;
        opacity: 1;
        top: 0;
        left: 0;
    }

    .orgchart .node .second-menu {
        display: none;
        position: absolute;
        top: 0;
        right: -70px;
        border-radius: 35px;
        box-shadow: 0 0 10px 1px #999;
        background-color: #fff;
        z-index: 1;
    }

    .hidden {
        display: none !important;
    }

    .orgchart {
        display: inline-block;
        min-height: 202px;
        min-width: 202px;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: linear-gradient(90deg, rgba(200, 0, 0, 0.15) 10%, rgba(0, 0, 0, 0) 10%), linear-gradient(rgba(200, 0, 0, 0.15) 10%, rgba(0, 0, 0, 0) 10%);
        background-size: 10px 10px;
        border: 1px dashed rgba(0, 0, 0, 0);
        transition: border .3s;
        padding: 20px;
    }

    .orgchart > .spinner {
        font-size: 100px;
        margin-top: 30px;
        color: rgba(68, 157, 68, 0.8);
    }

    .orgchart table {
        border-spacing: 0;
    }

    .orgchart > table:first-child {
        margin: 20px auto;
    }

    .orgchart td {
        text-align: center;
        vertical-align: top;
        padding: 0;
    }

    .orgchart td.top {
        border-top: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart td.right {
        border-right: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart td.left {
        border-left: 2px solid rgba(70, 155, 218, 0.8);
    }

    .orgchart td > .down {
        background-color: rgba(70, 155, 218, 0.8);
        margin: 0px auto;
        height: 50px;
        width: 2px;
    }

    /* node styling */
    .orgchart .node {
        display: inline-block;
        position: relative;
        margin: 0;
        padding: 3px;
        border: 2px dashed transparent;
        text-align: center;
        width: 250px;
    }

    .orgchart .node > .spinner {
        position: absolute;
        top: calc(50% - 15px);
        left: calc(50% - 15px);
        vertical-align: middle;
        font-size: 30px;
        color: rgba(68, 157, 68, 0.8);
    }

    .orgchart .node:hover {
        background-color: rgba(238, 217, 54, 0.5);
        transition: .5s;
        cursor: default;
        z-index: 20;
    }

    .orgchart .node.focused {
        background-color: rgba(238, 217, 54, 0.5);
    }

    .orgchart .node.allowedDrop {
        border-color: rgba(68, 157, 68, 0.9);
    }

    .orgchart .node .title {
        /*position: relative;*/
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        height: 20px;
        line-height: 20px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background-color: rgba(70, 155, 218, 0.8);
        color: #fff;
        border-radius: 4px 4px 0 0;
    }

    .orgchart .node .title .symbol {
        float: left;
        margin-top: 4px;
        margin-left: 2px;
    }

    .orgchart .node .content {
        position: relative;
        /*width: 100%;*/
        font-size: 13px;
        line-height: 13px;
        padding: 2px;
        border: 2px solid rgba(70, 155, 218, 0.8);
        border-radius: 0 0 4px 4px;
        text-align: center;
        background-color: #fff;
        color: #333;
        overflow: hidden;
    }

    .orgchart .node .edge {
        font-size: 15px;
        position: absolute;
        color: rgba(68, 157, 68, 0.5);
        cursor: default;
        transition: .2s;
        -webkit-transition: .2s;
    }

    .orgchart .edge:hover {
        color: #449d44;
        cursor: pointer;
    }

    .orgchart .node .verticalEdge {
        width: calc(100% - 10px);
        width: -webkit-calc(100% - 10px);
        width: -moz-calc(100% - 10px);
        left: 5px;
    }

    .orgchart .node .topEdge {
        top: -4px;
    }

    .orgchart .node .bottomEdge {
        bottom: -4px;
    }

    .orgchart .node .horizontalEdge {
        width: 15px;
        height: calc(100% - 10px);
        height: -webkit-calc(100% - 10px);
        height: -moz-calc(100% - 10px);
        top: 5px;
    }

    .orgchart .node .rightEdge {
        right: -4px;
    }

    .orgchart .node .leftEdge {
        left: -4px;
    }

    .orgchart .node .horizontalEdge::before {
        position: absolute;
        top: calc(50% - 7px);
        top: -webkit-calc(50% - 7px);
        top: -moz-calc(50% - 7px);
    }

    .orgchart .node .rightEdge::before {
        right: 3px;
    }

    .orgchart .node .leftEdge::before {
        left: 3px;
    }

    .orgchart .node .edge.fa-chevron-up:hover {
        transform: translate(0, -4px);
        -webkit-transform: translate(0, -4px);
    }

    .orgchart .node .edge.fa-chevron-down:hover {
        transform: translate(0, 4px);
        -webkit-transform: translate(0, 4px);
    }

    .orgchart .node .edge.fa-chevron-right:hover {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .orgchart .node .edge.fa-chevron-left:hover {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .orgchart .node .edge.fa-chevron-right:hover ~ .fa-chevron-left {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .orgchart .node .edge.fa-chevron-left:hover ~ .fa-chevron-right {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .rightEdgeMoveRight {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .rightEdgeMoveLeft {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .oc-export-btn {
        display: inline-block;
        position: absolute;
        right: 5px;
        top: 5px;
        padding: 4px 12px;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        color: #fff;
        background-color: #34495e;
        border: 1px solid transparent;
        border-color: #34495e;
        border-radius: 4px;
    }

    .oc-export-btn[disabled] {
        cursor: not-allowed;
        filter: alpha(opacity=30);
        -webkit-box-shadow: none;
        box-shadow: none;
        opacity: 0.3;
    }

    .oc-export-btn:hover, .oc-export-btn:focus, .oc-export-btn:active {
        background-color: #3f5872;
        border-color: #3f5872;
    }

    .orgchart ~ .mask {
        position: absolute;
        top: 0px;
        right: 0px;
        bottom: 0px;
        left: 0px;
        z-index: 999;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.3);
    }

    .orgchart ~ .mask .spinner {
        position: absolute;
        top: calc(50% - 54px);
        left: calc(50% - 54px);
        color: rgba(255, 255, 255, 0.8);
        font-size: 108px;
    }

    .node {
        transition: all 0.3s;
        webkit-transition: all 0.3s;
        opacity: 1;
        top: 0;
        left: 0;
    }

    .slide-down {
        opacity: 0;
        top: 42px;
    }

    .slide-up {
        opacity: 0;
        top: -42px;
    }

    .slide-right {
        opacity: 0;
        left: 130px;
    }

    .slide-left {
        opacity: 0;
        left: -130px;
    }

    .orgchart .second-menu-icon {
        transition: opacity .5s;
        opacity: 0;
        right: -5px;
        top: -5px;
        z-index: 2;
        color: rgba(68, 157, 68, 0.5);
        font-size: 18px;
        position: absolute;
    }

    .orgchart .second-menu-icon:hover {
        color: #449d44;
    }

    .orgchart .node:hover .second-menu-icon {
        opacity: 1;
    }

    .orgchart .node .second-menu {
        display: none;
        position: absolute;
        top: 0;
        right: -70px;
        border-radius: 35px;
        box-shadow: 0 0 10px 1px #999;
        background-color: #fff;
        z-index: 1;
    }

    .orgchart .node .second-menu .avatar {
        width: 60px;
        height: 60px;
        border-radius: 30px;
    }

    .orgchart {
        background: #fff;
    }

    a:hover, a:focus {
        color: #23527c;
        text-decoration: underline;
    }

    #chart-container {
        max-height: 260px;
        overflow-y: scroll;
    }
</style>

<?php
if (!empty($famArray)) {

      $FamMasterID= $famArray['master']['FamMasterID'];

$company_id = current_companyID();
$page = $this->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID=533 AND companyID={$company_id}
      
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');
$familyNames = array();

    $outrootemp = $this->db->query('SELECT CName_with_initials,FamilyName,FamMasterID FROM srp_erp_ngo_com_communitymaster left join srp_erp_ngo_com_familymaster  ON srp_erp_ngo_com_communitymaster.Com_MasterID = srp_erp_ngo_com_familymaster.LeaderID  WHERE srp_erp_ngo_com_familymaster.FamMasterID = "' . $FamMasterID . '"')->row_array();

    $famCom_MasterID = array();
    $queryFP4 = $this->db->query("SELECT  Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE srp_erp_ngo_com_familydetails.FamMasterID = '" . $FamMasterID . "'");
    $rowFP4 = $queryFP4->result();
    foreach ($rowFP4 as $resFP4) {
        $famCom_MasterID[] = $resFP4->Com_MasterID;

    }

    $delFamilyMem = "'".implode("', '", $famCom_MasterID)."'";



   //$delFamilyMem1 = $this->db->query("SELECT srp_erp_ngo_com_familydetails.FamMasterID FROM srp_erp_ngo_com_familydetails left join srp_erp_ngo_com_communitymaster  ON srp_erp_ngo_com_familydetails.Com_MasterID = srp_erp_ngo_com_communitymaster.Com_MasterID  WHERE srp_erp_ngo_com_familydetails.FamMasterID != '" . $FamMasterID . "' and Com_MasterID IN($delFamilyMem)")->result_array();

    $output = $this->db->query("SELECT
		CONCAT(srp_erp_ngo_com_familymaster.FamilyName,'~',srp_erp_ngo_com_communitymaster.CName_with_initials,'~',srp_erp_ngo_com_familydetails.FamMasterID,'~',if(srp_erp_ngo_com_familydetails.FamMasterID != '',srp_erp_ngo_com_familydetails.FamMasterID,'-')) AS FamMasterID5
	FROM srp_erp_ngo_com_familydetails 
	
	left join srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID = srp_erp_ngo_com_familydetails.Com_MasterID

	left join srp_erp_ngo_com_familymaster  ON srp_erp_ngo_com_familymaster.FamMasterID = srp_erp_ngo_com_familydetails.FamMasterID

	WHERE srp_erp_ngo_com_familydetails.FamMasterID != '" . $FamMasterID . "' and srp_erp_ngo_com_familydetails.Com_MasterID IN($delFamilyMem) and srp_erp_ngo_com_familydetails.isDeleted=0 ")->result_array();



    if (!empty($output)) {
        foreach ($output as $row) {

            $familyNames[str_replace("'", "", $row['FamMasterID5'])][] = $row['FamMasterID5'];

        }
    }

    $data = "";
    foreach ($familyNames as $k1 => $v1) {

        if (!empty($k1)) {
            $val1 = explode("~", $k1);
            $data .= "{ 'id': \"" . $val1[3] . "\",'name': '<a class=\'link\' href=\'#\' rel=\'tooltip\' title=\'" . htmlentities($val1[0], ENT_QUOTES) . "\' onClick=\"loadFamilyCreate(\'" . $val1[2] . "\',\'" . htmlentities($val1[0], ENT_QUOTES) . "\',\'" . $FamMasterID . "\')\">" . htmlentities($val1[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val1[1]) . "',";
            /*echo "<pre>";
            print_r($v1);
            echo "</pre>";*/
            $test1 = array_values($v1);
            unset($v1['']);
            if (!empty($v1)) {
                $data .= "'children': [";
                foreach ($v1 as $k2 => $v2) {
                    if (!empty($k2)) {
                        $val2 = explode("~", $k2);
                        $data .= "{ 'id': \"" . $val2[3] . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val2[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadFamilyCreate(\'" . $val2[2] . "\',\'" . htmlentities($val2[0], ENT_QUOTES) . "\',\'" . $FamMasterID . "\')\">" . htmlentities($val2[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val2[1]) . "',";
                        $test2 = array_values($v2);
                        unset($v2['']);

                        if ($v2) {
                            $data .= "'children': [";
                            foreach ($v2 as $k3 => $v3) {
                                if (!empty($k3)) {
                                    $val3 = explode("~", $k3);
                                    $data .= "{ 'id': \"" . $val3[3] . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val3[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadFamilyCreate(\'" . $val3[2] . "\',\'" . htmlentities($val3[0], ENT_QUOTES) . "\',\'" . $FamMasterID . "\')\">" . htmlentities($val3[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val3[1]) . "',";
                                    $test3 = array_values($v2);
                                    unset($v3['']);
                                    if ($v3) {
                                        $data .= "'children': [";
                                        foreach ($v3 as $k4 => $v4) {
                                            if (!empty($k4)) {
                                                $val4 = explode("~", $k4);
                                                $data .= "{ 'id': \"" . $val4[3] . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val4[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadFamilyCreate(\'" . $val4[2] . "\',\'" . htmlentities($val4[0], ENT_QUOTES) . "\',\'" . $FamMasterID . "\')\">" . htmlentities($val4[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val4[1]) . "'},";
                                            }
                                        }
                                        $data .= "]";
                                    }
                                    $data .= "},";
                                }
                            }
                            $data .= "]";
                        }
                        $data .= "},";
                    }
                }
                $data .= "]";
            }
            $data .= "},";
        }
    }
}
?>
<div id="chart-container" style="overflow: auto"></div>

<!--OrgChart-master-->
<script src="<?php echo base_url('plugins/OrgChart-master/dist/js/jquery.orgchart.js'); ?>"></script>
<script>
    $(document).ready(function () {
        var datascource = {
            'id': '<?php echo $outrootemp['FamMasterID'] ?>',
            'name': '<?php echo str_replace("'", "", $outrootemp['FamilyName']) ?>',
            'title': 'Head Of The Family : <?php echo $outrootemp['CName_with_initials'] ?>',
            <?php
            if($data)
            {
            ?>
            'children': [
                <?php echo $data ?>
            ]
            <?php
            }
            ?>
        };

        $('#chart-container').orgchart({
            'data': datascource,
            'depth': 5,
            'nodeTitle': 'name',
            'nodeContent': 'title',
            'exportButton': true,
            'exportFilename': 'Family Relationship List',
            'nodeID': 'id',
            'createNode': function ($node, data) {
                var secondMenuIcon = $('<i>', {
                    'class': 'fa fa-info-circle second-menu-icon',
                    hover: function () {
                        $(this).siblings('.second-menu').toggle();
                    }
                });
                var secondMenu = '<div class="second-menu"><img class="avatar" src="../images/users/' + data.id + '"></div>';
                $node.append(secondMenuIcon).append(secondMenu);

            }
        });
        $('.oc-export-btn').addClass('hidden');
        $('[rel="tooltip"]').tooltip();
    });

    function loadFamilyCreate(FamMasterID, FamilyName,FamMasterIDs) {

        fetchPage('<?php echo $page ?>', FamMasterID, 'View Family', 1, FamMasterIDs);

    }
</script>