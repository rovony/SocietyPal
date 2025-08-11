<?php

namespace App\Livewire\Forms;

use App\Models\Ticket;
use App\Models\TicketAgentSetting;
use App\Models\TicketFile;
use App\Models\TicketReply;
use App\Models\TicketTypeSetting;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddTicket extends Component
{
    use LivewireAlert, WithFileUploads;

    public $users;
    public $ticketTypes = [];
    public $user_id;
    public $type_id;
    public $status;
    public $subject;
    public $reply;
    public $ticket_number;
    public $agents;
    public $agent_id;
    public $files = [];


    public function mount()
    {
        $query = User::query();

        if (user_can('Show User')) {
            $query->where('tenant', false)->where('owner', false);
        }

        if (user_can('Show Owner')) {
            $query->orWhere('owner', 1);
        }

        if (user_can('Show Tenant')) {
            $query->orWhere('tenant', 1);
        }

        $this->users = $query->orWhere('id', user()->id)->get();

        if ($this->users->count() == 1) {
            $this->user_id = $this->users->first()->id;
        }    
       
        $this->ticketTypes = TicketTypeSetting::all();
        $this->agents = TicketAgentSetting::with('user')->get();
    }

    public function updateAgents()
    {
        if ($this->type_id) {
            $this->agents = TicketAgentSetting::where('ticket_type_id', $this->type_id)->with('user')->get();
        } else {
            $this->agents = [];
        }
    }

    public function submitForm()
    {
        $validatedData = $this->validate([
            'user_id' => 'required',
            'type_id' => 'required',
            'status' => 'required',
            'agent_id' => 'required',
            'subject' => 'required',
            'reply' => 'required',
        ], [
            'user_id.required' => __('messages.requestedByRequired'),
            'type_id.required' => __('messages.typeRequired'),
            'agent.required' => __('messages.agentRequired'),
            'files.*.max' => __('messages.fileSizeExceeded'),
            'reply.required' => __('messages.descriptionRequired'),
        ]);
       
        $this->ticket_number = $this->generateTicketNumber();

            $ticket = new Ticket();
            $ticket->ticket_number = $this->ticket_number;
            $ticket->user_id = $this->user_id;
            $ticket->type_id = $this->type_id;
            $ticket->status = $this->status;
            $ticket->agent_id = $this->agent_id;
            $ticket->subject = $this->subject;
            $ticket->reply = $this->reply;
            $ticket->save();

            $reply = new TicketReply();
            $reply->message = $this->reply;
            $reply->ticket_id = $ticket->id;
            $reply->user_id = $this->user_id; // Current logged in user
            $reply->save();

            foreach ($this->files as $file) {
                $filePath = $file->store('ticket_files');
                TicketFile::create([
                    'user_id' => $this->user_id,
                    'ticket_reply_id' => $reply->id,
                    'filename' => $file->getClientOriginalName(),
                    'hashname' => $filePath,
                ]);
            }
        try {
            $this->sendNotifications($ticket);

            $this->dispatch('hideAddTicket');
            $this->dispatch('reset-trix-editor');

            $this->resetForm();

            $this->alert('success', __('messages.ticketAdded'));

        }catch (\Exception $e) {

            // Handle exception and show error message
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }

    }

    public function sendNotifications($ticket)
    {
        $admin = auth()->user();
        $requester = $ticket->user;
        $agent = $ticket->agent;

        $admin->notify(new TicketNotification($ticket, 'admin', $admin->name, $requester->name, $agent->name));
        $requester->notify(new TicketNotification($ticket, 'requester', $admin->name, $requester->name, $agent->name));
        $agent->notify(new TicketNotification($ticket, 'agent', $admin->name, $requester->name, $agent->name));

    }

    public function resetForm()
    {
        $this->user_id = '';
        $this->type_id = '';
        $this->status = '';
        $this->agent_id = '';
        $this->subject = '';
        $this->reply = '';
        $this->files = [];
    }

    private function generateTicketNumber()
    {
        // Get the last ticket number as an integer
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $nextNumber = $lastTicket ? intval($lastTicket->ticket_number) + 1 : 1;

        // Return the next ticket number as a normal integer
        return (string) $nextNumber;
    }

    public function render()
    {
        return view('livewire.forms.add-ticket');
    }
}
