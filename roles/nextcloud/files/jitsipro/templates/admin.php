<?php
script('jitsipro', 'admin');
style('jitsipro', 'admin');
?>
<div id="jitsipro" class="section jitsipro-settings-container">
    <h2>Jitsi Pro Settings</h2>
    
    <div class="jitsipro-settings-group">
        <h3>Connection Settings</h3>
        <p class="jitsipro-settings-desc">Configure the connection to your backend Jitsi video routing server.</p>
        <div class="jitsipro-form-row">
            <label for="jitsipro-jitsi-url">Jitsi Server URL</label>
            <input type="text" id="jitsipro-jitsi-url" class="jitsipro-input" value="<?php p($_['jitsi_url']); ?>" placeholder="https://meet.jit.si" />
        </div>
    </div>

    <div class="jitsipro-settings-group">
        <h3>App Branding</h3>
        <p class="jitsipro-settings-desc">Customize how the Jitsi Pro app appears inside your Nextcloud environment.</p>
        <div class="jitsipro-form-row">
            <label for="jitsipro-app-name">Navigation App Name</label>
            <input type="text" id="jitsipro-app-name" class="jitsipro-input" value="<?php p($_['app_name']); ?>" placeholder="Video Call" />
        </div>
        
        <div class="jitsipro-form-row jitsipro-upload-row">
            <label>Navigation App Icon</label>
            <div class="jitsipro-upload-container">
                <div class="jitsipro-preview-box">
                    <img id="jitsipro-app-icon-preview" src="<?php p($_['app_icon']); ?>" />
                </div>
                <div class="jitsipro-upload-actions">
                    <div class="jitsipro-upload-btn-group">
                        <label for="jitsipro-app-icon-upload" class="button jitsipro-upload-btn">Choose Image</label>
                    </div>
                    <span class="jitsipro-upload-hint">SVG or PNG format. Recommended 32x32px.</span>
                    <input type="file" id="jitsipro-app-icon-upload" class="jitsipro-hidden-upload" accept=".svg,.png" />
                    <input type="hidden" id="jitsipro-app-icon" value="<?php p($_['app_icon']); ?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="jitsipro-settings-group">
        <h3>Video Watermark</h3>
        <p class="jitsipro-settings-desc">Replace or remove the Jitsi logo displayed during active video calls.</p>
        <div class="jitsipro-form-row jitsipro-upload-row">
            <label>Custom Watermark</label>
            <div class="jitsipro-upload-container">
                <div class="jitsipro-preview-box watermark-preview">
                    <img id="jitsipro-jitsi-watermark-preview" src="<?php p($_['jitsi_watermark']); ?>" style="<?php if(empty($_['jitsi_watermark'])) echo 'display:none;'; ?>" />
                    <span id="jitsipro-no-watermark-text" class="jitsipro-empty-state" style="<?php if(!empty($_['jitsi_watermark'])) echo 'display:none;'; ?>">No watermark<br/>(Hidden)</span>
                </div>
                <div class="jitsipro-upload-actions">
                    <div class="jitsipro-upload-btn-group">
                        <label for="jitsipro-jitsi-watermark-upload" class="button jitsipro-upload-btn">Upload Watermark</label>
                        <button type="button" id="jitsipro-clear-watermark" class="button">Remove Watermark</button>
                    </div>
                    <span class="jitsipro-upload-hint">Leave blank to disable. PNG, SVG, or JPG.</span>
                    <input type="file" id="jitsipro-jitsi-watermark-upload" class="jitsipro-hidden-upload" accept=".svg,.png,.jpg,.jpeg" />
                    <input type="hidden" id="jitsipro-jitsi-watermark" value="<?php p($_['jitsi_watermark']); ?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="jitsipro-save-container">
        <button id="jitsipro-save" class="button button-primary jitsipro-save-btn">Save Settings</button>
        <span id="jitsipro-save-msg" class="msg jitsipro-save-msg" style="display:none;"></span>
    </div>

    <div class="jitsipro-settings-group" style="margin-top: 40px; border-color: red;">
        <h3 style="color: red;">Diagnostic Logs</h3>
        <p class="jitsipro-settings-desc">These are the raw server logs. Please copy any fatal errors shown here.</p>
        <pre style="background: #111; color: #0f0; padding: 15px; overflow-x: auto; font-size: 11px; white-space: pre-wrap;"><?php p($_['server_logs']); ?></pre>
    </div>
</div>
