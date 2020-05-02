<?php

namespace App;


class InboxwebmailEmailParser
{
    private $imap_stream = null;
    // variables for email data
    private $subject = null;
    private $from = null;
    private $to = null;
    private $reply_to = null;
    private $cc = null;
    private $bcc = null;
    private $header_info = '';
    private $created = '';
    private $charset = null;
    private $html_msg = null;
    private $plain_msg = null;
    private $attachments = array();
    private $uid = '';
    private $d_from_server = '';

    /**
     * InboxWebmail_Model_EmailParser constructor.
     * @param $host
     * @param $login
     * @param $password
     * @param $uid
     * @throws \Exception
     */
    public function __construct($host, $login, $password, $uid, $d_from_server)
    {
        $this->imap_stream = imap_open($host, $login, $password);
        $this->uid = $uid;
        $this->d_from_server = $d_from_server;

        if ($this->imap_stream == false) {
            throw new \Exception("can't connect: " . imap_last_error());
        }
    }

    /**
     * parse emails
     * @return int
     */
    public function inboxWebmail_parse($checkDate)
    {
        if (!empty($checkDate)) {
            $date = date("d M Y H:i:s", strtotime($checkDate));
            $emails = imap_search($this->imap_stream, 'SINCE "' . $date . '"');
        } else {
            $emails = imap_search($this->imap_stream, 'ALL');
        }


        if ($emails != false) {
            $i = 0;
            arsort($emails); // it is mandatory to permanently move messages from inbox to processed/unprocessed folder.
            foreach ($emails as $email_number) {
                try {

                    $this->inboxWebmail_parse_msg($email_number);
                    $has_html_tags = ($this->html_msg == '') ? false : true;
                    $email_body = ($has_html_tags) ? $this->html_msg : $this->plain_msg;

                    $is_attachment = 0;
                    if (!empty($this->attachments)) {
                        $is_attachment = 1;
                    }

                    if ($this->from != '' && $this->subject != '' && $email_body != '') {
                     
                       
                        if (strtotime($checkDate) < strtotime($this->created)) {

                            $data = new InboxwebmailInbox();
                            $data->account_id = $this->uid;
                            $data->e_from = $this->from;
                            $data->e_to = $this->to;
                            $data->e_reply_to = $this->reply_to;
                            $data->e_cc = $this->cc;
                            $data->e_bcc = $this->bcc;
                            $data->e_subject = $this->subject;
                            $data->e_message = $email_body;
                            $data->header_info = $this->header_info;
                            $data->is_attachment = $is_attachment;
                            $data->created_at = $this->created;
                            $data->save();
                            $inbox_id = $data->id;

                            if (!empty($this->attachments) && $inbox_id > 0) {
                                $absolute_path = 'assets/inboxWebmail_files/' . $inbox_id;
                                mkdir($absolute_path, 0777);
                                $file_path = $absolute_path . '/index.php';
                                file_put_contents($file_path, '');

                                foreach ($this->attachments as $attachment_arr) {
                                    $documentType = strtolower($attachment_arr['extension']);
                                    $file_name = $attachment_arr['file_name'];
                                    $bytes = $attachment_arr['bytes'];

                                        // download file
                                        $file_path = $absolute_path . '/' . $file_name;
                                       file_put_contents($file_path, $bytes);

                                        if ($documentType == 'msword') {
                                            $documentType = 'doc';
                                        } elseif ($documentType == 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                            $documentType = 'docx';
                                        } elseif ($documentType == 'vnd.ms-excel') {
                                            $documentType = 'xls';
                                        } elseif ($documentType == 'vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                            $documentType = 'xlsx';
                                        } elseif ($documentType == 'vnd.ms-powerpoint') {
                                            $documentType = 'ppt';
                                        } elseif ($documentType == 'vnd.openxmlformats-officedocument.presentationml.presentation') {
                                            $documentType = 'pptx';
                                        }
                                        $data = new InboxwebmailAttachment();
                                        $data->inbox_id = $inbox_id;
                                        $data->file_name = $file_name;
                                        $data->file_type = $documentType;
                                        $data->file_bytes = $file_path;
                                        $data->save();
                                }
                            }
                        }
                    }
                   
                   if($this->d_from_server==1){
						imap_delete($this->imap_stream, $email_number);
					}
						
                    continue;
                } catch (\Exception $objExc) {
                    printf("%s <br/>", $objExc->getMessage());
                    exit;
                }
            }
            imap_expunge($this->imap_stream);
            return count($emails);
        } else {
            return 0;
        }

    }

    /**
     * parse each email message
     * @param $email_number
     */
    private function inboxWebmail_parse_msg($email_number)
    {

        $this->subject = null;
        $this->charset = null;
        $this->html_msg = null;
        $this->plain_msg = null;
        $this->attachments = array();
        $this->from = null;
        $this->to = null;
        $this->reply_to = null;
        $this->cc = null;
        $this->bcc = null;
        $this->header_info = '';
        $this->created = '';

        // HEADER
        $h = imap_header($this->imap_stream, $email_number);
        $this->subject = $h->subject;
        $this->from = $h->fromaddress;
        $this->to = $h->toaddress;
       if(isset($h->reply_toaddress)){
            $this->reply_to = $h->reply_toaddress;
        }
        if(isset($h->ccaddress)){
            $this->cc = $h->ccaddress;
        }
        if(isset($h->bccaddress)){
            $this->bcc = $h->bccaddress;
        }
        $this->created = date("Y-m-d H:i:s", strtotime($h->date));
        $this->header_info = json_encode($h);

        // BODY
        $s = imap_fetchstructure($this->imap_stream, $email_number);
       if(isset($s->parts)){
           foreach ($s->parts as $partno0 => $p)
               $this->inboxWebmail_parse_msg_part($email_number, $p, $partno0 + 1);
       }else{
           $this->inboxWebmail_parse_msg_part($email_number, $s, 0);
       }
    }

    /**
     * parse message
     * @param $email_number
     * @param $p
     * @param $partno
     */
    private function inboxWebmail_parse_msg_part($email_number, $p, $partno)
    {

        // DECODE DATA
        $data = ($partno) ?
            imap_fetchbody($this->imap_stream, $email_number, $partno) : // multipart
            imap_body($this->imap_stream, $email_number);  // simple
        // Any part may be encoded, even plain text messages, so check everything.
        if ($p->encoding == 4)
            $data = quoted_printable_decode($data);
        elseif ($p->encoding == 3)
            $data = base64_decode($data);

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if ($p->ifparameters)
            foreach ($p->parameters as $x)
                $params[strtolower($x->attribute)] = $x->value;
        if ($p->ifdparameters)
            foreach ($p->dparameters as $x)
                $params[strtolower($x->attribute)] = $x->value;

        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if (isset($params['filename']) || isset($params['name'])) {
            if ($p->type) {
                $extension = $p->subtype;
            }else{$extension='';}
            // filename may be given as 'Filename' or 'Name' or both
            $filename = (isset($params['filename'])) ? $params['filename'] : $params['name'];
            // filename may be encoded, so see imap_mime_header_decode()
            $this->attachments[] = array('file_name' => $filename, 'extension' => $extension, 'bytes' => $data);  // this is a problem if two files have same name
        }

        // TEXT
        if ($p->type == 0 && $data) {
            if (strtolower($p->subtype) == 'plain') {
                $this->plain_msg .= trim($data) . "\n\n";
            }else {
                $this->html_msg .= $data . "<br><br>";
            }
            $this->charset = $params['charset'];  // assume all parts are same charset
        } elseif ($p->type == 2 && $data) {
            $this->plain_msg .= $data . "\n\n";
        }

        // SUBPART RECURSION
        if (isset($p->parts) && $p->parts) {
            foreach ($p->parts as $partno0 => $p2) {
                $this->inboxWebmail_parse_msg_part($email_number, $p2, $partno . '.' . ($partno0 + 1));  // 1.2, 1.2.1, etc.
            }
        }
    }

    /**
     * destruct
     */
    public function __destruct()
    {
        imap_close($this->imap_stream);
    }
}

?>
