{{-- <div class="modal fade" id="addRequirementModal" tabindex="-1" role="dialog">
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
          <div class="form-group">
            <label>MS</label>
            <input type="number" name="ms" class="form-control" min="0">
          </div>
          <div class="form-group">
            <label>MPS</label>
            <input type="number" name="mps" class="form-control" min="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div> --}}


<div class="modal fade modern-modal animated-modal" id="addRequirementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addRequirementForm">
            @csrf
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks mr-2"></i> Add Requirement
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <select name="requirement_category_id" class="form-control" required>
                                @foreach(\App\Models\ProgramRequirementCategory::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Required Units</label>
                            <input type="number" name="required_units" class="form-control" min="0" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>MS</label>
                            <input type="number" name="ms" class="form-control" min="0">
                        </div>

                        <div class="form-group col-md-6">
                            <label>MPS</label>
                            <input type="number" name="mps" class="form-control" min="0">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
