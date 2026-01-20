{{-- Role Modal --}}
<div class="modal fade modern-modal animated-modal" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="roleForm">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalTitle">
                        <i class="fas fa-user-shield mr-2"></i> Add Role
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="_method" class="form-method" value="POST">
                    <input type="hidden" name="role_id" id="role_id">

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Role Name</label>
                            <input type="text" name="name" class="form-control" required>
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