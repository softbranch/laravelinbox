
@extends('admin.master')
@section('inboxwebmail', 'active')
@section('title', 'Add new account')
@section('content')

    <main class="app-content">
        <div class="app-title">
            <div>
                <h1>Inbox Account Add</h1>
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
                            <form action="{{route('admin.inboxwebmail.post')}}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Name</label> </h5>
                                        <input class="form-control form-control-lg" type="text"  name="name" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Email</label> </h5>
                                        <input class="form-control form-control-lg" type="email"  name="email" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5> <label class="col-form-label" for="Title">*Password</label> </h5>
                                        <input class="form-control form-control-lg" type="password"  name="password" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <h5>  <label for="exampleInputEmail1">Email Signature</label></h5>
                                        <textarea id="e_sign" class="form-control" type="text" rows="5" name="e_sign" ></textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h5> <label class="col-form-label" for="active">Active</label> </h5>
                                        <input  type="checkbox" name="active" data-toggle="toggle"  data-on="yes" data-off="no" data-onstyle="success"  data-offstyle="danger" data-width="100%" checked>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h5> <label class="col-form-label" for="d_from_server">Delete From Server</label> </h5>
                                        <input  type="checkbox" name="d_from_server" data-toggle="toggle"  data-on="yes" data-off="no" data-onstyle="success"  data-offstyle="danger" data-width="100%">
                                    </div>
                                </div>

                                <div class="tile-footer" style="text-align: center;">
                                    <button class="btn btn-primary" style="width: 50%!important;" type="submit">Submit</button>
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


@stop
