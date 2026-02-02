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
            unlockAnnouncementLayoutSelection();

            resetLayout5Media();

            modal.removeClass("force-close is-closing fade show");
            modal.modal("show");
        }

        // EDIT

        // ===============================
        // EDIT (GENERIC + MODULE-AWARE)
        // ===============================
        if (action === "edit") {
            $.get(`${url}/${id}/edit`, function (data) {

                /* --------------------------------
                | 1. GENERIC AUTO-POPULATE (ALL MODULES)
                |-------------------------------- */
                if (typeof populateForm === "function") {
                    populateForm(form, data);
                }

                /* --------------------------------
                | 2. NEWS MODULE (OPTIONAL)
                |-------------------------------- */
                if (form.attr("id") === "dotuniNewsForm") {

                    applyLayout(data.layout || "layout_1");

                    if (data.layout === "layout_5" && Array.isArray(data.media)) {
                        resetLayout5Media();
                        loadExistingLayout5(data.media);
                    }

                    if (typeof lockLayoutSelection === "function") {
                        lockLayoutSelection(data.layout);
                    }

                    if (Array.isArray(data.media) && data.media.length) {
                        setStep(2);
                    } else {
                        setStep(1);
                    }
                }

                /* --------------------------------
                | 3. ANNOUNCEMENT MODULE (OPTIONAL)
                |-------------------------------- */
                if (form.attr("id") === "announcementForm") {

                    populateAnnouncementForm(form, data);
                    applyAnnouncementLayout(data.layout || "layout_1");

                    if (data.layout === "layout_5" && Array.isArray(data.media)) {
                        resetAnnouncementLayout5Media();
                        loadExistingAnnouncementLayout5(data.media);
                    }

                    if (typeof lockAnnouncementLayoutSelection === "function") {
                        lockAnnouncementLayoutSelection(data.layout);
                    }

                    if (Array.isArray(data.media) && data.media.length) {
                        setAnnouncementStep(2);
                    } else {
                        setAnnouncementStep(1);
                    }
                }

                /* --------------------------------
                | 4. FINALIZE FORM + SHOW MODAL
                |-------------------------------- */
                form.attr("action", `${url}/${id}`);
                form.find(".form-method").val("PUT");

                modal.removeClass("force-close is-closing fade show");
                modal.modal("show");
            });
        }

        // if (action === "edit") {
        //     $.get(`${url}/${id}/edit`, function (data) {
        //         // ======================
        //         // NEWS
        //         // ======================
        //         if (form.attr("id") === "dotuniNewsForm") {
        //             populateForm(form, data);
        //             applyLayout(data.layout || "layout_1");

        //             if (
        //                 data.layout === "layout_5" &&
        //                 Array.isArray(data.media)
        //             ) {
        //                 resetLayout5Media();
        //                 loadExistingLayout5(data.media);
        //             }

        //             lockLayoutSelection(data.layout);

        //             if (Array.isArray(data.media) && data.media.length)
        //                 setStep(2);
        //             else setStep(1);
        //         }

        //         // ======================
        //         // ANNOUNCEMENT
        //         // ======================
        //         if (form.attr("id") === "announcementForm") {
        //             populateAnnouncementForm(form, data);
        //             applyAnnouncementLayout(data.layout || "layout_1");

        //             if (
        //                 data.layout === "layout_5" &&
        //                 Array.isArray(data.media)
        //             ) {
        //                 resetAnnouncementLayout5Media();
        //                 loadExistingAnnouncementLayout5(data.media);
        //             }

        //             lockAnnouncementLayoutSelection(data.layout);

        //             // step wizard
        //             if (Array.isArray(data.media) && data.media.length) {
        //                 setAnnouncementStep(2);
        //             } else {
        //                 setAnnouncementStep(1);
        //             }
        //         }

        //         form.attr("action", `${url}/${id}`);
        //         form.find(".form-method").val("PUT");

        //         modal.removeClass("force-close is-closing fade show");
        //         modal.modal("show");
        //     });
        // }

        // if (action === "edit") {
        //     $.get(`${url}/${id}/edit`, function (data) {
        //         populateForm(form, data);
        //         applyLayout(data.layout || "layout_1");

        //         if (data.layout === "layout_5" && Array.isArray(data.media)) {
        //             resetLayout5Media();

        //             if (
        //                 data.layout === "layout_5" &&
        //                 Array.isArray(data.media)
        //             ) {
        //                 loadExistingLayout5(data.media);
        //             }
        //         }

        //         lockLayoutSelection(data.layout);

        //         // go to step 2 if media exists
        //         if (Array.isArray(data.media) && data.media.length) setStep(2);
        //         else setStep(1);

        //         form.attr("action", `${url}/${id}`);
        //         form.find(".form-method").val("PUT");
        //         modal.removeClass("force-close is-closing fade show");
        //         modal.modal("show");
        //     });
        // }
        
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





$(document).on("change", ".media-input", function (e) {
    const input = $(this);
    const preview = $(input.data("preview"));

    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => preview.attr("src", e.target.result);
    reader.readAsDataURL(file);
});










    /* ===============================
    NEWS MODULE SCRIPTS
    =============================== */

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



    // ===============================
    // LAYOUT 5 — STABLE MEDIA SYSTEM
    // ===============================

    window.layout5Media = [];

    function renderLayout5() {
        const grid = $("#mediaGrid");
        grid.find(".image-card").remove();

        window.layout5Media.forEach((m, i) => {
            const card = $(`
            <div class="media-card image-card" data-key="${i}">
                <span class="remove-btn">&times;</span>
                <img src="${m.url}">
            </div>
        `);

            $("#addMediaCard").before(card);
        });

        updateLayout5Inputs();
    }

    function updateLayout5Inputs() {
        const form = $("#dotuniNewsForm");

        // Clear only layout 5 inputs
        form.find('input[name="existing_media_ids[]"]').remove();
        form.find('input[name^="media["]').remove();

        window.layout5Media.forEach((m, index) => {
            if (m.type === "existing") {
                form.append(`
                <input type="hidden" name="existing_media_ids[]" value="${m.id}">
            `);
            }

            if (m.type === "new") {
                const input = $(
                    `<input type="file" name="media[${index}][image]" hidden>`,
                );
                input[0].files = createFileList(m.file);
                form.append(input);
            }

            form.append(`
            <input type="hidden" name="media[${index}][caption]" value="${m.caption ?? ""}">
            <input type="hidden" name="media[${index}][sort_order]" value="${index}">
            <input type="hidden" name="media[${index}][is_thumbnail]" value="${index === 0 ? 1 : 0}">
        `);
        });
    }

    $("#mediaInput").on("change", function (e) {
        [...e.target.files].forEach((file) => {
            window.layout5Media.push({
                type: "new",
                file,
                url: URL.createObjectURL(file),
                caption: "",
            });
        });

        renderLayout5();
        this.value = "";
    });

    $(document).on("click", ".remove-btn", function () {
        const key = $(this).closest(".image-card").data("key");
        window.layout5Media.splice(key, 1);
        renderLayout5();
    });

    $("#mediaGrid").sortable({
        items: ".image-card",
        cancel: "#addMediaCard",
        tolerance: "pointer",

        update() {
            const reordered = [];

            $("#mediaGrid .image-card").each(function () {
                const key = $(this).data("key");
                reordered.push(window.layout5Media[key]);
            });

            window.layout5Media = reordered;
            renderLayout5(); // re-render to reset keys
        },
    });

    function loadExistingLayout5(media) {
        window.layout5Media = media
            .sort((a, b) => a.sort_order - b.sort_order)
            .map((m) => ({
                type: "existing",
                id: m.id,
                url: `/storage/${m.image_path}`,
                caption: m.caption,
            }));

        renderLayout5();
    }

    // Add selected images

    function createFileList(file) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        return dataTransfer.files;
    }

    function resetLayout5Media() {
        window.layout5Media = [];
        $("#mediaGrid .image-card").remove();

        const form = $("#dotuniNewsForm");
        form.find('input[name="existing_media_ids[]"]').remove();
        form.find('input[name^="media["]').remove();
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

    /* ===============================
    NEWS MODULE END SCRIPT
    =============================== */














    

    /* ===============================
ANNOUNCEMENT MODULE SCRIPTS
=============================== */

    window.announcementMediaHelper = {
        rowIndex: 0,

        // Generate a media row HTML
        makeRow: function (index, mediaData = null) {
            const isOdd = index % 2 === 0;
            const rowId = `announcementMediaRow_${index}`;

            const imageSrc = mediaData?.image_path
                ? `/storage/${mediaData.image_path}`
                : "https://via.placeholder.com/600x350?text=No+Image";

            const isThumbnail = mediaData?.is_thumbnail ? "checked" : "";
            const hiddenThumbnail = mediaData?.is_thumbnail ? "1" : "0";
            const caption = mediaData?.caption || "";

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
                    id="announcementMediaPreview_${index}"
                    data-placeholder="https://via.placeholder.com/600x350?text=No+Image"
                    alt="Preview">
            </div>

            <input
                type="file"
                name="media[${index}][image]"
                class="form-control-file media-input"
                data-preview="#announcementMediaPreview_${index}"
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
            $("#announcementMediaContainer .media-row").each(function (i) {
                $(this)
                    .find(".badge")
                    .text(`Row ${i + 1}`);
            });
        },

        // Add a new row
        addRow: function () {
            $("#announcementMediaContainer").append(
                this.makeRow(this.rowIndex),
            );
            this.rowIndex++;
            this.refreshBadges();
            if ($("#announcementMediaContainer").data("ui-sortable")) {
                $("#announcementMediaContainer").sortable("refresh");
            }
        },

        // Remove a row
        removeRow: function (row) {
            row.remove();
            this.refreshBadges();
        },
    };

    // ==========================
    // Event listeners
    // ==========================

    // Add new row
    $(document).on("click", "#addAnnouncementMediaRow", function () {
        window.announcementMediaHelper.addRow();
    });

    // Remove row
    $(document).on("click", ".remove-media-row", function () {
        const row = $(this).closest(".media-row");
        window.announcementMediaHelper.removeRow(row);
    });

    /* ===============================
LAYOUT scripts
=============================== */

    const ANNOUNCEMENT_LAYOUTS = {
        layout_1: {
            label: "Layout 1 – Image & Captions",
            panel: "#announcementLayoutClassic",
        },
        layout_2: {
            label: "Layout 2 – Big Image + Content",
            panel: "#announcementLayoutHero",
        },
        layout_3: {
            label: "Layout 3 – Image + Text (Split)",
            panel: "#announcementLayoutSplit",
        },
        layout_4: {
            label: "Layout 4 – Content Only",
            panel: "#announcementLayoutContent",
        },
        layout_5: {
            label: "Layout 5 – Image Slider + Content",
            panel: "#announcementLayoutGallery",
        },
    };

    /* ===============================
FORM AUTO-POPULATE (GENERIC)
=============================== */

    function populateAnnouncementForm(form, data) {
        for (const key in data) {
            const input = form.find(`[name="${key}"]`);
            if (!input.length) {
                if (Array.isArray(data[key])) {
                    data[key].forEach((val) => {
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

            if (tag === "select") {
                input
                    .val(
                        String(
                            value === true ? 1 : value === false ? 0 : value,
                        ),
                    )
                    .trigger("change");
            } else if (type === "checkbox") {
                input.prop("checked", !!value);
            } else if (type === "radio") {
                input.filter(`[value="${value}"]`).prop("checked", true);
            } else if (type === "datetime-local" && value) {
                input.val(value.replace(" ", "T").slice(0, 16));
            } else if (type !== "file") {
                input.val(value ?? "");
            }
        }

        // IMAGE PREVIEW (EDIT)
        form.find(".preview-img").each(function () {
            const img = $(this);
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

        // Populate media rows if any
        if (Array.isArray(data.media)) {
            const container = $("#announcementMediaContainer");
            container.empty();
            window.announcementMediaHelper.rowIndex = 0;

            data.media.forEach((media) => {
                const html = window.announcementMediaHelper.makeRow(
                    window.announcementMediaHelper.rowIndex,
                    media,
                );
                container.append(html);
                window.announcementMediaHelper.rowIndex++;
            });

            window.announcementMediaHelper.refreshBadges();
        }

        // Layout previews
        if (data.hero_image)
            $("#announcementHeroPreview").attr(
                "src",
                `/storage/${data.hero_image}`,
            );
        if (data.hero_caption)
            form.find('textarea[name="hero_caption"]').val(data.hero_caption);
        if (data.split_left_image)
            $("#announcementSplitLeftPreview").attr(
                "src",
                `/storage/${data.split_left_image}`,
            );
        if (data.split_right_caption)
            form.find('textarea[name="split_right_caption"]').val(
                data.split_right_caption,
            );
    }

    function setAnnouncementStep(step) {
        const isStep1 = step === 1;
        $("#announcementStep1").toggle(isStep1);
        $("#announcementStep2").toggle(!isStep1);

        $("#announcementNextBtn").toggle(isStep1);
        $("#announcementSaveBtn").toggle(!isStep1);
        $("#announcementBackBtn").toggle(!isStep1);

        $("#announcementStepBadge").text(
            isStep1 ? "Step 1 of 2" : "Step 2 of 2",
        );
        $("#announcementStepHint").text(
            isStep1
                ? "Fill details then choose a layout"
                : "Fill layout content then save",
        );
    }

function applyAnnouncementLayout(layoutKey) {
    const config = ANNOUNCEMENT_LAYOUTS[layoutKey];
    if (!config) return;

    $("#announcementLayout").val(layoutKey);
    $("#announcementSelectedLayoutLabel").text(config.label);

    // hide all panels
    $("#announcementModal .announcement-layout-panel").hide();

    // show selected
    if (config.panel) {
        $(config.panel).show();
    }

    // active card
    $("#announcementModal .announcement-layout-card").removeClass("active");
    $(
        `#announcementModal .announcement-layout-card[data-layout="${layoutKey}"]`,
    ).addClass("active");
}


    // ===============================
    // LAYOUT 5 MEDIA SYSTEM
    // ===============================

    window.announcementLayout5Media = [];

    function renderAnnouncementLayout5() {
        const grid = $("#announcementMediaGrid");
        grid.find(".image-card").remove();

        window.announcementLayout5Media.forEach((m, i) => {
            const card = $(`
            <div class="media-card image-card" data-key="${i}">
                <span class="remove-btn">&times;</span>
                <img src="${m.url}">
            </div>
        `);
            $("#announcementAddMediaCard").before(card);
        });

        updateAnnouncementLayout5Inputs();
    }

    function updateAnnouncementLayout5Inputs() {
        const form = $("#announcementForm");
        form.find('input[name="existing_media_ids[]"]').remove();
        form.find('input[name^="media["]').remove();

        window.announcementLayout5Media.forEach((m, index) => {
            if (m.type === "existing")
                form.append(
                    `<input type="hidden" name="existing_media_ids[]" value="${m.id}">`,
                );
            if (m.type === "new") {
                const input = $(
                    `<input type="file" name="media[${index}][image]" hidden>`,
                );
                input[0].files = createFileList(m.file);
                form.append(input);
            }

            form.append(`
            <input type="hidden" name="media[${index}][caption]" value="${m.caption ?? ""}">
            <input type="hidden" name="media[${index}][sort_order]" value="${index}">
            <input type="hidden" name="media[${index}][is_thumbnail]" value="${index === 0 ? 1 : 0}">
        `);
        });
    }

    $("#announcementMediaInput").on("change", function (e) {
        [...e.target.files].forEach((file) => {
            window.announcementLayout5Media.push({
                type: "new",
                file,
                url: URL.createObjectURL(file),
                caption: "",
            });
        });
        renderAnnouncementLayout5();
        this.value = "";
    });

    $(document).on("click", "#announcementMediaGrid .remove-btn", function () {
        const key = $(this).closest(".image-card").data("key");
        window.announcementLayout5Media.splice(key, 1);
        renderAnnouncementLayout5();
    });

    $("#announcementMediaGrid").sortable({
        items: ".image-card",
        cancel: "#announcementAddMediaCard",
        tolerance: "pointer",
        update() {
            const reordered = [];
            $("#announcementMediaGrid .image-card").each(function () {
                const key = $(this).data("key");
                reordered.push(window.announcementLayout5Media[key]);
            });
            window.announcementLayout5Media = reordered;
            renderAnnouncementLayout5();
        },
    });

    function loadExistingAnnouncementLayout5(media) {
        window.announcementLayout5Media = media
            .sort((a, b) => a.sort_order - b.sort_order)
            .map((m) => ({
                type: "existing",
                id: m.id,
                url: `/storage/${m.image_path}`,
                caption: m.caption,
            }));
        renderAnnouncementLayout5();
    }

    function resetAnnouncementLayout5Media() {
        window.announcementLayout5Media = [];
        $("#announcementMediaGrid .image-card").remove();
        const form = $("#announcementForm");
        form.find('input[name="existing_media_ids[]"]').remove();
        form.find('input[name^="media["]').remove();
    }

    // Click layout card
    $(document).on(
        "click",
        "#announcementModal .announcement-layout-card",
        function () {
            const layout = $(this).data("layout");
            applyAnnouncementLayout(layout);
        },
    );


    // Next / Back buttons
    $(document).on("click", "#announcementNextBtn", function () {
        let valid = true;
        $("#announcementStep1 :input[required]").each(function () {
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
        setAnnouncementStep(2);
    });

    $(document).on("click", "#announcementBackBtn", function () {
        setAnnouncementStep(1);
    });

    // Reset wizard on modal open
    $("#announcementModal").on("shown.bs.modal", function () {
        setAnnouncementStep(1);

        const layout = $("#announcementLayout").val() || "layout_1";
        applyAnnouncementLayout(layout);

        $(".announcement-layout-card").removeClass("active");
        $(`.announcement-layout-card[data-layout="${layout}"]`).addClass("active");
    });


    // File list helper
    function createFileList(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        return dt.files;
    }

    function lockAnnouncementLayoutSelection(activeLayout) {
        // Disable all cards
        $("#announcementModal .announcement-layout-card")
            .addClass("disabled")
            .css({
                pointerEvents: "none",
                opacity: 0.5,
            });

        // Enable active one
        $(
            `#announcementModal .announcement-layout-card[data-layout="${activeLayout}"]`,
        )
            .removeClass("disabled")
            .css({
                pointerEvents: "auto",
                opacity: 1,
            });

        // Hide all panels
        $("#announcementModal .announcement-layout-panel").hide();

        // Show only active panel
        const config = ANNOUNCEMENT_LAYOUTS[activeLayout];
        if (config?.panel) {
            $(config.panel).show();
        }

        $("#announcementStepHint").text(
            "Layout is locked for existing announcement",
        );
    }

    function unlockAnnouncementLayoutSelection() {
        $("#announcementModal .announcement-layout-card")
            .removeClass("disabled")
            .css({
                pointerEvents: "auto",
                opacity: 1,
            });

        $("#announcementStepHint").text("Fill details then choose a layout");
    }


    /* ===============================
    ANNOUNCEMENT MODULE END SCRIPT
    =============================== */










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
