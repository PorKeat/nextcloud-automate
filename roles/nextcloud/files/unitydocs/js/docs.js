document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('docs-grid');

    // Fetch recent documents
    fetch(OC.generateUrl('/apps/unitydocs/api/recent'))
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
            return '<svg viewBox="0 0 24 24"><path fill="#1e8e3e" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M9,13H15V15H9V13Z"/></svg>';
        } else if (type === 'presentation') {
            return '<svg viewBox="0 0 24 24"><path fill="#e37400" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,11H16V13H8V11Z"/></svg>';
        } else {
            return '<svg viewBox="0 0 24 24"><path fill="#1a73e8" d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M11,11V10H13V11H11M11,13V12H13V13H11M11,15V14H13V15H11M11,17V16H13V17H11Z"/></svg>';
        }
    }

    function renderDocs(docs) {
        if (!docs || docs.length === 0) {
            grid.innerHTML = `<div class="docs-loading">No documents found. Create one above to get started!</div>`;
            return;
        }

        grid.innerHTML = '';
        docs.forEach(doc => {
            const date = new Date(doc.mtime * 1000).toLocaleDateString();
            
            const card = document.createElement('a');
            card.className = 'doc-card';
            card.href = doc.url;
            // Nextcloud handles opening files through the standard file viewer mechanism
            // If richdocuments is installed, it will automatically hijack this and open the editor

            card.innerHTML = `
                <div class="doc-preview">
                    ${getIconSvg(doc.type)}
                </div>
                <div class="doc-info">
                    <div class="doc-name" title="${doc.name}">${doc.name}</div>
                    <div class="doc-meta">
                        <svg style="width:14px;height:14px;" viewBox="0 0 24 24"><path fill="currentColor" d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.28,15.28L11,10V5H12.5V9.38L17.34,14.22L16.28,15.28Z"/></svg>
                        Opened ${date}
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    // Template click handlers
    document.querySelectorAll('.template-card').forEach(card => {
        card.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            let type = 'document';
            if (action === 'new-sheet') type = 'spreadsheet';
            if (action === 'new-slide') type = 'presentation';

            this.style.opacity = '0.5';

            fetch(OC.generateUrl('/apps/unitydocs/api/create'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.url;
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
