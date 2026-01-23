<div class="modal fade modern-modal animated-modal" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt mr-2"></i> Form
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="form-row">

                        <!-- FILE UPLOAD -->
                        <div class="form-group col-md-12">
                            <label>Document</label>

                            <div class="mb-2">
                                <a
                                    id="formFilePreview"
                                    class="btn btn-outline-secondary"
                                    target="_blank"
                                    href="#"
                                    data-placeholder="#"
                                    style="display:none;">
                                    <i class="fas fa-file-download mr-1"></i>
                                    View current file
                                </a>
                            </div>

                            <input
                                type="file"
                                name="file"
                                class="form-control-file"
                                accept=".pdf,.doc,.docx,.xls,.xlsx">

                            <small class="text-muted">
                                Uploading a new file will replace the existing one.
                            </small>
                        </div>

                        <!-- CATEGORY -->
                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <select name="form_category_id" class="form-control" required>
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

                        <!-- NAME -->
                        <div class="form-group col-md-12">
                            <label>Form Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required>
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
                                value="0">
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
