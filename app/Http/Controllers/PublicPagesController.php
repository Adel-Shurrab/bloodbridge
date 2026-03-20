<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

use App\Traits\ProvidesPublicStats;

class PublicPagesController extends Controller
{
    use ProvidesPublicStats;

    /**
     * Show the home page with dynamic stats.
     */
    public function home(): View
    {
        $stats = $this->getStats();
        return view('pages.home', compact('stats'));
    }

    /**
     * Show the about page with dynamic stats.
     */
    public function about(): View
    {
        $stats = $this->getStats();
        return view('pages.about', compact('stats'));
    }

    /**
     * Show the contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Show the eligibility requirements page.
     */
    public function eligibility(): View
    {
        return view('pages.eligibility');
    }

    /**
     * Show the terms of service page.
     */
    public function terms(): View
    {
        return view('pages.terms');
    }
}
