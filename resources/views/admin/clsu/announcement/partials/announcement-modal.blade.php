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
                        <span class="badge badge-primary mr-2" id="stepBadge">Step 1 of 2</span>
                        <small class="text-muted" id="stepHint">Fill details then choose a layout</small>
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

                            <div class="form-group col-md-6">
                                <label>SEO Title</label>
                                <input type="text" name="seo_title" class="form-control" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label>SEO Description</label>
                                <input type="text" name="seo_description" class="form-control" required>
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
                                <label>Publish Window</label>
                                <input type="datetime-local" name="publish_start" class="form-control mb-2">
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

                    {{-- =====================
                    STEP 2: CONTENT
                    ====================== --}}
                    <div id="announcementStep2" style="display:none;">

                        <div class="alert alert-light border d-flex justify-content-between">
                            <strong>Selected layout:</strong>
                            <span id="selectedLayoutLabel"></span>
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
                        <div id="layoutClassic" class="layout-panel">
                            <h5><i class="far fa-images mr-2"></i> Images & Captions</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary mb-2" id="addAnnouncementMediaRow">
                                <i class="fas fa-plus"></i> Add Image
                            </button>
                            <div id="announcementMediaContainer"></div>
                        </div>

                        {{-- LAYOUT 2 --}}
                        <div id="layoutHero" class="layout-panel" style="display:none;">
                            <label>Hero Image</label>
                            <img id="heroPreview" class="media-preview mb-2">
                            <input type="file" name="hero_image" class="form-control-file" accept="image/*">

                            <label class="mt-3">Body</label>
                            <textarea name="hero_caption" class="form-control" rows="8"></textarea>
                        </div>

                        {{-- LAYOUT 3 --}}
                        <div id="layoutSplit" class="layout-panel" style="display:none;">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <label>Left Image</label>
                                    <img id="splitPreview" class="media-preview mb-2">
                                    <input type="file" name="split_left_image" class="form-control-file" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label>Right Body</label>
                                    <textarea name="split_right_caption" class="form-control" rows="8"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- LAYOUT 4 --}}
                        <div id="layoutArticle" class="layout-panel" style="display:none;">
                            <label>Announcement Body</label>
                            <textarea name="article_body" class="form-control" rows="12"></textarea>
                        </div>

                        {{-- LAYOUT 5 --}}
                        <div id="layoutGalleryArticle" class="layout-panel" style="display:none;">
                            <div id="mediaGrid" class="media-grid"></div>

                            <label class="mt-3">Body</label>
                            <textarea name="article_body" class="form-control" rows="10"></textarea>
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
