@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Sections')

@section('content_header')
    <h1>Rule Sections</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Section -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#sectionModal"
            data-form="#sectionForm"
            data-title="Add Section"
            data-url="/admin/rule_sections">
            <i class="fas fa-plus mr-1"></i> Add Section
        </button>

    </div>

    <div class="card-body">
        <table id="sectionsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Number</th>
                    <th>Body</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                <tr>
                    <td>{{ $section->article->number ?? '-' }}</td>
                    <td>{{ $section->number }}</td>
                    <td>{{ Str::limit($section->body, 80) }}</td>
                    <td>{{ $section->sort_order }}</td>
                    <td>

                        <button class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-modal="#sectionModal"
                            data-form="#sectionForm"
                            data-title="Edit Section"
                            data-url="/admin/rule_sections"
                            data-id="{{ $section->id }}">
                            Edit
                        </button>

                        <form action="{{ route('admin.rule_sections.destroy', $section) }}"
                              method="POST"
                              class="d-inline ajax-delete-section">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Section Modal --}}
@include('admin.rules_and_regulations.sections.partials.section-modal')
@stop

@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#sectionsTable')) $('#sectionsTable').DataTable().destroy();
    const table = $('#sectionsTable').DataTable({ responsive: true, autoWidth: false, pageLength: 10, columnDefs: [{ orderable: false, targets: 4 }] });

    $(document).on("submit", ".ajax-delete-section", function(e) {
        e.preventDefault();
        const form = $(this), url = form.attr("action"), row = form.closest("tr");
        Swal.fire({ title: "Delete this section?", text: "This action cannot be undone.", type: "warning", showCancelButton: true, confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel", confirmButtonColor: "#dc3545", reverseButtons: true })
            .then(result => { if (!result.value) return; $.ajax({ url, type: "POST", data: { _token: $('meta[name="csrf-token"]').attr("content"), _method: "DELETE" }, success: res => { Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false }); table.row(row).remove().draw(false); }, error: xhr => { Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" }); } }); });
    });
});
</script>
@endpush
