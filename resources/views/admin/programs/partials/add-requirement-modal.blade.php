<div class="modal fade" id="addRequirementModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="addRequirementForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Requirement</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <select name="requirement_category_id" class="form-control" required>
              @foreach(\App\Models\ProgramRequirementCategory::all() as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Required Units</label>
            <input type="number" name="required_units" class="form-control" min="0" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
