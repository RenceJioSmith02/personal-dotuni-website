<div class="modal fade modern-modal animated-modal"
     id="prospectiveStudentItemModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="prospectiveStudentItemForm">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Prospective Student Item
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="form-row">

                        <!-- CATEGORY -->
                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <select name="category_id"
                                    class="form-control"
                                    required>
                                <option value="">-- Select Category --</option>
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

                        <!-- CONTENT -->
                        <div class="form-group col-md-12">
                            <label>Content</label>
                            <textarea
                                name="content"
                                class="form-control"
                                rows="5"
                                required
                                placeholder="Enter information for prospective students..."></textarea>
                        </div>

                        <!-- SORT ORDER -->
                        <div class="form-group col-md-6">
                            <label>Sort Order</label>
                            <input type="number"
                                   name="sort_order"
                                   class="form-control"
                                   value="0"
                                   min="0">
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
