@extends('layouts.app')

@include('hak-akses.create')
{{-- @include('data-pengguna.edit') --}}

@section('content')

<div class="section-header">
    <h1>Aktivitas User</h1>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="log_table" class="display">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                {{-- <th>Event</th> --}}
                                <th>Data</th>
                                <th>Deskripsi</th>
                                <th>Log At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $log->causer->name ?? 'System' }}</td>
                                {{-- <td>{{ ucfirst($log->event) }}</td> --}}
                                <td>
                                    @foreach ($log->properties['attributes'] ?? [] as $key => $value)
                                        @if (!in_array($key, ['created_at', 'updated_at']))  {{-- Filter agar tidak menampilkan created_at & updated_at --}}
                                            <strong>{{ $key }}</strong>: {{ $value }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ $log->description }}</td>
                                {{-- <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td> --}}
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            @endforeach                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Datatables Jquery -->
<script>
    $(document).ready(function(){
        $('#log_table').DataTable({
            paging: true
        });
    })
</script>



@endsection