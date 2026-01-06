  function showSpinner(btn) {
                if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
                btn.disabled = true;

            }

            function hideSpinner(target) {
                const $btn = $(target);
                $btn.prop('disabled', false);
         
            }

$(document).ready(function(){

});