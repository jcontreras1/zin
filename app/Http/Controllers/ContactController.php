<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to(config('app.contact_email'))
            ->send(new ContactMail(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                body: $validated['message'],
            ));

        return redirect()->back()->with('contact_success', true);
    }
}
