 <div class="modal fade" id="otpModal" tabindex="-1">

     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

             <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                 <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">OTP sent to your email
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
             </div>

             <div class="modal-body" style="padding: 24px;">
                 <form id="verifyLoginForm">
                     <input type="hidden" name="email" id="loginEmai">
                     <div class="mb-4">
                         <label class="form-label"
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">
                             OTP:</label>
                         <input type="text" class="form-control" name="otp" id="login_otp"
                             placeholder="Enter OTP"
                             style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                     </div>
                 </form>
                 <div id="otpError" class="text-danger mb-3" style="display:none; font-size:13px;"></div>

             </div>

             <div class="modal-footer"
                 style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                 <button type="button" class="btn btn-primary modal-submit-btn" id="verifyOtp"
                     style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                     Verify</button>
                 <button class="btn" data-bs-dismiss="modal"
                     style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard</button>

             </div>

         </div>
     </div>
 </div>

 @push('scripts')
     <script>
         $(document).ready(function() {

             $(document).on('click', '#verifyOtp', function(e) {
                 e.preventDefault();
                 const btn = this;
                 showSpinner(btn)
                 const form = document.getElementById('verifyLoginForm');
                 const modal = $('#otpModal');
                 const formData = new FormData(form);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                 $.ajax({
                     url: `{{ route('login.verify') }}`,
                     method: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                         if (response.success) {
                             window.location.href = "{{ route('dashboard') }}"
                         } else {
                             console.log('error shown in partial')
                             hideSpinner(btn)
                             $('#otpError').text(response.message).show();
                         }
                     },
                     error: function(xhr) {
                         hideSpinner(btn)
                         console.error('Error:' + xhr.responseText);
                     }
                 });
             });

         });
     </script>
 @endpush
