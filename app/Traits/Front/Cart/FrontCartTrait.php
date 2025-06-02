<?php

namespace App\Traits\Front\Cart;

use App\Accessors\Front\FrontCartAccessor;
use App\Models\EventManager\Sellable;
use App\Models\FrontCartLine;
use MetaFramework\Traits\Responses;

trait FrontCartTrait
{
    use Responses;
    protected function checkCanRemoveService(Sellable $service): void
    {
        $frontCartAccessor = new FrontCartAccessor();
        $frontCartAccessor->getCart();

        $combinedServiceGroupIds = $this->getCombinedServiceGroupIdsInThisCart();
        if ($service->service_group && in_array($service->service_group, $combinedServiceGroupIds, true)) {

            $serviceLines = $frontCartAccessor->getServiceLines();
            $nbBoundServices = 0;
            $serviceLines->each(function (FrontCartLine $serviceLine) use ($service, &$nbBoundServices) {
                if($serviceLine->shoppable->service_group === $service->service_group){
                    $nbBoundServices++;
                }
            });

            if ($nbBoundServices > 1) {
                return;
            }
            $prestaNames = $frontCartAccessor->getServiceLines()
                ->filter(function (FrontCartLine $serviceLine) use ($combinedServiceGroupIds) {
                    return in_array($serviceLine->shoppable->service_group_combined, $combinedServiceGroupIds, true);
                })
                ->map(function (FrontCartLine $serviceLine) {
                    return $serviceLine->shoppable->title;
                })
                ->toArray();
            $this->responseError(__("front/user/cart.cart_cannot_remove_service_because_of_combined") . "<br>- " . implode("<br>- ", $prestaNames));
        }
    }

    private function getCombinedServiceGroupIdsInThisCart(): array
    {
        return (new FrontCartAccessor())->fetchCart()->getServiceLines()->map(function (FrontCartLine $serviceLine) {
            return $serviceLine->shoppable->service_group_combined;
        })
            ->unique()
            ->filter()
            ->toArray();
    }
}
