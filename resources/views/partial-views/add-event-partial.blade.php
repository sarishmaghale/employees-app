 <div class="modal fade" id="addTaskModal" tabindex="-1">

     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

             <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                 <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">Add Events</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
             </div>

             <div class="modal-body" style="padding: 24px;">
                 <form id="addTaskForm">
                     <input type="hidden" name="employee_id" id="employee_id">
                     <!-- Event Type and Category -->
                     <div class="row mb-4">
                         <div class="col-md-6">
                             <label class="form-label"
                                 style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                                 Category:</label>
                             <select class="form-select" name="category_id" id="add_task_category"
                                 style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                 <option selected disabled>Select Event Type</option>
                                 @forelse(getTaskCategories() as $category)
                                     <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                 @empty
                                     <option value="">No categories found</option>
                                 @endforelse
                             </select>
                         </div>
                         @if (session('role') === 'admin')
                             <div class="col-md-6">
                                 <label class="form-label"
                                     style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Assign
                                     to:
                                 </label>
                                 <select class="form-select" name="employee_id" id="add_task_employee"
                                     style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                     <option value=""> Self</option>
                                     @forelse(getEmployees() as $employee)
                                         <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                                     @empty
                                         <option value="">No employees found</option>
                                     @endforelse
                                 </select>
                             </div>
                         @endif
                     </div>

                     <!-- Event Title -->
                     <div class="mb-4">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                             Title:</label>
                         <input type="text" class="form-control" name="title" id="add_task_title"
                             placeholder="Enter Title"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                     </div>

                     <!-- From and To Dates -->
                     <div class="row mb-4">
                         <div class="col-md-6">
                             <label class="form-label"
                                 style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">From:</label>
                             <input type="text" class="form-control datepicker" id="add_task_start" name="start"
                                 placeholder="Start Date"
                                 style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; ">
                         </div>
                         <div class="col-md-6">
                             <label class="form-label"
                                 style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">To:</label>
                             <input type="text" class="form-control datepicker" id="add_task_end" name="end"
                                 placeholder="End Date"
                                 style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                         </div>
                     </div>

                     <div class="subCategoryContainer">
                         @foreach (getTaskCategories() as $category)
                             <div class="subCategoryGroup" data-category="{{ $category->id }}" style="display:none;">
                                 @foreach (getTaskSubCategories($category->id) as $sub)
                                     <div class="form-check form-check-inline">
                                         <input class="form-check-input" type="radio" name="badge" id="task_badge"
                                             value="{{ $sub->sub_category_name }}"
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
                 <button type="button" class="btn btn-primary modal-submit-btn" id="saveTaskBtn"
                     style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">Add
                     Task</button>
                 <button class="btn" data-bs-dismiss="modal"
                     style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard</button>

             </div>

         </div>
     </div>
 </div>

 @push('scripts')
     <script>
         $(document).ready(function() {
             // Submit new task
             $(document).on('click', '#saveTaskBtn', function(e) {
                 e.preventDefault();
                 const btn = this;
                 showSpinner(btn);
                 const form = document.getElementById('addTaskForm');
                 const modal = $('#addTaskModal');
                 const formData = new FormData(form);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                 $.ajax({
                     url: '/tasks',
                     method: 'POST',
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                         if (response.success) {
                             Swal.fire('Success', response.message, 'success');
                             modal.modal('hide');
                             form.reset();
                             recentTasks();
                             document.dispatchEvent(new Event('calendar:refresh'));
                         } else {
                             Swal.fire('Error', response.message, 'error');
                         }
                         hideSpinner(btn);
                     },
                     error: function(xhr) {
                         hideSpinner(btn);
                         if (xhr.status === 422) handleValidationErrors(xhr,
                             '#addTaskForm');
                         else console.error(xhr.responseText);
                     }
                 });
             });

             // Reset form when modal is closed
             $('#addTaskModal').on('hidden.bs.modal', function() {
                 $('#addTaskForm')[0].reset();
                 $('.subCategoryGroup').hide();
                 $('input[name="badge"]').prop('checked', false);
                 $('#addTaskForm .is-invalid').removeClass('is-invalid');
                 $('#addTaskForm .invalid-feedback').remove();
             });

             $('#add_task_category').on('change', function() {
                 const categoryId = $(this).val();
                 $('.subCategoryGroup').hide();
                 $(`.subCategoryGroup[data-category="${categoryId}"]`).show();
             });

         });
     </script>
 @endpush
