@extends('admin.master')
@section('inboxwebmail', 'active')
@section('title', 'Webmail Inbox overview')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/inboxWebmail_admin_style.css')}}">
@stop
@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1>Webmail Inbox overview</h1>
            </div>
            <button type="button" class="btn btn-info"><i class="fa fa-user"></i> {{$inboxwebmailAccount->email}}
            </button>
        </div>
        <div class="tile">
            <section class="content-n">
                <div class="body_scroll">
                    <div class="container-fluid">
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <input type="hidden" name="inlbl_refresh_url" id="inlbl_refresh_url"
                                           value="{{$aj_url}}">
                                    <div class="mobile-left" id="mobile_left">
                                        <a class="btn btn-info btn-icon toggle-email-nav collapsed"
                                           data-toggle="collapse" href="#email-nav" role="button" aria-expanded="false"
                                           aria-controls="email-nav">
                                            <span class="btn-label"><i class="zmdi zmdi-more"></i></span>
                                        </a>
                                    </div>
                                    <div class="inbox left" id="email-nav">
                                        <div class="mail-compose mb-4">
                                            <a href="{{$compose_url}}"
                                               class="btn btn-danger plr20">Compose</a>
                                            <span class="fl-right"><button type="button" id="refresh_data" class="btn btn-outline-secondary btn-sm" title="Refresh Data"><i class="zmdi zmdi-refresh"></i></button></span>
                                        </div>
                                        <div class="mail-side">
    <ul class="nav">
    <li class="@if ($sub == '' || $sub == 'inbox') active @endif"><a href="{{$current_url}}&sub=inbox"><i  class="zmdi zmdi-inbox"></i>Inbox
            <span class="badge badge-info-n">{{$allCounts['inbox']}}</span></a>
    </li>
    <li class="@if ($sub == 'sent') active @endif"><a href="{{$current_url}}&sub=sent"><i
                    class="zmdi zmdi-mail-send"></i>Sent
            <span class="badge badge-info-n">{{$allCounts['sent']}}</span></a>
    </li>
    <li class="@if ($sub == 'important') active @endif"><a href="{{$current_url}}&sub=important"><i
                    class="zmdi zmdi-badge-check"></i>Important
            <span class="badge badge-info-n">{{$allCounts['important']}}</span>
        </a></li>
    <li class="@if ($sub == 'star') active @endif"><a href="{{$current_url}}&sub=star"><i
                    class="zmdi zmdi-star"></i>Starred
            <span class="badge badge-info-n">{{$allCounts['star']}}</span></a>
    </li>

    <li class="@if ($sub == 'trash') active @endif"><a href="{{$current_url}}&sub=trash"><i
                    class="zmdi zmdi-delete"></i>Trash
            <span class="badge badge-danger">{{$allCounts['trash']}}</span></a>
    </li>
    </ul>
<h3 class="label">Labels</h3>
    <ul class="nav">
    @foreach ($labelData as $label)
    <li class="@if ($sub == $label->id) active @endif">
        <a href="{{$current_url}}&sub={{$label->id}}"><i
                    class="zmdi zmdi-label text-dark"></i>{{$label->lb_name}}
            <span class="badge badge-info" style="background-color: #{{$label->lb_code}}">{{$label->cnt}}</span></a>
    </li>
    @endforeach
    </ul>
                                        </div>
                                    </div>

<div class="inbox right">

<?php
if ($r == 1) {
$to = $detailData->e_from;
$cc = '';
$bcc = '';

$pattern_email = '/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i';
preg_match_all($pattern_email, $to, $matches);
if (is_array($matches[0])) {
    $to =  $matches[0][0];
} else {
    $to = $matches[0];
}
} else {

$to = "";
$cc = "";
$bcc = "";
}
if ($r == 1 || $r == 2) {
$subject = $detailData->e_subject;
$details_uid =$detailData->id;
} else {
$subject = "";
$details_uid = '';
}
?>
<div class="card">
<form name="ac_frm" id="ac_frm" method="post" action="{{route('admin.inboxwebmail.composesend', $uid)}}"
enctype='multipart/form-data'>
    @csrf
    <input type="hidden" name="details_uid" value="{{$details_uid}}">
<div class="body mb-2">
<p>Use comma , separated email for multiple email ids.</p>
<div class="form-group">
<input type="text" class="form-control" name='to' placeholder="To" value="{{$to}}" required="required"/>
</div>
<div class="form-group">
<input type="text" class="form-control" name='cc' placeholder="CC" value="{{$cc}}"/>
</div>
<div class="form-group">
<input type="text" class="form-control" name='bcc' placeholder="BCC" value="{{$bcc}}"/>
</div>
<div class="form-group mb-0">
<input type="text" class="form-control" name='subject' placeholder="Subject"
   value="{{$subject}}" required="required"/>
</div>
</div>
<div class="body">
<?php
if ($r == 1 || $r == 2) {
$meta_content = "<br><br><br><br>" . $inboxwebmailAccount->e_sign . "<hr>";
$meta_content .= "<br>-----On " . date('F j, Y h:i A', strtotime($detailData->created_at)) . " " . $detailData->e_from . " wrote-----<br><br>";
$meta_content .= $detailData->e_message;
} else {
$meta_content = "";
$meta_content .= "<br><br><br><br><br><br>" . $inboxwebmailAccount->e_sign;
}

?>
    <textarea id="area2" class="form-control" type="text" rows="15" name="meta_content" >{{$meta_content}}</textarea>

<div class="form-group">
<table id="inboxWebmail_table_file" width="25%">
<thead>
<tr align="left">
    <th colspan="3">Attach File</th>
</tr>
</thead>
<tbody>
<?php
if (count($detailAttachments) > 0 && $r==2) {
foreach ($detailAttachments as $files) {
$file_name = $files['file_name'];
$inbox_id = $files['inbox_id'];
$file_path = 'assets/inboxWebmail_files/' . $inbox_id . '/' . $file_name;
if (file_exists($file_path)) {
?>
<tr class="add_row">
    <td width="5%"><button type="button" class="badge badge-info delc" id="delete" title="Delete file">X
        </button></td>
    <td width="75%"><input type="hidden"  name="ex_file[]" value="<?php echo $file_path; ?>"/><?php echo $file_name; ?>
    </td>
    <td width="20%">
        
    </td>
</tr>

<?php }
}
} ?>
<tr class="add_row">
    <td id="no" width="5%"><button type="button" class="badge badge-info delc" id="delete_file" title="">X
        </button></td>
    <td width="75%"><input class="file brd0"  name='file[]' type='file' multiple/></td>
    <td width="20%">
        
    </td>
</tr>


</tbody>
<tfoot>
<tr>
    <td colspan="4" align="center">
        <button class="btn btn-success btn-sm addc" type="button" id="add_file" title='' >
        Add more file</button>
    </td>
</tr>

</tfoot>
</table>
</div>


<div class="form-group fl-right">
<button type="submit" class="btn btn-info" name="frm_sub"
    id="frm_sub">SEND</button>
</div>
</div>
</form>
</div>

</div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>
@endsection
@section('script')
    <script type="text/javascript" src="{{asset('assets/admin/js/inboxWebmail_admin.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/nicEdit/nicEdit-latest.js')}}"></script>
    <script type="text/javascript"> bkLib.onDomLoaded(function() {new nicEditor({fullPanel : true}).panelInstance('area2'); }); </script>
@stop


