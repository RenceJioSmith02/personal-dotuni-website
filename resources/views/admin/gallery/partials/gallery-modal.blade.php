<div class="modal fade modern-modal animated-modal" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="galleryForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-images mr-2"></i> Gallery
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
                            <label>Gallery Image</label>

                            <div class="mb-2">
                                <img
                                    id="galleryImagePreview"
                                    class="img-thumbnail preview-img"
                                    data-input-target="#galleryImageInput"
                                    data-json-key="asset"
                                    data-placeholder="https://via.placeholder.com/300x200?text=No+Image"
                                    src="https://via.placeholder.com/300x200?text=No+Image">
                            </div>

                            <input
                                type="file"
                                id="galleryImageInput"
                                name="image"
                                class="form-control-file preview-input"
                                data-preview-target="#galleryImagePreview"
                                accept="image/*">

                            <small class="text-muted">
                                Uploading a new image will replace the existing image.
                            </small>
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

                        <!-- IS HOMEPAGE BANNER -->
                        <div class="form-group col-md-6">
                            <label>Homepage Banner</label>
                            <select name="is_homepage_banner" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
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
