<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ConvertVideoForStreaming;
use App\Jobs\CreatethumbnailFromVideo;
use App\Models\Chapitre;
use App\Models\Cours;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Storage;
use Sunra\PhpSimple\HtmlDomParser;
use GuzzleHttp\Client;


class VideoController extends Controller
{
    public function index( Request $request, $id){

        $cours = \App\Models\Course::find($id);


        return view('admin.video.index', compact('cours'));
    }


    public function view()
    {

        // URL de la page à scraper (remplacez par l'URL de la page LinkedIn)
        $url = 'https://www.made-in-togo.com';

        // Créez une instance du client Guzzle
        $client = new Client();

        // Faites une requête GET vers l'URL
        $response = $client->request('GET', $url);

        // Vérifiez si la requête a réussi (statut HTTP 200)
        if ($response->getStatusCode() == 200) {
            // Récupérez le contenu de la réponse
            $html = $response->getBody()->getContents();


            // Analysez le contenu HTML avec PHP Simple HTML DOM Parser
            $dom = HtmlDomParser::str_get_html($html);
            dd($dom);
            // Extrayez le titre de la page
            $title = ($dom->find('title', 0)) ? $dom->find('title', 0)->plaintext : 'Titre non trouvé';

            // Extrayez la description de la page
            $meta_description = ($dom->find('meta[name=description]', 0)) ? $dom->find('meta[name=description]', 0)->getAttribute('content') : 'Description non trouvée';

            // Retournez les résultats
            return view('scraping.view', [
                'title' => $title,
                'description' => $meta_description,
            ]);
        } else {
            // En cas d'échec de la requête, retournez un message d'erreur
            return 'La requête a échoué';
        }

    }

    public function satore()
    {
        return view('admin.video.satore');
    }

    public function uploadVideo( Request $request)
    {
        $request->validate([
           'file' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm',
        ]);

        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            // file not uploaded

            dd(' file not uploaded') ;
        }

        $fileReceived = $receiver->receive();
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', Str::slug($file->getClientOriginalName())); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

            $disk = Storage::disk(config('filesystems.default'));
            $path = $disk->putFileAs('videos', $file, $fileName);

            // delete chunked file
//            unlink($file->getPathname());

            if ($request->type == 'course'){
                $cours = Course::find($request->id);
                $cours->video = $fileName;
                $cours->save();
                CreatethumbnailFromVideo::dispatch('cours', $cours->id) ;
                ConvertVideoForStreaming::dispatch( 'cours', $cours->id) ;
            }
            if ($request->type == 'chapitre'){
                $chapitre = Chapitre::find($request->id);
                $chapitre->video = $fileName;
                $chapitre->save();
                CreatethumbnailFromVideo::dispatch('chapitre', $chapitre->id) ;
                ConvertVideoForStreaming::dispatch( 'chapitre', $chapitre->id) ;
            }


//

            return [
                'path' => asset('storage/' . $path),
                'filename' => $fileName
            ];
        }



        // otherwise return percentage information
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }


    public function uploadImage( Request $request){
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);


        $file = $request->file('file'); // get file
        $extension = $file->getClientOriginalExtension();
        $fileName = str_replace('.'.$extension, '', Str::slug($file->getClientOriginalName())); //file name without extenstion
        $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

        $disk = Storage::disk(config('filesystems.default'));
        $path = $disk->putFileAs('public', $file, $fileName);

        if ($request->type == 'course'){
            $course = Course::find($request->id);
            $course->image = $fileName;
            $course->save();

        }
        if ($request->type == 'chapitre'){
            $chapitre = Chapitre::find($request->id);
            $chapitre->image = $fileName;
            $chapitre->save();
        }




        return [
            'path' => asset('storage/' . $fileName),
            'filename' => $fileName
        ];

    }

    public function apiUpdateVideoUrl(Request $request)
    {
        $cours = Course::findOrFail($request->id) ;

        $cours->video = $request->url;
        $cours->save();

        return response()->json($cours);
    }
}
