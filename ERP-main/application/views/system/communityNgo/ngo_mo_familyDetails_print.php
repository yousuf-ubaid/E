<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
                              echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('CommunityNgo_com_families');?><!--Community Family--> - <?php echo $extra['master']['FamilyName']; ?></h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:70%;">
        <table class="table table-striped" id="profileInfoTable"
               style="background-color: #ffffff;width: 100%">
            <tbody>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Head Of The Family :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['CName_with_initials']; ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Full Name :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['CFullName']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Date of Birth :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['CDOB'] ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Gender :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['name'] ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">N.I.C :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['CNIC_No']; ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Marital Status :</strong>
                </td>
                <td>
                    <?php
                    echo $extra['master']['maritalstatus'];
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Phone (Primary) :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['TP_Mobile']; ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Phone (Secondary) :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['TP_home']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Contact Address :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['C_Address'] ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">House No :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['HouseNo'] ?>
                </td>
            </tr>
            <tr>
                <td><strong style="color: #638bbe;">Email :</strong></td>
                <td>
                    <?php echo $extra['master']['EmailID'] ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Permanent Address :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['P_Address'] ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Area :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['arDescription'] ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">GS Division :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['diviDescription']. ' - ' .$extra['master']['GS_No'] ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong style="color: #638bbe;">Reference No :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['LedgerNo'] ?>
                </td>
                <td>
                    <strong style="color: #638bbe;">Ledger No :</strong>
                </td>
                <td>
                    <?php echo $extra['master']['FamilySystemCode'] ?>
                </td>
            </tr>

            </tbody>
        </table>
            </td>
            <td style="width:30%;">
                <table>
                    <tr style="text-align: center;">
                        <td colspan="3">
            <?php if ($extra['master']['CImage'] != '') { ?>
                <img src="<?php echo base_url('uploads/NGO/communitymemberImage/' . $extra['master']['CImage']); ?>"
                     id="changeImg" style="width: 180px; height: 145px;">
                <?php
            } else { ?>
                <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                     style="width:180px; height: 145px;">
            <?php } ?>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>

<h6 style="font-weight: bold"><?php echo $this->lang->line('CommunityNgo_family_members');?><!--Family Member Details--></h6>

<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="width: 5%">#</th>
            <th class='theadtr' style="width: 21%"><?php echo $this->lang->line('communityngo_name_of_member');?><!--Family Member--></th>
            <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('communityngo_gender');?><!--Gender--></th>
            <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('communityngo_dob');?><!--DOB--></th>
            <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('communityngo_relationship');?><!--Relationship--></th>
            <th class='theadtr' style="width: 13%"><?php echo $this->lang->line('CommunityNgo_famMem_AddedDate');?><!--Member Added Date--></th>
            <th class='theadtr' style="width: 18%"><?php echo $this->lang->line('communityngo_status');?><!--Current Status--></th>

        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php
          if (!empty($extra['family_mem'])) {
            for ($i=0; $i < count($extra['family_mem']); $i++) {
                if($extra['family_mem'][$i]['isMove']==1 ){ $moveStatus= '<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color: #00a5e6; display:inline-block;color: #00a5e6;" title="Moved To Another Family">m</span>'; } else{ $moveStatus=''; }
                if($extra['family_mem'][$i]['isActive'] ==1){ $activeState=''; } else{
                    if($extra['family_mem'][$i]['DeactivatedFor']==2){ $INactReson='Migrate';} else{$INactReson='Death';}
                    $activeState='<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color:red; display:inline-block;color: red;" title="The Member Is Inactive :'.$INactReson.'">a</span>';}

                echo '<tr>';
              echo '<td>'.($i+1).'</td>';
              echo '<td>'.$extra['family_mem'][$i]['CName_with_initials'].'&nbsp;&nbsp;' .$moveStatus.'&nbsp;&nbsp;'.$activeState.'</td>';
              echo '<td>'.$extra['family_mem'][$i]['name'].'</td>';
              echo '<td>'.$extra['family_mem'][$i]['CDOB'].'</td>';
              echo '<td>'.$extra['family_mem'][$i]['relationship'].'</td>';
              echo '<td>'.$extra['family_mem'][$i]['FamMemAddedDate'].'</td>';
              echo '<td>'.$extra['family_mem'][$i]['maritalstatus'].'</td>';

            }
          }else{
              $norec=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
          }

        ?>
        </tbody>
        <tfoot>
        <tr>

        </tr>
        </tfoot>
    </table>
</div>
<br>
<?php
if (!empty($extra['famHouseEnrl'])) { ?>
    <h6 style="font-weight: bold">FAMILY HOUSE ENROLLING DETAILS</h6>
<div class="table-responsive">
    <table class="<?php echo table_class(); ?>">
        <thead>
        <tr style="text-align: center;">
            <td class="theadtr" style="border-bottom: solid 1px #50749f;min-width: 4%;text-align:center;">#</td>
            <td class="theadtr" style="border-bottom: solid 1px #50749f;min-width: 12%;text-align:center;"><?php echo $this->lang->line('communityngo_famOwnType'); ?></td>
            <td class="theadtr" style="border-bottom: solid 1px #50749f;min-width: 12%;text-align:center;"><?php echo $this->lang->line('communityngo_famHouseType'); ?></td>
            <td class="theadtr" style="border-bottom: solid 1px #50749f;min-width: 10%;text-align:center;"><?php echo $this->lang->line('communityngo_famHouseSizeInPrch'); ?></td>
            <td class="theadtr" style="border-bottom: solid 1px #50749f;min-width: 27%;text-align:center;" colspan="6"><?php echo $this->lang->line('communityngo_famHouseFacilities'); ?></td>
        </tr>
        <tr>
            <td class="theadtr" style="font-size: 10px;" colspan="4"></td>
            <td class="theadtr" style="font-size: 10px;">Electricity</td>
            <td class="theadtr" style="font-size: 10px;">Water Supply</td>
            <td class="theadtr" style="font-size: 10px;">Toilet</td>
            <td class="theadtr" style="font-size: 10px;">Bathroom</td>
            <td class="theadtr" style="font-size: 10px;">Telephone</td>
            <td class="theadtr" style="font-size: 10px;">Kitchen</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $x = 1;
        $totEnrolled = 0;
        $totExitInFam = 0;

            if($extra['famHouseEnrl']['hEnrollingID']){

                $famOfExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_house_enrolling LEFT JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID LEFT JOIN srp_erp_ngo_com_communitymaster ON Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_ngo_com_house_ownership_master ON srp_erp_ngo_com_house_ownership_master.ownershipAutoID = srp_erp_ngo_com_house_enrolling.ownershipAutoID LEFT JOIN srp_erp_ngo_com_house_type_master ON srp_erp_ngo_com_house_enrolling.hTypeAutoID=srp_erp_ngo_com_house_type_master.hTypeAutoID WHERE srp_erp_ngo_com_house_enrolling.companyID={$extra['famHouseEnrl']['companyID']} AND FamHouseSt=1 AND srp_erp_ngo_com_house_enrolling.Link_hEnrollingID={$extra['famHouseEnrl']['hEnrollingID']} ");
                $familyEnExt = $famOfExist->result();

                ?>
                <tr>
                    <td class=""><?php echo $x; ?></td>
                    <td class=""><?php echo $extra['famHouseEnrl']['ownershipDescription']; ?></td>
                    <td class=""><?php echo $extra['famHouseEnrl']['hTypeDescription']; ?></td>
                    <td class=""><?php echo $extra['famHouseEnrl']['hESizeInPerches']; ?></td>
                    <td class=""><?php   if($extra['famHouseEnrl']['isHmElectric']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>
                    <td class=""><?php   if($extra['famHouseEnrl']['isHmWaterSup']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>
                    <td class=""><?php   if($extra['famHouseEnrl']['isHmToilet']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>
                    <td class=""><?php   if($extra['famHouseEnrl']['isHmBathroom']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>
                    <td class=""><?php  if($extra['famHouseEnrl']['isHmTelephone']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>
                    <td class=""><?php  if($extra['famHouseEnrl']['isHmKitchen']==1){ ?>
                            <span style="color: green;">Yes</span>
                        <?php } else{?>
                            <span style="color: red;">No</span>
                        <?php } ?>
                    </td>


                </tr>
                <?php
                if(!empty($familyEnExt)){
                    ?>
                    <tr style="background-color:#e5e5e5;">
                        <td colspan="10" style="font-weight: inherit;">With <?php echo $extra['famHouseEnrl']['FamilySystemCode'].' |'. $extra['famHouseEnrl']['FamilyName']; ?> Other Included Families</td>
                    </tr>
                    <?php

                    foreach ($familyEnExt as $familyEnl) {
                        ?>
                        <tr style="background-color: #c2d7ef;">
                            <td></td>
                            <td colspan="9"><?php echo $familyEnl->FamilySystemCode.' |'. $familyEnl->FamilyName; ?></td>
                        </tr>
                        <?php

                        $totExitInFam += 1;
                    }
                }
                ?>
                <?php
                $x++;
                $totEnrolled += 1;
            }

        ?>
        </tbody>
        <tfoot>
        <tr>

            <td class="text-left" colspan="10">
                <span style="color: #8b8b8b;font-size: 12px;">Total Enrolled Families : <?php echo ($totEnrolled + $totExitInFam); ?></span>


            </td>
        </tr>
        </tfoot>
    </table>
</div>
<?php
}
?>
<br>
<div class="table-responsive">
  <?php if($extra['master']['confirmedYN']){ ?>
                  <table style="width: 500px !important;">
                      <tbody>
                      <tr>
                          <td><b><?php echo $this->lang->line('CommunityNgo_electronically_confirmed_by');?><!--Electronically Confirmed By--> </b></td>
                          <td><strong>:</strong></td>
                          <td><?php echo $extra['master']['confirmedByName']; ?></td>
                      </tr>
                      <tr>
                          <td><b><?php echo $this->lang->line('CommunityNgo_electronically_confirmed_date');?><!--Electronically Confirmed Date--> </b></td>
                          <td><strong>:</strong></td>
                          <td><?php echo $extra['master']['confirmedDate']; ?></td>
                      </tr>
                      </tbody>
                  </table>
              <?php } ?>

</div>
<script>

    a_link=  "<?php echo site_url('CommunityNgo/load_community_family_confirmation'); ?>/<?php echo $extra['master']['FamMasterID'] ?>";

    $("#a_link").attr("href",a_link);
    $('.review').removeClass('hide');

</script>