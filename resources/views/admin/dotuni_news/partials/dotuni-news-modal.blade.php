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

                        @if(auth()->user()->hasRole('admin'))
                            <div class="form-group col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
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
                        @endif

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



@push('js')

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        
        (function () {

            // Make rows sortable and draggable
            $('#newsMediaContainer').sortable({
                handle: '.media-actions', 
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

            $(document).on('change', 'input[name="thumbnail_choice"]', function () {
                const chosenIndex = $(this).val();

                // Reset all hidden flags to 0
                $('#newsMediaContainer input[name^="media["][name$="[is_thumbnail]"]').val('0');

                // Set chosen row hidden flag to 1
                $(`#newsMediaContainer .media-row[data-index="${chosenIndex}"]`)
                    .find(`input[name="media[${chosenIndex}][is_thumbnail]"]`)
                    .val('1');
            });


            $('#dotuniNewsModal').on('hidden.bs.modal', function () {
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