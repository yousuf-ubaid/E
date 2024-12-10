<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

//print_r($master);exit;
?>
<div class="chats">

    <?php if($master){ ?>
        <?php foreach($master as $val){ ?>
            <?php if($val['isSrm']==0){ ?>
            <div class="chat">
                <div class="chat-avatar">
                    <a class="avatar avatar-online" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="June Lane">
                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="...">
                    <i></i>
                    </a>
                </div>
                <div class="chat-body">
                    <div class="chat-content">
                    <p>
                    <?php echo $val['message'] ?>
                    </p>
                    <time class="chat-time" datetime="2015-07-01T11:37"><?php echo $val['createdDateTime'] ?></time>
                    </div>
                </div>
            </div>
            <?php }else{ ?>
                <div class="chat chat-left">
                    <div class="chat-avatar">
                        <a class="avatar avatar-online" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="Edward Fletcher">
                        <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="...">
                        <i></i>
                        </a>
                    </div>
                    <div class="chat-body">
                        <div class="chat-content">
                        <p> <?php echo $val['message'] ?></p>
                        <time class="chat-time" datetime="2015-07-01T11:39"><?php echo $val['createdDateTime'] ?></time>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    
</div>