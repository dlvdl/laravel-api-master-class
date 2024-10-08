<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes()));
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to store that resource', 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            if ($this->include('author')) {
                return new TicketResource($ticket->load('author'));
            }

            return $this->ok('Ok', new TicketResource($ticket));
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            //policy
            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource', 401);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to replace that resource', 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket successfully deleted');
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to delete that resource', 401);
        }
    }
}
