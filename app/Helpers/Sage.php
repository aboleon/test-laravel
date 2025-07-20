<?php

namespace App\Helpers;

use App\Interfaces\SageInterface;
use MetaFramework\Components\Input;

class Sage
{

    public static function renderTab(SageInterface $model, bool $prefix = false, bool $plural = false, int $maxlength = 5): string
    {
        $html = '<div class="tab-pane fade" id="sage-tabpane" role="tabpanel" aria-labelledby="sage-tabpane-tab">
        <div class="row my-5">';
        foreach ($model->sageFields() as $code => $label) {
            $html .= '<div class="col-lg-4 col-12">';
            $html .= self::renderSageInput(code: $code, model: $model, label: $label, prefix: $prefix, plural: $plural, maxlength: $maxlength);
            $html .= '</div>';
        }

        $html .= '</div>
            </div>';

        return $html;
    }

    public static function limitSageInput(): string
    {
        return "<script>document.querySelectorAll('.sageinput').forEach(function(input) {
    // Handle input event
    input.addEventListener('input', function(e) {
        this.value = this.value
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '');
    });
});</script>";
    }

    public static function renderSageInput(string $code, SageInterface $model, string $label = '', bool $prefix = false, bool $plural = false, int $maxlength = 5): string
    {
        $html = new Input(
            name: "sage.".$code.($plural ? '.'.$model->id : ''),
            value: $model->getSageReferenceValue($code),
            params: ['maxlength' => $maxlength],
            label: $label,
            class: 'sageinput',
            randomize: true,
            prefix: $prefix ? $model->getSageCode() : null,
        )->render();

        $html .= '<small class="d-block text-end">limité à '.$maxlength.' caractères</small>';

        return $html;
    }


}
