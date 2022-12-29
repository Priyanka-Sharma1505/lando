<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Enums\TicketType;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function show(Ticket $ticket)
    {
        return $ticket;
    }

    public function ticket(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'file' => 'array|max:3',
            'file.*' => 'mimes:jpg,jpeg,png,pdf|size:10000'
        ]);

        $ticket = Ticket::create([
            'ref' => Str::uuid(),
            'subject' => $request->input('subject'),
            'name_user' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name,
            'user_id' => auth('api')->user()->id,
            'status' => TicketType::OPEN
        ]);

        $ticketMessage = TicketMessage::create([
            'posted_by' => auth('api')->user()->id,
            'ticket_id' => $ticket->id,
            'message' => $request->input('message')
        ]);

        if($request->hasfile('file'))
        {
            foreach($request->file('file') as $file)
            {
                $ticketMessage
                    ->addMedia($file)
                    ->toMediaCollection();
            }
        }

        return $ticket->fresh();
    }

    public function message(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required',
            'file' => 'array|max:3',
            'file.*' => 'mimes:jpg,jpeg,png,pdf|size:10000'
        ]);

        $message = [
            'posted_by' => auth('api')->user()->id,
            'ticket_id' => $ticket->id,
            'message' => $request->input('message')
        ];

        if( request()->input('action') ){
            $action = '';
            switch( request()->input('action') ){
                case 'archive':
                    $action = TicketType::ARCHIVE;
                    break;
                case 'close':
                    $action = TicketType::CLOSED;
                    break;
            }

            $ticket->status = $action;
            $ticket->save();
        }

        $ticketMessage = TicketMessage::create($message);

        if($request->hasfile('file'))
        {
            foreach($request->file('file') as $file)
            {
                $ticketMessage
                    ->addMedia($file)
                    ->toMediaCollection();
            }
        }

        // Update on ticket
        $ticket->touch();

        return $ticket->fresh();
    }

    public function status(Request $request, Ticket $ticket){

        $request->validate([
            'action' => 'required',
        ]);

        if( request()->input('action') ){
            $action = '';
            switch( request()->input('action') ){
                case 'archive':
                    $action = TicketType::ARCHIVE;
                    break;
                case 'open':
                    $action = TicketType::OPEN;
                    break;
                case 'close':
                    $action = TicketType::CLOSED;
                    break;
            }

            $ticket->status = $action;
            $ticket->save();
        }

        return $ticket;
    }

    public function all()
    {
        /** @var User $user */
        $user = auth('api')->user();

        if( $user->isAdmin() ) {
            return Ticket::all()->sortByDesc('created_at')->values();
        } else {
            return Ticket::where('user_id', $user->id)->get()->values();
        }
    }
}
