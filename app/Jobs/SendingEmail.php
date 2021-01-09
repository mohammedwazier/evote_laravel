<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Carbon\Carbon;

use App\Models\Email;

class SendingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailStore = new Email;
        $emailStore->to = $this->details['email'];
        $emailStore->title = $this->details['title'];
        $emailStore->body = $this->details['body'];
        $emailStore->save();
        Mail::raw($this->details['body'], function($message){
            $message->to($this->details['email']);
            $message->subject($this->details['title']);
        });

        if(Mail::failures()){
            $emailStore->status ='false';
            $emailStore->save();
        }

        $emailStore->updated_at = Carbon::now('Asia/Jakarta');
        $emailStore->save();
    }
}
