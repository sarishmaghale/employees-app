import './bootstrap';
import flatpickr from "flatpickr";
import './script';
import './validation';


$(document).ready(function(){
 flatpickr(".datepicker", {
     dateFormat: "Y-m-d"
 });
});

$('#addTaskModal','#editTaskModal','#newEmployeeModal').on('shown.bs.modal',function(){
    flatpickr(".datepicker", { 
        dateFormat: "Y-m-d"
     });
})