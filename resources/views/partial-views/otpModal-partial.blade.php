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
                             style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 12px; display: block;">
                             Enter 6-digit OTP:
                         </label>

                         <div class="d-flex justify-content-between" id="otp-inputs">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 autocomplete="one-time-code"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                             <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric"
                                 style="width: 45px; height: 50px; text-align: center; font-size: 20px; font-weight: 600; border: 1px solid #e0e0e0; border-radius: 8px;">
                         </div>

                         <input type="hidden" name="otp" id="login_otp">
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

             const $inputs = $('.otp-input');

             function updateHiddenOtp() {
                 let otp = '';
                 $inputs.each(function() {
                     otp += $(this).val();
                 });
                 $('#login_otp').val(otp);
             }

             // Auto move forward
             $inputs.on('input', function() {
                 const $this = $(this);
                 const value = $this.val().replace(/\D/g, '');
                 $this.val(value);
                 if (value && $this.next('.otp-input').length) {
                     $this.next('.otp-input').focus();
                 }
                 updateHiddenOtp();
             });

             // Backspace → move back
             $inputs.on('keydown', function(e) {
                 if (e.key === 'Backspace' && !$(this).val()) {
                     $(this).prev('.otp-input').focus();
                 }
             });

             // Paste full OTP
             $inputs.on('paste', function(e) {
                 e.preventDefault();
                 const pasteData = e.originalEvent.clipboardData.getData('text').replace(/\D/g,
                     '');
                 $inputs.each(function(i) {
                     $(this).val(pasteData[i] || '');
                 });
                 updateHiddenOtp();
                 $inputs.eq(Math.min(pasteData.length, $inputs.length - 1)).focus();
             });

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
