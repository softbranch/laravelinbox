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
@if(!empty($detailData))
  <div class="inbox right">
<div class="card">
    <div class="body mb-2">
        <div class="d-flex justify-content-between flex-wrap-reverse">
            <h5 class="mt-0 mb-0 font-17">{{$detailData->e_subject}}</h5>
            <div>
                <small>{{date('F j, Y h:i A', strtotime($detailData->created_at))}}</small>
                <a href="{{$compose_url}}&r=1&details={{$detailData->id}}" class="p-2" title="Reply"><i
                            class="zmdi zmdi-mail-reply"></i></a>
            </div>
        </div>
    </div>
    <div class="body mb-2">
        <ul class="list-unstyled d-flex justify-content-md-start mb-0">
            <li class="ml-3">
                <p class="mb-0"><span class="text-muted">From:</span> <a
                            href="javascript:void(0);">{{$detailData->e_from}}</a>
                </p>
                <p class="mb-0"><span
                            class="text-muted">To:</span> {{$detailData->e_to}}
                </p>
                @if($detailData->e_cc != '') <p class="mb-0"><span
                            class="text-muted">CC:</span> {{$detailData->e_cc}}
                </p>
                @endif
                @if($detailData->e_bcc != '')<p class="mb-0"><span
                            class="text-muted">BCC:</span> {{$detailData->e_bcc}}
                </p>@endif
            </li>
        </ul>
    </div>
    <div class="body mb-2">
        {!! __($detailData->e_message) !!}

        <br>
        <br>
        <br>
        @if ($detailData->is_attachment == 1)

        @if(!empty($detailAttachments))

        <div class="file_folder">
            <?php
            foreach ($detailAttachments as $files) {
            $file_name = $files['file_name'];
            $inbox_id = $files['inbox_id'];
            $file_path = 'assets/inboxWebmail_files/' . $inbox_id . '/' . $file_name;
            if (file_exists($file_path)) {
            $size = filesize($file_path);
            $size = ceil($size / 1024);

            $file_path_url = asset('assets/inboxWebmail_files/' . $inbox_id . '/' . $file_name);

            ?>
            <a href="<?php echo $file_path_url; ?>"
               title="Download <?php echo $file_name; ?>"
               download="<?php echo $file_name; ?>">
                <div class="icon">
                    <i class="zmdi zmdi-file text-primary"></i>
                </div>
                <div class="file-name">
                    <p class="mb-0 text-muted"><?php echo $file_name; ?></p>
                    <small>Size:<?php echo $size; ?> KB</small>
                </div>
            </a>
            <?php }
            } ?>

        </div>
        @endif

       @endif

    </div>
    <div class="body">
        <a href="{{$compose_url}}&r=1&details={{$detailData->id}}" class="p-2" title="Reply"><i
                    class="zmdi zmdi-mail-reply"></i>Reply
        </a>or
        <a href="{{$compose_url}}&r=2&details={{$detailData->id}}" class="p-2" title="Forward"><i
                    class="zmdi zmdi-mail-send"></i>Forward</a>
    </div>
</div>
</div>

  @else
                                    @if(count($inboxItems)> 0)
                                    <div class="inbox right">

                                        <div class="table-responsive">
                                            <form id="frm_inbox" name="frm_inbox" action="{{route('admin.inboxwebmail.view', $uid)}}" method="post">
                                                @csrf
                                                <div class="i_action d-flex justify-content-between align-items-center">
                                                    <div class="">
                                                        <div class="checkbox simple d-inline-block mr-1">
                                                            <input id="mc0" type="checkbox">
                                                            <label for="mc0"></label>
                                                        </div>

                                                        <div class="btn-group">
                                                            <select name="bulk_action" id="bulk_action" class="mr6">
                                                                <option value="">Bulk Actions</option>
                                                                @if ($sub == 'trash')
                                                                    <option value="moveinbox">Move to Inbox</option>
                                                                    <option value="deletep">Delete Permanently</option>
                                                                @else
                                                                    <option value="read">Mark as Read</option>
                                                                    <option value="unread">Mark as Unread</option>
                                                                    <option value="important">Mark as Important</option>
                                                                    <option value="unimportant">Remove Important
                                                                    </option>
                                                                    <option value="star">Mark as Starred</option>
                                                                    <option value="unstar">Remove Starred</option>
                                                                    <option value="delete">Delete</option>
                                                                    <option value="" class='lblshow' disabled>Labels
                                                                    </option>

                                                                    @foreach ($allLabelSelect as $label)
                                                                        <option value="{{$label->id}}">{{$label->lb_name}}</option>
                                                                    @endforeach

                                                                @endif
                                                            </select>
                                                            <input type="submit" id="doaction" class="btn btn-black"
                                                                   value="Apply">
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="table-responsive">
<table class="table c_table inbox_table">

@foreach ($inboxItems as $item)
<tr>
    <td class="chb">
        <div class="checkbox simple">
            <input id="mc{{$item->id}}" class="mycls" type="checkbox" name="inbox[]" value="{{$item->id}}" /><label for="mc{{$item->id}}"></label>

        </div>
    </td>
    <td class="starred @if ($item->is_star == 1) active @endif"><a href="javascript:void(0);" class="cur-def"><i class="zmdi zmdi-star"></i></a></td>
    <td class="starred @if ($item->is_important == 1) active @endif"><a href="javascript:void(0);" class="cur-def"><i class="zmdi zmdi-badge-check"></i></a></td>
    <td class="u_name"><h5 class="font-15 mt-0 mb-0"><a class="link"                        href="{{$current_url}}&sub={{$sub}}&details={{$item->id}}">
                @if($sub=='sent')
                    {{$item->e_to}}
                @else
                    {{$item->e_from}}
                @endif
                </a></h5></td>
    <td class="max_ellipsis @if ($item->is_read == 0) fnt-bold @endif" >
        <a class="link" href="{{$current_url}}&sub={{$sub}}&details={{$item->id}}">
            @if ($item->is_label > 0)
                @foreach ($allLabelSelect as $label)
                    @if ($item->is_label == $label->id)
            <span class="badge badge-info mr-2" style="background-color: #{{$label->lb_code}}">{{$label->lb_name}}</span>
                    @endif
                 @endforeach
            @endif
            {{$item->e_subject}} - &nbsp;{{strip_tags($item->e_message)}}
        </a>
    </td>
    <td class="clip">@if ($item->is_attachment == 1)
        <i class="zmdi zmdi-attachment-alt"></i>
        @endif</td>
    <td class="time" title="{{date('F j, Y h:i A', strtotime($item->created_at))}}">
        @if (date("d") == date('d', strtotime($item['created'])))
            {{date('h:i A', strtotime($item->created_at))}}
        @elseif (date("Y") == date('Y', strtotime($item->created_at)))
            {{date('M d', strtotime($item->created_at))}}
        @else
            {{date('d M Y', strtotime($item->created_at))}}
        @endif

    </td>
</tr>
@endforeach


</table>
                                                </div>
                                            </form>


                                        </div>
                                        <div class="d-flex flex-row-reverse">
                                            {{$inboxItems->links()}}
                                        </div>
                                    </div>
                                    @else
                                        <div class="inbox right">
                                            <div class="table-responsive">
                                                No data found
                                            </div>
                                        </div>
                                        @endif

   @endif
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
@stop


