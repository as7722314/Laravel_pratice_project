<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            "user_id" => $this->user_id,
            "receiver" => $this->receiver,
            "receiverTitle" => $this->receiverTitle,
            "receiverMobile" => $this->receiverMobile,
            "receiverEmail" => $this->receiverEmail,
            "receiverAddress" => $this->receiverAddress,
            'items' => ItemResource::collection($this->whenLoaded('items')),
            'pivot' => $this->whenPivotLoaded('order_item', function () {
                return $this->pivot;
            }),
        ];
    }
}
