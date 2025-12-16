 @extends('layout')
 @section('content')
     <div class="stats-grid">

         @if (Auth::user()->role === 'admin')
             <div class="stat-card">
                 <div class="stat-icon primary">
                     <i class="fas fa-users"></i>
                 </div>
                 <div class="stat-details">
                     <h3>{{ getEmployeesCount() }}</h3>
                     <p>Total Users</p>
                 </div>
             </div>
         @endif
         <div class="stat-card">
             <div class="stat-icon primary">
                 <i class="fas fa-tasks"></i>
             </div>
             <div class="stat-details">
                 <h3>{{ countTasksStartingToday() }} tasks</h3>
                 <p>Starting today</p>
             </div>
         </div>
         <div class="stat-card">
             <div class="stat-icon primary">
                 <i class="fas fa-hourglass-end"></i>
             </div>
             <div class="stat-details">
                 <h3>{{ countTasksEndingToday() }} tasks</h3>
                 <p>Deadline today</p>
             </div>
         </div>

     </div>

     <!-- Recent Tasks -->
     <div class="content-card">
         <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
             <h2 class="card-title" style="margin: 0;">Recently added Tasks</h2>
             <div style="display: flex; gap: 8px;">
                 <a href="{{ route('tasks.all') }}" class="btn btn-primary">
                     <i class="fas fa-tasks"></i>
                     View All
                 </a>
                 <button class="btn btn-primary" id="newTaskModal">
                     <i class="fas fa-plus"></i>
                     Add New
                 </button>
             </div>
         </div>
         @include('partial-views.all-tasks-partial')
         @include('partial-views.add-event-partial')
     </div>
 @endsection
 @push('scripts')
     <script>
         $(document).ready(function() {
             recentTasks();
             $(document).on('click', '#newTaskModal', function() {
                 $('#addTaskModal').modal('show');
             })
         })
     </script>
 @endpush
