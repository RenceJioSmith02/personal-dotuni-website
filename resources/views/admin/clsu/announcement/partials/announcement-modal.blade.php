<div class="modal fade modern-modal animated-modal" id="announcementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="announcementForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-bullhorn mr-2"></i> Announcement
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- STEP INDICATOR --}}
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge badge-primary mr-2" id="announcementStepBadge">Step 1 of 2</span>
                        <small class="text-muted" id="announcementStepHint">Fill details then choose a layout</small>
                    </div>

                    {{-- =====================
                    STEP 1: META + LAYOUT
                    ====================== --}}
                    <div id="announcementStep1">

                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label>SEO Title (Hashtags)</label>
                                <div class="hashtag-input-container" id="hashtagContainer">
                                    <input type="text" id="hashtagInput" placeholder="Type a word and press Enter">
                                </div>
                                <input type="hidden" name="seo_title" id="seoTitleInput">
                            </div>

                            <div class="form-group col-md-4">
                                <label>SEO Description</label>
                                <input type="text" name="seo_description" class="form-control" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Visibility</label>
                                <select name="visibility" class="form-control" required>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="unlisted">Unlisted</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Publish Start</label>
                                <input type="datetime-local" name="publish_start" class="form-control mb-2">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Publish End</label>
                                <input type="datetime-local" name="publish_end" class="form-control">
                            </div>
                        </div>

                        <hr>

                        {{-- LAYOUT PICKER --}}
                        <h5><i class="fas fa-layer-group mr-2"></i> Choose Announcement Layout</h5>
                        <small class="text-muted d-block mb-3">
                            Layouts behave like News, but documents can be attached in all layouts.
                        </small>

                        <input type="hidden" name="layout" id="announcementLayout" value="layout_1">

                        {{-- SAME layout cards as news --}}
                        {{-- @include('admin.partials.layout-cards') --}}


                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <div class="card layout-card announcement-layout-card" data-layout="layout_2">
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
                                <div class="card layout-card announcement-layout-card" data-layout="layout_3">
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
                                <div class="card layout-card announcement-layout-card" data-layout="layout_4">
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
                            
                        </div>

                        
                    </div>

                    {{-- =====================
                    STEP 2: CONTENT
                    ====================== --}}
                    <div id="announcementStep2" style="display:none;">

                        <div class="alert alert-light border d-flex justify-content-between">
                            <strong>Selected layout:</strong>
                            <span id="announcementSelectedLayoutLabel"></span>
                        </div>

                        {{-- OPTIONAL DOCUMENTS (GLOBAL) --}}
                        <div class="form-group">
                            <label>
                                Attach Documents <small class="text-muted">(Optional)</small>
                            </label>
                            <input
                                type="file"
                                name="documents[]"
                                class="form-control-file"
                                accept=".pdf,.doc,.docx,.xls,.xlsx"
                                multiple
                            >
                            <small class="text-muted">
                                PDF, Word, Excel files allowed
                            </small>
                        </div>

                        <hr>

                        {{-- LAYOUT 1 --}}
                        <div id="announcementLayoutClassic" class="layout-panel announcement-layout-panel">
                            <h5><i class="far fa-images mr-2"></i> Images & Captions</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary mb-2" id="addAnnouncementMediaRow">
                                <i class="fas fa-plus"></i> Add Image
                            </button>
                            <div id="announcementMediaContainer"></div>
                        </div>

                        {{-- LAYOUT 2 --}}
                        <div id="announcementLayoutHero" class="layout-panel announcement-layout-panel">
                            <label>Hero Image</label>
                            <img id="announcementHeroPreview" class="media-preview mb-2"
                                src="https://via.placeholder.com/600x350?text=No+Image">
                            <input
                            type="file"
                            name="hero_image"
                            class="form-control-file media-input"
                            data-preview="#announcementHeroPreview"
                            accept="image/*"
                            >

                            <label class="mt-3">Body</label>
                            <textarea name="hero_caption" class="form-control" rows="8"></textarea>
                        </div>

                        {{-- LAYOUT 3 --}}
                        <div id="announcementLayoutSplit" class="layout-panel announcement-layout-panel">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <label>Left Image</label>
                                    <img id="announcementSplitLeftPreview" class="media-preview mb-2"
                                        src="https://via.placeholder.com/600x350?text=No+Image">

                                    <input
                                    type="file"
                                    name="split_left_image"
                                    class="form-control-file media-input"
                                    data-preview="#announcementSplitLeftPreview"
                                    accept="image/*"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label>Right Body</label>
                                    <textarea name="split_right_caption" class="form-control" rows="8"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- LAYOUT 4 --}}
                        <div id="announcementLayoutContent" class="layout-panel announcement-layout-panel">
                            <label>Announcement Body</label>
                            <textarea name="article_body" class="form-control" rows="12"></textarea>
                        </div>

                        {{-- LAYOUT 5 --}}
                        <div id="announcementLayoutGallery" class="layout-panel announcement-layout-panel" style="display:none;">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0">
                                    <i class="far fa-images mr-2"></i> Slider Images
                                </h5>
                            </div>

                            <small class="text-muted d-block mb-3">
                                Images will appear as a slider on the public page.
                            </small>

                            <div id="announcementMediaGrid" class="media-grid">
                                <div class="media-card add-card" id="announcementAddMediaCard">
                                    <span>+</span>

                                    <input
                                        type="file"
                                        id="announcementMediaInput"
                                        accept="image/*"
                                        multiple
                                        style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer;"
                                    >
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Body</label>
                                <textarea
                                    name="article_body"
                                    class="form-control"
                                    rows="10"
                                    placeholder="Write the announcement content below the slider..."></textarea>
                            </div>
                        </div>


                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-outline-secondary" id="announcementBackBtn" style="display:none;">
                        Back
                    </button>

                    <button type="button" class="btn btn-primary" id="announcementNextBtn">
                        Next
                    </button>

                    <button type="submit" class="btn btn-primary" id="announcementSaveBtn" style="display:none;">
                        Save
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

            /* ============================
               SORTABLE MEDIA ROWS
            ============================ */

            $('#announcementMediaContainer').sortable({
                handle: '.media-actions', // drag handle
                axis: 'y',
                placeholder: "ui-state-highlight",
                update: function () {
                    $('#announcementMediaContainer .media-row').each(function (i) {
                        $(this).find('input[name$="[sort_order]"]').val(i);
                        $(this).find('.badge').text(`Row ${i + 1}`);
                    });
                }
            });


            /* ============================
               IMAGE PREVIEW
            ============================ */

            $(document).on('change', '.media-input', function () {
                const target = $(this).data('preview');
                const file = this.files?.[0];
                if (!file) return;

                const url = URL.createObjectURL(file);
                $(target).attr('src', url);
            });


            /* ============================
               THUMBNAIL SELECTION
            ============================ */

            $(document).on('change', 'input[name="thumbnail_choice"]', function () {
                const chosenIndex = $(this).val();

                // Reset all hidden flags
                $('#announcementMediaContainer input[name^="media["][name$="[is_thumbnail]"]').val('0');

                // Set chosen row
                $(`#announcementMediaContainer .media-row[data-index="${chosenIndex}"]`)
                    .find(`input[name="media[${chosenIndex}][is_thumbnail]"]`)
                    .val('1');
            });


            /* ============================
               RESET ON MODAL CLOSE (ADD MODE)
            ============================ */

            $('#announcementModal').on('hidden.bs.modal', function () {

                // Clear dynamic rows
                $('#announcementMediaContainer').empty();
                rowIndex = 0;

                // Reset thumbnail radios
                $('input[name="thumbnail_choice"]').prop('checked', false);

                // Reset thumbnail preview (if exists)
                const $thumb = $('#announcementThumbPreview');
                if ($thumb.length) {
                    $thumb.attr(
                        'src',
                        $thumb.data('placeholder') || 'https://via.placeholder.com/300x200?text=No+Thumbnail'
                    );
                }
            });

        })();
    </script>
@endpush
