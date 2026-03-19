<div class="modal fade modern-modal animated-modal" id="courseModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="courseForm">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="courseModalTitle">
            <i class="fas fa-book mr-2"></i> Add Course
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          @csrf
          <input type="hidden" name="_method" class="form-method" value="POST">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Course Code</label>
              <input type="text" name="code" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
              <label>Course Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>

            <div class="form-group col-md-12">
              <label>Description</label>
              <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group col-md-4">
              <label>Units</label>
              <input type="number" name="units" class="form-control" min="0" required>
            </div>

            <div class="form-group col-md-4">
              <label>Prerequisite</label>
              <input type="text" name="prerequisite" class="form-control" placeholder="Optional">
            </div>

            <div class="form-group col-md-4">
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
