<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mail Preview</title>
        <style>
            :root {
                color-scheme: dark;
                --mp-bg: #101214;
                --mp-panel: #181b1f;
                --mp-panel-soft: #20242a;
                --mp-border: #30363d;
                --mp-text: #f4f5f7;
                --mp-muted: #9aa3ad;
                --mp-dim: #69717c;
                --mp-accent: #c7f464;
                --mp-accent-text: #11140f;
                --mp-danger: #ffb4a8;
                --mp-danger-strong: #f97366;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                background: var(--mp-bg);
                color: var(--mp-text);
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                line-height: 1.5;
            }

            a {
                color: inherit;
            }

            .mp-shell {
                width: min(1180px, calc(100% - 32px));
                margin: 0 auto;
                padding: 48px 0;
            }

            .mp-header {
                display: flex;
                justify-content: space-between;
                gap: 20px;
                align-items: end;
                border-bottom: 1px solid var(--mp-border);
                padding-bottom: 28px;
            }

            .mp-eyebrow {
                margin: 0 0 10px;
                color: var(--mp-dim);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: .18em;
                text-transform: uppercase;
            }

            .mp-title {
                margin: 0;
                font-size: clamp(30px, 4vw, 44px);
                line-height: 1.08;
            }

            .mp-subtitle {
                max-width: 680px;
                margin: 12px 0 0;
                color: var(--mp-muted);
                font-size: 14px;
            }

            .mp-button,
            .mp-link-button {
                appearance: none;
                border: 1px solid var(--mp-border);
                border-radius: 8px;
                background: transparent;
                color: var(--mp-text);
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 40px;
                padding: 8px 14px;
                font: inherit;
                font-size: 14px;
                font-weight: 650;
                text-decoration: none;
                transition: border-color .15s ease, background .15s ease, color .15s ease;
            }

            .mp-button:hover,
            .mp-link-button:hover {
                border-color: var(--mp-muted);
            }

            .mp-button-primary,
            .mp-tab.is-active {
                border-color: var(--mp-accent);
                background: var(--mp-accent);
                color: var(--mp-accent-text);
            }

            .mp-button-danger {
                border-color: rgb(249 115 102 / 55%);
                color: var(--mp-danger);
            }

            .mp-button-danger:hover {
                border-color: var(--mp-danger-strong);
                background: rgb(249 115 102 / 12%);
            }

            .mp-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .mp-actions form {
                margin: 0;
            }

            .mp-flash {
                margin-top: 20px;
                border: 1px solid rgb(199 244 100 / 35%);
                border-radius: 8px;
                background: rgb(199 244 100 / 8%);
                color: var(--mp-accent);
                padding: 12px 14px;
                font-size: 14px;
            }

            .mp-table-wrap {
                margin-top: 28px;
                overflow: hidden;
                border: 1px solid var(--mp-border);
                border-radius: 10px;
                background: var(--mp-panel);
            }

            .mp-table-scroll {
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                min-width: 860px;
            }

            th,
            td {
                padding: 16px;
                text-align: left;
                vertical-align: top;
                border-bottom: 1px solid var(--mp-border);
                font-size: 14px;
            }

            tr:last-child td {
                border-bottom: 0;
            }

            th {
                background: #0d0f11;
                color: var(--mp-dim);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .16em;
                text-transform: uppercase;
            }

            tbody tr:hover {
                background: var(--mp-panel-soft);
            }

            .mp-nowrap {
                white-space: nowrap;
            }

            .mp-muted {
                color: var(--mp-muted);
            }

            .mp-dim {
                color: var(--mp-dim);
            }

            .mp-cell-limited {
                max-width: 360px;
                overflow-wrap: anywhere;
            }

            .mp-small {
                display: block;
                margin-top: 4px;
                color: var(--mp-dim);
                font-size: 12px;
            }

            .mp-empty {
                padding: 48px 16px;
                text-align: center;
                color: var(--mp-dim);
            }

            .mp-pagination {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 22px;
                color: var(--mp-muted);
                font-size: 14px;
            }

            .mp-pagination-actions {
                display: flex;
                gap: 8px;
            }

            .mp-disabled {
                opacity: .45;
                cursor: default;
            }

            .mp-modal {
                position: fixed;
                inset: 0;
                z-index: 50;
                display: none;
                padding: 24px;
                overflow-y: auto;
            }

            .mp-modal.is-open {
                display: block;
            }

            .mp-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgb(0 0 0 / 76%);
            }

            .mp-modal-panel {
                position: relative;
                width: min(1120px, 100%);
                min-height: 80vh;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid var(--mp-border);
                border-radius: 12px;
                background: var(--mp-bg);
                box-shadow: 0 30px 90px rgb(0 0 0 / 45%);
            }

            .mp-modal-header {
                display: flex;
                justify-content: space-between;
                gap: 20px;
                padding: 20px;
                border-bottom: 1px solid var(--mp-border);
            }

            .mp-modal-title {
                margin: 6px 0 0;
                font-size: 22px;
                line-height: 1.25;
                overflow-wrap: anywhere;
            }

            .mp-meta {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                margin: 16px 0 0;
            }

            .mp-meta dt {
                color: var(--mp-dim);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .mp-meta dd {
                margin: 4px 0 0;
                color: var(--mp-muted);
                overflow-wrap: anywhere;
            }

            .mp-close {
                width: 42px;
                height: 42px;
                flex: 0 0 auto;
                border-radius: 50%;
                font-size: 26px;
                line-height: 1;
                padding: 0;
            }

            .mp-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                padding: 12px 20px;
                border-bottom: 1px solid var(--mp-border);
            }

            .mp-modal-body {
                flex: 1;
                min-height: 420px;
                background: var(--mp-panel);
            }

            .mp-loading,
            .mp-error {
                padding: 36px 20px;
                color: var(--mp-muted);
                text-align: center;
            }

            .mp-error {
                color: var(--mp-danger);
            }

            .mp-pane {
                display: none;
                min-height: 520px;
            }

            .mp-pane.is-active {
                display: block;
            }

            .mp-preview-frame {
                width: 100%;
                min-height: 520px;
                border: 0;
                background: #fff;
            }

            .mp-pre {
                margin: 0;
                min-height: 520px;
                padding: 20px;
                white-space: pre-wrap;
                overflow-wrap: anywhere;
                color: #e7eaee;
                font: 12px/1.65 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            }

            .mp-attachments {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 12px;
                padding: 20px;
            }

            .mp-attachment {
                border: 1px solid var(--mp-border);
                border-radius: 10px;
                background: var(--mp-bg);
                padding: 14px;
            }

            .mp-attachment-title {
                font-weight: 700;
                overflow-wrap: anywhere;
            }

            @media (max-width: 720px) {
                .mp-header,
                .mp-modal-header {
                    flex-direction: column;
                    align-items: stretch;
                }

                .mp-meta {
                    grid-template-columns: 1fr;
                }

                .mp-pagination {
                    align-items: stretch;
                    flex-direction: column;
                }

                .mp-pagination-actions {
                    justify-content: stretch;
                }

                .mp-pagination-actions > * {
                    flex: 1;
                }
            }
        </style>
    </head>
    <body>
        <main class="mp-shell" data-mail-preview>
            <header class="mp-header">
                <div>
                    <p class="mp-eyebrow">Testing</p>
                    <h1 class="mp-title">Mail Preview</h1>
                    <p class="mp-subtitle">Captured outgoing mail from this environment.</p>
                </div>

                <a class="mp-link-button" href="{{ url()->current() }}">Refresh</a>
            </header>

            @if (session('mail-preview.status'))
                <div class="mp-flash" role="status">{{ session('mail-preview.status') }}</div>
            @endif

            <section class="mp-table-wrap" aria-label="Captured emails">
                <div class="mp-table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Captured</th>
                                <th scope="col">To</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Mailer</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($emails as $email)
                                <tr>
                                    <td class="mp-nowrap mp-muted">{{ $email->captured_at?->format('M j, Y H:i') }}</td>
                                    <td class="mp-cell-limited">
                                        {{ $email->recipients ?: 'No recipient' }}
                                        @if ($email->sender)
                                            <span class="mp-small">From {{ $email->sender }}</span>
                                        @endif
                                    </td>
                                    <td class="mp-cell-limited">
                                        {{ $email->subject ?: '(No subject)' }}
                                        @if ($email->cc || $email->bcc)
                                            <span class="mp-small">
                                                @if ($email->cc) CC {{ $email->cc }} @endif
                                                @if ($email->bcc) BCC {{ $email->bcc }} @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="mp-nowrap mp-muted">{{ $email->mailer ?: 'default' }}</td>
                                    <td class="mp-nowrap">
                                        <div class="mp-actions">
                                            <button
                                                type="button"
                                                class="mp-button mp-button-primary"
                                                data-mail-preview-open
                                                data-url="{{ route('mail-preview.show', $email) }}"
                                            >
                                                View
                                            </button>

                                            <form method="POST" action="{{ route('mail-preview.destroy', $email) }}" data-mail-preview-delete-list>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mp-button mp-button-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="mp-empty">No emails have been captured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($emails->hasPages())
                <nav class="mp-pagination" aria-label="Mail preview pagination">
                    <div>Page {{ $emails->currentPage() }} of {{ $emails->lastPage() }}</div>
                    <div class="mp-pagination-actions">
                        @if ($emails->onFirstPage())
                            <span class="mp-button mp-disabled">Previous</span>
                        @else
                            <a class="mp-link-button" href="{{ $emails->previousPageUrl() }}">Previous</a>
                        @endif

                        @if ($emails->hasMorePages())
                            <a class="mp-link-button" href="{{ $emails->nextPageUrl() }}">Next</a>
                        @else
                            <span class="mp-button mp-disabled">Next</span>
                        @endif
                    </div>
                </nav>
            @endif

            <section class="mp-modal" data-mail-preview-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="mail-preview-modal-title">
                <div class="mp-modal-backdrop" data-mail-preview-close></div>

                <div class="mp-modal-panel">
                    <header class="mp-modal-header">
                        <div>
                            <p class="mp-eyebrow" data-mail-preview-captured>Loading</p>
                            <h2 class="mp-modal-title" id="mail-preview-modal-title" data-mail-preview-subject>(No subject)</h2>
                            <dl class="mp-meta">
                                <div>
                                    <dt>From</dt>
                                    <dd data-mail-preview-sender>None</dd>
                                </div>
                                <div>
                                    <dt>To</dt>
                                    <dd data-mail-preview-recipients>None</dd>
                                </div>
                                <div data-mail-preview-cc-wrap hidden>
                                    <dt>CC</dt>
                                    <dd data-mail-preview-cc></dd>
                                </div>
                                <div data-mail-preview-bcc-wrap hidden>
                                    <dt>BCC</dt>
                                    <dd data-mail-preview-bcc></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mp-actions">
                            <form method="POST" data-mail-preview-delete-form>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mp-button mp-button-danger" data-mail-preview-delete-button disabled>Delete</button>
                            </form>

                            <button type="button" class="mp-button mp-close" data-mail-preview-close aria-label="Close email modal">&times;</button>
                        </div>
                    </header>

                    <div class="mp-tabs" role="tablist">
                        <button type="button" class="mp-button mp-tab is-active" data-mail-preview-tab="html">HTML</button>
                        <button type="button" class="mp-button mp-tab" data-mail-preview-tab="text">Text</button>
                        <button type="button" class="mp-button mp-tab" data-mail-preview-tab="headers">Headers</button>
                        <button type="button" class="mp-button mp-tab" data-mail-preview-tab="attachments">Attachments</button>
                    </div>

                    <div class="mp-modal-body">
                        <div class="mp-loading" data-mail-preview-loading>Loading email...</div>
                        <div class="mp-error" data-mail-preview-error hidden></div>

                        <div class="mp-pane is-active" data-mail-preview-pane="html" hidden>
                            <iframe class="mp-preview-frame" title="Email HTML preview" sandbox data-mail-preview-frame></iframe>
                        </div>
                        <pre class="mp-pane mp-pre" data-mail-preview-pane="text" data-mail-preview-text hidden></pre>
                        <pre class="mp-pane mp-pre" data-mail-preview-pane="headers" data-mail-preview-headers hidden></pre>
                        <div class="mp-pane mp-attachments" data-mail-preview-pane="attachments" data-mail-preview-attachments hidden></div>
                    </div>
                </div>
            </section>
        </main>

        <script>
            (() => {
                const root = document.querySelector('[data-mail-preview]');
                const modal = root.querySelector('[data-mail-preview-modal]');
                const frame = root.querySelector('[data-mail-preview-frame]');
                const loading = root.querySelector('[data-mail-preview-loading]');
                const error = root.querySelector('[data-mail-preview-error]');
                const modalDeleteForm = root.querySelector('[data-mail-preview-delete-form]');
                const modalDeleteButton = root.querySelector('[data-mail-preview-delete-button]');
                const panes = Array.from(root.querySelectorAll('[data-mail-preview-pane]'));
                const tabs = Array.from(root.querySelectorAll('[data-mail-preview-tab]'));
                let email = null;

                function setText(selector, value, fallback = '') {
                    root.querySelector(selector).textContent = value || fallback;
                }

                function escapeHtml(value) {
                    return value
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function textAsHtml(value) {
                    return `<pre style="white-space: pre-wrap; word-break: break-word; font: 14px/1.6 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; padding: 24px;">${escapeHtml(value)}</pre>`;
                }

                function showTab(tab) {
                    tabs.forEach((button) => button.classList.toggle('is-active', button.dataset.mailPreviewTab === tab));
                    panes.forEach((pane) => {
                        const isActive = pane.dataset.mailPreviewPane === tab;
                        pane.hidden = ! isActive;
                        pane.classList.toggle('is-active', isActive);
                    });

                    if (tab === 'html' && email) {
                        frame.srcdoc = email.html_body || textAsHtml(email.text_body || 'No HTML body.');
                    }
                }

                function renderEmail() {
                    setText('[data-mail-preview-captured]', email.captured_at, 'Captured email');
                    setText('[data-mail-preview-subject]', email.subject, '(No subject)');
                    setText('[data-mail-preview-sender]', email.sender, 'None');
                    setText('[data-mail-preview-recipients]', email.recipients, 'None');
                    setText('[data-mail-preview-cc]', email.cc);
                    setText('[data-mail-preview-bcc]', email.bcc);

                    root.querySelector('[data-mail-preview-cc-wrap]').hidden = ! email.cc;
                    root.querySelector('[data-mail-preview-bcc-wrap]').hidden = ! email.bcc;
                    root.querySelector('[data-mail-preview-text]').textContent = email.text_body || 'No text body.';
                    root.querySelector('[data-mail-preview-headers]').textContent = email.headers || 'No headers.';
                    modalDeleteForm.action = email.delete_url;
                    modalDeleteButton.disabled = ! email.delete_url;

                    const attachments = root.querySelector('[data-mail-preview-attachments]');
                    attachments.replaceChildren();

                    if (! email.attachments || email.attachments.length === 0) {
                        const empty = document.createElement('p');
                        empty.className = 'mp-muted';
                        empty.textContent = 'No attachments.';
                        attachments.append(empty);
                    } else {
                        email.attachments.forEach((attachment) => {
                            const card = document.createElement('article');
                            card.className = 'mp-attachment';
                            card.innerHTML = `
                                <div class="mp-attachment-title"></div>
                                <div class="mp-small"></div>
                                <div class="mp-small"></div>
                            `;
                            card.children[0].textContent = attachment.filename || 'Unnamed attachment';
                            card.children[1].textContent = attachment.content_type || 'Unknown type';
                            card.children[2].textContent = attachment.disposition || 'attachment';
                            attachments.append(card);
                        });
                    }

                    loading.hidden = true;
                    error.hidden = true;
                    showTab('html');
                }

                function openModal() {
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    email = null;
                    frame.srcdoc = '';
                    modalDeleteForm.removeAttribute('action');
                    modalDeleteButton.disabled = true;
                }

                async function loadEmail(url) {
                    openModal();
                    loading.hidden = false;
                    error.hidden = true;
                    modalDeleteForm.removeAttribute('action');
                    modalDeleteButton.disabled = true;
                    panes.forEach((pane) => pane.hidden = true);

                    try {
                        const response = await fetch(url, {
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        if (! response.ok) {
                            throw new Error('Email could not be loaded.');
                        }

                        email = await response.json();
                        renderEmail();
                    } catch (exception) {
                        loading.hidden = true;
                        error.hidden = false;
                        error.textContent = exception.message || 'Email could not be loaded.';
                    }
                }

                root.querySelectorAll('[data-mail-preview-open]').forEach((button) => {
                    button.addEventListener('click', () => loadEmail(button.dataset.url));
                });

                root.querySelectorAll('[data-mail-preview-close]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                root.querySelectorAll('[data-mail-preview-delete-list], [data-mail-preview-delete-form]').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (! window.confirm('Delete this captured email?')) {
                            event.preventDefault();
                        }
                    });
                });

                tabs.forEach((button) => {
                    button.addEventListener('click', () => showTab(button.dataset.mailPreviewTab));
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        closeModal();
                    }
                });
            })();
        </script>
    </body>
</html>
