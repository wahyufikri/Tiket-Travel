@extends('homepage.layouts.main')

@section('content')
<div class="container">
    <h1>Midtrans Logs</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Event</th>
                <th>Payload</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->order_id }}</td>
                <td>{{ $log->event_type }}</td>
                <td><pre>{{ json_encode(json_decode($log->payload), JSON_PRETTY_PRINT) }}</pre></td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
