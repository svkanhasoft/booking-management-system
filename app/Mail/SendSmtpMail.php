<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSmtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //print_r($this->details);exit();
        if ($this->details['mailTitle'] == 'forgot') {
            // $test =  $this->subject($this->details['subject'])->view('emails.forgot-mail')->render();
            // print_r($test);
            // exit;
            return $this->subject($this->details['subject'])->view('emails.forgot-mail');
        } else if ($this->details['mailTitle'] == 'register') {
            return $this->subject($this->details['subject'])->view('emails.register-mail');
        } else if ($this->details['mailTitle'] == 'addMatch') {
            return $this->subject($this->details['subject'])->view('emails.add-match-mail');
        } else if ($this->details['mailTitle'] == 'bookingConfirm') {
            return $this->subject($this->details['subject'])->view('emails.booking-confirm-mail');
        } else if ($this->details['mailTitle'] == 'bookingCancelByStaff') {
            return $this->subject($this->details['subject'])->view('emails.booking-cancel-mail');
        } else if ($this->details['mailTitle'] == 'bookingCancelBySignee') {
            return $this->subject($this->details['subject'])->view('emails.booking-cancelBySignee-mail');
        } else if ($this->details['mailTitle'] == 'bookingInvite') {
            return $this->subject($this->details['subject'])->view('emails.booking-invite-mail');
        } else if ($this->details['mailTitle'] == 'bookingAcceptBySignee') {
            return $this->subject($this->details['subject'])->view('emails.booking-accept-by-signee-mail');
        }else if ($this->details['mailTitle'] == 'sendShiftOfferToSignee') {
            return $this->subject($this->details['subject'])->view('emails.booking-offer-to-signee-mail');
        }else if ($this->details['mailTitle'] == 'signeeAccepBookingEmailToOrg') {
            return $this->subject($this->details['subject'])->view('emails.booking-accept-by-signee-mail-to-org');
        }else if ($this->details['mailTitle'] == 'signeeCancelBookingEmailToOrg') {
            return $this->subject($this->details['subject'])->view('emails.booking-cancel-by-signee-mail-to-org');
        }else if ($this->details['mailTitle'] == 'signeeApplyShift') {
            return $this->subject($this->details['subject'])->view('emails.signee-apply-shift');
        }else if ($this->details['mailTitle'] == 'signeeApplyShiftMailToOrg') {
            return $this->subject($this->details['subject'])->view('emails.signee-apply-shift-mail-to-org');
        }else if ($this->details['mailTitle'] == 'sendBookingCreatedEmailToOrg') {
            return $this->subject($this->details['subject'])->view('emails.shift-create-mail-to-org');
        }else if ($this->details['mailTitle'] == 'bookingConfirmMailToOrg') {
            return $this->subject($this->details['subject'])->view('emails.shift-confirm-mail-to-org');
        }else if ($this->details['mailTitle'] == 'document_expire') {
            return $this->subject($this->details['subject'])->view('emails.document-expire-signee-mail');
        }

    }
}
