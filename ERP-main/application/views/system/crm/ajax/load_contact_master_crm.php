<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<style>
   
</style>
<?php
if (!empty($header)) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <div class="col-md-4" style="margin-top: 2%;">
                    <div class="post-doc">
                        <div class="post-doc-left">
                            <div class="modal-body">
                                <?php if($val['contactImage'] != ''){ ?>
                                    <img class="align-left" src="<?php echo base_url('uploads/crm/profileimage/'.$val['contactImage']); ?>"  width="70" height="80">
                                    <?php
                                } else { ?>
                                    <img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>" alt=""  width="70" height="80">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="post-doc-right">
                            <div class="post-doc-right_body">
                                <b style="font-size: 14px">
                                    <?php if(!empty( $val['namecontactfull']))
                                    {
                                        echo $val['namecontactfull'];
                                    }else {
                                        echo '-';
                                    }
                                    ?></b><br>
                          <b style="font-size: 14px">
                                    <?php if(!empty( $val['email']))
                                    {
                                        echo $val['email'];
                                    }else {
                                        echo '-';
                                    }
                                    ?></b>

                            </div>
                            <div class="post-doc-right_footer">Created By : <?php echo $val['createdUserName'] ?></div>
                        </div>
                    </div>
                </div>
                <?php
                $x++;
            }
            ?>
        </div>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_organization_to_display');?>.</div><!--THERE ARE NO ORGANIZATION TO DISPLAY-->
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>