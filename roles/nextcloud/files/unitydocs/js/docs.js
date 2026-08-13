document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('docs-grid');
    const currentView = window.UNITY_WORKSPACE_VIEW || 'doc';

    // Fetch recent documents for the current view
    const apiUrl = OC.generateUrl('/apps/unitydocs/api/recent') + '?view=' + encodeURIComponent(currentView);
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.status === 'error') throw new Error(data.message || 'Unknown error');
            renderDocs(data.documents);
        })
        .catch(err => {
            grid.innerHTML = `<div class="docs-error">Failed to load documents: ${err.message}</div>`;
        });

    function getIconSvg(type) {
        if (type === 'spreadsheet') {
            return '<svg viewBox="0 0 24 24" class="fallback-icon"><path fill="#0F9D58" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M9,13H15V15H9V13Z"/></svg>';
        } else if (type === 'presentation') {
            return '<svg viewBox="0 0 24 24" class="fallback-icon"><path fill="#F4B400" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,11H16V13H8V11Z"/></svg>';
        } else if (type === 'diagram') {
            return '<svg viewBox="0 0 24 24" class="fallback-icon"><path fill="#DB4437" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>';
        } else {
            return '<svg viewBox="0 0 24 24" class="fallback-icon"><path fill="#4285F4" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M11,11V10H13V11H11M11,13V12H13V13H11M11,15V14H13V15H11M11,17V16H13V17H11Z"/></svg>';
        }
    }

    function renderDocs(docs) {
        if (!docs || docs.length === 0) {
            grid.innerHTML = `<div class="docs-loading">No documents found. Create one above to get started!</div>`;
            return;
        }

        grid.innerHTML = '';
        let previewQueue = [];
        docs.forEach(doc => {
            const date = new Date(doc.mtime * 1000).toLocaleDateString();
            
            const card = document.createElement('a');
            card.className = 'doc-card';
            let url = doc.url;
            if (url.indexOf('?') === -1) {
                url += '?requesttoken=' + encodeURIComponent(OC.requestToken);
            } else {
                url += '&requesttoken=' + encodeURIComponent(OC.requestToken);
            }
            card.href = url;
            
            const fallbackIcon = getIconSvg(doc.type);

            card.innerHTML = `
                <div class="doc-preview">
                    ${fallbackIcon}
                </div>
                <div class="doc-info">
                    <div class="doc-name" title="${doc.name}">${doc.name.replace(/\.[^/.]+$/, "")}</div>
                    <div class="doc-meta">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.28,15.28L11,10V5H12.5V9.38L17.34,14.22L16.28,15.28Z"/></svg>
                        Opened ${date}
                    </div>
                </div>
            `;
            grid.appendChild(card);
            previewQueue.push({ doc: doc, card: card });
        });

        function processNextPreview() {
            if (previewQueue.length === 0) return;
            const item = previewQueue.shift();
            const previewUrl = OC.generateUrl('/core/preview') + '?fileId=' + item.doc.fileid + '&x=250&y=250&a=1';
            
            const img = new Image();
            img.className = 'doc-thumbnail';
            
            img.onload = function() {
                const previewDiv = item.card.querySelector('.doc-preview');
                if (previewDiv) {
                    previewDiv.innerHTML = '';
                    previewDiv.appendChild(img);
                }
                processNextPreview();
            };
            
            img.onerror = function() {
                processNextPreview();
            };
            
            img.src = previewUrl;
        }

        // Run 2 preview requests concurrently to prevent server overload
        processNextPreview();
        if (previewQueue.length > 0) {
            processNextPreview();
        }
    }

    // Template click handlers
    document.querySelectorAll('.template-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.opacity = '0.5';

            fetch(OC.generateUrl('/apps/unitydocs/api/create'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify({ type: currentView })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let url = data.url;
                    if (url.indexOf('?') === -1) {
                        url += '?requesttoken=' + encodeURIComponent(OC.requestToken);
                    } else {
                        url += '&requesttoken=' + encodeURIComponent(OC.requestToken);
                    }
                    window.location.href = url;
                } else {
                    alert('Error creating document: ' + (data.message || 'Unknown error'));
                    this.style.opacity = '1';
                }
            })
            .catch(err => {
                alert('Network error: ' + err.message);
                this.style.opacity = '1';
            });
        });
    });
});
