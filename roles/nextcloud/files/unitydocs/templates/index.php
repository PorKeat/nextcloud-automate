<?php
use OCP\Util;
Util::addScript('unitydocs', 'docs');
Util::addStyle('unitydocs', 'docs');

$currentView = isset($_['view']) ? $_['view'] : 'doc';
?>

<script>
    window.UNITY_WORKSPACE_VIEW = '<?php p($currentView); ?>';
</script>

<div id="unity-workspace" class="workspace-view-<?php p($currentView); ?>">
    <div class="workspace-top-bar">
        <div class="workspace-logo-area">
            <div class="workspace-hamburger">
                <svg viewBox="0 0 24 24"><path fill="currentColor" d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"/></svg>
            </div>
            <div class="workspace-logo">
                <?php if($currentView === 'sheet'): ?>
                    <svg viewBox="0 0 24 24" class="logo-icon"><path fill="#0F9D58" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M9,13H15V15H9V13Z"/></svg>
                    <span>Sheets</span>
                <?php elseif($currentView === 'slide'): ?>
                    <svg viewBox="0 0 24 24" class="logo-icon"><path fill="#F4B400" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,11H16V13H8V11Z"/></svg>
                    <span>Slides</span>
                <?php elseif($currentView === 'diagram'): ?>
                    <svg viewBox="0 0 24 24" class="logo-icon"><path fill="#DB4437" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>
                    <span>Drawings</span>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" class="logo-icon"><path fill="#4285F4" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M11,11V10H13V11H11M11,13V12H13V13H11M11,15V14H13V15H11M11,17V16H13V17H11Z"/></svg>
                    <span>Docs</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="workspace-search">
            <svg viewBox="0 0 24 24" class="search-icon"><path fill="#5f6368" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/></svg>
            <input type="text" placeholder="Search" />
        </div>
        <div class="workspace-profile">
            <!-- Nextcloud handles the top right native profile menu usually, but we can put a placeholder avatar here to match Google -->
            <div class="avatar-placeholder"></div>
        </div>
    </div>

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
