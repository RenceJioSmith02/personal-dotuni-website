<div class="modal fade modern-modal animated-modal" id="dotuniNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1000px;">
        <form id="dotuniNewsForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" class="form-method" value="POST">

            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-newspaper mr-2"></i> DotUni News
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    {{-- ==========================
                       STATIC FIELDS (TOP)
                    =========================== --}}
                    <div class="form-row">

                        <!-- TITLE -->
                        <div class="form-group col-md-12">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- HEADLINE -->
                        <div class="form-group col-md-12">
                            <label>Headline (Optional)</label>
                            <input type="text" name="headline" class="form-control" maxlength="250">
                        </div>

                        <!-- SEO TITLE -->
                        <div class="form-group col-md-6">
                            <label>SEO Title</label>
                            <input type="text" name="seo_title" class="form-control" maxlength="250" required>
                        </div>

                        <!-- SEO DESCRIPTION -->
                        <div class="form-group col-md-6">
                            <label>SEO Description</label>
                            <input type="text" name="seo_description" class="form-control" maxlength="300" required>
                        </div>

                        <!-- STATUS -->
                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <!-- VISIBILITY -->
                        <div class="form-group col-md-4">
                            <label>Visibility</label>
                            <select name="visibility" class="form-control" required>
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="unlisted">Unlisted</option>
                            </select>
                        </div>

                        <!-- PUBLISHED AT -->
                        <div class="form-group col-md-4">
                            <label>Published At (Optional)</label>
                            <input type="datetime-local" name="published_at" class="form-control">
                        </div>
                    </div>

                    <hr>

                    {{-- ==========================
                       DYNAMIC IMAGES + CAPTIONS
                    =========================== --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">
                            <i class="far fa-images mr-2"></i> Images & Captions (Optional)
                        </h5>

                        <button type="button" class="btn btn-sm btn-outline-primary" id="addNewsMediaRow">
                            <i class="fas fa-plus mr-1"></i> Add Image + Caption
                        </button>
                    </div>

                    <small class="text-muted d-block mb-3">
                        Add multiple image+caption pairs. Layout alternates automatically per row.
                    </small>

                    <div id="newsMediaContainer"></div>

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

@push('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    .media-row {
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 12px;
        background: #fff;
    }

    .media-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        align-items: start;
    }

    .media-preview {
        width: 100%;
        max-height: 240px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,.1);
    }

    .media-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .media-actions .badge {
        font-size: 12px;
    }

    .thumb-flag {
        user-select: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
    }

    .ui-state-highlight {
        height: 100px;
        background: rgba(100, 149, 237, 0.2);
        border: 1px dashed #6495ED;
        margin-bottom: 12px;
        border-radius: 10px;
    }
    .ui-sortable-helper {
        z-index: 1055 !important; /* slightly above modal backdrop */
    }



    @media (max-width: 768px) {
        .media-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('js')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
(function () {

    // Make rows sortable and draggable
    $('#newsMediaContainer').sortable({
        handle: '.media-actions', // or remove handle to make the whole row draggable
        axis: 'y',
        placeholder: "ui-state-highlight",
        update: function () {
            $('#newsMediaContainer .media-row').each(function (i) {
                $(this).find('input[name$="[sort_order]"]').val(i);
                $(this).find('.badge').text(`Row ${i + 1}`);
            });
        }
    });


    // Preview selected image
    $(document).on('change', '.media-input', function () {
        const target = $(this).data('preview');
        const file = this.files?.[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        $(target).attr('src', url);
    });

    // When user selects "Set as Thumbnail" radio, set hidden is_thumbnail flags
    $(document).on('change', 'input[name="thumbnail_choice"]', function () {
        const chosenIndex = $(this).val();

        // Reset all hidden flags to 0
        $('#newsMediaContainer input[name^="media["][name$="[is_thumbnail]"]').val('0');

        // Set chosen row hidden flag to 1
        $(`#newsMediaContainer .media-row[data-index="${chosenIndex}"]`)
            .find(`input[name="media[${chosenIndex}][is_thumbnail]"]`)
            .val('1');
    });

    // Reset dynamic section whenever modal opens for ADD
    // (If your modal.js already handles clearing, you can remove this.)
    $('#dotuniNewsModal').on('hidden.bs.modal', function () {
        // optional: clear dynamic rows on close
        // comment this out if you want rows to persist while the modal is open/close
        $('#newsMediaContainer').empty();
        rowIndex = 0;

        // reset thumbnail choice
        $('input[name="thumbnail_choice"]').prop('checked', false);

        // reset thumbnail preview (top)
        const $thumb = $('#dotuniNewsThumbPreview');
        $thumb.attr('src', $thumb.data('placeholder') || 'https://via.placeholder.com/300x200?text=No+Thumbnail');
    });

})();
</script>
@endpush
