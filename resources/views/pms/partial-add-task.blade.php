  <div class="modal fade" id="pmsAddTaskModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">
                      <i class="fas fa-plus-circle me-2"></i>Add New Task
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                  <form action="" method="POST" id="pmsNewTaskForm">
                      @csrf
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label for="title"
                                      style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Title</label>
                                  <input type="text" class="form-control"
                                      style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;"
                                      id="pms_add_title" name="title">
                              </div>

                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-10">
                              <div class="form-group">
                                  <label for="description"
                                      style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Description</label>
                                  <textarea class="form-control" id="pms_add_description" name="description">
                                  </textarea>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <label for="start_date"
                                  style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Start
                                  Date</label>
                              <input type="text" class="form-control datepicker" id="pms_add_start_date"
                                  style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; "
                                  placeholder="Start Date" name="start_date">
                          </div>
                          <div class="col-md-6">
                              <label for="end_date"
                                  style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">End
                                  Date</label>
                              <input type="text" class="form-control datepicker" id="pms_add_end_date"
                                  style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; "
                                  placeholder="End Date" name="end_date">

                          </div>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      <i class="fas fa-times me-1"></i>Cancel
                  </button>
                  <button type="button" class="btn btn-primary modal-submit-btn" id="pmsAddNewTaskBtn">
                      <i class="fas fa-save me-1"></i>Save
                  </button>
              </div>
          </div>
      </div>
  </div>

  @push('scripts')
      <script>
          $(document).ready(function() {
              $(document).on("click", "#pmsAddNewTaskBtn", function(e) {
                  e.preventDefault();
                  const btn = this;
                  showSpinner(btn);
                  const form = document.getElementById('pmsNewTaskForm');
                  const formData = new FormData(form);
                  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                  $.ajax({
                      url: '',
                      type: "POST",
                      data: formData,
                      processData: false,
                      contentType: false,
                      success: function(response) {
                          $('#pmsAddTaskModal').modal('hide');
                          Swal.fire({
                              title: response.success ? 'Success' : 'Error',
                              text: response.message,
                              icon: response.success ? 'success' : 'error',
                          });
                          hideSpinner(btn)
                      },
                      error: function(xhr) {
                          hideSpinner(btn)
                          if (xhr.status === 422) handleValidationErrors(xhr, '#pmsNewTaskForm');
                          else {
                              alert('Something went wrong');
                              console.error('Error:' + xhr.responseText);
                          }
                      }
                  });
              });
          })
      </script>
  @endpush
