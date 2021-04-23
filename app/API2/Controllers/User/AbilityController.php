<?php

namespace Core\API2\Controllers\User;

use Illuminate\Http\Request;
use Core\API2\Controllers\Controller;
use Core\API2\Resources\Ability as AbilityResource;

class AbilityController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * List all abilities.
     */
    public function __invoke(Request $request)
    {
        return AbilityResource::collection(
            $request->user()->ability()->all()->values()
        );
    }
}
