<?php

namespace App\Http\Controllers;

use App\Models\GuideDocument;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.guides.index', [
            'latestGuide' => GuideDocument::query()
                ->with('uploader')
                ->latest()
                ->first(),
        ]);
    }
}

