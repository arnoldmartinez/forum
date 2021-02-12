<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    /**
     * Create a new RepliesController instance.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
         return $thread->replies()->paginate(20);
    }

    /**
     * Persist a new reply.
     *
     * @param integer $channelId
     * @param Thread $thread
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Database\Eloquent\Model|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function store($channelId, Thread $thread)
    {
        if (Gate::denies('create', new Reply)) {
            return response(
                'You are posting too frequently. Please take a break. :)', 422
            );
        }

        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            return response(
                'Sorry your reply could not be saved at this time.', 422
            );
        }

        return $reply->load('owner');
    }

    /**
     * @param Reply $reply
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply->update(request(['body']));
        } catch (\Exception $e) {
            return response(
                'Sorry your reply could not be saved at this time.', 422
            );
        }
    }

    /**
     * Delete the given reply.
     *
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
