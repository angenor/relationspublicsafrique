<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             "id"=>$this->id  ,
              "name"=>$this->name  ,
              "slug"=>$this->slug  ,
              "nom"=>$this->nom  ,
              "prenom"=>$this->prenom  ,
              "title"=>$this->title ,
              "fonction"=>$this->fonction  ,
              "domaine"=>$this->domaine  ,
              "facebook"=>$this->facebook  ,
              "twitter"=>$this->twitter  ,
              "youtube"=>$this->youtube  ,
              "linkding"=>$this->linkding  ,
              "site"=>$this->site  ,
              "contact"=>$this->contact  ,
              "adresse"=>$this->adresse  ,
              "tel"=>$this->tel,
              "email"=>$this->email  ,
              "bio"=>$this->bio  ,
              "image"=>$this->image  ,
              "online"=>$this->online  ,
              "aprouve"=>$this->aprouve  ,
              "pays_id"=>$this->pays_id  ,
              "user_id"=>$this->user_id  ,
              "img"=> asset($this->image)
        ];
    }
}
