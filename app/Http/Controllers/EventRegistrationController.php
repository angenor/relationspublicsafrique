<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EventRegistrationController extends Controller
{
    /**
     * Inscrire un utilisateur à un événement
     */
    public function register(Request $request, Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour vous inscrire à un événement.',
                'redirect' => route('login')
            ], 401);
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur peut s'inscrire
        if (!$event->can_register) {
            return response()->json([
                'success' => false,
                'message' => 'Les inscriptions ne sont pas disponibles pour cet événement.'
            ], 400);
        }

        // Vérifier si l'utilisateur n'est pas déjà inscrit
        if ($event->isUserRegistered($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous êtes déjà inscrit à cet événement.'
            ], 400);
        }

        try {
            // Créer l'inscription
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'notes' => $request->input('notes')
            ]);

            // Mettre à jour le nombre de participants actuels
            $event->increment('current_participants');

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Vous recevrez une confirmation par email.',
                'registration' => $registration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Annuler l'inscription d'un utilisateur
     */
    public function cancel(Request $request, Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ], 401);
        }

        $user = Auth::user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune inscription trouvée pour cet événement.'
            ], 404);
        }

        try {
            // Marquer l'inscription comme annulée
            $registration->update(['status' => 'cancelled']);

            // Décrémenter le nombre de participants actuels
            $event->decrement('current_participants');

            return response()->json([
                'success' => true,
                'message' => 'Votre inscription a été annulée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Vérifier le statut d'inscription d'un utilisateur
     */
    public function status(Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'registered' => false,
                'can_register' => $event->can_register
            ]);
        }

        $user = Auth::user();
        $isRegistered = $event->isUserRegistered($user->id);

        $registration = null;
        if ($isRegistered) {
            $registration = EventRegistration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->first();
        }

        return response()->json([
            'registered' => $isRegistered,
            'can_register' => $event->can_register && !$isRegistered,
            'registration' => $registration ? [
                'status' => $registration->status,
                'status_label' => $registration->status_label,
                'registered_at' => $registration->registered_at->format('d/m/Y H:i')
            ] : null
        ]);
    }
}
