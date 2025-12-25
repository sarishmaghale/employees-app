 <div class="modal fade" id="addNewStatusCardModal" tabindex="-1">

     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

             <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                 <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">
                     New Board
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
             </div>

             <div class="modal-body" style="padding: 24px;">
                 <form id="addKanbanStatusForm">
                     <input type="hidden" name="employee_id" id="status_emp_id">
                     <div class="mb-2">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">To
                             Category:</label>
                         <select class="form-select" name="category_id" id="status_category_id"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                             <option selected disabled>Select Category</option>
                             @forelse(getTaskCategories() as $category)
                                 <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                             @empty
                                 <option value="">No categories found</option>
                             @endforelse
                         </select>
                     </div>
                     <div class="mb-4">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">
                             Board Title:</label>
                         <input type="text" class="form-control" name="name" id="status_name"
                             placeholder="Eg: In progress, Important"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;"
                             required>
                     </div>

                 </form>
             </div>

             <div class="modal-footer"
                 style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                 <button type="button" class="btn btn-primary modal-submit-btn" id="saveNewBoardStatus"
                     style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                     Save </button>
                 <button class="btn" data-bs-dismiss="modal"
                     style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard</button>

             </div>

         </div>
     </div>
 </div>

 @push('scripts')
     <script>
         $(document).ready(function() {
             $(document).on('click', '#saveNewBoardStatus', function(e) {
                 e.preventDefault();
                 const btn = this;
                 showSpinner(btn);
                 const form = document.getElementById('addKanbanStatusForm');
                 const formData = new FormData(form);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                 $.ajax({
                     url: `{{ route('board.new') }}`,
                     type: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                         hideSpinner(btn);
                         if (response.success) {
                             $('#addNewStatusCardModal').modal('hide');
                             Swal.fire('Success', response.message, 'success');
                         } else Swal.fire('Error', response.message, 'error');
                     },
                     error: function(xhr) {
                         hideSpinner(btn);
                         if (xhr.status === 422) handleValidationErrors(xhr,
                             '#addKanbanStatusForm');
                         else {
                             Swal.fire('Error', 'Something went wrong', 'error');
                             console.error('Error:', xhr.responseText);
                         }
                     }
                 })
             })
         })
     </script>
 @endpush
