<?php

namespace App\Http\Controllers;

use App\Models\ConsentementProfil;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\DemandeRetraitConfirmeNotification;
use App\Services\Annuaire\ProfilConsentementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ProfilController extends Controller
{
    /**
     * Formulaire RGPD : la personne référencée arrive depuis le lien signé reçu par email.
     * Trois actions proposées : confirmer le maintien, demander une modification, demander le retrait.
     */
    public function retraitForm(string $token)
    {
        $consentement = ConsentementProfil::where('jeton_retrait', $token)->firstOrFail();

        abort_if($consentement->jeton_expire_le && $consentement->jeton_expire_le->isPast(), 410, 'Lien expiré');

        return view('annuaire.retrait.form', [
            'profil' => $consentement->profil,
            'token' => $token,
        ]);
    }

    /**
     * Traitement de la demande de retrait : archive le profil et notifie les admins.
     */
    public function retraitConfirmer(Request $request, string $token, ProfilConsentementService $service)
    {
        $profil = $service->traiterDemandeRetrait($token);

        if (! $profil) {
            abort(410, 'Lien invalide ou expiré');
        }

        // Notifier la personne (confirmation) + les admins.
        if ($profil->email) {
            Notification::route('mail', $profil->email)
                ->notify(new DemandeRetraitConfirmeNotification($profil));
        }

        $admins = User::where('type', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new DemandeRetraitConfirmeNotification($profil));
        }

        return view('annuaire.retrait.confirme', ['profil' => $profil]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Fiche détail publique d'un profil.
     *
     * Accessible via `/profil/{slug}-{id}`. Charge les relations annuaire,
     * applique la règle de publication (`publie` uniquement) et adapte le
     * fil d'Ariane selon que l'utilisateur arrive depuis l'annuaire ou
     * depuis la page "Appartenir".
     */
    public function show(Request $request, string $slug, int $id)
    {
        $profil = Profil::query()
            ->with(['user', 'pays', 'domainesExpertise', 'tags', 'liensExternes'])
            ->whereKey($id)
            ->firstOrFail();

        // Visibilité publique : seul un profil "publie" est accessible.
        // Les admins / propriétaires connectés peuvent voir les autres états.
        if ($profil->etat_publication !== 'publie') {
            $user = $request->user();
            $autorise = $user && (
                ($user->type ?? null) === 'admin'
                || (int) $profil->user_id === (int) $user->id
            );
            abort_unless($autorise, 404);
        }

        $contexte = $this->detecterContexte($request);

        return view('user.profilshow', [
            'profil' => $profil,
            'pageTitle' => $contexte['pageTitle'],
            'pagesousTitle' => $contexte['pagesousTitle'],
            'breadcrumbParent' => $contexte['parent'],
        ]);
    }

    /**
     * Détecte si la fiche est consultée depuis l'annuaire ou depuis la page
     * "Appartenir" pour adapter le breadcumb.
     *
     * @return array{pageTitle:string,pagesousTitle:string,parent:array{label:string,url:string}}
     */
    private function detecterContexte(Request $request): array
    {
        $referer = (string) $request->headers->get('referer', '');
        $depuisAnnuaire = $referer !== '' && str_contains($referer, '/annuaire');

        if ($depuisAnnuaire) {
            return [
                'pageTitle' => 'Annuaire',
                'pagesousTitle' => 'Découvrez les professionnels des relations publiques',
                'parent' => [
                    'label' => 'Annuaire',
                    'url' => route('annuaire.index'),
                ],
            ];
        }

        return [
            'pageTitle' => 'Appartenir',
            'pagesousTitle' => 'Faire parti de notre communauté',
            'parent' => [
                'label' => 'Appartenir',
                'url' => route('appartenir'),
            ],
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
