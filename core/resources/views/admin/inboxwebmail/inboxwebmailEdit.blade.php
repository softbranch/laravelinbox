
@extends('admin.master')
@section('inboxwebmail', 'active')
@section('title', 'edit account')
@section('content')


    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-edit"></i> Inbox Account Edit</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="row">
                        <div class="col-lg-3">
                              <b>Steps to Setup Inbox:</b> <br><br>
                              <b>1. </b>Enter Name of Sender<br>
                              <b>2. </b>Enter Email ID<br>
                              <b>3. </b>Enter Email Password<br>
                              <b>4. </b>Click Active<br>
                              <b>5. </b>Enter Signature<br>
                              <b>6. </b>Click Delete from server, <br>If you want to delete emails from server.<br>
                              <b>7. </b>Click Submit<br>
							  <b>8. </b>Please take Backup first.<br>
                              <b>Important :</b> Please read the Documentation for complete information.
                              </div>
                              
                        <div class="col-lg-9">
                            <form action="{{route('admin.inboxwebmail.update', $property->id)}}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
                                @csrf
                                <h4>Account Details</h4>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Name</label> </h5>
                                        <input class="form-control form-control-lg" type="text"  name="name" value="{{$property->name}}" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Email</label> </h5>
                                        <input class="form-control form-control-lg" type="email"  name="email" value="{{$property->email}}" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Password</label> </h5>
                                        <input class="form-control form-control-lg" type="password"  name="password" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5>  <label for="exampleInputEmail1">Email Signature</label></h5>
                                        <textarea id="e_sign" class="form-control" type="text" rows="5" name="e_sign" >{{$property->e_sign}}</textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h5> <label class="col-form-label" for="active">Active</label> </h5>
                                        <input  type="checkbox" name="active" @if($property->active == 1) checked @endif data-toggle="toggle"  data-on="yes" data-off="no" data-onstyle="success"  data-offstyle="danger" data-width="100%">
                                    </div>
                                     <div class="form-group col-md-3">
                                        <h5> <label class="col-form-label" for="d_from_server">Delete From Server</label> </h5>
                                        <input  type="checkbox" name="d_from_server" @if($property->d_from_server == 1) checked @endif data-toggle="toggle"  data-on="yes" data-off="no" data-onstyle="success"  data-offstyle="danger" data-width="100%">
                                    </div>
                                </div>

                                <div class="tile-footer"  style="text-align: center;">
                                    <button class="btn btn-primary" style="width: 50%!important;" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="{{route('admin.inboxwebmail.labels', $property->id)}}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
                                @csrf
                                <h4>Create Labels</h4>
                                <div class="row">
                                    <input type="hidden" name="inlbl_del_url" id="inlbl_del_url"
                                           value="{{route('admin.inboxwebmail.label.delete')}}">
                                    <div class="form-group col-md-6">
                                        <table id="inboxWebmail_table_lbl" width="100%">
                                            <thead>
                                            <tr class="text-center">
                                                <th>Label Name</th>
                                                <th>Color Code</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($allLabelSelect as $label)
                                            <tr class="add_row">
                                                <td><input type="text" class="form-control"
                                                           name="lbl_name[][{{$label->id}}]"
                                                           value="{{$label->lb_name}}" required="required"/></td>
                                                <td><input type="text" class="form-control jscolor "
                                                           name="lbl_code[][{{$label->id}}]"
                                                           value="{{$label->lb_code}}" required="required"/></td>
                                                <td class="text-center">
                                                    <button type="button" class="badge badge-danger delc" id="delete_lbl"
                                                            title="Delete label"
                                                            onclick="inboxWebmail_deleteLabel({{$label->id}});">
                                                        X
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="4" align="right">
                                                    <button class="btn btn-success btn-sm addc" type="button" id="add_lbl" title='Add more label'> Add more label</button>
                                                </td>
                                            </tr>

                                            </tfoot>
                                        </table>
                                    </div>

                                </div>

                                <div class="tile-footer" style="text-align: center;">
                                    <button class="btn btn-primary" style="width: 50%!important;" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
@section('script')
    <script type="text/javascript" src="{{asset('assets/admin/js/inboxWebmail_admin.js')}}"></script>

  <script>
      function inboxWebmail_deleteLabel(id) {
          "use strict";
          $.ajax({
              url: '{{route('admin.inboxwebmail.label.delete')}}',
              type: 'post',
              data: {
                  '_token': '{{csrf_token()}}',
                  'label_id' : id
              },
              success:function (res) {
                 // nothing do
              }
          });
      }
  </script>
@stop
