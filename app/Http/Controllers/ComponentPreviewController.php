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

    public function pagination(): Response
    {
        return Inertia::render('components/Pagination');
    }

    public function table(): Response
    {
        $statuses = ['paid', 'pending', 'unpaid'];
        $methods  = ['Credit Card', 'PayPal', 'Bank Transfer'];

        $invoices = collect(range(1, 47))->map(fn (int $number): array => [
            'id'     => $number,
            'number' => sprintf('INV-%03d', $number),
            'status' => $statuses[$number % 3],
            'method' => $methods[$number % 3],
            'amount' => 50 + (($number * 17) % 450),
        ]);

        return Inertia::render('components/Table', [
            'invoices' => $invoices->paginate(10),
        ]);
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

    public function formTextareaField(): Response
    {
        return Inertia::render('components/Form/TextareaField');
    }

    public function formCheckboxField(): Response
    {
        return Inertia::render('components/Form/CheckboxField');
    }

    public function formRadioField(): Response
    {
        return Inertia::render('components/Form/RadioField');
    }

    public function formSwitchField(): Response
    {
        return Inertia::render('components/Form/SwitchField');
    }
}
