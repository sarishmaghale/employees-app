  <!-- Category Filters -->
  <div class="kb-filters-wrapper">

      <div class="kb-filters">

          @foreach (getTaskCategories() as $category)
              <button class="kb-filter-btn" data-category-id="{{ $category->id }}"
                  data-category-name="{{ $category->category_name }}">
                  <span class="kb-filter-dot" style="background: {{ $category->color }}"></span>
                  {{ $category->category_name }}
              </button>
          @endforeach
      </div>
      @if (Auth::user()->role === 'admin')
          <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px;">
              <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Emlployee</label>
              <select id="employeeSelect"
                  style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 100%; outline: none; background: white; cursor: pointer;">
                  <option value="" selected>All Employees</option>
                  @forelse(getEmployees() as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                  @empty
                      <option value="">No employees found</option>
                  @endforelse
              </select>
          </div>
      @endif
      <div class="kb-add-card-top">
          <button class="kb-add-card-global">+ Add Card</button>
      </div>
  </div>

  <!-- Kanban Board -->
