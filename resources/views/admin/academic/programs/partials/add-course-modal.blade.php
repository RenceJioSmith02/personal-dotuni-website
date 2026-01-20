<div class="modal fade modern-modal animated-modal" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addCourseForm">
            @csrf
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-book mr-2"></i> Add Course
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label>Course</label>
                            <select name="course_id" class="form-control" required>
                                @foreach(\App\Models\Course::all() as $course)
                                <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Requirement Category</label>
                            <select name="requirement_category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
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
