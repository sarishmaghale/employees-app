@extends('layout')

@section('content')
    <div class="container py-4">

        <!-- My Workspace Section -->
        <div class="mb-5">
            <h2 class="h4 mb-3">My Workspace</h2>

            <div class="row g-3">
                @forelse($createdBoards as $board)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('pms-board.show', $board->id) }}" class="text-decoration-none">
                            <div class="card h-100 shadow-sm overflow-hidden">
                                <div class="card-img-top" style="height: 120px; background-color:#6366f1;"></div>
                                <div class="card-body">
                                    <h5 class="card-title text-dark mb-0">{{ $board->board_name }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    No boards
                @endforelse

                <!-- Create New Board -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="" class="btn w-100 h-100 p-0 border-0">
                        <div class="card h-100 border-dashed" style="border: 2px dashed #dee2e6;">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <span class="text-muted">+ Create new board</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Associated Boards Section -->
        <div class="mb-5">
            <h2 class="h4 mb-3">Associated Boards</h2>

            <div class="row g-3">
                @forelse($associatedBoards  as $board)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="" class="text-decoration-none">
                            <div class="card h-100 shadow-sm overflow-hidden">
                                <div class="card-img-top position-relative"
                                    style="height: 120px; background-color: #10b981;">
                                    <span
                                        class="badge bg-dark position-absolute top-0 end-0 m-2 small">{{ $board->owner_name ?? 'Owner' }}</span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-dark mb-0">{{ $board->board_name }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    No board
                @endforelse
            </div>
        </div>

    </div>

    <script>
        function createBoard() {

        }
    </script>
@endsection
