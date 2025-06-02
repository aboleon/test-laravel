<?php

namespace App\Http\Requests\Front\Transport;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\MediaLibraryPro\Rules\Concerns\ValidatesMedia;

class TransportDocumentFormRequest extends FormRequest
{

    use ValidatesMedia;

    public function rules()
    {
        return [
            'name' => ['nullable'],
            'document' => ['nullable', $this->validateSingleMedia()->maxItemSizeInKb(3000)],
        ];
    }
}