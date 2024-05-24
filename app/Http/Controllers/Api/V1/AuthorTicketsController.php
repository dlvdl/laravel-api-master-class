<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends ApiController
{
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate(10)
        );
    }

    public function store($author_id, StoreTicketRequest $request)
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id,
        ];

        return new TicketResource(Ticket::create($model));
    }

    public function destroy($authorId, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            if ($ticket->user_id == $authorId) {
                $ticket->delete();
                return $this->ok('Ticket successfully deleted');
            }

            return $this->error('Ticket cannot be found', 404);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}
