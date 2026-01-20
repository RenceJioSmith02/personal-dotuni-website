<div class="modal fade modern-modal animated-modal" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="userForm">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">
            <i class="fas fa-user mr-2"></i> Add User
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          @csrf
          <input type="hidden" name="_method" class="form-method" value="POST">
          <input type="hidden" name="user_id" id="user_id">

          <div class="form-row">

            <!-- Email -->
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email"
                     name="email"
                     id="email"
                     class="form-control"
                     required>
            </div>

            <!-- Name -->
            <div class="form-group col-md-6">
              <label>Name</label>
              <input type="text"
                     name="name"
                     id="name"
                     class="form-control">
            </div>

            <!-- Password -->
            <div class="form-group col-md-6 password-field">
              <label>Password</label>
              <input type="password"
                     name="password"
                     class="form-control">
              <small class="text-muted edit-hint d-none">
                Leave blank to keep current password
              </small>
            </div>

            <!-- Status -->
            <div class="form-group col-md-6">
              <label>Status</label>
              <select name="is_active"
                      id="is_active"
                      class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

            <!-- Roles -->
            <div class="form-group col-md-12">
              <label>Roles</label>
              <div class="row">
                @foreach($roles as $role)
                  <div class="col-md-4">
                    <div class="form-check">
                      <input type="checkbox"
                             class="form-check-input"
                             name="roles[]"
                             id="role_{{ $role->id }}"
                             value="{{ $role->id }}">
                      <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
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
