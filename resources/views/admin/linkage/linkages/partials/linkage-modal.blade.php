<div class="modal fade modern-modal animated-modal" id="linkageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="linkageForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-link mr-2"></i> Linkage
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="form-row">

                        <!-- LOGO UPLOAD -->
                        <div class="form-group col-md-12">
                            <label>Logo</label>

                            <div class="mb-2">
                                <img
                                    id="linkageLogoPreview"
                                    class="img-thumbnail preview-img"
                                    data-json-key="logo"
                                    data-placeholder="https://via.placeholder.com/200x120?text=No+Logo"
                                    src="https://via.placeholder.com/200x120?text=No+Logo">
                            </div>

                            <input
                                type="file"
                                name="logo"
                                class="form-control-file preview-input"
                                data-preview-target="#linkageLogoPreview"
                                accept="image/*">

                            <small class="text-muted">
                                Optional. Uploading a new logo will replace the existing one.
                            </small>
                        </div>

                        <!-- CATEGORY -->
                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- STATUS -->
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- TITLE -->
                        <div class="form-group col-md-12">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- URL -->
                        <div class="form-group col-md-12">
                            <label>URL</label>
                            <input type="url" name="url" class="form-control" required>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="form-group col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
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
