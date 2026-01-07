@extends('layout')

@section('content')
    <div class="container py-4">

        <!-- My Workspace Section -->
        <div class="mb-5">
            <h2 class="h4 mb-3">My Workspace</h2>

            <div class="row g-3">
                @forelse($createdBoards as $board)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm overflow-hidden position-relative">

                            <a href="{{ route('pms-board.show', $board->id) }}" class="text-decoration-none d-block">
                                <div class="card-img-top"
                                    style="height: 120px; background-color:#6366f1; background-image: url('{{ $board->image ? asset('storage/' . $board->image) : '' }}');
                                        background-size: cover;
                                        background-position: center;">
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title text-dark mb-0">{{ $board->board_name }}</h5>
                                </div>
                            </a>

                            {{-- Three-dot dropdown --}}
                            <div class="position-absolute top-0 end-0 m-2">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="{{ route('pms-board.updateCover') }}" method="POST"
                                                enctype="multipart/form-data" id="coverForm{{ $board->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="board_id" value="{{ $board->id }}">
                                                <input type="file" name="cover_image" class="d-none" accept="image/*"
                                                    onchange="document.getElementById('coverForm{{ $board->id }}').submit()">
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="document.querySelector('#coverForm{{ $board->id }} input[type=file]').click()">
                                                    Edit Cover Image
                                                </a>
                                            </form>

                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                @endforelse

                <!-- Create New Board -->
                <div class="col-12 col-md-6 col-lg-4">
                    <button type="button" id="pmsAddBoardBtn" class="btn w-100 h-100 p-0 border-0">
                        <div class="card h-100 border-dashed" style="border: 2px dashed #dee2e6;">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <span class="text-muted">+ Create new board</span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

        </div>

        <!-- Associated Boards Section -->
        <div class="mb-5">
            <h2 class="h4 mb-3">Associated Boards</h2>

            <div class="row g-3">
                @forelse($associatedBoards  as $board)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('pms-board.show', $board->id) }}" class="text-decoration-none">
                            <div class="card h-100 shadow-sm overflow-hidden">
                                <div class="card-img-top"
                                    style="height: 120px; background-color:#10b981; 
                                        background-image: url('{{ $board->image ? asset('storage/' . $board->image) : '' }}');
                                        background-size: cover;
                                        background-position: center;">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-dark mb-0">{{ $board->board_name }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                @endforelse
            </div>
        </div>

    </div>

    @include('pms.partial-add-board')
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#pmsAddBoardBtn', function() {
            $("#pmsAddNewBoardModal").modal('show');
        });
    </script>
@endpush
