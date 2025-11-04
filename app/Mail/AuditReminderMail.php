<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TindakAuditDB;

class AuditReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $audit;
    public $jenis; 

    public function __construct(TindakAuditDB $audit, $jenis = null)
    {
        $this->audit = $audit;
        $this->jenis = $jenis; 
    }

    public function build()
    {
        return $this->subject('📅 [Reminder] TLHA Audit: ' . ($this->jenis ? strtoupper($this->jenis) : ''))
                    ->view('emails.audit_reminder')
                    ->with([
                        'audit' => $this->audit,
                        'jenis' => $this->jenis, 
                    ]);
    }
}