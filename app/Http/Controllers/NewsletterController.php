<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterConfirmationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * Inscription opt-in : crée (ou réactive) un abonné `pending` et envoie l'email
     * de confirmation signé. Honeypot anti-bot + anti-doublon (email unique).
     */
    public function subscribe(Request $request): RedirectResponse
    {
        // Honeypot : un bot remplit le champ caché → on ignore silencieusement.
        if (filled($request->input('website'))) {
            return back()->with('media_newsletter_status', 'Merci, votre demande a été prise en compte.');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = mb_strtolower(trim($data['email']));
        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $email]);

        if ($subscriber->exists && $subscriber->status === 'confirmed') {
            return back()->with('media_newsletter_status', 'Vous êtes déjà abonné(e) à notre newsletter.');
        }

        // Nouveau ou réinscription après désabonnement → repasse en pending.
        $subscriber->status = 'pending';
        $subscriber->confirmed_at = null;
        $subscriber->save();

        $subscriber->notify(new NewsletterConfirmationNotification);

        return back()->with('media_newsletter_status', 'Merci ! Vérifiez votre boîte mail pour confirmer votre abonnement.');
    }

    /** Confirmation via URL signée (middleware `signed`). */
    public function confirm(string $token): View
    {
        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->status !== 'confirmed') {
            $subscriber->confirm();
        }

        return view('media.newsletter.confirme', compact('subscriber'));
    }

    /** Désabonnement via URL signée (middleware `signed`). */
    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->status !== 'unsubscribed') {
            $subscriber->unsubscribe();
        }

        return view('media.newsletter.desabonne', compact('subscriber'));
    }
}
