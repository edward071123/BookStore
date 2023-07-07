<?php
namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'memberId'          => $this->id,
            'memberName'        => $this->name,
            'memberAccount'     => $this->account,
            'memberMobile'      => $this->mobile,
            'memberEmail'       => $this->email
        ];
    }
}
