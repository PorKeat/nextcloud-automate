<?php
use OCP\Util;
Util::addScript('unitydocs', 'docs');
Util::addStyle('unitydocs', 'docs');
?>

<div id="unitydocs-dashboard">
    <div class="docs-header">
        <h2>Start a new document</h2>
        <div class="docs-templates">
            <div class="template-card" data-action="new-doc">
                <div class="template-icon blue"><svg viewBox="0 0 24 24"><path fill="currentColor" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M11,11V10H13V11H11M11,13V12H13V13H11M11,15V14H13V15H11M11,17V16H13V17H11Z"/></svg></div>
                <div class="template-name">Blank Document</div>
            </div>
            <div class="template-card" data-action="new-sheet">
                <div class="template-icon green"><svg viewBox="0 0 24 24"><path fill="currentColor" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M9,13H15V15H9V13Z"/></svg></div>
                <div class="template-name">Blank Spreadsheet</div>
            </div>
            <div class="template-card" data-action="new-slide">
                <div class="template-icon orange"><svg viewBox="0 0 24 24"><path fill="currentColor" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,11H16V13H8V11Z"/></svg></div>
                <div class="template-name">Blank Presentation</div>
            </div>
        </div>
    </div>

    <div class="docs-recent">
        <h3>Recent documents</h3>
        <div class="docs-grid" id="docs-grid">
            <div class="docs-loading">Loading your documents...</div>
        </div>
    </div>
</div>
