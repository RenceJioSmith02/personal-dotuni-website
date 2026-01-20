<div class="modal fade modern-modal animated-modal" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form id="categoryForm">
            @csrf
            <input type="hidden" class="form-method" name="_method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-folder-open mr-2"></i>
                        Prospective Student Category
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- CATEGORY NAME -->
                    <div class="form-group">
                        <label>Category Name</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Enter category name"
                            required>
                    </div>

                    <div class="form-row">

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
