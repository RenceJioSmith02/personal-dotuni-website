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

        if (action === "add") {
            form.attr("action", url);
            form.find(".form-method").val("POST");

            unlockLayoutSelection();

            resetLayout5Media();

            modal.removeClass("force-close is-closing fade show");
            modal.modal("show");
        }

        // EDIT
        if (action === "edit") {
            $.get(`${url}/${id}/edit`, function (data) {
                populateForm(form, data);
                applyLayout(data.layout || "layout_1");

                if (data.layout === "layout_5" && Array.isArray(data.media)) {
                    resetLayout5Media(); 
                    window.existingNewsMedia = data.media.map((m) => ({
                        id: m.id,
                        url: `/storage/${m.image_path}`,
                        caption: m.caption,
                        sort_order: m.sort_order,
                        is_thumbnail: m.is_thumbnail,
                    }));
                    renderExistingMedia();
                }


                lockLayoutSelection(data.layout);

                // go to step 2 if media exists
                if (Array.isArray(data.media) && data.media.length) setStep(2);
                else setStep(1);

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

            <div class="media-grid-generated">
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
    layout scripts
    =============================== */

    const NEWS_LAYOUTS = {
        layout_1: {
            label: "Layout 1 – Image & Captions",
            panel: "#layoutClassic",
        },
        layout_2: {
            label: "Layout 2 – Big Image + Article",
            panel: "#layoutHero",
        },
        layout_3: {
            label: "Layout 3 – Image + Text (Split)",
            panel: "#layoutSplit",
        },
        layout_4: {
            label: "Layout 4 – Article Only",
            panel: "#layoutArticle",
        },

        layout_5: {
            label: "Layout 5 – Image Slider + Article",
            panel: "#layoutGalleryArticle",
        },
    };

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

        // Layout 2 preview
        if (data.hero_image) {
            $("#heroPreview").attr("src", `/storage/${data.hero_image}`);
        }
        if (data.hero_caption) {
            $('textarea[name="hero_caption"]').val(data.hero_caption);
        }

        // Layout 3 preview
        if (data.split_left_image) {
            $("#splitLeftPreview").attr(
                "src",
                `/storage/${data.split_left_image}`,
            );
        }
        if (data.split_right_caption) {
            $('textarea[name="split_right_caption"]').val(
                data.split_right_caption,
            );
        }
    }

    function setStep(step) {
        const isStep1 = step === 1;

        $("#newsStep1").toggle(isStep1);
        $("#newsStep2").toggle(!isStep1);

        $("#newsNextBtn").toggle(isStep1);
        $("#newsSaveBtn").toggle(!isStep1);
        $("#newsBackBtn").toggle(!isStep1);

        $("#stepBadge").text(isStep1 ? "Step 1 of 2" : "Step 2 of 2");
        $("#stepHint").text(
            isStep1
                ? "Fill details then choose a layout"
                : "Fill layout content then save",
        );
    }

    function applyLayout(layoutKey) {
        const config = NEWS_LAYOUTS[layoutKey];
        if (!config) return;

        // save to hidden input
        $("#newsLayout").val(layoutKey);

        // label
        $("#selectedLayoutLabel").text(config.label);

        // panels
        $(".layout-panel").hide();
        if (config.panel) {
            $(config.panel).show();
        }

        // card highlight
        $(".layout-card").removeClass("active");
        $(`.layout-card[data-layout="${layoutKey}"]`).addClass("active");
    }



    
            // media script for layout 5

            // Add selected images
            $('#mediaInput').on('change', function (e) {
                [...e.target.files].forEach(file => addMediaCard(file));
                this.value = '';
            });

            function createFileList(file) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                return dataTransfer.files;
            }


            function addMediaCard(file) {
                const index = $('.media-card.image-card').length;

                const reader = new FileReader();
                reader.onload = function (e) {
                    const card = $(`
                        <div class="media-card image-card" draggable="true">
                            <span class="remove-btn">&times;</span>
                            <img src="${e.target.result}">
                            <input type="file" name="media[${index}][image]" hidden>
                            <input type="hidden" name="media[${index}][sort_order]" value="${index}">
                        </div>
                    `);

                    card.find('input[type="file"]')[0].files = createFileList(file);

                    $('#addMediaCard').before(card);
                    refreshSortOrder();
                };
                reader.readAsDataURL(file);
            }

            // Remove image
$(document).on("click", ".remove-btn", function () {
    const card = $(this).closest(".media-card");
    const index = card.data("index");

    // Remove hidden inputs with this index
    const form = $("#dotuniNewsForm");
    form.find(`input[data-index="${index}"]`).remove();

    // Remove card
    card.remove();

    // Refresh remaining indexes and hidden inputs
    refreshSortOrder();
    
});




            // Drag & Drop Reordering
            let dragged;

            $(document).on('dragstart', '.image-card', function () {
                dragged = this;
            });

            $(document).on('dragover', '.image-card', function (e) {
                e.preventDefault();
            });

            $(document).on('drop', '.image-card', function () {
                if (dragged !== this) {
                    $(this).before(dragged);
                    refreshSortOrder();
                }
            });

            // Update sort_order + media indexes
function refreshSortOrder() {
    $(".media-card.image-card").each(function (i) {
        const card = $(this);
        card.attr("data-index", i);

        // Update hidden inputs inside form
        const form = $("#dotuniNewsForm");
        form.find(`input[data-index]`).each(function () {
            const oldIndex = $(this).data("index");
            if (oldIndex === undefined) return;

            // Only update inputs that correspond to this card
            if (oldIndex === oldIndex) {
                $(this).attr("data-index", i);

                const name = $(this).attr("name");
                if (name.startsWith("media[")) {
                    const newName = name.replace(/media\[\d+]/, `media[${i}]`);
                    $(this).attr("name", newName);
                }
            }
        });
    });
}




       
function renderExistingMedia() {
    const grid = $("#mediaGrid");
    grid.find(".media-card.image-card").remove();

    const form = $("#dotuniNewsForm");

    window.existingNewsMedia.forEach((m, index) => {
        const card = $(`
            <div class="media-card image-card" data-index="${index}">
                <img src="${m.url}">
                <span class="remove-btn">×</span>
            </div>
        `);

        // Append card
        $("#addMediaCard").before(card);

        // Add hidden inputs inside the form
        form.append(
            `<input type="hidden" name="existing_media_ids[]" value="${m.id}" data-index="${index}">`,
        );
        form.append(
            `<input type="hidden" name="media[${index}][caption]" value="${m.caption ?? ""}" data-index="${index}">`,
        );
        form.append(
            `<input type="hidden" name="media[${index}][sort_order]" value="${index}" data-index="${index}">`,
        );
    });
}




    // Call it when modal opens for editing
    // $('#dotuniNewsModal').on('shown.bs.modal', function () {
    //     renderExistingMedia();
    // });
function resetLayout5Media() {
    window.existingNewsMedia = []; // clear existing data

    // Remove all current media cards
    $("#mediaGrid .media-card.image-card").remove();

    // Remove all hidden inputs for layout5
    const form = $("#dotuniNewsForm");
    form.find('input[name="existing_media_ids[]"]').remove();
    form.find('input[name^="media["]').remove();

    // Reset index counter
    window.newsMediaHelper.rowIndex = 0;
}



    // -----------------------------------------------------------------------------


    function lockLayoutSelection(activeLayout) {
        // Disable all layout cards
        $(".layout-card").addClass("disabled").css({
            pointerEvents: "none",
            opacity: 0.5,
        });

        // Re-enable & highlight the active one
        $(`.layout-card[data-layout="${activeLayout}"]`)
            .removeClass("disabled")
            .css({
                pointerEvents: "auto",
                opacity: 1,
            });

        // Hide ALL panels first
        $(".layout-panel").hide();

        // Show ONLY the panel for the current layout
        const config = NEWS_LAYOUTS[activeLayout];
        if (config?.panel) {
            $(config.panel).show();
        }

        // Optional UX hint
        $("#stepHint").text("Layout is locked for existing news");
    }

    function unlockLayoutSelection() {
        $(".layout-card").removeClass("disabled").css({
            pointerEvents: "auto",
            opacity: 1,
        });

        $("#stepHint").text("Fill details then choose a layout");
    }

    // click layout card
    $(document).on("click", ".layout-card", function () {
        applyLayout($(this).data("layout"));
    });

    // Next / Back
    $(document).on("click", "#newsNextBtn", function () {
        // minimal validation: required fields in step 1
        const form = $("#dotuniNewsForm")[0];
        // validate only visible required fields (simple approach)
        let valid = true;
        $("#newsStep1 :input[required]").each(function () {
            if (!this.value) valid = false;
        });
        if (!valid) {
            Swal.fire({
                icon: "warning",
                title: "Missing fields",
                text: "Please complete required fields before continuing.",
            });
            return;
        }

        setStep(2);
    });

    $(document).on("click", "#newsBackBtn", function () {
        setStep(1);
    });

    // reset wizard when modal opens/closes
    $("#dotuniNewsModal").on("shown.bs.modal", function () {
        setStep(1);
        applyLayout($("#newsLayout").val() || "layout_1");
    });

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
