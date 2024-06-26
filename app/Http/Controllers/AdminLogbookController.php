<?php

namespace App\Http\Controllers;

use App\Models\Help;
use Auth;
use Illuminate\Http\Request;

class AdminLogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $action = Auth::user()->action;
        $notifications = $action->Notifications()->orderBy('when', 'DESC')->get(['id', 'user', 'when', 'content'])->toArray();

        return $notifications;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $title = 'Logbuch-Eintrag - Bearbeiten';
        $action = Auth::user()->action;
        $notification = $action->notifications->where('id', $id)->first();
        $help = Help::where('title', $title)->first();

        return view('admin.logbooks.edit', compact('notification', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $action = Auth::user()->action;
        if ($action && ! Auth::user()->demo) {
            $notification = $action->notifications->where('id', $id)->first();
            $notification->update($request->all());
        }

        return redirect('/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $user = Auth::user();
        if (! $user->demo) {
            $action = $user->action;
            $notification = $action->notifications->where('id', $id)->first();
            $notification->delete();
        }

        return redirect('/admin');
    }
}
