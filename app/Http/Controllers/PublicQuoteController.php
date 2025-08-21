<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    public function show(string $token)
    {
        $quote = Quote::where('public_token',$token)->with(['customer','products'])->firstOrFail();
        return view('public.quote', [
            'quote'=>$quote,
        ]);
    }
}
