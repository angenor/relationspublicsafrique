<?php

namespace App\Http\Controllers;

use App\Models\LomeTourRegistration;
use App\Notifications\LomeTourAdminNotification;
use App\Notifications\LomeTourRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class LomeTourRegistrationController extends Controller
{
    public function show()
    {
        return view('lome-tour.registration');
    }

    public function store(Request $request)
    {
        // Normalisation simple du numéro (supprimer espaces et tirets)
        if ($request->filled('telephone_whatsapp')) {
            $request->merge([
                'telephone_whatsapp' => preg_replace('/[\s\-\.]/', '', $request->input('telephone_whatsapp')),
            ]);
        }

        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'statut' => 'required|in:etudiant,professionnel',
            'fonction' => 'required|string|max:255',
            // Autorise formats internationaux: optionnel +, 8 à 15 chiffres
            'telephone_whatsapp' => ['required', 'string', 'max:20', 'regex:/^\+?\d{8,15}$/'],
            'email' => 'nullable|email:rfc,dns|max:255',
            'photo_professionnelle' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:ratio=1/1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = $request->all();

        // Gestion de l'upload de la photo
        if ($request->hasFile('photo_professionnelle')) {
            $photo = $request->file('photo_professionnelle');
            $filename = 'lome-tour/' . time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public', $filename);
            $data['photo_professionnelle'] = $filename;
        }

        $registration = LomeTourRegistration::create($data);
        $registration->refresh();

        // Envoyer les notifications
        if ($registration->email) {
            $registration->notify(new LomeTourRegistrationNotification($registration));
        }

        // Notification à l'admin (toujours envoyée)
        $admin = \App\Models\User::where('email', config('mail.admin_email', 'admin@example.com'))->first();
        if ($admin) {
            $admin->notify(new LomeTourAdminNotification($registration));
        } else {
            // Si aucun admin trouvé, envoyer à l'email par défaut
            \Illuminate\Support\Facades\Notification::route('mail', config('mail.admin_email', 'admin@example.com'))
                ->notify(new LomeTourAdminNotification($registration));
        }

        // Vérifier si c'est une requête AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Votre inscription au Lomé COM\' TOUR a été enregistrée avec succès !'
            ]);
        }

        return redirect()->back()->with('success', 'Votre inscription au Lomé COM\' TOUR a été enregistrée avec succès !');
    }
}
