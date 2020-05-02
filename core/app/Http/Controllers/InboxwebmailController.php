<?php

namespace App\Http\Controllers;

use App\InboxwebmailAccount;
use App\InboxwebmailLabel;
use App\InboxwebmailInbox;
use App\InboxwebmailAttachment;
use App\InboxwebmailEmailParser;
use Illuminate\Http\Request;
use DB;

class InboxwebmailController extends Controller
{

    public function index()
    {
        $inboxwebmails = InboxwebmailAccount::paginate(20);

        return view('admin.inboxwebmail.index', compact('inboxwebmails'));
    }

    public function inboxwebmailAdd()
    {
        return view('admin.inboxwebmail.inboxwebmailAdd');
    }

    public function inboxwebmailPost(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email',
        ]);

        if (function_exists('imap_open')) {
            try {
                $email = $request->email;
                $password = $request->password;
                $emailArr = explode("@", $email);
                $domain = $emailArr[1];
                $port = '993';
                $host_string = "{" . $domain . ":" . $port . "/imap/ssl/novalidate-cert}INBOX";
                $mbox = imap_open($host_string, $email, $password);
                if ($mbox) {
                    $property = new InboxwebmailAccount();
                    $property->name = $request->name;
                    $property->email = $request->email;
                    $property->password = $request->password;
                    $property->domain = $domain;
                    $property->active = ($request->active)?1:0;
                    $property->d_from_server = ($request->d_from_server)?1:0;
                    $property->e_sign = $request->e_sign;
                    $property->save();
    
                    // assign default labels
                    $this->saveDefaultLabel($property->id);
                    imap_close($mbox);
                    return redirect()->route('admin.inboxwebmails')->with('success', 'Data saved successfully.');
                } else {
                    return redirect()->back()->withErrors('Entered Email/password is not correct.');
                }
            } catch (\Exception $ex) {
                return redirect()->back()->withErrors('Entered Email/password is not correct.');
            }
        }else{
            return redirect()->back()->withErrors('IMAP function not enabled.');
      }
    }

    private function saveDefaultLabel($account_id){
        $label = new InboxwebmailLabel();
        $label->account_id = $account_id;
        $label->lb_name = 'Primary';
        $label->lb_code = '888888';
        $label->save();

        $label = new InboxwebmailLabel();
        $label->account_id = $account_id;
        $label->lb_name = 'Promotions';
        $label->lb_code = '1cbfd0';
        $label->save();

        $label = new InboxwebmailLabel();
        $label->account_id = $account_id;
        $label->lb_name = 'Social';
        $label->lb_code = '0c7ce6';
        $label->save();
        return true;
    }

    public function inboxwebmailEdit($id)
    {
        $property = InboxwebmailAccount::findOrfail($id);
        $allLabelSelect = InboxwebmailLabel::where('account_id', $id)->get();
        return view('admin.inboxwebmail.inboxwebmailEdit', compact('property','allLabelSelect'));
    }


    public function inboxwebmailDelete($id)
    {
        $property = InboxwebmailAccount::findOrfail($id);
        
        // delete all inbox data attachments
        $allData =  InboxwebmailInbox::where('account_id', $id)->get();
        foreach($allData as $inData){
            $dt_id = $inData->id;
            InboxwebmailAttachment::where("inbox_id", $dt_id)->delete();
            $absolute_path = 'assets/inboxWebmail_files/' . $dt_id;
            $this->inboxwebmail_delete_directory($absolute_path);
        }
        // delete all inbox data
        InboxwebmailInbox::where("account_id", $id)->delete();
        
        // delete all labels
         InboxwebmailLabel::where("account_id", $id)->delete();
        
        // delete account info
        $property->delete();
        
         return redirect()->route('admin.inboxwebmails')->with('success', 'Data deleted successfully.');
    }

    public function inboxwebmailUpdate(Request $request, $id)
    {
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|string',
        ]);

        $property = InboxwebmailAccount::findOrfail($id);

        if (function_exists('imap_open')) {
             try {
                $email = $request->email;
                $password = $request->password;
                $emailArr = explode("@", $email);
                $domain = $emailArr[1];
                $port = '993';
                $host_string = "{" . $domain . ":" . $port . "/imap/ssl/novalidate-cert}INBOX";
                $mbox = imap_open($host_string, $email, $password);
                if ($mbox) {
                    $property->name = $request->name;
                    $property->email = $request->email;
                    $property->password = $request->password;
                    $property->domain = $domain;
                    $property->active = ($request->active)?1:0;
                    $property->d_from_server = ($request->d_from_server)?1:0;
                    $property->e_sign = $request->e_sign;
                    $property->save();
                    imap_close($mbox);
                    return redirect()->route('admin.inboxwebmails')->with('success', 'Data saved successfully.');
                } else {
                    return redirect()->back()->withErrors('Entered Email/password is not correct.');
                }
             } catch (\Exception $ex) {
                return redirect()->back()->withErrors('Entered Email/password is not correct.');
            }
        }else{
            return redirect()->back()->withErrors('IMAP function not enabled.');
        }
    }

    public function inboxwebmailLabels(Request $request, $id)
    {
        $lbl_code = $request['lbl_code'];

        if(count($lbl_code)>0) {
            foreach ($request['lbl_name'] as $key => $lbs) {
                foreach ($lbs as $lid => $label) {
                    $code = $lbl_code[$key][$lid];
                    if ($label != '' && $code != '') {
                        if ($lid > 0) {
                            $data =  InboxwebmailLabel::findOrfail($lid);
                            $data->lb_name = $label;
                            $data->lb_code = $code;
                            $data->save();
                        } else {
                            $data = new InboxwebmailLabel();
                            $data->account_id = $id;
                            $data->lb_name = $label;
                            $data->lb_code = $code;
                            $data->save();
                        }
                    }
                }
            }
            return redirect()->route('admin.inboxwebmails')->with('success', 'Data saved successfully.');
        }else{
            return redirect()->back()->withErrors('Something went wrong.');
        }
    }

    public function inboxwebmailLabelDelete(Request $request)
    {
        $label = InboxwebmailLabel::findOrfail($request->label_id);
        $label->delete();
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    public function inboxwebmailView(Request $request, $uid)
    {
        $inboxwebmailAccount = InboxwebmailAccount::findOrfail($uid);
        $allLabelSelect = InboxwebmailLabel::where('account_id', $uid)->get();

        if (isset($request->bulk_action) && $request->bulk_action != '') {
             $bulk_action = $request->bulk_action;

            $idArr = $request->inbox;
            if (!empty($idArr) && is_array($idArr)) {
                foreach ($idArr as $dt_id) {
                    $dt_id = intval($dt_id);
                    switch ($bulk_action) {
                        case 'read':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_read = 1;
                            $data->save();
                            break;
                        case 'unread':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_read = 0;
                            $data->save();
                            break;
                        case 'important':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_important = 1;
                            $data->save();
                            break;
                        case 'unimportant':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_important = 0;
                            $data->save();
                            break;
                        case 'star':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_star = 1;
                            $data->save();
                            break;
                        case 'unstar':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_star = 0;
                            $data->save();
                            break;
                        case 'moveinbox':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_deleted = 0;
                            $data->save();
                            break;
                        case 'deletep':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->delete();
                            InboxwebmailAttachment::where("inbox_id", $dt_id)->delete();
                            $absolute_path = 'assets/inboxWebmail_files/' . $dt_id;
                            $this->inboxwebmail_delete_directory($absolute_path);
                            break;
                        case 'delete':
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_deleted = 1;
                            $data->save();
                            break;
                        default:
                            $data=  InboxwebmailInbox::findOrfail($dt_id);
                            $data->is_label = $bulk_action;
                            $data->save();
                            break;
                    }
                }
                return redirect()->back()->with('success', 'Bulk Action performed.');
            } else {
                return redirect()->back()->withErrors('No any email selected.');
            }
        }

        if (isset($request->details) && is_numeric($request->details)) {
            $details_uid = intval($request->details);

            $detailData =  InboxwebmailInbox::findOrfail($details_uid);
            if($detailData->account_id == $uid){
                $detailData->is_read = 1;
                $detailData->save();
                $detailAttachments =  InboxwebmailAttachment::where(['inbox_id'=>$details_uid])->get();
            }else{
                $detailAttachments = '';
                $detailData = '';
            }
        } else {
            $detailAttachments = '';
            $detailData = '';
        }

        $sub = '';
        $filter['account_id'] = $uid;
        if (isset($request->sub)) {
            $sub = $request->sub;
            switch ($sub) {
                case 'inbox':
                    $filter['is_deleted'] = 0;
                    $filter['is_sent'] = 0;
                    $filter['is_draft'] = 0;
                    break;
                case 'sent':
                    $filter['is_deleted'] = 0;
                    $filter['is_sent'] = 1;
                    break;
                case 'important':
                    $filter['is_deleted'] = 0;
                    $filter['is_important'] = 1;
                    break;
                case 'star':
                    $filter['is_deleted'] = 0;
                    $filter['is_star'] = 1;
                    break;
                case 'trash':
                    $filter['is_deleted'] = 1;
                    break;
                default:
                    $filter['is_label'] = $sub;
                    break;
            }
        }else{
            //$filter .= ' and is_deleted =0 and is_sent =0 and is_draft =0';
            $filter['is_deleted'] = 0;
            $filter['is_sent'] = 0;
            $filter['is_draft'] = 0;
        }

        $inboxItems = InboxwebmailInbox::where($filter)->orderBy('created_at', 'desc')->paginate(20);

        $aj_url = route('admin.inboxwebmail.refdata',$uid).'?i=1';
        $compose_url = route('admin.inboxwebmail.compose',$uid).'?i=1';
        $current_url = route('admin.inboxwebmail.view',$uid).'?i=1';
        $allCounts = array();
        $allCounts['inbox'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_sent'=>0,'is_draft'=>0,'is_read'=>0])->count();
        $allCounts['sent'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_sent'=>1])->count();
        $allCounts['important'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_important'=>1])->count();
        $allCounts['star'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_star'=>1])->count();
        $allCounts['trash'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>1])->count();


 $labelData = InboxwebmailLabel::select("inboxwebmail_labels.id", 'inboxwebmail_labels.lb_name', 'inboxwebmail_labels.lb_code', DB::raw('COUNT(inboxwebmail_inboxes.is_label) as cnt'))
                        ->join('inboxwebmail_inboxes', 'inboxwebmail_inboxes.is_label', '=', 'inboxwebmail_labels.id')->where("inboxwebmail_labels.account_id", $uid)->groupBy('inboxwebmail_inboxes.is_label')->get();
          

        return view('admin.inboxwebmail.view', compact('inboxwebmailAccount','inboxItems','allLabelSelect','sub','aj_url','compose_url','allCounts','current_url','uid','detailData','detailAttachments','labelData'));
    }

private function inboxwebmail_delete_directory($dirname) {
         if (is_dir($dirname)){
           $dir_handle = opendir($dirname);
         
     while($file = readdir($dir_handle)) {
           if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file)){
                     unlink($dirname."/".$file);
                }else{
                     $this->inboxwebmail_delete_directory($dirname.'/'.$file);
                }
           }
     }
     closedir($dir_handle);
     rmdir($dirname);
         }
     return true;
}

    public function inboxwebmailCompose(Request $request, $uid){
        $inboxwebmailAccount = InboxwebmailAccount::findOrfail($uid);
        $allLabelSelect = InboxwebmailLabel::where('account_id', $uid)->get();

        if (isset($request->details) && is_numeric($request->details)) {
            $details_uid = intval($request->details);

            $detailData =  InboxwebmailInbox::findOrfail($details_uid);
            if($detailData->account_id == $uid){
                $detailData->is_read = 1;
                $detailData->save();
                $detailAttachments =  InboxwebmailAttachment::where(['inbox_id'=>$details_uid])->get();
            }else{
                $detailAttachments = array();
                $detailData = '';
            }
        } else {
            $detailAttachments = array();
            $detailData = '';
        }
        $filter['account_id'] = $uid;
        $filter['is_deleted'] = 0;
        $filter['is_sent'] = 0;
        $filter['is_draft'] = 0;

        if (isset($request->sub)) {
            $sub = $request->sub;
        }else{
            $sub='';
        }
        if (isset($request->r)) {
            $r = $request->r;
        }else{
            $r='';
        }
        $inboxItems = '';

        $aj_url = route('admin.inboxwebmail.refdata',$uid).'?i=1';
        $compose_url = route('admin.inboxwebmail.compose',$uid).'?i=1';
        $current_url = route('admin.inboxwebmail.view',$uid).'?i=1';
        $allCounts = array();
        $allCounts['inbox'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_sent'=>0,'is_draft'=>0,'is_read'=>0])->count();
        $allCounts['sent'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_sent'=>1])->count();
        $allCounts['important'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_important'=>1])->count();
        $allCounts['star'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>0,'is_star'=>1])->count();
        $allCounts['trash'] = InboxwebmailInbox::where(['account_id'=>$uid,'is_deleted'=>1])->count();


 $labelData = InboxwebmailLabel::select("inboxwebmail_labels.id", 'inboxwebmail_labels.lb_name', 'inboxwebmail_labels.lb_code', DB::raw('COUNT(inboxwebmail_inboxes.is_label) as cnt'))
                        ->join('inboxwebmail_inboxes', 'inboxwebmail_inboxes.is_label', '=', 'inboxwebmail_labels.id')->where("inboxwebmail_labels.account_id", $uid)->groupBy('inboxwebmail_inboxes.is_label')->get();
          
          
        return view('admin.inboxwebmail.compose', compact('inboxwebmailAccount','inboxItems','allLabelSelect','sub','aj_url','compose_url','allCounts','current_url','uid','detailData','detailAttachments','r','labelData'));
    }

    public function inboxwebmailComposesend(Request $request, $uid)
    {
        $this->validate($request,[
            'subject'=>'required|max:250'
        ]);

        $property = InboxwebmailAccount::findOrfail($uid);
        $details_uid = $request->details_uid;

            // save data and send email
            $to = $request->to;
            $cc = $request->cc;
            $bcc = $request->bcc;

            $to = $this->inboxWebmail_check_validate_email($to);
            $cc = $this->inboxWebmail_check_validate_email($cc);
            $bcc = $this->inboxWebmail_check_validate_email($bcc);

            $subject = $request->subject;
            $message = $body = nl2br($request->meta_content);
            $sender = $property->email;
            $sender_name = $property->name;

            if ($to == '' || $subject == '' || $message == '') {
                return redirect()->back()->withErrors( 'To email and Subject is required field.');
            } else {
                $headers = '';
                $attachments = array();

                $headers .= "From: $sender_name <$sender>\n";
                if ($cc != '') {
                    $headers .= "Cc:" . $cc."\n";
                }
                if ($bcc != '') {
                    $headers  .= "Bcc:" . $bcc."\n";
                }

                $is_attachment = 0;
                if (count($_FILES["file"]['name']) > 0 || count($request->ex_file) > 0) {
                    $is_attachment = 1;
                }

                $data = new InboxwebmailInbox();
                $data->account_id = $uid;
                $data->parent_id = $details_uid;
                $data->e_from = $sender;
                $data->e_to = $to;
                $data->e_reply_to = '';
                $data->e_cc = $cc;
                $data->e_bcc = $bcc;
                $data->e_subject = $subject;
                $data->e_message = $body;
                $data->header_info = json_encode($headers);
                $data->is_attachment = $is_attachment;
                $data->is_sent = 1;
                $data->save();
                $inbox_id = $data->id;
                if ($inbox_id > 0) {

                    // save attachments
                    if (count($_FILES["file"]['name']) > 0 || count($request->ex_file) > 0) {

                        $absolute_path = 'assets/inboxWebmail_files/' . $inbox_id;
                        mkdir($absolute_path, 0777);
                        $file_path = $absolute_path . '/index.php';

                        file_put_contents($file_path, '');

                        // manage for existing files.
                        if (isset($request->ex_file) && COUNT($request->ex_file) > 0) {
                            foreach ($request->ex_file as $exfiles) {
                                if ($exfiles != '' && $details_uid > 0) {
                                    $path_arr = explode($details_uid . "/", $exfiles);
                                    $file_name = $path_arr[1];
                                    $file_path = $absolute_path . '/' . $file_name;
                                    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                    $documentType = strtolower($ext);


                                        if (copy($exfiles, $file_path)) {
                                            $data = new InboxwebmailAttachment();
                                            $data->inbox_id = $inbox_id;
                                            $data->file_name = $file_name;
                                            $data->file_type = $documentType;
                                            $data->file_bytes = $file_path;
                                            $data->save();

                                            $attachments[] = $file_path;
                                        }
                                   
                                }
                            }
                        }

                        for ($j = 0; $j < count($_FILES["file"]['name']); $j++) {
                            if ($_FILES["file"]["name"][$j] != '') {
                                $file_name = $_FILES["file"]["name"][$j];
                                $file_path = $absolute_path . '/' . $file_name;
                                $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                $documentType = strtolower($ext);

                                $size_of_uploaded_file = $_FILES["file"]["size"][$j] / 1024; //size in KBs
                                $max_allowed_file_size = 5000; // size in KB

                                if (($size_of_uploaded_file < $max_allowed_file_size)) {
                                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$j], $file_path)) {
                                        $data = new InboxwebmailAttachment();
                                        $data->inbox_id = $inbox_id;
                                        $data->file_name = $file_name;
                                        $data->file_type = $documentType;
                                        $data->file_bytes = $file_path;
                                        $data->save();

                                        $attachments[] = $file_path;
                                    }
                                }
                            }
                        }
                    }

                    $this->inboxWebmail_mail_attachment($to, $subject, $message, $headers, $attachments,$inbox_id);
                    return redirect()->route('admin.inboxwebmail.view',$uid)->with('success', 'Email send successfully.');
                } else {
                    return redirect()->back()->withErrors('Some Problem occurred.');
                }
            }
    }

   private function inboxWebmail_mail_attachment($to, $subject, $message, $headers, $attachments,$inbox_id) {
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
        $headers .= "MIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";

if(count($attachments)>0){
        foreach ($attachments as $files) {
             $path_arr = explode($inbox_id . "/", $files);
             $filename = $path_arr[1];
                                    
            $data = file_get_contents($files);
            $data = chunk_split(base64_encode($data));

            $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$filename\"\n" .
                "Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" .
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            $message .= "--{$mime_boundary}\n";
        }
}
        @mail($to, $subject, $message, $headers);
    }

    private function inboxWebmail_check_validate_email($email_txt)
    {
        $result_email = '';
        if (!empty($email_txt)) {
            $email_Arr = explode(",", $email_txt);
            foreach ($email_Arr as $email) {
                $femail = $this->inboxWebmail_parse_validate_email($email);
                if ($femail!='' && filter_var(trim($femail),FILTER_VALIDATE_EMAIL)) {
                    $result_email .= trim($femail);
                    $result_email .= ',';
                }
            }
            if (!empty($result_email)) {
                $result_email = substr($result_email, 0, -1);
            }
        }
        return $result_email;
    }

    private function inboxWebmail_parse_validate_email($string_email)
    {
        if (empty($string_email)) {
            return '';
        }
        $pattern_email = '/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i';
        preg_match_all($pattern_email, $string_email, $matches);

        if (is_array($matches[0])) {
            return $matches[0][0];
        } else {
            return $matches[0];
        }
    }

    public function inboxwebmailRefdata(Request $request, $uid){
        $inboxwebmailAccount = InboxwebmailAccount::findOrfail($uid);
        if ($inboxwebmailAccount->active == 1) {
           $chkData = InboxwebmailInbox::where(['account_id'=>$uid])->orderBy('created_at', 'desc')->first();
           if(!empty($chkData)){
               $checkDate = $chkData->created_at;
           }else{
               $checkDate = '';
           }
            $host = $inboxwebmailAccount->domain;
            $port = '993';
            $user = $inboxwebmailAccount->email;
            $pass = $inboxwebmailAccount->password;
            $d_from_server = $inboxwebmailAccount->d_from_server;

            $host_string = "{" . $host . ":" . $port . "/imap/ssl/novalidate-cert}INBOX";
            $parser = new InboxwebmailEmailParser($host_string, $user, $pass, $uid, $d_from_server);
            $total = $parser->inboxWebmail_parse($checkDate);

            echo 'Data parse successfully. Total new email =' . $total;
        } else {
            echo 'Account not activated.';
        }
        die('done');
    }
    
    

    public function inboxwebmailParse(){
        $inboxwebmails = InboxwebmailAccount::get();
       
        foreach ($inboxwebmails as $inboxwebmail) {
             $uid = $inboxwebmail->id;
            $inboxwebmailAccount = InboxwebmailAccount::findOrfail($uid);
          
            if ($inboxwebmailAccount->active == 1) {
                $chkData = InboxwebmailInbox::where(['account_id' => $uid])->orderBy('created_at', 'desc')->first();
                if (!empty($chkData)) {
                    $checkDate = $chkData->created_at;
                } else {
                    $checkDate = '';
                }
              
                $host = $inboxwebmailAccount->domain;
                $port = '993';
                $user = $inboxwebmailAccount->email;
                $pass = $inboxwebmailAccount->password;
                $d_from_server = $inboxwebmailAccount->d_from_server;

                $host_string = "{" . $host . ":" . $port . "/imap/ssl/novalidate-cert}INBOX";
                $parser = new InboxwebmailEmailParser($host_string, $user, $pass, $uid, $d_from_server);
                $total = $parser->inboxWebmail_parse($checkDate);

                echo 'Data parse successfully. Total new email =' . $total;
            } else {
                echo 'Account not activated.';
            }
        }
        die('done');
    }
}
