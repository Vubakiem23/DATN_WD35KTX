@php
    $selector = $selector ?? null;
    $formSelector = $form ?? null;
    $editorVar = $editorVar ?? null;
    $config = $config ?? [];
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/styles.css" />
        <style>
            .document-editor__wrapper {
                border: 1px solid #d5d9de;
                border-radius: 16px;
                overflow: hidden;
                background-color: #fefefe;
                box-shadow: 0 25px 65px rgba(15, 23, 42, .08);
            }

            .document-editor__toolbar {
                border-bottom: 1px solid #e6e9f0;
                padding: 12px 18px;
                background: linear-gradient(145deg, #fdfdff 0%, #eef1fb 100%);
            }

            .document-editor__editable-container {
                padding: 26px;
                background: #f3f5fb;
            }

            .document-editor__editable {
                min-height: 420px;
                padding: 40px 48px;
                border-radius: 8px;
                background-color: #fff;
                box-shadow:
                    inset 0 0 0 2px rgba(59, 130, 246, 0.08),
                    0 20px 40px rgba(15, 23, 42, 0.05);
            }

            .document-editor__editable .ck-editor__editable_inline {
                min-height: 420px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>
        <script>
            window.LaravelCkeditor = (function () {
                const uploadUrl = @json(route('admin.ckeditor.upload', [], false));

                function getCsrfToken() {
                    const tokenTag = document.querySelector('meta[name="csrf-token"]');
                    return tokenTag ? tokenTag.getAttribute('content') : @json(csrf_token());
                }

                class UploadAdapter {
                    constructor(loader) {
                        this.loader = loader;
                        this.abortController = new AbortController();
                    }

                    upload() {
                        return this.loader.file.then(file => new Promise((resolve, reject) => {
                            const data = new FormData();
                            data.append('upload', file);

                            fetch(uploadUrl, {
                                method: 'POST',
                                body: data,
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin',
                                signal: this.abortController.signal,
                            })
                                .then(async response => {
                                    if (!response.ok) {
                                        let message = 'Upload failed';
                                        try {
                                            const errorBody = await response.json();
                                            message = errorBody?.message ?? message;
                                        } catch (err) {
                                            // ignore json parse errors
                                        }
                                        throw new Error(message);
                                    }

                                    return response.json();
                                })
                                .then(result => {
                                    if (result?.url) {
                                        resolve({ default: result.url });
                                    } else {
                                        reject(result?.message || 'Upload response invalid');
                                    }
                                })
                                .catch(error => reject(error));
                        }));
                    }

                    abort() {
                        this.abortController.abort();
                    }
                }

                function uploadPlugin(editor) {
                    editor.plugins.get('FileRepository').createUploadAdapter = loader => new UploadAdapter(loader);
                }

                function buildConfig(customConfig = {}) {
                    const defaultConfig = {
                        placeholder: 'Nhập nội dung tại đây...',
                        toolbar: {
                            items: [
                                'exportPdf', 'exportWord', '|',
                                'undo', 'redo', '|',
                                'findAndReplace', 'selectAll', '|',
                                'heading', '|',
                                'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                                'bold', 'italic', 'underline', 'strikethrough', 'code', 'subscript', 'superscript', '|',
                                'alignment', '|',
                                'bulletedList', 'numberedList', 'todoList', '|',
                                'outdent', 'indent', '|',
                                'link', 'blockQuote', 'insertTable', 'uploadImage', 'mediaEmbed', 'codeBlock', 'horizontalLine', 'pageBreak'
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        list: {
                            properties: {
                                styles: true,
                                startIndex: true,
                                reversed: true
                            }
                        },
                        heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                            ]
                        },
                        link: {
                            addTargetToExternalLinks: true,
                            decorators: {
                                openInNewTab: {
                                    mode: 'manual',
                                    label: 'Mở trong tab mới',
                                    defaultValue: true,
                                    attributes: {
                                        target: '_blank',
                                        rel: 'noopener noreferrer'
                                    }
                                }
                            }
                        },
                        mediaEmbed: {
                            previewsInData: true,
                        },
                        image: {
                            toolbar: [
                                'imageTextAlternative',
                                'toggleImageCaption',
                                '|',
                                'imageStyle:inline',
                                'imageStyle:block',
                                'imageStyle:side'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells',
                                'tableCellProperties',
                                'tableProperties'
                            ]
                        },
                        extraPlugins: [uploadPlugin],
                        removePlugins: [
                            'CKFinder',
                            'CKBox'
                        ]
                    };

                    return Object.assign({}, defaultConfig, customConfig || {});
                }

                function init(selector, customConfig = {}, formSelector = null) {
                    const sourceElement = typeof selector === 'string'
                        ? document.querySelector(selector)
                        : selector;

                    if (!sourceElement) {
                        return Promise.reject(new Error(`CKEditor element not found for selector: ${selector}`));
                    }

                    const form = formSelector ? document.querySelector(formSelector) : sourceElement.closest('form');

                    const wrapper = document.createElement('div');
                    wrapper.className = 'document-editor__wrapper';

                    const toolbarContainer = document.createElement('div');
                    toolbarContainer.className = 'document-editor__toolbar';

                    const editableContainer = document.createElement('div');
                    editableContainer.className = 'document-editor__editable-container';

                    const editableElement = document.createElement('div');
                    editableElement.className = 'document-editor__editable ck-content';
                    editableElement.innerHTML = sourceElement.value || '';

                    editableContainer.appendChild(editableElement);
                    wrapper.appendChild(toolbarContainer);
                    wrapper.appendChild(editableContainer);

                    sourceElement.style.display = 'none';
                    sourceElement.parentNode.insertBefore(wrapper, sourceElement.nextSibling);

                    return DecoupledEditor
                        .create(editableElement, buildConfig(customConfig))
                        .then(editor => {
                            toolbarContainer.appendChild(editor.ui.view.toolbar.element);

                            if (form) {
                                form.addEventListener('submit', () => {
                                    sourceElement.value = editor.getData();
                                });
                            }

                            return editor;
                        });
                }

                return { init };
            })();
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = {!! json_encode($selector) !!};
            const formSelector = {!! json_encode($formSelector) !!};
            const config = {!! json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

            if (!selector) {
                console.error('CKEditor selector is required.');
                return;
            }

            if (!window.LaravelCkeditor?.init) {
                console.error('CKEditor bootstrap script missing.');
                return;
            }

            window.LaravelCkeditor.init(selector, config, formSelector)
                .then(editor => {
                    @if(!empty($editorVar))
                        window['{{ $editorVar }}'] = editor;
                    @endif
                })
                .catch(error => console.error('CKEditor init failed:', error));
        });
    </script>
@endpush

