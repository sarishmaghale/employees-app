  function showSpinner(btn) {
                if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> `;
            }

            function hideSpinner(target) {
                const $btn = $(target);
                $btn.prop('disabled', false);
                if ($btn.data('originalText')) {
                    $btn.html($btn.data('originalText'));
                }
            }

$(document).ready(function(){

});