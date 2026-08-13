window.addEventListener('DOMContentLoaded', function() {
    var iconUpload = document.getElementById('jitsipro-app-icon-upload');
    var iconInput = document.getElementById('jitsipro-app-icon');
    var iconPreview = document.getElementById('jitsipro-app-icon-preview');

    if (iconUpload) {
        iconUpload.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(event) {
                var base64 = event.target.result;
                iconInput.value = base64;
                iconPreview.src = base64;
                iconPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    var watermarkUpload = document.getElementById('jitsipro-jitsi-watermark-upload');
    var watermarkInput = document.getElementById('jitsipro-jitsi-watermark');
    var watermarkPreview = document.getElementById('jitsipro-jitsi-watermark-preview');

    var clearWatermarkBtn = document.getElementById('jitsipro-clear-watermark');
    if (clearWatermarkBtn) {
        clearWatermarkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            watermarkInput.value = '';
            watermarkPreview.src = '';
            watermarkPreview.style.display = 'none';
            var emptyText = document.getElementById('jitsipro-no-watermark-text');
            if (emptyText) emptyText.style.display = 'block';
        });
    }

    if (watermarkUpload) {
        watermarkUpload.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(event) {
                var base64 = event.target.result;
                watermarkInput.value = base64;
                watermarkPreview.src = base64;
                watermarkPreview.style.display = 'block';
                var emptyText = document.getElementById('jitsipro-no-watermark-text');
                if (emptyText) emptyText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    var saveBtn = document.getElementById('jitsipro-save');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            var jitsiUrl = document.getElementById('jitsipro-jitsi-url').value;
            var appName = document.getElementById('jitsipro-app-name').value;
            var appIcon = document.getElementById('jitsipro-app-icon').value;
            var jitsiWatermark = document.getElementById('jitsipro-jitsi-watermark').value;
            
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            fetch(OC.generateUrl('/apps/jitsipro/settings'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify({
                    jitsi_url: jitsiUrl,
                    app_name: appName,
                    app_icon: appIcon,
                    jitsi_watermark: jitsiWatermark
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Unknown server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    throw new Error(data.message || 'Unknown error');
                }
                var msg = document.getElementById('jitsipro-save-msg');
                msg.style.display = 'inline';
                msg.style.color = 'green';
                msg.textContent = 'Settings saved successfully! Refresh page to see changes.';
                saveBtn.textContent = 'Save Settings';
                saveBtn.disabled = false;
                setTimeout(() => { msg.style.display = 'none'; }, 5000);
            })
            .catch(err => {
                var msg = document.getElementById('jitsipro-save-msg');
                msg.style.display = 'inline';
                msg.style.color = 'red';
                msg.textContent = 'Error: ' + err.message;
                saveBtn.textContent = 'Save Settings';
                saveBtn.disabled = false;
            });
        });
    }
});
