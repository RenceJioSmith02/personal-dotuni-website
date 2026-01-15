<div class="modal fade modern-modal animated-modal" id="categoryModal" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <form id="categoryForm">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-tags mr-2"></i> Category
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @csrf
          <input type="hidden" name="_method" class="form-method" value="POST">

          <div class="form-group">
            <label>Category Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Save
          </button>
        </div>

      </div>
    </form>
  </div>
</div>
