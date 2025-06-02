<?php

namespace App\Http\Controllers;

use App\Models\User;
use MetaFramework\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PhotoController extends Controller
{
    use Responses;
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = User::find($request['id']);
            $user->deleteProfilePhoto();
            $this->response['callback'] = $request['callback'];
            $this->response['image'] = $user->getProfilePhotoUrlAttribute();
            $this->responseSuccess('');
        } catch (Throwable $e) {
            $this->responseException($e);
        } finally {
            return response()->json($this->fetchResponse());
        }
    }
}
