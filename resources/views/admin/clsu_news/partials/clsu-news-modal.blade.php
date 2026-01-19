<div class="modal fade modern-modal animated-modal" id="clsuNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="clsuNewsForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-newspaper mr-2"></i> CLSU News
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="form-row">

                        <!-- IMAGE UPLOAD -->
                        <div class="form-group col-md-12">
                            <label>News Thumbnail</label>

                            <div class="mb-2">
                                <img
                                    id="clsuNewsImagePreview"
                                    class="img-thumbnail preview-img"
                                    data-input-target="#clsuNewsImageInput"
                                    data-json-key="thumbnail"
                                    data-placeholder="https://via.placeholder.com/300x200?text=No+Thumbnail"
                                    src="https://via.placeholder.com/300x200?text=No+Thumbnail">
                            </div>

                            <input
                                type="file"
                                id="clsuNewsImageInput"
                                name="image"
                                class="form-control-file preview-input"
                                data-preview-target="#clsuNewsImagePreview"
                                accept="image/*">

                            <small class="text-muted">
                                Optional. Uploading a new image will replace the existing thumbnail.
                            </small>
                        </div>

                        <!-- TITLE -->
                        <div class="form-group col-md-12">
                            <label>Title</label>
                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                required>
                        </div>

                        <!-- URL -->
                        <div class="form-group col-md-12">
                            <label>Article URL</label>
                            <input
                                type="url"
                                name="url"
                                class="form-control"
                                placeholder="https://example.com/news-article">
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="form-group col-md-12">
                            <label>Description</label>
                            <textarea
                                name="description"
                                class="form-control"
                                rows="3"></textarea>
                        </div>

                        <!-- SORT ORDER -->
                        <div class="form-group col-md-6">
                            <label>Sort Order</label>
                            <input
                                type="number"
                                name="sort_order"
                                class="form-control"
                                min="0"
                                value="0">
                        </div>

                        <!-- STATUS -->
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
