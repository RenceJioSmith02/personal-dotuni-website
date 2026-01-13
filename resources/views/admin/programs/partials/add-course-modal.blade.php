<div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="addCourseForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Course</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Course</label>
            <select name="course_id" class="form-control" required>
              @foreach(\App\Models\Course::all() as $course)
              <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Requirement Category</label>
            <select name="requirement_category_id" class="form-control" required>
              @foreach(\App\Models\ProgramRequirementCategory::all() as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="0">
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
