<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $subscription = auth()->user()->newsletterSubscription ?? 
                       auth()->user()->newsletterSubscription()->create();

        $categoryIds = array_keys(array_filter($request->input('category_updates', [])));
        $subscription->categories()->sync($categoryIds);

        return back()->with('success', 'Newsletter preferences updated successfully!');
    }

    public function unsubscribe()
    {
        NewsletterSubscription::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Successfully unsubscribed from the newsletter.');
    }
}
