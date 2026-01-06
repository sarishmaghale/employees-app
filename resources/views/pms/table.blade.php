@extends('layout')

@section('content')
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Card title</th>
                        <th>Label</th>
                        <th>Members</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody id="pmsTableBody">

                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('scripts')
@endpush
