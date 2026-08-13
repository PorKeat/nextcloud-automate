function initJitsi() {
    var dataDiv = document.getElementById('jitsipro-data');
    if (!dataDiv) return;
    
    var domain = dataDiv.getAttribute('data-domain');
    var displayName = dataDiv.getAttribute('data-display-name');
    var watermark = dataDiv.getAttribute('data-watermark');
    var avatar = dataDiv.getAttribute('data-avatar');
    var container = document.getElementById('jitsi-meet-container');

    var script = document.createElement('script');
    script.src = 'https://' + domain + '/external_api.js';
    script.onload = function() {
        const options = {
            roomName: 'NextcloudMeetingRoom',
            width: '100%',
            height: '100%',
            parentNode: container,
            userInfo: {
                displayName: displayName,
                avatarURL: avatar
            },
            configOverwrite: { 
                startWithAudioMuted: true, 
                startWithVideoMuted: true,
                prejoinPageEnabled: false,
                prejoinConfig: { enabled: false },
                hideWatermark: !watermark,
                customLogoUrl: watermark ? watermark : ''
            },
            interfaceConfigOverwrite: { 
                filmStripOnly: false,
                SHOW_JITSI_WATERMARK: !!watermark,
                SHOW_WATERMARK_FOR_GUESTS: !!watermark,
                DEFAULT_LOGO_URL: watermark ? watermark : '',
                DEFAULT_WELCOME_PAGE_LOGO_URL: watermark ? watermark : ''
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        api.addEventListener('videoConferenceLeft', function() {
            api.dispose();
            window.location.href = '/';
        });
    };
    document.head.appendChild(script);

    var fsBtn = document.getElementById('jitsipro-fullscreen-btn');
    if (fsBtn) {
        fsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (container.requestFullscreen) {
                    container.requestFullscreen();
                } else if (container.webkitRequestFullscreen) {
                    container.webkitRequestFullscreen();
                } else if (container.webkitRequestFullScreen) {
                    container.webkitRequestFullScreen();
                } else if (container.msRequestFullscreen) {
                    container.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initJitsi);
} else {
    initJitsi();
}
