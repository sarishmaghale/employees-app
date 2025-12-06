 @extends('layout')
 @section('content')
     <!-- Stats Cards -->
     <div class="stats-grid">
         <div class="stat-card">
             <div class="stat-icon primary">
                 <i class="fas fa-users"></i>
             </div>
             <div class="stat-details">
                 <h3>2,543</h3>
                 <p>Total Users</p>
             </div>
         </div>

         <div class="stat-card">
             <div class="stat-icon success">
                 <i class="fas fa-shopping-cart"></i>
             </div>
             <div class="stat-details">
                 <h3>1,845</h3>
                 <p>Total Orders</p>
             </div>
         </div>

         <div class="stat-card">
             <div class="stat-icon warning">
                 <i class="fas fa-dollar-sign"></i>
             </div>
             <div class="stat-details">
                 <h3>$45,678</h3>
                 <p>Total Revenue</p>
             </div>
         </div>

         <div class="stat-card">
             <div class="stat-icon danger">
                 <i class="fas fa-box"></i>
             </div>
             <div class="stat-details">
                 <h3>892</h3>
                 <p>Total Products</p>
             </div>
         </div>
     </div>

     <!-- Recent Orders -->
     <div class="content-card">
         <div class="card-header">
             <h2 class="card-title">Recent Orders</h2>
             <button class="btn btn-primary">
                 <i class="fas fa-plus"></i>
                 Add New
             </button>
         </div>

         <div class="table-container">
             <table class="table">
                 <thead>
                     <tr>
                         <th>Order ID</th>
                         <th>Customer</th>
                         <th>Product</th>
                         <th>Amount</th>
                         <th>Status</th>
                         <th>Date</th>
                     </tr>
                 </thead>
                 <tbody>
                     <tr>
                         <td>#ORD-001</td>
                         <td>John Doe</td>
                         <td>Laptop Pro</td>
                         <td>$1,299</td>
                         <td><span
                                 style="padding: 5px 12px; border-radius: 20px; background: rgba(34, 197, 94, 0.1); color: var(--success); font-size: 0.8rem;">Completed</span>
                         </td>
                         <td>2024-12-05</td>
                     </tr>
                 </tbody>
             </table>
         </div>
     </div>
 @endsection
