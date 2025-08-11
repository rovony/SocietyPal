<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;

class SocietyForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Forum', society_modules()) || !in_array('Forum', society_role_modules()), 403);
        abort_if((!user_can('Show Forum')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('society-forum.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(!in_array('Forum', society_modules()) || !in_array('Forum', society_role_modules()), 403);

        $forum = Forum::with('users')->findOrFail($id);

        if (user_can('Show Forum')) {
            return view('society-forum.show', compact('forum'));
        }

        if ($forum->discussion_type === 'public') {
            return view('society-forum.show', compact('forum'));
        }

        if ($forum->users->contains('id', user()->id)) {
            return view('society-forum.show', compact('forum'));
        }

        abort(403);
    }

}
