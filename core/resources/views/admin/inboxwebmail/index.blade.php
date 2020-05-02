@extends('admin.master')
@section('inboxwebmail', 'active')
@section('title', 'Inbox Account Overview')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1>Inbox Account Overview</h1>
                Cron URL: {{asset('inboxwebmail/parse')}}
            </div>
            <a href="{{route('admin.inboxwebmail.add')}}"> <button type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add New</button></a>
        </div>
        <div class="tile">

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    @if(count($inboxwebmails) == 0)
                        <tr>
                            <td class="text-center">
                                <h2>No data found </h2>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Delete from server</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($inboxwebmails as $data)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>
                                @if($data->active == 1) <span class="badge badge-success">Active</span>
                                @elseif($data->active == 0) <span class="badge badge-warning">Block</span>
                                @else <span class="badge badge-info">block</span>
                                @endif

                            </td>
                            <td>
                                @if($data->d_from_server == 1) <span class="badge badge-success">Yes</span>
                                @elseif($data->d_from_server == 0) <span class="badge badge-warning">No</span>
                                @else <span class="badge badge-info">block</span>
                                @endif

                            </td>
                            
                            <td>
                                <a href="{{route('admin.inboxwebmail.edit',$data->id)}}">  <button type="button" class="btn btn-info"><i class="fa fa-edit"></i> Edit </button></a>
                             &nbsp;   <a href="{{route('admin.inboxwebmail.delete',$data->id)}}" onclick="return confirm('Are you sure you want to delete this account? Email of this account also will delete and no re-cover.');">  <button type="button" class="btn btn-danger"><i class="fa fa-remove"></i> Delete </button></a>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                
            </div>
            <div class="d-flex flex-row-reverse">
{{$inboxwebmails->links()}}
            </div>
        </div>
    </main>
@endsection



