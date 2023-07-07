<?php
namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'author'            => $this->author,
            'publicationDate'   => $this->publication_date,
            'category'          => $this->category,
            'price'             => $this->price,
            'quantity'          => $this->quantity,
            'images'            => array_map(
                function ($image) {
                    return [
                        'name'      => $image['name'],
                        'path'      => $image['path']
                    ];
                },
                $this->images->toArray()
            ),
        ];
    }
}
