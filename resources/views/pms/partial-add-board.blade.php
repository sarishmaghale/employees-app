 <div class="modal fade" id="pmsAddNewBoardModal" tabindex="-1">

     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

             <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                 <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">
                     New Board
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
             </div>

             <div class="modal-body" style="padding: 24px;">
                 <form id="pmsAddNewBoardForm">
                     <div class="mb-4">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">
                             Board Title:</label>
                         <input type="text" class="form-control" name="board_name" id="pms_board_title"
                             placeholder="Enter board title"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;"
                             required>
                     </div>

                 </form>
             </div>

             <div class="modal-footer"
                 style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                 <button type="button" class="btn btn-primary modal-submit-btn" id="pmsSaveNewBoardBtn"
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
             $(document).on('click', '#pmsSaveNewBoardBtn', function(e) {
                 e.preventDefault();

                 let boardValue = $('#pms_board_title').val().trim();
                 if (boardValue === "") {
                     $('#pms_board_title').addClass('is-invalid').focus();
                     return;
                 }

                 const btn = this;
                 showSpinner(btn);
                 const form = document.getElementById('pmsAddNewBoardForm');
                 const formData = new FormData(form);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                 $.ajax({
                     url: `{{ route('pms-board.store') }}`,
                     type: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                         hideSpinner(btn);
                         if (response.success) {

                             $('#pmsAddNewBoardModal').modal('hide');

                             Swal.fire({
                                 icon: 'success',
                                 title: 'Success',
                                 text: response.message,
                                 timer: 1500,
                                 showConfirmButton: false,
                                 willClose: () => {
                                     window.location.href = '/pms-workspace';
                                 }
                             });
                         } else Swal.fire('Error', response.message, 'error');
                     },
                     error: function(xhr) {
                         hideSpinner(btn);
                         if (xhr.status === 422) handleValidationErrors(xhr,
                             '#pmsAddNewBoardForm');
                         else {
                             Swal.fire('Error', 'Something went wrong', 'error');
                             console.error('Error:', xhr.responseText);
                         }
                     }
                 })
             });

             $(document).on('input', '#pms_board_title', function() {
                 $(this).removeClass('is-invalid');
             });

             $('#pmsAddNewBoardModal').on('hidden.bs.modal', function() {
                 $('#pms_board_title').removeClass('is-invalid').val('');
             });
         })
     </script>
 @endpush
