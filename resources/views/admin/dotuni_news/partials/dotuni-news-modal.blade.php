<div class="modal fade modern-modal animated-modal" id="dotuniNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="dotuniNewsForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-newspaper mr-2"></i> DotUni News
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- STEP INDICATOR --}}
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge badge-primary mr-2" id="stepBadge">Step 1 of 2</span>
                        <small class="text-muted" id="stepHint">Fill details then choose a layout</small>
                    </div>

                    {{-- =========================
                    STEP 1: STATIC + LAYOUT PICK
                    ========================== --}}
                    <div id="newsStep1">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Headline (Optional)</label>
                                <input type="text" name="headline" class="form-control" maxlength="250">
                            </div>

                            <div class="form-group col-md-6">
                                <label>SEO Title</label>
                                <input type="text" name="seo_title" class="form-control" maxlength="250" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label>SEO Description</label>
                                <input type="text" name="seo_description" class="form-control" maxlength="300" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Visibility</label>
                                <select name="visibility" class="form-control" required>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="unlisted">Unlisted</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Published At (Optional)</label>
                                <input type="datetime-local" name="published_at" class="form-control">
                            </div>
                        </div>

                        <hr>

                        {{-- Layout selector --}}
                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="fas fa-layer-group mr-2"></i> Choose News Layout</h5>
                        </div>
                        <small class="text-muted d-block mb-3">
                            Select a layout style — you’ll fill the content on the next step.
                        </small>

                        <input type="hidden" name="layout" id="newsLayout" value="layout_1">

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <div class="card layout-card" data-layout="layout_1">
                                    <div class="card-body p-2">
                                        <div class="layout-preview layout-1">
                                            <div class="lp-row">
                                                <div class="lp-img"></div>
                                                <div class="lp-text"></div>
                                            </div>
                                            <div class="lp-row">
                                                <div class="lp-text"></div>
                                                <div class="lp-img"></div>
                                            </div>
                                        </div>
                                        <small class="d-block text-center mt-2">Layout 1</small>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <div class="card layout-card" data-layout="layout_2">
                                    <div class="card-body p-2">
                                        <div class="layout-preview layout-2">
                                            <div class="lp-hero"></div>
                                            <div class="lp-article"></div>
                                        </div>
                                        <small class="d-block text-center mt-2">Layout 2</small>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <div class="card layout-card" data-layout="layout_3">
                                    <div class="card-body p-2">
                                        <div class="layout-preview layout-3">
                                            <div class="lp-row">
                                                <div class="lp-img"></div>
                                                <div class="lp-text"></div>
                                            </div>
                                        </div>
                                        <small class="d-block text-center mt-2">Layout 3</small>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4 mb-3">
                                <div class="card layout-card" data-layout="layout_4">
                                    <div class="card-body p-2">
                                        <div class="layout-preview layout-4">
                                            <div class="lp-line"></div>
                                            <div class="lp-line"></div>
                                            <div class="lp-line"></div>
                                        </div>
                                        <small class="d-block text-center mt-2">Layout 4</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card layout-card" data-layout="layout_5">
                                    <div class="card-body p-2">
                                        <div class="layout-preview layout-5">
                                            <div class="lp-slider">
                                                <span class="lp-arrow left">‹</span>
                                                <div class="lp-slide"></div>
                                                <span class="lp-arrow right">›</span>
                                            </div>
                                            <div class="lp-caption"></div>
                                        </div>
                                        <small class="d-block text-center mt-2">Layout 5</small>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    {{-- =========================
                    STEP 2: CONTENT FOR CHOSEN LAYOUT
                    ========================== --}}
                    <div id="newsStep2" style="display:none;">

                        <div class="alert alert-light border d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Selected layout:</strong>
                                <span id="selectedLayoutLabel">Classic</span>
                            </div>
                            <small class="text-muted">You can go back and change it</small>
                        </div>

                        {{-- LAYOUT 1 --}}
                        <div id="layoutClassic" class="layout-panel">
                            {{-- media rows UI --}}
                            <div class="d-flex align-items-center justify-content-between mb-2 gap-3">
                                <h5 class="mb-0"><i class="far fa-images mr-2"></i> Images & Captions</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addNewsMediaRow">
                                    <i class="fas fa-plus mr-1"></i> Add Image + Caption
                                </button>
                            </div>
                            <small class="text-muted d-block mb-3">Add multiple image+caption pairs.</small>

                            <div id="newsMediaContainer"></div>
                        </div>

                        {{-- LAYOUT 2 --}}
                        <div id="layoutHero" class="layout-panel" style="display:none;">
                            <div class="form-group">
                                <label>Hero Image</label>

                                <img id="heroPreview" class="media-preview mb-2"
                                    src="https://via.placeholder.com/600x350?text=No+Image">

                                <input type="file" name="hero_image" class="form-control-file preview-input"
                                    data-preview-target="#heroPreview" accept="image/*">
                            </div>

                            <div class="form-group">
                                <label>Body</label>
                                <textarea name="hero_caption" class="form-control" rows="8"></textarea>
                            </div>
                        </div>

                        {{-- LAYOUT 3 --}}
                        <div id="layoutSplit" class="layout-panel" style="display:none;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Left Image</label>

                                    <img id="splitLeftPreview" class="media-preview mb-2"
                                        src="https://via.placeholder.com/600x350?text=No+Image">

                                    <input type="file" name="split_left_image" class="form-control-file preview-input"
                                        data-preview-target="#splitLeftPreview" accept="image/*">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Right Body</label>
                                    <textarea name="split_right_caption" class="form-control" rows="8"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- LAYOUT 4 --}}
                        <div id="layoutArticle" class="layout-panel" style="display:none;">
                            <div class="form-group">
                                <label>Article Body</label>
                                <textarea
                                    name="article_body"
                                    class="form-control"
                                    rows="12"
                                    placeholder="Write the article here..."></textarea>
                            </div>
                        </div>

                        {{-- LAYOUT 5 --}}
                        <div id="layoutGalleryArticle" class="layout-panel" style="display:none;">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0">
                                    <i class="far fa-images mr-2"></i> Slider Images
                                </h5>
                            </div>

                            <small class="text-muted d-block mb-3">
                                Images will appear as a slider on the public page.
                            </small>

                            <div id="mediaGrid" class="media-grid">
                                <!-- image cards injected here -->
                                <div class="media-card add-card" id="addMediaCard">
                                    <span>+</span>

                                    <input
                                        type="file"
                                        id="mediaInput"
                                        accept="image/*"
                                        multiple
                                        style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer;"
                                    >
                                </div>

                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Article Body</label>
                                <textarea
                                    name="article_body"
                                    class="form-control"
                                    rows="10"
                                    placeholder="Write the article content below the image slider..."></textarea>
                            </div>
                        </div>



                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-outline-secondary" id="newsBackBtn" style="display:none;">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>

                    <button type="button" class="btn btn-primary" id="newsNextBtn">
                        Next <i class="fas fa-arrow-right ml-1"></i>
                    </button>

                    <button type="submit" class="btn btn-primary" id="newsSaveBtn" style="display:none;">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        .media-row {
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
            background: #fff;
        }

        .media-grid-generated {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: start;
        }

        .media-preview {
            width: 100%;
            max-height: 240px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, .1);
        }

        .media-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .media-actions .badge {
            font-size: 12px;
        }

        .thumb-flag {
            user-select: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .ui-state-highlight {
            height: 100px;
            background: rgba(100, 149, 237, 0.2);
            border: 1px dashed #6495ED;
            margin-bottom: 12px;
            border-radius: 10px;
        }

        .ui-sortable-helper {
            z-index: 1055 !important;
            /* slightly above modal backdrop */
        }


        /* ===============================
       MINI LAYOUT PREVIEWS
    ================================ */

        .layout-preview {
            width: 100%;
            height: 110px;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 6px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* generic blocks */
        .lp-img {
            background: #ced4da;
            border-radius: 4px;
        }

        .lp-text {
            background: #e9ecef;
            border-radius: 4px;
        }

        /* row helpers */
        .lp-row {
            display: flex;
            gap: 6px;
            flex: 1;
        }

        /* Hover polish */
        .layout-card {
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .layout-card:hover {
            transform: translateY(-2px);
        }

        .layout-1 .lp-img {
            flex: 1;
        }

        .layout-1 .lp-text {
            flex: 2;
        }

        .layout-2 .lp-hero {
            flex: 2;
            background: #adb5bd;
            border-radius: 4px;
        }

        .layout-2 .lp-article {
            flex: 1;
            background: #e9ecef;
            border-radius: 4px;
        }

        .layout-3 .lp-img {
            flex: 1;
        }

        .layout-3 .lp-text {
            flex: 1;
        }

        .layout-4 .lp-line {
            height: 12px;
            background: #dee2e6;
            border-radius: 4px;
        }

        /* ===== Layout 5: Slider + Caption ===== */

        .layout-5 {
            justify-content: space-between;
        }

        .layout-5 .lp-slider {
            position: relative;
            flex: 1;
            background: #ced4da;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .layout-5 .lp-slide {
            width: 70%;
            height: 70%;
            background: #adb5bd;
            border-radius: 4px;
        }

        .layout-5 .lp-arrow {
            position: absolute;
            font-size: 18px;
            font-weight: bold;
            color: #6c757d;
            user-select: none;
        }

        .layout-5 .lp-arrow.left {
            left: 6px;
        }

        .layout-5 .lp-arrow.right {
            right: 6px;
        }

        .layout-5 .lp-caption {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            margin-top: 6px;
        }

        .layout-5 .lp-img {
            flex: 1;
        }


        .layout-card {
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 12px;
        }

        .layout-card.active {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .15);
        }

        .layout-card.active {
            transform: scale(1.02);
        }



        /* ===== Media Grid ===== */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }

        .media-card {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 10px;
            overflow: hidden;
            background: #f8f9fa;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
        }

        .media-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-card .remove-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 22px;
            height: 22px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
        }

        .add-card {
            border: 2px dashed #bbb;
            font-size: 32px;
            color: #999;
            cursor: pointer;
        }

        .add-card:hover {
            border-color: #007bff;
            color: #007bff;
        }


        @media (max-width: 768px) {
            .media-grid-generated {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        (function () {

            // Make rows sortable and draggable
            $('#newsMediaContainer').sortable({
                handle: '.media-actions', // or remove handle to make the whole row draggable
                axis: 'y',
                placeholder: "ui-state-highlight",
                update: function () {
                    $('#newsMediaContainer .media-row').each(function (i) {
                        $(this).find('input[name$="[sort_order]"]').val(i);
                        $(this).find('.badge').text(`Row ${i + 1}`);
                    });
                }
            });


            // Preview selected image
            $(document).on('change', '.media-input', function () {
                const target = $(this).data('preview');
                const file = this.files?.[0];
                if (!file) return;

                const url = URL.createObjectURL(file);
                $(target).attr('src', url);
            });

            // When user selects "Set as Thumbnail" radio, set hidden is_thumbnail flags
            $(document).on('change', 'input[name="thumbnail_choice"]', function () {
                const chosenIndex = $(this).val();

                // Reset all hidden flags to 0
                $('#newsMediaContainer input[name^="media["][name$="[is_thumbnail]"]').val('0');

                // Set chosen row hidden flag to 1
                $(`#newsMediaContainer .media-row[data-index="${chosenIndex}"]`)
                    .find(`input[name="media[${chosenIndex}][is_thumbnail]"]`)
                    .val('1');
            });

            // Reset dynamic section whenever modal opens for ADD
            // (If your modal.js already handles clearing, you can remove this.)
            $('#dotuniNewsModal').on('hidden.bs.modal', function () {
                // optional: clear dynamic rows on close
                // comment this out if you want rows to persist while the modal is open/close
                $('#newsMediaContainer').empty();
                rowIndex = 0;

                // reset thumbnail choice
                $('input[name="thumbnail_choice"]').prop('checked', false);

                // reset thumbnail preview (top)
                const $thumb = $('#dotuniNewsThumbPreview');
                $thumb.attr('src', $thumb.data('placeholder') || 'https://via.placeholder.com/300x200?text=No+Thumbnail');
            });



         


        })();
    </script>
@endpush