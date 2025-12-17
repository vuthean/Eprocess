<?php

namespace App\Myclass;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class Sendemail
{
    public static function sendEmail($content, $rec_id, $req_name, $req_branch, $req_position, $subject, $checker, $cc, $comment)
    {
        try {
            if ($checker=='accounting') {
                $email=User::where('group_id', 'accounting')->get();
                $checker=[];
                foreach ($email as $value) {
                    array_push($checker, $value->email);
                }
            }
            $details = [
                'title' => 'E-Procurement-Payment',
                'content' => $content,
                'rec_id' => $rec_id,
                'name' => $req_name,
                'branch'=>$req_branch,
                'position'=>$req_position,
                'comment'=>$comment
            ];
            $viewname = "emails.myTestMail";
            if (\App::environment('prd')) {
                \Mail::to($checker)->cc($cc)->send(new \App\Mail\MyTestMail($details, $subject, $viewname));
            }
            if (\App::environment('local')) {
                \Mail::to('h.vuthean@princebank.com.kh')->cc('h.vuthean@princebank.com.kh')->send(new \App\Mail\MyTestMail($details, $subject, $viewname));
            }
            return "success";
        } catch (\Exception $e) {
            Log::channel('email_failed')->info([
                'sender'=>env('MAIL_FROM_ADDRESS'),
                'rec_id'=>$rec_id,
                'req_name'=>$req_name,
                'receiver'=>$checker,
                'cc'=>$cc
            ]);
            Log::channel('email_failed')->info($e);
            return "fail";
        }
    }
    public static function sendEmailProcurementRequest($content, $rec_id, $req_name, $req_branch, $req_position, $subject, $checker, $cc, $comment,$request_subject)
    {
        try {
            if ($checker=='accounting') {
                $email=User::where('group_id', 'accounting')->get();
                $checker=[];
                foreach ($email as $value) {
                    array_push($checker, $value->email);
                }
            }
            $details = [
                'title' => 'E-Procurement-Payment',
                'content' => $content,
                'subject' =>$request_subject,
                'rec_id' => $rec_id,
                'name' => $req_name,
                'branch'=>$req_branch,
                'position'=>$req_position,
                'comment'=>$comment
            ];
            $viewname = "emails.myTestMailPR";
            if (\App::environment('prd')) {
                \Mail::to($checker)->cc($cc)->send(new \App\Mail\MyTestMail($details, $subject, $viewname));
            }
            if (\App::environment('local')) {
                \Mail::to(['h.vuthean@princebank.com.kh'])->cc(['h.vuthean@princebank.com.kh'])->send(new \App\Mail\MyTestMail($details, $subject, $viewname));
            }
            return "success";
        } catch (\Exception $e) {
            Log::channel('email_failed')->info([
                'sender'=>env('MAIL_FROM_ADDRESS'),
                'rec_id'=>$rec_id,
                'req_name'=>$req_name,
                'receiver'=>$checker,
                'cc'=>$cc
            ]);
            Log::channel('email_failed')->info($e);
            return "fail";
        }
    }
}
