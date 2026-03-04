<div class="modal fade modern-modal animated-modal" id="faqAnswerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="faqAnswerForm">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-comment-dots mr-2"></i> FAQ Answer
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="form-row">

                        <!-- QUESTION SELECT -->
                        <div class="form-group col-md-12">
                            <label>Question</label>
                            <select name="faq_id" class="form-control" required>
                                <option value="">— Select Question —</option>
                                @foreach($questions as $question)
                                    <option value="{{ $question->id }}">
                                        {{ $question->question }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ANSWER -->
                        <div class="form-group col-md-12">
                            <label>Answer</label>
                            <textarea
                                name="answer"
                                class="form-control"
                                rows="5"
                                required
                                placeholder="Enter the answer for this question..."></textarea>
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
