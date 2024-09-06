<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\TeamInvitation as TeamInvitationModel;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     *
     * @var TeamInvitationModel
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param TeamInvitationModel $invitation
     * @return void
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return TeamInvitation
     */
    public function build()
    {
        return $this->markdown('vendor.jetstream.mail.team-invitation', [
            'registerUrl' => URL::signedRoute('team-invitations.register', [
                'invitation' => $this->invitation,
            ]),
            'acceptUrl' => URL::signedRoute('team-invitations.accept', [
                'invitation' => $this->invitation,
            ])
        ]);
    }
}
