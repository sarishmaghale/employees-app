 <div class="modal fade" id="pmsAddNewCardModal" tabindex="-1">

     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

             <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                 <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">
                     New Card
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
             </div>

             <div class="modal-body" style="padding: 24px;">
                 <form id="pmsAddNewCardForm">
                     <input type="hidden" name="board_id" id="pms_card_board_id" value="{{ $boardId }}">
                     <div class="mb-4">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">
                             Card Title:</label>
                         <input type="text" class="form-control" name="title" id="pms_card_title"
                             placeholder="Enter card title"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;"
                             required>
                     </div>

                 </form>
             </div>

             <div class="modal-footer"
                 style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                 <button type="button" class="btn btn-primary modal-submit-btn" id="pmsSaveNewCardBtn"
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
             $(document).on('click', '#pmsSaveNewCardBtn', function(e) {
                 e.preventDefault();
                 const btn = this;
                 showSpinner(btn);
                 const form = document.getElementById('pmsAddNewCardForm');
                 const formData = new FormData(form);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                 $.ajax({
                     url: `{{ route('pms-card.store') }}`,
                     type: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                         hideSpinner(btn);
                         if (response.success) {
                             const boardDiv = $('.kb-board');
                             const card = response.data;
                             const cardHtml = `
                <div class="kb-column" data-card-id="${card.id}">
                    <div class="kb-column-header">
                        <div class="kb-column-header-top">
                            <h2>${card.title}</h2>
                        </div>
                        <span class="task-count">0 Tasks</span>
                    </div>
                    <div class="kb-column-body">
                        <button class="kb-add-task-btn initiatePmsAddTaskBtn" data-card-id="${card.id}">
                            + Add Task
                        </button>
                    </div>
                </div>`;
                             boardDiv.append(cardHtml);
                             $('#pmsAddNewCardModal').modal('hide');
                             form[0].reset();

                         } else Swal.fire('Error', response.message, 'error');
                     },
                     error: function(xhr) {
                         hideSpinner(btn);
                         if (xhr.status === 422) handleValidationErrors(xhr,
                             '#pmsAddNewCardForm');
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
