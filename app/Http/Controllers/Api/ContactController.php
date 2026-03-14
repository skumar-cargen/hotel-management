<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Models\ContactInquiry;
use App\Traits\ApiResponses;

class ContactController extends Controller
{
    use ApiResponses;

    public function store(ContactRequest $request)
    {
        $domain = $this->domain();

        ContactInquiry::create([
            'domain_id' => $domain->id,
            'hotel_id' => $request->validated('hotel_id'),
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'ip_address' => $request->ip(),
        ]);

        return $this->successResponse([
            'message' => 'Your inquiry has been submitted successfully. We will get back to you soon.',
        ], 201);
    }
}
