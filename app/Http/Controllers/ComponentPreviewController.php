<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ComponentPreviewController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('components.buttons');
    }

    public function alert(): Response
    {
        return Inertia::render('components/Alert');
    }

    public function badges(): Response
    {
        return Inertia::render('components/Badges');
    }

    public function buttons(): Response
    {
        return Inertia::render('components/Buttons');
    }

    public function collapsible(): Response
    {
        return Inertia::render('components/Collapsible');
    }

    // --- Form Components --- //

    public function formInputField(): Response
    {
        return Inertia::render('components/Form/InputField');
    }

    public function formInputGroupField(): Response
    {
        return Inertia::render('components/Form/InputGroupField');
    }

    public function formPhoneNumberField(): Response
    {
        return Inertia::render('components/Form/PhoneNumberField');
    }

    public function formSelectField(): Response
    {
        return Inertia::render('components/Form/SelectField');
    }

    public function formSearchSelectField(): Response
    {
        return Inertia::render('components/Form/SearchSelectField');
    }
}
