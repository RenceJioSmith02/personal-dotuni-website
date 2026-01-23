$(document).ready(function () {
    /* ===============================
       MODAL OPEN (ADD / EDIT)
    =============================== */
    $(document).on("click", ".open-modal", function () {
        const btn = $(this);
        const modal = $(btn.data("modal"));
        const form = $(btn.data("form"));
        const action = btn.data("action");
        const url = btn.data("url");
        const id = btn.data("id") || null;

        // Reset state
        form.trigger("reset");

        resetImagePreviews(form);

        modal.removeClass("modal-success modal-error");

        // Title
        modal.find(".modal-title").text(btn.data("title"));

        // ADD
        if (action === "add") {
            form.attr("action", url);
            form.find(".form-method").val("POST");

            modal.removeClass("force-close is-closing fade show");
            modal.modal("show");
        }

        // EDIT
        if (action === "edit") {
            $.get(`${url}/${id}/edit`, function (data) {
                populateForm(form, data);

                form.attr("action", `${url}/${id}`);
                form.find(".form-method").val("PUT");
                modal.removeClass("force-close is-closing fade show");
                modal.modal("show");
            });
        }
    });

    /* ===============================
   FORM SUBMIT (AJAX) WITH FILE SUPPORT
=============================== */
    $(document).on("submit", "form", function (e) {
        if (!$(this).closest(".modal").length) return;

        e.preventDefault();

        const form = $(this);
        const modal = form.closest(".modal");

        // Use FormData to handle files
        const formData = new FormData(form[0]);

        // Add _method if present
        const method = form.find(".form-method").val() || "POST";
        if (method !== "POST") formData.set("_method", method);

        $.ajax({
            url: form.attr("action"),
            type: "POST", // Always POST, Laravel will read _method
            data: formData,
            processData: false, // Prevent jQuery from converting to string
            contentType: false, // Let browser set content-type (multipart/form-data)
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                modal.addClass("modal-success");

                setTimeout(() => modal.modal("hide"), 400);

                Swal.fire({
                    type: "success",
                    title: "Success",
                    text: res.message || "Saved successfully",
                    timer: 1800,
                    showConfirmButton: false,
                });

                setTimeout(() => location.reload(), 1800);
            },
            error: function (xhr) {
                modal.addClass("modal-error");

                setTimeout(() => modal.removeClass("modal-error"), 400);

                let html = '<ul class="text-left">';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (_, msg) {
                        html += `<li>${msg[0]}</li>`;
                    });
                }
                html += "</ul>";

                Swal.fire({
                    type: "error",
                    title: "Validation Error",
                    html: html,
                });
            },
        });
    });

    /* ===============================
       MODAL ANIMATIONS
    =============================== */
    $(".modal").on("shown.bs.modal", function () {
        $(this).find("input:visible:first").focus();
    });

    /* ===============================
    MODAL CLOSE WITH ANIMATION
    =============================== */
    $(document).on(
        "click",
        "[data-dismiss='modal'], .modal-close",
        function (e) {
            e.preventDefault();

            const modal = $(this).closest(".modal");

            closeAnimatedModal(modal);
        },
    );

    /* Bootstrap hide event override */
    $(".modal").on("hide.bs.modal", function (e) {
        if (!$(this).hasClass("animated-modal")) return;
        if ($(this).hasClass("force-close")) return;

        e.preventDefault();
        closeAnimatedModal($(this));
    });

    /* Reusable close function */
    function closeAnimatedModal(modal) {
        if (modal.hasClass("is-closing")) return;

        modal.addClass("is-closing");

        $(".modal-backdrop").addClass("fade-out");

        setTimeout(() => {
            modal
                .removeClass("is-closing")
                .addClass("force-close")
                .modal("hide");

            modal.removeClass("force-close");
            $(".modal-backdrop").removeClass("fade-out");
        }, 300);
    }

    function resetImagePreviews(form) {
        form.find(".preview-img").each(function () {
            const img = $(this);

            const placeholder =
                img.attr("data-placeholder") ||
                "https://via.placeholder.com/300x200?text=No+Image";

            img.attr("src", placeholder);
        });

        // Reset file inputs safely
        form.find('input[type="file"]').val("");
    }

    window.newsMediaHelper = {
        rowIndex: 0,

        // Generate a media row HTML
        makeRow: function (index, mediaData = null) {
            const isOdd = index % 2 === 0; // Row 1 => index 0 => image left, caption right
            const rowId = `newsMediaRow_${index}`;

            const imageSrc = mediaData?.image_path
                ? `/storage/${mediaData.image_path}`
                : "https://via.placeholder.com/600x350?text=No+Image";

            const isThumbnail = mediaData?.is_thumbnail ? "checked" : "";
            const hiddenThumbnail = mediaData?.is_thumbnail ? "1" : "0";
            const caption = mediaData?.caption || "";

            // Include hidden input for existing media ID if it exists
            const existingIdInput = mediaData?.id
                ? `<input type="hidden" name="existing_media_ids[]" value="${mediaData.id}">`
                : "";

            const imageCol = `
        <div>
            <label class="mb-1">Image</label>
            <div class="mb-2">
                <img
                    src="${imageSrc}"
                    class="media-preview"
                    id="mediaPreview_${index}"
                    data-placeholder="https://via.placeholder.com/600x350?text=No+Image"
                    alt="Preview">
            </div>

            <input
                type="file"
                name="media[${index}][image]"
                class="form-control-file media-input"
                data-preview="#mediaPreview_${index}"
                accept="image/*">
        </div>
    `;

            const captionCol = `
        <div>
            <label class="mb-1">Caption</label>
            <textarea
                name="media[${index}][caption]"
                class="form-control"
                rows="6"
                maxlength="500"
                placeholder="Write a caption (optional)...">${caption}</textarea>

            <div class="mt-2 d-flex align-items-center justify-content-between">
                <div>
                    <label class="thumb-flag mb-0">
                        <input type="radio" name="thumbnail_choice" value="${index}" ${isThumbnail}>
                        Set as Thumbnail
                    </label>
                </div>
            </div>

            <input type="hidden" name="media[${index}][is_thumbnail]" value="${hiddenThumbnail}">
            <input type="hidden" name="media[${index}][is_cover]" value="0">
            ${existingIdInput}
        </div>
    `;

            const first = isOdd ? imageCol : captionCol;
            const second = isOdd ? captionCol : imageCol;

            return `
        <div class="media-row" id="${rowId}" data-index="${index}">
            <div class="media-actions">
                <span class="badge badge-light">Row ${index + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-media-row">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </div>

            <div class="media-grid">
                ${first}
                ${second}
            </div>
        </div>
    `;
    },
        // Refresh the row badges
        refreshBadges: function () {
            $("#newsMediaContainer .media-row").each(function (i) {
                $(this)
                    .find(".badge")
                    .text(`Row ${i + 1}`);
            });
        },

        // Add a new row
        addRow: function () {
            $("#newsMediaContainer").append(this.makeRow(this.rowIndex));
            this.rowIndex++;
            this.refreshBadges();
            $("#newsMediaContainer").sortable("refresh");
        },

        // Remove a row
        removeRow: function (row) {
            const removedIndex = row.data("index");

            // Clear thumbnail selection if removed
            const selected = $('input[name="thumbnail_choice"]:checked').val();
            if (String(selected) === String(removedIndex)) {
                $('input[name="thumbnail_choice"]').prop("checked", false);
            }

            row.remove();
            this.refreshBadges();
        },
    };

    // ==========================
    // Event listeners
    // ==========================

    // Add new row
    $(document).on("click", "#addNewsMediaRow", function () {
        window.newsMediaHelper.addRow();
    });

    // Remove row
    $(document).on("click", ".remove-media-row", function () {
        const row = $(this).closest(".media-row");
        window.newsMediaHelper.removeRow(row);
    });

    /* ===============================
    FORM AUTO-POPULATE (GENERIC)
    =============================== */

    function populateForm(form, data) {
        for (const key in data) {
            const input = form.find(`[name="${key}"]`);
            if (!input.length) {
                // Special case: array inputs (checkboxes)
                if (Array.isArray(data[key])) {
                    const arr = data[key];
                    arr.forEach((val) => {
                        form.find(`[name="${key}[]"][value="${val}"]`).prop(
                            "checked",
                            true,
                        );
                    });
                }
                continue;
            }

            const type = input.attr("type");
            const tag = input.prop("tagName").toLowerCase();
            let value = data[key];

            // Normalize boolean → string for selects
            if (tag === "select") {
                if (value === true) value = "1";
                if (value === false) value = "0";
                input.val(String(value)).trigger("change");
            } else if (type === "checkbox") {
                input.prop("checked", !!value);
            } else if (type === "radio") {
                input.filter(`[value="${value}"]`).prop("checked", true);
            } else if (type === "datetime-local") {
                if (value) {
                    // Convert "YYYY-MM-DD HH:MM:SS" → "YYYY-MM-DDTHH:MM"
                    value = value.replace(" ", "T").slice(0, 16);
                    input.val(value);
                }
            } else if (type === "file") {
                continue;
            } else {
                input.val(value ?? "");
            }
        }

        // IMAGE PREVIEW (EDIT) UNIVERSAL
        form.find(".preview-img").each(function () {
            const img = $(this);
            const inputSelector = img.data("input-target");
            const jsonKey = img.data("json-key");

            if (data[jsonKey] && data[jsonKey].storage_path) {
                img.attr("src", `/storage/${data[jsonKey].storage_path}`);
            } else {
                img.attr(
                    "src",
                    img.attr("data-placeholder") ||
                        "https://via.placeholder.com/300x200?text=No+Image",
                );
            }
        });

        if (Array.isArray(data.media)) {
            const container = $("#newsMediaContainer");
            container.empty();
            window.newsMediaHelper.rowIndex = 0;

            data.media.forEach((media) => {
                const html = window.newsMediaHelper.makeRow(
                    window.newsMediaHelper.rowIndex,
                    media, // pass existing media data
                );
                container.append(html);
                window.newsMediaHelper.rowIndex++;
            });

            window.newsMediaHelper.refreshBadges();
        }

    }

    // Universal image preview
    $(document).on("change", ".preview-input", function (e) {
        const input = $(this);
        const targetSelector = input.data("preview-target");
        const preview = $(targetSelector);

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.attr("src", e.target.result);
        };
        reader.readAsDataURL(file);
    });
});
