<?php
use OCP\Util;

$currentView = isset($_['view']) ? $_['view'] : 'doc';
$appVersion = time(); // Use timestamp as ultimate cache buster
$urlGenerator = \OC::$server->get(\OCP\IURLGenerator::class);
?>
<link rel="stylesheet" href="<?php p($urlGenerator->linkTo('unitydocs', 'css/docs.css')); ?>?v=<?php p($appVersion); ?>">
<script src="<?php p($urlGenerator->linkTo('unitydocs', 'js/docs.js')); ?>?v=<?php p($appVersion); ?>"></script>
<script>
    window.UNITY_WORKSPACE_VIEW = '<?php p($currentView); ?>';
</script>

<div id="unity-workspace" class="workspace-view-<?php p($currentView); ?>">
    <div class="workspace-templates-section">
        <div class="workspace-container">
            <div class="section-header">
                <h2>Start a new <?php 
                    if($currentView === 'sheet') p('spreadsheet');
                    elseif($currentView === 'slide') p('presentation');
                    elseif($currentView === 'diagram') p('drawing');
                    else p('document');
                ?></h2>
                <div class="template-gallery-btn">Template gallery <svg viewBox="0 0 24 24"><path fill="currentColor" d="M7,10L12,15L17,10H7Z"/></svg></div>
            </div>
            <div class="templates-grid">
                <div class="template-card" data-action="new-<?php p($currentView); ?>">
                    <div class="template-preview blank-preview">
                        <svg class="plus-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    </div>
                    <div class="template-name">Blank</div>
                </div>
            </div>
        </div>
    </div>

    <div class="workspace-recent-section">
        <div class="workspace-container">
            <div class="section-header">
                <h3>Recent documents</h3>
                <div class="recent-filters">
                    <span>Owned by anyone</span> <svg viewBox="0 0 24 24"><path fill="currentColor" d="M7,10L12,15L17,10H7Z"/></svg>
                </div>
            </div>
            <div class="recent-grid" id="docs-grid">
                <div class="docs-loading">Loading your files...</div>
            </div>
        </div>
    </div>
</div>
