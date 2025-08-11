<?php

namespace App\Livewire\Ticket;

use App\Models\Ticket;
use App\Models\TicketAgentSetting;
use App\Models\TicketReply;
use App\Models\TicketTypeSetting;
use App\Notifications\TicketReplyNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketDetail extends Component
{
    use LivewireAlert, WithFileUploads;

    public $ticket;
    public $ticketId;
    public $ticketReplies;
    public $replyMessage;
    public $subject;
    public $status;
    public $agents;
    public $agent_id;
    public $ticketTypes;
    public $type_id;
    public $uploadedFiles = [];

    protected $listeners = ['refreshTicketDetails' => 'mount'];

    public function mount()
    {
        $this->ticketId = $this->ticketId;
        $this->ticket = Ticket::with('user')->findOrFail($this->ticketId);
        $this->ticketReplies = TicketReply::with(['user', 'files'])->where('ticket_id', $this->ticketId)->get();
        $this->subject = $this->ticket->subject;
        $this->status = $this->ticket->status;
        $this->agent_id = $this->ticket->agent_id;
        $this->type_id = $this->ticket->type_id;
        $this->agents = TicketAgentSetting::with('user')->get();
        $this->ticketTypes = TicketTypeSetting::all();

    }

    // Submit a new reply
    public function submitReply($status)
    {

        $this->validate([
            'replyMessage' => 'required|string',
            'uploadedFiles.*' => 'nullable|file|max:2048', // File validation
        ]);

        // Create a new reply
        $reply = TicketReply::create([
            'ticket_id' => $this->ticketId,
            'message' => $this->replyMessage,
            'user_id' => auth()->id(),
        ]);

        // Handle file uploads
        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('ticket_files');
            $reply->files()->create([
                'user_id' => auth()->id(),
                'filename' => $file->getClientOriginalName(),
                'hashname' => $path,
            ]);
        }

        $this->ticket->update([
            'status' => $status,
        ]);

        $this->ticket->user->notify(new TicketReplyNotification($this->ticket, $this->replyMessage, auth()->user()));

        if ($this->ticket->agent_id) {
            $agent = $this->ticket->agent;
            $agent->notify(new TicketReplyNotification($this->ticket, $this->replyMessage, auth()->user()));
        }

        // Clear form inputs
        $this->replyMessage = '';
        $this->uploadedFiles = [];
        $this->dispatch('reset-trix-editor');

        // Refresh the replies and show a success message
        $this->mount();
        session()->flash('message', 'Reply submitted successfully.');
    }

    // Redirect back to tickets index
    public function redirectToTickets()
    {
        return redirect()->route('tickets.index');
    }

    public function removeFile($index)
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles); // Re-index array
    }

    // Delete a reply
    public function deleteReply($id)
    {
        TicketReply::destroy($id);

        $this->dispatch('refreshTicketDetails');

        $this->alert('success', __('messages.replyDeleted'));
    }

    public function updateAgents()
    {
        if ($this->type_id) {
            $this->agents = TicketAgentSetting::where('ticket_type_id', $this->type_id)
                                              ->with('user')
                                              ->get();
        } else {
            $this->agents = [];
        }
    }

    public function updateTicket()
    {
        // Update the ticket details
        $this->ticket->update([
            'status' => $this->status,
            'agent_id' => $this->agent_id,
            'type_id' => $this->type_id,
        ]);

        // Display success alert
        $this->alert('success', __('Ticket updated successfully'), [
            'toast' => true,
            'position' => 'top-end',
        ]);
    }

    public function render()
    {
        return view('livewire.ticket.ticket-detail');
    }
}
