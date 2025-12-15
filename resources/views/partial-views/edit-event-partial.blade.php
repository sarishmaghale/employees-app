  <div class="modal fade" id="editTaskModal" tabindex="-1">

      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

              <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                  <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">Add Events</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
              </div>

              <div class="modal-body" style="padding: 24px;">
                  <form id="editTaskForm">
                      <input type="hidden" name="id" id="edit_task_id">
                      <!-- Event Type and Category -->
                      <div class="row mb-4">
                          <div class="col-md-6">
                              <label class="form-label"
                                  style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                                  Category:</label>
                              <select class="form-select" name="category_id" id="edit_task_category"
                                  style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                  @forelse(getTaskCategories() as $category)
                                      <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                  @empty
                                      <option value="">No categories found</option>
                                  @endforelse
                              </select>
                          </div>
                      </div>

                      <!-- Event Title -->
                      <div class="mb-4">
                          <label class="form-label"
                              style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                              Title:</label>
                          <input type="text" class="form-control" name="title" id="edit_task_title"
                              placeholder="Enter Title"
                              style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                      </div>

                      <!-- From and To Dates -->
                      <div class="row mb-4">
                          <div class="col-md-6">
                              <label class="form-label"
                                  style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">From:</label>
                              <input type="text" class="form-control datepicker" id="edit_task_start" name="start"
                                  placeholder="Start Date"
                                  style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; ">
                          </div>
                          <div class="col-md-6">
                              <label class="form-label"
                                  style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">To:</label>
                              <input type="text" class="form-control datepicker" id="edit_task_end" name="end"
                                  placeholder="End Date"
                                  style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                          </div>
                      </div>

                      <!-- Event Description -->
                      <div class="mb-4">
                          <label class="form-label"
                              style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                              Description:</label>
                          <textarea class="form-control" rows="4" placeholder="Enter Description"
                              style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; resize: none;"></textarea>
                      </div>

                      <div class="subCategoryContainer">
                          @foreach (getTaskCategories() as $category)
                              <div class="subCategoryGroup" data-category="{{ $category->id }}" style="display:none;">
                                  @foreach (getTaskSubCategories($category->id) as $sub)
                                      <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="badge"
                                              id="edit_task_badge" value="{{ $sub->sub_category_name }}"
                                              style="width: 18px; height: 18px; border: 2px solid #dc3545; cursor: pointer;">
                                          <label class="form-check-label" _
                                              style="font-size: 14px; color: #666; margin-left: 6px; cursor: pointer;">
                                              {{ $sub->sub_category_name }}</label>
                                      </div>
                                  @endforeach
                              </div>
                          @endforeach
                      </div>
                  </form>
              </div>

              <div class="modal-footer"
                  style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                  <button type="submit" class="btn btn-primary" id="updateTaskBtn"
                      style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">Update
                      Task</button>
                  <button type="button" class="btn btn-danger" id="deleteTaskBtn"
                      style=" background: #dc3545; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(220,53,69,0.3);">
                      Delete
                  </button>
                  <button class="btn" data-bs-dismiss="modal"
                      style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard
                  </button>
              </div>

          </div>
      </div>
  </div>

  @push('scripts')
      <script>
          $(document).ready(function() {

              $('#edit_task_category').on('change', function() {
                  const categoryId = $(this).val();
                  $('.subCategoryGroup').hide();
                  $(`.subCategoryGroup[data-category="${categoryId}"]`).show();
                  $('input[name="badge"]').prop('checked', false);
              });

              $(document).on('click', '#updateTaskBtn', function(e) {
                  e.preventDefault();
                  const form = document.getElementById('editTaskForm');
                  const modal = $('#editTaskModal');
                  const formData = new FormData(form);
                  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                  const editEventId = $('#edit_task_id').val();
                  $.ajax({
                      url: `/task/update/${editEventId}`,
                      method: 'POST',
                      data: formData,
                      processData: false,
                      contentType: false,
                      success: function(response) {
                          if (response.success) {
                              Swal.fire('Success', response.message, 'success');
                              modal.modal('hide');
                              form.reset();
                              document.dispatchEvent(new Event('calendar:refresh'));

                          } else {
                              Swal.fire('Error', response.message, 'error');
                          }
                      },
                      error: function(xhr) {
                          if (xhr.status === 422) handleValidationErrors(xhr,
                              '#editTaskForm');
                          else console.error(xhr.responseText);
                      }
                  });
              });

              $(document).on('click', '#deleteTaskBtn', function(e) {
                  e.preventDefault();
                  const editEventId = $('#edit_task_id').val();
                  const modal = $('#editTaskModal');
                  if (!editEventId) return;
                  $.ajax({
                      url: `/task/${editEventId}`,
                      method: "DELETE",
                      success: function(response) {
                          if (response.success) {
                              Swal.fire('Deleted!', response.message, 'success');
                              $(modal).modal('hide');
                              document.dispatchEvent(new Event('calendar:refresh'));
                          }
                      },
                      error: function(xhr) {
                          console.error('Error:', xhr.responseText);
                      }
                  });

              })

              $('#editTaskModal').on('hidden.bs.modal', function() {
                  $('#editTaskForm')[0].reset();
                  $('.subCategoryGroup').hide();
                  $('input[name="badge"]').prop('checked', false);
                  $('#editTaskForm .is-invalid').removeClass('is-invalid');
                  $('#editTaskForm .invalid-feedback').remove();
              });

          });
      </script>
  @endpush
