<script>
    // Your data object
    window['noteData'] = {};

    // Helper to escape HTML to prevent XSS in text/code modes
    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function openNoteModal(data) {
        // 1. Populate Header & Meta
        document.getElementById('noteModalTitle').textContent = data.title;

        const date = new Date(data.created_at);
        document.getElementById('noteDate').textContent = date.toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        document.getElementById('noteLikes').textContent = data.likes_count;
        document.getElementById('noteDislikes').textContent = data.dislikes_count;
        document.getElementById('noteComments').textContent = data.comments_count;

        // 2. Populate Tags (Filter out empty strings)
        const tagsContainer = document.getElementById('noteTags');
        tagsContainer.innerHTML = '';
        const validTags = data.tags.filter(tag => tag.trim() !== '');
        if (validTags.length > 0) {
            validTags.forEach(tag => {
                const badge = document.createElement('span');
                badge.className = 'badge badge-secondary mr-1 mb-1';
                badge.textContent = tag;
                tagsContainer.appendChild(badge);
            });
        }

        // 3. Render Content based on Doctype
        const contentArea = document.getElementById('noteContentArea');
        contentArea.innerHTML = ''; // Clear previous content

        if (data.doctype === 'markdown') {
            // MARKDOWN: Use marked.js
            const mdDiv = document.createElement('div');
            mdDiv.className = 'note-markdown-content';
            // Note: marked.parse is the modern standard. If using an older version, use marked(data.body)
            mdDiv.innerHTML = marked.parse(data.body);
            contentArea.appendChild(mdDiv);

        } else if (data.doctype === 'code') {
            // CODE: Display as code block with copy button
            const wrapper = document.createElement('div');
            wrapper.className = 'note-code-wrapper';

            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-sm btn-outline-secondary copy-btn';
            copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
            copyBtn.onclick = () => copyToClipboard(data.body, copyBtn);

            const pre = document.createElement('pre');
            const code = document.createElement('code');
            code.className = 'note-code-content';
            code.textContent = data.body; // textContent automatically escapes HTML and preserves whitespace

            pre.appendChild(code);
            wrapper.appendChild(copyBtn);
            wrapper.appendChild(pre);
            contentArea.appendChild(wrapper);

        } else {
            // TEXT: Plain text, but preserve formatting (\n, \t)
            const textDiv = document.createElement('div');
            textDiv.className = 'note-text-content';
            textDiv.textContent = data.body; // textContent preserves \n and \t visually when combined with CSS white-space: pre-wrap
            contentArea.appendChild(textDiv);
        }

        // 4. Show Modal
        $('#noteModal').modal('show');
    }

    // Robust Copy to Clipboard function
    function copyToClipboard(text, btn) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => showCopySuccess(btn));
        } else {
            // Fallback for older browsers or non-secure contexts (HTTP)
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showCopySuccess(btn);
            } catch (err) {
                console.error('Failed to copy text', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showCopySuccess(btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
        btn.classList.add('border-success');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('border-success');
        }, 2000);
    }
</script>

<!-- Bootstrap 4 JS dependencies (Make sure these are at the end of your <body>) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
