<?php

// \Debugbar::enable();
// \Debugbar::disable();
use Symfony\Component\Process\Process;

$slugPatern = '[a-z0-9\-]+';

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// $posts = \App\Models\Post::get();
// for($i=0;$i<$posts->count();$i++){
//    $posts[$i]->image = "https://odindesignthemes.com/vikinger-theme/wp-content/uploads/2020/09/stephen-leonardi-8xn_PWT_6d0-unsplash.jpg";
//    $posts[$i]->save();
// }
//
// $posts = \App\Models\Profil::get();
// for($i=0;$i<$posts->count();$i++){
//    $posts[$i]->image = "https://www.jeuneafrique.com/cdn-cgi/image/q=100,f=auto,metadata=none,width=1256,height=628/https://www.jeuneafrique.com/medias/2018/10/12/27815hr_-e1539726055707.jpg";
//    $posts[$i]->save();
// }

// \App\Models\Post::factory(100)->create();
// App\Models\Post::factory(100)->create();
// \App\Models\User::truncate();
// \App\Models\Profil::truncate();
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//    return view('welcome');
// });

Route::get('/', [\App\Http\Controllers\PostController::class, 'index'])->name('accueil');

// Lomé COM' TOUR registration
Route::get('/lome-com-tour/inscription', [\App\Http\Controllers\LomeTourRegistrationController::class, 'show'])->name('lome.tour.register');
Route::post('/lome-com-tour/inscription', [\App\Http\Controllers\LomeTourRegistrationController::class, 'store'])->name('lome.tour.register.store');
Route::get('/profil/{slug}-{id}', [\App\Http\Controllers\ProfilController::class, 'show'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('profil.show');

Route::get('/actualites', [\App\Http\Controllers\PostController::class, 'blog'])->name('blog');
Route::get('/appartenir', [\App\Http\Controllers\AnnuaireController::class, 'appartenir'])->name('appartenir');

// Routes pour les événements
Route::get('/evenements', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/evenements/{slug}-{id}', [\App\Http\Controllers\EventController::class, 'show'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('events.show');
Route::get('/evenements/categorie/{slug}', [\App\Http\Controllers\EventController::class, 'category'])->name('events.category');
Route::get('/evenements/calendrier', [\App\Http\Controllers\EventController::class, 'calendar'])->name('events.calendar');
Route::get('/evenements/recherche', [\App\Http\Controllers\EventController::class, 'search'])->name('events.search');

// Routes pour les formations
Route::get('/formations', [\App\Http\Controllers\FormationController::class, 'index'])->name('formations');
Route::get('/formation', function () {
    return redirect()->route('formations');
})->name('formation');
Route::get('/formation/{slug}-{id}', [\App\Http\Controllers\FormationController::class, 'show'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('formation.show');
Route::post('/formation/{id}/enroll', [\App\Http\Controllers\FormationController::class, 'enroll'])->name('formation.enroll');
Route::post('/formation/{id}/unenroll', [\App\Http\Controllers\FormationController::class, 'unenroll'])->name('formation.unenroll');
Route::post('/formation/{id}/review', [\App\Http\Controllers\FormationController::class, 'review'])->name('formation.review');
Route::post('/formation/{id}/progress', [\App\Http\Controllers\FormationController::class, 'updateProgress'])->name('formation.progress');
Route::get('/api/formations/search', [\App\Http\Controllers\FormationController::class, 'search'])->name('api.formations.search');

Route::get('/categories/{slug}', [\App\Http\Controllers\PostController::class, 'categories'])->name('categories.slug');

Route::get('/apprendre', [\App\Http\Controllers\PostController::class, 'apprendre'])->name('apprendre');

Route::get('/acheter/cours/{slug}-{id}', [\App\Http\Controllers\CourseController::class, 'achetercours'])->where(['slug' => $slugPatern, 'id' => '[0-9]+'])->name('apprendre.acheter');
Route::get('/apprendre/{slug}-{id}', [\App\Http\Controllers\CourseController::class, 'show'])->where(['slug' => $slugPatern, 'id' => '[0-9]+'])->name('apprendre.show');

Route::get('/ressources', [\App\Http\Controllers\PostController::class, 'ressources'])->name('ressources');
Route::get('/apropos', [\App\Http\Controllers\PostController::class, 'apropos'])->name('apropos');

// Routes pour les pages "Nous"
Route::get('/nous/mission', [\App\Http\Controllers\NousController::class, 'mission'])->name('nous.mission');
Route::get('/nous/vision', [\App\Http\Controllers\NousController::class, 'vision'])->name('nous.vision');
Route::get('/nous/historique', [\App\Http\Controllers\NousController::class, 'historique'])->name('nous.historique');

// Routes pour l'annuaire
Route::get('/annuaire', [\App\Http\Controllers\AnnuaireController::class, 'index'])->name('annuaire.index');
Route::get('/annuaire/rejoindre', [\App\Http\Controllers\AnnuaireController::class, 'rejoindre'])->name('annuaire.rejoindre');
Route::get('/annuaire/suggerer', [\App\Http\Controllers\AnnuaireController::class, 'suggerer'])->name('annuaire.suggerer');
Route::get('/annuaire/consulter', [\App\Http\Controllers\AnnuaireController::class, 'index'])->name('annuaire.consulter');
// Gestion RGPD : lien signé envoyé à la personne référencée pour demander un retrait.
Route::get('/annuaire/retrait/{token}', [\App\Http\Controllers\ProfilController::class, 'retraitForm'])
    ->middleware('signed')
    ->name('annuaire.retrait.form');
Route::post('/annuaire/retrait/{token}/confirmer', [\App\Http\Controllers\ProfilController::class, 'retraitConfirmer'])
    ->middleware('signed')
    ->name('annuaire.retrait.confirmer');

Route::get('/actualites/{slug}-{id}', [\App\Http\Controllers\PostController::class, 'shoqBlog'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('blog.show');

Route::get('/explorer', [\App\Http\Controllers\PostController::class, 'explorer'])->name('explorer');
Route::get('/contact', [\App\Http\Controllers\PostController::class, 'contact'])->name('contact');
Route::get('/recherche', [\App\Http\Controllers\PostController::class, 'recherche'])->name('recherche');

Route::get('stream', function () {

    $path = 'videos/oceansmp4_ee1713cb89fa5bece06417de2327b46a.mp4';

    $file = Storage::get($path);
    $type = Storage::mimeType($path);

    $response = response($file, 200)->header('Content-Type', $type);

    return $response;
})->name('stream');

Route::get('stream/{filename}', function ($filename) {
    $path = storage_path('app/' . $filename);

    if (! Storage::exists($path)) {
        abort(404);
    }

    $process = new Process(['ffmpeg', '-i', $path, '-movflags', 'faststart', '-c', 'copy', '-f', 'mp4', 'pipe:1']);
    $process->run();

    if (! $process->isSuccessful()) {
        abort(500);
    }

    return response()->stream(
        function () use ($process) {
            echo $process->getOutput();
        },
        200,
        [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ]
    );
})->where('filename', '.*');

Auth::routes(['verify' => true]);

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/user/profil');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::group(['middleware' => ['auth:web', 'verified'], 'name' => 'user.', 'prefix' => 'user'], function () {

    //    Route::resource('userblog',\App\Http\Controllers\User\UserblogController::class)  ;

    Route::get('consulter-profil', [\App\Http\Controllers\User\PostController::class, 'consulterprofil'])->name('consulter-profil');
    Route::get('tableau-de-bord', [\App\Http\Controllers\User\PostController::class, 'tableauDeBord'])->name('tableau-de-bord');
    Route::get('profil', [\App\Http\Controllers\User\UserController::class, 'profil'])->name('profil');
    Route::get('mes-abonnements', [\App\Http\Controllers\User\AbonnementController::class, 'index'])->name('abonnements');
    Route::get('mes-cours', [\App\Http\Controllers\User\CoursController::class, 'index'])->name('index.cours');

    Route::put('profil', [\App\Http\Controllers\User\UserController::class, 'update'])->name('update.profil');
    Route::post('profil', [\App\Http\Controllers\User\UserController::class, 'update'])->name('update.profil.post');
    Route::put('profil/{id}', [\App\Http\Controllers\User\UserController::class, 'update'])->name('update.profil.id');
    Route::post('profil/{id}', [\App\Http\Controllers\User\UserController::class, 'update'])->name('update.profil.id.post');
    Route::post('saveimage', [\App\Http\Controllers\User\UserController::class, 'saveimage'])->name('store.saveimage');

    Route::group(['prefix' => 'api'], function () {
        Route::get('/profil', [\App\Http\Controllers\User\ApiController::class, 'profil']);
        Route::get('/posts', [\App\Http\Controllers\User\ApiController::class, 'posts']);
        Route::put('/posts/{id}', [\App\Http\Controllers\User\ApiController::class, 'update']);
        Route::put('/profil', [\App\Http\Controllers\User\UserController::class, 'update'])->name('api.profil.update');
    });

    // Route spéciale pour la mise à jour du profil sans CSRF
    Route::post('/profil-update', [\App\Http\Controllers\User\UserController::class, 'update'])->name('profil.update.simple');

    Route::group(['prefix' => 'payment', 'name' => 'payment.', 'as' => 'user.payment.'], function () {
        Route::post('checkout', [\App\Http\Controllers\StripController::class, 'checkout'])->name('checkout');
        Route::get('success', [\App\Http\Controllers\StripController::class, 'success'])->name('success');
        Route::get('cancel', [\App\Http\Controllers\StripController::class, 'cancel'])->name('cancel');
    });
});

Route::group(['middleware' => ['auth:web', 'isCoursAchete', 'verified'], 'name' => 'cours.', 'prefix' => 'apprendre'], function () use ($slugPatern) {
    Route::get('/suivre-le-cours/{slug}-{id}', [\App\Http\Controllers\CourseController::class, 'suivreLeCours'])->where(['slug' => $slugPatern, 'id' => '[0-9]+'])->name('cours.suivre-le-cours');
});

Route::group(['middleware' => ['auth:web', 'verified'], 'name' => 'admin.', 'prefix' => 'admin'], function () {

    Route::post('upload-video', [\App\Http\Controllers\Admin\VideoController::class, 'uploadVideo'])->name('upload-video');
    Route::post('upload-image', [\App\Http\Controllers\Admin\VideoController::class, 'uploadImage'])->name('upload-image');
    Route::post('/api-update-video-url', [\App\Http\Controllers\Admin\VideoController::class, 'apiUpdateVideoUrl'])->name('api-update-video-url');
    Route::get('/courses/{id}/add-course-video', [\App\Http\Controllers\Admin\VideoController::class, 'index'])->name('consulter-profil.add-course-video');
    Route::get('/courses/{id}/view', [\App\Http\Controllers\Admin\VideoController::class, 'view'])->name('consulter-view');
});

Route::group(['middleware' => ['auth:web', 'verified', 'isAdmin'], 'name' => 'administration.', 'prefix' => 'administration'], function () {

    Route::get('profil', [\App\Http\Controllers\Administration\UserController::class, 'profil'])->name('profil.edit');

    Route::resource('gestion-video-cours', \App\Http\Controllers\Administration\ControllerGestionvideocours::class);
    Route::get('gestion-video-cours-show/{id}', [\App\Http\Controllers\Administration\ControllerGestionvideocours::class, 'getCours']);
    Route::get('coures/{id}/chapitres/{id2}', [\App\Http\Controllers\Administration\GestionFormationController::class, 'getChapitre']);
    Route::put('coures/{id}/chapitres/{id2}', [\App\Http\Controllers\Administration\GestionFormationController::class, 'update']);
    //    Route::get('gestion-video-cours',[\App\Http\Controllers\Administration\ControllerGestionvideocours::class])->name('gestion-video-cours.edit')  ;

});

// Routes pour les formations
Route::get('/formations', [\App\Http\Controllers\FormationController::class, 'index'])->name('formations');
Route::get('/formation', function () {
    return redirect()->route('formations');
})->name('formation');
Route::get('/formation/{slug}-{id}', [\App\Http\Controllers\FormationController::class, 'show'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('formation.show');
Route::post('/formation/{id}/enroll', [\App\Http\Controllers\FormationController::class, 'enroll'])->name('formation.enroll');
Route::post('/formation/{id}/unenroll', [\App\Http\Controllers\FormationController::class, 'unenroll'])->name('formation.unenroll');
Route::post('/formation/{id}/review', [\App\Http\Controllers\FormationController::class, 'review'])->name('formation.review');
Route::post('/formation/{id}/progress', [\App\Http\Controllers\FormationController::class, 'updateProgress'])->name('formation.progress');
Route::get('/api/formations/search', [\App\Http\Controllers\FormationController::class, 'search'])->name('api.formations.search');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
