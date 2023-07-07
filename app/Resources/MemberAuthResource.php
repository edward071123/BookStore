<?php
namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberAuthResource extends JsonResource
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
            'memberId'                  => $this->id,
            'memberName'                => $this->name,
            'memberAccount'             => $this->account,
            'memberMobile'              => $this->mobile,
            'memberEmail'               => $this->email,
            'constructions'             => array_map(
                function ($construction) {
                    return [
                        'constructionId'            => $construction['id'],
                        'constructionName'          => $construction['name'],
                        'constructionCode'          => $construction['code'] ?? '',
                        'constructionOfficialUrl'   => $construction['official_url'] ?? '',
                        'constructionTel'           => $construction['tel'] ?? '',
                        'sipUserName'               => $construction['subscriber']['username'] ?? '',
                        'sipUserPassword'           => $construction['subscriber']['password'] ?? '',
                    ];
                },
                $this->constructions->toArray()
            ),
            'houses'                    => array_map(
                function ($house) {
                    return [
                        'constructionId'              => $house['construction_id'],
                        'constructionName'            => $house['construction']['name'],
                        'constructionCode'            => $house['construction']['code'],
                        'constructionOfficialUrl'     => $house['construction']['official_url'] ?? '',
                        'constructionTel'             => $house['construction']['tel'] ?? '',
                        'constructionSipUserName'     => $house['construction']['subscriber']['username'] ?? '',
                        'constructionSipUserPassword' => $house['construction']['subscriber']['password'] ?? '',
                        'houseID'                     => $house['id'],
                        'houseName'                   => $house['name'],
                        'sipUserName'                 => $house['subscriber']['username'] ?? '',
                        'sipUserPassword'             => $house['subscriber']['password'] ?? '',
                    ];
                },
                $this->houses->toArray()
            ),
        ];
    }
}
