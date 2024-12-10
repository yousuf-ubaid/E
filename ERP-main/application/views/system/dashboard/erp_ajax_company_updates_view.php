<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h4 style="font-size: 20px; margin: 0;"><?= htmlspecialchars($companyUpdateDetail['title']) ?></h4>    
        <p class="text-muted" id="expiryDate" style="font-size: 13px; margin: 0;">
            Expiry Date: <?= htmlspecialchars($companyUpdateDetail['expiryDate']) ?>
        </p>
    </div>
    <br>
    <p><?= nl2br(htmlspecialchars($companyUpdateDetail['description'])) ?></p>
    <br>
    <?php if (!empty($attachments)): ?> 
        <h5 style="font-weight: bold;">Attachments</h5>
        <div id="attachmentsContainer">
            <ul class="mailbox-attachments clearfix">
                <?php foreach ($attachments as $attachment): ?>
                    <li>
                        <span class="mailbox-attachment-icon">
                            <?php
                            if (strpos($attachment['iconClass'], 'image') !== false): ?>
                                <img src="<?= $attachment['myFileName']; ?>" 
                                alt="Image preview" 
                                style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <i class="<?= $attachment['iconClass'] ?>"></i>
                            <?php endif; ?>
                        </span>
                        <div class="mailbox-attachment-info">
                            <a href="javascript:void(0);" 
                            class="mailbox-attachment-name" 
                            onclick="downloadAttachment('<?= $attachment['myFileName']; ?>')">
                                <i class="<?= $attachment['iconClass'] ?>"></i>
                                <?= htmlspecialchars($attachment['attachmentDescription'] ?? basename($attachment['myFileName'])) ?>
                            </a>
                            <span class="mailbox-attachment-size">
                                <?= round($attachment['fileSize'] / 1024, 2) . ' KB'; ?>
                                <a href="javascript:void(0);" 
                                class="btn btn-default btn-xs pull-right" 
                                onclick="downloadAttachment('<?= $attachment['myFileName']; ?>')">
                                    <i class="fa fa-cloud-download"></i>
                                </a>
                            </span>
                        </div>
                    </li> 
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<script>

    function downloadAttachment(fileName) {
        const tempLink = document.createElement('a');
        tempLink.href = fileName;
        tempLink.setAttribute('download', '');
        document.body.appendChild(tempLink);
        tempLink.click(); 
        document.body.removeChild(tempLink); 
    }
    
</script>