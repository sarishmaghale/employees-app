  <div class="modal fade" id="newEmployeeModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">
                      <i class="fas fa-plus-circle me-2"></i>Add New Employee
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                  <form action="{{ route('employees.store') }}" method="POST" id="newEmployeeForm">
                      @csrf
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="username">Username</label>
                                  <input type="text" class="form-control" id="username" name="username">
                              </div>
                              <div class="form-group">
                                  <label for="email">Email</label>
                                  <input type="text" class="form-control" id="email" name="email">
                              </div>
                              <div class="form-group">
                                  <label for="password">Password</label>
                                  <input type="text" class="form-control" id="password" name="password">
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-row">
                                  <div class="form-group col-md 3">
                                      <label>DOB</label>
                                      <input type="text" class="form-control datepicker" id="dob"
                                          name="dob">
                                  </div>
                                  <div class="form-group col-md 3">
                                      <label>Address</label>
                                      <input type="text" class="form-control" placeholder="Address" id="address"
                                          name="address">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="role">Role</label>
                                  <select class="form-control" id="role" name="role">
                                      <option selected disabled>Select Role</option>
                                      <option value="admin">Admin</option>
                                      <option value="staff">Staff</option>
                                  </select>
                              </div>

                          </div>
                          <div class="form-group ">
                              <label>Contact info</label>
                              <input type="text" class="form-control" placeholder="contact" id="phone"
                                  name="phone">
                          </div>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      <i class="fas fa-times me-1"></i>Cancel
                  </button>
                  <button type="button" class="btn btn-primary modal-submit-btn" id="addNewEmployeeBtn">
                      <i class="fas fa-save me-1"></i>Save Info
                  </button>
              </div>
          </div>
      </div>
  </div>

  @push('scripts')
      <script>
          $(document).ready(function() {
              $(document).on("click", "#addNewEmployeeBtn", function(e) {
                  e.preventDefault();
                  const btn = this;
                  showSpinner(btn);
                  const form = document.getElementById('newEmployeeForm');
                  const formData = new FormData(form);
                  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                  $.ajax({
                      url: '/employees',
                      type: "POST",
                      data: formData,
                      processData: false,
                      contentType: false,
                      success: function(response) {
                          $('#newEmployeeModal').modal('hide');
                          Swal.fire({
                              title: response.success ? 'Success' : 'Error',
                              text: response.message,
                              icon: response.success ? 'success' : 'error',
                          });
                          if (response.success) {

                              document.dispatchEvent(new Event('employees:refresh'));
                          }
                          hideSpinner(btn)
                      },
                      error: function(xhr) {
                          hideSpinner(btn)
                          if (xhr.status === 422) handleValidationErrors(xhr, '#newEmployeeForm');
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
