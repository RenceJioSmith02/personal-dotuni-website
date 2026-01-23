<div class="modal fade modern-modal animated-modal" id="eResourceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="eResourceForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-book mr-2"></i> E-Resource
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="form-row">

                        <!-- NAME -->
                        <div class="form-group col-md-12">
                            <label>Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required>
                        </div>

                        <!-- LINK URL -->
                        <div class="form-group col-md-12">
                            <label>Link URL</label>
                            <input
                                type="url"
                                name="link_url"
                                class="form-control"
                                placeholder="https://example.com/resource"
                            >
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
