@extends('layout')
@push('styles')
    <style>
        .dropdown-menu-scroll {
            max-height: 150px;
            /* adjust as needed */
            overflow-y: auto;
        }
    </style>
@endpush
@section('content')
    <div class="kb-filters-wrapper">
        <div class="kb-filters-wrapper-inner">

            <!-- Left Side - Board Title + Members -->
            <div class="kb-filters d-flex align-items-center gap-3">
                <span class="fw-bold">{{ $board->board_name }}</span>

                <!-- Members List (Usernames) -->
                <div class="dropdown">
                    <button class="dropdown-toggle border-0 bg-transparent" data-bs-toggle="dropdown" style="font-size: 20px;">
                        <i class="fas fa-users"></i>
                    </button>

                    <ul id="boardMembersDropdown" class="dropdown-menu dropdown-menu-scroll" aria-labelledby="membersBtn">
                        @foreach ($board->members as $member)
                            @php
                                $profile = $member->detail?->profile_image ?? null;
                                $username = $member->username ?? 'U';
                            @endphp

                            <li>
                                <a href="#" class="dropdown-item d-flex align-items-center gap-2">
                                    @if ($profile)
                                        <img src="{{ asset('storage/' . $profile) }}" alt="{{ $username }}"
                                            class="rounded-circle" style="width:32px; height:32px; object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-warning text-center"
                                            style="width:32px; height:32px; line-height:32px; font-weight:600; color:#fff;">
                                            {{ strtoupper(substr($username, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $username }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <a href="{{ route('pms.table', ['id' => $board->id]) }}" class="btn btn-sm btn-outline-primary ms-2">
                    Table View
                </a>

            </div>

            <!-- Right Side - Action Buttons -->
            <div class="kb-add-card-top d-flex align-items-center gap-2">

                <!-- Add Member Dropdown -->
                <div class="dropdown">
                    <button class="kb-add-card-global dropdown-toggle" id="addMemberBtn" data-bs-toggle="dropdown">
                        + Add Member
                    </button>
                    <ul class="dropdown-menu dropdown-menu-scroll" aria-labelledby="addMemberBtn">
                        @foreach ($employees as $employee)
                            @php
                                $profile = $employee->detail?->profile_image ?? null;
                                $username = $employee->username ?? 'U';
                            @endphp

                            <li>
                                <a class="dropdown-item add-board-member d-flex align-items-center gap-2"
                                    data-id="{{ $employee->id }}" href="#">

                                    @if ($profile)
                                        <img src="{{ asset('storage/' . $profile) }}" alt="{{ $username }}"
                                            class="rounded-circle" style="width:32px; height:32px; object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-warning text-center"
                                            style="width:32px; height:32px; line-height:32px; font-weight:600; color:#fff;">
                                            {{ strtoupper(substr($username, 0, 1)) }}
                                        </div>
                                    @endif

                                    <span>{{ $username }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Add Card -->
                <button class="kb-add-card-global" id="pmsAddCardBtn">+ Add Card</button>

            </div>

        </div>
    </div>

    <div class="kb-board">
        @forelse($board->cards as $card)
            <div class="kb-column" data-card-id="{{ $card->id }}">

                <!-- Column Header -->
                <div class="kb-column-header">

                    <!-- Top row: title + 3-dot menu -->
                    <div class="kb-column-header-top">
                        <h2>{{ $card->title }}</h2>

                        <!-- Three-dot menu (only in header) -->
                        <div class="dropdown">
                            <button class="btn btn-link p-0 dropdown-toggle" type="button"
                                id="cardMenu{{ $card->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cardMenu{{ $card->id }}">
                                <li><a class="dropdown-item deleteCardBtn" href="#"
                                        data-card-id="{{ $card->id }}" data-board-id="{{ $board->id }}">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Task count below title -->
                    <span class="task-count">{{ $card->tasks->count() }} Tasks</span>
                </div>

                <!-- Column Body -->
                <div class="kb-column-body" data-card-id="{{ $card->id }}">
                    @foreach ($card->tasks as $task)
                        <div class="kb-card pms-task-item" data-task-id="{{ $task->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <!-- Task title -->
                                <h3>{{ $task->title }}</h3>

                                <!-- Assigned members stack -->
                                <div class="d-flex align-items-center member-stack">
                                    @foreach ($task->assignedEmployees as $employee)
                                        @php
                                            $profile = $employee->detail->profile_image ?? null;
                                            $username = $employee->username ?? 'U';
                                        @endphp
                                        <div class="member-avatar" title="{{ $username }}" style="margin-left:-6px;">
                                            @if ($profile)
                                                <img src="{{ asset('storage/' . $profile) }}" alt="{{ $username }}">
                                            @else
                                                <span>{{ strtoupper($username[0]) }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="kb-card-meta">
                                @forelse($task->labels as $label)
                                    <span class="kb-tag"
                                        style="background: {{ $label->color }}">{{ $label->title }}</span>
                                @empty
                                @endforelse
                                @if ($task->total_items > 0)
                                    <span class="kb-checklist-progress"
                                        style="font-size:0.75em; color:#555; margin-left:6px;">
                                        <i class="fas fa-regular fa-square-check"></i>
                                        {{ $task->completed_items }}/{{ $task->total_items }}
                                    </span>
                                @endif

                                <span class="kb-date">Due: {{ $task->end_date ?? 'N/A' }}</span>

                            </div>
                        </div>
                    @endforeach

                    <button class="kb-add-task-btn initiatePmsAddTaskBtn" data-card-id="{{ $card->id }}">
                        + Add Task
                    </button>
                </div>

            </div>
            @empty
            @endforelse
        </div>


        @include('pms.partial-edit-task', ['boardMembers' => $board->members]) @include('pms.partial-add-card', ['boardId' => $board->id]) <div id="inline-task-template" style="display:none;">
            <div class="inline-task-form" style="margin-top:6px;">
                <input type="text" name="title" class="form-control inline-task-input" style="margin-bottom:6px;"
                    placeholder="Task title" />
                <button type="button" class="btn btn-primary btn-save-task">Save</button>
                <button type="button" class="btn btn-secondary btn-cancel-task">Cancel</button>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ asset('js/pms/pms-board.js') }}"></script>
        <script>
            initializePmsBoard({{ $board->id }});
        </script>
    @endpush
