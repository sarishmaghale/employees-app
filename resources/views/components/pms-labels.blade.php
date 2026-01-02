@extends('layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Manage Labels</h2>

        <!-- Form to add new label -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="labelForm">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label for="labelTitle" class="form-label">Label Title</label>
                            <input type="text" id="labelTitle" name="title" class="form-control"
                                placeholder="Enter label name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="labelColor" class="form-label">Label Color</label>
                            <input type="color" id="labelColor" name="color" class="form-control form-control-color"
                                value="#ff0000" title="Choose label color">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Add Label</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Existing labels list -->
        <div class="card">
            <div class="card-body">
                <h5>Existing Labels</h5>
                <ul class="list-group mt-3" id="labelsList">
                    @foreach (getLabels() as $label)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            data-id="{{ $label->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle me-2"
                                    style="width:20px; height:20px; background: {{ $label->color }};"></span>
                                <span class="label-title">{{ $label->title }}</span>
                            </div>
                            <div class="label-actions">
                                <button class="btn btn-sm btn-outline-secondary editLabelBtn"
                                    data-id="{{ $label->id }}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger deleteLabelBtn"
                                    data-id="{{ $label->id }}">Delete</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Add new label
        $('#labelForm').on('submit', function(e) {
            e.preventDefault();
            const title = $('#labelTitle').val().trim();
            const color = $('#labelColor').val();

            if (!title || !color) return;

            $.post('/components-labels', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                title: title,
                color: color
            }, function(response) {
                if (response.success) {
                    hideSpinner('labelForm');
                    // Append new label to list
                    $('#labelsList').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${response.data.id}">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle me-2" style="width:20px; height:20px; background: ${response.data.color};"></span>
                        <span class="label-title">${response.data.title}</span>
                    </div>
                    <div class="label-actions">
                        <button class="btn btn-sm btn-outline-secondary editLabelBtn" data-id="${response.data.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-danger deleteLabelBtn" data-id="${response.data.id}">Delete</button>
                    </div>
                </li>
            `);
                    $('#labelForm')[0].reset();
                } else {
                    hideSpinner('labelForm');
                    Swal.fire('Error', response.message, 'error');
                }
            });
        });

        // Delete label
        $(document).on('click', '.deleteLabelBtn', function() {
            const li = $(this).closest('li');
            const id = li.data('id');
            const csrf = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will delete the label!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/components-label/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                li.remove();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Edit label inline
        $(document).on('click', '.editLabelBtn', function() {
            const li = $(this).closest('li');
            const id = li.data('id');
            const titleSpan = li.find('.label-title');
            const colorSpan = li.find('span.rounded-circle');
            const currentTitle = titleSpan.text();
            const currentColor = rgbToHex(colorSpan.css('background-color'));

            // Replace content with input + color picker
            li.find('div.d-flex.align-items-center').html(`
            <input type="text" class="form-control form-control-sm me-2 edit-title-input" value="${currentTitle}" style="width:120px;">
            <input type="color" class="edit-color-input form-control form-control-color p-0" value="${currentColor}">
            `);

            $(this).removeClass('editLabelBtn btn-outline-secondary').addClass('saveLabelBtn btn-success').text(
                'Save');
        });

        // Save edited label
        $(document).on('click', '.saveLabelBtn', function() {
            const li = $(this).closest('li');
            const id = li.data('id');
            const newTitle = li.find('.edit-title-input').val().trim();
            const newColor = li.find('.edit-color-input').val();
            const csrf = $('meta[name="csrf-token"]').attr('content');
            const btn = $(this);

            if (!newTitle || !newColor) return;

            $.ajax({
                url: `/components-label/${id}`,
                method: 'POST',
                data: {
                    _token: csrf,
                    title: newTitle,
                    color: newColor
                },
                success: function(response) {
                    if (response.success) {
                        // Restore normal view
                        li.find('div.d-flex.align-items-center').html(`
                        <span class="rounded-circle me-2" style="width:20px; height:20px; background: ${newColor};"></span>
                        <span class="label-title">${newTitle}</span>
                    `);
                        btn.removeClass('saveLabelBtn btn-success').addClass(
                            'editLabelBtn btn-outline-secondary').text('Edit');
                        Swal.fire('Updated!', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });


        function rgbToHex(rgb) {
            const result = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/.exec(rgb);
            return result ? "#" + ((1 << 24) + (parseInt(result[1]) << 16) + (parseInt(result[2]) << 8) + parseInt(result[
                3])).toString(16).slice(1) : rgb;
        }
    </script>
@endpush
