<div class="modal fade modern-modal animated-modal" id="programModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="programForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-graduation-cap mr-2"></i> Program
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
                            <label>Program Image</label>

                            <div class="mb-2">
                                <img
                                    id="programImagePreview"
                                    class="img-thumbnail preview-img"
                                    data-input-target="#programImageInput"
                                    data-json-key="asset"
                                    data-placeholder="https://via.placeholder.com/200x120?text=No+Logo"
                                    src="https://via.placeholder.com/300x200?text=No+Image">

                            </div>

                            <input
                                type="file"
                                name="image"
                                class="form-control-file preview-input"
                                data-preview-target="#programImagePreview"
                                accept="image/*">

                            <small class="text-muted">
                                Optional. Uploading a new image will replace the existing one.
                            </small>
                        </div>

                        <!-- TITLE -->
                        <div class="form-group col-md-6">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- TYPE -->
                        <div class="form-group col-md-6">
                            <label>Program Type</label>
                            <input type="text" name="type" class="form-control" required>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="form-group col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- TOTAL UNITS -->
                        <div class="form-group col-md-6">
                            <label>Total Units</label>
                            <input type="number" name="total_units" class="form-control" min="0" required>
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
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
