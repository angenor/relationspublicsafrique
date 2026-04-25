<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Category;
use App\Models\Instructors;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class FormationController extends Controller
{
    /**
     * Afficher la liste des formations
     */
    public function index(Request $request): View
    {
        $query = Formation::query()
            ->with(['instructor', 'category', 'reviews'])
            ->published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('language')) {
            $query->byLanguage($request->language);
        }

        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Tri
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'duration':
                $query->orderBy('duration', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $formations = $query->paginate(12);

        // Données pour les filtres
        $categories = Category::where('online', 1)->orderBy('name')->get();
        $instructors = Instructors::with('user')->get();
        $levels = ['débutant', 'intermédiaire', 'avancé', 'expert'];
        $languages = ['fr', 'en', 'es'];

        return view('formations.index', compact(
            'formations',
            'categories',
            'instructors',
            'levels',
            'languages'
        ));
    }

    /**
     * Afficher une formation spécifique
     */
    public function show(Request $request, $slug, $id): View
    {
        // Résoudre manuellement la formation par slug et ID
        $formation = Formation::where('slug', $slug)
            ->where('id', $id)
            ->first();

        if (!$formation) {
            abort(404);
        }

        // Vérifier si la formation est publiée
        if (!$formation->is_published) {
            abort(404);
        }

        $formation->load(['instructor.user', 'category', 'chapitres', 'reviews.user']);

        // Formations similaires
        $similarFormations = Formation::published()
            ->where('id', '!=', $formation->id)
            ->where(function ($query) use ($formation) {
                $query->where('category_id', $formation->category_id)
                    ->orWhere('instructor_id', $formation->instructor_id)
                    ->orWhere('level_name', $formation->level_name);
            })
            ->limit(4)
            ->get();

        // Vérifier si l'utilisateur est inscrit
        $isEnrolled = false;
        $enrollment = null;
        if (auth()->check()) {
            $enrollment = $formation->enrollments()->where('user_id', auth()->id())->first();
            $isEnrolled = $enrollment !== null;
        }

        return view('formations.show', compact(
            'formation',
            'similarFormations',
            'isEnrolled',
            'enrollment'
        ));
    }

    /**
     * S'inscrire à une formation
     */
    public function enroll(Request $request, Formation $formation): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour vous inscrire à une formation.'
            ], 401);
        }

        if (!$formation->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Cette formation n\'est pas disponible.'
            ], 404);
        }

        if (!$formation->canEnroll()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de s\'inscrire à cette formation.'
            ], 400);
        }

        // Vérifier si déjà inscrit
        $existingEnrollment = $formation->enrollments()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous êtes déjà inscrit à cette formation.'
            ], 400);
        }

        // Créer l'inscription
        $enrollment = $formation->enrollments()->create([
            'user_id' => auth()->id(),
            'status' => 'enrolled',
            'progress' => 0,
            'enrolled_at' => now(),
        ]);

        // Mettre à jour le compteur d'inscriptions
        $formation->increment('enrollment_count');

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie !',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Se désinscrire d'une formation
     */
    public function unenroll(Request $request, Formation $formation): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ], 401);
        }

        $enrollment = $formation->enrollments()
            ->where('user_id', auth()->id())
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas inscrit à cette formation.'
            ], 400);
        }

        $enrollment->update(['status' => 'dropped']);

        // Mettre à jour le compteur d'inscriptions
        $formation->decrement('enrollment_count');

        return response()->json([
            'success' => true,
            'message' => 'Désinscription réussie.'
        ]);
    }

    /**
     * Évaluer une formation
     */
    public function review(Request $request, Formation $formation): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour évaluer une formation.'
            ], 401);
        }

        // Vérifier si l'utilisateur a suivi la formation
        $enrollment = $formation->enrollments()
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir terminé la formation pour la noter.'
            ], 400);
        }

        // Vérifier si déjà évaluée
        $existingReview = $formation->reviews()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà évalué cette formation.'
            ], 400);
        }

        // Créer l'évaluation
        $review = $formation->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_verified' => true,
        ]);

        // Mettre à jour la note moyenne
        $formation->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Évaluation enregistrée avec succès.',
            'review' => $review
        ]);
    }

    /**
     * Mettre à jour la progression d'une formation
     */
    public function updateProgress(Request $request, Formation $formation): JsonResponse
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'completed_chapters' => 'nullable|array',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ], 401);
        }

        $enrollment = $formation->enrollments()
            ->where('user_id', auth()->id())
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas inscrit à cette formation.'
            ], 400);
        }

        $enrollment->updateProgress($request->progress);

        if ($request->has('completed_chapters')) {
            $enrollment->update(['completed_chapters' => $request->completed_chapters]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Progression mise à jour.',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Rechercher des formations (API)
     */
    public function search(Request $request): JsonResponse
    {
        $query = Formation::query()
            ->with(['instructor', 'category'])
            ->published();

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        $formations = $query->limit(10)->get();

        return response()->json([
            'success' => true,
            'formations' => $formations
        ]);
    }
}
