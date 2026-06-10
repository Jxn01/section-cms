// ─── Admin JS ───
// WYSIWYG editors, media browser, mobile sidebar, SEO helpers, and UI interactions.

document.addEventListener('DOMContentLoaded', () => {

    // ─── Mobile Sidebar Toggle ───
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar  = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        adminSidebar?.classList.add('open');
        sidebarOverlay?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        adminSidebar?.classList.remove('open');
        sidebarOverlay?.classList.remove('open');
        document.body.style.overflow = '';
    }
    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);
    // Close sidebar when navigating (clicking a sidebar link)
    adminSidebar?.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Auto-dismiss alerts after 4 seconds
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 4000);
    });

    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput  = document.getElementById('slug');
    if (titleInput && slugInput && !slugInput.readOnly && slugInput.value === '') {
        titleInput.addEventListener('input', () => {
            const map = {'á':'a','é':'e','í':'i','ó':'o','ö':'o','ő':'o','ú':'u','ü':'u','ű':'u',
                         'Á':'a','É':'e','Í':'i','Ó':'o','Ö':'o','Ő':'o','Ú':'u','Ü':'u','Ű':'u'};
            let slug = titleInput.value.toLowerCase();
            slug = slug.replace(/[áéíóöőúüűÁÉÍÓÖŐÚÜŰ]/g, c => map[c] || c);
            slug = slug.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            slugInput.value = slug;
        });
    }

    // ─── SEO Character Counters ───
    function addSeoCounter(inputId, maxLen, label) {
        const el = document.getElementById(inputId);
        if (!el) return;
        const counter = document.createElement('div');
        counter.className = 'seo-counter';
        el.parentElement.appendChild(counter);
        function update() {
            const len = el.value.length;
            counter.textContent = len + ' / ' + maxLen + ' karakter';
            counter.classList.toggle('over', len > maxLen);
        }
        el.addEventListener('input', update);
        update();
    }
    addSeoCounter('meta_title', 60, 'Meta cím');
    addSeoCounter('meta_description', 160, 'Meta leírás');

    // ─── SEO Hints (empty field warnings) ───
    function addSeoHint(inputId, message) {
        const el = document.getElementById(inputId);
        if (!el) return;
        const hint = document.createElement('div');
        hint.className = 'seo-hint';
        el.parentElement.appendChild(hint);
        function update() {
            if (el.value.trim() === '') {
                hint.textContent = '⚠ ' + message;
                hint.classList.add('seo-warn');
            } else {
                hint.textContent = '';
                hint.classList.remove('seo-warn');
            }
        }
        el.addEventListener('input', update);
        update();
    }
    addSeoHint('meta_title', 'A meta cím üres — az oldal címe lesz használva.');
    addSeoHint('meta_description', 'A meta leírás üres — az alapértelmezett leírás jelenik meg a Google-ben.');
    addSeoHint('meta_keywords', 'Nincsenek kulcsszavak megadva ehhez az oldalhoz.');

    // ─── WYSIWYG (Quill) Initialization ───
    const quillEditors = {};
    document.querySelectorAll('.quill-editor').forEach(editorDiv => {
        const id = editorDiv.id;
        const hiddenId = id.replace('quill_', 'quill_hidden_');
        const hidden = document.getElementById(hiddenId);
        if (!hidden) return;

        try {
            const quill = new Quill('#' + id, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'blockquote'],
                        [{ 'align': [] }],
                        ['clean']
                    ]
                },
                placeholder: 'Írja ide a szöveget...'
            });
            quillEditors[id] = { quill, hidden };
        } catch (e) {
            console.warn('Quill init failed for', id, e);
        }
    });

    // Sync Quill content to hidden textareas on form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            Object.values(quillEditors).forEach(({ quill, hidden }) => {
                let html = quill.root.innerHTML;
                // Clean up empty quill content
                if (html === '<p><br></p>') html = '';
                hidden.value = html;
            });
        });
    });

    // ─── Media Browser ───
    document.querySelectorAll('.browse-media-btn').forEach(wireBrowseBtn);

    // ─── Collapsible Help Sections ───
    document.querySelectorAll('.help-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.getAttribute('data-target'));
            if (!target) return;
            const isOpen = target.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen);
            btn.querySelector('.help-toggle-icon').textContent = isOpen ? '▾' : '▸';
        });
    });

    // ─── Page Selector for Link Fields ───
    // When a .page-selector <select> changes, fill the sibling URL input
    function wirePageSelector(sel) {
        sel.addEventListener('change', function () {
            const targetId = this.getAttribute('data-link-target');
            const input = document.getElementById(targetId);
            if (input && this.value) {
                input.value = this.value;
            }
        });
    }
    document.querySelectorAll('.page-selector').forEach(wirePageSelector);

    // ─── Media Browse Button wiring ───
    function wireBrowseBtn(btn) {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const popup = window.open(
                '/admin/media.php?browse=1&target=' + encodeURIComponent(targetId),
                'mediaBrowser',
                'width=800,height=600,scrollbars=yes,resizable=yes'
            );
            if (popup) popup.focus();
        });
    }

    // ─── Repeater: Add / Remove items in sections ───

    /**
     * Re-index all items in a repeater container so that PHP receives
     * sequential 0-based indices in the name attributes.
     */
    function reindexRepeater(container) {
        const items = container.querySelectorAll(':scope > .repeater-item, :scope > .card-editor-item');
        items.forEach((item, idx) => {
            // Update name attributes: replace [items][OLD] or [cards][OLD] etc. with new index
            item.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(
                    /(\[(?:items|cards|images|overlay_boxes|projects)\])\[\d+\]/,
                    '$1[' + idx + ']'
                );
            });
            // Update id attributes that contain _INDEX_
            item.querySelectorAll('[id]').forEach(el => {
                el.id = el.id.replace(/_\d+(?=(?:_\d+)?$)/, '_' + idx);
            });
            // Update data-link-target on page selectors
            item.querySelectorAll('.page-selector[data-link-target]').forEach(sel => {
                sel.dataset.linkTarget = sel.dataset.linkTarget.replace(/_\d+(?=(?:_\d+)?$)/, '_' + idx);
            });
            // Update browse button data-target
            item.querySelectorAll('.browse-media-btn[data-target]').forEach(btn => {
                btn.dataset.target = btn.dataset.target.replace(/_\d+(?=(?:_\d+)?$)/, '_' + idx);
            });
            // Update data-card-index
            if (item.dataset.cardIndex !== undefined) {
                item.dataset.cardIndex = idx;
            }
        });
    }

    /**
     * Remove a repeater item and re-index siblings.
     */
    function removeRepeaterItem(btn) {
        const item = btn.closest('.repeater-item, .card-editor-item');
        if (!item) return;
        const container = item.parentElement;
        if (!confirm('Biztosan törli ezt az elemet?')) return;
        item.remove();
        reindexRepeater(container);
    }

    /**
     * Add a new repeater item by cloning from a <template>.
     */
    function addRepeaterItem(btn) {
        const container = btn.closest('.repeater-editor, .cards-editor');
        if (!container) return;
        const tmpl = container.querySelector('template.repeater-template');
        if (!tmpl) return;

        // Count current items to determine the next index
        const items = container.querySelectorAll(':scope > .repeater-item, :scope > .card-editor-item');
        const nextIdx = items.length;

        // Clone template
        const clone = tmpl.content.firstElementChild.cloneNode(true);

        // Replace __IDX__ placeholder in all attributes
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/__IDX__/g, nextIdx);
        });
        clone.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(/__IDX__/g, nextIdx);
        });
        clone.querySelectorAll('[data-link-target]').forEach(el => {
            el.dataset.linkTarget = el.dataset.linkTarget.replace(/__IDX__/g, nextIdx);
        });
        clone.querySelectorAll('[data-target]').forEach(el => {
            el.dataset.target = el.dataset.target.replace(/__IDX__/g, nextIdx);
        });
        clone.querySelectorAll('[for]').forEach(el => {
            el.htmlFor = el.htmlFor.replace(/__IDX__/g, nextIdx);
        });
        if (clone.dataset.cardIndex !== undefined) {
            clone.dataset.cardIndex = nextIdx;
        }

        // Insert before the template (which is before the add button)
        container.insertBefore(clone, tmpl);

        // Wire up interactive elements inside the new item
        clone.querySelectorAll('.page-selector').forEach(wirePageSelector);
        clone.querySelectorAll('.browse-media-btn').forEach(wireBrowseBtn);
        clone.querySelectorAll('.repeater-remove').forEach(b => {
            b.addEventListener('click', () => removeRepeaterItem(b));
        });

        // Scroll the new item into view
        clone.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Wire up existing remove buttons
    document.querySelectorAll('.repeater-remove').forEach(btn => {
        btn.addEventListener('click', () => removeRepeaterItem(btn));
    });

    // Wire up add buttons
    document.querySelectorAll('.repeater-add-btn').forEach(btn => {
        btn.addEventListener('click', () => addRepeaterItem(btn));
    });

    // ─── Reference Gallery: nested repeater (images inside projects) ───

    function reindexNestedImages(projectItem) {
        const images = projectItem.querySelectorAll(':scope > .ref-images-container > .ref-image-row');
        const secId = projectItem.closest('.repeater-editor')?.dataset.sectionId || '0';
        const projIdx = projectItem.dataset.cardIndex || '0';
        images.forEach((row, idx) => {
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(
                    /(\[images\])\[\d+\]/,
                    '$1[' + idx + ']'
                );
            });
            row.querySelectorAll('[id]').forEach(el => {
                el.id = el.id.replace(/_\d+$/, '_' + idx);
            });
            row.querySelectorAll('.browse-media-btn[data-target]').forEach(btn => {
                btn.dataset.target = btn.dataset.target.replace(/_\d+$/, '_' + idx);
            });
        });
    }

    function removeRefImage(btn) {
        const row = btn.closest('.ref-image-row');
        if (!row) return;
        const projectItem = row.closest('.repeater-item');
        row.remove();
        if (projectItem) reindexNestedImages(projectItem);
    }

    function addRefImage(btn) {
        const projectItem = btn.closest('.repeater-item');
        if (!projectItem) return;
        const tmpl = projectItem.querySelector('template.ref-image-template');
        const imagesContainer = projectItem.querySelector('.ref-images-container');
        if (!tmpl || !imagesContainer) return;

        const secId = projectItem.closest('.repeater-editor')?.dataset.sectionId || '0';
        const projIdx = projectItem.dataset.cardIndex || '0';
        const nextIdx = imagesContainer.querySelectorAll('.ref-image-row').length;

        const clone = tmpl.content.firstElementChild.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/__PIDX__/g, projIdx).replace(/__IIDX__/g, nextIdx);
        });
        clone.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(/__PIDX__/g, projIdx).replace(/__IIDX__/g, nextIdx);
        });
        clone.querySelectorAll('[data-target]').forEach(el => {
            el.dataset.target = el.dataset.target.replace(/__PIDX__/g, projIdx).replace(/__IIDX__/g, nextIdx);
        });

        imagesContainer.appendChild(clone);
        clone.querySelectorAll('.browse-media-btn').forEach(wireBrowseBtn);
        clone.querySelectorAll('.ref-image-remove').forEach(b => {
            b.addEventListener('click', () => removeRefImage(b));
        });
    }

    // Wire existing ref-image buttons
    document.querySelectorAll('.ref-image-remove').forEach(btn => {
        btn.addEventListener('click', () => removeRefImage(btn));
    });
    document.querySelectorAll('.ref-image-add-btn').forEach(btn => {
        btn.addEventListener('click', () => addRefImage(btn));
    });

});
