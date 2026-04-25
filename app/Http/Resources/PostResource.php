<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'slug'=>$this->slug,
            'position'=>$this->position,
            'content'=>$this->content,
            'online'=>$this->online,
            'note'=>$this->note,
            'user_id'=>$this->user_id,
            'category_id'=>$this->category_id,
            'parent_id'=>$this->parent_id,
            'image'=>$this->image,
            'img'=> $this->image==null ? asset('images/front/ban.jpg'):  asset($this->image),
        ];
    }
}
